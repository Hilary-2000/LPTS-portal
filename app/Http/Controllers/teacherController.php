<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class teacherController extends Controller
{
    //this is the main teacher controller
    function teacherMessage(Request $request)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // select * from notifications
        $teacher_id = session("staff_infor")->user_id;
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_type` = 'teacher' AND `owner_id` = '".$teacher_id."' AND `message_edit_status` = 'Published' ORDER BY `id` DESC");

        // return to default database
        DB::setDefaultConnection("mysql");
        $staff_information = DB::select("SELECT * FROM `user_tbl`");
        for ($index=0; $index < count($notifications); $index++) { 
            // get the staff who created the notification.
            $notifications[$index]->created_by = $this->getStaffName($staff_information, $notifications[$index]->created_by);
        }

        // return $notifications;
        $teacher_notifications = $this->getTrsNotification();
        return view("message_alerts",["notifications" => $notifications,"teacher_notifications" => $teacher_notifications]);
    }

    function getStudentMessage(){
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the student notifications
        $adm_no = session("student_information")->adm_no;

        // get the teacher`s notification
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_id` = '".$adm_no."' AND `owner_type` = 'student' AND `message_edit_status` = 'Published' AND `message_status` = '0' ORDER BY `id` DESC");

        return $notifications;
    }
    function readNotification($readNotification){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the nofication details
        $notifications_details = DB::select("SELECT * FROM `message_n_alert` WHERE `id` = ?",[$readNotification]);
        // return $notifications_details;

        if (count($notifications_details) == 0) {
            return redirect("/Teacher/Messages");
        }

        if ($notifications_details[0]->message_status == 0) {
            $update = DB::update("UPDATE `message_n_alert` SET `message_status` = '1' WHERE `id` = ?",[$readNotification]);
        }

        // get the teacher`s notification
        $teacher_notifications = $this->getTrsNotification();
        return view("read_notice",["teacher_notifications" => $teacher_notifications,"notifications_details" => $notifications_details]);
    }

    function getStaffName($staff_list,$staff_id){
        for ($index=0; $index < count($staff_list); $index++) { 
            if ($staff_list[$index]->user_id == $staff_id) {

                // fullname
                return ucwords(strtolower($staff_list[$index]->fullname));
            }
        }

        // its not set
        return "N/A";
    }

    // get the data
    function createAlert(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // return value
        $teacher_notifications = $this->getTrsNotification();
        return view("create_message",["teacher_notifications" => $teacher_notifications]);
    }
    function createAlertnMessage(Request $request){
        // return $request;
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the assignments saved
        $notice_title = $request->input("notice_title");
        $notice_body = $request->input("notice_body");
        $message_recipient = $request->input("message_recipient");
        $message_status = $request->input("message_status");

        if ($message_recipient == "teacher") {
            // get all the staff that are supposed to recieve this message
            DB::setDefaultConnection("mysql");
            $school_code = session("school_information")->school_code;
            $teacher_data = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_code]);
    
            // get the latest id
            DB::setDefaultConnection("mysql2");
            $latest_id = DB::select("SELECT * FROM `message_n_alert` ORDER BY `id` DESC LIMIT 1");
            $message_id = count($latest_id) > 0 ? $latest_id[0]->message_editor_id + 1 : 1;
            // return $message_id;
    
            // insert the data
            $date_created = date("YmdHis");
            $teacher_id = session("staff_infor")->user_id;
            // return $teacher_data;
            for ($index=0; $index < count($teacher_data); $index++) {
                $message_read_status = 0;
                $insert = DB::insert("INSERT INTO `message_n_alert` (`owner_id`,`message_title`,`message_body`,`owner_type`,`message_status`,`date_created`,`message_edit_status`,`message_editor_id`,`created_by`) VALUES (?,?,?,?,?,?,?,?,?)",[$teacher_data[$index]->user_id,$notice_title,$notice_body,$message_recipient,$message_read_status,$date_created,$message_status,$message_id,$teacher_id]);
            }
            session()->flash("valid","Alert has been created for Teachers successfully! Click \"Manage My Alerts\" to view the alerts created!");
            return redirect("/Teacher/Messages/CreateAlert");
        }elseif ($message_recipient == "student") {
            // get the latest id
            DB::setDefaultConnection("mysql2");
            $latest_id = DB::select("SELECT * FROM `message_n_alert` ORDER BY `id` DESC LIMIT 1");
            $message_id = count($latest_id) > 0 ? $latest_id[0]->message_editor_id + 1 : 1;

            // student data
            $student_data = DB::select("SELECT * FROM `student_data` WHERE `stud_class` != '-1' AND `stud_class` != '-2'");
            $teacher_id = session("staff_infor")->user_id;
            $date_created = date("YmdHis");
            for ($index=0; $index < count($student_data); $index++) {
                $message_read_status = 0;
                $insert = DB::insert("INSERT INTO `message_n_alert` (`owner_id`,`message_title`,`message_body`,`owner_type`,`message_status`,`date_created`,`message_edit_status`,`message_editor_id`,`created_by`) VALUES (?,?,?,?,?,?,?,?,?)",[$student_data[$index]->adm_no,$notice_title,$notice_body,$message_recipient,$message_read_status,$date_created,$message_status,$message_id,$teacher_id]);
            }

            // return session
            session()->flash("valid","Alert has been created for students successfully! Click \"Manage My Alerts\" to view the alerts created!");
            return redirect("/Teacher/Messages/CreateAlert");
        }elseif ($message_recipient == "parent") {
            // get the latest id
            DB::setDefaultConnection("mysql2");
            $latest_id = DB::select("SELECT * FROM `message_n_alert` ORDER BY `id` DESC LIMIT 1");
            $message_id = count($latest_id) > 0 ? $latest_id[0]->message_editor_id + 1 : 1;

            // student data
            $student_data = DB::select("SELECT * FROM `student_data` WHERE `stud_class` != '-1' AND `stud_class` != '-2'");
            $teacher_id = session("staff_infor")->user_id;
            $date_created = date("YmdHis");
            $parents_added = [];
            for ($index=0; $index < count($student_data); $index++) {
                $message_read_status = 0;
                $parents_contact_1 = $student_data[$index]->parentContacts;
                $parents_contact_2 = $student_data[$index]->parent_contact2;
                
                // first parent notice
                if (strlen(trim($parents_contact_1)) != 0 && trim($parents_contact_1) != "none") {
                    if (!in_array($parents_contact_1,$parents_added)) {
                        array_push($parents_added,$parents_contact_1);
                        $insert = DB::insert("INSERT INTO `message_n_alert` (`owner_id`,`message_title`,`message_body`,`owner_type`,`message_status`,`date_created`,`message_edit_status`,`message_editor_id`,`created_by`) VALUES (?,?,?,?,?,?,?,?,?)",[$parents_contact_1,$notice_title,$notice_body,$message_recipient,$message_read_status,$date_created,$message_status,$message_id,$teacher_id]);
                    }
                }

                if (strlen(trim($parents_contact_2)) != 0 && trim($parents_contact_2) != "none"){
                    // second parent notice
                    if (!in_array($parents_contact_2,$parents_added)) {
                        array_push($parents_added,$parents_contact_2);
                        $insert = DB::insert("INSERT INTO `message_n_alert` (`owner_id`,`message_title`,`message_body`,`owner_type`,`message_status`,`date_created`,`message_edit_status`,`message_editor_id`,`created_by`) VALUES (?,?,?,?,?,?,?,?,?)",[$parents_contact_2,$notice_title,$notice_body,$message_recipient,$message_read_status,$date_created,$message_status,$message_id,$teacher_id]);
                    }
                }
            }

            // return session
            session()->flash("valid","Alert has been created for parents successfully! Click \"Manage My Alerts\" to view the alerts created!");
            return redirect("/Teacher/Messages/CreateAlert");
        }else {
            session()->flash("invalid","Select your recipient!!");
            return redirect("/Teacher/Messages/CreateAlert");
        }
    }
    // manage alerts
    function manageAlerts(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the alerts
        $teacher_id = session("staff_infor")->user_id;
        $message_notification = DB::select("SELECT MAX(`owner_type`) AS 'owner_type',MAX(`message_body`) AS 'message_body',MAX(`message_title`) AS 'message_title',MAX(`date_created`) AS 'date_created',MAX(`message_edit_status`) AS 'message_edit_status',MAX(`message_editor_id`) AS 'message_editor_id' FROM `message_n_alert` WHERE `created_by` = '1' GROUP BY `message_editor_id` ORDER BY MAX(`id`) DESC;");

        // loop through the notification to get the stats
        for ($index=0; $index < count($message_notification); $index++) { 
            $message_id = $message_notification[$index]->message_editor_id;
            
            $read_no = DB::select("SELECT COUNT(*) AS 'total' FROM `message_n_alert` WHERE `message_editor_id` = ? AND `message_status` = '1' ",[$message_id]);
            $unread_no = DB::select("SELECT COUNT(*) AS 'total' FROM `message_n_alert` WHERE `message_editor_id` = ? AND `message_status` = '0' ",[$message_id]);

            $message_notification[$index]->unread_no = count($unread_no) > 0 ? $unread_no[0]->total : 0;
            $message_notification[$index]->read_no = count($read_no) > 0 ? $read_no[0]->total : 0;
        }
        // return $message_notification;
        $teacher_notifications = $this->getTrsNotification();
        return view("manage_alert_message",["teacher_notifications" => $teacher_notifications,"message_notification" => $message_notification]);
    }

    function manageExistingAlert($message_id){
        // get the teacher`s academic plan
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the message details
        $message_data = DB::select("SELECT * FROM `message_n_alert` WHERE `message_editor_id` = ? LIMIT 1",[$message_id]);

        // UNREAD AND READ NO
        $read_no = DB::select("SELECT COUNT(*) AS 'total' FROM `message_n_alert` WHERE `message_editor_id` = ? AND `message_status` = '1' ",[$message_id]);
        $unread_no = DB::select("SELECT COUNT(*) AS 'total' FROM `message_n_alert` WHERE `message_editor_id` = ? AND `message_status` = '0' ",[$message_id]);
        $read_no = $read_no[0];
        $unread_no = $unread_no[0];
        // RETURN VALUE
        // return $read_no;
        $teacher_notifications = $this->getTrsNotification();
        return view("edit_message",["teacher_notifications" => $teacher_notifications,"message_data" => $message_data,"read" => $read_no,"unread" => $unread_no]);
    }

    function updateAlert(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");
        // return $request;
        $notice_title = $request->input("notice_title");
        $notice_ids = $request->input("notice_ids");
        $notice_body = $request->input("notice_body");
        $message_status = $request->input("message_status");

        $update = DB::update("UPDATE `message_n_alert` SET `message_title` = ?, `message_body` = ?, `message_edit_status` = ? WHERE `message_editor_id` = ?",[$notice_title,$notice_body,$message_status,$notice_ids]);

        session()->flash("valid","Update were done successfully!");
        return redirect("/Teacher/Messages/Manage/$notice_ids");
    }
    function deleteAlert($notification_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // delete the notification
        $delete = DB::delete("DELETE FROM `message_n_alert` WHERE `message_editor_id` = ?",[$notification_id]);

        // return redirect
        session()->flash("valid","Notice has been deleted successfully!");
        return redirect("/Teacher/Messages/Manage");
    }
    function getTrsNotification(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        $teacher_id = session("staff_infor")->user_id;

        // get the teacher`s notification
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_id` = '".$teacher_id."' AND `owner_type` = 'teacher' AND `message_edit_status` = 'Published' AND `message_status` = '0' ORDER BY `id` DESC");

        return $notifications;
    }

    function teacherProfile(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the staff profile details details
        $teacher_notifications = $this->getTrsNotification();
        // return session("school_information")->school_name;
        // get the staff information
        DB::setDefaultConnection("mysql");
        $teacher_id = session("staff_infor")->user_id;
        $staff_data = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$teacher_id]);
        return view("teacher_profile",["teacher_notifications" => $teacher_notifications,"staff_data" => $staff_data]);
    }
    
    function updateProfile(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $request;
        $user_id = $request->input("user_id");
        $address = $request->input("address");
        $phone = $request->input("phone");
        $email = $request->input("email");

        // update the database
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        // DB::setDefaultConnection("mysql2");

        $update = DB::update("UPDATE `user_tbl` SET `address` = ?, `phone_number` = ?, `email` = ? WHERE `user_id` = ?",[$address,$phone,$email,$user_id]);

        // redirect back to the page
        session()->flash("valid","Update done successfull!");
        return redirect("/Teacher/Profile");
    }

    function UpdatePassword(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $request;
        $user_id = $request->input("user_id");
        $password = $request->input("password");
        $newpassword = $request->input("newpassword");
        $renewpassword = $request->input("renewpassword");
        
        if ($newpassword !== $renewpassword) {
            session()->flash("invalid","Passwords don`t match!");
            return redirect("/Teacher/Profile");
        }
        // check if the password provided is correct
        $user_details = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$user_id]);

        if (count($user_details) == 0) {
            session()->flash("invalid","An error occured!");
            return redirect("/Teacher/Profile");
        }

        // password
        $old_password = $user_details[0]->password;
        if ($this->encryptCode($password) != $old_password) {
            session()->flash("invalid","You have provided the incorrect password!");
            return redirect("/Teacher/Profile");
        }

        // update the password
        $newpassword = $this->encryptCode($newpassword);
        $update = DB::update("UPDATE `user_tbl` SET `password` = ? WHERE `user_id` = ?",[$newpassword,$user_id]);

        session()->flash("valid","Passwords changed successfully!");
        return redirect("/Teacher/Profile");
    }
    function encryptCode($dataToEncrypt)
    {
        //first get char code for each name
        $revdata = strrev($dataToEncrypt);
        $data = str_split($revdata);
        $encrpted = "";
        for ($y = 0; $y < count($data); $y++) {
            $encrpted .= $this->getCode($data[$y]);
        }
        $encrypted = strrev($encrpted);
        return $encrypted;
    }
    function decryptcode($datatodecrypt)
    {
        $arrayeddata = str_split(strrev($datatodecrypt), 3);
        $data = "";
        for ($i = 0; $i < count($arrayeddata); $i++) {
            $data .= $arrayeddata[$i];
        }
        return strrev($data);
    }

    function getCode($code)
    {

        if ($code == 'A') {
            return '$rSv';
        } elseif ($code == 'B') {
            return 'Grp2';
        } elseif ($code == 'C') {
            return 'SnMp';
        } elseif ($code == 'D') {
            return 'Tr#4';
        } elseif ($code == 'E') {
            return '69!4';
        } elseif ($code == 'F') {
            return 'PpQr';
        } elseif ($code == 'G') {
            return 'TpSO';
        } elseif ($code == 'H') {
            return 'IvSr';
        } elseif ($code == 'I') {
            return 'LpTs';
        } elseif ($code == 'J') {
            return 'L496';
        } elseif ($code == 'K') {
            return '674S';
        } elseif ($code == 'L') {
            return 'IqRs';
        } elseif ($code == 'M') {
            return 'Rama';
        } elseif ($code == 'N') {
            return 'Kilo';
        } elseif ($code == 'O') {
            return 'PorT';
        } elseif ($code == 'P') {
            return 'Stea';
        } elseif ($code == 'Q') {
            return 'aTeM';
        } elseif ($code == 'R') {
            return '#4@p';
        } elseif ($code == 'S') {
            return '*9$N';
        } elseif ($code == 'T') {
            return 'NiPs';
        } elseif ($code == 'U') {
            return 'IobT';
        } elseif ($code == 'V') {
            return 'PpRT';
        } elseif ($code == 'W') {
            return 'wTvs';
        } elseif ($code == 'X') {
            return 'SunT';
        } elseif ($code == 'Y') {
            return 'umRT';
        } elseif ($code == 'Z') {
            return 'PrS!';
        } elseif ($code == 'a') {
            return 'ooEV';
        } elseif ($code == 'b') {
            return 'EmpT';
        } elseif ($code == 'c') {
            return 'Rt@P';
        } elseif ($code == 'd') {
            return '#41B';
        } elseif ($code == 'e') {
            return 'Yeyo';
        } elseif ($code == 'f') {
            return 'ZxMU';
        } elseif ($code == 'g') {
            return 'LuMk';
        } elseif ($code == 'h') {
            return 'SaWa';
        } elseif ($code == 'i') {
            return 'Eaws';
        } elseif ($code == 'j') {
            return 'GliM';
        } elseif ($code == 'k') {
            return 'NoNS';
        } elseif ($code == 'l') {
            return 'SiIB';
        } elseif ($code == 'm') {
            return 'prEA';
        } elseif ($code == 'n') {
            return 'ApEM';
        } elseif ($code == 'o') {
            return 'MoeN';
        } elseif ($code == 'p') {
            return 'NoST';
        } elseif ($code == 'q') {
            return 'SeTs';
        } elseif ($code == 'r') {
            return 'RasP';
        } elseif ($code == 's') {
            return 'PaRT';
        } elseif ($code == 't') {
            return 'TrUs';
        } elseif ($code == 'u') {
            return 'LuTr';
        } elseif ($code == 'v') {
            return 'rGgT';
        } elseif ($code == 'w') {
            return 'S@sY';
        } elseif ($code == 'x') {
            return 'YeTr';
        } elseif ($code == 'y') {
            return 'GeTr';
        } elseif ($code == 'z') {
            return 'TrSe';
        } elseif ($code == '0') {
            return 'OE#@';
        } elseif ($code == '1') {
            return 'PsT@';
        } elseif ($code == '2') {
            return 'TrO$';
        } elseif ($code == '3') {
            return '$sTp';
        } elseif ($code == '4') {
            return 'qoRp';
        } elseif ($code == '5') {
            return '?GrP';
        } elseif ($code == '6') {
            return 'OeMr';
        } elseif ($code == '7') {
            return 'StmR';
        } elseif ($code == '8') {
            return 'EpR!';
        } elseif ($code == '9') {
            return 'StpS';
        } elseif ($code == ' ') {
            return 'tP#3';
        } else {
            return "";
        }
    }

    function getChar($code)
    {
        if ($code == '$rSv') {
            return 'A';
        } elseif ($code == 'Grp2') {
            return 'B';
        } elseif ($code == 'SnMp') {
            return 'C';
        } elseif ($code == 'Tr#4') {
            return 'D';
        } elseif ($code == '69!4') {
            return 'E';
        } elseif ($code == 'PpQr') {
            return 'F';
        } elseif ($code == 'TpSO') {
            return 'G';
        } elseif ($code == 'IvSr') {
            return 'H';
        } elseif ($code == 'LpTs') {
            return 'I';
        } elseif ($code == 'L496') {
            return 'J';
        } elseif ($code == '674S') {
            return 'K';
        } elseif ($code == 'IqRs') {
            return 'L';
        } elseif ($code == 'Rama') {
            return 'M';
        } elseif ($code == 'Kilo') {
            return 'N';
        } elseif ($code == 'PorT') {
            return 'O';
        } elseif ($code == 'Stea') {
            return 'P';
        } elseif ($code == 'aTeM') {
            return 'Q';
        } elseif ($code == '#4@p') {
            return 'R';
        } elseif ($code == '*9$N') {
            return 'S';
        } elseif ($code == 'NiPs') {
            return 'T';
        } elseif ($code == 'IobT') {
            return 'U';
        } elseif ($code == 'PpRT') {
            return 'V';
        } elseif ($code == 'wTvs') {
            return 'W';
        } elseif ($code == 'SunT') {
            return 'X';
        } elseif ($code == 'umRT') {
            return 'Y';
        } elseif ($code == 'PrS!') {
            return 'Z';
        } elseif ($code == 'ooEV') {
            return 'a';
        } elseif ($code == 'EmpT') {
            return 'b';
        } elseif ($code == 'Rt@P') {
            return 'c';
        } elseif ($code == '#41B') {
            return 'd';
        } elseif ($code == 'Yeyo') {
            return 'e';
        } elseif ($code == 'ZxMU') {
            return 'f';
        } elseif ($code == 'LuMk') {
            return 'g';
        } elseif ($code == 'SaWa') {
            return 'h';
        } elseif ($code == 'Eaws') {
            return 'i';
        } elseif ($code == 'GliM') {
            return 'j';
        } elseif ($code == 'NoNS') {
            return 'k';
        } elseif ($code == 'SiIB') {
            return 'l';
        } elseif ($code == 'prEA') {
            return 'm';
        } elseif ($code == 'ApEM') {
            return 'n';
        } elseif ($code == 'MoeN') {
            return 'o';
        } elseif ($code == 'NoST') {
            return 'p';
        } elseif ($code == 'SeTs') {
            return 'q';
        } elseif ($code == 'RasP') {
            return 'r';
        } elseif ($code == 'PaRT') {
            return 's';
        } elseif ($code == 'TrUs') {
            return 't';
        } elseif ($code == 'LuTr') {
            return 'u';
        } elseif ($code == 'rGgT') {
            return 'v';
        } elseif ($code == 'S@sY') {
            return 'w';
        } elseif ($code == 'YeTr') {
            return 'x';
        } elseif ($code == 'GeTr') {
            return 'y';
        } elseif ($code == 'TrSe') {
            return 'z';
        } elseif ($code == 'OE#@') {
            return '0';
        } elseif ($code == 'PsT@') {
            return '1';
        } elseif ($code == 'TrO$') {
            return '2';
        } elseif ($code == '$sTp') {
            return '3';
        } elseif ($code == 'qoRp') {
            return '4';
        } elseif ($code == '?GrP') {
            return '5';
        } elseif ($code == 'OeMr') {
            return '6';
        } elseif ($code == 'StmR') {
            return '7';
        } elseif ($code == 'EpR!') {
            return '8';
        } elseif ($code == 'StpS') {
            return '9';
        } elseif ($code == 'tP#3') {
            return ' ';
        } else {
            return "";
        }
    }
}

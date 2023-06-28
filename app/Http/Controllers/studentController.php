<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

date_default_timezone_set('Africa/Nairobi');

class studentController extends Controller
{
    //this handles all the students data
    function studentCourseMaterials(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the subjects done in the student class
        $student_data = session("student_information");
        // return $student_data;
        
        // get the student class plus all the subjects the student is doing.
        $adm_no = $student_data->adm_no;
        $stud_class = $student_data->stud_class;

        // get the student
        $subjects_done = DB::select("SELECT * FROM `table_subject` WHERE `classes_taught` LIKE '%".$stud_class."%'");
        
        // get the subject details
        $student_notification = $this->getStudentMessage();
        // return $student_notification;
        return view("student_cm",["student_notification" => $student_notification,"subjects_done" => $subjects_done]);
    }

    // get the course materials for the subject they have selected
    function getCourseMaterials($subject_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the student resources that have been set by the teacher on the daily lesson plan!
        $student_data = session("student_information");
        
        // get the student class plus all the subjects the student is doing.
        $adm_no = $student_data->adm_no;
        $stud_class = $student_data->stud_class;

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y",strtotime($academic_calender[0]->start_time));
            $end_date = date("Y",strtotime($academic_calender[2]->end_time));
        }else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date.":".$end_date;

        // get the lesson plan for the subject the student has selected
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `class` = ? AND `academic_year` = ?",[$subject_id,$stud_class,$academic_year]);

        // return the lesson plan
        $short_term_plan = [];
        $medium_term_plan = [];
        $longterm_plan_data = [];
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = '".$subject_id."'");
        if (count($lesson_plan) > 0) {
            $short_term_plan = $lesson_plan[0]->short_term_plan;
            $medium_term_plan= $lesson_plan[0]->medium_term_plan;
            $longterm_plan_data= $lesson_plan[0]->longterm_plan_data;

            // decode short term lesson plan
            $short_term_plan =  $this->isJson_report($short_term_plan) ? json_decode($short_term_plan) : [];
            $medium_term_plan =  $this->isJson_report($medium_term_plan) ? json_decode($medium_term_plan) : [];
            $longterm_plan_data =  $this->isJson_report($longterm_plan_data) ? json_decode($longterm_plan_data) : [];
        }

        // return the short term plan
        // return $medium_term_plan;
        // return $academic_calender;
        $days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

        // STORE DATES HERE
        $dates_details = [];
        
        // TERM ONE STARTS HERE
        // process term one
        $start_time = $academic_calender[0]->start_time;
        $closing_date = $academic_calender[0]->closing_date;
        $day = date("D",strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index=0; $index < count($days); $index++) { 
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;
        
        $start_date = $this->addDays($start_time,-$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date,$closing_date);
        // return $weeks;

        for ($index=0; $index < $weeks; $index++) { 
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd",strtotime($start_date));
            $start_date = $this->addDays($start_date,6);
            $dates_detail->date_end = date("Ymd",strtotime($start_date));
            $dates_detail->week = "".($index+1);
            $dates_detail->term = "Term 1";
            $start_date = $this->addDays($start_date,1);

            array_push($dates_details,$dates_detail);
        }
        // END OF TERM 1

        // TERM TWO STARTS HERE
        // process term one
        $start_time = $academic_calender[1]->start_time;
        $closing_date = $academic_calender[1]->closing_date;
        $day = date("D",strtotime($start_time));
        
        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index=0; $index < count($days); $index++) { 
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;
        
        $start_date = $this->addDays($start_time,-$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date,$closing_date);
        // return $weeks;

        for ($index=0; $index < $weeks; $index++) { 
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd",strtotime($start_date));
            $start_date = $this->addDays($start_date,6);
            $dates_detail->date_end = date("Ymd",strtotime($start_date));
            $dates_detail->week = "".($index+1);
            $dates_detail->term = "Term 2";
            $start_date = $this->addDays($start_date,1);

            array_push($dates_details,$dates_detail);
        }
        // END OF TERM TWO

        // TERM THREE STARTS HERE
        // process term one
        $start_time = $academic_calender[2]->start_time;
        $closing_date = $academic_calender[2]->closing_date;
        $day = date("D",strtotime($start_time));
        
        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index=0; $index < count($days); $index++) { 
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;
        
        $start_date = $this->addDays($start_time,-$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date,$closing_date);
        // return $weeks;

        for ($index=0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd",strtotime($start_date));
            $start_date = $this->addDays($start_date,6);
            $dates_detail->date_end = date("Ymd",strtotime($start_date));
            $dates_detail->week = "".($index+1);
            $dates_detail->term = "Term 3";
            $start_date = $this->addDays($start_date,1);

            array_push($dates_details,$dates_detail);
        }

        // END OF TERM THREE
        $student_notification = $this->getStudentMessage();
        return view("subject_cm",["student_notification" => $student_notification,"longterm_plan_data" => $longterm_plan_data,"dates_details" => $dates_details,"medium_term_plan" => $medium_term_plan,"subject_details" => $subject_details[0], "short_term_plan" => $short_term_plan,"student_data" => $student_data]);
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
    function getStudentMessage(){
        if (session("school_information") == null) {
            return redirect("/");
        }
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
    function getStudentNotification(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the student notifications
        $adm_no = session("student_information")->adm_no;

        // get the teacher`s notification
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_id` = '".$adm_no."' AND `owner_type` = 'student' ORDER BY `id` DESC");

        $student_notification = $this->getStudentMessage();
        return view("student_message",["student_notification" => $student_notification,"notifications" => $notifications]);
    }
    function readStudentNotifications($notification_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the nofication details
        $notifications_details = DB::select("SELECT * FROM `message_n_alert` WHERE `id` = ?",[$notification_id]);
        // return $notifications_details;

        if (count($notifications_details) == 0) {
            return redirect("/Parent/Alert");
        }

        if ($notifications_details[0]->message_status == 0) {
            $update = DB::update("UPDATE `message_n_alert` SET `message_status` = '1' WHERE `id` = ?",[$notification_id]);
        }

        $student_notification = $this->getStudentMessage();
        return view("read_student_alert",["student_notification" => $student_notification,"notifications_details" => $notifications_details]);
    }
    function addDays($date,$days){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($days." day"));
        return date_format($date,"YmdHis");
    }
    function addDay($date,$days){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($days." day"));
        return date_format($date,"Y-m-d");
    }

    function addMonths($date,$months){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($months." Month"));
        return date_format($date,"YmdHis");
    }
    function addYear($date,$years){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($years." Year"));
        return date_format($date,"YmdHis");
    }
    function get_weeks_between_dates($start_date, $end_date) {
        // Convert the dates to UNIX timestamps
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        // Calculate the number of seconds in a week
        $seconds_in_week = 7 * 24 * 60 * 60;

        // Calculate the difference between the two timestamps
        $difference = abs($end_timestamp - $start_timestamp);

        // Calculate the number of weeks between the two dates
        $weeks = floor($difference / $seconds_in_week);

        // Return the number of weeks
        return $weeks;
    }

    function isJson_report($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    function updateStudentPassword(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $request;
        $password = $request->input("password");
        $newpassword = $request->input("newpassword");
        $renewpassword = $request->input("renewpassword");
        $student_adm = $request->input("student_adm");

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // check if the passwords match
        if ($renewpassword != $newpassword) {
            session()->flash("invalid","Passwords don`t match!");
            return redirect("/Student/Profile");
        }
        
        // convert password to portal password
        $old_password = $this->encryptCode($password);
        
        // check if the student provides the correct admission no
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ? AND `portal_password` = ?",[$student_adm,$old_password]);

        // check the student data
        if (count($student_data) == 0) {
            session()->flash("invalid","Passwords provided is in-correct!");
            return redirect("/Student/Profile");
        }

        // update the new password
        $convert_new_password = $this->encryptCode($newpassword);
        $update_password = DB::update("UPDATE `student_data` SET `portal_password` = ? WHERE `adm_no` = ?",[$convert_new_password,$student_adm]);


        session()->flash("valid","Passwords Updated Successfully!");
        return redirect("/Student/Profile");
    }


    function getStudentData(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the student data
        $student_admno = session("student_information")->adm_no;
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the data
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = '".$student_admno."'");
        if (count($student_data) == 0) {
            return redirect("/Student/Dashboard");
        }

        for ($index=0; $index < count($student_data); $index++) { 
            // get the class teachers name
            $class_teacher = "Teacher Not Available";
            $student_class = $student_data[$index]->stud_class;

            // class teacher data
            $class_teacher_data = DB::select("SELECT * FROM `class_teacher_tbl` WHERE `class_assigned` = '".$student_class."'");
            if (count($class_teacher_data) > 0) {
                $class_teacher_id = $class_teacher_data[0]->class_teacher_id;

                DB::setDefaultConnection("mysql");
                // get the teacher`s name
                $teacher_name = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$class_teacher_id]);

                $class_teacher = count($teacher_name) > 0 ? ($teacher_name[0]->gender == "M" ? "Mr" : "Ms").". ".$teacher_name[0]->fullname : "Teacher Not Available";
            }
        }

        // return the student data
        $student_notification = $this->getStudentMessage();
        
        // return $student_data;
        return view("student_profile",["class_teacher" => $class_teacher, "student_data" => $student_data[0], "student_notification" => $student_notification]);
    }
    function discussionForum(){
        return view("coming_soon");
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

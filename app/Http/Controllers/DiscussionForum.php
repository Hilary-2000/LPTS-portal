<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use stdClass;

date_default_timezone_set('Africa/Nairobi');
class DiscussionForum extends Controller
{
    //
    function discussionForum(Request $request){
        // database name
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        // get the teacher`s message senders.
        $staff_infor = session("staff_infor");
        // get parent chats
        $parents_chats = DB::select("SELECT * FROM `chats` WHERE (`chat_recipient` = ? OR `chat_sender` = ?) AND `recipient_type` = 'parent'",[$staff_infor->user_id,$staff_infor->user_id]);
        // get student chats
        $students_chats = DB::select("SELECT * FROM `chats` WHERE (`chat_recipient` = ? OR `chat_sender` = ?) AND `recipient_type` = 'student';",[$staff_infor->user_id,$staff_infor->user_id]);
        // get teacher chats
        $teacher_chats = DB::select("SELECT * FROM `chats` WHERE (`chat_recipient` = ? OR `chat_sender` = ?) AND `recipient_type` = 'staff'",[$staff_infor->user_id,$staff_infor->user_id]);
        
        // get the student data
        $student_data = DB::select("SELECT * FROM `student_data`");
        DB::setDefaultConnection("mysql");

        // get staff data
        $school_information = session("school_information");
        $staff_data = DB::select("SELECT * FROM `user_tbl` WHERE `school_code` = ?",[$school_information->school_code]);
        
        DB::setDefaultConnection("mysql2");

        // WHAT STUDENTS HAVE I SENT A MESSAGE
        $students_sent = [];
        foreach ($students_chats as $key => $value) {
            // check receipients
            if ($value->chat_recipient != $staff_infor->user_id) {
                if (!in_array($value->chat_recipient,$students_sent)) {
                    array_push($students_sent,$value->chat_recipient);
                }
            }
            // check senders
            if ($value->chat_sender != $staff_infor->user_id) {
                if (!in_array($value->chat_sender,$students_sent)) {
                    array_push($students_sent,$value->chat_sender);
                }
            }
        }
        // return $students_sent;
        $students_chats = [];
        foreach ($students_sent as $key => $value) {
            $student_chat = new stdClass();
            $student_chat->student_name = "N/A";
            $student_chat->student_adm_no = $value;
            $student_chat->student_detail = null;
            // last chat
            $last_chat = DB::select("SELECT * FROM `chats` WHERE (`chat_recipient` = ? OR `chat_sender` = ?) AND (`chat_recipient` = ? OR `chat_sender` = ?) ORDER BY `chat_id` DESC LIMIT 1",[$value,$value,$staff_infor->user_id,$staff_infor->user_id]);
            $last_message = count($last_chat) > 0 ? $last_chat[0] : null;
            $student_chat->last_chat = $last_message;

            for ($index=0; $index < count($student_data); $index++) { 
                if ($student_data[$index]->adm_no == $value) {
                    $student_detail = new stdClass();
                    $student_detail->name = ucwords(strtolower($student_data[$index]->first_name." (".$student_data[$index]->adm_no." - ".$this->classNameAdms($student_data[$index]->stud_class).")"));
                    $student_detail->adm_no = $value;
                    $student_chat->student_detail = $student_detail;
                    $student_chat->student_name = ucwords(strtolower($student_data[$index]->first_name." (".$student_data[$index]->adm_no." - ".$this->classNameAdms($student_data[$index]->stud_class).")"));
                    break;
                }
            }
            array_push($students_chats,$student_chat);
        }
        // return $students_chats;

        // set the sender names
        $parents_sent = [];
        foreach ($parents_chats as $key => $value) {
            // check receipients
            if ($value->chat_recipient != $staff_infor->user_id) {
                if (!in_array($value->chat_recipient,$parents_sent)) {
                    array_push($parents_sent,$value->chat_recipient);
                }
            }
            // check senders
            if ($value->chat_sender != $staff_infor->user_id) {
                if (!in_array($value->chat_sender,$parents_sent)) {
                    array_push($parents_sent,$value->chat_sender);
                }
            }
        }
        $parents_chats = [];
        foreach ($parents_sent as $key => $value) {
            $parent = new stdClass();
            $parent->parent_name = "N/A";
            $parent->parent_contact = $value;
            // last chat
            $last_chat = DB::select("SELECT * FROM `chats` WHERE (`chat_recipient` = ? OR `chat_sender` = ?) AND (`chat_recipient` = ? OR `chat_sender` = ?) ORDER BY `chat_id` DESC LIMIT 1",[$value,$value,$staff_infor->user_id,$staff_infor->user_id]);
            $last_message = count($last_chat) > 0 ? $last_chat[0] : null;
            $parent->last_chat = $last_message;

            for ($index=0; $index < count($student_data); $index++) { 
                if ($student_data[$index]->parentContacts == $value) {
                    $parent->parent_name = ucwords(strtolower($student_data[$index]->parentName));
                    break;
                }
            }
            if ($parent->parent_name == "N/A") {
                for ($index=0; $index < count($student_data); $index++) { 
                    if ($student_data[$index]->parent_contact2 == $value) {
                        $parent->parent_name = ucwords(strtolower($student_data[$index]->parent_name2));
                        break;
                    }
                }
            }
            array_push($parents_chats,$parent);
        }
        
        // set the sender names
        $teacher_sent = [];
        foreach ($teacher_chats as $key => $value) {
            // check receipients
            if ($value->chat_recipient != $staff_infor->user_id) {
                if (!in_array($value->chat_recipient,$teacher_sent)) {
                    array_push($teacher_sent,$value->chat_recipient);
                }
            }
            // check senders
            if ($value->chat_sender != $staff_infor->user_id) {
                if (!in_array($value->chat_sender,$teacher_sent)) {
                    array_push($teacher_sent,$value->chat_sender);
                }
            }
        }
        // return $students_sent;
        $teacher_chats = [];
        foreach ($students_sent as $key => $value) {
            $teacher_chat = new stdClass();
            $teacher_chat->teacher_name = "N/A";
            $teacher_chat->teacher_id = $value;
            // last chat
            $last_chat = DB::select("SELECT * FROM `chats` WHERE (`chat_recipient` = ? OR `chat_sender` = ?) AND (`chat_recipient` = ? OR `chat_sender` = ?) ORDER BY `chat_id` DESC LIMIT 1",[$value,$value,$staff_infor->user_id,$staff_infor->user_id]);
            $last_message = count($last_chat) > 0 ? $last_chat[0] : null;
            $teacher_chat->last_chat = $last_message;

            for ($index=0; $index < count($staff_data); $index++) { 
                if ($staff_data[$index]->user_id == $value) {
                    $teacher_chat->teacher_name = ucwords(strtolower($staff_data[$index]->fullname));
                    break;
                }
            }
            array_push($teacher_chats,$teacher_chat);
        }

        // get the classes
        DB::setDefaultConnection("mysql2");
        $classes = DB::select("SELECT * FROM `settings` WHERE `sett` = 'class'");
        $student_contacts = [];
        if (count($classes)) {
            $classes = explode(",",$classes[0]->valued);
            foreach ($classes as $key => $class) {
                $define_class = new stdClass();
                $define_class->class = $class;
                $define_class->class_name = $this->classNameAdms($class);
                $define_class->students = [];
                
                // add the class to the list
                array_push($student_contacts,$define_class);
            }
        }

        // loop to add the students
        foreach ($student_contacts as $key => $student_contact) {
            foreach ($student_data as $key_student => $student) {
                if ($student_contact->class == $student->stud_class) {
                    $new_student = new stdClass();
                    $new_student->student_fullname = ucwords(strtolower($student->first_name." ".$student->second_name));
                    $new_student->adm_no = $student->adm_no;
                    array_push($student_contacts[$key]->students,$new_student);
                }
            }
        }

        // get the staff contacts
        $staff_contacts = [];
        foreach ($staff_data as $key => $value) {
            $new_staff = new stdClass();
            $new_staff->staff_name = $value->fullname;
            $new_staff->staff_id = $value->user_id;
            array_push($staff_contacts,$new_staff);
        }
        $alphabet = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $new_staff_contacts = [];
        foreach ($alphabet as $key => $letter) {
            $group = new stdClass();
            $group->letter = $letter;
            $group->teachers = [];
            foreach ($staff_contacts as $key_staff => $staff) {
                if (strtoupper(substr($staff->staff_name,0,1)) == $letter) {
                    array_push($group->teachers,$staff);
                }
            }
            if (count($group->teachers) > 0) {
                array_push($new_staff_contacts,$group);
            }
        }

        // get the parents contacts
        $parent_contacts = [];
        $parent_contact_check = [];
        foreach ($student_data as $key => $student) {
            // check if the primary parent contact is present
            if(!in_array($student->parentContacts,$parent_contact_check)){
                if ($student->stud_class == "-1" || $student->stud_class == "-2" || $student->parentContacts == "" || $student->parentContacts == null || strlen(trim($student->parentContacts)) == 0) {
                    continue;
                }
                array_push($parent_contact_check,$student->parentContacts);
                $new_parent = new stdClass();
                $new_parent->parent_name = $student->parentName;
                $new_parent->parent_contact = $student->parentContacts;
                $new_parent->parent_relationship = $student->parent_relation;
                $new_parent->parent_to = [ucwords(strtolower($student->first_name." ".$student->second_name))." (".$student->adm_no." - ".$this->classNameAdms($student->stud_class).")"];
                array_push($parent_contacts,$new_parent);
            }else{
                // look for the parent with that contact and update their children
                foreach ($parent_contacts as $key_contact => $value) {
                    if ($value->parent_contact == $student->parentContacts) {
                        if (!in_array(ucwords(strtolower($student->first_name." ".$student->second_name))." (".$student->adm_no." - ".$this->classNameAdms($student->stud_class).")",$parent_contacts[$key_contact]->parent_to)) {
                            array_push($parent_contacts[$key_contact]->parent_to,ucwords(strtolower($student->first_name." ".$student->second_name))." (".$student->adm_no." - ".$this->classNameAdms($student->stud_class).")");
                        }
                    }
                }
            }

            // check if the secondary parent contact is present
            if(!in_array($student->parent_contact2,$parent_contact_check)){
                if ($student->stud_class == "-1" || $student->stud_class == "-2" || $student->parent_contact2 == "" || $student->parent_contact2 == null || strlen(trim($student->parent_contact2)) == 0) {
                    continue;
                }
                array_push($parent_contact_check,$student->parent_contact2);
                $new_parent = new stdClass();
                $new_parent->parent_name = $student->parent_name2;
                $new_parent->parent_contact = $student->parent_contact2;
                $new_parent->parent_relationship = $student->parent_relation2;
                $new_parent->parent_to = [ucwords(strtolower($student->first_name." ".$student->second_name))." (".$student->adm_no." - ".$this->classNameAdms($student->stud_class).")"];
                array_push($parent_contacts,$new_parent);
            }else{
                // look for the parent with that contact and update their children
                foreach ($parent_contacts as $key_contact => $value) {
                    if ($value->parent_contact == $student->parent_contact2) {
                        if (!in_array(ucwords(strtolower($student->first_name." ".$student->second_name))." (".$student->adm_no." - ".$this->classNameAdms($student->stud_class).")",$parent_contacts[$key_contact]->parent_to)) {
                            array_push($parent_contacts[$key_contact]->parent_to,ucwords(strtolower($student->first_name." ".$student->second_name))." (".$student->adm_no." - ".$this->classNameAdms($student->stud_class).")");
                        }
                    }
                }
            }
        }
        $new_parents = [];
        foreach ($alphabet as $key => $letter) {
            $parents = new stdClass();
            $parents->letter = $letter;
            $parents->parents = [];
            foreach ($parent_contacts as $key_contact => $parent) {
                if (substr($parent->parent_name,0,1) == $letter) {
                    array_push($parents->parents,$parent);
                }
            }
            if (count($parents->parents) > 0) {
                array_push($new_parents,$parents);
            }
        }
        // Convert the array to a collection
        $collection = new Collection($students_chats);

        // Sort the collection based on 'date_sent'
        $students_chats = $collection->sortByDesc(function ($item) {
            return $item->last_chat->date_sent;
        })->values()->all();

        // Convert the array to a collection
        $collection = new Collection($teacher_chats);

        // Sort the collection based on 'date_sent'
        $teacher_chats = $collection->sortByDesc(function ($item) {
            return $item->last_chat->date_sent;
        })->values()->all();

        // Convert the array to a collection
        $collection = new Collection($parents_chats);

        // Sort the collection based on 'date_sent'
        $parents_chats = $collection->sortByDesc(function ($item) {
            return $item->last_chat->date_sent;
        })->values()->all();
        
        if (!$request->input("get_teacher_chats")) {
            $teacher_notifications = $this->getTrsNotification();
            return view("teacher_chatroom",["teacher_notifications" => $teacher_notifications,"student_chats" => $students_chats,"teacher_chats" => $teacher_chats, "parent_chats" => $parents_chats,"student_contacts" => $student_contacts,"staff_contacts" => $new_staff_contacts,"parent_contacts" => $new_parents]);
        }else{
            return ["student_chats" => $students_chats,"teacher_chats" => $teacher_chats, "parent_chats" => $parents_chats,"student_contacts" => $student_contacts,"staff_contacts" => $new_staff_contacts,"parent_contacts" => $new_parents];
        }
    }
    function compareDateSent($a, $b) {
        return strcmp($a['last_chat']['date_sent'], $b['last_chat']['date_sent']);
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

    // get the date difference
    function dateDifference($date1, $date2) {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
    
        $interval = $datetime1->diff($datetime2);
    
        return $interval;
    }

    // get the classname
    function classNameAdms($data)
    {
        if ($data == "-1") {
            return "Alumni";
        }
        if ($data == "-2") {
            return "Transfered";
        }
        $datas = "Grade " . $data;
        if (strlen($data) > 1) {
            $datas = $data;
        }
        return $datas;
    }
    function getTeacherDiscussion(Request $req){
        if (session("school_information") == null) {
            return "An error occured!";
        }

        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        $teacher_id = session("staff_infor")->user_id;

        // get the messages shared between the sender and this receipients
        $get_message = DB::select("SELECT * FROM `chats` WHERE `chat_sender` = ? AND chat_recipient = ? ORDER BY `chat_id` ASC LIMIT 300",[$teacher_id, $req->input("recipient_id")]);

        // process the message with dates
        if (count($get_message) > 0) {
            $earliest_date = date("Ymd",strtotime($get_message[0]->date_sent)) * 1;
            // $earliest_date = date("Ymd",strtotime("-30 days"));
            $today = date("Ymd")*1;
            $difference = $this->dateDifference($earliest_date,$today);
            $days = ($difference->d == 0) ? 1 : ($difference->d*1);
            // return $today;


            // get dates
            $dates = [];
            for ($index=0; $index <= $days; $index++) {
                $datetime = new DateTime($earliest_date);
                $datetime->modify('+1 days');
                array_push($dates,$earliest_date*1);
                $earliest_date = $datetime->format('Ymd');
            }
            // return $dates;

            // go to all dates
            $messages = [];
            foreach ($dates as $key => $date1) {
                $date_messages = new stdClass();
                $date_messages->date = $date1;
                $date_messages->full_date = ($today == $date1) ? "Today" : date("D dS M Y",strtotime($date1));
                $date_messages->messages = [];
                foreach ($get_message as $key_msg => $message) {
                    if (substr($message->date_sent,0,8) == $date1) {
                        $message->time_sent = date("H:i A",strtotime($message->date_sent));
                        $message->date_sent = date("D dS M Y",strtotime($message->date_sent));
                        array_push($date_messages->messages,$message);
                    }
                }
                if (count($date_messages->messages) > 0) {
                    array_push($messages,$date_messages);
                }
            }
            $get_message = $messages;
        }
        
        return ["messages" => $get_message, "sender" => $teacher_id];
    }

    function send_message(Request $req){
        if (session("school_information") == null) {
            return "An error occured!";
        }

        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        $teacher_id = session("staff_infor")->user_id;

        // insert
        $message_content = $req->input("message_content");
        $receipient = $req->input("receipient");
        $sender_type = $req->input("sender_type");
        $receipient_type = $req->input("receipient_type");
        $date_sent = date("YmdHis");
        $chat_sender = $teacher_id;
        $insert = DB::insert("INSERT INTO `chats` (`chat_content`,`chat_sender`,`chat_recipient`,`date_sent`,`sender_type`,`recipient_type`) VALUES (?,?,?,?,?,?)",[$message_content,$chat_sender,$receipient,$date_sent,$sender_type,$receipient_type]);

        // send a success message
        return ["success" => true, "message" => "Message sent successfully"];
    }
}

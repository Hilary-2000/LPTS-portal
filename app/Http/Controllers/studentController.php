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
}

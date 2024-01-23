<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Google_Service_Exception;
use Google_Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class lessonPlan extends Controller
{
    function updateMediumPlan(Request $req)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $req;
        $subject_id = $req->input("subject_id");
        $subject_class = $req->input("subject_class");
        $hold_data = $req->input("hold_data");
        $hold_data_origin = $req->input("hold_data_origin");
        $holder_data = $req->input("hold_data");

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;

        $select = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$subject_id, $academic_year, $subject_class]);

        // get the medium term plan
        if (count($select) > 0) {
            $medium_term_plan = $select[0]->medium_term_plan != null ? $select[0]->medium_term_plan : "[]";

            // get the correct week index and term for that particular plan you are updating
            if ($this->isJson_report($hold_data)) {
                $hold_data = json_decode($hold_data);
                $week_name = $hold_data->week_name;
                $term_name = $hold_data->term_name;

                // go through the existing medium plan and get the medium term plan if present
                if ($this->isJson_report($medium_term_plan)) {
                    $medium_term_plan = json_decode($medium_term_plan);
                    // return $medium_term_plan;

                    // loop to see if present
                    $is_present = 0;
                    for ($index = 0; $index < count($medium_term_plan); $index++) {
                        $week_name_in = $medium_term_plan[$index]->week_name;
                        $term_name_in = $medium_term_plan[$index]->term_name;

                        if ($week_name == $week_name_in && $term_name == $term_name_in) {
                            $is_present = 1;
                            break;
                        }
                    }
                    // return $is_present;

                    if ($is_present == 1) {
                        // its present
                        $new_medium_plans = [];
                        for ($index = 0; $index < count($medium_term_plan); $index++) {
                            $week_name_in = $medium_term_plan[$index]->week_name;
                            $term_name_in = $medium_term_plan[$index]->term_name;

                            if ($week_name == $week_name_in && $term_name == $term_name_in) {
                                array_push($new_medium_plans, $hold_data);
                            } else {
                                array_push($new_medium_plans, $medium_term_plan[$index]);
                            }
                        }
                        $medium_term_plan = $new_medium_plans;
                    } else {
                        // its absent this means you need to add it to the array list
                        array_push($medium_term_plan, $hold_data);
                    }

                    // return $medium_term_plan;
                    $medium_term_plan = json_encode($medium_term_plan);
                    $update = DB::update("UPDATE `lesson_plan` SET `medium_term_plan` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$medium_term_plan, $subject_id, $academic_year, $subject_class]);

                    session()->flash("strand_success", "Data has been successfully update!");
                    return redirect("/Teacher/CreatePlan/Medium/" . $subject_id . "/class/" . $subject_class . "");
                } else {
                    session()->flash("strand_error", "An error has occured!");
                    return redirect("/Teacher/CreatePlan/Medium/" . $subject_id . "/class/" . $subject_class . "");
                }
            } else {
                session()->flash("strand_error", "An error has occured!");
                return redirect("/Teacher/CreatePlan/Medium/" . $subject_id . "/class/" . $subject_class . "");
            }
        } else {
            session()->flash("strand_error", "An error has occured!");
            return redirect("/Teacher/CreatePlan/Medium/" . $subject_id . "/class/" . $subject_class . "");
        }
    }

    function addDays($date, $days)
    {
        $date = date_create($date);
        date_add($date, date_interval_create_from_date_string($days . " day"));
        return date_format($date, "YmdHis");
    }
    function addDay($date, $days)
    {
        $date = date_create($date);
        date_add($date, date_interval_create_from_date_string($days . " day"));
        return date_format($date, "Y-m-d");
    }

    function addMonths($date, $months)
    {
        $date = date_create($date);
        date_add($date, date_interval_create_from_date_string($months . " Month"));
        return date_format($date, "YmdHis");
    }
    function addYear($date, $years)
    {
        $date = date_create($date);
        date_add($date, date_interval_create_from_date_string($years . " Year"));
        return date_format($date, "YmdHis");
    }
    function get_weeks_between_dates($start_date, $end_date)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
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

    function createMediumPlanHOD($lesson_id,$class)
    {
        // get the subject details
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // check if the teacher teaches the subject
        $staff_id = session("staff_infor")->user_id;
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ? AND `teachers_id` LIKE '%(" . $staff_id . ":" . $class . ")%'", [$lesson_id]);

        // if (count($subject_details) < 1) {
        //     session()->flash("invalid", "You do not teach the subject for that particular class!");
        //     DB::setDefaultConnection("mysql");
        //     return redirect("/Teacher/LessonPlan");
        // }

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;

        // academic calender and weeks
        // return $academic_calender;
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        // STORE DATES HERE
        $dates_details = [];

        // TERM ONE STARTS HERE
        // process term one
        $start_time = $academic_calender[0]->start_time;
        $closing_date = $academic_calender[0]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("D dS M Y", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("D dS M Y", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 1";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM 1

        // TERM TWO STARTS HERE
        // process term one
        $start_time = $academic_calender[1]->start_time;
        $closing_date = $academic_calender[1]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("D dS M Y", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("D dS M Y", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 2";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM TWO

        // TERM THREE STARTS HERE
        // process term one
        $start_time = $academic_calender[2]->start_time;
        $closing_date = $academic_calender[2]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("D dS M Y", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("D dS M Y", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 3";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM THREE
        // return $dates_details;


        // get the lesson plan
        $medium_term_status = 0;
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$lesson_id, $academic_year, $class]);
        if (count($lesson_plan) > 0) {
            $lesson_plans = $lesson_plan;
            $medium_term_status = $lesson_plan[0]->medium_term_status;
            $lesson_plan = $lesson_plan[0]->medium_term_plan == null ? "[]" : $lesson_plan[0]->medium_term_plan;
            // return $lesson_plans;
            $long_term_plan = $this->isJson_report($lesson_plans[0]->longterm_plan_data) ? json_decode($lesson_plans[0]->longterm_plan_data) : [];
            // return $lesson_plans;
            $lp_id = $lesson_plans[0]->id;

            // get the populator objectives and resources from the long term plan to the medium plan
            // get for term one
            $term_one_data = [];
            $strand_data_in_term_one = [];
            $weeks = 1;
            // return $long_term_plan;
            for ($ind = 0; $ind < count($long_term_plan); $ind++) {
                if ($long_term_plan[$ind]->term == "1") {
                    $strand_data = new stdClass();
                    $strand_data->week = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $strand_data->objectives = $long_term_plan[$ind]->objectives;
                    $strand_data->resources = $long_term_plan[$ind]->learning_materials;
                    $strand_data->term = "Term 1";


                    // store the strand data
                    $my_strand_data = new stdClass();
                    $my_strand_data->strand_name = $long_term_plan[$ind]->strand_name;
                    $my_strand_data->strand_code = $long_term_plan[$ind]->strand_code;
                    $my_strand_data->substrands = [];
                    $my_strand_data->weeks = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $my_strand_data->term = "Term 1";

                    // this are weeks to be added on the weeks used
                    $weeks = $weeks + $long_term_plan[$ind]->period;

                    // get the substrands
                    $sub_strands = $long_term_plan[$ind]->sub_strands;
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $objectives = $sub_strands[$indexes]->objectives;
                        for ($in = 0; $in < count($objectives); $in++) {
                            array_push($strand_data->objectives, $objectives[$in]);
                        }
                        $substrands_name = $sub_strands[$indexes]->name;
                        $substrands_code = $sub_strands[$indexes]->code;

                        // store the substrand data
                        $substrand_data = new stdClass();
                        $substrand_data->substrand_name = $substrands_name;
                        $substrand_data->substrand_code = $substrands_code;

                        array_push($my_strand_data->substrands, $substrand_data);
                    }
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $resources = $sub_strands[$indexes]->learning_materials;
                        for ($in = 0; $in < count($resources); $in++) {
                            array_push($strand_data->resources, $resources[$in]);
                        }
                    }

                    array_push($term_one_data, $strand_data);
                    array_push($strand_data_in_term_one, $my_strand_data);
                }
            }
            // return $strand_data_in_term_one;

            // get for term two
            $term_two_data = [];
            $weeks = 1;
            $strand_data_in_term_two = [];
            for ($ind = 0; $ind < count($long_term_plan); $ind++) {
                if ($long_term_plan[$ind]->term == "2") {
                    $strand_data = new stdClass();
                    $strand_data->week = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $strand_data->objectives = $long_term_plan[$ind]->objectives;
                    $strand_data->resources = $long_term_plan[$ind]->learning_materials;
                    $strand_data->term = "Term 2";

                    // store the strand data
                    $my_strand_data = new stdClass();
                    $my_strand_data->strand_name = $long_term_plan[$ind]->strand_name;
                    $my_strand_data->strand_code = $long_term_plan[$ind]->strand_code;
                    $my_strand_data->substrands = [];
                    $my_strand_data->weeks = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $my_strand_data->term = "Term 2";

                    $weeks = $weeks + $long_term_plan[$ind]->period;

                    // get the substrands
                    $sub_strands = $long_term_plan[$ind]->sub_strands;
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $objectives = $sub_strands[$indexes]->objectives;
                        for ($in = 0; $in < count($objectives); $in++) {
                            array_push($strand_data->objectives, $objectives[$in]);
                        }
                        $substrands_name = $sub_strands[$indexes]->name;
                        $substrands_code = $sub_strands[$indexes]->code;

                        // store the substrand data
                        $substrand_data = new stdClass();
                        $substrand_data->substrand_name = $substrands_name;
                        $substrand_data->substrand_code = $substrands_code;

                        array_push($my_strand_data->substrands, $substrand_data);
                    }

                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $resources = $sub_strands[$indexes]->learning_materials;
                        for ($in = 0; $in < count($resources); $in++) {
                            array_push($strand_data->resources, $resources[$in]);
                        }
                    }

                    array_push($term_two_data, $strand_data);
                    array_push($strand_data_in_term_two, $my_strand_data);
                }
            }

            // return $strand_data_in_term_two;

            // get for term three
            $term_three_data = [];
            $strand_data_in_term_three = [];
            $weeks = 1;
            for ($ind = 0; $ind < count($long_term_plan); $ind++) {
                if ($long_term_plan[$ind]->term == "3") {
                    $strand_data = new stdClass();
                    $strand_data->week = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $strand_data->objectives = $long_term_plan[$ind]->objectives;
                    $strand_data->resources = $long_term_plan[$ind]->learning_materials;
                    $strand_data->term = "Term 3";

                    // store the strand data
                    $my_strand_data = new stdClass();
                    $my_strand_data->strand_name = $long_term_plan[$ind]->strand_name;
                    $my_strand_data->strand_code = $long_term_plan[$ind]->strand_code;
                    $my_strand_data->substrands = [];
                    $my_strand_data->weeks = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $my_strand_data->term = "Term 3";

                    $weeks = $weeks + $long_term_plan[$ind]->period;

                    // get the substrands
                    $sub_strands = $long_term_plan[$ind]->sub_strands;
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $objectives = $sub_strands[$indexes]->objectives;
                        for ($in = 0; $in < count($objectives); $in++) {
                            array_push($strand_data->objectives, $objectives[$in]);
                        }
                        $substrands_name = $sub_strands[$indexes]->name;
                        $substrands_code = $sub_strands[$indexes]->code;

                        // store the substrand data
                        $substrand_data = new stdClass();
                        $substrand_data->substrand_name = $substrands_name;
                        $substrand_data->substrand_code = $substrands_code;

                        array_push($my_strand_data->substrands, $substrand_data);
                    }
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $resources = $sub_strands[$indexes]->learning_materials;
                        for ($in = 0; $in < count($resources); $in++) {
                            array_push($strand_data->resources, $resources[$in]);
                        }
                    }

                    array_push($term_three_data, $strand_data);
                    array_push($strand_data_in_term_three, $my_strand_data);
                }
            }
            // return $strand_data_in_term_three;

            // define the long term plan and its substrands

            // populators
            $populators = array_merge($term_one_data, $term_two_data, $term_three_data);
            $strands_data = array_merge($strand_data_in_term_one, $strand_data_in_term_two, $strand_data_in_term_three);
            $teacher_notifications = $this->getTrsNotification();
            return view("create_medium_plan_hod", ["teacher_notifications" => $teacher_notifications,"lesson_plan_id" => $lp_id , "medium_term_status" => $medium_term_status,"long_term_plan" => $long_term_plan, "strands_data" => $strands_data, "dates_details" => $dates_details, "lesson_plan" => $lesson_plan, "populators" => $populators, "lesson_id" => $lesson_id, "class" => $class, "subject_details" => $subject_details[0], "get_class_name" => $this->classNameAdms($class), "academic_calender" => $academic_calender]);
        } else {
            session()->flash("strand_error", "SET THE LONG TERM PLAN FIRST! You cannot set the medim term plan without the long term plan being set!");
            return redirect("/Teacher/HOD/Create/Lessonplan/$lesson_id/Class/$class");
        }
    }

    function createMediumPlan($lesson_id, $class)
    {
        // get the subject details
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // if ($this->isHod($lesson_id)) {
        //     return $this->createMediumPlanHOD($lesson_id, $class);
        // }


        // check if the teacher teaches the subject
        $staff_id = session("staff_infor")->user_id;
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ? AND `teachers_id` LIKE '%(" . $staff_id . ":" . $class . ")%'", [$lesson_id]);

        if (count($subject_details) < 1 && !$this->isHod($lesson_id)) {
            session()->flash("invalid", "You do not teach the subject for that particular class!");
            DB::setDefaultConnection("mysql");
            return redirect("/Teacher/LessonPlan");
        }

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;

        // academic calender and weeks
        // return $academic_calender;
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        // STORE DATES HERE
        $dates_details = [];

        // TERM ONE STARTS HERE
        // process term one
        $start_time = $academic_calender[0]->start_time;
        $closing_date = $academic_calender[0]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("D dS M Y", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("D dS M Y", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 1";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM 1

        // TERM TWO STARTS HERE
        // process term one
        $start_time = $academic_calender[1]->start_time;
        $closing_date = $academic_calender[1]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("D dS M Y", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("D dS M Y", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 2";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM TWO

        // TERM THREE STARTS HERE
        // process term one
        $start_time = $academic_calender[2]->start_time;
        $closing_date = $academic_calender[2]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("D dS M Y", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("D dS M Y", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 3";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM THREE
        // return $dates_details;


        // get the lesson plan
        $medium_term_status = 0;
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$lesson_id, $academic_year, $class]);
        if (count($lesson_plan) > 0) {
            $lesson_plans = $lesson_plan;
            $medium_term_status = $lesson_plan[0]->medium_term_status;
            $lesson_plan = $lesson_plan[0]->medium_term_plan == null ? "[]" : $lesson_plan[0]->medium_term_plan;
            // return $lesson_plans;
            $long_term_plan = $this->isJson_report($lesson_plans[0]->longterm_plan_data) ? json_decode($lesson_plans[0]->longterm_plan_data) : [];
            // return $long_term_plan;

            // get the populator objectives and resources from the long term plan to the medium plan
            // get for term one
            $term_one_data = [];
            $strand_data_in_term_one = [];
            $weeks = 1;
            // return $long_term_plan;
            for ($ind = 0; $ind < count($long_term_plan); $ind++) {
                if ($long_term_plan[$ind]->term == "1") {
                    $strand_data = new stdClass();
                    $strand_data->week = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $strand_data->objectives = $long_term_plan[$ind]->objectives;
                    $strand_data->resources = $long_term_plan[$ind]->learning_materials;
                    $strand_data->term = "Term 1";


                    // store the strand data
                    $my_strand_data = new stdClass();
                    $my_strand_data->strand_name = $long_term_plan[$ind]->strand_name;
                    $my_strand_data->strand_code = $long_term_plan[$ind]->strand_code;
                    $my_strand_data->substrands = [];
                    $my_strand_data->weeks = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $my_strand_data->term = "Term 1";

                    // this are weeks to be added on the weeks used
                    $weeks = $weeks + $long_term_plan[$ind]->period;

                    // get the substrands
                    $sub_strands = $long_term_plan[$ind]->sub_strands;
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $objectives = $sub_strands[$indexes]->objectives;
                        for ($in = 0; $in < count($objectives); $in++) {
                            array_push($strand_data->objectives, $objectives[$in]);
                        }
                        $substrands_name = $sub_strands[$indexes]->name;
                        $substrands_code = $sub_strands[$indexes]->code;

                        // store the substrand data
                        $substrand_data = new stdClass();
                        $substrand_data->substrand_name = $substrands_name;
                        $substrand_data->substrand_code = $substrands_code;

                        array_push($my_strand_data->substrands, $substrand_data);
                    }
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $resources = $sub_strands[$indexes]->learning_materials;
                        for ($in = 0; $in < count($resources); $in++) {
                            array_push($strand_data->resources, $resources[$in]);
                        }
                    }

                    array_push($term_one_data, $strand_data);
                    array_push($strand_data_in_term_one, $my_strand_data);
                }
            }
            // return $strand_data_in_term_one;

            // get for term two
            $term_two_data = [];
            $weeks = 1;
            $strand_data_in_term_two = [];
            for ($ind = 0; $ind < count($long_term_plan); $ind++) {
                if ($long_term_plan[$ind]->term == "2") {
                    $strand_data = new stdClass();
                    $strand_data->week = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $strand_data->objectives = $long_term_plan[$ind]->objectives;
                    $strand_data->resources = $long_term_plan[$ind]->learning_materials;
                    $strand_data->term = "Term 2";

                    // store the strand data
                    $my_strand_data = new stdClass();
                    $my_strand_data->strand_name = $long_term_plan[$ind]->strand_name;
                    $my_strand_data->strand_code = $long_term_plan[$ind]->strand_code;
                    $my_strand_data->substrands = [];
                    $my_strand_data->weeks = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $my_strand_data->term = "Term 2";

                    $weeks = $weeks + $long_term_plan[$ind]->period;

                    // get the substrands
                    $sub_strands = $long_term_plan[$ind]->sub_strands;
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $objectives = $sub_strands[$indexes]->objectives;
                        for ($in = 0; $in < count($objectives); $in++) {
                            array_push($strand_data->objectives, $objectives[$in]);
                        }
                        $substrands_name = $sub_strands[$indexes]->name;
                        $substrands_code = $sub_strands[$indexes]->code;

                        // store the substrand data
                        $substrand_data = new stdClass();
                        $substrand_data->substrand_name = $substrands_name;
                        $substrand_data->substrand_code = $substrands_code;

                        array_push($my_strand_data->substrands, $substrand_data);
                    }

                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $resources = $sub_strands[$indexes]->learning_materials;
                        for ($in = 0; $in < count($resources); $in++) {
                            array_push($strand_data->resources, $resources[$in]);
                        }
                    }

                    array_push($term_two_data, $strand_data);
                    array_push($strand_data_in_term_two, $my_strand_data);
                }
            }

            // return $strand_data_in_term_two;

            // get for term three
            $term_three_data = [];
            $strand_data_in_term_three = [];
            $weeks = 1;
            for ($ind = 0; $ind < count($long_term_plan); $ind++) {
                if ($long_term_plan[$ind]->term == "3") {
                    $strand_data = new stdClass();
                    $strand_data->week = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $strand_data->objectives = $long_term_plan[$ind]->objectives;
                    $strand_data->resources = $long_term_plan[$ind]->learning_materials;
                    $strand_data->term = "Term 3";

                    // store the strand data
                    $my_strand_data = new stdClass();
                    $my_strand_data->strand_name = $long_term_plan[$ind]->strand_name;
                    $my_strand_data->strand_code = $long_term_plan[$ind]->strand_code;
                    $my_strand_data->substrands = [];
                    $my_strand_data->weeks = $weeks . "-" . ($weeks + ($long_term_plan[$ind]->period - 1));
                    $my_strand_data->term = "Term 3";

                    $weeks = $weeks + $long_term_plan[$ind]->period;

                    // get the substrands
                    $sub_strands = $long_term_plan[$ind]->sub_strands;
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $objectives = $sub_strands[$indexes]->objectives;
                        for ($in = 0; $in < count($objectives); $in++) {
                            array_push($strand_data->objectives, $objectives[$in]);
                        }
                        $substrands_name = $sub_strands[$indexes]->name;
                        $substrands_code = $sub_strands[$indexes]->code;

                        // store the substrand data
                        $substrand_data = new stdClass();
                        $substrand_data->substrand_name = $substrands_name;
                        $substrand_data->substrand_code = $substrands_code;

                        array_push($my_strand_data->substrands, $substrand_data);
                    }
                    for ($indexes = 0; $indexes < count($sub_strands); $indexes++) {
                        $resources = $sub_strands[$indexes]->learning_materials;
                        for ($in = 0; $in < count($resources); $in++) {
                            array_push($strand_data->resources, $resources[$in]);
                        }
                    }

                    array_push($term_three_data, $strand_data);
                    array_push($strand_data_in_term_three, $my_strand_data);
                }
            }
            // return $strand_data_in_term_three;

            // define the long term plan and its substrands

            // populators
            $populators = array_merge($term_one_data, $term_two_data, $term_three_data);
            $strands_data = array_merge($strand_data_in_term_one, $strand_data_in_term_two, $strand_data_in_term_three);

            // teacher notification
            $teacher_notifications = $this->getTrsNotification();
            return view("create_medium_term_plan", ["teacher_notifications" => $teacher_notifications,"medium_term_status" => $medium_term_status,"long_term_plan" => $long_term_plan, "strands_data" => $strands_data, "dates_details" => $dates_details, "lesson_plan" => $lesson_plan, "populators" => $populators, "lesson_id" => $lesson_id, "class" => $class, "subject_details" => $subject_details[0], "get_class_name" => $this->classNameAdms($class), "academic_calender" => $academic_calender]);
        } else {
            session()->flash("strand_error", "SET THE LONG TERM PLAN FIRST! You cannot set the medim term plan without the long term plan being set!");
            if ($this->isHod($lesson_id)) {
                return redirect("/Teacher/HOD/Create/Lessonplan/$lesson_id/Class/$class");
            }else{
                return redirect("/Teacher/Create/Lessonplan/$lesson_id/Class/$class");
            }
        }
    }
    function CreateWeeklyPlan($lesson_id, $class)
    {
        // get the subject details
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // if ($this->isHod($lesson_id)) {
        //     return $this->CreateHODWeeklyPlan($lesson_id, $class);
        // }


        // check if the teacher teaches the subject
        $staff_id = session("staff_infor")->user_id;
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ? AND `teachers_id` LIKE '%(" . $staff_id . ":" . $class . ")%'", [$lesson_id]);
        
        if (count($subject_details) < 1 && !$this->isHod($lesson_id)) {
            session()->flash("invalid", "You do not teach the subject for ".$this->classNameAdms($class)."!");
            DB::setDefaultConnection("mysql");
            return redirect("/Teacher/LessonPlan");
        }

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;


        // academic calender and weeks
        // return $academic_calender;
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        // STORE DATES HERE
        $dates_details = [];

        // TERM ONE STARTS HERE
        // process term one
        $start_time = $academic_calender[0]->start_time;
        $closing_date = $academic_calender[0]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("Ymd", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 1";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM 1

        // TERM TWO STARTS HERE
        // process term one
        $start_time = $academic_calender[1]->start_time;
        $closing_date = $academic_calender[1]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("Ymd", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 2";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM TWO

        // TERM THREE STARTS HERE
        // process term one
        $start_time = $academic_calender[2]->start_time;
        $closing_date = $academic_calender[2]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("Ymd", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 3";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM THREE
        // return $dates_details;
        // get the lesson plan
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$lesson_id, $academic_year, $class]);
        // return $lesson_plan;
        $short_term_status = 0;

        // GET THE PROGRESS BY HOW FAR THE TEACHER HAS DONE UNTIL TODAY.

        if (count($lesson_plan) > 0) {
            $short_term_status = $lesson_plan[0]->short_term_status;
            // get the medium lesson plan
            $medium_term_plan = $this->isJson_report($lesson_plan[0]->medium_term_plan) ? json_decode($lesson_plan[0]->medium_term_plan) : [];
            $short_term_plan = $this->isJson_report($lesson_plan[0]->short_term_plan) ? json_decode($lesson_plan[0]->short_term_plan) : [];
            $long_term_plan = $this->isJson_report($lesson_plan[0]->longterm_plan_data) ? json_decode($lesson_plan[0]->longterm_plan_data) : [];
            $percentage = $this->shortTermProgress($short_term_plan);

            // notifications
            $teacher_notifications = $this->getTrsNotification();
            return view("create_daily_term_plan", ["teacher_notifications" => $teacher_notifications,"short_term_status" => $short_term_status, "long_term_plan" => $long_term_plan, "lesson_id" => $lesson_id, "dates_details" => $dates_details, "class" => $class, "medium_term_plan" => $medium_term_plan, "subject_details" => $subject_details[0], "get_class_name" => $this->classNameAdms($class), "academic_calender" => $academic_calender, "short_term_plan" => $short_term_plan, "percentage" => $percentage]);
        } else {
            session()->flash("strand_error", "SET THE LONG TERM PLAN FIRST! You cannot set the short term plan without the long term plan being set!");
            if($this->isHod($lesson_id)){
                return redirect("/Teacher/HOD/Create/Lessonplan/$lesson_id/Class/$class");
            }else{
                return redirect("/Teacher/Create/Lessonplan/$lesson_id/Class/$class");
            }
        }
    }
    function CreateHODWeeklyPlan($lesson_id, $class)
    {
        // get the subject details
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // check if the teacher teaches the subject
        // $staff_id = session("staff_infor")->user_id;
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?", [$lesson_id]);

        // if (count($subject_details) < 1) {
        //     session()->flash("invalid", "You do not teach the subject for that particular class!");
        //     DB::setDefaultConnection("mysql");
        //     return redirect("/Teacher/LessonPlan");
        // }

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;


        // academic calender and weeks
        // return $academic_calender;
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        // STORE DATES HERE
        $dates_details = [];

        // TERM ONE STARTS HERE
        // process term one
        $start_time = $academic_calender[0]->start_time;
        $closing_date = $academic_calender[0]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("Ymd", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 1";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM 1

        // TERM TWO STARTS HERE
        // process term one
        $start_time = $academic_calender[1]->start_time;
        $closing_date = $academic_calender[1]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("Ymd", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 2";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM TWO

        // TERM THREE STARTS HERE
        // process term one
        $start_time = $academic_calender[2]->start_time;
        $closing_date = $academic_calender[2]->closing_date;
        $day = date("D", strtotime($start_time));

        // loop through the days to get the number of days gone since the week started
        $days_passed = 0;
        for ($index = 0; $index < count($days); $index++) {
            if ($days[$index] == $day) {
                break;
            }
            $days_passed++;
        }
        // return $days_passed;

        $start_date = $this->addDays($start_time, -$days_passed);
        $weeks = $this->get_weeks_between_dates($start_date, $closing_date);
        // return $weeks;

        for ($index = 0; $index < $weeks; $index++) {
            $dates_detail = new stdClass();
            $dates_detail->date_start = date("Ymd", strtotime($start_date));
            $start_date = $this->addDays($start_date, 6);
            $dates_detail->date_end = date("Ymd", strtotime($start_date));
            $dates_detail->week = "" . ($index + 1);
            $dates_detail->term = "Term 3";
            $start_date = $this->addDays($start_date, 1);

            array_push($dates_details, $dates_detail);
        }
        // END OF TERM THREE
        // return $dates_details;
        // get the lesson plan
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$lesson_id, $academic_year, $class]);
        // return $lesson_plan;
        $short_term_status = 0;

        // GET THE PROGRESS BY HOW FAR THE TEACHER HAS DONE UNTIL TODAY.

        if (count($lesson_plan) > 0) {
            $short_term_status = $lesson_plan[0]->short_term_status;
            // get the medium lesson plan
            $medium_term_plan = $this->isJson_report($lesson_plan[0]->medium_term_plan) ? json_decode($lesson_plan[0]->medium_term_plan) : [];
            $short_term_plan = $this->isJson_report($lesson_plan[0]->short_term_plan) ? json_decode($lesson_plan[0]->short_term_plan) : [];
            $long_term_plan = $this->isJson_report($lesson_plan[0]->longterm_plan_data) ? json_decode($lesson_plan[0]->longterm_plan_data) : [];
            $percentage = $this->shortTermProgress($short_term_plan);

            // get the notifications
            $teacher_notifications = $this->getTrsNotification();
            return view("create_daily_term_hod", ["teacher_notifications" => $teacher_notifications,"lesson_plan_id" => $lesson_plan[0]->id, "short_term_status" => $short_term_status, "long_term_plan" => $long_term_plan, "lesson_id" => $lesson_id, "dates_details" => $dates_details, "class" => $class, "medium_term_plan" => $medium_term_plan, "subject_details" => $subject_details[0], "get_class_name" => $this->classNameAdms($class), "academic_calender" => $academic_calender, "short_term_plan" => $short_term_plan, "percentage" => $percentage]);
        } else {
            session()->flash("strand_error","SET THE LONG TERM PLAN FIRST! You cannot set the short term plan without the long term plan being set!");
            return redirect("/Teacher/HOD/Create/Lessonplan/$lesson_id/Class/$class");
        }
    }

    function shortTermProgress($weekly_plan)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the days that are out of the academic calender this academic year
        // this periods are the periods when school closes and before it opens
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        $days_to_omit = [];
        for ($i = 0; $i < count($academic_calender); $i++) {
            $start_date = $academic_calender[$i]->closing_date;
            if ($i < 2) {
                $end_time = $academic_calender[$i + 1]->start_time;
            } else {
                $end_time = $academic_calender[$i]->end_time;
            }

            // go through a loop of the days and add them to the days to omit
            while (true) {
                if ($start_date == $end_time) {
                    break;
                }
                array_push($days_to_omit, $start_date);
                $start_date = $this->addDay($start_date, 1);
            }
        }

        // go through the academic calender from start to today to know how many days are active
        $start_academic = $academic_calender[0]->start_time;
        $today = date("Y-m-d");

        // count the number of days to that date
        $counter = 0;
        $days_passed = [];
        while (true) {
            if ($start_academic == $today) {
                break;
            }
            if (!in_array($start_academic, $days_to_omit)) {
                $counter++;
                array_push($days_passed, $start_academic);
            }
            $start_academic = $this->addDay($start_academic, 1);
        }

        // go through the short term plan to get the number of lessons the user has completed!
        $counter_completed = 0;
        for ($index = 0; $index < count($weekly_plan); $index++) {
            $date = $weekly_plan[$index]->date;
            $completed = $weekly_plan[$index]->completed;
            if (in_array($date, $days_passed)) {
                // if the date is present in this list check if its completed
                if ($completed) {
                    $counter_completed++;
                }
            }
        }

        // get the percentage
        $percentage = round($counter_completed / $counter * 100);
        return $percentage;
    }

    function ManageShortPlan(Request $request)
    {
        $lesson_id = $request->input("lesson_id");
        $class = $request->input("class");
        $short_term_data = $request->input("short_term_data");
        // return $request;
        // update the setting
        // get the subject details
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // check if the teacher teaches the subject
        $staff_id = session("staff_infor")->user_id;
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ? AND `teachers_id` LIKE '%(" . $staff_id . ":" . $class . ")%'", [$lesson_id]);

        if (count($subject_details) < 1) {
            session()->flash("invalid", "You do not teach the subject for that particular class!");
            DB::setDefaultConnection("mysql");
            return redirect("/Teacher/LessonPlan");
        }

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;

        // update the data in the database
        $update = DB::update("UPDATE `lesson_plan` SET `short_term_plan` = ? WHERE `subject_id` = ? AND `class` = ? AND `academic_year` = ?", [$short_term_data, $lesson_id, $class, $academic_year]);

        return redirect("/Teacher/CreatePlan/Weekly/" . $lesson_id . "/class/" . $class . "");
    }

    function deleteStrands($subject_id, $class_selected, $strand_index)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;

        // get the lesson plan
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$subject_id, $academic_year, $class_selected]);
        if (count($lesson_plan) > 0) {
            $strands_details = $lesson_plan[0];
            // loop through and get the strand to remove
            $strand_name = "Null";
            if ($this->isJson_report($strands_details->longterm_plan_data)) {
                $longterm_plan_data = json_decode($strands_details->longterm_plan_data);
                $new_strand = [];
                for ($index = 0; $index < count($longterm_plan_data); $index++) {
                    $ind = $longterm_plan_data[$index]->index;
                    if ($ind == $strand_index) {
                        $strand_name = $longterm_plan_data[$index]->strand_name;
                        continue;
                    }
                    array_push($new_strand, $longterm_plan_data[$index]);
                }
                // return $new_strand;
                $longterm_plan_data = json_encode($new_strand);
                // update the databases
                $update = DB::update("UPDATE `lesson_plan` SET `longterm_plan_data` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$longterm_plan_data, $subject_id, $academic_year, $class_selected]);
                session()->flash("strand_success", "Strand \"" . $strand_name . "\" permanently deleted successfully!");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_selected . "");
            }
        } else {
            // return to the main page with the error message..
            session()->flash("strand_error", "An error has occured!");
            return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_selected . "");
        }
    }
    // delete substrand
    function deleteSubStrand($subject_id, $class_selected, $sub_strand_index)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;

        // get the lesson plan
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$subject_id, $academic_year, $class_selected]);
        // return $lesson_plan;
        if (count($lesson_plan) > 0) {
            $strands_details = $lesson_plan[0];
            // loop through and get the strand to remove
            $sub_strand_name = "Null";
            if ($this->isJson_report($strands_details->longterm_plan_data)) {
                $longterm_plan_data = json_decode($strands_details->longterm_plan_data);
                for ($index = 0; $index < count($longterm_plan_data); $index++) {
                    $sub_strands = $longterm_plan_data[$index]->sub_strands;
                    $new_strand = [];
                    for ($ind = 0; $ind < count($sub_strands); $ind++) {
                        $indexes = $sub_strands[$ind]->sub_index;
                        if ($indexes == $sub_strand_index) {
                            $sub_strand_name = $longterm_plan_data[$index]->strand_name;
                            continue;
                        }
                        array_push($new_strand, $sub_strands[$ind]);
                    }
                    $longterm_plan_data[$index]->sub_strands = $new_strand;
                }
                // return $longterm_plan_data;
                $longterm_plan_data = json_encode($longterm_plan_data);
                // update the databases
                $update = DB::update("UPDATE `lesson_plan` SET `longterm_plan_data` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$longterm_plan_data, $subject_id, $academic_year, $class_selected]);
                session()->flash("strand_success", "Sub-Strand \"" . $sub_strand_name . "\" permanently deleted successfully!");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_selected . "");
            }
        } else {
            // return to the main page with the error message..
            session()->flash("strand_error", "An error has occured!");
            return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_selected . "");
        }
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

    function studentDash()
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // student attendance calculate
        $student_attendance = $this->getStudentAttendance();
        // return $student_attendance;

        // get the current week number
        // get the current week in the academic calender
        $current_term = $this->getTermV3();

        // get the current week
        $academic_calender = DB::select("SELECT * FROM `academic_calendar` WHERE `term` = ?", [$this->underscoreToSpace_2($current_term)]);

        // get the number of weeks the term has
        $weeks = $this->get_weeks_between_dates($academic_calender[0]->start_time, $academic_calender[0]->end_time);
        $weeks+=1;
        // get the current week number
        $today_week = date("W");
        // get the date when the term started and get its week number from the whole year then delete from the current week number
        $start_term_week = date("W", strtotime($academic_calender[0]->start_time));
        $week_number = $today_week - $start_term_week == 0 ? 1 : $today_week - $start_term_week;
        $week_number+=1;

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }
        $academic_year = $start_date . ":" . $end_date;

        // get the assignments present
        $student_information = session("student_information");
        // return $student_information;

        $stud_class = $student_information->stud_class;
        $stud_admission = $student_information->adm_no;
        // return $stud_class;
        $assignments = DB::select("SELECT * FROM `assignments` WHERE `class` = ? AND `academic_yr` = ? ORDER BY `id` DESC",[$stud_class,$academic_year]);

        // return
        $attempted = 0;
        $total_done = 0;
        for ($index=0; $index < count($assignments); $index++) { 
            $subject_id = $assignments[$index]->subject_id;

            // subject name
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);

            $subject_name = count($subject_details) > 0 ? $subject_details[0]->display_name : "Not Set!";

            // set the subject name
            $assignments[$index]->subject_name = $subject_name;

            // come back and add the status for done or ongoing
            $answers = $assignments[$index]->answers;
            $questions = $assignments[$index]->questions;

            // status
            $assignments[$index]->done_status = false;
            $assignments[$index]->marks_attained = 0;
            $assignments[$index]->total_marks = 0;
            // decode
            if ($this->isJson_report($answers) && $this->isJson_report($questions)) {
                // answers
                $answers = json_decode($answers);
                $questions = json_decode($questions);
                // return $answers;
                
                $tot = 0;
                // loop through to get the total
                for ($ind=0; $ind < count($questions); $ind++) { 
                    $tot += $questions[$ind]->points;
                }
                
                // assign total
                $assignments[$index]->total_marks = $tot;


                // loop through the data to get if they have done the test
                for ($ind=0; $ind < count($answers); $ind++) {
                    if ($answers[$ind]->student_id == $stud_admission) {
                        $attempted++;
                        $assignments[$index]->done_status = true;
                        $assignments[$index]->marks_attained = $answers[$ind]->marks_attained;
                        break;
                    }
                }
            }

            // add the total attemptable assignments
            $total_done++;
        }

        // return $assignments;
        // get the student notifications
        $adm_no = session("student_information")->adm_no;

        // get the teacher`s notification
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_id` = '".$adm_no."' AND `owner_type` = 'student' AND `message_edit_status` = 'Published' ORDER BY `id` DESC LIMIT 5");


        // get the parent notification
        $student_notification = $this->getStudentMessage();
        return view("student_dash", ["dash_notification" => $notifications,"student_notification" => $student_notification,"attempted" => $attempted,"total_done" => $total_done,"assignments" => $assignments,"current_term" => $current_term, "student_attendance" => $student_attendance, "week_number" => $week_number, "weeks" => $weeks]);
    }

    function studentAssignments(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the student assignment

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }
        $academic_year = $start_date . ":" . $end_date;

        // get the assignments present
        $student_information = session("student_information");
        // return $student_information;

        $stud_class = $student_information->stud_class;
        // return $stud_class;
        $assignments = DB::select("SELECT * FROM `assignments` WHERE `class` = ? AND `academic_yr` = ? AND `status` = '1' ORDER BY `id` DESC",[$stud_class,$academic_year]);

        // return
        for ($index=0; $index < count($assignments); $index++) { 
            $subject_id = $assignments[$index]->subject_id;

            // subject name
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);

            $subject_name = count($subject_details) > 0 ? $subject_details[0]->display_name : "Not Set!";

            // set the subject name
            $assignments[$index]->subject_name = $subject_name;

            // come back and add the status for done or ongoing
            // get those assignments that are open and those that are closed
        }
        $student_notification = $this->getStudentMessage();
        return view("student_assignment",["student_notification" => $student_notification,"assignments" => $assignments]);
    }

    function getStudentAttendance()
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // from the start of the academic year to today
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the academic calender
        // first get the term
        $current_term = str_replace(" ", "_", strtoupper($this->getTermV3()));
        $student_information = session("student_information");

        // get the academic calender and get the days active for school
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");

        // loop through the days and get the total number of days active to today
        $total_active_days = 0;
        $end_date = date("Y-m-d");
        for ($index = 0; $index < count($academic_calender); $index++) {
            $calender = $academic_calender[$index];
            $term = $calender->term;
            $start_time = $calender->start_time;
            $end_time = $calender->end_time;
            $closing_date = $calender->closing_date;

            if ($calender->term == $current_term) {
                // check if we are at the current term and if the date we are in is not past the closing date
                $today = date("YmdHis");
                $end_time = date("YmdHis", strtotime($end_time));

                if ($today >= $end_time) {
                    $days_diff = $this->getWeekdayDifference($start_time, $end_time);
                    $total_active_days += $days_diff;
                    $end_date = date("Y-m-d", strtotime($end_time));
                } else {
                    $days_diff = $this->getWeekdayDifference($start_time, $today);
                    $total_active_days += $days_diff;
                }

                // to end the date count
                break;
            }

            // else nomarly we get the date difference
            $days_diff = $this->getWeekdayDifference($start_time, $end_time);
            $total_active_days += $days_diff;
        }
        // after getting the total active days we get the total number of days students have been in school
        // the date should be between the first term date and the term that is second term
        // return $end_date;
        $date_count = DB::select("SELECT COUNT(*) AS 'Total' FROM `attendancetable` WHERE `admission_no` = ? AND `date` BETWEEN ? AND ? ", [$student_information->adm_no, $academic_calender[0]->start_time, $end_date]);

        // the total days the student has been present
        $total_days = $date_count[0]->Total;

        // percentage 
        $percentage = round(($total_days / $total_active_days) * 100);
        return $percentage;
    }


    function getWeekdayDifference($date1, $date2)
    {
        $interval = date_diff(new DateTime($date1), new DateTime($date2));
        $days = $interval->days;
        $weekdays = 0;
        $current = strtotime($date1);
        $end = strtotime($date2);

        while ($current <= $end) {
            if (date('N', $current) < 6) {
                $weekdays++;
            }
            $current = strtotime('+1 day', $current);
        }

        return $weekdays;
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

    function teacherDash()
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the subjects taught by the teacher
        $subjects_taught = $this->getSubjectsTaught();


        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");
        

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;

        // get the assignments set for the subjects they are teaching
        $active_assignments = 0;
        $today = date("Ymd");
        $assignment_data = [];
        for ($index=0; $index < count($subjects_taught); $index++) { 
            $subject_id = $subjects_taught[$index]->subject_id;

            // get the subject assignments that are active
            $assignments_done = DB::select("SELECT * FROM `assignments` WHERE `academic_yr` = ? AND `subject_id` = ? ORDER BY `id` DESC",[$academic_year,$subject_id]);

            for ($ind=0; $ind < count($assignments_done); $ind++) { 
                $period = $assignments_done[$ind]->period;
                if ($this->isJson_report($period)) {
                    $period = json_decode($period);
                    $start_date = date("Ymd",strtotime($period->start_date));
                    $end_date = date("Ymd",strtotime($period->end_date));
                    if ($today >= $start_date && $today <= $end_date) {
                        $active_assignments++;
                    }
                }

                // limit to 5 last assignments
                $subject_id = $assignments_done[$ind]->subject_id;
                $sub_display_name = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);
                $display_name = count($sub_display_name) > 0 ? $sub_display_name[0]->display_name : "Not Available";
                $assignments_done[$ind]->sub_display = $display_name;

                // add the record to the table.
                $class_name = $this->classNameAdms($assignments_done[$ind]->class);
                $assignments_done[$ind]->class_name = $class_name;

                // push array
                // return $this->teachesSubject($assignments_done[$ind]->class,$subject_id);
                if($this->teachesSubject($assignments_done[$ind]->class,$subject_id)){
                    array_push($assignment_data,$assignments_done[$ind]);
                }
            }
        }

        // get exams that are active
        $today = date("Y-m-d");
        $exams_counts = DB::select("SELECT COUNT(*) AS 'Total' FROM `exams_tbl` WHERE `end_date` > ?", [$today]);
        $exam_count = count($exams_counts) > 0 ? $exams_counts[0]->Total : 0;

        // get the current week in the academic calender
        $current_term = $this->getTermV3();

        // get the current week
        $academic_calender = DB::select("SELECT * FROM `academic_calendar` WHERE `term` = ?", [$this->underscoreToSpace_2($current_term)]);

        // get the number of weeks the term has
        $weeks = $this->get_weeks_between_dates($academic_calender[0]->start_time, $academic_calender[0]->end_time);
        $weeks+=1;
        // get the current week number
        $today_week = date("W");
        // get the date when the term started and get its week number from the whole year then delete from the current week number
        $start_term_week = date("W", strtotime($academic_calender[0]->start_time));
        $week_number = $today_week - $start_term_week == 0 ? 1 : $today_week - $start_term_week;
        $week_number+=1;
        
        // return $notifications;
        $teacher_notifications = $this->getTrsNotification();


        // get the teacher`s notification
        $teacher_id = session("staff_infor")->user_id;
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_id` = '".$teacher_id."' AND `owner_type` = 'teacher' AND `message_edit_status` = 'Published' ORDER BY `id` DESC LIMIT 5");

        return view("teacher_dash", ["dash_notification" => $notifications,"teacher_notifications" => $teacher_notifications,"assignment_data" => $assignment_data,"active_assignments" => $active_assignments,"subject_taught" => count($subjects_taught), "exam_count" => $exam_count, "week_number" => $week_number, "total_weeks" => $weeks, "current_term" => $current_term]);
    }

    function teachesSubject($class,$subject){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the teachers id from the session teacher information
        $teacher_id = session("staff_infor")->user_id;
        
        // get the teachers subjects and classes he teaches
        $tables_subject = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = '".$subject."' AND `teachers_id` LIKE '%(".$teacher_id.":".$class.")%'");
        // return "SELECT * FROM `table_subject` WHERE `subject_id` = '".$subject."' AND `teachers_id` LIKE '(".$teacher_id.":".$class.")'";
        // return the boolean value
        return count($tables_subject) > 0 ? true : false;
    }
    function getTermV3()
    {
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        $date = date("Y-m-d");
        $select = DB::select("SELECT `term` FROM `academic_calendar` WHERE `end_time` >= ? AND `start_time` <= ?", [$date, $date]);
        return count($select) > 0 ? $this->underscoreToSpace($select[0]->term) : $this->underscoreToSpace("TERM_1");
    }

    function underscoreToSpace($str)
    {
        $str = str_replace('_', ' ', $str); // replace underscore with space
        $str = ucwords(strtolower($str)); // capitalize the first letter of each word
        return $str;
    }
    function underscoreToSpace_2($str)
    {
        $str = str_replace(' ', '_', $str); // replace underscore with space
        $str = strtoupper($str); // capitalize the whole word
        return $str;
    }
    //handle all lesson plan requests 
    function createLessonPlan(Request $req)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the teachers subjects and classes he teaches
        $tables_subject = DB::select("SELECT * FROM `table_subject`");

        // return to default database
        DB::setDefaultConnection("mysql");

        // get the teachers id from the session teacher information
        $teacher_id = session("staff_infor")->user_id;

        $subjects_taught = [];
        for ($index = 0; $index < count($tables_subject); $index++) {
            $teachers_id = $tables_subject[$index]->teachers_id;
            // explode to show the teacher and subject
            $tr_n_subject = explode("|", $teachers_id);
            // return $tr_n_subject;

            $classes_taught = [];
            $real_class_names = [];
            // split the classes and the teacher
            for ($index_1 = 0; $index_1 < count($tr_n_subject); $index_1++) {
                $tr_subject = substr($tr_n_subject[$index_1], 1, (strlen($tr_n_subject[$index_1]) - 2));
                // return $tr_subject;
                $tr_id = explode(":", $tr_subject);
                if ($tr_id[0] == $teacher_id) {
                    $class_name = $this->classNameAdms($tr_id[1]);
                    if (!$this->checkPresnt($classes_taught, $class_name)) {
                        array_push($classes_taught, $class_name);
                    }
                }

                if ($tr_id[0] == $teacher_id) {
                    $class_name = $tr_id[1];
                    if (!$this->checkPresnt($real_class_names, $class_name)) {
                        array_push($real_class_names, $class_name);
                    }
                }
            }

            if (count($classes_taught) > 0) {
                $tables_subject[$index]->class_taught = $classes_taught;
                // array_push($subjects_taught,$tables_subject[$index]);

                $tables_subject[$index]->real_class_names = $real_class_names;
                array_push($subjects_taught, $tables_subject[$index]);
            }
        }
        // return $subjects_taught;
        DB::setDefaultConnection("mysql2");
        // get the staff information

        // chec to see if the teacher is an HOD for a particular class
        $hod_settings = DB::select("SELECT * FROM `settings` WHERE `sett` = 'departments'");
        
        // get the department data
        $valued = count($hod_settings) > 0 ? $hod_settings[0]->valued : "[]";

        // decode the json data
        $department_data = $this->isJson_report($valued) ? json_decode($valued) : [];
        $user_id = session("staff_infor")->user_id;
        // return $department_data;
        
        $is_hod = [];
        for ($index=0; $index < count($department_data); $index++) {
            if ($department_data[$index]->hod == $user_id) {
                array_push($is_hod,$department_data[$index]);
            }
        }
        // return $subjects_taught;
        $teacher_notifications = $this->getTrsNotification();
        return view("tr_lesson_plan", ["teacher_notifications" => $teacher_notifications,"is_hod" => $is_hod,"department_data" => $department_data,"subjects_taught" => $subjects_taught]);
    }

    function isHod($subject_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // chec to see if the teacher is an HOD for a particular class
        $hod_settings = DB::select("SELECT * FROM `settings` WHERE `sett` = 'departments'");
        
        // get the department data
        $valued = count($hod_settings) > 0 ? $hod_settings[0]->valued : "[]";

        // decode the json data
        $department_data = $this->isJson_report($valued) ? json_decode($valued) : [];
        $user_id = session("staff_infor")->user_id;
        // return $department_data;
        
        $is_hod = [];
        for ($index=0; $index < count($department_data); $index++) {
            if ($department_data[$index]->hod == $user_id) {
                array_push($is_hod,$department_data[$index]);
            }
        }

        // proceed and check if the subject is present
        $is_present = false;
        if (count($is_hod) > 0) {
            for ($ind=0; $ind < count($is_hod); $ind++) { 
                $subjects = $is_hod[$ind]->subjects;
                for ($index=0; $index < count($subjects); $index++) { 
                    if($subjects[$index]->name == $subject_id){
                        $is_present = true;
                        break;
                    }
                }
            }
        }
        return $is_present;
    }

    function hodLessonPlan(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the subject that the HOD can manage
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the hod department
        $user_id = session("staff_infor")->user_id;

        // get the setting data
        $department_data = DB::select("SELECT * FROM `settings` WHERE `sett` = 'departments'");
        // return $department_data;
        $subjects_to_monitor = [];
        if (count($department_data) > 0) {
            $dept_data = $department_data[0]->valued;
            if ($this->isJson_report($dept_data)) {
                $dept_data = json_decode($dept_data);
                for ($index=0; $index < count($dept_data); $index++) { 
                    $hod = $dept_data[$index]->hod;
                    if ($hod == $user_id) {
                        for ($ind=0; $ind < count($dept_data[$index]->subjects); $ind++) {
                            $elem = $dept_data[$index]->subjects[$ind];
                            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$elem->name]);
                            $sub_dets = count($subject_details) > 0 ? $subject_details[0] : $elem->name;
                            array_push($subjects_to_monitor,$sub_dets);
                        }
                    }
                }
            }
        }

        // proceed from there
        for ($index=0; $index < count($subjects_to_monitor); $index++) { 
            $subjects_to_monitor[$index]->classes_taught = explode(",",$subjects_to_monitor[$index]->classes_taught);
        }
        // return $subjects_to_monitor;

        // get these subjects details

        // teachers notification
        $teacher_notifications = $this->getTrsNotification();
        return view("hod_lesson_plan_dash",["teacher_notifications" => $teacher_notifications,"subjects_to_monitor" => $subjects_to_monitor]);
    }

    function getSubjectsTaught()
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the teachers subjects and classes he teaches
        $tables_subject = DB::select("SELECT * FROM `table_subject`");

        // return to default database
        DB::setDefaultConnection("mysql");

        // get the teachers id from the session teacher information
        $teacher_id = session("staff_infor")->user_id;

        $subjects_taught = [];
        for ($index = 0; $index < count($tables_subject); $index++) {
            $teachers_id = $tables_subject[$index]->teachers_id;
            // explode to show the teacher and subject
            $tr_n_subject = explode("|", $teachers_id);
            // return $tr_n_subject;

            $classes_taught = [];
            $real_class_names = [];
            // split the classes and the teacher
            for ($index_1 = 0; $index_1 < count($tr_n_subject); $index_1++) {
                $tr_subject = substr($tr_n_subject[$index_1], 1, (strlen($tr_n_subject[$index_1]) - 2));
                // return $tr_subject;
                $tr_id = explode(":", $tr_subject);
                if ($tr_id[0] == $teacher_id) {
                    $class_name = $this->classNameAdms($tr_id[1]);
                    if (!$this->checkPresnt($classes_taught, $class_name)) {
                        array_push($classes_taught, $class_name);
                    }
                }

                if ($tr_id[0] == $teacher_id) {
                    $class_name = $tr_id[1];
                    if (!$this->checkPresnt($real_class_names, $class_name)) {
                        array_push($real_class_names, $class_name);
                    }
                }
            }

            if (count($classes_taught) > 0) {
                $tables_subject[$index]->class_taught = $classes_taught;
                // array_push($subjects_taught,$tables_subject[$index]);

                $tables_subject[$index]->real_class_names = $real_class_names;
                array_push($subjects_taught, $tables_subject[$index]);
            }
        }
        return $subjects_taught;
    }

    function editLessonPlan($lesson_id, $class)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the subject details
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?", [$lesson_id]);
        if (count($subject_details) > 0) {
            $staff_id = session("staff_infor")->user_id;
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ? AND `teachers_id` LIKE '%(" . $staff_id . ":" . $class . ")%'", [$lesson_id]);

            if (count($subject_details) < 1) {
                session()->flash("invalid", "You do not teach the subject for that particular class!");
                DB::setDefaultConnection("mysql");
                return redirect("/Teacher/LessonPlan");
            }

            $teacher_notifications = $this->getTrsNotification();
            return view("edit_lesson_plans", ["teacher_notifications" => $teacher_notifications,"subject_details" => $subject_details[0], "class" => $class, "lesson_id" => $lesson_id, "get_class_name" => $this->classNameAdms($class)]);
        } else {
            session()->flash("invalid", "Invalid Subject, select subjects from the table below to proceed");
            return redirect("/Teacher/LessonPlan");
        }
    }

    function editHODLessonPlan($lesson_id, $class){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the subject details
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?", [$lesson_id]);
        if (count($subject_details) > 0) {
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?", [$lesson_id]);
            $staff_name = "Not Assigned";
            if (count($subject_details) > 0) {
                $teachers_id = $subject_details[0]->teachers_id;
                // explode
                $teacher_data = explode("|",$teachers_id);
                for ($indx=0; $indx < count($teacher_data); $indx++) { 
                    $needle = $class.")";
                    if (strpos($teacher_data[$indx],$needle) == true) {
                        DB::setDefaultConnection("mysql");
                        $teacher_details = substr($teacher_data[$indx],1,strlen($teacher_data[$indx])-1);
                        $staff_id = explode(":",$teacher_details)[0];

                        // get the staff detail
                        $staff_information = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$staff_id]);
                        $staff_name = count($staff_information) > 0 ? ucwords(strtolower($staff_information[0]->fullname)) : "Not Assigned";
                    }
                }
            }
            // DB::setDefaultConnection("mysql");
            $teacher_notifications = $this->getTrsNotification();
            return view("edit_lesson_plan_hod", ["teacher_notifications" => $teacher_notifications,"staff_name" => $staff_name,"subject_details" => $subject_details[0], "class" => $class, "lesson_id" => $lesson_id, "get_class_name" => $this->classNameAdms($class)]);
        } else {
            session()->flash("invalid", "Invalid Subject, select subjects from the table below to proceed");
            return redirect("/Teacher/LessonPlan");
        }
    }

    function isJson_report($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
    function createLongTermPlan($lesson_id, $class)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }

        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        // if ($this->isHod($lesson_id)) {
        //     return $this->createLongTermPlanHOD($lesson_id, $class);
        // }

        $staff_id = session("staff_infor")->user_id;
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ? AND `teachers_id` LIKE '%(" . $staff_id . ":" . $class . ")%'", [$lesson_id]);

        if (count($subject_details) < 1 && !$this->isHod($lesson_id)) {
            session()->flash("invalid", "You do not teach the subject for that particular class!");
            DB::setDefaultConnection("mysql");
            return redirect("/Teacher/LessonPlan");
        }

        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?", [$lesson_id]);
        if (count($subject_details) > 0) {
            // get the academic calendar
            $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
            if (count($academic_calender) > 0) {
                $start_date = date("Y", strtotime($academic_calender[0]->start_time));
                $end_date = date("Y", strtotime($academic_calender[2]->end_time));
            } else {
                $start_date = date("Y");
                $end_date = date("Y");
            }

            $academic_year = $start_date . ":" . $end_date;

            // get the stored lesson plan lists
            $select = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$lesson_id, $academic_year, $class]);
            $long_lesson_plan = [];
            $long_term_status = 0;
            if (count($select) > 0) {
                if ($this->isJson_report($select[0]->longterm_plan_data)) {
                    $long_lesson_plan = json_decode($select[0]->longterm_plan_data);
                    $long_term_status = $select[0]->long_term_status;
                }
            }
            // return $long_term_status;
            $teacher_notifications = $this->getTrsNotification();
            return view("create_long_term_plan", ["teacher_notifications" => $teacher_notifications,"long_term_status" => $long_term_status,"lesson_id" => $lesson_id, "class" => $class, "subject_details" => $subject_details[0], "get_class_name" => $this->classNameAdms($class), "long_lesson_plan" => $long_lesson_plan, "academic_calender" => $academic_calender]);
        } else {
            session()->flash("invalid", "Invalid Subject, select subjects from the table below to proceed");
            return redirect("/Teacher/LessonPlan");
        }
    }

    function createLongTermPlanHOD($lesson_id, $class){
        if (session("school_information") == null) {
            return redirect("/");
        }

        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // CHECK IF THE USER IS THE TEACHER
        // $staff_id = session("staff_infor")->user_id;
        // $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ? AND `teachers_id` LIKE '%(" . $staff_id . ":" . $class . ")%'", [$lesson_id]);

        // if (count($subject_details) < 1) {
        //     session()->flash("invalid", "You do not teach the subject for that particular class!");
        //     DB::setDefaultConnection("mysql");
        //     return redirect("/Teacher/LessonPlan");
        // }

        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?", [$lesson_id]);
        if (count($subject_details) > 0) {
            // get the academic calendar
            $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
            if (count($academic_calender) > 0) {
                $start_date = date("Y", strtotime($academic_calender[0]->start_time));
                $end_date = date("Y", strtotime($academic_calender[2]->end_time));
            } else {
                $start_date = date("Y");
                $end_date = date("Y");
            }

            $academic_year = $start_date . ":" . $end_date;

            // get the stored lesson plan lists
            $select = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$lesson_id, $academic_year, $class]);
            $long_lesson_plan = [];
            $long_term_status = 0;
            $lesson_plan_id = 0;
            if (count($select) > 0) {
                if ($this->isJson_report($select[0]->longterm_plan_data)) {
                    $long_lesson_plan = json_decode($select[0]->longterm_plan_data);
                    $long_term_status = $select[0]->long_term_status;
                    $lesson_plan_id = $select[0]->id;
                }
            }
            // return $long_term_status;
            $teacher_notifications = $this->getTrsNotification();
            return view("create_long_term_hod", ["teacher_notifications" => $teacher_notifications,"lesson_plan_id" => $lesson_plan_id, "long_term_status" => $long_term_status,"lesson_id" => $lesson_id, "class" => $class, "subject_details" => $subject_details[0], "get_class_name" => $this->classNameAdms($class), "long_lesson_plan" => $long_lesson_plan, "academic_calender" => $academic_calender]);
        } else {
            session()->flash("invalid", "Invalid Subject, select subjects from the table below to proceed");
            return redirect("/Teacher/LessonPlan");
        }
    }

    function changeStatusPlan($id,$lesson_id,$class){
        if (session("school_information") == null) {
            return redirect("/");
        }

        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // select status
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `id` = ?",[$id]);
        
        if (count($lesson_plan) > 0) {
            $lesson_plan_id = $lesson_plan[0]->long_term_status == 0 ? 1 : 0;
            $update = DB::update("UPDATE `lesson_plan` SET `long_term_status` = '".$lesson_plan_id."' WHERE `id` = ?",[$id]);
            session()->flash("strand_success","Status changed successfully!");
            return redirect("/Teacher/HOD/CreatePlan/Long/".$lesson_id."/class/".$class."");
        }else{
            session()->flash("strand_error","Add atleast one Strand/Topic so that you may change the this lesson plan status!");
            return redirect("/Teacher/HOD/CreatePlan/Long/".$lesson_id."/class/".$class."");
        }
    }

    function changeStatusShortPlan($id,$lesson_id,$class){
        if (session("school_information") == null) {
            return redirect("/");
        }

        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // select status
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `id` = ?",[$id]);
        
        if (count($lesson_plan) > 0) {
            $lesson_plan_id = $lesson_plan[0]->short_term_status == 0 ? 1 : 0;
            $update = DB::update("UPDATE `lesson_plan` SET `short_term_status` = '".$lesson_plan_id."' WHERE `id` = ?",[$id]);
            session()->flash("strand_success","Status changed successfully!");
            return redirect("/Teacher/HOD/CreatePlan/Weekly/".$lesson_id."/class/".$class."");
        }else{
            session()->flash("strand_error","Add atleast one Strand/Topic so that you may change the this lesson plan status!");
            return redirect("/Teacher/HOD/CreatePlan/Weekly/".$lesson_id."/class/".$class."");
        }
    }

    function changeStatusMediumPlan($id,$lesson_id,$class){
        if (session("school_information") == null) {
            return redirect("/");
        }

        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // select status
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `id` = ?",[$id]);
        
        if (count($lesson_plan) > 0) {
            $lesson_plan_id = $lesson_plan[0]->medium_term_status == 0 ? 1 : 0;
            $update = DB::update("UPDATE `lesson_plan` SET `medium_term_status` = '".$lesson_plan_id."' WHERE `id` = ?",[$id]);
            session()->flash("strand_success","Status changed successfully!");
            return redirect("/Teacher/HOD/CreatePlan/Medium/".$lesson_id."/class/".$class."");
        }else{
            session()->flash("strand_error","Add atleast one Strand/Topic so that you may change the this lesson plan status!");
            return redirect("/Teacher/HOD/CreatePlan/Medium/".$lesson_id."/class/".$class."");
        }
    }

    function registerStrand(Request $req)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $req;
        $strand_name = $req->input("strand_name");
        $strands_objectives_holder = $req->input("strands_objectives_holder");
        $learning_materials_holder = $req->input("learning_materials_holder");
        $strands_comment = $req->input("strands_comment");
        $period = $req->input("period");
        $subject_id = $req->input("subject_id");
        $class_plan = $req->input("class_plan");
        $term_selected = $req->input("term_selected");
        $strand_code = $req->input("strand_code");

        // get already existing lesson plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");
        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        $academic_year = $start_date . ":" . $end_date;
        // return json_decode($strands_objectives_holder);

        // store the data as json
        $strand_data = new stdClass();
        $strand_data->strand_name = $strand_name;
        $strand_data->period = $period;
        $strand_data->comment = $strands_comment;
        $strand_data->term = $term_selected;
        $strand_data->sub_strands = [];
        $strand_data->strand_code = $strand_code;
        $strand_data->objectives = $this->isJson_report($strands_objectives_holder) ? json_decode($strands_objectives_holder) : [];
        $strand_data->learning_materials = $this->isJson_report($learning_materials_holder) ? json_decode($learning_materials_holder) : [];
        $strand_data->date_created = date("YmdHis");

        // get the data linked to that subject from the database
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$subject_id, $academic_year, $class_plan]);

        if (count($lesson_plan) > 0) {
            $longterm_plan_data = $lesson_plan[0]->longterm_plan_data;
            // get the long tern plan and append the strand data
            if (strlen($longterm_plan_data) > 0) {
                $longterm_plan_data = json_decode($longterm_plan_data);

                // get the largest index
                $largest_no = 0;
                for ($index = 0; $index < count($longterm_plan_data); $index++) {
                    $indexes = $longterm_plan_data[$index]->index * 1;
                    if ($indexes > $largest_no) {
                        $largest_no = $indexes;
                    }
                }

                // add the index id
                $strand_data->index = $largest_no + 1;

                // add the strand data to the existing one
                array_push($longterm_plan_data, $strand_data);
                $new_data = json_encode($longterm_plan_data);

                // update the databases
                $update = DB::update("UPDATE `lesson_plan` SET `longterm_plan_data` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$new_data, $subject_id, $academic_year, $class_plan]);
                session()->flash("strand_success", "Strand \"" . $strand_name . "\" added successfully!");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
            } else {
                $strand_data->index = 0;
                $strand_stored_data = [];
                array_push($strand_stored_data, $strand_data);

                $long_term_data = json_encode($strand_stored_data);
                // insert data
                $update = DB::update("UPDATE `lesson_plan` SET `longterm_plan_data` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$long_term_data, $subject_id, $academic_year, $class_plan]);
                session()->flash("strand_success", "Strand \"" . $strand_name . "\" added successfully!");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
            }
        } else {
            $strand_data->index = 0;
            $strand_stored_data = [];
            array_push($strand_stored_data, $strand_data);

            $long_term_data = json_encode($strand_stored_data);
            // insert data
            $insert_data = DB::insert("INSERT INTO `lesson_plan` (`subject_id`,`academic_year`,`class`,`longterm_plan_data`) VALUES (?,?,?,?)", [$subject_id, $academic_year, $class_plan, $long_term_data]);
            session()->flash("strand_success", "Strand \"" . $strand_name . "\" added successfully!");
            return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
        }
    }

    function updateStrands(Request $req)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $req;
        $strand_name = $req->input("strand_name");
        $strand_code = $req->input("strand_code");
        $strand_index = $req->input("strand_index");
        $term_selected = $req->input("term_selected");
        $edit_strands_objectives_holder = $req->input("edit_strands_objectives_holder");
        $period = $req->input("period");
        $edit_learning_materials_holder = $req->input("edit_learning_materials_holder");
        $comment = $req->input("comment");
        $subject_id = $req->input("subject_id");
        $class_plan = $req->input("class_plan");
        $move_term = $req->input("move_term");

        // return $move_term;
        $strands = $this->isJson_report($move_term) ? json_decode($move_term) : [];
        // return $strands;

        // get already existing lesson plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");


        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }
        $academic_year = $start_date . ":" . $end_date;

        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ? ORDER BY `id` DESC", [$subject_id, $academic_year, $class_plan]);

        // lets get the lesson plan
        if (count($lesson_plan) > 0) {
            // look for the lesson plan with the index
            $my_lesson_plan = $lesson_plan[0]->longterm_plan_data;
            if ($this->isJson_report($my_lesson_plan)) {
                $my_lesson_plan = json_decode($my_lesson_plan);
                for ($index = 0; $index < count($my_lesson_plan); $index++) {
                    if ($my_lesson_plan[$index]->index == $strand_index) {
                        $edit_strands_objectives_holder = $this->isJson_report($edit_strands_objectives_holder) && strlen($edit_strands_objectives_holder) > 0 ? json_decode($edit_strands_objectives_holder) : "";
                        $edit_learning_materials_holder = $this->isJson_report($edit_learning_materials_holder) && strlen($edit_learning_materials_holder) > 0 ? json_decode($edit_learning_materials_holder) : "";
                        $my_lesson_plan[$index]->strand_name = $strand_name;
                        $my_lesson_plan[$index]->term = $term_selected;
                        $my_lesson_plan[$index]->comment = $comment;
                        $my_lesson_plan[$index]->strand_code = $strand_code;
                        $my_lesson_plan[$index]->period = $period;
                        $my_lesson_plan[$index]->objectives = $edit_strands_objectives_holder;
                        $my_lesson_plan[$index]->learning_materials = $edit_learning_materials_holder;
                    }
                }
                // return $my_lesson_plan;
                if (count($strands) > 0) {
                    if ($strands[0] != "default") {
                        if ($strands[0] == "-1") {
                            // this means you are moving it at the beginning
                            // loop through the data and get the strand to add in the beginning
                            $strand_to_move = [];
                            for ($index = 0; $index < count($my_lesson_plan); $index++) {
                                if ($my_lesson_plan[$index]->index == $strands[1]) {
                                    $strand_to_move = $my_lesson_plan[$index];
                                    break;
                                }
                            }
                            // return $strand_to_move;

                            if (!empty($strand_to_move) > 0) {
                                $new_strand_data = [];
                                array_push($new_strand_data, $strand_to_move);
                                for ($index = 0; $index < count($my_lesson_plan); $index++) {
                                    if ($my_lesson_plan[$index]->index == $strands[1]) {
                                        continue;
                                    }
                                    array_push($new_strand_data, $my_lesson_plan[$index]);
                                }
                                $my_lesson_plan = $new_strand_data;
                            }
                        } else {
                            // this means we have passed a specific index code for the strand that we want the strand to appear after
                            // so first get the strand to move
                            $strand_to_move = [];
                            for ($index = 0; $index < count($my_lesson_plan); $index++) {
                                if ($my_lesson_plan[$index]->index == $strands[1]) {
                                    $strand_to_move = $my_lesson_plan[$index];
                                    break;
                                }
                            }

                            // ensure its not blank
                            if (!empty($strand_to_move) > 0) {
                                $new_strand_data = [];
                                for ($index = 0; $index < count($my_lesson_plan); $index++) {
                                    if ($my_lesson_plan[$index]->index == $strands[1]) {
                                        continue;
                                    }
                                    array_push($new_strand_data, $my_lesson_plan[$index]);
                                    // push it after the strand
                                    if ($my_lesson_plan[$index]->index == $strands[0]) {
                                        array_push($new_strand_data, $strand_to_move);
                                    }
                                }
                                $my_lesson_plan = $new_strand_data;
                            }
                        }
                        // return $my_lesson_plan;
                    }
                }
                $my_lesson_plan = json_encode($my_lesson_plan);
                $update = DB::update("UPDATE `lesson_plan` SET `longterm_plan_data` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$my_lesson_plan, $subject_id, $academic_year, $class_plan]);
                session()->flash("strand_success", "Update Successfully done to \"" . $strand_name . "\"");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
            } else {
                session()->flash("strand_error", "An error has occured!");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
            }
        } else {
            session()->flash("strand_error", "An error has occured!");
            return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
        }
    }

    function updateSubStrand(Request $req)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $req;
        $sub_strand_name = $req->input("sub_strand_name");
        $sub_strand_code = $req->input("sub_strand_code");
        $subject_id = $req->input("subject_id");
        $class_plan = $req->input("class_plan");
        $term_selected = $req->input("term_selected");
        $plan_index = $req->input("plan_index");
        $substrand_index = $req->input("substrand_index");
        $sub_strands_objectives_holder = $req->input("sub_strands_objectives_holder");
        $sub_strand_period = $req->input("sub_strand_period");
        $duration_unit = $req->input("duration_unit");
        $sub_strand_learning_materials_holder = $req->input("sub_strand_learning_materials_holder");
        $sub_strands_comment = $req->input("sub_strands_comment");
        $date_created = $req->input("date_created");
        $select_strand = $req->input("select_strand");
        $select_location = $req->input("select_location");
        $substrand_locale_opt = $req->input("substrand_locale_opt");

        // get the academic calender
        // get already existing lesson plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }

        // get the class data
        $academic_year = $start_date . ":" . $end_date;

        // get the class
        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$subject_id, $academic_year, $class_plan]);

        // get the long term lesson plan
        $longterm_plan_data = $lesson_plan[0]->longterm_plan_data;
        if ($this->isJson_report($longterm_plan_data) > 0) {
            $longterm_plan_data = json_decode($longterm_plan_data);
            // return $longterm_plan_data;
            for ($index = 0; $index < count($longterm_plan_data); $index++) {
                if ($longterm_plan_data[$index]->index == $plan_index) {
                    $objectives = $this->isJson_report($sub_strands_objectives_holder) > 0 ? json_decode($sub_strands_objectives_holder) : [];
                    $learning_materials = $this->isJson_report($sub_strand_learning_materials_holder) > 0 ? json_decode($sub_strand_learning_materials_holder) : [];

                    $sub_strand_data = new stdClass();
                    $sub_strand_data->name = $sub_strand_name;
                    $sub_strand_data->code = $sub_strand_code;
                    $sub_strand_data->class = $class_plan;
                    $sub_strand_data->term = $term_selected;
                    $sub_strand_data->subject_id = $subject_id;
                    $sub_strand_data->objectives = $objectives;
                    $sub_strand_data->learning_materials = $learning_materials;
                    $sub_strand_data->comments = $sub_strands_comment == null ? "" : $sub_strands_comment;
                    $sub_strand_data->period = $sub_strand_period . " " . $duration_unit;
                    $sub_strand_data->date_created = $date_created;
                    $sub_strand_data->date_modified = date("YmdHis");
                    $sub_strand_data->sub_index = $substrand_index;

                    $sub_strands = $longterm_plan_data[$index]->sub_strands;
                    $strand_name = $longterm_plan_data[$index]->strand_name;
                    if (is_array($sub_strands)) {
                        for ($ind = 0; $ind < count($sub_strands); $ind++) {
                            if ($sub_strands[$ind]->sub_index == $substrand_index) {
                                $longterm_plan_data[$index]->sub_strands[$ind] = $sub_strand_data;
                                break;
                            }
                        }
                    }
                    // return $select_location." - ".$substrand_index;

                    // change locations for the substrand
                    if ($select_strand != $plan_index || $select_location != $substrand_index) {
                        if ($substrand_locale_opt == "Different Strand") {
                            // this means that the substrand is to be moved to a different strand

                            // remove the substrand from where it is
                            $moved_substrand = null;
                            for ($indx = 0; $indx < count($longterm_plan_data); $indx++) {
                                if ($plan_index == $longterm_plan_data[$indx]->index) {
                                    $substrands = $longterm_plan_data[$indx]->sub_strands;
                                    $new_substrands = [];
                                    for ($in = 0; $in < count($substrands); $in++) {
                                        if ($substrands[$in]->sub_index == $substrand_index) {
                                            $moved_substrand = $substrands[$in];
                                            continue;
                                        }
                                        array_push($new_substrands, $substrands[$in]);
                                    }
                                    $longterm_plan_data[$indx]->sub_strands = $new_substrands;
                                }
                            }

                            // move it to the new location
                            for ($indx = 0; $indx < count($longterm_plan_data); $indx++) {
                                if ($select_strand == $longterm_plan_data[$indx]->index) {
                                    if ($moved_substrand != null) {
                                        array_push($longterm_plan_data[$indx]->sub_strands, $moved_substrand);
                                    }
                                }
                            }
                        } elseif ($substrand_locale_opt == "In Strand") {
                            // this means from the existing strands move it around the strands
                            // remove the substrand from where it is
                            $moved_substrand = null;
                            for ($indx = 0; $indx < count($longterm_plan_data); $indx++) {
                                if ($plan_index == $longterm_plan_data[$indx]->index) {
                                    $substrands = $longterm_plan_data[$indx]->sub_strands;
                                    $new_substrands = [];
                                    for ($in = 0; $in < count($substrands); $in++) {
                                        if ($substrands[$in]->sub_index == $substrand_index) {
                                            $moved_substrand = $substrands[$in];
                                            continue;
                                        }
                                        array_push($new_substrands, $substrands[$in]);
                                    }
                                    $longterm_plan_data[$indx]->sub_strands = $new_substrands;
                                    break;
                                }
                            }
                            // return $moved_substrand;


                            // move it to the new location
                            for ($indx = 0; $indx < count($longterm_plan_data); $indx++) {
                                if ($plan_index == $longterm_plan_data[$indx]->index) {
                                    $sub_strand = $longterm_plan_data[$indx]->sub_strands;
                                    $new_substrands = [];
                                    if ($select_location == "-1") {
                                        if ($moved_substrand != null) {
                                            array_push($new_substrands, $moved_substrand);
                                        }
                                    }

                                    for ($ind = 0; $ind < count($sub_strand); $ind++) {
                                        array_push($new_substrands, $sub_strand[$ind]);
                                        if ($select_location == $sub_strand[$ind]->sub_index) {
                                            if ($moved_substrand != null) {
                                                array_push($new_substrands, $moved_substrand);
                                            }
                                        }
                                    }
                                    $longterm_plan_data[$indx]->sub_strands = $new_substrands;
                                    break;
                                }
                            }
                        }
                    }
                    // return $longterm_plan_data;
                    // update the data and return to the main page
                    $longterm_plan_data = json_encode($longterm_plan_data);
                    $update = DB::update("UPDATE `lesson_plan` SET `longterm_plan_data` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class`= ?", [$longterm_plan_data, $subject_id, $academic_year, $class_plan]);
                    session()->flash("strand_success", "Update Successfully done to strand \"" . $strand_name . "\" on substrand \"" . $sub_strand_name . "\"");
                    return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
                }
            }
        } else {
            session()->flash("strand_error", "An error has occured!");
            return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
        }
    }

    function registerSubStrands(Request $req)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $req;
        // get the data
        $sub_strand_name = $req->input("sub_strand_name");
        $sub_strand_code = $req->input("sub_strand_code");
        $subject_id = $req->input("subject_id");
        $class_plan = $req->input("class_plan");
        $term_selected = $req->input("term_selected");
        $plan_index = $req->input("plan_index");
        $sub_strands_objectives_holder = $req->input("sub_strands_objectives_holder");
        $sub_strand_period = $req->input("sub_strand_period");
        $duration_unit = $req->input("duration_unit");
        $sub_strand_learning_materials_holder = $req->input("sub_strand_learning_materials_holder");
        $sub_strands_comment = $req->input("sub_strands_comment");

        // get already existing lesson plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        $objectives = $this->isJson_report($sub_strands_objectives_holder) ? json_decode($sub_strands_objectives_holder) : [];
        $learning_materials = $this->isJson_report($sub_strand_learning_materials_holder) ? json_decode($sub_strand_learning_materials_holder) : [];
        // create the data
        $sub_strand_data = new stdClass();
        $sub_strand_data->name = $sub_strand_name;
        $sub_strand_data->code = $sub_strand_code;
        $sub_strand_data->class = $class_plan;
        $sub_strand_data->term = $term_selected;
        $sub_strand_data->subject_id = $subject_id;
        $sub_strand_data->objectives = $objectives;
        $sub_strand_data->learning_materials = $learning_materials;
        $sub_strand_data->comments = $sub_strands_comment;
        $sub_strand_data->period = $sub_strand_period . " " . $duration_unit;
        $sub_strand_data->date_created = date("YmdHis");

        // return $sub_strand_data;

        // get the academic calendar
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`");
        if (count($academic_calender) > 0) {
            $start_date = date("Y", strtotime($academic_calender[0]->start_time));
            $end_date = date("Y", strtotime($academic_calender[2]->end_time));
        } else {
            $start_date = date("Y");
            $end_date = date("Y");
        }
        $academic_year = $start_date . ":" . $end_date;

        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `class` = ? AND `academic_year` = ? ORDER BY `id` DESC LIMIT 1", [$subject_id, $class_plan, $academic_year]);

        if (count($lesson_plan) > 0) {
            // check if the lesson plan is set
            $longterm_plan_data = $lesson_plan[0]->longterm_plan_data;
            if ($this->isJson_report($longterm_plan_data)) {
                $longterm_plan_data = json_decode($longterm_plan_data);
                for ($index = 0; $index < count($longterm_plan_data); $index++) {
                    if ($longterm_plan_data[$index]->index == $plan_index) {
                        $sub_strands = $longterm_plan_data[$index]->sub_strands;
                        $strand_name = $longterm_plan_data[$index]->strand_name;
                        // return $longterm_plan_data[$index];
                        $large_index = 0;
                        $counter = 0;
                        for ($indx = 0; $indx < count($longterm_plan_data); $indx++) {
                            $sub_str = $longterm_plan_data[$indx]->sub_strands;
                            if (count($sub_str) > 0) {
                                for ($indexes = 0; $indexes < count($sub_str); $indexes++) {
                                    $sub_index = $sub_str[$indexes]->sub_index;
                                    if ($sub_index > $large_index) {
                                        $large_index = $sub_index;
                                    }
                                    $counter++;
                                }
                            }
                        }
                        $counter > 0 ? $large_index += 1 : $large_index;

                        // proceed and set the subindex
                        $sub_strand_data->sub_index = $large_index;

                        // add the substrand to the strand list
                        array_push($longterm_plan_data[$index]->sub_strands, $sub_strand_data);
                        // return $longterm_plan_data[$index];
                        $longterm_plan_data = json_encode($longterm_plan_data);
                        $update = DB::update("UPDATE `lesson_plan` SET `longterm_plan_data` = ? WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?", [$longterm_plan_data, $subject_id, $academic_year, $class_plan]);
                        session()->flash("strand_success", "Sub-Strand \"" . $sub_strand_name . "\" added under \"" . $strand_name . "\" successfully!");
                        return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
                    }
                }
                session()->flash("strand_error", "An error occured!");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
            } else {
                session()->flash("strand_error", "You have no plan set yet!");
                return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
            }
        } else {
            session()->flash("strand_error", "You have no plan set yet!");
            return redirect("/Teacher/CreatePlan/Long/" . $subject_id . "/class/" . $class_plan . "");
        }
    }

    function uploadNotesFiles(Request $request)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        $subject_id = $request->input("subject_id");
        $class_selected = $request->input("class_selected");
        $file = $request->file('file');
        $original_name = $file->getClientOriginalName();
        $fileName = date("YmdHis") . "_" . $subject_id . "_" . $class_selected . "_" . str_replace("_", "-", str_replace(" ", "-", $file->getClientOriginalName()));

        // tell if the path is present
        if (!File::exists(public_path("/Notes"))) {
            // create if it does mot exist
            File::makeDirectory(public_path("/Notes"), $mode = 0777, $recursive = true);
        }

        // check if the database name directory of the school is present
        if (!File::exists(public_path("/Notes/" . session("school_information")->database_name . ""))) {
            // create if it does mot exist
            File::makeDirectory(public_path("/Notes/" . session("school_information")->database_name . ""), $mode = 0777, $recursive = true);
        }

        // call the file by the subject id and file name

        $file->move(public_path('Notes/' . session("school_information")->database_name), $fileName);
        $public_path = "/Notes/" . session("school_information")->database_name;

        return [$original_name, $fileName, public_path('Notes/' . session("school_information")->database_name), $public_path];
    }

    function DeleteFiles(Request $req)
    {
        $fileName = $req->input("file_path");
        // return $req;
        $file_path = public_path($fileName); // Replace with the actual path to the file

        if (File::exists($file_path)) {
            File::delete($file_path);
        }

        return "File deleted successfully!";
    }

    public function auth()
    {
        // Retrieve the client secret and client ID from the database
        $clientSecret = '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';
        $clientId = '103077607700-c53tduiob0gttrquv84dcvm3ivj9a8fr.apps.googleusercontent.com';

        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/auth/callback');
        $client->addScope(Google_Service_YouTube::YOUTUBE_UPLOAD);
        $auth_url = $client->createAuthUrl();
        return redirect($auth_url);
    }

    public function refreshAccessToken()
    {
        // Get the access token and refresh token from the session
        $accessToken = session('access_token');
        $refreshToken = session('refresh_token');

        // Create a new Google client and set the access token and refresh token
        $client = new Google_Client();
        $client->setAccessToken($accessToken);
        $client->refreshToken($refreshToken);

        // Refresh the access token if it has expired
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            session(['access_token' => $client->getAccessToken()]);
        }
    }

    // public function handleProviderCallback(Request $request)
    // {
    //     // Create a new Google client and set the client ID and client secret
    //     $client = new Google_Client();
    //     $client->setClientId('103077607700-c53tduiob0gttrquv84dcvm3ivj9a8fr.apps.googleusercontent.com');
    //     $client->setClientSecret('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');
    //     $client->setRedirectUri('https://lpts.ladybirdsmis.com/auth/callback');
    //     $client->setAccessType('offline');

    //     // Authenticate the user and get their access token
    //     $code = $request->input('code');
    //     $accessToken = $client->fetchAccessTokenWithAuthCode($code);

    //     // Save the access token as a session variable
    //     session([
    //         'access_token' => $accessToken,
    //         'refresh_token' => $client->getRefreshToken()
    //     ]);

    //     // Send a message to the main window indicating that the user has been authenticated
    //     return '<script>window.opener.postMessage("authenticated", "' . url('/') . '");window.close();</script>';
    // }

    public function handleProviderCallback(Request $request)
    {
        // Create a new Google client and set the client ID and client secret
        $client = new Google_Client();
        $client->setClientId('103077607700-c53tduiob0gttrquv84dcvm3ivj9a8fr.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri('https://lpts.ladybirdsmis.com/auth/callback');
        $client->setAccessType('offline');

        // Request the necessary scopes
        $client->addScope(Google_Service_YouTube::YOUTUBE_FORCE_SSL);
        $client->addScope(Google_Service_YouTube::YOUTUBE);

        // Authenticate the user and get their access token
        $code = $request->input('code');
        $accessToken = $client->fetchAccessTokenWithAuthCode($code);

        // Save the access token as a session variable
        session([
            'access_token' => $accessToken,
            'refresh_token' => $client->getRefreshToken()
        ]);

        // Send a message to the main window indicating that the user has been authenticated
        return '<script>window.opener.postMessage("authenticated", "' . url('/') . '");window.close();</script>';
    }

    public function deleteVideo($videoId)
    {
        // Load the Google API Client Library and the YouTube service
        $client = new Google_Client();
        $client->addScope(Google_Service_YouTube::YOUTUBE_FORCE_SSL);
        $client->addScope(Google_Service_YouTube::YOUTUBE);

        $client->setClientId("103077607700-c53tduiob0gttrquv84dcvm3ivj9a8fr.apps.googleusercontent.com");
        $client->setClientSecret("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");
        $client->setRedirectUri("https://lpts.ladybirdsmis.com/auth/callback");

        // Retrieve the access token from the session and set it on the Google_Client object
        if (session()->has('access_token')) {
            $accessToken = session("access_token");
            $client->setAccessToken($accessToken);
        } else {
            return "Click the authenticate button again!";
        }

        // Create a new YouTube service object
        $youtube = new Google_Service_YouTube($client);

        // Delete the video
        try {
            $response = $youtube->videos->delete($videoId);
            return "Video deleted successfully: " . $videoId;
        } catch (Google_Service_Exception $e) {
            return "" . htmlspecialchars($e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        // $this->refreshAccessToken();
        // Get the access token from the session
        $access_token = session('access_token');

        // Create a new Google client and set the access token
        $client = new Google_Client();
        $client->setAccessToken($access_token);

        // Create a new YouTube service and set the client
        $youtube = new Google_Service_YouTube($client);

        // Get the file from the request
        $file = $request->file('file');

        // Get video details from request
        $title = $request->input('video_name');
        $description = $request->input('video_description');
        $tags = ["Ladybird", "Learn", session("school_information")->school_name];
        $categoryId = '22'; // You can set this based on your needs
        $privacyStatus =  $request->input('video_privacy');


        // Create a new video resource and set its snippet and status
        $video = new Google_Service_YouTube_Video();
        $videoSnippet = new Google_Service_YouTube_VideoSnippet();
        if ($title) {
            $videoSnippet->setTitle($title);
        }
        if ($description) {
            $videoSnippet->setDescription($description);
        }
        if ($tags) {
            $videoSnippet->setTags($tags);
        }
        if ($categoryId) {
            $videoSnippet->setCategoryId($categoryId);
        }
        if ($privacyStatus) {
            $videoStatus = new Google_Service_YouTube_VideoStatus();
            $videoStatus->setPrivacyStatus($privacyStatus);
            $video->setStatus($videoStatus);
        }
        if ($videoSnippet) {
            $video->setSnippet($videoSnippet);
        }

        // Upload the video and set its resource
        try {
            // Call the API's videos.insert method to create and upload the video.
            $insertResponse = $youtube->videos->insert(
                "status,snippet",
                $video,
                array('data' => file_get_contents($file), 'mimeType' => 'video/*')
            );

            // If you want to make other calls after the file upload, set setDefer back to false
            return json_encode(response()->json(['message' => 'Video uploaded successfully', 'id' =>  $insertResponse['id']]));
        } catch (Google_Service_Exception $e) {
            return json_encode(response()->json(['message' => sprintf(
                '<p>A service error occurred: <code>%s</code></p>',
                htmlspecialchars($e->getMessage())
            )]));
        } catch (Google_Exception $e) {
            return json_encode(response()->json(['message' => sprintf(
                '<p>An client error occurred: <code>%s</code></p>',
                htmlspecialchars($e->getMessage())
            )]));
        }
    }

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
    function checkPresnt($array, $string)
    {
        if (count($array) > 0) {
            for ($i = 0; $i < count($array); $i++) {
                if ($string == $array[$i]) {
                    return 1;
                }
            }
        }
        return 0;
    }
}

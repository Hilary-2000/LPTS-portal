<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use stdClass;

date_default_timezone_set('Africa/Nairobi');

class assignmentController extends Controller
{
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
    //manage alll the questions and the question bank
    function getSubjects(){
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
        for ($index=0; $index < count($tables_subject); $index++) { 
            $teachers_id = $tables_subject[$index]->teachers_id;
            // explode to show the teacher and subject
            $tr_n_subject = explode("|",$teachers_id);
            // return $tr_n_subject;

            $classes_taught = [];
            $real_class_names = [];
            // split the classes and the teacher
            for ($index_1=0; $index_1 < count($tr_n_subject); $index_1++) { 
                $tr_subject = substr($tr_n_subject[$index_1],1,(strlen($tr_n_subject[$index_1])-2));
                // return $tr_subject;
                $tr_id = explode(":",$tr_subject);
                if ($tr_id[0] == $teacher_id) {
                    $class_name = $this->classNameAdms($tr_id[1]);
                    if (!$this->checkPresnt($classes_taught,$class_name)) {
                        array_push($classes_taught,$class_name);
                    }
                }

                if ($tr_id[0] == $teacher_id) {
                    $class_name = $tr_id[1];
                    if (!$this->checkPresnt($real_class_names,$class_name)) {
                        array_push($real_class_names,$class_name);
                    }
                }
            }

            if (count($classes_taught) > 0) {
                $tables_subject[$index]->class_taught = $classes_taught;
                // array_push($subjects_taught,$tables_subject[$index]);

                $tables_subject[$index]->real_class_names = $real_class_names;
                array_push($subjects_taught,$tables_subject[$index]);
            }
        }
        // return $subjects_taught;
        // get the notifications
        $teacher_notifications = $this->getTrsNotification();
        return view("assignment_dash",["teacher_notifications" => $teacher_notifications,"subjects_taught" => $subjects_taught]);
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

    // create assignments
    function createAssignments($subject_id,$selected_class){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // check if the school information is set
        if (session("school_information") == null) {
            return redirect("/");
        }
        // check if the class selected is invalid
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // check if the class is present
        $class_details = DB::select("SELECT * FROM `settings` WHERE `sett` = 'class' AND `valued` LIKE '%".$selected_class."%';");

        if (count($class_details) == 0) {
            session()->flash("invalid",$selected_class." is an invalid class.");
            return redirect("/Teacher/Assignment");
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

        // get the assignments set for that subjects and class
        $assignments = DB::select("SELECT * FROM `assignments` WHERE `class` = ? AND `subject_id` = ? ORDER BY `id` DESC",[$selected_class,$subject_id]);
        
        // get the subject details
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);

        if (count($subject_details) == 0) {
            session()->flash("invalid","Invalid subject! Select a subject from the subject list below.");
            return redirect("/Teacher/Assignment");
        }
        $class_name = $selected_class;
        $selected_class = $this->classNameAdms($selected_class);

        // get the notifications
        $teacher_notifications = $this->getTrsNotification();
        return view("subject_assignment",["teacher_notifications" => $teacher_notifications,"class_name" => $class_name,"subject_id" => $subject_id, "selected_class" => $selected_class, "subject_details" => $subject_details,"assignments" => $assignments]);
    }

    function createAssign($subject_id,$class_name){// check if the school information is set
        if (session("school_information") == null) {
            return redirect("/");
        }
        // check if the class selected is invalid
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // check if the class is present
        $class_details = DB::select("SELECT * FROM `settings` WHERE `sett` = 'class' AND `valued` LIKE '%".$class_name."%';");

        if (count($class_details) == 0) {
            session()->flash("invalid",$class_name." is an invalid class.");
            return redirect("/Teacher/Assignment");
        }

        // proceed and check if the subject exists
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);

        if (count($subject_details) == 0) {
            session()->flash("invalid","Invalid Subject! Select a subject from the subject list below.");
            return redirect("/Teacher/Assignment");
        }
        $selected_class = $this->classNameAdms($class_name);
        $staff_infor = session("staff_infor");
        // return $staff_infor;

        // show the platform to create the test
        // get the notifications
        $teacher_notifications = $this->getTrsNotification();
        return view("set_assignment",["teacher_notifications" => $teacher_notifications,"subject_id" => $subject_id,"class_name" => $class_name,"subject_details" => $subject_details,"selected_class" => $selected_class]);
    }

    function createAssignment(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $request;
        
        // save the data in the database
        $subject_id = $request->input("subject_id");
        $class_name = $request->input("class_name");
        $assignment_name = $request->input("assignment_name");
        $assignment_start_date = $request->input("assignment_start_date");
        $assignment_end_date = $request->input("assignment_end_date");

        // create the assignment
        // database name
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

        // insert the data
        $array = "[]";
        $status = "0";
        $my_period = new stdClass();
        $my_period->start_date = $assignment_start_date;
        $my_period->end_date = $assignment_end_date;
        $in_name = json_encode($my_period);
        $date_created = date("YmdHis");
        $staff_infor = session("staff_infor");
        $insert = DB::insert("INSERT INTO `assignments` (`name`,`period`,`questions`,`answers`,`status`,`date_created`,`created_by`,`class`,`subject_id`,`academic_yr`) VALUES (?,?,?,?,?,?,?,?,?,?)",[$assignment_name,$in_name,$array,$array,$status,$date_created,$staff_infor->user_id,$class_name,$subject_id,$academic_year]);

        // get the latest quiz id
        $assignments = DB::select("SELECT * FROM `assignments` ORDER BY `id` DESC LIMIT 1");
        
        if (count($assignments) > 0) {
            $assignment_id = $assignments[0]->id;
            return redirect("/Assignments/Set/".$assignment_id);
        }else{
            session()->flash("invalid","Invalid Assignment Id!");
            return redirect("/Teacher/Assignments/".$subject_id."/Create/".$class_name."");
        }
    }

    function editAssignment($assignment_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the subject details
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);

        // get the subject details and the class
        if (count($select) > 0) {
            // get the assignments
            $class_selected = $select[0]->class;
            $subject_id = $select[0]->subject_id;

            // get the subject details
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);

            // get the subject name
            $subject_name = count($subject_details) > 0 ? $subject_details[0]->display_name : "Null";

            // RETURN THE NEW SUBJECT NAME
            $student_class = $this->classNameAdms($class_selected);

            // get notifications
            $teacher_notifications = $this->getTrsNotification();
            return view("editAssignments",["teacher_notifications" => $teacher_notifications,"assignment_id" => $assignment_id,"selected_class" => $class_selected,"subject_name" => $subject_name,"class_selected" => $student_class,"subject_id" => $subject_id,"class_name" => $student_class, "assignment_details" => $select[0]]);
        }else{
            session()->flash("invalid","Invalid Assignment Id!");
            return redirect("/Teacher/Assignment");
        }
    }

    function assignmentIds($assignment_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        
        // get the assignments published
        $assignment_data = DB::select("SELECT * FROM `assignments` WHERE `id` = ? AND status = '1'",[$assignment_id]);
        // return $assignment_data;

        if (count($assignment_data) > 0) {
            // get the subject data
            $subject_id = $assignment_data[0]->subject_id;

            // get the subject data
            $subject_data = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);
            
            // return the questions and its should be displayed as a carousel
            return view("attemptAssignment",["assignment_data" => $assignment_data, "subject_details" => $subject_data, "assignment_id" => $assignment_id]);
        }else{
            session()->flash("invalid","An error has occured, It seems the assignment has not been published!");
            return redirect("/Students/Assignment");
        }
    }

    function assignmentStatus($assignment_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        
        // get the assignment data
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);
        if (count($select) > 0) {
            $status = $select[0]->status == 0 ? 1 : 0;
            
            // update table
            $update = DB::update("UPDATE `assignments` SET `status` = ? WHERE `id` = ?",[$status,$assignment_id]);

            // return to the main page
            session()->flash("valid","Assignment status changed successfully!");
            return redirect("/Assignments/Edit/".$assignment_id);
        }
        session()->flash("invalid","An error has occured!");
        return redirect("/Assignments/Edit/".$assignment_id);
    }

    function deleteAssignment($assignment_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // select
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);

        // this is to delete the resources
        if (count($select) > 0) {
            $classes = $select[0]->class;
            $subject_id = $select[0]->subject_id;
            $questions = $select[0]->questions;
            if ($this->isJson_report($questions)) {
                $quiz = json_decode($questions);
                // return $quiz;

                // loop through the resources
                for ($index=0; $index < count($quiz); $index++) { 
                    $resources = $quiz[$index]->resources;
                    if ($this->isJson_report($resources)) {
                        $resources = json_decode($resources);
                        if ($index == 1) {
                            for ($ind=0; $ind < count($resources); $ind++) { 
                                // get the locale for the resource
                                $locale = $resources[$ind]->locale;
                                
                                // delete the file
                                $this->DeleteFiles($locale);
                            }
                        }
                    }
                }
            }

            // this is to delete the question and assignments
            $delete = DB::delete("DELETE FROM `assignments` WHERE `id` = ?",[$assignment_id]);
            session()->flash("valid","Assignment deleted successfully!");
            return redirect("/Teacher/Assignments/$subject_id/Create/$classes");
        }else{
            session()->flash("invalid","An error occured!");
            return redirect("/Teacher/Assignment");
        }

    }

    function updateAssignments(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        
        // return $request;
        $assignment_name = $request->input("assignment_name");
        $start_date = $request->input("start_date");
        $end_date = $request->input("end_date");
        $assignment_id = $request->input("assignment_id");

        $period = new stdClass();
        $period->start_date = $start_date;
        $period->end_date = $end_date;

        // stringify text
        $text_data = json_encode($period);

        // update the data
        $update = DB::update("UPDATE `assignments` SET `name` = ?, `period` = ? WHERE `id` = ?",[$assignment_name,$text_data,$assignment_id]);

        // redirect data 
        session()->flash("valid","Assignment updated successfully!");
        return redirect("/Assignments/Edit/".$assignment_id);

    }

    function setAssignment($assignment_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the subject details
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);

        // get the subject details and the class
        if (count($select) > 0) {
            // get the assignments
            $class_selected = $select[0]->class;
            $subject_id = $select[0]->subject_id;

            // get the subject details
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);

            // get the subject name
            $subject_name = count($subject_details) > 0 ? $subject_details[0]->display_name : "Null";

            // RETURN THE NEW SUBJECT NAME
            $student_class = $this->classNameAdms($class_selected);

            // get notifications
            $teacher_notifications = $this->getTrsNotification();
            return view("edit_assignments",["teacher_notifications" => $teacher_notifications,"assignment_id" => $assignment_id,"selected_class" => $class_selected,"subject_name" => $subject_name,"class_selected" => $student_class,"subject_id" => $subject_id,"class_name" => $student_class, "assignment_details" => $select[0]]);
        }else{
            session()->flash("invalid","Invalid Assignment Id!");
            return redirect("/Teacher/Assignment");
        }
    }

    function uploadAssignments(Request $request)
    {
        if (session("school_information") == null) {
            return redirect("/");
        }
        $resource_name = $request->input("resource_name");
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName =$this->replace_space_with_underscore($resource_name)."_".date("YmdHis").".".$extension;

        // tell if the path is present
        if (!File::exists(public_path("/Assignment/resources/"))) {
            // create if it does mot exist
            File::makeDirectory(public_path("/Assignment/resources/"), $mode = 0777, $recursive = true);
        }

        // check if the database name directory of the school is present
        if (!File::exists(public_path("/Assignment/resources/" . session("school_information")->database_name . ""))) {
            // create if it does mot exist
            File::makeDirectory(public_path("/Assignment/resources/" . session("school_information")->database_name . ""), $mode = 0777, $recursive = true);
        }

        // call the file by the subject id and file name

        $file->move(public_path('Assignment/resources/' . session("school_information")->database_name), $fileName);
        $public_path = "/Assignment/resources/" . session("school_information")->database_name;

        return [$resource_name, $fileName, $public_path];
    }

    function DeleteFiles($fileName)
    {
        
        // return $req;
        $file_path = public_path($fileName); // Replace with the actual path to the file

        if (File::exists($file_path)) {
            File::delete($file_path);
        }

        return "File deleted successfully!";
    }

    function addAssignments(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $request;
        $assignment_question_holder = $request->input("assignment_question_holder");
        $multiple_choices_holder = $request->input("multiple_choices_holder");
        $maximum_points_holder = $request->input("maximum_points_holder");
        $resources_location = $request->input("resources_location");
        $assignment_id = $request->input("assignment_id");
        $correct_answer = $request->input("correct_answer");

        // get the data
        $quiz_data = new stdClass();
        $quiz_data->quiz = $assignment_question_holder;
        $quiz_data->choice = $multiple_choices_holder;
        $quiz_data->resources = $resources_location;
        $quiz_data->points = $maximum_points_holder;
        $quiz_data->correct_answer = $correct_answer;

        // return $quiz_data;

        // get the data of the assignment
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the student data
        $assignment_table = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);
        // return $assignment_id;
        // get the questio
        if (count($assignment_table) > 0) {
            $questions = $assignment_table[0]->questions;
            // return $questions;
            // json date
            $id = 0;
            if ($this->isJson_report($questions)) {
                // questions
                $questions = json_decode($questions);
                
                // loop through the questions
                for ($index=0; $index < count($questions); $index++) { 
                    if ($questions[$index]->id >= $id) {
                        $id = $questions[$index]->id;
                    }
                }
                $quiz_data->id = $id+=1;
                // add a question
                // return $quiz_data;
                array_push($questions,$quiz_data);

                // update the database
                $questions = json_encode($questions);
                $update = DB::update("UPDATE `assignments` SET `questions` = ? WHERE `id` = ?",[$questions,$assignment_id]);

                // session
                session()->flash("valid","Assignments have been successfully updated!");
                return redirect("/Assignments/Set/".$assignment_id."");
            }else{
                $quiz_data->id = $id+=1;
                $new_data = [];
                array_push($new_data,$quiz_data);

                // decode quiz
                $questions = json_encode($new_data);
                $update = DB::update("UPDATE `assignments` SET `questions` = ? WHERE `id` = ?",[$questions,$assignment_id]);
                
                // session
                session()->flash("valid","Assignments have been successfully updated!");
                return redirect("/Assignments/Set/".$assignment_id."");
            }
        }else{
            // session
            session()->flash("invalid","No such assignment present!");
            return redirect("/Assignments/Set/".$assignment_id."");
        }
    }

    function deleteQuiz($assignment_id,$questions){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $question_id = $questions;
        // return $assignment_id;
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);

        // count
        if (count($select) > 0) {
            // return $select;
            $questions = $select[0]->questions;
            // return $questions;

            // json decode
            if ($this->isJson_report($questions)) {
                $my_question = json_decode($questions);
                // return $my_question;

                // loop through the question
                $new_questions = [];
                $array_data = [];
                if (count($my_question) > 0) {
                    for ($index=0; $index < count($my_question); $index++) {
                        if ($question_id != $my_question[$index]->id) {
                            array_push($new_questions,$my_question[$index]);
                        }else{
                            array_push($array_data, $my_question[$index]);
                        }
                    }
                }
                // update the table
                $new_quiz = json_encode($new_questions);
                $update = DB::update("UPDATE `assignments` SET `questions` = ? WHERE `id` = ?",[$new_quiz,$assignment_id]);

                // loop and delete the resources
                if (count($array_data) > 0) {
                    // delete the resources
                    for ($index=0; $index < count($array_data); $index++) { 
                        // delete resources
                        $maswali = $array_data[$index]->resources;

                        if ($this->isJson_report($maswali)) {
                            $maswali = json_decode($maswali);
                            for ($ind=0; $ind < count($maswali); $ind++) {
                                // delete the resources linked with this questions
                                $locale = $maswali[$ind]->locale;
                                $this->DeleteFiles($locale);
                            }
                        }
                    }
                    // session
                    session()->flash("valid","Quiz have been successfully deleted!");
                    return redirect("/Assignments/Set/".$assignment_id."");
                }
            }
        }
        // session
        session()->flash("invalid","Quiz have been successfully deleted!");
        return redirect("/Assignments/Set/".$assignment_id."");
    }

    function submitAnswers(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $request;
        $assignment_data = $request->input("assignment_data");
        $assignment_id = $request->input("assignment_id");
        $assignment_answers = $request->input("assignment_answers");

        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // student information
        $student_data = session("student_information");
        // return $student_data;

        // answers
        $answers = new stdClass();
        $answers->student_id = $student_data->adm_no;
        $answers->date_completed = date("YmdHis");
        $answers->answer = $assignment_answers;
        $answers->marks_attained = 0;
        $answers->review = "";
        $answers->marked = false;
        // return $answers;

        // get the questions
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);
        if (count($select) > 0) {
            // return $select;
            // get answers
            $my_answers = $select[0]->answers;
            if ($this->isJson_report($my_answers)) {
                // get answers
                $my_answers = json_decode($my_answers);

                // add the new data to this table
                $present = 0;
                for ($index=0; $index < count($my_answers); $index++) { 
                    if ($my_answers[$index]->student_id == $student_data->adm_no) {
                        $present = 1;
                        $my_answers[$index] = $answers;
                    }
                }

                // if its present push it
                array_push($my_answers,$answers);

                // turn it to a string and update the table.
                $new_answers = json_encode($my_answers);
                $update = DB::update("UPDATE `assignments` SET `answers` = ? WHERE `id` = ?",[$new_answers,$assignment_id]);

                // success
                session()->flash("valid","Assignments Submitted Successfully!");
                return redirect("/Students/Assignment");
            }else{
                // my answers
                $new_answers = [$answers];
                $new_answers = json_encode($answers);

                // update answers
                $update = DB::update("UPDATE `assignments` SET `answers` = ? WHERE `id` = ?",[$new_answers,$assignment_id]);

                // success
                session()->flash("valid","Assignments Updated successfully!");
                return redirect("/Students/Assignment");
            }
        }else{
            session()->flash("invalid","The assignment you`ve done seems to have been deleted! Contact your teacher for further advice!");
            return redirect("/Students/Assignment");
        }
    }

    function reviewMyAnswers($assignment_id){
        // return $assignment_id;
        if (session("school_information") == null) {
            return redirect("/");
        }

        // get the subject details
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        
        // get the data from the database
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ? ORDER BY `id` DESC",[$assignment_id]);
        // return $select;
        // check if there is going to be a result st
        if (count($select) > 0) {
            // get the assignments
            $class_selected = $select[0]->class;
            $subject_id = $select[0]->subject_id;

            // get the subject details
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_id]);

            // get the subject name
            $subject_name = count($subject_details) > 0 ? $subject_details[0]->display_name : "Null";

            // RETURN THE NEW SUBJECT NAME
            $student_class = $this->classNameAdms($class_selected);
            
            // return value
            $student_notification = $this->getStudentMessage();

            // get the creater
            DB::setDefaultConnection("mysql");
            $creator_dets = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$select[0]->created_by]);
            $creators_name = count($creator_dets) > 0 ? "Tr. ".ucwords(strtolower($creator_dets[0]->fullname)) : "N/A";
            return view("reviewAssignment",["creators_name" => $creators_name,"student_notification" => $student_notification,"assignment" => $select[0], "subject_name" => $subject_name]);
        }else{
            session()->flash("invalid","The assignment might have been deleted or not published! Ask your teacher for further advice.");
            return redirect("/Students/Assignment");
        }
    }

    function markedAnswers(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $request;
        $assignment_id = $request->input("assignment_id");
        $my_student_answers = $request->input("my_student_answers");
        $student_id = $request->input("student_id");

        // get the question answers
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // select
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);
        
        // count data
        if (count($select)) {
            // store assignment data
            $assignment_data = $select[0];

            // loop through the assignments and update the specific student data
            if ($this->isJson_report($assignment_data->answers)) {
                $stud_answers = json_decode($assignment_data->answers);
                $student_answers = json_decode($my_student_answers);

                // update the total marks
                $total_score = 0;
                for ($index=0; $index < count($student_answers->answer); $index++) { 
                    $stud_ans = $student_answers->answer[$index];
                    if (isset($stud_ans->score)) {
                        $total_score+=$stud_ans->score;
                    }else{
                        $student_answers->answer[$index]->score = 0;
                        $student_answers->answer[$index]->review = 0;
                    }
                }
                $student_answers->marks_attained = $total_score;
                $student_answers->marked = true;
                $student_answers->date_marked = date("YmdHis");
                // return $student_answers;

                // loop through
                for ($index=0; $index < count($stud_answers); $index++) { 
                    if ($stud_answers[$index]->student_id == $student_id) {
                        $stud_answers[$index] = $student_answers;
                        // break;
                    }
                }
                // stringify text
                // return $stud_answers;
                $answers_string = json_encode($stud_answers);

                // update the database;
                $update = DB::update("UPDATE `assignments` SET `answers` = ? WHERE `id` = ?",[$answers_string,$assignment_id]);

                // update the session
                session()->flash("valid","Marks successfully saved");
                return redirect("/Assignments/Mark/".$assignment_id."");
            }
        }
    }

    function markAssignments($assignment_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // return $assignment_id;

        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        // get the answers so as to display the students who have attended the exams

        $assignments = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);

        // loop throught the answers and add the student name
        if (count($assignments) > 0) {
            // loop through the assignments
            $answers = $assignments[0]->answers;

            // jsondecode 
            // return $answers;
            if ($this->isJson_report($answers)) {
                $answers = json_decode($answers);
                // return $answers;
                for ($index=0; $index < count($answers); $index++) {
                    // answers
                    $ans = $answers[$index];

                    // decode answer
                    $student_id = $ans->student_id;

                    // get the student name
                    $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$student_id]);
                    $ans->student_name = count($student_data) > 0 ? $student_data[0]->first_name." ".$student_data[0]->second_name : "Not Set!";
                }
            }

            // return $answers;
            $assignments[0]->answers = $answers;

            // get the subject details
            $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$assignments[0]->subject_id]);
            $subject_name = count($subject_details) > 0 ? $subject_details[0]->display_name : "Not Set!";
            
            // subject name

            
            // RETURN THE NEW SUBJECT NAME
            $class_selected = $assignments[0]->class;
            $student_class = $this->classNameAdms($class_selected);

            // return the list
            // return $assignments;

            // get the notifications
            $teacher_notifications = $this->getTrsNotification();
            return view("markAssignment",["teacher_notifications" => $teacher_notifications,"assignment_id" => $assignment_id,"assignment" => $assignments[0],"subject_name" => $subject_name, "selected_class" => $student_class, "class_name" => $class_selected, "subject_id" => $assignments[0]->subject_id]);
        }else{
            session()->flash("invalid","The assignment you have selected seems to have been deleted!");
            return redirect()->back();
        }
    }

    function markStudentAssignments($assignment_id,$adm_no){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the student adm no
        $select = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);
        // return $select;

        // get the student answers and the subject details and also the assignment name
        if (count($select) > 0) {
            $answers = $select[0]->answers;
            $questions = $select[0]->questions;
            if ($this->isJson_report($answers) && $this->isJson_report($questions)) {
                $answers = json_decode($answers);

                // loop through the answers to get the student answers
                $student_answers = [];
                $student_names = "Invalid";
                $class_details = "0";
                for ($index=0; $index < count($answers); $index++) { 
                    if ($answers[$index]->student_id == $adm_no) {
                        $student_answers = $answers[$index];
                        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$adm_no]);
                        $student_names = count($student_data) > 0 ? $student_data[0]->first_name." ".$student_data[0]->second_name : "Not Present";
                        $class_details = count($student_data) > 0 ? $student_data[0]->stud_class : "0";
                        break;
                    }
                }

                // if is empty return to the previous page
                if (empty($student_answers)) {
                    session()->flash("invalid","The student seems to have not done the assesment yet!");
                    return redirect()->back();
                }

                // decode the questions
                $questions = json_decode($questions);
                // return $student_answers;

                // get the subject details
                $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$select[0]->subject_id]);
                $subject_name = count($subject_details) > 0 ? $subject_details[0]->display_name : "Not Set!";

                $class_selected = $select[0]->class;
                $student_class = $this->classNameAdms($class_selected);

                // return $student_answers['answer'];
                // get the notifications
                $teacher_notifications = $this->getTrsNotification();

                return view("markingAssignments",["teacher_notifications" => $teacher_notifications,"class_details" => $class_details,"assignment_id" => $assignment_id,"adm_no" => $adm_no,"assignment_details" => $select[0],"selected_class" => $student_class, "class_name" => $class_selected, "subject_name" => $subject_name, "questions" => $questions, "student_names" => $student_names, "student_answers" => $student_answers]);
            }else{
                session()->flash("valid","An error has occured!");
                return redirect()->back();
            }
        }
    }

    function redoAssignment($assignment_id,$student_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // database name
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the answers
        $assignments = DB::select("SELECT * FROM `assignments` WHERE `id` = ?",[$assignment_id]);
        if (count($assignments) > 0) {
            $answers = $assignments[0]->answers;
            
            // answers
            if ($this->isJson_report($answers)) {
                $answers = json_decode($answers);
                // return $answers;

                // loop questions
                $new_loops = [];
                for ($index=0; $index < count($answers); $index++) { 
                    if ($answers[$index]->student_id != $student_id) {
                        array_push($new_loops,$answers[$index]);
                    }
                }
                // return $new_loops;

                // update the answers
                $new_loops = json_encode($new_loops);
                $update = DB::update("UPDATE `assignments` SET `answers` = ? WHERE `id` = ?",[$new_loops,$assignment_id]);

                // return value
                session()->flash("valid","Update done successfully!");
                return redirect("/Assignments/Mark/".$assignment_id);
            }else{
                session()->flash("invalid","Assignment seems to have been deleted!");
                return redirect("/Assignments/Mark/".$assignment_id);
            }
        }else{
            session()->flash("invalid","Assignment seems to have been deleted!");
            return redirect("/Assignments/Mark/".$assignment_id);
        }
    }

    function isJson_report($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    function replace_space_with_underscore($string) {
        // Replace all spaces with underscores
        return str_replace(' ', '_', $string);
      }

    function classNameAdms($data){
        if ($data == "-1") {
            return "Alumni";
        }
        if ($data == "-2") {
            return "Transfered";
        }
        $datas = "Grade ".$data;
        if (strlen($data)>1) {
            $datas = $data;
        }
        return $datas;
    }
    function checkPresnt($array, $string){
        if (count($array)>0) {
            for ($i=0; $i < count($array); $i++) { 
                if ($string == $array[$i]) {
                    return 1;
                }
            }
        }
        return 0;
    }
}

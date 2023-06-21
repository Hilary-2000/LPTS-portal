<?php

namespace App\Http\Controllers;

use App\Classes\reports\FPDF;
use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class QuestionBank extends Controller
{
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
        return view("questionbank",["teacher_notifications" => $teacher_notifications,"subjects_taught" => $subjects_taught]);
    }
    
    function createTest($lesson_id,$class_id){
        // return $lesson_id;
        // check if the session hasn`t expired
        if (session("school_information") == null) {
            return redirect("/");
        }

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the subject details
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$lesson_id]);

        // get the data
        $this_data = DB::select("SELECT * FROM `questionbanks` WHERE `class` = ? AND `subject_id` = ?",[$class_id,$lesson_id]);
        
        if (count($this_data) > 0 && count($subject_details) > 0) {
            $questions = $this_data[0]->questions;
            if ($this->isJson_report($questions)) {
                $questions = json_decode($questions);
                // check questions
                if (count($questions) == 0) {
                    session()->flash("invalid","This subject question bank has no questions set to be able to create a test!");
                    return redirect("/Teacher/QuestionBank/".$lesson_id."/Create/".$class_id."");
                }
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
    
                // get the lesson plan data
                $lesson_plan_data = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?",[$lesson_id,$academic_year,$class_id]);
                $lesson_plan = count($lesson_plan_data) > 0 ? ($this->isJson_report($lesson_plan_data[0]->longterm_plan_data) ? json_decode($lesson_plan_data[0]->longterm_plan_data):[]):[];
                // return $lesson_plan;
                // get the lesson plan ids

                $topics_to_select = [];
                for ($counter=0; $counter < count($lesson_plan); $counter++) { 
                    $topic_sub_topic = new stdClass();
                    $topic_sub_topic->topic = $lesson_plan[$counter]->index;
                    $topic_sub_topic->subtopics = [];

                    // get the substrands
                    $sub_strands = $lesson_plan[$counter]->sub_strands;
                    // return $sub_strands;
                    for ($ind=0; $ind < count($sub_strands); $ind++) {
                        array_push($topic_sub_topic->subtopics,$sub_strands[$ind]->sub_index);
                    }

                    // add the arrays
                    array_push($topics_to_select,$topic_sub_topic);
                }
                $topics_to_select = json_encode($topics_to_select);
                        
                // get the question banks
                $teacher_notifications = $this->getTrsNotification();
                return view("createQBtest",["teacher_notifications" => $teacher_notifications,"topics_selected" => $topics_to_select,"lesson_id" => $lesson_id,"class_id" => $class_id,"subject_details" => $subject_details[0],"class_name" => $this->classNameAdms($class_id),"lesson_plan" => $lesson_plan]);
            }
        }
        session()->flash("invalid","This subject question bank has no questions set to be able to create a test!");
        return redirect("/Teacher/QuestionBank/".$lesson_id."/Create/".$class_id."");
    }

    function createQB($lesson_id,$class_id){
        // check if the session hasn`t expired
        if (session("school_information") == null) {
            return redirect("/");
        }

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$lesson_id]);
        // return $subject_details;

        // get the subjects questions in the question bank
        $questions_in_bank = DB::select("SELECT * FROM `questionbanks` WHERE `subject_id` = ? AND `class` = ?",[$lesson_id,$class_id]);
        $questions = count($questions_in_bank) > 0 ? json_decode($questions_in_bank[0]->questions) : [];

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

        
        // get the lesson plan data
        $lesson_plan_data = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?",[$lesson_id,$academic_year,$class_id]);
        // return $subject_details;
        $lesson_plan = count($lesson_plan_data) > 0 ? ($this->isJson_report($lesson_plan_data[0]->longterm_plan_data) ? json_decode($lesson_plan_data[0]->longterm_plan_data):[]):[];

        // get the teacher notification
        $teacher_notifications = $this->getTrsNotification();
        return view("manageqb",["teacher_notifications" => $teacher_notifications,"subject_details" => $subject_details[0], "questions"=>$questions,"lesson_id" => $lesson_id,"class_id" => $class_id,"class_name" => $this->classNameAdms($class_id),"lesson_plan" => $lesson_plan]);
    }

    function deleteQuestion($qid,$lesson_id,$class_id){
        // check if the session hasn`t expired
        if (session("school_information") == null) {
            return redirect("/");
        }

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the questions
        $qb_data = DB::select("SELECT * FROM `questionbanks` WHERE `subject_id` = ? AND `class` = ?",[$lesson_id,$class_id]);
        if (count($qb_data) > 0) {
            $questions = $qb_data[0]->questions;
            if ($this->isJson_report($questions)) {
                $questions = json_decode($questions);

                // get the question with that id and skip it
                $new_questions = [];
                for ($index=0; $index < count($questions); $index++) {
                    if ($questions[$index]->id == $qid) {
                        continue;
                    }
                    array_push($new_questions,$questions[$index]);
                }
                // return $new_questions;

                // decode it
                $new_questions = json_encode($new_questions);

                // update the database
                $update_table = DB::update("UPDATE `questionbanks` SET `questions` = ? WHERE `class` = ? AND `subject_id` = ?",[$new_questions,$class_id,$lesson_id]);
                session()->flash("valid","The question bank has been modified successfully!");
                return redirect("/Teacher/QuestionBank/".$lesson_id."/Create/".$class_id."");
            }else{
                session()->flash("invalid","An error occured. Try again later!");
                return redirect("/Teacher/QuestionBank/".$lesson_id."/Create/".$class_id."");
            }
        }else {
                session()->flash("invalid","The question has not been found in the bank. Its either deleted or missing!");
                return redirect("/Teacher/QuestionBank/".$lesson_id."/Create/".$class_id."");
        }
    }

    function bankQuestions($lesson_id,$class_id){
        // check if the session hasn`t expired
        if (session("school_information") == null) {
            return redirect("/");
        }

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$lesson_id]);

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
        
        // get the lesson plan data
        $lesson_plan_data = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?",[$lesson_id,$academic_year,$class_id]);
        // return $subject_details;
        $lesson_plan = count($lesson_plan_data) > 0 ? ($this->isJson_report($lesson_plan_data[0]->longterm_plan_data) ? json_decode($lesson_plan_data[0]->longterm_plan_data):[]):[];

        // get the question banks
        $teacher_notifications = $this->getTrsNotification();
        return view("bankquestions",["teacher_notifications" => $teacher_notifications,"lesson_id" => $lesson_id,"class_id" => $class_id, "subject_details" => $subject_details[0],"class_name" => $this->classNameAdms($class_id),"lesson_plan" => $lesson_plan]);
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
    function bankIt(Request $request){
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // return $request;
        $lesson_id = $request->input("lesson_id");
        $class_id = $request->input("class_id");
        $question = $request->input("question");
        $question_topic = $request->input("question_topic");
        $question_sub_topic = $request->input("question_sub_topic");
        $question_difficulty = $request->input("question_difficulty");
        
        $questioned = new stdClass();
        $questioned->difficulty = $question_difficulty;
        $questioned->question = $question;
        $questioned->multiple_choices = [];
        $questioned->topic = $question_topic;
        $questioned->sub_topic = $question_sub_topic;
        $questioned->date_recorded = date("YmdHis");

        // get the data already stored
        $question_banks = DB::select("SELECT * FROM `questionbanks` WHERE `class` = ? AND `subject_id` = ?",[$class_id,$lesson_id]);
        if (count($question_banks) > 0) {
            if ($this->isJson_report($question_banks[0]->questions)) {
                $maswali = json_decode($question_banks[0]->questions);
                
                // get an id
                $this_id = 0;
                for ($ind=0; $ind < count($maswali); $ind++) { 
                    if ($maswali[$ind]->id >= $this_id) {
                        $this_id = $maswali[$ind]->id;
                    }
                }
                $this_id+=1;
                $questioned->id = $this_id;

                // array push the data
                array_push($maswali,$questioned);

                // update the table
                $updated_data = json_encode($maswali);
                DB::update("UPDATE `questionbanks` SET `questions` = ? WHERE `class` = ? AND `subject_id` = ?",[$updated_data,$class_id,$lesson_id]);
                session()->flash("successfull_banking","Question banked successfully!");
                return redirect("/Teacher/QuestionBank/".$lesson_id."/BankQuestions/".$class_id."");
            }else{
                // create a new record and update it
                $new_maswali = [];
                $questioned->id = 1;

                // put the object in the arraylist
                array_push($new_maswali,$questioned);

                // update the table
                $updated_data = json_encode($new_maswali);
                DB::update("UPDATE `questionbanks` SET `questions` = ? WHERE `class` = ? AND `subject_id` = ?",[$updated_data,$class_id,$lesson_id]);
                session()->flash("successfull_banking","Question banked successfully!");
                return redirect("/Teacher/QuestionBank/".$lesson_id."/BankQuestions/".$class_id."");
            }
        }else{
            // create a new record and update it
            $new_maswali = [];
            $questioned->id = 1;

            // put the object in the arraylist
            array_push($new_maswali,$questioned);

            // update the table
            $updated_data = json_encode($new_maswali);
            $insert = DB::insert("INSERT INTO `questionbanks` (`class`,`subject_id`,`questions`) VALUES (?,?,?)",[$class_id,$lesson_id,$updated_data]);

            session()->flash("successfull_banking","Question banked successfully!");
            return redirect("/Teacher/QuestionBank/".$lesson_id."/BankQuestions/".$class_id."");
        }
        session()->flash("unsuccessfull_banking","An error has occured!");
        return redirect("/Teacher/QuestionBank/".$lesson_id."/BankQuestions/".$class_id."");
    }
    function editQB($lesson_id,$class_id,$quiz_id){
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        $subject_details = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$lesson_id]);

        // get the data
        $this_data = DB::select("SELECT * FROM `questionbanks` WHERE `class` = ? AND `subject_id` = ?",[$class_id,$lesson_id]);
        
        if (count($this_data) > 0) {
            $questions = $this_data[0]->questions;
            if ($this->isJson_report($questions)) {
                $questions = json_decode($questions);

                // get the specific questions
                $present = 0;

                // loop
                $data = [];
                for ($ind=0; $ind < count($questions); $ind++) { 
                    if ($questions[$ind]->id == $quiz_id) {
                        $present = 1;
                        $data = $questions[$ind];
                        break;
                    }
                }

                // return with the data 
                if ($present == 1) {

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
        
                    // get the lesson plan data
                    $lesson_plan_data = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?",[$lesson_id,$academic_year,$class_id]);
                    // return $lesson_plan_data;
                    $lesson_plan = count($lesson_plan_data) > 0 ? ($this->isJson_report($lesson_plan_data[0]->longterm_plan_data) ? json_decode($lesson_plan_data[0]->longterm_plan_data):[]):[];
                    return view("updatequestions",["question_data"=>$data,"lesson_id" => $lesson_id,"class_id" => $class_id,"subject_details" => $subject_details[0],"class_name" => $this->classNameAdms($class_id),"lesson_plan" => $lesson_plan]);
                }
            }
        }
        session()->flash("invalid","The question has either been deleted or does not exist!");
        return redirect("/Teacher/QuestionBank/".$lesson_id."/Create/".$class_id."");
    }
    function updateTable(Request $request){
        // return $request;
        $lesson_id = $request->input("lesson_id");
        $class_id = $request->input("class_id");
        $question = $request->input("question");
        $question_topic = $request->input("question_topic");
        $question_sub_topic = $request->input("question_sub_topic");
        $question_difficulty = $request->input("question_difficulty");
        $quiz_id = $request->input("quiz_id");
        $date_created = $request->input("date_created");

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        
        $questioned = new stdClass();
        $questioned->difficulty = $question_difficulty;
        $questioned->question = $question;
        $questioned->multiple_choices = [];
        $questioned->topic = $question_topic;
        $questioned->sub_topic = $question_sub_topic;
        $questioned->date_recorded = $date_created;
        $questioned->id = $quiz_id;

        // get the data already stored
        $question_banks = DB::select("SELECT * FROM `questionbanks` WHERE `class` = ? AND `subject_id` = ?",[$class_id,$lesson_id]);
        if (count($question_banks) > 0) {
            if ($this->isJson_report($question_banks[0]->questions)) {
                $maswali = json_decode($question_banks[0]->questions);

                for ($ind=0; $ind < count($maswali); $ind++) { 
                    if ($maswali[$ind]->id == $quiz_id) {
                        $maswali[$ind] = $questioned;
                    }
                }

                // update the table
                $updated_data = json_encode($maswali);
                DB::update("UPDATE `questionbanks` SET `questions` = ? WHERE `class` = ? AND `subject_id` = ?",[$updated_data,$class_id,$lesson_id]);
                session()->flash("successfull_banking","Question updated successfully!");
                return redirect("/Teacher/QuestionBank/".$lesson_id."/EditQB/".$class_id."/sub_id/".$quiz_id."");
            }else{
                // create a new record and update it
                $new_maswali = [];

                // put the object in the arraylist
                array_push($new_maswali,$questioned);

                // update the table
                $updated_data = json_encode($new_maswali);
                DB::update("UPDATE `questionbanks` SET `questions` = ? WHERE `class` = ? AND `subject_id` = ?",[$updated_data,$class_id,$lesson_id]);
                session()->flash("successfull_banking","Question updated successfully!");
                return redirect("/Teacher/QuestionBank/".$lesson_id."/EditQB/".$class_id."/sub_id/".$quiz_id."");
            }
        }
        session()->flash("unsuccessfull_banking","The question has either been deleted or does not exist!");
        return redirect("/Teacher/QuestionBank/".$lesson_id."/Create/".$class_id."");
    }
    function isJson_report($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    function createTestQB(Request $request){
        // return $request;
        $lesson_id = $request->input("lesson_id");
        $class_id = $request->input("class_id");
        $exam_title = $request->input("exam_title");
        $exams_time = $request->input("exams_time");
        $question_difficulty = $request->input("question_difficulty");
        $maximum_questions = $request->input("maximum_questions");
        $question_topic = $request->input("question_topic");
        $my_instructions = $request->input("my_instructions");
        $topics_selected = $request->input("topics_selected");
        $topics_to_select = $request->input("topics_to_select");

        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        
        // process the data
        $quiz_set = DB::select("SELECT * FROM `questionbanks` WHERE `class` = ? AND `subject_id` = ?",[$class_id,$lesson_id]);
        // return $quiz_set;

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

        $lesson_plan = DB::select("SELECT * FROM `lesson_plan` WHERE `subject_id` = ? AND `academic_year` = ? AND `class` = ?",[$lesson_id,$academic_year,$class_id]);
        if (count($quiz_set) > 0 && count($lesson_plan) > 0) {
            $questions = $quiz_set[0]->questions;
            $lesson_plans = $lesson_plan[0]->longterm_plan_data;

            if ($this->isJson_report($questions) && $this->isJson_report($lesson_plans) && $this->isJson_report($topics_selected) && $this->isJson_report($my_instructions)) {
                $questions = json_decode($questions);
                $lesson_plans = json_decode($lesson_plans);
                $topics_selected = $question_topic == "Random" ? json_decode($topics_to_select):json_decode($topics_selected);
                $my_instructions = json_decode($my_instructions);
                // return $my_instructions;

                // randomize questions
                $questions = $this->shuffleArray($questions);
                
                // loop through the data and pick the questions with the characteristics you want
                $difficulties = ['Simple','Normal','Hard','Extra-Hard'];
                $questions_to_set = [];
                $difficult = [];
                for ($index=0; $index < count($questions); $index++) {
                    $Q_difficulty = $question_difficulty == "Random" ? $this->getRandomDifficulty($difficulties) : $question_difficulty;
                    // return $question_difficulty;
                    array_push($difficult,$Q_difficulty);
                    if ($question_topic == "Random") {
                        // pick the next question
                        if ($Q_difficulty == $questions[$index]->difficulty) {
                            array_push($questions_to_set,$questions[$index]);
                        }
                    }else{
                        if ($Q_difficulty == $questions[$index]->difficulty){
                            // here we have to verify that the topic or subtopic is present
                            $topic = $questions[$index]->topic;
                            $sub_topic = $questions[$index]->sub_topic;
                            
                            // topics
                            $present = $this->checkIfPresent($topics_selected,$topic,$sub_topic);
                            if ($present) {
                                // add the next question
                                array_push($questions_to_set,$questions[$index]);
                            }
                        }
                    }
                }

                // return the question
                // return $questions_to_set;

                // We are done set the questions
                $my_pdf = new PDF();
                $my_pdf->set_document_title($exam_title);
                $my_pdf->set_document_box(session("school_information")->po_box);
                $my_pdf->set_document_code(session("school_information")->box_code);
                $my_pdf->set_school_contact(session("school_information")->school_contact);
                $my_pdf->set_school_name(session("school_information")->school_name);
                $my_pdf->setCompayLogo("https://lsims.ladybirdsmis.com/sims/".session("school_information")->school_profile_image);
                $my_pdf->AddPage();

                // if the instructions are set
                $my_pdf->SetFont("Helvetica","",8);
                $my_pdf->Cell(150,6,"Time : ".$exams_time,0,1);
                if (count($my_instructions) > 0) {
                    $my_pdf->SetFont("Helvetica","B",9);
                    $my_pdf->Cell(150,6,"Instructions:",0,1);
                    $my_pdf->SetFont("Helvetica","",8);

                    // display the instructions
                    for ($index=0; $index < count($my_instructions); $index++) { 
                        $my_pdf->MultiCell(190,4,($index+1).") ".$my_instructions[$index],0,'J',false);
                    }
                }
                // set questions
                $my_pdf->Ln(10);
                $my_pdf->SetFont("Helvetica","B",9);
                $my_pdf->Cell(150,6,"Questions:",0,1);
                $my_pdf->SetFont("Helvetica","",8);
                

                // randomize the questions
                $questions_to_set = $this->shuffleArray($questions_to_set);
                // return $questions_to_set;

                // displaye the questions
                for ($indexes=0; $indexes < count($questions_to_set); $indexes++) { 
                    if ($indexes == $maximum_questions) {
                        break;
                    }

                    // add the questions to the cells 
                    $my_pdf->MultiCell(190,6,($indexes+1).") ".$questions_to_set[$indexes]->question,0,'J',false);
                }

                $my_pdf->Output();
            }
        }
        // create a pdf
        // $my_pdf = new PDF();
        // $my_pdf->AddPage();
        // $my_pdf->Output();
    }

    function getRandomDifficulty($array){
        $random_number = rand(0,(count($array)-1));
        return $array[$random_number];
    }

    // get random topics and subtopics
    function checkIfPresent($subjects_selected,$topic,$sub_topic){
        // if the topic is null return false meaning that the question should not be selected
        if ($topic == null) {
            return false;
        }

        // check the topic or subtopic if present
        for ($index=0; $index < count($subjects_selected); $index++) {
            if ($topic == $subjects_selected[$index]->topic) {
                // subtopics
                $subtopics = $subjects_selected[$index]->subtopics;
                // if the subtopic in the selected subjects 
                if (count($subtopics) == 0) {
                    return true;
                }
                
                // loop through the subtopics and get the subtopic thats present
                $subtopics = $subjects_selected[$index]->subtopics;
                for ($ind=0; $ind < count($subtopics); $ind++) { 
                    if ($subtopics[$ind] == $sub_topic) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    function shuffleArray($array) {
        $n = count($array);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = rand(0, $i);
            $temp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $temp;
        }
        return $array;
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

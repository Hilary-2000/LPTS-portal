<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\reports\FPDF;
use Exception;
use stdClass;

class parentCOntroller extends Controller
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
    function getParentsNotification(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        // connect to mysql 2
        DB::setDefaultConnection("mysql2");

        $parents_id = session("parents_data")['parent_contact'];

        // get the teacher`s notification
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_id` = '".$parents_id."' AND `owner_type` = 'parent' AND `message_edit_status` = 'Published' AND `message_status` = '0' ORDER BY `id` DESC");

        return $notifications;
    }
    function parentPerfomance(){
        // get your the students first
        // check if the parent data has been found
        if (session("parents_data") == null) {
            return redirect("/");
        }
        
        // get the school database
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // get the students to whom their parents number is linked with this parent number
        $parent_contact = session("parents_data")['parent_contact'];

        // get the student data
        $students_data = DB::select("SELECT * FROM `student_data` WHERE `parentContacts` = ? OR `parent_contact2` = ?",[$parent_contact,$parent_contact]);

        $term = $this->getTermV2();


        // loop through the student data
        for ($index=0; $index < count($students_data); $index++) { 
            // get the student class
            $stud_class = $students_data[$index]->stud_class;
            $subject_taught = DB::select("SELECT * FROM `table_subject` WHERE `classes_taught` LIKE '%".$stud_class."%'");
            $students_data[$index]->subject_taught = count($subject_taught) > 0 ? $subject_taught : [];

            // get the number of active exams
            $today = date("Y-m-d");
            $active_exams = DB::select("SELECT * FROM `exams_tbl` WHERE `class_sitting` LIKE '%".$stud_class."%' AND `end_date` > '".$today."'");

            // student data
            $students_data[$index]->active_exams = $active_exams;
        }

        // return $students_data;

        $parents_notification = $this->getParentsNotification();
        return view("parentStudPerfomance",["parents_notification" => $parents_notification,"students_data" => $students_data]);
    }
    //
    function displayDash(){
        // check if the parent data has been found
        if (session("parents_data") == null) {
            return redirect("/");
        }
        
        // get the school database
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        // return session("parents_data");

        // get the students to whom their parents number is linked with this parent number
        $parent_contact = session("parents_data")['parent_contact'];

        // get the student data
        $students_data = DB::select("SELECT * FROM `student_data` WHERE `parentContacts` = ? OR `parent_contact2` = ?",[$parent_contact,$parent_contact]);

        $term = $this->getTermV2();
        // loop through the students and add the student balance
        for ($index=0; $index < count($students_data); $index++) { 
            $student_adm = $students_data[$index]->adm_no;

            // get student data
            $balance = $this->calculatedBalanceReport($student_adm,$term);
            // return $balance;
            
            $students_data[$index]->stud_balance = $balance;
            
            // student present stats;
            $students_data[$index]->present_stats = $this->presentStats($students_data[$index]->adm_no,$students_data[$index]->stud_class);

            // return $students_data[$index]->present_stats;
        }

        // return $students_data;
        // get the current week in the academic calender
        $current_term = $this->getTermV2();

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

        // get the students balance, attendance percentage and
        // get the teacher`s notification
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_id` = '".$parent_contact."' AND `owner_type` = 'parent' AND `message_edit_status` = 'Published' ORDER BY `id` DESC LIMIT 5");

        // get parents notification
        $parents_notification = $this->getParentsNotification();
        return view("parent_dashboard",["dash_notification" => $notifications, "parents_notification" => $parents_notification,"students_data" => $students_data, "term" => $term,"weeks" => $weeks,"week_number" => $week_number]);
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

    function underscoreToSpace_2($str)
    {
        $str = str_replace(' ', '_', $str); // replace underscore with space
        $str = strtoupper($str); // capitalize the whole word
        return $str;
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
    function presentStats($admno,$class_student){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the current term its starting period and ending period
        $term = $this->getTermV2();
        
        // get when the term is starting and ending
        $calender = $this->getAcademicStartV1();

        // attendance data 
        $attendance_stats = DB::select("SELECT COUNT(DISTINCT `date`) AS 'Totals' FROM `attendancetable` WHERE `date` >= ? AND `date` <= ? AND `class` = '".$class_student."'",[$calender[0],$calender[1]]);
        // return $calender;

        // get the number of times the student has been present
        $total_attendance = 0;
        if (count($attendance_stats) > 0) {
            $total_attendance = $attendance_stats[0]->Totals;
        }

        // get the number of times the register has been called
        $student_attendance = 0;
        $student_att = DB::select("SELECT COUNT(DISTINCT `date`) AS 'Totals' FROM `attendancetable` WHERE `date` >= ? AND `date` <= ? AND `admission_no` = '".$admno."' AND `class` = '".$class_student."'",[$calender[0],$calender[1]]);
        if (count($student_att) > 0) {
            $student_attendance = $student_att[0]->Totals;
        }

        // return the student attendance and the total attendance
        return [$student_attendance,$total_attendance];
    }
    function calculatedBalanceReport($admno,$term){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ? ",[$admno]);
        
        $feestopay = $this->getFeesAsFromTermAdmited($term,$student_data);
        // return $feestopay;
        // echo $feestopay."<br>";
        $feespaidbystud = $this->getFeespaidByStudent($student_data[0]->adm_no);

        // know if they paid this term
        $lastbal = $this->lastBalance($student_data[0]->adm_no);
        // return $lastbal;
        $balance = $feespaidbystud;
        $balance = $lastbal;
        $lastacad = $this->lastACADyrBal($admno);
        // return $lastacad;

        // get balance
        $feestopay += $lastacad;
        $balance = $feestopay - $feespaidbystud;
        // echo $transport_deduction;
        return $balance;
    }

    function lastBalance($admno){
        $last_balance = DB::select("SELECT `balance` ,`date_of_transaction`FROM `finance` WHERE `stud_admin` = ? ORDER BY `transaction_id` DESC LIMIT 1",[$admno]);
        
        // count data
        if (count($last_balance) > 0) {
            for ($index=0; $index < count($last_balance); $index++) { 
                $last_paid = date("YmdHis",strtotime($last_balance[$index]->date_of_transaction));
                $beginyear = date("YmdHis",strtotime($this->getAcademicStart()));
                if ($beginyear < $last_paid) {
                    return $last_balance[$index]->balance;
                }
            }
        }
        return 0;
    }
    function lastACADyrBal($admno){
        $beginyear = $this->getAcademicStart();
        $finance_table = DB::select("SELECT `balance` FROM `finance` WHERE `stud_admin` = ? AND `date_of_transaction` <= ? ORDER BY `transaction_id` DESC LIMIT 1",[$admno,$beginyear]);

        
        $balance = 0;
        if (count($finance_table) > 0) {
            $balance = $finance_table[0]->balance;
        }

        return $balance;
    }
    function getFeespaidByStudent($admno){
        $beginyear = $this->getAcademicStart();
        //start date of the academic year
        // echo $beginyear;
        $currentdate = date("Y-m-d");
        // finance paid
        $transactions_done = DB::select("SELECT * FROM `finance` where `stud_admin` = ?  AND `date_of_transaction` BETWEEN ? and ? AND `payment_for` != 'admission fees'",[$admno,$beginyear,$currentdate]);

        // count transactions
        if (count($transactions_done) > 0) {
            $total_amounts = 0;
            for ($index=0; $index < count($transactions_done); $index++) { 
                $payment_for = strtolower($transactions_done[$index]->payment_for);

                // get provisional pay
                $provisonal_pays = $this->getProvisionalPayments($admno);
                if (!$this->isPresent($provisonal_pays,$payment_for)) {
                    $total_amounts += $transactions_done[$index]->amount*1;
                }
            }
            return $total_amounts;
        }
        return 0;
    }
    // is present
    function isPresent($array,$string){
        if (count($array) > 0 ) {
            for ($indexes=0; $indexes <count($array) ; $indexes++) { 
                if ($string == $array[$indexes]) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }
    // get provisional payments
    function getProvisionalPayments($adm_no){
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = '".$adm_no."'");

        $class_student = "";
        if (count($student_data) > 0) {
            $class_student = $student_data[0]->stud_class;
        }

        // get the provisional payments
        $provisional_pay = DB::select("SELECT * FROM `fees_structure` WHERE `roles` = 'provisional' AND `classes` LIKE '%|".$class_student."|%'");

        $roles = [];
        if (count($provisional_pay) > 0) {
            for ($index=0; $index < count($provisional_pay); $index++) {
                array_push($roles,strtolower($provisional_pay[$index]->expenses));
            }
        }
        return $roles;
    }

    function getAcademicStart(){
        $db_select = DB::select("SELECT `start_time` FROM `academic_calendar` WHERE `term` = 'TERM_1'");
        if (count($db_select) > 0) {
            return $db_select[0]->start_time;
        }
        return date('Y')."-01-01";
    }

    function getAcademicStartV1($term = "TERM_1"){
        $academic_calender = DB::select("SELECT * FROM `academic_calendar` WHERE `term` = '".$term."'");
        if (count($academic_calender) > 0){
            return [$academic_calender[0]->start_time,$academic_calender[0]->end_time];
        }
        return [date('Y')."-01-01",date('Y')."-01-30"];
    }

    function getFeesAsFromTermAdmited($current_term,$student_data){
        // class
        $class = "%|".$student_data[0]->stud_class."|%";

        // get the date of registration is in what term
        $date_of_reg = count($student_data) > 0 ? $student_data[0]->D_O_A : date("Y-m-d");

        // get the academic calender
        $academic_calender = DB::select("SELECT * FROM `academic_calendar` WHERE `start_time` <= ? AND `end_time` >= ?",[$date_of_reg,$date_of_reg]);
        
        // assign data
        $term_admitted = count($academic_calender) > 0 ? $academic_calender[0]->term : "null";
        $start_time = count($academic_calender) > 0 ? $academic_calender[0]->start_time : "null";

        // start time and end time
        if ($start_time != "null" && $start_time > $date_of_reg) {
            $term_admitted = "TERM_1";
        }
        

        // get term the student was admitted
        if ($term_admitted == "TERM_1" || $term_admitted == "null") {
            if($current_term == "TERM_1"){
                $select = "SELECT sum(`TERM_1`) AS 'TOTALS' FROM `fees_structure` WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }elseif($current_term == "TERM_2"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`) AS 'TOTALS' FROM `fees_structure`  WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }elseif($current_term == "TERM_3"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`)+sum(`TERM_3`) AS 'TOTALS' FROM `fees_structure`  WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }
        }elseif($term_admitted == "TERM_2"){
            if($current_term == "TERM_2"){
                $select = "SELECT sum(`TERM_2`) AS 'TOTALS' FROM `fees_structure`  WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }elseif($current_term == "TERM_3"){
                $select = "SELECT sum(`TERM_2`)+sum(`TERM_3`) AS 'TOTALS' FROM `fees_structure`  WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }
        }elseif($term_admitted == "TERM_3"){
            if($current_term == "TERM_3"){
                $select = "SELECT sum(`TERM_3`) AS 'TOTALS' FROM `fees_structure`  WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }
        }else {
            if($current_term == "TERM_1"){
                $select = "SELECT sum(`TERM_1`) AS 'TOTALS' FROM `fees_structure` WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }elseif($current_term == "TERM_2"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`) AS 'TOTALS' FROM `fees_structure`  WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }elseif($current_term == "TERM_3"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`)+sum(`TERM_3`) AS 'TOTALS' FROM `fees_structure`  WHERE `classes` LIKE ? AND `activated` = 1  and `roles` = 'regular';";
            }
        }

        // return $select;
        $select = DB::select($select,[$class]);

        // prepare the data
        if (count($select) > 0) {
            // total fees
            $fees_to_pay = $select[0]->TOTALS;
            // return $fees_to_pay;

            // get if the student is boarding
            $isBoarding = DB::select("SELECT * FROM `boarding_list` WHERE `student_id` = ?",[$student_data[0]->adm_no]);

            // if they are boarding add their boarding fees
            if (count($isBoarding) > 0) {
                $boarding_fees = $this->getBoardingFeesFromTermAdmitted($student_data[0]->stud_class,$term_admitted);
                $fees_to_pay = $fees_to_pay+$boarding_fees;
            }

            // get the discounts
                
            // get discounts
            $discounts = $this->getDiscount($student_data[0]->adm_no);
            if ($discounts[0] > 0 || $discounts[1] > 0) {
                if ($discounts[0] > 0) {
                    $discounts = 100 - $discounts[0];
                    $fees_to_pay = round(($fees_to_pay * $discounts) / 100);
                }else{;
                    $fees_to_pay = $fees_to_pay - $discounts[1];
                }
            }

            // check if they are in transport
            $is_transport = DB::select("SELECT * FROM `transport_enrolled_students` WHERE `student_id` = ?",[$student_data[0]->adm_no]);

            // echo isBoarding($admno,$conn2);
            if (count($is_transport) > 0) {
                $transport = $this->transportBalanceSinceAdmission($student_data[0]->adm_no);
                $fees_to_pay+=$transport;
            }

            // add how much they are to pay if they are borders
            // $fees_to_pay+=TransportDeduction($conn2,$admno);

            if (strlen($fees_to_pay) < 1) {
                return 0;
            }
            return $fees_to_pay;
        }
        return 0;
    }
    function isJson_report_fin($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
    // get the student payment of transport per term if joined the same term the payment is taken for the only term
    function transportBalanceSinceAdmission($admno){
        $transport_values = DB::select("SELECT * FROM `transport_enrolled_students` WHERE `student_id` = ?",[$admno]);

        // count tranport values
        if (count($transport_values) > 0) {
            $deregistered = $transport_values[0]->deregistered;

            $router_t1 = 0;
            $router_t2 = 0;
            $router_t3 = 0;

            // check if json
            if ($this->isJson_report_fin($deregistered)){
                $deregistered = json_decode($deregistered);
                for ($index=0; $index < count($deregistered); $index++) { 
                    $elems = $deregistered[$index];
                    if($elems->term == "TERM_1"){
                        $router_t1 = $this->routeAmount($elems->route);
                    }
                    if($elems->term == "TERM_2"){
                        $router_t2 = $this->routeAmount($elems->route);
                    }
                    if($elems->term == "TERM_3"){
                        $router_t3 = $this->routeAmount($elems->route);
                    }
                }
            }

            // get the current term
            $current_term = $this->getTermV2();
            if ($current_term == "TERM_1") {
                return $router_t1;
            }elseif ($current_term == "TERM_2") {
                return $router_t1+$router_t2;
            }elseif ($current_term == "TERM_3") {
                return $router_t1+$router_t2+$router_t3;
            }else{
                return $router_t1;
            }
        }
        return 0;
    }
    
    // get route amount
    function routeAmount($route_id){
        $transport_data = DB::select("SELECT * FROM `van_routes` WHERE `route_id` = ?",[$route_id]);
        if (count($transport_data) > 0) {
            $route_price = $transport_data[0]->route_price;
            return $route_price;
        }
        return 0;
    }
    
    function isTransport($conn2,$admno){
        $select = "SELECT * FROM `transport_enrolled_students` WHERE `student_id` = ?;";
        $stmt = $conn2->prepare($select);
        $stmt->bind_param("s",$admno);
        $stmt->execute();
        $stmt->store_result();
        $rnum = $stmt->num_rows;
        if ($rnum > 0) {
            return true;
        }
        return false;
    }

    function getDiscount($admno){
        $select = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$admno]);

        // count select 
        if (count($select) > 0) {
            return [$select[0]->discount_percentage,$select[0]->discount_value];
        }

        // return values
        return [0,0];
    }

    function getBoardingFeesFromTermAdmitted($class,$admitted_term = "null"){
        $class = "%|".$class."|%";
        $term = $this->getTermV2();
        // echo $class;
        if ($admitted_term == "TERM_1" || $admitted_term == "null") {
            if($term == "TERM_1"){
                $select = "SELECT sum(`TERM_1`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }elseif($term == "TERM_2"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }elseif($term == "TERM_3"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`)+sum(`TERM_3`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }
        }elseif($admitted_term == "TERM_2"){
            if($term == "TERM_2"){
                $select = "SELECT sum(`TERM_2`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }elseif($term == "TERM_3"){
                $select = "SELECT sum(`TERM_2`)+sum(`TERM_3`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }
        }elseif($admitted_term == "TERM_3"){
            if($term == "TERM_3"){
                $select = "SELECT sum(`TERM_3`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }
        }else {
            if($term == "TERM_1"){
                $select = "SELECT sum(`TERM_1`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }elseif($term == "TERM_2"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }elseif($term == "TERM_3"){
                $select = "SELECT sum(`TERM_1`)+sum(`TERM_2`)+sum(`TERM_3`) AS 'Total' FROM `fees_structure` WHERE `roles` = 'boarding' AND `activated` = 1 AND `classes` like ?";
            }
        }

        $total_fees = DB::select($select,[$class]);

        if (count($total_fees) > 0) {
            return $total_fees[0]->Total;
        }
        return 0;
    }

    function parentFees(){
        // get the parents students fees details
        // check if the parent data has been found
        if (session("parents_data") == null) {
            return redirect("/");
        }
        
        // get the school database
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");
        // return session("parents_data");

        // get the students to whom their parents number is linked with this parent number
        $parent_contact = session("parents_data")['parent_contact'];

        // get the student data
        $students_data = DB::select("SELECT * FROM `student_data` WHERE `parentContacts` = ? OR `parent_contact2` = ?",[$parent_contact,$parent_contact]);
        
        $term = $this->getTermV2();

        // get the student fees balance and the last payment date
        for ($index=0; $index < count($students_data); $index++) { 
            $admission_no = $students_data[$index]->adm_no;
            $balance = $this->calculatedBalanceReport($admission_no,$term);

            // store the student balance
            $students_data[$index]->student_balance = $balance;

            // store the last time the student paid and how much
            $finance_data = DB::select("SELECT * FROM `finance` WHERE `stud_admin` = '".$admission_no."' ORDER BY `transaction_id` DESC LIMIT 1");
            
            // STORE THE LAST TRANSACTION
            $students_data[$index]->last_payment = count($finance_data) > 0 ? $finance_data[0] : null;
        }

        // student data
        // return $students_data;
        $parents_notification = $this->getParentsNotification();
        return view("parentFees",["parents_notification" => $parents_notification,"students_data" => $students_data]);

    }

    function studentFeesHistory($student_adm){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the parents students fees details
        // check if the parent data has been found
        if (session("parents_data") == null) {
            return redirect("/");
        }
        
        // get the school database
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // check student
        $student_mine = $this->checkStudent($student_adm);

        // student data
        if (!$student_mine) {
            session()->flash("invalid","You can only view your children`s information, Kindly select your child from the list below.");
            return redirect("/Parent/Fees");
        }

        // get the student data
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$student_adm]);

        // return the data and the table
        if (count($student_data) == 0) {
            // return the data and display the students fees history
            session()->flash("invalid","Invalid student! Select your child from the list below!");
            return redirect("/Parent/Fees");
        }
        
        // get the academic calender so that we display the fees paid in term one, term two, term three and the other academic years
        $academic_calender = DB::select("SELECT * FROM `academic_calendar`;");

        // store the termly fees
        $termly_fees = [];
        for ($index=0; $index < count($academic_calender); $index++) {
            // termly data
            $termly_data = new stdClass();
            $termly_data->term = $academic_calender[$index]->term;
            $termly_data->start_time = $academic_calender[$index]->start_time;
            $termly_data->end_time = $academic_calender[$index]->end_time;

            // fees recorded for that student
            $transactions = DB::select("SELECT * FROM `finance` WHERE `stud_admin` = ? AND `date_of_transaction` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$student_adm,$academic_calender[$index]->start_time,$academic_calender[$index]->end_time]);

            // store the transactions in this std class
            $termly_data->transactions = $transactions;

            // push the data into an array holder
            array_push($termly_fees,$termly_data);
        }

        $term = $this->getTermV2();
        // return $termly_fees;
        $fees_balance = $this->calculatedBalanceReport($student_adm,$term);

        // get the last time the student fees was paid
        $last_transaction = DB::select("SELECT * FROM `finance` WHERE `stud_admin` = '".$student_adm."' ORDER BY `transaction_id` DESC LIMIT 1;");

        // total fees since joining
        $total_paid = DB::select("SELECT SUM(`amount`) AS 'Total' FROM `finance` WHERE `stud_admin` = ?",[$student_adm]);
        $total_since_joining = count($total_paid) > 0 ? $total_paid[0]->Total : 0;

        // pull payment details
        $payment_details = DB::select("SELECT * FROM `settings` WHERE `sett` = 'payment details'");

        $payment_details = count($payment_details) > 0 ? ($this->isJson_report_fin($payment_details[0]->valued) ? json_decode($payment_details[0]->valued) : []) : [];
        // return $payment_details;

        // check if they are baording
        $isBoarding = DB::select("SELECT * FROM `boarding_list` WHERE `student_id` = ?",[$student_adm]);
        $baording_details = [];
        if (count($isBoarding) > 0) {
            $dorm_id = $isBoarding[0]->dorm_id;
            $dorm_name = DB::select("SELECT * FROM `dorm_list` WHERE `dorm_id` = ?",[$dorm_id]);

            // save the dorm details
            $baording_details = [(count($dorm_name) > 0 ? $dorm_name[0]->dorm_name : "N/A"),$isBoarding[0]->date_of_enrollment];
        }

        // get if they are enrolled in the transport
        $transport_data = DB::select("SELECT * FROM `transport_enrolled_students` WHERE `student_id` = ?",[$student_adm]);
        $transport_details = [];
        if (count($transport_data) > 0) {
            $deregistered = $transport_data[0]->deregistered;
            if ($this->isJson_report_fin($deregistered)) {
                $deregistered = json_decode($deregistered);
                for ($index=0; $index < count($deregistered); $index++) { 
                    $route_name = $deregistered[$index]->route;
                    // get the route name 
                    $route_name = DB::select("SELECT * FROM `van_routes` WHERE `route_id` = ?",[$route_name]);

                    // route name 
                    $deregistered[$index]->route_name = count($route_name) > 0 ? $route_name[0]->route_name : "N/A";
                    $deregistered[$index]->route_price = count($route_name) > 0 ? $route_name[0]->route_price : 0;
                }
                $transport_details = $deregistered;
            }
        }

        
        // CONTINUE AND DISPLAY THE VIEW
        $parents_notification = $this->getParentsNotification();
        return view("feesDetails",["parents_notification" => $parents_notification,"transport_details" => $transport_details,"baording_details" => $baording_details,"payment_details" => $payment_details,"total_since_joining" => $total_since_joining,"last_transaction" => $last_transaction,"student_data" => $student_data[0],"fees_balance" => $fees_balance,"termly_fees" => $termly_fees]);
    }

    // function to print fees statement between dates
    function printFeesStatement(Request $req){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $student_adm_no = $req->input("student_adm_no");
        $from_date = $req->input("from_date");
        $to_date = $req->input("to_date");
        
        // get the school database
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // check if the student is the parents
        $isMine = $this->checkStudent($student_adm_no);
        if (!$isMine) {
            return  "<p style='color:red'>Invalid student! Select your child from the list provided!</p>";
        }

        // get the students details
        $student_details = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$student_adm_no]);

        // get the student fees data
        $finance_data = DB::select("SELECT * FROM `finance` WHERE `stud_admin` = ? AND `date_of_transaction` BETWEEN ? AND ? ORDER BY `transaction_id` DESC",[$student_adm_no,$from_date,$to_date]);
        // return $finance_data;

        if (count($finance_data) == 0) {
            return "<p style='color:red'>NO Transactions found for \"".ucwords(strtolower($student_details[0]->first_name." ".$student_details[0]->second_name))."\" between ".date("D dS M Y",strtotime($from_date))." AND ".date("D dS M Y",strtotime($to_date))."!</p>";
        }

        // STORE THE MODES OF PAYMENT
        $cash =0;
        $mpesa = 0;
        $bank = 0;
        $reversed = 0;
        $credit_note = 0;
        $finance_list = [];
        $counter = 1;
        for ($index=0; $index < count($finance_data); $index++) { 
            $fin_data = array($counter, $finance_data[$index]->amount, $finance_data[$index]->transaction_code, $finance_data[$index]->mode_of_pay, $finance_data[$index]->payment_for, $finance_data[$index]->date_of_transaction, $finance_data[$index]->time_of_transaction,$finance_data[$index]->support_document);
            if ($finance_data[$index]->amount != 0) {
                array_push($finance_list, $fin_data);
                $counter++;
                if ($finance_data[$index]->mode_of_pay == "cash") {
                    $cash += $finance_data[$index]->amount;
                } 
                if ($finance_data[$index]->mode_of_pay == "mpesa"){
                    $mpesa += $finance_data[$index]->amount;
                }
                if ($finance_data[$index]->mode_of_pay == "bank"){
                    $bank += $finance_data[$index]->amount;
                }
                if ($finance_data[$index]->mode_of_pay == "reverse"){
                    $reversed += $finance_data[$index]->amount;
                }
                if ($finance_data[$index]->mode_of_pay == "Credit Note"){
                    $credit_note += $finance_data[$index]->amount;
                }
            }
        }
        // return $finance_list;

        // proceed and print the fees data
        $document_title = "Transactions for \"".ucwords(strtolower($student_details[0]->first_name." ".$student_details[0]->second_name))."\" - {".$student_details[0]->adm_no."} between ".date("D dS M Y",strtotime($from_date))." AND ".date("D dS M Y",strtotime($to_date))."";
        $pdf = new PDF("P","mm","A4");
        $pdf->set_document_title($document_title);
        $pdf->set_document_box(session("school_information")->po_box);
        $pdf->set_document_code(session("school_information")->box_code);
        $pdf->set_school_contact(session("school_information")->school_contact);
        $pdf->set_school_name(session("school_information")->school_name);
        $pdf->setCompayLogo("https://lsims.ladybirdsmis.com/sims/".session("school_information")->school_profile_image);
        $pdf->SetMargins(5, 5);
        $pdf->AddPage();
        $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(30, 5, "Cash :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Kes " . number_format($cash), 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell(30, 5, "M-Pesa :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Kes " . number_format($mpesa), 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell(30, 5, "Bank :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Kes " . number_format($bank), 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell(30, 5, "Reversed :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Kes " . number_format($reversed), 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell(30, 5, "Credit Note :", 0, 0, 'L', false);
        $pdf->Cell(30, 5, "Kes " . number_format($credit_note), 0, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell(30, 5, "Total Recieved:", 'T', 0, 'L', false);
        $pdf->Cell(30, 5, "Kes " . number_format($cash + $mpesa + $bank + $reversed + $credit_note), 'T', 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Times', 'IU', 13);
        $pdf->Ln();
        $pdf->Cell(200, 8, "Fees Collection Table", 0, 0, 'C', false);
        $pdf->Ln();
        $pdf->SetFont('Helvetica', 'B', 8);

        // finance table
        $header = array('No', 'Fees Paid', 'Code', 'Mode', 'Pay For', 'Date', 'Supporting Docs');
        $width = array(8, 20, 20, 22, 65, 40, 25);
        $pdf->financeTable($header, $finance_list, $width);

        // add the cell
        $pdf->Output();
    }

    function getFeesDetails($transaction_id, $adm_no){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the school database
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // check if the student is mine
        if (!$this->checkStudent($adm_no)) {
            session()->flash("invalid","Invalid student! Select your child from the list below!");
            return redirect("/Parent/Fees");
        }

        // get finance data
        $transaction = DB::select("SELECT * FROM `finance` WHERE `transaction_id` = ?",[$transaction_id]);
        if (count($transaction) == 0) {
            session()->flash("invalid","The transaction is not found!");
            return redirect("/Parent/Fees");
        }

        // return $transaction;
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$adm_no]);

        // connect to the main database
        // get the school database
        $database_name = "ladybird_smis";

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        // config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql");

        // get the staff information
        $staff_info = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$transaction[0]->payBy]);

        $paid_by = count($staff_info) > 0 ? $staff_info[0]->fullname : "System";

        // get the transaction
        $parents_notification = $this->getParentsNotification();
        return view("feesData",["parents_notification" => $parents_notification,"paid_by" => $paid_by,"transaction" => $transaction[0], "adm_no" => $adm_no, "student_data" => $student_data[0]]);
    }

    // function to check if the child is the parents

    function checkStudent($adm_no){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // student contact
        $parent_contact = session("parents_data")['parent_contact'];

        // student data and contacts
        $select = DB::select("SELECT * FROM `student_data` WHERE `parentContacts` = ? OR `parent_contact2` = ?",[$parent_contact,$parent_contact]);
        // return $select;

        if (count($select)) {
            for ($index=0; $index < count($select); $index++) { 
                if ($select[$index]->adm_no == $adm_no) {
                    return true;
                }
            }
        }

        return false;
    }

    function getTermV2(){
        $date = date("Y-m-d");
        $term = DB::select("SELECT * FROM `academic_calendar` WHERE `end_time` >= ? AND `start_time` <= ?",[$date,$date]);
        if (count($term) > 0) {
            return $term[0]->term;
        }

        return "TERM_1";
    }

    function parentAlert(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the school database
        $database_name = session("school_information")->database_name;

        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);

        DB::setDefaultConnection("mysql2");

        // GET THE NOTIFICATION
        $notifications = DB::select("SELECT * FROM `message_n_alert` WHERE `owner_type` = 'parent' AND `owner_id` = '".session("parents_data")['parent_contact']."' AND `message_edit_status` = 'Published' ORDER BY `id` DESC");

        // proceed and return the parent message button
        $parents_notification = $this->getParentsNotification();
        return view("parents_message_alerts",["parents_notification" => $parents_notification,"notifications" => $notifications]);
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

    function readParentAlert($alert_id){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        
        DB::setDefaultConnection("mysql2");

        // get the nofication details
        $notifications_details = DB::select("SELECT * FROM `message_n_alert` WHERE `id` = ?",[$alert_id]);
        // return $notifications_details;

        if (count($notifications_details) == 0) {
            return redirect("/Parent/Alert");
        }

        if ($notifications_details[0]->message_status == 0) {
            $update = DB::update("UPDATE `message_n_alert` SET `message_status` = '1' WHERE `id` = ?",[$alert_id]);
        }

        $parents_notification = $this->getParentsNotification();
        return view("read_parent_alert",["parents_notification" => $parents_notification,"notifications_details" => $notifications_details]);
    }

    function getParentsDetails(){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the teacher`s academic plan
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the student data
        $parent_contact = session("parents_data")['parent_contact'];
        $student_data = DB::select("SELECT * FROM `student_data` WHERE (`parentContacts` = '".$parent_contact."' OR `parent_contact2` = '".$parent_contact."') AND (`parentContacts` != '' AND `parent_contact2` != '') LIMIT 1");

        // return session("parents_data");
        // student data
        if (count($student_data) == 0) {
            session()->flash("invalid","No data found!");
            return redirect("/Parent/Dashboard");
        }
        // check if its the first parent
        if ($student_data[0]->parentContacts == $parent_contact) {
            // get the parent name contact
            $parent_data = [
                "parent_name" => $student_data[0]->parentName,
                "parent_contact" => $student_data[0]->parentContacts,
                "parent_relationship" => $student_data[0]->parent_relation,
                "parent_email" => $student_data[0]->parent_email
            ];
        }elseif ($student_data[0]->parent_contact2 == $parent_contact) {
            // get the parent name contact
            $parent_data = [
                "parent_name" => $student_data[0]->parentName,
                "parent_contact" => $student_data[0]->parentContacts,
                "parent_relationship" => $student_data[0]->parent_relation,
                "parent_email" => $student_data[0]->parent_email
            ];
        }else{
            $parent_data = session("parents_data");
        }

        // return the parents profile
        $parents_notification = $this->getParentsNotification();
        return view("parent_profile",["parents_notification" => $parents_notification, "parent_data" => $parent_data]);
    }
    function parentPasswordUpdate(Request $request){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // return $request;
        $parent_identifier = $request->input("parent_identifier");
        $portal_password = $request->input("password");
        $newpassword = $request->input("newpassword");
        $renewpassword = $request->input("renewpassword");

        // ARE THE PASSWORDS MATCHING
        if ($newpassword != $renewpassword) {
            session()->flash("invalid","Password don`t match!");
                return redirect("/Parent/Profile");
        }

        // portal passwords
        $portal_password = $this->encryptCode($portal_password);
        $newpassword = $this->encryptCode($newpassword);
        
        // get the parents details
        $select = DB::select("SELECT * FROM `student_data` WHERE `parentContacts` = ? OR `parent_contact2` = ? ORDER BY `ids` ASC LIMIT 1",[$parent_identifier,$parent_identifier]);
        // check if the password provided is correct
        if($select[0]->parentContacts == $parent_identifier){
            // check if the passwords match
            if ($portal_password != $select[0]->primary_parent_password) {
                session()->flash("invalid","Incorrect password provided!");
                return redirect("/Parent/Profile");
            }else{
                // proceed and update the user
                $update = DB::update("UPDATE `student_data` SET `primary_parent_password` = '".$newpassword."' WHERE `ids` = '".$select[0]->ids."'");
                session()->flash("valid","Password update done successfully!");
                return redirect("/Parent/Profile");
            }
        }elseif ($select[0]->parent_contact2 == $parent_identifier) {
            // check if the passwords match
            if ($portal_password != $select[0]->secondary_parent_password) {
                session()->flash("invalid","Incorrect password provided!");
                return redirect("/Parent/Profile");
            }else{
                // proceed and update the user
                $update = DB::update("UPDATE `student_data` SET `secondary_parent_password` = '".$newpassword."' WHERE `ids` = '".$select[0]->ids."'");
                session()->flash("valid","Password update done successfully!");
                return redirect("/Parent/Profile");
            }
        }else{
            session()->flash("invalid","Not found!");
            return redirect("/Parent/Profile");
        }
    }
    function studentPerfomances($student_adm){
        if (session("school_information") == null) {
            return redirect("/");
        }
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the student data so that we can get all the subjects taught in their class
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$student_adm]);
        // return $student_data;

        if (count($student_data) == 0) {
            session()->flash("invalid","Student details are not present!");
            return redirect("/Parent/Peformance");
        }

        // proceed and get subjects taught in their class
        $student_class = $student_data[0]->stud_class;
        $class_subjects = DB::select("SELECT * FROM `table_subject` WHERE `classes_taught` LIKE '%".$student_class."%';");

        // EXAMS FOR TERM ONE
        // get the exams done and separate it in term form
        $term_one = DB::select("SELECT * FROM `academic_calendar` WHERE `term` = 'TERM_1';");
        $start_time = $term_one[0]->start_time;
        $end_time = $term_one[0]->end_time;

        // GET THE EXAMS
        $our_exams = DB::select("SELECT * FROM `exams_tbl` WHERE `class_sitting` LIKE '%".$student_class."%' AND `start_date` BETWEEN '".$start_time."' AND '".$end_time."'");
        
        // CHECK IF HE WAS AMOUNG WHO SAT FOR THE EXAMS
        $student_exams_done = [];
        for ($index=0; $index < count($our_exams); $index++) { 
            $students_sitting = $our_exams[$index]->students_sitting;
            $exams_done = json_decode($students_sitting);
            for ($ind=0; $ind < count($exams_done); $ind++) { 
                if ($exams_done[$ind]->classname == $student_class) {
                    for ($indx=0; $indx < count($exams_done[$ind]->classlist); $indx++) { 
                        $elems = $exams_done[$ind]->classlist[$indx];
                        if ($elems == $student_adm) {
                            array_push($student_exams_done,$our_exams[$index]);
                        }
                    }
                }
            }
        }

        // exams done
        // return $student_exams_done;

        // GET THE STUDENT RESULTS FOR ALL THE SUBJECTS DONE
        $exams_results_1 = [];
        for ($index=0; $index < count($student_exams_done); $index++) { 
            $subject_done_unedited = $student_exams_done[$index]->subject_done;
            $subjects_list = explode(",",substr($subject_done_unedited,1,strlen($subject_done_unedited)-2));
            
            // set the subjects values
            $exams_data = new stdClass();
            $exams_data->name = $student_exams_done[$index]->exams_name;
            $exams_data->subjects = [];
            $exams_data->date_done = $student_exams_done[$index]->start_date;
            $exams_data->exams_id = $student_exams_done[$index]->exams_id;

            // add the subject data
            for ($ind=0; $ind < count($subjects_list); $ind++) { 
                $subject_details = new stdClass();
                $subject_details->name = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[0];
                $subject_details->display_name = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[1];
                $subject_details->max_marks = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[2];
                
                $subject_marks = DB::select("SELECT * FROM `exam_record_tbl` WHERE `exam_id` = ? AND `student_id` = ? AND `subject_id` = ? AND `class name` = ?",[$student_exams_done[$index]->exams_id,$student_adm,$subjects_list[$ind],$student_class]);
                $subject_details->scored_marks = count($subject_marks) > 0 ? $subject_marks[0]->exam_marks : 0;

                // push this data to the exam data subject marks
                array_push($exams_data->subjects,$subject_details);
            }

            // push array
            array_push($exams_results_1,$exams_data);
        }

        // END OF TERM ONE

        // EXAMS FOR TERM TWO
        // get the exams done and separate it in term form
        $term_two = DB::select("SELECT * FROM `academic_calendar` WHERE `term` = 'TERM_2';");
        $start_time = $term_two[0]->start_time;
        $end_time = $term_two[0]->end_time;

        // GET THE EXAMS
        $our_exams = DB::select("SELECT * FROM `exams_tbl` WHERE `class_sitting` LIKE '%".$student_class."%' AND `start_date` BETWEEN '".$start_time."' AND '".$end_time."'");
        
        // CHECK IF HE WAS AMOUNG WHO SAT FOR THE EXAMS
        $student_exams_done = [];
        for ($index=0; $index < count($our_exams); $index++) { 
            $students_sitting = $our_exams[$index]->students_sitting;
            $exams_done = json_decode($students_sitting);
            for ($ind=0; $ind < count($exams_done); $ind++) { 
                if ($exams_done[$ind]->classname == $student_class) {
                    for ($indx=0; $indx < count($exams_done[$ind]->classlist); $indx++) { 
                        $elems = $exams_done[$ind]->classlist[$indx];
                        if ($elems == $student_adm) {
                            array_push($student_exams_done,$our_exams[$index]);
                        }
                    }
                }
            }
        }

        // exams done
        // return $student_exams_done;

        // GET THE STUDENT RESULTS FOR ALL THE SUBJECTS DONE
        $exams_results_2 = [];
        for ($index=0; $index < count($student_exams_done); $index++) { 
            $subject_done_unedited = $student_exams_done[$index]->subject_done;
            $subjects_list = explode(",",substr($subject_done_unedited,1,strlen($subject_done_unedited)-2));
            
            // set the subjects values
            $exams_data = new stdClass();
            $exams_data->name = $student_exams_done[$index]->exams_name;
            $exams_data->subjects = [];
            $exams_data->date_done = $student_exams_done[$index]->start_date;
            $exams_data->exams_id = $student_exams_done[$index]->exams_id;

            // add the subject data
            for ($ind=0; $ind < count($subjects_list); $ind++) { 
                $subject_details = new stdClass();
                $subject_details->name = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[0];
                $subject_details->display_name = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[1];
                $subject_details->max_marks = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[2];
                
                $subject_marks = DB::select("SELECT * FROM `exam_record_tbl` WHERE `exam_id` = ? AND `student_id` = ? AND `subject_id` = ? AND `class name` = ?",[$student_exams_done[$index]->exams_id,$student_adm,$subjects_list[$ind],$student_class]);
                $subject_details->scored_marks = count($subject_marks) > 0 ? $subject_marks[0]->exam_marks : 0;

                // push this data to the exam data subject marks
                array_push($exams_data->subjects,$subject_details);
            }
            // push array
            array_push($exams_results_2,$exams_data);
        }
        // END OF TERM TWO


        // EXAMS FOR TERM THREE
        // get the exams done and separate it in term form
        $term_three = DB::select("SELECT * FROM `academic_calendar` WHERE `term` = 'TERM_3';");
        $start_time = $term_three[0]->start_time;
        $end_time = $term_three[0]->end_time;

        // GET THE EXAMS
        $our_exams = DB::select("SELECT * FROM `exams_tbl` WHERE `class_sitting` LIKE '%".$student_class."%' AND `start_date` BETWEEN '".$start_time."' AND '".$end_time."'");
        
        // CHECK IF HE WAS AMOUNG WHO SAT FOR THE EXAMS
        $student_exams_done = [];
        for ($index=0; $index < count($our_exams); $index++) { 
            $students_sitting = $our_exams[$index]->students_sitting;
            $exams_done = json_decode($students_sitting);
            for ($ind=0; $ind < count($exams_done); $ind++) { 
                if ($exams_done[$ind]->classname == $student_class) {
                    for ($indx=0; $indx < count($exams_done[$ind]->classlist); $indx++) { 
                        $elems = $exams_done[$ind]->classlist[$indx];
                        if ($elems == $student_adm) {
                            array_push($student_exams_done,$our_exams[$index]);
                        }
                    }
                }
            }
        }

        // exams done
        // return $student_exams_done;

        // GET THE STUDENT RESULTS FOR ALL THE SUBJECTS DONE
        $exams_results_3 = [];
        for ($index=0; $index < count($student_exams_done); $index++) { 
            $subject_done_unedited = $student_exams_done[$index]->subject_done;
            $subjects_list = explode(",",substr($subject_done_unedited,1,strlen($subject_done_unedited)-2));
            
            // set the subjects values
            $exams_data = new stdClass();
            $exams_data->name = $student_exams_done[$index]->exams_name;
            $exams_data->subjects = [];
            $exams_data->date_done = $student_exams_done[$index]->start_date;
            $exams_data->exams_id = $student_exams_done[$index]->exams_id;

            // add the subject data
            for ($ind=0; $ind < count($subjects_list); $ind++) { 
                $subject_details = new stdClass();
                $subject_details->name = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[0];
                $subject_details->display_name = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[1];
                $subject_details->max_marks = $this->getSubjectDetails($class_subjects,$subjects_list[$ind])[2];
                
                $subject_marks = DB::select("SELECT * FROM `exam_record_tbl` WHERE `exam_id` = ? AND `student_id` = ? AND `subject_id` = ? AND `class name` = ?",[$student_exams_done[$index]->exams_id,$student_adm,$subjects_list[$ind],$student_class]);
                $subject_details->scored_marks = count($subject_marks) > 0 ? $subject_marks[0]->exam_marks : 0;

                // push this data to the exam data subject marks
                array_push($exams_data->subjects,$subject_details);
            }
            // push array
            array_push($exams_results_3,$exams_data);
        }
        
        // END OF TERM THREE

        // get the plottable data for the graphs for term one
        // return $exams_results_1;
        $term_one_plot = [];
        for ($index=0; $index < count($exams_results_1); $index++) {
            $exam_plot = new stdClass();
            $exam_plot->exam_name = $exams_results_1[$index]->name;
            
            // get average marks for subjects
            $all_subjects = $exams_results_1[$index]->subjects;

            // total marks
            $total = 0;
            $counter = 0;
            for ($ind=0; $ind < count($all_subjects); $ind++) { 
                $max_marks = $all_subjects[$ind]->max_marks;
                $scored_marks = $all_subjects[$ind]->scored_marks;

                $total_score = round(($scored_marks/$max_marks) * 100);
                $total+=$total_score;
                $counter++;
            }

            $exams_score = round($total/$counter);
            $exam_plot->exams_score = $exams_score;

            // array added
            array_push($term_one_plot,$exam_plot);
        }
        // get the plottable data for the graphs for term one
        // return $exams_results_1;
        $term_two_plot = [];
        for ($index=0; $index < count($exams_results_2); $index++) {
            $exam_plot = new stdClass();
            $exam_plot->exam_name = $exams_results_2[$index]->name;
            
            // get average marks for subjects
            $all_subjects = $exams_results_2[$index]->subjects;

            // total marks
            $total = 0;
            $counter = 0;
            for ($ind=0; $ind < count($all_subjects); $ind++) { 
                $max_marks = $all_subjects[$ind]->max_marks;
                $scored_marks = $all_subjects[$ind]->scored_marks;

                $total_score = round(($scored_marks/$max_marks) * 100);
                $total+=$total_score;
                $counter++;
            }

            $exams_score = round($total/$counter);
            $exam_plot->exams_score = $exams_score;

            // array added
            array_push($term_two_plot,$exam_plot);
        }
        // get the plottable data for the graphs for term one
        // return $exams_results_1;
        $term_three_plot = [];
        for ($index=0; $index < count($exams_results_3); $index++) {
            $exam_plot = new stdClass();
            $exam_plot->exam_name = $exams_results_3[$index]->name;
            
            // get average marks for subjects
            $all_subjects = $exams_results_3[$index]->subjects;

            // total marks
            $total = 0;
            $counter = 0;
            for ($ind=0; $ind < count($all_subjects); $ind++) { 
                $max_marks = $all_subjects[$ind]->max_marks;
                $scored_marks = $all_subjects[$ind]->scored_marks;

                $total_score = round(($scored_marks/$max_marks) * 100);
                $total+=$total_score;
                $counter++;
            }

            $exams_score = round($total/$counter);
            $exam_plot->exams_score = $exams_score;

            // array added
            array_push($term_three_plot,$exam_plot);
        }

        // return $student_data;
        $parents_notification = $this->getParentsNotification();
        return view("parentPerfomance",["parents_notification" => $parents_notification,"student_data" => $student_data, "term_one_result" => $exams_results_1,"term_two_result" => $exams_results_2,"term_three_result" => $exams_results_3, "term_three_plot" => $term_three_plot, "term_two_plot" => $term_two_plot, "term_one_plot" => $term_one_plot]);
    }
    function getSubjectDetails($subject_list,$subject_id){
        for ($index=0; $index < count($subject_list); $index++) { 
            if ($subject_list[$index]->subject_id == $subject_id) {
                return [$subject_list[$index]->subject_name,$subject_list[$index]->display_name,$subject_list[$index]->max_marks,$subject_list[$index]->grading];
            }
        }
        return ["Not Set","Not Set","Not Set","Not Set"];
    }

    function printResultSlip($exam_id,$adm_no){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the exams result
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // get the student data so that we can get all the subjects taught in their class
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = ?",[$adm_no]);
        // return $student_data;
        if (count($student_data) == 0) {
            session()->flash("invalid","Student details are not present!");
            return redirect("/Parent/Peformance");
        }

        // get the exams if present
        $exams_list = DB::select("SELECT * FROM `exams_tbl` WHERE `exams_id` = '".$exam_id."'");
        // return $exams_list;
        if (count($exams_list) == 0) {
            session()->flash("invalid","Exams is Invalid!");
            return redirect("/Parent/Peformance");
        }

        // get the student results
        $subject_done = explode(",",substr($exams_list[0]->subject_done,1,strlen($exams_list[0]->subject_done)-2));
        
        // get the subject details
        $subject_details = [];
        for ($index=0; $index < count($subject_done); $index++) { 
            $sub_dets = DB::select("SELECT * FROM `table_subject` WHERE `subject_id` = ?",[$subject_done[$index]]);
            if (count($sub_dets) > 0) {
                array_push($subject_details, $sub_dets[0]);
            }else{
                $subject_dets = new stdClass();
                $subject_dets->subject_name = "N/A";
                $subject_dets->display_name = "N/A";
                $subject_dets->subject_id = $subject_done[$index];
                $subject_dets->grading = "[]";
                $subject_dets->max_marks = 100;
                array_push($subject_details,$subject_dets);
            }
        }

        // add the subject marks to the student details
        for ($index=0; $index < count($subject_details); $index++) { 
            // get the marks, if not present set to zero
            $subject_marks = DB::select("SELECT * FROM `exam_record_tbl` WHERE `exam_id` = '".$exam_id."' AND `student_id` = '".$adm_no."' AND `subject_id` = '".$subject_details[$index]->subject_id."'");
            $subject_details[$index]->subject_marks = count($subject_marks) > 0 ? $subject_marks[0]->exam_marks : 0;
            $subject_details[$index]->subject_grade = count($subject_marks) > 0 ? $subject_marks[0]->exam_grade : "N/A";
            $subject_details[$index]->subject_percentage = round(($subject_details[$index]->subject_marks / $subject_details[$index]->max_marks) * 100)."%";
            $subject_details[$index]->teacher_name = $this->getTrTeaching($subject_details[$index]->teachers_id,$student_data[0]->stud_class);
        }
        // return $subject_details;

        $document_title = "Result Slip";
        // create the results slip
        $pdf = new PDF("P","mm","A4");
        $pdf->set_document_title($document_title);
        $pdf->set_document_box(session("school_information")->po_box);
        $pdf->set_document_code(session("school_information")->box_code);
        $pdf->set_school_contact(session("school_information")->school_contact);
        $pdf->set_school_name(session("school_information")->school_name);
        $pdf->setCompayLogo("https://lsims.ladybirdsmis.com/sims/".session("school_information")->school_profile_image);
        $pdf->SetMargins(5, 5);
        $pdf->AddPage();
        $gender = $student_data[0]->gender;
        $full_name = ucwords(strtolower($student_data[0]->first_name." ".$student_data[0]->second_name));
        $admission = $student_data[0]->adm_no;
        $student_class = $this->classNameAdms($student_data[0]->stud_class);

        // STUDENT CLASSTEACHER
        $student_classteacher = "N/A";
        $class_teacher = DB::select("SELECT * FROM `class_teacher_tbl` WHERE `class_assigned` = ?",[$student_data[0]->stud_class]);
        if (count($class_teacher) > 0) {
            DB::setDefaultConnection("mysql");
            $tr_data =DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$class_teacher[0]->class_teacher_id]);
            $student_classteacher = count($tr_data) > 0 ? ucwords(strtolower($tr_data[0]->fullname)) : "N/A";
        }
        DB::setDefaultConnection("mysql2");
        try {
            if ($gender == "Male") {
                $pdf->Image("https://lsims.ladybirdsmis.com/sims/assets/img/male.jpg", 120, 38, 25);
            } else {
                $pdf->Image("https://lsims.ladybirdsmis.com/sims/assets/img/female.png", 120, 38, 25);
            }
            // try setting that image if not set the image
        } catch (Exception $e) {
            // echo $e->getMessage();
            if ($gender == "Male") {
                $pdf->Image("https://lsims.ladybirdsmis.com/sims/assets/img/male.jpg", 120, 38, 25);
            } else {
                $pdf->Image("https://lsims.ladybirdsmis.com/sims/assets/img/female.png", 120, 38, 25);
            }
        }
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(30, 5, "Name: ", 0, 0, 'L', false);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(60, 5, ucwords(strtolower($full_name)), 'R', 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(30, 5, "Reg No.: ", 0, 0, 'L', false);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(60, 5, $admission, 'R', 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(30, 5, "Class: ", 0, 0, 'L', false);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(60, 5, $student_class, 'R', 0, 'L', false);
        $pdf->Ln();
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(30, 5, "Exam Name: ", 0, 0, 'L', false);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(60, 5, ucwords(strtolower($exams_list[0]->exams_name)), 'R', 0, 'L', false);
        $pdf->Ln();
        // $pdf->Cell(30, 5, "Position: ", 0, 0, 'L', false);
        // $pdf->Cell(60, 5, "7", 'R', 0, 'L', false);
        // $pdf->Ln();
        $pdf->SetFont('Helvetica', 'B', 9);
        $pdf->Cell(30, 5, "Class Teacher: ", 0, 0, 'L', false);
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->Cell(60, 5, ucwords(strtolower($student_classteacher)), 'R', 0, 'L', false);
        $pdf->Ln();
        $pdf->Ln(5);
        $pdf->SetFont('Helvetica', 'U', 12);
        $pdf->Cell(190, 5, "Subject Scores", 0, 0, 'C', false);
        $pdf->Ln(10);
        $pdf->SetFont('Times', 'B', 10);
        // Colors, line width and bold font
        $pdf->SetFillColor(191, 191, 191);
        // $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(.1);
        $pdf->Cell(10, 7, "No.", 1, 0, 'C', true);
        $pdf->Cell(45, 7, "Subject Name", 1, 0, 'C', true);
        $pdf->Cell(45, 7, "Subject Scores", 1, 0, 'C', true);
        $pdf->Cell(45, 7, "Subject Grade", 1, 0, 'C', true);
        $pdf->Cell(45, 7, "Tutor", 1, 0, 'C', true);
        $pdf->Ln();
        $fill = false;
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Helvetica', '', 8);
        for ($index2 = 0; $index2 < count($subject_details); $index2++) {
            $pdf->Cell(10, 7, ($index2 + 1), 1, 0, 'C', $fill);
            $pdf->Cell(45, 7, $subject_details[$index2]->subject_name, 1, 0, 'L', $fill);
            $pdf->Cell(45, 7, $subject_details[$index2]->subject_marks." Out Of ".$subject_details[$index2]->max_marks." (".$subject_details[$index2]->subject_percentage.")", 1, 0, 'L', $fill);
            $pdf->Cell(45, 7, $subject_details[$index2]->subject_grade, 1, 0, 'L', $fill);
            $pdf->Cell(45, 7, $subject_details[$index2]->teacher_name, 1, 0, 'L', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        // 
        $pdf->Ln(10);
        $pdf->SetFont('Helvetica', 'U', 10);
        $pdf->Cell(30, 5, "Class Teacher Remarks", 0, 1, 'L', false);
        $pdf->Cell(190, 5, "", 'B', 1, 'C', false);
        $pdf->Cell(190, 5, "", 'B', 1, 'C', false);
        $pdf->Cell(100, 5, "", 'B', 1, 'C', false);
        // CLASS TEACHER SIGNATURE
        $pdf->Ln(5);
        $pdf->Cell(30, 5, "Class Teacher Signature", 0, 1, 'L', false);
        $pdf->Cell(60, 10, "", 'B', 1, 'C', false);
        $pdf->Ln(5);
        $pdf->Cell(50, 5, "Head Teacher / Principal Signature", 0, 1, 'L', false);
        $pdf->Cell(60, 10, "", 'B', 1, 'C', false);
        $pdf->Image("https://lsims.ladybirdsmis.com/sims/images/reports2.png", 1, 1, 209);
        // break;
        $pdf->Output();
    }

    function getStudentProfile($student_adm){
        if (session("school_information") == null) {
            return redirect("/");
        }
        // get the exams result
        $database_name = session("school_information")->database_name;
        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
        config(['database.connections.mysql2.database' => $database_name]);
        DB::setDefaultConnection("mysql2");

        // student data
        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = '".$student_adm."'");
        if (count($student_data) == 0) {
            // add the student information
            session()->flash("invalid","Student data not found!");
            return redirect("/Parent/Dashboard");
        }

        // classteacher
        $class_teacher = "N/A";
        $teacher_data = DB::select("SELECT * FROM `class_teacher_tbl` WHERE `class_assigned` = ?",[$student_data[0]->stud_class]);
        if (count($teacher_data) > 0) {
            $class_teacher_id = $teacher_data[0]->class_teacher_id;
            DB::setDefaultConnection("mysql");

            // teacher data
            $teacher_infor = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = ?",[$class_teacher_id]);

            // count status
            $class_teacher = count($teacher_infor) > 0 ? ucwords(strtolower($teacher_infor[0]->fullname)) : "N/A";
        }

        // get the student fees balance
        DB::setDefaultConnection("mysql2");
        $term = $this->getTermV2();
        // loop through the students and add the student balance
        // get student data
        $balance = $this->calculatedBalanceReport($student_adm,$term);
        // return $balance;
        
        $student_data[0]->stud_balance = number_format($balance);
        
        // student present stats;
        $student_data[0]->present_stats = $this->presentStats($student_data[0]->adm_no,$student_data[0]->stud_class);
        // return $student_data[0]->present_stats;
        // get the student data
        $parents_notification = $this->getParentsNotification();
        return view("parentStudentProfile",["class_teacher" => $class_teacher, "parents_notification" => $parents_notification, "student_data" => $student_data[0]]);
    }

    function getTrTeaching($classlist,$class_name){
        if (session("school_information") == null) {
            return redirect("/");
        }
        if (strlen($classlist) == 0) {
            return "N/A";
        }
        
        // CLASSLIST (1:Grade 1)|(1:7)|(1:8)|(2:6)
        // SPLIT USING THE "|"
        $array_explode = explode("|",$classlist);
        DB::setDefaultConnection("mysql");

        // loop through the new array and get the class
        $staff_name = "N/A";
        for ($index=0; $index < count($array_explode); $index++) {
            $new_class = ":".$class_name.")";
            if (strpos($array_explode[$index],$new_class)) {
                // get the teachers name (1:Grade 1)
                $new_string = substr($array_explode[$index],1,(strlen($array_explode[$index])-2));
                $ids = explode(":",$new_string);

                // get the staff details
                $get_staff = DB::select("SELECT * FROM `user_tbl` WHERE `user_id` = '".$ids[0]."'");
                $staff_name = count($get_staff) > 0 ? ($get_staff[0]->gender == "M" ? "Mr" : "Ms").". ".ucwords(strtolower($get_staff[0]->fullname)) : "N/A";
                break;
            }
        }
        DB::setDefaultConnection("mysql2");
        return $staff_name;
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

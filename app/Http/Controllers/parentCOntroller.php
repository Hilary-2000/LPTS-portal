<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class parentCOntroller extends Controller
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
    function getParentsNotification(){
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
            $active_exams = DB::select("SELECT * FROM `exams_tbl` WHERE `start_date` >= ? AND `end_date` <= ?",[$today,$today]);

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
}

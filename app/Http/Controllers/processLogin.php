<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class processLogin extends Controller
{
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
    function byPassLogin(Request $req){
        // get all schools
        $school_details = DB::select("SELECT * FROM `school_information`");
        $all_schools = [];
        for ($index=0; $index < count($school_details); $index++) { 
            $student_data = [];
            array_push($student_data,ucwords(strtolower($school_details[$index]->school_name)));
            array_push($student_data,ucwords(strtolower($school_details[$index]->school_code)));

            // add school data
            array_push($all_schools,$student_data);
        }

        // return $all_schools;
        // return $_COOKIE;
        if (count($_COOKIE) > 0) {
            $yourUserType = $this->encryptCode("yourUserType");
            $yourSchoolCode = $this->encryptCode("yourSchoolCode");
            $username = $this->encryptCode("username");
            $password = $this->encryptCode("password");

            // loop through the cookies
            $present = 0;
            $usernames = "";
            $school_code = "";
            $usertype = "";
            $passwords = "";
            foreach ($_COOKIE as $key => $value) {
                if ($this->decryptcode($key) == $yourUserType || $this->decryptcode($key) == $yourSchoolCode ||$this->decryptcode($key) == $username ||$this->decryptcode($key) == $password) {
                    $present++;
                    if ($this->decryptcode($key) == $yourUserType) {
                        $usertype = $value;
                    }
                    if ($this->decryptcode($key) == $yourSchoolCode) {
                        $school_code = $value;
                    }
                    if ($this->decryptcode($key) == $username) {
                        $usernames = $value;
                    }
                    if ($this->decryptcode($key) == $password) {
                        $passwords = $value;
                    }
                }
            }
            // return $present;
            if ($present>0) {
                $yourUserType = $usertype;
                // return $yourUserType;
                $yourSchoolCode = $school_code;
                $username = $usernames;
                $password = $passwords;

                // autheniticate and let the user in
                // connect to the default database and get the school information
                $school_information = DB::select("SELECT * FROM `school_information` WHERE `school_code` = ?", [$yourSchoolCode]);
                // return $school_information;

                if (count($school_information) > 0) {
                    // store the school information as a session
                    session()->put("school_information", $school_information[0]);

                    // if they are allowed to use the LPTS_Portal
                    $LPTS_portal_access = $school_information[0]->LPTS_portal_access;
                    if ($LPTS_portal_access == 0) {
                        if ($yourUserType == "Student") {
                            session()->flash("login_error", "You are currently not allowed to access the portal! Contact your teacher for more information");
                        } elseif ($yourUserType == "Teacher") {
                            session()->flash("login_error", "You are currently not allowed to access the portal! Contact your Administrator for more information");
                        } elseif ($yourUserType == "Parent") {
                            session()->flash("login_error", "You are currently not allowed to access the portal! Contact your Teacher for more information");
                        } else {
                            session()->flash("login_error", "Wrong School Code!:Contact your administrator for further assistance!");
                        }
                        return view("login", ["all_schools" => $all_schools]);
                    }

                    // authenticate the user credentials
                    // start by the teacher
                    // return $yourUserType;
                    if ($yourUserType == "Student") {
                        // get the school database
                        $database_name = session("school_information")->database_name;

                        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
                        config(['database.connections.mysql2.database' => $database_name]);

                        DB::setDefaultConnection("mysql2");
                        $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = '".$username."'");
                        if (count($student_data) > 0) {
                            session()->put("student_information",$student_data[0]);
                            // get the student password if its set
                            // if not redirect them to a login page
                            $portal_password = $this->encryptCode($password);
                            $existing_password = $student_data[0]->portal_password;
                            if ($existing_password == "null") {
                                // redirect them to the reset password window
                                return redirect("/Student/ResetPassword");
                            }else{
                                // redirect them to their dashboard
                                if ($portal_password == $student_data[0]->portal_password) {
                                    return redirect("/Student/Dashboard");
                                }else{
                                    // session()->flash("login_error","You have provided an Password! Kindly try again");
                                    return view("login", ["all_schools" => $all_schools]);
                                }
                            }
                        }else{
                            session()->flash("login_error","You have provided an incorrect username! Use your admission number and ensure its spelt correctly");
                            return view("login", ["all_schools" => $all_schools]);
                        }
                    }elseif ($yourUserType == "Teacher"){
                        $portal_password = $this->encryptCode($password);

                        // select from the database the user information if the password is correct
                        $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `username` = ? AND `password` = ?",[$username,$portal_password]);
                        
                        // if the user data is present proceed and redirect them to the teacher dashboard
                        if (count($user_data) > 0) {
                            session()->put("staff_infor",$user_data[0]);
                            session()->flash("login_success","Your session has been successfully restored!");
                            return redirect("/Teacher/Dashboard");
                        }else {
                            session()->flash("login_error","Your credentials are incorrect! Kindly try again");
                            return view("login", ["all_schools" => $all_schools]);
                        }
                    }elseif ($yourUserType == "Parent") {
                        $portal_password = $this->encryptCode($password);
                        // get the school database
                        $database_name = session("school_information")->database_name;

                        // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
                        config(['database.connections.mysql2.database' => $database_name]);

                        DB::setDefaultConnection("mysql2");

                        // get the parents details
                        $select = DB::select("SELECT * FROM `student_data` WHERE `parentContacts` = ? OR `parent_contact2` = ? ORDER BY `ids` ASC",[$username,$username]);
                        
                        if (count($select) > 0) {
                            // authenticate the password
                            // first check their role on the student either primary or secondary parent
                            if($select[0]->parentContacts == $username){
                                $password = $select[0]->primary_parent_password;
                                if ($password == "null") {
                                    $parent_data = [
                                        "parent_name" => $select[0]->parentName,
                                        "parent_contact" => $select[0]->parentContacts,
                                        "parent_relationship" => $select[0]->parent_relation,
                                        "parent_email" => $select[0]->parent_email
                                    ];
                                    // return $parent_data;
                                    session()->put("parents_data",$parent_data);

                                    // proceed to the reset password page
                                    return redirect("/Parent/ResetPassword");
                                }elseif ($password == $portal_password) {
                                    // store the parents data
                                    $parent_data = [
                                        "parent_name" => $select[0]->parentName,
                                        "parent_contact" => $select[0]->parentContacts,
                                        "parent_relationship" => $select[0]->parent_relation,
                                        "parent_email" => $select[0]->parent_email
                                    ];
                                    // return $parent_data;
                                    session()->put("parents_data",$parent_data);
                                    // proceed to the user dashboard
                                    return redirect("/Parent/Dashboard");
                                }else {
                                    session()->flash("login_error","Invalid User, Kindly contact your administrator!");
                                    return view("login", ["all_schools" => $all_schools]);
                                }
                            }elseif ($select[0]->parent_contact2 == $username) {
                                $password = $select[0]->secondary_parent_password;
                                if ($password == "null") {
                                    $parent_data = [
                                        "parent_name" => $select[0]->parent_name2,
                                        "parent_contact" => $select[0]->parent_contact2,
                                        "parent_relationship" => $select[0]->parent_relation2,
                                        "parent_email" => $select[0]->parent_email2
                                    ];
                                    // return $parent_data;
                                    session()->put("parents_data",$parent_data);

                                    // proceed to the reset password page
                                    return redirect("/Parent/ResetPassword");
                                }elseif ($password == $portal_password) {
                                    // store the parents data
                                    $parent_data = [
                                        "parent_name" => $select[0]->parent_name2,
                                        "parent_contact" => $select[0]->parent_contact2,
                                        "parent_relationship" => $select[0]->parent_relation2,
                                        "parent_email" => $select[0]->parent_email2
                                    ];
                                    // return $parent_data;
                                    session()->put("parents_data",$parent_data);
                                    // proceed to the user dashboard
                                    return redirect("/Parent/Dashboard");
                                }else {
                                    session()->flash("login_error","Invalid User, Kindly contact your administrator!");
                                    return view("login", ["all_schools" => $all_schools]);
                                }
                            }else{
                                session()->flash("login_error","Invalid User, Kindly contact your administrator!");
                                return view("login", ["all_schools" => $all_schools]);
                            }
                            DB::setDefaultConnection("mysql");
                        }else {
                            session()->flash("login_error","Invalid User, Kindly contact your administrator!");
                            return view("login", ["all_schools" => $all_schools]);
                        }
                    }else {
                        session()->flash("login_error","Select user type before proceeding!");
                        return view("login", ["all_schools" => $all_schools]);
                    }
                } else {
                    if ($yourUserType == "Student") {
                        session()->flash("login_error", "Wrong School Code!:Contact your teacher for the correct school code!");
                    } elseif ($yourUserType == "Teacher") {
                        session()->flash("login_error", "Wrong School Code!:Contact your administrator for the correct school code!");
                    } elseif ($yourUserType == "Parent") {
                        session()->flash("login_error", "Wrong School Code!:Contact your teacher for the correct school code!");
                    } else {
                        session()->flash("login_error", "Wrong School Code!:Contact your administrator for further assistance!");
                    }
                    return view("login", ["all_schools" => $all_schools]);
                }
            }else {
                return view("login", ["all_schools" => $all_schools]);
            }
        }else {
            session()->flash("login_error", "Wrong School Code!:Contact your administrator for further assistance!");
            return view("login", ["all_schools" => $all_schools]);
        }
    }
    function logout(Request $req){
        $yourUserType = $this->encryptCode("yourUserType");
        $yourSchoolCode = $this->encryptCode("yourSchoolCode");
        $username = $this->encryptCode("username");
        $password = $this->encryptCode("password");

        // delete cookies
        setcookie($yourUserType,"",time() - (3600));
        setcookie($yourSchoolCode,"",time() - (3600));
        setcookie($username,"",time() - (3600));
        setcookie($password,"",time() - (3600));

        session()->flash("school_information");
        session()->flash("student_information");
        session()->flash("staff_infor");
        session()->flash("parents_data");

        return redirect("/Login");
    }
    //process login function
    function procLogin(Request $req)
    {
        $yourUserType = $req->input("yourUserType");
        $yourSchoolCode = $req->input("yourSchoolCode");
        $username = $req->input("username");
        $password = $req->input("password");
        $remember = $req->input("remember");

        session()->flash('yourSchoolCode',$yourSchoolCode);
        session()->flash('username',$username);
        session()->flash('yourUserType',$yourUserType);
        session()->flash('remember',$remember);

        if ($remember == true) {
            $encode_username = $this->encryptCode("username");
            $encode_yourUserType = $this->encryptCode("yourUserType");
            $encode_yourSchoolCode = $this->encryptCode("yourSchoolCode");
            $encode_password = $this->encryptCode("password");

            setcookie($encode_username,$username,time() + (86400 * 7),"/");
            setcookie($encode_yourUserType,$yourUserType,time() + (86400 * 7),"/");
            setcookie($encode_yourSchoolCode,$yourSchoolCode,time() + (86400 * 7),"/");
            setcookie($encode_password,$password,time() + (86400 * 7),"/");
        }

        // connect to the default database and get the school information
        $school_information = DB::select("SELECT * FROM `school_information` WHERE `school_code` = ?", [$yourSchoolCode]);
        // return $school_information;

        if (count($school_information) > 0) {
            // store the school information as a session
            session()->put("school_information", $school_information[0]);

            // if they are allowed to use the LPTS_Portal
            $LPTS_portal_access = $school_information[0]->LPTS_portal_access;
            if ($LPTS_portal_access == 0) {
                if ($yourUserType == "Student") {
                    session()->flash("login_error", "You are currently not allowed to access the portal! Contact your teacher for more information");
                } elseif ($yourUserType == "Teacher") {
                    session()->flash("login_error", "You are currently not allowed to access the portal! Contact your Administrator for more information");
                } elseif ($yourUserType == "Parent") {
                    session()->flash("login_error", "You are currently not allowed to access the portal! Contact your Teacher for more information");
                } else {
                    session()->flash("login_error", "Wrong School Code!:Contact your administrator for further assistance!");
                }
                return redirect("/Login");
            }

            // authenticate the user credentials
            // start by the teacher
            // return $yourUserType;
            if ($yourUserType == "Student") {
                // get the school database
                $database_name = session("school_information")->database_name;

                // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
                config(['database.connections.mysql2.database' => $database_name]);

                DB::setDefaultConnection("mysql2");
                $student_data = DB::select("SELECT * FROM `student_data` WHERE `adm_no` = '".$username."'");
                if (count($student_data) > 0) {
                    session()->put("student_information",$student_data[0]);
                    // get the student password if its set
                    // if not redirect them to a login page
                    $portal_password = $this->encryptCode($password);
                    $existing_password = $student_data[0]->portal_password;
                    if ($existing_password == "null") {
                        // redirect them to the reset password window
                        return redirect("/Student/ResetPassword");
                    }else{
                        // redirect them to their dashboard
                        if ($portal_password == $student_data[0]->portal_password) {
                            return redirect("/Student/Dashboard");
                        }else{
                            session()->flash("login_error","You have provided an Password! Kindly try again");
                            return redirect("/Login");
                        }
                    }
                }else{
                    session()->flash("login_error","You have provided an incorrect username! Use your admission number and ensure its spelt correctly");
                    return redirect("/Login");
                }
            }elseif ($yourUserType == "Teacher"){
                $portal_password = $this->encryptCode($password);

                // select from the database the user information if the password is correct
                $user_data = DB::select("SELECT * FROM `user_tbl` WHERE `username` = ? AND `password` = ?",[$username,$portal_password]);
                
                // if the user data is present proceed and redirect them to the teacher dashboard
                if (count($user_data) > 0) {
                    session()->put("staff_infor",$user_data[0]);
                    return redirect("/Teacher/Dashboard");
                }else {
                    session()->flash("login_error","Your credentials are incorrect! Kindly try again");
                    return redirect("/Login");
                }
            }elseif ($yourUserType == "Parent") {
                // return $school_information;
                $portal_password = $this->encryptCode($password);
                // get the school database
                $database_name = session("school_information")->database_name;

                // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
                config(['database.connections.mysql2.database' => $database_name]);

                DB::setDefaultConnection("mysql2");

                // get the parents details
                $select = DB::select("SELECT * FROM `student_data` WHERE `parentContacts` = ? OR `parent_contact2` = ?",[$username,$username]);
                
                if (count($select) > 0) {
                    // authenticate the password
                    // first check their role on the student either primary or secondary parent
                    if($select[0]->parentContacts == $username){
                        $password = $select[0]->primary_parent_password;
                        if ($password == "null") {
                            $parent_data = [
                                "parent_name" => $select[0]->parentName,
                                "parent_contact" => $select[0]->parentContacts,
                                "parent_relationship" => $select[0]->parent_relation,
                                "parent_email" => $select[0]->parent_email
                            ];
                            // return $parent_data;
                            session()->put("parents_data",$parent_data);

                            // proceed to the reset password page
                            return redirect("/Parent/ResetPassword");
                        }elseif ($password == $portal_password) {
                            // store the parents data
                            $parent_data = [
                                "parent_name" => $select[0]->parentName,
                                "parent_contact" => $select[0]->parentContacts,
                                "parent_relationship" => $select[0]->parent_relation,
                                "parent_email" => $select[0]->parent_email
                            ];
                            // return $parent_data;
                            session()->put("parents_data",$parent_data);
                            // proceed to the user dashboard
                            return redirect("/Parent/Dashboard");
                        }else {
                            session()->flash("login_error","Invalid Password, Kindly contact your administrator to help you reset it!");
                            return redirect("/Login");
                        }
                    }elseif ($select[0]->parent_contact2 == $username) {
                        $password = $select[0]->secondary_parent_password;
                        if ($password == "null") {
                            $parent_data = [
                                "parent_name" => $select[0]->parent_name2,
                                "parent_contact" => $select[0]->parent_contact2,
                                "parent_relationship" => $select[0]->parent_relation2,
                                "parent_email" => $select[0]->parent_email2
                            ];
                            // return $parent_data;
                            session()->put("parents_data",$parent_data);

                            // proceed to the reset password page
                            return redirect("/Parent/ResetPassword");
                        }elseif ($password == $portal_password) {
                            // store the parents data
                            $parent_data = [
                                "parent_name" => $select[0]->parent_name2,
                                "parent_contact" => $select[0]->parent_contact2,
                                "parent_relationship" => $select[0]->parent_relation2,
                                "parent_email" => $select[0]->parent_email2
                            ];
                            // return $parent_data;
                            session()->put("parents_data",$parent_data);
                            // proceed to the user dashboard
                            return redirect("/Parent/Dashboard");
                        }else {
                            session()->flash("login_error","Invalid User, Kindly contact your administrator!");
                            return redirect("/Login");
                        }
                    }else{
                        session()->flash("login_error","Invalid User, Kindly contact your administrator!");
                        return redirect("/Login");
                    }
                    DB::setDefaultConnection("mysql");
                }else {
                    session()->flash("login_error","Invalid User, Kindly contact your administrator!");
                    return redirect("/Login");
                }
            }else {
                session()->flash("login_error","Select user type before proceeding!");
                return redirect("/Login");
            }
        } else {
            if ($yourUserType == "Student") {
                session()->flash("login_error", "Wrong School Code!:Contact your teacher for the correct school code!");
            } elseif ($yourUserType == "Teacher") {
                session()->flash("login_error", "Wrong School Code!:Contact your administrator for the correct school code!");
            } elseif ($yourUserType == "Parent") {
                session()->flash("login_error", "Wrong School Code!:Contact your teacher for the correct school code!");
            } else {
                session()->flash("login_error", "Wrong School Code!:Contact your administrator for further assistance!");
            }
            return redirect("/Login");
        }
    }
    function createParentPassword(Request $req){
        // update the primary parent password if the username of the user is primary
        $yourPassword = $req->input("yourPassword");
        $confirmPassword = $req->input("confirmPassword");

        if ($yourPassword == $confirmPassword) {
            session()->flash("yourPassword",$yourPassword);
            session()->flash("confirmPassword",$confirmPassword);
    
            // get the school database
            $database_name = session("school_information")->database_name;
    
            // SET THE DATABASE NAME AS PER THE STUDENT ADMISSION NO
            config(['database.connections.mysql2.database' => $database_name]);
    
            DB::setDefaultConnection("mysql2");
    
            // UPDATE THE USER PASSWORD
            $parents_data = session("parents_data");
            $new_password = $this->encryptCode($yourPassword);
            $update = DB::select("UPDATE `student_data` SET `primary_parent_password` = ? WHERE `parentContacts` = ?",[$new_password,$parents_data['parent_contact']]);
            $update = DB::select("UPDATE `student_data` SET `primary_parent_password` = ? WHERE `parentContacts` = ?",[$new_password,$parents_data['parent_contact']]);
    
            DB::setDefaultConnection("mysql");
    
            return redirect("/Parent/Dashboard");
        }else{
            session()->flash("login_error", "Passwords don`t match!");
            return redirect("/Parent/ResetPassword");
        }
    }
    function createPasswords(Request $req){
        $new_password = $req->input("yourPassword");
        $confirm_password = $req->input("confirmPassword");

        session()->flash("yourPassword",$new_password);
        session()->flash("confirmPassword",$confirm_password);

        if ($new_password == $confirm_password) {
            // configure database
            $database_name = session("school_information")->database_name;
            config(['database.connections.mysql2.database' => $database_name]);
            DB::setDefaultConnection("mysql2");

            $new_password = $this->encryptCode($new_password);
            // update the students password
            $stud_adm_no = session("student_information")->adm_no;
            $update = DB::update("UPDATE `student_data` SET `portal_password` = ? WHERE `adm_no` = ?",[$new_password,$stud_adm_no]);
            // return $update;
            
            if ($update) {
                session()->flash("login_success","Passwords change successfully!");
                return redirect("/Student/Dashboard");
            }else{
                session()->flash("login_error","An error occured!");
                return redirect("/Student/ResetPassword");
            }

            DB::setDefaultConnection("mysql");
        }else{
            session()->flash("login_error","Your passwords don`t match!");
            return redirect("/Student/ResetPassword");
        }
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

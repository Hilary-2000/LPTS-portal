<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Assignments -
        {{ session('student_information') != null ? ucwords(strtolower(session('student_information')->first_name))." ".ucwords(strtolower(session('student_information')->second_name)) : '' }}
    </title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" rel="icon">
    <link href="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="/assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="/assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="/assets/css/style.css" rel="stylesheet">

    <!-- =======================================================
  * Template Name: NiceAdmin - v2.2.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>
@php
    date_default_timezone_set('Africa/Nairobi');
@endphp
<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="." class=" d-flex align-items-center">
                <b
                    class="d-none d-sm-block text-sm">{{ session('school_information') != null ? session('school_information')->school_name : 'REMNANT VISION ACADEMY SCHOOLS SCHOOL' }}</b>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        {{-- <p class="text-success text-center p-2">Password set successfully!</p> --}}

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li><!-- End Search Icon-->

                <li class="nav-item dropdown">

                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-primary badge-number">{{count($student_notification)}}</span>
                    </a><!-- End Notification Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            You have {{count($student_notification)}} new notifications
                            <a href="/Students/Messages"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                        </li>
                        @php
                            function getTimeAgo($date) {
                                $currentTimestamp = time();
                                $timestamp = strtotime($date);
                                $difference = $currentTimestamp - $timestamp;
                                
                                if ($difference < 60) {
                                    return $difference . " seconds ago";
                                } elseif ($difference < 3600) {
                                    $minutes = floor($difference / 60);
                                    return $minutes . " minutes ago";
                                } elseif ($difference < 86400) {
                                    $hours = floor($difference / 3600);
                                    return $hours . " hours ago";
                                } elseif ($difference < 2592000) {
                                    $days = floor($difference / 86400);
                                    return $days . " days ago";
                                } elseif ($difference < 31536000) {
                                    $months = floor($difference / 2592000);
                                    return $months . " months ago";
                                } else {
                                    $years = floor($difference / 31536000);
                                    return $years . " years ago";
                                }
                            }
                            function getInnerText($html) {
                                // Remove any HTML tags and entities
                                $text = strip_tags($html);
                                
                                // Decode HTML entities
                                $text = html_entity_decode($text);
                                
                                // Normalize whitespace
                                $text = preg_replace('/\s+/', ' ', $text);
                                
                                // Trim leading and trailing whitespace
                                $text = trim($text);
                                
                                return $text;
                            }
                        @endphp
                        @for ($i = 0; $i < count($student_notification); $i++)
                            <li class="notification-item">
                                <i class="bi bi-bell text-primary"></i>
                                <a href="/Student/Alert/Read/{{$student_notification[$i]->id}}">
                                    <h4 class="text-dark">{{$student_notification[$i]->message_title}}</h4>
                                    <p>{{strlen(getInnerText($student_notification[$i]->message_body)) > 50 ? substr(getInnerText($student_notification[$i]->message_body),0,50)."..." : getInnerText($student_notification[$i]->message_body)}}</p>
                                    <p>{{getTimeAgo($student_notification[$i]->date_created)}}</p>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @php
                                if ($i == 4) {
                                    break;
                                }
                            @endphp
                        @endfor
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-footer">
                            <a href="/Students/Messages">Show all notifications</a>
                        </li>
                    </ul><!-- End Notification Dropdown Items -->

                </li><!-- End Notification Nav -->

                <li class="nav-item dropdown">

                    <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-chat-left-text"></i>
                        <span class="badge bg-success badge-number">0</span>
                    </a><!-- End Messages Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
                        <li class="dropdown-header">
                            You have 0 new messages
                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-footer">
                            <a href="#">View all messages</a>
                        </li>

                    </ul><!-- End Messages Dropdown Items -->

                </li><!-- End Messages Nav -->

                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#"
                        data-bs-toggle="dropdown">
                        <img src="{{ (session('student_information') != null && session('student_information')->student_image != null) ? 'https://lsims.ladybirdsmis.com/sims/'.session('student_information')->student_image : '/assets/img/dp.png' }}"
                            alt="Profile" class="rounded-circle">
                        <span
                            class="d-none d-md-block dropdown-toggle ps-2">{{ session('student_information') != null ? ucwords(strtolower(session('student_information')->first_name))." ".ucwords(strtolower(session('student_information')->second_name)) : '' }}</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ session('student_information') != null ? ucwords(strtolower(session('student_information')->first_name))." ".ucwords(strtolower(session('student_information')->second_name)) : '' }}
                            </h6>
                            <span>Student</span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="/Logout">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>
                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Student/Dashboard">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="/Student/CourseMaterial">
                    <i class="bi bi-book-half"></i>
                    <span>Course Materials</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link " href="/Students/Assignment">
                    <i class="bi bi-grid"></i>
                    <span>Assignment</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Students/Messages">
                    <i class="bi bi-grid"></i>
                    <span>Messages & alerts</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Students/DiscussionForum">
                    <i class="bi bi-grid"></i>
                    <span>Discussion Forums</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-heading">Pages</li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Student/Profile">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li><!-- End Profile Page Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Logout">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Logout</span>
                </a>
            </li><!-- End Login Page Nav -->
        </ul>

    </aside><!-- End Sidebar-->

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Assignments</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Student/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item active">Assignments</li>
                </ol>
            </nav>
            <p class="text-success">{{ session('strand_success') != null ? session('strand_success') : '' }}</p>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>Select the subject you want to do assignments.</li>
                                    <li>If one of the subjects that you are being taught does not appear, Ask your teacher!</li>
                                    <li>All the recent assignments are under the active assignments section.</li>
                                    <li>The tables will only show the assignments done this academic Year</li>
                                    <li>Goodluck 😁</li>
                                </ul>
                            </div>
                        </div>
                        @php
                            function isJson($string) {
                                return ((is_string($string) &&
                                        (is_object(json_decode($string)) ||
                                        is_array(json_decode($string))))) ? true : false;
                            }
                        @endphp
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <h5 class="card-title">Active Assignments Class: {{session("student_information")->stud_class}} <span></span></h5>
                                    <p class="text-danger">{{session("invalid") != null?session("invalid") : ""}}</p>
                                    <p class="text-success">{{session("valid") != null?session("valid") : ""}}</p>
                                    @php
                                        // function to check if the student has done the assignment
                                        function isDone($answers){
                                            $present = false;

                                            // check if the days done is passed
                                            $my_answers = $answers->answers;

                                            // check if today is between the period
                                            if (isJson($answers->period)) {
                                                // echo $answers->period;
                                                $my_periods = json_decode($answers->period);
                                                if (date("Y-m-d") >= $my_periods->start_date && date("Y-m-d") <= $my_periods->end_date) {
                                                    
                                                }else {
                                                    return true;
                                                }
                                            }

                                            if (isJson($my_answers)) {
                                                $answers = json_decode($my_answers);
                                                // student id
                                                $student_data = session("student_information");
                                                // decode answers
                                                for ($index=0; $index < count($answers); $index++) { 
                                                    if ($answers[$index]->student_id == $student_data->adm_no) {
                                                        $present = true;
                                                    }
                                                }
                                            }
                                            // return value
                                            return $present;
                                        }

                                        function answerData($answers){
                                            $present = [];

                                            if (isJson($answers)) {
                                                $answers = json_decode($answers);
                                                // student id
                                                $student_data = session("student_information");
                                                // decode answers
                                                for ($index=0; $index < count($answers); $index++) { 
                                                    if ($answers[$index]->student_id == $student_data->adm_no) {
                                                        $present = $answers[$index];
                                                        break;
                                                    }
                                                }
                                            }else{
                                                // $answers = json_decode($answers);
                                                // student id
                                                $student_data = session("student_information");
                                                // decode answers
                                                for ($index=0; $index < count($answers); $index++) { 
                                                    if ($answers[$index]->student_id == $student_data->adm_no) {
                                                        $present = $answers[$index];
                                                        break;
                                                    }
                                                }
                                            }
                                            // return value
                                            return $present;
                                        }
                                    @endphp
                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Assignment Name</th>
                                                <th scope="col">Subject</th>
                                                <th scope="col">Period</th>
                                                <th scope="col">Total Questions</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($assignments); $i++)
                                                @if (!isDone($assignments[$i]))
                                                    <th scope='row'><a href='#'>{{ $i + 1 }}</a></th>
                                                    <td>{{$assignments[$i]->name}}</td>
                                                    <td>{{$assignments[$i]->subject_name}}</td>
                                                    <td>{{isJson($assignments[$i]->period) ? date("D dS M Y",strtotime(json_decode($assignments[$i]->period)->start_date))." - ".date("D dS M Y",strtotime(json_decode($assignments[$i]->period)->end_date)) : date("D dS M Y") }}</td>
                                                    <td>{{isJson($assignments[$i]->questions) ? count(json_decode($assignments[$i]->questions))." Question(s)" : "0 Question(s)" }}</td>
                                                    <td><a href="/Student/Assignment/Attempt/{{$assignments[$i]->id}}" class="btn btn-success btn-sm"><i class="bi bi-pen"></i> Attempt</a></td>
                                                @endif
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div><!-- End Recent Sales -->
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <h5 class="card-title">Completed / Closed Assignment Table for Class: {{session("student_information")->stud_class}} <span></span></h5>
                                    <p class="text-danger">{{session("invalid") != null?session("invalid") : ""}}</p>
                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Assignment Name</th>
                                                <th scope="col">Subject</th>
                                                <th scope="col">Date Submitted</th>
                                                <th scope="col">Marks Attained</th>
                                                <th scope="col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($assignments); $i++)
                                                @if (isDone($assignments[$i]))
                                                <tr>
                                                    <th scope='row'><a href='#'>{{ $i + 1 }}</a></th>
                                                    <td style="min-width: 100px;" >{{$assignments[$i]->name}}</td>
                                                    <td style="min-width: 100px;" >{{$assignments[$i]->subject_name}}</td>
                                                    @php
                                                        $my_answers = answerData($assignments[$i]->answers);
                                                        if (isset($my_answers->date_completed) && isset($my_answers->marks_attained)) {
                                                            // echo json_encode($my_answers);
                                                            $date_done = empty($my_answers) ? date("D dS M Y",strtotime($my_answers->date_completed)) : date("D dS M Y");
                                                            $marks_attained = !empty($my_answers) ? $my_answers->marks_attained." Marks" : "0 Marks";
                                                        }else {
                                                            $date_done = "Not Done!";
                                                            $marks_attained = "Not Done!";
                                                        }
                                                    @endphp
                                                    <td style="min-width: 100px;" >{{$date_done}}</td>
                                                    <td style="min-width: 100px;" >
                                                        @if (!empty($my_answers))
                                                            @if ($my_answers->marked)
                                                                {{$marks_attained}}
                                                            @else
                                                                <span class="badge bg-secondary">Not-Marked</span>
                                                            @endif
                                                        @else
                                                            @if ($marks_attained == "Not Done!")
                                                                <span class="badge bg-danger">Not-Done</span>
                                                            @else
                                                                <span class="badge bg-secondary">Not-Marked</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td style="min-width: 100px;" ><a href="/Student/Assignment/ViewDone/{{$assignments[$i]->id}}" class="btn btn-secondary btn-sm my-2"><i class="bi bi-eye"></i> View</a></td>
                                                </tr>
                                                @endif
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div><!-- End Recent Sales -->
                    </div>
                </div><!-- End Left side columns -->
            </div>
        </section>

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="copyright">
            &copy; Copyright <strong><span>Ladybird Softech Co</span></strong>. All Rights Reserved
        </div>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
            {{-- Designed by <a href="https://ladybirdsmis.com/">Ladybird Softech Co.</a> --}}
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>

</body>
</html>
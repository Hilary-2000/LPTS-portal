<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Review Assignments - {{ $assignment->name }} || {{ $subject_name }} -
        {{ session('student_information') != null ? ucwords(strtolower(session('student_information')->first_name)) . ' ' . ucwords(strtolower(session('student_information')->second_name)) : '' }}
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
                            class="d-none d-md-block dropdown-toggle ps-2">{{ session('student_information') != null ? ucwords(strtolower(session('student_information')->first_name)) . ' ' . ucwords(strtolower(session('student_information')->second_name)) : '' }}</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ session('student_information') != null ? ucwords(strtolower(session('student_information')->first_name)) . ' ' . ucwords(strtolower(session('student_information')->second_name)) : '' }}
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
                    <li class="breadcrumb-item"><a href="/Students/Assignment">Assignments</a>
                    <li class="breadcrumb-item active">Assignments - "{{ $assignment->name }}"</li>
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
                                <a href="/Students/Assignment"
                                    class="btn btn-secondary btn-sm my-2"><i class="bi bi-arrow-left"></i> Back to
                                    Assignment Lists</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>At this window you will be able to see your perfomace on
                                        <b>{{ $assignment->name }}</b>.</li>
                                    <li>Incase of any errors in your marks contact your teacher so they can change it!
                                    </li>
                                    <li>Revise your assignment and if you`ve gotten any of them wrong, work on them to
                                        remember that knowledge later.</li>
                                </ul>
                            </div>
                        </div>
                        @php
                            $assignment_decoded = $assignment->answers;
                            $answers = [];
                            
                            if (isJson($assignment_decoded)) {
                                $answers = json_decode($assignment_decoded);
                            }

                            // decode questions
                            $assignment_decoded = $assignment->questions;
                            $questions_selected = [];
                            
                            if (isJson($assignment_decoded)) {
                                $questions_selected = json_decode($assignment_decoded);
                            }

                            function selectedData($my_answers,$question_id){
                                $answers_set = [];
                                $student_data = session("student_information");

                                if (isJson($my_answers)) {
                                    $my_answers = json_decode($my_answers);
                                    for ($index=0; $index < count($my_answers); $index++) { 
                                        if ($my_answers[$index]->student_id == $student_data->adm_no) {
                                            $answer = $my_answers[$index]->answer;
                                            if (isJson($answer)) {
                                                $my_answer = json_decode($answer);
                                                for ($ind=0; $ind < count($my_answer); $ind++) {
                                                    if ($my_answer[$ind]->linked == $question_id) {
                                                        $answers_set = $my_answer[$ind];
                                                        break;
                                                    }
                                                }
                                            }elseif (is_array($answer)) {
                                                $my_answer = ($answer);
                                                for ($ind=0; $ind < count($my_answer); $ind++) {
                                                    if ($my_answer[$ind]->linked == $question_id) {
                                                        $answers_set = $my_answer[$ind];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                return $answers_set;
                            }
                        @endphp
                        @php
                            function isJson($string)
                            {
                                return is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))) ? true : false;
                            }
                            
                            // get the total marks the student has gotten
                            $total_marks = 0;
                            $total_points = 0;
                            for ($i = 0; $i < count($questions_selected); $i++){
                                $total_points += $questions_selected[$i]->points;
                                $this_answer = selectedData($assignment->answers,$questions_selected[$i]->id);
                                // echo $this_answer;
                                $my_marks = isset($this_answer->score) ? $this_answer->score*1 : 0;
                                $total_marks += $my_marks;
                            }
                        @endphp
                    </div>
                </div><!-- End Left side columns -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Assignment Review : "{{ $assignment->name }}" ||
                                {{ $subject_name }}</h6>
                                <p class="col-md-4 mt-2 my-0 p-1 rounded border border-secondary" style="background: rgb(209, 209, 209);" ><b>Total Marks: </b>{{$total_points}} Mks</p>
                                <p class="col-md-4 mb-4 my-0 p-1 rounded border border-secondary" style="background: rgb(209, 209, 209);" ><b>Attained Marks: </b>{{$total_marks}} Mks</p>
                                
                            {{-- from this section all the questions ate going to be dislayed here --}}
                            <div class="container p-0 w-100">
                                @for ($i = 0; $i < count($questions_selected); $i++)
                                    <div class="col-md-11 my-4 border border-secondary p-1 rounded my-1 mx-auto">
                                        @php
                                            $this_answer = selectedData($assignment->answers,$questions_selected[$i]->id);
                                            // echo json_encode($assignment->answers);
                                        @endphp
                                        <div class="row">
                                            <div class="col-md-11">
                                                <h6><b>Q{{$i+1}}</b></h6>
                                            </div>
                                            <div class="col-md-1">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-9">
                                                <p>{{$questions_selected[$i]->quiz}}</p>
                                            </div>
                                            <div class="col-md-3 row">
                                                <p class="text-center col-sm-6 border border-primary my-0 w-75 mx-auto  p-1"><b>Tot Mks:</b> {{$questions_selected[$i]->points}} Mks</p>
                                                <p class="text-center col-sm-6 border border-primary my-0 w-75 mx-auto mb-2 p-1"><b>Att Mks:</b> {{isset($this_answer->score) ? $this_answer->score : "0";}} Mks</p>
                                                
                                            </div>
                                        </div>
                                        @if (isJson($questions_selected[$i]->resources))
                                            <div class="row">
                                                @php
                                                    $resources = json_decode($questions_selected[$i]->resources);
                                                @endphp
                                                @if (count($resources) > 0)
                                                    @for ($ind = 0; $ind < count($resources); $ind++)
                                                        <div class="mx-1 my-1" style="width: 100px; cursor:pointer;"
                                                            data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
                                                            <img src="{{$resources[$ind]->locale}}"
                                                                id="window_locale{{$i}}" class="window_locale my-1 mx-auto"
                                                                alt="" width="90" height="90">
                                                            <span class="text-center">{{$resources[$ind]->name}}</span>
                                                        </div>
                                                    @endfor
                                                @endif
                                            </div>
                                        @endif
                                        <hr class="w-75 mx-auto">
                                        @if (isJson($questions_selected[$i]->choice))
                                            @php
                                                $choices = json_decode($questions_selected[$i]->choice);
                                                $counted = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z']
                                            @endphp

                                            {{-- display choices --}}
                                            @if (count($choices))
                                                <p ><b>Multiple Choices</b></p>
                                                @for ($indexes = 0; $indexes < count($choices); $indexes++)
                                                    <p class="my-1 text-secondary"><b>{{$counted[$indexes]}}.</b> {{$choices[$indexes]->choice}}</p>
                                                @endfor
                                            @endif
                                        @endif
                                        <hr class="my-1">
                                        <p class="my-0"><b>Your Answer</b></p>
                                        <p class="my-0 text-secondary">{{!empty($this_answer) ? $this_answer->answer : "Not Answered"}}</p>
                                        <hr class="my-1">
                                        <p class="my-0"><b>Correct Answer</b></p>
                                        <p class="text-success">{{$questions_selected[$i]->correct_answer == null ? "Not Set" : $questions_selected[$i]->correct_answer}}</p>
                                        <hr class="my-1">
                                        @if (isset($this_answer->review))
                                            @if (strlen($this_answer->review))
                                                <p class="my-0"><b>Teacher Review</b></p>
                                                <p class="my-0 text-primary">{{isset($this_answer->review) ? $this_answer->review : "Not Set";}}</p>
                                            @endif
                                        @endif
                                    </div>
                                @endfor
                            </div>

                              <div class="modal fade" id="ExtralargeModal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="title_image">Not Set</h5>
                                      <input type="hidden" value="0" id="my_ids">
                                      <input type="hidden" value="[]" id="all_images">
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body row">
                                      <div class="col-md-1">
                                          <span class="btn btn-sm btn-primary" id="move_left_inside"><i class="bi bi-arrow-left"></i></span>
                                      </div>
                                      <div class="col-md-10">
                                          <img src="" alt="" id="image_assignments" class="w-100">
                                      </div>
                                      <div class="col-md-1">
                                          <span class="btn btn-sm btn-primary" id="move_right_inside"><i class="bi bi-arrow-right"></i></span>
                                      </div>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                  </div>
                                </div>
                              </div><!-- End Extra Large Modal-->
                        </div>
                    </div>
                </div>
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
    <script>
        function cObj(params) {
            return document.getElementById(params);
        }

        function valObj(params) {
            return document.getElementById(params).value;
        }
        window.onload = function () {
            var window_locale =  document.getElementsByClassName("window_locale");
            for (let index = 0; index < window_locale.length; index++) {
                const element = window_locale[index];
                element.addEventListener("click",showImage);
            }
        }

        function showImage() {
            cObj("image_assignments").src = this.src;
        }
    </script>

</body>

</html>

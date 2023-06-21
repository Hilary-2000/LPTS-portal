<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Create Test - {{ $subject_details->display_name }} - {{ $class_name }} -
        {{ session('staff_infor') != null ? ucwords(strtolower(session('staff_infor')->fullname)) : '' }}
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
                        <span class="badge bg-primary badge-number">{{count($teacher_notifications)}}</span>
                    </a><!-- End Notification Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            You have {{count($teacher_notifications)}} new notifications
                            <a href="/Teacher/Messages"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
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
                        @for ($i = 0; $i < count($teacher_notifications); $i++)
                            <li class="notification-item">
                                <i class="bi bi-bell text-primary"></i>
                                <a href="/Teacher/Alert/Read/{{$teacher_notifications[$i]->id}}">
                                    <h4 class="text-dark">{{$teacher_notifications[$i]->message_title}}</h4>
                                    <p>{{strlen(getInnerText($teacher_notifications[$i]->message_body)) > 50 ? substr(getInnerText($teacher_notifications[$i]->message_body),0,50)."..." : getInnerText($teacher_notifications[$i]->message_body)}}</p>
                                    <p>{{getTimeAgo($teacher_notifications[$i]->date_created)}}</p>
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
                            <a href="/Teacher/Messages">Show all notifications</a>
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
                        <img src="{{ session('staff_infor') != null ? 'https://lsims.ladybirdsmis.com/sims/' . session('staff_infor')->profile_loc : 'assets/img/dp.png' }}"
                            alt="Profile" class="rounded-circle">
                        <span
                            class="d-none d-md-block dropdown-toggle ps-2">{{ session('staff_infor') != null ? ucwords(strtolower(session('staff_infor')->fullname)) : '' }}</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ session('staff_infor') != null ? ucwords(strtolower(session('staff_infor')->fullname)) : '' }}
                            </h6>
                            <span>{{ session('staff_infor')->auth == 0 ? ' Administrator' : (session('staff_infor')->auth == 1 ? 'Headteacher' : (session('staff_infor')->auth == 2 ? 'Deputy principal' : (session('staff_infor')->auth == 3 || session('staff_infor')->auth == 4 ? 'Teacher' : (session('staff_infor')->auth == 5 ? 'Class Teacher' : session('staff_infor')->auth)))) }}</span>
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
                <a class="nav-link collapsed" href="/Teacher/Dashboard">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Teacher/LessonPlan">
                    <i class="bi bi-book-half"></i>
                    <span>Lesson Plan</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link " href="/Teacher/QuestionBank">
                    <i class="bi bi-columns"></i>
                    <span>Question Bank</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Teacher/Assignment">
                    <i class="bi bi-grid"></i>
                    <span>Assignment</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Teacher/Messages">
                    <i class="bi bi-bell"></i>
                    <span>Messages & alerts</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Teacher/DiscussionForum">
                    <i class="bi bi-chat"></i>
                    <span>Discussion Forums</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-heading">Pages</li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Teacher/Profile">
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
            <h1>Create Test - {{ $subject_details->display_name }} - {{ $class_name }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/QuestionBank">Subject I Teach</a></li>
                    <li class="breadcrumb-item"><a
                            href="/Teacher/QuestionBank/{{ $lesson_id }}/Create/{{ $class_id }}">Manage
                            Question Bank</a></li>
                    <li class="breadcrumb-item active">Create Test - {{ $subject_details->display_name }} -
                        {{ $class_name }}</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <a class="btn btn-secondary btn-sm my-2"
                                    href="/Teacher/QuestionBank/{{ $lesson_id }}/Create/{{ $class_id }}"><i
                                        class="bi bi-arrow-left"></i> Manage Question Bank</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>Create Test for {{ $subject_details->display_name }} that will be used to test
                                        the student understanding of this subject.</li>
                                    <li>The test you create will be created as a pdf. You will be able to print it OR
                                        save it and add it as a learning material in the short term plan.</li>
                                    <li>Select the criteria you want to generate the test with from the option below.
                                    </li>
                                    <li>The number of question you set is the maximum number that you will recieve,
                                        incase the criteria you input has less questions you will get less than the set
                                        number.</li>
                                </ul>
                            </div>
                        </div>
                        @php
                            function truncateWord($word, $count)
                            {
                                return strlen($word) > $count ? substr($word, 0, $count) . '...' : $word;
                            }
                        @endphp
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <h5 class="card-title">Create Test for {{ $subject_details->display_name }},
                                        Class: {{ $class_name }}<span></span></h5>
                                    <p class="text-success">
                                        {{ session('successfull_banking') != null ? session('successfull_banking') : '' }}
                                    </p>
                                    <p class="text-danger">
                                        {{ session('unsuccessfull_banking') != null ? session('unsuccessfull_banking') : '' }}
                                    </p>
                                    <hr>
                                    <form method="POST" class="row" target="_blank" action="/createTest/QB" class="form-control">
                                        @csrf
                                        <input type="hidden" name="lesson_id" value="{{ $lesson_id }}">
                                        <input type="hidden" name="class_id" value="{{ $class_id }}">
                                        <div class="form-group col-md-3 mb-2">
                                            <label for="exam_title" class="form-label"><b>Set Exam Title</b></label>
                                            <input type="text" name="exam_title" id="exam_title"
                                                class="form-control" placeholder="Set Exam Title here...">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="question_difficulty" class="form-label"><b>Difficulty
                                                    Level</b></label>
                                            <select required name="question_difficulty" id="question_difficulty"
                                                class="form-control">
                                                <option value="" hidden>Select Sub-Topic</option>
                                                <option value="Random">Random</option>
                                                <option value="Simple">Simple</option>
                                                <option selected value="Normal">Normal</option>
                                                <option value="Hard">Hard</option>
                                                <option value="Extra-Hard">Extra-Hard</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="maximum_questions" class="form-label"><b>Maximum
                                                    Number</b></label>
                                            <input type="number" name="maximum_questions" class="form-control"
                                                id="maximum_questions" placeholder="Set Maximum No. e.g, 10">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="question_topic" class="form-label"><b>Topic Selection
                                                    Criteria</b></label>
                                            <select name="question_topic" required id="question_topic"
                                                class="form-control">
                                                <option value="" hidden>Select Topic</option>
                                                <option selected value="Random">Random</option>
                                                <option value="Select Topics">Select Topics</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="exams_time" class="form-label"><b>Exams time
                                                    Criteria</b></label>
                                            <input type="text" name="exams_time" id="exams_time" class="form-control" placeholder="eg 1hr 40min">
                                        </div>
                                        <div class="col-md-9"></div>
                                        <div class="col-md-8 mx-auto my-2 border border-primary p-2">
                                            <h6 class="text-center">Instructions</h6>
                                            <input type="hidden" value="[]" name="my_instructions" id="my_instructions">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="set_instructions" class="form-label"><b>Set Instruction</b></label>
                                                    <input type="text" id="set_instructions"
                                                        class="form-control" placeholder="Set Instructions (One by One)">
                                                    <button type="button" class="btn btn-primary my-1" id="set_instructions_btn"><i class="bi bi-plus"></i> Add</button>
                                                </div>
                                                <div class="col-md-8">
                                                    <label for="" class="form-label"><b>Instructions</b></label>
                                                    <ul class="list-group" id="display_objectives">
                                                        <li class="list-group-item">1. Stop Violence</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 mx-auto my-2 border border-primary p-2 hide" id="topics_selectors">
                                            <h6 class="text-center">Select Topics</h6>
                                            <input type="hidden" name="topics_to_select" id="topics_to_select" value="{{$topics_selected}}">
                                            <input type="hidden" name="topics_selected" value="[]" id="topics_selected">
                                            <div class="accordion" id="accordionExample">
                                            @php
                                                for ($index=0; $index < count($lesson_plan); $index++) { 
                                                     $data_to_display = "<div class='accordion-item'>";
                                                            $data_to_display.="<h6 class='accordion-header' id='headingOne'>";
                                                                $data_to_display.="<button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='#window".$lesson_plan[$index]->index."' aria-expanded='true' aria-controls='window".$lesson_plan[$index]->index."'>";
                                                                    $data_to_display.="<input type='checkbox' class='selected' id='selected_".$lesson_plan[$index]->index."'> <label class='form-label' for='selected_".$lesson_plan[$index]->index."'><b>| ".($index+1).". - ".$lesson_plan[$index]->strand_name."</b></label>";
                                                                $data_to_display.="</button>";
                                                            $data_to_display.="</h6>";
                                                        $data_to_display.="<div id='window".$lesson_plan[$index]->index."' class='accordion-collapse collapse' aria-labelledby='headingOne' data-bs-parent='#accordionExample'>";
                                                            $data_to_display.="<div class='accordion-body'>";
                                                                if (count($lesson_plan[$index]->sub_strands) > 0) {
                                                                    $sub_strands = $lesson_plan[$index]->sub_strands;
                                                                    $data_to_display.="<p class='text-center'><b>Sub-Topics</b></p>";
                                                                    $data_to_display.="<ul class='list-group'>";
                                                                    for ($ind=0; $ind < count($sub_strands); $ind++) { 
                                                                        $data_to_display.="<li class='list-group-item'> ".($ind+1).". <input type='checkbox' class='selected_".$lesson_plan[$index]->index."' id='selected_".$lesson_plan[$index]->index."_".($sub_strands[$ind]->sub_index)."'>  <label for='selected_".$lesson_plan[$index]->index."_".($sub_strands[$ind]->sub_index)."' class='form-label'>".$sub_strands[$ind]->name."</label></li>";
                                                                    }
                                                                    $data_to_display.="<ul>";
                                                                }else {
                                                                    $data_to_display.="<h3 class='text-center text-secondary mt-1'><i class='bi bi-exclamation-triangle'></i></h3><p class='text-secondary text-center'>No Sub-Strands Available!</p>";
                                                                }
                                                            $data_to_display.="</div>";
                                                        $data_to_display.="</div>";
                                                    $data_to_display.="</div>";
                                                        echo $data_to_display;
                                                }
                                            @endphp
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <button class="btn btn-primary w-100" type="submit"><i class="bi bi-cog"></i> Create test!</button>
                                        </div>
                                    </form>
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

    {{-- page data --}}
    <script>
        var lesson_plan = @json($lesson_plan);
        var subjects_taught = @json($subjects_taught ?? []);
        var sms_data = @json($sms_data ?? '');
        window.onload = function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }
    </script>
    {{-- <script src="/assets/js/tr_js/lessonplan.js"></script> --}}
    <!-- Vendor JS Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/tr_js/createQBtest.js"></script>

</body>

</html>

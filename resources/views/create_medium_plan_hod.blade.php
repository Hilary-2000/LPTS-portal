<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">

    <title>Create Medium Term Lesson Plan - {{ $subject_details->display_name }} -
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

<style>
    .hide{
        display: none;
    }
    /*the container must be positioned relative:*/
    .autocomplete {
        position: relative;
        display: inline-block;
        width: 100%
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }

    .autocomplete-items div {
        padding: 2px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: DodgerBlue !important;
        color: #ffffff;
    }

</style>
<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="." class=" d-flex align-items-center">
                <b
                    class="d-none d-sm-block text-sm">{{ session('school_information') != null ? session('school_information')->school_name : 'REMNANT VISION ACADEMY ' }}</b>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        {{-- <p class="text-success text-center p-2">Password set successfully!</p> --}}
        @php
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
        @endphp

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
                <a class="nav-link " href="/Teacher/LessonPlan">
                    <i class="bi bi-book-half"></i>
                    <span>Lesson Plan</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Teacher/QuestionBank">
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
            <h1>Create Medium Term Lesson Plan - {{ $subject_details->display_name }} : {{ $get_class_name }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/LessonPlan">Subjects I teach</a></li>
                    <li class="breadcrumb-item"><a
                            href="/Teacher/Create/Lessonplan/{{ $lesson_id }}/Class/{{ $class }}">Create
                            Lesson Plan - {{ $subject_details->display_name }} : {{ $get_class_name }}</a> </li>
                    <li class="breadcrumb-item active">Medium Term Lesson Plan</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="container col-md-6 border border-primary rounded p-1">
                <a href="/Teacher/HOD/Create/Lessonplan/{{$lesson_id}}/Class/{{$class}}" class="btn btn-secondary my-2"><i class="bi bi-arrow-left"></i> Lesson Plan Manager</a>
                <br><span class="col-md-2" >Status : </span>
                <span class="col-md-6">
                    @if ($medium_term_status == 0)
                        <span class="badge p-1 text-lg bg-secondary">Preliminary Version</span>
                    @else
                        <span class="badge p-1 text-lg bg-success">Reviewed</span>
                    @endif
                </span>
                <a href="/Teacher/ChangeStatus/Medium/{{$lesson_plan_id}}/{{$lesson_id}}/{{$class}}" class="col-md-12 btn btn-success btn-sm my-1"><i class="bi bi-arrow-clockwise"></i> Change Status</a>
            </div>
            <hr>
            <p class="bg-white p-2"><b>Note:</b> <br>
                - Kindly save your work before you leave. <br>
                - Edit one plan at a time to avoid loosing the changes you made to that particular plan.<br>
                - To edit toggle the read only button to off so that you can edit the plan.
            </p>
            <p class="text-success">{{ session('strand_success') != null ? session('strand_success') : '' }}</p>
            <p class="text-danger">{{ session('strand_error') != null ? session('strand_error') : '' }}</p>
            <div class="row">
                {{-- store the medium term plan--}}
                <input type="hidden" name="lesson_plan_data" id="lesson_plan_data" value="{{$lesson_plan}}">
                <!-- Left side columns -->
                @php
                    $lesson_count = 0;
                    $term_1_weeks = get_weeks_between_dates($academic_calender[0]->start_time,$academic_calender[0]->end_time);
                    $term_2_weeks = get_weeks_between_dates($academic_calender[1]->start_time,$academic_calender[1]->end_time);
                    $term_3_weeks = get_weeks_between_dates($academic_calender[2]->start_time,$academic_calender[2]->end_time);
                @endphp
                <input type="hidden" id="term_one_weeks" value="{{$term_1_weeks}}">
                <input type="hidden" id="term_two_weeks" value="{{$term_2_weeks}}">
                <input type="hidden" id="term_three_weeks" value="{{$term_3_weeks}}">
                <div class="row col-lg-12 my-2 border border-primary my-2 p-2 rounded">
                    <div class="col-lg-4">
                        <label for="select_term" class="form-label">Select Term</label>
                        <select name="select_term" id="select_term" class="form-control">
                            <option value="" hidden>Select Term</option>
                            <option value="Term 1">Term 1</option>
                            <option value="Term 2">Term 2</option>
                            <option value="Term 3">Term 3</option>
                        </select>
                    </div>
                    <div class="col-lg-4 " id="term_select_weeks1">
                        <label for="select_week_1" class="form-label">Select Week for Term 1</label>
                        <select name="select_week_1" id="select_week_1" class="form-control">
                            <option value="" hidden>Select Week</option>
                            @for ($i = 0; $i < $term_1_weeks; $i++)
                                @if ($i == 0)
                                    <option id='term_one_default' value="{{$i+1}}">Week {{$i+1}}</option>
                                @else
                                    <option value="{{$i+1}}">Week {{$i+1}}</option>
                                @endif
                            @endfor
                        </select>
                    </div>
                    <div class="col-lg-4 hide" id="term_select_weeks2">
                        <label for="select_week_2" class="form-label">Select Week for Term 2</label>
                        <select name="select_week_2" id="select_week_2" class="form-control">
                            <option value="" hidden>Select Week</option>
                            @for ($i = 0; $i < $term_2_weeks; $i++)
                                @if ($i == 0)
                                    <option id='term_two_default' value="{{$i+1}}">Week {{$i+1}}</option>
                                @else
                                    <option value="{{$i+1}}">Week {{$i+1}}</option>
                                @endif
                            @endfor
                        </select>
                    </div>
                    <div class="col-lg-4 hide" id="term_select_weeks3">
                        <label for="select_week_3" class="form-label">Select Week for Term 3</label>
                        <select name="select_week_3" id="select_week_3" class="form-control">
                            <option value="" hidden>Select Week</option>
                            @for ($i = 0; $i < $term_3_weeks; $i++)
                                @if ($i == 0)
                                    <option id='term_three_default' value="{{$i+1}}">Week {{$i+1}}</option>
                                @else
                                    <option value="{{$i+1}}">Week {{$i+1}}</option>
                                @endif
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <p class="text-left p-0 text-bold mt-1">Select Mode</p>
                        <div class="form-check form-switch">
                            <input class="form-check-input" checked type="checkbox" id="readonly_mode">
                            <label class="form-label"  for="readonly_mode">ReadOnly Mode</label>
                        </div>
                    </div>
                </div>
                <div class="container p-1 row my-2" id="medium_plan_data">
                    <h3 class="text-center text-secondary"><i class="bi bi-exclamation-triangle"></i></h3>
                    <p class="text-secondary text-center">No Data to display at the moment! Select term you want to display to proceed!</p>
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
    <script>
        var lesson_plan = @json($lesson_plan);
        var lesson_id_medium = @json($lesson_id);
        var class_medium = @json($class);
        var populators = @json($populators);
        var dates_details = @json($dates_details);
        var strands_data = @json($strands_data);
    </script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/tr_js/medium_plan.js"></script>

</body>

</html>

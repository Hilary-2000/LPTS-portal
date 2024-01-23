<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Create Short Term Lesson Plan - {{ $subject_details->display_name }} : {{ $get_class_name }} -
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
    .hide {
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
            <h1>Create Short Term Lesson Plan - {{ $subject_details->display_name }} : {{ $get_class_name }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/LessonPlan">Subjects I teach</a></li>
                    <li class="breadcrumb-item"><a
                            href="/Teacher/Create/Lessonplan/{{ $lesson_id }}/Class/{{ $class }}">Create
                            Lesson Plan - {{ $subject_details->display_name }} : {{ $get_class_name }}</a> </li>
                    <li class="breadcrumb-item active">Short Term Lesson Plan</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <a href="/Teacher/Create/Lessonplan/{{ $lesson_id }}/Class/{{ $class }}"
                class="btn btn-secondary my-2"><i class="bi bi-arrow-left"></i> Lesson Plan Manager</a>
                <br>
            <span class="col-md-2" >Status : </span>
            <span class="col-md-6">
                @if ($short_term_status == 0)
                    <span class="badge p-1 text-lg bg-secondary">Preliminary Version</span>
                @else
                    <span class="badge p-1 text-lg bg-success">Reviewed</span>
                @endif
            </span>
            <hr>
            <p class="bg-white p-2"><b>Note:</b> <br>
                - Select the date below to show the plan for that day. <br>
            </p>
            <p class="text-success">{{ session('strand_success') != null ? session('strand_success') : '' }}</p>
            <p class="text-danger">{{ session('strand_error') != null ? session('strand_error') : '' }}</p>

            <div class="container bg-white border-white border rounded p-1">
                <div class="container border border-primary rounded bg-white mb-2">
                    <h5 class="text-center"><b>Short Term Plan</b></h5>
                    <div class="row">
                        <div class="container col-md-4 mx-0 p-1">
                            <label for="main_date_selector" class="form-label">Select Date</label>
                            <input type="date" name="main_date_selector" id="main_date_selector"
                                class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="container col-md-4 mx-0 p-1">
                            <button class="btn btn-primary mt-3 w-100" id="display_plans">Display Plan</button>
                        </div>
                    </div>
                </div>
                <div class="container p-1">
                    <div class="container border border-primary rounded p-2">
                        <h6 class="text-center"><b><u>Plan Details</u></b></h6>
                        <div class="container row">
                            <div class="container col-md-6">
                                <p><b>Date:</b> <span id="date_holders">{{ date('D, M d, Y') }}</span><br>
                                    <b>Week:</b> <span id="week_holder">Not Set</span><br>
                                    <b>Term:</b> <span id="term_holder">Not Set</span><br>
                                    <b>Strand / Topic Associated:</b> <span id="strands_topics_assoc">Not
                                        Set</span><br>
                                    <b>Sub-Strand / Sub-Topic Associated:</b> <span id="sub_strand_topics_assoc">Week
                                        1</span>
                                </p>
                                <button type="button" class="btn btn-primary hide" id="error_names_are_in"
                                    data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                    Vertically centered
                                </button>
                            </div>
                            <div class="container col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" checked="" type="checkbox"
                                        id="complete_status">
                                    <label class="form-label" for="complete_status"><b>Completed</b></label>
                                </div>
                                <label for="progress_until_today" class="form-label"><b>Progress</b></label>
                                <div class="progress my-1 text-center" id="">
                                    <div class="progress-bar" id="progress_until_today" role="progressbar"
                                        style="width: {{ $percentage }}%" aria-valuenow="0" aria-valuemin="0"
                                        aria-valuemax="100">{{ $percentage }}%</div>
                                </div>
                            </div>
                            <div class="container col-md-3">

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <!-- Vertically centered Modal -->
                            <div class="modal fade" id="verticalycentered" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Error</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <span class="text-danger" id="text_error_display">You cannot create a plan
                                                on a date thats not included in the academic calender, Its considered a
                                                holiday or a weekend!</span>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Vertically centered Modal-->
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Introduction / Getting Started / Utangulizi</b></h6>
                        </div>
                        <div class="col-md-4">
                            <label for="plan_introductions_get_started" class="form-label"><b>Set up Introduction</b></label>
                            <textarea name="plan_introductions_get_started" id="plan_introductions_get_started" class="form-control" placeholder="Set up your lesson introduction here..." colspan="30"
                                rows="5"></textarea>
                            <button class="btn btn-primary my-1 w-100" id="set_plan_introductions"><i class="bi-save"></i>
                                Save</button>
                        </div>
                        <div class="col-md-8" style="border-left: 1px solid rgb(119, 119, 119);">
                            <h6 id="introductions_appear_here" class="my-2 p-1"></h6>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Objectives (Learners should be able to / Mwanafunzi aweze)</b></h6>
                        </div>
                        <div class="col-md-4">
                            <label for="plan_objectives" class="form-label"><b>Define Objectives</b></label>
                            <div class="autocomplete">
                                <input type="text" name="plan_objectives" id="plan_objectives"
                                    class="form-control" placeholder="e.g, Students will be able to..">
                            </div>
                            <button class="btn btn-primary my-1 w-100" id="save_objectives"><i class="bi-plus"></i>
                                Add</button>
                        </div>
                        <div class="col-md-8" style="border-left: 2px solid rgb(119, 119, 119);">
                            <label for="display_objectives" class="form-label"><b>Objectives List</b></label>
                            <ul class="list-group" id="display_objectives">
                                <h3 class="text-center text-secondary mt-1"><i class="bi bi-exclamation-triangle"></i>
                                </h3>
                                <p class="text-secondary text-center">No Objectives set!</p>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Activities</b></h6>
                        </div>
                        <div class="col-md-4">
                            <label for="plan_activities" class="form-label"><b>Define Activity</b></label>
                            <div class="autocomplete">
                                <input type="text" name="plan_activities" id="plan_activities"
                                    class="form-control" placeholder="e.g, Creating of visual aid..">
                            </div>
                            <button class="btn btn-primary my-1 w-100" id="save_activities"><i class="bi-plus"></i>
                                Add</button>
                        </div>
                        <div class="col-md-8" style="border-left: 2px solid rgb(119, 119, 119);">
                            <label for="activities_list_display" class="form-label"><b>Activity List</b></label>
                            <ul class="list-group" id="activities_list_display">
                                <h3 class="text-center text-secondary mt-1"><i class="bi bi-exclamation-triangle"></i>
                                </h3>
                                <p class="text-secondary text-center">No Activity set!</p>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Key Inquiry Questions (KIQ) / Maswali Dadisi</b></h6>
                        </div>
                        <div class="col-md-4">
                            <label for="plan_quizes" class="form-label"><b>Set A Quiz</b></label>
                            <input type="text" name="plan_quizes" id="plan_quizes" class="form-control"
                                placeholder="Set quiz..">
                            <button class="btn btn-primary my-1 w-100" id="set_a_quizes"><i class="bi-save"></i>
                                Save</button>
                        </div>
                        <div class="col-md-8" style="border-left: 1px solid rgb(119, 119, 119);">
                            <label for="quiz_list" class="form-label"><b>Quiz List</b></label>
                            <ul class="list-group" id="quiz_list">
                                <li class="list-group-item">1. Stop Violence <input type="hidden" value="1"
                                        id="objective_id_1_0"> <span style="cursor:pointer;"
                                        class="text-danger trash_objective hide" id="trash_objective_1_0"><i
                                            class="bi bi-trash"></i></span></li>
                                <li class="list-group-item">2. Objective 1 <input type="hidden" value="2"
                                        id="objective_id_2_0"> <span style="cursor:pointer;"
                                        class="text-danger trash_objective hide" id="trash_objective_2_0"><i
                                            class="bi bi-trash"></i></span></li>
                                <li class="list-group-item">3. This plan is linked with the Long Term Plan, you will be
                                    able to create this plan reffering to the termly objectives that were defined in the
                                    long term plan. Furthermore you will be able to break the activities in weekly
                                    format, share the activities that will be done, objectives and the resources to be
                                    used. <input type="hidden" value="3" id="objective_id_3_0"> <span
                                        style="cursor:pointer;" class="text-danger trash_objective hide"
                                        id="trash_objective_3_0"><i class="bi bi-trash"></i></span></li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Learning Resources / Vifaa na Asilia</b></h6>
                        </div>
                        <div class="col-md-4 mx-1">
                            <div class="container">
                                <label for="plan_resources" class="form-label"><b>Define Resources</b></label>
                                <select name="plan_resources" id="plan_resources" class="form-control">
                                    <option value="" hidden>Select Resource to Define</option>
                                    <option selected value="Notes/Documents">Notes and Documents (pdf, Word, ppt)
                                    </option>
                                    <option value="Videos">Youtube Videos</option>
                                    <option value="Videos_ids">Youtube Videos Id</option>
                                    <option value="Book Reference">Book Reference</option>
                                </select>
                            </div>
                            <div class="container my-2 border border-primary rounded p-2 windows"
                                id="select_notes_window">
                                <form action="/UploadNotesFiles" id="form_handlers_inside" method="post"
                                    enctype="multipart/form-data">
                                    <h6 class="text-center">Select File</h6>
                                    @csrf
                                    <input type="hidden" id="subject_id_file" name="subject_id"
                                        value="{{ $lesson_id }}">
                                    <input type="hidden" id="class_id_file" name="class_selected"
                                        value="{{ $class }}">
                                    <input type="file" class="form-control" id="notes_file_accept" required
                                        accept=".pdf,.docx,.ppt,.pptx">
                                    <p id="error_handler_file_upload"></p>
                                    <div class="progress my-1 hide" id="file_progress_bars">
                                        <div class="progress-bar" id="progress_bars" role="progressbar"
                                            style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100">0%</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm my-1 w-100"><i
                                            class="bi-upload"></i> Upload</button>
                                </form>
                            </div>

                            <div class="container my-2 border border-primary rounded p-2 hide windows"
                                id="book_refferences_window">
                                <h6 class="text-center">Book Refferences</h6>
                                <label for="book_refferences" class="form-label"></label>
                                <div class="autocomplete">
                                    <input type="text" id="book_refferences" class="form-control"
                                        placeholder="e.g English pg:1-10">
                                </div>
                                <button id="add_book_refferences" class="btn btn-primary btn-sm my-1 w-100"><i
                                        class="bi-plus"></i> Add</button>
                            </div>
                            <div class="container my-2 border border-primary rounded p-2 hide windows"
                                id="youtube_video_ids_window">
                                <h6 class="text-center">Add Youtube Video Id</h6>
                                <label for="video_tittles" class="form-label" id="youtube_videos"><b>Video
                                        Title.</b></label>
                                <input required type="text" id="video_tittles" name="video_tittles"
                                    class="form-control" placeholder="e.g English Topic 1">

                                <label for="video_descriptions" class="form-label" id="youtube_videos"><b>Video
                                        Description.</b></label>
                                <textarea required name="video_descriptions" id="video_descriptions" cols="30" rows="5"
                                    class="form-control" placeholder="Video description."></textarea>

                                {{-- VIDEO ID GENERATOR --}}
                                <label for="video_id_selector" class="form-label"><b>Video ID options</b></label>
                                <select name="video_id_selector" id="video_id_selector" class="form-control">
                                    <option value="" hidden>Select option...</option>
                                    <option value="generate_id">Generate from URL</option>
                                    <option selected value="type_id">Type Video URL</option>
                                </select>
                                <div class="d-none" id="generate_video_url">
                                    <label for="youtube_video_urls" class="form-label text-primary"><b>Enter Video URL here!</b></label>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <input type="text" id="youtube_video_urls" class="form-control"
                                        placeholder="e.g, https://www.youtube.com/watch?v=Pou_FMGBw8Q">
                                        </div>
                                        <div class="col-md-2"><button id="generate_video_id" class="btn btn-sm btn-primary mx-auto"><i class="bi bi-gear"></i></button></div>
                                    </div>
                                </div>

                                <div class="" id="enter_video_id">
                                    <label for="youtube_video_ids" class="form-label text-primary"><b>Youtube Video Id</b></label>
                                    <input type="text" id="youtube_video_ids" class="form-control"
                                        placeholder="e.g, Pou_FMGBw8Q">
                                </div>

                                <label class="form-label" for="video_privacy_status"><b>Video Privacy
                                        Status</b></label>
                                <select name="video_privacy_status" id="video_privacy_status" class="form-control">
                                    <option value="" hidden>Select Option</option>
                                    <option selected value="unlisted">Unlisted</option>
                                    <option value="public">Public</option>
                                </select>
                                <button id="add_youtube_video_ids" class="btn btn-primary btn-sm my-1 w-100"><i
                                        class="bi-plus"></i> Add</button>
                            </div>

                            <div class="container my-2 border border-primary rounded p-2 hide windows"
                                id="youtube_video_upload_window">
                                <form action="/UploadYoutube" id="upload_youtube_video" enctype="multipart/form-data"
                                    method="post">
                                    <h6 class="text-center">Upload Youtube Video</h6>
                                    <label for="video_name" class="form-label" id="youtube_videos"><b>Video
                                            Title.</b></label>
                                    <input required type="text" id="video_name" name="video_name"
                                        class="form-control" placeholder="e.g English Topic 1">

                                    <label for="video_description" class="form-label" id="youtube_videos"><b>Video
                                            Description.</b></label>
                                    <textarea required name="video_description" id="video_description" cols="30" rows="5"
                                        class="form-control" placeholder="Video description."></textarea>

                                    <label for="youtube_videos_uploads" class="form-label"><b>Video File.</b></label>
                                    <input required type="file" class="form-control" name="youtube_videos_uploads"
                                        id="youtube_videos_uploads" accept=".mp4,.avi,.avi,.avi">

                                    <label class="form-label" for="video_privacy"><b>Video Privacy Status</b></label>
                                    <select name="video_privacy" id="video_privacy" class="form-control">
                                        <option value="" hidden>Select Option</option>
                                        <option selected value="unlisted">Unlisted</option>
                                        <option value="public">Public</option>
                                    </select>

                                    <div class="progress my-1 hide" id="file_progress_bars_youtube">
                                        <div class="progress-bar" id="progress_bars_youtubes" role="progressbar"
                                            style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100">0%</div>
                                    </div>
                                    <p class="text-center" id="youtube_upload_err"></p>
                                    <button class="btn btn-primary btn-sm my-1"
                                        @if (session('access_token')) {{-- {{"disabled"}} --}} @endif type="button"
                                        id="auth-btn">Authenticate Youtube <i class="bi-youtube"></i></button>
                                    <button type="submit" id="upload_youtube"
                                        @if (!session('access_token')) {{ 'disabled' }} @endif
                                        class="btn btn-primary btn-sm my-1 w-100"><i class="bi-upload"></i>
                                        Upload</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-8 row" style="border-left: 2px solid rgb(119, 119, 119);">
                            <div class="col-md-6">
                                <label for="notes_list_display" class="form-label"><b>Notes & Documents</b></label>
                                <ul class="list-group" id="notes_list_display">
                                    <h3 class="text-center text-secondary mt-1"><i class="bi bi-exclamation-triangle"></i>
                                    </h3>
                                    <p class="text-secondary text-center">No Notes & Documents set!</p>
                                </ul>
                            </div>
                            <div class="col-md-6" style="border-left: 1px solid rgb(119, 119, 119);">
                                <label for="book_refference_list_display" class="form-label"><b>Book
                                        Refferences</b></label>
                                <ul class="list-group" id="book_refference_list_display">
                                    <h3 class="text-center text-secondary mt-1"><i class="bi bi-exclamation-triangle"></i>
                                    </h3>
                                    <p class="text-secondary text-center">No Book Refferences set!</p>
                                </ul>
                            </div>
                            <div class="col-md-12" style="border-top: 1px solid rgb(119, 119, 119);">
                                <label for="youtube_videos_lists" class="form-label"><b>Learning Videos</b> <span
                                        class="hide" id="deleting_loaders"></span></label>
                                <ul class="list-group" id="youtube_videos_lists">
                                    <h3 class="text-center text-secondary mt-1"><i class="bi bi-exclamation-triangle"></i>
                                    </h3>
                                    <p class="text-secondary text-center">No Learning Videos set!</p>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Lesson Development / Utaratibu wa Somo</b></h6>
                        </div>
                        <div class="col-md-4">
                            <label for="plan_lesson_development" class="form-label"><b>Stage / Hatua</b></label>
                            <input type="text" name="plan_lesson_development" id="plan_lesson_development" class="form-control"
                                placeholder="Set quiz..">
                            <button class="btn btn-primary my-1 w-100" id="set_lesson_development"><i class="bi-save"></i>
                                Save</button>
                        </div>
                        <div class="col-md-8" style="border-left: 1px solid rgb(119, 119, 119);">
                            <label for="quiz_list" class="form-label"><b>Stages / Hatua</b></label>
                            <ul class="list-group" id="lesson_development_stages">
                                <li class="list-group-item">Step 1 / Hatua la 1: <input type="hidden" value="1"
                                        id="objective_id_1_0"> <span style="cursor:pointer;"
                                        class="text-danger  hide" id=""><i
                                            class="bi bi-trash"></i></span></li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Reflection of the Lesson / Maoni (Comments)</b></h6>
                        </div>
                        <div class="col-md-4">
                            <label for="plan_comments" class="form-label"><b>Lesson reflection</b></label>
                            <textarea name="plan_comments" id="plan_comments" class="form-control" placeholder="Write your lesson reflection here.." cols="30"
                                rows="5"></textarea>
                            <button class="btn btn-primary my-1 w-100" id="set_a_comments"><i class="bi-save"></i>
                                Save</button>
                        </div>
                        <div class="col-md-8" style="border-left: 1px solid rgb(119, 119, 119);">
                            {{-- <p><b>Comments appear here..</b></p> --}}
                            <h6 id="comments_appear_here" class="my-2 p-1"></h6>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-center"><b>Conclusion / Hitimisho</b></h6>
                        </div>
                        <div class="col-md-4">
                            <label for="plan_conclusions" class="form-label"><b>Conclusion</b></label>
                            <textarea name="plan_conclusions" id="plan_conclusions" class="form-control" placeholder="Conclusions..." cols="30"
                                rows="5"></textarea>
                            <button class="btn btn-primary my-1 w-100" id="set_a_conclusion"><i class="bi-save"></i>
                                Save</button>
                        </div>
                        <div class="col-md-8" style="border-left: 1px solid rgb(119, 119, 119);">
                            {{-- <p><b>Comments appear here..</b></p> --}}
                            <h6 id="conclusions_appear_here" class="my-2 p-1"></h6>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <form action="/ManageShortPlan" method="post" id="ManageSHortPlan">
                            @csrf
                            <input type="hidden" name="lesson_id" value="{{ $lesson_id }}">
                            <input type="hidden" name="class" value="{{ $class }}">
                            <input type="hidden" id="short_term_data_original"
                                value='{{ json_encode($short_term_plan) }}'>
                            <input type="hidden" id="short_term_data" name="short_term_data"
                                value='{{ json_encode($short_term_plan) }}'>
                            <button type="submit" class="w-100 btn btn-primary btn-sm"><i class="ft-save"></i> Save
                                Plan</button>
                        </form>
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
    <script>
        // Get the button element
        var button = document.getElementById('auth-btn');

        // Add an event listener for button click
        button.addEventListener('click', function() {
            var authWindow = window.open('/auth', 'authWindow', 'width=500,height=500');
            window.addEventListener('message', function(event) {
                if (event.origin === location.origin && event.data === 'authenticated') {
                    authWindow.close();
                    // User is authenticated
                    document.getElementById('upload_youtube').disabled = false;
                    button.disabled = true;
                }
            });
        });
    </script>
    <script>
        var short_term_plan = @json($short_term_plan);
        var academic_calender = @json($academic_calender);
        var get_class_name = @json($get_class_name);
        var subject_details = @json($subject_details);
        var medium_term_plan = @json($medium_term_plan);
        var classes = @json($class);
        var lesson_id = @json($lesson_id);
        var dates_details = @json($dates_details);
        var longterm_plan_data = @json($long_term_plan);
    </script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/tr_js/short_plan.js"></script>

</body>

</html>

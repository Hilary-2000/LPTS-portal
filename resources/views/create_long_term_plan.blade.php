<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">

    <title>Create Long Term Lesson Plan - {{ $subject_details->display_name }} -
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
                    class="d-none d-sm-block text-sm">{{ session('school_information') != null ? session('school_information')->school_name : 'REMNANT VISION ACADEMY ' }}</b>
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
            <h1>Create Long Term Lesson Plan - {{ $subject_details->display_name }} : {{ $get_class_name }} </h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/LessonPlan">Subjects I teach</a></li>
                    <li class="breadcrumb-item"><a
                            href="/Teacher/Create/Lessonplan/{{ $lesson_id }}/Class/{{ $class }}">Create
                            Lesson Plan - {{ $subject_details->display_name }} : {{ $get_class_name }}</a> </li>
                    <li class="breadcrumb-item active">Long Term Lesson Plan</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <p class="text-success">{{session("strand_success") != null ? session("strand_success") : ""}}</p>
            <p class="text-danger">{{session("strand_error") != null ? session("strand_error") : ""}}</p>
            <div class="row">
                <!-- Left side columns -->
                @php
                    $lesson_count = 0;
                @endphp
                <div class="col-lg-12">
                    {{-- term 1 --}}
                    <div class="card">
                        <div class="card-header">
                            <a href="/Teacher/Create/Lessonplan/{{$lesson_id}}/Class/{{$class}}" class="btn btn-secondary my-2"><i class="bi bi-arrow-left"></i> Lesson Plan Manager</a>
                            <div class="row col-md-6 rounded p-1">
                                <span class="col-md-2" >Status : </span>
                                <span class="col-md-6">
                                    @if ($long_term_status == 0)
                                        <span class="badge p-1 text-lg bg-secondary">Preliminary Version</span>
                                    @else
                                        <span class="badge p-1 text-lg bg-success">Reviewed</span>
                                    @endif
                                </span>
                            </div>
                            <hr>
                            <h5 class="card-title p-0 m-0">Term One :   <span  data-bs-toggle="modal" data-bs-target="#test_add_button_1" class="btn btn-sm btn-success text-white"><i class="bi bi-plus"></i> Add Strand / Topic</span></h5>
                            <div class="modal fade" id="test_add_button_1" tabindex="-1">
                                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Add Strand/Topic</h5>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        @php
                                            $term = 1;
                                        @endphp
                                        <div class="modal-body">
                                            <!-- Multi Columns Form -->
                                            <form class="row g-3" method="POST" action="/CreateLessonPlan/addStrands">
                                                @csrf
                                                <div class="col-md-9">
                                                    <label for="strand_name" class="form-label"><b>Strand/Topic Name</b></label>
                                                    <input type="text" class="form-control" name="strand_name" id="strand_name" placeholder="e.g., Introduction to Mathematics">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="strand_code" class="form-label"><b>Strand/Topic Code</b></label>
                                                    <input type="text" class="form-control" name="strand_code" id="strand_code" placeholder="e.g., IM101">
                                                </div>
                                                <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                <input type="hidden" name="class_plan" value="{{$class}}">
                                                <div class="col-md-3">
                                                    <label for="strand_term" class="form-label"><b>Term Selected:</b></label>
                                                    <select name="term_selected" id="strand_term" class="form-control">
                                                        <option value="1">Term 1</option>
                                                        <option value="2">Term 2</option>
                                                        <option value="3">Term 3</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    {{-- add objectives --}}
                                                    <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm show_objectives" id="add_objective_window"><i class="bi bi-plus"></i> Add</span></label>
                                                    <div class="col-md-8 border border-primary p-1 hide my-2" id="objective_record_window">
                                                        <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                        <label for="strands_objectives" class="form-label">Add Strand`s Objective</label>
                                                        <input type="text" class="form-control" id="strands_objectives" placeholder="Students will be able to...">
                                                        <span class="btn btn-primary btn-sm my-1" id="add_objective"><i class="bi bi-save"></i> Save</span>
                                                    </div>
                                                    <input type="hidden" name="strands_objectives_holder" id="strands_objectives_holder">

                                                    <!-- List group Numbered -->
                                                    <ol class="list-group list-group-numbered" id="strands_obj_list">
                                                        <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                    </ol><!-- End List group Numbered -->
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="period_1" class="form-label"><b>Period</b></label>
                                                    <div class="input-group">
                                                        <input type="number" name="period" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                                                        <span class="input-group-text" id="inputGroupPrepend2">Weeks</span>
                                                      </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm" id="add_learning_material"><i class="bi bi-plus"></i> Add</span></label>
                                                    <div class="col-md-6 border border-primary p-1 hide" id="add_learning_materials_window">
                                                        <label for="learning_materials" class="form-label">Add Learning Materials</label>
                                                        <input type="text" class="form-control" id="learning_materials" placeholder="E.g., Kiswahili Mufti">
                                                        <span class="btn btn-primary btn-sm my-1" id="add_learning_materials_list"><i class="bi bi-save"></i> Save</span>
                                                    </div>
                                                    <input type="hidden" name="learning_materials_holder" id="learning_materials_holder">
                                                    <!-- List group Numbered -->
                                                    <ol class="list-group list-group-numbered" id="learning_materials_lists">
                                                        <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                    </ol><!-- End List group Numbered -->
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="strands_comment" class="form-label"><b>Strands Comments</b></label>
                                                    <textarea name="strands_comment" id="strands_comment" cols="30" rows="5" class="form-control" placeholder="Strands Comments are written here."></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    {{-- <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button> --}}
                                                </div>
                                                <div class="col-md-4">
                                                </div>
                                            </form><!-- End Multi Columns Form -->
                                        </div>
                                        <div class="modal-footer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Lesson Plan Term One</h5>
                            {{-- create an accordion for termly data --}}
                            <div class="container">

                                <div class="accordion " id="accordionFlushExample">
                                    @php
                                        $from = 1;
                                        $to = 0;
                                        $term_one_strands = 0;
                                    @endphp
                                    @for ($i = 0; $i < count($long_lesson_plan); $i++)
                                        @php
                                            if ($long_lesson_plan[$i]->term != "1") {
                                                continue;
                                            }
                                            $term_one_strands++;
                                        @endphp
                                    @endfor
                                    {{-- strands starts from here --}}
                                    @if ($term_one_strands > 0)
                                        @for ($i = 0; $i < count($long_lesson_plan); $i++)
                                            @php
                                                if ($long_lesson_plan[$i]->term != "1") {
                                                    continue;
                                                }
                                                $long_plan_index = $long_lesson_plan[$i]->index;
                                                $term = $long_lesson_plan[$i]->term;
                                                $lesson_count++;
                                            @endphp
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    {{-- the button to be clicked to display content --}}
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse_{{$term}}_{{$i+1}}"
                                                        aria-expanded="false" aria-controls="flush-collapse_{{$term}}_{{$i+1}}">
                                                        <h6><b>Strand / Topic {{$lesson_count}}</b>: {{$long_lesson_plan[$i]->strand_name }}</h6>
                                                    </button>
                                                </h2>
                                                <div id="flush-collapse_{{$term}}_{{$i+1}}" class="accordion-collapse collapse p-1"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body border border-primary rounded">
                                                        <div class="row">
                                                            <div class="col-md-9">
                                                                <h6><b>Strand / Topic {{$lesson_count}} <i data-bs-toggle="modal"
                                                                            data-bs-target="#strand_edit_{{$term}}_{{$i+1}}"
                                                                            class="bi bi-pen edit-pen"></i> </b>:
                                                                            {{$long_lesson_plan[$i]->strand_name}}</h6>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <span id="delete_strand{{$term}}_{{$i}}" class="delete_strand btn btn-primary"><i class="bi bi-trash"></i> Delete</span>
                                                                <div class="container border border-danger rounded p-1 my-1 hide" id="delete_strand_window{{$term}}_{{$i}}">
                                                                    <p class="text-bold text-danger">Do you want to permanently delete <b>{{$long_lesson_plan[$i]->strand_name}}</b>?</p>
                                                                    <a href="/deleteStrand/Subject/{{$lesson_id}}/Class/{{$class}}/Strand/{{$long_lesson_plan[$i]->index}}" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Strand: {{$long_lesson_plan[$i]->strand_name}}"><i class="bi bi-trash"></i> Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <h6><b>Strand / Topic Code</b>: {{$long_lesson_plan[$i]->strand_code}}</h6>

                                                        <div class="modal fade" id="strand_edit_{{$term}}_{{$i+1}}" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Edit "{{$long_lesson_plan[$i]->strand_name}}"</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Multi Columns Form -->
                                                                        <form class="row g-3" action="/UpdateLessonPlan/updateStrand" method="POST">
                                                                            @csrf
                                                                            <div class="col-md-9">
                                                                                <label for="strand_name_{{$term}}_{{$i}}" class="form-label"><b>Strand/Topic Name</b></label>
                                                                                <input type="text" class="form-control" value="{{$long_lesson_plan[$i]->strand_name}}" name="strand_name" id="strand_name_{{$term}}_{{$i}}">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label for="strand_code_{{$term}}_{{$i}}" class="form-label"><b>Strand/Topic Name</b></label>
                                                                                <input type="text" name="strand_code" class="form-control" value="{{$long_lesson_plan[$i]->strand_code}}" id="strand_code_{{$term}}_{{$i}}">
                                                                            </div>
                                                                            <input type="hidden" name="strand_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                            <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                            <input type="hidden" name="class_plan" value="{{$class}}">
                                                                            <input type="hidden" name="" class="current_term" id="current_term{{$term}}">
                                                                            <div class="col-md-3">
                                                                                <label for="strand_term_{{$term}}_{{$i}}" class="form-label"><b>Term Selected:</b></label>
                                                                                <select name="term_selected" id="strand_term_{{$term}}_{{$i}}" class="form-control">
                                                                                    <option {{$long_lesson_plan[$i]->term == "1" ? "selected":""}} value="1">Term 1</option>
                                                                                    <option {{$long_lesson_plan[$i]->term == "2" ? "selected":""}} value="2">Term 2</option>
                                                                                    <option {{$long_lesson_plan[$i]->term == "3" ? "selected":""}} value="3">Term 3</option>
                                                                                </select>
                                                                            </div>
                                                                            @php
                                                                            $strand_term_1 = [];
                                                                                for ($indie2 = 0; $indie2 < count($long_lesson_plan); $indie2++){
                                                                                        if ($long_lesson_plan[$indie2]->term != "1") {
                                                                                            continue;
                                                                                        }
                                                                                        $strand_jina = $long_lesson_plan[$indie2]->strand_name;
                                                                                        array_push($strand_term_1,[$strand_jina,$long_lesson_plan[$indie2]->index]);
                                                                                    }
                                                                            @endphp
                                                                            @if (count($strand_term_1) >= 1)
                                                                            <div class="col-md-3">
                                                                                <label for="move_term_1" class="form-label"><b>Move To</b></label>
                                                                                <select class="form-control" name="move_term" id="move_term_1">
                                                                                    <option value="[default,{{$long_lesson_plan[$i]->index}}]" hidden>Select Option</option>
                                                                                    <option value="[-1,{{$long_lesson_plan[$i]->index}}]" >At the beginning.</option>
                                                                                    @for ($indie = 0; $indie < count($strand_term_1); $indie++)
                                                                                        @if ($strand_term_1[$indie][1] == $long_lesson_plan[$i]->index)
                                                                                            {{-- <option selected value="{{$strand_term_1[$indie][1]}}" >After {{$strand_term_1[$indie][0]}}</option> --}}
                                                                                        @else
                                                                                            <option value="[{{$strand_term_1[$indie][1]}},{{$long_lesson_plan[$i]->index}}]" >After {{$strand_term_1[$indie][0]}}</option>
                                                                                        @endif
                                                                                    @endfor
                                                                                </select>
                                                                            </div>
                                                                            @endif
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm edit_add_objective_window" id="edit_add_objective_window{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-8 border border-primary p-1 hide my-2" id="edit_objective_record_window{{$term}}_{{$i}}">
                                                                                    <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                    <label for="edit_strands_objectives{{$term}}_{{$i}}" class="form-label">Add Strand`s Objective</label>
                                                                                    <input type="text" class="form-control" id="edit_strands_objectives{{$term}}_{{$i}}" placeholder="Students will be able to...">
                                                                                    <span class="btn btn-primary btn-sm my-1 edit_add_objective" id="edit_add_objective{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" value="{{is_array($long_lesson_plan[$i]->objectives) > 0 ? json_encode($long_lesson_plan[$i]->objectives) : ""}}" name="edit_strands_objectives_holder" id="edit_strands_objectives_holder{{$term}}_{{$i}}">
                                                                                <!-- List group Numbered -->
                                                                                @if (is_array($long_lesson_plan[$i]->objectives)>0)
                                                                                    @if (count($long_lesson_plan[$i]->objectives))
                                                                                        <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                            @for ($ind = 0; $ind < count($long_lesson_plan[$i]->objectives); $ind++)
                                                                                                <li class="list-group-item">{{$long_lesson_plan[$i]->objectives[$ind]}} <span style='cursor:pointer;' class='text-danger trash_edit_obj{{$term}}_{{$i}} trash_obj_del' id='trash_edit_obj_{{$term}}_{{$i}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                            @endfor
                                                                                        </ol>
                                                                                    @else
                                                                                        <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                            <li class='list-group-item text-black'>No objectives set at the moment!</li>
                                                                                        </ol>
                                                                                    @endif
                                                                                @else
                                                                                    <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                        <li class='list-group-item text-black'>No objectives set at the moment!</li>
                                                                                    </ol>
                                                                                @endif
                                                                                <!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label for="period_1" class="form-label"><b>Period</b></label>
                                                                                <div class="input-group">
                                                                                    <input type="number" name="period" value="{{$long_lesson_plan[$i]->period}}" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2_{{$term}}" required>
                                                                                    <span class="input-group-text" id="inputGroupPrepend2_{{$term}}">Weeks</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm edit_learning_materials" id="edit_learning_materials_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-6 border border-primary p-1 hide" id="edit_learning_materials_window_{{$term}}_{{$i}}">
                                                                                    <label for="learning_materials_{{$term}}_{{$i}}" class="form-label">Add Learning Materials</label>
                                                                                    <input type="text" class="form-control" id="learning_materials_{{$term}}_{{$i}}" placeholder="E.g., Kiswahili Mufti">
                                                                                    <span class="btn btn-primary btn-sm my-1 edit_learning_materials_list" id="edit_learning_materials_list_{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" value="{{is_array($long_lesson_plan[$i]->learning_materials) > 0 ? json_encode($long_lesson_plan[$i]->learning_materials) : ""}}" name="edit_learning_materials_holder" id="edit_learning_materials_holder_{{$term}}_{{$i}}">
                                                                                @if (is_array($long_lesson_plan[$i]->learning_materials) > 0)
                                                                                    @if (count($long_lesson_plan[$i]->learning_materials) > 0)
                                                                                        <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                            @for ($ind = 0; $ind < count($long_lesson_plan[$i]->learning_materials); $ind++)
                                                                                                <li class="list-group-item">{{$long_lesson_plan[$i]->learning_materials[$ind]}} <span style='cursor:pointer;' class='text-danger trash_learning_materials{{$term}}_{{$i}} trash_learning_materials_edit' id='trash_lm_{{$term}}_{{$i}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                            @endfor
                                                                                        </ol>
                                                                                    @else
                                                                                        <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                            <li class='list-group-item text-black'>No Learning Materials Posted at the moment!</li>
                                                                                        </ol>
                                                                                    @endif
                                                                                @else
                                                                                    <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                        <li class='list-group-item text-black'>No Learning Materials Posted at the moment!</li>
                                                                                    </ol>
                                                                                @endif
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="comment_edit_{{$term}}_{{$i}}" class="form-label">Comments</label>
                                                                                <textarea name="comment" id="comment_edit_{{$term}}_{{$i}}" cols="30" rows="5" class="form-control" placeholder="Comments are written here..">{{$long_lesson_plan[$i]->comment}}</textarea>
                                                                            </div>
                                                                            <div class="colmd-4">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Update Changes</button>
                                                                            </div>
                                                                        </form><!-- End Multi Columns Form -->
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h6><b>Objectives</b> :</h6>
                                                        @if (is_array($long_lesson_plan[$i]->objectives))
                                                            <ul>
                                                                @for ($ind = 0; $ind < count($long_lesson_plan[$i]->objectives); $ind++)
                                                                    <li class="">{{$long_lesson_plan[$i]->objectives[$ind]}}</li>
                                                                @endfor
                                                            </ul>
                                                        @else
                                                            <ul>
                                                                <li class=''>No objectives set at the moment!</li>
                                                            </ul>
                                                        @endif
                                                        @php
                                                            $to += $long_lesson_plan[$i]->period;
                                                        @endphp
                                                            @if ($long_lesson_plan[$i]->period > 0)
                                                                <h6><b>Period : </b> From: Start of Week {{$from}}, To: End of Week {{$to}} ({{$long_lesson_plan[$i]->period}} Weeks)</h6>
                                                            @else
                                                            <h6><b>Period : </b> {{$long_lesson_plan[$i]->period}} Weeks</h6>
                                                            @endif
                                                        @php
                                                            $from += $long_lesson_plan[$i]->period;
                                                        @endphp
                                                        <h6><b>Learning Materials</b> : </h6>
                                                        @if (is_array($long_lesson_plan[$i]->learning_materials) > 0)
                                                            <ul class="">
                                                                @for ($ind = 0; $ind < count($long_lesson_plan[$i]->learning_materials); $ind++)
                                                                    <li class="">{{$long_lesson_plan[$i]->learning_materials[$ind]}}</li>
                                                                @endfor
                                                            </ul>
                                                        @else
                                                            <ul class="">
                                                                <li class=' text-black'>No Learning Materials Posted at the moment!</li>
                                                            </ul>
                                                        @endif
                                                        @if (strlen(trim($long_lesson_plan[$i]->comment)) > 0)
                                                            <h6><b>Comments : </b></h6>
                                                            <p>{{$long_lesson_plan[$i]->comment}}</p>
                                                        @endif
                                                        <hr>
                                                        <p class="text-secondary border border-secondary p-1 rounded">Add Sub-Strands / Sub-Topics : <span  data-bs-toggle="modal" data-bs-target="#add_substrands{{$term}}_{{$i}}" class="btn btn-sm btn-success text-white"><i class="bi bi-plus"></i> Add Sub-Strands / Sub-Topics </span></p>
                                                        <div class="modal fade" id="add_substrands{{$term}}_{{$i}}" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Add Sub-Strands / Sub-Topics</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Multi Columns Form -->
                                                                        <form class="row g-3" method="POST" action="/CreateLessonPlan/addSubStrands">
                                                                            @csrf
                                                                            <div class="col-md-9">
                                                                                <label for="sub_strand_name{{$term}}" class="form-label"><b>Sub-Strands / Sub-Topics Name</b></label>
                                                                                <input type="text" class="form-control" name="sub_strand_name" id="sub_strand_name{{$term}}" placeholder="e.g., Introduction to Mathematics">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label for="sub_strand_code{{$term}}" class="form-label"><b>Sub-Strands Code</b></label>
                                                                                <input type="text" class="form-control" name="sub_strand_code" id="sub_strand_code{{$term}}" placeholder="e.g., IM101">
                                                                            </div>
                                                                            <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                            <input type="hidden" name="class_plan" value="{{$class}}">
                                                                            <input type="hidden" name="term_selected" value="1">
                                                                            <input type="hidden" name="plan_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                            <div class="col-md-12">
                                                                                {{-- add objectives --}}
                                                                                <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm add_objective_window" id="add_objective_window_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-8 border border-primary p-1 hide my-2" id="objective_record_window_{{$term}}_{{$i}}">
                                                                                    <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                    <label for="strands_objectives{{$term}}_{{$i}}" class="form-label">Add Strand`s Objective</label>
                                                                                    <input type="text" class="form-control" id="strands_objectives{{$term}}_{{$i}}" placeholder="Students will be able to...">
                                                                                    <span class="btn btn-primary btn-sm my-1 add_objective" id="add_objective{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" name="sub_strands_objectives_holder" id="strands_objectives_holder{{$term}}_{{$i}}">
                            
                                                                                <!-- List group Numbered -->
                                                                                <ol class="list-group list-group-numbered" id="strands_obj_list{{$term}}_{{$i}}">
                                                                                    <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                </ol><!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label for="period_{{$term}}_{{$i}}" class="form-label"><b>Period</b></label>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="period_{{$term}}_{{$i}}" name="sub_strand_period" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                                                                                    <span class="input-group-text" id="inputGroupPrepend2">
                                                                                        <select name="duration_unit" id="duration_unit" class="form-control">
                                                                                            <option value="Days">Days</option>
                                                                                            <option value="Weeks">Weeks</option>
                                                                                            <option value="Months">Months</option>
                                                                                        </select>
                                                                                    </span>
                                                                                  </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm add_learning_materials" id="add_learning_materials_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-6 border border-primary p-1 hide" id="add_learning_materials_window_{{$term}}_{{$i}}">
                                                                                    <label for="learning_materials{{$term}}_{{$i}}" class="form-label">Add Learning Materials</label>
                                                                                    <input type="text" class="form-control" id="learning_materials{{$term}}_{{$i}}" placeholder="E.g., Kiswahili Mufti">
                                                                                    <span class="btn btn-primary btn-sm my-1 add_learning_materials_list" id="add_learning_materials_list{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" name="sub_strand_learning_materials_holder" id="learning_materials_holder{{$term}}_{{$i}}">
                                                                                <!-- List group Numbered -->
                                                                                <ol class="list-group list-group-numbered" id="learning_materials_lists{{$term}}_{{$i}}">
                                                                                    <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                </ol><!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="sub_strands_comment{{$term}}_{{$i}}" class="form-label"><b>Sub-Strands / Sub-Topics Comments</b></label>
                                                                                <textarea name="sub_strands_comment" id="sub_strands_comment{{$term}}_{{$i}}" cols="30" rows="5" class="form-control" placeholder="Sub-Strands / Sub-Topics Comments are written here."></textarea>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <button type="submit" class="btn btn-primary">Save Sub-Strands / Sub-Topics</button>
                                                                                {{-- <button type="button" class="btn btn-secondary"
                                                                                    data-bs-dismiss="modal">Close</button> --}}
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                            </div>
                                                                        </form><!-- End Multi Columns Form -->
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        @if (count($long_lesson_plan[$i]->sub_strands) > 0)
                                                            @for ($index = 0; $index < count($long_lesson_plan[$i]->sub_strands); $index++)
                                                                @php
                                                                    $sub_strands = $long_lesson_plan[$i]->sub_strands[$index];
                                                                @endphp
                                                                <div class="container border border-primary rounded p-2">
                                                                            <div class="row">
                                                                                <div class="col-md-9">
                                                                                    <h6><b>Sub-Strand / Sub-Topic {{$index+1}} </b> <i
                                                                                        class="bi bi-pen edit-pen"  data-bs-toggle="modal" data-bs-target="#edit_substrands_{{$term}}_{{$i}}_{{$index}}" ></i> : {{$sub_strands->name}}</h6>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <span id="delete_sub_strand{{$term}}_{{$i}}_{{$index}}" class="delete_sub_strand btn btn-primary"><i class="bi bi-trash"></i> Delete</span>
                                                                                    <div class="container border border-danger rounded p-1 my-1 hide" id="delete_sub_strand_window{{$term}}_{{$i}}_{{$index}}">
                                                                                        <p class="text-bold text-danger">Do you want to permanently delete <b>{{$sub_strands->name}}</b>?</p>
                                                                                        <a href="/deleteSubStrand/Subject/{{$lesson_id}}/Class/{{$class}}/Strand/{{$sub_strands->sub_index}}" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Sub-Strand: {{$sub_strands->name}}"><i class="bi bi-trash"></i> Delete</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal fade" id="edit_substrands_{{$term}}_{{$i}}_{{$index}}" tabindex="-1">
                                                                                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title">Edit "{{$sub_strands->name}}"</h5>
                                                                                            <button type="button" class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <!-- Multi Columns Form -->
                                                                                            <form class="row g-3" method="POST" action="/EditLessonPlan/editSubStrands">
                                                                                                @csrf
                                                                                                <div class="col-md-9">
                                                                                                    <label for="sub_strand_name_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands / Sub-Topics Name</b></label>
                                                                                                    <input type="text" class="form-control" name="sub_strand_name" id="sub_strand_name_{{$term}}_{{$i}}_{{$index}}" value="{{$sub_strands->name}}" placeholder="e.g., Introduction to Mathematics">
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <label for="sub_strand_code_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands Code</b></label>
                                                                                                    <input type="text" class="form-control" name="sub_strand_code" value="{{$sub_strands->code}}" id="sub_strand_code_{{$term}}_{{$i}}_{{$index}}" placeholder="e.g., IM101">
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <label for="substrand_locale_opt{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Move to :</b></label>
                                                                                                    <select name="substrand_locale_opt" id="substrand_locale_opt{{$term}}_{{$i}}_{{$index}}" class="form-control substrand_locale_opt">
                                                                                                        <option value="Different Strand">Different Strand</option>
                                                                                                        <option value="In Strand">In Strand</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-md-4" id="different_strand_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    <label for="select_strand{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Select Different Strand : </b></label>
                                                                                                    <select name="select_strand" id="select_strand{{$term}}_{{$i}}_{{$index}}" class="form-control">
                                                                                                        @for ($indx = 0; $indx < count($long_lesson_plan); $indx++)
                                                                                                            <option {{$long_lesson_plan[$indx]->index == $long_lesson_plan[$i]->index ? "selected" : ""}} value="{{$long_lesson_plan[$indx]->index}}">{{$indx+1}}). {{$long_lesson_plan[$indx]->strand_name}}</option>
                                                                                                        @endfor
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-md-4 hide" id="different_loc_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    <label for="select_location{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Select Another Location : </b></label>
                                                                                                    <select name="select_location" id="select_location{{$term}}_{{$i}}_{{$index}}" class="form-control">
                                                                                                        @if ($index != 0)
                                                                                                            <option value="-1">At the beginning</option>
                                                                                                        @endif
                                                                                                        @php
                                                                                                            $sub_str = $long_lesson_plan[$i]->sub_strands;
                                                                                                        @endphp
                                                                                                        @for ($indx = 0; $indx < count($sub_str); $indx++)
                                                                                                            <option {{$sub_str[$indx]->sub_index == $sub_strands->sub_index ? "selected" : ""}} value="{{$sub_str[$indx]->sub_index}}">After {{$sub_str[$indx]->name}}</option>
                                                                                                        @endfor
                                                                                                    </select>
                                                                                                </div>
                                                                                                <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                                                <input type="hidden" name="class_plan" value="{{$class}}">
                                                                                                <input type="hidden" name="term_selected" value="1">
                                                                                                <input type="hidden" name="plan_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                                                <input type="hidden" name="date_created" value="{{$long_lesson_plan[$i]->date_created}}">
                                                                                                <input type="hidden" name="substrand_index" value="{{$sub_strands->sub_index}}">
                                                                                                <div class="col-md-12">
                                                                                                    {{-- add objectives --}}
                                                                                                    <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm add_objective_window_{{$term}}_{{$i}} add_object_windows" id="add_objective_window_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                                    <div class="col-md-8 border border-primary p-1 hide my-2" id="objective_record_window_{{$term}}_{{$i}}_{{$index}}">
                                                                                                        <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                                        <label for="strands_objectives_{{$term}}_{{$i}}_{{$index}}" class="form-label">Add Strand`s Objective</label>
                                                                                                        <input type="text" class="form-control" id="strands_objectives_{{$term}}_{{$i}}_{{$index}}" placeholder="Students will be able to...">
                                                                                                        <span class="btn btn-primary btn-sm my-1 btn_add_obj{{$term}}_{{$i}} btn_add_obj" id="btn_add_obj_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-save"></i> Save</span>
                                                                                                    </div>
                                                                                                    <input type="hidden" value="{{count($sub_strands->objectives) > 0 ? json_encode($sub_strands->objectives) : "[]"}}" name="sub_strands_objectives_holder" id="strands_objectives_holder_{{$term}}_{{$i}}_{{$index}}">

                                                                                                    @if (count($sub_strands->objectives) > 0)
                                                                                                        <ol class="list-group list-group-numbered" id="strands_obj_list_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            @for ($inds = 0; $inds < count($sub_strands->objectives); $inds++)
                                                                                                                <li class='list-group-item text-black'>{{$sub_strands->objectives[$inds]}}. <span style='cursor:pointer;' class='text-danger trash_obj{{$term}}_{{$i}}_{{$index}} getTrashObjectives' id = 'trash_obj_{{$term}}_{{$i}}_{{$index}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                                            @endfor
                                                                                                        </ol>   
                                                                                                    @else
                                                                                                        <ol class="list-group list-group-numbered" id="strands_obj_list_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                                        </ol>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <label for="period_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Period</b></label>

                                                                                                    <div class="input-group">
                                                                                                        <input type="number" id="period_{{$term}}_{{$i}}_{{$index}}" value="{{explode(" ",$sub_strands->period)[0]}}" name="sub_strand_period" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                                                                                                        <span class="input-group-text" id="inputGroupPrepend2">
                                                                                                            <select name="duration_unit" id="duration_unit" class="form-control">
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Days"? "selected" : ""}} value="Days">Days</option>
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Weeks"? "selected" : ""}} value="Weeks">Weeks</option>
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Months"? "selected" : ""}} value="Months">Months</option>
                                                                                                            </select>
                                                                                                        </span>
                                                                                                      </div>
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm add_learning_material_{{$term}}_{{$i}} lm_lists" id="add_learning_material_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                                    <div class="col-md-6 border border-primary p-1 hide" id="add_learning_materials_window_{{$term}}_{{$i}}_{{$index}}">
                                                                                                        <label for="learning_material_{{$term}}_{{$i}}_{{$index}}" class="form-label">Add Learning Materials</label>
                                                                                                        <input type="text" class="form-control" id="learning_material_{{$term}}_{{$i}}_{{$index}}" placeholder="E.g., Kiswahili Mufti">
                                                                                                        <span class="btn btn-primary btn-sm my-1 btn_add_lm_list_{{$term}}_{{$i}} btn_add_lm_list" id="btn_add_lm_list_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-save"></i> Save</span>
                                                                                                    </div>
                                                                                                    <input type="hidden" value="{{count($sub_strands->learning_materials) > 0 ? json_encode($sub_strands->learning_materials) : "[]"}}" name="sub_strand_learning_materials_holder" id="learning_materials_holder_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    @if (count($sub_strands->learning_materials) > 0)
                                                                                                        <ol class="list-group list-group-numbered" id="learning_materials_lists_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            @for ($inds = 0; $inds < count($sub_strands->learning_materials); $inds++)
                                                                                                                <li class='list-group-item text-black'>{{$sub_strands->learning_materials[$inds]}} <span style='cursor:pointer;' class='text-danger trash_lm_lst_{{$term}}_{{$i}}_{{$index}} trash_lm_list' id = 'lmlist_{{$term}}_{{$i}}_{{$index}}_{{$inds}}'><i class='bi bi-trash'></i></span></li>
                                                                                                            @endfor
                                                                                                        </ol>   
                                                                                                    @else
                                                                                                        <ol class="list-group list-group-numbered" id="learning_materials_lists_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                                        </ol>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <label for="sub_strands_comment_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands / Sub-Topics Comments</b></label>
                                                                                                    <textarea name="sub_strands_comment" id="sub_strands_comment_{{$term}}_{{$i}}_{{$index}}" cols="30" rows="5" class="form-control" placeholder="Sub-Strands / Sub-Topics Comments are written here.">{{$sub_strands->comments}}</textarea>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <button type="submit" class="btn btn-primary">Save Sub-Strands / Sub-Topics</button>
                                                                                                    {{-- <button type="button" class="btn btn-secondary"
                                                                                                        data-bs-dismiss="modal">Close</button> --}}
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                </div>
                                                                                            </form><!-- End Multi Columns Form -->
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    <h6><b>Sub-Strand Code </b> : {{$sub_strands->code}}</h6>
                                                                    <h6><b>Objectives</b> :</h6>
                                                                    <ul>
                                                                        @if (count($sub_strands->objectives) > 0)
                                                                            @for ($index_2 = 0; $index_2 < count($sub_strands->objectives); $index_2++)
                                                                                <li>{{$sub_strands->objectives[$index_2]}}</li>
                                                                            @endfor
                                                                        @else
                                                                            <li>No Objectives Set at the moment!</li>
                                                                        @endif
                                                                    </ul>
                                                                    <h6><b>Period </b>: {{$sub_strands->period}}</h6>
                                                                    <h6><b>Learning Materials</b> :</h6>
                                                                    <ul>
                                                                        @if (count($sub_strands->learning_materials) > 0)
                                                                            @for ($index_2 = 0; $index_2 < count($sub_strands->learning_materials); $index_2++)
                                                                                <li>{{$sub_strands->learning_materials[$index_2]}}</li>
                                                                            @endfor
                                                                        @else
                                                                            <li>No Learning Materials set at the moment!</li>
                                                                        @endif
                                                                    </ul>

                                                                    @if (strlen(trim($sub_strands->comments)) > 0)
                                                                        <h6><b>Comments : </b></h6>
                                                                        <p>{{$sub_strands->comments}}</p>
                                                                    @endif
                                                                </div>
                                                                @if ($index < (count($long_lesson_plan[$i]->sub_strands)-1))
                                                                    <hr class="w-50 mx-auto mb-1 border-primary">
                                                                    <hr class="w-50 mx-auto mt-0 border-primary">
                                                                @else
                                                                    <hr class="w-50 mx-auto mb-1 border-primary">
                                                                @endif
                                                            @endfor
                                                        @else
                                                            <p class="text-secondary text-center">No Sub-Strands set for this Strand!</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @else
                                        <p class="text-secondary border border-secondary p-1 rounded">No Strands/Topics have been set for this subject in Term One.</p>
                                    @endif
                                    {{-- strands ends from here --}}
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <p class="text-secondary"><b>Opening Date</b>: {{date("D dS M Y",strtotime($academic_calender[0]->start_time))}}<br> <b>Closing Date</b>: {{date("D dS M Y",strtotime($academic_calender[0]->closing_date))}}<br><b>Term End Date</b>: {{date("D dS M Y",strtotime($academic_calender[0]->end_time))}}<br><b>Weeks:</b>{{get_weeks_between_dates($academic_calender[0]->start_time, $academic_calender[0]->end_time);}}</p>
                        </div>
                    </div>
                    {{-- term two --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title p-0 m-0">Term Two :  </h5>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Lesson Plan Term Two</h5>
                            {{-- create an accordion for termly data --}}
                            <div class="container">
                                <div class="accordion " id="accordionFlushExample">
                                    @php
                                        $from = 1;
                                        $to = 0;
                                        $term_two_strands = 0;
                                    @endphp
                                    @for ($i = 0; $i < count($long_lesson_plan); $i++)
                                        @php
                                            if ($long_lesson_plan[$i]->term != "2") {
                                                continue;
                                            }
                                            $term_two_strands++;
                                        @endphp
                                    @endfor
                                    {{-- strands starts from here --}}
                                    @if ($term_two_strands > 0)
                                        @for ($i = 0; $i < count($long_lesson_plan); $i++)
                                            @php
                                                if ($long_lesson_plan[$i]->term != "2") {
                                                    continue;
                                                }
                                                $long_plan_index = $long_lesson_plan[$i]->index;
                                                $term = $long_lesson_plan[$i]->term;
                                                $lesson_count++;
                                            @endphp
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    {{-- the button to be clicked to display content --}}
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse_{{$term}}_{{$i+1}}"
                                                        aria-expanded="false" aria-controls="flush-collapse_{{$term}}_{{$i+1}}">
                                                        <h6><b>Strand / Topic {{$lesson_count}}</b>: {{$long_lesson_plan[$i]->strand_name }}</h6>
                                                    </button>
                                                </h2>
                                                <div id="flush-collapse_{{$term}}_{{$i+1}}" class="accordion-collapse collapse p-1"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body border border-primary rounded">
                                                        <div class="row">
                                                            <div class="col-md-9">
                                                                <h6><b>Strand / Topic {{$lesson_count}} <i data-bs-toggle="modal"
                                                                            data-bs-target="#strand_edit_{{$term}}_{{$i+1}}"
                                                                            class="bi bi-pen edit-pen"></i> </b>:
                                                                            {{$long_lesson_plan[$i]->strand_name}}</h6>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <span id="delete_strand{{$term}}_{{$i}}" class="delete_strand btn btn-primary"><i class="bi bi-trash"></i> Delete</span>
                                                                <div class="container border border-danger rounded p-1 my-1 hide" id="delete_strand_window{{$term}}_{{$i}}">
                                                                    <p class="text-bold text-danger">Do you want to permanently delete <b>{{$long_lesson_plan[$i]->strand_name}}</b>?</p>
                                                                    <a href="/deleteStrand/Subject/{{$lesson_id}}/Class/{{$class}}/Strand/{{$long_lesson_plan[$i]->index}}" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Strand: {{$long_lesson_plan[$i]->strand_name}}"><i class="bi bi-trash"></i> Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <h6><b>Strand / Topic Code</b>: {{$long_lesson_plan[$i]->strand_code}}</h6>

                                                        <div class="modal fade" id="strand_edit_{{$term}}_{{$i+1}}" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Edit "{{$long_lesson_plan[$i]->strand_name}}"</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Multi Columns Form -->
                                                                        <form class="row g-3" action="/UpdateLessonPlan/updateStrand" method="POST">
                                                                            @csrf
                                                                            <div class="col-md-9">
                                                                                <label for="strand_name_{{$term}}_{{$i}}" class="form-label"><b>Strand/Topic Name</b></label>
                                                                                <input type="text" class="form-control" value="{{$long_lesson_plan[$i]->strand_name}}" name="strand_name" id="strand_name_{{$term}}_{{$i}}">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label for="strand_code_{{$term}}_{{$i}}" class="form-label"><b>Strand/Topic Name</b></label>
                                                                                <input type="text" name="strand_code" class="form-control" value="{{$long_lesson_plan[$i]->strand_code}}" id="strand_code_{{$term}}_{{$i}}">
                                                                            </div>
                                                                            <input type="hidden" name="strand_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                            <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                            <input type="hidden" name="class_plan" value="{{$class}}">
                                                                            <input type="hidden" name="" class="current_term" id="current_term{{$term}}">
                                                                            <div class="col-md-3">
                                                                                <label for="strand_term_{{$term}}_{{$i}}" class="form-label"><b>Term Selected:</b></label>
                                                                                <select name="term_selected" id="strand_term_{{$term}}_{{$i}}" class="form-control">
                                                                                    <option {{$long_lesson_plan[$i]->term == "1" ? "selected":""}} value="1">Term 1</option>
                                                                                    <option {{$long_lesson_plan[$i]->term == "2" ? "selected":""}} value="2">Term 2</option>
                                                                                    <option {{$long_lesson_plan[$i]->term == "3" ? "selected":""}} value="3">Term 3</option>
                                                                                </select>
                                                                            </div>
                                                                            @php
                                                                            $strand_term_1 = [];
                                                                                for ($indie2 = 0; $indie2 < count($long_lesson_plan); $indie2++){
                                                                                        if ($long_lesson_plan[$indie2]->term != "2") {
                                                                                            continue;
                                                                                        }
                                                                                        $strand_jina = $long_lesson_plan[$indie2]->strand_name;
                                                                                        array_push($strand_term_1,[$strand_jina,$long_lesson_plan[$indie2]->index]);
                                                                                    }
                                                                            @endphp
                                                                            @if (count($strand_term_1) >= 1)
                                                                            <div class="col-md-3">
                                                                                <label for="move_term_1" class="form-label"><b>Move To</b></label>
                                                                                <select class="form-control" name="move_term" id="move_term_1">
                                                                                    <option value="[default,{{$long_lesson_plan[$i]->index}}]" hidden>Select Option</option>
                                                                                    <option value="[-1,{{$long_lesson_plan[$i]->index}}]" >At the beginning.</option>
                                                                                    @for ($indie = 0; $indie < count($strand_term_1); $indie++)
                                                                                        @if ($strand_term_1[$indie][1] == $long_lesson_plan[$i]->index)
                                                                                            {{-- <option selected value="{{$strand_term_1[$indie][1]}}" >After {{$strand_term_1[$indie][0]}}</option> --}}
                                                                                        @else
                                                                                            <option value="[{{$strand_term_1[$indie][1]}},{{$long_lesson_plan[$i]->index}}]" >After {{$strand_term_1[$indie][0]}}</option>
                                                                                        @endif
                                                                                    @endfor
                                                                                </select>
                                                                            </div>
                                                                            @endif
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm edit_add_objective_window" id="edit_add_objective_window{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-8 border border-primary p-1 hide my-2" id="edit_objective_record_window{{$term}}_{{$i}}">
                                                                                    <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                    <label for="edit_strands_objectives{{$term}}_{{$i}}" class="form-label">Add Strand`s Objective</label>
                                                                                    <input type="text" class="form-control" id="edit_strands_objectives{{$term}}_{{$i}}" placeholder="Students will be able to...">
                                                                                    <span class="btn btn-primary btn-sm my-1 edit_add_objective" id="edit_add_objective{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" value="{{is_array($long_lesson_plan[$i]->objectives) > 0 ? json_encode($long_lesson_plan[$i]->objectives) : ""}}" name="edit_strands_objectives_holder" id="edit_strands_objectives_holder{{$term}}_{{$i}}">
                                                                                <!-- List group Numbered -->
                                                                                @if (is_array($long_lesson_plan[$i]->objectives)>0)
                                                                                    @if (count($long_lesson_plan[$i]->objectives))
                                                                                        <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                            @for ($ind = 0; $ind < count($long_lesson_plan[$i]->objectives); $ind++)
                                                                                                <li class="list-group-item">{{$long_lesson_plan[$i]->objectives[$ind]}} <span style='cursor:pointer;' class='text-danger trash_edit_obj{{$term}}_{{$i}} trash_obj_del' id='trash_edit_obj_{{$term}}_{{$i}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                            @endfor
                                                                                        </ol>
                                                                                    @else
                                                                                        <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                            <li class='list-group-item text-black'>No objectives set at the moment!</li>
                                                                                        </ol>
                                                                                    @endif
                                                                                @else
                                                                                    <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                        <li class='list-group-item text-black'>No objectives set at the moment!</li>
                                                                                    </ol>
                                                                                @endif
                                                                                <!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label for="period_1" class="form-label"><b>Period</b></label>
                                                                                <div class="input-group">
                                                                                    <input type="number" name="period" value="{{$long_lesson_plan[$i]->period}}" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2_{{$term}}" required>
                                                                                    <span class="input-group-text" id="inputGroupPrepend2_{{$term}}">Weeks</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm edit_learning_materials" id="edit_learning_materials_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-6 border border-primary p-1 hide" id="edit_learning_materials_window_{{$term}}_{{$i}}">
                                                                                    <label for="learning_materials_{{$term}}_{{$i}}" class="form-label">Add Learning Materials</label>
                                                                                    <input type="text" class="form-control" id="learning_materials_{{$term}}_{{$i}}" placeholder="E.g., Kiswahili Mufti">
                                                                                    <span class="btn btn-primary btn-sm my-1 edit_learning_materials_list" id="edit_learning_materials_list_{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" value="{{is_array($long_lesson_plan[$i]->learning_materials) > 0 ? json_encode($long_lesson_plan[$i]->learning_materials) : ""}}" name="edit_learning_materials_holder" id="edit_learning_materials_holder_{{$term}}_{{$i}}">
                                                                                @if (is_array($long_lesson_plan[$i]->learning_materials) > 0)
                                                                                    @if (count($long_lesson_plan[$i]->learning_materials) > 0)
                                                                                        <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                            @for ($ind = 0; $ind < count($long_lesson_plan[$i]->learning_materials); $ind++)
                                                                                                <li class="list-group-item">{{$long_lesson_plan[$i]->learning_materials[$ind]}} <span style='cursor:pointer;' class='text-danger trash_learning_materials{{$term}}_{{$i}} trash_learning_materials_edit' id='trash_lm_{{$term}}_{{$i}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                            @endfor
                                                                                        </ol>
                                                                                    @else
                                                                                        <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                            <li class='list-group-item text-black'>No Learning Materials Posted at the moment!</li>
                                                                                        </ol>
                                                                                    @endif
                                                                                @else
                                                                                    <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                        <li class='list-group-item text-black'>No Learning Materials Posted at the moment!</li>
                                                                                    </ol>
                                                                                @endif
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="comment_edit_{{$term}}_{{$i}}" class="form-label">Comments</label>
                                                                                <textarea name="comment" id="comment_edit_{{$term}}_{{$i}}" cols="30" rows="5" class="form-control" placeholder="Comments are written here..">{{$long_lesson_plan[$i]->comment}}</textarea>
                                                                            </div>
                                                                            <div class="colmd-4">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Update Changes</button>
                                                                            </div>
                                                                        </form><!-- End Multi Columns Form -->
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h6><b>Objectives</b> :</h6>
                                                        @if (is_array($long_lesson_plan[$i]->objectives))
                                                            <ul>
                                                                @for ($ind = 0; $ind < count($long_lesson_plan[$i]->objectives); $ind++)
                                                                    <li class="">{{$long_lesson_plan[$i]->objectives[$ind]}}</li>
                                                                @endfor
                                                            </ul>
                                                        @else
                                                            <ul>
                                                                <li class=''>No objectives set at the moment!</li>
                                                            </ul>
                                                        @endif
                                                        @php
                                                            $to += $long_lesson_plan[$i]->period;
                                                        @endphp
                                                            @if ($long_lesson_plan[$i]->period > 0)
                                                                <h6><b>Period : </b> From: Start of Week {{$from}}, To: End of Week {{$to}} ({{$long_lesson_plan[$i]->period}} Weeks)</h6>
                                                            @else
                                                            <h6><b>Period : </b> {{$long_lesson_plan[$i]->period}} Weeks</h6>
                                                            @endif
                                                        @php
                                                            $from += $long_lesson_plan[$i]->period;
                                                        @endphp
                                                        <h6><b>Learning Materials</b> : </h6>
                                                        @if (is_array($long_lesson_plan[$i]->learning_materials) > 0)
                                                            <ul class="">
                                                                @for ($ind = 0; $ind < count($long_lesson_plan[$i]->learning_materials); $ind++)
                                                                    <li class="">{{$long_lesson_plan[$i]->learning_materials[$ind]}}</li>
                                                                @endfor
                                                            </ul>
                                                        @else
                                                            <ul class="">
                                                                <li class=' text-black'>No Learning Materials Posted at the moment!</li>
                                                            </ul>
                                                        @endif
                                                        @if (strlen(trim($long_lesson_plan[$i]->comment)) > 0)
                                                            <h6><b>Comments : </b></h6>
                                                            <p>{{$long_lesson_plan[$i]->comment}}</p>
                                                        @endif
                                                        <hr>
                                                        <p class="text-secondary border border-secondary p-1 rounded">Add Sub-Strands / Sub-Topics : <span  data-bs-toggle="modal" data-bs-target="#add_substrands{{$term}}_{{$i}}" class="btn btn-sm btn-success text-white"><i class="bi bi-plus"></i> Add Sub-Strands / Sub-Topics </span></p>
                                                        <div class="modal fade" id="add_substrands{{$term}}_{{$i}}" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Add Sub-Strands / Sub-Topics</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Multi Columns Form -->
                                                                        <form class="row g-3" method="POST" action="/CreateLessonPlan/addSubStrands">
                                                                            @csrf
                                                                            <div class="col-md-9">
                                                                                <label for="sub_strand_name{{$term}}" class="form-label"><b>Sub-Strands / Sub-Topics Name</b></label>
                                                                                <input type="text" class="form-control" name="sub_strand_name" id="sub_strand_name{{$term}}" placeholder="e.g., Introduction to Mathematics">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label for="sub_strand_code{{$term}}" class="form-label"><b>Sub-Strands Code</b></label>
                                                                                <input type="text" class="form-control" name="sub_strand_code" id="sub_strand_code{{$term}}" placeholder="e.g., IM101">
                                                                            </div>
                                                                            <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                            <input type="hidden" name="class_plan" value="{{$class}}">
                                                                            <input type="hidden" name="term_selected" value="1">
                                                                            <input type="hidden" name="plan_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                            <div class="col-md-12">
                                                                                {{-- add objectives --}}
                                                                                <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm add_objective_window" id="add_objective_window_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-8 border border-primary p-1 hide my-2" id="objective_record_window_{{$term}}_{{$i}}">
                                                                                    <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                    <label for="strands_objectives{{$term}}_{{$i}}" class="form-label">Add Strand`s Objective</label>
                                                                                    <input type="text" class="form-control" id="strands_objectives{{$term}}_{{$i}}" placeholder="Students will be able to...">
                                                                                    <span class="btn btn-primary btn-sm my-1 add_objective" id="add_objective{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" name="sub_strands_objectives_holder" id="strands_objectives_holder{{$term}}_{{$i}}">
                            
                                                                                <!-- List group Numbered -->
                                                                                <ol class="list-group list-group-numbered" id="strands_obj_list{{$term}}_{{$i}}">
                                                                                    <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                </ol><!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label for="period_{{$term}}_{{$i}}" class="form-label"><b>Period</b></label>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="period_{{$term}}_{{$i}}" name="sub_strand_period" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                                                                                    <span class="input-group-text" id="inputGroupPrepend2">
                                                                                        <select name="duration_unit" id="duration_unit" class="form-control">
                                                                                            <option value="Days">Days</option>
                                                                                            <option value="Weeks">Weeks</option>
                                                                                            <option value="Months">Months</option>
                                                                                        </select>
                                                                                    </span>
                                                                                  </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm add_learning_materials" id="add_learning_materials_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-6 border border-primary p-1 hide" id="add_learning_materials_window_{{$term}}_{{$i}}">
                                                                                    <label for="learning_materials{{$term}}_{{$i}}" class="form-label">Add Learning Materials</label>
                                                                                    <input type="text" class="form-control" id="learning_materials{{$term}}_{{$i}}" placeholder="E.g., Kiswahili Mufti">
                                                                                    <span class="btn btn-primary btn-sm my-1 add_learning_materials_list" id="add_learning_materials_list{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" name="sub_strand_learning_materials_holder" id="learning_materials_holder{{$term}}_{{$i}}">
                                                                                <!-- List group Numbered -->
                                                                                <ol class="list-group list-group-numbered" id="learning_materials_lists{{$term}}_{{$i}}">
                                                                                    <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                </ol><!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="sub_strands_comment{{$term}}_{{$i}}" class="form-label"><b>Sub-Strands / Sub-Topics Comments</b></label>
                                                                                <textarea name="sub_strands_comment" id="sub_strands_comment{{$term}}_{{$i}}" cols="30" rows="5" class="form-control" placeholder="Sub-Strands / Sub-Topics Comments are written here."></textarea>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <button type="submit" class="btn btn-primary">Save Sub-Strands / Sub-Topics</button>
                                                                                {{-- <button type="button" class="btn btn-secondary"
                                                                                    data-bs-dismiss="modal">Close</button> --}}
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                            </div>
                                                                        </form><!-- End Multi Columns Form -->
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        @if (count($long_lesson_plan[$i]->sub_strands) > 0)
                                                            @for ($index = 0; $index < count($long_lesson_plan[$i]->sub_strands); $index++)
                                                                @php
                                                                    $sub_strands = $long_lesson_plan[$i]->sub_strands[$index];
                                                                @endphp
                                                                <div class="container border border-primary rounded p-2">
                                                                            <div class="row">
                                                                                <div class="col-md-9">
                                                                                    <h6><b>Sub-Strand / Sub-Topic {{$index+1}} </b> <i
                                                                                        class="bi bi-pen edit-pen"  data-bs-toggle="modal" data-bs-target="#edit_substrands_{{$term}}_{{$i}}_{{$index}}" ></i> : {{$sub_strands->name}}</h6>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <span id="delete_sub_strand{{$term}}_{{$i}}_{{$index}}" class="delete_sub_strand btn btn-primary"><i class="bi bi-trash"></i> Delete</span>
                                                                                    <div class="container border border-danger rounded p-1 my-1 hide" id="delete_sub_strand_window{{$term}}_{{$i}}_{{$index}}">
                                                                                        <p class="text-bold text-danger">Do you want to permanently delete <b>{{$sub_strands->name}}</b>?</p>
                                                                                        <a href="/deleteSubStrand/Subject/{{$lesson_id}}/Class/{{$class}}/Strand/{{$sub_strands->sub_index}}" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Sub-Strand: {{$sub_strands->name}}"><i class="bi bi-trash"></i> Delete</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal fade" id="edit_substrands_{{$term}}_{{$i}}_{{$index}}" tabindex="-1">
                                                                                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title">Edit "{{$sub_strands->name}}"</h5>
                                                                                            <button type="button" class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <!-- Multi Columns Form -->
                                                                                            <form class="row g-3" method="POST" action="/EditLessonPlan/editSubStrands">
                                                                                                @csrf
                                                                                                <div class="col-md-9">
                                                                                                    <label for="sub_strand_name_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands / Sub-Topics Name</b></label>
                                                                                                    <input type="text" class="form-control" name="sub_strand_name" id="sub_strand_name_{{$term}}_{{$i}}_{{$index}}" value="{{$sub_strands->name}}" placeholder="e.g., Introduction to Mathematics">
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <label for="sub_strand_code_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands Code</b></label>
                                                                                                    <input type="text" class="form-control" name="sub_strand_code" value="{{$sub_strands->code}}" id="sub_strand_code_{{$term}}_{{$i}}_{{$index}}" placeholder="e.g., IM101">
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <label for="substrand_locale_opt{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Move to :</b></label>
                                                                                                    <select name="substrand_locale_opt" id="substrand_locale_opt{{$term}}_{{$i}}_{{$index}}" class="form-control substrand_locale_opt">
                                                                                                        <option value="Different Strand">Different Strand</option>
                                                                                                        <option value="In Strand">In Strand</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-md-4" id="different_strand_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    <label for="select_strand{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Select Different Strand : </b></label>
                                                                                                    <select name="select_strand" id="select_strand{{$term}}_{{$i}}_{{$index}}" class="form-control">
                                                                                                        @for ($indx = 0; $indx < count($long_lesson_plan); $indx++)
                                                                                                            <option {{$long_lesson_plan[$indx]->index == $long_lesson_plan[$i]->index ? "selected" : ""}} value="{{$long_lesson_plan[$indx]->index}}">{{$indx+1}}). {{$long_lesson_plan[$indx]->strand_name}}</option>
                                                                                                        @endfor
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-md-4 hide" id="different_loc_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    <label for="select_location{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Select Another Location : </b></label>
                                                                                                    <select name="select_location" id="select_location{{$term}}_{{$i}}_{{$index}}" class="form-control">
                                                                                                        @if ($index != 0)
                                                                                                            <option value="-1">At the beginning</option>
                                                                                                        @endif
                                                                                                        @php
                                                                                                            $sub_str = $long_lesson_plan[$i]->sub_strands;
                                                                                                        @endphp
                                                                                                        @for ($indx = 0; $indx < count($sub_str); $indx++)
                                                                                                            <option {{$sub_str[$indx]->sub_index == $sub_strands->sub_index ? "selected" : ""}} value="{{$sub_str[$indx]->sub_index}}">After {{$sub_str[$indx]->name}}</option>
                                                                                                        @endfor
                                                                                                    </select>
                                                                                                </div>
                                                                                                <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                                                <input type="hidden" name="class_plan" value="{{$class}}">
                                                                                                <input type="hidden" name="term_selected" value="1">
                                                                                                <input type="hidden" name="plan_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                                                <input type="hidden" name="date_created" value="{{$long_lesson_plan[$i]->date_created}}">
                                                                                                <input type="hidden" name="substrand_index" value="{{$sub_strands->sub_index}}">
                                                                                                <div class="col-md-12">
                                                                                                    {{-- add objectives --}}
                                                                                                    <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm add_objective_window_{{$term}}_{{$i}} add_object_windows" id="add_objective_window_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                                    <div class="col-md-8 border border-primary p-1 hide my-2" id="objective_record_window_{{$term}}_{{$i}}_{{$index}}">
                                                                                                        <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                                        <label for="strands_objectives_{{$term}}_{{$i}}_{{$index}}" class="form-label">Add Strand`s Objective</label>
                                                                                                        <input type="text" class="form-control" id="strands_objectives_{{$term}}_{{$i}}_{{$index}}" placeholder="Students will be able to...">
                                                                                                        <span class="btn btn-primary btn-sm my-1 btn_add_obj{{$term}}_{{$i}} btn_add_obj" id="btn_add_obj_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-save"></i> Save</span>
                                                                                                    </div>
                                                                                                    <input type="hidden" value="{{count($sub_strands->objectives) > 0 ? json_encode($sub_strands->objectives) : "[]"}}" name="sub_strands_objectives_holder" id="strands_objectives_holder_{{$term}}_{{$i}}_{{$index}}">

                                                                                                    @if (count($sub_strands->objectives) > 0)
                                                                                                        <ol class="list-group list-group-numbered" id="strands_obj_list_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            @for ($inds = 0; $inds < count($sub_strands->objectives); $inds++)
                                                                                                                <li class='list-group-item text-black'>{{$sub_strands->objectives[$inds]}}. <span style='cursor:pointer;' class='text-danger trash_obj{{$term}}_{{$i}}_{{$index}} getTrashObjectives' id = 'trash_obj_{{$term}}_{{$i}}_{{$index}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                                            @endfor
                                                                                                        </ol>   
                                                                                                    @else
                                                                                                        <ol class="list-group list-group-numbered" id="strands_obj_list_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                                        </ol>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <label for="period_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Period</b></label>

                                                                                                    <div class="input-group">
                                                                                                        <input type="number" id="period_{{$term}}_{{$i}}_{{$index}}" value="{{explode(" ",$sub_strands->period)[0]}}" name="sub_strand_period" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                                                                                                        <span class="input-group-text" id="inputGroupPrepend2">
                                                                                                            <select name="duration_unit" id="duration_unit" class="form-control">
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Days"? "selected" : ""}} value="Days">Days</option>
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Weeks"? "selected" : ""}} value="Weeks">Weeks</option>
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Months"? "selected" : ""}} value="Months">Months</option>
                                                                                                            </select>
                                                                                                        </span>
                                                                                                      </div>
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm add_learning_material_{{$term}}_{{$i}} lm_lists" id="add_learning_material_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                                    <div class="col-md-6 border border-primary p-1 hide" id="add_learning_materials_window_{{$term}}_{{$i}}_{{$index}}">
                                                                                                        <label for="learning_material_{{$term}}_{{$i}}_{{$index}}" class="form-label">Add Learning Materials</label>
                                                                                                        <input type="text" class="form-control" id="learning_material_{{$term}}_{{$i}}_{{$index}}" placeholder="E.g., Kiswahili Mufti">
                                                                                                        <span class="btn btn-primary btn-sm my-1 btn_add_lm_list_{{$term}}_{{$i}} btn_add_lm_list" id="btn_add_lm_list_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-save"></i> Save</span>
                                                                                                    </div>
                                                                                                    <input type="hidden" value="{{count($sub_strands->learning_materials) > 0 ? json_encode($sub_strands->learning_materials) : "[]"}}" name="sub_strand_learning_materials_holder" id="learning_materials_holder_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    @if (count($sub_strands->learning_materials) > 0)
                                                                                                        <ol class="list-group list-group-numbered" id="learning_materials_lists_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            @for ($inds = 0; $inds < count($sub_strands->learning_materials); $inds++)
                                                                                                                <li class='list-group-item text-black'>{{$sub_strands->learning_materials[$inds]}} <span style='cursor:pointer;' class='text-danger trash_lm_lst_{{$term}}_{{$i}}_{{$index}} trash_lm_list' id = 'lmlist_{{$term}}_{{$i}}_{{$index}}_{{$inds}}'><i class='bi bi-trash'></i></span></li>
                                                                                                            @endfor
                                                                                                        </ol>   
                                                                                                    @else
                                                                                                        <ol class="list-group list-group-numbered" id="learning_materials_lists_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                                        </ol>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <label for="sub_strands_comment_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands / Sub-Topics Comments</b></label>
                                                                                                    <textarea name="sub_strands_comment" id="sub_strands_comment_{{$term}}_{{$i}}_{{$index}}" cols="30" rows="5" class="form-control" placeholder="Sub-Strands / Sub-Topics Comments are written here.">{{$sub_strands->comments}}</textarea>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <button type="submit" class="btn btn-primary">Save Sub-Strands / Sub-Topics</button>
                                                                                                    {{-- <button type="button" class="btn btn-secondary"
                                                                                                        data-bs-dismiss="modal">Close</button> --}}
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                </div>
                                                                                            </form><!-- End Multi Columns Form -->
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    <h6><b>Sub-Strand Code </b> : {{$sub_strands->code}}</h6>
                                                                    <h6><b>Objectives</b> :</h6>
                                                                    <ul>
                                                                        @if (count($sub_strands->objectives) > 0)
                                                                            @for ($index_2 = 0; $index_2 < count($sub_strands->objectives); $index_2++)
                                                                                <li>{{$sub_strands->objectives[$index_2]}}</li>
                                                                            @endfor
                                                                        @else
                                                                            <li>No Objectives Set at the moment!</li>
                                                                        @endif
                                                                    </ul>
                                                                    <h6><b>Period </b>: {{$sub_strands->period}}</h6>
                                                                    <h6><b>Learning Materials</b> :</h6>
                                                                    <ul>
                                                                        @if (count($sub_strands->learning_materials) > 0)
                                                                            @for ($index_2 = 0; $index_2 < count($sub_strands->learning_materials); $index_2++)
                                                                                <li>{{$sub_strands->learning_materials[$index_2]}}</li>
                                                                            @endfor
                                                                        @else
                                                                            <li>No Learning Materials set at the moment!</li>
                                                                        @endif
                                                                    </ul>

                                                                    @if (strlen(trim($sub_strands->comments)) > 0)
                                                                        <h6><b>Comments : </b></h6>
                                                                        <p>{{$sub_strands->comments}}</p>
                                                                    @endif
                                                                </div>
                                                                @if ($index < (count($long_lesson_plan[$i]->sub_strands)-1))
                                                                    <hr class="w-50 mx-auto mb-1 border-primary">
                                                                    <hr class="w-50 mx-auto mt-0 border-primary">
                                                                @else
                                                                    <hr class="w-50 mx-auto mb-1 border-primary">
                                                                @endif
                                                            @endfor
                                                        @else
                                                            <p class="text-secondary text-center">No Sub-Strands set for this Strand!</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @else
                                        <p class="text-secondary border border-secondary p-1 rounded">No Strands/Topics have been set for this subject in Term Two.</p>
                                    @endif
                                    {{-- strands ends from here --}}
                                </div>
                            </div>

                        </div>
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
                        <div class="card-footer">
                            <p class="text-secondary" ><b>Opening Date</b>: {{date("D dS M Y",strtotime($academic_calender[1]->start_time))}}<br> <b>Closing Date</b>: {{date("D dS M Y",strtotime($academic_calender[1]->closing_date))}}<br><b>Term End Date</b>: {{date("D dS M Y",strtotime($academic_calender[1]->end_time))}} <br><b>Weeks:</b>{{get_weeks_between_dates($academic_calender[1]->start_time, $academic_calender[1]->end_time);}}</p>
                        </div>
                    </div>
                    {{-- ebd term two --}}
                    {{-- term two --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title p-0 m-0">Term Three :  </h5>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Lesson Plan Term Three</h5>
                            {{-- create an accordion for termly data --}}
                            <div class="container">
                                <div class="accordion " id="accordionFlushExample">
                                    @php
                                        $from = 1;
                                        $to = 0;
                                        $term_three_strands = 0;
                                    @endphp
                                    @for ($i = 0; $i < count($long_lesson_plan); $i++)
                                        @php
                                            if ($long_lesson_plan[$i]->term != "3") {
                                                continue;
                                            }
                                            $term_three_strands++;
                                        @endphp
                                    @endfor
                                    {{-- strands starts from here --}}
                                    @if ($term_three_strands > 0)
                                        @for ($i = 0; $i < count($long_lesson_plan); $i++)
                                            @php
                                                if ($long_lesson_plan[$i]->term != "3") {
                                                    continue;
                                                }
                                                $long_plan_index = $long_lesson_plan[$i]->index;
                                                $term = $long_lesson_plan[$i]->term;
                                                $lesson_count++;
                                            @endphp
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    {{-- the button to be clicked to display content --}}
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse_{{$term}}_{{$i+1}}"
                                                        aria-expanded="false" aria-controls="flush-collapse_{{$term}}_{{$i+1}}">
                                                        <h6><b>Strand / Topic {{$lesson_count}}</b>: {{$long_lesson_plan[$i]->strand_name }}</h6>
                                                    </button>
                                                </h2>
                                                <div id="flush-collapse_{{$term}}_{{$i+1}}" class="accordion-collapse collapse p-1"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body border border-primary rounded">
                                                        <div class="row">
                                                            <div class="col-md-9">
                                                                <h6><b>Strand / Topic {{$lesson_count}} <i data-bs-toggle="modal"
                                                                            data-bs-target="#strand_edit_{{$term}}_{{$i+1}}"
                                                                            class="bi bi-pen edit-pen"></i> </b>:
                                                                            {{$long_lesson_plan[$i]->strand_name}}</h6>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <span id="delete_strand{{$term}}_{{$i}}" class="delete_strand btn btn-primary"><i class="bi bi-trash"></i> Delete</span>
                                                                <div class="container border border-danger rounded p-1 my-1 hide" id="delete_strand_window{{$term}}_{{$i}}">
                                                                    <p class="text-bold text-danger">Do you want to permanently delete <b>{{$long_lesson_plan[$i]->strand_name}}</b>?</p>
                                                                    <a href="/deleteStrand/Subject/{{$lesson_id}}/Class/{{$class}}/Strand/{{$long_lesson_plan[$i]->index}}" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Strand: {{$long_lesson_plan[$i]->strand_name}}"><i class="bi bi-trash"></i> Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h6><b>Strand / Topic Code</b>: {{$long_lesson_plan[$i]->strand_code}}</h6>

                                                        <div class="modal fade" id="strand_edit_{{$term}}_{{$i+1}}" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Edit "{{$long_lesson_plan[$i]->strand_name}}"</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Multi Columns Form -->
                                                                        <form class="row g-3" action="/UpdateLessonPlan/updateStrand" method="POST">
                                                                            @csrf
                                                                            <div class="col-md-9">
                                                                                <label for="strand_name_{{$term}}_{{$i}}" class="form-label"><b>Strand/Topic Name</b></label>
                                                                                <input type="text" class="form-control" value="{{$long_lesson_plan[$i]->strand_name}}" name="strand_name" id="strand_name_{{$term}}_{{$i}}">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label for="strand_code_{{$term}}_{{$i}}" class="form-label"><b>Strand/Topic Name</b></label>
                                                                                <input type="text" name="strand_code" class="form-control" value="{{$long_lesson_plan[$i]->strand_code}}" id="strand_code_{{$term}}_{{$i}}">
                                                                            </div>
                                                                            <input type="hidden" name="strand_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                            <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                            <input type="hidden" name="class_plan" value="{{$class}}">
                                                                            <input type="hidden" name="" class="current_term" id="current_term{{$term}}">
                                                                            <div class="col-md-3">
                                                                                <label for="strand_term_{{$term}}_{{$i}}" class="form-label"><b>Term Selected:</b></label>
                                                                                <select name="term_selected" id="strand_term_{{$term}}_{{$i}}" class="form-control">
                                                                                    <option {{$long_lesson_plan[$i]->term == "1" ? "selected":""}} value="1">Term 1</option>
                                                                                    <option {{$long_lesson_plan[$i]->term == "2" ? "selected":""}} value="2">Term 2</option>
                                                                                    <option {{$long_lesson_plan[$i]->term == "3" ? "selected":""}} value="3">Term 3</option>
                                                                                </select>
                                                                            </div>
                                                                            @php
                                                                            $strand_term_1 = [];
                                                                                for ($indie2 = 0; $indie2 < count($long_lesson_plan); $indie2++){
                                                                                        if ($long_lesson_plan[$indie2]->term != "3") {
                                                                                            continue;
                                                                                        }
                                                                                        $strand_jina = $long_lesson_plan[$indie2]->strand_name;
                                                                                        array_push($strand_term_1,[$strand_jina,$long_lesson_plan[$indie2]->index]);
                                                                                    }
                                                                            @endphp
                                                                            @if (count($strand_term_1) >= 1)
                                                                            <div class="col-md-3">
                                                                                <label for="move_term_1" class="form-label"><b>Move To</b></label>
                                                                                <select class="form-control" name="move_term" id="move_term_1">
                                                                                    <option value="[default,{{$long_lesson_plan[$i]->index}}]" hidden>Select Option</option>
                                                                                    <option value="[-1,{{$long_lesson_plan[$i]->index}}]" >At the beginning.</option>
                                                                                    @for ($indie = 0; $indie < count($strand_term_1); $indie++)
                                                                                        @if ($strand_term_1[$indie][1] == $long_lesson_plan[$i]->index)
                                                                                            {{-- <option selected value="{{$strand_term_1[$indie][1]}}" >After {{$strand_term_1[$indie][0]}}</option> --}}
                                                                                        @else
                                                                                            <option value="[{{$strand_term_1[$indie][1]}},{{$long_lesson_plan[$i]->index}}]" >After {{$strand_term_1[$indie][0]}}</option>
                                                                                        @endif
                                                                                    @endfor
                                                                                </select>
                                                                            </div>
                                                                            @endif
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm edit_add_objective_window" id="edit_add_objective_window{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-8 border border-primary p-1 hide my-2" id="edit_objective_record_window{{$term}}_{{$i}}">
                                                                                    <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                    <label for="edit_strands_objectives{{$term}}_{{$i}}" class="form-label">Add Strand`s Objective</label>
                                                                                    <input type="text" class="form-control" id="edit_strands_objectives{{$term}}_{{$i}}" placeholder="Students will be able to...">
                                                                                    <span class="btn btn-primary btn-sm my-1 edit_add_objective" id="edit_add_objective{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" value="{{is_array($long_lesson_plan[$i]->objectives) > 0 ? json_encode($long_lesson_plan[$i]->objectives) : ""}}" name="edit_strands_objectives_holder" id="edit_strands_objectives_holder{{$term}}_{{$i}}">
                                                                                <!-- List group Numbered -->
                                                                                @if (is_array($long_lesson_plan[$i]->objectives)>0)
                                                                                    @if (count($long_lesson_plan[$i]->objectives))
                                                                                        <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                            @for ($ind = 0; $ind < count($long_lesson_plan[$i]->objectives); $ind++)
                                                                                                <li class="list-group-item">{{$long_lesson_plan[$i]->objectives[$ind]}} <span style='cursor:pointer;' class='text-danger trash_edit_obj{{$term}}_{{$i}} trash_obj_del' id='trash_edit_obj_{{$term}}_{{$i}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                            @endfor
                                                                                        </ol>
                                                                                    @else
                                                                                        <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                            <li class='list-group-item text-black'>No objectives set at the moment!</li>
                                                                                        </ol>
                                                                                    @endif
                                                                                @else
                                                                                    <ol class="list-group list-group-numbered" id="edit_strands_obj_list{{$term}}_{{$i}}">
                                                                                        <li class='list-group-item text-black'>No objectives set at the moment!</li>
                                                                                    </ol>
                                                                                @endif
                                                                                <!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label for="period_1" class="form-label"><b>Period</b></label>
                                                                                <div class="input-group">
                                                                                    <input type="number" name="period" value="{{$long_lesson_plan[$i]->period}}" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2_{{$term}}" required>
                                                                                    <span class="input-group-text" id="inputGroupPrepend2_{{$term}}">Weeks</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm edit_learning_materials" id="edit_learning_materials_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-6 border border-primary p-1 hide" id="edit_learning_materials_window_{{$term}}_{{$i}}">
                                                                                    <label for="learning_materials_{{$term}}_{{$i}}" class="form-label">Add Learning Materials</label>
                                                                                    <input type="text" class="form-control" id="learning_materials_{{$term}}_{{$i}}" placeholder="E.g., Kiswahili Mufti">
                                                                                    <span class="btn btn-primary btn-sm my-1 edit_learning_materials_list" id="edit_learning_materials_list_{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" value="{{is_array($long_lesson_plan[$i]->learning_materials) > 0 ? json_encode($long_lesson_plan[$i]->learning_materials) : ""}}" name="edit_learning_materials_holder" id="edit_learning_materials_holder_{{$term}}_{{$i}}">
                                                                                @if (is_array($long_lesson_plan[$i]->learning_materials) > 0)
                                                                                    @if (count($long_lesson_plan[$i]->learning_materials) > 0)
                                                                                        <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                            @for ($ind = 0; $ind < count($long_lesson_plan[$i]->learning_materials); $ind++)
                                                                                                <li class="list-group-item">{{$long_lesson_plan[$i]->learning_materials[$ind]}} <span style='cursor:pointer;' class='text-danger trash_learning_materials{{$term}}_{{$i}} trash_learning_materials_edit' id='trash_lm_{{$term}}_{{$i}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                            @endfor
                                                                                        </ol>
                                                                                    @else
                                                                                        <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                            <li class='list-group-item text-black'>No Learning Materials Posted at the moment!</li>
                                                                                        </ol>
                                                                                    @endif
                                                                                @else
                                                                                    <ol class="list-group list-group-numbered" id="edit_learning_materials_lists_{{$term}}_{{$i}}">
                                                                                        <li class='list-group-item text-black'>No Learning Materials Posted at the moment!</li>
                                                                                    </ol>
                                                                                @endif
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="comment_edit_{{$term}}_{{$i}}" class="form-label">Comments</label>
                                                                                <textarea name="comment" id="comment_edit_{{$term}}_{{$i}}" cols="30" rows="5" class="form-control" placeholder="Comments are written here..">{{$long_lesson_plan[$i]->comment}}</textarea>
                                                                            </div>
                                                                            <div class="colmd-4">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Update Changes</button>
                                                                            </div>
                                                                        </form><!-- End Multi Columns Form -->
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h6><b>Objectives</b> :</h6>
                                                        @if (is_array($long_lesson_plan[$i]->objectives))
                                                            <ul>
                                                                @for ($ind = 0; $ind < count($long_lesson_plan[$i]->objectives); $ind++)
                                                                    <li class="">{{$long_lesson_plan[$i]->objectives[$ind]}}</li>
                                                                @endfor
                                                            </ul>
                                                        @else
                                                            <ul>
                                                                <li class=''>No objectives set at the moment!</li>
                                                            </ul>
                                                        @endif
                                                        @php
                                                            $to += $long_lesson_plan[$i]->period;
                                                        @endphp
                                                            @if ($long_lesson_plan[$i]->period > 0)
                                                                <h6><b>Period : </b> From: Start of Week {{$from}}, To: End of Week {{$to}} ({{$long_lesson_plan[$i]->period}} Weeks)</h6>
                                                            @else
                                                            <h6><b>Period : </b> {{$long_lesson_plan[$i]->period}} Weeks</h6>
                                                            @endif
                                                        @php
                                                            $from += $long_lesson_plan[$i]->period;
                                                        @endphp
                                                        <h6><b>Learning Materials</b> : </h6>
                                                        @if (is_array($long_lesson_plan[$i]->learning_materials) > 0)
                                                            <ul class="">
                                                                @for ($ind = 0; $ind < count($long_lesson_plan[$i]->learning_materials); $ind++)
                                                                    <li class="">{{$long_lesson_plan[$i]->learning_materials[$ind]}}</li>
                                                                @endfor
                                                            </ul>
                                                        @else
                                                            <ul class="">
                                                                <li class=' text-black'>No Learning Materials Posted at the moment!</li>
                                                            </ul>
                                                        @endif
                                                        @if (strlen(trim($long_lesson_plan[$i]->comment)) > 0)
                                                            <h6><b>Comments : </b></h6>
                                                            <p>{{$long_lesson_plan[$i]->comment}}</p>
                                                        @endif
                                                        <hr>
                                                        <p class="text-secondary border border-secondary p-1 rounded">Add Sub-Strands / Sub-Topics : <span  data-bs-toggle="modal" data-bs-target="#add_substrands{{$term}}_{{$i}}" class="btn btn-sm btn-success text-white"><i class="bi bi-plus"></i> Add Sub-Strands / Sub-Topics </span></p>
                                                        <div class="modal fade" id="add_substrands{{$term}}_{{$i}}" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Add Sub-Strands / Sub-Topics</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Multi Columns Form -->
                                                                        <form class="row g-3" method="POST" action="/CreateLessonPlan/addSubStrands">
                                                                            @csrf
                                                                            <div class="col-md-9">
                                                                                <label for="sub_strand_name{{$term}}" class="form-label"><b>Sub-Strands / Sub-Topics Name</b></label>
                                                                                <input type="text" class="form-control" name="sub_strand_name" id="sub_strand_name{{$term}}" placeholder="e.g., Introduction to Mathematics">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label for="sub_strand_code{{$term}}" class="form-label"><b>Sub-Strands Code</b></label>
                                                                                <input type="text" class="form-control" name="sub_strand_code" id="sub_strand_code{{$term}}" placeholder="e.g., IM101">
                                                                            </div>
                                                                            <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                            <input type="hidden" name="class_plan" value="{{$class}}">
                                                                            <input type="hidden" name="term_selected" value="1">
                                                                            <input type="hidden" name="plan_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                            <div class="col-md-12">
                                                                                {{-- add objectives --}}
                                                                                <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm add_objective_window" id="add_objective_window_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-8 border border-primary p-1 hide my-2" id="objective_record_window_{{$term}}_{{$i}}">
                                                                                    <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                    <label for="strands_objectives{{$term}}_{{$i}}" class="form-label">Add Strand`s Objective</label>
                                                                                    <input type="text" class="form-control" id="strands_objectives{{$term}}_{{$i}}" placeholder="Students will be able to...">
                                                                                    <span class="btn btn-primary btn-sm my-1 add_objective" id="add_objective{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" name="sub_strands_objectives_holder" id="strands_objectives_holder{{$term}}_{{$i}}">
                            
                                                                                <!-- List group Numbered -->
                                                                                <ol class="list-group list-group-numbered" id="strands_obj_list{{$term}}_{{$i}}">
                                                                                    <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                </ol><!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label for="period_{{$term}}_{{$i}}" class="form-label"><b>Period</b></label>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="period_{{$term}}_{{$i}}" name="sub_strand_period" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                                                                                    <span class="input-group-text" id="inputGroupPrepend2">
                                                                                        <select name="duration_unit" id="duration_unit" class="form-control">
                                                                                            <option value="Days">Days</option>
                                                                                            <option value="Weeks">Weeks</option>
                                                                                            <option value="Months">Months</option>
                                                                                        </select>
                                                                                    </span>
                                                                                  </div>
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm add_learning_materials" id="add_learning_materials_{{$term}}_{{$i}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                <div class="col-md-6 border border-primary p-1 hide" id="add_learning_materials_window_{{$term}}_{{$i}}">
                                                                                    <label for="learning_materials{{$term}}_{{$i}}" class="form-label">Add Learning Materials</label>
                                                                                    <input type="text" class="form-control" id="learning_materials{{$term}}_{{$i}}" placeholder="E.g., Kiswahili Mufti">
                                                                                    <span class="btn btn-primary btn-sm my-1 add_learning_materials_list" id="add_learning_materials_list{{$term}}_{{$i}}"><i class="bi bi-save"></i> Save</span>
                                                                                </div>
                                                                                <input type="hidden" name="sub_strand_learning_materials_holder" id="learning_materials_holder{{$term}}_{{$i}}">
                                                                                <!-- List group Numbered -->
                                                                                <ol class="list-group list-group-numbered" id="learning_materials_lists{{$term}}_{{$i}}">
                                                                                    <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                </ol><!-- End List group Numbered -->
                                                                            </div>
                                                                            <div class="col-md-12">
                                                                                <label for="sub_strands_comment{{$term}}_{{$i}}" class="form-label"><b>Sub-Strands / Sub-Topics Comments</b></label>
                                                                                <textarea name="sub_strands_comment" id="sub_strands_comment{{$term}}_{{$i}}" cols="30" rows="5" class="form-control" placeholder="Sub-Strands / Sub-Topics Comments are written here."></textarea>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <button type="submit" class="btn btn-primary">Save Sub-Strands / Sub-Topics</button>
                                                                                {{-- <button type="button" class="btn btn-secondary"
                                                                                    data-bs-dismiss="modal">Close</button> --}}
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                            </div>
                                                                        </form><!-- End Multi Columns Form -->
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        @if (count($long_lesson_plan[$i]->sub_strands) > 0)
                                                            @for ($index = 0; $index < count($long_lesson_plan[$i]->sub_strands); $index++)
                                                                @php
                                                                    $sub_strands = $long_lesson_plan[$i]->sub_strands[$index];
                                                                @endphp
                                                                <div class="container border border-primary rounded p-2">
                                                                            <div class="row">
                                                                                <div class="col-md-9">
                                                                                    <h6><b>Sub-Strand / Sub-Topic {{$index+1}} </b> <i
                                                                                        class="bi bi-pen edit-pen"  data-bs-toggle="modal" data-bs-target="#edit_substrands_{{$term}}_{{$i}}_{{$index}}" ></i> : {{$sub_strands->name}}</h6>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <span id="delete_sub_strand{{$term}}_{{$i}}_{{$index}}" class="delete_sub_strand btn btn-primary"><i class="bi bi-trash"></i> Delete</span>
                                                                                    <div class="container border border-danger rounded p-1 my-1 hide" id="delete_sub_strand_window{{$term}}_{{$i}}_{{$index}}">
                                                                                        <p class="text-bold text-danger">Do you want to permanently delete <b>{{$sub_strands->name}}</b>?</p>
                                                                                        <a href="/deleteSubStrand/Subject/{{$lesson_id}}/Class/{{$class}}/Strand/{{$sub_strands->sub_index}}" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Sub-Strand: {{$sub_strands->name}}"><i class="bi bi-trash"></i> Delete</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal fade" id="edit_substrands_{{$term}}_{{$i}}_{{$index}}" tabindex="-1">
                                                                                <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title">Edit "{{$sub_strands->name}}"</h5>
                                                                                            <button type="button" class="btn-close"
                                                                                                data-bs-dismiss="modal"
                                                                                                aria-label="Close"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            <!-- Multi Columns Form -->
                                                                                            <form class="row g-3" method="POST" action="/EditLessonPlan/editSubStrands">
                                                                                                @csrf
                                                                                                <div class="col-md-9">
                                                                                                    <label for="sub_strand_name_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands / Sub-Topics Name</b></label>
                                                                                                    <input type="text" class="form-control" name="sub_strand_name" id="sub_strand_name_{{$term}}_{{$i}}_{{$index}}" value="{{$sub_strands->name}}" placeholder="e.g., Introduction to Mathematics">
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <label for="sub_strand_code_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands Code</b></label>
                                                                                                    <input type="text" class="form-control" name="sub_strand_code" value="{{$sub_strands->code}}" id="sub_strand_code_{{$term}}_{{$i}}_{{$index}}" placeholder="e.g., IM101">
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <label for="substrand_locale_opt{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Move to :</b></label>
                                                                                                    <select name="substrand_locale_opt" id="substrand_locale_opt{{$term}}_{{$i}}_{{$index}}" class="form-control substrand_locale_opt">
                                                                                                        <option value="Different Strand">Different Strand</option>
                                                                                                        <option value="In Strand">In Strand</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-md-4" id="different_strand_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    <label for="select_strand{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Select Different Strand : </b></label>
                                                                                                    <select name="select_strand" id="select_strand{{$term}}_{{$i}}_{{$index}}" class="form-control">
                                                                                                        @for ($indx = 0; $indx < count($long_lesson_plan); $indx++)
                                                                                                            <option {{$long_lesson_plan[$indx]->index == $long_lesson_plan[$i]->index ? "selected" : ""}} value="{{$long_lesson_plan[$indx]->index}}">{{$indx+1}}). {{$long_lesson_plan[$indx]->strand_name}}</option>
                                                                                                        @endfor
                                                                                                    </select>
                                                                                                </div>
                                                                                                <div class="col-md-4 hide" id="different_loc_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    <label for="select_location{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Select Another Location : </b></label>
                                                                                                    <select name="select_location" id="select_location{{$term}}_{{$i}}_{{$index}}" class="form-control">
                                                                                                        @if ($index != 0)
                                                                                                            <option value="-1">At the beginning</option>
                                                                                                        @endif
                                                                                                        @php
                                                                                                            $sub_str = $long_lesson_plan[$i]->sub_strands;
                                                                                                        @endphp
                                                                                                        @for ($indx = 0; $indx < count($sub_str); $indx++)
                                                                                                            <option {{$sub_str[$indx]->sub_index == $sub_strands->sub_index ? "selected" : ""}} value="{{$sub_str[$indx]->sub_index}}">After {{$sub_str[$indx]->name}}</option>
                                                                                                        @endfor
                                                                                                    </select>
                                                                                                </div>
                                                                                                <input type="hidden" name="subject_id" value="{{$lesson_id}}">
                                                                                                <input type="hidden" name="class_plan" value="{{$class}}">
                                                                                                <input type="hidden" name="term_selected" value="1">
                                                                                                <input type="hidden" name="plan_index" value="{{$long_lesson_plan[$i]->index}}">
                                                                                                <input type="hidden" name="date_created" value="{{$long_lesson_plan[$i]->date_created}}">
                                                                                                <input type="hidden" name="substrand_index" value="{{$sub_strands->sub_index}}">
                                                                                                <div class="col-md-12">
                                                                                                    {{-- add objectives --}}
                                                                                                    <label for="input_1" class="form-label"><b>Objectives</b> <span class="btn btn-primary btn-sm add_objective_window_{{$term}}_{{$i}} add_object_windows" id="add_objective_window_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                                    <div class="col-md-8 border border-primary p-1 hide my-2" id="objective_record_window_{{$term}}_{{$i}}_{{$index}}">
                                                                                                        <p><b>Note:</b><br>Write one objective at a time then when done save.</p>
                                                                                                        <label for="strands_objectives_{{$term}}_{{$i}}_{{$index}}" class="form-label">Add Strand`s Objective</label>
                                                                                                        <input type="text" class="form-control" id="strands_objectives_{{$term}}_{{$i}}_{{$index}}" placeholder="Students will be able to...">
                                                                                                        <span class="btn btn-primary btn-sm my-1 btn_add_obj{{$term}}_{{$i}} btn_add_obj" id="btn_add_obj_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-save"></i> Save</span>
                                                                                                    </div>
                                                                                                    <input type="hidden" value="{{count($sub_strands->objectives) > 0 ? json_encode($sub_strands->objectives) : "[]"}}" name="sub_strands_objectives_holder" id="strands_objectives_holder_{{$term}}_{{$i}}_{{$index}}">

                                                                                                    @if (count($sub_strands->objectives) > 0)
                                                                                                        <ol class="list-group list-group-numbered" id="strands_obj_list_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            @for ($inds = 0; $inds < count($sub_strands->objectives); $inds++)
                                                                                                                <li class='list-group-item text-black'>{{$sub_strands->objectives[$inds]}}. <span style='cursor:pointer;' class='text-danger trash_obj{{$term}}_{{$i}}_{{$index}} getTrashObjectives' id = 'trash_obj_{{$term}}_{{$i}}_{{$index}}_{{$ind}}'><i class='bi bi-trash'></i></span></li>
                                                                                                            @endfor
                                                                                                        </ol>   
                                                                                                    @else
                                                                                                        <ol class="list-group list-group-numbered" id="strands_obj_list_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                                        </ol>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <label for="period_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Period</b></label>

                                                                                                    <div class="input-group">
                                                                                                        <input type="number" id="period_{{$term}}_{{$i}}_{{$index}}" value="{{explode(" ",$sub_strands->period)[0]}}" name="sub_strand_period" class="form-control" id="validationDefaultUsername" aria-describedby="inputGroupPrepend2" required>
                                                                                                        <span class="input-group-text" id="inputGroupPrepend2">
                                                                                                            <select name="duration_unit" id="duration_unit" class="form-control">
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Days"? "selected" : ""}} value="Days">Days</option>
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Weeks"? "selected" : ""}} value="Weeks">Weeks</option>
                                                                                                                <option {{explode(" ",$sub_strands->period)[1]=="Months"? "selected" : ""}} value="Months">Months</option>
                                                                                                            </select>
                                                                                                        </span>
                                                                                                      </div>
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <label for="input_1" class="form-label"><b>Learning Materials</b> <span class="btn btn-primary btn-sm add_learning_material_{{$term}}_{{$i}} lm_lists" id="add_learning_material_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-plus"></i> Add</span></label>
                                                                                                    <div class="col-md-6 border border-primary p-1 hide" id="add_learning_materials_window_{{$term}}_{{$i}}_{{$index}}">
                                                                                                        <label for="learning_material_{{$term}}_{{$i}}_{{$index}}" class="form-label">Add Learning Materials</label>
                                                                                                        <input type="text" class="form-control" id="learning_material_{{$term}}_{{$i}}_{{$index}}" placeholder="E.g., Kiswahili Mufti">
                                                                                                        <span class="btn btn-primary btn-sm my-1 btn_add_lm_list_{{$term}}_{{$i}} btn_add_lm_list" id="btn_add_lm_list_{{$term}}_{{$i}}_{{$index}}"><i class="bi bi-save"></i> Save</span>
                                                                                                    </div>
                                                                                                    <input type="hidden" value="{{count($sub_strands->learning_materials) > 0 ? json_encode($sub_strands->learning_materials) : "[]"}}" name="sub_strand_learning_materials_holder" id="learning_materials_holder_{{$term}}_{{$i}}_{{$index}}">
                                                                                                    @if (count($sub_strands->learning_materials) > 0)
                                                                                                        <ol class="list-group list-group-numbered" id="learning_materials_lists_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            @for ($inds = 0; $inds < count($sub_strands->learning_materials); $inds++)
                                                                                                                <li class='list-group-item text-black'>{{$sub_strands->learning_materials[$inds]}} <span style='cursor:pointer;' class='text-danger trash_lm_lst_{{$term}}_{{$i}}_{{$index}} trash_lm_list' id = 'lmlist_{{$term}}_{{$i}}_{{$index}}_{{$inds}}'><i class='bi bi-trash'></i></span></li>
                                                                                                            @endfor
                                                                                                        </ol>   
                                                                                                    @else
                                                                                                        <ol class="list-group list-group-numbered" id="learning_materials_lists_{{$term}}_{{$i}}_{{$index}}">
                                                                                                            <li class='list-group-item text-black'>No lists available at the moment.</li>
                                                                                                        </ol>
                                                                                                    @endif
                                                                                                </div>
                                                                                                <div class="col-md-12">
                                                                                                    <label for="sub_strands_comment_{{$term}}_{{$i}}_{{$index}}" class="form-label"><b>Sub-Strands / Sub-Topics Comments</b></label>
                                                                                                    <textarea name="sub_strands_comment" id="sub_strands_comment_{{$term}}_{{$i}}_{{$index}}" cols="30" rows="5" class="form-control" placeholder="Sub-Strands / Sub-Topics Comments are written here.">{{$sub_strands->comments}}</textarea>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <button type="submit" class="btn btn-primary">Save Sub-Strands / Sub-Topics</button>
                                                                                                    {{-- <button type="button" class="btn btn-secondary"
                                                                                                        data-bs-dismiss="modal">Close</button> --}}
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                </div>
                                                                                            </form><!-- End Multi Columns Form -->
                                                                                        </div>
                                                                                        <div class="modal-footer">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    <h6><b>Sub-Strand Code </b> : {{$sub_strands->code}}</h6>
                                                                    <h6><b>Objectives</b> :</h6>
                                                                    <ul>
                                                                        @if (count($sub_strands->objectives) > 0)
                                                                            @for ($index_2 = 0; $index_2 < count($sub_strands->objectives); $index_2++)
                                                                                <li>{{$sub_strands->objectives[$index_2]}}</li>
                                                                            @endfor
                                                                        @else
                                                                            <li>No Objectives Set at the moment!</li>
                                                                        @endif
                                                                    </ul>
                                                                    <h6><b>Period </b>: {{$sub_strands->period}}</h6>
                                                                    <h6><b>Learning Materials</b> :</h6>
                                                                    <ul>
                                                                        @if (count($sub_strands->learning_materials) > 0)
                                                                            @for ($index_2 = 0; $index_2 < count($sub_strands->learning_materials); $index_2++)
                                                                                <li>{{$sub_strands->learning_materials[$index_2]}}</li>
                                                                            @endfor
                                                                        @else
                                                                            <li>No Learning Materials set at the moment!</li>
                                                                        @endif
                                                                    </ul>

                                                                    @if (strlen(trim($sub_strands->comments)) > 0)
                                                                        <h6><b>Comments : </b></h6>
                                                                        <p>{{$sub_strands->comments}}</p>
                                                                    @endif
                                                                </div>
                                                                @if ($index < (count($long_lesson_plan[$i]->sub_strands)-1))
                                                                    <hr class="w-50 mx-auto mb-1 border-primary">
                                                                    <hr class="w-50 mx-auto mt-0 border-primary">
                                                                @else
                                                                    <hr class="w-50 mx-auto mb-1 border-primary">
                                                                @endif
                                                            @endfor
                                                        @else
                                                            <p class="text-secondary text-center">No Sub-Strands set for this Strand!</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    @else
                                        <p class="text-secondary border border-secondary p-1 rounded">No Strands/Topics have been set for this subject in Term Three.</p>
                                    @endif
                                    {{-- strands ends from here --}}
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <p class="text-secondary"> <b>Opening Date</b>: {{date("D dS M Y",strtotime($academic_calender[2]->start_time))}}<br> <b>Closing Date</b>: {{date("D dS M Y",strtotime($academic_calender[2]->closing_date))}}<br><b>Term End Date</b>: {{date("D dS M Y",strtotime($academic_calender[2]->end_time))}} <br><b>Weeks:</b>{{get_weeks_between_dates($academic_calender[2]->start_time, $academic_calender[2]->end_time);}}</p>
                        </div>
                    </div>
                    {{-- ebd term two --}}
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
    <script src="/assets/js/tr_js/lessonplan.js"></script>

</body>

</html>

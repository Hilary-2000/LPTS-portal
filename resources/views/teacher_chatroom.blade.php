<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">

    <title>Discussion Forum
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
    {{-- <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="/assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="/assets/vendor/simple-datatables/style.css" rel="stylesheet">

    
    <!-- Bootstrap Css -->
    <link href="/assets/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/assets/assets/css/icons.min.css" rel="stylesheet" type="text/css"/>
    <!-- App Css-->
    <link href="/assets/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <!-- App js -->
    <script src="/assets/assets/js/plugin.js"></script>

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
                <a class="nav-link collapsed" href="/Teacher/LessonPlan">
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
                <a class="nav-link " href="/Teacher/DiscussionForum">
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
            <h1>Chat Room</h1>
            {{-- <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item active">Chat Room</li>
                </ol>
            </nav> --}}
        </div><!-- End Page Title -->

        <section class="section dashboard p-0">
            <p class="text-success">{{session("communication_success") != null ? session("communication_success") : ""}}</p>
            <p class="text-danger">{{session("communication_error") != null ? session("communication_error") : ""}}</p>
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-lg-flex">
                        <div class="chat-leftsidebar me-lg-4">
                            <div class="">
                                <div class="border-bottom">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 align-self-center me-3">
                                            {{-- <img src="assets/images/users/avatar-1.jpg" class="avatar-xs rounded-circle" alt=""> --}}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-15 mb-1">Chat Room</h5>
                                            {{-- <p class="text-muted mb-0"><i class="mdi mdi-circle text-success align-middle me-1"></i> Active</p> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="search-box chat-search-box py-4">
                                    <div class="position-relative">
                                        <input type="text" class="form-control" placeholder="Search...">
                                        <i class="bx bx-search-alt search-icon"></i>
                                    </div>
                                </div>

                                <div class="chat-leftsidebar-nav">
                                    <ul class="nav nav-pills nav-justified">
                                        <li class="nav-item">
                                            <a href="#student_chats" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                                <i class="bx bx-chat font-size-20 d-sm-none"></i>
                                                <span class="d-none d-sm-block">Student</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#parent_chats" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <i class="bx bx-group font-size-20 d-sm-none"></i>
                                                <span class="d-none d-sm-block">Parent</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#teacher_chats" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                                                <span class="d-none d-sm-block">Teacher</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#contact_list" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                                                <span class="d-none d-sm-block">Contacts</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content py-4">
                                        <div class="tab-pane show active" id="student_chats">
                                            <div>
                                                @php
                                                    function trimText($text, $maxLength, $trimmarker = '...') {
                                                        return mb_strimwidth($text, 0, $maxLength, $trimmarker);
                                                    }
                                                @endphp 
                                                <h5 class="font-size-14 mb-3">Student Chats</h5>
                                                <ul class="list-unstyled chat-list" id="all_student_chats" data-simplebar style="max-height: 410px;">
                                                    @if (count($student_chats) > 0)
                                                        @foreach ($student_chats as $key => $student_chat)
                                                            <li  class="student_chats {{$key == 0 ? "active" : ""}}" id="student_chats_{{$student_chat->student_adm_no}}">
                                                                <input type="hidden" id="chat_student_details_{{$student_chat->student_adm_no}}" value="{{json_encode($student_chat->student_detail)}}">
                                                                <a href="javascript: void(0);">
                                                                    <div class="d-flex">
                                                                        <div class="flex-shrink-0 align-self-center me-3">
                                                                            <i class="mdi mdi-circle font-size-10"></i>
                                                                        </div>
                                                                        <div class="avatar-xs align-self-center me-3">
                                                                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                                {{substr($student_chat->student_name,0,1)}}
                                                                            </span>
                                                                        </div>
                                                                        
                                                                        <div class="flex-grow-1 overflow-hidden">
                                                                            <h5 class="text-truncate font-size-14 mb-1">{{$student_chat->student_name}}</h5>
                                                                            <p class="text-truncate mb-0">{{trimText($student_chat->last_chat->chat_content,35)}}</p>
                                                                        </div>
                                                                        <div class="font-size-11">{{getTimeAgo($student_chat->last_chat->date_sent)}}</div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    @else
                                                        <li class="active">
                                                            <a href="javascript: void(0);">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 align-self-center me-3">
                                                                        {{-- <i class="mdi mdi-circle text-success font-size-10"></i> --}}
                                                                    </div>
                                                                    <div class="flex-shrink-0 align-self-center me-3">
                                                                        <div class="avatar-xs">
                                                                            <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                                N
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="flex-grow-1 overflow-hidden">
                                                                        <h5 class="text-truncate font-size-14 mb-1">No Student Chats found! <br>Start sending Messages</h5>
                                                                        {{-- <p class="text-truncate mb-0">This theme is awesome!</p> --}}
                                                                    </div>
                                                                    {{-- <div class="font-size-11">24 min</div> --}}
                                                                </div>
                                                            </a>
                                                        </li>
                                                    @endif

                                                </ul>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="parent_chats">
                                            <h5 class="font-size-14 mb-3">Parents Chats</h5>
                                            <ul class="list-unstyled chat-list" data-simplebar style="max-height: 410px;">
                                                @if (count($parent_chats) > 0)
                                                    @foreach ($parent_chats as $key => $parent_chat)
                                                        <li class="{{$key == 0 ? "active" : ""}}">
                                                            <a href="javascript: void(0);">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 align-self-center me-3">
                                                                        <i class="mdi mdi-circle font-size-10"></i>
                                                                    </div>
                                                                    <div class="avatar-xs align-self-center me-3">
                                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                            S
                                                                        </span>
                                                                    </div>
                                                                    
                                                                    <div class="flex-grow-1 overflow-hidden">
                                                                        <h5 class="text-truncate font-size-14 mb-1">Steven Franklin</h5>
                                                                        <p class="text-truncate mb-0">Hey! there I'm available</p>
                                                                    </div>
                                                                    <div class="font-size-11">05 min</div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="active">
                                                        <a href="javascript: void(0);">
                                                            <div class="d-flex">
                                                                <div class="flex-shrink-0 align-self-center me-3">
                                                                    {{-- <i class="mdi mdi-circle text-success font-size-10"></i> --}}
                                                                </div>
                                                                <div class="flex-shrink-0 align-self-center me-3">
                                                                    <div class="avatar-xs">
                                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                            N
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <h5 class="text-truncate font-size-14 mb-1">No Parent Chats found! <br>Start sending Messages</h5>
                                                                    {{-- <p class="text-truncate mb-0">This theme is awesome!</p> --}}
                                                                </div>
                                                                {{-- <div class="font-size-11">24 min</div> --}}
                                                            </div>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="tab-pane" id="teacher_chats">
                                            <h5 class="font-size-14 mb-3">Teacher Chats</h5>
                                            <ul class="list-unstyled chat-list" data-simplebar style="max-height: 410px;">
                                                @if (count($parent_chats) > 0)
                                                    @foreach ($parent_chats as $key => $parent_chat)
                                                        <li class="{{$key == 0 ? "active" : ""}}">
                                                            <a href="javascript: void(0);">
                                                                <div class="d-flex">
                                                                    <div class="flex-shrink-0 align-self-center me-3">
                                                                        <i class="mdi mdi-circle font-size-10"></i>
                                                                    </div>
                                                                    <div class="avatar-xs align-self-center me-3">
                                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                            S
                                                                        </span>
                                                                    </div>
                                                                    
                                                                    <div class="flex-grow-1 overflow-hidden">
                                                                        <h5 class="text-truncate font-size-14 mb-1">Steven Franklin</h5>
                                                                        <p class="text-truncate mb-0">Hey! there I'm available</p>
                                                                    </div>
                                                                    <div class="font-size-11">05 min</div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="active">
                                                        <a href="javascript: void(0);">
                                                            <div class="d-flex">
                                                                <div class="flex-shrink-0 align-self-center me-3">
                                                                    {{-- <i class="mdi mdi-circle text-success font-size-10"></i> --}}
                                                                </div>
                                                                <div class="flex-shrink-0 align-self-center me-3">
                                                                    <div class="avatar-xs">
                                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                            N
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <h5 class="text-truncate font-size-14 mb-1">No Teacher Chats found! <br>Start sending Messages</h5>
                                                                    {{-- <p class="text-truncate mb-0">This theme is awesome!</p> --}}
                                                                </div>
                                                                {{-- <div class="font-size-11">24 min</div> --}}
                                                            </div>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                            {{-- <div  data-simplebar style="max-height: 410px;">

                                                <div>
                                                    <div class="avatar-xs mb-3">
                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                            A
                                                        </span>
                                                    </div>

                                                    <ul class="list-unstyled chat-list">
                                                        <li>
                                                            <a href="javascript: void(0);">
                                                                <h5 class="font-size-14 mb-0">Adam Miller</h5>
                                                            </a>
                                                        </li>
    
                                                        <li>
                                                            <a href="javascript: void(0);">
                                                                <h5 class="font-size-14 mb-0">Alfonso Fisher</h5>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="mt-4">
                                                    <div class="avatar-xs mb-3">
                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                            B
                                                        </span>
                                                    </div>

                                                    <ul class="list-unstyled chat-list">
                                                        <li>
                                                            <a href="javascript: void(0);">
                                                                <h5 class="font-size-14 mb-0">Bonnie Harney</h5>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="mt-4">
                                                    <div class="avatar-xs mb-3">
                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                            C
                                                        </span>
                                                    </div>

                                                    <ul class="list-unstyled chat-list">
                                                        <li>
                                                            <a href="javascript: void(0);">
                                                                <h5 class="font-size-14 mb-0">Charles Brown</h5>
                                                            </a>
                                                            <a href="javascript: void(0);">
                                                                <h5 class="font-size-14 mb-0">Carmella Jones</h5>
                                                            </a>
                                                            <a href="javascript: void(0);">
                                                                <h5 class="font-size-14 mb-0">Carrie Williams</h5>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="mt-4">
                                                    <div class="avatar-xs mb-3">
                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                            D
                                                        </span>
                                                    </div>

                                                    <ul class="list-unstyled chat-list">
                                                        <li>
                                                            <a href="javascript: void(0);">
                                                                <h5 class="font-size-14 mb-0">Dolores Minter</h5>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div> --}}
                                        </div>

                                        <div class="tab-pane" id="contact_list">
                                            <h5 class="font-size-14 mb-3">Start Chat</h5>
                                            <ul class="nav nav-pills nav-justified mb-2">
                                                <li class="nav-item">
                                                    <a href="#student_contacts" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                                        <i class="bx bx-chat font-size-20 d-sm-none"></i>
                                                        <span class="d-none d-sm-block">Student</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#parent_contacts" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                        <i class="bx bx-group font-size-20 d-sm-none"></i>
                                                        <span class="d-none d-sm-block">Parent</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#teacher_contacts" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                                        <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                                                        <span class="d-none d-sm-block">Teacher</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content py-4">
                                                <div class="tab-pane show active" id="student_contacts">
                                                    <div  data-simplebar style="max-height: 380px;">
                                                        @foreach ($student_contacts as $key => $student_contact)
                                                            @if (count($student_contact->students) > 0)
                                                                <div>
                                                                    <div class="avatar-xs mb-3">
                                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                            @php
                                                                                $class_accronym = explode(" ",$student_contact->class_name);
                                                                                $accronym = count($class_accronym) >= 2 ? substr($class_accronym[0],0,1)."".substr($class_accronym[1],0,1) : substr($student_contact->class_name,0,1);
                                                                            @endphp
                                                                            {{$accronym}}
                                                                        </span>
                                                                    </div>
                
                                                                    <ul class="list-unstyled chat-list">
                                                                        @foreach ($student_contact->students as $key_student => $student)
                                                                            <li class="send_message_student" id="send_message_student_{{$student->adm_no}}">
                                                                                <a href="javascript: void(0);">
                                                                                    <input type="hidden" id="student_details_{{$student->adm_no}}" value="{{json_encode($student)}}">
                                                                                    <h5 class="font-size-14 mb-0">{{$key_student+1}}. {{$student->student_fullname}} - {{$student->adm_no}}</h5>
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @else
                                                                <div>
                                                                    <div class="avatar-xs mb-3">
                                                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                            @php
                                                                                $class_accronym = explode(" ",$student_contact->class_name);
                                                                                $accronym = count($class_accronym) >= 2 ? substr($class_accronym[0],0,1)."".substr($class_accronym[1],0,1) : substr($student_contact->class_name,0,1);
                                                                            @endphp
                                                                            {{$accronym}}
                                                                        </span>
                                                                    </div>
                
                                                                    <ul class="list-unstyled chat-list">
                                                                        <li>
                                                                            <a href="javascript: void(0);">
                                                                                <h5 class="font-size-14 mb-0">No Students Present!</h5>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="tab-pane show" id="parent_contacts">
                                                    <div  data-simplebar style="max-height: 380px;">
                                                        @foreach ($parent_contacts as $key => $parent_contact)
                                                            <div>
                                                                <div class="avatar-xs mb-3">
                                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                        {{$parent_contact->letter}}
                                                                    </span>
                                                                </div>
            
                                                                <ul class="list-unstyled chat-list">
                                                                    @foreach ($parent_contact->parents as $key_parent => $parent)
                                                                        <li>
                                                                            <a href="javascript: void(0);">
                                                                                <h5 class="font-size-14 mb-0">{{ucwords(strtolower($parent->parent_name))}}</h5>
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                
                                                                    <li>
                                                                        <a href="javascript: void(0);">
                                                                            <h5 class="font-size-14 mb-0">Alfonso Fisher</h5>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="tab-pane show" id="teacher_contacts">
                                                    <div  data-simplebar style="max-height: 380px;">
                                                        @foreach ($staff_contacts as $staff_contact)
                                                            <div>
                                                                <div class="avatar-xs mb-3">
                                                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                                                        {{$staff_contact->letter}}
                                                                    </span>
                                                                </div>
            
                                                                <ul class="list-unstyled chat-list">
                                                                    @foreach ($staff_contact->teachers as $key => $teacher)
                                                                        <li>
                                                                            <a href="javascript: void(0);">
                                                                                <h5 class="font-size-14 mb-0">{{ucwords(strtolower($teacher->staff_name))}}</h5>
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-100 user-chat my-2">
                            <div class="card">
                                <div class="p-4 border-bottom ">
                                    <div class="row">
                                        <div class="col-md-6 col-7">
                                            <input type="hidden" id="receipient_id_message">
                                            <h5 class="font-size-15 mb-1" id="receipient_name" >Select a Conversation</h5>
                                            <p class="mb-0 badge bg-success" id="receipient_type_flag"><b>Not Selected!</b></p>
                                        </div>
                                        <div class="col-md-6 col-5">
                                            <ul class="list-inline user-chat-nav text-end mb-0">
                                                <li class="list-inline-item d-none d-sm-inline-block">
                                                    <div class="dropdown">
                                                        <button class="btn nav-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-search-alt-2"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-md">
                                                            <form class="p-3">
                                                                <div class="form-group m-0">
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                                                        
                                                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-inline-item  d-none d-sm-inline-block">
                                                    <div class="dropdown">
                                                        <button class="btn nav-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-cog"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="#">View Profile</a>
                                                            <a class="dropdown-item" href="#">Clear chat</a>
                                                            <a class="dropdown-item" href="#">Muted</a>
                                                            <a class="dropdown-item" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="list-inline-item">
                                                    <div class="dropdown">
                                                        <button class="btn nav-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="#">Action</a>
                                                            <a class="dropdown-item" href="#">Another action</a>
                                                            <a class="dropdown-item" href="#">Something else</a>
                                                        </div>
                                                    </div>
                                                </li>
                                                
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="card position-absolute w-100 h-100 bg-gray top-0 start-0 d-none" id="chat_loaders" style="z-index: 2; background-color: black; opacity: 0.2;">
                                        <img class="m-auto" src="/assets/img/load2.gif" alt="Loading" width="100" srcset="">
                                    </div>
                                    <div class="chat-conversation p-3">
                                        <ul class="list-unstyled mb-0 w-90" data-simplebar style="max-height: 486px;min-height: 486px;" id="message_contents">
                                            <li> 
                                                <div class="chat-day-title">
                                                    <span class="title">Select a conversation to proceed!</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="p-3 chat-input-section">
                                        <div class="row">
                                            <div class="col">
                                                <div class="position-relative">
                                                    {{-- <input type="text" id="message_content" class="form-control chat-input" placeholder="Enter Message..."> --}}
                                                    <textarea id="message_content" cols="30" rows="1" class="form-control chat-input" placeholder="Enter Message..."></textarea>
                                                    <div class="chat-input-links" id="tooltip-container">
                                                        <ul class="list-inline mb-0">
                                                            {{-- <li class="list-inline-item"><a href="javascript: void(0);" title="Emoji"><i class="mdi mdi-emoticon-happy-outline"></i></a></li>
                                                            <li class="list-inline-item"><a href="javascript: void(0);" title="Images"><i class="mdi mdi-file-image-outline"></i></a></li>
                                                            <li class="list-inline-item"><a href="javascript: void(0);" title="Add Files"><i class="mdi mdi-file-document-outline"></i></a></li> --}}
                                                        </ul>
                                                    </div>
                                                    <div id="error_message_placeholder">
                                                        <p class="text-success px-2"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" id="send_message" class="btn btn-primary btn-rounded chat-send w-md waves-effect waves-light"><span class="d-none d-sm-inline-block me-2">Send</span> <i class="mdi mdi-send"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
    <script src="/assets/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="/assets/js/tr_js/chat_room.js"></script>
    <!-- JAVASCRIPT -->
    {{-- <script src="/assets/assets/libs/jquery/jquery.min.js"></script>
    <script src="/assets/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="/assets/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="/assets/assets/libs/node-waves/waves.min.js"></script> --}}

    {{-- <script src="/assets/assets/js/app.js"></script> --}}

</body>

</html>

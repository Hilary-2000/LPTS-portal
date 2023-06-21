<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Mark Assignments for {{ $student_names }} - {{ $assignment_details->name }} - {{ $subject_name }} -
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
                <a class="nav-link collapsed" href="/Teacher/QuestionBank">
                    <i class="bi bi-columns"></i>
                    <span>Question Bank</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link " href="/Teacher/Assignment">
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
            <h1>Assignments</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/Assignment">Subjects I Teach</a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/Assignments/{{$assignment_id}}/Create/{{$class_details}}">Assignments I Set</a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Assignments/Mark/{{ $assignment_id }}">Student List</a>
                    </li>
                    <li class="breadcrumb-item active">Mark Assignments for {{ $student_names }} -
                        {{ $subject_name }} - {{ $selected_class }}</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                @php
                    function isJson($string)
                    {
                        return is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))) ? true : false;
                    }
                @endphp
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <a href="/Assignments/Mark/{{ $assignment_id }}"
                                    class="btn btn-secondary btn-sm my-2"><i class="bi bi-arrow-left"></i> Back to
                                    Student Lists</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>Mark <b>{{ucwords(strtolower($assignment_details->name))}}</b> done by <b>{{ucwords(strtolower($student_names))}}</b>.</li>
                                </ul>
                            </div>
                        </div>
                        @php
                            function selectedData($my_answers,$question_id){
                                $answers_set = [];
                                $student_data = session("student_information");
                                if (isJson($my_answers)) {
                                    $my_answer = json_decode($my_answers);
                                    for ($ind=0; $ind < count($my_answer); $ind++) {
                                        if ($my_answer[$ind]->linked == $question_id) {
                                            $answers_set = $my_answer[$ind];
                                            break;
                                        }
                                    }
                                }elseif (!empty($my_answers)) {
                                    $my_answer = ($my_answers);
                                    for ($ind=0; $ind < count($my_answer); $ind++) {
                                        if ($my_answer[$ind]->linked == $question_id) {
                                            $answers_set = $my_answer[$ind];
                                            break;
                                        }
                                    }
                                }
                                return $answers_set;
                            }
                        @endphp
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <h5 class="card-title">Mark Assignment for "{{$student_names}}" - {{ $subject_name }} -
                                        {{ $selected_class }}</h5>
                                    
                                    {{-- Alert assignment --}}
                                    <div class="modal fade" id="gatabaki_alert" tabindex="-1">
                                      <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                          <div class="modal-header">
                                            <h5 class="modal-title" id="title_image">Re-do "{{$assignment_details->name}}" confirmation.</h5>
                                            <input type="hidden" value="0" id="my_ids">
                                            <input type="hidden" value="[]" id="all_images">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                          </div>
                                          <div class="modal-body">
                                            Confirm that you want "<b>{{$student_names}}</b>" to re-do the test.
                                          </div>
                                          <div class="modal-footer">
                                            <a href="/Teacher/Redo/{{$assignment_id}}/{{$adm_no}}" class="btn btn-primary btn-sm"><i class="bi bi-arrow-repeat"></i> Re-Do Assignment</a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                          </div>
                                        </div>
                                      </div>
                                    </div><!-- End Extra Large Modal-->
                                    {{-- ends here --}}


                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#gatabaki_alert"><i class="bi bi-arrow-repeat"></i> Re-Do Assignment</button>
                                        </div>
                                    </div>
                                    <p class="text-danger">{{ session('invalid') != null ? session('invalid') : '' }}
                                    </p>
                                    <p class="text-success">{{ session('valid') != null ? session('valid') : '' }}</p>

                                    {{-- questions and their answers goes here --}}
                                    <div class="container p-1 w-100">
                                        @for ($i = 0; $i < count($questions); $i++)
                                            @php
                                                $student_ans = selectedData($student_answers->answer,$questions[$i]->id);
                                            @endphp
                                            <div class="col-md-11 border bg-gray border-success p-1 rounded my-4 mx-auto">
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <h6><b>Q{{$i+1}}</b></h6>
                                                    </div>
                                                    <div class="col-md-1">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <p>{{$questions[$i]->quiz}}</p>
                                                    </div>
                                                    <div class="col-md-2 row">
                                                        <p class="text-center w-75 mx-auto col-sm-12 border border-primary p-1"><b>Tot
                                                                Mks:</b> {{$questions[$i]->points}} Mks</p>
                                                        {{-- <p class="text-center col-sm-6 border border-primary p-1"><b>Att
                                                                Mks:</b> 0 Mks</p> --}}
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    @if (isJson($questions[$i]->resources))
                                                        @php
                                                            $resources = json_decode($questions[$i]->resources);
                                                        @endphp
                                                        @if (count($resources))
                                                            @for ($ind = 0; $ind < count($resources); $ind++)
                                                                <div class="mx-1 my-1" style="width: 100px; cursor:pointer;"
                                                                    data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
                                                                    <img src="{{$resources[$ind]->locale}}"
                                                                        id="window_locale_{{$i}}" class="window_locale window0 my-1 mx-auto"
                                                                        alt="" width="90" height="90">
                                                                    <span class="text-center">{{$resources[$ind]->name}}</span>
                                                                </div>
                                                            @endfor
                                                        @endif
                                                    @endif
                                                    {{-- <div class="mx-1 my-1" style="width: 100px; cursor:pointer;"
                                                        data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
                                                        <img src="/Assignment/resources/testimonytbl1/Fig_1_20230516153729.png"
                                                            id="window_locale0" class="window_locale window0 my-1 mx-auto"
                                                            alt="" width="90" height="90">
                                                        <span class="text-center">Fig 1</span>
                                                    </div>
                                                    <div class="mx-1 my-1" style="width: 100px; cursor:pointer;"
                                                        data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
                                                        <img src="/Assignment/resources/testimonytbl1/Fig_2_20230516153736.png"
                                                            id="window_locale0" class="window_locale window0 my-1 mx-auto"
                                                            alt="" width="90" height="90">
                                                        <span class="text-center">Fig 2</span>
                                                    </div> --}}
                                                </div> 
                                                @if (isJson($questions[$i]->choice))
                                                    @php
                                                        $choice = $questions[$i]->choice;
                                                        $choice = json_decode($choice);
                                                        $my_choices = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
                                                    @endphp

                                                    @if (count($choice))
                                                        <p><b>Multiple Choices</b></p>
                                                        @for ($ind = 0; $ind < count($choice); $ind++)
                                                            <p class="my-1 text-secondary"><b>{{$my_choices[$ind]}}.</b> {{$choice[$ind]->choice}}</p>
                                                        @endfor
                                                    @endif
                                                @endif
                                                <hr class="my-1">
                                                <p class="my-0"><b>Correct Answer</b></p>
                                                <p class="text-success">{{$questions[$i]->correct_answer == null ? "Not Set" : $questions[$i]->correct_answer}}</p>
                                                <hr class="my-1">
                                                <p class="my-0"><b>{{ucwords(strtolower(explode(" ",$student_names)[0]."`s"))}} Answer</b></p>
                                                <p class="my-0 text-secondary">{{isset($student_ans->answer) ? $student_ans->answer : "Not Set"}}</p>
                                                <hr class="my-1">
                                                <label class="my-0"><b>Marking Area. 
                                                    <div class="spinner-grow spinner-grow-sm text-success hide" id="spinners_load_{{$questions[$i]->id}}" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div></b>
                                                </label>
                                                <div style="background-color: rgb(206, 206, 206);" class="row border border-secondary w-80 p-2 rounded mx-auto">
                                                    <div class="col-md-7">
                                                        <label for="comment_here" class="form-label" id="comment_here">Write your comments / review here...</label>
                                                        <textarea id="comment_here{{$questions[$i]->id}}" cols="30" rows="5" class="form-control comment_here text-success text-bold" style="font-weight: 400;" placeholder="Write your comment / review here..">{{isset($student_ans->review) ? $student_ans->review : ""}}</textarea>
                                                        <input type="hidden" value="{{$questions[$i]->points}}" id="max_value_{{$questions[$i]->id}}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <p class="text-danger" id="danger_data"></p>
                                                        <label for="marks_attained_{{$questions[$i]->id}}" class="form-label">Assign Marks <small class="text-success">(Not more than {{$questions[$i]->points}} Mks.)</small></label>
                                                        <input type="number" class="form-control" value="{{isset($student_ans->score) ? $student_ans->score : 0}}" min="0" max="{{$questions[$i]->points}}" id="marks_attained_{{$questions[$i]->id}}" placeholder="Give Marks Attained">
                                                    </div>
                                                    <div class="col-md-12 my-2">
                                                        <button type="button" class="btn btn-success w-100 review_btn" id="review_btn_{{$questions[$i]->id}}"><i class="bi bi-save"></i> Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                        <form method="POST" action="/Teacher/Mark/Submit" class="col-md-11 my-2 mx-auto">
                                            <input type="hidden" name="my_student_answers" value="{{json_encode($student_answers)}}" id="my_students_answers">
                                            <input type="hidden" value="{{$assignment_id}}" name="assignment_id">
                                            <input type="hidden" value="{{$adm_no}}" name="student_id">
                                            @csrf
                                            <p class="text-success p-2 rounded" style="background: rgb(213, 213, 213);">
                                                <span ><b>Note</b></span> <br>
                                                - Double check the student marks before saving. <br>
                                                - Complete and save the student marks. <br>
                                                - Once they log in their portal they will see their marks. <br>
                                            </p>
                                            <button type="submit" class="btn btn-primary w-100" id="save_buttons"><i class="bi bi-save"></i> Complete & Save</button>
                                        </form>
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
    <script src="/assets/js/std_js/marking_scheme.js"></script>

</body>

</html>

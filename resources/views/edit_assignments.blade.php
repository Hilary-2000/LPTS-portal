<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Assignments Quiz - {{ $subject_name }} - {{ $assignment_details->name }} -
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
            <h1>Set "{{ $assignment_details->name }}" Questions</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/Assignment">Subjects I Teach</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            href="/Teacher/Assignments/{{ $subject_id }}/Create/{{ $selected_class }}">Assignments For
                            {{ $subject_name }} - {{ $class_name }}</a>
                    </li>
                    <li class="breadcrumb-item active">Set Questions for {{ $assignment_details->name }} </li>
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
                                <a href="/Teacher/Assignments/{{ $subject_id }}/Create/{{ $selected_class }}"
                                    class="btn btn-secondary btn-sm my-2"><i class="bi bi-arrow-left"></i> Assignments
                                    for {{ $subject_name }} - {{ $class_name }}</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>Set <b>{{ $assignment_details->name }}</b> assignment quizes from this page
                                    </li>
                                    <li>When adding multiple choices, the system will use the first choice you give it
                                        as the correct answer.</li>
                                    <li>This feature will be helpfull when you want the system to mark you the
                                        assignment automatically.</li>
                                    <li>Set the number of points the student will get when they get the question right!
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <h5 class="card-title">Set questions for {{ $assignment_details->name }} -
                                        {{ $subject_name }} ({{ $class_name }})</h5>
                                        <p class=""><b><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Questions Set</span> </b> : @php
                                            if (isJson($assignment_details->questions)) {
                                                $questions = json_decode($assignment_details->questions);
                                                echo count($questions) > 0 ? count($questions)." Question(s).":"0 Question(s).";
                                            }else {
                                                echo "0 Question(s).";
                                            }
                                        @endphp</p>
                                        <p class=""><b><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Total Marks :</span> </b> : @php
                                            $total_marks = 0;
                                            if (isJson($assignment_details->questions)) {
                                                $questions = json_decode($assignment_details->questions);
                                                for ($ind=0; $ind < count($questions); $ind++) { 
                                                    $total_marks += $questions[$ind]->points;
                                                }
                                                echo $total_marks." Mark(s)";
                                            }else {
                                                echo $total_marks." Mark(s)";
                                            }
                                        @endphp</p>
                                    <p class="text-danger">{{ session('invalid') != null ? session('invalid') : '' }}
                                    </p>
                                    <p class="text-success">{{ session('valid') != null ? session('valid') : '' }}</p>

                                    <h6 class="text-center">Fill all the required fields</h6>
                                    <div class="row my-2 p-1 border border-primary rounded p-1">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subject_id }}">
                                        <input type="hidden" name="class_name" value="{{ $class_name }}">
                                        <div class="col-md-12 p-1">
                                            <label for="assignment_question" class="form-label"><b>Set Question</b></label>
                                            <input type="text" class="form-control" name="assignment_question"
                                                id="assignment_question" required placeholder="Set Question">
                                        </div>
                                        <hr class="border border-primary my-1">
                                        <div class="col-md-4 p-1">
                                            <label for="maximum_points"  class="form-control-label"><b>Maximum
                                                Points</b></label>
                                            <input type="number" class="form-control" id="maximum_points"
                                                name="maximum_points" required placeholder="e.g. 2" value="1">
                                        </div>
                                        <div class="col-md-8"></div>
                                        <hr class="border border-primary my-1">
                                        <div class="col-md-4 p-1">
                                            <label for="multiple_choices" class="form-label"><b>Multiple
                                                Choices</b></label>
                                            <input type="text" class="form-control" name="multiple_choices"
                                                id="multiple_choices" placeholder="(Optional)">
                                            <button type="button" class="btn btn-primary btn-sm my-1 w-100"
                                                id="add_multiple_choices"><i class="bi bi-plus"></i> Add</button>
                                        </div>
                                        <div class="col-md-6" id="display_choices_window">
                                            <ul class="list-group" id="">
                                                <h3 class="text-center text-secondary mt-1"><i
                                                        class="bi bi-exclamation-triangle"></i></h3>
                                                <p class="text-secondary text-center">No Choices set!</p>
                                            </ul>
                                        </div>
                                        <hr class="border border-primary my-1">
                                        <div class="col-md-4 p-1">
                                            <label for="resource_name" class="form-label"><b>Add images to the question</b></label>
                                                <p id="text_holders_in"></p>
                                            <input type="text" name="resource_name" id="resource_name"
                                                class="form-control my-1" placeholder="Resource Name e.g. Figure 1">
                                            <input type="file" class="form-control" id="resource_location"
                                                accept=".jpg,.png,.jpeg">
                                            <div class="progress my-1 hide" id="file_progress_bars">
                                                <div class="progress-bar" id="progress_bars" role="progressbar"
                                                    style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                    aria-valuemax="100">0%</div>
                                            </div>
                                            <button class="btn btn-primary btn-sm w-100" id="add_images_btn" type="button"><i class="bi bi-plus"></i> Add</button>
                                        </div>
                                        <div class="col-md-8 mx-auto  row" id="resource_display">
                                            <h3 class="text-center text-secondary mt-1"><i
                                                class="bi bi-exclamation-triangle"></i></h3>
                                            <p class="text-secondary text-center">No Resources added yet!</p>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="correct_answer" class="form-label"><b>Correct Answer</b></label>
                                            <textarea id="correct_answer" cols="30" rows="5" class="form-control" placeholder="Write your correct answer!"></textarea>
                                        </div>
                                        <div class="col-md-12 p-1">
                                            <form action="/Assignments/Add" method="post">
                                                @csrf
                                                <input type="hidden" name="assignment_id" value="{{$assignment_id}}">
                                                <input type="hidden" name="subject_id" value="{{ $subject_id }}">
                                                <input type="hidden" name="class_name" value="{{ $class_name }}">
                                                <input type="hidden" id="admin_correct_answers" name="correct_answer">
                                                <input type="hidden" name="assignment_question_holder"
                                                id="assignment_question_holder" placeholder="Set Question">
                                                <input type="hidden" name="multiple_choices_holder" value="[]"
                                                    id="multiple_choices_holder">
                                                <input type="hidden" name="maximum_points_holder" value=""
                                                id="maximum_points_holder">
                                                <input type="hidden" value="[]" name="resources_location" id="resources_location" class="resources_location">
                                                <button type="submit" class="btn btn-success btn-sm w-100"><i
                                                        class="bi bi-save"></i> Save</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Recent Sales -->
                        {{-- question table --}}
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto p-1">
                                <h6 class="text-secondary text-center">Questions List</h6>
                                @if (isJson($assignment_details->questions))
                                    @php
                                        $questions = json_decode($assignment_details->questions);
                                    @endphp
                                    @if (count($questions) > 0)
                                        @for ($i = 0; $i < count($questions); $i++)
                                            <div class="col-md-11 border border-secondary p-1 rounded my-1 mx-auto">
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <h6><b>Q{{$i+1;}}</b></h6>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <a href="/DeleteQuiz/{{$assignment_id}}/{{$questions[$i]->id}}" class="text-danger">
                                                            <h6 class="text-danger text-center" style="cursor: pointer;"><b><i
                                                                class="bi bi-trash"></i></b></h6>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <p>{{$questions[$i]->quiz}}.</p>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <p class="text-center"><b>{{$questions[$i]->points}} Mks</b></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    @if (isJson($questions[$i]->resources))
                                                        @php
                                                            // resources
                                                            $resources = json_decode($questions[$i]->resources);
                                                        @endphp
                                                            @for ($index=0; $index < count($resources); $index++)
                                                                <div class="mx-1 my-1" style="width: 100px; cursor:pointer;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
                                                                    <img src="{{$resources[$index]->locale}}"  id="window_locale{{$index}}" class="window_locale window{{$i}} my-1 mx-auto" alt="" width="90" height="90">
                                                                    <input type="hidden" value="{{json_encode($resources[$index])}}" id="values_id_{{$index}}{{$i}}">
                                                                    <input type="hidden" value="{{json_encode($resources)}}" id="inside_values_{{$index}}{{$i}}" >
                                                                    <span class="text-center">{{$resources[$index]->name}}</span>
                                                                </div>
                                                            @endfor
                                                    @endif
                                                </div>
                                                @if (isJson($questions[$i]->choice))
                                                        @php
                                                            // choice
                                                            $choice = json_decode($questions[$i]->choice);
                                                            $counted = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z']
                                                        @endphp
                                                        @if (count($choice) > 0)
                                                            <b>Multiple Choices</b>
                                                            @for ($index=0; $index < count($choice); $index++)
                                                                @if ($index == 0)
                                                                    <p class="my-1 text-success"><b>{{$counted[$index]}}.</b> {{$choice[$index]->choice}}</p>
                                                                @else
                                                                    <p class="my-1 text-secondary"><b>{{$counted[$index]}}.</b> {{$choice[$index]->choice}}</p>
                                                                @endif
                                                            @endfor
                                                        @endif
                                                    @endif

                                                    {{-- correct answer --}}
                                                    @if (isset($questions[$i]->correct_answer))
                                                        <label for=""><b>Correct Answer</b></label>
                                                        <p>{{$questions[$i]->correct_answer}}</p>
                                                    @else
                                                        
                                                    @endif
                                            </div>
                                        @endfor
                                    @else
                                        <h3 class="text-center text-secondary mt-1"><i
                                            class="bi bi-exclamation-triangle"></i></h3>
                                        <p class="text-secondary text-center">No questions set!</p>
                                    @endif
                                @else
                                    <h3 class="text-center text-secondary mt-1"><i
                                        class="bi bi-exclamation-triangle"></i></h3>
                                    <p class="text-secondary text-center">No questions set!</p>
                                @endif

                                <!-- Extra Large Modal -->
                                {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ExtralargeModal">
                                  Extra Large Modal
                                </button> --}}
                  
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
                                {{-- <div class="col-md-11 border border-secondary p-1 rounded my-1 mx-auto">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <h6><b>Q1</b></h6>
                                        </div>
                                        <div class="col-md-1">
                                            <h6 class="text-danger text-center" style="cursor: pointer;"><b><i
                                                        class="bi bi-trash"></i></b></h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-11">
                                            <p>This is question one, here we test tests to test if you can stand the
                                                test of time.</p>
                                        </div>
                                        <div class="col-md-1">
                                            <p class="text-center"><b>1 Mks</b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <img src="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" class="m-1" alt=""
                                            width="90" height="90">
                                        <img src="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" class="m-1" alt=""
                                            width="90" height="90">
                                        <img src="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" class="m-1" alt=""
                                            width="90" height="90">
                                    </div>
                                    <b>Multiple Choices</b>
                                    <p class="my-1 text-success"><b>A.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>B.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>C.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>D.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>E.</b> Lab Rat</p>
                                </div> --}}
                                {{-- <div class="col-md-11 border border-secondary p-1 rounded my-1 mx-auto">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <h6><b>Q2</b></h6>
                                        </div>
                                        <div class="col-md-1">
                                            <h6 class="text-danger text-center" style="cursor: pointer;"><b><i
                                                        class="bi bi-trash"></i></b></h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-11">
                                            <p>This is question one, here we test tests to test if you can stand the
                                                test of time.</p>
                                        </div>
                                        <div class="col-md-1">
                                            <p class="text-center"><b>1 Mks</b></p>
                                        </div>
                                    </div> --}}
                                    {{-- <div class="col-md-8">
                                        <img src="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" class="m-1" alt="" width="90" height="90">
                                        <img src="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" class="m-1" alt="" width="90" height="90">
                                        <img src="{{'https://lsims.ladybirdsmis.com/sims/'.session('school_information')->school_profile_image}}" class="m-1" alt="" width="90" height="90">
                                    </div>
                                    <b>Multiple Choices</b>
                                    <p class="my-1 text-success"><b>A.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>B.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>C.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>D.</b> Lab Rat</p>
                                    <p class="my-1 text-secondary"><b>E.</b> Lab Rat</p> --}}
                                {{-- </div> --}}
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

    {{-- page data --}}
    <script></script>
    {{-- <script src="/assets/js/tr_js/lessonplan.js"></script> --}}
    <!-- Vendor JS Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/tr_js/assignments.js"></script>

</body>

</html>

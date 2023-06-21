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
                </li>
                <!-- End Notification Nav -->

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
                                    <li>Set <b>{{ $assignment_details->name }}</b> assignment metadata from this page
                                    </li>
                                    <li>When the assignment status is changed to published, students will be able to attempt it from their portal.</li>
                                </ul>
                            </div>
                        </div>
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <h5 class="card-title">Set questions for {{ $assignment_details->name }} -
                                        {{ $subject_name }} ({{ $class_name }})</h5>
                                        <div class="row">
                                            <div class="col-md-6">
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
                                                <p class=""><b><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Date Created :</span> </b> : {{date("D dS M Y",strtotime($assignment_details->date_created))}}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><b>Click to change the assignment status.</b></p>
                                                @if ($assignment_details->status == 1)
                                                    <a href="/Assignment/Status/{{$assignment_details->id}}" class="btn btn-primary btn-sm"> PUBLISHED</a>
                                                @else
                                                    <a href="/Assignment/Status/{{$assignment_details->id}}" class="btn btn-secondary btn-sm"> DRAFTED</a>
                                                @endif
                                                <p><b>Click to delete!</b></p>
                                                <button class="btn btn-outline-danger btn-sm" id="delete_btn" data-bs-toggle="modal" data-bs-target="#ExtralargeModal"><i class="bi bi-trash"></i> Delete Assignment</button>
                                            </div>
                                        </div>

                                    <div class="modal fade" id="ExtralargeModal" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                            <h5 class="modal-title">Delete "{{$assignment_details->name}}"</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-secondary">When you delete this assignment, all the answers, resources and questions associated to this assignment will be deleted!</p>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <a href="/Assignment/Delete/{{$assignment_details->id}}" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Permanently Delete</a>
                                            </div>
                                        </div>
                                        </div>
                                    </div><!-- End Extra Large Modal-->
                                    <p class="text-danger">{{ session('invalid') != null ? session('invalid') : '' }}
                                    </p>
                                    <p class="text-success">{{ session('valid') != null ? session('valid') : '' }}</p>
                                    
                                    <hr class="my-2">
                                    {{-- add the field to update the assignment data --}}
                                    <form class="container row" method="POST" action="/Assignment/Update">
                                        @csrf
                                        <input type="hidden" name="assignment_id" value="{{$assignment_id}}">
                                        <div class="col-md-4 my-1">
                                            <label for="assignment_name" class="form-label">Assignment Name</label>
                                            <input type="text" class="form-control" value="{{$assignment_details->name}}" name="assignment_name" id="assignment_name" placeholder="Assignment Name">
                                        </div>
                                        <div class="col-md-4 my-1">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" value="{{isJson($assignment_details->period) ? date("Y-m-d",strtotime(json_decode($assignment_details->period)->start_date)) : date("Y-m-d")}}" class="form-control" name="start_date" id="start_date" placeholder="Start Date">
                                        </div>
                                        <div class="col-md-4 my-1">
                                            <label for="end_date" class="form-label">End Date</label>
                                            <input type="date" value="{{isJson($assignment_details->period) ? date("Y-m-d",strtotime(json_decode($assignment_details->period)->end_date)) : date("Y-m-d")}}" class="form-control" name="end_date" id="end_date" placeholder="Assignment Name">
                                        </div>
                                        <div class="col-md-4 my-1">
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-update"></i> Update</button>
                                        </div>
                                    </form>
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

    {{-- page data --}}
    <script></script>
    {{-- <script src="/assets/js/tr_js/lessonplan.js"></script> --}}
    <!-- Vendor JS Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    {{-- <script src="/assets/js/tr_js/assignments.js"></script> --}}

</body>

</html>

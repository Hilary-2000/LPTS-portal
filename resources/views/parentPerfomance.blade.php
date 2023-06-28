<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ucwords(strtolower($student_data[0]->first_name." ".$student_data[0]->second_name))}}`s Perfomance -
        {{ session('parents_data') != null ? ucwords(strtolower(session('parents_data')['parent_name'])) : '' }}
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            <a href="/Parent/Dashboard" class=" d-flex align-items-center">
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
                        <span class="badge bg-primary badge-number">{{count($parents_notification)}}</span>
                    </a><!-- End Notification Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            You have {{count($parents_notification)}} new notifications
                            <a href="/Parent/Alert"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
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
                        @for ($i = 0; $i < count($parents_notification); $i++)
                            <li class="notification-item">
                                <i class="bi bi-bell text-primary"></i>
                                <a href="/Parent/Alert/Read/{{$parents_notification[$i]->id}}">
                                    <h4 class="text-dark">{{$parents_notification[$i]->message_title}}</h4>
                                    <p>{{strlen(getInnerText($parents_notification[$i]->message_body)) > 50 ? substr(getInnerText($parents_notification[$i]->message_body),0,50)."..." : getInnerText($parents_notification[$i]->message_body)}}</p>
                                    <p>{{getTimeAgo($parents_notification[$i]->date_created)}}</p>
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
                            <a href="/Parent/Alert">Show all notifications</a>
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
                        <img src="{{ session('parents_data') != null ? '/assets/img/dp.png' : '/assets/img/dp.png' }}"
                            alt="Profile" class="rounded-circle">
                        <span
                            class="d-none d-md-block dropdown-toggle ps-2">{{ session('parents_data') != null ? ucwords(strtolower(session('parents_data')['parent_name'])) : 'Null'  }}</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ session('parents_data') != null ? ucwords(strtolower(session('parents_data')['parent_name'])) : 'Null'  }}</h6>
                            <span>Parent</span>
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
                <a class="nav-link collapsed" href="/Parent/Dashboard">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->

            {{-- <li class="nav-item">
                <a class="nav-link collapsed" href="/Parent/Fees">
                    <i class="bi bi-person-square"></i>
                    <span>My Children</span>
                </a>
            </li><!-- End Dashboard Nav --> --}}

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Parent/Fees">
                    <i class="bi bi-currency-dollar"></i>
                    <span>Student Fees</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link " href="/Parent/Peformance">
                    <i class="bi bi-graph-up"></i>
                    <span>Student Perfomances</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Parent/Alert">
                    <i class="bi bi-bell"></i>
                    <span>Messages & Alerts</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Parent/DiscussionForum">
                    <i class="bi bi-chat-dots"></i>
                    <span>Discussion Forums</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-heading">Pages</li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Parent/Profile">
                    <i class="bi bi-person"></i>
                    <span>My Profile</span>
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
            <h1>Student Perfomance</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Parent/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Parent/Peformance">Student List</a>
                    </li>
                    <li class="breadcrumb-item active">{{ucwords(strtolower($student_data[0]->first_name." ".$student_data[0]->second_name))}}`s Perfomance</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                @php
                    function isJson($string) {
                        return ((is_string($string) &&
                                (is_object(json_decode($string)) ||
                                is_array(json_decode($string))))) ? true : false;
                    }
                @endphp
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <a href="/Parent/Peformance" class="btn btn-sm btn-secondary my-2"><i class="bi bi-arrow-left"></i> Back To My Children</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>View students perfomance term wise!</li>
                                    <li>Graphical representation of the performance is also included!</li>
                                </ul>
                                <p class="text-danger">{{session("invalid") != null?session("invalid") : ""}}</p>
                                <p class="text-success">{{session("valid") != null?session("valid") : ""}}</p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Perfomance Graph:</h5>
                                <div class="container border border-primary rounded p-0 row">
                                    <div class="col-md-4">
                                        <canvas height="300px" class="w-100" id="exams_data_1"></canvas>
                                    </div>
                                    <div class="col-md-4">
                                        <canvas height="300px" class="w-100" id="exams_data_2"></canvas>
                                    </div>
                                    <div class="col-md-4">
                                        <canvas height="300px" class="w-100" id="exams_data_3"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Left side columns -->
            </div>
        </section>
        <section class="section">
            <div class="card row p-2">
                <div class="card-body py-0 col-md-12">
                    <h5 class="card-title">Term 1 Exam List</h5>
                </div>
                @if (count($term_one_result) > 0)
                    @for ($i = 0; $i < count($term_one_result); $i++)
                        <div class="card col-md-12 my-1 border border-primary rounded">
                            <div class="card-body">
                                <div class="container p-0 row">
                                    <h5 class="col-md-9 card-title">{{ucwords(strtolower($term_one_result[$i]->name))}}</h5>
                                    <a class="col-md-3 my-2 nav-link" target="_blank" href="/Parent/Print-resultslip/{{$term_one_result[$i]->exams_id}}/{{$student_data[0]->adm_no}}"><span><i class="bi bi-printer"></i> Print Results</span></a>
                                </div>
                                <h6 class="card-subtitle mb-2 mx-1 text-muted">Date Started : {{date("D dS M Y",strtotime($term_one_result[$i]->date_done))}}</h6>
                                
                                @if (count($term_one_result[$i]->subjects) > 0)
                                    @php
                                        $total_score = 0;
                                        $counter = 0;
                                    @endphp
                                    <ul class="list-group my-2">
                                        @for ($index = 0; $index < count($term_one_result[$i]->subjects); $index++)
                                            @php
                                                $counter += 1;
                                                $elem = $term_one_result[$i]->subjects[$index];
                                                $percentage = round(($elem->scored_marks*=1) / ($elem->max_marks*=1) * 100) ;
                                                if ($percentage >= 0 && $percentage <= 25) {
                                                    $color_grade = "danger";
                                                }elseif ($percentage >= 26 && $percentage <= 50) {
                                                    $color_grade = "warning";
                                                }elseif ($percentage >= 51 && $percentage <= 75) {
                                                    $color_grade = "primary";
                                                }else {
                                                    $color_grade = "success";
                                                }
                                                $total_score+=$percentage;
                                            @endphp
                                                <li class="list-group-item"><i class="bi bi-star me-1 text-{{$color_grade}}"></i> {{ucwords(strtolower($elem->display_name))}} || {{ucwords(strtolower($elem->scored_marks))}} Out Of {{ucwords(strtolower($elem->max_marks))}} || <span class="text-primary"><b>{{(round(($elem->scored_marks*=1) / ($elem->max_marks*=1) * 100))}}%</b></span></li>
                                        @endfor
                                        <li class="list-group-item"><b>Average Score : </b>{{round($total_score / $counter)."%"}}</li>
                                    </ul>
                                @else
                                    <ul class="list-group my-2">
                                        <li class="list-group-item"><i class="bi bi-star me-1 text-danger"></i> Subjects not present at the moment!</li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endfor
                @else
                    <div class="card col-md-12 my-1 border border-primary rounded">
                        <div class="card-body">
                            <h5 class="card-title">No Exams Done</h5>
                            <h6 class="card-subtitle mb-2 mx-1 text-muted">Exams Will be Posted Once done!</h6>
                            
                            <ul class="list-group my-2">
                                <li class="list-group-item"><i class="bi bi-star me-1 text-danger"></i> No Exams Done</span></li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </section>
        <section class="section">
            <div class="card row p-2">
                <div class="card-body py-0 col-md-12">
                    <h5 class="card-title">Term 2 Exam List</h5>
                </div>
                @if (count($term_two_result) > 0)
                    @for ($i = 0; $i < count($term_two_result); $i++)
                        <div class=" col-md-12 my-1 border border-primary rounded">
                            <div class="card-body">
                                <div class="container p-0 row">
                                    <h5 class="col-md-9 card-title">{{ucwords(strtolower($term_two_result[$i]->name))}}</h5>
                                    <a class="col-md-3 my-2 nav-link" target="_blank" href="/Parent/Print-resultslip/{{$term_two_result[$i]->exams_id}}/{{$student_data[0]->adm_no}}"><span><i class="bi bi-printer"></i> Print Results</span></a>
                                </div>
                                <h6 class="card-subtitle mb-2 mx-1 text-muted">Date Started : {{date("D dS M Y",strtotime($term_two_result[$i]->date_done))}}</h6>
                                
                                @if (count($term_two_result[$i]->subjects) > 0)
                                    @php
                                        $total_score = 0;
                                        $counter = 0;
                                    @endphp
                                    <ul class="list-group my-2">
                                        @for ($index = 0; $index < count($term_two_result[$i]->subjects); $index++)
                                            @php
                                                $counter += 1;
                                                $elem = $term_two_result[$i]->subjects[$index];
                                                $percentage = round(($elem->scored_marks*=1) / ($elem->max_marks*=1) * 100) ;
                                                if ($percentage >= 0 && $percentage <= 25) {
                                                    $color_grade = "danger";
                                                }elseif ($percentage >= 26 && $percentage <= 50) {
                                                    $color_grade = "warning";
                                                }elseif ($percentage >= 51 && $percentage <= 75) {
                                                    $color_grade = "primary";
                                                }else {
                                                    $color_grade = "success";
                                                }
                                                $total_score+=$percentage;
                                            @endphp
                                                <li class="list-group-item"><i class="bi bi-star me-1 text-{{$color_grade}}"></i> {{ucwords(strtolower($elem->display_name))}} || {{ucwords(strtolower($elem->scored_marks))}} Out Of {{ucwords(strtolower($elem->max_marks))}} || <span class="text-primary"><b>{{(round(($elem->scored_marks*=1) / ($elem->max_marks*=1) * 100))}}%</b></span></li>
                                        @endfor
                                        <li class="list-group-item"><b>Average Score : </b>{{round($total_score / $counter)."%"}}</li>
                                    </ul>
                                @else
                                    <ul class="list-group my-2">
                                        <li class="list-group-item"><i class="bi bi-star me-1 text-danger"></i> Subjects not present at the moment!</li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endfor
                @else
                    <div class="card col-md-12 my-1 border border-primary rounded">
                        <div class="card-body">
                            <h5 class="card-title">No Exams Done</h5>
                            <h6 class="card-subtitle mb-2 mx-1 text-muted">Exams Will be Posted Once done!</h6>
                            
                            <ul class="list-group my-2">
                                <li class="list-group-item"><i class="bi bi-star me-1 text-danger"></i> No Exams Done</span></li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </section>
        <section class="section">
            <div class="card row p-2">
                <div class="card-body py-0 col-md-12">
                    <h5 class="card-title">Term 3 Exam List</h5>
                </div>
                @if (count($term_three_result) > 0)
                    @for ($i = 0; $i < count($term_three_result); $i++)
                        <div class="card col-md-12 my-1 border border-primary rounded">
                            <div class="card-body">
                                <div class="container p-0 row">
                                    <h5 class="col-md-9 card-title">{{ucwords(strtolower($term_three_result[$i]->name))}}</h5>
                                    <a class="col-md-3 my-2 nav-link" target="_blank" href="/Parent/Print-resultslip/{{$term_three_result[$i]->exams_id}}/{{$student_data[0]->adm_no}}"><span><i class="bi bi-printer"></i> Print Results</span></a>
                                </div>
                                <h6 class="card-subtitle mb-2 mx-1 text-muted">Date Started : {{date("D dS M Y",strtotime($term_three_result[$i]->date_done))}}</h6>
                                
                                @if (count($term_three_result[$i]->subjects) > 0)
                                    @php
                                        $total_score = 0;
                                        $counter = 0;
                                    @endphp
                                    <ul class="list-group my-2">
                                        @for ($index = 0; $index < count($term_three_result[$i]->subjects); $index++)
                                            @php
                                                $counter += 1;
                                                $elem = $term_three_result[$i]->subjects[$index];
                                                $percentage = round(($elem->scored_marks*=1) / ($elem->max_marks*=1) * 100) ;
                                                if ($percentage >= 0 && $percentage <= 25) {
                                                    $color_grade = "danger";
                                                }elseif ($percentage >= 26 && $percentage <= 50) {
                                                    $color_grade = "warning";
                                                }elseif ($percentage >= 51 && $percentage <= 75) {
                                                    $color_grade = "primary";
                                                }else {
                                                    $color_grade = "success";
                                                }
                                                $total_score+=$percentage;
                                            @endphp
                                                <li class="list-group-item"><i class="bi bi-star me-1 text-{{$color_grade}}"></i> {{ucwords(strtolower($elem->display_name))}} || {{ucwords(strtolower($elem->scored_marks))}} Out Of {{ucwords(strtolower($elem->max_marks))}} || <span class="text-primary"><b>{{(round(($elem->scored_marks*=1) / ($elem->max_marks*=1) * 100))}}%</b></span></li>
                                        @endfor
                                        <li class="list-group-item"><b>Average Score : </b>{{round($total_score / $counter)."%"}}</li>
                                    </ul>
                                @else
                                    <ul class="list-group my-2">
                                        <li class="list-group-item"><i class="bi bi-star me-1 text-danger"></i> Subjects not present at the moment!</li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endfor
                @else
                    <div class="card col-md-12 my-1 border border-primary rounded">
                        <div class="card-body">
                            <h5 class="card-title">No Exams Done</h5>
                            <h6 class="card-subtitle mb-2 mx-1 text-muted">Exams Will be Posted Once done!</h6>
                            
                            <ul class="list-group my-2">
                                <li class="list-group-item"><i class="bi bi-star me-1 text-danger"></i> No Exams Done</span></li>
                            </ul>
                        </div>
                    </div>
                @endif
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
        function generateRandomNumber(min, max) {
            return Math.floor(Math.random() * (max - min + 1) + min);
        }
        var term_one_plot = @json($term_one_plot);
        var term_two_plot = @json($term_two_plot);
        var term_three_plot = @json($term_three_plot);


        
        var rand_red = generateRandomNumber(100,255);
        var rand_green = generateRandomNumber(100,255);
        var rand_blue = generateRandomNumber(100,255);
        var rand_color = 'rgb('+rand_red+', '+rand_green+', '+rand_blue+')';
        console.log(term_one_plot);

        var ctx = document.getElementById("exams_data_1");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels:term_one_plot.map(row => row.exam_name),
                datasets: [{
                    tension: 0.4,
                    label: 'Exams Term One',
                    data: term_one_plot.map(row => row.exams_score),
                    borderWidth: 1,
                    font: {
                        size: 14
                    },
                    backgroundColor: rand_color,
                    borderColor:'rgb(55, 61, 125)'
                }],
                hoverOffset: 4
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks:{
                            stepSize: 10
                        },
                        grid:{
                            display:true,
                            drawOnChartArea:true,
                            drawTicks:true
                        },
                        display:true
                    },
                    x:{
                        grid:{
                            display:true,
                            drawOnChartArea:true,
                            drawTicks:true
                        },
                        display:true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: "Term 1",
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        });
        
        // term two
        var rand_red = generateRandomNumber(100,255);
        var rand_green = generateRandomNumber(100,255);
        var rand_blue = generateRandomNumber(100,255);
        var rand_color = 'rgb('+rand_red+', '+rand_green+', '+rand_blue+')';
        var ctx_2 = document.getElementById("exams_data_2");
        var myChart = new Chart(ctx_2, {
            type: 'bar',
            data: {
                labels:term_two_plot.map(row => row.exam_name),
                datasets: [{
                    tension: 0.4,
                    label: 'Exams Term Two',
                    data: term_two_plot.map(row => row.exams_score),
                    borderWidth: 1,
                    font: {
                        size: 14
                    },
                    backgroundColor: rand_color,
                    borderColor:'rgb(55, 61, 125)'
                }],
                hoverOffset: 4
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks:{
                            stepSize: 10
                        },
                        grid:{
                            display:true,
                            drawOnChartArea:true,
                            drawTicks:true
                        },
                        display:true
                    },
                    x:{
                        grid:{
                            display:true,
                            drawOnChartArea:true,
                            drawTicks:true
                        },
                        display:true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: "Term 2",
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        });

        var rand_red = generateRandomNumber(100,255);
        var rand_green = generateRandomNumber(100,255);
        var rand_blue = generateRandomNumber(100,255);
        var rand_color = 'rgb('+rand_red+', '+rand_green+', '+rand_blue+')';
        // term three
        var ctx_3 = document.getElementById("exams_data_3");
        var myChart = new Chart(ctx_3, {
            type: 'bar',
            data: {
                labels:term_three_plot.map(row => row.exam_name),
                datasets: [{
                    tension: 0.4,
                    label: 'Exams Term Three',
                    data: term_three_plot.map(row => row.exams_score),
                    borderWidth: 1,
                    font: {
                        size: 14
                    },
                    backgroundColor: rand_color,
                    borderColor:'rgb(55, 61, 125)'
                }],
                hoverOffset: 4
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks:{
                            stepSize: 10
                        },
                        grid:{
                            display:true,
                            drawOnChartArea:true,
                            drawTicks:true
                        },
                        display:true
                    },
                    x:{
                        grid:{
                            display:true,
                            drawOnChartArea:true,
                            drawTicks:true
                        },
                        display:true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: "Term 3",
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        });
    </script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>

</body>

</html>

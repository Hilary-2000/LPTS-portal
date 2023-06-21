<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Attempt Assignments -
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
                        <span class="badge bg-primary badge-number">0</span>
                    </a><!-- End Notification Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
                        <li class="dropdown-header">
                            You have 0 new notifications
                            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-footer">
                            <a href="#">Show all notifications</a>
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
            <h1>Attempt Assignments</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Student/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Students/Assignment"> Assignment Table</a>
                    </li>
                    <li class="breadcrumb-item active">Attempt Assignments : {{ $subject_details[0]->display_name }} -
                        "{{ $assignment_data[0]->name }}"</li>
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
                                <a href="/Students/Assignment" class="btn btn-secondary btn-sm my-2 p-1"><i class="bi bi-arrow-left"></i> Back to Subjects I Study</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>Attempt <b>"{{ $assignment_data[0]->name }}"</b></li>
                                    <li>Follow all instructions that have been told by your teachers.</li>
                                    <li>Success in your assignments 🙂</li>
                                    <li class="text-danger">Ensure all answers are saved before submitting!</li>
                                </ul>

                                {{-- show the button to proceed and attempt --}}

                            </div>
                        </div>
                        @php
                            function isJson($string)
                            {
                                return is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))) ? true : false;
                            }
                        @endphp
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Attempt Questions on {{ $subject_details[0]->display_name }} -
                                    "{{ $assignment_data[0]->name }}"</h6>
                                <div class="row my-0">
                                    <div class="border border-secondary p-1 col-md-3">
                                        <p class="m-0"><b>No Of Questions:</b></p>
                                    </div>
                                    <div class="border border-secondary p-1 col-md-9">
                                        <p class="m-0">{{isJson($assignment_data[0]->questions) ? count(json_decode($assignment_data[0]->questions)) : 0}} Question(s)</p>
                                        <input type="hidden" value="{{isJson($assignment_data[0]->questions) ? $assignment_data[0]->questions : "[]"}}" id="question_data_input">
                                    </div>
                                </div>
                                <div class="row my-0">
                                    <div class="border border-secondary p-1 col-md-3">
                                        <p class="m-0"><b>Total Marks:</b></p>
                                    </div>
                                    <div class="border border-secondary p-1 col-md-9">
                                        @php
                                            $total_count = 0;
                                            if(isJson($assignment_data[0]->questions)){
                                                $questions = json_decode($assignment_data[0]->questions);
                                                for ($index=0; $index < count($questions); $index++) { 
                                                    $total_count += $questions[$index]->points;
                                                }
                                            }
                                        @endphp
                                        <p class="m-0">{{$total_count}} Mark(s)</p>
                                    </div>
                                </div>
                                {{-- <div class="row my-0">
                                    <div class="border border-secondary p-1 col-md-3">
                                        <p class="m-0"><b>Progress:</b></p>
                                    </div>
                                    <div class="border border-secondary p-1 col-md-9">
                                        <div class="progress my-1" id="file_progress_bars">
                                            <div class="progress-bar" id="progress_bars" role="progressbar"
                                                style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div> --}}
                                <hr class="my-2">
                                <div class="container">
                                    <div class="col-md-12">
                                        <h6 class="text-center"><u>{{ $assignment_data[0]->name }}</u></h6>
                                    </div>
                                    <div class="col-md-11 border border-secondary p-1 rounded my-1 mx-auto">
                                        <div class="row">
                                            <div class="col-md-11">
                                                <h6><b id="question_number">Q1</b></h6>
                                            </div>
                                            <div class="col-md-1">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-11">
                                                <p id="question_displayer">Questions appear here ?</p>
                                            </div>
                                            <div class="col-md-1">
                                                <p class="text-center" ><b id="question_marks">3 Mks</b></p>
                                                <input type="hidden" id="quiz_id">
                                            </div>
                                        </div>
                                        <div class="row" id="data_to_display">
                                        </div>
                                        <div class="container p-1" id="multiple_choices">
                                        </div>
                                        <hr class="my-1">
                                    </div>
                                    <div class="col-md-11 mx-auto rounded my-1">
                                        <div class="container border border-primary p-2 rounded">
                                            <label for="your_answer" class="form-label"><b>Write Your Answer Here:</b> <span class="text-success hide" id="saved_status">Saved <div class="spinner-grow spinner-grow-sm" role="status"><span class="visually-hidden">Loading...</span></div></span></label>
                                            <textarea class="form-control border border-secondary" name="your_answer" id="your_answer" cols="30" rows="5" placeholder="Write your answer here!"></textarea>
                                            <button class="btn btn-primary btn-sm my-2" id="save_answers" type="button"><i class="bi bi-save"></i> Save Ans</button>
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-md-3">
                                                <button type="button" id="back_to_question" class="btn btn-primary w-100"><i class="bi bi-arrow-left"></i> Back</button>
                                            </div>
                                            <div class="col-md-3">
                                                {{-- <button type="button" class="btn btn-outline-primary"><i class="bi bi-arrow-right"></i> Skip</button> --}}
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" id="save_n_next" class="btn btn-success w-100">Next <i class="bi bi-arrow-right"></i></button>
                                            </div>
                                            <div class="col-md-3">
                                                <form action="/Submit/Assignment" method="post">
                                                    @csrf
                                                    <input type="hidden" name="assignment_data" id="assignment_data" value="[]">
                                                    <input type="hidden" name="assignment_id" id="assignment_id" value="{{$assignment_id}}">
                                                    <input type="hidden" name="assignment_answers" id="assignment_answers" value="[]">
                                                    <button type="submit" id="submit_assignments" class="hide btn btn-sm btn-success">Submit Assignment</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="container">
                                    <div class="progress my-1" id="file_progress_bars">
                                        <div class="progress-bar p-1" id="progress_bars" role="progressbar"
                                            style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
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
    <script>
        // var subject_details = @json_data($subject_details);
    </script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/std_js/attempt.js"></script>
    
</body>

</html>

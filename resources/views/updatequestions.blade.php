<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Edit Bank Questions - {{$subject_details->display_name}} - {{$class_name}} -
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
            <h1>Edit Bank Questions - {{$subject_details->display_name}} - {{$class_name}}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Teacher/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Teacher/QuestionBank">Subject I Teach</a></li>
                    <li class="breadcrumb-item"><a href="/Teacher/QuestionBank/{{$lesson_id}}/Create/{{$class_id}}">Manage Question Bank</a></li>
                    <li class="breadcrumb-item active">Edit Bank Questions - {{$subject_details->display_name}} - {{$class_name}}</li>
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
                                <a class="btn btn-secondary btn-sm my-2" href="/Teacher/QuestionBank/{{$lesson_id}}/Create/{{$class_id}}"><i class="bi bi-arrow-left"></i> Manage Question Bank</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>Edit Bank Questions for {{$subject_details->display_name}} that will later be used to create assesments and exams.</li>
                                    <li>Create questions and specify their difficulty level and topics & subtopic they are in, this will be helpfull when creating exams. It will allow the system to pick correct questions to the criteria that the examiner will input.</li>
                                    <li>You will be able to bank your questions only when you have set up your long term lesson plan!</li>
                                </ul>
                            </div>
                        </div>
                        @php
                            function truncateWord($word,$count){
                                return strlen($word) > $count ? substr($word,0,$count)."..." : $word;
                            }
                        @endphp
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <h5 class="card-title">Edit Question for {{$subject_details->display_name}}, Class: {{$class_name}}<span></span></h5>
                                    <p class="text-success">{{session("successfull_banking") != null?session("successfull_banking") : ""}}</p>
                                    <p class="text-danger">{{session("unsuccessfull_banking") != null?session("unsuccessfull_banking") : ""}}</p>
                                    <hr>
                                    <form method="POST" class="row" action="/UpdateQuizBank">
                                        @csrf
                                        <input type="hidden" name="lesson_id" value="{{$lesson_id}}">
                                        <input type="hidden" name="class_id" value="{{$class_id}}">
                                        <input type="hidden" name="quiz_id" value="{{$question_data->id}}">
                                        <input type="hidden" name="date_created" value="{{$question_data->date_recorded}}">
                                        <div class="form-group col-md-12 mb-2">
                                            <label for="question" class="form-label"><b>Question</b></label>
                                            <textarea required name="question" id="question" cols="30" rows="5" class="form-control" placeholder="Set your questions here...">{{$question_data->question}}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="question_topic" class="form-label"><b>Topic</b></label>
                                            <select name="question_topic" id="question_topic" class="form-control">
                                                <option value="" hidden>Select Topic</option>
                                                @for ($i = 0; $i < count($lesson_plan); $i++)
                                                    <option {{$question_data->topic == $lesson_plan[$i]->index ? "selected" : ""}} value="{{$lesson_plan[$i]->index}}">{{$lesson_plan[$i]->strand_name}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="question_sub_topic" class="form-label"><b>Sub-Topic</b></label>
                                            <select name="question_sub_topic" id="question_sub_topic" class="form-control">
                                                <option id="sub_topic_default" value="" hidden>Select Sub-Topic</option>
                                                @php
                                                    for ($i = 0; $i < count($lesson_plan); $i++){
                                                        for ($ind = 0; $ind < count($lesson_plan[$i]->sub_strands); $ind++){
                                                            $elems = $lesson_plan[$i]->sub_strands[$ind];
                                                            echo "<option ".($question_data->sub_topic == $elems->sub_index ? "id='selected_substrand'" : "")." class='options options_".$lesson_plan[$i]->index."' value=\"".$elems->sub_index."\">".$elems->name."</option>";
                                                        }    
                                                    }
                                                @endphp
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="question_difficulty" class="form-label"><b>Difficulty Level</b></label>
                                            <select required name="question_difficulty" id="question_difficulty" class="form-control">
                                                <option value="" hidden>Select Difficulty Level</option>
                                                <option {{$question_data->difficulty == "Simple" ? "selected" : ""}} value="Simple">Simple</option>
                                                <option {{$question_data->difficulty == "Normal" ? "selected" : ""}} value="Normal">Normal</option>
                                                <option {{$question_data->difficulty == "Hard" ? "selected" : ""}} value="Hard">Hard</option>
                                                <option {{$question_data->difficulty == "Extra-Hard" ? "selected" : ""}} value="Extra-Hard">Extra-Hard</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12 mt-2 row">
                                            <div class="col-md-3">
                                                <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Update It!</button>
                                            </div>
                                            <div class="col-md-6"></div>
                                            <div class="col-md-3">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#verticalycentered" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
                                            </div>
                                        </div>
                                        <!-- Vertically centered Modal -->
                                        <div class="modal fade" id="verticalycentered" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title">Confirm Question Deletion.</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Confirm elimination of <b>"{{$question_data->question}}"</b> a question in the <b>{{$subject_details->display_name}}</b> question bank?
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <a href="/Delete/QB/{{$question_data->id}}/Sid/{{$lesson_id}}/Class/{{$class_id}}" class="btn btn-danger"><i class="bi bi-trash"></i> Delete</a>
                                                </div>
                                            </div>
                                            </div>
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
        window.onload = function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }

        // create a function that will read the data for the subject
        var leson_plan_sub_topics = [];
        for (let index = 0; index < lesson_plan.length; index++) {
            const element = lesson_plan[index];
            // loop throught the substrands associated with it
            var sub_strands = [];
            for (let ind = 0; ind < element.sub_strands.length; ind++) {
                const elem = element.sub_strands[ind];
                var substrands = {
                    "sub_topic":elem.name,
                    "index":elem.sub_index
                }
                sub_strands.push(substrands);
            }
            var data = {
                "topic":element.strand_name,
                "index":element.index,
                "sub_strand":sub_strands
            }
            leson_plan_sub_topics.push(data);
        }

        // listener to hide unwanted subtopics
        var question_topic = document.getElementById("question_topic");
        question_topic.addEventListener("change", function () {
            document.getElementById("sub_topic_default").selected = true;
            var my_value = this.value;
            var options = document.getElementsByClassName("options");
            for (let index = 0; index < options.length; index++) {
                const element = options[index];
                element.classList.add("hide");
            }
            var options = document.getElementsByClassName("options_"+my_value+"");
            for (let index = 0; index < options.length; index++) {
                const element = options[index];
                element.classList.remove("hide");
            }
        });
        window.onload = function () {
            if (document.getElementById("selected_substrand") != undefined) {
                var selected_substrand = document.getElementById("selected_substrand");
                selected_substrand.classList.remove("hide");
                selected_substrand.selected = true;
            }
        }
    </script>
    {{-- <script src="/assets/js/tr_js/lessonplan.js"></script> --}}
    <!-- Vendor JS Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>

</body>
</html>

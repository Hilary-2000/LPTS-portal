<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Fees Details - {{$student_data->first_name." ".$student_data->second_name}} -
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
                                <a href="/Teacher/Alert/Read/{{$parents_notification[$i]->id}}">
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
                <a class="nav-link " href="/Parent/Fees">
                    <i class="bi bi-currency-dollar"></i>
                    <span>Student Fees</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="/Parent/Peformance">
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
            <h1>{{ucwords(strtolower($student_data->first_name))}}`s Fees Details</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/Parent/Dashboard"><i class="bi bi-house-door"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="/Parent/Fees">Student Fees</a></li>
                    <li class="breadcrumb-item active">{{ucwords(strtolower($student_data->first_name))}}`s Fees Details</li>
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
                                <a href="/Parent/Fees" class="btn btn-sm btn-secondary my-2"><i class="bi bi-arrow-left"></i> Back To My Children</a>
                                <h5 class="card-title">Note:</h5>
                                <ul>
                                    <li>View all <b>{{ucwords(strtolower($student_data->first_name))}}`s</b> fees information from this window.</li>
                                    <li>Incase of any in-correct information kindly inform your school accountant or finance office for rectification.</li>
                                </ul>
                            </div>
                        </div>
                        <!-- Recent Sales -->
                        <div class="col-12">
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body">
                                    <p class="text-danger col-md-12">{{session("invalid") != null?session("invalid") : ""}}</p>
                                    <p class="text-success col-md-12">{{session("valid") != null?session("valid") : ""}}</p>

                                    <div class="row">
                                        <div class="col-md-6 my-2 p-2  mx-auto shadow rounded-3">
                                            <h6 class="text-primary text-center">{{ucwords(strtolower($student_data->first_name." ".$student_data->second_name))}}`s Fees Balance</h6>
                                            <p class="my-1 row "><b class="col-md-4" >Fees Balance</b> <span class="col-md-8 text-secondary">:Kes {{number_format($fees_balance)}}</span></p>
                                            <p class="my-1 row "><b class="col-md-4" >Last Payment Date</b> <span class="col-md-8 text-secondary">:{{count($last_transaction) > 0 ? date("D dS M Y",strtotime($last_transaction[0]->date_of_transaction)) : "N/A"}}</span></p>
                                            <p class="my-1 row "><b class="col-md-4" >Last Amount Paid</b> <span class="col-md-8 text-secondary">:Kes {{count($last_transaction) > 0 ? number_format($last_transaction[0]->amount) : "N/A"}}</span></p>
                                            <p class="my-1 row "><b class="col-md-4" >Total Paid Since Joining</b> <span class="col-md-8 text-secondary">:Kes {{number_format($total_since_joining)}}</span></p>
                                            <hr class="my-1">
                                            <p class="my-1 row "><b class="col-md-4" >Boarding Status</b> <span class="col-md-8 text-secondary">:{{count($baording_details) > 0 ? $baording_details[0] : "Not Enrolled!"}}</span></p>
                                            <hr class="my-1">
                                            <p class="my-1 row "><b class="col-md-4" >Transport Status</b> <span class="col-md-8 text-secondary">:
                                                @if (count($transport_details) > 0)
                                                    @for ($index = 0; $index < count($transport_details); $index++)
                                                        <span>{{$index+1}}). {{$transport_details[$index]->term." ".$transport_details[$index]->route_name." @ Kes ".number_format($transport_details[$index]->route_price)}}</span><br>
                                                    @endfor
                                                @else
                                                    <span>Not-Enrolled!</span>
                                                @endif
                                                </span></p>
                                            <hr class="my-1">
                                        </div>
                                        <div class="col-md-5 my-2 p-2  mx-auto shadow rounded-3">
                                            <h6 class="text-primary text-center">Fees Payment Details</h6>
                                            @if (count($payment_details) > 0)
                                                @for ($index = 0; $index < count($payment_details); $index++)
                                                    <ul>
                                                        @if ($payment_details[$index]->show == "true")
                                                            <li class="my-1"><b>{{$payment_details[$index]->description}}</b></li>
                                                        @endif
                                                    </ul>
                                                @endfor
                                            @else
                                                <p>Payment details will be updated soon!</p>
                                            @endif
                                            <p class="my-1 "></p>
                                        </div>
                                        <form method="GET" target="_blank" action="/Parent/Print/Fees" class="col-md-11 my-4 p-2 mx-auto row shadow rounded-3">
                                            <h6 class="col-md-12 my-1 text-primary text-center">Print your Fees Statement</h6>
                                            <div class="col-md-4">
                                                <input type="hidden" name="student_adm_no" value="{{$student_data->adm_no}}" id="student_adm_no">
                                                <label for="from_date" class="form-label"><b>From</b></label>
                                                <input type="date" name="from_date" id="from_date" max="{{date("Y-m-d")}}" class="form-control" value="{{date("Y-m-d",strtotime("-7 days"))}}">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="to_date" class="form-label"><b>To</b></label>
                                                <input type="date" name="to_date" id="to_date" max="{{date("Y-m-d")}}" class="form-control" value="{{date("Y-m-d")}}">
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-primary btn-sm mt-4"  type="submit" type="submit"><i class="bi bi-printer"></i> Print</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card recent-sales overflow-auto">
                                <div class="card-body p-2">
                                    <h5 class="my-1 card-title">Term 1</h5>
                                    <p class="my-1">All the Term One transactions for <b>{{ucwords(strtolower($student_data->first_name))}}</b> have been done here.</p>
                                    <hr>
                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Amount Paid</th>
                                                <th scope="col">Date Paid</th>
                                                <th scope="col">Paid For</th>
                                                <th scope="col">Mode Of Payment</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($termly_fees); $i++)
                                                @if ($termly_fees[$i]->term == "TERM_1")
                                                    @php
                                                        $transactions = $termly_fees[$i]->transactions;
                                                        $my_totals = 0;
                                                    @endphp
                                                    @for ($index=0; $index < count($transactions); $index++)
                                                        <tr>
                                                            @php
                                                                $my_totals+=$transactions[$index]->amount;
                                                            @endphp
                                                            <th scope="row">{{$index+1}}</th>
                                                            <td style="min-width: 100px;">Kes {{number_format($transactions[$index]->amount)}}</td>
                                                            <td style="min-width: 100px;">{{date("D dS M Y",strtotime($transactions[$index]->date_of_transaction))}} @ {{$transactions[$index]->time_of_transaction}}</td>
                                                            <td style="min-width: 100px;">{{ucwords(strtolower($transactions[$index]->payment_for))}}</td>
                                                            <td style="min-width: 100px;">{{$transactions[$index]->mode_of_pay}}</td>
                                                            <td style="min-width: 100px;"><a href="/Parent/Fees/View/{{$transactions[$index]->transaction_id}}/{{$student_data->adm_no}}" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> View</a></td>
                                                        </tr>
                                                    @endfor
                                                @endif
                                            @endfor
                                        </tbody>
                                    </table>
                                    <hr class="my-1">
                                    <p class="my-2 text-primary"><b>Total Paid Term 1: </b> Kes {{number_format($my_totals)}}</p>
                                </div>
                            </div>

                            <div class="card recent-sales overflow-auto">
                                <div class="card-body p-2">
                                    <h5 class="my-1 card-title">Term 2</h5>
                                    <p class="my-1">All the Term Two transactions for <b>{{ucwords(strtolower($student_data->first_name))}}</b> have been done here.</p>
                                    <hr>
                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Amount Paid</th>
                                                <th scope="col">Date Paid</th>
                                                <th scope="col">Paid For</th>
                                                <th scope="col">Mode Of Payment</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($termly_fees); $i++)
                                                @if ($termly_fees[$i]->term == "TERM_2")
                                                    @php
                                                        $transactions = $termly_fees[$i]->transactions;
                                                        $my_totals = 0;
                                                    @endphp
                                                    @for ($index=0; $index < count($transactions); $index++)
                                                        <tr>
                                                            @php
                                                                $my_totals+=$transactions[$index]->amount;
                                                            @endphp
                                                            <th scope="row">{{$index+1}}</th>
                                                            <td style="min-width: 100px;">Kes {{number_format($transactions[$index]->amount)}}</td>
                                                            <td style="min-width: 100px;">{{date("D dS M Y",strtotime($transactions[$index]->date_of_transaction))}} @ {{$transactions[$index]->time_of_transaction}}</td>
                                                            <td style="min-width: 100px;">{{ucwords(strtolower($transactions[$index]->payment_for))}}</td>
                                                            <td style="min-width: 100px;">{{$transactions[$index]->mode_of_pay}}</td>
                                                            <td style="min-width: 100px;"><a href="/Parent/Fees/View/{{$transactions[$index]->transaction_id}}/{{$student_data->adm_no}}" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> View</a></td>
                                                        </tr>
                                                    @endfor
                                                @endif
                                            @endfor
                                        </tbody>
                                    </table>
                                    <hr class="my-1">
                                    <p class="my-2 text-primary"><b>Total Paid Term 2: </b> Kes {{number_format($my_totals)}}</p>
                                </div>
                            </div>

                            <div class="card recent-sales overflow-auto">
                                <div class="card-body p-2">
                                    <h5 class="my-1 card-title">Term 3</h5>
                                    <p class="my-1">All the Term Three transactions for <b>{{ucwords(strtolower($student_data->first_name))}}</b> have been done here.</p>
                                    <hr>
                                    <table class="table table-borderless datatable">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Amount Paid</th>
                                                <th scope="col">Date Paid</th>
                                                <th scope="col">Paid For</th>
                                                <th scope="col">Mode Of Payment</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($termly_fees); $i++)
                                                @if ($termly_fees[$i]->term == "TERM_3")
                                                    @php
                                                        $transactions = $termly_fees[$i]->transactions;
                                                        $my_totals = 0;
                                                    @endphp
                                                    @for ($index=0; $index < count($transactions); $index++)
                                                        <tr>
                                                            @php
                                                                $my_totals+=$transactions[$index]->amount;
                                                            @endphp
                                                            <th scope="row">{{$index+1}}</th>
                                                            <td style="min-width: 100px;">Kes {{number_format($transactions[$index]->amount)}}</td>
                                                            <td style="min-width: 100px;">{{date("D dS M Y",strtotime($transactions[$index]->date_of_transaction))}} @ {{$transactions[$index]->time_of_transaction}}</td>
                                                            <td style="min-width: 100px;">{{ucwords(strtolower($transactions[$index]->payment_for))}}</td>
                                                            <td style="min-width: 100px;">{{$transactions[$index]->mode_of_pay}}</td>
                                                            <td style="min-width: 100px;"><a href="/Parent/Fees/View/{{$transactions[$index]->transaction_id}}/{{$student_data->adm_no}}" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> View</a></td>
                                                        </tr>
                                                    @endfor
                                                @endif
                                            @endfor
                                        </tbody>
                                    </table>
                                    <hr class="my-1">
                                    <p class="my-2 text-primary"><b>Total Paid Term 3: </b> Kes {{number_format($my_totals)}}</p>
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

    <!-- Vendor JS Files -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>

</body>

</html>

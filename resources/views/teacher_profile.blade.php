<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Dashboard -
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
        
        <p class="text-success text-center p-2 mt-2">{{session("login_success") != null ? session("login_success") : ""}}</p>

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
                        <img src="{{ session('staff_infor') != null ? 'https://lsims.ladybirdsmis.com/sims/'.session('staff_infor')->profile_loc : 'assets/img/dp.png' }}"
                            alt="Profile" class="rounded-circle">
                        <span
                            class="d-none d-md-block dropdown-toggle ps-2">{{ session('staff_infor') != null ? ucwords(strtolower(session('staff_infor')->fullname)) : ''  }}</span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6>{{ session('staff_infor') != null ? ucwords(strtolower(session('staff_infor')->fullname)) : '' }}
                            </h6>
                            <span>{{session('staff_infor')->auth == 0 ? " Administrator" : (session('staff_infor')->auth == 1 ? "Headteacher" : (session('staff_infor')->auth == 2 ? "Deputy principal" : ((session('staff_infor')->auth == 3 || session('staff_infor')->auth == 4) ? "Teacher" : (session('staff_infor')->auth == 5 ? "Class Teacher" : session('staff_infor')->auth))))}}</span>
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
                <a class="nav-link collapsed" href="/Teacher/DiscussionForum">
                    <i class="bi bi-chat"></i>
                    <span>Discussion Forums</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-heading">Pages</li>

            <li class="nav-item">
                <a class="nav-link " href="/Teacher/Profile">
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
            <h1>Personal Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    {{-- <li class="breadcrumb-item"><a href="index.html">Home</a></li> --}}
                    {{-- <li class="breadcrumb-item active">Dashboard</li> --}}
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section profile">
            <div class="row">
              <div class="col-xl-4">
      
                <div class="card">
                  <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
      
                    <img src="{{ session('staff_infor') != null ? 'https://lsims.ladybirdsmis.com/sims/'.session('staff_infor')->profile_loc : 'assets/img/dp.png' }}"
                            alt="Profile" class="rounded-circle">
                    <h2>{{ucwords(strtolower($staff_data[0]->fullname))}}</h2>
                    <h3>{{$staff_data[0]->auth == 0 ? " Administrator" : ($staff_data[0]->auth == 1 ? "Headteacher" : ($staff_data[0]->auth == 2 ? "Deputy principal" : (($staff_data[0]->auth == 3 || $staff_data[0]->auth == 4) ? "Teacher" : ($staff_data[0]->auth == 5 ? "Class Teacher" : $staff_data[0]->auth))))}}</h3>
                    {{-- <div class="social-links mt-2">
                      <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                      <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                      <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                      <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                    </div> --}}
                    <p class="text-success">{{ session('valid') != null ? session('valid') : '' }}</p>
                    <p class="text-danger">{{ session('invalid') != null ? session('invalid') : '' }}</p>
                  </div>
                </div>
      
              </div>
      
              <div class="col-xl-8">
      
                <div class="card">
                  <div class="card-body pt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered">
      
                      <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                      </li>
      
                      <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                      </li>
      
                      <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                      </li>
      
                    </ul>
                    <div class="tab-content pt-2">
      
                      <div class="tab-pane fade show active profile-overview" id="profile-overview">
                        <h5 class="card-title">Profile Details</h5>
      
                        <div class="row">
                          <div class="col-lg-3 col-md-4 label"  style="font-size:12px;">Full Name</div>
                          <div class="col-lg-9 col-md-8" style="font-size:12px;">{{ucwords(strtolower($staff_data[0]->fullname))}}</div>
                        </div>
      
                        <div class="row">
                          <div class="col-lg-3 col-md-4 label" style="font-size:12px;">School</div>
                          <div class="col-lg-9 col-md-8" style="font-size:12px;">{{ucwords(strtolower(session("school_information")->school_name))}}</div>
                        </div>
      
                        <div class="row">
                          <div class="col-lg-3 col-md-4 label" style="font-size:12px;">User Role</div>
                          <div class="col-lg-9 col-md-8" style="font-size:12px;">{{$staff_data[0]->auth == 0 ? " Administrator" : ($staff_data[0]->auth == 1 ? "Headteacher" : ($staff_data[0]->auth == 2 ? "Deputy principal" : (($staff_data[0]->auth == 3 || $staff_data[0]->auth == 4) ? "Teacher" : ($staff_data[0]->auth == 5 ? "Class Teacher" : $staff_data[0]->auth))))}}</div>
                        </div>
      
                        <div class="row">
                          <div class="col-lg-3 col-md-4 label" style="font-size:12px;">Country</div>
                          <div class="col-lg-9 col-md-8" style="font-size:12px;">Kenya</div>
                        </div>
      
                        <div class="row">
                          <div class="col-lg-3 col-md-4 label" style="font-size:12px;">Address</div>
                          <div class="col-lg-9 col-md-8" style="font-size:12px;">{{$staff_data[0]->address}}</div>
                        </div>
      
                        <div class="row">
                          <div class="col-lg-3 col-md-4 label" style="font-size:12px;">Phone</div>
                          <div class="col-lg-9 col-md-8" style="font-size:12px;">{{$staff_data[0]->phone_number}}</div>
                        </div>
      
                        <div class="row">
                          <div class="col-lg-3 col-md-4 label" style="font-size:12px;">Email</div>
                          <div class="col-lg-9 col-md-8" style="font-size:12px;">{{$staff_data[0]->email}}</div>
                        </div>
                      </div>
      
                      <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
      
                        <!-- Profile Edit Form -->
                        <form action="/Teacher/Updateprofile" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ucwords(strtolower($staff_data[0]->user_id))}}">
                          <div class="row mb-3">
                            <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                            <div class="col-md-8 col-lg-9">
                                <img src="{{ session('staff_infor') != null ? 'https://lsims.ladybirdsmis.com/sims/'.session('staff_infor')->profile_loc : 'assets/img/dp.png' }}"
                                alt="Profile" class="rounded-circle">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="fullName" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">Full Name</label>
                            <div class="col-md-8 col-lg-9">
                              <input name="fullName" type="text" disabled class="form-control" id="fullName" value="{{ucwords(strtolower($staff_data[0]->fullname))}}">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="company" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">School</label>
                            <div class="col-md-8 col-lg-9">
                              <input name="company" type="text" disabled class="form-control" id="company" value="{{ucwords(strtolower(session("school_information")->school_name))}}">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="Job" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">User Role</label>
                            <div class="col-md-8 col-lg-9">
                              <input name="job" type="text" class="form-control" disabled id="Job" value="{{$staff_data[0]->auth == 0 ? " Administrator" : ($staff_data[0]->auth == 1 ? "Headteacher" : ($staff_data[0]->auth == 2 ? "Deputy principal" : (($staff_data[0]->auth == 3 || $staff_data[0]->auth == 4) ? "Teacher" : ($staff_data[0]->auth == 5 ? "Class Teacher" : $staff_data[0]->auth))))}}">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="Country" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">Country</label>
                            <div class="col-md-8 col-lg-9">
                              <input name="country" type="text" class="form-control" id="Country" value="Kenya" disabled>
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="Address" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">Address</label>
                            <div class="col-md-8 col-lg-9">
                              <input name="address" type="text" class="form-control" id="Address" value="{{$staff_data[0]->address}}">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="Phone" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">Phone</label>
                            <div class="col-md-8 col-lg-9">
                              <input name="phone" type="text" class="form-control" id="Phone" value="{{$staff_data[0]->phone_number}}">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="Email" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">Email</label>
                            <div class="col-md-8 col-lg-9">
                              <input name="email" type="email" class="form-control" id="Email" value="{{$staff_data[0]->email}}">
                            </div>
                          </div>
      
                          <div class="text-center">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Update Changes</button>
                          </div>
                        </form><!-- End Profile Edit Form -->
                      </div>
      
                      <div class="tab-pane fade pt-3" id="profile-change-password">
                        <!-- Change Password Form -->
                        <form method="POST" action="/Teacher/UpdatePass">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ucwords(strtolower($staff_data[0]->user_id))}}">
                          <div class="row mb-3">
                            <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">Current Password</label>
                            <div class="col-md-8 col-lg-9">
                              <input required name="password" type="password" placeholder="Current Password" class="form-control" id="currentPassword">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="newPassword" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">New Password</label>
                            <div class="col-md-8 col-lg-9">
                              <input required name="newpassword" type="password" placeholder="New Password" class="form-control" id="newPassword">
                            </div>
                          </div>
      
                          <div class="row mb-3">
                            <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label" style="font-size:12px;">Re-enter New Password</label>
                            <div class="col-md-8 col-lg-9">
                              <input required name="renewpassword" type="password" placeholder="Re-enter New Password" class="form-control" id="renewPassword">
                            </div>
                          </div>
      
                          <div class="text-center">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                          </div>
                        </form><!-- End Change Password Form -->
      
                      </div>
      
                    </div><!-- End Bordered Tabs -->
      
                  </div>
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

    <!-- Template Main JS File -->
    <script src="/assets/js/main.js"></script>

</body>

</html>

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Define project root (Tailor_Master)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Define Base URL automatically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', BASE_PATH) . '/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Tailor Management System - Admin Panel</title>
<link rel="icon" href="assets/images/thread32x32.png" type="image/x-icon">
<!-- <link rel="shortcut icon" href="assets/images/tailor-favicon.ico" type="image/x-icon"> -->

    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <!-- waves.css -->
    <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="assets/css/bootstrap/css/bootstrap.min.css">
      <!-- waves.css -->
      <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all">
      <!-- themify icon -->
      <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
      <!-- scrollbar.css -->
      <link rel="stylesheet" type="text/css" href="assets/css/jquery.mCustomScrollbar.css">
        <!-- am chart export.css -->
        <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
      <!-- Style.css -->
      <link rel="stylesheet" type="text/css" href="assets/css/style.css">

      <!-- Select2 -->
       <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


  </head>

  <body>
  <!-- Pre-loader start -->
  <div class="theme-loader">
      <div class="loader-track">
          <div class="preloader-wrapper">
              <div class="spinner-layer spinner-blue">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
              <div class="spinner-layer spinner-red">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
            
              <div class="spinner-layer spinner-yellow">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
            
              <div class="spinner-layer spinner-green">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- Pre-loader end -->
  <div id="pcoded" class="pcoded">
      <div class="pcoded-overlay-box"></div>
      <div class="pcoded-container navbar-wrapper">
          <nav class="navbar header-navbar pcoded-header">
              <div class="navbar-wrapper">
                  <div class="navbar-logo">
                      <a class="mobile-menu waves-effect waves-light" id="mobile-collapse" href="#!">
                          <i class="ti-menu"></i>
                      </a>
                      <div class="mobile-search waves-effect waves-light">
                          <div class="header-search">
                              <div class="main-search morphsearch-search">
                                  <div class="input-group">
                                      <span class="input-group-addon search-close"><i class="ti-close"></i></span>
                                      <input type="text" class="form-control" placeholder="Enter Keyword">
                                      <span class="input-group-addon search-btn"><i class="ti-search"></i></span>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <h5 class="mt-2 text-uppercase fw-bold">
  <i class="ti-cut me-1"></i>Tailor Management
</h5>

                      <a class="mobile-options waves-effect waves-light">
                          <i class="ti-more"></i>
                      </a>
                  </div>
                
                  <div class="navbar-container container-fluid">
                      <ul class="nav-left">
                          <li>
                              <div class="sidebar_toggle"><a href="javascript:void(0)"><i class="ti-menu"></i></a></div>
                          </li>
                          <li class="header-search">
                              <div class="main-search morphsearch-search">
                                  <div class="input-group">
                                      <span class="input-group-addon search-close"><i class="ti-close"></i></span>
                                      <input type="text" class="form-control">
                                      <span class="input-group-addon search-btn"><i class="ti-search"></i></span>
                                  </div>
                              </div>
                          </li>
                          <li>
                              <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                                  <i class="ti-fullscreen"></i>
                              </a>
                          </li>
                      </ul>
                      <ul class="nav-right">
                          <li class="header-notification">
                              <a href="#!" class="waves-effect waves-light">
                                  <i class="ti-bell"></i>
                                  <span class="badge bg-c-red"></span>
                              </a>
                              <ul class="show-notification">
                                  <li>
                                      <h6>Notifications</h6>
                                      <label class="label label-danger">New</label>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <div class="media">
                                          <img class="d-flex align-self-center img-radius" src="assets/images/avatar-2.jpg" alt="Generic placeholder image">
                                          <div class="media-body">
                                              <h5 class="notification-user">John Doe</h5>
                                              <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                              <span class="notification-time">30 minutes ago</span>
                                          </div>
                                      </div>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <div class="media">
                                          <img class="d-flex align-self-center img-radius" src="assets/images/avatar-4.jpg" alt="Generic placeholder image">
                                          <div class="media-body">
                                              <h5 class="notification-user">Joseph William</h5>
                                              <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                              <span class="notification-time">30 minutes ago</span>
                                          </div>
                                      </div>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <div class="media">
                                          <img class="d-flex align-self-center img-radius" src="assets/images/avatar-3.jpg" alt="Generic placeholder image">
                                          <div class="media-body">
                                              <h5 class="notification-user">Sara Soudein</h5>
                                              <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                              <span class="notification-time">30 minutes ago</span>
                                          </div>
                                      </div>
                                  </li>
                              </ul>
                          </li>
                          <li class="user-profile header-notification">
                              <a href="#!" class="waves-effect waves-light">
                                  <!-- <img src="assets/images/avatar-4.jpg" class="img-radius" alt="User-Profile-Image"> -->
                                  <span>Admin</span>
                                  <i class="ti-angle-down"></i>
                              </a>
                              <ul class="show-notification profile-notification">
                                  <!-- <li class="waves-effect waves-light">
                                      <a href="#!">
                                          <i class="ti-settings"></i> Settings
                                      </a>
                                  </li> -->
                                  <!-- <li class="waves-effect waves-light">
                                      <a href="user-profile.html">
                                          <i class="ti-user"></i> Profile
                                      </a>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <a href="email-inbox.html">
                                          <i class="ti-email"></i> My Messages
                                      </a>
                                  </li>
                                  <li class="waves-effect waves-light">
                                      <a href="auth-lock-screen.html">
                                          <i class="ti-lock"></i> Lock Screen
                                      </a>
                                  </li> -->
                                  <li class="waves-effect waves-light">
                                      <a href="logout.php">
                                          <i class="ti-layout-sidebar-left"></i> Logout
                                      </a>
                                  </li>
                              </ul>
                          </li>
                      </ul>
                  </div>
              </div>
          </nav>

          <div class="pcoded-main-container">
              <div class="pcoded-wrapper">
                  <nav class="pcoded-navbar">
  <div class="sidebar_toggle">
    <a href="#"><i class="icon-close icons"></i></a>
  </div>
  <div class="pcoded-inner-navbar main-menu">
    <!-- Logo and Title -->
    

    <!-- User Info -->
    <!-- <div class="main-menu-header">
      <img class="img-80 img-radius" src="assets/images/avatar-4.jpg" alt="User-Profile-Image">
      <div class="user-details">
        <span id="more-details">John Doe<i class="fa fa-caret-down"></i></span>
      </div>
    </div>
    <div class="main-menu-content">
      <ul>
        <li class="more-details">
          <a href="user-profile.html"><i class="ti-user"></i> View Profile</a>
          <a href="#"><i class="ti-settings"></i> Settings</a>
          <a href="auth-normal-sign-in.html"><i class="ti-layout-sidebar-left"></i> Logout</a>
        </li>
      </ul>
    </div> -->

    <!-- Navigation Menu -->
    <div class="pcoded-navigation-label" data-i18n="nav.category.navigation">Main Menu</div>
    <ul class="pcoded-item pcoded-left-item">

   


      <li>
        <a href="index.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-home"></i></span>
          <span class="pcoded-mtext">Dashboard</span>
        </a>
      </li>






       <li class="pcoded-hasmenu">
                                    <a href="javascript:void(0)" class="waves-effect waves-dark">
                                        <span class="pcoded-micon"><i class="ti-layout-grid2-alt"></i></span>
                                        <span class="pcoded-mtext"  data-i18n="nav.basic-components.main">Cloth</span>
                                        <span class="pcoded-mcaret"></span>
                                    </a>
                                    <ul class="pcoded-submenu">
                                        <li>
      <a href="cloth_master.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-wallet"></i></span>
          <span class="pcoded-mtext">Cloth Master</span>
        </a>
      </li>


       <li>
      <a href="cloth_type.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-wallet"></i></span>
          <span class="pcoded-mtext">Cloth Type</span>
        </a>
      </li>
                                       
                                    </ul>
                                </li>












       


      <li>
        <a href="customers.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-user"></i></span>
          <span class="pcoded-mtext">Customers</span>
        </a>
      </li>
      <li>
        <a href="karigars.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-id-badge"></i></span>
          <span class="pcoded-mtext">Karigars</span>
        </a>
      </li>
      <li>
        <a href="orders.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-shopping-cart"></i></span>
          <span class="pcoded-mtext">Orders</span>
        </a>
      </li>
      <!-- <li>
        <a href="customer_payments.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-wallet"></i></span>
          <span class="pcoded-mtext">Payments</span>
        </a>
      </li> -->
      <li>
        <a href="measurements.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-ruler-pencil"></i></span>
          <span class="pcoded-mtext">Measurements</span>
        </a>
      </li>



      <li>
        <a href="payments.php" class="waves-effect waves-dark">
          <span class="pcoded-micon"><i class="ti-wallet"></i></span>
          <span class="pcoded-mtext">Payments</span>
        </a>
      </li>
      



    </ul>
  </div>
</nav>

                  <div class="pcoded-content">

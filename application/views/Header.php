<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php echo HTTP_CSS_PATH; ?>favicon.png">
    <title>Home</title>
   <!-- Bootstrap core CSS -->
    <link href="<?php echo HTTP_CSS_PATH; ?>bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />
    <!-- Add custom CSS here -->
    <link href="<?php echo HTTP_CSS_PATH; ?>arkadmin.css" rel="stylesheet">
      <!-- JavaScript -->
    <script src="<?php echo HTTP_JS_PATH; ?>jquery-1.10.2.js"></script>
    <script src="<?php echo HTTP_JS_PATH; ?>bootstrap.js"></script>
    <script src="<?php echo HTTP_JS_PATH; ?>das.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php echo HTTP_JS_PATH; ?>html5shiv.js"></script>
      <script src="<?php echo HTTP_JS_PATH; ?>respond.min.js"></script>
    <![endif]-->
  </head>
<body>
    <?php
    $pg = isset($page) && $page != '' ?  $page :'home'  ;    
    ?>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo base_url(); ?>"><span style=" font-style: italic;font-weight: bold;">Crystal Properties</span></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li <?php echo  $pg =='home' ? 'class="active"' : '' ?>><a href="<?php echo base_url(); ?>">Dashboard</a></li>
            <li <?php echo  $pg =='setting' ? 'class="active"' : '' ?>><a href="<?php echo base_url('home/setting'); ?>">Setting</a></li>
            <li <?php echo  $pg =='status' ? 'class="active"' : '' ?>><a href="<?php echo base_url('home/systemStatus'); ?>">Status</a></li>
            <li <?php echo  $pg =='aboutus' ? 'class="active"' : '' ?>><a href="javascript:void(0);">About Us</a></li>
            
          </ul>
          <ul class="nav navbar-nav navbar-right navbar-user">
            <li class="dropdown user-dropdown">
              <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-user"></i> <?php echo $this->session->userdata('username');?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo base_url('dashboard/logout'); ?>"><i class="fa fa-power-off"></i>Log Out</a></li>
              </ul>
            </li>
              
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

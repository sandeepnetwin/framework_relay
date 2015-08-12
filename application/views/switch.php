<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
    $this->load->model('home_model');
    $sDeviceFullName = 'Valve';
    
?>  

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php echo HTTP_CSS_PATH; ?>favicon.png">
    <title>Home</title>
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

    
    
<link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
<link href="<?php echo site_url('assets/switchy/bootstrap.min.css'); ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>

<div id="page-wrapper">
    
    
    <select id="switch-me" style="box-sizing: content-box !important;">
                                <option value="left">Left</option>
                                <option value="" selected="selected"></option>
                                <option value="right">Right</option>
                                </select>
</div>

<script type="text/javascript">
    $(function() {

    $('#switch-me').switchy();

    $('.gender').on('click', function(){
        $('#switch-me').val($(this).attr('gender')).change();
    });

    $('#switch-me').on('change', function(){
        console.log('Y');
        // Animate Switchy Bar background color
        var bgColor = '#ccb3dc';

        if ($(this).val() == 'left'){
        bgColor = '#ed7ab0';
        } else if ($(this).val() == 'right'){
        bgColor = '#7fcbea';
        }

        $('.switchy-bar').animate({
        backgroundColor: bgColor
        });

        // Display action in console
        var log ='Selected value is "'+$(this).val()+'"';
        console.log(log);
        });
    });
</script>

<?php
//$this->load->view('Footer');
?>
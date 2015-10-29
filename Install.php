<?php
if($sIP == '')
  $sIP =  IP_ADDRESS;

if($sPort == '')
  $sPort =  PORT_NO; 

?>

<?php
	//@session_start();
	$strTitle = isset($Title) && $Title != '' ?  $Title :'Dashboard'  ;  

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE, NO-STORE, must-revalidate">
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
	<META HTTP-EQUIV="EXPIRES" CONTENT=0>
	
	<link rel="shortcut icon" href="<?php echo HTTP_CSS_PATH; ?>favicon.png">
    <title>Relay Board <?php echo '- '.$strTitle;?></title>
   <!-- main JS libs -->
	<script src="<?php echo HTTP_JS_PATH; ?>libs/jquery-1.10.2.min.js"></script>
	<script src="<?php echo HTTP_JS_PATH; ?>libs/jquery-ui.min.js"></script>
	<script src="<?php echo HTTP_JS_PATH; ?>libs/bootstrap.min.js"></script>

	<!-- Style CSS -->
	<link href="<?php echo HTTP_CSS_PATH; ?>bootstrap.css" media="screen" rel="stylesheet">
	<link href="<?php echo HTTP_ASSETS_PATH; ?>style.css" media="screen" rel="stylesheet">

	<!-- General Scripts -->
	<script src="<?php echo HTTP_JS_PATH; ?>general.js"></script>

	<!-- custom input -->
	<script src="<?php echo HTTP_JS_PATH; ?>jquery.customInput.js"></script>
  
	<link rel="stylesheet" href="<?php echo HTTP_CSS_PATH; ?>chosen.css">
	<script src="<?php echo HTTP_JS_PATH; ?>chosen.jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo HTTP_JS_PATH; ?>jquery.powerful-placeholder.min.js"></script>
	
	<script>
		jQuery(document).ready(function($) {
			if($("[placeholder]").size() > 0) {
				$.Placeholder.init();
			}
			
			var pull 		= $('#pull');
				menu 		= $('nav ul');
				menuHeight	= menu.height();

			$(pull).on('click', function(e) {
				e.preventDefault();
				menu.slideToggle();
			});

			$(window).resize(function(){
        		var w = $(window).width();
        		if(w > 320 && menu.is(':hidden')) {
        			menu.removeAttr('style');
        		}
    		});
		});
		
		function showRestrict()
		{
			alert("You don't have access to this section!");
			//$("#checkLinkNew").trigger('click');
			return false;
		}
		
	</script>
	
	<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ASSETS_PATH.'horizontalmenu/css/font-awesome.css'; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ASSETS_PATH.'horizontalmenu/css/menu.css'; ?>">
    <script type="text/javascript" src="<?php echo HTTP_ASSETS_PATH.'horizontalmenu/js/function.js'; ?>"></script>
	
  </head>
<body>
    <?php
    $pg = isset($page) && $page != '' ?  $page :'home'  ;    
    ?>
	<a class="fancyboxNew" id="checkLinkNew" href="#inline1New" style="display:none;">&nbsp;</a>
		<div id="inline1New" style="width:250px;height:40px; display:none;"><div >You Don't have access to this section!</div></div>
	
    <div class="body-wrap">
	
    <div class="content">
	
        <!--container-->
        <div class="container">
			<div id="wrap">
				<header>
					<div class="innerMenu relative">
						<a style="color:#ffffff !important; text-decoration:none;" href="<?php echo base_url();?>" class="logo">
						<span style="font: italic bold 33px/40px Times New Roman">Crystal Properties</span>
						</a>
						<div class="clear"></div>
					</div>
				</header>	
			</div>	
			<div class="row">&nbsp;</div>
			
    <?php
    $pg = isset($page) && $page != '' ?  $page :'home'  ;    
    ?>

    <div id="page-wrapper">
		<div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
				<li>System Configuration</li>
			</ol>
            <?php if($sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Details saved successfully! 
              </div>
            <?php } ?>
            <?php if($err_sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable" style="background-color: #FFC0CB;border: 1px solid #FFC0CB; color:red;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                IP and Port details required! 
              </div>
            <?php } ?>
		  </div>
        </div><!-- /.row -->
		
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading" style="border-top-left-radius: 0px;border-top-right-radius: 0px;">
                <h3 class="panel-title" style="color:#FFF;">Setting Page</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php echo site_url('dashboard/install');?>" method="post">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr>
                        <td width="10%"><strong>IP ADDRESS:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter ip address" name="relay_ip_address" value="<?php echo $sIP;?>" id="relay_ip_address"></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>PORT NO:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter port no" name="relay_port_no" value="<?php echo $sPort;?>" id="relay_port_no"></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><span class="btn btn-green btn-middle"><input type="submit" name="command" value="Save Setting"></span></td></tr>
                      
                    </table>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
<script type="text/javascript">

</script>
<hr />
     <footer>
     <p style="text-align:center; color:#FFF;">&nbsp; &copy; <?php echo date('Y') ?> Crystal Properties & Investments, Inc. All rights reserved. </p>
     </footer>
     
    <!-- Placed at the end of the document so the pages load faster -->
    </div><!-- /#container -->
     </div><!-- /.content -->
    </div><!-- /.body-wrap --> 
	
	
  </body>
</html>
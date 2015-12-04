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
			<?php 
								
				  $strSettingUrl	=	'href="'.base_url('home/setting/').'"';
				  $strStatusUrl		=	'href="'.base_url('home/systemStatus/').'"';
				  $strLogUrl		=	'href="'.base_url('home/getLogDetails/').'"';
				  $strPoolSpaUrl	=	'href="'.base_url('home/PoolSpaSetting/').'"';
				  $strModeUrl		=	'href="'.base_url('analog/changeModeManual/').'"';
				  
				  if(!empty($aModules))
				  {
						if(!in_array(12,$aModules->ids)) 
						{
							$strSettingUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(13,$aModules->ids)) 
						{
							$strStatusUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(15,$aModules->ids)) 
						{
							$strLogUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}  
						if(!in_array(18,$aModules->ids)) 
						{
							$strPoolSpaUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(4,$aModules->ids)) 
						{
							$strModeUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}						
				  }
				  
				  $strRelayUrl			=	'href="'.base_url('home/setting/R/').'"';
				  $strPowerUrl			=	'href="'.base_url('home/setting/P/').'"';
				  $strValveUrl			=	'href="'.base_url('home/setting/V/').'"';
				  $strPumpUrl			=	'href="'.base_url('home/setting/PS/').'"';
				  $strTempUrl			=	'href="'.base_url('home/setting/T/').'"';
				  
				  if(!empty($aModules))
				  {
						if(!in_array(3,$aModules->ids)) 
						{
							$strRelayUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(3,$aModules->ids)) 
						{
							$strPowerUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(8,$aModules->ids)) 
						{
							$strValveUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}  
						if(!in_array(9,$aModules->ids)) 
						{
							$strPumpUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(10,$aModules->ids)) 
						{
							$strTempUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						
				  }
				  
				  $strLightUrl			=	'href="'.base_url('analog/showLight/').'"';
				  $strHeaterUrl			=	'href="'.base_url('analog/showHeater/').'"';
				  $strBlowerUrl			=	'href="'.base_url('analog/showBlower/').'"';
				  $strMiscUrl			=	'href="'.base_url('analog/showMisc/').'"';
				  
				  
				  if(!empty($aModules))
				  {
						if(!in_array(16,$aModules->ids)) 
						{
							$strRelayUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(17,$aModules->ids)) 
						{
							$strPowerUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
						if(!in_array(19,$aModules->ids)) 
						{
							$strValveUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}  
						if(!in_array(19,$aModules->ids)) 
						{
							$strMiscUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
						}
				  }
				  
			?>
			<div id="wrap">
				<header>
					<div class="innerMenu relative">
						<a style="color:#ffffff !important; text-decoration:none;" href="<?php echo base_url();?>" class="logo">
						<span style="font: italic bold 33px/40px Times New Roman">Crystal Properties</span>
						</a>
						<a id="menu-toggle" class="button dark" href="#"><i class="icon-reorder"></i></a>
						<nav id="navigation">
							<ul id="main-menu">
								<li <?php if($pg == 'home') {echo 'class="current-menu-item"';}?>><a href="<?php echo base_url();?>">Home</a></li>
								<!--<li class="parent">
									<a href="javascript:void(0);">Switches</a>
									<ul class="sub-menu">
										<li><a <?php echo $strRelayUrl;?>><i class="glyphicon glyphicon-flash"></i>24V AC Relays</a></li>
										<li><a <?php echo $strPowerUrl;?>><i class="glyphicon glyphicon-flash"></i>12V DC Relays</a></li>
										<li><a <?php echo $strTempUrl;?>><i class="glyphicon glyphicon-flash"></i>Temeprature</a></li>
									</ul>
								</li>-->
								<li class="parent">
									<a href="javascript:void(0);">Devices</a>
									<ul class="sub-menu">
										<li class="parent">
											<a href="javascript:void(0);">Switches</a>
											<ul class="sub-menu">
												<li><a <?php echo $strRelayUrl;?>>24V AC Relays</a></li>
												<li><a <?php echo $strPowerUrl;?>>12V DC Relays</a></li>
												<li><a <?php echo $strTempUrl;?>>Temeprature</a></li>
											</ul>
										</li>
										<li><a <?php echo $strValveUrl;?>>Valves</a></li>
										<li><a <?php echo $strPumpUrl;?>>Pumps</a></li>
										<li><a <?php echo $strBlowerUrl;?>>Blower</a></li>
										<li><a <?php echo $strHeaterUrl;?>>Heater</a></li>
										<li><a <?php echo $strLightUrl;?>>Lights</a></li>
										<li><a <?php echo $strMiscUrl;?>>Miscelleneous Device</a></li>
									</ul>
								</li>
								<!--<li <?php if($pg == 'device') {echo 'class="current-menu-item"';}?>><a href="<?php echo base_url('home/PoolSpaSetting/');?>">Pool & Spa</a></li>-->
								<li class="parent">
									<a <?php if($pg == 'setting' || $pg == 'status' || $pg == 'log') {echo 'class="current-menu-item"';}?> href="javascript:void(0);">System</a>
									<ul class="sub-menu">
										<li class="parent"><a href="javascript:void(0);" >Settings</a>
											<ul class="sub-menu">
												<li><a <?php echo $strSettingUrl;?>><i class="glyphicon glyphicon-cog"></i>Basic Setting</a></li>
												<li><a href="<?php echo base_url('home/PoolSpaSetting/');?>"><i class="glyphicon glyphicon-cog"></i>Advance Setting</a></li>
											</ul>
										</li>
										<li><a <?php echo $strModeUrl;?>><i class="glyphicon glyphicon-sort"></i>Mode</a></li>
										<li><a <?php echo $strStatusUrl;?>><i class="glyphicon glyphicon-ok"></i>Status</a></li>
										<li><a <?php echo $strLogUrl;?>><i class="glyphicon glyphicon-folder-open"></i>Log</a></li>
									</ul>
								</li>
								<li class="parent"><a href="javascript:void(0);">User</a>
								<ul class="sub-menu">
									<li><a href="<?php echo base_url('dashboard/logout'); ?>"><i class="glyphicon glyphicon-lock"></i>Logout</a></li>
									<?php if($this->session->userdata('user_type') == 'SA') { ?>
									<li><a href="<?php echo base_url('dashboard/users/');?>"><i class="glyphicon glyphicon-user"></i>Sub Users</a></li>
									<li><a href="<?php echo base_url('dashboard/module/');?>"><i class="glyphicon glyphicon-th-list"></i>Modules</a></li>
									<li><a href="<?php echo base_url('dashboard/position/');?>"><i class="glyphicon glyphicon-th"></i>Positions</a></li>
									<?php } ?>
								</ul>
								</li>
							</ul>
						</nav>
						<div class="clear"></div>
					</div>
				</header>	
			</div>	
			<div class="row">&nbsp;</div>
			
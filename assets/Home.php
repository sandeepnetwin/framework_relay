<?php
	$strRole	=	'';
	if($this->session->userdata('user_type') == 'SA')
	{
		$strRole	=	'Super Admin';
	}
	else
	{
		$strRole	=	'Admin';
	}
?>
<style>
.custom-panel-heading
{
	height : 132px !important;
}

.clock {width:800px; margin:0 auto; padding:30px; border:1px solid #333; color:#fff; }

#Date {  font-size:12px; text-align:center; text-shadow:0 0 5px #00c6ff; }

.ulClock { margin:0 auto; padding:0px; list-style:none; text-align:center; }
.ulClock > li { display:inline; font-size:22px; text-align:center;text-shadow:0 0 5px #00c6ff; }

#point { position:relative; -moz-animation:mymove 1s ease infinite; -webkit-animation:mymove 1s ease infinite; padding-left:2px; padding-right:2px; }

@-webkit-keyframes mymove 
{
0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
50% {opacity:0; text-shadow:none; }
100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }	
}


@-moz-keyframes mymove 
{
0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
50% {opacity:0; text-shadow:none; }
100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }	
}

</style>
<script type="text/javascript">
jQuery(document).ready(function() {
// Create two variable with the names of the months and days in an array
var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]; 
var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]

// Create a newDate() object
var newDate = new Date();
// Extract the current date from Date object
newDate.setDate(newDate.getDate());
// Output the day, date, month and year    
jQuery('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

 setInterval( function() {
	jQuery.getJSON('<?php echo site_url('home/getCurrentServerTime/');?>', function(json) {
		//$("#clock1").html(json.time);
		var sTime			=	json.time;
		var aTimeDetails	=	sTime.split(':');
		jQuery("#hours").html(aTimeDetails[0]);
		jQuery("#min").html(aTimeDetails[1]);
		jQuery("#sec").html(aTimeDetails[2]);
	});
	},1000);
setInterval( function() {
	jQuery.getJSON('<?php echo site_url('home/getModeTime/');?>', function(json) {
		jQuery("#welcomeMessage").html(json.message);
	});
	},60000); 	
	
}); 
</script>
<div class="row">
	<div class="col-sm-3">
	<!-- Profile -->
		<div class="widget-container widget-profile boxed blue-line">
			<div class="inner">
				<h5 class="profile-title"><strong><?php echo $this->session->userdata('username');?></strong></h5>
				<span class="profile-subtitle">Role : <?php echo $strRole;?></span>
			</div>
		</div>
		<!--/ Profile -->
	</div>
	<!-- Message Field -->
	<div class="col-sm-6">
		<div class="comment-list message-field">
			<ol>
				<li class="comment" style="padding-left:0px;">
					<div class="comment-body boxed green-line">
						<div class="comment-text">
							<div class="comment-author"><a href="#" class="link-author">System Message</a></div>
							<div class="comment-entry"><?php if($welcome_message == '' ){ echo 'Welcome to Crystal Properties!'; } else { echo $welcome_message;} ?></div>
						</div>
					</div>
				</li>
			</ol>
		</div>
		<!--/ Message Field -->
	</div>
	<div class="col-sm-3">
	<!-- Time -->
		<div class="widget-container widget-profile boxed blue-line">
			<div class="inner">
			<h5 class="profile-title"><div id="Date"></div></h5>
			<span class="profile-subtitle" style="font-style:normal; color:#1D4B72;">	
				<ul class="ulClock" style="margin-top:-5px;">
					<li id="hours" class="liClock"> </li>
					<li id="point" class="liClock">:</li>
					<li id="min" class="liClock"> </li>
					<li id="point" class="liClock">:</li>
					<li id="sec" class="liClock"> </li>
				</ul>
			</span>	
			</div>
		</div>
	<!--/ Time -->
	</div>
</div>
<!-- row Level 2 -->
<div class="row">
	<!-- Tabs -->
	<div class="col-sm-8">
	<div class="tabs-framed tabs-small boxed green-line">
		<ul class="tabs clearfix">
			<li class="active"><a href="#24VAC" data-toggle="tab">24V AC Relays</a></li>
			<li><a href="#12VAC" data-toggle="tab">12V DC Relays</a></li>
			<li><a href="#Valve" data-toggle="tab">Valves</a></li>
			<li><a href="#Pump" data-toggle="tab">Pumps</a></li>
			<li><a href="#Temperature" data-toggle="tab">Temperature</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade in active" id="24VAC">
				<!-- Price item -->
				<div class="price-item style4">
					<div class="price-content clearfix">
						<div class="price-content-left">
							<div class="price-image">
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/rlb.jpg" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title"><a href="#"><span style="color:#1A315F">Total : <?php echo $relay_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;">Active : <?php echo $activeCountRelay;?></span></h2>
							<div class="price-desc">
							
							</div>
							<div class="price clearfix">
								<strong>ON : <?php echo $OnCountRelay;?></strong><strong style="color:#1A315F; margin-left:10px;">OFF : <?php echo $OFFCountRelay;?></strong>
							</div>
						</div>
					</div>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" onClick="location.href='<?php echo base_url('home/setting/R/');?>';">Configure</span>
						<a href="<?php echo base_url('home/setting/R/');?>" class="price-reserve">Switch ON/OFF</a>
					</div>
				</div>
				<!--/ Price item -->
			</div>
			
			<div class="tab-pane fade in" id="12VAC">
				<!-- Price item -->
				<div class="price-item style4">
					<div class="price-content clearfix">
						<div class="price-content-left">
							<div class="price-image">
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/rlb.jpg" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title"><a href="#"><span style="color:#1A315F">Total : <?php echo $power_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;">Active : <?php echo 8?></span></h2>
							<div class="price-desc">
							
							</div>
							<div class="price clearfix">
								<strong>ON : <?php echo $OnCountPower;?></strong><strong style="color:#1A315F; margin-left:10px;">OFF : <?php echo $OFFCountPower;?></strong>
							</div>
						</div>
					</div>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" onClick="location.href='<?php echo base_url('home/setting/P/');?>';">Configure</span>
						<a href="<?php echo base_url('home/setting/P/');?>" class="price-reserve">Switch ON/OFF</a>
					</div>
				</div>
				<!--/ Price item -->
			</div>
			
			<div class="tab-pane fade in" id="Valve">
				<!-- Price item -->
				<div class="price-item style4">
					<div class="price-content clearfix">
						<div class="price-content-left">
							<div class="price-image">
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/valve.jpg" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title"><a href="#"><span style="color:#1A315F">Total : <?php echo $valve_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;">Active : <?php echo $activeCountValve?></span></h2>
							<div class="price-desc">
							
							</div>
							<div class="price clearfix">
								<strong>ON : <?php echo $OnCountValve?></strong><strong style="color:#1A315F; margin-left:10px;">OFF : <?php echo $OFFCountValve?></strong>
							</div>
						</div>
					</div>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" onClick="location.href='<?php echo base_url('home/setting/V/');?>';">Configure</span>
						<a href="<?php echo base_url('home/setting/V/');?>" class="price-reserve">Switch ON/OFF</a>
					</div>
				</div>
				<!--/ Price item -->
			</div>
			
			<div class="tab-pane fade in" id="Pump">
				<!-- Price item -->
				<div class="price-item style4">
					<div class="price-content clearfix">
						<div class="price-content-left">
							<div class="price-image">
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/pump.jpg" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title"><a href="#"><span style="color:#1A315F">Total : <?php echo $pump_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;">Active : <?php echo $activeCountPump?></span></h2>
							<div class="price-desc">
							
							</div>
							<div class="price clearfix">
								<strong>ON : <?php echo $OnCountPump?></strong><strong style="color:#1A315F; margin-left:10px;">OFF : <?php echo $OFFCountPump?></strong>
							</div>
						</div>
					</div>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" onClick="location.href='<?php echo base_url('home/setting/PS/');?>';">Configure</span>
						<a href="<?php echo base_url('home/setting/PS/');?>" class="price-reserve">Switch ON/OFF</a>
					</div>
				</div>
				<!--/ Price item -->
			</div>
			
			<div class="tab-pane fade in" id="Temperature">
				<!-- Price item -->
				<div class="price-item style4">
					<div class="price-content clearfix">
						<div class="price-content-left">
							<div class="price-image">
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/rlb.jpg" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title"><a href="#"><span style="color:#1A315F">Total : <?php echo $temprature_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;">Active : <?php echo $activeCountTemperature;?></span></h2>
						</div>
					</div>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" onClick="location.href='<?php echo base_url('home/setting/T/');?>';">Configure</span>
						<a href="<?php echo base_url('home/setting/T/');?>" class="price-reserve">Temperature Sensors</a>
					</div>
				</div>
				<!--/ Price item -->
			</div>
		</div>
	</div>
	<!--/ Tabs -->

	</div>
	<div class="col-sm-4">
		<!-- Ribbons -->
		<div class="ribbons boxed blue-line">
			<div class="inner">
				<div class="ribbon" style="width:90%"><a href="<?php echo base_url('analog/changeModeManual/');?>"><span style="color:#000;">Modes</span></a></div>
			</div>
			<div class="inner">
				<div class="ribbon ribbon-green" style="width:90%"><span>Lights</span></div>
			</div>
			<div class="inner">
				<div class="ribbon" style="width:90%"><a href="<?php echo base_url('home/SpaDevice/');?>"><span style="color:#000;">Spa Devices</span></a></div>
			</div>
			<div class="inner">
				<div class="ribbon ribbon-green" style="width:90%"><a href="<?php echo base_url('home/PoolDevice/');?>"><span>Pool Devices</span></a></div>
			</div>
		</div>
		<!--/ Ribbons -->
	</div>
</div>
<!--/ row Level 2 -->
<!-- row Level 3 -->
<div class="row">
<div class="col-sm-3">
	<!-- Profile -->
		<div class="controls boxed blue-line" style="min-height:210px;">
			<div class="inner">
				<h3 class="profile-title"><strong style="color:#9CD70E;">Temperature</strong></h3>
				<span class="price-title" style="color:#C9376E;"><strong><?php echo $sTemperature;?></strong></span>
				<div class="price-bottom clearfix" style="margin-top:25px;"><a href="#" hidefocus="true" style="outline: medium none;">
						</a><a class="price-reserve" href="<?php echo base_url('home/setting/T/');?>" hidefocus="true" style="outline: medium none; font-size:13px;">Temperature Sensors</a>
					</div>
			</div>
		</div>
		<!--/ Profile -->
	</div>
<div class="col-sm-3">
	<!-- Profile -->
		<div class="controls boxed green-line" style="min-height:210px;">
			<div class="inner">
				<h3 class="profile-title"><strong style="color:#C9376E;">Input</strong></h3>
				<span class="profile-subtitle" style="font-size:40px; color:#9CD70E;">Total : 4</span>
				<div style="height:40px;">&nbsp;</div>
				<div class="price-bottom clearfix" style="margin-top:25px;"><a href="#" hidefocus="true" style="outline: medium none;">
						</a><a class="price-reserve" href="<?php echo base_url('analog/');?>" hidefocus="true" style="outline: medium none; font-size:13px;">Assign Device To Input</a>
					</div>
			</div>
		</div>
		<!--/ Profile -->
	</div>	
<div class="col-sm-6">
	<!-- widget Tags-->
	<div class="widget-container widget-tags styled boxed blue-line">
		<div class="inner">
			<h3 class="widget-title">Pool & Spa Sequence Device<i></i></h3>
			<div class="tagcloud clearfix">
				<a href="#"><span>Total : 10</span></a>
				<a href="#"><span>Active : 5</span></a>
				<a href="#"><span>Current Pool Temperature : 94.6F</span></a>
				<a href="#"><span>Current Spa Temperature : 94.6F</span></a>
				<a href="#"><span>Current On Device : Pump</span></a>
			</div>
		</div>
	</div>
	<!--/ widget Tags-->
</div>		
</div>	
<!--/ row Level 3 -->		
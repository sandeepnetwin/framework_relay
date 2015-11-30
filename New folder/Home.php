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

#Date {  font-size:12px; text-align:center;  }
/* text-shadow:0 0 5px #00c6ff; */

.ulClock { margin:0 auto; padding:0px; list-style:none; text-align:center; }
.ulClock > li { display:inline; font-size:22px; text-align:center; }

/* text-shadow:0 0 5px #00c6ff; */

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

setInterval( function() {
	
	$.ajax({
                type: "POST",
                url: "<?php echo site_url('home/getAllDeviceStatus/');?>", 
                data: {},
                success: function(data) {
					
					var obj = jQuery.parseJSON( data );
					var analog = obj.arrStatus;
					$.each(analog, function(index,value){
						var strChk	=	'';
						if(value.status == '1')
							strChk	=	'class="checked"';
						
						$("#deviceInput"+index).html('<div class="custom-checkbox"><input type="checkbox" value="'+index+'|||'+value.device+'" id="relay-'+index+'" name="relay-'+index+'" class="relayButton" hidefocus="true" style="outline: medium none;" onclick="onoffAnalog(this.value);"><label '+strChk+' id="lableRelay-'+index+'" for="relay-'+index+'"></label></div>');
						
						$("#deviceInputName"+index).html(value.name);
						
						strChk	=	'';
					});
					//24V AC Relays Status
					$("#deviceTotalRelays").html('Total : '+obj.relay_count);
					$("#deviceActiveRelays").html('Active : '+obj.activeCountRelay);
					$("#deviceOnRelays").html('ON : '+obj.OnCountRelay);
					$("#deviceOffRelays").html('OFF : '+obj.OFFCountRelay);
					
					//12V DC Relays Status
					$("#deviceTotalPower").html('Total : '+obj.power_count);
					//$("#deviceActivePower").html('Active : '+obj.activeCountRelay);
					$("#deviceOnPower").html('ON : '+obj.OnCountPower);
					$("#deviceOffPower").html('OFF : '+obj.OFFCountPower);
					
					//Valve Status
					$("#deviceTotalValve").html('Total : '+obj.valve_count);
					$("#deviceActiveValve").html('Active : '+obj.activeCountValve);
					$("#deviceOnValve").html('ON : '+obj.OnCountValve);
					$("#deviceOffValve").html('OFF : '+obj.OnCountValve);
					
					//Pump Status
					$("#deviceTotalPump").html('Total : '+obj.pump_count);
					$("#deviceActivePump").html('Active : '+obj.activeCountPump);
					$("#deviceOnPump").html('ON : '+obj.OnCountPump);
					$("#deviceOffPump").html('OFF : '+obj.OFFCountPump);
					
					//Temperature Status
					$("#deviceTotalTemp").html('Total : '+obj.temprature_count);
					$("#deviceActiveTemp").html('Active : '+obj.activeCountTemperature);
				}
		});
	
	},5000); 	

	$(".relayButton").click(function(){
		var clkVal		=	$(this).val();
		var aDetails	=	clkVal.split('|||');
		
		var input		=	aDetails[0];
		var device		=	aDetails[1];
		var status		=	'';
		
		if($("#lableRelay-"+input).hasClass('checked'))
		{	
			status	=	0;
		}
		else
		{
			status = 1;
		}
		
		$.ajax({
                type: "POST",
                url: "<?php echo site_url('home/makeInputDeviceOnOff');?>", 
                data: {input:input,device:device,status:status},
                success: function(data) 
				{
					if($("#lableRelay-"+input).hasClass('checked'))
					{	
						$("#lableRelay-"+input).removeClass('checked');
					}
					else
					{
						$("#lableRelay-"+input).addClass('checked');
					}
				}
		});
	});
}); 

function onoffAnalog(clkVal)
{
	var aDetails	=	clkVal.split('|||');
	
	var input		=	aDetails[0];
	var device		=	aDetails[1];
	var status		=	'';
	
	if($("#lableRelay-"+input).hasClass('checked'))
	{	
		status	=	0;
	}
	else
	{
		status = 1;
	}
	
	$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/makeInputDeviceOnOff');?>", 
			data: {input:input,device:device,status:status},
			success: function(data) 
			{
				if($("#lableRelay-"+input).hasClass('checked'))
				{	
					$("#lableRelay-"+input).removeClass('checked');
				}
				else
				{
					$("#lableRelay-"+input).addClass('checked');
				}
			}
	});
}
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
			<?php if($Remote_Spa == '1'){ ?>
			<li class="active"><a href="#remote" data-toggle="tab">Remote</a></li>
			<?php } ?>
			<li <?php if($Remote_Spa == '0') { echo 'class="active"'; } ?>><a href="#24VAC" data-toggle="tab">Switches</a></li>
			<li><a href="#Valve" data-toggle="tab">Valves</a></li>
			<li><a href="#Pump" data-toggle="tab">Pumps</a></li>
			<li><a href="#Temperature" data-toggle="tab">Temperature</a></li>
		</ul>
		<div class="tab-content">
			<?php if($Remote_Spa == '1')	{ ?>
			<div class="tab-pane fade in active" id="remote">
					<style>
					.circle-container {
						position: relative;
						width: 20em;
						height: 20em;
						padding: 2.8em; /*= 2em * 1.4 (2em = half the width of an img, 1.4 = sqrt(2))*/
						border: dashed 1px;
						border-radius: 50%;
						margin: 1.75em auto 0;
					}
					.circle-container a {
						display: block;
						overflow: hidden;
						position: absolute;
						top: 50%; left: 50%;
						width: 10em; height: 5em;
						margin: -1em -2em -2em; /* 2em = 4em/2 */ /* half the width */
						
					}
					.circle-container img { display: block; width: 100%; }
					.deg0 { transform: translate(7em); } /* 12em = half the width of the wrapper */
					.deg45 { transform: rotate(90deg) translate(7em) rotate(-90deg); }
					.deg135 { transform: rotate(135deg) translate(12em) rotate(-135deg); }
					.deg180 { transform: translate(-7em); }
					.deg225 { transform: rotate(270deg) translate(7em) rotate(-270deg); }
					.deg315 { transform: rotate(315deg) translate(12em) rotate(-315deg); }


					</style>
					<?php
						
						$iResultCnt =   count($aAPResult);
						$arrStatus	=	array();
						for($i=0; $i<$iResultCnt; $i++)
						{
							if($aAPResult[$i] != '')
							{
								$arrStatus[$i]['device']	=	$aAPResult[$i];
								$aDevice = explode('_',$aAPResult[$i]);
								
								if($aDevice[1] != '')
								{
									if($aDevice[1] == 'R')
									{
										if($sRelays[$aDevice[0]] != '' && $sRelays[$aDevice[0]] != '.')
										{
											
											$arrStatus[$i]['status']	=	$sRelays[$aDevice[0]];
											$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'R');
											if($arrStatus[$i]['name'] == '')
												$arrStatus[$i]['name'] = 'Relay '.$aDevice[0];
											
										}
										//exex('rlb m 0 2 1');
									}
									if($aDevice[1] == 'P')
									{
										$arrStatus[$i]['status']	=	$sPowercenter[$aDevice[0]];
										$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'P');
										
										if($arrStatus[$i]['name'] == '')
												$arrStatus[$i]['name'] = 'PowerCenter'.$aDevice[0];
									}
									if($aDevice[1] == 'V')
									{
										if($sValves[$aDevice[0]] != '' && $sValves[$aDevice[0]] != '.')
										{
											$arrStatus[$i]['status']	=	$sValves[$aDevice[0]];
											$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'V');
											if($arrStatus[$i]['name'] == '')
												$arrStatus[$i]['name'] = 'Valve '.$aDevice[0];
										}
									}
									if($aDevice[1] == 'PS')
									{
										
										$arrStatus[$i]['status']	=	$sPump[$aDevice[0]];
										$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'P');
										if($arrStatus[$i]['name'] == '')
											$arrStatus[$i]['name'] = 'Pump '.$aDevice[0];
									}
									if($aDevice[1] == 'B')
									{
										
										$arrStatus[$i]['status']	=	0;
										$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($aDevice[0]);
										if(!empty($aBlowerDetails))
										{
											foreach($aBlowerDetails as $aBlower)
											{
												$sBlowerStatus	=	'';
												$sRelayDetails  =   unserialize($aBlower->light_relay_number);
												
												//Blower Operated Type and Relay
												$sRelayType     =   $sRelayDetails['sRelayType'];
												$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
												
												if($sRelayType == '24')
												{
													$sBlowerStatus   =   $sRelays[$sRelayNumber];
												}
												if($sRelayType == '12')
												{
													$sBlowerStatus   =   $sPowercenter[$sRelayNumber];
												}
											
												$arrStatus[$i]['status'] = $sBlowerStatus;
											}
										}
										$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'B');
										if($arrStatus[$i]['name'] == '')
											$arrStatus[$i]['name'] = 'Blower '.$aDevice[0];
									}
									if($aDevice[1] == 'L')
									{
										
										$arrStatus[$i]['status']	=	0;
										$aLightDetails  =   $this->home_model->getLightDeviceDetails($aDevice[0]);
										if(!empty($aLightDetails))
										{
											foreach($aLightDetails as $aLight)
											{
												$sLightStatus	=	'';
												$sRelayDetails  =   unserialize($aLight->light_relay_number);
												
												//Blower Operated Type and Relay
												$sRelayType     =   $sRelayDetails['sRelayType'];
												$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
												
												if($sRelayType == '24')
												{
													$sLightStatus   =   $sRelays[$sRelayNumber];
												}
												if($sRelayType == '12')
												{
													$sLightStatus   =   $sPowercenter[$sRelayNumber];
												}
											
												$arrStatus[$i]['status'] = $sLightStatus;
											}
										}
										$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'L');
										if($arrStatus[$i]['name'] == '')
											$arrStatus[$i]['name'] = 'Light '.$aDevice[0];
									}
									if($aDevice[1] == 'H')
									{
										$arrStatus[$i]['status']	=	0;
										$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($aDevice[0]);
										if(!empty($aHeaterDetails))
										{
											foreach($aHeaterDetails as $aHeater)
											{
												$sHeaterStatus	=	'';
												$sRelayDetails  =   unserialize($aHeater->light_relay_number);
												
												//Blower Operated Type and Relay
												$sRelayType     =   $sRelayDetails['sRelayType'];
												$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
												
												if($sRelayType == '24')
												{
													$sHeaterStatus   =   $sRelays[$sRelayNumber];
												}
												if($sRelayType == '12')
												{
													$sHeaterStatus   =   $sPowercenter[$sRelayNumber];
												}
											
												$arrStatus[$i]['status'] = $sHeaterStatus;
											}
										}
										
										$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'H');
										if($arrStatus[$i]['name'] == '')
											$arrStatus[$i]['name'] = 'Heater '.$aDevice[0];
									}
									
								}
							}
						}
					?>
					<!-- content to be placed inside <body>…</body> -->
					<div class="controls boxed" style="min-height:210px;">
						<div style="text-align:center;">
							<h3 class="profile-title"><strong style="color:#9CD70E;">Remote Switch</strong></h3>
							<div class='circle-container'>
								
								<a href="javascript:void(0);" class='deg225' >
								
									<div class="rowCheckbox switch" id="deviceInput0">
										<div class="custom-checkbox"><input type="checkbox" value="0|||<?php echo $arrStatus[0]['device'];?>" id="relay-0" name="relay-0" class="relayButton" hidefocus="true" style="outline: medium none;">
										<label <?php if($arrStatus[0]['status']){ echo 'class="checked"';}?> id="lableRelay-0" for="relay-0"></label>
										</div>
									</div>
								
								</a>
								<a class="deg225" style="margin-top: 15px; margin-left: -60px;"><span id="deviceInputName0"><?php echo $arrStatus[0]['name'];?></span></a>
								
								<a href="javascript:void(0);" class="deg0" style="margin-left:-30px;">
								
									<div class="rowCheckbox switch" id="deviceInput1">
										<div class="custom-checkbox"><input type="checkbox" value="1|||<?php echo $arrStatus[1]['device'];?>" id="relay-1" name="relay-1" class="relayButton" hidefocus="true" style="outline: medium none;">
											<label <?php if($arrStatus[1]['status']){ echo 'class="checked"';}?> id="lableRelay-1" for="relay-1"></label>
										</div>
									</div>
								
								</a>
								<a class="deg0" style="margin-top: 15px; margin-left: -70px;"><span id="deviceInputName1"><?php echo $arrStatus[1]['name'];?></span></a>
								
								<a href="javascript:void(0);" class='deg45' >
								
									<div class="rowCheckbox switch" id="deviceInput2">
										<div class="custom-checkbox"><input type="checkbox" value="2|||<?php echo $arrStatus[2]['device'];?>" id="relay-2" name="relay-2" class="relayButton" hidefocus="true" style="outline: medium none;">
											<label <?php if($arrStatus[2]['status']){ echo 'class="checked"';}?> id="lableRelay-2" for="relay-2"></label>
										</div>
									</div>
								
								</a>
								<a class="deg45" style="margin-top: 15px; margin-left: -60px;"><span id="deviceInputName2"><?php echo $arrStatus[2]['name'];?></span></a>
								
								<a href="javascript:void(0);" class='deg180'>
								
									<div class="rowCheckbox switch" id="deviceInput3">
										<div class="custom-checkbox"><input type="checkbox" value="3|||<?php echo $arrStatus[3]['device'];?>" id="relay-3" name="relay-3" class="relayButton" hidefocus="true" style="outline: medium none;">
											<label <?php if($arrStatus[3]['status']){ echo 'class="checked"';}?> id="lableRelay-3" for="relay-3"></label>
										</div>
									</div>
								
								</a>
								<a class="deg180" style="margin-top: 15px; margin-left: -60px;"><span id="deviceInputName3"><?php echo $arrStatus[3]['name'];?></span></a>
								
								
								
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="tab-pane fade in <?php if($Remote_Spa == '0') { echo 'active'; } ?>" id="24VAC">
				<!-- Price item -->
				<?php 
								
						$strRelayUrl	=	'onClick="location.href=\''.base_url('home/setting/R/').'\'"'; 	
						if(!empty($aModules))
						{
							if(!in_array(2,$aModules->ids)) 
							{
								$strRelayUrl = 'onClick="showRestrict();"'; 
							} 
						}
						  
						$strPowerUrl	=	'onClick="location.href=\''.base_url('home/setting/P/').'\'"'; 	
						if(!empty($aModules))
						{
							if(!in_array(3,$aModules->ids)) 
							{
								$strPowerUrl = 'onClick="showRestrict();"'; 
							} 
						}
					?>
				<div class="price-item style4">
					<div class="price-content clearfix">
						<div class="price-content-left">
							<div class="price-image">
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/rlb.jpg" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title">
							<p>24V AC Relays<span class="price-info" style="cursor:pointer; float:right;" <?php echo $strRelayUrl;?> >Configure</span></p>
							<a href="#"><span id="deviceTotalRelays" style="color:#1A315F;font-size:20px;" >Total : <?php echo $relay_count;?></span></a>&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;font-size:20px;" id="deviceActiveRelays">Active : <?php echo $activeCountRelay;?></span></h2>
							<div class="price clearfix">
								<strong style="font-size:34px;" id="deviceOnRelays">ON : <?php echo $OnCountRelay;?></strong><strong style="color:#1A315F; margin-left:10px;font-size:34px;" id="deviceOffRelays">OFF : <?php echo $OFFCountRelay;?></strong>
							</div>
							<div class="price-desc"></div>
							
							<h2 class="price-title"><p>12V DC Relays<span class="price-info" style="cursor:pointer; float:right;" <?php echo $strPowerUrl;?> >Configure</span></p><a href="#"><span style="color:#1A315F;font-size:20px;" id="deviceTotalPower">Total : <?php echo $power_count;?></span></a>&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;font-size:20px;" id="deviceActivePower">Active : <?php echo 8?></span></h2>
							
							<div class="price clearfix">
								<strong style="font-size:34px;" id="deviceOnPower">ON : <?php echo $OnCountPower;?></strong><strong style="color:#1A315F; margin-left:10px;font-size:34px;" id="deviceOffPower">OFF : <?php echo $OFFCountPower;?></strong>
							</div>
						</div>
					</div>
					
					<div class="price-bottom clearfix">
						
						<a href="javascript:void(0);" <?php echo $strRelayUrl;?> class="price-reserve" style="float:left;">Switch ON/OFF 24V AC Relays</a>&nbsp;&nbsp;
						<a href="javascript:void(0);" <?php echo $strPowerUrl;?> class="price-reserve">Switch ON/OFF 12V DC Relays</a>
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
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/left_valve.png" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title"><a href="#"><span style="color:#1A315F" id="deviceTotalValve">Total : <?php echo $valve_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;" id="deviceActiveValve">Active : <?php echo $activeCountValve?></span></h2>
							<div class="price-desc">
							
							</div>
							<div class="price clearfix">
								<strong id="deviceOnValve">ON : <?php echo $OnCountValve?></strong>
								<strong style="color:#1A315F; margin-left:10px;" id="deviceOffValve">OFF : <?php echo $OFFCountValve?></strong>
							</div>
						</div>
					</div>
					<?php 
								
							  $strValveUrl	=	'onClick="location.href=\''.base_url('home/setting/V/').'\'"'; 	
							  if(!empty($aModules))
							  {
								if(!in_array(8,$aModules->ids)) 
								{
									$strValveUrl = 'onClick="showRestrict();"'; 
								} 
							  }
							  
						?>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" <?php echo $strValveUrl;?>>Configure</span>
						<a <?php echo $strValveUrl;?> href="javascript:void(0);" class="price-reserve">Switch ON/OFF</a>
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
								<img src="<?php echo HTTP_IMAGES_PATH;?>temp/motor_image.png" alt="" />
							</div>
						</div>
						<div class="price-content-right">
							<h2 class="price-title"><a href="#"><span style="color:#1A315F" id="deviceTotalPump">Total : <?php echo $pump_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;" id="deviceActivePump">Active : <?php echo $activeCountPump?></span></h2>
							<div class="price-desc">
							
							</div>
							<div class="price clearfix">
								<strong id="deviceOnPump">ON : <?php echo $OnCountPump?></strong>
								<strong style="color:#1A315F; margin-left:10px;" id="deviceOffPump">OFF : <?php echo $OFFCountPump?></strong>
							</div>
						</div>
					</div>
					<?php 
								
							  $strPumpUrl	=	'onClick="location.href=\''.base_url('home/setting/PS/').'\'"'; 	
							  if(!empty($aModules))
							  {
								if(!in_array(9,$aModules->ids)) 
								{
									$strPumpUrl = 'onClick="showRestrict();"'; 
								} 
							  }
							  
						?>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" <?php echo $strPumpUrl;?>>Configure</span>
						<a <?php echo $strPumpUrl;?> href="javascript:void(0);" class="price-reserve">Switch ON/OFF</a>
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
							<h2 class="price-title"><a href="#"><span style="color:#1A315F" id="deviceTotalTemp">Total : <?php echo $temprature_count;?></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><span style="color:#9CD70E;" id="deviceActiveTemp">Active : <?php echo $activeCountTemperature;?></span></h2>
						</div>
					</div>
					<?php 
								
							  $strTempUrl	=	'onClick="location.href=\''.base_url('home/setting/T/').'\'"'; 	
							  if(!empty($aModules))
							  {
								if(!in_array(10,$aModules->ids)) 
								{
									$strTempUrl = 'onClick="showRestrict();"'; 
								} 
							  }
							  
						?>
					<div class="price-bottom clearfix">
						<span class="price-info" style="cursor:pointer;" <?php echo $strTempUrl;?>>Configure</span>
						<a <?php echo $strTempUrl;?> href="javascript:void(0);" class="price-reserve">Temperature Sensors</a>
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
			<?php 
								
				  $strModeUrl	= 'href="'.base_url('analog/changeModeManual').'"'; 
				  $strSpaUrl	= 'href="'.base_url('home/SpaDevice/').'"';
				  $strPoolUrl	= 'href="'.base_url('home/PoolDevice/').'"'; 
				  $strLightUrl	= 'href="'.base_url('analog/showLight').'"'	;			  
				  
				  $strUserUrl	= 'href="'.base_url('dashboard/users/').'"'; 
				  $strModuleUrl	= 'href="'.base_url('dashboard/module/').'"';
				  $strInputUrl	= 'href="'.base_url('analog/').'"';	

				  if(!empty($aModules))
				  {
					if(!in_array(4,$aModules->ids)) 
					{
						$strModeUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
					} 
					if(!in_array(6,$aModules->ids)) 
					{
						$strSpaUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
					}
					if(!in_array(7,$aModules->ids)) 
					{
						$strPoolUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
					}
					if(!in_array(5,$aModules->ids)) 
					{
						$strLightUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
					}
					if(!in_array(11,$aModules->ids)) 
					{
						$strInputUrl = 'href="javascript:void(0);" onClick="showRestrict();"'; 
					}
				  }
				  
			?>
			<div class="inner">
				<div class="ribbon" style="width:90%"><a <?php echo $strModeUrl;?>><span style="color:#000;">Modes</span></a></div>
			</div>
			<div class="inner">
				<div class="ribbon ribbon-green" style="width:90%"><a <?php echo $strLightUrl;?>><span>Lights</span></a></div>
			</div>
			<div class="inner">
				<div class="ribbon" style="width:90%"><a <?php echo $strSpaUrl;?>><span style="color:#000;">Spa Devices</span></a></div>
			</div>
			<div class="inner">
				<div class="ribbon ribbon-green" style="width:90%"><a <?php echo $strPoolUrl;?>><span>Pool Devices</span></a></div>
			</div>
			<div class="inner">
				<div class="ribbon" style="width:90%"><a <?php echo $strInputUrl;?>><span>Input Device</span></a></div>
			</div>
		</div>
		<!--/ Ribbons -->
	</div>
</div>
<!--/ row Level 2 -->

<!-- row Level 3 -->
<div class="row">
<?php //$Remote_Spa = '0'; ?><?php //if($Remote_Spa == '0'){ echo '4';} else {echo '3';}?>
<div class="col-sm-6">
	<!-- Profile -->
		<?php 
				
			  $strTempUrl	=	'onClick="location.href=\''.base_url('home/setting/T/').'\'"'; 	
			  if(!empty($aModules))
			  {
				if(!in_array(10,$aModules->ids)) 
				{
					$strTempUrl = 'onClick="showRestrict();"'; 
				} 
			  }
			  
		?>
		<div class="controls boxed blue-line" style="min-height:210px;">
			<div class="inner">
				<h3 class="profile-title"><strong style="color:#9CD70E;">Temperature</strong></h3>
				<span class="price-title" style="color:#C9376E;"><strong><?php echo $sTemperature;?></strong></span>
				<div class="price-bottom clearfix" style="margin-top:25px;"><a href="#" hidefocus="true" style="outline: medium none;">
						</a><a class="price-reserve" <?php echo $strTempUrl;?> href="javascript:void(0);" hidefocus="true" style="outline: medium none; font-size:13px;">Temperature Sensors</a>
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
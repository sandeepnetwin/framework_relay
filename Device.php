<?php
$this->load->model('home_model');
$this->load->model('analog_model');

$sAccess 		= '';
$sModule	    = '';
$sDeviceFullName = '';
if($sDevice == 'R')
{
  $sDeviceFullName 	= '24V AC Relay';
  $sModule	    	= 2;
}
if($sDevice == 'P')
{
  $sDeviceFullName = '12V DC Power Center Relay';
  $sModule	       = 3;
}
if($sDevice == 'V')
{
  $sDeviceFullName = 'Valve';
  $sModule	       = 8;
}
if($sDevice == 'PS')
{
  $sDeviceFullName = 'Pump Sequencer';
  $sModule	       = 9;
}
if($sDevice == 'T')
{
  $sDeviceFullName = 'Temperature sensor';
  $sModule	       = 10;
}

  $sAccessKey	= 'access_'.$sModule;
			  
  if(!empty($aModules))
  {
	  if(in_array($sModule,$aModules->ids))
	  {
		 $sAccess 		= $aModules->$sAccessKey;
	  }
	  else if(!in_array($sModule,$aModules->ids)) 
	  {
		$sAccess 		= '0'; 
	  }
  }
  
  if($sAccess == '')
	$sAccess = '2' ; 
  
  if($sAccess == '0') {redirect(site_url('home/'));}
?>
<style>
.fancybox-inner 
{
	height:40px !important;
}
</style>
<script type="text/javascript" src="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.js?v=2.1.5';?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.css?v=2.1.5';?>" media="screen" />
<?php if($sDevice != 'V') { ?>
<script type="text/javascript">
var $a = $.noConflict();
$a(document).ready(function() {
	$a('.fancybox').fancybox({'closeBtn' : false,'helpers': {'overlay' : {'closeClick': false}}
	});
});
</script>
<?php } ?>	
<link href="<?php echo HTTP_ASSETS_PATH.'progressbar/css/static.css';?>" rel="stylesheet"/>
<script src="<?php echo HTTP_ASSETS_PATH.'progressbar/js/static.min.js';?>"></script>
<script src="<?php echo HTTP_ASSETS_PATH.'progressbar/dist/js/jquery.progresstimer.js';?>"></script>
<script>
jQuery(document).ready(function($) {
	
	$(".displayMore").hide();
	$(".more").click(function() {
		var txt	=	$(this).html();
		if(txt == 'More +')
			txt = 'More -';
		else
			txt = 'More +';
		
		$(".displayMore").toggle('slow',function() {	
			$(".more").html(txt);
		});
		
	});
	<?php if($sAccess == 2) { ?>
	$("#addMoreValve").click(function() {
		$("#moreValve").slideToggle('slow');
	});
	
	$(".relayButton").click(function()
	{
		//$a('.fancybox').fancybox();
		<?php if($iActiveMode == '2') { ?>
		$(".loading-progress").show();
		var progress = $(".loading-progress").progressTimer({
			timeLimit: 10,
			onFinish: function () {
			  //$(".loading-progress").hide();
			  parent.$a.fancybox.close();
			}
		});
		
		$a("#checkLink").trigger('click');
		
		
		//$a('.fancybox-inner').height(450);
		//$a.fancybox.reposition();
		
		var relayNumber = $(this).val();
		var status		= '';
		if($("#lableRelay-"+relayNumber).hasClass('checked'))
		{	
			status	=	0;
		}
		else
		{
			status = 1;
		}
		
		
		 $.ajax({
			type: "POST",
			url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
			data: {sName:relayNumber,sStatus:status,sDevice:'R'},
			success: function(data) {
				if($("#lableRelay-"+relayNumber).hasClass('checked'))
				{	
					$("#lableRelay-"+relayNumber).removeClass('checked');
				}
				else
				{
					$("#lableRelay-"+relayNumber).addClass('checked');
				}
				
			}
		}).error(function(){
        progress.progressTimer('error', {
            errorText:'ERROR!',
            onFinish:function(){
                alert('There was an error processing your information!');
            }
        });
		}).done(function(){
			progress.progressTimer('complete');
		});
		 <?php } else {  ?>
		  alert('You can perform this operation in manual mode only.');
		 <?php } ?> 
	});
	
	
	$(".relayRadio").click(function()
	{
		var chkVal 		= $(this).val();
		var relayNumber	= $(this).attr('name').split("_");	
		
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/saveDeviceMainType');?>", 
			data: {sDeviceID:relayNumber[0],sDevice:'R',sType:chkVal},
			success: function(data) {
				if(chkVal == 0)
				{
					$("#relay_other_"+relayNumber[0]).addClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 1)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).addClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 2)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).addClass('checked');
				}
				
			}

		 });
	});
	
	
	$(".powerButton").click(function()
	{
		<?php if($iActiveMode == '2') { ?>
		 $(".loading-progress").show();
		var progress = $(".loading-progress").progressTimer({
			timeLimit: 10,
			onFinish: function () {
			  //$(".loading-progress").hide();
			  parent.$a.fancybox.close();
			}
		});
		
		$a("#checkLink").trigger('click');
		
		
		var relayNumber = $(this).val();
		var status		= '';
		if($("#lablePower-"+relayNumber).hasClass('checked'))
		{	
			status	=	0;
		}
		else
		{
			status = 1;
		}
		
		
		 $.ajax({
			type: "POST",
			url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
			data: {sName:relayNumber,sStatus:status,sDevice:'P'},
			success: function(data) {
				if($("#lablePower-"+relayNumber).hasClass('checked'))
				{	
					$("#lablePower-"+relayNumber).removeClass('checked');
				}
				else
				{
					$("#lablePower-"+relayNumber).addClass('checked');
				}
				
			}
		}).error(function(){
        progress.progressTimer('error', {
            errorText:'ERROR!',
            onFinish:function(){
                alert('There was an error processing your information!');
            }
        });
		}).done(function(){
			progress.progressTimer('complete');
		});
		 <?php } else {  ?>
		  alert('You can perform this operation in manual mode only.');
		 <?php } ?> 
	});
	
	
	$(".powerRadio").click(function()
	{
		var chkVal 		= $(this).val();
		var relayNumber	= $(this).attr('name').split("_");	
		
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/saveDeviceMainType');?>", 
			data: {sDeviceID:relayNumber[0],sDevice:'P',sType:chkVal},
			success: function(data) {
				if(chkVal == 0)
				{
					$("#relay_other_"+relayNumber[0]).addClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 1)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).addClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 2)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).addClass('checked');
				}
				
			}

		 });
	});
	
	$(".valveRadio").click(function()
	{
		var chkVal 		= $(this).val();
		var relayNumber	= $(this).attr('name').split("_");	
		
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/saveDeviceMainType');?>", 
			data: {sDeviceID:relayNumber[0],sDevice:'V',sType:chkVal},
			success: function(data) {
				if(chkVal == 0)
				{
					$("#relay_other_"+relayNumber[0]).addClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 1)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).addClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 2)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).addClass('checked');
				}
				
			}

		 });
	});
	
	$(".pumpRadio").click(function()
	{
		var chkVal 		= $(this).val();
		var relayNumber	= $(this).attr('name').split("_");	
		
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/saveDeviceMainType');?>", 
			data: {sDeviceID:relayNumber[0],sDevice:'PS',sType:chkVal},
			success: function(data) {
				if(chkVal == 0)
				{
					$("#relay_other_"+relayNumber[0]).addClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 1)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).addClass('checked');
					$("#relay_pool_"+relayNumber[0]).removeClass('checked');
				}
				else if(chkVal == 2)
				{
					$("#relay_other_"+relayNumber[0]).removeClass('checked');
					$("#relay_spa_"+relayNumber[0]).removeClass('checked');
					$("#relay_pool_"+relayNumber[0]).addClass('checked');
				}
				
			}

		 });
	});
	
	
	$(".pumpsButton").click(function()
	{
		<?php if($iActiveMode == '2') { ?>
		$(".loading-progress").show();
		var progress = $(".loading-progress").progressTimer({
			timeLimit: 10,
			onFinish: function () {
			  //$(".loading-progress").hide();
				setTimeout(function(){location.reload();parent.$a.fancybox.close();},1000);
				
			}
		});
		
		$a("#checkLink").trigger('click');
		
		var relayNumber = $(this).val();
		var status		= '';
		if($("#lablePump-"+relayNumber).hasClass('checked'))
		{	
			status	=	0;
		}
		else
		{
			status = 1;
		}
		
		<?php //if($iActiveMode == '2') { ?>
		 $.ajax({
			type: "POST",
			url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
			data: {sName:relayNumber,sStatus:status,sDevice:'PS'},
			success: function(data) {
				
				if($("#lablePump-"+relayNumber).hasClass('checked'))
				{	
					$("#lablePump-"+relayNumber).removeClass('checked');
				}
				else
				{
					$("#lablePump-"+relayNumber).addClass('checked');
				}
				
				
				
			}
		}).error(function(){
        progress.progressTimer('error', {
            errorText:'ERROR!',
            onFinish:function(){
                alert('There was an error processing your information!');
            }
        });
		}).done(function(){
			progress.progressTimer('complete');
		});
		 <?php } else {  ?>
		  alert('You can perform this operation in manual mode only.');
		  
		 <?php } ?> 
	});
	
	$(".pumpSpeedSet").click(function() {
		$(".loading-progress").show();
		var progress = $(".loading-progress").progressTimer({
			timeLimit: 10,
			onFinish: function () {
			  //$(".loading-progress").hide();
				setTimeout(function(){location.reload();parent.$a.fancybox.close();},1000);
			}
		});
		
		$a("#checkLink").trigger('click');
		
		var speed 		= $(this).val();
		var pumpName	= $(this).attr('name');
		
		var PumpID		=	pumpName.split("_");
		
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/updatePumpSpeed/');?>", 
			data: {PumpID:PumpID[1],speed:speed},
			success: function(data) 
			{
				
			}
		}).error(function(){
        progress.progressTimer('error', {
            errorText:'ERROR!',
            onFinish:function(){
                alert('There was an error processing your information!');
            }
        });
		}).done(function(){
			progress.progressTimer('complete');
		});
	});
	<?php } ?>
	
});

function removePump(iPumpNumber)
{
	var cnf	=	confirm("Are you sure, you want to remove Pump?");
	if(cnf)
	{
		$("#loadingImgPumpRemove_"+iPumpNumber).show();
		$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/removePump');?>", 
				data: {iPumpNumber:iPumpNumber},
				success: function(resp) {
					$("#loadingImgPumpRemove_"+iPumpNumber).hide();
					alert('Pump details removed successfully!');
					location.reload(); 
				}

			});
	}
}

function RemoveValveRelays(iValaveNumber)
	{
		var cnf	=	confirm("Are you sure, you want to remove relays?");
		if(cnf)
		{
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/removeValveRelays');?>", 
				data: {iValaveNumber:iValaveNumber},
				success: function(resp) {
					location.reload(); 
				}

			 });
		}
	}
	
function saveValveCount()
{
	var ValveCnt	=	$("#moreValveCnt").val();
	if(ValveCnt == '')
	{
		alert('Please enter Valve number!');
		return false;
	}
	else if(isNaN(ValveCnt))
	{
		alert('Please enter valid Valve number!');
		return false;
	}
	else
	{
		$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/addMoreValve/');?>", 
				data: {ValveCnt:ValveCnt},
				success: function(resp) {
					//location.reload(); 
					if(resp == 'error')
					{
						alert('Total valve must be equal to 8 or less than 8 in count!');
						return false;
					}
					else if(resp == 'success')
					{
						alert('Valve count updated successfully!');
						location.reload(); 
					}
				}

			 });
	}
}	

function removeValve()
{
	var cnf	=	confirm("Are you sure, you want to remove valve?");
	if(cnf)
	{
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/removeValve/');?>", 
			data: {},
			success: function(resp) {
				location.reload(); 
			}

		 });
	}
}
</script>
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb" style="float:left">
		  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
		  <li class="active"><?php echo $sDeviceFullName;?></li>
		</ol>
		<p>
		<a class="fancybox" id="checkLink" href="#inline1" style="display:none;">&nbsp;</a>
		<div id="inline1" style="width:250px;height:auto; display:none;"><div class="loading-progress"></div></div>
		</p>
	</div>
</div>	


		
<!-- START : 24V AC RELAY -->
<?php if($sDevice == 'R') 
	  { ?>
	<?php if($sAccess == '1' || $sAccess == '2') 
		  { ?>
			<div class="row">
				<div class="col-sm-4">
					<div class="widget-container widget-stats boxed green-line">
					<div class="widget-title">
						<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
						<h3>ON/OFF</h3>
					</div>
					<div class="stats-content clearfix">
						<div class="stats-content-right" style="width:96% !important; margin-left:5px; margin-right:5px; margin-top:10px; float:none;">
							<?php
								for ($i=0;$i < $relay_count; $i++)
								{
									$iRelayVal = $sRelays[$i];
									
									if($iRelayVal != '' && $iRelayVal !='.') 
									{
										$strChecked	=	'';
										if($iRelayVal == '1')
											$strChecked	=	'class="checked"';
										
										$sRelayNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										$strRelayName = 'Relay '.$i;
										if($sRelayNameDb != '')
											$strRelayName .= ' ('.$sRelayNameDb.')';
							?>
									    <div class="rowCheckbox switch">
											<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="relay-<?php echo $i?>" name="relay-<?php echo $i?>" class="relayButton" hidefocus="true" style="outline: medium none;">
												<label <?php echo $strChecked;?>  id="lableRelay-<?php echo $i?>" for="relay-<?php echo $i?>"><span style="color:#C9376E; float:right;"><?php echo $strRelayName;?></span></label>
											</div>
										</div>
							<?php 	}		
								 }
							?> 
						</div>
					</div>
					</div>
				</div>
				  
				<div class="col-sm-8">
						<!-- Statistics -->
						<div class="widget-container widget-stats boxed green-line">
							<div class="widget-title">
								<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
								<h3>24V AC Relay Settings</h3>
							</div>
							<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
							
							  <table class="table table-hover">
								<thead>
								  <tr>
									<th class="header" style="width:25%">Relay</th>
									<th class="header"  style="width:25%">Relay Type</th>
									<th class="header"  style="width:50%">Action</th>
								  </tr>
								</thead>
								<tbody>
								<?php			
								//START : Relay Device 
								$j=0;
								//START : First Show all Relays assigned to Valves.
								for ($i=0;$i < $relay_count; $i++)
								{
									$iRelayVal = $sRelays[$i];
									if($iRelayVal == '' || $iRelayVal =='.') 
									{
										$sRelayNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sRelayNameDb == '')
										  $sRelayNameDb = 'Add Name';
								?>
									
										<tr <?php if($j>=1){ echo 'class="displayMore"';}?>>
										<td>Relay <?php echo $i;?><br />(<a href="<?php if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';} ?>"><?php echo $sRelayNameDb;?></a>)</td>
										<td> - </td>
										<td>
										<strong style="color:#FF0000">Output is Assigned to Valve.</strong>
										</td>
									</tr>	
									<?php 
										$j++;
									}
								}
								if($j > 0)
								{
									echo '<tr>
											<th class="header more" colspan="7" style="text-align: center; color:#428BCA; cursor:pointer;">More +</th>
										 </tr>';
								}
								//END : First Show all Relays assigned to Valves.
								for ($i=0;$i < $relay_count; $i++)
								{
									$iRelayVal = $sRelays[$i];
									if($iRelayVal != '' && $iRelayVal !='.') 
									{
										
										$iRelayNewValSb = 1;
										if($iRelayVal == 1)
										{
										  $iRelayNewValSb = 0;
										}
										$sRelayVal = false;
										if($iRelayVal)
										  $sRelayVal = true;
										//$sRelayNameDb = get_device_name(1, $i);

										$sRelayNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sRelayNameDb == '')
										  $sRelayNameDb = 'Add Name';
										
										$sDeviceTime =  $this->home_model->getDeviceTime($i,$sDevice);
										if($sDeviceTime == '')
										  $sDeviceTime = 'Add Time';
										else
										  $sDeviceTime .= ' Minute';
									  
										$iRelayProgramCount = $this->home_model->getProgramCount($i,$sDevice);
										$iPower	 = $this->home_model->getDevicePower($i,$sDevice);
										$sMainType =	$this->home_model->getDeviceMainType($i,$sDevice);
									?>
										<tr>
										<td>Relay <?php echo $i;?><br />(<a href="<?php if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';} ?>"><?php echo $sRelayNameDb;?></a>)</td>
										<td>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label></div></div>
										</td>
										<td>
										<div>
										<a class="btn btn-small" href="<?php if($sAccess == '2') { echo site_url('home/setPrograms/'.base64_encode($i).'/');} else { echo 'javascript:void(0);';}?>"><span>Programs</span></a>
										<a class="btn btn-small btn-red" href="<?php if($sAccess == '2') { echo site_url('home/setPrograms/'.base64_encode($i).'/');} else { echo 'javascript:void(0);';}?>"><span><?php echo $iRelayProgramCount;?><?php if($iRelayProgramCount == 1 || $iRelayProgramCount == 0){ echo ' Program';}else{ echo ' Programs';}?></span></a>
										<a class="btn btn-green btn-small" href="<?php if($sAccess == '2') {echo site_url('home/addTime/'.base64_encode($i).'/'.  base64_encode($sDevice));} else { echo 'javascript:void(0);';}?>"><span><?php echo $sDeviceTime;?></span></a>
										</div>
										</td>
										</tr>
								<?php  } ?>
								<?php  } ?>	
								</tbody>
								</table>
								</div>
							</div>
						</div>
						<!--/ Statistics -->
					</div>
			</div><!-- /.row -->
	<?php } ?>
<?php } //Relay Device End ?>	
<!-- END : 24V AC RELAY -->


<!-- START : 12V DC RELAY -->

<?php if($sDevice == 'P') 
	  { ?>
	<?php if($sAccess == '1' || $sAccess == '2') 
		  { ?>
			
			<div class="row">
				<div class="col-sm-4">
					<div class="widget-container widget-stats boxed green-line">
					<div class="widget-title">
						<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
						<h3>ON/OFF</h3>
					</div>
					<div class="stats-content clearfix">
						<div class="stats-content-right" style="width:96% !important; margin-left:5px; margin-right:5px; margin-top:10px; float:none;">
						<?php
								for ($i=0;$i < $power_count; $i++)
								{
									$iRelayVal = $sPowercenter[$i];
									
									if($iRelayVal != '' && $iRelayVal !='.') 
									{
										$strChecked	=	'';
										if($iRelayVal == '1')
											$strChecked	=	'class="checked"';
										
										$sRelayNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										$strRelayName = 'PowerCenter '.$i;
										if($sRelayNameDb != '')
											$strRelayName .= ' ('.$sRelayNameDb.')';
							?>
									    <div class="rowCheckbox switch">
											<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="power-<?php echo $i?>" name="power-<?php echo $i?>" class="powerButton" hidefocus="true" style="outline: medium none;">
												<label <?php echo $strChecked;?>  id="lablePower-<?php echo $i?>" for="power-<?php echo $i?>"><span style="color:#C9376E; float:right;"><?php echo $strRelayName;?></span></label>
											</div>
										</div>
							<?php 	}		
								 }
							?> 
					</div>
					</div>
					</div>
				</div>
				<div class="col-sm-8">
						<!-- Statistics -->
						<div class="widget-container widget-stats boxed green-line">
							<div class="widget-title">
								<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
								<h3>12V DC Relay Settings</h3>
							</div>
							<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
							
							  <table class="table table-hover">
								<thead>
								  <tr>
									<th class="header" style="width:25%">Relay</th>
									<th class="header"  style="width:25%">Relay Name</th>
									<th class="header"  style="width:50%">Action</th>
								  </tr>
								</thead>
								<tbody>
								<?php			
								for ($i=0;$i < $power_count; $i++)
								{
									$iRelayVal = $sPowercenter[$i];
									if($iRelayVal != '' && $iRelayVal !='.') 
									{
										$iRelayNewValSb = 1;
										if($iRelayVal == 1)
										{
										  $iRelayNewValSb = 0;
										}
										$sRelayVal = false;
										if($iRelayVal)
										  $sRelayVal = true;
										
										$sPowerCenterNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sPowerCenterNameDb == '')
										  $sPowerCenterNameDb = 'Add Name';
										
										$sMainType =	$this->home_model->getDeviceMainType($i,$sDevice);
									?>
										<tr>
										<td>PowerCenter <?php echo $i;?></td>
										<td><a href="<?php if($sAccess == '1'){echo 'javascript:void(0);';} else if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');}?>" ><?php echo $sPowerCenterNameDb;?></a>
										</td>
										<td>
											<div class="rowRadio"><div class="custom-radio"><input class="powerRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="powerRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="powerRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label></div></div>
										</td>
										</tr>
								<?php  } ?>
								<?php  } ?>	
								</tbody>
								</table>
								</div>
							</div>
						</div>
						<!--/ Statistics -->
					</div>
			</div>	
		
	<?php } ?>
<?php } ?>	
<!-- END : 12V DC RELAY -->


<!-- START : VALVES -->
<?php 
	if($sDevice == 'V') // Valve Start 
	{ 
		$iValveNumber = $extra['ValveNumber']; ?>
	 <?php if($sAccess == '1' || $sAccess == '2') 
		   { ?>
			<link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
			<script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
			<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
			<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>
			<script>
			var iActiveMode = '<?php echo $iActiveMode;?>';
			var sAccess 	= '<?php echo $sAccess;?>';
			</script>
			<div class="row">
				<div class="col-sm-4">
					<div class="widget-container widget-stats boxed green-line">
					<div class="widget-title">
						<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
						<h3>ON/OFF</h3>
					</div>
					<div class="stats-content clearfix">
						<div class="stats-content-right" style="width:96% !important; margin-left:5px; margin-right:5px; margin-top:10px; float:none;">
						<?php
							if( $iValveNumber == 0 || $iValveNumber == '' )
							{ ?>
								
								<tr>
									<td>
										<span style="color:red">Please add number of Vavles in the <a href="<?php echo base_url('home/setting/');?>">Settings</a> Page!</span>
									</td>
								</tr>
								
							<?php 
							}
							else
							{
								//START : Valve Device 
								$arrValve	=	array(0,1,2,3,4,5,6,7);
								$j=0;
								$remainigCount	=	0;
								$chkValve		=	0;
								
								if(!empty($ValveRelays))
								{
									foreach($ValveRelays as $valve)
									{
										$i = $valve->device_number;
										$j = $i *2;
										
										unset($arrValve[$i]);
										
										$iValvesVal = $sValves[$i];
										$iValvesNewValSb1 = 1;
										$iValvesNewValSb2 = 2 ;
										if($iValvesVal == 1)
										{
										  $iValvesNewValSb1 = 0;
										}
										if($iValvesVal == 2)
										{
										  $iValvesNewValSb2 = 1;
										}
										$sValvesVal1 = false;
										$sValvesVal2 = false;
										if($iValvesVal == 1)
										  $sValvesVal1 = true;
										if($iValvesVal == 2)
										  $sValvesVal2 = true;
										//$sValvesNameDb = get_device_name(3, $i);

										$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sValvesNameDb == '')
										  $sValvesNameDb = 'Add Name';
									  
										$aPositionName   =  $this->home_model->getPositionName($i,$sDevice);
										$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice));
										$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice);
										
										if($iValvesVal == '.' || $iValvesVal == '')
											continue;
										
										if($iValvesVal != '' && $iValvesVal != '.' && !empty($aRelayNumber)) 
										{
									?>
										<div class="row">
										<div class="col-sm-12">
									    <div class="span1 valve-<?php echo $i?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php if($aPositionName[0] == ''){ echo 'Spa';} else { echo $aPositionName[0];} ?></div>
										<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
										<select id='switch-me-<?php echo $i;?>'>
										<option value='1' <?php if($iValvesVal == '1') { echo 'selected="selected"';} ?>>Spa</option>
										<option value='0' <?php if($iValvesVal == '0' || $iValvesVal == '') { echo 'selected="selected"';} ?>></option>
										<option value='2' <?php if($iValvesVal == '2') { echo 'selected="selected"';} ?>>Pool</option>
										</select>
										<div class="valve-<?php echo $i?>" value="0" id="off-<?php echo $i;?>" style="color: red;font-weight: bold;width: 0; margin-left: 30px; cursor: pointer;">
											OFF
	</div>                              </div>
										<div class="span1 valve-<?php echo $i?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php if($aPositionName[1] == ''){ echo 'Pool';} else { echo $aPositionName[1];} ?></div>
										<div style="float: right;margin-right: 10px;margin-top: 10px;"><strong><span style="color:#C9376E;"><?php echo 'Valve '.$i; ?></span></strong></div>
										<script type="text/javascript">
										  $(function()
										  {
												var bgColor = '#E8E8E8';
												<?php if($iValvesVal == '1' || $iValvesVal == '2') { ?>
														bgColor = '#45A31F';
												<?php } else { ?>
														bgColor = '#E8E8E8';
												<?php } ?>
												
												$('#switch-me-<?php echo $i;?>').switchy();
												
												$('.valve-<?php echo $i?>').on('click', function(event){
													//event.preventDefault();
													//return false;
													$('#switch-me-<?php echo $i;?>').val($(this).attr('value')).change();
												});
												
												$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
														backgroundColor: bgColor
													});
												
												$('#switch-me-<?php echo $i;?>').on('change', function(event)
												{
													if(sAccess == 2)
													{
														if(iActiveMode != 2)
														{
															var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
															if(bConfirm)
															{
																$.ajax({
																	type: "POST",
																	url: "<?php echo site_url('analog/changeMode');?>", 
																	data: {iMode:'2'},
																	success: function(data) {
																	}
																});
																//event.preventDefault();
																//return false;
																// Animate Switchy Bar background color
																var bgColor = '#E8E8E8';

																if ($(this).val() == '1' || $(this).val() == '2')
																{
																	bgColor = '#45A31F';
																} 
																$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
																	backgroundColor: bgColor
																});
															
																
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
																$.ajax({
																	type: "POST",
																	url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																	data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																	success: function(data) {
																	//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																	location.reload();
																	}

																});
															}
														}
														else											
														{
															//event.preventDefault();
															//return false;
															// Animate Switchy Bar background color
															var bgColor = '#E8E8E8';

															if ($(this).val() == '1' || $(this).val() == '2')
															{
																bgColor = '#45A31F';
															} 
															$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
																backgroundColor: bgColor
															});
														
															
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																success: function(data) {
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																}

															});
														}
													}
												});
											});
									   </script>
									  </div>
									</div>
									<div style="height:10px;">&nbsp;</div>
							<?php 	}
									else
								    {
										echo '<div class="row"><div class="col-sm-12"><div class="tagcloud clearfix"><span style="color:#FF0000;">Relay Not Assigned</span><div style="float: right;margin-right: 10px;color:#C9376E"><strong>Valve '.$i.'</strong></div></div></div></div><div style="height:10px;">&nbsp;</div>';
								    }
									
										$chkValve++;
									}		
								}
								$remainigCount	=	$iValveNumber - $chkValve;
								//for ($i=0;$i < $valve_count; $i++)	
								//for ($i=0;$i < $remainigCount ; $i++)
								foreach($arrValve as $i)	
								{
									if($remainigCount == 0)	
										break;
									
									$remainigCount--;
									
									$j = $i * 2;
									
									$iValvesVal = $sValves[$i];
									$iValvesNewValSb1 = 1;
									$iValvesNewValSb2 = 2 ;
									if($iValvesVal == 1)
									{
									  $iValvesNewValSb1 = 0;
									}
									if($iValvesVal == 2)
									{
									  $iValvesNewValSb2 = 1;
									}
									$sValvesVal1 = false;
									$sValvesVal2 = false;
									if($iValvesVal == 1)
									  $sValvesVal1 = true;
									if($iValvesVal == 2)
									  $sValvesVal2 = true;
									//$sValvesNameDb = get_device_name(3, $i);

									$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice);
									
								  
									$aPositionName   =  $this->home_model->getPositionName($i,$sDevice);
									$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice));
									$iPower	 	     =  $this->home_model->getDevicePower($i,$sDevice);
									$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice);
									
								if($iValvesVal != '' && $iValvesVal != '.' && !empty($aRelayNumber)) 
								{
								?>
									<div class="row">
									<div class="col-sm-12">
									<div class="span1 valve-<?php echo $i?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php if($aPositionName[0] == ''){ echo 'Spa';} else { echo $aPositionName[0];} ?></div>
									<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
									<select id='switch-me-<?php echo $i;?>'>
									<option value='1' <?php if($iValvesVal == '1') { echo 'selected="selected"';} ?>>Spa</option>
									<option value='0' <?php if($iValvesVal == '0' || $iValvesVal == '') { echo 'selected="selected"';} ?>></option>
									<option value='2' <?php if($iValvesVal == '2') { echo 'selected="selected"';} ?>>Pool</option>
									</select>
									<div class="valve-<?php echo $i?>" value="0" id="off-<?php echo $i;?>" style="color: red;font-weight: bold;width: 0; margin-left: 30px; cursor: pointer;">
										OFF
	</div>                              </div>
									<div class="span1 valve-<?php echo $i?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php if($aPositionName[1] == ''){ echo 'Pool';} else { echo $aPositionName[1];} ?></div>
									<div style="float: right;margin-right: 10px;margin-top: 10px;"><strong><span style="color:#C9376E;"><?php echo 'Valve '.$i; ?></span></strong></div>
									<script type="text/javascript">
										  $(function()
										  {
												var bgColor = '#E8E8E8';
												<?php if($iValvesVal == '1' || $iValvesVal == '2') { ?>
														bgColor = '#45A31F';
												<?php } else { ?>
														bgColor = '#E8E8E8';
												<?php } ?>
												
												$('#switch-me-<?php echo $i;?>').switchy();
												
												$('.valve-<?php echo $i?>').on('click', function(event){
													//event.preventDefault();
													//return false;
													$('#switch-me-<?php echo $i;?>').val($(this).attr('value')).change();
												});
												
												$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
														backgroundColor: bgColor
													});
												
												$('#switch-me-<?php echo $i;?>').on('change', function(event)
												{
													if(sAccess == 2)
													{
														if(iActiveMode != 2)
														{
															var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
															if(bConfirm)
															{
																$.ajax({
																	type: "POST",
																	url: "<?php echo site_url('analog/changeMode');?>", 
																	data: {iMode:'2'},
																	success: function(data) {
																	}
																});
																//event.preventDefault();
																//return false;
																// Animate Switchy Bar background color
																var bgColor = '#E8E8E8';

																if ($(this).val() == '1' || $(this).val() == '2')
																{
																	bgColor = '#45A31F';
																} 
																$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
																	backgroundColor: bgColor
																});
															
																
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
																$.ajax({
																	type: "POST",
																	url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																	data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																	success: function(data) {
																	//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																	location.reload();
																	}

																});
															}
														}
														else											
														{
															//event.preventDefault();
															//return false;
															// Animate Switchy Bar background color
															var bgColor = '#E8E8E8';

															if ($(this).val() == '1' || $(this).val() == '2')
															{
																bgColor = '#45A31F';
															} 
															$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
																backgroundColor: bgColor
															});
														
															
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																success: function(data) {
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																}

															});
														}
													}
												});
											});
									</script> 
								</div>
								</div>
								<div style="height:10px;">&nbsp;</div>
							<?php }	
									else
								    {
										echo '<div class="row"><div class="col-sm-12"><div class="tagcloud clearfix"><span style="color:#FF0000;">Relay Not Assigned.</span><div style="float: right;margin-right: 10px; color:#C9376E"><strong>Valve '.$i.'</strong></div></div></div></div><div style="height:10px;">&nbsp;</div>';
								    }
								}
							}
							?>
						</div>
					</div>
					</div>
				</div>
				<div class="col-sm-8">
				<div class="widget-container widget-stats boxed green-line">
					<div class="widget-title">
						<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
						<h3>Valve Settings</h3>
					</div>
				<?php
					if( $iValveNumber == 0 || $iValveNumber == '' )
							{ ?>
								<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
										<span style="color:red">Please add number of Vavles in the <a href="<?php echo base_url('home/setting/');?>">Settings</a> Page!</span>
								</div>
								</div>
								
							<?php 
							}
							else
							{
							?>
							
								<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
							
							  <table class="table table-hover">
								<thead>
								  <tr>
									<th class="header" style="width:25%">Valve</th>
									<th class="header"  style="width:25%">Type</th>
									<th class="header"  style="width:50%">Action</th>
								  </tr>
								</thead>
								<tbody>
								<?php			
								$arrValve	=	array(0,1,2,3,4,5,6,7);
								$j=0;
								$remainigCount	=	0;
								$chkValve		=	0;
								
								if(!empty($ValveRelays))
								{
									foreach($ValveRelays as $valve)
									{
										$i = $valve->device_number;
										$j = $i *2;
										
										unset($arrValve[$i]);
										
										$iValvesVal = $sValves[$i];
										$iValvesNewValSb1 = 1;
										$iValvesNewValSb2 = 2 ;
										if($iValvesVal == 1)
										{
										  $iValvesNewValSb1 = 0;
										}
										if($iValvesVal == 2)
										{
										  $iValvesNewValSb2 = 1;
										}
										$sValvesVal1 = false;
										$sValvesVal2 = false;
										if($iValvesVal == 1)
										  $sValvesVal1 = true;
										if($iValvesVal == 2)
										  $sValvesVal2 = true;
										//$sValvesNameDb = get_device_name(3, $i);

										$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sValvesNameDb == '')
										  $sValvesNameDb = 'Add Name';
									  
										$aPositionName   =  $this->home_model->getPositionName($i,$sDevice);
										$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice));
										$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice);
										
									?>
										<tr>
										<td>Valve <?php echo $i;?><br />(<a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>" ><?php echo $sValvesNameDb;?></a>)</td>
										<td>
											<div class="rowRadio"><div class="custom-radio"><input class="valveRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="valveRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="valveRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label></div></div>
										</td>
										<td>

										<a class="btn btn-green btn-small" style="width:120px" href="<?php if($sAccess == '2') { echo base_url('home/valveRelays/'.base64_encode($i).'/'.base64_encode($sDevice));} else { echo 'javascript:void(0);';}?>"><span>Add Relays</span></a>
										<?php if(!empty($aRelayNumber)) { ?>
										<a class="btn btn-red btn-small" style="width:120px" href="javascript:void(0);" <?php if($sAccess == '2') { echo 'onclick="return RemoveValveRelays(\''.$i.'\')"';} ?>><span>Remove Relays</span></a>
										<?php } ?>
										<a class="btn btn-small" style="width:120px" href="<?php if($sAccess == 2) { echo site_url('home/positionName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>"><span>Edit Position</span></a>
										<!--<a class="btn btn-small btn-red" style="width:120px" href="<?php if($sAccess == 2) { echo site_url('home/removeValve/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>"><span>Remove Valve</span></a>-->
										</td>
										</tr>
								<?php
										$chkValve++;
									} ?>
								<?php  } ?>	
							
							<?php
								$remainigCount	=	$iValveNumber - $chkValve;
								//for ($i=0;$i < $valve_count; $i++)	
								//for ($i=0;$i < $remainigCount ; $i++)
								foreach($arrValve as $i)	
								{
									if($remainigCount == 0)	
										break;
									
									$remainigCount--;
									
									$j = $i * 2;
									
									$iValvesVal = $sValves[$i];
									$iValvesNewValSb1 = 1;
									$iValvesNewValSb2 = 2 ;
									if($iValvesVal == 1)
									{
									  $iValvesNewValSb1 = 0;
									}
									if($iValvesVal == 2)
									{
									  $iValvesNewValSb2 = 1;
									}
									$sValvesVal1 = false;
									$sValvesVal2 = false;
									if($iValvesVal == 1)
									  $sValvesVal1 = true;
									if($iValvesVal == 2)
									  $sValvesVal2 = true;
									//$sValvesNameDb = get_device_name(3, $i);

									$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice);
									if($sValvesNameDb == '')
									  $sValvesNameDb = 'Add Name';
								  
									$aPositionName   =  $this->home_model->getPositionName($i,$sDevice);
									$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice));
									$iPower	 	     =  $this->home_model->getDevicePower($i,$sDevice);
									$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice);
							?>
									<tr>
										<td>Valve <?php echo $i;?><br />(<a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>" ><?php echo $sValvesNameDb;?></a>)</td>
										<td>
											<div class="rowRadio"><div class="custom-radio"><input class="valveRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="valveRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="valveRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label></div></div>
										</td>
										<td>
										<a class="btn btn-green btn-small" style="width:120px" href="<?php if($sAccess == '2') { echo base_url('home/valveRelays/'.base64_encode($i).'/'.base64_encode($sDevice));} else { echo 'javascript:void(0);';}?>"><span>Add Relays</span></a>
										<?php if(!empty($aRelayNumber)) { ?>
										&nbsp;&nbsp;<a class="btn btn-red btn-small" style="width:120px" href="javascript:void(0);" <?php if($sAccess == '2') { echo ' onclick="return RemoveValveRelays(\''.$i.'\');"';}?>><span>Remove Relays</span></a>
										<?php } ?>
										<a class="btn btn-small" style="width:120px" href="<?php if($sAccess == 2) { echo site_url('home/positionName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>"><span>Edit Position</span></a>
										<a class="btn btn-small btn-red" style="width:120px" href="javascript:void(0);" <?php if($sAccess == '2') { echo ' onclick="onclick="removeValve();"';}?> ><span>Remove Valve</span></a>
										</td>
										</tr>
								<?php	} ?>
									<tr><td colspan="3">
									<div class="buttons">
									<a class="btn btn-icon btn-icon-right btn-icon-go" id="addMoreValve" href="javascript:void(0);" hidefocus="true" style="outline: medium none; padding:0px !important;"><span>Add More Valve</span></a>
									</div>
									<div id="moreValve" style="display:none;">
									<input type="text" value="" id="moreValveCnt" name="moreValveCnt" hidefocus="true" style="outline: medium none; width: 45%; margin-top:1px; margin-right:10px;" placeholder="Add Number"><a class="btn btn-icon btn-green btn-icon-right btn-icon-checkout" href="javascript:void(0);" style="padding:0px !important;" hidefocus="true" onclick="saveValveCount()"><span>Save</span></a>
									</div>
									</td></tr>
								</tbody>
								</table>
								</div>
							</div>
						</div>							
								
							<?php	
							}
							
							?>
				</div>
			</div>	
		 
	<?php  } ?>
<?php } ?>	
<!-- END : VALVE DEVICES-->

<!-- START : PUMPS -->
<?php if($sDevice == 'PS') {  // START : Pump Device
				$iPumpsNumber	=	$extra['PumpsNumber'];
		?>
		<?php if($sAccess == 1 || $sAccess == 2) { ?>
			<div class="row">
				<div class="col-sm-6">
					<div class="widget-container widget-stats boxed green-line">
					<div class="widget-title">
						<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
						<h3>ON/OFF</h3>
					</div>
					<div class="stats-content clearfix">
						<div class="stats-content-right" style="width:96% !important; margin-left:5px; margin-right:5px; margin-top:10px; float:none;">
						<?php
                    
								if( $iPumpsNumber == 0 || $iPumpsNumber == '' )
								{ ?>
									
									<div class="row">
										<div class="col-sm-12">
											<span style="color:red">Please add number of Pumps in the <a href="<?php echo base_url('home/setting/');?>">Settings</a> Page!</span>
										</div>
									</div>
									
								<?php 
								}
								else
								{
									$arrPump		=	array(0,1,2);
									$remainigCount	=	0;
									$chkPump		=	0;
									if(!empty($Pumps))
									{
										foreach($Pumps as $pump)
										{
											
										$i= $pump->pump_number;
										unset($arrPump[$i]);		
										$iPumpVal = $sPump[$i];
										$sPumpNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sPumpNameDb == '')
										  $sPumpNameDb = 'Add Name';
										
										$sStatus2Speed	=	'';
										
										$aPumpDetails = $this->home_model->getPumpDetails($i);
										$sPumpType		=	'';
										if(!empty($aPumpDetails))
										{
											foreach($aPumpDetails as $aResultEdit)
											{
												$sPumpType    = $aResultEdit->pump_type;//Pump Type
												$sPumpRelay   = $aResultEdit->relay_number;//Assigned Relay Number
												$sPumpRelay1   = $aResultEdit->relay_number_1;//Assigned Relay Number
												if($sPumpType == '12')
												{
													$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
												}
												else if($sPumpType == '24')
												{
													$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
												}
												else if($sPumpType == '2Speed')
												{
													$sPumpSubType    = $aResultEdit->pump_sub_type;//Pump SUB Type
													$sStatus2Speed   = $aResultEdit->status;//Pump SUB Type
													
													if($sStatus2Speed == '0')
													{
														$iPumpVal        = $sStatus2Speed;
													}
													else if($sStatus2Speed == '1')											
													{
														if($sPumpSubType == '12')
														{
															$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
														}
														else if($sPumpSubType == '24')
														{
															$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
														}
													}
													else if($sStatus2Speed == '2')											
													{
														if($sPumpSubType == '12')
														{
															$iPumpVal = $sPowercenter[$sPumpRelay1]; //Taken the status
														}
														else if($sPumpSubType == '24')
														{
															$iPumpVal = $sRelays[$sPumpRelay1];//Taken the status
														}
													}
												}
												else if(preg_match('/Emulator/',$sPumpType))
												{
													 $iPumpVal = $sPump[$i];
												}
											}
										}	//END : Getting assigned relay status from the Server.
										$sPumpVal = false;
										if($iPumpVal)
										  $sPumpVal = true;
										
										$strChecked	=	'';
										if($iPumpVal > 0)
											$strChecked	=	'class="checked"';
									  
										$sMainType	 = $this->home_model->getDeviceMainType($i,$sDevice);
										
										$strPumpName = 'Pump '.$i;
										if($sPumpNameDb != '' && $sPumpNameDb != 'Add Name')
											$strPumpName .= '('.$sPumpNameDb.')';
										
										if($sStatus2Speed == '')
											$sStatus2Speed = '0';
								?>
										<div class="row">
										<div class="col-sm-12">
										<?php if($sPumpType == '2Speed') { ?>
										<script>
										var iActiveMode = '<?php echo $iActiveMode;?>';
										var sAccess 	= '<?php echo $sAccess;?>';
										</script>
										<link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
										<script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
										<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
										<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>
										<div class="span1 pump-<?php echo $i?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 1</div>
										<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
										<select id='switch-me-<?php echo $i;?>'>
										<option value='1' <?php if($sStatus2Speed == '1') { echo 'selected="selected"';} ?>>Spa</option>
										<option value='0' <?php if($sStatus2Speed == '0' || $iPumpVal == '') { echo 'selected="selected"';} ?>></option>
										<option value='2' <?php if($sStatus2Speed == '2') { echo 'selected="selected"';} ?>>Pool</option>
										</select>
										<div class="pump-<?php echo $i?>" value="0" id="off-<?php echo $i;?>" style="color: red;font-weight: bold;width: 0; margin-left: 30px; cursor: pointer;">
											OFF
										</div>                              </div>
										<div class="span1 pump-<?php echo $i?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 2</div>
										<div style="margin-top:10px; float:right; color:#C9376E;"><strong><?php echo $strPumpName;?></strong></div>
										
									  <script type="text/javascript">
									  
									  $(function()
									  {
										  var bgColor = '#E8E8E8';
											<?php if($iPumpVal == '1' || $iPumpVal == '2') { ?>
													bgColor = '#45A31F';
											<?php } else { ?>
													bgColor = '#E8E8E8';
											<?php } ?>
											
											$('#switch-me-<?php echo $i;?>').switchy();
											
											$('.pump-<?php echo $i?>').on('click', function(event){
												//event.preventDefault();
												//return false;
												$('#switch-me-<?php echo $i;?>').val($(this).attr('value')).change();
											});
											
											$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
													backgroundColor: bgColor
												});
											
											$('#switch-me-<?php echo $i;?>').on('change', function(event)
											{
												if(sAccess == 2)
												{
													if(iActiveMode != 2)
													{
														var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
														if(bConfirm)
														{
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('analog/changeMode');?>", 
																data: {iMode:'2'},
																success: function(data) {
																}
															});
															//event.preventDefault();
															//return false;
															// Animate Switchy Bar background color
															var bgColor = '#E8E8E8';

															if ($(this).val() == '1' || $(this).val() == '2')
															{
																bgColor = '#45A31F';
															} 
															$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
																backgroundColor: bgColor
															});
														
															
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																success: function(data) {
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																location.reload();
																}

															});
														}
													}
													else											
													{
														//event.preventDefault();
														//return false;
														// Animate Switchy Bar background color
														var bgColor = '#E8E8E8';

														if ($(this).val() == '1' || $(this).val() == '2')
														{
															bgColor = '#45A31F';
														} 
														$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
															backgroundColor: bgColor
														});
													
														
														//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
														$.ajax({
															type: "POST",
															url: "<?php echo site_url('home/updateStatusOnOff');?>", 
															data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
															success: function(data) {
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
															}

														});
													}
												}
											});
										});
								   </script>
								   
										<?php } else if($sPumpType != '') { ?>
										
										<?php if(preg_match('/Emulator/',$sPumpType)) { ?>
										<script>
										  $(document).ready(function() {
											setInterval( function() {
												$.getJSON('<?php echo site_url('cron/pumpResponseLatest/');?>', {iPumpID: "<?php echo $i;?>"}, function(json) {
													
													if(json == '')
													{
														$("#lablePump-"+<?php echo $i;?>).removeClass('checked');
														$("#pumpRealResponse_"+<?php echo $i;?>).html('');
													}
													else
													{
														$("#pumpRealResponse_"+<?php echo $i;?>).html(json);
														if($("#lablePump-"+<?php echo $i;?>).hasClass('checked'))
														{}
														else
														{
															$("#lablePump-"+<?php echo $i;?>).addClass('checked');
														}
													}
												});
												},30000);
										  });
										</script>										  
										   <?php } ?>
										<div class="rowCheckbox switch">
											<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="pumps-<?php echo $i?>" name="pumps-<?php echo $i?>" class="pumpsButton" hidefocus="true" style="outline: medium none;">
												<label style="margin-left: 60px;" <?php echo $strChecked;?>  id="lablePump-<?php echo $i?>" for="pumps-<?php echo $i?>"><span style="float:right; color:#C9376E;"><?php echo $strPumpName;?></span></label>
											</div>
										</div>
											<?php if(preg_match('/Emulator/',$sPumpType)) { ?>
											<div id="pumpRealResponse_<?php echo $i;?>" style="color: #164c87;font-weight: bold;"><?php if($iPumpVal > 0) { echo $strPumpsResponse		= $this->home_model->selectPumpsLatestResponse($i); }?></div>
											<?php } ?>
										<?php } ?>
										</div>
										</div>
										<div style="height:20px;">&nbsp;</div>
									<?php 
										$chkPump++;
									}
									?>
								<?php } ?>
								<?php 
									$remainigCount	=	$iPumpsNumber - $chkPump;
									//for ($i=0;$i < $valve_count; $i++)	
									//for ($i=0;$i < $remainigCount ; $i++)
									foreach($arrPump as $i)	
									{
										if($remainigCount == 0)	
											break;
										
										$remainigCount--;
												
									//for ($i=0;$i < $pump_count; $i++)
									//{
										$iPumpVal = $sPump[$i];
										/* $iPumpNewValSb = 1;
										if($iPumpVal == 1)
										{
										  $iPumpNewValSb = 0;
										}
										$sPumpVal = false;
										if($iPumpVal)
										  $sPumpVal = true; */
										//$sRelayNameDb = get_device_name(1, $i);
									
										$sPumpNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sPumpNameDb == '')
										  $sPumpNameDb = 'Add Name';
										
										//$iPower	 = $this->home_model->getDevicePower($i,$sDevice);
										
										//$sPowercenter = '01000000'		;
										//START : Getting assigned relay status from the Server.	
										//Details of Pump
										
										$sStatus2Speed	=	'';
										
										$aPumpDetails = $this->home_model->getPumpDetails($i);
										$sPumpType		=	'';
										if(!empty($aPumpDetails))
										{
											foreach($aPumpDetails as $aResultEdit)
											{
												$sPumpType    = $aResultEdit->pump_type;//Pump Type
												$sPumpRelay   = $aResultEdit->relay_number;//Assigned Relay Number
												$sPumpRelay1   = $aResultEdit->relay_number_1;//Assigned Relay Number
												if($sPumpType == '12')
												{
													$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
												}
												else if($sPumpType == '24')
												{
													$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
												}
												else if($sPumpType == '2Speed')
												{
													$sPumpSubType    = $aResultEdit->pump_sub_type;//Pump SUB Type
													$sStatus2Speed   = $aResultEdit->status;//Pump SUB Type
													
													
													if($sStatus2Speed == '0')
													{
														$iPumpVal        = $sStatus2Speed;
													}
													else if($sStatus2Speed == '1')											
													{
														if($sPumpSubType == '12')
														{
															$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
														}
														else if($sPumpSubType == '24')
														{
															$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
														}
													}
													else if($sStatus2Speed == '2')											
													{
														if($sPumpSubType == '12')
														{
															$iPumpVal = $sPowercenter[$sPumpRelay1]; //Taken the status
														}
														else if($sPumpSubType == '24')
														{
															$iPumpVal = $sRelays[$sPumpRelay1];//Taken the status
														}
													}
												}
												else if(preg_match('/Emulator/',$sPumpType))
												{
													 $iPumpVal = $sPump[$i];
												}
											}
										}	//END : Getting assigned relay status from the Server.
										$sPumpVal = false;
										if($iPumpVal)
										  $sPumpVal = true;
									  
										$sMainType	 = $this->home_model->getDeviceMainType($i,$sDevice);
										
										$strChecked	=	'';
										if($iPumpVal > '1')
											$strChecked	=	'class="checked"';
										
										$strPumpName = 'Pump '.$i;
										if($sPumpNameDb != '' && $sPumpNameDb != 'Add Name')
											$strPumpName .= ' ('.$sPumpNameDb.')';
										
										if($sStatus2Speed == '')
											$sStatus2Speed = '0';
								?>	
										<div class="row">
										<div class="col-sm-12">
										<?php if($sPumpType == '2Speed') { ?>
										<script>
										var iActiveMode = '<?php echo $iActiveMode;?>';
										var sAccess 	= '<?php echo $sAccess;?>';
										</script>
										<link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
										<script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
										<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
										<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>
										<div class="span1 pump-<?php echo $i?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 1</div>
										<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
										<select id='switch-me-<?php echo $i;?>'>
										<option value='1' <?php if($sStatus2Speed == '1') { echo 'selected="selected"';} ?>>Spa</option>
										<option value='0' <?php if($sStatus2Speed == '0' || $iPumpVal == '') { echo 'selected="selected"';} ?>></option>
										<option value='2' <?php if($sStatus2Speed == '2') { echo 'selected="selected"';} ?>>Pool</option>
										</select>
										<div class="pump-<?php echo $i?>" value="0" id="off-<?php echo $i;?>" style="color: red;font-weight: bold;width: 0; margin-left: 30px; cursor: pointer;">
											OFF
				</div>                              </div>
										<div class="span1 pump-<?php echo $i?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 2</div>
										<div style="margin-top:10px; float:right; color:#C9376E;"><strong><?php echo $strPumpName;?></strong></div>
									  <script type="text/javascript">
									  
									  $(function()
									  {
										  var bgColor = '#E8E8E8';
											<?php if($iPumpVal == '1' || $iPumpVal == '2') { ?>
													bgColor = '#45A31F';
											<?php } else { ?>
													bgColor = '#E8E8E8';
											<?php } ?>
											
											$('#switch-me-<?php echo $i;?>').switchy();
											
											$('.pump-<?php echo $i?>').on('click', function(event){
												//event.preventDefault();
												//return false;
												$('#switch-me-<?php echo $i;?>').val($(this).attr('value')).change();
											});
											
											$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
													backgroundColor: bgColor
												});
											
											$('#switch-me-<?php echo $i;?>').on('change', function(event)
											{
												if(sAccess == 2)
												{
													if(iActiveMode != 2)
													{
														var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
														if(bConfirm)
														{
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('analog/changeMode');?>", 
																data: {iMode:'2'},
																success: function(data) {
																}
															});
															//event.preventDefault();
															//return false;
															// Animate Switchy Bar background color
															var bgColor = '#E8E8E8';

															if ($(this).val() == '1' || $(this).val() == '2')
															{
																bgColor = '#45A31F';
															} 
															$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
																backgroundColor: bgColor
															});
														
															
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																success: function(data) {
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																location.reload();
																}

															});
														}
													}
													else											
													{
														//event.preventDefault();
														//return false;
														// Animate Switchy Bar background color
														var bgColor = '#E8E8E8';

														if ($(this).val() == '1' || $(this).val() == '2')
														{
															bgColor = '#45A31F';
														} 
														$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
															backgroundColor: bgColor
														});
													
														
														//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
														$.ajax({
															type: "POST",
															url: "<?php echo site_url('home/updateStatusOnOff');?>", 
															data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
															success: function(data) {
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
															}

														});
													}
												}
											});
										});
								   </script>
								   
										<?php } else if($sPumpType != '') { ?>
										 <div class="rowCheckbox switch">
											<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="pumps-<?php echo $i?>" name="pumps-<?php echo $i?>" class="pumpsButton" hidefocus="true" style="outline: medium none;">
												<label style="margin-left: 60px;" <?php echo $strChecked;?>  id="lablePump-<?php echo $i?>" for="pumps-<?php echo $i?>"><span style="color:#C9376E;float:right;" ><?php echo $strPumpName;?></span></label>
											</div>
										</div>
										
										<?php } else if($sPumpType == '') { ?>
										<span style="color:#E94180;"><strong>Pump <?php echo $i; ?> not configured.</strong></span>
										<?php } ?>
										</div>
										</div>
										<div style="height:20px;">&nbsp;</div>
									<?php } ?>
							<?php } ?>
						</div>
					</div>
					</div>
				</div>
				  
				<div class="col-sm-6">
						<!-- Statistics -->
						<div class="widget-container widget-stats boxed green-line">
							<div class="widget-title">
								<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
								<h3>PUMP Settings</h3>
							</div>
							<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
							
							  <table class="table table-hover">
								<thead>
								  <tr>
									<th class="header" style="width:25%">Pump</th>
									<th class="header"  style="width:25%">Type</th>
									<th class="header"  style="width:50%">Action</th>
								  </tr>
								</thead>
								<tbody>
								
								<?php
                    
								if( $iPumpsNumber == 0 || $iPumpsNumber == '' )
								{ ?>
									
									<tr>
										<td colspan="3">
											<span style="color:red">Please add number of Pumps in the <a href="<?php echo base_url('home/setting/');?>">Settings</a> Page!</span>
										</td>
									</tr>
									
								<?php 
								}
								else
								{
									$arrPump		=	array(0,1,2);
									$remainigCount	=	0;
									$chkPump		=	0;
									if(!empty($Pumps))
									{
										foreach($Pumps as $pump)
										{
											
										$i= $pump->pump_number;
										unset($arrPump[$i]);		
										//for ($i=0;$i < $pump_count; $i++)
										//{
										$iPumpVal = $sPump[$i];
										/* $iPumpNewValSb = 1;
										if($iPumpVal == 1)
										{
										  $iPumpNewValSb = 0;
										}
										$sPumpVal = false;
										if($iPumpVal)
										  $sPumpVal = true; */
										//$sRelayNameDb = get_device_name(1, $i);
									
										$sPumpNameDb =  $this->home_model->getDeviceName($i,$sDevice);
										if($sPumpNameDb == '')
										  $sPumpNameDb = 'Add Name';
										
										//$iPower	 = $this->home_model->getDevicePower($i,$sDevice);
										$sStatus2Speed	=	'';
										//$sPowercenter = '01000000'		;
										//START : Getting assigned relay status from the Server.	
										//Details of Pump
										$aPumpDetails = $this->home_model->getPumpDetails($i);
										$sPumpType		=	'';
										$sPumpSpeed		=	'';
										if(!empty($aPumpDetails))
										{
											foreach($aPumpDetails as $aResultEdit)
											{
												$sPumpType    = $aResultEdit->pump_type;//Pump Type
												$sPumpRelay   = $aResultEdit->relay_number;//Assigned Relay Number
												$sPumpRelay1   = $aResultEdit->relay_number_1;//Assigned Relay Number
												if($sPumpType == '12')
												{
													$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
												}
												else if($sPumpType == '24')
												{
													$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
												}
												else if($sPumpType == '2Speed')
												{
													$sPumpSubType    = $aResultEdit->pump_sub_type;//Pump SUB Type
													$sStatus2Speed   = $aResultEdit->status;//Pump SUB Type
													
													
													if($sStatus2Speed == '0')
													{
														$iPumpVal        = $sStatus2Speed;
													}
													else if($sStatus2Speed == '1')											
													{
														if($sPumpSubType == '12')
														{
															$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
														}
														else if($sPumpSubType == '24')
														{
															$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
														}
													}
													else if($sStatus2Speed == '2')											
													{
														if($sPumpSubType == '12')
														{
															$iPumpVal = $sPowercenter[$sPumpRelay1]; //Taken the status
														}
														else if($sPumpSubType == '24')
														{
															$iPumpVal = $sRelays[$sPumpRelay1];//Taken the status
														}
													}
												}
												else if(preg_match('/Emulator/',$sPumpType) || preg_match('/Intellicom/',$sPumpType))
												{
													 $iPumpVal 		= $sPump[$i];
													 $sPumpSpeed 	= $aResultEdit->pump_speed;	
												}
											}
										}	//END : Getting assigned relay status from the Server.
										$sPumpVal = false;
										if($iPumpVal)
										  $sPumpVal = true;
									  
										$strChecked	=	'';
										if($iPumpVal == '1')
											$strChecked	=	'class="checked"';
									  
										$sMainType	 = $this->home_model->getDeviceMainType($i,$sDevice);
										
										$strPumpName = 'Pump '.$i;
										if($sPumpNameDb != '' && $sPumpNameDb != 'Add Name')
											$strPumpName .= ' ('.$sPumpNameDb.')';
								?>
									<tr>
									<td>Pump <?php echo $i;?><br />(<a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>" ><?php echo $sPumpNameDb;?></a>)<br /><br /><a href="javascript:void(0);" class="btn btn-red btn-small"><span><?php echo $sPumpType;?></span></a></td>
									<td>
										<div class="rowRadio"><div class="custom-radio"><input class="pumpRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
										<div class="rowRadio"><div class="custom-radio"><input class="pumpRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
										<div class="rowRadio"><div class="custom-radio"><input class="pumpRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label></div></div>
									</td>
									<td><a class="btn btn-green btn-small" href="<?php if($sAccess == 2){echo site_url('home/pumpConfigure/'.base64_encode($i).'/');}else{echo 'javscript:void(0);';}?>"><span>Configure</span></a>&nbsp;&nbsp;
										<a class="btn btn-red btn-small" href="<?php if($sAccess == 2){ echo site_url('home/setProgramsPump/'.base64_encode($i).'/');} else {echo 'javascript:void(0);';}?>"><span>Programs</span></a>
									<?php 
										if(preg_match('/Emulator/',$sPumpType) || preg_match('/Intellicom/',$sPumpType)) {
									?>									
									<div style="padding-top: 10px; padding-bottom: 10px;">
									Change Speed: <br />	
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed0" <?php if($sPumpSpeed == 0) {echo 'checked="checked";';}?> value="0">&nbsp;0&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed1" value="1" <?php if($sPumpSpeed == 1) {echo 'checked="checked";';}?>>&nbsp;1&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed2" value="2" <?php if($sPumpSpeed == 2) {echo 'checked="checked";';}?>>&nbsp;2&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed3" value="3" <?php if($sPumpSpeed == 3) {echo 'checked="checked";';}?>>&nbsp;3&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed4" value="4" <?php if($sPumpSpeed == 4) {echo 'checked="checked";';}?>>&nbsp;4&nbsp;&nbsp;
									</div>
										<?php } ?>
									<div style="padding-top: 10px; padding-bottom: 10px;">
									<a href="javascript:void(0);" onclick="removePump('<?php echo $i;?>')" class="btn btn-red btn-small"><span>Remove Pump</span>
									</a>&nbsp;&nbsp;<span id="loadingImgPumpRemove_<?php echo $i;?>" style="display:none;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span>
									</div>									
									</td>
									</tr>
									<?php 
											$chkPump++;
									} ?>
										
									<?php }
											
											$remainigCount	=	$iPumpsNumber - $chkPump;
											//for ($i=0;$i < $valve_count; $i++)	
											//for ($i=0;$i < $remainigCount ; $i++)
											foreach($arrPump as $i)	
											{
												if($remainigCount == 0)	
													break;
												
												$remainigCount--;
														
											//for ($i=0;$i < $pump_count; $i++)
											//{
												$iPumpVal = $sPump[$i];
												/* $iPumpNewValSb = 1;
												if($iPumpVal == 1)
												{
												  $iPumpNewValSb = 0;
												}
												$sPumpVal = false;
												if($iPumpVal)
												  $sPumpVal = true; */
												//$sRelayNameDb = get_device_name(1, $i);
											
												$sPumpNameDb =  $this->home_model->getDeviceName($i,$sDevice);
												if($sPumpNameDb == '')
												  $sPumpNameDb = 'Add Name';
												
												//$iPower	 = $this->home_model->getDevicePower($i,$sDevice);
												$sStatus2Speed	=	'';
												//$sPowercenter = '01000000'		;
												//START : Getting assigned relay status from the Server.	
												//Details of Pump
												$aPumpDetails = $this->home_model->getPumpDetails($i);
												$sPumpType		=	'';
												$sPumpSpeed 	= 	'';	
												
												if(!empty($aPumpDetails))
												{
													foreach($aPumpDetails as $aResultEdit)
													{
														$sPumpType    = $aResultEdit->pump_type;//Pump Type
														$sPumpRelay   = $aResultEdit->relay_number;//Assigned Relay Number
														$sPumpRelay1   = $aResultEdit->relay_number_1;//Assigned Relay Number
														if($sPumpType == '12')
														{
															$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
														}
														else if($sPumpType == '24')
														{
															$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
														}
														else if($sPumpType == '2Speed')
														{
															$sPumpSubType    = $aResultEdit->pump_sub_type;//Pump SUB Type
															$sStatus2Speed   = $aResultEdit->status;//Pump SUB Type
															
															
															if($sStatus2Speed == '0')
															{
																$iPumpVal        = $sStatus2Speed;
															}
															else if($sStatus2Speed == '1')											
															{
																if($sPumpSubType == '12')
																{
																	$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
																}
																else if($sPumpSubType == '24')
																{
																	$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
																}
															}
															else if($sStatus2Speed == '2')											
															{
																if($sPumpSubType == '12')
																{
																	$iPumpVal = $sPowercenter[$sPumpRelay1]; //Taken the status
																}
																else if($sPumpSubType == '24')
																{
																	$iPumpVal = $sRelays[$sPumpRelay1];//Taken the status
																}
															}
														}
														else if(preg_match('/Emulator/',$sPumpType) || preg_match('/Intellicom/',$sPumpType))
														{
															 $iPumpVal 		= $sPump[$i];
															 $sPumpSpeed 	= $aResultEdit->pump_speed;	
														}
													}
												}	//END : Getting assigned relay status from the Server.
												$sPumpVal = false;
												if($iPumpVal)
												  $sPumpVal = true;
											  
												$sMainType	 = $this->home_model->getDeviceMainType($i,$sDevice);
												
												$strChecked	=	'';
												if($iPumpVal == '1')
													$strChecked	=	'class="checked"';
												
												$strPumpName = 'Pump '.$i;
												if($sPumpNameDb != '')
													$strPumpName .= ' ('.$sPumpNameDb.')';
									?>	
									<tr>
									<td>Pump <?php echo $i;?><br />(<a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>" ><?php echo $sPumpNameDb;?></a>)</td>
									<td>
										<div class="rowRadio"><div class="custom-radio"><input class="pumpRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
										<div class="rowRadio"><div class="custom-radio"><input class="pumpRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
										<div class="rowRadio"><div class="custom-radio"><input class="pumpRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label></div></div>
									</td>
									<td><a class="btn btn-green btn-small" href="<?php if($sAccess == 2){echo site_url('home/pumpConfigure/'.base64_encode($i).'/');}else{echo 'javscript:void(0);';}?>"><span>Configure</span></a>&nbsp;&nbsp;
										<a class="btn btn-red btn-small" href="<?php if($sAccess == 2){ echo site_url('home/setProgramsPump/'.base64_encode($i).'/');} else {echo 'javascript:void(0);';}?>"><span>Programs</span></a>
										<?php 
										if(preg_match('/Emulator/',$sPumpType) || preg_match('/Intellicom/',$sPumpType)) {
									?>									
									<div style="padding-top: 10px; padding-bottom: 10px;">
									Change Speed: <br />	
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed0" <?php if($sPumpSpeed == 0) {echo 'checked="checked";';}?> value="0">&nbsp;0&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed1" value="1" <?php if($sPumpSpeed == 1) {echo 'checked="checked";';}?>>&nbsp;1&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed2" value="2" <?php if($sPumpSpeed == 2) {echo 'checked="checked";';}?>>&nbsp;2&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed3" value="3" <?php if($sPumpSpeed == 3) {echo 'checked="checked";';}?>>&nbsp;3&nbsp;&nbsp;
									<input type="radio" class="pumpSpeedSet" name="pageSpeed_<?php echo $i;?>" id="pageSpeed4" value="4" <?php if($sPumpSpeed == 4) {echo 'checked="checked";';}?>>&nbsp;4&nbsp;&nbsp;
									</div>
										<?php } ?>	
									<div style="padding-top: 10px; padding-bottom: 10px;">
									<a href="javascript:void(0);" onclick="removePump('<?php echo $i;?>')" class="btn btn-red btn-small"><span>Remove Pump</span>
									</a>&nbsp;&nbsp;<span id="loadingImgPumpRemove_<?php echo $i;?>" style="display:none;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span>
									</div>			
									</td>
									</tr>
											<?php } ?>
									<?php } ?>		
								</tbody>
								</table>
								</div>
							</div>
						</div>
						<!--/ Statistics -->
					</div>
			</div><!-- /.row -->
	<?php } ?>
<?php } ?>	
<!-- END : PUMPS -->

<!-- START : Temperature Sensors -->

<?php if($sDevice == 'T') { ?>
	<?php if($sAccess == 1 || $sAccess == 2) 
		  { 
	?>
			<div class="row">
				<div class="col-sm-12">
					<!-- widget Tags-->
					<div class="widget-container widget-stats boxed green-line">
					<div class="widget-title">
						<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
						<h3>Temperature Sensor</h3>
					</div>
					<div class="stats-content clearfix">
					<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
					
					 <table class="table table-hover">
						<thead>
						  <tr style="font-weight:bold;">
							<th class="header" style="width:25%">Temperature sensor</th>
							<th class="header"  style="width:25%">Temperature</th>
							<th class="header"  style="width:25%">Action</th>
							<th class="header"  style="width:25%">&nbsp;</th>
						  </tr>
						</thead>
						<tbody>
						<?php
                    
							//START : Temperature sensor
							for ($i=0;$i < $temprature_count; $i++)
							{
								$iTempratureVal = $sTemprature[$i];
								
								$sTempratureNameDb =  $this->home_model->getDeviceName($i,$sDevice);
								if($sTempratureNameDb == '')
								  $sTempratureNameDb = 'Add Name';
							  
								if($iTempratureVal == '')
									$iTempratureVal = '-';
								
								$strBusNumber	 =	'';
								$strGetBusNumber = "SELECT light_relay_number FROM rlb_device WHERE device_type = 'T' AND device_number='".$i."'";
								
								$query  =   $this->db->query($strGetBusNumber);
								
								if($query->num_rows() > 0)
								{
									foreach($query->result() as $rowResult)
									{
										$strBusNumber	=	$rowResult->light_relay_number;
									}
								}
						?>
							  <tr>
								<td>Temperature sensor <?php echo $i;?></td>
								<td><?php echo $iTempratureVal;?></td>
								<td><a href="<?php if($sAccess == 2){ echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');} else { echo 'javascript:void(0);';}?>" ><?php echo $sTempratureNameDb;?></a></td>
								<td><a class="btn btn-small btn-red" href="<?php echo base_url('analog/tempConfig/'.base64_encode($i));?>"><span>Configure</span>	</a><?php if($i != 0 && $strBusNumber != '') { ?>&nbsp;&nbsp;<a class="btn btn-small btn-red" href="<?php echo base_url('analog/tempConfig/'.base64_encode($i).'/remove/');?>"><span>Remove Sensor</span>	</a><?php } ?></td>
							  </tr>
						<?php } ?>
						</tbody>
					</table>
					</div>
					</div>
						
					<!-- widget Tags-->
				</div>
			</div>	
	</div>
	<?php } ?>
<?php } ?>		
<!-- END : Temperature Sensors -->
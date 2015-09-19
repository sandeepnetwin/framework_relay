<?php
$this->load->model('home_model');
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
  }
  
  if($sAccess == '')
	$sAccess = '2' ; 
  
  if($sAccess == '0') {redirect(site_url('home/'));}
?>
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
	$(".relayButton").click(function()
	{
		 $(".loading-progress").show();
		var progress = $(".loading-progress").progressTimer({
			timeLimit: 10,
			onFinish: function () {
			  $(".loading-progress").hide();
			}
		});
		
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
		
		<?php if($iActiveMode == '2') { ?>
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
});
</script>
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
				  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
				  <li class="active"><?php echo $sDeviceFullName;?></li>
		</ol>
	</div>
</div>		
<!-- START : 24V AC RELAY -->
<?php if($sDevice == 'R') 
	  { ?>
	<?php if($sAccess == '1' || $sAccess == '2') 
		  { ?>
			<div class="row">
				<div class="col-sm-4">
					<!-- widget Tags-->
					<div class="widget-container widget-tags styled boxed">
						<div class="inner">
							<h3 class="widget-title">Relay ON/OFF</h3>
							<div class="loading-progress"></div>
							<div class="tagcloud clearfix">
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
												<label <?php echo $strChecked;?>  id="lableRelay-<?php echo $i?>" for="relay-<?php echo $i?>"><?php echo $strRelayName;?></label>
											</div>
										</div>
							<?php 	}		
								 }
							?> 
							</div>
						</div>
					</div>
					<!--/ widget Tags-->
				</div>
				  
				<div class="col-sm-8">
						<!-- Statistics -->
						<div class="widget-container widget-stats boxed green-line">
							<div class="widget-title">
								<a href="<?php echo base_url('home/setting/R/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
								<h3>24V Relay Settings</h3>
							</div>
							<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
								<div class="table-responsive">
							  <table class="table table-hover tablesorter">
								<thead>
								  <tr>
									<th class="header">Relay</th>
									<th class="header">Relay Type</th>
									<th class="header">Action</th>
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
										<td>Relay <?php echo $i;?><br />(<a href="<?php if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';} ?>" ><?php echo $sRelayNameDb;?></a>)</td>
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
										<td>Relay <?php echo $i;?><br />(<a href="<?php if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';} ?>" >
										<?php echo $sRelayNameDb;?></a>)</td>
										<td>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" <?php if($sMainType == '2'){ echo 'checked';}?>>Pool</label></div></div>
										</td>
										<td>
										<div class="inner">
											<a class="btn btn-primary btn-xs" href="<?php if($sAccess == '2') { echo site_url('home/setPrograms/'.base64_encode($i).'/');} else { echo 'javascript:void(0);';}?>">Programs</a>&nbsp&nbsp;<a class="btn btn-primary btn-xs" href="<?php if($sAccess == '2') { echo site_url('home/setPrograms/'.base64_encode($i).'/');} else { echo 'javascript:void(0);';}?>" style="width: 90px;"><?php echo $iRelayProgramCount;?><?php if($iRelayProgramCount == 1 || $iRelayProgramCount == 0){ echo ' Program';}else{ echo ' Programs';}?></a>&nbsp;&nbsp;<a class="btn btn-primary btn-xs" href="<?php if($sAccess == '2') {echo site_url('home/addTime/'.base64_encode($i).'/'.  base64_encode($sDevice));} else { echo 'javascript:void(0);';}?>"><?php echo $sDeviceTime;?></a>
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
						</div>
						<!--/ Statistics -->
					</div>
			</div><!-- /.row -->
	<?php } ?>
<?php } //Relay Device End ?>	
<!-- END : 24V AC RELAY -->
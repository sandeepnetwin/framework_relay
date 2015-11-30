
<h3>Light Setting</h3>
<section>
<div class="col-sm-12">
   <label for="no_light">How many Spa/Pool lights do you have?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Please select number of lights used in the mode?" /></label><a class="changeLink" id="changeLinkLight" href="javascript:void(0);" onclick="javascript:$('#LightForm').toggleClass('disableConfig');" style="float:right;" title="Click here to Enable/Disable light related settings!">Enable/Disable Light Configuration</a>
	<select name="no_light" id="no_light" class="form-control" onchange="showLightAssign(this.value);">
			<option <?php if(isset($arrMore['light']) &&  $arrMore['light'] == ''){echo 'selected="selected"';}?> value="">Select number of lights</option>
			<?php
			for($i=0;$i<($extra['LightNumber']+1);$i++)
			{
		?>
			<option <?php if(isset($arrMore['light']) &&  $arrMore['light'] == $i){echo 'selected="selected"';}?> value="<?php echo $i;?>"><?php echo $i;?></option>
		<?php } ?>
	</select>
	<div style="height:10px">&nbsp;</div>
	<div class="col-sm-4" style="padding-left:0px;">
		<div class="controls boxed green-line" style="min-height:210px;">
			<div class="inner">
				<h3 class="profile-title"><strong style="color:#C9376E;">Assign Light</strong></h3>
				<div id="contentsLight">
				<?php
					for($i=0;$i<$extra['LightNumber'];$i++)
					{
						$strLightName 		=	'Light '.($i+1);	
						$strLightNameTmp 	=	$this->home_model->getDeviceName($i,'L');
						if($strLightNameTmp != '')
							$strLightName	.=	' ('.$strLightNameTmp.')';
							
				?>
					<?php if($i != 0){ echo '<hr />'; }?>
					<div class="rowCheckbox switch">
						<div style="margin-bottom:10px;"><?php echo $strLightName;?></div>
						<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="relayLight-<?php echo $i?>" name="relayLight[]" hidefocus="true" style="outline: medium none;">
						<label id="lableRelay-<?php echo $i?>" for="relayLight-<?php echo $i?>"><span style="color:#C9376E;">&nbsp;</span></label>
						</div>
					</div>
						
				<?php 	
					}
				?>
				</div>
			</div>
		</div>
	</div>	
	<div class="col-sm-8" style="padding-right:0px;">
	<div id="LightForm" class="disableConfig">
		<div class="controls boxed green-line" style="min-height:210px;">
		<div class="inner">
			<div style="margin-bottom:10px;"><h3 class="confHeader">Light Configuration</h3></div>
			<table class="table removeBorder" id="lightTable" width="100%" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td style="width:48%;">Enter Number of Lights to Use in system:</td>
						<td style="width:2%;">&nbsp;</td>
						<td style="width:50%;"><input type="text" name="lightNumber" id="lightNumber" onkeyup="showLightDetails();" value="<?php if(isset($extra['LightNumber']) && $extra['LightNumber'] != 0) { echo $extra['LightNumber'];}?>" class="form-control inputText"></td>
					</tr>
					<?php 
						  for($i=1;$i<9;$i++)
						  {
								$sRelayType     =   '';
								$sRelayNumber   =   '';
								
								$aLightDetails  =   $this->home_model->getLightDeviceDetails(($i-1));
								
								if(!empty($aLightDetails))
								{
									foreach($aLightDetails as $aLight)
									$sRelayDetails  =   unserialize($aLight->light_relay_number);

									$sRelayType     =   $sRelayDetails['sRelayType'];
									$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
								}
								
								$sRelayNameDb =  $this->home_model->getDeviceName(($i-1),'L');
					?>		
							<tr id="lightDetails_<?php echo $i;?>" style="display:<?php if(isset($extra['LightNumber']) && $i <= $extra['LightNumber']) { echo ''; } else { echo 'none;';}?>">
								<td colspan="3">
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr><td colspan="3"><strong>Light <?php echo $i;?></strong></td></tr>
										<tr>
										<tr>
										<td width="48%">Enter Name for Light <?php echo $i;?>?</td>
										<td width="2%">&nbsp;</td>
										<td width="50%">
										<input type="text" id="lightName<?php echo $i;?>" name="lightName<?php echo $i;?>" class="form-control inputText" value="<?php echo $sRelayNameDb;?>">
										</td>
										</tr>
										<tr><td colspan="3">&nbsp;</td></tr>
										<td width="48%">How do you turn on your Light <?php echo $i;?>?</td>
										<td width="2%">&nbsp;</td>
										<td width="50%">
											<select onchange="showRelaysLight(this.value,'<?php echo $i;?>');" class="form-control valid" id="light<?php echo $i;?>_equiment" name="light<?php echo $i;?>_equiment">
												<option value="24" <?php if($sRelayType == '24') { echo 'selected="selected"';} ?>>24V AC Relays</option>
												<option value="12" <?php if($sRelayType == '12') { echo 'selected="selected"';} ?>>12V DC Relays</option>
											</select>
										</td>
										</tr>
										<tr><td colspan="3">&nbsp;</td></tr>
										<tr id="trLightSub<?php echo $i;?>_24" style="display:<?php if($sRelayType == '12') { echo 'none';} ?>">
										<td width="48%"><label for="light<?php echo $i;?>_sub_equiment_24">Select 24V Relay<span class="requiredMark">*</span></label></td>
										<td width="2%">&nbsp;</td>
										<td width="50%">
										<select name="light<?php echo $i;?>_sub_equiment_24" id="light<?php echo $i;?>_sub_equiment_24" class="form-control">
											<option value="">Select Relay</option>
											<?php foreach($sRelays as $relay) {
													$strSelect	=	'';
													if($relay == $sRelayNumber)
														$strSelect	=	'selected="selected"';
											?>
												<option value="<?php echo $relay;?>" <?php echo $strSelect;?>>Relay <?php echo $relay;?></option>
											<?php } ?>
										</select>
										</td>
										</tr>
					
										<tr id="trLightSub<?php echo $i;?>_12" style="display:<?php if($sRelayType == '24' || $sRelayType == '') { echo 'none';} ?>">
										<td width="48%"><label for="light<?php echo $i;?>_sub_equiment_12">Select 12V Relay<span class="requiredMark">*</span></label></td>
										<td width="2%">&nbsp;</td>
										<td width="50%">
											<select name="light<?php echo $i;?>_sub_equiment_12" id="light<?php echo $i;?>_sub_equiment_12" class="form-control">
												<option value="">Select PowerCenter</option>
												<?php foreach($sPowercenter as $relay) { 
													$strSelect	=	'';
													if($relay == $sRelayNumber)
														$strSelect	=	'selected="selected"';
												?>
												<option value="<?php echo $relay;?>" <?php echo $strSelect;?>>PowerCenter <?php echo $relay;?></option>
												<?php } ?>
											</select>
										</td></tr>
										
										<tr><td colspan="3"><hr style="border-color:#000;" /></td></tr>
									</table>
								</td>
							</tr>
					<?php 
						}
					?>	
					<tr id="lightSaveConf" style="display:<?php if($extra['LightNumber'] == 0){ echo 'none;';}?>"><td colspan="3"><a href="javascript:void(0);" onclick="checkAndSaveLight();" class="btn btn-small"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:$('#LightForm').toggleClass('disableConfig');" class="btn btn-small btn-red"><span>Cancel</span></a>&nbsp;&nbsp;<span id="loadingImgLight" style="display:none; vertical-align: middle;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span></td></tr>
				</tbody>
			</table>
		</div>
	</div>
	</div>
</div>    
</div>
</section>
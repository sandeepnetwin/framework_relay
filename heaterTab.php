<script>
function checkHeaterAssign(heater)
{
	var numberOfHeater	=	$("#automatic_heaters_question1").val();
	if(numberOfHeater == '' || numberOfHeater == '0')
	{
		alert("Please select Heater number first!");
		return false;
	}
	
	if(!$("#lableRelayHeater-"+heater).hasClass('checked'))
	{
		$("#lableRelayHeater-"+heater).addClass('checked');
	}
	else
	{
		$("#lableRelayHeater-"+heater).removeClass('checked');
	}
	
	var arrHeaterAssignNumber	= 	Array();
	
	$(".heaterAssign").each(function(){
		var heaterNumber = $(this).val();
		if($("#lableRelayHeater-"+heaterNumber).hasClass('checked'))
		{	
			arrHeaterAssignNumber.push(heaterNumber);
		}
	});
	
	if(arrHeaterAssignNumber.length != numberOfHeater && arrHeaterAssignNumber.length != 0)
	{
		if(arrHeaterAssignNumber.length > numberOfHeater)
			$("#lableRelayHeater-"+heater).removeClass('checked');
		
		alert("Please assign "+numberOfHeater+" Heater!");
		return false;
	}
}
</script>
<h3>Heater Setting</h3>
<section>
<div class="col-sm-12">
	<label for="automatic_heaters_question1">How many heaters do you have?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Please select number of Heater used in the mode?" /></label><a class="changeLink" id="changeLinkHeater" href="javascript:void(0);" onclick="javascript:$('#heaterForm').toggleClass('disableConfig');" style="float:right;" title="Click here to Enable/Disable Heater related settings!">Enable/Disable Heater Configuration</a>
	<select name="automatic_heaters_question1" id="automatic_heaters_question1" class="form-control required" onchange="showHeater();">
	<option <?php if(isset($arrHeater['heater']) &&  $arrHeater['heater'] == ''){echo 'selected="selected"';}?> value="">Select number of Heater</option>
	<?php
			for($i=0;$i<($extra['HeaterNumber']+1);$i++)
			{
		?>
				<option <?php if(isset($arrHeater['heater']) &&  $arrHeater['heater'] ==  $i){echo 'selected="selected"';}?> value="<?php echo $i;?>"><?php echo $i;?></option>
		<?php
			}
		?>
	   </select>
</div>
<div style="height:10px">&nbsp;</div>
	<div class="col-sm-6">
		<table width="100%">
		<?php 
			for($i=1; $i<($extra['HeaterNumber']+1); $i++)
			{
		?>
				<tr id="trHeaterWork<?php echo $i;?>" style="display:<?php if(isset($arrHeater['heater']) &&  $arrHeater['heater'] >= $i){echo '';}else{echo 'none';}?>;">
					<td>
						<label for="Heater<?php echo $i;?>">Heater <?php echo $i;?>?<span class="requiredMark">*</span></label>
						<select name="Heater<?php echo $i;?>" id="Heater<?php echo $i;?>" class="form-control">
								<option <?php if(isset($arrHeater['Heater'.$i]) &&  $arrHeater['Heater'.$i] == 'Pool spa Combo'){echo 'selected="selected"';}?> value="Pool spa Combo">Heat pool and spa</option>
								<option <?php if(isset($arrHeater['Heater'.$i]) &&  $arrHeater['Heater'.$i] == 'Pool Only'){echo 'selected="selected"';}?> value="Pool Only">Heat pool</option>
								<option <?php if(isset($arrHeater['Heater'.$i]) &&  $arrHeater['Heater'.$i] == 'Spa Only'){echo 'selected="selected"';}?> value="Spa Only">Heat spa</option>
								<option <?php if(isset($arrHeater['Heater'.$i]) &&  $arrHeater['Heater'.$i] == 'Heat off'){echo 'selected="selected"';}?> value="Heat off">Heat off</option>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
		<?php } ?>	
		</table>
	</div>

	<div class="col-sm-6">
		<table width="100%">
		<?php 
			for($i=1; $i<($extra['HeaterNumber']+1); $i++)
			{
		?>
			<tr id="trHeaterPump<?php echo $i;?>" style="display:<?php if(isset($arrHeater['heater']) &&  $arrHeater['heater'] >= $i){echo '';}else{echo 'none';}?>;">
				<td>
					<label for="HeaterPump<?php echo $i;?>">Which Pump used in order to operate Heater <?php echo $i;?>?<span class="requiredMark">*</span></label>
					<select name="HeaterPump<?php echo $i;?>" id="HeaterPump<?php echo $i;?>" class="form-control">
						<?php
							for($j=0; $j<$extra['PumpsNumber']; $j++)
							{
						?>
							<option <?php if(isset($arrHeater['HeaterPump'.$i]) &&  $arrHeater['HeaterPump'.$i] == $j){echo 'selected="selected"';}?> value="<?php echo $j;?>">Pump <?php echo ($j+1);?></option>
						<?php 
							}
						?>
					</select>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
		<?php } ?>
		
		</table>
		<div style="height:10px">&nbsp;</div>
	</div>
	
	<div class="col-sm-12">
			<div class="col-sm-4" style="padding-left:0px;">
				<div class="controls boxed green-line" style="min-height:210px;">
					<div class="inner">
						<h3 class="profile-title"><strong style="color:#C9376E;">Assign Heater</strong></h3>
						<div id="contentsHeater">
						<?php
							for($i=0;$i<$extra['HeaterNumber'];$i++)
							{
								$strHeaterName 		=	'Heater '.($i+1);	
								$strHeaterNameTmp 	=	$this->home_model->getDeviceName($i,'H');
								if($strHeaterNameTmp != '')
									$strHeaterName	.=	' ('.$strHeaterNameTmp.')';
							?>
								<?php if($i != 0){ echo '<hr />'; }?>
								<div class="rowCheckbox switch">
									<div style="margin-bottom:10px;"><?php echo $strHeaterName;?></div>
									<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="relayHeater-<?php echo $i?>" name="relayHeater[]" hidefocus="true" style="outline: medium none;" class="heaterAssign" onclick="checkHeaterAssign(this.value)">
									<label id="lableRelayHeater-<?php echo $i?>" for="relayHeater-<?php echo $i?>"><span style="color:#C9376E;">&nbsp;</span></label>
									</div>
								</div>
									
							<?php 	
								}
							?>	
						</div>
					</div>
				</div>	
			</div>
			
			<div class="col-sm-8">
			<div id="heaterForm" class="disableConfig">	
		<div style="margin-bottom:10px;"><h3 class="confHeader">Heater Configuration</h3></div>
		<table class="table removeBorder" id="heaterTable" width="100%" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td style="width:48%;">Enter Number of Heater to Use in system:</td>
					<td style="width:2%;">&nbsp;</td>
					<td style="width:50%;"><input type="text" name="heaterNumber" id="heaterNumber" onkeyup="showHeaterDetails();" value="<?php if(isset($extra['HeaterNumber']) && $extra['HeaterNumber'] != 0) { echo $extra['HeaterNumber'];}?>" class="form-control inputText"></td>
				</tr>
				<?php for($i=1;$i<9;$i++)
					 {
						$strHeaterName	=   'Heater '.$i;
															
						$sRelayType     =   '';
						$sRelayNumber   =   '';
						
						$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails(($i-1));
						if(!empty($aHeaterDetails))
						{
							foreach($aHeaterDetails as $aHeater)
							$sRelayDetails  =   unserialize($aHeater->light_relay_number);

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
						}
						$sRelayNameDb =  $this->home_model->getDeviceName(($i-1),'H');
				?>
						<tr id="heaterDetails_<?php echo $i;?>" style="display:<?php if(isset($extra['HeaterNumber']) && $i <= $extra['HeaterNumber']) { echo ''; } else { echo 'none;';}?>">
							<td colspan="3">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tr><td colspan="3"><strong>Heater <?php echo $i;?></strong></td></tr>
                                                                        <tr>
										<td width="48%">Enter Name for Heater <?php echo $i;?>?</td>
										<td width="2%">&nbsp;</td>
										<td width="50%">
										<input type="text" id="heaterName<?php echo $i;?>" name="heaterName<?php echo $i;?>" class="form-control inputText" value="<?php echo $sRelayNameDb;?>">
										</td>
										</tr>
										<tr><td colspan="3">&nbsp;</td></tr>
									<tr>
									<td width="48%">How do you turn on your Heater <?php echo $i;?>?</td>
									<td width="2%">&nbsp;</td>
									<td width="50%">
                                    <select onchange="showRelays(this.value,'<?php echo $i;?>');" class="form-control valid" id="heater<?php echo $i;?>_equiment" name="heater<?php echo $i;?>_equiment">
                                    <option value="24" <?php if($sRelayType == '24'){ echo 'selected="selected;"';} ?>>24V AC Relays</option>                                                        <option value="12" <?php if($sRelayType == '12'){ echo 'selected="selected;"';} ?>>12V DC Relays</option>
                                    </select>
									</td>
									</tr>
									<tr><td colspan="3">&nbsp;</td></tr>
									<tr id="trHeaterSub<?php echo $i;?>_24" style="display:<?php if($sRelayType == '12') { echo 'none';} ?>">
									<td width="48%"><label for="heater<?php echo $i;?>_sub_equiment_24">Select 24V Relay<span class="requiredMark">*</span></label></td>
									<td width="2%">&nbsp;</td>
									<td width="50%">
									<select name="heater<?php echo $i;?>_sub_equiment_24" id="heater<?php echo $i;?>_sub_equiment_24" class="form-control">
										<option value="">Select Relay</option>
										<?php foreach($sRelays as $relay) {
                                              $strSelect	=	'';
                                              if($relay == $sRelayNumber)
                                              $strSelect	=	'selected="selected"';
										?>
											<option <?php echo $strSelect;?> value="<?php echo $relay;?>">Relay <?php echo $relay;?></option>
										<?php } ?>
									</select>
									</td>
									</tr>
				
									<tr id="trHeaterSub<?php echo $i;?>_12" style="display:<?php if($sRelayType == '24' || $sRelayType == '') { echo 'none';} ?>">
									<td width="48%"><label for="heater<?php echo $i;?>_sub_equiment_12">Select 12V Relay<span class="requiredMark">*</span></label></td>
									<td width="2%">&nbsp;</td>
									<td width="50%">
										<select name="heater<?php echo $i;?>_sub_equiment_12" id="heater<?php echo $i;?>_sub_equiment_12" class="form-control">
											<option value="">Select PowerCenter</option>
											<?php foreach($sPowercenter as $relay) {
                                                  $strSelect	=	'';
                                                  if($relay == $sRelayNumber)
                                                  $strSelect	=	'selected="selected"';
											?>
											<option <?php echo $strSelect;?> value="<?php echo $relay;?>">PowerCenter <?php echo $relay;?></option>
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
				<tr id="heaterSaveConf" style="display:<?php if($extra['HeaterNumber'] == 0){ echo 'none;';}?>"><td colspan="3"><a href="javascript:void(0);" onclick="checkAndSaveHeater();" class="btn btn-middle"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:$('#heaterForm').toggleClass('disableConfig');" class="btn btn-middle btn-red"><span>Cancel</span></a>&nbsp;&nbsp;<span id="loadingImgHeater" style="display:none; vertical-align: middle;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span></td></tr>
			</tbody>
		</table>
            </div>
	</div>
    </div>
</section>
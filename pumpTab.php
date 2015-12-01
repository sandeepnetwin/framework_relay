<script>
function checkPumpAssign(pump)
{
	var numberOfPump	=	$("#automatic_pumps").val();
	if(numberOfPump == '' || numberOfPump == '0')
	{
		alert("Please select Pump number first!");
		return false;
	}
	
	if(!$("#lableRelayPump-"+pump).hasClass('checked'))
	{
		$("#lableRelayPump-"+pump).addClass('checked');
	}
	else
	{
		$("#lableRelayPump-"+pump).removeClass('checked');
	}
	
	var arrPumpAssignNumber	= 	Array();
	
	$(".pumpAssign").each(function(){
		var pumpNumber = $(this).val();
		if($("#lableRelayPump-"+pumpNumber).hasClass('checked'))
		{	
			arrPumpAssignNumber.push(pumpNumber);
		}
	});
	
	if(arrPumpAssignNumber.length != numberOfPump && arrPumpAssignNumber.length != 0)
	{
		if(arrPumpAssignNumber.length > numberOfPump)
			$("#lableRelayPump-"+pump).removeClass('checked');
		
		alert("Please assign "+numberOfPump+" Pump!");
		return false;
	}
}
</script>

<h3>Pump Setting</h3>
<section style="float: none;padding: 2.5%;position: relative;">
	<div class="col-sm-12">
	<label for="automatic_pumps">How many Pumps do you have?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Please select number of pumps used in the mode?" /></label>
	<a class="changeLink" id="changeLinkPump" href="javascript:void(0);" onclick="javascript:$('#pumpForm').toggleClass('disableConfig');" style="float:right;" title="Click here to Enable/Disable pump related settings!">Enable/Disable Pump Configuration</a>
	
	<select name="automatic_pumps" id="automatic_pumps" class="form-control required" onchange="pumpChange();">
		<option <?php if(isset($arrDevice['pump']) && $arrDevice['pump'] == ''){ echo 'selected="selected"';} ?>  value="">Select Number of Pump</option>
		<option <?php if(isset($arrDevice['pump']) && $arrDevice['pump'] == '0'){ echo 'selected="selected"';} ?>  value="0">0</option>
		<?php for($i=1;$i<=$extra['PumpsNumber'];$i++){?>
		<option <?php if(isset($arrDevice['pump']) && $arrDevice['pump'] == $i){ echo 'selected="selected"';} ?>  value="<?php echo $i;?>"><?php echo $i;?></option>
		<?php } ?>
	</select>
		
	<div style="height:10px">&nbsp;</div>
	<table width="100%;">
	<?php for($i=1;$i<=$extra['PumpsNumber'];$i++){?>
	<tr id="trPump<?php echo $i;?>" style="display:<?php if(isset($arrDevice['pump']) && $arrDevice['pump'] >= $i){ echo '';} else {echo 'none;';} ?>">
		<td>
			<label for="strValve">Pump<?php echo $i;?><span class="requiredMark">*</span></label>
			<select name="Pump<?php echo $i;?>" id="Pump<?php echo $i;?>" class="form-control required">
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == ''){ echo 'selected="selected"';} ?> value="">--Select pump function--</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'Filtering Pool and Spa'){ echo 'selected="selected"';} ?> value="Filtering Pool and Spa">Filtering Pool and Spa</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'Filtering Spa Only'){ echo 'selected="selected"';} ?> value="Filtering Spa Only">Filtering Spa Only</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'Filtering Pool Only'){ echo 'selected="selected"';} ?> value="Filtering Pool Only">Filtering Pool Only</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'Spa Jets Only'){ echo 'selected="selected"';} ?> value="Spa Jets Only">Spa Jets Only</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'Spa Circulation and Heating'){ echo 'selected="selected"';} ?> value="Spa Circulation and Heating">Spa Circulation and Heating</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'Pool Circulation and Heating'){ echo 'selected="selected"';} ?> value="Pool Circulation and Heating">Pool Circulation and Heating</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'Pool and Spa Circulation and Heating'){ echo 'selected="selected"';} ?> value="Pool and Spa Circulation and Heating">Pool and Spa Circulation and Heating</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'waterfall'){ echo 'selected="selected"';} ?> value="waterfall">waterfall</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'solar heater'){ echo 'selected="selected"';} ?> value="solar heater">solar heater</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'waterslide'){ echo 'selected="selected"';} ?> value="waterslide">waterslide</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'water feature 1'){ echo 'selected="selected"';} ?> value="water feature 1">water feature 1</option>
			<option <?php if(isset($arrDevice['pump'.$i]) && $arrDevice['pump'.$i] == 'water feature 2'){ echo 'selected="selected"';} ?> value="water feature 2">water feature 2</option>
			</select>
		</td>
	</tr>
	<?php } ?>
	</table>

	<div style="height:10px">&nbsp;</div>

	<div class="col-sm-4" style="padding-left:0px;">
		<div class="controls boxed green-line" style="min-height:210px;">
			<div class="inner">
				<h3 class="profile-title"><strong style="color:#C9376E;">Assign Pump</strong></h3>
				<div id="contentsPump">
				<?php
					for($i=0;$i<$extra['PumpsNumber'];$i++)
					{
						$strPumpName 		=	'Pump '.($i+1);	
						$strPumpNameTmp 	=	$this->home_model->getDeviceName($i,'PS');
						if($strPumpNameTmp != '')
							$strPumpName	.=	' ('.$strPumpNameTmp.')';
							
				?>
					<?php if($i != 0){ echo '<hr />'; }?>
					<div class="rowCheckbox switch">
						<div style="margin-bottom:10px;"><?php echo $strPumpName;?></div>
						<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="relayPump-<?php echo $i?>" name="relayPumpchk[]" hidefocus="true" style="outline: medium none;" onclick="checkPumpAssign(this.value)" class="pumpAssign">
						<label id="lableRelayPump-<?php echo $i?>" for="relayPump-<?php echo $i?>"><span style="color:#C9376E;">&nbsp;</span></label>
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
		
		<div id="pumpForm" class="disableConfig">	
			<div style="margin-bottom:10px;"><h3 class="confHeader">Pump Configuration</h3></div>
			<table class="table removeBorder" id="pumpTable" width="100%" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td style="width:48%;">Enter Number of Pumps to Use in system:</td>
						<td style="width:2%;">&nbsp;</td>
						<td style="width:50%;"><input type="text" name="pumpNumber" id="pumpNumber" onkeyup="showPumpDetails();" value="<?php if(isset($extra['PumpsNumber']) && $extra['PumpsNumber'] != 0) { echo $extra['PumpsNumber'];}?>" class="form-control inputText"></td>
					</tr>
					<?php for($i=0;$i<8;$i++)
					{
						$sPumpDetails = $this->home_model->getPumpDetails($i);
						//Variable Initialization to blank.
						$sPumpNumber  	= '';
						$sPumpType  	= '';
						$sPumpSubType  	= '';
						$sPumpSpeed  	= '';
						$sPumpFlow 		= '';
						$sPumpClosure   = '';
						$sRelayNumber  	= '';
						$sPumpAddress	= '';
						$sRelayNumber1	= '';
						if(is_array($sPumpDetails) && !empty($sPumpDetails))
						{
						  foreach($sPumpDetails as $aResultEdit)
						  { 
							$sPumpNumber  = $aResultEdit->pump_number;
							$sPumpType    = $aResultEdit->pump_type;
							$sPumpSubType = $aResultEdit->pump_sub_type;
							$sPumpSpeed   = $aResultEdit->pump_speed;
							$sPumpFlow    = $aResultEdit->pump_flow;
							$sPumpClosure = $aResultEdit->pump_closure;
							$sRelayNumber = $aResultEdit->relay_number;
							$sPumpAddress = $aResultEdit->pump_address;
							$sRelayNumber1= $aResultEdit->relay_number_1;		
						  }
						}
					?>
					<tr id="trPumpDetails<?php echo $i;?>" style="display:<?php if(isset($extra['PumpsNumber']) && $i < $extra['PumpsNumber']) { echo ''; } else { echo 'none;';}?>">
					<td colspan="3">
					  <table border="0" cellspacing="0" cellpadding="0" width="100%">
						  <tr id="trVSClosure">
							<td width="10%"><strong>Pump Closure:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%">
								<div class="rowCustom">
								<div class="colCustom" style="padding-left:0;">
								  <input type="radio" name="sPumpClosure_<?php echo $i;?>" id="sPumpClosure0_<?php echo $i;?>" value="0" <?php if($sPumpClosure == '0') { echo 'checked="checked"'; } ?> required <?php if($sAccess == 1){ echo 'disabled="disabled";';}	?> style="display: inline;" /><lable style="margin-left: 5px;">No contact closure</lable>
								  <input type="radio" name="sPumpClosure_<?php echo $i;?>" id="sPumpClosure1_<?php echo $i;?>" value="1" <?php if($sPumpClosure == '1') { echo 'checked="checked"'; } ?> required <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;" /><lable style="margin-left: 5px;">Contact closure 1</lable>
							   </div>
								</div>
							</td>
						  </tr>
						  <tr><td colspan="3">&nbsp;</td></tr>
						  <tr>
							<td width="10%"><strong>Pump Number:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%"><input type="text" placeholder="Enter Pump Number" name="sPumpNumber_<?php echo $i;?>" class="inputText" value="<?php echo $i;?>" id="sPumpNumber_<?php echo $i;?>" required <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>></td>
						  </tr>
						  <tr><td colspan="3">&nbsp;</td></tr>

						  <tr>
							<td width="10%"><strong>Pump Type:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%">
							<select name="sPumpType_<?php echo $i;?>" id="sPumpType_<?php echo $i;?>" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> onchange="showDetailsPump(this.value,'<?php echo $i;?>');" class="form-control" style="width:50%">
							<option value="12" <?php if($sPumpType == '12'){echo 'selected="selected";';}?> >12V DC</option>
							<option value="24" <?php if($sPumpType == '24'){echo 'selected="selected";';}?>>24V AC</option>
							<option value="2Speed" <?php if($sPumpType == '2Speed'){echo 'selected="selected";';}?>>2 Speed</option>
							<option value="Intellicom" <?php if($sPumpType == 'Intellicom'){echo 'selected="selected";';}?>>Intellicom for a Pentair VS or VF Pump</option>
							<option value="Intellicom12" <?php if($sPumpType == 'Intellicom12'){echo 'selected="selected";';}?>>Intellicom for a Pentair VS or VF Pump 12V DC</option>
							<option value="Intellicom24" <?php if($sPumpType == 'Intellicom24'){echo 'selected="selected";';}?>>Intellicom for a Pentair VS or VF Pump 24V AC</option>
							<option value="Emulator" <?php if($sPumpType == 'Emulator'){echo 'selected="selected";';}?>>Emulator Pentair VS or VF Pump</option>
							<option value="Emulator12" <?php if($sPumpType == 'Emulator12'){echo 'selected="selected";';}?>>Emulator Pentair VS or VF Pump 12V DC</option>
							<option value="Emulator24" <?php if($sPumpType == 'Emulator24'){echo 'selected="selected";';}?>>Emulator Pentair VS or VF Pump 24V AC</option>
							</select>
							</td>
						  </tr>
						  
						  <tr id="pumpSubType2SpeedTrBlk_<?php echo $i;?>" style="display:<?php if($sPumpType == '2Speed'){echo '';}else{echo 'none;';} ?>"><td colspan="3">&nbsp;</td></tr>
						  <tr id="pumpSubType2SpeedTr_<?php echo $i;?>" style="display:<?php if($sPumpType == '2Speed'){echo '';}else{echo 'none;';} ?>">
							<td width="10%"><strong>Select Relay:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%">
								<select name="sPumpSubType1_<?php echo $i;?>" id="sPumpSubType1_<?php echo $i;?>" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> class="form-control" style="width:50%">
								<option value="12" <?php if($sPumpSubType == '12'){echo 'selected="selected";';}?>>12V DC</option>
								<option value="24" <?php if($sPumpSubType == '24'){echo 'selected="selected";';}?>>24V AC</option>
								</select>
							</td>
						  </tr>
						  
						  <tr id="pumpSubTypeTrBlk_<?php echo $i;?>" style="display:<?php if(preg_match('/Emulator/',$sPumpType)){echo '';}else{echo 'none;';} ?>"><td colspan="3">&nbsp;</td></tr>
						  <tr id="pumpSubTypeTr_<?php echo $i;?>" style="display:<?php if(preg_match('/Emulator/',$sPumpType)){echo '';}else{ echo'none;';} ?>;">
							<td width="10%"><strong>Pump Sub Type:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%">
								<select name="sPumpSubType_<?php echo $i;?>" id="sPumpSubType_<?php echo $i;?>" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> class="form-control" style="width:50%" onchange="subTypeDetails(this.value,'<?php  echo $i;?>')">
								<option value="VS" <?php if($sPumpSubType == 'VS'){echo 'selected="selected";';}?>>VS Pump (Variable Speed)</option>
								<option value="VF" <?php if($sPumpSubType == 'VF'){echo 'selected="selected";';}?>>VF Pump (Variable Flow)</option>
								</select>
							</td>
						  </tr>
						  
						  <tr id="trVSSpaceIntellicom_<?php echo $i;?>" style="display:<?php if(preg_match('/Intellicom/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
						  <tr id="trVSIntellicom_<?php echo $i;?>" style="display:<?php if(preg_match('/Intellicom/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;">
							<td width="10%"><strong>Pump Speed:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%">
								<div class="rowCustom">
								  <input type="radio" name="sPumpSpeedIn_<?php echo $i;?>" id="sPumpSpeedIn0_<?php echo $i;?>" value="0" <?php if($sPumpSpeed == '0') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">0</lable>
								  <input type="radio" name="sPumpSpeedIn_<?php echo $i;?>" id="sPumpSpeedIn1_<?php echo $i;?>" value="1" <?php if($sPumpSpeed == '1') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">1</lable>
								  <input type="radio" name="sPumpSpeedIn_<?php echo $i;?>" id="sPumpSpeedIn2_<?php echo $i;?>" value="2" <?php if($sPumpSpeed == '2') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">2</lable>
								  <input type="radio" name="sPumpSpeedIn_<?php echo $i;?>" id="sPumpSpeedIn3_<?php echo $i;?>" value="3" <?php if($sPumpSpeed == '3') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">3</lable>
								  <input type="radio" name="sPumpSpeedIn_<?php echo $i;?>" id="sPumpSpeedIn4_<?php echo $i;?>" value="4" <?php if($sPumpSpeed == '4') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">4</lable>
							   </div>
							</td>
						  </tr>
						  
						  <tr id="trVSSpace_<?php echo $i;?>" style="display:<?php if($sPumpSubType =='VS' && preg_match('/Emulator/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
						  <tr id="trVS_<?php echo $i;?>" style="display:<?php if($sPumpSubType =='VS' && preg_match('/Emulator/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;">
							<td width="10%"><strong>Pump Speed:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%">
								<div class="rowCustom">
								<div class="colCustom" style="padding-left:0;">
								  <input type="radio" name="sPumpSpeed_<?php echo $i;?>" id="sPumpSpeed0_<?php echo $i;?>" value="0" <?php if($sPumpSpeed == '0') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">0</lable>
								  <input type="radio" name="sPumpSpeed_<?php echo $i;?>" id="sPumpSpeed1_<?php echo $i;?>" value="1" <?php if($sPumpSpeed == '1') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">1</lable>
								  <input type="radio" name="sPumpSpeed_<?php echo $i;?>" id="sPumpSpeed2_<?php echo $i;?>" value="2" <?php if($sPumpSpeed == '2') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">2</lable>
								  <input type="radio" name="sPumpSpeed_<?php echo $i;?>" id="sPumpSpeed3_<?php echo $i;?>" value="3" <?php if($sPumpSpeed == '3') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">3</lable>
								  <input type="radio" name="sPumpSpeed_<?php echo $i;?>" id="sPumpSpeed4_<?php echo $i;?>" value="4" <?php if($sPumpSpeed == '4') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?> style="display: inline;"><lable style="margin-left: 5px;">4</lable>
							   </div>
							 </div>
							</td>
						  </tr>
						  <tr id="trVFSpace_<?php echo $i;?>" style="display:<?php if($sPumpSubType =='VF' && $sPumpType == 'Emulator') { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
						  <tr id="trVF_<?php echo $i;?>" style="display:<?php if($sPumpSubType =='VF' && $sPumpType == 'Emulator') { echo ''; } else { echo 'none';} ?>;">
							<td width="10%"><strong>Pump Flow:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%"><input type="text" name="sPumpFlow_<?php echo $i;?>" id="sPumpFlow_<?php echo $i;?>" value="<?php echo $sPumpFlow;?>" class="inputText" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
							</td>
						  </tr>
						  <tr id="trRelayNumberSpace_<?php echo $i;?>" <?php if($sPumpType == 'Intellicom' || $sPumpType == 'Emulator') { echo 'style="display:none;"';} ?>><td colspan="3">&nbsp;</td></tr>
						  <tr id="trRelayNumber_<?php echo $i;?>" <?php if($sPumpType == 'Intellicom' || $sPumpType == 'Emulator') { echo 'style="display:none;"';} ?>>
							<td width="10%"><strong>Relay Number:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%"><input type="text" class="inputText" name="sRelayNumber_<?php echo $i;?>" id="sRelayNumber_<?php echo $i;?>" value="<?php echo $sRelayNumber;?>" <?php if($sPumpType != 'Intellicom' && $sPumpType != 'Emulator'  ) { echo 'required';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
							</td>
						  </tr>
						  <tr id="trRelayNumber1Space_<?php echo $i;?>" <?php if($sPumpType == '2Speed') { echo '';} else { echo 'style="display:none;"';}?>><td colspan="3">&nbsp;</td></tr>
						  <tr id="trRelayNumber1_<?php echo $i;?>" <?php if($sPumpType == '2Speed') { echo '';} else { echo 'style="display:none;"';}?>>
							<td width="10%"><strong>Relay Number 2:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%"><input type="text" class="inputText" name="sRelayNumber1_<?php echo $i;?>" id="sRelayNumber1_<?php echo $i;?>" value="<?php echo $sRelayNumber1;?>" <?php if($sPumpType == '2Speed') { echo 'required';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
							</td>
						  </tr>
						  
						  <tr id="trAddressNumberSpace_<?php echo $i;?>" <?php if($sPumpType == '12' || $sPumpType == '24' || $sPumpType == '2Speed' || $sPumpType == '') { echo 'style="display:none;"';} ?>><td colspan="3">&nbsp;</td></tr>
						  <tr id="trAddressNumber_<?php echo $i;?>" <?php if($sPumpType == '12' || $sPumpType == '24' || $sPumpType == '2Speed' || $sPumpType == '') { echo 'style="display:none;"';} ?>>
							<td width="10%"><strong>Pump Address:</strong></td>
							<td width="1%">&nbsp;</td>
							<td width="89%"><input type="text" class="inputText" name="sPumpAddress_<?php echo $i;?>" id="sPumpAddress_<?php echo $i;?>" value="<?php echo $sPumpAddress;?>" <?php if($sPumpType != '12' && $sPumpType != '24' && $sPumpType != '2Speed') { echo 'required';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
							</td>
						  </tr>
						  <tr><td colspan="3"><hr style="border-color:#000;" /></td></tr>
						 </table>
						</td>
					</tr>
					
				<?php } ?>
				<tr id="pumpSaveConf" style="display:<?php if($extra['PumpsNumber'] == 0){ echo 'none;';}?>"><td colspan="3"><a href="javascript:void(0);" onclick="checkAndSavePumps();" class="btn btn-middle"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:$('#pumpForm').toggleClass('disableConfig');" class="btn btn-middle btn-red"><span>Cancel</span></a>&nbsp;&nbsp;<span id="loadingImgPump" style="display:none; vertical-align: middle;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span></td></tr>
				</tbody>
			</table>	
		</div>
			
	</div>
	</div>
</section>
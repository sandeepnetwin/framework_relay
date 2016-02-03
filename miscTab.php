<?php
	$iMiscCnt  = 0;
	if(!empty($aIPDetails))
	{
		foreach($aIPDetails as $aIP)
		{
			if($aIP->id <= 1)
				$iMiscCnt += $extra['MiscNumber'];
			else
				$iMiscCnt += $extra['MiscNumber2'];
			
			//First IP ID to show selected.
			if($iFirstIPId == '')
				$iFirstIPId = $aIP->id;
			
			$sDetails	=	$aIP->ip;
			if($aIP->name != '')
			{
				$sDetails .= ' ('.$aIP->name.')';
			}
			
			$sShow		=	'display:none';
			$sSelected	=	'';
			if($iFirstIPId == $aIP->id)
			{ 
				$sShow		=	'';
				$sSelected	=	'selected="selected"';
			} 
			
			$sIPOptions.='<option value="'.$aIP->id.'" '.$sSelected.'>'.$sDetails.'</option>';
		}
	}
?>
<script>
function checkMiscAssign(misc)
{
	<?php if($sAccess == '1') { ?>
		return false;
	<?php } else if($sAccess == '2') { ?>
	var numberOfMisc	=	$("#no_blower").val();
	if(numberOfMisc == '' || numberOfMisc == '0')
	{
		alert("Please select Miscellaneous Device number first!");
		return false;
	}
	
	if(!$("#lableRelayMisc-"+misc).hasClass('checked'))
	{
		$("#lableRelayMisc-"+misc).addClass('checked');
	}
	else
	{
		$("#lableRelayMisc-"+misc).removeClass('checked');
	}
	
	var arrMiscAssignNumber	= 	Array();
	
	$(".miscAssign").each(function(){
		var miscNumber = $(this).val();
		if($("#lableRelayMisc-"+miscNumber).hasClass('checked'))
		{	
			arrMiscAssignNumber.push(miscNumber);
		}
	});
	
	if(arrMiscAssignNumber.length != numberOfMisc && arrMiscAssignNumber.length != 0)
	{
		if(arrMiscAssignNumber.length > numberOfMisc)
			$("#lableRelayMisc-"+misc).removeClass('checked');
		
		alert("Please assign "+numberOfMisc+" Miscellaneous Device!");
		return false;
	}
	<?php } ?>
}
</script>
<h3>Miscellaneous Device</h3>
<section>
	<div class="col-sm-12">
		<!-- Misc Number -->
		<label for="no_blower">How many Spa/Pool miscellaneous devices do you have?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" alt="Help" class="top-right tipso_style_custom" data-tipso="SELECT NUMBER OF MISC DEVICES FROM TOTAL MISC DEVICES TO USE IN THE SELECTED MODE." /></label>
		
		<a class="changeLink" href="javascript:void(0);" <?php if($sAccess == '2') { ?>onclick="javascript:$('#MiscForm').toggleClass('disableConfig');" <?php } ?> style="float:right;" title="Click here to Enable/Disable miscellaneous related settings!">Enable/Disable miscellaneous Configuration</a>
		
		<select name="no_misc" id="no_misc" class="form-control" onchange="showMisc()" <?php if($sAccess == '1') { echo 'disabled="disabled"'; }?>>
			<option <?php if(isset($arrMore['misc']) &&  $arrMore['misc'] == ''){echo 'selected="selected"';}?> value="">Select number of Device</option>
			<?php
				for($i=0;$i<($iMiscCnt+1);$i++)
				{
			?>		
					<option <?php if(isset($arrMore['misc']) &&  $arrMore['misc'] == $i){echo 'selected="selected"';}?> value="<?php echo $i;?>"><?php echo $i;?></option>
			<?php
				}
			?>	
		</select>
		<!-- Misc Number -->
		
		<div style="height:10px">&nbsp;</div>
		
		<!-- Misc Assign -->
		<div class="col-sm-4" style="padding-left:0px;">
			<div class="controls boxed green-line" style="min-height:210px;">
				<div class="inner">
					<h3 class="profile-title"><strong style="color:#C9376E;">Assign Misc Devices</strong></h3>
					<?php
						if(!empty($aIPDetails))
						{
							foreach($aIPDetails as $aIP)
							{
					?>	
					<div id="contentsMisc_<?php echo $aIP->id;?>">
					<?php
						if($aIP->id <= 1)
							$iMiscCnt = $extra['MiscNumber'];
						else
							$iMiscCnt = $extra['MiscNumber2'];
						
						for($i=0;$i<$iMiscCnt;$i++)
						{
							$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($i,$aIP->id);
							
							if(empty($aMiscDetails))
							{
								continue;
							}
							
							$strBlowerName 		=	'Misc '.($i+1);	
							$strBlowerNameTmp 	=	$this->home_model->getDeviceName($i,'M',$aIP->id);
							if($strBlowerNameTmp != '')
								$strBlowerName	.=	' ('.$strBlowerNameTmp.')';
							
							$checked 	=	'';
							$clsChecked	=	'';
							$miscAssign	=	unserialize($arrMore['miscAssign']);	
							if(in_array($i,$miscAssign))
							{
								$checked 	=	'checked="checked"';
								$clsChecked	=	'class="checked"';
							}
					?>
						<?php if($i != 0){ echo '<hr />'; }?>
						<div class="rowCheckbox switch">
							<div style="margin-bottom:10px;"><?php echo $strBlowerName;?></div>
							<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i.'_'.$aIP->id;?>" id="relayMisc-<?php echo $i.'_'.$aIP->id;?>" name="relayMisc[]" hidefocus="true" style="outline: medium none;" onclick="checkMiscAssign(this.value)" class="miscAssign" <?php echo $checked;?>>
							<label <?php echo $clsChecked;?> id="lableRelayMisc-<?php echo $i.'_'.$aIP->id;?>" for="relayMisc-<?php echo $i.'_'.$aIP->id;?>"><span style="color:#C9376E;">&nbsp;</span></label>
							</div>
						</div>
							
					<?php 	
						}
					?>
					</div>
					<?php
							}
						}
					?>	
				</div>
			</div>
		</div>	
		<!-- Misc Assign -->
		
		<!-- Misc Configuration -->
		<div class="col-sm-8" style="padding-right:0px;">
			<div id="MiscForm" class="disableConfig">	
				<div style="margin-bottom:10px;"><h3 class="confHeader">Miscellaneous Configuration</h3></div>
				<div style="margin-bottom:10px;">
					<span style="font-weight:bold;">Select Board : </span>
					<select name="selPort" id="selPort" onchange="showBoardDetails(this.value,'miscTable_')">
						<option value="">--IP(Name)--</option>
						<?php echo $sIPOptions;?>
					</select>
				</div>
				<?php
					if(!empty($aIPDetails))
					{
						foreach($aIPDetails as $aIP)
						{
							if($aIP->id == 1)
								$miscNumber	=	$extra['MiscNumber'];
							else 
								$miscNumber	=	$extra['MiscNumber2'];
				?>
				<table class="table removeBorder" id="miscTable_<?php echo $aIP->id;?>" style="display:<?php if($aIP->id != $iFirstIPId){ echo 'none';} ?>" width="100%" cellspacing="0" cellpadding="0">
					<tbody>
						<tr>
							<td style="width:48%;">Enter Number of Miscellaneous devices to Use in system:</td>
							<td style="width:2%;">&nbsp;</td>
							<td style="width:50%;">
								<input type="text" name="miscNumber" id="miscNumber_<?php echo $aIP->id;?>" onkeyup="showMiscDetails('<?php echo $aIP->id;?>');" value="<?php if(isset($miscNumber) && $miscNumber != 0) { echo $miscNumber;}?>" class="form-control inputText" <?php if($sAccess == '1') { echo 'readonly="readonly"'; }?>>
							</td>
						</tr>
						<?php 
							  for($i=1;$i<9;$i++)
							  {
								$sRelayType     =   '';
								$sRelayNumber   =   '';
								
								$aMiscDetails  =   $this->home_model->getMiscDeviceDetails(($i-1),$aIP->id);
								
								if(!empty($aMiscDetails))
								{
									foreach($aMiscDetails as $aMisc)
									{
										$sRelayDetails  =   unserialize($aMisc->light_relay_number);
			
										$sRelayType     =   $sRelayDetails['sRelayType'];
										$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
									}
								}
								
								$sRelayNameDb =  $this->home_model->getDeviceName(($i-1),'M',$aIP->id);
								
						?>		
								<tr id="miscDetails_<?php echo $i.'_'.$aIP->id;?>" style="display:<?php if(isset($miscNumber) && $i <= $miscNumber) { echo ''; } else { echo 'none;';}?>">
									<td colspan="3">
										<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr><td colspan="3"><strong>Misc Device <?php echo $i;?></strong></td></tr>
											
											<tr>
												<td width="48%">Enter Name for Misc Device <?php echo $i;?>?</td>
												<td width="2%">&nbsp;</td>
												<td width="50%">
													<input type="text" id="miscName<?php echo $i.'_'.$aIP->id;?>" name="miscName<?php echo $i.'_'.$aIP->id;?>" class="form-control inputText" value="<?php echo $sRelayNameDb;?>" <?php if($sAccess == '1') { echo 'readonly="readonly"'; }?>>
												</td>
											</tr>
											
											<tr><td colspan="3">&nbsp;</td></tr>
											
											<tr>
												<td width="48%">How do you turn on your Misc Device <?php echo $i;?>?</td>
												<td width="2%">&nbsp;</td>
												<td width="50%">
													<select onchange="showRelaysMisc(this.value,'<?php echo $i.'_'.$aIP->id;?>');" class="form-control valid" id="misc<?php echo $i.'_'.$aIP->id;?>_equiment" name="misc<?php echo $i.'_'.$aIP->id;?>_equiment" <?php if($sAccess == '1') { echo 'disabled="disabled"'; }?>>
														<option value="24" <?php if($sRelayType == '24'){ echo 'selected="selected";';}?>>24V AC Relays</option>
														<option value="12" <?php if($sRelayType == '12'){ echo 'selected="selected";';}?>>12V DC Relays</option>
													</select>
												</td>
											</tr>
											
											<tr><td colspan="3">&nbsp;</td></tr>
											
											<tr id="trMiscSub<?php echo $i.'_'.$aIP->id;?>_24" style="display:<?php if($sRelayType == '12'){ echo 'none;';}?>">
												<td width="48%"><label for="misc<?php echo $i.'_'.$aIP->id;?>_sub_equiment_24">Select 24V Relay<span class="requiredMark">*</span></label></td>
												<td width="2%">&nbsp;</td>
												<td width="50%">
													<select name="misc<?php echo $i.'_'.$aIP->id;?>_sub_equiment_24" id="misc<?php echo $i.'_'.$aIP->id;?>_sub_equiment_24" class="form-control" <?php if($sAccess == '1') { echo 'disabled="disabled"'; }?>>
													<option value="">Select Relay</option>
													<?php foreach(${"sRelays".$aIP->id} as $relay) {
														  $strSelect ='';
														  if($relay == $sRelayNumber)
															$strSelect = "selected='selected'";
													?>
													<option value="<?php echo $relay;?>" <?php echo $strSelect; ?>>Relay <?php echo $relay;?></option>
													<?php } ?>
													</select>
												</td>
											</tr>
						
											<tr id="trMiscSub<?php echo $i.'_'.$aIP->id;?>_12" style="display:<?php if($sRelayType == '24' || $sRelayType == ''){ echo 'none;';}?>">
												<td width="48%"><label for="misc<?php echo $i.'_'.$aIP->id;?>_sub_equiment_12">Select 12V Relay<span class="requiredMark">*</span></label></td>
												<td width="2%">&nbsp;</td>
												<td width="50%">
													<select name="misc<?php echo $i.'_'.$aIP->id;?>_sub_equiment_12" id="misc<?php echo $i.'_'.$aIP->id;?>_sub_equiment_12" class="form-control" <?php if($sAccess == '1') { echo 'disabled="disabled"'; }?>>
														<option value="">Select PowerCenter</option>
														<?php foreach(${"sPowercenter".$aIP->id} as $relay) {
																$strSelect ='';
																if($relay == $sRelayNumber)
																	$strSelect= "selected='selected'";
														?>
														<option value="<?php echo $relay;?>" <?php echo $strSelect; ?>>PowerCenter <?php echo $relay;?></option>
														<?php } ?>
													</select>
												</td>
											</tr>
											
											<tr><td colspan="3"><hr style="border-color:#000;" /></td></tr>
										</table>
									</td>
								</tr>
						<?php 
							}
						?>	
						<?php if($sAccess == '2') { ?>
							<tr id="miscSaveConf<?php echo '_'.$aIP->id;?>" style="display:<?php if($extra['MiscNumber'] == 0){ echo 'none;';}?>">
								<td colspan="3" >
									<a href="javascript:void(0);" onclick="checkAndSaveMisc('<?php echo $aIP->id;?>');" class="btn btn-small"><span>Save</span></a>
									&nbsp;&nbsp;
									<a href="javascript:void(0);" onclick="javascript:$('#MiscForm').toggleClass('disableConfig');" class="btn btn-small btn-gray"><span>Cancel</span></a>
									&nbsp;&nbsp;
									<span id="loadingImgMisc_<?php echo $aIP->id;?>" style="display:none; vertical-align: middle;">
										<img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32">
									</span>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php	}
					}
				?>	
			</div>
		</div>
		<!-- Misc Configuration -->
	</div>	
</section>
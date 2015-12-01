<script>
function checkMiscAssign(misc)
{
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
}
</script>
<h3>Miscellaneous Device</h3>
<section>
<div class="col-sm-12">
<label for="no_blower">How many miscellaneous devices do you have?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Please select number of miscellaneous devices used in the mode?" /></label><a class="changeLink" href="javascript:void(0);" onclick="javascript:$('#MiscForm').toggleClass('disableConfig');" style="float:right;" title="Click here to Enable/Disable miscellaneous related settings!">Enable/Disable miscellaneous Configuration</a>
<select name="no_misc" id="no_misc" class="form-control" onchange="showMisc()">
	<option <?php if(isset($arrMore['misc']) &&  $arrMore['misc'] == ''){echo 'selected="selected"';}?> value="">Select number of Device</option>
	<?php
		for($i=0;$i<($extra['MiscNumber']+1);$i++)
		{
	?>		
			<option <?php if(isset($arrMore['misc']) &&  $arrMore['misc'] == $i){echo 'selected="selected"';}?> value="<?php echo $i;?>"><?php echo $i;?></option>
	<?php
		}
	?>	
</select>
<div style="height:10px">&nbsp;</div>
	<div class="col-sm-4" style="padding-left:0px;">
		<div class="controls boxed green-line" style="min-height:210px;">
			<div class="inner">
				<h3 class="profile-title"><strong style="color:#C9376E;">Assign Misc Devices</strong></h3>
				<div id="contentsMisc">
				<?php
					for($i=0;$i<$extra['MiscNumber'];$i++)
					{
						$strBlowerName 		=	'Misc '.($i+1);	
						$strBlowerNameTmp 	=	$this->home_model->getDeviceName($i,'M');
						if($strBlowerNameTmp != '')
							$strBlowerName	.=	' ('.$strBlowerNameTmp.')';
				?>
					<?php if($i != 0){ echo '<hr />'; }?>
					<div class="rowCheckbox switch">
						<div style="margin-bottom:10px;"><?php echo $strBlowerName;?></div>
						<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="relayMisc-<?php echo $i?>" name="relayMisc[]" hidefocus="true" style="outline: medium none;" onclick="checkMiscAssign(this.value)" class="miscAssign">
						<label id="lableRelayMisc-<?php echo $i?>" for="relayMisc-<?php echo $i?>"><span style="color:#C9376E;">&nbsp;</span></label>
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
	<div id="MiscForm" class="disableConfig">	
		<div style="margin-bottom:10px;"><h3 class="confHeader">Miscellaneous Configuration</h3></div>
		<table class="table removeBorder" id="miscTable" width="100%" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td style="width:48%;">Enter Number of Miscellaneous devices to Use in system:</td>
					<td style="width:2%;">&nbsp;</td>
					<td style="width:50%;"><input type="text" name="miscNumber" id="miscNumber" onkeyup="showMiscDetails();" value="<?php if(isset($extra['MiscNumber']) && $extra['MiscNumber'] != 0) { echo $extra['MiscNumber'];}?>" class="form-control inputText"></td>
				</tr>
				<?php 
					  for($i=1;$i<9;$i++)
					  {
						$sRelayType     =   '';
						$sRelayNumber   =   '';
						
						$aMiscDetails  =   $this->home_model->getMiscDeviceDetails(($i-1));
						
						if(!empty($aMiscDetails))
						{
							foreach($aMiscDetails as $aMisc)
							{
								$sRelayDetails  =   unserialize($aMisc->light_relay_number);
	
								$sRelayType     =   $sRelayDetails['sRelayType'];
								$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
							}
						}
						
						$sRelayNameDb =  $this->home_model->getDeviceName(($i-1),'M');
						
				?>		
						<tr id="miscDetails_<?php echo $i;?>" style="display:<?php if(isset($extra['MiscNumber']) && $i <= $extra['MiscNumber']) { echo ''; } else { echo 'none;';}?>">
							<td colspan="3">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tr><td colspan="3"><strong>Misc Device <?php echo $i;?></strong></td></tr>
									<tr>
									<tr>
										<td width="48%">Enter Name for Misc Device <?php echo $i;?>?</td>
										<td width="2%">&nbsp;</td>
										<td width="50%">
										<input type="text" id="miscName<?php echo $i;?>" name="miscName<?php echo $i;?>" class="form-control inputText" value="<?php echo $sRelayNameDb;?>">
										</td>
									</tr>
									<tr><td colspan="3">&nbsp;</td></tr>
									<td width="48%">How do you turn on your Misc Device <?php echo $i;?>?</td>
									<td width="2%">&nbsp;</td>
									<td width="50%">
										<select onchange="showRelaysMisc(this.value,'<?php echo $i;?>');" class="form-control valid" id="misc<?php echo $i;?>_equiment" name="misc<?php echo $i;?>_equiment">
											<option value="24" <?php if($sRelayType == '24'){ echo 'selected="selected";';}?>>24V AC Relays</option>
											<option value="12" <?php if($sRelayType == '12'){ echo 'selected="selected";';}?>>12V DC Relays</option>
										</select>
									</td>
									</tr>
									<tr><td colspan="3">&nbsp;</td></tr>
									<tr id="trMiscSub<?php echo $i;?>_24" style="display:<?php if($sRelayType == '12'){ echo 'none;';}?>">
									<td width="48%"><label for="misc<?php echo $i;?>_sub_equiment_24">Select 24V Relay<span class="requiredMark">*</span></label></td>
									<td width="2%">&nbsp;</td>
									<td width="50%">
									<select name="misc<?php echo $i;?>_sub_equiment_24" id="misc<?php echo $i;?>_sub_equiment_24" class="form-control">
										<option value="">Select Relay</option>
										<?php foreach($sRelays as $relay) {
											  $strSelect ='';
											  if($relay == $sRelayNumber)
												$strSelect = "selected='selected'";
										?>
											<option value="<?php echo $relay;?>" <?php echo $strSelect; ?>>Relay <?php echo $relay;?></option>
										<?php } ?>
									</select>
									</td>
									</tr>
				
									<tr id="trMiscSub<?php echo $i;?>_12" style="display:<?php if($sRelayType == '24' || $sRelayType == ''){ echo 'none;';}?>">
									<td width="48%"><label for="misc<?php echo $i;?>_sub_equiment_12">Select 12V Relay<span class="requiredMark">*</span></label></td>
									<td width="2%">&nbsp;</td>
									<td width="50%">
										<select name="misc<?php echo $i;?>_sub_equiment_12" id="misc<?php echo $i;?>_sub_equiment_12" class="form-control">
											<option value="">Select PowerCenter</option>
											<?php foreach($sPowercenter as $relay) {
													$strSelect ='';
													if($relay == $sRelayNumber)
														$strSelect= "selected='selected'";
											?>
											<option value="<?php echo $relay;?>" <?php echo $strSelect; ?>>PowerCenter <?php echo $relay;?></option>
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
				<tr id="miscSaveConf" style="display:<?php if($extra['MiscNumber'] == 0){ echo 'none;';}?>"><td colspan="3" ><a href="javascript:void(0);" onclick="checkAndSaveMisc();" class="btn btn-small"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:$('#MiscForm').toggleClass('disableConfig');" class="btn btn-small btn-red"><span>Cancel</span></a>&nbsp;&nbsp;<span id="loadingImgMisc" style="display:none; vertical-align: middle;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span></td></tr>
			</tbody>
		</table>
	</div>
</div>
</div>	
</section>
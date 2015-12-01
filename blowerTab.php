<script>
function checkBlowerAssign(blower)
{
	var numberOfBlower	=	$("#no_blower").val();
	if(numberOfBlower == '' || numberOfBlower == '0')
	{
		alert("Please select Blower number first!");
		return false;
	}
	
	if(!$("#lableRelayBlower-"+blower).hasClass('checked'))
	{
		$("#lableRelayBlower-"+blower).addClass('checked');
	}
	else
	{
		$("#lableRelayBlower-"+blower).removeClass('checked');
	}
	
	var arrBlowerAssignNumber	= 	Array();
	
	$(".blowerAssign").each(function(){
		var blowerNumber = $(this).val();
		if($("#lableRelayBlower-"+blowerNumber).hasClass('checked'))
		{	
			arrBlowerAssignNumber.push(blowerNumber);
		}
	});
	
	if(arrBlowerAssignNumber.length != numberOfBlower && arrBlowerAssignNumber.length != 0)
	{
		if(arrBlowerAssignNumber.length > numberOfBlower)
			$("#lableRelayBlower-"+blower).removeClass('checked');
		
		alert("Please assign "+numberOfBlower+" Blower!");
		return false;
	}
}
</script>
<h3>Blower Setting</h3>
            <section>
			<div class="col-sm-12">
			<label for="no_blower">How many Spa/Pool Blowers do you have?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Please select number of blowers used in the mode?" /></label><a class="changeLink" id="changeLinkLight" href="javascript:void(0);" onclick="javascript:$('#BlowerForm').toggleClass('disableConfig');" style="float:right;" title="Click here to Enable/Disable blower related settings!">Enable/Disable Blower Configuration</a>
			<select name="no_blower" id="no_blower" class="form-control" onchange="showBlower();">
				<option <?php if(isset($arrMore['blower']) &&  $arrMore['blower'] == ''){echo 'selected="selected"';}?> value="">Select number of blowers</option>
				<?php
					for($i=0;$i<($extra['BlowerNumber']+1);$i++)
					{
				?>		
						<option <?php if(isset($arrMore['blower']) &&  $arrMore['blower'] == $i){echo 'selected="selected"';}?> value="<?php echo $i;?>"><?php echo $i;?></option>
				<?php
					}
				?>	
			</select>
			<div style="height:10px">&nbsp;</div>
				<div class="col-sm-4" style="padding-left:0px;">
					<div class="controls boxed green-line" style="min-height:210px;">
						<div class="inner">
							<h3 class="profile-title"><strong style="color:#C9376E;">Assign Blower</strong></h3>
							<div id="contentsBlower">
							<?php
								for($i=0;$i<$extra['BlowerNumber'];$i++)
								{
									$strBlowerName 		=	'Blower '.($i+1);	
									$strBlowerNameTmp 	=	$this->home_model->getDeviceName($i,'B');
									if($strBlowerNameTmp != '')
										$strBlowerName	.=	' ('.$strBlowerNameTmp.')';
							?>
								<?php if($i != 0){ echo '<hr />'; }?>
								<div class="rowCheckbox switch">
									<div style="margin-bottom:10px;"><?php echo $strBlowerName;?></div>
									<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" id="relayBlower-<?php echo $i?>" name="relayBlower[]" hidefocus="true" style="outline: medium none;" onclick="checkBlowerAssign()" class="blowerAssign">
									<label id="lableRelayBlower-<?php echo $i?>" for="relayBlower-<?php echo $i?>"><span style="color:#C9376E;">&nbsp;</span></label>
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
				<div id="BlowerForm" class="disableConfig">	
					<div style="margin-bottom:10px;"><h3 class="confHeader">Blower Configuration</h3></div>
					<table class="table removeBorder" id="blowerTable" width="100%" cellspacing="0" cellpadding="0">
						<tbody>
							<tr>
								<td style="width:48%;">Enter Number of Blowers to Use in system:</td>
								<td style="width:2%;">&nbsp;</td>
								<td style="width:50%;"><input type="text" name="blowerNumber" id="blowerNumber" onkeyup="showBlowerDetails();" value="<?php if(isset($extra['BlowerNumber']) && $extra['BlowerNumber'] != 0) { echo $extra['BlowerNumber'];}?>" class="form-control inputText"></td>
							</tr>
							<?php 
								  for($i=1;$i<9;$i++)
								  {
									$sRelayType     =   '';
									$sRelayNumber   =   '';
									
									$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails(($i-1));
									
									if(!empty($aBlowerDetails))
									{
										foreach($aBlowerDetails as $aBlower)
										{
											$sRelayDetails  =   unserialize($aBlower->light_relay_number);
				
											$sRelayType     =   $sRelayDetails['sRelayType'];
											$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
										}
									}
									
									$sRelayNameDb =  $this->home_model->getDeviceName(($i-1),'B');
									
							?>		
									<tr id="blowerDetails_<?php echo $i;?>" style="display:<?php if(isset($extra['BlowerNumber']) && $i <= $extra['BlowerNumber']) { echo ''; } else { echo 'none;';}?>">
										<td colspan="3">
											<table border="0" cellspacing="0" cellpadding="0" width="100%">
												<tr><td colspan="3"><strong>Blower <?php echo $i;?></strong></td></tr>
												<tr>
												<tr>
													<td width="48%">Enter Name for Blower <?php echo $i;?>?</td>
													<td width="2%">&nbsp;</td>
													<td width="50%">
													<input type="text" id="blowerName<?php echo $i;?>" name="blowerName<?php echo $i;?>" class="form-control inputText" value="<?php echo $sRelayNameDb;?>">
													</td>
												</tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<td width="48%">How do you turn on your Blower <?php echo $i;?>?</td>
												<td width="2%">&nbsp;</td>
												<td width="50%">
													<select onchange="showRelaysBlower(this.value,'<?php echo $i;?>');" class="form-control valid" id="blower<?php echo $i;?>_equiment" name="blower<?php echo $i;?>_equiment">
														<option value="24" <?php if($sRelayType == '24'){ echo 'selected="selected";';}?>>24V AC Relays</option>
														<option value="12" <?php if($sRelayType == '12'){ echo 'selected="selected";';}?>>12V DC Relays</option>
													</select>
												</td>
												</tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr id="trBlowerSub<?php echo $i;?>_24" style="display:<?php if($sRelayType == '12'){ echo 'none;';}?>">
												<td width="48%"><label for="blower<?php echo $i;?>_sub_equiment_24">Select 24V Relay<span class="requiredMark">*</span></label></td>
												<td width="2%">&nbsp;</td>
												<td width="50%">
												<select name="blower<?php echo $i;?>_sub_equiment_24" id="blower<?php echo $i;?>_sub_equiment_24" class="form-control">
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
							
												<tr id="trBlowerSub<?php echo $i;?>_12" style="display:<?php if($sRelayType == '24' || $sRelayType == ''){ echo 'none;';}?>">
												<td width="48%"><label for="blower<?php echo $i;?>_sub_equiment_12">Select 12V Relay<span class="requiredMark">*</span></label></td>
												<td width="2%">&nbsp;</td>
												<td width="50%">
													<select name="blower<?php echo $i;?>_sub_equiment_12" id="blower<?php echo $i;?>_sub_equiment_12" class="form-control">
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
							<tr id="blowerSaveConf" style="display:<?php if($extra['BlowerNumber'] == 0){ echo 'none;';}?>"><td colspan="3" ><a href="javascript:void(0);" onclick="checkAndSaveBlower();" class="btn btn-small"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:$('#BlowerForm').toggleClass('disableConfig');" class="btn btn-small btn-red"><span>Cancel</span></a>&nbsp;&nbsp;<span id="loadingImgBlower" style="display:none; vertical-align: middle;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span></td></tr>
						</tbody>
					</table>
				</div>
			</div>
			</div>	
            </section>
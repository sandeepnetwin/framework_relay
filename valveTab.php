<script>
function checkValveAssign(valve)
{
	var numberOfValve	=	$("#strValve").val();
	if(numberOfValve == '' || numberOfValve == '0')
	{
		alert("Please select Valve number first!");
		return false;
	}
	
	if(!$("#lableRelayValve-"+valve).hasClass('checked'))
	{
		$("#lableRelayValve-"+valve).addClass('checked');
	}
	else
	{
		$("#lableRelayValve-"+valve).removeClass('checked');
	}
	
	var arrValveAssignNumber	= 	Array();
	
	$(".valveAssign").each(function(){
		var valveNumber = $(this).val();
		if($("#lableRelayValve-"+valveNumber).hasClass('checked'))
		{	
			arrValveAssignNumber.push(valveNumber);
		}
	});
	
	if(arrValveAssignNumber.length != numberOfValve && arrValveAssignNumber.length != 0)
	{
		if(arrValveAssignNumber.length > numberOfValve)
			$("#lableRelayValve-"+valve).removeClass('checked');
		
		alert("Please assign "+numberOfValve+" Valve!");
		return false;
	}
}
</script>
<h3>Valve Setting</h3>
	<section>
		<div class="col-sm-12">
			
			<label for="strValve">How many AUTOMATIC valves do you have on<br /> your pool and or pool/spa system?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Please select number of valve used in the mode?" /></label>
			<a class="changeLink" id="changeLinkValve" href="javascript:void(0);" onclick="javascript:$('#valveForm').toggleClass('disableConfig');" style="float:right;" title="Click here to Enable/Disable valve related settings!">Enable/Disable Valve Configuration</a>
			
			<select name="strValve" id="strValve" class="form-control required" onchange="valveChange();">
			<option <?php if(isset($arrDevice['valve']) && $arrDevice['valve'] == ''){ echo 'selected="selected"';} ?> value="">Select number of valves</option>
			<option <?php if(isset($arrDevice['valve']) && $arrDevice['valve'] == '0'){ echo 'selected="selected"';} ?> value="0">0</option>
			<?php
					for($i=0;$i<$iValveCnt;$i++)
					{
			?>
					<option <?php if(isset($arrDevice['valve']) && $arrDevice['valve'] == ($i+1)){ echo 'selected="selected"';} ?> value="<?php echo ($i+1);?>"><?php echo ($i+1);?></option>
				
			<?php 	} ?>
			</select>
			
			<div style="height:10px">&nbsp;</div>
			
			<label for="valve_actuated">If necessary, Which valve(s) is actuated when you switch from Pool to Spa Mode?<span class="requiredMark">*</span></label>
			
			<select name="valve_actuated[]" id="valve_actuated" class="form-control required" multiple="" onchange="showReason(this.value);">
				<option <?php if(isset($arrDevice['valve_actuated']) && in_array('',$arrDevice['valve_actuated'])){ echo 'selected="selected"';} ?> value="">Select Valve Quantity</option>
				<option <?php if(isset($arrDevice['valve_actuated'])  && in_array(0,$arrDevice['valve_actuated'])){ echo 'selected="selected"';} ?> value="0">Valve 0 is actuated</option>
				<?php
					for($i=0;$i<$iValveCnt;$i++)
					{
				?>
						<option <?php if(isset($arrDevice['valve_actuated'])  && in_array(1,$arrDevice['valve_actuated'])){ echo 'selected="selected"';} ?> value="<?php echo ($i+1);?>">Valve <?php echo ($i+1);?> is actuated</option>
				<?php } ?>
			</select>
			
			<div style="height:10px; display:none;" id="reasonValveBlk">&nbsp;</div>
			<label id="reasonValvelbl" for="reasonValve" style="display:none;">Reason for No valves are actuated?<span class="requiredMark">*</span></label>
			
			<input type="text" name="reasonValve" value="<?php if(isset($arrDevice['reasonValve']) && $arrDevice['reasonValve'] != '') { echo $arrDevice['reasonValve'];}?>" id="reasonValve" class="form-control inputText" style="display:none;">
			
			<div style="height:10px">&nbsp;</div>
			<label for="valveRunTime">Valve Run Time expressed in minutes?<span class="requiredMark">*</span></label>
			<input type="text" name="valveRunTime" value="<?php if(isset($arrDevice['valveRunTime']) && $arrDevice['valveRunTime'] != '') { echo $arrDevice['valveRunTime'];}?>" id="valveRunTime" class="form-control inputText required">
			
			<div style="height:10px">&nbsp;</div>
			<!-- Assign Div -->
			<div class="col-sm-4" style="padding-left:0px;">
				<div class="controls boxed green-line" style="min-height:210px;">
					<div class="inner">
						<h3 class="profile-title"><strong style="color:#C9376E;">Assign Valve</strong></h3>
						<div id="contentsValve">
						<?php
							for($i=0;$i<$iValveCnt;$i++)
							{
								$strValveName 		=	'Valve '.($i+1);	
								$strValveNameTmp 	=	$this->home_model->getDeviceName($i,'V');
								if($strValveNameTmp != '')
									$strValveName	.=	' ('.$strValveNameTmp.')';
									
						?>
							<?php if($i != 0){ echo '<hr />'; }?>
							<div class="rowCheckbox switch">
								<div style="margin-bottom:10px;"><?php echo $strValveName;?></div>
								<div class="custom-checkbox"><input type="checkbox" value="<?php echo $i;?>" onclick="checkValveAssign(this.value)" id="relayValve-<?php echo $i?>" name="relayValve[]" hidefocus="true" style="outline: medium none;" class="valveAssign">
								<label id="lableRelayValve-<?php echo $i?>" for="relayValve-<?php echo $i?>"><span style="color:#C9376E;">&nbsp;</span></label>
								</div>
							</div>
								
						<?php 	
							}
						?>
						</div>
					</div>
				</div>
			</div>
			<!-- Assign Div -->
			
			<!-- Configuration Div -->
			<div class="col-sm-8" style="padding-right:0px;">
				<div id="valveForm" class="disableConfig" >	
					<div style="margin-bottom:10px;"><h3 class="confHeader">Valve Configuration</h3></div>
					<table class="table removeBorder" id="valveTable" width="100%" cellspacing="0" cellpadding="0">
					<tbody>
						<tr>
							<td style="width:48%;">Enter Number of Valves to Use in system:</td>
							<td style="width:2%;">&nbsp;</td>
							<td style="width:50%;"><input type="text" name="valveNumber" id="valveNumber" onkeyup="showValveDetails();" value="<?php if(isset($extra['ValveNumber']) && $extra['ValveNumber'] != 0) { echo $extra['ValveNumber'];}?>" class="form-control inputText"></td>
						</tr>
						<?php if(!empty($ValveRelays))
							  {
								$i = 0;
								foreach($ValveRelays as $valve) 
								{
									$valve_relay_number	=	unserialize($valve->valve_relay_number);
									
						?>
									<tr>
									<td><strong>Valve <?php echo $valve->device_number;?></strong>&nbsp;<span class="relayText">(Enter relays in sequence)</span></td><td>&nbsp;</td><td><input type="text" style="width:50px; display:inline;" class="inputText" id="sRelay<?php echo $i;?>_1" value="<?php echo $valve_relay_number['Relay1'];?>">&nbsp;&nbsp;<input type="text" style="width:50px; display:inline;" class=" inputText" id="sRelay<?php echo $i;?>_2" value="<?php echo $valve_relay_number['Relay2'];?>">&nbsp;<a class="changeLink" href="javascript:void(0);" onclick="removeRow('valveTable')">Remove</a><div style="height: 5px;"> </div><select class="form-control" style="width:100px !important; display:inline;" name="valveDirection_<?php echo $i;?>_1"><option value="">Select Directions</option><option value="pool">Pool</option><option value="spa">Spa</option></select>&nbsp;&nbsp;<select class="form-control" style="width:100px !important; display:inline;" name="valveDirection_<?php echo $i;?>_2"><option value="">Select Directions</option><option value="pool">Pool</option><option value="spa">Spa</option></select>
									</td>
									</tr>
						<?php
									$i++;
								}
								
								echo '<tr>
										<td colspan="3">
											<a href="javascript:void(0);" onclick="checkAndSaveRelays();" class="btn btn-middle"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:$(\'#valveForm\').toggleClass(\'disableConfig\');" class="btn btn-middle btn-red"><span>Cancel</span></a>&nbsp;&nbsp;<span id="loadingImg" style="display:none; vertical-align: middle;"><img src="'.site_url('assets/images/loading.gif').'" alt="Loading...." width="32" height="32"></span>
										</td>
									</tr>';
							  }
						?>
					</tbody>		
					</table>
				</div>
			</div>
			<!-- Configuration Div -->
		</div>	
	</section>
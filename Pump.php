<?php
$sAccess 		= '';
$sModule	    = 9;

  //Get Permission Details
  $userID = $this->session->userdata('id');
  $aPermissions = json_decode(getPermissionOfModule($userID));
 
  $aModules 		= $aPermissions->sPermissionModule;	
  $aAllActiveModule = $aPermissions->sActiveModule;
  
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

$sSubmitButton  = 'Save';

$sSubmitUrl = site_url('home/pumpConfigure/'.base64_encode($sDeviceID));

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
<style type="text/css">
.rowCustom {
    overflow: hidden;
}

.colCustom {
    float: left;
    padding: 5px;
    margin-right: 5px;
}
</style>
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
						  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
						  <li><a href="<?php echo base_url('home/setting/PS/');?>">Pump Sequencer</a></li>
						  <li class="active">Pump Configure</li>
				</ol>
            <?php if($sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Details saved successfully! 
              </div>
            <?php } ?>
			<?php if($err != '') { ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $err;?>
              </div>
            <?php } ?>
			
          </div>
        </div><!-- /.row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading" style="border-top-left-radius: 0px;border-top-right-radius: 0px;">
                <h3 class="panel-title" style="color:#FFF;">Pump Configure Page</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php if($sAccess == 2){ echo $sSubmitUrl; } else { echo '';}?>" method="post">
                  <input type="hidden" name="sDeviceID" value="<?php echo base64_encode($sDeviceID);?>">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr id="trVSClosure">
                        <td width="10%"><strong>Pump Closure:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
                            <div class="rowCustom">
                            <div class="colCustom" style="padding-left:0;">
                              <input type="radio" class="form-control" name="sPumpClosure" id="sPumpClosure0" value="0" <?php if($sPumpClosure == '0') { echo 'checked="checked"'; } ?> required <?php if($sAccess == 1){ echo 'disabled="disabled";';}	?>><lable style="margin-left: 5px;">No contact closure</lable>
                           </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpClosure" id="sPumpClosure1" value="1" <?php if($sPumpClosure == '1') { echo 'checked="checked"'; } ?> required <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">Contact closure 1</lable>
                            </div>
                            </div>
                        </td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>Pump Number:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter Pump Number" name="sPumpNumber" value="<?php echo $sDeviceID;?>" id="sPumpNumber" required <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>

                      <tr>
                        <td width="10%"><strong>Pump Type:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
						<select name="sPumpType" id="sPumpType" class="form-control" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
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
					  
					  <tr id="pumpSubType2SpeedTrBlk" style="display:<?php if($sPumpType == '2Speed'){echo '';}else{echo 'none;';} ?>"><td colspan="3">&nbsp;</td></tr>
					  <tr id="pumpSubType2SpeedTr" style="display:<?php if($sPumpType == '2Speed'){echo '';}else{echo 'none;';} ?>">
                        <td width="10%"><strong>Select Relay:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
							<select name="sPumpSubType1" id="sPumpSubType1" class="form-control" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
							<option value="12" <?php if($sPumpSubType == '12'){echo 'selected="selected";';}?>>12V DC</option>
							<option value="24" <?php if($sPumpSubType == '24'){echo 'selected="selected";';}?>>24V AC</option>
							</select>
						</td>
                      </tr>
					  
					  <tr id="pumpSubTypeTrBlk" style="display:<?php if(preg_match('/Emulator/',$sPumpType)){echo '';}else{echo 'none;';} ?>"><td colspan="3">&nbsp;</td></tr>
					  <tr id="pumpSubTypeTr" style="display:<?php if(preg_match('/Emulator/',$sPumpType)){echo '';}else{ echo'none;';} ?>;">
                        <td width="10%"><strong>Pump Sub Type:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
							<select name="sPumpSubType" id="sPumpSubType" class="form-control" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
							<option value="VS" <?php if($sPumpSubType == 'VS'){echo 'selected="selected";';}?>>VS Pump (Variable Speed)</option>
							<option value="VF" <?php if($sPumpSubType == 'VF'){echo 'selected="selected";';}?>>VF Pump (Variable Flow)</option>
							</select>
						</td>
                      </tr>
					  
					  <tr id="trVSSpaceIntellicom" style="display:<?php if(preg_match('/Intellicom/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr id="trVSIntellicom" style="display:<?php if(preg_match('/Intellicom/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;">
                        <td width="10%"><strong>Pump Speed:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
                            <div class="rowCustom">
                            <div class="colCustom" style="padding-left:0;">
                              <input type="radio" class="form-control" name="sPumpSpeedIn" id="sPumpSpeedIn0" value="0" <?php if($sPumpSpeed == '0') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">0</lable>
                           </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeedIn" id="sPumpSpeedIn1" value="1" <?php if($sPumpSpeed == '1') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">1</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeedIn" id="sPumpSpeedIn2" value="2" <?php if($sPumpSpeed == '2') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">2</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeedIn" id="sPumpSpeedIn3" value="3" <?php if($sPumpSpeed == '3') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">3</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeedIn" id="sPumpSpeedIn4" value="4" <?php if($sPumpSpeed == '4') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">4</lable>
                            </div>
                            </div>
                        </td>
                      </tr>
					  
                      <tr id="trVSSpace" style="display:<?php if($sPumpSubType =='VS' && preg_match('/Emulator/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr id="trVS" style="display:<?php if($sPumpSubType =='VS' && preg_match('/Emulator/',$sPumpType)) { echo ''; } else { echo 'none';} ?>;">
                        <td width="10%"><strong>Pump Speed:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
                            <div class="rowCustom">
                            <div class="colCustom" style="padding-left:0;">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed0" value="0" <?php if($sPumpSpeed == '0') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">0</lable>
                           </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed1" value="1" <?php if($sPumpSpeed == '1') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">1</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed2" value="2" <?php if($sPumpSpeed == '2') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">2</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed3" value="3" <?php if($sPumpSpeed == '3') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">3</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed4" value="4" <?php if($sPumpSpeed == '4') { echo 'checked=""checked';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>><lable style="margin-left: 5px;">4</lable>
                            </div>
                            </div>
                        </td>
                      </tr>
                      <tr id="trVFSpace" style="display:<?php if($sPumpSubType =='VF' && $sPumpType == 'Emulator') { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr id="trVF" style="display:<?php if($sPumpSubType =='VF' && $sPumpType == 'Emulator') { echo ''; } else { echo 'none';} ?>;">
                        <td width="10%"><strong>Pump Flow:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" name="sPumpFlow" id="sPumpFlow" value="<?php echo $sPumpFlow;?>" <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
                        </td>
                      </tr>
					  <tr id="trRelayNumberSpace" <?php if($sPumpType == 'Intellicom' || $sPumpType == 'Emulator') { echo 'style="display:none;"';} ?>><td colspan="3">&nbsp;</td></tr>
					  <tr id="trRelayNumber" <?php if($sPumpType == 'Intellicom' || $sPumpType == 'Emulator') { echo 'style="display:none;"';} ?>>
                        <td width="10%"><strong>Relay Number:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" name="sRelayNumber" id="sRelayNumber" value="<?php echo $sRelayNumber;?>" <?php if($sPumpType != 'Intellicom' && $sPumpType != 'Emulator'  ) { echo 'required';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
                        </td>
                      </tr>
					  <tr id="trRelayNumber1Space" <?php if($sPumpType == '2Speed') { echo '';} else { echo 'style="display:none;"';}?>><td colspan="3">&nbsp;</td></tr>
					  <tr id="trRelayNumber1" <?php if($sPumpType == '2Speed') { echo '';} else { echo 'style="display:none;"';}?>>
                        <td width="10%"><strong>Relay Number 2:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" name="sRelayNumber1" id="sRelayNumber1" value="<?php echo $sRelayNumber1;?>" <?php if($sPumpType == '2Speed') { echo 'required';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
                        </td>
                      </tr>
					  
					  <tr id="trAddressNumberSpace" <?php if($sPumpType == '12' || $sPumpType == '24' || $sPumpType == '2Speed') { echo 'style="display:none;"';} ?>><td colspan="3">&nbsp;</td></tr>
					  <tr id="trAddressNumber" <?php if($sPumpType == '12' || $sPumpType == '24' || $sPumpType == '2Speed') { echo 'style="display:none;"';} ?>>
                        <td width="10%"><strong>Pump Address:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" name="sPumpAddress" id="sPumpAddress" value="<?php echo $sPumpAddress;?>" <?php if($sPumpType != '12' && $sPumpType != '24' && $sPumpType != '2Speed') { echo 'required';} ?> <?php if($sAccess == 1){ echo 'disabled="disabled";';}?>>
                        </td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><span class="btn btn-green"><input type="<?php if($sAccess == 2) {echo 'submit';} else {echo 'button';}?>" name="command" value="<?php echo $sSubmitButton;?>" onclick="return checkPumpAddress('<?php echo $sDeviceID;?>');"></span>&nbsp;&nbsp;<a class="btn btn-red" href="<?php echo site_url('home/setting/PS/');?>"><span>Back</span></a>&nbsp;&nbsp;<span id="loadingImg" style="display:none;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span></td></tr>
                      
                    </table>
                    <div style="height:20px;">&nbsp;</div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
	$("#sPumpType").change(function(){
		var sSelectedVal	=	$(this).val();
		if(sSelectedVal == 'Emulator' || sSelectedVal == 'Emulator12' || sSelectedVal == 'Emulator24')
		{
			$("#pumpSubTypeTr").show();
			$("#pumpSubTypeTrBlk").show();
			
			$("input:radio[name='sPumpSpeed']").attr('required','required');
		    $("#sPumpFlow").removeAttr('required');
		    $("#trVF").hide();
		    $("#trVFSpace").hide();
		  
		    $("#trVS").show(); 
		    $("#trVSSpace").show();
		}
		else
		{
			$("#pumpSubTypeTr").hide();
			$("#pumpSubTypeTrBlk").hide();
			
			$("#trVS").hide(); 
		    $("#trVSSpace").hide();
			$("input:radio[name='sPumpSpeed']").removeAttr('required');
			$("#sPumpFlow").removeAttr('required');
			$("#trVF").hide();
		    $("#trVFSpace").hide();
		}
		
		if(sSelectedVal == 'Emulator' || sSelectedVal == 'Intellicom')
		{
			$("#trRelayNumberSpace").hide();
			$("#trRelayNumber").hide();
			$("#sRelayNumber").removeAttr('required');
		}
		else
		{
			$("#trRelayNumberSpace").show();
			$("#trRelayNumber").show();
			$("#sRelayNumber").attr('required','required');
		}
		
		if(sSelectedVal == '12' || sSelectedVal == '24' || sSelectedVal == '2Speed')
		{
			$("#trAddressNumberSpace").hide();
			$("#trAddressNumber").hide();
			$("#sPumpAddress").removeAttr('required');
		}
		else
		{
			$("#trAddressNumberSpace").show();
			$("#trAddressNumber").show();
			$("#sPumpAddress").attr('required','required');
		}
		
		if(sSelectedVal == 'Intellicom' || sSelectedVal == 'Intellicom12' || sSelectedVal == 'Intellicom24')
		{
			$("input:radio[name='sPumpSpeedIn']").attr('required','required');
		    
		    $("#trVSIntellicom").show(); 
		    $("#trVSSpaceIntellicom").show();
		}
		else
		{
			$("input:radio[name='sPumpSpeedIn']").removeAttr('required');
			$("#trVSIntellicom").hide();
		    $("#trVSSpaceIntellicom").hide();
		}
		
		if(sSelectedVal == '2Speed')
		{
			$("input:radio[name='sRelayNumber1']").attr('required','required');
		    
		    $("#trRelayNumber1Space").show(); 
		    $("#trRelayNumber1").show();
			$("#pumpSubType2SpeedTrBlk").show(); 
		    $("#pumpSubType2SpeedTr").show();
			$("#trAddressNumberSpace").hide();
			$("#trAddressNumber").hide();
			$("input:radio[name='sPumpAddress']").removeAttr('required');
		}
		else
		{
			$("input:radio[name='sRelayNumber1']").removeAttr('required');
		    
		    $("#trRelayNumber1Space").hide(); 
		    $("#trRelayNumber1").hide();
			$("#pumpSubType2SpeedTrBlk").hide(); 
		    $("#pumpSubType2SpeedTr").hide();
			
			
		}
		
	});
	
	$("#sPumpSubType").change(function() {
		
    var sSelectedVal	=	$(this).val();
    if(sSelectedVal == 'VF' && $("#sPumpType").val() == 'Emulator')
    {
      $("#sPumpFlow").attr('required','required');
      $("input:radio[name='sPumpSpeed']").removeAttr('required');
	  $("input:radio[name='sPumpClosure']").removeAttr('required');

      $("#trVF").show();
      $("#trVFSpace").show();
      
      $("#trVS").hide();
      $("#trVSSpace").hide();
	  
    }
    else if(sSelectedVal == 'VS' && $("#sPumpType").val() == 'Emulator')
    {
      $("input:radio[name='sPumpSpeed']").attr('required','required');
	  $("input:radio[name='sPumpClosure']").attr('required','required');
      $("#sPumpFlow").removeAttr('required');
      $("#trVF").hide();
      $("#trVFSpace").hide();
      
      $("#trVS").show(); 
      $("#trVSSpace").show();
	  
    }
  });
  function checkPumpAddress(sDeviceID)
  {
		$("#loadingImg").show();
		
		var sSelectedVal	=	$("#sPumpType").val();
		var sPumpAddress	=	$("#sPumpAddress").val();
		
		if(sSelectedVal != '12' && sSelectedVal != '24' && sSelectedVal != '2Speed')
		{
			var checkAddress	=	0;
			//Check if entered address already exists.
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/checkAddressPump/');?>", 
				data: {sDeviceID:sDeviceID,sPumpAddress:sPumpAddress},
				async: false,
				success: function(data) {
					
					var obj = jQuery.parseJSON( data );
					if(obj.iAddressCheck == '1')
					{
						//return false;
						checkAddress = 1;
						alert('Entered address is already assigned to one of the Pumps (Pump '+obj.iPumpID+')');
					}
				}
			});
		}
		if(checkAddress == '1')
			return false;
		
		var checkRelay	=	0;
		var sRelayNumber	=	jQuery("#sRelayNumber").val();
		if(sSelectedVal != 'Emulator' && sSelectedVal != 'Intellicom' && sSelectedVal != '2Speed')
		{
			//Check if entered address already exists.
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/checkRelayNumber/');?>", 
				data: {sDeviceID:sDeviceID,sRelayNumber:sRelayNumber,type:sSelectedVal},
				async: false,
				success: function(data) {
					console.log(data);
					var obj = jQuery.parseJSON( data );
					
					if(obj.iPumpCheck == '1')
					{
						//return false;
						checkRelay = 1;
						$("#sRelayNumber").css('border','1px Solid Red');
						alert('Relay number is already used or not available for the selected Pump Type!');
					}
					else if(obj.iPumpCheck == '2')
					{
						checkRelay = 1;
						$("#sRelayNumber").css('border','1px Solid Red');
						alert('Relay number is assigned to Valve!');
					}
				}
			});
		}
		
		if(sSelectedVal == '2Speed')
		{
			var sRelayNumber1 = $("#sRelayNumber1").val();
			var sPumpSubType1 = $("#sPumpSubType1").val();
			if(sRelayNumber == sRelayNumber1)
			{
				alert("Please select another relay number!");
				$("#sRelayNumber1").css('border','1px Solid Red');
				checkRelay = 1;
			}
			
			if(checkRelay != 1)
			{
				$("#sRelayNumber1").css('');
				$.ajax({
							type: "POST",
							url: "<?php echo site_url('home/checkRelayNumber/');?>", 
							data: {sDeviceID:sDeviceID,sRelayNumber:sRelayNumber,type:sPumpSubType1},
							async: false,
							success: function(data) {
								console.log(data);
								var obj = jQuery.parseJSON( data );
								
								if(obj.iPumpCheck == '1')
								{
									//return false;
									checkRelay = 1;
									$("#sRelayNumber").css('border','1px Solid Red');
									alert('Relay number is already used or not available for the selected Pump Type!');
								}
								else if(obj.iPumpCheck == '2')
								{
									checkRelay = 1;
									$("#sRelayNumber").css('border','1px Solid Red');
									alert('Relay number is assigned to Valve!');
								}
								else
									$("#sRelayNumber").css('border','');
							}
						});
						
				if(checkRelay != 1)
				{
					$.ajax({
						type: "POST",
						url: "<?php echo site_url('home/checkRelayNumber/');?>", 
						data: {sDeviceID:sDeviceID,sRelayNumber:sRelayNumber1,type:sPumpSubType1},
						async: false,
						success: function(data) {
							console.log(data);
							var obj = jQuery.parseJSON( data );
							
							if(obj.iPumpCheck == '1')
							{
								//return false;
								checkRelay = 1;
								$("#sRelayNumber1").css('border','1px Solid Red');
								alert('Relay number is already used or not available for the selected Pump Type!');
							}
							else if(obj.iPumpCheck == '2')
							{
								checkRelay = 1;
								$("#sRelayNumber1").css('border','1px Solid Red');
								alert('Relay number is assigned to Valve!');
							}
							else
								$("#sRelayNumber1").css('border','');
						}
					});
				}
			}	
		}
		
		$("#loadingImg").hide();
		
		if(checkRelay == '1')
			return false;
		
		return true;
  }
</script>
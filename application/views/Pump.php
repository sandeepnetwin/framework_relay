<?php
$this->load->view('Header');

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
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> Pump Configure</li>
            </ol>
            <?php if($sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Details saved successfully! 
              </div>
            <?php } ?>
          </div>
        </div><!-- /.row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Pump Configure Page</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php echo $sSubmitUrl;?>" method="post">
                  <input type="hidden" name="sDeviceID" value="<?php echo base64_encode($sDeviceID);?>">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr id="trVSClosure">
                        <td width="10%"><strong>Pump Closure:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
                            <div class="rowCustom">
                            <div class="colCustom" style="padding-left:0;">
                              <input type="radio" class="form-control" name="sPumpClosure" id="sPumpClosure0" value="0" <?php if($sPumpClosure == '0') { echo 'checked="checked"'; } ?> required><lable style="margin-left: 5px;">No contact closure</lable>
                           </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpClosure" id="sPumpClosure1" value="1" <?php if($sPumpClosure == '1') { echo 'checked="checked"'; } ?> required><lable style="margin-left: 5px;">Contact closure 1</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpClosure" id="sPumpClosure2" value="2" <?php if($sPumpClosure == '2') { echo 'checked="checked"'; } ?> required><lable style="margin-left: 5px;">Contact closure 2</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpClosure" id="sPumpClosure3" value="3" <?php if($sPumpClosure == '3') { echo 'checked="checked"'; } ?> required><lable style="margin-left: 5px;">Contact closure 3</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpClosure" id="sPumpClosure4" value="4" <?php if($sPumpClosure == '4') { echo 'checked="checked"'; } ?> required><lable style="margin-left: 5px;">Contact closure 4</lable>
                            </div>
                            </div>
                        </td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>Pump Number:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter Pump Number" name="sPumpNumber" value="<?php echo $sDeviceID;?>" id="sPumpNumber" required></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>

                      <tr>
                        <td width="10%"><strong>Pump Type:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
						<select name="sPumpType" id="sPumpType" class="form-control">
						<option value="12" <?php if($sPumpType == '12'){echo 'selected="selected";';}?>>12V DC</option>
						<option value="24" <?php if($sPumpType == '24'){echo 'selected="selected";';}?>>24V AC</option>
						<option value="Intellicom" <?php if($sPumpType == 'Intellicom'){echo 'selected="selected";';}?>>Intellicom for a Pentair VS or VF Pump</option>
						<option value="Emulator" <?php if($sPumpType == 'Emulator'){echo 'selected="selected";';}?>>Emulator Pentair VS or VF Pump</option>
						</select>
						<!--<input type="radio" name="sPumpType" <?php if($sPumpType =='2' || $sPumpType == '') { echo 'checked="checked"'; } ?> value="2" id="sPumpTypeVS">&nbsp;VS Pump &nbsp;&nbsp;<input type="radio" name="sPumpType" <?php if($sPumpType =='3') { echo 'checked="checked"'; } ?> value="3" id="sPumpTypeVF">&nbsp;VF Pump-->
                        </td>
                      </tr>
					  <tr id="pumpSubTypeTrBlk" style="display:<?php if($sPumpType == 'Emulator') {echo '';} else {'none;';} ?>"><td colspan="3">&nbsp;</td></tr>
					  <tr id="pumpSubTypeTr" style="display:<?php if($sPumpType == 'Emulator') {echo '';} else {'none;';} ?>;">
                        <td width="10%"><strong>Pump Sub Type:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
							<select name="sPumpSubType" id="sPumpSubType" class="form-control">
							<option value="VS" <?php if($sPumpSubType == 'VS'){echo 'selected="selected";';}?>>VS Pump (Variable Speed)</option>
							<option value="VF" <?php if($sPumpSubType == 'VF'){echo 'selected="selected";';}?>>VF Pump (Variable Flow)</option>
							</select>
						</td>
                      </tr>
                      <tr id="trVSSpace" style="display:<?php if($sPumpSubType =='VS') { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr id="trVS" style="display:<?php if($sPumpSubType =='VS') { echo ''; } else { echo 'none';} ?>;">
                        <td width="10%"><strong>Pump Speed:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
                            <div class="rowCustom">
                            <div class="colCustom" style="padding-left:0;">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed0" value="0" <?php if($sPumpSpeed == '0') { echo 'checked=""checked';} ?> required><lable style="margin-left: 5px;">0</lable>
                           </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed1" value="1" <?php if($sPumpSpeed == '1') { echo 'checked=""checked';} ?> required><lable style="margin-left: 5px;">1</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed2" value="2" <?php if($sPumpSpeed == '2') { echo 'checked=""checked';} ?> required><lable style="margin-left: 5px;">2</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed3" value="3" <?php if($sPumpSpeed == '3') { echo 'checked=""checked';} ?> required><lable style="margin-left: 5px;">3</lable>
                            </div>
                            <div class="colCustom">
                              <input type="radio" class="form-control" name="sPumpSpeed" id="sPumpSpeed4" value="4" <?php if($sPumpSpeed == '4') { echo 'checked=""checked';} ?> required><lable style="margin-left: 5px;">4</lable>
                            </div>
                            </div>
                        </td>
                      </tr>
                      <tr id="trVFSpace" style="display:<?php if($sPumpSubType =='VF') { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr id="trVF" style="display:<?php if($sPumpSubType =='VF') { echo ''; } else { echo 'none';} ?>;">
                        <td width="10%"><strong>Pump Flow:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" name="sPumpFlow" id="sPumpFlow" value="<?php echo $sPumpFlow;?>">
                        </td>
                      </tr>
					  <tr><td colspan="3">&nbsp;</td></tr>
					  <tr>
                        <td width="10%"><strong>Relay Number:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" name="sRelayNumber" id="sRelayNumber" value="<?php echo $sRelayNumber;?>" required>
                        </td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><input type="submit" name="command" value="<?php echo $sSubmitButton;?>" class="btn btn-success">&nbsp;&nbsp;<a class="btn btn-primary btn-xs" style="padding: 7px;" href="<?php echo site_url('home/setting/PS/');?>">Back</a></td></tr>
                      
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
		if(sSelectedVal == 'Emulator')
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
	});
	
	$("#sPumpSubType").change(function() {
    var sSelectedVal	=	$(this).val();
    if(sSelectedVal == 'VF')
    {
      $("#sPumpFlow").attr('required','required');
      $("input:radio[name='sPumpSpeed']").removeAttr('required');
	  $("input:radio[name='sPumpClosure']").removeAttr('required');

      $("#trVF").show();
      $("#trVFSpace").show();
      
      $("#trVS").hide();
      $("#trVSSpace").hide();
	  
    }
    else if(sSelectedVal == 'VS')
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
</script>
<hr>
<?php
$this->load->view('Footer');
?>
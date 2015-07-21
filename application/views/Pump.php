<?php
$this->load->view('Header');

$sSubmitButton  = 'Save';

$sSubmitUrl = site_url('home/pumpConfigure/'.base64_encode($sDeviceID));

$sPumpNumber  = '';
$sPumpType  = '';
$sPumpSpeed  = '';
$sPumpFlow = '';
$sPumpClosure   = '';

if(is_array($sPumpDetails) && !empty($sPumpDetails))
{
  foreach($sPumpDetails as $aResultEdit)
  { 
    $sPumpNumber  = $aResultEdit->pump_number;
    $sPumpType    = $aResultEdit->pump_type;
    $sPumpSpeed   = $aResultEdit->pump_speed;
    $sPumpFlow    = $aResultEdit->pump_flow;
    $sPumpClosure = $aResultEdit->pump_closure;
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
                      <tr>
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
                        <td width="89%"><input type="radio" name="sPumpType" <?php if($sPumpType =='2' || $sPumpType == '') { echo 'checked="checked"'; } ?> value="2" id="sPumpTypeVS">&nbsp;VS Pump &nbsp;&nbsp;<input type="radio" name="sPumpType" <?php if($sPumpType =='3') { echo 'checked="checked"'; } ?> value="3" id="sPumpTypeVF">&nbsp;VF Pump
                        </td>
                      </tr>
                      <tr id="trVSSpace" style="display:<?php if($sPumpType =='2' || $sPumpType == '') { echo ''; } else { echo 'none';} ?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr id="trVS" style="display:<?php if($sPumpType =='2' || $sPumpType == '') { echo ''; } else { echo 'none';} ?>;">
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
                      <tr id="trVFSpace" style="display:<?php if($sPumpType == '3') { echo '';} else {echo 'none';}?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr id="trVF" style="display:<?php if($sPumpType == '3') { echo '';} else {echo 'none';}?>;">
                        <td width="10%"><strong>Pump Flow:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" name="sPumpFlow" id="sPumpFlow" value="<?php echo $sPumpFlow;?>">
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
  $("input:radio[name='sPumpType']").click(function() {
    var chkVal  = $("input:radio[name='sPumpType']:checked").val();
    if(chkVal == '3')
    {
      $("#sPumpFlow").attr('required','required');
      $("input:radio[name='sPumpSpeed']").removeAttr('required');

      $("#trVF").show();
      $("#trVFSpace").show();
      
      $("#trVS").hide();
      $("#trVSSpace").hide();
    }
    else if(chkVal == '2')
    {
      $("input:radio[name='sPumpSpeed']").attr('required','required');
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
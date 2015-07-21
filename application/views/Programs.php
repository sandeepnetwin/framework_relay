<?php
$this->load->view('Header');

$sProgrameNameEdit  = '';
$sProgrameTypeEdit  = '';
$sProgrameDaysEdit  = '';
$sProgrameStartEdit = '';
$sProgrameEndEdit   = '';
$isAbsoluteProgram  = ''; 
$aStart             = array();
$aEnd               = array();

if(is_array($sProgramDetailsEdit) && !empty($sProgramDetailsEdit))
{
  foreach($sProgramDetailsEdit as $aResultEdit)
  { 
    $sProgrameNameEdit  = $aResultEdit->relay_prog_name;
    $sProgrameTypeEdit  = $aResultEdit->relay_prog_type;
    $sProgrameDaysEdit  = explode(',',$aResultEdit->relay_prog_days);
    $sProgrameStartEdit = $aResultEdit->relay_start_time;
    $sProgrameEndEdit   = $aResultEdit->relay_end_time;

    $aStart             = explode(':',$sProgrameStartEdit);
    $aEnd               = explode(':',$sProgrameEndEdit);

    $isAbsoluteProgram  = $aResultEdit->relay_prog_absolute;
  }
}

if(empty($aStart))
  $aStart = array(0=>0,1=>0);
if(empty($aEnd))
  $aEnd   = array(0=>23,1=>59);

$sSubmitButton  = 'Save';

$sSubmitUrl = site_url('home/setPrograms/'.base64_encode($sDeviceID));
if($sProgramID != '')
{
  $sSubmitUrl .= '/'.base64_encode($sProgramID).'/';
  $sSubmitButton  = 'Update';
}

?>
<style type="text/css">
.row {
    overflow: hidden;
    padding: 5px;
}

.col {
    float: left;
    padding: 5px;
    margin-right: 5px;
}
</style>
  <link href="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css');?>" rel="stylesheet" type="text/css" />  
  <link href="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/jquery.ui.timepicker.css?v=0.3.3');?>" rel="stylesheet" type="text/css" />
 
  <script type="text/javascript" src="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/ui-1.10.0/jquery.ui.core.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/ui-1.10.0/jquery.ui.position.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/jquery.ui.timepicker.js?v=0.3.3');?>"></script>

    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> Programs</li>
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
                <h3 class="panel-title">Programs Page</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php echo $sSubmitUrl;?>" method="post">
                  <input type="hidden" name="sDeviceID" value="<?php echo base64_encode($sDeviceID);?>">
                   <input type="hidden" name="sProgramID" value="<?php echo base64_encode($sProgramID);?>">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr>
                        <td width="10%"><strong>Program Name:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter Program Name" name="sProgramName" value="<?php echo $sProgrameNameEdit;?>" id="sProgramName" required></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>Relay Number:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter Relay Number" name="sRelayNumber" value="<?php echo $sDeviceID;?>" id="sRelayNumber" required></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>

                      <tr>
                        <td width="10%"><strong>Program Type:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="radio" name="sProgramType" <?php if($sProgrameTypeEdit =='1' || $sProgrameTypeEdit == '') { echo 'checked="checked"'; } ?> value="1" id="sProgramTypeDaily">&nbsp;Daily &nbsp;&nbsp;<input type="radio" name="sProgramType" <?php if($sProgrameTypeEdit =='2') { echo 'checked="checked"'; } ?> value="2" id="sProgramTypeWeekly">&nbsp;Weekly
                        </td>
                      </tr>
                       <tr><td colspan="3">&nbsp;</td></tr>
                      <tr id="tr_week" style="display:<?php if($sProgrameTypeEdit =='1' || $sProgrameTypeEdit == '') { echo 'none'; } ?>;">
                        <td width="10%"><strong>&nbsp;</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><div class="row">
                            <div class="col">
                              <input type="checkbox" <?php if(!empty($sProgrameDaysEdit) && in_array(1,$sProgrameDaysEdit)){ echo 'checked="checked"';} ?> name="sProgramDays[]" value="1" /><lable style="margin-left: 5px;">Monday</lable>
                           </div>
                            <div class="col">
                              <input type="checkbox" <?php if(!empty($sProgrameDaysEdit) && in_array(2,$sProgrameDaysEdit)){ echo 'checked="checked"';} ?> name="sProgramDays[]" value="2" /><lable style="margin-left: 5px;">Tuesday</lable>
                           </div>
                            <div class="col">
                              <input type="checkbox" <?php if(!empty($sProgrameDaysEdit) && in_array(3,$sProgrameDaysEdit)){ echo 'checked="checked"';} ?> name="sProgramDays[]" value="3" /><lable style="margin-left: 5px;">Wednesday</lable>
                           </div>
                            <div class="col">
                              <input type="checkbox" <?php if(!empty($sProgrameDaysEdit) && in_array(4,$sProgrameDaysEdit)){ echo 'checked="checked"';} ?> name="sProgramDays[]" value="4" /><lable style="margin-left: 5px;">Thursday</lable>
                           </div>
                            </div>
                            <div class="row">
                            <div class="col">
                              <input type="checkbox" <?php if(!empty($sProgrameDaysEdit) && in_array(5,$sProgrameDaysEdit)){ echo 'checked="checked"';} ?> name="sProgramDays[]" value="5" /><lable style="margin-left: 5px;">Friday</lable>
                           </div>
                            <div class="col">
                              <input type="checkbox" <?php if(!empty($sProgrameDaysEdit) && in_array(6,$sProgrameDaysEdit)){ echo 'checked="checked"';} ?> name="sProgramDays[]" value="6" /><lable style="margin-left: 5px;">Saturday</lable>
                            </div>
                            <div class="col">
                              <input type="checkbox" <?php if(!empty($sProgrameDaysEdit) && in_array(7,$sProgrameDaysEdit)){ echo 'checked="checked"';} ?> name="sProgramDays[]" value="7"/><lable style="margin-left: 5px;">Sunday</lable>
                           </div>
                        </div>
                        </td>
                      </tr>
                      <tr id="tr_week_blank" style="display:<?php if($sProgrameTypeEdit =='1'  || $sProgrameTypeEdit == '') { echo 'none'; } ?>;"><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>Start Time:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Select Start Time" name="sStartTime" value="<?php echo substr($sProgrameStartEdit,0,-3);?>" id="timepicker_start" required></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>End Time:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Select End Time" name="sEndTime" value="<?php echo substr($sProgrameEndEdit,0,-3);?>" id="timepicker_end" required></td>
                      </tr>

                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>Absolute Program:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="radio" <?php if($isAbsoluteProgram == '1') { echo 'checked="checked;"';}?> name="isAbsoluteProgram" id="isAbsoluteProgramYes" value="1" >&nbsp;Yes&nbsp;&nbsp;<input type="radio" name="isAbsoluteProgram" id="isAbsoluteProgramNo" value="0" <?php if($isAbsoluteProgram == '0' || $isAbsoluteProgram == '') { echo 'checked="checked;"';}?> >&nbsp;No</td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><input type="submit" name="command" value="<?php echo $sSubmitButton;?>" class="btn btn-success">&nbsp;&nbsp;<a class="btn btn-primary btn-xs" style="padding: 7px;" href="<?php echo site_url('home/setPrograms/'.base64_encode($sDeviceID).'/');?>">Cancel</a>&nbsp;&nbsp;<a class="btn btn-primary btn-xs" style="padding: 7px;" href="<?php echo site_url('home/setting/R/');?>">Back</a></td></tr>
                      
                    </table>
                    <div style="height:20px;">&nbsp;</div>

                    <Div class="table-responsive">
                    <table class="table table-hover tablesorter">
                    <thead>
                      <tr>
                        <th class="header">Program Name <i class="fa fa-sort"></i></th>
                        <th class="header">Relay Number <i class="fa fa-sort"></i></th>
                        <th class="header">Program Type <i class="fa fa-sort"></i></th>
                        <th class="header">Program Days <i class="fa fa-sort"></i></th>
                        <th class="header">Start Time <i class="fa fa-sort"></i></th>
                        <th class="header">End Time <i class="fa fa-sort"></i></th>
                        <th class="header">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        if(is_array($sProgramDetails) && !empty($sProgramDetails))
                        {
                            $cntDevicePrograms  = count($sProgramDetails);
                            $aAllDays           = array( 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
                            foreach($sProgramDetails as $aResult)
                            {
                                $sProgramType = 'Daily';
                                $sProgramDays = 'All';

                                if($aResult->relay_prog_type == '2')
                                {
                                  $sProgramType = 'Weekly';
                                  $aProgDays    = switch_arrays($aAllDays, explode(',',$aResult->relay_prog_days));
                                  $sProgramDays = implode(',',$aProgDays); 
                                }

                                echo '<tr>
                                     <td>'.$aResult->relay_prog_name.'</td>
                                     <td>'.$aResult->relay_number.'</td>
                                     <td>'.$sProgramType.'</td>
                                     <td>'.$sProgramDays.'</td>
                                     <td>'.$aResult->relay_start_time.'</td>
                                     <td>'.$aResult->relay_end_time.'</td>
                                     <td><a class="btn btn-primary btn-xs" href="'.site_url('home/setPrograms/'.base64_encode($sDeviceID).'/'.base64_encode($aResult->relay_prog_id)).'">EDIT</a>&nbsp;&nbsp;<a class="btn btn-primary btn-xs" href="'.site_url('home/setPrograms/'.base64_encode($sDeviceID).'/'.base64_encode($aResult->relay_prog_id)).'/D/">DELETE</a></td> 
                                     </tr>';
                            }
                        }
                        else
                        echo  '<tr><td colspan="7" style="color:#B40404; font-weight:bold;">No Programs available for the Relay!</td></tr>';
                      ?>
                    </tbody>
                    </table>
                    </Div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
  //$('input.timepicker').timepicker({timeFormat: 'HH:mm:ss'});
  function checkModeSelected()
  {
    var sRelayMode = $("#relay_mode").val();
    if(sRelayMode == '0')
    {
      $("#relay_mode").css('border','1px solid #B40404');
      alert('Please select Mode!');
      return false;
    }
    else
    {
      $("#relay_mode").css('border','');
      return true;
    }

  }
  $("input:radio[name='sProgramType']").click(function() {
    var chkVal  = $("input:radio[name='sProgramType']:checked").val();
    if(chkVal == '2')
    {
      $("#tr_week").show();
      $("#tr_week_blank").show();
    }
    else
    {
      $("#tr_week").hide();
      $("#tr_week_blank").hide(); 
    }
  });
</script>
<script type="text/javascript">
            $(document).ready(function() {
                $('#timepicker_start').timepicker({
                    showLeadingZero: true,
                    showMinutesLeadingZero: true,
                    onSelect: tpStartSelect,
                    maxTime: {
                        hour: <?php echo $aEnd[0];?>, minute: <?php echo $aEnd[1];?>
                    }
                });
                $('#timepicker_end').timepicker({
                    showLeadingZero: true,
                    showMinutesLeadingZero: true,
                    onSelect: tpEndSelect,
                    minTime: {
                        hour: <?php echo $aStart[0];?>, minute: <?php echo $aStart[1];?>
                    }
                });
            });

            // when start time change, update minimum for end timepicker
            function tpStartSelect( time, endTimePickerInst ) {
                $('#timepicker_end').timepicker('option', {
                    minTime: {
                        hour: endTimePickerInst.hours,
                        minute: endTimePickerInst.minutes
                    }
                });
            }

            // when end time change, update maximum for start timepicker
            function tpEndSelect( time, startTimePickerInst ) {
                $('#timepicker_start').timepicker('option', {
                    maxTime: {
                        hour: startTimePickerInst.hours,
                        minute: startTimePickerInst.minutes
                    }
                });
            }
        </script>
<hr>
<?php
$this->load->view('Footer');
?>
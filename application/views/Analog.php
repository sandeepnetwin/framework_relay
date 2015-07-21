<?php
$this->load->view('Header');
?>
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> Input</li>
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
                <h3 class="panel-title">Assign Device To Input</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                <form action="<?php echo site_url('analog/');?>" method="post">
                <table class="table table-hover">
                <thead>
                  <tr>
                    <th class="header">Input</th>
                    <th class="header">&nbsp;</th>
                    <th class="header">Device</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $d=0;
                        foreach($aResponse as $input => $status)  
                        {

                            $sSelectDevice  =   '<select name="sDeviceName[]" id="sDeviceName_'.$d.'" class="form-control" onchange="showHideOption(\''.$d.'\');">';
                            $sSelectDevice  .=  '<option value="">---- Select Device ----</option>';

                            if($sRelays != '')
                            {
                                for ($i=0;$i < $relay_count; $i++)
                                {
                                    $sDeviceNameDb = 'Relay '.$i;
                                    $sNameDb =  $this->home_model->getDeviceName($i,'R');

                                    if($sNameDb != '')
                                        $sDeviceNameDb  .= ' ('.$sNameDb.')';

                                    $sSelect = '';
                                    if(isset($aAllAnalogDevice[$d]) && $aAllAnalogDevice[$d] == $i.'_R')
                                      $sSelect = 'selected="selected"';

                                        
                                    $sSelectDevice  .='<option value="'.$i.'_R" '.$sSelect.'>'.$sDeviceNameDb.'</option>';
                                }
                            }

                            if($sValves != '')
                            {
                                for ($i=0;$i < $valve_count; $i++)
                                {
                                    
                                    $sDeviceNameDb = 'Valve '.$i;
                                    $sNameDb =  $this->home_model->getDeviceName($i,'V');

                                    if($sNameDb != '')
                                        $sDeviceNameDb  .= ' ('.$sNameDb.')';

                                    $sSelect = '';
                                    if(isset($aAllAnalogDevice[$d]) && $aAllAnalogDevice[$d] == $i.'_V')
                                      $sSelect = 'selected="selected"';  
                                      
                                    $sSelectDevice  .='<option value="'.$i.'_V" '.$sSelect.'>'.$sDeviceNameDb.'</option>';
                                }
                            }

                            if($sPowercenter != '')
                            {
                                for ($i=0;$i < $power_count; $i++)
                                {
                                    
                                    $sDeviceNameDb = 'Power Center '.$i;
                                    $sNameDb =  $this->home_model->getDeviceName($i,'P');

                                    if($sNameDb != '')
                                        $sDeviceNameDb  .= ' ('.$sNameDb.')';

                                    $sSelect = '';
                                    if(isset($aAllAnalogDevice[$d]) && $aAllAnalogDevice[$d] == $i.'_P')
                                      $sSelect = 'selected="selected"';   
                                      
                                    $sSelectDevice  .='<option value="'.$i.'_P" '.$sSelect.'>'.$sDeviceNameDb.'</option>';
                                }
                            }

                            if($sPump != '')
                            {
                                for ($i=0;$i < $pump_count; $i++)
                                {
                                    $sDeviceNameDb = 'Pump Sequencer '.$i;
                                    $sNameDb =  $this->home_model->getDeviceName($i,'PS');

                                    if($sNameDb != '')
                                        $sDeviceNameDb  .= ' ('.$sNameDb.')';

                                    $sSelect = '';
                                    if(isset($aAllAnalogDevice[$d]) && $aAllAnalogDevice[$d] == $i.'_PS')
                                      $sSelect = 'selected="selected"';

                                        
                                    $sSelectDevice  .='<option value="'.$i.'_PS" '.$sSelect.'>'.$sDeviceNameDb.'</option>';
                                }
                            }

                            $sSelectDevice  .='</select>';

                            $strDirection1Checked = '';
                            $strDirection2Checked = '';
                            $strShow              = 'none;';

                            if($aAllANalogDeviceDirection[$d] != '' && $aAllANalogDeviceDirection[$d] != '0')
                             {
                                $strShow = '';
                              if($aAllANalogDeviceDirection[$d] == '1')
                                $strDirection1Checked = 'checked="checked"';
                              elseif($aAllANalogDeviceDirection[$d] == '2')
                                $strDirection2Checked = 'checked="checked"';

                             } 

                            echo '<tr>
                                  <td>'.$input.'</td>
                                  <td>&nbsp;</td>
                                  <td>'.$sSelectDevice.'&nbsp;
                                  <p id="sValveDirection_'.$d.'" style="display:'.$strShow.';"><input type="radio" name="sValveType_'.$d.'" value="1" '.$strDirection1Checked.'>&nbsp;Direction 1&nbsp;&nbsp;<input type="radio" name="sValveType_'.$d.'" value="2" '.$strDirection2Checked.'>&nbsp;Direction 2</p>
                                  </td>
                                  </tr>';
                          $d++;        
                        }
                  ?>     

                  <tr><td colspan="3"><input type="submit" name="command" value="Save" class="btn btn-success" ></td></tr> 
                </tbody>
                </table>
                </form>      
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
function showHideOption(iDeviceNumber)
{
  var sDeviceName = $("#sDeviceName_"+iDeviceNumber).val();
  if (sDeviceName.indexOf("_V") >= 0)
  {
      $("#sValveDirection_"+iDeviceNumber).show();
  }
  else
  {
    $("#sValveDirection_"+iDeviceNumber).hide();
  }
}
</script>
<hr>
<?php
$this->load->view('Footer');
?>
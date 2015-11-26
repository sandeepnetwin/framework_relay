
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
				<ol class="breadcrumb" style="float:left;">
						  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
						  <li class="active">Input</li>
				</ol>
			</div>
		</div><!-- /.row -->
		<div class="row">
			<div class="col-lg-12">			
			<?php if($sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Details saved successfully! 
              </div>
            <?php } ?>
          </div>
		</div>
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading" style="border-top-left-radius: 0px;border-top-right-radius: 0px;">
                <h3 class="panel-title" style="color:#FFF;">Assign Device To Input</h3>
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
									if($sRelays[$i] != '.' && $sRelays[$i] != '')
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
                            }

                            if($sValves != '')
                            {
                                for ($i=0;$i < $valve_count; $i++)
                                {
                                    if($sValves[$i] != '.' && $sValves[$i] != '')
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
							
							//Blower Devices
							if(isset($sExtra['BlowerNumber']) && $sExtra['BlowerNumber'] != 0 && $sExtra['BlowerNumber'] != '')
							{
								$iNumBlower	=	$sExtra['BlowerNumber'];
								for($i=0; $i<$iNumBlower; $i++)		
								{
									$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($i);
									$sDeviceNameDb = 'Blower '.($i+1);
                                    $sNameDb =  $this->home_model->getDeviceName($i,'B');

                                    if($sNameDb != '')
                                        $sDeviceNameDb  .= ' ('.$sNameDb.')';
									
									if(!empty($aBlowerDetails))
									{
										foreach($aBlowerDetails as $aBlower)
										{
											$sRelayDetails  =   unserialize($aBlower->light_relay_number);
											
											//Blower Operated Type and Relay
											$sRelayType     =   $sRelayDetails['sRelayType'];
											$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
											
											$sSelect = '';
											if(isset($aAllAnalogDevice[$d]) && $aAllAnalogDevice[$d] == $i.'_B')
											  $sSelect = 'selected="selected"';
											
											if($sRelayNumber	!=	'')
											{
												$sSelectDevice  .='<option value="'.$i.'_B" '.$sSelect.'>'.$sDeviceNameDb.'</option>';
											}
										}
									}
									
									
								}
								
							}
							
							//Light Devices
							if(isset($sExtra['LightNumber']) && $sExtra['LightNumber'] != 0 && $sExtra['LightNumber'] != '')
							{
								$iNumLight	=	$sExtra['LightNumber'];
								for($i=0; $i<$iNumLight; $i++)		
								{
									$aLightDetails  =   $this->home_model->getLightDeviceDetails($i);
									$sDeviceNameDb = 'Light '.($i+1);
                                    $sNameDb =  $this->home_model->getDeviceName($i,'L');

                                    if($sNameDb != '')
                                        $sDeviceNameDb  .= ' ('.$sNameDb.')';
									
									if(!empty($aLightDetails))
									{
										foreach($aLightDetails as $aLight)
										{
											$sRelayDetails  =   unserialize($aLight->light_relay_number);
											
											//Blower Operated Type and Relay
											$sRelayType     =   $sRelayDetails['sRelayType'];
											$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
											
											$sSelect = '';
											if(isset($aAllAnalogDevice[$d]) && $aAllAnalogDevice[$d] == $i.'_L')
											  $sSelect = 'selected="selected"';
											
											if($sRelayNumber	!=	'')
											{
												$sSelectDevice  .='<option value="'.$i.'_L" '.$sSelect.'>'.$sDeviceNameDb.'</option>';
											}
										}
									}
									
									
								}
								
							}
							
							//Heater Devices
							if(isset($sExtra['HeaterNumber']) && $sExtra['HeaterNumber'] != 0 && $sExtra['HeaterNumber'] != '')
							{
								$iNumHeater	=	$sExtra['HeaterNumber'];
								for($i=0; $i<$iNumHeater; $i++)		
								{
									$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($i);
									$sDeviceNameDb = 'Heater '.($i+1);
                                    $sNameDb =  $this->home_model->getDeviceName($i,'H');

                                    if($sNameDb != '')
                                        $sDeviceNameDb  .= ' ('.$sNameDb.')';
									
									if(!empty($aHeaterDetails))
									{
										foreach($aHeaterDetails as $aHeater)
										{
											$sRelayDetails  =   unserialize($aHeater->light_relay_number);
											
											//Blower Operated Type and Relay
											$sRelayType     =   $sRelayDetails['sRelayType'];
											$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
											
											$sSelect = '';
											if(isset($aAllAnalogDevice[$d]) && $aAllAnalogDevice[$d] == $i.'_H')
											  $sSelect = 'selected="selected"';
											
											if($sRelayNumber	!=	'')
											{
												$sSelectDevice  .='<option value="'.$i.'_H" '.$sSelect.'>'.$sDeviceNameDb.'</option>';
											}
										}
									}
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

                  <tr><td colspan="3"><span class="btn btn-grren"><input type="submit" name="command" value="Save" class="btn btn-success" onclick="return checkDevices();" ></span></td></tr> 
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

function checkDevices()
{
	var chk			=	'0';
	var aChkDevice	= Array();
	$("[id^='sDeviceName_']").each(function(){
		if($.inArray( $(this).val(), aChkDevice ) >= 0)
		{
			alert("Please select different devices for all inputs!")
			chk			=	'1';
			return false;
		}
		else
		{
			aChkDevice.push($(this).val());
		}
	});
	
	if(chk	==	'1')
		return false;
	else if(chk	==	'0')
	return true;
}
</script>

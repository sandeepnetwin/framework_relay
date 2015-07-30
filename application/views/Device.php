<?php
$this->load->view('Header');
$this->load->model('home_model');
$sDeviceFullName = '';
if($sDevice == 'R')
  $sDeviceFullName = 'Relay';
if($sDevice == 'P')
  $sDeviceFullName = 'Power Center';
if($sDevice == 'V')
  $sDeviceFullName = 'Valve';
if($sDevice == 'PS')
  $sDeviceFullName = 'Pump Sequencer';
?>
<link href="<?php echo site_url('assets/jquery-toggles-master/css/toggles.css'); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo site_url('assets/jquery-toggles-master/css/themes/toggles-light.css'); ?>">
<script src="<?php echo site_url('assets/jquery-toggles-master/toggles.min.js'); ?>" type="text/javascript"></script> 

    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> <?php echo $sDeviceFullName;?></li>
            </ol>
            <?php if($sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Details saved successfully! 
              </div>
            <?php } ?>
          </div>
        </div>
        <!-- /.row -->
        <?php if($sDevice == 'R') { //Relay Device Start  ?> 
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Relays List</h3>
              </div>
              <div class="table-responsive">
              <table class="table table-hover tablesorter">
                <thead>
                  <tr>
                    <th class="header">Relay <i class="fa fa-sort"></i></th>
                    <th class="header">Relay Name <i class="fa fa-sort"></i></th>
                    <th class="header">&nbsp;</th>
                    <th class="header">&nbsp;</th>
                    <th class="header">Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    
                    //START : Relay Device 
                    for ($i=0;$i < $relay_count; $i++)
                    {
                        $iRelayVal = $sRelays[$i];
                        $iRelayNewValSb = 1;
                        if($iRelayVal == 1)
                        {
                          $iRelayNewValSb = 0;
                        }
                        $sRelayVal = false;
                        if($iRelayVal)
                          $sRelayVal = true;
                        //$sRelayNameDb = get_device_name(1, $i);

                        $sRelayNameDb =  $this->home_model->getDeviceName($i,$sDevice);
                        if($sRelayNameDb == '')
                          $sRelayNameDb = 'Add Name';
                ?>
                      <tr>
                        <td>Relay <?php echo $i;?></td>
                        <td><a href="<?php echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');?>" ><?php echo $sRelayNameDb;?></a></td>
                        <td style="width:32px;"><span id="loading_relay_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
                        <td><div class="toggle-light" style="width:100px;">
                        <div>
                         <div class="toggle<?php echo $i;?> <?php echo $sRelayVal;?>"></div>
                        </div>
                        </div>

                       <script type="text/javascript">
                          var clickOff  = '';
                          <?php if($iActiveMode != '2') { ?>
                              $('.toggle<?php echo $i;?>').toggles({height:40,on:'<?php echo $sRelayVal;?>',drag: false, click: false});
                          <?php } else { ?> 
                              $('.toggle<?php echo $i;?>').toggles({height:40,on:'<?php echo $sRelayVal;?>'});
                          <?php } ?>    
                          
                          $( ".toggle<?php echo $i;?>" ).find( ".toggle-off" ).css({'padding-left':'10px','font-weight':'bold','font-size':'16px','color':'#B40404'});
                          $( ".toggle<?php echo $i;?>" ).find( ".toggle-on" ).css({'padding-left':'40px','font-weight':'bold','font-size':'16px'});
                          $('.toggle<?php echo $i;?>').on('toggle', function (e, active) {
                            var sStatus = '';
                            if (active) {
                                sStatus = 1;
                            } else {
                                sStatus = 0;
                            }
                            <?php if($iActiveMode == '2') { ?>
                              $("#loading_relay_<?php echo $i;?>").css('visibility','visible');
                             $.ajax({
                                type: "POST",
                                url: "<?php echo site_url('home/updateStatusOnOff');?>", 
                                data: {sName:'<?php echo $i;?>',sStatus:sStatus,sDevice:'<?php echo $sDevice;?>'},
                                success: function(data) {
                                  $("#loading_relay_<?php echo $i;?>").css('visibility','hidden');
                                }

                             });
                             <?php } else {  ?>
                              alert('You can perform this operation in manual mode only.');
                             <?php } ?> 
                          });
                       </script>
                       </td>
                        <td><a class="btn btn-primary btn-xs" href="<?php echo site_url('home/setPrograms/'.base64_encode($i).'/');?>">Programs</a></td>
                      </tr>
                <?php } ?>
                
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div><!-- /.row -->
        <?php } ?> <!-- END : Relay Device -->
        <?php if($sDevice == 'P') {  //Power center Device Start?>
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Power Center List</h3>
              </div>
              <div class="table-responsive">
              <table class="table table-hover tablesorter">
                <thead>
                  <tr>
                    <th class="header">Power Center <i class="fa fa-sort"></i></th>
                    <th class="header">Power Center Name <i class="fa fa-sort"></i></th>
                    <th class="header">&nbsp;</th>
                    <th class="header">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    //START : Power Center Device 
                    for ($i=0;$i < $power_count; $i++)
                    {
                        $iPowerCenterVal = $sPowercenter[$i];
                        $iPowerCenterNewValSb = 1;
                        if($iPowerCenterVal == 1)
                        {
                          $iPowerCenterNewValSb = 0;
                        }
                        $sPowerCenterVal = false;
                        if($iPowerCenterVal)
                          $sPowerCenterVal = true;
                        //$sPowerCenterNameDb = get_device_name(3, $i);

                        $sPowerCenterNameDb =  $this->home_model->getDeviceName($i,$sDevice);
                        if($sPowerCenterNameDb == '')
                          $sPowerCenterNameDb = 'Add Name';
                ?>
                      <tr>
                      <td>Power Center<?php echo $i;?></td>
                        <td><a href="<?php echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');?>" ><?php echo $sPowerCenterNameDb;?></a></td>
                        <td style="width:32px;"><span id="loading_power_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
                        <td><div class="toggle-light" style="width:100px;">
                        <div>
                         <div class="toggleP<?php echo $i;?> <?php echo $sPowerCenterVal;?>"></div>
                        </div>
                        </div>
                       <script type="text/javascript">
                          var clickOff  = '';
                          <?php if($iActiveMode != '2') { ?>
                              $('.toggleP<?php echo $i;?>').toggles({height:40,on:'<?php echo $sPowerCenterVal;?>',drag: false, click: false});
                          <?php } else { ?> 
                              $('.toggleP<?php echo $i;?>').toggles({height:40,on:'<?php echo $sPowerCenterVal;?>'});
                          <?php } ?>    
                          
                          $( ".toggleP<?php echo $i;?>" ).find( ".toggle-off" ).css({'padding-left':'10px','font-weight':'bold','font-size':'16px','color':'#B40404'});
                          $( ".toggleP<?php echo $i;?>" ).find( ".toggle-on" ).css({'padding-left':'40px','font-weight':'bold','font-size':'16px'});
                          $('.toggleP<?php echo $i;?>').on('toggle', function (e, active) {
                            
                            var sStatus = '';
                            if (active) {
                                sStatus = 1;
                            } else {
                                sStatus = 0;
                            }
                            <?php if($iActiveMode == '2') { ?>
                             $("#loading_power_<?php echo $i;?>").css('visibility','visible');
                             $.ajax({
                                type: "POST",
                                url: "<?php echo site_url('home/updateStatusOnOff');?>", 
                                data: {sName:'<?php echo $i;?>',sStatus:sStatus,sDevice:'<?php echo $sDevice;?>'},
                                success: function(data) {
                                  $("#loading_power_<?php echo $i;?>").css('visibility','hidden');
                                }

                             });
                             <?php } else {  ?>
                              alert('You can perform this operation in manual mode only.');
                             <?php } ?> 
                          });
                       </script>
                       </td>
                      </tr>
                <?php } ?>

                
                
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div><!-- /.row -->
        <?php } ?> <!-- END : Power Center Device -->

        <?php if($sDevice == 'V') { // Valve Start ?>
        <link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
        <!--<link href="<?php echo site_url('assets/switchy/bootstrap.min.css'); ?>" rel="stylesheet" />-->
        <script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>
                <div class="row">
                  <div class="col-lg-12">
                    <div class="panel panel-primary">
                      <div class="panel-heading">
                        <h3 class="panel-title">Valve List</h3>
                      </div>
                      <div class="table-responsive">
                      <table class="table table-hover tablesorter">
                        <thead>
                          <tr>
                            <th class="header">Valve <i class="fa fa-sort"></i></th>
                            <th class="header">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                            //START : Valve Device 
                            $j=0;
                            for ($i=0;$i < $valve_count; $i++)
                            {
                                if($j != 0 )
                                    $j++;
                                
                                $iValvesVal = $sValves[$i];
                                $iValvesNewValSb1 = 1;
                                $iValvesNewValSb2 = 2 ;
                                if($iValvesVal == 1)
                                {
                                  $iValvesNewValSb1 = 0;
                                }
                                if($iValvesVal == 2)
                                {
                                  $iValvesNewValSb2 = 1;
                                }
                                $sValvesVal1 = false;
                                $sValvesVal2 = false;
                                if($iValvesVal == 1)
                                  $sValvesVal1 = true;
                                if($iValvesVal == 2)
                                  $sValvesVal2 = true;
                                //$sValvesNameDb = get_device_name(3, $i);

                                $sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice);
                                if($sValvesNameDb == '')
                                  $sValvesNameDb = 'Add Name';
                                $aPositionName =  $this->home_model->getPositionName($i,$sDevice);
                        ?>
                              <tr>
                                  <td>Valve <?php echo $i;?>&nbsp;(<?php echo $j;?>-<?php echo ++$j;?>)<br /><br /><a href="<?php echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');?>" ><?php echo $sValvesNameDb;?></a><br /><br /><a href="<?php echo site_url('home/positionName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');?>">Edit Position</a></td>
                                
                                <td>
                                    <div class="span1 valve-<?php echo $i?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php if($aPositionName[0] == ''){ echo 'Spa';} else { echo $aPositionName[0];} ?></div>
                                    <div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
                                    <select id='switch-me-<?php echo $i;?>'>
                                    <option value='1' <?php if($iValvesVal == '1') { echo 'selected="selected"';} ?>>Spa</option>
                                    <option value='0' <?php if($iValvesVal == '0' || $iValvesVal == '') { echo 'selected="selected"';} ?>></option>
                                    <option value='2' <?php if($iValvesVal == '2') { echo 'selected="selected"';} ?>>Pool</option>
                                    </select>
                                    <div class="valve-<?php echo $i?>" value="0" id="off-<?php echo $i;?>" style="color: red;font-weight: bold;width: 0; margin-left: 40px; margin-top: 2px; cursor: pointer;">
                                        OFF
</div>                              </div>
                                    <div class="span1 valve-<?php echo $i?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php if($aPositionName[1] == ''){ echo 'Pool';} else { echo $aPositionName[1];} ?></div>
                                  <script type="text/javascript">
                                  $(function()
                                  {
                                    var bgColor = '#E8E8E8';
                                    <?php if($iValvesVal == '1' || $iValvesVal == '2') { ?>
                                            bgColor = '#45A31F';
                                    <?php } else { ?>
                                            bgColor = '#E8E8E8';
                                    <?php } ?>
                                    $('#switch-me-<?php echo $i;?>').switchy();
                                    
                                    $('.valve-<?php echo $i?>').on('click', function(event){
                                        //event.preventDefault();
                                        //return false;
                                        $('#switch-me-<?php echo $i;?>').val($(this).attr('value')).change();
                                    });
                                    
                                    $('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
                                            backgroundColor: bgColor
                                        });
                                    
                                    $('#switch-me-<?php echo $i;?>').on('change', function(event)
                                    {
                                        //event.preventDefault();
                                        //return false;
                                        // Animate Switchy Bar background color
                                        var bgColor = '#E8E8E8';

                                        if ($(this).val() == '1' || $(this).val() == '2')
                                        {
                                            bgColor = '#45A31F';
                                        } 
                                        $('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
                                            backgroundColor: bgColor
                                        });
                                        
                                        <?php if($iActiveMode == '2') { ?>
                                            //$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
                                            $.ajax({
                                                type: "POST",
                                                url: "<?php echo site_url('home/updateStatusOnOff');?>", 
                                                data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
                                                success: function(data) {
                                                //$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
                                                }

                                            });
                                            <?php } else {  ?>
                                            alert('You can perform this operation in manual mode only.');
                                            <?php } ?> 
                                        
                                       });
                                    });
                               </script>
                               </td>
                               
                              </tr>
                        <?php } ?>
                      </tbody>
              </table>
            </div>
            </div>
          </div>
        </div><!-- /.row -->
        <?php } ?> <!-- END : Valve Device -->  
        <?php if($sDevice == 'PS') {  // START : Pump Device?>
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Pump Sequencer List</h3>
              </div>
              <div class="table-responsive">
              <table class="table table-hover tablesorter">
                <thead>
                  <tr>
                    <th class="header">Pump <i class="fa fa-sort"></i></th>
                    <th class="header">Pump Name <i class="fa fa-sort"></i></th>
                    <th class="header">&nbsp;</th>
                    <th class="header">&nbsp;</th>
                    <th class="header">Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    
                    //START : Relay Device 
                    for ($i=0;$i < $pump_count; $i++)
                    {
                        $iPumpVal = $sPump[$i];
                        $iPumpNewValSb = 1;
                        if($iPumpVal == 1)
                        {
                          $iPumpNewValSb = 0;
                        }
                        $sPumpVal = false;
                        if($iPumpVal)
                          $sPumpVal = true;
                        //$sRelayNameDb = get_device_name(1, $i);

                        $sPumpNameDb =  $this->home_model->getDeviceName($i,$sDevice);
                        if($sPumpNameDb == '')
                          $sPumpNameDb = 'Add Name';
                ?>
                      <tr>
                        <td>Pump Sequencer <?php echo $i;?></td>
                        <td><a href="<?php echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');?>" ><?php echo $sPumpNameDb;?></a></td>
                        <td style="width:32px;"><span id="loading_pump_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
                        <td><div class="toggle-light" style="width:100px;">
                        <div>
                         <div class="togglePump<?php echo $i;?> <?php echo $sPumpVal;?>"></div>
                        </div>
                        </div>

                       <script type="text/javascript">
                          var clickOff  = '';
                          <?php if($iActiveMode != '2') { ?>
                              $('.togglePump<?php echo $i;?>').toggles({height:40,on:'<?php echo $sPumpVal;?>',drag: false, click: false});
                          <?php } else { ?> 
                              $('.togglePump<?php echo $i;?>').toggles({height:40,on:'<?php echo $sPumpVal;?>'});
                          <?php } ?>    
                          
                          $( ".togglePump<?php echo $i;?>" ).find( ".toggle-off" ).css({'padding-left':'10px','font-weight':'bold','font-size':'16px','color':'#B40404'});
                          $( ".togglePump<?php echo $i;?>" ).find( ".toggle-on" ).css({'padding-left':'40px','font-weight':'bold','font-size':'16px'});
                          $('.togglePump<?php echo $i;?>').on('toggle', function (e, active) {
                            var sStatus = '';
                            if (active) {
                                sStatus = 1;
                            } else {
                                sStatus = 0;
                            }
                            <?php if($iActiveMode == '2') { ?>
                              $("#loading_pump_<?php echo $i;?>").css('visibility','visible');
                             $.ajax({
                                type: "POST",
                                url: "<?php echo site_url('home/updateStatusOnOff');?>", 
                                data: {sName:'<?php echo $i;?>',sStatus:sStatus,sDevice:'<?php echo $sDevice;?>'},
                                success: function(data) {
                                  $("#loading_pump_<?php echo $i;?>").css('visibility','hidden');
                                }

                             });
                             <?php } else {  ?>
                              alert('You can perform this operation in manual mode only.');
                             <?php } ?> 
                          });
                       </script>
                       </td>
                        <td><a class="btn btn-primary btn-xs" href="<?php echo site_url('home/pumpConfigure/'.base64_encode($i).'/');?>">Configure</a></td>
                      </tr>
                <?php } ?>
                
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div><!-- /.row -->
        <?php } ?> <!-- END : Pump Device -->
      </div><!-- /#page-wrapper -->


    
    
<hr>
<?php
$this->load->view('Footer');
?>
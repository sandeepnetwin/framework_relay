<?php
$this->load->view('Header');
$this->load->model('home_model');
$sAccess 		= '';
$sModule	    = '';
$sDeviceFullName = '';
if($sDevice == 'R')
{
  $sDeviceFullName 	= '24V AC Relay';
  $sModule	    	= 2;
}
if($sDevice == 'P')
{
  $sDeviceFullName = '12V DC Power Center Relay';
  $sModule	       = 3;
}
if($sDevice == 'V')
{
  $sDeviceFullName = 'Valve';
  $sModule	       = 8;
}
if($sDevice == 'PS')
{
  $sDeviceFullName = 'Pump Sequencer';
  $sModule	       = 9;
}
if($sDevice == 'T')
{
  $sDeviceFullName = 'Temperature sensor';
  $sModule	       = 10;
}

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
?>
<link href="<?php echo site_url('assets/jquery-toggles-master/css/toggles.css'); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo site_url('assets/jquery-toggles-master/css/themes/toggles-light.css'); ?>">
<script src="<?php echo site_url('assets/jquery-toggles-master/toggles.min.js'); ?>" type="text/javascript"></script> 
<script type="text/javascript">
	$(document).ready(function() {
		$(".displayMore").hide();
		$(".more").click(function() {
			var txt	=	$(this).html();
			if(txt == 'More +')
				txt = 'More -';
			else
				txt = 'More +';
			
			$(".displayMore").toggle('slow',function() {	
				$(".more").html(txt);
			});
			
		});
	});
	function saveDevicePower(sDeviceID,sDevice,sPowerValue)
	{
		 $.ajax({
			type: "POST",
			url: "<?php echo site_url('home/saveDevicePower');?>", 
			data: {sDeviceID:sDeviceID,sDevice:sDevice,sPowerValue:sPowerValue},
			success: function(data) {
			}

		 });
	}
	function saveDeviceMainType(sDevice,sDeviceID,sType)
	{
		$.ajax({
			type: "POST",
			url: "<?php echo site_url('home/saveDeviceMainType');?>", 
			data: {sDeviceID:sDeviceID,sDevice:sDevice,sType:sType},
			success: function(data) {
			}

		 });
	}
	function RemoveValveRelays(iValaveNumber)
	{
		var cnf	=	confirm("Are you sure, you want to remove relays?");
		if(cnf)
		{
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/removeValveRelays');?>", 
				data: {iValaveNumber:iValaveNumber},
				success: function(resp) {
					location.reload(); 
				}

			 });
		}
	}
</script>
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
        <?php if($sAccess == '1' || $sAccess == '2') { ?> 		
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">24V AC Relay List</h3>
              </div>
              <div class="table-responsive">
              <table class="table table-hover tablesorter">
                <thead>
                  <tr>
                    <th class="header">Relay <i class="fa fa-sort"></i></th>
                    <th class="header" width="20px;">Relay Name <i class="fa fa-sort"></i></th>
                    <th class="header">&nbsp;</th>
                    <th class="header">&nbsp;</th>
                    <th class="header">Action</th>
                    <th class="header">Maximum run time</th>
					
                  </tr>
                </thead>
                <tbody>
                <?php
                    //START : Relay Device 
					$j=0;
					//START : First Show all Relays assigned to Valves.
					for ($i=0;$i < $relay_count; $i++)
                    {
						$iRelayVal = $sRelays[$i];
						if($iRelayVal == '' || $iRelayVal =='.') 
						{
							$sRelayNameDb =  $this->home_model->getDeviceName($i,$sDevice);
							if($sRelayNameDb == '')
							  $sRelayNameDb = 'Add Name';
					?>
						
							<tr <?php if($j>=1){ echo 'class="displayMore"';}?>>
							<td>Relay <?php echo $i;?></td>
							<td><a href="<?php if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';} ?>" ><?php echo $sRelayNameDb;?></a></td>
							<td style="width:32px;"><span id="loading_relay_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
							<td>
							<strong style="color:#FF0000">Output is Assigned to Valve.</strong>
							</td>
							<td><strong style="color:#428BCA">-</strong></td>
							<td><strong style="color:#428BCA">-</strong></td>     
						</tr>	
						<?php 
							$j++;
						}
					}
					if($j > 0)
					{
						echo '<tr>
								<th class="header more" colspan="7" style="text-align: center; color:#428BCA; cursor:pointer;">More +</th>
							 </tr>';
					}
					//END : First Show all Relays assigned to Valves.
					
                    for ($i=0;$i < $relay_count; $i++)
                    {
						$iRelayVal = $sRelays[$i];
						if($iRelayVal != '' && $iRelayVal !='.') 
                        {
							
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
							
							$sDeviceTime =  $this->home_model->getDeviceTime($i,$sDevice);
							if($sDeviceTime == '')
							  $sDeviceTime = 'Add Time';
							else
							  $sDeviceTime .= ' Minute';
						  
							$iRelayProgramCount = $this->home_model->getProgramCount($i,$sDevice);
							$iPower	 = $this->home_model->getDevicePower($i,$sDevice);
							$sMainType =	$this->home_model->getDeviceMainType($i,$sDevice);
						?>
							<tr>
							<td>Relay <?php echo $i;?></td>
							<td><a href="<?php if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';} ?>" ><?php echo $sRelayNameDb;?></a><br><br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="0" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> >&nbsp;Other<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Spa<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Pool</td>
							<td style="width:32px;"><span id="loading_relay_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
							<td>
								<div class="toggle-light" style="width:100px;">
									<div>
										<div class="toggle<?php echo $i;?> <?php echo $sRelayVal;?>"></div>
									</div>
								</div>

							<script type="text/javascript">
							  var clickOff  = '';
							  <?php if($iActiveMode != '2' || $sAccess == '1') { ?>
								  $('.toggle<?php echo $i;?>').toggles({height:40,on:'<?php echo $sRelayVal;?>',drag: false, click: false});
								  <?php if($sAccess == '2') { ?>
								  $('.toggle<?php echo $i;?>').click(function(){
									var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
									if(bConfirm)
									{
										$("#loading_relay_<?php echo $i;?>").css('visibility','visible');
										$.ajax({
											type: "POST",
											url: "<?php echo site_url('analog/changeMode');?>", 
											data: {iMode:'2'},
											success: function(data) {
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('home/updateStatusOnOff');?>", 
													data: {sName:'<?php echo $i;?>',sStatus:1,sDevice:'<?php echo $sDevice;?>'},
													success: function(data) {
													  $("#loading_relay_<?php echo $i;?>").css('visibility','hidden');
													  location.reload();
													}
												});
											}
										});
									}
								  });
								  <?php } ?>
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
							<td>
								<a class="btn btn-primary btn-xs" href="<?php if($sAccess == '2') { echo site_url('home/setPrograms/'.base64_encode($i).'/');} else { echo 'javascript:void(0);';}?>">Programs</a>&nbsp&nbsp<a class="btn btn-primary btn-xs" href="<?php if($sAccess == '2') { echo site_url('home/setPrograms/'.base64_encode($i).'/');} else { echo 'javascript:void(0);';}?>" style="width: 90px;"><?php echo $iRelayProgramCount;?><?php if($iRelayProgramCount == 1 || $iRelayProgramCount == 0){ echo ' Program';}else{ echo ' Programs';}?></a>
							</td>
							<td>
							<a class="btn btn-primary btn-xs" href="<?php if($sAccess == '2') {echo site_url('home/addTime/'.base64_encode($i).'/'.  base64_encode($sDevice));} else { echo 'javascript:void(0);';}?>"><?php echo $sDeviceTime;?></a>
							</td>     
							
						  </tr>
					<?php }
					} ?>
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div><!-- /.row -->
		<?php } ?>
        <?php } ?> <!-- END : Relay Device -->
        <?php if($sDevice == 'P') {  //Power center Device Start?>
		<?php if($sAccess == '1' || $sAccess == '2') 
			  { 
		?>
		<div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">12V DC Power Center Relays List</h3>
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
						
						$iPower	 	= $this->home_model->getDevicePower($i,$sDevice);
						$sMainType	= $this->home_model->getDeviceMainType($i,$sDevice);
                ?>
                      <tr>
                      <td>Power Center<?php echo $i;?></td>
                        <td><a href="<?php if($sAccess == '1'){echo 'javascript:void(0);';} else if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');}?>" ><?php echo $sPowerCenterNameDb;?></a><br><br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="0" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> >&nbsp;Other<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Spa<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Pool</td>
                        <td style="width:32px;"><span id="loading_power_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
                        <td><div class="toggle-light" style="width:100px;">
                        <div>
                         <div class="toggleP<?php echo $i;?> <?php echo $sPowerCenterVal;?>"></div>
                        </div>
                        </div>
                       <script type="text/javascript">
                          var clickOff  = '';
                          <?php if($iActiveMode != '2' || $sAccess == '1') { ?>
                              $('.toggleP<?php echo $i;?>').toggles({height:40,on:'<?php echo $sPowerCenterVal;?>',drag: false, click: false});
							  <?php if($sAccess == '2') { ?>
							  $('.toggleP<?php echo $i;?>').click(function(){
								var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
								if(bConfirm)
								{
									$("#loading_power_<?php echo $i;?>").css('visibility','visible');
									$.ajax({
										type: "POST",
										url: "<?php echo site_url('analog/changeMode');?>", 
										data: {iMode:'2'},
										success: function(data) {
											$.ajax({
												type: "POST",
												url: "<?php echo site_url('home/updateStatusOnOff');?>", 
												data: {sName:'<?php echo $i;?>',sStatus:1,sDevice:'<?php echo $sDevice;?>'},
												success: function(data) {
												  $("#loading_power_<?php echo $i;?>").css('visibility','hidden');
												  location.reload();
												}
											});
										}
									});
								}
							  });
							  <?php } ?>
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
			<?php } ?>
        <?php } ?> <!-- END : Power Center Device -->

        <?php if($sDevice == 'V') // Valve Start 
		{ 
			$iValveNumber = $extra['ValveNumber']; ?>
		 <?php if($sAccess == '1' || $sAccess == '2') { ?>
		<script>
		var iActiveMode = '<?php echo $iActiveMode;?>';
		var sAccess 	= '<?php echo $sAccess;?>';
		</script>
        <link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
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
                          </tr>
                        </thead>
                        <tbody>
						
                        <?php
							if( $iValveNumber == 0 || $iValveNumber == '' )
							{ ?>
								
								<tr>
									<td>
										<span style="color:red">Please add number of Vavles in the <a href="<?php echo base_url('home/setting/');?>">Settings</a> Page!</span>
									</td>
								</tr>
								
							<?php 
							}
							else
							{
								//START : Valve Device 
								$arrValve	=	array(0,1,2,3,4,5,6,7);
								$j=0;
								$remainigCount	=	0;
								$chkValve		=	0;
								if(!empty($ValveRelays))
								{
									foreach($ValveRelays as $valve)
									{
										$i = $valve->device_number;
										$j = $i *2;
										
										unset($arrValve[$i]);
										
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
									  
										$aPositionName   =  $this->home_model->getPositionName($i,$sDevice);
										$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice));
										$iPower	 	     =  $this->home_model->getDevicePower($i,$sDevice);
										$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice);
								?>
									  <tr>
										<td>
											<div class="col-lg-3">Valve <?php echo $i;?>&nbsp;(<?php echo $j;?>-<?php echo ($j+1);?>)<br /><br /></div>
											<div class="col-lg-3"><a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>" ><?php echo $sValvesNameDb;?></a><br /><br /><a href="<?php if($sAccess == 2) { echo site_url('home/positionName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>">Edit Position</a><br /><br /><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="0" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> >&nbsp;Other<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Spa<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Pool</div>
											<?php if($iValvesVal != '' && $iValvesVal != '.' && !empty($aRelayNumber)) { ?>
											<div class="col-lg-3">                                    
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
													if(sAccess == 2)
													{
														if(iActiveMode != 2)
														{
															var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
															if(bConfirm)
															{
																$.ajax({
																	type: "POST",
																	url: "<?php echo site_url('analog/changeMode');?>", 
																	data: {iMode:'2'},
																	success: function(data) {
																	}
																});
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
															
																
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
																$.ajax({
																	type: "POST",
																	url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																	data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																	success: function(data) {
																	//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																	location.reload();
																	}

																});
															}
														}
														else											
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
														
															
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																success: function(data) {
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																}

															});
														}
													}
												});
											});
									   </script>
									   </div>
										<?php } else { ?>
											<div class="col-lg-3"><strong style="color:#FF0000">Valve is not configured.</strong></div>
										<?php } ?>
										
										<div class="col-lg-3"><a class="btn btn-primary btn-xs" href="<?php echo base_url('home/valveRelays/'.base64_encode($i).'/'.base64_encode($sDevice));?>">Relays</a>	
										<?php if(!empty($aRelayNumber)) { ?>
										&nbsp;&nbsp;<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="return RemoveValveRelays('<?php echo $i;?>')">Remove Relays</a>		
										<?php } ?>
										</div>
										</td>
									</tr>
									<?php
										$chkValve++;
									}
								}
								$remainigCount	=	$iValveNumber - $chkValve;
								//for ($i=0;$i < $valve_count; $i++)	
								//for ($i=0;$i < $remainigCount ; $i++)
								foreach($arrValve as $i)	
								{
									if($remainigCount == 0)	
										break;
									
									$remainigCount--;
									
									$j = $i *2;
									
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
								  
									$aPositionName   =  $this->home_model->getPositionName($i,$sDevice);
									$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice));
									$iPower	 	     =  $this->home_model->getDevicePower($i,$sDevice);
									$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice);
							?>
								  <tr>
									<td>
										<div class="col-lg-3">Valve <?php echo $i;?>&nbsp;(<?php echo $j;?>-<?php echo ($j+1);?>)<br /><br /></div>
										<div class="col-lg-3"><a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>" ><?php echo $sValvesNameDb;?></a><br /><br /><a href="<?php if($sAccess == 2) { echo site_url('home/positionName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>">Edit Position</a><br /><br /><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="0" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> >&nbsp;Other<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Spa<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Pool</div>
										<?php if($iValvesVal != '' && $iValvesVal != '.' && !empty($aRelayNumber)) { ?>
										<div class="col-lg-3">                                    
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
												if(sAccess == 2)
												{
													if(iActiveMode != 2)
													{
														var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
														if(bConfirm)
														{
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('analog/changeMode');?>", 
																data: {iMode:'2'},
																success: function(data) {
																}
															});
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
														
															
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
															$.ajax({
																type: "POST",
																url: "<?php echo site_url('home/updateStatusOnOff');?>", 
																data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
																success: function(data) {
																//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
																location.reload();
																}

															});
														}
													}
													else											
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
													
														
														//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
														$.ajax({
															type: "POST",
															url: "<?php echo site_url('home/updateStatusOnOff');?>", 
															data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
															success: function(data) {
															//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
															}

														});
													}
												}
											});
										});
								   </script>
								   </div>
									<?php } else { ?>
										<div class="col-lg-3"><strong style="color:#FF0000">Valve is not configured.</strong></div>
									<?php } ?>
									
									<div class="col-lg-3"><a class="btn btn-primary btn-xs" href="<?php echo base_url('home/valveRelays/'.base64_encode($i).'/'.base64_encode($sDevice));?>">Relays</a>	
									<?php if(!empty($aRelayNumber)) { ?>
									&nbsp;&nbsp;<a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="return RemoveValveRelays('<?php echo $i;?>')">Remove Relays</a>		
									<?php } ?>
									</div>
									</td>
								</tr>
							<?php 
								}
							}	?>
                      </tbody>
              </table>
            </div>
            </div>
           
          </div>
        </div><!-- /.row -->
		 <?php }} ?> <!-- END : Valve Device -->  
        <?php if($sDevice == 'PS') {  // START : Pump Device
				$iPumpsNumber	=	$extra['PumpsNumber'];
		?>
		<?php if($sAccess == 1 || $sAccess == 2) { ?>
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
					<th class="header">&nbsp;</th>
                    <th class="header">Action</th>
				  </tr>
                </thead>
                <tbody>
                <?php
                    
					if( $iPumpsNumber == 0 || $iPumpsNumber == '' )
					{ ?>
						
						<tr>
							<td>
								<span style="color:red">Please add number of Pumps in the <a href="<?php echo base_url('home/setting/');?>">Settings</a> Page!</span>
							</td>
						</tr>
						
					<?php 
					}
					else
					{
						//START : Pump Device 
						
						$arrPump		=	array(0,1,2);
						$remainigCount	=	0;
						$chkPump		=	0;
						if(!empty($Pumps))
						{
							foreach($Pumps as $pump)
							{
								
							$i= $pump->pump_number;
							unset($arrPump[$i]);		
							//for ($i=0;$i < $pump_count; $i++)
							//{
							$iPumpVal = $sPump[$i];
							/* $iPumpNewValSb = 1;
							if($iPumpVal == 1)
							{
							  $iPumpNewValSb = 0;
							}
							$sPumpVal = false;
							if($iPumpVal)
							  $sPumpVal = true; */
							//$sRelayNameDb = get_device_name(1, $i);
						
							$sPumpNameDb =  $this->home_model->getDeviceName($i,$sDevice);
							if($sPumpNameDb == '')
							  $sPumpNameDb = 'Add Name';
							
							//$iPower	 = $this->home_model->getDevicePower($i,$sDevice);
							
							//$sPowercenter = '01000000'		;
							//START : Getting assigned relay status from the Server.	
							//Details of Pump
							$aPumpDetails = $this->home_model->getPumpDetails($i);
							$sPumpType		=	'';
							if(!empty($aPumpDetails))
							{
								foreach($aPumpDetails as $aResultEdit)
								{
									$sPumpType    = $aResultEdit->pump_type;//Pump Type
									$sPumpRelay   = $aResultEdit->relay_number;//Assigned Relay Number
									$sPumpRelay1   = $aResultEdit->relay_number_1;//Assigned Relay Number
									if($sPumpType == '12')
									{
										$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
									}
									else if($sPumpType == '24')
									{
										$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
									}
									else if($sPumpType == '2Speed')
									{
										$sPumpSubType    = $aResultEdit->pump_sub_type;//Pump SUB Type
										$sStatus2Speed   = $aResultEdit->status;//Pump SUB Type
										
										
										if($sStatus2Speed == '0')
										{
											$iPumpVal        = $sStatus2Speed;
										}
										else if($sStatus2Speed == '1')											
										{
											if($sPumpSubType == '12')
											{
												$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
											}
											else if($sPumpSubType == '24')
											{
												$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
											}
										}
										else if($sStatus2Speed == '2')											
										{
											if($sPumpSubType == '12')
											{
												$iPumpVal = $sPowercenter[$sPumpRelay1]; //Taken the status
											}
											else if($sPumpSubType == '24')
											{
												$iPumpVal = $sRelays[$sPumpRelay1];//Taken the status
											}
										}
									}
									else if(preg_match('/Emulator/',$sPumpType))
									{
										 $iPumpVal = $sPump[$i];
									}
								}
							}	//END : Getting assigned relay status from the Server.
							$sPumpVal = false;
							if($iPumpVal)
							  $sPumpVal = true;
						  
							$sMainType	 = $this->home_model->getDeviceMainType($i,$sDevice);
					?>
						  <tr>
							<td>Pump Sequencer <?php echo $i;?></td>
							<td><a href="<?php if($sAccess == 2) {echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');} else {echo 'javascript:void(0);';}?>" ><?php echo $sPumpNameDb;?></a><br /><br /><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="0" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> >&nbsp;Other<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Spa<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Pool</td>
							<td style="width:32px;"><span id="loading_pump_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
							<td ><div id="pumpResponse_<?php echo $i;?>"></div></td>
							<td >
							<?php if($sPumpType == '2Speed') { 
							
							?>
							
							<script>
							var iActiveMode = '<?php echo $iActiveMode;?>';
							var sAccess 	= '<?php echo $sAccess;?>';
							</script>
							<link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
							<script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
							<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
							<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>
							<div class="span1 pump-<?php echo $i?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 1</div>
							<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
							<select id='switch-me-<?php echo $i;?>'>
							<option value='1' <?php if($sStatus2Speed == '1') { echo 'selected="selected"';} ?>>Spa</option>
							<option value='0' <?php if($sStatus2Speed == '0' || $iPumpVal == '') { echo 'selected="selected"';} ?>></option>
							<option value='2' <?php if($sStatus2Speed == '2') { echo 'selected="selected"';} ?>>Pool</option>
							</select>
							<div class="pump-<?php echo $i?>" value="0" id="off-<?php echo $i;?>" style="color: red;font-weight: bold;width: 0; margin-left: 40px; margin-top: 2px; cursor: pointer;">
								OFF
	</div>                              </div>
							<div class="span1 pump-<?php echo $i?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 2</div>
						  <script type="text/javascript">
						  
						  $(function()
						  {
							  var bgColor = '#E8E8E8';
								<?php if($iPumpVal == '1' || $iPumpVal == '2') { ?>
										bgColor = '#45A31F';
								<?php } else { ?>
										bgColor = '#E8E8E8';
								<?php } ?>
								
								$('#switch-me-<?php echo $i;?>').switchy();
								
								$('.pump-<?php echo $i?>').on('click', function(event){
									//event.preventDefault();
									//return false;
									$('#switch-me-<?php echo $i;?>').val($(this).attr('value')).change();
								});
								
								$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
										backgroundColor: bgColor
									});
								
								$('#switch-me-<?php echo $i;?>').on('change', function(event)
								{
									if(sAccess == 2)
									{
										if(iActiveMode != 2)
										{
											var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
											if(bConfirm)
											{
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('analog/changeMode');?>", 
													data: {iMode:'2'},
													success: function(data) {
													}
												});
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
											
												
												//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('home/updateStatusOnOff');?>", 
													data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
													success: function(data) {
													//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
													location.reload();
													}

												});
											}
										}
										else											
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
										
											
											//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
											$.ajax({
												type: "POST",
												url: "<?php echo site_url('home/updateStatusOnOff');?>", 
												data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
												success: function(data) {
												//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
												}

											});
										}
									}
								});
							});
					   </script>
					   
					   
							
							<?php } else if($sPumpType != '') {?>
							<div class="toggle-light" style="width:100px;">
							<div>
							 <div class="togglePump<?php echo $i;?> <?php echo $sPumpVal;?>"></div>
							</div>
							</div>
						   <script type="text/javascript">
						   <?php if($iPumpVal > 0 && preg_match('/Emulator/',$sPumpType)) { ?>
						  $(document).ready(function() {
							setInterval( function() {
								$.getJSON('<?php echo site_url('cron/pumpResponseLatest/');?>', {iPumpID: "<?php echo $i;?>"}, function(json) {
									$("#pumpResponse_<?php echo $i;?>").html(json);
								});
								},30000);
						  });	
						   <?php } ?>
							  var clickOff  = '';
							  <?php if($iActiveMode != '2' || $sAccess == '1') { ?>
								  $('.togglePump<?php echo $i;?>').toggles({height:40,on:'<?php echo $sPumpVal;?>',drag: false, click: false});
								  <?php if($sAccess == 2) { ?>
								  $('.togglePump<?php echo $i;?>').click(function(){
									var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
									if(bConfirm)
									{
										$("#loading_pump_<?php echo $i;?>").css('visibility','visible');
										$.ajax({
											type: "POST",
											url: "<?php echo site_url('analog/changeMode');?>", 
											data: {iMode:'2'},
											success: function(data) {
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('home/updateStatusOnOff');?>", 
													data: {sName:'<?php echo $i;?>',sStatus:1,sDevice:'<?php echo $sDevice;?>'},
													success: function(data) {
													  $("#loading_pump_<?php echo $i;?>").css('visibility','hidden');
													  location.reload();
													}
												});
											}
										});
									}
								  });
								<?php } ?>
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
							<?php } ?>
						   </td>
							<td><a class="btn btn-primary btn-xs" href="<?php if($sAccess == 2){echo site_url('home/pumpConfigure/'.base64_encode($i).'/');}else{echo 'javscript:void(0);';}?>">Configure</a>&nbsp;&nbsp;
								<a class="btn btn-primary btn-xs" href="<?php if($sAccess == 2){ echo site_url('home/setProgramsPump/'.base64_encode($i).'/');} else {echo 'javascript:void(0);';}?>">Programs</a>
							</td>
						</tr>
						<?php
								$chkPump++;
							}
						}
						//echo $chkPump.'>>'.$iPumpsNumber;
						
						$remainigCount	=	$iPumpsNumber - $chkPump;
						//for ($i=0;$i < $valve_count; $i++)	
						//for ($i=0;$i < $remainigCount ; $i++)
						foreach($arrPump as $i)	
						{
							if($remainigCount == 0)	
								break;
							
							$remainigCount--;
									
						//for ($i=0;$i < $pump_count; $i++)
						//{
							$iPumpVal = $sPump[$i];
							/* $iPumpNewValSb = 1;
							if($iPumpVal == 1)
							{
							  $iPumpNewValSb = 0;
							}
							$sPumpVal = false;
							if($iPumpVal)
							  $sPumpVal = true; */
							//$sRelayNameDb = get_device_name(1, $i);
						
							$sPumpNameDb =  $this->home_model->getDeviceName($i,$sDevice);
							if($sPumpNameDb == '')
							  $sPumpNameDb = 'Add Name';
							
							//$iPower	 = $this->home_model->getDevicePower($i,$sDevice);
							
							//$sPowercenter = '01000000'		;
							//START : Getting assigned relay status from the Server.	
							//Details of Pump
							$aPumpDetails = $this->home_model->getPumpDetails($i);
							$sPumpType		=	'';
							if(!empty($aPumpDetails))
							{
								foreach($aPumpDetails as $aResultEdit)
								{
									$sPumpType    = $aResultEdit->pump_type;//Pump Type
									$sPumpRelay   = $aResultEdit->relay_number;//Assigned Relay Number
									$sPumpRelay1   = $aResultEdit->relay_number_1;//Assigned Relay Number
									if($sPumpType == '12')
									{
										$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
									}
									else if($sPumpType == '24')
									{
										$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
									}
									else if($sPumpType == '2Speed')
									{
										$sPumpSubType    = $aResultEdit->pump_sub_type;//Pump SUB Type
										$sStatus2Speed   = $aResultEdit->status;//Pump SUB Type
										
										
										if($sStatus2Speed == '0')
										{
											$iPumpVal        = $sStatus2Speed;
										}
										else if($sStatus2Speed == '1')											
										{
											if($sPumpSubType == '12')
											{
												$iPumpVal = $sPowercenter[$sPumpRelay]; //Taken the status
											}
											else if($sPumpSubType == '24')
											{
												$iPumpVal = $sRelays[$sPumpRelay];//Taken the status
											}
										}
										else if($sStatus2Speed == '2')											
										{
											if($sPumpSubType == '12')
											{
												$iPumpVal = $sPowercenter[$sPumpRelay1]; //Taken the status
											}
											else if($sPumpSubType == '24')
											{
												$iPumpVal = $sRelays[$sPumpRelay1];//Taken the status
											}
										}
									}
									else if(preg_match('/Emulator/',$sPumpType))
									{
										 $iPumpVal = $sPump[$i];
									}
								}
							}	//END : Getting assigned relay status from the Server.
							$sPumpVal = false;
							if($iPumpVal)
							  $sPumpVal = true;
						  
							$sMainType	 = $this->home_model->getDeviceMainType($i,$sDevice);
					?>
						  <tr>
							<td>Pump Sequencer <?php echo $i;?></td>
							<td><a href="<?php if($sAccess == 2) {echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');} else {echo 'javascript:void(0);';}?>" ><?php echo $sPumpNameDb;?></a><br /><br /><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="0" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> >&nbsp;Other<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Spa<br><input type="radio" onclick="saveDeviceMainType('<?php echo $sDevice;?>','<?php echo $i;?>',this.value);" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?>>&nbsp;Pool</td>
							<td style="width:32px;"><span id="loading_pump_<?php echo $i;?>" style="visibility: hidden;"><img src="<?php echo site_url('assets/images/loading.gif');?>"></span></td>
							<td ><div id="pumpResponse_<?php echo $i;?>"></div></td>
							<td >
							<?php if($sPumpType == '2Speed') { 
							
							?>
							
							<script>
							var iActiveMode = '<?php echo $iActiveMode;?>';
							var sAccess 	= '<?php echo $sAccess;?>';
							</script>
							<link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
							<script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
							<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
							<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>
							<div class="span1 pump-<?php echo $i?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 1</div>
							<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
							<select id='switch-me-<?php echo $i;?>'>
							<option value='1' <?php if($sStatus2Speed == '1') { echo 'selected="selected"';} ?>>Spa</option>
							<option value='0' <?php if($sStatus2Speed == '0' || $iPumpVal == '') { echo 'selected="selected"';} ?>></option>
							<option value='2' <?php if($sStatus2Speed == '2') { echo 'selected="selected"';} ?>>Pool</option>
							</select>
							<div class="pump-<?php echo $i?>" value="0" id="off-<?php echo $i;?>" style="color: red;font-weight: bold;width: 0; margin-left: 40px; margin-top: 2px; cursor: pointer;">
								OFF
	</div>                              </div>
							<div class="span1 pump-<?php echo $i?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;">Relay 2</div>
						  <script type="text/javascript">
						  
						  $(function()
						  {
							  var bgColor = '#E8E8E8';
								<?php if($iPumpVal == '1' || $iPumpVal == '2') { ?>
										bgColor = '#45A31F';
								<?php } else { ?>
										bgColor = '#E8E8E8';
								<?php } ?>
								
								$('#switch-me-<?php echo $i;?>').switchy();
								
								$('.pump-<?php echo $i?>').on('click', function(event){
									//event.preventDefault();
									//return false;
									$('#switch-me-<?php echo $i;?>').val($(this).attr('value')).change();
								});
								
								$('#switch-me-<?php echo $i;?>').next('.switchy-container').find('.switchy-bar').animate({
										backgroundColor: bgColor
									});
								
								$('#switch-me-<?php echo $i;?>').on('change', function(event)
								{
									if(sAccess == 2)
									{
										if(iActiveMode != 2)
										{
											var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
											if(bConfirm)
											{
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('analog/changeMode');?>", 
													data: {iMode:'2'},
													success: function(data) {
													}
												});
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
											
												
												//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('home/updateStatusOnOff');?>", 
													data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
													success: function(data) {
													//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
													location.reload();
													}

												});
											}
										}
										else											
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
										
											
											//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
											$.ajax({
												type: "POST",
												url: "<?php echo site_url('home/updateStatusOnOff');?>", 
												data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>'},
												success: function(data) {
												//$("#loading_valve_<?php //echo $i;?>").css('visibility','hidden');
												}

											});
										}
									}
								});
							});
					   </script>
					   
					   
							
							<?php } else if($sPumpType != '') {?>
							<div class="toggle-light" style="width:100px;">
							<div>
							 <div class="togglePump<?php echo $i;?> <?php echo $sPumpVal;?>"></div>
							</div>
							</div>
						   <script type="text/javascript">
						   <?php if($iPumpVal > 0 && preg_match('/Emulator/',$sPumpType)) { ?>
						  $(document).ready(function() {
							setInterval( function() {
								$.getJSON('<?php echo site_url('cron/pumpResponseLatest/');?>', {iPumpID: "<?php echo $i;?>"}, function(json) {
									$("#pumpResponse_<?php echo $i;?>").html(json);
								});
								},30000);
						  });	
						   <?php } ?>
							  var clickOff  = '';
							  <?php if($iActiveMode != '2' || $sAccess == '1') { ?>
								  $('.togglePump<?php echo $i;?>').toggles({height:40,on:'<?php echo $sPumpVal;?>',drag: false, click: false});
								  <?php if($sAccess == 2) { ?>
								  $('.togglePump<?php echo $i;?>').click(function(){
									var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
									if(bConfirm)
									{
										$("#loading_pump_<?php echo $i;?>").css('visibility','visible');
										$.ajax({
											type: "POST",
											url: "<?php echo site_url('analog/changeMode');?>", 
											data: {iMode:'2'},
											success: function(data) {
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('home/updateStatusOnOff');?>", 
													data: {sName:'<?php echo $i;?>',sStatus:1,sDevice:'<?php echo $sDevice;?>'},
													success: function(data) {
													  $("#loading_pump_<?php echo $i;?>").css('visibility','hidden');
													  location.reload();
													}
												});
											}
										});
									}
								  });
								<?php } ?>
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
							<?php } ?>
						   </td>
							<td><a class="btn btn-primary btn-xs" href="<?php if($sAccess == 2){echo site_url('home/pumpConfigure/'.base64_encode($i).'/');}else{echo 'javscript:void(0);';}?>">Configure</a>&nbsp;&nbsp;
								<a class="btn btn-primary btn-xs" href="<?php if($sAccess == 2){ echo site_url('home/setProgramsPump/'.base64_encode($i).'/');} else {echo 'javascript:void(0);';}?>">Programs</a>
							</td>
						</tr>
					<?php }
				} ?>
                
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div><!-- /.row -->
        <?php }
			} ?> <!-- END : Pump Device -->
		<?php if($sDevice == 'T') {  // START : Temperature sensor?>
		<?php if($sAccess == 1 || $sAccess == 2) { ?>
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Temperature sensor List</h3>
              </div>
              <div class="table-responsive">
              <table class="table table-hover tablesorter">
                <thead>
                  <tr>
                    <th class="header">Temperature sensor <i class="fa fa-sort"></i></th>
					<th class="header">Temperature <i class="fa fa-sort"></i></th>
                    <th class="header">Name <i class="fa fa-sort"></i></th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    
                    //START : Temperature sensor
                    for ($i=0;$i < $temprature_count; $i++)
                    {
                        $iTempratureVal = $sTemprature[$i];
                        
						$sTempratureNameDb =  $this->home_model->getDeviceName($i,$sDevice);
                        if($sTempratureNameDb == '')
                          $sTempratureNameDb = 'Add Name';
					  
						if($iTempratureVal == '')
							$iTempratureVal = '-';
                ?>
                      <tr>
                        <td>Temperature sensor <?php echo $i;?></td>
						<td><?php echo $iTempratureVal;?></td>
                        <td><a href="<?php if($sAccess == 2){ echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/');} else { echo 'javascript:void(0);';}?>" ><?php echo $sTempratureNameDb;?></a></td>
                      </tr>
                <?php } ?>
                
                </tbody>
              </table>
            </div>
            </div>
          </div>
        </div><!-- /.row -->
        <?php }
		} ?> <!-- END : Temperature sensor -->
      </div><!-- /#page-wrapper -->


    
    
<hr>
<?php
$this->load->view('Footer');
?>
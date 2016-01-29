

<!-- START : VALVES -->
<?php //$iValveNumber = $extra['ValveNumber'];?> 	
<link href="<?php echo site_url('assets/switchy/switchy.css'); ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo site_url('assets/switchy/switchy.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.event.drag.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/switchy/jquery.animate-color.js'); ?>"></script>

<script>
	var iActiveMode = '<?php echo $iActiveMode;?>';
	var sAccess 	= '<?php echo $sAccess;?>';
</script>

<style>

.valveGraph{
	visibility: hidden;
}

.td-custom{
	text-align: center !important;
	border-right: 1px solid #CCC;
}

.checkboxFour {
	width: 25px;
	height: 25px;
	background: #ddd;
	border-radius: 100%;
	position: relative;
	-webkit-box-shadow: 0px 1px 3px rgba(0,0,0,0.5);
	-moz-box-shadow: 0px 1px 3px rgba(0,0,0,0.5);
	box-shadow: 0px 1px 3px rgba(0,0,0,0.5);
	display: inline-block;
}

/**
 * Create the checkbox button
 */
.checkboxFour label {
	display: block;
	width: 15px;
	height: 15px;
	border-radius: 100px;

	-webkit-transition: all .5s ease;
	-moz-transition: all .5s ease;
	-o-transition: all .5s ease;
	-ms-transition: all .5s ease;
	transition: all .5s ease;
	cursor: pointer;
	position: absolute;
	top: 5px;
	left: 5px;
	z-index: 1;

	background: #FF0000;
	font-size: 10px;
    font-weight: bold;
	
	-webkit-box-shadow:inset 0px 1px 3px rgba(0,0,0,0.5);
	-moz-box-shadow:inset 0px 1px 3px rgba(0,0,0,0.5);
	box-shadow:inset 0px 1px 3px rgba(0,0,0,0.5);
}

/**
 * Create the checked state
 */
.checkboxFour input[type=checkbox]:checked + label {
	background: #26ca28;
}

@media screen and (max-width:590px)
	{
		.checkboxFour {
			width: 25px !important;
			height: 25px !important;
		}
		
		.checkboxFour label {
			width: 15px !important;
			height: 15px !important;
		}
	}
	
@media screen and (min-width : 591px) and (max-width:990px)
	{
		.checkboxFour {
			width: 50px !important;
			height: 50px !important;
		}
		
		.checkboxFour label {
			width: 40px !important;
			height: 40px !important;
			font-size: 28px !important;
		}
	}
	

</style>

<div class="row">
	<!--<div class="checkboxFour">
	  		<input type="checkbox" checked="checked" class="valveGraph" value="1" id="checkboxFourInput" name="" />
		  	<label for="checkboxFourInput"></label>
	  	</div>-->
	<?php
		if(!empty($aIPDetails))
		{
			foreach($aIPDetails as $aIP)
			{
				if($aIP->id <= 1)
					$iValveNumber = $extra['ValveNumber'];
			    else if($aIP->id > 1)
					$iValveNumber = $extra['ValveNumber2'];
				
	?>
	
	<div class="col-sm-12" id="graphbuttons_<?php echo $aIP->id;?>" style="display:<?php if($aIP->id != $iFirstIPId){ echo 'none';} ?>">
		<div class="widget-container widget-stats boxed green-line">
		<div class="widget-title">
			<h3>Valve Graphical Representation</h3>
		</div>
		<div class="stats-content clearfix" style="background: #fff none repeat scroll 0 0; overflow-x:auto;">
			<table class="table">
				<thead>
					<tr>
						<th style="border-right: 1px solid #CCC;"><strong>Valve Number</strong></th>
						<th class="td-custom">0</th>
						<th class="td-custom">1</th>
						<th class="td-custom">2</th>
						<th class="td-custom">3</th>
						<th class="td-custom">4</th>
						<th class="td-custom">5</th>
						<th class="td-custom">6</th>
						<th class="td-custom">7</th>
					</tr>
				</thead>
				<tbody>
				
					<tr>
						<td style="border-right: 1px solid #CCC;"><strong>Output Relays</strong></td>
						<?php
							$k=0;
							for($i=0;$i<8;$i++) 
							{
									
								$arrRelays =	json_decode($this->home_model->getValveRelayNumber($i,'V',$aIP->id));
								
								$iValveExists = ${"sValves".$aIP->id}[$i];
								
						?>
						<td class="td-custom">
						<?php 	for($j=1;$j<=2;$j++) 
								{
									$strChecked = '';
									if(!empty($arrRelays))
										$strChecked = 'checked="checked"';	
							
									if($iValveExists == '.')
									{
										$k++;
										$k++;
										echo '<strong>.</strong>';
										break;
									}
									else
									{		
						?>
							
							<div class="checkboxFour">
								<input type="checkbox" <?php echo $strChecked;?> class="valveGraph" value="1" id="checkboxFourInput<?php echo $i.'-'.$j.'-'.$aIP->id;?>" name="" />
								<label for="checkboxFourInput<?php echo $i.'-'.$j;?>" style="color:#FFF;"><?php echo $k;?></label>
							</div>
						<?php 
								$k++;
								if($j==1)
									echo '&nbsp;&nbsp;&nbsp;&nbsp;';
								
									}
								}	
						?>	
						</td>
						<?php 
							}
						?>	
					</tr>
					<tr><td colspan="9"><strong>Note:</strong></td></tr>
					<tr>
						<td colspan="3" style="border-top: medium none;">
							<div class="checkboxFour">
								<input type="checkbox" checked="checked" class="valveGraph" value="On" id="checkboxFourInputExmOn<?php echo $aIP->id;?>" name="" />
								<label for="checkboxFourInputExmOn<?php echo $aIP->id;?>"></label>
							</div> - Relays Assigned.
						</td>
						<td colspan="3" style="border-top: medium none;">
							<div class="checkboxFour">
								<input type="checkbox" class="valveGraph" value="Off" id="checkboxFourInputExmOff<?php echo $aIP->id;?>" name="" />
								<label for="checkboxFourInputExmOff<?php echo $aIP->id;?>"></label>
							</div> - Relays not Assigned.
						</td>
						<td colspan="3" style="border-top: medium none;">
							<strong>.</strong> - Output is Assigned to 24V AC.
						</td>
					</tr>
				</tbody>				
			</table>
		</div>	
		</div>
	</div>
	<div class="col-sm-4" id="onoffbuttons_<?php echo $aIP->id;?>" style="display:<?php if($aIP->id != $iFirstIPId){ echo 'none';} ?>">
		<div class="widget-container widget-stats boxed green-line">
		<div class="widget-title">
			<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
			<h3>ON/OFF</h3>
		</div>
		<div class="stats-content clearfix">
			<div class="stats-content-right" style="width:96% !important; margin-left:5px; margin-right:5px; margin-top:10px; float:none;">
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
					
					if(!empty(${"ValveRelays".$aIP->id}))
					{
						foreach(${"ValveRelays".$aIP->id} as $valve)
						{
							$i = $valve->device_number;
							$j = $i *2;
							
							unset($arrValve[$i]);
							
							$iValvesVal = ${"sValves".$aIP->id}[$i];
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

							$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice,$aIP->id);
							if($sValvesNameDb == '')
							  $sValvesNameDb = 'Add Name';
							
							//START : Get Valve Position Details.
							$aPositionName   =  $this->home_model->getPositionName($i,$sDevice,$aIP->id);
							
							$strPosition1 = '';
							$strPosition2 = '';
							
							if($aPositionName[0] != '')
							$strPosition1	 =	$this->home_model->getPositionNameFromID($aPositionName[0]);
							if($aPositionName[1] != '')
							$strPosition2	 =	$this->home_model->getPositionNameFromID($aPositionName[1]);
							//END : Get Valve Position Details.
							
							$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice,$aIP->id));
							$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice,$aIP->id);
							
							//echo 'iValvesVal : '.$iValvesVal;
							
							if($iValvesVal == '.' || $iValvesVal == '')
								continue;
							
							if($iValvesVal != '' && $iValvesVal != '.' && !empty($aRelayNumber)) 
							{
						?>
							<div class="row">
							<div class="col-sm-12">
							<div class="span1 valve-<?php echo $i?>-<?php echo $aIP->id;?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php echo $strPosition1; ?></div>
							<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
							<select id='switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>'>
							<option value='1' <?php if($iValvesVal == '1') { echo 'selected="selected"';} ?>>Spa</option>
							<option value='0' <?php if($iValvesVal == '0' || $iValvesVal == '') { echo 'selected="selected"';} ?>></option>
							<option value='2' <?php if($iValvesVal == '2') { echo 'selected="selected"';} ?>>Pool</option>
							</select>
							<div class="valve-<?php echo $i?>-<?php echo $aIP->id;?>" value="0" id="off-<?php echo $i;?>-<?php echo $aIP->id;?>" style="color: red;font-weight: bold;width: 0; margin-left: 30px; cursor: pointer;">
								OFF
							</div></div>
							<div class="span1 valve-<?php echo $i?>-<?php echo $aIP->id;?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php echo $strPosition2; ?></div>
							<div style="float: right;margin-right: 10px;margin-top: 10px;"><strong><span style="color:#C9376E;"><?php echo 'Valve '.$i; ?></span></strong></div>
							<script type="text/javascript">
							  $(function()
							  {
									var bgColor = '#E8E8E8';
									<?php if($iValvesVal == '1' || $iValvesVal == '2') { ?>
											bgColor = '#45A31F';
									<?php } else { ?>
											bgColor = '#E8E8E8';
									<?php } ?>
									
									$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').switchy();
									
									$('.valve-<?php echo $i?>-<?php echo $aIP->id;?>').on('click', function(event){
										
										$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').val($(this).attr('value')).change();
									});
									
									$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').next('.switchy-container').find('.switchy-bar').animate({
											backgroundColor: bgColor
										});
									
									$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').on('change', function(event)
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
													$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').next('.switchy-container').find('.switchy-bar').animate({
														backgroundColor: bgColor
													});
												
													
													//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
													$.ajax({
														type: "POST",
														url: "<?php echo site_url('home/updateStatusOnOff');?>", 
														data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>',sIdIP:'<?php echo $aIP->id;?>'},
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
												$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').next('.switchy-container').find('.switchy-bar').animate({
													backgroundColor: bgColor
												});
											
												
												//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('home/updateStatusOnOff');?>", 
													data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>',sIdIP:'<?php echo $aIP->id;?>'},
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
						</div>
						<div style="height:10px;">&nbsp;</div>
				<?php 	}
						else
						{
							echo '<div class="row"><div class="col-sm-12"><div class="tagcloud clearfix"><span style="color:#FF0000;">Relay Not Assigned</span><div style="float: right;margin-right: 10px;color:#C9376E"><strong>Valve '.$i.'</strong></div></div></div></div><div style="height:10px;">&nbsp;</div>';
						}
						
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
						
						$j = $i * 2;
						
						$iValvesVal = ${"sValves".$aIP->id}[$i];
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

						$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice,$aIP->id);
						
						//START : Get Valve Position Details.
						$aPositionName   =  $this->home_model->getPositionName($i,$sDevice,$aIP->id);
						
						$strPosition1 = '';
						$strPosition2 = '';
						
						if($aPositionName[0] != '')
						$strPosition1	 =	$this->home_model->getPositionNameFromID($aPositionName[0]);
						if($aPositionName[1] != '')
						$strPosition2	 =	$this->home_model->getPositionNameFromID($aPositionName[1]);
						//END : Get Valve Position Details.
					
						
						$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice,$aIP->id));
						$iPower	 	     =  $this->home_model->getDevicePower($i,$sDevice);
						$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice,$aIP->id);
						
						//echo 'iValvesVal : '.$iValvesVal;
						
					if($iValvesVal != '' && $iValvesVal != '.' && !empty($aRelayNumber)) 
					{
					?>
						<div class="row">
						<div class="col-sm-12">
						
						<div class="span1 valve-<?php echo $i?>-<?php echo $aIP->id;?>" value="1" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php  echo $strPosition1; ?></div>
						
						<div class="span2" style="margin-left:5px; margin-right:5px; float: left;" >
							<select id='switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>'>
							<option value='1' <?php if($iValvesVal == '1') { echo 'selected="selected"';} ?>>Spa</option>
							<option value='0' <?php if($iValvesVal == '0' || $iValvesVal == '') { echo 'selected="selected"';} ?>></option>
							<option value='2' <?php if($iValvesVal == '2') { echo 'selected="selected"';} ?>>Pool</option>
							</select>
							<div class="valve-<?php echo $i?>-<?php echo $aIP->id;?>" value="0" id="off-<?php echo $i;?>-<?php echo $aIP->id;?>" style="color: red;font-weight: bold;width: 0; margin-left: 30px; cursor: pointer;">
								OFF
							</div>
						</div>
						<div class="span1 valve-<?php echo $i?>-<?php echo $aIP->id;?>" value="2" style="margin-top: 10px; width: auto; color: #428BCA;font-weight: bold; cursor: pointer; float: left;"><?php  echo $strPosition2; ?></div>
						<div style="float: right;margin-right: 10px;margin-top: 10px;"><strong><span style="color:#C9376E;"><?php echo 'Valve '.$i; ?></span></strong></div>
						<script type="text/javascript">
						  $(function()
						  {
								var bgColor = '#E8E8E8';
								<?php if($iValvesVal == '1' || $iValvesVal == '2') { ?>
										bgColor = '#45A31F';
								<?php } else { ?>
										bgColor = '#E8E8E8';
								<?php } ?>
								
								$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').switchy();
								
								$('.valve-<?php echo $i?>-<?php echo $aIP->id;?>').on('click', function(event){
									
									$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').val($(this).attr('value')).change();
								});
								
								$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').next('.switchy-container').find('.switchy-bar').animate({
										backgroundColor: bgColor
									});
								
								$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').on('change', function(event)
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
												$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').next('.switchy-container').find('.switchy-bar').animate({
													backgroundColor: bgColor
												});
											
												
												//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
												$.ajax({
													type: "POST",
													url: "<?php echo site_url('home/updateStatusOnOff');?>", 
													data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>',sIdIP:'<?php echo $aIP->id;?>'},
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
											$('#switch-me-<?php echo $i;?>-<?php echo $aIP->id;?>').next('.switchy-container').find('.switchy-bar').animate({
												backgroundColor: bgColor
											});
										
											
											//$("#loading_valve_<?php //echo $i;?>").css('visibility','visible');
											$.ajax({
												type: "POST",
												url: "<?php echo site_url('home/updateStatusOnOff');?>", 
												data: {sName:'<?php echo $i;?>',sStatus:$(this).val(),sDevice:'<?php echo $sDevice;?>',sIdIP:'<?php echo $aIP->id;?>'},
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
					</div>
					<div style="height:10px;">&nbsp;</div>
				<?php }	
						else
						{
							echo '<div class="row"><div class="col-sm-12"><div class="tagcloud clearfix"><span style="color:#FF0000;">Relay Not Assigned.</span><div style="float: right;margin-right: 10px; color:#C9376E"><strong>Valve '.$i.'</strong></div></div></div></div><div style="height:10px;">&nbsp;</div>';
						}
					}
				}
				?>
			</div>
		</div>
		</div>
	</div>
	<div class="col-sm-8" id="relayConfigure_<?php echo $aIP->id;?>" style="display:<?php if($aIP->id != $iFirstIPId){ echo 'none';} ?>">
	<div class="widget-container widget-stats boxed green-line">
		<div class="widget-title">
			<a href="<?php echo base_url('home/setting/'.$sDevice.'/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
			<h3>Valve Settings</h3>
		</div>
	<?php
		if( $iValveNumber == 0 || $iValveNumber == '' )
				{ ?>
					<div class="stats-content clearfix">
					<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
							<span style="color:red">Please add number of Vavles in the <a href="<?php echo base_url('home/setting/');?>">Settings</a> Page!</span>
					</div>
					</div>
					
				<?php 
				}
				else
				{
				?>
				
					<div class="stats-content clearfix">
					<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
				
				  <table class="table table-hover">
					<thead>
					  <tr>
						<th class="header" style="width:25%">Valve</th>
						<th class="header"  style="width:25%">Type</th>
						<th class="header"  style="width:50%">Action</th>
					  </tr>
					</thead>
					<tbody>
					<?php			
					$arrValve	=	array(0,1,2,3,4,5,6,7);
					$j=0;
					$remainigCount	=	0;
					$chkValve		=	0;
					
					if(!empty(${"ValveRelays".$aIP->id}))
					{
						foreach(${"ValveRelays".$aIP->id} as $valve)
						{
							$i = $valve->device_number;
							$j = $i *2;
							
							unset($arrValve[$i]);
							
							$iValvesVal = ${"sValves".$aIP->id}[$i];
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

							$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice,$aIP->id);
							if($sValvesNameDb == '')
							  $sValvesNameDb = 'Add Name';
						  
							$aPositionName   =  $this->home_model->getPositionName($i,$sDevice,$aIP->id);
							$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice,$aIP->id));
							$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice,$aIP->id);
							
						?>
							<tr class="">
							<td>Valve <?php echo $i;?><br />(<a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'.base64_encode($aIP->id)); } else { echo 'javascript:void(0);';}?>" ><?php echo $sValvesNameDb;?></a>)</td>
							<td>
								<div class="rowRadio">
									<div class="custom-radio">
										
										<input class="valveRadio" type="radio" id="radio-other-<?php echo $i;?>-<?php echo $aIP->id;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;">
										<label id="relay_other_<?php echo $i;?>_<?php echo $aIP->id;?>" for="radio-other-<?php echo $i;?>-<?php echo $aIP->id;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label>
								
										<input class="valveRadio" type="radio" id="radio-spa-<?php echo $i;?>-<?php echo $aIP->id;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;">
										<label id="relay_spa_<?php echo $i;?>_<?php echo $aIP->id;?>" for="radio-spa-<?php echo $i;?>-<?php echo $aIP->id;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label>
								
										<input class="valveRadio" type="radio" id="radio-pool-<?php echo $i;?>-<?php echo $aIP->id;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;">
										
										<label id="relay_pool_<?php echo $i;?>_<?php echo $aIP->id;?>" for="radio-pool-<?php echo $i;?>-<?php echo $aIP->id;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label>
									</div>
								</div>
							</td>
							<td>

							<a class="btn btn-green btn-small" style="width:120px" href="<?php if($sAccess == '2') { echo base_url('home/valveRelays/'.base64_encode($i).'/'.base64_encode($sDevice).'/'.base64_encode($aIP->id));} else { echo 'javascript:void(0);';}?>"><?php if(!empty($aRelayNumber)) { ?><span>Edit Relays</span><?php } else { ?><span>Assign Relays</span><?php } ?></a>
							
							<?php if(!empty($aRelayNumber)) { ?>
							<a class="btn btn-small" style="width:120px" href="javascript:void(0);" <?php if($sAccess == '2') { echo 'onclick="return RemoveValveRelays(\''.$i.'\',\''.$aIP->id.'\')"';} ?>><span>Remove Relays</span></a>
							<?php } ?>
							
							<!-- Position Button-->
							
							<!--<a class="btn btn-small" style="width:120px" href="<?php //if($sAccess == 2) { echo site_url('home/positionName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>"><span>Edit Position</span></a>-->
							
							<!-- Position Button-->
							
							<!--<a class="btn btn-small btn-red" style="width:120px" href="<?php //if($sAccess == 2) { echo site_url('home/removeValve/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>"><span>Remove Valve</span></a>-->
							</td>
							</tr>
					<?php
							$chkValve++;
						} ?>
					<?php  } ?>	
				
				<?php
					$remainigCount	=	$iValveNumber - $chkValve;
					//for ($i=0;$i < $valve_count; $i++)	
					//for ($i=0;$i < $remainigCount ; $i++)
					foreach($arrValve as $i)	
					{
						if($remainigCount == 0)	
							break;
						
						$remainigCount--;
						
						$j = $i * 2;
						
						$iValvesVal = ${"sValves".$aIP->id}[$i];
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

						$sValvesNameDb =  $this->home_model->getDeviceName($i,$sDevice,$aIP->id);
						if($sValvesNameDb == '')
						  $sValvesNameDb = 'Add Name';
					  
						$aPositionName   =  $this->home_model->getPositionName($i,$sDevice,$aIP->id);
						$aRelayNumber    =  json_decode($this->home_model->getValveRelayNumber($i,$sDevice,$aIP->id));
						$iPower	 	     =  $this->home_model->getDevicePower($i,$sDevice);
						$sMainType	     =  $this->home_model->getDeviceMainType($i,$sDevice,$aIP->id);
				?>
						<tr class="">
							<td>Valve <?php echo $i;?><br />(<a href="<?php if($sAccess == 2) { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'.base64_encode($aIP->id)); } else { echo 'javascript:void(0);';}?>" ><?php echo $sValvesNameDb;?></a>)</td>
							<td>
								<div class="rowRadio">
									<div class="custom-radio">
										
										<input class="valveRadio" type="radio" id="radio-other-<?php echo $i;?>-<?php echo $aIP->id;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;">
										<label id="relay_other_<?php echo $i;?>_<?php echo $aIP->id;?>" for="radio-other-<?php echo $i;?>-<?php echo $aIP->id;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label>
										
										<input class="valveRadio" type="radio" id="radio-spa-<?php echo $i;?>-<?php echo $aIP->id;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;">
										<label id="relay_spa_<?php echo $i;?>_<?php echo $aIP->id;?>" for="radio-spa-<?php echo $i;?>-<?php echo $aIP->id;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label>
										
										<input class="valveRadio" type="radio" id="radio-pool-<?php echo $i;?>-<?php echo $aIP->id;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;">
										<label id="relay_pool_<?php echo $i;?>_<?php echo $aIP->id;?>" for="radio-pool-<?php echo $i;?>-<?php echo $aIP->id;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label>
									</div>
								</div>
							</td>
							<td>
							<a class="btn btn-green btn-small" style="width:120px" href="<?php if($sAccess == '2') { echo base_url('home/valveRelays/'.base64_encode($i).'/'.base64_encode($sDevice).'/'.base64_encode($aIP->id));} else { echo 'javascript:void(0);';}?>"><?php if(!empty($aRelayNumber)) { ?><span>Edit Relays</span><?php } else { ?><span>Assign Relays</span><?php } ?></a>
							<?php if(!empty($aRelayNumber)) { ?>
							&nbsp;&nbsp;<a class="btn btn-small" style="width:120px" href="javascript:void(0);" <?php if($sAccess == '2') { echo ' onclick="return RemoveValveRelays(\''.$i.'\',\''.$aIP->id.'\');"';}?>><span>Remove Relays</span></a>
							<?php } ?>
							<!-- Position Button-->
							
							<!-- <a class="btn btn-small" style="width:120px" href="<?php //if($sAccess == 2) { echo site_url('home/positionName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';}?>"><span>Edit Position</span></a> -->
							
							<!-- Position Button-->
							
							<a class="btn btn-small" style="width:120px" href="javascript:void(0);" <?php if($sAccess == '2') { echo ' onclick="removeValve(\''.$aIP->id.'\');"';}?> ><span>Remove Valve</span></a>
							</td>
							</tr>
					<?php	} ?>
						<tr><td colspan="3">
						<div class="buttons">
						<a class="btn btn-icon btn-icon-right btn-icon-go addMoreValve" id="addMoreValve-<?php echo $aIP->id;?>" href="javascript:void(0);" hidefocus="true" style="outline: medium none; padding:0px !important;"><span>Add More Valve</span></a>
						</div>
						<div id="moreValve-<?php echo $aIP->id;?>" style="display:none;">
							<input type="text" value="" id="moreValveCnt-<?php echo $aIP->id;?>" name="moreValveCnt-<?php echo $aIP->id;?>" hidefocus="true" style="outline: medium none; width: 45%; margin-top:1px; margin-right:10px;" placeholder="Add Number"><a class="btn btn-icon btn-green btn-icon-right btn-icon-checkout" href="javascript:void(0);" style="padding:0px !important;" hidefocus="true" onclick="saveValveCount('<?php echo $aIP->id;?>')"><span>Save</span></a>
						</div>
						</td></tr>
					</tbody>
					</table>
					</div>
				</div>
			</div>							
					
				<?php	
				}
				
				?>
	</div>
	<?php } 
		}
	?>	
</div>		
<!-- END : VALVE DEVICES-->
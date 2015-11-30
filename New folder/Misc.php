<?php
$this->load->model('home_model');
$sAccess 		= '';
$sModule	    = '19';
$sDeviceFullName = '';
if($sDevice == 'M')
{
  $sDeviceFullName 	= 'Miscelleneous Device';
}

$sAccessKey	= 'access_'.$sModule;
			  
if(!empty($aModules))
{
    if(in_array($sModule,$aModules->ids))
    {
	 $sAccess 		= $aModules->$sAccessKey;
    }
	else if(!in_array($sModule,$aModules->ids)) 
    {
		$sAccess 		= '0'; 
    }
}
  
  if($sAccess == '')
	$sAccess = '2' ; 
  
  if($sAccess == '0') {redirect(site_url('home/'));} 
?>
<style>
.fancybox-inner 
{
	height:40px !important;
}
</style>
<script type="text/javascript" src="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.js?v=2.1.5';?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.css?v=2.1.5';?>" media="screen" />
<script type="text/javascript">
var $a = $.noConflict();
$a(document).ready(function() {
    $a('.fancybox').fancybox({'closeBtn' : false,
                              'helpers': {'overlay' : {'closeClick': false}}
                             });
});
</script>	
<link href="<?php echo HTTP_ASSETS_PATH.'progressbar/css/static.css';?>" rel="stylesheet"/>
<script src="<?php echo HTTP_ASSETS_PATH.'progressbar/js/static.min.js';?>"></script>
<script src="<?php echo HTTP_ASSETS_PATH.'progressbar/dist/js/jquery.progresstimer.js';?>"></script>
<script>
jQuery(document).ready(function($) 
{
    
    $(".relayRadio").click(function()
    {
        var chkVal 		= $(this).val();
        var relayNumber	= $(this).attr('name').split("_");	

        $.ajax({
                type: "POST",
                url: "<?php echo site_url('home/saveDeviceMainType');?>", 
                data: {sDeviceID:relayNumber[0],sDevice:'M',sType:chkVal},
                success: function(data) {
                        if(chkVal == 0)
                        {
                                $("#relay_other_"+relayNumber[0]).addClass('checked');
                                $("#relay_spa_"+relayNumber[0]).removeClass('checked');
                                $("#relay_pool_"+relayNumber[0]).removeClass('checked');
                        }
                        else if(chkVal == 1)
                        {
                                $("#relay_other_"+relayNumber[0]).removeClass('checked');
                                $("#relay_spa_"+relayNumber[0]).addClass('checked');
                                $("#relay_pool_"+relayNumber[0]).removeClass('checked');
                        }
                        else if(chkVal == 2)
                        {
                                $("#relay_other_"+relayNumber[0]).removeClass('checked');
                                $("#relay_spa_"+relayNumber[0]).removeClass('checked');
                                $("#relay_pool_"+relayNumber[0]).addClass('checked');
                        }

                }

            });
    });
    
    $(".miscButton").click(function(){
        var chkVal      = $(this).val();
        var arrDetails	= chkVal.split("|||");	
        
        var miscNumber = arrDetails[0];
        var relayNumber = arrDetails[2];
        var sDevice     = '';
        
        if(arrDetails[1] == '24')
            sDevice     =   'R';    
        else if(arrDetails[1] == '12')
            sDevice     =   'P';
        
        <?php if($iActiveMode == '2') { ?>
        $(".loading-progress").show();
        var progress = $(".loading-progress").progressTimer({
                timeLimit: 10,
                onFinish: function () {
                    //$(".loading-progress").hide();
                    parent.$a.fancybox.close();
                }
        });
        
        
        $a("#checkLink").trigger('click');

        var status		= '';
        if($("#lableRelay-"+miscNumber).hasClass('checked'))
        {	
                status	=	0;
        }
        else
        {
                status = 1;
        }


            $.ajax({
                type: "POST",
                url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
                data: {sName:relayNumber,sStatus:status,sDevice:sDevice},
                success: function(data) {
                        if($("#lableRelay-"+miscNumber).hasClass('checked'))
                        {	
                                $("#lableRelay-"+miscNumber).removeClass('checked');
                                $("#miscImage_"+miscNumber).attr('src','<?php echo HTTP_IMAGES_PATH."icons/blower.png";?>');
                                
                        }
                        else
                        {
                                $("#lableRelay-"+miscNumber).addClass('checked');
                                $("#miscImage_"+miscNumber).attr('src','<?php echo HTTP_IMAGES_PATH."icons/blower.png";?>');
                        }

                }
        }).error(function(){
            progress.progressTimer('error', {
                errorText:'ERROR!',
                onFinish:function(){
                    alert('There was an error processing your information!');
                }
            });
        }).done(function(){
                progress.progressTimer('complete');
        });
            <?php } else {  ?>
            alert('You can perform this operation in manual mode only.');
            <?php } ?> 
    });
    
    
    $(".miscRadio").click(function()
    {
            var chkVal 		= $(this).val();
            var miscNumber	= $(this).attr('name').split("_");	

            if(chkVal == 24)
            {
                    $("#24VRelay_"+miscNumber[1]).show();
                    $("#12VRelay_"+miscNumber[1]).hide();
            }
            else if(chkVal == 12)
            {
                    $("#12VRelay_"+miscNumber[1]).show();
                    $("#24VRelay_"+miscNumber[1]).hide();
            }

    });
});
function cancel(miscNumber)
{
	$("#24VRelay_"+miscNumber).hide();
	$("#12VRelay_"+miscNumber).hide();
	
	$("#lightRelay24_"+miscNumber).prop('checked',false);
	$("#lightRelay12_"+miscNumber).prop('checked',false);
}

function removeBlower(miscNumber)
{
	var check	=	confirm("Are you sure you want to remove this Miscelleneous Device?");
	if(check)
	{
		$.ajax({
            type: "POST",
            url: "<?php echo site_url('analog/removeMisc/');?>", 
            data: {miscNumber:miscNumber},
            async: false,
            success: function(data) {
                    alert("Miscelleneous Device removed successfully!");
                    location.reload();
            }
		});
	}
}

function save(miscNumber,relayType)
{
    $("#loadingImg"+relayType+"_"+miscNumber).show();
    var relayNumber	=	$("#select"+relayType+"VRelays_"+miscNumber).val();

    //Check if entered address already exists.
    var checkRelay = 0;
    $.ajax({
            type: "POST",
            url: "<?php echo site_url('home/checkRelayNumberAlreadyAssigned/');?>", 
            data: {sRelayNumber:relayNumber,type:relayType,sDeviceId:miscNumber},
            async: false,
            success: function(data)
			{
				var obj = jQuery.parseJSON( data );
				if(obj.iPumpCheck == '1')
				{
						checkRelay = 1;
						$("#sRelayNumber").css('border','1px Solid Red');
						alert('Relay number is already used by other Device or not available!');
						//return false;
				}
				else
				{
					if(checkRelay == 0)
					{
							$.ajax({
									type: "POST",
									url: "<?php echo site_url('home/saveMiscRelay/');?>", 
									async: false,
									data: {sRelayNumber:relayNumber,sDevice:'M',sDeviceId:miscNumber,sRelayType:relayType},
									async: false,
									success: function() {
										alert('Relay is assigned to Miscelleneous Device successfully!')
										location.reload();
									}
							});
					}
				}
				
				$("#loadingImg"+relayType+"_"+miscNumber).hide();
            }
    });
}
</script>
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb" style="float:left">
		  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
		  <li class="active"><?php echo $sDeviceFullName;?></li>
		</ol>
	</div>
</div>	

<!-- START : Miscelleneous Device -->
<?php if($sDevice == 'M') 
	  { ?>
	<?php //if($sAccess == '1' || $sAccess == '2') 
		  { ?>
			<div class="row">
				<div class="col-sm-4">
					<div class="widget-container widget-stats boxed green-line">
							<div class="widget-title">
								<a href="<?php echo base_url('analog/showMisc/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
								<h3>ON/OFF</h3>
							</div>
							<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:96% !important; margin-left:5px; margin-right:5px; float:none; margin-top:10px;">
								<?php
								for ($i=0;$i<$numMisc; $i++)
								{	
									$strMiscName	=   'Misc Device '.($i+1);
                                                                        
									$sRelayType     =   '';
									$sRelayNumber   =   '';
									$strMisc		=   'blower.png';
									
									$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($i);
									if(!empty($aMiscDetails))
									{
										
										foreach($aMiscDetails as $aMisc)
										{
											$sMiscStatus	=	'';
											$sRelayDetails  =   unserialize($aMisc->light_relay_number);
											
											$sRelayType     =   $sRelayDetails['sRelayType'];
											$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
											
											if($sRelayType == '24')
											{
												$sMiscStatus   =   $sRelays[$sRelayNumber];
											}
											if($sRelayType == '12')
											{
												$sMiscStatus   =   $sPowercenter[$sRelayNumber];
											}
										}
									}
									
									$strChecked	=	'';
									if($sMiscStatus)
									{
										$strChecked	=	'class="checked"';
									}
									
									if($sRelayNumber != '')
									{
							?>
							
							<div class="rowCheckbox switch">
							<img id="miscImage_<?php echo $i;?>" src="<?php echo HTTP_IMAGES_PATH.'icons/'.$strMisc;?>" style="width:64px;">
								<div class="custom-checkbox" style="float:right; margin-right:10px; margin-top:20px;"><input type="checkbox" value="<?php echo $i.'|||'.$sRelayType.'|||'.$sRelayNumber;?>" id="relay-<?php echo $i?>" name="relay-<?php echo $i?>" class="miscButton" hidefocus="true" style="outline: medium none;">
									<label <?php echo $strChecked;?>  id="lableRelay-<?php echo $i?>" for="relay-<?php echo $i?>"><span style="color:#C9376E;"><?php echo $strMiscName;?></span></label>
								</div>
							</div>
							<?php	} else { ?>
                                                        <div class="rowCheckbox switch">
							<span style="color:#C9376E; font-weight: bold;">Relay not assinged to <?php echo $strMiscName;?></span>
							</div>
                                                        <?php } ?>
                                                        <div style="height:30px;">&nbsp;</div>
							<?php 			
								 }
							?> 
								</div>
							</div>
					</div>
				</div>
				  
				<div class="col-sm-8">
						<!-- Statistics -->
						<div class="widget-container widget-stats boxed green-line">
							<div class="widget-title">
								<a href="<?php echo base_url('analog/showMisc/');?>" class="link-refresh" id="link-refresh-1"><span class="glyphicon glyphicon-refresh"></span></a>
								<h3>Blower Settings</h3>
							</div>
							<div class="stats-content clearfix">
								<div class="stats-content-right" style="width:100% !important; margin-left:5px; margin-right:5px; float:none;">
							
							  <table class="table table-hover">
								<thead>
								  <tr>
									<th class="header" style="width:25%">Miscelleneous Device</th>
									<th class="header"  style="width:25%">Type</th>
									<th class="header"  style="width:50%">Action</th>
								  </tr>
								</thead>
								<tbody>
								<?php	

								for ($i=0;$i < $numMisc; $i++)
								{
									
									$sMainType =	$this->home_model->getDeviceMainType($i,$sDevice);
																			
									$sRelayType     =   '';
									$sRelayNumber   =   '';
									
									$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($i);
									if(!empty($aMiscDetails))
									{
										foreach($aMiscDetails as $aMisc)
										$sRelayDetails  =   unserialize($aMisc->light_relay_number);

										$sRelayType     =   $sRelayDetails['sRelayType'];
										$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
									}
									$sRelayNameDb =  $this->home_model->getDeviceName($i,$sDevice);
									if($sRelayNameDb == '')
									$sRelayNameDb = 'Add Name';
									?>
										<tr>
										<td>Misc Device <?php echo ($i+1);?><br />(<a href="<?php if($sAccess == '2') { echo site_url('home/deviceName/'.base64_encode($i).'/'.base64_encode($sDevice).'/'); } else { echo 'javascript:void(0);';} ?>"><?php echo $sRelayNameDb;?></a>)</td>
										<td>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_other_<?php echo $i;?>" value="0" name="<?php echo $i;?>_MainType" <?php if($sMainType == '0' || $sMainType == ''){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_other_<?php echo $i;?>" for="radio_other_<?php echo $i;?>" class="<?php if($sMainType == '0' || $sMainType == ''){ echo 'checked';}?>">Other</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_spa_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="1" <?php if($sMainType == '1'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_spa_<?php echo $i;?>" for="radio_spa_<?php echo $i;?>" class="<?php if($sMainType == '1'){ echo 'checked';}?>">Spa</label></div></div>
											<div class="rowRadio"><div class="custom-radio"><input class="relayRadio" type="radio" id="radio_pool_<?php echo $i;?>" name="<?php echo $i;?>_MainType" value="2" <?php if($sMainType == '2'){ echo 'checked="checked"';}?> <?php if($sAccess == '1') { echo 'disabled="disabled"';}?> hidefocus="true" style="outline: medium none;"><label id="relay_pool_<?php echo $i;?>" for="radio_pool_<?php echo $i;?>" class="<?php if($sMainType == '2'){ echo 'checked';}?>">Pool</label></div></div>
										</td>
										<td>
										<div>
										<input type="radio" <?php if($sRelayType == '24'){ echo 'checked="checked"';}?> class="miscRadio" name="miscRelay_<?php echo $i;?>" id="miscRelay24_<?php echo $i;?>" value="24">&nbsp;24V AC Relay&nbsp;&nbsp;<input type="radio" class="miscRadio" name="miscRelay_<?php echo $i;?>" id="miscRelay12_<?php echo $i;?>" <?php if($sRelayType == '12'){ echo 'checked="checked"';}?> value="12">&nbsp;12V DC Relay
										</div>
										<div id="24VRelay_<?php echo $i;?>" style="display:<?php if($sRelayType == '24'){ echo '';} else {echo 'none';}?>; padding-top:10px;">
										<select name="select24VRelays_<?php echo $i;?>" id="select24VRelays_<?php echo $i;?>" class="form-control" style="width:80%">
										<?php
												for ($j=0;$j < $relay_count; $j++)
												{
													$strSelect= "";
													$iRelayVal = $sRelays[$j];
													
													if($j == $sRelayNumber)
														$strSelect= "selected='selected'";
													
													if($iRelayVal != '' && $iRelayVal !='.') 
													{
														echo '<option value="'.$j.'" '.$strSelect.'>Relay '.$j.'</option>';
													}
												}
										?>
										</select>
										<a href="javascript:void(0);" class="btn btn-small btn-green" style="padding:6px 0 !important;" onclick="save('<?php echo $i;?>','24');"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" class="btn btn-small btn-red" style="padding:6px 0 !important;"onclick="cancel('<?php echo $i;?>')"><span>Cancel</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" class="btn btn-small btn-red" style="padding:6px 0 !important;" onclick="removeBlower('<?php echo $i;?>')"><span>Remove</span></a>&nbsp;&nbsp;<span id="loadingImg24_<?php echo $i;?>" style="display:none;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span>
										</div>
										<div id="12VRelay_<?php echo $i;?>" style="display:<?php if($sRelayType == '12'){ echo '';} else {echo 'none';}?>;padding-top:10px;">
										<select name="select12VRelays_<?php echo $i;?>" id="select12VRelays_<?php echo $i;?>" class="form-control" style="width:80%">
										<?php
											for ($j=0;$j < $power_count; $j++)
											{
												$strSelect  =   '';
												$iRelayVal = $sPowercenter[$j];
												
												if($j == $sRelayNumber)
													 $strSelect= "selected='selected'";
												
												if($iRelayVal != '' && $iRelayVal !='.') 
												{
														echo '<option value="'.$j.'" '.$strSelect.'>PowerCenter '.$j.'</option>';
												}
											}
										?>
										</select>
										<a href="javascript:void(0);" class="btn btn-small btn-green" style="padding:6px 0 !important;" onclick="save('<?php echo $i;?>','12');"><span>Save</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" class="btn btn-small btn-red" style="padding:6px 0 !important;" onclick="cancel('<?php echo $i;?>')"><span>Cancel</span></a>&nbsp;&nbsp;<a href="javascript:void(0);" class="btn btn-small btn-red" style="padding:6px 0 !important;" onclick="removeBlower('<?php echo $i;?>')"><span>Remove</span></a>&nbsp;&nbsp;<span id="loadingImg12_<?php echo $i;?>" style="display:none;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span>
										</div>
										</td>
										</tr>
								
								<?php  } ?>	
								</tbody>
								</table>
								</div>
							</div>
						</div>
						<!--/ Statistics -->
					</div>
					<p>
					<a class="fancybox" id="checkLink" href="#inline1" style="display:none;">&nbsp;</a>
					<div id="inline1" style="width:250px;height:40px; display:none;"><div class="loading-progress"></div></div>
					</p>
			</div><!-- /.row -->
	<?php } ?>
<?php } //Miscelleneous Device End ?>	
<!-- END -->
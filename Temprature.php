<?php
    /**
    * @Programmer: Dhiraj S.
    * @Created: 03 Aug 2015
    * @Modified: 
    * @Description: Time view is to add/edit the Time for Relay Device.
    **/


  $sDeviceFullName = 'Relay';
  
  $sAccess 		 = '';
  $sModule	    = 2;

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
	  else if(!in_array($sModule,$aModules->ids)) 
	  {
		$sAccess 		= '0'; 
	  }
  }
  
  if($sAccess == '')
	$sAccess = '2' ;

  if($sAccess == '0') {redirect(site_url('home/'));}
  


?>
<link href="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css');?>" rel="stylesheet" type="text/css" />  
  <link href="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/jquery.ui.timepicker.css?v=0.3.3');?>" rel="stylesheet" type="text/css" />
 
  <script type="text/javascript" src="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/ui-1.10.0/jquery.ui.core.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/ui-1.10.0/jquery.ui.position.min.js');?>"></script>
  <script type="text/javascript" src="<?php echo site_url('assets/js/jquery-ui-timepicker-0.3.3/jquery.ui.timepicker.js?v=0.3.3');?>"></script>
  
    <div id="page-wrapper">

        <div class="row">
			<div class="col-lg-12">
				<ol class="breadcrumb">
						  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
						  <li><a href="<?php echo base_url('home/setting/T/');?>">Temperature Sensors</a></li>
						  <li class="active">Configure Temperature</li>
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
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading" style="border-top-left-radius: 0px;border-top-right-radius: 0px;">
                <h3 class="panel-title" style="color:#FFF;">Temperature Configure</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                    <form action="<?php if($sAccess == 2) { echo site_url('analog/tempConfig/'.base64_encode($sDeviceID));}?>" method="post">
                  <input type="hidden" name="sDeviceID" value="<?php echo base64_encode($sDeviceID);?>">
                  <input type="hidden" name="sDevice" value="<?php echo base64_encode($sDevice);?>">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr>
                        <td width="10%"><strong>Select Bus To Configure:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%">
						<select name="busConfigure" id="busConfigure" class="form-control required" <?php if($sAccess == 1) { echo 'disabled="disabled";'; }?> style="max-width:200px; width:100%;">
						<option value="">Select Bus number</option>
						<?php 
							if(!empty($bus))
							{								
								foreach($bus as $strBusNumber) 
								{
									echo '<option value="'.substr_replace($strBusNumber,'',0,2).'">'.$strBusNumber.'</option>';
								}
							}
						?>	
						</select>
						</td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><span class="btn btn-green"><input type="<?php if($sAccess == 1) { echo 'button';} else { echo 'submit';}?>" name="command" value="Save" class="btn btn-success" ></span>&nbsp;&nbsp;<a class="btn btn-red " href="<?php echo site_url('home/setting/T/');?>"><span>Back</span></a></td></tr>
                      
                    </table>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#sDeviceTime').timepicker({
            showHours: false
        });
    });
</script>

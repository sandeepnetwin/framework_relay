<?php
    /**
    * @Programmer: Dhiraj S.
    * @Created: 03 Aug 2015
    * @Modified: 
    * @Description: Time view is to add/edit the Time for Relay Device.
    **/

$this->load->view('Header');
  $sDeviceFullName = 'Relay';

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
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> <?php echo $sDeviceFullName;?> Time Save</li>
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
              <div class="panel-heading">
                <h3 class="panel-title">Time</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                    <form action="<?php echo site_url('home/addTime/'.base64_encode($sDeviceID).'/'.base64_encode($sDevice));?>" method="post">
                  <input type="hidden" name="sDeviceID" value="<?php echo base64_encode($sDeviceID);?>">
                  <input type="hidden" name="sDevice" value="<?php echo base64_encode($sDevice);?>">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr>
                        <td width="10%"><strong>Select Time:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Select Time" name="sDeviceTime" value="<?php echo $sDeviceTime;?>" id="sDeviceTime" required></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><input type="submit" name="command" value="Save" class="btn btn-success" >&nbsp;&nbsp;<a class="btn btn-primary btn-xs" style="padding: 7px;" href="<?php echo site_url('home/setting/'.$sDevice.'/');?>">Back</a></td></tr>
                      
                    </table>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->

<script type="text/javascript">
    $(document).ready(function() {
        $('#sDeviceTime').timepicker({
            showHours: false
        });
    });
</script>
    
    
<hr>
<?php
$this->load->view('Footer');
?>
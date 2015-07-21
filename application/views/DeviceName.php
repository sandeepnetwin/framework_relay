<?php
$this->load->view('Header');
$sDeviceFullName = '';
if($sDevice == 'R')
  $sDeviceFullName = 'Relay';
if($sDevice == 'P')
  $sDeviceFullName = 'Power Center';
if($sDevice == 'V')
  $sDeviceFullName = 'Valve';

?>
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> <?php echo $sDeviceFullName;?> Name Save</li>
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
                <h3 class="panel-title">Save Name</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php echo site_url('home/deviceName/'.base64_encode($sDeviceID).'/'.base64_encode($sDevice).'/');?>" method="post">
                  <input type="hidden" name="sDeviceID" value="<?php echo base64_encode($sDeviceID);?>">
                  <input type="hidden" name="sDevice" value="<?php echo base64_encode($sDevice);?>">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr>
                        <td width="10%"><strong>Enter Name:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter Name" name="sDeviceName" value="<?php echo $sDeviceName;?>" id="sDeviceName" required></td>
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
  
</script>
    
    
<hr>
<?php
$this->load->view('Footer');
?>
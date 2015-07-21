<?php
$this->load->view('Header');

if($sIP == '')
  $sIP =  IP_ADDRESS;

if($sPort == '')
  $sPort =  PORT_NO; 

?>
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> Setting</li>
            </ol>
            <?php if($sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                Details saved successfully! 
              </div>
            <?php } ?>
            <?php if($err_sucess == '1') { ?>
              <div class="alert alert-success alert-dismissable" style="background-color: #FFC0CB;border: 1px solid #FFC0CB; color:red;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                IP and Port details required! 
              </div>
            <?php } ?>
            
          </div>
        </div><!-- /.row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Setting Page</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php echo site_url('home/setting');?>" method="post">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                      <tr>
                        <td width="10%"><strong>IP ADDRESS:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter ip address" name="relay_ip_address" value="<?php echo $sIP;?>" id="relay_ip_address"></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>PORT NO:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" class="form-control" placeholder="Enter port no" name="relay_port_no" value="<?php echo $sPort;?>" id="relay_port_no"></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>MODE:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><?php echo $sAllModes;?></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><input type="submit" name="command" value="Save Setting" class="btn btn-success" onclick="return checkModeSelected();"></td></tr>
                      
                    </table>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
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
</script>
<hr>
<?php
$this->load->view('Footer');
?>
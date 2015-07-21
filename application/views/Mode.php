<?php
$this->load->view('Header');
?>
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> Mode Change</li>
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
                <h3 class="panel-title">Mode Change Page</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php echo site_url('analog/changeMode');?>" method="post" id="formChangeMode">
                  <input type="hidden" name="iMode" value="" id="iMode">
                     <div class="row">
                      <div class="col-lg-4">
                        <div class="panel panel-success">
                          <div class="panel-heading" style="height:91px;">
                            <div class="row">
                              <div class="col-xs-6">
                                <i class="fa <?php if($iMode == '1') { echo 'fa-check'; } ?> fa-5x"></i>
                              </div>
                              <div class="col-xs-6 text-right">
                                <p class="announcement-heading" style="font-size: 25px;">Auto</p>
                              </div>
                            </div>
                          </div>
                          <a href="javascript:void(0);" onclick="submitForm('1');">
                            <div class="panel-footer announcement-bottom">
                              <div class="row">
                                <div class="col-xs-6">
                                  Switch Auto
                                </div>
                                <div class="col-xs-6 text-right">
                                  <i class="fa fa-arrow-circle-right"></i>
                                </div>
                              </div>
                            </div>
                          </a>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="panel panel-success">
                          <div class="panel-heading" style="height:91px;">
                            <div class="row">
                              <div class="col-xs-6">
                                <i class="fa <?php if($iMode == '2') { echo 'fa-check'; } ?> fa-5x"></i>
                              </div>
                              <div class="col-xs-6 text-right">
                                <p class="announcement-heading" style="font-size: 25px;">Manual</p>
                              </div>
                            </div>
                          </div>
                          <a href="javascript:void(0);" onclick="submitForm('2');">
                            <div class="panel-footer announcement-bottom">
                              <div class="row">
                                <div class="col-xs-6">
                                  Switch Manual
                                </div>
                                <div class="col-xs-6 text-right">
                                  <i class="fa fa-arrow-circle-right"></i>
                                </div>
                              </div>
                            </div>
                          </a>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="panel panel-success">
                          <div class="panel-heading" style="height:91px;">
                            <div class="row">
                              <div class="col-xs-6">
                                <i class="fa <?php if($iMode == '3') { echo 'fa-check'; } ?> fa-5x"></i>
                              </div>
                              <div class="col-xs-6 text-right">
                                <p class="announcement-heading" style="font-size: 25px;">Time-Out</p>
                              </div>
                            </div>
                          </div>
                          <a href="javascript:void(0);" onclick="submitForm('3');">
                            <div class="panel-footer announcement-bottom">
                              <div class="row">
                                <div class="col-xs-6">
                                  Switch Time-Out
                                </div>
                                <div class="col-xs-6 text-right">
                                  <i class="fa fa-arrow-circle-right"></i>
                                </div>
                              </div>
                            </div>
                          </a>
                        </div>
                      </div>
                     </div><!-- /.row -->
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
  function submitForm(iMode)
  {
    $("#iMode").val(iMode);
    $("#formChangeMode").submit();
  }
</script>
<hr>
<?php
$this->load->view('Footer');
?>
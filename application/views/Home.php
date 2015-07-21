<?php
$this->load->view('Header');
  
  if($relay_count == '')
    $relay_count = 0;
  if($valve_count == '')
    $valve_count = 0;
  if($power_count == '')
    $power_count = 0;

  $aTime = array();
  if($time != '')
  $aTime  = explode(':',$time);
?>
<style type="text/css">
  .customClass p 
  {
    font-size: 20px;
    font-weight: bold;
  }
</style>
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <h1>Dashboard <?php if(!empty($aTime)){ ?><span style="float:right;"><?php echo $aTime[0];?>:<?php echo $aTime[1];?>:<small><?php echo $aTime[2];?></small></span><?php } ?></h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
            </ol>
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              Welcome to Crystal Properties! 
            </div>
          </div>
        </div><!-- /.row -->
        <div class="row">
          <div class="col-lg-3">
            <a href="javascript:void(0);">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Links : 
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            <div class="panel panel-warning">

              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6 customClass" style="width:100%; height: 308px; text-align: center;">
                    <p><a href="<?php echo site_url('analog/changeMode');?>" style="color:#8A6D3B;">Modes</a></p>
                    <p><a href="javascript:void(0);" style="color:#8A6D3B;">Pool Lights</a></p>
                    <p><a href="javascript:void(0);" style="color:#8A6D3B;">Spa Equipment</a></p>
                    <p><a href="javascript:void(0);" style="color:#8A6D3B;">Pool Device</a></p>
                  </div>
               </div>
              </div>
            </div>
          </div> 

          <div class="col-lg-3">
            <div class="panel panel-success">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $relay_count;?></p>
                    <p class="announcement-text">Relay</p>
                  </div>
                </div>
              </div>
              <a href="<?php echo site_url('home/setting/R/');?>">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Switch Relay ON/OFF
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $valve_count;?></p>
                    <p class="announcement-text">Valve</p>
                  </div>
                </div>
              </div>
              <a href="<?php echo site_url('home/setting/V/');?>">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                     Switch Valve ON/OFF
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="panel panel-warning">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $power_count;?></p>
                    <p class="announcement-text">Power Center</p>
                  </div>
                </div>
              </div>
              <a href="<?php echo site_url('home/setting/P/');?>">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Switch Power Center ON/OFF
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="panel panel-success">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $pump_count;?></p>
                    <p class="announcement-text">Pumps</p>
                  </div>
                </div>
              </div>
              <a href="<?php echo site_url('home/setting/PS/');?>">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Switch Pumps ON/OFF
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-tasks fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading">&nbsp;</p>
                    <p class="announcement-text">Setting</p>
                  </div>
                </div>
              </div>
              <a href="<?php echo site_url('home/setting/');?>">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Add/Edit IP, PORT and Mode
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div> 

          <div class="col-lg-3">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-tasks fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading">4</p>
                    <p class="announcement-text">Input</p>
                  </div>
                </div>
              </div>
              <a href="<?php echo site_url('analog/');?>">
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Assign device to Input
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
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
  
</script>
<hr>
<?php
$this->load->view('Footer');
?>
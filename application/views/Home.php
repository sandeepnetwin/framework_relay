<?php
$this->load->view('Header');
  
  //echo date('Y-m-d H:i:s');
  
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

    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
			
			<h1>Dashboard 
			<?php if(!empty($aTime)){ ?><span style="float:right;"><?php echo $aTime[0];?>:<?php echo $aTime[1];?>:<small><?php echo $aTime[2];?></small></span><?php } ?>	
			<!--<span style="float:right;"><div id="Date"></div>
			
			<ul class="ulClock">
				<li id="hours" class="liClock"> </li>
				<li id="point" class="liClock">:</li>
				<li id="min" class="liClock"> </li>
				<li id="point" class="liClock">:</li>
				<li id="sec" class="liClock"> </li>
			</ul><div id="countdowntimer"><span id="future_date"><span></div></span>--></h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
            </ol>
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <?php if($welcome_message == '' ){ echo 'Welcome to Crystal Properties!'; } else { echo $welcome_message;} ?> 
            </div>
          </div>
        </div><!-- /.row -->
        <div class="row">
		  <!--<div class="col-lg-3">
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
                  <div class="col-xs-6 customClass" style="width:100%; height: 522px; text-align: center;">
                    <p><a href="<?php echo site_url('analog/changeMode');?>" style="color:#8A6D3B;">Modes</a></p>
                    <p><a href="javascript:void(0);" style="color:#8A6D3B;">Pool Lights</a></p>
                    <p><a href="javascript:void(0);" style="color:#8A6D3B;">Spa Devices</a></p>
                    <p><a href="javascript:void(0);" style="color:#8A6D3B;">Pool Device</a></p>
                  </div>
               </div>
              </div>
            </div>			
          </div> -->
		
		<div class="col-lg-3">
			<div class="panel panel-info">
              <div class="panel-heading" style="background-color: #f5f5f5;">
                <div class="row">
                  <div class="col-xs-6 customClass" style="width:100%; height: 543px; text-align: center;">
				  <span>Links : </span>
                    <div class="panel-heading" style="background-color:#D9EDF7; border-radius: 20px; border:1px solid; margin-top:20px;">
						<div class="row">
							<div class="col-xs-6 customClass" style="width:100%; text-align: center; font-size: 24px; height: 60px; padding-top: 12px;">
							<a href="<?php echo site_url('analog/changeMode');?>">Modes</a>
							</div>
						</div>
					</div>
					<div class="panel-heading" style="background-color:#D9EDF7; border-radius: 20px; border:1px solid; margin-top:20px;">
						<div class="row">
							<div class="col-xs-6 customClass" style="width:100%; text-align: center; font-size: 24px; height: 60px; padding-top: 12px;">
							<a href="javascript:void(0);">Pool Lights</a>
							</div>
						</div>
					</div>
					<div class="panel-heading" style="background-color:#D9EDF7; border-radius: 20px; border:1px solid; margin-top:20px;">
						<div class="row">
							<div class="col-xs-6 customClass" style="width:100%; text-align: center; font-size: 24px; height: 60px; padding-top: 12px;">
							<a href="javascript:void(0);">Spa Devices</a>
							</div>
						</div>
					</div>
					<div class="panel-heading" style="background-color:#D9EDF7; border-radius: 20px; border:1px solid; margin-top:20px;">
						<div class="row">
							<div class="col-xs-6 customClass" style="width:100%; text-align: center; font-size: 24px; height: 60px; padding-top: 12px;">
							<a href="javascript:void(0);">Pool Device</a>
							</div>
						</div>
					</div>
                  </div>
                </div>
              </div>
            </div>
		</div>	

          <div class="col-lg-3">
			<a href="<?php echo site_url('home/setting/R/');?>">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $relay_count;?></p>
                    <p class="announcement-text">24V AC Relay<br>&nbsp;</p>
                  </div>
                </div>
              </div>
              
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Switch ON/OFF
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              
            </div>
			</a>
          </div>

          <div class="col-lg-3">
		  <a href="<?php echo site_url('home/setting/V/');?>">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $valve_count;?></p>
                    <p class="announcement-text">Valve<br>&nbsp;</p>
                  </div>
                </div>
              </div>
              
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                     Switch ON/OFF
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              
            </div>
			</a>
          </div>

          <div class="col-lg-3">
		  <a href="<?php echo site_url('home/setting/P/');?>">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $power_count;?></p>
                    <p class="announcement-text">12V DC Power Center Relay</p>
                  </div>
                </div>
              </div>
              
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Switch ON/OFF
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              
            </div>
			</a>
          </div>

        <div class="col-lg-3">
		<a href="<?php echo site_url('home/setting/PS/');?>">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading"><?php echo $pump_count;?></p>
                    <p class="announcement-text">Pumps<br><br></p>
                  </div>
                </div>
              </div>
              
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Switch ON/OFF
					  <br />
					  <br />
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              
            </div>
			</a>
        </div>
		
		<div class="col-lg-3">
		<a href="<?php echo site_url('home/setting/T/');?>">
            <div class="panel panel-info">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-check fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading" style="font-size:20px;">
					<?php 
						echo $sTemperature;
					?>
					</p>
                    <p class="announcement-text">Temperature<br>Sensors</p>
                  </div>
                </div>
              </div>
              
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Temperature sensor
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              
            </div>
			</a>
        </div>

          <div class="col-lg-3">
		  <a href="<?php echo site_url('home/setting/');?>">
            <div class="panel panel-danger">
              <div class="panel-heading">
                <div class="row">
                  <div class="col-xs-6">
                    <i class="fa fa-tasks fa-5x"></i>
                  </div>
                  <div class="col-xs-6 text-right">
                    <p class="announcement-heading">&nbsp;</p>
                    <p class="announcement-text">Setting<br><br></p>
                  </div>
                </div>
              </div>
              
                <div class="panel-footer announcement-bottom">
                  <div class="row">
                    <div class="col-xs-6">
                      Settings
					  <br />
					  <br />
                    </div>
                    <div class="col-xs-6 text-right">
                      <i class="fa fa-arrow-circle-right"></i>
                    </div>
                  </div>
                </div>
              
            </div>
			</a>
          </div> 

          <div class="col-lg-3">
		  <a href="<?php echo site_url('analog/');?>">
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
              
            </div>
			</a>
          </div> 
		  
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
  
</script>
<hr>
<?php
$this->load->view('Footer');
?>
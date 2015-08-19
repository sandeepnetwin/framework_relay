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
<style>
.custom-panel-heading
{
	height : 132px !important;
}

@font-face {
    font-family: 'BebasNeueRegular';
    src: url('<?php echo site_url('assets/font/BebasNeue-webfont.eot');?>');
    src: url('<?php echo site_url('assets/font/BebasNeue-webfont.eot?#iefix');?>') format('embedded-opentype'),
         url('<?php echo site_url('assets/font/BebasNeue-webfont.woff');?>') format('woff'),
         url('<?php echo site_url('assets/font/BebasNeue-webfont.ttf');?>') format('truetype'),
         url('<?php echo site_url('assets/font/BebasNeue-webfont.svg#BebasNeueRegular');?>') format('svg');
    font-weight: normal;
    font-style: normal;

}

.clock {width:800px; margin:0 auto; padding:30px; border:1px solid #333; color:#fff; }

#Date { font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; font-size:18px; text-align:center; text-shadow:0 0 5px #00c6ff; }

.ulClock { margin:0 auto; padding:0px; list-style:none; text-align:center; }
.ulClock > li { display:inline; font-size:1em; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 5px #00c6ff; }

#point { position:relative; -moz-animation:mymove 1s ease infinite; -webkit-animation:mymove 1s ease infinite; padding-left:2px; padding-right:2px; }

@-webkit-keyframes mymove 
{
0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
50% {opacity:0; text-shadow:none; }
100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }	
}


@-moz-keyframes mymove 
{
0% {opacity:1.0; text-shadow:0 0 20px #00c6ff;}
50% {opacity:0; text-shadow:none; }
100% {opacity:1.0; text-shadow:0 0 20px #00c6ff; }	
}

</style>
<script type="text/javascript">
$(document).ready(function() {
// Create two variable with the names of the months and days in an array
var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]; 
var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]

// Create a newDate() object
var newDate = new Date();
// Extract the current date from Date object
newDate.setDate(newDate.getDate());
// Output the day, date, month and year    
$('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

setInterval( function() {
	$.getJSON('<?php echo site_url('home/getCurrentServerTime/');?>', function(json) {
		//$("#clock1").html(json.time);
		var sTime			=	json.time;
		var aTimeDetails	=	sTime.split(':');
		$("#hours").html(aTimeDetails[0]);
		$("#min").html(aTimeDetails[1]);
		$("#sec").html(aTimeDetails[2]);
	});
	},1000);
setInterval( function() {
	$.getJSON('<?php echo site_url('home/getModeTime/');?>', function(json) {
		$("#welcomeMessage").html(json.message);
	});
	},60000);	
	
	
/* setInterval( function() {
	// Create a newDate() object and extract the minutes of the current time on the visitor's
	var minutes = new Date().getMinutes();
	// Add a leading zero to the minutes value
	$("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
    },1000);
	
setInterval( function() {
	// Create a newDate() object and extract the hours of the current time on the visitor's
	var hours = new Date().getHours();
	// Add a leading zero to the hours value
	$("#hours").html(( hours < 10 ? "0" : "" ) + hours);
    }, 1000); */
	
}); 
</script>

    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
			
			<h1>Dashboard 
			<?php //if(!empty($aTime)){ ?><!--<span style="float:right;"><?php //echo $aTime[0];?>:<?php //echo $aTime[1];?>:<small><?php //echo $aTime[2];?></small></span>--><?php //} ?>	
			<span style="float:right;"><div id="Date"></div>
			
			<ul class="ulClock">
				<li id="hours" class="liClock"> </li>
				<li id="point" class="liClock">:</li>
				<li id="min" class="liClock"> </li>
				<li id="point" class="liClock">:</li>
				<li id="sec" class="liClock"> </li>
			</ul><span id="clock1"></span></span></h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
            </ol>
            <div class="alert alert-success alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <span id="welcomeMessage"><?php if($welcome_message == '' ){ echo 'Welcome to Crystal Properties!'; } else { echo $welcome_message;} ?></span> 
            </div>
          </div>
        </div><!-- /.row -->
        <div class="row">
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
							<a href="javascript:void(0);">Lights</a>
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
							<a href="javascript:void(0);">Pool Devices</a>
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
              <div class="panel-heading custom-panel-heading">
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
              <div class="panel-heading custom-panel-heading">
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
              <div class="panel-heading custom-panel-heading">
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
              <div class="panel-heading custom-panel-heading">
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
              <div class="panel-heading custom-panel-heading">
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
		  <a href="<?php echo site_url('analog/');?>">
            <div class="panel panel-danger">
              <div class="panel-heading custom-panel-heading">
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
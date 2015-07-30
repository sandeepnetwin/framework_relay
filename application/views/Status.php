<?php
$this->load->view('Header');
$aResponse  = explode(',', $response);
$cntRows    = count($aResponse);

$aName      = array('ID','SEQ','MODE','DOW','WCK','RTC','ERR','VLV','RLY','PCN','AP0','AP1',
                    'AP2','AP3','APS','TS0','TS1','TS2','TS3','TS4','TS5','LVI','RSC','LVA',
                    'PM0','PM1','PM2');

$aDesc      = array('Record identifier','Sequence number that runs from 000 ... 255','Mode',
                    'Day of week (0 – Sunday ... 6 – Saturday)','Wall clock (24 hour clock)',
                    'RTC status','HEX',
                    'Valve status, there are as many digits as there are valves configured. The field might be empty.',
                    'Relay status, there are as many digits as there are relays configured. The field might be empty.',
                    'Power center status','Analog input 0','Analog input 1','Analog input 2','Analog input 3',
                    'DC supply voltage','Temperature sensor 0 / Controller temperature','Temperature sensor 1',
                    'Temperature sensor 2','Temperature sensor 3','Temperature sensor 4','Temperature sensor 5',
                    'Level measurement [inch] instant','Remote Spa Control and digital input status','Level measurement [inch] average',
                    'Status of pump sequencer 0','Status of pump sequencer 1','Status of pump sequencer 2');
?>
    <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> Status</li>
            </ol>
          </div>
        </div><!-- /.row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Commands</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                <p>Links : <br /><strong><a href="#controllerClock">Controller Clock</a>, &nbsp;<a href="#I2C">I2C Bus Commands</a>, &nbsp;<a href="#Spa">Spa Remote Control and LED Status</a>,&nbsp;<a href="#Pentair">Pentair Pump Communication Sequencer</a>,&nbsp;<a href="#Configuration">Configuration Parameter get/set</a>,&nbsp;<a href="#Relay">Relay Status get/set</a>,&nbsp;<a href="#System">System Status Report</a>,&nbsp;<a href="#Scan">Scan One Wire Bus for Temperature Sensors</a>,&nbsp;<a href="#Valve">Valve Status get/set</a></strong></p>
                </div>            
              </div>
            </div>
          </div>
        </div>
         <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">System Status</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <table class="table table-hover">
                  <thead>
                    <tr>
                      <th style="width:15%;" class="header">Field</th>
                      <th style="width:15%;" class="header">Name</th>
                      <th style="width:20%;" class="header">Status</th>
                      <th class="header">Description</th>
                    </tr>
                  </thead>
                   <tbody>
                  <?php
                      
                      for($i=0; $i<$cntRows; $i++)
                      {
                          $sRes   = '';
                          $sDesc  = '';

                          if(preg_match('/TS/',$aName[$i]) && $aResponse[$i] == '')
                            $sRes = '0F';
                          else
                            $sRes = $aResponse[$i];

                          if($aDesc[$i] == 'HEX')
                          {
                              $sDesc  = '<strong>Hex error status :</strong><br>
                                        <table class="table table-hover">
                                          <thead>
                                            <tr>
                                              <th class="header">Bit</th>
                                              <th class="header">Hex</th>
                                              <th class="header">Description</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <tr>
                                              <td>0</td>
                                              <td>01</td>
                                              <td>One Wire Bus (temperature sensors)</td>
                                            </tr>
                                            <tr>  
                                              <td>1</td>
                                              <td>02</td>
                                              <td>Wall clock</td>
                                            </tr>
                                            <tr>  
                                              <td>2</td>
                                              <td>04</td>
                                              <td>Level measurement</td>
                                            </tr>
                                            <tr>  
                                              <td>3</td>
                                              <td>08</td>
                                              <td>I2C Bus</td>
                                            </tr>
                                            <tr>  
                                              <td>4</td>
                                              <td>10</td>
                                              <td>24VAC feed</td>
                                            </tr>
                                            <tr>    
                                              <td>5</td>
                                              <td>20</td>
                                              <td>TBA</td>
                                            </tr>
                                            <tr>  
                                              <td>6</td>
                                              <td>40</td>
                                              <td>TBA</td>
                                            </tr>
                                            <tr>  
                                              <td>7</td>
                                              <td>80</td>
                                              <td>TBA</td>
                                            </tr>
                                          </tbody>
                                        </table>
                                         ';
                          }  
                          else
                            $sDesc =   $aDesc[$i];

                          echo '<tr>
                                <td>'.$i.'</td>
                                <td>'.$aName[$i].'</td>
                                <td>'.$sRes.'</td>
                                <td>'.$sDesc.'</td>
                                </tr>';
                      }
                  ?>
                 
                  </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->
        <div class="row" id="controllerClock">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Controller Clock</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                    <section class="entry">
                        <p><strong>Commands &nbsp;(RLB&nbsp;Commands from the Linux Command Prompt )</strong></p>
                        <p>The command interface to the firmware is not meant to be human friendly; it is designed to interpret</p>
                        <p>commands from another computer. However, using ASCII commands enables human interaction. Most</p>
                        <p>commands are lower case (exceptions are the B, V and R multi I/O point set commands).</p>
                        <p><strong>Related Links</strong></p>
                        <p><strong>Controller Clock</strong></p>
                        <p>Syntax: c &lt;dow&gt; &lt;hour&gt; &lt;min&gt; &lt;sec&gt;</p>
                        <p>&lt;dow&gt; Day of week</p>
                        <p>0 Sunday</p>
                        <p>1 Monday</p>
                        <p>2 Tuesday</p>
                        <p>3 Wednesday</p>
                        <p>4 Thursday</p>
                        <p>5 Friday</p>
                        <p>6 Saturday</p>
                        <p>&lt;hour&gt; 0 … 23</p>
                        <p>&lt;min&gt; 0 … 59</p>
                        <p>&lt;sec&gt; 0 … 59</p>
                        <p><strong>Note:</strong> this command is rarely ever used directly and is intercepted when used with the rlb</p>
                        <p>server. The rlb server has a ‘C’ command that will set the clock of the relay board to the</p>
                        <p>system time of the rlb server host. A lower case ‘c’ command to the rlb server will print the</p>
                        <p>clock offset and make a suggestion how to adjust the ADJ parameter in the configuration.</p>
                        <p>&nbsp;</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="I2C">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">I2C Bus Commands</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                    <section class="entry">
                        <p><strong>I2C Bus Commands</strong></p>
                        <p>Syntax: i [&lt;adr&gt; [&lt;nrc&gt; [&lt;hex&gt;]]]</p>
                        <p>&lt;adr&gt; Hexadecimal 7 bit I2C address</p>
                        <p>&lt;nrc&gt; Number of bytes to read from device</p>
                        <p>&lt;hex&gt; Data to write to device, use hexadecimal e.g. 010203ff would send 4 bytes 01 02 03 and ff</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="Spa">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Spa Remote Control LED Status</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                    <section class="entry">
                        <p><strong>Spa Remote Control LED Status</strong></p>
                        <p>Syntax: l [&lt;0|1|2&gt;]</p>
                        <p>0 Off</p>
                        <p>1 On</p>
                        <p>2 Blink</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="Pentair">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">The Pentair Pump Communication Sequencer</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">       
                    <section class="entry">
                        <p><strong>The Pentair Pump Communication Sequencer</strong></p>
                        <p>Syntax: m [&lt;#&gt; [&lt;typ&gt; [&lt;arg&gt;]]</p>
                        <p>&lt;#&gt; Sequencer number</p>
                        <p>&lt;typ&gt; Sequencer type</p>
                        <p>type Description</p>
                        <p>. Don’t change</p>
                        <p>0 Sequencer shutdown</p>
                        <p>1 IntelliComII emulator</p>
                        <p>2 IntelliFlow VS pump sequencer</p>
                        <p>3 IntelliFlow VF pump sequencer</p>
                        <p>4 Get status VS/VF</p>
                        <p>10</p>
                        <p>&lt;arg&gt; Sequencer argument</p>
                        <p>type Sequencer</p>
                        <p>IntelliComII</p>
                        <p>arg Description</p>
                        <p>0 No contact closure</p>
                        <p>1 Contact closure 1</p>
                        <p>1</p>
                        <p>2 Contact closure 2</p>
                        <p>3 Contact closure 3</p>
                        <p>4 Contact closure 4</p>
                        <p>IntelliFlow VS pump</p>
                        <p>arg Description</p>
                        <p>2</p>
                        <p>speed Preset speed to run 1, 2, 3 or 4 &mdash; 0 is off</p>
                        <p>IntelliFlow VF pump</p>
                        <p>arg Description</p>
                        <p>3</p>
                        <p>gpm Gallons per minute set point</p>
                        <p>4 IntelliFlow VS/VF status report</p>
                        <p>There is no argument to this command</p>
                        <p>Note: The pump sequencers report messages start with an uppercase ‘M’ followed by the sequencer</p>
                        <p>number and a comma separated list of record fields.</p>
                    </section>
               </div>            
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="Configuration">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Configuration Parameters get/set</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">  
                    <section class="entry">
                        <p><strong>Configuration Parameters get/set</strong></p>
                        <p>Syntax: p [&lt;var&gt; [arg [arg …]]</p>
                        <p>&lt;var&gt; See Configuration section for variable names</p>
                        <p>Note: A ‘p’ command without arguments will output the complete parameter vector.</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div> 
        <div class="row" id="Relay">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Relay Status get/set</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">  
                    <section class="entry">
                	<p><strong>Relay Status get/set</strong></p>
                        <p>Syntax: r [&lt;#&gt; [0|1]]</p>
                        <p>&lt;#&gt; Relays are numbered 0 … (15-NVL*2). A relay output can have 2 different states.</p>
                        <p>0 Output off</p>
                        <p>1 Output on</p>
                        <p>Syntax: R [ss…]</p>
                        <p>Note: This command can be used to set relays in parallel. The system in the example has 2 valves</p>
                        <p>and therefore 12 relays:</p>
                        <p>rlb R 000000000000 turns all relays off</p>
                        <p>rlb R 111111111111 turns all relays on</p>
                        <p>rlb R ..0..0…… turns relay 2 and 5 off</p>
                        <p>The ‘.’ is a place holder and means ‘don’t change that relay’</p>
                        <p>Specifying more relays than are actually present is considered an error, specifying fewer is ok,</p>
                        <p>the unspecified relays are left alone.</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div> 
        <div class="row" id="System">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">System Status Report</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">  
                    <section class="entry">
                	<p><strong>System Status Report</strong></p>
                        <p>Syntax: s</p>
                        <p>Example: &nbsp;pi@raspberrypi -$ &nbsp; rlb s</p>
                        <p>Result:</p>
                        <p>S,078,0,4,04:21:30,0,16,,0000000000000000,00000000,0,0,1767,2333,0,96.4F,,,,,,0.00,0000,0.00,0,0,0</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div> 
        <div class="row" id="Scan">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Scan One Wire Bus for Temperature Sensors</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">  
                    <section class="entry">
                	<p><strong>Scan One Wire Bus for Temperature Sensors</strong></p>
                        <p>Description: You can set up to five temperature sensors. The one wire temperature sensors such as the DS 18B20 or the waterproof version can be used to check the pool water temperature, spa water, pump temperature, air temperature and more.</p>
                        <p>&nbsp;</p>
                        <p><a href="http://rpipool.com/wp-content/uploads/2015/07/ds1218b-temp-sensor.jpg"><img width="235" height="200" alt="ds1218b-temp-sensor" src="http://rpipool.com/wp-content/uploads/2015/07/ds1218b-temp-sensor.jpg" class="alignleft wp-image-120 "></a></p>
                        <p>&nbsp;</p>
                        <p>Syntax: t</p>
                        <p>Example: pi@rapsberrypi -$ &nbsp;rlb t</p>
                        <p>Result:&nbsp;S,078,0,4,04:21:30,0,16,,0000000000000000,00000000,0,0,1767,2333,0,<strong>96.4F, 97.4F,98.4F,99.4F,100.4F,,</strong>0.00,0000,0.00,0,0,0</p>
                        <p>Note: Example result of a system that has 4 sensors connected. The first temperature of 96.4f is always the temperature of the board. The four other temperature are reported after the board temperature. Note the last temperature is blank and therefore unknown to the system. One can conclude there are only four sensors out of the five possible temperature sensors in use from the above status report .</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="Valve">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Valve Status get/set</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">  
                    <section class="entry">
                	<p><strong>Valve Status get/set</strong></p>
                        <p><strong>Description:</strong> The RLB board enables a user to control automatic 24vac valves such as the 2400 series manufactured by Jandy or the Intermatic PE24VA.</p>
                        <p><a href="http://rpipool.com/wp-content/uploads/2015/07/jandy-valve.jpg"><img width="258" height="196" alt="jandy-valve" src="http://rpipool.com/wp-content/uploads/2015/07/jandy-valve.jpg" class="alignnone size-full wp-image-115"></a></p>
                        <p><span style="color: #3366ff;"><strong><a href="http://rpipool.com/?media_dl=110">Download RLB Manual in .pdf format</a></strong></span></p>
                        <p><strong>Limitations:</strong> You can set up and control up to eight valves.</p>
                        <p><strong>Setting the Valve Configurations: </strong>You will need to set the number of automatic valves in EEROM by using the following command</p>
                        <p><strong>Syntax:</strong> p NVL (Total number of valves)</p>
                        <p><strong>Command Prompt Example:</strong> &nbsp;pi@raspberrypi -$ rlb p NVL &nbsp;4</p>
                        <p><strong>Output:&nbsp;</strong>P,nvl,4</p>
                        <p>The above example would set the total number of valves to 4 and the output displayed after the command has been executed would be &nbsp;&nbsp;P,nvl,4</p>
                        <p>&nbsp;</p>
                        <p><b>Setting the State of the Valves&nbsp;</b></p>
                        <p><strong>Syntax:</strong> v [&lt;Valve #&gt; [0|1|2]]</p>
                        <p><strong>Command Prompt Example:</strong> &nbsp;pi@raspberrypi -$ rlb v 0 2</p>
                        <p>Note:&nbsp;rlb v 0 2 would set the automatic valve number 0 to the set position 2 which is off .</p>
                        <p><strong>Command Prompt Example 2:</strong> &nbsp;pi@raspberrypi -$ rlb v</p>
                        <p>Note: The above example would display a value of 0, 1 or 2 ( the three different states) for each configured automatic valve. &nbsp;Valve 0 controls the 24vac relays labeled zero and one therefore it is not possible to have both zero and one on at the same time.</p>
                        <p>Valve 0 controls the 24vac relays labeled 0 and 1</p>
                        <p>Valve 1 controls the 24vac relays labeled two and three,</p>
                        <p>Valve 3 controls the 24vac labeled relays four and five,</p>
                        <p>Valve 4 controls the 24 vac relays labeled six and seven.</p>
                        <p>Valve 5 controls the 24vac relays labeled eight and nine</p>
                        <p>Valve 6 controls the 24vac relays labeled ten and eleven</p>
                        <p>Valve 7 controls the 24vac relays labeled 12 and 13</p>
                        <p>Valve 8 controls the 24vac relay labeled 14 and 15</p>
                        <p>&lt;#&gt; Valves are numbered 0 … (NVL-1). &nbsp;????????????????????????????????</p>
                        <p>In summary, always remember there can be up to eight configured valves and each valve controls two positions on the rlb board. &nbsp;Each valve can have three different states and the status you get from the command (&nbsp;pi@raspberrypi -$ rlb s) will always return one character for each configured valve with a possible value of either 0,1 or 2.</p>
                        <p><strong>Set status of Valve is 1&nbsp;</strong></p>
                        <p>Output 1 is on</p>
                        <p>Output 2 is off</p>
                        <p><strong>Set status of valve is a Value of &nbsp;0 (zero)</strong></p>
                        <p>All outputs are off</p>
                        <p>Output 1 is off</p>
                        <p>Output 2 is off</p>
                        <p>Output 0 is a rather unconventional state for a pool controller, since it is expected&nbsp;that always one of the outputs would be energized. If a valve output is zero,&nbsp;the valve cannot be operated by the &nbsp;local toggle switch because there is no power to the automatic valve. This state can be&nbsp;used during power up of the controller to sequentially power the&nbsp;valves.</p>
                        <p><strong>Set status of Valve is a value of 2</strong></p>
                        <p>Output 1 is off</p>
                        <p>Output 2 is on</p>
                        <p>Note: Both outputs cannot be set to be on at the same time to prevent damage to the automatic valves.</p>
                        <p><strong>Example</strong> pi@raspberrypi ~ $ rlb s</p>
                        <p>S,015,0,0,00:06:48,0,16,<strong>10002</strong>,000000,00000000,0,0,1796,2382,0,93.8F,,,,,,0.00,0000,0.00,0,0,0</p>
                        <p>In bold and based on the output in bold font of <strong>10002</strong>, you can conclude that there are 5 valves,</p>
                        <p>Based on the value of 1 in the first character position, we know the status of Valve 1 is relay 0 is on and relay 1 is off.&nbsp;Based on the value of 0 in the second, third and fourth character positions, we know the status of Valves 2 through 7 are off.&nbsp;Based on the value of 2 in the fifth character position, we know the status of Valve 5 is relay 8 is off and &nbsp;relay 9 is on.</p>
                        <p><strong>Setting the State of Multiple Valves Simultaneously&nbsp;</strong></p>
                        <p><strong>Syntax:</strong> V [ss…]</p>
                        <p><strong>Command Prompt Example:</strong> &nbsp;pi@raspberrypi ~ $ rlb V 0102</p>
                        <p><strong>Output:&nbsp;</strong>V 0102</p>
                        <p>Note 1: rlb V 0102 would be a 4 valve system and would set valve 1 to 0&nbsp;(unusual state for pool controller), valve 2 to the 1 position, valve 3 to the 0 position and valve 4 to the 2nd position</p>
                        <p>Commands can be used to set valves in parallel. The system in the example below has 2 valves:</p>
                        <p>pi@raspberrypi ~ $ rlb V 00 turns automatic valve 0 and valve 1 to off (unusual state for pool controller)</p>
                        <p>pi@raspberrypi ~ $ rlb V 11 activates output 1 for valve 0 and output 1 for valve 2 (‘normal’ state for a pool controller)</p>
                        <p>pi@raspberrypi ~ $ rlb V 21 activates output 2 for valve 0 and output 1 for valve 1.</p>
                        <p>Just like with the relays, the ‘.’ is a place holder and means ‘don’t change that valve’</p>
                        <p>Specifying more valves than are actually present is considered an error, specifying fewer is ok,</p>
                        <p>the unspecified valves are left alone.</p>
                    </section>
                </div>            
              </div>
            </div>
          </div>
        </div>
    </div><!-- /#page-wrapper -->
<script type="text/javascript">
</script>
<hr>
<?php
$this->load->view('Footer');
?>
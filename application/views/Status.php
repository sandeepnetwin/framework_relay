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
      </div><!-- /#page-wrapper -->
<script type="text/javascript">
</script>
<hr>
<?php
$this->load->view('Footer');
?>
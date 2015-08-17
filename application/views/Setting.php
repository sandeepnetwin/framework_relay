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
                        <td width="10%"><strong>Display Desired Pool Temp on Home Page:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="radio" name="showPoolTemp" value="0" <?php if(isset($extra['Pool_Temp']) && $extra['Pool_Temp'] == '0') { echo 'checked="checked";';}?> checked="checked">&nbsp;No&nbsp;&nbsp;<input type="radio" name="showPoolTemp" value="1" <?php if(isset($extra['Pool_Temp']) && $extra['Pool_Temp'] == '1') { echo 'checked="checked";';}?>>&nbsp;Yes
						<div id="poolTempID" style="display:<?php if(isset($extra['Pool_Temp_Address']) && $extra['Pool_Temp_Address'] != '') { echo ''; } else {echo 'none';}?>">
						<strong>Select :</strong> <select name="selPoolTemp" id="selPoolTemp">
						<?php
								foreach($aTemprature as $key=>$temprature)
								{
									$strTemp	=	'';
									if($temprature != '')
									{
										$strTemp = '( '.$temprature.' )';
									}
									
									$strSelect	=	'';
									if(isset($extra['Pool_Temp_Address']) && $extra['Pool_Temp_Address'] != '')
									{
										if($extra['Pool_Temp_Address'] == $key)
										{
											$strSelect	=	'selected="selected"';
										}
									}
									
									echo '<option value="'.$key.'" '.$strSelect.'>'.$key.' '.$strTemp.'</option>';
								}

						?>
						</div>
						</td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
					  <tr>
                        <td width="10%"><strong>Display Desired Spa Temp on home page</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="radio" name="showSpaTemp" value="0" <?php if((isset($extra['Pool_Temp']) && $extra['Spa_Temp'] == '0')) { echo 'checked="checked";';}?> checked="checked">&nbsp;No&nbsp;&nbsp;<input type="radio" name="showSpaTemp" value="1" <?php if((isset($extra['Pool_Temp']) && $extra['Spa_Temp'] == '1')) { echo 'checked="checked";';}?>>&nbsp;Yes
						<div id="spaTempID" style="display:<?php if(isset($extra['Spa_Temp_Address']) && $extra['Spa_Temp_Address'] != '') { echo ''; } else {echo 'none';}?>">
						<strong>Select :</strong> <select name="selSpaTemp" id="selSpaTemp">
						<?php
								foreach($aTemprature as $key=>$temprature)
								{
									$strTemp	=	'';
									if($temprature != '')
									{
										$strTemp = '( '.$temprature.' )';
									}
									
									$strSelect	=	'';
									if(isset($extra['Spa_Temp_Address']) && $extra['Spa_Temp_Address'] != '')
									{
										if($extra['Spa_Temp_Address'] == $key)
										{
											$strSelect	=	'selected="selected"';
										}
									}	
									
									echo '<option value="'.$key.'" '.$strSelect.'>'.$key.' '.$strTemp.'</option>';
								}

						?>
						</div>
						</td>
                      </tr>
					  <tr><td colspan="3">&nbsp;</td></tr>
					  <tr>
                        <td width="10%"><strong>Enter Manual Mode Timing: </strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="text" name="manualMinutes" id="manualMinutes" value="<?php echo $manualMinutes;?>">(In Minutes)
						</td>
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
$(document).ready(function (){
	$("input[name='showPoolTemp']").click(function(){
		var checkedVal =	$(this).val();
		if(checkedVal == '1')
		{
			$("#poolTempID").show();
		}
		else
		{
			$("#poolTempID").hide();
		}
	});
	
	$("input[name='showSpaTemp']").click(function(){
		var checkedVal =	$(this).val();
		if(checkedVal == '1')
		{
			$("#spaTempID").show();
		}
		else
		{
			$("#spaTempID").hide();
		}
	});	
});
  function checkModeSelected()
  {
    var sRelayMode 		= 	$("#relay_mode").val();
	var manualMinutes	=	$("#manualMinutes").val();
	
    if(sRelayMode == '0')
    {
      $("#relay_mode").css('border','1px solid #B40404');
      alert('Please select Mode!');
      return false;
    }
    else
    {
      $("#relay_mode").css('border','');
    }
	
	if(manualMinutes != '')
	{
		if(isNaN(manualMinutes))
		{
			$("#manualMinutes").css('border','1px solid #B40404');
			alert("Please enter valid minutes!")
			return false;
		}
		else
		{
			$("#manualMinutes").css('border','');
			
		}
	}
	
	return true;
	

  }
  
  
</script>
<hr>
<?php
$this->load->view('Footer');
?>
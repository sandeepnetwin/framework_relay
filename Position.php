<?php
$sPositionID			= '';
$sPositionName			= '';
$sPositionActive 		= '';

if(!empty($positionDetails))
{
	foreach($positionDetails as $Position)
	{
		$sPositionID		= $Position->id;
		$sPositionName		= $Position->position_name;
		$sPositionActive 	= $Position->position_active;
	}
}

$sButtonText	=	'';
if($sPositionID == '')
	$sButtonText = 'Save Position';
else if($sPositionID != '')
	$sButtonText = 'Update Position';

?>
    <div id="page-wrapper">
        <div class="row">
          <div class="col-lg-12">
             <ol class="breadcrumb">
				  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
				  <li><a href="<?php echo base_url('dashboard/position');?>">All Positions</a></li>
				  <li class="active">Add/Edit Position</li>
				</ol>
          </div>
        </div><!-- /.row -->
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading" style="border-top-left-radius: 0px;border-top-right-radius: 0px;">
                <h3 class="panel-title" style="color:#FFF;">Add/Edit Page</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                  <form action="<?php echo site_url('dashboard/positionAddEdit');?>" method="post">
				  <input type="hidden" name="positionID" value="<?php echo $sPositionID;?>">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
                        <td colspan="3"><span style="color:#FF0000;">* indicates required field</span></td>
                    </tr>
					 <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="24%"><strong>Name: <span class="mandetory">*</span></strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="75%"><input type="text" class="form-control" placeholder="Enter Position Name" name="sPositionName" value="<?php echo $sPositionName;?>" id="sPositionName" required></td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr>
                        <td width="10%"><strong>Active:</strong></td>
                        <td width="1%">&nbsp;</td>
                        <td width="89%"><input type="radio" name="sPositionActive" value="0" <?php if($sPositionActive == '0'|| $sPositionActive == ''){echo 'checked="checked"';} ?> checked="checked">&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" <?php if($sPositionActive == '1'){echo 'checked="checked"';} ?> name="sPositionActive" value="1">&nbsp;Yes</td>
                      </tr>
                      <tr><td colspan="3">&nbsp;</td></tr>
                      <tr><td colspan="3"><span class="btn btn-green"><input type="submit" name="command" value="<?php echo $sButtonText;?>" class="btn btn-success" onclick="return checkForm();"></span>&nbsp;&nbsp;<span class="btn btn-red"><input type="button" name="back" value="Back" class="btn btn-success" onclick="javascript:location.href='<?php echo base_url('dashboard/position');?>';"></span></td></tr>
                      
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
	
});
  function checkForm()
  {
	return true;
  }
</script>

<?php

?>
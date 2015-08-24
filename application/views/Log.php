<?php
$this->load->view('Header');
/* $sAccess 		= '';
$sModule	    = 13;

  //Get Permission Details
  $userID = $this->session->userdata('id');
  $aPermissions = json_decode(getPermissionOfModule($userID));
 
  $aModules 		= $aPermissions->sPermissionModule;	
  $aAllActiveModule = $aPermissions->sActiveModule;
  
  $sAccessKey	= 'access_'.$sModule;
			  
  if(!empty($aModules))
  {
	  if(in_array($sModule,$aModules->ids))
	  {
		 $sAccess 		= $aModules->$sAccessKey;
	  }
  }
  
  if($sAccess == '')
	$sAccess = '2' ; 
  
  if($sAccess == '0') {redirect(site_url('home/'));} */
?>
<style>
.ui-datepicker-month
{
	color : #000 !important;
}
</style>
<link rel="stylesheet" href="<?php echo site_url('/assets/jquery-ui/jquery-ui.css');?>">
  <script src="<?php echo site_url('/assets/jquery-ui/jquery-ui.js');?>"></script>
    <script>
  $(function() {
    $( "#sFromDate" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
	  dateFormat: 'y-mm-dd',
      onClose: function( selectedDate ) {
        $( "#sToDate" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#sToDate" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
	  dateFormat: 'y-mm-dd',
      onClose: function( selectedDate ) {
        $( "#sFromDate" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>
    <div id="page-wrapper">
		<div class="row">
          <div class="col-lg-12">
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-dashboard"></i> <a href="<?php echo site_url();?>" style="color:#333;">Dashboard</a> >> Log</li>
            </ol>
          </div>
        </div><!-- /.row -->
		
		<div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-body">
                <div id="morris-chart-area">
                <p style="word-wrap: break-word;">
				<form name="searchLog" action="<?php echo site_url('/home/getLogDetails/');?>" method="post">
				<label for="from">From</label>
				<input type="text" id="sFromDate" name="sFromDate" value="<?php echo $sStartDate;?>">
				<label for="to">to</label>
				<input type="text" id="sToDate" name="sToDate" value="<?php echo $sEndDate;?>">&nbsp;&nbsp;
				<input type="submit" name="searchLog" value="Search" class="btn btn-success">
				</form>
				</p>
                </div>            
              </div>
            </div>
          </div>
        </div>
		
		
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title">Log Details : <?php echo $sDate; ?></h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area">
                <p style="word-wrap: break-word;"><?php echo $Log; ?></p>
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
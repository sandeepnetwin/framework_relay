<!--  
Author : Dhiraj S.
-->

<div id="page-wrapper">

<div class="row">
  <div class="col-lg-12">
		<ol class="breadcrumb">
		  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
		  <li class="active">All Positions</li>
		  <a class="btn btn-green btn-small" style="float:right;" onclick="javascript:location.href='<?php echo base_url('dashboard/positionAddEdit/'); ?>';"><span>Add New Position</span></a>
</ol>
	<?php if($msg != '') { ?>
	  <div class="alert alert-success alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo $msg;?>
	  </div>
	<?php } ?>
	<?php if($err != '') { ?>
	  <div class="alert alert-success alert-dismissable" style="background-color: #FFC0CB;border: 1px solid #FFC0CB; color:red;">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo $err;?>
	  </div>
	<?php } ?>
  </div>
</div><!-- /.row -->
	<div class="table-responsive">
	  <table class="table tablesorter" style="color:#FFF;">
		<thead>
		  <tr style="font-weight:bold;">
			<th class="header">Position Name <i class="fa fa-sort"></i></th>
			<th class="header">Status</th>
			<th class="header">Action</th>
		  </tr>
		</thead>
		<tbody>
		<?php if(!empty($allPositions))
			  { 
				foreach($allPositions as $Position) 
				{
		?>
		  <tr>
			<td><?php echo $Position->position_name;?></td>
			<td><?php if($Position->position_active){echo '<a class="btn btn-green btn-small"><span>Active</span></a>';} else {echo '<a class="btn btn-red btn-small"><span>Inactive</span></a>';}?></td>
		   <td><a class="btn btn-green btn-small" href="<?php echo site_url('dashboard/positionAddEdit/'.base64_encode($Position->id).'/');?>"><span>Edit</span></a> &nbsp; <a class="btn btn-red btn-small" href="<?php echo site_url('dashboard/positionDelete/'.base64_encode($Position->id).'/');?>"><span>Delete</span></a></td>
		  </tr>
		<?php 	}
			  } 
			  else 
			  { 
		  ?>  
		  <tr>
			<td colspan="5"><span style="color:red; font-weight:bold;">No Positions Available!</span></td>
		   </tr>
		<?php } ?>
		</tbody>
	  </table>
	</div>
</div><!-- /#page-wrapper -->
<script type="text/javascript">
  
</script>

<?php

?>
<?php
	/* $arrGeneral	=	array();
	if(!empty($arrDetails))
	{
		if(isset($arrDetails['General']))
			$arrGeneral = unserialize($arrDetails['General']);
	} */
?>
<h3>Home</h3>
<section>
<div class="col-sm-6">
	<label for="strType">Do you have a pool only, spa only and or a pool and spa?:<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Please select the mode from the list which you have?" /></label>
	<select name="strType" id="strType" class="form-control required" onchange="checkType(this.value)">
		<option <?php if(isset($arrGeneral['type']) && $arrGeneral['type'] == ''){ echo 'selected="selected"';} ?> value="">Select Type</option>
		<option <?php if(isset($arrGeneral['type']) && $arrGeneral['type'] == 'pool'){ echo 'selected="selected"';} ?> value="pool">Pool only</option>
		<option <?php if(isset($arrGeneral['type']) && $arrGeneral['type'] == 'spa'){ echo 'selected="selected"';} ?> value="spa">Spa only</option>
		<option <?php if(isset($arrGeneral['type']) && $arrGeneral['type'] == 'both'){ echo 'selected="selected"';} ?> value="both">Pool and Spa</option>
	</select>
	<div style="height:10px">&nbsp;</div>
	<label for="pool_maximum_temperature">What is the maximum temperature expressed in Fahrenheit that you would like to be able to set the pool temperature to ?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Set Maximum Pool Temperature that you would like to set!" /></label>
   <input type="text" name="pool_maximum_temperature" value="<?php if(isset($arrGeneral['pool_max_temp']) && $arrGeneral['pool_max_temp'] != '') { echo $arrGeneral['pool_max_temp'];}?>" id="pool_maximum_temperature" class="form-control inputText <?php if($strPoolRequired == 'Yes') { echo 'required';} ?>" <?php if($strPoolRequired == '') { echo 'readonly="readonly"';} ?>>
   <div style="height:10px">&nbsp;</div>
	<label for="pool_temperature">Desire pool temperature when user is in Pool Mode?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Set temprature when user is in pool!" /></label>
   <input type="text" name="pool_temperature" value="<?php if(isset($arrGeneral['pool_temp']) && $arrGeneral['pool_temp'] != '') { echo $arrGeneral['pool_temp'];}?>" id="pool_temperature" class="form-control inputText <?php if($strPoolRequired == 'Yes') { echo 'required';} ?>" <?php if($strPoolRequired == '') { echo 'readonly="readonly"';} ?>>
   <div style="height:10px">&nbsp;</div>
	<label for="pool_manual">The maximum allotted time for Pool Manual Mode expressed in minutes?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="For what time Pool mode is active in Manual mode!" /></label>
   <input type="text" name="pool_manual" value="<?php if(isset($arrGeneral['pool_manual']) && $arrGeneral['pool_manual'] != '') { echo $arrGeneral['pool_manual'];}?>" id="pool_manual" class="form-control inputText <?php if($strPoolRequired == 'Yes') { echo 'required';} ?>" <?php if($strPoolRequired == '') { echo 'readonly="readonly"';} ?>>
	<div style="height:10px">&nbsp;</div>
	
</div>
<div class="col-sm-6">
   <?php
		$strPoolRequired	=	'';
		$strSpaRequired		=	'';
		if(isset($arrGeneral['type']) && $arrGeneral['type'] == 'pool')
		{
			$strPoolRequired		=	'Yes';
		}
		if(isset($arrGeneral['type']) && $arrGeneral['type'] == 'spa')
		{
			$strSpaRequired			=	'Yes';
		}
		if(isset($arrGeneral['type']) && $arrGeneral['type'] == 'both')
		{
			$strPoolRequired		=	'Yes';
			$strSpaRequired			=	'Yes';
		}
	?>
	
	<label for="pool_temperature">What is the maximum temperature expressed in Fahrenheit that you would like to be able to set the spa temperature to?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Set Maximum Spa Temperature that you would like to set!" /></label>
   <input type="text" name="spa_maximum_temperature" value="<?php if(isset($arrGeneral['spa_max_temp']) && $arrGeneral['spa_max_temp'] != '') { echo $arrGeneral['spa_max_temp'];}?>" id="spa_maximum_temperature" class="form-control inputText <?php if($strSpaRequired == 'Yes') { echo 'required';} ?>" <?php if($strSpaRequired == '') { echo 'readonly="readonly"';} ?>>
   <div style="height:10px">&nbsp;</div>
	<label for="spa_temperature">Desire spa temperature when user is in Spa Mode?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="Set temperature when user is in spa!" /></label>
   <input type="text" name="spa_temperature" value="<?php if(isset($arrGeneral['spa_temperature']) && $arrGeneral['spa_temperature'] != '') { echo $arrGeneral['spa_temperature'];}?>" id="spa_temperature" class="form-control inputText <?php if($strSpaRequired == 'Yes') { echo 'required';} ?>" <?php if($strSpaRequired == '') { echo 'readonly="readonly"';} ?>>
   <div style="height:10px">&nbsp;</div>
	<label for="spa_manual">The maximum allotted time for Spa Mode expressed in minutes?<span class="requiredMark">*</span>&nbsp;<img src="<?php echo HTTP_ASSETS_PATH.'images/help.png';?>" width="24" class="tooltipster-icon" title="For what time Spa mode is active in Manual mode!" /></label>
   <input type="text" name="spa_manual" value="<?php if(isset($arrGeneral['spa_manual']) && $arrGeneral['spa_manual'] != '') { echo $arrGeneral['spa_manual'];}?>" id="spa_manual" class="form-control inputText <?php if($strSpaRequired == 'Yes') { echo 'required';} ?>" <?php if($strSpaRequired == '') { echo 'readonly="readonly"';} ?>>
   <div style="height:10px">&nbsp;</div>
	
</div>
</section>
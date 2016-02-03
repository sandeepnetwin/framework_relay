<?php

$sAccess 	= '';
$sModule	= 21;

//Get Permission Details
$userID       = $this->session->userdata('id');
$aPermissions = json_decode(getPermissionOfModule($userID));

$aModules         = $aPermissions->sPermissionModule;	
$aAllActiveModule = $aPermissions->sActiveModule;

$sAccessKey	= 'access_'.$sModule;

if(!empty($aModules))
{
  if(in_array($sModule,$aModules->ids))
  {
	 $sAccess 		= $aModules->$sAccessKey;
  }
  else if(!in_array($sModule,$aModules->ids)) 
  {
	$sAccess 		= '0'; 
  }
}

if($sAccess == '')
$sAccess = '2' ;

if($sAccess == '0') {redirect(site_url('home/'));} 

$arrGeneral     = array();
$arrDevice 	= array();
$arrHeater 	= array();
$arrMore	= array();

if(!empty($arrDetails))
{
    if(isset($arrDetails['General']))
            $arrGeneral = unserialize($arrDetails['General']);
    if(isset($arrDetails['Device']))
            $arrDevice = unserialize($arrDetails['Device']);
    if(isset($arrDetails['Heater']))
            $arrHeater = unserialize($arrDetails['Heater']);
    if(isset($arrDetails['More']))
            $arrMore = unserialize($arrDetails['More']);
}

	$iTotalIP	=	count($aIPDetails);
	  
	$sIPOptions	=	'';
	$iFirstIPId	=	'';	
	
	if($BackToIP != '')
		$iFirstIPId	= $BackToIP;
	
	

//$iValveCnt = count($ValveRelays);
$iPumpCnt  = count(${"Pumps"});
$iLightCnt = $extra['LightNumber'];

$iRelayCnt = count($sRelays);
$iPowerCnt = count($sPowercenter);

//Parameter for Tabs
$aParameter	=array('arrGeneral'=>$arrGeneral,'arrDevice'=>$arrDevice,'arrHeater'=>$arrHeater,'arrMore'=>$arrMore,'sAccess'=>$sAccess);

//'iValveCnt'=>$iValveCnt,'iPumpCnt'=>$iPumpCnt,'iLightCnt'=>$iLightCnt,'iRelayCnt'=>$iRelayCnt,'iPowerCnt'=>$iPowerCnt,

?>
<style>
	.requiredMark
	{
		color: #ff0000;
	}
	.questionText
	{
		line-height:15px;
	}
	.trClass
	{
		vertical-align:top !important;
	}
	.inputText
	{
		height:35px !important;
	}
	.wizard > .content
	{
		padding:0px !important;
		overflow-y: scroll;
	}
	.wizard > .content > .body
	{
		height : 100% !important;
		width : 100% !important;
	}
	.changeLink
	{
		color: blue; text-decoration: underline; font-size: 10px; cursor:pointer;	
	}
	.relayText
	{
		color: rgb(255, 0, 0); font-size: 10px;
	}
	.confHeader
	{
		color: #4fc4fe;
		font-style: italic;
		font-weight: 400;
		margin: 0;
	}
	.removeBorder > tbody > tr > td
	{
		border-top:0 none !important;
	}
	li > a:hover, li > a:focus
	{
		color:#FFFFFF !important;
	}
	.disableConfig
	{
		opacity:0.5; 
		pointer-events: none;
	}
	.tipso_style_custom 
	{
		border-bottom: medium none !important;
		vertical-align:middle;
	}
</style>

<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH.'steps/css/jquery.steps.css';?>">
<script src="<?php echo HTTP_ASSETS_PATH.'steps/lib/modernizr-2.6.2.min.js';?>"></script>
<script src="<?php echo HTTP_ASSETS_PATH.'steps/lib/jquery.cookie-1.3.1.js';?>"></script>
<script src="<?php echo HTTP_ASSETS_PATH.'steps/build/jquery.steps.js';?>"></script>
<script src="<?php echo HTTP_ASSETS_PATH.'steps/main.js';?>"></script>

<script type="text/javascript" src="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.js?v=2.1.5';?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.css?v=2.1.5';?>" media="screen" />

<script type="text/javascript">
    $(function ()
    {
		$('.fancybox').fancybox({
                autoSize : false,
                width:	'600',
                height: 'auto'
        });
        $('.fancyboxPump').fancybox({
                autoSize : false,
                height: 'auto'
        });
        //$('.tooltip').tooltipster();
        $('#wizard-t-3').click();


        $( document ).tooltip({
								position: {
									my: "center bottom-20",
									at: "center top",
									using: function( position, feedback ) {
									$( this ).css( position );
									$( "<div>" )
									.addClass( "arrow" )
									.addClass( feedback.vertical )
									.addClass( feedback.horizontal )
									.appendTo( this );
									}
								}
							});
    });
	
	function showReason(val)
	{
		if( val == 0 )
		{
			$("#reasonValveBlk").show();
			$("#reasonValvelbl").show();
			$("#reasonValve").show();
			$("#reasonValve").addClass('required');
		}
		else
		{
			$("#reasonValveBlk").hide();
			$("#reasonValve").hide();
			$("#reasonValvelbl").hide();
			$("#reasonValve").removeClass('required');
		}
	}
	
	function valveChange()
	{
		var valveNumber	=	$('#strValve').val();
		
		for (i = 0; i <= valveNumber; i++) 
		{
			if(i == 0)
			{
				$("#valve_actuated").html('<option value="">Select Valve Quantity</option><option value="'+i+'">Valve '+i+' is actuated</option>');
				$(".valveShow").hide();
			}
			else
			{
				$("#valve_actuated").append('<option value="'+i+'">Valve '+i+' is actuated</option>');
				$("#valveShow"+i).show();
			}
		}
		
		
		
		var arrValveAssignNumber	= 	Array();
		var cntValve				=	0;
		$(".valveAssign").each(function(){
			cntValve++;
			var valveNumber1 = $(this).val();
			if($("#lableRelayValve-"+valveNumber1).hasClass('checked'))
			{	
				arrValveAssignNumber.push(valveNumber1);
			}
		});
		
		if(cntValve == valveNumber)
		{
			$("[id^='lableRelayValve-']").addClass('checked');
			$("[id^='relayValve-']").prop('checked',true);
		}
		else
		{
			if(arrValveAssignNumber.length > valveNumber)
			{
				$("[id^='lableRelayValve-']").removeClass('checked');
				if(valveNumber != 0)
					alert("Please assign "+valveNumber+" valve!");
				return false;
			}
		}
	}
	
	function pumpChange()
	{
		var pumpNumber	=	$('#automatic_pumps').val();
		
		for (i = 1; i <= pumpNumber; i++) 
		{
			$("#trPump"+i).show();
			$("#trPumpShow"+i).show();
		}
		
		for (i = (parseInt(pumpNumber)+1); i <= 3; i++) 
		{
			$("#trPump"+i).hide();
			$("#trPumpShow"+i).hide();
		}
		//if(pumpNumber == 1 || pumpNumber == 2)
			//$(".content").css('height','auto');
		//if(pumpNumber == 3)
			//$(".content").css('height','530px');
		
		var arrPumpAssignNumber	= 	Array();
		var cntPump				=	0;
		$(".pumpAssign").each(function(){
			cntPump++;
			var pumpNumber1 = $(this).val();
			if($("#lableRelayPump-"+pumpNumber1).hasClass('checked'))
			{	
				arrPumpAssignNumber.push(pumpNumber1);
			}
		});
		
		if(cntPump == pumpNumber)
		{
			$("[id^='lableRelayPump-']").addClass('checked');
			$("[id^='relayPump-']").prop('checked',true);
		}
		else
		{
			if(arrPumpAssignNumber.length > pumpNumber)
			{
				$("[id^='lableRelayPump-']").removeClass('checked');
				if(pumpNumber != 0)
					alert("Please assign "+pumpNumber+" Pump!");
				return false;
			}
		}
	}
	
	function showHeater()
	{
		var heaterNumber	=	$('#automatic_heaters_question1').val();
		
		for (i = 1; i <= heaterNumber; i++) 
		{
			$("#trHeater"+i).show();
			$("#trHeaterWork"+i).show();
			$("#trHeaterPump"+i).show();
		}		
		
		for (i = (parseInt(heaterNumber)+1); i <= 3; i++) 
		{
			$("#trHeater"+i).hide();
			$("#trHeaterWork"+i).hide();
			$("#trHeaterPump"+i).hide();
			
			$('[id^="trHeaterSub'+i+'_"]').hide();
		}
		
		/* for (i = 1; i <= heaterNumber; i++) 
		{
			for(j = 1; j <= heaterNumber; j++)
			{
				if(j == 1)
					$("#HeaterPump"+i).html('<option value="'+j+'">Pump '+j+'</option>');
				else 
					$("#HeaterPump"+i).append('<option value="'+j+'">Pump '+j+'</option>');	
			}
		} */
		
		var arrHeaterAssignNumber	= 	Array();
		var cntHeater				=	0;
		$(".heaterAssign").each(function(){
			cntHeater++;
			var heaterNumber1 = $(this).val();
			if($("#lableRelayHeater-"+heaterNumber1).hasClass('checked'))
			{	
				arrHeaterAssignNumber.push(heaterNumber1);
			}
		});
		
		if(cntHeater == heaterNumber)
		{
			$("[id^='lableRelayHeater-']").addClass('checked');
			$("[id^='relayHeater-']").prop('checked',true);
		}
		else
		{
			if(arrHeaterAssignNumber.length > heaterNumber)
			{
				$("[id^='lableRelayPump-']").removeClass('checked');
				if(heaterNumber != 0)
					alert("Please assign "+heaterNumber+" Heater!");
				return false;
			}
		}
	}
	
	function showLight()
	{
		var lightNumber	=	$('#no_light').val();
		
		var arrLightAssignNumber	= 	Array();
		var cntLight				=	0;
		$(".lightAssign").each(function(){
			cntLight++;
			var lightNumber1 = $(this).val();
			if($("#lableRelayHeater-"+lightNumber1).hasClass('checked'))
			{	
				arrLightAssignNumber.push(lightNumber1);
			}
		});
		
		if(cntLight == lightNumber)
		{
			$("[id^='lableRelayLight-']").addClass('checked');
			$("[id^='relayLight-']").prop('checked',true);
		}
		else
		{
			if(arrLightAssignNumber.length > lightNumber)
			{
				$("[id^='lableRelayLight-']").removeClass('checked');
				if(lightNumber != 0)
					alert("Please assign "+lightNumber+" Light!");
				return false;
			}
		}
	}
	
	
	function showBlower()
	{
		var blowerNumber	=	$('#no_blower').val();
		
		var arrBlowerAssignNumber	= 	Array();
		var cntBlower				=	0;
		$(".blowerAssign").each(function(){
			cntBlower++;
			var blowerNumber1 = $(this).val();
			if($("#lableRelayBlower-"+blowerNumber1).hasClass('checked'))
			{	
				arrBlowerAssignNumber.push(blowerNumber1);
			}
		});
		
		if(cntBlower == blowerNumber)
		{
			$("[id^='lableRelayBlower-']").addClass('checked');
			$("[id^='relayBlower-']").prop('checked',true);
		}
		else
		{
			if(arrBlowerAssignNumber.length > blowerNumber)
			{
				$("[id^='lableRelayBlower-']").removeClass('checked');
				if(blowerNumber != 0)
					alert("Please assign "+blowerNumber+" Blower!");
				return false;
			}
		}
	}
	
	function showMisc()
	{
		var miscNumber	=	$('#no_misc').val();
		
		var arrMiscAssignNumber	= 	Array();
		var cntMisc				=	0;
		$(".miscAssign").each(function(){
			cntMisc++;
			var miscNumber1 = $(this).val();
			if($("#lableRelayMisc-"+miscNumber1).hasClass('checked'))
			{	
				arrMiscAssignNumber.push(miscNumber1);
			}
		});
		
		if(cntMisc == miscNumber)
		{
			$("[id^='lableRelayMisc-']").addClass('checked');
			$("[id^='relayMisc-']").prop('checked',true);
		}
		else
		{
			if(arrMiscAssignNumber.length > miscNumber)
			{
				$("[id^='lableRelayMisc-']").removeClass('checked');
				if(miscNumber != 0)
					alert("Please assign "+miscNumber+" Miscelleneous Device!");
				return false;
			}
		}
	}
	
	function checkType(type)
	{
		if(type == '')
		{
			$("#pool_maximum_temperature").attr('readonly','readonly');
			$("#pool_temperature").attr('readonly','readonly');
			$("#pool_manual").attr('readonly','readonly');
			$("#display_pool_temp").attr('disabled','disabled');
			$("#display_spa_temp").attr('disabled','disabled');
			$("#spa_maximum_temperature").attr('readonly','readonly');
			$("#spa_temperature").attr('readonly','readonly');
			$("#spa_manual").attr('readonly','readonly');
		}
		else if(type == 'spa')
		{
			$("#spa_maximum_temperature").removeAttr('readonly').addClass('required');
			$("#spa_temperature").removeAttr('readonly').addClass('required');
			$("#spa_manual").removeAttr('readonly').addClass('required');
			$("#display_spa_temp").removeAttr('disabled').addClass('required');
			
			$("#pool_maximum_temperature").attr('readonly','readonly').removeClass('required');
			$("#pool_temperature").attr('readonly','readonly').removeClass('required');
			$("#pool_manual").attr('readonly','readonly').removeClass('required');
			$("#display_pool_temp").attr('disabled','disabled').removeClass('required');
		}
		else if(type == 'pool')
		{
			$("#pool_maximum_temperature").removeAttr('readonly').addClass('required');
			$("#pool_temperature").removeAttr('readonly').addClass('required');
			$("#pool_manual").removeAttr('readonly').addClass('required');
			$("#display_pool_temp").removeAttr('disabled').addClass('required');
			
			$("#spa_maximum_temperature").attr('readonly','readonly').removeClass('required');
			$("#spa_temperature").attr('readonly','readonly').removeClass('required');
			$("#spa_manual").attr('readonly','readonly').removeClass('required');
			$("#display_spa_temp").attr('disabled','disabled').removeClass('required');
		}
		else if(type == 'both')
		{
			$("#pool_maximum_temperature").removeAttr('readonly').addClass('required');
			$("#pool_temperature").removeAttr('readonly').addClass('required');
			$("#pool_manual").removeAttr('readonly').addClass('required');
			$("#display_pool_temp").removeAttr('disabled').addClass('required');
			$("#spa_maximum_temperature").removeAttr('readonly').addClass('required');
			$("#spa_temperature").removeAttr('readonly').addClass('required');
			$("#spa_manual").removeAttr('readonly').addClass('required');
			$("#display_spa_temp").removeAttr('disabled').addClass('required');
		}
		
	}
	
	function showRelays(val,cnt)
	{
		if(val == '')
			$('[id^="trHeaterSub'+cnt+'_"]').hide();
		else
		{
			$('[id^="trHeaterSub'+cnt+'_"]').hide();
			$('#trHeaterSub'+cnt+'_'+val).show();
		}
	}
	
	function showRelaysLight(val,cnt)
	{
		if(val == '')
			$('[id^="trLightSub'+cnt+'_"]').hide();
		else
		{
			$('[id^="trLightSub'+cnt+'_"]').hide();
			$('#trLightSub'+cnt+'_'+val).show();
		}
	}
	
	function showRelaysBlower(val,cnt)
	{
		if(val == '')
				$('[id^="trBlowerSub'+cnt+'_"]').hide();
		else
		{
				$('[id^="trBlowerSub'+cnt+'_"]').hide();
				$('#trBlowerSub'+cnt+'_'+val).show();
		}
	}
	
	function showRelaysMisc(val,cnt)
	{
		if(val == '')
				$('[id^="trMiscSub'+cnt+'_"]').hide();
		else
		{
				$('[id^="trMiscSub'+cnt+'_"]').hide();
				$('#trMiscSub'+cnt+'_'+val).show();
		}
	}
	
	function showValveDetails(ipID)
	{
		var valveNumber	=	$("#valveNumber_"+ipID).val();
		
		if(isNaN(valveNumber))
		{
			$("#valveNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid valve numbers!");
			return false;
		}
		else if(valveNumber > 8)
		{
			$("#valveNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valve numbers less than or equal to 8!");
			return false;
		}
		else
		{
			$("#valveNumber_"+ipID).css('border','');
		}
		
		$('#valveTable_'+ipID+' > tbody > tr:gt(0)').hide();
		
		for(j = 0; j < valveNumber; j++)
		{
			//alert("#trPumpDetails"+j);
			$("#trValveDetails"+j+"_"+ipID).show();
		}
		
		if(valveNumber > 0)
		{
			$("#valveSaveConf_"+ipID).show();
		}
		else
			$("#valveSaveConf_"+ipID).hide();
	}
	
	function removeRow(table)
	{
		$("#"+table).find("tr:gt(0)").remove();
		
		$("#valveNumber").val($("#valveNumber").val() - 1);
		
		var valveNumber = $("#valveNumber").val();
		
		for(j = 0; j < valveNumber; j++)
		{
			$("#"+table).append('<tr><td><strong>Valve '+j+'</strong>&nbsp;<span class="relayText">(Enter relays in sequence)</span></td><td>&nbsp;</td><td><input type="text" style="width:100px;" class=" inputText" id="sRelay'+j+'_1">&nbsp;&nbsp;<input type="text" style="width:100px;" class=" inputText" id="sRelay'+j+'_2">&nbsp;<a class="changeLink" href="javascript:void(0);" onclick="removeRow(\'valveTable\')">Remove</a><div style="height: 5px;"> </div><select class="form-control" style="width:100px !important; display:inline;" name="valveDirection_'+j+'_1"><option value="pool">Pool</option><option value="spa">Spa</option></select>&nbsp;&nbsp;<select class="form-control" style="width:100px !important; display:inline;" name="valveDirection_'+j+'_2"><option value="pool">Pool</option><option value="spa">Spa</option></select></td></tr>');
		}
		
		var rowCount = $('#'+table+' tr').length;
		
		if(rowCount > 1)
		{
			$("#"+table).append('<tr><td colspan="3"><a href="javascript:void(0);" class="btn btn-middle"><span>Save</span></a>&nbsp;&nbsp;<span id="loadingImg" style="display:none; vertical-align: middle;"><img src="<?php echo site_url('assets/images/loading.gif');?>" alt="Loading...." width="32" height="32"></span></td></tr>');
		}
	}
	
	function checkAndSaveRelays(ipID)
	{
		$("#loadingImg_"+ipID).show();
		var rowCount 		=	$('#valveTable_'+ipID).find("tr:visible:gt(0)").length;
		var arrRelayStart 	=	['0','2','4','6','8','10','12','14'];
		var arrRelayEnd   	=	['1','3','5','7','9','11','13','15'];
		
		var arrValve	= Array();
		/* alert(rowCount);
		return false; */
		for(i = 0; i < (rowCount-1); i++)
		{
			var relay1 = $("#sRelay"+i+"_"+ipID+"_1").val();
			var relay2 = $("#sRelay"+i+"_"+ipID+"_2").val();
			var relay1Position = arrRelayStart.indexOf(relay1);
			var relay2Position = arrRelayEnd.indexOf(relay2);
			if(relay1Position != -1){
				$("#sRelay"+i+"_1").css('border','');
				arrValve[i]	= Array();
				arrValve[i].push(relay1);
			}
			else
			{
				$("#sRelay"+i+"_"+ipID+"_1").css('border','1px solid red');
				alert("Please select valid relay number for relay 1 for valve "+i);
				return false;
			}
			
			if(relay2Position != -1){
				$("#sRelay"+i+"_"+ipID+"_2").css('border','');
				
			}
			else
			{
				$("#sRelay"+i+"_"+ipID+"_2").css('border','1px solid red');
				alert("Please select valid relay number for relay for valve "+i);
				return false;
			}
				
			if(arrRelayEnd[relay1Position] != relay2)
			{
				$("#sRelay"+i+"_"+ipID+"_2").css('border','1px solid red');
				alert("Relay Number must be in sequence!");
				return false;
			}
			else
			{	
				$("#sRelay"+i+"_"+ipID+"_2").css('border','');
				arrValve[i].push(relay2);
			}
			
			arrValve[i].push($("#valveDirection_"+i+"_"+ipID+"_1").val());
			arrValve[i].push($("#valveDirection_"+i+"_"+ipID+"_2").val());
		}
		
		/* console.log(arrValve);
		return false; */
		
		
		$.ajax({
		type: "POST",
		async:false,
		url: "<?php echo site_url('home/saveValveRelayConf/');?>", 
		data: {valve:JSON.stringify(arrValve),sDevice:'V',ipID : ipID},
		success: function(data) {
				//var response	=	$.parseJSON(data);
				
				var numValve	=	data;
				
				//$("#strValveNum").val(numValve);
				
				$('#strValve').find('option:gt(1)').remove();
				$('#valve_actuated').find('option:gt(1)').remove();
				
				for(i = 1; i <= numValve; i++ )
				{
					$("#strValve").append('<option value="'+i+'">'+i+'</option>');
					$("#valve_actuated").append('<option value="'+i+'">Valve '+i+' is actuated</option>');
				}
				
				$.ajax({
					type: "POST",
					async:false,
					url: "<?php echo site_url('home/getAssignValveDetails/');?>", 
					data: {ipID:ipID},
					success: function(valveDetails) {
							$("#contentsValve_"+ipID).html(valveDetails);
						}
					});
				
				$("#loadingImg_"+ipID).hide();
				alert("Valves configured Successfully!");
				
			}
		});
		
	}
	
	function showPumpDetails(ipID)
	{
		var pumpNumber	=	$("#pumpNumber_"+ipID).val();
		
		if(isNaN(pumpNumber))
		{
			$("#pumpNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid pump numbers!");
			return false;
		}
		else
		{
			$("#pumpNumber_"+ipID).css('border','');
		}
		
		/* else if(pumpNumber > 3)
		{
			$("#pumpNumber").css('border','1px solid #FF0000');
			alert("Please enter pump numbers less than or equal to 3!");
			return false;
		} */
		
		//$("#pumpTable").find("tbody > tr:gt(0)").hide();
		$('#pumpTable_'+ipID+' > tbody > tr:gt(0)').hide();
		
		for(j = 0; j < pumpNumber; j++)
		{
			//alert("#trPumpDetails"+j);
			$("#trPumpDetails"+j+"_"+ipID).show();
		}
		
		var rowCount = $('#pumpTable_'+ipID+' > tbody > tr').length;
		
		if(pumpNumber > 0)
		{
			$("#pumpSaveConf_"+ipID).show();
		}
		else
			$("#pumpSaveConf_"+ipID).hide();
	}
	
	function showHeaterDetails(ipID)
	{
		var heaterNumber	=	$("#heaterNumber_"+ipID).val();

		if(isNaN(heaterNumber))
		{
				$("#heaterNumber_"+ipID).css('border','1px solid #FF0000');
				alert("Please enter valid Heater numbers!");
				return false;
		}
		/* else if(heaterNumber > 3)
		{
				$("#heaterNumber").css('border','1px solid #FF0000');
				alert("Please enter Heater numbers less than or equal to 3!");
				return false;
		} */
		else
		{
				$("#heaterNumber_"+ipID).css('border','');
		}

		//$("#pumpTable").find("tbody > tr:gt(0)").hide();
		$('#heaterTable_'+ipID+' > tbody > tr:gt(0)').hide();

		for(j = 1; j <= heaterNumber; j++)
		{
				//alert("#trPumpDetails"+j);
				$("#heaterDetails_"+j+"_"+ipID).show();
		}

		var rowCount = $('#heaterTable_'+ipID+' > tbody > tr').length;

		if(heaterNumber > 0)
		{
			$("#heaterSaveConf_"+ipID).show();
		}
		else
			$("#heaterSaveConf_"+ipID).hide();
	}
	
	function showLightDetails(ipID)
	{
		var lightNumber	=	$("#lightNumber_"+ipID).val();

		if(isNaN(lightNumber))
		{
				$("#lightNumber_"+ipID).css('border','1px solid #FF0000');
				alert("Please enter valid Light numbers!");
				return false;
		}
		else
		{
				$("#lightNumber_"+ipID).css('border','');
		}

		//$("#pumpTable").find("tbody > tr:gt(0)").hide();
		$('#lightTable_'+ipID+' > tbody > tr:gt(0)').hide();

		for(j = 1; j <= lightNumber; j++)
		{
				//alert("#trPumpDetails"+j);
				$("#lightDetails_"+j+"_"+ipID).show();
		}

		var rowCount = $('#lightTable_'+ipID+' > tbody > tr').length;

		if(lightNumber > 0)
		{
			$("#lightSaveConf_"+ipID).show();
		}
		else
			$("#lightSaveConf_"+ipID).hide();
	}
	
	function showBlowerDetails()
	{
		var blowerNumber	=	$("#blowerNumber").val();
		
		if(isNaN(blowerNumber))
		{
			$("#blowerNumber").css('border','1px solid #FF0000');
			alert("Please enter valid Blower numbers!");
			return false;
		}
		else
		{
			$("#blowerNumber").css('border','');
		}
		
		//$("#pumpTable").find("tbody > tr:gt(0)").hide();
		$('#blowerTable > tbody > tr:gt(0)').hide();
		
		for(j = 1; j <= blowerNumber; j++)
		{
			//alert("#trPumpDetails"+j);
			$("#blowerDetails_"+j).show();
		}
		
		var rowCount = $('#blowerTable > tbody > tr').length;
		
		if(blowerNumber > 0)
		{
			$("#blowerSaveConf").show();
		}
		else
			$("#blowerSaveConf").hide();
	}
	
	function showMiscDetails(ipID)
	{
		var miscNumber	=	$("#miscNumber_"+ipID).val();
		
		if(isNaN(miscNumber))
		{
			$("#miscNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid Miscelleneous device numbers!");
			return false;
		}
		else
		{
			$("#miscNumber_"+ipID).css('border','');
		}
		
		//$("#pumpTable").find("tbody > tr:gt(0)").hide();
		$('#miscTable_'+ipID+' > tbody > tr:gt(0)').hide();
		
		for(j = 1; j <= miscNumber; j++)
		{
			//alert("#trPumpDetails"+j);
			$("#miscDetails_"+j+"_"+ipID).show();
		}
		
		var rowCount = $('#miscTable_'+ipID+' > tbody > tr').length;
		
		if(miscNumber > 0)
		{
			$("#miscSaveConf_"+ipID).show();
		}
		else
			$("#miscSaveConf_"+ipID).hide();
	}
	
	
	
	function checkAndSavePumps(ipID)
	{
		var pumpNumber	=	$("#pumpNumber_"+ipID).val();
		
		if(isNaN(pumpNumber))
		{
			$("#pumpNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid pump numbers!");
			return false;
		}
		else if(pumpNumber > 3)
		{
			$("#pumpNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter pump numbers less than or equal to 3!");
			return false;
		}
		else
		{
			$("#pumpNumber_"+ipID).css('border','');
		}
		$("#loadingImgPump_"+ipID).show();
		var arrPumpSaveDetails	=	{};
		var errMsg				=	"";
		for(j = 0; j < pumpNumber; j++)
		{
			var pumpNumberConf	=	$("#sPumpNumber_"+j+"_"+ipID).val();
			var pumpClosure		=	$("[name='sPumpClosure_"+j+"_"+ipID+"']:checked").val();
			var pumpType		=	$("#sPumpType_"+j+"_"+ipID).val();
			
			var relayNumber1	=	'';
			var relayNumber2	=	'';
			var pumpAddress		=	'';
			var pumpSpeed		=	'';
			var pumpFlow		=	'';
			var pumpSubType		=	'';
			var pumpSubType1	=	'';
			var pumpSpeedIn		=	'';
			
			if (pumpType.indexOf("12") >= 0 || pumpType.indexOf("24") >= 0 || pumpType == '2Speed')
				relayNumber1	=	$("#sRelayNumber_"+j+"_"+ipID).val();
			
			if(pumpType == '2Speed')
			{
				pumpSubType1	=	$("#sPumpSubType1_"+j+"_"+ipID).val();
				relayNumber2	=	$("#sRelayNumber1_"+j+"_"+ipID).val();
			}
			
			if(pumpType.indexOf("Intellicom") >= 0 || pumpType.indexOf("Emulator") >= 0)
			{
				pumpSubType		=	$("#sPumpSubType_"+j+"_"+ipID).val();
				pumpAddress		=	$("#sPumpAddress_"+j+"_"+ipID).val();
			}
			
			if(pumpSubType != '')
			{
				if(pumpSubType == 'VS')
				{
					if(pumpType.indexOf("Intellicom") >= 0)
						pumpSpeedIn	=	$("[name='sPumpSpeedIn_"+j+"_"+ipID+"']:checked").val();
					else
						pumpSpeed	=	$("[name='sPumpSpeed_"+j+"_"+ipID+"']:checked").val();
				}
				if(pumpSubType == 'VF')
					pumpFlow	=	$("#sPumpFlow_"+j+"_"+ipID).val();
			}
			
			if(typeof(pumpClosure)  === "undefined")
			{
				errMsg	+=	"- Please select Closure for Pump "+pumpNumberConf+"!\n";
			}
			if ((pumpType.indexOf("12") >= 0 || pumpType.indexOf("24") >= 0 || pumpType == '2Speed') && relayNumber1 == '')
			{
				errMsg	+=	"- Please enter Relay Number for Pump "+pumpNumberConf+"!\n";
			}
			if(pumpType == '2Speed' && relayNumber2 == '')
			{
				errMsg	+=	"- Please enter Relay Number2 for Pump "+pumpNumberConf+"!\n";
			}
			if((pumpType.indexOf("Intellicom") >= 0 || pumpType.indexOf("Emulator") >= 0) && pumpAddress == '')
			{
				if(pumpSubType == 'VS' && typeof(pumpSpeed)  === "undefined")
					errMsg	+=	"- Please select speed for Pump "+pumpNumberConf+"!\n";
				if(pumpSubType == 'VF' && pumpFlow == '')
					errMsg	+=	"- Please enter flow for Pump "+pumpNumberConf+"!\n";
				
				errMsg	+=	"- Please enter Address for Pump "+pumpNumberConf+"!\n";
			}
			
			arrPumpSaveDetails[pumpNumberConf]		=	[];
			arrPumpSaveDetails[pumpNumberConf] 		=	{'closure': pumpClosure, 'type' : pumpType, 'relayNumber1':relayNumber1,'relayNumber2':relayNumber2,'pumpAddress':pumpAddress,'pumpSubType':pumpSubType,'pumpSpeed':pumpSpeed,'pumpFlow':pumpFlow,'pumpSubType1':pumpSubType1,'pumpSpeedIn':pumpSpeedIn};

		}
		//console.log(JSON.stringify(arrPumpSaveDetails));
		if(errMsg != '')
		{
			alert("Following are the errors : \n\n"+errMsg);
			$("#loadingImgPump_"+ipID).hide();
			return false;
		}
		//console.log(arrPumpSaveDetails);
		
		$.ajax({
		type: "POST",
		url: "<?php echo site_url('home/savePumpRelayConf/');?>", 
		data: {pump:JSON.stringify(arrPumpSaveDetails),sDevice:'P',ipID:ipID},
		success: function(data) {
				var aData = data.split('|||');
				alert(aData[1]);
				var pumpNumberNew	= aData[0];
				if(aData[1].indexOf("successfully") >= 0)
				{
					for (var i = 1; i <= pumpNumberNew; i++) 
					{
						if(i == 1)
						{
							$("#automatic_pumps").html('<option value="'+(i - 1)+'">'+(i - 1)+' Pump</option><option value="'+i+'">'+i+'</option>');
							$("#PumpAssign"+i).html('<option value="">--Select Pump--</option><option value="'+(i-1)+'">Pump '+(i-1)+'</option>');
							
						}
						else
						{
							$("#automatic_pumps").append('<option value="'+i+'" selected="selected">'+i+'</option>');
							$("#PumpAssign"+i).append('<option value="'+(i-1)+'">Pump '+(i-1)+'</option>');
						}
						
						$("#trPump"+i+"_"+ipID).show();
						$("#trPumpShow"+i+"_"+ipID).show();
					}
					
					for (i = (parseInt(pumpNumberNew)+1); i <= 3; i++) 
					{
						$("#trPump"+i+"_"+ipID).hide();
						$("#trPumpShow"+i+"_"+ipID).hide();
					}
					
					
					$.ajax({
					type: "POST",
					async:false,
					url: "<?php echo site_url('home/getAssignPumpDetails/');?>", 
					data: {ipID:ipID},
					success: function(lightDetails) {
							$("#contentsPump_"+ipID).html(lightDetails);
						}
					});
				}
				$("#loadingImgPump_"+ipID).hide();
				parent.$.fancybox.close();
		}
		});
		
	}
	
	function checkAndSaveHeater(ipID)
	{
		var heaterNumber	=	$("#heaterNumber_"+ipID).val();
		
		if(isNaN(heaterNumber))
		{
			$("#heaterNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid heater numbers!");
			return false;
		}
		/* else if(heaterNumber > 3)
		{
			$("#heaterNumber").css('border','1px solid #FF0000');
			alert("Please enter heater numbers less than or equal to 3!");
			return false;
		} */
		else
		{
			$("#heaterNumber_"+ipID).css('border','');
		}
		
		$("#loadingImgHeater_"+ipID).show();
		
		var arrHeaterSaveDetails	=	{};
		var errMsg					=	"";
		for(j = 1; j <= heaterNumber; j++)
		{
			var relayType 	=	$("#heater"+j+"_"+ipID+"_equiment").val();
			var relayNumber =	$("#heater"+j+"_"+ipID+"_sub_equiment_"+relayType).val();
			var heaterName	=	$("#heaterName"+j+"_"+ipID).val();
			
			if(relayNumber == '')
			{
				errMsg	+=	"- Please select Relay Number for Heater "+j+"!\n";
			}
			
			arrHeaterSaveDetails[j]		=	[];
			arrHeaterSaveDetails[j] 	=	{'relayType': relayType, 'relayNumber' : relayNumber, 'name' : heaterName} 
		}
		
		if(errMsg != '')
		{
			alert("Following are the errors : \n\n"+errMsg);
			$("#loadingImgHeater_"+ipID).hide();
			return false;
		}
		
		//Check if entered address already exists.
		$.ajax({
		type: "POST",
		url: "<?php echo site_url('home/saveHeaterRelayConf/');?>", 
		data: {heater:JSON.stringify(arrHeaterSaveDetails),sDevice:'H',ipID:ipID},
		success: function(data) {
					var aData = data.split('|||');
					alert(aData[1]);
					var heaterNumberNew	=aData[0];	
					
					console.log(aData);
					
					if(aData[1].indexOf("successfully") >= 0)
					{
						for (var i = 1; i <= heaterNumberNew; i++) 
						{
							if(i == 1)
							{
								$("#automatic_heaters_question1").html('<option value="'+(i - 1)+'">'+(i - 1)+'</option><option value="'+i+'">'+i+'</option>');
							}
							else
							{
								$("#automatic_heaters_question1").append('<option value="'+i+'">'+i+'</option>');
							}
							
							$("#trHeater"+i).show();
							$("#trHeaterWork"+i).show();
							$("#trHeaterPump"+i).show();
						}
						for (i = (parseInt(heaterNumberNew)+1); i <= 3; i++) 
						{
							$("#trHeater"+i).hide();
							$("#trHeaterWork"+i).hide();
							$("#trHeaterPump"+i).hide();
							
							$('[id^="trHeaterSub'+i+'_"]').hide();
						}
						
						var pumpNumber	=	$("#pumpNumber_"+ipID).val();
						
						for (i = 1; i <= pumpNumber; i++) 
						{
							for(j = 1; j <= pumpNumber; j++)
							{
								if(j == 1)
									$("#HeaterPump"+i).html('<option value="'+j+'">Pump '+j+'</option>');
								else 
									$("#HeaterPump"+i).append('<option value="'+j+'">Pump '+j+'</option>');	
							}
						}
						
						$.ajax({
						type: "POST",
						async:false,
						url: "<?php echo site_url('home/getAssignHeaterDetails/');?>", 
						data: {ipID:ipID},
						success: function(lightDetails) {
								$("#contentsHeater_"+ipID).html(lightDetails);
							}
						});
						
					}
					$("#loadingImgHeater_"+ipID).hide();
					parent.$.fancybox.close();
		}
		});
		
	}
	
	
	function checkAndSaveLight(ipID)
	{
		var lightNumber	=	$("#lightNumber_"+ipID).val();
		
		if(isNaN(lightNumber))
		{
			$("#lightNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid light numbers!");
			return false;
		}
		else
		{
			$("#lightNumber_"+ipID).css('border','');
		}
		
		$("#loadingImgLight_"+ipID).show();
		
		var arrLightSaveDetails	=	{};
		var errMsg					=	"";
		for(j = 1; j <= lightNumber; j++)
		{
			var relayType 	=	$("#light"+j+"_"+ipID+"_equiment").val();
			var relayNumber =	$("#light"+j+"_"+ipID+"_sub_equiment_"+relayType).val();
			var lightName	=	$("#lightName"+j+"_"+ipID).val();
			
			if(relayNumber == '')
			{
				errMsg	+=	"- Please select Relay Number for Light "+j+"!\n";
			}
			
			arrLightSaveDetails[j]		=	[];
			arrLightSaveDetails[j] 	=	{'relayType': relayType, 'relayNumber' : relayNumber, 'name' : lightName} 
		}
		
		if(errMsg != '')
		{
			alert("Following are the errors : \n\n"+errMsg);
			$("#loadingImgLight_"+ipID).hide();
			return false;
		}
		
		//Check if entered address already exists.
		$.ajax({
		type: "POST",
		url: "<?php echo site_url('home/saveLightRelayConf/');?>", 
		data: {light:JSON.stringify(arrLightSaveDetails),sDevice:'L',ipID:ipID},
		success: function(data) {
					var aData = data.split('|||');
					var lightNumberNew	=	aData[0];
					if(aData[1].indexOf("successfully") >= 0)
					{
						for (var i = 1; i <= lightNumberNew; i++) 
						{
							if(i == 1)
							{
								$("#no_light").html('<option value="'+(i - 1)+'">'+(i - 1)+'</option><option value="'+i+'">'+i+'</option>');
							}
							else
							{
								$("#no_light").append('<option value="'+i+'">'+i+'</option>');
							}
						}
						
						$("#loadingImgLight_"+ipID).hide();
					
						$.ajax({
						type: "POST",
						url: "<?php echo site_url('home/getAssignLightsDetails/');?>", 
						data: {ipID:ipID},
						success: function(lightDetails) {
								$("#contentsLight_"+ipID).html(lightDetails);
							}
						});
						$("#LightForm").toggleClass('disableConfig');
					}
					
					alert(aData[1]);
		}
		});
	}
	
	
	function checkAndSaveBlower(ipID)
	{
		var blowerNumber	=	$("#blowerNumber_"+ipID).val();
		
		if(isNaN(blowerNumber))
		{
			$("#blowerNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid Blower numbers!");
			return false;
		}
		else
		{
			$("#blowerNumber_"+ipID).css('border','');
		}
		
		$("#loadingImgBlower_"+ipID).show();
		
		var arrBlowerSaveDetails	=	{};
		var errMsg					=	"";
		for(j = 1; j <= blowerNumber; j++)
		{
			var relayType 	=	$("#blower"+j+"_"+ipID+"_equiment").val();
			var relayNumber =	$("#blower"+j+"_"+ipID+"_sub_equiment_"+relayType).val();
			var blowerName	=	$("#blowerName"+j+"_"+ipID).val();
			
			if(relayNumber == '')
			{
				errMsg	+=	"- Please select Relay Number for Blower "+j+"!\n";
			}
			
			arrBlowerSaveDetails[j]		=	[];
			arrBlowerSaveDetails[j] 	=	{'relayType': relayType, 'relayNumber' : relayNumber,'name' : blowerName} 
		}
		
		if(errMsg != '')
		{
			alert("Following are the errors : \n\n"+errMsg);
			$("#loadingImgBlower_"+ipID).hide();
			return false;
		}
		
		//Check if entered address already exists.
		$.ajax({
		type: "POST",
		url: "<?php echo site_url('home/saveBlowerRelayConf/');?>", 
		data: {blower:JSON.stringify(arrBlowerSaveDetails),sDevice:'B',ipID:ipID},
		success: function(data) {
					var aData = data.split("|||");
					var blowerNumberNew = aData[0];
					if(aData[1].indexOf("successfully") >= 0)
					{
						for (var i = 1; i <= blowerNumberNew; i++) 
						{
							if(i == 1)
							{
								$("#no_blower").html('<option value="'+(i - 1)+'">'+(i - 1)+'</option><option value="'+i+'">'+i+'</option>');
							}
							else
							{
								$("#no_blower").append('<option value="'+i+'">'+i+'</option>');
							}
						}
						$("#loadingImgBlower_"+ipID).hide();
						//parent.$.fancybox.close();
						$("#BlowerForm").toggleClass('disableConfig');
						
						$.ajax({
						type: "POST",
						url: "<?php echo site_url('home/getAssignBlowerDetails/');?>", 
						data: {ipID:ipID},
						success: function(blowerDetails) {
								$("#contentsBlower_"+ipID).html(blowerDetails);
							}
						});
					}
					alert(aData[1]);
					
		}
		});
	}
	
	
	function checkAndSaveMisc(ipID)
	{
		var miscNumber	=	$("#miscNumber_"+ipID).val();
		
		if(isNaN(miscNumber))
		{
			$("#miscNumber_"+ipID).css('border','1px solid #FF0000');
			alert("Please enter valid Miscelleneous Device numbers!");
			return false;
		}
		else
		{
			$("#miscNumber_"+ipID).css('border','');
		}
		
		$("#loadingImgMisc_"+ipID).show();
		
		var arrMiscSaveDetails	=	{};
		var errMsg					=	"";
		for(j = 1; j <= miscNumber; j++)
		{
			var relayType 	=	$("#misc"+j+"_"+ipID+"_equiment").val();
			var relayNumber =	$("#misc"+j+"_"+ipID+"_sub_equiment_"+relayType).val();
			var miscName	=	$("#miscName"+j+"_"+ipID).val();
			
			if(relayNumber == '')
			{
				errMsg	+=	"- Please select Relay Number for Miscelleneous Device "+j+"!\n";
			}
			
			arrMiscSaveDetails[j]		=	[];
			arrMiscSaveDetails[j] 	=	{'relayType': relayType, 'relayNumber' : relayNumber,'name' : miscName} 
		}
		
		if(errMsg != '')
		{
			alert("Following are the errors : \n\n"+errMsg);
			$("#loadingImgMisc_"+ipID).hide();
			return false;
		}
		
		//Check if entered address already exists.
		$.ajax({
		type: "POST",
		url: "<?php echo site_url('home/saveMiscRelayConf/');?>", 
		data: {misc:JSON.stringify(arrMiscSaveDetails),sDevice:'M',ipID:ipID},
		success: function(data) {
					var aData =	data.split('|||');
					var miscNumberNew = aData[0];
					if(aData[1].indexOf("successfully") >= 0)
					{
						for (var i = 1; i <= miscNumberNew; i++) 
						{
							if(i == 1)
							{
								$("#no_misc").html('<option value="'+(i - 1)+'">'+(i - 1)+'</option><option value="'+i+'">'+i+'</option>');
							}
							else
							{
								$("#no_misc").append('<option value="'+i+'">'+i+'</option>');
							}
						}
						$("#loadingImgMisc_"+ipID).hide();
						//parent.$.fancybox.close();
						$("#MiscForm").toggleClass('disableConfig');
						
						$.ajax({
						type: "POST",
						url: "<?php echo site_url('home/getAssignMiscDetails/');?>", 
						data: {ipID:ipID},
						success: function(miscDetails) {
								$("#contentsMisc_"+ipID).html(miscDetails);
							}
						});
					}
					alert(aData[1]);
					
		}
		});
	}
	
	
</script>

<div class="row">
	<div class="col-lg-12">
		<ol class="breadcrumb">
		  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
		  <li class="active">Pool & Spa Setting</li>
		</ol>
		
		<?php if($saveMsg != '') { ?>
		  <div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $saveMsg;?>
		  </div>
		<?php } ?>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div><span style="color:#FFF;"><strong>Edit Pool & Spa Automation Question To Set Up Their Equipment Properly<strong></span><span style="color:#FF0000; float:right;"><strong>* indicates required field</strong></span></div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">&nbsp;</div>
</div>

<!-- Tabs -->
<div class="row">
	<div class="col-lg-12">
		<script type="text/javascript">
		$(function ()
		 {
			var form = $("#formPoolSpa");
			 form.validate({
				errorPlacement: function errorPlacement(error, element) { element.before(error); },
				rules: {
					confirm: {
						equalTo: "#password"
					}
				}
			}); 
			form.children("div").steps({
				headerTag: "h3",
				bodyTag: "section",
				transitionEffect: "slideLeft",
				stepsOrientation: "vertical",
				enableAllSteps: true,
				onInit:function (event, currentIndex)
				{
					$('ul[role="tablist"] > li > a').css('color','#fff');
				},
				onStepChanging: function (event, currentIndex, newIndex)
				{
					form.validate().settings.ignore = ":disabled,:hidden";
					$('html, body').animate({
						scrollTop: $(".breadcrumb").offset().top
					}, 1000);
					
					<?php if($sAccess == '2') { ?>
					var chkForm = form.valid();
					
					if(chkForm)
					{
						//Save the details when step changed.
						//0 = Home,1=Temperarure,2=Valve,3 =Pump,4=Heater,5=Light,6=blower,7=Misc
						
						if(currentIndex == 0)
						{
							var type			=	$("#strType").val();
							var pool_max_temp	=	$("#pool_maximum_temperature").val();
							var pool_temp		=	$("#pool_temperature").val();
							var pool_manual		=	$("#pool_manual").val();
							var spa_max_temp	=	$("#spa_maximum_temperature").val();
							var spa_temp		=	$("#spa_temperature").val();
							var spa_manual		=	$("#spa_manual").val();
							
							var arrDetails		= 	{'type':type,'pool_max_temp':pool_max_temp,'pool_temp':pool_temp,'pool_manual':pool_manual,'spa_max_temp':spa_max_temp,'spa_temp':spa_temp,'spa_manual':spa_manual}
							
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
						else if(currentIndex == 1)
						{
							var temperature1 		= $("#temperature1").val();
							var temperature2 		= $("#temperature2").val();
							var display_pool_temp	= $("#display_pool_temp").val();
							var display_spa_temp	= $("#display_spa_temp").val();
							
							var arrDetails		= 	{'temperature1':temperature1,'temperature2':temperature2,'display_pool_temp':display_pool_temp,'display_spa_temp':display_spa_temp}
							
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
						else if(currentIndex == 2)
						{
							var valve			=	$("#strValve").val();
							var valve_actuated	=	$("#valve_actuated").val();
							var valveRunTime	=	$("#valveRunTime").val();
							var reasonValve		=	'';
							if(valve_actuated[0] == '0')
								reasonValve		=	$("#reasonValve").val();
							var valveAssign		=	[];
							$('.valveAssign').each(function(){
								var value	=	$(this).val();
								if($("#lableRelayValve-"+value).hasClass('checked'))
								{
									valveAssign.push(value);
								}
							});
							
							var arrDetails		= 	{'valve':valve,'valve_actuated':valve_actuated,'valveRunTime':valveRunTime,'reasonValve':reasonValve,'valveAssign':valveAssign}
							
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
						else if(currentIndex == 3)
						{
							var pump 		=	$("#automatic_pumps").val();
							var PumpService	=	[];
							for(var i=1; i<=pump; i++)
							{
								PumpService[i] = $("#Pump"+i).val();
							}
							var pumpAssign		=	[];
							$('.pumpAssign').each(function(){
								var value	=	$(this).val();
								if($("#lableRelayPump-"+value).hasClass('checked'))
								{
									pumpAssign.push(value);
								}
							});
							
							var arrDetails		= 	{'pump':pump,'PumpService':PumpService,'pumpAssign':pumpAssign}
												
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
						else if(currentIndex == 4)
						{
							var heater = $("#automatic_heaters_question1").val();
							var heaterService = [];
							var heaterPump 	  = [];
							
							for(var i=1; i<=heater; i++)
							{
								heaterService[i] = $("#Heater"+i).val();
								heaterPump[i]	 = $("#HeaterPump"+i).val();
							}
							
							var heaterAssign		=	[];
							$('.heaterAssign').each(function(){
								var value	=	$(this).val();
								if($("#lableRelayHeater-"+value).hasClass('checked'))
								{
									heaterAssign.push(value);
								}
							});
							
							var arrDetails		= 	{'heater':heater,'heaterService':heaterService,'heaterPump':heaterPump,'heaterAssign':heaterAssign}
												
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
						else if(currentIndex == 5)
						{
							var light	= $("#no_light").val();
							
							var lightAssign		=	[];
							$('.lightAssign').each(function(){
								var value	=	$(this).val();
								if($("#lableRelayLight-"+value).hasClass('checked'))
								{
									lightAssign.push(value);
								}
							});
							
							var arrDetails		= 	{'light':light,'lightAssign':lightAssign}
												
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
						else if(currentIndex == 6)
						{
							var blower	= $("#no_blower").val();
							
							var blowerAssign		=	[];
							$('.blowerAssign').each(function(){
								var value	=	$(this).val();
								if($("#lableRelayBlower-"+value).hasClass('checked'))
								{
									blowerAssign.push(value);
								}
							});
							
							var arrDetails		= 	{'blower':blower,'blowerAssign':blowerAssign}
												
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
						else if(currentIndex == 7)
						{
							var misc	= $("#no_misc").val();
							
							var miscAssign		=	[];
							$('.miscAssign').each(function(){
								var value	=	$(this).val();
								if($("#lableRelayMisc-"+value).hasClass('checked'))
								{
									miscAssign.push(value);
								}
							});
							
							var arrDetails		= 	{'misc':misc,'miscAssign':miscAssign}
												
							$.ajax({type:'POST',url:'<?php echo site_url('analog/saveAdvanceSettingTabDetails/');?>',async:false,
							data:{tabID:currentIndex,details:JSON.stringify(arrDetails)},
							success:function(result){}
							});
						}
					}
					return chkForm;
					<?php } else { ?>
					return true;
					<?php } ?>
				},
				onFinishing: function (event, currentIndex)
				{
					//form.validate().settings.ignore = ":disabled";
					//return form.valid();
					return true;
				},
				onFinished: function (event, currentIndex)
				{
					//alert("Submitted!");
					//document.formPoolSpa.submit();
					<?php if($sAccess == '2') { ?>
						$("#formPoolSpa").submit();
					<?php } ?>
				}
			});
			
			//wizard.steps("setStep", 2);
			//enableAllSteps: true,
		});
		</script>  

		<form id="formPoolSpa" name="formPoolSpa" action="<?php echo base_url('home/PoolSpaSetting/');?>" method="post">
		<input type="hidden" name="command" id="command" value="save">
			<div>
				<?php $this->load->view('homeTab',$aParameter); ?>
				<?php $this->load->view('tempratureTab',$aParameter); ?>
				<?php $this->load->view('valveTab',$aParameter); ?>
				<?php $this->load->view('pumpTab',$aParameter); ?>
				<?php $this->load->view('heaterTab',$aParameter); ?>
				<?php $this->load->view('lightTab',$aParameter); ?>
				<?php $this->load->view('blowerTab',$aParameter); ?>
				<?php $this->load->view('miscTab',$aParameter); ?>
			</div>
		</form>
	</div>
</div>
<!-- Tabs -->

<link rel="stylesheet" href="<?php echo HTTP_ASSETS_PATH.'/tipso-master/tipso.css';?>">
<script src="<?php echo HTTP_ASSETS_PATH.'/tipso-master/tipso.js';?>"></script>

<script type="text/javascript">
	
	$(document).ready(function(){
		$('.top-right').tipso({
				position: 'top-right',
				background: '#000000',
				useTitle: false
			});
	});
	
	//$("#sPumpType").change(function(){
	function showDetailsPump(sSelectedVal,i,ipID)
	{	
		//var sSelectedVal	=	$(this).val();
		if(sSelectedVal == 'Emulator' || sSelectedVal == 'Emulator12' || sSelectedVal == 'Emulator24')
		{
			$("#pumpSubTypeTr_"+i+"_"+ipID).show();
			$("#pumpSubTypeTrBlk_"+i+"_"+ipID).show();
			
			$("input:radio[name='sPumpSpeed_"+i+"_"+ipID+"']").attr('required','required');
		    $("#sPumpFlow_"+i+"_"+ipID).removeAttr('required');
		    $("#trVF_"+i+"_"+ipID).hide();
		    $("#trVFSpace_"+i+"_"+ipID).hide();
		  
		    $("#trVS_"+i+"_"+ipID).show(); 
		    $("#trVSSpace_"+i+"_"+ipID).show();
		}
		else
		{
			$("#pumpSubTypeTr_"+i+"_"+ipID).hide();
			$("#pumpSubTypeTrBlk_"+i+"_"+ipID).hide();
			
			$("#trVS_"+i+"_"+ipID).hide(); 
		    $("#trVSSpace_"+i+"_"+ipID).hide();
			$("input:radio[name='sPumpSpeed_"+i+"_"+ipID+"']").removeAttr('required');
			$("#sPumpFlow_"+i+"_"+ipID).removeAttr('required');
			$("#trVF_"+i+"_"+ipID).hide();
		    $("#trVFSpace_"+i+"_"+ipID).hide();
		}
		
		if(sSelectedVal == 'Emulator' || sSelectedVal == 'Intellicom')
		{
			$("#trRelayNumberSpace_"+i+"_"+ipID).hide();
			$("#trRelayNumber_"+i+"_"+ipID).hide();
			$("#sRelayNumber_"+i+"_"+ipID).removeAttr('required');
		}
		else
		{
			$("#trRelayNumberSpace_"+i+"_"+ipID).show();
			$("#trRelayNumber_"+i+"_"+ipID).show();
			$("#sRelayNumber_"+i+"_"+ipID).attr('required','required');
		}
		
		if(sSelectedVal == '12' || sSelectedVal == '24' || sSelectedVal == '2Speed')
		{
			$("#trAddressNumberSpace_"+i+"_"+ipID).hide();
			$("#trAddressNumber_"+i+"_"+ipID).hide();
			$("#sPumpAddress_"+i+"_"+ipID).removeAttr('required');
		}
		else
		{
			$("#trAddressNumberSpace_"+i+"_"+ipID).show();
			$("#trAddressNumber_"+i+"_"+ipID).show();
			$("#sPumpAddress_"+i+"_"+ipID).attr('required','required');
		}
		
		if(sSelectedVal == 'Intellicom' || sSelectedVal == 'Intellicom12' || sSelectedVal == 'Intellicom24')
		{
			$("input:radio[name='sPumpSpeedIn_"+i+"_"+ipID+"']").attr('required','required');
		    
		    $("#trVSIntellicom_"+i+"_"+ipID).show(); 
		    $("#trVSSpaceIntellicom_"+i+"_"+ipID).show();
		}
		else
		{
			$("input:radio[name='sPumpSpeedIn_"+i+"_"+ipID+"']").removeAttr('required');
			$("#trVSIntellicom_"+i+"_"+ipID).hide();
		    $("#trVSSpaceIntellicom_"+i+"_"+ipID).hide();
		}
		
		if(sSelectedVal == '2Speed')
		{
			$("input:radio[name='sRelayNumber1_"+i+"_"+ipID+"']").attr('required','required');
		    
		    $("#trRelayNumber1Space_"+i+"_"+ipID).show(); 
		    $("#trRelayNumber1_"+i+"_"+ipID).show();
			$("#pumpSubType2SpeedTrBlk_"+i+"_"+ipID).show(); 
		    $("#pumpSubType2SpeedTr_"+i+"_"+ipID).show();
			$("#trAddressNumberSpace_"+i+"_"+ipID).hide();
			$("#trAddressNumber_"+i+"_"+ipID).hide();
			$("input:radio[name='sPumpAddress_"+i+"_"+ipID+"']").removeAttr('required');
		}
		else
		{
			$("input:radio[name='sRelayNumber1_"+i+"_"+ipID+"']").removeAttr('required');
		    
		    $("#trRelayNumber1Space_"+i+"_"+ipID).hide(); 
		    $("#trRelayNumber1_"+i+"_"+ipID).hide();
			$("#pumpSubType2SpeedTrBlk_"+i+"_"+ipID).hide(); 
		    $("#pumpSubType2SpeedTr_"+i+"_"+ipID).hide();
		}
	}
	function subTypeDetails(sSelectedVal,i,ipID)
	{
		//$("#sPumpSubType").change(function() {
		
    //var sSelectedVal	=	$(this).val();
    if(sSelectedVal == 'VF' && ($("#sPumpType_"+i+"_"+ipID).val() == 'Emulator' || $("#sPumpType_"+i+"_"+ipID).val() == 'Emulator12' || $("#sPumpType_"+i+"_"+ipID).val() == 'Emulator24'))
    {
	  $("#sPumpFlow_"+i+"_"+ipID).attr('required','required');
      $("input:radio[name='sPumpSpeed_"+i+"_"+ipID+"']").removeAttr('required');
	  $("input:radio[name='sPumpClosure_"+i+"_"+ipID+"']").removeAttr('required');

      $("#trVF_"+i+"_"+ipID).show();
      $("#trVFSpace_"+i+"_"+ipID).show();
      
      $("#trVS_"+i+"_"+ipID).hide();
      $("#trVSSpace_"+i+"_"+ipID).hide();
	  
    }
    else if(sSelectedVal == 'VS' && ($("#sPumpType_"+i+"_"+ipID).val() == 'Emulator' || $("#sPumpType_"+i+"_"+ipID).val() == 'Emulator12' || $("#sPumpType_"+i+"_"+ipID).val() == 'Emulator24'))
    {
      $("input:radio[name='sPumpSpeed_"+i+"_"+ipID+"']").attr('required','required');
	  $("input:radio[name='sPumpClosure_"+i+"_"+ipID+"']").attr('required','required');
      $("#sPumpFlow_"+i+"_"+ipID).removeAttr('required');
      $("#trVF_"+i+"_"+ipID).hide();
      $("#trVFSpace_"+i+"_"+ipID).hide();
      
      $("#trVS_"+i+"_"+ipID).show(); 
      $("#trVSSpace_"+i+"_"+ipID).show();
	  
    }
  }
  function showBoardDetails(board,id)
	{
		
		if(board == '')
		{
			alert("Please select IP first!");
			return false;
		}
		$("[id^='"+id+"']").hide();
		$("#"+id+""+board).show();
		
	}
</script>
	
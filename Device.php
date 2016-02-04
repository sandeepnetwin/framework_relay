<?php

	/**
    * @Programmer: Dhiraj S.
    * @Created: 13 July 2015
    * @Modified: 
    * @Description: Home Controller for dashboard and device details.
    **/

	$this->load->model('home_model');
	$this->load->model('analog_model');

	$sAccess 		= '';
	$sModule	    = '';
	$sDeviceFullName = '';
	if($sDevice == 'R')
	{
	  $sDeviceFullName 	= '24V AC Relay';
	  $sModule	    	= 2;
	}
	if($sDevice == 'P')
	{
	  $sDeviceFullName = '12V DC Power Center Relay';
	  $sModule	       = 3;
	}
	if($sDevice == 'V')
	{
	  $sDeviceFullName = 'Valve';
	  $sModule	       = 8;
	}
	if($sDevice == 'PS')
	{
	  $sDeviceFullName = 'Pump Sequencer';
	  $sModule	       = 9;
	}
	if($sDevice == 'T')
	{
	  $sDeviceFullName = 'Temperature sensor';
	  $sModule	       = 10;
	}

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
	
	$iTotalIP	=	count($aIPDetails);
	  
	$sIPOptions	=	'';
	$iFirstIPId	=	'';	
	
	if($BackToIP != '')
		$iFirstIPId	= $BackToIP;
	
	if(!empty($aIPDetails))
	{
		foreach($aIPDetails as $aIP)
		{
			//First IP ID to show selected.
			if($iFirstIPId == '')
				$iFirstIPId = $aIP->id;
			
			$sDetails	=	$aIP->ip;
			if($aIP->name != '')
			{
				$sDetails .= ' ('.$aIP->name.')';
			}
			
			$sShow		=	'display:none';
			$sSelected	=	'';
			if($iFirstIPId == $aIP->id)
			{ 
				$sShow		=	'';
				$sSelected	=	'selected="selected"';
			} 
			
			$sIPOptions.='<option value="'.$aIP->id.'" '.$sSelected.'>'.$sDetails.'</option>';
		}
	}
		
	//Parameter for the Devices
	$aParam	=	array('sDevice'=>$sDevice,'sAccess'=>$sAccess,'extra'=>$extra,'iActiveMode'=>$iActiveMode,'aIPDetails'=>$aIPDetails,'iTotalIP'=>$iTotalIP,'iFirstIPId'=>$iFirstIPId);
	  
	?>
	<style>
	.fancybox-inner 
	{
		height:40px !important;
	}
	@media (max-width:835px)
	{
		.customType
		{
			padding-top: 10px;
			position: absolute;
		}
	}
	</style>
	<script type="text/javascript" src="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.js?v=2.1.5';?>"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ASSETS_PATH.'fancybox/source/jquery.fancybox.css?v=2.1.5';?>" media="screen" />
	<?php if($sDevice != 'V') { ?>
	<script type="text/javascript">
	var $a = $.noConflict();
	$a(document).ready(function() {
		$a('.fancybox').fancybox({'closeBtn' : false,'helpers': {'overlay' : {'closeClick': false}}
		});
	});
	</script>
	<?php } ?>	
	<link href="<?php echo HTTP_ASSETS_PATH.'progressbar/css/static.css';?>" rel="stylesheet"/>
	<script src="<?php echo HTTP_ASSETS_PATH.'progressbar/js/static.min.js';?>"></script>
	<script src="<?php echo HTTP_ASSETS_PATH.'progressbar/dist/js/jquery.progresstimer.js';?>"></script>
	<script>
	var Base64 = {


    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",


    encode: function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },


    decode: function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    _utf8_encode: function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    _utf8_decode: function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}
	jQuery(document).ready(function($) {
		
		$(".displayMore").hide();
		$(".more").click(function() {
			var txt	=	$(this).html();
			if(txt == 'More +')
				txt = 'More -';
			else
				txt = 'More +';
			
			$(".displayMore").toggle('slow',function() {	
				$(".more").html(txt);
			});
			
		});
		
		setInterval( function() {
			var sDevice	= '<?php echo $sDevice;?>';
			var IpId	= $("#IpId").val();	
			$.ajax({
					type: "POST",
					url: "<?php echo site_url('home/getStatus/');?>", 
					data: {sDevice:sDevice,IpId:IpId},
					success: function(data) {
						var deviceStatus = jQuery.parseJSON(data);
						var lableText   = '';
						
						if(sDevice == 'R')
						{
							lableText = 'lableRelay-';
						}
						else if(sDevice == 'P')
						{
							lableText = 'lablePower-';
						}
						
						$.each( deviceStatus, function( iDevice, sStatus ) 
						{
							if(sDevice != 'V' && sDevice != 'PS')
							{
								if(sStatus >= '1')
								{
									if(!$("#"+lableText+iDevice+"-"+IpId).hasClass('checked'))
									{
										$("#"+lableText+iDevice+"-"+IpId).addClass('checked');
									}
								}
								else if(sStatus == '0')
								{
									if($("#"+lableText+iDevice+"-"+IpId).hasClass('checked'))
									{
										$("#"+lableText+iDevice+"-"+IpId).removeClass('checked');
									}
								}
							}
							else if(sDevice == 'V')
							{
								var relayNumber = iDevice;
								var sPort	=	$("#selPort_V_"+relayNumber).val();
								if(sPort != '')
								{
									$('#switch-me-'+iDevice).val(sStatus).change();
								}
								
							}
						});
						
					}
			});
		},10000);
		
		
		<?php if($sAccess == 2) { ?>
		$(".addMoreValve").click(function() {
			var sIdIP	=	$("#IpId").val();
			$("#moreValve"+"-"+sIdIP).slideToggle('slow');
		});
		
		$(".relayButton").click(function()
		{
			//$a('.fancybox').fancybox();
			<?php if($iActiveMode == '2') { ?>
			var relayNumber = $(this).val();
			var sIdIP	=	$("#IpId").val();
			/* if(sIdIP == '')
			{
				alert("Please first select Board for Relay "+relayNumber+" !");
				$("#selPort_R_"+relayNumber).css('border','2px red solid');
				$('html, body').animate({
							scrollTop: $("#selPort_R_"+relayNumber).parent().offset().top
						}, 1000);
				return false;
			} */
			
			$(".loading-progress").show();
			var progress = $(".loading-progress").progressTimer({
				timeLimit: 10,
				onFinish: function () {
				  //$(".loading-progress").hide();
				  parent.$a.fancybox.close();
				}
			});
			
			$a("#checkLink").trigger('click');
			
			
			//$a('.fancybox-inner').height(450);
			//$a.fancybox.reposition();
			
			var status		= '';
			if($("#lableRelay-"+relayNumber+'-'+sIdIP).hasClass('checked'))
			{	
				status	=	0;
			}
			else
			{
				status = 1;
			}
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
				data: {sName:relayNumber,sStatus:status,sDevice:'R',sIdIP:sIdIP},
				success: function(data) {
					if($("#lableRelay-"+relayNumber+'-'+sIdIP).hasClass('checked'))
					{	
						$("#lableRelay-"+relayNumber+'-'+sIdIP).removeClass('checked');
					}
					else
					{
						$("#lableRelay-"+relayNumber+'-'+sIdIP).addClass('checked');
					}
					
				}
			}).error(function(){
			progress.progressTimer('error', {
				errorText:'ERROR!',
				onFinish:function(){
					alert('There was an error processing your information!');
				}
			});
			}).done(function(){
				progress.progressTimer('complete');
			});
			 <?php } else {  ?>
			  alert('You can perform this operation in manual mode only.');
			 <?php } ?> 
		});
		
		
		$(".relayRadio").click(function()
		{
			var chkVal 		= $(this).val();
			var relayNumber	= $(this).attr('name').split("_");

			var sIdIP	=	$("#IpId").val();	
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/saveDeviceMainType');?>", 
				data: {sDeviceID:relayNumber[0],sDevice:'R',sType:chkVal,sIdIP:sIdIP},
				success: function(data) {
					if(chkVal == 0)
					{
						$("#relay_other_"+relayNumber[0]+"-"+sIdIP).addClass('checked');
						$("#relay_spa_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 1)
					{
						$("#relay_other_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"-"+sIdIP).addClass('checked');
						$("#relay_pool_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 2)
					{
						$("#relay_other_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"-"+sIdIP).addClass('checked');
					}
					
				}

			 });
		});
		
		
		$(".powerButton").click(function()
		{
			<?php if($iActiveMode == '2') { ?>
			
			var relayNumber = $(this).val();
			var sIdIP	=	$("#IpId").val();
			/* if(sIdIP == '')
			{
				alert("Please first select Board for Power Center "+relayNumber+" !");
				$("#selPort_P_"+relayNumber).css('border','2px red solid');
				$('html, body').animate({
							scrollTop: $("#selPort_P_"+relayNumber).parent().offset().top
						}, 1000);
				return false;
			} */
			
			$(".loading-progress").show();
			var progress = $(".loading-progress").progressTimer({
				timeLimit: 10,
				onFinish: function () {
				  //$(".loading-progress").hide();
				  parent.$a.fancybox.close();
				}
			});
			
			$a("#checkLink").trigger('click');
			
			var status		= '';
			if($("#lablePower-"+relayNumber+"-"+sIdIP).hasClass('checked'))
			{	
				status	=	0;
			}
			else
			{
				status = 1;
			}
			
			
			 $.ajax({
				type: "POST",
				url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
				data: {sName:relayNumber,sStatus:status,sDevice:'P',sIdIP:sIdIP},
				success: function(data) {
					if($("#lablePower-"+relayNumber+"-"+sIdIP).hasClass('checked'))
					{	
						$("#lablePower-"+relayNumber+"-"+sIdIP).removeClass('checked');
					}
					else
					{
						$("#lablePower-"+relayNumber+"-"+sIdIP).addClass('checked');
					}
					
				}
			}).error(function(){
			progress.progressTimer('error', {
				errorText:'ERROR!',
				onFinish:function(){
					alert('There was an error processing your information!');
				}
			});
			}).done(function(){
				progress.progressTimer('complete');
			});
			 <?php } else {  ?>
			  alert('You can perform this operation in manual mode only.');
			 <?php } ?> 
		});
		
		
		$(".powerRadio").click(function()
		{
			var chkVal 		= $(this).val();
			var relayNumber	= $(this).attr('name').split("_");	
			
			var sIdIP	=	$("#IpId").val();
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/saveDeviceMainType');?>", 
				data: {sDeviceID:relayNumber[0],sDevice:'P',sType:chkVal,sIdIP:sIdIP},
				success: function(data) {
					if(chkVal == 0)
					{
						$("#relay_other_"+relayNumber[0]+"-"+sIdIP).addClass('checked');
						$("#relay_spa_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 1)
					{
						$("#relay_other_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"-"+sIdIP).addClass('checked');
						$("#relay_pool_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 2)
					{
						$("#relay_other_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"-"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"-"+sIdIP).addClass('checked');
					}
					
				}

			 });
		});
		
		$(".valveRadio").click(function()
		{
			var chkVal 		= $(this).val();
			var relayNumber	= $(this).attr('name').split("_");	
			
			var sIdIP	=	$("#IpId").val();
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/saveDeviceMainType');?>", 
				data: {sDeviceID:relayNumber[0],sDevice:'V',sType:chkVal,sIdIP:sIdIP},
				success: function(data) {
					if(chkVal == 0)
					{
						$("#relay_other_"+relayNumber[0]+"_"+sIdIP).addClass('checked');
						$("#relay_spa_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 1)
					{
						$("#relay_other_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"_"+sIdIP).addClass('checked');
						$("#relay_pool_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 2)
					{
						$("#relay_other_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"_"+sIdIP).addClass('checked');
					}
					
				}

			 });
		});
		
		$(".pumpRadio").click(function()
		{
			var chkVal 		= $(this).val();
			var relayNumber	= $(this).attr('name').split("_");	
			
			var sIdIP	=	$("#IpId").val();
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/saveDeviceMainType');?>", 
				data: {sDeviceID:relayNumber[0],sDevice:'PS',sType:chkVal,sIdIP:sIdIP},
				success: function(data) {
					if(chkVal == 0)
					{
						$("#relay_other_"+relayNumber[0]+"_"+sIdIP).addClass('checked');
						$("#relay_spa_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 1)
					{
						$("#relay_other_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"_"+sIdIP).addClass('checked');
						$("#relay_pool_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
					}
					else if(chkVal == 2)
					{
						$("#relay_other_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_spa_"+relayNumber[0]+"_"+sIdIP).removeClass('checked');
						$("#relay_pool_"+relayNumber[0]+"_"+sIdIP).addClass('checked');
					}
					
				}

			 });
		});
		
		
		$(".pumpsButton").click(function()
		{
			var sIdIP	=	$("#IpId").val();
			<?php if($iActiveMode == '2') { ?>
			
			var relayNumber = $(this).val();
			
			$(".loading-progress").show();
			var progress = $(".loading-progress").progressTimer({
				timeLimit: 10,
				onFinish: function () {
				  //$(".loading-progress").hide();
					setTimeout(function(){
								location.href='<?php echo base_url('home/setting/PS');?>'+'/'+Base64.encode(sIdIP);
								parent.$a.fancybox.close();},1000);
				}
			});
			
			$a("#checkLink").trigger('click');
			
			
			var status		= '';
			if($("#lablePump-"+relayNumber+"-"+sIdIP).hasClass('checked'))
			{	
				status	=	0;
			}
			else
			{
				status = 1;
			}
			
			<?php //if($iActiveMode == '2') { ?>
			 $.ajax({
				type: "POST",
				url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
				data: {sName:relayNumber,sStatus:status,sDevice:'PS',sIdIP:sIdIP},
				success: function(data) {
					
					if($("#lablePump-"+relayNumber+"-"+sIdIP).hasClass('checked'))
					{	
						$("#lablePump-"+relayNumber+"-"+sIdIP).removeClass('checked');
					}
					else
					{
						$("#lablePump-"+relayNumber+"-"+sIdIP).addClass('checked');
					}
					
					
					
				}
			}).error(function(){
			progress.progressTimer('error', {
				errorText:'ERROR!',
				onFinish:function(){
					alert('There was an error processing your information!');
				}
			});
			}).done(function(){
				progress.progressTimer('complete');
			});
			 <?php } else {  ?>
			  //alert('You can perform this operation in manual mode only.');
			  var bConfirm	=	confirm('You will need to change to Manual mode to make this change.\nWould you like to activate manual mode?' );
				if(bConfirm)
				{
					var relayNumber = $(this).val();
					var sPort	=	$("#selPort_PS_"+relayNumber).val();
					if(sPort == '')
					{
						alert("Please first select Port for Relay "+relayNumber+" !");
						$("#selPort_PS_"+relayNumber).css('border','2px red solid');
						$('html, body').animate({
									scrollTop: $("#selPort_PS_"+relayNumber).parent().offset().top
								}, 1000);
						return false;
					}
					
					$(".loading-progress").show();
					$.ajax({
						type: "POST",
						async:false,
						url: "<?php echo site_url('analog/changeMode');?>", 
						data: {iMode:'2'},
						success: function(data) {
						}
					});
					
					
					var progress = $(".loading-progress").progressTimer({
						timeLimit: 10,
						onFinish: function () {
						  //$(".loading-progress").hide();
							setTimeout(function(){
								//location.reload();
								location.href='<?php echo base_url('home/setting/PS');?>'+'/'+Base64.encode(sIdIP);
								parent.$a.fancybox.close();},1000);
							
						}
					});
					
					$a("#checkLink").trigger('click');
					
					var relayNumber = $(this).val();
					var status		= '';
					if($("#lablePump-"+relayNumber).hasClass('checked'))
					{	
						status	=	0;
					}
					else
					{
						status = 1;
					}
					
					$.ajax({
						type: "POST",
						url: "<?php echo site_url('home/updateStatusOnOff/');?>", 
						data: {sName:relayNumber,sStatus:status,sDevice:'PS',sPort:sPort},
						success: function(data) {
							
							if($("#lablePump-"+relayNumber).hasClass('checked'))
							{	
								$("#lablePump-"+relayNumber).removeClass('checked');
							}
							else
							{
								$("#lablePump-"+relayNumber).addClass('checked');
							}
							
							
							
						}
					}).error(function(){
					progress.progressTimer('error', {
						errorText:'ERROR!',
						onFinish:function(){
							alert('There was an error processing your information!');
						}
					});
					}).done(function(){
						progress.progressTimer('complete');
					});
				}
			  
			 <?php } ?> 
		});
		
		$(".pumpSpeedSet").click(function() {
			
			var sIdIP	=	$("#IpId").val();
			$(".loading-progress").show();
			var progress = $(".loading-progress").progressTimer({
				timeLimit: 10,
				onFinish: function () {
				  //$(".loading-progress").hide();
					setTimeout(function(){
										location.href='<?php echo base_url('home/setting/PS');?>'+'/'+Base64.encode(sIdIP);
										parent.$a.fancybox.close();
										},1000);
				}
			});
			
			$a("#checkLink").trigger('click');
			
			var speed 		= $(this).val();
			var pumpName	= $(this).attr('name');
			
			var PumpID		=	pumpName.split("_");
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/updatePumpSpeed/');?>", 
				data: {PumpID:PumpID[1],speed:speed,sIdIP:sIdIP},
				success: function(data) 
				{
					
				}
			}).error(function(){
			progress.progressTimer('error', {
				errorText:'ERROR!',
				onFinish:function(){
					alert('There was an error processing your information!');
				}
			});
			}).done(function(){
				progress.progressTimer('complete');
			});
		});
		<?php } ?>
		
	});

	function saveDevicePort(iDeviceNum,sDeviceType,sPort)
	{
		if(sPort == '')
		{
			alert("Please select Port number!");
			return false;
		}
		else
		{
			$("#selPort_R_"+iDeviceNum).css('border','');
			$("#selPort_P_"+iDeviceNum).css('border','');
			$("#selPort_V_"+iDeviceNum).css('border','');
			$("#selPort_PS_"+iDeviceNum).css('border','');
			$("#selPort_T_"+iDeviceNum).css('border','');
			
			if(sDeviceType != 'V')
			{
				$(".loading-progress").show();
				var progress = $(".loading-progress").progressTimer({
					timeLimit: 10,
					onFinish: function () {
					  setTimeout(function(){location.reload();parent.$a.fancybox.close();},1000);
					}
				});
			}
			
			if(sDeviceType != 'V')
				$a("#checkLink").trigger('click');
			
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/saveDevicePort/');?>", 
				data: {iDeviceNum:iDeviceNum,sDeviceType:sDeviceType,sPort:sPort},
				success: function(data) 
				{
					
				}
			}).error(function(){
				if(sDeviceType != 'V')
				{			
					progress.progressTimer('error', {
						errorText:'ERROR!',
						onFinish:function(){
							alert('There was an error processing your information!');
						}
					});
				}
			}).done(function(){
				if(sDeviceType != 'V')
					progress.progressTimer('complete');
			});
		}
	}

	function removePump(iPumpNumber,ipID)
	{
		var cnf	=	confirm("Are you sure, you want to remove Pump?");
		if(cnf)
		{
			$("#loadingImgPumpRemove_"+iPumpNumber).show();
			$.ajax({
					type: "POST",
					url: "<?php echo site_url('home/removePump');?>", 
					data: {iPumpNumber:iPumpNumber,ipID:ipID},
					success: function(resp) {
						$("#loadingImgPumpRemove_"+iPumpNumber+"_"+ipID).hide();
						alert('Pump details removed successfully!');
						//location.reload(); 
						location.href='<?php echo base_url('home/setting/PS');?>'+'/'+Base64.encode(ipID);
					}

				});
		}
	}

	function RemoveValveRelays(iValaveNumber,ipID)
		{
			var cnf	=	confirm("Are you sure, you want to remove relays?");
			if(cnf)
			{
				$.ajax({
					type: "POST",
					url: "<?php echo site_url('home/removeValveRelays');?>", 
					data: {iValaveNumber:iValaveNumber,ipID:ipID},
					success: function(resp) {
						//location.reload(); 
						location.href='<?php echo base_url('home/setting/V');?>'+'/'+Base64.encode(ipID)
					}

				 });
			}
		}
		
	function saveValveCount(ipID)
	{
		var ValveCnt	=	$("#moreValveCnt-"+ipID).val();
		if(ValveCnt == '')
		{
			alert('Please enter Valve number!');
			return false;
		}
		else if(isNaN(ValveCnt))
		{
			alert('Please enter valid Valve number!');
			return false;
		}
		else
		{
			$.ajax({
					type: "POST",
					url: "<?php echo site_url('home/addMoreValve/');?>", 
					data: {ValveCnt:ValveCnt,ipID:ipID},
					success: function(resp) {
						//location.reload(); 
						if(resp == 'error')
						{
							alert('Total valve must be equal to 8 or less than 8 in count!');
							return false;
						}
						else if(resp == 'success')
						{
							alert('Valve count updated successfully!');
							//location.reload(); 
							location.href='<?php echo base_url('home/setting/V');?>'+'/'+Base64.encode(ipID)
						}
					}

				 });
		}
	}	

	function removeValve(ipID,deviceNumber)
	{
		var cnf	=	confirm("Are you sure, you want to remove valve?");
		if(cnf)
		{
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('home/removeValve/');?>", 
				data: {ipID:ipID,deviceNumber:deviceNumber},
				success: function(resp) {
					//location.reload(); 
					location.href='<?php echo base_url('home/setting/V');?>'+'/'+Base64.encode(ipID)
				}

			 });
		}
	}

	function showBoardDetails(board)
	{
		
		if(board == '')
		{
			alert("Please select IP first!");
			return false;
		}
		$("[id^='onoffbuttons_']").hide();
		$("[id^='relayConfigure_']").hide();
		
		$("#onoffbuttons_"+board).show();
		$("#relayConfigure_"+board).show();
		
		$("[id^='graphbuttons_']").hide();
		$("#graphbuttons_"+board).show();
		
		$("#IpId").val(board);
	}
	
	
	
	</script>
	<div class="row">
		<div class="col-sm-12">
			<ol class="breadcrumb" style="float:left">
			  <li><img src="<?php echo HTTP_IMAGES_PATH.'icons/home.png';?>" width="24" style="vertical-align: middle !important;">&nbsp;<a href="<?php echo site_url();?>">Home</a> </li>
			  <li class="active"><?php echo $sDeviceFullName;?></li>
			</ol>
			<p>
			<a class="fancybox" id="checkLink" href="#inline1" style="display:none;">&nbsp;</a>
			<div id="inline1" style="width:250px;height:auto; display:none;"><div class="loading-progress"></div></div>
			</p>
		</div>
	</div>	

	<div class="row">
		<div class="col-sm-12">
		<span style="color:#FFF; font-weight:bold;">Select Board : </span>
		<select name="selPort" id="selPort" onchange="showBoardDetails(this.value)">
			<option value="">--IP(Name)--</option>
			<?php echo $sIPOptions;?>
		</select>	
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
		&nbsp;
		</div>
	</div>
	<!-- Device Views -->
	<?php
		if($sDevice == 'R')//24V Relay Devices.
		{ 
			if($sAccess == '1' || $sAccess == '2')
			{ 
				$this->load->view('Relays',$aParam);
			}
		}
		else if($sDevice == 'P') //12V Relay Devices.
		{ 
			if($sAccess == '1' || $sAccess == '2')
			{ 
				$this->load->view('Powercenter',$aParam);
			}
		}
		else if($sDevice == 'V') //Valve Devices.
		{ 
			if($sAccess == '1' || $sAccess == '2')
			{ 
				$this->load->view('Valve',$aParam);
			}
		}
		else if($sDevice == 'PS') //Pump Devices.
		{ 
			if($sAccess == '1' || $sAccess == '2')
			{ 
				$this->load->view('Pumps',$aParam);
			}
		}else if($sDevice == 'T') //Temperature Sensors.
		{ 
			if($sAccess == '1' || $sAccess == '2')
			{ 
				$this->load->view('TempratureSensor',$aParam);
			}
		}
	?>
	<input type="hidden" id="IpId" value="<?php echo $iFirstIPId;?>">
	<!-- Device Views -->
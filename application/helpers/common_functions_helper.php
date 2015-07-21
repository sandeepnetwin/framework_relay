<?php
/**
* @Programmer: SMW
* @Created: 25 Mar 2015
* @Modified: 
* @Description: Functions for display and set the relay valve values.
**/

	function relayboard_command($sUrl)
	{
		/* $sBasePath = "http://24.234.248.35/Securedshowing_api/sprinkler_api.php";
		$sData = file_get_contents($sBasePath."?url=".urlencode($sUrl));
		//@$sData = file_get_contents($sUrl); */
		
		/* $curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $sUrl);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$sData = curl_exec($curl_handle);
		
		if($sData === FALSE){
			die('@relayboard_command; '.curl_error($curl_handle).' <br/>Please insert proper ip adderss in <b>"include/constants.php"</b>');
		}
		curl_close($curl_handle);
		return $sData; */
		return send_command_udp($sUrl);
	}
	
	function send_command_udp($sUrl){
		$sServer = $sUrl['ip_address'];
		$iPort = $sUrl['port_no'];
		$sInput = $sUrl['data'];
		
		//Create socket for UDP
		/*if(!($sSock = socket_create(AF_INET, SOCK_DGRAM, 0)))
		{
			$iErrorcode = socket_last_error();
			$sErrormsg = socket_strerror($iErrorcode);
			 
			die("Couldn't create socket: [$iErrorcode] $sErrormsg \n");
		}
		
		//Send the message to the server
		if( ! socket_sendto($sSock, $sInput , strlen($sInput) , 0 , $sServer , $iPort))
		{
			$iErrorcode = socket_last_error();
			$sErrormsg = socket_strerror($iErrorcode);
			 
			die("Could not send data: [$iErrorcode] $sErrormsg \n");
		}
		
		//Now receive reply from server and print it
		if(socket_recv ( $sSock , $sReply , 2045 , MSG_WAITALL ) === FALSE)
		{
			$iErrorcode = socket_last_error();
			$sErrormsg = socket_strerror($iErrorcode);
			 
			die("Could not receive data: [$iErrorcode] $sErrormsg \n");
		}*/
		
		 $fp = @ fsockopen("udp://$sServer", $iPort, $iErrorcode, $sErrormsg,3);
		if (!$fp) {
			die("Could not send data: [$iErrorcode] $sErrormsg \n");
		} else {
			fwrite($fp, "$sInput");
			$sReply = fread($fp, 1024);
			fclose($fp);
		} 
		
		//echo '<pre>'; print_r($sReply); die;
		//Check for invalid response.
		$iCommaCount = substr_count($sReply, ",");
		if($iCommaCount < 10 || stripos($sReply, '?') !== FALSE){
			die("Invalid response: $sReply \n");
		}
		
		return $sReply;
	}

	function send_to_rlb($sUrl){
		$sReturnUrl = get_url($sUrl);
		$sResult = relayboard_command($sReturnUrl);
		if ($sResult === false) 
		{
			return 0;
		}
		return 1;
	}

	function get_from_rlb($sUrl) {
		$sReturnUrl = get_url($sUrl);
		return $sResult = relayboard_command($sReturnUrl);
	}

	function get_url($sUrl){
		/* $sReturnUrl = '';
		$sIpAddress = '';
		if(IP_ADDRESS){
			$sIpAddress = IP_ADDRESS;
		}elseif(isset($_SESSION['relayboard']['ip_addres'])){
			$sIpAddress = $_SESSION['relayboard']['ip_addres'];
		}
		if(IS_SSL){
			$sReturnUrl = 'https://'.$sIpAddress.$sUrl;
		}else{
			$sReturnUrl = 'http://'.$sIpAddress.$sUrl;
		}
		return $sReturnUrl; */

		$CI = get_instance();
		$CI->load->model('home_model');
	    $aSettingDetails	=	$CI->home_model->getSettingDetails();

	   	$sReturnUrl = array();
		$sIpAddress = $aSettingDetails[0];
		$sPortNo = $aSettingDetails[1];
		//Check for IP Address constant
		
		if($sIpAddress == '')
		{
			if(IP_ADDRESS){
				$sIpAddress = IP_ADDRESS;
			}elseif(isset($_SESSION['relayboard']['ip_addres'])){
				$sIpAddress = $_SESSION['relayboard']['ip_addres'];
			}
		}
		
		//Check for Port Number constant
		if($sPortNo == '')
		{	
			if(PORT_NO){
				$sPortNo = PORT_NO;
			}elseif(isset($_SESSION['relayboard']['port_no'])){
				$sPortNo = $_SESSION['relayboard']['port_no'];
			}
		}
		
		//Assign varible and return to udp port
		$sReturnUrl['ip_address'] = $sIpAddress;
		$sReturnUrl['port_no'] = $sPortNo;
		$sReturnUrl['data'] = $sUrl;
		return $sReturnUrl;
	}

	function get_rlb_status(){
		$aReturn = array();
		$sUrl = 's';
		$sResponse = get_from_rlb($sUrl);
		
		$aResponse = explode(',',$sResponse);
		
		$aReturn['response'] = $sResponse;
		$aReturn['day'] = $aResponse['3'];
		$aReturn['time'] = $aResponse['4'];
		$aReturn['valves'] = $aResponse['7'];
		$aReturn['relay'] = $aResponse['8'];
		$aReturn['powercenter'] = $aResponse['9'];
		
		$aReturn['controller_temp'] = $aResponse['15'];
		$aReturn['temp_sensor_1'] = $aResponse['16'];
		$aReturn['temp_sensor_2'] = $aResponse['17'];
		$aReturn['temp_sensor_3'] = $aResponse['18'];
		$aReturn['temp_sensor_4'] = $aResponse['19'];
		$aReturn['temp_sensor_5'] = $aResponse['20'];

		$aReturn['AP0'] = $aResponse['10'];
		$aReturn['AP1'] = $aResponse['11'];
		$aReturn['AP2'] = $aResponse['12'];
		$aReturn['AP3'] = $aResponse['13'];		
				
		$aReturn['push'] = $aResponse['22'];
		$aReturn['level_sensor_instant'] = $aResponse['21'];
		$aReturn['remote_spa_ctrl_st'] = $aResponse['22'];
		$aReturn['level_sensor_avg'] = $aResponse['23'];
		$aReturn['pump_seq_0_st'] = $aResponse['24'];
		$aReturn['pump_seq_1_st'] = $aResponse['25'];
		$aReturn['pump_seq_2_st'] = isset($aResponse['26']) ? $aResponse['26'] : '';
		
		return $aReturn;
	}

	function replace_return($sStr, $sReplace, $iReplace){
		$iStrCount = strlen($sStr);
		$sReturn = '';
		for($iStr = 0; $iStr < $iStrCount; $iStr++){
			if($iStr == $iReplace){
				$sReturn .= $sReplace;
			}else{
				$sReturn .= $sStr[$iStr];
			}
		}
		return $sReturn;
	}
	
	function onoff_rlb_relay($sRelayStatus){
		$sUrl = 'R,'.$sRelayStatus;
		$sResponse = send_to_rlb($sUrl);		
		return $sResponse;
	}

	function onoff_rlb_powercenter($sPowercenterStatus){
		$sUrl = 'B,'.$sPowercenterStatus;
		$sResponse = send_to_rlb($sUrl);		
		return $sResponse;
	}
	
	function onoff_rlb_valve($sRelayStatus){
		$sUrl = 'V,'.$sRelayStatus;
		$sResponse = send_to_rlb($sUrl);		
		return $sResponse;
	}

	function onoff_rlb_pump($sRelayStatus){
		echo $sUrl = 'm '.$sRelayStatus;
		$sResponse = send_to_rlb($sUrl);		
		return $sResponse;
	}

	
	
	function switch_arrays($aOrig, $aNew){
		$aReturn = array();
		foreach($aNew as $vNew){
			$aReturn[] = $aOrig[$vNew];
		}
		return $aReturn;
	}

	function update_prog_status($iProgId, $iStatus){
		if($iProgId){
			$sSqlUpdate = "UPDATE rlb_relay_prog SET relay_prog_active='".$iStatus."' WHERE relay_prog_id='".$iProgId."'";
			$rResult = mysql_query($sSqlUpdate) or die('ERR: @sSqlUpdate=> '.mysql_error());
		}
	}
	
	function get_current_mode(){
		//get list of relay modes.
		$iMode = '';
		$sSql = "SELECT * FROM rlb_modes WHERE mode_status='1' ";
		$rResult = mysql_query($sSql) or die('ERR: @sSql=> '.mysql_error());
		$iCnt = mysql_num_rows($rResult);
		if($iCnt){
			$aRow = mysql_fetch_assoc($rResult);
			$iMode = $aRow['mode_id'];
		}
		return $iMode;
	}
	
	/*function to return device name
	* @iDeviceType => 1-Relay, 2-Valve, 3-Powercenter
	* @iDeviceNum => As per availabel device type start from 0 - n
	*/
	function get_device_name($iDeviceType, $iDeviceNum){
		$aDeviceType = array(1, 2, 3);
		$aDeviceTypeName = array( '1' => 'Relay', '2' => 'Valve' , '3' => 'Powercenter');
		$aTbl = array('1' => 'rlb_relays', '2' => 'rlb_valves', '3' => 'rlb_powercenters');
		$aFldWhere = array('1' => 'relay_number', '2' => 'valve_number', '3' => 'powercenter_number');
		$aFldSel = array('1' => 'relay_name', '2' => 'valve_name', '3' => 'powercenter_name');
		$sDeviceNameAE = $aDeviceTypeName[$iDeviceType].' '.$iDeviceNum;
		
		if(is_numeric($iDeviceNum) && in_array($iDeviceType, $aDeviceType)){
			$sSqlEdit = "SELECT * FROM ". $aTbl[$iDeviceType] ." WHERE ". $aFldWhere[$iDeviceType] ." ='".$iDeviceNum."'";
			$rResultEdit = mysql_query($sSqlEdit) or die('ERR: @sSqlEdit=> '.mysql_error());
			$iCnt = mysql_num_rows($rResultEdit);
			if($iCnt){
				$aRowEdit = mysql_fetch_assoc($rResultEdit);
				$sDeviceNameAE = stripslashes($aRowEdit[$aFldSel[$iDeviceType]]);
			}
		}
		return $sDeviceNameAE;
	}
	
?>
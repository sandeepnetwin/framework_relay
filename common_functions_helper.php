<?php
/**
* @Programmer: SMW
* @Created: 25 Mar 2015
* @Modified: 
* @Description: Functions for display and set the relay valve values.
**/

	function relayboard_command($sUrl)
	{
		return send_command_udp($sUrl);
	}
	
	function send_command_udp($sUrl){
		$sServer = $sUrl['ip_address'];
		$iPort = $sUrl['port_no'];
		$sInput = $sUrl['data'];
		
		$fp =  fsockopen("udp://$sServer", $iPort, $iErrorcode, $sErrormsg,3);
		if (!$fp) {
			die("Could not send data: [$iErrorcode] $sErrormsg \n");
		} else {
                        
			fwrite($fp, "$sInput");
			$sReply = fread($fp, 1024);
			fclose($fp);
		}
                
        //Check for invalid response.
		$iCommaCount = substr_count($sReply, ",");
		if(stripos($sReply, '?') !== FALSE)
		{
			die("Invalid response: $sReply \n");
		}
		//if(hardware,busy)
		
		return $sReply;
	}
	
	function system_command($command)
	{
		//system($command);
		
		
	}
	
	function send_command_udp_new($IP,$PORT,$aPumps)
	{
		$aPumpNumber	=	json_decode($aPumps);
		$sServer 		= 	$IP;
		$iPort 			=	$PORT;
		
		$cntPump		=	count($aPumpNumber);
		
		$server = $sServer;
		$port = 13330;
		
		if(!($sSock = socket_create(AF_INET, SOCK_DGRAM, 0)))
		{
			$iErrorcode = socket_last_error();
			$sErrormsg = socket_strerror($iErrorcode);
			 
			die("Couldn't create socket: [$iErrorcode] $sErrormsg \n");
		} 
		
		socket_connect ( $sSock , $server , $port );
		//$line = socket_read ($sSock, 1024) or die("Could not read server response\n");
		//var_dump($line);
		$package = "\x07\x00";
		socket_send($sSock, $package, strLen($package), 0);
		$line1 = socket_read($sSock, 255);
		$strPumpResponse	= '';
		
		$package = "\x06\x00";
		socket_send($sSock, $package, strLen($package), 0);
		//echo $cntPump;
		
		for($i=0;$i<$cntPump; $i++)
		{
			$strResponse = socket_read($sSock, 255);
			while(preg_match('/^S/',$strResponse))
			{
                $strResponse = socket_read($sSock, 255);
			}
			
			if($strPumpResponse == '')
			{
				$strPumpResponse = $strResponse;
			}
			else
			{
				$strPumpResponse .= '|||'.$strResponse;
			}				
		}
		
		$package ="\x7f\x00";
		socket_send($sSock, $package, strLen($package), 0);
		
		//$line3 = socket_read($sSock, 255);
		//var_dump($line3); 
		
		socket_close($sSock);
		//die('STOP');
		return $strPumpResponse;
	}
	
	function response_input_switch($IP,$PORT)
	{
		$aPumpNumber	=	json_decode($aPumps);
		$sServer 		= 	$IP;
		$iPort 			=	$PORT;
		
		$cntPump		=	count($aPumpNumber);
		
		$server = $sServer;
		$port = 13330;
		
		if(!($sSock = socket_create(AF_INET, SOCK_DGRAM, 0)))
		{
			$iErrorcode = socket_last_error();
			$sErrormsg = socket_strerror($iErrorcode);
			 
			die("Couldn't create socket: [$iErrorcode] $sErrormsg \n");
		} 
		
		socket_connect ( $sSock , $server , $port );
		$package = "\x07\x00";
		socket_send($sSock, $package, strLen($package), 0);
		$line1 = socket_read($sSock, 255);
		//var_dump($line1);
		$strPumpResponse	= '';
		
		$package = "\x06\x00";
		socket_send($sSock, $package, strLen($package), 0);
		
		$strResponse = socket_read($sSock, 255);
		while(!preg_match('/^S/',$strResponse))
		{
			$strResponse = socket_read($sSock, 255);
		}			
		
		$package ="\x7f\x00";
		socket_send($sSock, $package, strLen($package), 0);
		socket_close($sSock);
		
		return $strResponse;
	}
	
	function checkResponse($Response,$arrCheck)
	{
		
	}
	
	function send_command_udp_new1($IP,$PORT)
	{
		$sServer = $IP;
		$iPort = $PORT;
		
		$server = $sServer;
		$port = 13330;
		
		if(!($sSock = socket_create(AF_INET, SOCK_DGRAM, 0)))
		{
			$iErrorcode = socket_last_error();
			$sErrormsg = socket_strerror($iErrorcode);
			 
			die("Couldn't create socket: [$iErrorcode] $sErrormsg \n");
		} 
		
		socket_connect ( $sSock , $server , $port );
		//$line = socket_read ($sSock, 1024) or die("Could not read server response\n");
		//var_dump($line);
		$package = "\x07\x00";
		socket_send($sSock, $package, strLen($package), 0);
		$line1 = socket_read($sSock, 255);
		//var_dump( $line1 );
		//$package = "\x06\x00";
		//socket_send($sSock, $package, strLen($package), 0);
		$line2 = socket_read($sSock, 255); 
		if(preg_match('/^S/',$line2))
		{
			$line2 = socket_read($sSock, 255);
			$line2 .= '|||'.socket_read($sSock, 255);				
		}
		
		socket_close($sSock);
		return $line2;
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

	function get_url($sUrl)
	{
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
		//$sResponse	=	'S,054,0,1,22:08:00,0,14,00000...,..........000000,00000000,0,0,1816,2373,0,86.6F,,,,,,0.00,0000,0.00,0,0,0';
		$aResponse = explode(',',$sResponse);
		
		$aReturn['response'] = $sResponse;
		//$aReturn['day'] = $aResponse['3'];
		$aReturn['day'] = (isset($aResponse['3'])) ? $aResponse['3'] : '';
		$aReturn['time'] = (isset($aResponse['4'])) ? $aResponse['4'] : '';
		$aReturn['valves'] = (isset($aResponse['7'])) ? $aResponse['7'] : '';
		$aReturn['relay'] = (isset($aResponse['8'])) ? $aResponse['8'] : '';
		$aReturn['powercenter'] = (isset($aResponse['9'])) ? $aResponse['9'] : '';
		
		$aReturn['TS0'] = (isset($aResponse['15'])) ? $aResponse['15'] : '';
		$aReturn['TS1'] = (isset($aResponse['16'])) ? $aResponse['16'] : '';
		$aReturn['TS2'] = (isset($aResponse['17'])) ? $aResponse['17'] : '';
		$aReturn['TS3'] = (isset($aResponse['18'])) ? $aResponse['18'] : '';
		$aReturn['TS4'] = (isset($aResponse['19'])) ? $aResponse['19'] : '';
		$aReturn['TS5'] = (isset($aResponse['20'])) ? $aResponse['20'] : '';

		$aReturn['AP0'] = (isset($aResponse['10'])) ? $aResponse['10'] : '';
		$aReturn['AP1'] = (isset($aResponse['11'])) ? $aResponse['11'] : '';
		$aReturn['AP2'] = (isset($aResponse['12'])) ? $aResponse['12'] : '';
		$aReturn['AP3'] = (isset($aResponse['13'])) ? $aResponse['13'] : '';		
				
		$aReturn['push'] = (isset($aResponse['22'])) ? $aResponse['22'] : '';
		$aReturn['level_sensor_instant'] = (isset($aResponse['21'])) ? $aResponse['21'] : '';
		$aReturn['remote_spa_ctrl_st'] = (isset($aResponse['22'])) ? $aResponse['22'] : '';
		$aReturn['level_sensor_avg'] = (isset($aResponse['23'])) ? $aResponse['23'] : '';
		$aReturn['pump_seq_0_st'] = (isset($aResponse['24'])) ? $aResponse['24'] : '';
		$aReturn['pump_seq_1_st'] = (isset($aResponse['25'])) ? $aResponse['25'] : '';
		$aReturn['pump_seq_2_st'] = isset($aResponse['26']) ? $aResponse['26'] : '';
		
		return $aReturn;
	}
	
	function get_rlb_status_shell(){
		$aReturn = array();
		$sUrl = 'rlb s';
		$sResponse = shell_exec($sUrl);
		$aResponse = explode(',',$sResponse);
		
		$aReturn['response'] = $sResponse;
		//$aReturn['day'] = $aResponse['3'];
		$aReturn['day'] = (isset($aResponse['3'])) ? $aResponse['3'] : '';
		$aReturn['time'] = (isset($aResponse['4'])) ? $aResponse['4'] : '';
		$aReturn['valves'] = (isset($aResponse['7'])) ? $aResponse['7'] : '';
		$aReturn['relay'] = (isset($aResponse['8'])) ? $aResponse['8'] : '';
		$aReturn['powercenter'] = (isset($aResponse['9'])) ? $aResponse['9'] : '';
		
		$aReturn['TS0'] = (isset($aResponse['15'])) ? $aResponse['15'] : '';
		$aReturn['TS1'] = (isset($aResponse['16'])) ? $aResponse['16'] : '';
		$aReturn['TS2'] = (isset($aResponse['17'])) ? $aResponse['17'] : '';
		$aReturn['TS3'] = (isset($aResponse['18'])) ? $aResponse['18'] : '';
		$aReturn['TS4'] = (isset($aResponse['19'])) ? $aResponse['19'] : '';
		$aReturn['TS5'] = (isset($aResponse['20'])) ? $aResponse['20'] : '';

		$aReturn['AP0'] = (isset($aResponse['10'])) ? $aResponse['10'] : '';
		$aReturn['AP1'] = (isset($aResponse['11'])) ? $aResponse['11'] : '';
		$aReturn['AP2'] = (isset($aResponse['12'])) ? $aResponse['12'] : '';
		$aReturn['AP3'] = (isset($aResponse['13'])) ? $aResponse['13'] : '';		
				
		$aReturn['push'] = (isset($aResponse['22'])) ? $aResponse['22'] : '';
		$aReturn['level_sensor_instant'] = (isset($aResponse['21'])) ? $aResponse['21'] : '';
		$aReturn['remote_spa_ctrl_st'] = (isset($aResponse['22'])) ? $aResponse['22'] : '';
		$aReturn['level_sensor_avg'] = (isset($aResponse['23'])) ? $aResponse['23'] : '';
		$aReturn['pump_seq_0_st'] = (isset($aResponse['24'])) ? $aResponse['24'] : '';
		$aReturn['pump_seq_1_st'] = (isset($aResponse['25'])) ? $aResponse['25'] : '';
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
		$sUrl = 'm '.$sRelayStatus;
		$sResponse = send_to_rlb($sUrl);		
		return $sResponse;
	}
	
	function getAddressToPump($sDeviceNumber){
		$sUrl = 'p pm'.$sDeviceNumber;
		$sReturnUrl = get_url($sUrl);
		$sResult = relayboard_command($sReturnUrl);
		return $sResult;
	}
	
	function assignAddressToPump($sDeviceNumber,$sAddress){
		$sUrl = 'p pm'.$sDeviceNumber.' '.$sAddress;
		$sReturnUrl = get_url($sUrl);
		$sResult = relayboard_command($sReturnUrl);
			
		return $sResult;
	}
	
	function assignValvesToRelay($sHexNumber){
		$sUrl = 'p vlm '.$sHexNumber;
		$sReturnUrl = get_url($sUrl);
		$sResult = relayboard_command($sReturnUrl);
			
		return $sResult;
	}
	
	function getTempratureBus($sHexNumber){
		$sUrl = 't';
		$sReturnUrl = get_url($sUrl);
		$sResult = relayboard_command($sReturnUrl);
			
		return $sResult;
	}
	
	function configureTempratureBus($TS,$BUS)
	{
		$sUrl = 'p '.$TS.' '.$BUS;
		
		$sReturnUrl = get_url($sUrl);
		$sResult = relayboard_command($sReturnUrl);
			
		return $sResult;
	}
	
	function removePumpAddress($Pump)
	{
		$sUrl = 'p '.$Pump.' 0';
		$sReturnUrl = get_url($sUrl);
		$sResult = relayboard_command($sReturnUrl);
			
		return $sResult;
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
	
	
	function getPermissionOfModule($userID)
	{
		$CI = get_instance();
		$CI->load->model('access_model');
		$aPermissions = $CI->access_model->getPermission($userID);
		$aModules	= array();
		$aReturn	= array();
		
		//var_dump($aPermissions);
		if(!empty($aPermissions))
		{
		  foreach($aPermissions as $sPermission)
		  {
			$aModules['ids'][] = $sPermission->module_id;
			$aModules['access_'.$sPermission->module_id] = $sPermission->access;
		  }
		}  
		
		$CI->load->model('user_model');
	    $aAllMActiveModule	=	$CI->user_model->getAllModulesActive();
		
		$aReturn['sPermissionModule']	=	$aModules;
		$aReturn['sActiveModule']		=	$aAllMActiveModule;
		
		return json_encode($aReturn);
	}
	
	function getModuleAccess($sModule)
	{
		$CI = get_instance();
		$CI->load->model('access_model');
	    $aPermissions = $CI->access_model->getPermission($userID);
	}
	
?>
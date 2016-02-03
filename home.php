<?php
    /**
    * @Programmer: Dhiraj S.
    * @Created: 13 July 2015
    * @Modified: 
    * @Description: Home Controller for dashboard and device details.
    **/

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller 
{
    public $userID,$aPermissions,$aModules,$aAllActiveModule;
	
    public function __construct()  
    {
        parent::__construct();
		$this->load->library('form_validation');
        $this->load->helper('common_functions'); //Common functions will be available for all functions in the file.
		
        //print_r($this->session->all_userdata());
		if (!$this->session->userdata('is_admin_login')) //START : Check if user login or not.
        {
            redirect('dashboard/login/');
            die;
        }  //END : Check if user login or not. 
		
        //Get Permission Details
        if($this->userID == '')
        $this->userID = $this->session->userdata('id');

        if($this->aPermissions == '')
        {
            $this->aPermissions 	= json_decode(getPermissionOfModule($this->userID));
            $this->aModules 		= $this->aPermissions->sPermissionModule;	
            $this->aAllActiveModule     = $this->aPermissions->sActiveModule;
        }
   }

    public function index() //START : Function for dashboard
    {
		$aViewParameter         =   array(); // Array for passing parameter to view.
        $aViewParameter['page'] =   'home'; 
        
        //Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();
		
		//Get All IP Details.
		$aViewParameter['aIPDetails'] = $this->home_model->getBoardIP();
		$this->load->model('analog_model');
		
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		foreach($aViewParameter['aIPDetails'] as $IP)
		{
			$shhPort	=	'';
			if(IS_LOCAL == '1')
			{
				//Get SSH port of the RLB board using IP.
				$shhPort = $this->home_model->getSSHPortFromID($IP->id);
			}
			$sResponse		=	array();
			$sValves        =   ''; 
			$sRelays        =   '';  
			$sPowercenter   =   ''; 
			$sTime          =   '';
			$sPump			=	'';	
			$sTemprature	=	'';
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($IP->ip,$sPort,$shhPort);
		
			$sValves        =   $sResponse['valves']; // Valve Device Status
			$sRelays        =   $sResponse['relay'];  // Relay Device Status
			$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
			$sTime          =   $sResponse['time']; // Server Time from Response
			
			$aViewParameter['sRelays'.$IP->id]		=	$sRelays;
			$aViewParameter['sValves'.$IP->id]		=	$sValves;
			$aViewParameter['sPowercenter'.$IP->id]	=	$sPowercenter;
			
			//Pump device Status
			$sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
			$aViewParameter['sPump'.$IP->id]		=	$sPump;
			
			// Temperature Sensor Device 
			$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);
			
			//Remote Switch Spa
			$aViewParameter['aAP'.$IP->id]	=	substr($sResponse['remote_spa_ctrl_st'], 0, 4);
			
			$aViewParameter['aAPResult'.$IP->id]            =   $this->analog_model->getAllAnalogDevice($IP->id);
			$aViewParameter['aAPResultDirection'.$IP->id]   =   $this->analog_model->getAllAnalogDeviceDirection($IP->id);
			
			$tempipID = ($IP->id > 1) ? $IP->id : '';
			
			$aViewParameter['Remote_Spa'.$IP->id] = isset($extra['Remote_Spa'.$tempipID])?$extra['Remote_Spa'.$tempipID]:0;
			$aViewParameter['Remote_Spa_display'.$IP->id] = isset($extra['Remote_Spa_display'.$tempipID])?$extra['Remote_Spa_display'.$tempipID]:0;
			
				
			//START : Parameter for View
			$aViewParameter['relay_count'.$IP->id]  =   strlen($sRelays);
			$aViewParameter['valve_count'.$IP->id]  =   strlen($sValves);
			$aViewParameter['power_count'.$IP->id]  =   strlen($sPowercenter);
				
			$aViewParameter['pump_count'.$IP->id]   	=   count($sPump);
			$aViewParameter['temprature_count'.$IP->id] =   count($sTemprature);
			$aViewParameter['time'.$IP->id]         	=   $sTime;
				
			$aViewParameter['activeCountRelay'.$IP->id] = 	$aViewParameter['relay_count'.$IP->id] - substr_count($sRelays, '.');
				
			$aViewParameter['OnCountRelay'.$IP->id]    = 	substr_count($sRelays, '1');
			$aViewParameter['OFFCountRelay'.$IP->id]   = 	substr_count($sRelays, '0');
			$aViewParameter['OnCountPower'.$IP->id]    = 	substr_count($sPowercenter, '1');
			$aViewParameter['OFFCountPower'.$IP->id]   = 	substr_count($sPowercenter, '0');
			$aViewParameter['activeCountValve'.$IP->id]= 	$aViewParameter['valve_count'.$IP->id] - substr_count($sValves, '.');
			$aViewParameter['OnCountValve'.$IP->id]    = 	substr_count($sValves, '1') + substr_count($sValves, '2');
			$aViewParameter['OFFCountValve'.$IP->id]   = 	substr_count($sValves, '0');
				
				
			$activeCountTemperature 	=	0;
			foreach($sTemprature as $temperature)
			{
					if($temperature > 0)
						$activeCountTemperature++;
			}
			$aViewParameter['activeCountTemperature'.$IP->id]     	= 	$activeCountTemperature;
		}	
        //END : Parameter for View
		
		/* echo '<pre>';
		print_r($aViewParameter);
		echo '</pre>'; */
		
		//START: GET the active MODE details.
			$aViewParameter['welcome_message'] = '';
			$this->load->model('home_model');
			$aModeDetails = $this->home_model->getActiveModeDetails();
			
		//Get Extra Details
		//list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();

		$activeCountPump				   = $extra['PumpsNumber'];
		$aViewParameter['activeCountPump'] = $activeCountPump;
		
		
		$OnCountPump 	=	0;
		$OFFCountPump	=	0;
		//foreach($sPump as $pump)
		for($i=0;$i<$activeCountPump;$i++)
		{
			if($sPump[$i] > 0)
				$OnCountPump++;
			else
				$OFFCountPump++;
		}
		$aViewParameter['OnCountPump']     		= 	$OnCountPump;
		$aViewParameter['OFFCountPump']     	= 	$OFFCountPump;
			
		
		if($aModeDetails['start_time'] != '0000-00-00 00:00:00' && $aModeDetails['start_time'] != '')
		{
			$sTimeDiff = date_diff(date_create($aModeDetails['start_time']),date_create(date('Y-m-d H:i:s')));
			
			$strMessage = 'For';
			if($sTimeDiff->y != 0)
			{
				if($sTimeDiff->y == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->y.' Year</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->y.' Years</strong> and';
			}	
			if($sTimeDiff->m != 0)
			{
				if($sTimeDiff->m == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->m.' Month</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->m.' Months</strong> and';
			}
			if($sTimeDiff->d != 0)
			{
				if($sTimeDiff->d == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->d.' Day</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->d.' Days</strong> and';
			}
			if($sTimeDiff->h != 0)
			{
				if($sTimeDiff->h == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->h.' Hour</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->h.' Hours</strong> and';
			}
			if($sTimeDiff->i == 0 || $sTimeDiff->i == 1)	
				$strMessage .= ' <strong>1 minute</strong>,';
			else
				$strMessage .= ' <strong>'.$sTimeDiff->i.' minutes</strong>,';
			
			$strMessage .= ' '.$aModeDetails['mode_name'].' Mode has been Active.';
			
			/* $sExtra = '';
			
			if($extra['Pool_Temp'] == '1' && isset($extra['Pool_Temp']))
			{
				if(isset($extra['Pool_Temp_Address']) && $extra['Pool_Temp_Address'] != '' && $sResponse[$extra['Pool_Temp_Address']] != '')
				{
					$strMessage.=' <br /><strong>Pool temperature is '.$sResponse[$extra['Pool_Temp_Address']].'.</strong>';
					
					$sExtra .='Pool : '.$sResponse[$extra['Pool_Temp_Address']];
				}
				else 
					$sExtra .='<br>';
				
			}
			else 
					$sExtra .='<br>';
			
			if($extra['Spa_Temp'] == '1' && isset($extra['Spa_Temp']) && $sResponse[$extra['Spa_Temp_Address']] != '')
			{
				if(isset($extra['Spa_Temp_Address']) && $extra['Spa_Temp_Address'] != '')
				{
					$strMessage.=' <strong>Spa temperature is '.$sResponse[$extra['Spa_Temp_Address']].'.</strong>';
					$sExtra .='<br>Spa : '.$sResponse[$extra['Spa_Temp_Address']];
				}
				else 
					$sExtra .='<br><br>';
				
			}
			else 
					$sExtra .='<br><br>'; */
			
				
			$aViewParameter['sTemperature'] = '<p style="line-height:auto">'.$sExtra.'</p>';
			
			
			
			
			$aAllActiveProgram	=	$this->home_model->getAllActivePrograms();
			
			if(!empty($aAllActiveProgram))
			{
				foreach($aAllActiveProgram as $aActiveProgram)
				{
					if($aActiveProgram->device_type == 'R')
					{
						$strMessage .= ' <br /><strong>'.$aActiveProgram->program_name.'</strong> Program is Running for <strong>Relay '.$aActiveProgram->device_number.'</strong>!';
					}
					else if($aActiveProgram->device_type == 'PS')
					{
						$aPumpDetails 	=	$this->home_model->getPumpDetails($aActiveProgram->device_number);
						
						if(is_array($aPumpDetails) && !empty($aPumpDetails))
						{
							foreach($aPumpDetails as $aResultEdit)
						    { 
								$sPumpNumber  = $aResultEdit->pump_number;
								$sPumpType    = $aResultEdit->pump_type;
								$sPumpSubType = $aResultEdit->pump_sub_type;
								$sPumpSpeed   = $aResultEdit->pump_speed;
						    }
						}
						
						$strMessage .= ' <br /><strong>'.$aActiveProgram->program_name.'</strong> Program is Running for <strong>Pump '.$aActiveProgram->device_number.'</strong>';
						
						if($sPumpType	==	'Emulator' && $sPumpSubType == 'VS')
						{
							$strMessage .= ' With <strong>Speed '.$sPumpSpeed.' </strong>';
						}
						$strMessage .= '!';
					}
				}
			}
			
			$aViewParameter['welcome_message'] = $strMessage;
		}//END: GET the active MODE details.
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
		//$this->load->view('Home');
		$this->template->build('Home',$aViewParameter);


    } //END : Function for dashboard
	
	
	
	public function checkSettingsSaved() //START : Check if IP and PORT is added.
    {
        $this->load->model('home_model');
        list($sIpAddress, $sPortNo, $extra) = $this->home_model->getSettings();
        
        if($sIpAddress == '' && $sPortNo == '')
            redirect(site_url('home/setting/'));
    }//END : Check if IP and PORT is added.
	
	
	function getCurrentServerTime()
	{
		$response = array('time' => date('H:i:s'));
		header('Content-type: application/json');
		echo json_encode($response);
	}
	
	public function getModeTime()
	{
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
		
		$this->load->model('home_model');
		$aModeDetails = $this->home_model->getActiveModeDetails();
			
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
					
		if($aModeDetails['start_time'] != '0000-00-00 00:00:00' && $aModeDetails['start_time'] != '')
		{
			$sTimeDiff = date_diff(date_create($aModeDetails['start_time']),date_create(date('Y-m-d H:i:s')));
			
			$strMessage = 'For';
			if($sTimeDiff->y != 0)
			{
				if($sTimeDiff->y == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->y.' Year</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->y.' Years</strong> and';
			}	
			if($sTimeDiff->m != 0)
			{
				if($sTimeDiff->m == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->m.' Month</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->m.' Months</strong> and';
			}
			if($sTimeDiff->d != 0)
			{
				if($sTimeDiff->d == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->d.' Day</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->d.' Days</strong> and';
			}
			if($sTimeDiff->h != 0)
			{
				if($sTimeDiff->h == 1)
					$strMessage .= ' <strong>'.$sTimeDiff->h.' Hour</strong> and';
				else
					$strMessage .= ' <strong>'.$sTimeDiff->h.' Hours</strong> and';
			}
			if($sTimeDiff->i == 0 || $sTimeDiff->i == 1)	
				$strMessage .= ' <strong>1 minute</strong>,';
			else
				$strMessage .= ' <strong>'.$sTimeDiff->i.' minutes</strong>,';
			
			$strMessage .= ' '.$aModeDetails['mode_name'].' Mode has been Active.';
			
			if($extra['Pool_Temp'] == '1' && isset($extra['Pool_Temp']))
			{
				if(isset($extra['Pool_Temp_Address']) && $extra['Pool_Temp_Address'] != '' && $sResponse[$extra['Pool_Temp_Address']] != '')
					$strMessage.=' <strong>Pool temperature is '.$sResponse[$extra['Pool_Temp_Address']].'.</strong>';
			}
			
			if($extra['Spa_Temp'] == '1' && isset($extra['Spa_Temp']) && $sResponse[$extra['Spa_Temp_Address']] != '')
			{
				if(isset($extra['Spa_Temp_Address']) && $extra['Spa_Temp_Address'] != '')
					$strMessage.=' <strong>Spa temperature is '.$sResponse[$extra['Spa_Temp_Address']].'.</strong>';
			}
		}
		
		$response = array('message' => $strMessage);
		header('Content-type: application/json');		
		
		echo json_encode($response);
			
		//END: GET the active MODE details.
	}
	
	public function systemStatus() //START : Server response page of relay board.
    {
        $aViewParameter         	=   array(); // Array for passing parameter to view.
        $aViewParameter['page'] 	=   'status';
		$aViewParameter['Title'] 	=   'System Status Details';
        //Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();
		
		$this->load->model('home_model');
		//Get All IP Details.
		$aIPDetails = $this->home_model->getBoardIP();
		
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		foreach($aIPDetails as $IP)
		{
			$shhPort	=	'';
			if(IS_LOCAL == '1')
			{
				//Get SSH port of the RLB board using IP.
				$shhPort = $this->home_model->getSSHPortFromID($IP->id);
				
			}
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($IP->ip,$sPort,$shhPort);
			
			//Parameter for view
			$aViewParameter['response_'.$IP->id] =	$sResponse['response'];
       }
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		$aViewParameter['aIPDetails'] 		= $aIPDetails;
		
		/* echo '<pre>';
		print_r($aViewParameter);	
		echo '</pre>'; */
        //Status view for showing relay board status.
        $this->template->build('Status',$aViewParameter);
    } //END : Server response page of relay board.
	
	public function setting() //START : Function to show Setting Page or Device Type Page
    {
        $aViewParameter                 =   array(); // Array for passing parameter to view.
        $aViewParameter['page']         =   'home';
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
		
		//print_r($this->input->post());
		
        
        //Get the type of the device to show page.
        $sPage  =   $this->uri->segment('3'); 
		$sIpID	=	base64_decode($this->uri->segment('4'));
		
		$aViewParameter['BackToIP'] = $sIpID;

        $this->load->model('home_model');
        
        //Current mode of the system.
        $aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();

        if($sPage == '') // START : If no device type then show setting page.
        {
			$aViewParameter['Title']       =   'Settings';
            $aViewParameter['page']        =   'setting';
            
            if($this->input->post('command') == 'Save Setting') // START : IF Setting details are posted.
            {
                // Get mode value from POST.
                $this->load->model('home_model');
                
                // Get IP and PORT value from POST.
                $sIP     =   $this->input->post('relay_ip_address');
                $sPort   =   $this->input->post('relay_port_no');
				$sIP2 	 =   $this->input->post('relay_ip_address2');
				
				$sBoard1 = $this->input->post('relay_board_name1');
				$sBoard2 = $this->input->post('relay_board_name2');
				$sShh1	 =	'';
				$sShh2	 =	'';
				
				$secondIP= $this->input->post('secondIP');
				
				if(IS_LOCAL == '1') //IF Working on the Localhost.
				{
					$sShh1 = $this->input->post('relay_ip_address_ssh1');
					$sShh2 = $this->input->post('relay_ip_address_ssh2');
					$aIP	= 	array($sIP.'_1'=>array($sBoard1,$sShh1,'1'),$sIP2.'_2'=>array($sBoard2,$sShh2,$secondIP));
				}
				else
					$aIP	= 	array($sIP=>array($sBoard1,$sShh1,'1'),$sIP2=>array($sBoard2,$sShh2,$secondIP));
				
				//ADD IP in the Table
				$this->home_model->saveBoardIP($aIP);
				
				//Get the number of device for IP 1.
					$iNumPumps  =   $this->input->post('numPumps');
					$iNumValve  =   $this->input->post('numValve');
					$iNumLight  =   $this->input->post('numLight');
					
					$iNumHeater  =   $this->input->post('numHeater');
					$iNumBlower  =   $this->input->post('numBlower');
					$iNumMisc	 =   $this->input->post('numMisc'); 
				
				//Get the number of device for IP 2.
					$iNumPumps2   =   $this->input->post('numPumps2');
					$iNumValve2   =   $this->input->post('numValve2');
					$iNumLight2   =   $this->input->post('numLight2');
					
					$iNumHeater2  =   $this->input->post('numHeater2');
					$iNumBlower2  =   $this->input->post('numBlower2');
					$iNumMisc2	  =   $this->input->post('numMisc2'); 				
				
				$aNumDevice     =   array();
				$aNumDevice['PumpsNumber']	= ($iNumPumps != '') ? $iNumPumps : 0;
				$aNumDevice['ValveNumber']	= ($iNumValve != '') ? $iNumValve : 0;
				$aNumDevice['LightNumber']	= ($iNumLight != '') ? $iNumLight : 0;
				$aNumDevice['HeaterNumber'] = ($iNumHeater != '') ? $iNumHeater : 0;
				$aNumDevice['BlowerNumber'] = ($iNumBlower != '') ? $iNumBlower : 0;
				$aNumDevice['MiscNumber']	= ($iNumMisc != '') ? $iNumMisc : 0;
				$aNumDevice['SecondIP']		= $secondIP;
				
				if($iNumPumps == '' || $iNumPumps == 0)
				{
					$this->home_model->removeAllPumps($sIP,$sShh1);
					$arrPumps = array(0,1,2);
					foreach($arrPumps as $pump)
					{
						assignAddressToPump($pump,0,$sIP,$sPort,$sShh1);
					}
				}
				
				if($iNumValve == '' || $iNumValve == 0)
				{
					$this->home_model->removeAllValves($sIP,$sShh1);
					$response	=	assignValvesToRelay(0,$sIP,$sPort,$sShh1);
				}
				
				$showRemoteSpa 		 	= $this->input->post('showRemoteSpa');
				$showRemoteSpaDisplay	=	'0';
				if($showRemoteSpa == '1')
				$showRemoteSpaDisplay 	= $this->input->post('showRemoteSpaDisplay');
				
				$showRemoteSpa2 		= $this->input->post('showRemoteSpa2');
				$showRemoteSpaDisplay2	=	'0';
				if($showRemoteSpa2 == '1')
				$showRemoteSpaDisplay2 	= $this->input->post('showRemoteSpaDisplay2');
				
				
				$aNumDevice2     =   array();
				$aNumDevice2['PumpsNumber']	= ($iNumPumps2 != '') ? $iNumPumps2 : 0;
				$aNumDevice2['ValveNumber']	= ($iNumValve2 != '') ? $iNumValve2 : 0;
				$aNumDevice2['LightNumber']	= ($iNumLight2 != '') ? $iNumLight2 : 0;
				$aNumDevice2['HeaterNumber']= ($iNumHeater2 != '') ? $iNumHeater2 : 0;
				$aNumDevice2['BlowerNumber']= ($iNumBlower2 != '') ? $iNumBlower2 : 0;
				$aNumDevice2['MiscNumber']	= ($iNumMisc2 != '') ? $iNumMisc2 : 0;
				
				if($iNumPumps2 == '' || $iNumPumps2 == 0)
				{
					$this->home_model->removeAllPumps($sIP2,$sShh2);
					$arrPumps = array(0,1,2);
					foreach($arrPumps as $pump)
					{
						assignAddressToPump($pump,0,$sIP2,$sPort,$sShh2);
					}
				}
				
				if($iNumValve2 == '' || $iNumValve2 == 0)
				{
					$this->home_model->removeAllValves($sIP2,$sShh2);
					$response	=	assignValvesToRelay(0,$sIP2,$sPort,$sShh2)	;
				}
				
				//Save Number of Devices and Spa Remote
				$this->home_model->updateSettingNumberDevice($aNumDevice,$showRemoteSpa,$aNumDevice2,$showRemoteSpa2,$showRemoteSpaDisplay,$showRemoteSpaDisplay2);
				
				//Save IP and PORT
                    $this->home_model->updateSetting($sIP,$sPort);
					
				//Get Manual Mode Minutes
				$sManualModeTime   =   $this->input->post('manualMinutes');
				
				//Save Manual Mode Time.
				$this->home_model->updateManualModeTime($sManualModeTime);	
				
				$aViewParameter['sucess']    =   '1'; //Set success flag 1 if Saved details. 
            } // END : IF Setting details are posted. if($this->input->post('command') == 'Save Setting') 
            
            //START : Create Mode select Box to show on setting page. 
                $aModes =   $this->home_model->getAllModes(); //Get all available mode from DB.
                $sSelectModeOpt =   '<select name="relay_mode" id="relay_mode" class="form-control"><option value="0" >Please Select Mode</option>';
                foreach($aModes as $iMode)
                {
                    $sSelectModeOpt .= '<option value="'.$iMode->mode_id.'"';
                                    if($iMode->mode_status == '1'){
                                        $sSelectModeOpt .= ' selected="selected" ';
                                    }
                                    $sSelectModeOpt .= '>'.$iMode->mode_name.'</option>';
                }
                $sSelectModeOpt .= '<select>';
                $aViewParameter['sAllModes'] =$sSelectModeOpt;
            //END : Create Mode select Box to show on setting page.
            
            //Get saved IP and PORT 
            list($aViewParameter['sIP'],$aViewParameter['sPort'],$aViewParameter['extra']) = $this->home_model->getSettings();
			
			//GET IP and Board Name.
			$aViewParameter['aIPDetails'] = $this->home_model->getBoardIP();
            
			$aViewParameter['manualMinutes'] = $this->home_model->getManualModeTime();
			
			//Permission related parameters.
			$aViewParameter['userID'] 			= $this->userID;
			$aViewParameter['aModules'] 		= $this->aModules;
			$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
			
			//View Setting
            $this->template->build('Setting',$aViewParameter);
			
        } // END : If no device type then show setting page. if($sPage == '')
        else //START : If device type is available then device page.
        {
            //Check if IP, PORT and Mode is set or not.
            $this->checkSettingsSaved();
			
			//Get saved IP and PORT 
			list($aViewParameter['sIP'],$aViewParameter['sPort'],$aViewParameter['extra']) = $this->home_model->getSettings();
			
			//GET IP and Board Name.
			$aViewParameter['aIPDetails'] = $this->home_model->getBoardIP();
			
			
			//START : GET details for each IP.
			foreach($aViewParameter['aIPDetails'] as $IP)
			{
				$sResponse	= array();
				$shhPort	=	'';
				if(IS_LOCAL == '1')
				{
					//Get SSH port of the RLB board using IP.
					$shhPort = $this->home_model->getSSHPortFromID($IP->id);
				}
				
				//Get the status response of devices from relay board.
				$sResponse      =   get_rlb_status($IP->ip,$aViewParameter['sPort'],$shhPort);
				
				//Parameter for view
				$aViewParameter['response_'.$IP->id] =	$sResponse['response'];
				
				$sValves        =   $sResponse['valves']; // Valve Device Status
				$sRelays        =   $sResponse['relay'];  // Relay Device Status
				$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
				$sTime          =   $sResponse['time']; // Server time from Response
				
				// Pump Device Status
				$sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
				// Temperature Sensor Device 
				$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);

				//START : Parameter for View
				$aViewParameter['relay_count'.$IP->id]      =   strlen($sRelays);
				$aViewParameter['valve_count'.$IP->id]      =   strlen($sValves);
				$aViewParameter['power_count'.$IP->id]      =   strlen($sPowercenter);
				$aViewParameter['time'.$IP->id]             =   $sTime;
				$aViewParameter['pump_count'.$IP->id]       =   count($sPump);
				$aViewParameter['temprature_count'.$IP->id] =   count($sTemprature);

				$aViewParameter['sRelays'.$IP->id]          =   $sRelays; 
				$aViewParameter['sPowercenter'.$IP->id]     =   $sPowercenter;
				$aViewParameter['sValves'.$IP->id]          =   $sValves;
				$aViewParameter['sPump'.$IP->id]            =   $sPump;
				$aViewParameter['sTemprature'.$IP->id]      =   $sTemprature;
		    }
            //END : GET details for each IP.
			
			$aViewParameter['Title']       =   '';
			if($sPage == 'R')
				$aViewParameter['Title']       =   '24V AC Relays';
			if($sPage == 'V')
				$aViewParameter['Title']       =   'Valves';
			if($sPage == 'P')
				$aViewParameter['Title']       =   '12V DC Power Center Relays';
			if($sPage == 'PS')
				$aViewParameter['Title']       =   'Pumps';
			if($sPage == 'T')
				$aViewParameter['Title']       =   'Temperature Sensors';
			
			if($sPage == 'V' || $sPage == 'PS')
			{
				$aViewParameter['ValveRelays'.$IP->id]	=	$this->home_model->getAllValvesHavingRelays($IP->id);
				$aViewParameter['Pumps']		=	$this->home_model->getAllPumps();
			}
				
            $aViewParameter['sDevice']          =   $sPage;
            //END : Parameter for View
            
			//Permission related parameters.
			$aViewParameter['userID'] 			= $this->userID;
			$aViewParameter['aModules'] 		= $this->aModules;
			$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
			
			// Device View to show device page.
            $this->template->build('Device',$aViewParameter); 
        } //END : If device type is available then device page. Else for if($sPage == '')
    } //END : Function to show Setting Page or Device Type Page
	
	
	
	public function updateStatusOnOff() //START : Function to swich the particular device ON/OFF
    {
        //Load Model
		$this->load->model('home_model');
		
        //Get the Device details from POST whose status will be changed.
        $sName          =   $this->input->post('sName'); //Device Number
        $sStatus        =   $this->input->post('sStatus'); //Change status for Device
        $sDevice        =   $this->input->post('sDevice'); //Device Type
		$sDevicePort	=	$this->input->post('sPort');
		$sIdIP			=	$this->input->post('sIdIP');
	
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIdIP);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($sIdIP);
		}
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
        
        //$sResponse      =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves'];   // Valve Device Status
        $sRelays        =   $sResponse['relay'];    // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
        $sNewResp       =   '';
           
        //R = Relay, P = PowerCenter, V = Valve, PS = Pumps
        if($sDevice == 'R') // If Device type is Relay
        {
            $sNewResp = replace_return($sRelays, $sStatus, $sName );
            onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
            $this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
			
			list($pumpNumber,$relay1,$relay2)	=	$this->home_model->getPumpNumberFromRelayNumber($sName,'24');
			if($pumpNumber != '')
			{
				//Check if the relay is assigned to pump or not.
				$aPumpDetails =	$this->home_model->getPumpDetails($pumpNumber);
				if(!empty($aPumpDetails))
				{
					foreach($aPumpDetails as $sPump)
					{
						if($sStatus != '0')
						{
							if($relay1 == $sName)
								$sStatus = '1';
							if($relay2 == $sName)
								$sStatus = '2';
						}
						$this->makePumpOnOFF($sPump->pump_number,$sStatus,$sDeviceIP,$sDevicePort,$shhPort,$sIdIP);
					}
				}
			}
            
        }
        if($sDevice == 'P') // If Device type is Power Center
        {
            $sNewResp = replace_return($sPowercenter, $sStatus, $sName );
            onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
			
			list($pumpNumber,$relay1,$relay2)	=	$this->home_model->getPumpNumberFromRelayNumber($sName,'12');
			
			if($pumpNumber != '')
			{
				//Check if the relay is assigned to pump or not.
				$aPumpDetails =	$this->home_model->getPumpDetails($pumpNumber);
				if(!empty($aPumpDetails))
				{
					foreach($aPumpDetails as $sPump)
					{
						if($sStatus != '0')
						{
							if($relay1 == $sName)
								$sStatus = '1';
							if($relay2 == $sName)
								$sStatus = '2';
						}
						$this->makePumpOnOFF($sPump->pump_number,$sStatus,$sDeviceIP,$sDevicePort,$shhPort,$sIdIP);
					}
				}
			}
        }
        if($sDevice == 'V') // If Device type is Valve
        {
            $sNewResp = replace_return($sValves, $sStatus, $sName );
            onoff_rlb_valve($sNewResp,$sDeviceIP,$sPort,$shhPort);
        }
        if($sDevice == 'PS') // If Device type is Pump
        {
			$this->makePumpOnOFF($sName,$sStatus,$sDeviceIP,$sDevicePort,$shhPort,$sIdIP);
		}
        exit;
    } //END : Function to swich the particular device ON/OFF
	
	public function setPrograms() // START : Function to save/update/delete the Programs to run relay in Auto mode.
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Program 24V AC Relays';
        
        //Get Values From URL.
        $sDeviceID      =   base64_decode($this->uri->segment('3')); // Get Device ID
        $sProgramID     =   base64_decode($this->uri->segment('4')); // Get Program ID 
        $sProgramDelete =   $this->uri->segment('5');// Get value for delete

        $this->load->model('home_model');

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                redirect(site_url('home/setting/R'));
        }
       
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        
        if($this->input->post('command') == 'Save') // START : Save program details.
        {
            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber');

            $this->home_model->saveProgramDetails($this->input->post(),$sDeviceID,'R',str_replace('ip=','',$sProgramID));
            $aViewParameter['sucess']    =   '1';
        }// END : Save program details.

        if($this->input->post('command') == 'Update' && !preg_match('/ip=/',$sProgramID)) // START : Update program details.
        {
            if($sProgramID == '') //START : Check if Program id is blank then redirect
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '') // IF Program ID not present in POST redirect
                    redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
            }  

            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber'); 
			
            $this->home_model->updateProgramDetails($this->input->post(),$sProgramID,$sDeviceID,'R');
			
			$programDetails =	 $this->home_model->getProgramDetails($sProgramID);
			$ipID			=	'';
			foreach($programDetails as $row)
			{
				$ipID	=	$row->ip_id;
			}
			
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID).'/'.base64_encode('ip='.$ipID)));
        }// END : Update program details.

        if($sProgramDelete != '' && $sProgramDelete == 'D') // START : Delete program details.
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setting/R/'));
            }
			
			$programDetails =	 $this->home_model->getProgramDetails($sProgramID);
			$ipID			=	'';
			foreach($programDetails as $row)
			{
				$ipID	=	$row->ip_id;
			}
			
            $this->home_model->deleteProgramDetails($sProgramID);
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID).'/'.base64_encode('ip='.$ipID)));
        } // START : Delete program details.

        // Get saved program details     
        $aViewParameter['sProgramDetails'] = $this->home_model->getProgramDetailsForDevice($sDeviceID,'R',str_replace('ip=','',$sProgramID));

        if($sProgramID != '') //If program exists the get program details.
        {
            $aViewParameter['sProgramID'] = $sProgramID;
			if(!preg_match('/ip=/',$sProgramID))
				$aViewParameter['sProgramDetailsEdit'] = $this->home_model->getProgramDetails($sProgramID);
			else
				$aViewParameter['sProgramDetailsEdit'] = '';
        }
        else
        {
            $aViewParameter['sProgramID']          = ''; 
            $aViewParameter['sProgramDetailsEdit'] = '';
        }
        
		$aViewParameter['sDeviceTime'] =  $this->home_model->getDeviceTime($sDeviceID,'R');
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        //Program View for Getting and Showing Programs
        $this->template->build('Programs',$aViewParameter); 
    } // END : Function to save/update/delete the Programs to run relay in Auto mode.
	
	//START : Function to check if there is program exists for the selected time.
	public function checkProgramTimeAlreadyExist()
	{
		$sDeviceID   	=  $this->input->post('sDeviceID');
		$sDevice   	 	=  $this->input->post('sDevice');
		$sProgramType 	=  $this->input->post('sProgramType');
		if($sProgramType == '2')
			$sDays			=  json_decode($this->input->post('sDays'));
	    else
			$sDays			=  $this->input->post('sDays');
		
		$startTime		=  $this->input->post('startTime').':00';	
		$endTime		=  $this->input->post('endTime').':00';		
		
		$this->load->model('home_model');
		$sProgramDetails	=	$this->home_model->getProgramDetailsForDevice($sDeviceID,$sDevice);
		$alreadyExists		=	0;
		if(is_array($sProgramDetails) && !empty($sProgramDetails))
		{
			$cntDevicePrograms  = count($sProgramDetails);
			$aAllDays           = array( 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
			foreach($sProgramDetails as $aResult)
			{
				if($sProgramType == '1')
				{
					if($startTime >= $aResult->start_time && $startTime <= $aResult->end_time)
					{
						$alreadyExists = 1;
						break;
					}
					else if($endTime >= $aResult->start_time && $endTime <= $aResult->end_time)
					{
						$alreadyExists = 1;
						break;
					}
					else if($startTime < $aResult->start_time && $endTime > $aResult->end_time)
					{
						$alreadyExists = 1;
						break;
					}
				}
				else if($sProgramType == '2')
				{
					$checkDaysExists	=	0;
					$existDays = explode(',',$aResult->program_days);
					if(!empty($sDays))
					{
						foreach($sDays as $days)
						{
							if(in_array($days,$existDays))
							{
								$checkDaysExists = 1;
								break;
							}
						}
						
						if($checkDaysExists == 1)
						{
							if($startTime >= $aResult->start_time && $startTime <= $aResult->end_time)
							{
								$alreadyExists = 1;
								break;
							}
							else if($endTime >= $aResult->start_time && $endTime <= $aResult->end_time)
							{
								$alreadyExists = 1;
								break;
							}
							else if($startTime < $aResult->start_time && $endTime > $aResult->end_time)
							{
								$alreadyExists = 1;
								break;
							}
						}
					}
					
				}
			}
		}
		
		echo $alreadyExists;
		
	}//END : Function to check if there is program exists for the selected time.
	
	public function addTime() //START : Function to save/update the Relay Time.
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Program Max Time For 24V AC Relays';
        
        //Get Device ID and Device Type From URL.
        $sDeviceID  =   base64_decode($this->uri->segment('3'));
        $sDevice    =   base64_decode($this->uri->segment('4'));
		$sIpID    	=   base64_decode($this->uri->segment('5'));
       
        if($sDeviceID == '') // START : If Device ID is blank check whether Device ID is present in POST  
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        } // END : If Device ID is blank check whether Device ID is present in POST
        
        if($sDevice == '') // START : If Device type is blank check whether Device type is present in POST    
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '') // IF Device type not present in POST redirect to the Dashboard.
                redirect(site_url('home'));
        }
		
		if($sIpID == '') 
        {
            $sIpID  =   base64_decode($this->input->post('sIpID'));
            if($sIpID == '') // IF Device ID not present in POST redirect to the Device List
                if($sIpID != '')
                redirect(site_url('home/setting/'.$sDevice));
        } // END : If Device ID is blank check whether Device ID is present in POST
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;
		$aViewParameter['sIpID']      	=   $sIpID;
        
        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') //START : If device name form is POSTED.
        {
            // Get the device name From POST.
            $sDeviceTime = $this->input->post('sDeviceTime');
            //Save device name
            $this->home_model->saveDeviceTime($sDeviceID,$sDevice,$sDeviceTime,$sIpID);

            $aViewParameter['sucess']    =   '1';//Set success parameter.
        } //END : If device name form is POSTED.
        // Get the saved device name
        $aViewParameter['sDeviceTime']      =   $this->home_model->getDeviceTime($sDeviceID,$sDevice,$sIpID);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        // Time save/update View
        $this->template->build('Time',$aViewParameter); 
    }//END : Function to save/update the Relay Time.
	
	public function saveDeviceMainType()
	{
		//Get the input 
		$sDeviceID  =  $this->input->post('sDeviceID');
		$sDevice   	=  $this->input->post('sDevice');
		$sType 		=  $this->input->post('sType');
		$sIdIP   	=  $this->input->post('sIdIP');
		
		if($sDeviceID != '' && $sDevice != '' && $sType != '' && $sIdIP != '')
		{
			$this->load->model('home_model');
			$this->home_model->saveDeviceMainType($sDeviceID,$sDevice,$sType,$sIdIP);
		}
	}
	
	public function deviceName() // START : Function Show Device Name Form and Save
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Device Name';
        
        //Get Device ID and Device Type From URL.
        $sDeviceID  =   base64_decode($this->uri->segment('3'));
        $sDevice    =   base64_decode($this->uri->segment('4'));
		$sIPId    	=   base64_decode($this->uri->segment('5'));

        if($sDeviceID == '') // START : If Device ID is blank check whether Device ID is present in POST  
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        } // END : If Device ID is blank check whether Device ID is present in POST

        if($sDevice == '') // START : If Device type is blank check whether Device type is present in POST    
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '') // IF Device type not present in POST redirect to the Dashboard.
                redirect(site_url('home'));
        }
		
		if($sIPId == '') // START : If Device type is blank check whether Device type is present in POST    
        {
            $sIPId  =   base64_decode($this->input->post('sIPId'));
            if($sIPId == '') // IF Device type not present in POST redirect to the Dashboard.
                redirect(site_url('home'));
        }
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;
        $aViewParameter['sIPId']      	=   $sIPId;
		
		
		

        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') //START : If device name form is POSTED.
        {
            // Get the device name From POST.
            $sDeviceName = $this->input->post('sDeviceName');
            //Save device name
            $this->home_model->saveDeviceName($sDeviceID,$sDevice,$sDeviceName,$sIPId);

            $aViewParameter['sucess']    =   '1';//Set success parameter.
        } //END : If device name form is POSTED.
        // Get the saved device name
        $aViewParameter['sDeviceName']      =   $this->home_model->getDeviceName($sDeviceID,$sDevice,$sIPId);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        // Device Name save View
        $this->template->build('DeviceName',$aViewParameter); 
    } // END : Function Show Device Name Form and Save
    
	public function valveRelays()
	{
		$aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Position Details';
        $sDeviceID  =   base64_decode($this->uri->segment('3')); //Get Device ID to which position names will be saved.
        $sDevice    =   base64_decode($this->uri->segment('4')); //Get Device Type
        $sIpID    	=   base64_decode($this->uri->segment('5')); //Get IP of relayboard

		$this->load->model('home_model');	
        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page  
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        } //END : Check if Device id is blank then redirect to the device list page  

        if($sDevice == '') //START : Check if Device type is blank then redirect to home page
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '')// IF Device Type not present in POST redirect to the Dashboard
                redirect(site_url('home'));
        } //END : Check if Device type is blank then redirect to home page
		
		
		if($sIpID == '') 
        {
            $sIpID  =   base64_decode($this->input->post('sIpID'));
            if($sIpID == '')
                redirect(site_url('home'));
        } 
        
        if($this->input->post('command') == 'Save') // START: Save position names in DB for device.
        {
			
			if($this->input->post('sDeviceIDHID') != '')
			{
				$sDeviceIDOld = $sDeviceID;
				$sDeviceID  =   $this->input->post('sDeviceIDHID');
			}
			
			//Get position values from POST
            $sRelay1 = $this->input->post('sRelay1');
            $sRelay2 = $this->input->post('sRelay2');
			
			$positionRelay1	= $this->input->post('positionRelay1');
			$positionRelay2	= $this->input->post('positionRelay2');
			
            $this->home_model->saveValveRelays($sDeviceID,$sDeviceIDOld,$sDevice,$sRelay1,$sRelay2,$sIpID);
			
			$this->home_model->savePositionName($sDeviceID,$sDevice,$positionRelay1,$positionRelay2,$sIpID);
			
			$arrValves	=	$this->home_model->getAllValvesHavingRelays($sIpID);
			
			$strValves	=	'00000000';
			if(!empty($arrValves))
			{
				foreach($arrValves as $aValve)
				{
					$device_number = $aValve->device_number;
					$strValves[$device_number] = '1';
				}
			}
			$strValves;
			$hexNumber = dechex(bindec(strrev($strValves)));
			
			//GET IP of Device
			$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIpID);
			
			//Get saved IP and PORT 
			list($sIP,$sPort,$extra) = $this->home_model->getSettings();
			
			$shhPort	=	'';
			if(IS_LOCAL == '1')
			{
				//Get SSH port of the RLB board using IP.
				$shhPort = $this->home_model->getSSHPortFromID($sIpID);
			}
			
			$response	=	assignValvesToRelay($hexNumber,$sDeviceIP,$sPort,$shhPort)	;
	
			$aViewParameter['sucess']    =   '1'; // Set success flag 1
			redirect(base_url('home/setting/V/'));
        } // END : Save position names in DB for device.

		//Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;
		$aViewParameter['sIpID']      	=   $sIpID;
		
        //Get Existing saved position names for Device
        list($aViewParameter['sPositionName1'],$aViewParameter['sPositionName2'])      =   $this->home_model->getPositionName($sDeviceID,$sDevice,$sIpID);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
		//Get Valve Relay Number
		$aViewParameter['aRelayNumber']		= json_decode($this->home_model->getValveRelayNumber($sDeviceID,$sDevice,$sIpID));
		
		//Get all Positions
		$this->load->model('user_model');
		$aViewParameter['aAllPositions'] 	= $this->user_model->getAllPositions();
		
		$this->template->build('ValveRelays',$aViewParameter);
	}
	
	public function removeValveRelays()
	{
		$iValaveNumber	= $this->input->post('iValaveNumber');
		$ipID			= $this->input->post('ipID');
		
		$this->load->model('home_model');
		$this->home_model->removeValveRelays($iValaveNumber,$ipID);
			
		$arrValves	=	$this->home_model->getAllValvesHavingRelays($ipID);
		$strValves	=	'00000000';
		if(!empty($arrValves))
		{
			foreach($arrValves as $aValve)
			{
				$device_number = $aValve->device_number;
				$strValves[$device_number] = '1';
			}
		}
		
		$hexNumber = dechex(bindec(strrev($strValves)));
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($ipID);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($ipID);
		}
		
		echo $response	=	assignValvesToRelay($hexNumber,$sDeviceIP,$sPort,$shhPort)	;
		die;
	}
	
	
	public function pumpConfigure() // START : Function for saving Pump Configuration
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Pump Configuration';
		$aViewParameter['err']		 =	'';
		
        $sDeviceID  =   base64_decode($this->uri->segment('3'));
		$sIpID      =   base64_decode($this->uri->segment('4'));
       
        $this->load->model('home_model');

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page   
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') //START : Check if Device id is blank in POST then redirect to the device list page
                redirect(site_url('home/setting/PS/'));
        }

		if($sIpID == '') //START : Check if IP id is set or not.   
        {
            $sIpID  =   base64_decode($this->input->post('sIpID'));
            if($sIpID == '') //START : Check if Device id is blank in POST then redirect to the device list page
                redirect(site_url('home/setting/PS/'));
        }
		
        if($this->input->post('command') == 'Save') // START : Save pump configuration Details.
        {
            if($this->input->post('sPumpNumber') != '')
                $sDeviceID   =  $this->input->post('sPumpNumber');
			
			//GET IP of Device
			$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIpID);
			
			//Get saved IP and PORT 
			list($sIP,$sPort,$extra) = $this->home_model->getSettings();
			
			$shhPort	=	'';
			if(IS_LOCAL == '1')
			{
				//Get SSH port of the RLB board using IP.
				$shhPort = $this->home_model->getSSHPortFromID($sIpID);
			}
			
			//Make Pump OFF if the type selected is different from existing
				$sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
        
				$sRelays        =   $sResponse['relay'];    // Relay Device Status
				$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
				
				$sDevice = 'PS';
		
				$aPumpDetails = $this->home_model->getPumpDetails($sDeviceID,$sIpID);
				//Variable Initialization to blank.
				$sPumpNumber  	= '';
				$sPumpType  	= '';
				$sPumpSubType  	= '';
				$sPumpSpeed  	= '';
				$sPumpFlow 		= '';
				$sPumpClosure   = '';
				$sRelayNumber  	= '';
					
				if(is_array($aPumpDetails) && !empty($aPumpDetails))
				{
				  foreach($aPumpDetails as $aResultEdit)
				  { 
					$sPumpNumber  = $aResultEdit->pump_number;
					$sPumpType    = $aResultEdit->pump_type;
					$sPumpSubType = $aResultEdit->pump_sub_type;
					$sPumpSpeed   = $aResultEdit->pump_speed;
					$sPumpFlow    = $aResultEdit->pump_flow;
					$sPumpClosure = $aResultEdit->pump_closure;
					$sRelayNumber = $aResultEdit->relay_number;
				  }
				}
				$sStatus	=	0;
				if($sPumpType != '' && $sPumpType != $this->input->post('sPumpType'))
				{
					if($sPumpType == '12' || $sPumpType == '24')
					{
						if($sPumpType == '24')
						{
							$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
							$this->home_model->updateDeviceRunTime($sDeviceID,$sDevice,$sStatus);
						}
						else if($sPumpType == '12')
						{
							$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
						}
					}
					else
					{
						if(preg_match('/Emulator/',$sPumpType))
						{
							$sNewResp = '';
							$sNewResp =  $sDeviceID.' '.$sStatus;
							
							onoff_rlb_pump($sNewResp,$sDeviceIP,$sPort,$shhPort);
							
							if($sPumpType == 'Emulator12')
							{
								$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp12,$sDeviceIP,$sPort,$shhPort);
							}
							if($sPumpType == 'Emulator24')
							{
								$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp24,$sDeviceIP,$sPort,$shhPort);
								$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus);
							}
						}
						else if(preg_match('/Intellicom/',$sPumpType))
						{
							$sNewResp = '';
							$sNewResp =  $sDeviceID.' '.$sStatus;
							onoff_rlb_pump($sNewResp,$sDeviceIP,$sPort,$shhPort);
							
							if($sPumpType == 'Intellicom12')
							{
								$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp12,$sDeviceIP,$sPort,$shhPort);
							}
							if($sPumpType == 'Intellicom24')
							{
								$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp24,$sDeviceIP,$sPort,$shhPort);
								$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus);
							}
						}
					}
				}
			
            //Change the address on the Raspberry Device
			$sPumpType      =   $this->input->post('sPumpType');
			if($sPumpType != '12' && $sPumpType != '24' && $sPumpType != '2Speed')
			{	
				$sAddress 	=	$this->input->post('sPumpAddress');
				$arrAddress	=	array();
				for($i=0;$i<3;$i++)
				{
					$sRes 		= getAddressToPump($i,$sDeviceIP,$sPort,$shhPort);
					if(!preg_match('/Invalid response/',$sRes))
					{
						$aResult		=	explode(',',$sRes);
						$arrAddress[]	=	trim($aResult[2]);
					}
					else
					{
						$aViewParameter['err']    =   'Following error occurs. '.$sRes;
					}
				}
				
				if(!in_array($sAddress,$arrAddress))
				{
					$sResult	=	assignAddressToPump($sDeviceID,$sAddress,$sDeviceIP,$sPort,$shhPort);
					if(!preg_match('/Invalid response/',$sRes))
					{}
					else
					{
						$aViewParameter['err']    =   'Following error occurs. '.$sResult;
					}
				}
				else
				{
					$aViewParameter['err']    =   'Following error occurs : Address already used by another device';
				}
			}
			
			if($aViewParameter['err'] == '')
			{
				$this->home_model->savePumpDetails($this->input->post(),$sDeviceID,$sIpID);
				$aViewParameter['sucess']    =   '1';
			}
			
			
        }// END : Save pump configuration Details.
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
		$aViewParameter['sIpID']    =   $sIpID;
        $aViewParameter['sPumpDetails'] = 	$this->home_model->getPumpDetails($sDeviceID,$sIpID);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        //Pump view for configuration of pumps
        $this->template->build('Pump',$aViewParameter); 
    } // END : Function for saving Pump Configuration
	
	
	public function setProgramsPump() // START : Function to save/update/delete the Programs to run relay in Auto mode.
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Program For Pumps';
        
        //Get Values From URL.
        $sDeviceID      =   base64_decode($this->uri->segment('3')); // Get Device ID
        $sProgramID     =   base64_decode($this->uri->segment('4')); // Get Program ID 
        $sProgramDelete =   $this->uri->segment('5');// Get value for delete

        $this->load->model('home_model');

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                redirect(site_url('home/setting/PS'));
        }
       
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        
        if($this->input->post('command') == 'Save') // START : Save program details.
        {
            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber');

            $this->home_model->saveProgramDetails($this->input->post(),$sDeviceID,'PS',str_replace('ip=','',$sProgramID));
            $aViewParameter['sucess']    =   '1';
        }// END : Save program details.

        if($this->input->post('command') == 'Update' && !preg_match('/ip=/',$sProgramID)) // START : Update program details.
        {
            if($sProgramID == '') //START : Check if Program id is blank then redirect
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '') // IF Program ID not present in POST redirect
                    redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
            }  

            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber'); 

            $this->home_model->updateProgramDetails($this->input->post(),$sProgramID,$sDeviceID,'PS');
			
			$programDetails =	 $this->home_model->getProgramDetails($sProgramID);
			$ipID			=	'';
			foreach($programDetails as $row)
			{
				$ipID	=	$row->ip_id;
			}
			
            redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID).'/'.base64_encode('ip='.$ipID)));
        }// END : Update program details.

        if($sProgramDelete != '' && $sProgramDelete == 'D') // START : Delete program details.
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setting/PS/'));
            }
			
			$programDetails =	 $this->home_model->getProgramDetails($sProgramID);
			$ipID			=	'';
			foreach($programDetails as $row)
			{
				$ipID	=	$row->ip_id;
			}
			
            $this->home_model->deleteProgramDetails($sProgramID);
            redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID).'/'.base64_encode('ip='.$ipID)));
        } // START : Delete program details.

        // Get saved program details     
        $aViewParameter['sProgramDetails'] = $this->home_model->getProgramDetailsForDevice($sDeviceID,'PS',str_replace('ip=','',$sProgramID));

        if($sProgramID != '') //If program exists the get program details.
        {
            $aViewParameter['sProgramID'] = $sProgramID;
			if(!preg_match('/ip=/',$sProgramID))
				$aViewParameter['sProgramDetailsEdit'] = $this->home_model->getProgramDetails($sProgramID);
			else 
				$aViewParameter['sProgramDetailsEdit'] = '';
        }
        else
        {
            $aViewParameter['sProgramID']          = ''; 
            $aViewParameter['sProgramDetailsEdit'] = '';
        }
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        //Program View for Getting and Showing Programs
        $this->template->build('PumpPrograms',$aViewParameter); 
    } // END : Function to save/update/delete the Programs to run relay in Auto mode.
	
	public function makePumpOnOFF($sName,$sStatus,$sIP,$sDevicePort,$sShh,$sIdIP)
	{
		
		$sResponse      =   get_rlb_status($sIP,$sDevicePort,$sShh);
        
        $sRelays        =   $sResponse['relay'];    // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
		$sDevice = 'PS';
		
		if($sDevice == 'PS') // If Device type is Pump
        {
			$aPumpDetails = $this->home_model->getPumpDetails($sName,$sIdIP);
			//Variable Initialization to blank.
			$sPumpNumber  	= '';
			$sPumpType  	= '';
			$sPumpSubType  	= '';
			$sPumpSpeed  	= '';
			$sPumpFlow 		= '';
			$sPumpClosure   = '';
			$sRelayNumber  	= '';
			$sRelayNumber1  = '';

			if(is_array($aPumpDetails) && !empty($aPumpDetails))
			{
			  foreach($aPumpDetails as $aResultEdit)
			  { 
				$sPumpNumber  = $aResultEdit->pump_number;
				$sPumpType    = $aResultEdit->pump_type;
				$sPumpSubType = $aResultEdit->pump_sub_type;
				$sPumpSpeed   = $aResultEdit->pump_speed;
				$sPumpFlow    = $aResultEdit->pump_flow;
				$sPumpClosure = $aResultEdit->pump_closure;
				$sRelayNumber = $aResultEdit->relay_number;
				$sRelayNumber1 = $aResultEdit->relay_number_1;
			  }
			}
			
			if($sPumpType != '' && $sPumpClosure == '1')
			{
				if($sPumpType == '12' || $sPumpType == '24' || $sPumpType == '2Speed')
				{
					if($sPumpType == '24')
					{
						$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
						onoff_rlb_relay($sNewResp,$sIP,$sDevicePort,$sShh);
						$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
						
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus,$sIdIP);
					}
					else if($sPumpType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp,$sIP,$sDevicePort,$sShh);
						
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus,$sIdIP);
					}
					if($sPumpType == '2Speed')
					{
						if($sPumpSubType == '24')
						{
							if($sStatus == '0')
							{
								$sNewResp = replace_return($sRelays, 0, $sRelayNumber );
								onoff_rlb_relay($sNewResp,$sIP,$sDevicePort,$sShh);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_relay($sNewResp,$sIP,$sDevicePort,$sShh);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
							}
							if($sStatus == '1')
							{
								$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp,$sIP,$sDevicePort,$sShh);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_relay($sNewResp,$sIP,$sDevicePort,$sShh);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
							}
							if($sStatus == '2')
							{	
								$sStatus = '1';
								$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber1 );
								onoff_rlb_relay($sNewResp,$sIP,$sDevicePort,$sShh);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber );
								onoff_rlb_relay($sNewResp,$sIP,$sDevicePort,$sShh);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
							}
							
							$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus,$sIdIP);
							
						}
						else if($sPumpSubType == '12')
						{
							if($sStatus == '0')
							{
								$sNewResp = replace_return($sPowercenter, '0', $sRelayNumber );
								onoff_rlb_powercenter($sNewResp,$sIP,$sDevicePort,$sShh);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_powercenter($sNewResp,$sIP,$sDevicePort,$sShh);
							}
							if($sStatus == '1')
							{
								$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp,$sIP,$sDevicePort,$sShh);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_powercenter($sNewResp,$sIP,$sDevicePort,$sShh);
							}
							if($sStatus == '2')
							{	
								$sStatus = '1';
								$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber1 );
								onoff_rlb_powercenter($sNewResp,$sIP,$sDevicePort,$sShh);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber );
								onoff_rlb_powercenter($sNewResp,$sIP,$sDevicePort,$sShh);
								
								$sStatus = '2';
							}
							
							$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus,$sIdIP);
							
						}
							
						
					}
				}
				else
				{
					if(preg_match('/Emulator/',$sPumpType))
					{
						$sNewResp = '';

						if($sStatus == '0')
							$sNewResp =  $sName.' '.$sStatus;
						else if($sStatus == '1')
						{
							$sType          =   '';
							if($sPumpSubType == 'VS')
								$sType  =   '2'.' '.$sPumpSpeed;
							elseif ($sPumpSubType == 'VF')
								$sType  =   '3'.' '.$sPumpFlow;

							$sNewResp =  $sName.' '.$sType;    
						}
						
						onoff_rlb_pump($sNewResp,$sIP,$sDevicePort,$sShh);
						
						if($sPumpType == 'Emulator12')
						{
							$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp12,$sIP,$sDevicePort,$sShh);
						}
						if($sPumpType == 'Emulator24')
						{
							$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp24,$sIP,$sDevicePort,$sShh);
							$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus,$sIdIP);
						}
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus,$sIdIP);
						
					}
					else if(preg_match('/Intellicom/',$sPumpType))
					{
						$sNewResp = '';

						if($sStatus == '0')
							$sNewResp =  $sName.' '.$sStatus;
						else if($sStatus == '1')
						{
							$sType  =   '2'.' '.$sPumpSpeed;
							$sNewResp =  $sName.' '.$sType;    
						}
						
						onoff_rlb_pump($sNewResp,$sIP,$sDevicePort,$sShh);
						
						if($sPumpType == 'Intellicom12')
						{
							$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp12,$sIP,$sDevicePort,$sShh);
						}
						if($sPumpType == 'Intellicom24')
						{
							$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp24,$sIP,$sDevicePort,$sShh);
							$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus,$sIdIP);
						}
						
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus,$sIdIP);
					}
				}
				
				//Update details of program to which pump is related.
				if($sStatus == 0)
				{
					$aProgramDetails	=	$this->home_model->getProgramDetailsForDevice($sName,$sDevice,$sIdIP);
					
					foreach($aProgramDetails as $Program)
					{
						if($Program->program_active == '1')
						{
							$this->home_model->updateProgramStatus($Program->program_id, 0);
							if($Program->program_absolute == '1')
							{
								$aAbsoluteDetails   = array(
								'absolute_s'  => $Program->program_absolute_start_time,             'absolute_e'  => $Program->program_absolute_end_time,
								'absolute_t'  => $Program->program_absolute_total_time,
								'absolute_ar' => $Program->program_absolute_run_time,
								'absolute_sd' => $Program->program_absolute_start_date,
								'absolute_st' => $Program->program_absolute_run);
															
								$this->home_model->updateAlreadyRunTime($Program->program_id, $aAbsoluteDetails);
								
							}
						}
					}
				}
				//$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
			}				
        }
	}
	
	public function getLogDetails()
	{
		$aViewParameter['Title'] = 'Log Details';
		//$dir    	= '/var/log/rlb/';
		$dir    	= "D:\wamp\www\CodeIgniter-pool-spa\log\\rlb\\";
		
		$sFileName				=	'';
		$strTodaysLogDetails 	=	'';
		$strDate				=	'';
		
		$strStartDate			=	'';			
		$strEndDate				=	'';	
		
		$this->load->model('home_model');
		//GET IP and Board Name.
		$aViewParameter['aIPDetails'] = $this->home_model->getBoardIP();
		
		foreach($aViewParameter['aIPDetails'] as $IP)
		{
			$shhPort	=	'';
			if(IS_LOCAL == '1')
			{
				//Get SSH port of the RLB board using IP.
				$shhPort = $this->home_model->getSSHPortFromID($IP->id);
			}
			
			$connection = ssh2_connect($IP->ip, $shhPort);
			ssh2_auth_password($connection, 'pi', 'lucky777');

			$sftp = ssh2_sftp($connection);

			$stream = fopen("ssh2.sftp://$sftp/var/log/rlb/160120.log", 'r');
			
			while(!feof($stream)){
				$line = fgets($stream);
				echo $line.'<br>';
				# do same stuff with the $line
			}
			
			echo '<br><strong>==========================================</strong><br>';
			
		}
		die('STOP');
		if($this->input->post('searchLog') == 'Search')
		{
			$sFromDate	=	$this->input->post('sFromDate');
			$sToDate	=	$this->input->post('sToDate');
			
			$sFromDate 	=	strtotime($sFromDate);
			$sToDate 	=	strtotime($sToDate);
			
			$strStartDate	=	date('Y-m-d', $sFromDate);			
			$strEndDate		=	date('Y-m-d', $sToDate);
			
			$strDate 	= $strStartDate.' - '.$strEndDate;
				
			//echo $sFromDate.'>>'.$sToDate;
			
			$allfiles   = scandir($dir);
			foreach($allfiles as $sFileName)
			{
				if (preg_match('/.log/',$sFileName))
				{
					$arrFileName	=	explode('.',$sFileName);
					$counter = 0;
					$sFileDate       =   $arrFileName[0];
					$sDateCreated  	 =	'';
					
					for($i = 0; $i < strlen($sFileDate); $i++)
					{
						if($counter == 2)
						{
							$counter = 0;
							$sDateCreated .= "-".$sFileDate[$i];
						}
						else
						{
							$sDateCreated .= $sFileDate[$i];
						}
						
						$counter++;
					}
					
					$sDateCreatedTime = strtotime($sDateCreated);
					
					if( $sFromDate <= $sDateCreatedTime && $sToDate >= $sDateCreatedTime)
					{
						$strTodaysLogDetails .= '<strong>'.$sDateCreated.'</strong><br><br>';
						
						$sFileName	=	str_replace('-','',$sDateCreated);
						$file = fopen($dir.$sFileName.'.log','r');	
						while(!feof($file)){
							$line = fgets($file);
							$strTodaysLogDetails .= str_replace($sFileName,'',$line).'<br>';
							# do same stuff with the $line
						}
						$strTodaysLogDetails .= '<hr>';
						fclose($file);
					}
					
					//echo $sFileName.'<br>';
				}
					
			}
		}
		else
		{
			$sFileName	=	date('ymd');
			
			$strStartDate	=	date('Y-m-d');			
			$strEndDate		=	date('Y-m-d');
			
			//$file = fopen($dir.$sFileName.'.log','r');
			$file = fopen($dir.'150824.log','r');			
			
			$strDate	=	'';
			if ($file) 
			{
				$strDate = date('Y-m-d');
				while(!feof($file)){
					$line = fgets($file);
					$strTodaysLogDetails .= str_replace($sFileName,'',$line).'<br>';
					# do same stuff with the $line
				}
			}
			else
			{
				$strStartDate	=	date('Y-m-d',strtotime('-1 day'));			
				$strEndDate		=	date('Y-m-d',strtotime('-1 day'));
				$sFileName	=	date('ymd', strtotime('-1 day'));
				$strDate = date('Y-m-d', strtotime('-1 day'));
				$file = fopen($dir.$sFileName.'.log','r');	
				while(!feof($file)){
					$line = fgets($file);
					$strTodaysLogDetails .= str_replace($sFileName,'',$line).'<br>';
					# do same stuff with the $line
				}
			}
			fclose($file);
		}
		
		$aViewParameter['Log']			= $strTodaysLogDetails;
		$aViewParameter['sDate']		= $strDate;
		$aViewParameter['sStartDate']	= $strStartDate;
		$aViewParameter['sEndDate']		= $strEndDate;
		$aViewParameter['page']			= 'log';
		//Status view for showing relay board status.
        $this->template->build('Log',$aViewParameter);
	}
	
	public function checkAddressPump()
	{
		$sDeviceID 		=	$this->input->post('sDeviceID');
		$sPumpAddress	=	$this->input->post('sPumpAddress');
		
		$aResponse		=	array('iAddressCheck'=>0,'iPumpID'=>0);
		
		$this->load->model('home_model');
		$aPumpDetails = $this->home_model->getPumpDetailsExcept($sDeviceID);
		//print_r($aPumpDetails);
		if(!empty($aPumpDetails))
		{
			foreach($aPumpDetails as $sPump)
			{
				if($sPumpAddress == $sPump->pump_address)
				{
					$aResponse['iAddressCheck']	=	1;
					$aResponse['iPumpID']		=	$sPump->pump_number;
					break;
				}	
			}
		}
		echo json_encode($aResponse);
		
		exit;
		
	}
	
	public function checkRelayNumber()
	{
		$sDeviceID 		=	$this->input->post('sDeviceID');
		$sRelayNumber	=	$this->input->post('sRelayNumber');
		$sPumpType		=	$this->input->post('type');
		$sPumpTypeChk	=	'';	
		
		$aResponse		=	array('iPumpCheck'=>0,'iPumpID'=>0);
		
		//Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
        if(preg_match('/12/',$sPumpType))
		{
			$sPumpTypeChk = 12;
			if($sRelayNumber > 7)
				$aResponse['iPumpCheck'] = 1;
			
				
		}
		else if(preg_match('/24/',$sPumpType))
		{
			$sPumpTypeChk = 24;
			if($sRelayNumber > 15)
				$aResponse['iPumpCheck'] = 1;
			else if($sRelays[$sRelayNumber] == '.')
				$aResponse['iPumpCheck'] = 2;
		}
		
		$this->load->model('home_model');
		
		if($aResponse['iPumpCheck'] != 1 || $aResponse['iPumpCheck'] != 2)
		{
			
			$aPumpDetails = $this->home_model->getPumpDetailsExcept($sDeviceID);
			//print_r($aPumpDetails);
			if(!empty($aPumpDetails))
			{
				foreach($aPumpDetails as $sPump)
				{
					if($sRelayNumber == $sPump->relay_number && preg_match('/'.$sPumpTypeChk.'/',$sPump->pump_type))
					{
						$aResponse['iPumpCheck']	=	1;
						$aResponse['iPumpID']		=	$sPump->pump_number;
						break;
					}	
				}
			}
		}
		
		if($aResponse['iPumpCheck'] != 1 || $aResponse['iPumpCheck'] != 2)
		{
			//START: Get all Light Devices with relays and of particular type.
			$arrLights	=	json_decode($this->home_model->getAllLightDeviceForType($sPumpTypeChk));
			if(!empty($arrLights))
			{
				foreach($arrLights as $aLight)
				{
					$sRelayDetails	= unserialize($aLight->light_relay_number);
					
					if($sRelayNumber == $sRelayDetails['sRelayNumber'])
					{
						$aResponse['iPumpCheck']	=	1;
						$aResponse['iPumpID']		=	'';
						break;
					}
				}
			}
		}
		
		echo json_encode($aResponse);
		
		exit;
	}
	
	
	public function SpaDevice()
	{
		$aViewParameter				=   array(); // Array for passing parameter to view.
		$aViewParameter['page']     =   'home';
		$aViewParameter['sucess']   =   '0';
		$aViewParameter['Title']    =   'Spa Devices';
        $aViewParameter['err_sucess']	=   '0';
		
		//Check if IP, PORT and Mode is set or not.
		$this->checkSettingsSaved();
		
		$this->load->model('home_model');
		
		//Current mode of the system.
                $aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
		
		//Get the status response of devices from relay board.
		$sResponse      =   get_rlb_status();
		//$sResponse      =   array('valves'=>'','powercenter'=>'0000','time'=>'','relay'=>'0000');
		
		$sValves        =   $sResponse['valves']; // Valve Device Status
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
		$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		$sTime          =   $sResponse['time']; // Server time from Response
		
		// Pump Device Status
		$sPump	=	array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		// Temperature Sensor Device 
		
		$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);

		//START : Parameter for View
			$aViewParameter['relay_count']      =   strlen($sRelays);
			$aViewParameter['valve_count']      =   strlen($sValves);
			$aViewParameter['power_count']      =   strlen($sPowercenter);
			$aViewParameter['time']             =   $sTime;
			$aViewParameter['pump_count']       =   count($sPump);
			$aViewParameter['temprature_count'] =   count($sTemprature);

			$aViewParameter['sRelays']          =   $sRelays; 
			$aViewParameter['sPowercenter']     =   $sPowercenter;
			$aViewParameter['sValves']          =   $sValves;
			$aViewParameter['sPump']            =   $sPump;
			$aViewParameter['sTemprature']      =   $sTemprature;

			//$aViewParameter['sDevice']          =   $sPage;
		//END : Parameter for View
		
		/* for ($i=0;$i < $relay_count; $i++)
        {
			$sMainType =	$this->home_model->getDeviceMainType($i,$sDevice);
		} */
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
                
        //Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$aViewParameter['extra']) = $this->home_model->getSettings();
		
		// Device View to show device page.
		$this->template->build('SpaDevice',$aViewParameter);
	}
	
	public function PoolDevice()
	{
		$aViewParameter                 =   array(); // Array for passing parameter to view.
                $aViewParameter['page']         =   'home';
                $aViewParameter['sucess']       =   '0';
                $aViewParameter['err_sucess']   =   '0';
		$aViewParameter['Title']        =   'Pool Devices';
		//Check if IP, PORT and Mode is set or not.
		$this->checkSettingsSaved();
		
		$this->load->model('home_model');
		
		//Current mode of the system.
                 $aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
		
		//Get the status response of devices from relay board.
		$sResponse      =   get_rlb_status();
		//$sResponse      =   array('valves'=>'','powercenter'=>'0000','time'=>'','relay'=>'0000');
		
		$sValves        =   $sResponse['valves']; // Valve Device Status
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
		$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		$sTime          =   $sResponse['time']; // Server time from Response
		
		// Pump Device Status
		$sPump	=	array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		// Temperature Sensor Device 
		
		$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);

		//START : Parameter for View
			$aViewParameter['relay_count']      =   strlen($sRelays);
			$aViewParameter['valve_count']      =   strlen($sValves);
			$aViewParameter['power_count']      =   strlen($sPowercenter);
			$aViewParameter['time']             =   $sTime;
			$aViewParameter['pump_count']       =   count($sPump);
			$aViewParameter['temprature_count'] =   count($sTemprature);

			$aViewParameter['sRelays']          =   $sRelays; 
			$aViewParameter['sPowercenter']     =   $sPowercenter;
			$aViewParameter['sValves']          =   $sValves;
			$aViewParameter['sPump']            =   $sPump;
			$aViewParameter['sTemprature']      =   $sTemprature;

			//$aViewParameter['sDevice']          =   $sPage;
		//END : Parameter for View
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$aViewParameter['extra']) = $this->home_model->getSettings();
		
		// Device View to show device page.
		$this->template->build('PoolDevice',$aViewParameter);
	}
	
	
	public function PoolSpaSetting()
	{
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
		$this->load->model('home_model');
		if($this->input->post('command') && $this->input->post('command') == 'save')
		{
			//All General Question 
			$arrGeneral			=	array();
			
			$arrGeneral['type']				=	trim($this->input->post('strType'));
			//$arrGeneral['equip']			=	trim($this->input->post('strEquipment'));
			$arrGeneral['pool_max_temp']	=	trim($this->input->post('pool_maximum_temperature'));
			$arrGeneral['pool_temp']		=	trim($this->input->post('pool_temperature'));
			$arrGeneral['pool_manual']		=	trim($this->input->post('pool_manual'));
			
			if(isset($_POST['display_pool_temp']))
				$arrGeneral['display_pool_temp']=	trim($this->input->post('display_pool_temp'));
			else
				$arrGeneral['display_pool_temp']=	'';
			
			if(isset($_POST['display_spa_temp']))
				$arrGeneral['display_spa_temp']	=	trim($this->input->post('display_spa_temp'));
			else
				$arrGeneral['display_spa_temp']	= '';
		
			$arrGeneral['spa_max_temp']		=	trim($this->input->post('spa_maximum_temperature'));
			$arrGeneral['spa_temperature']	=	trim($this->input->post('spa_temperature'));
			$arrGeneral['spa_manual']		=	trim($this->input->post('spa_manual'));
			$arrGeneral['temperature1']		=	trim($this->input->post('temperature1'));
			$arrGeneral['temperature2']		=	trim($this->input->post('temperature2'));
			
			//All Device
			$arrDevice						=	array();
			$arrDevice['valve'] 			= 	trim($this->input->post('strValve'));
			$arrDevice['valve_actuated'] 	= 	$this->input->post('valve_actuated');
			$arrDevice['reasonValve']		=	trim($this->input->post('reasonValve'));
			$arrDevice['valveRunTime'] 		= 	trim($this->input->post('valveRunTime'));
			$arrDevice['valveAssign']		=	trim(serialize($this->input->post('relayValve')));
			
			$arrDevice['pump']				=	trim($this->input->post('automatic_pumps'));
			for($i=1;$i<=$arrDevice['pump'];$i++)
			{
				$arrDevice['pump'.$i] 			= 	trim($this->input->post('Pump'.$i));
			}
			$arrDevice['pumpAssign']			=	trim(serialize($this->input->post('relayPumpchk')));
			
			//Heater Questions
			$arrHeater							=	array();
			$arrHeater['heater']				=	trim($this->input->post('automatic_heaters_question1'));
			
			for($i=0;$i<=$arrHeater['heater'];$i++)
			{
				$arrHeater['heater'.$i.'_equip']	=	trim($this->input->post('heater'.$i.'_equiment'));
				$arrHeater['Heater'.$i]				=	trim($this->input->post('Heater'.$i));
				$arrHeater['HeaterPump'.$i]			=	trim($this->input->post('HeaterPump'.$i));
			}
			$arrHeater['heaterAssign']	=	trim(serialize($this->input->post('relayHeater')));
						
			//More devices
			$arrMore				=	array();
			$arrMore['light']		=	trim($this->input->post('no_light'));
			$arrMore['blower']		=	trim($this->input->post('no_blower'));
			$arrMore['misc']		=	trim($this->input->post('no_misc'));
			
			$arrMore['lightAssign']	=	trim(serialize($this->input->post('relayLight')));
			$arrMore['blowerAssign']=	trim(serialize($this->input->post('relayBlower')));
			$arrMore['miscAssign']	=	trim(serialize($this->input->post('relayMisc')));
			
			$arrDetails	=	array('General'=>$arrGeneral,'Device'=>$arrDevice,'Heater'=>$arrHeater,'More'=>$arrMore);
			
			$this->home_model->savePoolSpaModeQuestions($arrDetails);
			
			//$this->session->set_flashdata('msg_save', 'Details are saved successfully!');
			$aViewParameter['saveMsg']	=	'Details saved successfully!';
			
		}
		
		$aViewParameter['arrDetails']	 =	$this->home_model->getPoolSpaModeQuestions();
		
		
		//Get All IP Details.
		$aViewParameter['aIPDetails'] = $this->home_model->getBoardIP();
		$this->load->model('analog_model');
		
		//list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$aViewParameter['extra']) = $this->home_model->getSettings();
		
		foreach($aViewParameter['aIPDetails'] as $IP)
		{
			$shhPort	=	'';
			if(IS_LOCAL == '1')
			{
				//Get SSH port of the RLB board using IP.
				$shhPort = $this->home_model->getSSHPortFromID($IP->id);
			}
			$sResponse		=	array();
			$sValves        =   ''; 
			$sRelays        =   '';  
			$sPowercenter   =   ''; 
			$sTime          =   '';
			$sPump			=	'';	
			$sTemprature	=	'';
		
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($IP->ip,$aViewParameter['sPort'],$shhPort);
			
			$aViewParameter['sValves'.$IP->id]       =   $sResponse['valves']; // Valve Device Status
					
			$iPowerCnt = strlen($sResponse['powercenter']);
			$iRelayCnt = strlen($sResponse['relay']);
			
			$available12VRelays	=array();
			$available24VRelays	=array();
			
			//Check 12V relays First
			for($i=0;$i<$iPowerCnt;$i++)
			{
				//$iCheck			=	$this->checkRelayNumbers($i,'12');
				
				//if(!$iCheck)
				{
					$available12VRelays[] = $i;
				}
			}
			
			//Check 24V relays First
			for($i=0;$i<$iRelayCnt;$i++)
			{
				if($sResponse['relay'][$i] != '.')
				{
					//$iCheck			=	$this->checkRelayNumbers($i,'24');
					
					//if(!$iCheck)
					{
						$available24VRelays[] = $i;
					}
				}
			}
			
			$aViewParameter['sRelays'.$IP->id]       =   $available24VRelays;
			$aViewParameter['sPowercenter'.$IP->id]  =   $available12VRelays;
			
			//Pump device Status
			$aViewParameter['sPump'.$IP->id]         =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
			
			// Temperature Sensor Device 
			$aViewParameter['sTemprature'.$IP->id]   =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);
			
			$aViewParameter['ValveRelays'.$IP->id]	=	$this->home_model->getAllValvesHavingRelays($IP->id);
			$aViewParameter['Pumps']		=	$this->home_model->getAllPumps($IP->id);
			
			if($aViewParameter['ValveRelays'.$IP->id] == '')
				$aViewParameter['ValveRelays'.$IP->id] = array();
			if($aViewParameter['Pumps'.$IP->id] == '')
				$aViewParameter['Pumps'.$IP->id] = array();
		
		}
				
		$this->template->build('PoolSpaSetting',$aViewParameter);
	}
	
	
	public function positionName() //START : Function to save position names for Valve 
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Position Details';
        $sDeviceID  =   base64_decode($this->uri->segment('3')); //Get Device ID to which position names will be saved.
        $sDevice    =   base64_decode($this->uri->segment('4')); //Get Device Type

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page  
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        } //END : Check if Device id is blank then redirect to the device list page  

        if($sDevice == '') //START : Check if Device type is blank then redirect to home page
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '')// IF Device Type not present in POST redirect to the Dashboard
                redirect(site_url('home'));
        } //END : Check if Device type is blank then redirect to home page
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;

        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') // START: Save position names in DB for device.
        {
            //Get position values from POST
            $sPositionName1 = $this->input->post('sPositionName1');
            $sPositionName2 = $this->input->post('sPositionName2');
            $this->home_model->savePositionName($sDeviceID,$sDevice,$sPositionName1,$sPositionName2);

            $aViewParameter['sucess']    =   '1'; // Set success flag 1
        } // END : Save position names in DB for device.

        //Get Existing saved position names for Device
        list($aViewParameter['sPositionName1'],$aViewParameter['sPositionName2'])      =   $this->home_model->getPositionName($sDeviceID,$sDevice);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        //Position Name Save View
        $this->template->build('PositionName',$aViewParameter); 
    } //END : Function to save position names for Valve
	
	
	public function addMoreValve()
	{
		$ValveCnt		=	$this->input->post('ValveCnt');
		$ipID			=	$this->input->post('ipID');
		
		$this->load->model('home_model');
		$extra			=	$this->home_model->getSettingsExtraDetails();
		if($ipID <= 1)
			$existValveCnt	=	(int)$extra[0]['ValveNumber'];
		else if($ipID > 1)
			$existValveCnt	=	(int)$extra[0]['ValveNumber2'];
	
		$totalValveCnt	=	$ValveCnt+$existValveCnt;
		
		if($totalValveCnt > 8)
		{
			echo 'error';
			exit;
		}
		else
		{
			$this->home_model->updateValveCnt($totalValveCnt,$ipID);
			echo 'success';
			exit;
		}
		
	}
	
	public function removeValve()
	{
		$ipID = $this->input->post('ipID');
		
		$this->load->model('home_model');
		$extra			=	$this->home_model->getSettingsExtraDetails();
		//$existValveCnt	=	(int)$extra[0]['ValveNumber'];
		
		if($ipID <= 1)
			$existValveCnt	=	(int)$extra[0]['ValveNumber'];
		else if($ipID > 1)
			$existValveCnt	=	(int)$extra[0]['ValveNumber2'];
		
		$totalValveCnt	=	$existValveCnt - 1;
		$this->home_model->updateValveCnt($totalValveCnt,$ipID);
	}
	
	public function test()
	{
		$this->template->build('welcome_message'); 
	}
	
	public function updatePumpSpeed()
	{
		$this->load->model('home_model');
		$pumpID		=	$this->input->post('PumpID');
		$pumpSpeed	=	$this->input->post('speed');
		$sIdIP		=	$this->input->post('sIdIP');
		
		$currentSpeed =	$this->home_model->getCurrentPumpSpeed($pumpID,$sIdIP);
		
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIdIP);
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($sIdIP);
		}
		
		if($currentSpeed != $pumpSpeed)
		{
			//Get the status response of devices from relay board.
			$sResponse      	=   get_rlb_status($sDeviceIP,$sPort,$shhPort);
			$currentPumpStatus	=	$sResponse['pump_seq_'.$pumpID.'_st'];
			
			//Check The current status is ON then make it OFF.
			if($currentPumpStatus > 0)
			{
				$this->makePumpOnOFF($pumpID,'0',$sDeviceIP,$sPort,$shhPort,$sIdIP);
			}
			
			//Pump Speed is updated.
			$this->home_model->updatePumpSpeed($pumpID,$pumpSpeed,$sIdIP);
			
			//If Pump was ON before changing the speed, then make it ON again after changing speed.
			if($currentPumpStatus > 0)
			{
				$this->makePumpOnOFF($pumpID,'1',$sDeviceIP,$sPort,$shhPort,$sIdIP);
			}
		}
	}
	
	public function checkRelayNumberAlreadyAssigned()
	{
		$sRelayNumber	=	$this->input->post('sRelayNumber');
		$sPumpType		=	$this->input->post('type');
		$sDeviceId      =   $this->input->post('sDeviceId');
		$ipID      		=   $this->input->post('ipID');

		$aResponse      =	array('iPumpCheck'=>0,'iPumpID'=>0);
		
		$this->load->model('home_model');
		
		//Check if IP, PORT and Mode is set or not.
            $this->checkSettingsSaved();
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($ipID);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($ipID);
		}
		//System real response is taken.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
			
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
                
		//First Check the existing Light Devices
		$aLightDetails = $this->home_model->getLightDeviceExceptSelected($sDeviceId,$ipID);
		
		if(!empty($aLightDetails))
		{
			foreach($aLightDetails as $sLight)
			{
				$arrLight   = unserialize($sLight->light_relay_number);
				if($sRelayNumber == $arrLight['sRelayNumber'] && $sPumpType == $arrLight['sRelayType'])
				{
					$aResponse['iPumpCheck']	=	1;
					$aResponse['iPumpID']		=	$sPump->pump_number;
					break;
				}
			}
		}
       
		if($aResponse['iPumpCheck'] != 1)
		{
			$aPumpDetails = $this->home_model->getAllPumpDetails($ipID);
			//print_r($aPumpDetails);
			if(!empty($aPumpDetails))
			{
				foreach($aPumpDetails as $sPump)
				{
					if($sRelayNumber == $sPump->relay_number && preg_match('/'.$sPumpType.'/',$sPump->pump_type))
					{
						$aResponse['iPumpCheck']	=	1;
						$aResponse['iPumpID']		=	$sPump->pump_number;
						break;
					}
					else if($sPump->pump_type == '2Speed')				
					{
						if($sRelayNumber == $sPump->relay_number ||  $sRelayNumber == $sPump->relay_number_1)
						{
							$aResponse['iPumpCheck']	=	1;
							$aResponse['iPumpID']		=	$sPump->pump_number;
							break;
						}
					}	
				}
			}
		}
		
		echo json_encode($aResponse);
		exit;
	}
        
	public function saveLightRelay()
	{
		$sRelayNumber   =   $this->input->post('sRelayNumber');
		$sDevice        =   $this->input->post('sDevice');
		$sDeviceId      =   $this->input->post('sDeviceId');
		$sRelayType     =   $this->input->post('sRelayType');
		$ipID			=	$this->input->post('ipID');
		
		$this->load->model('home_model');
		
		$this->home_model->saveLightRelay($sRelayNumber,$sDevice,$sDeviceId,$sRelayType,$ipID);
	}
	
	public function saveMiscRelay()
	{
		$sRelayNumber   =   $this->input->post('sRelayNumber');
		$sDevice        =   $this->input->post('sDevice');
		$sDeviceId      =   $this->input->post('sDeviceId');
		$sRelayType     =   $this->input->post('sRelayType');
		
		$this->load->model('home_model');
		
		$this->home_model->saveMiscRelay($sRelayNumber,$sDevice,$sDeviceId,$sRelayType);
	}
	
	public function updatePoolSpaMode()
	{
		$iMode   =   $this->input->post('iMode');
		$this->load->model('home_model');
		
		$this->home_model->UpdatePoolSpaMode($iMode);
	}
	
	public function checkRelayNumbers($sRelayNumber,$sPumpType)
	{
		$iCheck	=	0;
		
		$this->load->model('home_model');
		
		//First Check the existing Light Devices
		$aLightDetails = $this->home_model->getLightDevices();
		if(!empty($aLightDetails))
		{
			foreach($aLightDetails as $sLight)
			{
				$arrLight   = unserialize($sLight->light_relay_number);
				if($sRelayNumber == $arrLight['sRelayNumber'] && $sPumpType == $arrLight['sRelayType'])
				{
					$iCheck	=	1;
					break;
				}
			}
		}
       
		if($iCheck != 1)
		{
			$aPumpDetails = $this->home_model->getAllPumpDetails();
			//print_r($aPumpDetails);
			if(!empty($aPumpDetails))
			{
				foreach($aPumpDetails as $sPump)
				{
					if($sRelayNumber == $sPump->relay_number && preg_match('/'.$sPumpType.'/',$sPump->pump_type))
					{
						$iCheck	=	1;
						break;
					}
					else if($sPump->pump_type == '2Speed')				
					{
						if($sRelayNumber == $sPump->relay_number ||  $sRelayNumber == $sPump->relay_number_1)
						{
							$iCheck	=	1;
							break;
						}
					}	
				}
			}
		}
		
		return $iCheck;
	}
	
	function saveValveRelayConf()
	{
		$arrValves 	=	json_decode($this->input->post('valve'));
		$sDevice	=	$this->input->post('sDevice');
		$ipID		=	$this->input->post('ipID');
		
		
		$arrRelayStart 	=	array('0','2','4','6','8','10','12','14');
		
		$this->load->model('home_model');
		
		if(!empty($arrValves))
		{
			$sSql   =   "DELETE FROM rlb_device WHERE device_type = 'V' WHERE ip_id='".$ipID."'";
			$query  =   $this->db->query($sSql);
			
			foreach($arrValves as $valve)
			{
				//Get position values
				$sRelay1 = $valve[0];
				$sRelay2 = $valve[1];
				
				$sPosition1	=	$valve[2];
				$sPosition2	=	$valve[3];
				
				$sDeviceID		=	array_search($sRelay1,$arrRelayStart);
				$sDeviceIDOld	=	$sDeviceID;
				
				$this->home_model->saveValveRelays($sDeviceID,$sDeviceIDOld,$sDevice,$sRelay1,$sRelay2,$ipID);
				
				$this->home_model->savePositionName($sDeviceID,$sDevice,$sPosition1,$sPosition2,$ipID);
				
			}
		}
		
		
		$arrValves	=	$this->home_model->getAllValvesHavingRelays($ipID);
		$strValves	=	'00000000';
		if(!empty($arrValves))
		{
			foreach($arrValves as $aValve)
			{
				$device_number = $aValve->device_number;
				$strValves[$device_number] = '1';
			}
		}
		
		$hexNumber = dechex(bindec(strrev($strValves)));
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($ipID);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($ipID);
		}
		
		
		$response	=	assignValvesToRelay($hexNumber,$sDeviceIP,$sPort,$shhPort);
		
		$totalValve =	0;
		$aExtra	=	array();
		$sSql   =   "SELECT id,extra FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				
				if($ipID == 1)
				$aExtra['ValveNumber'] 	= count($arrValves);
				else
				$aExtra['ValveNumber2'] 	= count($arrValves);
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
				
				$totalValve = $aExtra['ValveNumber'] + $aExtra['ValveNumber2'];
            }
        }
        else
        {
			if($ipID == 1)
			$aNumDevice['ValveNumber']	=	count($arrValves);
			else
				$aExtra['ValveNumber2'] 	= count($arrValves);
            $data = array('extra' => serialize($aNumDevice) );
            $this->db->insert('rlb_setting', $data);
			
			$aExtra	=	array();
			$sSql   =   "SELECT id,extra FROM rlb_setting";
			$query  =   $this->db->query($sSql);

			if ($query->num_rows() > 0)
			{
				foreach($query->result() as $aRow)
				{  
					if($aRow->extra != '')
						$aExtra = unserialize($aRow->extra);
					
					$totalValve = $aExtra['ValveNumber'] + $aExtra['ValveNumber2'];
				}
			}
				
        }
		
		
		
		//echo json_encode($arrValves);
		echo $totalValve;
		exit;
	}
	
	function savePumpRelayConf()
	{
		$arrPumps 	=	json_decode($this->input->post('pump'));
		$ipID 		=	$this->input->post('ipID');
		$this->load->model('home_model');
		$aResponse	=	array();
		if(!empty($arrPumps))
		{
			$icheck	=	0;
			foreach($arrPumps as $sDeviceID => $pump)
			{
				$icheck	= $this->checkPumpRelayConf($sDeviceID,$pump->relayNumber1,$pump->type);
				if($icheck == '1')				
				{
					echo 'Relay number for pump '.$sDeviceID.' is already in use by other Device!';
					exit;
				}
			}
			
			foreach($arrPumps as $sDeviceID => $pump)
			{
				//GET IP of Device
				$sDeviceIP		= 	$this->home_model->getBoardIPFromID($ipID);
				
				//Get saved IP and PORT 
				list($sIP,$sPort,$extra) = $this->home_model->getSettings();
				
				$shhPort	=	'';
				if(IS_LOCAL == '1')
				{
					//Get SSH port of the RLB board using IP.
					$shhPort = $this->home_model->getSSHPortFromID($ipID);
				}
				
				//Get the status response of devices from relay board.
				$sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
				$sRelays        =   $sResponse['relay'];    // Relay Device Status
				$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
				
				$sDevice = 'PS';
		
				$aPumpDetails = $this->home_model->getPumpDetails($sDeviceID,$ipID);
				//Variable Initialization to blank.
				$sPumpNumber  	= '';
				$sPumpType  	= '';
				$sPumpSubType  	= '';
				$sPumpSpeed  	= '';
				$sPumpFlow 		= '';
				$sPumpClosure   = '';
				$sRelayNumber  	= '';
					
				if(is_array($aPumpDetails) && !empty($aPumpDetails))
				{
				  foreach($aPumpDetails as $aResultEdit)
				  { 
					$sPumpNumber  = $aResultEdit->pump_number;
					$sPumpType    = $aResultEdit->pump_type;
					$sPumpSubType = $aResultEdit->pump_sub_type;
					$sPumpSpeed   = $aResultEdit->pump_speed;
					$sPumpFlow    = $aResultEdit->pump_flow;
					$sPumpClosure = $aResultEdit->pump_closure;
					$sRelayNumber = $aResultEdit->relay_number;
				  }
				}
				$sStatus	=	0;
				if($sPumpType != '' && $sPumpType != $this->input->post('sPumpType'))
				{
					if($sPumpType == '12' || $sPumpType == '24')
					{
						if($sPumpType == '24')
						{
							$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
							$this->home_model->updateDeviceRunTime($sDeviceID,$sDevice,$sStatus,$ipID);
						}
						else if($sPumpType == '12')
						{
							$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
						}
					}
					else
					{
						if(preg_match('/Emulator/',$sPumpType))
						{
							$sNewResp = '';
							$sNewResp =  $sDeviceID.' '.$sStatus;
							
							onoff_rlb_pump($sNewResp,$sDeviceIP,$sPort,$shhPort);
							
							if($sPumpType == 'Emulator12')
							{
								$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp12,$sDeviceIP,$sPort,$shhPort);
							}
							if($sPumpType == 'Emulator24')
							{
								$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp24,$sDeviceIP,$sPort,$shhPort);
								$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus,$ipID);
							}
						}
						else if(preg_match('/Intellicom/',$sPumpType))
						{
							$sNewResp = '';
							$sNewResp =  $sDeviceID.' '.$sStatus;
							onoff_rlb_pump($sNewResp,$sDeviceIP,$sPort,$shhPort);
							
							if($sPumpType == 'Intellicom12')
							{
								$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp12,$sDeviceIP,$sPort,$shhPort);
							}
							if($sPumpType == 'Intellicom24')
							{
								$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp24,$sDeviceIP,$sPort,$shhPort);
								$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus,$ipID);
							}
						}
					}
				}
				$aPost	=	array('sPumpClosure'=>$pump->closure,'sPumpType'=>$pump->type,'sRelayNumber'=>$pump->relayNumber1,'sPumpSubType'=>$pump->pumpSubType,'sPumpAddress'=>$pump->pumpAddress,'sPumpSpeed'=>$pump->pumpSpeed,'sPumpFlow'=>$pump->pumpFlow,'sRelayNumber1'=>$pump->relayNumber2,'sPumpSubType1'=>$pump->pumpSubType1,'sPumpSpeedIn'=>$pump->pumpSpeedIn);
				
				$this->home_model->savePumpDetails($aPost,$sDeviceID,$ipID);
				
				//Change the address on the Raspberry Device
				$sPumpType      =   $this->input->post('sPumpType');
				if($sPumpType != '12' && $sPumpType != '24' && $sPumpType != '2Speed')
				{	
					$sAddress 	= $this->input->post('sPumpAddress');
					$sRes 		= getAddressToPump($sDeviceID);
					if(!preg_match('/Invalid response/',$sRes))
					{
						$aResult	=	explode(',',$sRes);
						if($aResult[2] != $sAddress)
						{
							$sResult	=	assignAddressToPump($sDeviceID,$sAddress,$sDeviceIP,$sPort,$shhPort);
						}
					}
				}
			}
		}
		
		$totalPump	=	0;
		$aExtra	=	array();
		$sSql   =   "SELECT id,extra FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				if($ipID == 1)
				$aExtra['PumpsNumber'] 	= count((array)$arrPumps);
			    else 
				$aExtra['PumpsNumber2'] 	= count((array)$arrPumps);
			
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
				
				$totalPump = $aExtra['PumpsNumber'] + $aExtra['PumpsNumber2'];
            }
        }
        else
        {
			if($ipID == 1)
			$aNumDevice['PumpsNumber'] 	= count((array)$arrPumps);
			else 
			$aNumDevice['PumpsNumber2'] 	= count((array)$arrPumps);
		
            $data = array('extra' => serialize($aNumDevice) );
            $this->db->insert('rlb_setting', $data);
			
			$aExtra	=	array();
			$sSql   =   "SELECT id,extra FROM rlb_setting";
			$query  =   $this->db->query($sSql);

			if ($query->num_rows() > 0)
			{
				foreach($query->result() as $aRow)
				{  
					if($aRow->extra != '')
						$aExtra = unserialize($aRow->extra);
					
					$totalPump = $aExtra['PumpsNumber'] + $aExtra['PumpsNumber2'];
				}
			}
        }
		
		
		
		//echo 'Pump Configuration done successfully!';
		echo $totalPump.'|||'.'Pump Configuration done successfully!';
		exit;
	}
	
	public function checkPumpRelayConf($sDeviceID,$sRelayNumber,$sPumpType)
	{
		$sPumpTypeChk	=	'';	
		
		$iCheck	=	0;
		
		//Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
        if(preg_match('/12/',$sPumpType))
		{
			$sPumpTypeChk = 12;
			if($sRelayNumber > 7)
				$iCheck = 1;
		}
		else if(preg_match('/24/',$sPumpType))
		{
			$sPumpTypeChk = 24;
			if($sRelayNumber > 15)
				$iCheck = 1;
			else if($sRelays[$sRelayNumber] == '.')
				$iCheck = 2;
		}
		
		$this->load->model('home_model');
		
		if($iCheck == 0)
		{
			//First Check the existing Light Devices
			$aLightDetails = $this->home_model->getLightDevices();
			if(!empty($aLightDetails))
			{
				foreach($aLightDetails as $sLight)
				{
					$arrLight   = unserialize($sLight->light_relay_number);
					if($sRelayNumber == $arrLight['sRelayNumber'] && $sPumpType == $arrLight['sRelayType'])
					{
						$iCheck	=	1;
						break;
					}
				}
			}
		}
		
		if($iCheck != 1 || $iCheck != 2)
		{
			//$aPumpDetails = $this->home_model->getAllPumpDetails();
			$aPumpDetails 	= $this->home_model->getPumpDetailsExcept($sDeviceID);
			//print_r($aPumpDetails);
			if(!empty($aPumpDetails))
			{
				foreach($aPumpDetails as $sPump)
				{
					if($sRelayNumber == $sPump->relay_number && preg_match('/'.$sPumpType.'/',$sPump->pump_type))
					{
						$iCheck	=	1;
						break;
					}
					else if($sPump->pump_type == '2Speed')				
					{
						if($sRelayNumber == $sPump->relay_number ||  $sRelayNumber == $sPump->relay_number_1)
						{
							$iCheck	=	1;
							break;
						}
					}	
				}
			}
		}
		return $iCheck;
	}
	
	public function saveHeaterRelayConf()
	{
		$arrHeater 	=	json_decode($this->input->post('heater'));
		$ipID		=	$this->input->post('ipID');
		
		//First Check whether relay is already assigned to other device or not.
		foreach($arrHeater as $heaterNumber => $heaterDetails)
		{
			$relayType		=	$heaterDetails->relayType;
			$sRelayNumber	=	$heaterDetails->relayNumber;
			
			$iCheck			=	$this->checkRelayNumbers($sRelayNumber,$sPumpType);
			if($icheck == '1')				
			{
				echo 'Relay number for Heater '.$heaterNumber.' is already in use by other Device!';
				exit;
			}
			//checkRelayNumbers
		}
		
		foreach($arrHeater as $heaterNumber => $heaterDetails)
		{
			$sRelayType		=	$heaterDetails->relayType;
			$sRelayNumber	=	$heaterDetails->relayNumber;
			$sHeaterName	=	$heaterDetails->name;
			
			$sDevice        =   'H';
			$sDeviceId      =   ($heaterNumber-1);
			
			$this->load->model('home_model');
			
			$this->home_model->saveLightRelay($sRelayNumber,$sDevice,$sDeviceId,$sRelayType,$ipID);
			
			if($sHeaterName != '')
			{
				$this->home_model->saveDeviceName($sDeviceId,$sDevice,$sHeaterName,$ipID);
			}
		}
		$totalHeater	=0;
		$aExtra	=	array();
		$sSql   =   "SELECT id,extra FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				if($ipID == 1)
				$aExtra['HeaterNumber'] 	= count((array)$arrHeater);
				else 
				$aExtra['HeaterNumber2'] 	= count((array)$arrHeater);
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
				
				$totalHeater = $aExtra['HeaterNumber'] + $aExtra['HeaterNumber2'];
            }

        }
        else
        {
			if($ipID == 1)
			$aNumDevice['HeaterNumber']	=	count((array)$arrHeater);
			else
			$aNumDevice['HeaterNumber2']	=	count((array)$arrHeater);
			
            $data = array('extra' => serialize($aNumDevice) );
            $this->db->insert('rlb_setting', $data);
			
			$aExtra	=	array();
			$sSql   =   "SELECT id,extra FROM rlb_setting";
			$query  =   $this->db->query($sSql);

			if ($query->num_rows() > 0)
			{
				foreach($query->result() as $aRow)
				{  
					if($aRow->extra != '')
						$aExtra = unserialize($aRow->extra);
					
					$totalHeater = $aExtra['HeaterNumber'] + $aExtra['HeaterNumber2'];
				}
			}
        }
		
		echo $totalHeater.'|||'.'Heater Configuration done successfully!';
		exit;
	
	}
	
	
	public function saveLightRelayConf()
	{
		$arrLight 	=	json_decode($this->input->post('light'));
		$ipID		=	$this->input->post('ipID');
		
		//First Check whether relay is already assigned to other device or not.
		foreach($arrLight as $lightNumber => $lightDetails)
		{
			$relayType		=	$lightDetails->relayType;
			$sRelayNumber	=	$lightDetails->relayNumber;
			
			
			$iCheck			=	$this->checkRelayNumbers($sRelayNumber,$sPumpType);
			if($icheck == '1')				
			{
				echo 'Relay number for Light '.$lightNumber.' is already in use by other Device!';
				exit;
			}
			//checkRelayNumbers
		}
		
		foreach($arrLight as $lightNumber => $lightDetails)
		{
			$sRelayType		=	$lightDetails->relayType;
			$sRelayNumber	=	$lightDetails->relayNumber;
			$slightName		=	$lightDetails->name;
			
			$sDevice        =   'L';
			$sDeviceId      =   ($lightNumber-1);
			
			$this->load->model('home_model');
			
			$this->home_model->saveLightRelay($sRelayNumber,$sDevice,$sDeviceId,$sRelayType,$ipID);
			
			if($slightName != '')
			{
				$this->home_model->saveDeviceName($sDeviceId,$sDevice,$slightName,$ipID);
			}
		}
		
		$totalLight	=	0;
		$aExtra		=	array();
		$sSql   	=   "SELECT id,extra FROM rlb_setting";
        $query  	=   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				
				if($ipID == 1)
				$aExtra['LightNumber'] 	= count((array)$arrLight);
				else 
				$aExtra['LightNumber2'] 	= count((array)$arrLight);	
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
				
				$totalLight	=	$aExtra['LightNumber'] + $aExtra['LightNumber2'];
            }
        }
        else
        {
			if($ipID == 1)
			$aNumDevice['LightNumber']	=	count((array)$arrLight);
			else
			$aNumDevice['LightNumber2']	=	count((array)$arrLight);
            $data = array('extra' => serialize($aNumDevice) );
            $this->db->insert('rlb_setting', $data);
			
			$aExtra	=	array();
			$sSql   =   "SELECT id,extra FROM rlb_setting";
			$query  =   $this->db->query($sSql);

			if ($query->num_rows() > 0)
			{
				foreach($query->result() as $aRow)
				{  
					if($aRow->extra != '')
						$aExtra = unserialize($aRow->extra);
					
					$totalLight	=	$aExtra['LightNumber'] + $aExtra['LightNumber2'];
				}
			}
			
			
        }
		
		echo $totalLight.'|||'.'Light Configuration done successfully!';
		exit;
	}
	
	
	public function saveBlowerRelayConf()
	{
		$arrBlower 	=	json_decode($this->input->post('blower'));
		$ipID		=	$this->input->post('ipID');
		
		//First Check whether relay is already assigned to other device or not.
		foreach($arrBlower as $blowerNumber => $blowerDetails)
		{
			$relayType		=	$blowerDetails->relayType;
			$sRelayNumber	=	$blowerDetails->relayNumber;
			
			$iCheck			=	$this->checkRelayNumbers($sRelayNumber,$sPumpType);
			if($icheck == '1')				
			{
				echo 'Relay number for Light '.$blowerNumber.' is already in use by other Device!';
				exit;
			}
			//checkRelayNumbers
		}
		
		foreach($arrBlower as $blowerNumber => $blowerDetails)
		{
			$sRelayType		=	$blowerDetails->relayType;
			$sRelayNumber	=	$blowerDetails->relayNumber;
			$sblowerName	=	$blowerDetails->name;
			
			$sDevice        =   'B';
			$sDeviceId      =   ($blowerNumber-1);
			
			$this->load->model('home_model');
			
			$this->home_model->saveLightRelay($sRelayNumber,$sDevice,$sDeviceId,$sRelayType,$ipID);
			
			if($sblowerName != '')
			{
				$this->home_model->saveDeviceName($sDeviceId,$sDevice,$sblowerName,$ipID);
			}
		}
		
		$totalBlower = 0;
		$aExtra	=	array();
		$sSql   =   "SELECT id,extra FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				
				if($ipID == 1)
				$aExtra['BlowerNumber'] 	= count((array)$arrBlower);
				else
				$aExtra['BlowerNumber2'] 	= count((array)$arrBlower);
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
				
				$totalBlower = $aExtra['BlowerNumber'] + $aExtra['BlowerNumber2'];
            }
        }
        else
        {
			if($ipID == 1)
			$aNumDevice['BlowerNumber']	=	count((array)$arrBlower);
			else
			$aNumDevice['BlowerNumber']	=	count((array)$arrBlower);
            $data = array('extra' => serialize($aNumDevice) );
            $this->db->insert('rlb_setting', $data);
			
			$aExtra	=	array();
			$sSql   =   "SELECT id,extra FROM rlb_setting";
			$query  =   $this->db->query($sSql);

			if ($query->num_rows() > 0)
			{
				foreach($query->result() as $aRow)
				{  
					if($aRow->extra != '')
						$aExtra = unserialize($aRow->extra);
					
					$totalBlower	=	$aExtra['BlowerNumber'] + $aExtra['BlowerNumber2'];
				}
			}
        }
		
		echo $totalBlower.'|||'.'Blower Configuration done successfully!';
		exit;
	}
	
	public function saveMiscRelayConf()
	{
		$arrMisc 	=	json_decode($this->input->post('misc'));
		$ipID       =	$this->input->post('ipID');
		
		//First Check whether relay is already assigned to other device or not.
		foreach($arrMisc as $miscNumber => $miscDetails)
		{
			$relayType		=	$miscDetails->relayType;
			$sRelayNumber	=	$miscDetails->relayNumber;
			
			$iCheck			=	$this->checkRelayNumbers($sRelayNumber,$sPumpType);
			if($icheck == '1')				
			{
				echo 'Relay number for Light '.$miscNumber.' is already in use by other Device!';
				exit;
			}
			//checkRelayNumbers
		}
		
		foreach($arrMisc as $miscNumber => $miscDetails)
		{
			$sRelayType		=	$miscDetails->relayType;
			$sRelayNumber	=	$miscDetails->relayNumber;
			$smiscName	=	$miscDetails->name;
			
			$sDevice        =   'M';
			$sDeviceId      =   ($miscNumber-1);
			
			$this->load->model('home_model');
			
			$this->home_model->saveLightRelay($sRelayNumber,$sDevice,$sDeviceId,$sRelayType,$ipID);
			
			if($smiscName != '')
			{
				$this->home_model->saveDeviceName($sDeviceId,$sDevice,$smiscName,$ipID);
			}
		}
		
		$totalMisc	=	0;
		$aExtra	=	array();
		$sSql   =   "SELECT id,extra FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				
				if($ipID == 1)
				$aExtra['MiscNumber'] 	= count((array)$arrMisc);
				else
				$aExtra['MiscNumber2'] 	= count((array)$arrMisc);
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
				
				$totalMisc	=	$aExtra['MiscNumber'] + $aExtra['MiscNumber2'];
            }
        }
        else
        {
			$aNumDevice['MiscNumber']	=	count((array)$arrMisc);
            $data = array('extra' => serialize($aNumDevice) );
            $this->db->insert('rlb_setting', $data);
			
			
			$aExtra	=	array();
			$sSql   =   "SELECT id,extra FROM rlb_setting";
			$query  =   $this->db->query($sSql);

			if ($query->num_rows() > 0)
			{
				foreach($query->result() as $aRow)
				{  
					if($aRow->extra != '')
						$aExtra = unserialize($aRow->extra);
					
					$totalMisc	=	$aExtra['MiscNumber'] + $aExtra['MiscNumber2'];
				}
			}
        }
		
		echo $totalMisc.'|||'.'Miscelleneous Device Configuration done successfully!';
		exit;
	}
	
	public function removePump()
	{
		$iPumpNumber	=	$this->input->post('iPumpNumber');
		$sIpID			=	$this->input->post('ipID');
		
		$this->load->model('home_model');
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIpID);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($sIpID);
		}
		
		//First check if pump ON/OFF
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
		$sValves        =   $sResponse['valves']; // Valve Device Status
        $sRelays        =   $sResponse['relay'];  // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
        
        //Pump device Status
        $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		
		if($sPump[$iPumpNumber] > 0) //Currently Pump is ON, make it OFF.
		{
			$aPumpDetails = $this->home_model->getPumpDetails($i,$sIpID);						
			//Variable Initialization to blank.
			$sPumpNumber  	= '';
			$sPumpType  	= '';
			$sPumpSubType  	= '';
			$sPumpSpeed  	= '';
			$sPumpFlow 		= '';
			$sPumpClosure   = '';
			$sRelayNumber  	= '';

			if(is_array($aPumpDetails) && !empty($aPumpDetails))
			{
			  foreach($aPumpDetails as $aResultEdit)
			  { 
				$sPumpNumber  = $aResultEdit->pump_number;
				$sPumpType    = $aResultEdit->pump_type;
				$sPumpSubType = $aResultEdit->pump_sub_type;
				$sPumpSpeed   = $aResultEdit->pump_speed;
				$sPumpFlow    = $aResultEdit->pump_flow;
				$sPumpClosure = $aResultEdit->pump_closure;
				$sRelayNumber = $aResultEdit->relay_number;
			  }
			}
			
			$iPumpStatus = 0;
				
			if($sPumpType != '' && $sPumpClosure == '1')
			{
				if($sPumpType == '12' || $sPumpType == '24')
				{
					if($sPumpType == '24')
					{
						$sNewResp = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
						onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
					}
					else if($sPumpType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
					}
				}
				else
				{
					if(preg_match('/Emulator/',$sPumpType))
					{
						$sNewResp = '';
						$sNewResp =  $sRelayName.' '.$iPumpStatus;
						onoff_rlb_pump($sNewResp,$sDeviceIP,$sPort,$shhPort);
						
						if($sPumpType == 'Emulator12')
						{
							$sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp12,$sDeviceIP,$sPort,$shhPort);
						}
						if($sPumpType == 'Emulator24')
						{
							$sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp24,$sDeviceIP,$sPort,$shhPort);
						}
					}
					else if(preg_match('/Intellicom/',$sPumpType))
					{
						$sNewResp = '';
						$sNewResp =  $sRelayName.' '.$iPumpStatus;
						onoff_rlb_pump($sNewResp,$sDeviceIP,$sPort,$shhPort);
						
						if($sPumpType == 'Intellicom12')
						{
							$sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp12,$sDeviceIP,$sPort,$shhPort);
						}
						if($sPumpType == 'Intellicom24')
						{
							$sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp24,$sDeviceIP,$sPort,$shhPort);
						}
					}
				}
			}
		}
		
		//Remove Pump Details From database.
		$this->home_model->removePump($iPumpNumber,$sIpID);
		
		//Remove the address associated with the pump on the relay board.
		$Pump	=	'pm'.$iPumpNumber;
		removePumpAddress($Pump,$sDeviceIP,$sPort,$shhPort);
		
		exit;
	}
	
	//Get Light Details for Assignment in the Pool and Spa After light configuration changed.
	public function getAssignLightsDetails()
	{
		$ipID	=	$this->input->post('ipID');
		
		$this->load->model('home_model');
		//Get Extra Details
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		if($ipID == 1)
		$lightNumber	=	$extra['LightNumber'];
		else
		$lightNumber	=	$extra['LightNumber2'];
	
		$sLightDetails	=	'';
		
		for($i=0;$i<$lightNumber;$i++)
		{
			$strLightName 		=	'Light '.($i+1);	
			$strLightNameTmp 	=	$this->home_model->getDeviceName($i,'L',$ipID);
			if($strLightNameTmp != '')
				$strLightName	.=	' ('.$strLightNameTmp.')';
				
	
			if($i != 0){ $sLightDetails .='<hr />'; }
			
			$sLightDetails .='<div class="rowCheckbox switch">
				<div style="margin-bottom:10px;">'.$strLightName.'</div>
				<div class="custom-checkbox"><input type="checkbox" value="'.$i.'_'.$ipID.'" id="relayLight-'.$i.'_'.$ipID.'" name="relayLight[]" hidefocus="true" style="outline: medium none;" class="lightAssign" onclick="checkLightAssign(this.value)">
				<label id="lableRelayLight-'.$i.'_'.$ipID.'" for="relayLight-'.$i.'_'.$ipID.'"><span style="color:#C9376E;">&nbsp;</span></label>
				</div>
			</div>';
			
		}
		
		echo $sLightDetails;
		exit;
		
	}
	
	public function getAssignValveDetails()
	{
		$ipID	=	$this->input->post('ipID');
		$this->load->model('home_model');
		//Get Extra Details
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		if($ipID == 1)
			$valveNumber	=	$extra['ValveNumber'];
		else
			$valveNumber	=	$extra['ValveNumber2'];
		$sValveDetails	=	'';
		
		for($i=0;$i<$valveNumber;$i++)
		{
			$strValveName 		=	'Valve '.($i+1);	
			$strValveNameTmp 	=	$this->home_model->getDeviceName($i,'V',$ipID);
			if($strValveNameTmp != '')
				$strValveName	.=	' ('.$strValveNameTmp.')';
				
	
			if($i != 0){ $sValveDetails .='<hr />'; }
			
			$sValveDetails .='<div class="rowCheckbox switch">
				<div style="margin-bottom:10px;">'.$strValveName.'</div>
				<div class="custom-checkbox"><input type="checkbox" value="'.$i.'_'.$ipID.'" id="relayValve-'.$i.'_'.$ipID.'" name="relayValve[]" hidefocus="true" style="outline: medium none;" onclick="checkValveAssign(this.value)" class="valveAssign">
				<label id="lableRelayValve-'.$i.'_'.$ipID.'" for="relayValve-'.$i.'_'.$ipID.'"><span style="color:#C9376E;">&nbsp;</span></label>
				</div>
			</div>';
			
		}
		
		echo $sValveDetails;
		exit;
		
	}
	
	public function getAssignPumpDetails()
	{
		$ipID	=	$this->input->post('ipID');

		$this->load->model('home_model');
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($ipID);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($ipID);
		}
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
		if($ipID == 1)
		$pumpNumber	=	$extra['PumpsNumber'];
		else
		$pumpNumber	=	$extra['PumpsNumber2'];
	
		$sPumpDetails	=	'';
		
		for($i=0;$i<$pumpNumber;$i++)
		{
			$strPumpName 		=	'Valve '.($i+1);	
			$strPumpNameTmp 	=	$this->home_model->getDeviceName($i,'PS',$ipID);
			if($strPumpNameTmp != '')
				$strPumpName	.=	' ('.$strPumpNameTmp.')';
				
	
			if($i != 0){ $sPumpDetails .='<hr />'; }
			
			$sPumpDetails .='<div class="rowCheckbox switch">
				<div style="margin-bottom:10px;">'.$strPumpName.'</div>
				<div class="custom-checkbox"><input type="checkbox" value="'.$i.'_'.$ipID.'" id="relayPump-'.$i.'_'.$ipID.'" name="relayPumpchk[]" hidefocus="true" style="outline: medium none;" onclick="checkPumpAssign(this.value)" class="pumpAssign">
				<label id="lableRelayPump-'.$i.'_'.$ipID.'" for="relayPump-'.$i.'_'.$ipID.'"><span style="color:#C9376E;">&nbsp;</span></label>
				</div>
			</div>';
			
		}
		
		echo $sPumpDetails;
		exit;
		
	}
	
	public function getAssignHeaterDetails()
	{
		$ipID	=	$this->input->post('ipID');
		$this->load->model('home_model');
		//Get Extra Details
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		if($ipID == 1)
			$heaterNumber	=	$extra['HeaterNumber'];
		else
			$heaterNumber	=	$extra['HeaterNumber2'];
		
		$sHeaterDetails	=	'';
		
		for($i=0;$i<$heaterNumber;$i++)
		{
			$strHeaterName 		=	'Valve '.($i+1);	
			$strHeaterNameTmp 	=	$this->home_model->getDeviceName($i,'H',$ipID);
			if($strHeaterNameTmp != '')
				$strHeaterName	.=	' ('.$strHeaterNameTmp.')';
				
	
			if($i != 0){ $sHeaterDetails .='<hr />'; }
			
			$sHeaterDetails .='<div class="rowCheckbox switch">
				<div style="margin-bottom:10px;">'.$strHeaterName.'</div>
				<div class="custom-checkbox"><input type="checkbox" value="'.$i.'_'.$ipID.'" id="relayHeater-'.$i.'_'.$ipID.'" name="relayHeater[]" hidefocus="true" style="outline: medium none;" onclick="checkHeaterAssign(this.value)" class="heaterAssign">
				<label id="lableRelayHeater-'.$i.'_'.$ipID.'" for="relayHeater-'.$i.'_'.$ipID.'"><span style="color:#C9376E;">&nbsp;</span></label>
				</div>
			</div>';
		}
		
		echo $sHeaterDetails;
		exit;
		
	}
	
	
	
	public function getAssignBlowerDetails()
	{
		$ipID	= $this->input->post('ipID');
		$this->load->model('home_model');
		//Get Extra Details
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		if($ipID == 1)
		$blowerNumber	=	$extra['BlowerNumber'];
		else
		$blowerNumber	=	$extra['BlowerNumber2'];		
	
		$sBlowerDetails	=	'';
		
		for($i=0;$i<$blowerNumber;$i++)
		{
			$strBlowerName 		=	'Blower '.($i+1);	
			$strBlowerNameTmp 	=	$this->home_model->getDeviceName($i,'B',$ipID);
			if($strBlowerNameTmp != '')
				$strBlowerName	.=	' ('.$strBlowerNameTmp.')';
				
	
			if($i != 0){ $sBlowerDetails .='<hr />'; }
			
			$sBlowerDetails .='<div class="rowCheckbox switch">
				<div style="margin-bottom:10px;">'.$strBlowerName.'</div>
				<div class="custom-checkbox"><input type="checkbox" value="'.$i.'_'.$ipID.'" id="relayBlower-'.$i.'_'.$ipID.'" name="relayBlower[]" hidefocus="true" style="outline: medium none;" onclick="checkBlowerAssign(this.value)" class="blowerAssign">
				<label id="lableRelayBlower-'.$i.'_'.$ipID.'" for="relayBlower-'.$i.'_'.$ipID.'"><span style="color:#C9376E;">&nbsp;</span></label>
				</div>
			</div>';
			
		}
		
		echo $sBlowerDetails;
		exit;
	}
	
	public function getAssignMiscDetails()
	{
		$this->load->model('home_model');
		$ipID = $this->input->post('ipID');
		//Get Extra Details
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$miscNumber	=	$extra['MiscNumber'];
		$sMiscDetails	=	'';
		
		for($i=0;$i<$miscNumber;$i++)
		{
			$strMiscName 		=	'Misc Device '.($i+1);	
			$strMiscNameTmp 	=	$this->home_model->getDeviceName($i,'M',$ipID);
			if($strMiscNameTmp != '')
				$strMiscName	.=	' ('.$strMiscNameTmp.')';
				
	
			if($i != 0){ $sMiscDetails .='<hr />'; }
			
			$sMiscDetails .='<div class="rowCheckbox switch">
				<div style="margin-bottom:10px;">'.$strMiscName.'</div>
				<div class="custom-checkbox"><input type="checkbox" value="'.$i.'_'.$ipID.'" id="relayMisc-'.$i.'_'.$ipID.'" name="relayMisc[]" hidefocus="true" style="outline: medium none;" onclick="checkMiscAssign(this.value)" class="miscAssign">
				<label id="lableRelayMisc-'.$i.'_'.$ipID.'" for="relayMisc-'.$i.'_'.$ipID.'"><span style="color:#C9376E;">&nbsp;</span></label>
				</div>
			</div>';
			
		}
		
		echo $sMiscDetails;
		exit;
	}
	
	
	public function makeInputDeviceOnOff()
	{
		//Get the Device details from POST whose status will be changed.
		$sInput		=	$this->input->post('input');
		$aDevice	=	explode("_",$this->input->post('device'));
		$sStatus	=	$this->input->post('status');
		$sPort		=	$this->input->post('sPort');
		$sIdIP		=	$this->input->post('sIdIP');
	
		$this->load->model('home_model');
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIdIP);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($sIdIP);
		}
		
		
		
		$sDeviceNum	=	$aDevice[0];
		$sDevice	=	$aDevice[1];
		$sName      =   $sDeviceNum; //Device Number
		
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
        
        //$sResponse      =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves'];   // Valve Device Status
        $sRelays        =   $sResponse['relay'];    // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
        
        $sNewResp       =   '';
           
        //list($sIP,$sPort1,$extra) = $this->home_model->getSettings();
		
        //R = Relay, P = PowerCenter, V = Valve, PS = Pumps
        if($sDevice == 'R') // If Device type is Relay
        {
            $sNewResp = replace_return($sRelays, $sStatus, $sName );
            onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
            $this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus,$sIdIP);
			
			list($pumpNumber,$relay1,$relay2)	=	$this->home_model->getPumpNumberFromRelayNumber($sName,'24',$sIdIP);
			if($pumpNumber != '')
			{
				//Check if the relay is assigned to pump or not.
				$aPumpDetails =	$this->home_model->getPumpDetails($pumpNumber,$sIdIP);
				if(!empty($aPumpDetails))
				{
					foreach($aPumpDetails as $sPump)
					{
						if($sStatus != '0')
						{
							if($relay1 == $sName)
								$sStatus = '1';
							if($relay2 == $sName)
								$sStatus = '2';
						}
						$this->makePumpOnOFF($sPump->pump_number,$sStatus,$sDeviceIP,$sPort,$shhPort,$sIdIP);
					}
				}
			}
            
        }
        if($sDevice == 'P') // If Device type is Power Center
        {
            $sNewResp = replace_return($sPowercenter, $sStatus, $sName );
            onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
			
			list($pumpNumber,$relay1,$relay2)	=	$this->home_model->getPumpNumberFromRelayNumber($sName,'12',$sIdIP);
			
			if($pumpNumber != '')
			{
				//Check if the relay is assigned to pump or not.
				$aPumpDetails =	$this->home_model->getPumpDetails($pumpNumber,$sIdIP);
				if(!empty($aPumpDetails))
				{
					foreach($aPumpDetails as $sPump)
					{
						if($sStatus != '0')
						{
							if($relay1 == $sName)
								$sStatus = '1';
							if($relay2 == $sName)
								$sStatus = '2';
						}
						$this->makePumpOnOFF($sPump->pump_number,$sStatus,$sDeviceIP,$sPort,$shhPort,$sIdIP);
					}
				}
			}
        }
        if($sDevice == 'V') // If Device type is Valve
        {
            $sNewResp = replace_return($sValves, $sStatus, $sName );
            onoff_rlb_valve($sNewResp,$sDeviceIP,$sPort,$shhPort);
        }
        if($sDevice == 'PS') // If Device type is Pump
        {
			$this->makePumpOnOFF($sName,$sStatus,$sDeviceIP,$sPort,$shhPort,$sIdIP);
		}
		if($sDevice	==	'L')
		{
			$aLightDetails  =   $this->home_model->getLightDeviceDetails($sName,$sIdIP);
			if(!empty($aLightDetails))
			{
				foreach($aLightDetails as $aLight)
				{
					$sLightStatus	=	'';
					$sRelayDetails  =   unserialize($aLight->light_relay_number);
					
					//Light Operated Type and Relay
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
						onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
						$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$sStatus,$sIdIP);
					}
					if($sRelayType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
					}
				}
			}
		}
		if($sDevice	==	'B')
		{
			$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($sName,$sIdIP);
			if(!empty($aBlowerDetails))
			{
				foreach($aBlowerDetails as $aBlower)
				{
					$sBlowerStatus	=	'';
					$sRelayDetails  =   unserialize($aBlower->light_relay_number);
					
					//Blower Operated Type and Relay
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
						onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
						$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$sStatus,$sIdIP);
					}
					if($sRelayType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
					}
				}
			}
		}
		if($sDevice	==	'H')
		{
			//Heater Details
			$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($sName,$sIdIP);
			if(!empty($aHeaterDetails))
			{
				foreach($aHeaterDetails as $aHeater)
				{
					$sHeaterStatus	=	'';
					$sRelayDetails  =   unserialize($aHeater->light_relay_number);
					
					//Heater Operated Type and Relay
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
						onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
						$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$sStatus,$sIdIP);
					}
					if($sRelayType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
					}
				}
			}
		}
		if($sDevice	==	'M')
		{
			//Heater Details
			$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($sName,$sIdIP);
			if(!empty($aMiscDetails))
			{
				foreach($aMiscDetails as $aMisc)
				{
					$sRelayDetails  =   unserialize($aMisc->light_relay_number);
					
					//Heater Operated Type and Relay
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
						onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
						$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$sStatus,$sIdIP);
					}
					if($sRelayType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
					}
				}
			}
		}
		
        exit;
		
	}
	
	//START : Function to get the status of all devices after every 5 Seconds
	public function getAllDeviceStatus()
	{
		$IpId			=	$this->input->post('IpId');
		$aViewParameter	=	array();
		
		//Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();
		
		$this->load->model('home_model');
		
		$IP	= $this->home_model->getBoardIPFromID($IpId);
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($IpId);
		}
				
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status($IP,$aViewParameter['sPort'],$shhPort);
		
		$sValves        =   $sResponse['valves']; // Valve Device Status
        $sRelays        =   $sResponse['relay'];  // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
        $sTime          =   $sResponse['time']; // Server Time from Response
        
		//Pump device Status
        $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		
        // Temperature Sensor Device 
		$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);
		
		//START : Sent for View
        $aViewParameter['relay_count']  =   strlen($sRelays);
        $aViewParameter['valve_count']  =   strlen($sValves);
        $aViewParameter['power_count']  =   strlen($sPowercenter);
			
        $aViewParameter['pump_count']   	=   count($sPump);
        $aViewParameter['temprature_count'] =   count($sTemprature);
        $aViewParameter['time']         	=   $sTime;
			
		$aViewParameter['activeCountRelay'] = 	$aViewParameter['relay_count'] - substr_count($sRelays, '.');
			
        $aViewParameter['OnCountRelay']    = 	substr_count($sRelays, '1');
        $aViewParameter['OFFCountRelay']   = 	substr_count($sRelays, '0');
        $aViewParameter['OnCountPower']    = 	substr_count($sPowercenter, '1');
        $aViewParameter['OFFCountPower']   = 	substr_count($sPowercenter, '0');
        $aViewParameter['activeCountValve']= 	$aViewParameter['valve_count'] - substr_count($sValves, '.');
        $aViewParameter['OnCountValve']    = 	substr_count($sValves, '1') + substr_count($sValves, '2');
        $aViewParameter['OFFCountValve']   = 	substr_count($sValves, '0');
		
		$activeCountTemperature	=	0;
        foreach($sTemprature as $temperature)
        {
            if($temperature > 0)
                $activeCountTemperature++;
        }
        $aViewParameter['activeCountTemperature']	=	$activeCountTemperature;
		
		$activeCountPump				   = $extra['PumpsNumber'];
		$aViewParameter['activeCountPump'] = $activeCountPump;
		
		
		$OnCountPump 	=	0;
		$OFFCountPump	=	0;
		//foreach($sPump as $pump)
		for($i=0;$i<$activeCountPump;$i++)
		{
			if($sPump[$i] > 0)
				$OnCountPump++;
			else
				$OFFCountPump++;
		}
		$aViewParameter['OnCountPump']     		= 	$OnCountPump;
		$aViewParameter['OFFCountPump']     	= 	$OFFCountPump;
		
		
		//START : GET remote switch status of the devices.
		$this->load->model('analog_model');
		$aAP		=   substr($sResponse['remote_spa_ctrl_st'], 0, 4);
		
		$aAPResult 	=   $this->analog_model->getAllAnalogDevice($IpId);
        
		$iResultCnt =   count($aAPResult);
		$arrStatus	=	array();
		for($i=0; $i<$iResultCnt; $i++)
		{
			if($aAPResult[$i] != '')
			{
				$arrStatus[$i]['device']	=	$aAPResult[$i];
				$aDevice = explode('_',$aAPResult[$i]);
				
				if($aDevice[1] != '')
				{
					if($aDevice[1] == 'R')
					{
						if($sRelays[$aDevice[0]] != '' && $sRelays[$aDevice[0]] != '.')
						{
							
							$arrStatus[$i]['status']	=	$sRelays[$aDevice[0]];
							$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'R',$IpId);
							if($arrStatus[$i]['name'] == '')
								$arrStatus[$i]['name'] = 'Relay '.$aDevice[0];
							
						}
						//exex('rlb m 0 2 1');
					}
					if($aDevice[1] == 'P')
					{
						$arrStatus[$i]['status']	=	$sPowercenter[$aDevice[0]];
						$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'P',$IpId);
						
						if($arrStatus[$i]['name'] == '')
								$arrStatus[$i]['name'] = 'PowerCenter'.$aDevice[0];
					}
					if($aDevice[1] == 'V')
					{
						if($sValves[$aDevice[0]] != '' && $sValves[$aDevice[0]] != '.')
						{
							$arrStatus[$i]['status']	=	$sValves[$aDevice[0]];
							$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'V',$IpId);
							if($arrStatus[$i]['name'] == '')
								$arrStatus[$i]['name'] = 'Valve '.$aDevice[0];
						}
					}
					if($aDevice[1] == 'PS')
					{
						
						$arrStatus[$i]['status']	=	$sPump[$aDevice[0]];
						$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'PS',$IpId);
						if($arrStatus[$i]['name'] == '')
							$arrStatus[$i]['name'] = 'Pump '.$aDevice[0];
					}
					if($aDevice[1] == 'B')
					{
						
						$arrStatus[$i]['status']	=	0;
						$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($aDevice[0],$IpId);
						if(!empty($aBlowerDetails))
						{
							foreach($aBlowerDetails as $aBlower)
							{
								$sBlowerStatus	=	'';
								$sRelayDetails  =   unserialize($aBlower->light_relay_number);
								
								//Blower Operated Type and Relay
								$sRelayType     =   $sRelayDetails['sRelayType'];
								$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
								
								if($sRelayType == '24')
								{
									$sBlowerStatus   =   $sRelays[$sRelayNumber];
								}
								if($sRelayType == '12')
								{
									$sBlowerStatus   =   $sPowercenter[$sRelayNumber];
								}
							
								$arrStatus[$i]['status'] = $sBlowerStatus;
							}
						}
						$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'B',$IpId);
						if($arrStatus[$i]['name'] == '')
							$arrStatus[$i]['name'] = 'Blower '.$aDevice[0];
					}
					if($aDevice[1] == 'L')
					{
						
						$arrStatus[$i]['status']	=	0;
						$aLightDetails  =   $this->home_model->getLightDeviceDetails($aDevice[0],$IpId);
						if(!empty($aLightDetails))
						{
							foreach($aLightDetails as $aLight)
							{
								$sLightStatus	=	'';
								$sRelayDetails  =   unserialize($aLight->light_relay_number);
								
								//Blower Operated Type and Relay
								$sRelayType     =   $sRelayDetails['sRelayType'];
								$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
								
								if($sRelayType == '24')
								{
									$sLightStatus   =   $sRelays[$sRelayNumber];
								}
								if($sRelayType == '12')
								{
									$sLightStatus   =   $sPowercenter[$sRelayNumber];
								}
							
								$arrStatus[$i]['status'] = $sLightStatus;
							}
						}
						$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'L',$IpId);
						if($arrStatus[$i]['name'] == '')
							$arrStatus[$i]['name'] = 'Light '.$aDevice[0];
					}
					if($aDevice[1] == 'H')
					{
						$arrStatus[$i]['status']	=	0;
						$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($aDevice[0],$IpId);
						if(!empty($aHeaterDetails))
						{
							foreach($aHeaterDetails as $aHeater)
							{
								$sHeaterStatus	=	'';
								$sRelayDetails  =   unserialize($aHeater->light_relay_number);
								
								//Blower Operated Type and Relay
								$sRelayType     =   $sRelayDetails['sRelayType'];
								$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
								
								if($sRelayType == '24')
								{
									$sHeaterStatus   =   $sRelays[$sRelayNumber];
								}
								if($sRelayType == '12')
								{
									$sHeaterStatus   =   $sPowercenter[$sRelayNumber];
								}
							
								$arrStatus[$i]['status'] = $sHeaterStatus;
							}
						}
						
						$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'H',$IpId);
						if($arrStatus[$i]['name'] == '')
							$arrStatus[$i]['name'] = 'Heater '.$aDevice[0];
					}
					if($aDevice[1] == 'M')
					{
						$arrStatus[$i]['status']	=	0;
						$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($aDevice[0],$IpId);
						if(!empty($aMiscDetails))
						{
							foreach($aMiscDetails as $aMisc)
							{
								$sMiscStatus	=	'';
								$sRelayDetails  =   unserialize($aMisc->light_relay_number);
								
								//Blower Operated Type and Relay
								$sRelayType     =   $sRelayDetails['sRelayType'];
								$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
								
								if($sRelayType == '24')
								{
									$sMiscStatus   =   $sRelays[$sRelayNumber];
								}
								if($sRelayType == '12')
								{
									$sMiscStatus   =   $sPowercenter[$sRelayNumber];
								}
							
								$arrStatus[$i]['status'] = $sMiscStatus;
							}
						}
						
						$arrStatus[$i]['name']		=	$this->home_model->getDeviceName($aDevice[0],'M',$IpId);
						if($arrStatus[$i]['name'] == '')
							$arrStatus[$i]['name'] = 'Heater '.$aDevice[0];
					}
					
				}
			}
		}
		
		//1st Relayboard Device Status for view.
		$aViewParameter['arrStatus'] = $arrStatus;
		
		//2nd Relayboard Device Status for view.
		//$aViewParameter['arrStatus2'] = $arrStatus2;
		
		echo json_encode($aViewParameter);
		exit;
		
	}//END : Function to get the status of all devices after every 5 Seconds
	
	public function changeProgramStatus()
	{
		$iProgramID		=	$this->input->post('programID');	
		$bChangestatus	=	$this->input->post('changeStatus');
		
		$this->load->model('home_model');
		$this->home_model->updateStatusProgram($iProgramID,$bChangestatus);
		exit;
	}
	
	
	//START : GET Device Status.
	public function getStatus()
	{
		$this->load->model('home_model');
		$sDevice	=	$this->input->post('sDevice');
		$IpId		=	$this->input->post('IpId');
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($IpId);
		
		//Get saved IP and PORT 
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($IpId);
		}
		
		//System real response is taken.
		$sResponse      = 	get_rlb_status($sDeviceIP,$sPort,$shhPort);
		$sRelays      	=   $sResponse['relay'];
		$sPowercenter   =   $sResponse['powercenter'];
		$sValves        =   $sResponse['valves'];
		
		$arrReturn		= array();
		
		if($sDevice == 'R') //24V AC device status.
		{
			$iRelayCount  =   strlen($sRelays);
			for($i=0;$i<$iRelayCount;$i++)
			{
				$arrReturn[$i] = $sRelays[$i];
			}
		}
		else if($sDevice == 'P') //12V AC device status.
		{
			$iPowerCount  	=   strlen($sPowercenter);
			for($i=0;$i<$iPowerCount;$i++)
			{
				$arrReturn[$i] = $sPowercenter[$i];
			}
		}
		else if($sDevice == 'V') //Valve device status.
		{
			$iValveCount  	=   strlen($sValves);
			for($i=0;$i<$iValveCount;$i++)
			{
				$arrReturn[$i] = $sValves[$i];
			}
		}
		else if($sDevice == 'PS') //Pump device status.
		{
			$arrReturn =  array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		}
		else if($sDevice == 'L') //Light device status.
		{
			$tempIpID	=	($IpId > 1) ? $IpId : '';
			$iLightCnt	=	$extra['LightNumber'.$tempIpID];
			for($i=0;$i<$iLightCnt;$i++)
			{
				$sLightStatus	=	'';
				$aLightDetails  =   $this->home_model->getLightDeviceDetails($i,$IpId);
				if(!empty($aLightDetails))
				{
					foreach($aLightDetails as $aLight)
					$sRelayDetails  =   unserialize($aLight->light_relay_number);
					
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sLightStatus   =   $sRelays[$sRelayNumber];
					}
					if($sRelayType == '12')
					{
						
						$sLightStatus   =   $sPowercenter[$sRelayNumber];
					}
					
					$arrReturn[$i] = $sLightStatus;
				}
			}
		}
		else if($sDevice == 'B') //Blower device status.
		{
			$tempIpID	=	($IpId > 1) ? $IpId : '';
			$iBlowerCnt	=	$extra['BlowerNumber'.$tempIpID];
			for($i=0;$i<$iBlowerCnt;$i++)
			{
				$sBlowerStatus	 = 	 '';
				$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($i,$IpId);
				if(!empty($aBlowerDetails))
				{
					foreach($aBlowerDetails as $aBlower)
					$sRelayDetails  =   unserialize($aBlower->light_relay_number);
					
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sBlowerStatus   =   $sRelays[$sRelayNumber];
					}
					if($sRelayType == '12')
					{
						$sBlowerStatus   =   $sPowercenter[$sRelayNumber];
					}
					
					$arrReturn[$i] = $sBlowerStatus;
				}
			}
		}
		else if($sDevice == 'H') //Heater device status.
		{
			$tempIpID	=	($IpId > 1) ? $IpId : '';
			$iHeaterCnt	=	$extra['HeaterNumber'.$tempIpID];
			for($i=0;$i<$iHeaterCnt;$i++)
			{
				$sHeaterStatus	 = 	 '';	
				$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($i,$IpId);
				if(!empty($aHeaterDetails))
				{
					foreach($aHeaterDetails as $aHeater)
					$sRelayDetails  =   unserialize($aHeater->light_relay_number);
					
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sHeaterStatus   =   $sRelays[$sRelayNumber];
					}
					if($sRelayType == '12')
					{
						$sHeaterStatus   =   $sPowercenter[$sRelayNumber];
					}
					
					$arrReturn[$i] = $sHeaterStatus;
				}
			}
		}
		else if($sDevice == 'M') //Miscelleneous device status.
		{
			$tempIpID	=	($IpId > 1) ? $IpId : '';
			$iMiscCnt	=	$extra['MiscNumber'.$tempIpID];
			for($i=0;$i<$iMiscCnt;$i++)
			{
				$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($i,$IpId);
				if(!empty($aMiscDetails))
				{
					foreach($aMiscDetails as $aMisc)
					{
						$sMiscStatus	=	'';
						$sRelayDetails  =   unserialize($aMisc->light_relay_number);
						
						$sRelayType     =   $sRelayDetails['sRelayType'];
						$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
						
						if($sRelayType == '24')
						{
							$sMiscStatus   =   $sRelays[$sRelayNumber];
						}
						if($sRelayType == '12')
						{
							$sMiscStatus   =   $sPowercenter[$sRelayNumber];
						}
					}
					
					$arrReturn[$i] = $sMiscStatus;
				}
			}
		}
		echo json_encode($arrReturn);
		exit;
		
	}
	//END : GET Device Status.
	
	
	//START : GET Device Status for all devices.
	public function getStatusAll()
	{
		$this->load->model('home_model');
		
		//System real response is taken.
		$sResponse      = 	get_rlb_status();
		$sRelays      	=   $sResponse['relay'];
		$sPowercenter   =   $sResponse['powercenter'];
		$sValves        =   $sResponse['valves'];
		
		$arrReturn		= array();
		
		//Get Extra Details
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		//24V AC Relay
		$iRelayCount  =   strlen($sRelays);
		for($i=0;$i<$iRelayCount;$i++)
		{
			$arrReturn['R'][$i] = $sRelays[$i];
		}
		
		//12V DC Relay
		$iPowerCount  	=   strlen($sPowercenter);
		for($i=0;$i<$iPowerCount;$i++)
		{
			$arrReturn['P'][$i] = $sPowercenter[$i];
		}
		
		//Valve Device Status
		$iValveCount  	=   strlen($sValves);
		for($i=0;$i<$iValveCount;$i++)
		{
			$arrReturn['V'][$i] = $sValves[$i];
		}
		
		//Pump Device Status
		//$arrReturn['PS'] =  array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		
		//Light Device Status
		$iLightCnt	=	$extra['LightNumber'];
		for($i=0;$i<$iLightCnt;$i++)
		{
			$sLightStatus	=	'';
			$aLightDetails  =   $this->home_model->getLightDeviceDetails($i);
			if(!empty($aLightDetails))
			{
				foreach($aLightDetails as $aLight)
				$sRelayDetails  =   unserialize($aLight->light_relay_number);
				
				$sRelayType     =   $sRelayDetails['sRelayType'];
				$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
				
				if($sRelayType == '24')
				{
					$sLightStatus   =   $sRelays[$sRelayNumber];
				}
				if($sRelayType == '12')
				{
					
					$sLightStatus   =   $sPowercenter[$sRelayNumber];
				}
				
				$arrReturn['L'][$i] = $sLightStatus;
			}
		}
		
		//Blower Device Status
		$iBlowerCnt	=	$extra['BlowerNumber'];
		for($i=0;$i<$iBlowerCnt;$i++)
		{
			$sBlowerStatus	 = 	 '';
			$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($i);
			if(!empty($aBlowerDetails))
			{
				foreach($aBlowerDetails as $aBlower)
				$sRelayDetails  =   unserialize($aBlower->light_relay_number);
				
				$sRelayType     =   $sRelayDetails['sRelayType'];
				$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
				
				if($sRelayType == '24')
				{
					$sBlowerStatus   =   $sRelays[$sRelayNumber];
				}
				if($sRelayType == '12')
				{
					$sBlowerStatus   =   $sPowercenter[$sRelayNumber];
				}
				
				$arrReturn['B'][$i] = $sBlowerStatus;
			}
		}
		
		//Heater Device Status
		$iHeaterCnt	=	$extra['HeaterNumber'];
		for($i=0;$i<$iHeaterCnt;$i++)
		{
			$sHeaterStatus	 = 	 '';	
			$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($i);
			if(!empty($aHeaterDetails))
			{
				foreach($aHeaterDetails as $aHeater)
				$sRelayDetails  =   unserialize($aHeater->light_relay_number);
				
				$sRelayType     =   $sRelayDetails['sRelayType'];
				$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
				
				if($sRelayType == '24')
				{
					$sHeaterStatus   =   $sRelays[$sRelayNumber];
				}
				if($sRelayType == '12')
				{
					$sHeaterStatus   =   $sPowercenter[$sRelayNumber];
				}
				
				$arrReturn['H'][$i] = $sHeaterStatus;
			}
		}
		
		//Miscelleneous Device Status
		$iMiscCnt	=	$extra['MiscNumber'];
		for($i=0;$i<$iMiscCnt;$i++)
		{
			$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($i);
			if(!empty($aMiscDetails))
			{
				foreach($aMiscDetails as $aMisc)
				{
					$sMiscStatus	=	'';
					$sRelayDetails  =   unserialize($aMisc->light_relay_number);
					
					$sRelayType     =   $sRelayDetails['sRelayType'];
					$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
					
					if($sRelayType == '24')
					{
						$sMiscStatus   =   $sRelays[$sRelayNumber];
					}
					if($sRelayType == '12')
					{
						$sMiscStatus   =   $sPowercenter[$sRelayNumber];
					}
				}
				
				$arrReturn['M'][$i] = $sMiscStatus;
			}
		}
		
		echo json_encode($arrReturn);
		exit;
	}
	
	
	//START : GET Device details related to the relay.
	//(L=Light,B=Blower,H=Heater,M=Miscellaneous)
	public function getDeviceRelayDetails()
	{
		//POST DATA
		$sDevice		=	$this->input->post('sDevice');
		$deviceNumber	=	$this->input->post('deviceNumber');
		$ipID			=	$this->input->post('ipID');	
		
		$this->load->model('home_model');
		$aDetails		=	array();
		$aResult		=	array();
		
		if($sDevice == 'L')//Light
		{
			$aDetails  =   $this->home_model->getLightDeviceDetails($deviceNumber,$ipID);
		}
		if($sDevice == 'H')//Heater
		{
			$aDetails  =   $this->home_model->getHeaterDeviceDetails($deviceNumber,$ipID);
		}
		if($sDevice == 'B')//Blower
		{
			$aDetails  =   $this->home_model->getBlowerDeviceDetails($deviceNumber,$ipID);
		}
		if($sDevice == 'M')//Misc
		{
			$aDetails  =   $this->home_model->getMiscDeviceDetails($deviceNumber,$ipID);	
		}
		
		if(!empty($aDetails))
		{
			foreach($aDetails as $aDevice)
			{
				$sStatus	=	'';
				$sRelayDetails  =   unserialize($aDevice->light_relay_number);
				
				$sRelayType     =   $sRelayDetails['sRelayType'];
				$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
			
				$aResult['type']	=	$sRelayType;
				$aResult['number']	=	$sRelayNumber;
			}
		}
		
		echo json_encode($aResult);
		exit;
		
		
	}//END : GET Device details related to the relay.
	
	//START: Save the Port number for the Device.
	public function saveDevicePort()
	{
		//Post Data
		$iDeviceNum		=	$this->input->post('iDeviceNum');
		$sDeviceType	=	$this->input->post('sDeviceType');
		$sPort			=	$this->input->post('sPort');
		
		if($sPort != '')
		{
			$this->load->model('home_model');
			
			$this->home_model->saveDevicePortDetails($iDeviceNum,$sDeviceType,$sPort);
		}
		
		exit;
	}
	//END: Save the Port number for the Device.
	
	
	//START: Save whether to show the temperature sensor on Dashboard or not.
	public function saveTempShowOnDashboard()
	{
		//Post Data
		$iTempSensor	=	$this->input->post('iTempSensor');
		$bStatus		=	$this->input->post('bStatus');
		$sIpId			=	$this->input->post('sIpId');
		
		$this->load->model('home_model');
		$this->home_model->saveDeviceShowOnDashboard($iTempSensor,$bStatus,$sIpId);
		
		exit;
	}
	//END: Save whether to show the temperature sensor on Dashboard or not.
	
	//START: Get the real responce for the system.
	public function getRealResponse()
	{
		$this->load->model('home_model');
		
		$arrResponse	=	array();
		$sIdIP	=	$this->input->post('sIdIP');
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIdIP);
		
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($sIdIP);
			
		}
		
		//Get the status response of devices from relay board.
		$sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
		$arrResponse['response'] =	$sResponse['response'];
		
		$aResponse	= explode(',',$arrResponse['response']);
		$cntRows	= count($aResponse);
		$sDescription	=	'';
		
		$aName      = array('ID','SEQ','MODE','DOW','WCK','RTC','ERR','VLV','RLY','PCN','AP0','AP1',
                    'AP2','AP3','APS','TS0','TS1','TS2','TS3','TS4','TS5','LVI','RSC','LVA',
                    'PM0','PM1','PM2');

		$aDesc      = array('Record identifier','Sequence number that runs from 000 ... 255','Mode',
							'Day of week (0 – Sunday ... 6 – Saturday)','Wall clock (24 hour clock)',
							'RTC status','HEX',
							'Valve status, a ‘.’ indicates the valve is not configured',
							'Relay status, a ‘.’ indicates the output is assigned to a valve',
							'Power center status','Analog input 0','Analog input 1','Analog input 2','Analog input 3',
							'DC supply voltage','Temperature sensor 0 / Controller temperature','Temperature sensor 1',
							'Temperature sensor 2','Temperature sensor 3','Temperature sensor 4','Temperature sensor 5',
							'Level measurement [inch] instant','Remote Spa Control and digital input status','Level measurement [inch] average',
							'Status of pump sequencer 0','Status of pump sequencer 1','Status of pump sequencer 2');
		
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
							<table class="table table-hover" style="width: 80%;">
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
			
			if($aName[$i] == 'ERR')	
			{
				$sDescription.= '<tr>
					<td>'.$i.'</td>
					<td>'.$aName[$i].'</td>
					<td colspan="2">'.$sRes.'</td>
					</tr>';
				
				$sDescription.='<tr>
					<td colspan="4">'.$sDesc.'</td>
					</tr>'; 							
			}
			else
			{
			  $sDescription.= '<tr>
					<td>'.$i.'</td>
					<td>'.$aName[$i].'</td>
					<td><div id="morris-chart-area"><p style="word-wrap: break-word;">'.$sRes.'</p></div></td>
					<td><p style="word-wrap: break-word;">'.$sDesc.'</p></td>
					</tr>';
			}
		}
		
		$arrResponse['description'] = 	$sDescription;
		
		echo json_encode($arrResponse);
		exit;
       
	}
	//START: Get the real responce for the system.
	
	public function savePositionAjax()
	{
		$sPositionName = $this->input->post('sPositionName');
		$sPositionActive = $this->input->post('sPositionActive');
		
		$this->load->model('user_model');
		
		$this->user_model->savePosition(array('sPositionName'=>$sPositionName,'sPositionActive'=>$sPositionActive));
		
		echo $this->db->insert_id();
		
		exit;
	}
	
}//END : Class Home

/* End of file home.php */
/* Location: ./application/controllers/home.php */
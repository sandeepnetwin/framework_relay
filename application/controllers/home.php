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
	protected $userID,$aPermissions,$aModules,$aAllActiveModule;
	
    public function __construct()  
    {
        parent::__construct();
		$this->load->library('form_validation');
        $this->load->helper('common_functions'); //Common functions will be available for all functions in the file.
		if (!$this->session->userdata('is_admin_login')) //START : Check if user login or not.
        {
            redirect('dashboard/login/');
			die;
        } //END : Check if user login or not.
		
		
		//Get Permission Details
		if($this->userID == '')
		$this->userID = $this->session->userdata('id');
		
		if($this->aPermissions == '')
		{
			$this->aPermissions 	= json_decode(getPermissionOfModule($this->userID));
			$this->aModules 		= $this->aPermissions->sPermissionModule;	
			$this->aAllActiveModule = $this->aPermissions->sActiveModule;
		}
	}

    public function index() //START : Function for dashboard
    {
        $aViewParameter         =   array(); // Array for passing parameter to view.
        $aViewParameter['page'] =   'home'; 
        
        //Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
        
        //$sResponse    =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves']; // Valve Device Status
        $sRelays        =   $sResponse['relay'];  // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
        $sTime          =   $sResponse['time']; // Server Time from Response
        
        //Pump device Status
        $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
        // Temperature Sensor Device 
		$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);
			
        //START : Parameter for View
            $aViewParameter['relay_count']  =   strlen($sRelays);
            $aViewParameter['valve_count']  =   strlen($sValves);
            $aViewParameter['power_count']  =   strlen($sPowercenter);
			
            $aViewParameter['pump_count']   		=   count($sPump);
			$aViewParameter['temprature_count']   	=   count($sTemprature);
            $aViewParameter['time']         =   $sTime;
        //END : Parameter for View
		
		//START: GET the active MODE details.
			$aViewParameter['welcome_message'] = '';
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
				
				$sExtra = '';
				
				if($extra['Pool_Temp'] == '1' && isset($extra['Pool_Temp']))
				{
					if(isset($extra['Pool_Temp_Address']) && $extra['Pool_Temp_Address'] != '' && $sResponse[$extra['Pool_Temp_Address']] != '')
					{
						$strMessage.=' <strong>Pool temperature is '.$sResponse[$extra['Pool_Temp_Address']].'.</strong>';
						
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
						$sExtra .='<br><br>';
				
					
				$aViewParameter['sTemperature'] = '<p style="font-size:20px">'.$sExtra.'</p>';
				$aViewParameter['Remote_Spa'] = isset($extra['Remote_Spa'])?$extra['Remote_Spa']:0;
				
				$aViewParameter['welcome_message'] = $strMessage;
			}
			
			
			
		//END: GET the active MODE details.
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
		//Home View
        $this->load->view('Home',$aViewParameter);
		


    } //END : Function for dashboard
    
    public function setting() //START : Function to show Setting Page or Device Type Page
    {
        $aViewParameter                 =   array(); // Array for passing parameter to view.
        $aViewParameter['page']         =   'home';
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
		
        
        //Get the type of the device to show page.
        $sPage  =   $this->uri->segment('3'); 

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
                $sIP    =   $this->input->post('relay_ip_address');
                $sPort  =   $this->input->post('relay_port_no');
				
				//Get whether to show temperature on Home page.
				$sPoolTemp  =   $this->input->post('showPoolTemp');
				$sSpaTemp  	=   $this->input->post('showSpaTemp');
				
				//Get the temperature address
				$sPoolTempAddress	=	'';
				$sSpaTempAddress	=	'';
				
				if($sPoolTemp == '1')
					$sPoolTempAddress   =   $this->input->post('selPoolTemp');
				if($sSpaTemp == '1')
					$sSpaTempAddress	=   $this->input->post('selSpaTemp');
			
				//Get the number of pumps and valve.
					$iNumPumps  =   $this->input->post('numPumps');
					$iNumValve  =   $this->input->post('numValve');
				
				$aNumDevice     =   array();
				if($iNumPumps != '')	
					$aNumDevice['PumpsNumber']     =   $iNumPumps;
				else
					$aNumDevice['PumpsNumber']     =   0;
				
				if($iNumValve != '')	
					$aNumDevice['ValveNumber']     =   $iNumValve;
				else
					$aNumDevice['ValveNumber']     =   0;
				
				$showRemoteSpa = $this->input->post('showRemoteSpa');
				
				//Get Manual Mode Minutes
				$sManualModeTime   =   $this->input->post('manualMinutes');
			
			    //Check for IP constant if IP is blank in POST
                if($sIP == '')
                {
                    if(IP_ADDRESS)
                        $sIP = IP_ADDRESS;
                }
                
                //Check for PORT number constant if PORT is blank in POST
                if($sPort == '')
                {   
                    if(PORT_NO)
                        $sPort = PORT_NO;
                }
                
                if($sIP == '' || $sPort == '') //IF Still IP or PORT is blank, error flag is set to 1.
                {
                    $aViewParameter['err_sucess']    =   '1';
                }
                else 
                {
                    //Save IP and PORT
                    $this->home_model->updateSetting($sIP,$sPort);
                } // END : else for if($sIP == '' || $sPort == '')
				
				//Save the Temperature related Details.
				$aTemprature	=	array('Pool_Temp'=>$sPoolTemp,'Pool_Temp_Address'=>$sPoolTempAddress,'Spa_Temp'=>$sSpaTemp,'Spa_Temp_Address'=>$sSpaTempAddress);
				$this->home_model->updateSettingTemp($aTemprature);
				
				//Save Manual Mode Time.
				$this->home_model->updateManualModeTime($sManualModeTime);
				
				//Save Number of Devices and Spa Remote
				$this->home_model->updateSettingNumberDevice($aNumDevice,$showRemoteSpa);
				
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
            
			$sResponse      =   get_rlb_status();
			
			$aViewParameter['aTemprature'] = array('TS0'=>$sResponse['TS0'],'TS1'=>$sResponse['TS1'],'TS2'=>$sResponse['TS2'],'TS3'=>$sResponse['TS3'],'TS4'=>$sResponse['TS4'],'TS5'=>$sResponse['TS5']); 
			
			$aViewParameter['manualMinutes'] = $this->home_model->getManualModeTime();
			
			//Permission related parameters.
			$aViewParameter['userID'] 			= $this->userID;
			$aViewParameter['aModules'] 		= $this->aModules;
			$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
			//View Setting
            $this->load->view('Setting',$aViewParameter);
        } // END : If no device type then show setting page. if($sPage == '')
        else //START : If device type is available then device page.
        {
            //Check if IP, PORT and Mode is set or not.
            $this->checkSettingsSaved();
            
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
				
            //Get the status response of devices from relay board.
            $sResponse      =   get_rlb_status();
            //$sResponse      =   array('valves'=>'','powercenter'=>'0000','time'=>'','relay'=>'0000');
            
            $sValves        =   $sResponse['valves']; // Valve Device Status
            $sRelays        =   $sResponse['relay'];  // Relay Device Status
            $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
            $sTime          =   $sResponse['time']; // Server time from Response
            
            // Pump Device Status
            $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
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

                $aViewParameter['sDevice']          =   $sPage;
            //END : Parameter for View
            
			//Permission related parameters.
			$aViewParameter['userID'] 			= $this->userID;
			$aViewParameter['aModules'] 		= $this->aModules;
			$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
			
            // Device View to show device page.
            $this->load->view('Device',$aViewParameter); 
        } //END : If device type is available then device page. Else for if($sPage == '')
    } //END : Function to show Setting Page or Device Type Page

    public function updateStatusOnOff() //START : Function to swich the particular device ON/OFF
    {
        //Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
        
        //$sResponse      =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves'];   // Valve Device Status
        $sRelays        =   $sResponse['relay'];    // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
        
        //Get the Device details from POST whose status will be changed.
        $sName          =   $this->input->post('sName'); //Device Number
        $sStatus        =   $this->input->post('sStatus'); //Change status for Device
        $sDevice        =   $this->input->post('sDevice'); //Device Type

        $sNewResp       =   '';
           
        $this->load->model('home_model');
        
        //R = Relay, P = PowerCenter, V = Valve, PS = Pumps
        if($sDevice == 'R') // If Device type is Relay
        {
            $sNewResp = replace_return($sRelays, $sStatus, $sName );
            onoff_rlb_relay($sNewResp);
            $this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
			
			//Check if the relay is assigned to pump or not.
			$aPumpDetails =	$this->home_model->getPumpDetailsFromRelayNumber($sName,'24');
			if(!empty($aPumpDetails))
			{
				foreach($aPumpDetails as $sPump)
				{
					$this->makePumpOnOFF($sPump->pump_number,$sStatus);
				}
			}
            
        }
        if($sDevice == 'P') // If Device type is Power Center
        {
            $sNewResp = replace_return($sPowercenter, $sStatus, $sName );
            onoff_rlb_powercenter($sNewResp);
			
			//Check if the relay is assigned to pump or not.
			$aPumpDetails =	$this->home_model->getPumpDetailsFromRelayNumber($sName,'12');
			if(!empty($aPumpDetails))
			{
				foreach($aPumpDetails as $sPump)
				{
					$this->makePumpOnOFF($sPump->pump_number,$sStatus);
				}
			}
        }
        if($sDevice == 'V') // If Device type is Valve
        {
            $sNewResp = replace_return($sValves, $sStatus, $sName );
            onoff_rlb_valve($sNewResp);
        }
        if($sDevice == 'PS') // If Device type is Pump
        {
			$this->makePumpOnOFF($sName,$sStatus);
		}
        exit;
    } //END : Function to swich the particular device ON/OFF
	
	
	public function makePumpOnOFF($sName,$sStatus)
	{
		$sResponse      =   get_rlb_status();
        
        $sRelays        =   $sResponse['relay'];    // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
		$sDevice = 'PS';
		
		if($sDevice == 'PS') // If Device type is Pump
        {
			$aPumpDetails = $this->home_model->getPumpDetails($sName);
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
						onoff_rlb_relay($sNewResp);
						$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
					}
					else if($sPumpType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp);
					}
					if($sPumpType == '2Speed')
					{
						if($sPumpSubType == '24')
						{
							if($sStatus == '0')
							{
								$sNewResp = replace_return($sRelays, 0, $sRelayNumber );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
							}
							if($sStatus == '1')
							{
								$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
							}
							if($sStatus == '2')
							{	
								$sStatus = '1';
								$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber1 );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
							}
							
						}
						else if($sPumpSubType == '12')
						{
							if($sStatus == '0')
							{
								$sNewResp = replace_return($sPowercenter, '0', $sRelayNumber );
								onoff_rlb_powercenter($sNewResp);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_powercenter($sNewResp);
							}
							if($sStatus == '1')
							{
								$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
								onoff_rlb_powercenter($sNewResp);
							}
							if($sStatus == '2')
							{	
								$sStatus = '1';
								$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber1 );
								onoff_rlb_powercenter($sNewResp);
								
								$sNewResp = replace_return($sNewResp, '0', $sRelayNumber );
								onoff_rlb_powercenter($sNewResp);
								
								$sStatus = '2';
							}
							
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
						
						onoff_rlb_pump($sNewResp);
						
						if($sPumpType == 'Emulator12')
						{
							$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp12);
						}
						if($sPumpType == 'Emulator24')
						{
							$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp24);
							$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus);
						}
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
						
						onoff_rlb_pump($sNewResp);
						
						if($sPumpType == 'Intellicom12')
						{
							$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp12);
						}
						if($sPumpType == 'Intellicom24')
						{
							$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
							onoff_rlb_relay($sNewResp24);
							$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus);
						}
					}
				}
				
				$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
			}				
           
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
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;

        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') //START : If device name form is POSTED.
        {
            // Get the device name From POST.
            $sDeviceName = $this->input->post('sDeviceName');
            //Save device name
            $this->home_model->saveDeviceName($sDeviceID,$sDevice,$sDeviceName);

            $aViewParameter['sucess']    =   '1';//Set success parameter.
        } //END : If device name form is POSTED.
        // Get the saved device name
        $aViewParameter['sDeviceName']      =   $this->home_model->getDeviceName($sDeviceID,$sDevice);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        // Device Name save View
        $this->load->view('DeviceName',$aViewParameter); 
    } // END : Function Show Device Name Form and Save
    
    public function addTime() //START : Function to save/update the Relay Time.
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Program Max Time For 24V AC Relays';
        
        //Get Device ID and Device Type From URL.
        $sDeviceID  =   base64_decode($this->uri->segment('3'));
        $sDevice    =   base64_decode($this->uri->segment('4'));
       
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
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;
        
        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') //START : If device name form is POSTED.
        {
            // Get the device name From POST.
            $sDeviceTime = $this->input->post('sDeviceTime');
            //Save device name
            $this->home_model->saveDeviceTime($sDeviceID,$sDevice,$sDeviceTime);

            $aViewParameter['sucess']    =   '1';//Set success parameter.
        } //END : If device name form is POSTED.
        // Get the saved device name
        $aViewParameter['sDeviceTime']      =   $this->home_model->getDeviceTime($sDeviceID,$sDevice);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        // Time save/update View
        $this->load->view('Time',$aViewParameter); 
    }//END : Function to save/update the Relay Time.


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
        $this->load->view('PositionName',$aViewParameter); 
    } //END : Function to save position names for Valve
	
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
                redirect(site_url('home/setting/R'));
        }
       
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        
        if($this->input->post('command') == 'Save') // START : Save program details.
        {
            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber');

            $this->home_model->saveProgramDetails($this->input->post(),$sDeviceID,'PS');
            $aViewParameter['sucess']    =   '1';
        }// END : Save program details.

        if($this->input->post('command') == 'Update') // START : Update program details.
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
            redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
        }// END : Update program details.

        if($sProgramDelete != '' && $sProgramDelete == 'D') // START : Delete program details.
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
            }

            $this->home_model->deleteProgramDetails($sProgramID);
            redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
        } // START : Delete program details.

        // Get saved program details     
        $aViewParameter['sProgramDetails'] = $this->home_model->getProgramDetailsForDevice($sDeviceID,'PS');

        if($sProgramID != '') //If program exists the get program details.
        {
            $aViewParameter['sProgramID'] = $sProgramID;
            $aViewParameter['sProgramDetailsEdit'] = $this->home_model->getProgramDetails($sProgramID);
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
        $this->load->view('PumpPrograms',$aViewParameter); 
    } // END : Function to save/update/delete the Programs to run relay in Auto mode.
	
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

            $this->home_model->saveProgramDetails($this->input->post(),$sDeviceID,'R');
            $aViewParameter['sucess']    =   '1';
        }// END : Save program details.

        if($this->input->post('command') == 'Update') // START : Update program details.
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
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
        }// END : Update program details.

        if($sProgramDelete != '' && $sProgramDelete == 'D') // START : Delete program details.
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
            }

            $this->home_model->deleteProgramDetails($sProgramID);
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
        } // START : Delete program details.

        // Get saved program details     
        $aViewParameter['sProgramDetails'] = $this->home_model->getProgramDetailsForDevice($sDeviceID,'R');

        if($sProgramID != '') //If program exists the get program details.
        {
            $aViewParameter['sProgramID'] = $sProgramID;
            $aViewParameter['sProgramDetailsEdit'] = $this->home_model->getProgramDetails($sProgramID);
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
        $this->load->view('Programs',$aViewParameter); 
    } // END : Function to save/update/delete the Programs to run relay in Auto mode.

    public function pumpConfigure() // START : Function for saving Pump Configuration
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
		$aViewParameter['Title']     =   'Pump Configuration';
		$aViewParameter['err']		 =	'';
		
        $sDeviceID      =   base64_decode($this->uri->segment('3'));
       
        $this->load->model('home_model');

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page   
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') //START : Check if Device id is blank in POST then redirect to the device list page
                redirect(site_url('home/setting/PS/'));
        }

        if($this->input->post('command') == 'Save') // START : Save pump configuration Details.
        {
            if($this->input->post('sPumpNumber') != '')
                $sDeviceID   =  $this->input->post('sPumpNumber');
			
			//Make Pump OFF if the type selected is different from existing
				$sResponse      =   get_rlb_status();
        
				$sRelays        =   $sResponse['relay'];    // Relay Device Status
				$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
				
				$sDevice = 'PS';
		
				$aPumpDetails = $this->home_model->getPumpDetails($sDeviceID);
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
							onoff_rlb_relay($sNewResp);
							$this->home_model->updateDeviceRunTime($sDeviceID,$sDevice,$sStatus);
						}
						else if($sPumpType == '12')
						{
							$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
							onoff_rlb_powercenter($sNewResp);
						}
					}
					else
					{
						if(preg_match('/Emulator/',$sPumpType))
						{
							$sNewResp = '';
							$sNewResp =  $sDeviceID.' '.$sStatus;
							
							onoff_rlb_pump($sNewResp);
							
							if($sPumpType == 'Emulator12')
							{
								$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp12);
							}
							if($sPumpType == 'Emulator24')
							{
								$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp24);
								$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus);
							}
						}
						else if(preg_match('/Intellicom/',$sPumpType))
						{
							$sNewResp = '';
							$sNewResp =  $sDeviceID.' '.$sStatus;
							onoff_rlb_pump($sNewResp);
							
							if($sPumpType == 'Intellicom12')
							{
								$sNewResp12 = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp12);
							}
							if($sPumpType == 'Intellicom24')
							{
								$sNewResp24 = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp24);
								$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$sStatus);
							}
						}
					}
				}
			
            $this->home_model->savePumpDetails($this->input->post(),$sDeviceID);
			
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
						$sResult	=	assignAddressToPump($sDeviceID,$sAddress);
						if(!preg_match('/Invalid response/',$sRes))
						{}
						else
						{
							$aViewParameter['err']    =   'Following error occurs. '.$sResult;
						}
					}
				}
				else
				{
					$aViewParameter['err']    =   'Following error occurs. '.$sRes;
				}
			}
			
			$aViewParameter['sucess']    =   '1';
			
        }// END : Save pump configuration Details.
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sPumpDetails'] = 	$this->home_model->getPumpDetails($sDeviceID);
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        //Pump view for configuration of pumps
        $this->load->view('Pump',$aViewParameter); 
    } // END : Function for saving Pump Configuration

    public function checkSettingsSaved() //START : Check if IP and PORT is added.
    {
        $this->load->model('home_model');
        list($sIpAddress, $sPortNo) = $this->home_model->getSettings();
        
        //If IP is not saved check for IP constant
        if($sIpAddress == '')
        {
            if(IP_ADDRESS)
                $sIpAddress = IP_ADDRESS;
        }
       
        //If PORT is not saved check for PORT constant
        if($sPortNo == '')
        {   
            if(PORT_NO)
                $sPortNo = PORT_NO;
        }

        if($sIpAddress == '' || $sPortNo == '')
            redirect(site_url('home/setting/'));
    }//END : Check if IP and PORT is added.

    public function systemStatus() //START : Server response page of relay board.
    {
        $aViewParameter         	=   array(); // Array for passing parameter to view.
        $aViewParameter['page'] 	=   'status';
		$aViewParameter['Title'] 	=   'System Status Details';
        //Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();

        //Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
        
        //Parameter for view
        $aViewParameter['response'] =$sResponse['response'];
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule;
		
        //Status view for showing relay board status.
        $this->load->view('Status',$aViewParameter);
    } //END : Server response page of relay board.
		
	public function saveDevicePower()
	{
		//Get the input 
		$sDeviceID   =  $this->input->post('sDeviceID');
		$sDevice   	 =  $this->input->post('sDevice');
		$sPowerValue =  $this->input->post('sPowerValue');
		
		if($sDeviceID != '' && $sDevice != '' && $sPowerValue != '')
		{
			$this->load->model('home_model');
			$this->home_model->saveDevicePower($sDeviceID,$sDevice,$sPowerValue);
		}
	}
	public function saveDeviceMainType()
	{
		//Get the input 
		$sDeviceID   =  $this->input->post('sDeviceID');
		$sDevice   	 =  $this->input->post('sDevice');
		$sType =  $this->input->post('sType');
		
		if($sDeviceID != '' && $sDevice != '' && $sType != '')
		{
			$this->load->model('home_model');
			$this->home_model->saveDeviceMainType($sDeviceID,$sDevice,$sType);
		}
	}
	
	
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
	
	public function getLogDetails()
	{
		$aViewParameter['Title'] = 'Log Details';
		$dir    	= '/var/log/rlb/';
		//$dir    	= "D:\wamp\www\CodeIgniter-pool-spa\log\\rlb\\";
		
		$sFileName				=	'';
		$strTodaysLogDetails 	=	'';
		$strDate				=	'';
		
		$strStartDate			=	'';			
		$strEndDate				=	'';	
		
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
			
			$file = fopen($dir.$sFileName.'.log','r');		
			
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
        $this->load->view('Log',$aViewParameter);
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
		
		if($aResponse['iPumpCheck'] != 1 || $aResponse['iPumpCheck'] != 2)
		{
			$this->load->model('home_model');
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
		echo json_encode($aResponse);
		
		exit;
	}
	
	public function SpaDevice()
	{
		$aViewParameter                 =   array(); // Array for passing parameter to view.
        $aViewParameter['page']         =   'home';
        $aViewParameter['sucess']       =   '0';
		$aViewParameter['Title']        =   'Spa Devices';
        $aViewParameter['err_sucess']   =   '0';
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
		
		// Device View to show device page.
		$this->load->view('SpaDevice',$aViewParameter);
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
		
		// Device View to show device page.
		$this->load->view('PoolDevice',$aViewParameter);
	}
	
	
	public function responsetest()
	{
		$this->load->view('welcome_message');
	}
    
}//END : Class Home

/* End of file home.php */
/* Location: ./application/controllers/home.php */
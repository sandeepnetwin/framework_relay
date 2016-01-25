<?php
	
/**
* @Programmer: Dhiraj S.
* @Created: 13 July 2015
* @Modified: 
* @Description: Analog Controller for analog devices and Manual Mode.
**/
	
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH.'controllers/home.php'); 

class Analog extends CI_Controller 
{
	public $userID,$aPermissions,$aModules,$aAllActiveModule;
	 
    public function __construct() 
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->helper('common_functions');
		
		if (!$this->session->userdata('is_admin_login')) 
        {
            redirect('dashboard/login/');
			die;
        }
		
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
    
	//START : Function to save the analog input details. 
    public function index()
    {
        $aViewParameter =   array();
        
        $aViewParameter['page'] 	=	'home';
		$this->load->model('home_model');
        $this->load->model('analog_model');
        $aViewParameter['sucess'] 	=   '0';
		$aViewParameter['Title']    =   'Inputs';

		$aObjHome = new Home();   
        $aObjHome->checkSettingsSaved(); 
        
		//GET IP and Board Name.
		$aViewParameter['aIPDetails'] = $this->home_model->getBoardIP();
		list($sIP,$aViewParameter['sPort'],$sExtra) 	=	$this->home_model->getSettings();
		
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
			
			//Device Status is seperated.
			$sValves        =   $sResponse['valves'];
			$sRelays        =   $sResponse['relay'];
			$sPowercenter   =   $sResponse['powercenter'];
			
			$sPump          =   array($sResponse['pump_seq_0_st'],
									  $sResponse['pump_seq_1_st'],
									  $sResponse['pump_seq_2_st']);
									  
			$sRemote		=	substr($sResponse['remote_spa_ctrl_st'], 0, 4);

			$aViewParameter['sValves'.$IP->id]          =   $sValves;
			$aViewParameter['sRelays'.$IP->id]          =   $sRelays;
			$aViewParameter['sPowercenter'.$IP->id]     =   $sPowercenter; 
			$aViewParameter['sPump'.$IP->id]            =   $sPump;

			$aViewParameter['relay_count'.$IP->id]      =   strlen($sRelays);
			$aViewParameter['valve_count'.$IP->id]      =   strlen($sValves);
			$aViewParameter['power_count'.$IP->id]      =   strlen($sPowercenter);
			$aViewParameter['pump_count'.$IP->id]       =   count($sPump);
			
			
			//Get the details related to the analog devices.
			$aViewParameter['aAllAnalogDevice'.$IP->id]	= $this->analog_model->getAllAnalogDevice($IP->id);
			$aViewParameter['aAllANalogDeviceDirection'.$IP->id] = $this->analog_model->getAllAnalogDeviceDirection($IP->id);
			
			$aViewParameter['aAllAnalogDevicePort'.$IP->id] = $this->analog_model->getAllAnalogDevicePorts($IP->id);
			
			$aViewParameter['aResponse'.$IP->id] =	array('Remote Spa Control0' => $sRemote[0],
                                                  'Remote Spa Control1' => $sRemote[1],
                                                  'Remote Spa Control2' => $sRemote[2],
                                                  'Remote Spa Control3' => $sRemote[3]);
												  
												  
		}
		        
		//Check and Save the details related to the analog Input.
        if($this->input->post('command') == 'Save')
        {
			foreach($aViewParameter['aIPDetails'] as $IP)
			{
				$sDeviceName = $this->input->post('sDeviceName_'.$IP->id);
				$this->analog_model->saveAnalogDevice($sDeviceName,$IP->id);
			}
            $aViewParameter['sucess'] =   '1';
        }
				
		//Get the number of Devices(Blower,Light and Heater) from the settings.
		$aViewParameter['sExtra']	=	$sExtra;
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule; 	
		

        //Load View for showing the analog devices.
        $this->template->build('Analog',$aViewParameter);
    }
	
	//START : Function for changing the Mode and updating the details in the database.
    public function changeMode()
    {
		//Default Parameter for the View
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
        $aViewParameter['page']         =   'home';
		//1-auto, 2-manual, 3-timeout
		
        $this->load->model('home_model');
		$iActiveMode	= $this->home_model->getActiveMode();
		$sManualTime	= $this->home_model->getManualModeTime();
		
        if($this->input->post('iMode') != '')
        {
            $iMode = $this->input->post('iMode');
			if($iActiveMode != $iMode)
			{
				$this->home_model->updateMode($iMode);
				//If mode is manual then add the manual start time and end time calculated using the manual time(in minute) added on settings page.
				if($sManualTime != '')
				{
					if($iMode == 2)
					{
						$sProgramAbsStart =   date("H:i:s", time());
						$aStartTime       =   explode(":",$sProgramAbsStart);
						$sProgramAbsEnd   =   mktime(($aStartTime[0]),($aStartTime[1]+$sManualTime),($aStartTime[2]),0,0,0);
						$sAbsoluteEnd     =   date("H:i:s", $sProgramAbsEnd);
						$this->home_model->updateManualModeTimer($sProgramAbsStart,$sAbsoluteEnd);
					}
					else
					{
						$this->home_model->updateManualModeTimer('','');
					}	
				}
			}
			

            if($iMode == 3 || $iMode == 1)
            { 
				$sResponse      =   get_rlb_status();
				$sValves        =   $sResponse['valves'];
				$sRelays        =   $sResponse['relay'];
				$sPowercenter   =   $sResponse['powercenter'];
				
                //off all relays
                if($sRelays != '')
                {
                    $sRelayNewResp = str_replace('1','0',$sRelays);
                    onoff_rlb_relay($sRelayNewResp);
                }
                
                //off all valves
                if($sValves != '')
                {
                    $sValveNewResp = str_replace(array('1','2'), '0', $sValves);
                    onoff_rlb_valve($sValveNewResp);  
                }
                
                //off all power center
                if($sPowercenter != '')
                {
                    $sPowerNewResp = str_replace('1','0',$sPowercenter);  
                    onoff_rlb_powercenter($sPowerNewResp); 
                }

				//GET all Pump Devices.
				$aPumpDetails	=	$this->home_model->getAllPumpDetails();
				if(!empty($aPumpDetails))
				{
					foreach($aPumpDetails as $aPump)	
					{
						$sStatus	  = 0;
						$sPumpNumber  = $aResultEdit->pump_number;
						$sPumpType    = $aResultEdit->pump_type;
						$sPumpSubType = $aResultEdit->pump_sub_type;
						$sRelayNumber = $aResultEdit->relay_number;
						$sRelayNumber1 = $aResultEdit->relay_number_1;
						
						if($sPumpType == '12' || $sPumpType == '24' || $sPumpType == '2Speed')
						{
							if($sPumpType == '24')
							{
								$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
								
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
							else if($sPumpType == '12')
							{
								$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp);
								
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
							if($sPumpType == '2Speed')
							{
								if($sPumpSubType == '24')
								{
									$sNewResp = replace_return($sRelays, 0, $sRelayNumber );
									onoff_rlb_relay($sNewResp);
									$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
									
									$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
									onoff_rlb_relay($sNewResp);
									$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
									
									$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
									
								}
								else if($sPumpSubType == '12')
								{
									$sNewResp = replace_return($sPowercenter, '0', $sRelayNumber );
									onoff_rlb_powercenter($sNewResp);
									
									$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
									onoff_rlb_powercenter($sNewResp);
										
									$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
								}
							}
						}
						else
						{
							if(preg_match('/Emulator/',$sPumpType))
							{
								$sNewResp =  $sName.' '.$sStatus;
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
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
							else if(preg_match('/Intellicom/',$sPumpType))
							{
								$sNewResp =  $sName.' '.$sStatus;
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
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
						}
					}
				}
					
            }
             $aViewParameter['sucess']    =   '1';

        }

        $aViewParameter['iMode']  =   $this->home_model->getActiveMode();

        $this->template->build("Mode",$aViewParameter);

    }//END : Function for changing the Mode and updating the details in the database.
	
	//START : Function for Updating the manual mode details.
	public function changeModeManual()
    {
		$aViewParameter['Title']    =   'Modes';
		$userID = $this->session->userdata('id');
		
		$aPermissions 		= json_decode(getPermissionOfModule($userID));
		$aModules 			= $aPermissions->sPermissionModule;	
		$aAllActiveModule   = $aPermissions->sActiveModule;
		
		
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
        $aViewParameter['page']         =   'home';

        $this->load->model('home_model');
		$iActiveMode	= $this->home_model->getActiveMode();
		$sManualTime	= $this->home_model->getManualModeTime();
		
        if($this->input->post('iMode') != '')
        {
            $iMode = $this->input->post('iMode');
			if($iActiveMode != $iMode)
			{
				$this->home_model->updateMode($iMode);
				//If mode is manual then add the manual start time and end time calculated using the manual time(in minute) added on settings page.
				if($sManualTime != '')
				{
					if($iMode == 2)
					{
						$sProgramAbsStart =   date("H:i:s", time());
						$aStartTime       =   explode(":",$sProgramAbsStart);
						$sProgramAbsEnd   =   mktime(($aStartTime[0]),($aStartTime[1]+$sManualTime),($aStartTime[2]),0,0,0);
						$sAbsoluteEnd     =   date("H:i:s", $sProgramAbsEnd);
						$this->home_model->updateManualModeTimer($sProgramAbsStart,$sAbsoluteEnd);
					}
					else
					{
						$this->home_model->updateManualModeTimer('','');
					}	
				}
			}
			

            if($iMode == 3 || $iMode == 1)
            { //1-auto, 2-manual, 3-timeout
		
				$sResponse      =   get_rlb_status();
				$sValves        =   $sResponse['valves'];
				$sRelays        =   $sResponse['relay'];
				$sPowercenter   =   $sResponse['powercenter'];
				
                //off all relays
                if($sRelays != '')
                {
                    $sRelayNewResp = str_replace('1','0',$sRelays);
                    onoff_rlb_relay($sRelayNewResp);
                }
                
                //off all valves
                if($sValves != '')
                {
                    $sValveNewResp = str_replace(array('1','2'), '0', $sValves);
                    onoff_rlb_valve($sValveNewResp);  
                }
                
                //off all power center
                if($sPowercenter != '')
                {
                    $sPowerNewResp = str_replace('1','0',$sPowercenter);  
                    onoff_rlb_powercenter($sPowerNewResp); 
                } 
				
				//GET all Pump Devices and make them OFF.
				$aPumpDetails	=	$this->home_model->getAllPumpDetails();
				if(!empty($aPumpDetails))
				{
					foreach($aPumpDetails as $aPump)	
					{
						$sStatus	  = 0;
						$sPumpNumber  = $aResultEdit->pump_number;
						$sPumpType    = $aResultEdit->pump_type;
						$sPumpSubType = $aResultEdit->pump_sub_type;
						$sRelayNumber = $aResultEdit->relay_number;
						$sRelayNumber1 = $aResultEdit->relay_number_1;
						
						if($sPumpType == '12' || $sPumpType == '24' || $sPumpType == '2Speed')
						{
							if($sPumpType == '24')
							{
								$sNewResp = replace_return($sRelays, $sStatus, $sRelayNumber );
								onoff_rlb_relay($sNewResp);
								$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
								
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
							else if($sPumpType == '12')
							{
								$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
								onoff_rlb_powercenter($sNewResp);
								
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
							if($sPumpType == '2Speed')
							{
								if($sPumpSubType == '24')
								{
									$sNewResp = replace_return($sRelays, 0, $sRelayNumber );
									onoff_rlb_relay($sNewResp);
									$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
									
									$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
									onoff_rlb_relay($sNewResp);
									$this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
									
									$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
									
								}
								else if($sPumpSubType == '12')
								{
									$sNewResp = replace_return($sPowercenter, '0', $sRelayNumber );
									onoff_rlb_powercenter($sNewResp);
									
									$sNewResp = replace_return($sNewResp, '0', $sRelayNumber1 );
									onoff_rlb_powercenter($sNewResp);
										
									$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
								}
							}
						}
						else
						{
							if(preg_match('/Emulator/',$sPumpType))
							{
								$sNewResp =  $sName.' '.$sStatus;
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
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
							else if(preg_match('/Intellicom/',$sPumpType))
							{
								$sNewResp =  $sName.' '.$sStatus;
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
								$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							}
						}
					}
				}

            }
             $aViewParameter['sucess']    =   '1';

        }

        $aViewParameter['iMode']  		 	=   $this->home_model->getActiveMode();
		
		$aViewParameter['iModePoolSpa']  	=   $this->home_model->getActiveModePoolSpa();
		
		$aViewParameter['strModePoolSpa']  	=   $this->home_model->getModePoolSpa();
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $userID;
		$aViewParameter['aModules'] 		= $aModules;
		$aViewParameter['aAllActiveModule'] = $aAllActiveModule;
        $this->template->build("Mode",$aViewParameter);

    }//END : Function for Updating the manual mode details.
	
	//START : Function for Temperature Sensor Configuration.
	public function tempConfig()
	{
		$arrDetails					=	array();
		$aViewParameter['Title']    =   'Temprature Configure';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sucess'] 	=	'';
		
		$this->load->model(analog_model);
		$this->load->model(home_model);
		
		//Get Extra Details
		list($sIP,$sPort,$extra) = $this->home_model->getSettings();
		
		$iTempID	=	base64_decode($this->uri->segment('3')); 
		$sIpId		=	base64_decode($this->uri->segment('4')); 
		
		$iTempAction	=	'';
		if($this->uri->segment('4') != '' && $this->uri->segment('4') == 'remove')
		{
			$iTempAction	=	$this->uri->segment('4');
			$sIpId		=	base64_decode($this->uri->segment('5'));
		}
		
		//GET IP of Device
		$sDeviceIP		= 	$this->home_model->getBoardIPFromID($sIpId);
		
		$shhPort	=	'';
		if(IS_LOCAL == '1')
		{
			//Get SSH port of the RLB board using IP.
			$shhPort = $this->home_model->getSSHPortFromID($sIpId);
		}
		
		//Remove Bus Number
		if($iTempAction != '')
		{
			$busNumber	=	'0000000000000000';
			$this->analog_model->saveBusNumber($iTempID,'',$sIpId);
			
			//Configure bus to Temperature Sensors.
				$sResponse      =   configureTempratureBus('ts'.$iTempID,$busNumber,$sDeviceIP,$sPort,$shhPort);
				
			redirect(base_url('home/setting/T/'.base64_encode($sIpId)));
			exit;			
		}
		
		
		
		if($iTempID == '')
		{
			$iTempID 	= 	base64_decode($this->input->post('iTempID'));
		}
		if($sIpId == '')
		{
			$sIpId	=	base64_decode($this->input->post('iTempID'));
		}
		
		if($iTempID == '' || $sIpId == '')
		{
			redirect(base_url('home/setting/T/'));
			exit;
		}
		
		
		if($this->input->post('command') == 'Save')
		{
			$busNumber	=	$this->input->post('busConfigure');
			
			$this->analog_model->saveBusNumber($iTempID,$busNumber,$sIpId);
			
			//Configure bus to Temperature Sensors.
				$sResponse      =   configureTempratureBus('ts'.$iTempID,$busNumber,$sDeviceIP,$sPort,$shhPort);
				
			$aViewParameter['sucess'] 	=	'1';
				
		}
		
		//Get the bus number from the relayboard hardware to use with the temperature sensors.
		$sResponse      =   getTempratureBus($sDeviceIP,$sPort,$shhPort);
		
		/* echo $sIpId.'>>'.$sDeviceIP.'>>'.$sPort.'>>'.$shhPort;
		echo '<pre>';
		print_r($sResponse);
		echo '</pre>'; */
		
		if(!empty($sResponse))
		{
			$arrResponse	=	explode(",",$sResponse);
			foreach($arrResponse as $strRes)
			{
				if(preg_match('/TS28/',$strRes))
				{
					$arrDetails[] = $strRes;
				}
			}
		}
		
		$aViewParameter['bus'] 			=	$arrDetails;
		$aViewParameter['sDeviceID']	=	$iTempID;
		$aViewParameter['sIpId']		=	$sIpId;
		$aViewParameter['busNumber']	=	$this->analog_model->getBusNumber($iTempID,$sIpId);
		
		
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule; 	
		
		$this->template->build("Temprature",$aViewParameter);
	}//END : Function for Temperature Sensor Configuration.
	
	
	public function modePoolSpa()
	{
		$aViewParameter['Title']    =   'Pool/Spa Mode';
		$aViewParameter['page'] 	=	'home';
		
		$this->load->model('analog_model');
		
		//Get All Details first.
		$arrModeDetails	=	$this->analog_model->getPoolSpaModeDetails();
		
		$arrGeneral		=	array();
		$arrDevice		=	array();
		$arrHeater		=	array();
		$arrMore		=	array();

		if(!empty($arrModeDetails))	
		{
			foreach($arrModeDetails as $arrDetails)
			{
				$arrGeneral		=	unserialize($arrDetails->general);
				$arrDevice		=	unserialize($arrDetails->device);
				$arrHeater		=	unserialize($arrDetails->heater);
				$arrMore		=	unserialize($arrDetails->more);
			}
		}
		
		/* echo '<pre>';
			print_r($arrGeneral);
			print_r($arrDevice);
			print_r($arrHeater);
			print_r($arrMore);
		echo '</pre>'; */
		
		/* $arrTemp	=	array();
		if(isset($arrDevice['valve']) && ($arrDevice['valve'] != 0 && $arrDevice['valve'] != ''))
		{
			$arrTemp['valve']	=	$arrDevice['valveSequence'];
		}
		if(isset($arrDevice['pump']) && ($arrDevice['pump'] != 0 && $arrDevice['pump'] != ''))
		{
			$arrTemp['pump']	=	$arrDevice['pumpSequence'];
		}
		if(isset($arrHeater['heater']) && ($arrHeater['heater'] != 0 && $arrHeater['heater'] != ''))
		{
			$arrTemp['heater']	=	$arrHeater['heaterSequence'];
		}
		if(isset($arrMore['light']) && ($arrMore['light'] != 0 && $arrMore['light'] != ''))
		{
			$arrTemp['light']	=	$arrMore['lightSequence'];
		}
		if(isset($arrMore['blower']) && ($arrMore['blower'] != 0 && $arrMore['blower'] != ''))
		{
			$arrTemp['blower']	=	$arrMore['blowerSequence'];
		}
		
		//Sort devices using sequences.
		asort($arrTemp);
				
		//START : Create Device list as per the Sequences.
		$aViewParameter['arrDeviceOnPage']	= $arrTemp; */
		
		$aViewParameter['arrGeneral']	= $arrGeneral;
		$aViewParameter['arrDevice']	= $arrDevice;
		$aViewParameter['arrHeater']	= $arrHeater;
		$aViewParameter['arrMore']		= $arrMore;
				
		$this->template->build("PoolSpa",$aViewParameter);
	}
	
	//START :Function for Manual Mode Page.
	public function manualMode()
	{
		$aViewParameter['page'] 	=	'home';
		
		$this->load->model('analog_model');
		$this->load->model('home_model');
		
		//Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
		
        $aViewParameter['sValves']		=	$sResponse['valves']; // Valve Device Status
		$aViewParameter['sRelays']  	=   $sResponse['relay'];  // Relay Device Status
        $aViewParameter['sPowercenter']	=   $sResponse['powercenter']; // Power Center Device Status
		//Pump device Status
        $aViewParameter['sPump']		=	array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		
		//Get All Details first.
		$arrModeDetails	=	$this->analog_model->getPoolSpaModeDetails();
		
		$arrGeneral		=	array();
		$arrDevice		=	array();
		$arrHeater		=	array();
		$arrMore		=	array();

		if(!empty($arrModeDetails))	
		{
			foreach($arrModeDetails as $arrDetails)
			{
				$arrGeneral		=	unserialize($arrDetails->general);
				$arrDevice		=	unserialize($arrDetails->device);
				$arrHeater		=	unserialize($arrDetails->heater);
				$arrMore		=	unserialize($arrDetails->more);
			}
		}
		
		$aViewParameter['arrGeneral']	= $arrGeneral;
		$aViewParameter['arrDevice']	= $arrDevice;
		$aViewParameter['arrHeater']	= $arrHeater;
		$aViewParameter['arrMore']		= $arrMore;
		
		$aViewParameter['Title']    	=   ucfirst($arrGeneral['type']).' Mode';
		$aViewParameter['iActiveMode']	=	$this->home_model->getActiveMode();
				
		$this->template->build("PoolSpa",$aViewParameter);
	}//END :Function for Manual Mode Page.
	
	//START: Function for working the Pool/Spa in auto Mode.
	public function MakeOnOffPoolSpaModeDevices()
	{
		$this->load->model('analog_model');
		$this->load->model('home_model');
		
		//Check if Pool/Spa Mode is ON.
		$isPoolSpaModeOn	=	$this->analog_model->checkPoolSpaModeOn();
		
		if($isPoolSpaModeOn != '' && $isPoolSpaModeOn > 0)
		{
			//Get All Details first.
			$arrModeDetails	=	$this->analog_model->getPoolSpaModeDetails();
			
			$arrGeneral		=	array();
			$arrDevice		=	array();
			$arrHeater		=	array();
			$arrMore		=	array();

			if(!empty($arrModeDetails))	
			{
				foreach($arrModeDetails as $arrDetails)
				{
					$arrGeneral		=	unserialize($arrDetails->general);
					$arrDevice		=	unserialize($arrDetails->device);
					$arrHeater		=	unserialize($arrDetails->heater);
					$arrMore		=	unserialize($arrDetails->more);
				}
			}
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status();
			
			//$sResponse    =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
			$sValves        =   $sResponse['valves']; // Valve Device Status
			$sRelays        =   $sResponse['relay'];  // Relay Device Status
			$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
			$sTime          =   $sResponse['time']; // Server Time from Response
			
			//Pump device Status
			$sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
			
			//START : Parameter for View
			$aViewParameter['relay_count']  =   strlen($sRelays);
			$aViewParameter['valve_count']  =   strlen($sValves);
			$aViewParameter['power_count']  =   strlen($sPowercenter);
			
			//Get unique id first.
			$unique_id	=	$this->analog_model->getPoolSpaModeUnique();
			
			$valveAssign	=	unserialize($arrDevice['valveAssign']);
			
			//Remove valve Acculated.
			$valve_actuated	=	$arrDevice['valve_actuated'];
			foreach($valve_actuated as $key => $valve)
			{
				if(in_array($valve,$valveAssign))
					unset($valveAssign[$key]);
			}
			
			$valveAssign = array_values($valveAssign);
			
			$pumpAssign		=	array();
			$heaterAssign	=	unserialize($arrHeater['heaterAssign']);
			
			for($i=1;$i<=$arrHeater['heater'];$i++)
			{
				$pumpAssign[] = $arrHeater['HeaterPump'.$i];
			}
			
			//Check if device is running.
			$currentDevice = $this->analog_model->getPoolCurrentDevice($isPoolSpaModeOn,$unique_id);
			
			if($currentDevice == '')
			{
				//START: First make valve device ON in the sequence.
				$sStatus		=	'1';
				$sRunTime		=	'';
				$deviceType 	=   '';
				$deviceNumber	=	'';
				
				//First check How many valves are already OFF from total Valve.
				$totalCompleteDevice		=   $this->analog_model->getCompleteDeviceOfType($isPoolSpaModeOn,$unique_id,'V');
			
				
				if($totalCompleteDevice != $arrDevice['valve'])
				{
					$deviceType 	=   'V';
					$deviceNumber	=	$valveAssign[0];
					
					//Get the pool position so that direction of the valve will be ON.
					$arrPosition = $this->home_model->getPositionName($deviceNumber,'V');
					
					$getPoolPosition	=	array_search(2,$arrPosition);
					
					if($getPoolPosition == 0)
					{
						$sStatus	=	'1';
					}
					else if($getPoolPosition == 1)
					{
						$sStatus	=	'2';
					}
					
					$sNewResp = replace_return($sValves, $sStatus, $deviceNumber );
					onoff_rlb_valve($sNewResp);
						
					$sRunTime	=	$arrDevice['valveRunTime'];
					$strDevice	=	'Valve '.$deviceNumber;	
					
					//Make Entry in the Current Running Device Table.
					$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strDevice,$sRunTime,$deviceType,$deviceNumber,1);
				}
			}
			else if($currentDevice != '')
			{
				foreach($currentDevice as $deviceDetails)
				{
					//If Device is Running Then Check if its Time is Complete.
					$OffTime	=	$deviceDetails->current_off_time;
					$currentServerTime	=	date('Y-m-d H:i:s');
					
					if($currentServerTime >= $OffTime)
					{
						$deviceDetails->device_type;
						//Make Device Off and Turn the next Device ON.
						$remainingDevice	=	0;
						$sRunTime			=	'';
						$deviceType 		=   '';
						$deviceNumber		=	'';
						
						if($deviceDetails->device_type == 'V')
						{
							$sNewResp = replace_return($sValves, '0', $deviceDetails->device_number);
							onoff_rlb_valve($sNewResp);
							
							//Insert Entry in the Log Table for future Reference.
							$this->analog_model->saveEntryInLog($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							//Delete Entry From the current details Table.
							$this->analog_model->deleteEntryFromCurrent($isPoolSpaModeOn,$unique_id);
							
							//First check How many valves are already OFF from total Valve.
							$totalCompleteDevice	=   $this->analog_model->getCompleteDeviceOfType($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							if($totalCompleteDevice == '')
								$totalCompleteDevice = 0;
							
							$remainingDevice	=	($arrDevice['valve'] - $totalCompleteDevice);
							
							if($remainingDevice > 0)
							{
								$Current = array_search($deviceDetails->device_number,$valveAssign); 
								while (key($valveAssign) !== $Current) next($valveAssign);
								
								$deviceType 	=   'V';
								$deviceNumber	=	next($valveAssign);
								
								//Get the pool position so that direction of the valve will be ON.
								$arrPosition = $this->home_model->getPositionName($deviceNumber,'V');
								
								$getPoolPosition	=	array_search(2,$arrPosition);
								$sStatus			=	'';
								
								if($getPoolPosition == 0)
								{
									$sStatus	=	'1';
								}
								else if($getPoolPosition == 1)
								{
									$sStatus	=	'2';
								}
								
								$strNextDevice	=	'Valve'.(str_replace('Valve','',$deviceNumber));
																
								$sNewResp = replace_return($sValves, $sStatus, $deviceNumber );
								onoff_rlb_valve($sNewResp);
								
								$sRunTime	=	$arrDevice['valveRunTime'];
								
								//Make Entry in the Current Running Device Table.
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strNextDevice,$sRunTime,$deviceType,$deviceNumber,$deviceDetails->current_sequence);
							}
							else
							{
								echo 'All Valve OFF now ON PUMP.<br>';
								$deviceType 	=   'PS';
								$deviceNumber	=	$pumpAssign[0];
								
								$this->makePumpOnOFFAnalog($deviceNumber,'1');
								
								$sRunTime		=	1; //Pump Run Time 1 minute
								$strDevice		=	'Pump '.$deviceNumber;
								
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strDevice,$sRunTime,$deviceType,$deviceNumber,1);
							} 
						}
						if($deviceDetails->device_type == 'PS')
						{
							//Insert Entry in the Log Table for future Reference.
							$this->analog_model->saveEntryInLog($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							//Delete Entry From the current details Table.
							$this->analog_model->deleteEntryFromCurrent($isPoolSpaModeOn,$unique_id);
							
							//First check How many valves are already OFF from total Valve.
							$totalCompleteDevice	=   $this->analog_model->getCompleteDeviceOfType($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							if($totalCompleteDevice == '')
								$totalCompleteDevice = 0;
							
							$remainingDevice	=	($arrDevice['heater'] - $totalCompleteDevice);
							
							if($remainingDevice > 0)
							{
								echo 'Next PUMP is ON.<br>';
								$Current = array_search($deviceDetails->device_number,$pumpAssign); 
								while (key($pumpAssign) !== $Current) next($pumpAssign);
								
								$deviceType 	=   'PS';
								$deviceNumber	=	next($pumpAssign);
								
								$strNextDevice	=	'Pump'.(str_replace('Pump','',$deviceNumber));
								
								$this->makePumpOnOFFAnalog($deviceNumber,'1');
								
								$sRunTime	=	1;
								
								//Make Entry in the Current Running Device Table.
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strNextDevice,$sRunTime,$deviceType,$deviceNumber,$deviceDetails->current_sequence);
							}
							else
							{
								echo 'All PUMP ON now ON Heater.<br>';
								$deviceType 	=   'H';
								$deviceNumber	=	$heaterAssign[0];
								
								$this->makeHeaterOnOFF($deviceNumber,'1');
																
								$sRunTime		=	1; //Pump Run Time 1 minute
								$strDevice		=	'Heater '.$deviceNumber;
								
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strDevice,$sRunTime,$deviceType,$deviceNumber,1);
							}
						}
						if($deviceDetails->device_type == 'H')
						{
							echo 'Check For Time.<br>';
						}
					}
				}
			}				
		}
	}//END: Function for working the Pool/Spa in auto Mode.
	
	//START: Function for making PUMP ON/OFF from the analog input.
	public function makePumpOnOFFAnalog($sName,$sStatus)
	{
		//$this->model->load('home_model');
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
						
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
					}
					else if($sPumpType == '12')
					{
						$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
						onoff_rlb_powercenter($sNewResp);
						
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
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
							
							$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							
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
							
							$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
							
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
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
						
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
						
						$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
					}
				}
				
				//$this->home_model->updateDeviceStauts($sName,$sDevice,$sStatus);
			}				
           
        }
		
	}//END: Function for making PUMP ON/OFF from the analog input.
	
	//START : Function for Light Deviece Page.
	public function showLight()
	{
		$aViewParameter['Title']    =   'Lights';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sDevice'] 	=	'L';
		
		$this->load->model('home_model');
		
		$sIpID	=	base64_decode($this->uri->segment('3'));
		$aViewParameter['BackToIP'] = $sIpID;
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
			
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
			$sResponse		=	array();
			$sRelays        =   '';  
			$sPowercenter   =   ''; 
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($IP->ip,$aViewParameter['sPort'],$shhPort);
		
			$sRelays        =   $sResponse['relay'];  // Relay Device Status
            $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
			//START : Parameter for View
			$aViewParameter['relay_count'.$IP->id]      =   strlen($sRelays);
			$aViewParameter['power_count'.$IP->id]      =   strlen($sPowercenter);
			
			$aViewParameter['sRelays'.$IP->id]          =   $sRelays; 
			$aViewParameter['sPowercenter'.$IP->id]     =   $sPowercenter;
			
			$tempIPID	=	($IP->id == '1') ? '' : $IP->id;
			$aViewParameter['numLight'.$IP->id]         =	'';
		
			if(isset($extra['LightNumber']))
				$aViewParameter['numLight'.$IP->id]     =	$extra['LightNumber'.$tempIPID];
		}
		
		
		
		//Current mode of the system.
		$aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
         
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule; 	
		
        $this->template->build("Light",$aViewParameter);
	}//END : Function for Light Deviece Page.
	
	//START: Function for Heater Device Page.
	public function showHeater()
	{
		$aViewParameter['Title']    =   'Heater';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sDevice'] 	=	'H';
		
		$this->load->model('home_model');
		
		$sIpID	=	base64_decode($this->uri->segment('3'));
		$aViewParameter['BackToIP'] = $sIpID;
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
			
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
			$sResponse		=	array();
			$sRelays        =   '';  
			$sPowercenter   =   ''; 
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($IP->ip,$aViewParameter['sPort'],$shhPort);
		
			$sRelays        =   $sResponse['relay'];  // Relay Device Status
			$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
			//START : Parameter for View
			$aViewParameter['relay_count'.$IP->id]      =   strlen($sRelays);
			$aViewParameter['power_count'.$IP->id]      =   strlen($sPowercenter);
			
			$aViewParameter['sRelays'.$IP->id]          =   $sRelays; 
			$aViewParameter['sPowercenter'.$IP->id]     =   $sPowercenter;
			
			$tempIPID	=	($IP->id == '1') ? '' : $IP->id;
			$aViewParameter['numHeater'.$IP->id]         =	'';
		
			if(isset($extra['HeaterNumber']))
				$aViewParameter['numHeater'.$IP->id]     =	$extra['HeaterNumber'.$tempIPID];
		}
		
		//Current mode of the system.
		$aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();

		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule; 		
        
        $this->template->build("Heater",$aViewParameter);
	}//END: Function for Heater Device Page.
	
	//START: Function for Blower Device Page.
	public function showBlower()
	{
		$aViewParameter['Title']    =   'Blower';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sDevice'] 	=	'B';
		
		$this->load->model('home_model');
		
		$sIpID	=	base64_decode($this->uri->segment('3'));
		$aViewParameter['BackToIP'] = $sIpID;
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
			
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
			$sResponse		=	array();
			$sRelays        =   '';  
			$sPowercenter   =   ''; 
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($IP->ip,$aViewParameter['sPort'],$shhPort);
			
			$sRelays        =   $sResponse['relay'];  // Relay Device Status
			$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
			
			//START : Parameter for View
			$aViewParameter['relay_count'.$IP->id]      =   strlen($sRelays);
			$aViewParameter['power_count'.$IP->id]      =   strlen($sPowercenter);
			
			$aViewParameter['sRelays'.$IP->id]          =   $sRelays; 
			$aViewParameter['sPowercenter'.$IP->id]     =   $sPowercenter;
			
			$tempIPID	=	($IP->id == '1') ? '' : $IP->id;
			$aViewParameter['numBlower'.$IP->id]         =	'';
					
			if(isset($extra['BlowerNumber']))
				$aViewParameter['numBlower'.$IP->id]     =	$extra['BlowerNumber'.$tempIPID];
			
			
		}
		
		//Current mode of the system.
		$aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
        
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule; 		
		
        $this->template->build("Blower",$aViewParameter);
	}//END: Function for Blower Device Page.
	
	//START: Function for Misc Device Page.
	public function showMisc()
	{
		$aViewParameter['Title']    =   'Miscelleneous';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sDevice'] 	=	'M';
		
		$this->load->model('home_model');
		
		$sIpID	=	base64_decode($this->uri->segment('3'));
		$aViewParameter['BackToIP'] = $sIpID;
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
			
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
			$sResponse		=	array();
			$sRelays        =   '';  
			$sPowercenter   =   ''; 
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($IP->ip,$aViewParameter['sPort'],$shhPort);
		
			$sRelays        =   $sResponse['relay'];  // Relay Device Status
			$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
			
			//START : Parameter for View
			$aViewParameter['relay_count'.$IP->id]      =   strlen($sRelays);
			$aViewParameter['power_count'.$IP->id]      =   strlen($sPowercenter);
			
			$aViewParameter['sRelays'.$IP->id]          =   $sRelays; 
			$aViewParameter['sPowercenter'.$IP->id]     =   $sPowercenter;
			
			$tempIPID	=	($IP->id == '1') ? '' : $IP->id;
			$aViewParameter['numMisc'.$IP->id]         =	'';
			
			if(isset($extra['MiscNumber']))
				$aViewParameter['numMisc'.$IP->id]     =	$extra['MiscNumber'.$tempIPID];
		
		}
		//Current mode of the system.
		$aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
		
		//Permission related parameters.
		$aViewParameter['userID'] 			= $this->userID;
		$aViewParameter['aModules'] 		= $this->aModules;
		$aViewParameter['aAllActiveModule'] = $this->aAllActiveModule; 	
		   
        $this->template->build("Misc",$aViewParameter);
	}//START: Function for Misc Device Page.
	
	//START: Function for Light Remove.
	public function removeLight()
	{
		$lightNumber	=	$this->input->post('lightNumber');
		$ipID			=	$this->input->post('ipID');
		
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
		//System real response is taken.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
		//Device Status is seperated.
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
		
		$sLightStatus	=	0;
		
		//First check if relay related to that light is ON/OFF.
		$aLightDetails  =   $this->home_model->getLightDeviceDetails($lightNumber,$ipID);
		if(!empty($aLightDetails))
		{
			foreach($aLightDetails as $aLight)
			$sRelayDetails  =   unserialize($aLight->light_relay_number);
			
			$sRelayType     =   $sRelayDetails['sRelayType'];
			$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
			
			if($sRelayType == '24')
			{
				$sLightStatus   =   $sRelays[$sRelayNumber];
				//IF light is ON then make that light OFF.
				if($sLightStatus)
				{
					$sNewResp = replace_return($sRelays, "0", $sRelayNumber );
					onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
					$this->home_model->updateDeviceRunTime($sRelayNumber,'R',"0",$ipID);
				}
				
			}
			if($sRelayType == '12')
			{
				$sLightStatus   =   $sPowercenter[$sRelayNumber];
				if($sLightStatus)
				{
					$sNewResp = replace_return($sPowercenter, "0", $sRelayNumber );
					onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
				}
			}
		}
		
		//Remove the light from database.
		$this->load->model('analog_model');
		$this->analog_model->removeDeviceDetails($lightNumber,'L',$ipID);
		exit;
	}//END: Function for Light Remove.
	
	//START: Function for Blower Remove.
	public function removeBlower()
	{
		$blowerNumber	=	$this->input->post('blowerNumber');
		$ipID			=	$this->input->post('ipID');
		
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
		//System real response is taken.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
		//Device Status is seperated.
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
		
		$sBlowerStatus	=	0;
		
		//First check if relay related to that Blower is ON/OFF.
		$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($blowerNumber,$ipID);
		if(!empty($aBlowerDetails))
		{
			foreach($aBlowerDetails as $aBlower)
			$sRelayDetails  =   unserialize($aBlower->light_relay_number);
			
			$sRelayType     =   $sRelayDetails['sRelayType'];
			$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
			
			if($sRelayType == '24')
			{
				$sBlowerStatus   =   $sRelays[$sRelayNumber];
				//IF light is ON then make that light OFF.
				if($sBlowerStatus)
				{
					$sNewResp = replace_return($sRelays, "0", $sRelayNumber );
					onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
					$this->home_model->updateDeviceRunTime($sRelayNumber,'R',"0",$ipID);
				}
				
			}
			if($sRelayType == '12')
			{
				$sBlowerStatus   =   $sPowercenter[$sRelayNumber];
				if($sBlowerStatus)
				{
					$sNewResp = replace_return($sPowercenter, "0", $sRelayNumber );
					onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
				}
			}
		}
		
		//Remove the light from database.
		$this->load->model('analog_model');
		$this->analog_model->removeDeviceDetails($blowerNumber,'B',$ipID);
		exit;
	}//END: Function for Blower Remove.
	
	//START: Function for Heater Remove.
	public function removeHeater()
	{
		$heaterNumber	=	$this->input->post('heaterNumber');
		$ipID			=	$this->input->post('ipID');
		
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
		//System real response is taken.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
		//Device Status is seperated.
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
		
		$sBlowerStatus	=	0;
		
		//First check if relay related to that Heater is ON/OFF.
		$aBlowerDetails  =   $this->home_model->getHeaterDeviceDetails($heaterNumber,$ipID);
		if(!empty($aBlowerDetails))
		{
			foreach($aBlowerDetails as $aBlower)
			$sRelayDetails  =   unserialize($aBlower->light_relay_number);
			
			$sRelayType     =   $sRelayDetails['sRelayType'];
			$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
			
			if($sRelayType == '24')
			{
				$sBlowerStatus   =   $sRelays[$sRelayNumber];
				//IF Heater is ON then make that Heater OFF.
				if($sBlowerStatus)
				{
					$sNewResp = replace_return($sRelays, "0", $sRelayNumber );
					onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
					$this->home_model->updateDeviceRunTime($sRelayNumber,'R',"0",$ipID);
				}
				
			}
			if($sRelayType == '12')
			{
				$sBlowerStatus   =   $sPowercenter[$sRelayNumber];
				if($sBlowerStatus)
				{
					$sNewResp = replace_return($sPowercenter, "0", $sRelayNumber );
					onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
				}
			}
		}
		
		//Remove the light from database.
		$this->load->model('analog_model');
		$this->analog_model->removeDeviceDetails($heaterNumber,'H',$ipID);
		exit;
	}//END: Function for Heater Remove.
	
	//START: Function for Misc Remove.
	public function removeMisc()
	{
		$miscNumber	=	$this->input->post('miscNumber');
		$ipID			=	$this->input->post('ipID');
		
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
		//System real response is taken.
        $sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
		
		//Device Status is seperated.
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
		
		$sBlowerStatus	=	0;
		
		//First check if relay related to that Misc is ON/OFF.
		$aBlowerDetails  =   $this->home_model->getMiscDeviceDetails($miscNumber,$ipID);
		if(!empty($aBlowerDetails))
		{
			foreach($aBlowerDetails as $aBlower)
			$sRelayDetails  =   unserialize($aBlower->light_relay_number);
			
			$sRelayType     =   $sRelayDetails['sRelayType'];
			$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
			
			if($sRelayType == '24')
			{
				$sBlowerStatus   =   $sRelays[$sRelayNumber];
				//IF Misc is ON then make that Misc OFF.
				if($sBlowerStatus)
				{
					$sNewResp = replace_return($sRelays, "0", $sRelayNumber );
					onoff_rlb_relay($sNewResp,$sDeviceIP,$sPort,$shhPort);
					$this->home_model->updateDeviceRunTime($sRelayNumber,'R',"0",$ipID);
				}
				
			}
			if($sRelayType == '12')
			{
				$sBlowerStatus   =   $sPowercenter[$sRelayNumber];
				if($sBlowerStatus)
				{
					$sNewResp = replace_return($sPowercenter, "0", $sRelayNumber );
					onoff_rlb_powercenter($sNewResp,$sDeviceIP,$sPort,$shhPort);
				}
			}
		}
		
		//Remove the light from database.
		$this->load->model('analog_model');
		$this->analog_model->removeDeviceDetails($miscNumber,'M',$ipID);
		exit;
	}//END: Function for Heater Remove.
	
	//START: Function for making Heater ON/OFF.
	public function makeHeaterOnOFF($deviceNumber,$sStatus)
	{
		//System real response is taken.
        $sResponse      =   get_rlb_status();
		
		//Device Status is seperated.
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
		
		$sDevice	=	'H';
		$this->load->model('home_model');
		$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($deviceNumber);
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
					onoff_rlb_relay($sNewResp);
					$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$sStatus);
				}
				if($sRelayType == '12')
				{
					$sNewResp = replace_return($sPowercenter, $sStatus, $sRelayNumber );
					onoff_rlb_powercenter($sNewResp);
				}
			}
		}
	}//END: Function for making Heater ON/OFF.
	
	//START: Function for checking manual mode pool/spa temperature and time.
	public function checkTempAndTime()
	{
		$sMode			=	$this->input->post('mode');
		$modeType		=	$this->input->post('modeType');
		$modeManualTime	=	$this->input->post('modeManualTime');
		$temprature		=	$this->input->post('temprature');
		
		$this->load->model('analog_model');
		$this->load->model('home_model');
		
		if($sMode == 2) //2-manual
		{
			//System real response is taken.
			$sResponse      =   get_rlb_status();
			// Temperature Sensor Device 
			$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);
			
			$sValves        =   $sResponse['valves']; // Valve Device Status
			$sRelays        =   $sResponse['relay'];  // Relay Device Status
			$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
			$sTime          =   $sResponse['time']; // Server Time from Response
			
			//First check the time is completed 
			$currentTime 		=   date('H:i:s',time());
			
			$manualStartTime	=	'';
			$manualEndTime		=	'';
			
			$manualTimeDetails	=	$this->analog_model->getManualTimeDetails();
			foreach($manualTimeDetails as $ManualTime)
			{
				$manualStartTime	=	$ManualTime->timer_start;
				$manualEndTime		=	$ManualTime->timer_end;
			}
			
			//Get All Mode Details first.
			$arrModeDetails	=	$this->analog_model->getPoolSpaModeDetails();
			
			$arrGeneral		=	array();
			$arrDevice		=	array();
			$arrHeater		=	array();
			$arrMore		=	array();

			if(!empty($arrModeDetails))	
			{
				foreach($arrModeDetails as $arrDetails)
				{
					$arrGeneral		=	unserialize($arrDetails->general);
					$arrDevice		=	unserialize($arrDetails->device);
					$arrHeater		=	unserialize($arrDetails->heater);
					$arrMore		=	unserialize($arrDetails->more);
				}
			}
			
			$valveAssign	=	unserialize($arrDevice['valveAssign']);
			
			//Remove valve Acculated.
			$valve_actuated	=	$arrDevice['valve_actuated'];
			foreach($valve_actuated as $key => $valve)
			{
				if(in_array($valve,$valveAssign))
					unset($valveAssign[$key]);
			}
			
			$valveAssign = array_values($valveAssign);
			
			$pumpAssign		=	array();
			$heaterAssign	=	unserialize($arrHeater['heaterAssign']);
			
			for($i=1;$i<=$arrHeater['heater'];$i++)
			{
				$pumpAssign[] = $arrHeater['HeaterPump'.$i];
			}
			
			//If End time is Passed
			if($currentTime >= $manualEndTime)
			{
				//All Valve Devices are Made OFF.
				foreach($valveAssign as $iDeviceValve)
				{
					if($sValves[$iDeviceValve] != 0) //Check if device is already OFF.
					{
						$sStatus  		= 0;
						$deviceNumber	= $iDeviceValve;
						$sNewResp = replace_return($sValves, $sStatus, $deviceNumber );
						onoff_rlb_valve($sNewResp);
					}
				}
				
				//All Heater Devices are Made OFF.
				foreach($heaterAssign as $iDeviceHeater)
				{
					$this->makeHeaterOnOFF($iDeviceHeater,0);
				}
				
				//Enter the pump end time of 10 min later after heater is OFF.
				$this->analog_model->insertPumpEndTimeDetails($pumpAssign);
			}
			else if($sTemprature[0] >= $temprature) //Check if got the required temperature
			{
				//All Heater Devices are Made OFF.
				foreach($heaterAssign as $iDeviceHeater)
				{
					$this->makeHeaterOnOFF($iDeviceHeater,0);
				}
				//Enter the pump end time of 10 min later after heater is OFF.
				$this->analog_model->insertPumpEndTimeDetails($pumpAssign);
			}
			else if(($sTemprature[0] <= ($temprature-2)) && ($currentTime < $manualEndTime))	
			{
				//IF temperature of the heater is down by 2 degree and time is still remaining then again start the pump and heater.
				
				//Check If Pump is OFF related to the Heater.
				foreach($pumpAssign as $iDevicePump)
				{
					//First check if the Pump is already ON.
					
					$this->makePumpOnOFFAnalog($iDevicePump,1);
				}
				//All Heater Devices are Made OFF.
				foreach($heaterAssign as $iDeviceHeater)
				{
					$this->makeHeaterOnOFF($iDeviceHeater,0);
				}
				
			}				
				
			echo $sTemprature[0];
			exit;
		}
	}//START: Function for checking manual mode pool/spa temperature and time.
	
	//START: Function for making PUMP OFF after 10 min from Heater OFF.
	function makePumpOffAfter10()
	{
		$currentTime 		=   date('H:i:s',time());
		
		//Get All Mode Details first.
		$arrModeDetails	=	$this->analog_model->getPoolSpaModeDetails();
		
		$arrGeneral		=	array();
		$arrDevice		=	array();
		$arrHeater		=	array();
		$arrMore		=	array();

		if(!empty($arrModeDetails))	
		{
			foreach($arrModeDetails as $arrDetails)
			{
				$arrGeneral		=	unserialize($arrDetails->general);
				$arrDevice		=	unserialize($arrDetails->device);
				$arrHeater		=	unserialize($arrDetails->heater);
				$arrMore		=	unserialize($arrDetails->more);
			}
		}
		
		$pumpAssign		=	array();
		$heaterAssign	=	unserialize($arrHeater['heaterAssign']);
		
		for($i=1;$i<=$arrHeater['heater'];$i++)
		{
			$pumpAssign[] = $arrHeater['HeaterPump'.$i];
		}
			
		//System real response is taken.
		$sResponse      =   get_rlb_status();
		
		//Make all Pump OFF after 10 Min
		foreach($pumpAssign as $iPump)
		{
			$EndTime = $this->analog_model->getPumpEndTimeDetails($iPump);
			if(!empty($EndTime))
			{
				foreach($EndTime as $time)
				{
					if($currentTime >= $time)
					{
						$this->makePumpOnOFFAnalog($iPump,0);
						$this->analog_model->deletePumpEntry($iPump);
					}
				}
			}
		}
	}//END: Function for making PUMP OFF after 10 min from Heater OFF.
	
	//START : Function to get the status of all devices.
	public function getDeviceStatusUpdate()
	{
		$this->load->model('home_model');
		$this->load->model('analog_model');
		
		//System real response is taken.
		$sResponse      =   get_rlb_status();
		// Temperature Sensor Device 
		$sTemprature    =   array($sResponse['TS0'],$sResponse['TS1'],$sResponse['TS2'],$sResponse['TS3'],$sResponse['TS4'],$sResponse['TS5']);
		
		$sValves        =   $sResponse['valves']; // Valve Device Status
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
		$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		$sTime          =   $sResponse['time']; // Server Time from Response
		
		$sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
		
		$arrDeviceDetails	=	array();
		
		//Get All Mode Details first.
		$arrModeDetails	=	$this->analog_model->getPoolSpaModeDetails();
		
		$arrGeneral		=	array();
		$arrDevice		=	array();
		$arrHeater		=	array();
		$arrMore		=	array();

		if(!empty($arrModeDetails))	
		{
			foreach($arrModeDetails as $arrDetails)
			{
				$arrGeneral		=	unserialize($arrDetails->general);
				$arrDevice		=	unserialize($arrDetails->device);
				$arrHeater		=	unserialize($arrDetails->heater);
				$arrMore		=	unserialize($arrDetails->more);
			}
		}
		
		//START : Get all assigned devices in the Mode.
		
			$valveAssign	=	unserialize($arrDevice['valveAssign']);
			
			//Remove valve Acculated.
			$valve_actuated	=	$arrDevice['valve_actuated'];
			foreach($valve_actuated as $key => $valve)
			{
				if(in_array($valve,$valveAssign))
					unset($valveAssign[$key]);
			}
			
			$valveAssign = array_values($valveAssign);
			
			$pumpAssign		=	array();
			$heaterAssign	=	unserialize($arrHeater['heaterAssign']);
			
			for($i=1;$i<=$arrHeater['heater'];$i++)
			{
				$pumpAssign[] = $arrHeater['HeaterPump'.$i];
			}
			
			$lightAssign	=	unserialize($arrMore['lightAssign']);
			$blowerAssign	=	unserialize($arrMore['blowerAssign']);
			$miscAssign		=	unserialize($arrMore['miscAssign']);
		
		//END : Get all assigned devices in the Mode.
		
		//Get Status of all Valves in the Mode.
		foreach($valveAssign as $iValve)
		{
			$arrDeviceDetails['valve'][$iValve] = $sValves[$iValve];
		}
		
		//Get Status of all Pumps in the Mode.
		foreach($pumpAssign as $iPump)
		{
			$arrDeviceDetails['pump'][$iPump] = $sPump[$iPump];
		}
		
		//Get Status of all Heaters in the Mode.
		foreach($heaterAssign as $iHeater)
		{
			$sHeaterStatus	 =	'';
			$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($iHeater);
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
			}
			
			$arrDeviceDetails['heater'][$iHeater] = $sHeaterStatus;
		}
		
		//Get Status of all Lights in the Mode.
		foreach($lightAssign as $iLight)
		{
			$sLightStatus	=	'';
			$aLightDetails  =   $this->home_model->getLightDeviceDetails($iLight);
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
			}
			$arrDeviceDetails['light'][$iLight] = $sLightStatus;
		}
		
		//Get Status of all Blowers in the Mode.
		foreach($blowerAssign as $iBlower)
		{
			$sBlowerStatus   = '';
			$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($iBlower);
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
			}
			
			$arrDeviceDetails['blower'][$iBlower] = $sBlowerStatus;
		}
		
		//Get Status of all Misc in the Mode.
		foreach($miscAssign as $iMisc)
		{
			$sMiscStatus	=	'';
			$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($iMisc);
			if(!empty($aMiscDetails))
			{
				foreach($aMiscDetails as $aMisc)
				{
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
			}
			$arrDeviceDetails['misc'][$iMisc] = $sMiscStatus;
		}
		
		echo json_encode($arrDeviceDetails);
		exit;
		
	}//END : Function to get the status of all devices.
	
	//START : Function to save the details of the TAB from advance setting using Ajax.
	public function saveAdvanceSettingTabDetails()
	{
		$this->load->model('home_model');
		
		//0 = Home,1=Temperarure,2=Valve,3 =Pump,4=Heater,5=Light,6=blower,7=Misc
		$tabID 		= $this->input->post('tabID');
		$arrDetails	= json_decode($this->input->post('details'));
		
		if($tabID == 0)
		{
			$arrTemp	=	array('type'			=> $arrDetails->type,
								  'pool_max_temp'	=> $arrDetails->pool_max_temp,
								  'pool_temp'		=> $arrDetails->pool_temp,
								  'pool_manual'		=> $arrDetails->pool_manual,
								  'spa_max_temp'	=> $arrDetails->spa_max_temp,
								  'spa_temperature'	=> $arrDetails->spa_temp,
								  'spa_manual'		=> $arrDetails->spa_manual
								  );
			
			$manualTime	=	0;
			if($arrDetails->type == 'pool')
				$manualTime	=	$arrDetails->pool_manual;
			else if($arrDetails->type == 'spa')
				$manualTime	=	$arrDetails->spa_manual;
			else
			{
				if($rrDetails->pool_manual >= $arrDetails->spa_manual)
					$manualTime	=	$arrDetails->pool_manual;
				else
					$manualTime	=	$arrDetails->spa_manual;
			}
			
			$this->home_model->updateManualModeTime($manualTime);
		}
		else if($tabID == 1)
		{
			$arrTemp	=	array('temperature1'	=> $arrDetails->temperature1,
								  'temperature2'	=> $arrDetails->temperature2,
								  'display_pool_temp'=> $arrDetails->display_pool_temp,
								  'display_spa_temp'=> $arrDetails->display_spa_temp
								  );
		}
		else if($tabID == 2)
		{
			$arrTemp	=	array('valve'			=> $arrDetails->valve,
								  'valve_actuated'	=> $arrDetails->valve_actuated,
								  'reasonValve'		=> $arrDetails->reasonValve,
								  'valveRunTime'	=> $arrDetails->valveRunTime,
								  'valveAssign'		=> serialize($arrDetails->valveAssign)
								  );
		}
		else if($tabID == 3)
		{
			$arrTemp	=	array('pump'			=> $arrDetails->pump,
								  'pumpAssign'		=> serialize($arrDetails->pumpAssign)
								  );
			for($i=1;$i<=$arrDetails->pump;$i++)
			{
				$arrTemp['pump'.$i] 			= 	$arrDetails->PumpService[$i];
			}					  
								
		}
		else if($tabID == 4)
		{
			$arrTemp	=	array('heater'			=> $arrDetails->heater,
								  'heaterAssign'	=> serialize($arrDetails->heaterAssign)
								  );
			for($i=1;$i<=$arrDetails->heater;$i++)
			{
				$arrTemp['Heater'.$i] 			= 	$arrDetails->heaterService[$i];
				$arrTemp['HeaterPump'.$i] 			= 	$arrDetails->heaterPump[$i];
			}					  
								
		}
		else if($tabID == 5)
		{
			$arrTemp	=	array('light'			=> $arrDetails->light,
								  'lightAssign'		=> serialize($arrDetails->lightAssign)
								  );
		}
		else if($tabID == 6)
		{
			$arrTemp	=	array('blower'			=> $arrDetails->blower,
								  'blowerAssign'	=> serialize($arrDetails->blowerAssign)
								  );
		}
		else if($tabID == 7)
		{
			$arrTemp	=	array('misc'			=> $arrDetails->misc,
								  'miscAssign'		=> serialize($arrDetails->miscAssign)
								  );
		}
		$this->home_model->saveTabDetails($arrTemp,$tabID);
		exit;
		
	}//END : Function to save the details of the TAB from advance setting using Ajax.
	
	//START : Function to save the quick settings Details.
	public function saveQuickSetting()
	{
		$this->load->model('home_model');
		
		//0 = Home,1=Temperarure,2=Valve,3 =Pump,4=Heater,5=Light,6=blower,7=Misc
		$tabID 		= 0;
		$arrDetails	= json_decode($this->input->post('general'));
		
		if($tabID == 0)
		{
			$arrTemp	=	array('type'			=> $arrDetails->type,
								  'pool_max_temp'	=> $arrDetails->pool_max,
								  'pool_temp'		=> $arrDetails->pool_des,
								  'pool_manual'		=> $arrDetails->pool_man,
								  'spa_max_temp'	=> $arrDetails->spa_max,
								  'spa_temperature'	=> $arrDetails->spa_des,
								  'spa_manual'		=> $arrDetails->spa_man,
								  'temperature1'	=> $arrDetails->temperature1,
								  'temperature2'	=> $arrDetails->temperature2
								  );
			
			$manualTime	=	0;
			if($arrDetails->type == 'pool')
				$manualTime	=	$arrDetails->pool_man;
			else if($arrDetails->type == 'spa')
				$manualTime	=	$arrDetails->spa_man;
			else
			{
				if($rrDetails->pool_man >= $arrDetails->spa_man)
					$manualTime	=	$arrDetails->pool_man;
				else
					$manualTime	=	$arrDetails->spa_man;
			}
			
			$this->home_model->updateManualModeTime($manualTime);
		}
		$this->home_model->saveTabDetails($arrTemp,$tabID);
		exit;
	}//END : Function to save the quick settings Details.
	
	//START : Get Remote Device STATUS every 10 Sec using Ajax.
	public function getRemoteDeviceStatus()
	{
		$this->load->model('analog_model');
		$this->load->model('home_model');
		
		$ipID	=	$this->input->post('ipID');
		//Get the details related to the analog devices.
        $aAllAnalogDevice          = $this->analog_model->getAllAnalogDevice($ipID);
        $aAllANalogDeviceDirection = $this->analog_model->getAllAnalogDeviceDirection($ipID);
		$aAllAnalogDevicePort	   = $this->analog_model->getAllAnalogDevicePorts($ipID);
		
		$arrResult	=	array();
		
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
		
		//print_r($aAllAnalogDevice);
		if(!empty($aAllAnalogDevice))
		{
			//System real response is taken.
			$sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
			
			//Device Status is seperated.
			$sValves        =   $sResponse['valves'];
			$sRelays        =   $sResponse['relay'];
			$sPowercenter   =   $sResponse['powercenter'];
			
			$sPump          =   array($sResponse['pump_seq_0_st'],
									  $sResponse['pump_seq_1_st'],
									  $sResponse['pump_seq_2_st']);
								  
			foreach($aAllAnalogDevice as $iDevice => $relatedDevice)
			{
				$arrDetails 	= explode("_",$relatedDevice);
				$deviceNumber	= $arrDetails[0];
				$deviceType		= $arrDetails[1];
				
				if($deviceType == 'R')
				{
					$arrResult[$iDevice]	=	$sRelays[$deviceNumber];
				}
				else if($deviceType == 'P')
				{
					$arrResult[$iDevice]	=	$sPowercenter[$deviceNumber];
				}
				else if($deviceType == 'V')
				{
					$arrResult[$iDevice]	=	$sValves[$deviceNumber];
				}
				else if($deviceType == 'PS')
				{
					$arrResult[$iDevice]	=	$sPump[$deviceNumber];
				}
				else if($deviceType == 'L')
				{
					$sLightStatus	=	'';
					$aLightDetails  =   $this->home_model->getLightDeviceDetails($deviceNumber,$ipID);
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
						
						$arrResult[$iDevice] = $sLightStatus;
					}
				}
				else if($deviceType == 'H')
				{
					$sHeaterStatus	 = 	 '';	
					$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($deviceNumber,$ipID);
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
						
						$arrResult[$iDevice] = $sHeaterStatus;
					}
				}
				else if($deviceType == 'B')
				{
					$sBlowerStatus	 = 	 '';
					$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($deviceNumber,$ipID);
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
						
						$arrResult[$iDevice] = $sBlowerStatus;
					}
				}
				else if($deviceType == 'M')
				{
					$aMiscDetails  =   $this->home_model->getMiscDeviceDetails($deviceNumber,$ipID);
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
						
						$arrResult[$iDevice] = $sMiscStatus;
					}
				}
				
			}
		}
		
		echo json_encode($arrResult);
		exit;
		
	}//END : Get Remote Device STATUS every 10 Sec using Ajax.
	
	//START : Save Port number of the relayboard for the Remote Devices.
	public function saveDevicePortAnalog()
	{
		//Post Data
		$iDeviceNum		=	$this->input->post('iDeviceNum');
		$sPort			=	$this->input->post('sPort');
		
		if($sPort != '')
		{
			$this->load->model('analog_model');
			
			$this->analog_model->saveDevicePortDetailsAnalog($iDeviceNum,$sPort);
		}
		
		exit;
	}
	//END : Save Port number of the relayboard for the Remote Devices.
}

?>

<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH.'controllers/home.php'); 

class Analog extends CI_Controller 
{
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
        
    }
    
	//START : Function to save the analog input details. 
    public function index()
    {
        $aViewParameter =   array();
        
        $aViewParameter['page'] 	=	'home';
        $this->load->model('analog_model');
        $aViewParameter['sucess'] 	=   '0';
		$aViewParameter['Title']    =   'Inputs';

        
        $aObjHome = new Home();   
        $aObjHome->checkSettingsSaved(); 
        
		//System real response is taken.
        $sResponse      =   get_rlb_status();
		
		//Device Status is seperated.
        $sValves        =   $sResponse['valves'];
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
        
        $sPump          =   array($sResponse['pump_seq_0_st'],
                                  $sResponse['pump_seq_1_st'],
                                  $sResponse['pump_seq_2_st']);

        $aViewParameter['sValves']          =   $sValves;
        $aViewParameter['sRelays']          =   $sRelays;
        $aViewParameter['sPowercenter']     =   $sPowercenter; 
        $aViewParameter['sPump']            =   $sPump;

        $aViewParameter['relay_count']      =   strlen($sRelays);
        $aViewParameter['valve_count']      =   strlen($sValves);
        $aViewParameter['power_count']      =   strlen($sPowercenter);
        $aViewParameter['pump_count']       =   count($sPump);
        
		//Check and Save the details related to the analog Input.
        if($this->input->post('command') == 'Save')
        {
            $sDeviceName = $this->input->post('sDeviceName');
            $this->analog_model->saveAnalogDevice($sDeviceName);
            $aViewParameter['sucess'] =   '1';
        }
		
		//Get the details related to the analog devices.
        $aAllAnalogDevice          = $this->analog_model->getAllAnalogDevice();
        $aAllANalogDeviceDirection = $this->analog_model->getAllAnalogDeviceDirection();
        
        $aViewParameter['aResponse']    =   array('AP0' => $sResponse['AP0'],
                                                  'AP1' => $sResponse['AP1'],
                                                  'AP2' => $sResponse['AP2'],
                                                  'AP3' => $sResponse['AP3']);

        $aViewParameter['aAllAnalogDevice']             =   $aAllAnalogDevice;
        $aViewParameter['aAllANalogDeviceDirection']    =   $aAllANalogDeviceDirection;  

		//Load View for showing the analog devices.
        $this->template->build('Analog',$aViewParameter);
    }

    public function changeMode()
    {
		//Default Parameter for the View
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
        $aViewParameter['page']         =   'home';
		
		//
        $this->load->model('home_model');
		$iActiveMode	= $this->home_model->getActiveMode();
		$sManualTime	= $this->home_model->getManualModeTime();
		
        if($this->input->post('iMode') != '')
        {
            $iMode = $this->input->post('iMode');
			if($iActiveMode != $iMode)
			{
				$this->home_model->updateMode($iMode);
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

            }
             $aViewParameter['sucess']    =   '1';

        }

        $aViewParameter['iMode']  =   $this->home_model->getActiveMode();

        $this->template->build("Mode",$aViewParameter);

    }
	
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

    }
	
	public function showLight()
	{
		$aViewParameter['Title']    =   'Lights';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sDevice'] 	=	'L';
		
		//Get the status response of devices from relay board.
            $sResponse      =   get_rlb_status();
		
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
                $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
		$this->load->model('home_model');
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
		
		$aViewParameter['numLight']         =	'';
		
		if(isset($extra['LightNumber']))
            $aViewParameter['numLight']     =	$extra['LightNumber'];
		
		//START : Parameter for View
		$aViewParameter['relay_count']      =   strlen($sRelays);
		$aViewParameter['power_count']      =   strlen($sPowercenter);
		
		$aViewParameter['sRelays']          =   $sRelays; 
		$aViewParameter['sPowercenter']     =   $sPowercenter;
		
		//Current mode of the system.
		$aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
                
        $this->template->build("Light",$aViewParameter);
	}
	
	public function tempConfig()
	{
		$arrDetails					=	array();
		$aViewParameter['Title']    =   'Temprature Configure';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sucess'] 	=	'';
		
		$this->load->model(analog_model);
		
		$iTempID		=	base64_decode($this->uri->segment('3')); 
		
		$iTempAction	=	'';
		if($this->uri->segment('4') != '' && $this->uri->segment('4') == 'remove')
		{
			$iTempAction	=	$this->uri->segment('4');
			echo $iTempAction;
			die;
		}
		
		
		
		//Remove Bus Number
		if($iTempAction != '')
		{
			$busNumber	=	'0000000000000000';
			$this->analog_model->saveBusNumber($iTempID,$busNumber);
			
			//Configure bus to Temperature Sensors.
				$sResponse      =   configureTempratureBus('ts'.$iTempID,$busNumber);
				
			redirect(base_url('home/setting/T/'));
			exit;			
		}
		
		if($iTempID == '')
		{
			$iTempID 	= 	$this->input->post('iTempID');
		}
		
		if($iTempID == '')
		{
			redirect(base_url('home/setting/T/'));
			exit;
		}
		
		
		if($this->input->post('command') == 'Save')
		{
			$busNumber	=	$this->input->post('busConfigure');
			
			$this->analog_model->saveBusNumber($iTempID,$busNumber);
			
			//Configure bus to Temperature Sensors.
				$sResponse      =   configureTempratureBus('ts'.$iTempID,$busNumber);
				
			$aViewParameter['sucess'] 	=	'1';
				
		}
		
		$sResponse      =   getTempratureBus();
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
		
		$this->template->build("Temprature",$aViewParameter);
	}
	
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
			
			$arrTemp	=	array();
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
			
			//Check if device is running.
			$currentDevice = $this->analog_model->getPoolCurrentDevice($isPoolSpaModeOn,$unique_id);
			
			var_dump($currentDevice);
			
			if($currentDevice == '')
			{
				//If Device is Not running then Get Last complete Device
				//$lastDevice	=   $this->analog_model->getLastCompleteDevice($isPoolSpaModeOn,$unique_id);
				//if($lastDevice == '')
				{
					//START first device in the sequence.
					$device = array_search('1', $arrTemp);
					
					$sStatus		=	'1';
					$sRunTime		=	'';
					$deviceType 	=   '';
					$deviceNumber	=	'';
					
					//Make First Device ON
					if($device == 'valve')
					{
						$deviceType 	=   'V';
						$deviceNumber	=	'0';
						
						$sNewResp = replace_return($sValves, $sStatus, $deviceNumber );
						onoff_rlb_valve($sNewResp);
						
						$sRunTime	=	$arrDevice['valveRunTime'];
						
					}
					if($device == 'pump')
					{
						$deviceType 	=   'PS';
						$deviceNumber	=	'0';
						
						$this->makePumpOnOFFAnalog($deviceNumber,'1');
						
						$sRunTime	=	$arrDevice['pumpRunTime'];
						
					}
					if($device == 'light')
					{
						
						$deviceType 	=   'L';
						$deviceNumber	=	'0';
						
						$sNewResp = replace_return($sRelays, '1', $deviceNumber );
						onoff_rlb_relay($sNewResp);
						
						$sRunTime	=	$arrDevice['lightRunTime'];
						
					}
					
					//Make Entry in the Current Running Device Table.
					$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$device.'1',$sRunTime,$deviceType,$deviceNumber,1);	
				}
			}
			else
			{
				foreach($currentDevice as $deviceDetails)
				{
					//If Device is Running Then Check if its Time is Complete.
					$OffTime	=	$deviceDetails->current_off_time;
					
					$currentServerTime	=	date('Y-m-d H:i:s');
					
					if($currentServerTime >= $OffTime)
					{
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
								$strNextDevice	=	'valve'.(str_replace('valve','',$deviceDetails->current_on_device) + 1);
							
								$deviceType 	=   'V';
								$deviceNumber	=	'0';
								
								$sNewResp = replace_return($sValves, '1', $deviceNumber );
								onoff_rlb_valve($sNewResp);
								
								$sRunTime	=	$arrDevice['valveRunTime'];
								
								//Make Entry in the Current Running Device Table.
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strNextDevice,$sRunTime,$deviceType,$deviceNumber,$deviceDetails->current_sequence);
							}
						}
											
						if($deviceDetails->device_type == 'PS')
						{
							$this->makePumpOnOFFAnalog($deviceDetails->device_number,'0');
							
							//Insert Entry in the Log Table for future Reference.
							$this->analog_model->saveEntryInLog($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							//Delete Entry From the current details Table.
							$this->analog_model->deleteEntryFromCurrent($isPoolSpaModeOn,$unique_id);
							
							//First check How many valves are already OFF from total Valve.
							$totalCompleteDevice	=   $this->analog_model->getCompleteDeviceOfType($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							if($totalCompleteDevice == '')
								$totalCompleteDevice = 0;
							
							$remainingDevice	=	($arrDevice['pump'] - $totalCompleteDevice);
							
							$remainingDevice    = 0;
							if($remainingDevice > 0)
							{
								$strNextDevice	=	'pump'.(str_replace('pump','',$deviceDetails->current_on_device) + 1);
							
								$deviceType 	=   'PS';
								$deviceNumber	=	'1';
								
								$this->makePumpOnOFFAnalog($deviceNumber,'1');
								
								$sRunTime	=	$arrDevice['pumpRunTime'];
								
								//Make Entry in the Current Running Device Table.
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strNextDevice,$sRunTime,$deviceType,$deviceNumber,$deviceDetails->current_sequence);
							}
						}
						
						if($deviceDetails->device_type == 'L')
						{
							$sNewResp = replace_return($sRelays, '0', $deviceDetails->device_number);
							onoff_rlb_relay($sNewResp);
							
							//Insert Entry in the Log Table for future Reference.
							$this->analog_model->saveEntryInLog($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							//Delete Entry From the current details Table.
							$this->analog_model->deleteEntryFromCurrent($isPoolSpaModeOn,$unique_id);
							
							//First check How many valves are already OFF from total Valve.
							$totalCompleteDevice	=   $this->analog_model->getCompleteDeviceOfType($isPoolSpaModeOn,$unique_id,$deviceDetails->device_type);
							
							if($totalCompleteDevice == '')
								$totalCompleteDevice = 0;
							
							$remainingDevice	=	($arrMore['light'] - $totalCompleteDevice);
							
							if($remainingDevice > 0)
							{
								$strNextDevice	=	'light'.(str_replace('light','',$deviceDetails->current_on_device) + 1);
							
								$deviceType 	=   'L';
								$deviceNumber	=	'3';
								
								$sNewResp = replace_return($sRelays, '1', $deviceNumber );
								onoff_rlb_relay($sNewResp);
								
								$sRunTime	=	$arrDevice['lightRunTime'];
								
								//Make Entry in the Current Running Device Table.
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$strNextDevice,$sRunTime,$deviceType,$deviceNumber,$deviceDetails->current_sequence);
							}
						}
						
						//Remaining Devices of particular is 0 then start next type of device from sequence.
						if($remainingDevice == 0)
						{
							//Get Next device from the sequence.
							$previousSequence	=	$deviceDetails->current_sequence;
							
							$current = array_search($previousSequence,$arrTemp);
							/* if($previousSequence == end($arrTemp))
							{
								$this->analog_model->stopCurrentPoolSpaMode($isPoolSpaModeOn);
								exit;
							}	 */
							$keys 	 = array_keys($arrTemp);
							$ordinal = (array_search($current,$keys)+1)%count($keys);
							$next 	 = $keys[$ordinal];
														
							//START first device in the sequence.
							$device = array_search($arrTemp[$next],$arrTemp);
							
							if($device != '')
							{
								$sStatus		=	'1';
								$sRunTime		=	'';
								$deviceType 	=   '';
								$deviceNumber	=	'';
								
								//Make First Device ON
								if($device == 'valve')
								{
									$deviceType 	=   'V';
									$deviceNumber	=	'0';
									
									$sNewResp = replace_return($sValves, $sStatus, $deviceNumber );
									onoff_rlb_valve($sNewResp);
									
									$sRunTime	=	$arrDevice['valveRunTime'];
									
								}
								if($device == 'pump')
								{
									
									$deviceType 	=   'PS';
									$deviceNumber	=	'0';
									
									$this->makePumpOnOFFAnalog($deviceNumber,'1');
									
									$sRunTime	=	$arrDevice['pumpRunTime'];
									
								}
								if($device == 'light')
								{
									
									$deviceType 	=   'L';
									$deviceNumber	=	'2';
									
									$sNewResp = replace_return($sRelays, '1', $deviceNumber );
									onoff_rlb_relay($sNewResp);
									
									$sRunTime	=	$arrDevice['lightRunTime'];
									
								}
								
								//Make Entry in the Current Running Device Table.
								$this->analog_model->saveCurrentRunningDevice($isPoolSpaModeOn,$unique_id,$device.'1',$sRunTime,$deviceType,$deviceNumber,$nextSequence);
							}
							else
							{
								$this->analog_model->stopCurrentPoolSpaMode($isPoolSpaModeOn);
							}
						}
					}
				}						
			}				
			
		}
	}
	
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
		
	}
	
	public function showHeater()
	{
		$aViewParameter['Title']    =   'Heater';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sDevice'] 	=	'H';
		
		//Get the status response of devices from relay board.
            $sResponse      =   get_rlb_status();
		
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
		$this->load->model('home_model');
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
		
		$aViewParameter['numHeater']         =	'';
		
		if(isset($extra['HeaterNumber']))
            $aViewParameter['numHeater']     =	$extra['HeaterNumber'];
		
		//START : Parameter for View
		$aViewParameter['relay_count']      =   strlen($sRelays);
		$aViewParameter['power_count']      =   strlen($sPowercenter);
		
		$aViewParameter['sRelays']          =   $sRelays; 
		$aViewParameter['sPowercenter']     =   $sPowercenter;
		
		//Current mode of the system.
		$aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
                
        $this->template->build("Heater",$aViewParameter);
	}
	
	public function showBlower()
	{
		$aViewParameter['Title']    =   'Blower';
		$aViewParameter['page'] 	=	'home';
		$aViewParameter['sDevice'] 	=	'B';
		
		//Get the status response of devices from relay board.
            $sResponse      =   get_rlb_status();
		
		$sRelays        =   $sResponse['relay'];  // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
		
		$this->load->model('home_model');
		
		//Get Extra Details
		list($aViewParameter['sIP'],$aViewParameter['sPort'],$extra) = $this->home_model->getSettings();
		
		$aViewParameter['numBlower']         =	'';
		
		if(isset($extra['BlowerNumber']))
            $aViewParameter['numHeater']     =	$extra['BlowerNumber'];
		
		//START : Parameter for View
		$aViewParameter['relay_count']      =   strlen($sRelays);
		$aViewParameter['power_count']      =   strlen($sPowercenter);
		
		$aViewParameter['sRelays']          =   $sRelays; 
		$aViewParameter['sPowercenter']     =   $sPowercenter;
		
		//Current mode of the system.
		$aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();
                
        $this->template->build("Blower",$aViewParameter);
	}
}

?>

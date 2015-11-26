<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('common_functions');
	}
    
    public function index()
    {
		//$seconds = 2; 
        //$micro = $seconds * 1000000;
        $this->load->model('analog_model');
        $this->load->model('home_model');
        //while(true)
        {    
            list($sIpAddress, $sPortNo) = $this->home_model->getSettings();
            
            if($sIpAddress == '')
            {
                if(IP_ADDRESS)
                {
                    $sIpAddress = IP_ADDRESS;
                }
            }
            
            //Check for Port Number constant
            if($sPortNo == '')
            {   
                if(PORT_NO)
                {
                    $sPortNo = PORT_NO;
                }
            }

            if($sIpAddress == '' || $sPortNo == '')
            {

            }   
            else
            { 
				$sResponse =   response_input_switch($sIpAddress,$sPortNo);
				
                /* $sResponse =   get_rlb_status();
                $aAP       =   array($sResponse['AP0'],$sResponse['AP1'],$sResponse['AP2'],$sResponse['AP3']);
                //$aAP       =   array(0,1,0,1);

                $sValves        	=   $sResponse['valves'];
                $sRelays        	=   $sResponse['relay'];
                $sPowercenter   	=   $sResponse['powercenter'];

                $aResult            =   $this->analog_model->getAllAnalogDevice();
                $aResultDirection   =   $this->analog_model->getAllAnalogDeviceDirection();
                $iResultCnt =   count($aResult);
                
                for($i=0; $i<$iResultCnt; $i++)
                {
                    if($aResult[$i] != '')
                    {
                        $aDevice = explode('_',$aResult[$i]);
                        if($aDevice[1] != '')
                        {
                            if($aDevice[1] == 'R')
                            {
                                if($sRelays[$aDevice[0]] != '' && $sRelays[$aDevice[0]] != '.')
                                {
                                    $sNewResp = replace_return($sRelays, $aAP[$i], $aDevice[0] );
                                    onoff_rlb_relay($sNewResp);
                                }
                                //exex('rlb m 0 2 1');
                            }
                            if($aDevice[1] == 'P')
                            {
                                $sNewResp = replace_return($sPowercenter, $aAP[$i], $aDevice[0] );
                                onoff_rlb_powercenter($sNewResp);
                            }
                            if($aDevice[1] == 'V')
                            {
                                if($sValves[$aDevice[0]] != '' && $sValves[$aDevice[0]] != '.')
                                {
                                    $sStatusChnage = $aResultDirection[$i];

                                    if($aAP[$i] == '0')
                                    $sNewResp = replace_return($sValves, $aAP[$i], $aDevice[0] );
                                    else if($aAP[$i] == '1')
                                    $sNewResp = replace_return($sValves, $sStatusChnage, $aDevice[0] ); 

                                    onoff_rlb_valve($sNewResp);
                                }
                            }
                        }
                    }
                } */
            } 

            /*$myFile = "/var/www/relay_framework/daemontest1.txt";
            $fh = fopen($myFile, 'a') or die("Can't open file");
            $stringData = "File updated at: " . $sResponse. "\n";
            fwrite($fh, $stringData);
            fclose($fh); 
            usleep($micro); */    
        }      
    }
	
    public function pumpResponse()
    { 		
        //$seconds = 15;
        //$micro = $seconds * 1000000;
        $this->load->model('home_model');
        list($sIpAddress, $sPortNo) = $this->home_model->getSettings();

        $aPumps		= $this->home_model->selectEmulatorOnPumps();	
        //print_r($aPumps);
        $aPumpsChk	= json_decode($aPumps);
        //print_r($aPumps);
        //while(true)

        if(!empty($aPumpsChk))	
        { 
            //while(true)
            {
                $sResponse =   send_command_udp_new($sIpAddress,$sPortNo,$aPumps);

                if($sResponse != '')
                {
					//$aResponse['message'] =$sResponse;
					$aResponse	=	explode("|||",$sResponse);
					foreach($aResponse as $strResponse)
					{
							$aCheckResponse	=	explode(',',$strResponse);
							//$iPump			=   str_replace('M','',$aCheckResponse[0]);
							//if($aCheckResponse[1] == '0')
							//	$strResponse .= ',STOP';

							$iPump	=	$aCheckResponse[1];

							if($aCheckResponse[2] == '0')
									$strResponse .= ',STOP';

							if(preg_match('/^M/',$strResponse))
								$this->home_model->savePumpResponse($strResponse,$iPump);
					}

                    //echo json_encode($aResponse);
                }

                /* $myFile = "/var/www/relay_framework/daemontest1.txt";
                $fh = fopen($myFile, 'a') or die("Can't open file");
                $stringData = "File updated at: " . time(). "\n";
                fwrite($fh, $stringData);
                fclose($fh); */
                //usleep($micro); 
            }
        }


        //$aresponse['message'] = $sResponse;
        //echo json_encode($aresponse);
    }
	
    public function pumpResponseLatest()
    {
            $iPumpID	=	$_GET['iPumpID'];
            $this->load->model('home_model');
            $strPumpsResponse		= $this->home_model->selectPumpsLatestResponse($iPumpID);	

            $sResponse      		=   get_rlb_status();
            //Pump device Status
            $sPump  =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
            $arrPumpsResponse   =   $this->home_model->selectPumpsStatus($iPumpID);

            if($sPump[$iPumpID] > 0 && $arrPumpsResponse == 0)
            {
                    $arrPumpsResponse		= 1;		
                    $this->home_model->updateDeviceStauts($iPumpID,'PS',$arrPumpsResponse);
            }
            else if($sPump[$iPumpID] == 0  && $arrPumpsResponse == 1)	
            {
                    $arrPumpsResponse		= 0;		
                    $this->home_model->updateDeviceStauts($iPumpID,'PS',$arrPumpsResponse);
            }

            $aResult    =   array();
            if($arrPumpsResponse == 0)
                    $aResult['message'] = '';
            else
                    $aResult['message'] = $strPumpsResponse;

            echo json_encode($aResult['message']);
    }
	
	
    public function program()
    {
	$this->load->model('home_model');
        $sResponse      =   get_rlb_status();
		
        //$sResponse      =   array('valves'=>'','powercenter'=>'0000','time'=>'','relay'=>'0000','day'=>'');
        //$sValves        =   $sResponse['valves'];
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
        $sTime          =   $sResponse['time'];
        $sDayret        =   $sResponse['day'];
        //$aTime          =   explode(':',$sTime);

        //$iRelayCount    =   strlen($sRelays);
        //$iValveCount    =   strlen($sValves);
        //$iPowerCount    =   strlen($sPowercenter);

        $iMode          =   $this->home_model->getActiveMode();
        //$iMode          =   1;
        $sTime          =   date('H:i:s',time());
		
		//$sTime			=	'10:00:01';
        $aAllProgram    =   $this->home_model->getAllProgramsDetails();
        
        if(is_array($aAllProgram) && !empty($aAllProgram))
        {
            foreach($aAllProgram as $aResultProgram)
            {
				$sRelayName     = $aResultProgram->device_number;
				$sDevice     	= $aResultProgram->device_type;
                $iProgId        = $aResultProgram->program_id;
                $sProgramType   = $aResultProgram->program_type;
                $sProgramStart  = $aResultProgram->start_time;
                $sProgramEnd    = $aResultProgram->end_time;
                $sProgramActive = $aResultProgram->program_active;
                $sProgramDays   = $aResultProgram->program_days;
                
                $sProgramAbs            = $aResultProgram->program_absolute;
                $sProgramAbsStart       = $aResultProgram->program_absolute_start_time;
                $sProgramAbsEnd         = $aResultProgram->program_absolute_end_time;
                $sProgramAbsTotal       = $aResultProgram->program_absolute_total_time;
                $sProgramAbsAlreadyRun  = $aResultProgram->program_absolute_run_time;

                $sProgramAbsStartDay    = $aResultProgram->program_absolute_start_date;
                $sProgramAbsRun         = $aResultProgram->program_absolute_run;

                $sDays          =   '';
                $aDays          =   array();

                if($sProgramType == 2)
                {
					$sDays = str_replace('7','0', $sProgramDays);
                    $aDays = explode(',',$sDays);
                }
				if($sRelays[$sRelayName] != '' && $sRelays[$sRelayName] != '.' && $sDevice == 'R')
                {
                    if($sProgramType == 1 || ($sProgramType == 2 && in_array($sDayret, $aDays)))
                    {
                        $aAbsoluteDetails       = array('absolute_s'  => $sProgramAbsStart,
                                                        'absolute_e'  => $sProgramAbsEnd,
                                                        'absolute_t'  => $sProgramAbsTotal,
                                                        'absolute_ar' => $sProgramAbsAlreadyRun,
                                                        'absolute_sd' => $sProgramAbsStartDay,
                                                        'absolute_st' => $sProgramAbsRun
                                                        ); 

                        if($sProgramAbs == '1' && $iMode == 1)
                        {
                            if($sProgramActive == 0)
                                $this->home_model->updateProgramAbsDetails($iProgId, $aAbsoluteDetails);

                            if($sTime >= $sProgramStart && $sProgramActive == 0 && $sProgramAbsRun == 0)
                            {
                                $iRelayStatus = 1;
                                $sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
                                onoff_rlb_relay($sRelayNewResp);
                                $this->home_model->updateProgramStatus($iProgId, 1);
                            }
                            else if($sTime >= $sProgramAbsEnd && $sProgramActive == 1)
                            {
                                $iRelayStatus = 0;
                                $sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
                                onoff_rlb_relay($sRelayNewResp);
                                $this->home_model->updateProgramStatus($iProgId, 0);
                                $this->home_model->updateAbsProgramRun($iProgId, '1');
                            }
                        }
                        else if($sProgramAbs == '1' && $iMode == 2)
                        {
                            if($sProgramActive == 1)
                            {
                                $iRelayStatus = 0;
                                $sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
                                onoff_rlb_relay($sRelayNewResp);
                                $this->home_model->updateProgramStatus($iProgId, 0);
                                $this->home_model->updateAlreadyRunTime($iProgId, $aAbsoluteDetails);
                            }
                        }
                        else
                        {
                            //on relay
                            if($sTime >= $sProgramStart && $sTime < $sProgramEnd && $sProgramActive == 0)
                            {
                                if($iMode == 1)
                                {
                                    $iRelayStatus = 1;
                                    $sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
                                    onoff_rlb_relay($sRelayNewResp);
                                    $this->home_model->updateProgramStatus($iProgId, 1);
                                }
                            }//off relay
                            else if($sTime >= $sProgramEnd && $sProgramActive == 1)
                            {
                                $iRelayStatus = 0;
                                $sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sRelayName );
                                onoff_rlb_relay($sRelayNewResp);
                                $this->home_model->updateProgramStatus($iProgId, 0);
                            }
                        } 
                    }
                }
				else if($sDevice == 'PS')
                {					
                    if($sProgramType == 1 || ($sProgramType == 2 && in_array($sDayret, $aDays)))
                    {
                        $aAbsoluteDetails       = array('absolute_s'  => $sProgramAbsStart,
                                                        'absolute_e'  => $sProgramAbsEnd,
                                                        'absolute_t'  => $sProgramAbsTotal,
                                                        'absolute_ar' => $sProgramAbsAlreadyRun,
                                                        'absolute_sd' => $sProgramAbsStartDay,
                                                        'absolute_st' => $sProgramAbsRun
                                                        ); 

                        $aPumpDetails = $this->home_model->getPumpDetails($sRelayName);						
                        //Variable Initialization to blank.
                        $sPumpNumber  	= '';
                        $sPumpType  	= '';
                        $sPumpSubType  	= '';
                        $sPumpSpeed  	= '';
                        $sPumpFlow 	= '';
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

                        //$iMode = '1';
                        if($sProgramAbs == '1' && $iMode == 1)
                        {
                            if($sTime >= $sProgramStart && $sProgramActive == 0 && $sProgramAbsRun == 0)
                            {
                                $this->home_model->updateProgramAbsDetails($iProgId, $aAbsoluteDetails);
                                $iPumpStatus = 1;

                                if($sPumpType != '' && $sPumpClosure == '1')
                                {
                                    if($sPumpType == '12' || $sPumpType == '24')
                                    {
                                        if($sPumpType == '24')
                                        {
                                            $sNewResp = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                            onoff_rlb_relay($sNewResp);
                                        }
                                        else if($sPumpType == '12')
                                        {
                                            $sNewResp = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                            onoff_rlb_powercenter($sNewResp);
                                        }
                                    }
                                    else
                                    {
                                        if(preg_match('/Emulator/',$sPumpType))
                                        {
                                            $sNewResp   =   '';
                                            $sType      =   '';
                                            if($sPumpSubType == 'VS')
                                                $sType  =   '2'.' '.$sPumpSpeed;
                                            elseif ($sPumpSubType == 'VF')
                                                $sType  =   '3'.' '.$sPumpFlow;

                                            $sNewResp =  $sRelayName.' '.$sType;
                                            onoff_rlb_pump($sNewResp);

                                            if($sPumpType == 'Emulator12')
                                            {
                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_powercenter($sNewResp12);
                                            }
                                            if($sPumpType == 'Emulator24')
                                            {
                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_relay($sNewResp24);
                                            }
                                        }
                                        else if(preg_match('/Intellicom/',$sPumpType))
                                        {
                                            $sNewResp   =   '';
                                            $sType      =   '2'.' '.$sPumpSpeed;
                                                                        
                                            $sNewResp =  $sRelayName.' '.$sType;
                                            onoff_rlb_pump($sNewResp);

                                            if($sPumpType == 'Intellicom12')
                                            {
                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_powercenter($sNewResp12);
                                            }
                                            if($sPumpType == 'Intellicom24')
                                            {
                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_relay($sNewResp24);
                                            }
                                        }
                                    }
                                }
                                $this->home_model->updateDeviceStauts($sRelayName,'PS','1');
                                $this->home_model->updateProgramStatus($iProgId, 1);
                            }
                            else if($sTime >= $sProgramAbsEnd && $sProgramActive == 1)
                            {
                                $iPumpStatus = 0;

                                if($sPumpType != '' && $sPumpClosure == '1')
                                {
                                    if($sPumpType == '12' || $sPumpType == '24')
                                    {
                                        if($sPumpType == '24')
                                        {
                                            $sNewResp = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                            onoff_rlb_relay($sNewResp);
                                        }
                                        else if($sPumpType == '12')
                                        {
                                            $sNewResp = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                            onoff_rlb_powercenter($sNewResp);
                                        }
                                    }
                                    else
                                    {
                                        if(preg_match('/Emulator/',$sPumpType))
                                        {
                                            $sNewResp = '';
                                            $sNewResp =  $sRelayName.' '.$iPumpStatus;
                                            onoff_rlb_pump($sNewResp);

                                            if($sPumpType == 'Emulator12')
                                            {
                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_powercenter($sNewResp12);
                                            }
                                            if($sPumpType == 'Emulator24')
                                            {
                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_relay($sNewResp24);
                                            }
                                        }
                                        else if(preg_match('/Intellicom/',$sPumpType))
                                        {
                                            $sNewResp = '';
                                            $sNewResp =  $sRelayName.' '.$iPumpStatus;
                                            onoff_rlb_pump($sNewResp);

                                            if($sPumpType == 'Intellicom12')
                                            {
                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_powercenter($sNewResp12);
                                            }
                                            if($sPumpType == 'Intellicom24')
                                            {
                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_relay($sNewResp24);
                                            }
                                        }
                                    }
                                }
                                $this->home_model->updateDeviceStauts($sRelayName,'PS','0');
                                $this->home_model->updateProgramStatus($iProgId, 0);
                                $this->home_model->updateAbsProgramRun($iProgId, '1');
                                $this->home_model->updateAbsProgramRunDetails($iProgId, '1');
                            }
                        }
                        else if($sProgramAbs == '1' && $iMode == 2 )
                        {
                            if($sProgramActive == 1)
                            {
                                $iPumpStatus = 0;

                                if($sPumpType != '' && $sPumpClosure == '1')
                                {
                                    if($sPumpType == '12' || $sPumpType == '24')
                                    {
                                        if($sPumpType == '24')
                                        {
                                            $sNewResp = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                            onoff_rlb_relay($sNewResp);
                                        }
                                        else if($sPumpType == '12')
                                        {
                                            $sNewResp = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                            onoff_rlb_powercenter($sNewResp);
                                        }
                                    }
                                    else
                                    {
                                        if(preg_match('/Emulator/',$sPumpType))
                                        {
                                            $sNewResp = '';
                                            $sNewResp =  $sRelayName.' '.$iPumpStatus;
                                            onoff_rlb_pump($sNewResp);

                                            if($sPumpType == 'Emulator12')
                                            {
                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_powercenter($sNewResp12);
                                            }
                                            if($sPumpType == 'Emulator24')
                                            {
                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_relay($sNewResp24);
                                            }
                                        }
                                        else if(preg_match('/Intellicom/',$sPumpType))
                                        {
                                            $sNewResp = '';
                                            $sNewResp =  $sRelayName.' '.$iPumpStatus;
                                            onoff_rlb_pump($sNewResp);

                                            if($sPumpType == 'Intellicom12')
                                            {
                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_powercenter($sNewResp12);
                                            }
                                            if($sPumpType == 'Intellicom24')
                                            {
                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_relay($sNewResp24);
                                            }
                                        }
                                    }
                                }
                                $this->home_model->updateDeviceStauts($sRelayName,'PS','0');
                                $this->home_model->updateProgramStatus($iProgId, 0);
                                $this->home_model->updateAlreadyRunTime($iProgId, $aAbsoluteDetails);
                            }
                        }
                        else
                        {
                            //on Pump
                            if($sTime >= $sProgramStart && $sTime < $sProgramEnd && $sProgramActive == 0)
                            {
                                if($iMode == 1)
                                {
                                    $iPumpStatus = 1;

                                    if($sPumpType != '' && $sPumpClosure == '1')
                                    {
                                        if($sPumpType == '12' || $sPumpType == '24')
                                        {
                                            if($sPumpType == '24')
                                            {
                                                $sNewResp = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_relay($sNewResp);
                                            }
                                            else if($sPumpType == '12')
                                            {
                                                $sNewResp = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                onoff_rlb_powercenter($sNewResp);
                                            }
                                        }
                                        else
                                        {
                                            if(preg_match('/Emulator/',$sPumpType))
                                                                        {
                                            $sNewResp = '';
											$sType          =   '';
											if($sPumpSubType == 'VS')
													$sType  =   '2'.' '.$sPumpSpeed;
											elseif ($sPumpSubType == 'VF')
													$sType  =   '3'.' '.$sPumpFlow;

											$sNewResp =  $sRelayName.' '.$sType;

											onoff_rlb_pump($sNewResp);

											if($sPumpType == 'Emulator12')
											{
													$sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
													onoff_rlb_powercenter($sNewResp12);
											}
											if($sPumpType == 'Emulator24')
											{
													$sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
													onoff_rlb_relay($sNewResp24);
											}
									}
									else if(preg_match('/Intellicom/',$sPumpType))
									{
											$sNewResp = '';
											$sType  =   '2'.' '.$sPumpSpeed;
											$sNewResp =  $sRelayName.' '.$sType;
											onoff_rlb_pump($sNewResp);

											if($sPumpType == 'Intellicom12')
											{
													$sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
													onoff_rlb_powercenter($sNewResp12);
											}
											if($sPumpType == 'Intellicom24')
											{
													$sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
													onoff_rlb_relay($sNewResp24);
											}
									}
							}
					}                
					$this->home_model->updateDeviceStauts($sRelayName,'PS','1');
                    $this->home_model->updateProgramStatus($iProgId, 1);
                }
            }//off Pump
            else if($sTime >= $sProgramEnd && $sProgramActive == 1)
            {
                $iPumpStatus = 0;

                                                if($sPumpType != '' && $sPumpClosure == '1')
                                                {
                                                        if($sPumpType == '12' || $sPumpType == '24')
                                                        {
                                                                if($sPumpType == '24')
                                                                {
                                                                        $sNewResp = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                                        onoff_rlb_relay($sNewResp);
                                                                }
                                                                else if($sPumpType == '12')
                                                                {
                                                                        $sNewResp = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                                        onoff_rlb_powercenter($sNewResp);
                                                                }
                                                        }
                                                        else
                                                        {
                                                                if(preg_match('/Emulator/',$sPumpType))
                                                                {
                                                                        $sNewResp = '';
                                                                        $sNewResp =  $sRelayName.' '.$iPumpStatus;
                                                                        onoff_rlb_pump($sNewResp);

                                                                        if($sPumpType == 'Emulator12')
                                                                        {
                                                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                                                onoff_rlb_powercenter($sNewResp12);
                                                                        }
                                                                        if($sPumpType == 'Emulator24')
                                                                        {
                                                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                                                onoff_rlb_relay($sNewResp24);
                                                                        }
                                                                }
                                                                else if(preg_match('/Intellicom/',$sPumpType))
                                                                {
                                                                        $sNewResp = '';
                                                                        $sNewResp =  $sRelayName.' '.$iPumpStatus;
                                                                        onoff_rlb_pump($sNewResp);

                                                                        if($sPumpType == 'Intellicom12')
                                                                        {
                                                                                $sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
                                                                                onoff_rlb_powercenter($sNewResp12);
                                                                        }
                                                                        if($sPumpType == 'Intellicom24')
                                                                        {
                                                                                $sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
                                                                                onoff_rlb_relay($sNewResp24);
                                                                        }
                                                                }
                                                        }
                                                }
                                                $this->home_model->updateDeviceStauts($sRelayName,'PS','0');
                $this->home_model->updateProgramStatus($iProgId, 0);
            }
        } 
    }
                }
            }
        }

    }
    
    public function checkDeviceManualTime() //START : Function to make the Device OFF if Time is set in Manual Mode.
    {
        $this->load->model('home_model');
        //Get the current status from the Relay board.
        $sResponse      =   get_rlb_status();
        
        $sRelays        =   $sResponse['relay'];
        $sTime          =   $sResponse['time'];
        
        //Get Current Mode of the System.
        $iMode          =   $this->home_model->getActiveMode();
        //Get All device with Time 
        $aAllTime       =   $this->home_model->getAllDeviceTimeDetails();
        
        //START : If atleast 1 device has Time and Mode is Manual.
		if(is_array($aAllTime) && !empty($aAllTime) && $iMode == 2)
		{
			foreach($aAllTime as $aResultTime)
			{
				$sDeviceName     = $aResultTime->device_number;
				$sDevice         = $aResultTime->device_type;
				$sDeviceEnd      = $aResultTime->device_end_time;

				if($sTime >= $sDeviceEnd)//If Device End time is passed then switch OFF the DEVICE.
				{
					$iRelayStatus = 0;
					$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $sDeviceName );
					
					//Update the start time and end time of the Device.
					$this->home_model->updateDeviceRunTime($sDeviceName, $sDevice, $iRelayStatus);
					onoff_rlb_relay($sRelayNewResp);
				}
			}
		}
        //END : If atleast 1 device has Time and Mode is Manual.
    }
	
	public function changeManualTimeMode() //START : Function to Manual Mode to auto after specified Time.
    {
        $this->load->model('home_model');
        
		//Get the timer start and end time.
        $aTimer			=	$this->home_model->getManualModeTimer();
		$sTime			=	date('H:i:s',time());
        //Get Current Mode of the System.
        $iActiveMode          =   $this->home_model->getActiveMode();
		$iMode = '1';
		if($aTimer['END'] != '')
		{
			if($sTime >= $aTimer['END'])//If Device End time is passed then switch OFF the DEVICE.
			{
				if($iActiveMode != $iMode)
				{
					$this->home_model->updateMode($iMode);
					
					//Get the current status from the Relay board.
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
			}
		}	
    }//END : Function to Manual Mode to auto after specified Time.
	
	public function checkIP()
	{
		$apache_port_number =	'8097'; 
		$sDeviceIP			=	'';
		$sSql   =   "SELECT ip_external FROM rlb_setting WHERE id = '1'";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $rowResult)
            {
                $sDeviceIP = $rowResult->ip_external; 
            }
        }

       if($sDeviceIP != '')
	   {
		   $url = 'http://www.lvnvacationhomerentals.com/checkRaspberryIP.php';
		   $fields = array(
						'IP' => urlencode($sDeviceIP),
						'PORT' => urlencode($apache_port_number)
				);
			
			$fields_string = '';
			foreach($fields as $key=>$value) 
			{ 
				$fields_string .= $key.'='.$value.'&'; 
			}
			rtrim($fields_string, '&');	
			
			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
			
			//execute post
			$result = json_decode(curl_exec($ch));

			//close connection
			curl_close($ch);
			
			if($result->Status == '0')
			{
				$strIPToChange	=	$result->IP;
				if($strIPToChange != '')
				{
					$sSqlUpdate   =   "UPDATE rlb_setting SET ip_external = '".$strIPToChange."' WHERE id = '1'";
					$query  =   $this->db->query($sSqlUpdate);
				}
			}
	   }
	}
	
	
	function resetAllAbsolutePrograms()
	{
		$this->load->model('home_model');
		
		//Get Real System Response.
		$sResponse      =   get_rlb_status();
		
		//Assign Device Response Seperatly.
		$sValves        =   $sResponse['valves'];
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
		
		//Get all the absolute programs details.
		$arrAbsDetails	=	$this->home_model->getAllAbsoluteProgramDetails();
		//$aAllProgram    =   $this->home_model->getAllProgramsDetails();
		
		if(!empty($arrAbsDetails))
		{
			foreach($arrAbsDetails as $arrProgramDetails)
			{
				$iProgramID	=	$arrProgramDetails->program_id;
				$iDeviceNum	=	$arrProgramDetails->device_number;
				//First Check if the program is running.
				$iCheck	=	$this->home_model->chkAbsoluteProgramRunning($iProgramID);
				if($iCheck != '')
				{
					if($iCheck == '1') // IF program is running then first stop that device.
					{
						if($arrProgramDetails->device_type == 'PS')
						{
							$aPumpDetails = $this->home_model->getPumpDetails($arrProgramDetails->device_number);						
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
										onoff_rlb_relay($sNewResp);
									}
									else if($sPumpType == '12')
									{
										$sNewResp = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
										onoff_rlb_powercenter($sNewResp);
									}
								}
								else
								{
									if(preg_match('/Emulator/',$sPumpType))
									{
										$sNewResp = '';
										$sNewResp =  $sPumpNumber.' '.$iPumpStatus;
										onoff_rlb_pump($sNewResp);
										
										if($sPumpType == 'Emulator12')
										{
											$sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
											onoff_rlb_powercenter($sNewResp12);
										}
										if($sPumpType == 'Emulator24')
										{
											$sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
											onoff_rlb_relay($sNewResp24);
										}
									}
									else if(preg_match('/Intellicom/',$sPumpType))
									{
										$sNewResp = '';
										$sNewResp =  $sPumpNumber.' '.$iPumpStatus;
										onoff_rlb_pump($sNewResp);
										
										if($sPumpType == 'Intellicom12')
										{
											$sNewResp12 = replace_return($sPowercenter, $iPumpStatus, $sRelayNumber );
											onoff_rlb_powercenter($sNewResp12);
										}
										if($sPumpType == 'Intellicom24')
										{
											$sNewResp24 = replace_return($sRelays, $iPumpStatus, $sRelayNumber );
											onoff_rlb_relay($sNewResp24);
										}
									}
								}
							}
						}
						else if($arrProgramDetails->device_type == 'R')
						{
							$iRelayStatus = 0;
							$sRelayNewResp = replace_return($sRelays, $iRelayStatus, $iDeviceNum );
							onoff_rlb_relay($sRelayNewResp); // OFF Relay Device;
						}
					}
					
					//Reset the run status.
					//$this->home_model->chkAbsoluteProgramRunning($iProgramID);
				}
				if($arrProgramDetails->device_type == 'PS')
				{
					$this->home_model->updateDeviceStauts($iDeviceNum,'PS','0');
				}
				
				if($iProgramID)
				{
					$data = array('program_absolute_start_time' => '','program_absolute_end_time'=>'','program_absolute_run_time'=>'','program_absolute_start_date'=>'');
					$this->db->where('program_id', $iProgramID);
					$this->db->update('rlb_program', $data);
				}
				
				$this->home_model->updateProgramStatus($iProgramID, 0);
				$this->home_model->updateAbsProgramRun($iProgramID, '0');
			}
		}
		
	}
	
	public function getPumpProgramStatus()
	{
		$iPumpID    =   $_GET['iPumpID'];
		$this->load->model('home_model');
		$aAllActiveProgram	=	$this->home_model->getAllActiveProgramsForPump($iPumpID);
		$strMessage		=	'';	
		if(!empty($aAllActiveProgram))
		{
			foreach($aAllActiveProgram as $aActiveProgram)
			{
				if($aActiveProgram->device_type == 'PS')
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

					if($strMessage != '')
					{
						$strMessage .= ' <br /><strong>'.$aActiveProgram->program_name.'</strong> Program is Running for <strong>Pump '.$aActiveProgram->device_number.'</strong>';

						if($sPumpType	==	'Emulator' && $sPumpSubType == 'VS')
						{
								$strMessage .= ' With <strong>Speed '.$sPumpSpeed.' </strong>';
						}
						else if($sPumpType	==	'12')
						{
							$strMessage .= ' With <strong> 12V DC Relays</strong>';
						}
						else if($sPumpType	==	'24')
						{
							$strMessage .= ' With <strong> 24V AC Relays</strong>';
						}
						else if($sPumpType	==	'2Speed')
						{
							if($sPumpSubType == '12')
							{
								$strMessage .= ' With <strong> 2 Speed 12V DC Relays</strong>';
							}
							else if($sPumpSubType == '24')
							{
								$strMessage .= ' With <strong> 2 Speed 24V AC Relays</strong>';
							}
						}
						$strMessage .= '!';
					}
					else
					{
						$strMessage .= '<strong>'.$aActiveProgram->program_name.'</strong> Program is Running for <strong>Pump '.$aActiveProgram->device_number.'</strong>';

						if($sPumpType	==	'Emulator' && $sPumpSubType == 'VS')
						{
							$strMessage .= ' With <strong>Speed '.$sPumpSpeed.' </strong>';
						}
						else if($sPumpType	==	'12')
						{
							$strMessage .= ' With <strong> 12V DC Relays</strong>';
						}
						else if($sPumpType	==	'24')
						{
							$strMessage .= ' With <strong> 24V AC Relays</strong>';
						}
						else if($sPumpType	==	'2Speed')
						{
							if($sPumpSubType == '12')
							{
								$strMessage .= ' With <strong> 2 Speed 12V DC Relays</strong>';
							}
							else if($sPumpSubType == '24')
							{
								$strMessage .= ' With <strong> 2 Speed 24V AC Relays</strong>';
							}
						}
						$strMessage .= '!';
					}
				}
			}
		}
		else //Check if Pump is ON manually then take the details and show.
		{
			//First Check if the pump is ON.
			$iCheck = $this->home_model->selectPumpsStatus($iPumpID);
			
			//If Pump is ON.
			if($iCheck)
			{
				$aPumpDetails 	=	$this->home_model->getPumpDetails($iPumpID);

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
				//Start Generating the status message.
				if($strMessage != '')
				{
					$strMessage .= ' <br /><strong>Pump '.$aActiveProgram->device_number.'</strong> is Running';

					if($sPumpType	==	'Emulator' && $sPumpSubType == 'VS')
					{
						$strMessage .= ' With <strong>Speed '.$sPumpSpeed.' </strong>';
					}
					else if($sPumpType	==	'12')
					{
						$strMessage .= ' With <strong> 12V DC Relays</strong>';
					}
					else if($sPumpType	==	'24')
					{
						$strMessage .= ' With <strong> 24V AC Relays</strong>';
					}
					else if($sPumpType	==	'2Speed')
					{
						if($sPumpSubType == '12')
						{
							$strMessage .= ' With <strong> 2 Speed 12V DC Relays</strong>';
						}
						else if($sPumpSubType == '24')
						{
							$strMessage .= ' With <strong> 2 Speed 24V AC Relays</strong>';
						}
					}
					$strMessage .= '!';
				}
				else
				{
					$strMessage .= '<strong>Pump '.$aActiveProgram->device_number.'</strong> is Running';

					if($sPumpType	==	'Emulator' && $sPumpSubType == 'VS')
					{
							$strMessage .= ' With <strong>Speed '.$sPumpSpeed.' </strong>';
					}
					else if($sPumpType	==	'12')
					{
						$strMessage .= ' With <strong> 12V DC Relays</strong>';
					}
					else if($sPumpType	==	'24')
					{
						$strMessage .= ' With <strong> 24V AC Relays</strong>';
					}
					else if($sPumpType	==	'2Speed')
					{
						if($sPumpSubType == '12')
						{
							$strMessage .= ' With <strong> 2 Speed 12V DC Relays</strong>';
						}
						else if($sPumpSubType == '24')
						{
							$strMessage .= ' With <strong> 2 Speed 24V AC Relays</strong>';
						}
					}
					$strMessage .= '!';
				}//End Generating the status message.
			}//If pump is ON.
		}

		$aResult['message']	=	$strMessage;
		echo json_encode($aResult['message']);
	}
	
	public function rebootSystem()
	{
		$this->load->model('home_model');
		//First Take all active programs.
		$aAllActiveProgram	=	$this->home_model->getAllActiveProgramsNew();
		
		if(!empty($aAllActiveProgram))
		{
			foreach($aAllActiveProgram as $aActive)
			{
				//Update all active programs status to inactive.
				$strChkProgram	=	"UPDATE rlb_program SET program_active = '0', is_on_after_reboot = '1' WHERE program_id = '".$aActive->program_id."'";
				$query  =   $this->db->query($strChkProgram);
				
			}
		}
		
		exec("sudo /sbin/reboot");
	}
	
	/* function testShell()
	{
		$myFile = "/var/www/relayboard/testshell.txt";
		$fh = fopen($myFile, 'a') or die("Can't open file");
		$stringData = "File updated at: " . time(). "\n";
		fwrite($fh, $stringData);
		fclose($fh);
	} */
	
	function tp()
	{
		$this->template->build('welcome_message');
	}
}

?>

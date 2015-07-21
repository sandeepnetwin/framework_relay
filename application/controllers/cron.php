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
        $seconds = 2;
        $micro = $seconds * 1000000;
        $this->load->model('analog_model');
        $this->load->model('home_model');
        while(true)
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
                $sResponse =   get_rlb_status();
                $aAP       =   array($sResponse['AP0'],$sResponse['AP1'],$sResponse['AP2'],$sResponse['AP3']);
                $aAP         =   array(0,1,0,1);

                $sValves        =   $sResponse['valves'];
                $sRelays        =   $sResponse['relay'];
                $sPowercenter   =   $sResponse['powercenter'];

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
                                $sNewResp = replace_return($sRelays, $aAP[$i], $aDevice[0] );
                                onoff_rlb_relay($sNewResp);
                                //exex('rlb m 0 2 1');
                            }
                            if($aDevice[1] == 'P')
                            {
                                $sNewResp = replace_return($sPowercenter, $aAP[$i], $aDevice[0] );
                                onoff_rlb_powercenter($sNewResp);
                            }
                            if($aDevice[1] == 'V')
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
            } 

            /*$myFile = "/var/www/relay_framework/daemontest1.txt";
            $fh = fopen($myFile, 'a') or die("Can't open file");
            $stringData = "File updated at: " . $sResponse. "\n";
            fwrite($fh, $stringData);
            fclose($fh); 
            usleep($micro); */    
        }      
    }

    public function program()
    {
        $this->load->model('home_model');
        $sResponse      =   get_rlb_status();
        //$sResponse      =   array('valves'=>'','powercenter'=>'0000','time'=>'','relay'=>'0000','day'=>'');
        $sValves        =   $sResponse['valves'];
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
        $sTime          =   $sResponse['time'];
        $sDayret        =   $sResponse['day'];
        $aTime          =   explode(':',$sTime);

        $iRelayCount    =   strlen($sRelays);
        $iValveCount    =   strlen($sValves);
        $iPowerCount    =   strlen($sPowercenter);

        $iMode          =   $this->home_model->getActiveMode();
        //$iMode          =   $this->uri->segment('3');
        //$sTime          =   date('H:i:s',time());
        $aAllProgram    =   $this->home_model->getAllProgramsDetails();
        
        // die;
        if(is_array($aAllProgram) && !empty($aAllProgram))
        {
            foreach($aAllProgram as $aResultProgram)
            {
                $sRelayName     = $aResultProgram->relay_number;
                $iProgId        = $aResultProgram->relay_prog_id;
                $sProgramType   = $aResultProgram->relay_prog_type;
                $sProgramStart  = $aResultProgram->relay_start_time;
                $sProgramEnd    = $aResultProgram->relay_end_time;
                $sProgramActive = $aResultProgram->relay_prog_active;
                $sProgramDays   = $aResultProgram->relay_prog_days;
                
                $sProgramAbs            = $aResultProgram->relay_prog_absolute;
                $sProgramAbsStart       = $aResultProgram->relay_prog_absolute_start_time;
                $sProgramAbsEnd         = $aResultProgram->relay_prog_absolute_end_time;
                $sProgramAbsTotal       = $aResultProgram->relay_prog_absolute_total_time;
                $sProgramAbsAlreadyRun  = $aResultProgram->relay_prog_absolute_run_time;

                $sProgramAbsStartDay    = $aResultProgram->relay_prog_absolute_start_date;
                $sProgramAbsRun         = $aResultProgram->relay_prog_absolute_run;

                $sDays          =   '';
                $aDays          =   array();

                if($sProgramType == 2)
                {
                    $sDays = str_replace('7','0', $sProgramDays);
                    $aDays = explode(',',$sProgramDays);
                }

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
        }

    }
}

?>

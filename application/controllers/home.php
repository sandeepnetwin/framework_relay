<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('common_functions');
        if (!$this->session->userdata('is_admin_login')) {
            redirect('dashboard/login/');
        }
    }

    public function index() 
    {
        $aViewParameter['page'] ='home';

        $this->checkSettingsSaved();

        $sResponse      =   get_rlb_status();

        //$sResponse    =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves'];
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
        $sTime          =   $sResponse['time'];
        $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
          
        $aViewParameter['relay_count']  =   strlen($sRelays);
        $aViewParameter['valve_count']  =   strlen($sValves);
        $aViewParameter['power_count']  =   strlen($sPowercenter);
        $aViewParameter['time']         =   $sTime;

        $aViewParameter['pump_count']   =   count($sPump);

        $this->load->view('Home',$aViewParameter);

    }
  

    public function setting()
    {
        $aViewParameter['page']         =   'home';
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
        
        $sPage  =   $this->uri->segment('3'); 

        $this->load->model('home_model');

        $aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();

        if($sPage == '')
        {
            $aViewParameter['page']         =   'setting';
            
            if($this->input->post('command') == 'Save Setting')
            {
                $iMode  =   $this->input->post('relay_mode');
                $this->load->model('home_model');
                
                $sIP    =   $this->input->post('relay_ip_address');
                $sPort  =   $this->input->post('relay_port_no');

                if($sIP == '')
                {
                    if(IP_ADDRESS){
                        $sIP = IP_ADDRESS;
                    }
                }
                
                //Check for Port Number constant
                if($sPort == '')
                {   
                    if(PORT_NO){
                        $sPort = PORT_NO;
                    }
                }

                if($sIP == '' || $sPort == '')
                {
                    $aViewParameter['err_sucess']    =   '1';
                }
                else
                {
                
                    $this->home_model->updateSetting($sIP,$sPort);
                    
                    $this->home_model->updateMode($iMode);

                    $sResponse      =   get_rlb_status();
                    $sValves        =   $sResponse['valves'];
                    $sRelays        =   $sResponse['relay'];
                    $sPowercenter   =   $sResponse['powercenter'];

                    if($iMode == 3 || $iMode == 1)
                    { //1-auto, 2-manual, 3-timeout
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

                
               
            }
            
            $aModes =   $this->home_model->getAllModes();
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

            list($aViewParameter['sIP'],$aViewParameter['sPort']) = $this->home_model->getSettings();

            $this->load->view('Setting',$aViewParameter);
        }
        else
        {
            $this->checkSettingsSaved();

            $sResponse      =   get_rlb_status();
            //$sResponse      =   array('valves'=>'','powercenter'=>'0000','time'=>'','relay'=>'0000');
            $sValves        =   $sResponse['valves'];
            $sRelays        =   $sResponse['relay'];
            $sPowercenter   =   $sResponse['powercenter'];
            $sTime          =   $sResponse['time'];
            $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);


            $aViewParameter['relay_count']      =   strlen($sRelays);
            $aViewParameter['valve_count']      =   strlen($sValves);
            $aViewParameter['power_count']      =   strlen($sPowercenter);
            $aViewParameter['time']             =   $sTime;
            $aViewParameter['pump_count']       =   count($sPump);

            $aViewParameter['sRelays']          =   $sRelays; 
            $aViewParameter['sPowercenter']     =   $sPowercenter;
            $aViewParameter['sValves']          =   $sValves;
            $aViewParameter['sPump']            =   $sPump;

            $aViewParameter['sDevice']          =   $sPage;
            

            $this->load->view('Device',$aViewParameter); 
        }
    }

    public function updateStatusOnOff()
    {
        $sResponse      =   get_rlb_status();
        //$sResponse      =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves'];
        $sRelays        =   $sResponse['relay'];
        $sPowercenter   =   $sResponse['powercenter'];
        $sTime          =   $sResponse['time'];

        $sName          =   $this->input->post('sName');
        $sStatus        =   $this->input->post('sStatus');
        $sDevice        =   $this->input->post('sDevice');

        $sNewResp       =   '';

        if($sDevice == 'R')
        {
            $sNewResp = replace_return($sRelays, $sStatus, $sName );
            onoff_rlb_relay($sNewResp);
        }
        if($sDevice == 'P')
        {
            $sNewResp = replace_return($sPowercenter, $sStatus, $sName );
            onoff_rlb_powercenter($sNewResp);
        }
        if($sDevice == 'V')
        {
            //echo $sStatus;
            $sNewResp = replace_return($sValves, $sStatus, $sName );
            onoff_rlb_valve($sNewResp);
        }
        if($sDevice == 'PS')
        {
            $sNewResp = '';

            if($sStatus == '0')
                $sNewResp =  $sName.' '.$sStatus;
            else if($sStatus == '1')
            {
                $this->load->model('home_model');
                $aPumpDetails   =   $this->home_model->getPumpDetails($sName);
                foreach($aPumpDetails as $aResultPumpDetails)
                {
                    $sType          =   '';

                    if($aResultPumpDetails->pump_type == '2')
                        $sType  =   $aResultPumpDetails->pump_type.' '.$aResultPumpDetails->pump_speed;
                    elseif ($aResultPumpDetails->pump_type == '3')
                        $sType  =   $aResultPumpDetails->pump_type.' '.$aResultPumpDetails->pump_flow;

                    $sNewResp =  $sName.' '.$sType;    
                }
                
            }    
            onoff_rlb_pump($sNewResp);
        }


        exit;
    }

    public function deviceName()
    {
       
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        $sDeviceID  =   base64_decode($this->uri->segment('3'));
        $sDevice    =   base64_decode($this->uri->segment('4'));

        if($sDeviceID == '')   
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '')
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        }

        if($sDevice == '')   
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '')
                redirect(site_url('home'));
        }

        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;

        $this->load->model('home_model');

        if($this->input->post('command') == 'Save')
        {
            $sDeviceName = $this->input->post('sDeviceName');
            $this->home_model->saveDeviceName($sDeviceID,$sDevice,$sDeviceName);

            $aViewParameter['sucess']    =   '1';
        }

        $aViewParameter['sDeviceName']      =   $this->home_model->getDeviceName($sDeviceID,$sDevice);

        $this->load->view('DeviceName',$aViewParameter); 
    }

    public function setPrograms()
    {
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        $sDeviceID      =   base64_decode($this->uri->segment('3'));
        $sProgramID     =   base64_decode($this->uri->segment('4'));
        $sProgramDelete =   $this->uri->segment('5');

        $this->load->model('home_model');

        if($sDeviceID == '')   
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '')
                redirect(site_url('home/setting/R'));
        }
        
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        
        if($this->input->post('command') == 'Save')
        {
            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber');

            $this->home_model->saveProgramDetails($this->input->post(),$sDeviceID);
            $aViewParameter['sucess']    =   '1';
        }

        if($this->input->post('command') == 'Update')
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
            }  

            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber'); 

            $this->home_model->updateProgramDetails($this->input->post(),$sProgramID,$sDeviceID);
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
        }

        if($sProgramDelete != '' && $sProgramDelete == 'D')
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
            }

            $this->home_model->deleteProgramDetails($sProgramID);
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
        }

        $aViewParameter['sProgramDetails'] = $this->home_model->getProgramDetailsForDevice($sDeviceID);

        if($sProgramID != '')
        {
            $aViewParameter['sProgramID'] = $sProgramID;
            $aViewParameter['sProgramDetailsEdit'] = $this->home_model->getProgramDetails($sProgramID);
        }
        else
        {
            $aViewParameter['sProgramID']          = ''; 
            $aViewParameter['sProgramDetailsEdit'] = '';
        }
        $this->load->view('Programs',$aViewParameter); 
    }

    public function pumpConfigure()
    {
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        $sDeviceID      =   base64_decode($this->uri->segment('3'));
       
        $this->load->model('home_model');

        if($sDeviceID == '')   
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '')
                redirect(site_url('home/setting/PS/'));
        }

        if($this->input->post('command') == 'Save')
        {
            if($this->input->post('sPumpNumber') != '')
                $sDeviceID   =  $this->input->post('sPumpNumber');

            $this->home_model->savePumpDetails($this->input->post(),$sDeviceID);
            $aViewParameter['sucess']    =   '1';
        }
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sPumpDetails'] = $this->home_model->getPumpDetails($sDeviceID);
        $this->load->view('Pump',$aViewParameter); 
    }

    public function checkSettingsSaved()
    {
        $this->load->model('home_model');
        list($sIpAddress, $sPortNo) = $this->home_model->getSettings();
        
        if($sIpAddress == '')
        {
            if(IP_ADDRESS){
                $sIpAddress = IP_ADDRESS;
            }
        }
        
        //Check for Port Number constant
        if($sPortNo == '')
        {   
            if(PORT_NO){
                $sPortNo = PORT_NO;
            }
        }

        if($sIpAddress == '' || $sPortNo == '')
            redirect(site_url('home/setting/'));
    }

    public function systemStatus()
    {
        $aViewParameter['page'] ='status';
        
        $this->checkSettingsSaved();
        $sResponse      =   get_rlb_status();
                
        $aViewParameter['response'] =$sResponse['response'];
         
        $this->load->view('Status',$aViewParameter);
    }

    
    
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
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
        }
        
    }
    
    public function index()
    {
        $aViewParameter =   array();
        
        $aViewParameter['page'] ='home';
        $this->load->model('analog_model');
        $aViewParameter['sucess'] =   '0';

        
        $aObjHome = new Home();   
        $aObjHome->checkSettingsSaved(); 
        
        $sResponse      =   get_rlb_status();

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
        

        if($this->input->post('command') == 'Save')
        {
            $sDeviceName = $this->input->post('sDeviceName');
            $this->analog_model->saveAnalogDevice($sDeviceName);
            $aViewParameter['sucess'] =   '1';
        }

        $aAllAnalogDevice          = $this->analog_model->getAllAnalogDevice();
        $aAllANalogDeviceDirection = $this->analog_model->getAllAnalogDeviceDirection();
        
        $aViewParameter['aResponse']    =   array('AP0' => $sResponse['AP0'],
                                                  'AP1' => $sResponse['AP1'],
                                                  'AP2' => $sResponse['AP2'],
                                                  'AP3' => $sResponse['AP3']);

        $aViewParameter['aAllAnalogDevice']             =   $aAllAnalogDevice;
        $aViewParameter['aAllANalogDeviceDirection']    =   $aAllANalogDeviceDirection;  

        $this->load->view('Analog',$aViewParameter);
    }

    public function changeMode()
    {
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
        $aViewParameter['page']         =   'home';

        $this->load->model('home_model');

        if($this->input->post('iMode') != '')
        {
            $iMode = $this->input->post('iMode');
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

        $aViewParameter['iMode']  =   $this->home_model->getActiveMode();

        $this->load->view("Mode",$aViewParameter);

    }
}

?>

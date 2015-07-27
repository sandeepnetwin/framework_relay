<?php
    /**
    * @Programmer: Dhiraj S.
    * @Created: 21 July 2015
    * @Modified: 
    * @Description: Webservice to on/off Devices and Get status of Devices.
    **/
    
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');
    
    class Web extends CI_Controller
    {
        public function __construct() 
        {
            parent::__construct(); // Parent Contructor Call
            $this->load->helper('common_functions'); // Loaded helper : To get all functions accessible from common_functions file.
            
        } // END : function __construct()
        
        public function changeDeviceStatus()
        {
            #INPUTS
            $sDevice         = isset($_REQUEST['dvc']) ? $_REQUEST['dvc'] : '' ; // Get the Device(ie. R=Relay,V=Valve,PC=Power Center)
            $sDeviceNo       = isset($_REQUEST['dn'])  ? $_REQUEST['dn'] : '' ;  // Get the Device No.
            $iDeviceStatus   = isset($_REQUEST['ds']) ? $_REQUEST['ds'] : '' ;   // Get the status to which Device will be changed         
            $sUsername       = isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ;   // Get the username of webservice 
            $sPassword       = isset($_REQUEST['sPassword']) ? $_REQUEST['sPassword'] : '' ;   // Get the password of webservice 
            
            $aAuthorisation     = array();
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']  = 0;
            $aDeviceStatus      = array('0', '1', '2'); //respective values of status.
            
            $aAuthorisation = json_decode($this->webAuthorisation($sUsername, $sPassword));
            if($aAuthorisation['status'] == '0')
            { 
                $aResult['msg'] = $aAuthorisation['msg'];
                echo json_encode($aResult);
                exit;
            }
            
            $this->load->model('home_model');
            $iActiveMode =  $this->home_model->getActiveMode();
                        
            if($iActiveMode == 2) // START : If Mode is Manual.
            {
                if($sDeviceNo != '' && in_array($iDeviceStatus, $aDeviceStatus) && $sDevice != '') 
                {
                    $sResponse      =   get_rlb_status(); // Get the relay borad response from server.
                    $sValves        =   $sResponse['valves']; // Valve Devices.
                    $sRelays        =   $sResponse['relay'];  // Relay Devices.
                    $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
                    $sRelayNewResp  =   '';
                    
                    if($sDevice == 'R') // START : If Device is Relay.
                    {
                        if($sRelays != '') // START : Check if Relay devices are available.
                        {
                            $iRelayCount    = strlen($sRelays); // Count of Relay Devices.
                            if( $sDeviceNo > ($iRelayCount-1) || $sDeviceNo < 0)
                            {
                                    $aResult['msg'] = "Invalid relay number.";
                            } // END : if( $sDeviceNo > ($iRelayCount-1) || $sDeviceNo < 0)
                            else
                            {
                                    $sRelayNewResp = replace_return($sRelays, $iDeviceStatus, $sDeviceNo ); // Change the status with the sent status for the device no.
                                    onoff_rlb_relay($sRelayNewResp); // Send the request to change the status on server.		
                                    $aResult['response'] = 1;
                                    $aResult['msg'] = "Relay status changed successfully.";
                            } // END : else of if( $sDeviceNo > ($iRelayCount-1) || $sDeviceNo < 0)
                        }
                        else
                            $aResult['msg'] = "Relay devices not available.";
                        
                    } // END : if($sDevice == 'R')
                                        
                    if($sDevice == 'PC') // START : If Device is Power Center.
                    {
                        if($sPowercenter != '') // START : Check if Power Center devices are available.
                        {
                            $iPowerCenterCount    = strlen($sPowercenter); // Count of Power Center Devices.
                            if( $sDeviceNo > ($iPowerCenterCount-1) || $sDeviceNo < 0)
                            {
                                    $aResult['msg'] = "Invalid Power Center number.";
                            } // END : if( $sDeviceNo > ($iPowerCenterCount-1) || $sDeviceNo < 0)
                            else
                            {
                                    $sRelayNewResp = replace_return($sPowercenter, $iDeviceStatus, $sDeviceNo ); // Change the status with the sent status for the device no.
                                    onoff_rlb_powercenter($sRelayNewResp); // Send the request to change the status on server.		
                                    $aResult['response'] = 1;
                                    $aResult['msg'] = "Power Center status changed successfully.";
                            } // END : else of if( $sDeviceNo > ($iPowerCenterCount-1) || $sDeviceNo < 0)
                        }
                        else
                            $aResult['msg'] = "Power Center devices not available.";
                        
                    } // END : if($sDevice == 'PC')
                                        
                    if($sDevice == 'V') // START : If Device is Power Center.
                    {
                        if($sValves != '') // START : Check if Valve devices are available.
                        {
                            $iValveCount    = strlen($sValves); // Count of Power Center Devices.
                            if( $sDeviceNo > ($iValveCount-1) || $sDeviceNo < 0)
                            {
                                    $aResult['msg'] = "Invalid Valve number.";
                            } // END : if( $sDeviceNo > ($iValveCount-1) || $sDeviceNo < 0)
                            else
                            {
                                    $sRelayNewResp = replace_return($sValves, $iDeviceStatus, $sDeviceNo ); // Change the status with the sent status for the device no.
                                    onoff_rlb_valve($sRelayNewResp); // Send the request to change the status on server.		
                                    $aResult['response'] = 1;
                                    $aResult['msg'] = "Valve status changed successfully.";
                            } // END : else of if( $sDeviceNo > ($iValveCount-1) || $sDeviceNo < 0)
                        }
                        else
                            $aResult['msg'] = "Valve devices not available."; 
                        
                    } // END : if($sDevice == 'V')
                    
                } // END : if($sDeviceNo != '' && in_array($iDeviceStatus, $aDeviceStatus) && $sDevice != '')
                else
                    $aResult['msg'] = "Invalid Device number Or Device status OR Device Type.";
            } // END : if($iActiveMode == 2)
            else
                $aResult['msg'] = "Invalid mode to perform this operation.";
            
            #OUTPUT
            echo json_encode($aResult);
            
        } //END : function changeDeviceStatus()
        
         public function getDeviceStatus()
         {
            #INPUTS
            $sDevice         = isset($_REQUEST['dvc']) ? $_REQUEST['dvc'] : '' ;
            
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']= 0;
            $aResult['status']  = 0;
            
            $sValves            = ""; // Valve Devices Initialization.
            $sRelays            = "";  // Relay Devices Initialization.
            $sPowercenter       = ""; // Power Center Devices Initialization.
            $iCntValves         = "0"; // Valve Devices Count Initialization.
            $iCntRelays         = "0";  // Relay Devices Count Initialization.
            $iCntPowercenter    = "0"; // Power Center Devices Initialization.
            
            $this->load->model('home_model');
            $iActiveMode =  $this->home_model->getActiveMode();
            
             if($iActiveMode == 2) // START : If Mode is Manual.
            {
                if($sDevice != '') // START : If device type is not empty
                {
                    $sResponse      =   get_rlb_status(); // Get the relay borad response from server.
                    if($sDevice == "V") // START : If Device is Valve
                    {
                        if($sResponse['valves'] != '') // START : Checked if Valve Devices are available
                        {
                            $sValves        =   $sResponse['valves']; // Valve Devices.
                            $iCntValves     =   strlen($sValves); // Count of Valve Devices.
                            
                            $aResult['response']   = 1;
                            $aResult['status']     = $sValves;
                            $aResult['count']      = $iCntValves;
                        } // END : Checked if Valve Devices are available
                        else
                            $aResult['msg'] = "Valve devices not available.";
                    } // if($sDevice == "V") END : If Device is Valve
                    else if($sDevice == "R") // START : If Device is Relay.
                    {
                        if($sResponse['relay'] != '')
                        {
                            $sRelays        =   $sResponse['relay'];  // Relay Devices.
                            $iCntRelays     =   strlen($sRelays); // Count of Relay Devices.
                            $aResult['response'] = 1;
                            $aResult['status']   = $sRelays;
                            $aResult['count']    = $iCntRelays;
                        }
                        else
                            $aResult['msg'] = "Relay devices not available.";
                    } // END : If Device is Relay.
                    else if($sDevice == "PC") // START : If Device is Power Center.
                    {
                        if($sResponse['powercenter'] != '')
                        {
                            $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
                            $iCntPowercenter=   strlen($sPowercenter); // Count of Power Center Devices.
                            $aResult['response'] = 1;
                            $aResult['status']     = $sPowercenter;
                            $aResult['count']   = $iCntPowercenter;
                        }
                        else
                            $aResult['msg'] = "Power Center devices not available.";
                    } // END : If Device is Power Center.
                } // END : If device type is not empty. if($sDevice != '')
                else
                    $aResult['msg'] = "Invalid Device Type.";
                
                #OUTPUT
                echo json_encode($aResult);
            } // END : If Mode is Manual.
            
         } // END : getDeviceStatus()
         
         public function getDeviceNumberStatus()
         {
            #INPUTS
            $sDevice         = isset($_REQUEST['dvc']) ? $_REQUEST['dvc'] : '' ;  // Get the Device(ie. R=Relay,V=Valve,PC=Power Center)
            $sDeviceNo       = isset($_REQUEST['dn'])  ? $_REQUEST['dn'] : '' ;  // Get the Device No.
            
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['status']  = 0;
            $aResult['response']="0";
            $sValves            = ""; // Valve Devices Initialization.
            $sRelays            = "";  // Relay Devices Initialization.
            $sPowercenter       = ""; // Power Center Devices Initialization.
            
            $this->load->model('home_model');
            $iActiveMode =  $this->home_model->getActiveMode();
            
             if($iActiveMode == 2) // START : If Mode is Manual.
            {
                if($sDevice != '' && $sDeviceNo != '') // START : If device type is not empty and Valid Device number is there.
                {
                    $sResponse      =   get_rlb_status(); // Get the relay borad response from server.
                    if($sDevice == "V") // START : If Device is Valve
                    {
                        if($sResponse['valves'] != '') // START : Checked if Valve Devices are available
                        {
                            $sValves        =   $sResponse['valves']; // Valve Devices.
                            if(isset($sValves[$sDeviceNo]) && $sValves[$sDeviceNo] != '')
                            {    
                                $aResult['response']  = 1;
                                $aResult['status']     = $sValves[$sDeviceNo];
                            }
                            else
                                $aResult['msg']     = 'Device Number is not Valid'; 
                        } // END : Checked if Valve Devices are available
                        else
                            $aResult['msg'] = "Valve devices not available.";
                    } // if($sDevice == "V") END : If Device is Valve
                    else if($sDevice == "R") // START : If Device is Relay.
                    {
                        if($sResponse['relay'] != '')
                        {
                            $sRelays        =   $sResponse['relay'];  // Relay Devices.
                            if(isset($sRelays[$sDeviceNo]) && $sRelays[$sDeviceNo] != '')
                            {    
                                $aResult['response']  = 1;
                                $aResult['status']     = $sRelays[$sDeviceNo];
                            }
                            else
                                $aResult['msg']     = 'Device Number is not Valid';
                        }
                        else
                            $aResult['msg'] = "Relay devices not available.";
                    } // END : If Device is Relay.
                    else if($sDevice == "PC") // START : If Device is Power Center.
                    {
                        if($sResponse['powercenter'] != '')
                        {
                            $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
                            if(isset($sPowercenter[$sDeviceNo]) && $sPowercenter[$sDeviceNo] != '')
                            {    
                                $aResult['response']  = 1;
                                $aResult['status']     = $sPowercenter[$sDeviceNo];
                            }
                            else
                                $aResult['msg']     = 'Device Number is not Valid';
                        }
                        else
                            $aResult['msg'] = "Power Center devices not available.";
                    } // END : If Device is Power Center.
                } // if($sDevice != '')  END : If device type is not empty and Valid Device number is there. 
                else
                    $aResult['msg'] = "Invalid Device Type or Device Number.";
                
                #OUTPUT
                echo json_encode($aResult);
            } // END : If Mode is Manual.
            
         } // END : function getDeviceNumberStatus()
         
         public function webAuthorisation($sUsername,$sPassword)
         {
             $aResponse             =   array();
             $aResponse['status']   =   "1";
             $aResponse['msg']      =   "";
             
             if( $sUsername != '' && $sPassword != '' )
             {
                 $aResponse['msg']      =   "Invalid Username or Password";
                 $aResponse['status']   =   "0";
             }
             if( $sUsername != 'foo' && $sPassword != 'bar' )
             {
                $aResponse['msg'] = "Invalid Username or Password";
                $aResponse['status']   =   "0";
             }
             
             return json_encode($aResponse);
             
         }
        
    } //END : Class Service
    
    /* End of file service.php */
    /* Location: ./application/controllers/service.php */
?>

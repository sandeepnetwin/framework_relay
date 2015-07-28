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
        protected $isHTTPSRequired          = FALSE; // Define whether an HTTPS connection is required
        protected $isAuthenticationRequired = FALSE; // Define whether user authentication is required
        
        // Define API response codes and their related HTTP response
        public $aApiResponseCode = array(
                                            0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
                                            1 => array('HTTP Response' => 200, 'Message' => 'Success'),
                                            2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
                                            3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
                                            4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
                                            5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
                                            6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
                                        );
        
        public function __construct() 
        {
            parent::__construct(); // Parent Contructor Call
            $this->load->helper('common_functions'); // Loaded helper : To get all functions accessible from common_functions file.
            
        } // END : function __construct()
        
        public function changeDeviceStatus()
        {
            // Set default HTTP response of 'ok'
            $aResponse              =   array();
            $aResponse['code']      =   0;
            $aResponse['status']    =   404;
            $aResponse['data']      =   NULL;
            $sformat                =   isset($_REQUEST['format']) ? $_REQUEST['format'] : '' ; // Get response Format (json,xml,html etc.)
            $sAuth                  =   isset($_REQUEST['auth']) ? $_REQUEST['auth'] : '' ;// Check if Authentication is required.
            $this->isAuthenticationRequired =   $sAuth;
                
            // Optionally require connections to be made via HTTPS
            if( $this->isHTTPSRequired && $_SERVER['HTTPS'] != 'on' )
            {
                $aResponse['code']      = 2;
                $aResponse['status']    = $aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = $aApiResponseCode[ $aResponse['code'] ]['Message'];

                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
            
            if($this->isAuthenticationRequired)
            {
                //START : Authorisation
                $sUsername       = isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ;   // Get the username of webservice 
                $sPassword       = isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ;   // Get the password of webservice 
                $this->webAuthorisation($sUsername, $sPassword,$sformat); // Check if username and password is valid.
                // END : Authorisation
            }
            
            #INPUTS
            $sDevice         = isset($_REQUEST['dvc']) ? $_REQUEST['dvc'] : '' ; // Get the Device(ie. R=Relay,V=Valve,PC=Power Center)
            $sDeviceNo       = isset($_REQUEST['dn'])  ? $_REQUEST['dn'] : '' ;  // Get the Device No.
            $iDeviceStatus   = isset($_REQUEST['ds']) ? $_REQUEST['ds'] : '' ;   // Get the status to which Device will be changed         
            
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']  = 0;
            $aDeviceStatus      = array('0', '1', '2'); //respective values of status.
                     
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
                                $aResponse['code']      = 5;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Invalid relay number.';

                                // Return Response to browser. This will exit the script.
                                $this->webResponse($sformat, $aResponse);
                            } // END : if( $sDeviceNo > ($iRelayCount-1) || $sDeviceNo < 0)
                            else
                            {
                                $sRelayNewResp = replace_return($sRelays, $iDeviceStatus, $sDeviceNo ); // Change the status with the sent status for the device no.
                                onoff_rlb_relay($sRelayNewResp); // Send the request to change the status on server.		
                                //$aResult['response'] = 1;
                                //$aResult['msg'] = "Relay status changed successfully.";
                                $aResponse['code']      = 1;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Relay status changed successfully.';

                                // Return Response to browser. This will exit the script.
                                $this->webResponse($sformat, $aResponse);
                            } // END : else of if( $sDeviceNo > ($iRelayCount-1) || $sDeviceNo < 0)
                        }
                        else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Relay devices not available.';

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        }
                    } // END : if($sDevice == 'R')
                                        
                    if($sDevice == 'PC') // START : If Device is Power Center.
                    {
                        if($sPowercenter != '') // START : Check if Power Center devices are available.
                        {
                            $iPowerCenterCount    = strlen($sPowercenter); // Count of Power Center Devices.
                            if( $sDeviceNo > ($iPowerCenterCount-1) || $sDeviceNo < 0)
                            {
                                $aResponse['code']      = 5;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Invalid Power Center number.';

                                // Return Response to browser. This will exit the script.
                                $this->webResponse($sformat, $aResponse);
                            } // END : if( $sDeviceNo > ($iPowerCenterCount-1) || $sDeviceNo < 0)
                            else
                            {
                                $sRelayNewResp = replace_return($sPowercenter, $iDeviceStatus, $sDeviceNo ); // Change the status with the sent status for the device no.
                                onoff_rlb_powercenter($sRelayNewResp); // Send the request to change the status on server.		
                                //$aResult['response'] = 1;
                                //$aResult['msg'] = "Power Center status changed successfully.";
                                $aResponse['code']      = 1;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Power Center status changed successfully.';

                                // Return Response to browser. This will exit the script.
                                $this->webResponse($sformat, $aResponse);
                            } // END : else of if( $sDeviceNo > ($iPowerCenterCount-1) || $sDeviceNo < 0)
                        }
                        else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Power Center devices not available.';

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        }
                    } // END : if($sDevice == 'PC')
                                        
                    if($sDevice == 'V') // START : If Device is Power Center.
                    {
                        if($sValves != '') // START : Check if Valve devices are available.
                        {
                            $iValveCount    = strlen($sValves); // Count of Power Center Devices.
                            if( $sDeviceNo > ($iValveCount-1) || $sDeviceNo < 0)
                            {
                                $aResponse['code']      = 5;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Invalid Valve number.';

                                // Return Response to browser. This will exit the script.
                                $this->webResponse($sformat, $aResponse);
                            } // END : if( $sDeviceNo > ($iValveCount-1) || $sDeviceNo < 0)
                            else
                            {
                                $sRelayNewResp = replace_return($sValves, $iDeviceStatus, $sDeviceNo ); // Change the status with the sent status for the device no.
                                onoff_rlb_valve($sRelayNewResp); // Send the request to change the status on server.		
                                //$aResult['response'] = 1;
                                //$aResult['msg'] = "Valve status changed successfully.";
                                $aResponse['code']      = 1;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Valve status changed successfully.';

                                // Return Response to browser. This will exit the script.
                                $this->webResponse($sformat, $aResponse);
                            } // END : else of if( $sDeviceNo > ($iValveCount-1) || $sDeviceNo < 0)
                        }
                        else
                        {
                            //$aResult['msg'] = "Valve devices not available."; 
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Valve devices not available.';

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        }
                    } // END : if($sDevice == 'V')
                } // END : if($sDeviceNo != '' && in_array($iDeviceStatus, $aDeviceStatus) && $sDevice != '')
                else
                {
                    $aResponse['code']      = 5;
                    $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                    $aResponse['data']      = 'Invalid Device number Or Device status OR Device Type.';

                    // Return Response to browser. This will exit the script.
                    $this->webResponse($sformat, $aResponse);
                }
            } // END : if($iActiveMode == 2)
            else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Invalid mode to perform this operation.';

                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
            //$aResult['msg'] = "Invalid mode to perform this operation.";
        } //END : function changeDeviceStatus()
        
         public function getDeviceStatus()
         {
            // Set default HTTP response of 'ok'
            $aResponse              =   array();
            $aResponse['code']      =   0;
            $aResponse['status']    =   404;
            $aResponse['data']      =   NULL;
            $sformat                =   isset($_REQUEST['format']) ? $_REQUEST['format'] : '' ; // Get response Format (json,xml,html etc.)
            $sAuth                  =   isset($_REQUEST['auth']) ? $_REQUEST['auth'] : '' ;// Check if Authentication is required.
            $this->isAuthenticationRequired =   $sAuth;
            
            // Optionally require connections to be made via HTTPS
            if( $this->isHTTPSRequired && $_SERVER['HTTPS'] != 'on' )
            {
                $aResponse['code']      = 2;
                $aResponse['status']    = $aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = $aApiResponseCode[ $aResponse['code'] ]['Message'];

                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
            
            if($this->isAuthenticationRequired)
            {
                //START : Authorisation
                $sUsername       = isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ;   // Get the username of webservice 
                $sPassword       = isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ;   // Get the password of webservice 
                $this->webAuthorisation($sUsername, $sPassword,$sformat); // Check if username and password is valid.
                // END : Authorisation
            }
             
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
                            
                            $aResponse['code']      = 1;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = $sValves;

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        } // END : Checked if Valve Devices are available
                        else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Valve devices not available.';

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        }
                    } // if($sDevice == "V") END : If Device is Valve
                    else if($sDevice == "R") // START : If Device is Relay.
                    {
                        if($sResponse['relay'] != '')
                        {
                            $sRelays        =   $sResponse['relay'];  // Relay Devices.
                            $iCntRelays     =   strlen($sRelays); // Count of Relay Devices.
                            
                            $aResponse['code']      = 1;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = $sRelays;
                            
                            $this->webResponse($sformat, $aResponse);
                        }
                        else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Relay devices not available.';

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        }
                    } // END : If Device is Relay.
                    else if($sDevice == "PC") // START : If Device is Power Center.
                    {
                        if($sResponse['powercenter'] != '')
                        {
                            $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
                            $iCntPowercenter=   strlen($sPowercenter); // Count of Power Center Devices.
                            
                            $aResponse['code']      = 1;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = $sPowercenter;
                            
                            $this->webResponse($sformat, $aResponse);
                        }
                        else
                        {    
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Power Center devices not available.';

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        }
                    } // END : If Device is Power Center.
                } // END : If device type is not empty. if($sDevice != '')
                else
                {
                    $aResponse['code']      = 5;
                    $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                    $aResponse['data']      = 'Invalid Device Type.';

                    // Return Response to browser. This will exit the script.
                    $this->webResponse($sformat, $aResponse);
                }
            } // END : If Mode is Manual.
            else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Invalid mode to perform this operation.';

                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
            
         } // END : getDeviceStatus()
         
         public function getDeviceNumberStatus()
         {
             // Set default HTTP response of 'ok'
            $aResponse              =   array();
            $aResponse['code']      =   0;
            $aResponse['status']    =   404;
            $aResponse['data']      =   NULL;
            $sformat                =   isset($_REQUEST['format']) ? $_REQUEST['format'] : '' ; // Get response Format (json,xml,html etc.)
            $sAuth                  =   isset($_REQUEST['auth']) ? $_REQUEST['auth'] : '' ;// Check if Authentication is required.
            $this->isAuthenticationRequired =   $sAuth;
            
            // Optionally require connections to be made via HTTPS
            if( $this->isHTTPSRequired && $_SERVER['HTTPS'] != 'on' )
            {
                $aResponse['code']      = 2;
                $aResponse['status']    = $aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = $aApiResponseCode[ $aResponse['code'] ]['Message'];

                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
            
            if($this->isAuthenticationRequired)
            {
                //START : Authorisation
                $sUsername       = isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ;   // Get the username of webservice 
                $sPassword       = isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ;   // Get the password of webservice 
                $this->webAuthorisation($sUsername, $sPassword,$sformat); // Check if username and password is valid.
                // END : Authorisation
            }
            
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
                                $aResponse['code']      = 1;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = $sValves[$sDeviceNo];
                                
                                $this->webResponse($sformat, $aResponse);
                            }
                            else
                            {
                                $aResponse['code']      = 5;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Device Number is not Valid';
                                
                                $this->webResponse($sformat, $aResponse);
                            }
                        } // END : Checked if Valve Devices are available
                        else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Valve devices not available.';

                            $this->webResponse($sformat, $aResponse);
                        }
                    } // if($sDevice == "V") END : If Device is Valve
                    else if($sDevice == "R") // START : If Device is Relay.
                    {
                        if($sResponse['relay'] != '')
                        {
                            $sRelays        =   $sResponse['relay'];  // Relay Devices.
                            if(isset($sRelays[$sDeviceNo]) && $sRelays[$sDeviceNo] != '')
                            {    
                                $aResponse['code']      = 1;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = $sRelays[$sDeviceNo];

                                $this->webResponse($sformat, $aResponse);
                            }
                            else
                            {
                                $aResponse['code']      = 5;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Device Number is not Valid';

                                $this->webResponse($sformat, $aResponse);
                            }
                        }
                        else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Relay devices not available.';

                            $this->webResponse($sformat, $aResponse);
                        }
                    } // END : If Device is Relay.
                    else if($sDevice == "PC") // START : If Device is Power Center.
                    {
                        if($sResponse['powercenter'] != '')
                        {
                            $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
                            if(isset($sPowercenter[$sDeviceNo]) && $sPowercenter[$sDeviceNo] != '')
                            {    
                                $aResponse['code']      = 1;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = $sPowercenter[$sDeviceNo];

                                $this->webResponse($sformat, $aResponse);
                            }
                            else
                            {
                                $aResponse['code']      = 5;
                                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                                $aResponse['data']      = 'Device Number is not Valid';

                                $this->webResponse($sformat, $aResponse);
                            }
                        }
                        else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Power Center devices not available.';

                            $this->webResponse($sformat, $aResponse);
                        }
                    } // END : If Device is Power Center.
                } // if($sDevice != '')  END : If device type is not empty and Valid Device number is there. 
                else
                {
                    $aResponse['code']      = 5;
                    $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                    $aResponse['data']      = 'Invalid Device Type or Device Number.';

                    $this->webResponse($sformat, $aResponse);
                    
                }
            } // END : If Mode is Manual.
            else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Invalid mode to perform this operation.';

                $this->webResponse($sformat, $aResponse);
            }
            
         } // END : function getDeviceNumberStatus()
         
         public function webAuthorisation($sUsername,$sPassword,$sFormat)
         {
             $aResponse             =   array();
             
             if( $sPassword == '' )
             {
                $aResponse['code'] = 3;
                $aResponse['status'] = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data'] = $this->aApiResponseCode[ $aResponse['code'] ]['Message'];

                // Return Response to browser
                $this->webResponse($sFormat, $aResponse);
             }
             if( $sPassword != 'bar' )
             {
                $aResponse['code'] = 4;
                $aResponse['status'] = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data'] = $this->aApiResponseCode[ $aResponse['code'] ]['Message'];

                // Return Response to browser
                $this->webResponse($sFormat, $aResponse);
             }
             
         }
         
         public function webResponse($sformat, $aApiResponse)
         {
           // Define HTTP responses
            $http_response_code = array(
                                        200 => 'OK',
                                        400 => 'Bad Request',
                                        401 => 'Unauthorized',
                                        403 => 'Forbidden',
                                        404 => 'Not Found'
                                       );

            // Set HTTP Response
            header('HTTP/1.1 '.$aApiResponse['status'].' '.$http_response_code[ $aApiResponse['status'] ]);

            // Process different content types
            if( strcasecmp($sformat,'json') == 0 )
            {
                // Set HTTP Response Content Type
                header('Content-Type: application/json; charset=utf-8');

                // Format data into a JSON response
                $json_response = json_encode($aApiResponse);

                // Deliver formatted data
                echo $json_response;

            }
            elseif( strcasecmp($sformat,'xml') == 0 )
            {
                // Set HTTP Response Content Type
                header('Content-Type: application/xml; charset=utf-8');

                // Format data into an XML response (This is only good at handling string data, not arrays)
                $xml_response = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
                    '<response>'."\n".
                    "\t".'<code>'.$aApiResponse['code'].'</code>'."\n".
                    "\t".'<data>'.$aApiResponse['data'].'</data>'."\n".
                    '</response>';

                // Deliver formatted data
                echo $xml_response;
            }
            else
            {
                // Set HTTP Response Content Type (This is only good at handling string data, not arrays)
                header('Content-Type: text/html; charset=utf-8');

                // Deliver formatted data
                echo $aApiResponse['data'];

            }

            // End script process
            exit;
        }
        
    } //END : Class Service
    
    /* End of file web.php */
    /* Location: ./application/controllers/web.php */
?>

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
                        
            //if($iActiveMode == 2) // START : If Mode is Manual.
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
					
					if($sDevice == 'PS')
					{
						$sPump	=	'';
						if(isset($sResponse['pump_seq_'.$sDeviceNo.'_st']))
							$sPump = $sResponse['pump_seq_'.$sDeviceNo.'_st'];
						
						if($sPump != '')
						{		
							$aPumpDetails = $this->home_model->getPumpDetails($sDeviceNo);
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
							else
							{
								$aResponse['code']      = 5;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = 'Pump devices not configured properly.';

								// Return Response to browser. This will exit the script.
								$this->webResponse($sformat, $aResponse);
							}
							
							if($sPumpType != '')
							{
								if($sPumpType == '12' || $sPumpType == '24')
								{
									if($sPumpType == '24')
									{
										$sNewResp = replace_return($sRelays, $iDeviceStatus, $sRelayNumber );
										onoff_rlb_relay($sNewResp);
										$this->home_model->updateDeviceRunTime($sDeviceNo,$sDevice,$iDeviceStatus);
									}
									else if($sPumpType == '12')
									{
										$sNewResp = replace_return($sPowercenter, $iDeviceStatus, $sRelayNumber );
										onoff_rlb_powercenter($sNewResp);
									}
									
									$aResponse['code']      = 1;
									$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
									$aResponse['data']      = 'Pump status changed successfully.';
								}
								else
								{
									if(preg_match('/Emulator/',$sPumpType))
									{
										$sNewResp = '';

										if($iDeviceStatus == '0')
											$sNewResp =  $sDeviceNo.' '.$iDeviceStatus;
										else if($iDeviceStatus == '1')
										{
											$sType          =   '';
											if($sPumpSubType == 'VS')
												$sType  =   '2'.' '.$sPumpSpeed;
											elseif ($sPumpSubType == 'VF')
												$sType  =   '3'.' '.$sPumpFlow;

											$sNewResp =  $sDeviceNo.' '.$sType;    
										}
										
										onoff_rlb_pump($sNewResp);
										
										if($sPumpType == 'Emulator12')
										{
											$sNewResp12 = replace_return($sPowercenter, $iDeviceStatus, $sRelayNumber );
											onoff_rlb_powercenter($sNewResp12);
										}
										if($sPumpType == 'Emulator24')
										{
											$sNewResp24 = replace_return($sRelays, $iDeviceStatus, $sRelayNumber );
											onoff_rlb_relay($sNewResp24);
											$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$iDeviceStatus);
										}
										$aResponse['code']      = 1;
										$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
										$aResponse['data']      = 'Pump status changed successfully.';
									}
									else if(preg_match('/Intellicom/',$sPumpType))
									{
										$sNewResp = '';

										if($iDeviceStatus == '0')
											$sNewResp =  $sDeviceNo.' '.$iDeviceStatus;
										else if($iDeviceStatus == '1')
										{
											$sType  =   '2'.' '.$sPumpSpeed;
											$sNewResp =  $sDeviceNo.' '.$sType;    
										}
										
										onoff_rlb_pump($sNewResp);
										
										if($sPumpType == 'Intellicom12')
										{
											$sNewResp12 = replace_return($sPowercenter, $iDeviceStatus, $sRelayNumber );
											onoff_rlb_powercenter($sNewResp12);
										}
										if($sPumpType == 'Intellicom24')
										{
											$sNewResp24 = replace_return($sRelays, $iDeviceStatus, $sRelayNumber );
											onoff_rlb_relay($sNewResp24);
											$this->home_model->updateDeviceRunTime($sRelayNumber,'R',$iDeviceStatus);
										}
										$aResponse['code']      = 1;
										$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
										$aResponse['data']      = 'Pump status changed successfully.';
									}
								}
							}
							else
							{
								$aResponse['code']      = 5;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = 'Pump devices not configured properly.';

								// Return Response to browser. This will exit the script.
								$this->webResponse($sformat, $aResponse);
							}
							
						}
						else
                        {
                            $aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Pump devices not available.';

                            // Return Response to browser. This will exit the script.
                            $this->webResponse($sformat, $aResponse);
                        }
					}						
					
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
            /*else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Invalid mode to perform this operation.';

                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }*/
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
            
            //if($iActiveMode == 2) // START : If Mode is Manual.
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
					else if($sDevice == "PS")// START : If Device is PUMPS.
					{
						$sPumps	= 	array();
						$sPumps[0] = $sResponse['pump_seq_0_st'];
						$sPumps[1] = $sResponse['pump_seq_1_st'];
						$sPumps[2] = $sResponse['pump_seq_2_st'];
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sPumps);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is PUMPS.
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
            /*else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Invalid mode to perform this operation.';

                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }*/
            
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
            
            //if($iActiveMode == 2) // START : If Mode is Manual.
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
					else if($sDevice == "PS")// START : If Device is PUMPS.
					{
						$sPumps = '';
						if(isset($sResponse['pump_seq_'.$sDeviceNo.'_st']))
							$sPumps	= 	$sResponse['pump_seq_'.$sDeviceNo.'_st'];
						
						if($sPumps != '')
						{
							$aResponse['code']      = 1;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = $sPumps;
							
							$this->webResponse($sformat, $aResponse);
						}
						else
						{
							$aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Pump devices not available.';

                            $this->webResponse($sformat, $aResponse);
						}
					}// END : If Device is PUMPS.
					
                } // if($sDevice != '')  END : If device type is not empty and Valid Device number is there. 
                else
                {
                    $aResponse['code']      = 5;
                    $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                    $aResponse['data']      = 'Invalid Device Type or Device Number.';

                    $this->webResponse($sformat, $aResponse);
                    
                }
            } // END : If Mode is Manual.
            /*else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Invalid mode to perform this operation.';

                $this->webResponse($sformat, $aResponse);
            }*/
            
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
         
        public function getActiveMode() //START : Function to get the active mode for web service.
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
             
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']= 0;
            $aResult['status']  = 0;
            
            
            $this->load->model('home_model');
            //Get the mode from Database.
            $iActiveMode =  $this->home_model->getActiveMode();
            
            if($iActiveMode != '')
            {
                $aResponse['code']      = 1;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = $iActiveMode;
                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
            else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Invalid mode.';
                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
         } //END : Function to get the active mode for web service.
		 
		 public function changeActiveMode() //START : Function to change the active mode for web service.
         {
            // Set default HTTP response of 'ok'
            $aResponse              =   array();
            $aResponse['code']      =   0;
            $aResponse['status']    =   404;
            $aResponse['data']      =   NULL;
            $sformat                =   isset($_REQUEST['format']) ? $_REQUEST['format'] : '' ; // Get response Format (json,xml,html etc.)
            $sAuth                  =   isset($_REQUEST['auth']) ? $_REQUEST['auth'] : '' ;// Check if Authentication is required.
            $this->isAuthenticationRequired =   $sAuth;
			
			//INPUTS
			$iMode	=	trim($_REQUEST['md']);
            
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
             
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']= 0;
            $aResult['status']  = 0;
            
            
            $this->load->model('home_model');
            //Get the mode from Database.
            $iActiveMode =  $this->home_model->getActiveMode();
            
            if($iMode != '')
            {
				if($iMode != $iActiveMode)
				{
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
							//onoff_rlb_relay($sRelayNewResp);
						}
						
						//off all valves
						if($sValves != '')
						{
							$sValveNewResp = str_replace(array('1','2'), '0', $sValves);
							//onoff_rlb_valve($sValveNewResp);  
						}
						
						//off all power center
						if($sPowercenter != '')
						{
							$sPowerNewResp = str_replace('1','0',$sPowercenter);  
							//onoff_rlb_powercenter($sPowerNewResp); 
						}

					}
					
					$aResponse['code']      = 1;
					$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
					$aResponse['data']      = $iMode;
					// Return Response to browser. This will exit the script.
					$this->webResponse($sformat, $aResponse);
				}
				else
				{
					$aResponse['code']      = 1;
					$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
					$aResponse['data']      = 'Mode already activated!';
					// Return Response to browser. This will exit the script.
					$this->webResponse($sformat, $aResponse);
				}
            }
            else
            {
                $aResponse['code']      = 5;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Please enter Valid mode.';
                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
            }
         } //END : Function to get the active mode for web service.
         
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

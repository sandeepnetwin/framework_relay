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
			
			//Get the number of Devices(Blower,Light and Heater) from the settings.
			list($sIP,$sPort,$sExtra) = $this->home_model->getSettings();
                        
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
							
							if($sPumpType != '' && $sPumpClosure == '1')
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
								$aResponse['data']      = 'Pump devices not configured properly or Closure is set to 0.';

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
					else if($sDevice == 'B') // START : If Device is Blower.
					{
						//Number of blower set on the setting Page.
						$iNumBlower	=	$sExtra['BlowerNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumBlower == 0 || $iNumBlower == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Blower Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($sDeviceNo);
						if(!empty($aBlowerDetails))
						{
							foreach($aBlowerDetails as $aBlower)
							{
								$sBlowerStatus	=	'';
								$sRelayDetails  =   unserialize($aBlower->light_relay_number);
								
								//Blower Operated Type and Relay
								$sRelayType     =   $sRelayDetails['sRelayType'];
								$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
								
								if($sRelayType == '24')
								{
									$sNewResp = replace_return($sRelays, $iDeviceStatus, $sRelayNumber );
									onoff_rlb_relay($sNewResp);
									$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$iDeviceStatus);
								}
								if($sRelayType == '12')
								{
									$sNewResp = replace_return($sPowercenter, $iDeviceStatus, $sRelayNumber );
									onoff_rlb_powercenter($sNewResp);
								}
								
								$aResponse['code']      = 1;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = 'Blower status changed successfully.';
								
								$this->webResponse($sformat, $aResponse);
							}
						}
						else
						{
							$aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Blower devices not available.';

                            $this->webResponse($sformat, $aResponse);
						}
					}// END : If Device is Blower.
					else if($sDevice == 'L') // START : If Device is Light.
					{
						//Number of blower set on the setting Page.
						$iNumLight	=	$sExtra['LightNumber'];
						
						//If Light is not set or count is 0.
						if($iNumLight == 0 || $iNumLight == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Light Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Light is not set or count is 0.
						
						$aLightDetails  =   $this->home_model->getLightDeviceDetails($sDeviceNo);
						
						if(!empty($aLightDetails))
						{
							foreach($aLightDetails as $aLight)
							{
								$sLightStatus	=	'';
								$sRelayDetails  =   unserialize($aLight->light_relay_number);
								
								//Light Operated Type and Relay
								$sRelayType     =   $sRelayDetails['sRelayType'];
								$sRelayNumber   =   $sRelayDetails['sRelayNumber'];
								
								if($sRelayType == '24')
								{
									$sNewResp = replace_return($sRelays, $iDeviceStatus, $sRelayNumber );
									onoff_rlb_relay($sNewResp);
									$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$iDeviceStatus);
								}
								if($sRelayType == '12')
								{
									$sNewResp = replace_return($sPowercenter, $iDeviceStatus, $sRelayNumber );
									onoff_rlb_powercenter($sNewResp);
								}
								
								$aResponse['code']      = 1;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = 'Light status changed successfully.';
								
								$this->webResponse($sformat, $aResponse);
								
							}
						}
						else
						{
							$aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Light devices not available.';

                            $this->webResponse($sformat, $aResponse);
						}
					}// END : If Device is Light.
					else if($sDevice == 'H') // START : If Device is Heater.
					{
						//Number of Heater set on the setting Page.
						$iNumHeater	=	$sExtra['HeaterNumber'];
						
						//If Heater is not set or count is 0.
						if($iNumHeater == 0 || $iNumHeater == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Heater Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Heater is not set or count is 0.
						
						//Heater Details
						$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($sDeviceNo);
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
									$sNewResp = replace_return($sRelays, $iDeviceStatus, $sRelayNumber );
									onoff_rlb_relay($sNewResp);
									$this->home_model->updateDeviceRunTime($sRelayNumber,$sDevice,$iDeviceStatus);
								}
								if($sRelayType == '12')
								{
									$sNewResp = replace_return($sPowercenter, $iDeviceStatus, $sRelayNumber );
									onoff_rlb_powercenter($sNewResp);
								}
								$aResponse['code']      = 1;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = 'Heater status changed successfully.';
								
								$this->webResponse($sformat, $aResponse);
							}
						}
						
					}// END : If Device is Heater.
					
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
			
			//Get the number of Devices(Blower,Light and Heater) from the settings.
			list($sIP,$sPort,$sExtra) = $this->home_model->getSettings();
            
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
						$sPumps 		= '';
						for($i=0;$i<3;$i++)
						{
							$aPumpDetails = $this->home_model->getPumpDetails($i);
							//Variable Initialization to blank.
							$sPumpNumber  	= '';
							$sPumpType  	= '';
							$relay_number   = '';
							
							if(is_array($aPumpDetails) && !empty($aPumpDetails))
							{
							  foreach($aPumpDetails as $aResultEdit)
							  { 
								$sPumpNumber  = $aResultEdit->pump_number;
								$sPumpType    = $aResultEdit->pump_type;
								$relay_number = $aResultEdit->relay_number;
								$sPumpClosure = $aResultEdit->pump_closure;
								
								if($sPumpType != '' && $sPumpClosure == '1')
								{
									if($sPumpType == '12' || $sPumpType == '24')
									{
										if($sPumpType == '24')
										{
											if($sResponse['relay'] != '')
											{
												$sRelays        =   $sResponse['relay'];  // Relay Devices.
												if(isset($sRelays[$relay_number]) && $sRelays[$relay_number] != '')
												{    
													$sPumps      .= $sRelays[$relay_number];
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
												$aResponse['data']      = 'Devices not available.';

												$this->webResponse($sformat, $aResponse);
											}
										}
										else if($sPumpType == '12')
										{
											
											if($sResponse['powercenter'] != '')
											{
												$sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
												if(isset($sPowercenter[$relay_number]) && $sPowercenter[$relay_number] != '')
												{    
													$sPumps      .= $sPowercenter[$relay_number];
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
												$aResponse['data']      = 'Device not available.';

												$this->webResponse($sformat, $aResponse);
											}
										}
									}
									else
									{
										if(preg_match('/Emulator/',$sPumpType) || preg_match('/Intellicom/',$sPumpType))
										{
											if($sResponse['pump_seq_'.$sPumpNumber.'_st'] > 0)
												$sPumps .= 1;
											else 
												$sPumps .= 0;
										}
									}
								}
								else
								{
									$aResponse['code']      = 5;
									$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
									$aResponse['data']      = 'Pump devices not configured properly or Closure is set to 0.';

									// Return Response to browser. This will exit the script.
									$this->webResponse($sformat, $aResponse);
								}
							  }
							}
							else
							{
								if(isset($sResponse['pump_seq_'.$i.'_st']))
								{
									$sPumps .= '.';
								}
							}
						}
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = $sPumps;
					
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is PUMPS.
					else if($sDevice == "T")// START : If Device is Temperature Sensor.
					{
						$sTemprature	= 	array();
						/* $sTemprature[0] = $sResponse['TS0'];
						$sTemprature[1] = $sResponse['TS1'];
						$sTemprature[2] = $sResponse['TS2'];
						$sTemprature[3] = $sResponse['TS3'];
						$sTemprature[4] = $sResponse['TS4'];
						$sTemprature[5] = $sResponse['TS5']; */
						
						for($i=0; $i<=5; $i++)
						{
							$sTemprature[$i]['temp'] = $sResponse['TS'.$i];	
							$sTemprature[$i]['name'] = $this->home_model->getDeviceName($i,'T');;	
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sTemprature);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Temperature Sensor.
					else if($sDevice == "B")// START : If Device is Blower.
					{
						$sBlower	= 	array();
						
						//Number of blower set on the setting Page.
						$iNumBlower	=	$sExtra['BlowerNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumBlower == 0 || $iNumBlower == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Blower Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						for($i=0; $i<$iNumBlower; $i++)		
						{
							//Blower Details
							$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($i);
							if(!empty($aBlowerDetails))
							{
								$sRelays        =   $sResponse['relay'];
								$sPowercenter   =   $sResponse['powercenter'];
								
								foreach($aBlowerDetails as $aBlower)
								{
									$sBlowerStatus	=	'';
									$sRelayDetails  =   unserialize($aBlower->light_relay_number);
									
									//Blower Operated Type and Relay
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
								
									$sBlower[$i] = $sBlowerStatus;
								}
							}
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sBlower);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Blower.
					else if($sDevice == "L")// START : If Device is Light.
					{
						$sLight	= 	array();
						
						//Number of blower set on the setting Page.
						$iNumLight	=	$sExtra['LightNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumLight == 0 || $iNumLight == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Light Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						for($i=0; $i<$iNumLight; $i++)		
						{
							//Light Details
							$aLightDetails  =   $this->home_model->getLightDeviceDetails($i);
							if(!empty($aLightDetails))
							{
								$sRelays        =   $sResponse['relay'];
								$sPowercenter   =   $sResponse['powercenter'];
								
								foreach($aLightDetails as $aLight)
								{
									$sLightStatus	=	'';
									$sRelayDetails  =   unserialize($aLight->light_relay_number);
									
									//Light Operated Type and Relay
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
								
									$sLight[$i] = $sLightStatus;
								}
							}
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sLight);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Heater.
					else if($sDevice == "H")// START : If Device is Heater.
					{
						$sHeater	= 	array();
						
						//Number of blower set on the setting Page.
						$iNumHeater	=	$sExtra['HeaterNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumHeater == 0 || $iNumHeater == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Heater Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						for($i=0; $i<$iNumHeater; $i++)		
						{
							//Heater Details
							$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($i);
							if(!empty($aHeaterDetails))
							{
								$sRelays        =   $sResponse['relay'];
								$sPowercenter   =   $sResponse['powercenter'];
								
								foreach($aHeaterDetails as $aHeater)
								{
									$sHeaterStatus	=	'';
									$sRelayDetails  =   unserialize($aHeater->light_relay_number);
									
									//Heater Operated Type and Relay
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
								
									$sHeater[$i] = $sHeaterStatus;
								}
							}
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sHeater);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Heater.
					
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
			
			//Get the number of Devices(Blower,Light and Heater) from the settings.
			list($sIP,$sPort,$sExtra) = $this->home_model->getSettings();
            
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
						$aPumpDetails = $this->home_model->getPumpDetails($sDeviceNo);
						//Variable Initialization to blank.
						$sPumpNumber  	= '';
						$sPumpType  	= '';
						$relay_number   = '';
						
						if(is_array($aPumpDetails) && !empty($aPumpDetails))
						{
						  foreach($aPumpDetails as $aResultEdit)
						  { 
							$sPumpNumber  = $aResultEdit->pump_number;
							$sPumpType    = $aResultEdit->pump_type;
							$relay_number = $aResultEdit->relay_number;
							$sPumpClosure = $aResultEdit->pump_closure;
							
							if($sPumpType != '' && $sPumpClosure == '1')
							{
								if($sPumpType == '12' || $sPumpType == '24')
								{
									if($sPumpType == '24')
									{
										if($sResponse['relay'] != '')
										{
											$sRelays        =   $sResponse['relay'];  // Relay Devices.
											if(isset($sRelays[$relay_number]) && $sRelays[$relay_number] != '')
											{    
												$sPumps      = $sRelays[$relay_number];
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
											$aResponse['data']      = 'Devices not available.';

											$this->webResponse($sformat, $aResponse);
										}
									}
									else if($sPumpType == '12')
									{
										
										if($sResponse['powercenter'] != '')
										{
											$sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
											if(isset($sPowercenter[$relay_number]) && $sPowercenter[$relay_number] != '')
											{    
												$sPumps      = $sPowercenter[$relay_number];
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
											$aResponse['data']      = 'Device not available.';

											$this->webResponse($sformat, $aResponse);
										}
									}
								}
								else
								{
									$sResponse['pump_seq_'.$sPumpNumber.'_st'];
									if($sResponse['pump_seq_'.$sPumpNumber.'_st'] > 0)
										$sPumps = 1;
									else if($sResponse['pump_seq_'.$sPumpNumber.'_st'] == 0)
										$sPumps = 0;
									
								}
							}
							else
							{
								$aResponse['code']      = 5;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = 'Pump devices not configured properly or Closure is set to 0.';

								// Return Response to browser. This will exit the script.
								$this->webResponse($sformat, $aResponse);
							}
						  }
						}
						else
						{
							if(isset($sResponse['pump_seq_'.$sDeviceNo.'_st']))
							{
								$sPumps = '.';
							}
						}
						
						if($sPumps != '' || $sPumps == 0)
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
					else if($sDevice == "T")// START : If Device is Temperature.
					{
						$sTemprature = '';
						if(isset($sResponse['TS'.$sDeviceNo]))
						{
							$sTemprature	= 	$sResponse['TS'.$sDeviceNo];
						}
						
						if($sTemprature != '')
						{
							$aResponse['code']      = 1;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = $sTemprature;
							
							$this->webResponse($sformat, $aResponse);
						}
						else
						{
							$aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Temperature devices not available.';

                            $this->webResponse($sformat, $aResponse);
						}
					}// END : If Device is Temperature.
					else if($sDevice == 'B') // START : If Device is Blower.
					{
						//Number of blower set on the setting Page.
						$iNumBlower	=	$sExtra['BlowerNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumBlower == 0 || $iNumBlower == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Blower Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						//Blower Details
						$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($sDeviceNo);
						if(!empty($aBlowerDetails))
						{
							$sRelays        =   $sResponse['relay']; // 24V AC Relays
                            $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
							foreach($aBlowerDetails as $aBlower)
							{
								$sBlowerStatus	=	'';
								$sRelayDetails  =   unserialize($aBlower->light_relay_number);
								
								//Blower Operated Type and Relay
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
							
								$aResponse['code']      = 1;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = $sBlowerStatus;
								
								$this->webResponse($sformat, $aResponse);
							}
						}
						else
						{
							$aResponse['code']      = 5;
                            $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                            $aResponse['data']      = 'Blower devices not available.';

                            $this->webResponse($sformat, $aResponse);
						}
					}// END : If Device is Blower.
					else if($sDevice == 'L') // START : If Device is Light.
					{
						//Number of Light set on the setting Page.
						$iNumLight	=	$sExtra['LightNumber'];
						
						//If Light is not set or count is 0.
						if($iNumLight == 0 || $iNumLight == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Light Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Light is not set or count is 0.
												
						//Light Details
						$aLightDetails  =   $this->home_model->getLightDeviceDetails($sDeviceNo);
						if(!empty($aLightDetails))
						{
							$sRelays        =   $sResponse['relay']; // 24V AC Relays
                            $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
							foreach($aLightDetails as $aLight)
							{
								$sLightStatus	=	'';
								$sRelayDetails  =   unserialize($aLight->light_relay_number);
								
								//Light Operated Type and Relay
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
							
								$aResponse['code']      = 1;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = $sLightStatus;
								
								$this->webResponse($sformat, $aResponse);
							}
						}
						else
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Light Device is not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}
					}// END : If Device is Light
					else if($sDevice == 'H')// START : If Device is Heater
					{
						//Number of Heater set on the setting Page.
						$iNumHeater	=	$sExtra['HeaterNumber'];
						
						//If Heater is not set or count is 0.
						if($iNumHeater == 0 || $iNumHeater == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Heater Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Heater is not set or count is 0.
						
						//Heater Details
						$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($sDeviceNo);
						if(!empty($aHeaterDetails))
						{
							$sRelays        =   $sResponse['relay']; // 24V AC Relays
                            $sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
							foreach($aHeaterDetails as $aHeater)
							{
								$sHeaterStatus	=	'';
								$sRelayDetails  =   unserialize($aHeater->light_relay_number);
								
								//Heater Operated Type and Relay
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
							
								$aResponse['code']      = 1;
								$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
								$aResponse['data']      = $sHeaterStatus;
								
								$this->webResponse($sformat, $aResponse);
							}
						}
						else
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Heater Device is not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}
					}// END : If Device is Heater
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
			$iTime	=	trim($_REQUEST['mt']); //Manual Time in Minute
            
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

					//If mode is manual then add the manual start time and end time calculated using the manual time(in minute) added on settings page.
					if($iMode == 2)
					{
						if($iTime == '')
							$sManualTime	=	$this->home_model->getManualModeTime();
					    else
							$sManualTime	=	$iTime;
						
						if($sManualTime != '')
						{
							if($iMode == 2)
							{
								$sProgramAbsStart =   date("H:i:s", time());
								$aStartTime       =   explode(":",$sProgramAbsStart);
								$sProgramAbsEnd   =   mktime(($aStartTime[0]),($aStartTime[1]+$sManualTime),($aStartTime[2]),0,0,0);
								$sAbsoluteEnd     =   date("H:i:s", $sProgramAbsEnd);
								$this->home_model->updateManualModeTimer($sProgramAbsStart,$sAbsoluteEnd);
								
								//Save Manual Mode Time.
								$this->home_model->updateManualModeTime($sManualTime);
							}
							else
							{
								$this->home_model->updateManualModeTimer('','');
							}	
						}
					}
						
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
					
					$aResponse['code']      = 1;
					$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
					$aResponse['data']      = $iMode;
					// Return Response to browser. This will exit the script.
					$this->webResponse($sformat, $aResponse);
				}
				else
				{
					//If mode is manual then add the manual start time and end time calculated using the manual time(in minute) added on settings page.
					if($iMode == 2)
					{
						$sManualTime	=	$this->home_model->getManualModeTime();
						
						if($iTime != '')
						{
							if($sManualTime != $iTime)
							{
								$sProgramAbsStart =   date("H:i:s", time());
								$aStartTime       =   explode(":",$sProgramAbsStart);
								$sProgramAbsEnd   =   mktime(($aStartTime[0]),($aStartTime[1]+$iTime),($aStartTime[2]),0,0,0);
								$sAbsoluteEnd     =   date("H:i:s", $sProgramAbsEnd);
								$this->home_model->updateManualModeTimer($sProgramAbsStart,$sAbsoluteEnd);
								
								//Update the new time in database.
								$this->home_model->updateManualModeTime($iTime);
								
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
         
         
		
		function saveProgramWeb()
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
			
			$sProgramName 		= trim($_REQUEST['sProgramName']);
			$sRelayNumber 		= trim($_REQUEST['sRelayNumber']);
			$sProgramType 		= trim($_REQUEST['sProgramType']);
			if($sProgramType == 2)
			$sProgramDays 		= explode(",",trim($_REQUEST['sProgramDays']));
			else
			$sProgramDays 		= 0;	
		
			$sStartTime 		= trim($_REQUEST['sStartTime']);
			$sEndTime		 	= trim($_REQUEST['sEndTime']);
			$isAbsoluteProgram 	= trim($_REQUEST['isAbsoluteProgram']);
			$sType				= trim($_REQUEST['type']);
			
			
			$sErrorMsg			=	'';
			
			if($sProgramName == '')
			{
				$sErrorMsg			.=	'Please enter Program Name.<br />';
			}
			if($sRelayNumber == '')
			{
				$sErrorMsg			.=	'Please enter Device Number.<br />';
			}
			if($sProgramType == '')
			{
				$sErrorMsg			.=	'Please select Program Type.<br />';
			}
			if($sProgramType == '2')
			{
				if(empty($sProgramDays))
				{
					$sErrorMsg			.=	'Please select Program Days.<br />';
				}
			}
			if($sStartTime == '')
			{
				$sErrorMsg			.=	'Please select Program Start Time.<br />';
			}
			if($sEndTime == '')
			{
				$sErrorMsg			.=	'Please select Program End Time.<br />';
			}
			if($isAbsoluteProgram == '')
			{
				$sErrorMsg			.=	'Please select Program Absolute.<br />';
			}
			if($sType == '')
			{
				$sErrorMsg			.=	'Please enter Program Device Type.<br />';
			}
			
			if($sErrorMsg != '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = $sErrorMsg;

				$this->webResponse($sformat, $aResponse);
				exit;
			}
			
			$startTime		=  $sStartTime.':00';	
			$endTime		=  $sEndTime.':00';		
			
			
			//START : CHECK if program time is already assined to another program.
				$sProgramDetails	=	$this->home_model->getProgramDetailsForDevice($sRelayNumber,$sType);
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
							if(!empty($sProgramDays))
							{
								foreach($sProgramDays as $days)
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
			//END : CHECK if program time is already assined to another program.
			if($alreadyExists == '1')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Selected Time is already assigned to other program!';

				$this->webResponse($sformat, $aResponse);
			}
			else
			{
				$aPost				= array('sProgramName'=>$sProgramName,
											'sProgramType'=>$sProgramType,
											'sProgramDays'=>$sProgramDays,
											'sStartTime'=>$sStartTime,
											'sEndTime'=>$sEndTime,
											'isAbsoluteProgram'=>$isAbsoluteProgram);
				
				$id =	$this->home_model->saveProgramDetails($aPost,$sRelayNumber,$sType);
				
				$aResponse['code']      = 1;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = $id;
                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
			}
            
		}
		
		function getAllProgramDetails()
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
            
			$sRelayNumber 		= trim($_REQUEST['sRelayNumber']);
			$sType				= trim($_REQUEST['type']);
			
			if($this->isAuthenticationRequired)
            {
                //START : Authorisation
                $sUsername       = isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ;   // Get the username of webservice 
                $sPassword       = isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ;   // Get the password of webservice 
                $this->webAuthorisation($sUsername, $sPassword,$sformat); // Check if username and password is valid.
                // END : Authorisation
            }
			
			if($sRelayNumber == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter valid Device number!';

				$this->webResponse($sformat, $aResponse);
			}
			if($sType == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter valid Program type!';

				$this->webResponse($sformat, $aResponse);
			}
             
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']= 0;
            $aResult['status']  = 0;
                       
            $this->load->model('home_model');
			
			$sDetails = $this->home_model->getProgramDetailsForDevice($sRelayNumber,$sType);
			
			if(!empty($sDetails))
			{
				$aResponse['code']      = 1;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = json_encode($sDetails);
				// Return Response to browser. This will exit the script.
				$this->webResponse($sformat, $aResponse);		
			}
			else
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'No Program Available!';

				$this->webResponse($sformat, $aResponse);
			}
			
		}
		
		
		function updateProgramDetails()
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
			
			$sProgramID 		= trim($_REQUEST['sProgramID']);
			$sProgramName 		= trim($_REQUEST['sProgramName']);
			$sRelayNumber 		= trim($_REQUEST['sRelayNumber']);
			$sProgramType 		= trim($_REQUEST['sProgramType']);
			if($sProgramType == 2)
			$sProgramDays 		= explode(",",trim($_REQUEST['sProgramDays']));
			else
			$sProgramDays 		= 0;	
		
			$sStartTime 		= trim($_REQUEST['sStartTime']);
			$sEndTime		 	= trim($_REQUEST['sEndTime']);
			$isAbsoluteProgram 	= trim($_REQUEST['isAbsoluteProgram']);
			$sType				= trim($_REQUEST['type']);
						
			$sErrorMsg			=	'';
			
			if($sProgramID == '')
			{
				$sErrorMsg			.=	'Please enter Program ID.<br />';
			}
			if($sProgramName == '')
			{
				$sErrorMsg			.=	'Please enter Program Name.<br />';
			}
			if($sRelayNumber == '')
			{
				$sErrorMsg			.=	'Please enter Device Number.<br />';
			}
			if($sProgramType == '')
			{
				$sErrorMsg			.=	'Please select Program Type.<br />';
			}
			if($sProgramType == '2')
			{
				if(empty($sProgramDays))
				{
					$sErrorMsg			.=	'Please select Program Days.<br />';
				}
			}
			if($sStartTime == '')
			{
				$sErrorMsg			.=	'Please select Program Start Time.<br />';
			}
			if($sEndTime == '')
			{
				$sErrorMsg			.=	'Please select Program End Time.<br />';
			}
			if($isAbsoluteProgram == '')
			{
				$sErrorMsg			.=	'Please select Program Absolute.<br />';
			}
			if($sType == '')
			{
				$sErrorMsg			.=	'Please enter Program Device Type.<br />';
			}
			
			if($sErrorMsg != '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = $sErrorMsg;

				$this->webResponse($sformat, $aResponse);
				exit;
			}
			
			$startTime		=  $sStartTime.':00';	
			$endTime		=  $sEndTime.':00';
			
			//START : CHECK if program time is already assined to another program.
				$sProgramDetails	=	$this->home_model->getProgramDetailsForDevice($sRelayNumber,$sType);
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
							if(!empty($sProgramDays))
							{
								foreach($sProgramDays as $days)
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
			//END : CHECK if program time is already assined to another program.
			if($alreadyExists == '1')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Selected Time is already assigned to other program!';

				$this->webResponse($sformat, $aResponse);
			}
			else
			{
				$aPost				= array('sProgramName'=>$sProgramName,
											'sProgramType'=>$sProgramType,
											'sProgramDays'=>$sProgramDays,
											'sStartTime'=>$sStartTime,
											'sEndTime'=>$sEndTime,
											'isAbsoluteProgram'=>$isAbsoluteProgram);
				
				$this->home_model->updateProgramDetails($aPost,$sProgramID,$sRelayNumber,$sType);
				
				$aResponse['code']      = 1;
                $aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
                $aResponse['data']      = 'Program Details Updated Succesfully!';
                // Return Response to browser. This will exit the script.
                $this->webResponse($sformat, $aResponse);
			}
			
			
			
			
                       
		}
		
		function deleteProgram()
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
            
			$sProgramID 		= trim($_REQUEST['sProgramID']);
			
			if($this->isAuthenticationRequired)
            {
                //START : Authorisation
                $sUsername       = isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ;   // Get the username of webservice 
                $sPassword       = isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ;   // Get the password of webservice 
                $this->webAuthorisation($sUsername, $sPassword,$sformat); // Check if username and password is valid.
                // END : Authorisation
            }
			
			if($sProgramID == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter valid Program ID!';

				$this->webResponse($sformat, $aResponse);
			}
		 
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']= 0;
            $aResult['status']  = 0;
                       
            $this->load->model('home_model');
			
			$sDetails = $this->home_model->deleteProgramDetails($sProgramID);
			
			$aResponse['code']      = 1;
			$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
			$aResponse['data']      = "Program Deleted Succesfully!";
			// Return Response to browser. This will exit the script.
			$this->webResponse($sformat, $aResponse);		
			
		}
		
		
		function getParticularProgramDetails()
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
            
			$sProgramID 		= trim($_REQUEST['sProgramID']);
			
			if($this->isAuthenticationRequired)
            {
                //START : Authorisation
                $sUsername       = isset($_REQUEST['username']) ? $_REQUEST['username'] : '' ;   // Get the username of webservice 
                $sPassword       = isset($_REQUEST['password']) ? $_REQUEST['password'] : '' ;   // Get the password of webservice 
                $this->webAuthorisation($sUsername, $sPassword,$sformat); // Check if username and password is valid.
                // END : Authorisation
            }
			
			if($sProgramID == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter valid Program ID!';

				$this->webResponse($sformat, $aResponse);
			}
		 
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']= 0;
            $aResult['status']  = 0;
                       
            $this->load->model('home_model');
			
			$sDetails = $this->home_model->getProgramDetails($sProgramID);
			
			if(!empty($sDetails))
			{
				$aResponse['code']      = 1;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = "Program Deleted Succesfully!";
				// Return Response to browser. This will exit the script.
				$this->webResponse($sformat, $aResponse);		
			}
			else
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Details Not available for the Program!';

				$this->webResponse($sformat, $aResponse);
			}
		}
		
		public function assignRelaysToValve()
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
			
			$sRelay1 		= trim($_REQUEST['rl1']);
			$sRelay2 		= trim($_REQUEST['rl2']);
			$sDeviceID		= trim($_REQUEST['nv']);
			$sDeviceIDOld	= trim($_REQUEST['ov']);			
			
			if($sRelay1 == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter valid Relay1!';

				$this->webResponse($sformat, $aResponse);
			}
			if($sRelay2 == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter valid Relay2!';

				$this->webResponse($sformat, $aResponse);
			}
			
			$arrStartRelays	=	array(0,2,4,6,8,10,12,14);
			$arrEndRelays	=	array(1,3,5,7,9,11,13,15);
			
			if(!in_array($sRelay1,$arrStartRelays))
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Relay1 is not valid relay sequence number!';

				$this->webResponse($sformat, $aResponse);
			}
			if(!in_array($sRelay2,$arrEndRelays))
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Relay2 is not valid relay sequence number!';

				$this->webResponse($sformat, $aResponse);
			}
			
			$key = array_search($sRelay1, $arrStartRelays);
			
			if($sRelay2 != $arrEndRelays[$key])
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter the relays in sequence!';

				$this->webResponse($sformat, $aResponse);
			}
			
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']= 0;
            $aResult['status']  = 0;
                       
            $this->load->model('home_model');
			
			$this->home_model->saveValveRelays($sDeviceID,$sDeviceIDOld,$sDevice,$sRelay1,$sRelay2);
			
			$arrValves	=	$this->home_model->getAllValvesHavingRelays();
			$strValves	=	'00000000';
			if(!empty($arrValves))
			{
				foreach($arrValves as $aValve)
				{
					$device_number = $aValve->device_number;
					$strValves[$device_number] = '1';
				}
			}
			
			$hexNumber = dechex(bindec(strrev($strValves)));
			
			$response	=	assignValvesToRelay($hexNumber)	;
			
		}
		
		public function removePumpDetails()
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
			
			$iPumpNumber	= trim($_REQUEST['pn']);
			
			if($iPumpNumber == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter valid Pump Number!';

				$this->webResponse($sformat, $aResponse);
			}
			if($iPumpNumber > 2)
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please enter pump number less than 3!';

				$this->webResponse($sformat, $aResponse);
			}
			
			if($iPumpNumber != '')
			{
				$this->load->model('home_model');
				
				$sResponse      =   get_rlb_status(); // Get the relay borad response from server.
				$sValves        =   $sResponse['valves']; // Valve Devices.
				$sRelays        =   $sResponse['relay'];  // Relay Devices.
				$sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
				
				//Pump device Status
				$sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
				
				if($sPump[$iPumpNumber] > 0) //Currently Pump is ON, make it OFF.
				{
					$aPumpDetails = $this->home_model->getPumpDetails($i);						
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
				}
			}
			
			//Remove Pump Details From database.
			$this->home_model->removePump($iPumpNumber);
			
			//Remove the address associated with the pump on the relay board.
			$Pump	=	'pm'.$iPumpNumber;
			removePumpAddress($Pump);
			
			
			$aResponse['code']      = 1;
			$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
			$aResponse['data']      = "Pump removed Succesfully!";
			// Return Response to browser. This will exit the script.
			$this->webResponse($sformat, $aResponse);

			exit;	
			
		}
		
		
		public function getDeviceStatus_shell()
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
			
			//Get the number of Devices(Blower,Light and Heater) from the settings.
			list($sIP,$sPort,$sExtra) = $this->home_model->getSettings();
            
            //if($iActiveMode == 2) // START : If Mode is Manual.
            {
                if($sDevice != '') // START : If device type is not empty
                {
                    $sResponse      =   get_rlb_status_shell(); // Get the relay borad response from server.
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
						$sPumps 		= '';
						for($i=0;$i<3;$i++)
						{
							$aPumpDetails = $this->home_model->getPumpDetails($i);
							//Variable Initialization to blank.
							$sPumpNumber  	= '';
							$sPumpType  	= '';
							$relay_number   = '';
							
							if(is_array($aPumpDetails) && !empty($aPumpDetails))
							{
							  foreach($aPumpDetails as $aResultEdit)
							  { 
								$sPumpNumber  = $aResultEdit->pump_number;
								$sPumpType    = $aResultEdit->pump_type;
								$relay_number = $aResultEdit->relay_number;
								$sPumpClosure = $aResultEdit->pump_closure;
								
								if($sPumpType != '' && $sPumpClosure == '1')
								{
									if($sPumpType == '12' || $sPumpType == '24')
									{
										if($sPumpType == '24')
										{
											if($sResponse['relay'] != '')
											{
												$sRelays        =   $sResponse['relay'];  // Relay Devices.
												if(isset($sRelays[$relay_number]) && $sRelays[$relay_number] != '')
												{    
													$sPumps      .= $sRelays[$relay_number];
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
												$aResponse['data']      = 'Devices not available.';

												$this->webResponse($sformat, $aResponse);
											}
										}
										else if($sPumpType == '12')
										{
											
											if($sResponse['powercenter'] != '')
											{
												$sPowercenter   =   $sResponse['powercenter']; // Power Center Devices.
												if(isset($sPowercenter[$relay_number]) && $sPowercenter[$relay_number] != '')
												{    
													$sPumps      .= $sPowercenter[$relay_number];
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
												$aResponse['data']      = 'Device not available.';

												$this->webResponse($sformat, $aResponse);
											}
										}
									}
									else
									{
										if(preg_match('/Emulator/',$sPumpType) || preg_match('/Intellicom/',$sPumpType))
										{
											if($sResponse['pump_seq_'.$sPumpNumber.'_st'] > 0)
												$sPumps .= 1;
											else 
												$sPumps .= 0;
										}
									}
								}
								else
								{
									$aResponse['code']      = 5;
									$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
									$aResponse['data']      = 'Pump devices not configured properly or Closure is set to 0.';

									// Return Response to browser. This will exit the script.
									$this->webResponse($sformat, $aResponse);
								}
							  }
							}
							else
							{
								if(isset($sResponse['pump_seq_'.$i.'_st']))
								{
									$sPumps .= '.';
								}
							}
						}
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = $sPumps;
					
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is PUMPS.
					else if($sDevice == "T")// START : If Device is Temperature Sensor.
					{
						$sTemprature	= 	array();
						/* $sTemprature[0] = $sResponse['TS0'];
						$sTemprature[1] = $sResponse['TS1'];
						$sTemprature[2] = $sResponse['TS2'];
						$sTemprature[3] = $sResponse['TS3'];
						$sTemprature[4] = $sResponse['TS4'];
						$sTemprature[5] = $sResponse['TS5']; */
						
						for($i=0; $i<=5; $i++)
						{
							$sTemprature[$i]['temp'] = $sResponse['TS'.$i];	
							$sTemprature[$i]['name'] = $this->home_model->getDeviceName($i,'T');;	
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sTemprature);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Temperature Sensor.
					else if($sDevice == "B")// START : If Device is Blower.
					{
						$sBlower	= 	array();
						
						//Number of blower set on the setting Page.
						$iNumBlower	=	$sExtra['BlowerNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumBlower == 0 || $iNumBlower == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Blower Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						for($i=0; $i<$iNumBlower; $i++)		
						{
							//Blower Details
							$aBlowerDetails  =   $this->home_model->getBlowerDeviceDetails($i);
							if(!empty($aBlowerDetails))
							{
								$sRelays        =   $sResponse['relay'];
								$sPowercenter   =   $sResponse['powercenter'];
								
								foreach($aBlowerDetails as $aBlower)
								{
									$sBlowerStatus	=	'';
									$sRelayDetails  =   unserialize($aBlower->light_relay_number);
									
									//Blower Operated Type and Relay
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
								
									$sBlower[$i] = $sBlowerStatus;
								}
							}
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sBlower);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Blower.
					else if($sDevice == "L")// START : If Device is Light.
					{
						$sLight	= 	array();
						
						//Number of blower set on the setting Page.
						$iNumLight	=	$sExtra['LightNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumLight == 0 || $iNumLight == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Light Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						for($i=0; $i<$iNumLight; $i++)		
						{
							//Light Details
							$aLightDetails  =   $this->home_model->getLightDeviceDetails($i);
							if(!empty($aLightDetails))
							{
								$sRelays        =   $sResponse['relay'];
								$sPowercenter   =   $sResponse['powercenter'];
								
								foreach($aLightDetails as $aLight)
								{
									$sLightStatus	=	'';
									$sRelayDetails  =   unserialize($aLight->light_relay_number);
									
									//Light Operated Type and Relay
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
								
									$sLight[$i] = $sLightStatus;
								}
							}
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sLight);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Heater.
					else if($sDevice == "H")// START : If Device is Heater.
					{
						$sHeater	= 	array();
						
						//Number of blower set on the setting Page.
						$iNumHeater	=	$sExtra['HeaterNumber'];
						
						//If Blower is not set or count is 0.
						if($iNumHeater == 0 || $iNumHeater == '')
						{
							$aResponse['code']      = 5;
							$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
							$aResponse['data']      = 'Heater Devices are not available!';
							
							$this->webResponse($sformat, $aResponse);
							exit;
						}//If Blower is not set or count is 0.
						
						for($i=0; $i<$iNumHeater; $i++)		
						{
							//Heater Details
							$aHeaterDetails  =   $this->home_model->getHeaterDeviceDetails($i);
							if(!empty($aHeaterDetails))
							{
								$sRelays        =   $sResponse['relay'];
								$sPowercenter   =   $sResponse['powercenter'];
								
								foreach($aHeaterDetails as $aHeater)
								{
									$sHeaterStatus	=	'';
									$sRelayDetails  =   unserialize($aHeater->light_relay_number);
									
									//Heater Operated Type and Relay
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
								
									$sHeater[$i] = $sHeaterStatus;
								}
							}
						}
						
						$aResponse['code']      = 1;
						$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
						$aResponse['data']      = json_encode($sHeater);
						
						$this->webResponse($sformat, $aResponse);
						
					}// END : If Device is Heater.
					
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
		
		public function sendBoardLocalIP()
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
            $aResult['response']  = 0;
                     
            $this->load->model('home_model');
            $aIPDetails = $this->home_model->getBoardIP(); 

			if(!empty($aIPDetails))
			{
				$aResponse['code']      = 1;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = json_encode($aIPDetails);
				
				$this->webResponse($sformat, $aResponse);
			}
			else
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'No IP is available!';

				// Return Response to browser. This will exit the script.
				$this->webResponse($sformat, $aResponse);
			}
				
            
        } //END : function sendBoardLocalIP()
		
		
		public function sendIPDeviceDetails()
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
            $iIPID         = isset($_REQUEST['ip']) ? $_REQUEST['ip'] : '' ; // Get the IP ID.
            $sDevice       = isset($_REQUEST['dvc'])  ? $_REQUEST['dvc'] : '' ;  // Get the Device Type.
			
			if($iIPID == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please Enter IP ID!';
				// Return Response.
				$this->webResponse($sformat, $aResponse);
				exit;
			}
			if($sDevice == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Please Enter Device Type!';

				// Return Response.
				$this->webResponse($sformat, $aResponse);
				exit;
			}
			
            
            $aResult            = array();
            $aResult['msg']     = "";
            $aResult['response']  = 0;
                                
            $this->load->model('home_model');
            //GET IP of Device
			$sDeviceIP		= 	$this->home_model->getBoardIPFromID($iIPID);
			
			if($sDeviceIP == '')
			{
				$aResponse['code']      = 5;
				$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
				$aResponse['data']      = 'Invalid IP ID!';
				// Return Response.
				$this->webResponse($sformat, $aResponse);
				exit;
			}
			
			//Get saved IP and PORT 
			list($sIP,$sPort,$extra) = $this->home_model->getSettings();
			
			$shhPort	=	'';
			if(IS_LOCAL == '1')
			{
				//Get SSH port of the RLB board using IP.
				$shhPort = $this->home_model->getSSHPortFromID($iIPID);
			}
			
			//Get the status response of devices from relay board.
			$sResponse      =   get_rlb_status($sDeviceIP,$sPort,$shhPort);
			
			$sValves        =   $sResponse['valves'];   // Valve Device Status
			$sRelays        =   $sResponse['relay'];    // Relay Device Status
			$sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
			$sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
			
			$aResponseDetails		= array();
			
			if($sDevice == 'R')
			{
				$iCount=strlen($sRelays);
				
				for($i=0; $i<$iCount; $i++)
				{
					$relay = $sRelays[$i] ;
					
					if($relay != '.' && $relay != '')
					{
						$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
						if($name == '')
							$name = 'Relay '.$i;
						
						$aResponseDetails[$i] = $name;
					}
					else
					{
						$aResponseDetails[$i] = '.';
					}
				}
			}
			else if($sDevice == 'P')
			{
				$iCount=strlen($sPowercenter);
				
				for($i=0; $i<$iCount; $i++)
				{
					$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
					if($name == '')
						$name = 'PowerCenter '.$i;
						
					$aResponseDetails[$i] = $name;
				}
			}
			if($sDevice == 'V')
			{
				$iCount=strlen($sValves);
				
				for($i=0; $i<$iCount; $i++)
				{
					$relay = $sValves[$i] ;
					
					if($relay != '.' && $relay != '')
					{
						$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
						if($name == '')
							$name = 'Valve '.$i;
						
						$aResponseDetails[$i] = $name;
					}
					else
					{
						$aResponseDetails[$i] = '.';
					}
				}
			}
			if($sDevice == 'PS')
			{
				$iCount=count($sPump);
				
				for($i=0; $i<$iCount; $i++)
				{
					$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
					if($name == '')
						$name = 'Pump '.$i;
						
					$aResponseDetails[$i] = $name;
				}
			}
			if($sDevice == 'L')
			{
				$iCount = $extra['LightNumber'];
				for($i=0;$i<$iCount;$i++)
				{
					$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
					if($name == '')
						$name = 'Light '.$i;
						
					$aResponseDetails[$i] = $name;
				}
			}
			if($sDevice == 'H')
			{
				$iCount = $extra['HeaterNumber'];
				for($i=0;$i<$iCount;$i++)
				{
					$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
					if($name == '')
						$name = 'Heater '.$i;
						
					$aResponseDetails[$i] = $name;
				}
			}
			if($sDevice == 'B')
			{
				$iCount = $extra['BlowerNumber'];
				for($i=0;$i<$iCount;$i++)
				{
					$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
					if($name == '')
						$name = 'Blower '.$i;
						
					$aResponseDetails[$i] = $name;
				}
			}
			if($sDevice == 'M')
			{
				$iCount = $extra['MiscNumber'];
				for($i=0;$i<$iCount;$i++)
				{
					$name = $this->home_model->getDeviceName($i,$sDevice,$iIPID);
											
					if($name == '')
						$name = 'Misc '.$i;
						
					$aResponseDetails[$i] = $name;
				}
			}
			
			$aResponse['code']      = 1;
			$aResponse['status']    = $this->aApiResponseCode[ $aResponse['code'] ]['HTTP Response'];
			$aResponse['data']      = json_encode($aResponseDetails);
			
			$this->webResponse($sformat, $aResponse);
				
            
        } //END : function sendBoardLocalIP()
	} //END : Class Service
    
    /* End of file web.php */
    /* Location: ./application/controllers/web.php */
?>

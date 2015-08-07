<?php
    /**
    * @Programmer: Dhiraj S.
    * @Created: 13 July 2015
    * @Modified: 
    * @Description: Home Controller for dashboard and device details.
    **/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller 
{
    public function __construct()  
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('common_functions'); //Common functions will be available for all functions in the file.
        if (!$this->session->userdata('is_admin_login')) //START : Check if user login or not.
        {
            redirect('dashboard/login/');
        } //END : Check if user login or not.
    }

    public function index() //START : Function for dashboard
    {
        $aViewParameter         =   array(); // Array for passing parameter to view.
        $aViewParameter['page'] =   'home'; 
        
        //Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();

        //Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
        
        //$sResponse    =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves']; // Valve Device Status
        $sRelays        =   $sResponse['relay'];  // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
        $sTime          =   $sResponse['time']; // Server Time from Response
        
        //Pump device Status
        $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
        // Temperature Sensor Device 
		$sTemprature    =   array($sResponse['temp_sensor_1'],$sResponse['temp_sensor_2'],$sResponse['temp_sensor_3'],$sResponse['temp_sensor_4'],$sResponse['temp_sensor_5']);
			
        //START : Parameter for View
            $aViewParameter['relay_count']  =   strlen($sRelays);
            $aViewParameter['valve_count']  =   strlen($sValves);
            $aViewParameter['power_count']  =   strlen($sPowercenter);
			
            $aViewParameter['pump_count']   		=   count($sPump);
			$aViewParameter['temprature_count']   	=   count($sTemprature);
            $aViewParameter['time']         =   $sTime;
        //END : Parameter for View
            
        //Home View
        $this->load->view('Home',$aViewParameter);

    } //END : Function for dashboard
    
    public function setting() //START : Function to show Setting Page or Device Type Page
    {
        $aViewParameter                 =   array(); // Array for passing parameter to view.
        $aViewParameter['page']         =   'home';
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
        
        //Get the type of the device to show page.
        $sPage  =   $this->uri->segment('3'); 

        $this->load->model('home_model');
        
        //Current mode of the system.
        $aViewParameter['iActiveMode'] =    $this->home_model->getActiveMode();

        if($sPage == '') // START : If no device type then show setting page.
        {
            $aViewParameter['page']         =   'setting';
            
            if($this->input->post('command') == 'Save Setting') // START : IF Setting details are posted.
            {
                // Get mode value from POST.
                $iMode  =   $this->input->post('relay_mode'); 
                $this->load->model('home_model');
                
                // Get IP and PORT value from POST.
                $sIP    =   $this->input->post('relay_ip_address');
                $sPort  =   $this->input->post('relay_port_no');
                
                //Check for IP constant if IP is blank in POST
                if($sIP == '')
                {
                    if(IP_ADDRESS)
                        $sIP = IP_ADDRESS;
                }
                
                 //Check for PORT number constant if PORT is blank in POST
                if($sPort == '')
                {   
                    if(PORT_NO)
                        $sPort = PORT_NO;
                }
                
                if($sIP == '' || $sPort == '') //IF Still IP or PORT is blank, error flag is set to 1.
                {
                    $aViewParameter['err_sucess']    =   '1';
                }
                else 
                {
                    //Save IP and PORT
                    $this->home_model->updateSetting($sIP,$sPort);
                    
                    /*//Save Mode
                    $this->home_model->updateMode($iMode);
                    
                    //Get the status response of devices from relay board.
                    $sResponse      =   get_rlb_status();
                    
                    $sValves        =   $sResponse['valves']; // Valve Device Status
                    $sRelays        =   $sResponse['relay'];  // Relay Device Status
                    $sPowercenter   =   $sResponse['powercenter']; // Power center Device Status
                    
                    //1-auto, 2-manual, 3-timeout
                    if($iMode == 3 || $iMode == 1) //START : IF mode is not Manual then switch all devices OFF.
                    { 
                        if($sRelays != '') //off all relays
                        {
                            $sRelayNewResp = str_replace('1','0',$sRelays);
                            onoff_rlb_relay($sRelayNewResp);
                        }
                        
                        if($sValves != '') //off all valves
                        {
                            $sValveNewResp = str_replace(array('1','2'), '0', $sValves);
                            onoff_rlb_valve($sValveNewResp);  
                        }
                        
                        if($sPowercenter != '') //off all power center
                        {
                            $sPowerNewResp = str_replace('1','0',$sPowercenter);  
                            onoff_rlb_powercenter($sPowerNewResp); 
                        }

                    } //END : IF mode is not Manual then switch all devices OFF. if($iMode == 3 || $iMode == 1)
					*/
                    
                    $aViewParameter['sucess']    =   '1'; //Set success flag 1 if Saved details. 
                } // END : else for if($sIP == '' || $sPort == '')
             } // END : IF Setting details are posted. if($this->input->post('command') == 'Save Setting') 
            
            //START : Create Mode select Box to show on setting page. 
                $aModes =   $this->home_model->getAllModes(); //Get all available mode from DB.
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
            //END : Create Mode select Box to show on setting page.
            
            //Get saved IP and PORT 
            list($aViewParameter['sIP'],$aViewParameter['sPort']) = $this->home_model->getSettings();
            
			//View Setting
            $this->load->view('Setting',$aViewParameter);
        } // END : If no device type then show setting page. if($sPage == '')
        else //START : If device type is available then device page.
        {
            //Check if IP, PORT and Mode is set or not.
            $this->checkSettingsSaved();
            
            //Get the status response of devices from relay board.
            $sResponse      =   get_rlb_status();
            //$sResponse      =   array('valves'=>'','powercenter'=>'0000','time'=>'','relay'=>'0000');
            
            $sValves        =   $sResponse['valves']; // Valve Device Status
            $sRelays        =   $sResponse['relay'];  // Relay Device Status
            $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
            $sTime          =   $sResponse['time']; // Server time from Response
            
            // Pump Device Status
            $sPump          =   array($sResponse['pump_seq_0_st'],$sResponse['pump_seq_1_st'],$sResponse['pump_seq_2_st']);
			// Temperature Sensor Device 
			$sTemprature    =   array($sResponse['temp_sensor_1'],$sResponse['temp_sensor_2'],$sResponse['temp_sensor_3'],$sResponse['temp_sensor_4'],$sResponse['temp_sensor_5']);

            //START : Parameter for View
                $aViewParameter['relay_count']      =   strlen($sRelays);
                $aViewParameter['valve_count']      =   strlen($sValves);
                $aViewParameter['power_count']      =   strlen($sPowercenter);
                $aViewParameter['time']             =   $sTime;
                $aViewParameter['pump_count']       =   count($sPump);
				$aViewParameter['temprature_count']       =   count($sTemprature);

                $aViewParameter['sRelays']          =   $sRelays; 
                $aViewParameter['sPowercenter']     =   $sPowercenter;
                $aViewParameter['sValves']          =   $sValves;
                $aViewParameter['sPump']            =   $sPump;
				$aViewParameter['sTemprature']      =   $sTemprature;

                $aViewParameter['sDevice']          =   $sPage;
            //END : Parameter for View
            
            // Device View to show device page.
            $this->load->view('Device',$aViewParameter); 
        } //END : If device type is available then device page. Else for if($sPage == '')
    } //END : Function to show Setting Page or Device Type Page

    public function updateStatusOnOff() //START : Function to swich the particular device ON/OFF
    {
        //Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
        
        //$sResponse      =   array('valves'=>'0120','powercenter'=>'0000','time'=>'','relay'=>'0000');
        $sValves        =   $sResponse['valves'];   // Valve Device Status
        $sRelays        =   $sResponse['relay'];    // Relay Device Status
        $sPowercenter   =   $sResponse['powercenter']; // Power Center Device Status
        
        //Get the Device details from POST whose status will be changed.
        $sName          =   $this->input->post('sName'); //Device Number
        $sStatus        =   $this->input->post('sStatus'); //Change status for Device
        $sDevice        =   $this->input->post('sDevice'); //Device Type

        $sNewResp       =   '';
           
        $this->load->model('home_model');
        
        //R = Relay, P = PowerCenter, V = Valve, PS = Pumps
        if($sDevice == 'R') // If Device type is Relay
        {
            $sNewResp = replace_return($sRelays, $sStatus, $sName );
            onoff_rlb_relay($sNewResp);
            $this->home_model->updateDeviceRunTime($sName,$sDevice,$sStatus);
            
        }
        if($sDevice == 'P') // If Device type is Power Center
        {
            $sNewResp = replace_return($sPowercenter, $sStatus, $sName );
            onoff_rlb_powercenter($sNewResp);
        }
        if($sDevice == 'V') // If Device type is Valve
        {
            $sNewResp = replace_return($sValves, $sStatus, $sName );
            onoff_rlb_valve($sNewResp);
        }
        if($sDevice == 'PS') // If Device type is Pump
        {
            $sNewResp = '';

            if($sStatus == '0')
                $sNewResp =  $sName.' '.$sStatus;
            else if($sStatus == '1')
            {
                //Get Pump Configuration details.
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
    } //END : Function to swich the particular device ON/OFF

    public function deviceName() // START : Function Show Device Name Form and Save
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        
        //Get Device ID and Device Type From URL.
        $sDeviceID  =   base64_decode($this->uri->segment('3'));
        $sDevice    =   base64_decode($this->uri->segment('4'));

        if($sDeviceID == '') // START : If Device ID is blank check whether Device ID is present in POST  
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        } // END : If Device ID is blank check whether Device ID is present in POST

        if($sDevice == '') // START : If Device type is blank check whether Device type is present in POST    
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '') // IF Device type not present in POST redirect to the Dashboard.
                redirect(site_url('home'));
        }
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;

        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') //START : If device name form is POSTED.
        {
            // Get the device name From POST.
            $sDeviceName = $this->input->post('sDeviceName');
            //Save device name
            $this->home_model->saveDeviceName($sDeviceID,$sDevice,$sDeviceName);

            $aViewParameter['sucess']    =   '1';//Set success parameter.
        } //END : If device name form is POSTED.
        // Get the saved device name
        $aViewParameter['sDeviceName']      =   $this->home_model->getDeviceName($sDeviceID,$sDevice);
        
        // Device Name save View
        $this->load->view('DeviceName',$aViewParameter); 
    } // END : Function Show Device Name Form and Save
    
    public function addTime() //START : Function to save/update the Relay Time.
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        
        //Get Device ID and Device Type From URL.
        $sDeviceID  =   base64_decode($this->uri->segment('3'));
        $sDevice    =   base64_decode($this->uri->segment('4'));
       
        if($sDeviceID == '') // START : If Device ID is blank check whether Device ID is present in POST  
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        } // END : If Device ID is blank check whether Device ID is present in POST
        
        if($sDevice == '') // START : If Device type is blank check whether Device type is present in POST    
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '') // IF Device type not present in POST redirect to the Dashboard.
                redirect(site_url('home'));
        }
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;
        
        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') //START : If device name form is POSTED.
        {
            // Get the device name From POST.
            $sDeviceTime = $this->input->post('sDeviceTime');
            //Save device name
            $this->home_model->saveDeviceTime($sDeviceID,$sDevice,$sDeviceTime);

            $aViewParameter['sucess']    =   '1';//Set success parameter.
        } //END : If device name form is POSTED.
        // Get the saved device name
        $aViewParameter['sDeviceTime']      =   $this->home_model->getDeviceTime($sDeviceID,$sDevice);
        
        // Time save/update View
        $this->load->view('Time',$aViewParameter); 
    }//END : Function to save/update the Relay Time.


    public function positionName() //START : Function to save position names for Valve 
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        $sDeviceID  =   base64_decode($this->uri->segment('3')); //Get Device ID to which position names will be saved.
        $sDevice    =   base64_decode($this->uri->segment('4')); //Get Device Type

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page  
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                if($sDevice != '')
                redirect(site_url('home/setting/'.$sDevice));
        } //END : Check if Device id is blank then redirect to the device list page  

        if($sDevice == '') //START : Check if Device type is blank then redirect to home page
        {
            $sDevice  =   base64_decode($this->input->post('sDevice'));
            if($sDevice == '')// IF Device Type not present in POST redirect to the Dashboard
                redirect(site_url('home'));
        } //END : Check if Device type is blank then redirect to home page
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sDevice']      =   $sDevice;

        $this->load->model('home_model');

        if($this->input->post('command') == 'Save') // START: Save position names in DB for device.
        {
            //Get position values from POST
            $sPositionName1 = $this->input->post('sPositionName1');
            $sPositionName2 = $this->input->post('sPositionName2');
            $this->home_model->savePositionName($sDeviceID,$sDevice,$sPositionName1,$sPositionName2);

            $aViewParameter['sucess']    =   '1'; // Set success flag 1
        } // END : Save position names in DB for device.

        //Get Existing saved position names for Device
        list($aViewParameter['sPositionName1'],$aViewParameter['sPositionName2'])      =   $this->home_model->getPositionName($sDeviceID,$sDevice);
        
        //Position Name Save View
        $this->load->view('PositionName',$aViewParameter); 
    } //END : Function to save position names for Valve
	
	public function setProgramsPump() // START : Function to save/update/delete the Programs to run relay in Auto mode.
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        
        //Get Values From URL.
        $sDeviceID      =   base64_decode($this->uri->segment('3')); // Get Device ID
        $sProgramID     =   base64_decode($this->uri->segment('4')); // Get Program ID 
        $sProgramDelete =   $this->uri->segment('5');// Get value for delete

        $this->load->model('home_model');

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                redirect(site_url('home/setting/R'));
        }
       
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        
        if($this->input->post('command') == 'Save') // START : Save program details.
        {
            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber');

            $this->home_model->saveProgramDetails($this->input->post(),$sDeviceID,'PS');
            $aViewParameter['sucess']    =   '1';
        }// END : Save program details.

        if($this->input->post('command') == 'Update') // START : Update program details.
        {
            if($sProgramID == '') //START : Check if Program id is blank then redirect
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '') // IF Program ID not present in POST redirect
                    redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
            }  

            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber'); 

            $this->home_model->updateProgramDetails($this->input->post(),$sProgramID,$sDeviceID,'PS');
            redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
        }// END : Update program details.

        if($sProgramDelete != '' && $sProgramDelete == 'D') // START : Delete program details.
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
            }

            $this->home_model->deleteProgramDetails($sProgramID);
            redirect(site_url('home/setProgramsPump/'.base64_encode($sDeviceID)));
        } // START : Delete program details.

        // Get saved program details     
        $aViewParameter['sProgramDetails'] = $this->home_model->getProgramDetailsForDevice($sDeviceID,'PS');

        if($sProgramID != '') //If program exists the get program details.
        {
            $aViewParameter['sProgramID'] = $sProgramID;
            $aViewParameter['sProgramDetailsEdit'] = $this->home_model->getProgramDetails($sProgramID);
        }
        else
        {
            $aViewParameter['sProgramID']          = ''; 
            $aViewParameter['sProgramDetailsEdit'] = '';
        }
        
        //Program View for Getting and Showing Programs
        $this->load->view('PumpPrograms',$aViewParameter); 
    } // END : Function to save/update/delete the Programs to run relay in Auto mode.
	
    public function setPrograms() // START : Function to save/update/delete the Programs to run relay in Auto mode.
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        
        //Get Values From URL.
        $sDeviceID      =   base64_decode($this->uri->segment('3')); // Get Device ID
        $sProgramID     =   base64_decode($this->uri->segment('4')); // Get Program ID 
        $sProgramDelete =   $this->uri->segment('5');// Get value for delete

        $this->load->model('home_model');

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') // IF Device ID not present in POST redirect to the Device List
                redirect(site_url('home/setting/R'));
        }
       
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        
        if($this->input->post('command') == 'Save') // START : Save program details.
        {
            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber');

            $this->home_model->saveProgramDetails($this->input->post(),$sDeviceID,'R');
            $aViewParameter['sucess']    =   '1';
        }// END : Save program details.

        if($this->input->post('command') == 'Update') // START : Update program details.
        {
            if($sProgramID == '') //START : Check if Program id is blank then redirect
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '') // IF Program ID not present in POST redirect
                    redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
            }  

            if($this->input->post('sRelayNumber') != '')
                $sDeviceID   =  $this->input->post('sRelayNumber'); 

            $this->home_model->updateProgramDetails($this->input->post(),$sProgramID,$sDeviceID,'R');
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
        }// END : Update program details.

        if($sProgramDelete != '' && $sProgramDelete == 'D') // START : Delete program details.
        {
            if($sProgramID == '')
            {
                $sProgramID  =   base64_decode($this->input->post('sProgramID'));
                if($sProgramID == '')
                    redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
            }

            $this->home_model->deleteProgramDetails($sProgramID);
            redirect(site_url('home/setPrograms/'.base64_encode($sDeviceID)));
        } // START : Delete program details.

        // Get saved program details     
        $aViewParameter['sProgramDetails'] = $this->home_model->getProgramDetailsForDevice($sDeviceID,'R');

        if($sProgramID != '') //If program exists the get program details.
        {
            $aViewParameter['sProgramID'] = $sProgramID;
            $aViewParameter['sProgramDetailsEdit'] = $this->home_model->getProgramDetails($sProgramID);
        }
        else
        {
            $aViewParameter['sProgramID']          = ''; 
            $aViewParameter['sProgramDetailsEdit'] = '';
        }
        
        //Program View for Getting and Showing Programs
        $this->load->view('Programs',$aViewParameter); 
    } // END : Function to save/update/delete the Programs to run relay in Auto mode.

    public function pumpConfigure() // START : Function for saving Pump Configuration
    {
        $aViewParameter              =   array(); // Array for passing parameter to view.
        $aViewParameter['page']      =   'home';
        $aViewParameter['sucess']    =   '0';
        $sDeviceID      =   base64_decode($this->uri->segment('3'));
       
        $this->load->model('home_model');

        if($sDeviceID == '') //START : Check if Device id is blank then redirect to the device list page   
        {
            $sDeviceID  =   base64_decode($this->input->post('sDeviceID'));
            if($sDeviceID == '') //START : Check if Device id is blank in POST then redirect to the device list page
                redirect(site_url('home/setting/PS/'));
        }

        if($this->input->post('command') == 'Save') // START : Save pump configuration Details.
        {
            if($this->input->post('sPumpNumber') != '')
                $sDeviceID   =  $this->input->post('sPumpNumber');

            $this->home_model->savePumpDetails($this->input->post(),$sDeviceID);
            $aViewParameter['sucess']    =   '1';
        }// END : Save pump configuration Details.
        
        //Parameter for View
        $aViewParameter['sDeviceID']    =   $sDeviceID;
        $aViewParameter['sPumpDetails'] = $this->home_model->getPumpDetails($sDeviceID);
        
        //Pump view for configuration of pumps
        $this->load->view('Pump',$aViewParameter); 
    } // END : Function for saving Pump Configuration

    public function checkSettingsSaved() //START : Check if IP and PORT is added.
    {
        $this->load->model('home_model');
        list($sIpAddress, $sPortNo) = $this->home_model->getSettings();
        
        //If IP is not saved check for IP constant
        if($sIpAddress == '')
        {
            if(IP_ADDRESS)
                $sIpAddress = IP_ADDRESS;
        }
       
        //If PORT is not saved check for PORT constant
        if($sPortNo == '')
        {   
            if(PORT_NO)
                $sPortNo = PORT_NO;
        }

        if($sIpAddress == '' || $sPortNo == '')
            redirect(site_url('home/setting/'));
    }//END : Check if IP and PORT is added.

    public function systemStatus() //START : Server response page of relay board.
    {
        $aViewParameter         =   array(); // Array for passing parameter to view.
        $aViewParameter['page'] =   'status';
        //Check if IP, PORT and Mode is set or not.
        $this->checkSettingsSaved();

        //Get the status response of devices from relay board.
        $sResponse      =   get_rlb_status();
        
        //Parameter for view
        $aViewParameter['response'] =$sResponse['response'];
        
        //Status view for showing relay board status.
        $this->load->view('Status',$aViewParameter);
    } //END : Server response page of relay board.
        
}//END : Class Home

/* End of file home.php */
/* Location: ./application/controllers/home.php */
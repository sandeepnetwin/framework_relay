<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home_model extends CI_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function getSettings()
    {
        $sSql       =   "SELECT * FROM rlb_setting";
        $query      =   $this->db->query($sSql);
       
        $aSettings  =   array(0=>'',1=>'',2=>'');

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {    
                $aSettings[0]   = $aRow->ip_address;
                $aSettings[1]   = $aRow->port_no;  
				$aSettings[2]   = unserialize($aRow->extra);
            }
        }

        return $aSettings;
    }
    public function getAllModes() 
    {
        $sSql   =   "SELECT * FROM rlb_modes";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            return $query->result(); 
        }

        return '';
    }

    public function getActiveMode()
    {
        $sSql   =   "SELECT mode_id FROM rlb_modes WHERE mode_status = '1'";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $rowResult)
            {
                return $rowResult->mode_id; 
            }
        }

        return '';
    }
	
	public function getActiveModeDetails()//START : Function to get the active mode Details.
    {
		$aModeDetails	=	array();
        $sSql   	=   "SELECT mode_id,mode_name,start_time FROM rlb_modes WHERE mode_status = '1'";
        $query  	=   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $rowResult)
            {
                $aModeDetails['mode_id']	=	$rowResult->mode_id;	
				$aModeDetails['mode_name']	=	$rowResult->mode_name;
				$aModeDetails['start_time']	=	$rowResult->start_time;
            }
        }

        return $aModeDetails;
    }//END : Function to get the active mode Details.

    public function updateMode($imode)
    {
        $data = array('mode_status' => '0','start_time'=>'00-00-00 00:00:00');
        $this->db->update('rlb_modes', $data);

        $data = array('mode_status' => '1','start_time'=>date('Y-m-d H:i:s'));
        $this->db->where('mode_id',$imode);
        $this->db->update('rlb_modes', $data);
    }
	
	//START : Function to SAVE/UPDATE manual Mode Time.
	public function updateManualModeTime($sTime)
	{
		$data = array('timer_total' => $sTime);
        $this->db->where('mode_id',2);
        $this->db->update('rlb_modes', $data);
	}//END : Function to SAVE/UPDATE manual Mode Time.
	
	//START : Function To get the Manual Mode Time
	public function getManualModeTime()
	{
		$sTime	=	'';
		$sSql   =   "SELECT timer_total FROM rlb_modes WHERE mode_id=2";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
				$sTime = $aRow->timer_total;
			}
		}
		
		return $sTime;
	}//END : Function To get the Manual Mode Time
	
	//START : Function to update the Manual Timer Start and End Time
	public function updateManualModeTimer($sProgramAbsStart,$sAbsoluteEnd)
	{
		$data = array('timer_start'=> $sProgramAbsStart,'timer_end'=>$sAbsoluteEnd);
        $this->db->where('mode_id',2);
        $this->db->update('rlb_modes', $data);
	}//END : Function to update the Manual Timer Start and End Time
	
	//START : Function to get the Manual Mode Timer START and END Time.
	public function getManualModeTimer()
	{
		$aTime	=	array();
		
		$sSql   =   "SELECT timer_start,timer_end FROM rlb_modes WHERE mode_id=2";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {    
                $aTime['START']	=	$aRow->timer_start;
				$aTime['END']	=	$aRow->timer_end;
            }
        }
		
		return $aTime;
		
	}//END : Function to get the Manual Mode Timer START and END Time.
	
    public function updateSetting($sIP, $sPort)
    {
        
        $sSql   =   "SELECT * FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {    
                $data = array('ip_address' => $sIP, 'port_no' => $sPort );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
            }
        }
        else
        {
            $data = array('ip_address' => $sIP, 'port_no' => $sPort );
            $this->db->insert('rlb_setting', $data);
        }
    }
	
	public function updateSettingTemp($aTemprature)
    {
		$aExtra	=	array();
        $sSql   =   "SELECT * FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				
				$aExtra['Pool_Temp'] 		 = $aTemprature['Pool_Temp'];
				$aExtra['Pool_Temp_Address'] = $aTemprature['Pool_Temp_Address'];
				$aExtra['Spa_Temp'] 		 = $aTemprature['Spa_Temp'];
				$aExtra['Spa_Temp_Address']  = $aTemprature['Spa_Temp_Address'];
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
            }
        }
        else
        {
            $data = array('extra' => serialize($aExtra) );
            $this->db->insert('rlb_setting', $data);
        }
    }
	
	public function updateSettingNumberDevice($aNumDevice,$showRemoteSpa)
	{
		$aExtra	=	array();
		$sSql   =   "SELECT id,extra FROM rlb_setting";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				if($aRow->extra != '')
					$aExtra = unserialize($aRow->extra);
				
				$aExtra['PumpsNumber'] = $aNumDevice['PumpsNumber'];
				$aExtra['ValveNumber'] = $aNumDevice['ValveNumber'];
				$aExtra['Remote_Spa']  = $showRemoteSpa;
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
            }
        }
        else
        {
            $data = array('extra' => serialize($aNumDevice) );
            $this->db->insert('rlb_setting', $data);
        }
	}
	
	public function saveDeviceName($sDeviceID,$sDevice,$sDeviceName)
    {

        $sSql   =   "SELECT device_id FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type ='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
                $sSqlUpdate =   "UPDATE rlb_device SET device_name='".$sDeviceName."', last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id = ".$aRow->device_id;
                $this->db->query($sSqlUpdate);
            }
        }
        else
        {
            $sSqlInsert =   "INSERT INTO rlb_device(device_number,device_name,device_type,last_updated_date) VALUES('".$sDeviceID."','".$sDeviceName."','".$sDevice."','".date('Y-m-d H:i:s')."')";
            $this->db->query($sSqlInsert);
        }
    }

    public function getDeviceName($sDeviceID,$sDevice)
    {
        $sDeviceName = '';

        $sSql   =   "SELECT device_id,device_name FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
                $sDeviceName = $aRow->device_name;
            }
        }

        return $sDeviceName;
    }

    public function getSettingDetails()
    {
        $arrSettingDetails  =   array();
        $query = $this->db->get('rlb_setting');
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $arrSettingDetails[] = $row->ip_address;
                $arrSettingDetails[] = $row->port_no;
            }
        }

        return $arrSettingDetails;
    }

    public function saveProgramDetails($aPost,$sDeviceID,$sDevice)
    {
        $sProgramName   =   $aPost['sProgramName'];
        $sProgramType   =   $aPost['sProgramType'];
        $sProgramDays   =   '0';

        if($sProgramType == '2')
        {
            $sProgramDays   =   $aPost['sProgramDays'];
            $sProgramDays   =   implode(',',$sProgramDays);
        }

        $sStartTime         =   $aPost['sStartTime'].':00';
        $sEndTime           =   $aPost['sEndTime'].':00';
        $bAbsoluteProgram   =   $aPost['isAbsoluteProgram'];

        $time1      = new DateTime($sStartTime);
        $time2      = new DateTime($sEndTime);
        $interval   = $time2->diff($time1);
        $sTotalTime = $interval->format('%H:%I:%S');

        $data = array('program_name' 	=> $sProgramName, 
                      'device_number'   => $sDeviceID,
					  'device_type' 	=> $sDevice, 		
                      'program_type' 	=> $sProgramType,
                      'program_days' 	=> $sProgramDays,
                      'start_time'		=> $sStartTime,
                      'end_time'  		=> $sEndTime,
                      'program_created_date'=> date('Y-m-d H:i:s'),
                      'program_absolute'=> $bAbsoluteProgram,
                      'program_absolute_total_time'=>$sTotalTime
                      );
        $this->db->insert('rlb_program', $data);
		
		$insert_id = $this->db->insert_id();
		return $insert_id;
    }

    public function updateProgramDetails($aPost,$sProgramID,$sDeviceID,$sDevice)
    {
        $sProgramName   =   $aPost['sProgramName'];
        $sProgramType   =   $aPost['sProgramType'];
        
        $sProgramDays   =   '0';

        if($sProgramType == '2')
        {
            $sProgramDays   =   $aPost['sProgramDays'];
            $sProgramDays   =   implode(',',$sProgramDays);
        }

        $sStartTime         =   $aPost['sStartTime'].':00';
        $sEndTime           =   $aPost['sEndTime'].':00';
        $bAbsoluteProgram   =   $aPost['isAbsoluteProgram'];

        $time1     = new DateTime($sStartTime);
        $time2      = new DateTime($sEndTime);
        $interval   = $time2->diff($time1);
        $sTotalTime = $interval->format('%H:%I:%S');

        $data = array('program_name' => $sProgramName, 
                      'device_number'    => $sDeviceID,
					  'device_type' 	=> $sDevice,					  
                      'program_type' => $sProgramType,
                      'program_days' => $sProgramDays,
                      'start_time'=> $sStartTime,
                      'end_time'  => $sEndTime,
                      'program_modified_date'=> date('Y-m-d H:i:s'),
                      'program_absolute' => $bAbsoluteProgram,
                      'program_absolute_total_time'=>$sTotalTime
                      );
        $this->db->where('program_id', $sProgramID);
        $this->db->update('rlb_program', $data);
    }

    public function deleteProgramDetails($sProgramID)
    {
        $data = array('program_delete' => '1');
        $this->db->where('program_id', $sProgramID);
        $this->db->update('rlb_program', $data);   
    }

    function getProgramDetailsForDevice($sDeviceID,$sDevice)
    {

        $this->db->where('device_number',$sDeviceID);
		$this->db->where('device_type',$sDevice);
        $this->db->where('program_delete','0');
        $query = $this->db->get('rlb_program');

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
    }

    public function getProgramDetails($sProgramID)
    {

        $this->db->where('program_id',$sProgramID);
        $query = $this->db->get('rlb_program');

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
    }

    public function getAllProgramsDetails()
    {
        $this->db->where('program_delete','0');
        //$this->db->where('relay_prog_id','7');
        $query = $this->db->get('rlb_program');

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
    }

    public function updateProgramStatus($iProgId,$sStatus)
    {
        if($iProgId)
        {
            $data = array('program_active' => $sStatus);
            $this->db->where('program_id', $iProgId);
            $this->db->update('rlb_program', $data);
        }
    }

    public function updateAbsProgramRun($iProgId,$sStatus)
    {
        if($iProgId)
        {
            $data = array('program_absolute_run' => $sStatus);
            $this->db->where('program_id', $iProgId);
            $this->db->update('rlb_program', $data);
        }
    }

    public function updateProgramAbsDetails($iProgId,$aAbsoluteDetails)
    {
        $sProgramAbsStart       =   $aAbsoluteDetails['absolute_s'];
        $sProgramAbsEnd         =   $aAbsoluteDetails['absolute_e'];
        $sProgramAbsTotal       =   $aAbsoluteDetails['absolute_t'];
        $sProgramAbsAlreadyRun  =   $aAbsoluteDetails['absolute_ar'];
        $sProgramAbsStartDay    =   $aAbsoluteDetails['absolute_sd'];
        $sProgramAbsRunStatus   =   $aAbsoluteDetails['absolute_st'];

        if($sProgramAbsStartDay == '' || strtotime($sProgramAbsStartDay) != strtotime(date('Y-m-d')))
        {
            $aTotalTime       =   explode(":",$sProgramAbsTotal);
            $sProgramAbsStart =   date("H:i:s", time());
            $aStartTime       =   explode(":",$sProgramAbsStart);
            $sProgramAbsEnd   =   mktime(($aStartTime[0]+$aTotalTime[0]),($aStartTime[1]+$aTotalTime[1]),($aStartTime[2]+$aTotalTime[2]),0,0,0);
            $sAbsoluteEnd     =   date("H:i:s", $sProgramAbsEnd);

            $data = array('program_absolute_start_time'  => $sProgramAbsStart,
                          'program_absolute_end_time'    => $sAbsoluteEnd,
                          'program_absolute_run_time'    => '',
                          'program_absolute_start_date'  => date('Y-m-d'),
                          'program_absolute_run'         => '0'
                          );

            $this->db->where('program_id', $iProgId);
            $this->db->update('rlb_program', $data);
        }
        else if(strtotime($sProgramAbsStartDay) == strtotime(date('Y-m-d')))
        {

            if($sProgramAbsAlreadyRun != '')
            {
                $aAlreadyRunTime    =   explode(":",$sProgramAbsAlreadyRun);
                $aTotalTime         =   explode(":",$sProgramAbsTotal);
                $sProgramAbsStart   =   date("H:i:s", time());
                $aStartTime       	=   explode(":",$sProgramAbsStart);
                $sProgramAbsEnd   	=   mktime(($aStartTime[0]+$aAlreadyRunTime[0]),($aStartTime[1]+$aAlreadyRunTime[1]),($aStartTime[2]+$aAlreadyRunTime[2]),0,0,0);
                $sAbsoluteEnd     	=   date("H:i:s", $sProgramAbsEnd);

                $data = array(  'program_absolute_start_time'  => $sProgramAbsStart,
                                'program_absolute_end_time'    => $sAbsoluteEnd,
                                'program_absolute_run_time'    => '',
                                'program_absolute_start_date'  => date('Y-m-d'),
                                'program_absolute_run'         => $sProgramAbsRunStatus
                              );

                $this->db->where('program_id', $iProgId);
                $this->db->update('rlb_program', $data);
            }
        }
    }

    public function updateAlreadyRunTime($iProgId,$aAbsoluteDetails)
    {
        $sProgramAbsStart       =   $aAbsoluteDetails['absolute_s'];
        $sProgramAbsEnd         =   $aAbsoluteDetails['absolute_e'];
        $sProgramAbsTotal       =   $aAbsoluteDetails['absolute_t'];
        $sProgramAbsAlreadyRun  =   $aAbsoluteDetails['absolute_ar'];
        $sProgramAbsStartDay    =   $aAbsoluteDetails['absolute_sd'];
        $sProgramAbsRunStatus   =   $aAbsoluteDetails['absolute_st'];

        $time1      = new DateTime($sProgramAbsStart);
        $tempTime   = date('H:i:s',time());
        $time2      = new DateTime($tempTime);
        $interval   = $time2->diff($time1);
        $sTotalTime = $interval->format('%H:%I:%S');

        $time1      = new DateTime($sTotalTime);
        $time2      = new DateTime($sProgramAbsTotal);
        $interval   = $time2->diff($time1);
        $sTotalTime = $interval->format('%H:%I:%S');
		
		$data = array('program_absolute_start_time'  => '',
                      'program_absolute_end_time'    => '',
                      'program_absolute_run_time'    => $sTotalTime,
                      'program_absolute_start_date'  => date('Y-m-d'),
                      'program_absolute_run'         => $sProgramAbsRunStatus
                      );

        $this->db->where('program_id', $iProgId);
        $this->db->update('rlb_program', $data);
    }

   public function getPumpDetails($sDeviceID)
   {
	   $query = $this->db->get_where('rlb_pump_device',array('pump_number'=>$sDeviceID));

       if($query->num_rows() > 0)
       {
            return $query->result();
       }

       return '';
   }
   
   public function getAllPumpDetails()
   {
	   $query = $this->db->get('rlb_pump_device');

       if($query->num_rows() > 0)
       {
            return $query->result();
       }

       return '';
   }
   

   public function savePumpDetails($aPost,$sDeviceID)
   {
        $sPumpClosure   = 	$aPost['sPumpClosure'];
        $sPumpType      =   $aPost['sPumpType'];
		$sRelayNumber   =   $aPost['sRelayNumber'];
		$sPumpAddress   =   '';
		$sPumpSubType   =   '';
        $sPumpSpeed     =   '';
        $sPumpFlow      =   '';
		$sRelayNumber1  =   '';
		
		if(preg_match('/Emulator/',$sPumpType))
		{
			$sPumpSubType   =   $aPost['sPumpSubType'];
		}
		
		if(preg_match('/Intellicom/',$sPumpType))
		{
			$sPumpSubType   =   '';
			$sPumpSpeed     =   $aPost['sPumpSpeedIn'];
		}
		
		
		if(preg_match('/Emulator/',$sPumpType) || preg_match('/Intellicom/',$sPumpType))
		{
			$sPumpAddress = $aPost['sPumpAddress'];
		}
		
		if($sPumpType == 'Emulator' || $sPumpType == 'Intellicom')
		{
			$sRelayNumber = '';
		}
		
        if($sPumpSubType == 'VS')
		{
            $sPumpSpeed     =   $aPost['sPumpSpeed'];
		}
        else if($sPumpSubType == 'VF')
		{
            $sPumpFlow      =   $aPost['sPumpFlow'];
		}
		
		if($sPumpType == '2Speed')
		{
			$sRelayNumber1  =   $aPost['sRelayNumber1'];
			$sPumpSubType   =   $aPost['sPumpSubType1'];
		}
        
        $this->db->select('pump_id');
        $query = $this->db->get_where('rlb_pump_device', array('pump_number' => $sDeviceID));

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aResult)
            {
                $data = array('pump_number'         => $sDeviceID,
                              'pump_type'           => $sPumpType,
							  'pump_sub_type'		=> $sPumpSubType,
                              'pump_speed'          => $sPumpSpeed,
                              'pump_flow'           => $sPumpFlow,
                              'pump_closure'        => $sPumpClosure,
							  'relay_number'		=> $sRelayNumber,
							  'pump_address'		=> $sPumpAddress,
                              'pump_modified_date'  => date('Y-m-d H:i:s'),
							  'relay_number_1'		=> $sRelayNumber1
                              );

                $this->db->where('pump_id', $aResult->pump_id);
                $this->db->update('rlb_pump_device', $data);
            }
        }
        else
        {
            $data = array('pump_number'         => $sDeviceID,
                          'pump_type'           => $sPumpType,
						  'pump_sub_type'		=> $sPumpSubType,
                          'pump_speed'          => $sPumpSpeed,
                          'pump_flow'           => $sPumpFlow,
                          'pump_closure'        => $sPumpClosure,
						  'relay_number'		=> $sRelayNumber,
						  'pump_address'		=> $sPumpAddress,
                          'pump_modified_date'  => date('Y-m-d H:i:s')   
                          );

            $this->db->insert('rlb_pump_device', $data);
        }    
   }
   
   public function savePositionName($sDeviceID,$sDevice,$sPositionName1,$sPositionName2)
   {
        $sSql   =   "SELECT device_id FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type ='".$sDevice."'";
        $query  =   $this->db->query($sSql);
        
        $aPositions = array('position1'=>$sPositionName1,'position2'=>$sPositionName2);
        
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
                $sSqlUpdate =   "UPDATE rlb_device SET device_position='".serialize($aPositions)."', last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id = ".$aRow->device_id;
                $this->db->query($sSqlUpdate);
            }
        }
        else
        {
            $sSqlInsert =   "INSERT INTO rlb_device(device_number,device_type,device_position,last_updated_date) VALUES('".$sDeviceID."','".$sDevice."','".serialize($aPositions)."','".date('Y-m-d H:i:s')."')";
            $this->db->query($sSqlInsert);
        }
   }
   
   public function getPositionName($sDeviceID,$sDevice)
    {
        $aPositionName = array('','');

        $sSql   =   "SELECT device_id,device_position FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
                $aTemp = unserialize($aRow->device_position);
                $aPositionName[0] = $aTemp['position1'];
                $aPositionName[1] = $aTemp['position2'];
            }
        }

        return $aPositionName;
    }
    
    public function saveDeviceTime($sDeviceID,$sDevice,$sDeviceTime) //START : Function to Save/UPDATE Device Time
    {
        //Check if device already present in the table.
        $sSql   =   "SELECT device_id FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type ='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0) //START: If device is already present in table.
        {
            foreach($query->result() as $aRow)
            {
                //Update the Time.
                $sSqlUpdate =   "UPDATE rlb_device SET device_total_time='".$sDeviceTime."', last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id = ".$aRow->device_id;
                $this->db->query($sSqlUpdate);
            }
        }//END: If device is already present in table.
        else //START: If device is not present in table.
        {
            //Insert Device with time.
            $sSqlInsert =   "INSERT INTO rlb_device(device_number,device_type,device_total_time,last_updated_date) VALUES('".$sDeviceID."','".$sDevice."','".$sDeviceTime."','".date('Y-m-d H:i:s')."')";
            $this->db->query($sSqlInsert);
        }
    }//END : Function to Save/UPDATE Device Time

    public function getDeviceTime($sDeviceID,$sDevice) //START : Function to get Device Time
    {
        $sDeviceTime = '';
        
        //Get saved time for the device.
        $sSql   =   "SELECT device_id,device_total_time FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
                $sDeviceTime = $aRow->device_total_time;
            }
        }

        return $sDeviceTime;
    } //End : Function to get Device Time
    
    public function updateDeviceRunTime($sDeviceID,$sDevice,$sStatus) //START: Function to update START Time and End Time Of Device
    {
        $sDeviceTime =$this->getDeviceTime($sDeviceID, $sDevice);
        
        if($sDeviceTime != '') // START : If user added the Time for Device.
        {
            $sDeviceStart =   date("H:i:s", time()); // Current Time as Start Time
            $aStartTime   =   explode(":",$sDeviceStart);
            
            //End Time Calculated using Start Time and Time of Device.
            $sDeviceEnd   =   mktime(($aStartTime[0]+ 0),($aStartTime[1]+$sDeviceTime),($aStartTime[2]+0),0,0,0);
            $sDeviceEnd   =   date("H:i:s", $sDeviceEnd);

            if($sStatus == '0') // IF Device is OFF
            {
                $data = array('device_start_time'  => '',
                            'device_end_time'    => ''
                            );
            }
            else // IF Device is ON
            {
                $data = array('device_start_time'  => $sDeviceStart,
                            'device_end_time'    => $sDeviceEnd
                            );
            }

            $this->db->where('device_number', $sDeviceID);
            $this->db->where('device_type', $sDevice);
            $this->db->update('rlb_device', $data);
        }// END : If user added the Time for Device.
    }//END: Function to update START Time and End Time Of Device
    
    public function getAllDeviceTimeDetails() //START : Function to get the Max Run Time for device.
    {
        $this->db->where('device_type', 'R');
        $this->db->where('device_total_time !=', '');
        
        $query = $this->db->get('rlb_device');

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
    }//START : Function to get the Max Run Time for device.
	
	
	public function getProgramCount($sDeviceNum,$sDevice)//START : Function to get the count of programs for device.
	{
		return $this->db
				->where('device_number', $sDeviceNum)
				->where('device_type', $sDevice)
				->where('program_delete', '0')
				->count_all_results('rlb_program');
	}//END : Function to get the count of programs for device.
	
	public function saveDevicePower($sDeviceID,$sDevice,$sPowerValue) //START : Function to Save/UPDATE Device Time
    {
        //Check if device already present in the table.
        $sSql   =   "SELECT device_id FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type ='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0) //START: If device is already present in table.
        {
            foreach($query->result() as $aRow)
            {
                //Update the Time.
                $sSqlUpdate =   "UPDATE rlb_device SET device_power_type='".$sPowerValue."', last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id = ".$aRow->device_id;
                $this->db->query($sSqlUpdate);
            }
        }//END: If device is already present in table.
        else //START: If device is not present in table.
        {
            //Insert Device with time.
            $sSqlInsert =   "INSERT INTO rlb_device(device_number,device_type,device_power_type,last_updated_date) VALUES('".$sDeviceID."','".$sDevice."','".$sPowerValue."','".date('Y-m-d H:i:s')."')";
            $this->db->query($sSqlInsert);
        }
    }//END : Function to Save/UPDATE Device Time
	
	public function saveDeviceMainType($sDeviceID,$sDevice,$sType) //START : Function to Save/UPDATE Device Main type
    {
        //Check if device already present in the table.
        $sSql   =   "SELECT device_id FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type ='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0) //START: If device is already present in table.
        {
            foreach($query->result() as $aRow)
            {
                //Update the Time.
                $sSqlUpdate =   "UPDATE rlb_device SET is_pool_or_spa='".$sType."', last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id = ".$aRow->device_id;
                $this->db->query($sSqlUpdate);
            }
        }//END: If device is already present in table.
        else //START: If device is not present in table.
        {
            //Insert Device with time.
            $sSqlInsert =   "INSERT INTO rlb_device(device_number,device_type,is_pool_or_spa,last_updated_date) VALUES('".$sDeviceID."','".$sDevice."','".$sType."','".date('Y-m-d H:i:s')."')";
            $this->db->query($sSqlInsert);
        }
    }//END : Function to Save/UPDATE Device Main type

    public function getDevicePower($sDeviceID,$sDevice) //START : Function to get Device Time
    {
        $sDevicePower = '';
        
        //Get saved time for the device.
        $sSql   =   "SELECT device_id,device_power_type FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
                $sDevicePower = $aRow->device_power_type;
            }
        }

        return $sDevicePower;
    } //End : Function to get Device Time
	
	public function getDeviceMainType($sDeviceID,$sDevice) //START : Function to get Device Time
    {
        $sDeviceMainType = '';
        
        //Get saved time for the device.
        $sSql   =   "SELECT device_id,is_pool_or_spa FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type='".$sDevice."'";
        $query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
                $sDeviceMainType = $aRow->is_pool_or_spa;
            }
        }

        return $sDeviceMainType;
    } //End : Function to get Device Time
	
	public function getPumpDetailsExcept($sDeviceID)
	{
		$sSql   =   "SELECT pump_number,pump_address,relay_number,pump_type FROM rlb_pump_device WHERE pump_number != ".$sDeviceID;
		$query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function getPumpDetailsFromRelayNumber($sDevice,$relayType)
	{
		$sSql   =   "SELECT pump_number FROM rlb_pump_device WHERE relay_number = '".$sDevice."' AND pump_type LIKE '%".$relayType."%'";
		$query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function saveProcess($line)
	{
		$sSql   =   "INSERT into test(res) VALUES('".$line."')";
		$query  =   $this->db->query($sSql);

        if($query->num_rows() > 0)
        {
            return $query->result();
        }
	}
	
	public function savePumpResponse($sResponse,$iPump)
	{
		$sSql   =   "INSERT INTO rlb_pump_response(pump_number,pump_response_time,pump_response) VALUES(".$iPump.",'".date('Y-m-d H:i:s')."','".$sResponse."')";
		$query  =   $this->db->query($sSql);
	}
	
	public function updateDeviceStauts($sName,$sDevice,$sStatus)
	{
		$sSql = "UPDATE rlb_pump_device SET status='".$sStatus."' WHERE pump_number='".$sName."'";
		$query  =   $this->db->query($sSql);
	}
	
	public function selectEmulatorOnPumps()
	{
		$aResult	=	array();
		$this->db->like('pump_type', 'Emulator'); 
        $this->db->where('status','1');
        $query = $this->db->get('rlb_pump_device');

        if($query->num_rows() > 0)
        {
			foreach($query->result() as $aRows)
			{
				$aResult[] = 'M'.$aRows->pump_number;
			}
        }
		

        return json_encode($aResult);
	}
	
	
}

/* End of file home_model.php */
/* Location: ./application/models/home_model.php */
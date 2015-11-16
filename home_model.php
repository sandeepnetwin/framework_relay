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
	
	public function getSettingsExtraDetails()
    {
        $sSql       =   "SELECT extra FROM rlb_setting";
        $query      =   $this->db->query($sSql);
       
        $aSettings  =   array();

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {    
                $aSettings[0]   = unserialize($aRow->extra);
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
				
				$aExtra['PumpsNumber'] 	= $aNumDevice['PumpsNumber'];
				$aExtra['ValveNumber'] 	= $aNumDevice['ValveNumber'];
				$aExtra['LightNumber'] 	= $aNumDevice['LightNumber'];
				$aExtra['HeaterNumber'] = $aNumDevice['HeaterNumber'];
				$aExtra['BlowerNumber'] = $aNumDevice['BlowerNumber'];
				$aExtra['Remote_Spa']  	= $showRemoteSpa;
				
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
	
	public function updateAbsProgramRunDetails($iProgId)
	{
		if($iProgId)
        {
            $data = array('program_absolute_start_time' => '','program_absolute_end_time'=>'','program_absolute_run_time'=>'');
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

		$time1Already      			= new DateTime($sProgramAbsStart);
        $time2Already      			= new DateTime($sProgramAbsEnd);
        $intervalAlready   			= $time2Already->diff($time1Already);
        $sProgramAbsAlreadyRun 		= $intervalAlready->format('%H:%I:%S');
		
        $time1      = new DateTime($sProgramAbsStart);
        $tempTime   = date('H:i:s',time());
        $time2      = new DateTime($tempTime);
        $interval   = $time2->diff($time1);
        $sTotalTime = $interval->format('%H:%I:%S');

        $time1      = new DateTime($sTotalTime);
        //$time2      = new DateTime($sProgramAbsTotal);
		$time2      = new DateTime($sProgramAbsAlreadyRun);
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
							  'relay_number_1'		=> $sRelayNumber1,
							  'status'				=> 0
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
                          'pump_modified_date'  => date('Y-m-d H:i:s'),
						  'status'				=> 0
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
	
	public function getValveRelayNumber($iDeviceID,$sDevice)
	{
		$arrValveRelayNumber	=	array();
		$this->db->select('valve_relay_number');
		$this->db->where('device_number', $iDeviceID); 
        $this->db->where('device_type', $sDevice);
        $query = $this->db->get('rlb_device');

        if($query->num_rows() > 0)
        {
			foreach($query->result() as $aRows)
			{
				$arrValveRelayNumber = unserialize($aRows->valve_relay_number);
			}
        }
        return json_encode($arrValveRelayNumber);
	}
	
	public function saveValveRelays($sDeviceID,$sDeviceIDold,$sDevice,$sRelay1,$sRelay2)
    {
        $sSql   =   "SELECT device_id FROM rlb_device WHERE device_number = ".$sDeviceID." AND device_type ='".$sDevice."'";
		$query  =   $this->db->query($sSql);
        
        $aRelays = array('Relay1'=>$sRelay1,'Relay2'=>$sRelay2);
        
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {
				
                $sSqlUpdate 	=   "UPDATE rlb_device SET valve_relay_number='".serialize($aRelays)."', last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id = ".$aRow->device_id;
                $this->db->query($sSqlUpdate);
            }
        }
        else
        {
            $sSqlInsert =   "INSERT INTO rlb_device(device_number,device_type,valve_relay_number,last_updated_date) VALUES('".$sDeviceID."','".$sDevice."','".serialize($aRelays)."','".date('Y-m-d H:i:s')."')";
			$this->db->query($sSqlInsert);
        }
		
		$sDeleteValve	=	"DELETE FROM rlb_device WHERE device_type ='".$sDevice."' AND valve_relay_number = ''";
		$this->db->query($sDeleteValve);
		
		if($sDeviceID != $sDeviceIDold)
		{
			$sDeleteOldValve	=	"DELETE FROM rlb_device WHERE device_type ='".$sDevice."' AND device_number = '".$sDeviceIDold."'";
			$this->db->query($sDeleteOldValve);
		}
    }
	
	public function getAllValvesHavingRelays()
	{
		$sSql   =   "SELECT * FROM rlb_device WHERE device_type ='V' AND valve_relay_number != ''";
        $query  =   $this->db->query($sSql);
        
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
		
		return '';
	}
	
	public function selectPumpsLatestResponse($iPumpID)
	{
		$sSql   =   "SELECT pump_response FROM rlb_pump_response WHERE pump_number ='".$iPumpID."' ORDER BY id DESC LIMIT 1";
		
        $query  =   $this->db->query($sSql);
        
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
			{
				return $row->pump_response;
			}
        }
		
		return '';
	}
	
	
	public function removeValveRelays($iValveNumber)
	{
		$sDeleteOldValve	=	"DELETE FROM rlb_device WHERE device_type ='V' AND device_number = '".$iValveNumber."'";
		$this->db->query($sDeleteOldValve);
	}
	
	public function removeAllValves()
	{
		$sDeleteOldValve	=	"DELETE FROM rlb_device WHERE device_type ='V'";
		$this->db->query($sDeleteOldValve);
	}
	
	
	public function getAllPumps()
	{
		$sSql   =   "SELECT * FROM rlb_pump_device";
        $query  =   $this->db->query($sSql);
        
        if($query->num_rows() > 0)
        {
            return $query->result();
        }
		
		return '';
	}
	
	public function removeAllPumps()
	{
		$sDeleteOldValve	=	"DELETE FROM rlb_pump_device";
		$this->db->query($sDeleteOldValve);
	}
	
	public function selectPumpsStatus($iPumpID)
	{
		$sSql   =   "SELECT status FROM rlb_pump_device WHERE pump_number ='".$iPumpID."'";
		
        $query  =   $this->db->query($sSql);
        
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
			{
				return $row->status;
			}
        }
		
		return '';
	}
	
	public function getPumpNumberFromRelayNumber($relayNumber, $sPumpSubType)
	{
		$sSql   =   "SELECT pump_number,relay_number,relay_number_1 FROM rlb_pump_device WHERE pump_type ='2Speed' AND pump_sub_type ='".$sPumpSubType."' AND (relay_number = '".$relayNumber."' || relay_number_1 = '".$relayNumber."')";
		
        $query  =   $this->db->query($sSql);
        
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
			{
				return array($row->pump_number,$row->relay_number,$row->relay_number_1);
			}
        }
		
		return '';
	}
	
	function updateValveCnt($totalValveCnt)
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
				
				$aExtra['ValveNumber'] = $totalValveCnt;
				
                $data = array('extra' => serialize($aExtra) );
                $this->db->where('id', $aRow->id);
                $this->db->update('rlb_setting', $data);
            }
        }
        else
        {
			$aExtra['ValveNumber'] = $totalValveCnt;
            $data = array('extra' => serialize($aExtra) );
            $this->db->insert('rlb_setting', $data);
        }
	}
	
	public function getCurrentPumpSpeed($pumpID)
	{
		$sPump	=	"";
		$sSql   =   "SELECT pump_speed FROM rlb_pump_device WHERE pump_number ='".$pumpID."'";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				$sPump	=	$aRow->pump_speed;
			}
		}
		
		return $sPump;
	}
	
	public function updatePumpSpeed($pumpID,$speed)
	{
		$sSql   =   "UPDATE rlb_pump_device SET pump_speed='".$speed."' WHERE pump_number ='".$pumpID."'";
        $query  =   $this->db->query($sSql);
	}
	
	public function savePoolSpaModeQuestions($arrDetails)
	{
		$sSql   =   "SELECT id FROM `rlb_mode_questions`";
        $query  =   $this->db->query($sSql);

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {  
				$data = array('general'     	=> serialize($arrDetails['General']),
							  'device'			=> serialize($arrDetails['Device']),
							  'heater'			=> serialize($arrDetails['Heater']),
							  'more'			=> serialize($arrDetails['More']),
							  'last_modified'	=> date('Y-m-d H:i:s')
						);
				$this->db->where('id',$aRow->id);	
				$this->db->update('rlb_mode_questions', $data);
			}
		}
		else
		{
			$data = array('general'     => serialize($arrDetails['General']),
						  'device'		=> serialize($arrDetails['Device']),
						  'heater'		=> serialize($arrDetails['Heater']),
						  'more'		=> serialize($arrDetails['More']),
                          'added_date'	=> date('Y-m-d H:i:s')
						);

            $this->db->insert('rlb_mode_questions', $data);
		}
	}
	
	public function getPoolSpaModeQuestions()
	{
		$arrDetails	=	array();
		$sSql   =   "SELECT id,general,device,heater,more FROM `rlb_mode_questions`";
                $query  =   $this->db->query($sSql);

                if ($query->num_rows() > 0)
                {
                    foreach($query->result() as $aRow)
                    {  
				$arrDetails['General']	=	$aRow->general;
				$arrDetails['Device']	=	$aRow->device;
				$arrDetails['Heater']	=	$aRow->heater;
				$arrDetails['More']		=	$aRow->more;
		    }
		}
		
		return $arrDetails;
	}
        
	public function saveLightRelay($sRelayNumber,$sDevice,$sDeviceId,$sRelayType)
	{
		$sSql   =   "SELECT device_id FROM rlb_device WHERE device_number = ".$sDeviceId." AND device_type ='".$sDevice."'";
		$query  =   $this->db->query($sSql);
		
		$arrLightDetails    =   array('sRelayType'=>$sRelayType,'sRelayNumber'=>$sRelayNumber);
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $aRow)
			{
				$sSqlUpdate =   "UPDATE rlb_device SET light_relay_number='".serialize($arrLightDetails)."', last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id = ".$aRow->device_id;
				$this->db->query($sSqlUpdate);
			}
		}
		else
		{
			$sSqlInsert =   "INSERT INTO rlb_device(device_number,device_type,light_relay_number,last_updated_date) VALUES('".$sDeviceId."','".$sDevice."','".serialize($arrLightDetails)."','".date('Y-m-d H:i:s')."')";
			$this->db->query($sSqlInsert);
		}
	}
	
	public function getLightDeviceExceptSelected($sDeviceId)
	{
		$sSql   =   "SELECT device_number,device_id,light_relay_number FROM rlb_device WHERE device_number != '".$sDeviceId."' AND device_type ='L'";
		$query  =   $this->db->query($sSql);
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		
		return '';
	}
	
	
	public function getLightDeviceDetails($sDeviceId)
	{
		$sSql   =   "SELECT device_number,device_id,light_relay_number FROM rlb_device WHERE device_number = '".$sDeviceId."' AND device_type ='L'";
		$query  =   $this->db->query($sSql);
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		
		return '';
	}
	
	public function getLightDevices()
	{
		$sSql   =   "SELECT device_number,device_id,light_relay_number FROM rlb_device WHERE  device_type ='L'";
		$query  =   $this->db->query($sSql);
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		
		return '';
	}
	
	public function getAllLightDeviceForType($sPumpType)
	{
		$arrDetails	=	array();
		$sSql   	=   "SELECT device_number,light_relay_number FROM rlb_device WHERE device_type = 'L' AND light_relay_number !=''";
		$query  =   $this->db->query($sSql);
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $rowResult)
			{
				$sRelayDetails	= unserialize($rowResult->light_relay_number);
				
				if(preg_match('/'.$sPumpType.'/',$sRelayDetails['sRelayType']))
				{
					$arrDetails[] = $rowResult;
				}
			}
		}
		
		return json_encode($arrDetails);
	}
	
	
	public function getHeaterDeviceDetails($sDeviceId)
	{
		$sSql   =   "SELECT device_number,device_id,light_relay_number FROM rlb_device WHERE device_number = '".$sDeviceId."' AND device_type ='H'";
		$query  =   $this->db->query($sSql);
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		
		return '';
	}
	
	public function getBlowerDeviceDetails($sDeviceId)
	{
		$sSql   =   "SELECT device_number,device_id,light_relay_number FROM rlb_device WHERE device_number = '".$sDeviceId."' AND device_type ='B'";
		$query  =   $this->db->query($sSql);
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
		
		return '';
	}
	
	public function getActiveModePoolSpa()
	{
		$sSql   =   "SELECT id FROM rlb_pool_spa_mode WHERE mode_status = '1'";
		$query  =   $this->db->query($sSql);

		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $rowResult)
			{
				return $rowResult->id; 
			}
		}

		return '';
	}
	
	public function UpdatePoolSpaMode($iMode)
	{
		//First Make all modes status 0
		$sSql	=	"UPDATE rlb_pool_spa_mode SET mode_status='0',total_run_time='',unique_id=''";
		$this->db->query($sSql);
		
		$unique_id	=	uniqid (rand(), true);
		$sSqlUpdate	=	"UPDATE rlb_pool_spa_mode SET mode_status = '1', last_start_date='".date('Y-m-d H:i:s')."',last_end_date='0000-00-00 00:00:00',unique_id='".$unique_id."' WHERE id=".$iMode;
		$this->db->query($sSqlUpdate);
	}
	
	public function getModePoolSpa()
	{
		$strMode =	'';	
		$sSql   =   "SELECT general FROM rlb_mode_questions WHERE id = '1'";
		$query  =   $this->db->query($sSql);

		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $rowResult)
			{
				$arrTemp	=	unserialize($rowResult->general);
				if(isset($arrTemp['type']) && $arrTemp['type'] != '')
				{
					$strMode =	ucfirst($arrTemp['type']);	
				}
			}
		}

		return $strMode;
	}
	
	//Get all absolute programs.
	public function getAllAbsoluteProgramDetails()
	{
		$strSelAbsProgram	=	"SELECT program_id,device_number,device_type FROM rlb_program WHERE program_absolute = '1' AND program_delete = '0'";
		$query  =   $this->db->query($strSelAbsProgram);

		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
		
		return '';
	}//Get all absolute programs.
	
	//Check if absolute program is running or not.
	public function chkAbsoluteProgramRunning($iProgramID)
	{
		$iActive			=	'';
		$strChkAbsProgram	=	"SELECT program_active FROM rlb_program WHERE program_id = '".$iProgramID."' AND program_delete = '0'";
		$query  =   $this->db->query($strChkAbsProgram);

		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $rowResult)
			{
				$iActive	=	$rowResult->program_active;
			}
		}
		
		return $iActive;
	}//Check if absolute program is running or not.
		
	public function removePump($iPumpNumber)
	{
		//First Delete Pump From the Device Table.
		$strDeletePump	=	"DELETE FROM rlb_device WHERE device_number='".$iPumpNumber."' AND device_type = 'PS'";
		$this->db->query($strDeletePump);
		
		//Delete Pump Details from pump_device table.
		$strDeletePumpDetails	=	"DELETE FROM rlb_pump_device WHERE pump_number='".$iPumpNumber."'";
		$this->db->query($strDeletePumpDetails);
		
		//Delete Response Stored for the Pump
		//$strDeletePumpResp	=	"DELETE FROM rlb_pump_response WHERE pump_number='".$iPumpNumber."'";
		//$this->db->query($strDeletePumpResp);
	}	
}

/* End of file home_model.php */
/* Location: ./application/models/home_model.php */
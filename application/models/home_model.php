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
       
        $aSettings  =   array(0=>'',1=>'');

        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $aRow)
            {    
                $aSettings[0]   = $aRow->ip_address;
                $aSettings[1]   = $aRow->port_no;  
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

    public function updateMode($imode)
    {
        $data = array('mode_status' => '0');
        $this->db->update('rlb_modes', $data);

        $data = array('mode_status' => '1');
        $this->db->where('mode_id',$imode);
        $this->db->update('rlb_modes', $data);
    }

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

    public function saveProgramDetails($aPost,$sDeviceID)
    {
        $sProgramName   =   $aPost['sProgramName'];
        $sProgramType   =   $aPost['sProgramType'];
        $sProgramDays   =   '0';

        if($sProgramType == '2')
        {
            $sProgramDays   =   $aPost['sProgramDays'];
            $sProgramDays   =   implode(',',$sProgramDays);
        }

        $sStartTime         =   $aPost['sStartTime'];
        $sEndTime           =   $aPost['sEndTime'];
        $bAbsoluteProgram   =   $aPost['isAbsoluteProgram'];

        $time1      = new DateTime($sStartTime);
        $time2      = new DateTime($sEndTime);
        $interval   = $time2->diff($time1);
        $sTotalTime = $interval->format('%H:%I:%S');

        $data = array('relay_prog_name' => $sProgramName, 
                      'relay_number'    => $sDeviceID, 
                      'relay_prog_type' => $sProgramType,
                      'relay_prog_days' => $sProgramDays,
                      'relay_start_time'=> $sStartTime,
                      'relay_end_time'  => $sEndTime,
                      'relay_prog_created_date'=> date('Y-m-d H:i:s'),
                      'relay_prog_absolute' => $bAbsoluteProgram,
                      'relay_prog_absolute_total_time'=>$sTotalTime
                      );
        $this->db->insert('rlb_relay_prog', $data);

    }

    public function updateProgramDetails($aPost,$sProgramID,$sDeviceID)
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

        $data = array('relay_prog_name' => $sProgramName, 
                      'relay_number'    => $sDeviceID,    
                      'relay_prog_type' => $sProgramType,
                      'relay_prog_days' => $sProgramDays,
                      'relay_start_time'=> $sStartTime,
                      'relay_end_time'  => $sEndTime,
                      'relay_prog_modified_date'=> date('Y-m-d H:i:s'),
                      'relay_prog_absolute' => $bAbsoluteProgram,
                      'relay_prog_absolute_total_time'=>$sTotalTime,
                      'relay_prog_absolute_start_time'=>$sAbsoluteStart,
                      'relay_prog_absolute_end_time'=>$sAbsoluteEnd
                      );
        $this->db->where('relay_prog_id', $sProgramID);
        $this->db->update('rlb_relay_prog', $data);
    }

    public function deleteProgramDetails($sProgramID)
    {
        $data = array('relay_prog_delete' => '1');
        $this->db->where('relay_prog_id', $sProgramID);
        $this->db->update('rlb_relay_prog', $data);   
    }

    function getProgramDetailsForDevice($sDeviceID)
    {

        $this->db->where('relay_number',$sDeviceID);
        $this->db->where('relay_prog_delete','0');
        $query = $this->db->get('rlb_relay_prog');

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
    }

    public function getProgramDetails($sProgramID)
    {

        $this->db->where('relay_prog_id',$sProgramID);
        $query = $this->db->get('rlb_relay_prog');

        if($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
    }

    public function getAllProgramsDetails()
    {
        $this->db->where('relay_prog_delete','0');
        //$this->db->where('relay_prog_id','7');
        $query = $this->db->get('rlb_relay_prog');

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
            $data = array('relay_prog_active' => $sStatus);
            $this->db->where('relay_prog_id', $iProgId);
            $this->db->update('rlb_relay_prog', $data);
        }
    }

    public function updateAbsProgramRun($iProgId,$sStatus)
    {
        if($iProgId)
        {
            $data = array('relay_prog_absolute_run' => $sStatus);
            $this->db->where('relay_prog_id', $iProgId);
            $this->db->update('rlb_relay_prog', $data);
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

            $data = array('relay_prog_absolute_start_time'  => $sProgramAbsStart,
                          'relay_prog_absolute_end_time'    => $sAbsoluteEnd,
                          'relay_prog_absolute_run_time'    => '',
                          'relay_prog_absolute_start_date'  => date('Y-m-d'),
                          'relay_prog_absolute_run'         => '0'
                          );

            $this->db->where('relay_prog_id', $iProgId);
            $this->db->update('rlb_relay_prog', $data);
        }
        else if(strtotime($sProgramAbsStartDay) == strtotime(date('Y-m-d')))
        {

            if($sProgramAbsAlreadyRun != '')
            {
                $aAlreadyRunTime     =   explode(":",$sProgramAbsAlreadyRun);
                $aTotalTime          =   explode(":",$sProgramAbsTotal);
                $sProgramAbsStart    =   date("H:i:s", time());
                $aStartTime       =   explode(":",$sProgramAbsStart);
                $sProgramAbsEnd   =   mktime(($aStartTime[0]+$aAlreadyRunTime[0]),($aStartTime[1]+$aAlreadyRunTime[1]),($aStartTime[2]+$aAlreadyRunTime[2]),0,0,0);
                $sAbsoluteEnd     =   date("H:i:s", $sProgramAbsEnd);

                $data = array(  'relay_prog_absolute_start_time'  => $sProgramAbsStart,
                                'relay_prog_absolute_end_time'    => $sAbsoluteEnd,
                                'relay_prog_absolute_run_time'    => '',
                                'relay_prog_absolute_start_date'  => date('Y-m-d'),
                                'relay_prog_absolute_run'         => $sProgramAbsRunStatus
                              );

                $this->db->where('relay_prog_id', $iProgId);
                $this->db->update('rlb_relay_prog', $data);
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

        $data = array('relay_prog_absolute_start_time'  => '',
                      'relay_prog_absolute_end_time'    => '',
                      'relay_prog_absolute_run_time'    => $sTotalTime,
                      'relay_prog_absolute_start_date'  => date('Y-m-d'),
                      'relay_prog_absolute_run'         => $sProgramAbsRunStatus
                      );

        $this->db->where('relay_prog_id', $iProgId);
        $this->db->update('rlb_relay_prog', $data);
    }

   public function getPumpDetails($sDeviceID)
   {
       if($sDeviceID == '')
           return '';
       $query = $this->db->get_where('rlb_pump_device',array('pump_number'=>$sDeviceID));

       if($query->num_rows() > 0)
       {
            return $query->result();
       }

       return '';
   }

   public function savePumpDetails($aPost,$sDeviceID)
   {
        $sPumpClosure   =   $aPost['sPumpClosure'];
        $sPumpType      =   $aPost['sPumpType'];
        $sPumpSpeed     =   '';
        $sPumpFlow      =   '';

        if($sPumpType == '2')
            $sPumpSpeed     =   $aPost['sPumpSpeed'];
        if($sPumpType == '3')
            $sPumpFlow      =   $aPost['sPumpFlow'];
        
        $this->db->select('pump_id');
        $query = $this->db->get_where('rlb_pump_device', array('pump_number' => $sDeviceID));

        if($query->num_rows() > 0)
        {
            foreach($query->result() as $aResult)
            {
                $data = array('pump_number'         => $sDeviceID,
                              'pump_type'           => $sPumpType,
                              'pump_speed'          => $sPumpSpeed,
                              'pump_flow'           => $sPumpFlow,
                              'pump_closure'        => $sPumpClosure,
                              'pump_modified_date'  => date('Y-m-d H:i:s')   
                              );

                $this->db->where('pump_id', $aResult->pump_id);
                $this->db->update('rlb_pump_device', $data);
            }
        }
        else
        {
            $data = array('pump_number'         => $sDeviceID,
                          'pump_type'           => $sPumpType,
                          'pump_speed'          => $sPumpSpeed,
                          'pump_flow'           => $sPumpFlow,
                          'pump_closure'        => $sPumpClosure,
                          'pump_modified_date'  => date('Y-m-d H:i:s')   
                          );

            $this->db->insert('rlb_pump_device', $data);
        }    
   }
}

/* End of file home_model.php */
/* Location: ./application/models/home_model.php */
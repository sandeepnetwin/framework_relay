<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Analog_model extends CI_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }
    
    public function saveAnalogDevice($sDeviceName)
    {
       foreach($sDeviceName as $key => $sDevice) 
       {
            if($sDevice != '')
            {
                $aDevice          = explode('_', $sDevice);
                $sDeviceDirection = '';

                if($aDevice[1] == 'V')
                {
                  $sDeviceDirection = $this->input->post('sValveType_'.$key);
                }

                $strCheckDevice = "SELECT analog_id FROM rlb_analog_device WHERE analog_input = '".$key."'";
                $query  =   $this->db->query($strCheckDevice);

                if ($query->num_rows() > 0)
                {
                    foreach($query->result() as $aResult)
                    {
                        $data = array('analog_input'  => $key,
                                      'analog_name'    => 'AP'.$key,
                                      'analog_device'    => $aDevice[0],
                                      'analog_device_type'  => $aDevice[1],
                                      'device_direction'  => $sDeviceDirection,
                                      'analog_device_modified_date' => date('Y-m-d H:i:s')
                                      );

                        $this->db->where('analog_id', $aResult->analog_id);
                        $this->db->update('rlb_analog_device', $data);                    
                    }
                }
                else
                {
                    $data = array('analog_input'  => $key,
                                      'analog_name'    => 'AP'.$key,
                                      'analog_device'    => $aDevice[0],
                                      'analog_device_type'  => $aDevice[1],
                                      'device_direction'  => $sDeviceDirection,
                                      'analog_device_modified_date' => date('Y-m-d H:i:s')
                                      );

                    $this->db->insert('rlb_analog_device', $data); 
                }
            }
            else
            {
                $strCheckDevice = "SELECT analog_id FROM rlb_analog_device WHERE analog_input = '".$key."'";
                $query  =   $this->db->query($strCheckDevice);

                if ($query->num_rows() > 0)
                {
                    foreach($query->result() as $aResult)
                    {
                        $data = array('analog_input'  => $key,
                                      'analog_name'    => 'AP'.$key,
                                      'analog_device'    => '',
                                      'analog_device_type'  => '',
                                      'device_direction'  => '',
                                      'analog_device_modified_date' => date('Y-m-d H:i:s')
                                      );

                        $this->db->where('analog_id', $aResult->analog_id);
                        $this->db->update('rlb_analog_device', $data);                    
                    }
                }
                else
                {
                    $data = array('analog_input'  => $key,
                                  'analog_name'    => 'AP'.$key,
                                  'analog_device'    => '',
                                  'analog_device_type'  => '',
                                  'device_direction'  => '',
                                  'analog_device_modified_date' => date('Y-m-d H:i:s')
                                  );

                    $this->db->insert('rlb_analog_device', $data);
                }   
            }

       }
   }

   public function getAllAnalogDevice()
   {
        $aDetails = array();

        $strCheckDevice = "SELECT * FROM rlb_analog_device";
        $query  =   $this->db->query($strCheckDevice);

        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $aResult)
            {
                if($aResult['analog_device'] != '')
                    $aDetails[] = $aResult['analog_device'].'_'.$aResult['analog_device_type'];
                else
                    $aDetails[] = '';
            }

        }

        return $aDetails;
   }

   public function getAllAnalogDeviceDirection()
   {
        $aDetails = array();

        $strCheckDevice = "SELECT * FROM rlb_analog_device";
        $query  =   $this->db->query($strCheckDevice);

        if ($query->num_rows() > 0)
        {
            foreach($query->result_array() as $aResult)
            {
                if($aResult['analog_device'] != '')
                    $aDetails[] = $aResult['device_direction'];
                else
                    $aDetails[] = '';
            }

        }

        return $aDetails;
   }
   
   public function saveBusNumber($iTempID,$busNumber)
   {
	   $strchk = "SELECT device_id FROM rlb_device WHERE device_type = 'T' AND device_number='".$iTempID."'";
	   $query  =   $this->db->query($strchk);

		if ($query->num_rows() > 0)
		{
			foreach($query->result_array() as $aResult)
			{
				$strUpdate = "UPDATE rlb_device SET light_relay_number='".$busNumber."',last_updated_date='".date('Y-m-d H:i:s')."' WHERE device_id=".$aResult['device_id'];
				$this->db->query($strUpdate);
			}
		}
		else
		{
			$sSqlInsert =   "INSERT INTO rlb_device(device_number,device_type,light_relay_number,last_updated_date) VALUES('".$iTempID."','T','".$busNumber."','".date('Y-m-d H:i:s')."')";
            $this->db->query($sSqlInsert);
		}
   }
   
   public function getPoolSpaModeDetails()
   {
	   $strGetModeDetails =	  "SELECT * FROM rlb_mode_questions";
	   $query  			  =   $this->db->query($strGetModeDetails);

		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
		
		return '';
   }
   
   public function checkPoolSpaModeOn()
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
   
   public function getPoolSpaModeUnique()
   {
	   $sSql   =   "SELECT unique_id FROM rlb_pool_spa_mode WHERE mode_status = '1'";
	   $query  =   $this->db->query($sSql);

	   if ($query->num_rows() > 0)
	   {
			foreach($query->result() as $rowResult)
			{
				return $rowResult->unique_id; 
			}
	   }

		return '';
   }
   
   public function getLastCompleteDevice($iMode,$unique_id)
   {
	   $sSql   =   "SELECT device FROM rlb_pool_spa_log WHERE mode_id = '".$iMode."' AND unique_id = '".$unique_id."' ORDER BY id DESC LIMIT 1";
	   $query  =   $this->db->query($sSql);

	   if ($query->num_rows() > 0)
	   {
			foreach($query->result() as $rowResult)
			{
				return $rowResult->device; 
			}
	   }

		return '';
   }
   
   public function getCompleteDeviceOfType($iMode,$unique_id,$deviceType)
   {
	   $sSql   =   "SELECT count(id) as deviceTotal FROM rlb_pool_spa_log WHERE mode_id = '".$iMode."' AND unique_id = '".$unique_id."' AND device_type='".$deviceType."'";
	   $query  =   $this->db->query($sSql);

	   if ($query->num_rows() > 0)
	   {
			foreach($query->result() as $rowResult)
			{
				return $rowResult->deviceTotal; 
			}
	   }

		return '';
   }
   
   public function getPoolCurrentDevice($iMode,$unique_id)
   {
	   $sSql   =   "SELECT id,current_on_device,device_type,device_number,current_on_time,current_off_time,current_sequence FROM rlb_pool_spa_current WHERE mode_id = '".$iMode."' AND current_unique_id = '".$unique_id."' AND current_device_complete = '0'";
	   $query  =   $this->db->query($sSql);

	   if ($query->num_rows() > 0)
	   {
			return $query->result();
	   }

	   return '';
   }
   
   public function saveCurrentRunningDevice($iMode,$unique_id,$device,$sRunTime,$deviceType,$deviceNumber,$currentSequence)
   {
	   $sProgramAbsStart =   date("H:i:s", time());
	   //$sProgramAbsStart =   '23:55:00';
	   $aStartTime       =   explode(":",$sProgramAbsStart);
	   $sProgramAbsEnd   =   mktime(($aStartTime[0]),($aStartTime[1]+(int)$sRunTime),($aStartTime[2]),date('m'),date('d'),date('Y'));
	   
	   $startTime     	=   date('Y-m-d').' '.$sProgramAbsStart;
	   $endTime     	=   date("Y-m-d H:i:s", $sProgramAbsEnd);
	   
	  $sqlInsertCurrentDevice	=	"INSERT INTO rlb_pool_spa_current(mode_id,current_on_device,device_type,device_number,current_on_time,current_off_time,	current_unique_id, current_sequence) VALUES('".$iMode."','".$device."','".$deviceType."','".$deviceNumber."','".$startTime."','".$endTime."','".$unique_id."','".$currentSequence."')";
	  $this->db->query($sqlInsertCurrentDevice);
   }
   
   public function getStopTimeofCurrentDevice($iMode,$unique_id,$device)
   {
	   $sSql   =   "SELECT current_off_time FROM rlb_pool_spa_current WHERE mode_id = '".$iMode."' AND current_unique_id = '".$unique_id."' AND current_on_device = '".$device."'";
	   $query  =   $this->db->query($sSql);

	   if ($query->num_rows() > 0)
	   {
			foreach($query->result() as $rowResult)
			{
				return $rowResult->current_off_time; 
			}
	   }

	   return '';
   }
   
   public function saveEntryInLog($iMode,$unique_id,$deviceType)
   {
	   $sSql   =   "SELECT * FROM rlb_pool_spa_current WHERE mode_id = '".$iMode."' AND current_unique_id = '".$unique_id."' AND device_type = '".$deviceType."'";
	   $query  =   $this->db->query($sSql);

	   if ($query->num_rows() > 0)
	   {
			foreach($query->result() as $rowResult)
			{
				$strInsertInLog = "INSERT INTO rlb_pool_spa_log(mode_id,unique_id,device,device_type,device_number,device_start,device_stop,device_complete_run,current_sequence) VALUES('".$iMode."','".$unique_id."','".$rowResult->current_on_device."','".$rowResult->device_type."','".$rowResult->device_number."','".$rowResult->current_on_time."','".$rowResult->current_off_time."','1','".$rowResult->current_sequence."')";
				
				$this->db->query($strInsertInLog);
			}
	   }
   }
   
   public function deleteEntryFromCurrent($iMode,$unique_id)
   {
	   $sSql   =   "DELETE FROM rlb_pool_spa_current WHERE mode_id = '".$iMode."' AND current_unique_id = '".$unique_id."'";
	   $query  =   $this->db->query($sSql);
   }
   
   public function stopCurrentPoolSpaMode($iMode)
   {
	   $sSql   =	"UPDATE rlb_pool_spa_mode SET mode_status = '0', last_end_date='".date('Y-m-d H:i:s')."', unique_id = '' WHERE id=".$iMode;
	   $this->db->query($sSql);
   }
   
   public function getBusNumber($iTempID)
   {
	    $strBusNumber	 =	'';
	    $strGetBusNumber = "SELECT light_relay_number FROM rlb_device WHERE device_type = 'T' AND device_number='".$iTempID."'";
		
		$query  =   $this->db->query($strGetBusNumber);
		
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $rowResult)
			{
				$strBusNumber	=	$rowResult->light_relay_number;
			}
		}
		
		return $strBusNumber;
   }
}
?>

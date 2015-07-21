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
}
?>

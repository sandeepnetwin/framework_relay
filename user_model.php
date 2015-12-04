<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_model extends CI_Model 
{
    public function __construct() 
    {
        parent::__construct();
    }
	
	//Get all Users added by the Super Admin
    public function getAllUsers($userID)
    {
		$sSql       =   "SELECT * FROM rlb_admin_users WHERE parent_id =".$userID." AND block='0'";
        $query      =   $this->db->query($sSql);
       
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
    }
	
	public function getUserDetails($userID)
	{
		$sSql       =   "SELECT * FROM rlb_admin_users WHERE id =".$userID;
        $query      =   $this->db->query($sSql);
       
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function saveUser($userID,$aPost)
	{
		 $data = array('username' 	=> $aPost['sUserUsername'], 
					   'email' 		=> $aPost['sUserEmail'],
					   'password' 	=> base64_encode($aPost['sUserPassword']),
					   'block' 		=> $aPost['sUserActive'],
					   'user_type'	=> 'A',
					   'name'		=> $aPost['sUserName'],
					   'created_date'=> date('Y-m-d H:i:s'),
					   'parent_id'	=>$userID);
         $this->db->insert('rlb_admin_users', $data);
	}
	
	public function updateUser($userID,$aPost)
	{
		$data = array('username' 	=> $aPost['sUserUsername'], 
					   'email' 		=> $aPost['sUserEmail'],
					   'password' 	=> base64_encode($aPost['sUserPassword']),
					   'block' 		=> $aPost['sUserActive'],
					   'user_type'	=> 'A',
					   'name'		=> $aPost['sUserName']
					   );
		$this->db->where('id', $userID);			   
        $this->db->update('rlb_admin_users', $data);
	}
	
	public function deleteUser($userID)
	{
		$sSql       =   "DELETE FROM rlb_admin_users WHERE id =".$userID;
        $query      =   $this->db->query($sSql);
	}
	
	public function getAllModules()
	{
		$sSql       =   "SELECT * FROM rlb_site_modules";
        $query      =   $this->db->query($sSql);
       
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function getAllModulesActive()
	{
		$sSql       =   "SELECT * FROM rlb_site_modules WHERE module_active='1'";
        $query      =   $this->db->query($sSql);
       
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function getModuleDetails($moduleID)
	{
		$sSql       =   "SELECT * FROM rlb_site_modules WHERE id =".$moduleID;
        $query      =   $this->db->query($sSql);
       
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function saveModule($aPost)
	{
		 $data = array('module_name' 	=> $aPost['sModuleName'], 
					   'module_active' 		=> $aPost['sModuleActive']
					   );
         $this->db->insert('rlb_site_modules', $data);
	}
	
	public function updateModule($moduleID,$aPost)
	{
		$data = array('module_name' 	=> $aPost['sModuleName'], 
					   'module_active' 		=> $aPost['sModuleActive']
					   );
		$this->db->where('id', $moduleID);			   
        $this->db->update('rlb_site_modules', $data);
	}
	
	public function deleteModule($moduleID)
	{
		$sSql       =   "DELETE FROM rlb_site_modules WHERE id =".$moduleID;
        $query      =   $this->db->query($sSql);
	}
	
	public function getAllPositions()
	{
		$query	=	$this->db->get('rlb_position');
		if ($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function getPositionDetails($iPositionID)
	{
		$query = $this->db->get_where('rlb_position', array('id' => $iPositionID));
		
		if ($query->num_rows() > 0)
        {
            return $query->result();
        }

        return '';
	}
	
	public function savePosition($aPost)
	{
		$data	=	array('position_name'		=>	$aPost['sPositionName'],
						  'position_active'		=>	$aPost['sPositionActive'],
						  'position_added_date' =>	date('Y-m-d H:i:s')
						);
		$this->db->insert('rlb_position',$data);				
	}
	
	public function updatePosition($positionID,$aPost)
	{
		$data = array('position_name' 		=> $aPost['sPositionName'], 
					  'position_active' 	=> $aPost['sPositionActive']
					 );
		$this->db->where('id', $positionID);			   
        $this->db->update('rlb_position', $data);
	}
	
	public function deletePosition($positionID)
	{
		$sSql       =   "DELETE FROM rlb_position WHERE id =".$positionID;
        $query      =   $this->db->query($sSql);
	}
    
}

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */
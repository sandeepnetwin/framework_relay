<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('common_functions');
		
    }
	
	public function index()
	{		
	}
	
    public function login() 
	{
		if ($this->session->userdata('is_admin_login')) 
        {
			redirect('home');
        } 
        else 
        {
			$err['error'] = '';
			$this->session->sess_destroy();
            $this->load->view('Login',$err);
        }
    }

     public function do_login() 
	 {
        if ($this->session->userdata('is_admin_login'))
		{
            redirect('home');
        } 
		else
		{
            $user 		= $_POST['username'];
            $password 	= $_POST['password'];
			$signup		= $_POST['remember'];
			
			$err['error'] = '';
			
	        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

            if ($this->form_validation->run() == FALSE) 
			{
                $this->load->view('Login');
            } 
			else 
			{
                $enc_pass  = base64_encode($password);
                $sql = "SELECT * FROM rlb_admin_users WHERE username = ? AND password = ?";
                $val = $this->db->query($sql,array($user ,$enc_pass ));
				
                if ($val->num_rows())
				{
                    foreach ($val->result_array() as $recs => $res) {
                        $this->session->set_userdata(array(
                            'id' => $res['id'],
                            'username' => $res['name'],
                            'email' => $res['email'],                            
                            'is_admin_login' => true,
                            'user_type' => $res['user_type']
                            )
                        );
                    }
					
					if($signup == '1')	
					{	
						/* expire in 1 hour */
						setcookie("username", $user, time()+(60*60*1));
						setcookie("password", $password, time()+(60*60*1));  
					}
					else
					{
						setcookie("username", $username, time()-1);
						setcookie("password", $password, time()-1);
					}
					
					$sqlUpdate = "UPDATE rlb_admin_users SET last_login = '".date('Y-m-d H:i:s')."' WHERE id =".$res['id'];
					$this->db->query($sqlUpdate);
					
					
					//redirect('home');
					header('Location: '.base_url('home'));
                } 
				else 
				{
                    $err['error'] = '<strong>Access Denied</strong> Invalid Username/Password.';
                    $this->load->view('Login', $err);
                }
            }
        }
    }

        
    public function logout() 
	{
		/* $this->session->unset_userdata('id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('user_type');
        $this->session->unset_userdata('is_admin_login');  
		
		$this->session->sess_destroy();
       
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		
		redirect(base_url('dashboard/login')); */
		//$this->load->view('Logout');
		
		$this->session->unset_userdata('id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('user_type');
        $this->session->unset_userdata('is_admin_login');   
        $this->session->sess_destroy();
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache"); 
		//redirect(base_url('dashboard/login'), 'refresh');
		
		//$this->load->view('Logout');
		header('Location: '.base_url('dashboard/login'));
		//$this->index();

    }
    
	public function users()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else	
		{	
			$aParameter['msg'] = '';
			$aParameter['err'] = '';
			$aParameter['Title'] = 'User List';
			if($this->session->flashdata('success_msg_add') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_add');
			if($this->session->flashdata('success_msg_edit') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_edit');
			if($this->session->flashdata('success_msg_delete') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_delete');
			
			if($this->session->flashdata('msg_error') != '')
				$aParameter['err'] = $this->session->flashdata('msg_error');
			
			$this->load->model('user_model');
			$aParameter['allUser'] = $this->user_model->getAllUsers($this->session->userdata('id'));
			$this->template->build('ManageUser',$aParameter);
		}
	}
	
	public function userAddEdit()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$aParameter['userDetails']	=	'';
			$aParameter['Title']		=	'ADD/EDIT User';
			$this->load->model('user_model');
			$editUserID  =   base64_decode($this->uri->segment('3')); 
			
			if($this->input->post('command') == 'Save User') // START : IF Setting details are posted.
			{
				$this->user_model->saveUser($this->session->userdata('id'),$this->input->post());
				$this->session->set_flashdata('success_msg_add', 'User added Successfully!');
				redirect('dashboard/users');
			}
			
			if($this->input->post('command') == 'Update User') // START : IF Setting details are posted.
			{
				$userID	=	$this->input->post('userID');
				if($userID == '')
				{
					$this->session->set_flashdata('msg_error', 'Please Select User First!');
					redirect('dashboard/users');
				}
				else
				{
					$this->user_model->updateUser($userID,$this->input->post());
					$this->session->set_flashdata('success_msg_edit', 'User updated Successfully!');
					redirect('dashboard/users');
				}
			}
			
			
			if($editUserID != '')
			{
				$aParameter['userDetails'] = $this->user_model->getUserDetails($editUserID);
			}
			$this->template->build('User',$aParameter);
		}
	}
	
	public function userDelete()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$this->load->model('user_model');
			$deleteUserID  =   base64_decode($this->uri->segment('3')); 
			if($deleteUserID == '')
			{
				$this->session->set_flashdata('msg_error', 'Please Select User First!');
				redirect('dashboard/users');
			}
			else
			{
				$this->user_model->deleteUser($deleteUserID);
				$this->session->set_flashdata('success_msg_delete', 'User deleted Successfully!');
				redirect('dashboard/users');
			}
		}
	}
	
	public function module()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else	
		{	
			$aParameter['msg'] = '';
			$aParameter['err'] = '';
			$aParameter['Title'] = 'Module List';
			if($this->session->flashdata('success_msg_add') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_add');
			if($this->session->flashdata('success_msg_edit') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_edit');
			if($this->session->flashdata('success_msg_delete') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_delete');
			
			if($this->session->flashdata('msg_error') != '')
				$aParameter['err'] = $this->session->flashdata('msg_error');
			
			$this->load->model('user_model');
			$aParameter['allModules'] = $this->user_model->getAllModules();
			$this->template->build('ManageModule',$aParameter);
		}
	}
	
	public function moduleAddEdit()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$aParameter['moduleDetails']	=	'';
			$aParameter['Title'] = 'ADD/EDIT Module';
			$this->load->model('user_model');
			$editModuleID  =   base64_decode($this->uri->segment('3')); 
			
			if($this->input->post('command') == 'Save Module') // START : IF Setting details are posted.
			{
				$this->user_model->saveModule($this->input->post());
				$this->session->set_flashdata('success_msg_add', 'New module added successfully.');
				redirect('dashboard/module');
			}
			
			if($this->input->post('command') == 'Update Module') // START : IF Setting details are posted.
			{
				$moduleID	=	$this->input->post('moduleID');
				if($moduleID == '')
				{
					$this->session->set_flashdata('msg_error', 'Please Select Module First!');
					redirect('dashboard/module');
				}
				else
				{
					$this->user_model->updateModule($moduleID,$this->input->post());
					$this->session->set_flashdata('success_msg_edit', 'Module updated successfully.');
					redirect('dashboard/module');
				}
				
			}
			
			
			if($editModuleID != '')
			{
				$aParameter['moduleDetails'] = $this->user_model->getModuleDetails($editModuleID);
			}
			$this->template->build('Module',$aParameter);
		}
	}
	
	public function moduleDelete()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$this->load->model('user_model');
			$deleteModuleID  =   base64_decode($this->uri->segment('3')); 
			if($deleteModuleID == '')
			{
				$this->session->set_flashdata('msg_error', 'Please Select Module First!');
				redirect('dashboard/module');
			}
			else
			{
				$this->user_model->deleteModule($deleteModuleID);
				$this->session->set_flashdata('success_msg_delete', 'Module deleted successfully.');
				redirect('dashboard/module');
			}
		}
	}
	
	public function position()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else	
		{	
			$aParameter['msg'] = '';
			$aParameter['err'] = '';
			$aParameter['Title'] = 'Position List';
			if($this->session->flashdata('success_msg_add') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_add');
			if($this->session->flashdata('success_msg_edit') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_edit');
			if($this->session->flashdata('success_msg_delete') != '')
				$aParameter['msg'] = $this->session->flashdata('success_msg_delete');
			
			if($this->session->flashdata('msg_error') != '')
				$aParameter['err'] = $this->session->flashdata('msg_error');
			
			$this->load->model('user_model');
			$aParameter['allPositions'] = $this->user_model->getAllPositions();
			$this->template->build('ManagePosition',$aParameter);
		}
	}
	
	public function positionAddEdit()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$aParameter['positionDetails']	=	'';
			$aParameter['Title'] = 'ADD/EDIT Position';
			$this->load->model('user_model');
			$editPositionID  =   base64_decode($this->uri->segment('3')); 
			
			if($this->input->post('command') == 'Save Position') // START : IF Setting details are posted.
			{
				$this->user_model->savePosition($this->input->post());
				$this->session->set_flashdata('success_msg_add', 'New Position added successfully.');
				redirect('dashboard/position');
			}
			
			if($this->input->post('command') == 'Update Position') // START : IF Setting details are posted.
			{
				$positionID	=	$this->input->post('positionID');
				if($positionID == '')
				{
					$this->session->set_flashdata('msg_error', 'Please Select Position First!');
					redirect('dashboard/position');
				}
				else
				{
					$this->user_model->updatePosition($positionID,$this->input->post());
					$this->session->set_flashdata('success_msg_edit', 'Position updated successfully.');
					redirect('dashboard/position');
				}
				
			}
			
			if($editPositionID != '')
			{
				$aParameter['positionDetails'] = $this->user_model->getPositionDetails($editPositionID);
			}
			$this->template->build('Position',$aParameter);
		}
	}
	
	public function positionDelete()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$this->load->model('user_model');
			$deletePositionID  =   base64_decode($this->uri->segment('3')); 
			if($deletePositionID == '')
			{
				$this->session->set_flashdata('msg_error', 'Please Select Position First!');
				redirect('dashboard/position');
			}
			else
			{
				$this->user_model->deletePosition($deletePositionID);
				$this->session->set_flashdata('success_msg_delete', 'Position deleted successfully.');
				redirect('dashboard/position');
			}
		}
	}
	
	public function accessCheck()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$this->load->model('user_model');
			$userID  =   base64_decode($this->uri->segment('3')); 
			if($userID == '')
			{
				$this->session->set_flashdata('msg_error', 'Please Select User First!');
				redirect('dashboard/users');
			}
			else
			{
				$this->load->model('access_model');
				//Get User Information
				$aUserDetails = $this->user_model->getUserDetails($userID);
				
				//Get all active modules list first.
				$allActiveModules = $this->user_model->getAllModulesActive();	
				$aParameter['allActiveModules'] = $allActiveModules;
				$aParameter['userID'] 	= $userID;
				$aParameter['Title'] 	= 'View Permissions';
				$aParameter['userName'] = $aUserDetails[0]->name;
				
				$this->template->build('AccessView',$aParameter);
			}
		}
			
	}
	
	public function accessAddEdit()
	{
		if (!$this->session->userdata('is_admin_login'))
		{
			redirect('dashboard/login');
		}
		else
		{
			$this->load->model('user_model');
			$userID  =   base64_decode($this->uri->segment('3')); 
			if($userID == '')
			{
				$this->session->set_flashdata('msg_error', 'Please Select User First!');
				redirect('dashboard/users');
			}
			else
			{
				$this->load->model('user_model');
				$this->load->model('access_model');
				//Get all active modules list first.
				$allActiveModules = $this->user_model->getAllModulesActive();	
				
				if($this->input->post('userID') != '')
				{
					$userID	=	$this->input->post('userID');
					foreach($allActiveModules as $Module)
					{
						$sPermission	=	$this->input->post('radioAccess_'.$Module->id);
						$this->access_model->changePermission($userID,$Module->id,$sPermission);
					}
					$this->session->set_flashdata('success_msg_add', 'Permissions changed Successfully!');
					redirect('dashboard/users');
				}
				
				//Get User Information
				$aUserDetails = $this->user_model->getUserDetails($userID);
				$aParameter['userName'] = $aUserDetails[0]->name;
				
				$aParameter['userID'] = $userID;
				$aParameter['allActiveModules'] = $allActiveModules;
				$aParameter['Title'] = 'Change Permissions';
				$this->template->build('Access',$aParameter);
			}
			
		}
	}
	
    public function install()
    {
        $aViewParameter['page']         =   'home';
        $aViewParameter['sucess']       =   '0';
        $aViewParameter['err_sucess']   =   '0';
        
        $sPage  =   $this->uri->segment('3'); 

        $this->load->model('home_model');
		
		$aViewParameter['page']         =   'setting';
		
		if($this->input->post('command') == 'Save Setting')
		{
			$iMode  =   $this->input->post('relay_mode');
			$sIP    =   $this->input->post('relay_ip_address');
			$sPort  =   $this->input->post('relay_port_no');

			if($sIP == '')
			{
				if(IP_ADDRESS){
					$sIP = IP_ADDRESS;
				}
			}
			
			//Check for Port Number constant
			if($sPort == '')
			{   
				if(PORT_NO){
					$sPort = PORT_NO;
				}
			}

			if($sIP == '' || $sPort == '')
			{
				$aViewParameter['err_sucess']    =   '1';
			}
			else
			{
				$this->home_model->updateSetting($sIP,$sPort);
				
				//Get whether to show temperature on Home page.
				$sPoolTemp  =   $this->input->post('showPoolTemp');
				$sSpaTemp  	=   $this->input->post('showSpaTemp');
				
				//Get the temperature address
				$sPoolTempAddress	=	'';
				$sSpaTempAddress	=	'';
				
				if($sPoolTemp == '1')
					$sPoolTempAddress   =   $this->input->post('selPoolTemp');
				if($sSpaTemp == '1')
					$sSpaTempAddress	=   $this->input->post('selSpaTemp');
			
				//Get the number of pumps and valve.
					$iNumPumps  =   $this->input->post('numPumps');
					$iNumValve  =   $this->input->post('numValve');
					$iNumLight  =   $this->input->post('numLight');
					
					$iNumHeater  =   $this->input->post('numHeater');
					$iNumBlower  =   $this->input->post('numBlower');
				
				$aNumDevice     =   array();
				if($iNumPumps != '')	
					$aNumDevice['PumpsNumber']     =   $iNumPumps;
				else
					$aNumDevice['PumpsNumber']     =   0;
				
				if($iNumValve != '')	
					$aNumDevice['ValveNumber']     =   $iNumValve;
				else
					$aNumDevice['ValveNumber']     =   0;
				
				if($iNumLight != '')	
					$aNumDevice['LightNumber']     =   $iNumLight;
				else
					$aNumDevice['LightNumber']     =   0;
				
				if($iNumHeater != '')	
					$aNumDevice['HeaterNumber']     =   $iNumHeater;
				else
					$aNumDevice['HeaterNumber']     =   0;
				
				if($iNumBlower != '')	
					$aNumDevice['BlowerNumber']     =   $iNumBlower;
				else
					$aNumDevice['BlowerNumber']     =   0;
				
				$showRemoteSpa = $this->input->post('showRemoteSpa');
				
				//Save Number of Devices and Spa Remote
				$this->home_model->updateSettingNumberDevice($aNumDevice,$showRemoteSpa);
				
				$aViewParameter['sucess']    =   '1';
			}
        }
            
        list($aViewParameter['sIP'],$aViewParameter['sPort'],$aViewParameter['extra']) = $this->home_model->getSettings();
		$this->load->view('Install',$aViewParameter);
    }
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
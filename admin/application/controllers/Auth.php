<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('registration_model');
	}

	public function index()
	{
			
			$data['data'] = $this->registration_model->getLastData();
			$data['agent_data']= $this->registration_model->getLastAgentData();
			$data['owner_data']= $this->registration_model->getLastOwnerData();
			$data['tenant_data']= $this->registration_model->getLastTenantData();
			$data['owner'] = $this->registration_model->countOwner();
			$data['agent'] = $this->registration_model->countAgent();
			$data['tenant'] = $this->registration_model->countTenant();
			$data['property'] = $this->registration_model->countProperty();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('home',$data);
			$this->load->view('common/home_footer');
	}
	 ///public function countProperty(){
	// 	//$datas['da'] = $this->registration_model->countOwner();
	// 	//$this->load->view('home');
	// 	//$this->load->view('register');
	 	//$this->registration_model->countProperty();
	// 	//echo "sds";
	// 	    //$this->load->view('common/home_header');
	// 		//$this->load->view('common/top_nav');
	// 		//$this->load->view('common/leftsidebar');
	// 		//$this->load->view('home',$data);
	// 		//$this->load->view('common/home_footer');
	// }


	public function login()
	{
		if(!empty($_POST))
		{
			$email= trim($this->input->post('email'));
			$password= trim($this->input->post('password'));
			$this->load->model('auth_model');
			$ResultData = $this->auth_model->processLogin($email,$password);
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email', 'required|callback_validateUser[' . count($ResultData) . ']');
			$this->form_validation->set_rules('password', 'Password', 'required');

		  $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		  $this->form_validation->set_message('required', 'Enter %s');

		  if ($this->form_validation->run() == FALSE) {
		   $this->load->view('common/header');
			$this->load->view('login');
			$this->load->view('common/footer');
		  }else{
		   if($ResultData){
			
			$user = array(
			 'id' => $ResultData[0]->assets_id,
			 'username' => $ResultData[0]->first_name,
			 'email' => $ResultData[0]->email,
			 'password' => $ResultData[0]->password,
			);

			$this->session->set_userdata($user);
			redirect('index');
		   }
		  }
		}else{
			$this->load->view('common/header');
			$this->load->view('login');
			$this->load->view('common/footer');
		}
	}

	public function register()
	{
		if(!empty($_POST))
		{
				$this->load->library('form_validation');

                $this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[12]|is_unique[user.username]',
				array(
					'required'      => 'You have not provided %s.',
					'is_unique'     => 'This %s already exists.'
				));
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[user.email]');
                $this->form_validation->set_rules('password', 'Password', 'required',
                        array('required' => 'You must provide a %s.')
                );
                $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
                

                if ($this->form_validation->run() == True)
                {
						$username = $this->input->post('username');
						$email = $this->input->post('email');
						$password = $this->input->post('password');
						$passconf = $this->input->post('passconf');
						
						$dataToInsert = array('username'=>$username,'email'=>$email,'password'=>$password,'passconf'=>$passconf);
						$this->load->model('auth_model');
						$InsertData = $this->auth_model->insertData($dataToInsert);
						redirect('/login');
						
						
                }else{
							$this->load->view('common/header');
							$this->load->view('register');
							$this->load->view('common/footer');
						}
		}else{
			$this->load->view('common/header');
			$this->load->view('register');
			$this->load->view('common/footer');
		}
		
	}
	
	public function validateUser($email,$recordCount){
	  if ($recordCount != 0){
	   return TRUE;
	  }else{
	   $this->form_validation->set_message('validateUser', 'Invalid %s or Password');
	   return FALSE;
	  }
	 }

	public function logout(){
		$this->session->unset_userdata('id');
		$this->session->sess_destroy();
		redirect('/login');
	}
}

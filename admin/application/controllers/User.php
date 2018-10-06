<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	
	public function __construct(){
		parent::__construct();
		$this->load->model('registration_model');
	}

	public function index()
	{
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('home');
			$this->load->view('common/home_footer');
	}
	
	// Owner Controller
	public function owner()
	{
		    $data['data'] = $this->registration_model->getOwner();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('owner_management',$data);
			$this->load->view('common/footer');
	}

	// Edit Owner Profile
	public function ownerProfile(){
		//$id = $this->input->post('id');
		$data['data'] = $this->registration_model->getOwnerData();
		$this->load->view('owner_profile',$data);
		//echo 'xsd';
	}

	// Update Owner Profile
	public function updateOwnerProfile(){
		$this->registration_model->updateOwnerProfile();
	}

    // Owner Status
	public function ownerStatus(){
		
		$this->registration_model->updateOwnerStatus();
		
	}

	

	// Delete Owner Profile
	public function delete_owner(){
		$id = $this->uri->segment(3);
		$deleteData = $this->registration_model->deleteOwnerData($id);
		redirect('/user/owner');	
	}
// --------------------------------------------------------------------  //
    // Agent controller
	public function agent()
	{		
			$data['data'] = $this->registration_model->getAgent();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('agent_management',$data);
			$this->load->view('common/footer');
	}
	
	// Edit Agent Profile
	public function agentProfile(){
		//$id = $this->input->post('id');
		$data['data'] = $this->registration_model->getOwnerData();
		$this->load->view('agent_profile',$data);
		//echo 'xsd';
	}

	// Update Agent Profile
	public function updateAgentProfile(){
		$this->registration_model->updateOwnerProfile();
	}

    // Agent Status
	public function agentStatus(){
		
		$this->registration_model->updateOwnerStatus();
		
	}

	// Delete Agent Profile
	public function delete_agent(){
		$id = $this->uri->segment(3);
		$deleteData = $this->registration_model->deleteOwnerData($id);
		redirect('/user/agent');	
	}

// ------------------------------------------------------------------- //

	// Tenent Controller
	public function tenant(){
			$data['data'] = $this->registration_model->getTenant();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('tenant_management',$data);
			$this->load->view('common/footer');
	 }

	 // Edit Tenent Profile
	public function tenentProfile(){
		//$id = $this->input->post('id');
		$data['data'] = $this->registration_model->getOwnerData();
		$this->load->view('agent_profile',$data);
		//echo 'xsd';
	}

	// Update Tenent Profile
	public function updateTenentProfile(){
		$this->registration_model->updateOwnerProfile();
	}

    // Tenent Status
	public function tenentStatus(){
		
		$this->registration_model->updateOwnerStatus();
		
	}

	// Delete Tenent Profile
	public function delete_tenent(){
		$id = $this->uri->segment(3);
		$deleteData = $this->registration_model->deleteOwnerData($id);
		redirect('/user/tenant');	
	}
	//Agent Review
	public function agent_review(){
		$data['review'] = $this->registration_model->agent_review();
		$this->load->view('common/home_header');
		$this->load->view('common/top_nav');
		$this->load->view('common/leftsidebar');
		$this->load->view('agent_review',$data);
		$this->load->view('common/footer');
	}
	
	
}

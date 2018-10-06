<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Property extends CI_Controller {

	
	public function __construct(){
		parent::__construct();
		$this->load->model('Property_model');
		//$this->
	}
	public function index()
	{
		    $data['data'] = $this->Property_model->getAllProperty();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('property_management',$data);
			$this->load->view('common/home_footer');
	}

	public function deleteProperty(){
		$id = $this->uri->segment(3);
		$deleteData = $this->Property_model->deleteProperty($id);
		redirect('/property');
	}

	public function editProperty(){
		$data['data'] = $this->Property_model->getAllProperty();
		$this->Property_model->editProperty();
		$this->load->view('property_edit',$data);
	}
	
	//public function propertyProfile(){
		//echo 'dsds';
	//}
	public function propertyProfile(){
		//$id = $this->input->post('id');
		$data['data'] = $this->Property_model->getPropertyProfile();
		$data['image'] = $this->Property_model->getPropertyImage();
		$this->load->view('property_profile',$data);
		//echo 'xsd';
	}
	public function editPropertyProfile(){
		 
		 $this->Property_model->editPropertyDetails();
		
	}
	public function editGeoLocation(){
		 
		 $this->Property_model->editGeoLocation();
		
	}

	public function propertyStatus(){
		$this->Property_model->propertyStatus();
		//echo $this->input->post('status');
	}
	
}

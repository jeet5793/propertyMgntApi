<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contactinfo extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	 public function __construct() {
        parent::__construct();
        $this->load->model('contactinfo_model');
    }
	
	public function index()
	{
			
			$data['data'] = $this->contactinfo_model->getData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('contactinfo_list',$data);
			$this->load->view('common/footer');
			
			
	}
	
	public function add()
	{
		
		if(!empty($_POST))
		{
			
			$address = $this->input->post('address');
			$phone = $this->input->post('phone');
			$mobile = $this->input->post('mobile');
			$fax = $this->input->post('fax');
			$email = $this->input->post('email');
			$status = $this->input->post('status');
			
			$dataToInsert = array(
				'address'=>$address,
				'phone'=>$phone,
				'mobile'=>$mobile,
				'fax'=>$fax,
				'email'=>$email,
				'status'=>$status
				);
				
				$InsertData = $this->contactinfo_model->insertData($dataToInsert);
						redirect('/contactinfo');
						
						
                
		}else{
			
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('contactinfo_add');
			$this->load->view('common/footer');
			
		}
			
	}
	
	public function edit()
	{
		$id = $this->uri->segment(3);
		if(!empty($_POST))
		{
			
			$address = $this->input->post('address');
			$phone = $this->input->post('phone');
			$mobile = $this->input->post('mobile');
			$fax = $this->input->post('fax');
			$email = $this->input->post('email');
			$status = $this->input->post('status');		
						
			$dataToUpdate = array(
				'address'=>$address,
				'phone'=>$phone,
				'mobile'=>$mobile,
				'fax'=>$fax,
				'email'=>$email,
				'status'=>$status);
						$updateData = $this->contactinfo_model->updateData($dataToUpdate,$id);
						redirect('/contactinfo');
				
		}else{
			$data['data'] = $this->contactinfo_model->getDataBy($id);
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('contactinfo_edit',$data);
			$this->load->view('common/footer');
		}
			
	}
	
	/* public function delete()
	{
		$id = $this->uri->segment(3);
		$deleteData = $this->contactinfo_model->deleteData($id);
		redirect('/contactinfo');	
	} */
	
	/*public function view()
	{
		$id = $this->uri->segment(3);
		$this->load->model('testimonial_model');
		$deleteData = $this->testimonial_model->getDataBy($id);
		redirect('testimonial', 'refresh');	
	}
	*/
	
	
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advertisment extends CI_Controller {

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
	public function index()
	{
			$this->load->model('advertisment_model');
			$data['data'] = $this->advertisment_model->getData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('advertisment_list',$data);
			$this->load->view('common/footer');
			
			
	}
	
	public function add()
	{
		
		if(!empty($_POST))
		{
			$this->load->library('form_validation');

                $this->form_validation->set_rules('name', 'Name', 'required',//|is_unique[user.username]
				array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
				$this->form_validation->set_rules('start_date', 'Start Date', 'required');
                $this->form_validation->set_rules('end_date', 'End Date', 'required');
                //$this->form_validation->set_rules('userfile', 'Image', 'required');
                

                if ($this->form_validation->run() == True)
                {
					
						  if(!empty($_FILES)) {
								if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
									$sourcePath = $_FILES['userfile']['tmp_name'];
									$targetPath = 'assets/uploads/'.$_FILES['userfile']['name'];
									move_uploaded_file($sourcePath,$targetPath);
								}
							}
						$name = $this->input->post('name');
						$start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
						$end_date = date('Y-m-d',strtotime($this->input->post('end_date')));
						$status = $this->input->post('status');
						$imagepath = $targetPath;
						
						$dataToInsert = array('name'=>$name,'start_date'=>$start_date,'end_date'=>$end_date,'status'=>$status,'img_path'=>$imagepath);
						$this->load->model('advertisment_model');
						$InsertData = $this->advertisment_model->insertData($dataToInsert);
						redirect('/advertisment');
						
						
                }else{
					
							$this->load->view('common/home_header');
							$this->load->view('common/top_nav');
							$this->load->view('common/leftsidebar');
							$this->load->view('advertisment_add');
							$this->load->view('common/footer');
						}
		}else{
			
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('advertisment_add');
			$this->load->view('common/footer');
			
		}
			
	}
	
	public function edit()
	{
		$id = $this->uri->segment(3);
		if(!empty($_POST))
		{
			$this->load->library('form_validation');

                 $this->form_validation->set_rules('name', 'Name', 'required',//|is_unique[user.username]
				array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
				$this->form_validation->set_rules('start_date', 'Start Date', 'required');
                $this->form_validation->set_rules('end_date', 'End Date', 'required');
                //$this->form_validation->set_rules('userfile', 'Image', 'required');
                

                if ($this->form_validation->run() == True)
                {
					
							if(!empty($_FILES)) {
								if(is_uploaded_file($_FILES['userfile']['tmp_name'])) {
									$sourcePath = $_FILES['userfile']['tmp_name'];
									$targetPath = 'assets/uploads/'.$_FILES['userfile']['name'];
									move_uploaded_file($sourcePath,$targetPath);
								} else{
								$targetPath = $this->input->post('userfile1');
								}
							} else{
								$targetPath = $this->input->post('userfile1');
							}
						$name = $this->input->post('name');
						$start_date = date('Y-m-d',strtotime($this->input->post('start_date')));
						$end_date = date('Y-m-d',strtotime($this->input->post('end_date')));
						$status = $this->input->post('status');
						$imagepath = $targetPath;
						
						$dataToUpdate = array('name'=>$name,'start_date'=>$start_date,'end_date'=>$end_date,'status'=>$status,'img_path'=>$imagepath);
						$this->load->model('advertisment_model');
						$updateData = $this->advertisment_model->updateData($dataToUpdate,$id);
						redirect('/advertisment');
						
						
                }else{
					
							$this->load->view('common/home_header');
							$this->load->view('common/top_nav');
							$this->load->view('common/leftsidebar');
							$this->load->view('advertisment_edit');
							$this->load->view('common/footer');
						}
		}else{
			
			$this->load->model('advertisment_model');
			$data['data'] = $this->advertisment_model->getDataBy($id);
			$this->load->view('advertisment_edit',$data);
		}
			
	}
	
	public function tdelete()
	{
		$id = $this->uri->segment(3);
		$this->load->model('advertisment_model');
		$deleteData = $this->advertisment_model->deleteData($id);
		redirect('/advertisment');	
	}
	
	/*public function view()
	{
		$id = $this->uri->segment(3);
		$this->load->model('testimonial_model');
		$deleteData = $this->testimonial_model->getDataBy($id);
		redirect('testimonial', 'refresh');	
	}
	*/
	
	
}

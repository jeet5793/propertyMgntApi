<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {

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
			$this->load->model('blog_model');
			$data['data'] = $this->blog_model->getData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('blog_list',$data);
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
				//$this->form_validation->set_rules('designation', 'Designation', 'required');
                $this->form_validation->set_rules('description', 'Description', 'required',
                        array('required' => 'You must provide a %s.')
                );
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
						//$designation = $this->input->post('designation');
						$description = $this->input->post('description');
						$status = $this->input->post('status');
						$imagepath = $targetPath;
						
						$dataToInsert = array('name'=>$name,'description'=>$description,'status'=>$status,'img_path'=>$imagepath);
						$this->load->model('blog_model');
						$InsertData = $this->blog_model->insertData($dataToInsert);
						
							/*$filename = 'blog.txt';
							$blogobj= $this->blog_model->getData();
							$data = $this->db->result_array($blogobj);
							//$data = 'My Text here';
							$serializeData = serialize(json_encode($data));
							if ( ! write_file('textfiles/'.$filename, $serializeData,'w+'))
							{
									echo 'Unable to write the file';
							}
							else
							{
									echo 'File written!';
							}*/
						redirect('/blog');
						
						
                }else{
					
							$this->load->view('common/home_header');
							$this->load->view('common/top_nav');
							$this->load->view('common/leftsidebar');
							$this->load->view('blog_add');
							$this->load->view('common/footer');
						}
		}else{
			
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('blog_add');
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
				//$this->form_validation->set_rules('designation', 'Designation', 'required');
                $this->form_validation->set_rules('description', 'Description', 'required',
                        array('required' => 'You must provide a %s.')
                );
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
						
						$description = $this->input->post('description');
						$status = $this->input->post('status');
						$imagepath = $targetPath;
						
						$dataToUpdate = array('name'=>$name,'description'=>$description,'status'=>$status,'img_path'=>$imagepath);
						$this->load->model('blog_model');
						$updateData = $this->blog_model->updateData($dataToUpdate,$id);
							/*$filename = 'blog.txt';
							$object= $this->blog_model->getData();
							function object_to_array($object) {
									return (array) $object;
								}
							//$data = 'My Text here';
							$serializeData = json_encode(serialize($data));
							if ( ! write_file('textfiles/'.$filename, $serializeData,'w+'))
							{
									echo 'Unable to write the file';
							}
							else
							{
									echo 'File written!';
							}*/
						redirect('/blog');
						
						
                }else{
							$this->load->model('blog_model');
							$data['data'] = $this->blog_model->getDataBy($id);
							$this->load->view('common/home_header');
							$this->load->view('common/top_nav');
							$this->load->view('common/leftsidebar');
							$this->load->view('blog_edit',$data);
							$this->load->view('common/footer');
						}
		}else{
			
			$this->load->model('blog_model');
			$data['data'] = $this->blog_model->getDataBy($id);
			$this->load->view('blog_edit',$data);
		}
			
	}
	
	public function tdelete()
	{
		$id = $this->uri->segment(3);
		$this->load->model('blog_model');
		$deleteData = $this->blog_model->deleteData($id);
		redirect('/blog');	
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

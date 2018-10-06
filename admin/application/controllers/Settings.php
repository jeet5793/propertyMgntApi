<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

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
			$this->load->view('common/home_header');
			$this->load->view('common/leftsidebar');
			$this->load->view('common/top_nav');
			$this->load->view('settings');
			$this->load->view('common/home_footer');
	}
	public function portal_content()
	{
			$this->load->view('common/home_header');
			$this->load->view('common/leftsidebar');
			$this->load->view('common/top_nav');
			$this->load->model('settings_model');
			$data['data'] = $this->settings_model->getPortalData();
			$this->load->view('portal_content_list',$data);
			$this->load->view('common/home_footer');
	}
	
	public function portal_content_add()
	{
		if(!empty($_POST))
		{
			$this->load->library('form_validation');

                $this->form_validation->set_rules('tag', 'Tag Name', 'required',//|is_unique[user.username]
				array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
				$this->form_validation->set_rules('description', 'Description', 'required');
                if ($this->form_validation->run() == True)
                {
						
						$tag = $this->input->post('tag');
						$description = $this->input->post('description');
						$status = $this->input->post('status');
						$dataToInsert = array(
							"tag"=>$tag,
							"description"=>$description,
							"status"=>$status
						);
						
						$this->load->model('settings_model');
						$InsertData = $this->settings_model->insertPortalData($dataToInsert);
						$filename = $tag.'.txt';
						$id= $this->db->insert_id();
							$data['data'] = $this->settings_model->getPortalDataBy($id);
							//$data = 'My Text here';
							$serializeData = json_encode($data);
							if ( ! write_file('textfiles/'.$filename, $serializeData,'w+'))
							{
									echo 'Unable to write the file';
							}
							else
							{
									echo 'File written!';
							}
						
						redirect('/portalcontent');
						
				}else
					{
						redirect('/portalcontent/add');
					}
		}else
			{
				$this->load->view('common/home_header');
				$this->load->view('common/leftsidebar');
				$this->load->view('common/top_nav');
				$this->load->view('portal_content_add');
				$this->load->view('common/home_footer');
			}
		
	}
	public function portal_content_edit()
	{
		$id = $this->uri->segment(3);
		if(!empty($_POST))
		{
			$tag = $this->input->post('tag');
						$description = $this->input->post('description');
						$status = $this->input->post('status');
						$dataToUpdate = array(
							"tag"=>$tag,
							"description"=>$description,
							"status"=>$status
						);
						
						$this->load->model('settings_model');
						$UpdateData = $this->settings_model->updatePortalData($dataToUpdate,$id);
							$filename = $tag.'.txt';
							$data= $this->settings_model->getPortalDataBy($id);
							//$data = 'My Text here';
							$serializeData = json_encode($data);
							if ( ! write_file('textfiles/'.$filename, $serializeData,'w+'))
							{
									echo 'Unable to write the file';
							}
							else
							{
									echo 'File written!';
							}
								
						redirect('/portalcontent');
						
				
		}else
		{
				$this->load->view('common/home_header');
				$this->load->view('common/leftsidebar');
				$this->load->view('common/top_nav');
				$this->load->model('settings_model');
				$data['data'] = $this->settings_model->getPortalDataBy($id);
				$this->load->view('portal_content_edit',$data);
				$this->load->view('common/home_footer');
		}
	}
	
	public function deletePortalData()
	{
		$id = $this->uri->segment(3);
		$this->load->model('settings_model');
		$deleteData = $this->settings_model->deletePortalData($id);
		redirect('/portalcontent');
	}
	public function change_password(){
		$old_pwd = $this->input->post('old_pwd');
		$new_pwd = $this->input->post('new_pwd');
		$confirm_pwd = $this->input->post('confirm_pwd');
		$this->load->model('settings_model');
		$result = $this->settings_model->check_pwd($old_pwd);
		if($result->cnt == 1){
			if($new_pwd == $confirm_pwd){
				$res = $this->settings_model->update_password($new_pwd);
				if($res)
					redirect(base_url().'settings?msg=success');
			}else{
				redirect(base_url().'settings?msg=mismatch');
			}
		}else{
			redirect(base_url().'settings?msg=invalid');
		}
	}
}

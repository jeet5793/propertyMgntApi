<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

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
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$id = $this->session->userdata('id');
			$query = $this->db->get_where('registration_tb',array('assets_id'=>$id));
			$result= $query->result_array();
			$data['data']=$result[0];
			
			$this->load->view('profile',$data);
			$this->load->view('common/footer');
	}
	public function profileupdate(){
		$id = $this->uri->segment(3);
		$Name = $this->input->post('name');
		$explode = explode(' ',$Name);
		$first_name = $explode[0];
		$last_name = $explode[1];
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$mobile_no = $this->input->post('mobile_no');
		$message = $this->input->post('message');
		$country = $this->input->post('country');
		$data = array(
			'first_name'=>$first_name,
			'last_name'=>$last_name,
			'email'=>$email,
			'password'=>$password,
			'mobile_no'=>$mobile_no,
			'about_us'=>$message,
			'country'=>$country
		);
		// print_r($data);
		// exit();
		$this->db->update('registration_tb',$data,array('assets_id'=>$id));
		redirect('profile');
	}
}

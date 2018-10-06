<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model {

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
	public function insertPortalData($dataToInsert)
	{
		$this->db->insert('portal_content_tb',$dataToInsert);
	}
	
	
	public function getPortalData(){
	  $query = $this->db->get('portal_content_tb');
	  return $query->result_array();
	 }
	 
	 public function updatePortalData($dataToUpdate,$id){
		$this->db->where('id', $id);
		$this->db->update('portal_content_tb', $dataToUpdate);
	 }
	 public function deletePortalData($id){
		$this->db->delete('portal_content_tb', array('id' => $id)); 
	 }
	 
	 public function getPortalDataBy($id){
	  $query = $this->db->get_where('portal_content_tb',array('id'=>$id));
	  return $query->result_array();
	 }
	 public function check_pwd($old_pwd){
		$this->db->select('count(*) as cnt');
		$this->db->where('assets_id',$this->session->userdata('id'));
		$this->db->where('password',$old_pwd);
		$query = $this->db->get('registration_tb');
		return $query->row();
	 }
	 public function update_password($new_pwd){
		$data['password'] = $new_pwd;
		$this->db->where('assets_id',$this->session->userdata('id'));
		return $this->db->update('registration_tb',$data);
	 }
}

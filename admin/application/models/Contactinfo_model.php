<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contactinfo_model extends CI_Model {

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
	public function insertData($dataToInsert)
	{
		$this->db->insert('contactinfo_tb',$dataToInsert);
	}
	
	public function getData(){
	  $this->db->select("id,address,phone,mobile,fax,email,status");
	  $this->db->from('contactinfo_tb');
	  $query = $this->db->get();
	  return $query->result();
	 }
	 
	 public function updateData($dataToUpdate,$id){
		$this->db->where('id', $id);
		$this->db->update('contactinfo_tb', $dataToUpdate);
	 }
	 /* public function deleteData($id){
		$this->db->delete('contactinfo_tb', array('id' => $id)); 
	 } */
	 
	 public function getDataBy($id){
	 $this->db->select("id,address,phone,mobile,fax,email,status");
	  $this->db->from('contactinfo_tb');
	  $this->db->where('id', $id);
	  $query = $this->db->get();
	  return $query->result_array();
	 }
}

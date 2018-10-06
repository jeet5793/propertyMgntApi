<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agreementform_model extends CI_Model {

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
	public function insertpropData($dataToInsert)
	{
		$this->db->insert('agreement_property_form_tb',$dataToInsert);
	}
	
	
	public function getpropData(){
	  $query = $this->db->get('agreement_property_form_tb');
	  return $query->result_array();
	 }
	 
	 public function updatepropData($dataToUpdate,$id){
		$this->db->where('id', $id);
		$this->db->update('agreement_property_form_tb', $dataToUpdate);
	 }
	 // public function deletepropData($id){
		// $this->db->delete('agreement_property_form_tb', array('id' => $id)); 
	 // }
	 
	 public function getPropDataBy($id){
		 $query = $this->db->get_where('agreement_property_form_tb',array('id' => $id)); 
	  return $query->result_array();
	 }
	 
	
	public function insertSignatureData($dataToInsert)
	{
		$this->db->insert('agreement_signature_form_tb',$dataToInsert);
	}
	
	
	public function getSignatureData(){
	  $query = $this->db->get('agreement_signature_form_tb');
	  return $query->result_array();
	 }
	 
	 public function updateSignatureData($dataToUpdate,$id){
		$this->db->where('id', $id);
		$this->db->update('agreement_signature_form_tb', $dataToUpdate);
	 }
	 // public function deletepropData($id){
		// $this->db->delete('agreement_property_form_tb', array('id' => $id)); 
	 // }
	 
	 public function getSignatureDataBy($id){
		 $query = $this->db->get_where('agreement_signature_form_tb',array('id' => $id)); 
	  return $query->result_array();
	 }
	 
	 
}

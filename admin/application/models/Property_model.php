<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Property_model extends CI_Model {

	
	public function getAllProperty(){
		$this->db->select("*");
	    $this->db->from('property_tb');
	    $query = $this->db->get();
	    return $query->result();
	}
	public function deleteProperty($id){
		$this->db->delete('property_tb', array('id' => $id)); 
	 }
	 public function editProperty(){
	 	$id = $this->input->post('hidden');
	 	$title = $this->input->post('title');
	 	$address = $this->input->post('address');
	 	$state = $this->input->post('state');
	 	$country = $this->input->post('country');
	 	$type = $this->input->post('type');
	 	$status = $this->input->post('status');
	 	$data = array('title'=>$title,'address'=>$address,'state'=>$state,'country'=>$country,'property_type'=>$type,'property_status'=>$status);
		$this->db->where('id', $id);
		$this->db->update('property_tb',$data);
	 }
	 public function getPropertyProfile(){
		$id = $this->input->post('id');
		$this->db->select("*");
		$this->db->where('id',$id);
	    $this->db->from('property_tb');
	    $query = $this->db->get();
	    return $query->result(); 
	}
	public function getPropertyImage(){
		$id = $this->input->post('id');
		$this->db->select("*");
		$this->db->where('property_id',$id);
	    $query = $this->db->get('property_photo_tb');
		$this->db->limit(1);
	    return $query->row();
	}
	public function editPropertyDetails(){
		 $id = $this->input->post('id');
		 $column = $this->input->post('column');
		
		$val = $this->input->post('editval');
		$data = array($column=>$val);
		$this->db->where('id', $id);
		if($this->db->update('property_tb',$data))
		{
			$result = array('error' => 1, 'msg' => 'Data Inserted Successfully');
		}
		else{
			$result = array('error' => 0, 'msg' => 'Data Error');
		}
		echo json_encode($result);
	}
	public function editGeoLocation(){
		 $id = $this->input->post('id');
		 $column = $this->input->post('column');
		$val = $this->input->post('editval');
		
		$data = array($column=>$val);
		$this->db->where('id', $id);
		if($this->db->update('property_tb',$data))
		{
			$result = array('error' => 1, 'msg' => 'Data Inserted Successfully');
		}
		else{
			$result = array('error' => 0, 'msg' => 'Data Error');
		}
		echo json_encode($result);
	}
	public function propertyStatus(){
		$status = $this->input->post('status');
		$id = $this->input->post('id');
		$value = array('status'=>$status);
		
		$this->db->where('id',$id);
		$this->db->update('property_tb',$value);
	}
}

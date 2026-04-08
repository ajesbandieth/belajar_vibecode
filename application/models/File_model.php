<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_model extends CI_Model {

    private $table = 'files';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_all($filters = array()) {
        $this->db->select('files.*, users.name as uploader_name, category.category_name');
        $this->db->from($this->table);
        $this->db->join('users', 'files.user_id = users.id');
        $this->db->join('category', 'files.category_id = category.id', 'left');
        
        if (!empty($filters['user_id'])) {
            $this->db->where('files.user_id', $filters['user_id']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->where('files.category_id', $filters['category_id']);
        }
        
        $this->db->order_by('files.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_by_id($id) {
        $this->db->select('files.*, users.name as uploader_name, category.category_name');
        $this->db->from($this->table);
        $this->db->join('users', 'files.user_id = users.id');
        $this->db->join('category', 'files.category_id = category.id', 'left');
        $this->db->where('files.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
}

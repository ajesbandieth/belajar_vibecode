<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all users
     * @return array
     */
    public function get_all_users() {
        $query = $this->db->get('users');
        return $query->result_array();
    }

    /**
     * Create a new user
     * @param array $data
     * @return bool
     */
    public function create_user($data) {
        return $this->db->insert('users', $data);
    }
}

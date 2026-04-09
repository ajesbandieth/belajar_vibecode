<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Session_model extends CI_Model {

    private $table = 'sessions';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_session($user_id) {
        // Clear previous sessions for this user to ensure only one active session
        $this->db->where('user_id', $user_id);
        $this->db->delete($this->table);

        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $data = array(
            'token' => $token,
            'user_id' => $user_id
        );
        $this->db->insert($this->table, $data);
        return $token;
    }

    public function get_by_token($token) {
        $query = $this->db->get_where($this->table, array('token' => $token));
        return $query->row_array();
    }

    public function delete_session($token) {
        $this->db->where('token', $token);
        return $this->db->delete($this->table);
    }
}

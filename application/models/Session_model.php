<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Session_model extends CI_Model {

    private $table = 'sessions';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_session($user_id) {
        // Clear previous sessions for this user to ensure fresh 1-hour start
        $this->db->where('user_id', $user_id);
        $this->db->delete($this->table);

        $token = bin2hex(openssl_random_pseudo_bytes(16));
        
        $this->db->set('token', $token);
        $this->db->set('user_id', $user_id);
        // Set expiry to 1 hour from now
        $this->db->set('expires_at', 'DATE_ADD(NOW(), INTERVAL 1 HOUR)', FALSE);
        
        $this->db->insert($this->table);
        return $token;
    }

    public function get_by_token($token) {
        $this->db->where('token', $token);
        $this->db->where('expires_at >', 'NOW()', FALSE);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    public function delete_session($token) {
        $this->db->where('token', $token);
        return $this->db->delete($this->table);
    }
}

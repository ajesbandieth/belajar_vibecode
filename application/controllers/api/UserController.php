<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Session_model');
    }

    public function login() {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!isset($input['email']) || empty($input['email']) || 
            !isset($input['password']) || empty($input['password'])) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Email dan password wajib diisi')));
        }

        $user = $this->User_model->get_by_email($input['email']);

        if (!$user || !password_verify($input['password'], $user['password'])) {
            return $this->output
                ->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Email atau password salah')));
        }

        $token = $this->Session_model->create_session($user['id']);

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $token)));
    }

    public function register() {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!isset($input['name']) || empty($input['name']) || 
            !isset($input['email']) || empty($input['email']) || 
            !isset($input['password']) || empty($input['password'])) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Parameter name, email, dan password wajib diisi')));
        }

        if ($this->User_model->is_email_registered($input['email'])) {
            return $this->output
                ->set_status_header(409)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Email sudah terdaftar')));
        }

        $data = array(
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => password_hash($input['password'], PASSWORD_BCRYPT)
        );

        $this->User_model->create($data);

        return $this->output
            ->set_status_header(201)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }

    public function index() {
        $users = $this->User_model->get_all();
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $users)));
    }

    public function show($id) {
        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'User tidak ditemukan')));
        }
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $user)));
    }

    public function update($id) {
        $input = json_decode($this->input->raw_input_stream, true);
        
        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'User tidak ditemukan')));
        }

        $update_data = array();
        if (isset($input['name'])) $update_data['name'] = $input['name'];
        
        // If updating email, verify uniqueness
        if (isset($input['email']) && $input['email'] !== $user['email']) {
            if ($this->User_model->is_email_registered($input['email'])) {
                return $this->output
                    ->set_status_header(409)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('data' => 'Email sudah terdaftar')));
            }
            $update_data['email'] = $input['email'];
        }

        if (isset($input['password'])) {
            $update_data['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
        }

        if (!empty($update_data)) {
            $this->User_model->update($id, $update_data);
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }

    public function destroy($id) {
        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'User tidak ditemukan')));
        }

        $this->User_model->delete($id);
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }

}

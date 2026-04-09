<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AppController extends CI_Controller {

    public function login() {
        $this->load->view('login');
    }

    public function register() {
        $this->load->view('register');
    }

    public function dashboard() {
        $this->load->view('dashboard');
    }

    public function categories() {
        $this->load->view('categories');
    }

    public function photos() {
        $this->load->view('photos');
    }

    public function photo_category($category_id) {
        $data['category_id'] = $category_id;
        $this->load->view('photo_category', $data);
    }

    public function videos() {
        $this->load->view('videos');
    }

    public function logout() {
        // We'll handle logout mainly on client side by clearing localStorage,
        // but this endpoint can be used to redirect.
        $this->load->view('login');
    }
}

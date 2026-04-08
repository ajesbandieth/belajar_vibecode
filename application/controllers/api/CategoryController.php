<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
    }

    private function generate_slug($string) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug;
    }

    public function register() {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!isset($input['category_name']) || empty($input['category_name'])) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Parameter category_name wajib diisi')));
        }

        $slug = $this->generate_slug($input['category_name']);

        $data = array(
            'category_name' => $input['category_name'],
            'slug'          => $slug
        );

        $this->Category_model->create($data);

        return $this->output
            ->set_status_header(201)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }

    public function index() {
        $categories = $this->Category_model->get_all();
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $categories)));
    }

    public function show($id) {
        $category = $this->Category_model->get_by_id($id);
        if (!$category) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Kategori tidak ditemukan')));
        }
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $category)));
    }

    public function update($id) {
        $input = json_decode($this->input->raw_input_stream, true);
        
        $category = $this->Category_model->get_by_id($id);
        if (!$category) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Kategori tidak ditemukan')));
        }

        $update_data = array();
        
        if (isset($input['category_name']) && !empty($input['category_name'])) {
            $update_data['category_name'] = $input['category_name'];
            $update_data['slug'] = $this->generate_slug($input['category_name']);
        }

        if (!empty($update_data)) {
            $this->Category_model->update($id, $update_data);
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }

    public function destroy($id) {
        $category = $this->Category_model->get_by_id($id);
        if (!$category) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Kategori tidak ditemukan')));
        }

        $this->Category_model->delete($id);
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }
}

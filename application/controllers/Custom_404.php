<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Custom_404 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper(array('url','libs'));
    }

    public function index()
    {
        
        $this->output->set_status_header('404');
        $this->load->view('custom_404');//loading in custom error view
        compress_output();
    }
}

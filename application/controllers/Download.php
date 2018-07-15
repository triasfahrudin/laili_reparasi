<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->helper('url');
        $this->load->library('session');

    }

    public function index()
    {
        //$this->session->sess_destroy();
        //redirect(site_url('signin'), 'reload');
        $url = '<a href="' . site_url('uploads/order_reparasi-debug.apk') . '">Download APK</a><br>';
        $url .= '<a href="' . site_url('uploads/order_reparasi-debug.apk') . '">Download Source Code</a><br>';
    }


}

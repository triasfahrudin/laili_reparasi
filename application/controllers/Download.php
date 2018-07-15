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
        $url = '<a target="_BLANK" href="https://github.com/triasfahrudin/laili_reparasi">Download (cari kalimat "Clone or Download")</a><br>';

        echo $url;
    }


}

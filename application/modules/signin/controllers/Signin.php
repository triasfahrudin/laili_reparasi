<?php

defined('BASEPATH') or exit('No direct script access allowed');


class Signin extends CI_Controller
{
    private $data = array();

    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->model(array('Signin_model'));
        $this->load->library(array('form_validation','recaptcha','session'));
        $this->load->helper(array('url','libs'));
    }

    public function index()
    {
        if (!empty($_POST)) {
            $this->Signin_model->validation();
        }

        $this->load->view('signin');
    }

}

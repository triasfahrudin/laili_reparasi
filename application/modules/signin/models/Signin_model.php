<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Signin_model extends CI_Model
{

    public function validation()
    {

        $captcha_answer = $this->input->post('g-recaptcha-response');
        $response       = $this->recaptcha->verifyResponse($captcha_answer);

        // if ($response['success']) {

            $this->form_validation->set_rules('email', 'Email', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() == true) {

                $email    = $this->input->post('email');
                $password = md5($this->input->post('password'));

                //cek admin
                $qry = $this->db->get_where('user', array(
                    'email'    => $email,
                    'password' => $password,
                ));

                if ($qry->num_rows() > 0) {
                    $user = $qry->row_array();

                    $this->session->set_userdata('user_id', $user['id']);
                    $this->session->set_userdata('user_email', $user['email']);
                    $this->session->set_userdata('user_nama_lengkap', $user['nama_lengkap']);

                    redirect(site_url('admin'), 'reload');

                } else {
                    return "error|Periksa kembali username & password yang anda masukkan";                    
                }
            }else{
                return "error|" . validation_errors();
            }
        // }
    }
}

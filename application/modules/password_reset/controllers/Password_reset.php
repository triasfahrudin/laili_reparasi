<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Password_reset extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('Asia/Jakarta');

        $this->load->helper(array('url', 'libs', 'form', 'alert'));
        $this->load->database();

        $this->load->libraries(array('session', 'form_validation', 'alert'));
    }

    public function index()
    {
        if (!empty($_POST)) {

            $email = $this->input->post('email');
            $cek   = $this->db->get_where('user', array('email' => $email));
            if ($cek->num_rows() > 0) {

                $token_reset_password = generate_uuid();
                //
                $this->db->where('email', $email);
                $this->db->update('user', array('token_reset_password' => $token_reset_password));

                //function send_email($recipient_email_address,$subject,$message,$attachment){
                $message = "Seseorang telah melakukan permintaan reset password akun anda <br />";
                $message .= "Jika anda tidak merasa melakukan hal ini, jangan hiraukan permintaan ini<br />";
                $message .= "Klik <a href='" . site_url('password-reset/do-reset/' . $token_reset_password) . "'>disini</a> jika anda ingin mereset password anda";
                $message .= "<hr />";
                $message .= "{timestamp:" . date("Y-m-d H:i:s") . "}";

                send_email($email, 'reset password', $message, 'none');
                echo '<script>
                  alert("Silahkan buka email anda untuk langkah selanjutnya");
                  window.location = "' . site_url('web') . '";
                </script>';
                // $this->alert->set('alert-success', 'Silahkan cek email untuk langkah selanjutnya' , false);
            } else {
                echo '<script>
                  alert("Email anda tidak ditemukan! Periksa kembali email yang anda masukkan");
                  window.location = "' . site_url('web') . '";
                </script>';
            }

            // redirect(site_url('web'),'reload');

        }

        $this->load->view('password_reset/index.php');
        // compress_output();
    }

    public function do_reset()
    {
        $token_reset_password = $this->uri->segment(3);

        //cek apakah token valid
        $cek = $this->db->get_where('user', array('token_reset_password' => $token_reset_password));
        if ($cek->num_rows() == 0) {
            echo '<script>alert("Token tidak valid!");window.location = "' . site_url('web') . '";</script>';
        }

        if (!empty($_POST)) {
            $this->form_validation->set_rules('new_pass', 'Password baru', 'required');
            $this->form_validation->set_rules('repeat_pass', 'Password ulangi', 'required|matches[new_pass]');

            if ($this->form_validation->run() == true) {
                $new_password = $this->input->post('new_pass');
                $this->db->where('token_reset_password', $token_reset_password);
                $this->db->update('user', array('password' => md5($new_password), 'token_reset_password' => generate_uuid()));
                echo '<script> alert("Password berhasil dirubah. Silahkan melakukan login dengan password yang baru");window.location = "' . site_url('signin') . '";</script>';
            } else {
                echo "<script>alert('Password tidak sama!');</script>";
            }
        }

        $this->load->view('form_reset_password');
    }

}

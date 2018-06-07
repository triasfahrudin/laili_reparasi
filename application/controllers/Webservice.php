<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webservice extends CI_Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        parent::__construct();

        $this->load->database();
        $this->load->helper(array('url', 'libs', 'form'));
        // $this->load->model('Basecrud_m');
        // $this->load->library('Json_Encode');
    }

    public function kirim_pesan()
    {
        header('content-type: application/json');

        $pelanggan = $this->input->post('pelanggan');
        $penjual   = $this->input->post('penjual');

        $pengirim = $this->input->post('pengirim');
        $pesan    = $this->input->post('pesan');

        $this->db->insert('percakapan',
            array(
                'pesan'        => $pesan,
                'pelanggan_id' => $pelanggan,
                'penjual_id'   => $penjual,
                'pengirim'     => $pengirim,
            )
        );

        echo json_encode(array('last_msg_id' => $this->db->insert_id()));
    }

    public function pesan_belum_terbaca()
    {

        /*select * from percakapan
        where pelanggan_id = 1 and penjual_id = 3 and id > 12*/

        header('content-type: application/json');

        $pelanggan   = $this->input->post('pelanggan');
        $penjual     = $this->input->post('penjual');
        $last_msg_id = $this->input->post('last_msg_id');

        $this->db->where('pelanggan_id', $pelanggan);
        $this->db->where('penjual_id', $penjual);
        $this->db->where('id > ', $last_msg_id);

        $this->db->order_by("tgl", "ASC");
        $rs_kat = $this->db->get("percakapan");

        $kat = '[';

        foreach ($rs_kat->result_array() as $r) {
            $kat .= '{';
            $kat .= '"id"   : "' . $r['id'] . '",';
            $kat .= '"tgl" : "' . $r['tgl'] . '",';
            $kat .= '"pesan" : "' . $r['pesan'] . '",';
            $kat .= '"pengirim" : "' . $r['pengirim'] . '"';
            $kat .= '},';
        }

        $kat = substr($kat, 0, -1);
        $kat .= ']';

        echo $kat;
    }

    public function pesan_tersimpan()
    {
        header('content-type: application/json');

        $pelanggan = $this->input->post('pelanggan');
        $penjual   = $this->input->post('penjual');

        $this->db->where('pelanggan_id', $pelanggan);
        $this->db->where('penjual_id', $penjual);
        $this->db->order_by("tgl", "ASC");
        $rs_kat = $this->db->get("percakapan");

        $kat = '[';

        foreach ($rs_kat->result_array() as $r) {
            $kat .= '{';
            $kat .= '"id"   : "' . $r['id'] . '",';
            $kat .= '"tgl" : "' . $r['tgl'] . '",';
            $kat .= '"pesan" : "' . $r['pesan'] . '",';
            $kat .= '"pengirim" : "' . $r['pengirim'] . '"';
            $kat .= '},';
        }

        $kat = substr($kat, 0, -1);
        $kat .= ']';

        echo $kat;
    }

    public function ganti_password()
    {
        /*
        reset_token : txtResetToken,
        pass_baru   : txtPass,
        email       : reset_email_request
         */

        header('content-type: application/json');

        $reset_token = $this->input->post('reset_token');
        $pass_baru   = $this->input->post('pass_baru');
        $email       = $this->input->post('email');

        $cek = $this->db->get_where('pelanggan', array('email' => $email, 'token_reset_password' => $reset_token));

        if ($cek->num_rows() > 0) {

            $token_reset_password = mt_rand(100000, 999999);

            $this->db->where('email', $email);
            $this->db->update('pelanggan',
                array(
                    'token_reset_password' => $token_reset_password,
                    'password'             => md5($pass_baru),
                )
            );

            echo json_encode(array('status' => 'berhasil'));

        } else {
            $cek = $this->db->get_where('penjual_jasa', array('email' => $email, 'token_reset_password' => $reset_token));

            if ($cek->num_rows() > 0) {

                $token_reset_password = mt_rand(100000, 999999);

                $this->db->where('email', $email);
                $this->db->update('penjual_jasa',
                    array(
                        'token_reset_password' => $token_reset_password,
                        'password'             => md5($pass_baru),
                    )
                );

                echo json_encode(array('status' => 'berhasil'));

            } else {

                echo json_encode(array('status' => 'reset_token_salah'));

            }
        }

    }

    public function order_jasa()
    {
        header('content-type: application/json');
        /*
        pelanggan_id      : localStorage.getItem('user_id'),
        penjual_jasa_id   : localStorage.getItem('order_penjual_id'),
        status            : 'PENDING',
        catatan_pelanggan : txtKerusakan
         */

        $pelanggan_id      = $this->input->post('pelanggan_id');
        $penjual_jasa_id   = $this->input->post('penjual_jasa_id');
        $status            = $this->input->post('status');
        $catatan_pelanggan = $this->input->post('catatan_pelanggan');
        $kategori_jasa_id  = $this->input->post('kategori_jasa_id');
        $email             = $this->input->post('email');

        $this->db->insert('transaksi',
            array(
                'pelanggan_id'      => $pelanggan_id,
                'penjual_jasa_id'   => $penjual_jasa_id,
                'kategori_jasa_id'  => $kategori_jasa_id,
                'status'            => $status,
                'catatan_pelanggan' => $catatan_pelanggan        
            )
        );

        $message = "Anda telah berhasil melakukan order reparasi pada reparasionline.com<br />";
        $message .= "Silahkan menunggu konfirmasi dari <br />";
        $message .= "Selalu waspada penipuan ! Jangan pernah melakukan pembayaran pada rekening selain rekening resmi orderreparasi.com! <br/>";
        
        // $message .= "Klik <a href='" . site_url('password-reset/do-reset/' . $token_reset_password) . "'>disini</a> jika anda ingin mereset password anda";
        $message .= "<hr />";
        $message .= "{timestamp:" . date("Y-m-d H:i:s") . "}";

        //kirim ke email pelanggan
        send_email($email, 'Order reparasi berhasil !', $message, 'none');

        //kirim ke email penyedia jasa
        $p = $this->db->get_where('penjual_jasa',array('id' => $penjual_jasa_id));
        if($p->num_rows() > 0){
            $res = $p->row_array();
            $email_penyedia_jasa = $res['email'];

            $message = "Ada order reparasi baru yang masuk<br />";
            $message .= "Silahkan melakukan konfirmasi di aplikasi<br />";
            
            $message .= "<hr />";
            $message .= "{timestamp:" . date("Y-m-d H:i:s") . "}";

            //kirim ke email pelanggan
            send_email($email_penyedia_jasa, 'Order reparasi baru !', $message, 'none');
        }





        echo json_encode(array('status' => 'berhasil'));
    }

    public function reset_password()
    {
        header('content-type: application/json');

        $email = $this->input->post('email');
        $cek   = $this->db->get_where('pelanggan', array('email' => $email));
        if ($cek->num_rows() > 0) {

            $token_reset_password = mt_rand(100000, 999999);

            $this->db->where('email', $email);
            $this->db->update('pelanggan', array('token_reset_password' => $token_reset_password));

            $message = "Seseorang telah melakukan permintaan reset password akun anda <br />";
            $message .= "Jika anda tidak merasa melakukan hal ini, jangan hiraukan permintaan ini<br />";
            $message .= "Reset number anda : " . $token_reset_password . '<br/>';
            $message .= "Jangan pernah membagi reset number anda kepada siapapun! <br/>";
            $message .= "Pihak kami tidak akan pernah meminta hal ini ! Waspadalah terhadap penipuan";

            // $message .= "Klik <a href='" . site_url('password-reset/do-reset/' . $token_reset_password) . "'>disini</a> jika anda ingin mereset password anda";
            $message .= "<hr />";
            $message .= "{timestamp:" . date("Y-m-d H:i:s") . "}";

            send_email($email, 'reset password', $message, 'none');

            echo json_encode(array('status' => 'berhasil'));

        } else {
            $cek = $this->db->get_where('penjual_jasa', array('email' => $email));

            if ($cek->num_rows() > 0) {

                $token_reset_password = mt_rand(100000, 999999);

                $this->db->where('email', $email);
                $this->db->update('penjual_jasa', array('token_reset_password' => $token_reset_password));

                $message = "Seseorang telah melakukan permintaan reset password akun anda <br />";
                $message .= "Jika anda tidak merasa melakukan hal ini, jangan hiraukan permintaan ini<br />";
                $message .= "Reset number anda : " . $token_reset_password . '<br/>';
                $message .= "Jangan pernah membagi reset number anda kepada siapapun! <br/>";
                $message .= "Pihak kami tidak akan pernah meminta hal ini ! Waspadalah terhadap penipuan";

                // $message .= "Klik <a href='" . site_url('password-reset/do-reset/' . $token_reset_password) . "'>disini</a> jika anda ingin mereset password anda";
                $message .= "<hr />";
                $message .= "{timestamp:" . date("Y-m-d H:i:s") . "}";

                send_email($email, 'reset password', $message, 'none');

                echo json_encode(array('status' => 'berhasil'));

            } else {
                echo json_encode(array('status' => 'email_ngga_terdaftar'));
            }
        }
    }

    public function kategori_jasa()
    {
        header('content-type: application/json');

        $rs_kat = $this->db->get('kategori_jasa');

        $kat = '[';

        foreach ($rs_kat->result_array() as $r) {
            $kat .= '{';
            $kat .= '"id"   : "' . $r['id'] . '",';
            $kat .= '"text" : "' . $r['nama'] . '"';
            $kat .= '},';
        }

        $kat = substr($kat, 0, -1);
        $kat .= ']';

        echo $kat;
    }

    public function encrypt_password($plain_text)
    {
        $encrypted_password = md5($plain_text);

        return $encrypted_password;
    }

    public function signup()
    {

        header('content-type: application/json');

        $email    = $this->input->post('email');
        $password = random_password(6);
        $nama     = $this->input->post('nama');
        $telp     = $this->input->post('telp');

        $cek = $this->db->get_where('pelanggan', array('email' => $email));

        if ($cek->num_rows() > 0) {
            echo json_encode(array('status' => 'email_udah_ada'));
        } else {

            $cek = $this->db->get_where('penjual_jasa', array('email' => $email));

            if ($cek->num_rows() > 0) {
                echo json_encode(array('status' => 'email_udah_ada'));
            } else {

                $jenis = $this->input->post('jenis');

                if ($jenis === 'pelanggan') {
                    $this->db->insert('pelanggan',
                        array(
                            'email'        => $email,
                            'password'     => md5($password),
                            'nama_lengkap' => $nama,
                            'telp'         => $telp,
                        )
                    );

                } else {
                    $kategori_jasa = $this->input->post('kategori_jasa');

                    $this->db->insert('penjual_jasa',
                        array(
                            'email'         => $email,
                            'password'      => md5($password),
                            'nama'          => $nama,
                            'telp'          => $telp,
                            'kategori_jasa' => $kategori_jasa,
                        )
                    );
                }

                $message = "Terimakasih anda mendaftar pada ReparasiOnline.com<br/>";
                $message .= "Berikut ini adalah detail akun anda<br/>";
                $message .= "Email: {email anda} <br/>";
                $message .= "Password: " . $password . "<br/>";
                $message .= "Nama    : " . $nama . "<br/>";
                $message .= "Telephon: " . $telp . "<br/>";
                $message .= "Jenis Akun: " . $jenis;
                $message .= "<hr />";
                $message .= "{timestamp:" . date("Y-m-d H:i:s") . "}";

                send_email($email, 'Pendaftaran ReparasiOnline.com', $message, 'none');

                echo json_encode(array('status' => 'berhasil'));
            }

        }

    }

    public function login()
    {
        header('content-type: application/json');

        $email    = $this->input->post('email');
        $password = $this->encrypt_password($this->input->post('password'));

        $qry = $this->db->get_where('pelanggan',
            array(
                'email'    => $email,
                'password' => $password)
        );

        if ($qry->num_rows() > 0) {

            $row = $qry->row_array();
            echo json_encode(
                array(
                    'id'        => $row['id'],
                    'status'    => 'pelanggan',
                    'nama'      => $row['nama_lengkap'],
                    'latitude'  => $row['latitude'],
                    'longitude' => $row['longitude'],
                )
            );
        } else {

            $qry = $this->db->get_where('penjual_jasa',
                array(
                    'email'    => $email,
                    'password' => $password)
            );

            if ($qry->num_rows() > 0) {

                $row = $qry->row_array();
                echo json_encode(
                    array(
                        'id'        => $row['id'],
                        'status'    => 'penjual',
                        'nama'      => $row['nama'],
                        'latitude'  => $row['latitude'],
                        'longitude' => $row['longitude'],
                    )
                );
            } else {
                echo json_encode(array('status' => 'not_found'));
            }

        }
    }

    public function kategori_reparasi()
    {
        header('content-type: application/json');

        $qry = $this->db->get('kategori_jasa');

        if ($qry->num_rows() > 0) {
            echo json_encode($qry->result());
        } else {
            echo json_encode(array('status' => 'not_found'));
        }
    }

    public function profile_penjual()
    {
        header('content-type: application/json');

        $qry = $this->db->get_where('penjual_jasa', array('id' => $this->input->post('penjual_id')));

        if ($qry->num_rows() > 0) {

            $p = $qry->row_array();
            echo json_encode(
                array(
                    'email'        => $p['email'],
                    'nama_lengkap' => $p['nama'],
                    'alamat'       => $p['alamat'],
                    'latitude'     => $p['latitude'],
                    'longitude'    => $p['longitude'],
                    'telp'         => $p['telp'],
                )
            );
        } else {
            echo json_encode(array('status' => 'not_found'));
        }
    }

    public function profile_pelanggan()
    {
        header('content-type: application/json');

        $qry = $this->db->get_where('pelanggan', array('id' => $this->input->post('pelanggan_id')));

        if ($qry->num_rows() > 0) {

            $p = $qry->row_array();
            echo json_encode(
                array(
                    'email'        => $p['email'],
                    'nama_lengkap' => $p['nama_lengkap'],
                    'alamat'       => $p['alamat'],
                    'latitude'     => $p['latitude'],
                    'longitude'    => $p['longitude'],
                    'telp'         => $p['telp'],
                )
            );
        } else {
            echo json_encode(array('status' => 'not_found'));
        }
    }

    public function update_lokasi()
    {
        $id        = $this->input->post('user_id');
        $jenis     = $this->input->post('jenis');
        $latitude  = $this->input->post('latitude');
        $longitude = $this->input->post('longitude');

        if ($jenis === 'pelanggan') {
            $this->db->where('id', $id);
            $this->db->update('pelanggan', array('latitude' => $latitude, 'longitude' => $longitude));
        } else {
            $this->db->where('id', $id);
            $this->db->update('penjual_jasa', array('latitude' => $latitude, 'longitude' => $longitude));
        }
    }

    public function update_profile()
    {
        $collumn = $this->input->post('c');
        $value   = $this->input->post('v');
        $jenis   = $this->input->post('j');
        $user_id = $this->input->post('u');

        if ($jenis === 'pelanggan') {
            $this->db->where('id', $user_id);
            $this->db->update('pelanggan', array($collumn => $value));
        } else {
            $this->db->where('id', $user_id);
            $this->db->update('penjual_jasa', array($collumn => $value));
        }
    }

    public function map_route_penjual()
    {
        header('content-type: application/json');

        $pelanggan_id = $this->input->post('pelanggan_id');
        $pelanggan    = $this->db->get_where('pelanggan', array('id' => $pelanggan_id))->row_array();

        $penjual_id = $this->input->post('penjual_id');
        $penjual    = $this->db->get_where('penjual_jasa', array('id' => $penjual_id))->row_array();

        echo json_encode(
            array(
                'lat_0' => $pelanggan['latitude'],
                'lng_0' => $pelanggan['longitude'],
                'lat_1' => $penjual['latitude'],
                'lng_1' => $penjual['longitude'],

            )
        );

    }


    public function map_route_pelanggan()
    {
        header('content-type: application/json');

        $pelanggan_id = $this->input->post('pelanggan_id');
        $pelanggan    = $this->db->get_where('pelanggan', array('id' => $pelanggan_id))->row_array();

        $penjual_id = $this->input->post('penjual_id');
        $penjual    = $this->db->get_where('penjual_jasa', array('id' => $penjual_id))->row_array();

        echo json_encode(
            array(
                'lat_1' => $pelanggan['latitude'],
                'lng_1' => $pelanggan['longitude'],
                'lat_0' => $penjual['latitude'],
                'lng_0' => $penjual['longitude'],

            )
        );

    }

    public function transaksi_penjual(){
        /*
             pelanggan_id     : localStorage.getItem('user_id'),
             status_transaksi : status_transaksi => {
                pending,
                proses,
                selesai (selesai , tolak)
             }
        */

        header('content-type: application/json');
        $penjual_jasa_id = $this->input->get('penjual_id');
        $status_transaksi = $this->input->get('status_transaksi');   

        //urutan ASC => dari tanggal lama ke baru
        //urutan DESC => dari tanggal baru ke lama  
        $urutan = "DESC";

        switch ($status_transaksi) {
              case 'pending':
              /*
                    '<div style="margin-left: 10px" class="bs-callout bs-callout-' + color_class[i] +'" id="callout-progress-animation-css3">' + 
                    '    <h4>Order #' + returnData.id + '</h4>' + 
                    '    <p>Reparator : ' + returnData.nama_penjual_jasa + '</p>' +
                    '    <p>Telp : ' + returnData.telp_penjual_jasa + '</p>' +
                    '    <p>Biaya disepakati : belum ditentukan</p>' +
                    '    <p>Tanggal Order : ' + returnData.tgl_transaksi + '</p>' +
                    '    <p>Catatan Order: ' + returnData.catatan_pelanggan + '</p>' +
                    '    <p><button type="button" class="btn btn-success btn-xs" onClick="msg(' + returnData.id_penjual_jasa +')">Kirim pesan</button></p>' +
                    '</div>';
              */
                  $this->db->select("a.id,a.status,a.biaya_disepakati,
                                     b.nama_lengkap AS nama_pelanggan,
                                     b.alamat AS alamat_pelanggan,
                                     b.telp AS telp_pelanggan,
                                     a.tgl_transaksi AS tanggal_order,
                                     a.catatan_pelanggan AS catatan_order,
                                     c.nama AS jenis_order,
                                     b.id AS pelanggan_id");
                  $this->db->join("pelanggan b","a.pelanggan_id = b.id","left");
                  $this->db->join("kategori_jasa c","a.kategori_jasa_id = c.id","left");
                  $this->db->where("a.penjual_jasa_id",$penjual_jasa_id);
                  $this->db->where("a.status","PENDING");
                  $this->db->order_by("a.tgl_transaksi",$urutan);
                  
                  $qry = $this->db->get("transaksi a");

                  break;

              case 'proses':
                  # code...
                  $this->db->select("a.id,a.status,a.biaya_disepakati,b.nama AS nama_penjual_jasa,b.telp AS telp_penjual_jasa,
                                     a.tgl_transaksi,a.tgl_diproses, a.catatan_pelanggan,a.catatan_penjual_jasa,b.id AS id_penjual_jasa");
                  $this->db->join("penjual_jasa b","a.penjual_jasa_id = b.id","left");
                  $this->db->where("a.pelanggan_id",$pelanggan_id);
                  $this->db->where("a.status","DALAM_PROSES");
                  $this->db->order_by("a.tgl_transaksi",$urutan);

                  $qry = $this->db->get("transaksi a");
                  break;    

              case 'selesai':
                  # code...
                  $this->db->select("a.id,a.status,a.biaya_disepakati,b.nama AS nama_penjual_jasa,b.telp AS telp_penjual_jasa,
                                     a.tgl_transaksi,a.tgl_selesai,a.catatan_pelanggan,a.catatan_penjual_jasa,b.id AS id_penjual_jasa");
                  $this->db->join("penjual_jasa b","a.penjual_jasa_id = b.id","left");
                  $this->db->where("a.pelanggan_id",$pelanggan_id);
                  $this->db->where("a.status","SELESAI");
                  $this->db->or_where("a.status","TOLAK");
                  $this->db->order_by("a.tgl_transaksi",$urutan);

                  $qry = $this->db->get("transaksi a");
                  break;        
              
              default:
                  # code...
                  break;
          }  

        // $qry = $this->db->query(
        //     "SELECT *
        //          FROM penjual_jasa a
        //          WHERE (a.kategori_jasa LIKE '$kategori_jasa_id,%'
        //                 OR a.kategori_jasa LIKE '%,$kategori_jasa_id,%'
        //                 OR a.kategori_jasa LIKE '%,$kategori_jasa_id')
        //         ");

        if ($qry->num_rows() > 0) {
            echo json_encode($qry->result());
        } else {
            echo json_encode(array('status' => 'not_found'));
        }
    }

    public function transaksi_pelanggan(){
        /*
             pelanggan_id     : localStorage.getItem('user_id'),
             status_transaksi : status_transaksi => {
                pending,
                proses,
                selesai (selesai , tolak)
             }
        */

        header('content-type: application/json');
        $pelanggan_id = $this->input->get('pelanggan_id');
        $status_transaksi = $this->input->get('status_transaksi');   

        switch ($status_transaksi) {
              case 'pending':
              /*
                    '<div style="margin-left: 10px" class="bs-callout bs-callout-' + color_class[i] +'" id="callout-progress-animation-css3">' + 
                    '    <h4>Order #' + returnData.id + '</h4>' + 
                    '    <p>Reparator : ' + returnData.nama_penjual_jasa + '</p>' +
                    '    <p>Telp : ' + returnData.telp_penjual_jasa + '</p>' +
                    '    <p>Biaya disepakati : belum ditentukan</p>' +
                    '    <p>Tanggal Order : ' + returnData.tgl_transaksi + '</p>' +
                    '    <p>Catatan Order: ' + returnData.catatan_pelanggan + '</p>' +
                    '    <p><button type="button" class="btn btn-success btn-xs" onClick="msg(' + returnData.id_penjual_jasa +')">Kirim pesan</button></p>' +
                    '</div>';
              */
                  $this->db->select("a.id,a.status,a.biaya_disepakati,b.nama AS nama_penjual_jasa,b.telp AS telp_penjual_jasa,
                                     a.tgl_transaksi,a.catatan_pelanggan,b.id AS id_penjual_jasa");
                  $this->db->join("penjual_jasa b","a.penjual_jasa_id = b.id","left");
                  $this->db->where("a.pelanggan_id",$pelanggan_id);
                  $this->db->where("a.status","PENDING");
                  $this->db->order_by("a.tgl_transaksi","DESC");
                  
                  $qry = $this->db->get("transaksi a");

                  break;

              case 'proses':
                  # code...
                  $this->db->select("a.id,a.status,a.biaya_disepakati,b.nama AS nama_penjual_jasa,b.telp AS telp_penjual_jasa,
                                     a.tgl_transaksi,a.tgl_diproses, a.catatan_pelanggan,a.catatan_penjual_jasa,b.id AS id_penjual_jasa");
                  $this->db->join("penjual_jasa b","a.penjual_jasa_id = b.id","left");
                  $this->db->where("a.pelanggan_id",$pelanggan_id);
                  $this->db->where("a.status","DALAM_PROSES");
                  $this->db->order_by("a.tgl_transaksi","DESC");

                  $qry = $this->db->get("transaksi a");
                  break;    

              case 'selesai':
                  # code...
                  $this->db->select("a.id,a.status,a.biaya_disepakati,b.nama AS nama_penjual_jasa,b.telp AS telp_penjual_jasa,
                                     a.tgl_transaksi,a.tgl_selesai,a.catatan_pelanggan,a.catatan_penjual_jasa,b.id AS id_penjual_jasa");
                  $this->db->join("penjual_jasa b","a.penjual_jasa_id = b.id","left");
                  $this->db->where("a.pelanggan_id",$pelanggan_id);
                  $this->db->where("a.status","SELESAI");
                  $this->db->or_where("a.status","TOLAK");
                  $this->db->order_by("a.tgl_transaksi","DESC");

                  $qry = $this->db->get("transaksi a");
                  break;        
              
              default:
                  # code...
                  break;
          }  

        // $qry = $this->db->query(
        //     "SELECT *
        //          FROM penjual_jasa a
        //          WHERE (a.kategori_jasa LIKE '$kategori_jasa_id,%'
        //                 OR a.kategori_jasa LIKE '%,$kategori_jasa_id,%'
        //                 OR a.kategori_jasa LIKE '%,$kategori_jasa_id')
        //         ");

        if ($qry->num_rows() > 0) {
            echo json_encode($qry->result());
        } else {
            echo json_encode(array('status' => 'not_found'));
        }
    }

    public function penjual_perkategori()
    {
        header('content-type: application/json');

        $kategori_jasa_id = $this->input->get('kategori_jasa_id');
        //10,
        //,10,
        //,10
        // $this->db->where('id = ',$kategori_jasa_id);
        // $this->db->or_like('id = ',$kategori_jasa_id . ',');
        // $this->db->or_like('id = ,',$kategori_jasa_id . ',');
        // $this->db->or_like('id = ,',$kategori_jasa_id . ',');

        // $qry = $this->db->get('/penjual_jasa');

        $qry = $this->db->query(
            "SELECT *
                 FROM penjual_jasa a
                 WHERE (a.kategori_jasa LIKE '$kategori_jasa_id,%'
                        OR a.kategori_jasa LIKE '%,$kategori_jasa_id,%'
                        OR a.kategori_jasa LIKE '%,$kategori_jasa_id')
                ");

        if ($qry->num_rows() > 0) {
            echo json_encode($qry->result());
        } else {
            echo json_encode(array('status' => 'not_found'));
        }
    }

    public function nama_kategori_jasa($kat_list)
    {
        $arr_kat_list = explode(',', $kat_list);

        $this->db->where_in('id', $arr_kat_list);
        $r = $this->db->get('kategori_jasa');

        $ret = "";
        foreach ($r->result_array() as $row) {
            $ret .= $row['nama'] . ",";
        }

        return substr($ret, 0, -1);
    }

    public function lokasi_terdekat()
    {
        header('content-type: application/json');

        $lat      = $this->input->get('lat');
        $lng      = $this->input->get('lng');
        $distance = get_settings('radius_jarak_terdekat');

        //en.wikipedia.org/wiki/Haversine_formula
        //https://developers.google.com/maps/solutions/store-locator/clothing-store-locator#findnearsql

        //To search by miles instead of kilometers, replace 6371 with 3959.
        $sql = "SELECT id, nama, email,alamat,telp,
                       latitude,longitude,
                       ( 6371 * acos( cos( radians($lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( latitude ) ) ) ) AS jarak
                FROM penjual_jasa a
                HAVING jarak BETWEEN 0.1 AND $distance
                ORDER BY jarak";

        $rs = $this->db->query($sql);

        if ($rs->num_rows() > 0) {
            //echo json_encode($qry->result());

            $p = '[';

            foreach ($rs->result_array() as $r) {
                $p .= '{';
                $p .= '"id"   : "' . $r['id'] . '",';
                $p .= '"nama" : "' . $r['nama'] . '",';
                $p .= '"alamat" : "' . $r['alamat'] . '",';
                $p .= '"telp" : "' . $r['telp'] . '",';
                $p .= '"email" : "' . $r['email'] . '",';
                $p .= '"latitude" : "' . $r['latitude'] . '",';
                $p .= '"longitude" : "' . $r['longitude'] . '",';
                $p .= '"jarak" : "' . $r['jarak'] . '"';
                $p .= '},';
            }

            $p = substr($p, 0, -1);
            $p .= ']';

            echo $p;

        } else {
            echo json_encode(array('status' => 'not_found'));
        }

    }

}
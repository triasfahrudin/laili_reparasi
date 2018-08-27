<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    private $user_id;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
        $this->load->helper(array(
            'url',
            'libs',
            'alert',
        ));
        $this->load->library(array(
            'form_validation',
            'session',
            'alert',
            'breadcrumbs',
        ));

        $this->breadcrumbs->load_config('default');

        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            redirect('signin', 'reload');
        }

        $this->user_id = $this->session->userdata('user_id');

        $this->output->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    public function _page_output($output = null)
    {
        $this->load->view('master.php', (array) $output);
    }


    public function lihat_percakapan(){
        header('content-type: application/json');

        $trans_id  = $this->input->post('transaksi_id');

        $this->db->where('trans_id', $trans_id);
        $this->db->order_by("tgl", "ASC");
        $rs_kat = $this->db->get("percakapan");

        $kat = '[';

        foreach ($rs_kat->result_array() as $r) {
            $kat .= '{';
            $kat .= '"id"   : "' . $r['id'] . '",';
            $kat .= '"tgl" : "' . nicetime($r['tgl']) . '",';
            $kat .= '"pesan" : "' . $r['pesan'] . '",';
            $kat .= '"pengirim" : "' . $r['pengirim'] . '"';
            $kat .= '},';
        }

        $kat = substr($kat, 0, -1);
        $kat .= ']';

        echo $kat;
    }

    public function index()
    {
        $this->breadcrumbs->push('Dashboard', '/admin');

        $data['breadcrumbs'] = $this->breadcrumbs->show();

        $data['page_name']  = 'beranda';
        $data['page_title'] = 'Beranda';
        $this->_page_output($data);
    }

    public function statistik()
    {
        header('content-type: application/json');

        $status = $this->input->post('status');
        // $status = $this->input->post('status');

        if ($status === 'semua') {

            $pelanggan    = $this->db->get('pelanggan');
            $penjual_jasa = $this->db->get('penjual_jasa');
            $transaksi    = $this->db->get('transaksi');

            echo json_encode(
                array(
                    'jml_pelanggan'    => $pelanggan->num_rows(),
                    'jml_penjual_jasa' => $penjual_jasa->num_rows(),
                    'jml_transaksi'    => $transaksi->num_rows(),
                )
            );
        } else {

        }

    }

    //<admin_user>
    public function user()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('user');
            $crud->set_subject('Akun User');

            $state = $crud->getState();

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Akun Admin', '/admin/user');

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/user/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/user/add');
            }

            $crud->required_fields('email', 'password', 'nama_lengkap');
            $crud->edit_fields('nama_lengkap');
            $crud->add_fields('email', 'password', 'nama_lengkap');

            $crud->callback_before_insert(function ($post_array, $primary_key = null) {
                $post_array['password'] = md5($post_array['password']);

                return $post_array;
            });

            $crud->columns('email', 'nama_lengkap');

            $crud->unset_read_fields('password');

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Akun Admin',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    //</admin_user>

    public function kategori_jasa()
    {
        try {
            $this->load->library(array(
                'grocery_CRUD',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('kategori_jasa');
            $crud->set_subject('Kategori Jasa');

            $state = $crud->getState();

            $crud->required_fields('nama');
            $crud->columns('nama', 'keterangan');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Pengumuman', '/admin/kategori-jasa');

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/kategori-jasa/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/kategori-jasa/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Kategori Jasa',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function transaksi_pelanggan($pelanggan_id)
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('transaksi');
            $crud->where('pelanggan_id', $pelanggan_id);
            $crud->set_subject('Data Transaksi Pelanggan');

            $crud->columns('tgl_transaksi', 'penjual_jasa_id', 'status', 'biaya_disepakati');

            $crud->callback_column('status', function ($value, $row) {
                $status = "";
                // if ($row->status === 'SELESAI') {
                //     $status = '<span class="label label-success">Selesai</span>';
                // } elseif ($row->status === 'DALAM_PROSES') {
                //     $status = '<span class="label label-warning">Diproses</span>';
                // } elseif ($row->status === 'TOLAK') {
                //     $status = '<span class="label label-danger">Ditolak</span>';
                // } else {
                //     $status = '<span class="label label-default">Pending</span>';
                // }

                return $row->status;
            });

            $crud->callback_column('biaya_disepakati', function ($value, $row) {
                return format_rupiah($row->biaya_disepakati);
            });

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data pelanggan', '/admin/pelanggan');
            $this->breadcrumbs->push('Data transaksi ' . get_val('pelanggan', $pelanggan_id, 'nama_lengkap'), '/admin/transaksi_pelanggan/' . $pelanggan_id);

            $state = $crud->getState();
            if ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', '/admin/transaksi_pelanggan/edit/' . $pelanggan_id);
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/transaksi_pelanggan/add');
            }

            $crud->set_relation('penjual_jasa_id', 'penjual_jasa', 'nama');

            $crud->display_as('penjual_jasa_id', 'Penyedia Jasa');

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $extra  = array('page_title' => 'Data Transaksi Pelanggan');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function transaksi_penjual_jasa($penjual_jasa_id)
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('transaksi');
            $crud->where('penjual_jasa_id', $penjual_jasa_id);
            $crud->set_subject('Data transaksi Penyedia jasa');

            $crud->columns('tgl_transaksi', 'pelanggan_id', 'status', 'biaya_disepakati');

            $crud->callback_column('status', function ($value, $row) {

                // if ($row->status === 'SELESAI') {
                //     $status = '<span class="label label-success">Selesai</span>';
                // } elseif ($row->status === 'DALAM_PROSES') {
                //     $status = '<span class="label label-warning">Diproses</span>';
                // } elseif($row->status === 'TOLAK') {
                //     $status = '<span class="label label-danger">Ditolak</span>';
                // }else{
                //     $status = '<span class="label label-default">Pending</span>';
                // }

                return $row->status;
            });

            $crud->callback_column('biaya_disepakati', function ($value, $row) {
                return format_rupiah($row->biaya_disepakati);
            });

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data Penyedia Jasa', '/admin/penjual_jasa');
            $this->breadcrumbs->push('Data Transaksi ' . get_val('penjual_jasa', $penjual_jasa_id, 'nama'), '/admin/transaksi_penjual_jasa/' . $penjual_jasa_id);

            $state = $crud->getState();
            if ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', '/admin/transaksi_penjual_jasa/edit/' . $penjual_jasa_id);
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/transaksi_penjual_jasa/add');
            }

            $crud->set_relation('pelanggan_id', 'pelanggan', 'nama_lengkap');

            $crud->display_as('pelanggan_id', 'Pelanggan');

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $extra  = array('page_title' => 'Data Transaksi Penyedia jasa');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function pelanggan()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('pelanggan');
            $crud->set_subject('Data Pelanggan');

            $crud->columns('email', 'nama_lengkap', 'telp', 'transaksi', 'tgl_daftar');

            $crud->callback_column('transaksi', function ($value, $row) {
                $pelanggan = $this->db->get_where('pelanggan', array('id' => $row->id))->row_array();
                return '<a href="' . site_url('admin/transaksi-pelanggan/' . $pelanggan['id']) . '">Lihat</a>';
            });

            $crud->unset_read_fields('password');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data Pelanggan', '/admin/pelanggan');

            $state = $crud->getState();
            if ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', '/admin/pelanggan/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/pelanggan/add');
            }

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $extra  = array('page_title' => 'Data Pelanggan');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function plot_point_js($id)
    {
        // $retailer_id = $this->session->userdata('retailer_id');
        $retailer = $this->db->get_where('penjual_jasa', array('id' => $id));
        if ($retailer->num_rows() > 0) {
            $ret       = $retailer->row_array();
            $latitude  = $ret['latitude'];
            $longitude = $ret['longitude'];
            $script    = '

                var map;
                var marker;
                var circle;
                var geocoder;
                window.onload = function() {
                  geocoder = new google.maps.Geocoder();
                  var latlng = new google.maps.LatLng(' . $latitude . ',' . $longitude . ');
                  var myOptions = {
                      zoom: 18,
                      center: latlng,
                      mapTypeId: google.maps.MapTypeId.SATELLITE
                    };
                    map = new google.maps.Map(document.getElementById("map_field_box"), myOptions);
                      addMarker(map.getCenter());
                      google.maps.event.addListener(map,"click", function(event) {
                    //alert("You cannot reset the location by changing pointer in here");
                      //addMarker(event.latLng);
                    });
                  }

                function addMarker(location) {
                    if(marker) {marker.setMap(null);}
                    marker = new google.maps.Marker({
                      position: location,
                        draggable: true
                    });
                    marker.setMap(map);
                  }';

            echo $script;
        }
    }

    public function show_map_field($value = false, $primary_key = false)
    {
        return '<p>Perbaiki posisi dengan menyeret dan menjatuhkan pin pada lokasi yang ditentukan</p>
                <input id="pac-input" class="controls" type="text" placeholder="Pencarian">
                <div id="location-map" style="width:530px; height:300px;"></div>';
    }

    public function resize_map($tipe, $width)
    {
        // echo '$("#map_field_box").attr("style", "height: ' . $height .';width:100%");';
        $style = "";
        if ($tipe === 'percent') {
            $style = $width . '%';
        } else {
            $style = $width . 'px';
        }

        echo 'document.getElementById("map_field_box").style["height"] = "' . $style . '";';

    }

    // public function web_settings()
    // {
    //     $this->breadcrumbs->push('Dashboard', '/admin');
    //     $this->breadcrumbs->push('Setting', '/settings');

    //     $data['breadcrumbs'] = $this->breadcrumbs->show();

    //     $act   = $this->uri->segment(3);
    //     $param = $this->uri->segment(4);

    //     if ($act === 'edt-value') {
    //         $value = $this->input->post('value');

    //         $this->db->where('title', $param);
    //         $this->db->update('settings', array('value' => $value));

    //         exit(0);
    //     } elseif ($act === 'edt-show') {
    //         $value = $this->input->post('value');

    //         $this->db->where('title', $param);
    //         $this->db->update('settings', array('show' => $value));

    //         exit(0);
    //     }

    //     $data['setting']    = $this->db->get('settings');
    //     $data['page_name']  = 'settings';
    //     $data['page_title'] = 'Data Settings';

    //     $this->_page_output($data);
    // }

    public function penjual_jasa()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('penjual_jasa');
            $crud->set_subject('Data Penyedia Jasa');

            $crud->columns('nama', 'email', 'kategori_jasa', 'telp', 'transaksi', 'tgl_daftar');

            $crud->callback_before_insert(function ($post_array, $primary_key = null) {
                $post_array['password'] = md5($post_array['email']);

                return $post_array;
            });

            $crud->callback_column('transaksi', function ($value, $row) {
                $pelanggan = $this->db->get_where('penjual_jasa', array('id' => $row->id))->row_array();
                return '<a href="' . site_url('admin/transaksi-penjual-jasa/' . $pelanggan['id']) . '">Daftar Transaksi</a>';
            });

            $crud->unset_read_fields('password');

            // $crud->edit_fields('email' , 'kategori_jasa','alamat','telp','foto','verifikasi','map','tgl_daftar');
            // $crud->add_fields('email', 'password', 'kategori_jasa','alamat','telp','foto','verifikasi','tgl_daftar');

            $crud->required_fields('nama', 'email', 'alamat', 'telp');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data Penyedia jasa', '/admin/penjual-jasa');

            $state = $crud->getState();

            if ($state === 'read') {
                $this->breadcrumbs->push('Ubah', '/admin/penjual-jasa/edit');

                $crud->set_js("admin/plot_point_js/" . $this->uri->segment(4));
                $crud->set_js('http://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4&sensor=false&libraries=places"', false);

            } elseif ($crud->getState() === 'add' || $crud->getState() === 'edit' || $crud->getState() === 'copy') {
                $this->breadcrumbs->push('Tambah', '/admin/penjual-jasa/add');

                $crud->set_js("assets/manage/js/map.js?v=" . date("YmdHis"));
                $crud->set_js('http://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4&sensor=false&libraries=places"', false);
            }

            $crud->callback_add_field('map', array($this, 'show_map_field'));
            $crud->callback_edit_field('map', array($this, 'show_map_field'));

            $crud->set_field_upload('foto', 'uploads/foto_tempat_reparasi');

            $crud->change_field_type('latitude', 'hidden');
            $crud->change_field_type('longitude', 'hidden');

            $crud->change_field_type('token_reset_password', 'hidden');
            $crud->change_field_type('device_id', 'hidden');
            // $crud->field_type('tgl_daftar', 'readonly');
            $crud->change_field_type('password', 'hidden');

            // $crud->unset_add();
            // $crud->unset_edit();
            $crud->unset_read();

            //set wilayah kerja
            $arr_kat_jasa  = array();
            $kategori_jasa = $this->db->get('kategori_jasa');
            foreach ($kategori_jasa->result_array() as $kat) {
                $arr_kat_jasa[$kat['id']] = $kat['nama'];
            }

            $crud->field_type('kategori_jasa', 'multiselect', $arr_kat_jasa);

            // $crud->display_as('rt','RT');
            // $crud->display_as('rw','RW');

            $crud->set_relation('provinsi', 'provinsi', 'nama');
            $crud->set_relation('kabupaten', 'kabupaten', 'nama');
            $crud->set_relation('kecamatan', 'kecamatan', 'nama');
            $crud->set_relation('kelurahan', 'kelurahan', 'nama');

            $this->load->library('Gc_Dependent_Select');

            $fields = array(
                // first field:
                'provinsi'  => array( // first dropdown name
                    'table_name'       => 'provinsi', // table of country
                    'title'            => 'nama', // country title
                    'relate'           => null, // the first dropdown hasn't a relation
                    'data-placeholder' => 'Pilih Provinsi', //dropdown's data-placeholder:
                ),
                // second field
                'kabupaten' => array( // second dropdown name
                    'table_name'       => 'kabupaten', // table of state
                    'title'            => 'nama', // state title
                    'id_field'         => 'id_kab', // table of state: primary key
                    'relate'           => 'id_prov', // table of state:
                    'data-placeholder' => 'Pilih kabupaten', //dropdown's data-placeholder:
                ),
                // third field. same settings
                'kecamatan' => array(
                    'table_name'       => 'kecamatan',
                    'title'            => 'nama', // now you can use this format )))
                    'id_field'         => 'id_kec',
                    'relate'           => 'id_kab',
                    'data-placeholder' => 'Pilih Kecamatan',
                ),
                'kelurahan' => array(
                    'table_name'       => 'kelurahan',
                    'title'            => 'nama', // now you can use this format )))
                    'id_field'         => 'id_kel',
                    'relate'           => 'id_kec',
                    'data-placeholder' => 'Pilih Kelurahan',
                ),

            );

            $config = array(
                'main_table'         => 'penjual_jasa',
                'main_table_primary' => 'id',
                "url"                => site_url('/admin/penjual_jasa/'),
                'ajax_loader'        => base_url() . 'assets/ajax-loader.gif',
            );

            $wilayah = new Gc_dependent_select($crud, $fields, $config);

            $extra = array('page_title' => 'Data Pelanggan');
            $js    = $wilayah->get_js();

            $output = $crud->render();
            $output->output .= $js;

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function update_status_penarikan_dana()
    {
        if (!empty($_POST)) {
            $id     = $this->input->post('id');
            $status = $this->input->post('status');

            if ($status === 'dibayar') {
                $this->db->where('id', $id);
                $this->db->update('request_dana_keluar', array('status' => $status, 'tanggal_bayar' => date("Y-m-d H:i:s")));
            } else {
                $this->db->where('id', $id);
                $this->db->update('request_dana_keluar', array('status' => $status));
            }

        }
    }

    public function penarikan_dana()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('request_dana_keluar');
            $crud->set_subject('Permintaan Dana Keluar');

            $crud->columns('penjual_jasa_id', 'nominal', 'status_bayar', 'tanggal_request', 'keterangan');

            $crud->set_relation('penjual_jasa_id', 'penjual_jasa', 'nama');

            $crud->callback_column('status_bayar', function ($value, $row) {
                if ($row->status === 'pending') {
                    return '<a href="#" onclick="update_status_penarikan_dana(' . $row->id . ',\'dibayar\')"><span class="label label-success">BAYAR</span></a>&nbsp|&nbsp;<a href="#" onclick="update_status_penarikan_dana(' . $row->id . ',\'ditolak\')"><span class="label label-danger">TOLAK</span></a>';
                } elseif ($row->status === 'dibayar') {
                    return '<span class="label label-success">DIBAYAR</span>';
                } else {
                    return '<span class="label label-danger">DITOLAK</span>';
                }
            });

            $crud->callback_column('keterangan', function ($value, $row) {
                $penjual_jasa_id = $row->penjual_jasa_id;
                $saldo           = 0;

                $this->db->order_by('id', 'DESC');
                $rs_dompetku = $this->db->get_where('dompetku', array('penjual_jasa_id' => $penjual_jasa_id));

                $penjual_jasa = $this->db->get_where('penjual_jasa', array('id' => $penjual_jasa_id))->row_array();

                if ($rs_dompetku->num_rows() > 0) {
                    $dompetku = $rs_dompetku->row_array();
                    $saldo    = $dompetku['saldo_akhir'];
                }

                if ($row->status === 'pending') {

                    $status_kecukupan_dana = 'TIDAK CUKUP';
                    if ($row->nominal <= $saldo) {
                        $status_kecukupan_dana = 'CUKUP';
                    }

                    return 'Rekening : ' . $penjual_jasa['bank'] . ' ( ' . $penjual_jasa['rekening_bank'] . ') <br> Saldo: Rp ' . $saldo . ' (' . $status_kecukupan_dana . ')';

                } elseif ($row->status === 'dibayar') {
                    return 'Dibayar pada : ' . $row->tanggal_bayar;
                }

                // return $row->status;

            });

            $crud->display_as('penjual_jasa_id', 'Penyedia Jasa');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Penarikan Dana', '/admin/penarikan_dana');

            // $state = $crud->getState();
            // if ($state === 'edit') {
            //     $this->breadcrumbs->push('Ubah', '/admin/transaksi/edit');
            // } elseif ($state === 'add') {
            //     $this->breadcrumbs->push('Tambah', '/admin/transaksi/add');
            // }

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_read();

            $extra  = array('page_title' => 'Permintaan Dana Keluar');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function detail_validasi_pembayaran()
    {
         header('content-type: application/json');

        $transaksi_id = $this->input->post('transaksi_id');

        $trans = $this->db->get_where('transaksi', array('id' => $transaksi_id))->row_array();

        $bukti_bayar = explode('|', $trans['bukti_bayar']);

        echo json_encode(
            array(
                'transaksi_id'  => $transaksi_id,
                'nominal'       => explode(':',$bukti_bayar[0])[1],
                'nama_penyetor' => explode(':',$bukti_bayar[1])[1],
                'tanggal'       => explode(':',$bukti_bayar[2])[1],

            )
        );

    }

    public function transaksi()
    {
        //pelanggan id, penjual_jasa_id, status,biaya_disepakati,tgl_transaksi
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('transaksi');
            $crud->set_subject('Data Transaksi');

            $crud->columns('tgl_transaksi','pelanggan_id', 'penjual_jasa_id', 'status', 'biaya_disepakati', 'detail');

            $crud->set_relation('pelanggan_id', 'pelanggan', 'email');
            $crud->set_relation('penjual_jasa_id', 'penjual_jasa', 'nama');

            $crud->callback_column('detail', function ($value, $row) {
                return '<a href="#" onclick="lihat_detail_transaksi(\'' . $row->id . '\')">Lihat</a>&nbsp;|&nbsp;<a href="#" onclick="lihat_percakapan(\'' . $row->id . '\')">Percakapan</a>';
            });

            // $crud->callback_column('percakapan', function ($value, $row) {
            //     return '<a href="#" onclick="lihat_percakapan(\'' . $row->id . '\')">Lihat</a>';
            // });

            $crud->callback_column('biaya_disepakati', function ($value, $row) {
                // if ($row->status === '') {
                //     return format_rupiah($row->biaya_disepakati);
                // } else {
                //     return 'Rp.-';
                // }

                return $row->biaya_disepakati;

            });

            $crud->callback_column('status', function ($value, $row) {
                if ($row->status === 'MENUNGGU_TANGGAPAN_PENJUAL' || $row->status === 'PENJUAL_TERIMA_KERJA' || $row->status === 'MENUNGGU_TANGGAPAN_PEMBELI' || $row->status === 'PELANGGAN_SETUJU_BIAYA') {

                    return '<span class="full-width label label-default">' . $row->status . '</span>';

                } elseif ($row->status === 'PELANGGAN_UPLOAD_BUKTI' || $row->status === 'BUKTI_VALID' || $row->status === 'DALAM_PROSES') {

                    if ($row->status === 'PELANGGAN_UPLOAD_BUKTI') {
                        //<a href="#" onclick="lihat_detail_transaksi(\'' . $row->id . '\')">Lihat</a>
                        return '<a href="#" onclick="validasi_pembayaran(\'' . $row->id . '\')" class="btn btn-warning">VALIDASI_PEMBAYARAN_DIPERLUKAN</a>';
                    } else {
                        return '<span class="full-width label label-warning">' . $row->status . '</span>';
                    }

                } elseif ($row->status === 'PROSES_SELESAI' || $row->status === 'BARANG_DITERIMA_PELANGGAN' || $row->status === 'BUKTI_VALID') {

                    return '<span class="label label-success">' . $row->status . '</span>';

                } elseif ($row->status === 'PENJUAL_TOLAK_KERJA' || $row->status === 'PELANGGAN_TOLAK_BIAYA' || $row->status === 'BUKTI_TIDAK_VALID') {

                    return '<span class="full-width label label-danger">' . $row->status . '</span>';

                }

                /*elseif ($row->status === 'DALAM_PROSES') {
            return '<span class="label label-success">DALAM PROSES</span>';
            } elseif ($row->status === 'SELESAI') {
            return '<span class="label label-default">SELESAI</span>';
            } else {
            return '<span class="label label-danger">TOLAK</span>';
            }*/
            });

            $crud->display_as('pelanggan_id', 'Pelanggan');
            $crud->display_as('penjual_jasa_id', 'Penyedia Jasa');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data Transaksi', '/admin/transaksi');

            $state = $crud->getState();
            if ($state === 'edit') {
                $this->breadcrumbs->push('Ubah', '/admin/transaksi/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/transaksi/add');
            }

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_read();

            $extra  = array('page_title' => 'Data Transaksi');
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    //===================================================================

    
    
    
    public function web_settings($act = null, $param = null)
    {
        // $this->load->model(array('Basecrud_m'));
        $this->breadcrumbs->push('Web Setting', '/web-settings');

        $data['breadcrumbs'] = $this->breadcrumbs->show();

        if ($act === 'upload') {
            if (!empty($_FILES['img']['name'])) {
                // $this->load->library(array('cloudinarylib'));
                // try{
                //   $imageupload = \Cloudinary\Uploader::upload($_FILES["img"]["tmp_name"]);
                //   $image_url = $imageupload['secure_url'];
                //
                //   $this->db->where('id', $blog_id);
                //   $this->db->update('blogs', array('image' => $image_url));
                //
                //   redirect('manage/blogs');
                //
                // }catch(Exception $e){
                //   $data['msg'] = 'ERROR : bukan file image !';
                // }

                $upload                  = array();
                $upload['upload_path']   = './uploads';
                $upload['allowed_types'] = 'jpeg|jpg|png';
                $upload['encrypt_name']  = true;

                $this->load->library('upload', $upload);

                if (!$this->upload->do_upload('img')) {
                    $data['msg'] = $this->upload->display_errors();
                } else {
                    $success  = $this->upload->data();
                    $value    = $success['file_name'];
                    $file_ext = $success['file_ext'];

                    $this->db->where('title', $param);
                    $this->db->update('settings', array('value' => $value, 'tipe' => 'image'));

                    redirect('admin/web-settings');
                }
            }
        } elseif ($act === 'edt') {
            $value = $this->input->post('value');

            $this->db->where('title', $param);
            $this->db->update('settings', array('value' => $value));

            exit(0);
        }

        $data['setting']    = $this->db->get('settings');
        $data['page_name']  = 'settings';
        $data['page_title'] = 'Data Settings';

        $this->_page_output($data);
    }

    

}

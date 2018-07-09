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
                if ($row->status === 'SELESAI') {
                    $status = '<span class="label label-success">Selesai</span>';
                } elseif ($row->status === 'DALAM_PROSES') {
                    $status = '<span class="label label-warning">Diproses</span>';
                } elseif($row->status === 'TOLAK') {
                    $status = '<span class="label label-danger">Ditolak</span>';
                }else{
                    $status = '<span class="label label-default">Pending</span>';
                }

                return $status;
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

            $crud->display_as('penjual_jasa_id', 'Penjual Jasa');

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
            $crud->set_subject('Data transaksi penjual jasa');

            $crud->columns('tgl_transaksi', 'pelanggan_id', 'status', 'biaya_disepakati');

            $crud->callback_column('status', function ($value, $row) {
                
                if ($row->status === 'SELESAI') {
                    $status = '<span class="label label-success">Selesai</span>';
                } elseif ($row->status === 'DALAM_PROSES') {
                    $status = '<span class="label label-warning">Diproses</span>';
                } elseif($row->status === 'TOLAK') {
                    $status = '<span class="label label-danger">Ditolak</span>';
                }else{
                    $status = '<span class="label label-default">Pending</span>';
                }

                return $status;
            });

            $crud->callback_column('biaya_disepakati', function ($value, $row) {
                return format_rupiah($row->biaya_disepakati);
            });

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data Penjual Jasa', '/admin/penjual_jasa');
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

            $extra  = array('page_title' => 'Data Transaksi Penjual jasa');
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
            $crud->set_subject('Data Penjual Jasa');

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
            $this->breadcrumbs->push('Data Penjual jasa', '/admin/penjual-jasa');

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

            if($status === 'dibayar'){
                $this->db->where('id', $id);
                $this->db->update('request_dana_keluar', array('status' => $status,'tanggal_bayar' => date("Y-m-d H:i:s") ));
            }else{
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

            $crud->columns('penjual_jasa_id', 'nominal',   'status_bayar', 'tanggal_request','keterangan');

            $crud->set_relation('penjual_jasa_id', 'penjual_jasa', 'nama');

            $crud->callback_column('status_bayar', function ($value, $row) {
                if ($row->status === 'pending') {
                    return '<a href="#" onclick="update_status_penarikan_dana(' . $row->id .',\'dibayar\')"><span class="label label-success">BAYAR</span></a>&nbsp|&nbsp;<a href="#" onclick="update_status_penarikan_dana(' . $row->id .',\'ditolak\')"><span class="label label-danger">TOLAK</span></a>';
                } elseif ($row->status === 'dibayar') {
                    return '<span class="label label-success">DIBAYAR</span>';
                } else {
                    return '<span class="label label-danger">DITOLAK</span>';
                }
            });

            $crud->callback_column('keterangan', function ($value, $row) {
                $penjual_jasa_id = $row->penjual_jasa_id;
                $saldo = 0;

                $this->db->order_by('id','DESC');
                $rs_dompetku = $this->db->get_where('dompetku',array('penjual_jasa_id' => $penjual_jasa_id));

                $penjual_jasa = $this->db->get_where('penjual_jasa',array('id' => $penjual_jasa_id))->row_array();


                if($rs_dompetku->num_rows() > 0){
                    $dompetku = $rs_dompetku->row_array();
                    $saldo = $dompetku['saldo_akhir'];
                }

                if($row->status === 'pending'){
                    
                    $status_kecukupan_dana = 'TIDAK CUKUP';
                    if($row->nominal <= $saldo){
                        $status_kecukupan_dana = 'CUKUP';
                    }

                    return  'Rekening : ' . $penjual_jasa['bank'] . ' ( ' . $penjual_jasa['rekening_bank'] . ') <br> Saldo: Rp ' . $saldo . ' (' . $status_kecukupan_dana  .')';

                }elseif($row->status === 'dibayar'){
                    return 'Dibayar pada : ' . $row->tanggal_bayar;
                }


                // return $row->status;
                
            });

            $crud->display_as('penjual_jasa_id', 'Penjual Jasa');

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

    public function transaksi()
    {
        //pelanggan id, penjual_jasa_id, status,biaya_disepakati,tgl_transaksi
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('transaksi');
            $crud->set_subject('Data Transaksi');

            $crud->columns('pelanggan_id', 'penjual_jasa_id', 'status', 'biaya_disepakati', 'detail');

            $crud->set_relation('pelanggan_id', 'pelanggan', 'email');
            $crud->set_relation('penjual_jasa_id', 'penjual_jasa', 'nama');

            $crud->callback_column('detail', function ($value, $row) {
                return '<a href="#" onclick="lihat_detail_transaksi(\'' . $row->id . '\')">Lihat</a>';
            });

            // $crud->callback_column('percakapan', function ($value, $row) {                
            //     return '<a href="#" onclick="lihat_percakapan(\'' . $row->id . '\')">Lihat</a>';
            // });

            $crud->callback_column('biaya_disepakati', function ($value, $row) {
                if ($row->status === '<span class="label label-default">SELESAI</span>') {
                    return format_rupiah($row->biaya_disepakati);
                } else {
                    return 'Rp.-';
                }

            });

            $crud->callback_column('status', function ($value, $row) {
                if ($row->status === 'PENDING') {
                    return '<span class="label label-default">PENDING</span>';
                } elseif ($row->status === 'DALAM_PROSES') {
                    return '<span class="label label-success">DALAM PROSES</span>';
                } elseif ($row->status === 'SELESAI') {
                    return '<span class="label label-default">SELESAI</span>';
                } else {
                    return '<span class="label label-danger">TOLAK</span>';
                }
            });

            $crud->display_as('pelanggan_id', 'Pelanggan');
            $crud->display_as('penjual_jasa_id', 'Penjual Jasa');

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

    public function get_klasifikasi()
    {
        $opd_id        = $this->input->get('opd_id');
        $klasifikasi   = $this->db->get_where('klasifikasi', array('opd_id' => $opd_id));
        $select_option = "<option value=''>Pilih klasifikasi</option>";
        foreach ($klasifikasi->result_array() as $k) {
            $select_option .= "<option value='" . $k['id'] . "'>" . $k['nama'] . "</option>";
        }

        echo $select_option;
    }

    //<berita>
    public function berita()
    {
        try {
            $this->load->library(array(
                'grocery_CRUD',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('berita');
            $crud->set_subject('Data Berita');
            $crud->order_by('dibuat', 'DESC');

            $state = $crud->getState();

            $crud->required_fields('judul', 'konten');
            $crud->columns('judul', 'konten', 'dibuat');

            $crud->field_type('dibuat', 'readonly');
            $crud->field_type('diupdate', 'readonly');
            $crud->field_type('slug', 'hidden');

            $crud->set_field_upload('gambar', 'uploads/berita');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Berita', '/admin/berita');

            $crud->callback_before_delete(function ($primary_key) {
                $berita = $this->db->get_where('berita', array(
                    'id' => $primary_key,
                ))->row_array();
                @unlink('uploads/berita/' . $berita['gambar']);
            });

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/blogs/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/blogs/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Berita',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function kegiatan()
    {
        try {
            $this->load->library(array(
                'grocery_CRUD',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('kegiatan');
            $crud->set_subject('Data Kegiatan');
            $crud->order_by('dibuat', 'DESC');

            $state = $crud->getState();

            $crud->required_fields('judul', 'tgl_mulai', 'tgl_selesai', 'jam', 'lokasi', 'isi');
            $crud->columns('judul', 'tgl_mulai', 'tgl_selesai');

            $crud->field_type('dibuat', 'readonly');
            $crud->field_type('diupdate', 'readonly');
            $crud->field_type('slug', 'hidden');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Kegiatan', '/admin/kegiatan');

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/kegiatan/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/kegiatan/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Kegiatan',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function get_details($table_name, $where, $collumn)
    {
        $get = $this->db->get_where($table_name, $where)->row_array();
        return $get[$collumn];
    }

    //<akuisisi>
    public function akuisisi($opd_id)
    {
        try {
            $this->load->library(array(
                'grocery_CRUD',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('akuisisi');
            $crud->set_subject('Data Akuisisi');
            $crud->where('opd_id', $opd_id);
            $crud->order_by('tgl_terima_surat', 'DESC');

            $state = $crud->getState();

            $crud->required_fields('nomor_surat', 'tgl_terima_surat', 'tgl_pengiriman_surat', 'rincian', 'jumlah', 'keterangan');
            $crud->columns('nomor_surat', 'tgl_terima_surat', 'rincian', 'jumlah');
            // $crud->set_relation('opd_id', 'opd', 'nama');
            // $crud->field_type('dibuat', 'readonly');
            // $crud->field_type('slug', 'hidden');
            // $crud->display_as('opd_id', 'OPD');
            $crud->display_as('tgl_terima_surat', 'Penerimaan');
            $crud->display_as('tgl_pengiriman_arsip', 'Pengiriman');

            $crud->field_type('opd_id', 'hidden', $opd_id);

            $crud->set_field_upload('file_berita_acara', 'uploads/akuisisi');

            $nama_opd = $this->get_details('opd', array('id' => $opd_id), 'nama');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD', '/admin/opd');
            $this->breadcrumbs->push('Data Akuisisi ' . $nama_opd, '/admin/akuisisi/' . $opd_id);

            $crud->callback_before_delete(function ($primary_key) {
                $akuisisi = $this->db->get_where('akuisisi', array(
                    'id' => $primary_key,
                ))->row_array();
                @unlink('uploads/akuisisi/' . $akuisisi['file_berita_acara']);
            });

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/akuisisi/' . $opd_id . '/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/akuisisi/' . $opd_id . '/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Akuisisi',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    //</akuisisi>

    private function remove_substring($string)
    {
        $remove_me = array(
            "SK Tentang Surat Ijin Usaha Perdagangan ( SIUP )",
            "SK Tentang Gangguan ( HO )",
            "SK Tentang Surat Ijin Tempat Usaha ( SITU )",
            "SK Tentang Surat Ijin Usaha Jasa Konstruksi ( SIUJK )",
            "SK Tentang Surat Tanda Daftar Perusahaan (TDP)",
            "SK Tentang Surat Ijin Usaha Perdagangan ( SIUP )",
        );
        return str_ireplace($remove_me, "", $string);
    }

    public function import_dpa()
    {
        if (!empty($_POST)) {

            $opd_id         = $this->input->post('opd_id');
            $klasifikasi_id = $this->input->post('klasifikasi_id');

            $conf['upload_path']   = './tmp';
            $conf['allowed_types'] = 'xls';

            $this->load->library('upload', $conf);

            if (!$this->upload->do_upload('userfile')) {

                // echo $this->upload->display_errors();
                // var_dump($this->upload->data());
                // exit(0);

                $this->alert->set('alert-danger', "Ada masalah dengan file yang anda import", false);
                redirect(site_url('admin/import-dpa'), 'reload');

            } else {
                ini_set('memory_limit', '-1');
                include_once APPPATH . 'libraries/excel_reader2.php';
                // echo FCPATH . 'tmp/test_import.xls';
                $xl_data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);

                //data dimulai pada row 13
                $start_row = 13;
                $end_row   = 0;

                $current_nomor_urut = 0;
                for ($i = 13; $i <= ($xl_data->rowcount($sheet_index = 0)); ++$i) {
                    $nomor_urut = $xl_data->val($i, 1);

                    if (is_numeric($nomor_urut)) {

                        if ($i == $start_row) {
                            $current_nomor_urut = $xl_data->val($i, 1);
                            continue;
                        } else {

                            $end_row = $i - 1;
                            // echo $start_row . ' s/d ' . $end_row . '<br />';

                            $rincian_masalah = "";

                            // $inserted_data = array();

                            for ($j = $start_row; $j <= ($end_row); ++$j) {

                                $current_rincian_masalah = $xl_data->val($j, 3);
                                $rincian_masalah .= "\n" . $current_rincian_masalah;

                                $array_current_rincian_masalah = explode(' ', trim($current_rincian_masalah));
                                $cek                           = count(array_filter($array_current_rincian_masalah, 'strlen'));
                                if ($cek == 2 && is_numeric($array_current_rincian_masalah[1])) {
                                    // echo "hit! <br />";
                                    if (strlen(trim($this->remove_substring($rincian_masalah))) > 10) {

                                        $inserted_rincian_masalah = $this->remove_substring($rincian_masalah);
                                        //remove "Tahun xxxx"
                                        $remove_me = array(
                                            $array_current_rincian_masalah[0] . " " . $array_current_rincian_masalah[1],
                                        );
                                        $inserted_rincian_masalah = str_ireplace($remove_me, "", $inserted_rincian_masalah);

                                        //remove "Tahun xxxx" phase 2
                                        // echo strtolower(substr($inserted_rincian_masalah,0,6)) . "<br />";
                                        if (trim(strtolower(substr($inserted_rincian_masalah, 0, 6))) === 'tahun') {
                                            $inserted_rincian_masalah = substr(trim($inserted_rincian_masalah), 10);
                                            // echo "hit!";
                                        }

                                        // $this->db->insert('dpa',
                                        //     array(
                                        //       'nomor_urut' => $current_nomor_urut,
                                        //       'rincian_masalah' => trim($inserted_rincian_masalah),
                                        //       'tahun' => $array_current_rincian_masalah[1]
                                        //     )
                                        // );

                                        $insert_query = $this->db->insert_string('dpa', array(
                                            'opd'             => $opd_id,
                                            'klasifikasi_id'  => $klasifikasi_id,
                                            'nomor_urut'      => $current_nomor_urut,
                                            'rincian_masalah' => trim($inserted_rincian_masalah),
                                            'tahun'           => $array_current_rincian_masalah[1],
                                            'status'          => 'selesai',
                                        ));
                                        $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
                                        $this->db->query($insert_query);

                                        // $this->db->query(
                                        //   'INSERT INTO dpa (nomor_urut,rincian_masalah,tahun)
                                        //    VALUES(' . $current_nomor_urut . ', "' . trim($inserted_rincian_masalah) .'", ' . $array_current_rincian_masalah[1] . ')
                                        //    ON DUPLICATE KEY UPDATE rincian_masalah="' . trim($inserted_rincian_masalah) . '"');

                                        //
                                        // $inserted_data[] =
                                        //     array(
                                        //       'nomor_urut' => $current_nomor_urut,
                                        //       'rincian_masalah' => trim($inserted_rincian_masalah),
                                        //       'tahun' => $array_current_rincian_masalah[1]
                                        //     );
                                        $rincian_masalah = "";

                                    }

                                }

                            }

                            //insert batch
                            // $errors = array_filter($inserted_data);
                            // if (!empty($errors)) {
                            //   $this->db->delete('dpa',array('nomor_urut' => $current_nomor_urut));
                            //   $this->db->insert_batch('dpa', $inserted_data);
                            //   $inserted_data = array();
                            // }

                            $start_row          = $end_row;
                            $current_nomor_urut = $nomor_urut;

                        }

                    }

                    // fix bug
                    if ($i == $xl_data->rowcount($sheet_index = 0)) {

                        $nomor_urut = $xl_data->val($i, 1);

                        for ($k = $start_row; $k <= $xl_data->rowcount($sheet_index = 0); ++$k) {

                            $current_rincian_masalah = $xl_data->val($k, 3);
                            $rincian_masalah .= "\n" . $current_rincian_masalah;

                            $array_current_rincian_masalah = explode(' ', trim($current_rincian_masalah));
                            $cek                           = count(array_filter($array_current_rincian_masalah, 'strlen'));
                            if ($cek == 2 && is_numeric($array_current_rincian_masalah[1])) {
                                // echo "hit! <br />";
                                if (strlen(trim($this->remove_substring($rincian_masalah))) > 10) {

                                    $inserted_rincian_masalah = $this->remove_substring($rincian_masalah);
                                    //remove "Tahun xxxx"
                                    $remove_me = array(
                                        $array_current_rincian_masalah[0] . " " . $array_current_rincian_masalah[1],
                                    );
                                    $inserted_rincian_masalah = str_ireplace($remove_me, "", $inserted_rincian_masalah);

                                    //remove "Tahun xxxx" phase 2
                                    if (trim(strtolower(substr($inserted_rincian_masalah, 0, 6))) === 'tahun') {
                                        $inserted_rincian_masalah = substr(trim($inserted_rincian_masalah), 10);
                                        // echo "hit!";
                                    }

                                    // $this->db->insert('dpa',
                                    //     array(
                                    //       'nomor_urut' => $current_nomor_urut,
                                    //       'rincian_masalah' => trim($inserted_rincian_masalah),
                                    //       'tahun' => $array_current_rincian_masalah[1]
                                    //     )
                                    // );

                                    $insert_query = $this->db->insert_string('dpa', array(
                                        'opd'             => $opd_id,
                                        'klasifikasi_id'  => $klasifikasi_id,
                                        'nomor_urut'      => $current_nomor_urut,
                                        'rincian_masalah' => trim($inserted_rincian_masalah),
                                        'tahun'           => $array_current_rincian_masalah[1],
                                        'status'          => 'selesai',
                                    ));
                                    $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
                                    $this->db->query($insert_query);

                                    // $this->db->query(
                                    //   'INSERT INTO dpa (nomor_urut,rincian_masalah,tahun)
                                    //    VALUES(' . $current_nomor_urut . ', "' . trim($inserted_rincian_masalah) .'", ' . $array_current_rincian_masalah[1] . ')
                                    //    ON DUPLICATE KEY UPDATE rincian_masalah="' . trim($inserted_rincian_masalah) . '"');

                                    $rincian_masalah = "";
                                }

                            }

                        }

                    }

                }

                $this->alert->set('alert-success', "Import berhasil dilakukan", false);
                redirect(site_url('admin/dpa/' . $opd_id), 'reload');
            }

        } else {
            // $this->breadcrumbs->push('Dashboard', '/admin');
            // $this->breadcrumbs->push('Dashboard', '/admin/import-dpa');

            $opd_id   = $this->uri->segment(3);
            $nama_opd = $this->get_details('opd', array('id' => $opd_id), 'nama');

            // $crud->field_type('opd', 'hidden',$opd_id);

            // $crud->set_field_upload('file_berkas', 'uploads/dpa');
            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD', '/admin/opd');
            $this->breadcrumbs->push('Data DPA ' . $nama_opd, '/admin/dpa/' . $opd_id);
            $this->breadcrumbs->push('Import DPA', '/admin/import-dpa');

            $data['breadcrumbs'] = $this->breadcrumbs->show();

            $data['opd']         = $this->db->get('opd');
            $data['klasifikasi'] = $this->db->get('klasifikasi');

            $data['page_name']  = 'import_dpa';
            $data['page_title'] = 'Import DPA';
            $this->_page_output($data);
        }
    }

    public function dpa_rekap_statistik()
    {
        $opd = $this->db->get_where('opd', array('id' => $this->uri->segment(3)));

        if ($opd->num_rows() == 0) {
            redirect(site_url('admin/index'), 'reload');
        }

        $data['opd'] = $opd->row_array();

        $this->breadcrumbs->push('Dashboard', '/admin');
        $this->breadcrumbs->push('OPD', '/admin/opd');
        $this->breadcrumbs->push('Rekap DPA', 'admin/dpa_rekap_statistik/' . $data['opd']['id']);
        // $this->breadcrumbs->push('Rekap DPA','admin/dpa_rekap_statistik/1
        // $this->breadcrumbs->push('Data Klasifikasi ' . $nama_opd, '/admin/klasifikasi/' . $opd_id);

        $data['page_name']  = 'dpa_rekap_statistik';
        $data['page_title'] = 'DPA Rekap Statistik';
        $this->_page_output($data);
    }

    public function dpa($opd_id, $status = 'semua')
    {
        try {
            $this->load->library(array(
                'grocery_CRUD',
                'Grocery_Btn',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('dpa');
            $crud->set_subject('DPA');

            $crud->where('opd', $opd_id);

            if ($status != 'semua') {
                $crud->where('status', $status);
            }

            $state = $crud->getState();

            $crud->required_fields('klasifikasi_id', 'rincian_masalah', 'tahun', 'nomor_urut');
            $crud->set_relation('klasifikasi_id', 'klasifikasi', 'nama');
            // $crud->set_relation('opd_id', 'opd', 'nama');

            // $this->grocery_btn->push(site_url('admin/klasifikasi'), 'Klasifikasi');
            $this->grocery_btn->push(site_url('admin/import-dpa/' . $opd_id), 'Import DPA');

            if ($status === 'pending') {
                $crud->columns('klasifikasi_id', 'rincian_masalah', 'tahun', 'status', 'upload_berkas', 'aksi');

                $crud->callback_column('aksi', function ($value, $row) {
                    return '<a href="" class="btn btn-info" data-toggle="modal" style="color:rgb(255, 255, 255)" onclick="dpa_pending_detail(\'' . $row->id . '\')">Periksa</a>';
                });

            } else {
                $crud->columns('klasifikasi_id', 'rincian_masalah', 'tahun', 'status', 'upload_berkas');

            }

            if ($status === 'disetujui') {
                $crud->columns('klasifikasi_id', 'rincian_masalah', 'tahun', 'status', 'upload_berkas', 'update_status');

                $crud->callback_column('update_status', function ($value, $row) {
                    return '<a href="' . site_url('admin/set_dpa_selesai/' . $row->id . '/dpa') . '" class="btn btn-info" style="color:rgb(255, 255, 255)">Selesai</a>';
                });
            }

            $crud->callback_column('status', function ($value, $row) {
                switch ($value) {
                    case 'pending':
                        return '<span class="label label-default">Pending</span>';
                        break;

                    case 'koreksi':
                        return '<span class="label label-danger">Koreksi</span>';
                        break;

                    case 'disetujui':
                        return '<span class="label label-info">Disetujui</span>';
                        break;

                    case 'selesai':
                        return '<span class="label label-success">Selesai</span>';
                        break;
                }
            });

            $crud->callback_column('upload_berkas', function ($value, $row) {
                $params = cloudinary_params('opd', $row->opd);
                $this->load->library('cloudinarylib', $params);

                $this->db->select('a.hash_rincian_masalah,a.image_url');
                $this->db->join('dpa b', 'md5(b.rincian_masalah) = a.hash_rincian_masalah', 'left');
                $dpa_file_berkas = $this->db->get_where('dpa_file_berkas a', array('b.id' => $row->id))->row_array();

                $image_url = file_pathinfo($dpa_file_berkas['image_url']);
                $link      = '<a class=" fancybox" rel="ligthbox-' . $row->id . '" href="' . $dpa_file_berkas['image_url'] . '"  target="_blank">';
                $link .= 'Lihat berkas';
                $link .= '</a>';

                $form = form_open_multipart('admin/do_upload_berkas/' . $row->id);
                $form .= '<input class="upload" name="img" onchange="this.form.submit()" type="file">';
                $form .= form_close();

                if (empty($dpa_file_berkas['image_url'])) {
                    return $form;
                } else {
                    return $link . $form;
                }
            });

            $crud->field_type('tgl_masuk', 'readonly');
            $crud->display_as('klasifikasi_id', 'Klasifikasi');
            $crud->display_as('klasifikasi_jra', 'Klasifikasi JRA');
            // $crud->display_as('opd_id', 'OPD');

            $nama_opd = $this->get_details('opd', array('id' => $opd_id), 'nama');

            $crud->field_type('opd', 'hidden', $opd_id);
            $crud->field_type('koreksi', 'hidden');

            // $crud->set_field_upload('file_berkas', 'uploads/dpa');
            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD', '/admin/opd');
            $this->breadcrumbs->push('Rekap DPA', 'admin/dpa_rekap_statistik/' . $opd_id);
            $this->breadcrumbs->push('Detail Data DPA ' . $nama_opd, '/admin/dpa/' . $opd_id);

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/dpa/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/dpa/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'grocery_btn' => $this->grocery_btn->show(),
                'page_title'  => 'Data DPA',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function do_upload_berkas($dpa_id)
    {

        $dpa = $this->db->get_where('dpa', array('id' => $dpa_id))->row_array();

        $params = cloudinary_params('opd', $dpa['opd']);
        $this->load->library('cloudinarylib', $params);
        try {
            $imageupload = \Cloudinary\Uploader::upload($_FILES["img"]["tmp_name"]);
            $image_url   = $imageupload['secure_url'];

            $cek = $this->db->get_where('dpa_file_berkas', array('hash_rincian_masalah' => md5($dpa['rincian_masalah'])));
            if ($cek->num_rows() > 0) {

                $this->db->where('hash_rincian_masalah', md5($dpa['rincian_masalah']));
                $this->db->update('dpa_file_berkas', array('image_url' => $image_url));

            } else {
                $this->db->insert('dpa_file_berkas',
                    array(
                        'hash_rincian_masalah' => md5($dpa['rincian_masalah']),
                        'image_url'            => $image_url,
                        'cloudinary_name'      => $params['cloud_name'],
                        'cloudinary_key'       => $params['api_key'],
                        'cloudinary_secret'    => $params['api_secret'],
                    )
                );
            }

            redirect('admin/dpa/' . $dpa['opd']);

        } catch (Exception $e) {
            $data['msg'] = 'ERROR : bukan file image !';
        }

    }

    public function galeri()
    {
        $this->breadcrumbs->push('Dashboard', '/admin');
        $this->breadcrumbs->push('Galeri', '/admin/galeri');

        $data['breadcrumbs'] = $this->breadcrumbs->show();

        $data['page_name']  = 'galeri';
        $data['page_title'] = 'Beranda';
        $this->_page_output($data);
    }

    public function galeri_dokumen()
    {
        try {
            $this->load->library(array(
                'grocery_CRUD', 'Grocery_Btn',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('galeri');
            $crud->set_subject('Data Galeri Dokumen');
            $crud->where('jenis', 'dokumen');
            $crud->order_by('dibuat', 'DESC');

            $state = $crud->getState();

            $crud->required_fields('judul');
            $crud->columns('judul', 'keterangan', 'dibuat');

            // $this->grocery_btn->push(site_url('admin/galeri-foto/add'), 'Galeri Foto');
            // $this->grocery_btn->push(site_url('admin/galeri-dokumen/add'), 'Galeri Dokumen');
            // $this->grocery_btn->push(site_url('admin/galeri-video/add'), 'Galeri Video');
            //
            // // $crud->callback_column('foto', function($value, $row)
            // {
            //     return '<a href="' . site_url('admin/galeri-foto/' . $row->id) . '">Kelola</a>';
            // });
            //
            // $crud->callback_column('dokumen', function($value, $row)
            // {
            //     return '<a href="' . site_url('admin/galeri-dokumen/' . $row->id) . '">Kelola</a>';
            // });
            //
            // $crud->callback_column('video', function($value, $row)
            // {
            //     return '<a href="' . site_url('admin/galeri-video/' . $row->id) . '">Kelola</a>';
            // });

            $crud->set_field_upload('file_link', 'uploads/galeri');

            $crud->change_field_type('latitude', 'hidden');
            $crud->change_field_type('longitude', 'hidden');
            $crud->change_field_type('map', 'hidden');

            $crud->change_field_type('dibuat', 'readonly');
            $crud->change_field_type('diupdate', 'readonly');

            $crud->change_field_type('jenis', 'hidden', 'dokumen');

            // $crud->unset_add();

            // $crud->callback_before_insert(array($this, 'unset_map_field_add'));
            // $crud->callback_before_update(array($this, 'unset_map_field_edit'));

            // $crud->callback_before_delete(function($primary_key)
            // {
            //     $files = $this->db->get_where('galeri_detail', array(
            //         'galeri_id' => $primary_key
            //     ));
            //     foreach ($files->result_array() as $file) {
            //         if (in_array($file['jenis'], array(
            //             'foto',
            //             'dokumen'
            //         ))) {
            //             @unlink('uploads/galeri/' . $file['file_link']);
            //         }
            //     }
            //
            //     $this->db->delete('galeri_detail', array(
            //         'galeri_id' => $primary_key
            //     ));
            //
            // });

            // $crud->field_type('dibuat', 'readonly');
            // $crud->field_type('diupdate', 'readonly');
            $crud->field_type('slug', 'readonly');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Galeri', '/admin/galeri');
            $this->breadcrumbs->push('Dokumen', '/admin/galeri-dokumen');

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/galeri-dokumen/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/galeri-dokumen/add');
            }

            $crud->callback_before_delete(function ($primary_key) {
                $galeri = $this->db->get_where('galeri', array('id' => $primary_key))->row_array();
                @unlink('uploads/galeri/' . $galeri['file_link']);
            });

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'grocery_btn' => $this->grocery_btn->show(),
                'page_title'  => 'Produk Hukum',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    /*
    public function galeri_dokumen()
    {
    try {
    $this->load->library('image_CRUD');
    $image_crud = new image_CRUD();

    $image_crud->set_table('galeri_detail');

    $image_crud->set_primary_key_field('id');
    $image_crud->set_url_field('file_link');

    $image_crud->where('jenis', 'dokumen');

    $image_crud->set_relation_field('galeri_id');

    // $image_crud->set_ordering_field('priority');
    $image_crud->set_image_path('uploads/galeri');
    $image_crud->set_title_field('judul');

    $this->breadcrumbs->push('Dashboard', '/admin');
    $this->breadcrumbs->push('Galeri', '/admin/galeri');

    $galeri_id = $this->uri->segment(3);
    @$galeri = $this->db->get_where('galeri', array(
    'id' => $galeri_id
    ), 'judul')->row_array();

    $this->breadcrumbs->push('Dokumen ' . $galeri['judul'], '/admin/galeri-dokumen/' . $galeri_id);

    $extra = array(
    'breadcrumbs' => $this->breadcrumbs->show(),
    'page_title' => 'Data Galeri'
    );

    $output = $image_crud->render();

    $output = array_merge((array) $output, $extra);

    $this->_page_output($output);
    }
    catch (Exception $e) {
    show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
    }
     */

    /*
    public function galeri_foto()
    {
    try {
    $this->load->library('image_CRUD');
    $image_crud = new image_CRUD();

    $image_crud->set_table('galeri_detail');

    $image_crud->set_primary_key_field('id');
    $image_crud->set_url_field('file_link');

    $image_crud->set_relation_field('galeri_id');

    $image_crud->where('jenis', 'foto');
    // $image_crud->set_ordering_field('priority');
    $image_crud->set_image_path('uploads/galeri');
    $image_crud->set_title_field('judul');

    $this->breadcrumbs->push('Dashboard', '/admin');
    $this->breadcrumbs->push('Galeri', '/admin/galeri');

    $galeri_id = $this->uri->segment(3);
    @$galeri = $this->db->get_where('galeri', array(
    'id' => $galeri_id
    ), 'judul')->row_array();

    $this->breadcrumbs->push('Foto ' . $galeri['judul'], '/admin/galeri-foto/' . $galeri_id);

    $extra = array(
    'breadcrumbs' => $this->breadcrumbs->show(),
    'page_title' => 'Data Galeri'
    );

    $output = $image_crud->render();

    $output = array_merge((array) $output, $extra);

    $this->_page_output($output);
    }
    catch (Exception $e) {
    show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    }
    }
     */

    public function preservasi($opd_id)
    {
        try {
            $this->load->library(array(
                'grocery_CRUD',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('preservasi');
            $crud->set_subject('Data Preservasi');
            $crud->order_by('tgl_terima_surat', 'DESC');
            $crud->where('opd_id', $opd_id);

            $state = $crud->getState();

            $crud->required_fields('nomor_surat', 'tgl_terima_surat', 'tgl_pengiriman_surat', 'rincian', 'jumlah', 'keterangan');
            $crud->columns('nomor_surat', 'tgl_terima_surat', 'rincian', 'jumlah', 'status');
            // $crud->set_relation('opd_id', 'opd', 'nama');
            // $crud->field_type('dibuat', 'readonly');
            // $crud->field_type('slug', 'hidden');
            // $crud->display_as('opd_id', 'OPD');
            $crud->display_as('tgl_terima_surat', 'Penerimaan');
            $crud->display_as('tgl_pengiriman_arsip', 'Pengiriman');
            $crud->field_type('opd_id', 'hidden', $opd_id);

            $crud->set_field_upload('file_berita_acara', 'uploads/preservasi');

            $nama_opd = $this->get_details('opd', array('id' => $opd_id), 'nama');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD', '/admin/opd');
            $this->breadcrumbs->push('Data Preservasi ' . $nama_opd, '/admin/preservasi/' . $opd_id);
            // $crud->field_type('status', 'readonly');

            $crud->callback_before_delete(function ($primary_key) {
                $preservasi = $this->db->get_where('preservasi', array(
                    'id' => $primary_key,
                ))->row_array();
                @unlink('uploads/preservasi/' . $preservasi['file_berita_acara']);
            });

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/preservasi/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/preservasi/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Berita',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function klasifikasi($opd_id)
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('klasifikasi');
            $crud->set_subject('Data Klasifikasi');
            $crud->where('opd_id', $opd_id);

            $nama_opd = $this->get_details('opd', array('id' => $opd_id), 'nama');

            $state = $crud->getState();

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD', '/admin/opd');
            $this->breadcrumbs->push('Data Klasifikasi ' . $nama_opd, '/admin/klasifikasi/' . $opd_id);

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/klasifikasi/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/klasifikasi/add');
            }

            $crud->required_fields('nama');
            $crud->columns('nama', 'keterangan');

            $crud->field_type('opd_id', 'hidden', $opd_id);

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Data Klasifikasi',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function konten()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('menu');
            $crud->set_subject('Data Konten');

            $state = $crud->getState();

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data konten', '/admin/konten');

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/konten/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/konten/add');
            }

            $crud->required_fields('posisi', 'judul', 'isi');
            $crud->field_type('slug', 'hidden');
            $crud->set_field_upload('gambar', 'uploads');
            $crud->columns('posisi', 'judul', 'isi');
            $crud->field_type('updated_at', 'readonly');

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Data Konten',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function link_terkait()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('link_terkait');
            $crud->set_subject('Data Link Terkait');

            $state = $crud->getState();

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Data konten', '/admin/link_terkait');

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/link_terkait/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/link_terkait/add');
            }

            $crud->required_fields('nama', 'url');
            $crud->columns('nama', 'url');

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Data Link Terkait',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    // public function akun_cloudinary()
    // {
    //     try {
    //         $this->load->library('grocery_CRUD');
    //         $crud = new Grocery_CRUD();
    //
    //         $crud->set_table('akun_cloudinary');
    //         $crud->set_subject('Data Akun Cloudinary');
    //
    //         $state = $crud->getState();
    //
    //         $this->breadcrumbs->push('Dashboard', '/admin');
    //         $this->breadcrumbs->push('Data Akun Cloudinary', '/admin/akun_cloudinary');
    //
    //         if ($state === 'edit') {
    //             $this->breadcrumbs->push('Edit', '/admin/akun_cloudinary/edit');
    //         } elseif ($state === 'add') {
    //             $this->breadcrumbs->push('Tambah', '/admin/akun_cloudinary/add');
    //         }
    //
    //         $crud->required_fields('cloud_name', 'api_key','api_secret');
    //         $crud->field_type('last_used', 'readonly');
    //
    //         $extra  = array(
    //             'breadcrumbs' => $this->breadcrumbs->show(),
    //             'page_title' => 'Data Akun Cloudinary'
    //         );
    //         $output = $crud->render();
    //
    //         $output = array_merge((array) $output, $extra);
    //
    //         $this->_page_output($output);
    //     }
    //     catch (Exception $e) {
    //         show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    //     }
    // }
    //

    public function opd()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('opd');
            $crud->set_subject('OPD');

            $state = $crud->getState();

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD', '/admin/opd');

            $crud->callback_column('klasifikasi', function ($value, $row) {
                return '<a href="' . site_url('admin/klasifikasi/' . $row->id) . '">Kelola</a>';
            });

            $crud->callback_column('akuisisi', function ($value, $row) {
                return '<a href="' . site_url('admin/akuisisi/' . $row->id) . '">Kelola</a>';
            });

            $crud->callback_column('preservasi', function ($value, $row) {
                return '<a href="' . site_url('admin/preservasi/' . $row->id) . '">Kelola</a>';
            });

            // $crud->callback_column('dpa', function ($value, $row) {
            //     return '<a href="' . site_url('admin/dpa/' . $row->id) . '/semua">Kelola</a>';
            // });

            $crud->callback_column('dpa', function ($value, $row) {
                return '<a href="' . site_url('admin/dpa_rekap_statistik/' . $row->id) . '">Kelola</a>';
            });

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/opd/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/opd/add');
            }

            $crud->required_fields('kode', 'username', 'password', 'nama', 'cloudinary_name', 'cloudinary_key', 'cloudinary_secret');
            $crud->edit_fields('kode', 'nama', 'email', 'telp', 'cp_nama', 'cp_telp', 'cloudinary_name', 'cloudinary_key', 'cloudinary_secret');
            $crud->add_fields('kode', 'username', 'password', 'nama', 'email', 'telp', 'cp_nama', 'cp_telp', 'cloudinary_name', 'cloudinary_key', 'cloudinary_secret');

            $crud->callback_before_insert(function ($post_array, $primary_key = null) {
                $post_array['password'] = md5($post_array['password']);

                return $post_array;
            });

            $crud->columns('kode', 'nama', 'klasifikasi', 'dpa', 'akuisisi', 'preservasi');

            $crud->unset_read_fields('password');

            $crud->display_as('cp_nama', 'Contact Person');

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Akun Organisasi Perangkat Daerah',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    //<peminjaman>
    public function peminjaman()
    {
        try {
            $this->load->library(array(
                'grocery_CRUD',
            ));
            $crud = new Grocery_CRUD();

            $crud->set_table('peminjaman');
            $crud->set_subject('Peminjaman Arsip');
            $crud->order_by('tgl_peminjaman', 'ASC');

            $state = $crud->getState();

            $crud->required_fields('nomor_peminjam', 'nama', 'instansi_alamat', 'jenis_arsip');
            $crud->columns('nomor_peminjam', 'tgl_peminjaman', 'nama', 'instansi_alamat', 'jenis_arsip', 'detail');
            $crud->field_type('tgl_peminjaman', 'readonly');

            $crud->display_as('instansi_alamat', 'Instansi / Alamat');
            $crud->display_as('tgl_peminjaman', 'Tanggal');

            $crud->callback_column('detail', function ($value, $row) {
                return '<a href="' . site_url('admin/detail-peminjaman/' . $row->id) . '">Kelola</a>';
            });

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Peminjaman', '/admin/peminjaman');

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/peminjaman/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/peminjaman/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Produk',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    //</peminjaman>

    //<detail-peminjaman>
    public function detail_peminjaman($peminjaman_id)
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('peminjaman_detail');
            $crud->set_subject('Detail peminjaman');

            $crud->where('peminjaman_id', $peminjaman_id);

            $crud->columns('uraian', 'jumlah', 'copy', 'pinjam', 'keterangan');

            $crud->required_fields('uraian', 'jumlah');

            $crud->field_type('peminjaman_id', 'hidden', $peminjaman_id);

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Peminjaman', '/admin/peminjaman');
            $this->breadcrumbs->push('Detail', '/admin/detail-peminjaman/' . $peminjaman_id);

            $state = $crud->getState();
            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/detail-peminjaman/' . $peminjaman_id . '/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/detail-peminjaman/' . $peminjaman_id . '/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Detail peminjaman',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    //</detail-peminjaman>

    public function galeri_video()
    {
        try {
            $this->load->library('grocery_CRUD');
            $crud = new Grocery_CRUD();

            $crud->set_table('galeri');
            $crud->set_subject('Data Galeri Video');
            $crud->where('jenis', 'video');

            $crud->columns('judul', 'keterangan', 'dibuat');

            $crud->required_fields('judul', 'file_link');

            $crud->field_type('jenis', 'hidden', 'video');

            $crud->display_as('file_link', 'Youtube Video ID');

            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('Galeri', '/admin/galeri');

            $this->breadcrumbs->push('Video ', '/admin/galeri-video/');

            $crud->change_field_type('latitude', 'hidden');
            $crud->change_field_type('longitude', 'hidden');

            $crud->callback_add_field('map', array($this, 'show_map_field'));
            $crud->callback_edit_field('map', array($this, 'show_map_field'));

            $crud->change_field_type('dibuat', 'readonly');
            $crud->change_field_type('diupdate', 'readonly');

            $state = $crud->getState();

            if ($crud->getState() === 'read') {
                $crud->set_js('http://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4&sensor=false&libraries=places"', false);
                $crud->set_js("admin/plot_point_js");
                // $crud->set_js("admin/resize_map/px/230");

            } elseif ($crud->getState() === 'add' || $crud->getState() === 'edit' || $crud->getState() === 'copy') {
                $crud->set_js("assets/manage/js/map.js?v=" . date("YmdHis"));
                $crud->set_js('http://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4&sensor=false&libraries=places"', false);
                // $crud->set_js("admin/resize_map/percent/100");
            }

            if ($state === 'edit') {
                $this->breadcrumbs->push('Edit', '/admin/galeri-video/edit');
            } elseif ($state === 'add') {
                $this->breadcrumbs->push('Tambah', '/admin/galeri-video/add');
            }

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Galeri video',
            );

            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    //<profile>
    public function profile()
    {
        $user_id = $this->session->userdata('user_id');

        if (!empty($_POST)) {
            $this->form_validation->set_rules('nama_lengkap', 'Nama lengkap', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

            if ($this->form_validation->run() == true) {

                if (!empty($_POST['pass_lama'])) {
                    $password = md5($this->input->post('pass_lama'));

                    $cek_user = $this->db->get_where('admin', array(
                        'id' => $user_id,
                    ))->row_array();

                    if ($password === $cek_user['p']) {
                        if (empty($_POST['pass_baru']) || empty($_POST['pass_ulangi'])) {
                            // $data['msg'] = array(
                            //     'content' => 'Password Baru / Ulangi tidak boleh kosong',
                            //     'css_class' => 'alert alert-danger'
                            // );

                            $this->alert->set('alert-danger', "Password baru / ulangi tidak boleh kosong", true);

                        } else {
                            $pass_baru   = $this->input->post('pass_baru');
                            $pass_ulangi = $this->input->post('pass_ulangi');

                            if ($pass_baru !== $pass_ulangi) {
                                // $data['msg'] = array(
                                //     'content' => 'Password Baru & Ulangi Harus Sama!',
                                //     'css_class' => 'alert alert-danger'
                                // );
                                $this->alert->set('alert-danger', "Password baru dan ulangi harus sama", true);
                            } else {
                                $realname = $this->input->post('nama_lengkap');
                                $email    = $this->input->post('email');

                                $this->db->where('id', $user_id);
                                $this->db->update('admin', array(
                                    'password'     => md5($pass_ulangi),
                                    'nama_lengkap' => $realname,
                                    'email'        => $email,
                                ));

                                // $data['msg'] = array(
                                //     'content' => 'Profile berhasil diupdate',
                                //     'css_class' => 'alert alert-success'
                                // );
                                $this->alert->set('alert-success', "Profile berhasil diubah", true);
                            }
                        }
                    } else {
                        // $data['msg'] = array(
                        //     'content' => 'Password Lama Salah',
                        //     'css_class' => 'alert alert-danger'
                        // );
                        $this->alert->set('alert-danger', "Password lama salah", true);
                    }
                } else {
                    $realname = $this->input->post('nama_lengkap');
                    $email    = $this->input->post('email');

                    $this->db->where('id', $user_id);
                    $this->db->update('admin', array(
                        'nama_lengkap' => $realname,
                        'email'        => $email,
                    ));

                    // $data['msg'] = array(
                    //     'content' => 'Profile berhasil diupdate',
                    //     'css_class' => 'alert alert-success'
                    // );
                    $this->alert->set('alert-success', "Profile berhasil diubah", true);
                }
            } else {
                // $data['msg'] = array(
                //     'content' => validation_errors(),
                //     'css_class' => 'alert alert-danger'
                // );
                $this->alert->set('alert-danger', "Profile gagal diubah", true);
            }

            $data['p']           = $this->db->get_where('user', array('id' => $user_id))->row();
            $data['page_name']   = 'profile';
            $data['page_title']  = 'Profile';
            $data['breadcrumbs'] = $this->breadcrumbs->show();

            $this->_page_output($data);
        } else {
            $data['p']         = $this->db->get_where('user', array('id' => $user_id))->row();
            $data['page_name'] = 'profile';

            $this->breadcrumbs->push('Beranda', '/admin');
            $this->breadcrumbs->push('Profile', '/admin/profile');

            $data['breadcrumbs'] = $this->breadcrumbs->show();
            $data['page_title']  = 'Profile';

            $this->_page_output($data);
        }
    }
    //</profile>

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

    public function dpa_pending()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('dpa');
            $crud->set_subject('DPA');

            $crud->where('status', 'pending');

            $state = $crud->getState();

            $crud->set_relation('klasifikasi_id', 'klasifikasi', 'nama');
            $crud->set_relation('opd', 'opd', 'nama');

            $crud->columns('opd', 'klasifikasi_id', 'rincian_masalah', 'tahun', 'file_berkas', 'aksi');

            $crud->callback_column('aksi', function ($value, $row) {
                return '<a href="" class="btn btn-info" data-toggle="modal" style="color:rgb(255, 255, 255)" onclick="dpa_pending_detail(\'' . $row->id . '\')">Periksa</a>';
            });

            $crud->callback_column('file_berkas', function ($value, $row) {
                $params = cloudinary_params('opd', $row->opd);
                $this->load->library('cloudinarylib', $params);

                $this->db->select('a.hash_rincian_masalah,a.image_url');
                $this->db->join('dpa b', 'md5(b.rincian_masalah) = a.hash_rincian_masalah', 'left');
                $dpa_file_berkas = $this->db->get_where('dpa_file_berkas a', array('b.id' => $row->id))->row_array();

                $image_url = file_pathinfo($dpa_file_berkas['image_url']);
                $link      = '<a class=" fancybox" rel="ligthbox-' . $row->id . '" href="' . $dpa_file_berkas['image_url'] . '"  target="_blank">';
                $link .= 'Lihat berkas';
                $link .= '</a>';

                // $form = form_open_multipart('admin/do_upload_berkas/' . $row->id);
                // $form .= '<input class="upload" name="img" onchange="this.form.submit()" type="file">';
                // $form .= form_close();

                if (empty($dpa_file_berkas['image_url'])) {
                    return 'belum-upload';
                } else {
                    return $link/*. $form*/;
                }
            });

            $crud->callback_column('status', function ($value, $row) {
                switch ($value) {
                    case 'pending':
                        return '<span class="label label-default">Pending</span>';
                        break;

                    case 'koreksi':
                        return '<span class="label label-danger">Koreksi</span>';
                        break;

                    case 'disetujui':
                        return '<span class="label label-info">Disetujui</span>';
                        break;

                    case 'selesai':
                        return '<span class="label label-success">Selesai</span>';
                        break;
                }
            });

            $crud->field_type('tgl_masuk', 'readonly');
            $crud->display_as('klasifikasi_id', 'Klasifikasi');
            $crud->display_as('klasifikasi_jra', 'Klasifikasi JRA');
            $crud->display_as('opd', 'OPD');

            $crud->field_type('koreksi', 'hidden');

            // $crud->set_field_upload('file_berkas', 'uploads/dpa');
            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD - Pending', '/admin/opd-pending');

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_read();

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Data DPA',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function set_dpa_selesai()
    {
        $dpa_id   = $this->uri->segment(3);
        $redirect = $this->uri->segment(4);
        $this->db->where('id', $dpa_id);
        $this->db->update('dpa', array('status' => 'selesai'));

        if ($redirect === 'dashboard') {
            redirect(site_url('admin/dpa_disetujui'), 'reload');
        } else {
            $dpa = $this->db->get_where('dpa', array('id' => $dpa_id))->row_array();

            redirect(site_url('admin/dpa/' . $dpa['opd'] . '/disetujui'), 'reload');
        }

    }

    public function dpa_koreksi()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('dpa');
            $crud->set_subject('DPA');

            $crud->where('status', 'koreksi');

            $state = $crud->getState();

            $crud->set_relation('klasifikasi_id', 'klasifikasi', 'nama');
            $crud->set_relation('opd', 'opd', 'nama');

            $crud->columns('opd', 'klasifikasi_id', 'rincian_masalah', 'tahun', 'file_berkas', 'koreksi');

            // $crud->callback_column('aksi', function ($value, $row) {
            //     return '<a href="" class="btn btn-info" data-toggle="modal" style="color:rgb(255, 255, 255)" onclick="dpa_pending_detail(\'' . $row->id . '\')">Periksa</a>';
            // });

            $crud->callback_column('file_berkas', function ($value, $row) {
                $params = cloudinary_params('opd', $row->opd);
                $this->load->library('cloudinarylib', $params);

                $this->db->select('a.hash_rincian_masalah,a.image_url');
                $this->db->join('dpa b', 'md5(b.rincian_masalah) = a.hash_rincian_masalah', 'left');
                $dpa_file_berkas = $this->db->get_where('dpa_file_berkas a', array('b.id' => $row->id))->row_array();

                $image_url = file_pathinfo($dpa_file_berkas['image_url']);
                $link      = '<a class=" fancybox" rel="ligthbox-' . $row->id . '" href="' . $dpa_file_berkas['image_url'] . '"  target="_blank">';
                $link .= 'Lihat berkas';
                $link .= '</a>';

                // $form = form_open_multipart('admin/do_upload_berkas/' . $row->id);
                // $form .= '<input class="upload" name="img" onchange="this.form.submit()" type="file">';
                // $form .= form_close();

                if (empty($dpa_file_berkas['image_url'])) {
                    return 'belum-upload';
                } else {
                    return $link/*. $form*/;
                }
            });

            // $crud->callback_column('status', function ($value, $row) {
            //     switch ($value) {
            //         case 'pending':
            //             return '<span class="label label-default">Pending</span>';
            //             break;

            //         case 'koreksi':
            //             return '<span class="label label-danger">Koreksi</span>';
            //             break;

            //         case 'disetujui':
            //             return '<span class="label label-info">Disetujui</span>';
            //             break;

            //         case 'selesai':
            //             return '<span class="label label-success">Selesai</span>';
            //             break;
            //     }
            // });

            $crud->field_type('tgl_masuk', 'readonly');
            $crud->display_as('klasifikasi_id', 'Klasifikasi');
            $crud->display_as('klasifikasi_jra', 'Klasifikasi JRA');
            $crud->display_as('opd', 'OPD');

            // $crud->field_type('koreksi', 'hidden');

            // $crud->set_field_upload('file_berkas', 'uploads/dpa');
            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD - Koreksi', '/admin/opd-koreksi');

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            // $crud->unset_read();

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Data DPA',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function dpa_disetujui()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('dpa');
            $crud->set_subject('DPA');

            $crud->where('status', 'disetujui');

            $state = $crud->getState();

            $crud->set_relation('klasifikasi_id', 'klasifikasi', 'nama');
            $crud->set_relation('opd', 'opd', 'nama');

            $crud->columns('opd', 'klasifikasi_id', 'rincian_masalah', 'tahun', 'file_berkas', 'update_status');

            // $crud->callback_column('aksi', function ($value, $row) {
            //     return '<a href="" class="btn btn-info" data-toggle="modal" style="color:rgb(255, 255, 255)" onclick="dpa_pending_detail(\'' . $row->id . '\')">Periksa</a>';
            // });

            $crud->callback_column('update_status', function ($value, $row) {
                return '<a href="' . site_url('admin/set_dpa_selesai/' . $row->id . '/dashboard') . '" class="btn btn-info" style="color:rgb(255, 255, 255)">Selesai</a>';
            });

            $crud->callback_column('file_berkas', function ($value, $row) {
                $params = cloudinary_params('opd', $row->opd);
                $this->load->library('cloudinarylib', $params);

                $this->db->select('a.hash_rincian_masalah,a.image_url');
                $this->db->join('dpa b', 'md5(b.rincian_masalah) = a.hash_rincian_masalah', 'left');
                $dpa_file_berkas = $this->db->get_where('dpa_file_berkas a', array('b.id' => $row->id))->row_array();

                $image_url = file_pathinfo($dpa_file_berkas['image_url']);
                $link      = '<a class=" fancybox" rel="ligthbox-' . $row->id . '" href="' . $dpa_file_berkas['image_url'] . '"  target="_blank">';
                $link .= 'Lihat berkas';
                $link .= '</a>';

                // $form = form_open_multipart('admin/do_upload_berkas/' . $row->id);
                // $form .= '<input class="upload" name="img" onchange="this.form.submit()" type="file">';
                // $form .= form_close();

                if (empty($dpa_file_berkas['image_url'])) {
                    return 'belum-upload';
                } else {
                    return $link/*. $form*/;
                }
            });

            // $crud->callback_column('status', function ($value, $row) {
            //     switch ($value) {
            //         case 'pending':
            //             return '<span class="label label-default">Pending</span>';
            //             break;

            //         case 'koreksi':
            //             return '<span class="label label-danger">Koreksi</span>';
            //             break;

            //         case 'disetujui':
            //             return '<span class="label label-info">Disetujui</span>';
            //             break;

            //         case 'selesai':
            //             return '<span class="label label-success">Selesai</span>';
            //             break;
            //     }
            // });

            $crud->field_type('tgl_masuk', 'readonly');
            $crud->display_as('klasifikasi_id', 'Klasifikasi');
            $crud->display_as('klasifikasi_jra', 'Klasifikasi JRA');
            $crud->display_as('opd', 'OPD');

            $crud->field_type('koreksi', 'hidden');

            // $crud->set_field_upload('file_berkas', 'uploads/dpa');
            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD - Disetujui', '/admin/opd-disetujui');

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            // $crud->unset_read();

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Data DPA',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function dpa_selesai()
    {

        try {
            $this->load->library(array('grocery_CRUD'));
            $crud = new Grocery_CRUD();

            $crud->set_table('dpa');
            $crud->set_subject('DPA');

            $crud->where('status', 'selesai');

            $state = $crud->getState();

            $crud->set_relation('klasifikasi_id', 'klasifikasi', 'nama');
            $crud->set_relation('opd', 'opd', 'nama');

            $crud->columns('opd', 'klasifikasi_id', 'rincian_masalah', 'tahun', 'file_berkas');

            $crud->callback_column('file_berkas', function ($value, $row) {
                $params = cloudinary_params('opd', $row->opd);
                $this->load->library('cloudinarylib', $params);

                $this->db->select('a.hash_rincian_masalah,a.image_url');
                $this->db->join('dpa b', 'md5(b.rincian_masalah) = a.hash_rincian_masalah', 'left');
                $dpa_file_berkas = $this->db->get_where('dpa_file_berkas a', array('b.id' => $row->id))->row_array();

                $image_url = file_pathinfo($dpa_file_berkas['image_url']);
                $link      = '<a class=" fancybox" rel="ligthbox-' . $row->id . '" href="' . $dpa_file_berkas['image_url'] . '"  target="_blank">';
                $link .= 'Lihat berkas';
                $link .= '</a>';

                // $form = form_open_multipart('admin/do_upload_berkas/' . $row->id);
                // $form .= '<input class="upload" name="img" onchange="this.form.submit()" type="file">';
                // $form .= form_close();

                if (empty($dpa_file_berkas['image_url'])) {
                    return 'belum-upload';
                } else {
                    return $link/*. $form*/;
                }
            });

            // $crud->callback_column('status', function ($value, $row) {
            //     switch ($value) {
            //         case 'pending':
            //             return '<span class="label label-default">Pending</span>';
            //             break;

            //         case 'koreksi':
            //             return '<span class="label label-danger">Koreksi</span>';
            //             break;

            //         case 'disetujui':
            //             return '<span class="label label-info">Disetujui</span>';
            //             break;

            //         case 'selesai':
            //             return '<span class="label label-success">Selesai</span>';
            //             break;
            //     }
            // });

            $crud->field_type('tgl_masuk', 'readonly');
            $crud->display_as('klasifikasi_id', 'Klasifikasi');
            $crud->display_as('klasifikasi_jra', 'Klasifikasi JRA');
            $crud->display_as('opd', 'OPD');

            $crud->field_type('koreksi', 'hidden');

            // $crud->set_field_upload('file_berkas', 'uploads/dpa');
            $this->breadcrumbs->push('Dashboard', '/admin');
            $this->breadcrumbs->push('OPD - Koreksi', '/admin/opd-koreksi');

            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            // $crud->unset_read();

            $extra = array(
                'breadcrumbs' => $this->breadcrumbs->show(),
                'page_title'  => 'Data DPA',
            );
            $output = $crud->render();

            $output = array_merge((array) $output, $extra);

            $this->_page_output($output);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function dpa_detail()
    {
        header('content-type: application/json');
        $dpa_id = $this->input->get('dpa');

        $dpa_pending = $this->db->get_where('dpa', array('id' => $dpa_id))->row_array();

        echo json_encode($dpa_pending);
    }

    public function update_status_dpa()
    {

        $id       = $this->input->post('id');
        $status   = $this->input->post('status');
        $koreksi  = $this->input->post('koreksi');
        $redirect = $this->input->post('redirect');

        $this->db->where('id', $id);
        $this->db->update('dpa', array('status' => $status, 'koreksi' => $koreksi));

        redirect($redirect, 'reload');
    }

}

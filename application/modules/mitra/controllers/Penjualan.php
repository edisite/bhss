<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends Admin_Controller {
	private $any_error = array();
	// Define Main Table
	public $tbl = 't_penerimaan_barang';

	public function __construct() {
		parent::__construct();
		$files = array(            
			'assets/js/penjualan_scripts.js',               
		);
		$this->add_script($files); 
		$this->mPageTitlePrefix = 'Penjualan - ';
	}

	public function index(){
		$this->view();
	}

	public function view(){    
			$this->load->library('form_builder');
			$form = $this->form_builder->create_form($this->mModule.'/penjualan/postdata','','class="form-horizontal"');
			$this->mViewData['form'] = $form;       	
            $this->mPageTitle = "Penjualan Barang";
            $this->render('penjualan-barang/form_penjualan_barang');		
	}




	public function getForm(){
		$this->load->library('form_builder');
		$form = $this->form_builder->create_form('','','class="form-horizontal"');

		if ($form->validate())
		{
			// table t_stok_gudang
			// echo count($this->input->post('m_barang_id'));
			// echo json_encode($this->input->post());
			// return;
			$in_tanggal_terima 				= $this->input->post('tanggal_terima') ?: date('d F, Y');
			$in_m_karyawan_id 				= $this->input->post('m_karyawan_id') ?: '';
			$in_m_gudang_id 				= $this->input->post('m_gudang_id') ?: '';
			$in_no_sj 						= $this->input->post('no_sj') ?: '';
			$in_po_select 					= $this->input->post('po_select') ?: '';
			$in_jml_item 					= $this->input->post('jml_itemBarang') ?: 0;
			$in_m_barang_id 				= $this->input->post('m_barang_id') ?: '';
			$in_m_project_kode 				= $this->input->post('m_project_kode') ?: '';
			$in_permintaan_barang_kode 		= $this->input->post('permintaan_barang_kode') ?: '';
			$in_orderdet_qty 				= $this->input->post('orderdet_qty') ?: '';
			$in_orderdet_id 				= $this->input->post('orderdet_id') ?: '';
			$in_penerimaan_barang_catatan 				= $this->input->post('penerimaan_barang_catatan') ?: '';

			$in_tanggal_terima = DateTime::createFromFormat('d F, Y', $in_tanggal_terima);
			$in_tanggal_terima = $in_tanggal_terima->format('Y-m-d');

			$penerimaan_barang_nomor = $this->get_kode_transaksi(date('m'));
			$data = array(				
				'penerimaan_barang_nomor' 				=> $penerimaan_barang_nomor,
				'penerimaan_barang_tanggal'				=> $in_tanggal_terima,
				'penerimaan_barang_tanggal_terima'		=> $in_tanggal_terima,
				'penerimaan_barang_sj' 					=> $in_no_sj,
				't_order_id'							=> $in_po_select,
				'penerimaan_barang_pemeriksa' 			=> $in_m_karyawan_id,				
				'm_gudang_id'							=> $in_m_gudang_id,
				'penerimaan_barang_catatan'				=> $in_penerimaan_barang_catatan,
				//'project_id'							=> $in_m_project_kode,
				//harus insert subtotal, ppn, total (ambil dr po)
				// 'penerimaan_barang_subtotal'			=> $this->input->post('t_order_subtotal', TRUE),
				// 'penerimaan_barang_ppn'					=> $this->input->post('t_order_ppn', TRUE),
				// 'penerimaan_barang_total'				=> $this->input->post('t_order_total', TRUE),

				'penerimaan_barang_status' 				=> 1,
				'penerimaan_barang_status_date'			=> date('Y-m-d H:i:s'),
				'penerimaan_barang_created_date'		=> date('Y-m-d H:i:s'),
				'penerimaan_barang_update_date'			=> date('Y-m-d H:i:s'),
				'penerimaan_barang_created_by'			=> $this->session->userdata('identity'),
				'penerimaan_barang_revised' 			=> 0,
			);

			//chek di penerimaan 
			$where_PB['data'][] = array(
				'column' => 't_order_id',
				'param'	 => $in_po_select
			);				
			$query_PB = $this->mod->select('*', 'v_penerimaan_barang', NULL, $where_PB);
			// if exisit 
			if($query_PB){
				foreach ($query_PB->result() as $row) {
					$insert_pb 		= $row->penerimaan_barang_id;	
				}
				//update

			}else{
				// insert new
				$insert = $this->mod->insert_data_table($this->tbl, NULL, $data);
				$insert_pb			= $insert->output;
			}
			// if($insert->status) {
				//sizeof($this->input->post('penerimaan_barangdet_refaksi_angka'.($i+1), TRUE))
				for ($i = 0; $i < count($this->input->post('m_barang_id')); $i++) {
					$data = array(
						't_penerimaan_barang_id' 				=> $insert_pb,
						'm_barang_id' 							=> $in_m_barang_id[$i],
						'penerimaan_barangdet_qty' 				=> $in_orderdet_qty[$i],
						// 'penerimaan_barangdet_netto'			=> $this->input->post('penerimaan_barangdet_netto', TRUE)[$seq],
						'penerimaan_barangdet_verifikasi'		=> 0,
						// 'penerimaan_barangdet_harga_satuan'	 	=> $this->input->post('orderdet_harga_satuan', TRUE)[$seq],
						// 'penerimaan_barangdet_total'			=> $this->input->post('orderdet_total', TRUE)[$seq],
						'penerimaan_barangdet_status'			=> 1,
						'penerimaan_barangdet_status_date'		=> date('Y-m-d H:i:s'),
						'penerimaan_barangdet_created_date'		=> date('Y-m-d H:i:s'),
						'penerimaan_barangdet_created_by'		=> $this->session->userdata('identity'),
						'penerimaan_barangdet_update_date'		=> date('Y-m-d H:i:s'),
						'penerimaan_barangdet_revised' 			=> 0,
					);
					$insert_det = $this->mod->insert_data_table('t_penerimaan_barangdet', NULL, $data);
							
					//  update stok barang
					$where_gudang2['data'][] = array(
						'column' => 'm_barang_id',
						'param'	 => $in_m_barang_id[$i]
					);
					$where_gudang2['data'][] = array(
						'column' => 'm_gudang_id',
						'param'	 => $in_m_gudang_id
					);
					$query_gudang2 = $this->mod->select('*', 't_stok_gudang', NULL, $where_gudang2);
					$qty_terima = $in_orderdet_qty[$i];
					$qty_masuk_gudang	= $qty_terima;
					if($query_gudang2){
						foreach ($query_gudang2->result() as $rowStok) {
							$update_stok_gudangjumlah = $rowStok->stok_gudang_jumlah + $qty_masuk_gudang;
							$whereStok2['data'][] = array(
								'column' => 'stok_gudang_id',
								'param'	 => $rowStok->stok_gudang_id
							);
							$dataStok2 = array(
								'stok_gudang_jumlah' 		=> $update_stok_gudangjumlah,
								'stok_gudang_update_date'	=> date('Y-m-d H:i:s'),
								'stok_gudang_update_by'		=> $this->session->userdata('identity'),
								'stok_gudang_revised' 		=> $rowStok->stok_gudang_revised + 1,
							);
							$updateStok2 = $this->mod->update_data_table('t_stok_gudang', $whereStok2, $dataStok2);
						}
					}else{
						$dataStokGudang = array(
							'm_gudang_id' 					=> $in_m_gudang_id,							
							'm_barang_id' 					=> $in_m_barang_id[$i],							
							'stok_gudang_jumlah' 			=> $qty_masuk_gudang,
							'stok_gudang_created_date'		=> date('Y-m-d H:i:s'),
							'stok_gudang_created_by' 		=> $this->session->userdata('identity'),
							'stok_gudang_revised' 			=> 0,
						);
						$insertStokGudang = $this->mod->insert_data_table('t_stok_gudang', NULL, $dataStokGudang);
					}
					
					// PO
					
					$orderdet_id = $in_orderdet_id[$i];
					if($orderdet_id > 0){
						if (@$where_po2['data']) {
							unset($where_po2['data']);
						}
						$where_po2['data'][] = array(
							'column' => 'orderdet_id',
							'param'	 => $orderdet_id
						);
						$query_po2 = $this->mod->select('*', 't_orderdet', NULL, $where_po2);
						if ($query_po2) {
							foreach ($query_po2->result() as $row) {
								$status_orderdet = 0;
								if($qty_terima >= $row->orderdet_qty){
									$status_orderdet = 1;
								}
								$data_po2 = array(
									'orderdet_status'			=> $status_orderdet,
									'orderdet_qty_realisasi' 	=> ($row->orderdet_qty_realisasi + $qty_masuk_gudang),
									'orderdet_update_by'		=> $this->session->userdata('identity'),
									'orderdet_update_date'		=> date('Y-m-d H:i:s'),
									'orderdet_revised' 			=> $row->orderdet_revised + 1,
								);
								$update_po2 = $this->mod->update_data_table('t_orderdet', $where_po2, $data_po2);
							}
						}
					}	
					// STATUS HDR
					if (@$where_po['data']) {
						unset($where_po['data']);
					}
					$where_po['data'][] = array(
						'column' => 'order_id',
						'param'	 => $in_po_select
					);
					if (@$where_podet['data']) {
						unset($where_podet['data']);
					}
					$where_podet['data'][] = array(
						'column' => 't_order_id',
						'param'	 => $in_po_select
					);
					$query_podet = $this->mod->select('*', 't_orderdet', NULL, $where_podet);
					$flag1 = 0;
					$flag2 = 0;
					if ($query_podet) {
						foreach ($query_podet->result() as $row) {
							$flag1++;
							if ($row->orderdet_status == 1) {
								$flag2++;
							}
						}
					}
					// CEK orderdet_status = 1 SEMUA
					if ($flag1 == $flag2) {
						// UPDATE STATUS t_order
						$data_po = array(
							'order_status' 			=> 7,
							'order_status_date' 	=> date('Y-m-d H:i:s'),
						);
						$update_po = $this->mod->update_data_table('t_order', $where_po, $data_po);
						// END UPDATE STATUS t_order
					} else {
						// UPDATE STATUS t_order
						$data_po = array(
							'order_status' 			=> 6,
							'order_status_date' 	=> date('Y-m-d H:i:s'),
						);
						$update_po = $this->mod->update_data_table('t_order', $where_po, $data_po);
						// END UPDATE STATUS t_order
					}
					// END PO
				}
			// }
			$this->system_message->set_success('Penerimaan Barang Masuk sudah berhasil di input');
			refresh();
		}
		$this->mViewData['form'] = $form;
		$this->mPageTitlePrefix = 'Gudang - ';
		$this->mPageTitle = "Penerimaan Barang";
		$this->render("penerimaan-barang/V_form_penerimaan_barang");
	}

	public function getForm2($id = null){
		$data = array(
			'aplikasi'		=> $this->app_name,
			'title_page' 	=> 'Pembelian',
			'title_page2' 	=> 'Penerimaan Barang',
			'id'			=> $id
		);
		$this->open_page('penerimaan-barang/V_form_penerimaan_barang2', $data);
	}

	public function loadDataWhere($type){
		$select = '*';
		$where['data'][] = array(
			'column' => 'penerimaan_barang_id',
			'param'	 => $this->input->get('id')
		);
		$query = $this->mod->select($select, $this->tbl, NULL, $where);
		if ($query<>false) {

			foreach ($query->result() as $val) {
				if($val->penerimaan_barang_jenis == 1){
					// CARI DETAIL SPAREPART
					$select = '*';
					$join_brg['data'][] = array(
						'table' => 'm_sparepart b',
						'join'	=> 'b.sparepart_id = a.m_barang_id',
						'type'	=> 'left'
					);
				} else if($val->penerimaan_barang_jenis == 2){
					// CARI DETAIL BARANG
					$select = 'a.*, b.*, c.*, d.*';
					$join_brg['data'][] = array(
						'table' => 'm_barang b',
						'join'	=> 'b.barang_id = a.m_barang_id',
						'type'	=> 'left'
					);
					$join_brg['data'][] = array(
						'table' => 'm_jenis_barang c',
						'join'	=> 'c.jenis_barang_id = b.m_jenis_barang_id',
						'type'	=> 'left'
					);
					$join_brg['data'][] = array(
						'table' => 'm_satuan d',
						'join'	=> 'd.satuan_id = b.m_satuan_id',
						'type'	=> 'left'
					);
				}
				$where_brg['data'][] = array(
					'column' => 't_penerimaan_barang_id',
					'param'	 => $val->penerimaan_barang_id
				);
				$query_brg = $this->mod->select($select, 't_penerimaan_barangdet a', $join_brg, $where_brg);
				$response['val2'] = array();
				if ($query_brg) {
					foreach ($query_brg->result() as $val2) {
						// CARI REFAKSI
						$where_refaksi['data'][] = array(
							'column' => 't_penerimaan_barangdet_id',
							'param'	 => $val2->penerimaan_barangdet_id
						);
						$query_refaksi = $this->mod->select('*', 't_refaksi', NULL, $where_refaksi);
						$hasil_refaksi['val2'] = array();
						if ($query_refaksi) {
							foreach ($query_refaksi->result() as $val3) {
								$hasil_refaksi['val2'][] = array(
									'id' 	=> $val3->refaksi_id,
									'angka' 	=> $val3->refaksi_angka,
									'alasan' 	=> $val3->refaksi_alasan
								);
							}
						}
						// END CARI REFAKSI

						// CARI ORDER DET
						if (@$where_det['data']) {
							unset($where_det['data']);
						}
						if (@$join_det['data']) {
							unset($join_det['data']);
						}
						$where_det['data'][] = array(
							'column' => 'order_id',
							'param'	 => $val->t_order_id
						);
						$join_det['data'][] = array(
							'table' => 't_orderdet b',
							'join'	=> 'b.t_order_id = a.order_id',
							'type'	=> 'left'
						);
						$query_det = $this->mod->select('*', 't_order a', $join_det, $where_det);
						$hasil_det['val2'] = array();
						if ($query_det) {
							foreach ($query_det->result() as $val3) {
								$hasil_det['val2'][] = array(
									'id' 			=> $val3->orderdet_id,
									'order_bahan'	=> $val3->order_bahan,
								);
							}
						}
						// END CARI ORDER DET
						// $array_refaksi = array();
						// if($val2->penerimaan_barangdet_refaksi_angka != null){
						// 	$array_refaksi = json_decode($val2->penerimaan_barangdet_refaksi_angka);
						// }
						// $refaksi_angka['val2'] = array();
						// for ($i = 0; $i < sizeof($array_refaksi); $i++) { 
						// 	$refaksi_angka['val2'][] = array(
						// 		'text' 	=> $array_refaksi[$i]
						// 	);
						// }
						if($val->penerimaan_barang_jenis == 1){
							$response['val2'][] = array(
								'penerimaan_barangdet_id'			=> $val2->penerimaan_barangdet_id,
								't_penerimaan_barang'				=> $val2->t_penerimaan_barang_id,
								't_orderdet_id'						=> $hasil_det,
								'm_barang_id'						=> $val2->m_barang_id,
								'barang_kode'						=> $val2->sparepart_nomor,
								'barang_uraian'						=> $val2->sparepart_nama,
								'satuan_nama'						=> 'Tidak Ada Satuan',
								'penerimaan_barangdet_refaksi_angka'=> $hasil_refaksi,
								'penerimaan_barangdet_qty'			=> $val2->penerimaan_barangdet_qty,
								'penerimaan_barangdet_netto'		=> $val2->penerimaan_barangdet_netto,
								'penerimaan_barangdet_verifikasi'	=> $val2->penerimaan_barangdet_verifikasi,
								'penerimaan_barangdet_harga_satuan'	=> $val2->penerimaan_barangdet_harga_satuan,
								'penerimaan_barangdet_potongan'		=> $val2->penerimaan_barangdet_potongan,
								'penerimaan_barangdet_total'		=> $val2->penerimaan_barangdet_total,
								'penerimaan_barangdet_keterangan'	=> $val2->penerimaan_barangdet_keterangan,
							);
						}
						if($val->penerimaan_barang_jenis == 2){
							$response['val2'][] = array(
								'penerimaan_barangdet_id'			=> $val2->penerimaan_barangdet_id,
								't_penerimaan_barang'				=> $val2->t_penerimaan_barang_id,
								't_orderdet_id'						=> $hasil_det,
								'm_barang_id'						=> $val2->m_barang_id,
								'barang_kode'						=> $val2->barang_kode,
								'barang_uraian'						=> $val2->barang_nama.'('.$val2->jenis_barang_nama.')',
								'jenis_barang_nama'					=> $val2->jenis_barang_nama,
								'satuan_nama'						=> $val2->satuan_nama,
								'penerimaan_barangdet_refaksi_angka'=> $hasil_refaksi,
								'penerimaan_barangdet_qty'			=> $val2->penerimaan_barangdet_qty,
								'penerimaan_barangdet_netto'		=> $val2->penerimaan_barangdet_netto,
								'penerimaan_barangdet_verifikasi'	=> $val2->penerimaan_barangdet_verifikasi,
								'penerimaan_barangdet_harga_satuan'	=> $val2->penerimaan_barangdet_harga_satuan,
								'penerimaan_barangdet_potongan'		=> $val2->penerimaan_barangdet_potongan,
								'penerimaan_barangdet_total'		=> $val2->penerimaan_barangdet_total,
								'penerimaan_barangdet_keterangan'	=> $val2->penerimaan_barangdet_keterangan,
							);
						}
					}
				}

				// PEMERIKSA
				$where1['data'][] = array(
					'column' => 'karyawan_id',
					'param'	 => $val->penerimaan_barang_pemeriksa
				);
				$query1 = $this->mod->select('*', 'm_karyawan', NULL, $where1);
				$hasil1['val2'] = array();
				if ($query1) {
					foreach ($query1->result() as $val2) {
						$hasil1['val2'][] = array(
							'id' 	=> $val2->karyawan_id,
							'text' 	=> $val2->karyawan_nama
						);
					}
				}
				// PENYETUJU
				$where2['data'][] = array(
					'column' => 'karyawan_id',
					'param'	 => $val->penerimaan_barang_penyetuju
				);
				$query2 = $this->mod->select('*', 'm_karyawan', NULL, $where2);
				$hasil2['val2'] = array();
				if ($query2) {
					foreach ($query2->result() as $val2) {
						$hasil2['val2'][] = array(
							'id' 	=> $val2->karyawan_id,
							'text' 	=> $val2->karyawan_nama
						);
					}
				}
				// GUDANG
				$where3['data'][] = array(
					'column' => 'gudang_id',
					'param'	 => $val->m_gudang_id
				);
				$query3 = $this->mod->select('*', 'm_gudang', NULL, $where3);
				$hasil3['val2'] = array();
				if ($query3) {
					foreach ($query3->result() as $val2) {
						$hasil3['val2'][] = array(
							'id' 	=> $val2->gudang_id,
							'text' 	=> $val2->gudang_nama
						);
					}
				}
				// NO ORDER
				$where4['data'][] = array(
					'column' => 'order_id',
					'param'	 => $val->t_order_id
				);
				$query4 = $this->mod->select('*', 't_order', NULL, $where4);
				$hasil4['val2'] = array();
				if ($query4) {
					foreach ($query4->result() as $val2) {
						$hasil4['val2'][] = array(
							'id' 		=> $val2->order_id,
							'text' 		=> $val2->order_nomor,
							'subtotal' 	=> $val2->order_subtotal,
							'ppn' 		=> $val2->order_ppn,
							'total' 	=> $val2->order_total
						);
					}
				}

				$response['val'][] = array(
					'kode' 									=> $val->penerimaan_barang_id,
					'penerimaan_barang_nomor' 				=> $val->penerimaan_barang_nomor,
					'penerimaan_barang_jenis' 				=> $val->penerimaan_barang_jenis,
					'penerimaan_barang_tanggal'				=> date("d/m/Y",strtotime($val->penerimaan_barang_tanggal)),
					'penerimaan_barang_tanggal_terima'		=> date("d/m/Y",strtotime($val->penerimaan_barang_tanggal_terima)),
					'penerimaan_barang_pemeriksa'			=> $hasil1,
					'penerimaan_barang_penyetuju'			=> $hasil2,
					'm_gudang_id'							=> $hasil3,
					'penerimaan_barang_sj'	 				=> $val->penerimaan_barang_sj,
					't_order_id'							=> $hasil4,
					'penerimaan_barang_status' 				=> $val->penerimaan_barang_status,
					'penerimaan_barang_catatan'				=> $val->penerimaan_barang_catatan,
					'penerimaan_barang_subtotal'			=> $val->penerimaan_barang_subtotal,
					'penerimaan_barang_ppn'					=> $val->penerimaan_barang_ppn,
					'penerimaan_barang_total'				=> $val->penerimaan_barang_total,
					'penerimaan_barang_status_pembayaran'	=> $val->penerimaan_barang_status_pembayaran,
					'penerimaan_barang_nominal_pembayaran'	=> $val->penerimaan_barang_nominal_pembayaran,
					'penerimaan_barang_kekurangan'			=> floatval(floatval($val->penerimaan_barang_total) - floatval($val->penerimaan_barang_nominal_pembayaran)),
				);
			}

			echo json_encode($response);
		}
	}

	public function checkStatus(){
		$id = $this->input->get('id');
		$select = '*';
		$where['data'][] = array(
			'column' => 'penerimaan_barang_id',
			'param'	 => $id
		);
		$query = $this->mod->select($select, $this->tbl, NULL, $where);
		if ($query<>false) {
			foreach ($query->result() as $row) {
				if ($row->penerimaan_barang_status == 1) {
					$data = $this->general_post_data(3, $id);
					$where['data'][] = array(
						'column' => 'penerimaan_barang_id',
						'param'	 => $id
					);
					$update = $this->mod->update_data_table($this->tbl, $where, $data);
					// INSERT LOG);
					$data_log = array(
						'referensi_id' 								=> $id,
						'penerimaan_baranglog_status_dari' 			=> 1,
						'penerimaan_baranglog_status_ke' 			=> 2,
						'penerimaan_baranglog_status_update_date' 	=> date('Y-m-d H:i:s'),
						'penerimaan_baranglog_status_update_by'		=> $this->session->userdata('user_username'),
					);
					$insert_log = $this->mod->insert_data_table('t_penerimaan_baranglog', NULL, $data_log);
					$response['status'] = '200';
				} else {
					$response['status'] = '204';
				}
			}
		} else {
			$response['status'] = '204';
		}
		echo json_encode($response);
	}

	public function loadData_select(){
		$param = $this->input->get('q');
		if ($param!=NULL) {
			$param = $this->input->get('q');
		} else {
			$param = "";
		}
		$select = '*';
		$where['data'][] = array(
			'column' => 'penerimaan_barang_status',
			'param'	 => 3
		);
		$where_like['data'][] = array(
			'column' => 'penerimaan_barang_nomor',
			'param'	 => $this->input->get('q')
		);
		$order['data'][] = array(
			'column' => 'penerimaan_barang_nomor',
			'type'	 => 'ASC'
		);
		$query = $this->mod->select($select, $this->tbl, NULL, $where, NULL, $where_like, $order);
		$response['items'] = array();
		if ($query<>false) {
			foreach ($query->result() as $val) {
				$response['items'][] = array(
					'id'	=> $val->penerimaan_barang_id,
					'text'	=> $val->penerimaan_barang_nomor
				);
			}
			$response['status'] = '200';
		}

		echo json_encode($response);
	}
	public function loadData_selectPembayaran(){
		$param = $this->input->get('q');
		if ($param!=NULL) {
			$param = $this->input->get('q');
		} else {
			$param = "";
		}
		$select = 'a.*, b.*';
		$join['data'][] = array(
			'table' => 't_order b',
			'join'	=> 'b.order_id = a.t_order_id',
			'type'	=> 'left'
		);
		// $where['data'][] = array(
		// 	'column' => 'a.penerimaan_barang_jenis',
		// 	'param'	 => 0
		// );
		$where['data'][] = array(
			'column' => 'a.penerimaan_barang_status',
			'param'	 => 3
		);
		$where['data'][] = array(
			'column' => 'a.penerimaan_barang_status_pembayaran',
			'param'	 => 1
		);
		$where['data'][] = array(
			'column' => 'b.m_supplier_id',
			'param'	 => $this->input->get('idsup')
		);
		$where['data'][] = array(
			'column' => 'b.order_type',
			'param'	 => 0
		);
		$where_like['data'][] = array(
			'column' => 'a.penerimaan_barang_nomor',
			'param'	 => $this->input->get('q')
		);
		$order['data'][] = array(
			'column' => 'a.penerimaan_barang_nomor',
			'type'	 => 'ASC'
		);
		$query = $this->mod->select($select, 't_penerimaan_barang a', $join, $where, NULL, $where_like, $order);
		$response['items'] = array();
		if ($query<>false) {
			foreach ($query->result() as $val) {
				$response['items'][] = array(
					'id'	=> $val->penerimaan_barang_id,
					'text'	=> $val->penerimaan_barang_nomor
				);
			}
			$response['status'] = '200';
		}

		echo json_encode($response);
	}

	// Function Insert & Update
	public function postData(){
			//INSERT
			$data_pelanggan = $this->general_post_data_pelanggan();
			$insert_pelanggan = $this->mod->insert_data_table('m_pelanggan', NULL, $data_pelanggan);
			if($insert_pelanggan->status) {
				$pelanggan_id = $insert_pelanggan->output;
			}else{
				return;
			}
			$data = $this->general_post_data($pelanggan_id);


			$insert = $this->mod->insert_data_table('t_penjualan', NULL, $data);

			if($insert->status) {
				// INSERT DETAIL BARANG
				$in_m_barang_id 				= $this->input->post('m_barang_id') ?: '';
				$in_m_harga 					= $this->input->post('orderdet_harga_satuan') ?: '';
				$in_m_qty 						= $this->input->post('penjualan_det_qty') ?: '';
				$in_m_jumlah 					= $this->input->post('penjualan_det_total') ?: '';

				for ($i = 0; $i < $this->input->post('jml_itemBarang', TRUE); $i++) {

					//penjualan detail
					$data_det = array(
						'jual_id' 							=> $insert->output,
						'prod_id' 							=> $in_m_barang_id[$i],
						'jual_harga' 						=> $in_m_harga[$i],
						'jual_qty'							=> $in_m_qty[$i],
						'jual_jumlah'						=> $in_m_jumlah[$i],
						'create_date'						=> date('Y-m-d H:i:s'),
						'create_by'							=> $this->session->userdata('identity'),
					);
					$insert_det = $this->mod->insert_data_table('t_penjualan_det', NULL, $data_det);				

						$where_gudang2['data'][] = array(
							'column' => 'm_barang_id',
							'param'	 => $in_m_barang_id[$i]
						);

						// $where_gudang2['data'][] = array(
						// 	'column' => 'm_gudang_id',
						// 	'param'	 => $data['m_gudang_id']
						// );
						
						$query_gudang2 = $this->mod->select('*', 't_stok_gudang', NULL, $where_gudang2);						
						// $qty_masuk_gudang = floatval($qty_terima) - floatval($qty_retur);
						if($query_gudang2){
							foreach ($query_gudang2->result() as $rowStok) {
								// PENAMBAHAN KARTU STOK
								// UPDATE STOK
								$update_stok_gudangjumlah = $rowStok->stok_gudang_jumlah - $in_m_qty[$i];
		
								$whereStok2['data'][] = array(
									'column' => 'stok_gudang_id',
									'param'	 => $rowStok->stok_gudang_id
								);
								$dataStok2 = array(
									'stok_gudang_jumlah' 		=> $update_stok_gudangjumlah,
									'stok_gudang_update_date'	=> date('Y-m-d H:i:s'),
									'stok_gudang_update_by'		=> $this->session->userdata('user_username'),
									'stok_gudang_revised' 		=> $rowStok->stok_gudang_revised + 1,
								);
								$updateStok2 = $this->mod->update_data_table('t_stok_gudang', $whereStok2, $dataStok2);
								// END UPDATE STOK
							}
						}
					}
				}

				// END INSERT DETAIL BARANG
				redirect('mitra/penjualan');
	}

	public function cetakPDF($id) {
		$this->load->library('pdf');
		$name = '';
		$select = '*';
		$where['data'][] = array(
			'column' => 'penerimaan_barang_id',
			'param'	 => $id
		);
		$query = $this->mod->select($select, $this->tbl, NULL, $where);
		if ($query<>false) {

			foreach ($query->result() as $val) {
				// CARI DETAIL
				$where_det['data'][] = array(
					'column' => 't_penerimaan_barang_id',
					'param'	 => $val->penerimaan_barang_id
				);
				$query_det = $this->mod->select('*','t_penerimaan_barangdet',NULL,$where_det);
				$response['val2'] = array();
				if ($query_det) {
					foreach ($query_det->result() as $val2) {
						// CARI BARANG DAN STOK
						if (@$join_brg['data']) {
							unset($join_brg['data']);
						}
						if (@$where_brg['data']) {
							unset($where_brg['data']);
						}
						if($val->penerimaan_barang_jenis == 1){
							$where_brg['data'][] = array(
								'column' => 'sparepart_id',
								'param'	 => $val2->m_barang_id
							);
							$hasil_refaksi['val2'] = array();
							$query_brg = $this->mod->select('*','m_sparepart',null,$where_brg);
							if ($query_brg) {
								foreach ($query_brg->result() as $val3) {
									$response['val2'][] = array(
										'penerimaan_barangdet_id'			=> $val2->penerimaan_barangdet_id,
										'm_barang_id'						=> $val2->m_barang_id,
										'barang_kode'						=> $val3->sparepart_nomor,
										'barang_nama'						=> $val3->sparepart_nama,
										'refaksi_angka'						=> $hasil_refaksi,
										'jenis_barang_nama'					=> 'Sparepart',
										'satuan_nama'						=> 'Tidak Ada Satuan',
										'penerimaan_barangdet_qty'			=> $val2->penerimaan_barangdet_qty,
										'penerimaan_barangdet_netto'		=> $val2->penerimaan_barangdet_netto,
										'penerimaan_barangdet_verifikasi'	=> $val2->penerimaan_barangdet_verifikasi,
										'penerimaan_barangdet_harga_satuan'	=> $val2->penerimaan_barangdet_harga_satuan,
										'penerimaan_barangdet_potongan'		=> $val2->penerimaan_barangdet_potongan,
										'penerimaan_barangdet_total'		=> $val2->penerimaan_barangdet_total,
										'penerimaan_barangdet_keterangan'	=> $val2->penerimaan_barangdet_keterangan,
									);
								}
							}
						} else if($val->penerimaan_barang_jenis == 2){
							$refaksi_angka = 0;
							$refaksi_alasan = "";
							$where_refaksi['data'][] = array(
								'column' => 't_penerimaan_barangdet_id',
								'param'	 => $val2->penerimaan_barangdet_id
							);
							$query_refaksi = $this->mod->select('*', 't_refaksi', NULL, $where_refaksi);
							$hasil_refaksi['val2'] = array();
							if ($query_refaksi) {
								foreach ($query_refaksi->result() as $val3) {
									$hasil_refaksi['val2'][] = array(
										'id' 		=> $val3->refaksi_id,
										'angka' 	=> $val3->refaksi_angka,
										'alasan' 	=> $val3->refaksi_alasan
									);
									$refaksi_angka = $val3->refaksi_angka;
									$refaksi_alasan = $val3->refaksi_alasan;
								}
							}
							// CARI DETAIL BARANG
							$join_brg['data'][] = array(
								'table' => 'm_jenis_barang c',
								'join'	=> 'c.jenis_barang_id = a.m_jenis_barang_id',
								'type'	=> 'left'
							);
							$join_brg['data'][] = array(
								'table' => 'm_satuan d',
								'join'	=> 'd.satuan_id = a.m_satuan_id',
								'type'	=> 'left'
							);
							$where_brg['data'][] = array(
								'column' => 'a.barang_id',
								'param'	 => $val2->m_barang_id
							);
							$query_brg = $this->mod->select('a.*, c.jenis_barang_nama, d.satuan_nama','m_barang a',$join_brg,$where_brg);
							if ($query_brg) {
								foreach ($query_brg->result() as $val3) {
									$response['val2'][] = array(
										'penerimaan_barang_id'				=> $val2->penerimaan_barangdet_id,
										'barang_kode'						=> $val3->barang_kode,
										'barang_nama'						=> $val3->barang_nama,
										'refaksi_angka'						=> $hasil_refaksi,
										'barang_nomor'						=> $val3->barang_nomor,
										'jenis_barang_nama'					=> $val3->jenis_barang_nama,
										'satuan_nama'						=> $val3->satuan_nama,
										'penerimaan_barangdet_qty'			=> $val2->penerimaan_barangdet_qty,
										'penerimaan_barangdet_netto'		=> $val2->penerimaan_barangdet_netto,
										'penerimaan_barangdet_harga_satuan'	=> $val2->penerimaan_barangdet_harga_satuan,
										'penerimaan_barangdet_total'		=> $val2->penerimaan_barangdet_total,
										'penerimaan_barangdet_keterangan'	=> $val2->penerimaan_barangdet_keterangan,
										'm_barang_id'						=> $val2->m_barang_id,
									);
								}
							}
						}
						// CARI BARANG DAN STOK
					}
				}
				// END CARI DETAIL
				// CARI PENYETUJU
				$hasil4['val2'] = array();
				$where_penyetuju['data'][] = array(
					'column' => 'karyawan_id',
					'param'	 => $val->penerimaan_barang_penyetuju
				);
				$query_penyetuju = $this->mod->select('*','m_karyawan',NULL,$where_penyetuju);
				if ($query_penyetuju) {
					foreach ($query_penyetuju->result() as $val2) {
						$hasil4['val2'][] = array(
							'id' 	=> $val2->karyawan_id,
							'text' 	=> $val2->karyawan_nama
						);
					}
				}
				// END CARI PENYETUJU
				// CARI PENERIMA
				$hasil5['val2'] = array();
				$where_penerima['data'][] = array(
					'column' => 'karyawan_id',
					'param'	 => $val->penerimaan_barang_pemeriksa
				);
				$query_penerima = $this->mod->select('*','m_karyawan',NULL,$where_penerima);
				if ($query_penerima) {
					foreach ($query_penerima->result() as $val2) {
						$hasil5['val2'][] = array(
							'id' 	=> $val2->karyawan_id,
							'text' 	=> $val2->karyawan_nama
						);
					}
				}
				// END CARI PENERIMA
				// CARI SUPPLIER
				$hasil6['val2'] = array();
				$join_supp['data'][] = array(
					'table' => 'm_partner c',
					'join'	=> 'c.partner_id = a.m_supplier_id',
					'type'	=> 'left'
				);
				$where_supp['data'][] = array(
					'column' => 'a.order_id',
					'param'	 => $val->t_order_id
				);
				$query_supp = $this->mod->select('a.*, c.partner_nama','t_order a',$join_supp,$where_supp);
				if ($query_supp) {
					foreach ($query_supp->result() as $val3) {
						$hasil6['val2'][] = array(
							'id' 	=> $val3->order_nomor,
							'supplier' 	=> $val3->partner_nama
						);
					}
				}
				// END CARI SUPLLIER
				// CARI CABANG
				$hasil7['val2'] = array();
				$where_cabang['data'][] = array(
					'column' => 'cabang_id',
					'param'	 => $val->m_cabang_id
				);
				$query_cabang = $this->mod->select('*','m_cabang',NULL,$where_cabang);
				if ($query_cabang) {
					foreach ($query_cabang->result() as $val2) {
						// CARI KOTA
						$hasil8['val2'] = array();
						$where_kota['data'][] = array(
							'column' => 'id',
							'param'	 => $val2->cabang_kota
						);
						$query_kota = $this->mod->select('*','regencies',NULL,$where_kota);
						if ($query_kota) {
							foreach ($query_kota->result() as $val3) {
								$hasil8['val3'][] = array(
									'id' 		=> $val3->id,
									'text' 		=> $val3->name,
								);
							}
						}
						// END CARI KOTA
						$hasil7['val2'][] = array(
							'id' 	=> $val2->cabang_id,
							'text' 	=> $val2->cabang_nama,
							'alamat'=> $val2->cabang_alamat,
							'kota'	=> $hasil8,
							'telp'  => json_decode($val2->cabang_telepon)
						);
					}
				}
				// END CARI CABANG
				$response['val'][] = array(
					'kode' 										=> $val->penerimaan_barang_id,
					'penerimaan_barang_nomor' 					=> $val->penerimaan_barang_nomor,
					'penerimaan_barang_jenis' 					=> $val->penerimaan_barang_jenis,
					'penerimaan_barang_sj' 						=> $val->penerimaan_barang_sj,
					'penerimaan_barang_ppn' 					=> $val->penerimaan_barang_ppn,
					'penerimaan_barang_tanggal'					=> date("d/m/Y",strtotime($val->penerimaan_barang_tanggal)),
					'penerimaan_barang_tanggal_terima'			=> date("d/m/Y",strtotime($val->penerimaan_barang_tanggal_terima)),
					'penerimaan_barang_catatan' 				=> $val->penerimaan_barang_catatan,
					'penerimaan_barang_status' 					=> $val->penerimaan_barang_status,
					'penerimaan_barang_penyetuju' 				=> $hasil4,
					'penerimaan_barang_pemeriksa' 				=> $hasil5,
					'penerimaan_barang_pembuat' 				=> $val->penerimaan_barang_created_by,
					'penerimaan_barang_supplier' 				=> $hasil6,
					'cabang'									=> $hasil7
				);
			}
		}
		$response['title'][] = array(
			'aplikasi'		=> $this->app_name,
			'title_page' 	=> 'Penerimaan Barang',
			'title_page2' 	=> 'Print BPB',
		);
		// echo json_encode($response);
		$this->pdf->load_view('print/P_bpb', $response);
		$this->pdf->render();
		$this->pdf->stream($name,array("Attachment"=>false));
	}

	/* Saving $data as array to database */
	function general_post_data($pelanggan_id = null){
	
			$penjualan_kode 	= $this->get_kode_transaksi();			
			$data = array(
				'mitra_id' 								=> '',
				'project_id' 							=> $this->input->post('spp_kode_project', TRUE),
				'pel_id'								=> $pelanggan_id,
				'jual_kode'								=> $penjualan_kode,
				'jual_kodereff' 						=> $this->input->post('pel_invoice', TRUE),
				'jual_tgl'								=> date('Y-m-d H:i:s'),
				'jual_detail' 							=> '',
				'jual_pembayaran'						=> $this->input->post('penerimaan_barang_penyetuju', TRUE),
				'jual_pengiriman'						=> $this->input->post('pel_expedisi', TRUE),
				'status_id'								=>'2',
				'jual_dibuat'							=> $this->session->userdata('identity'),
				'jual_notes'							=> $this->input->post('pel_keterangan', TRUE),
				'jual_subtotal'							=> $this->input->post('penjualan_subtotal', TRUE),
				'jual_diskon'							=> $this->input->post('order_diskon', TRUE),
				'jual_pajak' 							=> $this->input->post('order_tax', TRUE),
				'jual_biaya'							=> $this->input->post('penjualan_biaya', TRUE),
				'jual_total'							=> $this->input->post('penjualan_total', TRUE),
				'jual_dibayar'							=> $this->input->post('penjulan_dp', TRUE),
				'jual_sisabayar'						=> $this->input->post('penjualan_sisa_bayar', TRUE),
				'create_by' 							=> $this->session->userdata('identity'),
				'create_date'							=> date('Y-m-d H:i:s'),
			);		

		return $data;
	}
	/* Saving $data as array to database */
	function general_post_data_pelanggan(){		
		$data = array(
			'pel_kode' 								=> $this->get_kode_transaksi_pelanggan(),
			'pel_nama' 								=> $this->input->post('pel_nama', TRUE),
			'pel_kategori'							=> $this->input->post('kategori', TRUE),
			'pel_alamat'							=> $this->input->post('pelanggan_detail',TRUE),
			'pel_telp'								=> $this->input->post('pel_hp',TRUE),
			'pel_create_by' 						=> $this->session->userdata('identity'),
			'pel_create_date'						=> date('Y-m-d H:i:s'),
		);	
		return $data;
	}


	function get_kode_transaksi($kode_mitra = "000"){
		$bln = date('m');
		$thn = substr(date('Y'), 1);
		$select = 'MID(jual_kode,10,5) as id';
		$where['data'][] = array(
			'column' => 'MID(jual_kode,1,9)',
			'param'	 => 'SO/'.$kode_mitra.'/'.$thn.'0'.$bln
		);
		$order['data'][] = array(
			'column' => 'jual_kode',
			'type'	 => 'DESC'
		);
		$limit = array(
			'start'  => 0,
			'finish' => 1
		);
		$query = $this->mod->select($select, 't_penjualan', NULL, $where, NULL, NULL, $order, $limit);
		$kode_baru = $this->format_kode_transaksi('SO/'.$kode_mitra.'/',$query,$bln);
		return $kode_baru;
	}
	function get_kode_transaksi_pelanggan(){
		$bln = date('m');
		$thn = substr(date('Y'), 1);
		$select = 'MID(pel_kode,10,5) as id';
		$where['data'][] = array(
			'column' => 'MID(pel_kode,1,9)',
			'param'	 => 'PLG'.$thn.'0'.$bln
		);
		$order['data'][] = array(
			'column' => 'pel_kode',
			'type'	 => 'DESC'
		);
		$limit = array(
			'start'  => 0,
			'finish' => 1
		);
		$query = $this->mod->select($select, 'm_pelanggan', NULL, $where, NULL, NULL, $order, $limit);
		$kode_baru = $this->format_kode_transaksi('PLG',$query,$bln);
		return $kode_baru;
	}

	public function ReportPerTransaction(){    
		$this->mPageTitlePrefix = 'Penjualan - ';
		$this->mPageTitle = "Laporan Jual Per Transaksi";
		$this->render('penjualan-barang/V_report_per_transaksi');			
	}
	public function ReportDetailProduct(){    
		$this->mPageTitlePrefix = 'Penjualan - ';
		$this->mPageTitle = "Report Jual Detail Produk";
		$this->render('penjualan-barang/V_report_detail_produk');			
	}
		
	public function ReportPerTransaction_loaddata(){

		$get_datefrom 	= $this->input->get('date_from')	?: '';
		$get_dateto 	= $this->input->get('date_to')		?: '';
		$get_project 	= $this->input->get('project')		?: '';
		
		$get_datefrom = DateTime::createFromFormat('d F, Y', $get_datefrom);
		$get_datefrom = $get_datefrom->format('Y-m-d');

		$get_dateto = DateTime::createFromFormat('d F, Y', $get_dateto);
		$get_dateto = $get_dateto->format('Y-m-d');

		$select = '*';
		//LIMIT
		$limit = array(
			'start'  => $this->input->get('start'),
			'finish' => $this->input->get('length')
		);
		$where['data'][] = array(
			'column' => 'jual_tgl >=',
			'param'	 => $get_datefrom
		);
		$where['data'][] = array(
			'column' => 'jual_tgl <=',
			'param'	 => $get_dateto
		);
		if($get_project == ""){

		}else{
			$where['data'][] = array(
				'column' => 'project_id',
				'param'	 => $get_project
			);
		}
		//WHERE LIKE
		$where_like['data'][] = array(
			'column' => 'project_id, jual_tgl, status',
			'param'	 => $this->input->get('search[value]')
		);
		//ORDER
		$index_order = $this->input->get('order[0][column]');
		$order['data'][] = array(
			'column' => $this->input->get('columns['.$index_order.'][name]'),
			'type'	 => $this->input->get('order[0][dir]')
		);

		$query_total = $this->mod->select($select, 'v_report_penjualan');
		$query_filter = $this->mod->select($select, 'v_report_penjualan', NULL, $where, NULL, NULL, $order);
		$query = $this->mod->select($select, 'v_report_penjualan', NULL, $where, NULL, NULL, $order, NULL);
		$totaljual_total = 0;
		$totaljual_dibayar = 0;
		$totaljual_sisabayar = 0;
		$response['data'] = array();
		if ($query<>false) {
			$no = $limit['start']+1;
			foreach ($query->result() as $val) {

				$response['data'][] = array(
					$no,					
					$val->jual_tgl,
					$val->project_id,
					$val->jual_kode,
					$this->format_money_id($val->jual_total),
					$this->format_money_id($val->jual_dibayar),
					$this->format_money_id($val->jual_sisabayar),
					$val->status_name,
				);
				$totaljual_total 			+= $val->jual_total;
				$totaljual_dibayar 			+= $val->jual_dibayar;
				$totaljual_sisabayar 		+= $val->jual_sisabayar;
				$no++;
			}
		}
		$response['data'][] = array(
			'',
			'',
			'',		
			'<b>Total</b>',
			$this->format_money_id($totaljual_total),
			$this->format_money_id($totaljual_dibayar),
			$this->format_money_id($totaljual_sisabayar),
			''
		);

		$response['recordsTotal'] = 0;
		if ($query_total<>false) {
			$response['recordsTotal'] = $query_total->num_rows();
		}
		$response['recordsFiltered'] = 0;
		if ($query_filter<>false) {
			$response['recordsFiltered'] = $query_filter->num_rows();
		}

		echo json_encode($response);
	}
	public function ReportDetailProduk_loaddata(){

		$get_datefrom 	= $this->input->get('date_from')	?: '';
		$get_dateto 	= $this->input->get('date_to')		?: '';
		$get_project 	= $this->input->get('project')		?: '';
		
		$get_datefrom = DateTime::createFromFormat('d F, Y', $get_datefrom);
		$get_datefrom = $get_datefrom->format('Y-m-d');

		$get_dateto = DateTime::createFromFormat('d F, Y', $get_dateto);
		$get_dateto = $get_dateto->format('Y-m-d');

		$select = '*';
		//LIMIT
		$limit = array(
			'start'  => $this->input->get('start'),
			'finish' => $this->input->get('length')
		);
		$where['data'][] = array(
			'column' => 'jual_tgl >=',
			'param'	 => $get_datefrom
		);
		$where['data'][] = array(
			'column' => 'jual_tgl <=',
			'param'	 => $get_dateto
		);
		if($get_project == ""){

		}else{
			$where['data'][] = array(
				'column' => 'project_id',
				'param'	 => $get_project
			);
		}
		//WHERE LIKE
		$where_like['data'][] = array(
			'column' => 'project_id, jual_tgl, status',
			'param'	 => $this->input->get('search[value]')
		);
		//ORDER
		$index_order = $this->input->get('order[0][column]');
		$order['data'][] = array(
			'column' => $this->input->get('columns['.$index_order.'][name]'),
			'type'	 => $this->input->get('order[0][dir]')
		);

		$query_total = $this->mod->select($select, 'v_report_penjualan_produk_detail');
		$query_filter = $this->mod->select($select, 'v_report_penjualan_produk_detail', NULL, $where, NULL, NULL, $order);
		$query = $this->mod->select($select, 'v_report_penjualan_produk_detail', NULL, $where, NULL, NULL, $order, NULL);
		$totaljual_total = 0;
		$totaljual_dibayar = 0;
		$totaljual_sisabayar = 0;
		$response['data'] = array();
		if ($query<>false) {
			$no = $limit['start']+1;
			foreach ($query->result() as $val) {

				$response['data'][] = array(
					$no,					
					$val->jual_tgl,
					$val->project_id,
					$val->produk_kode,
					$val->produk_nama,
					$this->format_money_id($val->jual_harga),
					$val->jual_qty,
					$this->format_money_id($val->jual_jumlah),
				);
				$totaljual_total 			+= $val->jual_harga;
				$totaljual_dibayar 			+= $val->jual_qty;
				$totaljual_sisabayar 		+= $val->jual_jumlah;
				$no++;
			}
		}
		$response['data'][] = array(
			'',
			'',
			'',
			'',		
			'<b>Total</b>',
			$this->format_money_id($totaljual_total),
			$totaljual_dibayar,
			$this->format_money_id($totaljual_sisabayar),
		);

		$response['recordsTotal'] = 0;
		if ($query_total<>false) {
			$response['recordsTotal'] = $query_total->num_rows();
		}
		$response['recordsFiltered'] = 0;
		if ($query_filter<>false) {
			$response['recordsFiltered'] = $query_filter->num_rows();
		}

		echo json_encode($response);
	}

}

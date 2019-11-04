<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PermintaanPembelian extends Admin_Controller {
	private $any_error = array();
	// Define Main Table
	public $tbl = 'v_produk';

	public function __construct() {
        parent::__construct();            
		$files = array(            
			'assets/js/gudang_scripts.js',               
		);
		$this->add_script($files); 
	}

	public function index(){
            $this->render('errors/custom/error_404');
	}

	public function Gudang(){        
            $this->mPageTitlePrefix = 'Gudang - ';
            $this->mPageTitle = "Surat Permintaan Pembelian";
            $this->render('permintaan-pembelian/V_surat_permintaan_pembelian');
	}
	public function View($id = NULL, $nomor = NULL){    
		
		$select = '*';
		$where['data'][] = array(
			'column' => 'permintaan_pembelian_id',
			'param'	 => $id
		);
		$query = $this->mod->select($select, 'v_permintaan_pembelian', NULL, $where);
		if ($query<>false) {
			$this->mViewData['titlessp']	= $query->result();
		}else{
			redirect();
		}
		unset($select);
		unset($where);
		unset($query);
		unset($val);
		$select = '*';
		$where['data'][] = array(
			'column' => 't_permintaan_pembelian_id',
			'param'	 => $id
		);
		$arr = '';
		$bodyssp = $this->mod->select($select, 'v_permintaan_pembelian_produk', NULL, $where);
		if ($bodyssp<>false) {			                               
			$no=1;
			foreach($bodyssp as $vale){                                        
				
				$arr = "						<tr>
							<td class='text-truncate'>".$no." ></td>
							<td class='text-truncate'> ".$vale->produk_kode."</td>
						</tr>
					";

				
					$no++;
				}
			?>
			$this->mViewData['bodyssp']	=$arr;
		}
		}

		$this->mPageTitlePrefix = 'Gudang - ';
		$this->mPageTitle = "Surat Permintaan Pembelian";
		$this->render('permintaan-pembelian/V_view_surat_permintaan_pembelian');
}
    public function Pembelian(){        
            $this->mPageTitlePrefix = 'Pembelian - ';
            $this->mPageTitle = "Surat Permintaan Pembelian";
            $this->render('permintaan-pembelian/V_surat_permintaan_pembelian2');
	}
	public function loadData($type = ''){
	$select = '*';
		//LIMIT
		$limit = array(
			'start'  => $this->input->get('start'),
			'finish' => $this->input->get('length')
		);
		//WHERE LIKE
		$where_like['data'][] = array(
			'column' => 'permintaan_pembelian_nomor, permintaan_pembelian_jenis_nama, permintaan_pembelian_tanggal, permintaan_pembelian_status_nama',
			'param'	 => $this->input->get('search[value]')
		);
		//ORDER
		$index_order = $this->input->get('order[0][column]');
		$order['data'][] = array(
			'column' => $this->input->get('columns['.$index_order.'][name]'),
			'type'	 => $this->input->get('order[0][dir]')
		);

		$query_total = $this->mod->select($select, 'v_permintaan_pembelian');
		$query_filter = $this->mod->select($select, 'v_permintaan_pembelian', NULL, NULL, NULL, $where_like, $order);
		$query = $this->mod->select($select, 'v_permintaan_pembelian', NULL, NULL, NULL, $where_like, $order, $limit);

		$response['data'] = array();
		if ($query<>false) {
			$no = $limit['start']+1;
			foreach ($query->result() as $val) {
				$status = '';
				$button = '';

				if ($type == 1) {
					if ($val->permintaan_pembelian_status > 4) {
						$button = $button.'<a href="'.base_url().'PermintaanPembelian/gudang/EditForm/'.$val->permintaan_pembelian_id.'">
						<button class="btn blue-ebonyclay" type="button" title="Edit SPP" disabled>
							<i class="icon-pencil text-center"></i>
						</button>
						</a>';
					}
					else {
						$button = $button.'<a href="'.base_url().'PermintaanPembelian/gudang/EditForm/'.$val->permintaan_pembelian_id.'">
						<button class="btn blue-ebonyclay" type="button" title="Edit SPP">
							<i class="icon-pencil text-center"></i>
						</button>
						</a> ';
					}
					$button = $button.'
					<a href="'.base_url().'mitra/PermintaanPembelian/view/'.$val->permintaan_pembelian_id.'/'.$val->permintaan_pembelian_nomor.'">
					<button class="btn blue-ebonyclay" type="button" title="Lihat SPP">
						<i class="icon-eye text-center"></i>
					</button>
					</a>
					<a href="'.base_url().'PermintaanPembelian/gudang/print-SPP/'.$val->permintaan_pembelian_id.'">
					<button class="btn green-jungle" type="button" title="Print PDF">
						<i class="icon-printer text-center"></i>
					</button>
					</a>';
				} else if ($type == 2) {
					$button = '
					<a href="'.base_url().'Persetujuan/Surat-Permintaan-Pembelian/Form/'.$val->permintaan_pembelian_id.'">
					<button class="btn blue-ebonyclay" type="button" onclick="checkStatusSPP('.$val->permintaan_pembelian_id.')" title="Lihat SPP">
						<i class="icon-eye text-center"></i>
					</button>
					</a>
					<a href="'.base_url().'Persetujuan/Surat-Permintaan-Pembelian/print-SPP/'.$val->permintaan_pembelian_id.'">
					<button class="btn green-jungle" type="button" title="Print PDF">
						<i class="icon-printer text-center"></i>
					</button>
					</a>';
				}
				if($val->permintaan_pembelian_jenis_nama == 'Penting')
				{
					$status = '<span class="label bg-red bg-font-red-thunderbird">'.$val->permintaan_pembelian_jenis_nama.' </span>';
				}
				else
				{
					$status = $val->permintaan_pembelian_jenis_nama;
				}
				$response['data'][] = array(
					$no,	
				
					$val->permintaan_pembelian_nomor,
					//$status,
					date("d/m/Y",strtotime($val->permintaan_pembelian_tanggal)),
					$val->permintaan_pembelian_status_nama,
					$button
				);
				$no++;
			}
		}

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

	public function getFormGudang(){

		$this->load->library('form_builder');
		$form = $this->form_builder->create_form('','','class="form-horizontal"');

		if ($form->validate())
		{
			// passed validation
			$in_tanggal_permintaan 		= $this->input->post('tanggal_permintaan') ?: date('d F, Y');
			$in_tanggal_dibutuhkan 		= $this->input->post('tanggal_dibutuhkan') ?: date('d F, Y');
			$in_permintaan_pembelian 	= $this->input->post('permintaan_pembelian_catatan') ?: '';
			$in_jml_item 				= $this->input->post('jml_itemBarang', TRUE) ?: 0;
			$in_m_barang_id 			= $this->input->post('m_barang_id') ?: '';
			$in_project_kode 			= $this->input->post('m_project_kode') ?: '';
			$in_barang_kode				= $this->input->post('permintaan_barang_kode') ?: '';
			$in_qty 					= $this->input->post('permintaan_pembeliandet_qty') ?: '';
			
			$in_tanggal_permintaan = DateTime::createFromFormat('d F, Y', $in_tanggal_permintaan);
			$in_tanggal_permintaan = $in_tanggal_permintaan->format('Y-m-d');
			$in_tanggal_dibutuhkan = DateTime::createFromFormat('d F, Y', $in_tanggal_dibutuhkan);
			$in_tanggal_dibutuhkan = $in_tanggal_dibutuhkan->format('Y-m-d');

			$data = array(
				//'project_id'								=> $in_project_kode,
				'permintaan_pembelian_nomor'				=> $this->get_kode_transaksi(),
				'permintaan_pembelian_tanggal'				=> $in_tanggal_permintaan,
				'permintaan_pembelian_tanggal_dibutuhkan'	=> $in_tanggal_dibutuhkan,
				'permintaan_pembelian_alasan'				=> $in_permintaan_pembelian,
				'permintaan_pembelian_status'				=> '1',
				'permintaan_pembelian_created_date'			=> date('Y-m-d H:i:s'),
				'permintaan_pembelian_created_by'			=> $this->session->userdata('identity'),
			);
			$insert = $this->mod->insert_data_table('t_permintaan_pembelian', NULL, $data);
			if($insert->status) {
				for ($i = 0; $i < $in_jml_item; $i++) {
					$data_detail  = array(
						't_permintaan_pembelian_id'					=> $insert->output,
						'project_kode'								=> $in_project_kode[$i],
						'm_barang_id'								=> $in_m_barang_id[$i],
						'permintaan_pembeliandet_qty'				=> $in_qty[$i],
						'permintaan_pembeliandet_create_date'		=> date('Y-m-d H:i:s'),
						'permintaan_pembeliandet_create_by'			=> $this->session->userdata('identity'),		
					);
					$insert_detail = $this->mod->insert_data_table('t_permintaan_pembeliandet', NULL, $data_detail);
					if($insert_detail->status) {
					} 
				}
			}


			$this->system_message->set_success('SPP berhasil dibuat');
			refresh();

		}
		$this->mViewData['form'] = $form;
		$this->mPageTitlePrefix = 'Gudang - ';
		$this->mPageTitle = "Surat Permintaan Pembelian";
		$this->render("permintaan-pembelian/V_form_surat_permintaan_pembelian");
		 
		 //barang_nama=&barang_nama=&permintaan_pembelian_catatan=&spp_kode_project=PRJ_190005&jml_itemBarang=1&m_barang_id[]=1769445&permintaan_pembeliandet_id[]=&permintaan_pembeliandet_qty[]=65656
	}
	function get_kode_transaksi(){
		$bln = date('m');
		$thn = substr(date('Y'), 1);
		$select = 'MID(permintaan_pembelian_nomor,10,5) as id';
		$where['data'][] = array(
			'column' => 'MID(permintaan_pembelian_nomor,1,9)',
			'param'	 => 'SPP'.$thn.'0'.$bln
		);
		$order['data'][] = array(
			'column' => 'permintaan_pembelian_nomor',
			'type'	 => 'DESC'
		);
		$limit = array(
			'start'  => 0,
			'finish' => 1
		);
		$query = $this->mod->select($select, 't_permintaan_pembelian', NULL, $where, NULL, NULL, $order, $limit);
		$kode_baru = $this->format_kode_transaksi('SPP',$query);
		return $kode_baru;
	}

	public function getFormValue(){
		$this->check_session();
 		$this->load->view("value-barang/V_form_value_barang");
	}

	public function loadDataWhere(){
		$select = '*';
		$where['data'][] = array(
			'column' => 'barang_id',
			'param'	 => $this->input->get('id')
		);
		$query = $this->mod->select($select, $this->tbl, NULL, $where);
		if ($query<>false) {

			foreach ($query->result() as $val) {
				// CARI JENIS BARANG
				$hasil1['val2'] = array();
				$where_type['data'][] = array(
					'column' => 'jenis_barang_id',
					'param'	 => $val->m_jenis_barang_id
				);
				$query_type = $this->mod->select('*','m_jenis_barang',NULL,$where_type);
				foreach ($query_type->result() as $val2) {
					$hasil1['val2'][] = array(
						'id' 	=> $val2->jenis_barang_id,
						'text' 	=> $val2->jenis_barang_nama
					);
				}
				// END CARI JENIS BARANG
				// CARI KATEGORI BARANG
				$hasil5['val2'] = array();
				$where_kategori['data'][] = array(
					'column' => 'kategori_barang_id',
					'param'	 => $val->m_kategori_barang_id
				);
				$query_type = $this->mod->select('*','m_kategori_barang',NULL,$where_kategori);
				foreach ($query_type->result() as $val2) {
					$hasil5['val2'][] = array(
						'id' 	=> $val2->kategori_barang_id,
						'text' 	=> $val2->kategori_barang_nama
					);
				}
				// END CARI KATEGORI BARANG
				// CARI Satuan
				$hasil2['val2'] = array();
				$where_satuan['data'][] = array(
					'column' => 'satuan_id',
					'param'	 => $val->m_satuan_id
				);
				$query_satuan = $this->mod->select('*','m_satuan',NULL,$where_satuan);
				if ($query_satuan) {
					foreach ($query_satuan->result() as $val2) {
						$hasil2['val2'][] = array(
							'id' 	=> $val2->satuan_id,
							'text' 	=> $val2->satuan_nama
						);
					}
				}
				// END CARI Satuan
				$queryKonversi = $this->mod->select('*', 'm_konversi', null, $where);
				if($queryKonversi)
				{
					foreach ($queryKonversi->result() as $val2) {
						// CARI Satuan
						$hasil3['val2'] = array();
						if (@$where_akhirsatuan['data']) {
							unset($where_akhirsatuan['data']);
						}
						$where_akhirsatuan['data'][] = array(
							'column' => 'satuan_id',
							'param'	 => $val2->konversi_akhirsatuan
						);
						$query_akhirsatuan = $this->mod->select('*','m_satuan',NULL,$where_akhirsatuan);
						if($query_akhirsatuan)
						{
							foreach ($query_akhirsatuan->result() as $val3) {
								$hasil3['val2'][] = array(
									'id' 	=> $val3->satuan_id,
									'text' 	=> $val3->satuan_nama
								);
							}
						}
						// END CARI Satuan
						$response['val2'][] = array(
							'konversi_akhir'		=> $val2->konversi_akhir,
							'konversi_akhirsatuan'	=> $hasil3
						);
					}
				}
				$response['val'][] = array(
					'kode' 							=> $val->barang_id,
					'barang_kode' 					=> $val->barang_kode,
					'barang_nomor' 					=> $val->barang_nomor,
					'barang_nama' 					=> $val->barang_nama,
					'barang_minimum_stok' 			=> $val->barang_minimum_stok,
					'm_satuan_id'					=> $hasil2,
					'm_jenis_barang_id' 			=> $hasil1,
					'm_kategori_barang_id' 			=> $hasil5,
					'barang_status_aktif' 			=> $val->barang_status_aktif
				);
			}
			echo json_encode($response);
		}
	}

	public function loadDataValueWhere(){
		$select = '*';
		$where['data'][] = array(
			'column' => 'barang_id',
			'param'	 => $this->input->get('id')
		);
		$query = $this->mod->select($select, $this->tbl, NULL, $where);
		if ($query<>false) {

			foreach ($query->result() as $val) {
				// CARI JENIS BARANG
				$hasil1['val2'] = array();
				$where_type['data'][] = array(
					'column' => 'jenis_barang_id',
					'param'	 => $val->m_jenis_barang_id
				);
				$query_type = $this->mod->select('*','m_jenis_barang',NULL,$where_type);
				foreach ($query_type->result() as $val2) {
					$hasil1['val2'][] = array(
						'id' 	=> $val2->jenis_barang_id,
						'text' 	=> $val2->jenis_barang_nama
					);
				}
				// END CARI JENIS BARANG
				// CARI KATEGORI BARANG
				$hasil5['val2'] = array();
				$where_type['data'][] = array(
					'column' => 'kategori_barang_id',
					'param'	 => $val->m_kategori_barang_id
				);
				$query_type = $this->mod->select('*','m_kategori_barang',NULL,$where_type);
				foreach ($query_type->result() as $val2) {
					$hasil5['val2'][] = array(
						'id' 	=> $val2->kategori_barang_id,
						'text' 	=> $val2->kategori_barang_nama
					);
				}
				// END CARI KATEGORI BARANG
				// CARI Satuan
				$hasil2['val2'] = array();
				$where_satuan['data'][] = array(
					'column' => 'satuan_id',
					'param'	 => $val->m_satuan_id
				);
				$query_satuan = $this->mod->select('*','m_satuan',NULL,$where_satuan);
				if($query_satuan){
					foreach ($query_satuan->result() as $val2) {
						$hasil2['val2'][] = array(
							'id' 	=> $val2->satuan_id,
							'text' 	=> $val2->satuan_nama
						);
					}
				}
				// END CARI Satuan
				$response['val'][] = array(
					'kode' 							=> $val->barang_id,
					'barang_kode' 					=> $val->barang_kode,
					'barang_nomor' 					=> $val->barang_nomor,
					'barang_nama' 					=> $val->barang_nama,
					'barang_minimum_stok' 			=> $val->barang_minimum_stok,
					'm_jenis_barang_id' 			=> $hasil1,
					'm_kategori_barang_id' 			=> $hasil5,
					'm_satuan_id'					=> $hasil2,
					'barang_status_aktif' 			=> $val->barang_status_aktif
				);
				// ATRIBUT BARANG
				// CARI ATRIBUT BARANG
				$response['attribut'] = array();
				$response['subattribut'] = array();
				$response['value_attribut'] = array();
				$response['value_subattribut'] = array();

				$where_att['data'][] = array(
					'column' => 'm_barang_id',
					'param'	 => $val->barang_id
				);
				$where_att['data'][] = array(
					'column' => 'atribut_status_aktif',
					'param'	 => 'y'
				);
				$query_att = $this->mod->select('*','m_atribut_barang',NULL,$where_att);
				if ($query_att) {
					foreach ($query_att->result() as $row1) {
						// CARI SUB ATRIBUT
						if (@$where_sub_att['data']) {
							unset($where_sub_att['data']);
						}
						$where_sub_att['data'][] = array(
							'column' => 'm_atribut_id',
							'param'	 => $row1->atribut_id
						);
						$where_sub_att['data'][] = array(
							'column' => 'sub_atribut_status_aktif',
							'param'	 => 'y'
						);
						$query_sub_att = $this->mod->select('*','m_sub_atribut_barang',NULL,$where_sub_att);
						$subattribut = array();
						if ($query_sub_att) {
							foreach ($query_sub_att->result() as $row2) {
								// CHECK VALUE
								if (@$where_value_subatt['data']) {
									unset($where_value_subatt['data']);
								}
								$where_value_subatt['data'][] = array(
									'column' => 'referensi_id',
									'param'	 => $row2->sub_atribut_id
								);
								$where_value_subatt['data'][] = array(
									'column' => 'referensi_type',
									'param'	 => '2'
								);
								$query_value_subatt = $this->mod->select('*','m_value',NULL,$where_value_subatt);
								if ($query_value_subatt) {
									foreach ($query_value_subatt->result() as $rowval) {
										$response['value_subattribut'][] = array(
											'referensi_type' 	=> $rowval->referensi_type,
											'referensi_id' 		=> $rowval->referensi_id,
											'value' 			=> $rowval->value,
										);
									}
									$value_real = 1;
								} else {
									$value_real = 0;
								}
								// CARI SATUAN
								// $hasil1['val2'] = array();
								if (@$where_subSatuan['data']) {
									unset($where_subSatuan['data']);
								}
								$where_subSatuan['data'][] = array(
									'column' => 'satuan_id',
									'param'	 => $row2->sub_atribut_satuan
								);
								$query_subSatuan = $this->mod->select('*','m_satuan',NULL,$where_subSatuan);
								$satuan = '';
								if($query_subSatuan)
								{
									foreach ($query_subSatuan->result() as $val2) {
										$satuan = $val2->satuan_nama;
									}
								}
								// END SATUAN
								$response['subattribut'][] = array(
									'atribut_id' 				=> $row1->atribut_id,
									'sub_atribut_id' 			=> $row2->sub_atribut_id,
									'sub_atribut_jenis' 		=> $row2->sub_atribut_jenis,
									'sub_atribut_nama' 			=> $row2->sub_atribut_nama,
									'sub_atribut_satuan' 		=> $satuan,
									'sub_atribut_default_value'	=> $row2->sub_atribut_default_value,
									'value_real'				=> $value_real
								);
							}
						}
						// END CARI SUB ATRIBUT

						// CHECK VALUE 
						if (@$where_value_att['data']) {
							unset($where_value_att['data']);
						}
						$where_value_att['data'][] = array(
							'column' => 'referensi_id',
							'param'	 => $row1->atribut_id
						);
						$where_value_att['data'][] = array(
							'column' => 'referensi_type',
							'param'	 => '1'
						);
						$query_value_att = $this->mod->select('*','m_value',NULL,$where_value_att);
						if ($query_value_att) {
							foreach ($query_value_att->result() as $rowval) {
								$response['value_attribut'][] = array(
									'referensi_type' 	=> $rowval->referensi_type,
									'referensi_id' 		=> $rowval->referensi_id,
									'value' 			=> $rowval->value,
								);
							}
							$value_real = 1;
						} else {
							$value_real = 0;
						}
						// CARI SATUAN
						// $hasil5['val2'] = array();
						if (@$where_attrSatuan['data']) {
							unset($where_attrSatuan['data']);
						}
						$where_attrSatuan['data'][] = array(
							'column' => 'satuan_id',
							'param'	 => $row1->atribut_satuan
						);
						$query_attrSatuan = $this->mod->select('*','m_satuan',NULL,$where_attrSatuan);
						$attrSatuan = '';
						if($query_attrSatuan)
						{
							foreach ($query_attrSatuan->result() as $val2) {
								$attrSatuan = $val2->satuan_nama;
							}
						}
						// END SATUAN
						$response['attribut'][] = array(
							'atribut_id' 				=> $row1->atribut_id,
							'atribut_jenis' 			=> $row1->atribut_jenis,
							'atribut_nama' 				=> $row1->atribut_nama,
							'atr_satuan' 				=> $row1->atribut_satuan,
							'atribut_satuan' 			=> $attrSatuan,
							'atribut_default_value' 	=> $row1->atribut_default_value,
							'value_real'				=> $value_real
						);

					}
				}
				// END CARI ATRIBUT BARANG
				// END ATRIBUT BARANG
			}

			echo json_encode($response);
		}
	}

	public function loadData_select($type = NULL){
		$param = $this->input->get('q');
		if ($param!=NULL) {
			$param = $this->input->get('q');
		} else {
			$param = "";
		}
		$select = 'a.*, b.*,c.*';
		$join['data'][] = array(
			'table' => 'm_jenis_barang b',
			'join'	=> 'b.jenis_barang_id = a.m_jenis_barang_id',
			'type'	=> 'left'
		);
		$join['data'][] = array(
			'table' => 'm_kategori_barang c',
			'join'	=> 'c.kategori_barang_id = a.m_kategori_barang_id',
			'type'	=> 'left'
		);
		$where['data'][] = array(
			'column' => 'a.barang_status_aktif',
			'param'	 => 'y'
		);
		$where_like['data'][] = array(
			'column' => 'a.barang_nama',
			'param'	 => $this->input->get('q')
		);
		$order['data'][] = array(
			'column' => 'a.barang_nama',
			'type'	 => 'ASC'
		);
		$query = $this->mod->select($select, 'm_barang a', $join, $where, NULL, $where_like, $order);
		$response['items'] = array();
		if ($query<>false) {
			foreach ($query->result() as $val) {
				$response['items'][] = array(
					'id'	=> $val->barang_id,
					'text'	=> $val->barang_nama.' ('.$val->jenis_barang_nama.')'
				);
			}
			$response['status'] = '200';
		}

		echo json_encode($response);
	}

	public function loadData_select2(){
		$param = $this->input->get('q');
		if ($param!=NULL) {
			$param = $this->input->get('q');
		} else {
			$param = "";
		}
		$select = '*';
		$where['data'][] = array(
			'column' => 'barang_status_aktif',
			'param'	 => 'y'
		);
		$where_like['data'][] = array(
			'column' => 'barang_kode',
			'param'	 => $this->input->get('q')
		);
		$order['data'][] = array(
			'column' => 'barang_kode',
			'type'	 => 'ASC'
		);
		$query = $this->mod->select($select, $this->tbl, NULL, $where, NULL, $where_like, $order);
		$response['items'] = array();
		if ($query<>false) {
			foreach ($query->result() as $val) {
				$response['items'][] = array(
					'id'	=> $val->barang_id,
					'text'	=> $val->barang_kode
				);
			}
			$response['status'] = '200';
		}

		echo json_encode($response);
	}

	public function loadData_select3(){
		$param = $this->input->get('q');
		if ($param!=NULL) {
			$param = $this->input->get('q');
		} else {
			$param = "";
		}
		$select = 'a.*, b.*,c.*';
		$join['data'][] = array(
			'table' => 'm_jenis_barang b',
			'join'	=> 'b.jenis_barang_id = a.m_jenis_barang_id',
			'type'	=> 'left'
		);
		$join['data'][] = array(
			'table' => 'm_kategori_barang c',
			'join'	=> 'c.kategori_barang_id = a.m_kategori_barang_id',
			'type'	=> 'left'
		);
		$where['data'][0] = array(
			'column' => 'a.barang_status_aktif',
			'param'	 => 'y'
		);
		$where['data'][1] = array(
			'column' => 'b.jenis_barang_id',
			'param'	 => $this->input->get('id')
		);
		$where_like['data'][] = array(
			'column' => 'a.barang_nama',
			'param'	 => $this->input->get('q')
		);
		$order['data'][] = array(
			'column' => 'a.barang_nama',
			'type'	 => 'ASC'
		);
		$query = $this->mod->select($select, 'm_barang a', $join, $where, NULL, $where_like, $order);
		$response['items'] = array();
		if ($query<>false) {
			foreach ($query->result() as $val) {
				$response['items'][] = array(
					'id'	=> $val->barang_id,
					'text'	=> $val->barang_nama.' ('.$val->jenis_barang_nama.')'
				);
			}
			$response['status'] = '200';
		}

		echo json_encode($response);
	}

	// Function Insert & Update
	public function postData(){
		$id = $this->input->post('kode');
		if (strlen($id)>0) {
			//UPDATE
			$data = $this->general_post_data(2, $id);
			$where['data'][] = array(
				'column' => 'barang_id',
				'param'	 => $id
			);
			$update = $this->mod->update_data_table($this->tbl, $where, $data);
			if($update->status) {
				$response['status'] = '200';
				$queryKonversi = $this->mod->select('*', 'm_konversi', null, $where);
				if($queryKonversi) {
					for($i = 0; $i < sizeof($this->input->post('konversi_akhir_satuan', TRUE)); $i++) {
						$dataKonversi = $this->general_post_data3(2, $val->konversi_id, $i, $id);
						if(@$where_det['data']) {
							unset($where_det['data']);
						}
						$where_det['data'][] = array(
							'column'	=> 'jenis_produksidet_id',
							'param'		=> $this->input->post('jenis_produksidet_id', TRUE)[$i]
						);
						$update_det = $this->mod->update_data_table('m_konversi', $where, $dataKonversi);
						if($update_det->status) {
							$response['status'] = '200';
						} else {
							$response['status'] = '204';
						}
					}
					foreach ($queryKonversi->result() as $val) {
						$whereKonversi['data'][] = array(
							'column' => 'konversi_id',
							'param'	 => $val->konversi_id
						);
						$updateKonversi = $this->mod->update_data_table('m_konversi', $whereKonversi, $dataKonversi);
					}
				}
				else
				{
					$dataKonversi = $this->general_post_data3(1, null, $id);
					$insert = $this->mod->insert_data_table('m_konversi', NULL, $dataKonversi);
				}
				if($data['barang_status_aktif'] == 'n')
				{
					$updateAttr = $this->nonaktif_atribut($id);
				}
			} else {
				$response['status'] = '204';
			}
		} else {
			//INSERT
			$data = $this->general_post_data(1);
			$insert = $this->mod->insert_data_table('m_produk', NULL, $data);
			// $dataKonversi = $this->general_post_data3(1, null, $insert->output);
			// $insert = $this->mod->insert_data_table('m_konversi', NULL, $dataKonversi);
			if($insert->status) {
//				$response['status'] = '200';
//				for ($i = 0; $i < sizeof($this->input->post('konversi_akhir_satuan', TRUE)); $i++) {
//					$data_konversi = $this->general_post_data3(1, $insert->output, $i, null);
//					$insert_konversi = $this->mod->insert_data_table('m_konversi', NULL, $data_konversi);
//					if($insert_konversi->status) {
//					} else {
//						$response['status'] = '204';
//					}
//				}
                                $response['status'] = '200';
			} else {
				$response['status'] = '204';
			}
		}
		
		echo json_encode($response);
	}

	public function postDataValue(){
		for ($i = 0; $i < sizeof($this->input->post('referensi_id', TRUE)); $i++) { 
			if (@$where['data']) {
				unset($where['data']);
			}
			$where['data'][] = array(
				'column' => 'referensi_type',
				'param'	 => $this->input->post('referensi_type', TRUE)[$i]
			);
			$where['data'][] = array(
				'column' => 'referensi_id',
				'param'	 => $this->input->post('referensi_id', TRUE)[$i]
			);
			$check = $this->mod->select('*', 'm_value', NULL, $where);
			if ($check) {
				$data = $this->general_post_data2($i, 2);
				$update = $this->mod->update_data_table('m_value', $where, $data);
				if($update->status) {
					$response['status'] = '200';
				} else {
					$response['status'] = '204';
				}
			} else {
				$data = $this->general_post_data2($i, 1);
				$insert = $this->mod->insert_data_table('m_value', NULL, $data);
				if($insert->status) {
					$response['status'] = '200';
				} else {
					$response['status'] = '204';
				}
			}
		}

		echo json_encode($response);
	}

	// Function Delete
	public function deleteData(){
		$id = $this->input->post('id');
		$data = $this->general_post_data(3, $id);
		$where['data'][] = array(
			'column' => 'barang_id',
			'param'	 => $id
		);
		$update = $this->mod->update_data_table($this->tbl, $where, $data);
		$updateAttr = $this->nonaktif_atribut($id);
		if($update->status) {
			$response['status'] = '200';
		} else {
			$response['status'] = '204';
		}

		echo json_encode($response);
	}

	public function aktifData(){
		$id = $this->input->post('id');
		$data = $this->general_post_data(4, $id);
		$where['data'][] = array(
			'column' => 'barang_id',
			'param'	 => $id
		);
		$update = $this->mod->update_data_table($this->tbl, $where, $data);
		if($update->status) {
			$response['status'] = '200';
		} else {
			$response['status'] = '204';
		}

		echo json_encode($response);
	}

	/* Saving $data as array to database */
	function general_post_data($type, $id = null){
		// 1 Insert, 2 Update, 3 Delete / Non Aktif
		$where['data'][] = array(
			'column' => 'produk_id',
			'param'	 => $id
		);
		$queryRevised = $this->mod->select('produk_revised', 'm_produk', NULL, $where);
		if ($queryRevised) {
			$revised = $queryRevised->row_array();
			$rev = $revised['barang_revised'] + 1;
		}
		if ($type == 1) {
			$data = array(
//				'produk_id' 					=> $this->input->post('barang_kode', TRUE),
				'produk_id' 					=> "KST". rand(100, 999),
//				'barang_nomor' 					=> $this->input->post('barang_nomor', TRUE),
				'produk_nama' 					=> $this->input->post('barang_nama', TRUE),
//				'm_jenis_barang_id' 			=> $this->input->post('m_jenis_barang_id', TRUE),
				'm_jenis_barang_id' 			=> '1',
//				'm_kategori_barang_id' 			=> '1',
				'm_satuan_id' 					=> $this->input->post('m_satuan_id', TRUE),
				'produk_minimum_stok' 			=> '1',
				'produk_status_aktif' 			=> $this->input->post('barang_status_aktif', TRUE),
				'produk_create_date' 			=> date('Y-m-d H:i:s'),
				'produk_update_date' 			=> date('Y-m-d H:i:s'),
				'produk_create_by' 				=> $this->session->userdata('identity'),
				'produk_revised' 				=> 0,
			);
		} else if ($type == 2) {
			$data = array(
				'barang_kode' 					=> $this->input->post('barang_kode', TRUE),
				'barang_nomor' 					=> $this->input->post('barang_nomor', TRUE),
				'barang_nama' 					=> $this->input->post('barang_nama', TRUE),
				'm_jenis_barang_id' 			=> $this->input->post('m_jenis_barang_id', TRUE),
				'm_kategori_barang_id' 			=> $this->input->post('m_kategori_barang_id', TRUE),
				'm_satuan_id' 					=> $this->input->post('m_satuan_id', TRUE),
				'barang_minimum_stok' 			=> $this->input->post('barang_minimum_stok', TRUE),
				'barang_status_aktif' 			=> $this->input->post('barang_status_aktif', TRUE),
				'barang_update_date' 			=> date('Y-m-d H:i:s'),
				'barang_update_by' 				=> $this->session->userdata('user_username'),
				'barang_revised' 				=> $rev,
			);
		} else if ($type == 3) {
			$data = array(
				'barang_status_aktif' 			=> 'n',
				'barang_update_date' 			=> date('Y-m-d H:i:s'),
				'barang_update_by' 				=> $this->session->userdata('user_username'),
				'barang_revised' 				=> $rev,
			);
		} else if ($type == 4) {
			$data = array(
				'barang_status_aktif' 			=> 'y',
				'barang_update_date' 			=> date('Y-m-d H:i:s'),
				'barang_update_by' 				=> $this->session->userdata('user_username'),
				'barang_revised' 				=> $rev,
			);
		}

		return $data;
	}

	function general_post_data2($seq, $type){
		$where['data'][] = array(
			'column' => 'referensi_type',
			'param'	 => $this->input->post('referensi_type', TRUE)[$seq]
		);
		$where['data'][] = array(
			'column' => 'referensi_id',
			'param'	 => $this->input->post('referensi_id', TRUE)[$seq]
		);
		$queryRevised = $this->mod->select('value_revised', 'm_value', NULL, $where);
		if ($queryRevised) {
			$revised = $queryRevised->row_array();
			$rev = $revised['value_revised'] + 1;
		}
		if ($type == 1) {
			$data = array(
				'referensi_type' 		=> $this->input->post('referensi_type', TRUE)[$seq],
				'referensi_id' 			=> $this->input->post('referensi_id', TRUE)[$seq],
				'value' 				=> $this->input->post('value', TRUE)[$seq],
				'value_create_date'		=> date('Y-m-d H:i:s'),
				'value_create_by' 		=> $this->session->userdata('user_username'),
			);
		} else if ($type == 2) {
			$data = array(
				'referensi_type' 		=> $this->input->post('referensi_type', TRUE)[$seq],
				'referensi_id' 			=> $this->input->post('referensi_id', TRUE)[$seq],
				'value' 				=> $this->input->post('value', TRUE)[$seq],
				'value_update_date'		=> date('Y-m-d H:i:s'),
				'value_update_by' 		=> $this->session->userdata('user_username'),
				'value_revised' 		=> $rev,
			);
		} 

		return $data;
	}
	/* end Function */

	function general_post_data3($type, $idHdr, $seq, $id = null){
		// 1 Insert, 2 Update, 3 Delete / Non Aktif
		$where['data'][] = array(
			'column' => 'konversi_id',
			'param'	 => $id
		);
		$queryRevised = $this->mod->select('konversi_revised', 'm_konversi', NULL, $where);
		if ($queryRevised) {
			$revised = $queryRevised->row_array();
			$rev = $revised['konversi_revised'] + 1;
		}
		if ($type == 1) {
			$data = array(
				'barang_id' 					=> $idHdr,
				'konversi_awal' 				=> 1,
				'konversi_awalsatuan' 			=> $this->input->post('m_satuan_id', TRUE),
				'konversi_akhir' 				=> $this->input->post('konversi_akhir', TRUE)[$seq],
				'konversi_akhirsatuan' 			=> $this->input->post('konversi_akhir_satuan', TRUE)[$seq],
				// 'konversi_status_aktif' 		=> 'y',
				'konversi_created_date' 			=> date('Y-m-d H:i:s'),
				'konversi_update_date' 			=> date('Y-m-d H:i:s'),
				'konversi_created_by' 			=> $this->session->userdata('user_username'),
				'konversi_revised' 				=> 0,
			);
		} else if ($type == 2) {
			$data = array(
				'konversi_awal' 				=> 1,
				'konversi_awalsatuan' 			=> $this->input->post('m_satuan_id', TRUE),
				'konversi_akhir' 				=> $this->input->post('konversi_akhir', TRUE)[$seq],
				'konversi_akhirsatuan' 			=> $this->input->post('konversi_akhir_satuan', TRUE)[$seq],
				// 'konversi_status_aktif' 		=> 'y',
				'konversi_update_date' 			=> date('Y-m-d H:i:s'),
				'konversi_update_by' 				=> $this->session->userdata('user_username'),
				'konversi_revised' 				=> $rev,
			);
		}

		return $data;
	}

	function nonaktif_atribut($type_id)
	{
		// select data karyawan
		$tblAttr = 'm_atribut_barang';
		$select = 'atribut_id, atribut_revised';
		$where['data'][] = array(
			'column' => 'm_barang_id',
			'param'	 => $type_id,
		);
		$idAttr = $this->mod->select($select, $tblAttr, NULL, $where);
		// end select
		$dataAttr = array();
		$data = array();
		$i = 0;
		if($idAttr)
		{
			foreach ($idAttr->result_array() as $id) {
				// masukkan data karyawan ke dalam array
				$dataAttr[$i] = array(
					'atribut_id' 				=> $id['atribut_id'],
					'atribut_status_aktif' 		=> 'n',
					'atribut_update_date' 		=> date('Y-m-d H:i:s'),
					'atribut_update_by' 		=> $this->session->userdata('user_username'),
					'atribut_revised' 			=> $id['atribut_revised'] + 1, 
				);
				//
				//select user_revised
				$select = 'sub_atribut_revised';
				if (@$whereSubAttr['data']) {
					unset($whereSubAttr['data']);
				}
				$whereSubAttr['data'][] = array(
					'column' => 'm_atribut_id',
					'param'	 => $id['atribut_id'],
				);
				$revSubAttr = $this->mod->select($select, 'm_sub_atribut_barang', NULL, $whereSubAttr);
				// end select
				// cek jika data ada
				if($revSubAttr)
				{
					// ambil data dan masukkan ke dalam array data
					$revisedSubAttr = $revSubAttr->result_array();
					$data[$i] = array(
					    'm_atribut_id' 					=> $id['atribut_id'] ,
					    'sub_atribut_status_aktif' 		=> 'n',
						'sub_atribut_update_date' 		=> date('Y-m-d H:i:s'),
						'sub_atribut_update_by' 		=> $this->session->userdata('user_username'),
						'sub_atribut_revised' 			=> $revisedSubAttr['sub_atribut_revised'] + 1,
				    );
				}
				$i++;
			}
			$updateAttr = $this->db->update_batch($tblAttr, $dataAttr, 'atribut_id');
			if(count($data) > 0)
			{
				$updateSubAttr = $this->db->update_batch('m_sub_atribut_barang', $data, 'm_atribut_id');
			}
		}

        return true;
	}

}

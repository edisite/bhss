<?php

/**
 * Base Controller for Admin module
 */
class Admin_Controller extends MY_Controller {

	protected $mUsefulLinks = array();

	// Grocery CRUD or Image CRUD
	protected $mCrud;
	protected $mCrudUnsetFields;

	// Constructor
	public function __construct()
	{
		parent::__construct();

		// only login users can access Admin Panel
		$this->verify_login();
		$this->general_library();
		// store site config values
//		$this->mUsefulLinks = $this->mConfig['useful_links'];
                
	}

	// Render template (override parent)
	protected function render($view_file, $layout = 'default')
	{
		// load skin according to user role
		$config = $this->mConfig['adminlte'];
		$this->mBodyClass = $config['body_class'][$this->mUserMainGroup];

		// additional view data
//		$this->mViewData['useful_links'] = $this->mUsefulLinks;

		parent::render($view_file, $layout);
	}

	// Initialize CRUD table via Grocery CRUD library
	// Reference: http://www.grocerycrud.com/
	protected function generate_crud($table, $subject = '')
	{
		// create CRUD object
		$this->load->library('Grocery_CRUD');
		$crud = new grocery_CRUD();
		$crud->set_table($table);

		// auto-generate subject
		if ( empty($subject) )
		{
			$crud->set_subject(humanize(singular($table)));
		}

		// load settings from: application/config/grocery_crud.php
		$this->load->config('grocery_crud');
		$this->mCrudUnsetFields = $this->config->item('grocery_crud_unset_fields');

		if ($this->config->item('grocery_crud_unset_jquery'))
			$crud->unset_jquery();

		if ($this->config->item('grocery_crud_unset_jquery_ui'))
			$crud->unset_jquery_ui();

		if ($this->config->item('grocery_crud_unset_print'))
			$crud->unset_print();

		if ($this->config->item('grocery_crud_unset_export'))
			$crud->unset_export();

		if ($this->config->item('grocery_crud_unset_read'))
			$crud->unset_read();

		foreach ($this->config->item('grocery_crud_display_as') as $key => $value)
			$crud->display_as($key, $value);

		// other custom logic to be done outside
		$this->mCrud = $crud;
		return $crud;
	}
	
	// Set field(s) to color picker
	protected function set_crud_color_picker()
	{
		$args = func_get_args();
		if(isset($args[0]) && is_array($args[0]))
		{
			$args = $args[0];
		}
		foreach ($args as $field)
		{
			$this->mCrud->callback_field($field, array($this, 'callback_color_picker'));
		}
	}

	public function callback_color_picker($value = '', $primary_key = NULL, $field = NULL)
	{
		$name = $field->name;
		return "<input type='color' name='$name' value='$value' style='width:80px' />";
	}

	// Append additional fields to unset from CRUD
	protected function unset_crud_fields()
	{
		$args = func_get_args();
		if(isset($args[0]) && is_array($args[0]))
		{
			$args = $args[0];
		}
		$this->mCrudUnsetFields = array_merge($this->mCrudUnsetFields, $args);
	}

	// Initialize CRUD album via Image CRUD library
	// Reference: http://www.grocerycrud.com/image-crud
	protected function generate_image_crud($table, $url_field, $upload_path, $order_field = 'pos', $title_field = '')
	{
		// create CRUD object
		$this->load->library('Image_crud');
		$crud = new image_CRUD();
		$crud->set_table($table);
		$crud->set_url_field($url_field);
		$crud->set_image_path($upload_path);

		// [Optional] field name of image order (e.g. "pos")
		if ( !empty($order_field) )
		{
			$crud->set_ordering_field($order_field);
		}

		// [Optional] field name of image caption (e.g. "caption")
		if ( !empty($title_field) )
		{
			$crud->set_title_field($title_field);
		}

		// other custom logic to be done outside
		$this->mCrud = $crud;
		return $crud;
	}

	// Render CRUD
	protected function render_crud()
	{
		// logic specific for Grocery CRUD only
		$crud_obj_name = strtolower(get_class($this->mCrud));
		if ($crud_obj_name==='grocery_crud')
		{
			$this->mCrud->unset_fields($this->mCrudUnsetFields);	
		}

		// render CRUD
		$crud_data = $this->mCrud->render();

		// append scripts
		$this->add_stylesheet($crud_data->css_files, FALSE);
		$this->add_script($crud_data->js_files, TRUE, 'foot');

		// display view
		$this->mViewData['crud_output'] = $crud_data->output;
		$this->render('crud');
	}
        function general_library() {
            $files = array(
							//'http://localhost/fl/kelapa/assets/theme/global/plugins/jquery.min.js',
                            // 'app-assets/vendors/js/tables/datatable/datatables.min.js',
                            'app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js',
							'app-assets/js/scripts/tables/datatables-extensions/datatables-sources.js',
							
                            'app-assets/vendors/js/forms/select/select2.full.min.js',
                            'app-assets/js/scripts/forms/select/form-select2.js',
                            // 'app-assets/js/scripts/modal/components-modal.js',
                            'app-assets/js/scripts/customizer.min.js', 
                            'app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js',
                            'app-assets/vendors/js/forms/validation/jqBootstrapValidation.js',
                            'app-assets/vendors/js/forms/toggle/bootstrap-switch.min.js',
                            // 'app-assets/js/scripts/forms/validation/form-validation.js',
                            'app-assets/vendors/js/forms/toggle/switchery.min.js',
                            'app-assets/vendors/js/extensions/sweetalert.min.js',
                            'app-assets/js/scripts/extensions/sweet-alerts.js',
                            'app-assets/vendors/js/forms/repeater/jquery.repeater.min.js',
							// 'app-assets/js/scripts/forms/form-repeater.js',
							'app-assets/vendors/js/pickers/pickadate/picker.js',
							'app-assets/vendors/js/pickers/pickadate/picker.date.js',
							'app-assets/vendors/js/pickers/pickadate/picker.time.js',      
							'assets/js/numberformat/jquery.number.min.js',      
							// 'app-assets/vendors/js/pickers/daterange/daterangepicker.js',
                            // 'app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js',
                            'assets/custom_theme/custom.js',          
                            'assets/js/scripts.js',              
                );
                $screen = array(
                            'app-assets/vendors/css/tables/datatable/datatables.min.css',
                            'app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css',
                            'app-assets/vendors/css/forms/selects/select2.min.css',                            
                            'app-assets/css/plugins/animate/animate.css',
                            'app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css',
                            'app-assets/vendors/css/forms/toggle/bootstrap-switch.min.css',
                            'app-assets/vendors/css/forms/toggle/switchery.min.css',
                            'app-assets/css/plugins/forms/validation/form-validation.css',
                            'app-assets/css/plugins/forms/switch.css', 
							'app-assets/vendors/css/extensions/sweetalert.css',
							// 'app-assets/vendors/css/pickers/daterange/daterangepicker.css',
							'app-assets/vendors/css/pickers/pickadate/pickadate.css',
							// 'app-assets/css/plugins/pickers/daterange/daterange.css',

                );
                $this->add_script($files);  
                $this->add_stylesheet($screen);
		}
		function HowPersen($saldo_bulanini, $saldo_bulanlalu) {
            if($saldo_bulanini == 0 && $saldo_bulanlalu == 0){
                $persen = 0;
            }elseif ($saldo_bulanlalu ==0){
                $persen = 100;                    
            }
            else{
                $persen         = (($saldo_bulanini) / ($saldo_bulanlalu)) * 100;
            }  
            return round($persen);
		}
		function negativeValue($amount = 0) {
			if ($amount < 0):
				return '(' . $this->format_money_id(abs($amount)) . ')';    
			else:
				return $this->format_money_id($amount);                    
			endif;
		}
		// Format indonesian money
		function format_money_id($value){
			$format = number_format($value,2,',','.');
			return $format;
		}
			// Format Transaction Code
		function format_kode_transaksi($type, $query, $bln = NULL, $thn = NULL){
			if ($bln) {
				$bln = $bln;
			} else {
				$bln = date('m');
			}
			$thn = substr(date('Y'), 1);
			if ($query<>false) {
				foreach ($query->result() as $row) {
					$urut = intval($row->id) + 1;
					$seq = sprintf("%05d",$urut);
				}
			} else {
				$seq = sprintf("%05d",1);
			}
			$kode_baru = $type.$thn.'0'.$bln.$seq;
			return $kode_baru;
		}
}

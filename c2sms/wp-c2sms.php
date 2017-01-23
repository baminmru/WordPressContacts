<?php
/*
Plugin Name: WP C2SMS
Plugin URI: http://abolsoft.ru/
Description: A wordpress plugin to send site contacts by SMS.
Version: 1.0.0
Author: Michael Baranov 
Author URI: http://abolsoft.ru/
Text Domain: wp-c2sms
*/

define('WP_C2SMS_VERSION', '1.0.0');
define('WP_C2SMS_DIR_PLUGIN', plugin_dir_url(__FILE__));
define('WP_ADMIN_URL', get_admin_url());
define('WP_C2SMS_SITE', 'http://abolsoft.ru');
define('WP_C2SMS_MOBILE_REGEX', '/^(\+)?\d{1}(\(|\s)?\d{3}(\))?(-|\s)?\d{3}(-|\s)?\d{2}(-|\s)?\d{2}$/');

$date = date('Y-m-d H:i:s' ,current_time('timestamp', 0));
load_plugin_textdomain('wp-c2sms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');


	
include_once dirname( __FILE__ ) . '/includes/classes/wp-c2sms.class.php';
	
if(is_file(dirname( __FILE__ ) . '/includes/service.php')) 
	include_once dirname( __FILE__ ) . '/includes/service.php';
	

$sms = new SMSService;
//$sms->RefreshSMSStatus();
	

// Create object of plugin
$WP_C2SMS_Plugin = new WP_C2SMS_Plugin;
register_activation_hook( __FILE__, array( 'WP_C2SMS_Plugin', 'install' ) );
register_activation_hook( __FILE__, array( 'WP_C2SMS_Plugin', 'add_cap' ) );

// WP SMS Plugin Class
class WP_C2SMS_Plugin {
	/**
	 * Wordpress Admin url
	 *
	 * @var string
	 */
	public $admin_url = WP_ADMIN_URL;
	
	/**
	 * WP SMS gateway object
	 *
	 * @var string
	 */
	public $sms;
	
	
	
	/**
	 * Current date/time
	 *
	 * @var string
	 */
	public $date;
	
	
	
	/**
	 * Constructors plugin
	 *
	 * @param  Not param
	 */
	public function __construct() {
		global $sms,  $date;
		$this->sms = $sms;
		$this->date = $date;

		__('WP C2SMS', 'wp-c2sms');
		__('A wordpress plugin to send site contacts by sms.', 'wp-c2sms');
		
		$this->includes();
		$this->activity();
		
		add_action('admin_enqueue_scripts', array(&$this, 'admin_assets'));
		add_action('wp_enqueue_scripts', array(&$this, 'front_assets'));
		add_action('admin_bar_menu', array($this, 'adminbar'));
		add_action('admin_menu', array(&$this, 'menu'));
	}
	
	/**
	 * Creating plugin tables
	 *
	 * @param  Not param
	 */
	static function install() {
	}
	
	/**
	 * Adding new capability in the plugin
	 *
	 * @param  Not param
	 */
	public function add_cap() {
		// gets the administrator role
		$role = get_role( 'administrator' );
		//$role->add_cap( 'wpc2sms_sendsms' );
		$role->add_cap( 'wpc2sms_outbox' );
		$role->add_cap( 'wpc2sms_ip' );
		$role->add_cap( 'wpc2sms_phone' );
		$role->add_cap( 'wpc2sms_setting' );
	}
	
	/**
	 * Includes plugin files
	 *
	 * @param  Not param
	 */
	public function includes() {
		$files = array(
			'widget',
			'includes/functions'
		);
		
		foreach($files as $file) {
			include_once dirname( __FILE__ ) . '/' . $file . '.php';
		}
	}
	
	
	
	/**
	 * Include admin assets
	 *
	 * @param  Not param
	 */
	public function admin_assets() {
		wp_register_style('wpc2sms_admin', plugin_dir_url(__FILE__) . 'assets/css/admin.css', true, '1.1');
		wp_enqueue_style('wpc2sms_admin');
		wp_enqueue_script('jquery');
			
	}
	
	/**
	 * Include front table
	 *
	 * @param  Not param
	 */
	public function front_assets() {
	}
	
	/**
	 * Activity plugin
	 *
	 * @param  Not param
	 */
	private function activity() {
		// Check exists require function
		if( !function_exists('wp_get_current_user') ) {
			include(ABSPATH . "wp-includes/pluggable.php");
		}
		
		// Add plugin caps to admin role
		if( is_admin() and is_super_admin() ) {
			$this->add_cap();
		}
	}
	
	/**
	 * Admin bar plugin
	 *
	 * @param  Not param
	 */
	public function adminbar() {
		global $wp_admin_bar;
		
		if(is_super_admin() && is_admin_bar_showing()) {
			
			
			$wp_admin_bar->add_menu(array(
				'id'		=>	'wp-send-sms',
				'parent'	=>	'new-content',
				'title'		=>	__('SMS', 'wp-c2sms'),
				'href'		=>	$this->admin_url.'/admin.php?page=wp-c2sms'
			));
		}
	}
	
	/**
	 * Dashboard glance plugin
	 *
	 * @param  Not param
	 */
	public function dashboard_glance() {
		
	}
	
	
	
	/**
	 * Shortcodes plugin
	 *
	 * @param  Not param
	 */
	public function shortcode( $atts, $content = null ) {
		
	}
	
	/**
	 * Administrator menu
	 *
	 * @param  Not param
	 */
	public function menu() {
		add_menu_page(__('Wordpress C2SMS', 'wp-c2sms'), __('Wordpress C2SMS', 'wp-c2sms'), 'wpc2sms_setting', 'wp-c2sms', array(&$this, 'setting_page'), 'dashicons-email-alt');
		add_submenu_page('wp-c2sms', __('Outbox', 'wp-c2sms'), __('Outbox', 'wp-c2sms'), 'wpc2sms_outbox', 'wp-c2sms-outbox', array(&$this, 'outbox_page'));
		add_submenu_page('wp-c2sms', __('IP Blacklist', 'wp-c2sms'), __('IP Blacklist', 'wp-c2sms'), 'wpc2sms_ip', 'wp-c2sms-ip', array(&$this, 'ip_page'));
		add_submenu_page('wp-c2sms', __('Phone Blacklist', 'wp-c2sms'), __('Phone Blacklist', 'wp-c2sms'), 'wpc2sms_phone', 'wp-c2sms-phone', array(&$this, 'phone_page'));
		//add_submenu_page('wp-c2sms', __('Setting', 'wp-c2sms'), __('Setting', 'wp-c2sms'), 'wpc2sms_setting', 'wp-c2sms-settings', array(&$this, 'setting_page'));
	}
	

	
	/**
	 * Outbox sms admin page
	 *
	 * @param  Not param
	 */
	public function outbox_page() {
		if(isset($_GET['action'])) {
			// Export 
			if($_GET['action'] == 'export') {
				include_once dirname( __FILE__ ) . "/includes/templates/outbox/export.php";
				return;
			}
		}
		$this->sms->RefreshSMSStatus();
		include_once dirname( __FILE__ ) . '/includes/wp-c2sms-outbox.php';
		
		
		//Create an instance of our package class...
		$list_table = new WP_C2SMS_Outbox_List_Table();
		
		//Fetch, prepare, sort, and filter our data...
		$list_table->prepare_items();
		
		include_once dirname( __FILE__ ) . "/includes/templates/outbox/outbox.php";
	}
	
	// ip page
	public function ip_page() {
		if(isset($_GET['action'])) {
			
			if($_GET['action'] == 'create') {
				include_once dirname( __FILE__ ) . "/includes/templates/ip/add.php";
				return;
			}
			
			if($_GET['action'] == 'delete') {
				$this->sms->DelIP($_GET['ip']);
			}
		}
		
		if(isset($_POST['newip'])) {
			$this->sms->AddIP($_POST['newip']);
		}
		
		//$this->sms->RefreshSMSStatus();
		include_once dirname( __FILE__ ) . '/includes/wp-c2sms-ip.php';
		
		
		//Create an instance of our package class...
		$list_table = new WP_C2SMS_IP_List_Table();
		
		//Fetch, prepare, sort, and filter our data...
		$list_table->prepare_items();
		
		include_once dirname( __FILE__ ) . "/includes/templates/ip/ip.php";
	}
	
	// phone page
	public function phone_page() {
		if(isset($_GET['action'])) {
			if($_GET['action'] == 'create') {
				include_once dirname( __FILE__ ) . "/includes/templates/phone/add.php";
				return;
			}
			
			if($_GET['action'] == 'delete') {
				$this->sms->DelPhone($_GET['phone']);
			}
			
			
		}
		
		if(isset($_POST['newphone'])) {
			$this->sms->AddPhone($_POST['newphone']);
		}
		
		include_once dirname( __FILE__ ) . '/includes/wp-c2sms-phone.php';
		
		
		//Create an instance of our package class...
		$list_table = new WP_C2SMS_Phone_List_Table();
		
		//Fetch, prepare, sort, and filter our data...
		$list_table->prepare_items();
		
		include_once dirname( __FILE__ ) . "/includes/templates/phone/phone.php";
	}
		

	/**
	 * Plugin Setting page
	 *
	 * @param  Not param
	 */
	public function setting_page() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('functions', plugin_dir_url(__FILE__) . 'assets/js/functions.js', true, '1.0');
		//wp_enqueue_script('inputmask', plugin_dir_url(__FILE__) . 'assets/js/jquery.inputmask.js', true, '3.0');
		$sms_page['about'] = get_admin_url() . "admin.php?page=wp-c2sms-settings";
		global $sms;
		
		if(isset($_GET['settings-updated'])) {
			if($_GET['settings-updated'] == 'true') {
				$this->sms->SetConfig();
			}
		}
		
		include_once dirname( __FILE__ ) . "/includes/templates/settings/setting.php";
	}
	
		
}

<?php
/**
 * @category   class
 * @package    WP_C2SMS
 * @author     Mostafa Soufi <info@mostafa-soufi.ir>
 * @copyright  2015 wp-c2sms-plugin.com
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    1.0
 */
abstract class WP_C2SMS {

	/**
	 * Webservice username
	 *
	 * @var string
	 */
	public $username;
	
	/**
	 * Webservice password
	 *
	 * @var string
	 */
	public $password;
	
	/**
	 * Webservice API/Key
	 *
	 * @var string
	 */
	public $has_key = false;
	
	/**
	 * Validation mobile number
	 *
	 * @var string
	 */
	public $validateNumber = "";
	
	/**
	 * Help to gateway
	 *
	 * @var string
	 */
	public $help = false;
	
	/**
	 * SMsS send from number
	 *
	 * @var string
	 */
	public $from;
	
	/**
	 * Send SMS to number
	 *
	 * @var string
	 */
	public $to;
	
	/**
	 * SMS text
	 *
	 * @var string
	 */
	public $msg;
	
	
	
	/**
	 * Constructors
	 */
	public function __construct() {

	}
	
	
	
}

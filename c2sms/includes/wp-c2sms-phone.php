<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_C2SMS_Phone_List_Table extends WP_List_Table {
	var $data;
	
    function __construct(){
        global $status, $page, $wpdb, $table_prefix, $sms;
		
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'blacklist phone',     //singular name of the listed records
            'plural'    => 'phone',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ) );
        
		$this->data = (array)$sms->GetPhoneList();
		
    }
	
    function column_default($item, $column_name){
		$item=(array)$item;
		
		$actions = array(
            'delete'    => sprintf('<a href="admin.php?page=wp-c2sms-phone&action=delete&phone=%s">Delete</a>',$item['phone']),
        );

  
        switch($column_name){
		
			
			case 'domain':
				return $item[$column_name];
			case 'phone':
				$phone=trim($item[$column_name]);
				$phone = preg_replace("/[^0-9]/", "", $phone);
				if(strlen($phone) == 11)
					return preg_replace("/([7])([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/","$1 $2 $3 $4 $5",$phone)." ".$this->row_actions($actions);
				else
					return $item[$column_name]." ".$this->row_actions($actions);;
		
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
	
 
	
    function get_columns(){
        $columns = array(
            'phone'	=> __('Recipient phone', 'wp-c2sms'),
			'domain'	=> __('Site domain', 'wp-c2sms')
        );
        return $columns;
    }
	
    function get_sortable_columns() {
      /*  $sortable_columns = array(
          
            'createtime'=> array('createtime',true),
            'smstext'	=> array('smstext',false),
            'sendto'	=> array('sendto',false),
			'name'	=> array('name',false),
			'smsstatus'	=>array('smsstatus',false)
        );
		*/
		$sortable_columns = array();
        return $sortable_columns;
    }
	
   
	
   
	
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 25;
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
       // $hidden = array('clientip','clientport');
	    $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
       
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->data;
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
			$a=(array) $a;
			$b=(array) $b;
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'createdate'; 
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
		
        
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

}
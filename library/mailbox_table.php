<?php
  if(!class_exists( 'WP_List_Table')){
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
  }

  /**
   * Mailbox Table that extends WP_List_Table
   * It is used to generate Wordpress style table
   */
  class Mailbox_Table extends WP_List_Table {
    private $table_data; // username, email
    private $found_data; // for pagination

    /**
     * Set the data attribute for the table
     *
     * @param data
     * @return void
     */
    function set_data($data){
      $this->table_data = $data;
    }

    /**
     * Define columns
     * 
     * @return array
     */
    function get_columns(){
      $columns = array(
        'cb'       => '<input type="checkbox" />',
        'username' => 'Username',
        'email'    => 'Email'
      );
      return $columns;
    }

    /**
     * This is where all the table initializations goes
     *
     * @return void
     */
    function prepare_items() {
      $columns = $this->get_columns();
      $hidden = array();
      // Sortable
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable);
      // Load data
      $this->items = $this->table_data;
      // Sort it
      usort($this->table_data, array(&$this,'usort_reorder'));
      $this->items = $this->table_data;
      // Pagination
      $per_page = 20;
      $current_page = $this->get_pagenum();
      $total_items = count($this->table_data);
      // only ncessary because we have sample data
      $this->found_data = array_slice($this->table_data,(($current_page-1)*$per_page),$per_page);
      $this->set_pagination_args( array(
        'total_items' => $total_items, //WE have to calculate the total number of items
        'per_page'    => $per_page     //WE have to determine how many items to show on a page
      ));
      $this->items = $this->found_data;
    }

    /**
     * Set the column default
     * 
     * @param  $item
     * @param  $column_name
     * @return mixed
     */
    function column_default($item, $column_name){
      switch( $column_name ) {
        case 'username':
        case 'email':
          return $item[ $column_name ];
        default:
          return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
      }
    }

    /**
     * Set what field would be sortable
     *
     * @return array
     */
    function get_sortable_columns(){
      $sortable_columns = array(
        'username'  => array('username',true),
        'email' => array('email',true)
      );
      return $sortable_columns;
    }

    /**
     * Sort order
     *
     * @param  $a
     * @param  $b
     * @return int
     */
    function usort_reorder( $a, $b ){
      // If no sort, default to title
      $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'booktitle';
      // If no order, default to asc
      $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
      // Determine sort order
      $result = strcmp( $a[$orderby], $b[$orderby] );
      // Send final sort direction to usort
      return ( $order === 'asc' ) ? $result : -$result;
    }

    /**
     * Column action, this is for the username column
     *
     * @param  $item
     * @return string
     */
    function column_username($item) {
      $actions = array(
        'delete' => sprintf('<a href="#" onclick="if(confirm(\'Delete this email? '.$item["email"].'\')){location.href=\'?page=%s&action=%s&username=%s\';}">Delete</a>',$_REQUEST['page'],'delete',$item['username']),
        'change_password' => sprintf('<a href="?page=%s&action=%s&username=%s">Change Password</a>',$_REQUEST['page'],'change_password',$item['username']),
      );
      return sprintf('%1$s %2$s', $item['username'], $this->row_actions($actions) );
    }

    /**
     * Bulk action for the row
     *
     * @return array
     */
    function get_bulk_actions() {
      $actions = array(
        'delete' => 'Delete'
      );
      return $actions;
    }

    /**
     * Create checkbox
     *
     * @param  $item
     * @return string
     */
    function column_cb($item) {
      return sprintf(
        '<input type="checkbox" name="username[]" value="%s" />', $item['username']
      );
    }
  }
?>
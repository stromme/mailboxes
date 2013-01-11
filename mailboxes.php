<?php
/*
Plugin Name: Mailboxes
Plugin URI: http://www.uzbuz.com
Description: Enable the email management from wordpress to cpanel
Version: 1.0
Author: Josua Leonard
Author URI: http://www.uzbuz.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Class: Mailboxes
 *
 * @author: Josua
 */

class Mailboxes {
  /* Our class attributes */
  private $settings=array();
  private $mailbox;
  private $theme_mod_name;

  // To initialize the class without global variable
  public static function init(){
    $class = __CLASS__;
    new $class;
  }

  /* Plugin Construction */
  function __construct() {
    add_action('template_redirect', array($this, 'action_load_dependencies'));
    add_action('network_admin_menu', array($this, 'action_network_admin_menu'));
    add_action('admin_menu', array($this, 'action_admin_menu'));

    // Initialize the cpanel email API
    require(plugin_dir_path(__FILE__).'library/cpanel_api_query.php');
    require(plugin_dir_path(__FILE__).'library/cpanel_api_email.php');
    $this->settings = get_option('network_admin_mailboxes_settings');
    $this->theme_mod_name = 'tb_settings_mailboxes';
    $this->mailbox = new Cpanel_Api_Email($this->settings);
  }

  /**
   * Load all required files for the plugin to run.
   *
   * @uses wp_enqueue_script, plugins_url, add_action, get_option, wp_localize_script, wp_enqueue_style, includes_url
   * @action template_redirect
   * @return null
   */
  function action_load_dependencies() {
  }

  /**
   * Shows admin menu only for network admin menu
   *
   * @uses add_options_page
   * @action network_admin_menu
   * @return null
   */
  function action_network_admin_menu() {
    add_submenu_page('settings.php', 'Mailboxes', 'Mailboxes', 'manage_options', 'mailboxes_manager', array($this, 'network_mailboxes_settings'));
  }

  /**
   * The Mailbox Settings function for network admin
   *
   * @uses
   * @action admin_menu
   * @return null
   */
  function network_mailboxes_settings() {
    $ssl_yes = false;
    $ssl_no = false;
    if (isset($_POST['submit'])) {
      $host     = $_POST['host'];
      $domain   = $_POST['domain'];
      $port     = $_POST['port'];
      $ssl      = $_POST['ssl'];
      $quota    = $_POST['quota'];
      $username = $_POST['username'];
      $password = $_POST['password'];
      if($ssl==1) $ssl_yes = true;
      else $ssl_no = true;

      // Prepare new settings
      $new_settings = array(
        'host'     => $host,
        'domain'   => $domain,
        'port'     => $port,
        'ssl'      => $ssl,
        'quota'    => $quota,
        'username' => $username,
        'password' => $password
      );
      update_option('network_admin_mailboxes_settings', $new_settings);

      // Queue error message
      add_settings_error('general', 'settings_updated', __('Settings saved.'), 'updated');
    }
    else if (get_option('network_admin_mailboxes_settings')) {
      // Load settings if exist
      $settings = get_option('network_admin_mailboxes_settings');
      $host     = $settings['host'];
      $domain   = $settings['domain'];
      $port     = $settings['port'];
      $ssl      = $settings['ssl'];
      $quota    = $settings['quota'];
      $username = $settings['username'];
      $password = $settings['password'];
      if($ssl==1) $ssl_yes = true;
      else $ssl_no = true;
    }
    else {
      // Default setting for port and ssl
      $port = 2082;
      $ssl_no = true;
    }
    // Show message if exists
    settings_errors();
    
    // Load the settings form
    require(plugin_dir_path(__FILE__) . 'forms/network_admin_mailboxes_form.php');
  }

  /**
   * Shows admin menu for the site admin menu
   *
   * @uses
   * @action admin_menu
   * @return null
   */
  function action_admin_menu() {
    add_submenu_page('tools.php', 'Mailboxes', 'Mailboxes', 'manage_options', 'mailboxes_settings', array($this, 'site_mailboxes_settings'));
  }

  /**
   * The Mailbox Settings function for site admin
   *
   * @uses
   * @action admin_menu
   * @return null
   */
  function site_mailboxes_settings() {
    require(plugin_dir_path(__FILE__).'library/mailbox_table.php');
    $setting_exists = true; // Useful in form template

    if (get_option('network_admin_mailboxes_settings')) {
      $new_username = $_POST['username'];
      $new_password = $_POST['password'];
      $new_forwarding = $_POST['forwarding'];
      $show_form = false;
      $action = (isset($_POST['action'])?$_POST['action']:$_GET['action']);
      switch($action) {
        case 'add':
          $new_email = $new_username.'@'.$this->settings["domain"];
            
          $valid = 1;
          $error_string = '';
          if(strlen($new_username)<=0){
            $valid = 0;
            $error_string .= "Please add username.<br />";
          }
          if(strlen($new_password)<5){
            $valid = 0;
            $error_string .= "Password length minimum 5 characters.<br />";
          }

          // Don't add same email
          $email_list = get_theme_mod($this->theme_mod_name);
          if($email_list && in_array($new_email, $email_list)){
            $valid = 0;
            $error_string .= "Email already exist.<br />";
          }

          // All good?
          if($valid){
            $status = $this->mailbox->add($new_username, $new_password, $new_forwarding);
            // Add email account in cpanel is successfull
            if($status->result==1){
              $email_list = get_theme_mod($this->theme_mod_name);
              if($email_list){
                array_push($email_list,$new_email);
              }
              else{
                $email_list = array($new_email);
              }
              set_theme_mod($this->theme_mod_name, $email_list);
              add_settings_error('general', 'settings_updated', __('Successfully add new email.'), 'updated');
              // Clear the form
              $new_username   = '';
              $new_password   = '';
              $new_forwarding = '';
            }
            else {
              // Failed to add, rewrite the posted variables to the from and show it
              $show_form = true;
              add_settings_error('general', 'settings_updated', __($status->reason), 'error');
            }
          }
          else {
            // Contains error, rewrite the posted variables to the from and show it
            $show_form = true;
            add_settings_error('general', 'settings_updated', __($error_string), 'error');
          }
          break;
        case 'delete':
          // If it is from bulk delete action
          if(isset($_POST['username'])){
            $delete_emails = array();
            $usernames = $_POST['username'];
            foreach($usernames as $username){
              array_push($delete_emails, $username.'@'.$this->settings['domain']);
            }
          }
          // If it is from single click delete action
          else if(isset($_GET['username'])){
            $delete_emails = array($_GET['username'].'@'.$this->settings['domain']);
          }

          $success_messages = '';
          $error_messages = '';
          foreach($delete_emails as $delete_email){
            $email_list = get_theme_mod($this->theme_mod_name);
            // Check if the removed addres is on this list, preventing removing another site's email
            if(in_array($delete_email, $email_list)){
              $status = $this->mailbox->delete($delete_email);
              // If email on the cpanel is removed then remove it from our list
              if($status->result==1){
                $index = array_search($delete_email, $email_list);
                array_splice($email_list, $index, 1);
                set_theme_mod($this->theme_mod_name, $email_list);
                $success_messages .= 'Successfully remove email account '.$delete_email.'.<br />';
              }
              else {
                $reason = $status->reason.' ('.$delete_email.')<br />';
                if($reason==NULL){
                  // The error is unknown, maybe the internet is not connected
                  $reason = 'Connection error when removing '.$delete_email.'.<br />';
                }
                else if($status->reason=='Account does not exist.'){
                  // Remove from storage if username is stored but email account on cpanel is does not exist
                  $index = array_search($delete_email, $email_list);
                  array_splice($email_list, $index, 1);
                  set_theme_mod($this->theme_mod_name, $email_list);
                }
                $error_messages .= $reason;
              }
            }
            else {
              // If it's not exists in this list, tell user that it's already deleted
              $error_messages .= 'The email '.$delete_email.' already deleted.';
            }
          }
          if($success_messages!='') add_settings_error('general', 'settings_updated', __($success_messages), 'updated');
          if($error_messages!='') add_settings_error('general', 'settings_updated', __($error_messages), 'error');

            // Redirect after 3 seconds
          ?>
          <script type="text/javascript">setTimeout(function(){location.href='<?=admin_url('tools.php?page='.$_GET['page'])?>';}, 3000);</script>
          <?php
          break;
        case 'change_password':
          $username = $_GET['username'];
          $email = $username.'@'.$this->settings['domain'];
          if($_POST['submit']){
            // Make sure we only deleting account that we own
            $email_list = get_theme_mod($this->theme_mod_name);
            $email_exist = in_array($email, $email_list);
            if($email_exist){
              $valid = 1;
              $error_string = '';
              if(strlen($_POST['password'])<5){
                $valid = 0;
                $error_string .= "Password length minimum 5 characters<br />";
              }
              else if($_POST['password']!=$_POST['retype_password']){
                $valid = 0;
                $error_string .= "Password did not match<br />";
              }
              // All good?
              if($valid){
                $status = $this->mailbox->update_password($username, $_POST['password']);
                if($status->result==1){
                  add_settings_error('general', 'settings_updated', __('Successfully change password for '.$email.'.'), 'updated');
                }
                // If error is unknown
                else if($status->reason==NULL) {
                  add_settings_error('general', 'settings_updated', __('Unknown error when changing password for '.$email.'.'), 'error');
                }
                else {
                  add_settings_error('general', 'settings_updated', __($status->reason.' ('.$email.').'), 'error');
                }
              }
              else {
                add_settings_error('general', 'settings_updated', __($error_string), 'error');
              }
            }
            else {
              add_settings_error('general', 'settings_updated', __('You can only change password for '.$email.'.'), 'error');
            }
          }
          break;
        default:
          break;
      }
      $settings = get_option('network_admin_mailboxes_settings');
      $domain = $settings["domain"];
    }
    else {
      add_settings_error('general', 'settings_updated', __('No mailbox settings found. Please update your mailbox settings first.'), 'error');
      $setting_exists = false;
    }
    // Show message if exist
    settings_errors();

    // Change password has it's own form
    if($action=='change_password')
      require(plugin_dir_path(__FILE__).'forms/change_password_form.php');
    else{
      // Load list from tb_settings_mailboxes
      $list_email = $this->list_email();
      $mailbox_list = new Mailbox_Table();
      $mailbox_list->set_data($list_email);
      // Load settings form
      require(plugin_dir_path(__FILE__).'forms/site_admin_mailboxes_form.php');
    }
  }

  /**
   * The function that reads from theme mod variable tb_settings_mailboxes
   *
   * @uses
   * @action
   * @return array list of emails
   */
  public function list_email(){
    $email_list = get_theme_mod($this->theme_mod_name);
    $show_list  = array();
    if($email_list){
      foreach($email_list as $email){
        $email_split = explode('@', $email);
        $new_email = array('username' => $email_split[0], 'email' => $email);
        array_push($show_list, $new_email);
      }
    }
    return $show_list;
  }
};

/**
 * Initialize Mailboxes
 */
add_action( 'plugins_loaded', array( 'Mailboxes', 'init' ));

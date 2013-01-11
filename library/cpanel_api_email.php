<?php
/**
 * This is the cPanel API class for Email, extending the Cpanel_Api_Query
 *
 * Thanks to https://github.com/vthink/cpanel_api
 * @author nunenuh@gmail.com
 */
class Cpanel_Api_Email extends Cpanel_Api_Query {
  private $param = array();

  function __construct($param = array()) {
    $this->param = $param;
    parent::__construct($param);
  }

  /**
   * List email accounts associated with a particular domain.
   * This function will also list quota and disk usage information.
   *
   * @return type array of object
   *
   */
  public function  list_mail() {
    $input = array(
      'module'   => 'Email',
      'function' => 'listpopswithdisk'
    );
    $query = $this->build_query($input);
    $raw = $this->query($query);
    $ob = json_decode($raw, false);
    return $status = $ob->cpanelresult->data;
  }


  /**
   * Add (create) an e-mail account.
   *
   * Description <br>
   * <b>$domain</b> Domain name for the e-mail account.<br>
   * <b>$email</b> The address part before "@".<br>
   * <b>$password</b> Password for the e-mail account.<br>
   * <b>$quota</b> Positive integer, 0 for unlimited<br>
   *
   * @param type $domain string
   * @param type $email string
   * @param type $password string
   * @param type $quota integer
   * @return type boolean (true/false)
   *

   */
  public function add_mail($domain, $email, $password, $quota) {
    $input = array(
      'module'   => 'Email',
      'function' => 'addpop',
      'domain'   => $domain,
      'email'    => $email,
      'password' => $password,
      'quota'    => $quota
    );
    $query = $this->build_query($input);
    $raw = $this->query($query);
    $ob = json_decode($raw, false);
    return $ob->cpanelresult->data[0];
  }

  /**
   * Delete an Email Account
   *
   * Description <br>
   * <b>$domain</b> Domain name for the e-mail account.<br>
   * <b>$email</b> The address part before "@".<br>
   *
   * @param type $domain
   * @param type $email
   * @return type
   */
  public function delete_mail($domain, $email) {
    $input = array(
      'module'   => 'Email',
      'function' => 'delpop',
      'domain'   => $domain,
      'email'    => $email
    );
    $query = $this->build_query($input);
    $raw = $this->query($query);
    $ob = json_decode($raw, false);
    //$status = $ob->cpanelresult->data[0]->result;
    return $ob->cpanelresult->data[0];
  }

  /**
   * Change an email account's password.
   * Description <br>
   * <b>$domain</b> Domain name for the e-mail account.<br>
   * <b>$email</b> The address part before "@".<br>
   * <b>$new_password</b> The new password for the account<br>
   *
   * @param type $domain string
   * @param type $email string
   * @param type $new_password string
   * @return type
   */
  public function update_mail_password($domain, $email, $new_password) {
    $input = array(
      'module'   => 'Email',
      'function' => 'passwdpop',
      'domain'   => $domain,
      'email'    => $email,
      'password' => $new_password
    );
    $query = $this->build_query($input);
    $raw = $this->query($query);
    $ob = json_decode($raw, false);
    return $ob->cpanelresult->data[0];
  }

  /**
   * List forwarders associated with a specific domain<br>
   *
   * Descriptions <br>
   * <b>$domain</b> The domain name whose forwarders you wish to review. <br>
   * <b>$regex</b> Regular expressions allow you to filter results based on a set of criteria.<br>
   *
   * @param type $domain
   * @param type $regex
   * @return type
   */

  public function list_forwarders($domain = '', $regex = '') {
    $input = array(
      'module'   => 'Email',
      'function' => 'listforwards'
    );

    !empty($domain) && array_push($input, array('domain' => $domain));
    !empty($regex) && array_push($input, array('regex' => $regex));

    $query = $this->build_query($input);
    $raw   = $this->query($query);
    $ob    = json_decode($raw, false);
    return $ob->cpanelresult->data;
  }

  /**
   * Delete forwarders associated with a specific domain<br>
   *
   * Descriptions <br>
   * <b>$destination</b> The email that is going to be forwarded<br>
   * <b>$forwarder</b> The destination email address where the email will be forwarded.<br>
   *
   * @param type $destination
   * @param type $forwarder
   * @return nothing
   */

  public function delete_forwarder($destination = '', $forwarder = '') {
    $input = array(
      'user' => $this->param["username"],
      'cpanel_jsonapi_apiversion' => '1',
      'module'   => 'Email',
      'function' => 'delforward',
      'arg-0' => urlencode($destination.'='.$forwarder)
    );
    $query = $this->build_query($input);
    $this->query($query);
  }

  /**
   * Create an email forwarder for a specified address.
   * You can forward mail to a new address or pipe incoming email to a program. <br>
   *
   * Descriptions<br>
   * <b>$domain</b> The domain for which you wish to add a forwarder (e.g. example.com).<br>
   * <b>$email</b> The local address you wish to use as a forwarder (e.g. 'user' if the address was user@example.com)<br>
   * <b>$fwdopt</b> This parameter defines what type of forwarder should be used.<br>
   * The valid values for this option are: <br>
   * <b>'pipe'</b> - for forwarding to a program <br>
   * <b>'fwd'</b> - for forwarding to another non-system email address <br>
   * <b>'system'</b> - for forwarding to an account on the system <br>
   * <b>'blackhole'</b> - for bouncing emails using the blackhole functionality<br>
   * <b>'fail'</b> - for bounding emails using the fail functionality.<br>
   * <br>
   * <b>$fwdemail</b> The email address to which you want to forward mail.<br>
   * <b>$fwdsystem</b> The system account that you wish to forward email to, this should only be used if 'fwdopt' equals 'system'. <br>
   * <b>$failmsgs</b> If fwdopt is 'fail' this needs to be defined to determine the correct failure message. This should only be used if 'fwdopt' equals 'fail'. <br>
   * <b>$pipefwd</b> The path to the program to which you wish to pipe email. <br>
   *
   * @param type $domain
   * @param type $email
   * @param type $fwdopt
   * @param type $fwdemail
   * @param type $fwdsystem
   * @param type $failmsgs
   * @param type $pipefwd
   * @return type
   */
  public function add_forwader($domain, $email, $fwdopt,
                               $fwdemail = '', $fwdsystem = '', $failmsgs = '', $pipefwd = '') {
    $input = array(
      'module'   => 'Email',
      'function' => 'addforward',
      'domain'   => $domain,
      'email'    => $email,
      'fwdopt'   => $fwdopt
    );
    if(!empty($fwdemail)) $input['fwdemail'] = $fwdemail;
    //!empty($fwdsystem) && array_push($input, array('fwdsystem' => $fwdsystem));
    //!empty($failmsgs) && array_push($input, array('failmsgs' => $failmsgs));
    //!empty($pipefwd) && array_push($input, array('failmsgs' => $pipefwd));

    $query = $this->build_query($input);
    $raw   = $this->query($query);
    $ob    = json_decode($raw, false);
    return $ob->cpanelresult->data;
  }

  /**
   * Shorten the add email function, so we just have to pass the account name, password, and forwarding address parameters
   *
   * @param type $account_name
   * @param type $password
   * @param type $forwards_to
   * @return status
   */
  public function add($account_name, $password, $forwards_to){
    $forwards_valid = true;
    // Create object type status, resemble the status object from json api
    $status = new Status;
    if($forwards_to && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([\.]+)+([a-zA-Z0-9\._-]+)+$/",$forwards_to)){
      $status->result = 0;
      $status->reason = 'Forwarding email is not valid.';
      $forwards_valid = false;
    }

    if($forwards_valid){
      $status = $this->add_mail($this->param["domain"], $account_name, $password, $this->param["quota"]);
      if($status->result==1 && $forwards_to){
        $this->add_forwader($this->param["domain"], $account_name, 'fwd', $forwards_to);
      }
    }
    return $status;
  }

  /**
   * Shorten the delete email function, so we just have to pass the account name
   *
   * @param type $account_name
   * @return status
   */
  public function delete($account_name){
    $status = $this->delete_mail($this->param["domain"], $account_name);
    if($status->result==1){
      $list_forward = $this->list_forwarders($this->param["domain"], $account_name);
      $forward_exist = false;
      if($list_forward){
        foreach($list_forward as $forward){
          if($account_name==$forward->dest){
            $forward_exist = true;
            $forward_email = $forward->forward;
          }
        }
        if($forward_exist) $this->delete_forwarder($account_name, $forward_email);
      }
    }
    return $status;
  }

  /**
   * Shorten the update password function, so we just have to pass the account name and new password
   *
   * @param type $account_name
   * @param type $new_password
   * @return status
   */
  public function update_password($account_name, $new_password){
    $status = $this->update_mail_password($this->param["domain"], $account_name, $new_password);
    return $status;
  }
}

/**
 * This class is created to be used only in add email functions,
 * which is to check whether the forwarding address is valid
 * This class is will be an object that resemble of status from json api
 *
 * @author Josua
 */
class Status {
  public $result;
  public $reason;
  function __construct(){
    $this->result = 0;
    $this->reason = '';
  }
}
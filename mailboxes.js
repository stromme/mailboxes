/**
 * Created by JetBrains PhpStorm.
 * User: josua
 * Date: 1/14/13
 * Time: 2:57 PM
 */

/**
 * Template for email list
 * 
 * @param email_account
 */
function email_list_template(email_account){
  return '<div class="tier" style="display:none;" email-account="'+email_account+'">'+
    '<header><h4>'+email_account+'</h4></header>'+
    '<nav>'+
      '<a class="action-password" href="#">'+
        '<small><i class="icon-cog"></i>change password</small>'+
      '</a>'+
      '<a class="action-delete" href="#">'+
        '<small><i class="icon-trash"></i>delete</small>'+
      '</a>'+
    '</nav>'+
  '</div>';
}

function add_new_email(new_email, new_pass, forwarding, success_callback, failed_callback){
  var data = {
    action: 'create_new_email',
    'new_email': new_email,
    'new_password': new_pass,
    'forwarding': forwarding
  };
  var container = $('#emails-container');
  var post = new AjaxPost(data, {
    'spinner': new LoadingSpinner({
      'reference_elm': container,
      'insert_method': 'before',
      'style_class': 'mailboxes-loader',
      'loader_tag': 'div',
      'spinner_tag': 'div',
      'loader_style': ' ',
      'spinner_style': ' '
    })
  },
  // Ajax replied
  function(response){
    try {
      var json_response = JSON.parse(response);
      if(json_response.status==1){
        container.prepend(email_list_template(json_response.account_email));
        if(typeof(success_callback=="function")) success_callback();
      }
      else {
        //if(typeof(failed_callback=="function")) failed_callback();
        bootstrap_alert(json_response.status_message, 'error');
      }
    } catch (e) {
      bootstrap_alert('Connection error', 'error');
    }
  },
  function(){
    if(typeof(failed_callback)=='function') failed_callback();
  });
  post.doAjaxPost();
}

/**
 * Function to confirm deleting email by ajax post, run success_callback if success, and failed_callback if otherwise
 *
 * @param email
 * @param success_callback
 * @param failed_callback
 */
function confirm_delete_email(email, success_callback, failed_callback){
  var data = {
    action: 'delete_email',
    'email': email
  };
  var container = $("#delete-confirm");
  var button = $(".action-confirm", container);
  var post = new AjaxPost(data, {
    'spinner': new LoadingSpinner({
      'reference_elm': button,
      'in_parent': true,
      'insert_method': 'prepend'
    })
  },
  // Ajax replied
  function(response){
    try {
      var json_response = JSON.parse(response);
      if(json_response.status==1){
        if(typeof(success_callback)=='function') success_callback();
      }
      else {
        bootstrap_alert(json_response.status_message, 'error');
        if(typeof(failed_callback)=='function') failed_callback();
      }
    } catch (e) {
      bootstrap_alert('Connection error', 'error');
    }
  },
  function(){
    if(typeof(failed_callback)=='function') failed_callback();
  });
  post.doAjaxPost();
}

/**
 * Function to confirm changing password by ajax post, run success_callback if success, and failed_callback if otherwise
 *
 * @param email
 * @param new_password
 * @param retype_new_password
 * @param success_callback
 * @param failed_callback
 */
function confirm_change_password(email, new_password, success_callback, failed_callback){
  var data = {
    action: 'change_password',
    'email': email,
    'password': new_password
  };
  var container = $("#change-password");
  var button = $(".action-save", container);
  var post = new AjaxPost(data, {
    'spinner': new LoadingSpinner({
      'reference_elm': button,
      'in_parent': true,
      'insert_method': 'prepend'
    })
  },
  // Ajax replied
  function(response){
    try {
      var json_response = JSON.parse(response);
      if(json_response.status==1){
        bootstrap_alert(json_response.status_message, 'success');
        if(typeof(success_callback)=='function') success_callback();
      }
      else {
        bootstrap_alert(json_response.status_message, 'error');
        if(typeof(failed_callback)=='function') failed_callback();
      }
    } catch (e) {
      bootstrap_alert('Connection error', 'error');
    }
  },
  function(){
    if(typeof(failed_callback)=='function') failed_callback();
  });
  post.doAjaxPost();
}
/**
 * Created by JetBrains PhpStorm.
 * User: josua
 * Date: 1/14/13
 * Time: 2:57 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Template for email list
 * 
 * @param email_account
 */
function email_list_template(email_account){
  var email_tmpl =
    '<div class="tier" style="display:none;" email-account="'+email_account+'">'+
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
  return email_tmpl;
}

/**
 * Function for showing the spinner when in progress of deleting email
 */
function show_delete_email_spinner(){
  $("#delete-confirm .action-confirm").parent().prepend('<span id="mailboxes-deletemail-loader" class="button-side-loader"><span class="loader"></span></span>');
  $('#mailboxes-deletemail-loader .loader').spin('medium-left', '#000000');
  $('#mailboxes-deletemail-loader').fadeIn(300);
}

/**
 * Function for showing the spinner when in progress of changing email password
 */
function show_change_password_spinner(){
  $("#change-password .action-save").parent().prepend('<span id="mailboxes-changepass-loader" class="button-side-loader"><span class="loader"></span></span>');
  $('#mailboxes-changepass-loader .loader').spin('medium-left', '#000000');
  $('#mailboxes-changepass-loader').fadeIn(300);
}

/**
 * Function for showing the spinner when in progress of adding email
 */
function show_add_email_spinner(){
  $('<div id="mailboxes-loader"><div class="loader"></div></div>').insertBefore("#emails-container");
  $('#mailboxes-loader .loader').spin('medium-left', '#000000');
  $('#mailboxes-loader').slideDown(200);
}

function add_new_email(new_email, new_pass, forwarding, success_callback, failed_callback){
  var data = {
    action: 'create_new_email',
    'new_email': new_email,
    'new_password': new_pass,
    'forwarding': forwarding
  };

  show_add_email_spinner();
  $.post(ajaxurl, data, function(response) {
    var json_response = JSON.parse(response);
    $('#mailboxes-loader').slideUp(200, function(){
      $(this).remove();
      if(json_response.status==1){
        $("#emails-container").prepend(email_list_template(json_response.account_email));
        if(typeof(success_callback=="function")) success_callback();
      }
      else {
        if(typeof(failed_callback=="function")) failed_callback();
        bootstrap_alert(json_response.status_message, 'error');
      }
    });
  })
  .error(function() {
    $('#mailboxes-loader').fadeOut(200, function(){
      $(this).remove();
      bootstrap_alert("Connection error", 'error');
      if(typeof(failed_callback)=="function") failed_callback();
    });
  });
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
  // Show spinner while waiting
  show_delete_email_spinner();
  $.post(ajaxurl, data, function(response) {
    var json_response = JSON.parse(response);
    // Remove loading spinner before something else
    $('#mailboxes-deletemail-loader').fadeOut(300, function(){
      $(this).remove();
      if(json_response.status==1){
        if(typeof(success_callback)=='function') success_callback();
      }
      else {
        bootstrap_alert(json_response.status_message, 'error');
        if(typeof(failed_callback)=='function') failed_callback();
      }
    });
  })
  .error(function() {
    $('#mailboxes-deletemail-loader').fadeOut(200, function(){
      $(this).remove();
      bootstrap_alert("Connection error", 'error');
    });
  });
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
  show_change_password_spinner();
  var data = {
    action: 'change_password',
    'email': email,
    'password': new_password
  };
  $.post(ajaxurl, data, function(response) {
    var json_response = JSON.parse(response);
    $('#mailboxes-changepass-loader').fadeOut(300, function(){
      $(this).remove();
      if(json_response.status==1){
        bootstrap_alert(json_response.status_message, 'success');
        // Do callback, which is close the modal when validation is okay
        if(typeof(success_callback)=='function') success_callback();
      }
      else {
        bootstrap_alert(json_response.status_message, 'error');
        if(typeof(failed_callback)=='function') failed_callback();
      }
    });
  })
  .error(function() {
    $('#mailboxes-changepass-loader').fadeOut(200, function(){
      $(this).remove();
      bootstrap_alert("Connection error", 'error');
    });
  });
}
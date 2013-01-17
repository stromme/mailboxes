/**
 * Created by JetBrains PhpStorm.
 * User: josua
 * Date: 1/14/13
 * Time: 2:57 PM
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function(){
  $('form#add-email-form').attr('onsubmit','return false;');
  $('form#add-email-form').submit(function(){
    var form = $(this);
    var new_email_elm = $('#new-email', form);
    var new_pass_elm = $('#new-password', form);
    var retype_new_pass_elm = $('#retype-new-password', form);
    var forwarding = $('#forwarding', form);

    // Validation
    var error_strings = [];
    var valid = true;
    if(empty(new_email_elm.val())){
      error_strings.push('Please fill in email account');
      valid = false;
    }
    else if(!is_email_account(new_email_elm.val())){
      error_strings.push('Please insert a valid email account name');
      valid = false;
    }
    if(empty(new_pass_elm.val()) || new_pass_elm.val().length<5){
      error_strings.push('Minimum password length is 5 characters');
      valid = false;
    }
    else if(!retype_new_pass_elm.val() || retype_new_pass_elm.val()!=new_pass_elm.val()){
      error_strings.push('Password did not match');
      valid = false;
    }
    if(!empty(forwarding.val()) && !is_email(forwarding.val())){
      error_strings.push('Please insert a valid forwarding email address');
      valid = false;
    }

    if(!valid){
      if(error_strings.length>1){
        var error_text = '<ul>';
        $(error_strings).each(function(i, val){
          error_text += '<li>'+val+'</li>';
        });
        error_text += '</ul>';
      }
      else {
        error_text = error_strings[0];
      }
      bootstrap_alert(error_text, 'error');
    }
    else {
      var data = {
        action: 'create_new_email',
        'new_email': new_email_elm.val(),
        'new_password': new_pass_elm.val(),
        'forwarding': forwarding.val()
      };

      show_add_email_spinner();
      $.post(ajaxurl, data, function(response) {
        var json_response = JSON.parse(response);
        $('#mailboxes-loader').slideUp(200, function(){
          $(this).remove();
          if(json_response.status==1){
            $("#emails-container").prepend(email_list_template(json_response.account_email));
            new_email_elm.val('');
            new_pass_elm.val('');
            retype_new_pass_elm.val('');
            forwarding.val('');
            var email_row = $("#emails-container .tier").first();
            email_row.slideDown(200, function(){
              $(this).removeAttr('style');
              email_row.addClass('highlighted');
              setTimeout(function(){email_row.removeClass('highlighted');}, 1000);
            });
          }
          else {
            bootstrap_alert(json_response.status_message, 'error');
          }
        });
      });
    }
  });
});

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

function show_delete_email_spinner(){
  $("#confirm-delete-email").parent().prepend('<span id="mailboxes-deletemail-loader" class="button-side-loader"><span class="loader"></span></span>');
  $('#mailboxes-deletemail-loader .loader').spin('medium-left', '#000000');
  $('#mailboxes-deletemail-loader').fadeIn(300);
}

function show_change_password_spinner(){
  $("#confirm-change-password").parent().prepend('<span id="mailboxes-changepass-loader" class="button-side-loader"><span class="loader"></span></span>');
  $('#mailboxes-changepass-loader .loader').spin('medium-left', '#000000');
  $('#mailboxes-changepass-loader').fadeIn(300);
}

function show_add_email_spinner(){
  $('<div id="mailboxes-loader"><div class="loader"></div></div>').insertBefore("#emails-container");
  $('#mailboxes-loader .loader').spin('medium-left', '#000000');
  $('#mailboxes-loader').slideDown(200);
}

function confirm_delete_email(email, success_callback, failed_callback){
  show_delete_email_spinner();
  var data = {
    action: 'delete_email',
    'email': email
  };
  $.post(ajaxurl, data, function(response) {
    var json_response = JSON.parse(response);
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
  });
}

function confirm_change_password(email, new_password, retype_new_password, success_callback, failed_callback){
  var error_string = '';
  var valid = true;
  if(empty(new_password) || new_password.length<5){
    error_string = 'Minimum password length is 5 characters';
    valid = false;
  }
  else if(!retype_new_password || retype_new_password!=new_password){
    error_string = 'Password did not match';
    valid = false;
  }

  if(valid){
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
    });
  }
  else {
    bootstrap_alert(error_string, 'error');
  }
}
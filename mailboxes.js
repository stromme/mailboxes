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
      var error_text = '<ul>';
      $(error_strings).each(function(i, val){
        error_text += '<li>'+val+'</li>';
      });
      error_text += '</ul>';
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
            bootstrap_alert(json_response.status_message, 'success');
            new_email_elm.val('');
            new_pass_elm.val('');
            retype_new_pass_elm.val('');
            forwarding.val('');
            var email_row = $("#emails-container .tier").first();
            $('.action-password').click(function() {
              var current_email = $('.email-account', $(this).closest('.tier')).val();
              var new_password = $('#change-new-password');
              var retype_new_password = $('#retype-change-new-password');
              $('#change-password').modal();
              $('#confirm-change-password').unbind('click').click(function(){
                confirm_change_password(current_email, new_password, retype_new_password);
                $('#change-password').modal('hide');
              });
            });
            $('.action-delete').click(function() {
              var current_email = $('.email-account', $(this).closest('.tier')).val();
              $('#delete-confirm').modal();
              $('#confirm-delete-email').unbind('click').click(function(){
                confirm_delete_email(current_email);
                $('#delete-confirm').modal('hide');
              });
            });
            email_row.slideDown(200, function(){
              $(this).removeAttr('style');
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

function empty(value){
  return (!value || value.length<=0);
}

function is_email_account(value){
  var filter = /^([a-zA-Z0-9_\.\-])+$/;
  return filter.test(value);
}

function is_email(value){
  var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return filter.test(value);
}

function email_list_template(email_account){
  var email_tmpl =
    '<div class="tier" style="display:none;">'+
      '<input type="hidden" class="email-account" value="'+email_account+'" />'+
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

function bootstrap_alert(message, alert_type){
  var alert_elm = $('#alert');
  alert_elm.html('<div class="alert alert-'+alert_type+'"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')
  alert_elm.fadeIn(500, function(){
    setTimeout(function(){alert_elm.fadeOut(1000);}, 3000)
  });
}

function show_add_email_spinner(){
  $('<div id="mailboxes-loader"><div class="loader"></div></div>').insertBefore("#emails-container");
  $('#mailboxes-loader .loader').spin('medium-left', '#000000');
  $('#mailboxes-loader').slideDown(200);
}

function confirm_delete_email(email){
  show_add_email_spinner();
  var data = {
    action: 'delete_email',
    'email': email
  };
  $.post(ajaxurl, data, function(response) {
    var json_response = JSON.parse(response);
    $('#mailboxes-loader').slideUp(200, function(){
      $(this).remove();
      if(json_response.status==1){
        var tier = $("#emails-container .tier").filter(function(){
          return $('.email-account', this).val()==json_response.account_email;
        });
        tier.slideUp(200, function(){$(this).remove();});
        bootstrap_alert(json_response.status_message, 'success');
      }
      else {
        bootstrap_alert(json_response.status_message, 'error');
      }
    });
  });
}

function confirm_change_password(email, new_password_elm, retype_new_password_elm, callback){
  var new_password = new_password_elm.val();
  var retype_new_password = retype_new_password_elm.val();
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

  if(!valid){
    bootstrap_alert(error_string, 'error');
  }
  else {
    // Do callback, which is close the modal when validation is okay
    if(typeof(callback)=='function') callback();
    // Reset the form
    new_password_elm.val('');
    retype_new_password_elm.val('');
    show_spinner();
    var data = {
      action: 'change_password',
      'email': email,
      'password': new_password
    };
    $.post(ajaxurl, data, function(response) {
      var json_response = JSON.parse(response);
      $('#mailboxes-loader').slideUp(200, function(){
        $(this).remove();
        if(json_response.status==1){
          bootstrap_alert(json_response.status_message, 'success');
        }
        else {
          bootstrap_alert(json_response.status_message, 'error');
        }
      });
    });
  }
}
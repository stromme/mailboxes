<div class="wrap">
  <?php screen_icon('options-general'); ?>
  <h2>Mailboxes Settings<?php if($setting_exists){ ?><a id="add_email_button" href="#" class="add-new-h2">Add Email</a><?php } ?></h2>

  <?php if($setting_exists){ ?>
  <div class="metabox-holder">
    <div id="add_new_email" class="stuffbox <?=(!$show_form)?'hidden':''?>">
      <h3>Add Email</h3>
      <div class="inside">
        <form method="post" action="">
          <input type="hidden" name="action" value="add" />
          <table id="api_settings_cpanel" class="form-table">
            <tr valign="top">
              <th scope="row"><label for="username">Username</label></th>
              <td>
                <input type="text" id="username" name="username" value="<?=$new_username?>" />@<?=$domain?>
                <p class="description">Example: <strong>username</strong> only, not username@domain.com</p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><label for="password">Password</label></th>
              <td>
                <input type="password" id="password" name="password" value="<?=$new_password?>" />
                <p class="description">Minimum 5 characters</p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><label for="forwarding">Forwarding</label></th>
              <td>
                <input type="text" id="forwarding" name="forwarding" value="<?=$new_forwarding?>" /> (optional)
                <p class="description">Where you want the email to be forwarded. Leave empty if not used</p>
              </td>
            </tr>
          </table>
          <?php submit_button(); ?>
        </form>
      </div>
    </div>
  </div>

  <form method="post" action="">
  <?php
    $mailbox_list->prepare_items();
    $mailbox_list->display();
  ?>
  </form>
  <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery('#add_email_button').click(function(){jQuery('#add_new_email').slideToggle();});
    });
  </script>
  <?php } // if($setting_exists) ?>
</div>
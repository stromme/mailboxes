<div class="wrap">
  <?php screen_icon('options-general'); ?>
  <h2>Mailboxes Settings</h2>

  <form method="post" action="">
    <table id="api_chooser" class="form-table">
      <tr valign="top">
        <th scope="row"><label for="api">Select API</label></th>
        <td>
          <select id="api" name="api">
            <option value="cpanel">cPanel</option>
          </select>
        </td>
      </tr>
    </table>
    <hr />
    <table id="api_settings_cpanel" class="form-table">
      <tr valign="top">
        <th scope="row"><label for="host">Website Host</label></th>
        <td>
          <input type="text" id="host" name="host" value="<?=$host?>"/>
          <span class="description">Example: <strong>www.uzbuz.com</strong></span>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="domain">Domain</label></th>
        <td>
          <input type="text" id="domain" name="domain" value="<?=$domain?>"/>
          <span class="description">Example: <strong>uzbuz.com</strong></span>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="port">cPanel Port</label></th>
        <td>
          <input type="text" id="port" name="port" class="small-text" value="<?=$port?>"/>
          <span class="description">Default is <strong>2082</strong></span>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label>Use SSL</label></th>
        <td>
          <input type="radio" id="ssl_1" name="ssl" value="1" <?=($ssl_yes)?'checked="checked"':'';?> /> <label for="ssl_1">Yes</label> &nbsp;
          <input type="radio" id="ssl_2" name="ssl" value="0" <?=($ssl_no)?'checked="checked"':'';?> /> <label for="ssl_2">No</label>
          <p class="description">Default is No</p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="quota">Default Quota</label></th>
        <td>
          <input type="text" id="quota" name="quota" class="small-text" value="<?=$quota?>"/> Mb
          <p class="description">Size is in Megabyte, leave empty for unlimited quota size</p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="username">cPanel Username</label></th>
        <td>
          <input type="text" id="username" name="username" value="<?=$username?>"/>
          <p class="description">Your cPanel login username</p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="password">cPanel Password</label></th>
        <td>
          <input type="password" id="password" name="password" value="<?=password?>"/>
          <p class="description">Your cPanel password</p>
        </td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>
</div>
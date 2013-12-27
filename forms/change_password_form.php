<div class="wrap">
  <h2>Change Password</h2>

  <?php if($setting_exists){ ?>
  <form method="post" action="">
    <table id="api_settings_cpanel" class="form-table">
      <tr valign="top">
        <th scope="row"><label for="username">Email</label></th>
        <td>
          <input type="text" id="username" name="username" value="<?=$email?>" readonly="readonly" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="password">Password</label></th>
        <td>
          <input type="password" id="password" name="password" value="" />
          <span class="description">Minimum 5 characters</span>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row"><label for="retype_password">Retype Password</label></th>
        <td>
          <input type="password" id="retype_password" name="retype_password" value="" />
          <span class="description">Retype your new password</span>
        </td>
      </tr>
    </table>
    <p class="submit">
      <?php submit_button(null,'primary','submit',false); ?> 
      <input type="button" class="button primary" value="Back to list" onclick="location.href='./tools.php?page=<?=$_GET['page']?>'" />
    </p>
  </form>
  <?php } // if($setting_exists) ?>
</div>
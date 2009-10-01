
<style>@import '<?php echo site_url('css/users.css'); ?>';</style>
<script type="text/javascript" src="<?php echo site_url('js/users.js'); ?>"></script>

<form action="" method="post">
  <table class="user-list">
    <tr>
      <td><?php echo USERS_USERNAME; ?>:</td>
      <td><input type="text" name="username" value="<?php echo $user->username; ?>" /></td>
    </tr>
  
    <tr>
      <td><?php echo USERS_PASSWORD; ?>:</td>
      <td><input type="password" name="password" /></td>
    </tr>

    <tr>
      <td valign="top"><?php echo USERS_ACCESS; ?>:</td>
      <td class="text-left">
        <ul id="access_list">
          <?php echo user_model::print_checkboxes($access, '', (array) $user->access); ?>
        </ul>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="text-right">
        <input type="submit" value="<?php echo BASE_SAVE; ?>" />
      </td>
    </tr>
  </table>
</form>


<style>@import '<?php echo site_url('css/users.css'); ?>';</style>
<script type="text/javascript" src="<?php echo site_url('js/users.js'); ?>"></script>

<table class="user-list info">
  <tr>
    <th>ID</th>
    <th><?php echo strtoupper(USERS_USERNAME); ?></th>
    <th></th>
    <th></th>
  </tr>
<?php foreach ($users as $tmp_user): ?>
  <tr<?php if (empty($class) || $class == ''){ echo ''; $class = 'tr1'; }else{ echo ' class="'. $class .'"'; $class = ''; }?>>
    <td><?php echo $tmp_user->id; ?></td>
    <td><?php echo $tmp_user->username; ?></td>
    <td><a href="<?php echo site_url('users/edit/'. $tmp_user->id); ?>" title="<?php echo BASE_EDIT; ?>"><img src="<?php echo base_url('css/images/pencil.png'); ?>" alt="<?php echo BASE_EDIT; ?>" /></a></td>
    <td><a href="#" onclick="if (confirm('<?php echo BASE_RECORD_CONFIRM; ?>')){ location.href = '<?php echo site_url('users/delete/'. $tmp_user->id); ?>'; } return false;" title="<?php echo BASE_DELETE; ?>"><img src="<?php echo base_url('css/images/delete.png'); ?>" alt="<?php echo BASE_EDIT; ?>" /></a></td>
  </tr>
<?php endforeach; ?>
  <tr class="<?php echo $class; ?>">
    <td colspan="3"></td>
    <td colspan="2"><a href="<?php echo site_url('users/add'); ?>" title="<?php echo BASE_ADD; ?>"><img src="<?php echo base_url('css/images/add.png'); ?>" alt="<?php echo BASE_ADD; ?>" /></a></td>
  </tr>
</table>

<style>@import '<?php echo site_url('css/users.css'); ?>';</style>
<script type="text/javascript" src="<?php echo site_url('js/users.js'); ?>"></script>

<table class="user-list">
  <tr>
    <th>ID</th>
    <th>USERNAME</th>
    <th>ACCESS</th>
    <th></th>
    <th></th>
  </tr>
<?php foreach ($users as $tmp_user): ?>
  <tr>
    <td><?php echo $tmp_user->id; ?></td>
    <td><?php echo $tmp_user->username; ?></td>
    <td><?php echo $tmp_user->access; ?></td>
    <td><a href="<?php echo site_url('users/edit/'. $tmp_user->id); ?>">Labot</a></td>
    <td><a href="<?php echo site_url('users/delete/'. $tmp_user->id); ?>" onclick="return confirm('Tiešām dzēst lietotāju?');">Dzēst</a></td>
  </tr>
<?php endforeach; ?>
  <tr>
    <td colspan="3"></td>
    <td colspan="2"><a href="<?php echo site_url('users/add'); ?>">Pievienot</a></td>
  </tr>
</table>
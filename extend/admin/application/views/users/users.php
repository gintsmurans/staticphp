
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
    <td><a href="<?php echo site_url('users/edit/'. $tmp_user->id); ?>" title="Edit"><img src="<?php echo base_url('css/images/pencil.png'); ?>" alt="" /></a></td>
    <td><a href="#" onclick="if (confirm('Tiešām dzēst lietotāju?')){ location.href = '<?php echo site_url('users/delete/'. $tmp_user->id); ?>'; } return false;" title="Delete"><img src="<?php echo base_url('css/images/delete.png'); ?>" alt="" /></a></td>
  </tr>
<?php endforeach; ?>
  <tr>
    <td colspan="3"></td>
    <td colspan="2"><a href="<?php echo site_url('users/add'); ?>" title="Add"><img src="<?php echo base_url('css/images/add.png'); ?>" alt="" /></a></td>
  </tr>
</table>
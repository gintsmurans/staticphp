
<img src="<?php echo base_url('css/images/add_large.png'); ?>" /> <a href="<?php echo base_url('navigation/index/'. g('nav')->language .'/'. g('nav')->menu_id .'/'. g('nav')->nav_id .'/add'); ?>">Add new post</a>

<?php if (!empty($posts)): ?>
  <table class="texts_list info">
    <tr>
      <th>id</th>
      <th>Title</th>
      <th width="16"></th>
      <th width="16"></th>
    </tr>

  <?php foreach ($posts as $item): ?>
  <tr class="<?php if (empty($class) || $class == ''){ echo ''; $class = 'tr1'; }else{ echo $class; $class = ''; } if (empty($item->active)){ echo ' inactive'; }?>">
    <td><?php echo $item->id; ?></td>
    <td><?php echo $item->title; ?></td>
    <td><a href="<?php echo base_url('navigation/index/'. g('nav')->language .'/'. g('nav')->menu_id .'/'. g('nav')->nav_id .'/edit/'. $item->id); ?>"><img src="<?php echo base_url('css/images/pencil.png'); ?>" alt="" /></a></td>
    <td><a href="#" onclick="if (confirm('Are you sure want to delete this record?')){ location.href = '<?php echo site_url('texts/delete_item/'. g('nav')->language .'/'. g('nav')->menu_id .'/'. g('nav')->nav_id .'/'. $item->id); ?>'; } return false;"><img src="<?php echo base_url('css/images/delete.png'); ?>" alt="" /></a></td>
  </tr>
  <?php endforeach; ?>

  </table>
<?php endif; ?>

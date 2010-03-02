
<p id="msg">&nbsp;</p>

<div class="menu-tree">
  <?php if (user_model::_access('languages', 'add_new')): ?><div><img src="<?php echo base_url('css/images/add_large.png'); ?>" /> <a href="<?php echo site_url('navigation/add_new'); ?>"><?php echo NAV_ADD_MENU; ?></a></div><?php endif; ?>
  <?php echo navigation_model::generate_menu_tree(); ?>
</div>

<div class="menu-content">

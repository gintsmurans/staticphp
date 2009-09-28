<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php echo BASE_PAGE_TITLE; ?></title>
		<style>@import '<?php echo base_url('css/style.css'); ?>'; <?php css(); ?> </style>
		<script type="text/javascript" src="<?php echo base_url('js/jquery-1.2.6.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo site_url('home/base_js'); ?>"></script>
		<?php js(); ?>
	</head>
	<body>
    <div class="page">
      
      <div class="menu">

        <?php if (user_model::_access('navigation', 'index')): ?>
        <img src="<?php echo base_url('css/images/navigation.png'); ?>" alt="<?php echo BASE_NAVIGATION; ?>" /> <a href="<?php echo site_url('navigation'); ?>"<?php if (router::$class == 'navigation'){ echo ' class="active"'; } ?>><?php echo BASE_NAVIGATION; ?></a>
        <?php endif; ?>

        <?php if (user_model::_access('languages', 'index')): ?>
        <img src="<?php echo base_url('css/images/languages.png'); ?>" alt="<?php echo BASE_LANGUAGES; ?>" /> <a href="<?php echo site_url('languages'); ?>"<?php if (router::$class == 'languages'){ echo ' class="active"'; } ?>><?php echo BASE_LANGUAGES; ?></a>
        <?php endif; ?>

        <?php if (user_model::_access('users', 'index')): ?>
        <img src="<?php echo base_url('css/images/users.png'); ?>" alt="<?php echo BASE_USERS; ?>" /> <a href="<?php echo site_url('users'); ?>"<?php if (router::$class == 'users'){ echo ' class="active"'; } ?>><?php echo BASE_USERS; ?></a>
        <?php endif; ?>

        <img src="<?php echo base_url('css/images/logout.png'); ?>" alt="<?php echo BASE_LOGOUT; ?>" /> <a href="<?php echo site_url('login/out'); ?>"><?php echo BASE_LOGOUT; ?></a>
      </div>
      
      <div class="clear"></div>
      <noscript><p class="nojs"><?php echo BASE_JS_WARNING; ?></p></noscript>
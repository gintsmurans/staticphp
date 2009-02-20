<?php load(ADMIN_PATH.'views/header'); ?>


    <script type="text/javascript" src="<?php echo site_url('js/languages_auth.js'); ?>"></script>

    <div class="password">
        <input type="password" id="auth" value="" onkeyup=" if (event.keyCode == 13){ auth(this); }else if (event.keyCode == 27){ this.value = ''; } " /> <img id="loader" src="<?php echo base_url('css/images/loader.gif'); ?>" />
    </div>

<?php load(ADMIN_PATH.'views/footer'); ?>
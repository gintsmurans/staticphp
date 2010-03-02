<style>@import '<?php echo base_url('css/style.css'); ?>'; @import '<?php echo base_url('css/jbrowser.css'); ?>';</style>
<script type="text/javascript" src="<?php echo base_url('js/jquery-1.2.6.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('home/base_js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/jbrowser.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/swfupload.js'); ?>"></script>
<script type="text/javascript">
  window.settings = '<?php echo json_encode($settings); ?>';
  window.session_id = '<?php echo session_id(); ?>';
</script>

<div class="jbrowser">
  <div class="jtabs">
    <div id="jbrowse_tab" class="jactive">Browse</div><div id="jupload_tab">Upload</div>
    <p class="clear"></p>
  </div>
  
  <div class="jbrowser">
  
    <div id="jcontent">
      <?php load('views/jbrowser/browse', $vars); ?>
    </div>
  </div>
</div>
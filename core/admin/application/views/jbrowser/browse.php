<div class="jfiles">
  <div>
    Scope: 
    <select id="jscope_select">
      <option></option>
      <?php foreach ($scopes as $scope): ?>
      <option value="<?php echo $scope->scope; ?>"<?php if (!empty($settings->scope) && $settings->scope == $scope->scope){ echo ' selected="selected"'; } ?>><?php echo $scope->scope; ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php if (!empty($files)): foreach ($files as $file): ?>
  <div class="jfile" id="jfile-<?php echo $file->id; ?>"><img src="<?php echo base_url(g('jbrowser')->img_path . $file->scope .'/'. $file->filename .'-50x50'. $file->ext); ?>" width="50" height="50" align="bilde" /><br /><small><?php echo $file->name; ?></small></div>
  <?php endforeach; endif; ?>
</div>

<div id="jpreview"></div>

<div class="clear"></div>
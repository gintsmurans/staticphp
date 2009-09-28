<div class="jupload">
  <div>
    Scope: 
    <select id="jscope_select">
      <option></option>
      <?php foreach ($scopes as $scope): ?>
      <option value="<?php echo $scope->scope; ?>"<?php if (!empty($settings->scope) && $settings->scope == $scope->scope){ echo ' selected="selected"'; } ?>><?php echo $scope->scope; ?></option>
      <?php endforeach; ?>
    </select> or new <input type="text" id="jscope_input" />
  </div>
  
  <div id="jupload_wrapper"><div id="jupload_holder"></div></div>
  <div class="jfile_status">
    <span id="jfilename"></span>
    <span id="jpercent"></span>
  </div>
</div>
<style>@import '<?php echo base_url('css/languages.css'); ?>';</style>

<?php if (!empty($error)): ?>
  <pre><?php echo $error; ?></pre>
<?php else: ?>

  <style>@import '<?php echo base_url('css/jquery.wysiwyg.css'); ?>';</style>

  <script type="text/javascript" src="<?php echo site_url('js/jquery.wysiwyg.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('js/languages.js'); ?>"></script>
  <script type="text/javascript">
      var languages = <?php echo json_encode(array_slice(languages_model::$fields, 1)); ?>;
  </script>

  <div id="msg_failed" class="msg_failed"><?php if (!empty($msg_failed)): echo $msg_failed; endif; ?></div>
  <div id="msg_ok" class="msg_ok"><?php if (!empty($msg_ok)): echo $msg_ok; endif; ?></div>
  
  <div class="add-language">
    <input type="text" id="add_language" /> 
    <img src="<?php echo base_url('css/images/add.png'); ?>" /> <span id="add_language_button" class="aslink">Add language</span> | 
    <img src="<?php echo base_url('css/images/down.png'); ?>" /> <span id="copy_to_web" class="aslink">Copy all enabled languages to website</span>
  </div>


  <div id="translations">
    <table border="1" cellspacing="0" cellpadding="4" align="center" class="translation_table">
  
        <tr>
            <th width="24"></th>
            <th width="200">
                Scope<br />
  
                <?php if (!empty($scopes)): ?>
                <select onchange="location.href = '<?php echo site_url('language/index/'); ?>' + this.value;">
                    <option value="0"></option>
                    <?php foreach ($scopes as $scope): ?>
                    <option value="<?php echo $scope->scope; ?>"<?php if (router::segment(2) == $scope->scope){ echo ' selected="selected"'; }?>><?php echo $scope->scope; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </th>
            
            <th>Ident</th>
            
            <?php foreach($languages as $lang): ?>
            <th>
              <?php echo $lang; ?>
              <div style="float: right;">
                <a href="#" title="Copy &quot;<?php echo $lang; ?>&quot; to website" onclick="if (confirm('Are you sure want to copy this language to website?')){ window.location.href = '<?php echo site_url('languages/copy_to_web/'. $lang); ?>'; } return false;"><img src="<?php echo base_url('css/images/down'. (in_array($lang, g('config')->languages) ? '' : '-gray') .'.png'); ?>" /></a>
              <?php if ($lang != g('config')->lang_default): ?>
                <a href="#" title="<?php echo (in_array($lang, g('config')->languages) ? 'Disable' : 'Enable'); ?>" onclick="if (confirm('Are you sure want to enable/disable this language?')){ window.location.href = '<?php echo site_url('languages/activate_language/'. $lang); ?>'; } return false;"><img src="<?php echo site_url('css/images/tick'. (in_array($lang, g('config')->languages) ? '' : '-gray') .'.png'); ?>" alt="" /></a>&nbsp;
                <a href="#" title="Delete" onclick="if (confirm('Are you sure want to delete this whole language?')){ window.location.href = '<?php echo site_url('languages/delete_language/'. $lang); ?>'; } return false;"><img src="<?php echo site_url('css/images/delete.png'); ?>" alt="" /></a>
              <?php endif; ?>
              </div>
            </th>
            <?php endforeach; ?>
        </tr>
  
    <?php if (!empty($translations)): foreach($translations as $item): ?>
        <tr id="item-<?php echo $item->ident; ?>">
            <td class="hover" onclick="if (confirm('Are you sure want to delete this item?')){ delete_item('<?php echo $item->ident; ?>'); }"><img src="<?php echo site_url('css/images/delete.png'); ?>" alt="" /></td>
            <td class="hover" onclick="change(this, '<?php echo $item->ident; ?>', 'scope');"><?php echo $item->scope; ?></td>
            <td class="hover" onclick="change(this, '<?php echo $item->ident; ?>', 'ident');"><?php echo $item->ident; ?></td>
  
            <?php foreach($languages as $lang): ?>
            <td class="hover" onclick="change(this, '<?php echo $item->ident; ?>', '<?php echo $lang; ?>');"><?php echo $item->{$lang}; ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; endif; ?>
    
        <tr id="insert">
            <td colspan="<?php echo count($languages) + 3; ?>" align="right">
              Ident: 
              <input type="text" id="add_item" />
              <span id="add_item_button" class="aslink">Add item</span>
            </td>
        </tr>
    </table>
  </div>
<?php endif; ?>

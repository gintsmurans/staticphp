
<?php if (!empty($error)): ?>
  <pre><?php echo $error; ?></pre>
<?php else: ?>

  <div id="msg_failed" class="msg_failed"><?php if (!empty($msg_failed)): echo $msg_failed; endif; ?></div>
  <div id="msg_ok" class="msg_ok"><?php if (!empty($msg_ok)): echo $msg_ok; endif; ?></div>

  <div class="add-language">
    <?php if (user_model::_access('languages', 'add_language')): ?>
    <input type="text" id="add_language" /> 
    <span id="add_language_handler"><img src="<?php echo base_url('css/images/add.png'); ?>" alt="" /> <span id="add_language_button" class="aslink"><?php echo LANGUAGES_ADD_LANGUAGE; ?></span></span> | 
    <?php endif; ?>

    <?php if (user_model::_access('languages', 'add_language')): ?>
    <img src="<?php echo base_url('css/images/down.png'); ?>" alt="" /> <span id="copy_to_web" class="aslink"><?php echo LANGUAGES_COPY_TO_WEB; ?></span> | 
    <?php endif; ?>
    
    <?php if (user_model::_access('languages', 'add_language')): ?>
    <img src="<?php echo base_url('css/images/up.png'); ?>" alt="" /> <span id="copy_from_web" class="aslink"><?php echo LANGUAGES_COPY_FROM_WEB; ?></span>
    <?php endif; ?>
  </div>

  <div id="translations">
    <table class="info">
      <thead>
        <tr>
            <?php if (user_model::_access('languages', 'add_itema')): ?>
            <th width="24"></th>
            <?php endif; ?>

            <th width="200">
                <?php echo LANGUAGES_SCOPE; ?><br />
  
                <?php if (!empty($scopes)): ?>
                <select id="scope_change">
                    <option value="0"></option>
                    <?php foreach ($scopes as $scope): ?>
                    <option value="<?php echo $scope->scope; ?>"<?php if (router::segment(2) == $scope->scope){ echo ' selected="selected"'; }?>><?php echo $scope->scope; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <?php if (user_model::_access('languages', 'copy_scope_to_web')): ?>
                <span id="copy_scope_to_web" class="aslink"><img src="<?php echo base_url('css/images/down'. (empty($current_scope) ? '-gray' : '') .'.png'); ?>" alt="" /></span>
                <?php endif; ?>

                <?php if (user_model::_access('languages', 'copy_scope_from_web')): ?>
                <span id="copy_scope_from_web" class="aslink"><img src="<?php echo base_url('css/images/up'. (empty($current_scope) ? '-gray' : '') .'.png'); ?>" alt="" /></span>
                <?php endif; ?>

                <?php endif; ?>
            </th>
            
            <th><?php echo LANGUAGES_IDENT; ?></th>
            
            <?php foreach($languages as $lang): ?>
            <th>
              <?php echo $lang; ?>
              <div style="float: right;">
                <?php if (user_model::_access('languages', 'copy_to_web')): ?>
                <a href="#" title="<?php echo str_replace('!lang', $lang, LANGUAGES_COPY_LANG_TO_WEB); ?>" onclick="if (confirm('<?php echo LANGUAGES_CONFIRM1; ?>')){ window.location.href = '<?php echo site_url('languages/copy_to_web/'. $lang); ?>'; } return false;"><img src="<?php echo base_url('css/images/down'. (in_array($lang, g('config')->languages) ? '' : '-gray') .'.png'); ?>" /></a>
                <?php endif; ?>

                <?php if (user_model::_access('languages', 'copy_from_web')): ?>
                <a href="#" title="<?php echo str_replace('!lang', $lang, LANGUAGES_COPY_LANG_FROM_WEB); ?>" onclick="if (confirm('<?php echo LANGUAGES_CONFIRM2; ?>')){ window.location.href = '<?php echo site_url('languages/copy_from_web/'. $lang); ?>'; } return false;"><img src="<?php echo base_url('css/images/up'. (in_array($lang, g('config')->languages) ? '' : '-gray') .'.png'); ?>" /></a>
                <?php endif; ?>

              <?php if ($lang != g('config')->lang_default): ?>
                <?php if (user_model::_access('languages', 'activate_language')): ?>
                <a href="#" title="<?php echo (in_array($lang, g('config')->languages) ? LANGUAGES_DISABLE : LANGUAGES_ENABLE); ?>" onclick="if (confirm('<?php echo LANGUAGES_CONFIRM3; ?>')){ window.location.href = '<?php echo site_url('languages/activate_language/'. $lang); ?>'; } return false;"><img src="<?php echo site_url('css/images/tick'. (in_array($lang, g('config')->languages) ? '-gray' : '') .'.png'); ?>" alt="" /></a>&nbsp;
                <?php endif; ?>
                
                <?php if (user_model::_access('languages', 'delete_language')): ?>
                <a href="#" title="<?php echo BASE_DELETE; ?>" onclick="if (confirm('<?php echo LANGUAGES_CONFIRM4; ?>')){ window.location.href = '<?php echo site_url('languages/delete_language/'. $lang); ?>'; } return false;"><img src="<?php echo site_url('css/images/delete.png'); ?>" alt="" /></a>
                <?php endif; ?>
              <?php endif; ?>
              </div>
            </th>
            <?php endforeach; ?>
        </tr>
      </thead>
  
      <tbody>
      <?php if (!empty($translations)): foreach($translations as $item): ?>
        <tr id="item-<?php echo $item->ident; ?>"<?php if (empty($class) || $class == ''){ echo ''; $class = 'tr1'; }else{ echo ' class="'. $class .'"'; $class = ''; }?>>
            <?php if (user_model::_access('languages', 'add_itema')): ?>
            <td class="hover delete" onclick="if (confirm('<?php echo LANGUAGES_CONFIRM5; ?>')){ delete_item('<?php echo $item->ident; ?>'); }"><img src="<?php echo site_url('css/images/delete.png'); ?>" alt="" /></td>
            <?php endif; ?>

            <td class="hover"<?php if (user_model::_access('languages', 'edit_item')): ?> onclick="change(this, '<?php echo $item->ident; ?>', 'scope');"<?php endif; ?>><?php echo $item->scope; ?></td>
            <td class="hover"<?php if (user_model::_access('languages', 'edit_item')): ?> onclick="change(this, '<?php echo $item->ident; ?>', 'ident');"<?php endif; ?>><?php echo $item->ident; ?></td>
  
            <?php foreach($languages as $lang): ?>
            <td class="hover"><div class="edit"<?php if (user_model::_access('languages', 'edit_item')): ?> onclick="change(this, '<?php echo $item->ident; ?>', '<?php echo $lang; ?>');"<?php endif; ?>><?php echo $item->{$lang}; ?></div></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; endif; ?>
    </tbody>
    
    <?php if (user_model::_access('languages', 'add_item')): ?>
    <tfoot>
      <tr id="insert" class="<?php echo $class; ?>">
          <td colspan="<?php echo count($languages) + 3; ?>" align="right">
            <?php echo LANGUAGES_IDENT; ?>
            <input type="text" id="add_item" />
            <span id="add_item_handler">
              <img src="<?php echo base_url('css/images/add.png'); ?>" /> <span id="add_item_button" class="aslink"><?php echo LANGUAGES_ADD_ITEM; ?></span>
            </span>
          </td>
      </tr>
    </tfoot>
    <?php endif; ?>
    </table>
  </div>
<?php endif; ?>

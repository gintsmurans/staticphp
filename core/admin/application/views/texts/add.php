
<table class="texts_table" cellpadding="2">
  <tr>
    <th colspan="2"><?php TEXTS_ADD_NEW_POST; ?></th>
  </tr>
  <tr>
    <td><?php echo TEXTS_TITLE; ?></td>
    <td><input type="text" class="text" id="texts_title" /></td>
  </tr>

  <tr>
    <td><?php echo TEXTS_URL; ?></td>
    <td><input type="text" class="text" id="texts_url" /> <span id="texts_get_url_button" class="aslink"><img src="<?php echo base_url('css/images/refresh.png'); ?>" /></span></td>
  </tr>

  <tr>
    <td colspan="2" id="images_row"></td>
  </tr>

  <tr>
    <td valign="top"><?php echo TEXTS_TEXT; ?></td>
    <td><textarea id="texts_text"></textarea></td>
  </tr>

  <tr>
    <td><?php echo TEXTS_ACTIVE; ?></td>
    <td><input type="checkbox" id="texts_active" /></td>
  </tr>

  <tr>
    <td colspan="2" align="right"><input type="submit" class="submit" id="texts_add_button" value="<?php echo BASE_SAVE; ?>" /></td>
  </tr>
</table>

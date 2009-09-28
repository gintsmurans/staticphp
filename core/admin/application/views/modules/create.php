
<form action="" method="post">
  <table>
    <tr>
      <td><?php echo MODULES_NAME; ?></td>
      <td><input type="text" name="name" /></td>
    </tr>
    <tr>
      <td valign="top"><?php echo MODULES_DESCRIPTION; ?></td>
      <td><textarea name="description"></textarea></td>
    </tr>
    <tr>
      <td valign="top"><?php echo MODULES_FIELDS; ?></td>
      <td>

        <table class="info">
          <tr>
            <th>Type</th>
            <th>Type settings</th>
            <th>Name</th>
            <th>Field name</th>
            <th>Default value</th>
            <th>Field length</th>
            <th></th>
            <th></th>
          </tr>
          <tr id="module_null">
            <td>
              <select class="type">
                <option value="text">text</option>
                <option value="checkbox">checkbox</option>
                <option value="radio">radio</option>
                <option value="textarea">textarea</option>
                <option value="select">select</option>
                <option value="file_upload">File upload</option>
              </select>
            </td>
            <td></td>
            <td><input type="text" class="name" /></td>
            <td><input type="text" class="field_name" /></td>
            <td><input type="text" class="default_value" /></td>
            <td><input type="text" class="field_length" /></td>
            <td class="save"><a href="#" id="add_field_button"><img src="<?php echo base_url('css/images/save.png'); ?>" alt="<?php echo BASE_SAVE; ?>" /></a></td>
            <td class="delete"><a href="#" id="add_field_button"><img src="<?php echo base_url('css/images/delete-gray.png'); ?>" alt="<?php echo BASE_DELETE; ?>" /></a></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>

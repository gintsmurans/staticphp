<div class="menu-content">
  <table>
    <tr>
      <th colspan="2"><?php echo NAV_EDIT_ITEM; ?></th>
    </tr>
    <tr>
      <td><?php echo NAV_TITLE; ?></td>
      <td><input type="text" id="nav_title" value="<?php echo $nav_item->title; ?>" /></td>
    </tr>
    <tr>
      <td><?php echo NAV_URL; ?></td>
      <td><input type="text" id="nav_url" value="<?php echo $nav_item->url; ?>" /> <span id="nav_get_url_button" class="aslink"><img src="<?php echo base_url('css/images/refresh.png'); ?>" /></span></td>
    </tr>
    <tr>
      <td><?php echo NAV_MODEL; ?></td>
      <td>
        <select id="nav_model">
          <option></option>
          <?php foreach ($models as $model): ?>
          <option value="<?php echo $model->id; ?>"<?php if ($model->id == $nav_item->model_id){ echo ' selected="selected"'; } ?>><?php echo $model->name; ?></option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><?php echo NAV_ACTIVE; ?></td>
      <td>
        <input type="checkbox" id="nav_active" value="1"<?php if (!empty($nav_item->active)){ echo ' checked="checked"'; } ?> />
      </td>
    </tr>
    <tr>
      <td colspan="2" align="right">
        <input type="submit" class="submit" id="nav_edit_button" value="<?php echo BASE_SAVE; ?>" />
      </td>
    </tr>
  </table>
</div>
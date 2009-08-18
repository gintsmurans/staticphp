<div class="menu-content">
  <table>
    <tr>
      <th colspan="2"><?php echo (router::$method == 'add_new' ? NAV_ADD_MENU : NAV_ADD_NEW_ITEM); ?></th>
    </tr>
    <tr>
      <td><?php echo NAV_TITLE; ?></td>
      <td><input type="text" id="nav_title" /></td>
    </tr>
    <tr>
      <td><?php echo NAV_URL; ?></td>
      <td><input type="text" id="nav_url" /> <span id="nav_get_url_button" class="aslink"><img src="<?php echo base_url('css/images/refresh.png'); ?>" /></span></td>
    </tr>
    <tr>
      <td><?php echo NAV_MODEL; ?></td>
      <td>
        <select id="nav_model">
          <option></option>
          <?php foreach ($models as $model): ?>
          <option value="<?php echo $model->id; ?>"><?php echo $model->name; ?></option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <tr>
      <td><?php echo NAV_ACTIVE; ?></td>
      <td>
        <input type="checkbox" id="nav_active" value="1" />
      </td>
    </tr>
    <tr>
      <td colspan="2" align="right">
        <input type="submit" class="submit" id="nav_add_button" value="<?php echo BASE_SAVE; ?>" />
      </td>
    </tr>
  </table>
</div>
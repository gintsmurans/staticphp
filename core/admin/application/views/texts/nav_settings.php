
<table style="margin: 10px;">
  <tr>
    <td>Image upload path:</td>
    <td><input type="text" name="settings[image_path]"<?php if (!empty(navigation_model::$menu_array[g('nav')->nav_id]->settings->image_path)){ echo ' value="'. navigation_model::$menu_array[g('nav')->nav_id]->settings->image_path .'"'; }?> /></td>
  </tr>
  <tr>
    <td>Image size large:</td>
    <td>
      <input type="text" name="settings[size_l]"<?php if (!empty(navigation_model::$menu_array[g('nav')->nav_id]->settings->size_l)){ echo ' value="'. navigation_model::$menu_array[g('nav')->nav_id]->settings->size_l .'"'; }?> />
      <input type="checkbox" name="settings[size_l_crop]" value="1"<?php if (!empty(navigation_model::$menu_array[g('nav')->nav_id]->settings->size_l_crop)){ echo ' checked="checked"'; }?> /> Crop
    </td>
  </tr>
  <tr>
    <td>Image size medium:</td>
    <td>
      <input type="text" name="settings[size_m]"<?php if (!empty(navigation_model::$menu_array[g('nav')->nav_id]->settings->size_m)){ echo ' value="'. navigation_model::$menu_array[g('nav')->nav_id]->settings->size_m .'"'; }?> />
      <input type="checkbox" name="settings[size_m_crop]" value="1"<?php if (!empty(navigation_model::$menu_array[g('nav')->nav_id]->settings->size_m_crop)){ echo ' checked="checked"'; }?> /> Crop
    </td>
  </tr>
  <tr>
    <td>Image size small:</td>
    <td>
      <input type="text" name="settings[size_s]"<?php if (!empty(navigation_model::$menu_array[g('nav')->nav_id]->settings->size_s)){ echo ' value="'. navigation_model::$menu_array[g('nav')->nav_id]->settings->size_s .'"'; }?> />
      <input type="checkbox" name="settings[size_s_crop]" value="1"<?php if (!empty(navigation_model::$menu_array[g('nav')->nav_id]->settings->size_s_crop)){ echo ' checked="checked"'; }?> /> Crop
    </td>
  </tr>
</table>

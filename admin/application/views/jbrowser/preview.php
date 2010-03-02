<div id="jdelete"><img src="<?php echo base_url('css/images/delete.png'); ?>" alt="<?php echo BASE_DELETE; ?>" /></div>
<div id="jimage">
  <a href="<?php echo base_url(g('jbrowser')->img_path . $image_o->scope .'/'. $image_o->filename . $image_o->ext); ?>" onclick="window.open(this.href); return false;">
    <img src="<?php echo base_url(g('jbrowser')->img_path . $image->scope .'/'. $image->filename . $image->ext); ?>" width="<?php echo $image->width; ?>" height="<?php echo $image->height; ?>" border="1" alt="<?php echo $image->name; ?>" />
  </a>
</div>
<div id="jdesc"><?php echo $image->name; ?></div>
<div id="juploaded"><?php echo $image->date; ?></div>
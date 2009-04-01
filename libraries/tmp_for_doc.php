<?php


  /**
    &#44; - ,
    valid:
      required,
      email,
      format[\d\d\d-\d\d-\d\d]
      ipv4,
      ipv6,
      credit_card,
      length[from, to[=<>]]
      equal[]
      

      integer,
      float[delimiter],
      string,
      
      upload[maxfilesize,allowed extensions]
      
    filter:
      trim[ /]
      + all php in built
  **/

    load('libraries/form_validation');

    $_FILES['file']['name'] = 'test.jpeg';
    $_FILES['file']['tmp_name'] = 'test.jpeg';
    $_FILES['file']['size'] = '4096';

    fv::init($_POST, $_FILES);

    echo (int) fv::ispost('email');
    
    ?>
      <form action="" method="post">
        <input type="text" name="email"<?php fv::set_input('email'); ?> />
        <input type="checkbox" name="test3[aa]" value="1"<?php fv::set_checkbox(array('test3', 'aa')); ?> />
        <input type="checkbox" name="test3[bb]" value="1"<?php fv::set_checkbox(array('test3', 'bb')); ?> />
        <select name="test[]" multiple="multiple">
          <option value="aa"<?php fv::set_select('test', 'aa'); ?>>aa</option>
          <option value="bb"<?php fv::set_select('test', 'bb'); ?>>bb</option>
          <option value="cc"<?php fv::set_select('test', 'cc'); ?>>cc</option>
        </select>
        <select name="test2[]">
          <option value="aa"<?php fv::set_select(array('test2', 0), 'aa'); ?>>aa</option>
          <option value="bb"<?php fv::set_select(array('test2', 0), 'bb'); ?>>bb</option>
          <option value="cc"<?php fv::set_select(array('test2', 0), 'cc'); ?>>cc</option>
        </select>
        <select name="test2[]">
          <option value="aa"<?php fv::set_select(array('test2', 1), 'aa'); ?>>aa</option>
          <option value="bb"<?php fv::set_select(array('test2', 1), 'bb'); ?>>bb</option>
          <option value="cc"<?php fv::set_select(array('test2', 1), 'cc'); ?>>cc</option>
        </select>
        <input type="submit" />
      </form>
    <?php
    
    fv::errors(array(
      'required' => 'field "!name" is required',
      'email' => '"!value" is not a valid e-mail address'
    )); 
    fv::add_rules(array(
      'email' => array(
        'valid' => array(
          'required',
          'email'
        ),
        'filter' => array(
          'trim[ /]',
          'ucfirst'
        ),
        'errors' => array(
          'required' => 'The e-mail field is empty'
        )
      ),
      'file' => array(
        'valid' => array(
          'upload_required',
          'upload_size[4194304]',
          'upload_ext[jpg jpeg]',
        ),
      ),
    ));

    if (!fv::validate())
    {
      print_r(fv::get_error('email'));
      print_r(fv::$errors_all);
    }
    else
    {
      print_r(fv::$post);
    }

?>
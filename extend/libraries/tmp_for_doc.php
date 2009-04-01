<?php


// TODO: 
//      fv - FILTER (XSS, etc), !custom validation functions!
//      language - context instead of scope, numbers, datetimes, etc
//      system.php helper - session timeout, etc.
//      cache library
//      auth - roles


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
    
    // print_r(fv::$post);

    // echo (int) fv::ispost('email');
    
    fv::errors(array(
      'required' => 'field "!name" is required',
      'email' => '"!value" is not a valid e-mail address'
    ));

    function test_me($value)
    {
      return false;
    }

    fv::add_rules(array(
      'email' => array(
        'valid' => array(
          'required',
          'email',
          'test_me'
        ),
        'filter' => array(
          'trim[ /]',
          'ucfirst'
        ),
        'errors' => array(
          'required' => 'The e-mail field is empty',
          'test_me' => 'ashgdjahgs jahsgd '
        )
      ),
      'test55' => array(
        'valid' => array(
          'required',
          'test_me'
        ),
        'filter' => array(
          'trim[ /]',
          'ucfirst',
          // 'strip_tags[<a>]',
          'xss'
        ),
        'errors' => array(
          'required' => 'The e-mail field is empty',
          'test_me' => 'ashgdjahgs jahsgd '
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
      // print_r(fv::get_error('email'));
      // print_r(fv::$errors_all);
    }

    //print_r(fv::$post);
    
    
    ?>
      <form action="" method="post">
        <textarea name="test55" cols="40" rows="10"><?php fv::set_value('test55'); ?></textarea>
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

?>
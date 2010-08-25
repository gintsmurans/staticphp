<h1>Welcome to the <a href="http://github.com/gintsmurans/staticphpc" target="_blank">StaticPHP framework</a> start page</h1>

<div class="content">

    <strong>Some variables:</strong>
    <ol>
      <li>Base uri: <strong><?php echo BASE_URL; ?></strong></li>
      <li>Site uri: <strong><?php echo router::site_url(); ?></strong></li>
    </ol>

    <strong>This page files (edit them to change this page):</strong>
    <ol>
      <li>Controller: <strong><?php echo APP_PATH . 'controllers/home.php'; ?></strong></li>
      <li>View: <strong><?php echo __FILE__; ?></strong></li>
      <li>CSS: <strong><?php echo PUBLIC_PATH . 'css/style.css'; ?></strong></li>
    </ol>

    <div>&nbsp;</div>

    <strong>All included files:</strong>
    <ol>
    <?php foreach (get_included_files() as $file): ?>
      <li><?php echo $file; ?></li>
    <?php endforeach; ?>
    </ol>

</div>

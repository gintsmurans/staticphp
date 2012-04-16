<h1>Welcome to the <a href="http://github.com/gintsmurans/staticphpc" target="_blank">StaticPHP framework</a> start page</h1>

<div class="content">
  <strong>Some variables and definitions:</strong>
  <ol>
    <li>BASE_URI: <strong><?php echo BASE_URI; ?></strong></li>
    <li>router::site_uri(): <strong><?php echo router::site_uri(); ?></strong><br /><br /></li>

    <li>BASE_PATH: <strong><?php echo BASE_PATH; ?></strong></li>
    <li>APP_PATH: <strong><?php echo APP_PATH; ?></strong></li>
    <li>PUBLIC_PATH: <strong><?php echo PUBLIC_PATH; ?></strong></li>
  </ol>

  <strong>This page:</strong>
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
    <li><?php echo APP_PATH . 'views/footer.php'; ?></li>
  </ol>

  <br />
  <p>Execution time: <?php echo \load::execution_time(); ?></p>
</div>
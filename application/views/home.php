<?php load('views/header'); ?>

        <h1>Welcome to the StaticPHP Framework test page</h1>
        
        <div class="content">
        
            <div align="right"><a href="http://mstuff.org/frame/docs">See docs!</a></div>
        
            <strong>This page files (edit them to change this page):</strong>
            <ol>
                <li>Controller: <strong><?php echo APP_PATH.'controllers/home.php'; ?></strong></li>
                <li>View: <strong><?php echo __FILE__; ?></strong></li>
                <li>CSS: <strong><?php echo PUBLIC_PATH.'css/style.css'; ?></strong></li>
            </ol>
        
            <div>&nbsp;</div>
        
            <strong>All included files:</strong>
            <ol>
            <?php foreach (get_included_files() as $file): ?>
                <li><?php echo $file; ?></li>
            <?php endforeach; ?>
            </ol>

        </div>

    </body>
</html>
<?php load('views/header'); ?>

        <?php if (!empty($error)): ?>
        
            <pre><?php echo $error; ?></pre>
        
        <?php else: ?>

            <script type="text/javascript" src="<?php echo site_url('js/languages.js'); ?>"></script>
            <script type="text/javascript">
                var tr_keys = <?php echo json_encode(array_slice(mlanguage_admin::$fields, 1)); ?>;
            </script>

            <table border="1" cellspacing="0" cellpadding="4" align="center" class="translation_table">
                <tr>
                    <th>
                        Scope<br />

                        <?php if (!empty($scopes)): ?>
                        <select onchange="location.href = '<?php echo site_url('language/index/'); ?>' + this.value;">
                            <?php foreach ($scopes as $scope): ?>
                                <option value="<?php echo $scope->scope; ?>"<?php if (router::segment(2) == $scope->scope){ echo ' selected="selected"'; }?>><?php echo $scope->scope; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php endif; ?>
                    </th>
                    <th>Ident</th>
                    <?php foreach($tr_keys as $language): ?>
                    <th><?php echo $language; ?></th>
                    <?php endforeach; ?>
                </tr>
            <?php if (!empty($translations)): foreach($translations as $item): ?>
                <tr>
                    <td class="hover" onclick="change(this, '<?php echo $item->id; ?>', 'scope');"><?php echo $item->scope; ?></td>
                    <td class="hover" onclick="change(this, '<?php echo $item->id; ?>', 'ident');"><?php echo $item->ident; ?></td>

                    <?php foreach($tr_keys as $language): ?>
                    <td class="hover" onclick="change(this, '<?php echo $item->id; ?>', '<?php echo $language; ?>');"><?php echo $item->{$language}; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; endif; ?>
            
                <tr id="insert">
                    <td colspan="<?php echo count($tr_keys) + 2; ?>" align="right"><input type="button" value="Insert line" onclick="insert_line()" /></td>
                </tr>
            </table>
            
            <p><img id="loader" src="<?php echo site_url('css/images/loader.gif'); ?>" /></p>
        
        <?php endif; ?>

<?php load('views/footer'); ?>
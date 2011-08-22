<?php  $results = splash_page_paginated_records(); ?>
<?php  $pag = $results['pagination'] ?>
<?php  $records = $results['results'] ?>
<?php  $has_records = (sizeof($records) > 0); ?>


<?php  if (count($_SESSION['flash_messages']) > 0) : ?>
    <?php  foreach ($_SESSION['flash_messages'] as $message):?>
    <div id="zip-code-splash-page-message" class="updated fade">
        <?php echo $message?>
    </div>
    <?php  endforeach;?>
<?php  endif; ?>


<div class="wrap">

    <div id="icon-plugins" class="icon32"></div><h2>Splash Pages</h2>

    <div class="tablenav" style="width:99%;">
        <div class="alignleft actions">
            <a href="<?php echo $form_link?>" class="button-secondary"> Create </a>
        </div>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $results['pagination_count']; ?> items</span>
            <?php  if ($has_records) : ?>
                <?php  $pag->show(); ?>
            <?php  endif; ?>
        </div>
    </div>

    <table class="wp-list-table widefat fixed pages" cellspacing="0" style="width:99%;">
        <thead>
            <tr>
                <td>Redirect To</td>
                <td>URL</td>
                <td>Zip Code</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php  if ($has_records) : ?>
                <?php foreach ($records as $record) : ?>
                    <?php  $edit_link = "$form_link&splash_page_id={$record->id}";?>
                    <tr>
                        <td><a href="<?php echo $edit_link?>"><?php echo $record->title ?></a>
                        
                        </td>
                        <td><?php echo $record->url ?></td>
                        <td><?php echo $record->zipcode ?></td>
                        <td><a href="<?php echo $edit_link?>?>">Edit</a> |
                            <a href="<?php echo $delete_link?>&splash_page_id=<?php echo $record->id?>" class="delete">Delete</a>
                        </td>
                    </tr>
                <?php endforeach;?>
            <?php  else: ?>
                <tr>
                    <td colspan="3" style="text-align:center">No splash pages exist. Would you like to <a href="<?php echo $form_link?>">create one?</a></td>
                </tr>
            <?php  endif; ?>
        </tbody>
    </table>
</div>
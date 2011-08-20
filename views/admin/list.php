<? $results = splash_page_paginated_records(); ?>
<? $pag = $results['pagination'] ?>
<? $records = $results['results'] ?>
<? $has_records = (sizeof($records) > 0); ?>


<? if (count($_SESSION['flash_messages']) > 0) : ?>
    <? foreach ($_SESSION['flash_messages'] as $message):?>
    <div id="zip-code-splash-page-message" class="updated fade">
        <?=$message?>
    </div>
    <? endforeach;?>
<? endif; ?>


<div class="wrap">

    <div id="icon-plugins" class="icon32"></div><h2>Splash Pages</h2>

    <div class="tablenav" style="width:99%;">
        <div class="alignleft actions">
            <a href="<?=$form_link?>" class="button-secondary"> Create </a>
        </div>
        <div class="tablenav-pages">
            <span class="displaying-num"><?=$results['pagination_count']; ?> items</span>
            <? if ($has_records) : ?>
                <? $pag->show(); ?>
            <? endif; ?>
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
            <? if ($has_records) : ?>
                <?foreach ($records as $record) : ?>
                    <? $edit_link = "$form_link&splash_page_id={$record->id}";?>
                    <tr>
                        <td><a href="<?=$edit_link?>"><?=$record->title ?></a>
                        
                        </td>
                        <td><?=$record->url ?></td>
                        <td><?=$record->zipcode ?></td>
                        <td><a href="<?=$edit_link?>?>">Edit</a> |
                            <a href="<?=$delete_link?>&splash_page_id=<?=$record->id?>" class="delete">Delete</a>
                        </td>
                    </tr>
                <?endforeach;?>
            <? else: ?>
                <tr>
                    <td colspan="3" style="text-align:center">No splash pages exist. Would you like to <a href="<?=$form_link?>">create one?</a></td>
                </tr>
            <? endif; ?>
        </tbody>
    </table>
</div>
<h1>List View</h1>
<? $results = zip_code_paginated_records();?>
<? $pag = $results['pagination'] ?>
<? $records = $results['results'] ?>
<? if (sizeof($records) > 0) : ?>
<div class="tablenav">
    <div class="alignleft actions">
        <input type="submit" class="button-secondary" value="Bulk Delete" />
    </div>
    <div class="tablenav-pages">
        <span class="displaying-num"><?php echo $results['pagination_count']; ?> items</span>
        <?php $pag->show(); ?>
    </div>
</div>

<table class="wp-list-table widefat fixed pages" cellspacing="0">
    <thead>
        <tr>
            <td>Redirect To</td>
            <td>URL</td>
            <td>Zip Codes</td>
        </tr>
    </thead>
        
    <tbody>
        <?foreach ($records as $record) : ?>
            <tr>
                <td><a href="<?=site_url()?>/wp-admin/plugins.php?page=zip_code_splash_redirect_admin&action=form"><?=$record->title ?></a></td>
                <td><?=$record->url ?></td>
                <td><?=$record->zipcodes ?></td>
            </tr>
        <?endforeach;?>
    </tbody>
</table>

<? endif; ?>

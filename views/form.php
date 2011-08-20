<div class="zipcode_splash_redirect_form_wrapper">

    <? if (sizeof( $_SESSION['zipcode_flash_messages_front']) > 0):?>
        <ul class="zip_code_redirect_errors">
        <? foreach( $_SESSION['zipcode_flash_messages_front'] as $error): ?>
            <li><?=$error?></li>
        <? endforeach; ?>
        </ul>
    <? endif; ?>

    <form action="" method="POST">
    
        <label for="zipcode_splash_redirect_zipcode">Zipcode</label>
    
        <input class="zipcode_splash_redirect_zipcode" type="text" name="zipcode" value="<?=$_REQUEST['zipcode']?>" />
        <input type="hidden" name="action" value="zip_code_splash_redirect" />
        
        <? if ($use_image && !empty($image_path)) : ?>
            <input class="zip_code_splash_redirect_image" type="image" value="<?=$image_path?>" />
        <? else : ?>
            <input class="zip_code_splash_redirect_submit" type="submit" value="<?=$submit_value?>"/>
        <? endif; ?>
    </form>
</div>
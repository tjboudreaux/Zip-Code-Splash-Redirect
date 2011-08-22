<div class="zipcode_splash_redirect_form_wrapper">

    <?php if (sizeof( $_SESSION['zipcode_flash_messages_front']) > 0):?>
        <ul class="zip_code_redirect_errors">
        <?php foreach( $_SESSION['zipcode_flash_messages_front'] as $error): ?>
            <li><?php echo$error?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="" method="POST">
    
        <label for="zipcode_splash_redirect_zipcode">Zipcode</label>
    
        <input class="zipcode_splash_redirect_zipcode" type="text" name="zipcode" value="<?php echo $_REQUEST['zipcode']?>" />
        <input type="hidden" name="action" value="zip_code_splash_redirect" />
        
        <?php if ($use_image && !empty($image_path)) : ?>
            <input class="zip_code_splash_redirect_image" type="image" value="<?php echo $image_path?>" />
        <?php else : ?>
            <input class="zip_code_splash_redirect_submit" type="submit" value="<?php echo $submit_value?>"/>
        <?php endif; ?>
    </form>
</div>
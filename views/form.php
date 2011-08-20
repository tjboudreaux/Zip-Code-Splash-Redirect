<h1>REDIRECT FORM</h1>
<? if (sizeof( $_SESSION['zipcode_flash_messages_front']) > 0):?>
    <ul class="zip-code-redirect-errors">
    <? foreach( $_SESSION['zipcode_flash_messages_front'] as $error): ?>
        <li><?=$error?></li>
    <? endforeach; ?>
    </ul>
<? endif; ?>
<form action="" method="POST">
    <input type="text" name="zipcode" value="<?=$_REQUEST['zipcode']?>" />
    <input type="hidden" name="action" value="zip_code_splash_redirect" />
    
    <? if ($use_image) : ?>
    
    <? else : ?>
        <input type="submit" value="<?=$submit_value?>"/>
    <? endif; ?>
    
</form>
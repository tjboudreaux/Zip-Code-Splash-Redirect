<? $is_new = !isset($_REQUEST['splash_page_id']) ?>
<? $values =  zip_code_form_submission_values(); ?>

<? if (count($_SESSION['flash_messages']) > 0) : ?>
    <? foreach ($_SESSION['flash_messages'] as $message):?>
    <div id="zip-code-splash-page-message" class="updated fade">
        <?=$message?>
    </div>
    <? endforeach;?>
<? endif; ?>


<h2><?= $is_new ? "Create New Splash Page" : "Edit Splash Page" ?></h2>

<div id="" class="metabox-holder">

    <form action="" method="post" accept-charset="utf-8">
        <? if (!$is_new):?>
            <input type="hidden" name="splash_page_id" value="<?=$_REQUEST['splash_page_id']?>" />
        <? endif; ?>
        <div id="title-div">
            <div id="titlewrap" class="inputwrap">
                <label class="hide-if-no-js"  id="title-prompt-text" for="title">Splash Page Title</label>
                <input type="text" name="title" size="30" tabindex="1" value="<?=$values['title']?>" id="title" autocomplete="off"/>
            </div>
        </div>
        
        <div id="urlwrap" class="inputwrap">
            <label class="hide-if-no-js"  id="url-prompt-text" for="url">URL to Redirect To</label>
            <input type="text" name="url" size="30" tabindex="2" value="<?=$values['url']?>" id="url" autocomplete="off"/>
        </div>
    
        <div id="zipcodewrap" class="inputwrap">
            <label class="hide-if-no-js"  id="zipcode-prompt-text" for="url">Zipcode</label>
            <input type="text" name="zipcode" size="5" tabindex="3" value="<?=$values['zipcode']?>" id="zipcode" autocomplete="off" />
        </div>
        
        <p><a href="<?=$list_link?>" class="button-secondary"> Back </a> &nbsp; &nbsp; <input type="submit" class="button-secondary" value="Submit"></p>
    </form>
</div>

<style>
.inputwrap label { width:100%; display:block; clear:both; margin-bottom:10px;}
.inputwrap input,
.inputwrap textarea {
    border-bottom-left-radius:6px 6px;
      border-bottom-right-radius:6px 6px;
      border-bottom-style:solid;
      border-bottom-width:1px;
      border-left-style:solid;
      border-left-width:1px;
      border-right-style:solid;
      border-right-width:1px;
      border-top-left-radius:6px 6px;
      border-top-right-radius:6px 6px;
      border-top-style:solid;
      border-top-width:1px;
      font-size:1.7em;
      line-height:100%;
      outline-color:initial;
      outline-style:none;
      outline-width:initial;
      padding-bottom:3px;
      padding-left:4px;
      padding-right:4px;
      padding-top:3px;
      width:80%;
      clear:both;
      margin-bottom:10px;
}
</style>
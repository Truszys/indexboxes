<div class="item {$box->getFrontClasses()}">
    <div class="image-container">
        <a href="{$box->getLink()}">
            <img class="img-fluid" src="{$box->getImage()}" alt="{$box->getBoxLangByLangId()->getTitle()}">
        </a>
    </div>
    <div class="content">
        {if $box->getIcon()}
            <span class="icon material-icons notranslate">{$box->getIcon()}</span>
        {/if}
        <span class="title">{$box->getBoxLangByLangId()->getTitle()}</span>
    </div>
</div>
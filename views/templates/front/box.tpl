<div class="item{if $box->classes} {$box->classes}{/if}">
    <div class="image-container">
        <img src="{$box->getImage()}" alt="{$box->title}">
    </div>
    <div class="content">
        {if $box->icon}
            <span class="material-icons">{$box->icon}</span>
        {/if}
        {$box->title}
    </div>
</div>
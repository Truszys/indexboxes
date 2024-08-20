{if $boxes|@count > 0}
    <section id="index-boxes" class="row">
        {foreach from=$boxes item=box}
            {include file="module:indexboxes/views/templates/front/box.tpl" box=$box}
        {/foreach}
    </section>
{/if}
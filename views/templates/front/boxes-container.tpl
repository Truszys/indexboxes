{if $boxes|@count > 0}
    {dump($boxes)}
    <section id="index-boxes" class="row">
        <div class="row">
            {foreach from=$boxes item=box}
                {include file="module:indexboxes/views/templates/front/box.tpl" box=$box}
            {/foreach}
        </div>
    </section>
{/if}
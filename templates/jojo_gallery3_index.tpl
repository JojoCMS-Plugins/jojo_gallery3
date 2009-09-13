{if $pg_body}
{$pg_body}
{/if}
<div id="gallery-index">
{foreach item=g from=$galleries}
{if $g.numimages != 0 }
    <div class="gallery-index-item">
        {if $g.image}<a href="{$g.url}"><img class="float-right" src="images/s100/gallery3/{$g.gallery3id}/{$g.image}" alt="" /></a>{/if}
        <h3><a href="{$g.url}">{$g.name}</a></h3>
        {if $g.bodyplain}<p>{$g.bodyplain|truncate:300}</p>{/if}
        <p class="more"><a href="{$g.url}">View gallery</a>
        {if $OPTIONS.gallery_shownumimages == 'yes'}
            &nbsp;<span class="note">({$g.numimages} image{if $g.numimages >1}s{/if})</span>
        {/if}
        </p>
    <div class="clear"></div>
    </div>
{/if}
{/foreach}
 <div class="clear"></div>
</div>
{if $pg_body}
{$pg_body}
{/if}
<div id="gallery-index">
{foreach item=g from=$galleries}
{if $g.numimages != 0 }
    <div class="gallery-index-item">
        <h3><a href="{$g.url}">{$g.name}</a></h3>
        {if $g.keyimages}<div class="keyimages">{foreach from=$g.keyimages item=i}<a href="{$g.url}"><img class="float-left" src="images/{if $g.thumbsize}{$g.thumbsize}{else}s100{/if}/{$i}" alt="" /></a>{/foreach}</div>{/if}
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
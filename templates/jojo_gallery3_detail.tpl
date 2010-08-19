{if $gallery.body}{$gallery.body}{/if}
{$galleryhtml}
{if $tags}
    <p class="tags"><strong>Tags: </strong>{if $itemcloud}{$itemcloud}{else}{foreach from=$tags item=tag}<a href="{if $multilangstring}{$multilangstring}{/if}tags/{$tag.url}/">{$tag.cleanword}</a>{/foreach}</p>
{/if}
{/if}
{if !$single}
<p class="links">&lt;&lt; <a href="{if $multilangstring}{$multilangstring}{/if}{$gallery.pageurl}" title="{$gallery.pagetitle}">{$gallery.pagetitle}</a>&nbsp;
    {if $prevgallery}&lt; <a href="{$prevgallery.url}" title="Previous">{$prevgallery.title}</a>{/if}
    {if $nextgallery} | <a href="{$nextgallery.url}" title="Next">{$nextgallery.title}</a> &gt;{/if}
</p>
{/if}
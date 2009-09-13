{if $gallery.body}{$gallery.body}{/if}

{$galleryhtml}

{if $tags}
<div align="center" style="margin-top: 15px;">
    <strong>Tags: </strong>
{foreach from=$tags item=tag}
    <a href="tags/{$tag.url}/">{$tag.cleanword}</a>
{/foreach}
</div>
{/if}
{if !$single}
<p class="links"><br /> <!-- *{$pg_lang_prefix}* -->
    &lt;&lt; <a href="{if $MULTILANGUAGE}{if $pg_lang_prefix == 'null'}{elseif $pg_lang_prefix != ''}{$pg_lang_prefix}{else}{$pg_language}/{/if}{/if}{if $pg_url}{$pg_url}/{else}{$pageid}/{$pg_title|strtolower}{/if}" title="{$pg_title} Index">{$pg_title}</a>&nbsp;
    {if $prevgallery}&lt; <a href="{$prevgallery.url}" title="Previous">{$prevgallery.title}</a>{/if}
    {if $nextgallery} | <a href="{$nextgallery.url}" title="Next">{$nextgallery.title}</a> &gt;{/if}
</p>
{/if}
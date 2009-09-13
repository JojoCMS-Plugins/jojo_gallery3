{if $images}{foreach from=$images item=img}
<img style="border: 1px solid #ccc; padding: 2px; margin: 2px;" src="{$SITEURL}/images/s100/gallery3/{$galleryid}/{$img.filename}" alt="{$img.caption}" title="{$img.caption}" />
{/foreach}{/if}
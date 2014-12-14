{if $images}{foreach from=$images item=img}
<img class="img-thumbnail" src="{$SITEURL}/images/s100/gallery3/{$galleryid}/{$img.filename}" alt="" title="{if $img.caption}{$img.caption}{/if}" />
{/foreach}{/if}
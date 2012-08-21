<div class="jgallery">
<div id="jgallery-{$galleryid}">
{if $gallery.numimages >1}
    <div class="jg-thumbnail-wrap">
        <ul  class="jg-thumbnails">
        {foreach from=$images item=i}
            <li><a href="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$i.filename}" onclick= "return false;"><img id="{$i.filename}" title="{$i.caption}" src="images/{if $gallery.thumbsize}{$gallery.thumbsize}{else}s50{/if}/gallery3/{$galleryid}/{$i.filename}" alt="{$i.caption}" /></a></li>
        {/foreach}
        </ul>
    </div>
{/if}
{if $images}
    <div class="jg-large">
        <img title="{if $images[0].caption}{$images[0].caption}{/if}" src="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$images[0].filename}" alt="{if $images[0].caption}{$images[0].caption}{/if}" />
    </div>
    <div class="jg-imagepreload" style="display:none">
        {foreach from=$images key=k item=i}
        <img title="{if $i.caption}{$i.caption}{/if}" src="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$i.filename}" alt="{if $i.caption}{$i.caption}{/if}"{if $k!=0} style="display:none"{/if} />
        {/foreach}
    </div>
    <div class="jg-caption"><p class="caption">{if $images[0].caption}{$images[0].caption}{/if}</p></div>
{/if}
    <div class="clear"></div>
</div>
</div>
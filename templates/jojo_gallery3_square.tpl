<div class="gallery-square">
{foreach from=$images item=img}
{if $gallery.showcaptions}
    <div class="galleryimagebox">
        <p class="caption">
{/if}
        <a href="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$img.filename}" rel="lightbox" title="{if $img.caption}{$img.caption}{/if}{if $img.credit} - {$img.credit}{/if} {if $img.date and $gallery.showdate} - {$img.date}{/if}">
        <img src="images/{if $gallery.thumbsize}{$gallery.thumbsize}{else}s100{/if}/gallery3/{$galleryid}/{$img.filename}" alt="{if $img.caption}{$img.caption}{/if}" /></a>
{if $gallery.showcaptions}
        <br />
        {if $img.caption}{$img.caption}{/if}
        </p>
    </div>
{/if}
{/foreach}
</div>
<div class="clear"></div>
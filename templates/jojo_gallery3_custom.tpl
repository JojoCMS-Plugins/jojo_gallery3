
{foreach from=$images item=img}
{if $gallery.showcaptions}
	<div class="galleryimagebox">
        <p class="caption">
{/if}
        <a href="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$img.filename}" rel="lightbox" title="{$img.caption}{if $img.credit} - {$img.credit}{/if} {if $img.date and $gallery.showdate} - {$img.date}{/if}">
		<img src="images/{if $gallery.thumbsize}{$gallery.thumbsize}{else}s100{/if}/gallery3/{$galleryid}/{$img.filename}" alt="{$img.caption}" /></a>
{if $gallery.showcaptions}
		<br />
		{$img.caption}
		</p>
    </div>
{/if}
{/foreach}

<div class="gallery-square">
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
</div>
<div class="clear"></div>
<script type="text/javascript">
/*<![CDATA[*/
$(function() {ldelim}
	$('.gallery-square a[rel*=lightbox]').lightBox({ldelim}
	overlayBgColor: '#000',
	overlayOpacity: 0.8,
	imageBlank: '{$SITEURL}/external/jquery-lightbox/images/lightbox-blank.gif',
	imageLoading: '{$SITEURL}/external/jquery-lightbox/images/lightbox-ico-loading.gif',
	imageBtnClose: '{$SITEURL}/external/jquery-lightbox/images/lightbox-btn-close.gif',
	imageBtnPrev: '{$SITEURL}/external/jquery-lightbox/images/lightbox-btn-prev.gif',
	imageBtnNext: '{$SITEURL}/external/jquery-lightbox/images/lightbox-btn-next.gif',
	containerResizeSpeed: 200{rdelim});
{rdelim});
 /*]]>*/
 </script>
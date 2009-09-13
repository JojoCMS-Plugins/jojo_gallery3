<div class="gallery-square">
{foreach from=$images item=img}
{if $OPTIONS.gallery_showcaptions == 'yes'}
	<div class="galleryimagebox">
        <p class="caption">
{/if}
        <a href="images/{$gallery.previewsize|default:600}/gallery3/{$galleryid}/{$img.filename}" rel="lightbox" title="{$img.caption}{if $img.credit} - {$img.credit}{/if} {if $img.date and $OPTIONS.gallery_showdate != 'no'} - {$img.date}{/if}">
		<img src="images/s{$gallery.thumbsize|default:100}/gallery3/{$galleryid}/{$img.filename}" alt="{$img.caption}" /></a>
{if $OPTIONS.gallery_showcaptions == 'yes'}
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
<div class="jgallery">
<div id="jgallery-{$galleryid}">
    <div class="jg-thumbnail-wrap">
        <ul  class="jg-thumbnails">
        {foreach from=$images item=i}
            <li><a href="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$i.filename}" onclick= "return false;"><img id="{$i.filename}" title="{$i.caption}" src="images/{if $gallery.thumbsize}{$gallery.thumbsize}{else}s50{/if}/gallery3/{$galleryid}/{$i.filename}" alt="{$i.caption}" /></a></li>
        {/foreach}
        </ul>
    </div>
    <div class="jg-large">
        <img title="{$images[0].caption}" src="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$images[0].filename}" alt="{$images[0].caption}" />
    </div>
    <div class="jg-caption"><p class="caption">{$images[0].caption}</p></div>
    <div class="clear"></div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function(){ldelim}
    $("#jgallery-{$galleryid} .jg-thumbnails li a").click(function(){ldelim}
        $("#jgallery-{$galleryid} .jg-large img").hide().attr({ldelim}"src": $(this).attr("href"), "title": $("> img", this).attr("title"), "alt": $("> img", this).attr("title"){rdelim});
		$("#jgallery-{$galleryid} .jg-caption p").html($("> img", this).attr("title"));
		return false;
	{rdelim}, '');
	$("#jgallery-{$galleryid} .jg-large img").load(function(){ldelim}$("#jgallery-{$galleryid} .jg-large img:hidden").fadeIn("slow"){rdelim});
{rdelim});
</script>
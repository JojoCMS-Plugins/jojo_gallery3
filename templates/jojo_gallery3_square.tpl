<div class="gallery-square">
{foreach from=$images item=img}
    <div class="galleryimagebox">
        <a class="thumbnail" href="images/{if $gallery.previewsize}{$gallery.previewsize}{else}w450{/if}/gallery3/{$galleryid}/{$img.filename}" rel="lightbox" title="{if $img.caption}{$img.caption}{/if}{if $img.credit} - {$img.credit}{/if} {if $img.date and $gallery.showdate} - {$img.date}{/if}">
            <img src="images/{if $gallery.thumbsize}{$gallery.thumbsize}{else}s100{/if}/gallery3/{$galleryid}/{$img.filename}" alt="{if $img.caption}{$img.caption}{/if}" />
        </a>
{if $gallery.showcaptions}
        <p class="caption">{if $img.caption}{$img.caption}{/if}</p>
{/if}
    </div>
{/foreach}
</div>
<div class="clear"></div>
{if $filter}<script type="text/javascript">
/*<![CDATA[*/
{include file="jojo_gallery3_js.tpl"}
/*]]>*/
</script>
{/if}

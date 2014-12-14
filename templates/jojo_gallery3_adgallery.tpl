<div id="jgallery-{$galleryid}">
<div id="gallery" class="ad-gallery">
  <div class="ad-image-wrapper">
  </div>
  <div class="ad-controls">
  </div>
{if $gallery.numimages >1}
  <div class="ad-nav">
    <div class="ad-thumbs">
      <ul class="ad-thumb-list">
      {foreach from=$images item=i name="img"}
          <li>
            <a href="images/w580/gallery3/{$galleryid}/{$i.filename}">
              <img src="images/h60/gallery3/{$galleryid}/{$i.filename}" title="{$i.caption}" alt="{$i.caption}" class="image{$.foreach.img.iteration}">
            </a>
          </li>
      {/foreach}
      </ul>
    </div>
  </div>
{/if}
</div>
</div>
<div class="col-md-12">
    <span title="Actual size {$imagesize[0]}x{$imagesize[1]}px {$filesize}">
        <img src="{$SITEURL}/images/{$this->viewthumbsize}gallery3/{$galleryid}/{$this->value}" border="0" align="absmiddle" alt="{$this->value}"/>
    </span><br />
    <input type="hidden" name="fm_{$this->fd_field}" value="{$this->value}" />
    {if $this->value}<div style="color: #999">{$this->value}{if $filesize} ({$filesize}){/if} <a href="{$SITEURL}/downloads/gallery3/{$galleryid}/{value}" target="_BLANK">open</a></div>{/if}
    <input type="hidden" name="MAX_FILE_SIZE" value="{$this->fd_maxvalue}" />
    <input class="{$class}" type="file" name="fm_FILE_{$this->fd_field}" id="fm_FILE_{$this->fd_field}"  size="{$this->fd_size}" value=""  onchange="fullsave = true;" title="{fd_help}" />
</div>
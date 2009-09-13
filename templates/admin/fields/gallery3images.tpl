<div id="upload-form">
{*<form enctype="multipart/form-data" action="{$SITEURL}/actions/gallery3-upload-image.php?id={$currentid}" target="frajax-iframe" method="post">*}
  <h3>Upload images</h3>
  <div id="example1">
    <label for="uploadimage">Upload image:</label>
    <input name="MAX_FILE_SIZE" value="2000000" type="hidden" />
    <input name="uploadimage" id="uploadimage" type="file" />
  </div>
  <input type="submit" name="gallery3submit" value="Upload" />
</form>
</div>

<div id="files">
{if $thumbs}{$thumbs}{/if}
</div>
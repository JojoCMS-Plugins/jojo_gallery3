<div id="upload-form">
{*<form enctype="multipart/form-data" action="{$SITEURL}/actions/gallery3-upload-image.php?id={$currentid}" target="frajax-iframe" method="post">*}
  <h3>Upload images</h3>
  {if $currentid}
  <div id="example1">
    <label for="uploadimage">Upload image:</label>
    <input name="MAX_FILE_SIZE" value="2000000" type="hidden" />
    <input name="uploadimage" id="uploadimage" type="file" />
  </div>
  {else}You must save this new gallery before you can upload images{/if}
{*</form>*}
</div>

<div id="files">
{if $thumbs}{$thumbs}{/if}
</div>
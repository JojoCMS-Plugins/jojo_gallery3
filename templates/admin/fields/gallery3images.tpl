<div id="upload-form">
{*<form enctype="multipart/form-data" action="{$SITEURL}/actions/gallery3-upload-image.php?id={$currentid}" target="frajax-iframe" method="post">*}
  <h3>Upload images</h3>
  {if $currentid}
  <div id="example1">
    <label for="uploadimage">Upload image:</label>
    <input name="MAX_FILE_SIZE" value="2000000" type="hidden" />
    <input name="uploadimage[]" id="uploadimage" type="file" multiple="multiple" />
  </div>
  <p><strong>Files to upload</strong></p>
  <ul id="fileList">
    <li>No Files Selected</li>
  </ul>
  {else}You must save this new gallery before you can upload images{/if}
{*</form>*}
</div>
<script type="text/javascript">{literal}
$('#uploadimage').change(function(){
    //get the input and UL list
    var $list = $('#fileList');
    
    //empty list for now...
    $list.find('li').remove();
    
    //for every file...
    $.map($('#uploadimage').get(0).files, function(file, x) {
        var li = document.createElement('li');
        li.innerHTML = 'File ' + (x + 1) + ':  ' + file.name;
        $list.append(li);
    });
});
{/literal}</script>
<div id="files">
{if $thumbs}{$thumbs}{/if}
</div>

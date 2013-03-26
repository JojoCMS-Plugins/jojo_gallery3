{if $gallery.layout=='square'}{literal}
    $(function() {
        $('.gallery-square a').colorbox({
        rel: 'lightbox',
        width:"95%",
        height:"95%",
        current: "{current}/{total}"
        });{/literal}
         {if $OPTIONS.jquery_touch == 'yes'}
            $('#cboxContent').swipeleft(function() {ldelim}
                $.colorbox.next();
           {rdelim});
            $('#cboxContent').swiperight(function() {ldelim}
                $.colorbox.prev();
           {rdelim});
        {/if}
   {rdelim});

{elseif $gallery.layout=='magazine'}
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
    {literal}
    function magSwap(i,w,h) {
      //var reg = /^image\.php\?w=(.*?)&(?:amp;)?h=(.*?)&(?:amp;)?file=(.*?)$/mg;
      //var result = i.replace(reg, "image.php?w="+w+"&h="+h+"&file=$3");
      var reg = /^images\/(.*?)x(.*?)\/(.*?)$/mg;
      var result = i.replace(reg, "images/"+w+"x"+h+"/$3");
      return result;
    }

    $('div.mag a:not(.main)').click(function(){
      var mainimg = $(this).parents().find("a.main img");
      var maina = $(this).parents().find("a.main");
      var w = mainimg.attr('width');
      var h = mainimg.attr('height');
      maina.css('width',w).css('height',h);
      var src = magSwap($(this).find('img').attr('src'),mainimg.attr('width'),mainimg.attr('height'));
      var alt = $(this).find('img').attr('alt');
      if (alt==undefined) {alt = '';}
      var title = $(this).find('img').attr('title');
      if (title==undefined) {title = '';}
      var href = $(this).attr('href');
      if (href==undefined) {href = '';}
      //alert(maina.css('width'));
      mainimg.attr('width',20).attr('height',20).attr('src','loading.gif');
      preload = new Image();
      preload.onLoad = mainimg.attr('src',src).attr('alt',alt).attr('title',title).attr('width',w).attr('height',h);
      preload.src = src;
      //mainimg.attr('src',src).attr('alt',alt).attr('title',title);
      maina.attr('href',href);
      $(this).parents().find(".selected").removeClass('selected');
      $(this).addClass('selected');

      return false;
    });
    {/literal}
{elseif $gallery.layout=='jgallery'}
    $(document).ready(function(){ldelim}
        $("#jgallery-{$galleryid} .jg-thumbnails li:first-child a").hide();
        $("#jgallery-{$galleryid} .jg-thumbnails li a").bind("click", function(){ldelim}
            $("#jgallery-{$galleryid} .jg-large img").hide().attr({ldelim}"src": $(this).attr("href"), "title": $("> img", this).attr("title"), "alt": $("> img", this).attr("title"){rdelim});
            $("#jgallery-{$galleryid} .jg-thumbnails li a").show();
            $(this).addClass('current');
    		$("#jgallery-{$galleryid} .jg-caption p").html($("> img", this).attr("title"));
    		return false;
    	{rdelim});
    	$("#jgallery-{$galleryid} .jg-large img").load(function(){ldelim}$("#jgallery-{$galleryid} .jg-large img:hidden").fadeIn("slow"){rdelim});
    {rdelim});
{elseif $gallery.layout=='adgallery'}
    {literal}
    $.getScript("external/jquery.ad-gallery.min.js", function(){
        var galleries = $('.ad-gallery').adGallery();
    });
    {/literal}
{/if}

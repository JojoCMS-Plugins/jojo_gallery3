<div class="gallery3">
{$mag}
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
{literal}
<script type="text/javascript">
/* <![CDATA[ */
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
/* ]]> */
</script>
{/literal}
</div>
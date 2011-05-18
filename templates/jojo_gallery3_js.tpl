{if $gallery.layout=='square'}
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
 {/if}
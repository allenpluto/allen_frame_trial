/**
 * Created by User on 26/05/2017.
 */

$('body').on('click',function(event){
    var click_element = $(event.target);
    if (click_element.hasClass('listing_detail_view_gallery_container'))
    {
        click_element.closest('.listing_detail_view_gallery_wrapper').addClass('listing_detail_view_gallery_wrapper_active');
        click_element.closest('.listing_detail_view_gallery_wrapper').find('.listing_detail_view_gallery_container_active').removeClass('listing_detail_view_gallery_container_active');
        click_element.addClass('listing_detail_view_gallery_container_active');
    }
    else
    {
        $('.listing_detail_view_gallery_wrapper').removeClass('listing_detail_view_gallery_wrapper_active');
        $('.listing_detail_view_gallery_container_active').removeClass('listing_detail_view_gallery_container_active');
    }
});
$('.listing_detail_view_gallery_container').click(function(){
    $(this).closest('.listing_detail_view_gallery_wrapper')
});
// Click on body_wrapper, if not in any organization_block, de-select active block; if in active block, de-select active block; if in inactive block, de-select active block (if any), then set clicked block active
var form = $('.form_ajax_form_container');

form.on('retrieve_form_data',function(){
    form.find('.form_gallery_image_container_delete').removeClass('form_gallery_image_container_delete');
    form.find('.form_gallery_image_container_new').remove();
});

form.on('set_update_data',function(event, update_data){
    if (update_data['image_row'])
    {
        var form_data = {};
        form_data = form.data('form_data');
        var form_gallery_image_wrapper = form.find('.form_gallery_image_wrapper');
        form_gallery_image_wrapper.html('');
        update_data['image_row'].forEach(function(image_row, image_row_index){
            var form_gallery_image_container = $('<div />',{
                'class':'form_row_container form_gallery_image_container'
            }).html('<img class="form_gallery_image_file" src="'+image_row['file_uri']+'"><div class="form_gallery_image_delete_trigger"></div><label for="form_members_gallery_image_name_'+image_row['id']+'">Image Name</label><input id="form_members_gallery_image_name_'+image_row['id']+'" class="form_members_gallery_image_name" name="image_name_'+image_row['id']+'" type="text" placeholder="Image Name" value="'+image_row['name']+'">').appendTo(form_gallery_image_wrapper);
            form_data['image_name_'+image_row['id']] = image_row['name'];
        });
        form.data('form_data',form_data);
    }
});

form.on('get_update_data', function(event, update_data){
    var new_image = [];
    form.find('.form_gallery_image_container_new').each(function(){
        new_image.push({'name':$(this).find('.form_members_gallery_image_name').val(),'source_file':$(this).find('.form_gallery_image_file').attr('src')})
    });
    if (new_image.length > 0)
    {
        update_data['new_image'] = new_image;
    }
});

$('.footer_action_button_reset').click(function(event){
    event.preventDefault();
    $(this).closest('.ajax_form_container').trigger('retrieve_form_data');
});

$('.footer_action_button_save').click(function(event){
    event.preventDefault();
    $(this).closest('.ajax_form_container').trigger('post_form_data');
});

$(document).ready(function(){
    $('.form_gallery_uploader_container').form_gallery_uploader();
});
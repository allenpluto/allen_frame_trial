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

    var form = $('.ajax_form_container');

    form.on('set_image_row',function(event,image_row_array){
console.log('set image row');
        if (typeof image_row_array === 'undefined')
        {
            var form_data = form.data('form_data');
            image_row_array = form_data['image_row'];
        }
        var form_gallery_image_wrapper = form.find('.form_gallery_image_wrapper');
        form_gallery_image_wrapper.find('.form_gallery_image_container:not(.form_gallery_uploader_trigger_container)').remove();
        image_row_array.forEach(function(image_row, image_row_index){
            var form_gallery_image_container = $('<div />',{
                'class':'form_row_container form_gallery_image_container'
            }).data(image_row).data('order',image_row_index).html('<img class="form_gallery_image_file" src="'+image_row['file_uri']+'"><div class="form_gallery_image_background" style="background-image: url('+image_row['file_uri']+')"></div><div class="form_gallery_image_delete_trigger"></div><input id="form_members_gallery_image_name_'+image_row['id']+'" class="form_members_gallery_image_name" name="image_name_'+image_row['id']+'" type="text" placeholder="Image Name" value="'+image_row['name']+'">').appendTo(form_gallery_image_wrapper);
        });
    });

    form.on('store_form_data',function(){
        var form_data = {};
        form_data = form.data('form_data');
        form_data['image_row'] = [];
        form.find('.form_gallery_image_container:not(.form_gallery_uploader_trigger_container)').each(function(index){
            form_data['image_row'].push({'id':$(this).data('id'),'name':$(this).data('name'),'file_uri':$(this).data('file_uri')});
        });
        form.data('form_data',form_data);
    });

    form.on('retrieve_form_data',function(){
console.log('retrieve form data');
        form.find('.form_gallery_image_container_delete').removeClass('form_gallery_image_container_delete');
        form.find('.form_gallery_image_container_new').remove();
        var form_data = {};
        form_data = form.data('form_data');
        if (form_data['image_row'])
        {
            form.trigger('set_image_row',[form_data['image_row']]);
        }
    });

    form.on('set_update_data',function(event, update_data){
        var form_data = {};
        form_data = form.data('form_data');
        var form_gallery_image_wrapper = form.find('.form_gallery_image_wrapper');
        form_gallery_image_wrapper.html('');
        if (update_data['image_row'])
        {
            form.trigger('set_image_row',[update_data['image_row']]);
            form_data['image_row'] = update_data['image_row'];
        }
        form.data('form_data',form_data);
    });

    form.on('get_update_data', function(event, update_data){
        var form_data = {};
        form_data = form.data('form_data');
        var image_row = [];
        var image_row_updated = false;
        if (form.find('.form_gallery_image_container_new,.form_gallery_image_container_delete').length > 0)
        {
            image_row_updated = true;
        }
        if (!image_row_updated)
        {
            form.find('.form_gallery_image_container:not(.form_gallery_uploader_trigger_container)').each(function(index){
                if (image_row_updated) return false;
console.log('form_gallery_image_container '+index);
                if ($(this).data('order') != index || $(this).data('name') != $(this).find('.form_members_gallery_image_name').val())
                {
                    image_row_updated = true;
                }
            });
        }
        if (image_row_updated)
        {
            update_data['image_row'] = [];
            form.find('.form_gallery_image_container:not(.form_gallery_uploader_trigger_container,.form_gallery_image_container_delete)').each(function(index){
                if ($(this).hasClass('form_gallery_image_container_new'))
                {
                    update_data['image_row'].push({'name':$(this).find('.form_members_gallery_image_name').val(),'source_file':$(this).find('.form_gallery_image_file').attr('src'),'order':index})
                    return true;
                }
                update_data['image_row'].push({'id':$(this).data('id'),'name':$(this).find('.form_members_gallery_image_name').val(),'order':index})
            });
        }
    });
});
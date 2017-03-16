/**
 * Created by User on 15/03/2017.
 */
$('#body_wrapper').click(function(event){
    //console.log(event.target);
    if ($(event.target).parents('.members_organization_block_container').hasClass('members_organization_block_container_active'))
    {
        $(event.target).parents('.members_organization_block_container').removeClass('members_organization_block_container_active')
    }
    else
    {
        $('.members_organization_block_container_active').removeClass('members_organization_block_container_active');
        $(event.target).parents('.members_organization_block_container').addClass('members_organization_block_container_active');
    }
});
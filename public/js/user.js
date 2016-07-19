
$(function(){

    $('body').on('click', '#deactivate_users', function(){
        $.post( "/user/deactivate", {ids:getCheckedUsers()}, function( data ) {
            data.ids.forEach(function(id){
                findRowById(id).find('.user_active').text(0);
            });
        });
    });

    $('body').on('click', '#activate_users', function(){
        $.post( "/user/activate", {ids:getCheckedUsers()}, function( data ) {
            data.ids.forEach(function(id){
                findRowById(id).find('.user_active').text(1);
            });
        });
    });

});

function findRowById(id){
    return $('#users-table').find('input[data-user-id='+id+']').closest('tr');
}

function getCheckedUsers(){
    var ids = [];
    $('#users-table').find('input[type="checkbox"]:checked').each(function( i, v){
        ids.push(v.dataset.userId);
    });
    return ids;
}
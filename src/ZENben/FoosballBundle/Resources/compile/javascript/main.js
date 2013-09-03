;jQuery(function($){
    $('.btn-join').click(function(){
        var value = $('.comment').val();
        var url = $(this).attr('data-url');
        $('.confirm-participation').loading();
        $.getJSON(url,{comment:value})
         .done(function(data){
             if (data.success) {
                 $('.want-to-join').fadeOut(function(){
                     $('.joined').fadeIn();
                     $('.dummy em').html(data.comment);
                     $('.dummy').slideDown(function(){
                         $('.dummy').animate({opacity:1});
                     });
                 });
             } else {
                 $('.comment').addClass('invalid');
                 $('.want-to-join').fadeIn();
             }
         })
         .fail(function(){
             $('.want-to-join').fadeIn();
         })
         .always(function(){
             $('.confirm-participation').loading('hide');
         });
    });
    
    $(function() {
        $('#test').bracket({
            init: bracketData,
            skipConsolationRound: true,
        });
    });
    
});


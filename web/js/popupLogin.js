/**
 * Created by ramy on 08/04/15.
 */
$(document).ready(function(){
    $('body').on('click','#login_button',function(e){
        e.preventDefault();
        $.magnificPopup.open({
            items: {
                src: $('#login_button').attr('href')
            },
            type: 'ajax',
            overflowY: 'scroll',
            showCloseBtn: false,
            closeOnBgClick: false
        });
    });

    $('body').on('click','#close-popup-button',function(e){
        e.preventDefault();
        var $magnificPopup = $.magnificPopup.instance;
        $magnificPopup.close();
    });

    $('body').on('click','#ajax_login',function(e){
        e.preventDefault();
        var $form = $('#login_form');
        console.log('form data : ',$form.serialize());
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),

            data: $form.serialize(),
            success: function(data){
                if(data['success'] == false){
                    $('#login_error_msg').css('display','block');
                }else if(data['success'] == true){
                    $('#login_error_msg').css('display','none');
                    var $magnificPopup = $.magnificPopup.instance;
                    $magnificPopup.close();
                    location.reload();
                }
            }
        });
    });
})
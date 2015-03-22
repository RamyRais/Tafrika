/**
 * Created by ramy on 07/03/15.
 */

$(document).ready(function(){
    $('body').on('click', '#signal_nsfw',function(e){
        e.preventDefault();
        var $this = $(this);
        var $path = $this.attr('href');
        var $post_id = $this.attr('content');
        if($post_id===''){
            alert("une erreur est servenu try again later");
        }else{
            $.ajax({
                url: $path,
                type: "POST",
                data: { "post_id" : $post_id },
                success: function(data) {
                },
                error: function(jq,status,message){
                    //alert(message);
                }
            });
        }
    });

    $('body').on('click','#signal_porn',function(e){
        e.preventDefault();
        var $this = $(this);
        var $path = $this.attr('href');
        var $post_id = $this.attr('content');
        if($post_id===''){
            alert("une erreur est servenu try again later");
        }else{
            $.ajax({
                url: $path,
                type: "POST",
                data: { "post_id" : $post_id },
                success: function(data) {
                },
                error: function(jq,status,message){
                    //alert(message);
                }
            });
        }
    });
});


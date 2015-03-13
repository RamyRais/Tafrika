/**
 * Created by ramy on 13/03/15.
 */
$(document).ready(function(){
    $('#load_comment_link').on('click',function(e){
        e.preventDefault();
        var $this = $(this);
        var $url = $this.attr('href')
        var $page = ($('.list-group-item').length / $this.attr('commentPerLoad')) +1;
        var $post_id = $this.attr('content');
        $.ajax({
            url: $url,
            type: 'POST',
            data: { 'post_id' : $post_id, 'page': $page },
            success: function(data) {
                if(data != ""){
                    $('#test').append(data);
                }else{
                    $('#load_comment_link').remove();
                }
            }
        });

    });
});
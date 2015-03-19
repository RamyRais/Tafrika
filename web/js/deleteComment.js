/**
 * Created by ramy on 13/03/15.
 */
$(document).ready(function(){
    $(document).on('click', '#delete_comment',function(e){
        e.preventDefault();
        var $this = $(this);
        var $comment_id = $this.attr('content');
        var $url = $this.attr('href');
        if($comment_id===''){
            alert("une erreur est servenu try again later");
        }else{
            $.ajax({
                url: $url,
                type: 'POST',
                data: { 'comment_id' : $comment_id },
                success: function(data) {
                    var $comment = document.getElementById('comment_'.concat($comment_id));
                    console.log('comment_'.concat($comment_id));
                    $comment.parentNode.removeChild($comment);
                    //$('#comment_list').load(document.URL +  ' #comment_list');
                    //$('#jquery_div').load(document.URL +  ' #jquery_div');
                },
                error: function(jq,status,message){
                    //alert(message);
                }
            });
        }

    });
});
/**
 * Created by ramy on 25/03/15.
 */
$(document).ready(function(){
    $('body').on('click','#button_up',function(e){
        e.preventDefault();
        var $this = $(this);
        var $postId = $this.attr('postId');
        var $path = $this.attr('path');
        $.ajax({
            url: $path,
            type: 'POST',
            data: { 'post_id' : $postId },
            success: function(data) {
                console.log(data);
                var $child = $("i",$this);
                var $likes= $('#count'.concat($postId));
                $likes.text(data.concat(' likes'));
                if( $child.hasClass('fa-thumbs-o-up')){
                    $child.removeClass('fa-thumbs-o-up').addClass('fa-thumbs-up');
                }else if($child.hasClass('fa-thumbs-up')) {
                    $child.removeClass('fa-thumbs-up').addClass('fa-thumbs-o-up');
                }
                $("i",'.change_state_down_'.concat($postId)).removeClass('fa-thumbs-down')
                    .addClass('fa-thumbs-o-down')
            }
        })
    });
    $('body').on('click','#button_down',function(e){
        e.preventDefault();
        var $this = $(this);
        var $postId = $this.attr('postId');
        var $path = $this.attr('path');
        $.ajax({
            url: $path,
            type: 'POST',
            data: { 'post_id' : $postId },
            success: function(data) {
                var $child = $("i",$this);
                var $likes= $('#count'.concat($postId));
                $likes.text(data.concat(' likes'));
                if( $child.hasClass('fa-thumbs-o-down')){
                    $child.removeClass('fa-thumbs-o-down').addClass('fa-thumbs-down');
                }else if($child.hasClass('fa-thumbs-down')) {
                    $child.removeClass('fa-thumbs-down').addClass('fa-thumbs-o-down');
                }
                $("i",'.change_state_up_'.concat($postId)).removeClass('fa-thumbs-up')
                    .addClass('fa-thumbs-o-up')
            }
        })
    });
})
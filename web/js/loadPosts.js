/**
 * Created by ramy on 18/03/15.
 */
$(document).ready(function(){
    $('#load_post_link').on('click',function(e){
        e.preventDefault();
        var $this = $(this);
        var $url = $this.attr('href')
        var $type = $this.attr('type');
        var $page = Math.floor(($('.panel').length / $this.attr('postsPerLoad')) +1);
        console.log($page);
        $.ajax({
            url: $url,
            type: 'POST',
            data: { 'page': $page, 'type': $type },
            success: function(data) {
                //alert(data);
                if(data != ""){
                    $('#posts').append(data);
                }else{
                    $('#load_post_link').remove();
                }
            }
        });

    });
});

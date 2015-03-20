/**
 * Created by ramy on 18/03/15.
 */
$(document).ready(function(){
    $('#load_post_link').on('click',function(e){
        e.preventDefault();
        var $this = $(this);
        var $nbElements = $('.panel').length ;
        if($nbElements>=1) {
            var $url = $this.attr('href')
            var $type = $this.attr('type');
            var $userId = $this.attr('userid');
            var $postsPerLoad = $this.attr('postsPerLoad');
            if ($nbElements < $postsPerLoad) {
                var $page = 1;
            } else if ($nbElements % $postsPerLoad == 0) {
                var $page = Math.floor($nbElements / $postsPerLoad);
            } else {
                var $page = Math.floor($nbElements / $postsPerLoad) + 1;
            }
            $page += 1;
            $.ajax({
                url: $url,
                type: 'POST',
                data: {'page': $page, 'type': $type, 'userId' : $userId},
                success: function (data) {
                    //alert(data);
                    if (data != "") {
                        $('#posts').append(data);
                    } else {
                        $('#load_post_link').remove();
                    }
                }
            });
        }else{
            $('#load_post_link').remove();
        }
    });
});

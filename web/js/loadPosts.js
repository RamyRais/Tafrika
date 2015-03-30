/**
 * Created by ramy on 18/03/15.
 */
$(document).ready(function(){
    $(window).on('scroll',function() {
        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            var $link = $('#load_post_link');
            var $totalPage = $link.attr('totalPage');
            var $nbElements = $('.panel').length ;
            if($nbElements>=1) {
                var $url = $link.attr('href')
                var $type = $link.attr('type');
                var $userId = $link.attr('userid');
                var $postsPerLoad = $link.attr('postsPerLoad');
                if ($nbElements < $postsPerLoad) {
                    var $page = 1;
                } else if ($nbElements % $postsPerLoad == 0) {
                    var $page = Math.floor($nbElements / $postsPerLoad);
                } else {
                    var $page = Math.floor($nbElements / $postsPerLoad) + 1;
                }
                $page += 1;
                if($page<=$totalPage) {
                    $.ajax({
                        url: $url,
                        type: 'POST',
                        data: {'page': $page, 'type': $type, 'userId': $userId},
                        success: function (data) {
                            if (data != "") {
                                $('#posts').append(data);
                            }
                        }
                    });
                }
            }else{
                $('#load_post_link').remove();
            }
        }
    });
});

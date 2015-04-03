/**
 * Created by ramy on 19/01/15.
 */

window.fbAsyncInit = function() {
    FB.init({appId: '394977360654494', status: true, cookie: true,
        xfbml: true});
};
(function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
    '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
}());

$(document).ready(function(){
    $('#fb_share_button').click(function(e){
        e.preventDefault();
        var $this = $(this);
        var $name = $this.attr('name');
        var $link = $this.attr('link');
        var $picture = $this.attr('picture');
        var $caption = $this.attr('caption');
        var $description = $this.attr('description');
        FB.ui(
            {
                method: 'feed',
                name: $name,
                link: $link,
                picture: $picture,
                caption: $caption,
                description: $description,
                message: ''
            });
    });
});
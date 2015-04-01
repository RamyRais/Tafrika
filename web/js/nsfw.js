/**
 * Created by ramy on 31/03/15.
 */
$(function() {
    $('#toggle-event').change(function() {
        var $path = $(this).attr('path');
        window.location.href = $path;
    });
});

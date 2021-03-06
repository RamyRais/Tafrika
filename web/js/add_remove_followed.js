/**
 * Created by ramy on 15/03/15.
 */
$(document).ready(function() {
    var $userId = $("#friendButton").attr('userId');
    var $isFollowed = $("#friendButton").attr('isFollowed') == "false"? false : true;
    var $addPath = $("#friendButton").attr('addPath');
    var $deletePath = $("#friendButton").attr('deletePath');
    $("#friendButton").click(function () {
        if (!$isFollowed) { // user are not friend
            $.ajax({
                cache: false,
                type: "POST",
                url: $addPath,
                data: {'followed_id': $userId}
            }).done(function (data) {
                //alert(data);
                $isFollowed = !$isFollowed;
                $("#friendButton").removeClass('btn-primary').addClass('btn-success');
                $("#friendButton").text("سي ديجا في شلة");
            })
        } else {
            $.ajax({
                cache: false,
                type: "POST",
                url: $deletePath,
                data: {'followed_id': $userId}
            }).done(function (data) {
                //alert(data);
                $isFollowed = !$isFollowed;
                $("#friendButton").removeClass('btn-danger').removeClass('btn-success').addClass('btn-primary');
                $("#friendButton").text("زيدو لشلة");
            })
        }
    });

    $("#friendButton").mouseover(function () {
        if ($isFollowed) {
            $("#friendButton").removeClass('btn-success').addClass('btn-danger');
            $("#friendButton").text('فسخ الكازي');
        }
    });
    $("#friendButton").mouseout(function () {
        if ($isFollowed) {
            $("#friendButton").removeClass('btn-danger').addClass('btn-success');
            $("#friendButton").text('سي ديجا في شلة');
        }
    });
});

var skipped = new Array();
var skippedindex = 0;

$(document).ready(function() {
    
    hoverToggle();
    //trigger search
    $("#SearchBar").on("keyup", function() {
        $("#searchOutput").fadeIn("fast");
        // Show Loading
        $(".more").fadeIn();
        $(".more").html("<span class='span10'>RETRIEVING RESULTS</span><img src='" + BASE_URL + "img/ajax-loader.gif' />");
        searchPeople($(this).val(), "bar");
    });

    $(document).click(function(e) {
        var clicked = $(e.target);
        if (!clicked.hasClass(".searchOutput")) {
            $("#searchOutput").fadeOut("fast");
            $("#SearchBar").val("");
        }

    });
    //search end

    //notification
    $("#notifybell").on("click", function() {
        console.log("click on notification");
        $("#notificationOutput").fadeIn("fast");
        // Show Loading
        notifications();
    });

    $(document).click(function(e) {
        var clicked = $(e.target);
        console.log(clicked);
        if (!clicked.hasClass(".notificationOutput") && clicked.attr('id') !== "notifybell") {
            $("#notificationOutput").fadeOut("fast");
        }
    });

    //notification end

    $(document).on('click', '.followbtn', function() { //Follow,Unfollow events
        var btn = $(this);
        var status = btn.children('.btntext-display').text();
        console.log("clicked" + status);
        if (status === 'Unfollow') {
            var id = $(this).attr('id');
            id = parseInt(id.replace(/\D/g, ''));
            //console.log($followerid);
            $.ajax({
                type: 'POST',
                url: BASE_URL + 'connections/unfollow',
                data: {followerid: id}
            }).done(function(data) {
                if (data.status === 'SUCCESS')
                {
                    console.log(data.status);
                    btn.removeClass('btn-danger');
                    btn.removeClass('btn-primary');
                    btn.addClass('btn-default');
                    btn.children('#follow-text').addClass('active');
                    btn.children('#following-text').removeClass('active');
                    btn.children('#following-text').css('display', 'none');
                    btn.children('#unfollow-text').css('display', 'none');
                    btn.children('#follow-text').css('display', 'block');
                    btn.children('#unfollow-text').removeClass('btntext-display');
                    btn.children('#follow-text').addClass('btntext-display');
                }
                else
                {
                    console.log(data['message']);
                    alert("Error in unfollow");
                }
            });
        }
        var btn = $(this);
        if (status === 'Follow') {
            console.log("in follow");
            var id = $(this).attr('id');
            console.log($(this).attr('id'));
            id = parseInt(id.replace(/\D/g, ''));
            console.log('followingid :' + id);
            $.ajax({
                type: 'POST',
                url: BASE_URL + 'connections/follow',
                data: {followingid: id}
            }).done(function(data) {
                console.log(data);
                if (data.status === 'SUCCESS')
                {
                    console.log('success');
                    console.log(btn);
                    btn.removeClass('btn-default');
                    btn.addClass('btn-primary');
                    btn.children('#follow-text').removeClass('btntext-display');
                    btn.children('#follow-text').removeClass('active');
                    btn.children('#following-text').addClass('active');
                    btn.children('#follow-text').css('display', 'none');
                    btn.children('#following-text').css('display', 'block');
                    console.log(btn.parent());
                    if (btn.parent().parent().hasClass('suggestion')) {
                        fetchNext(btn.parent().parent(), "followed");
                    }
                }
            });
        }
    });

    $(document).on('click', '.skip', function() { //Skipping suggestions
        console.log('clicked');
        fetchNext($(this).parent().parent(), "skipped");
    });
});

function hoverToggle() {
    $(document).on({
        mouseenter: function() {
            var status = $(this).children('.active').text();
            console.log("active " + status);
            if (status === 'Following') {
                $(this).children('#following-text').css('display', 'none');
                $(this).removeClass('btn-primary');
                $(this).addClass('btn-danger');
                $(this).children('#unfollow-text').addClass('btntext-display');
                $(this).children('#unfollow-text').css('display', 'block');
            }
        },
        mouseleave: function() {
            var status = $(this).children('.active').text();
            console.log("active on leave: " + status);
            if (status === 'Following') {
                $(this).removeClass('btn-danger');
                $(this).addClass('btn-primary');
                $(this).children('.btntext.active').css('display', 'block');
                $(this).children('#unfollow-text').css('display', 'none');
                $(this).children('#unfollow-text').removeClass('.btntext-display');
            }
        }
    }, '.followbtn');
}
function notifications() {
    $.ajax({
        type: "POST",
        url: BASE_URL + "notifications/shownotifications",
        data: {id: user_id},
        cache: false
    }).done(function(response) {
        console.log(response);
        console.log(user_id);
        if (response.data !== null)
        {
            $result = "";
            $(response.data).each(function(index, obj) {
                console.log(obj);
                //var notification = obj['user_details'];
                //$result = $result + "<li class='resultList'><a href='" + BASE_URL + user.username + "' class='userLink'><span class='span1'><img class='searchImg' src='" + imgURL(user.username) + "'><span class='span2'><span class='span3'><span class='span4'>" + user.fullname + "</span><span class='span5'>@" + user.username + "</span></span></span></span></a></li>";
                var notification = obj['Notification'];
                //console.log(notification);
                $result = $result + "<li class='resultList'>notifications</li>";
            });
            $(".NotifyResults").html($result);

            if (response.data.length === 10)
            {
                $(".seemore").fadeIn();
                $(".seemore").html('<a href="' + BASE_URL + 'discover/">Show all</a>');
            }
        }
        else
        {
            $(".NotifyResults").html("There are no notifications");
        }

    });

}
function searchPeople(keyword, display)
{
    if (keyword !== "") {
        console.log(display);
        $.ajax({
            type: "POST",
            url: BASE_URL + "user_details/search",
            data: {keyword: keyword, display: display},
            cache: false
        }).done(function(response) {
            $(".more").fadeOut('fast');
            $(".pagemore").fadeOut('fast');
            if (display === "bar")
            {

                console.log(response);
                if (response.data.length > 0)
                {
                    $result = "";
                    $(response.data).each(function(index, obj) {
                        var user = obj['user_details'];
                        $result = $result + "<li class='resultList'><a href='" + BASE_URL + user.username + "' class='userLink'><span class='span1'><img class='searchImg' src='" + imgURL(user.username) + "'><span class='span2'><span class='span3'><span class='span4'>" + user.fullname + "</span><span class='span5'>@" + user.username + "</span></span></span></span></a></li>";
                    });
                    $(".Results").html($result);

                    if (response.data.length === 10)
                    {
                        $(".more").fadeIn();
                        $(".more").html('<a href="' + BASE_URL + 'discover/' + keyword + '">See more results for "' + keyword + '"</a>');
                    }
                }
                else
                {
                    $(".Results").html("There are no results for " + keyword);
                }
            }
            else if (display === "page") {

                console.log(response);
                if (response.data.length > 0)
                {
                    $result = "";
                    $(response.data).each(function(index, obj) {
                        var user = obj['user_details'];
                        if (obj[0]['isFollower'] == 1 && obj[0]['followstatus'] == 1)
                            $followstatus = "You follow each other"
                        else if (obj[0]['isFollower'] == 1)
                            $followstatus = "Follows you";
                        else
                            $followstatus = "";
                        if (user.bio === null)
                            user.bio = "";
                        $result = "<div class='resultList col-sm-6 col-md-6'> <div class='thumbnail'> <img src=" + imgURL(user.username) + " class='users-thumb' alt='" + user.fullname + "'> <div class='caption'> <a href='" + BASE_URL + user.username + "' class='profile-name'>" + user.fullname + "</a> <div class='profile-baseinfo'> @<span class='profile-username' id='profile-username'>" + user.username + "</span><span class='follow-status'>" + $followstatus + "</span></div> <p class='profile-bio text-centered'>" + user.bio + "</p> </div> <button class='btn btn-primary followbtn' id='searchPageUser" + user.id + "'><span class='btntext' id='follow-text'>Follow</span><span class='btntext' id='following-text'>Following</span><span class='btntext' id='unfollow-text'>Unfollow</span></button> </div> </div>";
                        //$result = "<li class='resultList'><a href='" + BASE_URL + user.username + "' class='userLink'><span class='span1'><img class='searchImg' src='" + imgURL(user.username) + "'><span class='span2'><span class='span3'><span class='span4'>" + user.fullname + "</span><span class='span5'>@" + user.username + "</span></span></span></span></a></li>";
                        $("#searchPageOutput").append($result);
                        if (obj[0]['followstatus'] == 1)
                        {
                            $('#searchPageUser' + user.id).children('#following-text').addClass('active');
                            $('#searchPageUser' + user.id).children('#following-text').css('display', 'block');
                        }
                        else
                        {
                            $('#searchPageUser' + user.id).children('#follow-text').addClass('active');
                            $('#searchPageUser' + user.id).children('#follow-text').css('display', 'block');
                            $('#searchPageUser' + user.id).removeClass("btn-primary");
                            $('#searchPageUser' + user.id).addClass("btn-default");
                            $('#searchPageUser' + user.id).children('#follow-text').addClass("btntext-display");
                        }
                    });
                    
                }
                else
                {
                    $("#searchPageOutput").append("There are no results for '" + keyword + "'");
                }
            }
        });
    }
    else {
        $("#searchOutput").fadeOut("fast");
    }
}

function fetchNext(element, type) //fetch next suggestion
{
    var id = [];
    element.fadeOut('3000', function() {
        if (type === "skipped")
        {
            var currentskip = element.children().find('.suggestion-btn').attr('id');
            skipped[skippedindex] = parseInt(currentskip.replace(/\D/g, ''));
            skippedindex = skippedindex + 1;
        }
        element.remove();
        if ($('#suggestion-box').children().length === 2 && !$('#suggestion-box').siblings().hasClass('suggestion_page'))
        {

            $('.suggestion-btn').each(function(i) {
                console.log(i);
                var userid = $(this).attr('id');
                id[i] = parseInt(userid.replace(/\D/g, ''));
            });
            console.log(id);
            index = id.length;
            $.each(skipped, function(i) {
                console.log(skipped);
                id[index] = skipped[i];
                index = index + 1;
            });
            console.log(id);
            getSuggestions(1, id);
        }
        else if ($('#suggestion-box').children().length === 0)
        {
            $msg = "<p align='center'>No people to suggest<p>"
            $('#suggestion-box').append($msg);
        }
    });
}

function getSuggestions(limit, id) //retrieve suggestions of specified limit and skipping users with specified id
{
    $.ajax({
        type: 'POST',
        url: BASE_URL + 'user_details/suggest',
        data: {limit: limit, id: id}
    }).done(function(data) {
        if (data.status === 'SUCCESS')
        {
            console.log(data);
            if (data.data.length < 10 && data.data.length > 0)
            {
                console.log("made false");
                flag = false;
            }
            if ($('#suggestion-box').children().length > 0 && limit !== 1 && !$('#suggestion-box').siblings().hasClass('suggestion_page'))
            {
                $('#suggestion-box').children().remove();
            }
            if (data.data.length > 0)
            {

                $.each(data.data, function(index, obj) {
                    user = obj['user_details'];
                    if (user.bio === null)
                        user.bio = "";
                    if ($('.suggestion_page').length > 0)
                        $newSuggestion = $("<div class='suggestion col-sm-6 col-md-6'> <div class='thumbnail'> <img src=" + imgURL(user.username) + " class='users-thumb' alt='" + user.fullname + "'> <div class='caption'> <a href='" + BASE_URL + user.username + "' class='profile-name'>" + user.fullname + "</a> <div class='profile-baseinfo'> @<span class='profile-username' id='profile-username'>" + user.username + "</span></div> <p class='profile-bio text-centered'>" + user.bio + "</p> </div> <button class='btn btn-primary followbtn suggestion-btn' id='user" + user.id + "'><span class='btntext' id='follow-text'>Follow</span><span class='btntext' id='following-text'>Following</span><span class='btntext' id='unfollow-text'>Unfollow</span></button> </div> </div>");
                    else
                        $newSuggestion = "<div class='suggestion'><div class='media'><button type='button' class='close skip'>×</button><img class='media-object suggestion-pic pull-left' src='" + imgURL(user.username) + "' alt='" + user.fullname + "' <div class='media-body'><a href='" + BASE_URL + user.username + "' class='profile-name media-heading'>" + user.fullname + "</a><div class='profile-baseinfo'>@<span class='profile-username'>" + user.username + "</span></div><div class='suggestion-bio'><p class='profile-bio'>" + user.bio + "</p></div><button class='btn btn-default followbtn suggestion-btn' id='user" + user.id + "'><span class='btntext' id='follow-text'>Follow</span><span class='btntext' id='following-text'>Following</span><span class='btntext' id='unfollow-text'>Unfollow</span></button></div></div></div>";
                    $('#suggestion-box').append($newSuggestion);
                    $('#user' + user.id).children('#follow-text').addClass('active');
                    $('#user' + user.id).children('#follow-text').css('display', 'block');
                    $('#user' + user.id).removeClass("btn-primary");
                    $('#user' + user.id).addClass("btn-default");
                    $('#user' + user.id).children('#follow-text').addClass("btntext-display");

                });
                suggestion_start = suggestion_start + data.data.length;
                console.log(suggestion_start);
            }
            else if ($('#suggestion-box').children().length === 0)
            {
                $msg = "<p align='center'>No people to suggest<p>"
                $('#suggestion-box').append($msg);
            }
        }
        else
        {
            console.log("message :" + data['message']);
        }
    });
}

function imgURL(username)
{
    var url = BASE_URL + "img/upload/" + username + ".jpg";
    return url;
}
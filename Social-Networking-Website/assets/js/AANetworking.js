$(document).ready(function()
{
    //Button for profile post
    $('#submit_profile_post').click(function()
    {
        $.ajax
        ({
            type: "POST",
            url: "includes/handlers/AjaxSubmitProfilePost.php",
            data: $('form.profile_post').serialize(), 
            success: function(msg)
            {
                $("#post_form").modal('hide');
                location.reload();
            },
            error: function()
            {
                alert('Failure');
            }
        });
    });
});

function getUsers(value, user)
{
    $.post("includes/handlers/AjaxFriendSearch.php", {query:value, userLoggedIn:user}, function(data)
    {
        $(".results").html(data);
    });
}


function getDropdownData(user, type)
{
    if($(".dropdownDataWindow").css("height") == "0px")
    {
        var pageName;

        if(type == 'notification')
        {

        }

        else if(type == 'message')
        {
            pageName = "AjaxLoadMessages.php";
             $("span").remove("#unread_message");
        }

        var ajaxreq = $.ajax(
        {
            url: "includes/handlers/" + pageName,
            type: "POST",
            data: "page=1&user=" + user,
            cache: false,
            success: function(response)
            {
                $(".dropdownDataWindow").html(response);
                $(".dropdownDataWindow").css({"padding": "0px", "height" : "200px", "border" : "1px solid #DADADA"});
                $(".dropdownDataType").val(type);
            }
        });
    }

    else
    {
        $(".dropdownDataWindow").html("");
        $(".dropdownDataWindow").css({"padding": "0px", "height" : "0px", "border" : "none"});
    }
}
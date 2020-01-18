$(document).ready(function()
{

    $('#searchTextInput').focus(function()
    {
        if(window.matchMedia("(min-width: 800px)").matches)
        {
            $(this).animate({width: '250px'}, 500);
        }
    });

    $('.buttonHolder').on('click', function()
    {
        document.searchForm.submit();
    });

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

$(document).click(function(e)
{
    if(e.target.class != "searchResults" && e.target.id != "searchTextInput")
    {
        $(".searchResults").html("");
        $('.searchResultsFooter').html("");
        $('.searchResultsFooter').toggleClass('searchResultsFooterEmpty');
        $('.searchResultsFooter').toggleClass('searchResultsFooter');
    }

    if(e.target.class != "dropdownDataWindow" && e.target.id != "searchTextInput")
    {
        $(".dropdownDataWindow").html("");
        $(".dropdownDataWindow").css({"padding" : "0px", "height" : "0px"});        
    }
})


function getDropdownData(user, type)
{
    if($(".dropdownDataWindow").css("height") == "0px")
    {
        var pageName;

        if(type == 'notification')
        {
            pageName = "AjaxLoadNotifications.php";
            $("span").remove("#unreadNotifications");
        }

        else if(type == 'message')
        {
            pageName = "AjaxLoadMessages.php";
            $("span").remove("#unreadMessages");
        }

        else
        {
            alert("pageName is undefined.");
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
                $("#dropdownDataType").val(type);
            }
        });
    }

    else
    {
        $(".dropdownDataWindow").html("");
        $(".dropdownDataWindow").css({"padding": "0px", "height" : "0px", "border" : "none"});
    }
}

function getLiveSearchUsers(value, user)
{
    $.post("includes/handlers/AjaxSearch.php", {query: value, userLoggedIn: user}, function(data)
    {
        if($(".searchResultsFooterEmpty")[0])
        {
            $(".searchResultsFooterEmpty").toggleClass("searchResultsFooter");
            $(".searchResultsFooterEmpty").toggleClass("searchResultsFooterEmpty");
        }

        $('.searchResults').html(data);
        $('.searchResultsFooter').html("<a href = 'search.php?q=" + value + "'>See All Results</a>");
        
        if(data == "")
        {
            $('.searchResultsFooter').html("");
            $('.searchResultsFooter').toggleClass('searchResultsFooterEmpty');
            $('.searchResultsFooter').toggleClass('searchResultsFooter');
        }
    });
}
$(document).ready(function()
{
    //When the signup link is clicked, hide login and show registration form
    $("#signup").click(function()
    {
        $("#first").slideUp("slow", function()
        {
            $("#second").slideDown("slow");
        });
    });

    //When the login link is clicked, hide registration and show login form
    $("#login").click(function()
    {
        $("#second").slideUp("slow", function()
        {
            $("#first").slideDown("slow");
        });
    });
});
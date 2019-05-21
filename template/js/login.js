
/*
function validation()                                    
{ 
    var name = document.forms["RegForm"]["Name"];               
    var email = document.forms["RegForm"]["EMail"];    
    var phone = document.forms["RegForm"]["Telephone"];  
    var what =  document.forms["RegForm"]["Subject"];  
    var password = document.forms["RegForm"]["Password"];  
    var address = document.forms["RegForm"]["Address"];  
   
    if (name.value == "")                                  
    { 
        window.alert("Please enter your name."); 
        name.focus(); 
        return false; 
    } 
   
    if (address.value == "")                               
    { 
        window.alert("Please enter your address."); 
        name.focus(); 
        return false; 
    } 
       
    if (email.value == "")                                   
    { 
        window.alert("Please enter a valid e-mail address."); 
        email.focus(); 
        return false; 
    } 
   
    if (email.value.indexOf("@", 0) < 0)                 
    { 
        window.alert("Please enter a valid e-mail address."); 
        email.focus(); 
        return false; 
    } 
   
    if (email.value.indexOf(".", 0) < 0)                 
    { 
        window.alert("Please enter a valid e-mail address."); 
        email.focus(); 
        return false; 
    } 
   
    if (phone.value == "")                           
    { 
        window.alert("Please enter your telephone number."); 
        phone.focus(); 
        return false; 
    } 
   
    if (password.value == "")                        
    { 
        window.alert("Please enter your password"); 
        password.focus(); 
        return false; 
    } 
   
    if (what.selectedIndex < 1)                  
    { 
        alert("Please enter your course."); 
        what.focus(); 
        return false; 
    } 
   
    return true; 
}
*/


$(document).ready(function()
{
    $("#regForm").submit(function()
    {

        $.ajax(
        {
            url: "/login/chreg",
            type: "POST",
            data: $(this).serialize(),
            success: function(response)
            {
                var string = "";
                $('#result_form_reg').html(string);
                result = $.parseJSON(response);
                $.each(result, function(keyError, value)
                {
                    if (keyError == 'success')
                    {
                        $('#result_form_reg').css("color", "green");
                        string = value;
                        setTimeout(function () 
                        {
                            window.location.href = window.location.href;
                        }, 2000);
                    }
                    else
                    {
                        $('#result_form_reg').css("color", "red");
                        string += '* ' + value + '<br>';
                    }
                });

                $('#result_form_reg').html(string);


            },
            error: function(response)
            {
                $('#result_form_reg').css("color", "red");
                $('#result_form_reg').html('Ошибка. Данные не отправлены.');
            }
        });
        return false;
    });


     $("#loginForm").submit(function()
    {
        $.ajax(
        {
            url: "/login/chlogin",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) 
            {
                var string = "";
                $('#result_form_log').html(string);
                result = $.parseJSON(response);

                $.each(result, function(keyError, value)
                {
                    if (keyError == 'success')
                    {
                        $('#result_form_log').css("color", "green");
                        string = value;
                        setTimeout(function () 
                        {
                            document.location.href = "/";
                        }, 2000);
                    }
                    else
                    {
                        $('#result_form_log').css("color", "red");
                        string += '* ' + value + '<br>';
                    }
                });

                $('#result_form_log').html(string);

            },
            error: function(response)
            {
                $('#result_form_log').html('Ошибка. Данные не отправлены.');
            }
        });
        return false;
    });

});




$(".eye").click(function()
{
$(this).siblings(".eye").addClass("eye-active");
$(this).removeClass("eye-active");

    if ($(this).siblings("input").attr("type") === "text")
    {
        $(this).siblings("input").attr("type", "password");
        if ($(this).siblings("input").attr("id") === "password-2")
        {
            $("#password-3").attr("type", "password");
        }
    }
    else
    {
        $(this).siblings("input").attr("type", "text");
        if ($(this).siblings("input").attr("id") === "password-2")
        {
            $("#password-3").attr("type", "text");
        }
    }
    
});

$("#sign-up").click(function()
{
  $("#loginForm").hide("fast", function()
  {
    $("#regForm").show("fast");
  });
});

$("#sign-in").click(function()
{
  $("#regForm").hide("fast", function()
  {
    $("#loginForm").show("fast");
  });
});
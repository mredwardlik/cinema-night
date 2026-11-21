$(function ()
{
    /*
     * Sends a sign in / sign up form and shows the server answer.
     * On success the server returns {success: "...", redirect: "/cabinet"}.
     */
    function submitForm($form, url, $result)
    {
        var $button = $form.find('button[type=submit]');

        $form.on('submit', function (event)
        {
            event.preventDefault();
            $button.prop('disabled', true);

            $.ajax({
                url: url,
                type: 'POST',
                data: $form.serialize()
            })
            .done(function (result)
            {
                if (result.success)
                {
                    $result.removeClass('is-error').addClass('is-success').text(result.success);
                    setTimeout(function () { window.location.href = result.redirect || '/cabinet'; }, 900);
                    return;
                }

                if (result.thereis || result.password_r)
                {
                    $form.find('input[type=password], input[name=password_r]').val('');
                }

                $result.removeClass('is-success').addClass('is-error').html(App.errors(result));
                $button.prop('disabled', false);
            })
            .fail(function (xhr)
            {
                $result.removeClass('is-success').addClass('is-error').text(App.failText(xhr));
                $button.prop('disabled', false);
            });
        });
    }

    submitForm($('#loginForm'), '/login/chlogin', $('#result_form_log'));
    submitForm($('#regForm'), '/login/chreg', $('#result_form_reg'));

    // Show / hide password
    $('.eye').on('click', function ()
    {
        var $input = $(this).siblings('input');
        var show = $input.attr('type') === 'password';

        $(this).removeClass('eye-active').siblings('.eye').addClass('eye-active');
        $input.attr('type', show ? 'text' : 'password');

        if ($input.attr('id') === 'password-2')
        {
            $('#password-3').attr('type', show ? 'text' : 'password');
        }
    });

    // Switch between sign in and sign up
    $('#sign-up').on('click', function ()
    {
        $('#loginForm').hide('fast', function () { $('#regForm').show('fast'); });
    });

    $('#sign-in').on('click', function ()
    {
        $('#regForm').hide('fast', function () { $('#loginForm').show('fast'); });
    });
});

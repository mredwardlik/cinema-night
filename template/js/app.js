/*
 * Shared helpers for all pages.
 */

// Every AJAX request sends the CSRF token from <meta name="csrf-token">
$.ajaxSetup({
    headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
    dataType: 'json',
    cache: false
});

var App = {
    /*
     * Shows a message in a .responce-like element and hides it after `timeout` ms.
     * type: 'success' | 'error'
     */
    message: function (selector, text, type, timeout) {
        var $el = $(selector);
        $el.removeClass('is-success is-error')
           .addClass(type === 'success' ? 'is-success' : 'is-error')
           .html(text)
           .stop(true, true)
           .hide()
           .fadeIn(150);

        clearTimeout($el.data('timer'));

        if (timeout) {
            $el.data('timer', setTimeout(function () { $el.fadeOut(300); }, timeout));
        }
    },

    /* Joins all error messages of a JSON response into one HTML string */
    errors: function (result) {
        return $.map(result, function (value, key) {
            return key === 'success' || key === 'redirect' ? null : '• ' + $('<span>').text(value).html();
        }).join('<br>');
    },

    /* Text for failed requests (network error, expired session...) */
    failText: function (xhr) {
        if (xhr && xhr.responseJSON && xhr.responseJSON.error) {
            return xhr.responseJSON.error;
        }
        return 'Ошибка. Данные не отправлены, проверьте подключение.';
    }
};

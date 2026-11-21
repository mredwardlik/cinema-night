$(function ()
{
//-------------------------------------------------//
//                     PROFILE                     //
//-------------------------------------------------//

    var $profile = $('#profileForm');
    var $editable = $profile.find('input').not('[name=login]');

    $('#button-change').on('click', function ()
    {
        $editable.prop('disabled', false).addClass('is-editing');
        $editable.filter('[name=name]').trigger('focus');
        $(this).hide();
        $('#button-save').show();
    });

    $profile.on('submit', function (event)
    {
        event.preventDefault();

        $.post('/cabinet/ch', $profile.serialize())
            .done(function (result)
            {
                if (result.success)
                {
                    App.message('#responce', result.success, 'success', 1500);
                    $editable.prop('disabled', true).removeClass('is-editing');
                    $('#button-save').hide();
                    $('#button-change').show();
                    $('.notice').toggle(!$editable.filter('[name=name]').val() || !$editable.filter('[name=phone]').val());
                }
                else
                {
                    App.message('#responce', App.errors(result), 'error', 4000);
                }
            })
            .fail(function (xhr) { App.message('#responce', App.failText(xhr), 'error', 4000); });
    });

//-------------------------------------------------//
//                   SEAT MAP                      //
//-------------------------------------------------//

    var $room = $('#room');

    if ($room.length)
    {
        var hall = $room.data('hall');
        var price = Number($room.data('price')) || 0;
        var html = '';

        $.each(hall, function (rowIndex, seatsInRow)
        {
            var row = rowIndex + 1;
            html += '<div class="row"><span class="num-column">' + row + '</span>';

            for (var seat = 1; seat <= seatsInRow; seat++)
            {
                html += '<button type="button" class="seat" data-row="' + row + '" data-seat="' + seat + '" title="Ряд ' + row + ', место ' + seat + '">' + seat + '</button>';
            }

            html += '<span class="num-column">' + row + '</span></div>';
        });

        $room.html(html);

        function selected()
        {
            return $room.find('.seat.bay').map(function ()
            {
                return $(this).data('row') + ',' + $(this).data('seat');
            }).get();
        }

        function updateSelection()
        {
            var seats = selected();
            var count = seats.length;

            if (!count)
            {
                $('#selection').text('Нажмите на свободные места на схеме');
            }
            else
            {
                var word = count % 10 === 1 && count % 100 !== 11 ? 'место'
                    : (count % 10 >= 2 && count % 10 <= 4 && (count % 100 < 10 || count % 100 >= 20) ? 'места' : 'мест');
                $('#selection').html('Выбрано <b>' + count + '</b> ' + word + ' · <b>' + (count * price).toLocaleString('ru-RU') + ' ₸</b>');
            }

            $('#button-take').prop('disabled', count === 0);
        }

        function loadOccupied()
        {
            return $.post('/cabinet/seats').done(function (result)
            {
                $room.find('.seat.bought').removeClass('bought').prop('disabled', false).each(function ()
                {
                    $(this).text($(this).data('seat'));
                });

                $.each(result.occupied || [], function (i, value)
                {
                    var parts = value.split(',');
                    $room.find('.seat[data-row="' + parts[0] + '"][data-seat="' + parts[1] + '"]')
                        .removeClass('bay').addClass('bought').prop('disabled', true).text('×');
                });

                updateSelection();
            });
        }

        $room.on('click', '.seat', function ()
        {
            if (!$(this).hasClass('bought'))
            {
                $(this).toggleClass('bay');
                updateSelection();
            }
        });

        $('#button-take').on('click', function ()
        {
            var $button = $(this).prop('disabled', true);

            $.post('/cabinet/taken', { data: selected() })
                .done(function (result)
                {
                    if (result.success)
                    {
                        App.message('#responce-taken', result.success, 'success');
                        setTimeout(function () { window.location.reload(); }, 1500);
                    }
                    else
                    {
                        App.message('#responce-taken', App.errors(result), 'error', 5000);
                        loadOccupied();
                    }
                })
                .fail(function (xhr)
                {
                    App.message('#responce-taken', App.failText(xhr), 'error', 5000);
                    updateSelection();
                });
        });

        loadOccupied();
    }

//-------------------------------------------------//
//                 ADMIN: REQUESTS                 //
//-------------------------------------------------//

    function decide(url, $button)
    {
        var id = $button.data('id');
        var $request = $('#request-' + id);
        $request.find('button').prop('disabled', true);

        $.post(url, { id: id })
            .done(function (result)
            {
                if (result.success)
                {
                    App.message('#responce-admin', result.success, 'success');
                    $request.slideUp(250);
                    setTimeout(function () { window.location.reload(); }, 900);
                }
                else
                {
                    App.message('#responce-admin', App.errors(result), 'error', 4000);
                    $request.find('button').prop('disabled', false);
                }
            })
            .fail(function (xhr)
            {
                App.message('#responce-admin', App.failText(xhr), 'error', 4000);
                $request.find('button').prop('disabled', false);
            });
    }

    $('.js-approve').on('click', function () { decide('/cabinet/approve', $(this)); });
    $('.js-reject').on('click', function () { decide('/cabinet/reject', $(this)); });
});

import jQuery from 'jquery';
import 'jquery-toast-plugin/src/jquery.toast.css';

window.$ = window.jQuery = jQuery;

await import('jquery-toast-plugin/src/jquery.toast.js');
await import('ez-plus/src/jquery.ez-plus.js');

const MSG_NO_IMAGES = 'Для этой модели не осталось изображений для голосования';
const MSG_SERVER_ERROR = 'Ошибка сервера';
const MSG_NOT_FOUND = 'Объект не найден';
const MSG_ALREADY_VOTED = 'Вы уже голосовали за эту машину';

const ezPlusOptions = {
    zoomType: 'lens',
    lensShape: 'round',
    lensSize: 200,
    cursor: 'pointer',
};

function destroyEzPlus($img) {
    const el = $img.get(0);
    if (!el) {
        return;
    }
    const inst = jQuery.data(el, 'ezPlus');
    if (inst && typeof inst.destroy === 'function') {
        inst.destroy();
    }
    jQuery.removeData(el, 'ezPlus');
}

function hidePairAndDestroyZoom() {
    $('#vote-pair').addClass('hidden');
    $('#vote-pair-instruction').addClass('hidden');
    destroyEzPlus($('#vote-img-a'));
    destroyEzPlus($('#vote-img-b'));
    $('#vote-img-a')
        .attr({ src: '', alt: '' })
        .removeAttr('data-car-id');
    $('#vote-img-b')
        .attr({ src: '', alt: '' })
        .removeAttr('data-car-id');
}

function showPairWithImages(pair) {
    const [left, right] = pair;

    hidePairAndDestroyZoom();

    $('#vote-img-a').attr({
        src: left.image,
        alt: '',
        'data-car-id': String(left.id),
    });
    $('#vote-img-b').attr({
        src: right.image,
        alt: '',
        'data-car-id': String(right.id),
    });

    $('#vote-pair-message').addClass('hidden').text('');
    $('#vote-pair-instruction').removeClass('hidden');
    $('#vote-pair').removeClass('hidden');

    $('#vote-img-a').ezPlus(ezPlusOptions);
    $('#vote-img-b').ezPlus(ezPlusOptions);
}

$(() => {
    const token = $('meta[name="csrf-token"]').attr('content');
    if (token) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token,
            },
        });
    }

    const $select = $('#vote-model-select');
    const $modelsError = $('#vote-models-error');
    const $pairMessage = $('#vote-pair-message');

    let currentModelId = '';
    let votingInProgress = false;

    function loadNextPair(modelId) {
        if (!modelId) {
            return;
        }

        $.ajax({
            url: `/api/next/${encodeURIComponent(modelId)}`,
            method: 'GET',
            dataType: 'json',
        })
            .done((pair) => {
                if (!Array.isArray(pair) || pair.length < 2) {
                    hidePairAndDestroyZoom();
                    $pairMessage.text(MSG_SERVER_ERROR).removeClass('hidden');

                    return;
                }
                showPairWithImages(pair);
            })
            .fail((xhr) => {
                hidePairAndDestroyZoom();
                $pairMessage
                    .removeClass('hidden')
                    .text(
                        xhr.status === 404
                            ? MSG_NO_IMAGES
                            : MSG_SERVER_ERROR,
                    );
            });
    }

    $.ajax({
        url: '/api/models',
        method: 'GET',
        dataType: 'json',
    })
        .done((items) => {
            items.forEach((item) => {
                $select.append(
                    $('<option>', {
                        value: String(item.id),
                        text: item.title,
                    }),
                );
            });
        })
        .fail(() => {
            $modelsError.text(MSG_SERVER_ERROR).removeClass('hidden');
            $select.prop('disabled', true);
        });

    $select.on('change', function () {
        const modelId = $(this).val();

        $pairMessage.addClass('hidden').text('');
        currentModelId = modelId || '';

        if (!modelId) {
            hidePairAndDestroyZoom();

            return;
        }

        loadNextPair(modelId);
    });

    $('#vote-pair').on('click', '.vote-img-wrap', function () {
        if (votingInProgress || !currentModelId) {
            return;
        }

        const $img = $(this).find('img').first();
        const $otherImg = $(this)
            .siblings('.vote-img-wrap')
            .find('img')
            .first();
        const selectedCarId = $img.attr('data-car-id');
        const otherCarId = $otherImg.attr('data-car-id');

        if (!selectedCarId || !otherCarId) {
            return;
        }

        votingInProgress = true;

        const voteRequest = $.ajax({
            url: '/api/vote',
            method: 'POST',
            contentType: 'application/json; charset=UTF-8',
            data: JSON.stringify({
                selected_car_id: Number(selectedCarId),
                other_car_id: Number(otherCarId),
            }),
        });

        voteRequest.done(() => {
            if (voteRequest.status === 204) {
                loadNextPair(currentModelId);
            }
        });

        voteRequest.fail((xhr) => {
            let msg = MSG_SERVER_ERROR;
            if (xhr.status === 404) {
                msg = MSG_NOT_FOUND;
            } else if (xhr.status === 409) {
                msg = MSG_ALREADY_VOTED;
            }
            $.toast({
                text: msg,
                showHideTransition: 'fade',
                position: 'top-right',
            });
        });

        voteRequest.always(() => {
            votingInProgress = false;
        });
    });
});

import jQuery from 'jquery';
import 'jquery-toast-plugin/src/jquery.toast.css';
import 'datatables/media/css/jquery.dataTables.min.css';

window.$ = window.jQuery = jQuery;

// DataTables 1.10 рассчитан на jQuery 3; в jQuery 4 убраны $.isArray, $.trim и др.
if (typeof jQuery.isArray !== 'function') {
    jQuery.isArray = Array.isArray;
}
if (typeof jQuery.trim !== 'function') {
    jQuery.trim = (text) => String(text ?? '').trim();
}

// legacy DataTables UMD: при сборке в ESM попадает CommonJS-ветка и экспортируется фабрика —
// её нужно вызвать с тем же jQuery, иначе $.fn.DataTable не появится
const dtMod = await import('datatables/media/js/jquery.dataTables.js');
const installDataTables = dtMod.default ?? dtMod;
if (typeof installDataTables === 'function') {
    installDataTables(window, jQuery);
}

await import('jquery-toast-plugin/src/jquery.toast.js');

const MSG_SERVER_ERROR = 'Ошибка сервера';
const MSG_NOT_FOUND = 'Записи не найдены';

function firstValidationMessage(xhr) {
    const json = xhr.responseJSON;
    if (!json) {
        return MSG_SERVER_ERROR;
    }
    if (typeof json.message === 'string' && json.message.length > 0) {
        return json.message;
    }
    if (json.errors && typeof json.errors === 'object') {
        const flat = Object.values(json.errors).flat();
        const first = flat.find((m) => typeof m === 'string' && m.length > 0);
        if (first) {
            return first;
        }
    }

    return MSG_SERVER_ERROR;
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

    const $model = $('#stat-model-select');
    const $yearFrom = $('#stat-year-from');
    const $yearTo = $('#stat-year-to');
    const $filtersError = $('#stat-filters-error');

    const table = $('#stat-table').DataTable({
        data: [],
        columns: [
            {
                data: 'image',
                orderable: false,
                render(data) {
                    const src = $('<div>').text(data).html();

                    return `<img src="${src}" alt="" class="max-h-16 max-w-[8rem] object-contain" />`;
                },
            },
            { data: 'year' },
            { data: 'votes_count' },
        ],
        paging: true,
        searching: false,
        info: true,
        language: {
            paginate: {
                first: 'Первая',
                last: 'Последняя',
                next: 'След.',
                previous: 'Пред.',
            },
            info: 'Записи с _START_ по _END_, всего _TOTAL_',
            infoEmpty: 'Нет записей',
            zeroRecords: 'Нет данных',
        },
    });

    function loadStat() {
        const carModelId = $model.val();
        const yearFrom = $yearFrom.val();
        const yearTo = $yearTo.val();

        if (!carModelId || !yearFrom || !yearTo) {
            table.clear().draw();

            return;
        }

        $.ajax({
            url: '/api/stat',
            method: 'GET',
            dataType: 'json',
            data: {
                car_model_id: Number(carModelId),
                year_from: Number(yearFrom),
                year_to: Number(yearTo),
            },
        })
            .done((rows) => {
                table.clear().rows.add(rows).draw();
            })
            .fail((xhr) => {
                table.clear().draw();
                let msg = MSG_SERVER_ERROR;
                if (xhr.status === 422) {
                    msg = firstValidationMessage(xhr);
                } else if (xhr.status === 404) {
                    msg = MSG_NOT_FOUND;
                }
                $.toast({
                    text: msg,
                    showHideTransition: 'fade',
                    position: 'top-right',
                });
            });
    }

    $.ajax({
        url: '/api/models/all',
        method: 'GET',
        dataType: 'json',
    })
        .done((items) => {
            items.forEach((item) => {
                $model.append(
                    $('<option>', {
                        value: String(item.id),
                        text: item.title,
                    }),
                );
            });
        })
        .fail(() => {
            $filtersError.text(MSG_SERVER_ERROR).removeClass('hidden');
            $model.prop('disabled', true);
        });

    $.ajax({
        url: '/api/years',
        method: 'GET',
        dataType: 'json',
    })
        .done((years) => {
            years.forEach((y) => {
                const label = String(y);
                $yearFrom.append($('<option>', { value: label, text: label }));
                $yearTo.append($('<option>', { value: label, text: label }));
            });
        })
        .fail(() => {
            $filtersError.text(MSG_SERVER_ERROR).removeClass('hidden');
            $yearFrom.prop('disabled', true);
            $yearTo.prop('disabled', true);
        });

    $model.add($yearFrom).add($yearTo).on('change', loadStat);
});

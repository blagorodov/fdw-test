import jQuery from 'jquery';

window.$ = window.jQuery = jQuery;

$(() => {
    const version = $.fn.jquery ?? 'unknown';

    $('#jq-status')
        .text(`jQuery ${version} подключён и выполняется.`)
        .removeClass('hidden');

    $('#jq-check-btn').on('click', function () {
        $(this).toggleClass('bg-green-600 bg-blue-600');
        $('#jq-click-msg').text(
            `Событие click обработано jQuery — ${new Date().toLocaleTimeString()}`,
        );
    });
});

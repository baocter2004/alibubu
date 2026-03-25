import './bootstrap';
import $ from 'jquery';

window.$ = window.jQuery = $;

// Mobile menu
function openMenu() {
    $('#mobile-menu').removeClass('hidden');
    requestAnimationFrame(() => $('#menu-panel').removeClass('-translate-x-full'));
    $('body').css('overflow', 'hidden');
}

function closeMenu() {
    $('#menu-panel').addClass('-translate-x-full');
    setTimeout(() => {
        $('#mobile-menu').addClass('hidden');
        $('body').css('overflow', '');
    }, 300);
}

$(document).ready(function () {
    $('#menu-open').on('click', openMenu);
    $('#menu-close').on('click', closeMenu);
    $('#menu-backdrop').on('click', closeMenu);

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeMenu();
    });
});

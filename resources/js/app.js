import "./bootstrap";

let toast = null;

function getToast() {
    if (toast || typeof Swal === "undefined") {
        return toast;
    }

    toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        customClass: { popup: "alibubu-toast" },
        didOpen: (el) => {
            el.addEventListener("mouseenter", Swal.stopTimer);
            el.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    return toast;
}

window.notify = function (icon, text) {
    const instance = getToast();

    if (!text || !instance) return;

    const labels = window.alertLabels || {};
    instance.fire({ icon, title: labels[icon] || "", text });
};

$(function () {
    notify("success", document.body.dataset.success);
    notify("error", document.body.dataset.error);
});

// Mobile menu
function openMenu() {
    $("#mobile-menu").removeClass("hidden");
    requestAnimationFrame(() =>
        $("#menu-panel").removeClass("-translate-x-full"),
    );
    $("body").css("overflow", "hidden");
}

function closeMenu() {
    $("#menu-panel").addClass("-translate-x-full");
    setTimeout(() => {
        $("#mobile-menu").addClass("hidden");
        $("body").css("overflow", "");
    }, 300);
}

$(document).ready(function () {
    $("#menu-open").on("click", openMenu);
    $("#menu-close").on("click", closeMenu);
    $("#menu-backdrop").on("click", closeMenu);

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") closeMenu();
    });
});

let invalids = $(".is-invalid")
    .filter(function () {
        return $(this).is(":visible") && $(this).offset() !== undefined;
    })
    .toArray();

let firstInvalid = invalids.sort(
    (a, b) => $(a).offset().top - $(b).offset().top,
)[0];

if (firstInvalid) {
    let $target = $(firstInvalid).closest(".address-item");
    if (!$target.length) $target = $(firstInvalid);

    $("html, body").animate(
        {
            scrollTop: $target.offset().top - 150,
        },
        500,
    );

    $(firstInvalid).focus();
}

$(document).on("click", "[data-locale-toggle]", function (e) {
    e.stopPropagation();
    const $menu = $(this).siblings("[data-locale-menu]");
    $("[data-locale-menu]").not($menu).addClass("hidden");
    $menu.toggleClass("hidden");
    $(this).attr("aria-expanded", !$menu.hasClass("hidden"));
});

$(document).on("click", function () {
    $("[data-locale-menu]").addClass("hidden");
    $("[data-locale-toggle]").attr("aria-expanded", "false");
});

$(document).on("click", "#account-menu button", function (e) {
    e.stopPropagation();
    $("#account-dropdown").toggleClass("hidden");
});

$(document).on("click", function () {
    $("#account-dropdown").addClass("hidden");
});

$(document).on("click", "#category-menu button", function (e) {
    e.stopPropagation();
    $("#category-dropdown").toggleClass("hidden");
    $("#account-dropdown").addClass("hidden");
});

$(document).on("click", function () {
    $("#category-dropdown").addClass("hidden");
});

$(function () {
    const $header = $("header.sticky");

    if (!$header.length) return;

    let lastScroll = 0;

    $(window).on("scroll", function () {
        const top = $(this).scrollTop();

        $header.toggleClass("shadow-md", top > 8);

        if (top > 240 && top > lastScroll) {
            $header.addClass("-translate-y-full");
        } else {
            $header.removeClass("-translate-y-full");
        }

        lastScroll = top;
    });
});

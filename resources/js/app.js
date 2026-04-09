import "./bootstrap";
import $ from "jquery";

window.$ = window.jQuery = $;

// Alert
$(function () {
    if (document.body.dataset.success) {
        Swal.fire({
            icon: "success",
            title: "Thành công!",
            text: document.body.dataset.success,
            confirmButtonText: "OK",
        });
    }

    if (document.body.dataset.error) {
        Swal.fire({
            icon: "error",
            title: "Thất bại!",
            text: document.body.dataset.error,
            confirmButtonText: "OK",
        });
    }
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

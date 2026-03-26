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

//Scroll To First Error

let firstInvalid = $(".is-invalid").first();

if (firstInvalid.length) {
    $("html, body").animate(
        {
            scrollTop: firstInvalid.offset().top - 100,
        },
        1000,
        "linear",
    );
}

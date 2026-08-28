document.addEventListener("DOMContentLoaded", function () {
    const sidebar = $("#sidebar");
    const overlay = $("#overlay");

    $("#sidebarToggle, #sidebarToggleMobile").on("click", function () {
        if ($(window).width() <= 768) {
            sidebar.toggleClass("show");
            overlay.toggleClass("active");
            $("body").toggleClass("overflow-hidden");
        } else {
            sidebar.toggleClass("collapsed");
        }
    });

    overlay.on("click", function () {
        sidebar.removeClass("show");
        overlay.removeClass("active");
        $("body").removeClass("overflow-hidden");
    });

    $(window).on("resize", function () {
        if ($(window).width() > 768) {
            sidebar.removeClass("show");
            overlay.removeClass("active");
            $("body").removeClass("overflow-hidden");
        }
    });

    $(window).on("scroll", function () {
        if ($(this).scrollTop() > 200) {
            $("#scrollToTop").removeClass("opacity-0 pointer-events-none");
        } else {
            $("#scrollToTop").addClass("opacity-0 pointer-events-none");
        }
    });

    $("#scrollToTop").on("click", function () {
        $("html, body").animate({ scrollTop: 0 }, 500);
    });

    $(".sidebar-dropdown.active").find(".submenu").show();
    $(".sidebar-dropdown.active").find(".arrow-icon").addClass("rotate-90");

    $(".dropdown-toggle").on("click", function (e) {
        e.preventDefault();

        let $parent = $(this).parent(".sidebar-dropdown");
        let $submenu = $parent.find(".submenu");
        let $arrow = $(this).find(".arrow-icon");

        $submenu.slideToggle(300);

        $arrow.toggleClass("rotate-90");
    });
});

$(document).on("submit", "form[data-confirm]", function (e) {
    const $form = $(this);

    if ($form.data("confirmed")) {
        return true;
    }

    e.preventDefault();

    Swal.fire({
        icon: "warning",
        title: $form.data("confirm-title") || "",
        text: $form.data("confirm"),
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: $form.data("confirm-yes") || (window.confirmLabels && window.confirmLabels.yes) || "OK",
        cancelButtonText: $form.data("confirm-no") || (window.confirmLabels && window.confirmLabels.no) || "Cancel",
    }).then(function (result) {
        if (result.isConfirmed) {
            $form.data("confirmed", true).trigger("submit");
        }
    });
});

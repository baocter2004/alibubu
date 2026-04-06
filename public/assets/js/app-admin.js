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
});

document.addEventListener("DOMContentLoaded", function () {
    $("#sidebarToggle").on("click", function () {
        $("#sidebar").toggleClass("active");
        $("#content").toggleClass("active");
    });

    $(window).on("resize", function () {
        if ($(window).width() > 768) {
            $("#sidebar").removeClass("active");
            $("#content").removeClass("active");
        }
    });

    $("#closeSidebar").on("click", function () {
        $("#sidebar").removeClass("active");
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

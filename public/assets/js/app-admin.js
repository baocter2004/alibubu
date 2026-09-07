document.addEventListener("DOMContentLoaded", function () {
    const $sidebar = $("#sidebar");
    const $overlay = $("#overlay");
    const RAIL_KEY = "alibubu:sidebar-collapsed";
    const isDesktop = () => $(window).width() > 1024;
    const syncSidebarToggle = () => {
        const expanded = isDesktop()
            ? !$sidebar.hasClass("collapsed")
            : $sidebar.hasClass("show");

        $("#sidebarToggle").attr("aria-expanded", expanded ? "true" : "false");
    };

    if (isDesktop() && localStorage.getItem(RAIL_KEY) === "1") {
        $sidebar.addClass("collapsed");
    }
    syncSidebarToggle();

    function openSubmenus() {
        $(".sidebar-dropdown.active").find(".submenu").show();
        $(".sidebar-dropdown.active").find(".arrow-icon").addClass("rotate-90");
    }

    $("#sidebarToggle").on("click", function () {
        if (isDesktop()) {
            $sidebar.toggleClass("collapsed");
            localStorage.setItem(RAIL_KEY, $sidebar.hasClass("collapsed") ? "1" : "0");

            if ($sidebar.hasClass("collapsed")) {
                $(".submenu").hide();
                $(".arrow-icon").removeClass("rotate-90");
            } else {
                openSubmenus();
            }

            syncSidebarToggle();

            return;
        }

        $sidebar.toggleClass("show");
        $overlay.toggleClass("active");
        $("body").toggleClass("overflow-hidden");
        syncSidebarToggle();
    });

    $("#sidebarClose, #overlay").on("click", function () {
        $sidebar.removeClass("show");
        $overlay.removeClass("active");
        $("body").removeClass("overflow-hidden");
        syncSidebarToggle();
    });

    $(window).on("resize", function () {
        if (isDesktop()) {
            $sidebar.removeClass("show");
            $overlay.removeClass("active");
            $("body").removeClass("overflow-hidden");
        }
        syncSidebarToggle();
    });

    $(".dropdown-toggle").on("click", function (e) {
        if ($sidebar.hasClass("collapsed") && isDesktop()) {
            return;
        }

        e.preventDefault();

        const $submenu = $(this).siblings(".submenu");
        const willExpand = $submenu.is(":hidden");
        $submenu.slideToggle(220);
        $(this).find(".arrow-icon").toggleClass("rotate-90");
        $(this).attr("aria-expanded", willExpand ? "true" : "false");
    });

    $(window).on("scroll", function () {
        $("#scrollToTop").toggleClass(
            "opacity-0 pointer-events-none",
            $(this).scrollTop() <= 200,
        );
    });

    $("#scrollToTop").on("click", function () {
        $("html, body").animate({ scrollTop: 0 }, 400);
    });

    openSubmenus();
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

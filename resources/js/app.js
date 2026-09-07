import "./bootstrap";
import "./cart";
import "./admin-dashboard";

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
    $("#mobile-menu").attr("aria-hidden", "false");
    $("#menu-open").attr("aria-expanded", "true");
    requestAnimationFrame(() =>
        $("#menu-panel").removeClass("-translate-x-full"),
    );
    $("body").css("overflow", "hidden");
}

function closeMenu() {
    $("#menu-panel").addClass("-translate-x-full");
    setTimeout(() => {
        $("#mobile-menu").addClass("hidden");
        $("#mobile-menu").attr("aria-hidden", "true");
        $("#menu-open").attr("aria-expanded", "false");
        $("body").css("overflow", "");
    }, 300);
}

$(document).ready(function () {
    $("#menu-open").on("click", openMenu);
    $("#menu-close").on("click", closeMenu);
    $("#menu-backdrop").on("click", closeMenu);

    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            closeMenu();
            $("#account-dropdown, #category-dropdown, [data-locale-menu]").addClass("hidden");
            $("#account-menu-toggle, #category-menu-toggle, [data-locale-toggle]").attr("aria-expanded", "false");
        }
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

$(document).on("click", "#account-menu-toggle", function (e) {
    e.stopPropagation();
    const $menu = $("#account-dropdown");
    const expanded = $menu.hasClass("hidden");

    $menu.toggleClass("hidden", !expanded);
    $(this).attr("aria-expanded", expanded ? "true" : "false");
    $("#category-dropdown").addClass("hidden");
    $("#category-menu-toggle").attr("aria-expanded", "false");
});

$(document).on("click", function () {
    $("#account-dropdown").addClass("hidden");
    $("#account-menu-toggle").attr("aria-expanded", "false");
});

$(document).on("click", "#category-menu button", function (e) {
    e.stopPropagation();
    const $menu = $("#category-dropdown");
    const expanded = $menu.hasClass("hidden");

    $menu.toggleClass("hidden", !expanded);
    $(this).attr("aria-expanded", expanded ? "true" : "false");
    $("#account-dropdown").addClass("hidden");
    $("#account-menu-toggle").attr("aria-expanded", "false");
});

$(document).on("click", function () {
    $("#category-dropdown").addClass("hidden");
    $("#category-menu-toggle").attr("aria-expanded", "false");
});

$(document).on("input", "[data-search-input]", function () {
    $(this).siblings("[data-search-clear]").toggleClass("hidden", !$(this).val().trim());
});

$(document).on("click", "[data-search-clear]", function () {
    const $input = $(this).siblings("[data-search-input]");

    $input.val("").trigger("input").focus();
    $input.closest("[data-search-box]").find("[data-search-panel]").addClass("hidden").empty();
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

$(function () {
    const targets = document.querySelectorAll(".reveal");

    if (!targets.length) return;

    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (reduced || !("IntersectionObserver" in window)) {
        targets.forEach((el) => el.classList.add("is-visible"));
        return;
    }
    document.querySelectorAll("[data-reveal-group]").forEach((group) => {
        group.querySelectorAll(":scope > .reveal").forEach((el, i) => {
            el.style.setProperty("--reveal-delay", Math.min(i, 8) * 55 + "ms");
        });
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            });
        },
        { rootMargin: "0px 0px -8% 0px", threshold: 0.06 },
    );

    targets.forEach((el) => observer.observe(el));
});

// Keep a small, private browsing history to help shoppers return to products.
const RECENT_PRODUCTS_KEY = "alibubu.recent-products";

function readRecentProducts() {
    try {
        const data = JSON.parse(window.localStorage.getItem(RECENT_PRODUCTS_KEY) || "[]");
        return Array.isArray(data) ? data : [];
    } catch (error) {
        return [];
    }
}

function writeRecentProducts(items) {
    try {
        window.localStorage.setItem(RECENT_PRODUCTS_KEY, JSON.stringify(items));
    } catch (error) {
        // Private browsing or blocked storage must not affect shopping.
    }
}

$(function () {
    const $source = $("[data-recent-product]");
    const $section = $("[data-recently-viewed]");
    const $list = $section.find("[data-recent-list]");

    if (!$source.length || !$section.length || !$list.length) return;

    const current = {
        id: $source.data("recent-id"),
        name: $source.data("recent-name"),
        url: $source.data("recent-url"),
        thumbnail: $source.data("recent-thumbnail"),
        price: $source.data("recent-price"),
    };
    const items = [current, ...readRecentProducts().filter((item) => String(item.id) !== String(current.id))].slice(0, 6);

    writeRecentProducts(items);

    items.slice(1, 5).forEach((item) => {
        const $card = $("<a>", {
            href: item.url,
            class: "group flex items-center gap-3 p-3 bg-card border border-border rounded-xl hover:border-primary/30 hover:shadow-md transition-all",
            "aria-label": item.name,
        });
        const $media = $("<span>", {
            class: "w-16 h-16 shrink-0 rounded-lg bg-white border border-border overflow-hidden flex items-center justify-center",
        });

        $media.append(item.thumbnail
            ? $("<img>", { src: item.thumbnail, alt: item.name, loading: "lazy", class: "w-full h-full object-contain p-1" })
            : $("<i>", { class: "fa-solid fa-box-open text-muted-foreground/25", "aria-hidden": "true" }));
        $card.append($media, $("<span>", { class: "min-w-0" }).append(
            $("<span>", { class: "block text-sm font-semibold text-foreground line-clamp-2 group-hover:text-primary transition-colors", text: item.name }),
            $("<span>", { class: "block mt-1 text-sm price-main", text: item.price }),
        ));
        $list.append($card);
    });

    if ($list.children().length) {
        $section.removeClass("hidden");
    }

    $section.on("click", "[data-clear-recent]", function () {
        writeRecentProducts([current]);
        $section.addClass("hidden");
    });
});

const csrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

const labels = () => window.cartLabels || {};

function request(url, method, body) {
    return fetch(url, {
        method: "POST",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": csrf(),
        },
        body: buildBody(body, method),
    }).then(async (response) => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || labels().failed || "");
        }

        return data;
    });
}

function buildBody(body, method) {
    const form = body instanceof FormData ? body : new FormData();

    if (!(body instanceof FormData) && body) {
        Object.entries(body).forEach(([key, value]) => form.append(key, value));
    }

    if (method && method !== "POST") {
        form.set("_method", method);
    }

    return form;
}

function setBusy($button, busy) {
    if (!$button.length) return;

    if (busy) {
        $button.data("label", $button.html()).prop("disabled", true).addClass("opacity-70");
        $button.html('<i class="fa-solid fa-circle-notch fa-spin"></i>');
        return;
    }

    $button.prop("disabled", false).removeClass("opacity-70");

    if ($button.data("label")) {
        $button.html($button.data("label"));
    }
}

function submitterOf(event, $form) {
    const submitter = event.originalEvent?.submitter;

    return submitter ? $(submitter) : $form.find("button[type=submit]").first();
}

function formDataWith(form, $button) {
    const data = new FormData(form);
    const name = $button.attr("name");

    if (name) {
        data.set(name, $button.attr("value") ?? "");
    }

    return data;
}

function renderCartCount(count) {
    $("[data-cart-count]").each(function () {
        const $badge = $(this);
        const cap = parseInt($badge.data("cart-count-cap"), 10) || 99;

        $badge.text(count > cap ? cap + "+" : count).toggleClass("hidden", count < 1);
    });
}

function renderTotals(data) {
    if (!data.totals) return;

    if (data.totals.is_empty) {
        window.location.reload();
        return;
    }

    $.each(data.lines || {}, function (key, line) {
        $('[data-line-subtotal="' + key + '"]').text(line.subtotal);
        $('[data-line-quantity="' + key + '"]').val(line.quantity);
    });

    $("[data-cart-subtotal]").text(data.totals.subtotal);
    $("[data-cart-total]").text(data.totals.total);
    $("[data-cart-discount]").text("-" + data.totals.discount);
    $("[data-cart-discount-row]").toggleClass("hidden", !data.totals.has_discount);
}

const COMPARE_HIDDEN_KEY = "alibubu.compare.hidden";

function compareDismissed() {
    try {
        return window.localStorage.getItem(COMPARE_HIDDEN_KEY) === "1";
    } catch (error) {
        return false;
    }
}

function setCompareDismissed(value) {
    try {
        value
            ? window.localStorage.setItem(COMPARE_HIDDEN_KEY, "1")
            : window.localStorage.removeItem(COMPARE_HIDDEN_KEY);
    } catch (error) {
    }
}

function compareLine(item) {
    const $form = $("<form>", {
        method: "POST",
        action: "/compare/" + item.id,
        class: "absolute -top-1 -right-1",
    }).attr("data-compare-remove", "").attr("data-product", item.id);

    $form.append(
        $("<input>", { type: "hidden", name: "_token", value: csrf() }),
        $("<input>", { type: "hidden", name: "_method", value: "DELETE" }),
        $("<button>", {
            type: "submit",
            "aria-label": item.name,
            class: "w-5 h-5 rounded-full bg-foreground text-white text-[9px] flex items-center justify-center shadow hover:bg-danger transition-colors",
        }).append($("<i>", { class: "fa-solid fa-xmark" })),
    );

    return $("<div>", {
        class: "relative shrink-0 w-14 h-14 bg-white border border-border rounded-xl overflow-hidden",
    }).append(
        $("<a>", { href: item.url, title: item.name, class: "w-full h-full flex items-center justify-center" }).append(
            item.thumbnail
                ? $("<img>", { src: item.thumbnail, alt: item.name, class: "w-full h-full object-contain p-1" })
                : $("<i>", { class: "fa-solid fa-box-open text-muted-foreground/30" }),
        ),
        $form,
    );
}

function renderCompareBar(data, { reveal = false } = {}) {
    const $bar = $("[data-compare-bar]");

    if (!$bar.length) return;

    const items = data.items || [];
    const max = parseInt($bar.data("compare-max"), 10) || 4;
    const $list = $bar.find("[data-compare-list]");

    $list.empty();
    items.forEach((item) => $list.append(compareLine(item)));

    for (let i = items.length; i < max; i++) {
        $list.append(
            $("<span>", {
                class: "shrink-0 w-14 h-14 rounded-xl border border-dashed border-border flex items-center justify-center text-muted-foreground/40",
            }).append($("<i>", { class: "fa-solid fa-plus text-xs" })),
        );
    }

    $("[data-compare-count]").text(items.length + "/" + max);

    if (reveal) {
        setCompareDismissed(false);
    }

    applyCompareVisibility(items.length);
}

function applyCompareVisibility(count) {
    const $bar = $("[data-compare-bar]");
    const $reopen = $("[data-compare-reopen]");
    const hidden = compareDismissed();
    const showBar = count > 0 && !hidden;

    $bar.toggleClass("hidden", !showBar);
    $reopen.toggleClass("hidden", !(count > 0 && hidden)).toggleClass("flex", count > 0 && hidden);

    $("#main")
        .toggleClass("pb-48 md:pb-32", showBar)
        .toggleClass("pb-24 md:pb-12", !showBar);
}

function compareCount() {
    return $("[data-compare-bar] [data-compare-list] form[data-compare-remove]").length;
}

$(function () {
    $(document).on("submit", "form[data-cart-add]", function (event) {
        event.preventDefault();

        const $form = $(this);
        const $button = submitterOf(event, $form);

        if ($button.prop("disabled")) return;

        setBusy($button, true);

        request($form.attr("action"), "POST", formDataWith(this, $button))
            .then((data) => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                setBusy($button, false);
                renderCartCount(data.count);
                window.notify("success", data.message);
            })
            .catch((error) => {
                setBusy($button, false);
                window.notify("error", error.message);
            });
    });

    $(document).on("click", "[data-qty-step]", function () {
        const $button = $(this);
        const $input = $button.closest("[data-cart-line]").find("[data-qty-input]");
        const step = parseInt($button.data("qty-step"), 10);
        const min = parseInt($input.attr("min"), 10) || 1;
        const max = parseInt($input.attr("max"), 10) || 20;
        const current = parseInt($input.val(), 10) || min;
        const next = Math.min(max, Math.max(min, current + step));

        if (next === current) return;

        $input.val(next).trigger("change");
    });

    $(document).on("submit", "form[data-cart-qty]", function (event) {
        event.preventDefault();
    });

    let pending = null;

    $(document).on("change", "[data-qty-input]", function () {
        const $input = $(this);
        const $line = $input.closest("[data-cart-line]");
        const min = parseInt($input.attr("min"), 10) || 1;
        const max = parseInt($input.attr("max"), 10) || 20;
        const quantity = Math.min(max, Math.max(min, parseInt($input.val(), 10) || min));

        $input.val(quantity);
        $line.addClass("opacity-60 pointer-events-none");

        clearTimeout(pending);
        pending = setTimeout(() => {
            request($line.data("cart-update"), "PATCH", { quantity })
                .then((data) => {
                    $line.removeClass("opacity-60 pointer-events-none");
                    renderCartCount(data.count);
                    renderTotals(data);
                })
                .catch((error) => {
                    $line.removeClass("opacity-60 pointer-events-none");
                    window.notify("error", error.message);
                });
        }, 350);
    });

    $(document).on("submit", "form[data-cart-remove]", function (event) {
        event.preventDefault();

        const $form = $(this);
        const $line = $form.closest("[data-cart-line]");

        $line.addClass("opacity-40 pointer-events-none");

        request($form.attr("action"), "DELETE")
            .then((data) => {
                renderCartCount(data.count);

                if (data.totals?.is_empty) {
                    window.location.reload();
                    return;
                }

                $line.slideUp(200, function () {
                    $(this).remove();
                    renderTotals(data);
                });

                window.notify("success", data.message);
            })
            .catch((error) => {
                $line.removeClass("opacity-40 pointer-events-none");
                window.notify("error", error.message);
            });
    });

    $(document).on("submit", "form[data-wishlist-toggle]", function (event) {
        event.preventDefault();

        const $form = $(this);
        const $button = $form.find("button").first();

        if ($button.prop("disabled")) return;

        $button.prop("disabled", true);

        request($form.attr("action"), "POST")
            .then((data) => {
                const on = ($button.data("wishlist-on") || "").split(" ").filter(Boolean);
                const off = ($button.data("wishlist-off") || "").split(" ").filter(Boolean);

                $button.prop("disabled", false).attr("aria-pressed", data.wishlisted ? "true" : "false");
                $button.attr("title", data.label);
                $button.find("[data-wishlist-label]").text(data.label);
                $button
                    .find("i")
                    .toggleClass("fa-solid", data.wishlisted)
                    .toggleClass("fa-regular", !data.wishlisted);

                on.forEach((cls) => $button.toggleClass(cls, data.wishlisted));
                off.forEach((cls) => $button.toggleClass(cls, !data.wishlisted));

                window.notify("success", data.message);
            })
            .catch((error) => {
                $button.prop("disabled", false);
                window.notify("error", error.message);
            });
    });

    $(document).on("submit", "form[data-compare-toggle]", function (event) {
        event.preventDefault();

        const $form = $(this);
        const $button = $form.find("button").first();

        if ($button.prop("disabled")) return;

        $button.prop("disabled", true);

        request($form.attr("action"), "POST")
            .then((data) => {
                $button.prop("disabled", false).attr("aria-pressed", data.compared ? "true" : "false");
                $button
                    .toggleClass("bg-primary text-white border-primary", data.compared)
                    .toggleClass("bg-white/95 text-muted-foreground border-border", !data.compared);
                $button.find("[data-compare-label]").text(data.message);

                renderCompareBar(data, { reveal: data.compared });
                window.notify("success", data.message);
            })
            .catch((error) => {
                $button.prop("disabled", false);
                window.notify("error", error.message);
            });
    });

    $(document).on("submit", "form[data-compare-remove]", function (event) {
        event.preventDefault();

        const $form = $(this);
        const id = $form.data("product");

        request($form.attr("action"), "DELETE")
            .then((data) => {
                renderCompareBar(data);

                if (id) {
                    $('form[data-compare-toggle][data-product="' + id + '"] button')
                        .attr("aria-pressed", "false")
                        .removeClass("bg-primary bg-primary/5 text-white text-primary border-primary")
                        .addClass("bg-white/95 text-muted-foreground border-border");
                }
            })
            .catch((error) => window.notify("error", error.message));
    });

    $(document).on("click", "[data-compare-dismiss]", function () {
        setCompareDismissed(true);
        applyCompareVisibility(compareCount());
    });

    $(document).on("click", "[data-compare-reopen]", function () {
        setCompareDismissed(false);
        applyCompareVisibility(compareCount());
    });

    applyCompareVisibility(compareCount());

    $(document).on("submit", "form[data-submit-once]", function () {
        const $button = $(this).find("button[type=submit]");

        if ($button.prop("disabled")) return false;

        $button.prop("disabled", true).addClass("opacity-70 cursor-wait");
        $button.find("[data-submit-label]").text($button.data("busy-label") || "");
        $button.find("i").attr("class", "fa-solid fa-circle-notch fa-spin");
    });

    let suggestTimer = null;

    $(document).on("input", "[data-search-input]", function () {
        const $input = $(this);
        const $panel = $input.closest("[data-search-box]").find("[data-search-panel]");
        const keyword = $input.val().trim();

        clearTimeout(suggestTimer);

        if (keyword.length < 2) {
            $panel.addClass("hidden").empty();
            return;
        }

        suggestTimer = setTimeout(() => {
            fetch($input.data("search-input") + "?keyword=" + encodeURIComponent(keyword), {
                headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            })
                .then((response) => response.json())
                .then((data) => renderSuggestions($panel, data.items || [], keyword, $input));
        }, 250);
    });

    function renderSuggestions($panel, items, keyword, $input) {
        $panel.empty();

        if (!items.length) {
            $panel.append(
                $("<p>", {
                    class: "px-4 py-5 text-sm text-muted-foreground text-center",
                    text: labels().searchEmpty || "",
                }),
            );
        }

        items.forEach((item) => {
            $panel.append(
                $("<a>", { href: item.url, class: "flex items-center gap-3 px-3 py-2.5 hover:bg-muted transition-colors" }).append(
                    $("<span>", {
                        class: "w-11 h-11 shrink-0 bg-white border border-border rounded-lg overflow-hidden flex items-center justify-center",
                    }).append(
                        item.thumbnail
                            ? $("<img>", { src: item.thumbnail, alt: item.name, class: "w-full h-full object-contain p-1" })
                            : $("<i>", { class: "fa-solid fa-box-open text-muted-foreground/30" }),
                    ),
                    $("<span>", { class: "min-w-0 flex-1" }).append(
                        $("<span>", { class: "block text-sm text-foreground line-clamp-1", text: item.name }),
                        $("<span>", { class: "block text-sm price-main", text: item.price }),
                    ),
                ),
            );
        });

        $panel.append(
            $("<a>", {
                href: $input.data("search-all") + "?keyword=" + encodeURIComponent(keyword),
                class: "flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-primary border-t border-border hover:bg-primary/5 transition-colors",
                text: labels().searchAll || "",
            }),
        );

        $panel.removeClass("hidden");
    }

    $(document).on("click", function (event) {
        if (!$(event.target).closest("[data-search-box]").length) {
            $("[data-search-panel]").addClass("hidden");
        }
    });

    $(document).on("keydown", "[data-search-input]", function (event) {
        if (event.key === "Escape") {
            $(this).closest("[data-search-box]").find("[data-search-panel]").addClass("hidden");
        }
    });
});

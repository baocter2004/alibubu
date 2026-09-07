import Chart from "chart.js/auto";

function numberFormatter(locale, options = {}) {
    return new Intl.NumberFormat(locale, options);
}

function createChart(canvas, config) {
    if (!canvas) return;

    try {
        return new Chart(canvas, config);
    } catch (error) {
        console.error("Dashboard chart failed to render", error);
        const fallback = document.createElement("div");
        fallback.className = "flex h-full items-center justify-center text-center text-sm text-slate-500";
        fallback.textContent = document.querySelector("[data-admin-dashboard]")?.dataset.chartError || "Chart unavailable";
        canvas.replaceWith(fallback);
    }
}

const centerTextPlugin = {
    id: "dashboardCenterText",
    afterDraw(chart, _, options) {
        if (!options?.value && options?.value !== 0) return;

        const { ctx, chartArea } = chart;

        if (!ctx || !chartArea) return;

        const x = (chartArea.left + chartArea.right) / 2;
        const y = (chartArea.top + chartArea.bottom) / 2;
        ctx.save();
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillStyle = "#173B67";
        ctx.font = "700 24px 'Be Vietnam Pro', sans-serif";
        ctx.fillText(formatNumberForPlugin(options.value, options.locale), x, y - 7);
        ctx.fillStyle = "#64748B";
        ctx.font = "500 11px 'Be Vietnam Pro', sans-serif";
        ctx.fillText(options.label || "", x, y + 15);
        ctx.restore();
    },
};

function formatNumberForPlugin(value, locale) {
    return new Intl.NumberFormat(locale || "vi-VN").format(Number(value || 0));
}

$(function () {
    const root = document.querySelector("[data-admin-dashboard]");
    if (!root) return;

    const data = JSON.parse(root.dataset.chartData || "{}");
    data.labels = Array.isArray(data.labels) ? data.labels : [];
    data.revenue = Array.isArray(data.revenue) ? data.revenue : [];
    data.cumulativeRevenue = Array.isArray(data.cumulativeRevenue) ? data.cumulativeRevenue : [];
    data.orders = Array.isArray(data.orders) ? data.orders : [];
    data.status = data.status || { labels: [], data: [] };
    data.payment = data.payment || { labels: [], data: [] };
    data.inventory = data.inventory || { labels: [], data: [] };
    data.topProducts = data.topProducts || { labels: [], data: [], quantities: [] };
    data.config = data.config || {};
    const locale = root.dataset.locale || "vi-VN";
    const currency = root.dataset.currency || "VND";
    const formatNumber = numberFormatter(locale);
    const formatCurrency = numberFormatter(locale, {
        style: "currency",
        currency,
        maximumFractionDigits: 0,
    });
    const formatMoney = (value) => formatCurrency.format(Number(value || 0));
    const percentage = (value, values) => {
        const total = values.reduce((sum, item) => sum + Number(item || 0), 0);
        return total > 0 ? ` (${formatNumber.format((Number(value || 0) / total) * 100)}%)` : "";
    };
    const common = {
        responsive: true,
        maintainAspectRatio: false,
        resizeDelay: 100,
        animation: { duration: 350, easing: "easeOutQuart" },
        interaction: { mode: "index", intersect: false },
        layout: { padding: { top: 18, right: 18, bottom: 0, left: 6 } },
        normalized: true,
        spanGaps: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                padding: 10,
                callbacks: {
                    label: (context) => `${context.dataset.label}: ${context.dataset.money ? formatMoney(context.parsed.y) : formatNumber.format(context.parsed.y ?? context.parsed)}`,
                },
            },
        },
    };
    const compactMoney = (value) => {
        const n = Number(value || 0);

        if (n >= 1000000000) return formatNumber.format(Math.round(n / 100000000) / 10) + " tỷ";
        if (n >= 1000000) return formatNumber.format(Math.round(n / 100000) / 10) + " tr";
        if (n >= 1000) return formatNumber.format(Math.round(n / 1000)) + "k";

        return formatNumber.format(n);
    };
    const axis = (config = {}, money = false) => ({
        beginAtZero: true,
        min: Number(config.min ?? 0),
        max: Number(config.max ?? 4),
        grace: 0,
        ticks: {
            padding: 8,
            stepSize: Number(config.step ?? 1),
            precision: Number.isInteger(Number(config.step ?? 1)) ? 0 : 2,
            callback: (value) => money ? compactMoney(value) : formatNumber.format(value),
        },
        grid: { color: "rgba(148, 163, 184, 0.18)" },
    });
    const lineScales = (config, money) => ({
        x: {
            grid: { display: false },
            ticks: { maxTicksLimit: 8, maxRotation: 0, autoSkip: true },
        },
        y: axis(config, money),
    });
    const denseSeries = data.labels.length > 14;
    const lineElements = {
        pointRadius: denseSeries ? 0 : 3,
        pointStyle: "circle",
        pointHoverRadius: 7,
        pointHitRadius: 18,
        pointBorderColor: "#FFFFFF",
        pointBorderWidth: 2,
        pointHoverBackgroundColor: "#FFFFFF",
        pointHoverBorderColor: "#173B67",
        pointHoverBorderWidth: 3,
        borderWidth: 3,
        borderJoinStyle: "round",
        borderCapStyle: "round",
        tension: 0.3,
        cubicInterpolationMode: "monotone",
        fill: false,
        showLine: true,
        clip: 8,
    };

    createChart(document.getElementById("revenue-chart"), {
        type: "line",
        data: {
            labels: data.labels,
            datasets: [{
                label: root.dataset.revenueLabel,
                data: data.revenue,
                borderColor: "#173B67",
                backgroundColor: "rgba(23, 59, 103, 0.12)",
                pointBackgroundColor: "#173B67",
                money: true,
                ...lineElements,
            }],
        },
        options: {
            ...common,
            scales: lineScales(data.config.revenue, true),
        },
    });

    createChart(document.getElementById("orders-chart"), {
        type: "line",
        data: {
            labels: data.labels,
            datasets: [{
                label: root.dataset.ordersLabel,
                data: data.orders,
                borderColor: "#F4B740",
                backgroundColor: "rgba(244, 183, 64, 0.16)",
                pointBackgroundColor: "#F4B740",
                ...lineElements,
            }],
        },
        options: {
            ...common,
            scales: lineScales(data.config.orders, false),
        },
    });

    createChart(document.getElementById("sales-area-chart"), {
        type: "line",
        data: {
            labels: data.labels,
            datasets: [{
                label: root.dataset.areaLabel,
                data: data.cumulativeRevenue,
                borderColor: "#F4B740",
                backgroundColor: "rgba(244, 183, 64, 0.2)",
                pointBackgroundColor: "#F4B740",
                money: true,
                ...lineElements,
                fill: true,
            }],
        },
        options: {
            ...common,
            scales: lineScales(data.config.cumulative_revenue, true),
        },
    });

    createChart(document.getElementById("mixed-performance-chart"), {
        type: "bar",
        data: {
            labels: data.labels,
            datasets: [
                {
                    type: "bar",
                    label: root.dataset.revenueLabel,
                    data: data.revenue,
                    yAxisID: "revenue",
                    money: true,
                    backgroundColor: "rgba(23, 59, 103, 0.85)",
                    borderColor: "#173B67",
                    borderWidth: 0,
                    borderRadius: 3,
                    borderSkipped: "bottom",
                    categoryPercentage: 0.9,
                    barPercentage: 0.9,
                    maxBarThickness: 34,
                },
                {
                    type: "line",
                    label: root.dataset.ordersLabel,
                    data: data.orders,
                    yAxisID: "orders",
                    borderColor: "#F4B740",
                    backgroundColor: "transparent",
                    pointBackgroundColor: "#F4B740",
                    pointHoverBorderColor: "#F4B740",
                    ...lineElements,
                    fill: false,
                },
            ],
        },
        options: {
            ...common,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 8, maxRotation: 0, autoSkip: true },
                },
                revenue: {
                    ...axis(data.config.revenue, true),
                    position: "left",
                },
                orders: {
                    ...axis(data.config.orders),
                    position: "right",
                    grid: { drawOnChartArea: false, color: "rgba(148, 163, 184, 0.18)" },
                },
            },
            plugins: {
                ...common.plugins,
                tooltip: {
                    callbacks: {
                        label: (context) => context.dataset.money
                            ? `${root.dataset.revenueLabel}: ${formatMoney(context.parsed.y)}`
                            : `${root.dataset.ordersLabel}: ${formatNumber.format(context.parsed.y)}`,
                    },
                },
            },
        },
    });

    createChart(document.getElementById("status-chart"), {
        type: "doughnut",
        data: {
            labels: data.status.labels,
            datasets: [{
                data: data.status.data,
                backgroundColor: ["#F59E0B", "#38BDF8", "#818CF8", "#22C55E", "#EF4444"],
                borderColor: "#FFFFFF",
                borderWidth: 3,
                hoverOffset: 10,
                spacing: 2,
                radius: "92%",
            }],
        },
        options: {
            ...common,
            cutout: "66%",
            plugins: {
                ...common.plugins,
                legend: {
                    display: true,
                    position: "bottom",
                    labels: { usePointStyle: true, boxWidth: 8, padding: 9, font: { size: 10 } },
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.label}: ${formatNumber.format(context.parsed)}${percentage(context.parsed, data.status.data)}`,
                    },
                },
                dashboardCenterText: {
                    value: data.status.data.reduce((sum, item) => sum + Number(item || 0), 0),
                    label: root.dataset.statusTotalLabel || "Total",
                    locale,
                },
            },
        },
        plugins: [centerTextPlugin],
    });

    createChart(document.getElementById("top-products-chart"), {
        type: "bar",
        data: {
            labels: data.topProducts.labels,
            datasets: [{
                label: root.dataset.revenueLabel,
                data: data.topProducts.data,
                money: true,
                backgroundColor: "#F4B740",
                borderRadius: 4,
                borderSkipped: "start",
                barPercentage: 0.82,
                categoryPercentage: 0.86,
                maxBarThickness: 26,
            }],
        },
        options: {
            ...common,
            indexAxis: "y",
            scales: {
                x: axis(data.config.top_products, true),
                y: { grid: { display: false }, ticks: { autoSkip: false } },
            },
            plugins: {
                ...common.plugins,
                tooltip: {
                    callbacks: {
                        label: (context) => `${root.dataset.revenueLabel}: ${formatMoney(context.parsed.x)}`,
                        afterLabel: (context) => `${root.dataset.quantityLabel}: ${formatNumber.format(data.topProducts.quantities[context.dataIndex] || 0)}`,
                    },
                },
            },
        },
    });

    createChart(document.getElementById("inventory-chart"), {
        type: "doughnut",
        data: {
            labels: data.inventory.labels,
            datasets: [{
                data: data.inventory.data,
                backgroundColor: ["#22C55E", "#F4B740", "#EF4444"],
                borderColor: "#FFFFFF",
                borderWidth: 3,
                hoverOffset: 10,
                spacing: 2,
                radius: "92%",
            }],
        },
        options: {
            ...common,
            cutout: "64%",
            plugins: {
                ...common.plugins,
                legend: {
                    display: true,
                    position: "bottom",
                    labels: { usePointStyle: true, boxWidth: 8, padding: 9, font: { size: 10 } },
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.label}: ${formatNumber.format(context.parsed)}${percentage(context.parsed, data.inventory.data)}`,
                    },
                },
                dashboardCenterText: {
                    value: data.inventory.data.reduce((sum, item) => sum + Number(item || 0), 0),
                    label: root.dataset.inventoryTotalLabel || "Total",
                    locale,
                },
            },
        },
        plugins: [centerTextPlugin],
    });

    createChart(document.getElementById("payment-chart"), {
        type: "pie",
        data: {
            labels: data.payment.labels,
            datasets: [{
                data: data.payment.data,
                backgroundColor: ["#38BDF8", "#818CF8"],
                borderColor: "#FFFFFF",
                borderWidth: 3,
                hoverOffset: 10,
                spacing: 2,
                radius: "92%",
            }],
        },
        options: {
            ...common,
            plugins: {
                ...common.plugins,
                legend: {
                    display: true,
                    position: "bottom",
                    labels: { usePointStyle: true, boxWidth: 8, padding: 9, font: { size: 10 } },
                },
                tooltip: {
                    callbacks: {
                        label: (context) => `${context.label}: ${formatNumber.format(context.parsed)}${percentage(context.parsed, data.payment.data)}`,
                    },
                },
            },
        },
    });
});

document.addEventListener('alpine:init', () => {
    Alpine.data('emailListStatisticsChart', () => ({
        chartData: {},
        chart: null,
        zoomed: false,
        resetZoom() {
            if (!this.chart) {
                return;
            }

            this.chart.resetZoom();
            this.zoomed = false;
        },
        renderChart: function (chartData) {
            const chart = document.getElementById('chart');

            this.chartData = chartData;

            let c = false;

            Chart.helpers.each(Chart.instances, function (instance) {
                if (instance.canvas.id === 'chart') {
                    c = instance;
                }
            });

            if (c) {
                c.destroy();
            }

            this.chart = new Chart(chart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: this.chartData.labels,
                    datasets: [
                        {
                            label: 'Subscribes',
                            backgroundColor: '#C4D5FF',
                            hoverBackgroundColor: '#A3BDFF',
                            borderRadius: 5,
                            data: this.chartData.subscribes,
                            stack: 'stack0',
                            order: 2,
                        },
                        {
                            label: 'Unsubscribes',
                            backgroundColor: '#F9D5D3',
                            hoverBackgroundColor: '#ED5E58',
                            borderRadius: 5,
                            data: this.chartData.unsubscribes.map((val) => (val ? -val : 0)),
                            stack: 'stack0',
                            order: 1,
                        },
                        {
                            label: 'Subscribers',
                            type: 'line',
                            borderColor: '#3461D6',
                            pointBackgroundColor: '#3461D6',
                            pointBorderColor: '#3461D6',
                            data: this.chartData.subscribers,
                            yAxisID: 'y1',
                            order: 0,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    barPercentage: 0.75,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        zoom: {
                            pan: {
                                enabled: true,
                                mode: 'x',
                                modifierKey: 'ctrl',
                            },
                            zoom: {
                                drag: {
                                    enabled: true,
                                },
                                mode: 'x',
                                onZoomComplete: () => (this.zoomed = true),
                            },
                        },
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: 'rgba(37, 42, 63, 1)',
                            titleSpacing: 4,
                            bodySpacing: 8,
                            padding: 20,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    let value = context.raw;

                                    if (typeof value === 'number') {
                                        value = Math.abs(value);
                                    }

                                    return `${label}: ${value}`;
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: 'rgba(100, 116, 139, 1)',
                                precision: 0,
                            },
                            grid: {
                                display: false,
                            },
                        },
                        y1: {
                            ticks: {
                                color: 'rgba(100, 116, 139, 1)',
                                precision: 0,
                            },
                            position: 'right',
                            beginAtZero: false,
                            grid: {
                                display: false,
                            },
                        },
                        x: {
                            ticks: {
                                autoSkip: true,
                                maxRotation: 0,
                                color: 'rgba(100, 116, 139, 1)',
                                callback: function (value, index, ticks) {
                                    return chartData.labels[index].substring(3);
                                },
                            },
                            grid: {
                                borderColor: 'rgba(100, 116, 139, .2)',
                                borderDash: [5, 5],
                                zeroLineColor: 'rgba(100, 116, 139, .2)',
                                zeroLineBorderDash: [5, 5],
                            },
                        },
                    },
                },
            });
        },
    }));
});

document.addEventListener('alpine:init', () => {
    Alpine.data('campaignStatisticsChart', () => ({
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

            const lineOptions = {
                fill: false,
                cubicInterpolationMode: 'monotone',
                pointRadius: 1,
                pointHoverRadius: 5,
            };

            this.chart = new Chart(chart.getContext('2d'), {
                type: 'line',
                data: {
                    labels: this.chartData.labels,
                    datasets: [
                        {
                            ...lineOptions,
                            label: 'Opens',
                            borderColor: '#3461D6',
                            backgroundColor: '#3461D6',
                            pointBackgroundColor: '#3461D6',
                            pointBorderColor: '#3461D6',
                            data: this.chartData.opens,
                        },
                        {
                            ...lineOptions,
                            label: 'Clicks',
                            borderColor: '#0FBA9E',
                            backgroundColor: '#0FBA9E',
                            pointBackgroundColor: '#0FBA9E',
                            pointBorderColor: '#0FBA9E',
                            data: this.chartData.clicks,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
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
                            display: true,
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                            },
                        },
                        tooltip: {
                            backgroundColor: 'rgba(37, 42, 63, 1)',
                            titleSpacing: 4,
                            bodySpacing: 8,
                            padding: 20,
                            displayColors: false,
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
                        x: {
                            ticks: {
                                autoSkip: true,
                                maxRotation: 0,
                                color: 'rgba(100, 116, 139, 1)',
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

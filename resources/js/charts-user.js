// App Usage Statistics Chart
const usageChartElement = document.querySelector("#usage-chart");

if (usageChartElement) {
    const usageChartOptions = {
        chart: {
            type: 'area',
            height: 350,
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        colors: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
        series: [
            {
                name: 'FreshMart',
                data: [120, 150, 170, 160, 180, 200, 210]
            },
            {
                name: 'StyleHub',
                data: [80, 90, 85, 95, 100, 110, 105]
            },
            {
                name: 'FitTrack',
                data: [60, 70, 75, 80, 85, 90, 95]
            },
            {
                name: 'TechNews',
                data: [40, 45, 50, 48, 52, 55, 58]
            }
        ],
        stroke: {
            curve: 'smooth',
            width: 2
        },
        fill: {
            type: 'gradient',
            gradient: {
                opacityFrom: 0.6,
                opacityTo: 0.1,
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            fontSize: '12px',
            fontWeight: 500,
            labels: {
                colors: '#6B7280'
            },
            markers: {
                width: 8,
                height: 8,
                radius: 12
            }
        },
        xaxis: {
            categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                }
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                },
                formatter: function (val) {
                    return Math.floor(val);
                }
            },
            title: {
                text: 'Active Users',
                style: {
                    color: '#6B7280',
                    fontSize: '12px',
                    fontWeight: 500
                }
            }
        },
        grid: {
            borderColor: '#E5E7EB',
            strokeDashArray: 4,
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return val + " users"
                }
            }
        },
        responsive: [{
            breakpoint: 640,
            options: {
                chart: {
                    height: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    const usageChart = new ApexCharts(usageChartElement, usageChartOptions);
    usageChart.render();

    // Filter buttons for usage chart
    const filterWeek = document.getElementById('usage-filter-week');
    const filterMonth = document.getElementById('usage-filter-month');

    if (filterWeek && filterMonth) {
        filterWeek.addEventListener('click', function() {
            filterWeek.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
            filterWeek.classList.add('text-white', 'bg-blue-700');

            filterMonth.classList.remove('text-white', 'bg-blue-700');
            filterMonth.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

            // Update chart with weekly data
            usageChart.updateSeries([
                {
                    name: 'FreshMart',
                    data: [120, 150, 170, 160, 180, 200, 210]
                },
                {
                    name: 'StyleHub',
                    data: [80, 90, 85, 95, 100, 110, 105]
                },
                {
                    name: 'FitTrack',
                    data: [60, 70, 75, 80, 85, 90, 95]
                },
                {
                    name: 'TechNews',
                    data: [40, 45, 50, 48, 52, 55, 58]
                }
            ]);
            usageChart.updateOptions({
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                }
            });
        });

        filterMonth.addEventListener('click', function() {
            filterMonth.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
            filterMonth.classList.add('text-white', 'bg-blue-700');

            filterWeek.classList.remove('text-white', 'bg-blue-700');
            filterWeek.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

            // Update chart with monthly data (30 days)
            usageChart.updateSeries([
                {
                    name: 'FreshMart',
                    data: [100, 110, 120, 130, 125, 140, 150, 145, 155, 160, 170, 165, 175, 180, 185, 190, 195, 200, 205, 210, 215, 220, 225, 230, 235, 240, 245, 250, 255, 260]
                },
                {
                    name: 'StyleHub',
                    data: [70, 75, 80, 78, 85, 82, 90, 88, 92, 95, 98, 100, 102, 105, 103, 108, 110, 112, 115, 118, 120, 122, 125, 123, 128, 130, 132, 135, 138, 140]
                },
                {
                    name: 'FitTrack',
                    data: [50, 55, 52, 58, 60, 62, 65, 68, 70, 72, 75, 78, 80, 82, 85, 83, 88, 90, 92, 95, 93, 98, 100, 102, 105, 108, 110, 112, 115, 118]
                },
                {
                    name: 'TechNews',
                    data: [35, 38, 40, 42, 45, 43, 48, 50, 52, 55, 53, 58, 60, 62, 65, 63, 68, 70, 72, 75, 78, 80, 82, 85, 83, 88, 90, 92, 95, 98]
                }
            ]);
            usageChart.updateOptions({
                xaxis: {
                    categories: Array.from({length: 30}, (_, i) => i + 1)
                }
            });
        });
    }
}

// Purchase Statistics Chart
const chartElement = document.querySelector("#purchase-chart");

if (chartElement) {
    const chartOptions = {
        chart: {
            type: 'line',
            height: 320,
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
        },
        colors: ['#3B82F6'],
        series: [{
            name: 'Purchases',
            data: [0, 0, 0, 0, 15, 58, 14, 14, 14, 28, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        }],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: Array.from({length: 31}, (_, i) => i + 1),
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                }
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#6B7280',
                    fontSize: '12px'
                }
            }
        },
        grid: {
            borderColor: '#E5E7EB',
            strokeDashArray: 4,
        },
        markers: {
            size: 4,
            colors: ['#3B82F6'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 6
            }
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return val + " purchases"
                }
            }
        },
        responsive: [{
            breakpoint: 640,
            options: {
                chart: {
                    height: 300
                }
            }
        }]
    };

    const chart = new ApexCharts(chartElement, chartOptions);
    chart.render();

    // Filter buttons
    const filterMonth = document.getElementById('filter-month');
    const filterYear = document.getElementById('filter-year');

    if (filterMonth && filterYear) {
        filterMonth.addEventListener('click', function() {
            filterMonth.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
            filterMonth.classList.add('text-white', 'bg-blue-700');

            filterYear.classList.remove('text-white', 'bg-blue-700');
            filterYear.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

            // Update chart with monthly data
            chart.updateSeries([{
                data: [0, 0, 0, 0, 15, 58, 14, 14, 14, 28, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
            }]);
            chart.updateOptions({
                xaxis: {
                    categories: Array.from({length: 31}, (_, i) => i + 1)
                }
            });
        });

        filterYear.addEventListener('click', function() {
            filterYear.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
            filterYear.classList.add('text-white', 'bg-blue-700');

            filterMonth.classList.remove('text-white', 'bg-blue-700');
            filterMonth.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

            // Update chart with yearly data
            chart.updateSeries([{
                data: [120, 200, 150, 220, 190, 300, 250, 280, 310, 350, 320, 380]
            }]);
            chart.updateOptions({
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                }
            });
        });
    }
}

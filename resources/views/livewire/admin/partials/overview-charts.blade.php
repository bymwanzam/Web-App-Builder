{{-- Revenue Trends Chart (Full Width) --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 mb-6">
    <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white mb-4">Revenue Trends (12 Months)</h5>
    <div wire:ignore>
        <div id="revenueChart" style="min-height: 300px;"></div>
    </div>
</div>

{{-- Build Activity Chart (Full Width) --}}
<div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 mb-6">
    <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white mb-4">Build Activity (Last 30 Days)</h5>
    <div wire:ignore>
        <div id="buildActivityChart" style="min-height: 300px;"></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', function () {
    initializeCharts();
});

document.addEventListener('livewire:init', function () {
    initializeCharts();

    Livewire.on('dateRangeChanged', (event) => {
        setTimeout(() => {
            initializeCharts();
        }, 100);
    });

    Livewire.on('statsRefreshed', () => {
        setTimeout(() => {
            initializeCharts();
        }, 100);
    });
});

function initializeCharts() {
    // Check if ApexCharts is loaded
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts not loaded');
        return;
    }

    // Get chart data from stats (passed from Livewire)
    const revenueData = @json($stats['revenue_chart_data'] ?? null) || {labels: [], data: []};
    const buildActivityData = @json($stats['build_activity_chart_data'] ?? null) || {labels: [], completed: [], failed: [], pending: []};

    // Determine if dark mode is active
    const isDarkMode = document.documentElement.classList.contains('dark');

    // Common chart options for dark mode
    const darkModeOptions = {
        theme: {
            mode: isDarkMode ? 'dark' : 'light'
        },
        chart: {
            background: 'transparent',
            toolbar: {
                show: false
            }
        },
        grid: {
            borderColor: isDarkMode ? '#374151' : '#e5e7eb'
        }
    };

    // Revenue Trends Chart
    const revenueChartElement = document.querySelector('#revenueChart');
    if (revenueChartElement) {
        // Destroy existing chart if it exists
        if (revenueChartElement._apexChart) {
            revenueChartElement._apexChart.destroy();
        }

        const revenueChart = new ApexCharts(revenueChartElement, {
            ...darkModeOptions,
            series: [{
                name: 'Revenue',
                data: revenueData.data
            }],
            chart: {
                ...darkModeOptions.chart,
                type: 'bar',
                height: 300,
                fontFamily: 'Inter, sans-serif',
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '60%',
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: revenueData.labels,
                labels: {
                    style: {
                        colors: isDarkMode ? '#9CA3AF' : '#6B7280'
                    }
                }
            },
            yaxis: {
                title: {
                    text: '$ (USD)',
                    style: {
                        color: isDarkMode ? '#9CA3AF' : '#6B7280'
                    }
                },
                labels: {
                    formatter: function (val) {
                        if (val === null || val === undefined || isNaN(val)) {
                            return '$0.00';
                        }
                        return '$' + Number(val).toFixed(2);
                    },
                    style: {
                        colors: isDarkMode ? '#9CA3AF' : '#6B7280'
                    }
                }
            },
            fill: {
                colors: ['#8B5CF6'],
                opacity: 1
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light',
                shared: false,
                intersect: true,
                x: {
                    show: true
                },
                y: {
                    formatter: function (val) {
                        if (val === null || val === undefined || isNaN(val)) {
                            return '$0.00';
                        }
                        return '$' + Number(val).toFixed(2);
                    },
                    title: {
                        formatter: function (seriesName) {
                            return 'Revenue';
                        }
                    }
                }
            }
        });

        revenueChart.render();
        revenueChartElement._apexChart = revenueChart;
    }

    // Build Activity Chart
    const buildActivityChartElement = document.querySelector('#buildActivityChart');
    if (buildActivityChartElement) {
        // Destroy existing chart if it exists
        if (buildActivityChartElement._apexChart) {
            buildActivityChartElement._apexChart.destroy();
        }

        const buildActivityChart = new ApexCharts(buildActivityChartElement, {
            ...darkModeOptions,
            series: [
                {
                    name: 'Completed',
                    data: buildActivityData.completed
                },
                {
                    name: 'Failed',
                    data: buildActivityData.failed
                },
                {
                    name: 'Pending',
                    data: buildActivityData.pending
                }
            ],
            chart: {
                ...darkModeOptions.chart,
                type: 'bar',
                height: 300,
                fontFamily: 'Inter, sans-serif',
                stacked: true,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4,
                    columnWidth: '70%',
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: buildActivityData.labels,
                labels: {
                    rotate: -45,
                    rotateAlways: false,
                    style: {
                        colors: isDarkMode ? '#9CA3AF' : '#6B7280',
                        fontSize: '10px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Builds',
                    style: {
                        color: isDarkMode ? '#9CA3AF' : '#6B7280'
                    }
                },
                labels: {
                    style: {
                        colors: isDarkMode ? '#9CA3AF' : '#6B7280'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                labels: {
                    colors: isDarkMode ? '#9CA3AF' : '#6B7280'
                }
            },
            colors: ['#10B981', '#EF4444', '#F59E0B'],
            fill: {
                opacity: 1
            },
            tooltip: {
                theme: isDarkMode ? 'dark' : 'light',
                shared: true,
                intersect: false,
                x: {
                    show: true
                },
                y: {
                    formatter: function (val) {
                        if (val === null || val === undefined || isNaN(val)) {
                            return '0 builds';
                        }
                        return Number(val) + ' builds';
                    }
                }
            }
        });

        buildActivityChart.render();
        buildActivityChartElement._apexChart = buildActivityChart;
    }
}

// Re-initialize charts when dark mode changes
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        setTimeout(() => {
            initializeCharts();
        }, 100);
    });
}
</script>
@endpush

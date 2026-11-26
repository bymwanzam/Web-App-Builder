// Admin Dashboard Charts using ApexCharts

// Check if we're on a page with admin charts
const revenueChartElement = document.getElementById('revenue-chart');
const usersChartElement = document.getElementById('users-chart');

if (revenueChartElement && usersChartElement) {

  // Revenue Chart Data
  const revenueMonthData = {
    categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    series: [8500, 12300, 15200, 11900]
  };

  const revenueYearData = {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    series: [32000, 38000, 35000, 42000, 45000, 48000, 52000, 49000, 51000, 48200, 47000, 50000]
  };

  // User Growth Chart Data
  const usersMonthData = {
    categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    series: [2150, 2480, 2890, 3247]
  };

  const usersYearData = {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    series: [8500, 9200, 9800, 10300, 10900, 11200, 11700, 12100, 12400, 12700, 12800, 12847]
  };

  // Check if dark mode is enabled
  const isDarkMode = document.documentElement.classList.contains('dark');

  // Revenue Chart Configuration
  const revenueChartOptions = {
    chart: {
      type: 'area',
      height: 288,
      fontFamily: 'Inter, sans-serif',
      toolbar: {
        show: false
      },
      animations: {
        enabled: true,
        speed: 800
      }
    },
    series: [{
      name: 'Revenue',
      data: revenueMonthData.series,
      color: '#8b5cf6'
    }],
    xaxis: {
      categories: revenueMonthData.categories,
      labels: {
        style: {
          colors: isDarkMode ? '#9ca3af' : '#6b7280',
          fontSize: '12px'
        }
      },
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      }
    },
    yaxis: {
      labels: {
        style: {
          colors: isDarkMode ? '#9ca3af' : '#6b7280',
          fontSize: '12px'
        },
        formatter: function(value) {
          return '$' + (value / 1000).toFixed(0) + 'K';
        }
      }
    },
    grid: {
      borderColor: isDarkMode ? '#374151' : '#e5e7eb',
      strokeDashArray: 4
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.4,
        opacityTo: 0.1,
        stops: [0, 90, 100]
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth',
      width: 3
    },
    tooltip: {
      theme: isDarkMode ? 'dark' : 'light',
      y: {
        formatter: function(value) {
          return '$' + value.toLocaleString();
        }
      }
    }
  };

  // User Growth Chart Configuration
  const usersChartOptions = {
    chart: {
      type: 'line',
      height: 288,
      fontFamily: 'Inter, sans-serif',
      toolbar: {
        show: false
      },
      animations: {
        enabled: true,
        speed: 800
      }
    },
    series: [{
      name: 'Total Users',
      data: usersMonthData.series,
      color: '#3b82f6'
    }],
    xaxis: {
      categories: usersMonthData.categories,
      labels: {
        style: {
          colors: isDarkMode ? '#9ca3af' : '#6b7280',
          fontSize: '12px'
        }
      },
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      }
    },
    yaxis: {
      labels: {
        style: {
          colors: isDarkMode ? '#9ca3af' : '#6b7280',
          fontSize: '12px'
        },
        formatter: function(value) {
          return (value / 1000).toFixed(1) + 'K';
        }
      }
    },
    grid: {
      borderColor: isDarkMode ? '#374151' : '#e5e7eb',
      strokeDashArray: 4
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth',
      width: 3
    },
    markers: {
      size: 5,
      colors: ['#3b82f6'],
      strokeColors: '#fff',
      strokeWidth: 2,
      hover: {
        size: 7
      }
    },
    tooltip: {
      theme: isDarkMode ? 'dark' : 'light',
      y: {
        formatter: function(value) {
          return value.toLocaleString() + ' users';
        }
      }
    }
  };

  // Initialize charts
  const revenueChart = new ApexCharts(revenueChartElement, revenueChartOptions);
  const usersChart = new ApexCharts(usersChartElement, usersChartOptions);

  revenueChart.render();
  usersChart.render();

  // Revenue Chart Filter Buttons
  const revenueMonthBtn = document.getElementById('revenue-month');
  const revenueYearBtn = document.getElementById('revenue-year');

  revenueMonthBtn.addEventListener('click', () => {
    revenueMonthBtn.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
    revenueMonthBtn.classList.add('text-white', 'bg-blue-700');

    revenueYearBtn.classList.remove('text-white', 'bg-blue-700');
    revenueYearBtn.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

    revenueChart.updateOptions({
      xaxis: {
        categories: revenueMonthData.categories
      }
    });
    revenueChart.updateSeries([{
      data: revenueMonthData.series
    }]);
  });

  revenueYearBtn.addEventListener('click', () => {
    revenueYearBtn.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
    revenueYearBtn.classList.add('text-white', 'bg-blue-700');

    revenueMonthBtn.classList.remove('text-white', 'bg-blue-700');
    revenueMonthBtn.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

    revenueChart.updateOptions({
      xaxis: {
        categories: revenueYearData.categories
      }
    });
    revenueChart.updateSeries([{
      data: revenueYearData.series
    }]);
  });

  // User Growth Chart Filter Buttons
  const usersMonthBtn = document.getElementById('users-month');
  const usersYearBtn = document.getElementById('users-year');

  usersMonthBtn.addEventListener('click', () => {
    usersMonthBtn.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
    usersMonthBtn.classList.add('text-white', 'bg-blue-700');

    usersYearBtn.classList.remove('text-white', 'bg-blue-700');
    usersYearBtn.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

    usersChart.updateOptions({
      xaxis: {
        categories: usersMonthData.categories
      }
    });
    usersChart.updateSeries([{
      data: usersMonthData.series
    }]);
  });

  usersYearBtn.addEventListener('click', () => {
    usersYearBtn.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');
    usersYearBtn.classList.add('text-white', 'bg-blue-700');

    usersMonthBtn.classList.remove('text-white', 'bg-blue-700');
    usersMonthBtn.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:border-gray-600');

    usersChart.updateOptions({
      xaxis: {
        categories: usersYearData.categories
      }
    });
    usersChart.updateSeries([{
      data: usersYearData.series
    }]);
  });
}

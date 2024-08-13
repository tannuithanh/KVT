import Chart from 'chart.js/auto';
document.addEventListener('DOMContentLoaded', function() {
    var chartCanvas = document.getElementById('chartOrder');
    var ctx = chartCanvas.getContext('2d');

    var orderChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Đơn hàng đã hoàn thành',
                data: [],
                backgroundColor: '#28a745', // Màu xanh lá cho đã hoàn thành
                borderColor: '#1e7e34', // Biên xanh đậm
                borderWidth: 1
            },
            {
                label: 'Đơn hàng đang thực hiện',
                data: [],
                backgroundColor: '#ffc107', // Màu vàng cho đang thực hiện
                borderColor: '#e0a800', // Biên vàng đậm
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Tỷ lệ đơn hàng'
                }
            }
        }
    });

    window.updateChart = function(timeFrame) {
        var url = '/ordersApi?timeframe=' + timeFrame;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            var labels = data.labels;
            var completedOrdersData = data.completedOrders;
            var pendingOrdersData = data.pendingOrders;

            orderChart.data.labels = labels;
            orderChart.data.datasets[0].data = completedOrdersData;
            orderChart.data.datasets[1].data = pendingOrdersData;
            orderChart.update();
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    };

    // Initial chart update
    updateChart('week');
});

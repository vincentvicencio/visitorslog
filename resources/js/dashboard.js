document.addEventListener('DOMContentLoaded', () => {
    const pieCanvas = document.getElementById('visitorPieChart');

    if (!pieCanvas) {
        return;
    }

    const dashboardRefreshMs = 60000;

    // Reload dashboard periodically to keep metrics and charts up to date.
    setInterval(() => {
        if (!document.hidden) {
            window.location.reload();
        }
    }, dashboardRefreshMs);

    const visitorLabels = JSON.parse(pieCanvas.dataset.labels || '[]');
    const visitorData = JSON.parse(pieCanvas.dataset.values || '[]');

    if (typeof window.Chart === 'undefined') {
        return;
    }

    const ctx = pieCanvas.getContext('2d');

    new window.Chart(ctx, {
        type: 'pie',
        data: {
            labels: visitorLabels,
            datasets: [{
                data: visitorData,
                backgroundColor: [
                    '#3498db',
                    '#2ecc71',
                    '#f1c40f',
                    '#e74c3c',
                    '#9b59b6',
                    '#1abc9c'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
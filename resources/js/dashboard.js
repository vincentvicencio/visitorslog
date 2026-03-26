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
                    '#0B3D91',
                    '#145DA0',
                    '#1E81B0',
                    '#2E8BC0',
                    '#76B5E9',
                    '#B3D9FF'
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
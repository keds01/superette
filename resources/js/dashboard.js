import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    // Graphique des mouvements
    const movementsCtx = document.getElementById('movementsChart');
    if (movementsCtx) {
        new Chart(movementsCtx, {
            type: 'line',
            data: {
                labels: window.movementsChartData.labels,
                datasets: [{
                    label: 'Entrées',
                    data: window.movementsChartData.entrees,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.1
                }, {
                    label: 'Sorties',
                    data: window.movementsChartData.sorties,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Évolution des mouvements de stock'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantité'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    }

    // Graphique des catégories
    const categoriesCtx = document.getElementById('categoriesChart');
    if (categoriesCtx) {
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: window.categoriesChartData.labels,
                datasets: [{
                    data: window.categoriesChartData.data,
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribution par catégorie'
                    }
                }
            }
        });
    }

    // Fonction pour appliquer les filtres
    function applyFilters() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
}); 
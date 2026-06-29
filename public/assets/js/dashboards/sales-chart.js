document.addEventListener('DOMContentLoaded', function() {
    const salesChartElement = document.getElementById('monthly-sales-chart');
    const monthSelector = document.getElementById('month-selector');
    
    if (!salesChartElement) return;
    
    // Initialiser le graphique avec des données vides
    const ctx = salesChartElement.getContext('2d');
    let salesChart = null;
    
    // Fonction pour initialiser le graphique
    function initChart(labels, currentMonthData, previousMonthData, currentMonthLabel, previousMonthLabel) {
        if (salesChart) {
            salesChart.destroy();
        }
        
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.map(day => day.toString()),
                datasets: [
                    {
                        label: currentMonthLabel || 'Mois en cours',
                        data: currentMonthData,
                        borderColor: 'rgba(110, 66, 193, 1)',
                        borderWidth: 2,
                        backgroundColor: 'rgba(110, 66, 193, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    },
                    {
                        label: previousMonthLabel || 'Mois précédent',
                        data: previousMonthData,
                        borderColor: 'rgba(91, 33, 182, 0.5)',
                        borderWidth: 2,
                        backgroundColor: 'rgba(91, 33, 182, 0.05)',
                        borderDash: [5, 5],
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--phoenix-600'),
                            font: {
                                family: 'Nunito Sans',
                                size: 12
                            },
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.98)',
                        titleColor: '#1E1E2D',
                        bodyColor: '#1E1E2D',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        displayColors: true,
                        padding: 12,
                        boxShadow: '0 4px 20px rgba(0, 0, 0, 0.15)',
                        cornerRadius: 6,
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: 'Nunito Sans, sans-serif'
                        },
                        bodyFont: {
                            size: 12,
                            family: 'Nunito Sans, sans-serif'
                        },
                        callbacks: {
                            title: function(tooltipItems) {
                                return `Jour ${tooltipItems[0].label}`;
                            },
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = Number(context.parsed.y).toLocaleString('fr-FR');
                                return `${label}: ${value} FCFA`;
                            },
                            labelTextColor: function(context) {
                                return context.datasetIndex === 0 ? 'rgba(110, 66, 193, 1)' : 'rgba(91, 33, 182, 0.8)';
                            },
                            afterBody: function(tooltipItems) {
                                if (tooltipItems.length > 1) {
                                    const current = tooltipItems[0].parsed.y;
                                    const previous = tooltipItems[1].parsed.y;
                                    if (previous > 0) {
                                        const diff = ((current - previous) / previous * 100).toFixed(1);
                                        const isPositive = diff >= 0;
                                        return [`Évolution: ${isPositive ? '+' : ''}${diff}% par rapport au mois précédent`];
                                    }
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--phoenix-600'),
                            font: {
                                family: 'Nunito Sans',
                                size: 11
                            },
                            autoSkip: true,
                            maxRotation: 0,
                            autoSkipPadding: 12,
                            maxTicksLimit: window.innerWidth < 576 ? 8 : 16
                        }
                    },
                    y: {
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--phoenix-100'),
                            drawBorder: false
                        },
                        beginAtZero: true,
                        ticks: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--phoenix-600'),
                            font: {
                                family: 'Nunito Sans',
                                size: 11
                            },
                            callback: function(value) {
                                // Sur mobile : abréger les montants pour gagner de la place
                                if (window.innerWidth < 576) {
                                    if (value >= 1000000) return (value / 1000000).toLocaleString('fr-FR') + ' M';
                                    if (value >= 1000) return Math.round(value / 1000) + ' k';
                                    return value;
                                }
                                return value.toLocaleString('fr-FR') + ' FCFA';
                            },
                            maxTicksLimit: 6
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
        
        // Mettre à jour le graphique lors du changement de mois
        const monthSelect = document.getElementById('month-selector');
        if (monthSelect) {
            monthSelect.addEventListener('change', function() {
                loadSalesData(this.value);
            });
        }
    }
    
    // Fonction pour charger les données de vente
    function loadSalesData(month) {
        // Afficher un indicateur de chargement
        const loadingElement = document.createElement('div');
        loadingElement.className = 'chart-loading';
        loadingElement.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>';
        salesChartElement.parentNode.appendChild(loadingElement);
        
        // Toujours faire une requête AJAX pour obtenir les données à jour
        fetch(`/admin/sales-data/${month}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    initChart(
                        data.data.labels,
                        data.data.currentMonth.data,
                        data.data.previousMonth.data,
                        data.data.currentMonth.label,
                        data.data.previousMonth.label
                    );
                } else {
                    console.error('Erreur dans la réponse du serveur:', data);
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données:', error);
                // En cas d'erreur, essayer de charger les données initiales
                try {
                    const currentMonthSales = JSON.parse(salesChartElement.dataset.currentMonthSales || '[]');
                    const previousMonthSales = JSON.parse(salesChartElement.dataset.previousMonthSales || '[]');
                    const daysInMonth = Math.max(currentMonthSales.length, previousMonthSales.length);
                    const labels = Array.from({length: daysInMonth}, (_, i) => i + 1);
                    
                    initChart(
                        labels,
                        currentMonthSales,
                        previousMonthSales,
                        'Mois en cours',
                        'Mois précédent'
                    );
                } catch (e) {
                    console.error('Impossible de charger les données de secours:', e);
                }
            })
            .finally(() => {
                loadingElement.remove();
            });
    }
    
    // 1) Rendu IMMÉDIAT à partir des données inline (s'affiche même si l'AJAX échoue)
    function renderInlineData() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js non chargé : le graphe des ventes ne peut pas s\'afficher.');
            return;
        }
        try {
            const currentMonthSales = JSON.parse(salesChartElement.dataset.currentMonthSales || '[]');
            const previousMonthSales = JSON.parse(salesChartElement.dataset.previousMonthSales || '[]');
            const daysInMonth = Math.max(currentMonthSales.length, previousMonthSales.length, 1);
            const labels = Array.from({ length: daysInMonth }, (_, i) => i + 1);
            initChart(labels, currentMonthSales, previousMonthSales, 'Mois en cours', 'Mois précédent');
        } catch (e) {
            console.error('Données inline du graphe invalides:', e);
        }
    }

    renderInlineData();

    // 2) Puis on rafraîchit avec des données à jour via AJAX (sans bloquer l'affichage)
    const initialMonth = monthSelector ? monthSelector.value : '';
    if (initialMonth) {
        loadSalesData(initialMonth);
    }
    
    // Écouter les changements du sélecteur de mois
    if (monthSelector) {
        monthSelector.addEventListener('change', function(e) {
            // Empêcher le comportement par défaut du formulaire
            e.preventDefault();
            e.stopPropagation();
            
            // Charger les données pour le mois sélectionné
            loadSalesData(this.value);
            
            // Empêcher la soumission du formulaire parent s'il existe
            let form = this.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    return false;
                });
            }
            
            return false;
        });
        
        // Désactiver la soumission du formulaire parent s'il existe
        let form = monthSelector.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                return false;
            });
        }
    }
    
    // Ajouter des styles pour l'indicateur de chargement
    const style = document.createElement('style');
    style.textContent = `
        .chart-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .chart-loading .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    `;
    document.head.appendChild(style);
});

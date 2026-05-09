<?php
/**
 * AmigoPet - Footer Component
 * Localização: ~/App/View/includes/dashboard/footer.php
 * Fecha as tags abertas no header e carrega scripts globais.
 */
?>
    <!-- Fechamento do main-content aberto no layout/menu -->
    <!-- Inclusão do Chart.js via CDN para os gráficos do Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cores da I.D. Visual (Poppins/Inter)
        const green = '#6FCF97';
        const orange = '#F2994A';
        const blue = '#56CCF2';

        // Verificação se os elementos de gráfico existem na página atual
        const elEspecies = document.getElementById('chartEspecies');
        const elStatus = document.getElementById('chartStatus');

        if (elEspecies) {
            new Chart(elEspecies.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Cachorros', 'Gatos', 'Outros'],
                    datasets: [{
                        data: [45, 30, 10],
                        backgroundColor: [orange, green, blue],
                        borderWidth: 0
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        if (elStatus) {
            new Chart(elStatus.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Cachorros', 'Gatos', 'Outros'],
                    datasets: [
                        {
                            label: 'Adotados',
                            data: [120, 90, 30],
                            backgroundColor: green,
                            borderRadius: 8
                        },
                        {
                            label: 'Disponíveis',
                            data: [40, 25, 15],
                            backgroundColor: '#E0E0E0',
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
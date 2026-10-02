<x-layouts.auth title="Dashboard">
    <x-page-header title="Dashboard" icon="dashboard"></x-page-header>

    {{-- Cart penjualan --}}
    <div class="row mt-6">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Penjualan {{ date('Y') }}</h3>
                </div>

                <div class="card-body">
                    <div style="height: 500px;">
                        <canvas id="saleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.saleChart = this.saleChartInit();
                }

                /**
                 * Inisialisasi chart penjualan
                 */
                saleChartInit() {
                    return new Chart(document.getElementById('saleChart'), {
                        type: 'bar',
                        data: {
                            labels: [
                                'Januari',
                                'Februari',
                                'Maret',
                                'April',
                                'Mei',
                                'Juni',
                                'Juli',
                                'Agustus',
                                'September',
                                'Oktober',
                                'Novenber',
                                'Desember',
                            ],
                            datasets: [
                                {
                                    label: 'Quantity',
                                    data: [],
                                    backgroundColor: '#2068FA',
                                },
                                {
                                    label: 'Jumlah',
                                    data: [],
                                    backgroundColor: '#F9BC2F',
                                },
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    ticks: {
                                        maxTicksLimit: 10
                                    }
                                }
                            }
                        }
                    })
                }

                /**
                 * Request data penjualan
                 */
                getMonthlySalesSummary() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('dashboard.sales') }}",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }
            }

            window.service = new Service();
        </script>
        <script>
            "use strict"

            /**
             * Request data sales setelah dikumen selesai dimuat.
             */
            $(document).ready(async function () {
                try {
                    const response = await service.getMonthlySalesSummary();
                    const quantities = response.data.map((data) => data.quantity);
                    const amounts = response.data.map((data) => data.amount);

                    service.saleChart.data.datasets[0].data = quantities;
                    service.saleChart.data.datasets[1].data = amounts;
                    service.saleChart.update();
                } catch (error) {
                    app.alert('danger', error.message);
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>

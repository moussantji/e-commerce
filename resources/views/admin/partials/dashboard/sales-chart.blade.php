<div class="card mb-3">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <h5 class="mb-0">Ventes mensuelles</h5>
        <div class="d-flex align-items-center flex-grow-1 flex-sm-grow-0">
            <select id="month-selector" class="form-select form-select-sm w-100" style="max-width: 220px;" formnovalidate>
                @foreach($months as $month)
                    <option value="{{ $month['value'] }}" {{ $month['selected'] ? 'selected' : '' }}>
                        {{ $month['label'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="position-relative">
            <div class="chart-container">
                <canvas id="monthly-sales-chart" class="echart-sales-chart" 
                        data-current-month-sales='@json($currentMonthSales)' 
                        data-previous-month-sales='@json($previousMonthSales)'>
                </canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <!-- Inclure Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Inclure le fichier JavaScript du graphique -->
    <script src="{{ asset('assets/js/dashboards/sales-chart.js') }}"></script>
    
    <style>
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
            /* Empêche le canvas de provoquer un débordement horizontal */
            max-width: 100%;
            overflow: hidden;
        }

        .echart-sales-chart {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        /* Adaptation tablette */
        @media (max-width: 768px) {
            .chart-container {
                height: 300px;
            }
        }

        /* Adaptation téléphone */
        @media (max-width: 576px) {
            .chart-container {
                height: 260px;
            }
            #month-selector {
                max-width: 100% !important;
            }
        }
    </style>
@endpush

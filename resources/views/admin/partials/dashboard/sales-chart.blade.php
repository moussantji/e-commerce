<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Ventes mensuelles</h5>
        <div class="d-flex align-items-center">
            <select id="month-selector" class="form-select form-select-sm" style="width: 200px;" formnovalidate>
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
        }
        
        .echart-sales-chart {
            width: 100%;
            height: 100%;
        }
    </style>
@endpush

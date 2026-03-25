@extends('layout')

@section('content')
<div class="dashboard-page mt-4">
    <div class="page-header report">
        <div class="header-content">
            <div class="page-title fs-2">Dashboard</div>
            <div class="page-subtitle mb-3">Live view of activity for {{ now()->format('F d, Y') }}.</div>
        </div>
    </div>

    <div class="summary">
        <div class="row">
            <div class="data-panel">
                <div class="data-labels">
                    <div class="label">Visitors Today</div>
                    <div class="meta">Based on latest check-ins</div>
                </div>
                <div class="value">{{ number_format($todayVisitors) }}</div>
            </div>
            <div class="data-panel">
                <div class="data-labels">
                    <div class="label">Currently In</div>
                    <div class="meta">Active inside facility</div>
                </div>
                <div class="value">{{ number_format($visitorsIn) }}</div>
            </div>
            <div class="data-panel">
                <div class="data-labels">
                    <div class="label">Currently Out</div>
                    <div class="meta">Already checked out</div>
                </div>
                <div class="value">{{ number_format($visitorsOut) }}</div>
            </div>
            <div class="data-panel">
                <div class="data-labels">
                    <div class="label">Employee Logs Today</div>
                    <div class="meta">Total employee entries</div>
                </div>
                <div class="value">{{ number_format($todayEmployeeLogs) }}</div>
            </div>
        </div>


        <div class="row">
            <div class="dash-graph graph-one">
                <div class="graph-header">Visitor Type Distribution Today</div>
                <canvas id="visitorPieChart"></canvas>
            @if($visitorTypeBreakdown->isEmpty())
                <p class="dash-meta mb-0">No visitor type records found for today.</p>
            @endif
            </div>
            <div class="dash-graph graph-two">
                <div class="graph-header">Last 7 Days</div>
                <div class="trend-grid">
                    @foreach($weeklyTrend as $point)
                        @php
                            $height = $maxTrendCount > 0 ? round(($point['total'] / $maxTrendCount) * 120, 0) : 6;
                        @endphp
                        <div class="trend-column" title="{{ $point['full_date'] }}">
                            <div class="trend-total">{{ $point['total'] }}</div>
                            <div class="trend-bar" style="height: {{ max($height, 6) }}px;"></div>
                            <div class="trend-day">{{ $point['label'] }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="quick-links">
                    <a class="quick-link" href="{{ route('visitorslog') }}"><i class="bi bi-journal-text"></i> View Log Sheets</a>
                    <a class="quick-link" href="{{ route('visitorslog.form') }}" target="_blank"><i class="bi bi-person-plus"></i> Add Visitor</a>
                    <a class="quick-link" href="{{ route('reports') }}"><i class="bi bi-file-earmark-bar-graph"></i> Open Reports</a>
                </div>
            </div>
        </div>
    </div>


</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const visitorLabels = @json($visitorTypeBreakdown->pluck('name'));
    const visitorData = @json($visitorTypeBreakdown->pluck('total'));

    const ctx = document.getElementById('visitorPieChart').getContext('2d');

    new Chart(ctx, {
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
</script>
@endsection

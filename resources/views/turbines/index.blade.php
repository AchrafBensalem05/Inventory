<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GE Turbine Operations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script>
    <style>
        :root {
            --bg: #f5efe7;
            --ink: #0d1b2a;
            --muted: #5f6c7b;
            --card: #ffffff;
            --accent: #0b7b6e;
            --accent-2: #d16a2b;
            --accent-3: #136f63;
            --ring: rgba(13, 27, 42, 0.08);
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: "IBM Plex Sans", "Segoe UI", sans-serif;
            background: radial-gradient(circle at top left, rgba(209, 106, 43, 0.12), transparent 50%),
                radial-gradient(circle at 80% 10%, rgba(11, 123, 110, 0.15), transparent 50%),
                var(--bg);
            color: var(--ink);
        }
        header {
            padding: 32px 32px 10px;
        }
        .title-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        h1 {
            font-family: "Space Grotesk", "Segoe UI", sans-serif;
            font-size: 32px;
            margin: 0 0 6px;
        }
        .subtitle {
            color: var(--muted);
            margin: 0;
        }
        .nav-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: var(--accent);
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .button.secondary {
            background: transparent;
            border-color: rgba(13, 27, 42, 0.2);
            color: var(--ink);
        }
        .button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px var(--ring);
        }
        main {
            padding: 0 32px 40px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .card {
            background: var(--card);
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 20px 35px var(--ring);
            animation: rise 0.5s ease both;
        }
        .card:nth-child(2) {
            animation-delay: 0.1s;
        }
        .card:nth-child(3) {
            animation-delay: 0.2s;
        }
        @keyframes rise {
            from {
                transform: translateY(12px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .import-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 20px;
        }
        .import-grid form {
            display: grid;
            gap: 12px;
        }
        label {
            font-weight: 600;
        }
        input[type="text"],
        input[type="file"],
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(13, 27, 42, 0.2);
            font-family: inherit;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }
        .filter-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        .helper {
            grid-column: 1 / -1;
            font-size: 12px;
            color: var(--muted);
            margin: 0;
        }
        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .flash {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 12px;
        }
        .flash.success {
            background: rgba(11, 123, 110, 0.12);
            color: var(--accent-3);
        }
        .flash.error {
            background: rgba(209, 106, 43, 0.15);
            color: #8a3a0f;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        .stat {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .stat span {
            color: var(--muted);
            font-size: 13px;
        }
        .stat strong {
            font-size: 24px;
        }
        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 780px;
        }
        th,
        td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid rgba(13, 27, 42, 0.08);
            font-size: 14px;
        }
        th {
            font-family: "Space Grotesk", "Segoe UI", sans-serif;
            font-weight: 600;
        }
        .table-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 12px;
        }
        .search {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(13, 27, 42, 0.06);
            font-size: 12px;
        }
        .chart-strip {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 6px;
            scroll-snap-type: x proximity;
        }
        .chart-card {
            min-width: 320px;
            flex: 0 0 320px;
            scroll-snap-align: start;
        }
        .chart-card.wide {
            min-width: 420px;
            flex-basis: 420px;
        }
        .range-controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 12px;
        }
        .range-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
            display: block;
        }
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(13, 27, 42, 0.65);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 1000;
        }
        .modal.open {
            display: flex;
        }
        .modal-card {
            background: var(--card);
            width: min(1200px, 95vw);
            max-height: 90vh;
            border-radius: 18px;
            box-shadow: 0 30px 60px rgba(13, 27, 42, 0.35);
            display: flex;
            flex-direction: column;
        }
        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(13, 27, 42, 0.08);
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
        }
        .modal-body {
            padding: 16px 20px 24px;
            overflow: auto;
        }
        .modal-canvas {
            width: 100%;
            height: 60vh;
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .pager {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 16px;
        }
        .pager a,
        .pager span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(13, 27, 42, 0.15);
            font-size: 13px;
            text-decoration: none;
            color: var(--ink);
        }
        .pager .disabled {
            opacity: 0.45;
        }
        @media (max-width: 980px) {
            header,
            main {
                padding-left: 18px;
                padding-right: 18px;
            }
            .import-grid {
                grid-template-columns: 1fr;
            }
            .table-actions {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="title-row">
            <div>
                <div class="pill">GE Turbine Operations</div>
                <h1>Fleet Status &amp; Dispatch</h1>
                <p class="subtitle">Import, review, edit, and export turbine availability in one place.</p>
            </div>
            <div class="nav-actions">
                <a class="button secondary" href="{{ url('/') }}">Dashboard</a>
                <a class="button" href="{{ route('turbines.export', request()->query()) }}">Export CSV</a>
            </div>
        </div>
    </header>

    <main>
        <section class="card">
            @if (session('status'))
                <div class="flash success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash error">
                    <strong>Import failed:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="import-grid">
                <div>
                    <h2>Import CSV</h2>
                    <p class="subtitle">Use the local path or upload a file to refresh the table.</p>
                    <form method="POST" action="{{ route('turbines.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="csv_path">CSV path</label>
                            <input id="csv_path" name="csv_path" type="text" value="{{ old('csv_path', $defaultCsvPath) }}">
                        </div>
                        <div>
                            <label for="csv_file">Or upload</label>
                            <input id="csv_file" name="csv_file" type="file" accept=".csv,text/csv">
                        </div>
                        <label class="checkbox" for="replace">
                            <input id="replace" name="replace" type="checkbox" value="1">
                            Replace existing data
                        </label>
                        <button class="button" type="submit">Import now</button>
                    </form>
                </div>
                <div>
                    <h2>Snapshot</h2>
                    <div class="grid">
                        <div class="stat">
                            <span>Total rows</span>
                            <strong>{{ number_format($summary['total']) }}</strong>
                        </div>
                        <div class="stat">
                            <span>Rows with load</span>
                            <strong>{{ number_format($summary['with_load']) }}</strong>
                        </div>
                        <div class="stat">
                            <span>Offline entries</span>
                            <strong>{{ number_format($summary['offline']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <h2>Filter &amp; focus</h2>
            <p class="subtitle">Use a single day or a date range, plus field and unit filters.</p>
            <form class="filter-grid" method="GET" action="{{ route('turbines.index') }}">
                <div>
                    <label for="conc">Field / Conc</label>
                    <select id="conc" name="conc">
                        <option value="">All fields</option>
                        @foreach ($concOptions as $conc)
                            <option value="{{ $conc }}" {{ $filters['conc'] === $conc ? 'selected' : '' }}>{{ $conc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="unit">Unit</label>
                    <select id="unit" name="unit">
                        <option value="">All units</option>
                        @foreach ($unitOptions as $unit)
                            <option value="{{ $unit }}" {{ $filters['unit'] === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date">Single day</label>
                    <input id="date" name="date" type="date" value="{{ $filters['date'] }}">
                </div>
                <div>
                    <label for="date_from">Date from</label>
                    <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}">
                </div>
                <div>
                    <label for="date_to">Date to</label>
                    <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}">
                </div>
                <div>
                    <label for="q">Keyword search</label>
                    <input id="q" name="q" type="text" placeholder="Unit, conc, status, remarks" value="{{ $filters['q'] }}">
                </div>
                <p class="helper">Tip: set a single day OR a date range. Single day overrides the range.</p>
                <div class="filter-actions">
                    <button class="button" type="submit">Apply filters</button>
                    <a class="button secondary" href="{{ route('turbines.index') }}">Clear</a>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Chart window</h2>
            <p class="subtitle">Show a smaller time slice and scroll through large datasets.</p>
            <div class="range-controls">
                <div>
                    <label for="windowSize">Window size</label>
                    <select id="windowSize">
                        <option value="60">2 months</option>
                        <option value="180">6 months</option>
                        <option value="365">1 year</option>
                        <option value="all">All data</option>
                    </select>
                </div>
                <div>
                    <label for="windowStart">Scroll window</label>
                    <input id="windowStart" type="range" min="0" max="0" value="0">
                    <span id="windowLabel" class="range-label">No data loaded yet.</span>
                </div>
            </div>
        </section>

        <section>
            <p class="helper">Click any chart to open a zoomable view.</p>
            <div class="chart-strip">
                <div class="card chart-card wide">
                    <h2>Load trend by unit</h2>
                    <p class="subtitle">Average MW over time for the selected unit(s).</p>
                    <canvas id="trendChart" height="140"></canvas>
                </div>
                <div class="card chart-card">
                    <h2>Status mix</h2>
                    <canvas id="statusChart" height="140"></canvas>
                </div>
                <div class="card chart-card wide">
                    <h2>Average load by month</h2>
                    <canvas id="monthlyChart" height="140"></canvas>
                </div>
                <div class="card chart-card wide">
                    <h2>Records per day</h2>
                    <canvas id="dailyChart" height="140"></canvas>
                </div>
                <div class="card chart-card">
                    <h2>Load distribution</h2>
                    <canvas id="distributionChart" height="140"></canvas>
                </div>
                <div class="card chart-card wide">
                    <h2>Top units by avg MW</h2>
                    <canvas id="topUnitsChart" height="140"></canvas>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="table-actions">
                <div>
                    <h2>Records</h2>
                    <p class="subtitle">
                        Showing {{ $turbines->firstItem() ?? 0 }} to {{ $turbines->lastItem() ?? 0 }} of {{ $turbines->total() }} rows.
                    </p>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Conc</th>
                            <th>Unit</th>
                            <th>Load (MW)</th>
                            <th>Status</th>
                            <th>Date on</th>
                            <th>Date off</th>
                            <th>Remarks</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($turbines as $turbine)
                            <tr>
                                <td>{{ optional($turbine->date)->format('Y-m-d') }}</td>
                                <td>{{ $turbine->conc }}</td>
                                <td>{{ $turbine->unit }}</td>
                                <td>{{ $turbine->load_mw !== null ? number_format($turbine->load_mw, 2) : '' }}</td>
                                <td>{{ $turbine->status }}</td>
                                <td>{{ optional($turbine->date_on)->format('Y-m-d') }}</td>
                                <td>{{ optional($turbine->date_off)->format('Y-m-d') }}</td>
                                <td>{{ $turbine->remarks }}</td>
                                <td><a class="button secondary" href="{{ route('turbines.edit', $turbine) }}">Edit</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">No records yet. Import the CSV to get started.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($turbines->hasPages())
                <div class="pager">
                    @if ($turbines->onFirstPage())
                        <span class="disabled">Previous</span>
                    @else
                        <a href="{{ $turbines->previousPageUrl() }}">Previous</a>
                    @endif
                    <span>Page {{ $turbines->currentPage() }} of {{ $turbines->lastPage() }}</span>
                    @if ($turbines->hasMorePages())
                        <a href="{{ $turbines->nextPageUrl() }}">Next</a>
                    @else
                        <span class="disabled">Next</span>
                    @endif
                </div>
            @endif
        </section>
    </main>

    <div class="modal" id="chartModal" aria-hidden="true">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h2 id="modalTitle">Chart detail</h2>
                    <p class="subtitle" id="modalSubtitle">Scroll or pinch to zoom. Drag to pan.</p>
                </div>
                <div class="modal-actions">
                    <button class="button secondary" id="modalReset" type="button">Reset zoom</button>
                    <button class="button" id="modalClose" type="button">Close</button>
                </div>
            </div>
            <div class="modal-body">
                <canvas id="modalChart" class="modal-canvas"></canvas>
            </div>
        </div>
    </div>

    <script>
        const statusLabels = @json($statusLabels);
        const statusValues = @json($statusValues);
        const trendLabels = @json($trendLabels);
        const trendSeries = @json($trendSeries);
        const monthlyLabels = @json($monthlyLabels);
        const monthlyValues = @json($monthlyValues);
        const dailyLabels = @json($dailyLabels);
        const dailyValues = @json($dailyValues);
        const distributionLabels = @json($distributionLabels);
        const distributionValues = @json($distributionValues);
        const topUnitLabels = @json($topUnitLabels);
        const topUnitValues = @json($topUnitValues);

        const windowSizeSelect = document.getElementById('windowSize');
        const windowStartInput = document.getElementById('windowStart');
        const windowLabel = document.getElementById('windowLabel');

        const statusChart = document.getElementById('statusChart');
        const trendChart = document.getElementById('trendChart');
        const monthlyChart = document.getElementById('monthlyChart');
        const dailyChart = document.getElementById('dailyChart');
        const distributionChart = document.getElementById('distributionChart');
        const topUnitsChart = document.getElementById('topUnitsChart');

        const modal = document.getElementById('chartModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalClose = document.getElementById('modalClose');
        const modalReset = document.getElementById('modalReset');
        const modalCanvas = document.getElementById('modalChart');
        let modalChart = null;

        const lineBaseOptions = {
            interaction: { mode: 'index', intersect: false },
            elements: { point: { radius: 0 } },
            scales: { y: { beginAtZero: true } }
        };

        const zoomOptions = {
            zoom: {
                wheel: { enabled: true },
                pinch: { enabled: true },
                mode: 'x'
            },
            pan: {
                enabled: true,
                mode: 'x'
            }
        };

        function openModal(title, config) {
            if (!modal || !modalCanvas) {
                return;
            }

            modalTitle.textContent = title;
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';

            if (modalChart) {
                modalChart.destroy();
            }

            const options = config.options ? { ...config.options } : {};
            const plugins = options.plugins ? { ...options.plugins } : {};
            plugins.zoom = zoomOptions;
            options.plugins = plugins;
            options.maintainAspectRatio = false;

            modalChart = new Chart(modalCanvas, {
                ...config,
                options
            });
        }

        function closeModal() {
            if (!modal) {
                return;
            }
            modal.classList.remove('open');
            document.body.style.overflow = '';
            if (modalChart) {
                modalChart.destroy();
                modalChart = null;
            }
        }

        if (modalClose) {
            modalClose.addEventListener('click', closeModal);
        }

        if (modalReset) {
            modalReset.addEventListener('click', () => {
                if (modalChart && typeof modalChart.resetZoom === 'function') {
                    modalChart.resetZoom();
                }
            });
        }

        if (modal) {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        function buildLineConfig(labels, datasets, legendPosition = 'bottom') {
            return {
                type: 'line',
                data: { labels, datasets },
                options: {
                    plugins: { legend: { position: legendPosition } },
                    ...lineBaseOptions
                }
            };
        }

        function buildBarConfig(labels, datasets) {
            return {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            };
        }

        function updateWindowedChart(chart, fullData, windowSize, ratio) {
            if (!chart || fullData.labels.length === 0) {
                return;
            }

            const total = fullData.labels.length;
            const size = Math.min(windowSize, total);
            const maxStart = Math.max(0, total - size);
            const start = Math.round(maxStart * ratio);
            const end = start + size;

            chart.data.labels = fullData.labels.slice(start, end);
            chart.data.datasets = fullData.datasets.map((dataset) => ({
                ...dataset,
                data: dataset.data.slice(start, end)
            }));
            chart.update();
        }

        function updateWindowLabel(referenceLabels, windowSize, ratio) {
            if (!windowLabel) {
                return;
            }

            if (!referenceLabels || referenceLabels.length === 0) {
                windowLabel.textContent = 'No data loaded yet.';
                return;
            }

            const total = referenceLabels.length;
            const size = Math.min(windowSize, total);
            const maxStart = Math.max(0, total - size);
            const start = Math.round(maxStart * ratio);
            const endIndex = Math.min(start + size - 1, total - 1);
            const startLabel = referenceLabels[start];
            const endLabel = referenceLabels[endIndex];

            windowLabel.textContent = `${startLabel} to ${endLabel}`;
        }

        function applyWindow(trendFull, dailyFull, trendChartInstance, dailyChartInstance) {
            if (!windowSizeSelect || !windowStartInput) {
                return;
            }

            const referenceLabels = dailyFull.labels.length ? dailyFull.labels : trendFull.labels;
            const total = referenceLabels.length;
            const windowSizeValue = windowSizeSelect.value;
            const windowSize = windowSizeValue === 'all' ? total : parseInt(windowSizeValue, 10);

            const maxStart = Math.max(0, total - windowSize);
            windowStartInput.max = maxStart.toString();
            const startValue = Math.min(parseInt(windowStartInput.value || '0', 10), maxStart);
            windowStartInput.value = startValue.toString();
            windowStartInput.disabled = maxStart === 0;

            const ratio = maxStart > 0 ? startValue / maxStart : 0;

            updateWindowedChart(trendChartInstance, trendFull, windowSize, ratio);
            updateWindowedChart(dailyChartInstance, dailyFull, windowSize, ratio);
            updateWindowLabel(referenceLabels, windowSize, ratio);
        }

        if (statusChart) {
            const statusDatasets = [{
                label: 'Rows',
                data: statusValues,
                backgroundColor: '#0b7b6e',
                borderRadius: 8
            }];
            const statusConfig = buildBarConfig(statusLabels, statusDatasets);
            new Chart(statusChart, statusConfig);
            statusChart.addEventListener('click', () => openModal('Status mix', statusConfig));
        }

        let trendChartInstance = null;
        let dailyChartInstance = null;

        if (trendChart) {
            const trendDatasets = trendSeries.map((series) => ({
                ...series,
                tension: 0.3,
                fill: true,
                spanGaps: true
            }));

            const trendConfig = buildLineConfig(trendLabels, trendDatasets, 'bottom');
            const trendFull = { labels: trendLabels, datasets: trendDatasets };

            trendChartInstance = new Chart(trendChart, trendConfig);
            trendChart.addEventListener('click', () => openModal('Load trend by unit', buildLineConfig(trendFull.labels, trendFull.datasets, 'bottom')));

            const dailyDatasets = [{
                label: 'Records',
                data: dailyValues,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124, 58, 237, 0.12)',
                tension: 0.2,
                fill: true
            }];
            const dailyFull = { labels: dailyLabels, datasets: dailyDatasets };

            if (dailyChart) {
                const dailyConfig = buildLineConfig(dailyFull.labels, dailyFull.datasets, 'bottom');
                dailyChartInstance = new Chart(dailyChart, dailyConfig);
                dailyChart.addEventListener('click', () => openModal('Records per day', buildLineConfig(dailyFull.labels, dailyFull.datasets, 'bottom')));
            }

            applyWindow(trendFull, dailyFull, trendChartInstance, dailyChartInstance);

            if (windowSizeSelect) {
                windowSizeSelect.addEventListener('change', () => {
                    applyWindow(trendFull, dailyFull, trendChartInstance, dailyChartInstance);
                });
            }

            if (windowStartInput) {
                windowStartInput.addEventListener('input', () => {
                    applyWindow(trendFull, dailyFull, trendChartInstance, dailyChartInstance);
                });
            }
        }

        if (monthlyChart) {
            const monthlyDatasets = [{
                label: 'Avg MW',
                data: monthlyValues,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                tension: 0.3,
                fill: true
            }];
            const monthlyConfig = buildLineConfig(monthlyLabels, monthlyDatasets, 'bottom');
            new Chart(monthlyChart, monthlyConfig);
            monthlyChart.addEventListener('click', () => openModal('Average load by month', monthlyConfig));
        }

        if (distributionChart) {
            const distributionDatasets = [{
                label: 'Rows',
                data: distributionValues,
                backgroundColor: '#d16a2b',
                borderRadius: 8
            }];
            const distributionConfig = buildBarConfig(distributionLabels, distributionDatasets);
            new Chart(distributionChart, distributionConfig);
            distributionChart.addEventListener('click', () => openModal('Load distribution', distributionConfig));
        }

        if (topUnitsChart) {
            const topUnitsDatasets = [{
                label: 'Avg MW',
                data: topUnitValues,
                backgroundColor: '#0b7b6e',
                borderRadius: 8
            }];
            const topUnitsConfig = buildBarConfig(topUnitLabels, topUnitsDatasets);
            new Chart(topUnitsChart, topUnitsConfig);
            topUnitsChart.addEventListener('click', () => openModal('Top units by avg MW', topUnitsConfig));
        }
    </script>
</body>
</html>

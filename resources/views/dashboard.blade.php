<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
    <style>
        :root {
            color-scheme: light;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: #f5f6f8;
            color: #0f172a;
        }
        header {
            background: #0f172a;
            color: #f8fafc;
            padding: 20px 28px;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 24px;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
        h2 {
            margin: 0 0 12px;
            font-size: 18px;
        }
        .muted {
            color: #64748b;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th,
        td {
            text-align: left;
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        @media (max-width: 980px) {
            main {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        OilGas Inventory Dashboard
        <a href="{{ route('turbines.index') }}" style="color: #93c5fd; font-size: 14px; margin-left: 16px;">GE Turbine Ops</a>
    </header>
    <main>
        <section class="card">
            <h2>Stock by Category</h2>
            <p class="muted" id="chartStatus">Loading chart data...</p>
            <canvas id="stockChart" height="120"></canvas>
        </section>
        <section class="card">
            <h2>Low Stock Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Location</th>
                        <th>Qty</th>
                        <th>Min</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Drill Bit 12-inch</td>
                        <td>Warehouse A</td>
                        <td>6 pcs</td>
                        <td>10 pcs</td>
                    </tr>
                    <tr>
                        <td>Valve Seal Kit</td>
                        <td>Field Site B</td>
                        <td>3 pcs</td>
                        <td>8 pcs</td>
                    </tr>
                    <tr>
                        <td>Drilling Mud</td>
                        <td>Warehouse C</td>
                        <td>180 L</td>
                        <td>250 L</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        const statusEl = document.getElementById('chartStatus');

        fetch('/api/inventory/summary')
            .then((response) => response.json())
            .then((data) => {
                statusEl.textContent = '';
                new Chart(document.getElementById('stockChart'), {
                    type: 'bar',
                    data: {
                        labels: data.categories,
                        datasets: [{
                            label: 'Current stock',
                            data: data.quantities,
                            backgroundColor: data.quantities.map((quantity, index) =>
                                quantity < data.thresholds[index] ? '#E24B4A' : '#1D9E75'
                            )
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch((error) => {
                statusEl.textContent = 'Failed to load chart data.';
                console.error(error);
            });
    </script>
</body>
</html>

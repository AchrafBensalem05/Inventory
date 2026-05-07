<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit GE Turbine Row</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5efe7;
            --ink: #0d1b2a;
            --muted: #5f6c7b;
            --card: #ffffff;
            --accent: #0b7b6e;
            --ring: rgba(13, 27, 42, 0.08);
        }
        body {
            margin: 0;
            font-family: "IBM Plex Sans", "Segoe UI", sans-serif;
            background: radial-gradient(circle at 10% 10%, rgba(11, 123, 110, 0.15), transparent 50%),
                radial-gradient(circle at 80% 0%, rgba(209, 106, 43, 0.2), transparent 50%),
                var(--bg);
            color: var(--ink);
        }
        main {
            max-width: 920px;
            margin: 32px auto;
            padding: 0 20px;
        }
        .card {
            background: var(--card);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 20px 35px var(--ring);
            animation: rise 0.4s ease both;
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
        h1 {
            font-family: "Space Grotesk", "Segoe UI", sans-serif;
            margin: 0 0 6px;
        }
        .subtitle {
            color: var(--muted);
            margin: 0 0 18px;
        }
        form {
            display: grid;
            gap: 16px;
        }
        label {
            font-weight: 600;
        }
        input,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(13, 27, 42, 0.2);
            font-family: inherit;
        }
        textarea {
            min-height: 90px;
        }
        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: var(--accent);
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }
        .button.secondary {
            background: transparent;
            border-color: rgba(13, 27, 42, 0.2);
            color: var(--ink);
        }
        .flash {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 12px;
            background: rgba(11, 123, 110, 0.12);
            color: #0b7b6e;
        }
    </style>
</head>
<body>
    <main>
        <div class="card">
            <h1>Edit Turbine Entry</h1>
            <p class="subtitle">Update the record and keep the history accurate.</p>

            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('turbines.update', $turbine) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div>
                        <label for="date">Date</label>
                        <input id="date" name="date" type="date" value="{{ old('date', optional($turbine->date)->format('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label for="conc">Conc</label>
                        <input id="conc" name="conc" type="text" value="{{ old('conc', $turbine->conc) }}">
                    </div>
                    <div>
                        <label for="unit">Unit</label>
                        <input id="unit" name="unit" type="text" value="{{ old('unit', $turbine->unit) }}">
                    </div>
                </div>

                <div class="row">
                    <div>
                        <label for="load_mw">Load (MW)</label>
                        <input id="load_mw" name="load_mw" type="number" step="0.01" value="{{ old('load_mw', $turbine->load_mw) }}">
                    </div>
                    <div>
                        <label for="status">Status</label>
                        <input id="status" name="status" type="text" value="{{ old('status', $turbine->status) }}">
                    </div>
                    <div>
                        <label for="date_on">Date on</label>
                        <input id="date_on" name="date_on" type="date" value="{{ old('date_on', optional($turbine->date_on)->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label for="date_off">Date off</label>
                        <input id="date_off" name="date_off" type="date" value="{{ old('date_off', optional($turbine->date_off)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div>
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks">{{ old('remarks', $turbine->remarks) }}</textarea>
                </div>

                <div class="actions">
                    <button class="button" type="submit">Save changes</button>
                    <a class="button secondary" href="{{ route('turbines.index') }}">Back to list</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

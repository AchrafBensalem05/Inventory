<?php

namespace App\Http\Controllers;

use App\Models\GeTurbine;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeTurbineController extends Controller
{
    private const CSV_HEADER = [
        'date',
        'conc',
        'unit',
        'load_mw',
        'status',
        'date_on',
        'date_off',
        'remarks',
    ];

    public function index(Request $request)
    {
        $filters = $this->extractFilters($request);
        $baseQuery = GeTurbine::query();
        $this->applyFilters($baseQuery, $filters);

        $turbines = (clone $baseQuery)
            ->orderByDesc('date')
            ->orderBy('unit')
            ->paginate(25)
            ->withQueryString();

        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'with_load' => (clone $baseQuery)->whereNotNull('load_mw')->count(),
            'offline' => (clone $baseQuery)->where('status', 'Offline')->count(),
        ];

        $concOptions = GeTurbine::query()
            ->select('conc')
            ->whereNotNull('conc')
            ->distinct()
            ->orderBy('conc')
            ->pluck('conc')
            ->values();

        $unitOptions = GeTurbine::query()
            ->select('unit')
            ->whereNotNull('unit')
            ->distinct()
            ->orderBy('unit')
            ->pluck('unit')
            ->values();

        $trendUnits = $filters['unit'] !== ''
            ? [$filters['unit']]
            : (clone $baseQuery)
                ->select('unit', DB::raw('count(*) as total'))
                ->whereNotNull('unit')
                ->whereNotNull('load_mw')
                ->groupBy('unit')
                ->orderByDesc('total')
                ->limit(4)
                ->pluck('unit')
                ->values()
                ->all();

        [$trendLabels, $trendSeries] = $this->buildTrendSeries($baseQuery, $trendUnits);

        $monthlySeries = (clone $baseQuery)
            ->select(DB::raw("strftime('%Y-%m', date) as month"), DB::raw('avg(load_mw) as avg_load'))
            ->whereNotNull('date')
            ->whereNotNull('load_mw')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyLabels = $monthlySeries->pluck('month')->values();
        $monthlyValues = $monthlySeries
            ->pluck('avg_load')
            ->map(fn ($value) => round((float) $value, 2))
            ->values();

        $dailySeries = (clone $baseQuery)
            ->select('date', DB::raw('count(*) as total'))
            ->whereNotNull('date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyLabels = [];
        $dailyValues = [];
        foreach ($dailySeries as $row) {
            $dateKey = $this->parseDate($row->date);
            if ($dateKey === null) {
                continue;
            }

            $dailyLabels[] = $dateKey;
            $dailyValues[] = (int) $row->total;
        }

        $bucketCase = "case"
            . " when load_mw < 5 then '0-5'"
            . " when load_mw < 10 then '5-10'"
            . " when load_mw < 20 then '10-20'"
            . " when load_mw < 50 then '20-50'"
            . " else '50+' end";

        $bucketCounts = (clone $baseQuery)
            ->select(DB::raw("{$bucketCase} as bucket"), DB::raw('count(*) as total'))
            ->whereNotNull('load_mw')
            ->groupBy('bucket')
            ->get()
            ->keyBy('bucket');

        $bucketOrder = ['0-5', '5-10', '10-20', '20-50', '50+'];
        $distributionLabels = [];
        $distributionValues = [];
        foreach ($bucketOrder as $bucket) {
            $distributionLabels[] = $bucket;
            $distributionValues[] = (int) ($bucketCounts[$bucket]->total ?? 0);
        }

        $topUnits = (clone $baseQuery)
            ->select('unit', DB::raw('avg(load_mw) as avg_load'))
            ->whereNotNull('load_mw')
            ->whereNotNull('unit')
            ->groupBy('unit')
            ->orderByDesc('avg_load')
            ->limit(10)
            ->get();

        $topUnitLabels = $topUnits->pluck('unit')->values();
        $topUnitValues = $topUnits
            ->pluck('avg_load')
            ->map(fn ($value) => round((float) $value, 2))
            ->values();

        $defaultCsvPath = 'C:\\Users\\OPS010\\Documents\\projects\\new QC\\clean_data\\combined_ge_turbines.csv';

        return view('turbines.index', [
            'turbines' => $turbines,
            'filters' => $filters,
            'concOptions' => $concOptions,
            'unitOptions' => $unitOptions,
            'statusLabels' => $statusCounts->map(fn ($row) => $row->status ?: 'Unknown')->values(),
            'statusValues' => $statusCounts->pluck('total')->values(),
            'trendLabels' => $trendLabels,
            'trendSeries' => $trendSeries,
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'dailyLabels' => $dailyLabels,
            'dailyValues' => $dailyValues,
            'distributionLabels' => $distributionLabels,
            'distributionValues' => $distributionValues,
            'topUnitLabels' => $topUnitLabels,
            'topUnitValues' => $topUnitValues,
            'summary' => $summary,
            'defaultCsvPath' => $defaultCsvPath,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_path' => 'nullable|string',
            'csv_file' => 'nullable|file|mimes:csv,txt',
            'replace' => 'nullable|boolean',
        ]);

        $csvPath = $request->file('csv_file')?->getRealPath()
            ?? $request->input('csv_path');

        if (!$csvPath || !is_file($csvPath)) {
            return back()->withErrors(['csv_path' => 'CSV file not found at the provided path.']);
        }

        if ($request->boolean('replace')) {
            GeTurbine::truncate();
        }

        try {
            [$inserted, $skipped] = $this->importCsv($csvPath);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['csv_path' => $exception->getMessage()]);
        }

        return back()->with('status', "Imported {$inserted} rows. Skipped {$skipped} rows.");
    }

    public function edit(GeTurbine $turbine)
    {
        return view('turbines.edit', [
            'turbine' => $turbine,
        ]);
    }

    public function update(Request $request, GeTurbine $turbine)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'conc' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'load_mw' => 'nullable|numeric',
            'status' => 'nullable|string|max:255',
            'date_on' => 'nullable|date',
            'date_off' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $turbine->update($data);

        return redirect()
            ->route('turbines.edit', $turbine)
            ->with('status', 'Row updated.');
    }

    public function export(Request $request)
    {
        $filters = $this->extractFilters($request);
        $query = GeTurbine::query();
        $this->applyFilters($query, $filters);
        $fileName = 'ge_turbines_export_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::CSV_HEADER);

            (clone $query)
                ->orderBy('date')
                ->orderBy('unit')
                ->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            optional($row->date)->format('Y-m-d'),
                            $row->conc,
                            $row->unit,
                            $row->load_mw,
                            $row->status,
                            optional($row->date_on)->format('Y-m-d'),
                            optional($row->date_off)->format('Y-m-d'),
                            $row->remarks,
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'q' => trim((string) $request->input('q', '')),
            'conc' => trim((string) $request->input('conc', '')),
            'unit' => trim((string) $request->input('unit', '')),
            'date' => $this->parseDate($request->input('date')) ?? '',
            'date_from' => $this->parseDate($request->input('date_from')) ?? '',
            'date_to' => $this->parseDate($request->input('date_to')) ?? '',
        ];
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if ($filters['q'] !== '') {
            $query->where(function ($builder) use ($filters) {
                $builder
                    ->where('unit', 'like', "%{$filters['q']}%")
                    ->orWhere('conc', 'like', "%{$filters['q']}%")
                    ->orWhere('status', 'like', "%{$filters['q']}%")
                    ->orWhere('remarks', 'like', "%{$filters['q']}%");
            });
        }

        if ($filters['conc'] !== '') {
            $query->where('conc', $filters['conc']);
        }

        if ($filters['unit'] !== '') {
            $query->where('unit', $filters['unit']);
        }

        if ($filters['date'] !== '') {
            $query->whereDate('date', $filters['date']);
            return;
        }

        if ($filters['date_from'] !== '' && $filters['date_to'] !== '') {
            $query->whereBetween('date', [$filters['date_from'], $filters['date_to']]);
            return;
        }

        if ($filters['date_from'] !== '') {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $query->whereDate('date', '<=', $filters['date_to']);
        }
    }

    private function buildTrendSeries(Builder $baseQuery, array $trendUnits): array
    {
        if (!$trendUnits) {
            return [[], []];
        }

        $rows = (clone $baseQuery)
            ->select('date', 'unit', DB::raw('avg(load_mw) as avg_load'))
            ->whereNotNull('load_mw')
            ->whereIn('unit', $trendUnits)
            ->groupBy('date', 'unit')
            ->orderBy('date')
            ->get();

        if ($rows->isEmpty()) {
            return [[], []];
        }

        $labels = $rows
            ->map(fn ($row) => $this->parseDate($row->date))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $byUnit = [];
        foreach ($rows as $row) {
            $dateKey = $this->parseDate($row->date);
            if ($dateKey === null) {
                continue;
            }
            $byUnit[$row->unit][$dateKey] = round((float) $row->avg_load, 2);
        }

        $palette = [
            ['#0b7b6e', 'rgba(11, 123, 110, 0.15)'],
            ['#d16a2b', 'rgba(209, 106, 43, 0.15)'],
            ['#2563eb', 'rgba(37, 99, 235, 0.15)'],
            ['#7c3aed', 'rgba(124, 58, 237, 0.15)'],
        ];

        $series = [];
        foreach ($trendUnits as $index => $unit) {
            $data = [];
            foreach ($labels as $date) {
                $data[] = $byUnit[$unit][$date] ?? null;
            }

            $colors = $palette[$index % count($palette)];
            $series[] = [
                'label' => $unit,
                'data' => $data,
                'borderColor' => $colors[0],
                'backgroundColor' => $colors[1],
            ];
        }

        return [$labels, $series];
    }

    private function importCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Unable to read the CSV file.');
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);
            throw new \RuntimeException('CSV file is empty.');
        }

        $normalized = array_map(fn ($value) => strtolower(trim($value)), $header);

        if ($normalized !== self::CSV_HEADER) {
            fclose($handle);
            throw new \RuntimeException('CSV header does not match expected columns: ' . implode(', ', self::CSV_HEADER));
        }

        $inserted = 0;
        $skipped = 0;
        $batch = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if ($this->rowIsEmpty($row)) {
                $skipped++;
                continue;
            }

            $row = array_pad($row, count(self::CSV_HEADER), null);
            $rowData = array_combine(self::CSV_HEADER, $row);

            if ($rowData === false) {
                $skipped++;
                continue;
            }

            $batch[] = [
                'date' => $this->parseDate($rowData['date']),
                'conc' => $this->nullIfEmpty($rowData['conc']),
                'unit' => $this->nullIfEmpty($rowData['unit']),
                'load_mw' => $this->parseNumber($rowData['load_mw']),
                'status' => $this->nullIfEmpty($rowData['status']),
                'date_on' => $this->parseDate($rowData['date_on']),
                'date_off' => $this->parseDate($rowData['date_off']),
                'remarks' => $this->nullIfEmpty($rowData['remarks']),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= 500) {
                GeTurbine::insert($batch);
                $inserted += count($batch);
                $batch = [];
            }
        }

        if ($batch) {
            GeTurbine::insert($batch);
            $inserted += count($batch);
        }

        fclose($handle);

        return [$inserted, $skipped];
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function nullIfEmpty(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function parseDate(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Exception $exception) {
            return null;
        }
    }

    private function parseNumber(?string $value): ?float
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }
}

<?php
/** @var array $predictions */
/** @var array|null $predictionsPage */
/** @var string|null $success */
/** @var array $config */
/** @var array $configHistory */
?>
<?= success_message($success ?? null); ?>
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-semibold mb-1">Flight Predictions</h2>
            <p class="text-sm text-slate-600">Recamán & Fibonacci enriched forecasts for the next 1–5 years.</p>
        </div>
        <form method="post" class="flex gap-2 items-end">
            <input type="hidden" name="action" value="generate_predictions">
            <label class="text-sm text-slate-600">
                Years
                <select name="years" class="ml-2 bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <?php for ($y = 1; $y <= 5; $y++): ?>
                        <option value="<?= $y; ?>"><?= $y; ?></option>
                    <?php endfor; ?>
                </select>
            </label>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-md" type="submit">Generate</button>
        </form>
        <form method="post" onsubmit="return confirm('Clear all stored predictions?');">
            <input type="hidden" name="action" value="clear_predictions">
            <button class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md" type="submit">Clear</button>
        </form>
    </div>

    <div class="grid md:grid-cols-2 gap-4 mb-6">
        <form method="post" class="border border-slate-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold mb-2">Prediction configuration</h3>
            <p class="text-xs text-slate-500 mb-4">Adjust base probabilities and recurrence behavior. Changes are logged below.</p>
            <input type="hidden" name="action" value="update_config">
            <div class="grid grid-cols-2 gap-3">
                <label class="text-sm">Base %
                    <input type="number" step="0.01" min="0.3" max="0.95" name="base_percentage" value="<?= sanitize((string)($config['base_percentage'] ?? 0.5)); ?>" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </label>
                <label class="text-sm">+ per day
                    <input type="number" step="0.01" min="0" max="0.2" name="extra_day_increment" value="<?= sanitize((string)($config['extra_day_increment'] ?? 0.05)); ?>" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </label>
                <label class="text-sm">Recamán start
                    <input type="number" name="recaman_start" value="<?= sanitize((string)($config['recaman_start'] ?? 0)); ?>" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </label>
                <label class="text-sm">Fibonacci start
                    <input type="number" name="fibonacci_start" value="<?= sanitize((string)($config['fibonacci_start'] ?? 1)); ?>" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </label>
                <label class="text-sm">Boost interval (days)
                    <input type="number" min="1" name="exponential_growth_days" value="<?= sanitize((string)($config['exponential_growth_days'] ?? 2)); ?>" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </label>
            </div>
            <button class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md" type="submit">Save configuration</button>
        </form>
        <div class="border border-slate-200 rounded-lg p-4 overflow-y-auto max-h-64">
            <h3 class="text-lg font-semibold mb-2">Configuration history</h3>
            <?php if (!$configHistory): ?>
                <p class="text-sm text-slate-500">No changes recorded yet. Save an update to begin tracking.</p>
            <?php else: ?>
                <ul class="space-y-3 text-xs text-slate-600">
                    <?php foreach (array_reverse($configHistory) as $entry): ?>
                        <li>
                            <p class="font-semibold text-slate-700">Updated <?= sanitize(date('M d, Y H:i', strtotime($entry['updated_at'] ?? 'now'))); ?></p>
                            <p>Base: <?= number_format(($entry['changes']['base_percentage'] ?? 0) * 100, 1); ?>% | Increment: <?= number_format(($entry['changes']['extra_day_increment'] ?? 0) * 100, 1); ?>% | Boost every <?= (int)($entry['changes']['exponential_growth_days'] ?? 0); ?> days</p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Build passenger list for filtering in the chart
    $passengerNames = [];
    foreach ($predictions as $p) {
        if (!empty($p['passenger_name'])) {
            $passengerNames[$p['passenger_name']] = true;
        }
    }
    $passengerNames = array_keys($passengerNames);
    ?>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-semibold mb-1">Flight prediction chart</h3>
                <p class="text-sm text-slate-500">Arrival and departure probabilities over predicted dates. Uses Recamán + Fibonacci-based configuration.</p>
            </div>
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-slate-600" for="predictionPassenger">Passenger</label>
                <select id="predictionPassenger" class="bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="__ALL__">All passengers</option>
                    <?php foreach ($passengerNames as $name): ?>
                        <option value="<?= sanitize($name); ?>"><?= sanitize($name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mt-4 overflow-x-auto">
            <div id="predictionChart" class="w-full" style="height: 320px;"></div>
        </div>
        <?php if (!$predictions): ?>
            <p class="mt-3 text-xs text-slate-500">No predictions yet. Generate predictions to populate the chart.</p>
        <?php endif; ?>
    </div>

    <?php
        // Pagination data for predictions table (latest-first handled in controller)
        $pd = $predictionsPage ?? [
            'items' => array_reverse($predictions ?? []),
            'page' => 1,
            'pages' => 1,
            'total' => count($predictions ?? []),
            'perPage' => 10,
        ];
    ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium">Passenger</th>
                    <th class="px-3 py-2 text-left font-medium">Arrival Airport</th>
                    <th class="px-3 py-2 text-left font-medium">Departure Airport</th>
                    <th class="px-3 py-2 text-left font-medium">Arrival %</th>
                    <th class="px-3 py-2 text-left font-medium">Departure %</th>
                    <th class="px-3 py-2 text-left font-medium">Years</th>
                    <th class="px-3 py-2 text-left font-medium">Recamán</th>
                    <th class="px-3 py-2 text-left font-medium">Fibonacci</th>
                    <th class="px-3 py-2 text-left font-medium">Predicted Date</th>
                    <th class="px-3 py-2 text-left font-medium">Generated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!$pd['total']): ?>
                    <tr>
                        <td colspan="9" class="px-3 py-4 text-center text-slate-500">Run a prediction to populate this table.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($pd['items'] as $prediction): ?>
                    <tr>
                        <td class="px-3 py-2 font-semibold"><?= sanitize($prediction['passenger_name']); ?></td>
                        <td class="px-3 py-2"><?= sanitize($prediction['arrival_airport']); ?></td>
                        <td class="px-3 py-2"><?= sanitize($prediction['departure_airport']); ?></td>
                        <td class="px-3 py-2 text-emerald-600 font-semibold"><?= number_format($prediction['arrival_percentage'] * 100, 1); ?>%</td>
                        <td class="px-3 py-2 text-emerald-600 font-semibold"><?= number_format($prediction['departure_percentage'] * 100, 1); ?>%</td>
                        <td class="px-3 py-2"><?= (int)$prediction['prediction_years']; ?></td>
                        <td class="px-3 py-2 text-slate-500"><?= sanitize((string)$prediction['recaman_value']); ?></td>
                        <td class="px-3 py-2 text-slate-500"><?= sanitize((string)$prediction['fibonacci_value']); ?></td>
                        <td class="px-3 py-2 text-slate-500"><?= sanitize($prediction['predicted_date'] ?? 'n/a'); ?></td>
                        <td class="px-3 py-2 text-xs text-slate-500"><?= sanitize(date('M d, Y H:i', strtotime($prediction['generated_at']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (($pd['pages'] ?? 1) > 1): ?>
        <nav class="mt-4 flex items-center justify-between" aria-label="Pagination">
            <?php $prev = max(1, ($pd['page'] - 1)); $next = min($pd['pages'], ($pd['page'] + 1)); ?>
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] <= 1 ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=predictions&p=<?= $prev; ?>">Previous</a>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $pd['pages']; $i++): ?>
                    <a class="px-3 py-2 rounded-md <?= $i === $pd['page'] ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>" href="index.php?page=predictions&p=<?= $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] >= $pd['pages'] ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=predictions&p=<?= $next; ?>">Next</a>
        </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const allPredictions = <?= json_encode($predictions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
    const passengerSelect = document.getElementById('predictionPassenger');
    const predContainer = document.getElementById('predictionChart');

    function safeDateStr(p) {
        const d = p.predicted_date && p.predicted_date !== 'n/a' ? p.predicted_date : p.generated_at;
        return d;
    }

    function toSeries(filterName) {
        const map = new Map();
        for (const p of allPredictions) {
            if (!p) continue;
            if (filterName && filterName !== '__ALL__' && p.passenger_name !== filterName) continue;
            const dateStr = safeDateStr(p);
            if (!dateStr) continue;
            const key = new Date(dateStr);
            if (Number.isNaN(key.getTime())) continue;
            const k = key.toISOString().slice(0,10);
            if (!map.has(k)) map.set(k, { a: 0, d: 0, c: 0 });
            const rec = map.get(k);
            const a = Number(p.arrival_percentage ?? 0) * 100;
            const d = Number(p.departure_percentage ?? 0) * 100;
            rec.a += a;
            rec.d += d;
            rec.c += 1;
        }
        const rows = Array.from(map.entries()).sort((x, y) => new Date(x[0]) - new Date(y[0]));
        const arrival = rows.map(([k, v]) => ({ x: new Date(k), y: Number((v.a / v.c).toFixed(2)) }));
        const departure = rows.map(([k, v]) => ({ x: new Date(k), y: Number((v.d / v.c).toFixed(2)) }));
        return { arrival, departure };
    }

    function optionsFor(filterName) {
        const ds = toSeries(filterName);
        return {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            series: [
                { name: 'Arrival %', data: ds.arrival },
                { name: 'Departure %', data: ds.departure }
            ],
            stroke: { width: 2 },
            markers: { size: 3 },
            xaxis: { type: 'datetime' },
            yaxis: {
                min: 0,
                max: 100,
                labels: { formatter: (v) => `${Number(v).toFixed(0)}%` }
            },
            tooltip: { x: { format: 'MMM dd, yyyy' } },
            colors: ['#2563eb', '#10b981']
        };
    }

    if (predContainer) {
        let currentFilter = passengerSelect ? passengerSelect.value : '__ALL__';
        let chart = new ApexCharts(predContainer, optionsFor(currentFilter));
        chart.render();
        if (passengerSelect) {
            passengerSelect.addEventListener('change', (e) => {
                currentFilter = e.target.value;
                chart.updateOptions(optionsFor(currentFilter), false, true);
            });
        }
    }
</script>

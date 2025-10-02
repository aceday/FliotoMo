<?php
/** @var array $airports */
/** @var array $passengers */
/** @var array $flights */
/** @var array $predictions */
?>
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <?php
    $stats = [
        ['title' => 'Airports', 'value' => count($airports)],
        ['title' => 'Passengers', 'value' => count($passengers)],
        ['title' => 'Flights', 'value' => count($flights)],
        ['title' => 'Predictions', 'value' => count($predictions)],
    ];
    foreach ($stats as $stat): ?>
        <div class="bg-white shadow rounded-lg px-4 py-6">
            <h2 class="text-sm text-slate-500 uppercase tracking-wide"><?= $stat['title']; ?></h2>
            <p class="text-3xl font-semibold text-slate-900 mt-2"><?= $stat['value']; ?></p>
        </div>
    <?php endforeach; ?>
</section>

<section class="mt-10 grid gap-6 md:grid-cols-2">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-slate-800 mb-4">Quick Actions</h2>
        <ul class="space-y-3">
            <li><a class="text-blue-600 hover:underline" href="index.php?page=airports">Manage airports</a></li>
            <li><a class="text-blue-600 hover:underline" href="index.php?page=passengers">Manage passengers</a></li>
            <li><a class="text-blue-600 hover:underline" href="index.php?page=flights">Manage flights</a></li>
            <li><a class="text-blue-600 hover:underline" href="index.php?page=predictions">Run predictions</a></li>
        </ul>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-slate-800 mb-4">How predictions work</h2>
        <p class="text-sm text-slate-600 leading-relaxed">
            FliotoMo analyzes historical flights per passenger, applies Recamán and Fibonacci sequences to
            identify cadence patterns, and projects their likelihood of booking new flights within 1–5 years.
            Base probabilities receive incremental boosts every few days and cap at 95% confidence.
        </p>
    </div>
</section>

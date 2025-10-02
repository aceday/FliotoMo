<?php
/** @var array $airports */
/** @var array|null $airportsPage */
/** @var string|null $success */
$error = $_GET['error'] ?? null;
$editingId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editing = $editingId ? find_by_id($airports, $editingId) : null;
// Pagination data (10 per page)
$pd = $airportsPage ?? [
    'items' => $airports,
    'page' => 1,
    'pages' => 1,
    'total' => count($airports ?? []),
    'perPage' => 10,
];
?>
<?= success_message($success ?? null); ?>
<?= error_message($error); ?>
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
        <h2 class="text-2xl font-semibold">Airport Codes</h2>
        <p class="text-sm text-slate-500">Maintain active airport references. Archived codes stay searchable for flight history.</p>
    </div>
    <form method="post" class="grid md:grid-cols-4 gap-3 items-end mb-6">
        <input type="hidden" name="action" value="<?= $editing ? 'update_airport' : 'create_airport'; ?>">
        <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= (int)$editing['id']; ?>">
        <?php endif; ?>
        <div>
            <label class="block text-sm font-medium text-slate-600">Code</label>
            <input class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="code" value="<?= sanitize($editing['code'] ?? '') ?>" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Name</label>
            <input class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="name" value="<?= sanitize($editing['name'] ?? '') ?>" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">City</label>
            <input class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="city" value="<?= sanitize($editing['city'] ?? '') ?>" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Country</label>
            <input class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="country" value="<?= sanitize($editing['country'] ?? '') ?>" />
        </div>
        <div class="md:col-span-4 flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-md" type="submit">
                <?= $editing ? 'Update Airport' : 'Add Airport'; ?>
            </button>
            <?php if ($editing): ?>
                <a class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md" href="index.php?page=airports">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium">Code</th>
                    <th class="px-3 py-2 text-left font-medium">Name</th>
                    <th class="px-3 py-2 text-left font-medium">Location</th>
                    <th class="px-3 py-2 text-left font-medium">Status</th>
                    <th class="px-3 py-2 text-left font-medium">Updated</th>
                    <th class="px-3 py-2 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!$pd['total']): ?>
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-slate-500">No airports yet. Add the first above.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($pd['items'] as $airport): ?>
                    <tr class="<?= ($airport['archived'] ?? false) ? 'opacity-60' : ''; ?>">
                        <td class="px-3 py-2 font-semibold"><?= sanitize($airport['code']); ?></td>
                        <td class="px-3 py-2"><?= sanitize($airport['name']); ?></td>
                        <td class="px-3 py-2"><?= sanitize(trim(($airport['city'] ?? '') . ', ' . ($airport['country'] ?? ''), ', ')); ?></td>
                        <td class="px-3 py-2">
                            <?php if (($airport['archived'] ?? false)): ?>
                                <span class="px-2 py-1 bg-slate-200 rounded">Archived</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 text-slate-500 text-xs">
                            <?= sanitize(date('M d, Y H:i', strtotime($airport['updated_at'] ?? 'now'))); ?>
                        </td>
                        <td class="px-3 py-2 text-right space-x-2">
                            <a class="text-blue-600 hover:underline" href="index.php?page=airports&edit=<?= (int)$airport['id']; ?>">Edit</a>
                            <form class="inline" method="post" onsubmit="return confirm('Toggle archive status for this airport?');">
                                <input type="hidden" name="action" value="toggle_airport">
                                <input type="hidden" name="id" value="<?= (int)$airport['id']; ?>">
                                <button class="text-slate-600 hover:underline" type="submit">Toggle</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (($pd['pages'] ?? 1) > 1): ?>
        <nav class="mt-4 flex items-center justify-between" aria-label="Pagination">
            <?php $prev = max(1, ($pd['page'] - 1)); $next = min($pd['pages'], ($pd['page'] + 1)); ?>
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] <= 1 ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=airports&p=<?= $prev; ?>">Previous</a>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $pd['pages']; $i++): ?>
                    <a class="px-3 py-2 rounded-md <?= $i === $pd['page'] ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>" href="index.php?page=airports&p=<?= $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] >= $pd['pages'] ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=airports&p=<?= $next; ?>">Next</a>
        </nav>
    <?php endif; ?>
</div>

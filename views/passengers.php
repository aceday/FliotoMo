<?php
/** @var array $passengers */
/** @var array|null $passengersPage */
/** @var string|null $success */
$error = $_GET['error'] ?? null;
$editingId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editing = $editingId ? find_by_id($passengers, $editingId) : null;
// Pagination
$pd = $passengersPage ?? [
    'items' => $passengers,
    'page' => 1,
    'pages' => 1,
    'total' => count($passengers ?? []),
    'perPage' => 10,
];
?>
<?= success_message($success ?? null); ?>
<?= error_message($error); ?>
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
        <h2 class="text-2xl font-semibold">Passengers</h2>
        <p class="text-sm text-slate-500">Archive passengers to keep their record while removing them from active selection.</p>
    </div>
    <form method="post" class="grid md:grid-cols-3 gap-3 items-end mb-6">
        <input type="hidden" name="action" value="<?= $editing ? 'update_passenger' : 'create_passenger'; ?>">
        <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= (int)$editing['id']; ?>">
        <?php endif; ?>
        <div class="md:col-span-1">
            <label class="block text-sm font-medium text-slate-600">Full name</label>
            <input class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="name" value="<?= sanitize($editing['name'] ?? '') ?>" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Email</label>
            <input type="email" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="email" value="<?= sanitize($editing['email'] ?? '') ?>" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Phone</label>
            <input class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="phone" value="<?= sanitize($editing['phone'] ?? '') ?>" />
        </div>
        <div class="md:col-span-3">
            <label class="block text-sm font-medium text-slate-600">Notes</label>
            <textarea class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="notes" rows="2"><?= sanitize($editing['notes'] ?? '') ?></textarea>
        </div>
        <div class="md:col-span-3 flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-md" type="submit">
                <?= $editing ? 'Update Passenger' : 'Add Passenger'; ?>
            </button>
            <?php if ($editing): ?>
                <a class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md" href="index.php?page=passengers">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium">Name</th>
                    <th class="px-3 py-2 text-left font-medium">Email</th>
                    <th class="px-3 py-2 text-left font-medium">Phone</th>
                    <th class="px-3 py-2 text-left font-medium">Status</th>
                    <th class="px-3 py-2 text-left font-medium">Updated</th>
                    <th class="px-3 py-2 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!$pd['total']): ?>
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-slate-500">No passengers recorded yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($pd['items'] as $passenger): ?>
                    <tr class="<?= ($passenger['archived'] ?? false) ? 'opacity-60' : ''; ?>">
                        <td class="px-3 py-2 font-semibold"><?= sanitize($passenger['name']); ?></td>
                        <td class="px-3 py-2 text-slate-500"><?= sanitize($passenger['email'] ?? ''); ?></td>
                        <td class="px-3 py-2 text-slate-500"><?= sanitize($passenger['phone'] ?? ''); ?></td>
                        <td class="px-3 py-2">
                            <?php if (($passenger['archived'] ?? false)): ?>
                                <span class="px-2 py-1 bg-slate-200 rounded">Archived</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 text-slate-500 text-xs">
                            <?= sanitize(date('M d, Y H:i', strtotime($passenger['updated_at'] ?? 'now'))); ?>
                        </td>
                        <td class="px-3 py-2 text-right space-x-2">
                            <a class="text-blue-600 hover:underline" href="index.php?page=passengers&edit=<?= (int)$passenger['id']; ?>">Edit</a>
                            <form class="inline" method="post" onsubmit="return confirm('Toggle archive status for this passenger?');">
                                <input type="hidden" name="action" value="toggle_passenger">
                                <input type="hidden" name="id" value="<?= (int)$passenger['id']; ?>">
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
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] <= 1 ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=passengers&p=<?= $prev; ?>">Previous</a>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $pd['pages']; $i++): ?>
                    <a class="px-3 py-2 rounded-md <?= $i === $pd['page'] ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>" href="index.php?page=passengers&p=<?= $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] >= $pd['pages'] ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=passengers&p=<?= $next; ?>">Next</a>
        </nav>
    <?php endif; ?>
</div>

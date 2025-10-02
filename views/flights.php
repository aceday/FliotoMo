<?php
/** @var array $flights */
/** @var array|null $flightsPage */
/** @var array $passengers */
/** @var array $airports */
/** @var string|null $success */
$error = $_GET['error'] ?? null;
$editingId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editing = $editingId ? find_by_id($flights, $editingId) : null;
// Pagination
$pd = $flightsPage ?? [
    'items' => $flights,
    'page' => 1,
    'pages' => 1,
    'total' => count($flights ?? []),
    'perPage' => 10,
];
?>
<?= success_message($success ?? null); ?>
<?= error_message($error); ?>
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
        <h2 class="text-2xl font-semibold">Flights</h2>
        <p class="text-sm text-slate-500">Link passengers with airport codes and track schedule notes. Archived flights remain for analytics.</p>
    </div>
    <form method="post" class="grid md:grid-cols-3 gap-3 items-end mb-6">
        <input type="hidden" name="action" value="<?= $editing ? 'update_flight' : 'create_flight'; ?>">
        <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= (int)$editing['id']; ?>">
        <?php endif; ?>
        <div>
            <label class="block text-sm font-medium text-slate-600">Passenger</label>
            <!-- Hidden native select (kept as the form's value carrier) -->
            <select id="passengerSelect" class="hidden" name="passenger_id" required>
                <option value="">Select passenger</option>
                <?php foreach ($passengers as $passenger): ?>
                    <option value="<?= (int)$passenger['id']; ?>" <?= (isset($editing['passenger_id']) && (int)$editing['passenger_id'] === (int)$passenger['id']) ? 'selected' : ''; ?>>
                        <?= sanitize($passenger['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <!-- Visible control to open/search modal (now typeable) -->
            <div class="mt-1 flex gap-2">
                <input id="passengerDisplay" type="text" class="flex-1 bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search or select passenger" value="">
                <button id="openPassengerModal" type="button" class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300">Find</button>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Departure airport</label>
            <div class="mt-1 flex gap-2">
                <input id="departureAirportInput" class="flex-1 bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="departure_airport" value="<?= sanitize($editing['departure_airport'] ?? '') ?>" placeholder="e.g., MNL" required />
                <button id="openDepartureAirportModal" type="button" class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300">Find</button>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Arrival airport</label>
            <div class="mt-1 flex gap-2">
                <input id="arrivalAirportInput" class="flex-1 bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="arrival_airport" value="<?= sanitize($editing['arrival_airport'] ?? '') ?>" placeholder="e.g., CEB" required />
                <button id="openArrivalAirportModal" type="button" class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300">Find</button>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Departure time</label>
            <input type="datetime-local" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="departure_time" value="<?= sanitize($editing['departure_time'] ?? '') ?>" required />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Arrival time</label>
            <input type="datetime-local" class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="arrival_time" value="<?= sanitize($editing['arrival_time'] ?? '') ?>" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600">Flight number</label>
            <input class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="flight_number" value="<?= sanitize($editing['flight_number'] ?? '') ?>" />
        </div>
        <div class="md:col-span-3">
            <label class="block text-sm font-medium text-slate-600">Notes</label>
            <textarea class="mt-1 w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="notes" rows="2"><?= sanitize($editing['notes'] ?? '') ?></textarea>
        </div>
        <div class="md:col-span-3 flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-md" type="submit">
                <?= $editing ? 'Update Flight' : 'Add Flight'; ?>
            </button>
            <?php if ($editing): ?>
                <a class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md" href="index.php?page=flights">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium">Passenger</th>
                    <th class="px-3 py-2 text-left font-medium">Route</th>
                    <th class="px-3 py-2 text-left font-medium">Schedule</th>
                    <th class="px-3 py-2 text-left font-medium">Flight #</th>
                    <th class="px-3 py-2 text-left font-medium">Status</th>
                    <th class="px-3 py-2 text-left font-medium">Updated</th>
                    <th class="px-3 py-2 text-right font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!$pd['total']): ?>
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-slate-500">No flights tracked yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($pd['items'] as $flight):
                    $passenger = find_by_id($passengers, (int)($flight['passenger_id'] ?? 0));
                ?>
                    <tr class="<?= ($flight['archived'] ?? false) ? 'opacity-60' : ''; ?>">
                        <td class="px-3 py-2 font-semibold"><?= sanitize($passenger['name'] ?? 'Unknown'); ?></td>
                        <td class="px-3 py-2">
                            <?= sanitize($flight['departure_airport'] ?? ''); ?>
                            <span class="text-slate-400">→</span>
                            <?= sanitize($flight['arrival_airport'] ?? ''); ?>
                        </td>
                        <td class="px-3 py-2 text-xs text-slate-500">
                            Departs: <?= sanitize($flight['departure_time'] ?? 'n/a'); ?><br>
                            Arrives: <?= sanitize($flight['arrival_time'] ?? 'n/a'); ?>
                        </td>
                        <td class="px-3 py-2 text-slate-500"><?= sanitize($flight['flight_number'] ?? ''); ?></td>
                        <td class="px-3 py-2">
                            <?php if (($flight['archived'] ?? false)): ?>
                                <span class="px-2 py-1 bg-slate-200 rounded">Archived</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 text-slate-500 text-xs">
                            <?= sanitize(date('M d, Y H:i', strtotime($flight['updated_at'] ?? 'now'))); ?>
                        </td>
                        <td class="px-3 py-2 text-right space-x-2">
                            <a class="text-blue-600 hover:underline" href="index.php?page=flights&edit=<?= (int)$flight['id']; ?>">Edit</a>
                            <form class="inline" method="post" onsubmit="return confirm('Toggle archive status for this flight?');">
                                <input type="hidden" name="action" value="toggle_flight">
                                <input type="hidden" name="id" value="<?= (int)$flight['id']; ?>">
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
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] <= 1 ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=flights&p=<?= $prev; ?>">Previous</a>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $pd['pages']; $i++): ?>
                    <a class="px-3 py-2 rounded-md <?= $i === $pd['page'] ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' ?>" href="index.php?page=flights&p=<?= $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <a class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 <?= $pd['page'] >= $pd['pages'] ? 'opacity-60 pointer-events-none' : '' ?>" href="index.php?page=flights&p=<?= $next; ?>">Next</a>
        </nav>
    <?php endif; ?>
</div>

<!-- Passenger Search Modal -->
<div id="passengerModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="passengerModalTitle">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="absolute inset-0 flex items-start md:items-center justify-center p-4">
        <div class="w-full max-w-xl bg-white rounded-lg shadow-lg ring-1 ring-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                <h3 id="passengerModalTitle" class="text-lg font-semibold">Select passenger</h3>
                <button id="closePassengerModal" type="button" class="p-2 rounded-md text-slate-600 hover:bg-slate-100" aria-label="Close">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-4">
                <input id="passengerSearch" type="text" class="w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search by name, email, or phone">
                <div id="passengerList" class="mt-3 max-h-72 overflow-y-auto divide-y divide-slate-100"></div>
                <?php if (!$passengers): ?>
                    <p class="mt-3 text-sm text-slate-500">No passengers yet. Add one on the Passengers page.</p>
                <?php endif; ?>
            </div>
            <div class="px-4 py-3 border-t border-slate-200 flex justify-end">
                <button id="cancelPassengerModal" type="button" class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // JSON data for quick client-side search
    const passengersData = <?= json_encode(array_map(function($p){
        return [
            'id' => (int)($p['id'] ?? 0),
            'name' => (string)($p['name'] ?? ''),
            'email' => (string)($p['email'] ?? ''),
            'phone' => (string)($p['phone'] ?? ''),
        ];
    }, $passengers), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    const sel = document.getElementById('passengerSelect');
    const display = document.getElementById('passengerDisplay');
    const openBtn = document.getElementById('openPassengerModal');
    const modal = document.getElementById('passengerModal');
    const closeBtn = document.getElementById('closePassengerModal');
    const cancelBtn = document.getElementById('cancelPassengerModal');
    const searchInput = document.getElementById('passengerSearch');
    const listEl = document.getElementById('passengerList');

    function setDisplayFromSelect() {
        if (!sel) return;
        const opt = sel.options[sel.selectedIndex];
        const raw = opt && opt.value ? opt.textContent : '';
        // Collapse internal whitespace and trim edges
        display.value = String(raw).replace(/\s+/g, ' ').trim();
    }

    function openModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => searchInput && searchInput.focus(), 0);
        renderList('');
    }
    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function renderList(query) {
        if (!listEl) return;
        const q = (query || '').toLowerCase();
        const items = passengersData.filter(p =>
            !q || (p.name && p.name.toLowerCase().includes(q)) || (p.email && p.email.toLowerCase().includes(q)) || (p.phone && p.phone.toLowerCase().includes(q))
        );
        listEl.innerHTML = '';
        for (const p of items) {
            const row = document.createElement('button');
            row.type = 'button';
            row.className = 'w-full text-left px-3 py-2 hover:bg-slate-50 flex items-center justify-between';
            const niceName = String(p.name || '').replace(/\s+/g, ' ').trim();
            row.innerHTML = `<div><div class=\"font-medium text-slate-900\">${escapeHtml(niceName)}</div><div class=\"text-xs text-slate-500\">${escapeHtml(p.email || '')}${p.email && p.phone ? ' • ' : ''}${escapeHtml(p.phone || '')}</div></div>`;
            row.addEventListener('click', () => {
                for (let i = 0; i < sel.options.length; i++) {
                    if (String(sel.options[i].value) === String(p.id)) {
                        sel.selectedIndex = i;
                        break;
                    }
                }
                setDisplayFromSelect();
                closeModal();
            });
            listEl.appendChild(row);
        }
        if (items.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'px-3 py-8 text-center text-sm text-slate-500';
            empty.textContent = 'No matching passengers';
            listEl.appendChild(empty);
        }
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    // Wire up (passenger)
    setDisplayFromSelect();
    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (modal) modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    if (searchInput) searchInput.addEventListener('input', (e) => renderList(e.target.value));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    // Make the visible passenger input type-to-search
    if (display) {
        display.addEventListener('focus', () => {
            if (modal.classList.contains('hidden')) openModal();
            if (searchInput) {
                searchInput.value = display.value || '';
                renderList(searchInput.value);
            }
        });
        display.addEventListener('input', () => {
            if (modal.classList.contains('hidden')) openModal();
            if (searchInput) {
                searchInput.value = display.value || '';
                renderList(searchInput.value);
            }
        });
    }

    // Airports modal (reusable for departure/arrival)
    const airportsData = <?= json_encode(array_map(function($a){
        return [
            'code' => (string)($a['code'] ?? ''),
            'name' => (string)($a['name'] ?? ''),
            'city' => (string)($a['city'] ?? ''),
            'country' => (string)($a['country'] ?? ''),
        ];
    }, $airports), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    // Create airport modal elements once
    const airportModal = document.createElement('div');
    airportModal.id = 'airportModal';
    airportModal.className = 'fixed inset-0 z-50 hidden';
    airportModal.innerHTML = `
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 flex items-start md:items-center justify-center p-4">
            <div class="w-full max-w-xl bg-white rounded-lg shadow-lg ring-1 ring-slate-200 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
                    <h3 class="text-lg font-semibold">Select airport</h3>
                    <button id="closeAirportModal" type="button" class="p-2 rounded-md text-slate-600 hover:bg-slate-100" aria-label="Close">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-4">
                    <input id="airportSearch" type="text" class="w-full bg-white border border-slate-300 rounded-md px-3 py-2 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search by code, name, city, or country">
                    <div id="airportList" class="mt-3 max-h-72 overflow-y-auto divide-y divide-slate-100"></div>
                </div>
                <div class="px-4 py-3 border-t border-slate-200 flex justify-end">
                    <button id="cancelAirportModal" type="button" class="px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300">Cancel</button>
                </div>
            </div>
        </div>`;
    document.body.appendChild(airportModal);

    const depInput = document.getElementById('departureAirportInput');
    const arrInput = document.getElementById('arrivalAirportInput');
    const depBtn = document.getElementById('openDepartureAirportModal');
    const arrBtn = document.getElementById('openArrivalAirportModal');

    let airportTarget = null; // HTMLInputElement to fill
    const airportSearch = () => document.getElementById('airportSearch');
    const airportList = () => document.getElementById('airportList');
    const closeAirportBtn = () => document.getElementById('closeAirportModal');
    const cancelAirportBtn = () => document.getElementById('cancelAirportModal');

    function openAirportModal(targetInput, preset='') {
        airportTarget = targetInput;
        airportModal.classList.remove('hidden');
        const input = airportSearch();
        if (input) {
            input.value = preset || '';
            renderAirportList(input.value);
            setTimeout(() => input.focus(), 0);
        }
    }
    function closeAirportModal() {
        airportModal.classList.add('hidden');
        airportTarget = null;
    }
    function renderAirportList(query) {
        const q = (query || '').toLowerCase();
        const items = airportsData.filter(a => !q ||
            (a.code && a.code.toLowerCase().includes(q)) ||
            (a.name && a.name.toLowerCase().includes(q)) ||
            (a.city && a.city.toLowerCase().includes(q)) ||
            (a.country && a.country.toLowerCase().includes(q))
        );
        const list = airportList();
        if (!list) return;
        list.innerHTML = '';
        for (const a of items) {
            const row = document.createElement('button');
            row.type = 'button';
            row.className = 'w-full text-left px-3 py-2 hover:bg-slate-50 flex items-center justify-between';
            row.innerHTML = `<div><div class="font-medium text-slate-900">${escapeHtml(a.code)} — ${escapeHtml(a.name)}</div><div class="text-xs text-slate-500">${escapeHtml(a.city)}${a.city && a.country ? ', ' : ''}${escapeHtml(a.country)}</div></div>`;
            row.addEventListener('click', () => {
                if (airportTarget) airportTarget.value = a.code;
                closeAirportModal();
            });
            list.appendChild(row);
        }
        if (items.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'px-3 py-8 text-center text-sm text-slate-500';
            empty.textContent = 'No matching airports';
            list.appendChild(empty);
        }
    }

    if (depBtn && depInput) depBtn.addEventListener('click', () => openAirportModal(depInput, depInput.value));
    if (arrBtn && arrInput) arrBtn.addEventListener('click', () => openAirportModal(arrInput, arrInput.value));
    airportModal.addEventListener('click', (e) => { if (e.target === airportModal) closeAirportModal(); });
    document.addEventListener('keydown', (e) => { if (!airportModal.classList.contains('hidden') && e.key === 'Escape') closeAirportModal(); });
    if (closeAirportBtn()) closeAirportBtn().addEventListener('click', closeAirportModal);
    if (cancelAirportBtn()) cancelAirportBtn().addEventListener('click', closeAirportModal);
    if (airportSearch()) airportSearch().addEventListener('input', (e) => renderAirportList(e.target.value));
</script>

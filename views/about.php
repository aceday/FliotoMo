<?php
/** @var array $airports */
/** @var array $passengers */
/** @var array $flights */
/** @var array $predictions */
?>
<div class="bg-white shadow rounded-lg p-6">
  <h2 class="text-2xl font-semibold mb-2">About FliotoMo</h2>
  <p class="text-slate-600 mb-4">FliotoMo is a lightweight, JSON‑backed airline management demo. It helps you manage airports, passengers, and flights, and explore a simple prediction model for arrivals/departures.</p>

  <p class="font-bold">Members:</p>
  <p>Mark Cedie Buday</p>
  <p>John Owen Baguion</p>
  <p>John Matthew Dipad</p>
  <br>
  
  <p>"Fly like a pro with FliotoMo is your friend"</p>

  <h3 class="text-lg font-semibold mt-4 mb-2">Key features</h3>
  <ul class="list-disc ml-6 text-slate-700 space-y-1">


    <li>CRUD for Airports, Passengers, and Flights</li>
    <li>Searchable modals for picking Passengers and Airports</li>
    <li>Prediction engine using Recamán + Fibonacci patterns</li>
    <li>Pagination on large lists (10 per page)</li>
    <li>Mobile‑friendly UI with Tailwind CSS</li>
  </ul>

  <h3 class="text-lg font-semibold mt-6 mb-2">Data model (JSON)</h3>
  <div class="grid md:grid-cols-2 gap-4 text-sm">
    <div class="bg-slate-50 rounded p-3 border border-slate-200">
      <div class="font-mono text-slate-800 mb-2">airports.json</div>
      <pre class="text-xs leading-relaxed whitespace-pre-wrap">[
  {
    "id": 1,
    "code": "MNL",
    "name": "Ninoy Aquino Intl",
    "city": "Manila",
    "country": "Philippines",
    "archived": false,
    "updated_at": "2025-10-02T10:00:00Z"
  }
]</pre>
    </div>
    <div class="bg-slate-50 rounded p-3 border border-slate-200">
      <div class="font-mono text-slate-800 mb-2">passengers.json</div>
      <pre class="text-xs leading-relaxed whitespace-pre-wrap">[
  {
    "id": 1,
    "name": "Juan Dela Cruz",
    "email": "juan@example.com",
    "phone": "+63 912 345 6789",
    "archived": false,
    "notes": "",
    "updated_at": "2025-10-02T10:00:00Z"
  }
]</pre>
    </div>
    <div class="bg-slate-50 rounded p-3 border border-slate-200">
      <div class="font-mono text-slate-800 mb-2">flights.json</div>
      <pre class="text-xs leading-relaxed whitespace-pre-wrap">[
  {
    "id": 1,
    "passenger_id": 1,
    "departure_airport": "MNL",
    "arrival_airport": "CEB",
    "departure_time": "2025-10-05T09:00",
    "arrival_time": "2025-10-05T10:30",
    "flight_number": "AA101",
    "archived": false,
    "notes": "",
    "updated_at": "2025-10-02T10:00:00Z"
  }
]</pre>
    </div>
    <div class="bg-slate-50 rounded p-3 border border-slate-200">
      <div class="font-mono text-slate-800 mb-2">predictions.json</div>
      <pre class="text-xs leading-relaxed whitespace-pre-wrap">[
  {
    "id": 1,
    "flight_id": 1,
    "predicted_date": "2025-10-06",
    "arrival_probability": 0.62,
    "departure_probability": 0.58,
    "config": { /* configuration snapshot */ }
  }
]</pre>
    </div>
  </div>

  <h3 class="text-lg font-semibold mt-6 mb-2">Tips</h3>
  <ul class="list-disc ml-6 text-slate-700 space-y-1">
    <li>Use the Flights page to link a passenger between two airport codes.</li>
    <li>Click “Find” to search passengers and airports quickly.</li>
    <li>Generate predictions from the Predictions page; tune parameters in the Config panel.</li>
  </ul>
</div>

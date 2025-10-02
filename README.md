# FliotoMo — Airlines Management System Suite

FliotoMo is a lightweight PHP + Tailwind CSS web console for managing AA Airlines customer data, airport codes, flight histories, and predictive insights. All state is saved to JSON files so the app runs without an external database.

## Features

- **Dashboard** with key metrics and quick navigation
- **Airport registry**: add, edit, archive airport codes while keeping history intact
- **Passenger directory**: maintain contact info, archive former flyers
- **Flight tracker**: link passengers with airport codes, manage schedules, archive old flights
- **Predictive analytics**: generate future flight likelihoods using Recamán + Fibonacci series with configurable boosts (50–75% range)
- **Configuration history**: adjust probability parameters and review change log
- **Forex insight**: crypto-style candlestick view of PHP exchange rates to anticipate fare sensitivity alongside predictions

## Project structure

```
FliotoMo/
├── data/
│   ├── airports.json
│   ├── passengers.json
│   ├── flights.json
│   ├── predictions.json
│   ├── config.json
│   ├── config_history.json
│   └── forex.json
├── lib/
│   ├── helpers.php
│   ├── storage.php
│   └── patterns.php
├── partials/
│   ├── header.php
│   └── footer.php
├── views/
│   ├── airports.php
│   ├── dashboard.php
│   ├── flights.php
│   ├── passengers.php
│   └── predictions.php
└── index.php
```

## Requirements

- PHP 8.0+ (tested with CLI server)
- Internet access for Tailwind CSS CDN (or swap to a local build if preferred)

## Getting started

```bash
cd FliotoMo
php -S localhost:8000
```

Open <http://localhost:8000> in your browser. All data writes go to the `data/` directory beside your PHP files.

## Data management

- JSON files are pretty-printed for manual editing or backup.
- Each record carries an `archived` flag; archived rows stay available for analytics while hidden from active dropdowns.
- Prediction configuration is saved in `config.json` and every change is appended to `config_history.json` with timestamps.
- Populate `forex.json` with historical rates (sample provided) to enable the forex chart on the predictions page. Each entry should contain `open`, `high`, `low`, and `close` values per currency for the candlestick renderer.

## Prediction logic overview

- Base probability starts at 50% (configurable between 30–95%).
- Every configurable `exponential_growth_days` (default 2) adds an extra 5% boost capped at 75%.
- Recamán and Fibonacci series are combined to determine future-day offsets and enrich confidence scores.
- Predictions include an estimated reservation date, arrival/departure airport guesses, and stored percentages.

## Troubleshooting

- If you see write-permission errors, ensure the `data/` folder is writable by PHP.
- Tailwind CDN issues? Replace the `<script src=...>` tag in `partials/header.php` with a local tailwind build.
- Reset the app by clearing the JSON files or deleting the `data/` directory (FliotoMo will recreate empty files).

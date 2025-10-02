<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';

function recaman_sequence(int $n, int $start = 0): array
{
    $sequence = [];
    $seen = [];
    $current = $start;
    for ($i = 0; $i < $n; $i++) {
        $next = $current - $i;
        if ($next > 0 && !isset($seen[$next])) {
            $current = $next;
        } else {
            $current = $current + $i;
        }
        $sequence[] = $current;
        $seen[$current] = true;
    }
    return $sequence;
}

function fibonacci_sequence(int $n, int $start1 = 0, int $start2 = 1): array
{
    if ($n <= 0) {
        return [];
    }
    if ($n === 1) {
        return [$start1];
    }

    $sequence = [$start1, $start2];
    for ($i = 2; $i < $n; $i++) {
        $sequence[$i] = $sequence[$i - 1] + $sequence[$i - 2];
    }
    return $sequence;
}

function calculate_prediction_percentages(int $days, float $basePercentage, float $increment, int $expDays): float
{
    $percentage = $basePercentage;
    for ($day = $expDays; $day <= $days; $day += $expDays) {
        $percentage += $increment;
    }
    return min($percentage, 0.75);
}

function generate_predictions(array $flights, array $passengers, array $airports, array $config, int $years): array
{
    $results = [];
    $base = (float)($config['base_percentage'] ?? 0.5);
    $increment = (float)($config['extra_day_increment'] ?? 0.05);
    $expDays = (int)($config['exponential_growth_days'] ?? 2);

    $recaman = recaman_sequence(max($years * 12, 12), (int)($config['recaman_start'] ?? 0));
    $fibStart = (int)($config['fibonacci_start'] ?? 1);
    $fibonacci = fibonacci_sequence(max($years + 2, 2), $fibStart, $fibStart + 1);

    foreach ($passengers as $passenger) {
        $pid = $passenger['id'];
        $passengerFlights = array_filter($flights, fn($flight) => (int)($flight['passenger_id'] ?? 0) === $pid && ($flight['archived'] ?? false) === false);
        if (!$passengerFlights) {
            continue;
        }
        $lastFlight = end($passengerFlights);
        $arrival = $lastFlight['arrival_airport'] ?? '';
        $departure = $lastFlight['departure_airport'] ?? '';

        $recamanIndex = ($pid - 1) % max(count($recaman), 1);
        $fibonacciIndex = min($years, count($fibonacci) - 1);
        $recamanValue = $recaman[$recamanIndex] ?? 0;
        $fibonacciValue = $fibonacci[$fibonacciIndex] ?? 0;
        $daysAhead = max(30, ($years * 180) + $recamanValue + $fibonacciValue);
        $arrivalPct = calculate_prediction_percentages($daysAhead, $base, $increment, $expDays);
        $departurePct = min($arrivalPct + ($increment / 2), 0.75);

        $lastDeparture = strtotime($lastFlight['departure_time'] ?? 'now');
        if ($lastDeparture === false) {
            $lastDeparture = time();
        }
        $predictedDate = date('Y-m-d', strtotime('+' . $daysAhead . ' days', $lastDeparture));

        $results[] = [
            'id' => uniqid('pred_', true),
            'passenger_name' => $passenger['name'] ?? 'Unknown',
            'arrival_airport' => $arrival,
            'departure_airport' => $departure,
            'arrival_percentage' => round($arrivalPct, 2),
            'departure_percentage' => round($departurePct, 2),
            'prediction_years' => $years,
            'generated_at' => date('c'),
            'predicted_date' => $predictedDate,
            'recaman_value' => $recamanValue,
            'fibonacci_value' => $fibonacciValue,
        ];
    }

    return $results;
}

<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/storage.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/patterns.php';

$page = $_GET['page'] ?? 'dashboard';
$errors = [];
$success = $_GET['success'] ?? null;

$airports = load_data('airports.json');
$passengers = load_data('passengers.json');
$flights = load_data('flights.json');
$predictions = load_data('predictions.json');
$config = load_data('config.json');
$configHistory = load_data('config_history.json');
$forexRates = load_data('forex.json', [
    'base_currency' => 'PHP',
    'currencies' => [],
    'series' => [],
    'updated_at' => null,
]);

function render(string $view, array $data = []): void
{
    extract($data);
    include __DIR__ . '/partials/header.php';
    include __DIR__ . '/views/' . $view . '.php';
    include __DIR__ . '/partials/footer.php';
    exit;
}

/**
 * Simple array pagination helper.
 * @return array{items:array,page:int,pages:int,total:int,perPage:int}
 */
function paginate(array $items, int $perPage = 10, string $param = 'p'): array
{
    $total = count($items);
    $pages = max(1, (int)ceil($total / $perPage));
    $page = isset($_GET[$param]) ? (int)$_GET[$param] : 1;
    if ($page < 1) { $page = 1; }
    if ($page > $pages) { $page = $pages; }
    $offset = ($page - 1) * $perPage;
    $slice = array_slice($items, $offset, $perPage);
    return [
        'items' => $slice,
        'page' => $page,
        'pages' => $pages,
        'total' => $total,
        'perPage' => $perPage,
    ];
}

function handle_post(): void
{
    global $airports, $passengers, $flights, $predictions, $config, $configHistory;
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create_airport':
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $name = trim($_POST['name'] ?? '');
            $city = trim($_POST['city'] ?? '');
            $country = trim($_POST['country'] ?? '');

            if ($code === '' || $name === '') {
                redirect('index.php?page=airports&error=Missing+required+fields');
            }

            foreach ($airports as $airport) {
                if (strtoupper($airport['code']) === $code) {
                    redirect('index.php?page=airports&error=Airport+code+already+exists');
                }
            }

            $airports[] = [
                'id' => generate_id($airports),
                'code' => $code,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'archived' => false,
                'updated_at' => date('c'),
            ];
            save_data('airports.json', $airports);
            redirect('index.php?page=airports&success=Airport+added');
        case 'update_airport':
            $id = (int)($_POST['id'] ?? 0);
            $airports = update_record($airports, $id, [
                'code' => strtoupper(trim($_POST['code'] ?? '')),
                'name' => trim($_POST['name'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'country' => trim($_POST['country'] ?? ''),
                'updated_at' => date('c'),
            ]);
            save_data('airports.json', $airports);
            redirect('index.php?page=airports&success=Airport+updated');
        case 'toggle_airport':
            $id = (int)($_POST['id'] ?? 0);
            foreach ($airports as &$airport) {
                if ((int)$airport['id'] === $id) {
                    $airport['archived'] = !$airport['archived'];
                    $airport['updated_at'] = date('c');
                    break;
                }
            }
            unset($airport);
            save_data('airports.json', $airports);
            redirect('index.php?page=airports&success=Airport+status+updated');
        case 'create_passenger':
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            if ($name === '') {
                redirect('index.php?page=passengers&error=Name+is+required');
            }
            $passengers[] = [
                'id' => generate_id($passengers),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'archived' => false,
                'notes' => trim($_POST['notes'] ?? ''),
                'updated_at' => date('c'),
            ];
            save_data('passengers.json', $passengers);
            redirect('index.php?page=passengers&success=Passenger+added');
        case 'update_passenger':
            $id = (int)($_POST['id'] ?? 0);
            $passengers = update_record($passengers, $id, [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'updated_at' => date('c'),
            ]);
            save_data('passengers.json', $passengers);
            redirect('index.php?page=passengers&success=Passenger+updated');
        case 'toggle_passenger':
            $id = (int)($_POST['id'] ?? 0);
            foreach ($passengers as &$passenger) {
                if ((int)$passenger['id'] === $id) {
                    $passenger['archived'] = !$passenger['archived'];
                    $passenger['updated_at'] = date('c');
                    break;
                }
            }
            unset($passenger);
            save_data('passengers.json', $passengers);
            redirect('index.php?page=passengers&success=Passenger+status+changed');
        case 'create_flight':
            $flightId = generate_id($flights);
            $passengerId = (int)($_POST['passenger_id'] ?? 0);
            $departure = trim($_POST['departure_airport'] ?? '');
            $arrival = trim($_POST['arrival_airport'] ?? '');
            $departureTime = trim($_POST['departure_time'] ?? '');
            $arrivalTime = trim($_POST['arrival_time'] ?? '');
            $flightNumber = trim($_POST['flight_number'] ?? '');

            if (!$passengerId || $departure === '' || $arrival === '' || $departureTime === '') {
                redirect('index.php?page=flights&error=Missing+required+fields');
            }

            $flights[] = [
                'id' => $flightId,
                'passenger_id' => $passengerId,
                'departure_airport' => $departure,
                'arrival_airport' => $arrival,
                'departure_time' => $departureTime,
                'arrival_time' => $arrivalTime,
                'flight_number' => $flightNumber,
                'archived' => false,
                'notes' => trim($_POST['notes'] ?? ''),
                'updated_at' => date('c'),
            ];
            save_data('flights.json', $flights);
            redirect('index.php?page=flights&success=Flight+added');
        case 'update_flight':
            $id = (int)($_POST['id'] ?? 0);
            $fields = [
                'passenger_id' => (int)($_POST['passenger_id'] ?? 0),
                'departure_airport' => trim($_POST['departure_airport'] ?? ''),
                'arrival_airport' => trim($_POST['arrival_airport'] ?? ''),
                'departure_time' => trim($_POST['departure_time'] ?? ''),
                'arrival_time' => trim($_POST['arrival_time'] ?? ''),
                'flight_number' => trim($_POST['flight_number'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'updated_at' => date('c'),
            ];
            $flights = update_record($flights, $id, $fields);
            save_data('flights.json', $flights);
            redirect('index.php?page=flights&success=Flight+updated');
        case 'toggle_flight':
            $id = (int)($_POST['id'] ?? 0);
            foreach ($flights as &$flight) {
                if ((int)$flight['id'] === $id) {
                    $flight['archived'] = !$flight['archived'];
                    $flight['updated_at'] = date('c');
                    break;
                }
            }
            unset($flight);
            save_data('flights.json', $flights);
            redirect('index.php?page=flights&success=Flight+status+changed');
        case 'generate_predictions':
            $years = min(max((int)($_POST['years'] ?? 1), 1), 5);
            $generated = generate_predictions($flights, $passengers, $airports, $config, $years);
            $predictions = array_merge($predictions, $generated);
            save_data('predictions.json', $predictions);
            redirect('index.php?page=predictions&success=Predictions+generated');
        case 'update_config':
            $base = max(min((float)($_POST['base_percentage'] ?? 0.5), 0.95), 0.3);
            $increment = max(min((float)($_POST['extra_day_increment'] ?? 0.05), 0.2), 0.0);
            $expDays = max(1, (int)($_POST['exponential_growth_days'] ?? 2));

            $config = [
                'base_percentage' => $base,
                'recaman_start' => (int)($_POST['recaman_start'] ?? 0),
                'fibonacci_start' => (int)($_POST['fibonacci_start'] ?? 1),
                'extra_day_increment' => $increment,
                'exponential_growth_days' => $expDays,
            ];
            save_data('config.json', $config);

            $configHistory[] = [
                'updated_at' => date('c'),
                'changes' => $config,
            ];
            save_data('config_history.json', $configHistory);
            redirect('index.php?page=predictions&success=Configuration+updated');
        case 'clear_predictions':
            $predictions = [];
            save_data('predictions.json', $predictions);
            redirect('index.php?page=predictions&success=Predictions+cleared');
        default:
            redirect('index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_post();
}

$viewData = compact('airports', 'passengers', 'flights', 'predictions', 'config', 'configHistory', 'forexRates', 'success');
$viewData['activePage'] = $page;

switch ($page) {
    case 'about':
        $pageTitle = 'About • FliotoMo';
        render('about', $viewData);
    case 'airports':
        $pageTitle = 'Airports • FliotoMo';
        $viewData['airportsPage'] = paginate($airports, 10);
        render('airports', $viewData);
    case 'passengers':
        $pageTitle = 'Passengers • FliotoMo';
        $viewData['passengersPage'] = paginate($passengers, 10);
        render('passengers', $viewData);
    case 'flights':
        $pageTitle = 'Flights • FliotoMo';
        $viewData['flightsPage'] = paginate($flights, 10);
        render('flights', $viewData);
    case 'predictions':
        $pageTitle = 'Predictions • FliotoMo';
        // Show latest predictions first in the table
        $viewData['predictionsPage'] = paginate(array_reverse($predictions), 10);
        render('predictions', $viewData);
    default:
        $pageTitle = 'Dashboard • FliotoMo';
        render('dashboard', $viewData);
}

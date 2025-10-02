<?php
if (!isset($pageTitle)) {
    $pageTitle = 'FliotoMo AA Airlines Manager';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">
    <div class="max-w-6xl mx-auto py-6 px-4">
        <header class="mb-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-blue-700">FliotoMo • Airlines Management System</h1>
                    <p class="text-xs md:text-sm text-slate-500">Manage listing</p>
                </div>
                <button id="mobileMenuBtn" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-slate-600 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Open main menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
            <?php
            $active = $activePage ?? 'dashboard';
            $links = [
                ['label' => 'Dashboard', 'href' => 'index.php', 'page' => 'dashboard'],
                ['label' => 'Airports', 'href' => 'index.php?page=airports', 'page' => 'airports'],
                ['label' => 'Passengers', 'href' => 'index.php?page=passengers', 'page' => 'passengers'],
                ['label' => 'Flights', 'href' => 'index.php?page=flights', 'page' => 'flights'],
                ['label' => 'Predictions', 'href' => 'index.php?page=predictions', 'page' => 'predictions'],
                ['label' => 'About', 'href' => 'index.php?page=about', 'page' => 'about'],
            ];
            ?>
            <nav class="hidden md:flex gap-2 mt-4">
                <?php foreach ($links as $link):
                    $isActive = $link['page'] === $active;
                    $classes = $isActive
                        ? 'px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700'
                        : 'px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300';
                ?>
                    <a class="<?= $classes; ?>" href="<?= $link['href']; ?>"><?= $link['label']; ?></a>
                <?php endforeach; ?>
            </nav>
            <div id="mobileMenu" class="md:hidden mt-3 hidden">
                <div class="flex flex-col gap-2 bg-white border border-slate-200 rounded-md p-2 shadow">
                    <?php foreach ($links as $link):
                        $isActive = $link['page'] === $active;
                        $classes = $isActive
                            ? 'px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700'
                            : 'px-3 py-2 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300';
                    ?>
                        <a class="<?= $classes; ?>" href="<?= $link['href']; ?>"><?= $link['label']; ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <script>
                const btn = document.getElementById('mobileMenuBtn');
                const menu = document.getElementById('mobileMenu');
                if (btn && menu) {
                    btn.addEventListener('click', () => {
                        menu.classList.toggle('hidden');
                    });
                }
            </script>
        </header>

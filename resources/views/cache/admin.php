<?php include __DIR__ . '/../layouts/main.php'; ?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Zarządzanie Cache</h1>
            <p class="text-gray-600 mt-2">Panel zarządzania cache danych z API</p>
        </div>

        <!-- Statystyki cache -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Statystyki Cache</h2>
            
            <?php if (!empty($stats)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($stats as $filename => $stat): ?>
                <div class="border rounded-lg p-4 <?= $stat['is_valid'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?>">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900"><?= ucfirst($filename) ?></h3>
                            <p class="text-sm text-gray-600">
                                <?= $stat['count'] ?? 0 ?> produktów
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $stat['is_valid'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $stat['is_valid'] ? 'Aktualny' : 'Przestarzały' ?>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">
                        <p>Ostatnia aktualizacja: <?= $stat['last_updated'] ?></p>
                        <p>Wiek: <?= $stat['age_hours'] ?> godzin</p>
                        <p>Rozmiar: <?= $stat['size_kb'] ?> KB</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8">
                <p class="text-gray-500">Brak danych cache</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Akcje -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Aktualizacja Cache</h3>
                <p class="text-gray-600 mb-4">Wymuś aktualizację wszystkich danych z API</p>
                <a href="/cache/refresh" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Aktualizuj cache
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Czyszczenie Cache</h3>
                <p class="text-gray-600 mb-4">Usuń przestarzałe pliki cache</p>
                <a href="/cache/clean" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Wyczyść cache
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Logi Aktualizacji</h3>
                <p class="text-gray-600 mb-4">Sprawdź logi ostatnich aktualizacji</p>
                <a href="/logs/cache_update.log" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Zobacz logi
                </a>
            </div>
        </div>

        <!-- Informacje -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Informacje o Cache</h3>
            <div class="text-sm text-blue-800 space-y-2">
                <p><strong>Czas ważności:</strong> 24 godziny</p>
                <p><strong>Automatyczna aktualizacja:</strong> Codziennie o 3:00</p>
                <p><strong>Lokalizacja plików:</strong> <code>data/cache/</code></p>
                <p><strong>Lokalizacja logów:</strong> <code>logs/cache_update.log</code></p>
            </div>
        </div>
    </div>
</div> 
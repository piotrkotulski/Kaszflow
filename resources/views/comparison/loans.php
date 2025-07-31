<?php include __DIR__ . '/../layouts/main.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Porównywarka Kredytów Gotówkowych
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Porównaj kredyty gotówkowe i znajdź najlepszą ofertę dla siebie. 
                Sprawdź RRSO, raty i warunki kredytów od różnych banków.
            </p>
        </div>
    </div>
</section>

<!-- Filtry -->
<section class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Filtry wyszukiwania</h2>
            
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Kwota kredytu -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Kwota kredytu (zł)
                    </label>
                    <input type="number" 
                           id="amount" 
                           name="amount" 
                           value="<?= htmlspecialchars($filters['amount'] ?? 20000) ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Okres kredytowania -->
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                        Okres kredytowania (miesiące)
                    </label>
                    <select id="period" 
                            name="period" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="12" <?= ($filters['period'] ?? 48) == 12 ? 'selected' : '' ?>>12 miesięcy</option>
                        <option value="24" <?= ($filters['period'] ?? 48) == 24 ? 'selected' : '' ?>>24 miesiące</option>
                        <option value="36" <?= ($filters['period'] ?? 48) == 36 ? 'selected' : '' ?>>36 miesięcy</option>
                        <option value="48" <?= ($filters['period'] ?? 48) == 48 ? 'selected' : '' ?>>48 miesięcy</option>
                        <option value="60" <?= ($filters['period'] ?? 48) == 60 ? 'selected' : '' ?>>60 miesięcy</option>
                        <option value="72" <?= ($filters['period'] ?? 48) == 72 ? 'selected' : '' ?>>72 miesiące</option>
                        <option value="84" <?= ($filters['period'] ?? 48) == 84 ? 'selected' : '' ?>>84 miesiące</option>
                        <option value="96" <?= ($filters['period'] ?? 48) == 96 ? 'selected' : '' ?>>96 miesięcy</option>
                        <option value="120" <?= ($filters['period'] ?? 48) == 120 ? 'selected' : '' ?>>120 miesięcy</option>
                    </select>
                </div>
                
                <!-- Przycisk wyszukiwania -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Wyszukaj
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Wyniki -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($loans) && !isset($loans['error'])): ?>
        <!-- Opcje sortowania -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Sortuj według:</h3>
                    <p class="text-sm text-gray-600">Znaleziono <?= count($loans) ?> ofert</p>
                </div>
                <div class="flex space-x-2">
                    <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'rate'])) ?>" 
                       class="px-4 py-2 rounded-md <?= ($_GET['sort'] ?? 'rate') == 'rate' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                        Najniższa rata
                    </a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'rrso'])) ?>" 
                       class="px-4 py-2 rounded-md <?= ($_GET['sort'] ?? 'rate') == 'rrso' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                        Najniższe RRSO
                    </a>
                </div>
            </div>
        </div>
        <!-- Lista ofert -->
        <div class="space-y-6">
            <?php foreach ($loans as $index => $loan): ?>
            <div class="relative">
                <!-- Simple numbering - outside the card -->
                <div class="absolute -top-2 -left-2 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shadow-lg z-10 bg-white text-gray-700">
                    <?= $index + 1 ?>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <!-- Bank section with fixed width -->
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 flex items-center justify-center flex-shrink-0 mr-6">
                            <?php if (!empty($loan['logo_url_format'])): ?>
                                <img src="<?= htmlspecialchars($loan['logo_url_format']) ?>" 
                                     alt="<?= htmlspecialchars($loan['bank_name'] ?? 'Bank') ?>" 
                                     class="h-12 w-auto max-w-full object-contain">
                            <?php elseif (!empty($loan['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($loan['logo_url']) ?>" 
                                     alt="<?= htmlspecialchars($loan['bank_name'] ?? 'Bank') ?>" 
                                     class="h-12 w-auto max-w-full object-contain">
                            <?php else: ?>
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="w-48">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                <?= htmlspecialchars($loan['bank_name'] ?? 'Bank') ?>
                            </h3>
                            <p class="text-sm text-gray-600 truncate">
                                <?= htmlspecialchars($loan['product_name'] ?? 'Kredyt gotówkowy') ?>
                            </p>
                        </div>
                    </div>
                
                <!-- Loan parameters - evenly distributed -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">RRSO</p>
                        <p class="text-lg font-bold text-green-600">
                            <?= number_format($loan['aprc'] ?? 0, 2, ',', ' ') ?>%
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Rata</p>
                        <p class="text-lg font-bold text-gray-900">
                            <?= number_format($loan['first_installment'] ?? 0, 2, ',', ' ') ?> zł
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Kwota do spłaty</p>
                        <p class="text-lg font-bold text-gray-900">
                            <?= number_format($loan['total_amount'] ?? 0, 2, ',', ' ') ?> zł
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">Max kwota</p>
                        <p class="text-lg font-bold text-gray-900">
                            <?= number_format($loan['amount_max'] ?? 0, 0, ',', ' ') ?> zł
                        </p>
                    </div>
                </div>
                
                <!-- Buttons - below everything -->
                <div class="flex flex-wrap items-center justify-between pt-4 border-t border-gray-200 gap-3">
                    <button type="button" 
                            class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center whitespace-nowrap"
                            onclick="toggleDetails('details-loan-<?= $index ?>')">
                        <span class="mr-2">Szczegóły oferty</span>
                        <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <a href="<?= htmlspecialchars('https://kaszflow.produktyfinansowe.pl' . ($loan['lead_url'] ?? '/produkt?id=' . ($loan['product_id'] ?? ''))) ?>" 
                       target="_blank"
                       class="bg-green-600 text-white px-6 py-3 rounded font-bold hover:bg-green-700 transition-colors text-center min-h-12 flex items-center justify-center">
                        Złóż wniosek
                    </a>
                </div>
                
                <!-- Ukryta sekcja szczegółów -->
                <div id="details-loan-<?= $index ?>" class="hidden mt-4 space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Parametry kredytu</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Kwota pożyczki</p>
                                <p class="font-semibold text-gray-900"><?= number_format($loan['amount'] ?? 0, 0, ',', ' ') ?> zł</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Okres kredytowania</p>
                                <p class="font-semibold text-gray-900"><?= $loan['period'] ?? 0 ?> miesięcy</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Oprocentowanie nominalne</p>
                                <p class="font-semibold text-gray-900"><?= number_format($loan['nominal_interest_rate'] ?? 0, 2, ',', ' ') ?>%</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Rodzaj rat</p>
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($loan['type_of_installments'] ?? 'Nie określono') ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Prowizja</p>
                                <p class="font-semibold text-gray-900"><?= number_format($loan['commission_amount'] ?? 0, 2, ',', ' ') ?> zł</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Ubezpieczenie kredytu</p>
                                <p class="font-semibold text-gray-900">
                                    <?php if ($loan['insurance'] === null): ?>
                                        Nie wymagane
                                    <?php elseif ($loan['insurance'] === true): ?>
                                        Wymagane
                                    <?php else: ?>
                                        <?= htmlspecialchars($loan['insurance']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($loan['representative_example'])): ?>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Przykład reprezentatywny</h4>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <?= nl2br(htmlspecialchars($loan['representative_example'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <!-- Dolna sekcja z przyciskiem - responsywne -->
                    <div class="flex flex-wrap items-center justify-between pt-4 border-t border-gray-200 gap-3">
                        <button type="button" 
                                class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center whitespace-nowrap"
                                onclick="toggleDetails('details-loan-<?= $index ?>')">
                            <span class="mr-2">Zwiń szczegóły</span>
                            <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <a href="<?= htmlspecialchars('https://kaszflow.produktyfinansowe.pl' . ($loan['lead_url'] ?? '/produkt?id=' . ($loan['product_id'] ?? ''))) ?>" 
                           target="_blank"
                           class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors text-center">
                            Złóż wniosek
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php elseif (isset($loans['error'])): ?>
            <!-- Błąd API -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-red-800 mb-2">Brak dostępnych ofert</h3>
                <p class="text-red-600 mb-4">Nie znaleziono kredytów spełniających wybrane kryteria.</p>
                <div class="flex justify-center space-x-4">
                    <a href="/kredyty-gotowkowe" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Zobacz wszystkie kredyty
                    </a>
                    <button onclick="history.back()" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Zmień parametry
                    </button>
                </div>
            </div>
        <?php elseif (empty($loans)): ?>
            <!-- Brak wyników -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Brak ofert</h3>
                <p class="text-gray-600">Nie znaleziono ofert spełniających kryteria wyszukiwania.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- JavaScript dla rozwijania szczegółów -->
<script>
function toggleDetails(detailsId) {
    const detailsElement = document.getElementById(detailsId);
    const button = event.target.closest('button');
    const icon = button.querySelector('svg');
    
    if (detailsElement.classList.contains('hidden')) {
        detailsElement.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        detailsElement.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>

<!-- Footer -->
<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Logo i opis -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center">
                    <img class="h-8 w-auto" src="/assets/images/logo-white.svg" alt="Kaszflow">
                    <span class="ml-2 text-xl font-bold">Kaszflow</span>
                </div>
                <p class="mt-4 text-gray-300 max-w-md">
                    Porównywarka produktów finansowych. Znajdź najlepsze kredyty, konta i lokaty na rynku.
                </p>
                <div class="mt-6 flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <span class="sr-only">LinkedIn</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Produkty -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Produkty</h3>
                <ul class="mt-4 space-y-4">
                    <li><a href="/kredyty-gotowkowe" class="text-base text-gray-300 hover:text-white transition-colors">Kredyty gotówkowe</a></li>
                    <li><a href="/kredyty-hipoteczne" class="text-base text-gray-300 hover:text-white transition-colors">Kredyty hipoteczne</a></li>
                    <li><a href="/konta-osobiste" class="text-base text-gray-300 hover:text-white transition-colors">Konta osobiste</a></li>
                    <li><a href="/lokaty" class="text-base text-gray-300 hover:text-white transition-colors">Lokaty</a></li>
                </ul>
            </div>
            
            <!-- Firma -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Firma</h3>
                <ul class="mt-4 space-y-4">
                    <li><a href="/o-nas" class="text-base text-gray-300 hover:text-white transition-colors">O nas</a></li>
                    <li><a href="/kontakt" class="text-base text-gray-300 hover:text-white transition-colors">Kontakt</a></li>
                    <li><a href="/blog" class="text-base text-gray-300 hover:text-white transition-colors">Blog</a></li>
                    <li><a href="/polityka-prywatnosci" class="text-base text-gray-300 hover:text-white transition-colors">Polityka prywatności</a></li>
                </ul>
            </div>
        </div>
        
        <div class="mt-8 pt-8 border-t border-gray-700">
            <p class="text-base text-gray-400 text-center">
                &copy; <?= date('Y') ?> Kaszflow. Wszystkie prawa zastrzeżone.
            </p>
        </div>
    </div>
</footer> 

<style>
    /* Style dla kredytów gotówkowych - układ poziomy jak w hipotekach */
</style> 
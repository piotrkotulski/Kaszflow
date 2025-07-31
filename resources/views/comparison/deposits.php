<?php include __DIR__ . '/../layouts/main.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Porównywarka Lokat
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Porównaj lokaty i znajdź najlepszą ofertę dla swoich oszczędności. 
                Sprawdź oprocentowanie, okres i warunki lokat od różnych banków.
            </p>
        </div>
    </div>
</section>

<!-- Wyniki -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (isset($deposits['error'])): ?>
            <!-- Błąd API -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-red-800 mb-2">Brak dostępnych ofert</h3>
                <p class="text-red-600 mb-4">Nie znaleziono lokat spełniających wybrane kryteria.</p>
                <div class="flex justify-center space-x-4">
                    <a href="/lokaty" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Zobacz wszystkie lokaty
                    </a>
                    <button onclick="history.back()" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Zmień parametry
                    </button>
                </div>
            </div>
        <?php elseif (empty($deposits)): ?>
            <!-- Brak wyników -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Brak ofert</h3>
                <?php if (!empty($filters['amount']) || !empty($filters['period'])): ?>
                    <p class="text-gray-600">
                        Nie znaleziono lokat spełniających wybrane kryteria.
                        <?php if (!empty($filters['amount'])): ?>
                            <br>Kwota: <?= number_format($filters['amount'], 0, ',', ' ') ?> zł
                        <?php endif; ?>
                        <?php if (!empty($filters['period'])): ?>
                            <br>Okres: <?= $filters['period'] ?> miesięcy
                        <?php endif; ?>
                    </p>
                    <div class="mt-4">
                        <a href="/lokaty" class="text-blue-600 hover:text-blue-800 font-medium">
                            Zobacz wszystkie lokaty
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600">Nie znaleziono ofert lokat.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Filtry -->
            <section class="py-8 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Filtry wyszukiwania</h2>
                        
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Kwota lokaty -->
                            <div>
                                <label for="deposit_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kwota lokaty (zł)
                                </label>
                                <input type="number" 
                                       id="deposit_amount" 
                                       name="deposit_amount" 
                                       value="<?= htmlspecialchars($filters['amount'] ?? 10000) ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <!-- Okres lokaty -->
                            <div>
                                <label for="deposit_period" class="block text-sm font-medium text-gray-700 mb-2">
                                    Okres lokaty (miesiące)
                                </label>
                                <select id="deposit_period" 
                                        name="deposit_period" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="1" <?= ($filters['period'] ?? 6) == 1 ? 'selected' : '' ?>>1 miesiąc</option>
                                    <option value="3" <?= ($filters['period'] ?? 6) == 3 ? 'selected' : '' ?>>3 miesiące</option>
                                    <option value="6" <?= ($filters['period'] ?? 6) == 6 ? 'selected' : '' ?>>6 miesięcy</option>
                                    <option value="12" <?= ($filters['period'] ?? 6) == 12 ? 'selected' : '' ?>>12 miesięcy</option>
                                    <option value="24" <?= ($filters['period'] ?? 6) == 24 ? 'selected' : '' ?>>24 miesiące</option>
                                    <option value="36" <?= ($filters['period'] ?? 6) == 36 ? 'selected' : '' ?>>36 miesięcy</option>
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
            
            <!-- Opcje sortowania -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Sortuj według:</h3>
                        <p class="text-sm text-gray-600">Znaleziono <?= count($deposits) ?> ofert</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'rate'])) ?>" 
                           class="px-4 py-2 rounded-md <?= ($_GET['sort'] ?? 'rate') == 'rate' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                            Najwyższe oprocentowanie
                        </a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'total'])) ?>" 
                           class="px-4 py-2 rounded-md <?= ($_GET['sort'] ?? 'rate') == 'total' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                            Najwyższe odsetki
                        </a>
                    </div>
                </div>
            </div>
            <!-- Lista ofert -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($deposits as $index => $deposit): ?>
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <!-- Informacje o banku -->
                    <div class="flex items-center mb-4">
                        <!-- Simple numbering - before logo -->
                        <div class="w-8 h-8 flex items-center justify-center font-bold text-sm text-gray-700 -mt-10" style="margin-left: -10px;">
                            <?= $index + 1 ?>
                        </div>
                        
                        <div class="w-16 h-16 flex items-center justify-center flex-shrink-0 mr-6">
                            <?php if (!empty($deposit['logo_url_format'])): ?>
                                <img src="<?= htmlspecialchars($deposit['logo_url_format']) ?>" 
                                     alt="<?= htmlspecialchars($deposit['bank_name'] ?? 'Bank') ?>" 
                                     class="h-12 w-auto max-w-full object-contain">
                            <?php elseif (!empty($deposit['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($deposit['logo_url']) ?>" 
                                     alt="<?= htmlspecialchars($deposit['bank_name'] ?? 'Bank') ?>" 
                                     class="h-12 w-auto max-w-full object-contain">
                            <?php else: ?>
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0 w-48">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                <?= htmlspecialchars($deposit['bank_name'] ?? 'Bank') ?>
                            </h3>
                            <p class="text-sm text-gray-600 truncate">
                                <?= htmlspecialchars($deposit['product_name'] ?? 'Lokata') ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Parametry lokaty -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Oprocentowanie:</span>
                            <span class="font-semibold text-green-600">
                                <?php 
                                $maxRate = 0;
                                if (!empty($deposit['interest_rate']) && is_array($deposit['interest_rate'])) {
                                    foreach ($deposit['interest_rate'] as $rate) {
                                        if (isset($rate[1]) && $rate[1] > $maxRate) {
                                            $maxRate = $rate[1];
                                        }
                                    }
                                }
                                echo number_format($maxRate, 2, ',', ' ') . '%';
                                ?>
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Okres:</span>
                            <span class="font-semibold">
                                <?= htmlspecialchars($deposit['period_description'] ?? '') ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($deposit['interest'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kwota odsetek:</span>
                            <span class="font-semibold text-green-600">
                                <?= number_format($deposit['interest'], 2, ',', ' ') ?> zł
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($deposit['min_amount'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Min. kwota:</span>
                            <span class="font-semibold">
                                <?= number_format($deposit['min_amount'], 0, ',', ' ') ?> zł
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($deposit['max_amount'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Max. kwota:</span>
                            <span class="font-semibold">
                                <?= number_format($deposit['max_amount'], 0, ',', ' ') ?> zł
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($deposit['capital_interest'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kapitalizacja:</span>
                            <span class="font-semibold">
                                <?= htmlspecialchars($deposit['capital_interest_description'] ?? '') ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Przycisk -->
                    <a href="<?= htmlspecialchars('https://kaszflow.produktyfinansowe.pl' . ($deposit['lead_url'] ?? '/produkt?id=' . ($deposit['product_id'] ?? ''))) ?>" 
                       target="_blank"
                       class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center block">
                        Sprawdź ofertę
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

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
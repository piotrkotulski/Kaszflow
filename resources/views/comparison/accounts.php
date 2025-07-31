<?php include __DIR__ . '/../layouts/main.php'; ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Porównywarka Kont Osobistych
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Porównaj konta osobiste i znajdź najlepszą ofertę dla siebie. 
                Sprawdź opłaty, premie i dodatkowe usługi banków.
            </p>
        </div>
    </div>
</section>

<!-- Wyniki -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (isset($accounts['error'])): ?>
            <!-- Błąd API -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-red-800 mb-2">Brak dostępnych ofert</h3>
                <p class="text-red-600 mb-4">Nie znaleziono kont osobistych spełniających wybrane kryteria.</p>
                <div class="flex justify-center space-x-4">
                    <a href="/konta-osobiste" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Zobacz wszystkie konta
                    </a>
                    <button onclick="history.back()" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Zmień parametry
                    </button>
                </div>
            </div>
        <?php elseif (empty($accounts)): ?>
            <!-- Brak wyników -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Brak ofert</h3>
                <p class="text-gray-600">Nie znaleziono ofert kont osobistych.</p>
            </div>
        <?php else: ?>
            <!-- Opcje sortowania -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Sortuj według:</h3>
                        <p class="text-sm text-gray-600">Znaleziono <?= count($accounts) ?> ofert</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'bonus'])) ?>" 
                           class="px-4 py-2 rounded-md <?= ($_GET['sort'] ?? '') == 'bonus' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                            Najwyższa premia
                        </a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'fee'])) ?>" 
                           class="px-4 py-2 rounded-md <?= ($_GET['sort'] ?? '') == 'fee' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition-colors">
                            Najniższa opłata
                        </a>
                    </div>
                </div>
            </div>
            <!-- Lista ofert -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($accounts as $index => $account): ?>
                <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <!-- Informacje o banku -->
                        <div class="flex items-center mb-4">
                            <!-- Simple numbering - before logo -->
                            <div class="w-8 h-8 flex items-center justify-center font-bold text-sm text-gray-700 -mt-10" style="margin-left: -10px;">
                                <?= $index + 1 ?>
                            </div>
                            
                            <div class="w-16 h-16 flex items-center justify-center flex-shrink-0 mr-6">
                            <?php if (!empty($account['logo_url_format'])): ?>
                                <img src="<?= htmlspecialchars(is_string($account['logo_url_format']) ? $account['logo_url_format'] : '') ?>" 
                                     alt="<?= htmlspecialchars(is_string($account['bank_name']) ? ($account['bank_name'] ?? 'Bank') : 'Bank') ?>" 
                                     class="h-12 w-auto max-w-full object-contain">
                            <?php elseif (!empty($account['logo_url'])): ?>
                                <img src="<?= htmlspecialchars(is_string($account['logo_url']) ? $account['logo_url'] : '') ?>" 
                                     alt="<?= htmlspecialchars(is_string($account['bank_name']) ? ($account['bank_name'] ?? 'Bank') : 'Bank') ?>" 
                                     class="h-12 w-auto max-w-full object-contain">
                            <?php else: ?>
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0 w-48">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                <?= htmlspecialchars(is_string($account['bank_name']) ? ($account['bank_name'] ?? 'Bank') : 'Bank') ?>
                            </h3>
                            <p class="text-sm text-gray-600 truncate">
                                <?= htmlspecialchars(is_string($account['product_name']) ? ($account['product_name'] ?? 'Konto osobiste') : 'Konto osobiste') ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Parametry konta -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Opłata za prowadzenie:</span>
                            <span class="font-semibold">
                                <?= number_format($account['management_fee_min'] ?? 0, 2, ',', ' ') ?> zł
                            </span>
                        </div>
                        
                        <?php if (!empty($account['bonus'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Premia:</span>
                            <span class="font-semibold text-green-600">
                                <?= htmlspecialchars(is_string($account['bonus']) ? $account['bonus'] : (is_array($account['bonus']) ? json_encode($account['bonus']) : (string)$account['bonus'])) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($account['interest_rate'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Oprocentowanie:</span>
                            <span class="font-semibold text-green-600">
                                <?= number_format($account['interest_rate'], 2, ',', ' ') ?>%
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($account['card_fee'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Opłata za kartę:</span>
                            <span class="font-semibold">
                                <?= number_format($account['card_fee'], 2, ',', ' ') ?> zł
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Dodatkowe informacje -->
                    <?php if (!empty($account['features'])): ?>
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Dodatkowe usługi:</h4>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach (array_slice($account['features'], 0, 3) as $feature): ?>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                <?= htmlspecialchars(is_string($feature) ? $feature : (is_array($feature) ? json_encode($feature) : (string)$feature)) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Przycisk -->
                    <a href="<?= htmlspecialchars('https://kaszflow.produktyfinansowe.pl' . ($account['lead_url'] ?? '/produkt?id=' . ($account['product_id'] ?? ''))) ?>" 
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
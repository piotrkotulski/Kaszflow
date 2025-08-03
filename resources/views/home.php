<?php include __DIR__ . '/layouts/main.php'; ?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    Znajdź najlepsze <span class="text-yellow-300">oferty finansowe</span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-100 mb-8 leading-relaxed">
                    Porównuj kredyty, konta, lokaty i inne produkty finansowe. 
                    Oszczędzaj czas i pieniądze dzięki naszej inteligentnej porównywarce.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/kredyty-gotowkowe" 
                       class="bg-yellow-400 text-blue-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-yellow-300 transition-colors text-center">
                        Porównaj kredyty
                    </a>
                    <a href="/konta-osobiste" 
                       class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-900 transition-colors text-center">
                        Sprawdź konta
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 rounded-2xl transform rotate-3"></div>
                    <div class="relative bg-white rounded-2xl p-8 shadow-2xl">
                        <div class="space-y-4">
                            <!-- Kredyt gotówkowy -->
                            <?php if (!empty($heroOffers['loan'])): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Kredyt gotówkowy</p>
                                        <p class="text-sm text-gray-500">RRSO od <?= number_format($heroOffers['loan']['aprc'] ?? 0, 2, ',', ' ') ?>%</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <?php 
                                    $rrsoDiff = ($heroOffers['avgLoanRRSO'] ?? 0) - ($heroOffers['loan']['aprc'] ?? 0);
                                    if ($rrsoDiff > 0): ?>
                                        <p class="text-lg font-bold text-green-600">-<?= number_format($rrsoDiff, 2, ',', ' ') ?>%</p>
                                        <p class="text-xs text-gray-500">niż średnia</p>
                                    <?php else: ?>
                                        <p class="text-lg font-bold text-red-600">+<?= number_format(abs($rrsoDiff), 2, ',', ' ') ?>%</p>
                                        <p class="text-xs text-gray-500">niż średnia</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Konto osobiste -->
                            <?php if (!empty($heroOffers['account'])): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Konto osobiste</p>
                                        <p class="text-sm text-gray-500"><?= number_format($heroOffers['account']['management_fee_min'] ?? 0, 2, ',', ' ') ?> zł za prowadzenie</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <?php 
                                    $bonusValue = 0;
                                    if (!empty($heroOffers['account']['bonus'])) {
                                        preg_match('/(\d+(?:\.\d+)?)/', $heroOffers['account']['bonus'], $matches);
                                        $bonusValue = isset($matches[1]) ? (float)$matches[1] : 0;
                                    }
                                    if ($bonusValue > 0): ?>
                                        <p class="text-lg font-bold text-green-600">+<?= number_format($bonusValue, 0, ',', ' ') ?> zł</p>
                                        <p class="text-xs text-gray-500">premia</p>
                                    <?php else: ?>
                                        <p class="text-lg font-bold text-gray-600">Brak premii</p>
                                        <p class="text-xs text-gray-500">standard</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Lokata -->
                            <?php if (!empty($heroOffers['deposit'])): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Lokata</p>
                                        <?php 
                                        $maxRate = 0;
                                        if (!empty($heroOffers['deposit']['interest_rate']) && is_array($heroOffers['deposit']['interest_rate'])) {
                                            foreach ($heroOffers['deposit']['interest_rate'] as $rate) {
                                                if (isset($rate[1]) && $rate[1] > $maxRate) {
                                                    $maxRate = $rate[1];
                                                }
                                            }
                                        }
                                        ?>
                                        <p class="text-sm text-gray-500">Oprocentowanie <?= number_format($maxRate, 2, ',', ' ') ?>%</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <?php 
                                    $rateDiff = $maxRate - ($heroOffers['avgDepositRate'] ?? 0);
                                    if ($rateDiff > 0): ?>
                                        <p class="text-lg font-bold text-green-600">+<?= number_format($rateDiff, 2, ',', ' ') ?>%</p>
                                        <p class="text-xs text-gray-500">wyższe</p>
                                    <?php else: ?>
                                        <p class="text-lg font-bold text-red-600"><?= number_format($rateDiff, 2, ',', ' ') ?>%</p>
                                        <p class="text-xs text-gray-500">niższe</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Best Offers Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Najlepsze oferty tego miesiąca
            </h2>
            <p class="text-xl text-gray-600">
                Sprawdź najkorzystniejsze produkty finansowe dostępne na rynku.
            </p>
        </div>
        
        <!-- Kredyty gotówkowe -->
        <?php if (!empty($bestOffers['loans'])): ?>
        <div class="mb-12">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Kredyty gotówkowe</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach (array_slice($bestOffers['loans'], 0, 3) as $loan): ?>
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="<?= htmlspecialchars($loan['logo_url_format'] ?? '') ?>" 
                             alt="<?= htmlspecialchars($loan['bank_name'] ?? '') ?>" 
                             class="h-8 w-auto">
                        <span class="ml-3 text-sm text-gray-500"><?= htmlspecialchars($loan['bank_name'] ?? '') ?></span>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2"><?= htmlspecialchars($loan['product_name'] ?? '') ?></h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">RRSO:</span>
                            <span class="font-semibold text-green-600"><?= number_format($loan['aprc'] ?? 0, 2, ',', ' ') ?>%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rata:</span>
                            <span class="font-semibold"><?= number_format($loan['first_installment'] ?? 0, 2, ',', ' ') ?> zł</span>
                        </div>
                    </div>
                    <a href="<?= htmlspecialchars('https://kaszflow.produktyfinansowe.pl' . ($loan['lead_url'] ?? '/produkt?id=' . ($loan['product_id'] ?? ''))) ?>" 
                       target="_blank"
                       class="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center block">
                        Sprawdź ofertę
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Konta osobiste -->
        <?php if (!empty($bestOffers['accounts'])): ?>
        <div class="mb-12">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Konta osobiste</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach (array_slice($bestOffers['accounts'], 0, 3) as $account): ?>
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="<?= htmlspecialchars($account['logo_url_format'] ?? '') ?>" 
                             alt="<?= htmlspecialchars($account['bank_name'] ?? '') ?>" 
                             class="h-8 w-auto">
                        <span class="ml-3 text-sm text-gray-500"><?= htmlspecialchars($account['bank_name'] ?? '') ?></span>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2"><?= htmlspecialchars($account['product_name'] ?? '') ?></h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Opłata:</span>
                            <span class="font-semibold"><?= number_format($account['management_fee_min'] ?? 0, 2, ',', ' ') ?> zł</span>
                        </div>
                        <?php if (!empty($account['bonus'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Premia:</span>
                            <span class="font-semibold text-green-600"><?= htmlspecialchars($account['bonus']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?= htmlspecialchars('https://kaszflow.produktyfinansowe.pl' . ($account['lead_url'] ?? '/produkt?id=' . ($account['product_id'] ?? ''))) ?>" 
                       target="_blank"
                       class="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center block">
                        Sprawdź ofertę
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Lokaty -->
        <?php if (!empty($bestOffers['deposits'])): ?>
        <div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">Lokaty</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach (array_slice($bestOffers['deposits'], 0, 3) as $deposit): ?>
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <img src="<?= htmlspecialchars($deposit['logo_url_format'] ?? '') ?>" 
                             alt="<?= htmlspecialchars($deposit['bank_name'] ?? '') ?>" 
                             class="h-8 w-auto">
                        <span class="ml-3 text-sm text-gray-500"><?= htmlspecialchars($deposit['bank_name'] ?? '') ?></span>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2"><?= htmlspecialchars($deposit['product_name'] ?? '') ?></h4>
                    <div class="space-y-2">
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
                            <span class="font-semibold"><?= htmlspecialchars($deposit['period_description'] ?? '') ?></span>
                        </div>
                    </div>
                    <a href="<?= htmlspecialchars('https://kaszflow.produktyfinansowe.pl' . ($deposit['lead_url'] ?? '/produkt?id=' . ($deposit['product_id'] ?? ''))) ?>" 
                       target="_blank"
                       class="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center block">
                        Sprawdź ofertę
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
@keyframes scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

.animate-scroll {
    animation: scroll 30s linear infinite;
}

.animate-scroll:hover {
    animation-play-state: paused;
}
</style>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Dlaczego warto wybrać Kaszflow?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Oferujemy najnowsze technologie i sprawdzone rozwiązania, które pomogą Ci znaleźć najlepsze oferty finansowe.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Dokładne porównania</h3>
                <p class="text-gray-600">
                    Porównujemy wszystkie koszty i warunki, abyś mógł podjąć najlepszą decyzję finansową.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Szybkie wyniki</h3>
                <p class="text-gray-600">
                    Otrzymujesz wyniki w ciągu kilku sekund. Nie trać czasu na przeglądanie wielu stron.
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Bezpieczeństwo</h3>
                <p class="text-gray-600">
                    Twoje dane są bezpieczne. Nie przechowujemy wrażliwych informacji finansowych.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Gotowy na oszczędności?
        </h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
            Dołącz do tysięcy zadowolonych klientów, którzy już oszczędzają dzięki naszej porównywarce.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/kredyty-gotowkowe" 
               class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors">
                Porównaj teraz
            </a>
            <a href="/blog" 
               class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition-colors">
                Czytaj blog
            </a>
        </div>
    </div>
</section>

<!-- Bank Partners Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Instytucje finansowe z którymi współpracujemy
            </h2>
            <div class="flex items-center justify-center space-x-4 mb-8">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <p class="text-gray-600 text-sm">
                        Nasi eksperci przechodzą przez cały proces i aktualizują na bieżąco oferty z rankingów.
                    </p>
                    <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 text-xs font-bold">i</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slick Bank Logos Carousel -->
        <?php if (!empty($bankPartners)): ?>
        <div class="relative overflow-hidden">
            <!-- Gradient overlays for smooth edges -->
            <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-white to-transparent z-10"></div>
            <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-white to-transparent z-10"></div>
            
            <!-- Bank logos container -->
            <div class="flex space-x-1 animate-scroll">
                <?php foreach ($bankPartners as $bank): ?>
                <div class="flex-shrink-0 group">
                    <div class="bg-white rounded-2xl shadow-lg p-6 w-48 h-32 flex items-center justify-center hover:shadow-2xl transition-all duration-500 transform hover:scale-110">
                        <?php if (!empty($bank['logo_url'])): ?>
                            <!-- Real bank logo -->
                            <div class="w-full h-full flex items-center justify-center">
                                <img src="<?= htmlspecialchars($bank['logo_url']) ?>" 
                                     alt="<?= htmlspecialchars($bank['name']) ?> logo" 
                                     class="max-w-[80%] max-h-[80%] object-contain filter grayscale hover:grayscale-0 transition-all duration-300">
                            </div>
                        <?php else: ?>
                            <!-- Fallback with initials and gradient -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mx-auto mb-3 group-hover:from-blue-600 group-hover:to-blue-700 transition-all duration-300 shadow-lg">
                                    <span class="text-white font-bold text-xl">
                                        <?= htmlspecialchars($bank['initials']) ?>
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-gray-700 truncate">
                                    <?= htmlspecialchars($bank['name']) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Duplicate logos for infinite scroll effect -->
                <?php foreach ($bankPartners as $bank): ?>
                <div class="flex-shrink-0 group">
                    <div class="bg-white rounded-2xl shadow-lg p-6 w-48 h-32 flex items-center justify-center hover:shadow-2xl transition-all duration-500 transform hover:scale-110">
                        <?php if (!empty($bank['logo_url'])): ?>
                            <!-- Real bank logo -->
                            <div class="w-full h-full flex items-center justify-center">
                                <img src="<?= htmlspecialchars($bank['logo_url']) ?>" 
                                     alt="<?= htmlspecialchars($bank['name']) ?> logo" 
                                     class="max-w-[80%] max-h-[80%] object-contain filter grayscale hover:grayscale-0 transition-all duration-300">
                            </div>
                        <?php else: ?>
                            <!-- Fallback with initials and gradient -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mx-auto mb-3 group-hover:from-blue-600 group-hover:to-blue-700 transition-all duration-300 shadow-lg">
                                    <span class="text-white font-bold text-xl">
                                        <?= htmlspecialchars($bank['initials']) ?>
                                    </span>
                                </div>
                                <p class="text-sm font-medium text-gray-700 truncate">
                                    <?= htmlspecialchars($bank['name']) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Fallback - Slick placeholder design -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <!-- PKO BP -->
            <div class="group">
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 w-48 h-32 flex items-center justify-center hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <span class="text-red-600 font-bold text-xl">PK</span>
                        </div>
                        <p class="text-sm font-medium text-white">PKO BP</p>
                    </div>
                </div>
            </div>
            
            <!-- Santander -->
            <div class="group">
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 w-48 h-32 flex items-center justify-center hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <span class="text-red-600 font-bold text-xl">SA</span>
                        </div>
                        <p class="text-sm font-medium text-white">Santander</p>
                    </div>
                </div>
            </div>
            
            <!-- mBank -->
            <div class="group">
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 w-48 h-32 flex items-center justify-center hover:from-orange-600 hover:to-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <span class="text-orange-600 font-bold text-xl">MB</span>
                        </div>
                        <p class="text-sm font-medium text-white">mBank</p>
                    </div>
                </div>
            </div>
            
            <!-- ING -->
            <div class="group">
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 w-48 h-32 flex items-center justify-center hover:from-orange-600 hover:to-orange-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <span class="text-orange-600 font-bold text-xl">IN</span>
                        </div>
                        <p class="text-sm font-medium text-white">ING</p>
                    </div>
                </div>
            </div>
            
            <!-- BNP Paribas -->
            <div class="group">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 w-48 h-32 flex items-center justify-center hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                            <span class="text-green-600 font-bold text-xl">BN</span>
                        </div>
                        <p class="text-sm font-medium text-white">BNP Paribas</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Progress Bar -->
        <div class="mt-16">
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: 85%"></div>
            </div>
            <p class="text-center text-gray-600 text-sm mt-4">
                <span class="font-semibold text-blue-600">85%</span> banków w Polsce współpracuje z Kaszflow
            </p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Logo i opis -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l10 10M17 7L7 17"></path>
                    </svg>
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
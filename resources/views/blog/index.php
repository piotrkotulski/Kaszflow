<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDescription ?>">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l10 10M17 7L7 17"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">Kaszflow</span>
                    </a>
                </div>
                
                <!-- Navigation -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/kredyty-gotowkowe" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Kredyty gotówkowe
                        </a>
                        <a href="/kredyty-hipoteczne" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Kredyty hipoteczne
                        </a>
                        <a href="/konta-osobiste" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Konta osobiste
                        </a>
                        <a href="/lokaty" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Lokaty
                        </a>
                        <a href="/blog" class="text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Blog
                        </a>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="mobile-menu-button bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <span class="sr-only">Otwórz menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div class="mobile-menu hidden md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="/kredyty-gotowkowe" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                        Kredyty gotówkowe
                    </a>
                    <a href="/kredyty-hipoteczne" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                        Kredyty hipoteczne
                    </a>
                    <a href="/konta-osobiste" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                        Konta osobiste
                    </a>
                    <a href="/lokaty" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                        Lokaty
                    </a>
                    <a href="/blog" class="text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                        Blog
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Blog Kaszflow</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Artykuły o finansach osobistych, porady ekspertów i aktualności ze świata finansów. 
                Dowiedz się, jak mądrze zarządzać swoimi pieniędzmi.
            </p>
        </div>

        <!-- Categories -->
        <div class="flex justify-center mb-8">
            <div class="flex flex-wrap space-x-2 bg-white rounded-lg shadow p-2">
                <a href="/blog" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($currentCategory ?? 'wszystkie') === 'wszystkie' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">
                    Wszystkie
                </a>
                <a href="/blog?category=kredyty" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($currentCategory ?? '') === 'kredyty' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">
                    Kredyty
                </a>
                <a href="/blog?category=hipoteki" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($currentCategory ?? '') === 'hipoteki' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">
                    Hipoteki
                </a>
                <a href="/blog?category=bankowosc" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($currentCategory ?? '') === 'bankowość' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">
                    Bankowość
                </a>
                <a href="/blog?category=oszczedzanie" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($currentCategory ?? '') === 'oszczędzanie' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">
                    Oszczędzanie
                </a>
                <a href="/blog?category=ubezpieczenia" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($currentCategory ?? '') === 'ubezpieczenia' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">
                    Ubezpieczenia
                </a>
                <a href="/blog?category=firmy" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors <?= ($currentCategory ?? '') === 'firmy' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' ?>">
                    Firmy
                </a>
            </div>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($articles as $article): ?>
            <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Article Image -->
                <?php if (!empty($article['image_url'])): ?>
                <div class="h-48 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($article['image_url']) ?>');">
                </div>
                <?php else: ?>
                <!-- Fallback to gradient with initials -->
                <div class="h-48 bg-gradient-to-br <?= $article['graphic']['colors']['from'] ?> <?= $article['graphic']['colors']['to'] ?> flex items-center justify-center">
                    <div class="text-white text-center">
                        <div class="text-4xl font-bold mb-2">
                            <?= htmlspecialchars($article['graphic']['initials']) ?>
                        </div>
                        <div class="text-sm opacity-90">Kaszflow Blog</div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Article Content -->
                <div class="p-6">
                    <!-- Category Badge -->
                    <div class="mb-3">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                            <?= $article['category'] === 'Kredyty' ? 'bg-red-100 text-red-800' : 
                                ($article['category'] === 'Hipoteki' ? 'bg-purple-100 text-purple-800' :
                                ($article['category'] === 'Bankowość' ? 'bg-blue-100 text-blue-800' :
                                ($article['category'] === 'Oszczędzanie' ? 'bg-green-100 text-green-800' :
                                ($article['category'] === 'Ubezpieczenia' ? 'bg-orange-100 text-orange-800' :
                                ($article['category'] === 'Firmy' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'))))) ?>">
                            <?= $article['category'] ?>
                        </span>
                    </div>
                    
                    <!-- Article Title -->
                    <h2 class="text-xl font-bold text-gray-900 mb-3 hover:text-blue-600 transition-colors">
                        <a href="/blog/<?= $article['slug'] ?>">
                            <?= htmlspecialchars($article['title']) ?>
                        </a>
                    </h2>
                    
                    <!-- Article Excerpt -->
                    <p class="text-gray-600 mb-4 leading-relaxed">
                        <?= htmlspecialchars($article['excerpt']) ?>
                    </p>
                    
                    <!-- Article Meta -->
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <div class="flex items-center space-x-4">
                            <span><?= date('d.m.Y', strtotime($article['published_at'])) ?></span>
                            <span>•</span>
                            <span><?= $article['read_time'] ?></span>
                        </div>
                        <a href="/blog/<?= $article['slug'] ?>" 
                           class="text-blue-600 hover:text-blue-800 font-medium">
                            Czytaj więcej →
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Newsletter Signup -->
        <div class="mt-16 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Nie przegap nowych artykułów!</h3>
            <p class="text-blue-100 mb-6 max-w-2xl mx-auto">
                Zapisz się do naszego newslettera i otrzymuj najnowsze artykuły o finansach osobistych 
                oraz ekskluzywne porady ekspertów.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
                <input type="email" 
                       placeholder="Twój adres email" 
                       class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300">
                <button class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Zapisz się
                </button>
            </div>
        </div>

        <!-- Featured Products -->
        <div class="mt-16">
            <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Sprawdź nasze porównywarki</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="/kredyty-gotowkowe" 
                   class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Kredyty gotówkowe</h4>
                    <p class="text-gray-600">Porównaj oferty i znajdź najlepszy kredyt</p>
                </a>
                
                <a href="/konta-osobiste" 
                   class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Konta osobiste</h4>
                    <p class="text-gray-600">Wybierz konto bez opłat i z premią</p>
                </a>
                
                <a href="/lokaty-bankowe" 
                   class="bg-white rounded-lg shadow p-6 text-center hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Lokaty bankowe</h4>
                    <p class="text-gray-600">Zarabiaj na swoich oszczędnościach</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
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
    
    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('.mobile-menu-button').click(function() {
                $('.mobile-menu').toggleClass('hidden');
            });
        });
    </script>
</body>
</html> 
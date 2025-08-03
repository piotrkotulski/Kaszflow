<?php 
$pageTitle = $pageTitle ?? 'Artykuł - Kaszflow';
$pageDescription = $pageDescription ?? 'Artykuł na blogu Kaszflow';
?>
<?php include __DIR__ . '/../layouts/main.php'; ?>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="text-gray-600 hover:text-gray-900">Strona główna</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="/blog" class="ml-1 text-gray-600 hover:text-gray-900 md:ml-2">Blog</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2"><?= htmlspecialchars(mb_substr($article['title'], 0, 50)) ?><?= mb_strlen($article['title']) > 50 ? '...' : '' ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Article Header -->
        <header class="mb-8">
            <!-- Category Badge -->
            <div class="mb-4">
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
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
            <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                <?= htmlspecialchars($article['title']) ?>
            </h1>
            
            <!-- Article Meta -->
            <div class="flex items-center text-gray-600 mb-6">
                <div class="flex items-center space-x-4">
                    <span><?= date('d.m.Y', strtotime($article['published_at'])) ?></span>
                    <span>•</span>
                    <span><?= $article['read_time'] ?></span>
                    <?php if (!empty($article['tags'])): ?>
                    <span>•</span>
                    <div class="flex space-x-2">
                        <?php foreach (array_slice($article['tags'], 0, 3) as $tag): ?>
                        <span class="text-sm bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Article Image -->
            <?php if (!empty($article['image_url'])): ?>
            <div class="w-full h-48 rounded-lg mb-8 overflow-hidden">
                <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full h-full object-cover">
            </div>
            <?php else: ?>
            <!-- Fallback to gradient with initials -->
            <div class="w-full h-48 bg-gradient-to-br <?= $article['graphic']['colors']['from'] ?> <?= $article['graphic']['colors']['to'] ?> rounded-lg flex items-center justify-center mb-8">
                <div class="text-white text-center">
                    <div class="text-4xl font-bold mb-2">
                        <?= htmlspecialchars($article['graphic']['initials']) ?>
                    </div>
                    <div class="text-sm opacity-90">Kaszflow Blog</div>
                </div>
            </div>
            <?php endif; ?>
        </header>

        <!-- Article Content -->
        <article class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="prose prose-lg max-w-none">
                <?= $article['content'] ?>
            </div>
            
            <!-- Call to Action -->
            <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">
                    Sprawdź nasze porównywarki
                </h3>
                <p class="text-gray-600 mb-4">
                    Skorzystaj z naszych narzędzi do porównywania produktów finansowych i znajdź najlepsze oferty na rynku.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-between">
                    <a href="/kredyty-gotowkowe" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center flex-1 mr-1">
                        Porównaj kredyty
                    </a>
                    <a href="/konta-osobiste" 
                       class="bg-white text-blue-600 border border-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors text-center flex-1 mr-1">
                        Porównaj konta
                    </a>
                    <a href="/lokaty" 
                       class="bg-white text-blue-600 border border-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors text-center flex-1">
                        Porównaj lokaty
                    </a>
                </div>
            </div>
        </article>

        <!-- Related Articles -->
        <?php if (!empty($relatedArticles)): ?>
        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Powiązane artykuły</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($relatedArticles as $relatedArticle): ?>
                <article class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <!-- Article Image -->
                    <?php if (!empty($relatedArticle['image_url'])): ?>
                    <div class="w-full h-48 rounded-lg mb-4 overflow-hidden">
                        <img src="<?= htmlspecialchars($relatedArticle['image_url']) ?>" alt="<?= htmlspecialchars($relatedArticle['title']) ?>" class="w-full h-full object-cover">
                    </div>
                    <?php else: ?>
                    <!-- Fallback to gradient with initials -->
                    <div class="h-48 bg-gradient-to-br <?= $relatedArticle['graphic']['colors']['from'] ?> <?= $relatedArticle['graphic']['colors']['to'] ?> rounded-lg flex items-center justify-center mb-4">
                        <div class="text-white text-center">
                            <div class="text-2xl font-bold mb-2">
                                <?= htmlspecialchars($relatedArticle['graphic']['initials']) ?>
                            </div>
                            <div class="text-sm opacity-90">Kaszflow</div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            <?= $relatedArticle['category'] === 'Kredyty' ? 'bg-red-100 text-red-800' : 
                                ($relatedArticle['category'] === 'Hipoteki' ? 'bg-purple-100 text-purple-800' :
                                ($relatedArticle['category'] === 'Bankowość' ? 'bg-blue-100 text-blue-800' :
                                ($relatedArticle['category'] === 'Oszczędzanie' ? 'bg-green-100 text-green-800' :
                                ($relatedArticle['category'] === 'Ubezpieczenia' ? 'bg-orange-100 text-orange-800' :
                                ($relatedArticle['category'] === 'Firmy' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800'))))) ?>">
                            <?= $relatedArticle['category'] ?>
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <a href="/blog/<?= htmlspecialchars($relatedArticle['slug']) ?>" class="hover:text-blue-600">
                            <?= htmlspecialchars($relatedArticle['title']) ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-3">
                        <?= htmlspecialchars($relatedArticle['excerpt']) ?>
                    </p>
                    <div class="text-sm text-gray-500">
                        <span><?= $relatedArticle['published_at'] ?></span>
                        <span class="mx-2">•</span>
                        <span><?= $relatedArticle['read_time'] ?></span>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Newsletter Signup -->
        <section class="bg-blue-600 rounded-lg p-8 text-center text-white">
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
        </section>
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

    <style>
        .prose {
            color: #374151;
        }
        .prose h2 {
            color: #111827;
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .prose h3 {
            color: #111827;
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .prose p {
            margin-bottom: 1rem;
            line-height: 1.75;
        }
        .prose ul {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .prose li {
            margin-bottom: 0.5rem;
        }
        .prose strong {
            font-weight: 600;
        }
        .prose a {
            color: #2563eb;
            text-decoration: underline;
        }
        .prose a:hover {
            color: #1d4ed8;
        }
    </style>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
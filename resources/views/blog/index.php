<?php include __DIR__ . '/../layouts/main.php'; ?>

<!-- Blog Header -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Blog Kaszflow
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl mx-auto">
                Artykuły o finansach osobistych, kredytach, kontach i lokatach. 
                Ekspercka wiedza w przystępnej formie.
            </p>
        </div>
    </div>
</section>

<!-- Blog Posts -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
            <article class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <!-- Post Image -->
                <div class="h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                
                <!-- Post Content -->
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?= date('d.m.Y', strtotime($post['published_at'])) ?>
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                        <?= htmlspecialchars($post['title']) ?>
                    </h2>
                    
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        <?= htmlspecialchars($post['excerpt']) ?>
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <?= htmlspecialchars($post['author']) ?>
                        </div>
                        
                        <a href="/blog/<?= $post['slug'] ?>" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                            Czytaj więcej
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Newsletter Signup -->
        <div class="mt-16 bg-white rounded-lg shadow-md p-8 text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                Nie przegap nowych artykułów!
            </h3>
            <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                Zapisz się do newslettera i otrzymuj najnowsze artykuły o finansach osobistych 
                prosto na swoją skrzynkę email.
            </p>
            <form class="max-w-md mx-auto flex gap-4">
                <input type="email" 
                       placeholder="Twój adres email" 
                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Zapisz się
                </button>
            </form>
        </div>
    </div>
</section>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
       </style>
       
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
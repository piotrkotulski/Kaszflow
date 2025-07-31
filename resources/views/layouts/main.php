<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Kaszflow - Porównywarka Produktów Finansowych' ?></title>
    <meta name="description" content="<?= $pageDescription ?? 'Porównuj kredyty, konta, lokaty i inne produkty finansowe. Znajdź najlepsze oferty na rynku.' ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= $pageTitle ?? 'Kaszflow' ?>">
    <meta property="og:description" content="<?= $pageDescription ?? 'Porównywarka produktów finansowych' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $_ENV['APP_URL'] ?? 'https://kaszflow.pl' ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- CSS -->
    <link href="/assets/css/tailwind.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Google Analytics -->
    <?php if ($_ENV['GOOGLE_ANALYTICS_ID'] ?? false): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $_ENV['GOOGLE_ANALYTICS_ID'] ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= $_ENV['GOOGLE_ANALYTICS_ID'] ?>');
    </script>
    <?php endif; ?>
    
    <!-- Facebook Pixel -->
    <?php if ($_ENV['FACEBOOK_PIXEL_ID'] ?? false): ?>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= $_ENV['FACEBOOK_PIXEL_ID'] ?>');
        fbq('track', 'PageView');
    </script>
    <?php endif; ?>
</head>
<body class="bg-gray-50 font-inter flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="/" class="flex items-center">
                        <img class="h-8 w-auto" src="/assets/images/logo.svg" alt="Kaszflow">
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
                        <a href="/blog" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
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
                    <a href="/blog" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                        Blog
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        <div id="app">
            <?= $content ?? '' ?>
        </div>
    </main>



    <!-- JavaScript -->
    <!-- Vue.js temporarily disabled - using PHP-only approach -->
    <!-- <script src="/assets/js/app-f43646e7.js"></script> -->
    
    <!-- Newsletter Modal -->
    <div id="newsletter-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Newsletter</h3>
                    <button class="newsletter-close text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-gray-600 mb-4">
                    Otrzymuj najlepsze oferty finansowe prosto na swoją skrzynkę email.
                </p>
                <form id="newsletter-form" class="space-y-4">
                    <div>
                        <input type="email" name="email" placeholder="Twój adres email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Zapisz się
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

<script>
// Toggle details functionality
function toggleDetails(elementId) {
    console.log('toggleDetails called with:', elementId);
    const element = document.getElementById(elementId);
    console.log('Found element:', element);
    if (element) {
        const isHidden = element.classList.contains('hidden');
        console.log('Element is hidden:', isHidden);
        element.classList.toggle('hidden');
        console.log('Element hidden after toggle:', element.classList.contains('hidden'));
    } else {
        console.error('Element not found:', elementId);
    }
}

// Mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Newsletter modal functionality
    const newsletterModal = document.getElementById('newsletter-modal');
    const newsletterClose = document.querySelector('.newsletter-close');
    const newsletterForm = document.getElementById('newsletter-form');
    
    if (newsletterClose && newsletterModal) {
        newsletterClose.addEventListener('click', function() {
            newsletterModal.classList.add('hidden');
        });
    }
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Newsletter form handling would go here
            alert('Dziękujemy za zapisanie się do newslettera!');
            newsletterModal.classList.add('hidden');
        });
    }
    
    // Test if toggleDetails is available
    console.log('toggleDetails function available:', typeof toggleDetails);
});
</script>
</html> 
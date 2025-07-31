<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'O nas - Kaszflow') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'Poznaj naszą misję i wartości. Jesteśmy ekspertami w dziedzinie finansów osobistych.') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.8s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/" class="flex items-center">
                            <img class="h-8 w-auto" src="/assets/images/logo.svg" alt="Kaszflow">
                            <span class="ml-2 text-xl font-bold text-gray-900">Kaszflow</span>
                        </a>
                    </div>
                </div>
                
                <div class="hidden md:flex md:items-center">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/kredyty-gotowkowe" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Kredyty gotówkowe
                        </a>
                        <a href="/kredyty-hipoteczne" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Kredyty hipoteczne
                        </a>
                        <a href="/konta-osobiste" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Konta osobiste
                        </a>
                        <a href="/lokaty" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            Lokaty
                        </a>
                        <a href="/o-nas" class="text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            O nas
                        </a>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="text-gray-600 hover:text-blue-600 focus:outline-none focus:text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-100 py-24">
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-32 h-32 bg-blue-200 rounded-full opacity-20"></div>
            <div class="absolute bottom-20 right-20 w-24 h-24 bg-indigo-200 rounded-full opacity-30"></div>
            <div class="absolute top-1/2 left-1/3 w-16 h-16 bg-purple-200 rounded-full opacity-25"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center animate-fade-in">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-8">
                Poznaj <span class="text-blue-600">Kaszflow</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
                Twój zaufany partner w świecie finansów osobistych. Pomagamy znaleźć najlepsze kredyty, konta i lokaty.
            </p>
        </div>
    </section>

    <!-- O firmie -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-8">O firmie</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Jesteśmy zespołem ekspertów, którzy pomagają Polakom znaleźć najlepsze produkty finansowe
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <div class="text-center group p-8 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-all duration-300">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-300">
                        <span class="text-3xl font-bold text-blue-600">15+</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Banków partnerskich</h3>
                    <p class="text-gray-600">
                        Współpracujemy z największymi bankami w Polsce, aby zapewnić Ci najlepsze oferty
                    </p>
                </div>
                
                <div class="text-center group p-8 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-all duration-300">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-300">
                        <span class="text-3xl font-bold text-green-600">1000+</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Zadowolonych klientów</h3>
                    <p class="text-gray-600">
                        Pomogliśmy tysiącom Polaków znaleźć najlepsze produkty finansowe
                    </p>
                </div>
                
                <div class="text-center group p-8 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-all duration-300">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-300">
                        <span class="text-3xl font-bold text-purple-600">24/7</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Wsparcie</h3>
                    <p class="text-gray-600">
                        Jesteśmy dostępni zawsze, gdy potrzebujesz pomocy w wyborze produktu
                    </p>
                </div>
            </div>
            
            <!-- Dane firmy -->
            <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl shadow-xl p-12">
                <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center">Dane firmy</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-6 bg-white rounded-xl shadow-sm">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">NIP</p>
                                <p class="text-lg font-semibold text-gray-900">6272583881</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-white rounded-xl shadow-sm">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">REGON</p>
                                <p class="text-lg font-semibold text-gray-900">362237693</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-white rounded-xl shadow-sm">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Adres</p>
                                <p class="text-lg font-semibold text-gray-900">ul. Karola Miarki 9/16<br>41-500 Chorzów</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-white rounded-xl shadow-sm">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="text-lg font-semibold text-gray-900">piotr.kotulski@kaszflow.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- O Kaszflow -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="bg-white rounded-3xl shadow-xl p-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-8">O Kaszflow</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="space-y-6">
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Kaszflow to porównywarka produktów finansowych w Polsce, która pomaga znaleźć najlepsze kredyty gotówkowe, 
                            kredyty hipoteczne, konta osobiste i lokaty. Naszą misją jest pomoc w znalezieniu najkorzystniejszych 
                            ofert bankowych poprzez przejrzyste i obiektywne porównania produktów finansowych.
                        </p>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Dzięki współpracy z bankami, pomagamy Polakom znaleźć produkty dopasowane do ich potrzeb - 
                            od kredytów gotówkowych i hipotecznych, przez konta osobiste, po lokaty terminowe. 
                            Porównywarka finansowa Kaszflow to narzędzie do porównania kredytów, kont bankowych i lokat.
                        </p>
                    </div>
                    <div class="space-y-6">
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Specjalizujemy się w analizie produktów finansowych i doradztwie kredytowym. 
                            Nasze porównania obejmują kredyty gotówkowe, kredyty hipoteczne, konta osobiste 
                            oraz lokaty terminowe z największych banków w Polsce.
                        </p>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Oferujemy przejrzyste porównania z najniższym RRSO, najkorzystniejszymi warunkami 
                            i najwyższymi oprocentowaniami. Pomagamy znaleźć najlepsze kredyty i produkty bankowe 
                            dopasowane do indywidualnych potrzeb każdego klienta.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Zespół -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-8">Nasz zespół</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Poznaj ekspertów, którzy stoją za sukcesem Kaszflow
                </p>
            </div>
            
            <div class="max-w-5xl mx-auto">
                <div class="bg-gray-50 rounded-3xl shadow-xl p-12 transform hover:scale-105 transition-transform duration-500">
                    <div class="flex flex-col lg:flex-row items-center space-y-8 lg:space-y-0 lg:space-x-12">
                        <div class="flex-shrink-0">
                            <div class="w-48 h-48 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center shadow-2xl">
                                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                                    <span class="text-4xl font-bold text-blue-600">P</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 text-center lg:text-left">
                            <h3 class="text-3xl font-bold text-gray-900 mb-2">Piotr Kotulski</h3>
                            <p class="text-xl text-blue-600 font-medium mb-6">CEO & Założyciel Kaszflow</p>
                            <p class="text-gray-600 leading-relaxed mb-6 text-lg">
                                Doświadczony Manager z solidnym backgroundem w sektorze bankowym. Łączę umiejętności zarządzania zespołem 
                                i projektami z wiedzą techniczną w zakresie cyklu wytwarzania oprogramowania. Zdobyte przez lata kompetencje 
                                pozwalają mi skutecznie realizować projekty na styku biznesu i technologii.
                            </p>
                            <p class="text-gray-600 leading-relaxed mb-8 text-lg">
                                Od 2015 roku prowadzę własną działalność gospodarczą w zakresie doradztwa finansowego i business consulting. 
                                Specjalizuję się w analizie produktów finansowych i doradztwie kredytowym. 
                                Absolwent informatyki na Uniwersytecie WSB Merito w Chorzowie, specjalizacja Cloud developer.
                            </p>
                            <div class="flex justify-center lg:justify-start space-x-4">
                                <a href="https://www.linkedin.com/in/piotr-kotulski-86b76422b/" 
                                   class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" />
                                    </svg>
                                    LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Nasze wartości -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-8">Nasze wartości</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Wszystko co robimy, opiera się na kilku fundamentalnych zasadach
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <div class="text-center group p-8 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Przejrzystość</h3>
                    <p class="text-gray-600">
                        Wszystkie nasze porównania kredytów i produktów finansowych są obiektywne i przejrzyste. 
                        Nie ukrywamy żadnych kosztów ani warunków bankowych.
                    </p>
                </div>
                
                <div class="text-center group p-8 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Innowacyjność</h3>
                    <p class="text-gray-600">
                        Wykorzystujemy najnowsze technologie, aby zapewnić najlepsze doświadczenie użytkownika 
                        przy porównywaniu produktów finansowych i kredytów bankowych.
                    </p>
                </div>
                
                <div class="text-center group p-8 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Troska o klienta</h3>
                    <p class="text-gray-600">
                        Klient jest w centrum wszystkiego co robimy. Pomagamy znaleźć najlepsze kredyty gotówkowe, 
                        kredyty hipoteczne i konta osobiste dopasowane do indywidualnych potrzeb.
                    </p>
                </div>
            </div>
            
            <!-- Dodatkowa sekcja SEO -->
            <div class="bg-white rounded-3xl shadow-xl p-12">
                <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center">Dlaczego warto wybrać Kaszflow?</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div class="space-y-6">
                        <h4 class="text-2xl font-semibold text-gray-900 mb-4">Porównywarka kredytów</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">
                            Oferujemy porównanie kredytów gotówkowych i kredytów hipotecznych z największych banków w Polsce. 
                            Znajdziesz u nas najlepsze oferty kredytów z najniższym RRSO i najkorzystniejszymi warunkami.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Kredyty gotówkowe - porównanie rat i kosztów</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Kredyty hipoteczne - najlepsze oferty banków</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Kalkulator kredytowy online</span>
                            </li>
                        </ul>
                    </div>
                    <div class="space-y-6">
                        <h4 class="text-2xl font-semibold text-gray-900 mb-4">Porównywarka kont i lokat</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">
                            Sprawdź najlepsze konta osobiste z premią za otwarcie i lokaty terminowe z najwyższym oprocentowaniem. 
                            Porównywarka finansowa Kaszflow pomoże Ci znaleźć najkorzystniejsze produkty bankowe.
                        </p>
                        <ul class="space-y-3">
                            <li class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Konta osobiste z premią za otwarcie</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Lokaty terminowe - najwyższe oprocentowanie</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-700">Porównanie produktów bankowych</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-blue-600">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 text-center">
            <h2 class="text-4xl font-bold text-white mb-8">Gotowy na lepsze finanse?</h2>
            <p class="text-xl text-blue-100 mb-12 max-w-2xl mx-auto">
                Sprawdź nasze porównania i znajdź najlepsze oferty kredytów, kont i lokat
            </p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="/kredyty-gotowkowe" 
                   class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg font-semibold">
                    Porównaj kredyty
                </a>
                <a href="/konta-osobiste" 
                   class="inline-flex items-center px-8 py-4 border-2 border-white text-white rounded-lg hover:bg-white hover:text-blue-600 transition-all duration-300 transform hover:scale-105 font-semibold">
                    Sprawdź konta
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-6 sm:px-8 lg:px-12">
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
</body>
</html> 
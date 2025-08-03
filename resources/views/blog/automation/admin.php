<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automatyczny Blog Finansowy - Panel Administracyjny</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7l10 10M17 7L7 17"></path>
                        </svg>
                        <span class="ml-2 text-xl font-bold text-gray-900">Kaszflow</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/blog/automation/settings" class="text-blue-600 hover:text-blue-800">Ustawienia</a>
                    <a href="/" class="text-gray-600 hover:text-gray-800">Powrót do strony głównej</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Panel Automatyzacji Blogów</h2>
            <p class="text-gray-600">System automatycznie generuje artykuły na podstawie trendów finansowych i polskich uwarunkowań prawnych.</p>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Generowanie Artykułów</h3>
                <p class="text-gray-600 mb-4">Wygeneruj artykuł na podstawie aktualnych trendów</p>
                <button id="generate-article-btn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Wygeneruj artykuł teraz
                </button>
                <div id="generation-status" class="mt-3 text-sm"></div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Planowanie</h3>
                <p class="text-gray-600 mb-4">Ustaw automatyczne generowanie</p>
                <select id="frequency-select" class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-3">
                    <option value="daily">Codziennie</option>
                    <option value="weekly">Raz w tygodniu</option>
                    <option value="twice_weekly">2 razy w tygodniu</option>
                </select>
                <button id="schedule-btn" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Zaplanuj
                </button>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statystyki</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Łącznie artykułów:</span>
                        <span class="font-semibold"><?= $data['statistics']['total_articles'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">W tym miesiącu:</span>
                        <span class="font-semibold"><?= $data['statistics']['published_this_month'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Łącznie wyświetleń:</span>
                        <span class="font-semibold"><?= number_format($data['statistics']['total_views']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Artykuły</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $data['statistics']['total_articles'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Wyświetlenia</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= number_format($data['statistics']['total_views']) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Średnio/artykuł</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $data['statistics']['average_views_per_article'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Następne generowanie</p>
                        <p class="text-sm font-semibold text-gray-900"><?= date('d.m H:i', strtotime($data['statistics']['next_generation'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topic Suggestions -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Sugestie Tematów na Tydzień</h3>
                    <button id="refresh-suggestions-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Odśwież sugestie
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-1">Tematy sugerowane na podstawie analizy danych rynkowych, RSS feedów i trendów sezonowych</p>
            </div>
            
            <div class="p-6">
                <div id="suggestions-loading" class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-gray-600 mt-2">Ładowanie sugestii...</p>
                </div>
                
                <div id="suggestions-content" class="hidden">
                    <div class="mb-4">
                        <h4 class="text-md font-semibold text-gray-900 mb-2">Tydzień: <span id="suggestions-period"></span></h4>
                        <p class="text-sm text-gray-600">Znaleziono <span id="suggestions-count"></span> sugestii</p>
                    </div>
                    
                    <div id="suggestions-list" class="space-y-4">
                        <!-- Sugestie będą ładowane dynamicznie -->
                    </div>
                </div>
                
                <div id="suggestions-error" class="hidden text-center py-8">
                    <div class="text-red-600 mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600">Błąd podczas ładowania sugestii</p>
                    <button id="retry-suggestions-btn" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Spróbuj ponownie
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Articles -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Ostatnie Artykuły</h3>
                    <div class="flex space-x-2">
                        <button id="regenerate-images-btn" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                            Zregeneruj obrazki
                        </button>
                        <button id="generate-article-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Wygeneruj artykuł teraz
                        </button>
                    </div>
                </div>
                <div id="debug-info" class="text-sm text-gray-500 mt-2"></div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tytuł</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data utworzenia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="articles-list">
                        <!-- Artykuły będą ładowane przez JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Article Preview Modal -->
        <div id="article-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Podgląd artykułu</h3>
                        <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Podgląd artykułu -->
                    <div id="modal-content" class="max-h-96 overflow-y-auto">
                        <!-- Treść artykułu będzie ładowana przez JavaScript -->
                    </div>
                    
                    <!-- Formularz edycji (ukryty domyślnie) -->
                    <div id="edit-form" class="hidden">
                        <div class="mb-4">
                            <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-2">Tytuł artykułu</label>
                            <input type="text" id="edit-title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="edit-content" class="block text-sm font-medium text-gray-700 mb-2">Treść artykułu (HTML)</label>
                            <textarea id="edit-content" rows="15" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="edit-meta-description" class="block text-sm font-medium text-gray-700 mb-2">Meta opis</label>
                            <textarea id="edit-meta-description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="edit-category" class="block text-sm font-medium text-gray-700 mb-2">Kategoria</label>
                            <select id="edit-category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="kredyty">Kredyty</option>
                                <option value="hipoteki">Hipoteki</option>
                                <option value="oszczędzanie">Oszczędzanie</option>
                                <option value="bankowość">Bankowość</option>
                                <option value="firmy">Firmy</option>
                                <option value="finanse osobiste">Finanse osobiste</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-4">
                        <button id="publish-article" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">Opublikuj</button>
                        <button id="edit-article" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Edytuj</button>
                        <button id="save-edit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors hidden">Zapisz zmiany</button>
                        <button id="cancel-edit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors hidden">Anuluj</button>
                        <button id="delete-article" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">Usuń</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scheduled Jobs -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Zaplanowane Zadania</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($data['scheduled_jobs'] as $job): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($job['name']) ?></h4>
                            <p class="text-sm text-gray-500">Częstotliwość: <?= $job['frequency'] ?></p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">
                                Następne uruchomienie: <?= date('d.m.Y H:i', strtotime($job['next_run'])) ?>
                            </span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?= $job['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= $job['status'] === 'active' ? 'Aktywne' : 'Nieaktywne' ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            console.log('JavaScript loaded successfully');
            
            let currentArticleId = null;
            
            // Ładowanie listy artykułów
            function loadArticles() {
                console.log('loadArticles() called');
                $('#debug-info').text('Ładowanie artykułów...');
                
                $.get('/blog/automation/articles', function(response) {
                    console.log('Articles response:', response);
                    $('#debug-info').text('Otrzymano odpowiedź: ' + (response.success ? 'sukces' : 'błąd'));
                    
                    if (response.success) {
                        const articles = response.articles;
                        const tbody = $('#articles-list');
                        tbody.empty();
                        
                        console.log('Articles count:', Object.keys(articles).length);
                        $('#debug-info').text('Znaleziono ' + Object.keys(articles).length + ' artykułów');
                        
                        Object.values(articles).forEach(article => {
                            const statusClass = article.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                            const statusText = article.status === 'published' ? 'Opublikowany' : 'Szkic';
                            
                            const row = `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">${article.title}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                                            ${statusText}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${new Date(article.created_at).toLocaleDateString('pl-PL')}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button class="text-blue-600 hover:text-blue-900 view-article" data-id="${article.id}">
                                                Podgląd
                                            </button>
                                            ${article.status === 'draft' ? 
                                                `<button class="text-green-600 hover:text-green-900 publish-article" data-id="${article.id}">
                                                    Opublikuj
                                                </button>` : ''
                                            }
                                            <button class="text-red-600 hover:text-red-900 delete-article" data-id="${article.id}">
                                                Usuń
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    } else {
                        $('#debug-info').text('Błąd: ' + (response.message || 'Nieznany błąd'));
                    }
                }).fail(function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    $('#debug-info').text('Błąd AJAX: ' + error);
                });
            }
            
            // Generowanie artykułu
            $('#generate-article-btn').click(function() {
                const btn = $(this);
                const status = $('#generation-status');
                
                btn.prop('disabled', true).text('Generowanie...');
                status.html('<span class="text-blue-600">Generowanie artykułu...</span>');
                
                $.post('/blog/automation/generate', {engine: 'openai'}, function(response) {
                    if (response.success) {
                        status.html('<span class="text-green-600">Artykuł wygenerowany pomyślnie!</span>');
                        loadArticles();
                    } else {
                        status.html('<span class="text-red-600">Błąd: ' + response.message + '</span>');
                    }
                    btn.prop('disabled', false).text('Wygeneruj artykuł teraz');
                });
            });
            
            // Regenerowanie obrazów
            $('#regenerate-images-btn').click(function() {
                const btn = $(this);
                
                if (!confirm('Czy na pewno chcesz zregenerować obrazki dla wszystkich artykułów? To może zająć kilka minut.')) {
                    return;
                }
                
                btn.prop('disabled', true).text('Regenerowanie obrazków...');
                
                $.post('/blog/automation/regenerate-images', function(response) {
                    if (response.success) {
                        alert('Zregenerowano obrazki dla ' + response.results.length + ' artykułów!');
                        loadArticles(); // Odśwież listę artykułów
                    } else {
                        alert('Błąd podczas regenerowania obrazków: ' + response.message);
                    }
                    btn.prop('disabled', false).text('Zregeneruj obrazki');
                }).fail(function(xhr, status, error) {
                    alert('Błąd połączenia: ' + error);
                    btn.prop('disabled', false).text('Zregeneruj obrazki');
                });
            });
            
            // Planowanie automatycznego generowania
            $('#schedule-btn').click(function() {
                const btn = $(this);
                const frequency = $('#frequency-select').val();
                
                btn.prop('disabled', true).text('Planowanie...');
                
                $.post('/blog/automation/schedule', {
                    frequency: frequency
                }, function(response) {
                    if (response.success) {
                        alert('Automatyczne generowanie zostało zaplanowane!');
                        // Zamiast reload, zaktualizuj tylko potrzebne elementy
                        updateScheduledJobsDisplay();
                        updateNextGenerationDisplay();
                    } else {
                        alert('Błąd podczas planowania: ' + response.message);
                    }
                    btn.prop('disabled', false).text('Zaplanuj');
                }).fail(function(xhr, status, error) {
                    alert('Błąd połączenia: ' + error);
                    btn.prop('disabled', false).text('Zaplanuj');
                });
            });
            
            // Funkcja do aktualizacji wyświetlania zaplanowanych zadań
            function updateScheduledJobsDisplay() {
                $.get('/blog/automation/admin', function(html) {
                    // Wyciągnij sekcję zaplanowanych zadań z odpowiedzi
                    const tempDiv = $('<div>').html(html);
                    const scheduledJobsSection = tempDiv.find('.bg-white.rounded-lg.shadow:has(h3:contains("Zaplanowane Zadania"))');
                    $('.bg-white.rounded-lg.shadow:has(h3:contains("Zaplanowane Zadania"))').replaceWith(scheduledJobsSection);
                });
            }
            
            // Zapamiętaj wybraną wartość w dropdown
            $('#frequency-select').change(function() {
                localStorage.setItem('selectedFrequency', $(this).val());
            });
            
            // Przywróć wybraną wartość przy ładowaniu strony
            $(document).ready(function() {
                const savedFrequency = localStorage.getItem('selectedFrequency');
                if (savedFrequency) {
                    $('#frequency-select').val(savedFrequency);
                }
            });
            
            // Przywróć wartość po odświeżeniu strony
            $(window).on('load', function() {
                const savedFrequency = localStorage.getItem('selectedFrequency');
                if (savedFrequency) {
                    setTimeout(function() {
                        $('#frequency-select').val(savedFrequency);
                    }, 100);
                }
            });
            
            // Funkcja do aktualizacji wyświetlania następnego generowania
            function updateNextGenerationDisplay() {
                $.get('/blog/automation/admin', function(html) {
                    const tempDiv = $('<div>').html(html);
                    const nextGenerationElement = tempDiv.find('.bg-white.rounded-lg.shadow:has(.text-purple-600) .text-sm.font-semibold.text-gray-900');
                    $('.bg-white.rounded-lg.shadow:has(.text-purple-600) .text-sm.font-semibold.text-gray-900').text(nextGenerationElement.text());
                });
            }
            
            // Podgląd artykułu
            $(document).on('click', '.view-article', function() {
                const articleId = $(this).data('id');
                currentArticleId = articleId;
                
                // Pobierz szczegóły artykułu
                $.get('/blog/automation/articles', function(response) {
                    if (response.success && response.articles[articleId]) {
                        const article = response.articles[articleId];
                        $('#modal-title').text(article.title);
                        $('#modal-content').html(article.content);
                        
                        // Wypełnij formularz edycji
                        $('#edit-title').val(article.title);
                        $('#edit-content').val(article.content);
                        $('#edit-meta-description').val(article.meta_description || '');
                        $('#edit-category').val(article.category || 'finanse osobiste');
                        
                        // Pokaż podgląd, ukryj edycję
                        $('#modal-content').show();
                        $('#edit-form').hide();
                        $('#edit-article').show();
                        $('#save-edit').hide();
                        $('#cancel-edit').hide();
                        
                        $('#article-modal').removeClass('hidden');
                    }
                });
            });
            
            // Przełączanie do trybu edycji
            $('#edit-article').click(function() {
                $('#modal-content').hide();
                $('#edit-form').show();
                $('#edit-article').hide();
                $('#save-edit').show();
                $('#cancel-edit').show();
                $('#modal-title').text('Edycja artykułu');
            });
            
            // Anulowanie edycji
            $('#cancel-edit').click(function() {
                $('#modal-content').show();
                $('#edit-form').hide();
                $('#edit-article').show();
                $('#save-edit').hide();
                $('#cancel-edit').hide();
                $('#modal-title').text('Podgląd artykułu');
            });
            
            // Zapisywanie zmian
            $('#save-edit').click(function() {
                if (!currentArticleId) return;
                
                const btn = $(this);
                btn.prop('disabled', true).text('Zapisywanie...');
                
                const editData = {
                    article_id: currentArticleId,
                    title: $('#edit-title').val(),
                    content: $('#edit-content').val(),
                    meta_description: $('#edit-meta-description').val(),
                    category: $('#edit-category').val()
                };
                
                $.post('/blog/automation/edit', editData, function(response) {
                    if (response.success) {
                        alert('Artykuł został zaktualizowany!');
                        loadArticles();
                        
                        // Wróć do trybu podglądu
                        $('#modal-content').show();
                        $('#edit-form').hide();
                        $('#edit-article').show();
                        $('#save-edit').hide();
                        $('#cancel-edit').hide();
                        $('#modal-title').text('Podgląd artykułu');
                        
                        // Odśwież treść w podglądzie
                        $('#modal-content').html(editData.content);
                    } else {
                        alert('Błąd: ' + response.message);
                    }
                    btn.prop('disabled', false).text('Zapisz zmiany');
                });
            });
            
            // Zamykanie modala
            $('#close-modal').click(function() {
                $('#article-modal').addClass('hidden');
            });
            
            // Publikowanie artykułu
            $('#publish-article').click(function() {
                if (!currentArticleId) return;
                
                $.post('/blog/automation/publish', {article_id: currentArticleId}, function(response) {
                    if (response.success) {
                        $('#article-modal').addClass('hidden');
                        loadArticles();
                        alert('Artykuł został opublikowany!');
                    } else {
                        alert('Błąd: ' + response.message);
                    }
                });
            });
            
            // Usuwanie artykułu
            $('#delete-article').click(function() {
                if (!currentArticleId) return;
                
                if (confirm('Czy na pewno chcesz usunąć ten artykuł?')) {
                    $.post('/blog/automation/delete', {article_id: currentArticleId}, function(response) {
                        if (response.success) {
                            $('#article-modal').addClass('hidden');
                            loadArticles();
                            updateStatistics(); // Odśwież statystyki
                            alert('Artykuł został usunięty!');
                        } else {
                            alert('Błąd: ' + response.message);
                        }
                    });
                }
            });
            
            // Publikowanie z listy
            $(document).on('click', '.publish-article', function() {
                const articleId = $(this).data('id');
                
                $.post('/blog/automation/publish', {article_id: articleId}, function(response) {
                    if (response.success) {
                        loadArticles();
                        alert('Artykuł został opublikowany!');
                    } else {
                        alert('Błąd: ' + response.message);
                    }
                });
            });
            
            // Usuwanie z listy
            $(document).on('click', '.delete-article', function() {
                const articleId = $(this).data('id');
                
                if (confirm('Czy na pewno chcesz usunąć ten artykuł?')) {
                    $.post('/blog/automation/delete', {article_id: articleId}, function(response) {
                        if (response.success) {
                            loadArticles();
                            updateStatistics(); // Odśwież statystyki
                            alert('Artykuł został usunięty!');
                        } else {
                            alert('Błąd: ' + response.message);
                        }
                    });
                }
            });
            
            // Załadowanie artykułów przy starcie
            loadArticles();
            
            // Funkcje dla sugestii tematów
            function loadSuggestions() {
                console.log('loadSuggestions() called');
                $('#suggestions-loading').show();
                $('#suggestions-content').hide();
                $('#suggestions-error').hide();
                
                $.get('/blog/automation/suggestions', function(response) {
                    console.log('Suggestions response:', response);
                    $('#suggestions-loading').hide();
                    
                    if (response.success) {
                        displaySuggestions(response.suggestions);
                        $('#suggestions-content').show();
                    } else {
                        $('#suggestions-error').show();
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Suggestions AJAX error:', error);
                    $('#suggestions-loading').hide();
                    $('#suggestions-error').show();
                });
            }
            
            function displaySuggestions(data) {
                $('#suggestions-period').text(data.week.period);
                $('#suggestions-count').text(data.total);
                
                const suggestionsList = $('#suggestions-list');
                suggestionsList.empty();
                
                data.suggestions.forEach(function(suggestion, index) {
                    const priorityClass = suggestion.priority >= 8 ? 'border-l-4 border-red-500' : 
                                       suggestion.priority >= 6 ? 'border-l-4 border-yellow-500' : 
                                       'border-l-4 border-green-500';
                    
                    const categoryBadge = getCategoryBadge(suggestion.category);
                    
                    const suggestionHtml = `
                        <div class="bg-gray-50 rounded-lg p-4 ${priorityClass}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="text-sm font-medium text-gray-900">${index + 1}.</span>
                                        <h4 class="text-md font-semibold text-gray-900 ml-2">${suggestion.topic}</h4>
                                        ${categoryBadge}
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">${suggestion.reason}</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <span class="mr-4">Priorytet: ${suggestion.priority}/10</span>
                                        <span>Kategoria: ${suggestion.category}</span>
                                    </div>
                                </div>
                                <button class="generate-from-suggestion ml-4 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors"
                                        data-topic="${suggestion.topic}"
                                        data-category="${suggestion.category}"
                                        data-keywords="${suggestion.keywords.join(',')}">
                                    Generuj artykuł
                                </button>
                            </div>
                        </div>
                    `;
                    
                    suggestionsList.append(suggestionHtml);
                });
            }
            
            function getCategoryBadge(category) {
                const badges = {
                    'kredyty': '<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded ml-2">Kredyty</span>',
                    'hipoteki': '<span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded ml-2">Hipoteki</span>',
                    'oszczędzanie': '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded ml-2">Oszczędzanie</span>',
                    'bankowość': '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded ml-2">Bankowość</span>',
                    'ubezpieczenia': '<span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded ml-2">Ubezpieczenia</span>',
                    'firmy': '<span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded ml-2">Firmy</span>',
                    'finanse': '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded ml-2">Oszczędzanie</span>',
                    'finanse osobiste': '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded ml-2">Oszczędzanie</span>'
                };
                
                return badges[category] || '<span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded ml-2">' + category + '</span>';
            }
            
            // Generowanie artykułu z sugestii
            $(document).on('click', '.generate-from-suggestion', function() {
                const btn = $(this);
                const topic = btn.data('topic');
                const category = btn.data('category');
                const keywords = btn.data('keywords');
                
                console.log('Generating from suggestion:', { topic, category, keywords });
                
                btn.prop('disabled', true).text('Generowanie...');
                
                $.post('/blog/automation/generate-from-suggestion', {
                    topic: topic,
                    category: category,
                    keywords: keywords
                }, function(response) {
                    console.log('Generate from suggestion response:', response);
                    if (response.success) {
                        alert('Artykuł został wygenerowany pomyślnie!');
                        loadArticles();
                        loadSuggestions(); // Odśwież sugestie
                        updateStatistics(); // Odśwież statystyki
                    } else {
                        alert('Błąd: ' + (response.message || response.error || 'Nieznany błąd'));
                    }
                    btn.prop('disabled', false).text('Generuj artykuł');
                }).fail(function(xhr, status, error) {
                    console.error('Generate from suggestion AJAX error:', { xhr, status, error });
                    alert('Błąd połączenia: ' + error);
                    btn.prop('disabled', false).text('Generuj artykuł');
                });
            });
            
            // Funkcja do aktualizacji statystyk
            function updateStatistics() {
                $.get('/blog/automation/admin', function(html) {
                    const tempDiv = $('<div>').html(html);
                    
                    // Aktualizuj licznik artykułów
                    const totalArticles = tempDiv.find('.bg-white.rounded-lg.shadow:has(.text-blue-600) .text-2xl.font-semibold.text-gray-900').text();
                    $('.bg-white.rounded-lg.shadow:has(.text-blue-600) .text-2xl.font-semibold.text-gray-900').text(totalArticles);
                    
                    // Aktualizuj statystyki w sekcji
                    const statsTotal = tempDiv.find('.bg-white.rounded-lg.shadow:has(.text-gray-600:contains("Łącznie artykułów")) .font-semibold').first().text();
                    $('.bg-white.rounded-lg.shadow:has(.text-gray-600:contains("Łącznie artykułów")) .font-semibold').first().text(statsTotal);
                    
                    const statsMonth = tempDiv.find('.bg-white.rounded-lg.shadow:has(.text-gray-600:contains("W tym miesiącu")) .font-semibold').first().text();
                    $('.bg-white.rounded-lg.shadow:has(.text-gray-600:contains("W tym miesiącu")) .font-semibold').first().text(statsMonth);
                });
            }
            
            // Odświeżanie sugestii
            $('#refresh-suggestions-btn, #retry-suggestions-btn').click(function() {
                loadSuggestions();
            });
            
            // Załadowanie sugestii przy starcie
            loadSuggestions();
        });
    </script>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ustawienia Automatyzacji Blogów - Kaszflow</title>
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
                    <h1 class="text-xl font-semibold text-gray-900">Ustawienia Automatyzacji</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/blog/automation/admin" class="text-blue-600 hover:text-blue-800">Panel główny</a>
                    <a href="/" class="text-gray-600 hover:text-gray-800">Powrót do strony głównej</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Ustawienia Automatyzacji Blogów</h2>
            <p class="text-gray-600">Skonfiguruj parametry automatycznego generowania artykułów finansowych.</p>
        </div>

        <!-- Settings Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Konfiguracja Systemu</h3>
            </div>
            
            <form id="settings-form" class="p-6 space-y-6">
                <!-- OpenAI API Configuration -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Konfiguracja OpenAI API</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="openai_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                Klucz API OpenAI
                            </label>
                            <input type="password" 
                                   id="openai_api_key" 
                                   name="openai_api_key" 
                                   value="<?= htmlspecialchars($data['settings']['openai_api_key'] ?? '') ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="sk-...">
                            <p class="text-sm text-gray-500 mt-1">
                                Wymagany do generowania treści AI. Możesz go znaleźć w 
                                <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">
                                    panelu OpenAI
                                </a>.
                            </p>
                        </div>
                        
                        <div class="flex items-center">
                            <button type="button" id="test-api-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Testuj połączenie
                            </button>
                            <div id="api-test-result" class="ml-3 text-sm"></div>
                        </div>
                    </div>
                </div>

                <!-- Claude AI API Configuration -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Konfiguracja Claude AI API</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="claude_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                Klucz API Claude AI (Anthropic)
                            </label>
                            <input type="password" 
                                   id="claude_api_key" 
                                   name="claude_api_key" 
                                   value="<?= htmlspecialchars($data['settings']['claude_api_key'] ?? '') ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="sk-ant-...">
                            <p class="text-sm text-gray-500 mt-1">
                                Alternatywny silnik AI. Możesz go znaleźć w 
                                <a href="https://console.anthropic.com/" target="_blank" class="text-blue-600 hover:underline">
                                    panelu Anthropic
                                </a>.
                            </p>
                        </div>
                        
                        <div class="flex items-center">
                            <button type="button" id="test-claude-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                Testuj połączenie Claude
                            </button>
                            <div id="claude-test-result" class="ml-3 text-sm"></div>
                        </div>
                    </div>
                </div>

                <!-- AI Engine Selection -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Wybór Silnika AI</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="ai_engine" class="block text-sm font-medium text-gray-700 mb-2">
                                Domyślny silnik AI
                            </label>
                            <select id="ai_engine" 
                                    name="ai_engine" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="openai" <?= ($data['settings']['ai_engine'] ?? 'openai') === 'openai' ? 'selected' : '' ?>>
                                    OpenAI GPT-4 (zalecane)
                                </option>
                                <option value="claude" <?= ($data['settings']['ai_engine'] ?? 'openai') === 'claude' ? 'selected' : '' ?>>
                                    Claude AI (Anthropic)
                                </option>
                                <option value="auto" <?= ($data['settings']['ai_engine'] ?? 'openai') === 'auto' ? 'selected' : '' ?>>
                                    Automatyczny wybór
                                </option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">
                                System automatycznie wybierze dostępny silnik lub użyje wybranego domyślnie.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Generation Settings -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Ustawienia Generowania</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="generation_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                                Częstotliwość generowania
                            </label>
                            <select id="generation_frequency" 
                                    name="generation_frequency" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="daily" <?= ($data['settings']['generation_frequency'] ?? 'daily') === 'daily' ? 'selected' : '' ?>>
                                    Codziennie
                                </option>
                                <option value="weekly" <?= ($data['settings']['generation_frequency'] ?? 'daily') === 'weekly' ? 'selected' : '' ?>>
                                    Raz w tygodniu
                                </option>
                                <option value="twice_weekly" <?= ($data['settings']['generation_frequency'] ?? 'daily') === 'twice_weekly' ? 'selected' : '' ?>>
                                    2 razy w tygodniu
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="auto_publish" class="flex items-center">
                                <input type="checkbox" 
                                       id="auto_publish" 
                                       name="auto_publish" 
                                       <?= ($data['settings']['auto_publish'] ?? false) ? 'checked' : '' ?>
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">
                                    Automatyczne publikowanie
                                </span>
                            </label>
                            <p class="text-sm text-gray-500 mt-1">
                                Artykuły będą automatycznie publikowane bez wymagania ręcznej weryfikacji
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content Settings -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Ustawienia Treści</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="min_article_length" class="block text-sm font-medium text-gray-700 mb-2">
                                Minimalna długość artykułu (słowa)
                            </label>
                            <input type="number" 
                                   id="min_article_length" 
                                   name="min_article_length" 
                                   value="<?= $data['settings']['min_article_length'] ?? 800 ?>"
                                   min="500" 
                                   max="2000"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="max_article_length" class="block text-sm font-medium text-gray-700 mb-2">
                                Maksymalna długość artykułu (słowa)
                            </label>
                            <input type="number" 
                                   id="max_article_length" 
                                   name="max_article_length" 
                                   value="<?= $data['settings']['max_article_length'] ?? 1200 ?>"
                                   min="800" 
                                   max="3000"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Target Categories -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Kategorie Docelowe</h4>
                    
                    <div class="space-y-3">
                        <?php 
                        $categories = [
                            'kredyty' => 'Kredyty i pożyczki',
                            'bankowość' => 'Bankowość osobista',
                            'oszczędzanie' => 'Oszczędzanie i inwestowanie',
                            'finanse osobiste' => 'Finanse osobiste',
                            'aktualności' => 'Aktualności finansowe'
                        ];
                        
                        $selectedCategories = $data['settings']['target_categories'] ?? ['kredyty', 'bankowość', 'oszczędzanie'];
                        ?>
                        
                        <?php foreach ($categories as $key => $name): ?>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="target_categories[]" 
                                   value="<?= $key ?>"
                                   <?= in_array($key, $selectedCategories) ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                <?= htmlspecialchars($name) ?>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Custom Topics -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Własne Tematy Artykułów</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Wprowadź własne tematy artykułów (jeden na linię). System będzie generował artykuły na te tematy zamiast automatycznych.
                    </p>
                    
                    <div>
                        <label for="custom_topics" class="block text-sm font-medium text-gray-700 mb-2">
                            Lista tematów
                        </label>
                        <textarea id="custom_topics" 
                                  name="custom_topics" 
                                  rows="8"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Przykłady:
Jak wybrać najlepszy kredyt hipoteczny w 2025?
Bon energetyczny - czy warto się ubiegać?
Split Payment dla firm - korzyści i ryzyka
Refinansowanie kredytu - kiedy się opłaca?
Faktoring - rozwiązanie problemów z płynnością"><?= htmlspecialchars($data['settings']['custom_topics'] ?? '') ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">
                            Każdy temat w nowej linii. System będzie generował artykuły na te tematy w kolejności.
                        </p>
                    </div>
                </div>

                <!-- Custom Keywords -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Własne Słowa Kluczowe</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Dodaj słowa kluczowe, które mają być używane we wszystkich generowanych artykułach.
                    </p>
                    
                    <div>
                        <label for="custom_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                            Słowa kluczowe
                        </label>
                        <textarea id="custom_keywords" 
                                  name="custom_keywords" 
                                  rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Przykłady:
finanse osobiste
planowanie finansowe
inwestycje
oszczędzanie
kredyty
bankowość"><?= htmlspecialchars($data['settings']['custom_keywords'] ?? '') ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">
                            Każde słowo kluczowe w nowej linii. Będą dodane do wszystkich artykułów.
                        </p>
                    </div>
                </div>

                <!-- Affiliate Links -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Linki Afiliacyjne</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Dodaj linki afiliacyjne w formacie: słowo_kluczowe|link_url
                    </p>
                    
                    <div>
                        <label for="affiliate_links" class="block text-sm font-medium text-gray-700 mb-2">
                            Linki afiliacyjne
                        </label>
                        <textarea id="affiliate_links" 
                                  name="affiliate_links" 
                                  rows="6"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Przykłady:
kredyt hipoteczny|https://example.com/affiliate/hipoteka
lokata|https://example.com/affiliate/lokata
konto osobiste|https://example.com/affiliate/konto
fundusz inwestycyjny|https://example.com/affiliate/fundusz"><?= htmlspecialchars($data['settings']['affiliate_links'] ?? '') ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">
                            Format: słowo_kluczowe|link_url (jeden na linię). Linki będą używane naturalnie w tekście.
                        </p>
                    </div>
                </div>

                <!-- Topic Blacklist -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Czarna Lista Tematów</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Tematy, które NIE mają być generowane (jeden na linię).
                    </p>
                    
                    <div>
                        <label for="topic_blacklist" class="block text-sm font-medium text-gray-700 mb-2">
                            Zakazane tematy
                        </label>
                        <textarea id="topic_blacklist" 
                                  name="topic_blacklist" 
                                  rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Przykłady:
Bezpieczny Kredyt 2%
RRSO
Split Payment"><?= htmlspecialchars($data['settings']['topic_blacklist'] ?? '') ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">
                            Każdy zakazany temat w nowej linii. System nie będzie generował artykułów na te tematy.
                        </p>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="border-b border-gray-200 pb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Ustawienia SEO</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="default_meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Domyślny prefix meta title
                            </label>
                            <input type="text" 
                                   id="default_meta_title" 
                                   name="default_meta_title" 
                                   value="<?= htmlspecialchars($data['settings']['default_meta_title'] ?? 'Kaszflow - ') ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Kaszflow - ">
                        </div>
                        
                        <div>
                            <label for="default_meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Domyślny suffix meta description
                            </label>
                            <input type="text" 
                                   id="default_meta_description" 
                                   name="default_meta_description" 
                                   value="<?= htmlspecialchars($data['settings']['default_meta_description'] ?? ' | Porównywarka produktów finansowych') ?>"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder=" | Porównywarka produktów finansowych">
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div>
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Ustawienia Zaawansowane</h4>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="openai_model" class="block text-sm font-medium text-gray-700 mb-2">
                                Model OpenAI
                            </label>
                            <select id="openai_model" 
                                    name="openai_model" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="gpt-4" <?= ($data['settings']['openai_model'] ?? 'gpt-4') === 'gpt-4' ? 'selected' : '' ?>>
                                    GPT-4 (najlepsza jakość)
                                </option>
                                <option value="gpt-3.5-turbo" <?= ($data['settings']['openai_model'] ?? 'gpt-4') === 'gpt-3.5-turbo' ? 'selected' : '' ?>>
                                    GPT-3.5 Turbo (szybszy, tańszy)
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-2">
                                Temperatura (kreatywność)
                            </label>
                            <input type="range" 
                                   id="temperature" 
                                   name="temperature" 
                                   min="0" 
                                   max="1" 
                                   step="0.1"
                                   value="<?= $data['settings']['temperature'] ?? 0.7 ?>"
                                   class="w-full">
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0.0 (konserwatywne)</span>
                                <span id="temperature-value">0.7</span>
                                <span>1.0 (kreatywne)</span>
                            </div>
                        </div>
                        
                        <div>
                            <label for="max_tokens" class="block text-sm font-medium text-gray-700 mb-2">
                                Maksymalna liczba tokenów
                            </label>
                            <input type="number" 
                                   id="max_tokens" 
                                   name="max_tokens" 
                                   value="<?= $data['settings']['max_tokens'] ?? 2000 ?>"
                                   min="1000" 
                                   max="4000"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end space-x-4 pt-6">
                    <button type="button" id="reset-btn" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        Przywróć domyślne
                    </button>
                    <button type="submit" id="save-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Zapisz ustawienia
                    </button>
                </div>
            </form>
        </div>

        <!-- Status Messages -->
        <div id="status-message" class="mt-4"></div>
    </div>

    <script>
        $(document).ready(function() {
            // Temperature slider
            $('#temperature').on('input', function() {
                $('#temperature-value').text($(this).val());
            });

            // Test API connection
            $('#test-api-btn').click(function() {
                var btn = $(this);
                var result = $('#api-test-result');
                
                btn.prop('disabled', true).text('Testowanie...');
                result.html('<span class="text-blue-600">Sprawdzanie połączenia...</span>');
                
                $.ajax({
                    url: '/blog/automation/test-api',
                    type: 'POST',
                    data: {
                        api_key: $('#openai_api_key').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            result.html('<span class="text-green-600">✓ Połączenie udane</span>');
                        } else {
                            result.html('<span class="text-red-600">✗ Błąd: ' + response.message + '</span>');
                        }
                        btn.prop('disabled', false).text('Testuj połączenie');
                    },
                    error: function() {
                        result.html('<span class="text-red-600">✗ Wystąpił błąd podczas testowania</span>');
                        btn.prop('disabled', false).text('Testuj połączenie');
                    }
                });
            });

            // Test Claude AI API connection
            $('#test-claude-btn').click(function() {
                var btn = $(this);
                var result = $('#claude-test-result');
                
                btn.prop('disabled', true).text('Testowanie...');
                result.html('<span class="text-blue-600">Sprawdzanie połączenia Claude...</span>');
                
                $.ajax({
                    url: '/blog/automation/test-claude',
                    type: 'POST',
                    data: {
                        api_key: $('#claude_api_key').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            result.html('<span class="text-green-600">✓ Połączenie Claude udane</span>');
                        } else {
                            result.html('<span class="text-red-600">✗ Błąd Claude: ' + response.message + '</span>');
                        }
                        btn.prop('disabled', false).text('Testuj połączenie Claude');
                    },
                    error: function() {
                        result.html('<span class="text-red-600">✗ Wystąpił błąd podczas testowania Claude</span>');
                        btn.prop('disabled', false).text('Testuj połączenie Claude');
                    }
                });
            });

            // Save settings
            $('#settings-form').submit(function(e) {
                e.preventDefault();
                
                var btn = $('#save-btn');
                var status = $('#status-message');
                
                btn.prop('disabled', true).text('Zapisywanie...');
                status.html('<p class="text-blue-600">Zapisywanie ustawień...</p>');
                
                var formData = $(this).serialize();
                
                $.ajax({
                    url: '/blog/automation/settings',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            status.html('<p class="text-green-600">✓ Ustawienia zostały zapisane</p>');
                            setTimeout(function() {
                                status.html('');
                            }, 3000);
                        } else {
                            status.html('<p class="text-red-600">✗ Błąd: ' + response.message + '</p>');
                        }
                        btn.prop('disabled', false).text('Zapisz ustawienia');
                    },
                    error: function() {
                        status.html('<p class="text-red-600">✗ Wystąpił błąd podczas zapisywania</p>');
                        btn.prop('disabled', false).text('Zapisz ustawienia');
                    }
                });
            });

            // Reset to defaults
            $('#reset-btn').click(function() {
                if (confirm('Czy na pewno chcesz przywrócić domyślne ustawienia?')) {
                    $('#generation_frequency').val('daily');
                    $('#auto_publish').prop('checked', false);
                    $('#min_article_length').val(800);
                    $('#max_article_length').val(1200);
                    $('#openai_model').val('gpt-4');
                    $('#temperature').val(0.7);
                    $('#max_tokens').val(2000);
                    $('#default_meta_title').val('Kaszflow - ');
                    $('#default_meta_description').val(' | Porównywarka produktów finansowych');
                    
                    // Reset custom fields
                    $('#custom_topics').val('');
                    $('#custom_keywords').val('');
                    $('#affiliate_links').val('');
                    $('#topic_blacklist').val('');
                    
                    // Reset checkboxes
                    $('input[name="target_categories[]"]').prop('checked', false);
                    $('#target_categories_kredyty').prop('checked', true);
                    $('#target_categories_bankowość').prop('checked', true);
                    $('#target_categories_oszczędzanie').prop('checked', true);
                    
                    $('#temperature-value').text('0.7');
                }
            });
        });
    </script>
</body>
</html> 
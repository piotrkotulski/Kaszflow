<?php

use Kaszflow\Controllers\HomeController;
use Kaszflow\Controllers\ComparisonController;
use Kaszflow\Controllers\BlogAutomationController;
use Kaszflow\Controllers\CacheController;

// Strona główna
$router->get('/', [HomeController::class, 'home']);

// Strona "O nas"
$router->get('/o-nas', [HomeController::class, 'about']);

// Porównywarki produktów finansowych
$router->get('/kredyty-gotowkowe', [ComparisonController::class, 'loans']);
$router->get('/kredyty-hipoteczne', [ComparisonController::class, 'mortgages']);
$router->get('/konta-osobiste', [ComparisonController::class, 'accounts']);
$router->get('/lokaty-bankowe', [ComparisonController::class, 'deposits']);
$router->get('/lokaty', [ComparisonController::class, 'deposits']); // Alias dla /lokaty

// Blog routes
$router->get('/blog', [HomeController::class, 'blog']);
$router->get('/blog/category/{category}', [HomeController::class, 'blogByCategory']);
$router->get('/blog/{slug}', [HomeController::class, 'blogPost']);

// Blog automation routes
$router->get('/blog/automation/admin', [BlogAutomationController::class, 'adminPanel']);
$router->get('/blog/automation/settings', [BlogAutomationController::class, 'settingsPage']);
$router->post('/blog/automation/settings', [BlogAutomationController::class, 'saveSettings']);
$router->get('/blog/automation/articles', [BlogAutomationController::class, 'listArticles']);
$router->post('/blog/automation/set-api-key', [BlogAutomationController::class, 'setApiKey']);
$router->post('/blog/automation/generate', [BlogAutomationController::class, 'generateArticle']);
$router->post('/blog/automation/schedule', [BlogAutomationController::class, 'scheduleGeneration']);
$router->post('/blog/automation/publish', [BlogAutomationController::class, 'publishDraft']);
$router->post('/blog/automation/edit', [BlogAutomationController::class, 'editArticle']);
$router->post('/blog/automation/delete', [BlogAutomationController::class, 'deleteArticle']);
$router->post('/blog/automation/test-api', [BlogAutomationController::class, 'testApiConnection']); 
$router->post('/blog/automation/test-claude', [BlogAutomationController::class, 'testClaudeAPI']);
$router->get('/blog/automation/suggestions', [BlogAutomationController::class, 'getSuggestions']);
$router->post('/blog/automation/generate-from-suggestion', [BlogAutomationController::class, 'generateFromSuggestion']);
$router->post('/blog/automation/regenerate-images', [BlogAutomationController::class, 'regenerateImages']);

// Cache management routes
$router->get('/cache/admin', [CacheController::class, 'admin']);
$router->get('/cache/refresh', [CacheController::class, 'refresh']);
$router->get('/cache/clean', [CacheController::class, 'clean']); 
<?php

use Kaszflow\Controllers\HomeController;
use Kaszflow\Controllers\ComparisonController;
use Kaszflow\Controllers\BlogController;
use Kaszflow\Controllers\ApiController;

// Strona główna
$router->get('/', [HomeController::class, 'index']);

// Strony statyczne
$router->get('/o-nas', [HomeController::class, 'about']);
$router->get('/kontakt', [HomeController::class, 'contact']);
$router->get('/polityka-prywatnosci', [HomeController::class, 'privacy']);
$router->get('/regulamin', [HomeController::class, 'terms']);

// Porównywarki produktów
$router->get('/kredyty-gotowkowe', [ComparisonController::class, 'loans']);
$router->get('/kredyty-hipoteczne', [ComparisonController::class, 'mortgages']);
$router->get('/konta-osobiste', [ComparisonController::class, 'accounts']);
$router->get('/konta-firmowe', [ComparisonController::class, 'businessAccounts']);
$router->get('/lokaty', [ComparisonController::class, 'deposits']);

// Szczegóły produktu
$router->get('/produkt', [ComparisonController::class, 'productDetails']);

// Blog
$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

// Newsletter
$router->post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter']);

// Sitemap
$router->get('/sitemap.xml', [HomeController::class, 'sitemap']); 
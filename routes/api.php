<?php

use Kaszflow\Controllers\ApiController;
use Kaszflow\Controllers\BlogController;

// API Produktów finansowych
$router->get('/api/products/loans', [ApiController::class, 'getLoans']);
$router->get('/api/products/mortgages', [ApiController::class, 'getMortgages']);
$router->get('/api/products/accounts', [ApiController::class, 'getAccounts']);
$router->get('/api/products/savings', [ApiController::class, 'getSavings']);
$router->get('/api/products/deposits', [ApiController::class, 'getDeposits']);
$router->get('/api/products/{id}', [ApiController::class, 'getProductDetails']);

// API Blog
$router->get('/api/blog/posts', [BlogController::class, 'getPosts']);
$router->post('/api/blog/generate', [BlogController::class, 'generatePost']);

// API Analytics
$router->post('/api/analytics/track', [ApiController::class, 'trackEvent']);
$router->get('/api/analytics/stats', [ApiController::class, 'getStats']);

// API Newsletter
$router->post('/api/newsletter/subscribe', [ApiController::class, 'subscribeNewsletter']);
$router->post('/api/newsletter/unsubscribe', [ApiController::class, 'unsubscribeNewsletter']);

// API Personalizacji
$router->post('/api/personalization/preferences', [ApiController::class, 'savePreferences']);
$router->get('/api/personalization/recommendations', [ApiController::class, 'getRecommendations']); 
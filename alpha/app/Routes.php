<?php
use App\Core\Router;
use App\Controller\SiteController;
use App\Controller\ErrorController;
use App\Controller\GetFileUserController;
use App\Controller\GetPublicFileController;
use App\Controller\TestDbController;
use App\Controller\AuthController;
use App\Controller\HubController;

Router::get('/login', [AuthController::class, 'showLoginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);
Router::get('/register', [AuthController::class, 'showRegisterForm']);
Router::post('/register', [AuthController::class, 'register']);
Router::post('/register/send-otp', [AuthController::class, 'sendOtp']);
Router::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Router::get('/password/reset/{token}', [AuthController::class, 'showResetForm']);
Router::post('/password/reset', [AuthController::class, 'resetPassword']);

Router::get('/hub', [HubController::class, 'index']);
Router::get('/hub/login', [HubController::class, 'showLoginForm']);
Router::post('/hub/login', [HubController::class, 'login']);
Router::get('/hub/logout', [HubController::class, 'logout']);
Router::get('/hub/{page}', [HubController::class, 'showPage']);

Router::get('/',            [SiteController::class, 'index']);
Router::get('/index',       [SiteController::class, 'index']);
Router::get('/admin',       [SiteController::class, 'admin']);
Router::get('/admin/{page}',[SiteController::class, 'admin']);

Router::get('/db-guide',[SiteController::class, 'dbGuide']);

Router::get('/404',         [ErrorController::class, 'notFound']);
Router::get('/static/{folder}/{file}', [GetFileUserController::class, 'file']);
Router::get('/static/{folder}/{subfolder}/{file}', [GetFileUserController::class, 'file']);
Router::get('/static/{folder}/{subfolder}/{subsubfolder}/{file}', [GetFileUserController::class, 'file']);

Router::get('/public/{folder}/{file}', [GetPublicFileController::class, 'file']);
Router::get('/public/{folder}/{subfolder}/{file}', [GetPublicFileController::class, 'file']);
Router::get('/public/{folder}/{subfolder}/{subsubfolder}/{file}', [GetPublicFileController::class, 'file']);

Router::get('/api',            [ErrorController::class, 'notFound']);

Router::get('/test-db', [TestDbController::class, 'index']);
Router::post('/test-db/insert', [TestDbController::class, 'processInsert']);
Router::post('/test-db/update', [TestDbController::class, 'processUpdate']);
Router::post('/test-db/delete', [TestDbController::class, 'processDelete']);
Router::post('/test-db/select', [TestDbController::class, 'processSelect']);
Router::post('/test-db/find-last', [TestDbController::class, 'processFindLast']);
Router::post('/test-db/raw-query', [TestDbController::class, 'processRawQuery']);
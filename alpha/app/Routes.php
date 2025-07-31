<?php
use App\Core\Router;
use App\Controller\SiteController;
use App\Controller\ErrorController;
use App\Controller\GetFileUserController;
use App\Controller\GetPublicFileController;
use App\Controller\TestDbController;

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

Router::get('/test-db', [TestDbController::class, 'index']);
Router::post('/test-db/insert', [TestDbController::class, 'processInsert']);
Router::post('/test-db/update', [TestDbController::class, 'processUpdate']);
Router::post('/test-db/delete', [TestDbController::class, 'processDelete']);
Router::post('/test-db/select', [TestDbController::class, 'processSelect']);
Router::post('/test-db/find-last', [TestDbController::class, 'processFindLast']);
Router::post('/test-db/raw-query', [TestDbController::class, 'processRawQuery']);
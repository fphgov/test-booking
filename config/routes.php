<?php

declare(strict_types=1);

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

return static function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    $app->get('/app/api/ping', App\Handler\PingHandler::class, 'app.api.ping');

    if (getenv('NODE_ENV') === 'development') {
        $app->post('/app/api/registration', [
            App\Handler\Applicant\AddHandler::class
        ], 'app.api.applicant.add');
    } else {
        $app->post('/app/api/registration', [
            \Middlewares\Recaptcha::class,
            App\Handler\Applicant\AddHandler::class
        ], 'app.api.applicant.add');
    }

    $app->post('/app/api/cancellation', [
        App\Handler\Applicant\CancellationHandler::class
    ], 'app.api.applicant.cancellation');

    $app->get('/app/api/options', [
        App\Handler\Setting\GetHandler::class
    ], 'app.api.options.get');

    $app->get('/app/api/appointment/time/{id:\d+}', [
        App\Handler\Appointment\GetTimesHandler::class
    ], 'app.api.appointment.time.get');

    $app->get('/app/api/appointment/{id:\d+}/{date:\d{4}-\d{2}-\d{2}}', [
        App\Handler\Appointment\GetHandler::class
    ], 'app.api.appointment.get');

    $app->post('/app/api/appointment/reservation', [
        App\Handler\Appointment\ReservationHandler::class
    ], 'app.api.appointment.reservation');

    // Admin
    if (getenv('NODE_ENV') === 'development') {
        $app->post('/admin/api/login', [
            Jwt\Handler\TokenHandler::class,
        ], 'admin.api.login');
    } else {
        $app->post('/admin/api/login', [
            \Middlewares\Recaptcha::class,
            Jwt\Handler\TokenHandler::class,
        ], 'admin.api.login');
    }

    if (getenv('NODE_ENV') === 'development') {
        $app->get('/admin/api/cache/clear', [
            App\Handler\Tools\ClearCacheHandler::class
        ], 'admin.api.cache.clear');
    } else {
        $app->get('/admin/api/cache/clear', [
            Jwt\Handler\JwtAuthMiddleware::class,
            App\Middleware\UserMiddleware::class,
            \Mezzio\Authorization\AuthorizationMiddleware::class,
            App\Handler\Tools\ClearCacheHandler::class
        ], 'admin.api.cache.clear');
    }

    $app->get('/admin/api/dashboard', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Dashboard\GetHandler::class
    ], 'admin.api.dashboard.get');

    $app->post('/admin/api/dashboard', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Dashboard\ChangeHandler::class
    ], 'admin.api.dashboard.set');

    $app->get('/admin/api/informations', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Information\GetHandler::class
    ], 'admin.api.informations.get');

    $app->get('/admin/api/applicant/s/{search}', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\SearchHandler::class
    ], 'admin.api.applicant.search');

    $app->get('/admin/api/applicant/{id:\d+}', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\GetHandler::class
    ], 'admin.api.applicant.get');

    $app->post('/admin/api/applicant/{id:\d+}', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\PostHandler::class
    ], 'admin.api.applicant.post');

    $app->delete('/admin/api/applicant/{id:\d+}', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\DelHandler::class
    ], 'admin.api.applicant.del');

    $app->get('/admin/api/applicant/export', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\ExportHandler::class
    ], 'admin.api.applicant.export');

    $app->get('/admin/api/check/s/{search}', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\CheckSearchHandler::class
    ], 'admin.api.check.search');

    $app->get('/admin/api/check/{humanId:[\w]{2}-[\d]{5}}', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\CheckGetHandler::class
    ], 'admin.api.check.get');

    $app->post('/admin/api/check/{humanId:[\w]{2}-[\d]{5}}', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        \Mezzio\Authorization\AuthorizationMiddleware::class,
        App\Handler\Applicant\CheckPostHandler::class
    ], 'admin.api.check.post');

    $app->post('/admin/api/generate/appointment', [
        Jwt\Handler\JwtAuthMiddleware::class,
        App\Middleware\UserMiddleware::class,
        App\Handler\Appointment\GenerateHandler::class
    ], 'admin.api.generate.appointment');
};

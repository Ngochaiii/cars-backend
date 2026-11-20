<?php

use App\Http\Middleware\HandleRedirects;
use App\Http\Middleware\RememberVisit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sau Cloudflare, IP khách nằm trong X-Forwarded-For còn IP nối tới
        // Nginx là của Cloudflare. Không khai báo proxy tin cậy thì mọi lead
        // ghi chung một IP, và link sinh ra có thể tụt về http.
        $middleware->trustProxies(at: '*');

        // Bắt đường dẫn cũ trong bảng redirects. TOÀN CỤC chứ không phải nhóm
        // web: đường dẫn cũ thường không còn route nào nên nhóm web không kịp
        // chạy trước khi Laravel ném 404.
        $middleware->prepend(HandleRedirects::class);

        // Nhớ nguồn quảng cáo + các trang khách đã xem trong session, để lượt
        // bấm gọi/Zalo (ContactClickController) biết khách đến từ đâu.
        $middleware->web(append: [RememberVisit::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*')
                || $request->is('admin/media')
                || $request->expectsJson(),
        );
    })->create();

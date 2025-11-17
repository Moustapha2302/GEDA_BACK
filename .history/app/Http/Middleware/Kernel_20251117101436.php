protected $routeMiddleware = [
    // Middleware Laravel par défaut
    'auth'          => \App\Http\Middleware\Authenticate::class,
    'auth.basic'    => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'guest'         => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'verified'      => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

    // Sanctum
    'auth:sanctum'  => \Laravel\Sanctum\Http\Middleware\EnsureAuthenticated::class,

    // 🔥 Ton middleware personnalisé
    'service.s01'   => \App\Http\Middleware\EnsureServiceS01::class,
];

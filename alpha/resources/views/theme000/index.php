<?php partial('head'); use App\Core\Session; ?>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
</head>
<body class="text-black min-h-screen flex flex-col items-center justify-center bg-gray-50">

    <?php if (Session::has('user_name')): ?>
        <div class="max-w-xl mx-auto p-10 bg-white rounded-lg shadow-2xl text-center border border-black">
            <h1 class="text-4xl font-extrabold text-black tracking-wider">
                Benvenuto, <?= eq(Session::get('user_name')) ?>!
            </h1>
            <p class="mt-4 text-gray-800 text-lg">
                Sei loggato. Utilizza il pannello di controllo per gestire il tuo account.
            </p>
            <form action="/<?= eq(current_lang()) ?>/logout" method="get" class="mt-8">
                <?php csrf_field(); ?>
                <button type="submit"
                    class="w-full rounded-lg bg-black text-white py-3 font-semibold transition-all duration-300 hover:bg-gray-800">
                    <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="relative flex flex-col items-center justify-center min-h-screen pt-12 sm:pt-0">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-black">
                    <span class="text-black">Otix</span> Core
                </h1>
                <div class="mt-4 text-lg md:text-xl text-gray-800">
                    Micro-framework PHP per lo sviluppo web.
                </div>
            </div>

            <div class="mt-12 w-full max-w-6xl mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="/<?= eq(current_lang()) ?>/login"
                        class="group p-6 bg-white rounded-lg shadow-md border border-black hover:bg-black transition-all duration-300 transform hover:-translate-y-1">
                        <h3 class="text-xl font-semibold mb-2 transition-all duration-300 text-black group-hover:text-white">
                            <i class="fa-solid fa-right-to-bracket"></i> Login
                        </h3>
                        <p class="mt-2 text-gray-800 transition-all duration-300 group-hover:text-white">Accedi alla tua dashboard.</p>
                    </a>
                    <a href="/<?= eq(current_lang()) ?>/register"
                        class="group p-6 bg-white rounded-lg shadow-md border border-black hover:bg-black transition-all duration-300 transform hover:-translate-y-1">
                        <h3 class="text-xl font-semibold mb-2 transition-all duration-300 text-black group-hover:text-white">
                            <i class="fa-solid fa-user-plus"></i> Registrati
                        </h3>
                        <p class="mt-2 text-gray-800 transition-all duration-300 group-hover:text-white">Crea un nuovo account.</p>
                    </a>
                    <a href="/<?= eq(current_lang()) ?>/db-guide"
                        class="group p-6 bg-white rounded-lg shadow-md border border-black hover:bg-black transition-all duration-300 transform hover:-translate-y-1">
                        <h3 class="text-xl font-semibold mb-2 transition-all duration-300 text-black group-hover:text-white">
                            <i class="fa-solid fa-database"></i> Guida DB
                        </h3>
                        <p class="mt-2 text-gray-800 transition-all duration-300 group-hover:text-white">Impara a usare il database.</p>
                    </a>
                    <a href="/<?= eq(current_lang()) ?>/docs"
                        class="group p-6 bg-white rounded-lg shadow-md border border-black hover:bg-black transition-all duration-300 transform hover:-translate-y-1">
                        <h3 class="text-xl font-semibold mb-2 transition-all duration-300 text-black group-hover:text-white">
                            <i class="fa-solid fa-book"></i> Documentazione
                        </h3>
                        <p class="mt-2 text-gray-800 transition-all duration-300 group-hover:text-white">Scopri tutte le funzionalità.</p>
                    </a>
                </div>

                <div class="flex justify-center mt-12 text-sm text-gray-800">
                    Otix Core v.0.1.1
                    <span class="mx-4 text-black">|</span>
                    Creato da <a href="https://otix.it" target="_blank" class="text-black ms-1" style="color:#E6811F">Otix</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
<?php partial('footer'); ?>
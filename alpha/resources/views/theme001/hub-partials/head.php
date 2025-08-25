<!DOCTYPE html>
<html lang="<?= eq(current_lang()) ?>" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hub Docs - <?= eq($_ENV['APP_NAME'])?></title>
    <link rel="stylesheet" href="/public/assets/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            overflow-y: auto;
        }

        main {
            padding-left: 280px;
        }
        .nav-link {
            font-size: 0.9rem;
        }
        section {
            padding-top: 56px;
            margin-top: -56px;
        }
        code {
            background-color: rgba(255,255,255,0.1);
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.9em;
        }
        pre > code {
            display: block;
            padding: 1rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>
<body>
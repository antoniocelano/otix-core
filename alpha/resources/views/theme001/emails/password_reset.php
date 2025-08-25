<!DOCTYPE html>
<html>
<head>
    <title>Recupero Password</title>
</head>
<body>
    <h1>Recupero Password</h1>
    <p>Clicca sul seguente link per resettare la tua password:</p>
    <p><a href="http://<?= $_SERVER['HTTP_HOST'] ?>/password/reset/<?= eq($token) ?>">Reset Password</a></p>
</body>
</html>
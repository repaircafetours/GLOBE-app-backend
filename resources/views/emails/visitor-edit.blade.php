<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <p>Bonjour {{$visitor->surname}} {{$visitor->name}}</p>

    <p>Vous pouvez modifier vos informations en cliquant sur le lien ci-dessous :</p>

    <p>
        <a href="{{ $url }}">Modifier mes informations</a>
    </p>

    <p>Ce lien est valable 60 minutes.</p>

    <p>L'équipe du Repair Café Tours</p>
</body>
</html>

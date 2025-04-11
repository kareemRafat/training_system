<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Document</title>
    <style>
        body {
            font-family:readexpro;
            direction: rtl;
            text-align: right;
            unicode-bidi: embed;
        }
        .arabic {
            font-family: readexpro;
            letter-spacing: 0 !important;
            word-spacing: normal;
        }
    </style>
</head>
<body>
    <p>{{ $trainingGroup }}</p>
    <ul>
        @foreach ($students as $student )
            <li>{{ $student['name'] }}</li>
        @endforeach
    </ul>
</body>
</html>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الطلاب</title>
    <style>
        body {
            /* font-family: 'Readex Pro', sans-serif; */
            margin: 0;
            padding: 0;
        }
        header {
            color: black;
            text-align: center;
            text-transform: capitalize
        }

        header h4 {
            font-size : 30px;
            letter-spacing :1px;
            margin: 25px
        }

        .container {
            max-width: 800px;
            margin: 10px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
        .overflow-x-auto {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: right;
            padding: 11px 8px;
            border: 1px solid #8e8e8e;
        }
        th {
            background-color: #f4f4f4;
        }
        .col-signature {
            width: 35%; /* Wider signature column */
            min-width: 200px; /* Minimum width */
        }
    </style>
</head>
<body>
    <header>
        <h4>{{ $trainingGroup -> name }}</h4>
    </header>

    <div class="container">
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-right" dir="rtl">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border-b border-gray-300">#</th>
                        <th class="px-4 py-2 border-b border-gray-300">الاسم</th>
                        <th class="px-4 py-2 border-b border-gray-300" style="text-align: center">التوقيع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $student['name'] }}</td>
                        <td class="px-4 py-2 border-b border-gray-300 col-signature"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

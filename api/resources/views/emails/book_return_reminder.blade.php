<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Return Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333333;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        h1 {
            color: #4CAF50;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        ul li {
            background-color: #f9f9f9;
            border: 1px solid #dddddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        ul li:nth-child(odd) {
            background-color: #e9e9e9;
        }

        .due-date {
            font-weight: bold;
            color: #E74C3C;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Book Return Reminder</h1>
        <p>Dear {{ $user->name }},</p>
        <p>This is a reminder to return the following books:</p>
        <ul>
            @foreach ($bookTitles as $index => $bookTitle)
                <li>{{ $bookTitle }} <br>- <span class="due-date">Due Date: {{ $dueDates[$index] }}</span></li>
            @endforeach
        </ul>
        <p>Please return them by the due date to avoid any overdue charges.</p>
        <p>Thank you!</p>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Kiai soft Library.</p>
        </div>
    </div>
</body>

</html>

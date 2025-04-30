<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome Email</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #4f46e5;
            color: white;
            text-align: center;
            padding: 30px 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
        }

        .content {
            padding: 30px 20px;
            color: #333333;
        }

        .content h2 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            font-size: 13px;
            color: #999999;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Our App, {{ $user->name }}!</h1>
        </div>

        <div class="content">
            <h2>Hi {{ $user->name }},</h2>
            <p>
                We're excited to have you join our community. You’ve taken the first step into something great!
            </p>
            <p>
                Explore features, connect with others, and get started on your journey with us.
            </p>
            <p style="text-align: center;">
                <a href="{{ url('https://rohitgupta.great-site.net') }}" class="button">Go to Dashboard</a>
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Our App. All rights reserved.
        </div>
    </div>
</body>

</html>
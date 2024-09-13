<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
            text-align: center;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Hello,
        </div>
        <div class="content">
            <p class="text-justify">Two-Factor Authentication Code</p>
            <p>Your verification code is: <strong>{{ $token }}</strong></p>
        </div>
        <div class="footer"> 
            <p>Contact us:  @lang('email.contactUs') </p>
            <p>Home DeliveryBR Team</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Contact Message</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-top: 5px solid #007bff;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #007bff;
            font-size: 24px;
        }

        .info-group {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 16px;
            color: #222;
        }

        .message-box {
            margin-top: 25px;
            padding: 20px;
            background: #fff;
            border-left: 4px solid #007bff;
            border-radius: 4px;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Us Message</h2>
            <p>You have received a new message through your website contact form.</p>
        </div>

        <div class="info-group">
            <span class="info-label">Name</span>
            <span class="info-value">{{ $contact->name }}</span>
        </div>

        <div class="info-group">
            <span class="info-label">Email Address</span>
            <span class="info-value">{{ $contact->email }}</span>
        </div>

        <div class="info-group">
            <span class="info-label">Phone Number</span>
            <span class="info-value">{{ $contact->number }}</span>
        </div>

        <div class="info-group">
            <span class="info-label">Message Details</span>
            <div class="message-box">
                {{ $contact->message }}
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #4A90E2; text-align: center;">Reset Password Notification</h2>
        <p>Hello,</p>
        <p>You are receiving this email because we received a password reset request for your customer account.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #4A90E2; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                Reset Password
            </a>
        </div>
        <p>This password reset link will expire in {{ $count }} minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 13px; color: #666;">
            If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
            <br>
            <a href="{{ $url }}" style="color: #4A90E2; word-break: break-all; text-decoration: underline;">{{ $url }}</a>
        </p>
        
        <p style="font-size: 12px; color: #888; text-align: center; margin-top: 20px;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>

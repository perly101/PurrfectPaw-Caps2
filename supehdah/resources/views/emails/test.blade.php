<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .content { padding: 20px 0; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $title }}</h2>
        </div>
        
        <div class="content">
            <p>{{ $content }}</p>
            <p>This is a test email to verify that your email configuration is working properly.</p>
        </div>
        
        <div class="footer">
            <p>This email was sent from the test script at {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
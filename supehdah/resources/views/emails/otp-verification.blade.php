<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 25px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            background-color: #f5f5f5;
            padding: 15px 30px;
            border-radius: 8px;
            display: inline-block;
            border: 1px solid #e0e0e0;
        }
        .instructions {
            margin: 25px 0;
            line-height: 1.8;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #555;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Verify Your Email Address</h2>
        </div>
        
        <p>Hello {{ $user->first_name }} {{ $user->last_name }},</p>
        
        <div class="instructions">
            <p>Thank you for registering with PurrfectPaw. To ensure the security of your account and complete your registration, please enter the verification code below on the verification page:</p>
        </div>
        
        <div class="otp-container">
            <div class="otp-code">{{ $otp }}</div>
        </div>
        
        <div class="instructions">
            <p>This verification code will expire in 30 minutes for security purposes.</p>
            <p>If you did not create an account with PurrfectPaw, you can safely ignore this email.</p>
        </div>
        
        <div class="footer">
            <p>If you're having trouble with the verification process, please contact our support team at support@PurrfectPaw.com</p>
            <div class="social-links">
                <a href="#">Facebook</a> | <a href="#">Twitter</a> | <a href="#">Instagram</a>
            </div>
            <p>&copy; {{ date('Y') }} PurrfectPaw. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
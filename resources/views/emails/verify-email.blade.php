<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email</title>
    <style>
        /* Add your custom styles here */
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 150px; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 8px; }
        .button { display: inline-block; padding: 12px 24px; background: #007bff; 
                 color: white; text-decoration: none; border-radius: 4px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Logo -->
        <div class="header">
            @if(isset($logo) && $logo)
                <img src="{{ $logo }}" alt="Your App Logo" class="logo">
            @else
                <h1 style="color: #007bff;">{{ config('app.name', 'Laravel') }}</h1>
            @endif
        </div>

        <!-- Email Content -->
        <div class="content">
            <h2>Verify Your Email Address</h2>
            
            <p>Hello {{ $user->name ?? 'User' }},</p>
            
            <p>Thank you for registering with {{ config('app.name', 'our application') }}. 
               Please click the button below to verify your email address and activate your account.</p>

            <!-- Verification Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button">
                    Verify Email Address
                </a>  
            </div>

            <p>If you're having trouble clicking the "Verify Email Address" button, 
               copy and paste the URL below into your web browser:</p>
            
            <p style="word-break: break-all; background: #eee; padding: 10px; border-radius: 4px;">
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </p>

            <p>If you did not create an account, no further action is required.</p>
            
            <p>Best regards,<br>The {{ config('app.name', 'Laravel') }} Team</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
            
            @if(isset($unsubscribeUrl) && $unsubscribeUrl)
                <p>
                    <a href="{{ $unsubscribeUrl }}" style="color: #666; text-decoration: none;">
                        Unsubscribe from these emails
                    </a>
                </p>
            @endif
        </div>
    </div>
</body>
</html>
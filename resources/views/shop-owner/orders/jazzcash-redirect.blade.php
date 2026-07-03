<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to JazzCash Payment Gateway...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            color: #333;
            margin: 0 0 10px;
            font-size: 24px;
        }

        p {
            color: #666;
            margin: 0 0 20px;
            line-height: 1.6;
        }

        .jazzcash-logo {
            margin: 20px 0;
            font-size: 14px;
            color: #999;
        }

        noscript {
            color: #d32f2f;
            background: #ffebee;
            padding: 15px;
            border-radius: 8px;
            display: block;
            margin-bottom: 20px;
        }

        form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Processing Payment</h1>
        <p>Redirecting you to JazzCash secure payment gateway...</p>
        <p style="font-size: 14px; color: #999;">Please do not close this window.</p>
        <div class="jazzcash-logo">🏦 Secure Payment by JazzCash</div>

        <noscript>
            <strong>JavaScript is disabled!</strong> Please enable JavaScript or click the button below to continue to payment.
            <button type="submit" style="margin-top: 10px; padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px;">
                Continue to Payment
            </button>
        </noscript>
    </div>

    <!-- JazzCash redirect form -->
    <form id="jazzcash_form" name="jazzcash_form" method="POST" action="{{ config('jazzcash.endpoint_ma') }}">
        @foreach($payload as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        // Auto-submit the form when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.forms['jazzcash_form'].submit();
        });
    </script>
</body>
</html>

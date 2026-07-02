<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Testing JazzCash Locally

To test the end-to-end JazzCash Mobile Account integration locally (including receiving webhook callbacks from the JazzCash sandbox sandbox server):

### 1. Start a secure tunnel with Ngrok
Since the sandbox webhook callback (IPN) must call a public endpoint, run `ngrok` to expose your local Laravel environment (assumed running on port 8000):
```bash
ngrok http --url=<your-custom-ngrok-domain> 8000
```
Update your local `.env` configuration file to match the secure HTTPS url:
```
APP_URL=https://<your-custom-ngrok-domain>
```

### 2. Configure Return URL/IPN in sandbox panel
1. Log in to the [JazzCash Sandbox Merchant Portal](https://sandbox.jazzcash.com.pk/).
2. Navigate to Merchant Settings / Integration Credentials.
3. Set the **IPN/Return URL** to:
   ```
   https://<your-custom-ngrok-domain>/jazzcash/callback
   ```

### 3. Seed a Test Manufacturer with Sandbox Credentials
Use the following `tinker` command to configure a manufacturer account with your JazzCash Sandbox keys:
```bash
php artisan tinker
```
Then run the snippet inside tinker:
```php
$manufacturer = App\Models\User::where('role', 'manufacturer')->first();
$manufacturer->update([
    'jazzcash_mobile' => '03001234567',
    'jazzcash_account_title' => 'Sandbox Merchant Account',
    'jazzcash_merchant_id' => 'MC825731', // Your Sandbox Merchant ID
    'jazzcash_password' => 'your_sandbox_password', // Encrypted automatically
    'jazzcash_integrity_salt' => 'your_sandbox_salt', // Encrypted automatically
]);
```

### 4. Manual Test Flow
1. **Login as Shop Owner**: Navigate to `https://<your-custom-ngrok-domain>/login` and sign in.
2. **Checkout Order**: Navigate to an existing order that has a balance due and click **Pay Now with JazzCash**.
3. **Trigger Prompt**: Enter your shop owner mobile wallet account and checkout.
4. **Approve Transaction**: Check your phone / JazzCash app for the test MPIN popup to authorize.
5. **Inspect Hook Logs**: Monitor ngrok logs to confirm that the `POST /jazzcash/callback` request succeeds.
6. **Verify Complete**: Verify the database `payments` table status changes to `completed` and the order's `paid_amount` increments successfully.


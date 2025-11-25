<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Web Yaka - E-Commerce Platform

Web Yaka adalah platform e-commerce yang dibangun dengan Laravel 12 dan dilengkapi dengan integrasi payment gateway **Midtrans**.

## Fitur Utama

- 🛒 Manajemen produk dan kategori
- 🛍️ Sistem keranjang belanja
- 📦 Sistem order dan tracking
- 💳 Payment integration dengan Midtrans
- 👤 User authentication dan profile management
- ⭐ Review dan rating produk
- 👨‍💼 Admin dashboard
- 📱 Responsive design

## Integrasi Midtrans

Aplikasi ini sudah dilengkapi dengan integrasi payment gateway **Midtrans**. 

### Setup Cepat

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   php artisan seed
   ```

4. **Configure Midtrans** di `.env`:
   ```
   MIDTRANS_SERVER_KEY=your_server_key
   MIDTRANS_CLIENT_KEY=your_client_key
   MIDTRANS_IS_PRODUCTION=false
   ```

5. **Jalankan Server**
   ```bash
   php artisan serve
   npm run dev
   ```

### Dokumentasi Midtrans

- 📖 [Setup & Configuration](./MIDTRANS_SETUP.md)
- 🧪 [Testing Guide](./TESTING_PAYMENT.md)
- 📋 [API Documentation](./API_DOCUMENTATION.md)
- 💻 [View Snippet](./PAYMENT_VIEW_SNIPPET.md)

### Fitur Payment

✅ Create payment snap token
✅ Handle payment callbacks
✅ Process webhook notifications
✅ Update order status automatically
✅ Support multiple payment methods
✅ Fraud detection
✅ Transaction logging

## File Structure

```
app/
├── Services/
│   └── MidtransService.php          # Service untuk Midtrans API
├── Models/
│   ├── Payment.php                   # Model payment
│   ├── Order.php                     # Model order
│   └── ...
├── Http/
│   ├── Controllers/
│   │   ├── PaymentController.php      # Payment handling
│   │   └── ...
│   └── Middleware/
└── Providers/
    └── AppServiceProvider.php        # Service registration

config/
└── midtrans.php                      # Midtrans configuration

database/
├── migrations/
│   └── create_payments_table.php      # Payment table migration
└── seeders/

resources/
├── views/
│   ├── orders/
│   │   └── payment.blade.php         # Payment view
│   └── ...

routes/
└── web.php                           # Routes (includes payment routes)
```

## Technology Stack

- **Framework**: Laravel 12
- **Database**: MySQL
- **Frontend**: Tailwind CSS, Alpine.js
- **Payment**: Midtrans Snap API
- **Build**: Vite

## Development

### Running Tests
```bash
php artisan test
```

### Database Migrations
```bash
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
```

### Clearing Cache
```bash
php artisan cache:clear
php artisan config:clear
```

## API Endpoints

### Payment Endpoints
- `POST /payment/create/{order}` - Create payment snap
- `GET /payment/status/{order}` - Check payment status
- `POST /midtrans/notification` - Webhook handler

Lihat [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) untuk detail lengkap.

## Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL 5.7+
- Midtrans account (https://midtrans.com)

## Installation

1. Clone repository
   ```bash
   git clone https://github.com/Juli-arch7/Web-Yaka.git
   cd Web-Yaka
   ```

2. Install composer dependencies
   ```bash
   composer install
   ```

3. Install npm dependencies
   ```bash
   npm install
   ```

4. Copy environment file
   ```bash
   cp .env.example .env
   ```

5. Generate application key
   ```bash
   php artisan key:generate
   ```

6. Configure database di `.env`

7. Run migrations
   ```bash
   php artisan migrate
   ```

8. Build assets
   ```bash
   npm run build
   ```

9. Start development server
   ```bash
   php artisan serve
   ```

Aplikasi akan berjalan di `http://localhost:8000`

## Troubleshooting

Lihat bagian Troubleshooting di [MIDTRANS_SETUP.md](./MIDTRANS_SETUP.md).

## Contributing

Silakan buat pull request atau laporkan issues.

## License

MIT License - lihat LICENSE file.

## Support

- 📧 Email: support@webyaka.com
- 💬 Telegram: @webyaka
- 🌐 Website: https://webyaka.com

---

**Dibuat dengan ❤️ untuk memudahkan penjualan online di Indonesia**

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

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

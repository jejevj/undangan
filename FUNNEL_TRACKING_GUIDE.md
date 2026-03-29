# Funnel Tracking System - Panduan Penggunaan

## Deskripsi
Sistem pelacakan funnel kustom untuk menganalisis konversi pengguna tanpa menggunakan Google Analytics 4 (GA4). Sistem ini melacak perjalanan pengguna melalui berbagai tahapan (funnel) dan menyediakan laporan visual untuk analisis.

## Fitur Utama

### 1. Event Tracking
- Pelacakan otomatis untuk setiap langkah dalam funnel
- **Mendukung anonymous users (pengunjung yang belum login)**
- Menyimpan data event ke database lokal
- Mendukung data tambahan (metadata) untuk setiap event
- Session-based tracking untuk melacak perjalanan user

### 2. Funnel Analysis
- Analisis konversi subscription (dari view plans hingga payment completed)
- Analisis konversi invitation (dari view templates hingga publish)
- **Analisis konversi registration (dari view register hingga success) - untuk anonymous users**
- Identifikasi titik dropoff terbesar
- Perhitungan conversion rate antar tahapan

### 3. Visual Reports
- Dashboard admin dengan grafik interaktif
- Filter berdasarkan rentang tanggal
- Visualisasi funnel dengan bar chart
- Highlight dropoff points
- **3 funnel utama: Subscription, Invitation, dan Registration**

## Struktur Database

### Tabel: user_events
```sql
- id: bigint (primary key)
- user_id: bigint (nullable, foreign key ke users)
- session_id: string (untuk tracking anonymous users)
- event_name: string (nama event, contoh: view_plans, select_plan)
- event_category: string (kategori, contoh: subscription_funnel, invitation_funnel)
- event_label: string (label deskriptif)
- event_data: json (data tambahan)
- page_url: text (URL halaman saat event terjadi)
- referrer: text (URL referrer)
- user_agent: text (browser user agent)
- ip_address: string (IP address user)
- created_at: timestamp
```

## Funnel Steps

### Subscription Funnel (Authenticated Users)
1. **view_plans** - User melihat halaman pricing plans
2. **select_plan** - User memilih paket tertentu
3. **view_checkout** - User membuka halaman checkout
4. **select_payment** - User memilih metode pembayaran (VA/E-Wallet/QRIS)
5. **payment_initiated** - User berhasil membuat pembayaran
6. **payment_completed** - Pembayaran berhasil dikonfirmasi

### Invitation Funnel (Authenticated Users)
1. **view_templates** - User melihat daftar template
2. **select_template** - User memilih template
3. **start_create** - User mulai membuat undangan
4. **fill_details** - User mengisi detail undangan
5. **preview** - User preview undangan
6. **publish** - User mempublikasikan undangan

### Registration Funnel (Anonymous Users) ⭐ NEW
1. **view_register** - Pengunjung membuka halaman registrasi
2. **submit_register** - Pengunjung submit form registrasi
3. **register_success** - Registrasi berhasil dan user terdaftar

### Landing Page Tracking (Anonymous Users) ⭐ NEW
- **page_view** - Pengunjung membuka landing page
- **browse_templates** - Pengunjung melihat-lihat template di landing page

### Authentication Tracking
- **view_login** - User membuka halaman login
- **submit_login** - User submit form login
- **login_success** - Login berhasil
- **login_failed** - Login gagal
- **logout** - User logout

## Cara Menggunakan

### 1. Tracking Event di Controller

```php
use App\Services\EventTrackingService;

// Track subscription funnel
EventTrackingService::subscriptionFunnel('view_plans');

// Track dengan data tambahan
EventTrackingService::subscriptionFunnel('select_plan', [
    'plan_id' => $plan->id,
    'plan_name' => $plan->name,
]);

// Track invitation funnel
EventTrackingService::invitationFunnel('view_templates');

// Track generic event
EventTrackingService::track('custom_event', 'custom_category', 'Custom Label', [
    'key' => 'value',
]);

// Track page view
EventTrackingService::pageView('Dashboard', ['section' => 'admin']);
```

### 2. Mengakses Laporan Funnel

1. Login sebagai admin
2. Buka menu "Laporan" → "Funnel Analysis"
3. URL: `/dash/admin/funnel-report`
4. Filter berdasarkan tanggal untuk melihat data periode tertentu

### 3. Analisis Programatik

```php
use App\Services\FunnelAnalysisService;

// Analisis subscription funnel
$subscriptionData = FunnelAnalysisService::subscriptionFunnel(
    now()->subDays(30), // start date
    now()               // end date
);

// Analisis invitation funnel
$invitationData = FunnelAnalysisService::invitationFunnel(
    now()->subDays(30),
    now()
);

// Hitung conversion rate antara 2 step
$conversionRate = FunnelAnalysisService::conversionRate(
    'subscription_funnel',
    'view_plans',
    'payment_completed',
    now()->subDays(30),
    now()
);

// Dapatkan top dropoff points
$dropoffs = FunnelAnalysisService::topDropoffPoints(
    'subscription_funnel',
    now()->subDays(30),
    now()
);

// Dapatkan user journey untuk session tertentu
$journey = FunnelAnalysisService::getUserJourney($sessionId);
```

## Implementasi Saat Ini

### Landing Page (Anonymous Users) ✅ Terintegrasi
- ✅ `LandingController::index()` - Track page_view
- ✅ `LandingController::getTemplates()` - Track browse_templates

### Authentication Flow ✅ Terintegrasi
- ✅ `LoginController::showLoginForm()` - Track view_login
- ✅ `LoginController::login()` - Track submit_login, login_success, login_failed
- ✅ `LoginController::logout()` - Track logout
- ✅ `RegisterController::showRegistrationForm()` - Track view_register
- ✅ `RegisterController::register()` - Track submit_register, register_success

### Subscription Flow ✅ Terintegrasi
- ✅ `SubscriptionController::index()` - Track view_plans
- ✅ `SubscriptionController::checkout()` - Track select_plan & view_checkout
- ✅ `SubscriptionController::createVirtualAccount()` - Track select_payment & payment_initiated
- ✅ `SubscriptionController::createEWalletPayment()` - Track select_payment & payment_initiated
- ✅ `SubscriptionController::createQrisPayment()` - Track select_payment & payment_initiated
- ✅ `DokuWebhookController::activateSubscription()` - Track payment_completed
- ✅ `PaymentStatusController::activateSubscription()` - Track payment_completed

### Invitation Flow (Belum Terintegrasi)
- ⏳ View templates - Perlu ditambahkan di InvitationController
- ⏳ Select template - Perlu ditambahkan di InvitationController
- ⏳ Start create - Perlu ditambahkan di InvitationController
- ⏳ Fill details - Perlu ditambahkan di InvitationController
- ⏳ Preview - Perlu ditambahkan di InvitationController
- ⏳ Publish - Perlu ditambahkan di InvitationController

## Menambahkan Tracking ke Invitation Flow

Contoh implementasi untuk invitation funnel:

```php
// Di InvitationController::selectTemplate()
EventTrackingService::invitationFunnel('view_templates');

// Di InvitationController::create() saat user pilih template
EventTrackingService::invitationFunnel('select_template', [
    'template_id' => $template->id,
    'template_name' => $template->name,
]);

// Di InvitationController::create() saat form ditampilkan
EventTrackingService::invitationFunnel('start_create', [
    'template_id' => $template->id,
]);

// Di InvitationController::store()
EventTrackingService::invitationFunnel('fill_details', [
    'invitation_id' => $invitation->id,
]);

// Di InvitationController::preview()
EventTrackingService::invitationFunnel('preview', [
    'invitation_id' => $invitation->id,
]);

// Di InvitationController::publish()
EventTrackingService::invitationFunnel('publish', [
    'invitation_id' => $invitation->id,
    'slug' => $invitation->slug,
]);
```

## Keuntungan Sistem Ini

1. **Privacy-First**: Data disimpan di server sendiri, tidak dikirim ke pihak ketiga
2. **Customizable**: Mudah menambahkan funnel atau event baru
3. **No External Dependencies**: Tidak perlu API key atau konfigurasi eksternal
4. **Real-time**: Data langsung tersedia tanpa delay
5. **Detailed**: Bisa menyimpan metadata lengkap untuk setiap event
6. **Cost-Free**: Tidak ada biaya tambahan untuk tracking
7. **Anonymous User Support**: Melacak pengunjung yang belum login menggunakan session ID
8. **Complete User Journey**: Melacak dari anonymous visitor → registration → subscription → invitation

## Maintenance

### Membersihkan Data Lama
```php
// Hapus event lebih dari 90 hari
UserEvent::where('created_at', '<', now()->subDays(90))->delete();
```

### Monitoring Performance
- Index pada kolom `event_category`, `event_name`, `created_at` untuk query cepat
- Pertimbangkan partitioning tabel jika data sangat besar
- Archive data lama ke tabel terpisah jika perlu

## Troubleshooting

### Event tidak tercatat
- Pastikan `EventTrackingService::track()` dipanggil di controller
- Cek log error di `storage/logs/laravel.log`
- Pastikan tabel `user_events` sudah di-migrate

### Laporan tidak muncul
- Pastikan ada data event di database
- Cek filter tanggal, pastikan mencakup periode yang ada datanya
- Pastikan user login sebagai admin

### Conversion rate 0%
- Pastikan semua step dalam funnel sudah di-track
- Cek apakah ada data untuk semua step di database
- Verifikasi session_id konsisten untuk user yang sama

## File Terkait

- `app/Services/EventTrackingService.php` - Service untuk tracking event
- `app/Services/FunnelAnalysisService.php` - Service untuk analisis funnel
- `app/Models/UserEvent.php` - Model untuk tabel user_events
- `app/Http/Controllers/Admin/FunnelReportController.php` - Controller untuk laporan
- `resources/views/admin/funnel-report.blade.php` - View laporan funnel
- `database/migrations/*_create_user_events_table.php` - Migration tabel
- `database/seeders/FunnelReportMenuSeeder.php` - Seeder menu

## Pengembangan Selanjutnya

1. **Email Notifications**: Kirim notifikasi jika dropoff rate tinggi
2. **A/B Testing**: Integrasikan dengan sistem A/B testing
3. **Cohort Analysis**: Analisis berdasarkan cohort user
4. **Retention Analysis**: Tracking user retention
5. **Custom Funnels**: UI untuk membuat funnel kustom tanpa coding
6. **Export Reports**: Export laporan ke PDF/Excel
7. **Real-time Dashboard**: Dashboard real-time dengan WebSocket
8. **Heatmap**: Visualisasi heatmap untuk dropoff points

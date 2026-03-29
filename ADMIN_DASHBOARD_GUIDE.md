# Admin Dashboard Guide

## Overview

Admin dashboard menyediakan visualisasi data dan statistik lengkap untuk monitoring sistem undangan digital.

## Access

- **URL**: `http://127.0.0.1:8000/dash`
- **Role Required**: `admin`
- **Regular users**: Akan melihat dashboard sederhana dengan data mereka sendiri

## Statistics Cards

### 1. Total Users
- **Metric**: Jumlah total user terdaftar
- **Sub-metric**: User baru bulan ini
- **Icon**: Users icon
- **Color**: Primary (Blue)

### 2. Subscription Aktif
- **Metric**: Jumlah subscription berbayar yang aktif
- **Sub-metric**: "Paket berbayar"
- **Icon**: Check circle
- **Color**: Success (Green)

### 3. Total Undangan
- **Metric**: Jumlah total undangan dibuat
- **Sub-metric**: Jumlah undangan published
- **Icon**: Envelope
- **Color**: Info (Cyan)

### 4. Total Revenue
- **Metric**: Total pendapatan dari subscription
- **Sub-metric**: Revenue bulan ini
- **Icon**: Money
- **Color**: Warning (Yellow)

## Charts & Visualizations

### 1. Revenue 6 Bulan Terakhir
- **Type**: Line Chart
- **Data**: Monthly revenue dari subscription
- **Period**: 6 bulan terakhir
- **Y-Axis**: Revenue dalam Rupiah
- **X-Axis**: Bulan (YYYY-MM format)
- **Purpose**: Melihat trend pendapatan

### 2. Metode Pembayaran
- **Type**: Doughnut Chart
- **Data**: Distribusi pembayaran berdasarkan metode
- **Categories**:
  - Virtual Account (Blue)
  - E-Wallet (Green)
  - QRIS (Red)
- **Purpose**: Melihat preferensi metode pembayaran

### 3. Pertumbuhan User
- **Type**: Bar Chart
- **Data**: Jumlah user baru per bulan
- **Period**: 6 bulan terakhir
- **Y-Axis**: Jumlah user
- **X-Axis**: Bulan
- **Purpose**: Melihat trend pertumbuhan user

### 4. Subscription per Paket
- **Type**: Pie Chart
- **Data**: Distribusi subscription berdasarkan pricing plan
- **Categories**: Free, Basic, Premium, Business, dll
- **Purpose**: Melihat paket mana yang paling populer

### 5. Penggunaan Channel VA
- **Type**: Horizontal Bar Chart
- **Data**: Jumlah transaksi per bank VA
- **Categories**: BRI, BNI, CIMB, Mandiri, Permata
- **Purpose**: Melihat bank mana yang paling sering digunakan

### 6. Subscription Terbaru
- **Type**: Table
- **Data**: 10 subscription terbaru yang sudah dibayar
- **Columns**:
  - User (name + email)
  - Paket (plan name)
  - Amount (Rupiah)
  - Tanggal (paid_at)
- **Purpose**: Monitoring transaksi terbaru

## Data Sources

### User Statistics
```php
- Total Users: User::count()
- New Users This Month: User::whereMonth('created_at', now()->month)
- Active Subscriptions: UserSubscription::where('status', 'active')
```

### Invitation Statistics
```php
- Total Invitations: Invitation::count()
- Published Invitations: Invitation::where('is_published', true)
- Invitations This Month: Invitation::whereMonth('created_at', now()->month)
```

### Revenue Statistics
```php
- Total Revenue: UserSubscription::where('status', 'active')->sum('amount')
- Revenue This Month: UserSubscription::whereMonth('paid_at', now()->month)->sum('amount')
```

### Payment Methods
```php
- VA: DokuVirtualAccount::where('status', 'paid')->count()
- E-Wallet: DokuEWalletPayment::where('status', 'success')->count()
- QRIS: DokuQrisPayment::where('status', 'paid')->count()
```

## Technologies Used

### Frontend
- **Chart.js 4.4.0**: JavaScript charting library
- **Bootstrap 5**: UI framework
- **Font Awesome**: Icons

### Backend
- **Laravel Eloquent**: Database queries
- **MySQL**: Database with aggregation functions
- **Carbon**: Date manipulation

## Customization

### Adding New Statistics Card

```php
// In DashboardController::adminDashboard()
$myNewStat = Model::where('condition', 'value')->count();

// Pass to view
return view('dashboard-admin', compact('myNewStat', ...));
```

```blade
{{-- In dashboard-admin.blade.php --}}
<div class="col-xl-3 col-lg-6 col-md-6">
    <div class="stat-card bg-danger text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-white mb-1">My New Stat</h6>
                <h3 class="text-white mb-0">{{ number_format($myNewStat) }}</h3>
            </div>
            <div class="stat-icon">
                <i class="fa fa-icon-name"></i>
            </div>
        </div>
    </div>
</div>
```

### Adding New Chart

```php
// In DashboardController::adminDashboard()
$myChartData = Model::select('field', DB::raw('COUNT(*) as total'))
    ->groupBy('field')
    ->get()
    ->pluck('total', 'field');

return view('dashboard-admin', compact('myChartData', ...));
```

```blade
{{-- In dashboard-admin.blade.php --}}
<div class="col-xl-6 col-lg-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">My Chart Title</h4>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const myChartCtx = document.getElementById('myChart').getContext('2d');
new Chart(myChartCtx, {
    type: 'bar', // or 'line', 'pie', 'doughnut'
    data: {
        labels: {!! json_encode($myChartData->keys()) !!},
        datasets: [{
            label: 'My Data',
            data: {!! json_encode($myChartData->values()) !!},
            backgroundColor: 'rgba(13, 110, 253, 0.8)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
```

## Performance Considerations

### Query Optimization
- All queries use indexes on `created_at`, `status`, `paid_at`
- Aggregation done at database level
- Limited to recent data (6 months for charts)

### Caching (Optional)
```php
// Cache dashboard data for 5 minutes
$totalUsers = Cache::remember('dashboard.total_users', 300, function() {
    return User::count();
});
```

### Pagination
- Recent subscriptions limited to 10 records
- Can be increased if needed

## Troubleshooting

### Issue: Charts not showing
- Check browser console for JavaScript errors
- Verify Chart.js CDN is loaded
- Ensure data is properly JSON encoded

### Issue: Wrong statistics
- Check database data
- Verify query conditions (status, dates)
- Check timezone settings

### Issue: Slow loading
- Add database indexes
- Implement caching
- Reduce chart data period

## Future Enhancements

### Possible Additions
1. **Date Range Filter**: Allow admin to select custom date range
2. **Export Reports**: Export data to PDF/Excel
3. **Real-time Updates**: WebSocket for live statistics
4. **Comparison**: Compare current vs previous period
5. **Drill-down**: Click chart to see detailed data
6. **Email Reports**: Scheduled email with statistics
7. **More Metrics**:
   - Average order value
   - Conversion rate
   - Churn rate
   - Customer lifetime value

### Example: Date Range Filter

```php
// Add to controller
$startDate = $request->input('start_date', now()->subMonths(6));
$endDate = $request->input('end_date', now());

$monthlyRevenue = UserSubscription::where('status', 'active')
    ->whereBetween('paid_at', [$startDate, $endDate])
    ->select(...)
    ->get();
```

## Related Files

- Controller: `app/Http/Controllers/DashboardController.php`
- View: `resources/views/dashboard-admin.blade.php`
- User View: `resources/views/dashboard.blade.php`
- Models: `app/Models/User.php`, `UserSubscription.php`, etc.

## Support

For dashboard-related issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Verify user has admin role
- Test queries in tinker
- Check Chart.js documentation: https://www.chartjs.org/docs/

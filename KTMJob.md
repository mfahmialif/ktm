# Dokumentasi Laravel Queue untuk KTM Generator

Panduan lengkap menjalankan Queue Jobs di berbagai hosting environment.

---

## 🚀 Quick Start

### 1. Konfigurasi `.env`

```env
QUEUE_CONNECTION=database
```

### 2. Jalankan Migration

```bash
php artisan queue:table
php artisan migrate
```

### 3. Test Lokal

```bash
php artisan queue:work
```

---

## 📦 Setup Hosting

### Niagahoster / cPanel Hosting

#### Langkah 1: Tambah Cron Job

1. Login ke **cPanel**
2. Buka **Cron Jobs**
3. Set frequency: **Once Per Minute** (`* * * * *`)
4. Command:
    ```
    cd /home/USERNAME/public_html && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
    ```
    > Ganti `USERNAME` dengan username cPanel Anda

#### Langkah 2: Tambah Schedule Command

Buat/edit file `routes/console.php`:

```php
<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --stop-when-empty --max-time=55')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
```

#### Langkah 3: Verifikasi

Cek apakah cron berjalan:

```bash
php artisan schedule:list
```

---

### VPS / Dedicated Server (dengan Supervisor)

#### Langkah 1: Install Supervisor

```bash
sudo apt-get install supervisor
```

#### Langkah 2: Buat Config

Buat file `/etc/supervisor/conf.d/ktm-worker.conf`:

```ini
[program:ktm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ktm/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ktm/storage/logs/worker.log
stopwaitsecs=3600
```

#### Langkah 3: Reload Supervisor

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ktm-worker:*
```

---

### Alternatif: Manual Trigger via HTTP

Jika tidak bisa setup cron/supervisor, gunakan HTTP trigger:

#### Tambah Route

Di `routes/web.php`:

```php
use Illuminate\Support\Facades\Artisan;

Route::get('/run-queue', function () {
    if (!auth()->check()) {
        abort(403);
    }

    Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--max-time' => 55,
    ]);

    return response()->json(['status' => 'Queue processed']);
})->middleware('auth');
```

#### Akses

Setelah generate KTM, buka:

```
https://yourdomain.com/run-queue
```

---

## 📊 Monitoring

### Lihat Queue Status

```bash
# Cek jumlah pending jobs
php artisan queue:monitor database:default

# Lihat failed jobs
php artisan queue:failed

# Retry semua failed jobs
php artisan queue:retry all

# Hapus semua pending jobs
php artisan queue:clear
```

### Lihat Progress di Database

```sql
-- Cek batch progress
SELECT * FROM ktm_batch_jobs ORDER BY created_at DESC LIMIT 10;

-- Cek pending queue jobs
SELECT * FROM jobs;

-- Cek failed jobs
SELECT * FROM failed_jobs;
```

---

## 🔧 Troubleshooting

### ❌ Progress Bar Tidak Update

**Penyebab:** Queue worker tidak berjalan

**Solusi:**

1. Cek apakah cron aktif di cPanel
2. Pastikan `QUEUE_CONNECTION=database`
3. Cek log: `storage/logs/laravel.log`

### ❌ Jobs Timeout

**Penyebab:** PHP max_execution_time terlalu pendek

**Solusi:**
Di `.htaccess` atau `php.ini`:

```
php_value max_execution_time 300
```

### ❌ Memory Limit Error

**Penyebab:** Generate image membutuhkan memory besar

**Solusi:**

```
php_value memory_limit 512M
```

### ❌ Jobs Stuck di "Pending"

**Penyebab:** Worker tidak me-pick jobs

**Solusi:**

```bash
# Restart queue
php artisan queue:restart

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### ❌ "Batch job not found for ID"

**Penyebab:** Database migration belum jalan

**Solusi:**

```bash
php artisan migrate
```

---

## ⚙️ Konfigurasi Lanjutan

### Chunk Size

Default: 50 students per batch. Ubah di `Index.php`:

```php
$chunkSize = 50; // Sesuaikan
```

### Job Timeout

Default: 10 menit per batch. Ubah di `GenerateKtmBatchJob.php`:

```php
public $timeout = 600; // dalam detik
```

### Max Retries

Default: 3 kali retry. Ubah di `GenerateKtmBatchJob.php`:

```php
public $tries = 3;
```

---

## 📝 Log Files

| File                       | Deskripsi                                |
| -------------------------- | ---------------------------------------- |
| `storage/logs/laravel.log` | Log aplikasi utama                       |
| `storage/logs/worker.log`  | Log queue worker (jika pakai supervisor) |

### Contoh Log Sukses

```
[2024-12-24 16:35:33] local.INFO: GenerateKtmBatchJob: Starting batch abc-123 with 50 students
[2024-12-24 16:36:10] local.INFO: GenerateKtmBatchJob: Completed batch of 50 students for template Default Template
```

---

## 🎯 Best Practices

1. **Gunakan database queue** untuk produksi, bukan sync
2. **Monitor failed_jobs** secara berkala
3. **Set timeout yang wajar** (10 menit cukup untuk 50 KTM)
4. **Batasi chunk size** sesuai memory server (50-100 optimal)
5. **Backup database** sebelum bulk generate

---

## 📞 Support

Jika ada masalah:

1. Cek `storage/logs/laravel.log`
2. Cek tabel `failed_jobs` di database
3. Test dengan sync mode dulu: `QUEUE_CONNECTION=sync`

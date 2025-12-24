# KTM Generator

Aplikasi web untuk generate Kartu Tanda Mahasiswa (KTM) secara otomatis dan bulk.

## ✨ Fitur

-   📝 **Manajemen Template KTM** - Desain template dengan drag & drop field
-   👨‍🎓 **Import Data Mahasiswa** - Import dari Excel/CSV
-   🖼️ **Generate Bulk KTM** - Generate 1000+ KTM tanpa timeout
-   📊 **Progress Tracking** - Real-time progress bar
-   🎨 **Multi Template** - Status KTM berbeda per template

## 🛠️ Tech Stack

-   Laravel 11
-   Livewire 3
-   Tailwind CSS
-   Intervention Image
-   MySQL

## 📦 Installation

```bash
git clone [repo-url]
cd ktm
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
```

## 🚀 Usage

1. Login sebagai admin
2. Upload template KTM
3. Import data mahasiswa
4. Klik "Generate All KTMs"

## 📖 Dokumentasi

-   [KTMJob.md](KTMJob.md) - Panduan menjalankan Queue di hosting

## 📄 License

MIT

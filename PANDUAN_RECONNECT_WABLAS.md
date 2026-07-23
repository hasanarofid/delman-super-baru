# 📱 Panduan Re-connect WA Blast & Pencegahan Blokir (Wablas + DelmanSuper)

Dokumen ini berisi panduan langkah demi langkah saat akun WhatsApp client sudah selesai dari masa peninjauan (*unban*) oleh pihak WhatsApp/Meta dan siap untuk di-scan ulang ke Wablas.

---

## 🚀 Bagian 1: Langkah Re-Scan QR Code di Wablas

Jika nomor client sudah pulih (bisa digunakan kembali di HP):

1. **Buka Dashboard Wablas**
   - Akses: [https://jogja.wablas.com](https://jogja.wablas.com)
   - Login dengan akun Wablas terdaftar.

2. **Masuk ke Menu Device**
   - Buka menu **Device / Perangkat**.
   - Cari perangkat bernama **`Sistem Modip Gateway`** (ID: `#1JJ2Y9`).
   - Status saat ini kemungkinan masih `DISCONNECTED`.

3. **Tampilkan QR Code**
   - Klik tombol **Scan QR / Connect** pada device `Sistem Modip Gateway`.
   - Di layar monitor akan muncul kode QR WhatsApp Web.

4. **Scan dari WhatsApp HP Client**
   - Buka aplikasi **WhatsApp Business** di HP Client.
   - Ketuk menu **Titik Tiga (⋮)** di kanan atas (Android) atau **Pengaturan / Settings** (iOS).
   - Pilih **Perangkat Tertaut (Linked Devices)** → Ketuk **Tautkan Perangkat (Link a Device)**.
   - Arahkan kamera HP ke QR Code yang ada di monitor.

5. **Verifikasi Status**
   - Pastikan status di dashboard Wablas berubah menjadi **`CONNECTED`** (Warna Hijau).
   - Tes kirim 1 pesan notifikasi dari aplikasi **DelmanSuper**.

---

## 🔒 Bagian 2: Pengaturan Keamanan Terpasang (Fitur Anti-Blokir Sistem)

Sistem **DelmanSuper** telah diperbarui dengan fitur pencegahan blokir otomatis yang terkonfigurasi di `.env` & Controller:

### 1. **Delay Pengiriman Acak (`WABLAS_DELAY_SECONDS`)**
- Sistem secara otomatis memberikan jeda acak (1–3 detik) di setiap pengiriman pesan notifikasi.
- **Tujuan**: Mencegah WhatsApp mendeteksi pengiriman pesan secara simultan/bot berkecepatan tinggi.

### 2. **Variasi Kode Unik / Anti-Spam Suffix (`WABLAS_ANTI_SPAM_SUFFIX`)**
- Setiap pesan yang dikirimkan oleh sistem akan secara otomatis disisipkan kode referensi unik di bagian paling bawah pesan (misal: `[Ref: 20260723083015-482]`).
- **Tujuan**: Memastikan setiap isi pesan memiliki struktur hash yang berbeda (tidak 100% identik). WhatsApp Meta sangat rawan memblokir nomor jika mendeteksi teks pesan massal yang sama persis dikirim berulang kali.

---

## 💡 Bagian 3: Tips & Best Practice untuk Client (Sisi Penggunaan)

Agar nomor WA client aman dari blokir di masa mendatang, bagikan tips ini kepada client:

1. **Hindari Blast Terlalu Banyak dalam 1 Menit**
   - Batasi pengiriman notifikasi masal sekaligus (misal maksimal 30–50 pengawasan per sesi).
2. **Gunakan Nomor WA Business Terverifikasi/Berumur**
   - Hindari menggunakan nomor baru yang belum berusia 1 bulan untuk keperluan blast otomatis.
3. **Simpan Kontak Penerima**
   - Sangat dianjurkan agar penerima (Kepala Sekolah/Guru) sudah menyimpan nomor pengirim di kontak mereka, karena algoritma WA memprioritaskan akun dengan interaksi 2 arah.
4. **Segera Hentikan Kirim jika Ada Peringatan**
   - Jika pesan gagal berturut-turut, segera cek status device di Wablas sebelum melanjutkan pengiriman berikutnya.

---

## 🛠️ Konfigurasi `.env` Server (Referensi Developer)

```env
# Wablas Configuration
WABLAS_ENDPOINT=https://jogja.wablas.com/api/send-message
WABLAS_TOKEN=ChvMJmr8Y5PwD130iY6kZqNQoAvCNQBxvH4RKiCOckJCAvEtVZtBO2Gyubj9THyU
WABLAS_SECRET=3eOzFZaU
WABLAS_DELAY_SECONDS=3
WABLAS_ANTI_SPAM_SUFFIX=true
```

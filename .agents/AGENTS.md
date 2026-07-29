# Shared Storage & File Handling Rules (Delman Super Platform)

## 1. Arsitektur Shared Storage (Integrasi 2 Website 1 Storage)
Aplikasi ini terintegrasi dengan satu direktori storage bersama untuk 2 website di server hosting (Hostinger):
- **Path Storage Server (Production)**: `/home/u144635195/shared-storage`
- **Path Local Fallback (Development)**: `storage_path('shared')`
- **Laravel Filesystem Disk**: `'shared'` (didefinisikan di `config/filesystems.php`)

### Struktur Folder di Shared Storage:
1. `umpanbalik/` -> Foto bukti pengawasan legacy (diupload dari formulir umpan balik lama).
2. `umpanbalik_dynamic/` -> Foto/File kuesioner dinamis (diupload dari pertanyaan tipe file / Q13).
3. `pengawas/` -> Foto profil / dokumen pengawas.
4. `laporan/` -> Lampiran pelaporan.
5. `favicon/` -> File favicon aplikasi.

---

## 2. Aturan Kode untuk Akses & Upload File (Best Practices)
1. **DILARANG Hardcode Path Absolute**:
   - Jangan pernah menuliskan string path `/home/u144635195/shared-storage` langsung di route atau controller.
   - Gunakan selalu helper disk Laravel: `Storage::disk('shared')->path('nama_subfolder/' . $filename)` atau `config('filesystems.disks.shared.root')`.

2. **Proses Upload File**:
   - Setiap proses upload file yang dibagikan antar-sistem WAJIB menyimpan ke disk `'shared'`:
     ```php
     $request->file('foto')->storeAs('umpanbalik', $imageName, 'shared');
     // atau untuk kuesioner dinamis:
     $file->storeAs('umpanbalik_dynamic', $imageName, 'shared');
     ```

3. **Penanganan Route Gambar**:
   - Route `umpanbalikfoto/{filename}` -> mengambil dari `Storage::disk('shared')->path('umpanbalik/' . $filename)`
   - Route `umpanbalik-dynamic/{filename}` -> mengambil dari `Storage::disk('shared')->path('umpanbalik_dynamic/' . $filename)`
   - Route `fotopengawas/{filename}` -> mengambil dari `Storage::disk('shared')->path('pengawas/' . $filename)`
   - Route `laporan/{filename}` -> mengambil dari `Storage::disk('shared')->path('laporan/' . $filename)`

---

## 3. Diagnosa "Gambar Tidak Muncul" / "Image Placeholder" (Dokumentasi Pengawasan)
Di role **Pengawas** -> menu **Dokumentasi Pengawasan** (`dashboard_pengawas/umpanbalik/dokumentasi.blade.php`), jika terdapat icon gambar rusak dengan alt text `Image placeholder`:
1. **Penyebab Utama**:
   - File fisik tidak ditemukan (HTTP 404) pada folder `/home/u144635195/shared-storage/umpanbalik/` atau `/home/u144635195/shared-storage/umpanbalik_dynamic/`.
   - File hilang saat migrasi database/hosting tanpa membawa file fisiknya dari server lama.
   - Permisi folder `shared-storage` tertutup (harus 755/775).
2. **Langkah Penanganan**:
   - Cek record di tabel `umpanbalik_t` / `tanggapan_umpanbalik_t` / `umpanbalik_answers` untuk nama file yang dirujuk.
   - Pastikan file fisik ada di lokasi `shared-storage/[folder_terkait]/[nama_file]`.

---

## 4. Penanganan MySQL Connection Limit (`SQLSTATE[HY000] [2002] Operation not permitted`)
Di hosting Hostinger, terdapat batasan koneksi simultan MySQL (Max Connections Limit).
- **Penyebab**: Ketika halaman tabel (seperti DataTables) memuat puluhan `<img>` sekaligus (misal 30-50 foto), setiap tag `<img>` mengirim request HTTP terpisah. Jika setiap request gambar membuka koneksi database (untuk menguji session/auth user), koneksi MySQL penuh dan melempar error HTTP 500 `SQLSTATE[HY000] [2002] Operation not permitted`.
- **Solusi**: Di setiap closure route penyedia gambar static (`umpanbalikfoto`, `umpanbalik-dynamic`, `fotopengawas`, `laporan`, `favicon`), panggil `DB::disconnect()` di baris paling atas closure agar koneksi MySQL langsung ditutup dan dilepas kembali ke pool sebelum membaca/mengirim file.


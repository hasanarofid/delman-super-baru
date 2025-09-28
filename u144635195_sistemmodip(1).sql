-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 28, 2025 at 12:31 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u144635195_sistemmodip`
--

-- --------------------------------------------------------

--
-- Table structure for table `guru_m`
--

CREATE TABLE `guru_m` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sekolah_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `kode_area` int(11) DEFAULT NULL,
  `jabatan` varchar(255) NOT NULL DEFAULT 'Guru',
  `is_aktif` tinyint(1) DEFAULT 1,
  `kabupaten_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guru_m`
--

INSERT INTO `guru_m` (`id`, `sekolah_id`, `nama`, `no_telp`, `kota`, `alamat_lengkap`, `kode_area`, `jabatan`, `is_aktif`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Siti Badriah S.P.D', '0881026697527', 'Yogjakarta', 'Desa Magelang RT 10 210', 42132, 'Guru', 1, 1, NULL, NULL),
(2, 1, 'Prof Abdullah S.P.D', '0881026697527', 'Yogjakarta', 'Desa Kulon RT 20 RW 10', 35433, 'Kepala Sekolah', 1, 1, NULL, NULL),
(3, 4, 'Sunariah', '0881026697527', NULL, 'Serang', NULL, 'Kepala Sekolah', 1, 3, '2024-03-28 02:48:17', '2024-03-28 02:48:17'),
(4, 4, 'Hani Haeroni', '0881026697527', NULL, 'Padarincang', NULL, 'Guru', 1, 3, '2024-03-28 02:49:08', '2024-03-28 02:49:08'),
(5, 25, 'Andi Bakhtiar', '087808505606', NULL, 'Serang', NULL, 'Guru', 1, 3, '2024-03-28 03:03:13', '2024-03-28 03:03:13'),
(6, 25, 'Untung Supriyanto', '087771361002', NULL, 'serang', NULL, 'Kepala Sekolah', 1, 3, '2024-03-28 03:03:56', '2024-03-28 03:03:56'),
(7, 25, 'Agus Santosa', '087838598853', NULL, 'serang', NULL, 'Guru', 1, 3, '2024-03-28 03:04:48', '2024-03-28 03:04:48'),
(8, 26, 'Endang Tirtana', '081287599838', 'kab tanggerang', 'PERUM LEGOK INDAH', 15157, 'Kepala Sekolah', 1, 6, '2024-10-16 01:24:45', '2024-10-16 01:24:45');

-- --------------------------------------------------------

--
-- Table structure for table `kategory`
--

CREATE TABLE `kategory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategory`
--

INSERT INTO `kategory` (`id`, `nama`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Program Reguler', 'perencanaan', NULL, NULL),
(2, 'Program Tematik', 'perencanaan', NULL, NULL),
(3, 'Program Dengan Kondisi Khusus', 'perencanaan', NULL, NULL),
(4, 'Laporan Reguler', 'pelaporan', NULL, NULL),
(5, 'Laporan Tematik', 'pelaporan', NULL, NULL),
(6, 'Laporan Dengan Kondisi Khusus', 'pelaporan', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lampiran`
--

CREATE TABLE `lampiran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pelaporan` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_kabupaten`
--

CREATE TABLE `master_kabupaten` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kabupaten` varchar(255) DEFAULT NULL,
  `kelompok_kabupaten` varchar(255) DEFAULT NULL,
  `is_aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_kabupaten`
--

INSERT INTO `master_kabupaten` (`id`, `nama_kabupaten`, `kelompok_kabupaten`, `is_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Kota Serang', 'Wilayah Seragon', 1, NULL, NULL),
(2, 'Kota Cilegon', 'Wilayah Seragon', 1, NULL, NULL),
(3, 'Kab Serang', 'Wilayah Seragon', 1, NULL, NULL),
(4, 'Kab Pandeglang', 'Wilayah Pandeglang', 1, NULL, NULL),
(5, 'Kab Lebak', 'Wilayah Lebak', 1, NULL, NULL),
(6, 'Kab Tangerang', 'Wilayah Kab Tangerang', 1, NULL, NULL),
(7, 'Kota Tangerang', 'Wilayah Tangerang', 1, NULL, NULL),
(8, 'Kota Tangerang Selatan', 'Wilayah Tangerang', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_tupoksi`
--

CREATE TABLE `master_tupoksi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran` varchar(255) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `kegiatan` varchar(255) NOT NULL,
  `id_kegiatan` int(11) DEFAULT NULL,
  `sub_kegiatan` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_tupoksi`
--

INSERT INTO `master_tupoksi` (`id`, `tahun_ajaran`, `semester`, `kegiatan`, `id_kegiatan`, `sub_kegiatan`, `urutan`, `created_at`, `updated_at`) VALUES
(1, '2024', 'I', 'Menusun Program Pengawasan Tahunan', NULL, NULL, 1, NULL, NULL),
(2, '2024', 'I', 'Melaksanakan Pembinaan guru dan/atau Kepala Sekolah', NULL, NULL, 2, NULL, NULL),
(3, '2024', 'I', 'Melaksanakan Pemantauan pelaksanaan 8 SNP', NULL, NULL, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2020_04_18_132841_create_profiles_table', 1),
(4, '2023_07_09_040601_create_profilemarketpalces_table', 1),
(5, '2023_08_05_145659_create_guru_m_s_table', 1),
(6, '2023_08_05_145834_create_sekolah_m_s_table', 1),
(7, '2023_08_13_023116_create_master_kabupaten', 1),
(8, '2023_08_28_125924_create_master_tupoksi', 1),
(9, '2023_09_06_005223_create_table_gol_pangkat_ruang', 1),
(10, '2024_02_15_123549_create_sekolahbinaan_t_table', 1),
(11, '2024_02_15_124409_create_tugaskerja_t_table', 1),
(12, '2024_02_15_124546_create_umpanbalik_m_table', 1),
(13, '2024_02_15_124602_create_umpanbalik_t_table', 1),
(14, '2024_02_29_133332_create_table_pelaporan', 1),
(15, '2024_03_03_112600_create_lampiran_table', 1),
(16, '2024_03_03_114058_create_kategory_table', 1),
(17, '2024_03_03_114119_create_sub_kategory_table', 1),
(18, '2024_03_04_160411_create_tanggapan_umpanbalik_table', 1),
(19, '2024_03_07_101724_create_rencakakerja_t_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelaporan`
--

CREATE TABLE `pelaporan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pengawas` int(11) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `sub_kategori` varchar(255) DEFAULT NULL,
  `judul` text NOT NULL,
  `sasaran` varchar(255) NOT NULL,
  `object` varchar(255) DEFAULT NULL,
  `tgl_pendampingan` date NOT NULL,
  `deskripsi_permasalahan` text DEFAULT NULL,
  `uraian` text DEFAULT NULL,
  `catatan_evaluasi` text DEFAULT NULL,
  `saran_rekomendasi` text DEFAULT NULL,
  `akses` text DEFAULT NULL,
  `disposisi` date DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelaporan`
--

INSERT INTO `pelaporan` (`id`, `id_pengawas`, `kategori`, `sub_kategori`, `judul`, `sasaran`, `object`, `tgl_pendampingan`, `deskripsi_permasalahan`, `uraian`, `catatan_evaluasi`, `saran_rekomendasi`, `akses`, `disposisi`, `lampiran`, `created_at`, `updated_at`) VALUES
(1, 13, '4', '2', 'Ujicoba Aplikasi Monitoring Online', 'Sekolah', '4', '2024-03-28', NULL, NULL, '<p>Sekolah dapat mengembangkan ……</p>', NULL, NULL, NULL, 'lampiran20240328025129_Z6CCRsootw.jpeg', '2024-03-28 02:51:29', '2024-03-28 02:51:29'),
(2, 14, '4', '3', 'Ujicoba Aplikasi Monitoring Online', 'Sekolah', '25', '2024-03-28', NULL, NULL, NULL, NULL, NULL, NULL, 'lampiran20240328031406_dP9BSVC1zZ.jpg', '2024-03-28 03:14:06', '2024-03-28 03:14:06'),
(3, 14, '4', '3', 'Ujicoba Aplikasi Monitoring Online', 'Guru', '5', '2024-03-28', NULL, NULL, '<p>jjndjsndasds</p>', '<p>lkkndaldnasds</p>', NULL, NULL, 'lampiran20240328031524_vZFqcqT0uW.jpg', '2024-03-28 03:15:24', '2024-03-28 03:15:24'),
(4, 14, '4', '3', 'monitoring uji kompetensi', 'Guru', '5', '2024-03-28', NULL, NULL, NULL, NULL, NULL, NULL, 'lampiran20240328052836_um7rVGNv5g.jpg', '2024-03-28 05:28:36', '2024-03-28 05:28:36'),
(5, 14, '4', '3', 'monitoring uji kompetensi', 'Guru', '7', '2024-03-31', NULL, NULL, '<p>KJDSFKJSFJK &nbsp;asdfjsdfjnkj</p>', '<p>lsakjflkaskd asfkas dasflda fsao</p>', NULL, NULL, 'lampiran20240331160009_hIvyNmtWLQ.png', '2024-03-31 16:00:09', '2024-03-31 16:00:09'),
(6, 14, '4', '3', 'ad', 'Guru', '5', '2024-04-02', NULL, NULL, '<p>ada</p>', '<p>ada</p>', NULL, NULL, 'lampiran20240401070449_MwaOgv7lXt.pdf', '2024-04-01 07:04:49', '2024-04-01 07:04:49'),
(7, 14, '4', '4', 'monitoring kesiapan sekolah pasca libur', 'Sekolah', '25', '2024-04-18', NULL, NULL, NULL, NULL, NULL, NULL, 'lampiran20240418035717_kPfuJsHO8M.jpg', '2024-04-18 03:57:17', '2024-04-18 03:57:17'),
(8, 14, '4', '3', 'test', 'Guru', '7', '2024-04-25', NULL, NULL, '<p>test</p>', '<p>test</p>', NULL, NULL, 'lampiran20240425073934_sjbiJm20ax.pdf', '2024-04-25 07:39:34', '2024-04-25 07:39:34'),
(9, 2, '4', '5', 'judul pelaporan 1', 'Guru', '4', '2024-10-01', NULL, NULL, '<p>evaluasi&nbsp;</p>', '<p>saran&nbsp;</p>', NULL, NULL, 'lampiran20240930130150_ijiI13PWQg.pdf', '2024-09-30 13:01:50', '2024-09-30 13:01:50'),
(10, 2, '4', '5', 'Testing Laporan', 'Guru', '4', '2024-10-06', NULL, NULL, '<p>catatan dan evaluasi</p>', '<p>saran dan rekomendasi</p>', NULL, NULL, 'lampiran20241006113240_QjNSoatAIe.pdf', '2024-10-06 11:32:40', '2024-10-06 11:32:40'),
(11, 2, '4', '5', 'test 3', 'Sekolah', '4', '2024-10-09', NULL, NULL, '<p>Catatan dan Evaluasi</p>', '<p>Saran dan Rekomendasi</p>', NULL, NULL, 'lampiran20241008012555_w3oOl3rh5U.pdf', '2024-10-08 01:25:55', '2024-10-08 01:25:55'),
(12, 2, '4', '5', 'test 4', 'Guru', '4', '2024-10-09', NULL, NULL, '<p>Catatan dan Evaluasi</p><p>Paragraph</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', '<p>Saran dan Rekomendasi</p>', NULL, NULL, 'lampiran20241008012840_2DwtmTe50w.xls', '2024-10-08 01:28:40', '2024-10-08 01:28:40'),
(13, 2, '4', '5', 'Judul Laporan', 'Guru', '4', '2024-10-08', NULL, NULL, '<p>Deskripsikan permasalahan</p><p>Paragraph</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', '<p>Deskripsikan permasalahan</p><p>Paragraph</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', NULL, NULL, 'lampiran20241008013922_vtJQJFQX4Z.xls', '2024-10-08 01:39:22', '2024-10-08 01:39:22'),
(15, 2, '4', '5', 'test 5', 'Guru', '4', '2024-10-09', NULL, NULL, '<p>Deskripsikan permasalahan</p><p>Paragraph</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', '<p>Deskripsikan permasalahan</p><p>Paragraph</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>', NULL, NULL, 'lampiran20241008033502_XvqN7ORN2P.xls', '2024-10-08 03:35:02', '2024-10-08 03:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `profilemarketpalces`
--

CREATE TABLE `profilemarketpalces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `diskripsi` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `telpon` text DEFAULT NULL,
  `zipcode` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `social1` varchar(255) DEFAULT NULL,
  `social2` varchar(255) DEFAULT NULL,
  `social3` varchar(255) DEFAULT NULL,
  `social4` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profilemarketpalces`
--

INSERT INTO `profilemarketpalces` (`id`, `title`, `diskripsi`, `address`, `telpon`, `zipcode`, `email`, `social1`, `social2`, `social3`, `social4`, `logo`, `favicon`, `kota`, `created_at`, `updated_at`) VALUES
(1, 'Sistem MoDiP', 'by Andi B Fransiska', 'Jogjakarta', '0813242424', 3055, 'hasanarofid@gitatalavial.com', 'facebok.com/gitatalavial', 'instagram.com/gitatalavial', 'theards.com/gitatalavial', 'twitter.com/gitatalavial', 'logogita.jpeg', 'logogita.jpeg', 'Yogjakarta', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `homepage` varchar(255) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `kode_area` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `no_telp`, `kota`, `homepage`, `alamat_lengkap`, `bio`, `kode_area`, `created_at`, `updated_at`) VALUES
(1, 1, '0812133313131', 'Yogjakarta', NULL, 'Desa Magelang RT 10 210', NULL, 42132, NULL, NULL),
(2, 2, '087262626262', 'Yogjakarta', NULL, 'Desa Kulon RT 20 RW 10', NULL, 35433, NULL, NULL),
(3, 3, '085234423', 'Yogjakarta', NULL, 'Desa Wetan RT 20 RW 10', NULL, 35433, NULL, NULL),
(4, 4, '085234423', 'Yogjakarta', NULL, 'Desa Wetan RT 20 RW 10', NULL, 35433, NULL, NULL),
(5, 5, '085234423', 'Banten', NULL, 'Desa Banten', NULL, 35433, NULL, NULL),
(6, 13, NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-23 14:30:25', '2024-03-23 14:30:25'),
(7, 14, NULL, NULL, NULL, NULL, NULL, NULL, '2024-03-28 02:57:52', '2024-03-28 02:57:52'),
(8, 15, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-16 01:17:41', '2024-10-16 01:17:41'),
(9, 16, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 01:51:00', '2024-10-17 01:51:00'),
(10, 16, '087773614368', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 01:51:00', '2024-10-17 01:51:00'),
(11, 17, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(12, 17, '081515910177', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(13, 18, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(14, 18, '081387074166', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(15, 19, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(16, 19, '081310426262', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(17, 20, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(18, 20, '081310101500', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(19, 21, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(20, 21, '08121319723', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(21, 22, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 03:59:07', '2024-10-17 03:59:07'),
(22, 22, '08121352339', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 03:59:07', '2024-10-17 03:59:07'),
(23, 23, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(24, 23, '087774521154', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(25, 24, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(26, 24, '082210794237', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(27, 25, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(28, 25, '082193087435', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(29, 26, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(30, 26, '081315031403', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(31, 27, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(32, 27, '081386049690', 'Kab tanggerang', NULL, 'Kab tanggerang', NULL, 15157, '2024-10-17 04:03:16', '2024-10-17 04:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `rencakakerja_t`
--

CREATE TABLE `rencakakerja_t` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pengawas` int(11) NOT NULL,
  `tahun_ajaran` varchar(255) NOT NULL,
  `nama_program_kerja` varchar(255) DEFAULT NULL,
  `kategoriprogram_id` int(11) NOT NULL,
  `sekolah_id` varchar(255) DEFAULT NULL,
  `deskripsi_permasalahan` text DEFAULT NULL,
  `target_capaian` text DEFAULT NULL,
  `tenggat_waktu` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rencakakerja_t`
--

INSERT INTO `rencakakerja_t` (`id`, `id_pengawas`, `tahun_ajaran`, `nama_program_kerja`, `kategoriprogram_id`, `sekolah_id`, `deskripsi_permasalahan`, `target_capaian`, `tenggat_waktu`, `created_at`, `updated_at`) VALUES
(1, 13, '2024', 'Pendampingan Kurikulum Merdeka', 1, '4,11,12,13,14,15,16,17', '<p>Sekolah binaan sudah mengembangkan diri menjadi Sekolah Mandiri Berubah sehingga diperlukan pendampingan&nbsp;</p>', '<p>Tercapainya KSOP pada satuan pendidikan</p>', 'Triwulan 1', '2024-03-28 02:45:03', '2024-03-28 02:45:03'),
(2, 13, '2024', 'Monitoring Uji Kompetensi', 1, '4,11,12,13,14,15,16,17', '<p>Pelaksanaan Uji kompetensi sebagai program akhis pembelajaran di jenjang SMK</p>', '<p>Terlaksananya Uji Kompetensi yang sesuai dengan ketentuan dan memenuhi harapan Dunia Industri</p>', 'Triwulan 2', '2024-03-28 02:46:44', '2024-03-28 02:46:44'),
(3, 14, '2024', 'Monitoring Uji Kompetensi', 1, '25,24,18,22,23', '<p>Penjaminan Mutu pelaksanaan Uji Kompetensi</p>', '<p>Tercapai pelaksanaan Uji Kompetensi sesuai Ketentuan</p>', 'Triwulan 2', '2024-03-28 03:12:31', '2024-03-28 03:12:31'),
(4, 14, '2024', 'Monitoring Pasca Libur Idul Fitri', 1, '25,24,18,22,23', '<p>memantau kesipana guru dan tendiknpasca libur</p>', '<p>tercapai kesiapan optimal sdm sekolah mengahdapi pelayanan pendidikan pasca liburan</p>', 'Triwulan 1', '2024-04-18 03:56:10', '2024-04-18 03:56:10'),
(5, 2, '2024', 'Program 1', 1, '4', NULL, NULL, 'Triwulan 4', '2024-09-30 13:00:49', '2024-10-15 03:13:34');

-- --------------------------------------------------------

--
-- Table structure for table `sekolahbinaan_t`
--

CREATE TABLE `sekolahbinaan_t` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pengawas` int(11) NOT NULL,
  `id_sekolah` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolahbinaan_t`
--

INSERT INTO `sekolahbinaan_t` (`id`, `id_pengawas`, `id_sekolah`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, NULL),
(2, 3, 2, NULL, NULL),
(3, 3, 3, NULL, NULL),
(4, 5, 1, NULL, NULL),
(5, 5, 2, NULL, NULL),
(6, 5, 3, NULL, NULL),
(7, 2, 4, '2024-03-23 14:58:00', '2024-03-23 14:58:00'),
(8, 13, 4, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(9, 13, 11, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(10, 13, 12, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(11, 13, 13, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(12, 13, 14, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(13, 13, 15, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(14, 13, 16, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(15, 13, 17, '2024-03-28 02:38:03', '2024-03-28 02:38:03'),
(16, 14, 25, '2024-03-28 03:08:36', '2024-03-28 03:08:36'),
(17, 14, 24, '2024-03-28 03:08:46', '2024-03-28 03:08:46'),
(18, 14, 18, '2024-03-28 03:09:15', '2024-03-28 03:09:15'),
(19, 14, 22, '2024-03-28 03:09:15', '2024-03-28 03:09:15'),
(20, 14, 23, '2024-03-28 03:09:15', '2024-03-28 03:09:15'),
(21, 15, 26, '2024-10-16 01:22:20', '2024-10-16 01:22:20'),
(23, 15, 27, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(24, 15, 28, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(25, 15, 29, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(26, 15, 30, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(27, 15, 31, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(28, 15, 32, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(29, 15, 33, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(30, 15, 34, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(31, 15, 35, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(32, 15, 36, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(33, 15, 37, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(34, 15, 38, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(35, 15, 39, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(36, 15, 40, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(37, 15, 41, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(38, 15, 42, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(39, 15, 43, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(40, 15, 44, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(41, 15, 45, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(42, 15, 46, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(43, 15, 47, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(44, 15, 48, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(45, 15, 49, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(46, 15, 50, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(47, 15, 51, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(48, 15, 52, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(49, 15, 53, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(50, 15, 54, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(51, 15, 55, '2024-10-18 08:50:35', '2024-10-18 08:50:35'),
(52, 15, 56, '2024-10-18 08:50:35', '2024-10-18 08:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah_m`
--

CREATE TABLE `sekolah_m` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_sekolah` varchar(255) DEFAULT NULL,
  `npsn` varchar(255) NOT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `kode_area` int(11) DEFAULT NULL,
  `is_aktif` tinyint(1) DEFAULT 1,
  `kabupaten_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sekolah_m`
--

INSERT INTO `sekolah_m` (`id`, `nama_sekolah`, `npsn`, `no_telp`, `kota`, `alamat_lengkap`, `kode_area`, `is_aktif`, `kabupaten_id`, `created_at`, `updated_at`) VALUES
(1, 'SMAN 1 Leuwidamar Lebak', '', '031244233', 'Banten', 'Lebak', 42132, 1, 1, NULL, NULL),
(2, 'SMAN 2 Leuwidamar Lebak', '', '031244233', 'Banten', 'Lebak', 42132, 1, 1, NULL, NULL),
(3, 'SMAN 1 Rangkasbitung Lebak', '', '031244233', 'Banten', 'Lebak', 42132, 1, 1, NULL, NULL),
(4, 'SMKN Padarincang', '', '0', NULL, '0', NULL, 1, 3, '2024-03-23 14:31:54', '2024-03-23 14:35:14'),
(11, 'SMKS Bismillah Padarincang', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 02:34:45', '2024-03-28 02:34:45'),
(12, 'SMKS Bhakti Pertiwi Ciptayasa Ciruas', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 02:35:11', '2024-03-28 02:35:11'),
(13, 'SMKS Darunnajah Pabuaran', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 02:35:39', '2024-03-28 02:35:39'),
(14, 'SMKS Daruttaiban Pabuaran', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 02:36:00', '2024-03-28 02:36:00'),
(15, 'SMKS Al - MAshoem Pabuaran', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 02:36:32', '2024-03-28 02:36:32'),
(16, 'SMKS Mutakhir Petir', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 02:36:55', '2024-03-28 02:36:55'),
(17, 'SMKS Miftahul Falah Tj', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 02:37:21', '2024-03-28 02:37:21'),
(18, 'SMKN 1 Kramatwatu', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 03:00:11', '2024-03-28 03:00:11'),
(19, 'SMKS Maulana Yusuf Kotser', '0', '0', '-', '-', 0, 1, 1, '2024-03-28 03:00:30', '2024-03-28 03:00:30'),
(20, 'SMKS Al Aroof Cilegon', '0', '0', '-', '-', 0, 1, 2, '2024-03-28 03:00:51', '2024-03-28 03:00:51'),
(21, 'SMKS Madinatul Hadid Cilegon', '0', '0', '-', '-', 0, 1, 2, '2024-03-28 03:01:10', '2024-03-28 03:01:10'),
(22, 'SMKS Pembangunan Terpadu Tj', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 03:01:37', '2024-03-28 03:01:37'),
(23, 'SMKS Nurul Falah Bojong Pandan Tj', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 03:02:04', '2024-03-28 03:02:04'),
(24, 'SMKS Insan Cendekia Kragilan', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 03:02:31', '2024-03-28 03:02:31'),
(25, 'SMKN 1 Ciruas', '0', '0', '-', '-', 0, 1, 3, '2024-03-28 03:02:40', '2024-03-28 03:02:40'),
(26, 'SMAN 17 Kabupaten Tangerang', '20613528', '081287599838', 'kab tanggerang', 'PERUM LEGOK INDAH', 15157, 1, 6, '2024-10-16 01:21:56', '2024-10-16 01:21:56'),
(27, 'SMAN 22 Kabupaten Tangerang', '20613519', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(28, 'SMAN 23 Kabupaten Tangerang', '20613785', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(29, 'SMAN 28 Kabupaten Tangerang', '20613771', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(30, 'SMA Al-Asmaniyah Legok', '69734019', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(31, 'SMA IT Alia Legok', '20623136', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(32, 'SMA PGRI 83 Legok', '20603325', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(33, 'SMA Insan Kamil Tartila Legok', '69970379', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(34, 'SMA Yuppentek 3 Legok', '20603356', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(35, 'SMA Sunan Bonang Legok', '20603378', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(36, 'SMA UPH College Kelapa Dua', '20606507', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(37, 'SMA Pahoa Kelapa Dua', '20616061', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(38, 'SMA Kristen Tunas Bangsa Kelapa Dua', '20613974', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(39, 'SMA Stela Maris Kelapa Dua', '69883655', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(40, 'SMA Kristen Menara Tirza Kelapa Dua', '69883639', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(41, 'SMA Pelita Harapan Kelapa Dua', '69896001', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(42, 'SMA Al Wildan Kelapa Dua', '69982590', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(43, 'SMA Islamic Village Kelapa Dua', '20603346', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(44, 'SMA Katolik Penabur Gading Serpong', '20603357', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(45, 'SMA Tarakanita Gading Serpong', '20613524', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(46, 'SMA Al-Fityan Kelapa Dua', '69984260', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(47, 'SMA Tunas Mulia Gading Serpong Kelapa Dua', '69923665', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(48, 'SMA Genius Curug', '69895488', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(49, 'SMA Surya Bangsa Kelapa Dua', '20623139', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(50, 'SMA Jakarta Nanyang School KelapaDua', '69900307', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(51, 'SMA Alfa Sanah Cisauk', '20603170', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(52, 'SMA Ruhul Bayan Cisauk', '20607845', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(53, 'SMA Plus Daarul Hikmah Cisauk', '69945479', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(54, 'SMA Imtek Pagedangan', '20613766', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(55, 'SMA Syafana Islamic School Pagedangan', '69971027', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02'),
(56, 'SMA Iman Pengharapan Kasih BSD Pagedangan', '69950814', '08245545454545', 'Kab Tanggerang', 'Kab Tanggerang', 15157, 1, 6, '2024-10-18 08:48:02', '2024-10-18 08:48:02');

-- --------------------------------------------------------

--
-- Table structure for table `sub_kategory`
--

CREATE TABLE `sub_kategory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_kategory` int(11) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_kategory`
--

INSERT INTO `sub_kategory` (`id`, `id_kategory`, `nama`, `created_at`, `updated_at`) VALUES
(1, 4, 'Harian', NULL, NULL),
(2, 4, 'Bulanan', NULL, NULL),
(3, 4, 'Rekapitulasi Pelaporan Reguler Satuan Pendidikan', NULL, NULL),
(4, 5, 'Laporan PKKS', NULL, NULL),
(5, 5, 'Laporan Tematik', NULL, NULL),
(6, 4, 'MPLS', NULL, NULL),
(7, 6, 'Ijin Operasional Penambahan Kompetensi Keahlian', NULL, NULL),
(8, 6, 'Konflik Kepemilikan Sekolah', NULL, NULL),
(9, 6, 'Pencegahan Stunting', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `table_gol_pangkat_ruang`
--

CREATE TABLE `table_gol_pangkat_ruang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_golongan` varchar(255) DEFAULT NULL,
  `pangkat` varchar(255) DEFAULT NULL,
  `ruang_kerja` varchar(255) DEFAULT NULL,
  `id_golongan` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `table_gol_pangkat_ruang`
--

INSERT INTO `table_gol_pangkat_ruang` (`id`, `nama_golongan`, `pangkat`, `ruang_kerja`, `id_golongan`, `created_at`, `updated_at`) VALUES
(1, 'Golongan III', 'Penata Muda', 'III A', 0, NULL, NULL),
(2, 'Golongan III', 'Penata Muda Tingkat 1', 'III B', 0, NULL, NULL),
(3, 'Golongan III', 'Penata', 'III C', 0, NULL, NULL),
(4, 'Golongan III', 'Penata Tingkat 1', 'III D', 0, NULL, NULL),
(5, 'Golongan IV', 'Pembina', 'IV A', 0, NULL, NULL),
(6, 'Golongan IV', 'Pembina Tingkat 1', 'IV B', 0, NULL, NULL),
(7, 'Golongan IV', 'Pembina Utama Muda', 'IV C', 0, NULL, NULL),
(8, 'Golongan IV', 'Pembina Utama Madya', 'IV D', 0, NULL, NULL),
(9, 'Golongan IV', 'Pembina Utama', 'IV E', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tanggapan_umpanbalik_t`
--

CREATE TABLE `tanggapan_umpanbalik_t` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_umpanbalik` int(11) NOT NULL,
  `jawaban_1` varchar(255) DEFAULT NULL,
  `jawaban_2` varchar(255) DEFAULT NULL,
  `jawaban_3` varchar(255) DEFAULT NULL,
  `jawaban_4` varchar(255) DEFAULT NULL,
  `jawaban_5` varchar(255) DEFAULT NULL,
  `jawaban_6` varchar(255) DEFAULT NULL,
  `jawaban_7` varchar(255) DEFAULT NULL,
  `jawaban_8` varchar(255) DEFAULT NULL,
  `jawaban_9` varchar(255) DEFAULT NULL,
  `jawaban_10` varchar(255) DEFAULT NULL,
  `jawaban_11` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tugaskerja_t`
--

CREATE TABLE `tugaskerja_t` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_pengawas` int(11) NOT NULL,
  `id_tugas` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tugaskerja_t`
--

INSERT INTO `tugaskerja_t` (`id`, `id_pengawas`, `id_tugas`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, NULL),
(2, 3, 2, NULL, NULL),
(3, 3, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `umpanbalik_m`
--

CREATE TABLE `umpanbalik_m` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pertanyaan` varchar(255) DEFAULT NULL,
  `jawaban` text DEFAULT NULL,
  `type_input` varchar(255) DEFAULT NULL,
  `aspek` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `urutan` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `umpanbalik_m`
--

INSERT INTO `umpanbalik_m` (`id`, `pertanyaan`, `jawaban`, `type_input`, `aspek`, `status`, `urutan`, `created_at`, `updated_at`) VALUES
(1, 'Pelayanan apakah yang diberikan oleh Pengawas sekolah saat ini?', 'supervisi managerial,supervisi Akademik,Evaluasi pendidikan,penelitian dan pengembangan,Pendampingan Tematik ( PPDB, MPLS, Uji Kompetensi, dll)', 'radiobutton', 'pendampingan', 1, 1, NULL, NULL),
(2, 'Apakah Pengawas sekolah menyampaikan rencana pendampingan sebelum pelaksanaan pendampingan', 'Ya,Tidak', 'radiobutton', 'pendampingan', 1, 2, NULL, NULL),
(3, 'Bagiaman Pelaksanaan pendampingan apakah sesuai dengan rencana?', 'Ya,Tidak', 'radiobutton', 'pendampingan', 1, 3, NULL, NULL),
(4, 'Bagaiamana pengawas sekolah melibatkan saudara dalam diskusi selama proses pendampingan?', 'Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang', 'radiobutton', 'pendampingan', 1, 4, NULL, NULL),
(5, 'Bagaimana intetraksi yang terjadi selama proses pendampingan?', 'Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang', 'radiobutton', 'pendampingan', 1, 5, NULL, NULL),
(6, 'Bagaimana suasana yang tercipta selama proses pendampingan?', 'Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang', 'radiobutton', 'pendampingan', 1, 6, NULL, NULL),
(7, 'Bagaimana penguasaan materi/ Pengetahuan  yang dimiliki Pengawas Sekolah', 'Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang', 'radiobutton', 'kompetensi', 1, 7, NULL, NULL),
(8, 'Bagaimana Komunikasi yang dilakukan selama proses pendampingan?', 'Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang', 'radiobutton', 'kompetensi', 1, 8, NULL, NULL),
(9, 'Bagaimana ketepatan waktu pelaksanaan pendampingan?', 'Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang', 'radiobutton', 'kompetensi', 1, 9, NULL, NULL),
(10, 'Berikan saran , hal apa yang harus ditingkatkan dari pelayanan Pengawas sekolah?', NULL, 'textarea', 'lainnya', 1, 10, NULL, NULL),
(11, 'Kebutuhan layanan supervisi / pendampingan seperti apa yang saudara harapkan?', NULL, 'textarea', 'lainnya', 1, 11, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `umpanbalik_t`
--

CREATE TABLE `umpanbalik_t` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_pelaporan` int(11) NOT NULL,
  `generate_url` varchar(255) NOT NULL,
  `id_pengawas` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `umpanbalik_t`
--

INSERT INTO `umpanbalik_t` (`id`, `id_user`, `id_pelaporan`, `generate_url`, `id_pengawas`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'c41534371d11441a9a3f6e3d713ee1b4', 13, '2024-03-28 02:51:30', '2024-03-28 02:51:30'),
(2, 1, 3, '8ea6ed08836c4eaaab76bf1a480cefa9', 14, '2024-03-28 03:15:24', '2024-03-28 03:15:24'),
(3, 1, 4, 'bca797b2758548149114dad569344e8e', 14, '2024-03-28 05:28:36', '2024-03-28 05:28:36'),
(4, 1, 5, 'c568650889b7412c91a67baddd6ed43a', 14, '2024-03-31 16:00:09', '2024-03-31 16:00:09'),
(5, 1, 6, '3eb50bcd0fe747a7b9bb8f8bb5746758', 14, '2024-04-01 07:04:49', '2024-04-01 07:04:49'),
(6, 1, 8, '7b7e902cae2f4118aa4fe3c80de796ec', 14, '2024-04-25 07:39:34', '2024-04-25 07:39:34'),
(7, 1, 9, '03de928d66134b95bf5de95a1511dbc1', 2, '2024-09-30 13:01:50', '2024-09-30 13:01:50'),
(8, 1, 10, '9de2e9bbe61f4d7fb9ded48a7135e921', 2, '2024-10-06 11:32:40', '2024-10-06 11:32:40'),
(9, 1, 11, '47bd9e8f9a8d4b33ae4cfce03c2456a2', 2, '2024-10-08 01:25:55', '2024-10-08 01:25:55'),
(10, 4, 12, 'de8c835e1663472bae7420ba34416987', 2, '2024-10-08 01:28:40', '2024-10-08 01:28:40'),
(11, 4, 13, '93624871d36843a593787c63f7e23236', 2, '2024-10-08 01:39:22', '2024-10-08 01:39:22'),
(13, 4, 15, 'a54e3035140f4cccb5235443f7e5f8b1', 2, '2024-10-08 03:35:02', '2024-10-08 03:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Admin',
  `nip` varchar(100) DEFAULT NULL,
  `foto_profile` varchar(255) DEFAULT NULL,
  `jenjang_jabatan` varchar(255) DEFAULT NULL,
  `pangkat` varchar(255) DEFAULT NULL,
  `gol_ruang` varchar(255) DEFAULT NULL,
  `no_telp` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `kode_area` int(11) DEFAULT NULL,
  `kabupaten_id` bigint(20) UNSIGNED NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `nip`, `foto_profile`, `jenjang_jabatan`, `pangkat`, `gol_ruang`, `no_telp`, `kota`, `alamat_lengkap`, `kode_area`, `kabupaten_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Gita', 'admin@gitatalavial.com', NULL, '$2y$10$xJVoD1t8jFnKVxpVCK3l9e0EEPG1tzItcLPEFMkl8l5iiR1VxkqJS', 'Super Admin', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 0, 'aw0KuevPwDMMWA3oAKv9UFrAtq0wXVJ2iHObE1XDNjl17bppCSpais9rI1YW', NULL, NULL),
(2, 'Hasan', 'hasan@gmail.com', NULL, '$2y$10$WaZr6MFZoPgqPtkJkghvRuq7iuV4LuQ.dGLUdsp7i6kAxOdSD8X/y', 'Pengawas', '15481548745154687', 'userdefault.jpg', 'Pengawas Sekolah Utama', 'Pembina Utama', 'IV/d', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(3, 'Akbar', 'akbar@gmail.com', NULL, '$2y$10$HDoc0Pl59mHsAt83OyAzKOuOyYBXpb4p4U0wvw7ebjd9CVhTzuKga', 'Stakeholder', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(4, 'Dr. Eko Supraptono, M.Si.', 'ekosupraptono@gmail.com', NULL, '$2y$10$E3cu7rg6C6fMiZDgk0YVtuOdRnz/MPzWexiMX2xG28Pt0EgXvIk6u', 'Pengawas', '196404151992031006', 'userdefault.jpg', 'Pengawas Sekolah Utama', 'Pembina Utama', 'IV/d', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL),
(5, 'Admin Wilayah Kota Serang', 'adminseragon@gmail.com', NULL, '$2y$10$JzxjopJYxWoGDbI3hq.A6eDzIp3OGIu5XvUEQQKj1kMvePfvFIika', 'Admin', '', 'userdefault.jpg', '', '', '', '087808505606', NULL, 'Kota Serang', NULL, 3, NULL, NULL, '2024-03-23 14:22:22'),
(6, 'Admin Wilayah Cilegon', 'admincilegon@gmail.com', NULL, '$2y$10$xp0opZXxPBroXV1RNYN5J.7AAHaMjetZ10JEyvLmJfIVKdcHFEp1m', 'Admin', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 2, '3rO1AepiNa3TVyefaJ4qJ7mLHSF57VsYnvx8few0oNHfZ2v3xmOmPciR0XBg', NULL, NULL),
(7, 'Admin Wilayah Kabupaten Serang', 'adminserang@gmail.com', NULL, '$2y$10$En5Nv9t8mC2BOrNtP4NS2O30kh9mJz4DK20LKAl1cKQUtaQ0WXtUW', 'Admin', '', 'userdefault.jpg', '', '', '', '87808505606', NULL, 'Kabupaten Serang', NULL, 3, 'krriJzm9wQWn58dgvXtkAAjtF8yqne4SW8Os8qCFZFiq7CiLteMqYZKM04SC', NULL, '2024-03-23 14:23:41'),
(8, 'Admin Wilayah Pandeglang', 'adminpandeglang@gmail.com', NULL, '$2y$10$xhxVOnjLy4GkCYbPVT7GWud8idDTDV6qjFrrjsUTgPWDF0gxu/CWi', 'Admin', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL),
(9, 'Admin Wilayah Lebak', 'adminlebak@gmail.com', NULL, '$2y$10$MJ2tST/.Cy3WgojE8Brn/.1av0pQujZ7pe2mOm/A/Q2w76iUsWgbu', 'Admin', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 5, NULL, NULL, NULL),
(10, 'Admin Wilayah Kab Tangerang', 'adminkabtangerang@gmail.com', NULL, '$2y$10$Vx2EsmqJPno6fZIYHNYLsOuaCTSGhaMdUvzRFF/pDS.HAF8T78EZi', 'Admin', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 6, 'VL8Yjs6Aw5a64KI2Hqxnugqd7Sx1cDui1eg5FedpEyZQATKl6FSY25ZCWPIE', NULL, NULL),
(11, 'Admin Wilayah Kota Tangerang', 'adminkotatangerang@gmail.com', NULL, '$2y$10$1lv7Y0kVrYYeVFlLFvENNulHyulzTQmBwGlQ10kLX0jSSntVnF2Aa', 'Admin', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 7, NULL, NULL, NULL),
(12, 'Admin Wilayah Kota Tangerang Selatan', 'adminkotatangerangselatan@gmail.com', NULL, '$2y$10$PRHuUFXjEP4ilXI.KIV55OFduPGOfAfFX/1sJVEK/yAxDuxj92rue', 'Admin', '', 'userdefault.jpg', '', '', '', NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL),
(13, 'Anto Jayadi Kusuma, M.Pd.', 'antojayadi@gmail.com', NULL, '$2y$10$InETdBJ6TU85FyvI9BMT0uY4FLxhWGbxG5smJwdvPmRFlMgZHuUnG', 'Pengawas', '198502022009021002', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', '5', '5', '85719086864', NULL, 'padarincang', NULL, 3, NULL, '2024-03-23 14:30:25', '2024-03-28 02:41:46'),
(14, 'Billy Tedja Arief', 'billytedjaarief@gmail.com', NULL, '$2y$10$ysjjuB9uDjyBLuDnqqDr5.gflTrnLiaEaDWhz2su3zplu0GbMQlGy', 'Pengawas', '197907012010011006', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', '4', '4', '087822099180', NULL, 'serang', NULL, 3, NULL, '2024-03-28 02:57:52', '2024-03-28 02:57:52'),
(15, 'Drs. Utoyo, M.Pd', 'utoyo@delmansuper.com', NULL, '$2y$10$VBcRcE0zLQ5LHKpp.Kbhnu970LpdSpM9nx88ohx9uo66zbBtXAv12', 'Pengawas', '196604251994121001', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', '6', '6', '085773721283', 'kab tanggerang', 'Kab tanggerang', 15157, 6, NULL, '2024-10-16 01:17:41', '2024-10-16 01:17:41'),
(16, 'Drs. H. Suparto, M.Pd', 'suparto@gmail.com', NULL, '$2y$10$0oP6YYGWHUUwOIoNzocrxuY1FizBAsxzwr3NVttL5zgdT.1b786vS', 'Pengawas', '196606241994121005', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '087773614368', NULL, NULL, NULL, 6, NULL, '2024-10-17 01:51:00', '2024-10-17 01:51:00'),
(17, 'RISWAN, S.Pd.,', 'riswan@gmail.com', NULL, '$2y$10$kN6i4Pdr90G8BfJ/AJm2re7AVqB8tZPrtk/xIp2LPp5.DLG6XP8mC', 'Pengawas', '196804171994011001', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '081515910177', NULL, NULL, NULL, 6, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(18, 'Drs. Tri Susilarto, M.Pd', 'trisusilarto@gmail.com', NULL, '$2y$10$bIKV5Ac3sVR6pIAZMxVREeKLI/0Yt4lww8aZbyByxZkLH.X3/RX1G', 'Pengawas', '196608311994121003', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '081387074166', NULL, NULL, NULL, 6, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(19, 'Zonya Rubeka Bawengan, M.Pd', 'zonyarubeka@gmail.com', NULL, '$2y$10$Uj3X0ml0FDfe4QDohWwb9u.JSFghsAXY4lUGePt7kxHcTyUdIkp8i', 'Pengawas', '196810071994122003', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '081310426262', NULL, NULL, NULL, 6, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(20, 'Mohamad Ina Royana, S.Pd., MM', 'mohamadina@gmail.com', NULL, '$2y$10$sqa6IBbV9FVNn46XOjda.O10/yDXYpYM//bbJkU2WesLltMIfTogW', 'Pengawas', '197602282003121003', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '081310101500', NULL, NULL, NULL, 6, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(21, 'Herwan Toni, S.Pd., M.Eng', 'herwantoni@gmail.com', NULL, '$2y$10$oRO6wmZN2f924qOv7hbVZewi5MuZEaPHh5ygf8QsiSdlWeVnI9I2.', 'Pengawas', '196609051999031004', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '08121319723', NULL, NULL, NULL, 6, NULL, '2024-10-17 03:59:06', '2024-10-17 03:59:06'),
(22, 'Dra. Endeh Indradewi, M.Pd', 'endehindradewi@gmail.com', NULL, '$2y$10$m9mknWOURqQCp/cmEN7MwubvB3yP7A4.sfwOgLCjmej8LMyayBQ9m', 'Pengawas', '196509251992032006', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '08121352339', NULL, NULL, NULL, 6, NULL, '2024-10-17 03:59:07', '2024-10-17 03:59:07'),
(23, 'Eko Saptini., S.Pd.', 'ekosaptini@gmail.com', NULL, '$2y$10$lX5o0fqdbfpLwdzkP9ulke56UjaImCR2hbqONGxZvy4DalVkTGCuq', 'Pengawas', '1970001152008012013', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '087774521154', NULL, NULL, NULL, 6, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(24, 'Masliha, M.Pd.', 'masliha@gmail.com', NULL, '$2y$10$HZwcLrcOHy8tCtfu6Udice1I2I.dxISIwE1gnyy7teWaFz4zgBt8y', 'Pengawas', '197202022007012015', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '082210794237', NULL, NULL, NULL, 6, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(25, 'Umayah, S.P., M.Si.', 'umayah@gmail.com', NULL, '$2y$10$G9wS7TT4ZdIf8.H.y6ztT.ROZ3eRdtLoZ60AJlRW.GJrlhyyjkW4K', 'Pengawas', '197311262006042023', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '082193087435', NULL, NULL, NULL, 6, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(26, 'Henry Akmal, S.T., M.Pd. ', 'henryakmal@gmail.com', NULL, '$2y$10$Ul/KWs6CN/.52Pnjs3FbHOfBRwNYCHYV0AQ0KDtdX/gd5X7BaZwky', 'Pengawas', '197510262009021001', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '081315031403', NULL, NULL, NULL, 6, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16'),
(27, 'Dra. Hj. Ngatini, MM', 'ngatini@gmail.com', NULL, '$2y$10$pE.hKJJ.WymfD9kcTrokKOEysf/gSMLxzfWgeME7EjagfWLBxrCc2', 'Pengawas', '196606241994032006', 'userdefault.jpg', 'Pengawas Sekolah Ahli Madya', 'Pembina Tk.I', 'Ivb', '081386049690', NULL, NULL, NULL, 6, NULL, '2024-10-17 04:03:16', '2024-10-17 04:03:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guru_m`
--
ALTER TABLE `guru_m`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_m_kabupaten_id_index` (`kabupaten_id`),
  ADD KEY `guru_m_sekolah_id_index` (`sekolah_id`);

--
-- Indexes for table `kategory`
--
ALTER TABLE `kategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lampiran`
--
ALTER TABLE `lampiran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_kabupaten`
--
ALTER TABLE `master_kabupaten`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_tupoksi`
--
ALTER TABLE `master_tupoksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `pelaporan`
--
ALTER TABLE `pelaporan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profilemarketpalces`
--
ALTER TABLE `profilemarketpalces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profiles_user_id_index` (`user_id`);

--
-- Indexes for table `rencakakerja_t`
--
ALTER TABLE `rencakakerja_t`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sekolahbinaan_t`
--
ALTER TABLE `sekolahbinaan_t`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sekolah_m`
--
ALTER TABLE `sekolah_m`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_m_kabupaten_id_index` (`kabupaten_id`);

--
-- Indexes for table `sub_kategory`
--
ALTER TABLE `sub_kategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_gol_pangkat_ruang`
--
ALTER TABLE `table_gol_pangkat_ruang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tanggapan_umpanbalik_t`
--
ALTER TABLE `tanggapan_umpanbalik_t`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tugaskerja_t`
--
ALTER TABLE `tugaskerja_t`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `umpanbalik_m`
--
ALTER TABLE `umpanbalik_m`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `umpanbalik_t`
--
ALTER TABLE `umpanbalik_t`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_kabupaten_id_index` (`kabupaten_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `guru_m`
--
ALTER TABLE `guru_m`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kategory`
--
ALTER TABLE `kategory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lampiran`
--
ALTER TABLE `lampiran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_kabupaten`
--
ALTER TABLE `master_kabupaten`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `master_tupoksi`
--
ALTER TABLE `master_tupoksi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pelaporan`
--
ALTER TABLE `pelaporan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `profilemarketpalces`
--
ALTER TABLE `profilemarketpalces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `rencakakerja_t`
--
ALTER TABLE `rencakakerja_t`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sekolahbinaan_t`
--
ALTER TABLE `sekolahbinaan_t`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `sekolah_m`
--
ALTER TABLE `sekolah_m`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `sub_kategory`
--
ALTER TABLE `sub_kategory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `table_gol_pangkat_ruang`
--
ALTER TABLE `table_gol_pangkat_ruang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tanggapan_umpanbalik_t`
--
ALTER TABLE `tanggapan_umpanbalik_t`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tugaskerja_t`
--
ALTER TABLE `tugaskerja_t`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `umpanbalik_m`
--
ALTER TABLE `umpanbalik_m`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `umpanbalik_t`
--
ALTER TABLE `umpanbalik_t`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

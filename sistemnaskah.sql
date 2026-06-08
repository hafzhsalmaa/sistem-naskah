-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 08, 2026 at 04:25 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistemnaskah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `id_user`, `email`, `username`, `password`) VALUES
(1, 5, 'admints2@gmail.com', 'Admin2', '$2y$12$BEIeeMbbAC4/FddKRaCzkuDzi6dg6t.Buw0msjCAWUvi/6Rdnt/Aq');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `editor`
--

CREATE TABLE `editor` (
  `id_editor` bigint UNSIGNED NOT NULL,
  `kode_editor` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidang_keahlian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_mapel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mata_pelajaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `editor`
--

INSERT INTO `editor` (`id_editor`, `kode_editor`, `id_user`, `nama_lengkap`, `no_hp`, `bidang_keahlian`, `kategori_mapel`, `mata_pelajaran`) VALUES
(1, 'E-IPA-SDMI-001', 7, 'Editor Satu', '62812344441217', 'SD/MI', 'Umum', 'IPA'),
(2, 'E-BING-SMAMASMK-002', 9, 'Editor Dua', '6298152798172', 'SMA/MA/SMK', 'Bahasa', 'Bahasa Inggris'),
(3, 'E-BING-SDMI-003', 20, 'Editor Tiga', '081356785196', 'SD/MI', 'Bahasa', 'Bahasa Inggris');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_penerbitan`
--

CREATE TABLE `jadwal_penerbitan` (
  `id_jadwal` bigint UNSIGNED NOT NULL,
  `id_naskah` bigint UNSIGNED NOT NULL,
  `tanggal_cetak` datetime NOT NULL,
  `catatan_admin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_penerbitan`
--

INSERT INTO `jadwal_penerbitan` (`id_jadwal`, `id_naskah`, `tanggal_cetak`, `catatan_admin`) VALUES
(1, 4, '2027-02-01 00:00:00', 'Terbit February 2027'),
(2, 13, '2027-06-01 00:00:00', 'Terbit June 2027'),
(3, 8, '2028-06-01 00:00:00', 'Terbit June 2028'),
(4, 16, '2030-10-01 00:00:00', 'Terbit October 2030'),
(5, 17, '2026-08-01 00:00:00', 'Terbit August 2026'),
(6, 11, '2026-11-01 00:00:00', 'Terbit November 2026'),
(7, 10, '2031-06-01 00:00:00', 'Terbit Juni 2031');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `layout`
--

CREATE TABLE `layout` (
  `id_layout` bigint UNSIGNED NOT NULL,
  `id_naskah` bigint UNSIGNED NOT NULL,
  `id_layouter` bigint UNSIGNED NOT NULL,
  `id_penulis` bigint UNSIGNED NOT NULL,
  `file_layout` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file_layout_asli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_layout` datetime NOT NULL,
  `status_layout` enum('Proses Layout','Selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Proses Layout'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layout`
--

INSERT INTO `layout` (`id_layout`, `id_naskah`, `id_layouter`, `id_penulis`, `file_layout`, `nama_file_layout_asli`, `tanggal_layout`, `status_layout`) VALUES
(1, 4, 1, 1, 'layout/BWzwbHk4TtxcodGTc3vYbbdIHMtjqzsuMpW99lCS.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-04-19 10:00:00', 'Selesai'),
(2, 6, 1, 1, 'layout/LlsEU6podvEmH8uzmJIzboCMICetKqaJZAmjinv6.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-11 08:01:41', 'Selesai'),
(3, 5, 1, 1, 'layout/6mucmQmGmu2rzT7SycIKb9w6i8qNbmcPwbsMhFSd.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-14 05:24:06', 'Selesai'),
(4, 7, 1, 1, 'layout/roNOdBd6aW6msFoj4GEDM8uzvMWynsWHnCbbUaCq.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-25 06:35:00', 'Selesai'),
(5, 13, 1, 1, 'layout/q3kT5QyorxsGm1RKbsAAopODR56RicN92ofbjYfx.docx', 'Naskah Buku Layout Layouter.pdf', '2026-05-25 06:39:04', 'Selesai'),
(6, 9, 1, 1, 'layout/jdpTxQm9JsRCM0QN7dceL9llioLQmGbxfmZdRVmg.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-22 03:58:01', 'Selesai'),
(7, 10, 1, 1, 'layout/TqcLZSjiYqngtcJwUOaDOwysl6YQvCBdvS4hicBi.docx', 'Naskah Buku Layout Layouter.pdf', '2026-05-25 06:37:28', 'Selesai'),
(8, 8, 1, 2, 'layout/Bl0TC685ddmPxH3vlK82QfCdo3eUb8AJQ2f8SUtD.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-26 02:40:22', 'Selesai'),
(9, 16, 1, 2, 'layout/cfthUV9wjWSf5daIia9NiaIihCcaNbC32E4zq1Eu.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-26 04:31:47', 'Selesai'),
(10, 17, 1, 2, 'layout/Pir7GQ1GEUMPaQklnMxmfIo5YTn8Cnh9Z1kiP7zh.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-26 06:55:04', 'Selesai'),
(11, 11, 1, 1, 'layout/p15WQYXxGHL5vtgWyinEheqbksxVt3MaYeY68tv7.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-05-29 07:03:23', 'Selesai'),
(12, 20, 1, 1, 'layout/uv6FVFUQxJNPclW4zg3LuYCOeVTXKqOSjVbKgeMp.pdf', 'Naskah Buku Layout Layouter.pdf', '2026-06-06 17:04:36', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `layouter`
--

CREATE TABLE `layouter` (
  `id_layouter` bigint UNSIGNED NOT NULL,
  `kode_layouter` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bidang_keahlian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_mapel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mata_pelajaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layouter`
--

INSERT INTO `layouter` (`id_layouter`, `kode_layouter`, `id_user`, `nama_lengkap`, `no_hp`, `bidang_keahlian`, `kategori_mapel`, `mata_pelajaran`) VALUES
(1, 'L-IPA-SMAMASMK-001', 8, 'Layouter Satu', '085110873456', 'SMA/MA/SMK', 'Umum', 'IPA');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_15_100000_create_sistem_naskah_tables', 1),
(5, '2026_04_17_000001_add_auth_fields_to_admin_table', 2),
(6, '2026_04_17_000002_update_role_profile_columns_for_dynamic_register', 3),
(7, '2026_04_17_000003_add_bidang_mapel_to_penulis_table', 4),
(8, '2026_04_17_000004_split_bidang_mapel_into_kategori_and_mata_pelajaran', 5),
(9, '2026_04_17_000005_extend_naskah_status_for_layout_flow', 6),
(10, '2026_04_19_000001_add_no_hp_to_editor_and_layouter_tables', 7),
(11, '2026_04_19_000002_create_notifications_table', 8),
(12, '2026_05_11_000001_add_jurusan_pendidikan_to_penulis_table', 9),
(13, '2026_05_11_000002_add_kode_naskah_to_naskah_table', 10),
(14, '2026_05_11_000003_add_kode_penulis_to_penulis_table', 11),
(15, '2026_05_11_000004_add_kode_editor_to_editor_table', 12),
(16, '2026_05_11_000005_add_kode_layouter_to_layouter_table', 13),
(17, '2026_05_14_000001_add_perbaikan_dikirim_status_to_review_flow', 14),
(18, '2026_05_15_000001_add_bidang_keahlian_to_naskah_table', 15),
(19, '2026_05_15_000002_drop_legacy_bidang_columns_from_penulis_table', 16),
(20, '2026_05_26_000001_add_original_file_names_to_naskah_files', 17),
(21, '2026_05_29_000001_add_file_final_editor_fields_to_naskah_table', 18),
(22, '2026_05_29_000002_add_review_attachment_fields_to_revisi_table', 19),
(23, '2026_06_06_000001_group_editor_layouter_bidang_keahlian', 20),
(24, '2026_06_07_000001_drop_foto_profil_from_penulis_table', 21),
(25, '2026_06_07_000002_drop_id_editor_final_from_naskah_table', 22),
(26, '2026_06_08_000001_drop_notifikasi_table', 23);

-- --------------------------------------------------------

--
-- Table structure for table `naskah`
--

CREATE TABLE `naskah` (
  `id_naskah` bigint UNSIGNED NOT NULL,
  `kode_naskah` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_penulis` bigint UNSIGNED NOT NULL,
  `id_editor` bigint UNSIGNED DEFAULT NULL,
  `id_layouter` bigint UNSIGNED DEFAULT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bidang_keahlian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kurikulum` enum('Merdeka','K13') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_mapel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mata_pelajaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cek_kurikulum` tinyint(1) NOT NULL DEFAULT '0',
  `cek_silabus` tinyint(1) NOT NULL DEFAULT '0',
  `cek_rpp` tinyint(1) NOT NULL DEFAULT '0',
  `bebas_sara` tinyint(1) NOT NULL DEFAULT '0',
  `tanggal_submit` datetime NOT NULL,
  `status_naskah` enum('Pending Review','Perbaikan Dikirim','Ditolak','Revisi','Diterima','Menunggu Layout','Proses Layout','Revisi Layout','Selesai Layout') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending Review',
  `file_final_editor_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_file_final_editor_asli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_file_final_editor` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `naskah`
--

INSERT INTO `naskah` (`id_naskah`, `kode_naskah`, `id_penulis`, `id_editor`, `id_layouter`, `judul`, `kelas`, `bidang_keahlian`, `kurikulum`, `kategori_mapel`, `mata_pelajaran`, `deskripsi`, `cek_kurikulum`, `cek_silabus`, `cek_rpp`, `bebas_sara`, `tanggal_submit`, `status_naskah`, `file_final_editor_path`, `nama_file_final_editor_asli`, `tanggal_file_final_editor`) VALUES
(3, 'N-IPA-05-0003', 1, 2, 1, 'IPA', '5', 'SMA', 'Merdeka', 'Umum', 'IPA', 'xxxx', 1, 1, 1, 1, '2026-04-16 03:43:42', 'Menunggu Layout', NULL, NULL, NULL),
(4, 'N-IPA-05-0004', 1, 2, 1, 'IPA', '5', 'SMA', 'Merdeka', 'Umum', 'IPA', 'xxxx', 0, 0, 0, 0, '2026-04-16 03:44:08', 'Selesai Layout', NULL, NULL, NULL),
(5, 'N-BIG-06-0005', 1, 1, 1, 'Let\'s Go Series', '6', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'For the first time..', 1, 1, 1, 1, '2026-04-19 11:38:58', 'Selesai Layout', NULL, NULL, NULL),
(6, 'N-BIG-12-0006', 1, 1, 1, 'Vocabularies', '12', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Look at me!', 1, 1, 1, 1, '2026-04-19 12:09:11', 'Selesai Layout', NULL, NULL, NULL),
(7, 'N-BIG-12-0007', 1, 1, 1, 'English for Changes', '12', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Untuk menunjang kemampuan berbahasa Inggris saat ini banyak hal yang bisa kamu lakukan.', 1, 1, 1, 1, '2026-04-19 14:03:41', 'Selesai Layout', NULL, NULL, NULL),
(8, 'N-BIG-06-0008', 2, 3, 1, 'Try On the Book', '6', 'SD', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'You see it? It\'s LEGEND', 1, 1, 1, 1, '2026-05-14 06:03:43', 'Selesai Layout', NULL, NULL, NULL),
(9, 'N-IPA-11-0009', 1, 2, 1, 'Seri IPA Biologi, Fisika, & Kimia Kelas XI & XII', '11', 'SMA', 'Merdeka', 'Umum', 'IPA', 'Untuk permulaan', 1, 1, 1, 1, '2026-05-15 06:22:33', 'Selesai Layout', NULL, NULL, NULL),
(10, 'N-IPA-11-0010', 1, 2, 1, 'Seri IPA Biologi, Fisika, & Kimia', '11', 'SMA', 'Merdeka', 'Umum', 'IPA', 'Untuk pemula', 1, 1, 1, 1, '2026-05-15 06:23:39', 'Selesai Layout', NULL, NULL, NULL),
(11, 'N-BIG-10-0011', 1, 2, 1, 'Easy English Conversation XII', '10', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Iziin', 1, 1, 1, 1, '2026-05-16 03:01:30', 'Selesai Layout', 'editor-final/3iYqVzaq9Q9hnfdeyDtVkuT4vT7raeWRexs7UVxa.pdf', 'Naskah Buku Final Editor.docx', '2026-05-29 07:01:50'),
(12, 'N-BIG-11-0012', 2, 1, NULL, 'Work In Progress', '11', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Coba', 1, 1, 1, 1, '2026-05-16 07:01:20', 'Diterima', 'editor-final/j0nNemGdovjNtXW71s7fomigLDxtIJE2sSkYlrD0.docx', 'Naskah Buku Final Editor.docx', '2026-05-29 08:38:39'),
(13, 'N-IPA-11-0013', 1, 2, 1, 'IPA', '11', 'SMA', 'Merdeka', 'Umum', 'IPA', 'Goal', 1, 1, 1, 1, '2026-05-17 14:21:50', 'Selesai Layout', NULL, NULL, NULL),
(14, 'N-BIG-06-0014', 2, 3, 1, 'Audible Book\'s Rain', '6', 'SD', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Mohon diterima, terima kasih', 1, 1, 1, 1, '2026-05-25 08:21:18', 'Menunggu Layout', NULL, NULL, NULL),
(16, 'N-BIG-10-0016', 2, 2, 1, 'Holiday to Study', '10', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Mengirim draft awal naskah buku Bahasa Inggris untuk proses review editor. Mohon pengecekan terkait isi materi, struktur penyajian, kebahasaan, dan kesesuaian kurikulum. Terima kasih.', 1, 1, 1, 1, '2026-05-26 04:14:50', 'Selesai Layout', NULL, NULL, NULL),
(17, 'N-BIG-12-0017', 2, 2, 1, 'Open The World Class XII', '12', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Naskah Buku Bahasa Inggris untuk kelas XII', 1, 1, 1, 1, '2026-05-26 06:45:03', 'Selesai Layout', NULL, NULL, NULL),
(18, 'N-IPA-07-0018', 2, NULL, NULL, 'Read the Story and Fun!', '7', 'MTS', 'Merdeka', 'Umum', 'IPA', 'Naskah pertama untuk siswa/i MTS kelas 7', 0, 0, 0, 0, '2026-06-06 14:22:49', 'Pending Review', NULL, NULL, NULL),
(19, 'N-BIG-11-0019', 2, NULL, NULL, 'Story Telling', '11', 'SMA', 'Merdeka', 'Bahasa', 'Bahasa Inggris', 'Naskah pertama untuk siswa/i SMA kelas 11', 0, 0, 0, 0, '2026-06-06 14:27:06', 'Pending Review', NULL, NULL, NULL),
(20, 'N-IPA-05-0020', 1, 1, 1, 'Senang Belajar Ilmu Pengetahuan Alam', '5', 'SD', 'Merdeka', 'Umum', 'IPA', 'Naskah buku untuk siswa/i SD kelas 5', 1, 1, 1, 1, '2026-06-06 14:38:36', 'Selesai Layout', 'editor-final/kRTWfFeKJRfs9360v57xe1ZIquE9SwYJ2hLoMlUg.docx', 'Naskah Buku Final Editor.docx', '2026-06-06 16:50:17'),
(21, 'N-IPA-04-0021', 1, 1, NULL, 'Hafalan Materi Ilmu Pengetahuan Alam', '4', 'SD', 'Merdeka', 'Umum', 'IPA', 'Naskah buku pertama untuk siswa/i kelas 4 SD', 1, 1, 1, 1, '2026-06-06 16:37:38', 'Revisi', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('0137af0f-73ef-44ea-a3b1-98f87e6eb40b', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia Kelas XI & XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-21 20:57:58', '2026-05-21 20:57:58'),
('02e79cfd-d585-48d1-86dc-936315c8711f', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Open The World Class XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 23:54:47', '2026-05-25 23:54:47'),
('046ba4bb-0095-4064-9e4f-5a7eab377305', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"English for Changes\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-04-19 07:03:41', '2026-04-19 07:03:41'),
('0842207c-46b2-48fd-83b8-d3394e095e13', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/5\"}', '2026-04-19 04:58:47', '2026-04-19 04:52:29', '2026-04-19 04:58:47'),
('0913a4b7-1381-44d1-b347-fa6167a5edea', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Audible Book\'s Rain\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/14\"}', NULL, '2026-05-28 23:27:33', '2026-05-28 23:27:33'),
('0a787ed7-66b9-4ddb-8459-59376eb83296', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:25:20', '2026-05-25 21:25:20'),
('0f9104ab-cf6c-4286-ace5-d6521bf5c902', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/20\"}', NULL, '2026-06-06 09:17:59', '2026-06-06 09:17:59'),
('1139f2d8-d1d8-41ee-81ea-79bb08609ab3', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/5\"}', '2026-04-19 04:42:11', '2026-04-19 04:41:54', '2026-04-19 04:42:11'),
('11d05d5f-7803-47e7-97bb-3921aaf85357', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Easy English Conversation XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-29 00:03:13', '2026-05-29 00:03:13'),
('1451f864-f0a6-4b17-a794-7b0400ffc3ad', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/10\"}', NULL, '2026-05-14 23:28:37', '2026-05-14 23:28:37'),
('14d3d35a-c52d-49f2-8216-c7295b572cb5', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/12\"}', NULL, '2026-05-29 00:55:25', '2026-05-29 00:55:25'),
('15a4260c-44d4-49a4-aeea-1c3a07e838cc', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Open The World Class XII\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 23:45:03', '2026-05-25 23:45:03'),
('15c25c2b-baf2-42af-90ea-99a894c24771', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/12\"}', NULL, '2026-05-29 01:37:50', '2026-05-29 01:37:50'),
('1786d438-b59a-4766-a4d7-f890f6cf9ce3', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"IPA\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/3\"}', NULL, '2026-05-15 20:02:18', '2026-05-15 20:02:18'),
('182766df-798a-49df-b093-c4c8671152d0', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/11\"}', NULL, '2026-05-29 00:02:10', '2026-05-29 00:02:10'),
('1a5a2d1e-7cd6-43ff-9aef-c2e91a2f15ab', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Jadwal terbit naskah telah ditentukan.\",\"message\":\"Admin menetapkan jadwal terbit October 2030 untuk naskah Anda.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/riwayat-naskah\"}', NULL, '2026-05-25 21:33:26', '2026-05-25 21:33:26'),
('1b39a346-4cc9-4137-8f88-54cf71009263', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/20\"}', NULL, '2026-06-06 10:04:19', '2026-06-06 10:04:19'),
('1f7db79b-adb0-4b85-9569-eb87ed17a328', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Vocabularies\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-05-11 01:14:37', '2026-05-11 01:01:38', '2026-05-11 01:14:37'),
('21426707-2f5f-41a1-a119-187c74e6ef33', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Read the Story and Fun!\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 07:22:53', '2026-06-06 07:22:53'),
('238d7c7c-31cb-4f10-a20b-d548fbaeeb9c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Audible Book\'s Rain\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/14\"}', NULL, '2026-05-28 23:28:54', '2026-05-28 23:28:54'),
('23bf077c-ee06-438c-95c8-8e037fd1bdac', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/20\"}', NULL, '2026-06-06 09:50:46', '2026-06-06 09:50:46'),
('243c8a22-5911-4c32-8098-38ca48e5cda9', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Holiday to Study\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/16\"}', NULL, '2026-05-25 21:24:33', '2026-05-25 21:24:33'),
('25d4c5b3-f5cf-4c55-8424-ffa74cacc239', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Easy English Conversation XII\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-15 20:01:31', '2026-05-15 20:01:31'),
('2717a200-2753-4efb-b84c-40e20d3069cd', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/10\"}', NULL, '2026-05-24 23:37:21', '2026-05-24 23:37:21'),
('2762a773-ecde-46f1-9ff8-ea3cadc92109', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/7\"}', '2026-04-19 07:05:02', '2026-04-19 07:04:40', '2026-04-19 07:05:02'),
('2a22588e-bec6-4ad7-90ff-4c5287d17969', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Let\'s Go Series\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-13 22:23:50', '2026-05-13 22:23:50'),
('2efbbfab-42c0-4690-92c4-4984ddb4c2d3', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Holiday to Study\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/16\"}', NULL, '2026-05-25 21:25:20', '2026-05-25 21:25:20'),
('2fa20f3e-a235-4e76-902f-08d4f863ef7d', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Story Telling\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 07:27:06', '2026-06-06 07:27:06'),
('301ba96e-6239-4163-80ed-0ba80d801a0e', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"English for Changes\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-13 22:52:19', '2026-05-13 22:52:19'),
('31f41989-7569-4423-9bc6-68e362a142ef', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"IPA\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/13\"}', NULL, '2026-05-17 07:49:58', '2026-05-17 07:49:58'),
('328e7d43-4e82-4396-9774-3d4f5c873f0c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/8\"}', NULL, '2026-05-13 23:27:53', '2026-05-13 23:27:53'),
('358fb12f-e50e-4bdc-a78a-d6431f4f9f16', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Jadwal terbit naskah telah ditentukan.\",\"message\":\"Admin menetapkan jadwal terbit June 2027 untuk naskah Anda.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/riwayat-naskah\"}', NULL, '2026-05-21 20:27:38', '2026-05-21 20:27:38'),
('3741d965-8fbe-468e-ae7a-0edb84356250', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/16\"}', NULL, '2026-05-25 21:16:18', '2026-05-25 21:16:18'),
('3a16bfe2-71ab-48ed-9234-76ea49407b07', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/8\"}', NULL, '2026-05-13 23:09:22', '2026-05-13 23:09:22'),
('3b678246-bbe8-4597-9ffa-dd2417c92260', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"English for Changes\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/7\"}', '2026-04-19 08:04:41', '2026-04-19 07:06:25', '2026-04-19 08:04:41'),
('3dbecbc5-91e6-4dc4-a9d5-498035f3b0a2', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/16\"}', '2026-05-28 22:51:42', '2026-05-25 21:22:00', '2026-05-28 22:51:42'),
('405641cb-249e-475d-833b-885bba17d535', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Vocabularies\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/6\"}', '2026-05-11 21:02:32', '2026-05-11 01:01:38', '2026-05-11 21:02:32'),
('4331a8b1-a4af-451d-96f6-e6f0525c6a01', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Open The World\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:01:18', '2026-05-25 21:01:18'),
('4c4c4293-0e0d-4e93-9c92-cdc29f74206a', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:24:33', '2026-05-25 21:24:33'),
('5092385e-dacb-4ff6-8294-39033cd755eb', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/12\"}', NULL, '2026-05-16 00:38:18', '2026-05-16 00:38:18'),
('550bef4d-ea04-4e78-9879-80aadb167815', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/13\"}', NULL, '2026-05-17 07:34:16', '2026-05-17 07:34:16'),
('58d6722f-2688-4adf-ae04-ebd9352be3fc', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Holiday to Study\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:14:50', '2026-05-25 21:14:50'),
('5b7954f0-74d3-43a6-b976-79d3505e6d00', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:26:10', '2026-05-25 21:26:10'),
('5b7dce0d-2c64-4a05-a9a8-1dd8139f71dc', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Let\'s Go Series\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/5\"}', '2026-04-19 04:43:35', '2026-04-19 04:43:11', '2026-04-19 04:43:35'),
('5c13a01c-92e1-4e11-b8b4-693eb9646e48', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/8\"}', NULL, '2026-05-13 23:21:35', '2026-05-13 23:21:35'),
('5d8dc6c6-a44c-40fd-9475-c4500c9f7f39', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia Kelas XI & XII\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/9\"}', NULL, '2026-05-21 20:57:58', '2026-05-21 20:57:58'),
('5e842367-4186-4fe0-9552-fe2b8221d2fe', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"IPA\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/13\"}', NULL, '2026-05-17 07:46:31', '2026-05-17 07:46:31'),
('5e91343a-e279-496b-931f-85b82f19a286', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/14\"}', NULL, '2026-05-28 23:29:08', '2026-05-28 23:29:08'),
('61aa79a2-9aec-478a-b8bd-ace8a890e474', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Let\'s Go Series\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-04-19 04:40:27', '2026-04-19 04:38:59', '2026-04-19 04:40:27'),
('6332e7a9-0e79-4383-a927-61faf7c164ca', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 10:04:19', '2026-06-06 10:04:19'),
('65b0103f-2e6d-49f7-a844-73c6eacd3239', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Try On the Book\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-13 23:03:43', '2026-05-13 23:03:43'),
('65e6364f-5958-453a-ba16-7f7c459ca254', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/6\"}', '2026-04-19 05:20:59', '2026-04-19 05:09:30', '2026-04-19 05:20:59'),
('66073cac-c11f-4b9a-9ca3-418198c09f50', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Open The World Class XII\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 23:45:03', '2026-05-25 23:45:03'),
('66a62dcd-e6dd-4d80-b1df-647d0afafe9e', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Open The World\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:01:18', '2026-05-25 21:01:18'),
('6dc6a14b-1d35-44d6-802c-ae3ffcfbd368', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/12\"}', NULL, '2026-05-29 01:35:25', '2026-05-29 01:35:25'),
('6df71975-d3bc-4e0b-be61-36b6f6189cca', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/9\"}', '2026-05-28 22:51:42', '2026-05-21 20:54:01', '2026-05-28 22:51:42'),
('6f40d5de-efec-4fac-8b78-22fb7902250a', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/12\"}', NULL, '2026-05-29 00:54:18', '2026-05-29 00:54:18'),
('7443b715-7448-4a2f-9513-2a89049fe08c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Work In Progress\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-16 00:01:24', '2026-05-16 00:01:24'),
('76a16c54-c93d-4f21-abbd-900677cb959d', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Read the Story and Fun!\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 07:22:53', '2026-06-06 07:22:53'),
('7843a803-ec0d-4064-ad00-22e22daf98b5', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/16\"}', NULL, '2026-05-25 21:21:43', '2026-05-25 21:21:43'),
('7b67b7c6-1591-45a7-b403-3ca3c7e29a47', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Open The World Class XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/17\"}', NULL, '2026-05-25 23:49:05', '2026-05-25 23:49:05'),
('7bd9dce2-2aa7-40df-92a5-623a451f9aed', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/9\"}', NULL, '2026-05-15 19:54:51', '2026-05-15 19:54:51'),
('7cbd1420-d891-4741-9d90-6ebafa8242f9', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Hafalan Materi Ilmu Pengetahuan Alam\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/21\"}', NULL, '2026-06-06 09:48:39', '2026-06-06 09:48:39'),
('7dbc2383-b100-4161-8167-19e5ee198d36', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 19:40:07', '2026-05-25 19:40:07'),
('7e775d6e-480a-43fa-bd2a-ef2f8199f11c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Open The World Class XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/17\"}', NULL, '2026-05-25 23:52:26', '2026-05-25 23:52:26'),
('84060931-2c3a-4b2f-a4b1-1a753035f0d7', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Hafalan Materi Ilmu Pengetahuan Alam\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 09:37:39', '2026-06-06 09:37:39'),
('867c8516-760c-4b7d-af98-29f29496276d', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Holiday to Study\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:14:50', '2026-05-25 21:14:50'),
('880902b0-5f5d-438d-b5d3-956ba5c0352f', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 07:38:36', '2026-06-06 07:38:36'),
('88a782cb-945a-4543-b81b-edc52e4344d8', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/8\"}', '2026-05-28 22:51:42', '2026-05-25 00:10:56', '2026-05-28 22:51:42'),
('890455dc-cdd6-4abe-8ee8-96d28ebe2373', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Seri IPA Biologi, Fisika, & Kimia Kelas XI & XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/9\"}', '2026-05-15 20:00:12', '2026-05-15 19:56:17', '2026-05-15 20:00:12'),
('8afb7791-eaa1-42c4-8335-97bea61ae59a', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"English for Changes\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/7\"}', '2026-05-15 20:00:12', '2026-05-13 22:52:19', '2026-05-15 20:00:12'),
('8dbe02c4-c0ce-434a-aca1-79d027825724', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Jadwal terbit naskah telah ditentukan.\",\"message\":\"Admin menetapkan jadwal terbit November 2026 untuk naskah Anda.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/riwayat-naskah\"}', NULL, '2026-05-29 00:22:02', '2026-05-29 00:22:02'),
('8eac159a-1260-417f-bc5d-5f96b4131c20', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/3\"}', '2026-05-28 22:51:42', '2026-05-25 20:19:33', '2026-05-28 22:51:42'),
('915c45b5-feb1-431c-96f1-a2b7bbdaba9d', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Work In Progress\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-16 00:01:24', '2026-05-16 00:01:24'),
('91e91bb5-d13a-4a2e-b2cc-059eeca7897c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-14 23:23:39', '2026-05-14 23:23:39'),
('92540b7e-7cee-431e-8d5f-6009f59c0134', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/11\"}', NULL, '2026-05-28 23:47:13', '2026-05-28 23:47:13'),
('9308629c-2a92-4a79-b404-d99cb7e2f3b0', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Easy English Conversation XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-06-06 09:29:41', '2026-05-29 00:03:13', '2026-06-06 09:29:41'),
('95a6f614-472d-4a4d-b3b7-9fff624ef7e4', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/7\"}', '2026-05-13 23:53:16', '2026-05-13 22:51:45', '2026-05-13 23:53:16'),
('95c5a35c-a46a-4e99-8545-090c4a47cb29', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:26:10', '2026-05-25 21:26:10'),
('976da484-ea89-4ba1-8f68-3949cdf69dea', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 07:38:36', '2026-06-06 07:38:36'),
('98ca2ee8-a4f4-4a1d-827f-e2472218383b', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/20\"}', NULL, '2026-06-06 09:40:38', '2026-06-06 09:40:38'),
('9914ac99-7ea2-46a9-9386-e182b4037767', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/20\"}', NULL, '2026-06-06 09:49:41', '2026-06-06 09:49:41'),
('9c10ef78-aa35-4a45-846b-d8fd1222b230', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"IPA\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/13\"}', NULL, '2026-05-17 07:42:33', '2026-05-17 07:42:33'),
('a1c048be-97f3-4502-afe9-554930907b68', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Let\'s Go Series\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/5\"}', '2026-04-19 04:46:01', '2026-04-19 04:45:48', '2026-04-19 04:46:01'),
('a391db9e-3193-4c0e-8f8b-9061d85fa61f', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Easy English Conversation XII\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/11\"}', NULL, '2026-05-29 00:03:13', '2026-05-29 00:03:13'),
('a3a2c771-fc41-41dd-a650-48766e4bde53', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Story Telling\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 07:27:06', '2026-06-06 07:27:06'),
('a6f69a5c-63c6-48a3-9d5e-f21c54f642d8', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Let\'s Go Series\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/5\"}', '2026-05-15 20:00:12', '2026-05-13 22:23:50', '2026-05-15 20:00:12'),
('a8094408-e85d-44e4-a0fa-de23e4343928', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/13\"}', '2026-05-28 22:51:42', '2026-05-21 20:16:45', '2026-05-28 22:51:42'),
('a94882dc-f670-4c80-bd33-12e14b7b0cf1', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Vocabularies\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-11 01:01:38', '2026-05-11 01:01:38'),
('ab37020f-f5d2-4d27-890a-b59a104f9fbc', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 20, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/8\"}', NULL, '2026-05-13 23:07:59', '2026-05-13 23:07:59'),
('ac86f0c0-d061-4136-aa0a-59ea8626b420', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/6\"}', '2026-05-13 23:53:16', '2026-04-19 05:19:58', '2026-05-13 23:53:16'),
('ae4403d4-b5e9-4ff9-bcaa-656c117f9042', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Audible Book\'s Rain\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 01:21:22', '2026-05-25 01:21:22'),
('af1dbe21-6d79-418f-87e4-7dbfd946ce0c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"IPA\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-17 07:21:54', '2026-05-17 07:21:54'),
('af33eb8b-fe38-4cf3-acd9-ccc347b6f763', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/10\"}', NULL, '2026-05-14 23:25:03', '2026-05-14 23:25:03'),
('b01258b9-ad99-4179-9b91-e4f8cda01eb1', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"IPA\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-21 20:22:57', '2026-05-21 20:22:57'),
('b1a63580-b939-454f-9baf-ff7d47b47799', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/12\"}', NULL, '2026-05-29 01:34:34', '2026-05-29 01:34:34'),
('b226adde-7a87-4795-aae4-5ee4bb7302a0', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 20, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/14\"}', NULL, '2026-05-25 01:50:52', '2026-05-25 01:50:52'),
('b3b1be89-91ab-4bed-b222-de12ce8e9416', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Hafalan Materi Ilmu Pengetahuan Alam\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 09:37:39', '2026-06-06 09:37:39'),
('b4a76d18-c733-4be8-b5f4-f9e95499b783', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/12\"}', NULL, '2026-05-29 01:33:40', '2026-05-29 01:33:40'),
('b4be6d06-74e0-4d53-b211-8ed36cba52c4', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/12\"}', NULL, '2026-05-29 01:36:20', '2026-05-29 01:36:20'),
('b52f218f-05d0-4cf3-b685-c6db26b0bf85', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-24 23:37:21', '2026-05-24 23:37:21'),
('b60bc7e3-7439-40b5-84e1-fd7ca85e017d', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Jadwal penerbitan naskah telah diperbarui.\",\"message\":\"Admin memperbarui jadwal terbit June 2031 untuk naskah Anda.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/riwayat-naskah\"}', NULL, '2026-06-07 06:33:33', '2026-06-07 06:33:33'),
('b6f36533-56f8-4184-87c8-8c4e3b64d5af', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/17\"}', '2026-05-28 22:51:42', '2026-05-25 23:53:21', '2026-05-28 22:51:42'),
('b790c9fb-f8fa-45d7-ad38-5435b1f6b246', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Let\'s Go Series\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-05-13 23:50:01', '2026-05-13 22:23:50', '2026-05-13 23:50:01'),
('bee1149c-1c1c-4b21-a70a-d343ab8b32b2', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Seri IPA Biologi, Fisika, & Kimia Kelas XI & XII\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-14 23:22:34', '2026-05-14 23:22:34'),
('bf260275-89f4-4d88-9808-d5a7cb8363a3', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia Kelas XI & XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-21 20:57:58', '2026-05-21 20:57:58'),
('bf960b55-b4ec-48b8-a7d3-0cc148eceeca', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"English for Changes\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-05-13 23:50:01', '2026-05-13 22:52:19', '2026-05-13 23:50:01'),
('bfb12d43-a52b-4653-a8be-1000cae393de', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 8, '{\"title\":\"Anda menerima tugas layout naskah.\",\"message\":\"Editor telah mengirim naskah untuk proses layout.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/layouter\\/naskah\\/10\"}', '2026-05-28 22:51:42', '2026-05-14 23:31:02', '2026-05-28 22:51:42'),
('c009b029-7f2a-4877-b057-2ccd310ca3a3', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 20, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/8\"}', NULL, '2026-05-13 23:23:42', '2026-05-13 23:23:42'),
('c1ac8f3f-130f-4d97-80e6-219850510630', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/12\"}', NULL, '2026-05-16 00:37:55', '2026-05-16 00:37:55'),
('c1eecd7f-4e83-4ace-8a0c-a9000ac0059c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Vocabularies\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/6\"}', '2026-04-19 05:20:28', '2026-04-19 05:10:08', '2026-04-19 05:20:28'),
('c20f680d-b46d-4dbb-a09d-0835757ef077', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/17\"}', NULL, '2026-05-25 23:47:20', '2026-05-25 23:47:20'),
('c35c6376-34d4-4f06-9ef9-09c009dca699', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Vocabularies\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-04-19 05:09:12', '2026-04-19 05:09:12'),
('c5810044-d239-4c72-b8d4-3cf71e8ec559', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/12\"}', NULL, '2026-05-16 00:10:20', '2026-05-16 00:10:20'),
('c7122320-c7b9-4637-8bc3-3f6c704d2d63', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"IPA\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/13\"}', NULL, '2026-05-21 20:22:57', '2026-05-21 20:22:57'),
('c7779469-0336-407a-836a-7a073d4ebd40', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/8\"}', NULL, '2026-05-13 23:48:39', '2026-05-13 23:48:39'),
('c7862647-1b36-4ec1-9184-971b770135bf', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:25:20', '2026-05-25 21:25:20'),
('c7b109de-cbe2-4b97-9852-0b73baf16b66', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Audible Book\'s Rain\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 01:21:21', '2026-05-25 01:21:21'),
('c8b291e1-c854-4a3d-beb9-edc9268ade66', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Vocabularies\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-04-19 05:09:26', '2026-04-19 05:09:12', '2026-04-19 05:09:26'),
('c9e8bad2-9943-4ed1-8b24-9e99a5c9257a', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Try On the Book\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-05-13 23:50:01', '2026-05-13 23:03:43', '2026-05-13 23:50:01'),
('cd947bf1-e667-4920-9a4d-30e30086aab6', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Holiday to Study\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/16\"}', NULL, '2026-05-25 21:26:10', '2026-05-25 21:26:10'),
('d17e612f-6772-42dd-ab38-85f0f4ba1866', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Open The World Class XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/17\"}', NULL, '2026-05-25 23:50:45', '2026-05-25 23:50:45'),
('d195b2db-9c8d-417a-aaf1-c9923828ed41', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Jadwal terbit naskah telah ditentukan.\",\"message\":\"Admin menetapkan jadwal terbit August 2026 untuk naskah Anda.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/riwayat-naskah\"}', NULL, '2026-05-25 23:56:41', '2026-05-25 23:56:41'),
('d20bb0e6-d399-49bd-a151-a49bf2608714', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/10\"}', '2026-05-15 20:00:12', '2026-05-14 23:27:31', '2026-05-15 20:00:12'),
('d2efe4a4-8a33-4cf3-a760-6f5ebf269210', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/20\"}', '2026-06-06 09:52:47', '2026-06-06 09:41:36', '2026-06-06 09:52:47'),
('d3676097-1df0-45f9-b621-bb888f7ef2e4', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 20, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/8\"}', NULL, '2026-05-13 23:10:18', '2026-05-13 23:10:18'),
('d40bda63-27cc-4a0b-818d-3bc7f347da11', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Let\'s Go Series\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-04-19 04:38:59', '2026-04-19 04:38:59'),
('d55177b0-4668-4bf6-8d55-5f04a864137d', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 20, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Audible Book\'s Rain\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/14\"}', NULL, '2026-05-28 23:28:30', '2026-05-28 23:28:30'),
('d7812881-11a3-4858-b389-ad6507fe0e8b', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Jadwal terbit naskah telah ditentukan.\",\"message\":\"Admin menetapkan jadwal terbit June 2028 untuk naskah Anda.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/riwayat-naskah\"}', NULL, '2026-05-25 19:43:54', '2026-05-25 19:43:54'),
('d95d8bd5-eaff-477f-b897-6e98085f7e66', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Work In Progress\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/12\"}', NULL, '2026-05-29 01:37:11', '2026-05-29 01:37:11'),
('dca1be06-9ee3-4e60-ae36-09e92bb46f77', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"IPA\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-21 20:22:57', '2026-05-21 20:22:57'),
('de2b0edd-4196-49ba-9b94-e5f432e4dc53', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-14 23:23:39', '2026-05-14 23:23:39'),
('de7cb3c1-ba27-4f7b-8761-42552b99f435', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-24 23:37:21', '2026-05-24 23:37:21'),
('e36ef62e-5b3e-41c3-bd24-c4ea161c21e6', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/12\"}', NULL, '2026-05-16 00:02:22', '2026-05-16 00:02:22'),
('e4213754-356a-428c-ad48-5331c977fe26', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Open The World Class XII\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/17\"}', NULL, '2026-05-25 23:54:47', '2026-05-25 23:54:47'),
('e8199bb4-9f79-4523-8d19-f85ed563ca4e', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Easy English Conversation XII\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-15 20:01:31', '2026-05-15 20:01:31'),
('eb72a595-a8bf-4b56-a59c-7b1d585ba5d3', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"English for Changes\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', '2026-04-19 07:04:32', '2026-04-19 07:03:41', '2026-04-19 07:04:32');
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('ecd0cd7f-833f-499e-97b2-bcd27509fd38', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 9, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/16\"}', NULL, '2026-05-25 21:20:16', '2026-05-25 21:20:16'),
('ed611612-a159-4e73-b617-c039a0e956c9', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Jadwal terbit naskah telah ditentukan.\",\"message\":\"Admin menetapkan jadwal terbit November 2031 untuk naskah Anda.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/riwayat-naskah\"}', NULL, '2026-06-06 09:27:18', '2026-06-06 09:27:18'),
('edf690f5-6287-42aa-941a-783ed5d1fcd0', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"IPA\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-17 07:21:54', '2026-05-17 07:21:54'),
('f04127a0-a089-4613-aaf5-49f75b137dc8', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Hasil layout untuk naskah \\\"Try On the Book\\\" telah diunggah.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/8\"}', NULL, '2026-05-25 19:40:07', '2026-05-25 19:40:07'),
('f0d993d5-3401-4bf5-830b-037214e2f382', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"English for Changes\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/7\"}', '2026-05-15 20:00:12', '2026-05-13 22:47:01', '2026-05-15 20:00:12'),
('f1468249-3fb3-4bae-8d5e-81f39bb03094', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Open The World Class XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 23:54:47', '2026-05-25 23:54:47'),
('f1b49aab-e5f5-4261-b569-a0aebd01324a', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 1, '{\"title\":\"Naskah baru menunggu review.\",\"message\":\"Naskah \\\"Seri IPA Biologi, Fisika, & Kimia Kelas XI & XII\\\" baru saja diunggah penulis dan menunggu review admin.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-14 23:22:34', '2026-05-14 23:22:34'),
('f1d7129a-449b-4dcb-9cd9-8d9bf10375a6', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Easy English Conversation XII\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/11\"}', NULL, '2026-05-28 23:48:25', '2026-05-28 23:48:25'),
('f36cbf54-ba36-48f9-8b8c-4f0535a6eef9', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Seri IPA Biologi, Fisika, & Kimia\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/10\"}', '2026-05-15 20:00:12', '2026-05-14 23:29:13', '2026-05-15 20:00:12'),
('f5b74bf0-36a6-4d42-8dff-7d2d736693a0', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 21:24:33', '2026-05-25 21:24:33'),
('f6035dbd-996b-4e4a-8909-f3b23fb62883', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 7, '{\"title\":\"Anda menerima tugas review naskah baru.\",\"message\":\"Admin telah mengirim naskah untuk Anda review.\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/21\"}', '2026-06-06 09:52:41', '2026-06-06 09:46:46', '2026-06-06 09:52:41'),
('f7fc7b65-6576-465f-ba9e-d13a6768a290', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Senang Belajar Ilmu Pengetahuan Alam\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-06-06 10:04:19', '2026-06-06 10:04:19'),
('f8995e2a-7a4e-4eee-ad3f-a6ca724ee027', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 4, '{\"title\":\"Naskah telah diterima editor.\",\"message\":\"Editor telah menerima naskah \\\"Let\'s Go Series\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/5\"}', '2026-04-19 04:46:47', '2026-04-19 04:46:28', '2026-04-19 04:46:47'),
('fc0ad9e0-d48a-49e6-b357-6c87134be222', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 6, '{\"title\":\"Naskah memerlukan revisi.\",\"message\":\"Editor memberikan catatan revisi untuk naskah \\\"Holiday to Study\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/penulis\\/naskah\\/16\"}', NULL, '2026-05-25 21:18:12', '2026-05-25 21:18:12'),
('fc0c6cee-f82d-4b5b-bda2-fc6de0cba80c', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 5, '{\"title\":\"Layout naskah telah selesai.\",\"message\":\"Layouter telah mengunggah hasil layout untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/naskah\"}', NULL, '2026-05-25 19:40:07', '2026-05-25 19:40:07'),
('fcd171b3-643c-4c6f-9d20-c5e3d32fb8bd', 'App\\Notifications\\WorkflowNotification', 'App\\Models\\User', 20, '{\"title\":\"Penulis telah mengirim revisi naskah.\",\"message\":\"Penulis telah mengunggah revisi untuk naskah \\\"Try On the Book\\\".\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/editor\\/naskah\\/8\"}', NULL, '2026-05-13 23:28:15', '2026-05-13 23:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('hafizhahsalma1@gmail.com', '$2y$12$dDE3tW80qxz78lOGF8EvVeVNbIqdTJg7hwJaoQwqRSsVFZOmUwpdq', '2026-06-06 06:41:12');

-- --------------------------------------------------------

--
-- Table structure for table `penulis`
--

CREATE TABLE `penulis` (
  `id_penulis` bigint UNSIGNED NOT NULL,
  `kode_penulis` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profesi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan_pendidikan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penulis`
--

INSERT INTO `penulis` (`id_penulis`, `kode_penulis`, `id_user`, `nama_lengkap`, `alamat`, `profesi`, `jurusan_pendidikan`, `no_hp`) VALUES
(1, 'P-IPA-001', 4, 'Penulis Satu', 'Sukoharjo', 'Guru', 'Biologi', '08511223456'),
(2, 'P-SING-002', 6, 'Penulis Tiga', 'Laweyan, Surakarta', 'Dosen', 'Sastra Inggris', '08964684469'),
(3, 'P-TINF-010', 10, 'Penulis Dua', 'Surakarta', 'Dosen', 'Teknik Informatika', '081234561010'),
(13, 'P-SI-013', 21, 'Putri Purbasari', 'Semarang', 'Dosen', 'Sastra Indonesia', '081234568856');

-- --------------------------------------------------------

--
-- Table structure for table `revisi`
--

CREATE TABLE `revisi` (
  `id_revisi` bigint UNSIGNED NOT NULL,
  `id_naskah` bigint UNSIGNED NOT NULL,
  `id_editor` bigint UNSIGNED NOT NULL,
  `id_penulis` bigint UNSIGNED NOT NULL,
  `catatan_editor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan_penulis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_review_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_file_review_asli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_review_mime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_revisi` datetime NOT NULL,
  `status_revisi` enum('Pending Review','Perbaikan Dikirim','Ditolak','Revisi','Diterima') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending Review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `revisi`
--

INSERT INTO `revisi` (`id_revisi`, `id_naskah`, `id_editor`, `id_penulis`, `catatan_editor`, `catatan_penulis`, `file_review_path`, `nama_file_review_asli`, `file_review_mime`, `tanggal_revisi`, `status_revisi`) VALUES
(1, 4, 2, 1, 'Ga jelas sumpah', 'Sumpah, ini udah sejelas itu gausah denial', NULL, NULL, NULL, '2026-04-17 14:56:17', 'Revisi'),
(2, 4, 2, 1, 'Iya, ini udah ga denial lagi kok', NULL, NULL, NULL, NULL, '2026-04-17 15:07:03', 'Diterima'),
(3, 5, 1, 1, 'Take a Break, Laila', 'Done. Saya tambahkan beberapa catatan dan vocab yang memang mungkin kamu butuhkan', NULL, NULL, NULL, '2026-04-19 11:45:48', 'Revisi'),
(4, 5, 1, 1, 'Ini adalah versi final kami', NULL, NULL, NULL, NULL, '2026-04-19 11:46:28', 'Diterima'),
(5, 6, 1, 1, 'Diterima tanpa revisi! Applause', NULL, NULL, NULL, NULL, '2026-04-19 12:10:07', 'Diterima'),
(6, 7, 1, 1, 'Sepertinya ada beberapa kata yang harus kamu ulas lagi. Saya menemukan bebrapa kata ganti dan arti nya masih tidak sesuai', NULL, NULL, NULL, NULL, '2026-04-19 14:06:25', 'Revisi'),
(7, 7, 1, 1, 'Ok', NULL, NULL, NULL, NULL, '2026-05-14 05:47:00', 'Diterima'),
(8, 8, 3, 2, 'Tolong dicek lagi ya, masih banyak typo!', 'Dijamin Bagus, Bro', NULL, NULL, NULL, '2026-05-14 06:10:18', 'Revisi'),
(9, 8, 3, 2, 'Belum', 'Udah plis', NULL, NULL, NULL, '2026-05-14 06:23:42', 'Perbaikan Dikirim'),
(10, 8, 3, 2, 'Lagio sumpahj', NULL, NULL, NULL, NULL, '2026-05-14 06:25:09', 'Perbaikan Dikirim'),
(11, 8, 3, 2, 'lagi', 'noh', NULL, NULL, NULL, '2026-05-14 06:28:15', 'Perbaikan Dikirim'),
(12, 8, 3, 2, 'Good', NULL, NULL, NULL, NULL, '2026-05-14 06:48:39', 'Diterima'),
(13, 10, 2, 1, 'Masih ada yang salah ya', 'Sudah', NULL, NULL, NULL, '2026-05-15 06:28:36', 'Perbaikan Dikirim'),
(14, 10, 2, 1, 'Okee', NULL, NULL, NULL, NULL, '2026-05-15 06:29:13', 'Diterima'),
(15, 9, 2, 1, 'Good', NULL, NULL, NULL, NULL, '2026-05-16 02:56:17', 'Diterima'),
(16, 3, 2, 1, 'Good', NULL, NULL, NULL, NULL, '2026-05-16 03:02:18', 'Diterima'),
(17, 12, 1, 2, 'Revisi', 'Same', NULL, NULL, NULL, '2026-05-16 07:37:55', 'Perbaikan Dikirim'),
(18, 12, 1, 2, 'Lagi', 'Mohon ditinjau kembali', NULL, NULL, NULL, '2026-05-29 07:54:18', 'Perbaikan Dikirim'),
(19, 13, 2, 1, 'Revisi', 'Sudah', NULL, NULL, NULL, '2026-05-17 14:46:31', 'Perbaikan Dikirim'),
(20, 13, 2, 1, 'Okee', NULL, NULL, NULL, NULL, '2026-05-17 14:49:57', 'Diterima'),
(21, 16, 2, 2, 'Naskah telah direview. Terdapat beberapa bagian yang perlu diperbaiki, terutama pada struktur materi, penulisan bahasa. Mohon lakukan revisi sesuai catatan editor sebelum diproses ke tahap berikutnya.', 'Revisi naskah telah diperbarui sesuai catatan dan masukan editor sebelumnya. Perbaikan dilakukan pada bagian materi, penulisan, dan struktur isi. Mohon dilakukan pengecekan kembali untuk proses review tahap selanjutnya.', NULL, NULL, NULL, '2026-05-26 04:20:14', 'Perbaikan Dikirim'),
(22, 16, 2, 2, 'Naskah telah direview dan dinyatakan sesuai untuk diproses ke tahap berikutnya.', NULL, NULL, NULL, NULL, '2026-05-26 04:21:43', 'Diterima'),
(23, 17, 2, 2, 'Perhatikan penulisan kalimat dan ejaan', 'Perbaikan sesuai dengan instruksi', NULL, NULL, NULL, '2026-05-26 06:50:43', 'Perbaikan Dikirim'),
(24, 17, 2, 2, 'Naskah Diterima', NULL, NULL, NULL, NULL, '2026-05-26 06:52:26', 'Diterima'),
(25, 14, 3, 2, 'Berikan catatan yang lebih lengkap', 'Sudah diperbaiki. Mohon ditinjau kembali', NULL, NULL, NULL, '2026-05-29 06:28:29', 'Perbaikan Dikirim'),
(26, 14, 3, 2, 'Diterima', NULL, NULL, NULL, NULL, '2026-05-29 06:28:54', 'Diterima'),
(27, 11, 2, 1, 'Good', NULL, NULL, NULL, NULL, '2026-05-29 06:48:25', 'Diterima'),
(28, 12, 1, 2, 'Maih ada yang perlu dibenahi', 'Baik', 'review-editor/LxsIjUVjYqMzSdyrpMF5fX6KAEmb6zRuKTF9WyPb.docx', 'Naskah Buku Review Editor.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-05-29 08:33:40', 'Perbaikan Dikirim'),
(29, 12, 1, 2, 'Buatlah seperti bla bla bla', 'Ini yang terakhir', 'review-editor/864HrzRf5PLvk6bCBVf1SGmRJs7VXgiAklEdl1TY.docx', 'Naskah Buku Review Editor.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-05-29 08:35:25', 'Perbaikan Dikirim'),
(30, 12, 1, 2, 'Lihat file lampiran sebelumnya, masih ada yang tidak sesuai', 'Baik, sudah saya perbaiki menurut file lampiran terakhir', NULL, NULL, NULL, '2026-05-29 08:37:11', 'Perbaikan Dikirim'),
(31, 12, 1, 2, 'DITERIMA', NULL, NULL, NULL, NULL, '2026-05-29 08:37:50', 'Diterima'),
(32, 20, 1, 1, 'Berikan penjelasan untuk kalimat yang saya tandai', 'Perbaikan sesuai dengan arahan', 'review-editor/Ky8ZD05CSToZLwESsQKo3lTwc9cI7HwteSwPzuRX.docx', 'Naskah Buku Review Editor.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-06-06 16:41:36', 'Perbaikan Dikirim'),
(33, 21, 1, 1, 'Perbaiki sesuai arahan yang ada di dalam file', NULL, 'review-editor/JlweHUYo3dzYwqQuRDTt79dmrmoooI5vvcvegf1h.docx', 'Naskah Buku Review Editor.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '2026-06-06 16:48:38', 'Revisi'),
(34, 20, 1, 1, 'Naskah sudah sesuai, diterima', NULL, NULL, NULL, NULL, '2026-06-06 16:49:41', 'Diterima');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0fefthaqyIZd3Wih6VUIZIHVxDVhZYiWXqkDIeoy', 20, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJPaVBGVFUxWFFZRjBQNTI4a1hGalhjeWgwdjBTMEpteXJjZkJRelVlIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2VkaXRvclwvcGVuZ2F0dXJhbiIsInJvdXRlIjoiZWRpdG9yLnBlbmdhdHVyYW4uaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIwfQ==', 1780935620),
('32nesbqKNZI7uqbhLCbQlPGIa7KbOV4PBZfpgDNM', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ3WFg0S0tpNXM4OXZmOFFuTnBGZWZENUpOUHQxbGpqc0NDc0d6WHlVIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BlbnVsaXNcL3Jpd2F5YXQtbmFza2FoXC8xMCIsInJvdXRlIjoicGVudWxpcy5yaXdheWF0LW5hc2thaC5zaG93In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjo0fQ==', 1780839330),
('C67tn9sv5H3KUPuCEj88kWxoiQZb69Hzyu1nOmYM', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI2RHRmeGhqMEZBRDB4MW5TdVNSMDJ1dnBnRmJLQlhxYkJNYldhbDJ4IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BlbnVsaXNcL3Jpd2F5YXQtbmFza2FoIiwicm91dGUiOiJwZW51bGlzLnJpd2F5YXQtbmFza2FoLmluZGV4In0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjo1fQ==', 1780839265);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` bigint UNSIGNED NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `email`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admints@gmail.com', 'Admin1', '$2y$12$3.ycbKW/4a2.IxwTTPKbwuo4HMqVGiCDPIMdx8eB4Ps1Z.b2NsJ3m', 'admin', '2026-04-15 04:07:43', '2026-04-15 04:07:43'),
(4, 'penulists@gmail.com', 'Penulis Satu', '$2y$12$PIhkqddB/CYOKyF.Bfho/.iSVzLxZAUXTEUEpEYiQXYW4G2u0MJ/C', 'penulis', '2026-04-15 20:43:03', '2026-06-08 09:19:25'),
(5, 'admints2@gmail.com', 'Admin2', '$2y$12$BEIeeMbbAC4/FddKRaCzkuDzi6dg6t.Buw0msjCAWUvi/6Rdnt/Aq', 'admin', '2026-04-17 04:57:26', '2026-04-17 04:57:26'),
(6, 'penulists3@gmail.com', 'Penulis Tiga', '$2y$12$Id81etf5j6oXi71yxKD1.OpU/LGgsDvMlQfAkcsR.LUY6PyGmzuMG', 'penulis', '2026-04-17 05:32:07', '2026-06-07 05:19:43'),
(7, 'editorts@gmail.com', 'Editor Satu', '$2y$12$6y5g3MV4rfXGMGQG.0Fze.M0OO9oO8Qd2vSJ3GozkwfX1uc5gqqIy', 'editor', '2026-04-17 05:33:27', '2026-06-07 05:21:23'),
(8, 'layouterts@gmail.com', 'Layouter Satu', '$2y$12$UOThSX7wqOgCpeYXhe2JCOIdNceXQo8CI7aDYhhTkQ003l9.Ze7r.', 'layouter', '2026-04-17 05:34:54', '2026-06-07 05:23:49'),
(9, 'editorts2@gmail.com', 'Editor Dua', '$2y$12$PQt5reD9a7aY7jHnFf.yL.nsultEbblPgzCOqY3Tf7GuUlpeKhXTC', 'editor', '2026-04-17 07:06:59', '2026-06-07 05:21:48'),
(10, 'penulists2@gmail.com', 'Penulis Dua', '$2y$12$9kKDW9o32Fyl.sTnLlyDIeH6z59/7dKrYsWmFWCrludqtv1bQNho2', 'penulis', '2026-05-11 08:33:40', '2026-06-07 05:20:31'),
(20, 'editorts3@gmail.com', 'Editor Tiga', '$2y$12$AaKr09hLRj/745yPezI9IeyMc3pmNueEdpM2vDqtOYh7FCukSZxZq', 'editor', '2026-05-13 23:07:31', '2026-06-08 09:20:20'),
(21, 'purbasariputri89@gmail.com', 'Putri Purbasari', '$2y$12$zc87C9zi/6dwzHYbEJ5Ulu8ifas2RzEgg04QCiCua8UCjnrgry/oG', 'penulis', '2026-06-06 09:24:32', '2026-06-06 09:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `versi_naskah`
--

CREATE TABLE `versi_naskah` (
  `id_versi` bigint UNSIGNED NOT NULL,
  `id_naskah` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_file_asli` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_versi` int NOT NULL,
  `tanggal_upload` datetime NOT NULL,
  `id_user_pengunggah` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `versi_naskah`
--

INSERT INTO `versi_naskah` (`id_versi`, `id_naskah`, `file_path`, `nama_file_asli`, `no_versi`, `tanggal_upload`, `id_user_pengunggah`) VALUES
(1, 4, 'naskah/1ZXO3XjZXJe2QTzl1fJuxq75nCRof07LoWFv156v.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-04-16 03:44:08', 4),
(2, 4, 'naskah/P08e25dhu8wIlnVAsaASUZF7CTpr7zKyrOYRDCr7.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-04-17 14:56:17', 4),
(3, 5, 'naskah/9dhgQAO2PFygJMyc6tDEFirAYrrZLdmfztzzBDHe.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-04-19 11:38:58', 4),
(4, 5, 'naskah/npwdo2lATCRpdh0kenqpzCTggWcJDJRTcccbkm2U.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-04-19 11:45:48', 4),
(5, 6, 'naskah/j1ayQuBCi61HnOgsY6xN5ydC17QigsUqctsVF7q2.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-04-19 12:09:11', 4),
(6, 7, 'naskah/u7LhpdnUVyJLFHqsFz9onObRayAfj62e2yHBeBoY.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-04-19 14:03:41', 4),
(7, 8, 'naskah/nfPqoXP2gvjiwdt6991NjM9xLOYSvOgIueao2IEb.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-14 06:03:43', 6),
(8, 8, 'naskah/9H2kTsRi9oY70UUMUsL4M9VSqvME8vEo8fNQpEUz.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-05-14 06:10:18', 6),
(11, 8, 'naskah/wBX1ZXjQtdrlrvowbgNVpE0BRkqpoA3TZPWZo2Dz.docx', 'Naskah Buku Pelajaran.docx', 3, '2026-05-14 06:23:42', 6),
(12, 8, 'naskah/5BIPS0ZW9wQRLvahwWNzV48m4cM2p68xpIYeSZP0.docx', 'Naskah Buku Pelajaran.docx', 4, '2026-05-14 06:28:15', 6),
(13, 9, 'naskah/q2WqQMODnNN6ZoVN342yIPH9Z3RNTc1Me1JzpRlN.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-15 06:22:33', 4),
(14, 10, 'naskah/2Qz9Jq8VjMH21HyzJvntnpcqY4kbe1rWgtE3V2jk.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-15 06:23:39', 4),
(15, 10, 'naskah/3mEKkkAaGobsfmYPoiOA0ZjIGN6rdH1VviBbPqvt.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-05-15 06:28:36', 4),
(16, 11, 'naskah/ac38utwUTZVDg9uZb0Wa9od1ReRdvgp1vaFpw3u1.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-16 03:01:30', 4),
(17, 12, 'naskah/NzHfk1e3lVdU70cdJq8zuNLCubv3zXrtacKwhYBB.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-16 07:01:20', 6),
(18, 12, 'naskah/rdPY5ZVeSulCoDRj3wsKem8ILyTEruoaPfilVfPk.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-05-16 07:37:55', 6),
(19, 13, 'naskah/KTNW84wc7qhVOT7ScQzLTREnXrt5KRiJJgVkqTwO.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-17 14:21:50', 4),
(20, 13, 'naskah/te81Gx9Ydn0bwCKSuncNLnHe4vn1r4Gkydh7FDje.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-05-17 14:46:31', 4),
(21, 14, 'naskah/fRSN4Fy6Cpz7txr2AhU2Hu3koOvFDE39Jz9dHMNg.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-25 08:21:18', 6),
(23, 16, 'naskah/PUwYGgYlcCkWAo85cO5OTkylOOeoJkngthHaUyaG.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-26 04:14:50', 6),
(24, 16, 'naskah/ERkvFYMEj4qULiBGZ67Xb9Ai3N7ZNRiRZDnOtsH9.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-05-26 04:20:14', 6),
(25, 17, 'naskah/y0iZoxuRcHOD1qNQE66SANAPvREQaPrkD5JRjAHk.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-05-26 06:45:03', 6),
(26, 17, 'naskah/R6NWmZ68K5lybIVSb67fCkIKxn1K2prfGVSGs2OH.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-05-26 06:50:43', 6),
(27, 14, 'naskah/5WMMCIU3k5i2aCrIeYZzTDx5ZRPPBR1xGxSQb6tj.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-05-29 06:28:29', 6),
(28, 12, 'naskah/OltNb0LUATAvvX3zcCFefF8L1HQ6NNA2l8c933e9.docx', 'Naskah Buku Pelajaran.docx', 3, '2026-05-29 07:54:18', 6),
(29, 12, 'naskah/ocNugyaKxmZzeGFg20gFzdGPLKFR0PDlJQCWUAjm.docx', 'Naskah Buku Pelajaran.docx', 4, '2026-05-29 08:33:40', 6),
(30, 12, 'naskah/pJ70EVFB4rx422496wp6hFFdhLEnTeflbviTEEdV.docx', 'Naskah Buku Pelajaran.docx', 5, '2026-05-29 08:35:25', 6),
(31, 12, 'naskah/TfWqhNqXwJs6SOVff4RoJIMLIeaOl873oAv7XItI.docx', 'Naskah Buku Pelajaran.docx', 6, '2026-05-29 08:37:11', 6),
(32, 18, 'naskah/Ar2lsoKpNWSEQ5N44lLF8Dg1lGhlnNlMXdrUwbVQ.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-06-06 14:22:49', 6),
(33, 19, 'naskah/B5IC9PbfY3lU6ag4aKx6Cx782VRcglSkJ8FYonO1.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-06-06 14:27:06', 6),
(34, 20, 'naskah/ZyM1k15xx5m05T5RUhl0ngKBfegUaoZRvOP4Q6FR.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-06-06 14:38:36', 4),
(35, 21, 'naskah/TmEMSWI3QcorR1RcaGodqGqrgnCcF0Imz9zXNo05.docx', 'Naskah Buku Pelajaran.docx', 1, '2026-06-06 16:37:38', 4),
(36, 20, 'naskah/jwKq7lHhRyl61aeKq1UkpOQcaBJt2fxPt65Clpkq.docx', 'Naskah Buku Pelajaran.docx', 2, '2026-06-06 16:41:36', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `admin_id_user_unique` (`id_user`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `editor`
--
ALTER TABLE `editor`
  ADD PRIMARY KEY (`id_editor`),
  ADD UNIQUE KEY `editor_id_user_unique` (`id_user`),
  ADD UNIQUE KEY `editor_kode_editor_unique` (`kode_editor`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jadwal_penerbitan`
--
ALTER TABLE `jadwal_penerbitan`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `jadwal_penerbitan_id_naskah_foreign` (`id_naskah`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `layout`
--
ALTER TABLE `layout`
  ADD PRIMARY KEY (`id_layout`),
  ADD KEY `layout_id_naskah_foreign` (`id_naskah`),
  ADD KEY `layout_id_layouter_foreign` (`id_layouter`),
  ADD KEY `layout_id_penulis_foreign` (`id_penulis`);

--
-- Indexes for table `layouter`
--
ALTER TABLE `layouter`
  ADD PRIMARY KEY (`id_layouter`),
  ADD UNIQUE KEY `layouter_id_user_unique` (`id_user`),
  ADD UNIQUE KEY `layouter_kode_layouter_unique` (`kode_layouter`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `naskah`
--
ALTER TABLE `naskah`
  ADD PRIMARY KEY (`id_naskah`),
  ADD UNIQUE KEY `naskah_kode_naskah_unique` (`kode_naskah`),
  ADD KEY `naskah_id_penulis_foreign` (`id_penulis`),
  ADD KEY `naskah_id_editor_foreign` (`id_editor`),
  ADD KEY `naskah_id_layouter_foreign` (`id_layouter`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `penulis`
--
ALTER TABLE `penulis`
  ADD PRIMARY KEY (`id_penulis`),
  ADD UNIQUE KEY `penulis_id_user_unique` (`id_user`),
  ADD UNIQUE KEY `penulis_kode_penulis_unique` (`kode_penulis`);

--
-- Indexes for table `revisi`
--
ALTER TABLE `revisi`
  ADD PRIMARY KEY (`id_revisi`),
  ADD KEY `revisi_id_naskah_foreign` (`id_naskah`),
  ADD KEY `revisi_id_editor_foreign` (`id_editor`),
  ADD KEY `revisi_id_penulis_foreign` (`id_penulis`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `versi_naskah`
--
ALTER TABLE `versi_naskah`
  ADD PRIMARY KEY (`id_versi`),
  ADD KEY `versi_naskah_id_naskah_foreign` (`id_naskah`),
  ADD KEY `versi_naskah_id_user_pengunggah_foreign` (`id_user_pengunggah`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `editor`
--
ALTER TABLE `editor`
  MODIFY `id_editor` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal_penerbitan`
--
ALTER TABLE `jadwal_penerbitan`
  MODIFY `id_jadwal` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `layout`
--
ALTER TABLE `layout`
  MODIFY `id_layout` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `layouter`
--
ALTER TABLE `layouter`
  MODIFY `id_layouter` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `naskah`
--
ALTER TABLE `naskah`
  MODIFY `id_naskah` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `penulis`
--
ALTER TABLE `penulis`
  MODIFY `id_penulis` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `revisi`
--
ALTER TABLE `revisi`
  MODIFY `id_revisi` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `versi_naskah`
--
ALTER TABLE `versi_naskah`
  MODIFY `id_versi` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `editor`
--
ALTER TABLE `editor`
  ADD CONSTRAINT `editor_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `jadwal_penerbitan`
--
ALTER TABLE `jadwal_penerbitan`
  ADD CONSTRAINT `jadwal_penerbitan_id_naskah_foreign` FOREIGN KEY (`id_naskah`) REFERENCES `naskah` (`id_naskah`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `layout`
--
ALTER TABLE `layout`
  ADD CONSTRAINT `layout_id_layouter_foreign` FOREIGN KEY (`id_layouter`) REFERENCES `layouter` (`id_layouter`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `layout_id_naskah_foreign` FOREIGN KEY (`id_naskah`) REFERENCES `naskah` (`id_naskah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `layout_id_penulis_foreign` FOREIGN KEY (`id_penulis`) REFERENCES `penulis` (`id_penulis`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `layouter`
--
ALTER TABLE `layouter`
  ADD CONSTRAINT `layouter_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `naskah`
--
ALTER TABLE `naskah`
  ADD CONSTRAINT `naskah_id_editor_foreign` FOREIGN KEY (`id_editor`) REFERENCES `editor` (`id_editor`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `naskah_id_layouter_foreign` FOREIGN KEY (`id_layouter`) REFERENCES `layouter` (`id_layouter`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `naskah_id_penulis_foreign` FOREIGN KEY (`id_penulis`) REFERENCES `penulis` (`id_penulis`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `penulis`
--
ALTER TABLE `penulis`
  ADD CONSTRAINT `penulis_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `revisi`
--
ALTER TABLE `revisi`
  ADD CONSTRAINT `revisi_id_editor_foreign` FOREIGN KEY (`id_editor`) REFERENCES `editor` (`id_editor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `revisi_id_naskah_foreign` FOREIGN KEY (`id_naskah`) REFERENCES `naskah` (`id_naskah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `revisi_id_penulis_foreign` FOREIGN KEY (`id_penulis`) REFERENCES `penulis` (`id_penulis`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `versi_naskah`
--
ALTER TABLE `versi_naskah`
  ADD CONSTRAINT `versi_naskah_id_naskah_foreign` FOREIGN KEY (`id_naskah`) REFERENCES `naskah` (`id_naskah`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `versi_naskah_id_user_pengunggah_foreign` FOREIGN KEY (`id_user_pengunggah`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

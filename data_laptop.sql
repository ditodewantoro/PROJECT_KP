-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Feb 2025 pada 05.09
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `data_laptop`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_barang`
--

CREATE TABLE `tbl_barang` (
  `id_barang` int(10) NOT NULL,
  `Merk_tipe` varchar(50) NOT NULL,
  `prosessor` varchar(50) NOT NULL,
  `ram` int(10) NOT NULL,
  `storage` int(10) NOT NULL,
  `jenis_storage` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_barang`
--

INSERT INTO `tbl_barang` (`id_barang`, `Merk_tipe`, `prosessor`, `ram`, `storage`, `jenis_storage`) VALUES
(1, 'lenovo', 'intel core I5', 16, 512, 'SSD'),
(2, 'SAMSUNG', 'Ryzen 9', 4, 218, 'SSD'),
(4, 'Asus', 'intel core I5', 4, 256, 'HDD'),
(7, 'RAZER', 'intel core I9', 16, 1000, 'HDD');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_peminjaman`
--

CREATE TABLE `tbl_peminjaman` (
  `id_pinjam` int(10) NOT NULL,
  `id_barang` int(10) DEFAULT NULL,
  `keperluan_pinjam` text DEFAULT NULL,
  `tgl_pinjam` date DEFAULT NULL,
  `durasi_pinjam` int(11) DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `pic_it` varchar(50) DEFAULT NULL,
  `pic_pinjam` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_peminjaman`
--

INSERT INTO `tbl_peminjaman` (`id_pinjam`, `id_barang`, `keperluan_pinjam`, `tgl_pinjam`, `durasi_pinjam`, `tgl_kembali`, `pic_it`, `pic_pinjam`) VALUES
(11, 7, 'owen', '2025-02-15', 14, '2025-03-01', 'dito', 'ya'),
(15, 2, 'backup data', '2025-02-19', 3, '2025-02-22', 'dito', 'faqih');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_barang`
--
ALTER TABLE `tbl_barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indeks untuk tabel `tbl_peminjaman`
--
ALTER TABLE `tbl_peminjaman`
  ADD PRIMARY KEY (`id_pinjam`),
  ADD KEY `tbl_peminjaman_ibfk_1` (`id_barang`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_peminjaman`
--
ALTER TABLE `tbl_peminjaman`
  MODIFY `id_pinjam` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tbl_peminjaman`
--
ALTER TABLE `tbl_peminjaman`
  ADD CONSTRAINT `tbl_peminjaman_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `tbl_barang` (`id_barang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

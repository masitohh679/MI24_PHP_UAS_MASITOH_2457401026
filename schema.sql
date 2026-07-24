-- NIM   : 2457401026
-- Nama  : Masitoh
-- Kelas : MI24

CREATE DATABASE IF NOT EXISTS katalog_produk;
USE katalog_produk;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
);

INSERT INTO users (username, password) VALUES
('sitinurfazriah', '$2y$12$oGzzB9CwAJyCP1/XoDdofubWeG9.zJXYX0heVWURS9BJoWq.xNBUm');

INSERT INTO kategori (nama_kategori) VALUES
('Baju'), ('Tas'), ('Sepatu'), ('Aksesoris'), ('Kosmetik');

INSERT INTO produk (kode_produk, nama_produk, kategori_id, deskripsi) VALUES
('P01', 'Blouse Wanita Katun', 1, 'Blouse katun adem, cocok untuk kerja maupun santai'),
('P02', 'Tas Selempang Kulit', 2, 'Tas selempang kulit sintetis, muat dompet & handphone'),
('P03', 'Heels Wanita 5cm', 3, 'Sepatu heels nyaman untuk acara formal'),
('P04', 'Anting Mutiara', 4, 'Anting mutiara imitasi, cocok untuk kondangan'),
('P05', 'Lipstik Matte', 5, 'Lipstik matte tahan lama, tidak mudah pudar');

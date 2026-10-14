-- =========================================
-- CSDL WEBSITE BÁN SÁCH ONLINE
-- Import file này vào phpMyAdmin (tab Import)
-- =========================================

CREATE DATABASE IF NOT EXISTS ban_sach CHARACTER SET utf8mb4;
USE ban_sach;

CREATE TABLE tacgia (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_tacgia VARCHAR(100) NOT NULL,
  tieu_su TEXT
);

CREATE TABLE nhaxuatban (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_nxb VARCHAR(100) NOT NULL,
  dia_chi VARCHAR(255)
);

CREATE TABLE theloai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_theloai VARCHAR(100) NOT NULL
);

CREATE TABLE sach (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_sach VARCHAR(255) NOT NULL,
  id_tacgia INT,
  id_nxb INT,
  id_theloai INT,
  gia DECIMAL(12,0) NOT NULL,
  so_luong_ton INT DEFAULT 0,
  mo_ta TEXT,
  hinh_anh VARCHAR(255) DEFAULT 'no-image.png',
  nam_xb YEAR,
  FOREIGN KEY (id_tacgia) REFERENCES tacgia(id) ON DELETE SET NULL,
  FOREIGN KEY (id_nxb) REFERENCES nhaxuatban(id) ON DELETE SET NULL,
  FOREIGN KEY (id_theloai) REFERENCES theloai(id) ON DELETE SET NULL
);

CREATE TABLE taikhoan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ho_ten VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  mat_khau VARCHAR(255) NOT NULL,
  sdt VARCHAR(15),
  dia_chi VARCHAR(255),
  vai_tro TINYINT DEFAULT 0   -- 0 = khách hàng, 1 = admin
);

CREATE TABLE giohang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_taikhoan INT NOT NULL,
  id_sach INT NOT NULL,
  so_luong INT DEFAULT 1,
  FOREIGN KEY (id_taikhoan) REFERENCES taikhoan(id) ON DELETE CASCADE,
  FOREIGN KEY (id_sach) REFERENCES sach(id) ON DELETE CASCADE
);

CREATE TABLE donhang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_taikhoan INT NOT NULL,
  ngay_dat DATETIME DEFAULT CURRENT_TIMESTAMP,
  tong_tien DECIMAL(12,0),
  trang_thai VARCHAR(50) DEFAULT 'Chờ xác nhận',
  dia_chi_giao VARCHAR(255),
  FOREIGN KEY (id_taikhoan) REFERENCES taikhoan(id)
);

CREATE TABLE chitiet_donhang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_donhang INT NOT NULL,
  id_sach INT NOT NULL,
  so_luong INT NOT NULL,
  don_gia DECIMAL(12,0) NOT NULL,
  FOREIGN KEY (id_donhang) REFERENCES donhang(id) ON DELETE CASCADE,
  FOREIGN KEY (id_sach) REFERENCES sach(id)
);

-- Dữ liệu mẫu để test ngay
INSERT INTO tacgia (ten_tacgia) VALUES ('Nguyễn Nhật Ánh'), ('Tô Hoài'), ('Dale Carnegie');
INSERT INTO nhaxuatban (ten_nxb) VALUES ('NXB Trẻ'), ('NXB Kim Đồng'), ('NXB Tổng Hợp');
INSERT INTO theloai (ten_theloai) VALUES ('Văn học'), ('Kỹ năng sống'), ('Thiếu nhi');

INSERT INTO sach (ten_sach, id_tacgia, id_nxb, id_theloai, gia, so_luong_ton, mo_ta, nam_xb) VALUES
('Cho tôi xin một vé đi tuổi thơ', 1, 1, 1, 68000, 50, 'Một cuốn sách nhẹ nhàng về tuổi thơ.', 2008),
('Dế Mèn phiêu lưu ký', 2, 2, 3, 45000, 30, 'Tác phẩm kinh điển dành cho thiếu nhi.', 1941),
('Đắc Nhân Tâm', 3, 3, 2, 88000, 40, 'Cuốn sách kỹ năng sống bán chạy nhất mọi thời đại.', 1936);

-- Tài khoản admin: email admin@bansach.com / mật khẩu: admin123
-- (hash bcrypt thật, PHP password_verify() đọc được bình thường)
INSERT INTO taikhoan (ho_ten, email, mat_khau, vai_tro) VALUES
('Quản trị viên', 'admin@bansach.com', '$2b$10$skC/ok0CRI7MeKc8zsnudO8SdwefzF5aosQXl1vGooyxbxPgkQQmG', 1);

-- =====================================================================
-- seed.sql : dữ liệu mẫu để cả nhóm có dữ liệu thật khi làm giao diện
-- Người phụ trách: Huy
-- Cách dùng: import SAU schema.sql
--
-- Tài khoản mẫu:
--   admin  / Admin@123   (vai trò quản trị)
--   khach1 / Khach@123   (khách hàng)
--   khach2 / Khach@123   (khách hàng)
-- =====================================================================

use web_ban_game;

-- ---------------------------------------------------------------------
-- Người dùng (mật khẩu đã băm bằng bcrypt, tương thích password_verify)
-- ---------------------------------------------------------------------
insert into nguoidung (ten_dangnhap, email, mat_khau, ho_ten, vai_tro) values
('admin',  'admin@webbangame.local',  '$2y$10$Ul35u2r0pVS2.1GwTuqq6u6DVTJ4JZ6UYz1TPYNnUdc0XJLQUC0w6', 'Quản trị viên', 'admin'),
('khach1', 'khach1@webbangame.local', '$2y$10$zB6BmwsEMZmPaiRUXgaiPe8TihFXzfhVE8p0P8QeViFzkoXClStLi', 'Nguyễn Văn An',  'khach'),
('khach2', 'khach2@webbangame.local', '$2y$10$zB6BmwsEMZmPaiRUXgaiPe8TihFXzfhVE8p0P8QeViFzkoXClStLi', 'Trần Thị Bình',  'khach');

-- ---------------------------------------------------------------------
-- Thể loại
-- ---------------------------------------------------------------------
insert into theloai (ten_theloai) values
('Nhập vai (RPG)'),
('Hành động - Phiêu lưu'),
('Chiến thuật'),
('Mô phỏng'),
('Đua xe'),
('Indie');

-- ---------------------------------------------------------------------
-- Game (giá tính bằng đồng; hinh_anh là tên file đặt trong assets/img/games/)
-- ---------------------------------------------------------------------
insert into game (ten_game, gia, mo_ta, hinh_anh, nha_phat_hanh, ngay_phat_hanh, ma_theloai) values
('Elden Ring', 990000,
 'Thế giới mở giả tưởng rộng lớn với lối chơi thử thách, khám phá tự do và hệ thống chiến đấu sâu.',
 '1795340291_el.webp', 'FromSoftware', '2022-02-25', 1),
('The Witcher 3: Wild Hunt', 380000,
 'Nhập vai thợ săn quái vật Geralt trong một thế giới mở giàu cốt truyện và nhiệm vụ phụ hấp dẫn.',
 '1795340283_w3.webp', 'CD Projekt Red', '2015-05-19', 1),
('Cyberpunk 2077', 690000,
 'Nhập vai hành động trong thành phố tương lai Night City với nhiều lựa chọn ảnh hưởng đến cốt truyện.',
 '1795340273_cyber.webp', 'CD Projekt Red', '2020-12-10', 1),
('Resident Evil 4', 790000,
 'Bản làm lại của tựa game kinh dị sinh tồn kinh điển, với đồ họa và lối chơi được hiện đại hóa.',
 '1795340264_re4.webp', 'Capcom', '2023-03-24', 2),
('Hollow Knight', 150000,
 'Phiêu lưu hành động 2D trong vương quốc côn trùng dưới lòng đất, đồ họa vẽ tay và âm nhạc ấn tượng.',
 '1795340152_hollow.webp', 'Team Cherry', '2017-02-24', 6),
('Hades', 250000,
 'Roguelike hành động, vào vai hoàng tử địa ngục tìm cách thoát khỏi cõi âm với cốt truyện thay đổi theo mỗi lần chơi.',
 '1795340139_hades.webp', 'Supergiant Games', '2020-09-17', 6),
('Stardew Valley', 120000,
 'Mô phỏng nông trại thư giãn: trồng trọt, chăn nuôi, câu cá, kết bạn và khám phá hang động.',
 '1795340074_stadew.webp', 'ConcernedApe', '2016-02-26', 4),
('Civilization VI', 590000,
 'Chiến thuật theo lượt, xây dựng đế chế từ thời đồ đá đến thời hiện đại và chinh phục thế giới.',
 '1795340013_civil.webp', 'Firaxis Games', '2016-10-21', 3),
('Forza Horizon 5', 890000,
 'Đua xe thế giới mở lấy bối cảnh Mexico với hàng trăm mẫu xe và các sự kiện đua đa dạng.',
 '1795339908_forza.webp', 'Playground Games', '2021-11-09', 5),
('It Takes Two', 450000,
 'Phiêu lưu hợp tác dành cho hai người chơi, với các màn chơi sáng tạo và cốt truyện cảm động.',
 '1795339422_it.webp', 'Hazelight Studios', '2021-03-26', 2),
('Grand Theft Auto VI', 1990000,
 'Phần mới nhất của loạt game thế giới mở của Rockstar, đưa người chơi trở lại Vice City cùng cặp đôi Lucia và Jason.',
 '1795340604_gta.webp', 'Rockstar Games', '2026-11-19', 2),
('Resident Evil Requiem', 1790000,
 'Phần chính thứ chín của loạt game kinh dị sinh tồn Resident Evil, theo chân đặc vụ FBI Grace Ashcroft cùng Leon S. Kennedy.',
 '1795340786_rere.webp', 'Capcom', '2026-02-27', 2);

-- ---------------------------------------------------------------------
-- Giỏ hàng mẫu của khach1 (để thử giao diện giỏ hàng)
-- ---------------------------------------------------------------------
insert into giohang (ma_nguoidung, ma_game, so_luong) values
(2, 1, 1),
(2, 6, 1);

-- ---------------------------------------------------------------------
-- Một đơn hàng mẫu của khach2 (để thử trang lịch sử đơn hàng)
-- ---------------------------------------------------------------------
insert into donhang (ma_nguoidung, tong_tien, trang_thai, ghi_chu) values
(3, 520000, 'da_thanh_toan', 'Đơn hàng mẫu');

insert into chitietdonhang (ma_donhang, ma_game, so_luong, don_gia) values
(1, 6, 1, 250000),
(1, 5, 1, 150000),
(1, 7, 1, 120000);

-- ---------------------------------------------------------------------
-- Đánh giá mẫu
-- ---------------------------------------------------------------------
insert into danhgia (ma_nguoidung, ma_game, so_sao, noi_dung) values
(3, 6, 5, 'Chơi cuốn, mỗi lần chết lại thấy cốt truyện tiến thêm một chút.'),
(3, 5, 5, 'Đồ họa đẹp, khó nhưng rất đáng chơi.'),
(2, 1, 4, 'Thế giới rộng và đẹp, nhưng độ khó khá cao với người mới.');
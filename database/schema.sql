-- =====================================================================
-- schema.sql : tạo database và các bảng cho Web Bán Game
-- Người phụ trách: Huy
-- Cách dùng: import file này TRƯỚC, rồi mới import seed.sql
-- Lưu ý: file có lệnh "drop table" để import lại được nhiều lần khi
--        đang phát triển. Khi import lại, TOÀN BỘ dữ liệu cũ sẽ mất.
-- =====================================================================

create database if not exists web_ban_game
    character set utf8mb4
    collate utf8mb4_unicode_ci;

use web_ban_game;

-- Xóa bảng theo thứ tự ngược lại với quan hệ khóa ngoại
drop table if exists danhgia;
drop table if exists chitietdonhang;
drop table if exists donhang;
drop table if exists giohang;
drop table if exists game;
drop table if exists theloai;
drop table if exists nguoidung;

-- ---------------------------------------------------------------------
-- 1. nguoidung : tài khoản khách hàng và quản trị viên
-- ---------------------------------------------------------------------
create table nguoidung (
    ma_nguoidung   int auto_increment primary key,
    ten_dangnhap   varchar(50)  not null unique,
    email          varchar(100) not null unique,
    mat_khau       varchar(255) not null,              -- lưu bằng password_hash()
    ho_ten         varchar(100) not null,
    vai_tro        enum('khach', 'admin') not null default 'khach',
    ngay_tao       datetime not null default current_timestamp
);

-- ---------------------------------------------------------------------
-- 2. theloai : thể loại game
-- ---------------------------------------------------------------------
create table theloai (
    ma_theloai     int auto_increment primary key,
    ten_theloai    varchar(100) not null unique
);

-- ---------------------------------------------------------------------
-- 3. game : sản phẩm được bán
-- ---------------------------------------------------------------------
create table game (
    ma_game        int auto_increment primary key,
    ten_game       varchar(200) not null,
    gia            decimal(12,0) not null check (gia >= 0),   -- đơn vị: đồng
    mo_ta          text,
    hinh_anh       varchar(255),                               -- tên file trong assets/img/games/
    nha_phat_hanh  varchar(150),
    ngay_phat_hanh date,
    ma_theloai     int not null,
    dang_ban       tinyint(1) not null default 1,              -- 1 = đang bán, 0 = ẩn
    ngay_them      datetime not null default current_timestamp,
    foreign key (ma_theloai) references theloai(ma_theloai)
);

-- ---------------------------------------------------------------------
-- 4. giohang : mỗi dòng là một game trong giỏ của một người dùng
-- ---------------------------------------------------------------------
create table giohang (
    ma_nguoidung   int not null,
    ma_game        int not null,
    so_luong       int not null default 1 check (so_luong > 0),
    ngay_them      datetime not null default current_timestamp,
    primary key (ma_nguoidung, ma_game),
    foreign key (ma_nguoidung) references nguoidung(ma_nguoidung) on delete cascade,
    foreign key (ma_game)      references game(ma_game)           on delete cascade
);

-- ---------------------------------------------------------------------
-- 5. donhang : đơn hàng
-- ---------------------------------------------------------------------
create table donhang (
    ma_donhang     int auto_increment primary key,
    ma_nguoidung   int not null,
    ngay_dat       datetime not null default current_timestamp,
    tong_tien      decimal(14,0) not null default 0,
    trang_thai     enum('cho_xu_ly', 'da_thanh_toan', 'da_huy') not null default 'cho_xu_ly',
    ghi_chu        varchar(255),
    foreign key (ma_nguoidung) references nguoidung(ma_nguoidung)
);

-- ---------------------------------------------------------------------
-- 6. chitietdonhang : các game trong từng đơn hàng
--    don_gia lưu lại giá tại thời điểm mua, để sau này đổi giá game
--    thì đơn cũ không bị ảnh hưởng.
-- ---------------------------------------------------------------------
create table chitietdonhang (
    ma_donhang     int not null,
    ma_game        int not null,
    so_luong       int not null check (so_luong > 0),
    don_gia        decimal(12,0) not null,
    primary key (ma_donhang, ma_game),
    foreign key (ma_donhang) references donhang(ma_donhang) on delete cascade,
    foreign key (ma_game)    references game(ma_game)
);

-- ---------------------------------------------------------------------
-- 7. danhgia : đánh giá game (tính năng mở rộng)
--    Mỗi người chỉ đánh giá một game một lần.
-- ---------------------------------------------------------------------
create table danhgia (
    ma_danhgia     int auto_increment primary key,
    ma_nguoidung   int not null,
    ma_game        int not null,
    so_sao         tinyint not null check (so_sao between 1 and 5),
    noi_dung       varchar(500),
    ngay_danh_gia  datetime not null default current_timestamp,
    unique (ma_nguoidung, ma_game),
    foreign key (ma_nguoidung) references nguoidung(ma_nguoidung) on delete cascade,
    foreign key (ma_game)      references game(ma_game)           on delete cascade
);

-- Chỉ mục giúp tìm kiếm và lọc nhanh hơn
create index idx_game_theloai on game(ma_theloai);
create index idx_game_ten     on game(ten_game);
create index idx_donhang_user on donhang(ma_nguoidung);

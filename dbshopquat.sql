-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 10:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbshopquat`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'quat_tran'),
(2, 'quat_dung'),
(3, 'quat_treo'),
(4, 'quat_lung'),
(5, 'quat_ban'),
(6, 'quat_hop'),
(7, 'quat_cong_nghiep'),
(8, 'quat_thong_gio'),
(9, 'quat_hoi_nuoc');

-- --------------------------------------------------------

--
-- Table structure for table `discount`
--

CREATE TABLE `discount` (
  `discount_id` int(11) NOT NULL,
  `discount_amout` float DEFAULT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(13,2) NOT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `user_id`, `order_date`, `total_amount`, `status`) VALUES
(1, 2, '2025-05-14 16:37:52', 6530000.00, 'Completed'),
(2, 1, '2025-06-11 18:17:14', 16750000.00, 'Completed'),
(3, 5, '2025-06-03 19:21:14', 15350000.00, 'Completed'),
(4, 2, '2025-06-12 20:21:07', 1530000.00, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetail`
--

CREATE TABLE `orderdetail` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(13,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetail`
--

INSERT INTO `orderdetail` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 602, 2, 790000.00),
(2, 1, 604, 5, 990000.00),
(3, 2, 621, 4, 420000.00),
(4, 2, 632, 2, 690000.00),
(5, 2, 760, 1, 13690000.00),
(6, 3, 608, 5, 1670000.00),
(7, 3, 754, 2, 3500000.00),
(8, 4, 607, 1, 1530000.00);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `discount_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `category_id`, `price`, `picture`, `discount_id`) VALUES
(601, 'Quạt trần 3 cánh Mỹ Phong MP1400 (Không kèm hộp số)', 1, 1210000.00, 'image/quat_tran/quat-tra-my-phong_6ea99f1dbee64521ad453a24f83493e6_compact.gif', NULL),
(602, 'Quạt trần 3 cánh ChingHai SF7168 - có Hộp số', 1, 790000.00, 'image/quat_tran/1_f2413d88d31748a2849486cd9c1e9a16_compact.jpg', NULL),
(603, 'QUẠT TRẦN 3 CÁNH ACF01A563', 1, 950000.00, 'image/quat_tran/2_535102ba8589487c92241b27754a75e9_compact.jpg', NULL),
(604, 'Quạt trần đèn ốp trần Thịnh Hoa W2310', 1, 990000.00, 'image/quat_tran/z6347474392047_6f2290647793fc7ce2cbff77b6a848a5_33fa0c77075a4d3588e5e48eef07edaf_compact.jpg', NULL),
(605, 'Quạt trần 3 cánh Panasonic F-60MZ2 / F-60MZ2-K - Trắng / Đen  - Hộp  số', 1, 1130000.00, 'image/quat_tran/quat-tran-panasonic-f-60mz2_85d5091c1c234cc78ef6682f34067483_compact.png', NULL),
(606, 'Quạt trần KDK M60XG sải cánh 1.5m - Hộp số', 1, 1495000.00, 'image/quat_tran/quat-tran-kdk-m60xg-3-canh-5-toc-do_6dba0820576e4ae68fe88a100652b5fe_compact.jpg', NULL),
(607, 'Quạt trần Panasonic F-56NCL - 140cm - 70w (Hộp số)', 1, 1530000.00, 'image/quat_tran/pa_56e3d8c511c140948cd0408be59f9ef8_compact.png', NULL),
(608, 'Quạt trần KDK N56YG - Hộp số', 1, 1670000.00, 'image/quat_tran/quat-tran-kdk-n56yg-3-canh-5-toc-do_f938d140f66e40deab2df17f472e02d2_compact.jpg', NULL),
(609, '[Hỏa tốc] Quạt trần Thái 3 cánh Hatari C48M1-Sải cánh 1200mm', 1, 1670000.00, 'image/quat_tran/1_02ccf6d0b03a4a4e8b27dfa1a0f48045_compact.jpg', NULL),
(610, 'Quạt trần trang trí T52-6500AB-1320mm', 1, 1950000.00, 'image/quat_tran/unnamed__2__0d50222081a949debed8dc79db0afd6d_compact.jpg', NULL),
(611, 'Quạt trần đèn T52-2319FG - 1320mm', 1, 2050000.00, 'image/quat_tran/z5863105623367_adb891cc85a83d1ff70eb810855de511_4f42775800634cbab44907f3aed5470d_compact.jpg', NULL),
(612, '[Hỏa tốc] Quạt trần 3 cánh Hatari C56M1-Sải cánh 1400mm', 1, 2070000.00, 'image/quat_tran/1_f65678602f6849afa6bad1d5faf5a501_compact.jpg', NULL),
(613, 'Quạt trần đèn 52-KC3110 - 1170mm - 6 cấp gió', 1, 2150000.00, 'image/quat_tran/1_af54936d287f4ca7ae661cacc129edd8_compact.jpg', NULL),
(614, 'Quạt trần 4 cánh Panasonic F-56MPG-S / F-56MPG-GO ( Bạc / Vàng )', 1, 2240000.00, 'image/quat_tran/quat-tran-panasonic-f-56mpg-bac-2_775adf94c3c94bc38537b9036c17861b_compact.jpg', NULL),
(615, 'Quạt trần 4 cánh Panasonic F-56MZG-S / F-56MZG-G - Bạc/vàng- Remote', 1, 2250000.00, 'image/quat_tran/quat-tran-panasonic-f-56mzg-s_2ed658e1a6db41909c558409df1d800f_compact.jpg', NULL),
(616, 'Quạt trần đèn T52-6702OB - 1320mm', 1, 2250000.00, 'image/quat_tran/z5863136295356_a4026fd0252d1c50168f4d2db4569c93_287af07d43b740949501fe8335d8a9d5_compact.jpg', NULL),
(617, 'Quạt trần L896-WH/BR - 1320mm', 1, 2250000.00, 'image/quat_tran/screenshot_1727233420_aa3cac76ad954d46941e28f47c9d5961_compact.png', NULL),
(618, 'Quạt trần đèn cánh ABS lá cong NDT-322 - 1370mm - DC', 1, 2300000.00, 'image/quat_tran/5979ec494f21827fdb30_fca970e1b6fd46c48a9d728304872ba0_compact.jpg', NULL),
(619, 'Quạt trần không đèn Beller 552-N - 1320mm', 1, 2300000.00, 'image/quat_tran/z5954845445031_02c6db5ee571a789aae374622ec56ce1_9800055198fe416e9d00331d678fd648_compact.jpg', NULL),
(620, 'Quạt trần đèn Klasse 48KSA (Màu nâu gỗ/ Màu trắng/màu vàng gỗ)', 1, 2300000.00, 'image/quat_tran/z6328259466600_0e52aa0f66cf69465171742b22fea81a_525bdbc02a604eababc335ded1c0a732_compact.jpg', NULL),
(621, '[Hỏa tốc] Quạt đứng SENKO DTS1607 65w - Nút vặn', 2, 420000.00, 'image/quat_dung/dts1607-01_4575ecb4e74b47d58d54494cafc317e8_compact.jpg', NULL),
(622, '[Hỏa tốc] Quạt đứng Senko DH1600', 2, 440000.00, 'image/quat_dung/cc70f9acc40d98dadda6c3cbd724426d_tn_68df7175271848b2ab8d01cdf1d10d88_compact.jpeg', NULL),
(623, 'Quạt đứng Senko 5 cánh DTS1609 (60W)', 2, 440000.00, 'image/quat_dung/screenshot_1694851776_f2c1d3b968d54d5caa4ff998daa7699c_compact.png', NULL),
(624, '[Hỏa tốc] Quạt đứng SENKO DCN1807 75w - Nút vặn', 2, 480000.00, 'image/quat_dung/1_28f1ba9598024eef8a604d74e6bd7523_compact.jpg', NULL),
(625, 'Quạt đứng SENKO  DD1602 47w - Nút nhấn', 2, 510000.00, 'image/quat_dung/dd868-03_dfa972cb03f6497fb8451699dc432b2d_compact.jpg', NULL),
(626, 'Quạt đứng ASIAvina Turbo X - Đen - VY629890 /  Xám - VY629790 - 40cm - 80w', 2, 530000.00, 'image/quat_dung/d_d24d63ce8e0d4871ae83f99eeaafe296_compact.png', NULL),
(627, '[Hỏa tốc] Quạt đứng Senko DCN1809 75w - Nút vặn', 2, 530000.00, 'image/quat_dung/10032717_quat-dung_senko_dcn1808-mau-den-cam_01_8d8711a52f09468d9e7765af601f0fda_compact.jpg', NULL),
(628, 'Quạt đứng Ching Hai HS918CĐ', 2, 595000.00, 'image/quat_dung/12232_11_ddf9f51810284b4a93671f1786b5fd2f_compact.jpg', NULL),
(629, 'Quạt đứng Bán Công Nghiệp Asia - Đen cam VY589890 / Xám VY589790 - 45cm - 80W', 2, 619000.00, 'image/quat_dung/9_02319b914e624235bea37a5f9b039b9d_compact.jpg', NULL),
(630, 'Quạt đứng LIFAN D-18CXN - Sải cánh 45cm - 55W', 2, 620000.00, 'image/quat_dung/1_83085e1a16da49edaa85df717e425d23_compact.jpg', NULL),
(631, 'Quạt đứng LIFAN D-18CN - Sải cánh 45cm - 55W', 2, 670000.00, 'image/quat_dung/d-18cn_e40dcd918a11474992ec5f5b4ad5b929_compact.jpg', NULL),
(632, '[Hỏa tốc] Quạt đứng SENKO DR1608 65w - Remote', 2, 690000.00, 'image/quat_dung/z5863147817804_8fe4f53220c41375ed080daf42a1ddf8_b0db2114a74e4e2985d79a8f78022683_compact.jpg', NULL),
(633, 'Quạt đứng ASIAvina TURBO PLUS VY639792 - ( Xám ) /  VY639892 ( Đen ) - 90W (New 2024) Siêu mạnh', 2, 730000.00, 'image/quat_dung/z5323665120017_4667027349a6c07924e166c909a2c5e8_a6b80f2f18ab4a4db4e6df8d7d8b2d4f_compact.jpg', NULL),
(634, 'Quạt đứng ChingHai HS916B', 2, 850000.00, 'image/quat_dung/z5864611273938_6fd3605af80c07d1e8be342722a7ab3c_0375dcb9caa34b3f971376555dca306f_compact.jpg', NULL),
(635, 'Quạt đứng Asiavina TURBO PLUS VY639092 - 90w (New 2025)', 2, 880000.00, 'image/quat_dung/screenshot_1739977230_193a2f539260493ba3600485f2144941_compact.png', NULL),
(636, 'Quạt Đứng Toshiba F-LSA10(K)VN / F-LSA10(H)VN - Nút nhấn (Đen/Trắng xám)', 2, 895000.00, 'image/quat_dung/f-lsa10_k_vn_2b6d8957c0ae48ce921960f05754b7c4_compact.jpg', NULL),
(637, 'Quạt đứng 3 cánh 45cm Chinghai HS966B', 2, 910000.00, 'image/quat_dung/fact-depot-quat-dung-3-canh-45cm-chinghai-hs966b_2139b7229def4d0582c6b045e76f1e29_compact.jpg', NULL),
(638, 'Quạt đứng Chinghai HS966A - có Remote', 2, 980000.00, 'image/quat_dung/fact-depot-quat-dung-3-canh-45cm-chinghai-hs966b_dbad634b296d4ae0bc73c4bf7cf0f228_compact.jpg', NULL),
(639, 'Quạt đứng ASIAvina TURBO DECOR VY659991 110w (New 2025)', 2, 990000.00, 'image/quat_dung/screenshot_1739977551_d6769c73eeff42c49a15ed0e65400a44_compact.png', NULL),
(640, 'Quạt đứng công nghiệp Ching Hai HS920', 2, 1040000.00, 'image/quat_dung/hs920_8d0cfd901858402d8d8b7b0a5d3d6644_989a0478d0544fa6bb298898be80aa96_compact.jpg', NULL),
(641, '[Hỏa tốc] Quạt treo SENKO T1680  47w - 1 Dây giật', 3, 290000.00, 'image/quat_treo/t1688-02_c18708cbb50b412c90b8123c218b1dd9_compact.jpg', NULL),
(642, '[Hỏa tốc] Quạt treo SENKO TC1626 47w - Dây giật', 3, 319000.00, 'image/quat_treo/tc1626-01_39b613bbcf7a44b6b5f688d31181e948_compact.jpg', NULL),
(643, '[Hỏa tốc] Quạt treo Senko TC16 47w - 2 Dây giật', 3, 340000.00, 'image/quat_treo/tc16_53cfa4dd7cde41669c324a29a050d4e5_compact.png', NULL),
(644, '[Hỏa tốc] Quạt treo SENKO TC1620 65w - Dây giật', 3, 360000.00, 'image/quat_treo/tc1620-03_9fead0384833487dbf3bdc7091cbc549_compact.jpg', NULL),
(645, '[Hỏa tốc] Quạt treo SENKO TC1622 65w - Dây giật', 3, 370000.00, 'image/quat_treo/tc1622-01_518c9e9f514c4bebbfbda835675ccbef_compact.jpg', NULL),
(646, 'Quạt treo tường Lifan T-161 (45W - 1 dây giựt)', 3, 385000.00, 'image/quat_treo/1_63e2e1d616e34dc694d218c40c528e75_compact.png', NULL),
(647, 'Quạt treo tường ChingHai W613 - 2 Dây giựt', 3, 420000.00, 'image/quat_treo/product_4829_1_b2b2f158faba40deb2f9067e39c63fc0_compact.png', NULL),
(648, '[Hỏa tốc] Quạt treo SENKO TC1880 75w - Dây giật', 3, 420000.00, 'image/quat_treo/10038866_quattreo_senko_tc1880-dencam_01_fc1u-k8_ba9c97a70e0044d2b9ac53a357bbcf66_compact.jpg', NULL),
(649, '[Hỏa tốc] QUẠT TREO SENKO TC1886 75w - Dây giật', 3, 430000.00, 'image/quat_treo/unnamed_64998ae3887e43558ee37844ae3423f3_compact.jpg', NULL),
(650, 'Quạt treo Asia VY357792 / VY357192 - 55w ( Xám, Thiên Thanh)', 3, 450000.00, 'image/quat_treo/product_16056_3_6faa1b9246d94641a018f35da679072e_compact.png', NULL),
(651, '[Hỏa tốc] Quạt treo Senko TR1683 47w - Remote', 3, 450000.00, 'image/quat_treo/tr1428-03_4d1fc94fe2784d9ebe8682e6411afe03_compact.jpg', NULL),
(652, '[Hỏa tốc] Quạt treo Senko TR1628 47w - Remote', 3, 450000.00, 'image/quat_treo/tr828-02_e47e26a70b49443a9772a14aaed18836_compact.jpg', NULL),
(653, 'Quạt treo ASIAvina Turbo X - VY627790 (Xám) / VY627890 (Đen) - 80W', 3, 470000.00, 'image/quat_treo/4_1c38f34a4a704bc8a77c17fd26543ca8_663602eb796d4067b27e50958fd59b49_compact.jpg', NULL),
(654, 'QUẠT TREO TƯỜNG CHING HAI W615B2 - 2 dây giựt', 3, 485000.00, 'image/quat_treo/screenshot_1727229100_b72deac53f884240a8a63fd45f700182_compact.png', NULL),
(655, 'Quạt Treo HALI TC-199D (2Dây) - 45cm - 80W', 3, 516000.00, 'image/quat_treo/screenshot_1727229155_d2051460ce534740bb5c14b67b37033c_compact.png', NULL),
(656, 'Quạt treo công nghiệp Ching Hai W18CĐ', 3, 545000.00, 'image/quat_treo/z5864683204247_5d3e01993d4962b2dfa02ed5961a78d5_8764dd50fa36487ba0dc94f502f6e086_compact.jpg', NULL),
(657, 'Quạt treo Bán Công Nghiệp Asia - VY587790 - Xám - 45cm - 80W', 3, 549000.00, 'image/quat_treo/7_cea5220c248046bc8cde3c6b777236d1_compact.jpg', NULL),
(658, 'Quạt treo Bán Công Nghiệp Asia - VY587890 - 45cm - 80W', 3, 549000.00, 'image/quat_treo/8_f6c4254ab6e248b38441d495d55f5a18_compact.jpg', NULL),
(659, 'Quạt Treo HALI TN19(2)- 2 dây giựt -45cm - 80W', 3, 590000.00, 'image/quat_treo/z5864661681454_ff6a1fd51e6973c52c06e6c2e77a5ec2_28503d67a09146e1b0dd264266d0ff19_compact.jpg', NULL),
(660, 'Quạt treo đảo hình sóng LiFan TE-1689 - Remote', 3, 610000.00, 'image/quat_treo/z5864646248650_2a03969c6a59dea3d56836a49dc1338d_08e9e0c987244de3b99e5996d6cbbe1b_compact.jpg', NULL),
(661, '[Hỏa tốc] Quạt lửng SENKO L1638 - 47w - thân nhựa', 4, 327000.00, 'image/quat_lung/l1338-02_1261bf47f41b4a7b8b5a37b8cebabf12_compact.jpg', NULL),
(662, '[Hỏa tốc] Quạt lững SENKO LTS1636 65w - Nút vặn', 4, 380000.00, 'image/quat_lung/lts1636-01_487b65698a944fad9d3f735ee141dce9_69f24b51cc2146c9a7d4e70bb2b6e59a_compact.jpg', NULL),
(663, 'Quạt lửng Asia VY358192 Thiên Thanh / VY358792 Xám - 40cm - 55w', 4, 430000.00, 'image/quat_lung/xanh_7d275cbc0a724637975c2f66cc32b65d_compact.png', NULL),
(664, 'Quạt lửng SENKO LTS1632 60w - Nút vặn', 4, 430000.00, 'image/quat_lung/10049498-quat-lo-senko-lts1632-den-cam-1_746ef77f1cd7440fbdaa6f8ab15bc27e_compact.jpg', NULL),
(665, 'Quạt lửng Bán Công Nghiệp Asia - Đen cam VY558890 - 40cm - 75w', 4, 469000.00, 'image/quat_lung/screenshot_1710154709_aadb776af56041839665b0f5dd97aca9_compact.png', NULL),
(666, 'Quạt lửng Chinghai HS988 - 50w', 4, 590000.00, 'image/quat_lung/hs988_d1643a881e4b4469b1dcd063d219e54e_compact.png', NULL),
(667, 'Quạt lửng ASIAvina TURBO PLUS - VY638892 - Đen 90w (New 2024) Siêu mạnh', 4, 675000.00, 'image/quat_lung/1_c29f34554bed45e49d6943fb92b934b1_compact.jpg', NULL),
(668, 'Quạt Thái lửng Hatari S14M1 - 49W (New 2024)', 4, 790000.00, 'image/quat_lung/102a2-8cfe-4707-a484-a76274d4a859-efd50512-5e7d-448f-a181-f5948fca5b63_2634783972a446f18bb57282fccee8a0_compact.png', NULL),
(669, 'Quạt lửng Hatari S16M1 - 49W', 4, 840000.00, 'image/quat_lung/3_7fe5b0eb4b7341a9be0fb3123a42c151_compact.jpg', NULL),
(670, '[Hỏa tốc] Quạt Thái Lan lửng Hatari HB-S16M4 - 49w - Bạc đạn', 4, 920000.00, 'image/quat_lung/screenshot_1663671830_a50b27ddc30046e88bce82e89cb53218_compact.png', NULL),
(671, 'Quạt Lửng Benny BF-41SL,  60W, 16 inch', 4, 950000.00, 'image/quat_lung/quat-lung-benny-bf-41sl-2_cb8260bf95514cebbd61f4717287dcd7_compact.jpg', NULL),
(672, '[ FLASH SALE ] Quạt lửng Hatari HE-S18M1 - 61w', 4, 970000.00, 'image/quat_lung/screenshot_1727228823_ed545d36755446ccb4429833f81c9cb8_compact.png', NULL),
(673, '[Hỏa tốc] Quạt Thái Lan lửng Hatari HT-S16R2 52w - Remote', 4, 1490000.00, 'image/quat_lung/hatari-ht-s16r2_8c2b7a0b7c864a8c8c3833676f7c1a1f_compact.jpg', NULL),
(674, '[Hỏa tốc] Quạt Thái Lan lửng Hatari Slide Smart L1 61w - remote', 4, 1780000.00, 'image/quat_lung/z5864631393795_0030f5922f4ec95b3cb4cc7de2297ef3_12ca817d82414f06bdace3e164f8a860_compact.jpg', NULL),
(675, 'Quạt lửng Panasonic F-307KHB / F-307KHS- Remote', 4, 1880000.00, 'image/quat_lung/4581_quat-lung-panasonic-f307khb-blue_dbc720b901fd47948f7453cddb63dca8_04bb198a388b4335ad11551daef7e0b5_compact.jpg', NULL),
(676, 'Quạt lửng Panasonic F-308NHB / F-308NHP - remote', 4, 2750000.00, 'image/quat_lung/quat-dung-panasonic-f308nhp-2_dfab1d012631474d8e78cd0ad28fbf27_compact.jpg', NULL),
(677, 'Quạt Lửng Mitsubishi R12A-DA (Trắng/Đỏ)', 4, 6190000.00, 'image/quat_lung/7fdc84820b519fe17babd7f1c25fc476_0bf3a0865b244efd9cd2e67800b7a0e5_compact.jpg', NULL),
(678, 'Quạt bàn Senko B102 28w - Nút nhấn', 5, 230000.00, 'image/quat_ban/b102-01_2c5df28137794ecfa46a847d676ee8cc_compact.jpg', NULL),
(679, 'Quạt bàn Senko B1213 40W - Nút vặn', 5, 270000.00, 'image/quat_ban/b1213-01_8c9326be1d654e2a8fd6a69d77bb0ffc_compact.jpg', NULL),
(680, 'Quạt bàn Ching Hai HD606', 5, 280000.00, 'image/quat_ban/z5864745016771_bc5c8c8a07943739a0ac4714e91a8736_828efb613f4c466abd39bec76f77934c_compact.jpg', NULL),
(681, 'QUẠT BÀN CHING HAI HD601', 5, 290000.00, 'image/quat_ban/quat-ban-ching-hai-hd-601_6ce1f0867960429591bbf59e789fe231_compact.jpg', NULL),
(682, 'Quạt bàn Senko BX1212 47w - Nút nhấn', 5, 300000.00, 'image/quat_ban/bx1212-01_887aef568ebb4c9a861c3ca02c203101_compact.jpg', NULL),
(683, 'Quạt bàn senko B1612 47W - Nút vặn', 5, 300000.00, 'image/quat_ban/b1613-01_340ae9770a8648f0a0e99da449471106_compact.jpg', NULL),
(684, 'Quạt bàn Senko B1616 47W - Nút nhấn', 5, 300000.00, 'image/quat_ban/b1610-03_47f799bf849f446e86f1d12e04f3c45d_compact.jpg', NULL),
(685, 'Quạt đứng lở Ching hai HS802', 5, 420000.00, 'image/quat_ban/z2888312738623_5f7a5921abd8283e55f90c7eb3002339_33dce0fd087a429b82107948d9d954c9_compact.jpg', NULL),
(686, 'Quạt bàn cánh 35cm Chinghai TF1499', 5, 450000.00, 'image/quat_ban/fact-depot-quat-ban-canh-35cm-chinghai-tf1499_d005b086443a420681ae1ac5c0c0187b_compact.jpg', NULL),
(687, '[Hỏa tốc] Quạt Thái Lan bàn HATARI HT-PS20M1 - 19w (Nhiều màu)', 5, 470000.00, 'image/quat_ban/2_d689abeb0515442888a8f82840819ca5_compact.jpeg', NULL),
(688, 'Quạt bàn Hatari PS8M1 - 18W', 5, 500000.00, 'image/quat_ban/1_5ca2985f3fd047b1b7dd948d4c98c103_745c06766df94d1b97a1e99c84c117bd_compact.jpg', NULL),
(689, 'Quạt bàn Thái  Hatari T14M1-43W (New 2024)', 5, 740000.00, 'image/quat_ban/1_498f286b14bf47619df6c9da97ea5acf_ee668a1c65b2424d9b7be69ddaf61375_compact.jpg', NULL),
(690, 'Quạt bàn Hatari T16M1-49W (New 2024)', 5, 840000.00, 'image/quat_ban/0a2b8-47d6-47c7-9bf9-ff5606b02305-4f926210-acab-4d73-b9dc-b00993b62774_6bc5c4d01411425da2c29bc24e08a31a_compact.png', NULL),
(691, 'Quạt Bàn Benny BFT-30GN, 45W,  12 inch', 5, 850000.00, 'image/quat_ban/bft-30-ava_2578f812747c47b6af29dde7988f1d1c_compact.png', NULL),
(692, 'Quạt Bàn Panasonic F-400CB / F-400CI - Nút nhấn', 5, 1050000.00, 'image/quat_ban/download_7e8b326f5b744681b77e73d270790a6f_740e053277054459ae7a8a5b7a8053d9_compact.jpg', NULL),
(693, 'Quạt bàn Thái Hatari T18M1 - 61W (New 2024)', 5, 1090000.00, 'image/quat_ban/102a2-8cfe-4707-a484-a76274d4a859-efd50512-5e7d-448f-a181-f5948fca5b63_453f58ffb9a84574865fb59c3a078081_compact.png', NULL),
(694, 'QUẠT BÀN HIỆN ĐẠI MEMORY', 5, 3492000.00, 'image/quat_ban/1_996342a9d7274952a28de008880b7554_compact.jpg', NULL),
(695, 'Quạt hộp Senko BD1012 - 40w', 6, 310000.00, 'image/quat_hop/bd860-01_062d11ad94fb4f0a86af3b7aae26d173_compact.jpg', NULL),
(696, 'Quạt hộp Senko model BD1410 - 47w', 6, 460000.00, 'image/quat_hop/bd850-01_466d233ef8a44f7a9cace6f6a605a1df_compact.jpg', NULL),
(697, 'Quạt hộp Chinghai BF1688', 6, 485000.00, 'image/quat_hop/z5864712789668_13413fb921c7132b9a85199b647eb01b_49a46ebb4e1d4afe9b179ba06e814cde_compact.jpg', NULL),
(698, 'Quạt hộp Ching hai BF168', 6, 590000.00, 'image/quat_hop/z2888404875098_b114957028f161b8fea541bddf753a70_c33f4edc90e242c3ba56ae69e5033f79_compact.jpg', NULL),
(699, 'Quạt hộp Chinghai BF16899', 6, 600000.00, 'image/quat_hop/m_b192768cc81947b5846786d4886cef33_compact.png', NULL),
(700, '[ BIG SALE ] Quạt hộp International MP530 – 3 cánh, sải cánh 35 cm - xuất xứ Lào', 6, 600000.00, 'image/quat_hop/3_a049fa68e3a443a0974b40fed5198437_compact.jpg', NULL),
(701, 'Quạt xếp Dasin DFF-1845A', 7, 2100000.00, 'image/quat_cong_nghiep/screenshot_1747020971_6efc9a384a104cdf8d464c471d90cb71_compact.png', NULL),
(702, 'Quạt sàn dân dụng Hawinco HFE-45 - 80W', 7, 1055000.00, 'image/quat_cong_nghiep/1_52efdbdc7acb4152a805abb65e103d34_97f391351b49403ca670305c8eaff090_compact.jpg', NULL),
(703, 'Quạt sàn dân dụng Hawinco HFE-40 - 70W', 7, 980000.00, 'image/quat_cong_nghiep/1_3e0b1886886046ed9f0b197e6bab65ac_2db069680a4e432284a3aeee1519dc27_compact.jpg', NULL),
(704, 'Quạt đứng công nghiệp Miphaco DCN-500 - 230W', 7, 1350000.00, 'image/quat_cong_nghiep/z6447743810092_0b7ada032326389cfe182b10b2be82b1_db28abbf17c34d5fb614fbf8a864cb45_compact.jpg', NULL),
(705, 'Quạt sàn công nghiệp Miphaco SCN-45 - 150W', 7, 1000000.00, 'image/quat_cong_nghiep/z6447835829835_98244f1bf9c37b9b485ad64d3cc0e72c_e4f334a41ad74108ba42942ebd5c2083_compact.jpg', NULL),
(706, 'Quạt đứng bán công nghiệp Miphaco DF-45 - 110W', 7, 970000.00, 'image/quat_cong_nghiep/z6447356975721_981e1918ec02926a9a1b824561e1dd76_dafc0e46e4ce4ea3977614e6e57ab717_compact.jpg', NULL),
(707, 'Quạt sàn công nghiệp Deton CF-50G - 110W ( Sơn đen / Xi mạ )', 7, 1535000.00, 'image/quat_cong_nghiep/z6428073845195_716f37fdfc9cd6b72880101f5901a67f_78f47641f3f34cab8e5e1ecc4cc3ecce_compact.jpg', NULL),
(708, 'Quạt sàn công nghiệp Deton CF-45G - 75W ( Sơn đen / Xi mạ )', 7, 1400000.00, 'image/quat_cong_nghiep/z6428073845195_716f37fdfc9cd6b72880101f5901a67f_007b3ecb69bb4ab49a6222edd079f0cf_compact.jpg', NULL),
(709, 'Quạt sàn lửng Chinghai FF2522 - 55cm - 155W', 7, 1500000.00, 'image/quat_cong_nghiep/z6360839245506_2dd4487033fa24cc7538ce86c561d3bd_fcdb568efaad4415a897606da955dfde_compact.jpg', NULL),
(710, 'Quạt sàn lửng Ching Hai FF2518 - 45cm - 128W', 7, 1100000.00, 'image/quat_cong_nghiep/z6360796939466_b196124fc863e784b116e92cd2de67d3_5b6e283b03bb476098086596dcf376ad_compact.jpg', NULL),
(711, 'Quạt sàn công nghiệp ChingHai FF920B', 7, 1000000.00, 'image/quat_cong_nghiep/screenshot_1740729422_14878cef6eee4801909b73d14339ae7f_compact.png', NULL),
(712, 'Quạt treo tường ChingHai W22 - 55cm - 155W', 7, 1400000.00, 'image/quat_cong_nghiep/11_c7ee1ba6a4024dbd9fd2c1875cb0cfab_compact.png', NULL),
(713, 'Quạt treo tường ChingHai W2518 - 45cm - 128W', 7, 900000.00, 'image/quat_cong_nghiep/screenshot_1740728920_34d49fe0207f4bb293611a5bfc15dd38_compact.png', NULL),
(714, 'Quạt sàn ASIAvina TURBO MAX VY646990 - 100w (New 2025)', 7, 785000.00, 'image/quat_cong_nghiep/screenshot_1740107032_2c8a3a728e344c4dae2ee86b94272a60_compact.png', NULL),
(715, 'Quạt thông gió nối ống Hawinco HT-100/HT-150/HT-200', 7, 790000.00, 'image/quat_cong_nghiep/2_a0cbd3e3346c4826a89a035b1cf7dd1e_compact.jpg', NULL),
(716, 'Quạt thổi khô sàn công nghiệp Omysu CD-H04 - 3200w', 7, 3700000.00, 'image/quat_cong_nghiep/may-thoi-san-mini-cd-04_f1e8692abba14d6a8bddf012b853a802_compact.jpg', NULL),
(717, 'Quạt thổi khô sàn công nghiệp Omysu CD-T01 - 300w', 7, 1700000.00, 'image/quat_cong_nghiep/z5927683581221_eec22d3dc1a1ba37bc7800599197280a_5008ff7ff2694274bf5b4457a4ea32f6_compact.jpg', NULL),
(718, 'Quạt thông gió công nghiệp Omysu BMF500 - 220V (Vỏ tôn kẽm cánh Inox, chớp Inox)', 7, 2000000.00, 'image/quat_cong_nghiep/z5927557318571_6529cd2c9a361015e4eb7866f404bf2a_be898cf127f94b93b45ddc10b1d1b570_compact.jpg', NULL),
(719, 'Quạt công nghiệp di động Dasin TANK-50125 (220V)', 7, 10700000.00, 'image/quat_cong_nghiep/z5894657987233_212b29bfd7199fcb3a43f36e5ae6f653_769b0f4a63c14e57a42ab6a7d0ca4566_compact.jpg', NULL),
(720, 'Quạt công nghiệp di động Dasin Tank-3076 (220V/380V)', 7, 5250000.00, 'image/quat_cong_nghiep/tank-3076_3649ad10141b4e1cba28969c8b02bf24_compact.png', NULL),
(721, 'Quạt thông gió tròn DETON DFC3B-4 / DFC4B-4 / DFC5B-4 / DFC6B-4', 8, 1500000.00, 'image/quat_thong_gio/img_5946_a2d73ad6e7cb44149f3a5fde31c2d8c4_compact.jpg', NULL),
(722, 'Quạt thông gió nối ống Hawinco HT-100/HT-150/HT-200', 8, 790000.00, 'image/quat_thong_gio/2_a0cbd3e3346c4826a89a035b1cf7dd1e_compact.jpg', NULL),
(723, 'Quạt vuông HSV 30 - 220V Có lưới - 45W', 8, 850000.00, 'image/quat_thong_gio/screenshot_1727234416_5f1a459c6366477fb2825d61f5fe8b45_compact.png', NULL),
(724, 'Quạt vuông HSV 40 - 220V Có lưới - 130W', 8, 1290000.00, 'image/quat_thong_gio/screenshot_1727234416_ba15e64166314c77a3c3474d4a60cab7_compact.png', NULL),
(725, 'Quạt Thông Gió iFan-36D / iFan-42D / iFan-48D / iFan-54D', 8, 4780000.00, 'image/quat_thong_gio/quat-thong-gio-ifan-d1_810d055c121245b6b55486f26ad6e821_a35e192a65f349ddb40c88a1f6a84e3b_compact.jpg', NULL),
(726, 'Quạt Loa composite  iFan-106C / iFan-126C / iFan-146C - Chuyển động gián tiếp', 8, 7600000.00, 'image/quat_thong_gio/z5863057755841_9c31d8d29fe7ca780ad9bc010464c9cf_6b794a28dd5b4084b59afd212f802b95_compact.jpg', NULL),
(727, 'Quạt hút công nghiệp tròn IFAN TA-3B / TA-4B / TA-5B / TA-6B (250w/370w/450w/550w)', 8, 1450000.00, 'image/quat_thong_gio/ta-3b_031b9c829e574bddbac89f8accbaebfd_compact.jpg', NULL),
(728, 'Quạt thông gió công nghiệp Superwin SPW-600 - 370w (380v/220v)', 8, 2700000.00, 'image/quat_thong_gio/quat-vuong-truc-tiep-super-win-qv620-220v_357b7ddc5de94583b08a0dd632e03790_compact.jpg', NULL),
(729, 'Quạt thông gió công nghiệp Superwin SPW-1380 (380V/220V)', 8, 4640000.00, 'image/quat_thong_gio/2_40d92d4498d34c8ca331286865293472_compact.jpg', NULL),
(730, 'Quạt thông gió công nghiệp Superwin ZRA-1000 (380V/220V)', 8, 3360000.00, 'image/quat_thong_gio/img_6515_03cdde4b3bc342418ee194e17dbb50bb_compact.jpg', NULL),
(731, 'Quạt Hút composite iFan- 850CSA/ 1060CSA/ 1260CSA/ 1460CSA - chuyển độngTrực tiếp', 8, 4050000.00, 'image/quat_thong_gio/screenshot_1640930433_00d6d43a39e346ed9eb0f5bb8413e055_compact.png', NULL),
(732, 'Quạt thông gió công nghiệp Superwin SPW-1220 (380V/220V)', 8, 4100000.00, 'image/quat_thong_gio/2_24ff9744aae843b5a71a6f4ab7cf50b7_compact.jpg', NULL),
(733, 'Quạt thông gió COMPOSITE  Superwin ZRG-1460 - 750W', 8, 7000000.00, 'image/quat_thong_gio/1460_9124fadde7644a53bdd1842bd7f15e16_compact.jpg', NULL),
(734, 'Quạt cấp gió đường ống Superwin YXF-100 / YXF-125 / YXF-150 / YXF-200 / YXF-250 / YXF-315', 8, 1988000.00, 'image/quat_thong_gio/05_411x452_cfa812d212b946cba2642dbfa0544169_compact.png', NULL),
(735, 'Quạt thông gió công nghiệp  Superwin SPW-1000 (380V/220V)', 8, 3700000.00, 'image/quat_thong_gio/21301_quat-thong-gio-cong-nghiep-truc-vuong-super-win-spw-1000-380v_a137e65b8c1f43a583756e5b582cfb8f_compact.jpg', NULL),
(736, 'Quạt thông gió vuông inox Superwin FDI 25-4 / FDI 30-4 / FD 35-4 (27W - 34W - 42W)', 8, 510000.00, 'image/quat_thong_gio/quat-thong-gio-vuong-inox-super-win-fdi-25-4_673c065084d64b0390b8586e04fcb449_compact.jpg', NULL),
(737, 'Quạt vuông thông gió trực tiếp IFAN-20A / IFAN-24A / IFAN-28A / IFAN-30A', 8, 2780000.00, 'image/quat_thong_gio/screenshot_1640923946_e4bf0789af994cb08121d757a4491f06_compact.png', NULL),
(738, 'Quạt vuông HSV 50 - 220V Có lưới - 200W', 8, 1600000.00, 'image/quat_thong_gio/screenshot_1727234416_c6e84e2f5a4c4b8084c69367903ae1f7_compact.png', NULL),
(739, 'QUẠT thông gió chống cháy nổ TSBF3-4 / TSBF4-4 / TSBF5-4 / TSBF6-4 / TSBF7-6 / TSBF8-6', 8, 4265000.00, 'image/quat_thong_gio/1_f4a5e44df4534c87bb18f72f7cfeddaa_compact.png', NULL),
(740, 'Quạt thông gió công nghiệp Composite Omysu BMF1460 - 1.1KW', 8, 4750000.00, 'image/quat_thong_gio/1_568704b552314731b877c7d9e0c7568f_8ed61b674dca4b62a09c7816f4ae46c9_compact.jpg', NULL),
(741, 'MÁY KHỬ KHUẨN KHÔNG KHÍ FUKATA', 9, 1590000.00, 'image/quat_hoi_nuoc/screenshot_1727250008_ccd586eb6347435dbb92dc861fadb318_compact.png', NULL),
(742, 'Quạt hơi nước SUNHOUSE SHD7744 - 125w', 9, 1890000.00, 'image/quat_hoi_nuoc/3-toc-_1_70a444ef5eec47a3a6e2185e25ecac03_compact.jpeg', NULL),
(743, 'Quạt làm mát hơi nước điều hòa Hawin HSN55C - 130w - Bản cơ - 40L', 9, 2200000.00, 'image/quat_hoi_nuoc/screenshot_1715315352_21eb73f20b1f4abe8b99ff6531f44fc0_compact.png', NULL),
(744, 'Máy làm mát không khí Kangaroo KG50F92 - 100w', 9, 2490000.00, 'image/quat_hoi_nuoc/may-lam-mat-khong-khi-kangaroo-kg50f92_40ee9e3913be489aacef9e95f8bb1a4f_compact.jpg', NULL),
(745, 'Quạt hơi nước SUNHOUSE SHD7777 - 200w', 9, 2850000.00, 'image/quat_hoi_nuoc/7775-02_3__c2aaaa21595845f4b9e7e9a1a62551a3_compact.jpg', NULL),
(746, 'Máy làm mát không khí Kangaroo KG50F64 - 90w', 9, 2850000.00, 'image/quat_hoi_nuoc/kg50f64_5addb73ec9724e0f8336d15d0bf83b35_compact.jpg', NULL),
(747, 'Máy làm mát không khí Kangaroo KG50F99 - 165w', 9, 2850000.00, 'image/quat_hoi_nuoc/quat-dieu-hoa-kangaroo-kg50f99-2_104493f375ee4212804d5dc5bd3ed94c_compact.jpg', NULL),
(748, 'Quạt hơi nước SUNHOUSE SHD7719 - 100w', 9, 2850000.00, 'image/quat_hoi_nuoc/1079_may_lam_mat_khong_khi_sunhouse_shd7719_001_3512a4c2a51f4920b8eb0597094b7119_compact.jpg', NULL),
(749, 'Quạt hơi nước SUNHOUSE SHD7789 - 190w', 9, 2900000.00, 'image/quat_hoi_nuoc/may-lam-mat-khong-khi-sunhouse-shd7789-1_4554498dc4da4e51aa68e3b56bc02386_compact.jpg', NULL),
(750, 'Quạt hơi nước SUNHOUSE SHD7776 - 200w', 9, 2900000.00, 'image/quat_hoi_nuoc/10057453-quat-dieu-hoa-sunhouse-shd-7776-5_dc4ae1936425499888dff0ccfcaddea7_compact.jpg', NULL),
(751, 'Quạt hơi nước SUNHOUSE SHD7727 - 150W', 9, 3150000.00, 'image/quat_hoi_nuoc/10045129-quat-dieu-hoa-sunhouse-shd7727-1_c1986a74fc7b4687a2a5aca7f7f15364_compact.jpg', NULL),
(752, 'Máy làm mát không khí Kangaroo KG50F88 - 200w', 9, 3200000.00, 'image/quat_hoi_nuoc/may-lam-mat-khong-khi-kangaroo-kg50f88_1ef5aea567ca486bae61a1df6aeaf1a4_compact.jpg', NULL),
(753, 'Quạt làm mát hơi nước điều hòa Hawin HSN 80C - 280w (Bảng Cơ) -65L', 9, 3240000.00, 'image/quat_hoi_nuoc/z5865172382232_ce8314f4711824e0672b320f1ed1df92_d0142a5af2de427caebe5469d6423d3f_compact.jpg', NULL),
(754, 'Quạt làm mát hơi nước điều hòa Boss S-102 S102 Thái Lan (Mới 100% Không phải hàng trưng bày)', 9, 3500000.00, 'image/quat_hoi_nuoc/quat-boss-s-102-kg-2_703eb8197041483c97ee99697bfd8982_compact.jpg', NULL),
(755, 'Quạt làm mát hơi nước điều hòa Hawin HSN100C - 400w - 65L', 9, 3640000.00, 'image/quat_hoi_nuoc/screenshot_1715315455_f1f901f9ba6643ac8bf345b9bf8c0c18_compact.png', NULL),
(756, 'Quạt làm mát hơi nước điều hòa Hawin HSN120 - 500w - Cơ - 90L', 9, 4600000.00, 'image/quat_hoi_nuoc/screenshot_1715314681_529f4c4f5385437b8509e9c6456bbf09_compact.png', NULL),
(757, 'Quạt làm mát hơi nước điều hòa Boss S106 / S-106 - Xuất xứ : Thái Lan (Mới 100% Không phải hàng trưn', 9, 5450000.00, 'image/quat_hoi_nuoc/10035987_quat-lam-mat-khong-khi-boss-s106-1_2fd2bf3e833d437ea51c4b35ca79f94e_compact.jpg', NULL),
(758, 'Quạt Phun Sương Công Nghiệp 1100W Aircooler TM-L03HSZ - 1.100W', 9, 5800000.00, 'image/quat_hoi_nuoc/screenshot_1727320466_c5d595ffd1b543eeafea75b5a9796fa8_compact.png', NULL),
(759, 'Quạt Phun Sương Công Nghiệp Aircooler TM-L06HSZ - 1.500W', 9, 7800000.00, 'image/quat_hoi_nuoc/screenshot_1727322938_4872554f15654a7e91ca7915c91bbb4b_compact.png', NULL),
(760, 'Điều hòa di động công nghiệp Kyungjin ND-9200 (1.100W) / ND-12000 (1.590W) / ND-22000 (6.500W)', 9, 13690000.00, 'image/quat_hoi_nuoc/1a_dedac8381eb94d1aa2496123c948b43b_compact.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_review`
--

CREATE TABLE `product_review` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_review`
--

INSERT INTO `product_review` (`review_id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 601, 1, 5, 'Quạt chạy rất êm, mát nhanh!', '2025-06-14 08:06:04'),
(2, 601, 2, 4, 'Thiết kế đẹp, nhưng giá hơi cao.', '2025-06-14 08:06:04'),
(3, 602, 2, 4, 'Hoạt động ổn, hơi ồn chút khi max công suất.', '2025-06-14 08:06:04'),
(4, 602, 3, 5, 'Rất hài lòng với sản phẩm, đáng tiền.', '2025-06-14 08:06:04'),
(5, 603, 1, 3, 'Chất lượng bình thường, không ồn nhưng gió yếu.', '2025-06-14 08:06:04'),
(6, 603, 4, 4, 'Công suất tốt, dễ lắp đặt.', '2025-06-14 08:06:04'),
(7, 604, 3, 2, 'Quạt hỏng sau vài ngày sử dụng.', '2025-06-14 08:06:04'),
(8, 604, 5, 5, 'Quạt đẹp, đa chức năng, rất chắc chắn.', '2025-06-14 08:06:04'),
(9, 605, 4, 5, 'Hoạt động êm, thiết kế sang trọng.', '2025-06-14 08:06:04'),
(10, 605, 5, 5, 'Phiên bản cao cấp, quá tuyệt vời!', '2025-06-14 08:06:04'),
(11, 621, 5, 3, 'Tiện lợi, gọn nhẹ, nhưng hơi rung.', '2025-06-14 08:06:04'),
(12, 621, 1, 4, 'Tốt, nhưng phần chân chưa chắc chắn.', '2025-06-14 08:06:04'),
(13, 622, 4, 4, 'Đáng mua, phù hợp với phòng nhỏ.', '2025-06-14 08:06:04'),
(14, 622, 3, 5, 'Hoàn hảo cho phòng ngủ của tôi.', '2025-06-14 08:06:04');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','employee','customer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `password`, `full_name`, `phone_number`, `address`, `email`, `role`) VALUES
(1, 'doanh', 'Doanh0904', 'ngo quoc doanh', '0123456789', '140 Lê Trọng Tấn', 'aitthuylan@gmail.com', 'customer'),
(2, 'anhba', 'test123', 'batu khan', '0123456789', '140 Lê Trọng Tấn', 'batukhan@gmail.com', 'customer'),
(4, 'admin', '2zN£:7qte5u8', 'nguyen van admin', '0123456789', '140 Le Trong Tan', 'admin@gmail.com', 'admin'),
(5, 'user1', 'password1', 'nguyen van user1', '0123456789', '140 Lê Trọng Tấn', 'user1@gmail.com', 'customer'),
(6, 'user2', 'password2', 'nguyen van user2', '0123456789', '150 Lê Trọng Tấn', 'user2@gmail.com', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_user_cart` (`user_id`),
  ADD KEY `fk_product_cart` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`discount_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `fk_user_feedback` (`user_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `fk_order` (`order_id`),
  ADD KEY `fk_product` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category` (`category_id`),
  ADD KEY `fk_discount` (`discount_id`);

--
-- Indexes for table `product_review`
--
ALTER TABLE `product_review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `discount`
--
ALTER TABLE `discount`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orderdetail`
--
ALTER TABLE `orderdetail`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=766;

--
-- AUTO_INCREMENT for table `product_review`
--
ALTER TABLE `product_review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_product_cart` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `fk_user_cart` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_user_feedback` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `fk_discount` FOREIGN KEY (`discount_id`) REFERENCES `discount` (`discount_id`);

--
-- Constraints for table `product_review`
--
ALTER TABLE `product_review`
  ADD CONSTRAINT `product_review_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

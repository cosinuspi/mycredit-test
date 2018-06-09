CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `area_id` char(10) NOT NULL,
    `city_id` char(10),
    `city_district_id` char(10),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`area_id`) REFERENCES t_koatuu_tree(`ter_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`city_id`) REFERENCES t_koatuu_tree(`ter_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`city_district_id`) REFERENCES t_koatuu_tree(`ter_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

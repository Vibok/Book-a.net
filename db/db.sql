--
-- Base de datos: `booka`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acl`
--

DROP TABLE IF EXISTS `acl`;
CREATE TABLE `acl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` varchar(50) DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `allow` tinyint(1) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `role_FK` (`role_id`),
  KEY `user_FK` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Volcar la base de datos para la tabla `acl`
--

INSERT INTO `acl` VALUES(1, '*', '*', '//', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(2, '*', '*', '/image/*', 1, '2011-05-18 23:08:42');
INSERT INTO `acl` VALUES(3, '*', '*', '/tpv/*', 1, '2011-05-27 01:04:02');
INSERT INTO `acl` VALUES(4, '*', '*', '/admin/*', 0, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(5, '*', '*', '/booka/*', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(6, 'superadmin', '*', '/admin/*', 1, '2011-05-18 16:45:40');
INSERT INTO `acl` VALUES(7, 'admin', '*', '/admin/*', 1, '2012-09-16 15:50:49');
INSERT INTO `acl` VALUES(8, '*', '*', '/user/*', 1, '2011-05-18 20:59:54');
INSERT INTO `acl` VALUES(10, '*', '*', '/search', 1, '2011-05-18 21:16:40');
INSERT INTO `acl` VALUES(11, 'admin', '*', '/booka/create/', 1, '2012-07-12 13:47:52');
INSERT INTO `acl` VALUES(12, 'user', '*', '/dashboard/*', 1, '2011-05-18 21:48:43');
INSERT INTO `acl` VALUES(13, 'public', '*', '/invest/*', 0, '2011-05-18 22:30:23');
INSERT INTO `acl` VALUES(14, 'user', '*', '/message/*', 1, '2011-05-18 22:30:23');
INSERT INTO `acl` VALUES(15, 'user', '*', '/message/edit/*', 0, '2012-07-23 09:23:41');
INSERT INTO `acl` VALUES(17, '*', '*', '/booka/create', 0, '2011-05-18 22:38:22');
INSERT INTO `acl` VALUES(18, '*', '*', '/booka/edit/*', 0, '2011-05-18 22:38:22');
INSERT INTO `acl` VALUES(19, '*', '*', '/booka/raw/*', 0, '2011-05-18 22:39:37');
INSERT INTO `acl` VALUES(20, 'root', '*', '/booka/raw/*', 1, '2011-05-18 22:39:37');
INSERT INTO `acl` VALUES(21, 'superadmin', '*', '/booka/edit/*', 1, '2011-05-18 22:43:08');
INSERT INTO `acl` VALUES(22, '*', '*', '/booka/delete/*', 0, '2011-05-18 22:43:51');
INSERT INTO `acl` VALUES(23, 'superadmin', '*', '/booka/delete/*', 1, '2011-05-18 22:44:37');
INSERT INTO `acl` VALUES(24, '*', '*', '/blog/*', 1, '2011-05-18 22:45:14');
INSERT INTO `acl` VALUES(25, '*', '*', '/faq/*', 1, '2011-05-18 22:49:01');
INSERT INTO `acl` VALUES(26, '*', '*', '/about/*', 1, '2011-05-18 22:49:01');
INSERT INTO `acl` VALUES(31, 'user', '*', '/message/delete/*', 0, '2011-05-19 00:45:29');
INSERT INTO `acl` VALUES(32, 'superadmin', '*', '/message/edit/*', 1, '2011-05-19 00:56:55');
INSERT INTO `acl` VALUES(33, 'superadmin', '*', '/message/delete/*', 1, '2011-05-19 00:00:00');
INSERT INTO `acl` VALUES(34, 'user', '*', '/invest/*', 1, '2011-05-19 00:56:32');
INSERT INTO `acl` VALUES(35, 'public', '*', '/message/*', 0, '2011-05-19 00:56:32');
INSERT INTO `acl` VALUES(37, 'root', '*', '/cron/*', 1, '2011-05-27 01:04:02');
INSERT INTO `acl` VALUES(38, '*', '*', '/widget/*', 1, '2011-06-10 11:30:39');
INSERT INTO `acl` VALUES(39, '*', '*', '/user/recover/*', 1, '2011-06-12 22:31:36');
INSERT INTO `acl` VALUES(42, '*', '*', '/ws/*', 1, '2011-06-20 23:18:15');
INSERT INTO `acl` VALUES(44, '*', '*', '/contact/*', 1, '2011-06-30 00:24:00');
INSERT INTO `acl` VALUES(45, '*', '*', '/service/*', 1, '2011-07-13 17:26:04');
INSERT INTO `acl` VALUES(48, '*', '*', '/legal/*', 1, '2011-08-05 13:09:08');
INSERT INTO `acl` VALUES(49, '*', '*', '/rss/*', 1, '2011-08-14 18:32:01');
INSERT INTO `acl` VALUES(50, 'superadmin', '*', '/impersonate/*', 1, '2011-08-20 09:41:05');
INSERT INTO `acl` VALUES(52, 'user', 'paypal', '/paypal/*', 1, '2011-09-05 00:58:55');
INSERT INTO `acl` VALUES(53, 'user', 'paypal', '/cron/*', 1, '2011-09-05 00:58:55');
INSERT INTO `acl` VALUES(56, '*', '*', '/mail/*', 1, '2011-09-25 14:13:58');
INSERT INTO `acl` VALUES(58, '*', '*', '/json/*', 1, '2011-11-22 16:10:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `booka`
--

DROP TABLE IF EXISTS `booka`;
CREATE TABLE `booka` (
  `id` varchar(50) NOT NULL,
  `owner` varchar(50) NOT NULL DEFAULT 'booka',
  `name_es` tinytext,
  `name_en` tinytext,
  `subtitle_es` tinytext,
  `subtitle_en` tinytext,
  `author` tinytext,
  `collection` bigint(20) unsigned DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `progress` int(3) NOT NULL DEFAULT '0',
  `amount` int(6) DEFAULT '0' COMMENT 'acumulado actualmente',
  `created` date DEFAULT NULL,
  `updated` date DEFAULT NULL,
  `published` date DEFAULT NULL,
  `success` date DEFAULT NULL,
  `closed` date DEFAULT NULL,
  `image` varchar(256) DEFAULT NULL,
  `description_es` text,
  `description_en` text,
  `motivation_es` text,
  `motivation_en` text,
  `about_es` text,
  `about_en` text,
  `goal_es` text,
  `goal_en` text,
  `related_es` text,
  `related_en` text,
  `patron_es` text,
  `patron_en` text,
  `category` varchar(50) DEFAULT NULL,
  `keywords_es` tinytext COMMENT 'Separadas por comas',
  `keywords_en` tinytext COMMENT 'Separadas por comas',
  `media_es` varchar(256) DEFAULT NULL,
  `media_en` varchar(256) DEFAULT NULL,
  `media_usubs` int(1) NOT NULL DEFAULT '0',
  `comment` text COMMENT 'Comentario para los admin',
  `info_es` tinytext,
  `info_en` tinytext,
  `caption_es` tinytext,
  `caption_en` tinytext,
  `media_caption_es` tinytext,
  `media_caption_en` tinytext,
  `milestone1_es` text,
  `milestone1_en` text,
  `milestone2_es` text,
  `milestone2_en` text,
  `milestone3_es` text,
  `milestone3_en` text,
  `milestone4_es` text,
  `milestone4_en` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Libros de la biblioteca';

--
-- Volcar la base de datos para la tabla `booka`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `booka2_image`
--

DROP TABLE IF EXISTS `booka2_image`;
CREATE TABLE `booka2_image` (
  `booka2` varchar(50) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`booka2`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Imagenes de los libros';

--
-- Volcar la base de datos para la tabla `booka2_image`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `booka_category`
--

DROP TABLE IF EXISTS `booka_category`;
CREATE TABLE `booka_category` (
  `booka` varchar(50) NOT NULL,
  `category` int(12) NOT NULL,
  UNIQUE KEY `BOOK_category` (`booka`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categorias de los libros';

--
-- Volcar la base de datos para la tabla `booka_category`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `booka_image`
--

DROP TABLE IF EXISTS `booka_image`;
CREATE TABLE `booka_image` (
  `booka` varchar(50) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`booka`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Imagenes de los libros';

--
-- Volcar la base de datos para la tabla `booka_image`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_es` tinytext,
  `name_en` tinytext,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Categorias de los proyectos' AUTO_INCREMENT=11 ;

--
-- Volcar la base de datos para la tabla `category`
--

INSERT INTO `category` VALUES(1, 'Arte', '', 1);
INSERT INTO `category` VALUES(2, 'Arquitectura', '', 3);
INSERT INTO `category` VALUES(3, 'Paisaje', '', 7);
INSERT INTO `category` VALUES(4, 'Cultura urbana', '', 5);
INSERT INTO `category` VALUES(5, 'Fotografía', '', 2);
INSERT INTO `category` VALUES(6, 'Ensayos', '', 4);
INSERT INTO `category` VALUES(7, 'Monografias', '', 6);
INSERT INTO `category` VALUES(10, 'Geografías', '', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `collection`
--

DROP TABLE IF EXISTS `collection`;
CREATE TABLE `collection` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_es` tinytext,
  `name_en` tinytext,
  `color` varchar(7) DEFAULT '#E7E7E7',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Colecciones de libros' AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `collection`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cost`
--

DROP TABLE IF EXISTS `cost`;
CREATE TABLE `cost` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booka` varchar(50) NOT NULL,
  `cost_es` tinytext,
  `cost_en` tinytext,
  `description_es` text,
  `description_en` text,
  `stage` int(1) NOT NULL DEFAULT '1' COMMENT 'Hitos de financiacion',
  `type` int(20) unsigned DEFAULT NULL,
  `amount` int(5) DEFAULT '0',
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Desglose de costes de proyectos' AUTO_INCREMENT=15 ;

--
-- Volcar la base de datos para la tabla `cost`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cost_type`
--

DROP TABLE IF EXISTS `cost_type`;
CREATE TABLE `cost_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_es` tinytext,
  `name_en` tinytext,
  `description_es` text,
  `description_en` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tipos de costes' AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `cost_type`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL DEFAULT 'main',
  `title_es` tinytext,
  `title_en` tinytext,
  `description_es` text,
  `description_en` text,
  `order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Preguntas frecuentes' AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `faq`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE `image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Volcar la base de datos para la tabla `image`
--

INSERT INTO `image` VALUES(1, 'user.png', 'image/png', 13916);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest`
--

DROP TABLE IF EXISTS `invest`;
CREATE TABLE `invest` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `booka` varchar(50) NOT NULL,
  `account` varchar(256) NOT NULL COMMENT 'Solo para aportes de cash',
  `amount` int(6) NOT NULL,
  `status` int(1) NOT NULL COMMENT '-1 en proceso, 0 pendiente, 1 cobrado, 2 devuelto, 3 pagado al proyecto',
  `anonymous` tinyint(1) DEFAULT NULL,
  `resign` tinyint(1) DEFAULT NULL,
  `invested` date DEFAULT NULL,
  `charged` date DEFAULT NULL,
  `returned` date DEFAULT NULL,
  `payment` varchar(256) DEFAULT NULL COMMENT 'PayKey',
  `transaction` varchar(256) DEFAULT NULL COMMENT 'PaypalId',
  `method` varchar(20) NOT NULL COMMENT 'Metodo de pago',
  `admin` varchar(50) DEFAULT NULL COMMENT 'Admin que creó el aporte manual',
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Aportes monetarios a proyectos' AUTO_INCREMENT=11 ;

--
-- Volcar la base de datos para la tabla `invest`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest_address`
--

DROP TABLE IF EXISTS `invest_address`;
CREATE TABLE `invest_address` (
  `invest` bigint(20) unsigned NOT NULL,
  `user` varchar(50) NOT NULL,
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nif` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`invest`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Dirección de entrega de recompensa';

--
-- Volcar la base de datos para la tabla `invest_address`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invest_reward`
--

DROP TABLE IF EXISTS `invest_reward`;
CREATE TABLE `invest_reward` (
  `invest` bigint(20) unsigned NOT NULL,
  `reward` bigint(20) unsigned NOT NULL,
  `fulfilled` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `invest` (`invest`,`reward`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Recompensas elegidas al aportar';

--
-- Volcar la base de datos para la tabla `invest_reward`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lang`
--

DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `id` varchar(2) NOT NULL COMMENT 'Código ISO-639',
  `name` varchar(20) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `short` varchar(10) DEFAULT NULL,
  `locale` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Idiomas';

--
-- Volcar la base de datos para la tabla `lang`
--

INSERT INTO `lang` VALUES('en', 'English', 1, 'ENG', 'en_GB');
INSERT INTO `lang` VALUES('es', 'Español', 1, 'ES', 'es_ES');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mail`
--

DROP TABLE IF EXISTS `mail`;
CREATE TABLE `mail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` tinytext NOT NULL,
  `html` longtext NOT NULL,
  `template` int(20) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contenido enviado por email para el -si no ves-' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `mail`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mailer_content`
--

DROP TABLE IF EXISTS `mailer_content`;
CREATE TABLE `mailer_content` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '1',
  `mail` int(20) NOT NULL,
  `subject` text NOT NULL,
  `content` longtext NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `blocked` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contenido a enviar' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `mailer_content`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mailer_send`
--

DROP TABLE IF EXISTS `mailer_send`;
CREATE TABLE `mailer_send` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `email` varchar(256) NOT NULL,
  `name` varchar(100) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sended` int(1) DEFAULT NULL,
  `error` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Destinatarios pendientes y realizados' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `mailer_send`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `booka` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mensajes de usuarios en proyecto' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `message`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` varchar(50) NOT NULL,
  `name` tinytext NOT NULL,
  `text_es` text,
  `text_en` text,
  `url` tinytext,
  `content_es` longtext,
  `content_en` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Páginas institucionales';

--
-- Volcar la base de datos para la tabla `page`
--

INSERT INTO `page` VALUES('about', 'Acerca de', 'about', '', '/about', '', '');
INSERT INTO `page` VALUES('colaborate', 'Nuevos colaboradores', 'Autores y editore', 'Eng Autores y editore', '/about/colaborate', '', '');
INSERT INTO `page` VALUES('collections', 'Colecciones', 'Sobre las colecciones', '', '/about/collections', '', '');
INSERT INTO `page` VALUES('confirm', 'Confirmación de registro', 'Hemos creado tu cuenta de usuario', NULL, '/user/confirm', '<p>\r\nTe habrá llegado un email con una dirección para confirmar la cuenta y comezar a usar book-a.net<br />\r\nOJO! Mira también en la carpeta de spam o elementos no deseados.\r\n</p>', NULL);
INSERT INTO `page` VALUES('confirm_account', 'Confirmación de un solo click', 'Tu cuenta ha quedado asociada', NULL, '/user/confirm_account', '<p>Siempre podrás seguir logueando con esta cuenta pero te recomendamos que pongas una contraseña en tu usuario.</p>\r\n', NULL);
INSERT INTO `page` VALUES('contact', 'Contacto', 'Contacta con book-a', 'ENG Contacta con book-a', '/contact', 'Contacta con book-a', 'ENG Contacta con book-a');
INSERT INTO `page` VALUES('credits', 'Créditos', 'Creditos', '', '/about/credits', '<a href="http://onliners-web.com">Onliners Web development</a><br /><a href="http://vibokworks.com/">Vibok Work</a><br /><a href="http://goteo.org">Goteo.org</a><br />', '');
INSERT INTO `page` VALUES('error', 'La página que buscas no existe', 'La página que buscas no existe', '', '/about/error', '<p>\r\n	<span style="font-size:36px;"><em><strong>Uops...</strong></em></span> esta página que buscas no existe (o la URL es incorrecta).<br />\r\n	Si estás segur@ de la URL, contacta con nosotr@s.</p>\r\n', '');
INSERT INTO `page` VALUES('for', 'Para quién', NULL, NULL, '/about/for', '', '');
INSERT INTO `page` VALUES('introduction', 'Presentaciones', NULL, NULL, '/about/introduction', '<p>\r\n	Presentaciones</p>\r\n', '');
INSERT INTO `page` VALUES('leave', 'Darse de baja', 'Para darte de baja', NULL, '/user/leave', '<p>Introduce tu email.</p>\r\n<p>Sin embargo, es una lástima que nos abandones... ¿serías tan amable de dejarnos un comentario? <br />Gracias!!</p>', NULL);
INSERT INTO `page` VALUES('login', 'Acceso', '', '', '/user/login', '', '');
INSERT INTO `page` VALUES('maintenance', 'Página de "en mantenimiento"', 'Estamos realizando mejoras', NULL, '/about/maintenance', 'La plataforma volverá a estar operativa en breve', NULL);
INSERT INTO `page` VALUES('privacy', 'Política de privacidad', 'Política de privacidad', NULL, '/legal/privacy', 'Política de privacidad', NULL);
INSERT INTO `page` VALUES('recover', 'Recuperar contraseña', 'Para recuperar la contraseña', NULL, '/user/recover', 'introduce el email asociado a la cuenta o el nombre de acceso', NULL);
INSERT INTO `page` VALUES('register', 'Texto de registro', NULL, NULL, '/register', '<p>\r\n	Acepto las condiciones de uso de la plataforma, as&iacute; como presto mi consentimiento para el tratamiento de mis datos personales. A tal efecto, el responsable del portal ha establecido una <a href="/legal/privacy">pol&iacute;tica de privacidad</a> donde se puede conocer la finalidad que sedar&aacute; a los datos suministrados a trav&eacute;s del presente formulario, as&iacute; como los derechos que asisten a la presona que suinistra dichos datos.</p>\r\n', '');
INSERT INTO `page` VALUES('terms', 'Condiciones de uso\r\n', 'Condiciones de uso', 'Condiciones de uso', '/legal/terms', '<p>Condiciones de uso</p>', '<p>Condiciones de uso</p>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title_es` tinytext,
  `title_en` tinytext,
  `subtitle_es` tinytext,
  `subtitle_en` tinytext,
  `text_es` longtext COMMENT 'texto de la entrada',
  `text_en` longtext COMMENT 'texto de la entrada',
  `media_es` varchar(256) DEFAULT NULL,
  `media_en` varchar(256) DEFAULT NULL,
  `image` int(10) DEFAULT NULL,
  `date` date NOT NULL COMMENT 'fehca de publicacion',
  `order` int(11) DEFAULT '1',
  `home` tinyint(1) DEFAULT '0' COMMENT 'En portada',
  `footer` tinyint(1) DEFAULT '0' COMMENT 'En pie',
  `publish` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Publicado',
  `top` tinyint(1) DEFAULT '0' COMMENT 'En cabecera',
  `legend_es` text,
  `legend_en` text,
  `url` tinytext COMMENT 'Enlace externo',
  `booka` varchar(50) DEFAULT NULL COMMENT 'Booka al que se refiere la noticia',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Entradas para la portada' AUTO_INCREMENT=8 ;

--
-- Volcar la base de datos para la tabla `post`
--
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_image`
--

DROP TABLE IF EXISTS `post_image`;
CREATE TABLE `post_image` (
  `post` bigint(20) NOT NULL,
  `image` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post`,`image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `post_image`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `post_tag`
--

DROP TABLE IF EXISTS `post_tag`;
CREATE TABLE `post_tag` (
  `post` bigint(20) unsigned NOT NULL,
  `tag` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`post`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tags de las entradas';

--
-- Volcar la base de datos para la tabla `post_tag`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promote`
--

DROP TABLE IF EXISTS `promote`;
CREATE TABLE `promote` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booka` varchar(50) NOT NULL,
  `title_es` tinytext,
  `title_en` tinytext,
  `description_es` text,
  `description_en` text,
  `order` smallint(5) unsigned NOT NULL DEFAULT '1',
  `active` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Proyectos destacados' AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `promote`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reward`
--

DROP TABLE IF EXISTS `reward`;
CREATE TABLE `reward` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booka` varchar(50) NOT NULL,
  `reward_es` tinytext,
  `reward_en` tinytext,
  `description_es` text,
  `description_en` text,
  `type` int(20) unsigned DEFAULT NULL COMMENT 'tipo de recompensa',
  `amount` int(5) DEFAULT NULL,
  `units` int(5) DEFAULT NULL,
  `other_text` tinytext COMMENT 'Otro tipo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Recompensas' AUTO_INCREMENT=26 ;

--
-- Volcar la base de datos para la tabla `reward`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reward_type`
--

DROP TABLE IF EXISTS `reward_type`;
CREATE TABLE `reward_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_es` tinytext,
  `name_en` tinytext,
  `description_es` text,
  `description_en` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tipos de recompensa' AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `reward_type`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `role`
--

INSERT INTO `role` VALUES('admin', 'Administrador');
INSERT INTO `role` VALUES('root', 'ROOT');
INSERT INTO `role` VALUES('superadmin', 'Super administrador');
INSERT INTO `role` VALUES('user', 'Usuario mediocre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsor`
--

DROP TABLE IF EXISTS `sponsor`;
CREATE TABLE `sponsor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `url` tinytext,
  `image` int(10) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Patrocinadores' AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `sponsor`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_es` tinytext NOT NULL,
  `name_en` tinytext,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tags' AUTO_INCREMENT=7 ;

--
-- Volcar la base de datos para la tabla `tag`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `template`
--

DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `purpose` tinytext NOT NULL,
  `title_es` tinytext NOT NULL,
  `title_en` tinytext NOT NULL,
  `text_es` text NOT NULL,
  `text_en` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Plantillas emails automáticos' AUTO_INCREMENT=34 ;

--
-- Volcar la base de datos para la tabla `template`
--

INSERT INTO `template` VALUES(1, 'Mensaje de contacto', 'Plantilla para un mensaje de contacto', 'Contacto: %SUBJECT%', '', '<p>Hola <span class="message-highlight-red">%TONAME%</span>,</p>\r\n<p>Éste es un mensaje de contacto enviado por <span class="message-highlight-blue">%USEREMAIL%</span> desde Book-a.net:</p>\r\n<blockquote>%MESSAGE%</blockquote>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(2, 'Mensaje a los cofinanciadores', 'Plantilla del mensaje masivo a cofinanciadores desde dashboard - gestión de retornos', 'Mensaje de un promotor', '', '<p>Hola <span class="message-highlight-red">%NAME%</span>,</p>\r\n<p>Éste es un mensaje enviado desde la plataforma Book-a.net por parte de quien impulsa el proyecto <span class="message-highlight-blue">%SITENAME%</span>:</p>\r\n<blockquote>%MESSAGE%</blockquote>\r\n<p>Te recordamos que puedes acceder a la página de <span class="message-highlight-blue">%SITENAME%</span> en Book-a.net desde la siguiente URL: </p>\r\n<p><span class="message-highlight-blue"><a href="%SITEURL%">%SITEURL%</a></span></p>\r\n<p>Saludos!</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(4, 'Mensaje entre usuarios', 'Mensaje de un usuario a otro desde la página de perfil del destinatario', 'Mensaje personal de %USERNAME% desde Book-a.net', '', '<p>Hola <span class="message-highlight-red">%TONAME%</span>, <p>\r\n<p>Mensaje enviado por <span class="message-highlight-red">%USERNAME%</span> desde Book-a.net:<p>\r\n<blockquote>%MESSAGE%</blockquote>\r\n<p>Para enviar un mensaje a <span class="message-highlight-red">%USERNAME%</span> pulsa <span class="message-highlight-blue"><a href="%RESPONSEURL%">AQUÍ</a></span>.</p>\r\n<p>Cordialmente</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(5, 'Confirmación de registro', 'Plantilla del mensaje de confirmación de registro', 'Confirmación de registro en Book-a.net', '', 'Hola %USERNAME%!\r\n\r\nMuchas gracias por tu interés en Book-a.net, tu cuenta ha sido creada con éxito :)\r\n\r\nPara confirmar tu dirección de correo electrónico y completar el registro, haz clic en el siguiente enlace (o cópialo en la barra de dirección del navegador):\r\n\r\n%ACTIVATEURL%\r\n\r\nRecuerda que tu login es <strong>%USERID%</strong>. Una vez se active tu cuenta podrás acceder y comenzar a utilizar la plataforma para apoyar o proponer proyectos.\r\n\r\nEstamos empezando todo esto :) Así que te invitamos a apoyar esta apuesta por el crowdfunding abierto cofinanciando dentro de tus posibilidades alguna de las iniciativas que encontrarás destacadas en la portada cuando accedas. Ayudarás así a hacer realidad los fantásticos retornos colectivos que proponen.\r\n\r\nSaludos,\r\n\r\nThe Mailer\r\n- - - - - - - - - - - - - - - - - - - -\r\n', '');
INSERT INTO `template` VALUES(6, 'Recuperar contraseña', 'Plantilla para el mensaje al solicitar la recuperación de contraseña', 'Petición de recuperación de contraseña', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Hemos recibido una petición para recuperar la contraseña de tu cuenta de usuario en <a href="%SITEURL%">%SITEURL%.</a></p>\r\n<p>Para acceder a tu cuenta y cambiar la contraseña utiliza el siguiente enlace:</p>\r\n<p><span class="message-highlight-blue"><a href="%RECOVERURL%">%RECOVERURL%</a></span></p>\r\n<p>Si no puedes hacer click, cópialo y pégalo en el navegador.</p>\r\n<p>(En caso de que no hayas has solicitado este cambio de contraseña, uhm... ignora este mensaje).</p>\r\n<p>Cordialmente,</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(7, 'Cambio de email', 'Plantilla del mensaje al cambiar el email', 'Petición de cambio de email', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Hemos recibido una petición para cambiar el email de tu cuenta de usuario en Book-a.net</p>\r\n<p>Para confirmar la propiedad de tu nueva dirección de correo electrónico, haz clic en el siguiente enlace (o cópialo en la barra de dirección del navegador):</p>\r\n<p><span class="message-highlight-blue"><a href="%CHANGEURL%">%CHANGEURL%</a></span></p>\r\n<p>Este proceso es necesario para confirmar la propiedad de la dirección de correo electrónico, así que no podrás operar con esta dirección hasta que la hayas confirmado.</p>\r\n<p>Cordialmente,</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(8, 'Confirmacion de proyecto enviado', 'Mensaje al usuario cuando envia un proyecto a revisión desde el preview del formulario', 'El proyecto %SITENAME% ha pasado a fase de valoración', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Hemos recibido la petición de revisar el proyecto <span class="message-highlight-blue">%SITENAME%</span> para valorar su publicación y promoción mediante Book-a.net</p>\r\n<p>En breve alguien del equipo se pondrá en contacto contigo al respecto.</p>\r\n<p>Puedes encontrar más información sobre el proceso de revisión de proyectos en nuestras FAQ: <span class="message-highlight-blue"><a href="%SITEURL%faq">%SITEURL%faq</a></span></p>\r\n<p>Muchas gracias!</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(9, 'Darse de baja', 'Plantilla para el mensaje al solicitar la baja', 'Solicitud de baja', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Hemos recibido una solicitud para dar de baja tu cuenta de usuario en <a href="%SITEURL%">%SITEURL%</a></p>\r\n<p>Para completar el proceso de baja accede al siguiente enlace:</p>\r\n<p><span class="message-highlight-blue"><a href="%URL%">%URL%</a></span></p>\r\n<p>Si no puedes hacer click, cópialo y pégalo en el navegador.</p>\r\n<p>(En caso de que no hayas has solicitado esta baja, uhm... ignora este mensaje)</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(10, 'Agradecimiento aporte', 'Mensaje al usuario después de aportar a un proyecto', 'Gracias por cofinanciar el proyecto %SITENAME%', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Muchas gracias por cofinanciar el proyecto <span class="message-highlight-blue"><a href="%SITEURL%">%SITENAME%</a></span> con %AMOUNT% €. %DROPED%</p>\r\n\r\n<p>Te recordamos que has seleccionado las siguientes recompensas: <br>  %REWARDS%<br><br>\r\n%ADDRESS%</p>\r\n\r\n<p>Por otro lado, aprovechamos este email para darte algunos detalles del funcionamiento de las transacciones a través de Book-a.net. </p>\r\n<p>Como habrás podido ver en el momento de elegir la forma de pago, hay dos sistemas posibles: PayPal y tarjeta de crédito. </p>\r\n\r\n<p>En caso de que hayas utilizado PayPal el cargo no será realizado hasta que finalice la 1ª ronda del proyecto (40 días) y éste haya obtenido el mínimo del presupuesto que necesita reunir. Este proceso es una preautorización a cobrar el importe que hayas decidido sólo si se cumplen esas condiciones. Podría definirse como una compromiso de pago si el proyecto reúne suficientes apoyos o "promesas de apoyos" :-) </p>\r\n\r\n<p>Si has utilizado el sistema de pago con tarjeta de crédito el procedimiento es diferente: el cargo se ejecuta inmediatamente en la cuenta correspondiente, y si el proyecto que has apoyado no llegara a la financiación mínima, tu aportación te será reembolsada (sin ningún coste adicional) en la misma cuenta que has utilizado para hacer la transacción. </p>\r\n\r\n<p>Que tengas un buen día!</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(11, 'Comunicación desde admin', 'Plantilla para un mensaje de comunicación enviado desde admin a los destinatarios seleccionados', 'El asunto lo pone el admin', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<blockquote>%CONTENT%</blockquote>\r\n<p>--<br />\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(15, 'Agradecimiento cofinanciadores al financiarse', 'Mensaje a los cofinanciadores de un proyecto cuando este consigue la financiación', 'El proyecto %SITENAME% se ha financiado', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Gracias a tu aportación y la de más personas, <span class="message-highlight-blue">%SITENAME%</span> se ha cofinanciado con éxito y ha pasado a segunda ronda!</p>\r\n<p>Pero esto no acaba aquí, todavía puedes hacer más por el procomún y los recursos compartidos siguiendo de cerca la evolución de <span class="message-highlight-blue">%SITENAME%</span>, que ahora entrará en fase de producción. También accediendo a %SITEURL% para descubrir otros proyectos destacados que te interesen y puedas cofinanciar o apoyar de otro modo para hacer realidad.</p>\r\n<p>Te recordamos que puedes participar en las conversaciones y la comunidad de <span class="message-highlight-blue">%SITENAME%</span> desde <span class="message-highlight-blue"><a href="%SITEURL%">%SITEURL%</a></span>.</p>\r\n<p>Seguimos :)</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(17, 'Aviso cofinanciadores proyecto fallido', 'Mensaje a los cofinanciadores de un proyecto cuando este caduca sin conseguir el mínimo', 'El proyecto %SITENAME% no ha logrado su objetivo mínimo :(', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>A pesar de tu inestimable aporte (mediante paypal), que queremos volverte a agradecer, nos ponemos en contacto contigo para comunicarte que el proyecto <span class="message-highlight-blue">%SITENAME%</span> no ha conseguido finalmente el apoyo mínimo de cofinanciación que necesitaba en Book-a.net y se ha archivado.</p>\r\n<p>No obstante, nos gustaría que consideradas la posibilidad de volver a cofinanciar con la misma cantidad, o la que consideres oportuna, algún otro de los proyectos actualmente en campaña.</p>\r\n<p> Se trata de propuestas igualmente interesantes y abiertas, en busca de diferentes apoyos para desarrollarse mediante la comunidad de Book-a.net, de la que ya consideramos que formas parte.</p>\r\n<p>Afectuosamente,</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(20, 'Notificación al autor proyecto supera primera ronda', 'Mensaje al autor de un proyecto cuando este pasa a segunda ronda', 'El proyecto %SITENAME% ha pasado a segunda ronda', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Enhorabuena! Tu proyecto <span class="message-highlight-blue">%SITENAME%</span> ha completado su financiación.</p>\r\n<p>Te recordamos que puedes encontrar el widget para publicitar tu proyecto en tu <span class="message-highlight-blue"><a href="%WIDGETURL%">Dashboard</a></span>.</p>\r\n<p>Mucha suerte en la segunda ronda!</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(21, 'Notificación al autor proyecto fallido', 'Mensaje al autor de un proyecto cuando este caduca sin conseguir el mínomo', 'El proyecto %SITENAME% ha caducado', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Sentimos que tu proyecto <span class="message-highlight-blue">%SITENAME%</span> haya caducado sin conseguir el apoyo mínimo dentro del plazo fijado. Esperamos que haya sido igualmente una buena experiencia, y te invitamos a seguir participando en Book-a.net y pensando en maneras que pueda serte de utilidad.</p>\r\n<p>Puedes consultar el resumen del proyecto en tu <span class="message-highlight-blue"><a href="%SUMMARYURL%">Dashboard</a></span>.</p>\r\n<p>Cordialmente,</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(28, 'Agradecimiento donativo', 'Mensaje al usuario aporta renunciando a la recompensa', 'Gracias por tu donativo al proyecto %SITENAME%', '', '<p>Hola <span class="message-highlight-red">%USERNAME%</span>,</p>\r\n<p>Gracias por cofinanciar el proyecto <span class="message-highlight-blue">%SITENAME%</span> con %AMOUNT% €. %DROPED%</p>\r\n<p>Como has renunciado a la recompensa individual que fija el proyecto, te recordamos que es un donativo fiscalmente desgravable.</p>\r\n<p>Muchas gracias de nuevo por tu generosidad!</p>\r\n<p>--<br />\r\n<p>Seguimos :)</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(29, 'Notificación de nuevo aporte al autor', 'Mensaje al autor de un proyecto cuando un nuevo aporte', 'Nuevo aporte al proyecto %SITENAME%', '', '<p>Hola <span class="message-highlight-red">%OWNERNAME%</span>,</p>\r\n<p>Tu proyecto <span class="message-highlight-blue">%SITENAME%</span> ha recibido un nuevo aporte de %AMOUNT% € de <span class="message-highlight-red">%USERNAME%</span>. Puedes enviarle un mensaje desde esta pagina <a href="%MESSAGEURL%">%MESSAGEURL%</a>. %DROPED%</p>\r\n<p>Te recordamos que puedes comunicarte con tus cofinanciadores desde tu <span class="message-highlight-blue"><a href="%SITEURL%/dashboard">Dashboard</a></span>.</p>\r\n<p>Seguimos!</p>\r\n<p>The Mailer<br>\r\n- - - - - - - - - - - - - - - - - - - -<br>\r\n<br>\r\n</p>', '');
INSERT INTO `template` VALUES(33, 'Boletin', 'Plantilla de newsletter', '', '', '<div style="width:590px;background-color:#ffffff;font-size:18px;padding:20px 20px 5px;" >\r\n<span style="font-size:21px;font-weight:bold;" >Hoy destacamos...</span>\r\n<p>texto newsletter</p>\r\n<p>adfasd fasdf asdf</p>\r\n</div>', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `text`
--

DROP TABLE IF EXISTS `text`;
CREATE TABLE `text` (
  `id` varchar(50) NOT NULL,
  `text_es` text NOT NULL,
  `text_en` text,
  `html` tinyint(1) DEFAULT NULL COMMENT 'Si el texto lleva formato html',
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Agrupacion de uso',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Textos de la web';

--
-- Volcar la base de datos para la tabla `text`
--

INSERT INTO `text` VALUES('blog-no_comments', '*_*blog-no_comments', NULL, NULL, 'new');
INSERT INTO `text` VALUES('blog-side-last_comments', '*_*blog-side-last_comments', NULL, NULL, 'new');
INSERT INTO `text` VALUES('blog-side-last_posts', 'Noticias recientes', '', NULL, 'general');
INSERT INTO `text` VALUES('blog-side-tags', 'Tags más habituales', '', NULL, 'general');
INSERT INTO `text` VALUES('booka-invest-continue', 'Continuar con el aporte', '', NULL, 'invest');
INSERT INTO `text` VALUES('booka-invest-fail', 'No se ha podido completar el aporte, inténtalo de nuevo o ponte en contacto con nosotr@s', '', NULL, 'invest');
INSERT INTO `text` VALUES('booka-invest-start', 'Orientativo para realizar el aporte', '', NULL, 'invest');
INSERT INTO `text` VALUES('booka-invest-total', 'Total aporte', '', NULL, 'invest');
INSERT INTO `text` VALUES('booka-menu-home', 'Booka', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-menu-messages', 'Comentarios', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-menu-needs', 'Necesidades', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-menu-supporters', 'Cofinanciadores', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-messages-send_message-button', 'Enviar comentario', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-messages-send_message-header', 'Escribe tu comentario acerca de este booka', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-rewards-header', 'Recompensas acumulativas', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-rewards-reward-limited', 'Límite de unidades', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('booka-rewards-reward-title', 'Recompensas', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-rewards-reward-units_left', 'Quedan %s unidades', '', NULL, 'invest');
INSERT INTO `text` VALUES('booka-rewards-rewards-title', 'Recompensas', NULL, NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-rewards-supertitle', 'Transferencias bookallow', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-side-investors-header', 'Cofinanciadores de este booka', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-support-supertitle', 'Financiación', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-view-categories-title', 'Categorias/Colecciones', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-view-metter-got', 'Conseguido', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-view-metter-investment', 'Financiamiento', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-view-metter-investors', 'Cofinancian', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-view-metter-minimum', 'Minimo', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('booka-view-metter-optimum', 'Optimo', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('claim-bookallow', 'libros evolutivos, flexibles y sensibles a sus lector@s', NULL, NULL, 'home');
INSERT INTO `text` VALUES('claim-bookateca', 'club editorial para impulsores de book-as', NULL, NULL, 'home');
INSERT INTO `text` VALUES('contact-email-field', 'Tu email:', '', NULL, 'about');
INSERT INTO `text` VALUES('contact-message-field', 'Tu mensaje:', '', NULL, 'about');
INSERT INTO `text` VALUES('contact-send_message-button', 'Enviar', '', NULL, 'about');
INSERT INTO `text` VALUES('contact-send_message-header', 'Envíanos un mensaje', '', NULL, 'about');
INSERT INTO `text` VALUES('contact-subject-field', 'Asunto:', '', NULL, 'about');
INSERT INTO `text` VALUES('costs-field-amount', 'Importe', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-field-cost', 'Concepto del coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-field-cost_en', 'Concepto del coste (inglés)', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-field-description', 'Descripción del coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-field-description_en', 'Descripción del coste (inglés)', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-field-required_cost-no', 'No imprescindible', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('costs-field-required_cost-yes', 'Imprescindible', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('costs-field-type', 'Tipo de coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-fields-main-title', 'Desglose de costes', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-main-header', 'Segundo paso: Costes', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('costs-total', '*_*costs-total', NULL, NULL, 'new');
INSERT INTO `text` VALUES('dashboard-header-main', 'Panel privado de usuario', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('dashboard-menu-access', 'Datos de acceso', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('dashboard-menu-home', 'Portada', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('dashboard-menu-invests', 'Aportaciones', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('dashboard-menu-preferences', 'Preferencias', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('dashboard-menu-profile', 'Perfil', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('dashboard-menu-profile-access', 'Datos para identificar tu cuenta de usuario', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('error-register-email-confirm', 'La confirmación de email no coincide', '', NULL, 'login');
INSERT INTO `text` VALUES('error-register-invalid-password', '*_*error-register-invalid-password', NULL, NULL, 'new');
INSERT INTO `text` VALUES('error-register-password-confirm', 'La confirmación de contraseña no coincide', '', NULL, 'login');
INSERT INTO `text` VALUES('error-register-pasword-empty', 'Tienes que poner una contraseña', '', NULL, 'login');
INSERT INTO `text` VALUES('error-register-userid', 'Tienes que poner un nombre de acceso', '', NULL, 'login');
INSERT INTO `text` VALUES('error-register-username', 'Tienes que poner un nombre público', '', NULL, 'login');
INSERT INTO `text` VALUES('error-user-email-empty', 'Tienes que poner tu email para que podamos contestarte', '', NULL, 'about');
INSERT INTO `text` VALUES('error-user-password-invalid', 'La contraseña debe tener al menos 6 caracteres', '', NULL, 'login');
INSERT INTO `text` VALUES('faq-ask-question', '¿Alguna pregunta?', 'Any question?', NULL, 'faq');
INSERT INTO `text` VALUES('faq-bookas-section-header', 'Sobre los contenidos', NULL, NULL, 'faq');
INSERT INTO `text` VALUES('faq-investors-section-header', 'Para impulsores', NULL, NULL, 'faq');
INSERT INTO `text` VALUES('faq-main-section-header', 'Sobre book-a', NULL, NULL, 'faq');
INSERT INTO `text` VALUES('faq-readers-section-header', 'Para lectores', NULL, NULL, 'faq');
INSERT INTO `text` VALUES('fatal-error-booka', 'El booka solicitado no exite', '', NULL, 'error');
INSERT INTO `text` VALUES('fatal-error-teapot', 'Teapot error', '', NULL, 'error');
INSERT INTO `text` VALUES('fatal-error-user', '*_*fatal-error-user', NULL, NULL, 'new');
INSERT INTO `text` VALUES('feed-timeago-justnow', 'Nada', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('feed-timeago-periods', 'segundo-segundos_minuto-minutos_hora-horas_día-días_semana-semanas_mes-meses_año-años_década-décadas', '', NULL, 'general');
INSERT INTO `text` VALUES('form-accept-button', 'Aceptar', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-add-button', 'Añadir', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-ajax-info', 'Este formulario se guarda según se va pasando por las secciones', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('form-apply-button', 'Aplicar', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-booka_status-campaing', 'En campaña', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-booka_status-cancelled', 'Descartado', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-booka_status-edit', 'En edición', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-booka_status-fulfilled', 'Producido', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-booka_status-review', 'En revisión', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-booka_status-shared', 'Disponible', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-booka_status-success', 'Financiado', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-errors-info', 'Total: %s | En este paso: %s', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('form-errors-no_errors', 'Ningún error!', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('form-errors-total', 'Hay %s errores en total', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('form-footer-errors_title', 'Errores detectados:', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-image_upload-button', 'Subir imagen', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-navigation_bar-header', 'Pasos para completar el formulario', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-next-button', 'Siguiente', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-remove-button', 'Quitar', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-self_review-button', 'Revisar', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-send_review-button', 'Enviar a revisión', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('form-upload-button', 'Subir', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('guide-booka-costs', 'En este segundo paso hay que desglosar los costes de producción por tareas', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('guide-booka-overview', 'En este primer paso hay que poner todos los datos del Booka y rellenar las areas de descripción', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('guide-booka-review', 'Este es el último paso de revisión. Si no hay errores se puede enviar el booka a revisión con un comentario. Si hay errores se tienen que arreglar.', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('guide-booka-rewards', 'En este cuarto paso hay que especificar que recompensas se ofrecen, el importe mínimo a aportar y si hay límite de unidades.', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('guide-booka-translate', 'En este paso se traducen los textos de descricción del booka al inglés', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('guide-dashboard-user-profile', 'Completa tu perfil para presentarte a la comunidad Book-a!', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('invest-address-address-field', 'Domicilio', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-address-country-field', 'Pais', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-address-header', 'Domicilio', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-address-location-field', 'Población', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-address-name-field', 'Nombre completo', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-address-nif-field', 'NIF', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-address-zipcode-field', 'CP', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-alert-investing', 'Vas a aportar', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-alert-lackamount', 'El aporte debe ser mayor a la recompensa elegida', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-alert-noreward', 'No has marcado ninguna recompensa, ¿es correcto?', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-alert-noreward_renounce', '¿Deseas renunciar a la recompensa y desgravar tu donativo?', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-alert-renounce', 'Renuncias pero no has puesto tu NIF para desgravar el donativo, ¿es correcto?', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-alert-rewards', 'Has elegido las siguientes recompensas:', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-amount', 'Importe', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-amount-error', 'No has puesto el importe que deseas aportar', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-amount-tooltip', 'Obtendrás la recompensa más alta posible, segun el importe aportado.', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-anonymous', 'Deseo que mi aporte sea anónimo', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-donation-header', 'Introduce los datos fiscales para el certificado de donativo 	', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-owner-error', 'Como gestor del booka, deberías tramitar tu auto-financiación desde el backoffice. Contacta con el administrador de la plataforma.', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-resign', 'Renuncio a cualquier recompensa', '', NULL, 'invest');
INSERT INTO `text` VALUES('invest-rewards-header', 'Recompensas', '', NULL, 'invest');
INSERT INTO `text` VALUES('leave-email-sended', 'Te hemos enviado un email para que confirmes la baja.', NULL, NULL, 'login');
INSERT INTO `text` VALUES('leave-process-completed', 'El proceso de baja se ha completado correctamente', NULL, NULL, 'login');
INSERT INTO `text` VALUES('leave-process-fail', 'No se ha podido completar el proceso de baja, ponte en contacto con nosotr@s.', NULL, NULL, 'login');
INSERT INTO `text` VALUES('leave-request-fail', 'No existe ninguna cuenta asociada al email.', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-access-button', 'entrar', '', NULL, 'login');
INSERT INTO `text` VALUES('login-access-header', 'Usuarios registrados', '', NULL, 'login');
INSERT INTO `text` VALUES('login-access-password-field', 'Contraseña', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-access-username-field', 'Nombre de acceso', '', NULL, 'login');
INSERT INTO `text` VALUES('login-fail', 'El nombre de acceso o la contraseña no son correctos', '', NULL, 'login');
INSERT INTO `text` VALUES('login-leave-button', 'Darse de baja', '', NULL, 'login');
INSERT INTO `text` VALUES('login-leave-header', 'Darse de baja de Book-a.net', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-leave-info', 'Para dar de baja tu usuario, introduce el email asociado a la cuenta.', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-leave-message', 'Déjanos un mensaje', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-oneclick-header', 'Accede con un solo click', '', NULL, 'login');
INSERT INTO `text` VALUES('login-recover-button', 'Recuperar', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-recover-email-field', 'Email asociado a la cuenta', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-recover-header', 'Recuperar la contraseña', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-recover-info', 'Introduce el nombre de acceso y/o el email asociado a la cuenta.', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-recover-link', 'Recuperar contraseña', '', NULL, 'login');
INSERT INTO `text` VALUES('login-recover-username-field', 'Nombre de acceso de la cuenta', NULL, NULL, 'login');
INSERT INTO `text` VALUES('login-register-button', 'Registrarse', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-conditions', 'Acepto las condiciones de uso de la plataforma Book-a.net, así­ como presto mi consentimiento para el tratamiento de mis datos personales. A tal efecto, el responsable de la plataforma ha establecido una <a href="/legal/privacy" target="_blank">polí­tica de privacidad</a> donde se puede conocer la finalidad que se le darán a los datos suministrados a través del presente formulario, así­ como los derechos que asisten a la persona que suministra dichos datos.', '', 1, 'login');
INSERT INTO `text` VALUES('login-register-confirm-field', 'Confirmar e-mail', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-confirm_password-field', 'Confirmar contraseña', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-email-field', 'E-mail', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-header', 'Nuevos usuarios', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-password-field', 'Contraseña', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-password-minlength', 'La contraseña debe tener mínimo 6 caracteres.', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-userid-field', 'Nombre de acceso', '', NULL, 'login');
INSERT INTO `text` VALUES('login-register-username-field', 'Nombre público', '', NULL, 'login');
INSERT INTO `text` VALUES('login-signin-facebook', 'Accede con facebook', '', NULL, 'login');
INSERT INTO `text` VALUES('login-signin-google', 'Accede con google', '', NULL, 'login');
INSERT INTO `text` VALUES('login-signin-linkedin', 'Accede con linkedin', '', NULL, 'login');
INSERT INTO `text` VALUES('login-signin-twitter', 'Accede con twitter', '', NULL, 'login');
INSERT INTO `text` VALUES('logo-booka', 'book-a.net', NULL, NULL, 'general');
INSERT INTO `text` VALUES('logo-bookallow', 'book-a/llow', NULL, NULL, 'home');
INSERT INTO `text` VALUES('logo-bookateca', 'book-a/teca', NULL, NULL, 'home');
INSERT INTO `text` VALUES('mandatory-booka-costs', 'Hay que poner al menos un coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('mandatory-booka-field-about', 'Tienes que poner alguna descripción acerca del proyecto', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('mandatory-booka-field-category', 'Tienes que seleccionar la categoría/colección', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('mandatory-booka-field-description', 'Tienes que rellenar la descripción general del libro', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('mandatory-booka-field-image', 'Tienes que poner al menos la imagen de la portada del libro', '', NULL, 'booka_public');
INSERT INTO `text` VALUES('mandatory-cost-field-amount', 'Tienes que poner el importe del coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('mandatory-cost-field-description', 'Tienes que poner una descripcion del coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('mandatory-cost-field-name', 'El coste tiene que tener un concepto', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('mandatory-cost-field-type', 'Tienes que marcar un tipo de csote. Si no hay ninguno adecuado contacta con el administrador.', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('mandatory-register-field-email', 'Tienes que poner el email', '', NULL, 'login');
INSERT INTO `text` VALUES('mandatory-reward-field-description', 'Es  obligatorio poner una descripción de la recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('mandatory-reward-field-name', 'Es obligatorio poner el nombre de la recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-about', 'Acerca de este libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-author', 'Autor', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-caption', 'Pie de foto', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-categories', 'Elegir la categoría / colección', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-collection', 'Colección', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-description', 'Descripción del proyecto booka para este libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-goal', 'Objetivo del libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-image_gallery', 'Galería de imágenes', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-info', 'Info adicional', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-keywords', 'Palabras clave', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-media', 'Video promocional', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-media_preview', 'Previsualización', NULL, NULL, 'new');
INSERT INTO `text` VALUES('overview-field-motivation', 'Motivación para el proyecto', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-name', 'Nombre del booka / título del libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-patron', 'Promotor', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-related', 'Datos relacionados', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-subtitle', 'Subtítulo', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-field-usubs', 'Usar universal subtitles', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-fields-images-title', 'Imagen  de la portada del libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('overview-main-header', 'Primer paso: Descripción', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('profile-about-header', 'Acerca de mi:', '', NULL, 'profile_public');
INSERT INTO `text` VALUES('profile-field-about', 'Cuéntanos algo sobre tí:', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-field-location', 'Lugar de residencia habitual:', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-field-name', 'Nombre público', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-field-url', 'URL', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-field-websites', 'Tus webs', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-fields-image-title', 'Tu imagen de perfil', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-fields-social-title', 'Tu identidad en las redes sociales', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-invested-header', '*_*profile-invested-header', NULL, NULL, 'new');
INSERT INTO `text` VALUES('profile-name-header', 'Nombre público', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('profile-social-header', 'Mi identidad social', '', NULL, 'profile_public');
INSERT INTO `text` VALUES('profile-webs-header', 'Mis webs', '', NULL, 'profile_public');
INSERT INTO `text` VALUES('project-messages-send_message-button', 'Enviar mensaje', '', NULL, 'profile_public');
INSERT INTO `text` VALUES('recover-email-sended', 'Te hemos enviado un email con el acceso para recuperar tu contraseña', NULL, NULL, 'login');
INSERT INTO `text` VALUES('recover-request-fail', 'No existe ninguna cuenta con esos datos', NULL, NULL, 'login');
INSERT INTO `text` VALUES('regular-admin_board', 'Panel admin', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-anonymous', 'Anónimo', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-ask', 'Pregunta', 'Ask', NULL, 'general');
INSERT INTO `text` VALUES('regular-by', 'Por:', 'By:', NULL, 'general');
INSERT INTO `text` VALUES('regular-dashboard', 'Mi Booka', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('regular-delete', 'Quitar', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('regular-edit', 'Editar', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-facebook', 'Facebook', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-facebook-url', 'http://www.facebook.com/', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-faq', 'Preguntas frecuentes', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-getit', 'Consíguelo', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-google', 'Google+', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-google-url', 'https://plus.google.com/', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-go_up', 'Subir', 'up', NULL, 'general');
INSERT INTO `text` VALUES('regular-header-about', 'Sobre Book-a.net', 'About', NULL, 'general');
INSERT INTO `text` VALUES('regular-header-blog', 'Blog', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-header-faq', 'FAQ', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-hello', 'Hola', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-highlights', 'Novedades:', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-investing', 'Aportando', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-invest_it', 'Impulsar', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-linkedin', 'Linkedin', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-linkedin-url', 'http://es.linkedin.com/in/', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-login', 'ACCEDER', 'LOG-IN', NULL, 'menu');
INSERT INTO `text` VALUES('regular-logout', 'Cerrar sesión', 'Logout', NULL, 'menu');
INSERT INTO `text` VALUES('regular-main-header', 'book-a.net', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-no', 'No', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-preview', 'Previsualizar', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-read_more', 'Leer más', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-save', 'Guardar', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-see_more', 'Ver más', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-send_message', 'Enviar un mensaje', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-share_this', 'Compartir', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-sorry', 'Lo sentimos', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-total', 'Total', '', NULL, 'general');
INSERT INTO `text` VALUES('regular-twitter', 'Twitter', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-twitter-url', 'http://twitter.com/#!/', NULL, NULL, 'general');
INSERT INTO `text` VALUES('regular-yes', 'Sí', NULL, NULL, 'general');
INSERT INTO `text` VALUES('review-main-header', 'Último paso: Revisión', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('review-send-comment', 'Comentario para el revisor', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-field-reward-amount', 'Importe', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-field-reward-description', 'Descripción de la recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-field-reward-description_en', 'Descripción de la recompensa (inglés)', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-field-reward-reward', 'Concepto de la recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-field-reward-reward_en', 'Nombre de la recompensa (inglés)', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-field-reward-type', 'Tipo de recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-field-reward-units', 'Limite de unidades (cero = ilimitadas)', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-fields-reward-title', 'Transferencias', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('rewards-main-header', 'Tercer paso: Recompensas', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('social-account-facebook', 'http://www.facebook.com/vibokworks', '', NULL, 'general');
INSERT INTO `text` VALUES('step-costs', 'Presupuesto', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('step-milestones', 'Objetivos', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('step-overview', 'Descripción', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('step-rewards', 'Transferencias', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('step-translate', 'Traducir', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-book-about', 'Texto que aparece en la caja de booka', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-author', 'Autor/es del libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-caption', 'Texto que aparece en la parte inferior de la imagen en la caja de booka', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-category', 'Se pueden marcar varias categorías', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-collection', 'Seleccionar la colección a la que pertenece', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-comment', 'Sugerencia de poner un comentario para el revisor', NULL, NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-cost-amount', 'Importe del coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-cost-cost', 'Concepto del coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-cost-description', 'Descripción del coste', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-cost-type', 'Tipo de tarea', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-costs', 'Desglose de costes', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-description', 'Descripción larga de este booka (80 palabras aprox)', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-goal', 'tooltip-booka-goal', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-image', 'Poner primero la imagen de la portada del libo en proporción 275x240. Si la portada es apaisada o estrecha, redimensionar el lado más alto y rellenar con color de fondo igual a la caja del proyecto.', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-info', 'Información adicional bajo el nombre del autor', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-keywords', 'Palabras clave de este trabajo dentro del sector', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-media', 'tooltip-booka-media', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-motivation', 'tooltip-booka-motivation', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-name', 'Nombre del libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-patron', 'Texto presentación Promotor', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-related', 'tooltip-booka-related', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-reward-description', 'Descripción de la recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-reward-reward', 'Concepto de la recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-reward-type', 'Ver la descripción de cada tipo de recompensa en esta columna', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-rewards', 'Recomnpensas', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-subtitle', 'Subtítulo para el nombre del libro', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-booka-usubs', 'Marca la casilla en caso de que hayas subtitulado a otros idiomas el vídeo mediante Universal Subtitles: http://www.universalsubtitles.org/', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-project-reward-amount', 'Es la cantidad mínima que se debe aportar para conseguir esta recompensa', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('tooltip-project-reward-units', 'Conviene siempre limitar las unidades', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('top-menu-mybooka', 'MI BOOKA', NULL, NULL, 'menu');
INSERT INTO `text` VALUES('top-menu-myedit', 'Editar mi cuenta', NULL, NULL, 'menu');
INSERT INTO `text` VALUES('translate-main-header', 'Traducir al inglés', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('user-activate-fail', 'No se ha podido completar el registro , ponte en contacto con nosotros', '', NULL, 'login');
INSERT INTO `text` VALUES('user-activate-success', 'El registro se ha completado satisfactoriamente, el email asociado a la cuenta ha sido confirmado.', '', NULL, 'login');
INSERT INTO `text` VALUES('user-changeemail-fail', 'No se ha podido completar el cambiode email, ponte en contacto con nosotros.', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('user-changeemail-title', 'Cambiar el email asociado a la cuenta', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('user-changepass-confirm', 'Confirmar la nueva contraseña:', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('user-changepass-new', 'Nueva contraseña:', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('user-changepass-title', 'Cambiar la contraseña', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('user-email-change-sended', 'Te hemos enviado un correo para completar el proceso de cambio de email', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('user-email-not_confirmed', 'No puedes gestionar tus datos de acceso porque el email asociado a tu cuenta no está confirmado. Ponte en contacto con nosotros.', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('user-login-required-access', 'Debes acceder para ver esta sección', NULL, NULL, 'general');
INSERT INTO `text` VALUES('user-login-required-to_invest', 'Necesitas registrarte para cofinanciar un booka', '', NULL, 'general');
INSERT INTO `text` VALUES('user-login-required-to_see', '*_*user-login-required-to_see', NULL, NULL, 'new');
INSERT INTO `text` VALUES('user-message-send_personal-header', 'Envía un mensaje personal', '', NULL, 'profile_public');
INSERT INTO `text` VALUES('user-password-changed', 'La contraseña ha sido modificada correctamente', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('user-profile-saved', 'Los datos de tu perfil han sido actualizados', NULL, NULL, 'dashboard');
INSERT INTO `text` VALUES('user-register-access_data', 'Tus datos de acceso son Usuario: <strong>%s</strong> Contraseña: <strong>%s</strong>', NULL, 1, 'login');
INSERT INTO `text` VALUES('user-register-must_accept', '*_*user-register-must_accept', NULL, NULL, 'new');
INSERT INTO `text` VALUES('user-register-success', 'El usuario se ha registrado correctamente. A continuación recibirás un email para confirmar la cuenta.', '', NULL, 'login');
INSERT INTO `text` VALUES('user-save-fail', 'Ha fallado al actualizar los datos del usuario', '', NULL, 'dashboard');
INSERT INTO `text` VALUES('validate-booka-costs-any_error', 'Hay algún error en alguno de los costes, revísalos.', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('validate-booka-field-description', 'La descripción del libro debe tener al menos 80 palabras', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('validate-booka-rewards', 'Falta algún dato en las recompensas', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('validate-booka-rewards-any_error', 'Hay algún error en alguna de las recompensas, revísalas.', '', NULL, 'booka_form');
INSERT INTO `text` VALUES('validate-register-value-email', 'El email introducido no es correcto', '', NULL, 'login');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `about` text,
  `active` tinyint(1) NOT NULL,
  `avatar` int(11) DEFAULT NULL,
  `twitter` tinytext,
  `facebook` tinytext,
  `google` tinytext,
  `linkedin` tinytext,
  `worth` int(7) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `token` tinytext NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se ve publicamente',
  `confirmed` int(1) NOT NULL DEFAULT '0',
  `lang` varchar(2) NOT NULL DEFAULT 'es' COMMENT 'Idioma preferido del usuario',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user`
--

INSERT INTO `user` VALUES('root', 'Super administrador', '', 'mail', '', '', 1, NULL, '', '', '', '', 0, '2011-08-31 19:54:11', '2012-06-08 05:34:12', '', 0, 1, 'es');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_login`
--

DROP TABLE IF EXISTS `user_login`;
CREATE TABLE `user_login` (
  `user` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `oauth_token` text NOT NULL,
  `oauth_token_secret` text NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user`,`oauth_token`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user_login`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_personal`
--

DROP TABLE IF EXISTS `user_personal`;
CREATE TABLE `user_personal` (
  `user` varchar(50) NOT NULL,
  `real_name` varchar(255) DEFAULT NULL,
  `nif` varchar(15) DEFAULT NULL COMMENT 'Guardar sin espacios ni puntos ni guiones',
  `phone` varchar(9) DEFAULT NULL COMMENT 'guardar sin espacios ni puntos',
  `address` tinytext,
  `zipcode` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Datos personales de usuario';

--
-- Volcar la base de datos para la tabla `user_personal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_prefer`
--

DROP TABLE IF EXISTS `user_prefer`;
CREATE TABLE `user_prefer` (
  `user` varchar(50) NOT NULL,
  `news` int(1) NOT NULL DEFAULT '0',
  `mailing` int(1) NOT NULL DEFAULT '0',
  `bookas` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Preferencias de notificacion de usuario';

--
-- Volcar la base de datos para la tabla `user_prefer`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_id` varchar(50) NOT NULL,
  `role_id` varchar(50) NOT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_FK` (`user_id`),
  KEY `role_FK` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `user_role`
--

INSERT INTO `user_role` VALUES('root', 'root', NULL);
INSERT INTO `user_role` VALUES('root', 'superadmin', NULL);
INSERT INTO `user_role` VALUES('root', 'user', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_web`
--

DROP TABLE IF EXISTS `user_web`;
CREATE TABLE `user_web` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `url` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Webs de los usuarios' AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `user_web`
--


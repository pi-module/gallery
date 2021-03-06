CREATE TABLE `{photo}` (
  `id` int(10) unsigned NOT NULL  auto_increment,
  `title` varchar(255) NOT NULL ,
  `alias` varchar(255) NOT NULL,
  `album` int(10) unsigned NOT NULL,
  `information` text   NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `size` float NOT NULL,
  `resx` int(10) unsigned NOT NULL,
  `resy` int(10) unsigned NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `hits` int(10) unsigned NOT NULL,
  `comments` int(10) unsigned NOT NULL,
  `download` int(10) unsigned NOT NULL,
  `create` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `point` int(10) NOT NULL,
  `count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `album` (`album`),
  KEY `title` (`title`),
  KEY `create` (`create`),
  KEY `status` (`status`),
  KEY `author` (`author`),
  KEY `order` (`order`),
  KEY `photo_list` (`album`, `status`),
  KEY `author_list` (`author`, `status`)
);

CREATE TABLE `{album}` (
  `id` int(10) unsigned NOT NULL  auto_increment,
  `category` int(5) unsigned NOT NULL ,
  `title` varchar(255) NOT NULL ,
  `alias` varchar(255) NOT NULL,
  `information` text,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `create` int(10) unsigned NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `photo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `category` (`category`),
  KEY `title` (`title`),
  KEY `create` (`create`),
  KEY `status` (`status`),
  KEY `author` (`author`),
  KEY `order` (`order`),
  KEY `album_list` (`category`, `status`),
  KEY `author_list` (`author`, `status`)
);

CREATE TABLE `{category}` (
  `id` int(10) unsigned NOT NULL  auto_increment,
  `pid` int(5) unsigned NOT NULL ,
  `title` varchar(255) NOT NULL ,
  `alias` varchar(255) NOT NULL,
  `information` text   NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `create` int(10) unsigned NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `pid` (`pid`),
  KEY `title` (`title`),
  KEY `create` (`create`),
  KEY `status` (`status`),
  KEY `order` (`order`),
  KEY `category_list` (`pid`, `status`)
);

CREATE TABLE `{photographer}` (
  `id` int(10) unsigned NOT NULL  auto_increment,
  `author` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `author` (`author`),
  KEY `count` (`count`)
);
# Authentication

By default, Babble uses Kohana's Auth and ORMs implementation of it with a
slightly modified users table schema, however, we use an abstract class
[API_User] that handles authentication so you can add whatever authentication
behind that that you wish. You will want to view [Babble_API_User] to see what
abstract methods should be defined in your own implementation. It should be
fairly simple for you to add your own authentication functionality.

For the rest of this document we'll be discussing our own implementation.

Babble has been coupled (loosely) with the Auth and ORM modules. If you are
going to use our authentication functionaly you'll need to enable both of these
modules. It's a good idea to get an understanding of how the ORM module
integrates with the Auth module. For example, the ORM module has an idea of
"roles" which is required for login checks and to login. You could of course
extend this further and create the roles how you wish (or do away with them).

Since we're using ORM's implementation of Auth your SQL schema should match the
schema found in MODPATH/orm/auth-schema-mysql.sql. In addition to that schema,
we have an additiontal field added to the users table to determine if the user
is an API user. For example, `api_user tinyint(1) NOT NULL DEFAULT 0`.

So the DB schema required for our Auth implementation would look like so.
~~~
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` int(11) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `roles_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`level`, `name`, `description`) VALUES(0, 'root', 'Root user, has access to everything.');
INSERT INTO `roles` (`level`, `name`, `description`) VALUES(100, 'admin', 'Administrative user, has access to most things.');
INSERT INTO `roles` (`level`, `name`, `description`) VALUES(500, 'user-write', 'General user, has write access.');
INSERT INTO `roles` (`level`, `name`, `description`) VALUES(1000, 'user-read', 'General user, can only view.');

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `roles_users_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(254) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `api_user` tinyint(1) NOT NULL DEFAULT 0,
  `logins` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_login` int(10) UNSIGNED,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `users_username` (`username`),
  UNIQUE KEY `users_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created` int(10) UNSIGNED NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_tokens_token` (`token`),
  KEY `user_tokens_user_id` (`user_id`),
  KEY `user_tokens_expires` (`expires`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE roles_users
  ADD CONSTRAINT `roles_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
  
ALTER TABLE user_tokens
  ADD CONSTRAINT `user_tokens_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
~~~

Going further, you'll want to get an understanding the Auth and ORM modules.

<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'forumkajian');

/** MySQL database username */
define('DB_USER', 'forumkajian');

/** MySQL database password */
define('DB_PASSWORD', 'H@i14090');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'e36J<D|jGz72U-L&Y7^F_ju$cFNk-!Y/W|lQ12%Dg,gKf,$sV/^T(/0cLQl0Tl=x');
define('SECURE_AUTH_KEY',  '63E`^}Q+>H@D,Z_(5nk+2:Tr<v10(eMaxZ>GMFqeX9VN$HM0:iM/k/ZVNl{l:My}');
define('LOGGED_IN_KEY',    'Ec%HA*)~ky!A<P}SNGfni/9;W^;a|{oYM1sI4]cas]u!=-}V+l2}b+IZCHb2^RB%');
define('NONCE_KEY',        ':P#X4t#!rpoMFfrHSd[ZEx:Phv2C5SiY)P5G9d(F4V%G-Lmv#4a/(X=PG=$~#MKP');
define('AUTH_SALT',        'R6uf6o(Tii)1+c)~|e)w,l[!{7GRg]`~,9f%UVW-tHF8g lvrQ]/{zvpY#FkJi~k');
define('SECURE_AUTH_SALT', '|U00-r+<w3NI?py.jlBAR|~E<g]AIF!|nh3a2y=!nl_s%i)L/};~au;F2x8t}SFX');
define('LOGGED_IN_SALT',   'd(sjvz?oS8|J+gB6Egq~WfDk68w P0;T)/l@UDqY5A_bLFU)a3 x^:TG54l[Gf).');
define('NONCE_SALT',       'JdCt?-A d1$x<Ie+WPa34uUh9#3.cRo#X?t<C:#@nr5V:}XRdL{(yJ?1,_dO?(Pp');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');


/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

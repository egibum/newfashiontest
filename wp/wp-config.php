<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'aivis_blog');

/** MySQL database username */
define('DB_USER', 'aivis_blog');

/** MySQL database password */
define('DB_PASSWORD', 'Siladep11');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '|EMks,v5p$N&+,u*A4^cA?L@8 D[j#VfR-yQT.e0EvJ!X IVKyGJN~{x)I|WTtGQ');
define('SECURE_AUTH_KEY',  'o_TFQItc-y5$5TPsmp-z:7 @~2(`+q,R-g6%,~@stmc!ALFu.}1}Mr(Mg.$$z< k');
define('LOGGED_IN_KEY',    'u=+af>60u^b<Q$M^-Eae3wX9+Fv+bsvAi/--G|.__&4wY!SZy`T|P4`G>&Pyze~_');
define('NONCE_KEY',        'Rox>[LsgS||IEB#dK2j+=SZ=0Kc1!Y Yf+-&Egy$?Vm]-{LKC/l!Qk.it]+]T~i7');
define('AUTH_SALT',        '$Ig&-]*oeVSiVGs{Z5on@+O&};UkRi0k*_zCbp|El.dI8Teo)@q:N+B 1-Xh+ @q');
define('SECURE_AUTH_SALT', 's 8m{M12;<TDSs-f-[|t3I5bL+3S:|pnC-A2^ci09/DTw6t,#a`NkH-+*qV_j9~s');
define('LOGGED_IN_SALT',   'mz35$3>4+gYA6dKma,Zs(.yh,-2-?N?KAC;OA:SvhKej,gFHxwpmNCJ4Nh)gfhf&');
define('NONCE_SALT',       'iaNdZ]!aQbBR[#$HE33x+X!xGz^kYo+K0i9@i)agx5i_lCE61*[>|x6L8nb{~B2I');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'en_GB');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

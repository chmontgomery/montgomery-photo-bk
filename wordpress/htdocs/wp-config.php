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
define('DB_NAME', 'bitnami_wordpress');

/** MySQL database username */
define('DB_USER', 'bn_wordpress');

/** MySQL database password */
define('DB_PASSWORD', '5c546adc64');

/** MySQL hostname */
define('DB_HOST', 'localhost:3306');

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
/* Substitution already done */
define('AUTH_KEY', '20a83f527e551881f01a6b2dfbbbafc562c374b76b9ae1f0d9e15f27856304ec');
define('SECURE_AUTH_KEY', '3223e3a4c2004e7606b3d5f229e7ae3959700b453d300e8fd274b9ab37380324');
define('LOGGED_IN_KEY', 'ecdf3c95982d1f4b1519d5ff9bd793188ade43042d4e5f5db898956e51ceee00');
define('NONCE_KEY', '7d72d73dbfd1e10332b26246ad2ef593ce11b362661cd8c1919b03af732b74af');
define('AUTH_SALT', 'ba77251800542ef2e4c856d213e65f8b85718a32dc2c910402aaf4b49be4e329');
define('SECURE_AUTH_SALT', '275ab9ea3627ad78714424ab5164bb378e9d0d5c992bdf8989bf52984f9ca8c0');
define('LOGGED_IN_SALT', '541c149db5462824c6b3299ba8b98617e5881d4277a0174af15313798ee3bd98');
define('NONCE_SALT', '50148b3a1299b2e5bac3552b74907951f1416528f735166f6de668d87afad4d7');

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
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */
/**
 * The WP_SITEURL and WP_HOME options are configured to access from any hostname or IP address.
 * If you want to access only from an specific domain, you can modify them. For example:
 *  define('WP_HOME','http://example.com');
 *  define('WP_SITEURL','http://example.com');
 *
*/

define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/wordpress');
define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] . '/wordpress');


/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define('FS_METHOD', 'ftpext');
define('FTP_BASE', '/opt/bitnami/apps/wordpress/htdocs/');
define('FTP_USER', 'bitnami');
define('FTP_PASS', 'i5LgyfyzaHD6MEFLjZpC5LbQletOced8QVHSFs00iEi8HFRLJV');
define('FTP_HOST', '127.0.0.1');
define('FTP_SSL', false);


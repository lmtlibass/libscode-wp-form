<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'libs_plugin' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Obx99(;(Thl{Gq/4j/&q[3rL]HRna:>786$oc}}+ED{G`%;0S@))aE7|.%KD=j@3' );
define( 'SECURE_AUTH_KEY',  '72WKEt+p+`le2>c3:##kq<T >7@`S3y:W6z [I[}1~mmo5L6i^d|EY.xz]3AByy*' );
define( 'LOGGED_IN_KEY',    ',%r(NsTI|mwky|)Jqql#k7:Fem2DLH1cZOXtOn|39#KDox4^^=UsHjmSM+l{TYcN' );
define( 'NONCE_KEY',        'fUWm4*gXr{;X47xve%+re!LIGP1Hhdt;S-pOI@nWBl/`-OxdN<v!^un5eoW]u;`B' );
define( 'AUTH_SALT',        'uP{`Y;bnt^i!5~YQY:C$;Q.?1g 2;dxJy>qIQaQlt[detcB_,K#eR]/4J!o]7m.i' );
define( 'SECURE_AUTH_SALT', 'eWR&au]PF:CC~h1,V-TUPB>K:9jV~%m@;~=y<8L]xL=XPtD2nu.xZQ@} ?c1$OSN' );
define( 'LOGGED_IN_SALT',   'Jn;r)=79~:7QX8U1Z:9sSq~QIs6)VzLu^a?;xv  wt^N(iO&~JS]`Tb4Dz[RU;B7' );
define( 'NONCE_SALT',       'dN3i!Z?{3*;u)=20zx~7V~6VKI4Q_22./U]=cnX;{PQ lN0uDr7#A`rG$31R~<wh' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', '64aa9febe4e2a5a1ac69e1ac3f7f5edb353f8a08b3151dde' );

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
define( 'AUTH_KEY',         'FmH2(LB_zkmq{ritCCnBUk?vOzm7(qzk*<OimT[P`(V-1C:NhdGnJY Ez,PVA:4O' );
define( 'SECURE_AUTH_KEY',  'J4#v=+[j@Hcg,<A>>li5`s __p|r}u>(h6u[-z]<CDna?acdihkwIM!# !w#w4pX' );
define( 'LOGGED_IN_KEY',    '8tr>s]T{Rn_SlV?.6TPD{;)!KFw*z(o>u:s$[l8Uhu?D`#n?NC *>/SVZfMm+<b.' );
define( 'NONCE_KEY',        '>~Y9s!i$#wabbMInB*~36Hq5&j+eT^5UiW{W UsBG*8]L*<Rda(9 EjYFZEF.,w&' );
define( 'AUTH_SALT',        'L`c3Y59|+[@BH^RB/h.OYEM]Bt9_*})A:AHKdo4u}cr[m4Hm)IugI(E0&4lO079|' );
define( 'SECURE_AUTH_SALT', 'BH?t)fAc*0O4Th;_Rmmk1}>{-dm9gaB-CnKCs@PQEFXcjU=ik}ni*8M{WO]8OOMk' );
define( 'LOGGED_IN_SALT',   'Qr*z,qwiiCS+!q~VUhE3^{|xdQ(^|;#TC&g.SSHOk,r=aVoh+@w;`Mc!Z~TQbp:i' );
define( 'NONCE_SALT',       'Ft.vv9kgilL@zKYH[M$rAO3wy5LMWdy4d{=WWB{)N5MO[#cNN`7AYw2lg3de{FR2' );

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

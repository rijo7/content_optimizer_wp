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
define( 'DB_NAME', 'content_optimizer_db' );

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
define( 'AUTH_KEY',         ';i4IIn9xa=o,A0w0iMMB=]pRZ &.YO2!o{StxcrEE(6BnQ m?1jfMVPZ4)0ev}Ku' );
define( 'SECURE_AUTH_KEY',  '| sXvNpr9lLEonMA=vFF?}s4C6A{(D0(n)W1Fssfj{!+`5xLQ(css::$z}2rt%h7' );
define( 'LOGGED_IN_KEY',    'i;Ef1IDw~Qt(<c0mk^%ZOd2h|S/AhC^!rqcPR,Su}s})w=;h0!mfY.G*8SoN@e@J' );
define( 'NONCE_KEY',        ')99%:U~5Nn) xjRB*&C1^DCVb~;`!E9f)7`CGvc(.h1eIf^:yqZC,mYi,;[[%v9)' );
define( 'AUTH_SALT',        'GUJx*jV-u`LM,Sb4g)FTsyS~}Y&rA8I!=AL2g~t_E Dz0Ri]f13JyqhK.#fr&l_^' );
define( 'SECURE_AUTH_SALT', ']5AuJBru$41h6wj ri<fv?dbkPr9,(ZSFbMh^V +$K6.>~ujdlRZg:p{56:@E.D5' );
define( 'LOGGED_IN_SALT',   'dx27xS2q0,hrsL~ot7&:JT(`Bpr-onv1dX}$)<{]&5I+pci cgo5D4>(JMugwL_,' );
define( 'NONCE_SALT',       '~,KYE#mvc~SKxpsPG+Qu`WLbx7NW>_X;B(`ilG@)A~$2tPeI>jSEKEogQN)b-ua`' );

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

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
define( 'DB_NAME', 'dbju7syehvtkpy' );

/** MySQL database username */
define( 'DB_USER', 'u2hupmsezztx8' );

/** MySQL database password */
define( 'DB_PASSWORD', '66xp63g36cbq' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'D1(#m.94E6VaD!AXRk{44eq3{a`>@pwyhy6%mK^Y>P_XvTX1yy$iDhbZ@Pkj;C6/' );
define( 'SECURE_AUTH_KEY',   'bSdw4jh:2DQI#=I6o7Sg!F,(b8.`,*2mG+38H.=q7f*|kT)~pY<]|wU-Sng/17cc' );
define( 'LOGGED_IN_KEY',     'D?^:/b[Z)J2%%0$m[hYcAwqtcvDq$ZKfKD=YbIWEM/1$ozdS>T(lId=i~30d-Mzr' );
define( 'NONCE_KEY',         'l|A-L:dgbf%p{,!+EMqxsm=46.%TfPBtG;YMhJrs/T#X9nq9o~1uu6v+abcYDy? ' );
define( 'AUTH_SALT',         'yidOmj Hq%}GfQvX$sHM+hOu0g9g{8?^R4i]_U)aZcKX}8pt&LBnsgB#`3)$Ca~X' );
define( 'SECURE_AUTH_SALT',  '[!tb]XkNi^)F{k3cQ.7.^6Evfc=nE4AK{/>7nzh#(P~>=FhI5w_~ENd&+qgf(7@t' );
define( 'LOGGED_IN_SALT',    ';:}+lmJODjjO hz,1g:NmuBnDi`FPtq$EzFVhc=oaqTMdrtnGb1c@*#hRo*3JPbz' );
define( 'NONCE_SALT',        'hB$_|#[[&Mx<`B^Sniz(:dnAiwR@#hJq?xoZv$!7nT@!Lhtsi*pdxKUF}T9H0SRk' );
define( 'WP_CACHE_KEY_SALT', 'ySl//z=OlYHY<O0aGYDnnO9GUZar`g@<l?TVvTHzKT{lCD2N*`Y;O1VTZeNS;`Wb' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'qzc_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system

<?php
/**
 * LRMA Rock — Theme Functions
 */

// ─── Theme Setup ──────────────────────────────────────────────────────────────
function lrma_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );

    add_image_size( 'lrma-hero',  1920, 900,  true );
    add_image_size( 'lrma-card',  800,  450,  true );
    add_image_size( 'lrma-thumb', 400,  225,  true );
    add_image_size( 'lrma-sq',    300,  300,  true );

    register_nav_menus( [
        'primary' => 'Galvenā Navigācija',
        'footer'  => 'Kājenes Navigācija',
    ] );

    load_theme_textdomain( 'lrma', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'lrma_setup' );

// ─── Enqueue ──────────────────────────────────────────────────────────────────
function lrma_enqueue() {
    wp_enqueue_style(
        'lrma-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap',
        [], null
    );
    wp_enqueue_style( 'lrma-style', get_stylesheet_uri(), [ 'lrma-fonts' ], '2.2.0' );

    wp_enqueue_script( 'lrma-main', get_template_directory_uri() . '/assets/js/main.js', [], '2.0.0', true );
    wp_localize_script( 'lrma-main', 'lrmaData', [
        'radioStream' => get_theme_mod( 'radio_url', 'https://rockradio.lv' ),
        'homeUrl'     => home_url( '/' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'lrma_enqueue' );

// ─── Custom Post Type: Koncerti ───────────────────────────────────────────────
function lrma_register_cpt() {
    register_post_type( 'koncerti', [
        'labels' => [
            'name'          => 'Koncerti',
            'singular_name' => 'Koncerts',
            'add_new_item'  => 'Pievienot Koncertu',
        ],
        'public'        => true,
        'has_archive'   => true,
        'supports'      => [ 'title', 'editor', 'thumbnail' ],
        'menu_icon'     => 'dashicons-tickets-alt',
        'show_in_rest'  => true,
    ] );
}
add_action( 'init', 'lrma_register_cpt' );

// ─── Concert Meta Fields ──────────────────────────────────────────────────────
function lrma_register_concert_meta() {
    foreach ( [ 'concert_date', 'concert_venue', 'concert_ticket_url' ] as $field ) {
        register_post_meta( 'koncerti', $field, [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ] );
    }
}
add_action( 'init', 'lrma_register_concert_meta' );

// ─── Customizer ───────────────────────────────────────────────────────────────
function lrma_customizer( $wp_customize ) {
    $wp_customize->add_section( 'lrma_options', [
        'title'    => 'LRMA Settings',
        'priority' => 30,
    ] );

    $fields = [
        'radio_url'        => [ 'Radio Website URL',          'url',  'https://rockradio.lv' ],
        'radio_stream_url' => [ 'Radio Stream URL (mp3/aac)', 'url',  'https://c22.radioboss.fm/stream/318' ],
        'radio_meta_url'   => [ 'Radio Metadata JSON URL',    'url',  'https://c22.radioboss.fm/w/nowplayinginfo?u=318' ],
        'social_facebook'  => [ 'Facebook URL',            'url',  '' ],
        'social_instagram' => [ 'Instagram URL',           'url',  '' ],
        'social_youtube'   => [ 'YouTube URL',             'url',  '' ],
        'social_twitter'   => [ 'Twitter/X URL',           'url',  '' ],
        'site_email'       => [ 'Contact Email',           'text', 'info@lrma.lv' ],
    ];

    foreach ( $fields as $key => [ $label, $type, $default ] ) {
        $wp_customize->add_setting( $key, [
            'default'           => $default,
            'sanitize_callback' => $type === 'url' ? 'esc_url_raw' : 'sanitize_text_field',
        ] );
        $wp_customize->add_control( $key, [
            'label'   => $label,
            'section' => 'lrma_options',
            'type'    => $type,
        ] );
    }
}
add_action( 'customize_register', 'lrma_customizer' );

// ─── Helpers ──────────────────────────────────────────────────────────────────
add_filter( 'excerpt_length', fn() => 28 );
add_filter( 'excerpt_more',   fn() => '…' );

function lrma_get_cat_name() {
    $cats = get_the_category();
    return $cats ? esc_html( $cats[0]->name ) : 'Jaunumi';
}

function lrma_get_cat_link() {
    $cats = get_the_category();
    return $cats ? esc_url( get_category_link( $cats[0]->term_id ) ) : home_url( '/' );
}

function lrma_thumb( $size = 'lrma-card', $fallback = true ) {
    if ( has_post_thumbnail() ) {
        return get_the_post_thumbnail_url( null, $size );
    }
    $external = get_post_meta( get_the_ID(), 'lrma_hero_image', true );
    if ( $external ) {
        return esc_url( $external );
    }
    if ( $fallback ) {
        return get_template_directory_uri() . '/assets/img/placeholder.svg';
    }
    return '';
}

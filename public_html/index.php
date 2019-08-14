<?php

// connect to WordPress
define( 'WP_USE_THEMES', false );
require( '../wp-blog-header.php' );

/**
 * Functions.
 */

function display_code_begin()
{
    echo "<pre style=\"max-height:200px;overflow-y:scroll;z-index:9999;position:relative;white-space:pre-wrap;word-wrap:break-word;padding:10px 15px;border:1px solid #fff;background-color:#161616;text-align:left;line-height:1.5;font-family:Courier;font-size:16px;color:#fff;\">";
}

function display_code_end()
{
    echo "</pre>";
}

function get_attachment_permalinks()
{
    $permalinks = [];

    $ids = get_posts([
        'post_type'      => 'attachment',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    foreach ( $ids as $id )
        $permalinks[] = wp_get_attachment_url( $id );

    return $permalinks;
}

function get_post_type_permalinks( $post_type )
{
    if ( $post_type == 'attachment' )
        return get_attachment_permalinks();

    $permalinks = [];

    $multilingual = ( function_exists( 'icl_object_id' ) && is_post_type_translated( $post_type ) );

    if ( !$multilingual )
    {
        $ids = get_posts([
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        foreach ( $ids as $id )
            $permalinks[] = get_permalink( $id );
    }

    if ( $multilingual )
    {
        $wpml_langauges = apply_filters( 'wpml_active_languages', NULL, [] );

        foreach ( $wpml_langauges as $lang )
        {
            do_action( 'wpml_switch_language', $lang['code'] );

            $ids = get_posts([
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);

            foreach ( $ids as $id )
                $permalinks[] = get_permalink( $id );
        }

        do_action( 'wpml_switch_language', NULL );
    }

    return $permalinks;
}

function get_tax_permalinks( $taxonomy )
{
    $permalinks = [];

    $multilingual = ( function_exists( 'icl_object_id' ) && is_taxonomy_translated( $taxonomy ) );

    if ( !$multilingual )
    {
        $ids = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'fields'     => 'ids',
        ]);

        foreach ( $ids as $id )
            $permalinks[] = get_term_link( $id, $taxonomy );
    }

    if ( $multilingual )
    {
        $wpml_langauges = apply_filters( 'wpml_active_languages', NULL, [] );

        foreach ( $wpml_langauges as $lang )
        {
            do_action( 'wpml_switch_language', $lang['code'] );

            $ids = get_terms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'fields'     => 'ids',
            ]);

            foreach ( $ids as $id )
                $permalinks[] = get_term_link( $id, $taxonomy );
        }

        do_action( 'wpml_switch_language', NULL );
    }

    return $permalinks;
}

function display_all_posts( $post_type = false )
{
    if ( !$post_type )
        return false;

    $permalinks = get_post_type_permalinks( $post_type );

    echo '<h2>'.$post_type.' ('.count( $permalinks ).')</h2>';
    display_code_begin();
    if ( !empty( $permalinks ) ):
        foreach ( $permalinks as $permalink )
            echo '<a href="'.$permalink.'" target="_blank">'.$permalink.'</a><br>';
    else:
        echo 'No posts.';
    endif;
    display_code_end();
}

function display_all_terms( $taxonomy = false )
{
    if ( !$taxonomy )
        return false;

    $permalinks = get_tax_permalinks( $taxonomy );

    echo '<h2>'.$taxonomy.'</h2>';
    display_code_begin();
    if ( !empty( $permalinks ) ):
        foreach ( $permalinks as $permalink )
            echo '<a href="'.$permalink.'" target="_blank">'.$permalink.'</a><br>';
    else:
        echo 'No terms.';
    endif;
    display_code_end();
}

function display_all_post_types()
{
    $post_types = get_post_types( [ 'public' => true ] );

    echo '<h1>Post Types</h1>';
    foreach ( $post_types as $post_type )
        display_all_posts( $post_type );
}

function display_all_taxonomies()
{
    $taxonomies = get_taxonomies( [ 'public' => true ] );

    echo '<h1>Taxonomies</h1>';
    foreach ( $taxonomies as $taxonomy )
        display_all_terms( $taxonomy );
}

/**
 * Styles.
 */

?>
<style>
body {
    background-color: #161616;
    padding: 100px;
}
h1, h2 {
    padding-left: 15px;
    font-family: Helvetica, Arial, sans-serif;
}
h1 {
    text-transform: uppercase;
    letter-spacing: 3px;
    font-size: 40px;
    color: #f09;
}
h2 {
    font-size: 25px;
    color: #09f;
}
a {
    color: #fff;
}
</style>
<?php

/**
 * Runtime.
 */

display_all_post_types();

display_all_taxonomies();


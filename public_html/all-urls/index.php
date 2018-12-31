<?php

// connect to WordPress
define( 'WP_USE_THEMES', false );
require( '../wp-blog-header.php' );

/**
 * Functions.
 */

function display_code_begin()
{
    echo "<pre style=\"max-height:300px;z-index:9999;position:relative;overflow-y:scroll;white-space:pre-wrap;word-wrap:break-word;padding:10px 15px;border:1px solid #fff;background-color:#161616;text-align:left;line-height:1.5;font-family:Courier;font-size:16px;color:#fff;\">";
}

function display_code_end()
{
    echo "</pre>";
}

function get_post_type_permalinks( $post_type )
{
    global $languages_other;

    $permalinks = [];

    $ids = get_posts([
        'post_type'        => $post_type,
        'post_status'      => 'publish',
        'posts_per_page'   => -1,
        'fields'           => 'ids',
        'suppress_filters' => false,
    ]);

    if ( !empty( $ids ) )
    {
        foreach ( $ids as $id )
        {
            $permalink_default_lang = get_permalink( $id );
            $permalinks[] = $permalink_default_lang;

            if ( !empty( $languages_other ) )
            {
                foreach ( $languages_other as $lang )
                {
                    $translated_id = apply_filters( 'wpml_object_id', $id, 'page', false, $lang );
                    if ( $translated_id && get_post_status( $translated_id ) == 'publish' )
                    {
                        $permalinks[] = apply_filters( 'wpml_permalink', $permalink_default_lang, $lang, true );
                    }
                }
            }
        }
    }

    return $permalinks;
}

function get_tax_permalinks( $taxonomy )
{
    global $languages_other;

    $permalinks = [];

    $ids = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'fields'     => 'ids',
    ]);

    if ( !empty( $ids ) )
    {
        foreach ( $ids as $id )
        {
            $permalink_default_lang = get_term_link( $id, $taxonomy );
            $permalinks[] = $permalink_default_lang;

            if ( !empty( $languages_other ) )
            {
                foreach ( $languages_other as $lang )
                {
                    $translated_id = apply_filters( 'wpml_object_id', $id, $taxonomy, false, $lang );
                    if ( $translated_id && $translated_id != $id )
                    {
                        $permalinks[] = apply_filters( 'wpml_permalink', $permalink_default_lang, $lang, true );
                    }
                }
            }
        }
    }

    return $permalinks;
}

function display_all_posts( $post_type = false )
{
    if ( !$post_type )
        return false;

    $permalinks = get_post_type_permalinks( $post_type );

    echo '<h2>'.$post_type.'</h2>';
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
 * Runtime.
 */

if ( defined( 'ICL_LANGUAGE_CODE' ) )
{
    global $sitepress;
    $language_default = $sitepress->get_default_language();
    $languages        = array_column( icl_get_languages(), 'code' );
    $languages_other  = array_diff( $languages, [ $language_default ] );
}

display_all_post_types();

display_all_taxonomies();

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


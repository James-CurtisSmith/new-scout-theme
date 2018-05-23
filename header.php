<?php
/**
 * The head section for the theme.
 *
 * This is the template that displays all of the <head> section
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Group
 */
  $GLOBALS['theme']->output( 'header-block', array(
    'title'      => the_title_attribute('echo=0'),
    'name'       => get_bloginfo( 'name' ),
    'intro'      => strip_tags( get_field( 'intro' ) ),
    'url'        => get_permalink(),
    'hero'       => get_field( 'hero' ),
    'path'       => get_template_directory_uri(),
    'stylesheet' => get_bloginfo( 'stylesheet_url' )
  ));

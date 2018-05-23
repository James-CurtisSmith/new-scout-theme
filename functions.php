<?php

// Define class properties....
const MY_DEFN = [
  'DEFAULT_TYPE' => 'page',
  'PARAMETERS' => [
    'email_domain' => [
      'type'        => 'text',
      'section'     => 'title_tagline',
      'default'     => 'mydomainname.org.uk',
      'description' => 'Specify the domain for email addresses.'
    ],
    'hash_tags' => [
      'type'        => 'text',
      'section'     => 'title_tagline',
      'default'     => 'scouts',
      'description' => 'Specify the hash tags for tweeting.'
    ],
	  'facebook_page' => [
	    'type'        => 'text',
	    'section'     => 'title_tagline',
	    'default'     => 'scouts',
	    'description' => 'Name of facebook page'
	  ]
  ]
];

const MY_FIELDS = [
  'id' => 'acf_page', 'title' => 'Page',
  'fields' => [
    [ 'key' => 'field_hero', 'label' => 'Hero', 'name' => 'hero', 'required' => 1,
      'type' => 'image', 'save_format' => 'object', 'library' => 'all', 'preview_size' => 'large' ],
    [ 'key' => 'field_email', 'label' => 'Email address', 'name' => 'email_address',
      'type' => 'text', 'default_value' => '', 'placeholder' => '', 'prepend' => '',
      'append' => '', 'formatting' => 'html', 'maxlength' => '' ],
    [ 'key' => 'field_intro', 'label' => 'Intro', 'name' => 'intro', 'required' => 1,
      'type' => 'wysiwyg', 'default_value' => '', 'toolbar' => 'full', 'media_upload' => 'yes' ],
    [ 'key' => 'field_body', 'label' => 'Body', 'name' => 'body',
      'type' => 'wysiwyg', 'default_value' => '', 'toolbar' => 'full', 'media_upload' => 'yes' ]
  ],
  'location' => [ [
    [ 'param' => 'page_template', 'operator' => '==', 'value' => 'default', 'order_no' => 0, 'group_no' => 0, ]
  ] ],
  'options' => [ 'position' => 'normal', 'layout' => 'no_box', 'hide_on_screen' => [ 0 => 'the_content' ] ],
  'menu_order' => 0
];

include_once( dirname(__FILE__).'/support/base-theme-class.php' );

$theme = (new BaseThemeClass( MY_DEFN ))
       ->define_acf_elements( MY_FIELDS )
       ->hide_acf_admin()
       ->load_from_directory( '__templates' );
// As we are using a software defined field set we will remove the ACF edit functionality...!


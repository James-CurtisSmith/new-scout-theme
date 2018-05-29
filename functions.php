<?php

// Define class properties....
const MY_DEFN = [
  'DEFAULT_TYPE' => 'page',
  'PARAMETERS' => [
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

const PAGE_FIELDS = [
  'Hero'          => [ 'required' => 1, 'type' => 'image', 'save_format' => 'object', 'library' => 'all', 'preview_size' => 'large' ],
  'Email address' => [ 'type' => 'text' ],
  'Intro'         => [ 'type' => 'wysiwyg', 'required' => 1, 'toolbar' => 'full', 'media_upload' => 'yes' ],
  'Body'          => [ 'type' => 'wysiwyg', 'required' => 0, 'toolbar' => 'full', 'media_upload' => 'yes' ]
];

$theme = (new BaseThemeClass( MY_DEFN ))
       ->define_type( 'Page', PAGE_FIELDS )
//       ->hide_acf_admin()
       ->load_from_directory()
       ;
// As we are using a software defined field set we will remove the ACF edit functionality...!


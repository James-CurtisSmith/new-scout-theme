<?php

const DEFAULT_DEFN = [
  'PARAMETERS'   => [   // Associate array of definitions...
    // Key is variable "name"
    // type    - is type of input 
    // section - is which part of menu to add this to [may need to add "add-section" code
    // default - default value
    // description - help text appears under 
    'email_domain' => [
      'type'        => 'text',
      'section'     => 'title_tagline',
      'default'     => 'mydomainname.org.uk',
      'description' => 'Specify the domain for email addresses.'
    ]
  ],
  'DEFAULT_TYPE' => 'page',
  'TYPES'        => [],     // Associate array of types
     // Values are an array
     // [ code, dashicons code, name (singluar)?, name (plural)? ]
     // For dashicons code see: https://developer.wordpress.org/resource/dashicons/
     // name (singular/plural) if null/omitted are guessed from code
     //                             ucfirst / add "s"
  'STYLE'        => [],     // Associate array of CSS files (key/filename)
  'SCRIPTS'      => []      // Associate array of JS files  (key/filename)
];

class BaseThemeClass {
  protected $template_directory;
  protected $template_directory_uri;
  protected $defn;
  protected $templates;
  protected $preprocessors;
  protected $postprocessors;
  protected $debug;
  protected $array_methods;
  protected $scalar_methods;
  
  public function __construct( $defn ) {
    $this->defn = $defn;
    $this->initialize_templates()
         ->initialize_templates_directory()
         ->add_my_scripts_and_stylesheets()
         ->set_up_theme()
         ->clean_up_the_rubbish_wordpress_adds()
         ->stop_wordpress_screwing_up_image_widths_with_captions()
         ->set_up_custom_types()
         ->set_up_custom_parameters()
         ->remove_comments_admin()
         ->set_up_email_short_code();
  }
  function initialize_templates_directory() {
    $this->template_directory     = get_template_directory();
    $this->template_directory_uri = get_template_directory_uri();
    return $this;
  }

  function add_my_scripts_and_stylesheets() {
    add_action( 'wp_enqueue_scripts',         array( $this, 'enqueue_scripts'  ) );
    return $this;
  }

  public function enqueue_scripts() {
    //wp_scripts()->add_data( 'jquery', 'group', 1 );
    if( isset( $this->defn[ 'STYLES' ] ) ) {
      foreach( $this->defn[ 'STYLES' ] as $key => $name ) {
        wp_enqueue_script( $key, $this->template_directory_uri.$name,array(),null,false);
      }
    }
    if( isset( $this->defn[ 'SCRIPTS' ] ) ) {
      foreach( $this->defn[ 'SCRIPTS' ] as $key => $name ) {
        wp_enqueue_script( $key, $this->template_directory_uri.$name,array(),null,true);
      }
    }
  }

//----------------------------------------------------------------------
// Just minor theme support functionality
//----------------------------------------------------------------------
 
  function set_up_theme() {
    add_action( 'after_setup_theme',          array( $this, 'theme_setup'      ) );
    return $this;
  }

  public function theme_setup() {
    add_theme_support( 'html5' );
    add_theme_support( 'title-tag' );
  }

//----------------------------------------------------------------------
// Functions to clean up stuff which wordpress adds that we don't want
// or was just a really bad decisions...!
//----------------------------------------------------------------------

  public function clean_up_the_rubbish_wordpress_adds() {
    remove_filter( 'oembed_dataparse',       'wp_filter_oembed_result', 10 );
    remove_action( 'wp_head',                'wlwmanifest_link');
    remove_action( 'wp_head',                'rsd_link');
    remove_action( 'wp_head',                'rest_output_link_wp_head' );
    remove_action( 'wp_head',                'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head',                'wp_oembed_add_host_js' );
    remove_action( 'wp_head',                'print_emoji_detection_script', 7);
    remove_action( 'wp_print_styles',        'print_emoji_styles');
    remove_action( 'admin_print_scripts',    'print_emoji_detection_script' );
    remove_action( 'admin_print_styles',     'print_emoji_styles' );
    remove_action( 'rest_api_init',          'wp_oembed_register_route' );
    add_filter(    'emoji_svg_url',          '__return_false' );
    add_filter(    'embed_oembed_discover',  '__return_false' );
    remove_action( 'wp_head',                'wp_shortlink_wp_head', 10);
    remove_action( 'template_redirect',      'wp_shortlink_header', 11);
    remove_action( 'wp_head', 'feed_links', 2 );
    remove_action( 'wp_head', 'feed_links_extra', 3 );
    return $this;
  }

  public function stop_wordpress_screwing_up_image_widths_with_captions() {
    add_filter( 'post_thumbnail_html',  'remove_width_attribute', PHP_INT_MAX );
    add_filter( 'image_send_to_editor', 'remove_width_attribute', PHP_INT_MAX );
    add_filter( 'get_image_tag',        'remove_width_attribute', PHP_INT_MAX );

    add_filter(    'image_widget_image_attributes',  array( $this, 'responsive_image_widget' ) );

    add_filter(    'wp_calculate_image_sizes',      '__return_empty_array',  PHP_INT_MAX );
    add_filter(    'wp_calculate_image_srcset',     '__return_empty_array', PHP_INT_MAX );
    add_filter(    'img_caption_shortcode_width',   '__return_false', PHP_INT_MAX );
    remove_filter( 'the_content',                   'wp_make_content_images_responsive' );
    add_filter(    'wp_get_attachment_image_attributes',
      array( $this,  'remove_image_image_attributes_related_to_size' ),
      PHP_INT_MAX );
    return $this;
  }

  function remove_image_image_attributes_related_to_size( $attr )  {
    foreach( array('sizes','srcset','width','height') as $key) {
      if( isset( $attr[$key] ) ) {
        unset( $attr[$key] );
      }
    }
    return $attr; 
  }
  function remove_width_attribute( $html ) {
    $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
    return $html;
  }
  function responsive_image_widget($html) {
    return preg_replace('/(width|height)=["\']\d*["\']\s?/', "", $html);
  }

//----------------------------------------------------------------------
// Set up custom types from configuration hash {TYPES}
//----------------------------------------------------------------------
 
  function set_up_custom_types() {
    add_action( 'init',                       array( $this, 'create_custom_types'        ) );
    return $this;
  }

  function create_custom_types() {
    if( isset( $this->defn[ 'TYPES' ] ) ) {
      foreach( $this->defn[ 'TYPES' ] as $def ) {
        $name   = isset( $def[2] ) ? $def[2] : ucfirst( $def[0] );
        $lc     = strtolower($name);
        $plural = isset( $def[3] ) ? $def[3] : $name.'s';
        $new_item  = __("New $lc");
        $edit_item = __("Edit $lc");
        $view_item = __("View $lc");
        $view_items = __('View '.strtolower($plural) );
        $all_items  = __('All '.strtolower($plural) );
        register_post_type( $def[0], [ 'public' => true, 'has_archive' => true, 'menu_icon' => 'dashicons-'.$def[1],
          'labels' => [
            'add_new'          => $new_item,
            'add_new_singular' => $new_item,
            'new_item'         => $new_item,
            'edit_item'        => $edit_item,
            'view_item'        => $view_item,
            'view_items'       => $view_items,
            'all_items'        => $all_items,
            'singular_name' => __($name), 'name' => __($plural) ] ] );
      }
    }
  }

//----------------------------------------------------------------------
// Set up custom paraemters (in customizer) from config hash (PARAMETERS)
//----------------------------------------------------------------------
 
  function set_up_custom_parameters() {
    add_action( 'customize_register',         array( $this, 'create_custom_theme_params' ) );
    return $this;
  }

  function create_custom_theme_params( $wp_customize ) {
    if( isset( $this->defn[ 'PARAMETERS' ] ) ) {
      foreach( $this->defn[ 'PARAMETERS' ] as $par => $def ) {
        $name = isset( $def['name'] ) ? $def['name'] : $this->hr( $par );
        $type = isset( $def['type'] ) ? $def['type'] : 'text';
        $sanitize = 'sanitize_text_field';
        $wp_customize->add_setting( $par, array(
          'default'           => isset( $def['default'] ) ? $def['default'] : '',
          'sanitize_callback' => $sanitize
        ) );
        $wp_customize->add_control( $par, array(
          'type'        => $type,
          'section'     => $def['section'],
          'label'       => __( $name ),
          'description' => __( isset( $def['description'] ) ? $def['description'] : '' )
        ));
      }
    }
  }


//----------------------------------------------------------------------
// As we are removing "blog" functionality we don't need posts and
// comments fields... this requires remove a number of different bits
// of code hooked in a number of different places...
// 1) We need to modify the "new" link in the admin bar so it defaults
//    to a type other than post (default this to page - but possibly
//    could recall if you want it to default to something else!!)
// 2) Remove the new post and comments link from this menu bar!
//----------------------------------------------------------------------

  function remove_comments_admin() {
    add_action( 'admin_bar_menu',             array( $this, 'change_default_new_link' ), PHP_INT_MAX-1 );
    add_action( 'admin_menu',                 array( $this, 'remove_posts_sidebar') );
    add_filter( 'manage_edit-post_columns',   array( $this, 'remove_post_columns') ,10,1);
    add_filter( 'manage_edit-page_columns',   array( $this, 'remove_page_columns') ,10,1);
    return $this;
  }

  // Remove the comments and new post menu entries
  //   and change the default "New" link to "page"...
  function change_default_new_link( $wp_admin_bar, $type = '', $title = '' ) {
    if( $type === '' ) {
      $type = array_key_exists( 'DEFAULT_TYPE', $this->defn ) 
            ? $this->defn[  'DEFAULT_TYPE' ]
            : DEFAULT_DEFN[ 'DEFAULT_TYPE' ]
            ;
    }
    if( $title === '' ) {
      $title = ucfirst( $type );
    }
    // We can't have the node directly (shame) so we have to copy the node...
    $new_content_node = $wp_admin_bar->get_node('new-content');
    // Change the link... and set the 
    $new_content_node->href .= '?post_type='.$type;
    // Change the title (to include default add action!)
    $new_content_node->title = preg_replace(
       '/(label">).*?</', '$1'.__('New').' ('.__($title).')<', $new_content_node->title );
    $wp_admin_bar->remove_node('new-content');
    $wp_admin_bar->add_node( $new_content_node);
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_node('new-post');
    $wp_admin_bar->remove_menu('wp-logo');   // Not to do with psts 
  }

  function remove_posts_sidebar() {
    global $menu;
    $remove_menu_items = [ 'edit-comments.php', 'edit.php' ];
    end($menu);
    while (prev($menu)){
      if( in_array( $menu[key($menu)][2], $remove_menu_items ) ) {
        unset($menu[key($menu)]);
      }
    }
  }
  function remove_post_columns($columns) {
    unset($columns['comments']);
    return $columns;
  }

  function remove_page_columns($columns) {
    unset($columns['comments']);
    return $columns;
  }

//----------------------------------------------------------------------
// Add email link short code functionality to obfuscate emails...
//----------------------------------------------------------------------

  public function set_up_email_short_code() {
    add_shortcode( 'email_link', array( $this, 'email_link' ) );
    return $this;
  }

  // Short code: [email_link {email} {link text}?]
  //
  // Render an (source code) obfuscated email (mailto:) link 
  //
  //  * If email does not contain "@" then we add email_domain from customizer...
  //  * If link text isn't defined it defaults to email address
  //
 
  function email_link( $atts, $content = null ) {
    $email = array_shift( $atts );
    $email = strpos( $email, '@' ) !== false
           ? $email
           : $email.'@'.get_theme_mod('email_domain')
           ;

    $name  = implode( $atts, ' ' );
    if( $name === '' ) {
      $name = $email;
    }
    return sprintf( '<a href="mailto:%s">%s</a>',
      $this->random_url_encode( $email ),
      $this->random_html_entities( $name )
    );
  } 
  
//----------------------------------------------------------------------
// Some additional functions!
//----------------------------------------------------------------------
  
  public function theme_version() {
    return wp_get_theme()->get( 'Version' );
  }

//----------------------------------------------------------------------
// Template funcations....
//----------------------------------------------------------------------

  function initialize_templates() {
    $this->templates      = [];
    $this->preprocessors  = [];
    $this->postprocessors = [];
    $this->debug          = false;
    $this->array_methods = [
      'templates' => function( $t_data, $extra ) {
        if( is_array( $t_data ) ) {
          return implode( '', array_map(function($row) use ($extra) {
            return $this->expand_template( $this->template_name( $extra, $row ), $row );
          } ));
        }
        return '';
      },
      'template'  => function( $t_data, $extra ) {
        return $this->expand_template( $this->template_name( $extra, $t_data ), $t_data );
      }
    ];
    $this->scalar_methods = [
      'raw'       => function( $s ) { return $s; },
      'date'      => function( $s ) { return date_format( date_create( $s ), 'F jS Y' ); },
      'enc'       => 'rawurlencode',
      'rand_enc'  => function( $s ) { return $this->random_url_encode( $s ); },
      'integer'   => 'intval',
      'boolean'   => function( $s ) { return $s ? 'true' : 'false'; },
      'shortcode' => 'do_shortcode',
      'strip'     => function( $s ) { return preg_replace( '/\s*\b(height|width)=["\']\d+["\']/', '', do_shortcode( $s ) ); },
      'rand_html' => function( $s ) { return $this->random_html_entities( $s ); },
      'html'      => 'HTMLentities'
    ];

    return $this;
  }
  
  function add_array_method( $key, $fn ) {
    $this->array_methods[  $key ] = $fn;
    return $this;
  }
  function add_scalar_method( $key, $fn ) {
    $this->scalar_methods[ $key ] = $fn;
    return $this;
  }
  function add_template($key, $template) {
    if( ! array_key_exists( $key, $this->templates ) ) {
      $this->templates[$key] = array();
    }
    if( is_array( $template ) ) {
      if( array_key_exists( 'template', $template ) ){
        if( array_key_exists( 'pre', $template ) ) {
          $this->add_preprocessor( $key, $template['pre'] );
        }
        $this->add_template( $key, $template['template'] );
      #  $this->templates[$key][] = $template['template'];
        if( array_key_exists( 'post', $template ) ) {
          $this->add_postprocessor( $key, $template['post'] );
        } 
        return $this;
      }
      foreach( $template as $t ) {
        $this->templates[$key][] = $t;
      }
      return $this;
    }
    $this->templates[$key][] = $template;
    return $this;
  }

  public function load_from_file( $filename ) {
    $full_path = $this->template_directory.'/'.$filename;
    if( file_exists( $full_path ) ) {
      $templates = include $full_path;
      foreach( $templates as $key => $template ) {
        $this->add_template( $key, $template );
      }
    }
    return $this;
  }
  
  public function load_from_directory( $dirname ) {
    $full_path = $this->template_directory.'/'.$dirname;
    if( file_exists( $full_path ) ) {
      if( is_dir( $full_path ) ) {
        if( $dh = opendir($full_path) ) {
          while( ($file = readdir($dh)) !== false ) {
            if( '.' !== substr($file,0,1) ) {
              $this->load_from_directory( $dirname.'/'.$file );
            }
          }
          closedir($dh);
        }
      } else {
        $templates = include $full_path;
        foreach( $templates as $key => $template ) {
          $this->add_template( $key, $template );
        }
      }
    }
    return $this;
  }
  
  public function dump_templates( ) {
    print '<pre style="height:800px;overflow:scrollbar">';
    print '<h4>Templates</h4>';
    print_r( $this->templates );
    print '<h4>Pre-processors</h4>';
    print_r( $this->preprocessors );
    print '<h4>Post-processors</h4>';
    print_r( $this->postprocessors );
    print '</pre>';
    return $this;
  }

// Pre-processor code...

  public function add_preprocessor( $key, $function ) {
    $this->preprocessors[ $key ] = $function;
    return $this;
  }

  public function add_postprocessor( $key, $function ) {
    $this->postprocessors[ $key ] = $function;
    return $this;
  }

// Debug and error code

  public function debug_on() {
    $this->debug = true;
    return $this;
  }

  public function debug_off() {
    $this->debug = false;
    return $this;
  }

  protected function show_error( $message ) {
    if( $this->debug ) {
      return '<div class="error">'.HTMLentities( $message ).'</div>';
    }
    error_log( $message );
    return '';
  }

  private function expand_template( $template_code, $data) {
    if( ! array_key_exists( $template_code, $this->templates ) ) {
      return $this->show_error( "Template '$template_code' is missing" );
    }
    // Apply any pre-processors to data - thie munges/amends the data-structure
    // being passed...
    if( array_key_exists( $template_code, $this->preprocessors ) ) {
      $function = $this->preprocessors[$template_code];
      $data = $function( $data, $this );
    }
    $regexp = sprintf( '/\[\[(?:(%s|%s):)?([-~.\w+]+)(?::([^\]]+))?\]\]/', 
       implode('|',array_keys( $this->array_methods )),
       implode('|',array_keys( $this->scalar_methods )) );
    $out = implode( '', array_map(
      function( $t ) use ( $data, $template_code, $regexp ) {
        return is_object($t) && ($t instanceof Closure)
      ? $t( $data, $template_code ) // If the template being parsed is a closure then we call the function
      : preg_replace_callback(      // It's a string so parse it - regexps are wonderful things!!!
          $regexp,
          function($match) use ($data, $template_code) {
            // For each substitute - get the parsed values....

            list( $render_type, $variable, $extra ) = [ $match[1], $match[2], array_key_exists( 3, $match ) ? $match[3] : '' ];

            $t_data = $this->parse_variable( $variable, $extra, $data );
            if( array_key_exists( $render_type, $this->array_methods ) ) {
              return $this->array_methods[ $render_type ]( $t_data, $extra ); }
            if( is_array( $t_data ) ) { $this->show_error(
              "Rendering array as '$variable' in '$template_code' ($render_type)<pre>".print_r($t_data,1).'</pre>' ); }
            if( array_key_exists( $render_type, $this->scalar_methods ) ) {
              return $this->scalar_methods[ $render_type ]( $t_data ); }
            return HTMLentities( $t_data );
          },
          $t
        );
      },
      $this->templates[$template_code]
    ));
    // Apply any post processors to the markup - this can clean up the HTML afterwards...
    if( array_key_exists( $template_code, $this->postprocessors ) ) {
      $function = $this->postprocessors[$template_code];
      $out = $function( $out, $data, $this );
    }
    return $out;
  }

  function parse_variable( $variable, $extra, $data ) {
    //
    // First switch - parse the variable name and get the data from the object
    //
    // special variable names:
    //     "-" - data is just the raw value of extra
    //     "~" - data is the theme parameter {from customizer} give by extra
    //     "." - data is just the data for the current template
    // otherwise 
    //   split the variable on "." and use these as keys for the elements of data to
    //   sub-value or sub-tree of data...
    //
    switch( $variable ) {
      case '-':  // raw string
        return $extra;
      case '~': // customizer parameter
        return get_theme_mod( $extra );
      case '.'; // just pass data through!
        return $data;
      default:  // navigate down data tree...
        $t_data = $data;
        foreach( explode( '.', $variable ) as $key ) {
          // Missing data
          if( !is_array( $t_data ) ) {
            return ''; // No value in tree with that key!
          }
          // key doesn't exist in data structure or has null value...
          if( !array_key_exists( $key, $t_data ) || 
            !isset(            $t_data[$key] ) ||
            is_null(           $t_data[$key] ) ) {
            return '';
          }
          $t_data = $t_data[$key];
        }
        return $t_data;
    }
  }
    
  function render_scalar( $scalar, $style = 'default' ) {
  }

  function template_name( $str, $data ) {
    return preg_replace_callback( '/[*](\w+)/', function ($match) use ($data) {
      return array_key_exists( $match[1], $data ) ? $data[$match[1]] : '';
    },$str);
  }

  function render( $template_code, $data = []) {
    return $this->collapse_empty(
      preg_replace('/<a\s[^>]*href=""[^>]*>.*?<\/a>/','',       // Empty links
      preg_replace('/<iframe\s[^>]*src=""[^>]*><\/iframe>/','', // Empty iframes
      preg_replace('/<img\s[^>]*src=""[^>]*>/','',              // Empty images
        $this->expand_template( $template_code, $data ) ) ) ) );
  }
  
  function output( $template_code, $data = [] ) {
    print $this->render( $template_code, $data );
    return $this;
  }

  function output_page( $page_type ) {
    get_header();
    $this->output( $page_type,
      array_merge(get_fields(),['url'=>get_permalink(),'title'=>the_title('','',false)])
    );
    get_footer();
  }

//----------------------------------------------------------------------
// Support functions used by other methods!
//----------------------------------------------------------------------

  function hr( $s ) {
    return ucfirst( str_replace( '_', ' ', $s ) );
  }

  function random_html_entities( $string ) {
    $alwaysEncode = array('.', ':', '@');
    $res='';
    for($i=0;$i<strlen($string);$i++) {
      $x = htmlentities( $string[$i] );
      if( $x === $string[$i] && ( in_array( $x, $alwaysEncode ) || !mt_rand(0,3) ) ) {
        $x = mt_rand(0,1) ? '&#'.ord($x).';' : '&#x'.dechex(ord($x)).';';
      }
      $res.=$x;
    }
    return $res;
  }
  function random_url_encode( $string ) {
    $alwaysEncode = array('.', ':', '@');
    $res='';
    for($i=0;$i<strlen($string);$i++){
      $x = urlencode( $string[$i] );
      if( $x === $string[$i] && ( in_array( $x, $alwaysEncode ) || !mt_rand(0,3) ) ) {
        $x = sprintf( mt_rand(0,1) ? '%%%02X' : '%%%02x', ord($x) );
      }
      $res.=$x;
    }
    return $res;
  }

  function collapse_empty( $html_str ) {
    $munged = '';
    while( $munged !== $html_str ) {
      // Trim empty tags -- a, span, p, div, h[1-6], ...
      list($munged,$html_str) = array(
        $html_str,
        preg_replace('/<(a|span|p|div|h\d)[^>]*>\s*<\/\1>/','',$html_str)
      );
    }
    return preg_replace( '/\s*[\r\n]+\s*[\r\n]/',"\n", $html_str ); // Remove blank lines
  }

  function define_acf_elements( $defn ) {
    if(! function_exists("register_field_group") ) {
      return  $this->show_error( 'ACF plugin not installed!' );
    }
    register_field_group( $defn );
    return $this;
  }

  function hide_acf_admin() {
    define( 'ACF_LITE', true );
    return $this;
  }
}


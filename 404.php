<?php
  // Load the 404 page into the post object...
  $post = get_page_by_path( 'not-found', OBJECT );
  $theme->output_page( 'page' );

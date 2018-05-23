<?php
//  tmpl()->dump_templates();
  return [
// Top level page templates....
    'page' => ['
[[template:.:nav-block]]
      <section>
        <div class="hero" style="background-image:url(\'[[hero.url]]\')">
          <h2>[[title]]</h2>
        </div>
      </section>
      <section>
	    <p class="email">',
    function( $d ) {
      if( ! $d['email_address'] ) {
        return '';
      }
      $email = strpos( $d['email_address'], '@' ) !== false ? $d['email_address']
             : $d['email_address'].'@'.get_theme_mod('email_domain');
      return sprintf( '<a href="mailto:%s">%s</a>',
        $this->random_url_encode( $email ) , $this->random_html_entities( $email ) );
    },'
          <span class="social"><a id="twitter-share" target="_blank" href="https://twitter.com/share?text=[[enc:title]]&amp;url=[[url]]&amp;hashtags=[[html:~:hash_tags]]">Tweet this</a>
          <a id="facebook-share" target="_blank" href="https://www.facebook.com/sharer.php?u=[[url]]">Share this</a></span>
        </p>
		<div class="intro">
          [[strip:intro]]
        </div>
        [[strip:body]]
      </section>'
    ]
  ];

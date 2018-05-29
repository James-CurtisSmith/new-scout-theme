<?php

return [
  'nav-block' => [ '
<header>
  <h1><a href="/" aria-label="Return to home page" title="Home page">G</a></h1>
  <nav>
  <p>Giving&nbsp;our&nbsp;young&nbsp;people skills&nbsp;for&nbsp;life!</p>
  <input type="checkbox" id="ham" />
  <label for="ham"><span id="overlay">Toggle for responsive menu - click to show/hide menu</span></label>
  <ul>
    <li id="nb"><a href="/beavers/"   title="Beaver Scouts"              aria-label="Discover more about the Beaver Scout Colony" >B</a></li>
    <li id="nc"><a href="/cubs/"      title="Cub Scouts"                 aria-label="Discover more about the Cub Scout Pack"      >C</a></li>
    <li id="ns"><a href="/scouts/"    title="Scouts"                     aria-label="Discover more about the Scout Troop page"    >S</a></li>
    <li id="ne"><a href="/explorers/" title="Explorer Scouts and beyond" aria-label="Discover more about Explorer Scouts"         >E</a></li>
    <li><a         href="/join/"                                         aria-label="Discover how your children can join in"      >How to join</a></li>
    <li><a         href="/parents/"                                      aria-label="More information for parents of young people">For parents</a></li>
    <li><a         href="/our-team/"                                     aria-label="Discover more about helping the Group"       >Can I help?</a></li>
    <li><a         href="/links/"                                        aria-label="Links to useful online resources"            >Resources</a></li>
  </ul>
  </nav>
</header>'
  ],

  'header-block' => '<!DOCTYPE html>
<html lang="en-gb" xml:lang="en-gb" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <link rel="stylesheet" type="text/css"            href="[[stylesheet]]" />
    <link rel="manifest"                              href="[[path]]/manifest.json">
    <meta charset="utf-8">
    <meta property="og:title"                         content="[[title]] - [[name]]"  />
    <meta property="og:image"                         content="[[hero.url]]" />
    <meta property="og:type"                          content="website" />
    <meta property="og:url"                           content="[[url]]" />
    <meta name="viewport"                             content="width=device-width, initial-scale=1.0, user-scalable=yes, minimum-scale=1.0, maximum-scale=5.0" />
    <meta name="description"                          content="[[intro]]">
    <meta name="theme-color"                          content="#7413dc">
    <meta name="msapplication-TileColor"              content="#7413dc">
    <meta name="msapplication-TileImage"              content="[[path]]/gfx/favicon-144x144.png">
    <link rel="shortcut icon"                         href="[[path]]/gfx/favicon-16x16.png"        >
    <link rel="apple-touch-icon"      sizes="57x57"   href="[[path]]/gfx/favicon-57x57.png"     >
    <link rel="apple-touch-icon"      sizes="60x60"   href="[[path]]/gfx/favicon-60x60.png"     >
    <link rel="apple-touch-icon"      sizes="72x72"   href="[[path]]/gfx/favicon-72x72.png"     >
    <link rel="apple-touch-icon"      sizes="76x76"   href="[[path]]/gfx/favicon-76x76.png"     >
    <link rel="apple-touch-icon"      sizes="114x114" href="[[path]]/gfx/favicon-114x114.png"   >
    <link rel="apple-touch-icon"      sizes="120x120" href="[[path]]/gfx/favicon-120x120.png"   >
    <link rel="apple-touch-icon"      sizes="144x144" href="[[path]]/gfx/favicon-144x144.png"   >
    <link rel="apple-touch-icon"      sizes="152x152" href="[[path]]/gfx/favicon-152x152.png"   >
    <link rel="apple-touch-icon"      sizes="180x180" href="[[path]]/gfx/favicon-180x180.png"   >
    <link rel="icon" type="image/png" sizes="192x192" href="[[path]]/gfx/favicon-192x192.png" >
    <link rel="icon" type="image/png" sizes="32x32"   href="[[path]]/gfx/favicon-32x32.png"        >
    <link rel="icon" type="image/png" sizes="96x96"   href="[[path]]/gfx/favicon-96x96.png"        >
    <link rel="icon" type="image/png" sizes="16x16"   href="[[path]]/gfx/favicon-16x16.png"        >
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,700,900" />
    [[wp:-:head]]
  </head>
  <body class="[[wp:-:body_class]]">',

  'footer-block' => '
<footer>
  <ul>
    <li><a href="/privacy/">Privacy policy</a></li>
	<li>
	  <a id="facebook-follow" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/[[html:~:facebook_page]]/">Follow us on Facebook</a>
    </li>
  </ul>
  <ul>
    <li><a href="/contact-us/">Contact us</a>: <a href="mailto:[[rand_enc:-:contact@]][[rand_enc:~:email_domain]]">[[rand_html:-:contact@]][[rand_html:~:email_domain]]</a></li>
    <li>Copyight &copy; Shelfords and Stapleford Scout Group 2018; <a target="_blank" rel="noopener noreferrer" href="http://beta.charitycommission.gov.uk/charity-details/?regid=301093&amp;subid=0">Charity number: 301093</a></li>
  </ul>
</footer>
  [[wp:-:foot]]
  </body>
</html>'
];


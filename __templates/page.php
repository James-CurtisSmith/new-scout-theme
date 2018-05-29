<?php
//  tmpl()->dump_templates();
  return [
// Top level page templates....
    'page' => '
[[template:.:nav-block]]
      <main role="main">
      <section>
        <div class="hero">
          <img src="[[hero.url]]" alt="**Hero image" />
          <h2>[[title]]</h2>
        </div>
      </section>
      <section>
        <p class="email">
          [[email:email_address]]
          <span class="social"><a id="twitter-share" target="_blank" rel="noopener noreferrer" href="https://twitter.com/share?text=[[enc:title]]&amp;url=[[url]]&amp;hashtags=[[html:~:hash_tags]]">Tweet this</a>
          <a id="facebook-share" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/sharer.php?u=[[url]]">Share this</a></span>
        </p>
        <div class="intro">
          [[strip:intro]]
        </div>
        [[strip:body]]
      </section>
      </main>'
  ];

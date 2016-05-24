<?php use Roots\Sage\Utils; ?>

<nav
  id="navbar"
  class="navbar <?php Utils\sage_header_navbar_class(); ?>"
  role="navigation">

  <div
    id="navbar-tabs"
    class="navbar-tabs"
    role="tablist"
    aria-multiselectable="true">

    <?php Utils\sage_header_logo(); ?>

    <div class="panel-heading" role="tab" id="heading-menu">
        <button
          class="navbar-toggler collapsed"
          type="button"
          data-toggle="collapse"
          data-target="#nav-menu"
          data-parent="#navbar-tabs"
          aria-expanded="false"
          aria-controls="nav-menu">

          <span class="icon icon-menu">&#9776;</span>
          <span class="icon icon-close">&#10006;</span>
        </button>
    </div>

    <div class="panel-heading" role="tab" id="heading-search">
        <button
          class="navbar-toggler collapsed"
          type="button"
          data-toggle="collapse"
          data-target="#search-form"
          data-parent="#navbar-tabs"
          aria-expanded="false"
          aria-controls="search-form">

          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-search">
            <use xlink:href="#magnifier"></use>
          </svg>
          <span class="icon icon-close">&#10006;</span>
        </button>
    </div>

    <div class="navbar-search-wrapper panel">
      <div
        id="search-form"
        class="panel-collapse collapse"
        role="tabpanel"
        aria-labelledby="nav-menu">

        <?php get_search_form(); ?>
      </div>
    </div>

    <div class="navbar-nav-wrapper panel">
      <div
        id="nav-menu"
        class="panel-collapse collapse"
        role="tabpanel"
        aria-labelledby="nav-menu">

        <?php if (has_nav_menu( 'primary_navigation')) wp_nav_menu(array(
          'theme_location'=>'primary_navigation',
          'menu_class' => 'nav navbar-nav'
        )); ?>
      </div>
    </div>

  </div>

</nav>

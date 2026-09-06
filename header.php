<?php
/**
 * Header — 公共头部
 * 包含 <html>…</head>…<body>…</header> 全部内容（按笔记约定）
 * 钩子：language_attributes / bloginfo(charset) / wp_head / wp_body_open / body_class
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body id="root" <?php body_class('no-response previewHead'); ?>>
<div id="document">
<?php wp_body_open(); ?>

  <!-- ===================== HEADER ===================== -->
  <header id="SITE_HEADER" class="no-response previewHead">
    <div class="container header-bar">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" title="HOME">
        <?php if (function_exists('the_custom_logo') && has_custom_logo()) {
            the_custom_logo();
        } else { ?>
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="<?php bloginfo('name'); ?>" width="362" height="407">
        <?php } ?>
      </a>

      <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="mainNav">
        <span></span><span></span><span></span>
      </button>

      <nav class="nav" aria-label="Main navigation">
        <button class="nav-close" id="navClose" aria-label="Close navigation"><span></span><span></span></button>
        <?php
        if (has_nav_menu('primary_menu')) {
            wp_nav_menu(array(
                'theme_location' => 'primary_menu',
                'container'      => false,
                'menu_class'     => 'mainNav',
                'menu_id'        => 'mainNav',
                'walker'         => new Jc_Nav_Walker(),
                'fallback_cb'    => 'jc_default_menu',
            ));
        } else {
            jc_default_menu();
        }
        ?>
      </nav>

      <div class="header-actions">
        <button class="search-btn" id="searchOpen" aria-label="Search"><?php echo jc_icon('search'); ?></button>
        <a href="<?php echo esc_url(jc_wa_url()); ?>" class="header-wa" title="WhatsApp" aria-label="WhatsApp"><?php echo jc_icon('whatsapp'); ?></a>
        <a href="#" class="btn-contact" data-lightbox="contactModal" title="Contact Us">Contact Us</a>
        <div class="lang-switch">
          <button class="lang-btn" aria-label="Language"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/flags/en.png'); ?>" alt="English"><?php echo jc_icon('chevron-down'); ?></button>
          <ul class="lang-dropdown">
            <li><a href="#" class="selected"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/flags/en.png'); ?>" alt=""> English</a></li>
            <li><a href="#"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/flags/ar.png'); ?>" alt=""> العربية</a></li>
            <li><a href="#"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/flags/es.png'); ?>" alt=""> Español</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <?php
  // 全站搜索弹窗：get_search_form() 优先加载主题里的 searchform.php（笔记约定）
  get_search_form();
  ?>

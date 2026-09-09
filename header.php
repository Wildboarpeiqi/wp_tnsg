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
      <a href="<?php echo esc_url(jc_home_url()); ?>" class="logo" title="<?php echo esc_attr(jc_t('HOME')); ?>">
        <?php if (function_exists('the_custom_logo') && has_custom_logo()) {
            the_custom_logo();
        } else { ?>
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="<?php bloginfo('name'); ?>" width="362" height="407">
        <?php } ?>
      </a>

      <button class="nav-toggle" id="navToggle" aria-label="<?php echo esc_attr(jc_t('Toggle navigation')); ?>" aria-expanded="false" aria-controls="mainNav">
        <span></span><span></span><span></span>
      </button>

      <nav class="nav" aria-label="<?php echo esc_attr(jc_t('Main navigation')); ?>">
        <button class="nav-close" id="navClose" aria-label="<?php echo esc_attr(jc_t('Close navigation')); ?>"><span></span><span></span></button>
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
        <button class="search-btn" id="searchOpen" aria-label="<?php echo esc_attr(jc_t('Search')); ?>"><?php echo jc_icon('search'); ?></button>
        <a href="<?php echo esc_url(jc_wa_url()); ?>" class="header-wa" title="WhatsApp" aria-label="WhatsApp"><?php echo jc_icon('whatsapp'); ?></a>
        <a href="#" class="btn-contact" data-lightbox="contactModal" title="<?php echo esc_attr(jc_t('Contact Us')); ?>"><?php echo esc_html(jc_t('Contact Us')); ?></a>
<?php
$jc_languages = array();

if (function_exists('pll_the_languages')) {
    $jc_languages = pll_the_languages(array(
        'raw'                    => 1,
        'hide_if_empty'          => 0,
        'hide_if_no_translation' => 0,
    ));
}

if (!is_array($jc_languages)) {
    $jc_languages = array();
}

/* 找当前语言 */
$jc_current_language = null;

foreach ($jc_languages as $jc_lang) {
    if (!empty($jc_lang['current_lang'])) {
        $jc_current_language = $jc_lang;
        break;
    }
}

/* 主题自带旗帜 */
$jc_flag_map = array(
    'en' => 'en.png',
    'es' => 'es.png',
    'ar' => 'ar.png',
);

/* 当前语言旗帜 */
$jc_current_flag = '';

if ($jc_current_language) {

    $jc_current_slug = $jc_current_language['slug'];

    if (isset($jc_flag_map[$jc_current_slug])) {
        $jc_current_flag =
            get_template_directory_uri()
            . '/assets/images/flags/'
            . $jc_flag_map[$jc_current_slug];
    } elseif (!empty($jc_current_language['flag'])) {
        $jc_current_flag = $jc_current_language['flag'];
    }
}
?>

<div class="lang-switch">

  <button
    class="lang-btn"
    aria-label="<?php echo esc_attr(jc_t('Language')); ?>"
    aria-haspopup="true"
  >
    <?php if ($jc_current_language && $jc_current_flag) : ?>

      <img
        src="<?php echo esc_url($jc_current_flag); ?>"
        alt="<?php echo esc_attr($jc_current_language['name']); ?>"
      >

    <?php endif; ?>

    <?php echo jc_icon('chevron-down'); ?>
  </button>

  <ul class="lang-dropdown">

    <?php if (!empty($jc_languages)) : ?>

      <?php foreach ($jc_languages as $jc_lang) : ?>

        <?php
        $jc_slug = $jc_lang['slug'];

        if (isset($jc_flag_map[$jc_slug])) {
            $jc_flag =
                get_template_directory_uri()
                . '/assets/images/flags/'
                . $jc_flag_map[$jc_slug];
        } else {
            $jc_flag = !empty($jc_lang['flag'])
                ? $jc_lang['flag']
                : '';
        }

        $jc_url = !empty($jc_lang['url'])
            ? $jc_lang['url']
            : (
                function_exists('pll_home_url')
                    ? pll_home_url($jc_slug)
                    : '#'
            );

        $jc_selected = !empty($jc_lang['current_lang'])
            ? ' class="selected"'
            : '';
        ?>

        <li>
          <a
            href="<?php echo esc_url($jc_url); ?>"
            hreflang="<?php echo esc_attr($jc_slug); ?>"
            lang="<?php echo esc_attr($jc_slug); ?>"
            <?php echo $jc_selected; ?>
          >

            <?php if ($jc_flag) : ?>
              <img
                src="<?php echo esc_url($jc_flag); ?>"
                alt=""
              >
            <?php endif; ?>

            <?php echo esc_html($jc_lang['name']); ?>

          </a>
        </li>

      <?php endforeach; ?>

    <?php else : ?>

      <li>
        <a href="<?php echo esc_url(jc_home_url()); ?>" class="selected">
          English
        </a>
      </li>

    <?php endif; ?>

  </ul>
</div>
      </div>
    </div>
  </header>

  <?php
  // 全站搜索弹窗：get_search_form() 优先加载主题里的 searchform.php（笔记约定）
  get_search_form();
  ?>

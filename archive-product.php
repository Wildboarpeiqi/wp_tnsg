<?php
/**
 * Archive — Product 产品列表页（/products/，产品总列表）
 * CPT: product 归档模板；product_category 单分类列表走 taxonomy-product_category.php
 *
 * 字段来源约定（与 single-product.php 一致）：
 *   - banner（page_banner_*）、侧栏（side_*）、reasons（reason_title / reason_group）公共字段页 62 → jc_g62()
 *   - 分类列表：all product_category 按 sort_order 升序 → get_terms / get_field
 *   - 产品数据：WP_Query(product) 主循环，卡片复用 template-parts/product-card.php
 *   - 分页：每页 9 条（PC 3×3；多页时出分页）
 * 侧栏 + 移动端分类导航直接写在此文件（与 single-product.php 风格一致，不用模板部件）
 * 表单：[fluentform id="3"]（侧栏）硬编码 + shortcode_exists() 判断
 *
 * CSS/JS：assets/css/product-list.css + assets/js/product-list.js
 *   （functions.php 里 jc_assets() 通过 is_post_type_archive('product')||is_tax() 加载）
 */

get_header();

$theme_uri = get_template_directory_uri();

// ---- 左栏 use：所有 product_category，按 sort_order 排序（当前页无选中）----
$all_cats = get_terms(array('taxonomy' => 'product_category', 'hide_empty' => false));
if (!is_wp_error($all_cats)) {
    usort($all_cats, function ($a, $b) {
        $sa = (int) get_field('sort_order', 'product_category_' . $a->term_id);
        $sb = (int) get_field('sort_order', 'product_category_' . $b->term_id);
        if ($sa === $sb) {
            return ($a->name <=> $b->name);
        }
        return $sa <=> $sb;
    });
} else {
    $all_cats = array();
}

// ---- 联系方式（公共字段 company_whatsapp / company_email）----
$email        = jc_get('company_email', '');

// ---- banner（62 页 page_banner_img / page_banner_title）----
$banner_img   = jc_g62('page_banner_img', $theme_uri . '/assets/images/banner-products.png');
$banner_title = jc_g62('page_banner_title', jc_t('PRODUCTS'));
?>

<!-- ===================== PAGE BANNER（内容页公共横幅） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner_img); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html($banner_title); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(jc_home_url()); ?>"><?php echo esc_html(jc_t('HOME')); ?></a> &gt;
      <span><?php echo esc_html($banner_title); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：左侧快捷导航 + 右侧产品网格 ===================== -->
<div class="container page-wrap">

  <!-- ============ 左侧快捷导航 ============ -->
  <aside class="page-sidebar">
    <div class="side-box">
      <h3 class="side-title"><?php echo esc_html(jc_g62('side_nav_title', jc_t('Navigation'))); ?></h3>

      <!-- 搜索栏 -->
      <form class="side-search" action="<?php echo esc_url(jc_home_url()); ?>" method="get" role="search">
        <input type="text" name="s" placeholder="<?php echo esc_attr(jc_t('Search starts here')); ?>"autocomplete="off">
        <button type="submit" aria-label="<?php echo esc_attr(jc_t('Search')); ?>"><?php echo jc_icon('search'); ?></button>
      </form>

      <!-- product_category 分类列表（sort_order 升序） -->
      <ul class="side-cats">
        <?php foreach ($all_cats as $cat) : ?>
          <li><a href="<?php echo esc_url(get_term_link($cat)); ?>" class="side-cat-link"><p><?php echo esc_html($cat->name); ?></p></a></li>
        <?php endforeach; ?>
      </ul>

      <!-- reasons 区块（空标题则整块隐藏；共 3 组，icon/title/desc 三项全空则跳过该组） -->
      <?php
      $reason_title = jc_g62('reason_title', '');
      $rg = function_exists('get_field') ? get_field('reason_group', jc_global_field_id()) : null;
      $rg = is_array($rg) ? $rg : array();
      if ($reason_title !== '') : ?>
        <div class="side-reasons">
          <h4 class="side-reasons-title"><?php echo esc_html($reason_title); ?></h4>
          <?php for ($i = 1; $i <= 3; $i++) :
              $icon = isset($rg['reason_' . $i . '_icon']) ? $rg['reason_' . $i . '_icon'] : '';
              $ttl  = isset($rg['reason_' . $i . '_title']) ? $rg['reason_' . $i . '_title'] : '';
              $desc = isset($rg['reason_' . $i . '_desc']) ? $rg['reason_' . $i . '_desc'] : '';
              if (empty($icon) && empty($ttl) && empty($desc)) { continue; }
          ?>
            <div class="reason-item">
              <div class="reason-icon">
                <?php if (!empty($icon)) : ?>
                  <img src="<?php echo esc_url($icon); ?>" alt="" class="reason-icon-img">
                <?php else : ?>
                  <i class="yiyingbaoicon">&#xe6a9;</i>
                <?php endif; ?>
              </div>
              <div class="reason-txt">
                <div class="reason-title"><?php echo esc_html($ttl); ?></div>
                <div class="reason-desc"><?php echo esc_html($desc); ?></div>
              </div>
            </div>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

      <!-- 小标题 / 自由段（可空隐藏） -->
      <?php $side_sub = jc_g62('side_sub_text', ''); if ($side_sub !== '') : ?>
        <h4 class="side-sub"><?php echo esc_html($side_sub); ?></h4>
      <?php endif; ?>

      <!-- 蓝底邮箱：优先 side_email_text，空则用 company_email -->
      <?php
      $side_email = jc_g62('side_email_text', '');
      if ($side_email === '') { $side_email = $email; }
      if ($side_email !== '') :
      ?>
        <a href="mailto:<?php echo esc_attr($side_email); ?>" class="side-email"><?php echo esc_html($side_email); ?></a>
      <?php endif; ?>

      <!-- 左栏询盘表单：[fluentform id="3"] -->
      <div class="side-form-box">
        <h4 class="side-form-title"><?php echo esc_html(jc_t('Send Us A Message')); ?></h4>
        <?php if (shortcode_exists('fluentform')) {
            echo do_shortcode('[fluentform id="3"]');
        } else { ?>
          <form class="side-form" action="#" method="post">
            <input type="text" name="name" placeholder="<?php echo esc_attr(jc_t('Name')); ?>" required>
            <input type="tel" name="whatsapp" placeholder="<?php echo esc_attr(jc_t('WhatsApp')); ?>">
            <input type="text" name="company" placeholder="<?php echo esc_attr(jc_t('Company')); ?>">
            <input type="email" name="email" placeholder="<?php echo esc_attr(jc_t('Email')); ?>" required>
            <textarea name="message" rows="4" placeholder="<?php echo esc_attr(jc_t('Message')); ?>" required></textarea>
            <button type="submit" class="btn-submit"><?php echo esc_html(jc_t('Submit')); ?></button>
          </form>
        <?php } ?>
      </div>
    </div>
  </aside>

  <!-- ============ 右侧产品列表 ============ -->
  <main class="page-main">

    <!-- 移动端分类导航条（仅移动端显示）：总列表 = "All Categories"，汉堡展开分类 -->
    <div class="pl-mnav" id="plMnav">
      <button type="button" class="pl-mnav-btn" id="plMnavBtn" aria-expanded="false" aria-controls="plMnavList">
        <span class="pl-mnav-label"><?php echo esc_html(jc_t('All Categories')); ?></span>
        <span class="pl-mnav-hamburger" aria-hidden="true"><span></span><span></span><span></span></span>
      </button>
      <ul class="pl-mnav-list" id="plMnavList" hidden>
        <?php foreach ($all_cats as $cat) : ?>
          <li><a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <?php
    // ---- 产品排序：分类 sort_order 升序 → 分类内 product_sort 升序（无排序按时间在后）----
    $jc_all_ids  = jc_get_sorted_product_ids();
    $jc_per_page = 9;
    $jc_total    = count($jc_all_ids);
    $jc_pages    = max(1, (int) ceil($jc_total / $jc_per_page));
    $jc_paged    = max(1, (int) get_query_var('paged'));
    if ($jc_paged > $jc_pages) { $jc_paged = $jc_pages; }
    $jc_page_ids = array_slice($jc_all_ids, ($jc_paged - 1) * $jc_per_page, $jc_per_page);
    $jc_query    = new WP_Query(array(
        'post_type'      => 'product',
        'post__in'       => $jc_page_ids ? $jc_page_ids : array(0),
        'orderby'        => 'post__in',
        'posts_per_page' => $jc_per_page,
        'no_found_rows'  => true,
    ));
    ?>
    <ul class="proList pl-grid">
      <?php if ($jc_query->have_posts()) : while ($jc_query->have_posts()) : $jc_query->the_post(); ?>
        <?php get_template_part('template-parts/product-card'); ?>
      <?php endwhile; wp_reset_postdata(); endif; ?>
    </ul>

    <!-- 分页（每页 9 条，多页时显示） -->
    <?php jc_product_pagination($jc_pages, $jc_paged); ?>

  </main>
</div>

<?php get_footer(); ?>
<?php
/**
 * Taxonomy — product_category（单分类产品列表）
 * 如 /product_category/forged-gear-rings/，展示该分类下产品
 *
 * 与 archive-product.php 共用结构，差异：
 *   banner 标题=分类名、面包屑含分类、侧栏高亮当前分类、移动导航显示当前分类名。
 *   侧栏 / reasons / 移动导航按 single-product.php 风格直接内联（无模板部件）。
 *
 * 字段：分类的 sort_order（get_field('sort_order','product_category_'.term_id)），
 *   其余公共字段（page_banner_* / side_* / reason_*）读 62 页 jc_g62()。
 *
 * CSS/JS：product-list.css / product-list.js（functions.php 按 is_tax('product_category') 加载）
 */

get_header();

$theme_uri = get_template_directory_uri();
$cur_term  = get_queried_object(); // product_category term

// ---- 左栏 use：所有 product_category，按 sort_order 排序 ----
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

$email = jc_get('company_email', '');

// ---- banner（62 页 page_banner_img；标题=分类名）----
$banner_img   = jc_g62('page_banner_img', $theme_uri . '/assets/images/banner-products.png');
$banner_title = ($cur_term && !is_wp_error($cur_term) && !empty($cur_term->name)) ? $cur_term->name : 'PRODUCTS';

// ---- reasons 字段（62 页 reason_title / reason_group）----
$reason_title = jc_g62('reason_title', '');
$reason_group = function_exists('get_field') ? get_field('reason_group', JC_GLOBAL_FIELD_ID) : null;
?>

<!-- ===================== PAGE BANNER ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner_img); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html($banner_title); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a> &gt;
      <a href="<?php echo esc_url(jc_products_url()); ?>">PRODUCTS</a> &gt;
      <span><?php echo esc_html($banner_title); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：左侧快捷导航 + 右侧产品网格 ===================== -->
<div class="container page-wrap">

  <!-- ============ 左侧快捷导航 ============ -->
  <aside class="page-sidebar">
    <div class="side-box">
      <h3 class="side-title"><?php echo esc_html(jc_g62('side_nav_title', 'Navigation')); ?></h3>

      <!-- 搜索栏 -->
      <form class="side-search" action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search">
        <input type="text" name="s" placeholder="Search starts here" autocomplete="off">
        <button type="submit" aria-label="Search"><?php echo jc_icon('search'); ?></button>
      </form>

      <!-- product_category 分类列表（sort_order 升序；当前分类高亮） -->
      <ul class="side-cats">
        <?php foreach ($all_cats as $cat) : ?>
          <li>
            <?php if ($cur_term && $cat->term_id === $cur_term->term_id) : ?>
              <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="side-cat-link selected"><p><?php echo esc_html($cat->name); ?></p></a>
            <?php else : ?>
              <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="side-cat-link"><p><?php echo esc_html($cat->name); ?></p></a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- reasons 区块（空标题则整块隐藏；共 3 组，icon/title/desc 三项全空则跳过该组） -->
      <?php
      $reason_group = is_array($reason_group) ? $reason_group : array();
      if ($reason_title !== '') : ?>
        <div class="side-reasons">
          <h4 class="side-reasons-title"><?php echo esc_html($reason_title); ?></h4>
          <?php for ($i = 1; $i <= 3; $i++) :
              $icon = isset($reason_group['reason_' . $i . '_icon']) ? $reason_group['reason_' . $i . '_icon'] : '';
              $ttl  = isset($reason_group['reason_' . $i . '_title']) ? $reason_group['reason_' . $i . '_title'] : '';
              $desc = isset($reason_group['reason_' . $i . '_desc']) ? $reason_group['reason_' . $i . '_desc'] : '';
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
        <h4 class="side-form-title">Send Us A Message</h4>
        <?php if (shortcode_exists('fluentform')) : ?>
          <?php echo do_shortcode('[fluentform id="3"]'); ?>
        <?php endif; ?>
      </div>
    </div>
  </aside>

  <!-- ============ 右侧产品列表 ============ -->
  <main class="page-main">

    <!-- 移动端分类导航条（仅移动端显示） -->
    <div class="pl-mnav" id="plMnav">
      <button type="button" class="pl-mnav-btn" id="plMnavBtn" aria-expanded="false" aria-controls="plMnavList">
        <span class="pl-mnav-label"><?php echo esc_html($banner_title); ?></span>
        <span class="pl-mnav-hamburger" aria-hidden="true"><span></span><span></span><span></span></span>
      </button>
      <ul class="pl-mnav-list" id="plMnavList" hidden>
        <?php foreach ($all_cats as $cat) : ?>
          <li><a href="<?php echo esc_url(get_term_link($cat)); ?>"<?php if ($cur_term && $cat->term_id === $cur_term->term_id) : ?> class="selected"<?php endif; ?>><?php echo esc_html($cat->name); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <?php
    // ---- 产品排序：当前分类内 product_sort 升序（无排序按时间在后）----
    $jc_term_id  = ($cur_term && !is_wp_error($cur_term)) ? (int) $cur_term->term_id : 0;
    $jc_all_ids  = jc_get_sorted_product_ids($jc_term_id);
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

    <!-- 分页 -->
    <?php jc_product_pagination($jc_pages, $jc_paged); ?>

  </main>
</div>

<?php get_footer(); ?>
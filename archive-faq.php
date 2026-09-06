<?php
/**
 * Archive — FAQ 列表页（/faq/）
 * 模板命名：archive-faq.php（WP 官方：CPT faq 的归档模板）
 * 说明：
 *   - 本模板 = 静态版 FAQ.html 的 WP 落地
 *   - Banner 背景图：公共字段页 62 的 faq_banner_img（CPT 归档页没有页面可挂 site_banner，
 *     与产品列表页用 page_banner_img 同模式），空则兜底主题图 banner-news.jpg
 *   - Banner 标题：faq_banner_title（默认 FAQ）；面包屑 HOME > FAQ
 *   - 卡片：无图（Q 角标 + 问题标题 + 摘要 + Read More），标题 1 行截断 / 摘要 2 行截断（faq-list.css）
 *   - 每页条数：8（pre_get_posts 钩子 jc_faq_archive_per_page，2 列 × 4 行）
 *   - 排序：发布时间倒序（WP 默认主循环，无需额外处理）
 *   - 分页：the_posts_pagination()
 * CSS/JS：assets/css/faq-list.css 在 functions.php jc_assets() 条件加载（仅样式，无专属 JS）
 */

get_header();

$theme_uri = get_template_directory_uri();

// ---- Banner（62 页 faq_banner_img / faq_banner_title，与产品列表页 page_banner_* 同模式）----
$banner_img   = jc_g62('faq_banner_img', $theme_uri . '/assets/images/banner-news.jpg');
$banner_title = jc_g62('faq_banner_title', 'FAQ');
?>

<!-- ===================== PAGE BANNER（栏目页 banner：图 + 标题 + 面包屑） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner_img); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html($banner_title); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a> &gt;
      <span><?php echo esc_html($banner_title); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：FAQ 问题网格 + 分页（全宽无侧栏） ===================== -->
<div class="container faq-page">

  <?php if (have_posts()) : ?>
    <!-- FAQ 网格（主循环：按发布时间倒序；卡片 = Q 角标 + 标题 + 摘要 + Read More） -->
    <ul class="faq-grid">
      <?php while (have_posts()) : the_post(); ?>
        <li class="faq-card">
          <a href="<?php the_permalink(); ?>" class="faq-card-link" title="<?php echo esc_attr(get_the_title()); ?>">
            <span class="faq-q">Q</span>
            <div class="faq-card-body">
              <h3 class="faq-card-title"><?php the_title(); ?></h3>
              <p class="faq-card-desc"><?php echo esc_html(get_the_excerpt()); ?></p>
              <span class="faq-card-more">Read More</span>
            </div>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>

    <!-- 分页：the_posts_pagination()（单页时不输出；样式在 faq-list.css） -->
    <?php
    the_posts_pagination(array(
        'mid_size'  => 2,
        'end_size'  => 1,
        'prev_text' => '<span class="arrow">&lt;</span>Previous',
        'next_text' => 'Next<span class="arrow">&gt;</span>',
        'screen_reader_text' => 'FAQ pagination',
    ));
    ?>
  <?php else : ?>
    <!-- 暂无内容（兜底提示，不发 404） -->
    <ul class="faq-grid">
      <li class="faq-card" style="grid-column:1/-1;text-align:center;padding:4rem 2rem;">
        <p style="font-size:1.6rem;color:#666;">No questions published yet. Please check back later.</p>
      </li>
    </ul>
  <?php endif; ?>

</div>

<?php get_footer(); ?>

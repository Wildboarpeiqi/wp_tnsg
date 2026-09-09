<?php
/**
 * Category — 分类归档页（news 的三个分类共用）
 * 模板命名：category.php（WP 官方：分类归档模板）
 * 说明：
 *   - 本模板 = 静态版 news/index.html 分类过滤的 WP 落地（每个分类独立 URL，SEO 独立落地页）
 *   - Banner 背景图：当前分类 term 的 site_banner 字段（分类法=全部 的字段规则），空则兜底主题图
 *   - Banner 标题：当前分类名 single_cat_title()；面包屑 HOME > NEWS > 分类名
 *   - 分类 tab：All → 文章页 /news/，其余分类 → 各自归档；当前分类高亮
 *   - 每页条数：后台「设置→阅读→博客页面至多显示」（主循环，不写死）
 *   - 分页：the_posts_pagination()
 * CSS/JS：与 home.php 共用 assets/css/news-list.css（functions.php 条件加载）
 */

get_header();

$theme_uri = get_template_directory_uri();
$term      = get_queried_object(); // 当前分类 term

// ---- Banner 背景图：当前分类的 site_banner（ACF 分类法字段，挂在 category 上），空则兜底主题图 ----
$banner = '';
if (function_exists('get_field') && $term && !is_wp_error($term)) {
    $banner = get_field('site_banner', 'category_' . $term->term_id);
}
// 兼容 ACF 三种返回格式：图片 ID / 数组 / URL 字符串（2026-09-06：返回 ID 时前端曾不显示）
if (is_numeric($banner)) {
    $banner = function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url((int) $banner, 'full') : '';
} elseif (is_array($banner) && !empty($banner['url'])) {
    $banner = $banner['url'];
}
if (!$banner) { $banner = $theme_uri . '/assets/images/banner-news.jpg'; }

// ---- 新闻分类（tab 用）：不卡 slug，列出所有分类（排除 WP 默认 Uncategorized），
// 建了就能显示；顺序优先 product-knowledge / buying-guide / industry-news（静态版顺序），
// 其余分类按名称排后面。 ----
$news_cats = get_terms(array(
    'taxonomy'   => 'category',
    'hide_empty' => false,
    'exclude'    => array((int) get_option('default_category')), // 排除默认 Uncategorized
    'orderby'    => 'name',
));
if (is_wp_error($news_cats)) { $news_cats = array(); }

$preferred = array('product-knowledge' => 1, 'buying-guide' => 2, 'industry-news' => 3);
usort($news_cats, function ($a, $b) use ($preferred) {
    $pa = isset($preferred[$a->slug]) ? $preferred[$a->slug] : 9;
    $pb = isset($preferred[$b->slug]) ? $preferred[$b->slug] : 9;
    if ($pa !== $pb) { return $pa - $pb; }
    return strcmp($a->name, $b->name);
});

// 面包屑里 NEWS 链接：文章页（posts page），没有则回退首页
$posts_page_id = jc_posts_page_id();
$news_url      = $posts_page_id ? get_permalink($posts_page_id) : jc_home_url();
$news_label    = $posts_page_id ? get_the_title($posts_page_id) : 'NEWS';
?>

<!-- ===================== PAGE BANNER（栏目页 banner：图 + 标题 + 面包屑） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php single_cat_title(); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(jc_home_url()); ?>">HOME</a> &gt;
      <a href="<?php echo esc_url($news_url); ?>"><?php echo esc_html($news_label); ?></a> &gt;
      <span><?php single_cat_title(); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：分类 tab + 文章网格 + 分页（全宽无侧栏） ===================== -->
<div class="container news-page">

  <!-- 分类 Tab（当前分类高亮） -->
  <div class="news-tabs" role="tablist" aria-label="News categories">
    <a class="news-tab" href="<?php echo esc_url($news_url); ?>">All</a>
    <?php foreach ($news_cats as $cat) : ?>
      <a class="news-tab<?php echo ($term && !is_wp_error($term) && $cat->term_id === $term->term_id) ? ' active' : ''; ?>" href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (have_posts()) : ?>
    <!-- 文章网格（主循环：当前分类下的文章，按发布时间倒序） -->
    <ul class="news-grid" id="newsGrid">
      <?php while (have_posts()) : the_post();
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'jc-card');
        if (!$thumb) {
            $thumb = $theme_uri . '/assets/images/news/news-' . (($wp_query->current_post % 3) + 1) . '.jpg';
        }
        $cats = get_the_category();
        $first_cat = !empty($cats) ? $cats[0] : null;
      ?>
        <li class="news-card">
          <a href="<?php the_permalink(); ?>" class="news-card-link">
            <div class="news-card-thumb"><img loading="lazy" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"></div>
            <div class="news-card-body">
              <div class="news-card-meta">
                <time datetime="<?php echo esc_attr(get_the_time('Y-m-d')); ?>"><?php echo esc_html(date('M j, Y', get_the_time('U'))); ?></time>
                <?php if ($first_cat) : ?>
                  <span class="news-card-cat"><?php echo esc_html($first_cat->name); ?></span>
                <?php endif; ?>
              </div>
              <h3 class="news-card-title"><?php the_title(); ?></h3>
              <p class="news-card-desc"><?php echo esc_html(get_the_excerpt()); ?></p>
              <span class="news-card-more">Read More &rarr;</span>
            </div>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>

    <!-- 分页 -->
    <?php
    the_posts_pagination(array(
        'mid_size'  => 2,
        'end_size'  => 1,
        'prev_text' => jc_icon('arrow-left'),
        'next_text' => jc_icon('arrow-right'),
        'screen_reader_text' => 'News pagination',
    ));
    ?>
  <?php else : ?>
    <ul class="news-grid" id="newsGrid">
      <li class="news-card" style="grid-column:1/-1;text-align:center;padding:4rem 2rem;">
        <p style="font-size:1.6rem;color:#666;">No articles in this category yet. Please check back later.</p>
      </li>
    </ul>
  <?php endif; ?>

</div>

<?php get_footer(); ?>

<?php
/**
 * Home — 文章列表页（Posts Index）
 * 模板命名：home.php（WP 官方：博客文章索引页 = 后台「设置 → 阅读 → 文章页」指定的页面）
 * 说明：
 *   - 本模板 = 静态版 news/index.html 的 WP 落地
 *   - Banner 背景图：当前页面（News 页）的 site_banner 字段（栏目页专属，不在公共 62 页），
 *     规则：文章类型=页面 且 ≠首页 且 ≠公共参数页 OR 分类法=全部 → 显示 banner
 *   - Banner 标题：页面标题 the_title()（News 页标题）；面包屑 HOME > NEWS
 *   - 分类 tab：All → 本页 /news/，其余 → category-{slug} 归档页（真实链接，不再 JS 过滤）
 *   - 每页条数：后台「设置→阅读→博客页面至多显示」（主循环，不写死）
 *   - 分页：the_posts_pagination()
 * CSS/JS：assets/css/news-list.css 在 functions.php jc_assets() 条件加载
 *   （静态版 news-list.js 的前端过滤/分页在 WP 端由分类归档 URL + 服务器渲染承担，不再加载）
 */

get_header();

$theme_uri = get_template_directory_uri();

// ---- 文章页（posts page）ID：后台「设置→阅读→文章页」指定；未设置时标题/链接兜底 ----
$posts_page_id = jc_posts_page_id();
$news_title    = $posts_page_id ? get_the_title($posts_page_id) : 'NEWS';
$news_url      = $posts_page_id ? get_permalink($posts_page_id) : jc_home_url();

// ---- Banner 背景图：News 页（posts page）的 site_banner 字段，空则兜底主题图 ----
$banner = '';
if (function_exists('get_field') && $posts_page_id) {
    $banner = get_field('site_banner', $posts_page_id);
}
if (is_array($banner) && !empty($banner['url'])) { $banner = $banner['url']; }
if (!$banner) { $banner = $theme_uri . '/assets/images/banner-news.jpg'; }

// ---- 新闻分类（tab 用）：不卡 slug，列出所有分类（排除 WP 默认 Uncategorized），
// 建了就能显示；顺序优先 product-knowledge / buying-guide / industry-news（静态版顺序），
// 其余分类按名称排后面。 ----后面改了，代码都删了，前面说的没用了。
$news_cats = jc_get_sorted_news_categories();

<!-- ===================== PAGE BANNER（栏目页 banner：图 + 标题 + 面包屑） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html($news_title); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(jc_home_url()); ?>">HOME</a> &gt;
      <span><?php echo esc_html($news_title); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：分类 tab + 文章网格 + 分页（全宽无侧栏） ===================== -->
<div class="container news-page">

  <!-- 分类 Tab（WP：All → 本页 /news/，其余 → category-{slug} 归档；当前页高亮） -->
  <div class="news-tabs" role="tablist" aria-label="News categories">
    <a class="news-tab active" href="<?php echo esc_url($news_url); ?>">All</a>
    <?php foreach ($news_cats as $cat) : ?>
      <a class="news-tab" href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (have_posts()) : ?>
    <!-- 文章网格（主循环：按发布时间倒序；卡片字段：特色图/日期/分类徽章/标题/摘要/Read More） -->
    <ul class="news-grid" id="newsGrid">
      <?php while (have_posts()) : the_post();
        // 特色图：优先特色图（jc-card 尺寸），没有则兜底 news-1/2/3.jpg 轮换
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'jc-card');
        if (!$thumb) {
            $thumb = $theme_uri . '/assets/images/news/news-' . (($wp_query->current_post % 3) + 1) . '.jpg';
        }
        // 分类徽章：取第一个分类名（没分类则不显示徽章）
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

    <!-- 分页：the_posts_pagination()（单页时不输出；样式在 news-list.css） -->
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
    <!-- 暂无文章（兜底提示，不发 404） -->
    <ul class="news-grid" id="newsGrid">
      <li class="news-card" style="grid-column:1/-1;text-align:center;padding:4rem 2rem;">
        <p style="font-size:1.6rem;color:#666;">No news has been published yet. Please check back later.</p>
      </li>
    </ul>
  <?php endif; ?>

</div>

<?php get_footer(); ?>

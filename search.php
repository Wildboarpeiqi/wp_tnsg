<?php
/**
 * search.php — 搜索结果页（WP 标准模板：/?s=关键词 时自动调用）
 * 静态版对照：E:\jingxiang\site\search.html
 * 说明：
 *   - Banner：标题固定 "Search Results"，背景图主题图 banner-news.jpg（搜索页为系统页，无 ACF 字段）
 *   - 结果：WP 主循环自动搜全站（产品/新闻/FAQ/页面，CPT 在 ACF 后台注册，默认参与搜索）
 *   - 类型标签映射：product→Product / post→News / faq→FAQ / page→Page / 其他→首字母大写
 *   - 摘要：the_excerpt()，为空则不输出 snippet 行
 *   - 无结果：提示 + 内联搜索框（name="s" 是 WP 固定参数）+ Back to Home
 *   - 分页：the_posts_pagination()（?s=xxx&paged=2）
 *   - 搜索结果页自动 noindex（The SEO Framework 默认处理）
 * CSS：assets/css/search.css 在 functions.php jc_assets() 里 is_search() 条件加载
 */
get_header();

$theme_uri = get_template_directory_uri();
$found     = (int) $wp_query->found_posts;
$query_txt = get_search_query();
?>

<!-- ===================== PAGE BANNER（搜索页 banner：主题图 + 标题 + 面包屑） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($theme_uri . '/assets/images/banner-news.jpg'); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title">Search Results</div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a> &gt;
      <span>Search</span>
    </div>
  </div>
</section>

<!-- ===================== 主体：结果统计 + 列表 / 无结果 ===================== -->
<div class="container search-page">

  <?php if (have_posts()) : ?>

    <!-- 结果统计 -->
    <p class="search-count"><?php
      printf(
        _n('%s result found for &ldquo;%s&rdquo;', '%s results found for &ldquo;%s&rdquo;', $found, 'jc'),
        number_format_i18n($found),
        '<strong>' . esc_html($query_txt) . '</strong>'
      );
    ?></p>

    <!-- 结果列表 -->
    <ul class="search-list">
      <?php while (have_posts()) : the_post();
        $pt     = get_post_type();
        $labels = array('product' => 'Product', 'post' => 'News', 'faq' => 'FAQ', 'page' => 'Page');
        $tag    = isset($labels[$pt]) ? $labels[$pt] : ucfirst($pt);
        $ex     = get_the_excerpt();
      ?>
        <li class="search-item">
          <span class="search-tag"><?php echo esc_html($tag); ?></span>
          <a class="search-link" href="<?php the_permalink(); ?>">
            <h3 class="search-title"><?php the_title(); ?></h3>
          </a>
          <?php if ($ex !== '') : ?>
            <p class="search-snippet"><?php echo esc_html($ex); ?></p>
          <?php endif; ?>
        </li>
      <?php endwhile; ?>
    </ul>

    <!-- 分页：the_posts_pagination()（单页时不输出） -->
    <?php
    the_posts_pagination(array(
        'mid_size'           => 2,
        'end_size'           => 1,
        'prev_text'          => '<span class="arrow">&lt;</span>Previous',
        'next_text'          => 'Next<span class="arrow">&gt;</span>',
        'screen_reader_text' => 'Search pagination',
    ));
    ?>

  <?php else : ?>

    <!-- 无结果状态 -->
    <div class="search-empty">
      <h3 class="search-empty-title">No results found</h3>
      <p class="search-empty-desc">Sorry, nothing matched &ldquo;<?php echo esc_html($query_txt); ?>&rdquo;. Please try different keywords, or contact us directly for product information.</p>
      <form role="search" method="get" class="search-form search-empty-form" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="text" name="s" placeholder="Search starts here..." value="<?php echo esc_attr($query_txt); ?>">
        <button type="submit" class="btn-main">Search</button>
      </form>
      <p style="margin-top:2rem;"><a href="<?php echo esc_url(home_url('/')); ?>" class="btn-outline">Back to Home</a></p>
    </div>

  <?php endif; ?>

</div>

<?php get_footer(); ?>

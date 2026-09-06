<?php
/**
 * Single Post — 文章详情页（WordPress 官方 post 类型）
 * 模板命名：single-post.php（WP 官方 post 专属单篇模板）
 * 字段来源约定：
 *   - 公共字段（news_banner_img / news_banner_title / news_related_title）：公共字段页 62 → jc_g62()
 *   - 标题 / 日期 / 内容：WP 原生（the_title / get_the_date / the_content），非字段
 *   - 侧栏 Related Posts：WP_Query(post_type=post) 按发布日期倒序前 10 篇
 *   - 侧栏表单：[fluentform id="3"]（与产品详情页一致）
 * CSS/JS：专属 assets/css/news-single.css 在 functions.php jc_assets() 条件加载
 */

get_header();

$post_id    = get_the_ID();
$theme_uri  = get_template_directory_uri();

// ---- banner 图（62 页 news_banner_img，Image URL 格式）----
$news_banner = jc_g62('news_banner_img', $theme_uri . '/assets/images/banner-news.jpg');

// ---- 上一篇 / 下一篇（首尾判断：第一篇无 previous、最后一篇无 next）----
$prev_post = get_previous_post();
$next_post = get_next_post();

// ---- Related Posts：post 类型，按发布日期倒序前 10 篇（不足显示全部；含当前篇，当前篇高亮）----
$related_title = jc_g62('news_related_title', 'Related Posts');
$related_query = new WP_Query(array(
    'post_type'      => 'post',
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
));
?>

<!-- ===================== PAGE BANNER（内容页公共横幅） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($news_banner); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html(jc_g62('news_banner_title', 'NEWS')); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a> &gt;
      <a href="<?php echo esc_url(jc_page_url('news')); ?>">NEWS</a> &gt;
      <span><?php echo esc_html(get_the_title()); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：左侧 Related Posts + 右侧文章 ===================== -->
<div class="container page-wrap">

  <!-- ============ 左侧侧边栏 ============ -->
  <aside class="page-sidebar">
    <div class="side-box">

      <!-- Related Posts（按发布日期倒序前 10 篇，不足显示全部） -->
      <h3 class="side-title"><?php echo esc_html($related_title); ?></h3>
      <?php if ($related_query->have_posts()) : ?>
        <ul class="side-posts">
          <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
            <li class="<?php echo get_the_ID() === $post_id ? 'current' : ''; ?>">
              <a href="<?php the_permalink(); ?>" class="side-post-link"><p><?php the_title(); ?></p></a>
            </li>
          <?php endwhile; wp_reset_postdata(); ?>
        </ul>
      <?php endif; ?>

      <!-- 左栏询盘表单：[fluentform id="3"]，与产品详情页一致 -->
      <div class="side-form-box">
        <h4 class="side-form-title">Send Us A Message</h4>
        <?php if (shortcode_exists('fluentform')) {
            echo do_shortcode('[fluentform id="3"]');
        } else { ?>
          <form class="side-form" action="#" method="post">
            <input type="text" name="name" placeholder="Name" required>
            <input type="tel" name="whatsapp" placeholder="WhatsApp">
            <input type="text" name="company" placeholder="Company">
            <input type="email" name="email" placeholder="Email" required>
            <textarea name="message" rows="4" placeholder="Message" required></textarea>
            <button type="submit" class="btn-submit">Submit</button>
          </form>
        <?php } ?>
      </div>
    </div>
  </aside>

  <!-- ============ 右侧文章正文 ============ -->
  <main class="page-main">

    <article class="article" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

      <!-- 文章标题（WP 编辑器标题） -->
      <h1 class="article-title"><?php the_title(); ?></h1>

      <!-- 发布时间（强制英文格式：用 PHP 原生 date()，不经过 WP 语言机制，不受后台中文/多语言插件影响）
           与首页 front-page.php 的写法完全一致：date('M j, Y', get_the_time('U')) -->
      <div class="article-meta">
        <time datetime="<?php echo esc_attr(get_the_time('Y-m-d')); ?>">
          <span class="meta-label">Time:</span><?php echo esc_html(date('M j, Y', get_the_time('U'))); ?>
        </time>
      </div>

      <!-- 正文内容（WP 富文本） -->
      <div class="article-content">
        <?php the_content(); ?>
      </div>

      <!-- 上一篇 / 下一篇：上下两行带标题，首尾判断 -->
      <nav class="article-nav" aria-label="Post navigation">
        <?php if ($prev_post) : ?>
          <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="nav-item nav-prev">
            <span class="nav-label">Previous page</span>
            <span class="nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
          </a>
        <?php else : ?>
          <div class="nav-item nav-prev disabled">
            <span class="nav-label">Previous page</span>
            <span class="nav-title">Already the first</span>
          </div>
        <?php endif; ?>

        <?php if ($next_post) : ?>
          <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="nav-item nav-next">
            <span class="nav-label">Next page</span>
            <span class="nav-title"><?php echo esc_html($next_post->post_title); ?></span>
          </a>
        <?php else : ?>
          <div class="nav-item nav-next disabled">
            <span class="nav-label">Next page</span>
            <span class="nav-title">Already the last</span>
          </div>
        <?php endif; ?>
      </nav>

    </article>

  </main>
</div>

<?php get_footer(); ?>
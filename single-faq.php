<?php
/**
 * Single FAQ — FAQ 详情页（CPT faq 单篇）
 * 模板命名：single-faq.php（WP 官方：CPT faq 专属单篇模板）
 * 说明：版式与 single-post.php（文章详情页）完全一致，直接复用其结构：
 *   - Banner 背景图：公共字段页 62 的 faq_banner_img（与 FAQ 列表页共用栏目 banner）
 *   - Banner 标题：faq_banner_title；面包屑 HOME > FAQ > 问题标题（FAQ 链接指向归档页 /faq/）
 *   - 标题 / 正文：WP 原生（标题 = 问题，the_content = 答案）
 *   - 侧栏 Related Questions：WP_Query(post_type=faq) 按发布日期倒序前 10 篇
 *   - 侧栏表单：[fluentform id="3"]（与文章详情页一致）
 * CSS/JS：与文章详情页共用 assets/css/news-single.css（版式相同，不新建专属文件）
 */

get_header();

$post_id   = get_the_ID();
$theme_uri = get_template_directory_uri();

// ---- banner 图（62 页 faq_banner_img，与 FAQ 列表页共用栏目 banner）----
$faq_banner = jc_g62('faq_banner_img', $theme_uri . '/assets/images/banner-news.jpg');

// ---- 上一篇 / 下一篇（首尾判断：get_previous_post/get_next_post 自动按当前 CPT 类型查询）----
$prev_post = get_previous_post();
$next_post = get_next_post();

// ---- Related Questions：faq 类型，按发布日期倒序前 10 篇（含当前篇，当前篇高亮）----
$related_title = jc_g62('faq_related_title', 'Related Questions');
$related_query = new WP_Query(array(
    'post_type'      => 'faq',
    'posts_per_page' => 10,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
));
?>

<!-- ===================== PAGE BANNER（内容页公共横幅） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($faq_banner); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html(jc_g62('faq_banner_title', 'FAQ')); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">HOME</a> &gt;
      <a href="<?php echo esc_url(get_post_type_archive_link('faq')); ?>">FAQ</a> &gt;
      <span><?php echo esc_html(get_the_title()); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：左侧 Related Questions + 右侧文章 ===================== -->
<div class="container page-wrap">

  <!-- ============ 左侧侧边栏 ============ -->
  <aside class="page-sidebar">
    <div class="side-box">

      <!-- Related Questions（按发布日期倒序前 10 篇，不足显示全部） -->
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

      <!-- 左栏询盘表单：[fluentform id="3"]，与文章详情页一致 -->
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

  <!-- ============ 右侧问题正文 ============ -->
  <main class="page-main">

    <article class="article" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

      <!-- 问题标题（WP 编辑器标题） -->
      <h1 class="article-title"><?php the_title(); ?></h1>

      <!-- 发布时间（强制英文格式，与 single-post.php 一致） -->
      <div class="article-meta">
        <time datetime="<?php echo esc_attr(get_the_time('Y-m-d')); ?>">
          <span class="meta-label">Time:</span><?php echo esc_html(date('M j, Y', get_the_time('U'))); ?>
        </time>
      </div>

      <!-- 答案正文（WP 富文本） -->
      <div class="article-content">
        <?php the_content(); ?>
      </div>

      <!-- 上一篇 / 下一篇：上下两行带标题，首尾判断 -->
      <nav class="article-nav" aria-label="FAQ navigation">
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

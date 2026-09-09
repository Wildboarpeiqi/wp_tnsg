<?php
/**
 * Archive — Project 列表页（/project/）
 * 模板命名：archive-project.php（WP 官方：CPT project 的归档模板）
 * 说明：
 *   - 本模板 = 静态版 projects.html 的 WP 落地
 *   - Banner 背景图：公共字段页 62 的 project_banner_img（CPT 归档页没有页面可挂 site_banner，
 *     与产品/FAQ 列表页用 62 页公共字段同模式），空则兜底主题图 banner-news.jpg
 *   - Banner 标题：project_banner_title（默认 PROJECTS）；面包屑 HOME > PROJECTS
 *   - 卡片：特色图（jc-card 尺寸，无则轮换示例图）+ 标题（2 行截断）+ 摘要（3 行截断）+ Read More
 *   - 无分类 tab（用户确认：Project 列表页不需要分类 tab）
 *   - 每页条数：6（pre_get_posts 钩子 jc_project_archive_per_page，3 列 × 2 行）
 *   - 排序：发布时间倒序（WP 默认主循环）
 *   - 分页：the_posts_pagination()
 * CSS：assets/css/project-list.css 在 functions.php jc_assets() 条件加载（仅样式，无专属 JS）
 */

get_header();

$theme_uri = get_template_directory_uri();

// ---- Banner（62 页 project_banner_img / project_banner_title）----
$banner_img   = jc_g62('project_banner_img', $theme_uri . '/assets/images/banner-news.jpg');
$banner_title = jc_g62('project_banner_title',jc_t('PROJECTS'));
?>

<!-- ===================== PAGE BANNER（栏目页 banner：图 + 标题 + 面包屑） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner_img); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html($banner_title); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(jc_home_url()); ?>"><?php echo esc_html(jc_t('HOME')); ?></a> &gt;
      <span><?php echo esc_html($banner_title); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：Project 网格 + 分页（全宽无侧栏、无分类 tab） ===================== -->
<div class="container project-page">

  <?php if (have_posts()) : ?>
    <!-- Project 网格（主循环：按发布时间倒序；卡片 = 特色图 + 标题 + 摘要 + Read More） -->
    <ul class="project-grid">
      <?php while (have_posts()) : the_post();
        // 特色图：优先特色图（jc-card 尺寸），没有则兜底轮换示例图（news-1/2/3.jpg）
        $thumb = get_the_post_thumbnail_url(get_the_ID(), 'jc-card');
        if (!$thumb) {
            $thumb = $theme_uri . '/assets/images/news/news-' . (($wp_query->current_post % 3) + 1) . '.jpg';
        }
      ?>
        <li class="project-card">
          <a href="<?php the_permalink(); ?>" class="project-card-link" title="<?php echo esc_attr(get_the_title()); ?>">
            <div class="project-card-thumb"><img loading="lazy" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"></div>
            <div class="project-card-body">
              <h3 class="project-card-title"><?php the_title(); ?></h3>
              <p class="project-card-desc"><?php echo esc_html(get_the_excerpt()); ?></p>
              <span class="project-card-more"><?php echo esc_html(jc_t('Read More')); ?></span>
            </div>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>

    <!-- 分页：the_posts_pagination()（单页时不输出；样式在 project-list.css） -->
    <?php
    the_posts_pagination(array(
        'mid_size'  => 2,
        'end_size'  => 1,
        'prev_text' => '<span class="arrow">&lt;</span>' . esc_html(jc_t('Previous')),
        'next_text' => esc_html(jc_t('Next')) . '<span class="arrow">&gt;</span>',
        'screen_reader_text' => jc_t('Projects pagination'),
    ));
    ?>
  <?php else : ?>
    <!-- 暂无内容（兜底提示，不发 404） -->
    <ul class="project-grid">
      <li class="project-card" style="grid-column:1/-1;text-align:center;padding:4rem 2rem;">
        <p style="font-size:1.6rem;color:#666;"><?php echo esc_html(jc_t('No projects published yet. Please check back later.')); ?></p>
      </li>
    </ul>
  <?php endif; ?>

</div>

<?php get_footer(); ?>
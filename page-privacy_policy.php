<?php
/**
 * Template Name: Privacy Policy
 * page-privacy_policy.php — 隐私政策页
 * WordPress 按页面 slug = privacy_policy 自动匹配本模板（后台无需手动选模板）；
 * 若 slug 不同，可在后台「页面属性 → 模板」手动选择 "Privacy Policy"。
 * 静态版对照：E:\jingxiang\site\privacy-policy.html
 *
 * 字段读取规则（零新增字段）：
 *   - Banner 背景图：页面自身 site_banner 字段（与 About/Contact 页同款），
 *     空则兜底主题图 banner-news.jpg。若想让本页 banner 后台可换，
 *     把 site_banner 字段组的位置规则加上「页面 == Privacy Policy」即可。
 *   - 正文：直接用后台页面编辑器写（the_content），标题 = 页面标题（唯一 H1）。
 *     内容建议按原站结构录入：Intro / Collection（4 项列表）/ Use / Disclosure。
 * CSS：assets/css/pp.css 在 functions.php jc_assets() 里 is_page('privacy_policy') 条件加载
 */
get_header();

$page_id   = get_the_ID();
$theme_uri = get_template_directory_uri();

/* 图片/文件字段取值：兼容 ACF 三种返回格式（图片 ID / 数组 / URL 字符串），其余原样 */
$pimg = function ($v) {
    if (is_numeric($v)) {
        $u = function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url((int) $v, 'full') : '';
        if (!$u && function_exists('wp_get_attachment_url')) { $u = wp_get_attachment_url((int) $v); }
        return $u ? $u : '';
    }
    if (is_array($v)) { return isset($v['url']) ? $v['url'] : ''; }
    return is_string($v) ? $v : '';
};

/* 页面专属字段读取（图片字段经 $pimg 兼容 ID/数组/URL；文本字段原样，空则兜底） */
$pf = function ($key, $default = '') use ($page_id, $pimg) {
    $v = function_exists('get_field') ? get_field($key, $page_id) : '';
    $v = $pimg($v);
    return ($v !== '' && $v !== null && $v !== false) ? $v : $default;
};

$banner_img = $pf('site_banner', $theme_uri . '/assets/images/banner-news.jpg');
?>

<!-- ===================== PAGE BANNER（背景图 = 字段 site_banner；标题 = 页面标题；面包屑自动） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner_img); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php the_title(); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(jc_home_url()); ?>"><?php echo esc_html(jc_t('HOME')); ?></a> &gt;
      <span><?php the_title(); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 正文（页面编辑器 the_content，排版 = pp.css） ===================== -->
<div class="container pp-page">
  <article class="pp-content">
    <h1 class="pp-title"><?php the_title(); ?></h1>
    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
  </article>
</div>

<?php get_footer(); ?>

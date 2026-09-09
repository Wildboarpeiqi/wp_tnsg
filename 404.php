<?php
/**
 * 404.php — 404 错误页（WP 标准模板：访问不存在的 URL 时自动调用）
 * 静态版对照：E:\jingxiang\site\404.html
 * 说明：
 *   - 零字段、零数据库查询，纯静态错误页
 *   - Back to Home → 首页；Contact Us → 联系页（slug=contact_us，jc_page_url 自动取链接）
 *   - 404 页自动 noindex（The SEO Framework 默认处理）
 * CSS：assets/css/404.css 在 functions.php jc_assets() 里 is_404() 条件加载
 */
get_header();
?>

<!-- ===================== 404 主体（浅灰底 + 大号 404 + 双按钮） ===================== -->
<section class="nf">
  <div class="container nf-inner">
    <div class="nf-code">404</div>
    <h1 class="nf-title"><?php echo esc_html(jc_t('Page Not Found')); ?></h1>
    <p class="nf-desc"><?php echo esc_html(jc_t('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable. Please return to the homepage or contact us for assistance.')); ?></p>
    <div class="nf-actions">
      <a href="<?php echo esc_url(jc_home_url()); ?>" class="btn-main"><?php echo esc_html(jc_t('Back to Home')); ?></a>
      <a href="<?php echo esc_url(jc_page_url('contact_us')); ?>" class="btn-outline"><?php echo esc_html(jc_t('Contact Us')); ?></a>
    </div>
  </div>
</section>

<?php get_footer(); ?>

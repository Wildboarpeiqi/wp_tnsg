<?php
/**
 * Search form — 全站搜索（搜索弹窗内）
 * 笔记约定：name="s" 是 WordPress 默认搜索机制的固定参数，不能改。
 * get_search_form() 会优先加载主题里的这份 searchform.php。
 * 原 action="search.html" 改为 home_url('/')，由 WordPress 搜索结果页接管。
 */
?>
<div class="modal" id="searchModal" aria-hidden="true">
  <div class="modal-box search-modal">
    <button class="modal-close" data-close="searchModal" aria-label="Close">&times;</button>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
      <input type="text" name="s" placeholder="Search starts here...">
      <button type="submit" class="btn-main">Search</button>
    </form>
  </div>
</div>

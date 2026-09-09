<?php
/* ==========================================================================
   Xiaobai Basic — functions.php
   Liaocheng TNSG (northforging.com) 首页主题
   结构约定见主题根目录《ACF字段清单.md》：用户在 ACF 后台建字段，本文件只负责调用。
   ========================================================================== */

/* =========================
   1. THEME SETUP（主题功能）
   ========================= */

function jc_theme_setup() {

    // 网页 <title> 交给 WordPress / SEO 插件（The SEO Framework）控制
    add_theme_support('title-tag');

    // 支持特色图片（产品/新闻的卡片图都用它）
    add_theme_support('post-thumbnails');

    // 支持自定义 Logo（外观 → 自定义 → 站点身份）
    add_theme_support('custom-logo');

    // 产品/新闻卡片缩略图尺寸（按设计稿 4:3 卡片比例裁剪）
    add_image_size('jc-card', 600, 450, true);

    // 输出更标准的 HTML5
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));

    // 注册菜单位置（后台 外观 → 菜单 里指定显示位置）
    register_nav_menus(array(
        'primary_menu' => 'Primary Menu',   // 顶部主导航
        'footer_menu'  => 'Footer Menu',    // footer Quick Links
    ));
}
add_action('after_setup_theme', 'jc_theme_setup');

/* =========================
   2. CSS & JS（wp_enqueue_scripts）
   ========================= */

function jc_assets() {

    $theme_uri = get_template_directory_uri();

    // 全站唯一公共 CSS（静态站原样搬运，路径已适配 assets/css/）
    wp_enqueue_style(
        'jc-main',
        $theme_uri . '/assets/css/style.css',
        array(),
        '1.0.5'   // 改 CSS 后递增版本号，强制浏览器刷新缓存（2026-09-06：+ hero 视频媒体样式 .hero-video）
    );

    // 全站唯一公共 JS（滑块/计数器/菜单/弹窗，零依赖）
    wp_enqueue_script(
        'jc-main',
        $theme_uri . '/assets/js/main.js',
        array(),
        '1.0.4',   // 2026-09-06：修正嵌入视频 cover 尺寸计算（宽屏下不再左右留白）
        true      // 放在 </body> 前加载
    );

    // 产品详情页专属样式 + 脚本（仅 single-product.php，即 CPT product 单页）
    if (is_singular('product')) {
        wp_enqueue_style(
            'jc-product-single',
            $theme_uri . '/assets/css/product-single.css',
            array('jc-main'),
            '1.0.0'
        );
        wp_enqueue_script(
            'jc-product-single',
            $theme_uri . '/assets/js/product-single.js',
            array('jc-main'),
            '1.0.0',
            true
        );
    }

    // 产品列表页专属样式 + 脚本（产品总列表 / 单分类列表）
    // 模板：archive-product.php + taxonomy-product_category.php
    if (is_post_type_archive('product') || is_tax('product_category')) {
        wp_enqueue_style(
            'jc-product-list',
            $theme_uri . '/assets/css/product-list.css',
            array('jc-main'),
            '1.0.0'
        );
        wp_enqueue_script(
            'jc-product-list',
            $theme_uri . '/assets/js/product-list.js',
            array('jc-main'),
            '1.0.0',
            true
        );
    }

    // 文章详情页专属样式（single-post.php）+ FAQ 详情页（single-faq.php）+ Project 详情页（single-project.php）共用
    // 三个详情页版式一致（banner/分栏/侧栏/表单），共用 news-single.css；公共布局已在 style.css
    if (is_singular('post') || is_singular('faq') || is_singular('project')) {
        wp_enqueue_style(
            'jc-news-single',
            $theme_uri . '/assets/css/news-single.css',
            array('jc-main'),
            '1.0.1'
        );
    }

    // 文章列表页 / 分类归档页专属样式（home.php 文章列表页 + category.php 三个 news 分类）
    // 注意：静态版 news-list.js 的前端过滤/分页在 WP 端由分类归档 URL + 服务器渲染承担，
    // 因此不再加载该脚本（避免空跑请求，2h2g 性能原则）；仅加载样式。
    if (is_home() || is_category()) {
        wp_enqueue_style(
            'jc-news-list',
            $theme_uri . '/assets/css/news-list.css',
            array('jc-main'),
            '1.0.0'
        );
    }

    // FAQ 列表页专属样式（archive-faq.php 归档页；静态版无专属 JS，这里只加载样式）
    if (is_post_type_archive('faq')) {
        wp_enqueue_style(
            'jc-faq-list',
            $theme_uri . '/assets/css/faq-list.css',
            array('jc-main'),
            '1.0.0'
        );
    }

    // Project 列表页专属样式（archive-project.php 归档页；静态版无专属 JS，这里只加载样式）
    if (is_post_type_archive('project')) {
        wp_enqueue_style(
            'jc-project-list',
            $theme_uri . '/assets/css/project-list.css',
            array('jc-main'),
            '1.0.0'
        );
    }

    // About Us 页专属样式 + 脚本（page-about_us.php，slug=about_us 自动匹配）
    // 公共样式（header/footer/banner/轮播基础）已在 style.css + main.js，这里只加载页面专属部分
    if (is_page('about_us')) {
        wp_enqueue_style(
            'jc-about',
            $theme_uri . '/assets/css/about.css',
            array('jc-main'),
            '1.0.1'   // 2026-09-06：移动端 CNC 一屏 2 张 + 隐藏轮播箭头
        );
        wp_enqueue_script(
            'jc-about',
            $theme_uri . '/assets/js/about.js',
            array('jc-main'),
            '1.0.1',
            true
        );
    }

    // Contact Us 页专属样式（page-contact_us.php，slug=contact_us 自动匹配）
    // 静态版无专属 JS（表单演示走 main.js 通用逻辑）；WP 端表单由 Fluent Forms 输出，故只加载 CSS
    if (is_page('contact_us')) {
        wp_enqueue_style(
            'jc-contact',
            $theme_uri . '/assets/css/contact.css',
            array('jc-main'),
            '1.0.1'   // 2026-09-06：PC 端表单宽松化（padding/gap 加大）
        );
    }

    // 404 页专属样式（404.php，WP 标准错误页模板；纯静态无 JS）
    if (is_404()) {
        wp_enqueue_style(
            'jc-404',
            $theme_uri . '/assets/css/404.css',
            array('jc-main'),
            '1.0.0'
        );
    }

    // 搜索结果页专属样式（search.php，WP 标准搜索模板；纯静态无 JS）
    if (is_search()) {
        wp_enqueue_style(
            'jc-search',
            $theme_uri . '/assets/css/search.css',
            array('jc-main'),
            '1.0.0'
        );
    }

    // Privacy Policy 页专属样式（page-privacy_policy.php，slug=privacy_policy 自动匹配；纯静态无 JS）
    if (is_page('privacy_policy')) {
        wp_enqueue_style(
            'jc-pp',
            $theme_uri . '/assets/css/pp.css',
            array('jc-main'),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'jc_assets');

/* 产品列表：archive-product / taxonomy-product_category 每页 9 条
   PC 3×3 = 9 个/页，有多页时显示分页。 */
function jc_product_archive_per_page($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->is_post_type_archive('product') || $query->is_tax('product_category')) {
        $query->set('posts_per_page', 9);
    }
}
add_action('pre_get_posts', 'jc_product_archive_per_page');

/* FAQ 归档页：每页 8 条（2 列 × 4 行，静态版约定；有多页时显示分页） */
function jc_faq_archive_per_page($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->is_post_type_archive('faq')) {
        $query->set('posts_per_page', 8);
    }
}
add_action('pre_get_posts', 'jc_faq_archive_per_page');

/* Project 归档页：每页 6 条（3 列 × 2 行，静态版约定；有多页时显示分页） */
function jc_project_archive_per_page($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->is_post_type_archive('project')) {
        $query->set('posts_per_page', 6);
    }
}
add_action('pre_get_posts', 'jc_project_archive_per_page');

/* 搜索结果排序：产品优先 → 其他（页面等）→ 新闻/FAQ 最后（2026-09-06 用户需求）
   ----------------------------------------------------------
   规则：只影响前台主查询的搜索结果（?s=关键词）
   1. product（产品）排最前
   2. page（页面）等其他类型排中间
   3. post（新闻）和 faq（FAQ）排最后
   4. 同一类型内部按发布时间倒序（WP 默认）
   实现：posts_orderby 过滤器，用 CASE 映射类型优先级；
   以后新增类型未在 CASE 中列出 → 默认归入中间档（ELSE 2），无需改代码。 */
function jc_search_orderby($orderby, $query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return $orderby;
    }
    global $wpdb;
    return "CASE {$wpdb->posts}.post_type "
         . "WHEN 'product' THEN 1 "
         . "WHEN 'post' THEN 3 "
         . "WHEN 'faq' THEN 4 "
         . "ELSE 2 END ASC, "
         . "{$wpdb->posts}.post_date DESC";
}
add_filter('posts_orderby', 'jc_search_orderby', 10, 2);

/* =========================
   产品列表排序（archive 产品总页 / taxonomy 分类页 共用）
   ----------------------------------------------------------
   规则（与用户约定一致）：
   1. 先按 分类的 sort_order 字段升序 排列分类（A=1 → B=2 → C=3…）
   2. 每个分类内部：
      - 有 product_sort 字段的产品 → 按该字段升序排在前面
      - 没有 product_sort 的产品 → 按发布时间（WP 默认倒序）排在有序产品后面
   3. 一个分类的所有产品排完 → 再排下一个分类的产品
   4. 无分类的产品追加到末尾
   返回排序后的产品 ID 数组。
   ========================= */
function jc_get_sorted_product_ids($term_id = 0) {
    // ---- 分类列表（sort_order 升序；$term_id>0 时只看该分类）----
    if ($term_id > 0) {
        $term = get_term($term_id, 'product_category');
        $cats = (!is_wp_error($term) && $term) ? array($term) : array();
    } else {
        $cats = get_terms(array('taxonomy' => 'product_category', 'hide_empty' => false));
        if (is_wp_error($cats)) { $cats = array(); }
        usort($cats, function ($a, $b) {
            $sa = (int) get_field('sort_order', 'product_category_' . $a->term_id);
            $sb = (int) get_field('sort_order', 'product_category_' . $b->term_id);
            if ($sa === $sb) { return ($a->name <=> $b->name); }
            return $sa <=> $sb;
        });
    }

    $sorted_ids = array();
    $seen       = array(); // 已出现产品 ID（防跨分类重复）

    // ---- 逐分类：取产品 → 分类内排序 → 追加 ----
    foreach ($cats as $cat) {
        $q = new WP_Query(array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'tax_query'      => array(array(
                'taxonomy' => 'product_category',
                'field'    => 'term_id',
                'terms'    => $cat->term_id,
            )),
        ));

        $with_sort    = array(); // pid => product_sort 值
        $without_sort = array(); // pid 列表（保持 WP 默认发布时间倒序）

        foreach ($q->posts as $pid) {
            if (isset($seen[$pid])) { continue; }
            $seen[$pid] = true;
            $ps = function_exists('get_field') ? get_field('product_sort', $pid) : '';
            if (is_numeric($ps) && (int) $ps > 0) {
                $with_sort[$pid] = (int) $ps;
            } else {
                $without_sort[] = $pid;
            }
        }

        asort($with_sort, SORT_NUMERIC);          // product_sort 升序
        foreach (array_keys($with_sort) as $pid) { $sorted_ids[] = $pid; }
        foreach ($without_sort as $pid)          { $sorted_ids[] = $pid; }
    }

    // ---- 无分类产品追加到末尾 ----
    $orphans = get_posts(array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'tax_query'      => array(array(
            'taxonomy' => 'product_category',
            'operator' => 'NOT EXISTS',
        )),
    ));
    foreach ($orphans as $pid) {
        if (!isset($seen[$pid])) {
            $seen[$pid]     = true;
            $sorted_ids[] = $pid;
        }
    }

    return $sorted_ids;
}

/* 字体预加载（与静态版一致，加快首屏字体渲染） */
function jc_preload_fonts() {
    $uri = get_template_directory_uri();
    $fonts = array(
        'KumbhSans-VariableFont_wght.ttf',
        'LeagueGothic-Regular-VariableFont_wdth.ttf',
        'NotoSans-Regular.ttf',
    );
    foreach ($fonts as $f) {
        echo '<link rel="preload" as="font" type="font/ttf" href="' . esc_url($uri . '/assets/fonts/' . $f) . '" crossorigin>' . "\n";
    }
}
add_action('wp_head', 'jc_preload_fonts', 2);

/* body 补充静态模板依赖的 class（CSS/JS 判断用） */
function jc_body_class($classes) {
    $classes[] = 'wp-theme';
    return $classes;
}
add_filter('body_class', 'jc_body_class');

/* =========================
   3. CPT + TAXONOMY（产品/证书）
   —— 全部统一由 ACF 管理（ACF 后台 > 文章类型 / 分类法 创建）。
   —— 这里【绝不】注册任何 CPT/分类法，否则会和你在 ACF 里建的冲突：
      * certificate 创建时提示"关键字已被 ACF 以外使用"就是因此
      * product 你改标签不生效也是因此（functions.php 的大写标签占住了）
   —— 前端对不存在的 CPT 会自动回退到静态演示内容，不会报错；ACF 建好后自动生效。
   ========================= */

// 注：CPT/分类法注册已完全移除，统一由 ACF 管理。
// 需要在此兜底时，请确认 ACF 未创建同名类型，否则会冲突。

/* =========================
   4. ACF 辅助函数（所有字段调用的统一入口）
   —— 免费版 ACF 没有 Options Page，公共字段按你的笔记约定：
      字段组挂在"前台首页"上，代码用首页 ID 直调。
   —— 你已把「全局公共字段」（Company Phone/Mob/WhatsApp/Email/Address/Slogan 等）
      单独建组放在一个空白页面（页面 ID 62）上，代码会自动从该页面读取。
   —— 每个字段都有静态兜底值：字段还没建/没填时，前台照样显示默认内容，不散架。
   ========================= */

/* 全局公共字段所在页面 ID（你在后台建的"公共字段"空白页，可随时改这里） */
if (!defined('JC_GLOBAL_FIELD_ID')) {
    define('JC_GLOBAL_FIELD_ID', 62);
}
/**
 * 根据当前语言取得某篇 Post / Page 的对应翻译 ID。
 *
 * - Polylang 未启用：返回原 ID
 * - Polylang 已启用且存在当前语言翻译：返回翻译 ID
 * - 当前语言没有对应翻译：返回原 ID
 */
function jc_translate_post_id($post_id) {

    $post_id = (int) $post_id;

    if ($post_id <= 0) {
        return 0;
    }

    if (function_exists('pll_get_post')) {

        $translated_id = pll_get_post($post_id);

        if ($translated_id) {
            return (int) $translated_id;
        }
    }

    return $post_id;
}

/**
 * 当前语言对应的全局公共字段页 ID。
 *
 * JC_GLOBAL_FIELD_ID 始终保存默认语言公共字段页的基准 ID，
 * 具体读取时根据 Polylang 当前语言自动取得对应翻译页。
 */
function jc_global_field_id() {

    return jc_translate_post_id(
        JC_GLOBAL_FIELD_ID
    );
}


/* 当前前台首页 ID（静态首页=该页面；博客列表首页=0 时退回当前 ID）
   更健壮的探测：静态首页未设置时，遍历页面找 slug=home 或第一个页面 */
function jc_front_id() {
    $id = (int) get_option('page_on_front');
    if ($id > 0) {
        return jc_translate_post_id($id);
    }
    // 静态首页未设置：找 slug 为 home 的页面
    $home = get_page_by_path('home');
    if ($home) {
        return $home->ID;
    }
    // 再退：取任意一个已发布页面
    $any = get_posts(array('post_type' => 'page', 'posts_per_page' => 1, 'post_status' => 'publish', 'orderby' => 'menu_order ID', 'order' => 'ASC'));
    if ($any) {
        return $any[0]->ID;
    }
    return get_the_ID();
}

/* 判断某个字段 key 是否属于"全局公共字段"（仅 company_ 前缀：公司联系信息/标语都在公共字段页 62） */
function jc_is_global_key($key) {
    return strpos($key, 'company_') === 0;
}

/* 字段所属的对象 ID：公共字段用全局页（62），其他用首页 */
function jc_field_id($key) {
    return jc_is_global_key($key) ? jc_global_field_id() : jc_front_id();
}

/* 取文本/选择类字段，空则返回兜底值
   注意：ACF 未填时可能返回 false，也按空处理 */
function jc_get($key, $default = '') {
    $v = function_exists('get_field') ? get_field($key, jc_field_id($key)) : '';
    return ($v !== '' && $v !== null && $v !== false) ? $v : $default;
}

/* 取图片字段（Image 字段返回格式选"图片地址 Image URL"），空则返回主题内置演示图
   兼容返回数组（url 字段）或字符串两种情况 */
function jc_img($key, $default_path) {
    $v = function_exists('get_field') ? get_field($key, jc_field_id($key)) : '';
    if (is_array($v) && !empty($v['url'])) {
        $v = $v['url'];
    }
    return ($v !== '' && $v !== null && $v !== false) ? $v : get_template_directory_uri() . $default_path;
}

/* 取 Link 字段（返回格式选"数组 Array"：url+title），空则用兜底 URL/文案
   兼容返回数组、字符串 URL、false 三种情况 */
function jc_link($key, $default_url, $default_text) {
    $v = function_exists('get_field') ? get_field($key, jc_field_id($key)) : '';
    if (is_array($v) && !empty($v['url'])) {
        return array('url' => $v['url'], 'text' => !empty($v['title']) ? $v['title'] : $default_text);
    }
    if (is_string($v) && $v !== '') {
        return array('url' => $v, 'text' => $default_text);
    }
    return array('url' => $default_url, 'text' => $default_text);
}

/* 取嵌套 Group 内的字段（你的首页相关大字段结构：homepage_slides > hero1 > hero_1_title）
   用法：jc_group('homepage_slides', 'hero1_hero_1_title', '默认值') */
function jc_group($group, $path, $default = '') {
    return jc_get($group . '_' . $path, $default);
}

/* 视频嵌入链接转换（hero 轮播视频用，2026-09-06）
   支持 YouTube（watch?v= / youtu.be/ / shorts/ / embed/）和 Vimeo，
   自动转成 embed 格式并带上 autoplay+mute+loop 参数（背景视频静音自动播）；
   其他链接原样返回（用户填的已是 embed 嵌入链接）。 */
function jc_video_embed_url($url) {
    // 兼容 Textarea/URL/Text 类型：清掉换行和多余空白（防止粘贴时带入杂字符）
    $url = preg_replace('/\s+/', '', trim((string)$url));
    if ($url === '') { return ''; }
    // YouTube
    if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
        $id = $m[1];
        return 'https://www.youtube.com/embed/' . $id . '?autoplay=1&mute=1&playsinline=1&loop=1&playlist=' . $id;
    }
    // Vimeo
    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&muted=1&loop=1';
    }
    return $url;
}

/* 读公共字段页 62（JC_GLOBAL_FIELD_ID）上的字段。
   详情页的 page_banner_*、ws_*、side_*、quote_*、related_*、btn_* 等全局字段都挂在 62 页，
   但 jc_is_global_key() 只把 company_ 前缀归 62，所以这些字段要用本函数读，避免误读首页。
   用法：
     jc_g62('ws_video_title', 'Production Workshop Video')          → 简单文本/选择/URL 字段
     jc_g62('ws_forging_imgs', 'img_1', '')                          → Group 内子字段（拼完整路径）
     $imgs = jc_g62('ws_forging_imgs')                               → 整个 Group 数组（foreach 遍历）
 */
function jc_g62($key, $path_or_default = '', $default = null) {

    // 取得当前语言对应的公共字段页 ID
    $global_id = jc_global_field_id();

    if (!function_exists('get_field')) {
        return $default !== null ? $default : '';
    }

    if (func_num_args() >= 3) {

        // jc_g62('ws_forging_imgs', 'img_1', '默认')
        $real = get_field(
            $key . '_' . $path_or_default,
            $global_id
        );

        return (
            $real !== null
            && $real !== ''
            && $real !== false
        ) ? $real : $default;
    }

    // jc_g62('ws_video_title', '默认')
    // jc_g62('ws_forging_imgs')
    $v = get_field(
        $key,
        $global_id
    );

    if (
        $v !== null
        && $v !== ''
        && $v !== false
    ) {
        return $v;
    }

    return $path_or_default;
}

/* 当前语言首页链接 */
function jc_home_url() {

    if (function_exists('pll_home_url')) {

        $url = pll_home_url();

        if ($url) {
            return $url;
        }
    }

    return home_url('/');
}

/* 按页面 slug 取页面链接（页面还没建时返回 #，前台不出现 404 死链） */
function jc_page_url($slug) {

    $page = get_page_by_path($slug);

    if (!$page) {
        return '#';
    }

    $page_id = jc_translate_post_id(
        $page->ID
    );

    return get_permalink($page_id);
}

/**
 * 当前语言对应的 WordPress Posts Page ID。
 */
function jc_posts_page_id() {

    $id = (int) get_option('page_for_posts');

    if ($id <= 0) {
        return 0;
    }

    return jc_translate_post_id($id);
}

/* 产品归档链接（CPT 已注册即有效） */
function jc_products_url() {
    $u = get_post_type_archive_link('product');
    return $u ? $u : '#';
}

/* WhatsApp 链接：公共字段只填纯数字（如 8618596356103），这里统一拼 wa.me */
function jc_wa_url() {
    $num = preg_replace('/\D+/', '', jc_get('company_whatsapp', '8618596356103'));
    return 'https://wa.me/' . $num;
}

/* =========================
   4.6 字段调试工具（仅登录管理员可用）
   —— 前台地址后加 ?jc_debug=1 即可看到每个字段实际读到什么、从哪个页面读的。
   —— 用途：排查“后台改了字段前端没变化”——能立刻看出是字段没挂对页面、没填值、还是读错页。
   ========================= */
function jc_debug_panel() {
    if (empty($_GET['jc_debug'])) {
        return;
    }
    if (!current_user_can('manage_options')) {
        return;
    }
    $front = jc_front_id();
    $global = JC_GLOBAL_FIELD_ID;
    echo '<style>#jc-debug{position:fixed;right:10px;bottom:10px;z-index:99999;background:#111;color:#0f0;font:11px/1.5 Consolas,monospace;padding:10px;max-width:520px;max-height:70vh;overflow:auto;border:1px solid #0f0;white-space:pre-wrap;}</style>';
    echo '<div id="jc-debug">';
    echo '<b>[JC 字段调试]</b><br>';
    echo 'ACF 插件: ' . (function_exists('get_field') ? '已启用' : '未启用(!!)') . '<br>';
    echo '静态首页 ID (page_on_front): ' . get_option('page_on_front') . '<br>';
    echo 'jc_front_id() 返回值: ' . $front . '<br>';
    echo '公共字段页 (JC_GLOBAL_FIELD_ID): ' . $global . '<br>';
    echo '当前页面 ID: ' . get_the_ID() . '<br>';
    echo '------------------------<br>';
    // 测试公共字段
    foreach (array('company_email', 'company_phone', 'company_whatsapp', 'company_slogan') as $k) {
        $v = function_exists('get_field') ? get_field($k, $global) : '';
        echo '公共[' . $k . '] @' . $global . ' = ' . (is_scalar($v) ? var_export($v, true) : json_encode($v)) . '<br>';
    }
    echo '------------------------<br>';
    // 字段组挂载检测：列出服务器上所有 ACF 字段组、位置规则、匹配的页面、字段清单
    if (function_exists('acf_get_field_groups')) {
        $all = acf_get_field_groups();
        echo '== ACF 字段组清单 (' . count($all) . ' 个) ==<br>';
        foreach ($all as $g) {
            echo '■ 组: ' . $g['title'] . ' (key=' . $g['key'] . ')<br>';
            $loc = isset($g['location']) ? $g['location'] : array();
            foreach ($loc as $rg) {
                foreach ($rg as $rule) {
                    echo '   · 位置规则: ' . $rule['param'] . ' ' . $rule['operator'] . ' ' . $rule['value'] . '<br>';
                }
            }
            // 该组实际匹配哪些页面
            $m39 = acf_get_field_groups(array('post_id' => 39));
            $m62 = acf_get_field_groups(array('post_id' => 62));
            $in39 = false;
            foreach ($m39 as $mg) { if ($mg['key'] === $g['key']) { $in39 = true; } }
            $in62 = false;
            foreach ($m62 as $mg) { if ($mg['key'] === $g['key']) { $in62 = true; } }
            echo '   · 匹配 HOME(39): ' . ($in39 ? '✓是' : '✗否') . ' | 匹配公共页(62): ' . ($in62 ? '✓是' : '✗否') . '<br>';
            $fields = function_exists('acf_get_fields') ? acf_get_fields($g) : array();
            if ($fields) {
                echo '   · 字段明细 (含类型/子字段):<br>';
                foreach ($fields as $f) {
                    $ft = isset($f['type']) ? $f['type'] : '?';
                    $fk = isset($f['key']) ? $f['key'] : '';
                    echo '     - ' . $f['name'] . ' [类型: ' . $ft . '] (key=' . $fk . ')';
                    if (!empty($f['sub_fields'])) {
                        $subs = array();
                        foreach ($f['sub_fields'] as $sf) {
                            $subs[] = $sf['name'] . '[' . (isset($sf['type']) ? $sf['type'] : '?') . ']';
                        }
                        echo '  → 子字段: ' . implode(', ', $subs);
                    }
                    // 首页相关组：打印实际值
                    if ($g['key'] === 'group_6a9137e7bddeb') {
                        $fv = function_exists('get_field') ? get_field($f['name'], 39) : '';
                        echo '  ★值: ' . (is_scalar($fv) ? var_export($fv, true) : json_encode($fv, JSON_UNESCAPED_UNICODE));
                    }
                    echo '<br>';
                }
            }
        }
    } else {
        echo '== acf_get_field_groups 不可用 ==<br>';
    }
    echo '------------------------<br>';
    // 测试首页字段
    foreach (array('hero_company', 'hero_1_title', 'about_company', 'about_text', 'products_title', 'svc_1_title') as $k) {
        $v = function_exists('get_field') ? get_field($k, $front) : '';
        echo '首页[' . $k . '] @' . $front . ' = ' . (is_scalar($v) ? var_export($v, true) : json_encode($v)) . '<br>';
    }
    echo '------------------------<br>';
    echo '前端显示的 hero_company = ' . var_export(jc_get('hero_company', '(默认TNSG)'), true) . '<br>';
    echo '前端显示的 company_email = ' . var_export(jc_get('company_email', '(默认)'), true) . '<br>';
    echo '</div>';
}
add_action('wp_footer', 'jc_debug_panel');

/* =========================
   4.5 SVG 图标辅助函数
   —— 原站 yiyingbao 图标字体有版权风险，全部改为内联 SVG（免费开源、零依赖、可配 currentColor）。
   —— 用法：echo jc_icon('search');  /  echo jc_icon('arrow-left');
   ========================= */

function jc_icon($name) {
    $icons = array(
        // 通用 stroke 图标（viewBox 24）
        'search'       => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
        'arrow-left'   => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>',
        'arrow-right'  => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>',
        'chevron-down' => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>',
        'arrow-up'     => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>',
        'mail'         => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'phone'        => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        'whatsapp'     => '<svg class="jc-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>',
        'wechat'       => '<svg class="jc-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 0 0 .167-.054l1.903-1.114a.864.864 0 0 1 .717-.098 10.16 10.16 0 0 0 2.837.403c.276 0 .543-.027.811-.05a6.133 6.133 0 0 1-.24-1.71c0-3.571 3.468-6.468 7.745-6.468.214 0 .424.013.634.026C16.85 4.59 13.154 2.188 8.691 2.188zM5.892 6.5a.978.978 0 1 1 0 1.956.978.978 0 0 1 0-1.956zm5.6 0a.978.978 0 1 1 0 1.956.978.978 0 0 1 0-1.956zM24 13.582c0-3.235-3.08-5.857-6.876-5.857s-6.876 2.622-6.876 5.857c0 3.235 3.08 5.858 6.876 5.858.632 0 1.246-.083 1.823-.222a.64.64 0 0 1 .52.06l1.476.855a.273.273 0 0 0 .211.036.272.272 0 0 0 .206-.268c0-.06-.02-.117-.034-.174l-.28-1.078a.633.633 0 0 1 .213-.66A5.7 5.7 0 0 0 24 13.582zm-8.64-1.394a.734.734 0 1 1 0-1.468.734.734 0 0 1 0 1.468zm3.53 0a.734.734 0 1 1 0-1.468.734.734 0 0 1 0 1.468z"/></svg>',
        // About 统计图标
        'stat-handshake' => '<svg class="jc-icon" viewBox="0 0 1170 1024" fill="currentColor" aria-hidden="true"><path d="M944.128 480.621714l-44.982857-46.153143a33.792 33.792 0 0 0-4.461714-3.949714L666.258286 262.070857a36.717714 36.717714 0 0 0-62.976-31.963428c-0.731429 0.731429-66.633143 70.217143-186.514286 66.340571a82.578286 82.578286 0 0 1-59.245714-27.794286l168.886857-142.409143 411.282286-45.933714 150.089142 247.369143-143.652571 152.941714z m69.266286 225.718857a36.571429 36.571429 0 0 1-51.346286 2.340572l-71.021714-65.609143-0.219429-0.146286-0.950857-0.877714-0.146286-0.146286h-0.073143a36.717714 36.717714 0 0 0-48.566857 55.149715l70.875429 65.389714 0.512 0.512 13.385143 12.434286a36.571429 36.571429 0 0 1-49.444572 53.540571l-48.713143-44.982857-1.462857-1.316572-48.786285-45.129142-0.585143-0.438858a36.717714 36.717714 0 0 0-49.810286 54.052572l50.102857 46.299428a36.278857 36.278857 0 0 1 1.243429 50.761143 36.132571 36.132571 0 0 1-51.492572 2.048l-72.411428-66.852571-1.024-0.877714-31.670857-29.257143a36.790857 36.790857 0 1 0-49.810286 54.125714l32.182857 29.696c15.067429 14.409143 17.261714 39.862857 4.461714 53.686857a36.278857 36.278857 0 0 1-50.395428 3.072l-52.589715-59.392c13.165714-14.994286 21.504-33.645714 23.698286-53.979428a97.28 97.28 0 0 0-21.138286-71.826286c-21.430857-26.624-56.685714-38.765714-89.892571-34.669714a97.133714 97.133714 0 0 0-21.504-65.682286 96.402286 96.402286 0 0 0-55.369143-33.133714 97.865143 97.865143 0 0 0-17.846857-88.137143 93.622857 93.622857 0 0 0-37.302857-27.574857c8.338286-29.549714 2.852571-62.610286-17.846857-88.283429-32.694857-40.521143-97.133714-47.469714-137.654857-14.774857l-3.072 2.413714-44.982858-73.142857 221.257143-239.689143 83.236572 61.44L285.257143 233.472a36.790857 36.790857 0 0 0-10.093714 42.496c1.536 3.657143 39.716571 90.697143 139.190857 93.988571a343.625143 343.625143 0 0 0 199.68-55.076571l234.642285 173.056 162.669715 166.912a36.571429 36.571429 0 0 1 2.121143 51.492571z m-634.075429 177.371429a24.283429 24.283429 0 0 1-30.573714-37.888l68.022857-54.784a24.283429 24.283429 0 0 1 30.500571 37.888l-67.949714 54.857143zM233.691429 779.702857a24.356571 24.356571 0 0 1 3.657142-34.230857l68.022858-54.784a24.137143 24.137143 0 0 1 39.424 21.577143 23.990857 23.990857 0 0 1-8.923429 16.310857l-68.022857 54.784a24.283429 24.283429 0 0 1-34.157714-3.657143zM155.209143 640.585143a23.990857 23.990857 0 0 1 8.923428-16.310857l68.022858-54.784a24.283429 24.283429 0 0 1 30.500571 37.888l-68.022857 54.784a24.137143 24.137143 0 0 1-39.497143-21.577143z m52.224-149.138286l-67.949714 54.857143a24.283429 24.283429 0 0 1-30.500572-37.888l68.022857-54.857143a24.283429 24.283429 0 0 1 30.500572 37.888zM1165.165714 313.490286L988.306286 22.089143a36.717714 36.717714 0 0 0-35.474286-17.481143L507.172571 54.418286a36.717714 36.717714 0 0 0-19.602285 8.411428l-42.496 35.84L321.097143 7.241143a36.717714 36.717714 0 0 0-48.786286 4.608L9.728 296.155429a36.717714 36.717714 0 0 0-4.242286 44.178285l64.512 104.96-7.168 5.851429a97.060571 97.060571 0 0 0-35.84 65.682286 97.572571 97.572571 0 0 0 58.148572 100.205714 98.304 98.304 0 0 0 17.993143 87.552c14.189714 17.554286 33.645714 29.476571 55.076571 34.157714a98.011429 98.011429 0 0 0 108.105143 122.587429 97.499429 97.499429 0 0 0 97.792 101.302857c22.235429 0 43.958857-7.68 61.366857-21.650286l23.844571-19.236571 55.296 62.464a109.348571 109.348571 0 0 0 81.627429 31.744 109.348571 109.348571 0 0 0 76.434286-35.328c8.484571-9.216 15.213714-19.894857 19.894857-31.524572 20.699429 16.822857 46.08 25.307429 73.435428 24.283429a109.202286 109.202286 0 0 0 76.434286-35.328c9.874286-10.678857 17.408-22.966857 22.308572-36.132572a110.006857 110.006857 0 0 0 155.574857-112.932571c21.211429-4.681143 41.252571-15.652571 57.051428-32.768 41.179429-44.544 38.4-114.249143-4.827428-154.038857l-66.998857-68.827429 164.937142-175.689143a36.717714 36.717714 0 0 0 4.681143-44.178285z" fill="currentColor" /></svg>',
        'stat-trophy'   => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 21h8M12 17v4M7 4h10v6a5 5 0 0 1-10 0V4z"/><path d="M7 6H4a2 2 0 0 0 2 4h1M17 6h3a2 2 0 0 1-2 4h-1"/></svg>',
'stat-users'   => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
'stat-veteran' => '<svg class="jc-icon" viewBox="0 0 1024 1024" fill="currentColor" aria-hidden="true"><path d="M387.43871 1024H33.267525c-18.425091 0-33.267525-14.842434-33.267525-33.267525v-354.171185c0-18.425091 14.842434-33.267525 33.267525-33.267525h354.171185c18.425091 0 33.267525 14.842434 33.267525 33.267525v354.171185c0 18.425091-14.842434 33.267525-33.267525 33.267525zM66.535049 957.464951h287.636137v-287.636137H66.535049V957.464951zM923.81357 1024h-354.171186c-18.425091 0-33.267525-14.842434-33.267525-33.267525v-354.171185c0-18.425091 14.842434-33.267525 33.267525-33.267525h354.171186c18.425091 0 33.267525 14.842434 33.267525 33.267525v354.171185c0 18.425091-14.842434 33.267525-33.267525 33.267525z m-320.903661-66.535049H890.546045v-287.636137h-287.636136V957.464951zM387.43871 487.113333H33.267525c-18.425091 0-33.267525-14.842434-33.267525-33.267525V100.18643C0 81.761339 14.842434 66.918905 33.267525 66.918905h354.171185c18.425091 0 33.267525 14.842434 33.267525 33.267525v354.171186c0 17.913283-14.842434 32.755717-33.267525 32.755717zM66.535049 421.090091h287.636137V132.942147H66.535049v288.147944zM746.727977 553.648382c-8.700737 0-16.889666-3.070848-23.543171-9.724354L480.075972 300.303386c-12.795202-12.795202-12.795202-33.779333 0-47.086342l243.620642-243.620643c12.795202-12.795202 33.779333-12.795202 47.086342 0l243.620643 243.620643c12.795202 12.795202 12.795202 33.779333 0 47.086342l-243.620643 243.620642c-6.653505 6.653505-15.354242 9.724353-24.054979 9.724354z m-196.022492-276.376359L747.239785 473.806323l196.5343-196.5343L747.239785 80.737723l-196.5343 196.5343z" fill="currentColor"/></svg>',
        // Our Service 图标（5 个）
        'svc-chain'     => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
        'svc-zap'       => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
        'svc-shield'    => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11 11 13 15 9"/></svg>',
        'svc-factory'   => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 20h20M4 20V9l5 3V9l5 3V9l5 3v8"/><path d="M9 20v-3h2v3M14 20v-3h2v3"/></svg>',
        'svc-badge'     => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M8.5 13.5 7 22l5-3 5 3-1.5-8.5"/></svg>',
        // Contact 页图标（与 mail/phone 同风格 stroke 图标）
        'map-pin'       => '<svg class="jc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
    );
    return isset($icons[$name]) ? $icons[$name] : '';
}

/* =========================
   5. 导航菜单 Walker（输出与静态模板完全一致的 class）
   —— 免费版方案：不用插件，一个小 Walker 让 wp_nav_menu 套上原 CSS。
   —— Resources 仅移动端显示：后台编辑该菜单项 → CSS 类 填 nav-resources
   ========================= */

class Jc_Nav_Walker extends Walker_Nav_Menu {

    /* 子菜单开始 */
    function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<ul class="nav-submenu">';
    }

    /* 每个菜单项 */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes, true);
        $is_current   = in_array('current-menu-item', $classes, true)
                        || in_array('current_page_item', $classes, true);
        // 后台"CSS 类"里手动加的类（如 nav-resources）原样带上
        $extra = array();
        foreach ($classes as $c) {
            if ($c && !in_array($c, array('menu-item', 'menu-item-has-children', 'menu-item-type-custom',
                'menu-item-type-post_type', 'menu-item-type-taxonomy', 'menu-item-object-page',
                'menu-item-object-custom', 'menu-item-object-product', 'menu-item-object-product_category',
                'current-menu-item', 'current_page_item', 'menu-item-home', 'has-submenu'), true)
                && strpos($c, 'menu-item-') !== 0) {
                $extra[] = $c;
            }
        }

        if ($depth > 0) {
            // 二级菜单项
            $output .= '<li><a href="' . esc_url($item->url) . '" class="nav-submenuA"><p>'
                     . esc_html($item->title) . '</p></a>';
        } else {
            $li  = 'mainNavLi' . ($has_children ? ' has-submenu' : '') . ($extra ? ' ' . esc_attr(implode(' ', $extra)) : '');
            $a   = 'mainNavLiA' . ($is_current ? ' selected' : '');
            $arrow = $has_children ? jc_icon('chevron-down') : '';
            $output .= '<li class="' . trim($li) . '"><a href="' . esc_url($item->url) . '" class="' . $a . '"><p>'
                     . esc_html($item->title) . '</p>' . $arrow . '</a>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

/* 菜单没建时的兜底（与静态模板相同的默认菜单） */
function jc_default_menu() {
    $sub = array(
        'Forged gear rings'          => 'forged-gear-rings',
        'Forged gear shafts'         => 'forged-gear-shafts',
        'Forged ring parts'          => 'forged-ring-parts',
        'Forged cylindrical parts'   => 'forged-cylindrical-parts',
        'Forged Ring Blank'          => 'forged-ring-blank',
        'Wheel-type forgings'        => 'wheel-type-forgings',
        'Slewing bearing rings'      => 'slewing-bearing-rings',
        'Flange forgings'            => 'flange-forgings',
        'Special-shaped forgings'    => 'special-shaped-forgings',
    );
    echo '<ul class="mainNav" id="mainNav">';
    echo '<li class="mainNavLi"><a href="' . esc_url(home_url('/')) . '" class="mainNavLiA selected"><p>HOME</p></a></li>';
    echo '<li class="mainNavLi"><a href="' . esc_url(jc_page_url('about_us')) . '" class="mainNavLiA"><p>ABOUT US</p></a></li>';
    echo '<li class="mainNavLi has-submenu"><a href="' . esc_url(jc_products_url()) . '" class="mainNavLiA"><p>PRODUCTS</p>' . jc_icon('chevron-down') . '</a>';
    echo '<ul class="nav-submenu">';
    foreach ($sub as $label => $slug) {
        echo '<li><a href="' . esc_url(jc_products_url()) . '" class="nav-submenuA"><p>' . esc_html($label) . '</p></a></li>';
    }
    echo '</ul></li>';
    echo '<li class="mainNavLi"><a href="' . esc_url(get_post_type_archive_link('faq')) . '" class="mainNavLiA"><p>FAQ</p></a></li>';
    echo '<li class="mainNavLi"><a href="' . esc_url(jc_page_url('news')) . '" class="mainNavLiA"><p>NEWS</p></a></li>';
    echo '<li class="mainNavLi"><a href="' . esc_url(jc_page_url('contact_us')) . '" class="mainNavLiA"><p>CONTACT US</p></a></li>';
    echo '<li class="mainNavLi nav-resources"><a href="#" class="mainNavLiA"><p>Resources</p></a></li>';
    echo '</ul>';
}

// ==========================================================================
// 第三方统计 / 广告 / 埋点代码挂载区（两段式，按代码位置分）
// --------------------------------------------------------------------------
// 怎么用：
//   1) 看你要加的代码该出现在哪：
//       · 出现在 <head> 里          → 放进下面的【HEAD 段】
//       · 出现在 <body> 标签之后     → 放进下面的【BODY 段】
//   2) 把 <script> / <meta> 等代码，粘贴到对应段的 <!-- ... --> 位置
//   3) 把该段下面的 add_action 前的 "//" 去掉（启用），即可生效
// 提示：想暂时关掉某段，就再把那行的 "//" 加回去。
// --------------------------------------------------------------------------

// ==================== 【HEAD 段】放在 <head> 里的代码 ====================
// 适用：GA4、Google Ads 标签脚本、Google/Bing 站验证 meta、百度统计 等
function jc_third_party_head() { ?>
    <!-- ↓↓↓ 把要放在 <head> 的代码粘贴到这一行下面 ↓↓↓ -->

<?php }
// add_action('wp_head', 'jc_third_party_head', 5);

// ==================== 【BODY 段】放在 <body> 标签之后的代码 ====================
// 适用：Google Ads 转换段 script、需要紧跟 body 加载的代码 等
function jc_third_party_body() { ?>
    <!-- ↓↓↓ 把要放在 <body> 后的代码粘贴到这一行下面 ↓↓↓ -->

<?php }
// add_action('wp_body_open', 'jc_third_party_body', 5);


/* =========================
   产品列表分页（archive-product.php / taxonomy-product_category.php 共用）
   $total 可选：排序后总页数；$current 可选：当前页码。
   不传时回退到主查询（兼容旧调用）。
   ========================= */
function jc_product_pagination($total = 0, $current = 0) {
    // 确保在主查询循环作用域内调用（在 have_posts() 之后可用）
    if (is_singular()) { return; }
    if ($total <= 0) {
        global $wp_query;
        $total = (int) $wp_query->max_num_pages;
    }
    if ($current <= 0) {
        $current = max(1, (int) get_query_var('paged'));
    }
    if ($total <= 1) { return; }

    $pages = paginate_links(array(
        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'format'    => '?paged=%#%',
        'current'   => $current,
        'total'     => $total,
        'type'      => 'array',
        'mid_size'  => 2,
        'end_size'  => 1,
        'prev_text' => '<span class="arrow">&lt;</span>Previous',
        'next_text' => 'Next<span class="arrow">&gt;</span>',
    ));
    if (!$pages) { return; }

    echo '<nav class="pl-pagination" aria-label="Products pagination">';
    echo '<ul class="pageBtnCon">';

    foreach ($pages as $page) {
        // 当前页：<li><span class="page-numbers current">1</span></li>
        if (strpos($page, 'current') !== false) {
            $num = wp_strip_all_tags($page);
            echo '<li><span class="pl-pageNum active">' . esc_html($num) . '</span></li>';
        }
        // 上一页按钮
        elseif (strpos($page, 'prev') !== false) {
            if (strpos($page, '<a') !== false) {
                // <a class="prev page-numbers">有链接→可点击
                echo '<li>' . preg_replace('/class="prev page-numbers"/', 'class="pl-pagePrev"', $page) . '</li>';
            } else {
                // 第一页没有链接 → 置灰
                echo '<li><span class="pl-pagePrev pl-disabled">' . wp_strip_all_tags($page) . '</span></li>';
            }
        }
        // 下一页按钮
        elseif (strpos($page, 'next') !== false) {
            if (strpos($page, '<a') !== false) {
                echo '<li>' . preg_replace('/class="next page-numbers"/', 'class="pl-pageNext"', $page) . '</li>';
            } else {
                echo '<li><span class="pl-pageNext pl-disabled">' . wp_strip_all_tags($page) . '</span></li>';
            }
        }
        // 省略号 → 不渲染圆点之外的，直接显示 …
        elseif (strpos($page, 'dots') !== false) {
            echo '<li><span class="pl-pageDots">' . wp_strip_all_tags($page) . '</span></li>';
        }
        // 普通页码
        else {
            // <a class="page-numbers">2</a> → 套上 pl-pageNum 容器类
            echo '<li>' . preg_replace('/<a([^>]*)class="page-numbers"/', '<a$1class="pl-pageNum"', $page) . '</li>';
        }
    }

    echo '</ul>';
    echo '</nav>';
}
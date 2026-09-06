# ACF 字段清单（前台首页）

> 使用说明：在 WordPress 后台 **自定义字段（ACF）→ 字段组 → 新建字段组**。
> 每个字段的 **字段名称（Field Name）** 必须与下表**完全一致**（决定前台能否读到，别改）。
> **位置规则（Location）**：
> - **公共字段组**（公司联系信息）→ 显示条件设为 **页面等于 → 你建的空白页面（当前页面 ID=62）**。代码已内置全局字段页 ID（`functions.php` 里 `JC_GLOBAL_FIELD_ID = 62`），凡 `company_` 开头或 `hero_company`/`about_company` 的字段，自动从该页面读取。
> - **其他区块字段**（Hero/About/应用/服务等）→ 显示条件设为 **页面等于 → 前台首页**（静态首页），代码用首页 ID 读取。
> 没建的字段、没填的内容：前台会自动回退到静态默认值，页面不会散架——所以你可以分批慢慢建。
> **图标说明**：本主题已全部改用**内联 SVG 图标**（免费开源、无版权问题、零外部依赖），不再使用原站 yiyingbao 图标字体。因此统计图标、服务图标等**不再需要 ACF 字段**，图标固定由代码输出。
> **编码提醒**：主题内全部 PHP/CSS/JS 均为 **UTF-8 无 BOM**，上传宝塔时请用二进制/原样上传，不要用文本编辑器转码，否则中文注释会乱码。

---

## 一、全局联系信息（公共字段组，挂在页面 ID=62）

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `company_email` | Text | `jiucheng@jcforging.com` | 邮箱 |
| `company_whatsapp` | Text | `8618596356103` | WhatsApp 号码（纯数字，代码自动拼 wa.me） |
| `company_phone` | Text | `8618596356103` | 电话（纯数字） |
| `company_mob` | Text | `8618596356103` | 手机（你已建，代码预留读取） |
| `company_address` | Textarea | `(空)` | 公司地址（你已建，代码预留读取） |
| `company_slogan` | Textarea | `Looking ahead, we will continue to uphold the philosophy of "professional export, value delivery".` | footer 标语 |

## 二、Hero 轮播（3 帧）

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `hero_company` | Text | `TNSG` | **公共字段（页 62）**：所有帧共用的公司名小标题 |
| `hero_1_bg` | Image（返回图片 URL） | `hero-1.jpg` | 第 1 帧背景图 |
| `hero_1_title` | Text | `Energy-saving forging, quality first` | 第 1 帧大标题 |
| `hero_1_desc` | Textarea | `Primarily manufactures high-end automotive components and precision automotive forgings.` | 第 1 帧描述 |
| `hero_1_align` | Text | `(空)` | 内容对齐：`left` / `center` / `right` |
| `hero_1_btn` | Link（返回数组） | `Learn More → About Us` | 第 1 帧主按钮（第一个） |
| `hero_1_btn2` | Link（返回数组） | `Contact Us → Contact` | 第 1 帧副按钮（第二个，可留空隐藏） |
| `hero_2_bg` | Image（URL） | `hero-2.webp` | 第 2 帧背景图 |
| `hero_2_title` | Text | `Deeply Rooted in Precision Forging` | |
| `hero_2_desc` | Textarea | `High-End Auto Components & Custom Forging Parts Manufacturer` | |
| `hero_2_align` | Text | `center` | |
| `hero_2_btn` | Link | `Learn More → Products` | |
| `hero_3_bg` | Image（URL） | `hero-3.jpg` | |
| `hero_3_title` | Text | `Precision Custom Forging Solutions` | |
| `hero_3_desc` | Textarea | `Primarily manufactures high-end automotive components and precision automotive forgings.` | |
| `hero_3_align` | Text | `right` | |
| `hero_3_btn` | Link | `Learn More → Products` | |

## 三、Products 区块头部（内容本身来自「产品」CPT，见文末）

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `products_eyebrow` | Text | `Our Hot Products` | 区块小标签 |
| `products_title` | Text | `Meet Your Products Needs` | 区块大标题 |
| `products_more` | Link | `Learn More → 产品列表页` | 右上角按钮 |

## 四、About 区块

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `about_bg` | Image（URL） | `about-bg.jpg` | 背景图 |
| `about_company` | Text | `TNSG` | **公共字段（页 62）**：公司名 |
| `about_text` | **Textarea** | （4 段公司介绍原文） | **公司介绍正文（单个字段，不再拆 4 段）** |
| `stat_1_num` | Text | `200` | 统计 1 数字（动画从 0 计数到此值） |
| `stat_1_label` | Text | `TRUSTED PARTNERS` | 统计 1 标签 |
| `stat_2_num` | Text | `253` | |
| `stat_2_label` | Text | `HIGH-QUALITY WORKFORCE` | |
| `stat_3_num` | Text | `188` | |
| `stat_3_label` | Text | `DEDICAYED PROFESSIONALS` | |
| `about_btn` | Link | `Learn More → About Us` | 底部按钮 |

> **关于 `about_text`（重要）**：正文全部放这一个字段里，**段落之间空一行**即可自动分段（代码用 `wpautop` 自动把空行转成段落）。内容多少随便填，支持换行。统计图标已改为代码内置 SVG，**不再需要 icon 字段**。

## 五、Certificate 证书轮播

**无需 ACF 字段** —— 证书由 **「证书」栏目（certificate CPT）** 提供：

- 后台 **证书 → 新增证书**，每篇证书一篇文章
- **标题** = 证书名；**特色图** = 证书图片
- 前台首页证书轮播自动拉取该栏目全部证书（按后台排序），想加几张加几张，不用动代码
- 还没录入证书时，前台显示主题内置的 5 张演示图兜底

## 六、Application 行业应用（5 个 slide + 5 个 tab 联动）

**Slide（大图+标题+描述）：**

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `app_1_bg` | Image（URL） | `apps/automotive.jpg` | 应用 1 背景图 |
| `app_1_title` | Text | `Automotive Accessories` | 应用 1 标题 |
| `app_1_desc` | Textarea | （原文） | 应用 1 描述 |
| `app_2_bg` / `app_2_title` / `app_2_desc` | Image/Text/Textarea | `consumer-electronics.jpg` / `Consumer Electronics` / … | 应用 2 |
| `app_3_*` | … | `industrial.jpg` / `Industrial Product` / … | 应用 3 |
| `app_4_*` | … | `telecom.jpg` / `Telecommunication Parts` / … | 应用 4 |
| `app_5_*` | … | `medical.jpg` / `Medical Parts` / … | 应用 5 |

**Tab（下方行业标签 + 图标）：**

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `ind_1_label` | Text | `Automotive Manufacturing` | Tab 1 文字 |
| `ind_1_icon` | Image（URL） | `icons/auto.png` | Tab 1 常态图标 |
| `ind_1_icon_white` | Image（URL） | `icons/auto-white.png` | Tab 1 高亮图标（蓝色时用白色版） |
| `ind_1_link` | Link | `→ 产品列表页` | Tab 1 点击跳转 |
| `ind_2_*` … `ind_5_*` | 同上 | machinery / petro / wind / ship 对应图标 | Tab 2–5 |

> 注意：tab 的 `data-index` 顺序与 slide 一一对应（1↔1、2↔2…）。若增删应用，需同步调整代码（暂不支持后台随意增删数量，属于固定 5 个的结构）。

## 七、News 区块

**无 ACF 字段**——直接读取 WordPress **文章（Posts）**：最新 3 篇自动展示（标题、日期、特色图、摘要）。没发布文章时显示静态演示新闻兜底。区块标题/副标题固定为 "News / 公司名"。

## 八、Our Service 服务（5 内容卡 + 1 CTA 卡）

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `svc_1_title` | Text | `Complete Industrial Chain Services` | |
| `svc_1_desc` | Textarea | `Integrated R&D, production, and sales…` | |
| `svc_1_link` | Link | `Learn More → About Us` | |
| `svc_2_*` … `svc_5_*` | 同上 | 对应原站 5 项服务内容 | 服务 2–5 |
| `svc_cta_bg` | Image（URL） | `service-bg.jpg` | CTA 卡背景 |
| `svc_cta_text` | Textarea | `Please contact us as soon as possible for cooperation!` | CTA 文案 |
| `svc_cta_link` | Link | `Contact Us → Contact` | CTA 按钮 |

> 服务卡片图标已改为代码内置 SVG（链/闪电/盾牌/工厂/徽章），**不再需要 icon 字段**。

---

## 需要同时在后台配置的（非 ACF）

| 事项 | 位置 | 说明 |
|---|---|---|
| 主菜单 | 外观 → 菜单 | 分配到 **Primary Menu** 位置；首页项加 "selected"，Resources 项 CSS 类填 `nav-resources` |
| 页脚菜单 | 外观 → 菜单 | 分配到 **Footer Menu** 位置（Quick Links） |
| 产品分类 | 产品 → 产品分类（`product_category`） | 建 9 个一级分类：Forged gear rings 等 |
| 产品 | 产品 → 新增产品 | 每篇设标题 + 特色图；首页自动取最新 8 个 |
| **证书** | **证书 → 新增证书** | 每篇设标题 + 特色图（证书图）；首页证书轮播自动展示 |
| 静态首页 | 设置 → 阅读 | 首页显示为**静态页面**，选"前台首页"页面 |
| 联系页面 | 页面 → 新增 | slug 用 `contact-us`；About 用 `about_us`；FAQ 用 `faq`；News 用 `news`（代码按 slug 找链接） |

> 钩子：`wp_head` 里预留了 GA4 统计代码位（functions.php 末尾注释块），拿到测量 ID 后启用。

---

## 九、产品详情页（single-product.php，2026-09-03 新增）

**页面对应模板**：`single-product.php`（产品 CPT 单页）。CSS/JS 已复制为 `assets/css/product-single.css` + `assets/js/product-single.js`，由 functions.php 在**产品详情页**（`is_singular('product')`）自动加载，其他页面不加载，不拖慢首页。

字段来源分三处，代码已写死读取规则，**你在 ACF 后台按下面位置规则建即可**（字段名必须一致）：

### A. 产品详情页公共字段（位置规则：**页面 == 62** 公共字段页）

| 字段名称 | 类型 | 默认值 | 单页里做什么 |
|---|---|---|---|
| `page_banner_img` | Image（URL） | `banner-products.png` | 顶部横幅背景图 |
| `page_banner_title` | Text | `PRODUCTS` | 横幅大标题（保持 PRODUCTS，产品名放正文 H1） |
| `side_nav_title` | Text | `Navigation` | 左栏标题 |
| `side_sub_text` | Textarea | `Order Now` | 左栏小标题 / 自由段（空则隐藏） |
| `side_email_text` | Text | `(空)` | 左栏蓝底邮箱（留空则用 company_email） |
| `ws_video_title` | Text | `Production Workshop Video` | 车间视频小标题（空则隐藏） |
| `ws_video_embed` | Textarea | `(空)` | 视频嵌入代码（YouTube iframe 等，**有值优先**） |
| `ws_video_file` | File | `(空)` | 视频 mp4 文件（embed 为空时才用） |
| `ws_video_poster` | Image（URL） | `(空)` | 视频封面（仅文件模式生效） |
| `ws_forging_title` | Text | `Forging workshop` | 锻造车间小标题 |
| `ws_forging_imgs` | Group | `(空)` | 组内 `img_1`~`img_6`（全部空则隐藏整块） |
| `ws_cnc_title` | Text | `CNC Machining Workshop` | CNC 车间小标题 |
| `ws_cnc_imgs` | Group | `(空)` | 组内 `img_1`~`img_6` |
| `ws_testing_title` | Text | `Testing equipment` | 检测设备小标题 |
| `ws_testing_imgs` | Group | `(空)` | 组内 `img_1`~`img_6` |
| `ws_cert_title` | Text | `Certificate` | 证书区块小标题（内容=**证书 CPT 轮播**） |
| `quote_title` | Text | `Get Your Free Quote Now!` | 底部询盘区标题（空则隐藏） |
| `related_title` | Text | `Related Products` | 相关产品标题（空则隐藏） |
| `btn_inquiry_text` | Text | `Inquiry` | 右栏按钮 1（平滑滚到 #quote） |
| `btn_related_text` | Text | `Related Products` | 右栏按钮 2（跳当前分类列表） |

### B. 产品字段（位置规则：**文章类型 == product**）

| 字段名称 | 类型 | 说明 |
|---|---|---|
| `product_gallery` | Group | 组内 `img_1`~`img_6`：主图取 `img_1`，缩略图取全部非空；未设则用产品特色图 |
| `product_sort` | Number | 已存在（首页排序用） |

> 产品介绍文字（product-desc）自动取产品**摘要（Excerpt）**，没填摘要则截取正文前 30 词。

### C. 分类字段（位置规则：**分类法 == product_category**）

| 字段名称 | 类型 | 说明 |
|---|---|---|
| `sort_order` | Number | 左栏分类排序：数字小在前，没填按创建时间升序 |

### 非字段项（代码已实现）

- 左栏询盘表单 `[fluentform id="3"]`、底部询盘 `[fluentform id="4"]`，后端用 `shortcode_exists()` 判断，未装 Fluent Forms 时显示静态占位表单
- 证书轮播 = `WP_Query(post_type='certificate')`（同一套证书，首页/详情页共用）
- Related Products 轮播 = 当前产品**所属分类下**的其他产品（排除自己），自动轮播 + 箭头 + 手动
- Product Parameters 表格 = 产品**正文 the_content**（富文本里放表格）
- 视频区 embed / file 二选一，任一有值才显示；图片空则整块隐藏，字段没建页面也不散架

---

## FAQ（问答列表 + 详情页，2026-09-05 新增）

> 背景：FAQ 是独立 CPT（slug aq），无分类法。列表页 = `archive-faq.php`（/faq/ 归档页），详情页 = `single-faq.php`（版式与文章详情页 single-post.php 完全一致）。
> 注意：**CPT 归档页不是页面**，读不到 site_banner（那字段只挂在页面上），所以 FAQ banner 与产品列表页 page_banner_* 同模式，用独立字段放公共页 62。

### A. 创建 FAQ 自定义文章类型（ACF 后台 → 自定义文章类型 → 新建）

| 项目 | 值 | 说明 |
|---|---|---|
| 文章类型名称 | `faq` | 必须小写，决定 URL /faq/ |
| 标签 | FAQ | 后台显示名 |
| 复数标签 | FAQs | |
| 公开 | 是 | |
| **归档** | **是** | **关键：开启后才有 /faq/ 列表页** |
| 重写 slug | faq | URL：列表 /faq/，详情 /faq/问题-slug/ |
| 支持 | 标题、编辑器、摘要 | 标题=问题，正文=答案，摘要=列表卡片描述 |
| 分类法 | 不选 | 不用分类法 |
| 菜单图标 | dashicons-editor-help（问号） | 可选 |

### B. FAQ 字段（挂在公共字段页 62，与 page_banner_img / news_banner_img 同模式）

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `faq_banner_img` | Image（返回 URL） | 选一张 FAQ 横幅图 | FAQ 列表页 + 详情页 banner 背景图（栏目统一 banner），独立于 product / news，后台可单独换 |
| `faq_banner_title` | Text | `FAQ` | banner 大字标题 |
| `faq_related_title` | Text | `Related Questions` | FAQ 详情页侧栏标题。**不能复用 news_related_title**（那是文章详情页的，会重名冲突） |

### C. FAQ 内容录入规则

- 标题 = 问题（如 What is the typical machining allowance for forged gear ring blanks?）
- 正文 = 答案（H2 小节结构，可插图）
- 摘要 = 列表卡片一句话描述（不填则列表页自动截取正文开头）
- 排序 = 发布时间倒序（最新在前，WP 默认）
- 每页 = 8 条（`jc_faq_archive_per_page` 钩子固定，2 列 × 4 行）

### 模板对应关系

| 模板 | 对应 | 说明 |
|---|---|---|
| `archive-faq.php` | 静态版 FAQ.html | 列表页（全宽无侧栏：banner + 网格 + 分页） |
| `single-faq.php` | 文章详情页模板 | 详情页 = single-post.php 复制，Related 改查 faq 类型 |

### 非字段项（代码已实现）

- 列表卡片：无图（Q 角标 + 标题 1 行截断 + 摘要 2 行截断 + Read More），CSS = assets/css/faq-list.css
- FAQ 详情页 CSS：与文章详情页共用 assets/css/news-single.css（版式相同，不新建文件）
- 菜单：jc_default_menu() 兜底菜单里 FAQ 已指向归档链接 get_post_type_archive_link('faq')；后台自定义菜单（外观→菜单）请把 FAQ 项指向 /faq/（自定义链接或 CPT 归档）

---

## 十、关于我们页（page-about_us.php，2026-09-05 新增）

**页面对应模板**：`page-about_us.php`（WordPress 按 slug `about_us` 自动匹配，后台无需选模板）。CSS/JS 已复制为 `assets/css/about.css` + `assets/js/about.js`，由 functions.php 在 `is_page('about_us')` 时自动加载。

**静态版**：`E:\jingxiang\site\about.html`（含页面结构注释，转 WP 时对照）。

### A. Banner（你已建字段，无需新建）

| 字段名称 | 类型 | 说明 |
|---|---|---|
| `site_banner` | Image | **你已建好**（位置规则：页面≠首页≠公共页 OR 分类法=全部）。About 页面后台直接填背景图。**标题 = 页面标题（About Us），面包屑 HOME > ABOUT US 自动生成**，都不需要字段 |

### B. 字段组（位置规则：**页面 == About Us**，slug 用 about_us）

**① 公司介绍 Intro**

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `about_intro_title` | Text | `About Us` | 左列顶部小标题 |
| `about_intro_desc` | Textarea | 见附录① | 公司介绍正文（**一个字段，不含标题**；段落间空行自动分段；容器固定高度，超出下滑） |
| `about_intro_img` | Image（URL） | `about-intro.png` | 右侧图片（16:9 容器） |
| `about_intro_video_embed` | Textarea | `(空)` | 右侧视频嵌入代码（YouTube iframe 等，**有值优先于图片**） |
| `about_intro_video_file` | File | `(空)` | 右侧视频 mp4（embed 空时用；容器 16:9 加载前保持高度） |

> 公司名 = 复用公共字段页 62 的 `about_company`（首页同款），无需新建。

**② CNC Machining**

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `cnc_title` | Text | `CNC Machining` | 右列标题 |
| `cnc_desc` | Textarea | 见附录② | 右列段落（材料利用率 50% 那段；固定高度，超出下滑） |
| `cnc_imgs` | Group | `(空)` | 组内 `img_1`~`img_6`：左列轮播图（3 秒自动 + 左右箭头） |
| `cnc_quote_text` | Text | `Get Free Quote` | 按钮文字（点击弹询盘窗，**与 header Contact Us 同一弹窗**，表单逻辑见 header.php） |

**③ Certificate**

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `about_cert_title` | Text | `Certificate` | 区块标题 |
| `about_cert_desc` | Textarea | 见附录③ | 区块描述文案 |
| ~~证书图~~ | — | — | **不需要建**：证书轮播 = certificate CPT（与首页同一套） |

**④ Testing equipment**

| 字段名称 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `test_title` | Text | `Testing equipment` | 区块标题 |
| `test_imgs` | Group | `(空)` | 组内 `img_1`~`img_6`（设备图）+ `name_1`~`name_6`（设备名）**同一组**：左大图 + 右缩略图共用图，名称同时显示（默认值见附录④，**空则隐藏名称**） |

**⑤ Our service**：无字段，**复用首页已建的 `svc_1`~`svc_5` + `svc_cta_*`**（代码从首页 ID 读取，About 页零重复录入）。

### C. 非字段项（代码已实现）

- Get Free Quote 按钮点击打开 contactModal（与 header Contact Us 共用，表单输出方式同 header.php）
- 设备轮播交互：点击缩略图切换大图并顺延循环，3 秒自动，hover 暂停
- 不散架原则：字段没建/没填 → 回退静态默认值（about.html 里的原文/图片）

### 附录：字段默认值文案

**① about_intro_desc**

> Established in 2015, Liaocheng Jiucheng Auto Parts Co., Ltd. is a high-tech enterprise integrating R&D, production, and sales. The company specializes in the manufacturing and processing of precision-forged automotive synchronizer blanks, high-precision gear blanks, and large-specification chain sleeves. It has a registered capital of RMB 1 million, fixed assets of RMB 10 million, a total building area of 8,000 m² (including 6,000 m² of workshop space), and dedicated workshops for precision forging and machining. The workforce consists of 45 employees, including one senior engineer, three mechanical engineers, one quality engineer, eight quality inspectors, and one electrical engineer. The company focuses on producing high-end automotive components and precision forgings, with a product portfolio that includes automotive synchronizer sleeves, synchronizer hubs, differential housings, and various other gear forgings.

**② cnc_desc**

> Its precision-forged products—such as automotive synchronizer sleeves—achieve the goals of eliminating or minimizing the need for turning, conserving raw materials and energy, and reducing production costs; material utilization rates have increased by 50% compared to traditional processes, overall production costs have dropped by 20%, and electricity savings exceed 30%. The "Jiucheng" team is committed to scaling new heights through continuous self-breakthroughs, writing new chapters of success, and maintaining strong momentum for growth.

**③ about_cert_desc**

> Quality is the foundation of the enterprise's survival, while innovation paves the path for its development. Mindset determines the way forward; the company seeks sincere, mutually beneficial cooperation with you to deliver superior product performance and create the industrial value you envision, working together to achieve excellence and continued success.

**④ 设备名（test_imgs 组内 name_1~name_6）**

1. Universal Materials Testing Machine
2. UV Notched Tensile Tester
3. Brinell hardness tester
4. Impact testing machine
5. Metallographic Analysis System
6. Metallographic Polishing Machine – Notch Tester

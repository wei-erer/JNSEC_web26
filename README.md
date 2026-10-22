# JNSEC_web26 · 竹隐

江南大学信息安全俱乐部 Web 方向 2026 届学习札记，对全体 26 届开放。

讲台上的东西，讲完就散了。这里把它整理成能一段一段读完的小节，
补上没来得及展开的部分，配上能自己跑起来的靶场 —— 让"当时没听懂"这件事，
有个可以慢慢补的地方。

---

## 目录结构

```
JNSEC_web26/
├─ index.html                  山门（首页目录）
├─ roadmap.html                路线（学习路径、练手资源、工具一览）
├─ weeks/
│  └─ week01.html              其一 · 前端基础以及基本工具的使用
├─ lab/
│  └─ zhulin_lab.py            配套靶场（纯 Python 标准库，四关）
├─ assets/
│  ├─ css/
│  │  ├─ zhulin.css            全站样式（纸、墨、竹、印）
│  │  └─ tools.css             交互工具样式
│  └─ js/
│     ├─ site-config.js        ★ 全站配置（评论模式、站点名）
│     ├─ site.js               回顶、目录高亮、勾选进度、代码复制
│     ├─ widgets.js            URL 解剖台、HTTP 报文工坊
│     └─ comments.js           评论区（本机留痕 / giscus 双模）
└─ 例会文档/                    原始文档，仅本机保留（已写进 .gitignore，不上传）
```

没有构建步骤，没有依赖。改完直接刷新。

---

## 本地预览

在项目根目录开一个静态服务器就行（**不要直接双击 HTML**，
`file://` 下部分浏览器的脚本和存储会受限）：

```bash
# 有 Python 的话
python -m http.server 8000

# 或者有 Node 的话
npx serve .
```

然后浏览器打开 <http://127.0.0.1:8000>。

### 跑靶场

另开一个窗口：

```bash
python lab/zhulin_lab.py
```

打开 <http://127.0.0.1:8081>，四道门在那里等你。

> 靶场是故意写漏的，只用于练习。别拿它的写法去写线上服务。

---

## 加新的一讲

1. 复制 `weeks/week01.html`，改名成 `week02.html`。
2. 改里面这几处：
   - `<title>` 和 `<h1>`、`.juan-deck`、`.juan-meta`
   - 正文 `<div class="prose">` 里的内容
   - 侧栏 `.side-toc` 里的小目录（`href` 要和正文 `h2` 的 `id` 对上）
3. 在 `index.html` 的"已开卷"列表里加一条 `.entry`，指向新文件。
4. 顶部导航 `.site-nav` 里也补一条。

小节编号是自动的（`h2` 上的 CSS 计数器），不用手写"一、二、三"。
不想编号的那一节，给 `h2` 加 `class="nonum"`。

---

## 开评论区

默认是 **本机留痕** 模式：留言存在浏览器 `localStorage` 里，只有自己能看见。
方便本地预览和试手，但同门之间不通。

要变成所有人共享的茶寮，用 [giscus](https://giscus.app)（背后是 GitHub Discussions，
免费、不用服务器）：

1. 仓库设成 **public**，并且装一次 [giscus app](https://github.com/apps/giscus)。
2. 仓库 **Settings → General → Features** 里勾上 **Discussions**，
   再建一个分类（比如"例会评论"）。
3. 打开 <https://giscus.app>，填仓库名、选分类，
   页面下方会生成一段 `<script>`，里面有 `data-repo`、`data-repo-id`、
   `data-category`、`data-category-id` 四个值。
4. 把这四个值和 `mode: 'giscus'` 填进 `assets/js/site-config.js`。

```js
comments: {
  mode: 'giscus',
  giscus: {
    repo: 'yourname/JNSEC_web26',
    repoId: 'R_kgDOxxxxxxx',
    category: '例会评论',
    categoryId: 'DIC_kwDOxxxxxxxx',
    ...
  }
}
```

每一页用 `pathname` 做映射，所以各周的评论各自独立。

---

## 部署到 GitHub Pages

这个目录**已经 git 初始化并提交好了**，只剩推上去。

### 一、推到 GitHub

#### 法子 A：用 GitHub Desktop（最省事）

1. 打开 GitHub Desktop → **File → Add local repository**，
   选中本文件夹 `JNSEC_web26`。
2. 右侧会出现 **Publish repository**，点它。
3. 名字保持 `JNSEC_web26`，**取消勾选 Keep this code private**（Pages 免费版需要 public 仓库）。
4. 点 **Publish repository**，等它跑完。

#### 法子 B：命令行

先在 GitHub 上建一个**空仓库**（不要勾 README、.gitignore、license），
名字 `JNSEC_web26`。然后：

```bash
git remote add origin https://github.com/你的用户名/JNSEC_web26.git
git push -u origin main
```

第一次推会要求登录。密码那一栏**不能填账号密码**，
要填 Personal Access Token（GitHub → Settings → Developer settings →
Personal access tokens → 生成一个带 `repo` 权限的）。

### 二、开 Pages

仓库 → **Settings → Pages** →
- Source 选 **Deploy from a branch**
- Branch 选 **main**，目录选 **/ (root)**
- 保存

等一两分钟，站点就在：

```
https://你的用户名.github.io/JNSEC_web26/
```

> 推送之后如果样式没生效，八成是浏览器缓存。`Ctrl + F5` 强刷一次。
> 仓库里已经放了空的 `.nojekyll`，免得 GitHub 用 Jekyll 处理这些文件。

---

## 以后改东西，方不方便？

方便。**Pages 只在第一次配一遍，之后每次改完推上去，一两分钟自己就更新了**，
不用重新开 Pages，也不用管什么构建流程。

### 用 GitHub Desktop

1. 用 VS Code / 记事本改文件，保存。
2. GitHub Desktop 会自动发现改动，左边列出改了哪些文件。
3. 左下角填一句说明（比如"补了 Burp 的证书部分"），点 **Commit to main**。
4. 右上角点 **Push origin**。
5. 等一分钟，线上刷新。**改过样式就按 `Ctrl + F5`**，不然看到的是缓存里的旧 CSS。

### 用命令行

```bash
git add -A
git commit -m "补了 Burp 的证书部分"
git push
```

就这样。GitHub Pages 会自己重新部署，历史记录也都在，改错了能回退。

### 三、注意路径

全站都用**相对路径**（`../assets/...`、`week01.html`），
所以放在 `用户名.github.io/JNSEC_web26/` 这种子路径下也能正常工作。
以后要是搬到根仓库（`用户名.github.io`），也不用改任何东西。

---

## 关于风格

色板和字体都收在 `assets/css/zhulin.css` 顶部的 `:root` 里。
想换整体色调，改那十几个变量就够了，不用翻具体规则：

```css
--paper: #f5f0e4;   /* 纸 */
--ink:   #1e2a23;   /* 墨 */
--bamboo:#4f6f52;   /* 竹 */
--cinnabar:#a83a2c; /* 朱砂 */
```

字体走系统栈（楷体 / 宋体 / 雅黑），不加载网络字体，
所以离线也能正常显示，不会出现字体闪烁。

---

## 一句提醒

站里所有的实操、靶场、payload 演示，都只在自己机器或明确授权的靶场里做。
俱乐部不教人干没授权的事。

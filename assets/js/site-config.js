/* ============================================================
   JNSEC · 竹隐 —— 全站配置
   改这个文件就够了，不用去翻 HTML。
   ============================================================ */
window.JNSEC_CONFIG = {

  /* ---- 站名，页眉页脚都读这里（改完刷新即可） ---- */
  site: {
    title: '竹隐',
    club: '江南大学信息安全俱乐部',
    track: 'Web 方向 · 2026 届'
  },

  /* ----------------------------------------------------------
     评论区
     mode: 'local'   —— 默认。留言只存自己浏览器，方便本地预览、试手。
     mode: 'giscus'  —— 上线后改成这个，所有人共享同一片茶寮。

     切到 giscus 的步骤：
       1. 仓库必须是 public，并且装了 giscus app
          （进 https://github.com/apps/giscus 点 Install）
       2. 仓库 Settings → General → Features 勾上 Discussions
       3. 打开 https://giscus.app ，填仓库名，选 Discussions 分类
       4. 页面下方会生成一段 <script>，里面四个 data-* 值抄到下面
     ---------------------------------------------------------- */
  comments: {
    mode: 'local',

    giscus: {
      repo: '',            // 形如 "yourname/JNSEC_web26"
      repoId: '',          // data-repo-id
      category: 'General', // 分类名，建议新建一个 "例会评论"
      categoryId: '',      // data-category-id
      mapping: 'pathname', // 每一页各自的评论串
      strict: '0',
      lang: 'zh-CN',
      theme: 'light'       // 想统一到竹林色调，可以换成自家 css 的网址
    }
  }
};

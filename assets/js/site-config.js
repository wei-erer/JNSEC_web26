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
     评论区（茶寮）

     mode: 'giscus'  —— 接的是 GitHub Discussions，所有人共享。
     mode: 'local'   —— 退回到本机留痕：留言只存自己浏览器，
                        换台机器就没了。本地调试、断网时用这个。

     换成 local 只需把下面那行 mode 改掉，别的不用动。
     ---------------------------------------------------------- */
  comments: {
    mode: 'giscus',

    giscus: {
      repo: 'wei-erer/JNSEC_web26',
      repoId: 'R_kgDOVTyCpw',
      category: 'Announcements',
      categoryId: 'DIC_kwDOVTyCp84DJWrb',
      mapping: 'pathname',   // 每一页各自的讨论串
      strict: '0',
      lang: 'zh-CN',
      theme: 'light',        // 想换深色或自定义主题，改这里
      inputPosition: 'top'   // 评论区里输入框的位置：top / bottom
    }
  }
};

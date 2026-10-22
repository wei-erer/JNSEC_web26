/* ============================================================
   JNSEC · 竹隐 —— 站点通用脚本
   只做四件小事：回顶、目录高亮、勾选进度、代码复制。
   不需要构建工具，浏览器直接跑。
   ============================================================ */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- 1. 回到卷首 ---------- */
  var toTop = document.querySelector('.to-top');
  if (toTop) {
    var onScrollTop = function () {
      if (window.scrollY > 640) toTop.classList.add('show');
      else toTop.classList.remove('show');
    };
    window.addEventListener('scroll', onScrollTop, { passive: true });
    onScrollTop();
    toTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }

  /* ---------- 2. 侧栏目录跟着滚动点亮 ---------- */
  var tocLinks = Array.prototype.slice.call(document.querySelectorAll('.side-toc a[href^="#"]'));
  if (tocLinks.length) {
    var sections = tocLinks.map(function (a) {
      return document.getElementById(a.getAttribute('href').slice(1));
    });
    var marks = [];

    // 每一节的位置，滚动时重算（图片/字体加载完会变）
    var measure = function () {
      marks = sections.map(function (el) {
        return el ? el.getBoundingClientRect().top + window.scrollY : Infinity;
      });
    };
    measure();
    window.addEventListener('resize', measure);
    window.addEventListener('load', measure);

    var ticking = false;
    var lightUp = function () {
      ticking = false;
      var y = window.scrollY + 140;
      var current = -1;
      for (var i = 0; i < marks.length; i++) {
        if (marks[i] <= y) current = i;
      }
      // 还没滚到第一节时，先点亮第一节
      if (current < 0) current = 0;
      // 滚到底时，最后一项必须亮
      if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 4) {
        current = marks.length - 1;
      }
      tocLinks.forEach(function (a, i) {
        a.classList.toggle('active', i === current);
      });
    };
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(lightUp); }
    }, { passive: true });
    lightUp();
  }

  /* ---------- 3. 渐入（只做一次，别搞成滚动特效） ---------- */
  if (!reduceMotion && 'IntersectionObserver' in window) {
    var risers = document.querySelectorAll('.rise');
    if (risers.length) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add('in');
            io.unobserve(e.target);
          }
        });
      }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });
      risers.forEach(function (el) { io.observe(el); });
    }
  } else {
    document.querySelectorAll('.rise').forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- 4. 勾选进度（存本机，换页面不丢） ---------- */
  var boxes = Array.prototype.slice.call(document.querySelectorAll('.checkable input[type="checkbox"]'));
  if (boxes.length) {
    var KEY = 'jnsec.progress:' + location.pathname.replace(/\/+$/, '/');

    var readState = function () {
      try { return JSON.parse(localStorage.getItem(KEY) || '[]'); }
      catch (e) { return []; }
    };
    var writeState = function (arr) {
      try { localStorage.setItem(KEY, JSON.stringify(arr)); } catch (e) { /* 隐身模式就算了 */ }
    };

    var saved = readState();
    boxes.forEach(function (box, i) {
      box.checked = saved.indexOf(box.dataset.k || String(i)) > -1;
    });

    var ring = document.querySelector('[data-progress-ring]');
    var ringFg = ring ? ring.querySelector('.ring-fg') : null;
    var ringNum = ring ? ring.querySelector('[data-progress-num]') : null;
    var CIRC = 2 * Math.PI * 15.5;

    var paint = function () {
      var done = boxes.filter(function (b) { return b.checked; }).length;
      if (ringFg) {
        ringFg.style.strokeDasharray = CIRC.toFixed(2);
        ringFg.style.strokeDashoffset = (CIRC * (1 - done / boxes.length)).toFixed(2);
      }
      if (ringNum) ringNum.textContent = done + '/' + boxes.length;
    };

    boxes.forEach(function (box, i) {
      box.addEventListener('change', function () {
        var k = box.dataset.k || String(i);
        var cur = readState().filter(function (x) { return x !== k; });
        if (box.checked) cur.push(k);
        writeState(cur);
        paint();
      });
    });
    paint();
  }

  /* ---------- 5. 代码块一键抄走 ---------- */
  var copyText = function (text) {
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text);
    }
    return new Promise(function (resolve, reject) {
      var ta = document.createElement('textarea');
      ta.value = text;
      ta.setAttribute('readonly', '');
      ta.style.cssText = 'position:fixed;top:-9999px;opacity:0';
      document.body.appendChild(ta);
      ta.select();
      try {
        document.execCommand('copy') ? resolve() : reject(new Error('copy failed'));
      } catch (err) { reject(err); }
      finally { document.body.removeChild(ta); }
    });
  };

  Array.prototype.slice.call(document.querySelectorAll('.prose pre, .cmd, .labfile pre')).forEach(function (block) {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'copy-btn';
    btn.textContent = '抄';
    btn.setAttribute('aria-label', '复制这段内容');
    btn.addEventListener('click', function () {
      var codeEl = block.querySelector('code') || block;
      copyText(codeEl.textContent.replace(/\s+$/, '')).then(function () {
        btn.textContent = '已抄';
        btn.classList.add('done');
        setTimeout(function () {
          btn.textContent = '抄';
          btn.classList.remove('done');
        }, 1500);
      }).catch(function () {
        btn.textContent = '失败';
        setTimeout(function () { btn.textContent = '抄'; }, 1500);
      });
    });
    block.appendChild(btn);
  });

  /* ---------- 6. 窄屏时，顶栏当前菜单项滚进视野 ---------- */
  var current = document.querySelector('.site-nav a[aria-current="page"]');
  if (current) {
    var nav = current.parentElement;
    if (nav.scrollWidth > nav.clientWidth) {
      nav.scrollLeft = Math.max(0, current.offsetLeft - nav.clientWidth / 2);
    }
  }
})();

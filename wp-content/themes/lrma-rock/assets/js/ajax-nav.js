(function () {
  'use strict';

  /* Bail out if browser is too old or main wrapper missing */
  var mainEl = document.getElementById('lrma-main');
  if (!mainEl || !window.fetch || !window.history || !window.DOMParser) return;

  var siteOrigin = window.location.origin;
  var currentController = null;

  /* ── Helpers ─────────────────────────────────────────────────────── */

  function isInternalUrl(href) {
    try {
      return new URL(href).origin === siteOrigin;
    } catch (e) { return false; }
  }

  function shouldIntercept(a) {
    if (!a || a.tagName !== 'A') return false;
    var href = a.getAttribute('href') || '';
    if (!href || href.charAt(0) === '#') return false;
    if (a.target === '_blank') return false;
    if (a.hasAttribute('download')) return false;
    if (/^(mailto:|tel:|javascript:)/i.test(href)) return false;
    if (!isInternalUrl(a.href)) return false;
    var path = new URL(a.href).pathname;
    if (/^\/(wp-admin|wp-login|wp-json|feed)/.test(path)) return false;
    if (/\.(pdf|zip|doc[x]?|xls[x]?|png|jpe?g|gif|svg|mp3|mp4|webp|rar|7z)$/i.test(path)) return false;
    return true;
  }

  /* Re-run <script> tags inside swapped content — innerHTML does not execute them */
  function runScripts(container) {
    container.querySelectorAll('script').forEach(function (old) {
      var s = document.createElement('script');
      Array.from(old.attributes).forEach(function (attr) {
        s.setAttribute(attr.name, attr.value);
      });
      s.textContent = old.textContent;
      old.parentNode.replaceChild(s, old);
    });
  }

  /* Re-observe .reveal elements for the scroll-reveal IntersectionObserver */
  function reinitScrollReveal() {
    if (!('IntersectionObserver' in window)) {
      mainEl.querySelectorAll('.reveal').forEach(function (el) {
        el.classList.add('revealed');
      });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('revealed');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.08 });
    mainEl.querySelectorAll('.reveal:not(.revealed)').forEach(function (el) {
      io.observe(el);
    });
  }

  /* ── Core navigation ─────────────────────────────────────────────── */

  function navigate(url) {
    /* Abort any in-flight request */
    if (currentController) {
      try { currentController.abort(); } catch (e) {}
    }
    currentController = window.AbortController ? new AbortController() : null;

    fetch(url, {
      signal: currentController ? currentController.signal : undefined
    })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.text();
      })
      .then(function (html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var newMain = doc.getElementById('lrma-main');

        /* If the response has no #lrma-main, fall back to a real navigation */
        if (!newMain) { window.location.href = url; return; }

        document.title = doc.title;
        mainEl.innerHTML = newMain.innerHTML;

        /* Re-execute inline scripts (hero slider, etc.) */
        runScripts(mainEl);

        window.scrollTo(0, 0);
        reinitScrollReveal();
      })
      .catch(function (err) {
        if (err && err.name === 'AbortError') return;
        /* On any fetch error fall back to normal navigation */
        window.location.href = url;
      });
  }

  /* ── Click intercept ─────────────────────────────────────────────── */

  document.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!shouldIntercept(a)) return;

    /* Same page — just prevent reload */
    if (a.href === window.location.href || a.href === window.location.href + '#') {
      e.preventDefault();
      return;
    }

    e.preventDefault();
    history.pushState(null, '', a.href);
    navigate(a.href);
  });

  /* ── Back / Forward ──────────────────────────────────────────────── */

  window.addEventListener('popstate', function () {
    navigate(window.location.href);
  });

})();

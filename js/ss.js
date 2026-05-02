/* ============================================
   SNAPSERVE SUPPLY — ss.js
   ============================================ */

document.addEventListener("DOMContentLoaded", function () {

  /* ============================================
     SHOP SORT DROPDOWN
     ============================================ */
  const dropdown = document.querySelector(".ss-sort-dropdown");
  if (dropdown && document.body.classList.contains('post-type-archive-product')) {
    const selected = dropdown.querySelector(".ss-sort-selected");
    const options  = dropdown.querySelectorAll(".ss-sort-options li");
    const select   = document.querySelector(".orderby");

    if (selected && select) {
      selected.addEventListener("click", () => {
        dropdown.classList.toggle("active");
      });

      options.forEach(option => {
        option.addEventListener("click", () => {
          const value = option.getAttribute("data-value");
          selected.innerHTML = option.innerText + '<span class="arrow">⌄</span>';
          select.value = value;
          const form = select.closest("form");
          if (form) form.submit();
        });
      });

      document.addEventListener("click", (e) => {
        if (!dropdown.contains(e.target)) {
          dropdown.classList.remove("active");
        }
      });
    }
  }


  /* ============================================
     AJAX SEARCH (header search bar)
     ============================================ */
  const searchForms = document.querySelectorAll(".ss-ajax-search");
  let searchTimeout = null;

  searchForms.forEach(form => {
    const input            = form.querySelector('input[name="s"]');
    const resultsContainer = form.querySelector(".ss-search-results");
    if (!input || !resultsContainer) return;

    input.addEventListener("input", function () {
      const query = this.value.trim();
      clearTimeout(searchTimeout);

      if (query.length < 3) {
        resultsContainer.classList.remove("active");
        resultsContainer.innerHTML = "";
        return;
      }

      searchTimeout = setTimeout(() => {
        performSearch(query, resultsContainer);
      }, 400);
    });

    document.addEventListener("click", (e) => {
      if (!form.contains(e.target)) {
        resultsContainer.classList.remove("active");
      }
    });

    input.addEventListener("focus", function () {
      if (this.value.trim().length >= 3 && resultsContainer.innerHTML !== "") {
        resultsContainer.classList.add("active");
      }
    });
  });

  function performSearch(query, container) {
    if (typeof ss_ajax === 'undefined') return;

    container.classList.add("loading", "active");

    const formData = new FormData();
    formData.append("action", "ss_ajax_search");
    formData.append("query",  query);
    formData.append("nonce",  ss_ajax.nonce);

    fetch(ss_ajax.url, { method: "POST", body: formData })
      .then(r => r.json())
      .then(data => {
        container.classList.remove("loading");
        container.innerHTML = data.success
          ? data.data.results
          : '<div class="ss-no-results">Please type more.</div>';
      })
      .catch(() => {
        container.classList.remove("loading");
        container.innerHTML = '<div class="ss-no-results">Please type more.</div>';
      });
  }


  /* ============================================
     MOBILE SEARCH OVERLAY
     ============================================ */
  const searchToggle  = document.getElementById("searchToggle");
  const searchOverlay = document.getElementById("searchOverlay");
  const searchClose   = document.getElementById("searchClose");

  if (searchToggle && searchOverlay) {
    searchToggle.addEventListener("click", () => {
      searchOverlay.classList.add("active");
      document.body.style.overflow = "hidden";
      const inp = searchOverlay.querySelector('input[name="s"]');
      if (inp) setTimeout(() => inp.focus(), 100);
    });
  }

  if (searchClose && searchOverlay) {
    searchClose.addEventListener("click", () => {
      searchOverlay.classList.remove("active");
      document.body.style.overflow = "";
    });
  }

  if (searchOverlay) {
    searchOverlay.addEventListener("click", (e) => {
      if (e.target === searchOverlay || e.target.classList.contains('container')) {
        searchOverlay.classList.remove("active");
        document.body.style.overflow = "";
      }
    });
  }


  /* ============================================
     HERO SEARCH — LIVE SUGGESTIONS
     All PHP values come from ss_ajax (wp_localize_script):
       ss_ajax.url      → admin-ajax.php URL
       ss_ajax.nonce    → nonce
       ss_ajax.shop_url → shop page URL  ← add this in functions.php
     ============================================ */
  (function () {
    'use strict';

    if (typeof ss_ajax === 'undefined') return;

    var input        = document.getElementById('ss-hero-input');
    var box          = document.getElementById('ss-suggestions-box');
    var spinner      = document.getElementById('ss-search-spinner');
    var clearBtn     = document.getElementById('ss-search-clear');
    var form         = document.getElementById('ss-hero-search-form');
    var ajaxUrl      = ss_ajax.url;
    var nonce        = ss_ajax.nonce;
    // shop_url is passed via ss_ajax.shop_url — see functions.php note below
    var shopUrl      = (ss_ajax.shop_url !== undefined) ? ss_ajax.shop_url : window.location.origin;

    var debounceTimer  = null;
    var currentQuery   = '';
    var focusedIndex   = -1;
    var currentItems   = [];
    var activeRequest  = null;

    if (!input || !box || !spinner || !clearBtn || !form) return;

    // ---- INPUT ----
    input.addEventListener('input', function () {
      var q = this.value.trim();

      clearBtn.style.display = q.length > 0 ? 'flex' : 'none';

      if (q.length < 2) { closeBox(); return; }
      if (q === currentQuery) return;
      currentQuery = q;

      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () {
        fetchSuggestions(q);
      }, 280);
    });

    // ---- KEYBOARD ----
    input.addEventListener('keydown', function (e) {
      if (!box.classList.contains('open')) return;

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        focusedIndex = Math.min(focusedIndex + 1, currentItems.length - 1);
        updateFocus();

      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        focusedIndex = Math.max(focusedIndex - 1, -1);
        updateFocus();

      } else if (e.key === 'Enter') {
        if (focusedIndex >= 0 && currentItems[focusedIndex]) {
          e.preventDefault();
          window.location.href = currentItems[focusedIndex].url;
        }

      } else if (e.key === 'Escape') {
        closeBox();
        input.blur();
      }
    });

    // ---- CLEAR ----
    clearBtn.addEventListener('click', function () {
      input.value            = '';
      clearBtn.style.display = 'none';
      closeBox();
      input.focus();
    });

    // ---- CLICK OUTSIDE ----
    document.addEventListener('click', function (e) {
      if (!form.contains(e.target)) closeBox();
    });

    // ---- FOCUS — reopen if has cached results ----
    input.addEventListener('focus', function () {
      if (input.value.trim().length >= 2 && currentItems.length > 0) openBox();
    });


    // ================================================================
    // FETCH
    // ================================================================
    function fetchSuggestions(q) {
      if (activeRequest) { activeRequest.abort(); }

      showLoading();

      var fd = new FormData();
      fd.append('action', 'ss_hero_suggest');
      fd.append('nonce',  nonce);
      fd.append('query',  q);

      var xhr      = new XMLHttpRequest();
      activeRequest = xhr;

      xhr.open('POST', ajaxUrl, true);
      xhr.onload = function () {
        spinner.classList.remove('active');
        activeRequest = null;
        if (xhr.status !== 200) return;
        try {
          var res = JSON.parse(xhr.responseText);
          if (res.success) renderSuggestions(res.data, q);
        } catch (err) {
          // malformed JSON — silently ignore
        }
      };
      xhr.onerror = function () {
        spinner.classList.remove('active');
        activeRequest = null;
      };
      xhr.send(fd);
    }


    // ================================================================
    // RENDER
    // ================================================================
    function renderSuggestions(items, q) {
      box.innerHTML = '';
      currentItems  = items || [];
      focusedIndex  = -1;

      if (!currentItems.length) {
        box.innerHTML =
          '<div class="ss-suggest-empty">' +
            '<strong>No parts found</strong>' +
            '<span>Try a different model number, SKU, or part name</span>' +
          '</div>';
        openBox();
        return;
      }

      var fragment = document.createDocumentFragment();

      currentItems.forEach(function (item, idx) {
        var a         = document.createElement('a');
        a.className   = 'ss-suggest-item';
        a.href        = item.url;
        a.setAttribute('role',       'option');
        a.setAttribute('data-index', idx);

        var metaParts = [];
        if (item.sku)      metaParts.push('<span class="ss-suggest-sku">SKU: ' + escapeHtml(item.sku) + '</span>');
        if (item.category) metaParts.push(escapeHtml(item.category));

        a.innerHTML =
          '<img class="ss-suggest-thumb" src="' + escapeHtml(item.thumb) + '" alt="' + escapeHtml(item.title) + '" loading="lazy">' +
          '<div class="ss-suggest-info">' +
            '<div class="ss-suggest-title">' + highlightMatch(escapeHtml(item.title), q) + '</div>' +
            '<div class="ss-suggest-meta">'  + metaParts.join(' · ') + '</div>' +
          '</div>' +
          '<div class="ss-suggest-price">' + item.price + '</div>';

        a.addEventListener('mouseenter', function () {
          focusedIndex = idx;
          updateFocus();
        });

        fragment.appendChild(a);
      });

      // "View all" row
      var viewAll      = document.createElement('a');
      viewAll.className = 'ss-suggest-view-all';
      viewAll.href      = shopUrl + '?s=' + encodeURIComponent(q) + '&post_type=product';
      viewAll.innerHTML =
        '<i class="bi bi-search"></i> ' +
        'View all results for <strong>&ldquo;' + escapeHtml(q) + '&rdquo;</strong>' +
        '<i class="bi bi-arrow-right"></i>';

      fragment.appendChild(viewAll);
      box.appendChild(fragment);
      openBox();
    }


    // ================================================================
    // HELPERS
    // ================================================================
    function showLoading() {
      spinner.classList.add('active');
      box.innerHTML = '<div class="ss-suggest-loading"><span>Searching parts...</span></div>';
      openBox();
    }

    function openBox() {
      box.classList.add('open');
      input.setAttribute('aria-expanded', 'true');
    }

    function closeBox() {
      box.classList.remove('open');
      input.setAttribute('aria-expanded', 'false');
      focusedIndex = -1;
    }

    function updateFocus() {
      box.querySelectorAll('.ss-suggest-item').forEach(function (el, i) {
        el.classList.toggle('focused', i === focusedIndex);
      });
    }

    function highlightMatch(text, q) {
      if (!q) return text;
      try {
        var escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp('(' + escaped + ')', 'gi'), '<mark>$1</mark>');
      } catch (e) {
        return text;
      }
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#039;');
    }

  })();

}); // end DOMContentLoaded
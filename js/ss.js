document.addEventListener("DOMContentLoaded", function () {

  /* ============================================
     SHOP SORT DROPDOWN
     ============================================ */
  const dropdown = document.querySelector(".ss-sort-dropdown");
  if (dropdown && document.body.classList.contains('post-type-archive-product')) {
    const selected = document.querySelector(".ss-sort-selected");
    const options = document.querySelectorAll(".ss-sort-options li");
    const select = document.querySelector(".orderby");

    // toggle dropdown
    selected.addEventListener("click", () => {
      dropdown.classList.toggle("active");
    });

    // option click
    options.forEach(option => {
      option.addEventListener("click", () => {
        const value = option.getAttribute("data-value");
        const text = option.innerText;
        selected.innerHTML = text + '<span class="arrow">⌄</span>';
        select.value = value;
        // submit form
        select.closest("form").submit();
      });
    });

    // close on outside click
    document.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove("active");
      }
    });
  }


  /* ============================================
     AJAX SEARCH
     ============================================ */
  const searchForms = document.querySelectorAll(".ss-ajax-search");
  let searchTimeout = null;

  searchForms.forEach(form => {
    const input = form.querySelector('input[name="s"]');
    const resultsContainer = form.querySelector(".ss-search-results");

    if (!input || !resultsContainer) return;

    input.addEventListener("input", function() {
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

    // Close results when clicking outside
    document.addEventListener("click", (e) => {
      if (!form.contains(e.target)) {
        resultsContainer.classList.remove("active");
      }
    });

    // Re-open if input is focused and has value
    input.addEventListener("focus", function() {
      if (this.value.trim().length >= 3 && resultsContainer.innerHTML !== "") {
        resultsContainer.classList.add("active");
      }
    });
  });

  function performSearch(query, container) {
    container.classList.add("loading");
    container.classList.add("active");

    const formData = new FormData();
    formData.append("action", "ss_ajax_search");
    formData.append("query", query);
    formData.append("nonce", ss_ajax.nonce);

    fetch(ss_ajax.url, {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      container.classList.remove("loading");
      if (data.success) {
        container.innerHTML = data.data.results;
      } else {
        container.innerHTML = '<div class="ss-no-results">Error performing search.</div>';
      }
    })
    .catch(error => {
      container.classList.remove("loading");
      container.innerHTML = '<div class="ss-no-results">Error performing search.</div>';
    });
  }

  // Mobile search toggle logic
  const searchToggle = document.getElementById("searchToggle");
  const searchOverlay = document.getElementById("searchOverlay");
  const searchClose = document.getElementById("searchClose");

  if (searchToggle && searchOverlay) {
    searchToggle.addEventListener("click", () => {
      searchOverlay.classList.add("active");
      document.body.style.overflow = "hidden"; // Prevent background scroll
      const input = searchOverlay.querySelector('input[name="s"]');
      if (input) setTimeout(() => input.focus(), 100);
    });
  }

  if (searchClose && searchOverlay) {
    searchClose.addEventListener("click", () => {
      searchOverlay.classList.remove("active");
      document.body.style.overflow = ""; // Restore scroll
    });
  }

  // Close search overlay on click outside container
  if (searchOverlay) {
    searchOverlay.addEventListener("click", (e) => {
      if (e.target === searchOverlay || e.target.classList.contains('container')) {
        searchOverlay.classList.remove("active");
        document.body.style.overflow = "";
      }
    });
  }

});

// Common utilities: global AJAX setup, CSRF header, toast messages
(function () {
  if (window.jQuery) {
    $.ajaxSetup({
      headers: {
        "X-CSRF-Token": window.CSRF_TOKEN || "",
      },
    });
  }

  window.showToast = function (message, type) {
    var container = document.getElementById("toastContainer");
    if (!container) {
      container = document.createElement("div");
      container.id = "toastContainer";
      container.style.position = "fixed";
      container.style.top = "16px";
      container.style.right = "16px";
      container.style.zIndex = "10000";
      document.body.appendChild(container);
    }

    var el = document.createElement("div");
    el.className = "toast " + (type || "info");
    el.textContent = message;
    container.prepend(el);
    setTimeout(function () {
      el.classList.add("hide");
      setTimeout(function () {
        el.remove();
      }, 300);
    }, 4000);
  };

  // Theme toggle with persistence
  try {
    var savedTheme = localStorage.getItem("theme");
    if (savedTheme === "light") {
      document.body.classList.add("theme-light");
    }
  } catch (e) {
    // ignore storage errors
  }

  function applyTheme(isLight) {
    if (isLight) {
      document.body.classList.add("theme-light");
      try {
        localStorage.setItem("theme", "light");
      } catch (e) {}
    } else {
      document.body.classList.remove("theme-light");
      try {
        localStorage.setItem("theme", "dark");
      } catch (e) {}
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    var btn = document.getElementById("themeToggle");
    if (btn) {
      btn.addEventListener("click", function () {
        var isLight = !document.body.classList.contains("theme-light");
        applyTheme(isLight);
        window.showToast(
          isLight ? "Light theme enabled" : "Dark theme enabled",
          "success"
        );
      });
    }
  });
})();

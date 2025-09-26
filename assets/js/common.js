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
})();

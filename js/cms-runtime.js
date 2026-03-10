(function () {
  async function loadSettings() {
    try {
      const response = await fetch("/cms/api/settings.php", {
        headers: {
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        return;
      }

      const payload = await response.json();
      if (!payload || !payload.success || !payload.settings) {
        return;
      }

      applySettings(payload.settings);
    } catch (error) {
      console.warn("CMS runtime settings unavailable.", error);
    }
  }

  function applySettings(settings) {
    document.querySelectorAll("[data-cms-text]").forEach((element) => {
      const key = element.getAttribute("data-cms-text");
      if (settings[key]) {
        element.textContent = settings[key];
      }
    });

    document.querySelectorAll("[data-cms-html]").forEach((element) => {
      const key = element.getAttribute("data-cms-html");
      if (settings[key]) {
        element.innerHTML = settings[key];
      }
    });

    document.querySelectorAll("[data-cms-href]").forEach((element) => {
      const key = element.getAttribute("data-cms-href");
      if (settings[key]) {
        element.setAttribute("href", settings[key]);
      }
    });
  }

  document.addEventListener("DOMContentLoaded", loadSettings);
})();

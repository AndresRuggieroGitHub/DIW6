(function () {
  const currentScript = document.currentScript;
  const fallbackUrl = "../public/js/script.js";
  const publicScriptUrl = currentScript?.src
    ? new URL("../public/js/script.js", currentScript.src).href
    : fallbackUrl;

  const publicScript = document.createElement("script");
  publicScript.src = publicScriptUrl;
  document.head.appendChild(publicScript);
})();

document.addEventListener("DOMContentLoaded", () => {
  const CART_KEY = "lexiCart";
  const LIBRARY_KEY = "lexiLibrary";
  const LANG_KEY = "lexiLang";

  const LANG_CONFIG = {
    // Europa Occidental (lenguas más estudiadas)
    en: { flag: "icons/flags/united_kingdom_flag.svg", label: "Inglés" },
    es: { flag: "icons/flags/spain_flag.svg", label: "Español" },
    fr: { flag: "icons/flags/france_flag.svg", label: "Francés" },
    de: { flag: "icons/flags/germany_flag.svg", label: "Alemán" },
    it: { flag: "icons/flags/italy_flag.svg", label: "Italiano" },
    pt: { flag: "icons/flags/brazil_flag.svg", label: "Portugués" },
    // Germánico continental
    nl: { flag: "icons/flags/netherlands_flag.svg", label: "Neerlandés" },
    // Países Nórdicos
    no: { flag: "icons/flags/norway_flag.svg", label: "Noruego" },
    sv: { flag: "icons/flags/sweden_flag.svg", label: "Sueco" },
    dk: { flag: "icons/flags/denmark_flag.svg", label: "Danés" },
    fi: { flag: "icons/flags/finland_flag.svg", label: "Finés" },
    // Europa del Este (eslavos)
    ru: { flag: "icons/flags/russia_flag.svg", label: "Ruso" },
    ua: { flag: "icons/flags/ukraine_flag.svg", label: "Ucraniano" },
    pl: { flag: "icons/flags/poland_flag.svg", label: "Polaco" },
    cs: { flag: "icons/flags/czech_republic_flag.svg", label: "Checo" },
    sk: { flag: "icons/flags/slovakia_flag.svg", label: "Eslovaco" },
    // Europa Central y Balcánica
    hu: { flag: "icons/flags/hungary_flag.svg", label: "Húngaro" },
    ro: { flag: "icons/flags/romania_flag.svg", label: "Rumano" },
    bg: { flag: "icons/flags/bulgaria_flag.svg", label: "Búlgaro" },
    gr: { flag: "icons/flags/greece_flag.svg", label: "Griego" },
    // Oriente Medio
    tr: { flag: "icons/flags/turkey_flag.svg", label: "Turco" },
    ar: { flag: "icons/flags/saudi_arabia_flag.svg", label: "Árabe" },
    he: { flag: "icons/flags/israel_flag.svg", label: "Hebreo" },
    // Asia Oriental
    zh: { flag: "icons/flags/china_flag.svg", label: "Chino" },
    ja: { flag: "icons/flags/japan_flag.svg", label: "Japonés" },
    ko: { flag: "icons/flags/south_korea_flag.svg", label: "Coreano" },
    // Asia del Sur y Sudeste
    hi: { flag: "icons/flags/india_flag.svg", label: "Hindi" },
    th: { flag: "icons/flags/thailand_flag.svg", label: "Tailandés" },
    vi: { flag: "icons/flags/vietnam_flag.svg", label: "Vietnamita" },
    id: { flag: "icons/flags/indonesia_flag.svg", label: "Indonesio" },
  };

  const NATIVE_LANG = "es";

  const LANG_HISTORY_KEY = "lexiLangHistory";
  const getLangHistory = () => {
    try { return JSON.parse(localStorage.getItem(LANG_HISTORY_KEY) || "[]"); } catch { return []; }
  };
  const recordLangActivity = (lang) => {
    if (!lang || lang === NATIVE_LANG) return;
    const hist = getLangHistory();
    if (!hist.find(h => h.lang === lang)) {
      hist.push({ lang, firstAt: Date.now() });
      localStorage.setItem(LANG_HISTORY_KEY, JSON.stringify(hist));
    }
  };

  const getActiveLang = () => localStorage.getItem(LANG_KEY) || "en";
  const setActiveLang = (lang) => localStorage.setItem(LANG_KEY, lang);

  const getCart = () => {
    try {
      const raw = localStorage.getItem(CART_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch {
      return [];
    }
  };

  const saveCart = (cart) => {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  };

  const getLibrary = () => {
    try {
      const raw = localStorage.getItem(LIBRARY_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch {
      return [];
    }
  };

  const saveLibrary = (items) => {
    localStorage.setItem(LIBRARY_KEY, JSON.stringify(items));
  };

  const COLLECTIONS_KEY = "lexiCollections";
  const getCollections = () => {
    try { return JSON.parse(localStorage.getItem(COLLECTIONS_KEY) || "[]"); } catch { return []; }
  };
  const saveCollections = (c) => { localStorage.setItem(COLLECTIONS_KEY, JSON.stringify(c)); };

  const updateAllBookmarkStates = () => {
    const library = getLibrary();
    const collections = getCollections();
    document.querySelectorAll("[data-word-card]").forEach(card => {
      const mainBtn = card.querySelector("[data-save-word]");
      if (!mainBtn) return;
      const wordId = card.dataset.wordId;
      const inMain = library.some(i => i.id === wordId);
      const inAny = inMain || collections.some(c => (c.items || []).some(i => i.id === wordId));
      mainBtn.classList.toggle("is-saved", inMain);
      mainBtn.innerHTML = `<i class="bi bi-bookmark${inAny ? "-fill" : ""}"></i>`;
      // Arrow stays neutral but show filled if saved anywhere but not in main
      const arrow = card.querySelector("[data-save-pick]");
      if (arrow) arrow.style.color = (!inMain && inAny) ? "var(--brand-dark)" : "";
    });
  };

  const setupSaveDropdown = () => {
    const dropdown = document.getElementById("saveDropdown");
    const ddList = document.getElementById("saveDropdownLists");
    const newBtn = document.getElementById("saveDropdownNewBtn");
    const newInput = document.getElementById("saveDropdownNewInput");
    const newName = document.getElementById("newCollectionName");
    const newConfirm = document.getElementById("newCollectionConfirm");
    const newCancel = document.getElementById("newCollectionCancel");
    if (!dropdown) return;

    let activeCard = null;

    const close = () => { dropdown.hidden = true; activeCard = null; window._activeDropdownCard = null; };
    window._closeSaveDropdown = close;

    const renderDropdown = () => {
      if (!activeCard) return;
      const wordId = activeCard.dataset.wordId;
      const library = getLibrary();
      const collections = getCollections();
      const activeLang = getActiveLang();
      const all = [
        { id: "__main__", name: "Mi lista", saved: library.some(i => i.id === wordId) },
        ...collections.filter(c => !c.lang || c.lang === activeLang).map(c => ({ id: c.id, name: c.name, saved: (c.items || []).some(i => i.id === wordId) }))
      ];
      ddList.innerHTML = all.map(c => `
        <li class="save-dropdown-item" data-coll-id="${c.id}">
          <span class="save-dropdown-item-icon"><i class="bi bi-bookmark${c.saved ? "-fill" : ""}"></i></span>
          <span>${c.name}</span>
        </li>`).join("");

      ddList.querySelectorAll(".save-dropdown-item").forEach(item => {
        item.addEventListener("click", () => {
          const collId = item.dataset.collId;
          const wordLabel = activeCard.dataset.wordLabel ||
            activeCard.querySelector("h2")?.textContent?.trim() || "Palabra";
          if (collId === "__main__") {
            const lib = getLibrary();
            const exists = lib.some(i => i.id === wordId);
            if (exists) saveLibrary(lib.filter(i => i.id !== wordId));
            else saveLibrary([...lib, { id: wordId, label: wordLabel }]);
          } else {
            const colls = getCollections();
            const idx = colls.findIndex(c => c.id === collId);
            if (idx === -1) return;
            const items = colls[idx].items || [];
            const exists = items.some(i => i.id === wordId);
            if (exists) colls[idx].items = items.filter(i => i.id !== wordId);
            else colls[idx].items = [...items, { id: wordId, label: wordLabel }];
            saveCollections(colls);
          }
          updateAllBookmarkStates();
          renderDropdown();
          window.dispatchEvent(new Event("lexi-library-updated"));
        });
      });
    };

    window._openSaveDropdown = (card, btn) => {
      activeCard = card;
      window._activeDropdownCard = card;
      newInput.hidden = true;
      newName.value = "";
      newBtn.hidden = false;
      renderDropdown();
      dropdown.hidden = false;
      // Position
      const rect = btn.getBoundingClientRect();
      const ddW = 240;
      let left = rect.right - ddW;
      if (left < 8) left = 8;
      let top = rect.bottom + 6;
      dropdown.style.left = left + "px";
      dropdown.style.top = top + "px";
      // Adjust if off bottom
      requestAnimationFrame(() => {
        const ddH = dropdown.offsetHeight;
        if (top + ddH > window.innerHeight - 8) {
          dropdown.style.top = (rect.top - ddH - 6) + "px";
        }
      });
    };

    newBtn.addEventListener("click", () => { newBtn.hidden = true; newInput.hidden = false; newName.focus(); });
    newCancel.addEventListener("click", () => { newBtn.hidden = false; newInput.hidden = true; newName.value = ""; });
    newConfirm.addEventListener("click", () => {
      const name = newName.value.trim();
      if (!name) return;
      saveCollections([...getCollections(), { id: "coll-" + Date.now(), name, lang: getActiveLang(), items: [] }]);
      newName.value = "";
      newBtn.hidden = false;
      newInput.hidden = true;
      updateAllBookmarkStates();
      renderDropdown();
      window.dispatchEvent(new Event("lexi-library-updated"));
    });
    newName.addEventListener("keydown", e => {
      if (e.key === "Enter") newConfirm.click();
      if (e.key === "Escape") newCancel.click();
    });
    document.addEventListener("click", e => {
      if (dropdown.hidden) return;
      if (!dropdown.contains(e.target) && !e.target.closest("[data-save-pick]") && !e.target.closest("[data-save-word]")) close();
    });
    document.addEventListener("keydown", e => { if (e.key === "Escape" && !dropdown.hidden) close(); });
  };

  const cartTotalItems = (cart) => cart.reduce((acc, item) => acc + item.qty, 0);
  const cartTotalPrice = (cart) => cart.reduce((acc, item) => acc + item.price * item.qty, 0);
  const formatEur = (n) => `${Number(n).toFixed(2)} EUR`;

  const showAlert = (message, type = "success") => {
    let alertBox = document.getElementById("globalAlert");
    if (!alertBox) {
      alertBox = document.createElement("div");
      alertBox.id = "globalAlert";
      alertBox.className = "global-alert";
      document.body.appendChild(alertBox);
    }

    alertBox.innerHTML = `<div class="alert alert-${type} mb-0" role="status">${message}</div>`;

    clearTimeout(showAlert.timer);
    showAlert.timer = setTimeout(() => {
      alertBox.innerHTML = "";
    }, 2200);
  };

  const updateCartBadges = () => {
    const count = cartTotalItems(getCart());
    document.querySelectorAll("[data-cart-count]").forEach((el) => {
      el.textContent = count;
      el.setAttribute("data-cart-count", count);
    });
  };

  const addToCart = (product) => {
    const cart = getCart();
    const existing = cart.find((p) => p.id === product.id);

    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({ ...product, qty: 1 });
    }

    saveCart(cart);
    updateCartBadges();
    renderCartDrawer();
  };

  const removeFromCart = (id) => {
    const cart = getCart().filter((item) => item.id !== id);
    saveCart(cart);
    updateCartBadges();
    renderCartDrawer();
    renderCartPage();
  };

  const changeQty = (id, delta) => {
    const cart = getCart();
    const item = cart.find((p) => p.id === id);
    if (!item) return;

    item.qty += delta;

    if (item.qty <= 0) {
      saveCart(cart.filter((p) => p.id !== id));
    } else {
      saveCart(cart);
    }

    updateCartBadges();
    renderCartDrawer();
    renderCartPage();
  };

  const clearCart = () => {
    saveCart([]);
    updateCartBadges();
    renderCartDrawer();
    renderCartPage();
  };

  const createCartDrawer = () => {
    if (document.getElementById("cartDrawer")) return;

    const backdrop = document.createElement("div");
    backdrop.className = "cart-drawer-backdrop";
    backdrop.id = "cartDrawerBackdrop";

    const drawer = document.createElement("aside");
    drawer.className = "cart-drawer";
    drawer.id = "cartDrawer";
    drawer.setAttribute("aria-label", "Resumen del carrito");

    drawer.innerHTML = `
      <div class="cart-drawer-header">
        <strong>Carrito</strong>
        <button class="btn btn-sm btn-outline-secondary" type="button" id="closeCartDrawer">Cerrar</button>
      </div>
      <div class="cart-drawer-body" id="cartDrawerBody"></div>
      <div class="cart-drawer-footer">
        <div class="cart-total-line">
          <span>Total</span>
          <span id="cartDrawerTotal">0.00 EUR</span>
        </div>
        <a class="btn btn-primary w-100" href="carrito.html">Ir al carrito</a>
      </div>
    `;

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    backdrop.addEventListener("click", () => toggleCartDrawer(false));
    drawer.querySelector("#closeCartDrawer").addEventListener("click", () => toggleCartDrawer(false));
  };

  const toggleCartDrawer = (open) => {
    const drawer = document.getElementById("cartDrawer");
    const backdrop = document.getElementById("cartDrawerBackdrop");
    if (!drawer || !backdrop) return;

    drawer.classList.toggle("open", open);
    backdrop.classList.toggle("open", open);
  };

  const renderCartDrawer = () => {
    const body = document.getElementById("cartDrawerBody");
    const total = document.getElementById("cartDrawerTotal");
    if (!body || !total) return;

    const cart = getCart();

    if (!cart.length) {
      body.innerHTML = '<p class="cart-empty">Tu carrito esta vacio.</p>';
      total.textContent = formatEur(0);
      return;
    }

    body.innerHTML = cart
      .map(
        (item) => `
          <div class="cart-drawer-item">
            <div>
              <strong>${item.name}</strong><br>
              <small>Cantidad: ${item.qty}</small>
            </div>
            <div>${formatEur(item.price * item.qty)}</div>
          </div>
        `
      )
      .join("");

    total.textContent = formatEur(cartTotalPrice(cart));
  };

  const setupCartTriggers = () => {
    createCartDrawer();
    renderCartDrawer();

    document.querySelectorAll("[data-cart-trigger]").forEach((btn) => {
      btn.addEventListener("click", () => {
        renderCartDrawer();
        toggleCartDrawer(true);
      });
    });
  };

  const createUtilityDrawer = () => {
    if (document.getElementById("utilityDrawer")) return;

    const currentPage = window.location.pathname.split("/").pop() || "app.html";
    const activeClass = (href) => currentPage === href ? " utility-link--active" : "";

    const backdrop = document.createElement("div");
    backdrop.className = "utility-drawer-backdrop";
    backdrop.id = "utilityDrawerBackdrop";

    const drawer = document.createElement("aside");
    drawer.className = "utility-drawer";
    drawer.id = "utilityDrawer";
    drawer.setAttribute("aria-label", "Menu secundario");

    drawer.innerHTML = `
      <div class="utility-drawer-header">
        <strong>Menu</strong>
        <button class="btn btn-sm btn-outline-secondary" type="button" id="closeUtilityDrawer">Cerrar</button>
      </div>
      <div class="utility-drawer-body">
        <div class="utility-drawer-section utility-drawer-section--mobile-nav">
          <p class="utility-drawer-label">Navegación</p>
          <a class="utility-link${activeClass("app.html")}" href="app.html"><i class="bi bi-house"></i><span>Inicio</span></a>
          <a class="utility-link${activeClass("biblioteca.html")}" href="biblioteca.html"><i class="bi bi-journals"></i><span>Biblioteca</span></a>
          <a class="utility-link${activeClass("ejercicios.html")}" href="ejercicios.html"><i class="bi bi-lightning-charge"></i><span>Ejercicios</span></a>
        </div>
        <div class="utility-drawer-section">
          <p class="utility-drawer-label">Más opciones</p>
          <a class="utility-link utility-link--premium${activeClass("producto.html")}" href="producto.html"><i class="bi bi-gem"></i><span>Premium</span></a>
          <a class="utility-link utility-link--cart${activeClass("carrito.html")}" href="carrito.html"><i class="bi bi-bag"></i><span>Carrito</span><span class="cart-count-badge cart-count-badge--drawer" data-cart-count="0">0</span></a>
          <a class="utility-link${activeClass("contacto.html")}" href="contacto.html"><i class="bi bi-envelope"></i><span>Contacto</span></a>
          <a class="utility-link${activeClass("info.html")}" href="info.html"><i class="bi bi-info-circle"></i><span>Información</span></a>
          <button class="btn btn-outline-danger utility-logout-btn" type="button" id="logoutAction"><i class="bi bi-box-arrow-right"></i><span>Cerrar sesión</span></button>
        </div>
      </div>
    `;

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    backdrop.addEventListener("click", () => toggleUtilityDrawer(false));
    drawer.querySelector("#closeUtilityDrawer").addEventListener("click", () => toggleUtilityDrawer(false));
    drawer.querySelector("#logoutAction").addEventListener("click", () => {
      toggleUtilityDrawer(false);
      showAlert("Sesión cerrada (demo)", "warning");
    });
  };

  const syncUtilityDrawerForViewport = () => {
    const mobileNavSection = document.querySelector(".utility-drawer-section--mobile-nav");
    if (!mobileNavSection) return;

    mobileNavSection.hidden = window.innerWidth > 600;
  };

  const toggleUtilityDrawer = (open) => {
    const drawer = document.getElementById("utilityDrawer");
    const backdrop = document.getElementById("utilityDrawerBackdrop");
    if (!drawer || !backdrop) return;

    drawer.classList.toggle("open", open);
    backdrop.classList.toggle("open", open);

    document.querySelectorAll("[data-utility-trigger]").forEach((btn) => {
      btn.setAttribute("aria-expanded", String(open));
    });
  };

  const setupUtilityMenu = () => {
    createUtilityDrawer();
    syncUtilityDrawerForViewport();

    window.addEventListener("resize", syncUtilityDrawerForViewport);

    document.querySelectorAll("[data-utility-trigger]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const isOpen = document.getElementById("utilityDrawer")?.classList.contains("open");
        toggleUtilityDrawer(!isOpen);
      });
    });
  };

  const setupAddToCartButtons = () => {
    document.querySelectorAll("[data-add-cart]").forEach((btn) => {
      btn.addEventListener("click", () => {
        addToCart({
          id: btn.dataset.id,
          name: btn.dataset.name,
          price: Number(btn.dataset.price)
        });
        showAlert("Producto anadido al carrito");
      });
    });
  };

  let pendingRemoveId = null;

  const renderCartPage = () => {
    const tbody = document.getElementById("cartItems");
    const total = document.getElementById("cartTotal");
    if (!tbody || !total) return;

    const cart = getCart();

    if (!cart.length) {
      tbody.innerHTML = '<tr><td colspan="5">No hay productos en el carrito.</td></tr>';
      total.textContent = formatEur(0);
      return;
    }

    tbody.innerHTML = cart
      .map(
        (item) => `
          <tr>
            <td>${item.name}</td>
            <td>
              <div class="cart-qty-controls">
                <button class="qty-btn" type="button" data-qty-minus="${item.id}" aria-label="Restar cantidad">-</button>
                <span>${item.qty}</span>
                <button class="qty-btn" type="button" data-qty-plus="${item.id}" aria-label="Sumar cantidad">+</button>
              </div>
            </td>
            <td>${formatEur(item.price)}</td>
            <td>${formatEur(item.price * item.qty)}</td>
            <td>
              <button class="btn btn-sm btn-outline-danger" type="button" data-remove-id="${item.id}">Eliminar</button>
            </td>
          </tr>
        `
      )
      .join("");

    total.textContent = formatEur(cartTotalPrice(cart));
  };

  const setupCartPageEvents = () => {
    const cartPage = document.querySelector("[data-cart-page]");
    if (!cartPage) return;

    const tbody = document.getElementById("cartItems");
    const clearButton = document.getElementById("clearCart");
    const confirmBtn = document.getElementById("confirmRemoveBtn");
    const modalElement = document.getElementById("confirmRemoveModal");
    const bsModal = modalElement && window.bootstrap ? new bootstrap.Modal(modalElement) : null;

    if (tbody) {
      tbody.addEventListener("click", (e) => {
        const plusId = e.target.getAttribute("data-qty-plus");
        const minusId = e.target.getAttribute("data-qty-minus");
        const removeId = e.target.getAttribute("data-remove-id");

        if (plusId) return changeQty(plusId, 1);
        if (minusId) return changeQty(minusId, -1);

        if (removeId) {
          pendingRemoveId = removeId;
          if (bsModal) bsModal.show();
          else if (confirm("Quieres eliminar este producto del carrito?")) removeFromCart(removeId);
        }
      });
    }

    if (confirmBtn) {
      confirmBtn.addEventListener("click", () => {
        if (!pendingRemoveId) return;
        removeFromCart(pendingRemoveId);
        pendingRemoveId = null;
        if (bsModal) bsModal.hide();
      });
    }

    if (clearButton) {
      clearButton.addEventListener("click", () => {
        clearCart();
        showAlert("Carrito vaciado", "warning");
      });
    }

    renderCartPage();
  };

  const setupNavToggle = () => {
    const toggle = document.querySelector(".nav-toggle");
    const nav = document.querySelector(".nav-left");
    if (!toggle || !nav) return;

    toggle.addEventListener("click", () => {
      const isOpen = nav.classList.toggle("open");
      toggle.setAttribute("aria-expanded", String(isOpen));
    });
  };

  const setupScrollButton = () => {
    const btnSubir = document.getElementById("btnSubir");
    if (!btnSubir) return;

    const onScroll = () => {
      btnSubir.style.display = window.scrollY > 200 ? "block" : "none";
    };

    onScroll();
    window.addEventListener("scroll", onScroll);

    btnSubir.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  };

  const setupKeyboardShortcut = () => {
    document.addEventListener("keydown", (e) => {
      const isTyping = ["INPUT", "TEXTAREA", "SELECT"].includes(document.activeElement?.tagName);
      if (isTyping) return;

      if (e.key.toLowerCase() === "t") {
        window.scrollTo({ top: 0, behavior: "smooth" });
        showAlert("Atajo: volver arriba");
      }
    });
  };

  const setupObserver = () => {
    const items = document.querySelectorAll(".scroll-animado");
    if (!items.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) entry.target.classList.add("visible");
      });
    }, { threshold: 0.12 });

    items.forEach((item) => observer.observe(item));
  };

  const setupActionButtons = () => {
    document.querySelectorAll(".accion").forEach((btn) => {
      btn.addEventListener("click", () => {
        showAlert("Ejercicio iniciado");
      });
    });
  };

  const setupLibraryList = () => {
    const cards = Array.from(document.querySelectorAll("[data-word-card]"));
    const listEl = document.getElementById("librarySavedList");
    const countEl = document.getElementById("libraryCount");
    const clearBtn = document.getElementById("clearLibrary");
    const filterInput = document.getElementById("savedWordFilter");
    const listsGrid = document.getElementById("listsGrid");
    const listsIndex = document.getElementById("listsIndex");
    const listDetail = document.getElementById("listDetail");
    const listBackBtn = document.getElementById("listBackBtn");
    const listDetailTitle = document.getElementById("listDetailTitle");

    // --- Vista índice: tarjeta por lista ---
    const renderListsIndex = () => {
      if (!listsGrid) return;
      const saved = getLibrary();
      const lang = getActiveLang();
      const filtered = saved.filter((item) => {
        if (!item.id) return true;
        if (item.id.startsWith("custom-")) return item.id.startsWith(`custom-${lang}-`);
        const card = document.querySelector(`[data-word-id="${item.id}"]`);
        if (card) return card.dataset.language === lang;
        return true;
      });

      const count = filtered.length;
      const collections = getCollections().filter(c => !c.lang || c.lang === lang);

      // Tarjeta "Mi lista" siempre primera
      let html = `
        <div class="list-card" id="mainListCard" role="button" tabindex="0" aria-label="Abrir Mi lista">
          <div class="list-card-top">
            <div class="list-card-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div>
              <p class="list-card-name">Mi lista</p>
              <span class="list-card-count">${count} ${count === 1 ? "palabra" : "palabras"}</span>
            </div>
          </div>
        </div>`;

      // Tarjetas de colecciones extra
      collections.forEach(c => {
        const cCount = (c.items || []).length;
        html += `
        <div class="list-card" data-coll-card="${c.id}" role="button" tabindex="0" aria-label="Abrir ${c.name}">
          <div class="list-card-top">
            <div class="list-card-icon" style="background:#f0eeff;color:#7c3aed"><i class="bi bi-collection"></i></div>
            <div>
              <p class="list-card-name">${c.name}</p>
              <span class="list-card-count">${cCount} ${cCount === 1 ? "palabra" : "palabras"}</span>
            </div>
          </div>
          <div class="coll-kebab-wrap">
            <button class="coll-kebab-btn" type="button" data-kebab-coll="${c.id}" aria-label="Opciones de ${c.name}"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="coll-kebab-menu" data-kebab-menu="${c.id}" hidden>
              <button type="button" data-rename-coll="${c.id}"><i class="bi bi-pencil"></i> Renombrar</button>
              <div class="coll-kebab-divider"></div>
              <button type="button" data-clear-coll="${c.id}" class="coll-kebab-clear"><i class="bi bi-eraser"></i> Vaciar lista</button>
              <button type="button" data-delete-coll="${c.id}" class="coll-kebab-delete"><i class="bi bi-trash"></i> Eliminar</button>
            </div>
          </div>
        </div>`;
      });

      listsGrid.innerHTML = html;

      document.getElementById("mainListCard")?.addEventListener("click", openDetail);
      document.getElementById("mainListCard")?.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") openDetail();
      });

      listsGrid.querySelectorAll("[data-coll-card]").forEach(card => {
        card.addEventListener("click", (e) => {
          if (e.target.closest("[data-kebab-coll]") || e.target.closest(".coll-kebab-menu")) return;
          openCollDetail(card.dataset.collCard);
        });
        card.addEventListener("keydown", (e) => {
          if (e.key === "Enter" || e.key === " ") openCollDetail(card.dataset.collCard);
        });
      });

      // Kebab: abrir/cerrar menú
      listsGrid.querySelectorAll("[data-kebab-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.kebabColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          // Cerrar todos los demás
          listsGrid.querySelectorAll(".coll-kebab-menu").forEach(m => { if (m !== menu) m.hidden = true; });
          menu.hidden = !menu.hidden;
        });
      });

      // Kebab: renombrar
      listsGrid.querySelectorAll("[data-rename-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.renameColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          if (menu) menu.hidden = true;
          openRenameModal(id, renderListsIndex);
        });
      });

      // Kebab: vaciar lista
      listsGrid.querySelectorAll("[data-clear-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.clearColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          if (menu) menu.hidden = true;
          openClearModal(id, renderListsIndex);
        });
      });

      // Kebab: eliminar
      listsGrid.querySelectorAll("[data-delete-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.deleteColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          if (menu) menu.hidden = true;
          openDeleteModal(id, renderListsIndex);
        });
      });
    };

    // Cerrar kebab al hacer click fuera (registrado una sola vez)
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".coll-kebab-wrap")) {
        listsGrid?.querySelectorAll(".coll-kebab-menu").forEach(m => m.hidden = true);
      }
    });

    // Botón "Nueva colección" del índice
    const newCollTopBtn = document.getElementById("newCollectionTopBtn");
    if (newCollTopBtn) {
      newCollTopBtn.addEventListener("click", () => {
        const name = prompt("Nombre de la nueva colección:");
        if (!name || !name.trim()) return;
        saveCollections([...getCollections(), { id: "coll-" + Date.now(), name: name.trim(), lang: getActiveLang(), items: [] }]);
        renderListsIndex();
      });
    }

    const addPanel = document.querySelector(".library-add-panel");
    let activeCollId = null;

    const showDetail = (title) => {
      if (listsIndex) { listsIndex.style.animation = "fadeOut 0.18s ease forwards"; setTimeout(() => { listsIndex.hidden = true; listsIndex.style.animation = ""; }, 180); }
      if (addPanel) { addPanel.style.animation = "fadeOut 0.18s ease forwards"; setTimeout(() => { addPanel.hidden = true; addPanel.style.animation = ""; }, 180); }
      setTimeout(() => {
        if (listDetail) { listDetail.hidden = false; listDetail.style.animation = "fadeIn 0.2s ease"; }
        if (listDetailTitle) listDetailTitle.textContent = title;
        renderList();
      }, 190);
    };

    const openDetail = () => {
      activeCollId = null;
      showDetail("Mi lista");
    };

    const openCollDetail = (collId) => {
      const coll = getCollections().find(c => c.id === collId);
      if (!coll) return;
      activeCollId = collId;
      showDetail(coll.name);
    };

    const closeDetail = () => {
      activeCollId = null;
      if (listDetail) { listDetail.style.animation = "fadeOut 0.18s ease forwards"; setTimeout(() => { listDetail.hidden = true; listDetail.style.animation = ""; }, 180); }
      setTimeout(() => {
        if (addPanel) { addPanel.hidden = false; addPanel.style.animation = "fadeIn 0.2s ease"; }
        if (listsIndex) { listsIndex.hidden = false; listsIndex.style.animation = "fadeIn 0.2s ease"; }
        renderListsIndex();
      }, 190);
    };

    if (listBackBtn) listBackBtn.addEventListener("click", closeDetail);

    if (!cards.length || !listEl || !countEl) return;

    const PAGE_SIZE = 10;
    let currentPage = 0;

    const paginationEl = document.getElementById("listPagination");
    const pagePrevBtn  = document.getElementById("listPagePrev");
    const pageNextBtn  = document.getElementById("listPageNext");
    const pageInfoEl   = document.getElementById("listPageInfo");

    const renderList = () => {
      const query = filterInput ? filterInput.value.trim().toLowerCase() : "";
      let source;
      if (activeCollId) {
        const coll = getCollections().find(c => c.id === activeCollId);
        source = coll ? (coll.items || []) : [];
      } else {
        const lang = getActiveLang();
        source = getLibrary().filter((item) => {
          if (!item.id) return true;
          if (item.id.startsWith("custom-")) return item.id.startsWith(`custom-${lang}-`);
          const card = document.querySelector(`[data-word-id="${item.id}"]`);
          if (card) return card.dataset.language === lang;
          return true;
        });
      }

      let filtered = source;
      if (query) {
        filtered = filtered.filter((item) =>
          item.label.toLowerCase().includes(query)
        );
      }

      if (!filtered.length) {
        const hasAny = source.length > 0;
        listEl.innerHTML = query
          ? `<li class="library-empty-state"><i class="bi bi-search"></i><span>Ninguna palabra coincide con "<strong>${query}</strong>".</span></li>`
          : hasAny
          ? `<li class="library-empty-state"><i class="bi bi-globe"></i><span>No hay palabras guardadas en este idioma.</span></li>`
          : `<li class="library-empty-state"><i class="bi bi-journal-plus"></i><span>Esta lista está vacía.<br><small>Guarda palabras desde el catálogo.</small></span></li>`;
        if (paginationEl) paginationEl.hidden = true;
      } else {
        const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
        if (currentPage >= totalPages) currentPage = totalPages - 1;
        if (currentPage < 0) currentPage = 0;

        const pageStart = currentPage * PAGE_SIZE;
        const pageEnd   = pageStart + PAGE_SIZE;
        const toShow    = filtered.slice(pageStart, pageEnd);

        listEl.innerHTML = toShow
          .map((item) => {
            const card = document.querySelector(`[data-word-id="${item.id}"]`);
            const cefr        = card?.dataset.cefr        || "";
            const topic       = card?.dataset.topic       || "";
            const translation = card?.dataset.translation || item.translation || "";
            const cefrClass   = cefr ? `cefr-${cefr.toLowerCase()}` : "";
            return `<li class="saved-word-row">
              <div class="saved-word-info">
                <span class="saved-word-label">${item.label}</span>
                ${translation ? `<span class="saved-word-translation">${translation}</span>` : ""}
                <div class="saved-word-tags">
                  ${cefr  ? `<span class="cefr-badge ${cefrClass}">${cefr}</span>` : ""}
                  ${topic ? `<span class="topic-tag">${topic}</span>` : ""}
                </div>
              </div>
              <button class="remove-saved-btn" type="button" data-remove-saved="${item.id}" aria-label="Eliminar ${item.label}"><i class="bi bi-x"></i></button>
            </li>`;
          })
          .join("");

        if (paginationEl) {
          paginationEl.hidden = totalPages <= 1;
          if (pageInfoEl) pageInfoEl.textContent = `${currentPage + 1} / ${totalPages}`;
          if (pagePrevBtn) pagePrevBtn.disabled = currentPage === 0;
          if (pageNextBtn) pageNextBtn.disabled = currentPage >= totalPages - 1;
        }
      }

      countEl.textContent = `${filtered.length} ${filtered.length === 1 ? "palabra" : "palabras"}`;

      updateAllBookmarkStates();
    };

    listEl.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-remove-saved]");
      if (!btn) return;
      const id = btn.dataset.removeSaved;
      if (activeCollId) {
        const colls = getCollections();
        const idx = colls.findIndex(c => c.id === activeCollId);
        if (idx !== -1) { colls[idx].items = (colls[idx].items || []).filter(i => i.id !== id); saveCollections(colls); }
      } else {
        saveLibrary(getLibrary().filter((item) => item.id !== id));
      }
      showAlert("Palabra eliminada", "warning");
      renderList();
    });

    if (pagePrevBtn) {
      pagePrevBtn.addEventListener("click", () => {
        if (currentPage > 0) { currentPage--; renderList(); listEl.scrollIntoView({ behavior: "smooth", block: "nearest" }); }
      });
    }
    if (pageNextBtn) {
      pageNextBtn.addEventListener("click", () => {
        currentPage++; renderList(); listEl.scrollIntoView({ behavior: "smooth", block: "nearest" });
      });
    }

    if (filterInput) {
      filterInput.addEventListener("input", () => {
        currentPage = 0;
        renderList();
      });
    }

    cards.forEach((card) => {
      // Botón principal: si está guardado en cualquier sitio → quita de todo; si no → añade a Mi lista
      const mainBtn = card.querySelector("[data-save-word]");
      if (mainBtn) {
        mainBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = card.dataset.wordId;
          const label = card.dataset.wordLabel || card.querySelector("h2")?.textContent?.trim() || "Palabra";
          const lib = getLibrary();
          const colls = getCollections();
          const inMain = lib.some(i => i.id === id);
          const inAny = inMain || colls.some(c => (c.items || []).some(i => i.id === id));
          if (inAny) {
            // Quitar de Mi lista y de todas las colecciones
            saveLibrary(lib.filter(i => i.id !== id));
            const updated = colls.map(c => ({ ...c, items: (c.items || []).filter(i => i.id !== id) }));
            saveCollections(updated);
          } else {
            recordLangActivity(card.dataset.language || getActiveLang());
            saveLibrary([...lib, { id, label }]);
          }
          updateAllBookmarkStates();
          window.dispatchEvent(new Event("lexi-library-updated"));
        });
      }
      // Flecha: abre el dropdown selector de colecciones
      const pickBtn = card.querySelector("[data-save-pick]");
      if (pickBtn) {
        pickBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          const dropdown = document.getElementById("saveDropdown");
          if (dropdown && !dropdown.hidden && window._activeDropdownCard === card) {
            window._closeSaveDropdown && window._closeSaveDropdown();
          } else {
            if (window._openSaveDropdown) window._openSaveDropdown(card, pickBtn);
          }
        });
      }
    });
    updateAllBookmarkStates();

    if (clearBtn) {
      clearBtn.addEventListener("click", () => {
        if (activeCollId) {
          const colls = getCollections();
          const idx = colls.findIndex(c => c.id === activeCollId);
          if (idx !== -1) { colls[idx].items = []; saveCollections(colls); }
        } else {
          saveLibrary([]);
        }
        currentPage = 0;
        if (filterInput) filterInput.value = "";
        renderList();
        renderListsIndex();
        showAlert("Lista vaciada", "warning");
      });
    }

    window.addEventListener("lexi-library-updated", () => {
      currentPage = 0;
      if (listDetail && !listDetail.hidden) renderList();
      else renderListsIndex();
    });
    window.addEventListener("lexi-lang-changed", () => {
      currentPage = 0;
      if (listDetail && !listDetail.hidden) renderList();
      else renderListsIndex();
    });

    // Arranque: siempre muestra el índice primero
    renderListsIndex();
  };

  // ---- Modal de renombrar colección ----
  const openRenameModal = (collId, onSaved) => {
    const modal = document.getElementById("renameCollModal");
    const input = document.getElementById("renameCollInput");
    const confirmBtn = document.getElementById("renameCollConfirm");
    const cancelBtn = document.getElementById("renameCollCancel");
    if (!modal || !input) return;

    const colls = getCollections();
    const coll = colls.find(c => c.id === collId);
    if (!coll) return;

    input.value = coll.name;
    modal.hidden = false;
    requestAnimationFrame(() => input.select());

    const close = () => {
      modal.hidden = true;
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      input.removeEventListener("keydown", onKey);
      modal.removeEventListener("click", onBackdrop);
    };
    const onConfirm = () => {
      const name = input.value.trim();
      if (!name) return;
      const updated = getCollections();
      const idx = updated.findIndex(c => c.id === collId);
      if (idx !== -1) { updated[idx].name = name; saveCollections(updated); }
      close();
      if (onSaved) onSaved();
      showAlert("Lista renombrada");
    };
    const onKey = (e) => {
      if (e.key === "Enter") onConfirm();
      if (e.key === "Escape") close();
    };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    input.addEventListener("keydown", onKey);
    modal.addEventListener("click", onBackdrop);
  };

  const openDeleteModal = (collId, onConfirmed) => {
    const modal      = document.getElementById("deleteCollModal");
    const bodyEl     = document.getElementById("deleteModalBody");
    const confirmBtn = document.getElementById("deleteCollConfirm");
    const cancelBtn  = document.getElementById("deleteCollCancel");
    if (!modal) return;

    const coll = getCollections().find(c => c.id === collId);
    if (bodyEl) bodyEl.textContent = coll
      ? `¿Eliminar "${coll.name}"? Esta acción no se puede deshacer.`
      : "Esta acción no se puede deshacer.";

    modal.hidden = false;

    const close = () => {
      modal.hidden = true;
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      modal.removeEventListener("click", onBackdrop);
      document.removeEventListener("keydown", onKey);
    };
    const onConfirm = () => {
      saveCollections(getCollections().filter(c => c.id !== collId));
      close();
      if (onConfirmed) onConfirmed();
      showAlert("Colección eliminada", "warning");
    };
    const onKey = (e) => { if (e.key === "Escape") close(); };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    modal.addEventListener("click", onBackdrop);
    document.addEventListener("keydown", onKey);
  };

  const openClearModal = (collId, onConfirmed) => {
    const modal      = document.getElementById("clearCollModal");
    const bodyEl     = document.getElementById("clearModalBody");
    const confirmBtn = document.getElementById("clearCollConfirm");
    const cancelBtn  = document.getElementById("clearCollCancel");
    if (!modal) return;

    const colls = getCollections();
    const idx   = colls.findIndex(c => c.id === collId);
    if (idx === -1) return;
    if (bodyEl) bodyEl.textContent = `¿Vaciar "${colls[idx].name}"? Se eliminarán todas las palabras.`;

    modal.hidden = false;

    const close = () => {
      modal.hidden = true;
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      modal.removeEventListener("click", onBackdrop);
      document.removeEventListener("keydown", onKey);
    };
    const onConfirm = () => {
      const updated = getCollections();
      const i = updated.findIndex(c => c.id === collId);
      if (i !== -1) { updated[i].items = []; saveCollections(updated); }
      close();
      if (onConfirmed) onConfirmed();
      showAlert("Lista vaciada", "warning");
    };
    const onKey = (e) => { if (e.key === "Escape") close(); };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    modal.addEventListener("click", onBackdrop);
    document.addEventListener("keydown", onKey);
  };

  const setupLibraryImport = () => {
    const fileInput = document.getElementById("wordFileInput");
    const dropZone = document.getElementById("dropZone");
    const chooseFileBtn = document.getElementById("chooseFileBtn");
    const fileNameDisplay = document.getElementById("fileNameDisplay");
    const importFileBtn = document.getElementById("importFileBtn");
    const pasteInput = document.getElementById("pasteWordsInput");
    const importPasteBtn = document.getElementById("importPasteBtn");

    if (!fileInput && !pasteInput) return;

    const normalize = (value) =>
      String(value || "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    const parseWords = (text) =>
      String(text || "")
        .split(/[\n,;\t]+/)
        .map((item) => item.trim())
        .filter((item) => item.length > 0);

    const addWords = (words, language = "en") => {
      const saved = getLibrary();
      const seen = new Set(saved.map((item) => normalize(item.label)));
      let added = 0;

      words.forEach((rawLabel) => {
        const label = rawLabel.trim();
        const key = normalize(label);
        if (!key || seen.has(key)) return;

        saved.push({
          id: `custom-${language}-${key.replace(/[^a-z0-9]+/g, "-")}`,
          label
        });
        seen.add(key);
        added += 1;
      });

      saveLibrary(saved);
      return added;
    };

    if (importFileBtn && fileInput) {
      if (chooseFileBtn) {
        chooseFileBtn.addEventListener("click", (e) => { e.stopPropagation(); fileInput.click(); });
      }
      if (fileInput && fileNameDisplay) {
        fileInput.addEventListener("change", () => {
          fileNameDisplay.textContent = fileInput.files[0]?.name ?? "";
        });
      }
      if (dropZone) {
        dropZone.addEventListener("dragover", (e) => { e.preventDefault(); dropZone.classList.add("drag-over"); });
        dropZone.addEventListener("dragleave", () => dropZone.classList.remove("drag-over"));
        dropZone.addEventListener("drop", (e) => {
          e.preventDefault();
          dropZone.classList.remove("drag-over");
          const file = e.dataTransfer.files?.[0];
          if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            fileNameDisplay.textContent = file.name;
          }
        });
      }
      importFileBtn.addEventListener("click", () => {
        const file = fileInput.files?.[0];
        if (!file) {
          showAlert("Selecciona un archivo primero", "warning");
          return;
        }

        const reader = new FileReader();
        reader.onload = () => {
          const words = parseWords(reader.result);
          const added = addWords(words, manualLang?.value || "en");
          showAlert(`${added} palabras importadas`, added ? "success" : "warning");
          window.dispatchEvent(new Event("lexi-library-updated"));
        };
        reader.readAsText(file);
      });
    }

    if (importPasteBtn && pasteInput) {
      importPasteBtn.addEventListener("click", () => {
        const words = parseWords(pasteInput.value);
        const added = addWords(words, manualLang?.value || "en");
        showAlert(`${added} palabras importadas`, added ? "success" : "warning");
        if (added) pasteInput.value = "";
        window.dispatchEvent(new Event("lexi-library-updated"));
      });
    }

    window.addEventListener("lexi-library-updated", renderList);
  };

  const setupLibrarySearch = () => {
    const searchInput = document.getElementById("searchInput");
    const cefrFilter = document.getElementById("cefrFilter");
    const topicFilter = document.getElementById("topicFilter");
    const cards = Array.from(document.querySelectorAll("[data-word-card]"));
    const noResults = document.getElementById("noResults");
    const paginationEl = document.getElementById("libraryPagination");

    if (!searchInput || !cards.length) return;

    const CARDS_PER_PAGE = 16;
    let currentPage = 1;
    let filteredCards = [];

    const normalize = (value) =>
      String(value || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    const renderPagination = (totalPages) => {
      if (!paginationEl) return;
      if (totalPages <= 1) {
        paginationEl.innerHTML = "";
        return;
      }

      paginationEl.innerHTML = Array.from({ length: totalPages }, (_, i) => {
        const page = i + 1;
        const active = page === currentPage ? "page-btn--active" : "";
        return `<button class="page-btn ${active}" type="button" data-page="${page}">${page}</button>`;
      }).join("");

      paginationEl.querySelectorAll("[data-page]").forEach((btn) => {
        btn.addEventListener("click", () => {
          currentPage = Number(btn.dataset.page);
          renderPage();
          document.getElementById("searchInput")?.scrollIntoView({ behavior: "smooth", block: "nearest" });
        });
      });
    };

    const renderPage = () => {
      const start = (currentPage - 1) * CARDS_PER_PAGE;
      const end = start + CARDS_PER_PAGE;

      cards.forEach((card) => {
        const idx = filteredCards.indexOf(card);
        card.hidden = idx === -1 || idx < start || idx >= end;
      });

      const totalPages = Math.ceil(filteredCards.length / CARDS_PER_PAGE);
      renderPagination(totalPages);
    };

    const applyFilters = () => {
      const query = normalize(searchInput.value.trim());
      const activeLang = getActiveLang();
      const cefrValue = cefrFilter ? cefrFilter.value : "all";
      const topicValue = topicFilter ? topicFilter.value : "all";

      filteredCards = cards.filter((card) => {
        const title = normalize(card.querySelector("h2")?.textContent);
        const text = normalize(card.textContent);
        const language = card.dataset.language || "all";
        const cefr = card.dataset.cefr || "all";
        const topic = card.dataset.topic || "";

        const matchesQuery = !query || title.includes(query) || text.includes(query);
        const matchesLanguage = language === activeLang;
        const matchesCefr = cefrValue === "all" || cefr === cefrValue;
        const matchesTopic = topicValue === "all" || topic === topicValue;

        return matchesQuery && matchesLanguage && matchesCefr && matchesTopic;
      });

      currentPage = 1;
      renderPage();

      if (noResults) noResults.hidden = filteredCards.length > 0;
    };

    searchInput.addEventListener("input", applyFilters);
    if (cefrFilter) cefrFilter.addEventListener("change", applyFilters);
    if (topicFilter) topicFilter.addEventListener("change", applyFilters);

    applyFilters();
  };

  const setupLangDropdown = () => {
    const trigger = document.querySelector("[data-lang-trigger]");
    const dropdown = document.getElementById("langDropdown");
    const flagImg = document.getElementById("activeLangFlag");

    const applyLang = (lang, saveToStorage = true) => {
      const cfg = LANG_CONFIG[lang];
      if (!cfg) return;
      if (saveToStorage) {
        setActiveLang(lang);
        location.href = location.pathname + location.search;
        return;
      }
      // Initial load: sync saved list with active lang
      window.dispatchEvent(new CustomEvent("lexi-lang-changed", { detail: { lang } }));
    };

    if (trigger && dropdown) {
      // Build dropdown items
      dropdown.innerHTML = Object.entries(LANG_CONFIG)
        .map(([code, cfg]) => {
          const isNative = code === NATIVE_LANG;
          return `<button
            class="lang-option${isNative ? " lang-option--native" : ""}"
            type="button"
            data-lang-code="${code}"
            ${isNative ? "disabled aria-disabled='true'" : ""}>
            <img src="${cfg.flag}" alt="${cfg.label}">
            <span>${cfg.label}</span>
            ${isNative ? "<span class='lang-native-badge'>nativa</span>" : ""}
          </button>`;
        })
        .join("");

      trigger.addEventListener("click", (e) => {
        e.stopPropagation();
        const isHidden = dropdown.hidden;
        dropdown.hidden = !isHidden;
        trigger.setAttribute("aria-expanded", String(isHidden));
      });

      dropdown.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-lang-code]");
        if (!btn || btn.disabled) return;
        applyLang(btn.dataset.langCode);
        dropdown.hidden = true;
        trigger.setAttribute("aria-expanded", "false");
      });

      document.addEventListener("click", (e) => {
        if (!e.target.closest("[data-lang-wrap]")) {
          dropdown.hidden = true;
          trigger.setAttribute("aria-expanded", "false");
        }
      });
    }

    // Apply stored lang on load — don't re-save, just reflect
    applyLang(getActiveLang(), false);
  };

  // ===== PANEL DE PROGRESO =====
  const CEFR_LEVELS = [
    { key: "c2", label: "C2", min: 70, next: null,  desc: "Dominio pleno" },
    { key: "c1", label: "C1", min: 55, next: 70,    desc: "Competencia profesional" },
    { key: "b2", label: "B2", min: 40, next: 55,    desc: "Independiente avanzado" },
    { key: "b1", label: "B1", min: 25, next: 40,    desc: "Independiente" },
    { key: "a2", label: "A2", min: 10, next: 25,    desc: "Elemental" },
    { key: "a1", label: "A1", min: 0,  next: 10,    desc: "Principiante" },
  ];

  const getCefrLevel = (wordCount) =>
    CEFR_LEVELS.find((l) => wordCount >= l.min) || CEFR_LEVELS[CEFR_LEVELS.length - 1];

  const createProgressDrawer = () => {
    if (document.getElementById("progressDrawer")) return;

    const backdrop = document.createElement("div");
    backdrop.className = "progress-drawer-backdrop";
    backdrop.id = "progressDrawerBackdrop";

    const drawer = document.createElement("aside");
    drawer.className = "progress-drawer";
    drawer.id = "progressDrawer";
    drawer.setAttribute("aria-label", "Tu progreso");

    drawer.innerHTML = `
      <div class="progress-drawer-header">
        <p class="progress-drawer-title">Tu progreso</p>
        <button class="progress-drawer-close" type="button" id="closeProgressDrawer" aria-label="Cerrar">&#x2715;</button>
      </div>
      <div class="progress-drawer-body" id="progressDrawerBody"></div>
      <div class="progress-drawer-footer">
        <a class="btn btn-warning w-100" href="progreso.html">Ver progreso completo</a>
      </div>
    `;

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    backdrop.addEventListener("click", () => toggleProgressDrawer(false));
    drawer.querySelector("#closeProgressDrawer").addEventListener("click", () => toggleProgressDrawer(false));
  };

  const toggleProgressDrawer = (open) => {
    const drawer = document.getElementById("progressDrawer");
    const backdrop = document.getElementById("progressDrawerBackdrop");
    if (!drawer || !backdrop) return;
    drawer.classList.toggle("open", open);
    backdrop.classList.toggle("open", open);
  };

  const renderProgressDrawer = () => {
    const body = document.getElementById("progressDrawerBody");
    if (!body) return;

    const library = getLibrary();
    const wordCount = library.length;

    let stats = {};
    try { stats = JSON.parse(localStorage.getItem("lexiStats") || "{}"); } catch (e) {}

    const streak = stats.streak || 0;
    const modeData = [
      { key: "reading",   label: "Lectura",   icon: "bi-book-half",         color: "#4f8ef7" },
      { key: "listening", label: "Escucha",   icon: "bi-headphones",        color: "#f76b4f" },
      { key: "speaking",  label: "Habla",     icon: "bi-mic-fill",          color: "#2dc98b" },
      { key: "writing",   label: "Escritura", icon: "bi-pencil-fill",       color: "#a855f7" },
      { key: "mix",       label: "Combinado", icon: "bi-shuffle",           color: "#f9b233" },
    ];
    const totalEx = modeData.reduce((acc, m) => acc + (stats[m.key] || 0), 0);

    const level = getCefrLevel(wordCount);
    const pct = level.next
      ? Math.min(100, Math.round(((wordCount - level.min) / (level.next - level.min)) * 100))
      : 100;
    const toNext = level.next ? level.next - wordCount : 0;

    body.innerHTML = `
      <!-- Racha -->
      <div>
        <p class="pd-section-title">Racha diaria</p>
        <div class="pd-streak">
          <span class="pd-streak-fire">${streak > 0 ? "🔥" : "💤"}</span>
          <div>
            <div class="pd-streak-num">${streak}</div>
            <div class="pd-streak-label">${streak === 1 ? "día seguido" : "días seguidos"}</div>
          </div>
          <div style="margin-left:auto;text-align:right;">
            <div style="font-size:1.1rem;font-weight:700;color:var(--text)">${wordCount}</div>
            <div style="font-size:0.78rem;color:var(--muted)">${wordCount === 1 ? "palabra" : "palabras"}</div>
          </div>
        </div>
      </div>

      <!-- Nivel -->
      <div>
        <p class="pd-section-title">Nivel estimado</p>
        <div class="pd-level-row">
          <div class="pd-level-badge cefr-${level.key}">${level.label}</div>
          <div class="pd-level-info">
            <div class="pd-level-desc">${level.desc}</div>
            <div class="pd-bar-wrap"><div class="pd-bar" style="width:${pct}%"></div></div>
            <p class="pd-bar-next">${level.next ? `${toNext} palabra${toNext !== 1 ? "s" : ""} para ${CEFR_LEVELS.find(l => l.min === level.next)?.label || ""}` : "Nivel máximo alcanzado 🎉"}</p>
          </div>
        </div>
      </div>

      <!-- Modos -->
      <div>
        <p class="pd-section-title">Ejercicios completados · ${totalEx} en total</p>
        <div class="pd-modes">
          ${modeData.map(m => `
            <div class="pd-mode">
              <div class="pd-mode-icon" style="--mc:${m.color}"><i class="bi ${m.icon}"></i></div>
              <div class="pd-mode-count">${stats[m.key] || 0}</div>
              <div class="pd-mode-label">${m.label}</div>
            </div>
          `).join("")}
        </div>
      </div>
    `;
  };

  const setupProgressTrigger = () => {
    createProgressDrawer();

    document.querySelectorAll("[data-progress-trigger]").forEach((btn) => {
      btn.addEventListener("click", () => {
        renderProgressDrawer();
        toggleProgressDrawer(true);
      });
    });
  };

  const setupProfileLangChips = () => {
    const container = document.getElementById("langChips");
    if (!container) return;
    const activeLang = getActiveLang();
    const hist = getLangHistory().sort((a, b) => a.firstAt - b.firstAt);
    if (!hist.length) {
      container.innerHTML = `<span style="font-size:0.85rem;color:var(--muted)">Aún no has estudiado ningún idioma.</span>`;
      return;
    }
    container.innerHTML = hist.map(({ lang }) => {
      const cfg = LANG_CONFIG[lang];
      if (!cfg) return "";
      const isActive = lang === activeLang;
      return `<span class="profile-lang-chip${isActive ? " is-active" : ""}">
        <img src="${cfg.flag}" alt="${cfg.label}">
        ${cfg.label}${isActive ? " <span style=\"font-size:0.7rem;opacity:0.65\">· activo</span>" : ""}
      </span>`;
    }).join("");
  };

  setupObserver();
  setupNavToggle();
  setupScrollButton();
  setupKeyboardShortcut();
  setupActionButtons();
  setupCartTriggers();
  setupUtilityMenu();
  setupProgressTrigger();
  setupLangDropdown();
  setupAddToCartButtons();
  setupCartPageEvents();
  setupSaveDropdown();
  setupLibrarySearch();
  setupLibraryList();
  setupLibraryImport();
  updateCartBadges();
  setupProfileLangChips();

  // Tooltips sutiles en el header
  const headerTooltips = [
    { selector: '[data-progress-trigger]', label: 'Mi progreso' },
    { selector: '[data-lang-trigger]',     label: 'Cambiar idioma' },
    { selector: 'a.icon-btn[href="perfil.html"]', label: 'Mi perfil' },
    { selector: '[data-cart-trigger]',     label: 'Mi lista de repaso' },
    { selector: '[data-utility-trigger]',  label: 'Más opciones' },
  ];
  headerTooltips.forEach(({ selector, label }) => {
    const el = document.querySelector(selector);
    if (!el) return;
    el.setAttribute('data-tooltip', label);
  });

});

window.liquidPageLoaded = false;
window.liquidIsElementor = window.liquidIsElementor || document.body.classList.contains("elementor-page");
window.liquidElements = (i) => {
  window.$liquidWindow = i(window);
  window.$liquidHtml = i("html");
  window.$liquidBody = i("body");
  window.$liquidSiteWrap = i("#wrap");
  window.$liquidContents = i("#lqd-site-content");
  window.$liquidContentsWrap = i("#lqd-contents-wrap");
  window.$liquidTitlebar = i(".titlebar");
  window.$liquidMainHeader = i(".main-header, .elementor-location-header");
  window.$liquidMainFooter = i(".main-footer, .elementor-location-footer");
  window.$liquidSectionsWrapper = $liquidContentsWrap;
  const l = $liquidBody.hasClass("single-liquid-portfolio");
  const d = $liquidBody.hasClass("lqd-blog-post");
  if (liquidIsElementor) {
    const e = i(".elementor-section-wrap", $liquidContentsWrap).first();
    window.$liquidSectionsWrapper = e.legth ? e : i("> .elementor", $liquidContentsWrap).first();
  }
  if (l) {
    window.$liquidSectionsWrapper = i(".pf-single-contents");
    const e = window.$liquidSectionsWrapper.children(".elementor");
    if (e.length) {
      window.$liquidSectionsWrapper = e;
    }
  }
  if (d) {
    const e = i(".lqd-single-post-content > .container");
    window.$liquidSectionsWrapper = e.length ? e : window.$liquidSectionsWrapper;
    const n = window.$liquidSectionsWrapper.children(".elementor");
    if (n.length) {
      window.$liquidSectionsWrapper = n;
    }
  }
  const h = `
	> .elementor-section-wrap > .elementor-section,
	> .elementor-section,
	> .e-con,
	> .e-con > .e-con,
	> .e-con > .e-con-inner > .e-con,
	> .e-container,
	> .e-container > .e-container,
	> .elementor-section-wrap > .elementor-top-section > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-inner-section,
	> .elementor-top-section > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-inner-section`;
  const t = i("> .elementor", $liquidMainFooter).find(h);
  window.$liquidSections = liquidIsElementor ? $liquidTitlebar.add($liquidSectionsWrapper.find(h)).add(t) : $liquidSectionsWrapper.add($liquidMainFooter).find(".lqd-section, > .vc_row, > .vc_section, > .vc_section > .vc_row, > .lqd-section-scroll-sections > .vc_row, > .vc_element");
  if ((l || d) && liquidIsElementor) {
    window.$liquidSections = window.$liquidSections.add(window.$liquidSectionsWrapper.find("> .elementor").find(h));
  }
  if (d && liquidIsElementor) {
    window.$liquidSections = i(window.$liquidSections.get()).add(".lqd-post-cover");
    if (i(".lqd-single-post-content > .container").length) {
      window.$liquidSections = i(window.$liquidSections.get()).add(window.$liquidContents);
    }
  }
  if (!window.$liquidSections.length) {
    window.$liquidSections = $liquidSectionsWrapper.find("> section").add(window.$liquidMainFooter?.find("> section"));
  }
  window.liquidBodyBg = window.$liquidBody.css("backgroundColor");
  window.liquidContentsBg = window.$liquidContents.css("backgroundColor");
  window.liquidMainFooterBg = window.$liquidMainFooter.css("backgroundColor");
};
liquidElements(jQuery);
window.liquidHeaderIsElementor = $liquidMainHeader.children(".elementor:not(.lqd-mobile-sec)").length;
window.liquidLazyloadEnabled = $liquidBody.hasClass("lazyload-enabled");
window.liquidCheckedFonts = [];
window.liquidAvailableLightboxes = [];
if ("getLiquidActiveBreakpoint" in window) {
  window.liquidIsMobile = function() {
    const i = getLiquidActiveBreakpoint();
    return i === "tablet" || i === "mobile" || i === "mobile_extra";
  };
} else {
  window.liquidIsMobile = function() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || navigator.platform === "MacIntel" && navigator.maxTouchPoints > 0 || navigator.platform === "iPad";
  };
}
if (liquidIsMobile()) {
  let i = "mobile";
  if ("getLiquidActiveBreakpoint" in window) {
    i = getLiquidActiveBreakpoint();
  }
  document.documentElement.classList.add("vc_mobile");
  document.body.setAttribute("data-elementor-device-mode", i);
}
window.liquidMobileNavBreakpoint = function() {
  if (window.liquidParams && window.liquidParams.mobileNavBreakpoint) {
    return window.liquidParams.mobileNavBreakpoint;
  } else {
    return $liquidBody.data("mobile-nav-breakpoint") || 1199;
  }
};
window.liquidWindowWidth = function() {
  return window.innerWidth;
};
window.liquidWindowHeight = function() {
  return window.innerHeight;
};
window.liquidDocHeight = function() {
  return document.body.clientHeight;
};
window.liquidSlugify = function(i) {
  return String(i).normalize("NFKD").replace(/[\u0300-\u036f]/g, "").trim().toLowerCase().replace(/[^a-z0-9 -]/g, "").replace(/\s+/g, "-").replace(/-+/g, "-");
};
const L = function(i, l) {
  l = l == null ? i.length - 1 : +l;
  return function() {
    var d = Math.max(arguments.length - l, 0), h = Array(d), t = 0;
    for (;t < d; t++) {
      h[t] = arguments[t + l];
    }
    switch (l) {
      case 0:
        return i.call(this, h);
      case 1:
        return i.call(this, arguments[0], h);
      case 2:
        return i.call(this, arguments[0], arguments[1], h);
    }
    var e = Array(l + 1);
    for (t = 0;t < l; t++) {
      e[t] = arguments[t];
    }
    e[l] = h;
    return i.apply(this, e);
  };
};
const B = L(function(i, l, d) {
  return setTimeout(function() {
    return i.apply(null, d);
  }, l);
});
const D = Date.now || function() {
  return new Date().getTime();
};
window.liquidThrottle = function(i, l, d) {
  var h, t, e, n;
  var s = 0;
  if (!d)
    d = {};
  var o = function() {
    s = d.leading === false ? 0 : D();
    h = null;
    n = i.apply(t, e);
    if (!h)
      t = e = null;
  };
  var a = function() {
    var r = D();
    if (!s && d.leading === false)
      s = r;
    var c = l - (r - s);
    t = this;
    e = arguments;
    if (c <= 0 || c > l) {
      if (h) {
        clearTimeout(h);
        h = null;
      }
      s = r;
      n = i.apply(t, e);
      if (!h)
        t = e = null;
    } else if (!h && d.trailing !== false) {
      h = setTimeout(o, c);
    }
    return n;
  };
  a.cancel = function() {
    clearTimeout(h);
    s = 0;
    h = t = e = null;
  };
  return a;
};
window.liquidDebounce = function(i, l, d) {
  var h, t;
  var e = function(s, o) {
    h = null;
    if (o)
      t = i.apply(s, o);
  };
  var n = L(function(s) {
    if (h)
      clearTimeout(h);
    if (d) {
      var o = !h;
      h = setTimeout(e, l);
      if (o)
        t = i.apply(this, s);
    } else {
      h = B(e, l, this, s);
    }
    return t;
  });
  n.cancel = function() {
    clearTimeout(h);
    h = null;
  };
  return n;
};
window.liquidGetMousePos = (i, l) => {
  let d = 0;
  let h = 0;
  if (!i)
    i = window.event;
  if (i.pageX || i.pageY) {
    d = i.pageX;
    h = i.pageY;
  } else if (i.clientX || i.clientY) {
    d = i.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
    h = i.clientY + document.body.scrollTop + document.documentElement.scrollTop;
  }
  if (l) {
    const t = i.currentTarget.getBoundingClientRect();
    d = d - t.left - window.scrollX;
    h = h - t.top - window.scrollY;
  }
  return { x: d, y: h };
};
(function(i) {
  let l;
  const d = (h) => {
    l = i(h).find('a[rel="prev"]').attr("href");
  };
  if (!("liquidTheme" in window))
    return;
  if (liquidTheme?.options?.blogInfiniteScroll?.enabled) {
    const h = i("#lqd-site-content");
    l = h.find('a[rel="prev"]').attr("href");
    h.infiniteScroll({
      path: function() {
        return l;
      },
      append: "#lqd-contents-wrap",
      history: liquidTheme.options.blogInfiniteScroll.history ? "push" : false,
      historyTitle: liquidTheme.options.blogInfiniteScroll.history,
      hideNav: ".post-nav"
    });
    h.on("append.infiniteScroll", function(t, e, n, s, o) {
      window.liquidLazyload.update();
      window.Sharer.init();
    });
    h.on("load.infiniteScroll", function(t, e) {
      d(e);
    });
    if (liquidTheme.options.blogInfiniteScroll.history && liquidTheme.options.blogInfiniteScroll.ga && typeof ga === "function") {
      h.on("history.infiniteScroll", function() {
        ga("set", "page", location.pathname);
        ga("send", "pageview");
      });
    }
  }
})(jQuery);
(function(i) {
  const l = i(".products");
  const d = () => {
    l.attr("class", function(t, e) {
      return e.replace(/\bcolumns-\S+/g, "").trim();
    });
  };
  const h = () => {
    const t = (l.attr("class").split(/\s+/).find((e) => e.startsWith("columns-")) || "").match(/\d+/)?.[0];
    return t * 1;
  };
  if (l.length) {
    let t = l.width();
    let e = Math.floor(t / 200);
    i(window).on("resize", function() {
      t = l.width();
      e = Math.floor(t / 200);
    });
    i(".lqd-woo-grid-col-changer button").on("click", function() {
      const n = i(this);
      const s = n.data("col-change");
      if (s === "increase") {
        const o = h();
        const a = o + 1;
        if (a <= e) {
          d();
          l.addClass("columns-" + a);
        }
      }
      if (s === "decrease") {
        const o = h();
        const a = o - 1;
        if (a >= 1) {
          d();
          l.addClass("columns-" + a);
        }
      }
    });
  }
})(jQuery);
(function(i) {
  let l = false;
  i(".ld-currency-switcher li a").on("click", function(d) {
    d.preventDefault();
    if (l) {
      return;
    }
    const h = i(this);
    const t = h.attr("href").replace("#", "");
    i.ajax({
      url: liquidTheme.uris.ajax,
      method: "POST",
      dataType: "json",
      data: {
        action: "liquid_multicurrency_switch_currency",
        _ajax_nonce: wcLiquid.nonce,
        currency: t
      },
      beforeSend: () => {
        l = true;
        h.parents("ul").addClass("ld-loading");
      },
      success: (e) => {
        if (e.success) {
          window.location.reload();
        } else {
          l = false;
          h.parents("ul").removeClass("ld-loading");
        }
      }
    });
  });
})(jQuery);
(function(i) {
  i(window).on("elementor/frontend/init", function() {
    i(".lqd-woo-shoppable-video__yt").each(function() {
      const l = i(this);
      const d = l.closest("[data-lqd-flickity]");
      if (d.length) {
        return;
      }
      l.YTPlayer();
      l.YTPPlay();
    });
    i(".lqd-woo-shoppable-video__media video").each(function() {
      const l = i(this);
      const d = l.closest("[data-lqd-flickity]");
      if (d.length) {
        return;
      }
      l.length && l.get(0).play();
    });
  });
  i("[data-lqd-flickity]").each(function() {
    const l = i(this);
    const d = l.find(".carousel-item");
    l.on("change.flickity", function(h, t) {
      const e = d.eq(t);
      d.find("video").each(function() {
        i(this).get(0).pause();
      });
      const n = d.find(".lqd-woo-shoppable-video__yt");
      if (n.length) {
        d.find(".lqd-woo-shoppable-video__yt").each(function() {
          i(this).YTPPause();
        });
        e.find(".lqd-woo-shoppable-video__yt").YTPPlay();
      }
      const s = e.find("video").get(0);
      s && s.play();
    });
    l.on("ready.flickity", function(h, t) {
      const e = l.find(".carousel-item");
      const n = l.find(".carousel-item.is-selected");
      const s = n.find("video").get(0);
      s && s.play();
      const o = l.find(".lqd-woo-shoppable-video__yt");
      if (o.length) {
        l.find(".lqd-woo-shoppable-video__yt").YTPlayer({
          onReady: function() {
            e.find(".lqd-woo-shoppable-video__yt").each(function() {
              i(this).YTPPause();
            });
            l.find(".lqd-woo-shoppable-video__yt").YTPPlay();
          }
        });
      }
    });
  });
})(jQuery);
(function(i) {
  i(document.body).on("wc_fragments_refreshed wc_fragments_loaded", function() {
    const l = i(".ld-empty-cart-suggest");
    if (l.length) {
      l.find("[data-lqd-flickity]").liquidCarousel();
    }
  });
})(jQuery);

class A {
  constructor(i, l, d = {}) {
    this.el = i;
    this.opts = d;
    this.setupIO(l);
  }
  setupIO(i) {
    new IntersectionObserver(([l], d) => {
      if (l.isIntersecting && i) {
        if (this.opts.disconnect) {
          d.disconnect();
        }
        i();
      }
    }, { ...this.opts }).observe(this.el);
  }
}

class _ {
  constructor() {
    this.sections = [];
    this.footerBg = tinycolor(liquidMainFooterBg).getAlpha() === 0 ? liquidBodyBg : liquidMainFooterBg;
  }
  static getInstance() {
    if (!this.instance) {
      this.instance = new _;
    }
    return this.instance;
  }
  static getDetails(i = false) {
    const l = this.getInstance();
    return new Promise(async (d) => {
      if (l.sections.length < 1 || i) {
        l.sections = [];
        const h = await l.getElementRect({ element: $liquidContents[0] });
        await Promise.all(l.init(h, l));
        const t = l.sections.filter((e) => e.isInMainContent);
        if (t.length < 1) {
          const e = {
            element: $liquidContents[0],
            $element: $liquidContents
          };
          await l.createDetailsObj(h, h, e, true).then((n) => {
            l.sections.unshift(n);
          });
        }
        l.addParentSections(l);
        l.addInnerSections(l);
        await l.addLuminosity(l);
      }
      d(l.sections);
    });
  }
  init(i, l) {
    const d = [];
    $liquidSections.each((h, t) => {
      const e = new Promise((n) => {
        const s = {
          element: t,
          $element: jQuery(t),
          parent: t.parentElement
        };
        this.getElementRect(s).then((o) => {
          this.createDetailsObj(i, o, s, false).then((a) => {
            l.sections[h] = a;
            n(a);
          });
        });
      });
      d.push(e);
    });
    return d;
  }
  getElementRect(i) {
    return new Promise((l) => {
      new IntersectionObserver(([d], h) => {
        fastdom.measure(() => {
          h.disconnect();
          l(d.boundingClientRect);
        });
      }).observe(i.element);
    });
  }
  createDetailsObj(i, l, d, h) {
    return new Promise((t) => {
      fastdom.measure(async () => {
        const { scrollY: e, scrollX: n } = window;
        const s = getComputedStyle(d.element);
        const o = {};
        o.el = d.element;
        o.$el = d.$element;
        o.rect = {
          initialOffset: {
            x: l.x + n,
            y: l.y + e
          },
          width: l.width,
          height: l.height,
          x: l.x,
          y: l.y
        };
        o.backgroundColor = s.backgroundColor;
        if (h) {
          o.isMainContentElement = true;
          return t(o);
        }
        const a = d.element.closest(".main-footer");
        const r = d.$element.parents(".e-container, .e-con");
        o.borderColor = s.borderColor;
        o.isOuterSection = liquidIsElementor ? d.element.classList.contains("elementor-top-section") || !!!r.length : d.element.classList.contains("vc_section") || d.element.parentElement.closest(".lqd-section") == null;
        o.isInnerSection = liquidIsElementor ? d.element.classList.contains("elementor-inner-section") || !!r.length : d.parent.classList.contains("vc_section") || d.element.parentElement.closest(".lqd-section") != null;
        o.isInFooter = a != null;
        o.isInMainContent = d.element.closest("#lqd-site-content") != null;
        o.isHidden = o.rect.width < 1 && o.rect.height < 1;
        o.predefinedLuminosity = null;
        o.parentSection = null;
        o.innerSections = [];
        if (o.el.hasAttribute("data-section-luminosity")) {
          o.predefinedLuminosity = o.el.getAttribute("data-section-luminosity");
        }
        if (o.isInFooter) {
          o.parentFooter = a;
          if (a.hasAttribute("data-sticky-footer")) {
            const c = i.height;
            const p = document.body.offsetHeight - (i.y + e) - i.height;
            const g = Math.abs(window.innerHeight - p - o.rect.y);
            o.rect.initialOffset.y = c + g;
            o.rect.y = c + g;
          }
        }
        t(o);
      });
    });
  }
  addParentSections(i) {
    const l = i.sections.filter((d) => d.isInnerSection);
    l.forEach((d) => {
      let h = null;
      if (liquidIsElementor) {
        h = d.el.closest(".elementor-top-section") || d.$el.parents(".e-container") || d.$el.parents(".e-con");
      } else {
        h = d.el.closest(".vc_section") || d.el.parentElement.closest(".lqd-section");
      }
      i.sections.forEach((t) => {
        if (t.el === h) {
          d.parentSection = t;
        }
      });
    });
  }
  addInnerSections(i) {
    const l = i.sections.filter((d) => d.isInnerSection);
    i.sections.forEach((d, h) => {
      if (d.isInnerSection)
        return;
      l.forEach((t) => {
        if (t.parentSection && t.parentSection.el === d.el) {
          i.sections[h].innerSections.push(t);
        }
      });
    });
  }
  getLuminosity(i, l) {
    let { backgroundColor: d } = i;
    if (i.isInnerSection && i.parentSection && tinycolor(d).getAlpha() === 0) {
      d = i.parentSection.backgroundColor;
    }
    if (tinycolor(d).getAlpha() === 0) {
      if (i.isInFooter) {
        d = l.footerBg;
      } else {
        d = window.liquidContentsBg;
      }
    }
    return tinycolor(d).isDark() ? "dark" : "light";
  }
  async addLuminosity(i) {
    i.sections.forEach(async (l) => {
      l.isBgTransparent = tinycolor(l.backgroundColor).getAlpha() === 0;
      l.luminosity = l.predefinedLuminosity ? l.predefinedLuminosity : i.getLuminosity(l, i);
      await fastdomPromised.mutate(() => {
        l.el.setAttribute("data-section-luminosity", l.luminosity);
      });
    });
  }
}
(function(i) {
  const l = "liquidPreloader";
  let d = {
    animationType: "fade",
    animationTargets: "self",
    dir: "x",
    stagger: 0,
    duration: 1400
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = i.extend({}, d, e);
      this.element = t;
      this.$element = i(t);
      this.animationTargets = this.getAnimationTargets();
      this.onPreloaderHiddenEvent = new CustomEvent("lqd-preloader-anim-done");
      this.onPageLoad();
    }
    getAnimationTargets() {
      const { animationTargets: t } = this.options;
      if (t === "self") {
        return this.element;
      } else {
        return document.querySelectorAll(t);
      }
    }
    getAnimationProperties() {
      const { animationType: t } = this.options;
      return this[`${t}Properties`]();
    }
    fadeProperties() {
      const t = {
        opacity: [0, 1]
      };
      const e = {
        opacity: [1, 0]
      };
      return {
        animateIn: t,
        animateOut: e
      };
    }
    slideProperties() {
      const { dir: t } = this.options;
      const e = {
        [t]: ["100%", "0%"]
      };
      const n = {
        [t]: ["0%", "-100%"]
      };
      return {
        animateIn: e,
        animateOut: n
      };
    }
    scaleProperties() {
      const t = {
        [`scale${this.options.dir.toUpperCase()}`]: [0, 1]
      };
      const e = {
        [`scale${this.options.dir.toUpperCase()}`]: [1, 0]
      };
      return {
        animateIn: t,
        animateOut: e
      };
    }
    onPageLoad() {
      $liquidBody.addClass("lqd-page-loaded lqd-preloader-animations-started");
      $liquidBody.removeClass("lqd-page-leaving lqd-page-not-loaded");
      this.hidePreloader();
    }
    hidePreloader() {
      const t = this.options.stagger / 1000;
      const e = this.options.duration / 1000;
      const n = gsap.timeline({
        duration: e,
        ease: "expo.out",
        stagger: t,
        onComplete: () => {
          this.$element.hide();
          $liquidBody.removeClass("lqd-preloader-animations-started");
          $liquidBody.addClass("lqd-preloader-animations-done");
          i(this.animationTargets).css("transform", "");
          document.dispatchEvent(this.onPreloaderHiddenEvent);
        }
      });
      i(this.animationTargets).each((s, o) => {
        const a = i(o);
        if (o.hasAttribute("data-animations")) {
          const r = a.data("animations");
          n.to(o, {
            ...r
          }, t * s);
        } else {
          const r = this.getAnimationProperties().animateOut;
          n.fromTo(o, {
            [Object.keys(r)[0]]: Object.values(r)[0][0]
          }, {
            [Object.keys(r)[0]]: Object.values(r)[0][1]
          }, t * s);
        }
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("preloader-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
$liquidWindow.on("pageshow load", (i) => {
  if ("elementorFrontend" in window && elementorFrontend.isEditMode())
    return;
  liquidPageLoaded || jQuery(".lqd-preloader-wrap").liquidPreloader();
  liquidPageLoaded = true;
});
((i) => {
  const l = "liquidSmoothScroll";
  let d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.init();
    }
    init() {
      document.documentElement.style.scrollBehavior = "auto";
      if (!("Lenis" in window)) {
        return console.error("Couldn't find Lenis.", this);
      }
      window.liquidLenis = new Lenis({
        ...this.options
      });
      if ("ScrollTrigger" in window) {
        liquidLenis.on("scroll", ScrollTrigger.update);
      }
      if ("gsap" in window) {
        gsap.ticker.add((t) => {
          liquidLenis.raf(t * 1000);
        });
        gsap.ticker.lagSmoothing(0);
      }
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-scroll-smoother=true]").liquidSmoothScroll();
});
(function(i) {
  const l = "liquidSubmenu";
  let d = {
    toggleType: "fade",
    handler: "mouse-in-out",
    animationSpeed: 200
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = { ...d, ...e };
      this._defaults = d;
      this._name = l;
      this.rects = [];
      this.windowWidth = fastdom.measure(liquidWindowWidth)();
      this.itemsAreFullwidth = this.element.classList.contains("lqd-menu-items-block");
      this.$submenuParents = i(this.$element.find(".menu-item-has-children, .page_item_has_children").get().reverse());
      this.$elementorHeaderWidgets = liquidIsElementor && $liquidMainHeader.find("> .elementor:not(.lqd-mobile-sec) > .elementor-section-wrap > .elementor-section, > .elementor:not(.lqd-mobile-sec) > .elementor-section, > .elementor:not(.lqd-mobile-sec) > .e-con").not(".lqd-stickybar-wrap").find('> .elementor-container > .elementor-column > .elementor-widget-wrap > [data-element_type="widget"]');
      this.reactToMegamenuEnabled = $liquidMainHeader.attr("data-react-to-megamenu") === "true";
      this.init();
    }
    init() {
      const { handler: t } = this.options;
      this.$submenuParents.each(async (e, n) => {
        const s = n.classList.contains("megamenu");
        const o = n.querySelector(".nav-item-children, .children");
        const a = n.querySelector("a");
        if (!o)
          return;
        if (t === "click") {
          this.element.classList.add("lqd-submenu-toggle-click");
        } else if (t === "mouse-in-out") {
          this.element.classList.add("lqd-submenu-toggle-hover");
        }
        if (!a.hasAttribute("href")) {
          a.setAttribute("href", "#");
        }
        if ((!liquidIsMobile() || !i(n).is(":hidden")) && !this.itemsAreFullwidth) {
          if (!s) {
            await this.measure(e, o, n);
          } else {
            this.getMegamenuBackgroundLuminance(n);
          }
        } else {
          n.classList.add("position-applied");
        }
        if (!liquidIsMobile()) {
          this.eventHandlers(e, o, n);
        }
      });
      i(document).on("click", this.closeActiveSubmenu.bind(this));
      i(document).keyup((e) => {
        if (e.keyCode == 27) {
          this.closeActiveSubmenu(e);
        }
      });
    }
    async measure(t, e, n) {
      await this.getRects(t, e, n);
      await this.positioning(t, e, n);
    }
    eventHandlers(t, e, n) {
      const { handler: s } = this.options;
      const o = i(n).children("a");
      if (s === "click") {
        o.off();
        o.on("click", this.handleToggle.bind(this, "toggle"));
      } else {
        i(n).off();
        i(n).on("mouseenter", this.handleToggle.bind(this, "show"));
        i(n).on("mouseleave", this.handleToggle.bind(this, "hide"));
        o.on("focus", this.handleToggle.bind(this, "show"));
        o.on("blur", this.handleToggle.bind(this, "hide"));
      }
      document.addEventListener("lqd-header-sticky-change", async () => {
        await this.measure(t, e, n);
      });
      return this;
    }
    handleToggle(t, e) {
      const { toggleType: n, handler: s } = this.options;
      const o = i(e.currentTarget);
      const a = o.closest("li");
      const r = a.children(".nav-item-children, .children");
      const c = a.hasClass("megamenu");
      const p = c && a.attr("data-bg-color");
      const g = c && a.attr("data-megamenu-bg-scheme");
      if (!r.length)
        return;
      e.preventDefault();
      const u = t === "show" || !a.hasClass("is-active");
      if (n === "fade") {
        if (t === "show") {
          this.fadeIn(r, c, g, p);
        } else if (t === "hide") {
          this.fadeOut(r, c);
        } else if (t === "toggle") {
          this.fadeToggle(r, c, g, p);
        }
      }
      if (n === "slide") {
        if (t === "show") {
          this.slideDown(r, c, g, p);
        } else if (t === "hide") {
          this.slideUp(r, c);
        } else if (t === "toggle") {
          this.slideToggle(r, c, g, p);
        }
      }
      a.toggleClass(`is-active ${s === "mouse-in-out" ? "is-hovered" : ""}`, u).siblings().removeClass(`is-active ${s === "mouse-in-out" ? "is-hovered" : ""}`);
      document.dispatchEvent(new CustomEvent("lqd-submenu-change", {
        bubbles: false,
        detail: { state: u ? "show" : "hide", $submenu: r }
      }));
    }
    fadeToggle(t, e, n, s) {
      const { handler: o } = this.options;
      const a = t.closest("li");
      if (o === "click") {
        t.css({
          visibility: "visible",
          opacity: a.hasClass("is-active") ? 0 : 1
        });
        t[a.hasClass("is-active") ? "show" : "hide"]();
        a.siblings().find(".nav-item-children, .children").stop().fadeOut(this.options.animationSpeed);
        t.stop().fadeToggle(this.options.animationSpeed);
      }
      if (this.reactToMegamenuEnabled) {
        $liquidMainHeader.removeClass("megamenu-scheme-dark megamenu-scheme-light megamenu-scheme-transparent");
        $liquidMainHeader.toggleClass(`megamenu-scheme-${n}`, e);
        if (this.$elementorHeaderWidgets && n !== "transparent" && !$liquidMainHeader.hasClass("is-stuck")) {
          this.$elementorHeaderWidgets.removeClass(`lqd-active-row-dark lqd-active-row-light`);
          this.$elementorHeaderWidgets.addClass(`lqd-active-row-${n}`);
        }
      }
      $liquidMainHeader.toggleClass(`megamenu-item-active`, e);
      if (e) {
        $liquidMainHeader[0].style.setProperty("--lqd-megamenu-background-color", s);
      }
    }
    fadeIn(t, e, n, s) {
      const { handler: o } = this.options;
      const a = t.closest("li");
      if (o === "click") {
        t.css({
          visibility: "visible",
          display: "none",
          opacity: 0
        });
        a.siblings().find(".nav-item-children, .children").stop().fadeOut(this.options.animationSpeed);
        t.stop().fadeIn(this.options.animationSpeed);
      }
      if (e) {
        if (this.reactToMegamenuEnabled) {
          if (this.$elementorHeaderWidgets && n !== "transparent" && !$liquidMainHeader.hasClass("is-stuck")) {
            this.$elementorHeaderWidgets.removeClass(`lqd-active-row-dark lqd-active-row-light`);
            this.$elementorHeaderWidgets.addClass(`lqd-active-row-${n}`);
          }
          $liquidMainHeader.removeClass("megamenu-scheme-dark megamenu-scheme-light megamenu-scheme-transparent");
          $liquidMainHeader.addClass(`megamenu-scheme-${n}`);
        }
        $liquidMainHeader.addClass(`megamenu-item-active`);
        $liquidMainHeader[0].style.setProperty("--lqd-megamenu-background-color", s);
      }
      if (t.find("[data-lqd-flickity]").length) {
        t.find("[data-lqd-flickity]").flickity("resize");
      }
    }
    fadeOut(t, e) {
      const { handler: n } = this.options;
      const s = t.closest("li");
      if (n === "click") {
        t.css({
          visibility: "visible",
          display: "block",
          opacity: s.hasClass("is-active") ? 1 : 0
        });
        s.siblings().find(".nav-item-children, .children").stop().fadeOut(this.options.animationSpeed);
        t.stop().fadeOut(this.options.animationSpeed);
      }
      if (e) {
        if (this.reactToMegamenuEnabled) {
          if (this.$elementorHeaderWidgets) {
            this.$elementorHeaderWidgets.removeClass(`lqd-active-row-dark lqd-active-row-light`);
          }
          $liquidMainHeader.removeClass("megamenu-scheme-dark megamenu-scheme-light megamenu-scheme-transparent");
        }
        $liquidMainHeader.removeClass("megamenu-item-active");
      }
    }
    slideToggle(t, e, n, s) {
      t.closest("li").siblings().find(".nav-item-children, .children").stop().slideUp(this.options.animationSpeed);
      t.stop().slideToggle(this.options.animationSpeed);
      if (e) {
        if (this.reactToMegamenuEnabled) {
          if (this.$elementorHeaderWidgets && n !== "transparent" && !$liquidMainHeader.hasClass("is-stuck")) {
            this.$elementorHeaderWidgets.removeClass(`lqd-active-row-dark lqd-active-row-light`);
            this.$elementorHeaderWidgets.addClass(`lqd-active-row-${n}`);
          }
          $liquidMainHeader[0].style.setProperty("--lqd-megamenu-background-color", s);
          $liquidMainHeader.removeClass("megamenu-scheme-dark megamenu-scheme-light megamenu-scheme-transparent");
          $liquidMainHeader.toggleClass(`megamenu-scheme-${n}`);
        }
        $liquidMainHeader.toggleClass(`megamenu-item-active`);
      }
    }
    slideDown(t, e, n, s) {
      t.closest("li").siblings().find(".nav-item-children, .children").stop().slideUp(this.options.animationSpeed);
      t.stop().slideDown(this.options.animationSpeed);
      if (e) {
        if (this.reactToMegamenuEnabled) {
          if (this.$elementorHeaderWidgets && n !== "transparent" && !$liquidMainHeader.hasClass("is-stuck")) {
            this.$elementorHeaderWidgets.removeClass(`lqd-active-row-dark lqd-active-row-light`);
            this.$elementorHeaderWidgets.addClass(`lqd-active-row-${n}`);
          }
          $liquidMainHeader[0].style.setProperty("--lqd-megamenu-background-color", s);
          $liquidMainHeader.removeClass("megamenu-scheme-dark megamenu-scheme-light megamenu-scheme-transparent");
          $liquidMainHeader.addClass(`megamenu-scheme-${n}`);
        }
        $liquidMainHeader.addClass(`megamenu-item-active`);
      }
    }
    slideUp(t, e) {
      t.stop().slideUp(this.options.animationSpeed);
      if (e) {
        if (this.reactToMegamenuEnabled) {
          if (this.$elementorHeaderWidgets) {
            this.$elementorHeaderWidgets.removeClass(`lqd-active-row-dark lqd-active-row-light`);
          }
          $liquidMainHeader.removeClass("megamenu-scheme-dark megamenu-scheme-light megamenu-scheme-transparent");
        }
        $liquidMainHeader.removeClass("megamenu-item-active");
      }
    }
    getMegamenuBackgroundLuminance(t) {
      const e = t.querySelector(".lqd-megamenu-rows-wrap");
      let n;
      fastdom.measure(() => {
        const s = getComputedStyle(e);
        n = tinycolor(s.backgroundColor);
      });
      fastdom.mutate(() => {
        t.setAttribute("data-bg-color", n);
        if (n.getAlpha() === 0) {
          return t.setAttribute("data-megamenu-bg-scheme", "transparent");
        }
        if (n.isLight()) {
          return t.setAttribute("data-megamenu-bg-scheme", "light");
        }
        if (n.isDark()) {
          return t.setAttribute("data-megamenu-bg-scheme", "dark");
        }
      });
    }
    closeActiveSubmenu(t) {
      const { toggleType: e } = this.options;
      if (t.keyCode) {
        this.$submenuParents.each((n, s) => {
          const o = i(s);
          o.removeClass("active, is-active");
          this[e === "fade" ? "fadeOut" : "slideUp"](o.children(".nav-item-children, .children"), o.hasClass("megamenu"));
        });
        document.dispatchEvent(new CustomEvent("lqd-submenu-change", { bubbles: false, detail: { state: "hide", $submenu: null } }));
      } else {
        this.$submenuParents.each(async (n, s) => {
          const o = i(s);
          const a = o.children("a");
          if (!o.hasClass("is-active"))
            return;
          if (!a.is(t.target) && !o.is(t.target) && !o.has(t.target).length) {
            o.removeClass("active, is-active");
            this[e === "fade" ? "fadeOut" : "slideUp"](o.children(".nav-item-children, .children"), o.hasClass("megamenu"));
            document.dispatchEvent(new CustomEvent("lqd-submenu-change", {
              bubbles: false,
              detail: { state: "hide", $submenu: null }
            }));
          }
        });
      }
    }
    async getRects(t, e, n) {
      this.rects[t] = {
        submenuRect: {},
        subParentRect: {}
      };
      return fastdomPromised.measure(() => {
        return new Promise((s) => {
          new IntersectionObserver(([o], a) => {
            const { boundingClientRect: r } = o;
            a.disconnect();
            s(r);
          }).observe(e);
        });
      }).then((s) => {
        this.rects[t].submenuRect = s;
        return new Promise((o) => {
          new IntersectionObserver(([a], r) => {
            const { boundingClientRect: c } = a;
            r.disconnect();
            o(c);
          }).observe(n);
        });
      }).then((s) => {
        this.rects[t].subParentRect = s;
      });
    }
    positioning(t, e, n) {
      return fastdomPromised.mutate(() => {
        const s = this.rects[t].submenuRect;
        const o = this.rects[t].subParentRect;
        if (s.left + s.width >= this.windowWidth) {
          e.classList.add("to-left");
        }
        n.style.setProperty("--item-height", `${o.height}px`);
        n.classList.add("position-applied");
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("submenu-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".main-nav, .lqd-custom-menu").liquidSubmenu();
});
(function(i) {
  const l = "liquidMobileNav";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.$mobileSec = i(".lqd-mobile-sec", $liquidMainHeader);
      this.$mobileSecInner = i(".lqd-mobile-sec-inner", this.$mobileSec);
      this.$mobileNavCollapse = i(".mobile-navbar-collapse", this.$mobileSec);
      this.$submenuExpanders = i(".submenu-expander");
      this.isModernMobileNav = document.body.hasAttribute("data-mobile-nav-style") && document.body.getAttribute("data-mobile-nav-style") === "modern";
      this.submenuTriggerBehavior = document.body.getAttribute("data-mobile-submenu-trigger-behavior") ?? "default";
      this.$navItems = [];
      this.init();
    }
    init() {
      this.mobileExpander();
      this.eventHandlers();
      if (document.body.hasAttribute("data-mobile-header-builder"))
        return;
      this.removeStyleTags();
      this.addHeightVar();
      this.mobileModules();
      if ("elementorFrontend" in window && !elementorFrontend.isEditMode()) {
        this.$mobileSec.addClass("elementor");
      }
    }
    mobileExpander() {
      this.$submenuExpanders.attr("role", "button");
      this.$submenuExpanders.attr("tabindex", 0);
      this.$mobileNavCollapse.add(".nav-item-children", this.$mobileNavCollapse).add(".nav-item-children", i(".ld-sd-inner")).css({
        display: "block",
        visibility: "hidden"
      });
      this.$submenuExpanders.each((t, e) => {
        const n = i(e);
        const s = n.parent();
        const o = s.css("paddingTop");
        const a = n.css("position");
        if (a === "absolute") {
          n.css({
            top: (s.height() + parseInt(o, 10)) / 2 - 18
          });
        }
        n.insertAfter(s);
      });
      this.$mobileNavCollapse.add(".nav-item-children", this.$mobileNavCollapse).add(".nav-item-children", i(".ld-sd-inner")).css({
        display: "",
        visibility: ""
      });
    }
    eventHandlers() {
      if (this.submenuTriggerBehavior === "expand-first") {
        $liquidBody.off("click.mobileMenuExpandFirst keypress.mobileMenuExpandFirst", ".main-header .menu-item-has-children > a").on("click.mobileMenuExpandFirst keypress.mobileMenuExpandFirst", ".main-header .menu-item-has-children > a", (t) => {
          const e = t.currentTarget;
          const n = e?.closest("li");
          const s = n?.querySelector(".nav-item-children");
          if (!e || !n || !s || !liquidIsMobile() || t.code && t.code !== "Enter" && t.code !== "Space" && t.code !== "NumpadEnter")
            return;
          const o = s.getBoundingClientRect();
          if (o.height === 0) {
            t.preventDefault();
            t.stopPropagation();
            n.querySelector(":scope > .submenu-expander")?.click();
            return;
          }
        });
      }
      $liquidBody.off("click keypress", ".submenu-expander").on("click keypress", ".submenu-expander", (t) => {
        if (t.code && t.code !== "Enter" && t.code !== "Space" && t.code !== "NumpadEnter")
          return;
        t.preventDefault();
        const e = i(t.currentTarget).closest("li");
        const n = e.children(".nav-item-children, .children");
        const s = e.closest(".navbar-collapse-inner");
        const o = e.hasClass("is-active");
        e.toggleClass("is-active");
        e.siblings().removeClass("is-active").find(".nav-item-children, .children").stop().slideUp(200);
        n.stop().slideToggle(300, () => {
          if (this.isModernMobileNav && !o && s.length) {
            s.animate({
              scrollTop: s.scrollTop() + (e.offset().top - s.offset().top)
            });
          }
        });
      });
      this.$mobileNavCollapse.on("hide.bs.collapse", this.closeAllSubmenus.bind(this));
    }
    removeStyleTags() {
      fastdom.mutate(() => {
        this.$mobileSec.find(".navbar-collapse style[data-type=vc_shortcodes-custom-css]").remove();
      });
    }
    addHeightVar() {
      fastdom.mutate(() => {
        if (this.$mobileSecInner.length) {
          document.documentElement.style.setProperty("--lqd-mobile-sec-height", `${this.$mobileSecInner[0].offsetHeight}px`);
        }
      });
    }
    mobileModules() {
      const t = i(".lqd-show-on-mobile", this.element);
      if (!t.length)
        return;
      const e = i(".lqd-mobile-modules-container", this.$mobileSec);
      fastdom.mutate(() => {
        e.removeClass("empty");
        t.each((n, s) => {
          const o = i(s);
          if (!o.children().length)
            return false;
          const a = o.clone(true);
          const r = i("[data-target]", a);
          if (r.length) {
            const c = r.attr("data-target");
            const p = i(c, a);
            p.attr({
              id: `${c.replace("#", "")}-cloned`
            });
            r.attr({
              "data-target": `${c}-cloned`,
              "aria-controls": `${c.replace("#", "")}-cloned`
            });
            r.attr({
              "data-bs-target": `${c}-cloned`,
              "aria-controls": `${c.replace("#", "")}-cloned`
            });
            p.on("show.bs.collapse", () => {
              p.add(r).addClass("is-active");
            });
            p.on("hide.bs.collapse", () => {
              p.add(r).removeClass("is-active");
            });
          }
          a.appendTo(e);
          if (!a.hasClass("header-module")) {
            a.wrap('<div class="header-module" />');
          }
        });
      });
    }
    closeAllSubmenus() {
      const t = this.$mobileNavCollapse.find(".menu-item-has-children");
      t.each((e, n) => {
        const s = i(n);
        const o = s.find(".nav-item-children");
        s.stop().removeClass("is-hovered is-active");
        o.stop().slideUp(50);
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("mobilenav-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function() {
  $liquidMainHeader.liquidMobileNav();
});
(function(i) {
  const l = "liquidButton";
  let d = {};

  class h {
    constructor(t, e) {
      this.element = t;
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.init();
    }
    init() {
      this.gradientBorderRoundness();
    }
    gradientBorderRoundness() {
      const t = this;
      const e = i(t.element);
      if (e.find(".btn-gradient-border").length && e.hasClass("circle") && e.is(":visible")) {
        const n = e.find(".btn-gradient-border").children("rect");
        const s = e.height();
        n.attr({
          rx: s / 2,
          ry: s / 2
        });
      }
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("plugin-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {});
(function(i) {
  const l = "liquidFitText";
  let d = {
    compressor: 1,
    minFontSize: Number.NEGATIVE_INFINITY,
    maxFontSize: Number.POSITIVE_INFINITY
  };
  function h(t, e) {
    this.element = t;
    this.options = i.extend({}, d, e);
    this._defaults = d;
    this._name = l;
    this.init();
  }
  h.prototype = {
    init() {
      this.setMinFontSize();
      this.setMaxFontSize();
      this.resizer();
      this.onWindowResize();
    },
    setMinFontSize() {
      const t = this.options.minFontSize;
      const e = i(this.element).css("fontSize");
      if (t == "currentFontSize") {
        this.options.minFontSize = e;
      }
    },
    setMaxFontSize() {
      const t = this.options.maxFontSize;
      const e = i(this.element).css("fontSize");
      if (t == "currentFontSize") {
        this.options.maxFontSize = e;
      }
    },
    resizer() {
      const t = this.options;
      const e = t.compressor;
      const n = t.maxFontSize;
      const s = t.minFontSize;
      const o = i(this.element);
      const a = o.parent(".ld-fancy-heading").length ? o.parent().width() : o.width();
      o.css("font-size", Math.max(Math.min(a / (e * 10), parseFloat(n)), parseFloat(s)));
    },
    onWindowResize() {
      i(window).on("resize.fittext orientationchange.fittext", this.resizer.bind(this));
    }
  };
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("fittext-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-fittext]").liquidFitText();
});
jQuery(document).ready(function(i) {
  if (liquidLazyloadEnabled) {
    const l = liquidParams?.lazyLoadOffset;
    const d = l >= 0 ? l : 500;
    window.liquidLazyload = new LazyLoad({
      elements_selector: ".ld-lazyload",
      threshold: d,
      callback_loaded: (h) => {
        const t = i(h);
        const e = t.closest("[data-liquid-masonry=true]");
        const n = t.closest(".flex-viewport");
        const s = t.closest("[data-webglhover]");
        const o = t.closest("[data-reveal]");
        t.parent().not("#wrap, #lqd-site-content").addClass("loaded");
        t.closest("[data-responsive-bg=true]").liquidResponsiveBG();
        if (e.length && e.data("isotope")) {
          e.isotope("layout");
        }
        if (n.length && n.parent().data("flexslider")) {
          n.height(t.height());
        }
        if (s.length && !liquidIsMobile()) {
          s.liquidWebGLHover();
        }
        if (o.length) {
          o.liquidReveal();
        }
      }
    });
    jQuery(document).on("elementor/popup/show", () => {
      window.liquidLazyload.update();
    });
  }
});
(function(i) {
  const l = "liquidInView";
  let d = {
    delayTime: 0,
    onImagesLoaded: false,
    toggleBehavior: "stay"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = i.extend({}, d, e);
      this.element = t;
      this.$element = i(t);
      this.$sentinel = null;
      this.isVisible = false;
      this.imagesAlreadyLoaded = false;
      this.isFixedPos = fastdom.measure(() => this.$element.css("position") === "fixed")();
      this.windowWidth = fastdom.measure(liquidWindowWidth)();
      this.windowHeight = fastdom.measure(liquidWindowHeight)();
      this.initIO();
    }
    initIO() {
      const { toggleBehavior: t } = this.options;
      new IntersectionObserver(([e], n) => {
        fastdomPromised.measure(() => {
          return {
            boundingClientRect: e.boundingClientRect,
            scrollY: window.scrollY
          };
        }).then(({ boundingClientRect: s, scrollY: o }) => {
          fastdom.mutate(() => {
            if (t === "toggleInView") {
              if (o + this.windowHeight >= s.top + o) {
                this.isVisible = true;
                this.callFns();
              } else if (o <= s.bottom + o) {
                this.isVisible = false;
                this.callFns();
              }
            }
            if (e.isIntersecting && t === "stay") {
              n.disconnect();
              this.isVisible = true;
              this.callFns();
            } else if (!e.isIntersecting && t === "toggleOutOfView") {
              this.onOutOfView();
            }
          });
        });
      }, { threshold: t === "toggleInView" ? [0, 0.25, 0.5, 0.75, 1] : [0] }).observe(!this.isFixedPos ? this.element : this.$element.parent()[0]);
    }
    callFns() {
      if (!this.options.onImagesLoaded && !this.imagesAlreadyLoaded) {
        this.run();
      } else {
        imagesLoaded(this.element, () => {
          this.imagesAlreadyLoaded = true;
          this.run();
        });
      }
    }
    run() {
      const { delayTime: t } = this.options;
      t <= 0 ? this.onInView() : this.timeoutId = setTimeout(this.onInView.bind(this), t);
    }
    onInView() {
      this.$element.toggleClass("is-in-view", this.isVisible);
      clearTimeout(this.timeoutId);
    }
    onOutOfView() {
      const { toggleBehavior: t } = this.options;
      if (t === "toggleOutOfView") {
        this.isVisible = false;
      }
      if (!this.isVisible) {
        this.$element.removeClass("is-in-view");
      }
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("inview-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (!$liquidContents.length) {
    return i("[data-inview]").liquidInView();
  }
  const l = () => {
    if ($liquidBody.hasClass("lqd-preloader-activated")) {
      document.addEventListener("lqd-preloader-anim-done", () => {
        i("[data-inview]").liquidInView();
      });
    } else {
      i("[data-inview]").liquidInView();
    }
  };
  if (!$liquidContents[0].hasAttribute("data-liquid-stack")) {
    l();
  } else {
    const d = $liquidContents.attr("data-stack-options");
    if (d) {
      const h = JSON.parse(d);
      const { disableOnMobile: t } = h;
      if (t && (liquidIsMobile() || liquidWindowWidth() <= liquidMobileNavBreakpoint())) {
        l();
      }
    }
  }
});
(function(i) {
  const l = "liquidFullscreenNav";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.$element = i(t);
      this.init();
    }
    init() {
      this.DOM.$element.children(".header-modules-container").find(".lqd-head-col").removeClass("lqd-head-col");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("fullscreen-nav-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".navbar-fullscreen").liquidFullscreenNav();
});
(function(i) {
  const l = i("body");
  const d = "liquidToggle";
  let h = {
    type: "click",
    cloneTriggerInTarget: false,
    closeOnOutsideClick: true,
    toggleDelay: 300
  };

  class t {
    constructor(e, n) {
      this.element = e;
      this.$element = i(e);
      this.options = { ...h, ...n };
      this._defaults = h;
      this._name = d;
      this.$targetElement = i(this.$element.attr("data-target") || this.$element.attr("data-bs-target"));
      this.$parentElement = this.$element.parent();
      this.isInVerticalBar = this.$element.closest(".lqd-stickybar-wrap").length;
      this.isSearchModule = this.$parentElement.hasClass("ld-module-search");
      this.isCartModule = this.$parentElement.hasClass("ld-module-cart");
      this.$clonedTrigger = null;
      this.isOpened = false;
      this.isInHeader = $liquidMainHeader.has(this.element).length;
      this.windowWidth = fastdom.measure(liquidWindowWidth)();
      this.targetRect = {};
      this.isBS5 = typeof bootstrap !== "undefined";
      if (this.$element.hasClass("lqd-custom-menu-dropdown-btn") && this.$element.parents(".header-module").length) {
        this.options.type = "hoverFade";
      }
      this.init();
    }
    async init() {
      const e = this.$targetElement.not(".navbar-collapse");
      if (!this.isInVerticalBar && e.length && !liquidIsMobile()) {
        e[0].classList.add("positioning");
        await this.measure(e[0]);
        await this.positioning(e[0]);
      }
      this.addBodyClassnames();
      this.eventHandlers();
      this.cloneTriggerInTarget();
      this.cloneTargetInBody();
    }
    measure(e) {
      return fastdomPromised.measure(() => {
        return new Promise((n) => {
          new IntersectionObserver(([s], o) => {
            o.disconnect();
            n(s.boundingClientRect);
          }).observe(e);
        });
      }).then((n) => {
        this.targetRect = n;
      });
    }
    positioning(e) {
      return fastdomPromised.mutate(() => {
        if (this.targetRect.width + this.targetRect.left >= this.windowWidth) {
          e.classList.remove("left");
          e.classList.add("right");
        }
        if (this.targetRect.left < 0) {
          e.classList.remove("right");
          e.classList.add("left");
        }
        e.classList.remove("positioning");
      });
    }
    addBodyClassnames() {
      if (this.$parentElement[0].hasAttribute("data-module-style")) {
        l.addClass(this.$parentElement.attr("data-module-style"));
      }
    }
    eventHandlers() {
      const { type: e } = this.options;
      if (e === "hover") {
        this.$element.on("mouseenter", () => {
          this.$targetElement.collapse("show");
        });
        this.$element.add(this.$targetElement).on("mouseleave", () => {
          this.$targetElement.collapse("hide");
        });
      } else if (e === "hoverFade" && !liquidIsMobile()) {
        fastdom.mutate(() => {
          let n = false;
          this.$targetElement.addClass("lqd-dropdown-fade-onhover");
          this.$element.add(this.$targetElement).on("mouseenter", () => {
            this.$targetElement.addClass("is-active");
            this.$targetElement.trigger("shown.bs.collapse");
            n && clearTimeout(n);
          });
          this.$element.add(this.$targetElement).on("mouseleave", () => {
            n = setTimeout(() => {
              this.$targetElement.removeClass("is-active");
              this.$targetElement.trigger("hidden.bs.collapse");
              n && clearTimeout(n);
            }, this.options.toggleDelay);
          });
        });
      }
      if (!this.isBS5) {
        this.$targetElement.on("show.bs.collapse", this.onShow.bind(this));
        this.$targetElement.on("shown.bs.collapse", this.onShown.bind(this));
        this.$targetElement.on("hide.bs.collapse", this.onHide.bind(this));
        this.$targetElement.on("hidden.bs.collapse", this.onHidden.bind(this));
      } else {
        this.$targetElement.each((n, s) => {
          s.addEventListener("show.bs.collapse", this.onShow.bind(this));
          s.addEventListener("shown.bs.collapse", this.onShown.bind(this));
          s.addEventListener("hide.bs.collapse", this.onHide.bind(this));
          s.addEventListener("hidden.bs.collapse", this.onHidden.bind(this));
        });
      }
      i(document).on("click", (n) => {
        this.closeAll.call(this, n);
      });
      i(document).on("keyup", (n) => {
        if (n.key === "Escape") {
          this.closeAll.call(this, n);
        }
      });
      if (this.isInHeader) {
        document.addEventListener("lqd-header-sticky-visibility-change", (n) => {
          if (this.isOpened && n.detail.state === "hide") {
            this.$targetElement.collapse("hide");
          }
        });
      }
    }
    onShow(e) {
      const n = this.$element.attr("data-target") || this.$element.attr("data-bs-target");
      i("html").addClass("module-expanding");
      if (this.isSearchModule) {
        i("html").addClass("lqd-module-search-expanded");
      } else if (this.isCartModule) {
        i("html").addClass("lqd-module-cart-expanded");
      }
      this.$targetElement.add(this.element).add(this.$clonedTrigger).addClass("is-active");
      if (n.replace("#", "") === i(e.target).attr("id")) {
        this.toggleClassnames();
        this.focusOnSearch();
      }
      this.isOpened = true;
      const s = setTimeout(() => {
        this.$targetElement.add(this.element).add(this.$clonedTrigger).removeClass("collapsed");
        this.$targetElement.removeClass("collapse");
        if (!this.options.changeClassnames && this.$targetElement.hasClass("navbar-fullscreen")) {
          $liquidHtml.addClass("overflow-hidden");
        }
        s && clearTimeout(s);
      }, 10);
    }
    onShown() {
      i("html").removeClass("module-expanding");
      if (window.liquidLazyload) {
        window.liquidLazyload.update();
      }
    }
    onHide(e) {
      const n = this.$element.attr("data-target") || this.$element.attr("data-bs-target");
      i("html").addClass("module-collapsing");
      this.$targetElement.add(this.element).add(this.$clonedTrigger).removeClass("is-active");
      if (n.replace("#", "") === i(e.target).attr("id")) {
        this.toggleClassnames();
      }
      this.isOpened = false;
      const s = setTimeout(() => {
        this.$targetElement.add(this.element).add(this.$clonedTrigger).addClass("collapsed");
        this.$targetElement.addClass("collapse");
        if (!this.options.changeClassnames && this.$targetElement.hasClass("navbar-fullscreen")) {
          $liquidHtml.removeClass("overflow-hidden");
        }
        s && clearTimeout(s);
      }, 10);
    }
    onHidden() {
      i("html").removeClass("module-collapsing lqd-module-search-expanded lqd-module-cart-expanded");
    }
    toggleClassnames() {
      i.each(this.options.changeClassnames, (e, n) => {
        i(e).toggleClass(n, !this.isOpened);
      });
    }
    focusOnSearch() {
      const e = this;
      if (e.$targetElement.find("input[type=search]").length) {
        setTimeout(function() {
          e.$targetElement.find("input[type=search]").focus().select();
        }, 150);
      }
    }
    shouldIGetClosed(e) {
      const { closeOnOutsideClick: n } = this.options;
      if (typeof n === "boolean") {
        return n;
      } else {
        const { ifNotIn: s } = n;
        const o = i(s);
        return !o.has(e).length ? true : false;
      }
    }
    closeAll(e) {
      const { closeOnOutsideClick: n } = this.options;
      const s = this.shouldIGetClosed(this.$targetElement);
      if (e.keyCode || !this.$targetElement.is(e.target) && !this.$targetElement.has(e.target).length) {
        if (n && s || typeof n === "boolean" && n) {
          if (this.isBS5 && this.$targetElement.length) {
            new bootstrap.Collapse(this.$targetElement[0], {
              toggle: false,
              show: false,
              hide: true
            });
          }
          this.$targetElement.collapse("hide");
        }
      }
    }
    cloneTriggerInTarget() {
      if (this.$targetElement.attr("id") === "lqd-mobile-sec-nav" && l.attr("data-mobile-nav-style") === "modern" || this.options.cloneTriggerInTarget || this.$targetElement.hasClass("navbar-fullscreen")) {
        this.$clonedTrigger = this.$element.clone(true).prependTo(this.$targetElement);
      }
    }
    cloneTargetInBody() {
      if (this.$targetElement.attr("id") === "lqd-mobile-sec-nav" && l.attr("data-mobile-nav-style") === "modern") {
        this.$targetElement.children(".main-nav, .header-module").wrapAll('<div class="navbar-collapse-inner"></div>');
      }
    }
  }
  i.fn[d] = function(e) {
    return this.each(function() {
      const n = { ...i(this).data("toggle-options"), ...e };
      if (!i.data(this, "plugin_" + d)) {
        i.data(this, "plugin_" + d, new t(this, n));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-ld-toggle]").liquidToggle();
});
(function(i) {
  const l = "liquidResponsiveBG";
  let d = {};

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.targetImage = null;
      this.targetImage = this.element.querySelector("img");
      this.init();
    }
    init() {
      if (typeof this.targetImage === "undefined" || this.targetImage === null) {
        console.error("There should be an image to get the source from it.");
        return false;
      }
      this.setBgImage();
      imagesLoaded(this.targetImage).on("done", this.onLoad.bind(this));
    }
    getCurrentSrc() {
      let t = this.targetImage.currentSrc ? this.targetImage.currentSrc : this.targetImage.src;
      if (/data:image\/svg\+xml/.test(t)) {
        t = this.targetImage.dataset.src;
      }
      return t;
    }
    setBgImage() {
      this.$element.css({
        backgroundImage: `url( ${this.getCurrentSrc()} )`
      });
    }
    reInitparallaxBG() {
      const t = this.$element.children(".lqd-parallax-container").find(".lqd-parallax-figure");
      if (t.length) {
        t.css({
          backgroundImage: `url( ${this.getCurrentSrc()} )`
        });
      }
    }
    onLoad() {
      this.reInitparallaxBG();
      this.$element.addClass("loaded");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("responsive-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-responsive-bg=true]").filter((l, d) => {
    return !d.querySelector(".ld-lazyload");
  }).liquidResponsiveBG();
});
(function(i) {
  const l = $liquidContents.length && $liquidContents[0].getAttribute("data-liquid-bg-options");
  const d = l && (JSON.parse(l).interactWithHeader === true || JSON.parse(l).interactWithHeader === "true");
  const h = "liquidStickyHeader";
  let t = {
    stickyTrigger: "this",
    dynamicColors: false,
    disableOnMobile: false,
    smartSticky: false
  };

  class e {
    constructor(n, s) {
      this._defaults = t;
      this._name = h;
      this.options = { ...t, ...s };
      this.DOM = {
        element: n,
        $element: i(n),
        $stickySections: null,
        sentinel: null,
        placeholder: null,
        $stickyElements: null
      };
      this.DOM.$stickySections = liquidIsElementor ? i("> .elementor-section-wrap > .elementor-section, > .elementor-section, > .e-container, > .e-con", this.DOM.$element.children(".elementor:not(.lqd-mobile-sec)")).not(".lqd-hide-onstuck, .lqd-stickybar-wrap") : i(".lqd-head-sec-wrap", n).not(".lqd-hide-onstuck");
      this.isInTitlebar = this.DOM.element.parentElement.classList.contains("titlebar");
      this.isOverlay = this.DOM.$element.css("position") === "absolute";
      this.DOM.sentinel = null;
      this.DOM.placeholder = this.DOM.$element.prev(".lqd-sticky-placeholder")[0];
      this.DOM.$stickyElements = this.getStickyElements();
      this.firstRow = document.body.classList.contains("single-post") ? document.querySelector(".lqd-post-cover") : $liquidSections.filter(":visible").first()[0];
      this.firstRowIsSticky = this.firstRow ? getComputedStyle(this.firstRow).position === "sticky" : false;
      this.stickyElsDetails = null;
      this.stickySectionsHeight = 0;
      this.smartStickyStuff = {
        state: null,
        prevScrollY: 0,
        scrolledDistance: 0,
        tolerance: {
          up: 25,
          down: 3
        },
        toleranceExceeded: false
      };
      this.wasStuck = false;
      this.isStuck = false;
      this.onResize = liquidDebounce(this.onResize.bind(this), 100);
      this.init();
    }
    async init() {
      await this.addSentinel();
      !this.DOM.placeholder && await this.addPlaceholder();
      await this.getStickySectionsHeight();
      await this.getStickyElsDetails();
      this.DOM.element.setAttribute("data-sticky-values-measured", "true");
      this.sentinelIO();
      this.headerIO();
      this.addStickySectionsHeight();
      this.initDynamicColors();
      this.handleResizeEvents();
      this.eventListeners();
    }
    eventListeners() {
      document.addEventListener("lqd-header-sticky-change", (n) => {
        const s = n.detail.stuck;
        this.updateStickyStates(s);
        this.isStuck && this.addStickySectionsHeight();
      });
      if (this.options.smartSticky) {
        $liquidWindow.on("scroll.lqdSmartStickyHeader", this.handleSmartSticky.bind(this));
      }
    }
    updateStickyStates(n) {
      fastdom.mutate(() => {
        this.wasStuck = this.isStuck;
        this.isStuck = n;
        this.DOM.element.classList.toggle("is-stuck", this.isStuck);
        this.DOM.element.classList.toggle("is-not-stuck", !this.isStuck);
        if (!this.isOverlay || this.isInTitlebar) {
          this.DOM.placeholder.classList.toggle("d-none", !this.isStuck);
          this.DOM.placeholder.classList.toggle("hidden", !this.isStuck);
        }
        if (this.options.smartSticky) {
          if (!this.isStuck) {
            this.smartStickyStuff.state = null;
            this.DOM.element.classList.remove("lqd-smart-sticky-show", "lqd-smart-sticky-hide", "lqd-just-stuck");
          } else {
            this.DOM.element.classList.add("lqd-smart-sticky-hide");
            if (!this.wasStuck) {
              this.DOM.element.classList.add("lqd-just-stuck");
            }
          }
        }
      });
    }
    async getStickySectionsHeight() {
      const n = [];
      this.DOM.$stickySections.each((o, a) => {
        const r = new Promise((c) => {
          fastdom.measure(() => {
            new IntersectionObserver(([p], g) => {
              g.disconnect();
              c(p.boundingClientRect);
            }).observe(a);
          });
        });
        n.push(r);
      });
      const s = await Promise.all(n);
      s.forEach((o) => this.stickySectionsHeight += o.height);
    }
    addStickySectionsHeight() {
      const n = liquidIsElementor ? document.body : document.documentElement;
      fastdomPromised.mutate(() => {
        n.style.setProperty("--lqd-sticky-header-height", `${this.stickySectionsHeight}px`);
      });
    }
    addPlaceholder() {
      return fastdomPromised.mutate(() => {
        const n = document.createElement("div");
        n.setAttribute("class", "lqd-sticky-placeholder d-none");
        this.DOM.placeholder = n;
        this.DOM.element.before(n);
      });
    }
    addSentinel() {
      return fastdomPromised.mutate(() => {
        const n = document.querySelector("#lqd-temp-sticky-header-sentinel");
        const s = n.content.firstElementChild.cloneNode(true);
        const { stickyTrigger: o } = this.options;
        let a = document.body;
        if (o === "first-section") {
          const r = document.querySelector(".titlebar");
          if (r) {
            a = r;
          } else if (this.firstRow && !this.firstRow.closest(".main-footer")) {
            if (!this.firstRowIsSticky) {
              a = this.firstRow;
            }
          } else {
            this.options.stickyTrigger = "this";
          }
        }
        a.appendChild(s);
        this.DOM.sentinel = s;
      });
    }
    sentinelIO() {
      new IntersectionObserver(([n]) => {
        fastdom.measure(() => {
          let s = n.boundingClientRect;
          let o = n.rootBounds;
          if (!o) {
            o = {
              top: 0,
              bottom: window.innerHeight
            };
          }
          if (!this.isStuck && o && s.bottom < o.top) {
            this.fireEvent("stickyChange", true);
          } else if (this.isStuck && o && s.bottom >= o.top && s.bottom < o.bottom) {
            this.fireEvent("stickyChange", false);
          }
        });
      }).observe(this.DOM.sentinel);
    }
    headerIO() {
      const { stickyTrigger: n } = this.options;
      const s = liquidIsElementor ? document.body : document.documentElement;
      fastdomPromised.measure(() => {
        const o = {
          height: this.DOM.element.offsetHeight
        };
        return o;
      }).then(({ height: o }) => {
        fastdom.mutate(() => {
          s.style.setProperty("--lqd-sticky-header-placeholder-height", `${o}px`);
          if (n === "this") {
            s.style.setProperty("--lqd-sticky-header-sentinel-top", `var(--lqd-sticky-header-placeholder-height)`);
          } else {
            if (this.firstRowIsSticky) {
              s.style.setProperty("--lqd-sticky-header-sentinel-top", `${i(this.firstRow).outerHeight()}px`);
            }
          }
        });
      });
    }
    stickyVisibilityChange(n) {
      let s = ["lqd-smart-sticky-hide", "lqd-just-stuck"];
      let o = ["lqd-smart-sticky-show"];
      if (n === "hide") {
        s = ["lqd-smart-sticky-show"];
        o = ["lqd-smart-sticky-hide"];
      }
      this.smartStickyStuff.state = n;
      this.DOM.element.classList.remove(...s);
      this.DOM.element.classList.add(...o);
      this.fireEvent("stickyVisibility", n);
    }
    handleSmartSticky() {
      fastdomPromised.measure(() => {
        const { scrollY: n } = window;
        const s = n > this.smartStickyStuff.prevScrollY ? "down" : "up";
        return {
          scrollY: n,
          scrollDirection: s
        };
      }).then(({ scrollY: n, scrollDirection: s }) => {
        this.smartStickyStuff.scrolledDistance = Math.abs(n - this.smartStickyStuff.prevScrollY);
        fastdom.mutate(() => {
          if (this.isStuck && this.smartStickyStuff.toleranceExceeded) {
            if (s === "up" && this.smartStickyStuff.state !== "show") {
              this.stickyVisibilityChange("show");
            } else if (s === "down" && this.smartStickyStuff.state !== "hide") {
              this.stickyVisibilityChange("hide");
            }
          }
          this.smartStickyStuff.prevScrollY = n;
          this.smartStickyStuff.toleranceExceeded = this.smartStickyStuff.scrolledDistance > this.smartStickyStuff.tolerance[s];
        });
      });
    }
    fireEvent(n = "stickyChange", s) {
      fastdom.mutate(() => {
        if (n === "stickyChange") {
          document.dispatchEvent(new CustomEvent("lqd-header-sticky-change", {
            bubbles: false,
            detail: { stuck: s, target: this.DOM.element }
          }));
        }
        if (n === "stickyVisibility") {
          document.dispatchEvent(new CustomEvent("lqd-header-sticky-visibility-change", {
            bubbles: false,
            detail: { state: s, target: this.DOM.element }
          }));
        }
      });
    }
    getStickyElements() {
      const n = liquidIsElementor ? this.DOM.$element.find("> .elementor:not(.lqd-mobile-sec)").find('[data-element_type="widget"]').filter((o, a) => {
        return !a.classList.contains("elementor-widget-ld_modal_window") && !a.closest(".ld-module-sd") && !a.closest(".navbar-fullscreen") && !a.closest(".lqd-modal");
      }) : this.DOM.$element.find(".lqd-head-sec-wrap, .lqd-stickybar-wrap").find(".lqd-head-col > .header-module, [data-lqd-interactive-color=true]");
      const s = this.DOM.element.hasAttribute("data-liquid-bg") ? n.add(this.DOM.$element).not(".navbar-brand-solid") : n.add(this.DOM.$element);
      return s;
    }
    async getStickyElsDetails() {
      const n = [];
      const s = liquidWindowWidth();
      this.DOM.$stickyElements.each((a, r) => {
        const c = new Promise((p) => {
          new IntersectionObserver(([g], u) => {
            fastdom.measure(() => {
              u.disconnect();
              let { boundingClientRect: m } = g;
              let f = {};
              f.el = g.target;
              f.moduleEl = r;
              let { x: v, y, width: w, height: b } = m;
              if (v < 0) {
                v = 0;
              } else if (v >= s) {
                v = s - w - g.target.parentElement.offsetWidth;
              }
              if (y < 0) {
                y = y + window.scrollY;
              }
              f.rect = {
                width: w,
                height: b,
                x: v,
                y
              };
              f.currentColor = "default";
              p(f);
            });
          }).observe(r === this.DOM.element ? this.DOM.element : r);
        });
        n.push(c);
      });
      const o = await Promise.all(n);
      this.stickyElsDetails = o;
    }
    getSections(n) {
      let s = [];
      n.forEach((o) => {
        let a = o;
        if (o.isInnerSection) {
          a = o.parentSection;
          if (!a)
            return;
          const c = o.backgroundColor.replace(/, /g, ",").split(" ")[0];
          const p = a.backgroundColor.replace(/, /g, ",").split(" ")[0];
          const g = tinycolor(c).getAlpha();
          const u = tinycolor(p).getAlpha();
          if ((g !== 0 || o.predefinedLuminosity) && u === 0) {
            a = o;
            s = s.filter((m) => m.el !== a.parentSection.el);
          }
        }
        const r = s.some((c) => c.el === a.el);
        !r && s.push(a);
      });
      return s;
    }
    initDynamicColors(n = false) {
      if (!this.options.dynamicColors || d)
        return;
      _.getDetails(n).then((s) => {
        const o = s.filter((c) => !c.isHidden);
        const a = this.getSections(o);
        const r = liquidThrottle(this.onScroll.bind(this, a), 150, { leading: true });
        this.onScroll(a);
        $liquidWindow.off("scroll.lqdStickyHeader");
        $liquidWindow.on("scroll.lqdStickyHeader", r);
      });
    }
    onScroll(n) {
      for (let s = 0;s < n.length; s++) {
        fastdomPromised.measure(() => {
          const o = n[s];
          const a = { ...o.rect };
          a.y = a.initialOffset.y - window.scrollY;
          a.x = a.initialOffset.x - window.scrollX;
          for (let r = 0;r < this.stickyElsDetails.length; r++) {
            if (this.isCollide(this.stickyElsDetails[r].rect, a)) {
              this.changeAttrs(o, this.stickyElsDetails[r]);
            }
          }
        });
      }
    }
    changeAttrs(n, s) {
      const { luminosity: o } = n;
      const { moduleEl: a } = s;
      fastdom.mutate(() => {
        if (o === "light" && s.currentColor !== "dark") {
          s.currentColor = "dark";
          a.classList.add("lqd-active-row-light");
          a.classList.remove("lqd-active-row-dark");
        } else if (o === "dark" && s.currentColor !== "light") {
          s.currentColor = "light";
          a.classList.add("lqd-active-row-dark");
          a.classList.remove("lqd-active-row-light");
        }
      });
    }
    isCollide(n, s) {
      return !(n.y + n.height < s.y || n.y > s.y + s.height || n.x + n.width < s.x || n.x + n.width / 2 > s.x + s.width);
    }
    handleResizeEvents() {
      $liquidWindow.on("resize", this.onResize);
      i(document).on("lqd-masonry-layout-init", this.onResize);
    }
    onResize() {
      this.headerIO();
      this.initDynamicColors(true);
    }
    drawIndicators(n) {
      const s = i(`<div class="lqd-section-ind pos-abs pointer-events-none absolute" style="width: ${n.rect.width}px; height: ${n.rect.height}px; border: 3px solid red; top: ${n.rect.y}px; left: ${n.rect.x}px; z-index: 10;"><span style="display: inline-block; background: var(--color-primary); color: #fff; padding: 0.35em 1em;">${n.luminosity}</span></div>`);
      s.appendTo($liquidBody);
    }
  }
  i.fn[h] = function(n) {
    return this.each(function() {
      const s = { ...i(this).data("sticky-options"), ...n };
      if (s.disableOnMobile && liquidIsMobile())
        return;
      if (!i.data(this, "plugin_" + h)) {
        i.data(this, "plugin_" + h, new e(this, s));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  const l = i("[data-sticky-header]");
  if ($liquidContents.length) {
    const d = $liquidContents[0].hasAttribute("data-liquid-stack");
    const h = d && $liquidContents[0].hasAttribute("data-stack-options") && JSON.parse($liquidContents[0].getAttribute("data-stack-options")).disableOnMobile === true;
    if (!$liquidBody.hasClass("header-style-side") && (!d || d && liquidIsMobile() && h)) {
      l.liquidStickyHeader();
    } else if (d) {
      l.attr("data-sticky-values-measured", "true");
    }
  }
});
(function(i) {
  const l = "liquidStickyFooter";
  let d = {
    shadow: 0,
    parallax: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.footerHeight = 0;
      this.windowWidth = fastdom.measure(() => window.innerWidth)();
      this.windowHeight = fastdom.measure(() => window.innerHeight)();
      this.init();
    }
    measure() {
      return fastdomPromised.measure(() => {
        if (this.windowWidth < 768) {
          this.footerHeight = 0;
        } else {
          this.footerHeight = this.element.offsetHeight - 2;
          this.windowWidth = window.innerWidth;
          this.windowHeight = window.innerHeight;
        }
      });
    }
    init() {
      imagesLoaded(this.element, async () => {
        await this.measure();
        this.addMargin();
        this._addShadow();
        this._handleResize();
      });
    }
    addMargin() {
      fastdomPromised.mutate(() => {
        if (this.footerHeight >= this.windowHeight) {
          return this.$element.addClass("lqd-footer-cant-stick");
        }
      });
    }
    _addShadow() {
      const { shadow: t } = this.options;
      if (t > 0) {
        document.body.classList.add(`lqd-sticky-footer-shadow-${t}`);
      }
    }
    _handleResize() {
      const t = liquidDebounce(this._onResize.bind(this), 400);
      $liquidWindow.on("resize", t);
    }
    async _onResize() {
      await this.measure();
      this.addMargin();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("sticky-footer-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidIsMobile())
    return;
  i("[data-sticky-footer=true]").liquidStickyFooter();
});
(function(i) {
  const l = "liquidCustomCursor";
  let d = {
    outerCursorSpeed: 0.2,
    outerCursorHide: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.clientX = -100;
      this.clientY = -100;
      this.magneticCords = { x: 0, y: 0 };
      this.element = t;
      this.$element = i(t);
      this.initiated = false;
      this.innerCursor = document.querySelector(".lqd-cc--inner");
      this.outerCursor = document.querySelector(".lqd-cc--outer");
      this.dragCursor = document.querySelector(".lqd-cc-drag");
      this.exploreCursor = document.querySelector(".lqd-cc-explore");
      this.arrowCursor = document.querySelector(".lqd-cc-arrow");
      this.iconCursor = document.querySelector(".lqd-cc-custom-icon");
      this.cursorEls = document.querySelectorAll(".lqd-cc--el");
      this.extraCursors = document.querySelectorAll(".lqd-extra-cursor");
      this.lastMovedOtherEl = null;
      this.lastMovedOtherInnerEl = null;
      this.scaleOuterCursor = null;
      this.scaleOuterCursorX = null;
      this.scaleOuterCursorY = null;
      this.activeEl = null;
      const n = liquidIsElementor ? document.body : document.documentElement;
      const s = window.liquidParams?.ccOuterSize || getComputedStyle(n).getPropertyValue("--lqd-cc-size-outer");
      this.outerCursorSize = parseInt(s || 0, 10);
      this.activeCircleBg = window.liquidParams?.ccActiveCircleBg || getComputedStyle(document.body).getPropertyValue("--lqd-cc-active-bg");
      this.activeCircleBc = window.liquidParams?.ccActiveCircleBc || getComputedStyle(document.body).getPropertyValue("--lqd-cc-active-bc");
      this.innerQuickSetX = gsap.quickSetter(this.innerCursor, "x", "px");
      this.innerQuickSetY = gsap.quickSetter(this.innerCursor, "y", "px");
      this.outerQuickToX = gsap.quickTo(this.outerCursor, "x", { duration: this.options.outerCursorSpeed });
      this.outerQuickToY = gsap.quickTo(this.outerCursor, "y", { duration: this.options.outerCursorSpeed });
      this.extrasQuickToX = gsap.quickTo([...this.cursorEls, ...this.extraCursors], "x", { duration: 0.1 });
      this.extrasQuickToY = gsap.quickTo([...this.cursorEls, ...this.extraCursors], "y", { duration: 0.1 });
      this.init();
    }
    init() {
      if (this.options.outerCursorHide) {
        $liquidBody.addClass("lqd-cc-outer-hidden");
      }
      this.initCursor();
      this.initHovers();
      document.body.classList.add("lqd-cc-init");
    }
    initCursor() {
      const t = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
      document.addEventListener("mousemove", (e) => {
        this.clientX = e.clientX;
        this.clientY = e.clientY;
      });
      gsap.ticker.add(() => {
        if (!this.initiated) {
          this.initiated = true;
          this.fadeOutInnerCursor = false;
          this.fadeOutInnerCursor = false;
        }
        const e = 1 - Math.pow(1 - 0.4, gsap.ticker.deltaRatio());
        t.x += (this.clientX - t.x) * e;
        t.y += (this.clientY - t.y) * e;
        this.innerQuickSetX(t.x);
        this.innerQuickSetY(t.y);
        this.extrasQuickToX(this.clientX);
        this.extrasQuickToY(this.clientY);
        if (!this.isStuck && !this.isMagnetic) {
          this.outerQuickToX(this.clientX - this.outerCursorSize / 2);
          this.outerQuickToY(this.clientY - this.outerCursorSize / 2);
        }
        if (this.isStuck && this.activeEl) {
          const n = this.activeEl.getBoundingClientRect();
          let { left: s, top: o, width: a, height: r } = n;
          s += a / 2 - this.outerCursorSize / 2;
          o += r / 2 - this.outerCursorSize / 2;
          this.outerQuickToX(s);
          this.outerQuickToY(o);
        }
        if (this.isMagnetic) {
          this.outerQuickToX(this.magneticCords.x);
          this.outerQuickToY(this.magneticCords.y);
        }
        if (this.scaleOuterCursor) {
          gsap.to(this.outerCursor, {
            scaleX: () => this.scaleOuterCursorX || 1,
            scaleY: () => this.scaleOuterCursorY || 1
          });
        }
        if (this.fadeOutInnerCursor) {
          gsap.to(this.innerCursor, {
            opacity: 0
          });
        } else {
          gsap.to(this.innerCursor, {
            opacity: 1
          });
        }
        if (this.fadeOutOuterCursor) {
          gsap.to(this.outerCursor, {
            opacity: 0
          });
        } else {
          gsap.to(this.outerCursor, {
            opacity: 1
          });
        }
      });
    }
    initHovers() {
      document.addEventListener("lqd-carousel-initialized", (t) => {
        const { carouselData: e } = t.detail;
        const n = e.flickityData;
        if (n.nextButton) {
          this.initCarouselNavCursor([n.nextButton.element, n.prevButton.element]);
        }
        if (!n.options.draggable)
          return;
        n.on("dragMove", (s) => {
          this.clientX = s.clientX;
          this.clientY = s.clientY;
        });
        n.on("pointerDown", () => {
          this.cursorEls.forEach((s) => s.classList.add("lqd-carousel-pointer-down"));
        });
        n.on("pointerUp", () => {
          this.cursorEls.forEach((s) => s.classList.remove("lqd-carousel-pointer-down"));
        });
        this.initSolidCircles(n.viewport, this.dragCursor);
      });
      this.initPageLinksCursor();
      this.initExtraCursor();
      this.initSolidCircles(".lqd-cc-label-trigger", this.exploreCursor);
      this.initArrowCursor(".lqd-dist-gal-menu a, .lqd-imgtxt-slider-link, .lqd-af-slide__link ~ a");
      this.initCustomIconCursor(".lqd-cc-icon-trigger", "lqd-cc__active-icon");
      this.initButtonShapeCursors();
      this.initNavTriggerCursor();
      this.initMenuItemsCursor();
    }
    initPageLinksCursor() {
      const t = () => {
        gsap.to(this.innerCursor, {
          scale: 2.25,
          backgroundColor: this.activeCircleBg,
          ease: "power2.out"
        });
        gsap.to(this.outerCursor, {
          scale: 1.2,
          borderColor: this.activeCircleBc,
          ease: "power2.out"
        });
      };
      const e = () => {
        gsap.to(this.innerCursor, {
          scale: 1,
          backgroundColor: "",
          ease: "power2.out"
        });
        gsap.to(this.outerCursor, {
          scale: 1,
          borderColor: "",
          ease: "power2.out"
        });
      };
      const n = [...document.querySelectorAll("a")].filter((s) => {
        return !s.classList.contains("lqd-cc-icon-trigger") && !s.classList.contains("lqd-cc-label-trigger") && !s.classList.contains("lqd-imgtxt-slider-link") && !s.closest(".carousel-items") && !s.closest(".lqd-slsh-alt") && !s.closest(".lqd-dist-gal-menu") && !i(s).siblings(".lqd-af-slide__link").length;
      });
      n.forEach((s) => {
        s.addEventListener("mouseenter", t);
        s.addEventListener("mouseleave", e);
      });
    }
    initCarouselNavCursor(t) {
      const e = (s, o) => {
        this.activeEl = s;
        this.isStuck = true;
        this.scaleOuterCursor = true;
        this.fadeOutInnerCursor = true;
        gsap.to(this.outerCursor, {
          borderColor: o.borderColor,
          borderRadius: o.borderRadius
        });
        this.moveOtherElements(s, [...s.querySelectorAll("i"), ...s.querySelectorAll("svg")], true);
      };
      const n = () => {
        this.activeEl = null;
        this.isStuck = false;
        this.fadeOutInnerCursor = false;
        gsap.to(this.outerCursor, {
          borderColor: "",
          borderRadius: ""
        });
        this.moveOtherElements();
      };
      t.forEach((s) => {
        fastdomPromised.measure(() => {
          const o = getComputedStyle(s);
          return {
            navElStyle: o
          };
        }).then(({ navElStyle: o }) => {
          s.addEventListener("mouseenter", e.bind(this, s, o));
          s.addEventListener("mouseleave", n.bind(this));
          s.addEventListener("click", (a) => {
            const r = setTimeout(() => {
              if (s.disabled) {
                n();
              }
              clearTimeout(r);
            }, 10);
          });
        });
      });
    }
    initExtraCursor() {
      const t = (n) => {
        this.fadeOutInnerCursor = true;
        this.fadeOutOuterCursor = true;
        n.classList.add("lqd-is-active");
        gsap.to(n, {
          scale: 1,
          opacity: 1,
          duration: 0.65,
          ease: "expo.out"
        });
      };
      const e = (n) => {
        this.fadeOutInnerCursor = false;
        this.fadeOutOuterCursor = false;
        n.classList.remove("lqd-is-active");
        gsap.to(n, {
          scale: 0.15,
          opacity: 0,
          duration: 0.65,
          ease: "expo.out"
        });
      };
      this.extraCursors.forEach((n) => {
        const s = n.parentElement;
        s.addEventListener("mousemove", t.bind(this, n));
        s.addEventListener("mouseleave", e.bind(this, n));
      });
    }
    initSolidCircles(t, e) {
      if (!t)
        return;
      const n = () => {
        this.fadeOutInnerCursor = true;
        this.fadeOutOuterCursor = true;
        e.classList.add("lqd-is-active");
      };
      const s = () => {
        this.fadeOutInnerCursor = false;
        this.fadeOutOuterCursor = false;
        e.classList.remove("lqd-is-active");
      };
      const o = (a) => {
        a.addEventListener("mouseenter", n);
        a.addEventListener("mouseleave", s);
      };
      if (typeof t === "string") {
        document.querySelectorAll(t).forEach((a) => o(a));
      } else {
        o(t);
      }
    }
    initArrowCursor(t) {
      const e = () => {
        this.fadeOutInnerCursor = true;
        this.fadeOutOuterCursor = true;
        this.arrowCursor.classList.add("lqd-is-active");
      };
      const n = () => {
        this.fadeOutInnerCursor = false;
        this.fadeOutOuterCursor = false;
        this.arrowCursor.classList.remove("lqd-is-active");
      };
      const s = (o) => {
        o.addEventListener("mouseenter", e);
        o.addEventListener("mouseleave", n);
      };
      if (typeof t === "string") {
        document.querySelectorAll(t).forEach((o) => s(o));
      } else {
        s(t);
      }
    }
    initCustomIconCursor(t) {
      const e = (s) => {
        this.fadeOutInnerCursor = true;
        this.fadeOutOuterCursor = true;
        s && this.iconCursor.style.setProperty("--cc-icon-color", s);
        this.iconCursor.classList.add("lqd-is-active");
      };
      const n = () => {
        this.fadeOutInnerCursor = false;
        this.fadeOutOuterCursor = false;
        this.iconCursor.style.removeProperty("--cc-icon-color");
        this.iconCursor.classList.remove("lqd-is-active");
      };
      document.querySelectorAll(t).forEach((s) => {
        const o = s.getAttribute("data-cc-icon-color");
        s.addEventListener("mouseenter", e.bind(this, o));
        s.addEventListener("mouseleave", n);
      });
    }
    initButtonShapeCursors() {
      const t = (s, o) => {
        this.isMagnetic = true;
        this.scaleOuterCursor = true;
        this.fadeOutInnerCursor = true;
        gsap.to(this.outerCursor, {
          borderColor: o.borderColor,
          borderRadius: o.borderRadius
        });
        this.moveOtherElements(s, [...s.querySelectorAll("i"), ...s.querySelectorAll("svg")]);
      };
      const e = () => {
        this.isMagnetic = false;
        this.fadeOutInnerCursor = false;
        gsap.to(this.outerCursor, {
          borderColor: "",
          borderRadius: ""
        });
        this.moveOtherElements();
      };
      const n = document.querySelectorAll(".btn-icon-bordered, .btn-icon-solid");
      n.forEach((s) => {
        const o = s.querySelector(".btn-icon");
        const a = s.classList.contains("btn-disable-magnetic-icon");
        if (!o || a)
          return;
        fastdomPromised.measure(() => {
          const r = getComputedStyle(o);
          return {
            iconStyles: r
          };
        }).then(({ iconStyles: r }) => {
          s.addEventListener("mouseenter", t.bind(this, o, r));
          s.addEventListener("mouseleave", e.bind(this));
        });
      });
    }
    initNavTriggerCursor() {
      const t = (o, a, r) => {
        this.isMagnetic = true;
        r && (this.scaleOuterCursor = true);
        this.fadeOutInnerCursor = true;
        this.fadeOutOuterCursor = true;
        this.moveOtherElements(o, a);
      };
      const e = () => {
        this.isMagnetic = false;
        this.fadeOutInnerCursor = false;
        this.fadeOutOuterCursor = false;
        this.moveOtherElements();
      };
      const n = document.querySelectorAll(".nav-trigger");
      const s = document.querySelectorAll(".ld-module-trigger");
      n.forEach((o) => {
        const a = i(o);
        const r = o.querySelector(".bars");
        const c = o.querySelector(".bars-inner");
        const p = a.data("plugin_liquidToggle");
        if (p && p.options.type !== "hover") {
          o.addEventListener("mouseenter", t.bind(this, r, c, true));
          o.addEventListener("mouseleave", e);
        }
      });
      s.forEach((o) => {
        const a = i(o);
        const r = o.querySelector(".ld-module-trigger-txt");
        const c = o.querySelector(".ld-module-trigger-icon");
        const p = a.data("plugin_liquidToggle");
        if (p && p.options.type !== "hover") {
          o.addEventListener("mouseenter", () => {
            const g = !o.classList.contains("lqd-module-icon-plain") && !o.parentElement.classList.contains("ld-dropdown-menu");
            t(o, r, g);
            t(o, c, g);
          });
          o.addEventListener("mouseleave", e);
        }
      });
    }
    initMenuItemsCursor() {
      const t = (s) => {
        this.moveOtherElements(s);
      };
      const e = () => {
        this.moveOtherElements();
      };
      const n = document.querySelectorAll(".lqd-magnetic-items");
      n.forEach((s) => {
        const o = s?.querySelector("ul");
        const a = o?.querySelectorAll(":scope > li > a");
        a?.forEach((r) => {
          r.addEventListener("mouseenter", t.bind(this, r));
          r.addEventListener("mouseleave", e.bind(this));
        });
      });
    }
    moveOtherElements(t, e, n) {
      let s = null;
      if (!t) {
        this.lastMovedOtherEl && gsap.to(this.lastMovedOtherEl, {
          duration: 0.4,
          x: 0,
          y: 0,
          clearProps: "all"
        });
        this.lastMovedOtherInnerEl && gsap.to(this.lastMovedOtherInnerEl, {
          duration: 0.35,
          x: 0,
          y: 0,
          clearProps: "all"
        });
        this.scaleOuterCursor = null;
        this.scaleOuterCursorX = null;
        this.scaleOuterCursorY = null;
        $liquidWindow.off("mousemove.lqdCCMoveOthers");
        return;
      }
      this.lastMovedOtherEl = t;
      this.lastMovedOtherInnerEl = e;
      $liquidWindow.on("mousemove.lqdCCMoveOthers", (o) => {
        !s && (s = t.getBoundingClientRect());
        const a = {
          x: s.left + s.width / 2 - this.clientX,
          y: s.top + s.height / 2 - this.clientY
        };
        const r = Math.atan2(a.x, a.y);
        const c = Math.sqrt(a.x * a.x + a.y * a.y);
        this.magneticCords = {
          x: s.left + s.width / 2 - this.outerCursorSize / 2 - Math.sin(r) * c / 3,
          y: s.top + s.height / 2 - this.outerCursorSize / 2 - Math.cos(r) * c / 3
        };
        !n && gsap.to(t, {
          duration: 0.4,
          x: -(Math.sin(r) * c) / 8,
          y: -(Math.cos(r) * c) / 8
        });
        e && gsap.to(e, {
          duration: 0.35,
          x: -(Math.sin(r) * c) / 8,
          y: -(Math.cos(r) * c) / 8
        });
        if (this.scaleOuterCursor) {
          this.scaleOuterCursorX = s.width / this.outerCursorSize;
          this.scaleOuterCursorY = s.height / this.outerCursorSize;
        }
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("cc-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidIsMobile() || window.vc_iframe || "elementorFrontend" in window && elementorFrontend.isEditMode())
    return;
  const l = () => {
    const d = i("[data-lqd-custom-cursor]");
    d.each((h, t) => {
      const e = i(`<span class="lqd-extra-cursor pos-fix pointer-events-none"></span>`);
      i(t).prepend(e);
    });
  };
  l();
  i("[data-lqd-cc]").liquidCustomCursor();
});
(function(i) {
  const l = "liquidAccordion";
  let d = {};
  function h(t, e) {
    this.element = t;
    this.$element = i(t);
    this.isBS5 = typeof bootstrap !== "undefined";
    this.options = i.extend({}, d, e);
    this.$contents = i(".accordion-collapse", this.element);
    this.$triggers = i("[data-toggle=collapse]", this.element);
    this._defaults = d;
    this._name = l;
    this.init();
  }
  h.prototype = {
    init() {
      this.setHashOnLoad();
      this.eventHandlers();
    },
    setHashOnLoad() {
      const t = i(this.element);
      if (location.hash !== "" && t.find(location.hash).length) {
        const e = t.find(location.hash).closest(".accordion-item");
        e.find(location.hash).addClass("in");
        e.find(".accordion-heading").find("a").attr("aria-expanded", "true").removeClass("collapsed");
        e.siblings().find(".in").removeClass("in");
        e.siblings().find(".accordion-heading").find("a").attr("aria-expanded", "false").addClass("collapsed");
      }
    },
    eventHandlers() {
      if (!this.isBS5) {
        this.$contents.on("show.bs.collapse", this.onShow.bind(this));
        this.$contents.on("shown.bs.collapse", this.onShown.bind(this));
        this.$contents.on("hide.bs.collapse", this.onHide.bind(this));
      } else {
        this.$contents.each((t, e) => {
          e.addEventListener("show.bs.collapse", this.onShow.bind(this));
          e.addEventListener("shown.bs.collapse", this.onShown.bind(this));
          e.addEventListener("hide.bs.collapse", this.onHide.bind(this));
        });
      }
    },
    onShow(t) {
      this.toggleActiveClass(t, "show");
      this.setHashOnLoad(t);
      const e = i(t.target);
      if (e.closest(".vc_vc_accordion_tab").length) {
        e.closest(".vc_vc_accordion_tab").siblings().find(".accordion-collapse").collapse("hide");
      }
    },
    onHide(t) {
      this.toggleActiveClass(t, "hide");
    },
    toggleActiveClass(t, e) {
      const n = i(t.target).closest(".accordion-item");
      if (e === "show") {
        n.addClass("active").siblings().removeClass("active");
      }
      if (e === "hide") {
        n.removeClass("active");
      }
    },
    setHashOnShow(t) {
      if (history.pushState) {
        history.pushState(null, null, "#" + i(t.target).attr("id"));
      } else {
        location.hash = "#" + i(t.target).attr("id");
      }
    },
    onShown(t) {
      const e = i(t.target);
      const n = e.closest(".accordion-item");
      const s = i(window);
      const o = n.offset().top;
      this.initPlugins(n);
      if (o <= s.scrollTop() - 15) {
        i("html, body").animate({
          scrollTop: o - 45
        }, 800);
      }
    },
    initPlugins(t) {
      i("[data-split-text]", t).liquidSplitText();
    },
    destroy() {
      this.$contents.off("show.bs.collapse shown.bs.collapse hide.bs.collapse");
    }
  };
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("accordion-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".accordion").liquidAccordion();
});
(function(i) {
  const l = "liquidAjaxLoadMore";
  let d = {
    trigger: "inview"
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.observer = null;
      this.init();
    }
    init() {
      const { trigger: t } = this.options;
      t == "inview" && this.setupIntersectionObserver();
      t == "click" && this.onClick();
    }
    onClick() {
      this.$element.on("click", this.loadItems.bind(this));
    }
    setupIntersectionObserver() {
      this.observer = new IntersectionObserver((t) => {
        t.forEach((e) => {
          if (e.isIntersecting) {
            this.loadItems();
          }
        });
      }, { threshold: [1] });
      this.observer.observe(this.element);
    }
    loadItems(t) {
      t && t.preventDefault();
      const e = this.options;
      const n = i(e.wrapper);
      const s = this.$element.attr("href") || this.$element.data("href");
      const o = this.getPageNumber(s);
      this.$element.addClass("items-loading");
      i.ajax({
        type: "GET",
        url: s,
        error: (a, r, c) => {
          alert(c);
        },
        success: (a) => {
          const r = i(a);
          const c = r.find(e.wrapper);
          const p = c.find(e.items);
          let g = c.find("[data-ajaxify=true]");
          if (!g.length) {
            g = c.siblings(".ld-pf-nav-ajax").find("[data-ajaxify=true]");
          }
          let u = g.attr("href");
          const m = u && this.getPageNumber(u);
          if (m <= o) {
            u = s.replace(`page/${o}`, `page/${o + 1}`);
          }
          imagesLoaded(p.get(), () => {
            if (u && s != u) {
              this.$element.attr("href", u);
              this.$element.data("href", u);
            } else {
              this.observer && this.observer.unobserve(this.element);
              this.$element.removeClass("items-loading").addClass("all-items-loaded");
            }
            p.appendTo(n);
            if (n.get(0).hasAttribute("data-liquid-masonry")) {
              const f = n.data("plugin_liquidMasonry");
              const v = f && f.isoData;
              v && v.appended(p);
            }
            this.onSuccess(n);
          });
        }
      });
    }
    getPageNumber(t) {
      const e = new URL(t);
      const n = new URLSearchParams(e?.search);
      if (t.includes("page/")) {
        return parseInt(t.split("page/")[1].split("/")[0], 10);
      } else if (n.size > 0) {
        const s = Array.from(n.keys()).find((o) => o.includes("page") || o.includes("-page"));
        return parseInt(n.get(s), 10);
      }
    }
    onSuccess(t) {
      this.$element.removeClass("items-loading");
      if (i("body").hasClass("lazyload-enabled")) {
        window.liquidLazyload.update();
      }
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("ajaxify-options"), options: t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (i("body").hasClass("compose-mode"))
    return false;
  i("[data-ajaxify=true]").liquidAjaxLoadMore();
  i(document).on("ajaxComplete", (l, d, h) => {
    setTimeout(() => {
      if (!i("body").hasClass("lazyload-enabled")) {
        i("[data-responsive-bg=true]").liquidResponsiveBG();
      }
      i("[data-split-text]").filter((t, e) => !i(e).parents("[data-custom-animations]").length && !e.hasAttribute("data-custom-animations")).liquidSplitText();
      i("[data-fittext]").liquidFitText();
      i("[data-custom-animations]").map((t, e) => {
        const n = i(e);
        const s = n.parents(".wpb_wrapper[data-custom-animations]");
        if (s.length) {
          n.removeAttr("data-custom-animations");
          n.removeAttr("data-ca-options");
        }
      });
      i("[data-custom-animations]").filter((t, e) => {
        const n = i(e);
        const s = n.closest(".vc_row[data-row-bg]");
        const o = n.closest(".vc_row[data-slideshow-bg]");
        return !s.length && !o.length;
      }).liquidCustomAnimations();
      i("[data-lqd-flickity]").liquidCarousel();
      i("[data-parallax]").liquidParallax();
      i("[data-hover3d=true]").liquidHover3d();
      ScrollTrigger?.refresh();
    });
  });
});
(function(i) {
  const l = "liquidAnimatedFrames";
  let d = {
    current: 0,
    scrollable: false,
    forceDisablingWindowScroll: false,
    autoplay: false,
    autoplayTimeout: 4000
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.DOM = {};
      this.DOM.el = t;
      this.DOM.slides = Array.from(this.DOM.el.querySelectorAll(".lqd-af-slides > div"));
      this.DOM.nav = this.DOM.el.querySelector(".lqd-af-slidenav");
      this.DOM.nums = this.DOM.el.querySelector(".lqd-af-slidenum");
      this.DOM.numsCurrent = this.DOM.el.querySelector(".lqd-af-slidenum__current");
      this.DOM.numsTotal = this.DOM.el.querySelector(".lqd-af-slidenum__total");
      this.DOM.nextCtrl = this.DOM.nav.querySelector(".lqd-af-slidenav__item--next");
      this.DOM.prevCtrl = this.DOM.nav.querySelector(".lqd-af-slidenav__item--prev");
      this.slidesTotal = this.DOM.slides.length;
      this.current = this.options.current;
      this.startY = 0;
      this.currentY = 0;
      this.dragY = 0;
      this.userInteracted = false;
      this.autoplayTimeout = null;
      this.initNumbers();
      this.init();
      this.animateNumbers();
      this.initEvents();
      this.autoplay();
    }
    init() {
      const t = this.DOM.slides[this.current];
      t.classList.add("lqd-af-slide--current");
      this.DOM.el.classList.add("lqd-af--initial");
      this.onSlideLoaded(t);
    }
    initNumbers() {
      if (liquidIsElementor)
        return;
      const t = i('<span class="pos-abs pos-tl absolute top-0 left-0" />');
      this.DOM.numsTotal.innerText = this.slidesTotal;
      for (let e = 1;e <= this.slidesTotal; e++) {
        const n = i(`<span class="d-flex align-items-center justify-content-center flex items-center justify-center">${e}</span>`);
        n.appendTo(t);
      }
      t.appendTo(this.DOM.numsCurrent);
    }
    animateNumbers(t = 0) {
      const e = this.DOM.numsCurrent.querySelector("span");
      if (!e)
        return;
      e.style.transform = `translateY(${t * 100 * -1}%)`;
    }
    initEvents() {
      if (this.slidesTotal <= 1)
        return;
      i(this.DOM.nextCtrl).off("click.lqdAnimateFrames", this.navigate);
      i(this.DOM.prevCtrl).off("click.lqdAnimateFrames", this.navigate);
      i(this.DOM.nextCtrl).on("click.lqdAnimateFrames", this.navigate.bind(this, "next", true));
      i(this.DOM.prevCtrl).on("click.lqdAnimateFrames", this.navigate.bind(this, "prev", true));
      i(document).on("keydown.lqdAnimateFrames", (n) => {
        const s = n.originalEvent.key;
        if (s === "ArrowUp") {
          this.navigate("prev", true);
        } else if (s === "ArrowDown") {
          this.navigate("next", true);
        }
      });
      if (!this.options.scrollable)
        return false;
      this.initDrag();
      const t = "elementorFrontend" in window && elementorFrontend.isEditMode();
      if (this.options.forceDisablingWindowScroll && !t) {
        document.documentElement.classList.add("overflow-hidden");
      }
      const e = liquidThrottle((n) => {
        const s = n.target;
        if (s !== this.DOM.el && !this.DOM.el.contains(s))
          return;
        const o = Math.sign(n.originalEvent.deltaY);
        if (o < 0) {
          this.navigate("prev", true);
        } else if (o > 0) {
          this.navigate("next", true);
        }
      }, 800, true);
      $liquidWindow.on("wheel.lqdAnimateFrames", e);
    }
    initDrag() {
      this.$element.on("mousedown touchstart", this.pointerStart.bind(this));
      this.$element.on("mousemove touchmove", this.pointerMove.bind(this));
      this.$element.on("mouseup touchend", this.pointerEnd.bind(this));
    }
    pointerStart(t) {
      if (this.options.forceDisablingWindowScroll && "elementorFrontend" in window && !elementorFrontend.isEditMode()) {
        document.documentElement.classList.add("overflow-hidden");
      }
      this.startY = t.pageY || t.originalEvent.changedTouches[0].pageY;
      this.currentY = this.startY;
      this.$element.addClass("pointer-down");
    }
    pointerMove(t) {
      if (this.options.forceDisablingWindowScroll && "elementorFrontend" in window && !elementorFrontend.isEditMode()) {
        document.documentElement.classList.add("overflow-hidden");
      }
      this.currentY = t.pageY || t.originalEvent.changedTouches[0].pageY;
      this.dragY = parseInt(this.startY - this.currentY, 10);
    }
    pointerEnd() {
      this.dragY = parseInt(this.startY - this.currentY, 10);
      if (this.dragY >= 20) {
        this.navigate("next");
      } else if (this.dragY <= -20) {
        this.navigate("prev");
      }
      this.element.classList.remove("pointer-down");
    }
    navigate(t = "next", e) {
      if (this.isAnimating)
        return false;
      if (e) {
        this.userInteracted = true;
      }
      this.isAnimating = true;
      if (this.autoplayTimeout) {
        clearTimeout(this.autoplayTimeout);
      }
      if (this.options.forceDisablingWindowScroll && "elementorFrontend" in window && !elementorFrontend.isEditMode()) {
        document.documentElement.classList.add("overflow-hidden");
      }
      this.element.classList.add("lqd-af--navigating", "lqd-af--navigation-init");
      t === "next" && this.element.classList.add("lqd-af--moving-up");
      t === "prev" && this.element.classList.add("lqd-af--moving-down");
      const n = this.DOM.slides[this.current];
      const s = n.querySelector(".lqd-af-slide__img");
      const o = s.querySelector(".lqd-af-slide__img__inner");
      const a = s.querySelector("figure");
      const r = n.querySelectorAll(".lqd-af-slide__title .split-inner");
      const c = n.querySelectorAll(".lqd-af-slide__desc .split-inner");
      const p = n.querySelector(".lqd-af-slide__link");
      const g = gsap.timeline({
        duration: 1.2,
        onComplete: () => {
          n.classList.remove("lqd-af-slide--movin-out");
        }
      });
      g.fromTo(a, { scale: 1 }, { scale: 1.25, ease: CustomEase.create("custom", "M0,0,C0.4,0,0.2,1,1,1") }, 0);
      g.fromTo(o, { scale: 1 }, { scale: 0.5, ease: CustomEase.create("custom", "M0,0,C0.4,0,0.2,1,1,1") }, 0.015);
      g.to(s, {
        duration: 0.85,
        ease: CustomEase.create("custom", "M0,0,C0.395,0,0.1,1,1,1"),
        y: t === "next" ? "-100%" : "100%"
      }, 0.35);
      n.classList.add("lqd-af-slide--movin-out");
      const u = gsap.timeline({
        duration: 1.2,
        delay: 0.5,
        ease: CustomEase.create("custom", "M0,0,C0.4,0,0.1,1,1,1"),
        onComplete: () => {
          n.classList.remove("lqd-af-slide--current");
          this.DOM.el.classList.add("lqd-af--initial");
        }
      });
      r?.length && u.fromTo(r, { y: "0%" }, { y: t === "next" ? "-105%" : "105%" }, t === "next" ? 0 : 0.35);
      c?.length && u.fromTo(c, { opacity: 1, y: "0%" }, { opacity: 0, y: t === "next" ? "-100%" : "100%" }, 0.25);
      p && u.fromTo(p, { opacity: 1, y: "0%" }, { opacity: 0, y: t === "next" ? "-75%" : "75%" }, t === "next" ? 0.35 : 0);
      this.current = t === "next" ? this.current < this.slidesTotal - 1 ? this.current + 1 : 0 : this.current > 0 ? this.current - 1 : this.slidesTotal - 1;
      this.animateNumbers(this.current);
      const m = this.DOM.slides[this.current];
      m.classList.add("lqd-af-slide--current", "lqd-af-slide--movin-in");
      this.DOM.el.classList.add("lqd-af--initial");
      this.onSlideLoaded(m);
      const f = m.querySelector(".lqd-af-slide__img");
      const v = m.querySelectorAll(".lqd-af-slide__title .split-inner");
      const y = m.querySelectorAll(".lqd-af-slide__desc .split-inner");
      const w = m.querySelector(".lqd-af-slide__link");
      const b = gsap.timeline({
        duration: 1.2,
        delay: 0.35,
        ease: CustomEase.create("custom", "M0,0,C0.4,0,0.1,1,1,1"),
        onComplete: () => {
          m.classList.remove("lqd-af-slide--movin-in");
        }
      });
      b.fromTo(f, { y: t === "next" ? "100%" : "-100%" }, {
        y: 0,
        duration: 0.85,
        ease: CustomEase.create("custom", "M0,0,C0.395,0,0.1,1,1,1")
      }, 0);
      v?.length && b.fromTo(v, { y: t === "next" ? "105%" : "-105%" }, { y: "0%" }, t === "next" ? 0.3 : 0.5);
      y?.length && b.fromTo(y, { opacity: 0, y: t === "next" ? "100%" : "-100%" }, { opacity: 1, y: "0%" }, 0.4);
      w && b.fromTo(w, { opacity: 0, y: t === "next" ? "75%" : "-75%" }, { opacity: 1, y: "0%" }, t === "next" ? 0.5 : 0.3);
      g.then(this.animateShapeOut.bind(this, t));
    }
    animateShapeOut() {
      const t = this.DOM.slides[this.current];
      const e = t.querySelector(".lqd-af-slide__img");
      const n = e.querySelector(".lqd-af-slide__img__inner");
      const s = e.querySelector("figure");
      gsap.to([n, s], {
        scale: 1,
        duration: 0.8,
        ease: CustomEase.create("custom", "M0,0,C0.4,0,0.2,1,1,1"),
        onComplete: () => {
          this.isAnimating = false;
          this.element.classList.remove("lqd-af--navigating", "lqd-af--moving-up", "lqd-af--moving-down");
          !this.options.forceDisablingWindowScroll && document.documentElement.classList.remove("overflow-hidden");
          this.autoplay();
        }
      });
    }
    autoplay() {
      if (!this.options.autoplay || this.userInteracted || this.slidesTotal <= 1)
        return;
      this.autoplayTimeout = setTimeout(() => {
        this.navigate();
      }, this.options.autoplayTimeout);
    }
    onSlideLoaded(t) {
      const e = i(t);
      const n = e.find("video");
      n.each(function() {
        const s = i(this);
        s.find("source").each(function() {
          const o = i(this);
          o.attr("src", o.attr("data-src"));
        });
        s[0].load();
        s[0].play();
      });
    }
    destroy() {
      i(this.DOM.nextCtrl).off("click.lqdAnimateFrames");
      i(this.DOM.prevCtrl).off("click.lqdAnimateFrames");
      i(document).off("keydown.lqdAnimateFrames");
      this.$element.off("mouseenter.lqdAnimateFrames");
      $liquidWindow.off("wheel.lqdAnimateFrames");
      this.$element.off("mouseleave.lqdAnimateFrames");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("af-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-liquid-animatedframes=true]").liquidAnimatedFrames();
});
(function(i) {
  const l = "liquidAsymmetricSlider";
  let d = {
    autoplay: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.isRTL = i("html").attr("dir") === "rtl";
      this.DOM = {
        titlesWrap: this.element.querySelector(".lqd-asym-slider-title-wrap"),
        infosWrap: this.element.querySelector(".lqd-asym-slider-info-wrap"),
        imagesWrap: this.element.querySelector(".lqd-asym-slider-img-wrap"),
        titles: [...this.element.querySelectorAll(".lqd-asym-slider-title")],
        infos: [...this.element.querySelectorAll(".lqd-asym-slider-info")],
        images: [...this.element.querySelectorAll(".lqd-asym-slider-img")],
        prevBtn: this.element.querySelector(".lqd-asym-slider-prev"),
        nextBtn: this.element.querySelector(".lqd-asym-slider-next")
      };
      this.isPlaying = false;
      this.currentSlide = 0;
      this.nextSlide = null;
      this.prevSlide = null;
      this.totalSlides = this.DOM.images.length - 1;
      const n = [];
      const s = i(this.DOM.titlesWrap).find(".lqd-asym-slider-title-element").first();
      const o = s.css("font-family").replace(/"/g, "").replace(/'/g, "").split(",")[0];
      const a = s.css("font-weight");
      const r = s.css("font-style");
      const c = window.liquidSlugify(o);
      n.push(new Promise((p) => imagesLoaded(this.element, p)));
      if (!window.liquidCheckedFonts.find((p) => p === c)) {
        const p = new FontFaceObserver(o, {
          weight: a,
          style: r
        });
        n.push(p.load());
      }
      Promise.all(n).finally(() => {
        new IntersectionObserver(([p], g) => {
          if (p.isIntersecting) {
            g.disconnect();
            this.init();
          }
        }).observe(this.element);
      });
    }
    init() {
      const { autoplay: t } = this.options;
      this.updateHeights();
      this.events();
      this.playInitial();
      this.element.classList.add("lqd-asym-slider-ready");
      if (t && t > 0) {
        this.autoplayInitCall = gsap.delayedCall(1.5, this.autoplay.bind(this));
      }
    }
    autoplay() {
      const { autoplay: t } = this.options;
      if (!t || t <= 0)
        return;
      this.autoplayInitCall && this.autoplayInitCall.kill();
      this.autoplayCall = gsap.delayedCall(t, this.next.bind(this));
    }
    events() {
      this.DOM.prevBtn.addEventListener("click", this.prev.bind(this));
      this.DOM.nextBtn.addEventListener("click", this.next.bind(this));
      window.addEventListener("resize", liquidDebounce(this.updateHeights.bind(this), 1000));
    }
    updateHeights() {
      this.DOM.imagesWrap.style.transition = "height 0.3s 1s";
      this.DOM.titlesWrap.style.transition = "height 0.3s 1s";
      this.DOM.infosWrap.style.transition = "height 0.3s 1s";
      this.DOM.imagesWrap.style.height = `${this.DOM.images[this.currentSlide].offsetHeight}px`;
      this.DOM.titlesWrap.style.height = `${this.DOM.titles[this.currentSlide].offsetHeight}px`;
      this.DOM.infosWrap.style.height = `${this.DOM.infos[this.currentSlide].offsetHeight}px`;
    }
    beforePlay() {
      this.element.classList.add("lqd-asym-slider-changing");
      this.DOM.titles[this.nextSlide].classList.add("is-next");
      this.DOM.titles[this.nextSlide].classList.remove("active");
      this.DOM.images[this.nextSlide].classList.add("is-next");
      this.DOM.images[this.nextSlide].classList.remove("active");
      this.DOM.infos[this.nextSlide].classList.add("is-next");
      this.DOM.infos[this.nextSlide].classList.remove("active");
      this.isPlaying = true;
    }
    afterPlay() {
      this.element.classList.remove("lqd-asym-slider-changing");
      this.DOM.titles[this.nextSlide].classList.remove("is-next");
      this.DOM.titles[this.nextSlide].classList.add("active");
      this.DOM.titles[this.prevSlide].classList.remove("active");
      this.DOM.images[this.nextSlide].classList.remove("is-next");
      this.DOM.images[this.nextSlide].classList.add("active");
      this.DOM.images[this.prevSlide].classList.remove("active");
      this.DOM.infos[this.nextSlide].classList.remove("is-next");
      this.DOM.infos[this.nextSlide].classList.add("active");
      this.DOM.infos[this.prevSlide].classList.remove("active");
      this.isPlaying = false;
      this.autoplayCall && this.autoplayCall.kill();
      this.autoplay();
    }
    playInitial() {
      this.prevSlide = this.currentSlide;
      this.nextSlide = this.currentSlide;
      this.playTitle("init");
      this.playInfo("init");
      this.playImages("init");
    }
    prev() {
      if (this.isPlaying)
        return;
      this.prevSlide = this.currentSlide;
      this.nextSlide = this.currentSlide === 0 ? this.totalSlides : this.currentSlide - 1;
      this.currentSlide = this.nextSlide;
      this.beforePlay();
      this.updateHeights();
      this.playTitle("prev");
      this.playInfo("prev");
      this.playImages("prev").then(() => {
        this.afterPlay();
      });
    }
    next() {
      if (this.isPlaying)
        return;
      this.prevSlide = this.currentSlide;
      this.nextSlide = this.currentSlide === this.totalSlides ? 0 : this.currentSlide + 1;
      this.currentSlide = this.nextSlide;
      this.beforePlay();
      this.updateHeights();
      this.playTitle("next");
      this.playInfo("next");
      this.playImages("next").then(() => {
        this.afterPlay();
      });
    }
    playTitle(t) {
      const e = this.DOM.titles[this.prevSlide];
      const n = this.DOM.titles[this.nextSlide];
      const s = this.isRTL ? e.querySelectorAll(".lqd-words") : e.querySelectorAll(".lqd-chars");
      const o = this.isRTL ? n.querySelectorAll(".lqd-words") : n.querySelectorAll(".lqd-chars");
      const a = gsap.timeline({
        defaults: {
          duration: 1
        },
        delay: t === "next" ? 0.15 : 0
      });
      if (t === "prev") {
        a.fromTo([...s].reverse(), { y: "0%", rotation: 0, opacity: 1 }, { y: "100%", rotation: 15, opacity: 0, ease: "expo.inOut", stagger: 0.025 }).fromTo([...o].reverse(), { y: "-100%", rotation: 15, opacity: 0 }, { y: "0%", rotation: 0, opacity: 1, ease: "expo.out", stagger: 0.025 }, 0.75);
      } else if (t === "next") {
        a.fromTo(s, { y: "0%", rotation: 0, opacity: 1 }, { y: "-100%", rotation: 15, opacity: 0, ease: "expo.inOut", stagger: 0.025 }).fromTo(o, { y: "100%", rotation: 15, opacity: 0 }, { y: "0%", rotation: 0, opacity: 1, ease: "expo.out", stagger: 0.025 }, 0.75);
      } else {
        a.fromTo(s, { x: 35, opacity: 0 }, { x: 0, opacity: 1, ease: "expo.inOut", stagger: 0.045 });
      }
    }
    playInfo(t) {
      const e = this.DOM.infos[this.prevSlide];
      const n = e.querySelector(".lqd-asym-slider-subtitle-element");
      const s = e.querySelector(".lqd-asym-slider-description-element");
      const o = e.querySelector("hr");
      const a = this.DOM.infos[this.nextSlide];
      const r = a.querySelector(".lqd-asym-slider-subtitle-element");
      const c = a.querySelector(".lqd-asym-slider-description-element");
      const p = gsap.timeline({
        defaults: {
          ease: "expo.inOut",
          duration: 1.5
        },
        delay: t === "prev" ? 0.3 : 0.15
      });
      if (t === "prev") {
        p.fromTo(n, { x: 0, opacity: 1 }, { x: 15, opacity: 0 }, 0).fromTo(s, { x: 0, opacity: 1 }, { x: 15, opacity: 0 }, 0.15).fromTo(r, { x: -15, opacity: 0 }, { x: 0, opacity: 1 }, 0.15).fromTo(c, { x: -15, opacity: 0 }, { x: 0, opacity: 1 }, 0.3);
      } else if (t === "next") {
        p.fromTo(n, { x: 0, opacity: 1 }, { x: -15, opacity: 0 }, 0).fromTo(s, { x: 0, opacity: 1 }, { x: -15, opacity: 0 }, 0.15).fromTo(r, { x: 15, opacity: 0 }, { x: 0, opacity: 1 }, 0.15).fromTo(c, { x: 15, opacity: 0 }, { x: 0, opacity: 1 }, 0.3);
      } else {
        p.fromTo(n, { x: 30, opacity: 0 }, { x: 0, opacity: 1 }, 0).fromTo(o, { scaleX: 0.6, opacity: 0 }, { scaleX: 1, opacity: 1 }, 0).fromTo(s, { x: 30, opacity: 0 }, { x: 0, opacity: 1 }, 0.15);
      }
    }
    playImages(t) {
      const e = this.DOM.images[this.prevSlide];
      const n = e.querySelector(".lqd-asym-slider-img-inner");
      const s = this.DOM.images[this.nextSlide];
      const o = s.querySelector(".lqd-asym-slider-img-inner");
      const a = gsap.timeline({
        defaults: {
          ease: "expo.inOut",
          duration: 1.5
        },
        delay: t === "prev" ? 0.15 : 0
      });
      if (t === "prev") {
        a.fromTo(n, { x: "0%", scale: 1 }, { x: "-100%", scale: 1.2 }, 0).fromTo(e, { x: "0%" }, { x: "100%" }, 0).fromTo(s, { x: "-100%" }, { x: "0%" }, 0).fromTo(o, { x: "100%", scale: 1.2 }, { x: "0%", scale: 1 }, 0);
      } else if (t === "next") {
        a.fromTo(n, { x: "0%", scale: 1 }, { x: "100%", scale: 1.2 }, 0).fromTo(e, { x: "0%" }, { x: "-100%" }, 0).fromTo(s, { x: "100%" }, { x: "0%" }, 0).fromTo(o, { x: "-100%", scale: 1.2 }, { x: "0%", scale: 1 }, 0);
      } else {
        a.fromTo(n, { x: "100%", scale: 1.2 }, { x: "0%", scale: 1 }, 0).fromTo(e, { x: "-100%" }, { x: "0%" }, 0);
      }
      return a;
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("asym-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-asym-slider]").liquidAsymmetricSlider();
});
(function(i) {
  const l = "liquidBackToTop";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.init();
    }
    init() {
      this.checkforWPBottomMenu();
      this.firstSectionIO();
    }
    checkforWPBottomMenu() {
      const t = document.getElementById("wp-bottom-menu");
      if (!t)
        return;
      const e = t.clientHeight;
      this.element.style.bottom = `${e + 30}px`;
    }
    firstSectionIO() {
      let t = $liquidSectionsWrapper.children().not("style, p").first();
      if (t.hasClass("lqd-contents")) {
        t = t.children().first();
      }
      const e = t.css("position") === "sticky";
      const n = t.is(":only-child");
      if (t.is(":hidden") || e) {
        t = t.siblings().not("style, p").first();
      }
      if (!t.length)
        return;
      let s = [0, 0.25, 0.5, 0.75, 1];
      if (e) {
        s = [0];
      }
      if (n) {
        s = [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1];
      }
      new IntersectionObserver(([o]) => {
        const { boundingClientRect: a, rootBounds: r } = o;
        let c = r && r.top >= a.bottom - a.height / 2;
        if (e) {
          c = r && r.bottom >= a.top;
        }
        if (n) {
          c = r && r.bottom - r.height / 2 >= a.top + 250;
        }
        if (c) {
          this.$element.addClass("is-visible");
        } else {
          this.$element.removeClass("is-visible");
        }
      }, { threshold: s }).observe(t.get(0));
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("back-to-top-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (!i("html").hasClass("pp-enabled")) {
    i("[data-back-to-top]").liquidBackToTop();
  }
});
(function(i) {
  const l = "liquidBgColor";
  let d = {
    getBgFromSelector: "backgroundColor",
    setBgTo: "self",
    manipulateColor: null,
    changeBorderColor: false,
    interactWithHeader: false,
    makeGradient: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.$stickyModules = this._getStickyModules();
      this.setBgToEls = this.options.setBgTo === "self" ? [this.element] : i(this.options.setBgTo, this.element).get();
      this.$bgEl = false;
      this.rowsRect = [];
      this.colors = [];
      this.direction = 0;
      window.document.documentElement.style.scrollBehavior = "auto";
      this.$element.is($liquidContents) && this._addBgElement();
      this.liquidBgColorInitPromise = new Promise((n) => {
        this.$element.on("lqd-bg-color-init", n.bind(this, this));
      });
      i(window).on("load", () => {
        window.scrollBy(0, -1);
      });
      _.getDetails().then((n) => {
        i(n).imagesLoaded(this._init(n));
      });
    }
    _init(t) {
      const e = t.filter((n) => !n.isHidden && !n.isInFooter).filter((n) => !n.isInnerSection);
      e.forEach(async (n, s) => {
        await this._getColors(n);
        Promise.all(this._getRects(n, s)).then(() => {
          this._setupIO(n, s);
          if (this.$bgEl) {
            this.element.classList.add("bg-transparent");
            n.el.classList.add("bg-transparent");
            n.isInnerSection && n.parentSection && n.parentSection.el.classList.add("bg-transparent");
          }
        });
      });
      this.$element.trigger("lqd-bg-color-init", this.element);
    }
    _getStickyModules() {
      let t = false;
      if ($liquidMainHeader.length && $liquidMainHeader[0].hasAttribute("data-sticky-header")) {
        if (liquidIsElementor) {
          t = $liquidMainHeader.find("> .elementor").find("> .elementor-section-wrap > .elementor-section, > .elementor-section, > .elementor-section-wrap > .e-container, > .e-container, > .e-con").not(".lqd-hide-onstuck").find('[data-element_type="widget"]');
        } else {
          t = $liquidMainHeader.find(".lqd-head-sec-wrap, .lqd-stickybar-wrap").not(".lqd-hide-onstuck").find(".header-module");
        }
      }
      return t;
    }
    _addBgElement() {
      if (this.$bgEl)
        return;
      const t = liquidIsMobile() ? "overflow-hidden" : "";
      const e = liquidIsMobile() ? "pos-fix fixed" : "pos-sticky sticky";
      const n = liquidIsMobile() ? "h-100 h-full" : "h-vh-100 h-100vh";
      this.$bgEl = i(`<div class="lqd-liquid-bg-el-wrap lqd-overlay pointer-events-none z-index--1 ${t}"><div class="lqd-liquid-bg-el ${e} pos-tl w-100 top-0 left-0 w-full ${n} pointer-events-none"></div></div>`);
      this.$bgEl.prependTo(this.$element);
      this.setBgToEls = [this.$bgEl.children()[0]];
      if ($liquidMainFooter.length && !$liquidMainFooter[0].hasAttribute("data-sticky-footer") || liquidIsMobile()) {
        $liquidMainFooter.css({
          position: "relative",
          zIndex: 2
        });
      }
    }
    _getColors(t) {
      return fastdomPromised.measure(() => {
        const { getBgFromSelector: e, manipulateColor: n } = this.options;
        const s = {};
        let o = t[e].replace(/, /g, ",").split(" ")[0];
        let a = t.$el.attr("data-section-luminosity");
        if (t.isInnerSection && t.parentSection) {
          if (t.isBgTransparent && !t.parentSection.isBgTransparent) {
            o = t.parentSection[e].replace(/, /g, ",").split(" ")[0];
          }
        }
        if (t.isBgTransparent) {
          o = $liquidContents.css("backgroundColor");
        }
        if (n && n.length > 0) {
          for (let r = 0;r < n.length; r++) {
            o = tinycolor(o)[Object.keys(n[r])[0]](Object.values(n[r])[0]).toString();
          }
        }
        s.color = o;
        s.luminosity = a != null && !n ? a : tinycolor(o).getLuminance() <= 0.4 ? "dark" : "light";
        this.colors.push(s);
      });
    }
    _interactWithHeader(t) {
      const e = $liquidMainHeader.add(this.$stickyModules).filter((n, s) => {
        const o = i(s);
        return !o.children(".navbar-brand-solid").length && !o.hasClass("navbar-brand-solid") && !o.find("> .elementor-widget-container > .navbar-brand-solid").length;
      });
      if (t === "dark") {
        e.addClass("lqd-active-row-dark").removeClass("lqd-active-row-light");
      } else {
        e.addClass("lqd-active-row-light").removeClass("lqd-active-row-dark");
      }
    }
    _interactWithColors(t, e) {
      const n = i(t);
      if (n.hasClass("btn-icon") || n.hasClass("btn-solid")) {
        const s = e === "dark" ? "#fff" : "#000";
        n.css({
          transition: "box-shadow 0.3s, transform 0.3s, color 0.3s",
          color: s
        });
      }
      if (n.hasClass("navbar-brand-inner")) {
        const s = liquidIsElementor ? n.closest(".elementor-element") : n.closest(".header-module");
        if (e === "dark") {
          s.addClass("lqd-active-row-dark").removeClass("lqd-active-row-light");
        } else {
          s.addClass("lqd-active-row-light").removeClass("lqd-active-row-dark");
        }
      }
    }
    _getRects(t, e) {
      const n = [];
      let s = t.el.querySelector(".ld-row");
      if (liquidIsElementor) {
        if (t.el.classList.contains("e-container") || t.el.classList.contains("e-con")) {
          s = t.el;
        } else {
          s = t.el.querySelector(".elementor-container");
        }
      }
      const o = new Promise((a) => {
        new IntersectionObserver(([r], c) => {
          c.disconnect();
          this.rowsRect[e] = r.boundingClientRect;
          a();
        }).observe(s || t.el);
      });
      n.push(o);
      return n;
    }
    _setupIO(t, e) {
      const n = gsap.timeline();
      this.setBgToEls.forEach((s) => {
        n.fromTo(s, {
          backgroundColor: e === 0 ? this.colors[e].color : this.colors[e - 1].color
        }, {
          backgroundColor: this.colors[e].color,
          onUpdate: () => {
            const o = this.direction < 1 && e > 0 ? e - 1 : e;
            this.options.interactWithHeader && this.$stickyModules && this._interactWithHeader(this.colors[o].luminosity);
            this._interactWithColors(s, this.colors[o].luminosity);
          },
          onComplete: () => {
            s.style.transition = "";
          }
        }, 0);
      });
      ScrollTrigger.create({
        animation: n,
        trigger: t.el,
        start: () => {
          return e === 0 ? "top bottom" : `top+=${this.rowsRect[e].y - t.rect.y} bottom`;
        },
        end: () => {
          return `+=${window.innerWidth > 767 ? Math.min(window.innerHeight / 2, t.el.offsetHeight) : t.el.offsetHeight}`;
        },
        scrub: 0.1,
        onUpdate: (s) => {
          this.direction = s.direction;
        }
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("liquid-bg-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  const l = i("[data-liquid-bg]");
  l.liquidBgColor();
  if (liquidIsElementor) {
    l.each((d, h) => {
      const t = i(h);
      if (t.is($liquidContents) && $liquidMainHeader.length && !$liquidMainHeader.attr("data-liquid-bg")) {
        $liquidMainHeader.liquidBgColor({
          setBgTo: `
						> .elementor > .elementor-section-wrap > .elementor-section:not(.lqd-hide-onstuck) > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-widget-ld_header_image .navbar-brand-solid .navbar-brand-inner,
						> .elementor > .elementor-section-wrap > .elementor-section:not(.lqd-hide-onstuck) > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-widget-ld_button .btn-solid,
						> .elementor > .elementor-section-wrap > .elementor-section:not(.lqd-hide-onstuck) > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-widget-ld_button .btn-icon-solid .btn-icon,
						> .elementor > .elementor-section:not(.lqd-hide-onstuck) > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-widget-ld_header_image .navbar-brand-solid .navbar-brand-inner,
						> .elementor > .elementor-section:not(.lqd-hide-onstuck) > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-widget-ld_button .btn-solid,
						> .elementor > .elementor-section:not(.lqd-hide-onstuck) > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-widget-ld_button .btn-icon-solid .btn-icon`,
          manipulateColor: [{ darken: 30 }, { brighten: 15 }, { saturate: 20 }]
        });
      }
    });
  }
});
(function(i) {
  const l = "liquidAnimatedIcon";
  let d = {
    objContainer: ".iconbox-icon-container",
    color: "#f42958",
    hoverColor: null,
    type: "delayed",
    delay: 0,
    duration: 100,
    resetOnHover: false,
    file: null
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = i.extend({}, d, e);
      this.element = t;
      this.$element = i(t);
      this.options.file && this.init();
    }
    init() {
      this.animateIcon();
      return this;
    }
    animateIcon() {
      const { type: t, duration: e, file: n, objContainer: s } = this.options;
      const o = this.$element.attr("id") || Math.round(Math.random() * 1e6);
      const a = this.$element.find(s).attr("id", `${o.replace(/ld_icon_box/, "ld_icon_container")}`);
      new Vivus(a.attr("id"), {
        file: n,
        type: t,
        duration: e,
        start: "manual",
        onReady: (r) => {
          this.onReady.call(this, r);
        }
      }).setFrameProgress(1);
    }
    onReady(t) {
      const e = this.$element.closest(".carousel-items");
      const n = e.data("plugin_liquidCarousel");
      this.addColors(t);
      this.animate(t);
      if (e.length && n != null && n.flickityData != null) {
        n.flickityData.resize();
      }
    }
    addColors(t) {
      const e = i(t.el);
      const { color: n, hoverColor: s } = this.options;
      const o = Math.round(Math.random() * 1e6);
      let a = s;
      let r = document.createElementNS("http://www.w3.org/2000/svg", "style");
      let c = n != null ? n.split(":") : "#000";
      let p = null;
      if (c[1] === undefined) {
        c[1] = c[0];
      }
      p = '<defs xmlns="http://www.w3.org/2000/svg">' + '<linearGradient gradientUnits="userSpaceOnUse" id="grad' + o + '" x1="0%" y1="0%" x2="0%" y2="100%">' + '<stop offset="0%" stop-color="' + c[0] + '" />' + '<stop offset="100%" stop-color="' + c[1] + '" />' + "</linearGradient>" + "</defs>";
      const g = new DOMParser().parseFromString(p, "text/xml");
      e.prepend(g.documentElement);
      if (typeof a !== "undefined" && a !== null) {
        a = a.split(":");
        if (a[1] === undefined) {
          a[1] = a[0];
        }
        r.innerHTML = "#" + this.$element.attr("id") + ":hover .iconbox-icon-container defs stop:first-child{stop-color:" + a[0] + ";}" + "#" + this.$element.attr("id") + ":hover .iconbox-icon-container defs stop:last-child{stop-color:" + a[1] + ";}";
        e.prepend(r);
      }
      e.find("path, rect, ellipse, circle, polygon, polyline, line").attr({
        stroke: "url(#grad" + o + ")",
        fill: "none"
      });
      this.$element.addClass("iconbox-icon-animating");
      return this;
    }
    animate(t) {
      const e = this.options;
      const n = parseInt(e.delay, 10);
      const { duration: s } = e;
      t.reset().stop();
      new IntersectionObserver(([o], a) => {
        const r = t.getStatus();
        if (o.isIntersecting && r === "start" && r !== "progress") {
          a.disconnect();
          this.resetAnimate(t, n, s);
          this.eventHandlers(t, n, s);
        }
      }).observe(this.element);
      return this;
    }
    eventHandlers(t, e, n) {
      const { options: s } = this;
      i(document).on("shown.bs.tab", 'a[data-toggle="tab"]', (o) => {
        const a = i(i(o.currentTarget).attr("href"));
        if (a.find(this.element).length) {
          this.resetAnimate.call(this, t, e, n);
        }
      });
      if (s.resetOnHover) {
        this.$element.on("mouseenter.lqdIconbox", () => {
          if (t.getStatus() === "end") {
            this.resetAnimate(t, 0, n);
          }
        });
      }
    }
    resetAnimate(t, e, n) {
      t.stop().reset();
      const s = setTimeout(() => {
        t.play(n / 100);
        clearTimeout(s);
      }, e);
    }
    destroy() {
      this.$element.off("mouseenter.lqdIconbox");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-animate-icon]").liquidAnimatedIcon();
});
(function(i) {
  const l = "liquidCarousel";
  let d = {
    bypassCheck: false,
    carouselEl: null,
    contain: false,
    imagesLoaded: true,
    percentPosition: true,
    prevNextButtons: false,
    pageDots: true,
    adaptiveHeight: false,
    cellAlign: "left",
    groupCells: true,
    dragThreshold: 0,
    wrapAround: false,
    autoplay: false,
    fullwidthSide: false,
    navArrow: 1,
    filters: false,
    filtersCounter: false,
    doSomethingCrazyWithFilters: false,
    equalHeightCells: false,
    middleAlignContent: false,
    randomVerOffset: false,
    parallax: false,
    parallaxEl: "img",
    dotsIndicator: "classic",
    numbersStyle: "circle",
    addSlideNumbersToArrows: false,
    marquee: false,
    marqueeTickerSpeed: 1,
    fade: false,
    prevNextButtonsOnlyOnMobile: false,
    columnsAutoWidth: false,
    watchCSS: false,
    forceApply: false,
    skipWrapItems: false,
    forceEnableOnMobile: false
  };
  function h(t, e) {
    this._defaults = d;
    this._name = l;
    this.options = { ...d, ...e };
    this.flickityData = null;
    this.isRTL = i("html").attr("dir") === "rtl";
    if (liquidIsMobile()) {
      this.options.dragThreshold = 5;
    }
    this.element = t;
    this.$element = i(t);
    this.$carouselContainer = this.$element.closest(".carousel-container").length ? this.$element.closest(".carousel-container") : this.$element.parent();
    this.carouselNavElement = null;
    this.carouselDotsElement = null;
    this.carouselMobileDotsElement = null;
    this.$carouselCurrentSlide = null;
    this.$carouselCurrentSlideInner = null;
    this.$carouselTotalSlides = null;
    this.$carouselSlidesShape = null;
    this.carouselSlidesPathLength = this.options.numbersStyle === "circle" ? 471 : 200;
    this.windowWidth = liquidWindowWidth();
    this.$carouselEl = this.options.carouselEl ? i(this.options.carouselEl, this.element) : this.$element;
    this.carouselEl = this.$carouselEl[0];
    this.originalWrapAroundOption = this.options.wrapAround;
    this.originalDraggableOption = this.options.draggable;
    this.carouselInitPromise = new Promise((n) => {
      this.$element.on("lqd-carousel-initialized", n.bind(this, this));
    });
    if (this.options.marquee) {
      this.options.wrapAround = true;
    }
    this.init();
  }
  h.prototype = {
    init() {
      if (this.options.asNavFor) {
        const t = i(this.options.asNavFor);
        if (t.length) {
          t.liquidCarousel({ forceApply: true });
          t.data("plugin_liquidCarousel").carouselInitPromise.then(() => {
            this.initFlicky();
          });
        }
      } else {
        if (this.options.forceApply) {
          this.initFlicky();
        } else {
          this.setIO();
        }
      }
    },
    setIO() {
      new IntersectionObserver(([t], e) => {
        if (t.isIntersecting) {
          this.initFlicky();
          e.unobserve(t.target);
        }
      }, { rootMargin: "35%" }).observe(this.element);
    },
    initFlicky() {
      const t = { ...this.options, rightToLeft: this.isRTL || this.options.rightToLeft };
      const { equalHeightCells: e } = this.options;
      imagesLoaded(this.element, () => {
        this.columnsAutoWidth();
        this.wrapItems();
        this.setEqualHeightCells();
        this.$carouselEl.flickity(t);
        this.flickityData = this.$carouselEl.data("flickity");
        t.adaptiveHeight && i(".flickity-viewport", this.element).css("transition", "height 0.3s");
        this.onImagesLoaded();
        this.$element.addClass("lqd-carousel-ready");
        const n = this.flickityData.resize;
        const s = this;
        const { carouselEl: o } = this;
        this.flickityData.resize = function() {
          if (this.options.marquee && s.windowWidth === liquidWindowWidth())
            return;
          if (e) {
            o.classList.remove("flickity-equal-cells");
          }
          n.call(this);
          if (e) {
            o.classList.add("flickity-equal-cells");
          }
          s.windowWidth = liquidWindowWidth();
        };
      });
    },
    onImagesLoaded() {
      if (!this.flickityData)
        return;
      this.sliderElement = this.element.querySelector(".flickity-slider");
      this.initPlugins();
      this.setElementNavArrow();
      this.carouselNav();
      this.navOffsets();
      this.carouselDots();
      this.carouselMobileDots();
      this.carouselDotsNumbers();
      this.addSlideNumbersToArrows();
      this.addSlidesCurrentNumbers();
      this.randomVerOffset();
      this.fullwidthSide();
      this.controllingCarousels();
      this.marquee();
      this.filtersInit();
      this.windowResize();
      this.events();
      this.dispatchEvents();
      if (this.options.columnsAutoWidth) {
        this.$element.find(".carousel-item-content").css("width", "");
        this.flickityData.reposition();
      }
    },
    initPlugins() {
      this.element.hasAttribute("data-custom-animations") && this.$element.liquidCustomAnimations();
    },
    dispatchEvents() {
      const t = new CustomEvent("lqd-carousel-initialized", { detail: { carouselData: this } });
      document.dispatchEvent(t);
      this.$element.trigger("lqd-carousel-initialized", this.element);
    },
    windowResize() {
      const t = liquidDebounce(this.doOnWindowResize.bind(this), 200);
      i(window).on("resize.lqdCarousel", t);
    },
    doOnWindowResize() {
      if (this.windowWidth === window.innerWidth)
        return;
      this.windowWidth = window.innerWidth;
      this.fullwidthSide();
      this.columnsAutoWidth();
      if (this.options.columnsAutoWidth) {
        this.$element.find(".carousel-item-content").css("width", "");
        this.flickityData.reposition();
      }
    },
    events() {
      this.flickityData.on("pointerDown", () => {
        $liquidHtml.addClass("lqd-carousel-pointer-down");
      });
      this.flickityData.on("pointerUp", () => {
        $liquidHtml.removeClass("lqd-carousel-pointer-down");
      });
      this.flickityData.on("dragStart", () => {
        i("[data-column-clickable]", this.element).css("pointer-events", "none");
        i("a", this.element).css("pointer-events", "none");
      });
      this.flickityData.on("dragEnd", () => {
        i("[data-column-clickable]", this.element).css("pointer-events", "");
        i("a", this.element).css("pointer-events", "");
      });
      if (this.options.marquee)
        return;
      this.flickityData.on("settle", () => {
        this.sliderElement.style.willChange = "auto";
      });
      this.flickityData.on("scroll", () => {
        this.sliderElement.style.willChange = "transform";
        this.doSomethingCrazyWithFilter();
        this.parallax();
        this.changeSlidesShape();
      });
      this.flickityData.on("change", () => {
        this.changeSlidesNumbers();
      });
      i(document).on("added_to_cart", (t, e, n, s) => {
        if (this.$carouselEl.has(s)) {
          this.flickityData.resize();
        }
      });
      i("[data-toggle=tab]").on("hidden.bs.tab shown.bs.tab", (t) => {
        const e = i(t.target);
        const n = i(i(e.attr("href")), e.parent().next(".lqd-tabs-content"));
        const s = n.find("[data-lqd-flickity]");
        let o = s.data("plugin_liquidCarousel");
        if (t.type === "shown") {
          s.on("lqd-carousel-initialized", () => {
            o = s.data("plugin_liquidCarousel");
            const a = i(o.carouselNavElement);
            a.siblings(".carousel-nav").addClass("screen-reader-text").stop().fadeOut(300);
            a.removeClass("screen-reader-text").stop().fadeIn(300);
          });
          if (o) {
            const a = i(o.carouselNavElement);
            a.siblings(".carousel-nav").addClass("screen-reader-text").stop().fadeOut(300);
            a.removeClass("screen-reader-text").stop().fadeIn(300);
          }
        }
      });
    },
    wrapItems() {
      const { middleAlignContent: t, equalHeightCells: e, randomVerOffset: n, skipWrapItems: s } = this.options;
      if (s)
        return;
      const o = this.$carouselEl.children().first();
      if (o.hasClass("flickity-viewport") || o.hasClass("flickity-viewport-wrap")) {
        const r = o.find(".flickity-slider").children();
        r.each((c, p) => {
          const g = i(p);
          const u = g.find(".carousel-item-content").first();
          const m = u.children().not("style").length === 1;
          if (m) {
            g.addClass("has-one-child");
          }
        });
        return;
      }
      this.$carouselEl.children("p, style").insertBefore(this.$carouselEl);
      const a = this.$carouselEl.children();
      a.each((r, c) => {
        const p = i(c);
        if (p.hasClass("vc_ld_carousel_section") || p.hasClass("vc_ld_carousel_marquee_section") || p.hasClass("vc_container-anchor") || p.hasClass("lqd-sticky-stack-nav") || p.is("pre"))
          return;
        const g = p.children().hasClass("carousel-item-inner");
        const u = g ? p.find(".carousel-item-content") : p;
        const m = u.children().not("style, .vc_controls-container").length === 1;
        let f;
        if (p.attr("class")) {
          if (p.hasClass("lqd-prod-item")) {
            f = p.attr("class").split(" ").filter((v) => v !== "lqd-prod-item" && v !== "product");
          } else {
            f = p.attr("class").split(" ").filter((v) => v.includes("vc_hidden-") || v.includes("hidden-") || v.includes("col-") || v.includes("vc_col-"));
          }
        }
        if (p.hasClass("carousel-item")) {
          t && e && !n && p.addClass("align-items-center");
          m && p.addClass("has-one-child");
          if (!p.children(".carousel-item-inner").length) {
            p.wrapInner('<div class="carousel-item-inner" />');
          }
          if (!p.children(".carousel-item-inner").children(".carousel-item-content").length) {
            p.children().wrapInner('<div class="carousel-item-content" />');
          }
        } else {
          p.wrap(`<div class="carousel-item ${m ? "has-one-child" : ""} ${f && f.join(" ")} ${t && e && !n ? "align-items-center" : ""}" />`).wrap(`<div class="carousel-item-inner" />`).wrap(`<div class="carousel-item-content" />`);
        }
      });
    },
    columnsAutoWidth() {
      if (!this.options.columnsAutoWidth)
        return;
      let t = this.$carouselEl.children();
      const e = t.first();
      if (e.hasClass("flickity-viewport") || e.hasClass("flickity-viewport-wrap")) {
        t = e.find(".flickity-slider").children();
      }
      t.each((n, s) => {
        const o = i(s);
        if (o.hasClass("width-is-set"))
          return;
        const a = o.find(".carousel-item-content");
        let r = a.children().not("style").first();
        if (r.hasClass("ld-fancy-heading")) {
          r = r.children();
        }
        this.setColumnWidth(o, r, a);
      });
    },
    setColumnWidth(t, e, n) {
      const s = e.length ? e.outerWidth() : n.outerWidth();
      n.css("width", s);
      t.css("width", "auto");
    },
    carouselNav() {
      if (!this.options.prevNextButtons || !this.flickityData.prevButton || !this.flickityData.nextButton)
        return;
      let t = this.options.buttonsAppendTo;
      if (t === "parent_row") {
        t = liquidIsElementor ? this.$element.closest(".elementor-section") : this.$element.closest(".vc_row");
      }
      if (t === "parent_el") {
        t = this.$element.parent();
      }
      if (t === "self") {
        t = this.$carouselContainer;
      }
      const e = i(this.flickityData.prevButton.element);
      const n = i(this.flickityData.nextButton.element);
      const s = i(t);
      const o = i(`<div class="carousel-nav"></div>`);
      const a = [];
      let r = this.options.carouselEl ? this.$element.attr("id") : this.$carouselContainer.attr("id");
      if (liquidIsElementor) {
        r = `elementor-element elementor-element-${this.$element.closest(".elementor-element").attr("data-id")}`;
      }
      i.each(i(this.$carouselContainer[0].classList), (c, p) => {
        if (p.indexOf("carousel-nav-") >= 0)
          a.push(p);
      });
      o.addClass([...a, r].join(" "));
      this.$carouselContainer.removeClass(a.join(" "));
      o.append([e, n]);
      if (t != null) {
        if (s.hasClass("vc_column_container")) {
          const c = s.children(".vc_column-inner").children(".wpb_wrapper");
          o.appendTo(c);
        } else if (s.hasClass("elementor-column")) {
          const c = s.children(".elementor-widget-wrap");
          c.children(".elementor-empty-view").remove();
          o.appendTo(c);
        } else {
          if (this.options.appendingBtnRel) {
            o.appendTo(this.$carouselEl[this.options.appendingBtnRel](t));
          } else {
            o.appendTo(t);
          }
        }
        s.addClass("carousel-nav-appended");
      } else {
        o.appendTo(this.$carouselContainer);
      }
      this.carouselNavElement = o[0];
      this.options.prevNextButtonsOnlyOnMobile && this.carouselNavElement.classList.add("visible-xs", "visible-sm");
    },
    carouselDots() {
      if (!this.options.pageDots)
        return;
      const { dotsAppendTo: t, hasPageDotsFromOptions: e, carouselEl: n } = this.options;
      const s = this.flickityData.pageDots.holder;
      const o = i(`<div class="carousel-dots ${e ? "carousel-dots-from-options" : "carousel-dots-to-hide"}"></div>`);
      let a = n ? this.$carouselEl.attr("id") : this.$carouselContainer.attr("id");
      let r = t;
      if (liquidIsElementor) {
        a = `elementor-element elementor-element-${this.$element.closest(".elementor-element").attr("data-id")}`;
      }
      if (r === "parent_row") {
        r = liquidIsElementor ? this.$element.closest(".elementor-section") : this.$element.closest(".vc_row");
      }
      if (r === "self") {
        r = this.$carouselContainer;
      }
      o.append(s);
      const c = [a];
      i.each(i(this.$carouselContainer[0].classList), (p, g) => {
        if (g.indexOf("carousel-dots-") >= 0)
          c.push(g);
      });
      o.addClass(c.join(" "));
      if (r != null) {
        if (i(r).hasClass("vc_column_container")) {
          const p = i(r).children(".vc_column-inner ").children(".wpb_wrapper");
          o.appendTo(p);
        } else if (i(r).hasClass("elementor-column")) {
          const p = i(r).children(".elementor-widget-wrap");
          p.children(".elementor-empty-view").remove();
          o.appendTo(p);
        } else {
          o.appendTo(r);
        }
        i(r).addClass("carousel-dots-appended");
      } else {
        o.appendTo(this.$carouselContainer);
      }
      this.carouselDotsElement = o[0];
    },
    carouselMobileDots(t = false) {
      if ((!this.options.pageDots || this.options.marquee) && !t)
        return;
      const { carouselEl: e } = this.options;
      const n = e ? this.$carouselEl.attr("id") : this.$carouselContainer.attr("id");
      const s = [n];
      i.each(i(this.$carouselContainer[0].classList), (c, p) => {
        if (p.indexOf("carousel-dots-mobile-") >= 0)
          s.push(p);
      });
      const o = i(this.flickityData.pageDots.holder).clone(true);
      const a = i(`<div class="carousel-dots-mobile carousel-dots-style4 ${s.join(" ")}"></div>`);
      a.append(o);
      if (this.carouselDotsElement && this.$carouselEl.has(this.carouselDotsElement).length) {
        a.insertBefore(this.carouselDotsElement);
      } else {
        a.appendTo(this.$carouselContainer);
        i(this.carouselDotsElement).addClass("hidden-xs hidden-sm md:hidden");
      }
      this.carouselMobileDotsElement = a[0];
      const r = this.carouselMobileDotsElement.querySelectorAll(".dot");
      r.forEach((c, p) => {
        c.addEventListener("click", () => {
          this.flickityData.select(p);
          this.carouselMobileDotsClasslist(c, p);
        });
      });
      this.flickityData.on("select", (c) => this.carouselMobileDotsClasslist.call(this, r[c], c));
    },
    carouselMobileDotsClasslist(t, e) {
      if (!t)
        return;
      t.classList.add("is-selected");
      const n = [...this.carouselMobileDotsElement.querySelectorAll(".dot")].filter((s, o) => e !== o);
      n.forEach((s) => {
        s.classList.remove("is-selected");
      });
    },
    carouselDotsNumbers() {
      if (!this.options.pageDots || this.options.dotsIndicator !== "numbers")
        return;
      const { flickityData: t } = this;
      const { numbersStyle: e } = this.options;
      const n = i(t.pageDots.holder);
      let s;
      if (e === "circle") {
        const o = this.createSlideNumbers(false);
        s = i('<div class="lqd-carousel-slides-numbers d-inline-flex pos-rel inline-flex relative">' + '<svg xmlns="http://www.w3.org/2000/svg" width="150" height="152" viewBox="-2 0 154 150" class="w-100 h-100 w-full h-full">' + '<circle fill="none" cx="75" cy="75" r="74.5"/>' + '<path fill="none" stroke-dashoffset="' + this.carouselSlidesPathLength + '" stroke-dasharray="' + this.carouselSlidesPathLength + '" stroke-width="3" x="2" d="M75,150 C116.421356,150 150,116.421356 150,75 C150,33.5786438 116.421356,0 75,0 C33.5786438,0 0,33.5786438 0,75 C0,116.421356 33.5786438,150 75,150 Z"/>' + "</svg>" + "</div>");
        o.prependTo(s);
      } else if (e === "line") {
        const o = this.createSlideNumbers(true);
        s = i('<div class="lqd-carousel-slides-numbers d-inline-flex pos-rel inline-flex relative lqd-carousel-numbers-line">' + '<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" width="200" height="1" viewBox="0 0 200 1" class="w-100 h-100 w-full h-full">' + '<path opacity="0.15" d="M1 1 201 1 201 2 1 2z"></path>' + '<path stroke-dashoffset="' + this.carouselSlidesPathLength + '" stroke-dasharray="' + this.carouselSlidesPathLength + '" d="M1 1 201 1 201 2 1 2z"></path>' + "</svg>" + "</div>");
        o.prependTo(s);
      }
      n.appendTo(s);
      s.appendTo(this.carouselDotsElement);
      this.$carouselTotalSlides = i(".lqd-carousel-slides-total", s);
      this.$carouselCurrentSlide = i(".lqd-carousel-slides-current", s);
      this.$carouselSlidesShape = i("svg", s);
    },
    addSlideNumbersToArrows() {
      if (!this.options.prevNextButtons || !this.options.addSlideNumbersToArrows)
        return;
      const { prevButton: t } = this.flickityData;
      const e = t.element;
      const n = this.createSlideNumbers();
      n.insertAfter(e);
      this.$carouselTotalSlides = i(".lqd-carousel-slides-total", i(e).next(".lqd-carousel-slides"));
      this.$carouselCurrentSlide = i(".lqd-carousel-slides-current", i(e).next(".lqd-carousel-slides"));
    },
    createSlideNumbers(t) {
      const e = (this.flickityData.slides.length < 10 && t ? "0" : "") + this.flickityData.slides.length;
      const n = i(`<div class="lqd-carousel-slides d-flex align-items-center justify-content-center flex items-center justify-center lqd-overlay">
				<div class="lqd-carousel-slides-current d-inline-block overflow-hidden ws-nowrap text-center inline-block whitespace-nowrap"></div>
				<svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="width: 1em; height: 1em;"><path fill="currentColor" d="M6 15.77a1 1 0 0 1 1-1h18.05a1 1 0 1 1 0 2h-18.04a1 1 0 0 1-1.01-1z"></path></svg>
				<div class="lqd-carousel-slides-total">${e}</div>
			</div>`);
      return n;
    },
    addSlidesCurrentNumbers() {
      if (this.options.dotsIndicator !== "numbers" && !this.options.addSlideNumbersToArrows) {
        return false;
      }
      const { flickityData: t } = this;
      const { numbersStyle: e } = this.options;
      const n = e === "line";
      const s = i('<div class="lqd-carousel-slides-current-inner d-inline-block pos-rel inline-block relative" />');
      for (let o = 1;o <= t.slides.length; o++) {
        s.append(`<span class="d-inline-block inline-block" style="text-indent: 0;">${o < 10 && n ? "0" : ""}${o}</span>`);
      }
      setTimeout(() => {
        const o = s.children("span");
        const a = o.map((c, p) => i(p).outerWidth(true));
        const r = Math.ceil(Math.max(...a));
        this.$carouselCurrentSlide.add(o).css("width", r);
      }, 0);
      s.appendTo(this.$carouselCurrentSlide);
      this.$carouselCurrentSlideInner = s;
    },
    changeSlidesNumbers() {
      if (this.options.dotsIndicator !== "numbers" && !this.options.addSlideNumbersToArrows) {
        return false;
      }
      const { flickityData: t } = this;
      const { selectedIndex: e } = t;
      const n = this.$carouselCurrentSlideInner.children("span").eq(e)[0];
      this.$carouselCurrentSlideInner.css({
        transition: "transform 0.5s",
        transform: `translateX(${n.offsetLeft * -1}px)`
      });
    },
    changeSlidesShape() {
      if (this.options.pageDots && this.options.dotsIndicator !== "numbers") {
        return false;
      }
      const { flickityData: t } = this;
      const e = this.$carouselSlidesShape.find("path").last();
      const n = this.carouselSlidesPathLength;
      const s = Math.floor(Math.abs(Math.floor(t.x + t.cursorPosition)) / Math.abs(Math.floor(t.slidesWidth)) * 100);
      const o = n - s / 100 * n;
      e.css("stroke-dashoffset", Math.abs(o));
    },
    fullwidthSide() {
      if (!this.options.fullwidthSide)
        return;
      const t = i(this.flickityData.viewport);
      const e = this.flickityData.size.width - parseInt(this.$element.css("padding-left"), 10);
      const n = t.offset();
      const s = this.windowWidth - (e + n.left);
      const o = !this.isRTL ? "marginRight" : "marginLeft";
      const a = !this.isRTL ? "paddingRight" : "paddingLeft";
      let r = t.parent(".flickity-viewport-wrap");
      let c = r.length ? r : i('<div class="flickity-viewport-wrap overflow-hidden" />');
      if (!r.length) {
        t.wrap(c);
        t.removeClass("overflow-hidden");
        c = t.parent();
        t.css("overflow", "visible");
      }
      c.css({
        [o]: "",
        [a]: ""
      });
      c.css({
        [o]: !this.isRTL ? s >= 0 ? (s - 1) * -1 : s - 1 : n.left * -1,
        [a]: !this.isRTL ? Math.abs(s - 1) : Math.abs(n.left - 1)
      });
      this.flickityData.resize();
    },
    randomVerOffset() {
      if (this.options.randomVerOffset) {
        const t = this.flickityData.cells;
        let e = 0;
        for (let n = 0;n < t.length; n++) {
          const s = i(t[n].element);
          const o = t[n].size.height;
          if (o > e) {
            e = o;
          }
          const a = e - o;
          const r = (Math.random() * a).toFixed();
          s.children(".carousel-item-inner").css("top", r + "px");
        }
      }
    },
    navOffsets() {
      const { options: t } = this;
      const { navOffsets: e } = t;
      const n = i(this.carouselNavElement);
      if (e && n && this.flickityData.options.prevNextButtons) {
        const s = i(this.flickityData.prevButton.element);
        const o = i(this.flickityData.nextButton.element);
        if (e.nav) {
          for (const a in e.nav) {
            let r = e.nav[a].trim();
            r.match(/^-?\d*(\.\d+){0,1}(%|in|cm|mm|em|rem|ex|pt|pc|px|vw|vh|vmin|vmax)$/) || (r = isNaN(parseFloat(r)) ? "" : parseFloat(r) + "px");
            n.css(a.trim(), r);
          }
        }
        s.css({
          left: e.prev
        });
        o.css({
          right: e.next
        });
      }
    },
    setElementNavArrow() {
      if (!this.options.prevNextButtons) {
        return false;
      }
      const t = this.navArrows;
      const e = this.flickityData.prevButton ? this.flickityData.prevButton.element : null;
      const n = this.flickityData.nextButton ? this.flickityData.nextButton.element : null;
      let s = this.options.navArrow;
      let o;
      let a;
      if (typeof s !== "object") {
        s = s - 1;
        if (!this.isRTL) {
          o = i(t[s].prev);
          a = i(t[s].next);
        } else {
          o = i(t[s].next);
          a = i(t[s].prev);
        }
      } else {
        o = i(this.options.navArrow.prev);
        a = i(this.options.navArrow.next);
      }
      if (e || n) {
        i(e).find("svg").remove().end().append(o);
        i(n).find("svg").remove().end().append(a);
      }
    },
    navArrows: [
      {
        prev: '<svg width="27" height="16" viewBox="0 0 27 16" xmlns="http://www.w3.org/2000/svg"> <path d="M2.5 7.75H27V9H2.5L9 15L8 16L0 8.5L8 0L9 1L2.5 7.75Z" /> </svg>',
        next: '<svg width="27" height="16" viewBox="0 0 27 16" xmlns="http://www.w3.org/2000/svg"> <path d="M24.5 7.75H0V9H24.5L18 15L19 16L27 8.5L19 0L18 1L24.5 7.75Z"/> </svg>'
      },
      {
        prev: '<svg width="32" height="18" viewBox="0 0 32 18" xmlns="http://www.w3.org/2000/svg"> <path d="M8.77638 0.223663L10.2018 1.64911L3.85885 7.99209H32V10.008H3.85885L10.2018 16.3509L8.77638 17.7763L1.71102e-06 8.99997L8.77638 0.223663Z"/> </svg> ',
        next: '<svg width="32" height="18" viewBox="0 0 32 18" xmlns="http://www.w3.org/2000/svg"> <path d="M23.2236 0.223663L21.7982 1.64911L28.1412 7.99209H0V10.008H28.1412L21.7982 16.3509L23.2236 17.7763L32 8.99997L23.2236 0.223663Z"/> </svg>'
      },
      {
        prev: '<svg width="20" height="18" viewBox="0 0 32 28" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9881 0.478424L0.377096 13.0899C-0.12566 13.5922 -0.12566 14.4072 0.377096 14.91L12.9881 27.5214C13.2395 27.7728 13.5685 27.8985 13.8979 27.8985C14.2274 27.8985 14.5564 27.7728 14.8077 27.5214C15.3105 27.0191 15.3105 26.2041 14.8077 25.7018L4.39347 15.2871H30.7132C31.424 15.2871 32.0001 14.7105 32.0001 14.0002C32.0001 13.2898 31.4239 12.7133 30.7132 12.7133H4.39338L14.8077 2.29851C15.3105 1.79619 15.3105 0.981181 14.8077 0.478424C14.305 -0.0238953 13.4909 -0.0238953 12.9881 0.478424Z"/> </svg>',
        next: '<svg width="20" height="18" viewBox="0 0 32 28" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M19.012 0.478424L31.623 13.0899C32.1257 13.5921 32.1257 14.4072 31.623 14.9099L19.012 27.5214C18.7606 27.7728 18.4316 27.8985 18.1021 27.8985C17.7727 27.8985 17.4437 27.7728 17.1923 27.5214C16.6896 27.0191 16.6896 26.2041 17.1923 25.7018L27.6066 15.287H1.28687C0.57605 15.287 0 14.7105 0 14.0002C0 13.2898 0.576111 12.7132 1.28687 12.7132H27.6067L17.1923 2.29849C16.6896 1.79617 16.6896 0.981171 17.1923 0.478424C17.6951 -0.0238953 18.5092 -0.0238953 19.012 0.478424Z"/> </svg>'
      },
      {
        prev: '<svg width="10" height="19" viewBox="0 0 33 60" xmlns="http://www.w3.org/2000/svg"> <path d="M1.41739 28L28.823 0.670159C29.7209 -0.224745 31.1747 -0.22324 32.0711 0.674788C32.9668 1.5727 32.9645 3.02725 32.0664 3.92285L6.29209 29.626L32.0674 55.3291C32.9653 56.2248 32.9676 57.6784 32.072 58.5765C31.6226 59.0266 31.0339 59.2517 30.4452 59.2517C29.8581 59.2517 29.2717 59.0281 28.8231 58.5811L1.41739 31.252C0.984926 30.8217 0.742248 30.2361 0.742248 29.626C0.742248 29.0159 0.98562 28.4311 1.41739 28Z"/> </svg>',
        next: '<svg width="10" height="19" viewBox="0 0 33 60" xmlns="http://www.w3.org/2000/svg"> <path d="M32.0671 28L4.66149 0.670159C3.76358 -0.224745 2.30984 -0.22324 1.41343 0.674788C0.517715 1.5727 0.52003 3.02725 1.41806 3.92285L27.1924 29.626L1.41713 55.3291C0.519219 56.2248 0.516905 57.6784 1.4125 58.5765C1.86186 59.0266 2.45056 59.2517 3.03926 59.2517C3.62645 59.2517 4.21283 59.0281 4.66138 58.5811L32.0671 31.252C32.4996 30.8217 32.7422 30.2361 32.7422 29.626C32.7422 29.0159 32.4989 28.4311 32.0671 28Z"/> </svg>'
      },
      {
        prev: '<svg width="16" height="17" viewBox="0 0 16 17" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.612 16.0721C15.6116 16.2693 15.4515 16.4289 15.2542 16.4286C15.1593 16.4286 15.0684 16.3908 15.0014 16.3236L7.14431 8.46655C7.00489 8.32706 7.00489 8.101 7.14431 7.96154L15.0014 0.104495C15.141 -0.0351572 15.3674 -0.0351572 15.5071 0.104495C15.6467 0.244147 15.6467 0.47055 15.5071 0.610202L7.90217 8.21436L15.5071 15.8186C15.5744 15.8857 15.6122 15.977 15.612 16.0721ZM9.18351 16.0721C9.18314 16.2693 9.02297 16.4289 8.82573 16.4286C8.73118 16.4286 8.64051 16.3911 8.57358 16.3243L0.716562 8.46727C0.577143 8.32778 0.577143 8.10171 0.716562 7.96226L8.57361 0.105214C8.71199 -0.0284448 8.9314 -0.0284448 9.06981 0.105214C9.21167 0.242255 9.21562 0.468357 9.07858 0.610219L1.47368 8.21438L9.07858 15.8186C9.14591 15.8857 9.18368 15.977 9.18351 16.0721Z"/> </svg>',
        next: '<svg width="16" height="17" viewBox="0 0 16 17" xmlns="http://www.w3.org/2000/svg"> <path d="M0.612 16.0721C0.61237 16.2693 0.772547 16.4289 0.969787 16.4286C1.06467 16.4286 1.15564 16.3908 1.22264 16.3236L9.07969 8.46655C9.21911 8.32706 9.21911 8.101 9.07969 7.96154L1.22264 0.104495C1.08299 -0.0351572 0.856586 -0.0351572 0.716933 0.104495C0.577281 0.244147 0.577281 0.47055 0.716933 0.610202L8.32183 8.21436L0.716933 15.8186C0.649602 15.8857 0.611834 15.977 0.612 16.0721Z"/> <path d="M7.04049 16.0721C7.04085 16.2693 7.20103 16.4289 7.39827 16.4286C7.49282 16.4286 7.58349 16.3911 7.65042 16.3243L15.5074 8.46727C15.6469 8.32778 15.6469 8.10171 15.5074 7.96226L7.65039 0.105214C7.51201 -0.0284448 7.2926 -0.0284448 7.15419 0.105214C7.01233 0.242255 7.00838 0.468357 7.14542 0.610219L14.7503 8.21438L7.14542 15.8186C7.07809 15.8857 7.04032 15.977 7.04049 16.0721Z"/> </svg>'
      },
      {
        prev: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="13.4" viewBox="0 0 16 13.4"><path d="M9.3,1.3,7.9,2.7,12.2,7H0V9H12.2L7.9,13.3l1.4,1.4L16,8Z" transform="translate(16 14.7) rotate(180)"/></svg>',
        next: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="13.4" viewBox="0 0 16 13.4"><path d="M9.3,13.4,7.9,12l4.3-4.3H0v-2H12.2L7.9,1.4,9.3,0,16,6.7Z"/></svg>'
      }
    ],
    setEqualHeightCells() {
      if (!this.options.equalHeightCells || this.element.classList.contains("flickity-equal-cells"))
        return;
      const { carouselEl: t } = this;
      Flickity.prototype._createResizeClass = function() {
        t.classList.add("flickity-equal-cells");
      };
      Flickity.createMethods.push("_createResizeClass");
    },
    parallax() {
      if (!this.options.parallax || liquidIsMobile()) {
        return false;
      }
      this.flickityData.cells.forEach((t, e) => {
        const n = !this.isRTL ? -1 : 1;
        const s = (t.target + this.flickityData.x) * n / 3;
        const o = i(t.element);
        const a = o.find(this.options.parallaxEl);
        if (!a.parent(".ld-carousel-parallax-wrap").length) {
          a.wrap('<div class="ld-carousel-parallax-wrap overflow-hidden"></div>');
        }
        if (a.is(":only-child")) {
          a.css({
            willChange: "transform",
            "-webkit-transform": `translateX(${s}px)`,
            transform: `translateX(${s}px)`
          });
        }
      });
    },
    controllingCarousels() {
      const { options: t } = this;
      const { controllingCarousels: e } = t;
      if (typeof e !== "undefined" && e !== null && e.length) {
        const n = i(e.map((s) => i(s).children("[data-lqd-flickity]")));
        i.each(n, (s, o) => {
          const a = i(o);
          a.imagesLoaded(() => {
            const r = a.data("plugin_liquidCarousel");
            if (r) {
              r.carouselInitPromise.then(() => {
                a.parent().addClass("is-controlled-carousel");
                r.carouselMobileDotsElement && r.carouselMobileDotsElement.classList.add("hidden");
                this.flickityData.on("change", (c) => {
                  r.flickityData.select(c);
                });
                r.flickityData.on("change", (c) => {
                  this.flickityData.select(c);
                });
              });
            }
          });
        });
      }
    },
    getCellsArray() {
      return this.flickityData.cells.map((t) => t.element);
    },
    doSomethingCrazyWithFilter() {
      if (!this.options.doSomethingCrazyWithFilters || liquidIsMobile() || this.windowWidth <= 992)
        return false;
      const t = i(".lqd-pf-carousel-header", this.$carouselContainer)[0];
      if (!t)
        return false;
      const { x: e, size: n } = this.flickityData;
      const s = this.flickityData.cells.filter((p) => i(p.element).is(":visible"))[0];
      if (!s)
        return false;
      const o = s.size.width;
      const a = gsap.utils.normalize(-o, 0, this.isRTL ? e + n.width : e);
      const r = gsap.utils.mapRange(0, -o, 0, -100, this.isRTL ? e + n.width : e);
      const c = gsap.utils.mapRange(0, -o, 0, -300, this.isRTL ? e + n.width : e);
      i(t).parent().addClass("perspective");
      gsap.to(t, {
        opacity: a,
        z: c,
        rotationY: r,
        duration: 0.6,
        ease: "expo.out"
      });
    },
    filtersInit() {
      if (!this.options.filters)
        return;
      const { filtersCounter: t, filters: e } = this.options;
      const n = i(e);
      const s = i("[data-filter]", n);
      const o = n.siblings(".lqd-filter-dropdown");
      s.each((a, r) => {
        const c = i(r);
        const p = c.attr("data-filter");
        t && this.addFilterCounts(c, p);
        c.off("click");
        c.on("click.lqdCarouselFilter", () => {
          if (!c.hasClass("active")) {
            c.addClass("active").siblings().removeClass("active");
            this.filterAnimateStart(p);
          }
        });
      });
      if (o.length) {
        i("select", o).on("selectmenuchange", (a, r) => {
          const c = r.item.value;
          this.filterAnimateStart(c);
        });
      }
    },
    addFilterCounts(t, e) {
      const n = e === "*" ? this.flickityData.cells.length : i(e, this.element).length;
      const s = i(`
				<sup class="lqd-filter-counter">
					<span>${n}</span>
				</sup>`);
      s.appendTo(t);
    },
    filterAnimateStart(t) {
      const e = this.getCellsArray().filter((n) => !n.classList.contains("hidden"));
      gsap.killTweensOf(e);
      gsap.to(e, {
        x: "-=10%",
        opacity: 0,
        ease: "power4.inOut",
        duration: 0.5,
        stagger: 0.05,
        clearProps: "x",
        onStart: () => {
          if (this.options.autoplay) {
            this.flickityData.pausePlayer();
          }
          if (this.options.equalHeightCells) {
            const n = i(this.flickityData.cells);
            const s = this.flickityData.size.height;
            n.css("minHeight", s);
          }
          i(e).css({
            transition: "none"
          });
        },
        onComplete: () => {
          this.filterItems(t);
        }
      });
    },
    filterItems(t) {
      const e = i(this.getCellsArray());
      this.$element.find(".hidden").removeClass("hidden");
      if (t !== "*") {
        e.not(t).addClass("hidden");
      }
      if (this.options.equalHeightCells) {
        e.css("minHeight", "");
      }
      this.flickityData.resize();
      this.flickityData.reposition();
      if (this.options.autoplay) {
        this.flickityData.pausePlayer();
      }
      if (this.flickityData.slides.length <= 1) {
        this.flickityData.options.wrapAround = false;
        this.flickityData.options.draggable = false;
      } else {
        this.flickityData.options.wrapAround = this.originalWrapAroundOption;
        this.flickityData.options.draggable = this.originalDraggableOption;
      }
      this.filterAnimateComplete();
    },
    filterAnimateComplete() {
      const t = this.getCellsArray().filter((e) => !e.classList.contains("hidden"));
      gsap.killTweensOf(t);
      this.flickityData.resize();
      gsap.fromTo(t, {
        x: "+=10%",
        opacity: 0
      }, {
        x: (e, n) => {
          const s = parseInt(gsap.getProperty(n, "x", "%"), 10);
          return `${s - 10}%`;
        },
        opacity: 1,
        duration: 0.5,
        stagger: 0.05,
        ease: "power4.out",
        onComplete: () => {
          i(t).css({
            transition: "",
            opacity: ""
          });
          if (this.options.autoplay && this.flickityData.slides.length > 1) {
            this.flickityData.unpausePlayer();
          }
          this.flickityData.reposition();
          this.flickityData.select(0);
        }
      });
      if (this.carouselMobileDotsElement) {
        this.carouselMobileDotsElement.remove();
        this.carouselMobileDots(true);
      }
    },
    marquee() {
      if (!this.options.marquee)
        return;
      this.marqueeIsPaused = true;
      this.flickityData.x = 0;
      const t = () => {
        new IntersectionObserver(([e]) => {
          if (e.isIntersecting) {
            this.sliderElement.style.willChange = "transform";
            this.marqueePlay();
          } else {
            this.sliderElement.style.willChange = "";
            this.marqueePause();
          }
        }, { rootMargin: "50%" }).observe(this.element);
      };
      if (this.options.pauseAutoPlayOnHover) {
        this.element.addEventListener("mouseenter", this.marqueePause.bind(this), false);
        this.element.addEventListener("focusin", this.marqueePause.bind(this), false);
        this.element.addEventListener("mouseleave", this.marqueePlay.bind(this), false);
        this.element.addEventListener("focusout", this.marqueePlay.bind(this), false);
      }
      this.flickityData.on("dragStart", this.marqueePause.bind(this));
      this.flickityData.on("dragEnd", !this.options.pauseAutoPlayOnHover && this.marqueePlay.bind(this));
      t();
    },
    marqueePlay() {
      if (!this.marqueeIsPaused)
        return;
      this.marqueeIsPaused = false;
      this.marqueeUpdate();
    },
    marqueePause() {
      this.marqueeIsPaused = true;
      this.marqueeRAF && cancelAnimationFrame(this.marqueeRAF);
    },
    marqueeUpdate() {
      if (this.marqueeIsPaused || !this.flickityData.slides)
        return;
      this.flickityData.x = (this.flickityData.x - this.options.marqueeTickerSpeed) % this.flickityData.slideableWidth;
      this.flickityData.settle(this.flickityData.x);
      this.marqueeRAF = window.requestAnimationFrame(this.marqueeUpdate.bind(this));
    },
    destroy() {
      i(window).off("resize.lqdCarousel");
    }
  };
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this);
      const n = { ...e.data("lqd-flickity"), ...t };
      const s = document.body.hasAttribute("data-disable-carousel-onmobile");
      if (liquidIsMobile() && s && !n.forceEnableOnMobile) {
        e.find(".flickity-viewport").css("overflow-x", "auto");
        return;
      }
      if (n.pageDots) {
        n.hasPageDotsFromOptions = true;
      } else {
        n.hasPageDotsFromOptions = false;
      }
      if (!n.forceDisablePageDots) {
        n.pageDots = true;
      }
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, n));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-flickity]").liquidCarousel();
});
(function(i) {
  const l = "liquidCarouselFalcate";
  let d = {};

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.ms = null;
      this.activeItems = [0, 1, 2];
      this.loopCount = 2;
      this.$carouselItems = i(".carousel-items", this.element);
      this.$carouselCells = [];
      this.isMobile = liquidIsMobile();
      if (this.$carouselItems.length) {
        this.onImagesLoaded();
      }
    }
    onImagesLoaded() {
      imagesLoaded(this.element, () => {
        if (!this.isMobile) {
          this.addCarouselCellsDetails();
          this.initFalcateCarousel();
        } else {
          this.$element.addClass("carousel-falcate-mobile");
          this.initFlickityCarousel();
        }
      });
    }
    addCarouselCellsDetails() {
      i.each(i(".carousel-item", this.element), (t, e) => {
        const n = {};
        const s = i(e);
        n.el = e;
        n.width = s.outerWidth(true);
        n.height = s.outerHeight(true);
        this.$carouselCells.push(n);
      });
      this.$carouselItems.hide();
    }
    initFalcateCarousel() {
      this.ms = new MomentumSlider({
        el: this.element,
        multiplier: 0.6,
        range: [0, this.$carouselCells.length - 1],
        vertical: true,
        loop: this.loopCount,
        currentIndex: this.activeItems[1],
        rangeContent: (t) => i(this.$carouselCells[t].el).html(),
        style: {
          transform: [
            {
              translateX: [0, -100]
            }
          ]
        },
        customStyles: (t, e, n) => {
          this.customStyles(t, e, n);
          this.updateContainerHeight(t, e, n);
        },
        change: () => {
          this.fadeInActiveItems();
          this.updateContainerHeight();
        }
      });
      if (i("body").hasClass("lazyload-enabled") && window.liquidLazyload) {
        window.liquidLazyload.update();
      }
    }
    fadeInActiveItems() {
      if (!this.ms) {
        const t = this.$element.find(".ms-container");
        const e = i(".ms-slide", t);
        this.activeItems.forEach((n) => {
          const s = n + this.loopCount;
          i(e[s]).css("opacity", 1);
        });
      }
    }
    fadeOutInactiveItems(t) {
      if (!this.ms)
        return false;
      const e = t + this.loopCount - 1;
      const n = t + this.loopCount;
      const s = t + this.loopCount + 1;
      i(this.ms.msSlides).each((o, a) => {
        const r = i(a);
        r.addClass("ms-slide-inactive");
        [e, n, s].map((c) => {
          o === c && r.removeClass("ms-slide-inactive");
        });
      });
    }
    updateContainerHeight(t, e) {
      if (!this.ms) {
        const o = this.$element.find(".ms-container");
        const a = i(".ms-slide", o);
        const r = i(a[this.activeItems[0 + this.loopCount]]);
        let c = 0;
        this.activeItems.forEach((p) => {
          const g = p + this.loopCount;
          c += i(a[g]).outerHeight();
        });
        o.css({
          height: c,
          transform: `translateY(${r.outerHeight()}px)`
        });
        return false;
      }
      const { msSlides: n } = this.ms;
      let s = 0;
      i.each([i(n[t - 1]), i(n[t]), i(n[t + 1])], (o, a) => {
        s += i(a).outerHeight();
      });
      s = Math.round((Math.abs(e) - s) / -1);
      this.$element.height(s);
    }
    customStyles(t, e, n) {
      if (!this.ms)
        return false;
      const { msSlides: s } = this.ms;
      const o = (Math.abs(e) - 1) / -1;
      i(s[t]).css({
        opacity: 1
      });
      if (n) {
        const a = i(s[t - 1]);
        const r = Math.round((e - a.outerHeight()) / -1);
        a.css({
          opacity: o
        });
        i(this.ms.msContainer).css({
          transform: `translateY(${r}px)`
        });
      }
      if (!n) {
        const a = i(s[t + 1]);
        const r = i(s[t - 1]);
        const c = Math.round((Math.abs(e) - r.outerHeight()) / -1);
        a.css({
          opacity: o
        });
        i(this.ms.msContainer).css({
          transform: `translateY(${c}px)`
        });
      }
    }
    initFlickityCarousel() {
      this.$carouselItems.liquidCarousel({
        pageDots: true
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("carousel-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".carousel-falcate").liquidCarouselFalcate();
});
(function(i) {
  const l = "liquidCarouselStack";
  let d = {
    autoplay: false,
    distDragBack: 150,
    distDragMax: 450,
    isRandom: false,
    onUpdateStack: function(t) {
      return false;
    }
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.$container = i(".carousel-items", this.element);
      this.$prevBtn = i(".lqd-carousel-stack-prev", this.element);
      this.$nextBtn = i(".lqd-carousel-stack-next", this.element);
      this.items = this.$container.children(".carousel-item").get();
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.isInit = false;
      this.moveVector = {};
      this.draggie = null;
      this._init();
      if (this.options.autoplay) {
        this.autoplay();
      }
    }
    autoplay() {
      if (isNaN(this.options.autoplay) || this.options.autoplay <= 0)
        return;
      this.autoplayTimeout = setTimeout(() => {
        this._moveAway("next");
      }, this.options.autoplay);
    }
    shuffle(t) {
      let e = t.length;
      let n;
      let s;
      while (e) {
        s = Math.floor(Math.random() * e--);
        n = t[e];
        t[e] = t[s];
        t[s] = n;
      }
      return t;
    }
    setTransformStyle(t, e) {
      t.style.WebkitTransform = e;
      t.style.msTransform = e;
      t.style.transform = e;
    }
    initSetting() {
      this.itemsCount = this.items.length;
      this._setContainerHeight();
      this._setStackStyle();
      if (this.itemsCount <= 1)
        return;
      if (!this.isInit) {
        this._initDragg();
        this._initEvents();
      }
      this.isInit = true;
    }
    _init() {
      if (this.options.isRandom) {
        this.shuffle(this.items);
      }
      this.current = 0;
      this.initSetting();
    }
    _initEvents() {
      const t = liquidDebounce(this.onResize.bind(this), 750);
      this.draggie.on("dragMove", (e, n, s) => {
        this._onDragMove.call(this, e, s);
      });
      this.draggie.on("dragEnd", (e) => {
        this._onDragEnd.call(this, e);
      });
      this.$prevBtn.on("click", this.goToPrev.bind(this));
      this.$nextBtn.on("click", this.goToNext.bind(this));
      $liquidWindow.on("resize.lqdCarouselStack", t);
    }
    _setContainerHeight() {
      this.element.style.transition = `height 0.3s`;
      this.element.style.height = `${i(this._firstItem()).outerHeight()}px`;
    }
    _setStackStyle(t) {
      var e = this._firstItem(), n = this._secondItem(), s = this._thirdItem();
      this.items.forEach((o) => o.classList.remove("is-first", "is-second", "is-third"));
      if (e) {
        e.style.zIndex = 4;
        e.classList.add("is-first");
        gsap.to(e, {
          ease: "power4.out",
          duration: 0.6,
          x: 0,
          y: 0,
          z: 0
        });
      }
      if (n) {
        n.style.zIndex = 3;
        n.classList.add("is-second");
        gsap.to(n, {
          startAt: {
            x: 0,
            y: 0,
            z: () => {
              if (!t || t === "next") {
                return -180;
              } else {
                return 0;
              }
            }
          },
          x: 0,
          y: 0,
          z: () => {
            if (!t || t === "next") {
              return -80;
            } else {
              return -80;
            }
          },
          ease: "power4.out",
          duration: 0.6
        });
      }
      if (s) {
        s.style.zIndex = 2;
        s.classList.add("is-third");
        gsap.to(s, {
          startAt: {
            x: 0,
            y: 0,
            z: () => {
              if (!t || t === "next") {
                return -280;
              } else {
                return 0;
              }
            }
          },
          x: 0,
          y: 0,
          z: () => {
            if (!t || t === "next") {
              return -180;
            } else {
              return -180;
            }
          },
          duration: 0.6,
          ease: "power4.out"
        });
      }
    }
    _moveAway(t) {
      if (this.animating)
        return;
      const e = this._getTranslateVal(t);
      let n;
      let s = false;
      this.animating = true;
      this._disableDragg();
      if (!t || t === "next") {
        n = this.draggie.element;
      } else {
        n = this.draggie.element.previousElementSibling || this.items[this.itemsCount - 1];
        this.draggie.element.style.zIndex = 3;
        n.style.zIndex = 4;
      }
      gsap.killTweensOf(n);
      const o = gsap.to(n, {
        startAt: {
          z: e.z[0],
          opacity: () => {
            if (t !== "prev") {
              return 1;
            } else {
              return 0;
            }
          }
        },
        duration: 0.6,
        ease: "power4.out",
        x: e.x,
        y: e.y || 0,
        z: e.z[1],
        opacity: () => {
          if (t !== "prev") {
            return 0;
          } else {
            return 1;
          }
        },
        onUpdate: () => {
          if (o.progress() >= 0.5 && !s) {
            s = true;
            this.onEndTransFn(t);
          }
        },
        onComplete: () => {
          this.onCompleteTransFn(n);
        }
      });
      const a = this._secondItem();
      const r = this._thirdItem();
      if (a) {
        gsap.to(a, {
          ease: "power4.out",
          duration: 0.6,
          x: 0,
          y: 0,
          z: -80
        });
      }
      if (r) {
        gsap.to(r, {
          ease: "power4.out",
          duration: 0.6,
          x: 0,
          y: 0,
          z: -180
        });
      }
    }
    onEndTransFn(t) {
      gsap.to(this.draggie.element, {
        x: 0,
        y: 0,
        z: -180,
        ease: "power4.out",
        duration: 0.6,
        onComplete: () => {
          this.draggie.element.style.transform = "";
        }
      });
      if (!t || t === "next") {
        this.draggie.element.style.left = this.draggie.element.style.top = "0px";
        this.draggie.element.style.zIndex = -1;
        this.current = this.current < this.itemsCount - 1 ? this.current + 1 : 0;
      } else {
        this.draggie.element.style.zIndex = 4;
        this.current = this.current > 0 ? this.current - 1 : this.itemsCount - 1;
      }
      this._setStackStyle(t);
      this._initDragg();
      this._initEvents();
      this.options.onUpdateStack(this.current);
      this._setContainerHeight();
    }
    onCompleteTransFn(t) {
      this.animating = false;
      if (this.autoplayTimeout) {
        clearTimeout(this.autoplayTimeout);
      }
      if (this.options.autoplay) {
        this.autoplay();
      }
      t.style.opacity = "";
    }
    _moveBack() {
      const t = this._secondItem();
      const e = this._thirdItem();
      gsap.to(this.draggie.element, {
        startAt: {
          x: this.moveVector.x,
          y: this.moveVector.y
        },
        ease: "power4.out",
        duration: 0.6,
        x: 0,
        y: 0,
        z: 0
      });
      this.draggie.element.style.left = "0px";
      this.draggie.element.style.top = "0px";
      if (t) {
        gsap.to(t, {
          ease: "power4.out",
          duration: 0.6,
          x: 0,
          y: 0,
          z: -80
        });
      }
      if (e) {
        gsap.to(e, {
          ease: "power4.out",
          duration: 0.6,
          x: 0,
          y: 0,
          z: -180
        });
      }
    }
    _onDragMove(t, e) {
      this.moveVector = e;
      if (this._outOfBounds()) {
        this._moveAway();
      } else {
        const n = this._secondItem();
        const s = this._thirdItem();
        if (n) {
          gsap.to(n, {
            ease: "power4.out",
            duration: 0.3,
            x: this.moveVector.x * 0.6,
            y: this.moveVector.y * 0.6
          });
        }
        if (s) {
          gsap.to(s, {
            ease: "power4.out",
            duration: 0.3,
            x: this.moveVector.x * 0.3,
            y: this.moveVector.y * 0.3
          });
        }
      }
    }
    _onDragEnd() {
      if (this._outOfBounds())
        return;
      if (this._outOfSight()) {
        this._setContainerHeight();
        this._moveAway();
      } else {
        this._moveBack();
      }
    }
    _initDragg() {
      this.draggie = new Draggabilly(this.items[this.current], { handle: ".lqd-carousel-handle" });
    }
    _disableDragg() {
      this.draggie.disable();
    }
    _outOfBounds() {
      return Math.abs(this.moveVector.x) > this.options.distDragMax || Math.abs(this.moveVector.y) > this.options.distDragMax;
    }
    _outOfSight() {
      return Math.abs(this.moveVector.x) > this.options.distDragBack || Math.abs(this.moveVector.y) > this.options.distDragBack;
    }
    _getTranslateVal(t) {
      var e = Math.sqrt(Math.pow(this.moveVector.x, 2) + Math.pow(this.moveVector.y, 2)), n = Math.asin(Math.abs(this.moveVector.y) / e) / (Math.PI / 180), s = e + this.options.distDragBack, o = Math.cos(n * (Math.PI / 180)) * s, a = Math.sin(n * (Math.PI / 180)) * s, r = o - Math.abs(this.moveVector.x), c = a - Math.abs(this.moveVector.y);
      if (!t) {
        return {
          x: this.moveVector.x > 0 ? r : r * -1,
          y: this.moveVector.y > 0 ? c : c * -1,
          z: [0, 0]
        };
      } else if (t === "prev") {
        return {
          x: 0,
          y: 0,
          z: [80, 0]
        };
      } else if (t === "next") {
        return {
          x: 0,
          y: 0,
          z: [0, 80]
        };
      }
    }
    _firstItem() {
      return this.items[this.current];
    }
    _secondItem() {
      if (this.itemsCount >= 2) {
        return this.current + 1 < this.itemsCount ? this.items[this.current + 1] : this.items[Math.abs(this.itemsCount - (this.current + 1))];
      }
    }
    _thirdItem() {
      if (this.itemsCount >= 3) {
        return this.current + 2 < this.itemsCount ? this.items[this.current + 2] : this.items[Math.abs(this.itemsCount - (this.current + 2))];
      }
    }
    _lastItem() {
      if (this.itemsCount >= 3) {
        return this._thirdItem();
      } else {
        return this._secondItem();
      }
    }
    goToPrev() {
      this._moveAway("prev");
    }
    goToNext() {
      this._moveAway("next");
    }
    add(t) {
      this.$container.appendChild(t);
      this.items.push(t);
      this.initSetting();
    }
    getSize() {
      return this.itemsCount;
    }
    getCurrent() {
      return this.current;
    }
    getCurrentItem() {
      return this.items[this.current];
    }
    insert(t, e) {
      this.$container.insertBefore(t, this.$container.childNodes[e]);
      this.items.splice(e, 0, t);
      this.initSetting();
    }
    remove(t) {
      if (this.items.length === 0) {
        return;
      }
      if (this.current >= t) {
        this.current--;
      }
      this.$container.removeChild(this.$container.childNodes[t]);
      this.items.splice(t, 1);
      if (this.current >= this.items.length) {
        this.current = 0;
      }
      this.initSetting();
    }
    onResize() {
      this._setContainerHeight();
    }
    destroy() {
      i(window).off("resize.lqdCarouselStack");
      this.$prevBtn.off("click");
      this.$nextBtn.off("click");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("carousel-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidWindowWidth() <= 768)
    return;
  i(".lqd-carousel-stack").filter((l, d) => !d.closest(".lqd-tabs-pane:not(.active)")).liquidCarouselStack();
});
(function(i) {
  const l = "liquidCarouselV3d";
  let d = {
    itemsSelector: ".carousel-item"
  };
  function h(t, e) {
    this.element = t;
    this.options = i.extend({}, d, e);
    this._defaults = d;
    this._name = l;
    this.build();
  }
  h.prototype = {
    build: function() {
      this.init();
    },
    init: function() {
      const t = this;
      const e = i(t.element);
      const n = t.options.itemsSelector;
      t.prepareitems();
      const s = i(n, e).first();
      const o = s.next();
      const a = o.next();
      t.dragY = 0;
      t.startY = 0;
      t.currentY = 0;
      t.setActive(s, e);
      t.initAnim(e, s, a, o);
      t.initDrag();
      t.initClicks();
      e.addClass("carousel-initialized");
      return t;
    },
    prepareitems() {
      const t = this;
      const e = i(t.options.itemsSelector, t.element);
      if (e.length <= 2 && e.length >= 1) {
        const n = e[0];
        for (let s = e.length;s <= 2; s++) {
          i(n).clone(true).appendTo(i(t.element).find(".carousel-items"));
        }
      }
    },
    setActive: function(t, e) {
      const n = i(".is-top", e);
      const s = i(".is-active", e);
      const o = i(".is-bottom", e);
      if (n.length) {
        n.addClass("was-top");
      }
      if (s.length) {
        s.addClass("was-active");
      }
      if (o.length) {
        o.addClass("was-bottom");
      }
      t.addClass("is-active").removeClass("is-top is-bottom").siblings().removeClass("is-active");
      this.setBottom(t);
      this.setTop(t);
    },
    setBottom: function(t) {
      const e = i(this.element);
      const n = this.options.itemsSelector;
      const s = i(n, e).first();
      let o = t.next();
      if (!o.length && t.is(":last-child")) {
        o = s;
      }
      o.addClass("is-bottom").removeClass("is-active is-top was-active").siblings().removeClass("is-bottom");
    },
    setTop: function(t) {
      const e = i(this.element);
      const n = this.options.itemsSelector;
      const s = i(n, e).last();
      let o = t.prev();
      if (!o.length && t.is(":first-child")) {
        o = s;
      }
      o.addClass("is-top").removeClass("is-active is-bottom was-active").siblings().removeClass("is-top");
    },
    initAnim: function(t, e, n, s) {
      this.animInited = false;
      if (!this.animInited) {
        const o = gsap.timeline({
          duration: 0
        });
        o.to(t.get(0).querySelectorAll(".carousel-item:not(.is-active):not(.is-bottom)"), {
          yPercent: -60,
          z: 0,
          scale: 0.9
        }, 0).to(e.get(0), {
          z: 50,
          scale: 1
        }, 0).to(s.get(0), {
          yPercent: 50,
          z: 0,
          scale: 0.9
        }, 0);
        this.animInited = true;
      }
    },
    initClicks() {
      i(this.element).on("click", ".is-top", this.moveItems.bind(this, "prev"));
      i(this.element).on("click", ".is-bottom", this.moveItems.bind(this, "next"));
    },
    initDrag: function() {
      const t = this;
      const e = i(t.element);
      e.on("mousedown", t.pointerStart.bind(t));
      e.on("mousemove", t.pointerMove.bind(t));
      e.on("mouseup", t.pointerEnd.bind(t));
    },
    pointerStart: function(t) {
      const e = this;
      const n = i(e.element);
      e.startY = t.pageY || t.touches[0].pageY;
      e.currentY = e.startY;
      n.addClass("pointer-down");
    },
    pointerMove: function(t) {
      const e = this;
      e.currentY = t.pageY || t.touches[0].pageY;
      e.dragY = parseInt(e.startY - e.currentY, 10);
    },
    pointerEnd: function() {
      const t = i(this.element);
      this.dragY = parseInt(this.startY - this.currentY, 10);
      if (this.dragY >= 20) {
        this.moveItems("next");
      } else if (this.dragY <= -20) {
        this.moveItems("prev");
      }
      t.removeClass("pointer-down");
    },
    moveItems: function(t) {
      if (i(this.element).hasClass("items-moving"))
        return;
      const e = i(this.element);
      const n = i(this.options.itemsSelector);
      const s = i(".is-bottom", e);
      const o = i(".is-top", e);
      const a = gsap.timeline({
        duration: 0.65,
        onUpdate: () => {
          i(n, e).addClass("is-moving");
        },
        onComplete: () => {
          i(n, e).removeClass("is-moving was-top was-active was-bottom");
          i(this.element).removeClass("items-moving");
        }
      });
      if (t == "next")
        this.setActive(s, e);
      else if (t == "prev")
        this.setActive(o, e);
      const r = i(".is-active", e);
      const c = i(".is-bottom", e);
      const p = i(".is-top", e);
      if (t == "next") {
        this.moveNext(a, r, c, p);
      } else if (t == "prev") {
        this.movePrev(a, r, c, p);
      }
    },
    moveNext: function(t, e, n, s) {
      i(this.element).addClass("items-moving");
      t.fromTo(s.get(0), {
        rotateX: -18
      }, {
        yPercent: -60,
        z: 0,
        rotateX: 0,
        scale: 0.9
      }, 0).fromTo(e.get(0), {
        rotateX: -18
      }, {
        yPercent: 0,
        z: 50,
        rotateX: 0,
        scale: 1
      }, 0).fromTo(n.get(0), {
        rotateX: -18
      }, {
        yPercent: 50,
        z: 0,
        rotateX: 0,
        scale: 0.9
      }, 0);
    },
    movePrev: function(t, e, n, s) {
      i(this.element).addClass("items-moving");
      t.fromTo(s.get(0), {
        rotateX: 18
      }, {
        yPercent: -60,
        z: 0,
        rotateX: 0,
        scale: 0.9
      }, 0).fromTo(e.get(0), {
        rotateX: 18
      }, {
        yPercent: 0,
        z: 50,
        rotateX: 0,
        scale: 1
      }, 0).fromTo(n.get(0), {
        rotateX: 18
      }, {
        yPercent: 50,
        z: 0,
        rotateX: 0,
        scale: 0.9
      }, 0);
    }
  };
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".carousel-vertical-3d").liquidCarouselV3d();
});
(function(i) {
  const l = "liquidMegamenu";
  let d = {};

  class h {
    constructor(t, e) {
      this.options = { ...d, ...e };
      this._defaults = d;
      this._name = l;
      this.element = t;
      this.$element = i(this.element);
      this.mobileNavBreakPoint = fastdom.measure(liquidMobileNavBreakpoint)();
      this.tabletBreakpoint = this.mobileNavBreakPoint <= 992 ? 992 : this.mobileNavBreakPoint;
      this.elementBoundingRect = null;
      this.megamenuBoundingRect = null;
      this.megamenuFinalPos = {};
      this.parentOffsetLeft = 0;
      this.breakpoints = {
        [this.mobileNavBreakPoint - 60]: [this.mobileNavBreakPoint + 1, 1 / 0],
        940: [992, this.tabletBreakpoint]
      };
      this.$customMenuParent = this.$element.parent().parent(".lqd-custom-menu");
      this.isInCustomMenu = this.$customMenuParent.length && !this.$element.parent().hasClass("inline-nav");
      this.submenu = this.element.querySelector(".nav-item-children");
      this.megamenuRowsWrap = this.submenu.querySelector(".lqd-megamenu-rows-wrap");
      this.megamenuRows = liquidHeaderIsElementor ? this.megamenuRowsWrap.querySelectorAll(":scope > .elementor > .elementor-section-wrap > .elementor-section, :scope > .elementor > .elementor-section, :scope > .elementor > .e-container, :scope > .elementor > .e-con") : this.megamenuRowsWrap.querySelectorAll(":scope > .megamenu-row, :scope > .vc_row");
      this.isContentStretched = this.megamenuRowsWrap.classList.contains("container-fluid");
      this.isFullwidth = this.element.classList.contains("megamenu-fullwidth");
      this.windowWidth = fastdom.measure(liquidWindowWidth)();
      this.columnsWidth = 0;
      this.defaultSidePadding = liquidHeaderIsElementor ? 0 : 15;
      this.positionApplied = false;
      this.dropdownInfoPromise = new Promise((n) => {
        this.element.addEventListener("megamenu-position-applied", async () => {
          n({
            element: this.element,
            dropdown: this.submenu,
            elementBoundingRect: this.elementBoundingRect,
            megamenuBoundingRect: this.megamenuBoundingRect || await this.getMegamenuBoundingRect(),
            megamenuFinalPos: this.megamenuFinalPos
          });
        });
      });
      this.init();
    }
    init() {
      this.isInCustomMenu && this.$customMenuParent.css("position", "static");
      if (this.isInCustomMenu && !this.$customMenuParent.hasClass("lqd-custom-menu-mobile-collapsible") && this.$element.closest("ul").siblings(".lqd-custom-menu-dropdown-btn").length && !this.$element.closest("ul").hasClass("is-active")) {
        this.$element.closest("ul").on("shown.bs.collapse", this.sizing.bind(this));
      } else {
        this.sizing();
      }
    }
    async sizing() {
      if (this.positionApplied)
        return;
      if (!this.isFullwidth && !liquidHeaderIsElementor) {
        await this.getColumnsWidth();
        await this.setContainerWidth();
      }
      this.elementBoundingRect = await this.getElementBoundingRect();
      this.megamenuBoundingRect = await this.getMegamenuBoundingRect();
      this.positioning();
      this.resizeWindow();
      if (this.isContentStretched) {
        this.element.classList.add("megamenu-content-stretch");
      }
    }
    async getColumnsWidth() {
      if (!this.megamenuRows || liquidHeaderIsElementor) {
        return 0;
      }
      const t = [];
      this.megamenuRows.forEach((n) => {
        const s = n.querySelectorAll(":scope > .megamenu-col, :scope > .ld-container > .ld-row > .wpb_column");
        if (!s || !s.length) {
          return 0;
        }
        const o = new Promise((a) => {
          let r = 0;
          s.forEach((c) => {
            const p = getComputedStyle(c);
            const { paddingLeft: g, paddingRight: u } = p;
            r += c.offsetWidth + (parseInt(g, 10) + parseInt(u, 10));
            if (s.length === 1) {
              c.style.width = "100%";
            }
          });
          a(r);
        });
        t.push(o);
      });
      const e = await Promise.all(t);
      this.columnsWidth = Math.max(...e);
    }
    setContainerWidth() {
      return fastdomPromised.mutate(() => {
        this.megamenuRowsWrap.style.width = `${this.columnsWidth - this.defaultSidePadding * 2}px`;
      });
    }
    getGlobalContainerDimensions() {
      const t = this.windowWidth;
      const e = {
        width: 0,
        offsetLeft: 0
      };
      i.each(this.breakpoints, (n, s) => {
        if (t >= s[0] && t <= s[1]) {
          e.width = parseInt(n, 10);
          e.offsetLeft = (t - n) / 2;
        }
      });
      return e;
    }
    getElementBoundingRect() {
      const t = {
        width: 0,
        height: 0,
        top: 0,
        left: 0
      };
      return new Promise((e) => {
        new IntersectionObserver(([n], s) => {
          const { boundingClientRect: o } = n;
          s.disconnect();
          t.width = o.width;
          t.height = o.height;
          t.top = o.top;
          t.left = o.left;
          e(t);
        }).observe(this.element);
      });
    }
    async getMegamenuBoundingRect() {
      const t = this.$element.find("[data-lqd-flickity]");
      const e = [];
      const n = {
        width: 0,
        height: 0,
        top: 0,
        left: 0
      };
      if (t.length) {
        t.each((s, o) => {
          i(o).liquidCarousel({ forceApply: true });
          const a = i(o).data("plugin_liquidCarousel");
          if (a) {
            e.push(a.carouselInitPromise);
          }
        });
      }
      if (e.length > 0) {
        await Promise.all(e);
      }
      return new Promise((s) => {
        new IntersectionObserver(([o], a) => {
          const { boundingClientRect: r } = o;
          a.disconnect();
          n.width = o.target.offsetWidth;
          n.height = o.target.offsetHeight;
          n.top = r.top;
          n.left = r.left;
          s(n);
        }).observe(this.megamenuRowsWrap);
      });
    }
    async resetPositioning() {
      await new Promise((t) => {
        this.windowWidth = liquidWindowWidth();
        this.columnsWidth = 0;
        this.positionApplied = false;
        this.element.classList.remove("position-applied");
        this.megamenuRowsWrap.style.width = "";
        this.submenu.style.width = "";
        this.submenu.style.left = "";
        this.submenu.style.right = "";
        this.submenu.style.top = "";
        this.submenu.style.marginLeft = "";
        t();
      });
    }
    positioning() {
      const t = window.elementorFrontendConfig && (window.elementorFrontendConfig.responsive?.breakpoints?.mobile_extra?.value || window.elementorFrontendConfig.responsive?.breakpoints?.mobile?.value);
      if (t && liquidWindowWidth() < t) {
        return this.onPositioningDone();
      }
      const e = this.elementBoundingRect.width;
      const n = this.elementBoundingRect.left;
      const s = this.megamenuBoundingRect.width;
      const o = fastdom.measure(this.getGlobalContainerDimensions, this)();
      const a = o.width;
      const r = o.offsetLeft;
      const c = n <= a + r;
      let p = 0;
      let g = 0;
      let u = 0;
      let m = 0;
      fastdomPromised.mutate(() => {
        if (!this.isFullwidth) {
          if (s === a && c) {
            p = r - this.parentOffsetLeft;
            this.submenu.style.left = `${p}px`;
          }
          if (c) {
            p = r + (a / 2 - s / 2) - this.parentOffsetLeft;
            this.submenu.style.left = `${p}px`;
            m = p;
          }
          if (m > n) {
            p = n - this.parentOffsetLeft;
            this.submenu.style.left = `${p}px`;
          }
          if (m + s < n + e) {
            p = n + e - (m + s) + m - this.parentOffsetLeft;
            this.submenu.style.left = `${p}px`;
          }
          this.megamenuFinalPos.left = p;
        }
        if (this.isInCustomMenu) {
          const f = this.elementBoundingRect.top;
          const v = this.elementBoundingRect.height;
          const y = this.megamenuBoundingRect.top;
          const w = this.megamenuBoundingRect.height;
          if (f + v > y + w) {
            u = f - y;
          }
          this.submenu.style.top = `${u}px`;
          this.megamenuFinalPos.top = u;
          if (this.isFullwidth) {
            if (this.megamenuBoundingRect.left + s > this.windowWidth) {
              g = (this.windowWidth - (n + e)) * -1;
              this.submenu.style.width = "auto";
              this.submenu.style.right = `${g}px`;
              this.megamenuFinalPos.right = g;
            }
          }
        }
        this.onPositioningDone();
      });
    }
    onPositioningDone() {
      this.positionApplied = true;
      this.element.classList.add("position-applied");
      this.element.dispatchEvent(new CustomEvent("megamenu-position-applied", { bubbles: false, detail: { element: this.element } }));
    }
    resizeWindow() {
      const t = liquidDebounce(this.onResizeWindow.bind(this), 300);
      i(window).on("resize", t);
      i(document).on("lqd-header-sticky-change", () => {
        if (this.$element.is(":visible") && this.isInCustomMenu && this.$element.closest(".main-header").length || this.$element.closest(".lqd-show-onstuck")) {
          t();
        }
      });
    }
    async onResizeWindow() {
      await this.resetPositioning();
      if (!this.isFullwidth && !liquidHeaderIsElementor) {
        await this.getColumnsWidth();
        await this.setContainerWidth();
      }
      this.elementBoundingRect = await this.getElementBoundingRect();
      this.megamenuBoundingRect = await this.getMegamenuBoundingRect();
      this.positioning();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("megamenu-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".megamenu").filter((l, d) => {
    const h = i(d);
    const t = h.parent().hasClass("lqd-mobile-main-nav");
    if (t) {
      d.classList.add("position-applied");
    }
    return !h.closest(".navbar-fullscreen").length && !(h.closest(".main-header").length && $liquidBody.hasClass("header-style-side")) && !t && !h.parent().hasClass("lqd-menu-items-block");
  }).liquidMegamenu();
});
(function(i) {
  const l = "liquidMegamenuSlide";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.$element = i(t);
      this.DOM.$megamenuItems = this.DOM.$element.find(".megamenu").filter((n, s) => !i(s).parent().hasClass("lqd-mobile-main-nav") && !i(s).parent().hasClass("lqd-menu-items-block"));
      this.DOM.$nonMegamenuItems = this.DOM.$megamenuItems.eq(0).siblings().not(".megamenu");
      this.dropdownsInfo = [];
      this.$megamenusParent = false;
      this.lastActiveIndex = false;
      this.activeIndex = false;
      this.wasRevealed = false;
      this.timeout = false;
      this.getMegamenuDropdowns().then((n) => {
        this.dropdownsInfo = n.filter((s) => s.dropdown != null);
        this.$megamenusParent = i(n[0].element.parentNode);
        this.buildMarkup();
        this.init();
      });
    }
    async getMegamenuDropdowns() {
      const t = [];
      this.DOM.$megamenuItems.each((e, n) => {
        const s = i(n);
        const o = s.data("plugin_liquidMegamenu");
        if (o) {
          t.push(o.dropdownInfoPromise);
        }
      });
      return await Promise.all(t);
    }
    buildMarkup() {
      this.$megamenusParent.append(`
				<div class="lqd-megamenu-slide-stuff pos-abs pos-bl pos-r absolute bottom-0 left-0 right-0 pointer-events-none">
					<div class="lqd-megamenu-slide-stuff-wrap pos-abs pos-l pos-r absolute left-0 right-0">
						<span class="lqd-megamenu-slide-arrow pos-abs pos-tl absolute top-0 left-0"></span>
						<span class="lqd-megamenu-slide-bg pos-abs pos-tl absolute top-0 left-0">
							<span class="lqd-megamenu-slide-bg-inner pos-abs pos-tl absolute top-0 left-0"></span>
						</span>
					</div>
				</div>
			`);
    }
    init() {
      this.eventListeners();
    }
    eventListeners() {
      document.addEventListener("lqd-submenu-change", (t) => {
        const { detail: e } = t;
        const { state: n, $submenu: s } = e;
        const o = s?.closest("li");
        if (o?.length) {
          const a = this.DOM.$megamenuItems.get().indexOf(o[0]);
          if (a >= 0) {
            if (n === "show") {
              this.onMegamenuItemEnter(a);
            } else {
              this.onMegamenuItemLeave(a);
            }
          } else {
            this.resetReveal();
          }
        } else {
          this.resetReveal();
        }
      });
    }
    onMegamenuItemEnter(t) {
      let e;
      let n;
      let s;
      let o;
      e = this.dropdownsInfo[t].elementBoundingRect;
      n = this.dropdownsInfo[t].megamenuBoundingRect;
      s = this.dropdownsInfo[t].megamenuFinalPos;
      o = e.left + e.width / 2;
      if (!this.wasRevealed) {
        this.DOM.element.classList.add("lqd-megamenu-slide-reveal");
        this.DOM.element.style.setProperty("--lqd-megamenu-init-width", n.width);
        this.DOM.element.style.setProperty("--lqd-megamenu-init-height", n.height);
      } else {
        this.DOM.element.classList.remove("lqd-megamenu-slide-reveal");
      }
      this.DOM.$megamenuItems.eq(t).removeClass("lqd-megamenu-item-slide-out");
      this.activeIndex = t;
      this.DOM.element.style.setProperty("--lqd-megamenu-slide-arrow-pos", `${o}px`);
      this.DOM.element.style.setProperty("--lqd-megamenu-current-width", n.width);
      this.DOM.element.style.setProperty("--lqd-megamenu-current-height", n.height);
      this.DOM.element.style.setProperty("--lqd-megamenu-x", `${s.left || 0}px`);
      this.DOM.element.style.setProperty("--lqd-megamenu-y", `${s.top || 0}px`);
      if (this.lastActiveIndex >= 0 && this.activeIndex >= 0) {
        if (this.lastActiveIndex < this.activeIndex) {
          this.DOM.element.classList.add("lqd-megamenu-slide-from-right");
        } else if (this.lastActiveIndex > this.activeIndex) {
          this.DOM.element.classList.remove("lqd-megamenu-slide-from-right");
        }
      }
      this.wasRevealed = true;
      $liquidMainHeader.addClass("lqd-megamenu-slide-active");
    }
    onMegamenuItemLeave(t) {
      this.activeIndex = false;
      this.lastActiveIndex = t;
      this.DOM.element.classList.remove("lqd-megamenu-slide-reveal");
      this.DOM.element.classList.remove("lqd-megamenu-slide-from-right");
      this.DOM.$megamenuItems.eq(t).addClass("lqd-megamenu-item-slide-out");
      $liquidMainHeader.removeClass("lqd-megamenu-slide-active");
      this.timeout = setTimeout(() => {
        if (this.activeIndex === false || this.activeIndex < 0) {
          this.resetReveal();
        }
        clearTimeout(this.timeout);
      }, 180);
    }
    resetReveal() {
      this.activeIndex = false;
      this.wasRevealed = false;
      this.DOM.element.classList.remove("lqd-megamenu-slide-reveal");
      this.DOM.element.classList.remove("lqd-megamenu-slide-from-right");
      $liquidMainHeader.removeClass("lqd-megamenu-slide-active");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("megamenu-slide-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-megamenu-slide]").filter((l, d) => i(d).find(".megamenu").length).liquidMegamenuSlide();
});
(function(i) {
  const l = "liquidCountdown";
  let d = {
    daysLabel: "Days",
    hoursLabel: "Hours",
    minutesLabel: "Minutes",
    secondsLabel: "Seconds",
    timezone: null
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.init();
    }
    init() {
      const { options: t } = this;
      const { until: e, timezone: n } = t;
      this.$element.countdown({
        until: new Date(e.replace(/-/g, "/")),
        padZeroes: true,
        timezone: n,
        layout: '<span class="countdown-row">' + '<span class="countdown-section">' + '<span class="countdown-amount">{dn}</span>' + '<span class="countdown-period">' + t.daysLabel + "</span>" + "</span>" + '<span class="countdown-sep">:</span>' + '<span class="countdown-section">' + '<span class="countdown-amount">{hn}</span>' + '<span class="countdown-period">' + t.hoursLabel + "</span>" + "</span>" + '<span class="countdown-sep">:</span>' + '<span class="countdown-section">' + '<span class="countdown-amount">{mn}</span>' + '<span class="countdown-period">' + t.minutesLabel + "</span>" + "</span>" + '<span class="countdown-sep">:</span>' + '<span class="countdown-section">' + '<span class="countdown-amount">{sn}</span>' + '<span class="countdown-period">' + t.secondsLabel + "</span>" + "</span>" + "</span>"
      });
      return this;
    }
    destroy() {
      this.$element.countdown("destroy");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("countdown-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery, window, document);
jQuery(document).ready(function(i) {
  i("[data-plugin-countdown=true]").liquidCountdown();
});
((i) => {
  const l = "liquidCounter";
  let d = {
    targetNumber: 0,
    startDelay: 0,
    blurEffect: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = i.extend({}, d, e);
      this.element = t;
      this.$element = i(t);
      this.init();
    }
    init() {
      this.createMarkup();
      this.setupIO();
    }
    formatNumberWithCommas(t) {
      return t.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    formatNumberWithSpaces(t) {
      return t.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }
    formatCounterAnimator(t) {
      return t.toString().replace(/(\d)/g, '<span class="lqd-counter-animator d-inline-flex pos-rel inline-flex relative overflow-hidden"><span class="lqd-animator-value d-inline-block inline-block invisible">$1</span></span>');
    }
    createMarkup() {
      const t = i(this.element).children(".lqd-counter-nums-wrap");
      const e = this.options;
      const n = e.targetNumber;
      const s = /,+/.test(n);
      const o = /\s+/.test(n);
      if (s)
        t.html(this.formatCounterAnimator(this.formatNumberWithCommas(n)));
      else if (o)
        t.html(this.formatCounterAnimator(this.formatNumberWithSpaces(n)));
      else
        t.html(this.formatCounterAnimator(n));
      t.find(".lqd-counter-animator").each(function(a, r) {
        const c = i(r);
        const p = c.find(".lqd-animator-value").text();
        c.append(`<div class="lqd-animator-numbers lqd-overlay overflow-hidden" data-value="${p}">
	<ul class="reset-ul w-100 h-100 pos-rel w-full h-full relative">
		<li class="m-0">0</li>
		<li class="m-0">1</li>
		<li class="m-0">2</li>
		<li class="m-0">3</li>
		<li class="m-0">4</li>
		<li class="m-0">5</li>
		<li class="m-0">6</li>
		<li class="m-0">7</li>
		<li class="m-0">8</li>
		<li class="m-0">9</li>
	</ul>
</div>`);
      });
    }
    addBlurEffect(t) {
      if (this.options.blurEffect) {
        const e = i(".lqd-animator-numbers ul", this.element);
        e.each((n, s) => {
          const o = i(s);
          if (o.parent().data("value") != 0) {
            o.css({
              filter: "url('#counter-blur-" + t + "')"
            });
          }
        });
      }
    }
    animateCounter() {
      const t = this.options.startDelay / 1000;
      const e = Math.round(gsap.utils.random(0, 100));
      const n = i(`<svg class="counter-blur-svg" xmlns="http://www.w3.org/2000/svg" version="1.1" width="0" height="0">
	<defs>
		<filter id="counter-blur-${e}">
			<feGaussianBlur id="counter-blur-filter-${e}" in="SourceGraphic" stdDeviation="0,0" />
		</filter>
	</defs>
</svg>`);
      this.addBlurEffect(e);
      this.$element.find(".lqd-animator-numbers").each((s, o) => {
        const a = i(o);
        const r = parseInt(a.data("value"), 10);
        let c = { x: 0, y: 0 };
        let p;
        gsap.to(a.find("ul").get(0), {
          y: r * -100 + "%",
          ease: "power2.out",
          delay: t,
          duration: 1.2,
          onComplete: () => {
            this.$element.addClass("counter-animated");
          }
        });
        if (this.options.blurEffect) {
          if (!i(".counter-blur-svg", this.element).length) {
            n.appendTo(this.element);
          }
          p = n.find(`#counter-blur-filter-${e}`).get(0);
          gsap.to(c, {
            startAt: {
              y: 50 + r * 10
            },
            ease: "power4.out",
            duration: 1.2,
            delay: t,
            y: 0.01,
            onUpdate: () => {
              p.setAttribute("stdDeviation", "0," + c.y);
            },
            onComplete: () => {
              i(".lqd-animator-numbers ul", this.element).css("filter", "");
            }
          });
        }
      });
    }
    setupIO() {
      new A(this.element, this.animateCounter.bind(this), { threshold: 0.8, disconnect: true });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("counter-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-enable-counter]").liquidCounter();
});
(function(i) {
  const l = "liquidDistortedImageGallery";
  let d = {
    imagesContainerSelector: ".lqd-dist-gal-distort",
    menuSelector: ".lqd-dist-gal-menu",
    menuItemSelector: ".lqd-dist-gal-menu-item",
    svgSelector: ".lqd-dist-gal-distort"
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.DOM = {
        imagesContainer: this.element.querySelector(this.options.imagesContainerSelector),
        svg: this.element.querySelector(this.options.svgSelector),
        menu: this.element.querySelector(this.options.menuSelector),
        feDisplacementMapEl: this.element.querySelector("feDisplacementMap"),
        feBlurEl: this.element.querySelector("feGaussianBlur"),
        feTurbulence: this.element.querySelector("feTurbulence")
      };
      this.DOM.imgs = [...this.DOM.imagesContainer.querySelectorAll(".lqd-dist-gal-img")];
      this.DOM.menuLinks = [...this.DOM.menu.querySelectorAll(this.options.menuItemSelector)];
      this.mousePos = { x: liquidWindowWidth() / 2, y: liquidWindowHeight() / 2 };
      this.lastMousePos = {
        translation: { x: liquidWindowWidth() / 2, y: liquidWindowHeight() / 2 },
        displacement: { x: 0, y: 0 }
      };
      this.dmScale = 0;
      this.brightness = 0;
      this.rotate = 0;
      this.current = -1;
      this.rAFId = null;
      this.elOffsetTop = this.$element.offset().top;
      this.initEvents();
    }
    lerp(t, e, n) {
      return (1 - n) * t + n * e;
    }
    distance(t, e, n, s) {
      var o = t - e;
      var a = n - s;
      return Math.hypot(o, a);
    }
    lineEq(t, e, n, s, o) {
      var a = (t - e) / (n - s), r = e - a * s;
      return a * o + r;
    }
    initEvents() {
      const t = liquidIsMobile();
      if (!t) {
        window.addEventListener("mousemove", (e) => this.mousePos = liquidGetMousePos(e));
      }
      this.DOM.menuLinks.forEach((e, n) => {
        if (!t) {
          const s = () => {
            if (this.current !== -1) {
              gsap.to(this.DOM.imgs[this.current], {
                duration: 0.2,
                opacity: 0,
                ease: "power2.out"
              });
            }
            this.current = n;
            if (this.fade) {
              gsap.to(this.DOM.imgs[this.current], {
                duration: 0.2,
                opacity: 1,
                ease: "power2.out"
              });
              this.fade = false;
            } else {
              gsap.to(this.DOM.imgs[this.current], {
                duration: 0.2,
                opacity: 1,
                ease: "power2.out"
              });
            }
          };
          const o = () => {
            gsap.to(this.DOM.imgs[this.current], {
              duration: 0.2,
              opacity: 0,
              ease: "power2.out"
            });
          };
          e.addEventListener("mouseenter", s);
          e.addEventListener("mouseleave", o);
        } else {
          e.addEventListener("click", (s) => {
            if (e.href !== "" || e.href !== "#")
              return;
            s.preventDefault();
            const o = this.DOM.imgs[n];
            const a = [...o.querySelectorAll("a.fresco")];
            if (a.length > 0) {
              a[0].click();
            }
          });
        }
      });
      if (!t) {
        this.DOM.menu.addEventListener("mouseenter", () => {
          this.rAFId = requestAnimationFrame(this.render.bind(this));
          this.fade = true;
        });
        this.DOM.menu.addEventListener("mouseleave", () => {
          this.rAFId && cancelAnimationFrame(this.rAFId);
        });
      }
    }
    render() {
      const t = this.distance(this.lastMousePos.displacement.x, this.mousePos.x, this.lastMousePos.displacement.y, this.mousePos.y);
      this.lastMousePos.translation.x = this.lerp(this.lastMousePos.translation.x, this.mousePos.x, 0.2);
      this.lastMousePos.translation.y = this.lerp(this.lastMousePos.translation.y, this.mousePos.y, 0.2);
      this.lastMousePos.displacement.x = this.lerp(this.lastMousePos.displacement.x, this.mousePos.x, 0.1);
      this.lastMousePos.displacement.y = this.lerp(this.lastMousePos.displacement.y, this.mousePos.y, 0.1);
      this.brightness = Math.min(this.lineEq(1.75, 1, 140, 1, t), 1.5);
      this.contrast = Math.min(this.lineEq(1.1, 1, 140, 1, t), 1.5);
      if (this.lastMousePos.translation.x > this.mousePos.x) {
        this.rotate = Math.min(this.lineEq(-7, 0, 130, 0, t), 50);
      } else {
        this.rotate = Math.min(this.lineEq(7, 0, 130, 0, t), 50);
      }
      gsap.to(this.DOM.imagesContainer, {
        x: this.lastMousePos.translation.x - liquidWindowWidth() / 2,
        y: this.lastMousePos.translation.y - this.elOffsetTop,
        rotation: this.rotate
      });
      this.rAFId = requestAnimationFrame(this.render.bind(this));
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("dist-gal-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-dist-gal]").liquidDistortedImageGallery();
});
(function(i) {
  const l = "liquidDynamicShape";
  let d = {};

  class h {
    constructor(t, e) {
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.svgEl = this.DOM.element.querySelector("svg");
      this.DOM.pathEl = this.DOM.element.querySelector("path");
      this.paths = this.DOM.pathEl.getAttribute("pathdata:id").trim().split(";");
      this.paths.splice(this.paths.length, 0, this.DOM.pathEl.getAttribute("d"));
      this.timeline = null;
      this.createTimeline();
      this.init();
    }
    createTimeline() {
      const t = this.paths.map((e) => {
        return { d: e.trim() };
      });
      this.timeline = gsap.timeline({
        repeat: -1,
        yoyo: true
      }).pause();
      for (let e = 0;e < t.length; e++) {
        this.timeline.to(this.DOM.pathEl, {
          attr: {
            d: t[e].d
          },
          duration: 2
        });
      }
    }
    init() {
      this.animate();
    }
    animate() {
      this.timeline.play();
    }
    pause() {
      this.timeline.pause();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("mi-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidIsMobile())
    return;
  i("[data-dynamic-shape]").filter((l, d) => {
    const h = i(d);
    const t = h.closest(".vc_row.pp-section");
    return !t.length;
  }).liquidDynamicShape();
});
(function(i) {
  const l = "liquidFormInputs";
  let d = {
    dropdownAppendTo: null
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = { ...d, ...e };
      this._defaults = d;
      this._name = l;
      this.init();
    }
    init() {
      this.icons();
      this.initDatePicker();
      this.initSelect();
      this.initSpinner();
      this.inputsEventHandlers();
    }
    icons() {
      if (this.$element.hasClass("wpcf7-form")) {
        this.$element.find(".wpcf7-form-control-wrap").siblings("i").each((t, e) => {
          i(e).appendTo(i(e).prev(".wpcf7-form-control-wrap"));
        });
      }
    }
    initDatePicker() {
      const t = this.$element.find(".date-picker, input.wpcf7-form-control[type=date]");
      t.each((e, n) => {
        const s = i(n);
        if (s.attr("type") !== "date") {
          i(n).datepicker();
        }
      });
    }
    initSelect() {
      const t = this.$element.find("select").not(".select2-hidden-accessible, .select, .woo-rating, #bbp_stick_topic_select, #bbp_topic_status_select, #bbp_forum_id, .woocommerce-widget-layered-nav-dropdown, .elementor-field-textual, .wc-pao-addon-select, .quform-field, .choicesjs-select, .choices__input");
      if (t.closest(".checkout, table.cart, .elementor-widget-woocommerce-product-add-to-cart, .hub-booking-form").length)
        return;
      let { dropdownAppendTo: e } = this.options;
      if (t.hasClass("orderby") || t.hasClass("search-field") || t.hasClass("wpcf7-form-control") || t.hasClass("liquid-schedule-filter")) {
        e = "self";
      }
      if (!t.closest(".variations").length) {
        t.each((n, s) => {
          const o = i(s);
          const a = o.selectmenu({
            change: () => {
              o.trigger("change");
            }
          });
          if (e) {
            let r;
            if (e === "self") {
              r = i('<div class="lqd-select-dropdown" />').insertAfter(o);
            } else {
              r = i(e, this.element).length ? i(e, this.element) : i(e);
            }
            a.selectmenu("option", "appendTo", r);
          }
          o.on("change", () => {
            o.selectmenu("refresh");
          });
        });
      } else {
        const n = i('<span class="lqd-select-ext" />');
        t.wrap('<span class="lqd-select-wrap pos-rel relative" />');
        n.insertAfter(t);
      }
    }
    initSpinner() {
      const t = this.$element.find("input.spinner, input[type=number].qty");
      t.each((e, n) => {
        const s = i(n);
        s.spinner({
          spin: (o, a) => {
            s.val(a.value);
            s.trigger("change");
          }
        });
      });
    }
    getInputParent(t) {
      if (t.closest("p").length) {
        return t.closest("p");
      } else {
        return t.closest("div");
      }
    }
    inputsEventHandlers() {
      i("input, textarea", this.$element).on("focus", this.inputOnFocus.bind(this));
      i("input, textarea", this.$element).on("blur", this.inputOnBlur.bind(this));
    }
    inputOnFocus(t) {
      const e = this.getInputParent(i(t.target));
      e.addClass("input-focused");
    }
    inputOnBlur(t) {
      const e = i(t.target);
      const n = this.getInputParent(e);
      if (e.val() !== "") {
        n.addClass("input-filled");
      } else {
        n.removeClass("input-filled");
      }
      n.removeClass("input-focused");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("form-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("form, .lqd-filter-dropdown, .widget").not('[name="chbs-form"], .frm-fluent-form, .hub-booking-form, .geodir-listing-search, .wpforms-form, .wc-block-checkout__form, .woocommerce-shipping-calculator').liquidFormInputs();
});
(function(i) {
  const l = "liquidHover3d";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.hoverable = this.element.querySelector("[data-stacking-factor]");
      this.stackingFactor = this.hoverable.getAttribute("data-stacking-factor");
      this.rect = {};
      this.maxRotation = 8;
      this.maxTranslation = 4;
      this.build();
    }
    build() {
      fastdom.measure(() => {
        new IntersectionObserver(([t], e) => {
          e.disconnect();
          const { boundingClientRect: n } = t;
          this.rect = {
            width: n.width,
            height: n.height
          };
          this.init();
          this.$element.addClass("hover-3d-applied");
        }).observe(this.element);
      });
    }
    measure() {
      return fastdomPromised.measure(() => {
        return new Promise((t) => {
          new IntersectionObserver(([e], n) => {
            n.disconnect();
            t(e.boundingClientRect);
          }).observe(this.element);
        });
      }).then((t) => {
        this.rect = {
          width: t.width,
          height: t.height
        };
      });
    }
    init() {
      this.eventHandlers();
    }
    eventHandlers() {
      this.$element.on("mousemove.lqdHover3d", this.hover.bind(this));
      this.$element.on("mouseleave.lqdHover3d", this.leave.bind(this));
      $liquidWindow.on("resize.lqdHover3d", this.onWindowLoad.bind(this));
    }
    async onWindowLoad() {
      await this.measure();
    }
    animate(t, e) {
      fastdom.mutate(() => {
        if (e) {
          this.element.classList.add("mouse-in");
        } else {
          this.element.classList.remove("mouse-in");
        }
        this.hoverable.style.transition = "none";
        gsap.to(this.hoverable, {
          x: t.xTranslationPercentage * -1 * t.maxTranslationX,
          y: t.yTranslationPercentage * -1 * t.maxTranslationY,
          rotateX: t.xRotationPercentage * -1 * t.maxRotationX,
          rotateY: t.yRotationPercentage * -1 * t.maxRotationY,
          duration: 0.8,
          ease: "power2.out"
        });
      });
    }
    calculateRotationPercentage(t, e) {
      return -2 / e * t + 1;
    }
    calculateTranslationPercentage(t, e) {
      return -2 / e * t + 1;
    }
    hover(t) {
      const e = {
        x: t.pageX - this.$element.offset().left,
        y: t.pageY - this.$element.offset().top
      };
      const n = {
        xRotationPercentage: this.calculateRotationPercentage(e.y, this.rect.height),
        yRotationPercentage: this.calculateRotationPercentage(e.x, this.rect.width) * -1,
        xTranslationPercentage: this.calculateTranslationPercentage(e.x, this.rect.width),
        yTranslationPercentage: this.calculateTranslationPercentage(e.y, this.rect.height)
      };
      this.animate({
        maxTranslationX: this.maxTranslation * this.stackingFactor,
        maxTranslationY: this.maxTranslation * this.stackingFactor,
        maxRotationX: this.maxRotation * this.stackingFactor,
        maxRotationY: this.maxRotation * this.stackingFactor,
        xRotationPercentage: n.xRotationPercentage,
        yRotationPercentage: n.yRotationPercentage,
        xTranslationPercentage: n.xTranslationPercentage,
        yTranslationPercentage: n.yTranslationPercentage
      }, true);
    }
    leave() {
      this.animate({
        maxTranslationX: 0,
        maxTranslationY: 0,
        maxRotationX: 0,
        maxRotationY: 0,
        xRotationPercentage: 0,
        yRotationPercentage: 0,
        xTranslationPercentage: 0,
        yTranslationPercentage: 0
      }, false);
    }
    destroy() {
      this.$element.off("mousemove.lqdHover3d mouseleave.lqdHover3d");
      $liquidWindow.off("resize.lqdHover3d");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("hover3d-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidIsMobile())
    return false;
  i("[data-hover3d]").filter((l, d) => !i(d).closest(".lqd-tabs-pane").not(".active").length).liquidHover3d();
});
(function(i) {
  const l = "liquidIconboxCircle";
  let d = {
    startAngle: 45,
    itemSelector: ".lqd-ib-circ-icn, .one-ib-circ-icn",
    contentsContainer: ".lqd-ib-circ-inner, .one-ib-circ-inner"
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.$parent = i(this.options.contentsContainer);
      this.$items = i(this.options.itemSelector, this.element);
      this.anglesArray = [this.options.startAngle];
      this.init();
    }
    init() {
      this.addAngles(this.$items);
      this.setTransformOrigin();
      this.setIntersectionObserver();
      this.windowResize();
    }
    getItemsArray() {
      const t = this.$items;
      const e = t.length;
      const n = [];
      for (let s = 0;s < e; s++) {
        n.push(t[s]);
      }
      return n;
    }
    getElementDimension(t) {
      return {
        width: t.width(),
        height: t.height()
      };
    }
    addAngles(t) {
      const e = t.length;
      t.each((n) => {
        this.anglesArray.push(360 / e + this.anglesArray[n]);
      });
    }
    setTransformOrigin() {
      const t = this.getElementDimension(this.$parent);
      this.$items.each((e, n) => {
        const s = i(n);
        s.css({
          transformOrigin: ""
        });
        s.css({
          transformOrigin: `${t.width / 2}px ${t.height / 2}px`
        });
        s.children().css({
          transform: `rotate(${this.anglesArray[e] * -1}deg)`
        });
      });
    }
    setIntersectionObserver() {
      new IntersectionObserver((t, e) => {
        t.forEach((n) => {
          if (n.isIntersecting) {
            this.animateIcons();
            e.unobserve(n.target);
          }
        });
      }, { threshold: 0.25 }).observe(this.element);
    }
    animateIcons() {
      const t = this.getItemsArray();
      const e = gsap.timeline();
      e.to(t, {
        opacity: 1,
        duration: 0.2,
        ease: "linear",
        stagger: (n) => n * 0.2
      }, 0.45).to(t, {
        rotation: (n) => this.anglesArray[n],
        duration: 1,
        ease: "power4.inOut",
        stagger: (n) => n * 0.15
      }, 0);
    }
    windowResize() {
      i(window).on("resize.lqdIconboxCircle", this.setTransformOrigin.bind(this));
    }
    destroy() {
      i(window).off("resize.lqdIconboxCircle");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-spread-incircle]").liquidIconboxCircle();
});
(function(i) {
  const l = "liquidImageComparison";
  let d = {};

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.dragging = false;
      this.resizing = false;
      this.$dragElement = this.$element.find(".cd-handle");
      this.$resizeElement = this.$element.find(".cd-resize-img");
      this.dragWidth = this.$dragElement.outerWidth();
      this.containerWidth = this.$element.outerWidth();
      this.init();
    }
    init() {
      this._onResize = liquidThrottle(this._onResize.bind(this), 250);
      this.element.style.setProperty("--container-width", `${this.containerWidth}px`);
      this.initIO();
      this._drags();
      this._handleResize();
    }
    initIO() {
      new IntersectionObserver(([t], e) => {
        if (t.isIntersecting) {
          e.unobserve(t.target);
          this.$element.addClass("is-visible");
        }
      }).observe(this.element, { threshold: 0.35 });
    }
    _drags() {
      this.$dragElement.on("pointerdown.lqdImageComparison", (t) => {
        t.preventDefault();
        this.$dragElement.addClass("draggable");
        this.$resizeElement.addClass("resizable");
        const e = t.originalEvent.pageX;
        const n = this.$dragElement.offset().left + this.dragWidth - e;
        i(document).on("pointermove.lqdImageComparison", (s) => {
          if (!this.dragging) {
            this.dragging = true;
            requestAnimationFrame(() => {
              this._animateDraggedHandle(s, n);
            });
          }
        });
      });
      i(document).on("pointerup.lqdImageComparison", () => {
        this.$dragElement.removeClass("draggable");
        this.$resizeElement.removeClass("resizable");
        i(document).off("pointermove.lqdImageComparison");
        this.dragging = false;
      });
    }
    _animateDraggedHandle(t, e) {
      var n = this.$element.offset().left;
      var s = n;
      var o = n + this.containerWidth;
      var a = t.originalEvent.pageX;
      var r = a + e - this.dragWidth / 2;
      if (r < s) {
        r = s;
      } else if (r > o) {
        r = o;
      }
      var c = (r - n) * 100 / this.containerWidth + "%";
      this.$dragElement.css("left", c).on("pointerup.lqdImageComparison", () => {
        this.$dragElement.removeClass("draggable");
        this.$resizeElement.removeClass("resizable");
      });
      this.$resizeElement.css("width", c);
      this.dragging = false;
    }
    _handleResize() {
      window.addEventListener("resize", this._onResize);
    }
    _onResize() {
      this.dragWidth = this.$dragElement.outerWidth();
      this.containerWidth = this.$element.outerWidth();
      this.element.style.setProperty("--container-width", `${this.containerWidth}px`);
    }
    destroy() {
      window.removeEventListener("resize", this._onResize);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("plugin-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  const l = i(".cd-image-container");
  const d = l.filter((e, n) => !i(n).closest(".lqd-tabs-pane, .e-n-tabs-content > .e-con").not(".active, .e-active").length);
  const h = l.closest(".e-n-tabs-content > .e-con");
  d.liquidImageComparison();
  if (!h.length)
    return;
  const t = new MutationObserver(([e], n) => {
    const { target: s } = e;
    if (e.target.classList.contains("e-active")) {
      i(s).find(".cd-image-container").liquidImageComparison();
    }
  });
  h.each((e, n) => {
    t.observe(n, { attributes: true, attributeFilter: ["class"] });
  });
});
(function(i) {
  const l = "liquidImageTrail";
  let d = {
    trigger: "body",
    triggerRelation: null,
    imgArrayContainer: ".lqd-img-trail-array",
    threshold: 85,
    keepLastItemVisible: false,
    respectDirection: false
  };

  class h {
    constructor(e) {
      this.DOM = { el: e };
      this.defaultStyle = {
        scale: 1,
        x: 0,
        y: 0,
        opacity: 0
      };
      this.getRect();
      this.initEvents();
    }
    initEvents() {
      window.addEventListener("resize", () => this.resize());
    }
    resize() {
      gsap.set(this.DOM.el, {
        ...this.defaultStyle
      });
      this.getRect();
    }
    getRect() {
      this.rect = this.DOM.el.getBoundingClientRect();
    }
    isActive() {
      return this.DOM.el.style.opacity != 0;
    }
  }

  class t {
    constructor(e, n) {
      this.element = e;
      this.$element = i(e);
      this.options = i.extend({}, d, n);
      this._defaults = d;
      this._name = l;
      this.DOM = {
        imgArrayContainer: this.element.querySelector(this.options.imgArrayContainer)
      };
      this.images = [];
      [...this.DOM.imgArrayContainer.querySelectorAll(".lqd-img-trail-img")].forEach((o) => this.images.push(new h(o)));
      this.imagesTotal = this.images.length;
      this.imgPosition = 0;
      this.zIndexVal = 1;
      this.lastMousePos;
      this.cacheMousePos;
      this.mousePos = this.lastMousePos = this.cacheMousePos = { x: 0, y: 0 };
      this.rAFId = null;
      const s = () => {
        return new Promise((o) => {
          imagesLoaded(this.DOM.imgArrayContainer.querySelector(".lqd-img-trail-img"), o);
        });
      };
      s().then(() => {
        this.initEvents();
        this.element.classList.add("img-trail-initiated");
      });
    }
    lerp(e, n, s) {
      return (1 - s) * e + s * n;
    }
    distance(e, n, s, o) {
      return Math.hypot(s - e, o - n);
    }
    getMouseDistance() {
      return this.distance(this.mousePos.x, this.mousePos.y, this.lastMousePos.x, this.lastMousePos.y);
    }
    setThreshold(e) {
      if (this.options.threshold === "auto") {
        this.options.threshold = (i(e).outerWidth() / this.images.length - 1) / 1.75;
      }
    }
    initEvents() {
      let { trigger: e, triggerRelation: n } = this.options;
      if (n) {
        e = this.$element[n](e);
      }
      if (e === "this") {
        e = this.element;
      }
      this.setThreshold(e);
      i(e).on("mouseenter", () => this.rAFId = requestAnimationFrame(this.render.bind(this)));
      i(e).on("mousemove", (s) => {
        this.mousePos = liquidGetMousePos(s, true);
      });
      i(e).on("mouseleave", () => cancelAnimationFrame(this.rAFId));
    }
    render() {
      let e = this.getMouseDistance();
      this.cacheMousePos.x = this.lerp(this.cacheMousePos.x || this.mousePos.x, this.mousePos.x, 0.1);
      this.cacheMousePos.y = this.lerp(this.cacheMousePos.y || this.mousePos.y, this.mousePos.y, 0.1);
      if (e > this.options.threshold) {
        ++this.zIndexVal;
        if (!this.options.respectDirection || this.options.respectDirection && this.cacheMousePos.x < this.mousePos.x) {
          this.imgPosition = this.imgPosition < this.imagesTotal - 1 ? this.imgPosition + 1 : 0;
        } else {
          this.imgPosition = this.imgPosition === 0 ? this.imagesTotal - 1 : this.imgPosition - 1;
        }
        this.showNextImage();
        this.lastMousePos = this.mousePos;
      }
      let n = true;
      for (let s of this.images) {
        if (s.isActive()) {
          n = false;
          break;
        }
      }
      if (n && this.zIndexVal !== 1) {
        this.zIndexVal = 1;
      }
      this.rAFId = requestAnimationFrame(this.render.bind(this));
    }
    showNextImage() {
      const e = this.images[this.imgPosition];
      gsap.killTweensOf(e.DOM.el);
      e.DOM.el.style.zIndex = this.zIndexVal;
      gsap.timeline({
        onStart: () => {
          if (this.options.keepLastItemVisible) {
            this.images.forEach((n, s) => {
              if (s !== this.images.indexOf(e)) {
                gsap.killTweensOf(n.DOM.el);
                gsap.to(n.DOM.el, {
                  opacity: 0,
                  duration: 0.25,
                  ease: "expo.out"
                });
              }
            });
          }
        }
      }).set(e.DOM.el, {
        opacity: 1,
        scale: 1,
        zIndex: this.zIndexVal,
        x: this.cacheMousePos.x - e.rect.width / 2,
        y: this.cacheMousePos.y - e.rect.height / 2
      }, 0).to(e.DOM.el, {
        ease: "expo.out",
        duration: 0.9,
        x: this.mousePos.x - e.rect.width / 2,
        y: this.mousePos.y - e.rect.height / 2
      }, 0).to(e.DOM.el, {
        ease: "power1.out",
        duration: 1,
        opacity: this.options.keepLastItemVisible ? 1 : 0
      }, 0.4);
    }
  }
  i.fn[l] = function(e) {
    return this.each(function() {
      const n = { ...i(this).data("img-trl-options"), ...e };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new t(this, n));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-img-trail]").liquidImageTrail();
});
let P = 0;
(function(i) {
  const l = "liquidLightBox";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = i.extend({}, d, e);
      this.isOpen = false;
      this.$backdrop = false;
      this.lityInstance = false;
      this.element = t;
      this.$element = i(t);
      this.modalTemplate = document.querySelector("body > #lqd-temp-modal-box");
      this.scrollSmoothEnabled = "liquidLenis" in window;
      let n = this.element.getAttribute("href");
      this.isVideoUrl = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|vimeo\.com\/)/i.test(n);
      if (this.isVideoUrl) {
        this.createVideoTarget();
        n = this.element.getAttribute("href");
      }
      const s = !n || n === "" || n.length < 1 || n.replace("#", "").length < 1;
      if (s)
        return;
      try {
        document.querySelector(n);
      } catch (o) {
        console.warn(`Invalid selector: ${n}`, this.element);
        return;
      }
      this.targets = document.querySelectorAll(n);
      this.target = false;
      this.$target = false;
      if (this.targets.length < 1)
        return;
      this.onWindowResize = liquidDebounce(this.onWindowResize.bind(this), 100);
      this.init();
    }
    async init() {
      await this.buildMarkup();
      this.events();
    }
    createVideoTarget() {
      const t = `lqd-lightbox-wrap-${P}`;
      const e = document.createElement("div");
      e.className = "lqd-lity-iframe-container";
      e.id = t;
      e.style = "display: none;";
      e.innerHTML = '<iframe frameborder="0" allowfullscreen allow="autoplay; fullscreen" src="' + this.element.getAttribute("href") + '"/>';
      document.body.appendChild(e);
      this.element.setAttribute("href", `#${t}`);
      P++;
    }
    buildMarkup() {
      if (!this.modalTemplate)
        return;
      return fastdomPromised.mutate(() => {
        if (this.targets.length > 1) {
          this.targets.forEach((s, o) => o !== 0 && s.remove());
        }
        const t = this.targets[0];
        const e = this.element.getAttribute("href").replace("#", "");
        const n = t.getAttribute("data-modal-type") || "default";
        if (window.liquidAvailableLightboxes.find(({ id: s }) => s === e)) {
          this.target = t;
        } else {
          if (n !== "in-container") {
            const s = this.modalTemplate.content.firstElementChild.cloneNode(true);
            const o = s.children[0];
            const a = t.closest(".elementor-element");
            const r = t.closest(".elementor");
            if (r) {
              s.classList.add(...r.getAttribute("class").split(" ").filter((c) => c.indexOf("elementor-") !== -1));
            }
            o.setAttribute("data-modal-type", n);
            o.setAttribute("id", e);
            t.removeAttribute("id");
            t.removeAttribute("data-modal-type");
            if (a) {
              o.classList.add(...a.getAttribute("class").split(" "));
            }
            o.querySelector(".lqd-lity-content").appendChild(t);
            document.body.appendChild(s);
            window.liquidAvailableLightboxes.push({ id: e, type: n });
            this.target = o;
          } else {
            this.target = t.closest(".elementor-element");
          }
        }
        this.$target = i(this.target);
        this.$backdrop = this.$target.children(".lqd-lity-backdrop");
        if (this.target.querySelector(".lqd-lity-iframe-container")) {
          this.target.querySelector(".lqd-lity-iframe-container").style.display = "block";
          this.target.querySelector(".lqd-lity-content").style.maxHeight = "none";
        }
        this.modalType = n;
      });
    }
    events() {
      i(document).on("lqd-lity:open", this.onOpen.bind(this));
      i(document).on("lqd-lity:close", this.onClose.bind(this));
      i("[data-lqd-lity-close]", this.target).off("click");
      i("[data-lqd-lity-close]", this.target).on("click", this.onCloseBtnClick.bind(this));
      this.$backdrop.on("click", this.onBackdropClick.bind(this));
      i(window).on("resize", this.onWindowResize);
    }
    onCloseBtnClick() {
      if (!this.isOpen) {
        return this.$element.trigger("click");
      }
      if (this.isOpen && this.lityInstance) {
        return this.lityInstance.close();
      }
    }
    onOpen(t, e) {
      if (t.target !== this.target && !this.target.contains(t.target))
        return;
      const n = this.target.querySelector("video");
      const s = this.target.querySelector("audio");
      if (this.modalType === "box") {
        this.boxModalPosition();
      }
      this.lityInstance = e;
      this.modalType !== "in-container" && this.$element.addClass("pointer-events-none");
      this.target.classList.add("lqd-is-active");
      this.element.classList.add("lqd-is-active");
      this.isOpen = true;
      if (n) {
        n.oncanplay = n.play();
      }
      if (s) {
        s.oncanplay = s.play();
      }
      if (this.scrollSmoothEnabled) {
        liquidLenis.stop();
      }
      i(document).on("click.lityClose", "[data-lqd-lity]", () => {
        if (this.isOpen && this.lityInstance) {
          this.lityInstance.close();
        }
      });
    }
    onClose(t) {
      if (t.target !== this.target && !this.target.contains(t.target))
        return;
      const e = this.target.querySelector("video");
      const n = this.target.querySelector("audio");
      if (e) {
        e.oncanplay = e.pause();
      }
      if (n) {
        n.oncanplay = n.pause();
      }
      this.$element.blur();
      this.modalType !== "in-container" && this.$element.removeClass("pointer-events-none");
      this.target.classList.remove("lqd-is-active");
      this.element.classList.remove("lqd-is-active");
      if ("Vimeo" in window) {
        const s = this.$target.find('iframe[src*="vimeo.com"]');
        if (s.length) {
          const o = new Vimeo.Player(s);
          o?.pause();
        }
      }
      this.isOpen = false;
      if (this.scrollSmoothEnabled) {
        liquidLenis.start();
      }
      i(document).off("click.lityClose");
    }
    boxModalPosition() {
      if (!this.$target.length)
        return;
      const t = this.$element.offset();
      const e = this.$target.outerWidth();
      const n = this.$target.outerHeight();
      const s = t.left + this.$element.outerWidth() - 60;
      const o = window.innerWidth;
      this.$target.css({
        position: "",
        top: "",
        left: "",
        right: ""
      });
      if (this.$element.closest(".lqd-stickybar-wrap").length) {
        this.$target.css({
          position: "fixed",
          top: t.top - window.scrollY - n - 25
        });
      } else {
        this.$target.css({
          top: t.top - n - 25
        });
      }
      this.$target.css({
        left: s,
        right: s + e >= o ? o - s : "auto"
      });
      if (s + e >= o) {
        this.$target.addClass("to-left");
        this.$target.css({
          left: "auto",
          right: o - s - 60
        });
      }
    }
    onBackdropClick() {
      if (!this.isOpen || !this.lityInstance)
        return;
      this.lityInstance.close();
    }
    onWindowResize() {
      if (this.modalType === "box") {
        this.boxModalPosition();
      }
    }
    destroy() {
      if (!this.$target || !this.$target.length)
        return;
      i("[data-lqd-lity-close]", this.target).off("click");
      i(document).off("click.lityClose");
      this.$backdrop.off("click");
      i(window).off("resize", this.onWindowResize);
      i.data(this.element, "plugin_" + l, null);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("lightbox-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery, window, document);
jQuery(document).ready(function(i) {
  i("[data-lqd-lity]").liquidLightBox();
});
(function(i) {
  const l = "liquidMap";
  let d = {
    address: "7420 Shore Rd, Brooklyn, NY 11209, USA",
    marker: null,
    style: "wy",
    marker_style: "default",
    className: "lqd-custom-map-marker pos-abs absolute border-radius-circle",
    markers: null,
    map: {
      zoom: 16,
      mapTypeId: "roadmap",
      disableDefaultUI: true,
      panControl: false,
      zoomControl: true,
      mapTypeControl: false,
      streetViewControl: false,
      scrollwheel: false
    }
  };
  const h = {
    wy: [
      { featureType: "all", elementType: "geometry.fill", stylers: [{ weight: "2.00" }] },
      { featureType: "all", elementType: "geometry.stroke", stylers: [{ color: "#9c9c9c" }] },
      { featureType: "all", elementType: "labels.text", stylers: [{ visibility: "on" }] },
      { featureType: "landscape", elementType: "all", stylers: [{ color: "#f2f2f2" }] },
      { featureType: "landscape", elementType: "geometry.fill", stylers: [{ color: "#ffffff" }] },
      { featureType: "landscape.man_made", elementType: "geometry.fill", stylers: [{ color: "#ffffff" }] },
      { featureType: "poi", elementType: "all", stylers: [{ visibility: "off" }] },
      { featureType: "road", elementType: "all", stylers: [{ saturation: -100 }, { lightness: 45 }] },
      { featureType: "road", elementType: "geometry.fill", stylers: [{ color: "#eeeeee" }] },
      { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#7b7b7b" }] },
      { featureType: "road", elementType: "labels.text.stroke", stylers: [{ color: "#ffffff" }] },
      { featureType: "road.highway", elementType: "all", stylers: [{ visibility: "simplified" }] },
      { featureType: "road.arterial", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
      { featureType: "transit", elementType: "all", stylers: [{ visibility: "off" }] },
      { featureType: "water", elementType: "all", stylers: [{ color: "#46bcec" }, { visibility: "on" }] },
      { featureType: "water", elementType: "geometry.fill", stylers: [{ color: "#c8d7d4" }] },
      { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#070707" }] },
      { featureType: "water", elementType: "labels.text.stroke", stylers: [{ color: "#ffffff" }] }
    ],
    blueEssence: [
      {
        featureType: "landscape.natural",
        elementType: "geometry.fill",
        stylers: [{ visibility: "on" }, { color: "#e0efef" }]
      },
      {
        featureType: "poi",
        elementType: "geometry.fill",
        stylers: [{ visibility: "on" }, { hue: "#1900ff" }, { color: "#c0e8e8" }]
      },
      { featureType: "road", elementType: "geometry", stylers: [{ lightness: 100 }, { visibility: "simplified" }] },
      { featureType: "road", elementType: "labels", stylers: [{ visibility: "off" }] },
      { featureType: "transit.line", elementType: "geometry", stylers: [{ visibility: "on" }, { lightness: 700 }] },
      { featureType: "water", elementType: "all", stylers: [{ color: "#7dcdcd" }] }
    ],
    lightMonochrome: [
      {
        featureType: "administrative.locality",
        elementType: "all",
        stylers: [{ hue: "#2c2e33" }, { saturation: 7 }, { lightness: 19 }, { visibility: "on" }]
      },
      {
        featureType: "landscape",
        elementType: "all",
        stylers: [{ hue: "#ffffff" }, { saturation: -100 }, { lightness: 100 }, { visibility: "simplified" }]
      },
      {
        featureType: "poi",
        elementType: "all",
        stylers: [{ hue: "#ffffff" }, { saturation: -100 }, { lightness: 100 }, { visibility: "off" }]
      },
      {
        featureType: "road",
        elementType: "geometry",
        stylers: [{ hue: "#bbc0c4" }, { saturation: -93 }, { lightness: 31 }, { visibility: "simplified" }]
      },
      {
        featureType: "road",
        elementType: "labels",
        stylers: [{ hue: "#bbc0c4" }, { saturation: -93 }, { lightness: 31 }, { visibility: "on" }]
      },
      {
        featureType: "road.arterial",
        elementType: "labels",
        stylers: [{ hue: "#bbc0c4" }, { saturation: -93 }, { lightness: -2 }, { visibility: "simplified" }]
      },
      {
        featureType: "road.local",
        elementType: "geometry",
        stylers: [{ hue: "#e9ebed" }, { saturation: -90 }, { lightness: -8 }, { visibility: "simplified" }]
      },
      {
        featureType: "transit",
        elementType: "all",
        stylers: [{ hue: "#e9ebed" }, { saturation: 10 }, { lightness: 69 }, { visibility: "on" }]
      },
      {
        featureType: "water",
        elementType: "all",
        stylers: [{ hue: "#e9ebed" }, { saturation: -78 }, { lightness: 67 }, { visibility: "simplified" }]
      }
    ],
    assassinsCreedIV: [
      { featureType: "all", elementType: "all", stylers: [{ visibility: "on" }] },
      { featureType: "all", elementType: "labels", stylers: [{ visibility: "off" }, { saturation: "-100" }] },
      {
        featureType: "all",
        elementType: "labels.text.fill",
        stylers: [{ saturation: 36 }, { color: "#000000" }, { lightness: 40 }, { visibility: "off" }]
      },
      {
        featureType: "all",
        elementType: "labels.text.stroke",
        stylers: [{ visibility: "off" }, { color: "#000000" }, { lightness: 16 }]
      },
      { featureType: "all", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
      {
        featureType: "administrative",
        elementType: "geometry.fill",
        stylers: [{ color: "#000000" }, { lightness: 20 }]
      },
      {
        featureType: "administrative",
        elementType: "geometry.stroke",
        stylers: [{ color: "#000000" }, { lightness: 17 }, { weight: 1.2 }]
      },
      { featureType: "landscape", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 20 }] },
      { featureType: "landscape", elementType: "geometry.fill", stylers: [{ color: "#4d6059" }] },
      { featureType: "landscape", elementType: "geometry.stroke", stylers: [{ color: "#4d6059" }] },
      { featureType: "landscape.natural", elementType: "geometry.fill", stylers: [{ color: "#4d6059" }] },
      { featureType: "poi", elementType: "geometry", stylers: [{ lightness: 21 }] },
      { featureType: "poi", elementType: "geometry.fill", stylers: [{ color: "#4d6059" }] },
      { featureType: "poi", elementType: "geometry.stroke", stylers: [{ color: "#4d6059" }] },
      { featureType: "road", elementType: "geometry", stylers: [{ visibility: "on" }, { color: "#7f8d89" }] },
      { featureType: "road", elementType: "geometry.fill", stylers: [{ color: "#7f8d89" }] },
      {
        featureType: "road.highway",
        elementType: "geometry.fill",
        stylers: [{ color: "#7f8d89" }, { lightness: 17 }]
      },
      {
        featureType: "road.highway",
        elementType: "geometry.stroke",
        stylers: [{ color: "#7f8d89" }, { lightness: 29 }, { weight: 0.2 }]
      },
      { featureType: "road.arterial", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 18 }] },
      { featureType: "road.arterial", elementType: "geometry.fill", stylers: [{ color: "#7f8d89" }] },
      { featureType: "road.arterial", elementType: "geometry.stroke", stylers: [{ color: "#7f8d89" }] },
      { featureType: "road.local", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 16 }] },
      { featureType: "road.local", elementType: "geometry.fill", stylers: [{ color: "#7f8d89" }] },
      { featureType: "road.local", elementType: "geometry.stroke", stylers: [{ color: "#7f8d89" }] },
      { featureType: "transit", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 19 }] },
      { featureType: "water", elementType: "all", stylers: [{ color: "#2b3638" }, { visibility: "on" }] },
      { featureType: "water", elementType: "geometry", stylers: [{ color: "#2b3638" }, { lightness: 17 }] },
      { featureType: "water", elementType: "geometry.fill", stylers: [{ color: "#24282b" }] },
      { featureType: "water", elementType: "geometry.stroke", stylers: [{ color: "#24282b" }] },
      { featureType: "water", elementType: "labels", stylers: [{ visibility: "off" }] },
      { featureType: "water", elementType: "labels.text", stylers: [{ visibility: "off" }] },
      { featureType: "water", elementType: "labels.text.fill", stylers: [{ visibility: "off" }] },
      { featureType: "water", elementType: "labels.text.stroke", stylers: [{ visibility: "off" }] },
      { featureType: "water", elementType: "labels.icon", stylers: [{ visibility: "off" }] }
    ],
    unsaturatedBrowns: [
      {
        elementType: "geometry",
        stylers: [{ hue: "#ff4400" }, { saturation: -68 }, { lightness: -4 }, { gamma: 0.72 }]
      },
      { featureType: "road", elementType: "labels.icon" },
      { featureType: "landscape.man_made", elementType: "geometry", stylers: [{ hue: "#0077ff" }, { gamma: 3.1 }] },
      { featureType: "water", stylers: [{ hue: "#00ccff" }, { gamma: 0.44 }, { saturation: -33 }] },
      { featureType: "poi.park", stylers: [{ hue: "#44ff00" }, { saturation: -23 }] },
      {
        featureType: "water",
        elementType: "labels.text.fill",
        stylers: [{ hue: "#007fff" }, { gamma: 0.77 }, { saturation: 65 }, { lightness: 99 }]
      },
      {
        featureType: "water",
        elementType: "labels.text.stroke",
        stylers: [{ gamma: 0.11 }, { weight: 5.6 }, { saturation: 99 }, { hue: "#0091ff" }, { lightness: -86 }]
      },
      {
        featureType: "transit.line",
        elementType: "geometry",
        stylers: [{ lightness: -48 }, { hue: "#ff5e00" }, { gamma: 1.2 }, { saturation: -23 }]
      },
      {
        featureType: "transit",
        elementType: "labels.text.stroke",
        stylers: [{ saturation: -64 }, { hue: "#ff9100" }, { lightness: 16 }, { gamma: 0.47 }, { weight: 2.7 }]
      }
    ],
    classic: [
      {
        featureType: "administrative.country",
        elementType: "geometry",
        stylers: [{ visibility: "simplified" }, { hue: "#ff0000" }]
      }
    ],
    evenLighter: [
      { featureType: "administrative", elementType: "labels.text.fill", stylers: [{ color: "#6195a0" }] },
      { featureType: "landscape", elementType: "all", stylers: [{ color: "#f2f2f2" }] },
      { featureType: "landscape", elementType: "geometry.fill", stylers: [{ color: "#ffffff" }] },
      { featureType: "poi", elementType: "all", stylers: [{ visibility: "off" }] },
      {
        featureType: "poi.park",
        elementType: "geometry.fill",
        stylers: [{ color: "#e6f3d6" }, { visibility: "on" }]
      },
      {
        featureType: "road",
        elementType: "all",
        stylers: [{ saturation: -100 }, { lightness: 45 }, { visibility: "simplified" }]
      },
      { featureType: "road.highway", elementType: "all", stylers: [{ visibility: "simplified" }] },
      {
        featureType: "road.highway",
        elementType: "geometry.fill",
        stylers: [{ color: "#f4d2c5" }, { visibility: "simplified" }]
      },
      { featureType: "road.highway", elementType: "labels.text", stylers: [{ color: "#4e4e4e" }] },
      { featureType: "road.arterial", elementType: "geometry.fill", stylers: [{ color: "#f4f4f4" }] },
      { featureType: "road.arterial", elementType: "labels.text.fill", stylers: [{ color: "#787878" }] },
      { featureType: "road.arterial", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
      { featureType: "transit", elementType: "all", stylers: [{ visibility: "off" }] },
      { featureType: "water", elementType: "all", stylers: [{ color: "#eaf6f8" }, { visibility: "on" }] },
      { featureType: "water", elementType: "geometry.fill", stylers: [{ color: "#eaf6f8" }] }
    ],
    shadesOfGray: [
      {
        featureType: "all",
        elementType: "labels.text.fill",
        stylers: [{ saturation: 36 }, { color: "#000000" }, { lightness: 40 }]
      },
      {
        featureType: "all",
        elementType: "labels.text.stroke",
        stylers: [{ visibility: "on" }, { color: "#000000" }, { lightness: 16 }]
      },
      { featureType: "all", elementType: "labels.icon", stylers: [{ visibility: "off" }] },
      {
        featureType: "administrative",
        elementType: "geometry.fill",
        stylers: [{ color: "#000000" }, { lightness: 20 }]
      },
      {
        featureType: "administrative",
        elementType: "geometry.stroke",
        stylers: [{ color: "#000000" }, { lightness: 17 }, { weight: 1.2 }]
      },
      { featureType: "landscape", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 20 }] },
      { featureType: "poi", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 21 }] },
      {
        featureType: "road.highway",
        elementType: "geometry.fill",
        stylers: [{ color: "#000000" }, { lightness: 17 }]
      },
      {
        featureType: "road.highway",
        elementType: "geometry.stroke",
        stylers: [{ color: "#000000" }, { lightness: 29 }, { weight: 0.2 }]
      },
      { featureType: "road.arterial", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 18 }] },
      { featureType: "road.local", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 16 }] },
      { featureType: "transit", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 19 }] },
      { featureType: "water", elementType: "geometry", stylers: [{ color: "#000000" }, { lightness: 17 }] }
    ]
  };

  class t {
    constructor(n, s) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...s };
      this.options.map = { ...d.map, ...s.map };
      this.element = n;
      this.$element = i(n);
      this.markers = [];
      this.init(n, this.options);
    }
    init() {
      this.build();
      this.adjustHeight();
      return this;
    }
    build() {
      const n = { ...this.options.map, styles: h[this.options.style] };
      const s = new google.maps.Map(this.element, n);
      this.element.lqdMap = s;
      const o = new google.maps.Geocoder;
      o.geocode({
        address: this.options.address
      }, (a, r) => {
        if (r == google.maps.GeocoderStatus.OK) {
          const p = a[0].geometry.location;
          const g = a[0].geometry.location.lat();
          const u = a[0].geometry.location.lng();
          if (this.options.marker_style === "html") {
            this.$element.addClass("marker-html");
          }
          if (this.options.markers == null) {
            this.addMarker(p, s);
          } else {
            for (var c = 0;c < this.options.markers.length; c++) {
              this.addMarker(new google.maps.LatLng(this.options.markers[c][0], this.options.markers[c][1]), s);
            }
          }
          s.setCenter(new google.maps.LatLng(g, u));
          i(".lightbox-link[data-type=inline]").on("mfpOpen", function(m) {
            setTimeout(function() {
              s.setCenter(new google.maps.LatLng(g, u));
            }, 500);
          });
          i(document).on("shown.bs.tab", 'a[data-toggle="tab"]', function(m) {
            setTimeout(function() {
              s.setCenter(new google.maps.LatLng(g, u));
            }, 500);
          });
        }
      });
      this.$element.trigger("liquidMapReady", [s, this.markers]);
      i(document).on("shown.bs.tab", 'a[data-toggle="tab"]', function(a) {
        setTimeout(function() {
          google.maps.event.trigger(s, "resize");
        }, 500);
      });
      return this;
    }
    addMarker(n, s) {
      if (this.options.marker_style === "image" || this.options.marker_style === "default") {
        const o = new google.maps.Marker({
          position: n,
          map: s,
          visible: true,
          icon: this.options.marker,
          zIndex: 9999999
        });
        this.markers.push(o);
      } else if (this.options.marker_style === "html") {
        if (typeof google !== "undefined" && typeof google.maps !== "undefined") {
          this.markers.push(new e(n, s, this.options.className));
        }
      }
    }
    adjustHeight() {
      const n = this.$element.parent(".ld-gmap-container");
      const s = n.parents(".vc_row").last();
      if (!n.siblings().length && s.hasClass("vc_row-o-equal-height")) {
        n.height(n.parent().outerHeight());
      }
    }
  }
  function e(n, s, o) {
    this.latlng_ = n;
    this.className = o;
    this.setMap(s);
  }
  if (typeof google !== "undefined" && typeof google.maps !== "undefined") {
    e.prototype = new google.maps.OverlayView;
    e.prototype.draw = function() {
      let n = this.div_;
      let s;
      let o;
      if (!n) {
        n = this.div_ = document.createElement("DIV");
        n.className = this.className;
        s = document.createElement("div");
        s.className = "lqd-overlay border-radius-circle";
        n.appendChild(s);
        o = document.createElement("div");
        o.className = "lqd-overlay border-radius-circle";
        n.appendChild(o);
        google.maps.event.addDomListener(n, "click", () => {
          google.maps.event.trigger(this, "click");
        });
        const r = this.getPanes();
        r.overlayImage.appendChild(n);
      }
      const a = this.getProjection().fromLatLngToDivPixel(this.latlng_);
      if (a) {
        n.style.left = a.x + "px";
        n.style.top = a.y + "px";
      }
    };
    e.prototype.remove = function() {
      if (this.div_) {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
      }
    };
    e.prototype.getPosition = function() {
      return this.latlng_;
    };
  }
  i.fn[l] = function(n) {
    return this.each(function() {
      const s = i(this).data("plugin-options") || n;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new t(this, s));
      }
    });
  };
})(jQuery);
$liquidWindow.on("load", () => {
  if (typeof google !== "undefined" && google !== null) {
    jQuery("[data-plugin-map]").liquidMap();
  }
});
(function(i) {
  const l = "liquidStoreLocations";
  let d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.args = this.$element.data("args");
      this.$allLocations = this.$element.find(".ld-store-location");
      this.$locationHead = this.$element.find(".ld-store-location__head");
      this.$mapEl = this.$element.find("[data-plugin-map]");
      this.$searchInput = this.$element.find(".ld-store-locations-search__input");
      this.$browserLocationButton = this.$element.find(".ld-store-locations__browser-location .btn");
      this.$notFound = this.$element.find(".ld-store-location__not-found");
      this.mapInstance = null;
      this.markers = [];
      this.pluginOptions = this.$mapEl.data("plugin-options");
      this.init();
    }
    init() {
      const t = this;
      this.$mapEl.on("liquidMapReady", function(e, n, s) {
        t.mapInstance = n;
        t.markers = s;
        const o = 100;
        let a = 0;
        const r = setInterval(function() {
          a++;
          if (t.markers.length || a >= 100) {
            clearInterval(r);
            t.setMarkers();
            t.initFilterInput();
            t.initFindLocation();
          }
        }, 100);
      });
      this.$locationHead.on("click", function() {
        const e = i(this);
        const n = e.parents(".ld-store-location");
        const s = n.data("args");
        n.toggleClass("ld-active");
        const o = new google.maps.LatLng(s.coords.lat, s.coords.lng);
        t.mapInstance.panTo(o);
        if (n.hasClass("ld-active")) {
          n.find(".ld-store-location__body").slideDown(250);
        } else {
          n.find(".ld-store-location__body").slideUp(250);
        }
        t.$allLocations.not(n).removeClass("ld-active");
        t.$allLocations.not(n).find(".ld-store-location__body").slideUp(250);
      });
      this.$allLocations.each(function() {
        const e = i(this);
        const n = e.data("args");
        const { tooltipContent: s } = n;
        e.find("h3").data("original-text", e.find("h3").text());
        if (s) {}
      });
    }
    setMarkers() {
      const t = this;
      const e = [];
      this.$locationHead.each(function(n) {
        const s = i(this);
        const o = s.parents(".ld-store-location");
        const a = o.data("args");
        const r = t.markers[n];
        if (!a.tooltipContent) {
          return;
        }
        const c = `https://www.google.com/maps?q=${a.coords.lat},${a.coords.lng}`;
        const p = `<a class="ld-store-location-tooltip__title" href="${c}" target="_blank" rel="noopener noreferrer">${a.title}</a>`;
        let g = `<div class="ld-store-location-tooltip">${p}`;
        g += `<div class="ld-store-location-tooltip__content">${a.tooltipContent}</div>`;
        g += "</div>";
        const u = new google.maps.InfoWindow({
          content: g,
          ariaLabel: a.title
        });
        e.push(u);
        r.addListener("click", () => {
          e.forEach((m) => m.close());
          u.open(t.map, r);
        });
        s.on("click", function(m) {
          if (!o.hasClass("ld-active"))
            return;
          e.forEach((f) => f.close());
          u.open(t.map, r);
        });
      });
    }
    initAutocomplete() {
      const t = this;
      const e = new google.maps.places.Autocomplete(this.$searchInput.get(0));
      e.addListener("place_changed", () => {
        const n = e.getPlace();
        if (!n.geometry)
          return;
        const s = {
          lat: n.geometry.location.lat(),
          lng: n.geometry.location.lng()
        };
        t.setDistances(s);
      });
    }
    initFilterInput() {
      const t = this;
      this.$searchInput.on("input", function() {
        const e = i(this).val().trim();
        let n = 0;
        t.$allLocations.each(function() {
          const s = i(this);
          const o = s.find("h3");
          const a = o.data("original-text");
          if (e === "") {
            o.text(a);
            s.removeClass("hidden");
            n++;
          } else {
            const r = new RegExp(e, "gi");
            if (r.test(a)) {
              const c = a.replace(r, function(p) {
                return "<mark>" + p + "</mark>";
              });
              o.html(c);
              s.removeClass("hidden");
              n++;
            } else {
              s.addClass("hidden");
            }
          }
        });
        if (n === 0 && e !== "") {
          t.$notFound.removeClass("hidden");
        } else {
          t.$notFound.addClass("hidden");
        }
      });
    }
    setDistances(t) {
      const e = this;
      this.$allLocations.each(function() {
        const n = i(this);
        const s = n.find(".ld-store-location__distance");
        const o = n.data("args");
        const { coords: a } = o;
        s.html("...");
        const r = new google.maps.DistanceMatrixService;
        r.getDistanceMatrix({
          origins: [a],
          destinations: [t],
          travelMode: "DRIVING",
          unitSystem: google.maps.UnitSystem.METRIC
        }, (c, p) => {
          if (p === "OK") {
            const g = c.rows[0].elements[0];
            if (g.status === "OK") {
              if (e.pluginOptions.distanceUnit === "km") {
                s.html(g.distance.text);
              } else {
                s.html((g.distance.value / 1609.34).toFixed(2) + " mi");
              }
            } else {
              s.html(e.args.locations_error_message);
            }
          }
        });
      });
    }
    initFindLocation() {
      const t = this;
      this.$browserLocationButton.on("click", function(e) {
        e.preventDefault();
        if (!navigator.geolocation) {
          return;
        }
        navigator.geolocation.getCurrentPosition((n) => {
          const s = n.coords.latitude;
          const o = n.coords.longitude;
          const a = n.coords.accuracy;
          t.setDistances({ lat: s, lng: o });
          t.$searchInput.val("");
        }, (n) => {
          console.log(n);
        }, {
          enableHighAccuracy: true,
          timeout: 1e4,
          maximumAge: 0
        });
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".ld-store-locations").liquidStoreLocations();
});
(function(i) {
  const l = "liquidMasonry";
  let d = {
    bypassCheck: false,
    layoutMode: "packery",
    itemSelector: ".masonry-item",
    alignMid: false,
    filtersID: null,
    filtersCounter: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.isoData = null;
      this.init();
    }
    init() {
      const t = this.$element.parents(".lqd-tabs-pane, .accordion-collapse");
      if (t.length && t.is(":hidden")) {
        return this.setupIO();
      }
      this.onImagesLoad();
    }
    setupIO() {
      if (this.isoData)
        return;
      new IntersectionObserver(([t], e) => {
        if (t.isIntersecting) {
          e.disconnect();
          this.onImagesLoad();
        }
      }).observe(this.element);
    }
    onImagesLoad() {
      imagesLoaded(this.element, this.handleOnImagesLoaded.bind(this));
    }
    handleOnImagesLoaded() {
      this.initIsotope();
      this.initFilters();
      this.eventHandlers();
    }
    initIsotope() {
      const { layoutMode: t, itemSelector: e, stamp: n, bypassCheck: s } = this.options;
      this.isoData = new Isotope(this.element, {
        bypassCheck: s,
        layoutMode: t,
        itemSelector: e,
        stamp: n
      });
      i(document).trigger("lqd-masonry-layout-init", { detail: { isotopeData: this.isoData } });
    }
    setStamps() {
      this.setAlignMidStamps();
    }
    setAlignMidStamps() {
      const t = this.options;
      if (t.alignMid) {
        const e = i(t.itemSelector, this.element);
        const n = this.$element.attr("data-columns");
        const s = [];
        let o = i(".grid-stamp", this.$element);
        i.each(e, (a, r) => {
          const c = i(r);
          const p = c.outerHeight();
          s.push(p);
        });
        this.highestHeight = Math.max(...s);
        this.lowestHeight = Math.min(...s);
        if (n >= 3) {
          o.clone().insertBefore(e.eq(n - 1)).addClass("is-right");
          o = o.add(".grid-stamp", this.$element);
        }
        o.height(this.lowestHeight / 2);
        t.stamp = ".grid-stamp";
      }
    }
    initFilters() {
      const { filtersID: t, filtersCounter: e } = this.options;
      if (!t) {
        return;
      }
      const n = i(t);
      const s = n.siblings(".lqd-filter-dropdown");
      const o = this.element.hasAttribute("data-liquid-grid-args");
      const a = n.closest(".liquid-filter-items").is("[data-term-counts]") || n.find(".lqd-filter-counter").length > 0;
      i("li", n).each((r, c) => {
        const p = i(c);
        const g = p.attr("data-filter");
        if (e && !a) {
          const u = i(g, this.element);
          const m = i(`
						<sup class="lqd-filter-counter">
							<span>${g === "*" ? this.isoData.items.length : u.length}</span>
						</sup>`);
          m.appendTo(p);
        }
        if (o)
          return;
        p.on("click.lqdMasonryFilter", () => {
          p.addClass("active").siblings().removeClass("active");
          this.isoData.arrange({ filter: g });
        });
      });
      if (!o && s.length) {
        i("select", s).on("selectmenuchange", (r, c) => {
          const p = c.item.value;
          this.isoData.arrange({ filter: p });
        });
      }
    }
    eventHandlers() {
      this.isoData.on("layoutComplete", this.handleLayoutComplete.bind(this));
    }
    handleLayoutComplete() {
      i(document).trigger("lqd-masonry-layout-complete", { detail: { isotopeData: this.isoData } });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("masonry-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (!$liquidContents.length || $liquidContents.length && !$liquidContents[0].hasAttribute("data-liquid-stack")) {
    i("[data-liquid-masonry]").liquidMasonry();
  } else if ($liquidContents.length && $liquidContents[0].hasAttribute("data-liquid-stack")) {
    const l = $liquidContents.attr("data-stack-options");
    if (l) {
      const d = JSON.parse(l);
      const { disableOnMobile: h } = d;
      if (h && (liquidIsMobile() || liquidWindowWidth() <= liquidMobileNavBreakpoint())) {
        i("[data-liquid-masonry]").liquidMasonry();
      }
    }
  }
});
(function(i) {
  const l = "liquidStickyRow";
  let d = {};

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.markupInitiated = false;
      this.$stickyWrap = null;
      this.$stickyWrapInner = null;
      this.boundingClientRect = null;
      this.rowStickyInitPromise = new Promise((n) => {
        this.$element.on("lqd-sticky-row-initiated", n.bind(this, this));
      });
      this.init();
    }
    init() {
      this.initMarkup();
      this.handleSizes();
      this.addEvents();
      this.handleWindowResize();
    }
    initMarkup() {
      if (this.markupInitiated)
        return false;
      const t = i('<div class="lqd-css-sticky-wrap w-100 pos-rel w-full relative" />');
      const e = i('<div class="lqd-css-sticky-wrap-inner w-100 h-200 w-full pos-abs pos-tl absolute top-0 left-0" />');
      this.$element.wrap(t).wrap(e);
      this.$stickyWrapInner = this.$element.parent();
      this.$stickyWrap = this.$element.parent().parent();
      this.markupInitiated = true;
    }
    handleSizes() {
      const t = this.$stickyWrap.nextAll();
      const e = this.$element.outerHeight();
      this.$stickyWrap.css({
        height: e
      });
      if (t.length) {
        let n = 0;
        i.each(t, (s, o) => {
          n += i(o).outerHeight();
        });
        if (e > n) {
          this.$stickyWrapInner.css({
            height: `calc(200% - ${n}px)`
          });
        }
      }
    }
    addEvents() {
      const t = new CustomEvent("lqd-sticky-row-initiated", { detail: { $element: this.$element } });
      this.element.dispatchEvent(t);
    }
    handleWindowResize() {
      const t = liquidDebounce(this.onWindowResize, 500);
      i(window).on("resize", t.bind(this));
    }
    onWindowResize() {
      this.handleSizes();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("sticky-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidWindowWidth() <= liquidMobileNavBreakpoint() || liquidIsMobile())
    return;
  i(".vc_row.lqd-css-sticky, .lqd-force-css-sticky").liquidStickyRow();
});
(function(i) {
  const l = "liquidParticles";
  let d = {
    asBG: false,
    particles: {
      number: {
        value: 40,
        density: {
          enable: false,
          value_area: 800
        }
      },
      color: {
        value: ["#f7ccaf", "#f6cacd", "dbf5f8", "#c5d8f8", "#c5f8ce", "#f7afbd", "#b2d6ef", "#f1ecb7"]
      },
      shape: {
        type: "triangle"
      },
      size: {
        value: 55,
        random: true,
        anim: {
          enable: true,
          speed: 1
        }
      },
      move: {
        direction: "right",
        attract: {
          enable: true
        }
      },
      line_linked: {
        enable: false
      }
    },
    interactivity: {
      events: {
        onhover: {
          enable: false
        },
        onclick: {
          enable: false
        }
      }
    }
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.options.particles = { ...d.particles, ...e.particles };
      this.options.interactivity = { ...d.interactivity, ...e.interactivity };
      this.element = t;
      this.$element = i(t);
      this.build();
    }
    build() {
      this.id = this.element.id;
      if (this.options.interactivity.events.onhover || this.options.interactivity.events.onclick) {
        this.$element.removeClass("pointer-events-none");
        this.$element.addClass("pointer-events-auto");
      }
      this.asSectionBg();
      this.init();
    }
    init() {
      particlesJS(this.id, this.options);
    }
    asSectionBg() {
      if (this.options.asBG) {
        const t = i('<div class="lqd-particles-bg-wrap lqd-overlay pointer-events-none"></div>');
        const e = this.$element.parent(".ld-particles-container");
        let n = this.$element.parents(".vc_row").last();
        if (liquidIsElementor) {
          n = this.$element.parents(".elementor-section").last();
          if (!n.length) {
            n = this.$element.parent(".e-container");
          }
          if (!n.length) {
            n = this.$element.closest(".e-con");
          }
        }
        const s = liquidIsElementor ? n.children(".elementor-container") : n.children(".ld-container");
        const o = n.children(".lqd-sticky-bg-wrap");
        t.append(e);
        if (o.length) {
          t.appendTo(o);
        } else if (n.hasClass("pp-section")) {
          t.prependTo(n);
        } else {
          if (s.length) {
            t.insertBefore(s);
          } else {
            t.prependTo(n);
          }
        }
      }
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("particles-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-particles=true]").filter((l, d) => {
    const h = i(d);
    const t = h.closest(".vc_row.pp-section");
    return !t.length;
  }).liquidParticles();
});
(function(i) {
  const l = "liquidPin";
  let d = {
    trigger: "self",
    start: "top top",
    end: null,
    endTrigger: false,
    duration: "contentsHeight",
    offset: 0,
    pinSpacing: false,
    pinReparent: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.ST = null;
      this.spacerElement = null;
      this.offset = 0;
      this.start = 0;
      this.end = 0;
      this.rect = {};
      this.pinPromise = new Promise((n) => {
        this.element.addEventListener("element-was-pinned", n(this, this));
      });
      this.init();
    }
    async init() {
      await this.measure();
      this.pin();
      this.events();
      this.handleResize();
      this.element.dispatchEvent(new CustomEvent("element-was-pinned", { bubbles: false }));
    }
    async measure() {
      this.rect = await this.getRect();
      this.offset = await this.getOffset();
      this.start = await this.getStart();
      this.end = await this.getEnd();
    }
    async getOffset() {
      const { offset: t } = this.options;
      if (isNaN(parseInt(t), 10)) {
        return await this.getOffsetElementsHeight();
      }
      return t;
    }
    async getStart() {
      const { start: t } = this.options;
      return `${t}+=${this.offset}`;
    }
    getRect() {
      return fastdomPromised.measure(() => {
        return this.element.getBoundingClientRect();
      });
    }
    pin() {
      fastdom.mutate(() => {
        const { pinSpacing: t, pinReparent: e, trigger: n } = this.options;
        let s = "(min-width: 992px)";
        if (this.element.classList.contains("lqd-custom-menu")) {
          s = "all";
        } else if (this.element.classList.contains("lqd-add-to-cart-row")) {
          s = "(max-width: 767px)";
        }
        ScrollTrigger.matchMedia({
          [`${s}`]: async () => {
            this.ST = ScrollTrigger.create({
              trigger: n === "self" ? this.element : i(n)[0],
              pin: true,
              start: () => this.start,
              endTrigger: this.getEndTrigger(),
              end: () => this.end,
              pinSpacing: t,
              pinReparent: e,
              onEnter: () => {
                this.element.classList.add("lqd-pin-is-stuck");
              },
              onLeave: () => {
                this.element.classList.remove("lqd-pin-is-stuck");
              },
              onLeaveBack: () => {
                this.element.classList.remove("lqd-pin-is-stuck");
              },
              onEnterBack: () => {
                this.element.classList.add("lqd-pin-is-stuck");
              }
            });
            this.spacerElement = this.ST.spacer;
          }
        });
      });
    }
    async getOffsetElementsHeight() {
      const { options: t } = this;
      const e = [];
      let n = 0;
      t.offset.split(",").forEach((o) => {
        const a = document.querySelector(o);
        if (a) {
          const r = new Promise((c) => {
            new IntersectionObserver(([p], g) => {
              g.disconnect();
              c(p.boundingClientRect.height);
            }).observe(a);
          });
          e.push(r);
        }
      });
      const s = await Promise.all(e);
      s.forEach((o) => n += o);
      return n;
    }
    getEnd() {
      return fastdomPromised.measure(() => {
        let { duration: t, end: e } = this.options;
        if (e) {
          return e;
        }
        if (t === "contentsHeight") {
          if (this.element.classList.contains("lqd-sticky-bg-wrap") || this.element.classList.contains("lqd-section-borders-wrap")) {
            const n = this.spacerElement ? i(this.spacerElement).siblings(".ld-container") : this.$element.siblings(".ld-container");
            const s = n[0].offsetHeight;
            t = `+=${s}`;
          } else {
            t = `+=${this.rect.height}`;
          }
        }
        if (t === "parent") {
          if (this.element.classList.contains("vc_column-inner")) {
            t = `+=${this.element.closest(".ld-row").offsetHeight - this.rect.height}`;
          } else {
            let n = 0;
            this.$element.children().each((s, o) => n += i(o).outerHeight(true));
            t = `+=${this.rect.height - n}`;
          }
        }
        if (t === "last-link") {
          const n = i("a", this.element).last();
          const s = n.attr("href");
          let o;
          if (s !== "" && s.startsWith("#") && i(s).length) {
            o = i(s);
          } else {
            o = $liquidContents;
          }
          t = `+=${o.offset().top + o.outerHeight() - (this.rect.top + window.scrollY) - this.offset}`;
        }
        return t;
      });
    }
    getEndTrigger() {
      const { duration: t } = this.options;
      let { endTrigger: e } = this.options;
      if (t === "parent") {
        e = this.spacerElement ? this.spacerElement.parentElement : this.element.parentElement;
      }
      return e;
    }
    events() {
      i(document).on("lqd-header-sticky-change lqd-masonry-layout-complete lqd-carousel-initialized", async () => {
        await this.measure();
        this.ST && this.ST.refresh();
      });
    }
    handleResize() {
      const t = liquidDebounce(this.onWindowResize, 250);
      i(window).on("resize", t.bind(this));
    }
    async onWindowResize() {
      await this.measure();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("pin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  const l = i(i("[data-pin=true]").get().reverse());
  l.liquidPin();
});
(function(i) {
  const l = "liquidProgressbar";
  let d = {
    value: 0,
    suffix: null,
    prefix: null,
    skipCreateMarkup: false,
    orientation: "horizontal"
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.valueEl = i(".liquid-progressbar-value, .lqd-progressbar-value", t);
      this.prefixEl = i(".liquid-progressbar-prefix, .lqd-progressbar-prefix", t);
      this.suffixEl = i(".liquid-progressbar-suffix, .lqd-progressbar-prefix", t);
      this.percentageElement = i(".liquid-progressbar-percentage, .lqd-progressbar-percentage", t);
      this.barElement = i(".liquid-progressbar-bar, .lqd-progressbar-bar", t);
      this.titleElement = i(".liquid-progressbar-title, .lqd-progressbar-title", t);
      this.isRTL = i("html").attr("dir") == "rtl";
      this.init();
    }
    init() {
      this.addValue();
      this.addPrefixSuffix();
      this.setupIntersectionObserver();
    }
    addValue() {
      if (this.options.skipCreateMarkup || this.valueEl.length || !this.percentageElement.length)
        return;
      this.valueEl = i('<span class="liquid-progressbar-value lqd-progressbar-value">0</span>');
      this.percentageElement.html("");
      this.valueEl.appendTo(this.percentageElement);
    }
    addPrefixSuffix() {
      if (this.options.skipCreateMarkup || this.prefixEl.length && this.suffixEl.length || !this.percentageElement.length)
        return;
      const t = this.options.prefix;
      const e = this.options.suffix;
      const n = i('<span class="liquid-progressbar-prefix lqd-progressbar-prefix"></span>');
      const s = i('<span class="liquid-progressbar-suffix lqd-progressbar-suffix"></span>');
      if (t)
        n.text(t);
      if (e)
        s.text(e);
      n.prependTo(this.percentageElement);
      s.appendTo(this.percentageElement);
    }
    checkValuesEncountering() {
      if (!this.percentageElement.length)
        return;
      if (this.options.orientation == "horizontal" && this.titleElement.length) {
        const t = this.titleElement.width();
        const e = this.percentageElement.offset().left || 0;
        const n = this.percentageElement.width();
        const s = this.titleElement.offset().left || 0;
        if (!this.isRTL) {
          if (e >= s + t) {
            this.$element.addClass("values-not-encountering");
          } else {
            this.$element.removeClass("values-not-encountering");
          }
        } else {
          if (e + n <= s) {
            this.$element.addClass("values-not-encountering");
          } else {
            this.$element.removeClass("values-not-encountering");
          }
        }
      } else {
        this.$element.addClass("values-not-encountering");
      }
    }
    setupIntersectionObserver() {
      new IntersectionObserver(([t], e) => {
        if (t.isIntersecting) {
          this.animatePercentage();
          this.animateProgressbar();
          e.unobserve(t.target);
        }
      }, { threshold: 1 }).observe(this.element);
    }
    animatePercentage() {
      const t = { value: 0 };
      gsap.to(t, {
        value: this.options.value,
        duration: 1.2,
        ease: "power3.inOut",
        onUpdate: () => {
          this.percentageElement.length && this.valueEl.text(Math.round(t.value));
        }
      });
    }
    animateProgressbar() {
      const t = this.barElement.get(0);
      const e = this.options.value + "%";
      const n = this.options.orientation;
      if (n === "horizontal") {
        this.animateHorizontal(t, e);
      } else {
        this.initCircleProgressbar(e);
      }
    }
    animateHorizontal(t, e) {
      gsap.to(t, {
        width: e,
        duration: 1.2,
        ease: "power3.inOut",
        onUpdate: () => {
          this.checkValuesEncountering();
        }
      });
    }
    initCircleProgressbar(t) {
      const e = i(this.element).find(".ld-prgbr-circle-container");
      const n = e.width();
      const s = parseInt(t, 10);
      e.circleProgress({
        value: s / 100,
        size: n,
        lineCap: "round",
        startAngle: -Math.PI / 2
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("progressbar-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-progressbar]").liquidProgressbar();
});
(function(i) {
  const l = "liquidResponsiveAccordion";
  let d = {
    triggers: ".lqd-tabs-nav a",
    contents: ".lqd-tabs-pane",
    parent: ".lqd-tabs"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.$element = i(t);
      this.DOM.$triggers = this.DOM.$element.find(this.options.triggers);
      this.DOM.$contents = this.DOM.$element.find(this.options.contents);
      this.DOM.responsiveTriggers = [];
      this.init();
    }
    init() {
      this.createTriggers();
      this.appendTriggers();
      this.initCollapse();
    }
    createTriggers() {
      this.DOM.$triggers.each((t, e) => {
        const n = i(e).clone();
        const s = i('<h2 role="tab" class="lqd-res-acc-trigger" />');
        s.append(n);
        this.DOM.responsiveTriggers.push(s);
      });
    }
    appendTriggers() {
      this.DOM.$contents.each((t, e) => {
        i(this.DOM.responsiveTriggers[t]).insertBefore(e);
      });
    }
    initCollapse() {
      i.each(this.DOM.responsiveTriggers, (t, e) => {
        const n = i(e).children("a");
        const s = n.closest(this.options.parent);
        const o = s.find(this.options.contents);
        n.off("click");
        n.on("click", (a) => {
          a.preventDefault();
          const r = i(n.attr("href"));
          n.parent().siblings(".lqd-res-acc-trigger").removeClass("is-active");
          o.not(r).removeClass("is-active").stop().slideUp(300);
          r.toggleClass("is-active").stop().slideToggle(300);
          n.parent().toggleClass("is-active");
        });
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("res-acc-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".woocommerce-tabs").liquidResponsiveAccordion({
    triggers: ".wc-tabs > li > a",
    contents: ".woocommerce-Tabs-panel",
    parent: ".woocommerce-tabs"
  });
});
(function(i) {
  const l = "liquidReveal";
  let d = {
    isContentHidden: true,
    animteWhenInView: true,
    delay: 0,
    revealSettings: {
      direction: "lr",
      bgcolor: "#f0f0f0",
      duration: 0.5,
      ease: "power4.inOut",
      coverArea: 0,
      onCover: function(t, e) {
        return false;
      },
      onStart: function(t, e) {
        return false;
      },
      onComplete: function(t, e) {
        return false;
      },
      onCoverAnimations: null
    }
  };

  class h {
    constructor(t, e) {
      this.options = { ...d, ...e };
      this._defaults = d;
      this._name = l;
      this.element = t;
      this.$element = i(t);
      this.$content = this.$element.children();
      this.revealer = null;
      this.init();
    }
    init() {
      this._layout();
      if (this.options.animteWhenInView) {
        this.setIntersectionObserver();
      } else {
        imagesLoaded(this.element, this.doTheReveal.bind(this));
      }
    }
    _createDOMEl(t, e, n) {
      var s = document.createElement(t);
      s.className = e || "";
      s.innerHTML = n || "";
      return s;
    }
    _layout() {
      const t = getComputedStyle(this.element).position;
      if (t !== "fixed" && t !== "absolute" && t !== "relative") {
        this.element.style.position = "relative";
      }
      if (this.options.isContentHidden) {
        this.$content.css("opacity", 0);
      }
      this.revealer = this._createDOMEl("div", "block-revealer__element");
      this.element.classList.add("block-revealer");
      this.element.appendChild(this.revealer);
    }
    _getTransformSettings(t) {
      var e, n, s;
      switch (t) {
        case "lr":
          e = "scaleX(0)";
          n = "0 50%";
          s = "100% 50%";
          break;
        case "rl":
          e = "scaleX(0)";
          n = "100% 50%";
          s = "0 50%";
          break;
        case "tb":
          e = "scaleY(0)";
          n = "50% 0";
          s = "50% 100%";
          break;
        case "bt":
          e = "scaleY(0)";
          n = "50% 100%";
          s = "50% 0";
          break;
        default:
          e = "scaleX(0)";
          n = "0 50%";
          s = "100% 50%";
          break;
      }
      return {
        val: e,
        origin: { initial: n, halfway: s }
      };
    }
    reveal(t) {
      if (this.isAnimating) {
        return false;
      }
      this.isAnimating = true;
      var e = {
        duration: 0.5,
        ease: "power4.inOut",
        delay: this.options.delay ? this.options.delay / 1000 : 0,
        bgcolor: "#f0f0f0",
        direction: "lr",
        coverArea: 0
      }, n = t || this.options.revealSettings, s = n.direction || e.direction, o = this._getTransformSettings(s);
      this.revealer.style.WebkitTransform = this.revealer.style.transform = o.val;
      this.revealer.style.WebkitTransformOrigin = this.revealer.style.transformOrigin = o.origin.initial;
      if (!liquidIsElementor) {
        this.revealer.style.background = n.bgcolor || e.bgcolor;
      }
      this.revealer.style.opacity = 1;
      var a = this, r = {
        onComplete: function() {
          a.isAnimating = false;
          if (typeof n.onComplete === "function") {
            n.onComplete(a.content, a.revealer);
          }
          i(a.element).addClass("revealing-ended").removeClass("revealing-started");
        }
      }, c = {
        delay: n.delay ? n.delay / 1000 : e.delay,
        onComplete: function() {
          a.revealer.style.WebkitTransformOrigin = a.revealer.style.transformOrigin = o.origin.halfway;
          if (typeof n.onCover === "function") {
            n.onCover(a.content, a.revealer);
          }
          i(a.element).addClass("element-uncovered");
          gsap.to(a.revealer, {
            ...r
          });
        }
      };
      c.duration = r.duration = n.duration ? n.duration / 1000 : e.duration;
      c.ease = r.ease = n.ease || e.ease;
      var p = n.coverArea || e.coverArea;
      if (s === "lr" || s === "rl") {
        c.keyframes = [{ scaleX: 0 }, { scaleX: 1, duration: c.duration }];
        r.keyframes = [
          { scaleX: 1 },
          { scaleX: p / 100, duration: c.duration }
        ];
      } else {
        c.keyframes = [{ scaleY: 0 }, { scaleY: 1, duration: c.duration }];
        r.keyframes = [
          { scaleY: 1 },
          { scaleY: p / 100, duration: c.duration }
        ];
      }
      if (typeof n.onStart === "function") {
        n.onStart(a.content, a.revealer);
      }
      i(a.element).addClass("revealing-started");
      gsap.to(a.revealer, {
        ...c
      });
    }
    setIntersectionObserver() {
      new IntersectionObserver(([t], e) => {
        if (t.isIntersecting) {
          e.disconnect();
          i(t.target).imagesLoaded(this.doTheReveal.bind(this));
        }
      }).observe(this.element);
    }
    doTheReveal() {
      const t = this.options.revealSettings.onCoverAnimations || [{ scale: 0.9 }, { scale: 1 }];
      const e = {
        onCover: () => {
          if (this.options.isContentHidden) {
            this.$content.css("opacity", 1);
          }
          gsap.fromTo(this.element.querySelector("figure"), {
            ...t[0]
          }, {
            duration: 0.8,
            ease: "power4.out",
            ...t[1]
          });
        }
      };
      const n = { ...this.options, ...e };
      this.reveal(n);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("reveal-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (window.$liquidContents.length && window.$liquidContents[0].hasAttribute("data-liquid-stack"))
    return;
  i("[data-reveal]").filter((l, d) => {
    const h = i(d);
    const t = h.find(".ld-lazyload");
    return !t.length;
  }).liquidReveal();
});
(function(i) {
  const l = "liquidSetActiveOnhover";
  let d = {
    classname: "lqd-is-active",
    offClassname: "lqd-was-active",
    triggerHandlers: ["mouseenter", "mouseleave"],
    triggers: "> li",
    targets: "",
    lazyLoadImgVid: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.$element = i(t);
      this.DOM.$triggers = this.options.triggers === "self" ? this.DOM.$element : this.DOM.$element.find(this.options.triggers);
      this.DOM.$targets = this.options.targets === "self" ? this.DOM.$element : this.DOM.$element.find(this.options.targets);
      if (this.DOM.$element.parents("[data-lqd-fullproj]").length) {
        this.DOM.$fullscreenProjectParent = this.DOM.$element.parents("[data-lqd-fullproj]");
      }
      this.listenToFullscreenProjectParent();
      this.init();
    }
    listenToFullscreenProjectParent() {
      if (this.DOM.$targets.length > 1 || this.DOM.$triggers.length > 1)
        return;
      const { classname: t, offClassname: e } = this.options;
      const n = this.DOM.$targets.eq(0);
      const s = this.DOM.$triggers.eq(0);
      this.DOM.$fullscreenProjectParent && this.DOM.$fullscreenProjectParent.on("show.bs.collapse", () => {
        n.add(s).addClass(t);
        n.add(s).removeClass(e);
        n.add(s).css("pointer-events", "none");
        this.handleLazyload(n.add(s));
      });
      this.DOM.$fullscreenProjectParent && this.DOM.$fullscreenProjectParent.on("hide.bs.collapse", () => {
        n.add(s).removeClass(t);
        n.add(s).removeClass(e);
        n.add(s).css("pointer-events", "");
        n.find("video")[0]?.pause();
      });
    }
    init() {
      const { triggerHandlers: t, classname: e, offClassname: n, lazyLoadImgVid: s } = this.options;
      this.DOM.$triggers.each((o, a) => {
        const r = i(a);
        let c = this.DOM.$targets.eq(o);
        if (!c.length) {
          c = r;
        }
        if (t[0] === t[1]) {
          r.on(t[0], () => {
            c.add(a).toggleClass(e);
          });
        } else {
          r.on(t[0], () => {
            this.DOM.$targets.add(this.DOM.$triggers).removeClass(e);
            c.add(a).addClass(e);
            c.add(a).removeClass(n);
            s && this.handleLazyload(c);
          });
          if (t[1] != null) {
            r.on(t[1], () => {
              c.add(a).removeClass(e);
              c.add(a).addClass(n);
            });
            i(document).on("click.lqdActiveOnHoverClick", (p) => {
              if (!c[0].contains(p.target)) {
                c.add(a).removeClass(e);
                c.add(a).addClass(n);
              }
            });
          }
        }
      });
    }
    handleLazyload(t) {
      const e = t.find("img, source");
      e.each((n, s) => {
        const o = i(s);
        const a = o.attr("data-src");
        if (a) {
          o.attr("src", a);
        }
        if (o.is("source") && n === e.length - 1) {
          const r = o.parent("video")[0];
          r.load();
          r.play();
        }
      });
    }
    destroy() {
      const { triggers: t } = this.options;
      const e = this.DOM.$element.find(t);
      e.each((n, s) => {
        i(s).off();
      });
      i.data(this, "plugin_" + l, null);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("active-onhover-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-active-onhover]").liquidSetActiveOnhover();
});
(function(i) {
  const l = "liquidShrinkBorders";
  let d = {
    start: "top",
    end: "bottom-=30%",
    scrub: 0.2
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.$parentRow = this.$element.closest(".vc_row");
      this.$contents = this.$parentRow.children(".container").length ? this.$parentRow.children(".container") : this.$parentRow.children(".ld-container");
      this.contentsHeight = this.$contents.height();
      this.$animatables = this.$element.children();
      if (this.$parentRow[0].hasAttribute("data-row-bg")) {
        this.$parentRow.on("lqdrowbginit", this.init.bind(this));
      } else {
        this.init();
      }
    }
    init() {
      this._initScrollTrigger();
      this.$element.addClass("sticky-applied");
    }
    _initScrollTrigger() {
      const t = this.$element.siblings(".row-bg-wrap");
      const { start: e, end: n, scrub: s } = this.options;
      const o = gsap.timeline();
      if (t.length) {
        this.$animatables = this.$animatables.add(t);
      }
      i.each(this.$animatables, (a, r) => {
        const c = i(r);
        const p = c.attr("data-axis");
        const g = {
          startAt: {}
        };
        if (p === "x") {
          g.startAt.scaleX = 1;
          g.scaleX = 0;
        } else if (p === "y") {
          g.startAt.scaleY = 1;
          g.scaleY = 0;
        } else {
          g.startAt.scale = 1.05;
          g.scale = 1;
        }
        o.to(r, {
          ...g
        }, 0);
      });
      ScrollTrigger.create({
        animation: o,
        trigger: this.element,
        start: e,
        end: n,
        scrub: s,
        toggleClass: { targets: this.$animatables.get(), className: "will-change" }
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidWindowWidth() <= liquidMobileNavBreakpoint() || liquidIsMobile())
    return false;
  i("[data-shrink-borders]").liquidShrinkBorders();
});
(function(i) {
  const l = "liquidSlideElement";
  let d = {
    hiddenElement: null,
    visibleElement: null,
    hiddenElementOnHover: null,
    alignMid: false,
    waitForSplitText: false,
    disableOnMobile: false,
    triggerElement: "self"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.$triggerElement = this.options.triggerElement === "self" ? this.$element : this.$element.closest(this.options.triggerElement);
      this.timeline = gsap.timeline();
      if (this.options.waitForSplitText) {
        const n = this.$element.find("[data-split-text]");
        const s = [];
        if (n.length) {
          n.liquidSplitText({ forceApply: true });
          n.each((o, a) => {
            const r = i(a);
            const c = r.data("plugin_liquidSplitText");
            if (c) {
              s.push(c.splitDonePormise);
            }
          });
        }
        if (s.length > 0) {
          Promise.all(s).then(this.init.bind(this));
        }
      } else {
        this.init();
      }
    }
    init() {
      this.getElements();
      if (!this.$hiddenElement.length || !this.$visibleElement.length) {
        return;
      }
      imagesLoaded(this.element, () => {
        this.hiddenElementHeight = this.getHiddenElementHeight();
        this.$element.addClass("hide-target");
        this.createTimeline();
        this.moveElements();
        this.eventListeners();
      });
    }
    getElements() {
      this.$hiddenElement = i(this.options.hiddenElement, this.element);
      this.$visibleElement = i(this.options.visibleElement, this.element);
      this.$hiddenElementOnHover = i(this.options.hiddenElementOnHover, this.element);
      this.$hiddenElement.wrap('<div class="ld-slideelement-hidden" />').wrap('<div class="ld-slideelement-hidden-inner" />');
      this.$visibleElement.wrap('<div class="ld-slideelement-visible" />').wrap('<div class="ld-slideelement-visible-inner" />');
      this.$hiddenElementWrap = this.$hiddenElement.closest(".ld-slideelement-hidden");
      this.$hiddenElementInner = this.$hiddenElement.closest(".ld-slideelement-hidden-inner");
      this.$visibleElementWrap = this.$visibleElement.closest(".ld-slideelement-visible");
      this.$visibleElementInner = this.$visibleElement.closest(".ld-slideelement-visible-inner");
    }
    getHiddenElementHeight() {
      let t = 0;
      i.each(this.$hiddenElement, (e, n) => {
        t += i(n).outerHeight(true);
      });
      return t;
    }
    getHiddenElementChilds() {
      return this.$hiddenElementInner.children().map((t, e) => e.parentElement);
    }
    getVisibleElementChilds() {
      return this.$visibleElementInner.children().map((t, e) => e.parentElement);
    }
    moveElements() {
      const t = this.options.alignMid ? this.hiddenElementHeight / 2 : this.hiddenElementHeight;
      this.$visibleElementWrap.css({
        transform: `translateY(${t}px)`
      });
      this.$hiddenElementWrap.css({
        transform: `translateY(${t}px)`
      });
    }
    createTimeline() {
      const { options: t } = this;
      const e = [...this.getVisibleElementChilds(), ...this.getHiddenElementChilds()];
      let n = t.alignMid ? this.hiddenElementHeight / 2 : this.hiddenElementHeight;
      if (t.hiddenElementOnHover) {
        const s = this.$hiddenElementOnHover.outerHeight(true);
        n = t.alignMid ? (this.hiddenElementHeight + s) / 2 : this.hiddenElementHeight + s;
      }
      this.timeline.to(e, {
        y: n * -1,
        opacity: (s, o) => {
          if (i(o).is(i(this.$hiddenElementOnHover).parent())) {
            return 0;
          }
          return 1;
        },
        ease: "power3.out",
        duration: 0.65,
        stagger: 0.065
      }).pause();
    }
    eventListeners() {
      const t = liquidDebounce(this.onWindowResize.bind(this), 500);
      this.$triggerElement.on("mouseenter.lqdSlideElementOnHover", this.onMouseEnter.bind(this));
      this.$triggerElement.on("mouseleave.lqdSlideElementOnHover", this.onMouseLeave.bind(this));
      i(window).on("resize.lqdSlideElementOnResize", t);
    }
    onMouseEnter() {
      this.timeline.play();
    }
    onMouseLeave() {
      this.timeline.reverse();
    }
    onWindowResize() {
      this.hiddenElementHeight = this.getHiddenElementHeight();
      this.moveElements();
    }
    destroy() {
      this.$triggerElement.off("mouseenter.lqdSlideElementOnHover mouseleave.lqdSlideElementOnHover");
      i(window).off("resize.lqdSlideElementOnResize");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("slideelement-options"), ...t };
      if (e.disableOnMobile && liquidIsMobile()) {
        return;
      }
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  const l = i("[data-slideelement-onhover]").filter((d, h) => {
    return !i(h).parents("[data-custom-animations]").length && !h.hasAttribute("data-custom-animations") && h.clientHeight > 0;
  });
  l.liquidSlideElement();
});
(function(i) {
  const l = $liquidMainHeader.length && $liquidMainHeader[0].getAttribute("data-sticky-options");
  const d = l && JSON.parse(l).dynamicColors === true;
  const h = "liquidStack";
  let t = {
    sectionSelector: "#lqd-contents-wrap > .vc_row, #lqd-contents-wrap > .vc_section",
    anchors: [],
    easing: "linear",
    scrollingSpeed: "1.2s",
    loopTop: false,
    loopBottom: false,
    navigation: false,
    defaultTooltip: "Section",
    prevNextButtons: true,
    prevNextLabels: { prev: "Previous", next: "Next" },
    pageNumber: true,
    effect: "none",
    disableOnMobile: true,
    normalScrollElements: null,
    normalScrollElementTouchThreshold: 5,
    touchSensitivity: 5
  };
  const e = i("[data-back-to-top], [data-lqd-scroll-indicator]");

  class n {
    constructor(s, o) {
      this._defaults = t;
      this._name = h;
      this.options = { ...t, ...o };
      this.element = s;
      this.$element = i(s);
      if (this.options.disableOnMobile && (liquidIsMobile() || liquidWindowWidth() <= liquidMobileNavBreakpoint()))
        return false;
      this.lastScrolledDestiny;
      this.lastAnimation = 0;
      this.scrollings = [];
      this.isTouch = "ontouchstart" in window || navigator.msMaxTouchPoints > 0 || navigator.maxTouchPoints;
      this.touchStartY = 0;
      this.touchEndY = 0;
      this.prevTime = new Date().getTime();
      this.scrollDelay = 600;
      this.anchors = [];
      this.tooltips = [];
      this.$sectionElements = null;
      this.$ppNav = null;
      this.$ppNavCurrent = null;
      this.$prevNextButtons = i(".lqd-stack-prevnext-wrap");
      this.$pageNumber = i(".lqd-stack-page-number");
      this.$stickyModules = liquidIsElementor ? $liquidMainHeader.find("> .elementor > .elementor-section-wrap > .elementor-section, > .elementor > .elementor-section, > .elementor > .e-container, > .elementor > .e-con").not(".lqd-hide-onstuck").find('[data-element_type="widget"]') : $liquidMainHeader.find(".lqd-head-sec-wrap, .lqd-stickybar-wrap").not(".lqd-hide-onstuck").find(".lqd-head-col > .header-module");
      this.$pageNumbersStyle = $liquidBody.hasClass("lqd-stack-nums-style-1") ? "style1" : $liquidBody.hasClass("lqd-stack-nums-style-2") ? "style2" : "";
      this.element.style.setProperty("--lqd-stack-animation-speed", this.options.scrollingSpeed);
      this.stackInitPromise = new Promise((a) => {
        this.$element.on("stackinit", a.bind(this, this));
      });
      this.$mainNavLocalScroll = i(".main-nav");
      this.onWindowResize = liquidDebounce(this.onWindowResize.bind(this), 500);
      this.hashChangeHandler = this.hashChangeHandler.bind(this);
      this.buildSections();
      this.addMouseWheelHandler();
      this.handleKeys();
      this.addTouchHandler();
    }
    moveSectionUp() {
      var s = this.getActiveSection().$element.prevAll(".pp-section").filter(":not(:hidden)").first();
      if (!s.length && this.options.loopTop) {
        s = this.$sectionElements.last();
      }
      if (s.length) {
        this.scrollPage(s);
      }
    }
    moveSectionDown() {
      var s = this.getActiveSection().$element.nextAll(".pp-section").filter(":not(:hidden)").first();
      if (!s.length && this.options.loopBottom) {
        s = this.$sectionElements.first();
      }
      if (s.length) {
        this.scrollPage(s);
      }
    }
    moveTo(s) {
      let o;
      if (isNaN(s)) {
        o = this.$sectionElements.filter((a, r) => r.getAttribute("data-anchor") === s);
      } else {
        o = this.$sectionElements.eq(s - 1);
      }
      this.scrollPage(o);
    }
    getActiveSection() {
      let s = 0;
      let o = this.$sectionElements.first();
      this.$sectionElements.each((a, r) => {
        if (r.classList.contains("active")) {
          s = a;
          o = this.$sectionElements.eq(a);
        }
      });
      if (o.is(":hidden")) {
        const a = o.prevAll(":not(:hidden)").first();
        const r = o.nextAll(":not(:hidden)").first();
        o = a.length ? a : r;
        s = o.index();
      }
      if (!o.length) {
        o = this.$sectionElements.filter(":not(:hidden)").first();
        s = o.index();
      }
      return {
        $element: o,
        index: s
      };
    }
    makeScrollable(s) {
      const o = i(s);
      const a = o.children().filter((p, g) => {
        const u = i(g).css("position");
        return u !== "absolute" && u !== "fixed";
      });
      const r = o.outerHeight();
      let c = 0;
      if (a.length) {
        a.each((p, g) => {
          c += i(g).outerHeight();
        });
      }
      if (c > r) {
        const p = parseInt(o.css("paddingTop"), 10) + parseInt(o.css("paddingBottom"), 10);
        o.addClass("pp-scrollable");
        s.style.setProperty("--lqd-section-height", `${c + p}px`);
      } else {
        o.removeClass("pp-scrollable");
        s.style.removeProperty("--lqd-section-height");
      }
    }
    addTableClass(s) {
      const o = i(s);
      o.addClass("d-flex flex-column flex-nowrap align-content-start flex flex-col content-start");
      if (!o.children(".pp-section-wrap").length) {
        o.wrapInner('<div class="pp-section-wrap"><div class="lqd-stack-section-inner"></div></div>');
      }
    }
    getYmovement(s) {
      var o = this.getActiveSection().index;
      var a = this.$sectionElements.index(s);
      let r = "up";
      if (o < a) {
        r = "down";
      }
      return r;
    }
    scrollPage(s) {
      const o = this.getActiveSection();
      var a = {
        destination: s,
        activeSection: o.$element,
        anchorLink: s.data("anchor"),
        sectionIndex: this.$sectionElements.index(s),
        toMove: s,
        yMovement: this.getYmovement(s),
        leavingSection: o.index + 1
      };
      if (a.activeSection.is(s)) {
        return;
      }
      if (typeof a.anchorLink !== "undefined") {
        this.setURLHash(a.anchorLink, a.sectionIndex);
      }
      this.$sectionElements.removeClass("active");
      this.setActiveSection(a.sectionIndex);
      a.sectionsToMove = this.getSectionsToMove(a);
      a.translate3d = "";
      if (a.yMovement === "down") {
        if (a.destination.is(".pp-auto-height")) {
          var r = a.destination.outerHeight() * -1 + "px";
          a.translate3d = `translate3d(0px, ${r}, 0px)`;
          a.scrolling = r;
          a.sectionsToMove = a.activeSection;
        } else {
          a.scrolling = "-100%";
        }
        a.animateSection = a.activeSection;
      } else {
        a.scrolling = "0";
        a.animateSection = s;
      }
      this.onLeave(a.leavingSection, a.sectionIndex + 1, a.yMovement);
      this.performMovement(a);
      this.activateNavDots(a.sectionIndex);
      this.lastScrolledDestiny = a.anchorLink;
      this.lastAnimation = new Date().getTime();
    }
    performMovement(s) {
      this.timeout1 = setTimeout(() => {
        this.transformContainer(s.animateSection, s.translate3d);
        s.sectionsToMove.each(() => {
          this.transformContainer(i(this), s.translate3d);
        });
        this.afterSectionLoads(s);
      }, parseFloat(this.options.scrollingSpeed) * 250);
      this.timeout1 = setTimeout(() => {
        $liquidMainFooter.removeClass("lqd-stack-row-entering lqd-stack-row-leaving");
      }, parseFloat(this.options.scrollingSpeed) * 1000);
    }
    afterSectionLoads(s) {
      this.afterLoad(s.anchorLink, s.sectionIndex + 1);
    }
    afterLoad(s, o) {
      const a = i(this.$sectionElements[o - 1]);
      this.$ppNav.css("pointer-events", "");
      $liquidBody.removeClass("lqd-stack-moving lqd-stack-moving-up lqd-stack-moving-down");
      this.initInview(a, false);
    }
    getSectionsToMove(s) {
      var o;
      if (s.yMovement === "down") {
        o = this.$sectionElements.map(function(a) {
          if (a < s.destination.index(this.$sectionElements)) {
            return i(this);
          }
        });
      } else {
        o = this.$sectionElements.map(function(a) {
          if (a > s.destination.index(this.$sectionElements)) {
            return i(this);
          }
        });
      }
      return o;
    }
    setURLHash(s) {
      location.hash = s;
    }
    scrollToAnchor() {
      const s = window.location.hash.replace("#", "");
      let o = this.$sectionElements.filter((a, r) => r.getAttribute("data-anchor") === s && !i(r).is(":hidden")).first();
      if (o.is(":hidden") || !o.length) {
        const a = o.prevAll(":not(:hidden)").first();
        const r = o.nextAll(":not(:hidden)").first();
        o = a.length ? a : r;
      }
      if (!o.length) {
        o = this.$sectionElements.filter(":not(:hidden)").first();
      }
      this.scrollPage(o);
    }
    isMoving() {
      var s = new Date().getTime();
      if (s - this.lastAnimation < this.scrollDelay + parseFloat(this.options.scrollingSpeed) * 250) {
        return true;
      }
      return false;
    }
    hashChangeHandler() {
      var s = window.location.hash.replace("#", "").split("/");
      var o = s[0];
      if (o.length) {
        if (o && o !== this.lastScrolledDestiny) {
          const a = this.$sectionElements.filter((r, c) => i(c).attr("data-anchor") === o);
          this.scrollPage(a);
        }
      }
    }
    getTransforms(s) {
      return {
        "-webkit-transform": s,
        transform: s
      };
    }
    transformContainer(s, o) {
      s.css(this.getTransforms(o));
    }
    mouseWheelHandler(s) {
      var o = new Date().getTime();
      const a = s.originalEvent;
      var r = a.wheelDelta || -a.deltaY || -a.detail;
      var c = Math.max(-1, Math.min(1, r));
      var p = typeof a.wheelDeltaX !== "undefined" || typeof a.deltaX !== "undefined";
      var g = Math.abs(a.wheelDeltaX) < Math.abs(a.wheelDelta) || Math.abs(a.deltaX) < Math.abs(a.deltaY) || !p;
      if (this.scrollings.length > 149) {
        this.scrollings.shift();
      }
      this.scrollings.push(Math.abs(r));
      var u = o - this.prevTime;
      this.prevTime = o;
      if (u > 200) {
        this.scrollings = [];
      }
      if (!this.isMoving()) {
        var m = this.getActiveSection().$element;
        var f = this.isScrollable(m);
        var v = this.getAverage(this.scrollings, 10);
        var y = this.getAverage(this.scrollings, 70);
        var w = v >= y;
        if (w && g) {
          if (c < 0) {
            this.scrolling("down", f);
          } else if (c > 0) {
            this.scrolling("up", f);
          }
        }
      }
    }
    getAverage(s, o) {
      var a = 0;
      var r = s.slice(Math.max(s.length - o, 1));
      for (var c = 0;c < r.length; c++) {
        a = a + r[c];
      }
      return Math.ceil(a / o);
    }
    scrolling(s, o) {
      var a;
      var r;
      if (s == "down") {
        a = "bottom";
        r = this.moveSectionDown.bind(this);
      } else {
        a = "top";
        r = this.moveSectionUp.bind(this);
      }
      if (o.length > 0) {
        if (this.isScrolled(a, o)) {
          r();
        } else {
          return true;
        }
      } else {
        r();
      }
    }
    isScrolled(s, o) {
      if (s === "top") {
        return !o.scrollTop();
      } else if (s === "bottom") {
        return o.scrollTop() + 1 + o.innerHeight() >= o[0].scrollHeight;
      }
    }
    isScrollable(s) {
      return s.filter(".pp-scrollable");
    }
    addMouseWheelHandler() {
      $liquidWindow.on("mousewheel wheel", this.mouseWheelHandler.bind(this));
    }
    handleKeys() {
      i(document).keydown((s) => {
        if (!this.isMoving()) {
          switch (s.which) {
            case 38:
            case 33:
              this.moveSectionUp();
              break;
            case 40:
            case 34:
              this.moveSectionDown();
              break;
            case 36:
              this.moveTo(1);
              break;
            case 35:
              this.moveTo(i(".pp-section").length);
              break;
            default:
              return;
          }
        }
      });
    }
    addTouchHandler() {
      if (this.isTouch) {
        this.$element.off("touchstart").on("touchstart", this.touchStartHandler.bind(this));
        this.$element.off("touchmove").on("touchmove", this.touchMoveHandler.bind(this));
      }
    }
    getEventsPage(s) {
      var o = new Array;
      o.y = typeof s.pageY !== "undefined" && (s.pageY || s.pageX) ? s.pageY : s.touches[0].pageY;
      return o;
    }
    isReallyTouch(s) {
      return typeof s.pointerType === "undefined" || s.pointerType != "mouse";
    }
    touchStartHandler(s) {
      var o = s.originalEvent;
      if (this.isReallyTouch(o)) {
        var a = this.getEventsPage(o);
        this.touchStartY = a.y;
      }
    }
    touchMoveHandler(s) {
      var o = s.originalEvent;
      if (this.isReallyTouch(o)) {
        var a = this.getActiveSection().$element;
        var r = this.isScrollable(a);
        if (!r.length) {
          s.preventDefault();
        }
        if (!this.isMoving()) {
          var c = this.getEventsPage(o);
          this.touchEndY = c.y;
          if (Math.abs(this.touchStartY - this.touchEndY) > this.$element.height() / 100 * this.options.touchSensitivity) {
            if (this.touchStartY > this.touchEndY) {
              this.scrolling("down", r);
            } else if (this.touchEndY > this.touchStartY) {
              this.scrolling("up", r);
            }
          }
        }
      }
    }
    buildNavigationMarkup() {
      if (this.$ppNav) {
        this.$ppNav.remove();
      }
      this.$ppNav = i(`
				<div id="pp-nav">
					<div class="pp-nav-inner">
						<span class="pp-nav-current"><span></span></span>
						<ul class="pp-nav-ul reset-ul"></ul>
					</div>
				</div>
			`);
      this.$ppNavCurrent = i(".pp-nav-current", this.$ppNav);
      this.$ppNav.children().append(`<span class="pp-nav-total">${this.$sectionElements.length < 10 ? "0" + this.$sectionElements.length : this.$sectionElements.length}</span>`);
      i("body").append(this.$ppNav);
    }
    addNavigationItem(s) {
      this.$ppNavCurrent.find("> span").append(`<span>${s < 10 ? "0" + (s + 1) : s + 1}</span>`);
      const o = i(`<li data-tooltip="${this.tooltips[s]}">
				<a href="#${this.anchors[s]}">
					<span></span>
					<svg width="29px" height="29px" viewBox="0 0 29 29" stroke="#000" stroke-width="1" fill="none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<path d="M14.5,28 C21.9558441,28 28,21.9558441 28,14.5 C28,7.04415588 21.9558441,1 14.5,1 C7.04415588,1 1,7.04415588 1,14.5 C1,21.9558441 7.04415588,28 14.5,28 Z"></path>
					</svg>
				</a>
				<span class="pp-tooltip">${this.tooltips[s]}</span>
			</li>`);
      if (this.getActiveSection().index === s) {
        o.addClass("active");
      }
      this.$ppNav.find("ul").append(o);
    }
    activateNavDots(s) {
      this.$ppNav.find("li").removeClass("active");
      this.$ppNav.find("li").eq(s).addClass("active");
    }
    updateSections(s) {
      this.$sectionElements = s;
      this.buildNavigationMarkup();
      this.addClassnames();
      this.eachSection();
    }
    sectionAppended(s, o) {
      const a = i(s).get(0);
      if (!o) {
        this.$sectionElements = i([...this.$sectionElements, a]);
      } else {
        this.$sectionElements = i([...this.$sectionElements].splice(o, 0, a));
      }
      this.buildNavigationMarkup();
      this.addClassnames();
      this.eachSection();
    }
    setActiveSection(s) {
      this.$sectionElements.removeClass("active");
      this.$sectionElements.eq(s).addClass("active");
    }
    getSections() {
      return _.getDetails().then((s) => {
        const o = s.filter((r) => !r.isInFooter && !r.isInnerSection && r.el.clientHeight > 0);
        const a = o.map((r) => r.el);
        this.$sectionElements = i(a);
      });
    }
    async buildSections() {
      await this.getSections();
      this.build();
      this.addClassnames();
      this.eachSection();
      this.init();
      this.$element.trigger("stackinit");
    }
    build() {
      this.buildNavigationMarkup();
      if ($liquidMainFooter.length && !window.frameElement) {
        let s;
        if (liquidIsElementor) {
          const o = $liquidContentsWrap.find(" > .elementor > .elementor-section-wrap");
          s = o.length ? o : $liquidContentsWrap.find(" > .elementor");
        } else {
          s = $liquidContentsWrap;
        }
        s.append($liquidMainFooter);
        this.$sectionElements.last().addClass("section-before-footer");
        this.$sectionElements.push($liquidMainFooter[0]);
      }
      $liquidContentsWrap.children("style").appendTo("head");
      $liquidContentsWrap.children("p").insertAfter($liquidSiteWrap);
    }
    addClassnames() {
      const { options: s } = this;
      this.$sectionElements.addClass("pp-section");
      $liquidMainFooter.length && $liquidMainFooter.addClass("vc_row pp-auto-height");
      s.navigation && $liquidBody.addClass("lqd-stack-has-nav");
      s.prevNextButtons && $liquidBody.addClass("lqd-stack-has-prevnext-buttons");
      s.pageNumber && $liquidBody.addClass("lqd-stack-has-page-numbers");
      s.effect !== "none" && $liquidBody.addClass("lqd-stack-effect-enabled");
      $liquidBody.addClass(`lqd-stack-effect-${s.effect}`);
      $liquidHtml.add("html").addClass("html-pp-enabled overflow-hidden");
      $liquidBody.addClass("pp-enabled");
    }
    onSectionAnimationEnd(s, o) {
      if (s.target === o) {
        o.classList.remove("lqd-stack-row-entering", "lqd-stack-row-leaving", "lqd-stack-row-moving-up", "lqd-stack-row-moving-down");
      }
    }
    eachSection() {
      i.each(this.$sectionElements, (s, o) => {
        o.removeEventListener("animationend", (a) => this.onSectionAnimationEnd(a, o));
        this.makeScrollable(o);
        this.setAnchors(s, o);
        this.setTooltips(s, o);
        o.classList.contains("main-footer") || this.addNavigationItem(s);
        o.addEventListener("animationend", (a) => this.onSectionAnimationEnd(a, o));
      });
    }
    setAnchors(s, o) {
      let a = "";
      let r = false;
      if (o.hasAttribute("id")) {
        a = o.getAttribute("id");
      } else if (o.hasAttribute("data-tooltip")) {
        a = o.getAttribute("data-tooltip").replaceAll(" ", "-").toLowerCase();
      } else {
        if (!o.hasAttribute("data-anchor") || o.hasAttribute("data-dynamic-anchor")) {
          a = `${this.options.defaultTooltip}-${s + 1}`;
        } else {
          r = true;
          a = o.getAttribute("data-anchor");
        }
      }
      this.anchors[s] = a;
      if (!r) {
        o.setAttribute("data-anchor", a);
        o.setAttribute("data-dynamic-anchor", "true");
      }
    }
    setTooltips(s, o) {
      if (!o.hasAttribute("data-tooltip")) {
        this.tooltips[s] = `${this.options.defaultTooltip} ${s + 1}`;
      } else {
        this.tooltips[s] = o.getAttribute("data-tooltip");
      }
    }
    init() {
      window.scrollTo(0, 0);
      if (!this.lastScrolledDestiny) {
        this.setActiveSection(0);
        this.activateNavDots(0);
        this.addLuminosityClassnames(0);
      }
      this.scrollToAnchor();
      this.afterRender();
      i(window).off("hashchange", this.hashChangeHandler).on("hashchange", this.hashChangeHandler);
      i(window).off("resize", this.onWindowResize).on("resize", this.onWindowResize);
    }
    appendPrevNextButtons() {
      const { prevNextLabels: s } = this.options;
      if (this.$prevNextButtons) {
        this.$prevNextButtons.remove();
      }
      this.$prevNextButtons = i('<div class="lqd-stack-prevnext-wrap" />');
      const o = i(`<button class="lqd-stack-prevnext-button lqd-stack-prev-button">
				<span class="lqd-stack-button-label">${s.prev}</span>
				<span class="lqd-stack-button-ext">
				</span>
			</button>`);
      const a = i(`<button class="lqd-stack-prevnext-button lqd-stack-next-button">
				<span class="lqd-stack-button-label">${s.next}</span>
				<span class="lqd-stack-button-ext">
				</span>
			</button>`);
      this.$prevNextButtons.append(o.add(a));
      $liquidBody.append(this.$prevNextButtons);
    }
    prevNextButtonsEvents() {
      const s = this.$prevNextButtons.find(".lqd-stack-prev-button");
      const o = this.$prevNextButtons.find(".lqd-stack-next-button");
      s.on("click", this.moveSectionUp.bind(this));
      o.on("click", this.moveSectionDown.bind(this));
    }
    appendPageNumber() {
      let s;
      if (this.$pageNumber) {
        this.$pageNumber.remove();
      }
      if (this.$pageNumbersStyle === "style1")
        s = this.appendPageNumbersStyle1();
      if (this.$pageNumbersStyle === "style2")
        s = this.appendPageNumbersStyle2();
      $liquidBody.append(s);
      this.$pageNumber = s;
    }
    appendPageNumbersStyle1() {
      const s = this.$sectionElements.not(".main-footer").length;
      const o = i('<div class="lqd-stack-page-number" />');
      const a = i(`<span class="lqd-stack-page-number-counter">
				<span class="lqd-stack-page-number-current"></span>
				<span class="lqd-stack-page-number-passed"></span>
			</span>`);
      const r = i(`<span class="lqd-stack-page-number-total">${s < 10 ? "0" : ""}${s}</span>`);
      o.append(a);
      o.append(r);
      return o;
    }
    appendPageNumbersStyle2() {
      const s = i('<div class="lqd-stack-page-number" />');
      const o = this.$ppNav.find(".pp-nav-ul").clone(true);
      s.append(o);
      return s;
    }
    setPageNumber(s) {
      $liquidBody.attr("data-lqd-stack-page", s);
      this.$pageNumbersStyle === "style1" && this.setPageNumbersStyle1(s);
      this.$pageNumbersStyle === "style2" && this.setPageNumbersStyle2(s);
    }
    setPageNumbersStyle1(s) {
      const o = this.$pageNumber.find(".lqd-stack-page-number-current");
      const a = this.$pageNumber.find(".lqd-stack-page-number-passed");
      a.html(o.html());
      o.html(`${s < 10 ? "0" : ""}${s}`);
    }
    setPageNumbersStyle2(s) {
      const o = this.$pageNumber.find("li");
      o.removeClass("active");
      o.eq(s - 1).addClass("active");
    }
    addDirectionClassname(s) {
      if (s === "down") {
        $liquidBody.removeClass("lqd-stack-moving-up").addClass("lqd-stack-moving-down");
        this.$sectionElements.removeClass("lqd-stack-moving-up").addClass("lqd-stack-moving-down");
      } else if (s === "up") {
        $liquidBody.removeClass("lqd-stack-moving-down").addClass("lqd-stack-moving-up");
        this.$sectionElements.removeClass("lqd-stack-moving-down").addClass("lqd-stack-moving-up");
      }
    }
    addLuminosityClassnames(s) {
      fastdom.mutate(() => {
        const o = !d ? $liquidBody : $liquidBody.add($liquidMainHeader).add(this.$stickyModules);
        o.removeClass("lqd-active-row-dark lqd-active-row-light").addClass(`lqd-active-row-${this.$sectionElements.eq(s).attr("data-section-luminosity")}`);
      });
    }
    initShortcodes(s, o) {
      !liquidIsMobile() && i("[data-dynamic-shape]", s).liquidDynamicShape();
      i("[data-reveal]", s).liquidReveal();
      i("[data-particles=true]", s).liquidParticles();
      i("[data-liquid-masonry]", s).liquidMasonry();
      if (o) {
        this.initInview(s, true);
      }
      if (liquidIsMobile() && document.body.hasAttribute("data-disable-animations-onmobile")) {
        return i("[data-custom-animations]").addClass("ca-initvalues-applied");
      }
      i("[data-custom-animations]", s).liquidCustomAnimations({
        startImmediate: true
      });
      s.is("[data-custom-animations]") && s.liquidCustomAnimations({
        startImmediate: true
      });
    }
    initBackToTop(s) {
      if (s > 1) {
        e.addClass("is-visible");
      } else {
        e.removeClass("is-visible");
      }
      i("a", e).on("click", (o) => {
        o.preventDefault();
        this.moveTo(1);
      });
    }
    afterRender() {
      if ($liquidMainFooter.length) {
        $liquidBody.addClass("lqd-stack-has-footer");
      }
      const s = this.$sectionElements.get().findIndex((o) => o.classList.contains("active"));
      this.initShortcodes(this.$sectionElements.eq(s), true);
      if (!window.frameElement) {
        this.options.prevNextButtons && this.appendPrevNextButtons();
        this.options.prevNextButtons && this.prevNextButtonsEvents();
        this.options.pageNumber && this.appendPageNumber();
        this.setPageNumber(s + 1);
      }
      $liquidBody.addClass("lqd-stack-initiated");
    }
    onLeave(s, o, a) {
      const r = i(this.$sectionElements[o - 1]);
      const c = i(this.$sectionElements[s - 1]);
      if (!r.hasClass("main-footer") && !c.hasClass("main-footer")) {
        this.$ppNav.css("pointer-events", "none");
        $liquidBody.addClass("lqd-stack-moving");
        this.setPageNumber(o);
        r.removeClass("lqd-stack-row-leaving").addClass("lqd-stack-row-entering");
        c.removeClass("lqd-stack-row-entering").addClass("lqd-stack-row-leaving");
        this.addLuminosityClassnames(o - 1);
        this.$ppNavCurrent.children("span").css({
          transform: `translateY(-${(o - 1) * 100}%)`
        });
      } else if (r.hasClass("main-footer")) {
        c.removeClass("lqd-stack-row-entering lqd-stack-row-leaving");
      }
      if (r.hasClass("main-footer")) {
        $liquidBody.addClass("lqd-stack-footer-active");
        c.css("transform", "none");
      } else {
        $liquidBody.removeClass("lqd-stack-footer-active");
      }
      this.addDirectionClassname(a);
      this.initShortcodes(r, false);
      e.length && this.initBackToTop(o);
      if (this.$mainNavLocalScroll.length) {
        this.handleMainNavLocalScroll();
      }
    }
    initInview(s, o) {
      if ($liquidBody.hasClass("lqd-preloader-activated") && o) {
        document.addEventListener("lqd-preloader-anim-done", () => {
          i("[data-inview]", s).liquidInView();
        });
      } else {
        i("[data-inview]", s).liquidInView();
      }
    }
    handleMainNavLocalScroll() {
      const s = window.location.hash;
      if (s) {
        this.$mainNavLocalScroll.find(`a[href="${s}"]`).parent().addClass("is-active").siblings().removeClass("is-active");
        this.$mainNavLocalScroll.closest(".navbar-fullscreen").collapse("hide");
        this.$mainNavLocalScroll.closest(".navbar-collapse").collapse("hide");
        this.$mainNavLocalScroll.closest(".ld-module-dropdown").collapse("hide");
      }
    }
    onWindowResize() {
      this.rebuild();
    }
    async rebuild() {
      await this.buildSections();
    }
  }
  i.fn[h] = function(s) {
    return this.each(function() {
      const o = { ...i(this).data("stack-options"), ...s };
      if (!i.data(this, "plugin_" + h)) {
        i.data(this, "plugin_" + h, new n(this, o));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (!window.frameElement) {
    i("[data-liquid-stack=true]").liquidStack();
  }
});
(function(i) {
  const l = "liquidStickyStack";
  let d = {
    itemsSelector: ".lqd-sticky-stack-item",
    itemsInnerSelector: ".lqd-sticky-stack-item-inner",
    offset: 30,
    spacer: 30,
    minScale: 0.8
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.$element = i(t);
      this.DOM.$items = this.DOM.$element.find(this.options.itemsSelector);
      this.DOM.$innerItems = this.DOM.$element.find(this.options.itemsInnerSelector);
      this.DOM.$nav = null;
      this.DOM.$navItems = null;
      if (this.DOM.$items.length < 2)
        return;
      imagesLoaded(this.DOM.element, () => {
        this.init();
      });
    }
    createNav() {
      this.DOM.$nav = i('<div class="lqd-sticky-stack-nav h-100 pos-abs pos-tl absolute top-0 left-0" />');
      const t = i('<ul class="reset-ul lqd-css-sticky d-flex flex-column justify-content-center h-vh-100 flex flex-col justify-center" />');
      for (let e = 0;e < this.DOM.$items.length; e++) {
        t.append("<li><span></span></li>");
      }
      t.appendTo(this.DOM.$nav);
      this.DOM.$navItems = t.children();
      this.DOM.$nav.appendTo(this.DOM.$element);
    }
    init() {
      const { spacer: t, minScale: e } = this.options;
      const n = this.getOffset();
      const s = getComputedStyle(document.documentElement).getPropertyValue("--lqd-sticky-header-height") || 0;
      const o = this.DOM.$items.last().outerHeight();
      const a = gsap.utils.distribute({ base: e, amount: 0.2 });
      this.DOM.$innerItems.each((r, c) => {
        const p = a(r, this.DOM.$innerItems.get(r), this.DOM.$innerItems.get());
        const g = gsap.to(c, {
          scale: p
        });
        ScrollTrigger.create({
          animation: g,
          trigger: c,
          start: `top top`,
          scrub: 0.25
        });
      });
      this.DOM.$items.each((r, c) => {
        ScrollTrigger.create({
          trigger: c,
          start: `top-=${n + r * t + parseInt(s)} top`,
          end: `bottom top+=${o + this.DOM.$items.length * t}`,
          endTrigger: this.DOM.element,
          pin: true,
          pinSpacing: false
        });
      });
    }
    getOffset() {
      let { offset: t } = this.options;
      const e = $liquidMainHeader.find(".lqd-head-sec-wrap").not(".lqd-hide-onstuck");
      if (e.length) {
        e.each((n, s) => {
          t += i(s).outerHeight();
        });
      }
      return t;
    }
    initNav() {
      function t() {
        let e = [];
        let n = 20;
        for (let s = 1;s <= n; s++) {
          let o = s / n;
          e.push(o);
        }
        e.push(0);
        return e;
      }
      for (let e = 0;e < this.DOM.$items.length; e++) {
        let n = 0;
        new IntersectionObserver(([s]) => {
          if (s.isIntersecting && n < s.intersectionRatio) {
            this.DOM.$navItems.removeClass("is-active").eq(e).addClass("is-active");
          }
        }, { threshold: t() }).observe(this.DOM.$items.get(e));
      }
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("sticky-stack-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  liquidIsMobile() || i(".lqd-sticky-stack").liquidStickyStack();
});
(function(i) {
  const l = "liquidStretchElement";
  let d = {
    to: "right"
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.isStretched = false;
      this.boundingClientRect = null;
      this.rootBounds = null;
      this.initIO();
    }
    initIO() {
      new IntersectionObserver((t) => {
        t.forEach((e) => {
          if (e.isIntersecting && !this.isStretched) {
            this.boundingClientRect = e.boundingClientRect;
            this.rootBounds = e.rootBounds;
            this.init();
            this.isStretched = true;
          }
        });
      }).observe(this.element, { rootMargin: "3%" });
    }
    init() {
      this.stretch();
      this.$element.addClass("element-is-stretched");
    }
    stretch() {
      if (this.options.to === "right") {
        this.stretchToRight();
      } else {
        this.stretchToLeft();
      }
    }
    stretchToRight() {
      const t = this.rootBounds.width - (this.boundingClientRect.width + this.boundingClientRect.left);
      this.$element.css("marginRight", t * -1);
    }
    stretchToLeft() {
      const t = this.rootBounds.width - this.boundingClientRect.left;
      this.$element.css("marginLeft", t * -1);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("stretch-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-stretch-element=true]").liquidStretchElement();
});
(function(i) {
  const l = "liquidTab";
  let d = {
    deepLink: false,
    trigger: "click",
    translateNav: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.isBS5 = typeof bootstrap !== "undefined";
      this.isRTL = i("html").attr("dir") === "rtl";
      this.element = t;
      this.$element = i(t);
      this.$tabNav = i(".lqd-tabs-nav", this.element);
      this.$navItems = i("> li > a", this.$tabNav);
      this.$activeItem = this.$navItems.filter((n, s) => s.parentElement?.classList?.contains("active") || s.classList.contains("active"));
      this.$tabNavArrows = (".lqd-tabs-nav-arrows", this.element);
      this.$tabNavPrev = i(".lqd-tabs-nav-prev", this.$tabNavArrows);
      this.$tabNavNext = i(".lqd-tabs-nav-next", this.$tabNavArrows);
      this.canChange = true;
      if (this.$activeItem.length && this.options.translateNav) {
        this.activeItemWidth = this.$activeItem.outerWidth();
        this.activeItemHeight = this.$activeItem.outerHeight();
        this.activeItemPosLeft = !this.isRTL ? this.$activeItem.position().left : this.$tabNav.width() - (this.$activeItem.position().left + this.activeItemWidth) * -1;
      }
      if (this.element.parentElement.closest(".lqd-mobile-sec")) {
        this.changeIds();
      }
      this.init();
    }
    changeIds() {
      this.$navItems.each((t, e) => {
        const n = e.getAttribute("href");
        e.setAttribute("href", `${n}-mobile`);
        e.setAttribute("aria-controls", `${e.getAttribute("aria-controls")}-mobile`);
        e.setAttribute("data-bs-target", `${e.getAttribute("data-bs-target")}-mobile`);
        this.element.querySelector(n).setAttribute("id", `${n.replace("#", "")}-mobile`);
      });
    }
    init() {
      const { deepLink: t, translateNav: e } = this.options;
      if (!this.isBS5) {
        this.$navItems.tab();
      } else {
        this.$navItems.each((n, s) => new bootstrap.Tab(s));
      }
      this.eventHandlers();
      t && this.setHash();
      e && this.translateNav(false);
    }
    setHash() {
      const t = this.$tabNav.find(`a[href="${location.hash}"]`);
      if (location.hash === "" || !t.length)
        return;
      if (!this.isBS5) {
        t.tab("show");
      } else {
        bootstrap.Tab.getInstance(t[0]).show();
      }
      if (window.scrollY === 0) {
        i("html,body").stop().animate({
          scrollTop: this.$tabNav.offset().top - 90
        }, 600);
      }
    }
    eventHandlers() {
      if (this.options.trigger === "hover" && !liquidIsMobile()) {
        this.$navItems.on("mouseenter.lqdTabs", (t) => {
          const e = t.currentTarget;
          if (!this.isBS5) {
            i(e).tab("show");
          } else {
            bootstrap.Tab.getInstance(e).show();
          }
        });
      }
      if (!this.isBS5) {
        this.$element.on("hide.bs.tab", this.onHide.bind(this));
        this.$element.on("show.bs.tab", this.onShow.bind(this));
        this.$element.on("shown.bs.tab", this.onShown.bind(this));
      } else {
        this.$navItems.each((t, e) => {
          e.addEventListener("hide.bs.tab", this.onHide.bind(this));
          e.addEventListener("show.bs.tab", this.onShow.bind(this));
          e.addEventListener("shown.bs.tab", this.onShown.bind(this));
        });
      }
      this.$tabNavPrev.on("click.lqdTabs", this.showPrev.bind(this));
      this.$tabNavNext.on("click.lqdTabs", this.showNext.bind(this));
    }
    onHide(t) {
      const e = t.target;
      const n = e.getAttribute("data-bs-target");
      if (n) {
        const s = document.querySelector(n);
        if (s) {
          s.classList.remove("active", "show", "in");
        }
      }
      e.parentElement.classList.remove("active");
      this.canChange = false;
    }
    onShow(t) {
      const e = t.target;
      const { deepLink: n, translateNav: s } = this.options;
      const o = e.getAttribute("data-bs-target");
      this.$activeItem = i(e);
      if (n) {
        const a = i(t.target).attr("href");
        location.hash = a;
        const r = i(document).scrollTop();
        i(document).scrollTop(r);
      }
      this.$activeItem.parent().addClass("active").siblings().removeClass("active");
      if (o) {
        const a = document.querySelector(o);
        if (a) {
          i(a).siblings().removeClass("active");
        }
      }
      if (t.relatedTarget) {
        const a = i(t.relatedTarget);
        const r = i(a.attr("href"));
        r.removeClass("active in");
      }
      if (s) {
        this.activeItemWidth = this.$activeItem.outerWidth();
        this.activeItemHeight = this.$activeItem.outerHeight();
        this.activeItemPosLeft = !this.isRTL ? this.$activeItem.position().left : (this.$tabNav.width() - (this.$activeItem.position().left + this.activeItemWidth)) * -1;
        this.translateNav(true);
      }
    }
    onShown(t) {
      this.canChange = true;
      const e = i(t.target);
      const n = i(e.attr("href"));
      const s = liquidIsMobile() ? i(".lqd-tabs-content", this.$element).offset().top : this.$element.offset().top;
      const o = i("[data-sticky-header].is-stuck")?.outerHeight() || 0;
      const a = t.target;
      const r = a.getAttribute("data-bs-target");
      if (r) {
        const c = document.querySelector(r);
        if (c) {
          c.classList.add("in");
        }
      }
      if (this.options.trigger !== "hover" && (!liquidIsMobile() && s <= $liquidWindow.scrollTop() - 15 || liquidIsMobile() && (s > $liquidWindow.scrollTop() + window.innerHeight || $liquidWindow.scrollTop() > s))) {
        i("html, body").stop().animate({
          scrollTop: s - o - 45
        }, 800);
      }
      this.initPlugins(n);
    }
    translateNav(t) {
      this.element.style.setProperty("--lqd-tabs-nav-active-width", `${this.activeItemWidth}px`);
      this.element.style.setProperty("--lqd-tabs-nav-active-height", `${this.activeItemHeight}px`);
      if (t) {
        this.element.style.setProperty("--lqd-tabs-nav-translate", `${this.activeItemPosLeft}px`);
      }
    }
    showPrev() {
      if (!this.canChange) {
        return;
      }
      this.$activeItem.parent().prev().children("a").tab("show");
    }
    showNext() {
      if (!this.canChange) {
        return;
      }
      this.$activeItem.parent().next().children("a").tab("show");
    }
    initPlugins(t) {
      const e = i(".vc_pie_chart:not(.vc_ready)", t);
      const n = i(".vc_round-chart", t);
      const s = i(".vc_line-chart", t);
      const o = i(".elementor-gallery__container", t);
      if (e.length && i.fn.vcChat)
        e.vcChart();
      if (n.length && i.fn.vcRoundChart)
        n.vcRoundChart({ reload: !1 });
      if (s.length && i.fn.vcLineChart)
        s.vcLineChart({ reload: !1 });
      i("[data-hover3d=true]", t).liquidHover3d();
      i("[data-split-text]", t).liquidSplitText();
      i("[data-slideelement-onhover]", t).liquidSlideElement();
      i(".cd-image-container", t).liquidImageComparison();
      i(".lqd-carousel-stack", t).liquidCarouselStack();
      o.trigger("resize");
    }
    destroy() {
      this.$navItems.off("mouseenter.lqdTabs");
      this.$element.off("hide.bs.tab show.bs.tab shown.bs.tab", this.onHide.bind(this));
      this.$tabNavPrev.off("click.lqdTabs");
      this.$tabNavNext.off("click.lqdTabs");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("tabs-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".lqd-tabs").liquidTab();
});
(function(i) {
  const l = "liquidTypewriter";
  let d = {
    repeat: false,
    speed: 75
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = { ...d, ...e };
      this._defaults = d;
      this._name = l;
      this.init();
      this.initIO();
    }
    init() {
      const { repeat: t, speed: e } = this.options;
      this.$element.t({
        repeat: t,
        speed: e
      });
      this.$element.t("pause", true);
    }
    initIO() {
      new IntersectionObserver(([t], e) => {
        if (t.isIntersecting) {
          this.$element.t("pause", false);
        } else {
          this.$element.t("pause", true);
        }
      }).observe(this.element);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-typewriter]").liquidTypewriter();
});
((i) => {
  const l = "liquidVideoBg";
  let d = {
    startVolume: false,
    controls: false,
    loop: true,
    muted: true,
    hideVideoControlsOnLoad: true,
    hideVideoControlsOnPause: true,
    clickToPlayPause: false,
    disableOnMobile: false
  };
  let h = {
    autoPlay: true,
    showControls: false,
    loop: true,
    mute: true,
    showYTLogo: false,
    stopMovieOnBlur: false,
    disableOnMobile: false
  };

  class t {
    constructor(e, n, s) {
      this.element = e;
      this.$element = i(e);
      this.inlineVideoOptions = { ...d, ...n };
      this.youtubeOptions = { ...h, ...s };
      this._name = l;
      this.lqdVBG = null;
      this.lqdYTPlayer = null;
      this.init();
    }
    init() {
      const e = liquidIsMobile();
      if (this.$element.is("video") && this.inlineVideoOptions.disableOnMobile && e) {
        this.$element.closest(".lqd-vbg-wrap").addClass("hidden");
      } else if (this.$element.is("video")) {
        this.$element.removeClass("hidden");
        this.initInlineVideo();
      }
      if (!this.$element.is("video") && this.youtubeOptions.disableOnMobile && e) {
        this.$element.closest(".lqd-vbg-wrap").addClass("hidden");
      } else if (!this.$element.is("video")) {
        this.initYoutubeVideo();
      }
    }
    initInlineVideo() {
      const e = this.$element.closest(".lqd-vbg-wrap");
      const n = e.length ? e.get(0) : this.element;
      this.lqdVBG = this.element;
    }
    initYoutubeVideo() {
      const e = i.extend({}, this.youtubeOptions, {
        containment: this.$element
      });
      this.lqdYTPlayer = this.$element.YTPlayer(e);
      this.lqdYTPlayer.on("YTPReady", () => {
        this.lqdYTPlayer.YTPPause();
        this.initYTIO();
      });
    }
    initInlineVidIO(e) {
      new IntersectionObserver(([n]) => {
        if (n.isIntersecting) {
          this.lqdVBG && this.lqdVBG.play();
        } else {
          this.lqdVBG && this.lqdVBG.pause();
        }
      }).observe(e);
    }
    initYTIO() {
      new IntersectionObserver(([e]) => {
        if (e.isIntersecting) {
          this.lqdYTPlayer && this.lqdYTPlayer.YTPPlay();
        } else {
          this.lqdYTPlayer && this.lqdYTPlayer.YTPPause();
        }
      }).observe(this.element);
    }
  }
  i.fn[l] = function(e, n) {
    return this.each(function() {
      const s = { ...i(this).data("inlinevideo-options"), ...e };
      const o = { ...i(this).data("youtube-options"), ...n };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new t(this, s, o));
      }
    });
  };
})(jQuery, window, document);
jQuery(document).ready((i) => {
  i("[data-video-bg]").liquidVideoBg();
});
(function(i) {
  const l = "liquidVideoTrigger";
  let d = {
    triggerType: ["mouseenter", "mouseleave"],
    videoPlacement: "parent",
    loop: false
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.videoElement = this.$element[this.options.videoPlacement]().find("video").get(0);
      this.init();
    }
    init() {
      this.videoElement.oncanplay = this.events.call(this);
    }
    events() {
      this.$element.on(this.options.triggerType[0], this.triggerOn.bind(this));
      this.$element.on(this.options.triggerType[1], this.triggerOff.bind(this));
    }
    triggerOn() {
      if (this.options.loop) {
        this.videoElement.loop = true;
        this.videoElement.currentTime = 0;
      }
      this.videoElement.play();
    }
    triggerOff() {
      this.videoElement.pause();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("trigger-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-video-trigger]").liquidVideoTrigger();
});
(function(i) {
  const l = "liquidWebGLHover";
  let d = {};

  class h {
    constructor(n = document.body, s = null) {
      this.container = n;
      this.itemsWrapper = s;
      if (!this.container || !this.itemsWrapper)
        return;
      this.build();
    }
    build() {
      new IntersectionObserver(([n], s) => {
        if (n.isIntersecting) {
          this.setup();
          this.initEffectShell().then(() => {
            this.isLoaded = true;
            if (this.isMouseOver)
              this.onMouseOver(this.tempItemIndex);
            this.tempItemIndex = null;
            this.createEventsListeners();
            this.init();
            this.container.classList.add("lqd-webglhover-ready");
          });
          s.unobserve(n.target);
        }
      }).observe(this.container);
    }
    setup() {
      window.addEventListener("resize", this.onWindowResize.bind(this), false);
      this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
      this.renderer.setSize(this.viewport.width, this.viewport.height);
      this.renderer.setPixelRatio = window.devicePixelRatio;
      this.container.appendChild(this.renderer.domElement);
      this.scene = new THREE.Scene;
      this.camera = new THREE.PerspectiveCamera(52.75, this.viewport.aspectRatio, 1, 1000);
      this.camera.position.set(0, 0, 1);
      this.mouse = new THREE.Vector2;
      this.timeSpeed = 2;
      this.time = 0;
      this.clock = new THREE.Clock;
      this.renderer.setAnimationLoop(this.render.bind(this));
    }
    render() {
      this.time += this.clock.getDelta() * this.timeSpeed;
      this.renderer.render(this.scene, this.camera);
    }
    initEffectShell() {
      let n = [];
      this.items = this.itemsElements;
      const s = new THREE.TextureLoader;
      this.items.forEach((o, a) => {
        n.push(this.loadTexture(s, o.img ? o.img.src : null, a));
      });
      return new Promise((o) => {
        Promise.all(n).then((a) => {
          a.forEach((r, c) => {
            this.items[c].texture = r.texture;
          });
          o();
        });
      });
    }
    createEventsListeners() {
      this.items.forEach((n, s) => {
        n.element.addEventListener("mouseover", this._onMouseOver.bind(this, s), false);
      });
      this.container.addEventListener("mousemove", this._onMouseMove.bind(this), false);
      this.itemsWrapper.addEventListener("mouseleave", this._onMouseLeave.bind(this), false);
    }
    _onMouseLeave(n) {
      this.isMouseOver = false;
      this.onMouseLeave(n);
    }
    _onMouseMove(n) {
      this.mouse.x = n.clientX / this.viewport.width * 2 - 1;
      this.mouse.y = -(n.clientY / this.viewport.height) * 2 + 1;
      this.onMouseMove(n);
    }
    _onMouseOver(n, s) {
      this.tempItemIndex = n;
      this.onMouseOver(n, s);
    }
    onWindowResize() {
      this.camera.aspect = this.viewport.aspectRatio;
      this.camera.updateProjectionMatrix();
      this.renderer.setSize(this.viewport.width, this.viewport.height);
    }
    onUpdate() {}
    onMouseEnter(n) {}
    onMouseLeave(n) {}
    onMouseMove(n) {}
    onMouseOver(n, s) {}
    get viewport() {
      let n = this.container.clientWidth;
      let s = this.container.clientHeight;
      let o = n / s;
      return {
        width: n,
        height: s,
        aspectRatio: o
      };
    }
    get viewSize() {
      let n = this.camera.fov / 3 * Math.PI / 180;
      let s = Math.tan(n / 3) * 0.25;
      let o = s * this.viewport.aspectRatio;
      return { width: o, height: s, vFov: n };
    }
    get itemsElements() {
      const n = [...this.itemsWrapper.querySelectorAll("figure")];
      return n.map((s, o) => ({
        element: s,
        img: s.querySelector("img") || null,
        index: o
      }));
    }
    loadTexture(n, s, o) {
      return new Promise((a, r) => {
        if (!s) {
          a({ texture: null, index: o });
          return;
        }
        n.load(s, (c) => {
          a({ texture: c, index: o });
        }, undefined, (c) => {
          console.error("An error happened.", c);
          r(c);
        });
      });
    }
  }

  class t extends h {
    constructor(n = document.body, s = null, o = {}) {
      super(n, s);
      if (!this.container || !this.itemsWrapper)
        return;
      o.strength = o.strength || 0.25;
      this.options = o;
      this.init();
    }
    init() {
      this.position = new THREE.Vector3(0, 0, 0);
      this.scale = new THREE.Vector3(1, 1, 1);
      this.geometry = new THREE.PlaneBufferGeometry(1, 1, 32, 32);
      this.uniforms = {
        uTime: {
          value: 0
        },
        uTexture: {
          value: null
        },
        uOffset: {
          value: new THREE.Vector2(0, 0)
        },
        uAlpha: {
          value: 1
        }
      };
      this.material = new THREE.ShaderMaterial({
        uniforms: this.uniforms,
        vertexShader: `
					uniform vec2 uOffset;

					varying vec2 vUv;

					vec3 deformationCurve(vec3 position, vec2 uv, vec2 offset) {
						float M_PI = 3.1415926535897932384626433832795;
						position.x = position.x + (sin(uv.y * M_PI) * offset.x);
						position.y = position.y + (sin(uv.x * M_PI) * offset.y);
						return position;
					}
					void main() {
						vUv = uv;
						vec3 newPosition = position;
						newPosition = deformationCurve(position,uv,uOffset);
						gl_Position = projectionMatrix * modelViewMatrix * vec4( newPosition, 1.0 );
					}
				`,
        fragmentShader: `
					uniform sampler2D uTexture;
					uniform float uAlpha;
					uniform vec2 uOffset;

					varying vec2 vUv;

					vec3 rgbShift(sampler2D texture, vec2 uv, vec2 offset) {
						float r = texture2D(uTexture,vUv + uOffset).r;
						vec2 gb = texture2D(uTexture,vUv).gb;
						return vec3(r,gb);
					}
					void main() {
						vec3 color = rgbShift(uTexture,vUv,uOffset);
						gl_FragColor = vec4(color,uAlpha);
					}
				`,
        transparent: true
      });
      this.plane = new THREE.Mesh(this.geometry, this.material);
      this.scene && this.scene.add(this.plane);
      this.onTargetChange(0);
      this.draw(0, 0);
    }
    onMouseEnter() {
      if (!this.currentItem || !this.isMouseOver) {
        this.isMouseOver = true;
      }
    }
    onMouseLeave(n) {}
    onMouseMove(n) {
      let s = gsap.utils.mapRange(-1, 1, -this.viewSize.width / 4, this.viewSize.width / 4, this.mouse.x);
      let o = gsap.utils.mapRange(-1, 1, -this.viewSize.height / 4, this.viewSize.height / 4, this.mouse.y);
      this.draw(s, o);
    }
    draw(n, s) {
      this.position = new THREE.Vector3(n, s, 0);
      gsap.to(this.plane.position, {
        x: n,
        y: s,
        duration: 1,
        ease: "power4.out",
        onUpdate: this.onPositionUpdate.bind(this)
      });
    }
    onPositionUpdate() {
      let n = this.plane.position.clone().sub(this.position).multiplyScalar(-this.options.strength);
      this.uniforms.uOffset.value = n;
    }
    onMouseOver(n) {
      if (!this.isLoaded)
        return;
      this.onMouseEnter();
      if (this.currentItem && this.currentItem.index === n)
        return;
      this.onTargetChange(n);
    }
    onTargetChange(n) {
      if (!this.items)
        return;
      this.currentItem = this.items[n];
      if (!this.currentItem.texture)
        return;
      let s = this.currentItem.img.naturalWidth / this.currentItem.img.naturalHeight;
      this.scale = new THREE.Vector3(s, 1, 1);
      const o = this.currentItem.texture;
      o.generateMipmaps = false;
      o.wrapS = o.wrapT = THREE.ClampToEdgeWrapping;
      o.minFilter = THREE.LinearFilter;
      this.uniforms.uTexture.value = o;
      this.plane.scale.copy(this.scale);
    }
  }

  class e {
    constructor(n, s) {
      this.element = n;
      this.$element = i(n);
      this.options = { ...d, ...s };
      this._defaults = d;
      this._name = l;
      this.init();
    }
    init() {
      const n = () => {
        return new Promise((s) => {
          imagesLoaded(this.element.querySelector("img"), s);
        });
      };
      n().then(() => {
        new t(this.element, this.element.querySelector("[data-hoverme]"), { strength: 3 });
      });
    }
  }
  i.fn[l] = function(n) {
    return this.each(function() {
      const s = { ...i(this).data("webglhover-options"), ...n };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new e(this, s));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (liquidIsMobile() || $liquidBody.hasClass("lazyload-enabled"))
    return;
  i("[data-webglhover]").liquidWebGLHover();
});
(function(i) {
  const l = "liquidSlideshow";
  let d = {
    handler: "click",
    menuItems: ".lqd-slsh-alt-menu a, .lqd-vslider-menu a",
    images: ".lqd-slsh-alt-images figure, .lqd-vslider-images figure",
    extras: ".lqd-slsh-alt-ext > ul > li, .lqd-vslider-ext > ul > li"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.$element = i(t);
      this.DOM.$menuItems = i(this.options.menuItems, this.DOM.$element);
      this.DOM.$images = i(this.options.images, this.DOM.$element);
      this.DOM.$extras = i(this.options.extras, this.DOM.$element);
      this.DOM.$loader = i(".lqd-vslider-loader", this.DOM.$element);
      this.isLoading = false;
      this.prevItem = 0;
      this.currentItem = 0;
      this.init();
    }
    init() {
      this.loadImage();
      this.events();
    }
    events() {
      this.DOM.$menuItems.each((t, e) => {
        i(e).on(this.options.handler, (n) => {
          n.preventDefault();
          n.stopPropagation();
          if (this.isLoading)
            return;
          this.prevItem = this.currentItem;
          this.currentItem = t;
          if (this.prevItem === this.currentItem)
            return;
          this.loadImage();
        });
      });
    }
    loadImage() {
      const t = this.DOM.$images.eq(this.currentItem);
      const e = i("img", t);
      if (!e.hasClass("loaded")) {
        e.attr("src", e.attr("data-src"));
      }
      this.isLoading = true;
      this.DOM.$element.addClass("is-loading");
      imagesLoaded(e[0], this.onImageLoaded.bind(this));
    }
    onImageLoaded() {
      const t = this.DOM.$images.eq(this.currentItem);
      const e = i("img", t);
      const n = this.DOM.$extras.eq(this.currentItem);
      const s = this.DOM.$menuItems.eq(this.currentItem).parent();
      this.isLoading = false;
      this.DOM.$element.removeClass("is-loading");
      e.addClass("loaded");
      this.DOM.$images.removeClass("is-active");
      this.DOM.$menuItems.parent().removeClass("is-active");
      this.DOM.$extras.removeClass("is-active");
      t.addClass("is-active");
      n.addClass("is-active");
      s.addClass("is-active");
      this.animateElements();
    }
    animateElements() {
      const t = this.DOM.$extras.eq(this.prevItem);
      const e = this.DOM.$extras.eq(this.currentItem);
      const n = t.children().not("style");
      const s = e.children().not("style");
      gsap.fromTo(t[0], {
        opacity: 1
      }, {
        opacity: 0,
        delay: 0.2
      });
      gsap.fromTo(e[0], {
        opacity: 0
      }, {
        opacity: 1,
        delay: 0.2
      });
      gsap.fromTo(n.get(), {
        xPercent: 0,
        opacity: 1
      }, {
        xPercent: 3,
        opacity: 0,
        stagger: 0.1,
        delay: 0.3
      });
      gsap.fromTo(s.get(), {
        xPercent: -3,
        opacity: 0
      }, {
        xPercent: 0,
        opacity: 1,
        stagger: 0.1,
        delay: 0.3
      });
    }
    destroy() {
      this.DOM.$menuItems.each((t, e) => {
        i(e).off();
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("slideshow-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-slideshow]").liquidSlideshow();
});
(function(i) {
  const l = "liquidWoo";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.$element = i(t);
      this.DOM.$headerCart = i(".ld-module-cart");
      this.DOM.$snickersBarTemp = i("#lqd-temp-snickersbar");
      this.DOM.$snickersBar = null;
      this.snickersBarsHeight = 20;
      this.cartItems = [];
      this.init();
    }
    init() {
      this.events();
    }
    events() {
      i(document).on("adding_to_cart", (t, e, n) => {
        this.onAddingToCart.call(this, e, n);
      });
      i(document).on("added_to_cart", (t, e, n, s) => {
        this.onAddedToCart.call(this, e, s);
      });
      i(document).on("removed_from_cart", (t, e) => {
        this.onRemovedFromCart.call(this, e);
      });
      i(document).on("updated_wc_div", this.onUpdatedWcDiv.bind(this));
      i(".widget_price_filter form").on("submit", this.onWcanAjaxLoading.bind(this));
      i(document).on("yith-wcan-ajax-loading", this.onWcanAjaxLoading.bind(this));
      i(document).on("yith-wcan-ajax-filtered ajaxComplete", this.onWCanAjaxFiltered.bind(this));
      i(document).on("qv_loader_stop", () => {
        this.formIntputsInit.call(this);
      });
    }
    onAddingToCart(t, e) {
      this.initSnickersBar(t, e);
    }
    initSnickersBar(t, e) {
      const n = this.cartItems.find((s) => s.id === e.product_id || s.id === t.attr("data-product_id") || s.id === t.attr("value"));
      if (n) {
        const { $snickersBarEl: s, snickersBarHeight: o, isVisible: a } = n;
        s.removeClass("lqd-snickersbar-action-done");
        if (!a) {
          s.addClass("lqd-snickersbar-in").removeClass("lqd-snickersbar-out");
          n.isVisible = true;
          this.snickersBarsHeight += o;
        }
        this.hideSnickersBar(n);
      } else {
        this.createSnickersBar(t, e);
      }
      this.upadteSnickersBarsPos();
    }
    createSnickersBar(t, e) {
      let { product_id: n, product_name: s } = e;
      const o = this.DOM.$snickersBarTemp[0].content.cloneNode(true);
      const a = i(".lqd-snickersbar", o);
      const r = i(".lqd-snickersbar-addding-temp", a);
      const c = i(".lqd-snickersbar-added-temp", a);
      const p = i(".lqd-snickersbar-msg", a);
      const g = i(".lqd-snickersbar-msg-done", a);
      const u = i('<svg width="32" height="29" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 29" style="height: 1.25em; margin-inline-start: .25rem;"><path fill="currentColor" d="M25.74 6.23c0.38 0.34 0.42 0.9 0.09 1.28l-12.77 14.58a0.91 0.91 0 0 1-1.33 0.04l-5.46-5.46a0.91 0.91 0 1 1 1.29-1.29l4.77 4.78 12.12-13.85a0.91 0.91 0 0 1 1.29-0.08z"></path></svg>');
      const m = i('<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" style="height: 1em; margin-inline-start: .25rem;"><path fill="currentColor" d="M4.005 16.03c0-5.945 4.344-10.842 10.027-11.802v1.784l4.004-3.006L14.032 0v2.162C7.244 3.142 2.007 8.98 2.007 16.03c0 5.072 2.715 9.503 6.75 11.976l1.745-1.31c-3.85-2.007-6.487-6.03-6.497-10.666zm26.056 0c0-5.072-2.716-9.504-6.75-11.967l-1.745 1.31c3.85 2.006 6.487 6.03 6.487 10.656 0 5.944-4.344 10.86-10.017 11.82v-1.793l-4.004 3.006 4.004 3.006v-2.172c6.788-.98 12.025-6.817 12.025-13.867z"></path></svg>');
      if ($liquidBody.hasClass("single")) {
        if (!s) {
          s = i("h1[itemprop=name].entry-title").text();
        }
        if (!n) {
          n = t.attr("data-product_id") || t.attr("value");
        }
      }
      p.text(r.text().replace(/\{\{itemName\}\}/, s));
      g.text(c.text().replace(/\{\{itemName\}\}/, s));
      a.attr("data-item-id", n);
      p.append(m);
      g.append(u);
      $liquidBody.append(a);
      const f = {
        id: n,
        name: s,
        $snickersBarEl: a,
        $msgEl: p,
        $msgDoneEl: g,
        snickersBarHeight: a.outerHeight(true),
        isVisible: true,
        timeout: null
      };
      this.snickersBarsHeight += f.snickersBarHeight;
      this.cartItems.push(f);
    }
    onAddedToCart(t, e) {
      this.updateCartAmount(t);
      const n = this.cartItems.find((s) => s.id === e.attr("data-product_id") || s.id === e.attr("value"));
      if (n) {
        const { $snickersBarEl: s } = n;
        const o = i(".lqd-snickersbar-ext", s);
        !o.children(".added_to_cart").length && o.append(e.siblings(".added_to_cart").clone(true));
        s.addClass("lqd-snickersbar-action-done");
        this.hideSnickersBar(n);
      }
    }
    hideSnickersBar(t) {
      const { $snickersBarEl: e, snickersBarHeight: n, isVisible: s } = t;
      const o = liquidIsElementor ? getComputedStyle(document.body).getPropertyValue("--lqd-snickersbar-stay-time") : getComputedStyle(document.documentElement).getPropertyValue("--lqd-snickersbar-stay-time");
      if (t.timeout) {
        clearTimeout(t.timeout);
        t.timeout = null;
        e.addClass("lqd-snickersbar-in").removeClass("lqd-snickersbar-out");
        t.isVisible = true;
      } else {
        t.timeout = setTimeout(() => {
          if (s) {
            e.addClass("lqd-snickersbar-out").removeClass("lqd-snickersbar-in");
            t.isVisible = false;
            this.snickersBarsHeight -= n;
          }
          this.upadteSnickersBarsPos();
          clearTimeout(t.timeout);
        }, parseFloat(o) * 1000);
      }
    }
    upadteSnickersBarsPos() {
      let t = 0;
      this.cartItems.forEach((e) => {
        if (e.isVisible)
          t += e.snickersBarHeight;
        e.$snickersBarEl.css("transform", `translateY(${(this.snickersBarsHeight - t) * -1}px)`);
      });
    }
    onRemovedFromCart(t) {
      this.updateCartAmount(t);
      this.lazyLoadUpdate();
    }
    onUpdatedWcDiv() {
      this.formIntputsInit();
    }
    updateCartAmount(t) {
      if (!t)
        return;
      const e = this.DOM.$headerCart.find(".ld-module-trigger-txt .woocommerce-Price-amount");
      const n = i(t["span.header-cart-amount"]);
      if (e.length && n) {
        e.text(n.text());
      }
    }
    lazyLoadUpdate() {
      if (window.liquidLazyload) {
        window.liquidLazyload.update();
      }
    }
    formIntputsInit() {
      i("form").liquidFormInputs();
    }
    onWCanAjaxFiltered() {
      this.lazyLoadUpdate();
      i(".widget").removeClass("wcan-ajax-loading");
    }
    onWcanAjaxLoading() {
      i(".widget").addClass("wcan-ajax-loading");
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("woo-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  if (i(".woocommerce").length) {
    $liquidBody.liquidWoo();
  }
});
(function(i) {
  const l = "liquidZIndex";
  let d = {
    triggers: ["mouseenter", "mouseleave"],
    setTo: "self",
    init: 10,
    to: 15,
    duration: 0.6
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.init();
    }
    init() {
      this;
      this.initEvents();
    }
    initEvents() {
      const { triggers: t } = this.options;
      this.$element.on(t[0], this.addZIndex.bind(this));
      this.$element.on(t[1], this.removeZIndex.bind(this));
    }
    addZIndex() {
      window.currentZIndex = window.currentZIndex || 10;
      window.currentZIndex += 1;
      gsap.killTweensOf(this.element);
      gsap.set(this.element, {
        zIndex: window.currentZIndex
      });
    }
    removeZIndex() {
      const { duration: t } = this.options;
      gsap.to(this.element, {
        zIndex: this.options.init,
        delay: t,
        ease: "quint.out",
        duration: 0.15
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("zindex-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-zindex]").liquidZIndex();
});
((i) => {
  const l = "liquidSectionScroll";
  let d = {
    scroller: ".lqd-section-scroll-sections",
    itemsSelector: ":scope > .elementor-container > .elementor-column > .elementor-widget-wrap > .elementor-section, :scope > .e-container, :scope > .e-con"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.$element = i(t);
      this.DOM.scroller = this.DOM.element.querySelector(`:scope > ${this.options.scroller}`);
      this.DOM.items = [...this.DOM.element.querySelectorAll(this.options.itemsSelector)];
      if (!this.DOM.items || this.DOM.items.length <= 1)
        return;
      this.DOM.dots = [];
      this.isAnimating = false;
      this.activeItem = 0;
      this.totalItemsHeight = 0;
      this.elementRect = {};
      this.itemsRects = [];
      this.wheelFree = true;
      this.isScrollingDown = false;
      this.isScrollingUp = false;
      this.onScrollerTransitionEnd = this.onScrollerTransitionEnd.bind(this);
      this.onElementMouseOver = this.onElementMouseOver.bind(this);
      this.onElementMouseOut = this.onElementMouseOut.bind(this);
      this.onElementMouseWheel = this.onElementMouseWheel.bind(this);
      this.scrollSmoothEnabled = "liquidLenis" in window;
      $liquidWindow.on("resize", liquidDebounce(this.onWindowResize.bind(this), 250));
      if (window.innerWidth <= 1024)
        return;
      this.build().then(() => {
        this.appendDots();
        this.events();
      });
    }
    async build() {
      await this.createScroller();
      const t = await Promise.all([this.getElementRect(), ...this.getItemsRects()]);
      const e = window.scrollY;
      const n = t[0];
      const s = t.filter((o, a) => a > 0);
      this.elementRect = {
        width: n.width,
        height: n.height,
        y: n.y + e,
        bottom: n.bottom
      };
      s.forEach((o, a) => {
        this.itemsRects[a] = {
          width: o.width,
          height: o.height,
          y: o.y - this.elementRect.y,
          bottom: o.bottom - this.elementRect.y
        };
        this.totalItemsHeight += o.height;
      });
      this.DOM.element.classList.add("lqd-section-scroll-activated");
    }
    async createScroller() {
      if (this.DOM.scroller)
        return;
      await fastdomPromised.mutate(() => {
        i(this.DOM.items).wrapAll(`<div class="${this.options.scroller.substring(1)} lqd-overlay" />`);
        this.DOM.scroller = this.DOM.element.querySelector(this.options.scroller);
      });
    }
    getElementRect() {
      return new Promise((t) => {
        new IntersectionObserver(([e], n) => {
          n.disconnect();
          t(e.boundingClientRect);
        }).observe(this.DOM.element);
      });
    }
    getItemsRects() {
      const t = [];
      this.DOM.items.forEach((e) => {
        const n = new Promise((s) => {
          new IntersectionObserver(([o], a) => {
            a.disconnect();
            s(o.boundingClientRect);
          }).observe(e);
        });
        t.push(n);
      });
      return t;
    }
    events() {
      this.DOM.$element.on("wheel mousewheel", this.onElementMouseWheel);
      this.DOM.scroller.addEventListener("transitionend", this.onScrollerTransitionEnd);
      this.DOM.element.addEventListener("mouseover", this.onElementMouseOver);
      this.DOM.element.addEventListener("mouseout", this.onElementMouseOut);
      this.DOM.dots.forEach((t, e) => {
        t.on("click", this.onDotsClick.bind(this, e));
      });
    }
    onElementMouseWheel(t) {
      const { deltaY: e } = t.originalEvent;
      if (e < 0) {
        this.isScrollingUp = true;
        this.isScrollingDown = false;
      } else if (e > 0) {
        this.isScrollingUp = false;
        this.isScrollingDown = true;
      }
      if (this.activeItem === 0 && this.isScrollingDown || this.activeItem === this.DOM.items.length - 1 && this.isScrollingUp) {
        const n = this.DOM.$element.offset().top - (window.innerHeight - this.DOM.element.offsetHeight) / 2;
        if (this.scrollSmoothEnabled) {
          liquidLenis.scrollTo(n, {
            lock: true
          });
        } else {
          i("html, body").animate({
            scrollTop: n
          }, 350);
        }
      }
      if (!this.isAnimating) {
        if (this.isScrollingUp && this.activeItem > 0) {
          this.wheelFree = false;
          this.navigate("prev");
        } else if (this.isScrollingDown && this.activeItem !== this.DOM.items.length - 1) {
          this.wheelFree = false;
          this.navigate("next");
        }
      }
      if (!this.wheelFree || this.isAnimating) {
        t.preventDefault();
        return false;
      }
    }
    onScrollerTransitionEnd(t) {
      if (t.target === this.DOM.scroller) {
        this.wheelFree = true;
        this.isAnimating = false;
        this.navigateDone();
      }
    }
    onElementMouseOver() {
      if (this.activeItem === 0 && this.isScrollingDown || this.activeItem === this.DOM.items.length - 1 && this.isScrollingUp) {
        this.wheelFree = false;
      }
    }
    onElementMouseOut() {
      this.wheelFree = true;
    }
    appendDots() {
      if (this.DOM.dots.length)
        return;
      const t = i('<div class="lqd-section-scroll-dots d-flex flex-column pos-abs z-index-5" />');
      this.itemsRects.forEach((e, n) => {
        const s = i(`<div class="lqd-section-scroll-dot d-flex align-items-center justify-content-center border-radius-circle pos-rel text-center ${n === 0 ? "is-active" : ""}"><span></span></div>`);
        t.append(s);
        this.DOM.dots.push(s);
      });
      t.appendTo(this.DOM.$element);
    }
    navigate(t) {
      this.isAnimating = true;
      this.DOM.scroller.style.willChange = "transform";
      switch (t) {
        case "prev":
          this.navigatePrev();
          break;
        default:
          this.navigateNext();
          break;
      }
    }
    navigateDone() {
      this.DOM.scroller.style.willChange = "auto";
    }
    navigateNext() {
      if (this.activeItem < this.DOM.items.length - 1) {
        this.activeItem += 1;
      }
      this.moveScroller();
      this.manageDotsActiveState();
    }
    navigatePrev() {
      if (this.activeItem > 0) {
        this.activeItem -= 1;
      }
      this.moveScroller();
      this.manageDotsActiveState();
    }
    moveScroller() {
      this.DOM.scroller.style.transform = `translate3d(0, ${this.activeItem * 100 * -1}%, 0)`;
    }
    manageDotsActiveState() {
      this.DOM.dots.forEach((t) => t.removeClass("is-active"));
      this.DOM.dots[this.activeItem].addClass("is-active");
    }
    onDotsClick(t) {
      this.activeItem = t;
      this.moveScroller();
      this.manageDotsActiveState();
    }
    onWindowResize() {
      if (window.innerWidth <= 1024) {
        this.destroy();
      } else {
        this.build().then(() => {
          this.appendDots();
          this.events();
        });
      }
    }
    destroy() {
      this.DOM.$element.off("wheel mousewheel", this.onElementMouseWheel);
      this.DOM.scroller.removeEventListener("transitionend", this.onScrollerTransitionEnd);
      this.DOM.element.removeEventListener("mouseover", this.onElementMouseOver);
      this.DOM.element.removeEventListener("mouseout", this.onElementMouseOut);
      this.DOM.dots.forEach((t, e) => {
        t.off("click");
      });
      this.DOM.element.classList.remove("lqd-section-scroll-activated");
      this.DOM.items.forEach((t) => {
        this.DOM.scroller.insertAdjacentElement("beforebegin", t);
      });
      this.DOM.scroller.remove();
      this.wheelFree = true;
      this.isAnimating = false;
      this.isScrollingDown = false;
      this.isScrollingUp = false;
      this.DOM.scroller = null;
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("section-scroll-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-section-scroll=true]").not(".elementor-inner-section").liquidSectionScroll();
});
((i) => {
  const l = $liquidContents.attr("data-liquid-stack") != null;
  const d = $liquidBody.attr("data-localscroll-offset");
  const h = d != null && d !== "" ? parseInt(d, 10) : 0;
  const t = "liquidLocalScroll";
  let e = {
    itemsSelector: "self",
    scrollSpeed: 600,
    scrollBelowSection: false,
    offsetElements: "#wpadminbar, .main-header[data-sticky-header] .lqd-head-sec-wrap:not(.lqd-hide-onstuck), body.elementor-page .main-header[data-sticky-header] > .elementor > .elementor-section-wrap > .elementor-section:not(.lqd-hide-onstuck):not(.lqd-stickybar-wrap), body.elementor-page .main-header[data-sticky-header] > .elementor > .elementor-section:not(.lqd-hide-onstuck):not(.lqd-stickybar-wrap), .lqd-custom-menu[data-pin]:not(.lqd-sticky-menu-floating)",
    includeParentAsOffset: false,
    trackWindowScroll: false,
    offset: 0
  };

  class n {
    constructor(s, o) {
      this._defaults = e;
      this._name = t;
      this.options = { ...e, ...o };
      this.element = s;
      this.$element = i(s);
      this.$items = this.options.itemsSelector === "self" ? this.$element : i(this.options.itemsSelector, this.$element);
      this.targetsArray = [];
      this.offset = [];
      this.targetsRects = [];
      this.windowHeight = window.innerHeight;
      this.scrollTimer = null;
      this.resizeTimer = null;
      this.lastScrollFireTime = 0;
      this.lastItemPercent = null;
      this.CurrentPositionTop = null;
      if (this.options.includeParentAsOffset && this.element.parentElement.classList.contains("lqd-sticky-menu-floating")) {
        this.options.includeParentAsOffset = false;
      }
      if (!l) {
        $liquidContents.imagesLoaded(async () => {
          this.$items.each((a, r) => {
            this.getTargetsArray(a, r);
          });
          if (this.options.trackWindowScroll) {
            this.offset = await this.getOffsets();
          }
          this.targetsRects = await this.getTargetsRects();
          this.init();
        });
      } else {
        this.init();
      }
    }
    async getTargetsRects() {
      const s = [];
      this.targetsArray.forEach((a) => {
        const r = new Promise((c) => {
          if (a == null) {
            return c(null);
          }
          new IntersectionObserver(([p], g) => {
            const { boundingClientRect: u } = p;
            const { scrollY: m } = window;
            const f = {
              y: u.y + m,
              bottom: u.bottom + m,
              height: u.height,
              el: p.target
            };
            c(f);
            g.disconnect();
          }).observe(a);
        });
        s.push(r);
      });
      const o = await Promise.all(s);
      return [...o];
    }
    async getOffsets() {
      const { offsetElements: s, includeParentAsOffset: o, offset: a } = this.options;
      let r = h;
      if (!s) {
        return r + a;
      }
      const c = !o ? i(s) : i(s).add(this.$element.parent());
      const p = [];
      c.each((u, m) => {
        const f = new Promise((v) => {
          new IntersectionObserver(([y], w) => {
            w.disconnect();
            v(y.boundingClientRect.height);
          }).observe(m);
        });
        p.push(f);
      });
      const g = await Promise.all(p);
      g.forEach((u) => r += u);
      if (a) {
        r += a;
      }
      return r;
    }
    getTargetsArray(s, o) {
      if (this.options.scrollBelowSection) {
        const a = liquidIsElementor ? this.$element.parents(".elementor-section, .e-container, .e-con").last() : this.$element.parents(".vc_row, .vc_section").last();
        const r = liquidIsElementor ? a.nextAll(".elementor-section, .e-container, .e-con").first() : a.nextAll(".vc_row, .vc_section").first();
        return this.targetsArray[s] = r[0] || null;
      }
      return this.targetsArray[s] = this.getHashTarget(o);
    }
    getHashTarget(s) {
      const o = typeof s.hash === "string" ? s.hash : "";
      if (o.length < 2)
        return null;
      let a = o.slice(1);
      try {
        a = decodeURIComponent(a);
      } catch (r) {}
      return document.getElementById(a) || null;
    }
    init() {
      this.$items.each((s, o) => {
        const a = i(o);
        const r = a.parent("li");
        const c = a.attr("href");
        const p = window.location.origin + window.location.pathname;
        const g = c && (/^(\/#|#)/.test(c) || c.startsWith(p + "#") || c.startsWith(p + "/#"));
        if (g) {
          r.removeClass("current-menu-item current_page_item");
        }
        this.events(s, o);
      });
      if (this.options.trackWindowScroll && !l) {
        this.onScroll();
        $liquidWindow.on("scroll", this.onScroll.bind(this));
        $liquidWindow.on("resize", this.onResize.bind(this));
      }
    }
    events(s, o) {
      i(o).on("click", { itemIndex: s }, this.onClick.bind(this));
      i(document).on("lqd-masonry-layout-init", async () => {
        this.targetsRects = await this.getTargetsRects();
      });
    }
    onClick(s) {
      if (!l) {
        const { offset: o, scrollBelowSection: a, scrollSpeed: r } = this.options;
        let c = 0;
        if (a) {
          let p = liquidIsElementor ? this.$element.parents(".elementor-section, .e-container, .e-con").last() : this.$element.parents(".vc_row, .vc_section").last();
          let g = liquidIsElementor ? p.nextAll(".elementor-section, .e-container, .e-con").first() : p.nextAll(".vc_row, .vc_section").first();
          if (g.length) {
            c = g.offset().top;
          } else {
            p = liquidIsElementor ? this.$element.closest(".elementor-section, .e-container, .e-con") : this.$element.closest(".vc_row, .vc_section");
            c = p.offset().top + p.outerHeight();
          }
        } else {
          const p = this.getHashTarget(s.currentTarget);
          if (!p)
            return;
          const g = i(p);
          let u = g.offset().top;
          if (g.hasClass("lqd-css-sticky")) {
            g.removeClass("lqd-css-sticky");
            u = g.offset().top;
            g.addClass("lqd-css-sticky");
          }
          c = u;
        }
        s.preventDefault();
        s.stopPropagation();
        $liquidHtml.removeClass("overflow-hidden");
        window.document.documentElement.style.scrollBehavior = "auto";
        i("html, body").animate({
          scrollTop: c - h - o
        }, parseInt(r, 10), function() {
          window.document.documentElement.style.scrollBehavior = "";
        });
      }
      this.$element.closest(".navbar-fullscreen").collapse("hide");
      this.$element.closest(".navbar-collapse").collapse("hide");
      this.$element.closest(".ld-module-dropdown").collapse("hide");
      this.$element.closest(".mobile-navbar-collapse").collapse("hide");
      this.$element.closest(".lqd-fancy-menu").find(".lqd-custom-menu-dropdown-btn").siblings("ul").collapse("hide");
    }
    onScroll() {
      let s = 200;
      let o = new Date().getTime();
      const a = () => {
        let r = this.getScrollPos();
        let c = [];
        let p;
        this.targetsRects.forEach((u) => {
          if (!u)
            return;
          let m = [u.y, this.getVisibilityPercent(u, r)];
          if (m[1] !== 0) {
            c.push(m);
          }
        });
        if (c.length === 0) {
          if (this.CurrentPositionTop !== null) {
            this.$items.parent().removeClass("is-active");
            this.CurrentPositionTop = null;
            this.lastItemPercent = null;
          }
          return;
        }
        if (c.length === 1) {
          c[1] = [0, 0];
        }
        let g = c.reduce((u, m) => {
          return Math.max(u[1], m[1]);
        });
        if (isNaN(g)) {
          let u = [];
          c.forEach((m) => {
            this.targetsRects.forEach((f) => {
              if (!f)
                return;
              if (f.y === m[0]) {
                let v = [f.y, this.getVisibilityDistanceFromCenter(f, r)];
                if (!u.includes(v)) {
                  u.push(v);
                }
              }
            });
          });
          u.reduce((m, f) => {
            if (typeof m !== "undefined" && typeof f != "undefined") {
              g = Math.min(m[1], f[1]);
            }
          });
          c = u;
        }
        if (g !== this.lastItemPercent) {
          this.lastItemPercent = g;
          c.forEach((u) => {
            if (u[1] === g && u[0] !== this.CurrentPositionTop) {
              this.CurrentPositionTop = u[0];
              this.$items.parent().removeClass("is-active");
              this.targetsRects.forEach((m, f) => {
                if (!m)
                  return;
                if (m.y === u[0]) {
                  p = m.el;
                  this.$items.eq(f).parent().addClass("is-active");
                }
              });
            }
          });
        }
      };
      if (!this.scrollTimer) {
        if (o - this.lastScrollFireTime > 3 * s) {
          a();
          this.lastScrollFireTime = o;
        }
        this.scrollTimer = setTimeout(() => {
          this.scrollTimer = null;
          this.lastScrollFireTime = new Date().getTime();
          a();
        }, s);
      } else {
        a();
      }
    }
    onResize() {
      clearTimeout(this.resizeTimer);
      this.resizeTimer = setTimeout(async () => {
        this.windowHeight = window.innerHeight;
        this.targetsRects = await this.getTargetsRects();
        this.onScroll();
      }, 200);
    }
    checkIsInView(s, o = this.getScrollPos()) {
      return s.y > o[0] && s.y < o[1] || s.bottom > o[0] && s.bottom < o[1] || s.y < o[0] && s.bottom > o[1];
    }
    getScrollPos() {
      let s;
      let o;
      s = window.scrollY || 0;
      o = s + this.windowHeight;
      return [s, o];
    }
    getVisibilityPercent(s, o = this.getScrollPos()) {
      if (!this.checkIsInView(s, o))
        return 0;
      let a = o[0];
      let r = o[1];
      let c = 0;
      if (s.y >= a && s.bottom <= r) {
        c = s.height;
      }
      if (s.y < a && s.bottom <= r) {
        c = s.bottom - a;
      }
      if (s.y >= a && s.bottom > r) {
        c = r - s.y;
      }
      if (s.y < a && s.bottom > r) {
        c = r - a;
      }
      return Math.round(c / this.windowHeight * 100);
    }
    getVisibilityDistanceFromCenter(s, o = this.getScrollPos()) {
      if (!this.checkIsInView(s, o))
        return 0;
      let a = o[0];
      let r = a + this.windowHeight / 2;
      let c = 0;
      if (s.y < r && s.bottom < r) {
        c = r - s.bottom;
      }
      if (s.y >= r && s.bottom > r) {
        c = s.bottom - r;
      }
      if (s.y <= r && s.bottom >= r) {
        c = 0;
      }
      return c;
    }
  }
  i.fn[t] = function(s) {
    return this.each(function() {
      const o = { ...i(this).data("localscroll-options"), ...s };
      if (!i.data(this, "plugin_" + t)) {
        i.data(this, "plugin_" + t, new n(this, o));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(window).on("elementor/frontend/init", () => {
    if (typeof elementorFrontend === "undefined") {
      return;
    }
    elementorFrontend.on("components:init", function() {
      if (elementorFrontend.config.experimentalFeatures.e_css_smooth_scroll) {
        elementorFrontend.utils.anchor_scroll_margin?.setSettings("selectors.targets", ".to-the-hell");
      } else {
        elementorFrontend.utils.anchors?.setSettings("selectors.targets", ".to-the-hell");
      }
    });
  });
  i("[data-localscroll]").liquidLocalScroll();
  i(".lqd-mobile-sec-nav .main-nav").liquidLocalScroll({
    itemsSelector: "> li > a"
  });
});
(function(i) {
  const l = "liquidRowBG";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.bgUrl = this.element.getAttribute("data-row-bg");
      this.hasBgMarkup = this.element.classList.contains("lqd-has-bg-markup");
      this.rowBgInitPromise = new Promise((n) => {
        this.$element.on("lqdrowbginit", n(this));
      });
      this.build();
    }
    async build() {
      if (!this.hasBgMarkup) {
        await this._createElements();
        await this._addBgElement();
      } else {
        fastdomPromised.mutate(() => {
          this.bgWrap = this.element.querySelector(".row-bg-wrap");
          this.rowBg = this.bgWrap.querySelector(".row-bg");
        });
      }
      if (liquidLazyloadEnabled) {
        return new IntersectionObserver(([t], e) => {
          if (t.isIntersecting) {
            e.disconnect();
            this.initLoading();
          }
        }, { rootMargin: "500px" }).observe(this.element);
      }
      this.initLoading();
    }
    async initLoading() {
      await this._addBg();
      if (this.element.hasAttribute("data-parallax") && !liquidIsMobile()) {
        this.element.addEventListener("lqd-parallax-initiated", this._imagesLoaded.bind(this));
      } else {
        this._imagesLoaded();
      }
    }
    _createElements() {
      return fastdomPromised.mutate(() => {
        const t = [
          {
            is: "rowBg",
            classname: "row-bg",
            tag: "figure"
          },
          {
            classname: "row-bg-inner",
            tag: "div",
            append: ".row-bg"
          },
          {
            classname: "row-bg-wrap bg-not-loaded",
            tag: "div",
            append: ".row-bg-inner"
          }
        ];
        const e = new DocumentFragment;
        t.forEach((n) => {
          const s = document.createElement(n.tag);
          s.setAttribute("class", n.classname);
          e.appendChild(s);
          if (n.append) {
            s.appendChild(e.querySelector(n.append));
          }
          if (n.is) {
            this[n.is] = s;
          }
        });
        this.bgWrap = e.querySelector(".row-bg-wrap");
      });
    }
    _addBg() {
      return fastdomPromised.mutate(() => {
        this.rowBg.style.backgroundImage = `url(${this.bgUrl})`;
      });
    }
    _addBgElement() {
      return fastdomPromised.mutate(() => {
        let t = this.$element.children(".row-bg-loader");
        let e = t;
        let n = "insertAfter";
        if (this.$element.children(".lqd-sticky-bg-spacer").length) {
          e = this.$element.children(".lqd-sticky-bg-spacer");
          n = "appendTo";
        }
        if (this.$element.hasClass("vc_column_container")) {
          e = this.$element.find("> .vc_column-inner > .row-bg-loader");
        }
        e.siblings(".row-bg-wrap").remove();
        e.find(".row-bg-wrap").remove();
        i(this.bgWrap)[n](e);
      });
    }
    _imagesLoaded() {
      fastdomPromised.mutate(() => {
        imagesLoaded(this.rowBg, { background: true }, this._onImagesLoaded.bind(this));
      });
    }
    _onImagesLoaded() {
      fastdomPromised.mutate(() => {
        this.element.classList.remove("row-bg-loaded");
        this.bgWrap.classList.remove("bg-not-loaded");
        this.bgWrap.classList.add("bg-loaded");
        this.element.classList.add("row-bg-loaded");
        this._onRowBgInit();
      });
    }
    _onRowBgInit() {
      this.$element.trigger("lqdrowbginit", this.element);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("row-bg-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-row-bg]:not([data-slideshow-bg])").liquidRowBG();
});
(function(i) {
  const l = "liquidSlideshowBG";
  let d = {
    effect: "fade",
    delay: 3000,
    imageArray: []
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.slideshowBgInitPromise = new Promise((n) => {
        this.$element.on("lqdslideshowinit", n.bind(this, this));
      });
      this.init();
    }
    init() {
      const t = this._addMarkup();
      this.imageArray = this.options.imageArray;
      this.slideshowWrap = t.slideshowWrap;
      this.slideshowInner = t.slideshowInner;
      this._addImages();
      this._initSlideShow();
      this._onImagesLoaded();
    }
    _addMarkup() {
      const t = i('<div class="ld-slideshow-bg-wrap" />');
      const e = i('<div class="ld-slideshow-bg-inner" />');
      const n = i('<span class="row-bg-loader" />');
      t.append(e);
      t.append(n);
      this.$element.prepend(t);
      return {
        slideshowWrap: t,
        slideshowInner: e
      };
    }
    _addImages() {
      i.each(this.imageArray, (t, { src: e, alt: n }) => {
        const s = i(`<img class="invisible" src="${e}" alt="${n || "Slideshow image"}"/>`);
        const o = i(`<figure class="ld-slideshow-figure" style="background-image: url(${e})" />`);
        const a = i('<div class="ld-slideshow-item" />');
        const r = i('<div class="ld-slideshow-item-inner" />');
        o.append(s);
        r.append(o);
        a.append(r);
        this.slideshowInner.append(a);
      });
    }
    _initSlideShow() {
      this.slideshowInner.children(":first-child").addClass("active");
      this.slideshowInner.children().not(":first-child").css({
        opacity: 0
      });
    }
    _onImagesLoaded() {
      imagesLoaded(this.slideshowInner.children().first().get(0), () => {
        this.$element.addClass("slideshow-applied");
        this._initSlideshowAnimations();
        this._onSlideshowInit();
      });
    }
    _getCurrentSlide() {
      return this.slideshowInner.children(".active");
    }
    _getAllSlides() {
      return this.slideshowInner.children();
    }
    _setActiveClassnames(t) {
      i(t).addClass("active").siblings().removeClass("active");
    }
    _getNextSlide() {
      return !this._getCurrentSlide().is(":last-child") ? this._getCurrentSlide().next() : this.slideshowInner.children(":first-child");
    }
    _initSlideshowAnimations() {
      this[this.options.effect]();
    }
    _setWillChange(t) {
      const e = this._getAllSlides();
      e.css({
        willChange: t.join(", ")
      });
    }
    fade() {
      imagesLoaded([this._getCurrentSlide(), this._getNextSlide()], () => this._fadeOutCurrentSlide());
    }
    _fadeOutCurrentSlide() {
      gsap.to(this._getCurrentSlide().get(0), {
        startAt: {
          opacity: 1
        },
        opacity: 0,
        duration: 1,
        delay: parseInt(this.options.delay, 10) / 1000,
        ease: "power1.in",
        onStart: () => {
          this._fadeInNextSlide();
        }
      });
    }
    _fadeInNextSlide() {
      const t = this._getNextSlide().get(0);
      gsap.to(t, {
        startAt: {
          opacity: 0
        },
        opacity: 1,
        duration: 1,
        ease: "power1.inOut",
        onComplete: () => {
          this._setActiveClassnames(t);
          this._fadeOutCurrentSlide();
        }
      });
    }
    slide() {
      imagesLoaded([this._getCurrentSlide(), this._getNextSlide()], () => this._slideOutCurrentSlide());
    }
    _slideOutCurrentSlide() {
      const t = this._getCurrentSlide().get(0);
      const e = i(t).children().get(0);
      const n = i(e).children().get(0);
      gsap.timeline({
        delay: parseInt(this.options.delay, 10) / 1000
      }).to(t, {
        startAt: {
          x: "0%"
        },
        x: "-100%",
        duration: 1,
        ease: "power4.inOut"
      }, 0).to(e, {
        startAt: {
          x: "0%"
        },
        x: "100%",
        duration: 1,
        ease: "power4.inOut"
      }, 0).to(n, {
        startAt: {
          scale: 1
        },
        scale: 1.2,
        duration: 1,
        ease: "power3.inOut",
        onStart: () => {
          this._slideInNextSlide();
        }
      }, 0);
    }
    _slideInNextSlide() {
      const t = this._getNextSlide();
      const e = t.get(0);
      const n = t.children().get(0);
      const s = i(n).children().get(0);
      gsap.timeline({
        onComplete: () => {
          this._slideOutCurrentSlide();
        }
      }).to(e, {
        startAt: {
          x: "100%",
          opacity: 1
        },
        x: "0%",
        duration: 0.85,
        ease: "power4.inOut"
      }, 0).to(n, {
        startAt: {
          x: "-100%",
          opacity: 1
        },
        x: "0%",
        duration: 0.85,
        ease: "power4.inOut"
      }, 0).to(s, {
        startAt: {
          scale: 1.2
        },
        scale: 1,
        duration: 1.65,
        ease: "power3.out",
        onStart: () => {
          this._setActiveClassnames(e);
        }
      }, 0);
    }
    scale() {
      imagesLoaded([this._getCurrentSlide(), this._getNextSlide()], () => this._scaleUpCurrentSlide());
    }
    _scaleUpCurrentSlide() {
      gsap.to(this._getCurrentSlide().get(0), {
        startAt: {
          scale: 1,
          opacity: 1,
          zIndex: 0
        },
        scale: 1.2,
        opacity: 0,
        duration: 0.9,
        ease: "power4.inOut",
        delay: parseInt(this.options.delay, 10) / 1000,
        onStart: () => {
          this._scaleDownNextSlide();
        }
      });
    }
    _scaleDownNextSlide() {
      const t = this._getNextSlide().get(0);
      gsap.to(t, {
        startAt: {
          scale: 1.2,
          opacity: 0,
          zIndex: 1
        },
        scale: 1,
        opacity: 1,
        duration: 0.9,
        ease: "power4.inOut",
        onComplete: () => {
          this._setActiveClassnames(t);
          this._scaleUpCurrentSlide();
        }
      });
    }
    _onSlideshowInit() {
      this.$element.trigger("lqdslideshowinit", this.element);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("slideshow-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-slideshow-bg]").liquidSlideshowBG();
});
(function(i) {
  const l = "liquidSplitText";
  let d = {
    type: "words",
    forceApply: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.splittedTextList = {
        lines: [],
        words: [],
        chars: []
      };
      this.splitTextInstance = null;
      this.isRTL = i("html").attr("dir") === "rtl";
      this.element = t;
      this.$element = i(t);
      this.prevWindowWidth = window.innerWidth;
      this._splitGen = 0;
      this._boundResize = liquidDebounce(this._onWindowResize.bind(this), 200);
      this.fontInfo = {};
      this.splitDonePormise = new Promise((n) => {
        this.$element.on("lqdsplittext", n.bind(this, this));
      });
      if (!this.options.forceApply) {
        new IntersectionObserver(([n], s) => {
          if (n.isIntersecting) {
            s.disconnect();
            this.init();
          }
        }, { rootMargin: "20%" }).observe(this.element);
      } else {
        this.init();
      }
    }
    async init() {
      await this._measure();
      if (this._destroyed)
        return;
      await this._onFontsLoad();
      if (this._destroyed)
        return;
      this._windowResize();
    }
    _measure() {
      return fastdomPromised.measure(() => {
        const t = getComputedStyle(this.element);
        this.fontInfo.elementFontFamily = t.fontFamily.replace(/"/g, "").replace(/'/g, "").split(",")[0];
        this.fontInfo.elementFontWeight = t.fontWeight;
        this.fontInfo.elementFontStyle = t.fontStyle;
        this.fontInfo.fontFamilySlug = window.liquidSlugify(this.fontInfo.elementFontFamily);
      });
    }
    _onFontsLoad() {
      const t = this._splitGen;
      return fastdomPromised.measure(() => {
        if (window.liquidCheckedFonts.find((n) => n === this.fontInfo.fontFamilySlug)) {
          return this._doSplit(t);
        }
        const e = new FontFaceObserver(this.fontInfo.elementFontFamily, {
          weight: this.fontInfo.elementFontWeight,
          style: this.fontInfo.elementFontStyle
        });
        return e.load().finally(() => {
          window.liquidCheckedFonts.push(this.fontInfo.fontFamilySlug);
          if (t === this._splitGen && !this._destroyed)
            this._doSplit(t);
        });
      });
    }
    getSplitTypeArray() {
      const { type: t } = this.options;
      const e = t.split(",").map((n) => n.replace(" ", ""));
      if (!this.isRTL) {
        return e;
      } else {
        return e.filter((n) => n !== "chars");
      }
    }
    async _doSplit(t) {
      await this._split(t);
      await this._unitsOp(t);
      await this._onSplittingDone(t);
    }
    _split(t) {
      const e = this.getSplitTypeArray();
      const n = this.element.classList.contains("ld-fh-txt") && this.element.querySelector(".ld-fh-txt-inner") != null;
      const s = n ? this.element.querySelector(".ld-fh-txt-inner") : this.element;
      let o;
      return fastdomPromised.mutate(() => {
        if (t !== this._splitGen || this._destroyed)
          return;
        o = new SplitText(s, {
          type: e,
          charsClass: "split-unit lqd-chars",
          linesClass: "split-unit lqd-lines",
          wordsClass: "split-unit lqd-words"
        });
        e.forEach((a) => {
          o[a].forEach((r) => {
            this.splittedTextList[a].push(r);
          });
        });
        this.element.classList.add("split-text-applied");
        this.splitTextInstance = o;
      });
    }
    _unitsOp(t) {
      return fastdomPromised.mutate(() => {
        if (t !== this._splitGen || this._destroyed)
          return;
        for (const [e, n] of Object.entries(this.splittedTextList)) {
          if (n && n.length > 0) {
            n.forEach((s, o) => {
              s.style.setProperty(`--${e}-index`, o);
              s.style.setProperty(`--${e}-last-index`, n.length - 1 - o);
              i(s).wrapInner(`<span class="split-inner" />`);
            });
          }
        }
      });
    }
    _onSplittingDone(t) {
      return fastdomPromised.mutate(() => {
        if (t !== this._splitGen || this._destroyed)
          return;
        this.element.dispatchEvent(new CustomEvent("lqdsplittext", { detail: { gen: t } }));
      });
    }
    _windowResize() {
      i(window).on("resize.lqdSplitText", this._boundResize);
    }
    _onWindowResize() {
      if (this._destroyed || this.prevWindowWidth === window.innerWidth)
        return;
      this.prevWindowWidth = window.innerWidth;
      this._resplit();
    }
    _resplit() {
      const t = ++this._splitGen;
      this.element.dispatchEvent(new CustomEvent("lqdsplittext-before", { detail: { gen: t } }));
      if (this.splitTextInstance) {
        this.splitTextInstance.revert();
        this.element.classList.remove("split-text-applied");
        this.splitTextInstance = null;
      }
      this.splittedTextList = {
        lines: [],
        words: [],
        chars: []
      };
      this._doSplit(t).then(() => {
        if (t !== this._splitGen || this._destroyed)
          return;
        this.$element.find(".split-unit").addClass("lqd-unit-animation-done");
      });
    }
    destroy() {
      this._destroyed = true;
      const t = ++this._splitGen;
      if (this._boundResize)
        this._boundResize.cancel();
      i(window).off("resize.lqdSplitText", this._boundResize);
      if (this.splitTextInstance) {
        this.element.dispatchEvent(new CustomEvent("lqdsplittext-before", { detail: { gen: t } }));
        this.splitTextInstance.revert();
        this.element.classList.remove("split-text-applied");
        this.splitTextInstance = null;
      }
      this.splittedTextList = { lines: [], words: [], chars: [] };
      i.removeData(this.element, "plugin_" + l);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("split-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  const l = i("[data-split-text]").filter((d, h) => {
    const t = i(h);
    const e = h.hasAttribute("data-custom-animations");
    const n = t.closest("[data-custom-animations]").length;
    const s = t.closest(".accordion-content").length;
    const o = t.closest(".lqd-tabs-pane").length;
    const a = t.closest("[data-lqd-webgl-slideshow]").length;
    return !e && !n && !s && !o && !a;
  });
  l.liquidSplitText();
});
(function(i) {
  const l = "liquidCustomAnimations";
  let d = {
    delay: 160,
    startDelay: 0,
    direction: "forward",
    duration: 1600,
    ease: "power4.out",
    animationTarget: "this",
    addPerspective: true,
    perspectiveVal: 1400,
    initValues: {
      x: 0,
      y: 0,
      z: 0,
      rotationX: 0,
      rotationY: 0,
      rotationZ: 0,
      scaleX: 1,
      scaleY: 1,
      skewX: 0,
      skewY: 0,
      opacity: 1,
      transformOriginX: 50,
      transformOriginY: 50,
      transformOriginZ: "0px"
    },
    animations: { transformOriginX: 50, transformOriginY: 50, transformOriginZ: "0px" },
    randomizeInitValues: false,
    randomizeTargets: false,
    clearProps: "transform,opacity,transform-origin",
    trigger: "this",
    startImmediate: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.options.duration = this.options.duration / 1000;
      this.options.offDuration = this.options.offDuration / 1000;
      this.options.offDelay = this.options.offDelay / 1000;
      this.options.delay = this.options.delay / 1000;
      this.options.startDelay = this.options.startDelay / 1000;
      this.element = t;
      this.$element = i(t);
      this.animationTargets = [];
      this.animationsTimeline = null;
      this.animationsStarted = false;
      this.needPerspective = this.options.addPerspective && this._needPerspective();
      this.animationsInitiatedPromise = new Promise((n) => {
        this.$element.on("lqdanimationsinitiated", n.bind(this, this));
      });
      this.animationsDonePromise = new Promise((n) => {
        this.$element.on("lqdanimationsdone", n.bind(this, this));
      });
      gsap.config({
        nullTargetWarn: false,
        trialWarn: false
      });
      new IntersectionObserver(([n], s) => {
        if (n.isIntersecting) {
          s.disconnect();
          this._build();
        }
      }, { rootMargin: "8%" }).observe(this.element);
    }
    _build() {
      const t = this.$element.closest("[data-row-bg]");
      const e = this.$element.closest("[data-slideshow-bg]");
      const n = [];
      if (!this.element.classList.contains("vc_row")) {
        const s = this.$element.find("[data-split-text]");
        if (this.element.hasAttribute("data-split-text")) {
          s.push(this.element);
        }
        if (s.length) {
          s.each((o, a) => {
            const r = i(a);
            r.liquidSplitText({ forceApply: true });
            const c = r.data("plugin_liquidSplitText");
            c && n.push(c.splitDonePormise);
          });
        }
      }
      if (t.length) {
        const s = t.data("plugin_liquidRowBG");
        s && n.push(s.rowBgInitPromise);
      }
      if (e.length) {
        const s = e.data("plugin_liquidSlideshowBG");
        s && n.push(s.slideshowBgInitPromise);
      }
      if (n.length > 0) {
        Promise.all(n).then(() => {
          this._init();
        });
      } else {
        this._init();
      }
    }
    _init() {
      this._getAnimationTargets();
      this._createTimeline();
      this._initValues();
      this._runAnimations();
      this._initPlugins();
    }
    _getAnimationTargets() {
      const { animationTarget: t } = this.options;
      let e = null;
      switch (t) {
        case "this":
          e = [this.element];
          break;
        case "all-childs":
          e = this._getChildElments();
          break;
        default:
          e = this.element.querySelectorAll(t);
          break;
      }
      this.animationTargets = Array.from(e);
    }
    _getChildElments() {
      let t = this.$element.children();
      return this._getInnerChildElements(t);
    }
    _getInnerChildElements(t) {
      const e = [];
      let n = i(t).map((s, o) => {
        const a = i(o);
        if (a.hasClass("vc_inner") || a.hasClass("vc_vc_row_inner")) {
          return a.find(".wpb_wrapper").children().get();
        } else if (a.hasClass("row")) {
          return a.find(".lqd-column").children().get();
        } else if (a.hasClass("ld-slideelement-visible") || a.hasClass("ld-slideelement-hidden")) {
          return a.children().children().get();
        } else if (a.hasClass("elementor-container")) {
          return a.children(".elementor-column").get();
        } else if (a.hasClass("elementor-widget-wrap")) {
          return a.children(".elementor-element").get();
        } else {
          return a.not("style, .lqd-exclude-parent-ca").get();
        }
      });
      i.each(n, (s, o) => {
        const a = i(o);
        if (o.hasAttribute("data-custom-animations")) {
          return e.push(o);
        }
        if (o.querySelector("[data-custom-animations]")) {
          return o.querySelectorAll("[data-custom-animations]").forEach((r) => {
            e.push(r);
          });
        }
        if (o.tagName === "UL") {
          return i.each(a.children(), (r, c) => {
            e.push(c);
          });
        }
        if (o.classList.contains("lqd-custom-menu")) {
          return i.each(a.find("> ul > li"), (r, c) => {
            e.push(c);
          });
        }
        if (o.classList.contains("accordion")) {
          return i.each(a.children(), (r, c) => {
            e.push(c);
          });
        }
        if (o.classList.contains("vc_inner") || o.classList.contains("vc_vc_row_inner")) {
          return i.each(a.find(".wpb_wrapper"), (r, c) => {
            e.push(c);
          });
        }
        if (o.classList.contains("row")) {
          return i.each(a.find(".lqd-column"), (r, c) => {
            e.push(c);
          });
        }
        if (o.classList.contains("lqd-pb-container")) {
          return i.each(a.find(".lqd-pb"), (r, c) => {
            e.push(c);
          });
        }
        if (a.find("[data-split-text]").length || o.hasAttribute("data-split-text")) {
          if (o.classList.contains("btn") || o.classList.contains("vc_ld_button")) {
            return e.push(a[0]);
          } else {
            return i.each(a.find(".split-inner"), (r, c) => {
              const p = i(c).find(".split-inner");
              if (p.length) {
                e.push(p[0]);
              } else {
                e.push(c);
              }
            });
          }
        }
        if (!o.classList.contains("vc_empty_space") && !o.classList.contains("ld-empty-space") && !o.classList.contains("vc_ld_spacer") && !o.classList.contains("ld-particles-container") && !o.classList.contains("elementor-widget-spacer") && !o.hasAttribute("data-split-text") && o.tagName !== "STYLE") {
          return e.push(a[0]);
        }
      });
      return e;
    }
    _needPerspective() {
      const t = this.options.initValues;
      const e = ["z", "rotationX", "rotationY"];
      let n = false;
      for (let s in t) {
        for (let o = 0;o <= e.length - 1; o++) {
          const a = e[o];
          if (s === a) {
            n = true;
            break;
          }
        }
      }
      return n;
    }
    _generateRandomValues(t) {
      const e = { ...t };
      for (const n in t) {
        if (n.search("transformOrigin") < 0 && n.search("opacity") < 0) {
          e[n] = () => gsap.utils.random(0, t[n]);
        }
      }
      return e;
    }
    _createTimeline() {
      const { ease: t, duration: e, clearProps: n } = this.options;
      this.animationsTimeline = gsap.timeline({
        defaults: {
          duration: e,
          ease: t,
          clearProps: n
        },
        onComplete: this._onTimelineAnimationComplete.bind(this)
      });
    }
    _initValues() {
      const { options: t } = this;
      const { randomizeInitValues: e, initValues: n } = t;
      const s = i(this.animationTargets);
      const o = !e ? n : this._generateRandomValues(n);
      s.css({
        transition: "none",
        transitionDelay: 0
      }).addClass("will-change");
      if (this.needPerspective) {
        s.parent().parent().addClass("perspective");
        s.each((a, r) => {
          const c = i(r);
          if (!c.hasClass("lqd-imggrp-single")) {
            c.parent().addClass("transform-style-3d");
          }
        });
      }
      gsap.set(this.animationTargets, {
        ...o
      });
      this.element.classList.add("ca-initvalues-applied");
      this.$element.trigger("lqdanimationsinitiated", this);
    }
    async _runAnimations() {
      const { delay: t, startDelay: e, animations: n, direction: s, trigger: o } = this.options;
      const a = {
        from: s,
        each: t
      };
      const r = o === "this" ? this.element : this.animationTargets?.at(0);
      if (s === "forward") {
        a["from"] = "start";
      } else if (s === "backward") {
        a["from"] = "end";
      }
      this.animationsTimeline.to(this.animationTargets, {
        ...n,
        stagger: a,
        delay: e,
        onStart: () => {
          this.animationsStarted = true;
        },
        onComplete: this._onUnitsAnimationsComplete,
        onCompleteParams: [this.animationTargets]
      });
      if (this.options.startImmediate) {
        this.animationsTimeline.play(0);
      } else {
        this.ST = ScrollTrigger.create({
          trigger: r || this.element,
          start: "top bottom",
          animation: this.animationsTimeline
        });
      }
    }
    _onTimelineAnimationComplete() {
      if (this.needPerspective) {
        i(this.animationTargets).parent().parent().removeClass("perspective");
        i(this.animationTargets).parent().removeClass("transform-style-3d");
      }
      this.$element.addClass("lqd-animations-done");
      this.$element.trigger("lqdanimationsdone", this);
    }
    _onUnitsAnimationsComplete(t) {
      t.forEach((e) => {
        e.style.transition = "";
        e.style.transitionDelay = "";
        e.classList.remove("will-change");
        if (e.classList.contains("split-inner")) {
          e.parentElement.classList.add("lqd-unit-animation-done");
        } else {
          e.classList.add("lqd-unit-animation-done");
        }
      });
    }
    _initPlugins() {
      this.$element.find("[data-slideelement-onhover]").filter((t, e) => {
        return e.clientHeight > 0;
      }).liquidSlideElement();
      this.element.hasAttribute("data-slideelement-onhover") && this.$element.liquidSlideElement();
    }
    destroy() {
      this.element.classList.remove("ca-initvalues-applied", "lqd-animations-done", "transform-style-3d");
      this.animationTargets.forEach((t) => {
        if (!t.vars) {
          t.classList.remove("will-change");
          if (t.classList.contains("split-inner")) {
            t.parentElement.classList.remove("lqd-unit-animation-done");
          } else {
            t.classList.remove("lqd-unit-animation-done");
          }
          gsap.set(t, { clearProps: "all" });
        } else {
          this.animationsTimeline.killTweensOf(t);
        }
      });
      if (this.animationsTimeline) {
        this.animationsTimeline.kill();
        this.animationsTimeline.clear();
      }
      if (this.ST) {
        this.ST.kill(true);
      }
      i.data(this.element, "plugin_" + l, null);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this);
      const n = `plugin_${l}`;
      const s = { ...e.data("ca-options"), ...t };
      let { initValues: o, animations: a } = s;
      function r(c) {
        if (!c)
          return;
        const { transformOriginX: p, transformOriginY: g, transformOriginZ: u } = c;
        if (p && typeof p === "number") {
          c.transformOriginX = p + "%";
        }
        if (g && typeof g === "number") {
          c.transformOriginY = g + "%";
        }
        if (u && typeof u === "number") {
          c.transformOriginZ = u + "%";
        }
        if (p && g && u) {
          c.transformOrigin = `${c.transformOriginX} ${c.transformOriginY} ${c.transformOriginZ}`;
          delete c.transformOriginX;
          delete c.transformOriginY;
          delete c.transformOriginZ;
        }
        return c;
      }
      o = r(o);
      a = r(a);
      if (!i.data(this, n)) {
        i.data(this, `plugin_${l}`, new h(this, s));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  const l = i("[data-custom-animations]").filter((d, h) => {
    const t = i(h);
    const e = $liquidContents.length && $liquidContents[0].getAttribute("data-stack-options");
    const n = e && JSON.parse(e).disableOnMobile === true;
    return (!e || e && n && (liquidIsMobile() || liquidWindowWidth() <= liquidMobileNavBreakpoint())) && !t.hasClass("carousel-items");
  }).get().reverse();
  if (l.length < 1) {
    return;
  }
  if (liquidIsMobile() && document.body.hasAttribute("data-disable-animations-onmobile")) {
    return i(l).addClass("ca-initvalues-applied");
  }
  if ($liquidBody.hasClass("lqd-preloader-activated") && i(".lqd-preloader-wrap").length) {
    document.addEventListener("lqd-preloader-anim-done", () => {
      i(l).liquidCustomAnimations();
    });
  } else {
    i(l).liquidCustomAnimations();
  }
});
(function(i) {
  const l = "liquidTextRotator";
  let d = {
    delay: 2,
    duration: 0.8,
    easing: "power4.inOut",
    animationType: "slide",
    marquee: false
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      this.activeKeywordIndex = 0;
      this.nextKeywordIndex = 1;
      this.isFirstItterate = true;
      this.basicAnimationTimeline = null;
      this.basicAnimationsResetTimeout = null;
      this.$keywordsContainer = null;
      this.keywordsContainer = null;
      this.$keywords = null;
      this.keywordsLength = 0;
      this.keywordsDimensions = [];
      this.slideInTimeout = null;
      this.slideOutTimeout = null;
      this.prevWindowWidth = window.innerWidth;
      this.build();
    }
    async init() {
      await this._measure();
      await this._onFontsLoad();
    }
    _measure() {
      return fastdomPromised.measure(() => {
        const t = getComputedStyle(this.element);
        this.fontInfo.elementFontFamily = t.fontFamily.replace(/"/g, "").replace(/'/g, "").split(",")[0];
        this.fontInfo.elementFontWeight = t.fontWeight;
        this.fontInfo.elementFontStyle = t.fontStyle;
        this.fontInfo.fontFamilySlug = window.liquidSlugify(this.fontInfo.elementFontFamily);
      });
    }
    _onFontsLoad() {
      return fastdomPromised.measure(() => {
        if (window.liquidCheckedFonts.find((e) => e === this.fontInfo.fontFamilySlug)) {
          return this.build();
        }
        const t = new FontFaceObserver(this.fontInfo.elementFontFamily, {
          weight: this.fontInfo.elementFontWeight,
          style: this.fontInfo.elementFontStyle
        });
        t.load().finally(() => {
          window.liquidCheckedFonts.push(this.fontInfo.fontFamilySlug);
          this.build();
        });
      });
    }
    build() {
      const t = [];
      const e = this.$element.closest("[data-custom-animations]");
      const n = this.$element.children("[data-custom-animations]");
      const s = this.$element.children("[data-split-text]");
      if (this.element.hasAttribute("data-split-text")) {
        const o = this.$element.data("plugin_liquidSplitText");
        o && t.push(o.splitDonePormise);
      }
      if (s.length) {
        const o = s.data("plugin_liquidSplitText");
        o && t.push(o.splitDonePormise);
      }
      if (e.length) {
        const o = e.data("plugin_liquidCustomAnimations");
        o && t.push(o.animationsDonePromise);
      }
      if (n.length) {
        const o = n.data("plugin_liquidCustomAnimations");
        o && t.push(o.animationsDonePromise);
      }
      if (this.element.hasAttribute("data-custom-animations")) {
        const o = this.$element.data("plugin_liquidCustomAnimations");
        o && t.push(o.animationsDonePromise);
      }
      if (t.length) {
        Promise.all(t).finally(() => {
          this.init();
        });
      } else {
        this.init();
      }
    }
    async init() {
      this._handleWindowResize = liquidDebounce(this._handleWindowResize.bind(this), 350);
      this.$keywordsContainer = i(".txt-rotate-keywords", this.element);
      if (!this.$keywordsContainer.length) {
        return console.warn("Could not find keywords container");
      }
      this.keywordsContainer = this.$keywordsContainer[0];
      this.keywordsInner = this.keywordsContainer.querySelector(".txt-rotate-keywords-inner");
      this.$keywords = i(".txt-rotate-keyword", this.$keywordsContainer);
      this.$keywords.attr("class", "txt-rotate-keyword").eq(0).addClass("active");
      this.keywordsLength = this.$keywords.length - 1;
      this.keywordsDimensions = await this.getKeywordsDimensions();
      this.setContainerWidth(0);
      this.initAnimations();
      this._windowResize();
      this.$element.addClass("text-rotator-activated");
    }
    async getKeywordsDimensions() {
      const t = [];
      this.$keywords.each((n, s) => {
        const o = new Promise((a) => {
          new IntersectionObserver(([r], c) => {
            c.disconnect();
            const {
              boundingClientRect: { width: p, height: g }
            } = r;
            a({
              width: p,
              height: g
            });
          }).observe(s);
        });
        t.push(o);
      });
      const e = await Promise.all(t);
      return e;
    }
    updateActiveIndex() {
      this.activeKeywordIndex = this.activeKeywordIndex + 1 > this.keywordsLength ? 0 : this.activeKeywordIndex + 1;
    }
    updateNextIndex() {
      this.nextKeywordIndex = this.nextKeywordIndex + 1 > this.keywordsLength ? 0 : this.nextKeywordIndex + 1;
    }
    setActiveClass() {
      this.$keywords.removeClass("active");
      this.$keywords.eq(this.activeKeywordIndex).addClass("active");
    }
    setNextClass() {
      this.$keywords.removeClass("is-next");
      this.$keywords.eq(this.nextKeywordIndex).addClass("is-next");
    }
    setContainerWidth(t) {
      const e = this.$keywordsContainer[0];
      if (this.options.animationType === "list") {
        return e.style.width = `${Math.max(...this.keywordsDimensions.map((n) => parseInt(n.width, 10)))}px`;
      }
      e.style.width = `${this.keywordsDimensions[t].width}px`;
    }
    slideInNextKeyword() {
      const t = this.$keywords.eq(this.nextKeywordIndex);
      const e = this.isFirstItterate ? this.options.delay / 2 : this.options.delay;
      this.slideInTimeout = setTimeout(() => {
        this.setContainerWidth(this.nextKeywordIndex);
        t.removeClass("lqd-keyword-slide-out").addClass("lqd-keyword-slide-in");
        this.isFirstItterate = false;
        this.updateNextIndex();
        this.setNextClass();
        this.slideOutAciveKeyword();
        clearTimeout(this.slideInTimeout);
      }, e * 1000);
    }
    slideOutAciveKeyword() {
      const t = this.$keywords.eq(this.activeKeywordIndex);
      const e = this.isFirstItterate ? this.options.delay / 2 : this.options.delay;
      t.removeClass("lqd-keyword-slide-in").addClass("lqd-keyword-slide-out");
      this.updateActiveIndex();
      this.setActiveClass();
      this.slideOutTimeout = setTimeout(() => {
        this.slideInNextKeyword();
        clearTimeout(this.slideOutTimeout);
      }, e * 1000);
    }
    buildBaiscAnimation() {
      this.$element.addClass("txt-rotator-basic");
      this.basicAnimationTimeline = gsap.timeline({
        easing: "power2.inOut",
        onStart: () => {
          this.isFirstItterate = false;
          if (this.basicAnimationsResetTimeout) {
            clearTimeout(this.basicAnimationsResetTimeout);
          }
          this.setContainerWidth(this.nextKeywordIndex);
        },
        onComplete: () => {
          this.updateActiveIndex();
          this.updateNextIndex();
          this.setActiveClass();
          this.setNextClass();
          this.basicAnimationsResetTimeout = setTimeout(() => this.basicAnimationTimeline && this.basicAnimationTimeline.restart(), this.options.delay * 1000);
        }
      });
      this.$keywords.each((t, e) => {
        this.basicAnimationTimeline.to(e, {
          duration: 0.125,
          opacity: 1,
          onStart: () => {
            const n = i(e);
            this.$keywords.not(n).removeClass("active");
            n.addClass("active");
          }
        });
      });
    }
    buildListAnimation() {
      const t = 2;
      const e = parseInt(getComputedStyle(this.keywordsContainer).getPropertyValue("--visible-words"), 10);
      const n = this.keywordsDimensions.map((y) => y.height).reduce((y, w) => y + w, 0);
      const s = this.keywordsDimensions.slice(0, e).map((y) => y.height).reduce((y, w) => y + w, 0);
      const o = this.$keywords.length;
      const a = gsap.delayedCall(this.options.delay, v.bind(this));
      let r = 1;
      let c = r + 1;
      let p = 0;
      let g = false;
      const u = gsap.timeline({
        defaults: {
          repeat: -1,
          duration: t,
          ease: "none"
        },
        paused: true
      });
      this.keywordsInnerClone = this.keywordsInner.cloneNode(true);
      this.keywordsInnerClone.classList.add("txt-rotate-keywords-inner-clone", "lqd-overlay", "flex-column");
      this.keywordsContainer.append(this.keywordsInnerClone);
      this.keywordsContainer.style.height = `${s}px`;
      this.keywordsContainer.style.overflow = `hidden`;
      this.$keywords.add(i(this.keywordsInnerClone).children()).each((y, w) => {
        y = y % o;
        const b = this.keywordsDimensions[y].height;
        const q = gsap.utils.wrap(b * -1, n - b);
        gsap.set(w, {
          position: "absolute",
          y: p
        });
        u.to(w, {
          y: `-=${n}`,
          modifiers: {
            y: gsap.utils.unitize(q)
          }
        }, 0).add(`keyword-${y + 1}`, gsap.utils.mapRange(0, o, 0, t)(y));
        p += b;
      });
      const m = () => {
        gsap.set([this.keywordsInner, this.keywordsInnerClone], {
          "--current-keyword-height": `${this.keywordsDimensions[r - 1].height / 2 * -1}px`
        });
      };
      m();
      const f = (y, w) => {
        if (g) {
          return new gsap.timeline().add(u.tweenFromTo(y, t, {
            duration: this.options.duration,
            ease: this.options.easing
          })).add(u.tweenFromTo(0, w, {
            duration: this.options.duration,
            ease: this.options.easing,
            immediateRender: false
          }));
        }
        return u.tweenFromTo(y, w, { duration: this.options.duration, ease: this.options.easing });
      };
      function v() {
        a && a.restart(true);
        r === o ? g = true : g = false;
        if (!g) {
          f(`keyword-${r}`, `keyword-${c}`);
        } else {
          f(`keyword-${o}`, `keyword-${1}`);
        }
        m();
        r = r >= o ? 1 : r + 1;
        c = r === o ? 1 : r + 1;
      }
      v();
    }
    initAnimations() {
      const { animationType: t } = this.options;
      switch (t) {
        case "basic":
          this.buildBaiscAnimation();
          break;
        case "list":
          this.buildListAnimation();
          break;
        default:
          this.slideInNextKeyword();
      }
    }
    _windowResize() {
      i(window).on("resize.lqdTextRotator", this._handleWindowResize.bind(this));
    }
    _handleWindowResize() {
      if (this.prevWindowWidth === window.innerWidth)
        return;
      gsap.killTweensOf(this.$keywordsContainer[0]);
      this.keywordsInner && gsap.killTweensOf(this.keywordsInner);
      this.$keywords.each((t, e) => {
        gsap.killTweensOf(e);
      });
      if (this.keywordsInnerClone) {
        gsap.killTweensOf(this.keywordsInnerClone);
        i(this.keywordsInnerClone).children().each((t, e) => {
          gsap.killTweensOf(e);
        });
      }
      this.destroy();
      this._onWindowResize();
      this.prevWindowWidth = window.innerWidth;
    }
    _onWindowResize() {
      this.activeKeywordIndex = 0;
      this.nextKeywordIndex = 1;
      this.isFirstItterate = true;
      this.basicAnimationTimeline = null;
      this.basicAnimationsResetTimeout = null;
      this.slideInTimeout && clearTimeout(this.slideInTimeout);
      this.slideOutTimeout && clearTimeout(this.slideOutTimeout);
      this.build();
    }
    destroy() {
      i(window).off("resize.lqdTextRotator");
      this.keywordsInnerClone && this.keywordsInnerClone.remove();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("text-rotator-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-text-rotator]").liquidTextRotator();
});
((i) => {
  const l = "liquidParallax";
  const d = {
    start: "top bottom",
    end: "bottom top",
    ease: "linear",
    scrub: 0.55,
    parallaxBG: false,
    scaleBG: true,
    overflowHidden: false,
    startTrigger: null,
    parallaxTargets: null,
    skipWillChange: false
  };
  const h = {};
  const t = {};
  function e() {
    const u = window.liquidBreakpoints || {};
    const m = Object.entries(u).filter(([, f]) => f && f.is_enabled && f.direction === "max").sort((f, v) => Number(f[1].value) - Number(v[1].value));
    for (const [f, v] of m) {
      if (window.matchMedia("(max-width: " + Number(v.value) + "px)").matches)
        return f;
    }
    return "desktop";
  }
  window.liquidParallaxActiveBreakpoint = e;
  const n = {
    blur: "0px",
    brightness: "100%",
    contrast: "100%"
  };
  const s = "blur(0px) brightness(100%) contrast(100%)";
  const o = new Set;
  let a = false;
  function r() {
    o.forEach((u) => {
      u.onWindowResize();
    });
  }
  function c(u) {
    o.add(u);
    if (a)
      return;
    a = true;
    i(window).on("resize.lqdParallax", liquidDebounce(r, 500));
  }
  function p(u) {
    o.delete(u);
  }

  class g {
    constructor(u, m, f, v) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...m };
      this.element = u;
      this.$element = i(u);
      this.parallaxFromOptions = { ...h, ...f };
      this.parallaxToOptions = { ...t, ...v };
      this.ST = null;
      this.parallaxTimeline = null;
      this.stepTweens = null;
      this.breakpoints = null;
      this.applyOn = null;
      this.activeBreakpoint = null;
      this.willChangeObserver = null;
      this.parallaxElements = [];
      this.isRowBg = this.element.getAttribute("data-row-bg");
      this.rect = {};
      this.bgImg = null;
      this.sentinel = null;
      this.parallaxFigure = null;
      this.parallaxMarkupExists = this.element.classList.contains("lqd-parallax-markup-exists");
      if (this.isV2()) {
        const w = m && Object.prototype.hasOwnProperty.call(m, "breakpoints");
        this.breakpoints = this.parseBreakpoints(w ? m.breakpoints : this.$element.data("parallax-breakpoints"));
        if (m && Object.prototype.hasOwnProperty.call(m, "apply")) {
          this.applyOn = this.parseApplyList(m.apply);
        } else if (this.element.hasAttribute("data-parallax-apply")) {
          this.applyOn = this.parseApplyList(this.element.getAttribute("data-parallax-apply"));
        }
        this.activeBreakpoint = m && m.activeBreakpoint || e();
      }
      const y = [];
      if (this.$element.hasClass("lqd-css-sticky") && this.$element.data("plugin_liquidStickyRow")) {
        const w = this.$element.data("plugin_liquidStickyRow");
        const b = w.rowStickyInitPromise;
        b && y.push(b);
      }
      if (this.element.hasAttribute("data-split-text")) {
        this.element.addEventListener("lqdsplittext-before", (b) => {
          this._splitGen = b.detail && b.detail.gen;
          this.releaseSplitAnimation();
        });
        this.element.addEventListener("lqdsplittext", (b) => {
          this.syncAfterSplit(b.detail && b.detail.gen);
        });
        this.$element.liquidSplitText({ forceApply: true });
        const w = this.$element.data("plugin_liquidSplitText");
        w && y.push(w.splitDonePormise);
      }
      if (y.length > 0) {
        Promise.all(y).then(this.build.bind(this));
      } else {
        this.build();
      }
    }
    isTextReveal() {
      return !!(this.element && this.element.classList.contains("lqd-text-reveal-el"));
    }
    isV2() {
      const u = window.liquidParams || {};
      if (this.isTextReveal())
        return u.textRevealV2 === true;
      return u.parallaxV2 === true;
    }
    parseBreakpoints(u) {
      if (typeof u === "string") {
        try {
          u = JSON.parse(u);
        } catch (m) {
          return null;
        }
      }
      if (!u || typeof u !== "object" || Array.isArray(u))
        return null;
      return u;
    }
    parseApplyList(u) {
      if (typeof u === "string") {
        try {
          u = JSON.parse(u);
        } catch (m) {
          return null;
        }
      }
      if (!Array.isArray(u))
        return null;
      return u.filter((m) => typeof m === "string" && m !== "");
    }
    filterComponentKeys() {
      return ["blur", "brightness", "contrast"];
    }
    hasFilterKeys(u) {
      if (!u || typeof u !== "object")
        return false;
      return this.filterComponentKeys().some((m) => Object.prototype.hasOwnProperty.call(u, m));
    }
    filterCss(u, m) {
      if (u == null || u === "")
        return m;
      if (typeof u === "number") {
        if (m === "0px")
          return `${u}px`;
        if (u <= 3)
          return String(u);
        return `${u}%`;
      }
      return String(u);
    }
    expandFilter(u) {
      if (!this.hasFilterKeys(u))
        return u;
      const m = { ...u };
      const f = this.filterCss(m.blur, "0px");
      const v = this.filterCss(m.brightness, "100%");
      const y = this.filterCss(m.contrast, "100%");
      this.filterComponentKeys().forEach((w) => {
        delete m[w];
      });
      m.filter = `blur(${f}) brightness(${v}) contrast(${y})`;
      return m;
    }
    pairFilters(u, m) {
      if (!this.hasFilterKeys(u) && !this.hasFilterKeys(m)) {
        return { from: u, to: m };
      }
      const f = (v) => this.expandFilter({ ...n, ...v || {} });
      return { from: f(u), to: f(m) };
    }
    perspectiveValue(u) {
      const m = u && u.transformPerspective;
      if (m == null || m === "" || m === 0 || m === "0" || m === "0px")
        return null;
      return m;
    }
    stampPerspective(u, m) {
      if (m == null || !Array.isArray(u))
        return;
      u.forEach((f) => {
        if (!f)
          return;
        if (f.from)
          f.from.transformPerspective = m;
        if (f.to)
          f.to.transformPerspective = m;
      });
    }
    async build() {
      await this.handleSentinel();
      await this.buildParallaxMarkups();
      this.parallaxElements = this.getParallaxElements();
      new IntersectionObserver(([u], m) => {
        if (u.isIntersecting) {
          m.disconnect();
          this.init();
        }
      }, { rootMargin: "50%" }).observe(this.element);
    }
    getParallaxElements() {
      if (this.options.parallaxTargets) {
        return [...this.element.querySelectorAll(this.options.parallaxTargets)];
      } else if (this.element.classList.contains("vc_column_container")) {
        return [this.element.querySelector(".vc_column-inner")];
      } else if (this.options.parallaxBG) {
        return [this.parallaxFigure];
      } else {
        return [this.element];
      }
    }
    measure() {
      this.captureRect();
      return Promise.resolve();
    }
    layoutRect(u) {
      const m = u || this.element;
      const f = m.style;
      const v = f.transform;
      const y = v && v !== "none";
      if (y)
        f.transform = "none";
      const w = m.getBoundingClientRect();
      if (y)
        f.transform = v;
      return w;
    }
    captureRect() {
      const u = this.layoutRect(this.element);
      this.rect.width = u.width;
      this.rect.height = u.height;
      this.rect.top = u.top + window.scrollY;
      this.rect.left = u.left;
    }
    getBgInfo() {
      return fastdomPromised.measure(() => {
        if (!this.bgImg) {
          if (this.isRowBg) {
            return this.bgImg = `url(${this.isRowBg})`;
          }
          const u = getComputedStyle(this.element);
          this.bgImg = u.backgroundImage;
        }
      });
    }
    async handleSentinel() {
      c(this);
      await this.createSentinel();
    }
    createSentinel() {
      return fastdomPromised.mutate(() => {
        this.sentinel = document.createElement("div");
        this.sentinel.setAttribute("class", "lqd-parallax-sentinel pointer-events-none pos-abs z-index--1 invisible absolute -z-1");
        document.body.appendChild(this.sentinel);
      });
    }
    positionSentinel() {
      return fastdomPromised.mutate(() => {
        this.sentinel.style.width = `${this.rect.width}px`;
        this.sentinel.style.height = `${this.rect.height}px`;
        this.sentinel.style.top = `${this.rect.top}px`;
        this.sentinel.style.left = `${this.rect.left}px`;
      });
    }
    buildParallaxMarkups() {
      return new Promise(async (u) => {
        if (!this.options.parallaxBG) {
          this.initParallax();
          u();
        } else {
          await this.getBgInfo();
          this.initParallaxBG();
          this.element.classList.add("lqd-parallax-bg");
          u();
        }
      });
    }
    initParallax() {
      const { overflowHidden: u } = this.options;
      if (!this.element.classList.contains("vc_column_container") && !this.element.classList.contains("ld-fancy-heading") && (u || this.options.forceWrap)) {
        const m = u ? "overflow-hidden" : "";
        const f = document.createElement("div");
        f.setAttribute("class", `ld-parallax-wrap ${m}`);
        this.element.parentNode.insertBefore(f, this.element);
        f.appendChild(this.element);
      }
    }
    initParallaxBG() {
      const u = this.element.hasAttribute("data-slideshow-bg");
      const m = this.element.querySelector(":scope > .lqd-vbg-wrap");
      const f = this.$element.data("plugin_liquidSlideshowBG");
      const v = this.$element.data("plugin_liquidRowBG");
      if ((!u && !this.isRowBg || this.isRowBg && !v || u && !f) && !m) {
        if (!this.parallaxMarkupExists) {
          this.createParallaxBgMarkup();
        }
        this.parallaxFigure = this.element.querySelector(".lqd-parallax-figure");
        this.updateParallaxBgOptions();
        this.setParallaxBgImg();
      }
      if (u) {
        return f.slideshowBgInitPromise.then((y) => {
          const w = y.slideshowInner;
          this.updateParallaxBgOptions();
          return w;
        });
      }
      if (this.isRowBg) {
        return v.rowBgInitPromise.then((y) => {
          const { rowBg: w } = y;
          this.updateParallaxBgOptions();
          return w;
        });
      }
      if (m) {
        this.updateParallaxBgOptions();
        return m.children;
      }
    }
    createParallaxBgMarkup() {
      const u = document.createElement("div");
      u.setAttribute("class", "lqd-parallax-container lqd-overlay overflow-hidden");
      u.setAttribute("style", "border-radius: inherit; background-size: inherit; background-attachment: inherit; background-repeat: inherit; background-position: inherit;");
      const m = document.createElement("figure");
      m.setAttribute("class", "lqd-parallax-figure lqd-overlay");
      m.setAttribute("style", "border-radius: inherit; background-size: inherit; background-attachment: inherit; background-repeat: inherit; background-position: inherit;");
      u.appendChild(m);
      this.$element.prepend(u);
    }
    setParallaxBgImg() {
      if (this.bgImg && this.bgImg !== "none" && this.options.parallaxBG) {
        this.parallaxFigure.style.backgroundImage = this.bgImg;
        this.element.classList.add("bg-none");
      }
    }
    updateParallaxBgOptions() {
      if (this.isV2())
        return;
      if (typeof this.parallaxFromOptions.yPercent === "undefined") {
        this.parallaxFromOptions.yPercent = -15;
      }
      if (typeof this.parallaxToOptions.yPercent === "undefined") {
        this.parallaxToOptions.yPercent = 0;
      }
    }
    init() {
      if (this.isV2()) {
        this.initV2();
        return;
      }
      fastdomPromised.measure(async () => {
        await this.measure();
        await this.positionSentinel();
      }).then(() => {
        fastdomPromised.mutate(() => {
          const u = this.options.parallaxBG;
          let { start: m, end: f, scrub: v, ease: y, startTrigger: w } = this.options;
          let b = this.sentinel;
          if (w) {
            if (typeof w === "string") {
              b = document.querySelector(w);
            } else {
              b = w;
            }
          }
          this.parallaxTimeline = gsap.timeline();
          this.parallaxTimeline.fromTo(this.parallaxElements, { ...this.parallaxFromOptions }, {
            ease: y,
            ...this.parallaxToOptions
          });
          this.ST = this.bindScrollTrigger(this.parallaxTimeline, b, () => m, () => f, u ? 0.35 : v, () => {
            m = this.options.start;
            f = this.options.end;
          });
          !this.options.skipWillChange && this.addWillChange();
          if (u) {
            gsap.to(this.parallaxElements, {
              opacity: 1
            });
          }
          this.element.dispatchEvent(new CustomEvent("lqd-parallax-initiated"));
        });
      });
    }
    initV2() {
      fastdomPromised.measure(async () => {
        await this.measure();
        await this.positionSentinel();
      }).then(() => {
        fastdomPromised.mutate(() => {
          if (!this.activeBreakpoint) {
            this.activeBreakpoint = e();
          }
          this.rebuildTween();
          if (!this.didInitiate) {
            this.didInitiate = true;
            this.element.dispatchEvent(new CustomEvent("lqd-parallax-initiated"));
          }
        });
      });
    }
    relayout(u = {}) {
      const m = u || {};
      if (Object.prototype.hasOwnProperty.call(m, "from")) {
        this.parallaxFromOptions = { ...m.from || {} };
      }
      if (Object.prototype.hasOwnProperty.call(m, "to")) {
        this.parallaxToOptions = { ...m.to || {} };
      }
      if (Object.prototype.hasOwnProperty.call(m, "options")) {
        this.options = { ...this.options, ...m.options || {} };
      }
      if (Object.prototype.hasOwnProperty.call(m, "steps")) {
        this.stepsOverride = m.steps;
      }
      if (Object.prototype.hasOwnProperty.call(m, "breakpoints")) {
        this.breakpoints = this.parseBreakpoints(m.breakpoints);
      }
      if (Object.prototype.hasOwnProperty.call(m, "apply")) {
        this.applyOn = this.parseApplyList(m.apply);
      }
      this.activeBreakpoint = m.activeBreakpoint || e();
      if (!this.sentinel)
        return;
      this.rebuildTween();
    }
    isDisabled(u) {
      if (Array.isArray(this.applyOn)) {
        return this.applyOn.indexOf(u) === -1;
      }
      const m = window.liquidParams || {};
      const f = this.isTextReveal() ? "textRevealApplyBreakpoints" : "parallaxApplyBreakpoints";
      if (Array.isArray(m[f]) && m[f].indexOf(u) === -1) {
        return true;
      }
      const v = this.breakpoints && this.breakpoints[u];
      return !!(v && v.disable === true);
    }
    baseSteps() {
      let u;
      if (this.stepsOverride !== undefined) {
        u = this.stepsOverride;
      } else if (this.options.steps != null) {
        u = this.options.steps;
      } else {
        u = this.$element.data("parallax-steps");
      }
      if (typeof u === "string") {
        try {
          u = JSON.parse(u);
        } catch (m) {
          u = null;
        }
      }
      return Array.isArray(u) ? u : [];
    }
    cloneStep(u) {
      const m = u || {};
      return {
        start: m.start,
        end: m.end,
        ease: m.ease,
        from: { ...m.from || {} },
        to: { ...m.to || {} },
        values: { ...m.values || {} }
      };
    }
    mergeSteps(u, m) {
      const f = Array.isArray(u) ? u : [];
      const v = f.map((y) => this.cloneStep(y));
      if (!Array.isArray(m))
        return v;
      m.forEach((y, w) => {
        if (!y || typeof y !== "object" || !Object.keys(y).length)
          return;
        if (!v[w]) {
          v[w] = this.cloneStep(y);
          return;
        }
        const b = v[w];
        if ("start" in y)
          b.start = y.start;
        if ("end" in y)
          b.end = y.end;
        if ("ease" in y)
          b.ease = y.ease;
        if (y.from && typeof y.from === "object") {
          b.from = { ...b.from, ...y.from };
        }
        if (y.to && typeof y.to === "object") {
          b.to = { ...b.to, ...y.to };
        }
        if (y.values && typeof y.values === "object") {
          b.values = { ...b.values || {}, ...y.values };
        }
      });
      return v;
    }
    maxBreakpointsToOverlay(u) {
      const m = window.liquidBreakpoints || {};
      const f = m[u];
      if (!f || f.direction !== "max" || f.value == null)
        return [];
      const v = Number(f.value);
      return Object.entries(m).filter(([, y]) => y && y.is_enabled && y.direction === "max" && Number(y.value) >= v).sort((y, w) => Number(w[1].value) - Number(y[1].value)).map(([y]) => y);
    }
    overlayRecord(u, m) {
      if (!m || typeof m !== "object")
        return;
      const f = Array.isArray(u.steps) && u.steps.length;
      if (m.options && typeof m.options === "object") {
        u.options = { ...u.options, ...m.options };
      }
      if ("steps" in m && m.steps != null) {
        u.steps = this.mergeSteps(u.steps, m.steps);
      }
      if ("start" in m && m.start) {
        if (f && u.steps[0]) {
          u.steps[0].start = m.start;
        } else {
          u.options.start = m.start;
        }
      }
      if ("end" in m && m.end && !f) {
        u.options.end = m.end;
      }
      if (m.from && typeof m.from === "object") {
        u.from = { ...u.from, ...m.from };
      }
      if (m.to && typeof m.to === "object" && !f) {
        u.to = { ...u.to, ...m.to };
      }
    }
    cascade(u) {
      const m = {
        from: { ...this.parallaxFromOptions },
        to: { ...this.parallaxToOptions },
        options: { ...this.options },
        steps: this.mergeSteps(this.baseSteps(), null)
      };
      const f = ["desktop"].concat(this.maxBreakpointsToOverlay(u));
      f.forEach((v) => {
        if (this.breakpoints && this.breakpoints[v]) {
          this.overlayRecord(m, this.breakpoints[v]);
        }
      });
      return m;
    }
    resolveTrigger(u) {
      if (!u) {
        if (this.element.hasAttribute("data-split-text"))
          return this.element;
        return this.sentinel;
      }
      if (typeof u === "string")
        return document.querySelector(u);
      return u;
    }
    clearTweenProps() {
      const u = (this.parallaxElements || []).filter(Boolean);
      if (u.length) {
        gsap.set(u, { clearProps: "transform,opacity,filter,color,transition,willChange" });
      }
      if (this.element && this.element.classList.contains("lqd-text-reveal-el")) {
        gsap.set(this.element, { clearProps: "color" });
      }
    }
    killOwnedScrollTriggers() {
      if (!window.ScrollTrigger || typeof ScrollTrigger.getAll !== "function")
        return;
      const u = this.element && this.element.hasAttribute("data-split-text");
      ScrollTrigger.getAll().slice().forEach((m) => {
        if (!m)
          return;
        const f = this.sentinel && m.trigger === this.sentinel;
        const v = u && m.trigger === this.element;
        const y = this.parallaxTimeline && m.animation === this.parallaxTimeline;
        if (f || v || y)
          m.kill();
      });
    }
    releaseSplitAnimation() {
      const u = (this.parallaxElements || []).filter(Boolean);
      if (u.length)
        gsap.killTweensOf(u);
      this.killAnimation();
      if (u.length)
        gsap.set(u, { clearProps: "all" });
    }
    killAnimation() {
      if (this.willChangeObserver) {
        this.willChangeObserver.disconnect();
        this.willChangeObserver = null;
      }
      this.killOwnedScrollTriggers();
      if (this.parallaxElements && this.parallaxElements.length) {
        gsap.killTweensOf(this.parallaxElements);
      }
      if (this.parallaxTimeline) {
        const u = this.parallaxTimeline.scrollTrigger || this.ST;
        if (u && u.kill)
          u.kill();
        this.parallaxTimeline.kill();
        this.parallaxTimeline.clear();
        this.parallaxTimeline = null;
      } else if (this.ST && this.ST.kill) {
        this.ST.kill();
      }
      this.ST = null;
      this.stepTweens = null;
    }
    rebuildTween() {
      const u = this.activeBreakpoint || e();
      this.activeBreakpoint = u;
      this.killAnimation();
      this.clearTweenProps();
      if (this.isDisabled(u) || !this.sentinel)
        return;
      if (!this.parallaxElements || !this.parallaxElements.length) {
        this.parallaxElements = this.getParallaxElements();
      }
      const m = this.cascade(u);
      const f = (m.steps || []).filter(Boolean);
      if (f.length) {
        this.buildStepsTween(m, f);
      } else {
        this.buildSingleTween(m);
      }
    }
    buildSingleTween(u) {
      const m = u.options;
      let { start: f, end: v, scrub: y, ease: w, startTrigger: b } = m;
      const q = this.resolveTrigger(b);
      const C = this.pairFilters({ ...u.from }, { ...u.to });
      const x = C.from;
      const S = C.to;
      const O = this.perspectiveValue(m);
      if (O != null) {
        x.transformPerspective = O;
        S.transformPerspective = O;
      }
      const E = !!m.parallaxBG;
      if (E) {
        if (typeof x.yPercent === "undefined")
          x.yPercent = -15;
        if (typeof S.yPercent === "undefined")
          S.yPercent = 0;
      }
      this.parallaxTimeline = gsap.timeline();
      this.stepTweens = null;
      this.parallaxTimeline.fromTo(this.parallaxElements, x, {
        ease: w,
        ...S
      });
      let T;
      T = this.ST = this.bindScrollTrigger(this.parallaxTimeline, q, () => f, () => v, E ? 0.35 : y, () => {
        if (this.ST !== T)
          return;
        f = m.start;
        v = m.end;
      });
      if (!m.skipWillChange)
        this.addWillChange(x, S);
      if (E) {
        gsap.to(this.parallaxElements, {
          opacity: 1
        });
      }
    }
    sequenceViewportSteps(u, m) {
      const f = Array.isArray(u) ? u : [];
      const v = { ...m || {} };
      const y = this.hasFilterKeys(v) || f.some((x) => this.hasFilterKeys(x && x.values));
      const w = (x) => {
        const S = { ...x || {} };
        if (y && !this.hasFilterKeys(S))
          Object.assign(S, n);
        return this.expandFilter(S);
      };
      const b = w(v);
      let q = b;
      const C = [];
      f.forEach((x) => {
        const S = { ...q, ...w(x && x.values) };
        const O = Math.max(0, Math.min(100, Number(x && x.start) || 0));
        const E = Math.max(0, Math.min(100, Number(x && x.end) || 0));
        const T = {};
        const I = {};
        Object.keys(S).forEach((k) => {
          if (q[k] === S[k])
            return;
          if (Object.prototype.hasOwnProperty.call(q, k))
            T[k] = q[k];
          I[k] = S[k];
        });
        if (I.filter && !Object.prototype.hasOwnProperty.call(T, "filter")) {
          T.filter = s;
        }
        if (Object.keys(I).length) {
          const k = { start: O, end: E, from: T, to: I };
          if (x && x.ease)
            k.ease = x.ease;
          C.push(k);
        }
        q = S;
      });
      return { origin: b, sequence: C };
    }
    fitStepStagger(u, m) {
      if (!u || m <= 0)
        return null;
      const f = (this.parallaxElements || []).length;
      if (f < 2)
        return null;
      const v = typeof u === "object" && u.from ? u.from : "start";
      const y = m * 0.85;
      return {
        amount: y,
        from: v,
        duration: Math.max(m - y, 0.001)
      };
    }
    buildStepsTween(u, m) {
      const f = u.options;
      const v = f.ease;
      const y = !!f.parallaxBG;
      const w = this.resolveTrigger(f.startTrigger);
      const b = this.sequenceViewportSteps(m, u.from || {});
      const q = b.origin;
      const C = b.sequence;
      this.stampPerspective(C, this.perspectiveValue(f));
      const x = f.stagger || null;
      this.parallaxTimeline = gsap.timeline();
      this.stepTweens = [];
      if (Object.keys(q).length) {
        this.stepTweens.push(this.parallaxTimeline.set(this.parallaxElements, q, 0));
      }
      C.forEach((S) => {
        const O = S.start / 100;
        const E = Math.max(0, S.end - S.start) / 100;
        const T = { ...S.from || {} };
        const I = { ...S.to || {} };
        if (E === 0) {
          const k = { ...T, ...I };
          if (Object.keys(k).length) {
            this.stepTweens.push(this.parallaxTimeline.set(this.parallaxElements, k, O));
          }
        } else if (Object.keys(I).length) {
          const k = this.fitStepStagger(x, E);
          const M = {
            ...I,
            ease: S.ease || v || "linear",
            duration: k ? k.duration : E,
            immediateRender: O === 0
          };
          if (k)
            M.stagger = { amount: k.amount, from: k.from };
          this.stepTweens.push(this.parallaxTimeline.fromTo(this.parallaxElements, T, M, O));
        }
      });
      if (this.parallaxTimeline.duration() < 1) {
        this.parallaxTimeline.set({}, {}, 1);
      }
      this.ST = this.bindScrollTrigger(this.parallaxTimeline, w, f.start || "top bottom", f.end || "bottom top", y ? 0.35 : f.scrub);
      if (!f.skipWillChange)
        this.addWillChange(null, null, C);
      if (y && !this.stepsAnimateOpacity(C)) {
        gsap.to(this.parallaxElements, {
          opacity: 1
        });
      }
    }
    bindScrollTrigger(u, m, f, v, y, w) {
      const b = {
        animation: u,
        trigger: m,
        start: f,
        end: v,
        scrub: y,
        onUpdate: () => {
          gsap.set(this.parallaxElements, {
            transition: "none"
          });
        },
        onScrubComplete: () => {
          gsap.set(this.parallaxElements, {
            transition: ""
          });
        }
      };
      if (w)
        b.onRefresh = w;
      return ScrollTrigger.create(b);
    }
    stepsAnimateOpacity(u) {
      return u.some((m) => {
        const f = m.from && Object.prototype.hasOwnProperty.call(m.from, "opacity");
        const v = m.to && Object.prototype.hasOwnProperty.call(m.to, "opacity");
        return f || v;
      });
    }
    addWillChange(u = this.parallaxFromOptions, m = this.parallaxToOptions, f = null) {
      const v = ["transform"];
      if (Array.isArray(f) && f.length) {
        const q = f.some((C) => {
          if (!C || !C.from || !C.to)
            return false;
          if (!Object.prototype.hasOwnProperty.call(C.from, "opacity"))
            return false;
          if (!Object.prototype.hasOwnProperty.call(C.to, "opacity"))
            return false;
          return C.from.opacity !== C.to.opacity;
        });
        if (q)
          v.push("opacity");
      } else if (u && m && Object.prototype.hasOwnProperty.call(u, "opacity") && Object.prototype.hasOwnProperty.call(m, "opacity") && u.opacity !== m.opacity) {
        v.push("opacity");
      }
      const y = (q) => q && Object.prototype.hasOwnProperty.call(q, "filter");
      const w = Array.isArray(f) && f.length ? f.some((q) => q && (y(q.from) || y(q.to))) : y(u) || y(m);
      if (w)
        v.push("filter");
      const b = v.join(", ");
      if (this.willChangeObserver) {
        this.willChangeObserver.disconnect();
        this.willChangeObserver = null;
      }
      if (!this.sentinel)
        return;
      this.willChangeObserver = new IntersectionObserver(([q]) => {
        if (q.isIntersecting) {
          gsap.set(this.parallaxElements, {
            willChange: b
          });
        } else {
          gsap.set(this.parallaxElements, {
            willChange: "auto"
          });
        }
      });
      this.willChangeObserver.observe(this.sentinel);
    }
    async syncAfterSplit(u) {
      if (!this.sentinel)
        return;
      const m = () => u == null || u === this._splitGen;
      await new Promise((v) => requestAnimationFrame(v));
      if (!m() || !this.sentinel)
        return;
      let f = this.getParallaxElements();
      if (!f.length)
        return;
      this.parallaxElements = f;
      this.captureRect();
      await this.positionSentinel();
      if (!m() || !this.sentinel)
        return;
      if (!i.data(this.element, "plugin_" + l))
        return;
      this.activeBreakpoint = e();
      f = this.getParallaxElements();
      if (!f.length)
        return;
      this.parallaxElements = f;
      this.rebuildTween();
    }
    async onWindowResize() {
      if (!this.element || this.element.hasAttribute("data-split-text"))
        return;
      if (!i.data(this.element, "plugin_" + l))
        return;
      this.captureRect();
      await this.positionSentinel();
      if (!i.data(this.element, "plugin_" + l))
        return;
      if (this.isV2()) {
        this.activeBreakpoint = e();
        this.rebuildTween();
        return;
      }
      if (this.ST && this.ST.refresh)
        this.ST.refresh();
    }
    destroy() {
      p(this);
      this.killAnimation();
      if (this.sentinel) {
        this.sentinel.remove();
        this.sentinel = null;
      }
      if (this.parallaxElements && this.parallaxElements.length) {
        gsap.set(this.parallaxElements, { clearProps: "all" });
      }
      i.data(this.element, "plugin_" + l, null);
    }
  }
  i.fn[l] = function(u, m, f) {
    return this.each(function() {
      const v = window.liquidParams || {};
      const y = this.classList && this.classList.contains("lqd-text-reveal-el");
      const w = y ? v.textRevealV2 === true : v.parallaxV2 === true;
      const b = liquidParams?.disableParallaxOnMobile != null ? liquidParams.disableParallaxOnMobile : true;
      const q = { disableOnMobile: b, ...i(this).data("parallax-options"), ...u };
      if (w) {
        const S = u && Object.prototype.hasOwnProperty.call(u, "breakpoints") ? u.breakpoints : undefined;
        q.breakpoints = S !== undefined ? S : i(this).data("parallax-breakpoints");
        if (q.steps == null) {
          q.steps = i(this).data("parallax-steps");
        }
        if (u && Object.prototype.hasOwnProperty.call(u, "apply")) {
          q.apply = u.apply;
        }
      }
      const C = { ...i(this).data("parallax-from"), ...m };
      const x = { ...i(this).data("parallax-to"), ...f };
      if (!i.data(this, "plugin_" + l)) {
        if (!w && q.disableOnMobile && liquidIsMobile())
          return;
        i.data(this, "plugin_" + l, new g(this, q, C, x));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-parallax]").not("[data-pin]:not(.vc_row), .rev-slidebg").liquidParallax();
});
(function(i) {
  const l = "liquidTransitionDelay";
  let d = {
    elements: null,
    startDelay: 0,
    delayBetween: 250,
    random: false,
    reverse: false,
    delayType: "transition"
  };

  class h {
    constructor(t, e) {
      this.element = t;
      this.$element = i(t);
      this.options = i.extend({}, d, e);
      this._defaults = d;
      this._name = l;
      const n = this.$element.find("[data-split-text]").get();
      const s = [];
      if (this.element.hasAttribute("data-split-text")) {
        n.push(this.element);
      }
      n.forEach((o) => {
        const a = i(o).data("plugin_liquidSplitText");
        if (a) {
          s.push(a.splitDonePormise);
        }
      });
      if (s.length) {
        Promise.all(s).then(this.init.bind(this));
      } else {
        this.init();
      }
    }
    init() {
      this.addDelays();
    }
    addDelays() {
      const { elements: t, delayBetween: e, startDelay: n, delayType: s, reverse: o } = this.options;
      if (t) {
        const a = !o ? i(t, this.element) : i(t, this.element).get().reverse();
        i.each(a, (r, c) => {
          const p = r * e + n;
          i(c).css({
            [`-webkit-${s}-delay`]: `${p}ms`,
            [`${s}-delay`]: `${p}ms`
          });
        });
      }
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("delay-options") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-transition-delay=true]").liquidTransitionDelay();
  i(".lqd-submenu-cover .main-nav > .menu-item-has-children:not(.megamenu) > .nav-item-children, .navbar-visible-ontoggle > ul").liquidTransitionDelay({
    elements: "> li",
    delayBetween: 60
  });
});
(function(i) {
  const l = "liquidMoveElement";
  let d = {
    target: "#selector",
    targetRelation: "closest",
    type: "prependTo",
    wrapper: null,
    wrapperClass: null,
    wrapWithElementorWidgetEl: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.movePromise = new Promise((n) => {
        this.element.addEventListener("element-was-moved", n(this, this));
      });
      this.build();
    }
    build() {
      const t = [];
      if (this.element.hasAttribute("data-pin")) {
        const e = this.$element.data("plugin_liquidPin");
        if (e) {
          t.push(e.pinPromise);
        }
      }
      if (t.length > 0) {
        Promise.all(t).then(this.init.bind(this));
      } else {
        this.init();
      }
    }
    init() {
      this.getHiddenClasses();
      this.moveElement();
    }
    getHiddenClasses() {
      const t = this.element.closest(".vc_column_container");
      if (t) {
        const e = t.getAttribute("class");
        const n = e.split(" ").filter((s) => s.search("vc_hidden") >= 0);
        if (n.length > 0) {
          this.element.classList.add([...n]);
        }
      }
    }
    moveElement() {
      fastdom.mutate(() => {
        const { target: t, type: e, targetRelation: n, wrapWithElementorWidgetEl: s, wrapper: o, wrapperClass: a } = this.options;
        let r = this.$element;
        let c = this.$element;
        if (o) {
          const p = i(o);
          if (a) {
            p.addClass(a);
          }
          p.insertBefore(this.$element);
          p.append(this.$element);
          c = p;
          r = p;
        }
        if (s) {
          const p = this.$element.closest(".elementor-element");
          const g = i("<div></div>");
          g.addClass(p.attr("class"));
          g.insertBefore(p);
          g.append(r);
          c = g;
        }
        c[e](this.$element[n](t));
        this.element.classList.add("element-was-moved");
        this.element.dispatchEvent(new CustomEvent("element-was-moved", { bubbles: false }));
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = i(this).data("move-element") || t;
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-move-element]").liquidMoveElement();
});
(function(i) {
  const l = "liquidScrollIndicator";
  let d = {
    start: "top top",
    end: "bottom top",
    scrollingTarget: "body",
    indicatorBase: ".lqd-scrl-indc-line",
    indicatorEl: ".lqd-scrl-indc-el",
    dir: "y",
    scale: false,
    origin: "null",
    waitForElementMove: false
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.scrollingTarget = this.getScrollingTarget();
      this.DOM.indicatorBase = this.DOM.element.querySelector(this.options.indicatorBase);
      this.DOM.indicatorEl = this.DOM.element.querySelector(this.options.indicatorEl);
      if (this.options.waitForElementMove) {
        const n = i(this.DOM.element).closest("[data-move-element]");
        if (n.length) {
          return n.data("plugin_liquidMoveElement").movePromise.then(() => this.init());
        }
      }
      this.init();
    }
    init() {
      const t = $liquidContents.length && $liquidContents[0].hasAttribute("data-liquid-stack");
      t ? this.initMutationObserver() : this.initialTrigger();
    }
    getScrollingTarget() {
      const { scrollingTarget: t } = this.options;
      switch (t) {
        case "this":
          return this.DOM.element;
        case "parentHref":
          return this.$element.closest("a").attr("href");
        case "siblingsHref":
          return this.$element.siblings("a").first().attr("href");
        default:
          const e = t.includes("#");
          let n = "body";
          if (e) {
            n = document.querySelector(t);
          }
          if (n === "body") {
            n = document.body;
          }
          return n;
      }
    }
    initialTrigger() {
      const { dir: t, scale: e, start: n, end: s, origin: o } = this.options;
      const { indicatorBase: a, scrollingTarget: r, indicatorEl: c } = this.DOM;
      const p = `scale${t.toUpperCase()}`;
      const g = t === "x" ? "scaleY" : "scaleX";
      let u;
      if (o) {
        gsap.set(c, { transformOrigin: o });
      }
      if (!e) {
        u = gsap.to(c, {
          [t]: t === "y" ? a.offsetHeight : a.offsetWidth,
          force3D: false
        });
      } else {
        u = gsap.fromTo(c, {
          [p]: 0,
          [g]: 1
        }, {
          scale: 1,
          force3D: false
        });
      }
      ScrollTrigger.create({
        trigger: r,
        animation: u,
        start: n,
        end: s,
        scrub: 0.5
      });
    }
    initMutationObserver() {
      const t = $liquidContents.data("plugin_liquidStack");
      if (!t || liquidIsMobile() || liquidWindowWidth() <= liquidMobileNavBreakpoint())
        return;
      t.stackInitPromise.then((e) => {
        const { dir: n } = this.options;
        const { indicatorBase: s, indicatorEl: o } = this.DOM;
        const a = [...e.$sectionElements].filter((c) => !c.classList.contains("main-footer")).length;
        const r = n === "y" ? s.offsetHeight : s.offsetWidth;
        new MutationObserver(() => {
          const c = document.body.getAttribute("data-lqd-stack-page");
          const p = gsap.utils.mapRange(1, a, 0, r, c);
          gsap.to(o, {
            [n]: p,
            ease: "linear",
            duration: 1
          });
        }).observe(document.body, { attributeFilter: ["data-lqd-stack-page"] });
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("scrl-indc-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-scroll-indicator]").liquidScrollIndicator();
});
(function(i) {
  const l = "liquidTimer";
  let d = {
    time: 5000,
    repeat: true,
    targets: [[]],
    cancelIfClickedOn: ""
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.DOM = {};
      this.DOM.element = t;
      this.DOM.$element = i(t);
      this.activeIndex = 0;
      this.firstRound = true;
      this.init();
    }
    init() {
      this.fn();
      this.startTimer();
      this.handleCancel();
    }
    handleCancel() {
      const { cancelIfClickedOn: t } = this.options;
      if (t) {
        this.DOM.$element.on("click", this.options.cancelIfClickedOn, () => {
          this.DOM.$element.css("--time", "0.3s");
          this.stopTimer();
        });
      }
    }
    fn() {
      const { targets: t } = this.options;
      !this.firstRound && (this.activeIndex = this.activeIndex === i(t[0][0], this.DOM.element).length - 1 ? 0 : this.activeIndex + 1);
      t.forEach((e, n) => {
        const s = i(e[0], this.DOM.element);
        s.removeClass("lqd-is-active");
        s.eq(this.activeIndex).addClass("lqd-is-active");
      });
      this.firstRound = false;
    }
    startTimer() {
      const t = parseInt(this.options.time, 10);
      if (this.options.repeat) {
        this.interval = setInterval(() => {
          this.fn();
        }, t);
      } else {
        this.timeout = setTimeout(() => {
          this.fn();
          clearTimeout(this.timeout);
        }, t);
      }
    }
    stopTimer() {
      this.interval && clearInterval(this.interval);
      this.timeout && clearTimeout(this.timeout);
    }
    destroy() {
      this.stopTimer();
      i.data(this, "plugin_" + l, null);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("timer-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-timer]").liquidTimer();
});
(function(i) {
  const l = "liquidCurtain";
  let d = {
    itemsSelector: ".lqd-curtain-item",
    contentSelector: ".lqd-curtain-item-content",
    contentWidthOuter: ".lqd-curtain-item-content-width-outer",
    contentWidthInner: ".lqd-curtain-item-content-width-inner",
    activeClassname: "lqd-curtain-item-active",
    inactiveClassname: "lqd-curtain-item-inactive",
    duration: 0.65,
    ease: "cubic-bezier(0.23, 1, 0.320, 1)",
    eventTrigger: "click"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.items = [...t.querySelectorAll(this.options.itemsSelector)];
      if (!this.items.length)
        return;
      this.onElementActive = this.onElementActive.bind(this);
      this.onWindowResize = window.liquidDebounce(this.onWindowResize.bind(this), 450);
      this.init();
    }
    init() {
      this.setActiveIndex();
      this.setActiveElement();
      this.setActiveContentWidth();
      this.events();
    }
    events() {
      const { eventTrigger: t } = this.options;
      this.items.forEach((e) => {
        e.addEventListener(t, this.onElementActive);
      });
      window.addEventListener("resize", this.onWindowResize);
    }
    setActiveIndex() {
      this.activeIndex = this.items.findIndex((t) => t.classList.contains(this.options.activeClassname));
    }
    setActiveElement() {
      this.activeElement = this.items[this.activeIndex];
    }
    activeContentSlideToggle() {}
    setActiveContentWidth() {
      if (!this.getElDirection().includes("row"))
        return;
      const t = this.activeElement.querySelector(this.options.contentWidthOuter);
      const e = t.offsetWidth;
      this.element.style.setProperty("--active-width", `${e}px`);
    }
    onElementActive(t) {
      const { activeClassname: e, inactiveClassname: n } = this.options;
      const s = t.currentTarget;
      this.items.forEach((o) => {
        o.classList.remove(e);
        o.classList.add(n);
      });
      s.classList.remove(n);
      s.classList.add(e);
      this.setActiveIndex();
      this.setActiveElement();
      this.activeContentSlideToggle();
    }
    getElDirection() {
      const t = window.getComputedStyle(this.activeElement);
      return t.flexDirection;
    }
    onWindowResize() {
      this.setActiveContentWidth();
    }
    destroy() {
      this.items.forEach((t) => {
        t.removeEventListener(this.options.eventTrigger, this.onElementActive);
      });
      window.removeEventListener("resize", this.onWindowResize);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("curtain-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-curtain]").liquidCurtain();
});
(function(i) {
  const l = "liquidCutout";
  let d = {};

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.svgEl = this.element.querySelector("svg");
      if (!this.svgEl)
        return;
      this.svgObjects = this.svgEl.querySelectorAll("rect, circle, path, polygon");
      this.init();
    }
    init() {
      this.onResize = this.onResize.bind(this);
      this.afterResize = window.liquidDebounce(this.afterResize.bind(this), 1);
      this.svgEl.setAttribute("viewBox", `0 0 ${this.$element.parent().outerWidth()} ${this.$element.parent().outerHeight()}`);
      this.events();
    }
    events() {
      i(window).on("resize", this.onResize);
      this.resizeObserver = new ResizeObserver(([t]) => {
        this.onResize();
      });
      this.resizeObserver.observe(this.svgEl);
    }
    onResize() {
      this.changeObjAttr("-");
      this.afterResize();
    }
    afterResize() {
      this.changeObjAttr("+");
    }
    changeObjAttr(t) {
      this.svgObjects.forEach((e) => {
        if (e.hasAttribute("x")) {
          e.setAttribute("x", parseFloat(parseFloat(e.getAttribute("x")) + t + "1"));
        } else if (e.hasAttribute("width")) {
          e.setAttribute("width", parseFloat(parseFloat(e.getAttribute("width")) + t + "1"));
        } else if (e.hasAttribute("cx")) {
          e.setAttribute("cx", parseFloat(parseFloat(e.getAttribute("cx")) + t + "1"));
        } else if (e.hasAttribute("r")) {
          e.setAttribute("r", parseFloat(parseFloat(e.getAttribute("r")) + t + "1"));
        }
      });
      this.svgEl.setAttribute("viewBox", `0 0 ${this.$element.parent().outerWidth()} ${this.$element.parent().outerHeight()}`);
    }
    destroy() {
      i(window).off("resize", this.onResize);
      this.resizeObserver?.unobserve(this.element);
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("cutout-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".lqd-cutout").liquidCutout();
});
((i) => {
  const l = "liquidStackedSections";
  let d = {
    innerElementSelector: ".lqd-stacked-sections-inner",
    itemsSelector: ".lqd-stacked-sections-item",
    contentsSelector: ".lqd-stacked-sections-content",
    imagesSelector: ".lqd-stacked-sections-image",
    navSelector: ".lqd-stacked-sections-nav",
    navButtonsSelector: ".lqd-stacked-sections-nav-button",
    navButtonsProgressSelector: ".lqd-stacked-sections-nav-button-progress"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.innerElement = this.element.querySelector(this.options.innerElementSelector);
      this.items = this.element.querySelectorAll(this.options.itemsSelector);
      this.contents = this.element.querySelectorAll(this.options.contentsSelector);
      this.images = this.element.querySelectorAll(this.options.imagesSelector);
      this.nav = this.element.querySelector(this.options.navSelector);
      this.navButtons = this.nav.querySelectorAll(this.options.navButtonsSelector);
      this.itemsST = [];
      this.mm = gsap.matchMedia();
      this.scrollSmoothEnabled = "liquidLenis" in window;
      this.init();
    }
    init() {
      this.mm.add("(min-width: 1025px)", () => {
        this.initItems();
        this.initInnerElement();
        const t = (e) => {
          const n = e.currentTarget;
          const s = parseInt(n.getAttribute("data-index"), 10);
          const o = this.itemsST.at(s);
          if (!o)
            return;
          const a = o.start + o.start * 0.05;
          if (this.scrollSmoothEnabled) {
            liquidLenis.scrollTo(a, {
              lock: true
            });
          } else {
            i("html, body").animate({
              scrollTop: a
            }, 350);
          }
        };
        this.navButtons.forEach((e) => {
          e.addEventListener("click", t);
        });
        return () => {
          this.navButtons.forEach((e) => e.removeEventListener("click", t));
        };
      });
    }
    initInnerElement() {
      ScrollTrigger.create({
        trigger: this.innerElement,
        endTrigger: this.element,
        pin: true,
        start: `top top+=${document.querySelector("#wpadminbar")?.offsetHeight ?? 0}`,
        end: "bottom bottom",
        onUpdate: (t) => {
          this.element.style.setProperty("--progress", t.progress.toFixed(3) * 100);
        }
      });
    }
    initItems() {
      let t = 0;
      function e(n = [], s = "enter") {
        gsap.to(n, {
          autoAlpha: s === "enter" ? 1 : 0,
          x: s === "enter" ? 0 : -10,
          duration: 1,
          ease: "power3.out",
          stagger: 0.1
        });
      }
      this.items.forEach((n, s) => {
        const o = this.contents[s];
        const a = this.images[s];
        const r = a.querySelector("figure");
        const c = a.querySelector("img");
        const p = o.querySelectorAll("[data-element_type=widget]");
        const g = this.navButtons[s];
        const u = g?.querySelector(this.options.navButtonsProgressSelector);
        const m = gsap.timeline();
        const f = n.offsetHeight;
        m.to(u, {
          scaleX: 1
        });
        m.to(r, {
          y: 0,
          duration: 0.05
        }, 0);
        m.to(c, {
          y: 0,
          scale: 1,
          duration: 0.05
        }, 0);
        const v = ScrollTrigger.create({
          trigger: n,
          pinnedContainer: this.innerElement,
          animation: m,
          start: `top+=${t} top`,
          end: `+=${f}`,
          scrub: true,
          onEnter: () => {
            n.classList.add("active");
            e(p, "enter");
          },
          onLeave: () => {
            if (s === this.items.length - 1)
              return;
            n.classList.remove("active");
            e(p, "leave");
          },
          onEnterBack: () => {
            n.classList.add("active");
            e(p, "enter");
          },
          onLeaveBack: () => {
            if (s === 0)
              return;
            n.classList.remove("active");
            e(p, "leave");
          }
        });
        this.itemsST.push(v);
        t += f;
      });
    }
    destroy() {
      this.mm.revert();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("stacked-sections-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-stacked-sections=true]").liquidStackedSections();
});
(function(i) {
  const l = "liquidVideoThumbnails";
  let d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.init();
    }
    init() {
      this.$element.on("mouseenter", ".products .product.lqd-has-post-video-thumbnail", function() {
        const t = i(this).find("video");
        if (t.attr("autoplay"))
          return;
        t.get(0).play();
      });
      this.$element.on("mouseleave", ".products .product.lqd-has-post-video-thumbnail", function() {
        const t = i(this).find("video");
        if (t.attr("autoplay"))
          return;
        t.get(0).pause();
        t.get(0).load();
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(document).liquidVideoThumbnails();
});
(function(i) {
  const l = "liquidAjaxSearch";
  let d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.init();
    }
    init() {
      const t = this.$element;
      const e = t.parents("[data-ajax-search-module]");
      const n = t.find("[data-search-loader]");
      const s = t.find("[data-search-results]");
      const o = 500;
      let a;
      t.find("input[type=search]").on("input", function() {
        const c = i(this).val().trim();
        clearTimeout(a);
        if (!c.length) {
          e.removeClass("ld-has-results");
          t.removeAttr("data-loading");
          return;
        }
        a = setTimeout(function() {
          r(c);
        }, o);
      });
      const r = (c) => {
        i.ajax({
          url: liquidTheme.uris.ajax,
          method: "POST",
          dataType: "json",
          data: {
            action: "liquid_wc_ajax_search",
            q: c,
            _ajax_nonce: liquidTheme.uris.nonce,
            post_type: t.find('input[name="post_type"]').val(),
            cpt: t.find('input[name="custom_post_type"]').val()
          },
          beforeSend: () => {
            t.attr("data-loading", "");
          },
          success: (p) => {
            e.addClass("ld-has-results");
            s.html(p.data.data);
            if (p.data.info) {
              s.prepend(p.data.info);
            } else {
              s.find(".liquid-ajax-search-results-info").remove();
            }
          },
          complete: () => {
            t.removeAttr("data-loading");
          }
        });
      };
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  setTimeout(() => {
    i("[data-ajax-search]").liquidAjaxSearch();
  }, 3000);
});
((i) => {
  const l = "liquidAjaxFilter";
  const d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.args = this.$element.data("args");
      this.$mainWrapper = this.$element.parents(this.args?.mainClass || ".lqd-lp-grid, .lqd-pf-grid").first();
      this.$sections = this.$mainWrapper.find(this.args?.gridWrapperClass || ".lqd-lp-grid-inner");
      this.$mobileFilterSelect = this.$mainWrapper.find(".lqd-filter-dropdown select");
      this.$primarySection = this.$mainWrapper.find(`${this.args?.gridWrapperClass || ".lqd-lp-grid-inner"}[data-id="0"]`);
      this.init();
    }
    init() {
      if (this.$element.parent().closest(".liquid-filter-items").length > 0)
        return;
      if (!this.$mainWrapper.length)
        return;
      const t = this.$mainWrapper.find("[data-liquid-grid-args]").first();
      this.useGridFilter = t.length > 0;
      this.$gridWithArgs = t;
      if (!this.useGridFilter && !this.$sections.length) {
        return;
      }
      const e = this;
      this.$mobileFilterSelect.on("selectmenuchange", (n, s) => {
        if (e.useGridFilter) {
          e.handleGridFilterClick(s.item.element, s.item.element.attr("data-id"));
          return;
        }
      });
      this.$element.find("li").on("click", function(n) {
        const s = i(this);
        const o = parseInt(s.data("id"), 10) || 0;
        if (s.hasClass("active")) {
          if (n && typeof n.preventDefault === "function")
            n.preventDefault();
          return;
        }
        if (e.useGridFilter) {
          const r = e.$gridWithArgs.data("liquid-grid-args") || {};
          if (r.pagination_mode === "pagination") {
            return;
          }
          if (n && typeof n.preventDefault === "function")
            n.preventDefault();
          e.handleGridFilterClick(s, o);
          if (e.$mobileFilterSelect.length) {
            e.$mobileFilterSelect.find("option").prop("selected", false);
            e.$mobileFilterSelect.find(`option[data-id="${o}"]`).prop("selected", true);
            e.$mobileFilterSelect.trigger("change");
          }
          return;
        }
        const a = e.$sections.filter(function() {
          return i(this).data("id") === o;
        });
        if (!a.length)
          return;
        if (!a.find(".ld-ajax-loadmore").length) {
          const r = e.$primarySection.find(".liquid-pf-nav").clone().hide();
          a.append('<div class="ld-loader mx-auto"></div>');
          r.appendTo(a);
          const c = r.find(".ld-ajax-loadmore");
          const p = c.data("args");
          const g = a.find(e.args?.itemsWrapperClass || ".lqd-lp-row");
          if (typeof p !== "undefined") {
            p.category = o;
          }
          c.data("args", p);
          c.liquidRealAjaxLoadMore({ paged: 1, refreshMasonry: false });
          c.click();
          s.addClass("active").siblings().removeClass("active");
          e.$sections.hide();
          a.show();
          c.on("liquid:success", () => {
            a.find(".ld-loader").remove();
            r.show();
            if (g.get(0).hasAttribute("data-new-liquid-masonry")) {
              g.liquidMasonry();
            }
          });
        } else {
          s.addClass("active").siblings().removeClass("active");
          e.$sections.hide();
          a.show();
        }
      });
    }
    handleGridFilterClick(t, e) {
      const n = this;
      const s = this.$gridWithArgs;
      const o = s.data("liquid-grid-args") || {};
      const a = o.grid_filter_param;
      const r = o.items_wrapper;
      const c = r && i(r).length ? i(r) : s;
      t.addClass("active").siblings().removeClass("active");
      c.css("opacity", 0.4);
      const p = i.extend({}, o, { term_id: e });
      delete p.items_wrapper;
      i.ajax({
        method: "POST",
        url: liquidTheme.uris.ajax,
        dataType: "json",
        data: {
          _ajax_nonce: liquidTheme.uris.nonce,
          action: "liquid_get_ajax_grid",
          args: p,
          paged: 1
        },
        success: (g) => {
          c.css("opacity", "");
          const u = g?.success;
          const m = u ? g.data.html : "";
          const f = u ? !!g.data.last : true;
          const v = c.data("plugin_liquidMasonry");
          const y = v?.isoData;
          console.log(u, y);
          if (y) {
            y.remove(y.getItemElements());
            y.layout();
            if (m) {
              const b = i(m);
              c.css("height", "");
              c.append(b);
              y.appended(b);
              imagesLoaded(b.get(), () => {
                y.arrange();
              });
            }
          } else {
            c.html(m);
          }
          console.log(c);
          const w = n.$mainWrapper.find(".ld-ajax-loadmore");
          if (w.length) {
            const b = w.attr("data-args");
            let q = {};
            try {
              q = b ? JSON.parse(b) : {};
            } catch (x) {
              q = w.data("args") || {};
            }
            q.term_id = e;
            w.attr("data-args", JSON.stringify(q));
            w.data("args", q);
            const C = w.data("plugin_liquidRealAjaxLoadMore");
            if (C) {
              C.paged = 2;
              C.allLoaded = !!f;
              C.args = q;
            }
            w.removeClass("items-loading all-items-loaded");
            if (f) {
              w.addClass("all-items-loaded");
            }
          }
          if (a && typeof window.history?.pushState === "function") {
            try {
              const b = new URL(window.location.href);
              if (e > 0) {
                b.searchParams.set(a, e);
              } else {
                b.searchParams.delete(a);
              }
              window.history.pushState({}, "", b.toString());
            } catch (b) {}
          }
          i(document).trigger("liquid:filter:change", { termId: e, args: p, success: u });
        },
        error: () => {
          c.css("opacity", "");
        }
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready((i) => {
  i(".liquid-filter-items").liquidAjaxFilter();
});
(function(i) {
  const l = "liquidRealAjaxLoadMore";
  let d = {
    paged: 2,
    reloadMasonry: true
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.args = this.$element.data("args");
      this.useGridFilter = this.args && this.args.action === "liquid_get_ajax_grid";
      this.mainWrapper = this.$element.parents(this.$element.data("main-wrapper") || ".lqd-lp-grid");
      this.$wrapper = this.$element.parents(this.$element.data("wrapper") || ".lqd-lp-grid-inner");
      const n = this.$element.data("items-wrapper");
      if (this.useGridFilter && n) {
        this.$itemsWrapper = i(n).first();
      } else {
        this.$itemsWrapper = this.$wrapper.find(n || ".lqd-lp-row");
      }
      this.paged = this.options.paged;
      this.allLoaded = false;
      this.init();
    }
    init() {
      const t = this;
      if (this.$element.data("ajaxify")) {
        return;
      }
      this.$element.on("click", function() {
        if (t.allLoaded)
          return;
        let e = t.args;
        if (t.useGridFilter) {
          const n = t.$element.attr("data-args");
          if (n) {
            try {
              e = JSON.parse(n);
            } catch (s) {}
          } else {
            e = t.$element.data("args") || t.args;
          }
          t.args = e;
        }
        i.ajax({
          method: "POST",
          url: liquidTheme.uris.ajax,
          dataType: "json",
          data: {
            _ajax_nonce: liquidTheme.uris.nonce,
            action: e.action,
            args: e,
            paged: t.paged
          },
          beforeSend: function() {
            t.$element.prop("disabled", true);
            t.$element.addClass("items-loading");
          },
          success: function(n) {
            if (!n.success) {
              t.$element.removeClass("items-loading").addClass("all-items-loaded");
              t.allLoaded = true;
              return;
            }
            const s = i(n.data.html);
            imagesLoaded(s, () => {
              s.appendTo(t.$itemsWrapper);
              t.$element.trigger("liquid:success", [s]);
              if (t.$itemsWrapper.length && (t.$itemsWrapper.get(0).hasAttribute("data-liquid-masonry") || t.$itemsWrapper.get(0).hasAttribute("data-new-liquid-masonry"))) {
                const o = t.$itemsWrapper.data("plugin_liquidMasonry");
                const a = o && o.isoData;
                if (t.options.reloadMasonry) {
                  a && a.appended(s);
                }
              }
              t.onSuccess(t.$itemsWrapper);
            });
            t.$element.prop("disabled", false);
            t.paged++;
            if (n.data.last) {
              t.$element.removeClass("items-loading").addClass("all-items-loaded");
              t.allLoaded = true;
            }
          },
          complete: function() {
            t.$element.removeClass("items-loading");
          }
        });
      });
    }
    onSuccess(t) {
      if (!i("body").hasClass("lazyload-enabled")) {
        i("[data-responsive-bg=true]", t).liquidResponsiveBG();
      }
      if (i("body").hasClass("lazyload-enabled")) {
        window.liquidLazyload = new LazyLoad({
          elements_selector: ".ld-lazyload",
          callback_loaded: (e) => {
            i(e).closest("[data-responsive-bg=true]").liquidResponsiveBG();
            i(e).parent().not("#wrap, #lqd-site-content").addClass("loaded");
          }
        });
      }
      i("[data-split-text]", t).filter((e, n) => !i(n).parents("[data-custom-animations]").length && !n.hasAttribute("data-custom-animations")).liquidSplitText();
      i("[data-fittext]", t).liquidFitText();
      i("[data-custom-animations]", t).map((e, n) => {
        const s = i(n);
        const o = s.parents(".wpb_wrapper[data-custom-animations]");
        if (o.length) {
          s.removeAttr("data-custom-animations");
          s.removeAttr("data-ca-options");
        }
      });
      i("[data-custom-animations]", t).filter((e, n) => {
        const s = i(n);
        const o = s.closest(".vc_row[data-row-bg]");
        const a = s.closest(".vc_row[data-slideshow-bg]");
        return !o.length && !a.length;
      }).liquidCustomAnimations();
      i("[data-lqd-flickity]", t).liquidCarousel();
      i("[data-parallax]", t).liquidParallax();
      i("[data-hover3d=true]", t).liquidHover3d();
      this.$element.removeClass("items-loading");
      ScrollTrigger?.refresh();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".ld-ajax-loadmore").liquidRealAjaxLoadMore();
});
(function(i) {
  const l = "liquidStories";
  let d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.$items = this.$element.find(".lqd-stories-item");
      this.init();
    }
    init() {
      this.$items.each(function() {
        const t = i(this);
        const e = t.data("id");
        const n = localStorage.getItem("lqd-visited-stories") ? JSON.parse(localStorage.getItem("lqd-visited-stories")).includes(e) : false;
        if (n) {
          t.addClass("lqd-stories-item--visited");
        }
      });
      this.$items.on("click", function() {
        const t = i(this);
        const e = t.data("id");
        const n = localStorage.getItem("lqd-visited-stories") ? JSON.parse(localStorage.getItem("lqd-visited-stories")) : [];
        if (!n.includes(e)) {
          n.push(e);
          localStorage.setItem("lqd-visited-stories", JSON.stringify(n));
        }
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".lqd-stories").liquidStories();
});
(function(i) {
  const l = "liquidPromoBanner";
  let d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.isEdit = "elementorFrontend" in window && elementorFrontend.isEditMode();
      this.element = t;
      this.$element = i(t);
      this.args = this.$element.data("lqd-pb-args");
      this.cookieValue = Cookies.get(`lqd-pb-${this.args.id}`);
      this.cookieVersion = this.cookieValue ? this.cookieValue * 1 : 0;
      this.id = this.$element.data("lqd-pb");
      this.firstOpen = false;
      this.checkAndGenerate();
      this.$modal = i(".lqd-promo-banner");
      this.$backdrop = i(".lqd-promo-banner-backdrop");
      this.$content = this.$modal.find(".lqd-promo-banner__content");
      this.$closeBtn = this.$modal.find("[data-lqd-pb-close]");
      this.init();
    }
    checkAndGenerate() {
      i("body").find(".lqd-promo-banner-backdrop, .lqd-promo-banner").remove();
      i("body").append('<div class="lqd-promo-banner-backdrop"></div>');
      const t = i('<div class="lqd-promo-banner"><div class="lqd-promo-banner__dialog"><div class="lqd-promo-banner__content"></div></div></div>');
      const e = i('<button type="button" data-lqd-pb-close class="d-flex align-items-center justify-content-center lqd-promo-banner__close"><span class="lqd-icn-ess icon-ion-ios-close"></span></button>');
      e.addClass("lqd-promo-banner__close--" + this.args.close_button_position);
      e.appendTo(t.find(".lqd-promo-banner__content"));
      t.appendTo("body");
    }
    init() {
      const t = this;
      i(`[data-lqd-pb-open="${this.id}"]`).on("click", () => {
        this.open();
      });
      if (!this.isEdit) {
        this.open();
      }
      this.$closeBtn.on("click", function() {
        t.close();
      });
      this.$content.find(".lqd-promo-banner-closer").on("click", function() {
        t.close();
      });
      this.$modal.click(function(e) {
        if (e.target !== this)
          return;
        t.close();
      });
    }
    open() {
      if (this.args.enable_cookies && this.cookieVersion >= this.args.cookie_version && !this.isEdit) {
        return;
      }
      this.$element.appendTo(this.$content);
      this.$modal.show();
      this.$element.show();
      this.$backdrop.show();
      if (!this.firstOpen) {
        this.firstOpen = true;
        this.firstOpenEvent();
      }
    }
    close() {
      this.$element.hide();
      this.$modal.hide();
      this.$backdrop.hide();
      if (this.args.enable_cookies && this.args.cookie_version * 1 > this.cookieVersion) {
        Cookies.set(`lqd-pb-${this.args.id}`, this.args.cookie_version, { expires: this.args.expire * 1 });
      }
    }
    firstOpenEvent() {
      window.liquidLazyload.update();
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i("[data-lqd-pb]").liquidPromoBanner();
});
(function(i) {
  const l = "liquidHorizontalScroll";
  let d = {
    propertyName: "value"
  };

  class h {
    constructor(t, e) {
      this._defaults = d;
      this._name = l;
      this.options = { ...d, ...e };
      this.element = t;
      this.$element = i(t);
      this.scrollTriggerInstance = null;
      this.init();
    }
    init() {
      this.load();
    }
    load() {
      const t = this.$element;
      let e = t.children(".e-con-inner");
      if (!e.length)
        e = t.children().wrapAll('<div class="lqd-horizontal-scroll__virtual-wrapper"></div>').parent();
      let n = 0;
      if (i("#wpadminbar").length)
        n = i("#wpadminbar").height();
      const s = () => {
        const o = window.pageYOffset;
        window.scrollTo(0, 0);
        requestAnimationFrame(() => {
          ScrollTrigger.refresh();
          const a = e.children().last();
          const r = a.position().left + a.outerWidth() - t.width();
          gsap.to(e, {
            x: -r,
            ease: "none",
            scrollTrigger: {
              scrub: 1,
              trigger: t,
              pin: t,
              start: `top top+=${n}`,
              end: `+=${r}`
            }
          });
          requestAnimationFrame(() => {
            window.scrollTo(0, o);
            ScrollTrigger.refresh();
          });
        });
      };
      i(window).on("load", () => {
        setTimeout(s, 150);
      });
    }
  }
  i.fn[l] = function(t) {
    return this.each(function() {
      const e = { ...i(this).data("plugin-options"), ...t };
      if (!i.data(this, "plugin_" + l)) {
        i.data(this, "plugin_" + l, new h(this, e));
      }
    });
  };
})(jQuery);
jQuery(document).ready(function(i) {
  i(".lqd-horizontal-scroll").liquidHorizontalScroll();
});

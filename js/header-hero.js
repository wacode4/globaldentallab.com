/**
 * Global Dental Lab - Shared Header & Hero Component
 *
 * Usage:
 * <script src="js/header-hero.js"></script>
 * <script>
 *   GlobalDentalLab.init({
 *     heroType: 'slider', // 'slider' | 'static' | 'short'
 *     slides: [
 *       { image: 'url', title: 'Title', subtitle: 'Subtitle' }
 *     ],
 *     // OR for static/short hero:
 *     heroImage: 'url',
 *     heroTitle: 'Page Title',
 *     heroSubtitle: 'Page Subtitle',
 *     heroCTAs: [
 *       { text: 'Contact Us', href: 'contact.html', style: 'primary' },
 *       { text: 'Services', href: 'services.html', style: 'secondary' }
 *     ],
 *     showTrustBadges: true // Show CE, ISO, FDA badges in hero
 *   });
 * </script>
 */

const GlobalDentalLab = {
  config: {
    heroType: "slider",
    slides: [
      {
        image:
          "https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=1920&q=80",
        title: "Global<br>Dental Lab",
        subtitle: "The most personalized<br>Dental Lab in Asia-Pacific",
      },
      {
        image:
          "https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=1920&q=80",
        title: "Global<br>Dental Lab",
        subtitle: "The most personalized<br>Dental Lab in Asia-Pacific",
      },
      {
        image:
          "https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=1920&q=80",
        title: "Global<br>Dental Lab",
        subtitle: "The most personalized<br>Dental Lab in Asia-Pacific",
      },
    ],
    heroImage: "",
    heroTitle: "",
    heroSubtitle: "",
    heroLabel: "Premium Dental Laboratory",
    heroCTAs: [
      { text: "CONTACT US", href: "contact.html", style: "white" },
      { text: "LAB SERVICES", href: "services.html", style: "primary" },
    ],
    showTrustBadges: true,
    showSliderDots: true,
    currentPage: "home", // home, services, about, technology, contact, product, category
  },

  init(options = {}) {
    this.config = { ...this.config, ...options };
    this.renderHeader();
    this.renderHero();
    this.initSlider();
    this.initHeaderScroll();
    this.initMobileMenu();
    this.initScrollAnimations();
  },

  renderHeader() {
    const headerHTML = `
      <header id="main-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" data-transparent="true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="index.html" class="flex items-center gap-3 cursor-pointer flex-shrink-0">
              <div class="w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 logo-bg">
                <svg class="w-7 h-7 text-white logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
              </div>
              <div class="hidden md:block">
                <span class="text-lg font-bold text-white header-text whitespace-nowrap font-heading" style="font-family: 'Open Sans Condensed', 'Open Sans', sans-serif;">Global Dental Lab</span>
                <span class="block text-[10px] text-white/60 header-subtext whitespace-nowrap tracking-wide">Excellence in Dental Restorations</span>
              </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-1">
              ${this.renderNavItems()}
            </nav>

            <!-- CTA Buttons -->
            <div class="hidden lg:flex items-center gap-3">
              <a href="tel:+85291424923" class="flex items-center gap-2 px-4 py-2 border border-white/30 rounded text-[13px] text-white font-semibold hover:bg-white/10 transition-colors duration-200 cursor-pointer header-btn whitespace-nowrap">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                +852 9142 4923
              </a>
              <a href="contact.html" class="flex items-center gap-2 px-5 py-2 bg-white text-navy rounded text-[13px] font-bold hover:bg-gray-100 transition-colors duration-200 cursor-pointer whitespace-nowrap uppercase header-cta">
                Get Started
              </a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="lg:hidden p-2 text-white cursor-pointer" aria-label="Open menu">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white/95 backdrop-blur-md">
          <div class="px-4 py-4 space-y-1">
            <a href="index.html" class="block py-3 px-3 text-navy font-semibold rounded hover:bg-gray-100 cursor-pointer">Home</a>
            <div class="border-t border-gray-200 my-2"></div>
            <p class="px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Lab Services</p>
            <a href="services.html#crowns" class="block py-2 px-3 text-navy hover:text-primary hover:bg-gray-100 rounded cursor-pointer">Crown & Bridge</a>
            <a href="services.html#ceramics" class="block py-2 px-3 text-navy hover:text-primary hover:bg-gray-100 rounded cursor-pointer">All Ceramics & Zirconia</a>
            <a href="services.html#implants" class="block py-2 px-3 text-navy hover:text-primary hover:bg-gray-100 rounded cursor-pointer">Implant Restorations</a>
            <a href="services.html#removable" class="block py-2 px-3 text-navy hover:text-primary hover:bg-gray-100 rounded cursor-pointer">Removable Prosthetics</a>
            <a href="services.html#orthodontics" class="block py-2 px-3 text-navy hover:text-primary hover:bg-gray-100 rounded cursor-pointer">Orthodontic Appliances</a>
            <div class="border-t border-gray-200 my-2"></div>
            <a href="about.html" class="block py-3 px-3 text-navy font-semibold rounded hover:bg-gray-100 cursor-pointer">About Us</a>
            <a href="technology.html" class="block py-3 px-3 text-navy font-semibold rounded hover:bg-gray-100 cursor-pointer">Technology</a>
            <a href="contact.html" class="block py-3 px-3 text-navy font-semibold rounded hover:bg-gray-100 cursor-pointer">Contact</a>
            <div class="pt-4 border-t border-gray-200 mt-4">
              <a href="tel:+85291424923" class="flex items-center justify-center gap-2 text-navy py-3 cursor-pointer">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                +852 9142 4923
              </a>
              <a href="contact.html" class="block mt-3 bg-primary text-white text-center py-3 rounded font-semibold cursor-pointer">
                Get Started
              </a>
            </div>
          </div>
        </div>
      </header>
    `;

    const container = document.getElementById("header-container");
    if (container) {
      container.innerHTML = headerHTML;
    } else {
      document.body.insertAdjacentHTML("afterbegin", headerHTML);
    }
  },

  renderNavItems() {
    const navItems = [
      {
        label: "Lab Services",
        href: "services.html",
        hasDropdown: true,
        dropdownItems: [
          { label: "Crown & Bridge", href: "services.html#crowns" },
          { label: "All Ceramics & Zirconia", href: "services.html#ceramics" },
          { label: "Implant Restorations", href: "services.html#implants" },
          { label: "Removable Prosthetics", href: "services.html#removable" },
          {
            label: "Orthodontic Appliances",
            href: "services.html#orthodontics",
          },
          { label: "Veneers & Inlays", href: "services.html#veneers" },
        ],
      },
      {
        label: "About",
        href: "about.html",
        hasDropdown: true,
        dropdownItems: [
          { label: "Our Story", href: "about.html" },
          { label: "Our Team", href: "about.html#team" },
          { label: "Technology", href: "technology.html" },
        ],
      },
      {
        label: "Technology",
        href: "technology.html",
        hasDropdown: false,
      },
      {
        label: "Contact",
        href: "contact.html",
        hasDropdown: true,
        dropdownItems: [
          { label: "Contact Us", href: "contact.html" },
          { label: "+852 9142 4923", href: "tel:+85291424923" },
          { label: "WhatsApp", href: "https://wa.me/85291424923" },
        ],
      },
    ];

    return navItems
      .map((item) => {
        if (item.hasDropdown) {
          return `
          <div class="relative group">
            <a href="${item.href}" class="nav-link px-4 py-3 text-[13px] tracking-wide text-white font-semibold hover:text-white transition-colors duration-200 cursor-pointer flex items-center gap-1 header-link whitespace-nowrap uppercase">
              ${item.label}
              <svg class="w-3 h-3 flex-shrink-0 opacity-60 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
              </svg>
            </a>
            <div class="absolute top-full ${item.label === "Contact" ? "right-0" : "left-0"} pt-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
              <div class="bg-white rounded-lg shadow-2xl py-3 min-w-[220px]">
                ${item.dropdownItems
                  .map(
                    (sub) => `
                  <a href="${sub.href}" class="block px-5 py-3 hover:bg-gray-50 text-navy hover:text-primary cursor-pointer transition-colors duration-200">${sub.label}</a>
                `,
                  )
                  .join("")}
              </div>
            </div>
          </div>
        `;
        } else {
          return `
          <a href="${item.href}" class="nav-link px-4 py-3 text-[13px] tracking-wide text-white font-semibold hover:text-white transition-colors duration-200 cursor-pointer header-link whitespace-nowrap uppercase">${item.label}</a>
        `;
        }
      })
      .join("");
  },

  renderHero() {
    let heroHTML = "";

    if (this.config.heroType === "slider") {
      heroHTML = this.renderSliderHero();
    } else if (this.config.heroType === "static") {
      heroHTML = this.renderStaticHero(true); // full height
    } else if (this.config.heroType === "short") {
      heroHTML = this.renderStaticHero(false); // short banner
    }

    const container = document.getElementById("hero-container");
    if (container) {
      container.innerHTML = heroHTML;
    }
  },

  renderSliderHero() {
    const slides = this.config.slides;
    const slidesHTML = slides
      .map(
        (slide, index) => `
      <div class="slide absolute inset-0 ${index === 0 ? "opacity-100" : "opacity-0"} transition-opacity duration-1000" data-slide="${index}">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('${slide.image}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-navy/80 via-navy/50 to-transparent"></div>
      </div>
    `,
      )
      .join("");

    const dotsHTML = this.config.showSliderDots
      ? `
      <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 flex gap-3">
        ${slides
          .map(
            (_, index) => `
          <button class="slider-dot w-3 h-3 rounded-full ${index === 0 ? "bg-white active" : "bg-white/50"} hover:bg-white transition-all duration-200 cursor-pointer" data-slide="${index}" aria-label="Slide ${index + 1}"></button>
        `,
          )
          .join("")}
      </div>
    `
      : "";

    return `
      <section class="relative min-h-screen">
        <div id="hero-slider" class="absolute inset-0 z-0">
          ${slidesHTML}
        </div>

        ${this.renderHeroContent(slides[0].title, slides[0].subtitle, true)}
        ${dotsHTML}
      </section>
    `;
  },

  renderStaticHero(fullHeight) {
    const heightClass = fullHeight ? "min-h-screen" : "min-h-[60vh]";
    const image = this.config.heroImage;
    const title = this.config.heroTitle;
    const subtitle = this.config.heroSubtitle;

    return `
      <section class="relative ${heightClass}">
        <div class="absolute inset-0 z-0">
          <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('${image}');"></div>
          <div class="absolute inset-0 bg-gradient-to-r from-navy/80 via-navy/50 to-transparent"></div>
        </div>

        ${this.renderHeroContent(title, subtitle, fullHeight)}
      </section>
    `;
  },

  renderHeroContent(title, subtitle, fullHeight) {
    const paddingClass = fullHeight ? "py-32" : "py-24 pt-32";
    const titleSizeClass = fullHeight
      ? "text-5xl md:text-6xl lg:text-7xl xl:text-8xl"
      : "text-4xl md:text-5xl lg:text-6xl";

    const ctasHTML = this.config.heroCTAs
      .map((cta) => {
        const styleClass =
          cta.style === "white"
            ? "bg-white text-navy shadow-lg hover:shadow-xl"
            : "bg-primary hover:bg-primary-dark text-white shadow-lg";
        return `
        <a href="${cta.href}" class="btn-primary inline-flex items-center justify-center ${styleClass} px-8 py-4 rounded font-bold text-lg cursor-pointer">
          ${cta.text}
          <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      `;
      })
      .join("");

    const trustBadgesHTML = this.config.showTrustBadges
      ? `
      <div class="mt-12 flex flex-wrap items-center gap-6 fade-up fade-up-delay-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-white/80 text-sm font-medium">CE Certified</span>
        </div>
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-white/80 text-sm font-medium">ISO 13485</span>
        </div>
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-accent" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-white/80 text-sm font-medium">FDA Approved</span>
        </div>
      </div>
    `
      : "";

    return `
      <div class="relative z-10 ${fullHeight ? "min-h-screen" : ""} flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ${paddingClass}">
          <div class="max-w-3xl">
            ${this.config.heroLabel ? `<p class="text-accent font-semibold tracking-[0.2em] uppercase text-sm mb-4 fade-up">${this.config.heroLabel}</p>` : ""}
            <h1 class="hero-title ${titleSizeClass} font-bold text-white leading-[1.1] mb-6 fade-up fade-up-delay-1 font-heading" style="font-family: 'Open Sans Condensed', 'Open Sans', sans-serif;">
              ${title}
            </h1>
            <p class="hero-subtitle text-xl md:text-2xl lg:text-3xl text-white/90 mb-10 leading-relaxed font-light max-w-xl fade-up fade-up-delay-2">
              ${subtitle}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 fade-up fade-up-delay-3">
              ${ctasHTML}
            </div>
            ${trustBadgesHTML}
          </div>
        </div>
      </div>
    `;
  },

  initSlider() {
    if (this.config.heroType !== "slider") return;

    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".slider-dot");
    let currentSlide = 0;
    const totalSlides = slides.length;

    const showSlide = (index) => {
      slides.forEach((slide, i) => {
        slide.style.opacity = i === index ? "1" : "0";
      });
      dots.forEach((dot, i) => {
        dot.classList.toggle("active", i === index);
        dot.classList.toggle("bg-white", i === index);
        dot.classList.toggle("bg-white/50", i !== index);
      });
      currentSlide = index;
    };

    // Auto-advance slider
    setInterval(() => {
      showSlide((currentSlide + 1) % totalSlides);
    }, 5000);

    // Click on dots
    dots.forEach((dot, i) => {
      dot.addEventListener("click", () => showSlide(i));
    });
  },

  initHeaderScroll() {
    const header = document.getElementById("main-header");
    const mobileMenuBtn = document.getElementById("mobile-menu-btn");

    const updateHeader = () => {
      const scrollY = window.scrollY;

      if (scrollY > 100) {
        header.classList.add("bg-white", "shadow-md");
        header.classList.remove("bg-transparent");

        // Update text colors for white background
        document.querySelectorAll(".header-text").forEach((el) => {
          el.classList.remove("text-white");
          el.classList.add("text-navy");
        });
        document.querySelectorAll(".header-subtext").forEach((el) => {
          el.classList.remove("text-white/60");
          el.classList.add("text-gray-500");
        });
        document.querySelectorAll(".header-link").forEach((el) => {
          el.classList.remove("text-white");
          el.classList.add("text-navy");
        });
        document.querySelectorAll(".header-btn").forEach((el) => {
          el.classList.remove(
            "border-white/30",
            "text-white",
            "hover:bg-white/10",
          );
          el.classList.add("border-gray-300", "text-navy", "hover:bg-gray-100");
        });
        document.querySelectorAll(".header-cta").forEach((el) => {
          el.classList.remove("bg-white", "text-navy");
          el.classList.add("bg-primary", "text-white");
        });

        // Update logo
        const logoBg = document.querySelector(".logo-bg");
        if (logoBg) {
          logoBg.classList.remove("bg-white/10", "border-white/20");
          logoBg.classList.add("bg-primary", "border-primary");
        }

        if (mobileMenuBtn) {
          mobileMenuBtn.classList.remove("text-white");
          mobileMenuBtn.classList.add("text-navy");
        }
      } else {
        header.classList.remove("bg-white", "shadow-md");
        header.classList.add("bg-transparent");

        // Restore transparent header colors
        document.querySelectorAll(".header-text").forEach((el) => {
          el.classList.add("text-white");
          el.classList.remove("text-navy");
        });
        document.querySelectorAll(".header-subtext").forEach((el) => {
          el.classList.add("text-white/60");
          el.classList.remove("text-gray-500");
        });
        document.querySelectorAll(".header-link").forEach((el) => {
          el.classList.add("text-white");
          el.classList.remove("text-navy");
        });
        document.querySelectorAll(".header-btn").forEach((el) => {
          el.classList.add(
            "border-white/30",
            "text-white",
            "hover:bg-white/10",
          );
          el.classList.remove(
            "border-gray-300",
            "text-navy",
            "hover:bg-gray-100",
          );
        });
        document.querySelectorAll(".header-cta").forEach((el) => {
          el.classList.add("bg-white", "text-navy");
          el.classList.remove("bg-primary", "text-white");
        });

        // Update logo
        const logoBg = document.querySelector(".logo-bg");
        if (logoBg) {
          logoBg.classList.add("bg-white/10", "border-white/20");
          logoBg.classList.remove("bg-primary", "border-primary");
        }

        if (mobileMenuBtn) {
          mobileMenuBtn.classList.add("text-white");
          mobileMenuBtn.classList.remove("text-navy");
        }
      }
    };

    window.addEventListener("scroll", updateHeader);
    updateHeader();
  },

  initMobileMenu() {
    const mobileMenuBtn = document.getElementById("mobile-menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");

    if (mobileMenuBtn && mobileMenu) {
      mobileMenuBtn.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
      });
    }
  },

  initScrollAnimations() {
    const observerOptions = {
      root: null,
      rootMargin: "0px",
      threshold: 0.1,
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
        }
      });
    }, observerOptions);

    document.querySelectorAll(".fade-up").forEach((el) => {
      observer.observe(el);
    });

    // Add visible class to hero elements immediately
    setTimeout(() => {
      document.querySelectorAll("section .fade-up").forEach((el) => {
        if (
          el.closest("section")?.classList.contains("min-h-screen") ||
          el.closest("section")?.classList.contains("min-h-[60vh]")
        ) {
          el.classList.add("visible");
        }
      });
    }, 100);
  },
};

// Auto-initialize if data attributes are present
document.addEventListener("DOMContentLoaded", () => {
  const heroContainer = document.getElementById("hero-container");
  if (heroContainer && heroContainer.dataset.autoInit !== "false") {
    // Check for data attributes for configuration
    const config = {};
    if (heroContainer.dataset.heroType)
      config.heroType = heroContainer.dataset.heroType;
    if (heroContainer.dataset.heroImage)
      config.heroImage = heroContainer.dataset.heroImage;
    if (heroContainer.dataset.heroTitle)
      config.heroTitle = heroContainer.dataset.heroTitle;
    if (heroContainer.dataset.heroSubtitle)
      config.heroSubtitle = heroContainer.dataset.heroSubtitle;
    if (heroContainer.dataset.heroLabel)
      config.heroLabel = heroContainer.dataset.heroLabel;

    if (Object.keys(config).length > 0) {
      GlobalDentalLab.init(config);
    }
  }
});

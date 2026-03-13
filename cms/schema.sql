CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cms_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value MEDIUMTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) NULL,
    clinic VARCHAR(120) NULL,
    service VARCHAR(80) NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_inquiries_created_at (created_at),
    KEY idx_inquiries_email (email),
    KEY idx_inquiries_status (status)
);

CREATE TABLE IF NOT EXISTS languages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(64) NOT NULL,
    native_name VARCHAR(64) NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 100
);

CREATE TABLE IF NOT EXISTS site_setting_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    setting_value MEDIUMTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_site_setting_language (setting_key, language_id),
    CONSTRAINT fk_site_setting_translations_language FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(120) NOT NULL UNIQUE,
    page_type VARCHAR(40) NOT NULL DEFAULT 'page',
    template_key VARCHAR(80) NOT NULL DEFAULT 'default',
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    sort_order INT NOT NULL DEFAULT 100,
    show_in_nav TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_key VARCHAR(80) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_id INT UNSIGNED NOT NULL,
    page_id INT UNSIGNED NULL,
    custom_label VARCHAR(150) NULL,
    custom_url VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 100,
    target VARCHAR(20) NOT NULL DEFAULT '_self',
    is_enabled TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_menu_items_menu FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
    CONSTRAINT fk_menu_items_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE SET NULL,
    KEY idx_menu_items_menu_sort (menu_id, sort_order)
);

CREATE TABLE IF NOT EXISTS menu_item_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    custom_label VARCHAR(150) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_menu_item_language (menu_item_id, language_id),
    CONSTRAINT fk_menu_item_translations_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_menu_item_translations_language FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS page_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    page_name VARCHAR(150) NOT NULL,
    nav_label VARCHAR(120) NULL,
    seo_title VARCHAR(255) NULL,
    seo_description TEXT NULL,
    UNIQUE KEY uniq_page_language (page_id, language_id),
    CONSTRAINT fk_page_translations_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    CONSTRAINT fk_page_translations_language FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS modules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_key VARCHAR(120) NOT NULL UNIQUE,
    module_type VARCHAR(60) NOT NULL,
    variant VARCHAR(60) NOT NULL DEFAULT 'default',
    status VARCHAR(20) NOT NULL DEFAULT 'published',
    settings_json LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS module_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NULL,
    kicker VARCHAR(120) NULL,
    subtitle TEXT NULL,
    content_html MEDIUMTEXT NULL,
    content_json LONGTEXT NULL,
    UNIQUE KEY uniq_module_language (module_id, language_id),
    CONSTRAINT fk_module_translations_module FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    CONSTRAINT fk_module_translations_language FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS page_modules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    module_id INT UNSIGNED NOT NULL,
    region_name VARCHAR(60) NOT NULL DEFAULT 'main',
    sort_order INT NOT NULL DEFAULT 100,
    is_enabled TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_page_modules_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    CONSTRAINT fk_page_modules_module FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    KEY idx_page_modules_page_sort (page_id, sort_order)
);

CREATE TABLE IF NOT EXISTS product_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(120) NOT NULL UNIQUE,
    page_slug VARCHAR(120) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    sort_order INT NOT NULL DEFAULT 100,
    image_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_product_categories_sort (sort_order, id)
);

CREATE TABLE IF NOT EXISTS product_category_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    nav_label VARCHAR(150) NULL,
    summary TEXT NULL,
    content_html MEDIUMTEXT NULL,
    content_json MEDIUMTEXT NULL,
    seo_title VARCHAR(255) NULL,
    seo_description TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_product_category_language (category_id, language_id),
    CONSTRAINT fk_product_category_translations_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE CASCADE,
    CONSTRAINT fk_product_category_translations_language FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    page_slug VARCHAR(120) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    sort_order INT NOT NULL DEFAULT 100,
    image_path VARCHAR(255) NULL,
    badge VARCHAR(120) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    KEY idx_products_category_sort (category_id, sort_order, id)
);

CREATE TABLE IF NOT EXISTS product_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    nav_label VARCHAR(150) NULL,
    short_description TEXT NULL,
    content_html MEDIUMTEXT NULL,
    content_json MEDIUMTEXT NULL,
    seo_title VARCHAR(255) NULL,
    seo_description TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_product_language (product_id, language_id),
    CONSTRAINT fk_product_translations_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_product_translations_language FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
);

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

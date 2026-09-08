SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS page_sections;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS blog_posts;
DROP TABLE IF EXISTS blog_categories;
DROP TABLE IF EXISTS contact_enquiries;
DROP TABLE IF EXISTS rfq_status_history;
DROP TABLE IF EXISTS rfq_notes;
DROP TABLE IF EXISTS rfqs;
DROP TABLE IF EXISTS product_industries;
DROP TABLE IF EXISTS industries;
DROP TABLE IF EXISTS product_documents;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    username VARCHAR(80) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('SUPER_ADMIN','ADMIN','SALES','CONTENT_MANAGER') NOT NULL DEFAULT 'CONTENT_MANAGER',
    phone VARCHAR(30) NULL,
    avatar VARCHAR(255) NULL,
    status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    last_login_at DATETIME NULL,
    password_changed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_username (username),
    KEY idx_users_role_status (role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_group VARCHAR(80) NOT NULL DEFAULT 'general',
    setting_key VARCHAR(120) NOT NULL,
    setting_value LONGTEXT NULL,
    field_type VARCHAR(40) NOT NULL DEFAULT 'text',
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_settings_key (setting_key),
    KEY idx_settings_group (setting_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    short_description VARCHAR(500) NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    icon VARCHAR(100) NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    seo_title VARCHAR(190) NULL,
    seo_description VARCHAR(320) NULL,
    canonical_url VARCHAR(255) NULL,
    og_image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_categories_slug (slug),
    KEY idx_product_categories_status_sort (status, sort_order),
    CONSTRAINT fk_product_categories_parent FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(190) NOT NULL,
    slug VARCHAR(210) NOT NULL,
    product_code VARCHAR(80) NULL,
    main_image VARCHAR(255) NULL,
    short_description VARCHAR(600) NULL,
    full_description LONGTEXT NULL,
    features LONGTEXT NULL,
    benefits LONGTEXT NULL,
    applications LONGTEXT NULL,
    technical_information LONGTEXT NULL,
    packaging_information LONGTEXT NULL,
    official_url VARCHAR(500) NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    seo_title VARCHAR(190) NULL,
    seo_description VARCHAR(320) NULL,
    canonical_url VARCHAR(255) NULL,
    og_image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_slug (slug),
    UNIQUE KEY uq_products_code (product_code),
    KEY idx_products_category_status (category_id, status),
    KEY idx_products_featured_status (featured, status),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(190) NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_product_images_product_sort (product_id, sort_order),
    CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    document_type ENUM('TDS','SDS','BROCHURE','OTHER') NOT NULL,
    title VARCHAR(190) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_product_documents_product (product_id),
    CONSTRAINT fk_product_documents_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE industries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    thumbnail VARCHAR(255) NULL,
    banner VARCHAR(255) NULL,
    short_description VARCHAR(600) NULL,
    detailed_description LONGTEXT NULL,
    applications LONGTEXT NULL,
    challenges LONGTEXT NULL,
    solutions LONGTEXT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    seo_title VARCHAR(190) NULL,
    seo_description VARCHAR(320) NULL,
    canonical_url VARCHAR(255) NULL,
    og_image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_industries_slug (slug),
    KEY idx_industries_status_sort (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_industries (
    product_id BIGINT UNSIGNED NOT NULL,
    industry_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id, industry_id),
    KEY idx_product_industries_industry (industry_id),
    CONSTRAINT fk_product_industries_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_product_industries_industry FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rfqs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(30) NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    company_name VARCHAR(160) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    whatsapp VARCHAR(30) NULL,
    email VARCHAR(190) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NULL,
    product_name VARCHAR(190) NULL,
    quantity_requirement VARCHAR(120) NULL,
    application VARCHAR(255) NULL,
    message TEXT NULL,
    preferred_contact_method ENUM('PHONE','WHATSAPP','EMAIL') NOT NULL DEFAULT 'PHONE',
    status ENUM('NEW','CONTACTED','REQUIREMENT_VERIFIED','QUOTED','FOLLOW_UP','NEGOTIATION','CONVERTED','CLOSED','REJECTED') NOT NULL DEFAULT 'NEW',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    assigned_user_id BIGINT UNSIGNED NULL,
    source_url VARCHAR(500) NULL,
    ip_hash CHAR(64) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_rfqs_reference (reference),
    KEY idx_rfqs_status_created (status, created_at),
    KEY idx_rfqs_read_created (is_read, created_at),
    KEY idx_rfqs_assigned (assigned_user_id, status),
    CONSTRAINT fk_rfqs_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_rfqs_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    CONSTRAINT fk_rfqs_user FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rfq_notes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rfq_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    note TEXT NOT NULL,
    follow_up_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_rfq_notes_rfq_created (rfq_id, created_at),
    CONSTRAINT fk_rfq_notes_rfq FOREIGN KEY (rfq_id) REFERENCES rfqs(id) ON DELETE CASCADE,
    CONSTRAINT fk_rfq_notes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rfq_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rfq_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    old_status VARCHAR(40) NULL,
    new_status VARCHAR(40) NOT NULL,
    note VARCHAR(500) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_rfq_history_rfq_created (rfq_id, created_at),
    CONSTRAINT fk_rfq_history_rfq FOREIGN KEY (rfq_id) REFERENCES rfqs(id) ON DELETE CASCADE,
    CONSTRAINT fk_rfq_history_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_enquiries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    company VARCHAR(160) NULL,
    mobile VARCHAR(30) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('NEW','READ','REPLIED','CLOSED','SPAM') NOT NULL DEFAULT 'NEW',
    assigned_user_id BIGINT UNSIGNED NULL,
    ip_hash CHAR(64) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_contact_status_created (status, created_at),
    CONSTRAINT fk_contact_user FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(140) NOT NULL,
    slug VARCHAR(160) NOT NULL,
    description TEXT NULL,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'PUBLISHED',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_blog_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blog_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    title VARCHAR(220) NOT NULL,
    slug VARCHAR(240) NOT NULL,
    featured_image VARCHAR(255) NULL,
    excerpt VARCHAR(700) NULL,
    content LONGTEXT NOT NULL,
    author VARCHAR(120) NOT NULL DEFAULT 'L2 Chemicals',
    published_at DATETIME NULL,
    featured TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    seo_title VARCHAR(190) NULL,
    seo_description VARCHAR(320) NULL,
    canonical_url VARCHAR(255) NULL,
    og_image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_blog_posts_slug (slug),
    KEY idx_blog_posts_status_date (status, published_at),
    CONSTRAINT fk_blog_posts_category FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    excerpt VARCHAR(600) NULL,
    content LONGTEXT NULL,
    template VARCHAR(80) NOT NULL DEFAULT 'default',
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    seo_title VARCHAR(190) NULL,
    seo_description VARCHAR(320) NULL,
    canonical_url VARCHAR(255) NULL,
    og_image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pages_slug (slug),
    KEY idx_pages_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE page_sections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id BIGINT UNSIGNED NOT NULL,
    section_key VARCHAR(120) NOT NULL,
    eyebrow VARCHAR(160) NULL,
    heading VARCHAR(255) NULL,
    content LONGTEXT NULL,
    image VARCHAR(255) NULL,
    button_text VARCHAR(100) NULL,
    button_url VARCHAR(255) NULL,
    extra_data JSON NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'PUBLISHED',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_page_section_key (page_id, section_key),
    KEY idx_page_sections_sort (page_id, sort_order),
    CONSTRAINT fk_page_sections_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE banners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(600) NULL,
    image VARCHAR(255) NULL,
    button_text VARCHAR(100) NULL,
    button_url VARCHAR(255) NULL,
    position VARCHAR(100) NOT NULL DEFAULT 'home_hero',
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_banners_position_status (position, status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE testimonials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_name VARCHAR(120) NOT NULL,
    company_name VARCHAR(160) NULL,
    designation VARCHAR(120) NULL,
    quote TEXT NOT NULL,
    image VARCHAR(255) NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_testimonials_status_sort (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uploaded_by BIGINT UNSIGNED NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    folder ENUM('products','categories','industries','blog','banners','documents','general') NOT NULL DEFAULT 'general',
    mime_type VARCHAR(100) NOT NULL,
    extension VARCHAR(10) NOT NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    alt_text VARCHAR(190) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_media_path (file_path),
    KEY idx_media_folder_created (folder, created_at),
    CONSTRAINT fk_media_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email_hash CHAR(64) NOT NULL,
    ip_hash CHAR(64) NOT NULL,
    successful TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_login_attempts_lookup (email_hash, ip_hash, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

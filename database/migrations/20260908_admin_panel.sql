SET NAMES utf8mb4;

ALTER TABLE products ADD COLUMN official_url VARCHAR(500) NULL AFTER packaging_information;
ALTER TABLE rfqs ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER status;
ALTER TABLE rfqs ADD INDEX idx_rfqs_read_created (is_read, created_at);

INSERT INTO settings (setting_group, setting_key, setting_value, field_type, is_public) VALUES
('homepage','hero_title','Complete Solutions for Thermoplastic Resins & Polymer Additives','text',1),
('homepage','hero_description','Reliable supply partner for PVC, PE, PP, Engineering Plastics, Masterbatch and thermoplastic additives.','textarea',1),
('homepage','hero_primary_text','Explore Products','text',1),
('homepage','hero_primary_url','products','text',1),
('homepage','hero_secondary_text','Request a Quote','text',1),
('homepage','hero_secondary_url','request-quote','text',1),
('homepage','home_about_title','Your Reliable Polymer & Additive Partner','text',1),
('homepage','home_about_description','L2 Chemicals supplies thermoplastic resins and specialty additives to compounders, masterbatch producers, converters and manufacturers across India.','textarea',1),
('homepage','why_heading','Why Choose L2 Chemicals','text',1),
('homepage','why_description','Responsive supply and application-oriented support for polymer processors.','textarea',1),
('homepage','why_benefits','Reliable Supply\nQuality Materials\nTechnical Support\nCommercial Support\nCustomized Solutions\nPan-India Supply','textarea',1),
('homepage','solutions_title','Solutions Engineered for Better Performance','text',1),
('homepage','solutions_description','From material selection to additive recommendations, we help align polymer solutions with processing needs and end-use applications.','textarea',1),
('homepage','solutions_button_text','Discuss Your Requirement','text',1),
('homepage','quote_cta_title','Looking for the Right Material for Your Application?','text',1),
('homepage','quote_cta_button_text','Request Quote','text',1),
('content','industries_intro','Explore polymer material and additive solutions by industry and processing application.','textarea',1),
('content','insights_intro','Practical perspectives on polymers, additives, processing and material performance.','textarea',1),
('content','contact_intro','Speak with our team about products, availability and application requirements.','textarea',1)
ON DUPLICATE KEY UPDATE setting_key=VALUES(setting_key);

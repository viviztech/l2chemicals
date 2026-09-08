<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class SiteRepository
{
    private PDO $db;

    public function __construct() { $this->db = Database::connection(); }

    public function settings(): array
    {
        $rows = $this->db->query('SELECT setting_key, setting_value FROM settings WHERE is_public=1')->fetchAll();
        return array_column($rows, 'setting_value', 'setting_key');
    }

    public function categories(?int $limit = null): array
    {
        $sql = 'SELECT * FROM product_categories WHERE status = \'PUBLISHED\' ORDER BY sort_order, name';
        if ($limit !== null) $sql .= ' LIMIT ' . max(1, $limit);
        return $this->db->query($sql)->fetchAll();
    }

    public function category(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM product_categories WHERE slug = :slug AND status = \'PUBLISHED\' LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function products(?int $limit = null, bool $featured = false, ?int $categoryId = null): array
    {
        $where = ['p.status = \'PUBLISHED\''];
        $params = [];
        if ($featured) $where[] = 'p.featured = 1';
        if ($categoryId !== null) { $where[] = 'p.category_id = :category_id'; $params['category_id'] = $categoryId; }
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN product_categories c ON c.id = p.category_id WHERE ' . implode(' AND ', $where) . ' ORDER BY p.sort_order, p.name';
        if ($limit !== null) $sql .= ' LIMIT ' . max(1, $limit);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function product(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN product_categories c ON c.id = p.category_id WHERE p.slug = :slug AND p.status = \'PUBLISHED\' LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch();
        if (!$product) return null;
        $product['images'] = $this->children('product_images', 'product_id', (int) $product['id']);
        $product['documents'] = $this->children('product_documents', 'product_id', (int) $product['id']);
        return $product;
    }

    public function industries(?int $limit = null): array
    {
        $sql = 'SELECT * FROM industries WHERE status = \'PUBLISHED\' ORDER BY sort_order, name';
        if ($limit !== null) $sql .= ' LIMIT ' . max(1, $limit);
        return $this->db->query($sql)->fetchAll();
    }

    public function industry(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM industries WHERE slug = :slug AND status = \'PUBLISHED\' LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $industry = $stmt->fetch();
        if (!$industry) return null;
        $products = $this->db->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN product_categories c ON c.id=p.category_id JOIN product_industries pi ON pi.product_id=p.id WHERE pi.industry_id=:id AND p.status=\'PUBLISHED\' ORDER BY p.name');
        $products->execute(['id' => $industry['id']]);
        $industry['products'] = $products->fetchAll();
        return $industry;
    }

    public function posts(int $limit = 12): array
    {
        $stmt = $this->db->prepare('SELECT b.*, c.name AS category_name FROM blog_posts b LEFT JOIN blog_categories c ON c.id=b.category_id WHERE b.status=\'PUBLISHED\' AND (b.published_at IS NULL OR b.published_at <= NOW()) ORDER BY b.featured DESC, b.published_at DESC LIMIT :limit');
        $stmt->bindValue('limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function post(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT b.*, c.name AS category_name FROM blog_posts b LEFT JOIN blog_categories c ON c.id=b.category_id WHERE b.slug=:slug AND b.status=\'PUBLISHED\' LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function page(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pages WHERE slug=:slug AND status=\'PUBLISHED\' LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $page = $stmt->fetch();
        if (!$page) return null;
        $page['sections'] = $this->children('page_sections', 'page_id', (int) $page['id']);
        return $page;
    }

    private function children(string $table, string $column, int $id): array
    {
        $allowed = ['product_images', 'product_documents', 'page_sections'];
        if (!in_array($table, $allowed, true)) return [];
        $statusClause = $table === 'page_sections' ? " AND status='PUBLISHED'" : '';
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE {$column}=:id{$statusClause} ORDER BY sort_order, id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }
}

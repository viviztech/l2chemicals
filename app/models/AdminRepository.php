<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class AdminRepository
{
    private PDO $db;
    public function __construct() { $this->db = Database::connection(); }

    public function dashboard(): array
    {
        return [
            'products' => (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'active_products' => (int) $this->db->query("SELECT COUNT(*) FROM products WHERE status='PUBLISHED'")->fetchColumn(),
            'categories' => (int) $this->db->query('SELECT COUNT(*) FROM product_categories')->fetchColumn(),
            'rfqs' => (int) $this->db->query('SELECT COUNT(*) FROM rfqs')->fetchColumn(),
            'unread_rfqs' => (int) $this->db->query('SELECT COUNT(*) FROM rfqs WHERE is_read=0')->fetchColumn(),
            'contacts' => (int) $this->db->query("SELECT COUNT(*) FROM contact_enquiries WHERE status='NEW'")->fetchColumn(),
            'recent' => $this->db->query('SELECT id,reference,full_name,company_name,product_name,status,is_read,created_at FROM rfqs ORDER BY created_at DESC LIMIT 7')->fetchAll(),
        ];
    }

    public function products(string $search = ''): array
    {
        $sql = 'SELECT p.*, c.name category_name FROM products p JOIN product_categories c ON c.id=p.category_id';
        $params = [];
        if ($search !== '') { $sql .= ' WHERE p.name LIKE :search OR p.product_code LIKE :search OR c.name LIKE :search'; $params['search'] = '%' . $search . '%'; }
        $sql .= ' ORDER BY p.updated_at DESC, p.name';
        $stmt = $this->db->prepare($sql); $stmt->execute($params); return $stmt->fetchAll();
    }

    public function product(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id=:id'); $stmt->execute(['id'=>$id]); return $stmt->fetch() ?: null;
    }

    public function saveProduct(array $data, ?int $id = null): int
    {
        $fields = ['category_id','name','slug','product_code','main_image','short_description','full_description','features','benefits','applications','technical_information','packaging_information','official_url','featured','status'];
        if ($id === null) {
            $columns = implode(',', $fields); $values = ':' . implode(',:', $fields);
            $stmt = $this->db->prepare("INSERT INTO products ({$columns}) VALUES ({$values})"); $stmt->execute($data); return (int) $this->db->lastInsertId();
        }
        $set = implode(',', array_map(static fn($field) => "{$field}=:{$field}", $fields));
        $data['id'] = $id; $this->db->prepare("UPDATE products SET {$set} WHERE id=:id")->execute($data); return $id;
    }

    public function deleteProduct(int $id): void { $this->db->prepare('DELETE FROM products WHERE id=:id')->execute(['id'=>$id]); }
    public function toggleProduct(int $id): void { $this->db->prepare("UPDATE products SET status=IF(status='PUBLISHED','DRAFT','PUBLISHED') WHERE id=:id")->execute(['id'=>$id]); }

    public function categories(bool $all = true): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) product_count FROM product_categories c LEFT JOIN products p ON p.category_id=c.id';
        if (!$all) $sql .= " WHERE c.status='PUBLISHED'";
        return $this->db->query($sql . ' GROUP BY c.id ORDER BY c.sort_order,c.name')->fetchAll();
    }
    public function category(int $id): ?array { $stmt=$this->db->prepare('SELECT * FROM product_categories WHERE id=:id'); $stmt->execute(['id'=>$id]); return $stmt->fetch() ?: null; }
    public function saveCategory(array $data, ?int $id = null): int
    {
        $fields=['name','slug','short_description','description','image','icon','sort_order','status'];
        if ($id===null) { $stmt=$this->db->prepare('INSERT INTO product_categories (' . implode(',',$fields) . ') VALUES (:' . implode(',:',$fields) . ')'); $stmt->execute($data); return (int)$this->db->lastInsertId(); }
        $set=implode(',',array_map(static fn($f)=>"{$f}=:{$f}",$fields)); $data['id']=$id; $this->db->prepare("UPDATE product_categories SET {$set} WHERE id=:id")->execute($data); return $id;
    }
    public function deleteCategory(int $id): void { $this->db->prepare('DELETE FROM product_categories WHERE id=:id')->execute(['id'=>$id]); }
    public function toggleCategory(int $id): void { $this->db->prepare("UPDATE product_categories SET status=IF(status='PUBLISHED','DRAFT','PUBLISHED') WHERE id=:id")->execute(['id'=>$id]); }

    public function settings(array $groups): array
    {
        $marks=implode(',',array_fill(0,count($groups),'?')); $stmt=$this->db->prepare("SELECT * FROM settings WHERE setting_group IN ({$marks}) ORDER BY setting_group,id"); $stmt->execute($groups); return $stmt->fetchAll();
    }
    public function updateSettings(array $values): void
    {
        $stmt=$this->db->prepare('UPDATE settings SET setting_value=:value WHERE setting_key=:key');
        foreach ($values as $key=>$value) $stmt->execute(['key'=>$key,'value'=>$value]);
    }
    public function editablePages(): array { return $this->db->query("SELECT * FROM pages WHERE slug IN ('about','technical-support') ORDER BY title")->fetchAll(); }
    public function updatePage(int $id, string $excerpt, string $content): void { $this->db->prepare('UPDATE pages SET excerpt=:excerpt,content=:content WHERE id=:id')->execute(compact('id','excerpt','content')); }

    public function rfqs(string $search='', string $status=''): array
    {
        $where=[];$params=[];
        if($search!==''){ $where[]='(reference LIKE :search OR full_name LIKE :search OR company_name LIKE :search OR email LIKE :search OR product_name LIKE :search)';$params['search']='%'.$search.'%'; }
        if($status!==''){ $where[]='status=:status';$params['status']=$status; }
        $sql='SELECT * FROM rfqs' . ($where?' WHERE '.implode(' AND ',$where):'') . ' ORDER BY created_at DESC';
        $stmt=$this->db->prepare($sql);$stmt->execute($params);return $stmt->fetchAll();
    }
    public function rfq(int $id): ?array { $stmt=$this->db->prepare('SELECT r.*,c.name category_name,p.name linked_product FROM rfqs r LEFT JOIN product_categories c ON c.id=r.category_id LEFT JOIN products p ON p.id=r.product_id WHERE r.id=:id');$stmt->execute(['id'=>$id]);return $stmt->fetch()?:null; }
    public function markRfq(int $id, bool $read): void { $this->db->prepare('UPDATE rfqs SET is_read=:read WHERE id=:id')->execute(['read'=>$read?1:0,'id'=>$id]); }
    public function updateRfqStatus(int $id,string $status,int $userId): void
    {
        $old=$this->rfq($id); if(!$old)return;
        $this->db->beginTransaction();
        try { $this->db->prepare('UPDATE rfqs SET status=:status,is_read=1 WHERE id=:id')->execute(compact('status','id')); $this->db->prepare('INSERT INTO rfq_status_history(rfq_id,user_id,old_status,new_status) VALUES(:id,:user,:old,:new)')->execute(['id'=>$id,'user'=>$userId,'old'=>$old['status'],'new'=>$status]);$this->db->commit(); } catch(\Throwable $e){$this->db->rollBack();throw $e;}
    }
    public function deleteRfq(int $id): void { $this->db->prepare('DELETE FROM rfqs WHERE id=:id')->execute(['id'=>$id]); }
    public function contacts(): array { return $this->db->query('SELECT * FROM contact_enquiries ORDER BY created_at DESC')->fetchAll(); }
    public function deleteContact(int $id): void { $this->db->prepare('DELETE FROM contact_enquiries WHERE id=:id')->execute(['id'=>$id]); }

    public function uniqueSlug(string $table,string $slug,?int $ignore=null): string
    {
        if(!in_array($table,['products','product_categories'],true)) throw new \InvalidArgumentException('Invalid slug table.');
        $base=$slug;$counter=2;
        while(true){$sql="SELECT COUNT(*) FROM {$table} WHERE slug=:slug".($ignore?' AND id<>:id':'');$stmt=$this->db->prepare($sql);$params=['slug'=>$slug];if($ignore)$params['id']=$ignore;$stmt->execute($params);if((int)$stmt->fetchColumn()===0)return $slug;$slug=$base.'-'.$counter++;}
    }
}


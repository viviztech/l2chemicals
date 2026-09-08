<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class EnquiryRepository
{
    private PDO $db;
    public function __construct(){ $this->db=Database::connection(); }
    public function createRfq(array $data): string
    {
        if ($data['category_id'] !== null) {
            $category = $this->db->prepare("SELECT COUNT(*) FROM product_categories WHERE id=:id AND status='PUBLISHED'");
            $category->execute(['id' => $data['category_id']]);
            if ((int) $category->fetchColumn() === 0) $data['category_id'] = null;
        }
        $this->db->beginTransaction();
        try {
            $temporary='TMP-'.bin2hex(random_bytes(12));
            $stmt=$this->db->prepare('INSERT INTO rfqs(reference,full_name,company_name,phone,whatsapp,email,city,state,category_id,product_name,quantity_requirement,application,message,preferred_contact_method,source_url,ip_hash) VALUES(:reference,:full_name,:company_name,:phone,:whatsapp,:email,:city,:state,:category_id,:product_name,:quantity_requirement,:application,:message,:preferred_contact_method,:source_url,:ip_hash)');
            $stmt->execute(array_merge($data,['reference'=>$temporary]));
            $id=(int)$this->db->lastInsertId();$reference='L2-RFQ-'.date('Ym').'-'.str_pad((string)$id,5,'0',STR_PAD_LEFT);
            $this->db->prepare('UPDATE rfqs SET reference=:reference WHERE id=:id')->execute(compact('reference','id'));
            $this->db->prepare("INSERT INTO rfq_status_history(rfq_id,old_status,new_status,note) VALUES(:id,NULL,'NEW','Enquiry submitted from public website')")->execute(['id'=>$id]);
            $this->db->commit();return $reference;
        } catch(\Throwable $e){$this->db->rollBack();throw $e;}
    }
    public function createContact(array $data):void
    {
        $stmt=$this->db->prepare('INSERT INTO contact_enquiries(name,company,mobile,email,subject,message,ip_hash) VALUES(:name,:company,:mobile,:email,:subject,:message,:ip_hash)');$stmt->execute($data);
    }
}

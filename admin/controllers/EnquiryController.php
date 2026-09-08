<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Core\Auth;

final class EnquiryController extends AdminController
{
    private const STATUSES=['NEW','CONTACTED','REQUIREMENT_VERIFIED','QUOTED','FOLLOW_UP','NEGOTIATION','CONVERTED','CLOSED','REJECTED'];
    public function __construct(){parent::__construct();Auth::requireRoles(['SUPER_ADMIN','ADMIN','SALES']);}
    public function index():void{$search=trim((string)($_GET['q']??''));$status=in_array($_GET['status']??'',self::STATUSES,true)?$_GET['status']:'';$this->render('enquiries/index',['title'=>'Quote Requests','rfqs'=>$this->admin->rfqs($search,$status),'search'=>$search,'filterStatus'=>$status,'statuses'=>self::STATUSES]);}
    public function show(string $id):void{$rfq=$this->admin->rfq((int)$id);if(!$rfq){flash('error','Enquiry not found.');redirect('admin/enquiries');}$this->admin->markRfq((int)$id,true);$rfq['is_read']=1;$this->render('enquiries/show',['title'=>$rfq['reference'],'rfq'=>$rfq,'statuses'=>self::STATUSES]);}
    public function mark(string $id):void{$this->csrf();$this->admin->markRfq((int)$id,($_POST['read']??'1')==='1');flash('success','Enquiry read status updated.');redirect('admin/enquiries');}
    public function status(string $id):void{$this->csrf();$status=(string)($_POST['status']??'');if(!in_array($status,self::STATUSES,true)){flash('error','Invalid status.');redirect('admin/enquiries/'.$id);}$this->admin->updateRfqStatus((int)$id,$status,(int)Auth::user()['id']);flash('success','Enquiry status updated.');redirect('admin/enquiries/'.$id);}
    public function delete(string $id):void{$this->csrf();$this->admin->deleteRfq((int)$id);flash('success','Quote request deleted.');redirect('admin/enquiries');}
}


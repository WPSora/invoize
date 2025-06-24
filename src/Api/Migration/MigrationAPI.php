<?php

namespace Invoize\Api\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;
use Invoize\Api\Api;
use Invoize\Migration\OJTInvoiceMigration\OJTInvoiceMigration;

class MigrationAPI extends Api
{
    protected string $routeName = 'migration';

    public function invoice()
    {
        $status = OJTInvoiceMigration::instance()->run();
        return $this->response([
            'message' => 'success',
            'data' => $status,
        ]);
    }

    public function test()
    {
        $products = Capsule::connection('ojt')->table('ojt_invoice_meta')
            ->where('ojt_invoices_id', 163)
            ->where('meta_key', '_status_mail')
            ->where('meta_value', 1)
            ->first();
        return $this->response(['message' => $products]);
    }

    public function registerRoutes()
    {
        $this->registerGetRequest('invoice', [
            'callback' => [$this, 'invoice'],
        ]);

        $this->registerGetRequest('test', [
            'callback' => [$this, 'test'],
        ]);
    }
}

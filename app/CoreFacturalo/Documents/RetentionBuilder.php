<?php

namespace App\CoreFacturalo\Documents;

use App\Models\Tenant\Retention;

class RetentionBuilder
{
    public function save($inputs)
    {
//        $document = $this->saveDocument(array_except($inputs, 'invoice'));
//        $document->invoice()->create($inputs['invoice']);
//
//        return $document;
//
//        $data = $inputs['retention'];
        $retention = Retention::create($inputs);
        foreach ($inputs['documents'] as $row) {
            $retention->documents()->create($row);
        }

        return $retention;
    }
}
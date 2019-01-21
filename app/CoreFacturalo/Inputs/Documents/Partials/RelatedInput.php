<?php

namespace App\CoreFacturalo\Inputs\Documents\Partials;

class RelatedInput
{
    public static function set($inputs)
    {
        if(key_exists('related', $inputs)) {
            $related = [];
            foreach ($inputs['related'] as $row)
            {
                $number = $row['number'];
                $document_type_id = $row['document_type_id'];
                $amount = $row['amount'];

                $related[] = [
                    'number' => $number,
                    'document_type_id' => $document_type_id,
                    'amount' => $amount
                ];
            }

            return $related;
        }
        return null;
    }
}
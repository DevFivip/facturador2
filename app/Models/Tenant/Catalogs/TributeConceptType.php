<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class TributeConceptType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_tribute_concept_types";

    protected $fillable = [
        'id',
        'active',
        'description',
    ];

    public $timestamps = false;

}
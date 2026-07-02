<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {
    use BelongsToTenant;
    protected $casts = [
        'seo_content' => 'object'
    ];
}

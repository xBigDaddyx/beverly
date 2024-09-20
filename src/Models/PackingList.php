<?php

namespace Xbigdaddyx\Beverly\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Xbigdaddyx\Fuse\Domain\Company\Models\Company;
use Wildside\Userstamps\Userstamps;

//class PackingList extends Model implements Auditable
class PackingList extends Model
{
    use HasFactory,SoftDeletes,Userstamps;

    protected $guarded = [];

    protected $casts = [
        'is_ratio' => 'boolean',
    ];
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {

            $model->company_id = auth()->user()->company->id;
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'id');
    }
    public function buyers()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'id');
    }
    public function cartonBoxes()
    {
        return $this->hasMany(CartonBox::class, 'packing_list_id', 'id');
    }

    public function packingListAttributes(): HasMany
    {
        return $this->hasMany(PackingListAttribute::class);
    }
}

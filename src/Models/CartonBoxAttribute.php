<?php

namespace Xbigdaddyx\Beverly\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Xbigdaddyx\BeverlyRatio\Traits\HasRatioTag;

class CartonBoxAttribute extends Model
{

    use SoftDeletes, HasRatioTag;

    protected $guarded = [];

    public function carton(): BelongsTo
    {
        return $this->belongsTo(CartonBox::class, 'carton_box_id', 'id');
    }
    public function tags(): MorphMany
    {
        return $this->morphMany(Tag::class, 'attributable');
    }

    public function packingList(): BelongsTo
    {
        return $this->belongsTo(CartonBox::class);
    }
}

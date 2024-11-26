<?php

namespace Xbigdaddyx\Beverly\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Polybag extends Model
{

    use HasFactory;
    use SoftDeletes;

    public static function boot()
    {
        parent::boot();
    }

    protected $guarded = [];

    public function tags(): MorphMany
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function user()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by', 'id');
    }
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by', 'id');
    }
    public function createdBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by', 'id');
    }

    public function box()
    {
        return $this->belongsTo(CartonBox::class, 'carton_box_id', 'id');
    }
}

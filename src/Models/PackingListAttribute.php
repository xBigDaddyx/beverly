<?php

namespace Xbigdaddyx\Beverly\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class PackingListAttribute extends Model
{

    use SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        Model::shouldBeStrict();
        self::creating(function ($model) {

            $model->company_id = auth()->user()->company->id;
        });
    }

    public function packingList(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id', 'id');
    }
    public function tags(): MorphMany
    {
        return $this->morphMany(Tag::class, 'attributable');
    }
}

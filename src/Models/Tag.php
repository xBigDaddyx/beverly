<?php

namespace Xbigdaddyx\Beverly\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tag extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function taggable()
    {
        return $this->morphTo();
    }

    public function attributable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(config('accuracy.models.user'), 'updated_by');
    }

    public function polybag(): BelongsTo
    {
        return $this->belongsTo(Polybag::class);
    }
}

<?php

namespace Xbigdaddyx\Beverly\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Xbigdaddyx\Fuse\Domain\User\Models\User;

class Tag extends Model
{
    use SoftDeletes, HasUuids;
    protected $primaryKey = 'uuid';
    protected $guarded = [];
    public static function boot()
    {
        parent::boot();
        Model::shouldBeStrict();
    }
    public function taggable()
    {
        return $this->morphTo();
    }

    public function attributable()
    {
        return $this->morphTo();
    }

    public function polybag(): BelongsTo
    {
        return $this->belongsTo(Polybag::class);
    }
}

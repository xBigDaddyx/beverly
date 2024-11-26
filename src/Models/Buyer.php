<?php

namespace Xbigdaddyx\Beverly\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Xbigdaddyx\Fuse\Domain\Company\Models\Company;
use Wildside\Userstamps\Userstamps;

class Buyer extends Model
{
    use HasFactory, SoftDeletes, Userstamps;
    protected $fillable = [
        'logo',
        'name',
        'country',
        'deleted_at',
        'deleted_by',
        'created_by',
        'updated_by',
        'company_id'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->company_id = Auth::user()->company_id;
        });
    }

    public function packingLists(): HasMany
    {
        return $this->hasMany(PackingList::class, 'buyer_id', 'id');
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}

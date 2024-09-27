<?php

namespace Xbigdaddyx\Beverly\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Wildside\Userstamps\Userstamps;
use Xbigdaddyx\BeverlySolid\Traits\HasSolidPolybag;

class CartonBox extends Model
{
    use HasFactory, SoftDeletes, Userstamps, LogsActivity, HasSolidPolybag;

    protected $fillable = [
        'box_code',
        'packing_list_id',
        'carton_number',
        'quantity',
        'type',
        'description',
        'is_completed',
        'locked_at',
        'deleted_by',
        'created_by',
        'updated_by',
        'completed_by',
        'company_id'
    ];

    public function scopeLocked($query)
    {
        return $query->whereNotNull('locked_at');
    }
    public function scopeUnlocked($query)
    {
        return $query->whereNull('locked_at');
    }
    protected $casts = [
        'is_completed' => 'boolean',
    ];
    public static function boot()
    {
        parent::boot();
        Model::shouldBeStrict();
        static::creating(function ($model) {
            $model->company_id = Auth::user()->company_id;
        });
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['box_code', 'packingList', 'is_completed', 'carton_number', 'quantity', 'locked_at', 'solidPolybags'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "This carton box has been {$eventName}")
            ->useLogName('carton-boxes')
            ->dontSubmitEmptyLogs();
    }
    public function polybags(): HasMany
    {
        return $this->hasMany(Polybag::class);
    }
    public function scopeOutstanding($query)
    {
        return $query->where('is_completed', false);
    }
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function packingList(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id', 'id');
    }
    public function packingLists(): BelongsTo
    {
        return $this->belongsTo(PackingList::class, 'packing_list_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(config('beverly.models.user'), 'updated_by', 'id');
    }
    public function company()
    {
        return $this->belongsTo(config('beverly.tenant'), config('beverly.tenant_foreign_key'), config('beverly.tenant_other_key'));
    }
    public function polybagTags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class, Polybag::class, 'carton_box_id', 'taggable_id')->where(
            'taggable_type',
            Polybag::class
        );
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(CartonBoxAttribute::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(config('beverly.models.user'), 'completed_by', 'id');
    }
}

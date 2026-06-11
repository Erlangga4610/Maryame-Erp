<?php

namespace App\Content\Models;

use App\Content\Enums\ContentFormat;
use App\Content\Enums\ContentPriority;
use App\Content\Enums\ContentStatus;
use App\Content\Enums\ContentType;
use App\Content\Enums\TiktokSubtype;
use App\Models\Campaign;
use App\Models\Platform;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['content_code'];

    protected $fillable = [
        'platform_id',
        'product_id',
        'campaign_id',
        'theme',
        'caption',
        'description',
        'content_type',
        'format',
        'priority',
        'tiktok_subtype',
        'final_asset_link',
        'thumbnail_link',
        'has_claim',
        'is_sensitive',
        'publish_date',
        'publish_time',
        'deadline_produksi',
        'deadline_approval',
        'pic_copy_id',
        'pic_visual_id',
        'pic_video_id',
        'revision_note',
        'version',
        'copy_brief',
        'visual_brief',
        'video_brief',
    ];

    protected $casts = [
        'has_claim' => 'boolean',
        'is_sensitive' => 'boolean',
        'publish_date' => 'date',
        'publish_time' => 'datetime:H:i',
        'deadline_produksi' => 'date',
        'deadline_approval' => 'date',
        'status' => ContentStatus::class,
        'content_type' => ContentType::class,
        'format' => ContentFormat::class,
        'priority' => ContentPriority::class,
        'tiktok_subtype' => TiktokSubtype::class,
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function picCopy()
    {
        return $this->belongsTo(User::class, 'pic_copy_id');
    }

    public function picVisual()
    {
        return $this->belongsTo(User::class, 'pic_visual_id');
    }

    public function picVideo()
    {
        return $this->belongsTo(User::class, 'pic_video_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function tiktokQc()
    {
        return $this->hasOne(TiktokQc::class, 'content_id');
    }

    public function versions()
    {
        return $this->hasMany(ContentVersion::class);
    }

    public function adjustmentLogs()
    {
        return $this->hasMany(AdjustmentLog::class);
    }

    public function getFullPublishDateAttribute(): ?string
    {
        if (! $this->publish_date) {
            return null;
        }

        return $this->publish_date->format('d M Y').
               ($this->publish_time ? ' '.$this->publish_time->format('H:i') : '');
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match ($this->priority) {
            ContentPriority::HIGH => 'red',
            ContentPriority::MEDIUM => 'yellow',
            ContentPriority::LOW => 'green',
            default => 'gray',
        };
    }

    public function scopeByStatus($query, ContentStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            ContentStatus::DRAFT,
            ContentStatus::IN_PRODUCTION,
            ContentStatus::READY_REVIEW,
        ]);
    }

    public function scopePublishBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('publish_date', [$startDate, $endDate]);
    }
}

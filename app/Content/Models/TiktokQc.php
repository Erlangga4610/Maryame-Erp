<?php

namespace App\Content\Models;

use App\Content\Enums\TiktokSubtype;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TiktokQc extends Model
{
    protected $table = 'tiktok_qc';

    protected $fillable = [
        'content_id',
        'k1_audio_original',
        'k2_demo_penggunaan',
        'k3_produk_visible',
        'k4_manfaat_verbal',
        'k5_tambahan',
        'k6_tambahan',
        'k5_label',
        'k6_label',
        'has_shopping_cart',
        'notes',
        'status',
        'checked_by',
        'checked_at',
    ];

    protected $casts = [
        'has_shopping_cart' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function allPass(): bool
    {
        return $this->k1_audio_original === 'pass'
            && $this->k2_demo_penggunaan === 'pass'
            && $this->k3_produk_visible === 'pass'
            && $this->k4_manfaat_verbal === 'pass'
            && $this->k5_tambahan === 'pass'
            && $this->k6_tambahan === 'pass';
    }

    public function suggestedSubtype(): TiktokSubtype
    {
        if ($this->has_shopping_cart && $this->allPass()) {
            return TiktokSubtype::KK_INTERAKTIF;
        }

        if ($this->has_shopping_cart) {
            return TiktokSubtype::KK_SOFT_SELLING;
        }

        return TiktokSubtype::NON_KK;
    }

    public static function criteriaLabels(): array
    {
        return [
            'k1_audio_original' => 'Voice/Audio original (ada narasi/voice-over; dilarang hanya musik trending tanpa narasi)',
            'k2_demo_penggunaan' => 'Demo penggunaan (tunjukkan cara pakai; dilarang cuma pegang & goyang produk)',
            'k3_produk_visible' => 'Produk visible jelas (lighting bagus, produk tajam; dilarang buram/gelap)',
            'k4_manfaat_verbal' => 'Manfaat/benefit disebut verbal',
            'k5_tambahan' => 'Kriteria tambahan #5',
            'k6_tambahan' => 'Kriteria tambahan #6',
        ];
    }
}

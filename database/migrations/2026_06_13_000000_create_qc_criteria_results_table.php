<?php

use App\Content\Models\TiktokQc;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qc_criteria_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_check_id')->constrained('tiktok_qc')->cascadeOnDelete();
            $table->unsignedTinyInteger('criteria_no')->comment('1-6');
            $table->string('result', 10)->nullable()->comment('pass, fail, na');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['qc_check_id', 'criteria_no']);
        });

        $criteriaMap = [
            1 => 'k1_audio_original',
            2 => 'k2_demo_penggunaan',
            3 => 'k3_produk_visible',
            4 => 'k4_manfaat_verbal',
            5 => 'k5_tambahan',
            6 => 'k6_tambahan',
        ];

        TiktokQc::chunk(100, function ($qcChecks) use ($criteriaMap) {
            foreach ($qcChecks as $qc) {
                foreach ($criteriaMap as $no => $column) {
                    $value = $qc->{$column};
                    if ($value !== null) {
                        DB::table('qc_criteria_results')->insert([
                            'qc_check_id' => $qc->id,
                            'criteria_no' => $no,
                            'result' => $value,
                            'note' => $no === 5 ? $qc->k5_label : ($no === 6 ? $qc->k6_label : null),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_criteria_results');
    }
};

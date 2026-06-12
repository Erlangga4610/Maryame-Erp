<?php

namespace App\Livewire;

use Livewire\Component;

class RoleGuide extends Component
{
    public function render()
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'tier' => 0,
                'color' => 'red',
                'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                'description' => 'Akses penuh tanpa batasan. Bypass semua role & permission gates.',
                'tasks' => [
                    'Membuat, mengedit, menghapus konten apa pun tanpa batasan',
                    'Finalize brief kapan pun',
                    'Approve semua tahap approval (CW, CSP, SMS, RnD, Legal)',
                    'CRUD Master Data (Platforms, Products, Campaigns, Users)',
                    'Melihat semua halaman dan fitur',
                    'Assign role ke user lain',
                    'Capacity & Production Schedule — full access',
                ],
            ],
            [
                'name' => 'CSP',
                'tier' => 1,
                'color' => 'pink',
                'badge' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300',
                'description' => 'Content Strategy Planner — penanggung jawab strategi konten.',
                'tasks' => [
                    'Membuat konten baru (draft)',
                    'Mengisi Strategic Brief (Angle, Positioning, Target Audience, Key Message, Tone, Copy Direction)',
                    'Finalize brief (tandai brief selesai)',
                    'Approve tahap CSP pada approval pipeline',
                    'Mengelola Major & Reactive adjustment',
                    'Mengedit konten milik sendiri (+)',
                    'Melihat semua konten',
                ],
            ],
            [
                'name' => 'SMS',
                'tier' => 1,
                'color' => 'purple',
                'badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
                'description' => 'Social Media Specialist — penanggung jawab aspek teknis konten.',
                'tasks' => [
                    'Mengisi Technical Brief (Aspect Ratio, Resolution, Durasi, Format, Hashtag, Audio, Originality, Thumbnail Note)',
                    'Mengisi Visual Direction & Video Direction',
                    'Upload Final Asset & Thumbnail',
                    'Approve tahap SMS pada approval pipeline',
                    'Mengedit konten milik sendiri (+)',
                ],
            ],
            [
                'name' => 'CW',
                'tier' => 2,
                'color' => 'emerald',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'description' => 'Content Writer — penulis konten, PIC Copy.',
                'tasks' => [
                    'Menulis caption dan copy konten',
                    'PIC Copy — menerima task di My Tasks',
                    'Approval stage 1 (CW) — review awal konten',
                    'Melihat & mengomentari konten',
                ],
            ],
            [
                'name' => 'GVD',
                'tier' => 2,
                'color' => 'emerald',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'description' => 'Graphic & Visual Designer — desainer grafis, PIC Visual.',
                'tasks' => [
                    'Membuat visual/grafis konten',
                    'PIC Visual — menerima task di My Tasks',
                    'Melihat & mengomentari konten',
                ],
            ],
            [
                'name' => 'CC',
                'tier' => 2,
                'color' => 'emerald',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'description' => 'Content Creator — kreator video, PIC Video.',
                'tasks' => [
                    'Memproduksi video konten',
                    'PIC Video — menerima task di My Tasks',
                    'Melihat & mengomentari konten',
                ],
            ],
            [
                'name' => 'VG',
                'tier' => 2,
                'color' => 'emerald',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'description' => 'Video Grapher — videografer pendukung.',
                'tasks' => [
                    'Mendukung produksi video',
                    'Melihat & mengomentari konten',
                ],
            ],
            [
                'name' => 'ASM',
                'tier' => 2,
                'color' => 'emerald',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'description' => 'Area Sales Manager — sales lapangan.',
                'tasks' => [
                    'Melihat jadwal konten & campaign',
                    'Memberikan masukan/komentar',
                ],
            ],
            [
                'name' => 'RnD',
                'tier' => 2,
                'color' => 'emerald',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'description' => 'Research & Development — riset produk.',
                'tasks' => [
                    'Approval stage 4 (RnD) — verifikasi klaim produk',
                    'Memastikan konten sesuai fakta produk',
                ],
            ],
            [
                'name' => 'Legal',
                'tier' => 2,
                'color' => 'emerald',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                'description' => 'Legal — kepatuhan regulasi.',
                'tasks' => [
                    'Approval stage 5 (Legal) — verifikasi kepatuhan legal & regulasi',
                    'Memastikan konten tidak melanggar aturan BPOM / periklanan',
                ],
            ],
            [
                'name' => 'MC_BM',
                'tier' => 3,
                'color' => 'zinc',
                'badge' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-900/30 dark:text-zinc-300',
                'description' => 'Marketing Communication / Brand Manager — pengawas brand & adjustment.',
                'tasks' => [
                    'Menyetujui Major Adjustment (Alur 9) — perubahan besar pada konten',
                    'Melihat semua konten (view only)',
                    'Tidak bisa mengedit konten',
                ],
            ],
        ];

        $pipeline = [
            ['stage' => 'CW', 'role' => 'CW', 'color' => 'emerald', 'desc' => 'Step 1 — Review copy & key message oleh Content Writer'],
            ['stage' => 'CSP', 'role' => 'CSP', 'color' => 'pink', 'desc' => 'Step 2 — Review strategic & angle oleh Content Strategy Planner'],
            ['stage' => 'SMS', 'role' => 'SMS', 'color' => 'purple', 'desc' => 'Step 3 — Review spec teknis oleh Social Media Specialist'],
            ['stage' => 'RnD', 'role' => 'RnD', 'color' => 'emerald', 'desc' => 'Step 4 — Validasi klaim produk (hanya jika has_claim = true)'],
            ['stage' => 'Legal', 'role' => 'Legal', 'color' => 'emerald', 'desc' => 'Step 5 — Verifikasi legal & regulasi (hanya jika is_sensitive = true)'],
        ];

        return view('livewire.role-guide', [
            'roles' => $roles,
            'pipeline' => $pipeline,
        ])->layout('layouts.admin', ['title' => 'Panduan Role & Tugas']);
    }
}

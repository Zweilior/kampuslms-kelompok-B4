<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Mengukur jumlah query, waktu, dan memori halaman utama KampusLMS.
 *
 * Simpan sebagai: app/Console/Commands/MeasurePerformance.php
 * Jalankan     : php artisan perf:measure --md --detail
 */
class MeasurePerformance extends Command
{
    protected $signature = 'perf:measure
        {--runs=3 : Jumlah pengukuran per halaman (waktu diambil mediannya)}
        {--md : Cetak juga tabel Markdown untuk docs/performa.md}
        {--detail : Tampilkan query berulang dan EXPLAIN untuk halaman terburuk}';

    protected $description = 'Ukur query, waktu, dan memori halaman utama (hanya non-produksi)';

    /** @var array<int, array{sql: string, bindings: array, time: float}> */
    private array $log = [];

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Perintah ini hanya untuk lingkungan non-produksi.');

            return self::FAILURE;
        }

        // Pilih data yang paling "berat" supaya masalahnya terlihat.
        $course = Course::query()
            ->where('status', 'active')
            ->withCount(['students', 'assignments'])
            ->orderByDesc('students_count')
            ->orderByDesc('assignments_count')
            ->first();

        if (! $course) {
            $this->error('Tidak ada mata kuliah aktif. Jalankan: php artisan migrate:fresh --seed');

            return self::FAILURE;
        }

        // Memakai query langsung (bukan $course->lecturer) agar tidak memicu
        // preventLazyLoading kalau nanti diaktifkan.
        $lecturer = User::find($course->lecturer_id);
        $student = $course->students()->first();
        $admin = User::where('role', 'admin')->first();
        $assignment = $course->assignments()
            ->where('status', 'published')
            ->withCount('submissions')
            ->orderByDesc('submissions_count')
            ->first();

        $this->info("Mata kuliah uji : {$course->name} ({$course->students_count} mahasiswa, {$course->assignments_count} tugas)");
        $this->info('Dosen uji       : '.($lecturer?->name ?? '-'));
        $this->info('Mahasiswa uji   : '.($student?->name ?? '-'));
        $this->newLine();

        DB::listen(function ($q) {
            $this->log[] = ['sql' => $q->sql, 'bindings' => $q->bindings, 'time' => $q->time];
        });

        $r = fn (string $name, array $params = []) => route($name, $params, false);
        $c = ['course' => $course];

        $pages = array_filter([
            ['Daftar mata kuliah (mahasiswa)', $student, $r('mahasiswa.courses.index')],
            ['Daftar mata kuliah (dosen)', $lecturer, $r('dosen.courses.index')],
            ['Detail mata kuliah (mahasiswa)', $student, $r('mahasiswa.courses.show', $c)],
            ['Detail mata kuliah (dosen)', $lecturer, $r('dosen.courses.show', $c)],
            ['Daftar tugas (mahasiswa)', $student, $r('mahasiswa.courses.assignments.index', $c)],
            ['Daftar tugas (dosen)', $lecturer, $r('dosen.courses.assignments.index', $c)],
            $assignment ? ['Daftar pengumpulan (dosen)', $lecturer, $r('dosen.courses.assignments.submissions.index', $c + ['assignment' => $assignment])] : null,
            ['Rekap nilai (dosen)', $lecturer, $r('dosen.grades.index')],
            ['Rekap nilai (mahasiswa)', $student, $r('mahasiswa.grades.index')],
            ['Rekap nilai per matkul (mahasiswa)', $student, $r('mahasiswa.grades.courses.show', $c)],
            ['Rekap nilai (admin)', $admin, $r('admin.grades.index')],
            ['Dashboard mahasiswa', $student, $r('mahasiswa.dashboard')],
            ['Dashboard dosen', $lecturer, $r('dosen.dashboard')],
            ['Dashboard admin', $admin, $r('admin.dashboard')],
        ]);

        $runs = max(1, (int) $this->option('runs'));
        $results = [];

        foreach ($pages as [$label, $user, $uri]) {
            if (! $user) {
                $this->warn("Dilewati (user tidak ada): {$label}");

                continue;
            }
            $this->line("Mengukur: {$label}");
            $results[] = ['label' => $label] + $this->measure($user, $uri, $runs);
        }

        usort($results, fn ($a, $b) => [$b['queries'], $b['ms']] <=> [$a['queries'], $a['ms']]);

        $this->newLine();
        $this->table(
            ['Halaman (terburuk di atas)', 'Status', 'Query', 'Duplikat', 'Waktu (ms)', 'Memori (MB)'],
            array_map(fn ($x) => [$x['label'], $x['status'], $x['queries'], $x['dups'], $x['ms'], $x['mem']], $results)
        );
        $this->comment('Status 500 = halaman error (mis. kena preventLazyLoading). Cek storage/logs/laravel.log.');
        $this->comment('"Duplikat" = jumlah query dengan SQL persis sama yang diulang (tanda N+1).');
        $this->comment('Memori = tambahan puncak selama request, bukan angka absolut Debugbar. Pakai konsisten untuk sebelum/sesudah.');

        if ($this->option('md')) {
            $this->newLine();
            $this->line('| Halaman | Jumlah query | Waktu (ms) | Memori (MB) |');
            $this->line('|---------|-------------:|-----------:|------------:|');
            foreach ($results as $x) {
                $this->line("| {$x['label']} | {$x['queries']} | {$x['ms']} | {$x['mem']} |");
            }
        }

        if ($this->option('detail') && $results) {
            $targets = collect($results)->filter(fn ($x) => $x['dups'] > 0)->keyBy('label');
            $slowest = collect($results)->sortByDesc('ms')->first();
            $targets[$slowest['label']] = $slowest;

            foreach ($targets as $x) {
                $this->showDetail($x);
            }
        }

        return self::SUCCESS;
    }

    private function measure(User $user, string $uri, int $runs): array
    {
        $kernel = app(Kernel::class);
        $times = [];
        $out = [];

        // Pengukuran ke-0 hanya pemanasan (cache view/kompilasi), tidak dihitung.
        for ($i = 0; $i <= $runs; $i++) {
            $this->log = [];
            gc_collect_cycles();
            memory_reset_peak_usage();
            $baseline = memory_get_usage();

            Auth::guard('web')->setUser($user);

            $start = hrtime(true);
            $response = $kernel->handle(Request::create($uri, 'GET'));
            $ms = (hrtime(true) - $start) / 1e6;

            if ($i === 0) {
                continue;
            }

            $times[] = $ms;
            $sqls = array_column($this->log, 'sql');
            $out = [
                'status' => $response->getStatusCode(),
                'queries' => count($sqls),
                'dups' => count($sqls) - count(array_unique($sqls)),
                'mem' => round((memory_get_peak_usage() - $baseline) / 1048576, 2),
                'log' => $this->log,
            ];
        }

        sort($times);
        $out['ms'] = round($times[intdiv(count($times), 2)], 1);

        return $out;
    }

    private function showDetail(array $worst): void
    {
        $this->newLine();
        $this->info("DETAIL halaman terburuk: {$worst['label']}");

        $counts = array_count_values(array_column($worst['log'], 'sql'));
        arsort($counts);
        $this->line('Query yang diulang (kandidat N+1):');
        foreach (array_slice($counts, 0, 5, true) as $sql => $n) {
            if ($n > 1) {
                $this->line("  {$n}x  ".mb_strimwidth($sql, 0, 140, '...'));
            }
        }

        $slow = collect($worst['log'])
            ->filter(fn ($q) => str_starts_with(strtolower(ltrim($q['sql'])), 'select'))
            ->sortByDesc('time')
            ->first();

        if (! $slow) {
            return;
        }

        $this->newLine();
        $this->line('Query SELECT paling lambat ('.round($slow['time'], 2).' ms):');
        $this->line('  '.mb_strimwidth($slow['sql'], 0, 200, '...'));

        try {
            $rows = collect(DB::select('EXPLAIN '.$slow['sql'], $slow['bindings']))
                ->map(fn ($row) => collect((array) $row)->only(['table', 'type', 'key', 'rows'])->all())
                ->all();
            $this->table(['table', 'type', 'key', 'rows'], $rows);
            $this->comment('type=ALL berarti seluruh tabel dipindai (index tidak terpakai).');
        } catch (\Throwable $e) {
            $this->warn('EXPLAIN gagal: '.$e->getMessage());
        }
    }
}
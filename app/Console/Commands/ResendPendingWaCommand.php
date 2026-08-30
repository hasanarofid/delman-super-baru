<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RencanaKerjaT;
use App\Http\Controllers\RencanaTugasController;
use Illuminate\Support\Facades\Log;

class ResendPendingWaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:resend-pending {--tahun=2026} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim ulang WA Blast untuk semua rencana kerja yang belum diisi umpan baliknya';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tahun = $this->option('tahun');
        $sendAll = $this->option('all');
        $this->info("Mulai memproses resend WA Blast rencana kerja...");

        $query = RencanaKerjaT::query();

        if ($tahun !== 'all') {
            $query->where('tahun_ajaran', 'like', "%{$tahun}%");
        }

        if (!$sendAll) {
            $query->where(function($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 1);
            });
        }

        $rencanaList = $query->get();

        $controller = app(RencanaTugasController::class);
        $count = 0;

        foreach ($rencanaList as $row) {
            try {
                $controller->kirimWa($row->id);
                $count++;
                $this->info("[{$count}] Berhasil mengirim WA untuk Rencana ID: {$row->id} ({$row->nama_program_kerja})");
            } catch (\Exception $e) {
                $this->error("Gagal mengirim WA Rencana ID {$row->id}: " . $e->getMessage());
            }
        }

        $this->info("Selesai! Total {$count} pesan WA berhasil dikirim ulang.");
        return 0;
    }
}

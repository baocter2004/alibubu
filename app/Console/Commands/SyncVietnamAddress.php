<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncVietnamAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-vietnam-address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing Vietnam address...');

        $data = Http::timeout(120)
            ->withoutVerifying()
            ->get('https://provinces.open-api.vn/api/v2/?depth=2')
            ->json();

        try {
            DB::beginTransaction();

            DB::table('wards')->truncate();
            DB::table('provinces')->truncate();

            foreach ($data as $provinceData) {
                $provinceId = DB::table('provinces')->insertGetId([
                    'code' => $provinceData['code'],
                    'name' => $provinceData['name'],
                    'codename' => $provinceData['codename'],
                    'division_type' => $provinceData['division_type'],
                    'phone_code' => $provinceData['phone_code'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $wardsInsert = [];

                foreach ($provinceData['wards'] as $ward) {
                    $wardsInsert[] = [
                        'code' => $ward['code'],
                        'name' => $ward['name'],
                        'codename' => $ward['codename'],
                        'division_type' => $ward['division_type'],
                        'province_id' => $provinceId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('wards')->insert($wardsInsert);
            }

            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            $this->error('Sync failed: ' . $th->getMessage());
            return;
        }

        $this->info('Sync completed.');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncVietnamAddress extends Command
{
    protected $signature = 'app:sync-vietnam-address';
    protected $description = 'Sync Vietnam provinces and wards';

    public function handle()
    {
        $this->info('Syncing Vietnam address...');

        $data = Http::timeout(120)
            ->withoutVerifying()
            ->get('https://provinces.open-api.vn/api/v2/?depth=2')
            ->json();

        if (!$data) {
            $this->error('Không lấy được dữ liệu API');
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($data as $provinceData) {

                DB::table('provinces')->updateOrInsert(
                    ['code' => $provinceData['code']],
                    [
                        'name' => $provinceData['name'],
                        'codename' => $provinceData['codename'],
                        'division_type' => $provinceData['division_type'],
                        'phone_code' => $provinceData['phone_code'] ?? 0,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );

                $provinceId = DB::table('provinces')
                    ->where('code', $provinceData['code'])
                    ->value('id');

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

                DB::table('wards')->upsert(
                    $wardsInsert,
                    ['code'],
                    ['name', 'codename', 'division_type', 'province_id', 'updated_at']
                );
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->error('Sync failed: ' . $th->getMessage());
            return;
        }

        $this->info('Sync completed.');
    }
}
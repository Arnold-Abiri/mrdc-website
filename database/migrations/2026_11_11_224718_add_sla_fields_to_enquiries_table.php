<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table): void {
            $table->dateTime('due_at')->nullable()->after('submitted_at');
            $table->dateTime('last_escalated_at')->nullable()->after('due_at');
        });

        $slaDays = (int) config('cms.enquiries_sla_days', 5);
        DB::table('enquiries')
            ->whereIn('status', ['new', 'in_progress'])
            ->whereNull('due_at')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($slaDays): void {
                foreach ($rows as $row) {
                    DB::table('enquiries')->where('id', $row->id)->update([
                        'due_at' => Carbon::parse($row->submitted_at)->addDays($slaDays)->toDateTimeString(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table): void {
            $table->dropColumn(['due_at', 'last_escalated_at']);
        });
    }
};

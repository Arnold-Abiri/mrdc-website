<?php

namespace App\Domain\Cms;

use App\Domain\Identity\AuditWriter;
use App\Models\DistrictStatistic;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class DistrictStatisticManager
{
    public function create(User $actor, array $input): DistrictStatistic
    {
        Gate::forUser($actor)->authorize('create', DistrictStatistic::class);
        $data = $this->validate($input);

        return DB::transaction(function () use ($actor, $data): DistrictStatistic {
            $statistic = new DistrictStatistic($data);
            $statistic->save();
            app(AuditWriter::class)->record($actor, 'statistic.created', $statistic, ['label' => $statistic->label]);

            return $statistic;
        });
    }

    public function update(User $actor, DistrictStatistic $statistic, array $input): DistrictStatistic
    {
        Gate::forUser($actor)->authorize('update', $statistic);
        $data = $this->validate($input);

        return DB::transaction(function () use ($actor, $statistic, $data): DistrictStatistic {
            $statistic->fill($data);
            $statistic->save();
            app(AuditWriter::class)->record($actor, 'statistic.updated', $statistic, ['label' => $statistic->label]);

            return $statistic;
        });
    }

    private function validate(array $input): array
    {
        return Validator::make($input, [
            'label' => ['required', 'string', 'max:120'], 'value' => ['required', 'string', 'max:60'],
            'unit' => ['nullable', 'string', 'max:60'], 'icon' => ['nullable', 'string', 'max:60'],
            'source_note' => ['nullable', 'string', 'max:255'],
            'display_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['required', 'boolean'],
        ])->validate();
    }
}

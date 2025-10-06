<?php

namespace App\Console\Commands;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Console\Command;

class AllocateEmployeeLeaves extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaves:allocate {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allocate yearly leaves to all employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->argument('year') ?? now()->year;

        $this->info("Allocating leaves for year: {$year}");

        // Get all employees
        $employees = User::whereHas('roles', function ($q) {
            $q->where('name', 'employee');
        })->get();

        if ($employees->isEmpty()) {
            $this->warn('No employees found!');
            return 0;
        }

        // Get leave types
        $sickLeave = LeaveType::where('name', 'Sick')->first();
        $casualLeave = LeaveType::where('name', 'Casual')->first();

        if (!$sickLeave || !$casualLeave) {
            $this->error('Leave types not found! Please ensure "Sick" and "Casual" leave types exist.');
            return 1;
        }

        $allocated = 0;
        $updated = 0;

        foreach ($employees as $employee) {
            $this->info("Processing: {$employee->name}");

            // Allocate Sick Leave (7 days)
            $sickAllocation = LeaveAllocation::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'leave_type_id' => $sickLeave->id,
                    'year' => $year,
                ],
                [
                    'total_allocated' => 7,
                    'remaining' => 7,
                ]
            );

            if ($sickAllocation->wasRecentlyCreated) {
                $allocated++;
                $this->line("  ✓ Allocated 7 Sick leaves");
            } else {
                $updated++;
                $this->line("  ↻ Updated Sick leaves allocation");
            }

            // Allocate Casual Leave (30 days)
            $casualAllocation = LeaveAllocation::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'leave_type_id' => $casualLeave->id,
                    'year' => $year,
                ],
                [
                    'total_allocated' => 30,
                    'remaining' => 30,
                ]
            );

            if ($casualAllocation->wasRecentlyCreated) {
                $allocated++;
                $this->line("  ✓ Allocated 30 Casual leaves");
            } else {
                $updated++;
                $this->line("  ↻ Updated Casual leaves allocation");
            }
        }

        $this->newLine();
        $this->info("✓ Leave allocation completed!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Employees Processed', $employees->count()],
                ['New Allocations', $allocated],
                ['Updated Allocations', $updated],
                ['Year', $year],
            ]
        );

        return 0;
    }
}

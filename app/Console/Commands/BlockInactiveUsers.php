<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BlockInactiveUsers extends Command
{
    protected $signature = 'users:blockInactive';
    protected $description = 'Block inactive users who have not been active for the last 3 days';

    public function handle()
    {
        $thresholdDate = Carbon::now()->subDays(3);

        $inactiveUsers = User::where('last_active_at', '<=', $thresholdDate)
            ->where('is_active', 1)
            ->update(['is_active'=> 0]);

        $this->info('Inactive users blocked successfully.');
    }
}

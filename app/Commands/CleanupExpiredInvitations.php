<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GameInvitation;

class CleanupExpiredInvitations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تنظيف الدعوات المنتهية الصلاحية';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 بدء تنظيف الدعوات المنتهية الصلاحية...');

        // تحديث الدعوات المنتهية الصلاحية
        $expiredCount = GameInvitation::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        // حذف الدعوات القديمة (أكثر من 24 ساعة)
        $deletedCount = GameInvitation::whereIn('status', ['expired', 'rejected', 'cancelled'])
            ->where('updated_at', '<=', now()->subHours(24))
            ->delete();

        $this->info("✅ تم تحديث {$expiredCount} دعوة منتهية الصلاحية");
        $this->info("🗑️ تم حذف {$deletedCount} دعوة قديمة");
        $this->info('✨ تم الانتهاء من التنظيف بنجاح');

        return Command::SUCCESS;
    }
}
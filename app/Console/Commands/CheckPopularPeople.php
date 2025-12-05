<?php

namespace App\Console\Commands;

use App\Mail\PopularPersonMail;
use App\Models\Like;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckPopularPeople extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'people:check-popular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if any person has more than 50 likes and send email notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for popular people...');

        // Get users who have received more than 50 likes
        // Using subquery approach for PostgreSQL compatibility
        $popularUserIds = DB::table('likes')
            ->select('target_user_id')
            ->groupBy('target_user_id')
            ->havingRaw('COUNT(*) > 50')
            ->pluck('target_user_id');

        if ($popularUserIds->isEmpty()) {
            $this->info('No popular people found (no one has more than 50 likes).');
            return Command::SUCCESS;
        }

        $popularPeople = User::whereIn('id', $popularUserIds)
            ->get()
            ->map(function ($user) {
                $user->total_likes = Like::where('target_user_id', $user->id)->count();
                return $user;
            })
            ->sortByDesc('total_likes')
            ->values();

        if ($popularPeople->isEmpty()) {
            $this->info('No popular people found (no one has more than 50 likes).');
            return Command::SUCCESS;
        }

        $this->info("Found {$popularPeople->count()} popular person(s) with more than 50 likes.");

        // Get admin email from env
        $adminEmail = env('MAIL_TO_ADDRESS');

        if (!$adminEmail) {
            $this->error('MAIL_TO_ADDRESS is not set in .env file. Please set it first.');
            return Command::FAILURE;
        }

        // Validate mail configuration
        $mailMailer = config('mail.default');
        if (in_array($mailMailer, ['log', 'array'])) {
            $this->warn("Warning: MAIL_MAILER is set to '{$mailMailer}'. Email will not be sent, only logged.");
            return Command::FAILURE;
        }

        try {
            // Send email immediately without queue
            Mail::to($adminEmail)->sendNow(new PopularPersonMail($popularPeople));

            // Update notify_at for all popular people
            User::whereIn('id', $popularUserIds)
                ->update(['notify_at' => now()]);

            $this->info("Email notification sent successfully to: {$adminEmail}");
            $this->info("Updated notify_at for {$popularPeople->count()} popular person(s).");

            // Display summary
            $this->table(
                ['ID', 'Name', 'Age', 'Total Likes'],
                $popularPeople->map(function ($person) {
                    return [
                        $person->id,
                        $person->name,
                        $person->age ?? 'N/A',
                        $person->total_likes,
                    ];
                })->toArray()
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send email: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }
}

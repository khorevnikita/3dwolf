<?php

namespace App\Console\Commands;

use App\Mail\NewsletterEmail;
use App\Models\Newsletter;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mockery\Exception;

class SendNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletters:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $newsletter = Newsletter::query()
            ->sending()->orderBy("id", "asc")->first();
        if (!$newsletter) {
            echo "No sending notification \n";
            return;
        }

        $users = $newsletter->queuedUsers()->limit(10)->get();

        if ($users->count() === 0) {
            $newsletter->status = Newsletter::STATUSES[2];
            $newsletter->progress = 100;
            $newsletter->save();
            echo "Sent to all receivers \n";
            return;
        }
        foreach ($users as $user) {
            try {
                Mail::to($user)->send(new NewsletterEmail($newsletter));
            } catch (Exception $exception) {

            }
            DB::table('customer_newsletter')
                ->where('newsletter_id', $newsletter->id)
                ->where("customer_id", $user->id)
                ->update(['sent_at' => Carbon::now()]);
            $newsletter->progress = round($newsletter->sentUsers()->count() / $newsletter->customers()->count() * 100);
            $newsletter->save();
        }

        echo "Sent to next batch of receivers \n";
    }
}

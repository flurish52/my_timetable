<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FcmPushService;
use App\Services\QotdResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendQuestionOfTheDayPush extends Command
{
    protected $signature = 'qotd:notify';
    protected $description = 'Notify users that today\'s Question of the Day is ready';

    public function __construct(
        private FcmPushService $fcm,
        private QotdResolver $qotdResolver
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $today = today();

        User::with('deviceTokens')
            ->whereHas('deviceTokens')
            ->chunk(100, function ($users) use ($today) {
                foreach ($users as $user) {
                    $qotd = $this->qotdResolver->resolveForUser($user, $today);

                    if (! $qotd) {
                        continue;
                    }

                    $qotd->loadMissing('question.pastQuestion.course');

                    $tokens = $user->deviceTokens->pluck('token')->toArray();

                    [$title, $body] = $this->buildMessage($qotd);

                    $this->fcm->sendToTokens($tokens, $title, $body, [
                        'url' => '/questions-of-the-day',
                    ]);
                }
            });

        $this->info('QOTD push notifications sent for ' . $today->toDateString());

        return self::SUCCESS;
    }

    private function buildMessage($qotd): array
    {
        $question = $qotd->question;
        $courseCode = $question->pastQuestion?->course?->code;

        $title = $courseCode
            ? "🧠 {$courseCode} — Question of the Day"
            : '🧠 Question of the Day';

        $preview = Str::limit(strip_tags($question->question_text), 90);

        $body = "{$preview} — Tap to answer & keep your streak 🔥";

        return [$title, $body];
    }
}

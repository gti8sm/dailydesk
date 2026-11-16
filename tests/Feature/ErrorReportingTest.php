<?php

namespace Tests\Feature;

use App\Mail\ErrorReportMail;
use App\Services\ErrorReporter;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ErrorReportingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.error_report_force' => true,
            'app.error_report_email' => 'contact@dailydesk.fr',
        ]);
    }

    public function test_error_reporter_sends_email(): void
    {
        Mail::fake();

        (new ErrorReporter())->report(new \RuntimeException('Erreur test DailyDesk'));

        Mail::assertSent(ErrorReportMail::class, 1);
        Mail::assertSent(function (ErrorReportMail $mail) {
            return $mail->message === 'Erreur test DailyDesk'
                && $mail->exceptionClass === \RuntimeException::class
                && $mail->line > 0;
        });
    }

    public function test_same_error_is_throttled(): void
    {
        Mail::fake();

        // Même exception (même classe, fichier, ligne) = même empreinte
        $exception = new \RuntimeException('Erreur répétée');

        $reporter = new ErrorReporter();
        $reporter->report($exception);
        $reporter->report($exception);
        $reporter->report($exception);

        Mail::assertSent(ErrorReportMail::class, 1);
    }

    public function test_benign_exceptions_are_not_reported(): void
    {
        Mail::fake();

        (new ErrorReporter())->report(new NotFoundHttpException('Page introuvable'));

        Mail::assertNothingSent();
    }

    public function test_nothing_sent_when_not_forced(): void
    {
        config(['app.error_report_force' => false]);
        Mail::fake();

        (new ErrorReporter())->report(new \RuntimeException('Erreur ignorée en tests'));

        Mail::assertNothingSent();
    }
}

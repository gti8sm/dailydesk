<?php

namespace App\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Notifie par email les erreurs applicatives en production
 * (destination : config app.error_report_email, contact@dailydesk.fr).
 * Une même erreur (classe + fichier + ligne) n'est envoyée qu'une fois
 * toutes les 15 minutes pour éviter le spam en cas de boucle d'erreurs.
 */
class ErrorReporter
{
    private const THROTTLE_MINUTES = 15;

    public function report(\Throwable $e): void
    {
        try {
            if (!$this->shouldReport($e)) {
                return;
            }

            $recipient = (string) config('app.error_report_email');

            if ($recipient === '') {
                return;
            }

            $fingerprint = hash('sha256', get_class($e) . '|' . $e->getFile() . '|' . $e->getLine());

            if (!Cache::add('error_report_' . $fingerprint, true, now()->addMinutes(self::THROTTLE_MINUTES))) {
                return;
            }

            $user = auth()->user();

            Mail::to($recipient)->send(new \App\Mail\ErrorReportMail(
                exceptionClass: get_class($e),
                message: $e->getMessage(),
                file: $e->getFile(),
                line: $e->getLine(),
                trace: mb_substr((string) $e->getTraceAsString(), 0, 4000),
                url: request()->fullUrl() ?: 'CLI',
                user: $user?->email,
                tenant: $user?->tenant_id,
            ));
        } catch (\Throwable $reportingError) {
            // Le reporting ne doit jamais faire échouer l'application
            Log::warning('Envoi du rapport d\'erreur impossible : ' . $reportingError->getMessage());
        }
    }

    private function shouldReport(\Throwable $e): bool
    {
        // Uniquement en production (ERROR_REPORT_FORCE=true pour tester)
        if (!app()->environment('production') && !config('app.error_report_force')) {
            return false;
        }

        // Erreurs HTTP attendues (404, 403...) : sans intérêt
        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
            return false;
        }

        foreach ([
            AuthenticationException::class,
            AuthorizationException::class,
            ValidationException::class,
            TokenMismatchException::class,
            ModelNotFoundException::class,
            NotFoundHttpException::class,
            TooManyRequestsHttpException::class,
            ThrottleRequestsException::class,
            \Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException::class,
        ] as $ignored) {
            if ($e instanceof $ignored) {
                return false;
            }
        }

        return true;
    }
}

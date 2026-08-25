<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\NewsletterSubscriptionRequest;
use App\Services\Newsletter\NewsletterSubscriptionResult;
use App\Services\Newsletter\NewsletterSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewsletterSubscriptionController extends Controller
{
    public function __construct(
        private readonly NewsletterSubscriptionService $subscriptions,
    ) {}

    public function __invoke(NewsletterSubscriptionRequest $request): JsonResponse|RedirectResponse
    {
        if (trim((string) $request->validated('website', '')) !== '') {
            return $this->respond($request, NewsletterSubscriptionResult::accepted());
        }

        try {
            $result = $this->subscriptions->subscribe(
                (string) $request->validated('email'),
                (string) app()->getLocale(),
                $request->ip(),
            );
        } catch (Throwable $exception) {
            Log::error('Newsletter subscription failed unexpectedly.', [
                'error_code' => 'unexpected_error',
                'exception' => $exception::class,
            ]);

            $result = NewsletterSubscriptionResult::unavailable();
        }

        return $this->respond($request, $result);
    }

    private function respond(
        NewsletterSubscriptionRequest $request,
        NewsletterSubscriptionResult $result,
    ): JsonResponse|RedirectResponse {
        $message = (string) __($result->messageKey);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $result->successful,
                'message' => $message,
            ], $result->httpStatus);
        }

        if ($result->successful) {
            return redirect()->back()->with('newsletter_success', $message);
        }

        return redirect()->back()
            ->withInput($request->safe()->only('email'))
            ->with('newsletter_error', $message);
    }
}

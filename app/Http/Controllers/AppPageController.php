<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Plugins\Shared\Models\AppPage;

class AppPageController extends Controller
{
    /**
     * Display a hosted page.
     */
    public function show(App $app, string $slug)
    {
        // Find the page by slug
        $page = AppPage::where('app_id', $app->id)
            ->where('slug', $slug)
            ->first();

        // Page not found or is draft
        if (! $page || $page->status !== 'published') {
            return $this->renderUnavailablePage(
                'Page Not Found',
                'The page you are looking for does not exist.'
            );
        }

        // Check if user's plan allows hosted pages
        $user = $app->user;

        if (! $user) {
            return $this->renderUnavailablePage();
        }

        // Check plan access
        if (! $user->canUseHostedPages()) {
            return $this->renderUnavailablePage();
        }

        // Check subscription status (except for free plans)
        $subscription = $user->activeSubscription;
        $plan = $user->plan;

        if ($plan && $plan->price > 0) {
            // Paid plan requires active subscription
            if (! $subscription || $subscription->status !== 'active') {
                return $this->renderUnavailablePage();
            }
        }

        return view('hosted-page', [
            'page' => $page,
            'app' => $app,
        ]);
    }

    /**
     * Render the unavailable page.
     */
    private function renderUnavailablePage(
        string $title = 'Page Unavailable',
        string $message = 'This page has been disabled. Please contact the app administrator.'
    ) {
        return view('hosted-page-unavailable', compact('title', 'message'));
    }
}

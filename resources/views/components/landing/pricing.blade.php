@php
    $plans = \App\Models\Plan::where('status', 'active')->orderBy('price', 'asc')->get();
    $pricingTitle = \App\Models\SystemSetting::get('pricing_section_title', 'Simple, transparent pricing');
    $pricingDescription = \App\Models\SystemSetting::get('pricing_section_description', 'Choose the perfect plan for your needs. All plans include core features with different build credit allocations.');
    $popularBadgeText = \App\Models\SystemSetting::get('pricing_popular_badge_text', 'Most Popular');
    $freePriceText = \App\Models\SystemSetting::get('pricing_free_text', 'Free');
    $noPlansMessage = \App\Models\SystemSetting::get('pricing_no_plans_message', 'Pricing plans coming soon. Please check back later.');
@endphp

<section id="pricing" class="bg-gray-50 dark:bg-gray-800">
    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-md text-center mb-8 lg:mb-12">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                {{ $pricingTitle }}
            </h2>
            <p class="mb-5 font-light text-gray-500 sm:text-xl dark:text-gray-400">
                {{ $pricingDescription }}
            </p>
        </div>

        @if($plans->count() > 0)
            <div class="space-y-8 lg:grid lg:grid-cols-3 sm:gap-6 xl:gap-10 lg:space-y-0">
                @foreach($plans as $plan)
                    @php
                        $isFreePlan = $plan->price == 0;
                        $isProPlan = strtolower($plan->name) === 'pro';
                        $isEnterprisePlan = strtolower($plan->name) === 'enterprise';
                        $isPopular = $isProPlan; // Pro plan gets the popular badge
                    @endphp
                    <div class="flex flex-col p-6 mx-auto max-w-lg text-center text-gray-900 bg-white rounded-lg border @if($isPopular) border-blue-600 @else border-gray-100 @endif shadow-lg dark:border-gray-600 xl:p-8 dark:bg-gray-800 dark:text-white @if($isPopular) relative ring-2 ring-blue-600 @endif">
                        @if($isPopular)
                            <div class="absolute top-0 right-0 -mt-4 -mr-4">
                                <span class="inline-flex items-center bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    {{ $popularBadgeText }}
                                </span>
                            </div>
                        @endif

                        <h3 class="mb-4 text-2xl font-semibold">{{ $plan->name }}</h3>
                        @if(isset($plan->description) && $plan->description)
                            <p class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
                                {{ $plan->description }}
                            </p>
                        @endif

                        <div class="flex justify-center items-baseline my-8">
                            <span class="mr-2 text-5xl font-extrabold">
                                @if($plan->price > 0)
                                    ${{ number_format($plan->price, 0) }}
                                @else
                                    {{ $freePriceText }}
                                @endif
                            </span>
                            @if($plan->price > 0)
                                <span class="text-gray-500 dark:text-gray-400">/{{ $plan->interval_unit ?? 'month' }}</span>
                            @endif
                        </div>

                        <!-- Features List -->
                        <ul role="list" class="mb-8 space-y-3 text-left text-sm">
                            @php
                                $features = $plan->all_features;
                            @endphp

                            @foreach($features as $feature)
                                @php
                                    $included = is_array($feature) ? ($feature['included'] ?? true) : true;
                                    $featureName = is_array($feature) ? $feature['name'] : $feature;
                                @endphp
                                <li class="flex items-center {{ $included ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500' }}">
                                    @if($included)
                                        <svg class="w-4 h-4 text-green-500 me-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-500 me-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    <span>{{ $featureName }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @php
                            // Custom CTA text per plan type
                            if ($isFreePlan) {
                                $buttonText = 'Start Free';
                            } elseif ($isProPlan) {
                                $buttonText = 'Go Pro';
                            } else {
                                $buttonText = 'Get Enterprise';
                            }
                        @endphp
                        <a href="{{ route('register') }}" wire:navigate class="@if($isPopular) text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 @else text-blue-600 bg-blue-50 hover:bg-blue-100 focus:ring-4 focus:ring-blue-200 dark:text-white dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900 @endif font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            {{ $buttonText }}
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Fallback if no plans in database -->
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">{{ $noPlansMessage }}</p>
            </div>
        @endif
    </div>
</section>

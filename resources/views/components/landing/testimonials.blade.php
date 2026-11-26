@php
    $title = \App\Models\SystemSetting::get('testimonials_title', 'What Our Customers Say');
    $subtitle = \App\Models\SystemSetting::get('testimonials_subtitle', 'Join thousands of satisfied developers and businesses');

    // Default testimonials - can be overridden via SystemSetting
    $defaultTestimonials = [
        [
            'quote' => 'This platform has transformed how we deliver cross-platform solutions to our clients. What used to take weeks now takes minutes. The AI assistant is incredibly helpful for quick setups.',
            'author' => 'Sarah Johnson',
            'role' => 'Founder, WebCraft Agency',
            'rating' => 5,
        ],
        [
            'quote' => 'We converted our e-commerce website to native apps and saw a 40% increase in user engagement. The push notifications feature alone has been a game-changer.',
            'author' => 'Michael Chen',
            'role' => 'CEO, ShopEasy',
            'rating' => 5,
        ],
        [
            'quote' => 'As a developer, I appreciate how this tool handles all the complexity while giving me full control over customization. The REST API integration is fantastic.',
            'author' => 'David Rodriguez',
            'role' => 'Senior Developer, TechStart',
            'rating' => 5,
        ],
    ];

    $testimonials = \App\Models\SystemSetting::get('testimonials', $defaultTestimonials);
    if (is_string($testimonials)) {
        $testimonials = json_decode($testimonials, true) ?? $defaultTestimonials;
    }
@endphp

<section id="testimonials" class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <!-- Section Header -->
        <div class="max-w-screen-md mb-8 lg:mb-12 mx-auto text-center">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                {{ $title }}
            </h2>
            <p class="text-gray-500 sm:text-xl dark:text-gray-400">
                {{ $subtitle }}
            </p>
        </div>

        <!-- Testimonials Carousel -->
        <div x-data="{
            activeSlide: 0,
            totalSlides: {{ count($testimonials) }},
            autoplay: null,
            startAutoplay() {
                this.autoplay = setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                }, 5000);
            },
            stopAutoplay() {
                if (this.autoplay) {
                    clearInterval(this.autoplay);
                }
            },
            goToSlide(index) {
                this.stopAutoplay();
                this.activeSlide = index;
                this.startAutoplay();
            },
            nextSlide() {
                this.goToSlide((this.activeSlide + 1) % this.totalSlides);
            },
            prevSlide() {
                this.goToSlide((this.activeSlide - 1 + this.totalSlides) % this.totalSlides);
            }
        }"
        x-init="startAutoplay()"
        @mouseenter="stopAutoplay()"
        @mouseleave="startAutoplay()"
        class="relative max-w-4xl mx-auto">

            <!-- Carousel Container -->
            <div class="overflow-hidden relative rounded-xl">
                <div class="flex transition-transform duration-500 ease-in-out"
                     :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
                    @foreach($testimonials as $index => $testimonial)
                        <div class="w-full flex-shrink-0 px-4">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 md:p-10 text-center">
                                <!-- Stars Rating -->
                                <div class="flex justify-center mb-6">
                                    @for($i = 0; $i < ($testimonial['rating'] ?? 5); $i++)
                                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>

                                <!-- Quote -->
                                <blockquote class="mb-8">
                                    <p class="text-lg md:text-xl font-medium text-gray-700 dark:text-gray-300 leading-relaxed">
                                        "{{ $testimonial['quote'] }}"
                                    </p>
                                </blockquote>

                                <!-- Author -->
                                <div class="flex items-center justify-center space-x-4">
                                    <!-- Avatar -->
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($testimonial['author'] ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $testimonial['author'] ?? 'Anonymous' }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $testimonial['role'] ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button @click="prevSlide()"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 md:-translate-x-12 w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="nextSlide()"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 md:translate-x-12 w-10 h-10 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Dots Navigation -->
            <div class="flex justify-center mt-6 space-x-2">
                @foreach($testimonials as $index => $testimonial)
                    <button @click="goToSlide({{ $index }})"
                            :class="activeSlide === {{ $index }} ? 'bg-blue-600 w-8' : 'bg-gray-300 dark:bg-gray-600 w-2'"
                            class="h-2 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>

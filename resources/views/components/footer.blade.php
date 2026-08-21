<footer class="border-t border-brand-100 bg-white/90">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <!-- Brand -->
            <div>
                <a href="{{ route('home') }}" class="mb-4 flex items-center">
                    <img src="{{ asset('images/jobseekers-logo.png') }}" alt="Job Seekers" class="brand-logo">
                </a>
                <p class="text-sm text-slate-600">Finding the right talent for Kenya's growing job market.</p>
            </div>

            <!-- Product -->
            <div>
                <h3 class="text-sm font-semibold text-neutral-900 mb-4">Product</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Browse Jobs</a></li>
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Companies</a></li>
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Pricing</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h3 class="text-sm font-semibold text-neutral-900 mb-4">Company</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">About</a></li>
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Blog</a></li>
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Careers</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-sm font-semibold text-neutral-900 mb-4">Legal</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Privacy</a></li>
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Terms</a></li>
                    <li><a href="#" class="text-neutral-600 hover:text-neutral-900">Contact</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom section -->
        <div class="border-t border-neutral-200 pt-8 flex flex-col sm:flex-row justify-between items-center">
            <p class="text-sm text-neutral-600">&copy; {{ date('Y') }} {{ config('app.name', 'JobSeekers') }}. All rights reserved.</p>
            <div class="flex gap-6 mt-4 sm:mt-0">
                <a href="#" class="text-neutral-600 hover:text-neutral-900 text-sm">Twitter</a>
                <a href="#" class="text-neutral-600 hover:text-neutral-900 text-sm">LinkedIn</a>
                <a href="#" class="text-neutral-600 hover:text-neutral-900 text-sm">GitHub</a>
            </div>
        </div>
    </div>
</footer>

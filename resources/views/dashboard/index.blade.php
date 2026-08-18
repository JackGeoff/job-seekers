@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-neutral-50" x-data="dashboardModule()">
    <!-- Main content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <!-- Welcome header -->
        <div class="mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-neutral-900 mb-2">
                Welcome back, {{ Auth::user()->name }}!
            </h1>
            <p class="text-lg text-neutral-600">Here are today's top job opportunities for you</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="card cursor-pointer transition-transform hover:scale-105" @click="activeStatTab = 'applications'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 mb-1">Active Applications</p>
                        <p class="text-3xl font-bold text-neutral-900">0</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card cursor-pointer transition-transform hover:scale-105" @click="activeStatTab = 'saved'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 mb-1">Saved Jobs</p>
                        <p class="text-3xl font-bold text-neutral-900">0</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 mb-1">Profile Views</p>
                        <p class="text-3xl font-bold text-neutral-900">12</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-600 mb-1">Recommendations</p>
                        <p class="text-3xl font-bold text-neutral-900">8</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Jobs -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-neutral-900">Featured Opportunities</h2>
                    <p class="text-neutral-600 mt-1">Jobs tailored to your profile</p>
                </div>
                <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm">View all →</a>
            </div>

            <div class="space-y-4">
                <!-- Job Card 1 -->
                <div class="card-hover space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-neutral-200 rounded-lg"></div>
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-900">Senior Product Designer</h3>
                                    <p class="text-sm text-neutral-600">TechFlow Inc.</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">Design</span>
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">Figma</span>
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">UI/UX</span>
                            </div>
                            <p class="text-neutral-600 text-sm mt-3">Join a fast-growing team building the future of product design. Remote-first culture, competitive compensation, and amazing benefits.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-neutral-900 mb-1">KES 150K+</p>
                            <p class="text-xs text-neutral-500 mb-3">per month</p>
                            <a href="#" class="btn btn-primary btn-sm">Apply</a>
                        </div>
                    </div>
                </div>

                <!-- Job Card 2 -->
                <div class="card-hover space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-neutral-200 rounded-lg"></div>
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-900">Full Stack Engineer</h3>
                                    <p class="text-sm text-neutral-600">Digital Innovations Ltd</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">Backend</span>
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">React</span>
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">Node.js</span>
                            </div>
                            <p class="text-neutral-600 text-sm mt-3">Build scalable applications for millions of users. We invest in our engineers' growth and provide cutting-edge tools.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-neutral-900 mb-1">KES 180K+</p>
                            <p class="text-xs text-neutral-500 mb-3">per month</p>
                            <a href="#" class="btn btn-primary btn-sm">Apply</a>
                        </div>
                    </div>
                </div>

                <!-- Job Card 3 -->
                <div class="card-hover space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-12 h-12 bg-neutral-200 rounded-lg"></div>
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-900">Product Manager</h3>
                                    <p class="text-sm text-neutral-600">StartupXyz</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">Product</span>
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">Strategy</span>
                                <span class="inline-block px-3 py-1 bg-neutral-100 text-neutral-700 rounded-full text-xs font-medium">Analytics</span>
                            </div>
                            <p class="text-neutral-600 text-sm mt-3">Lead product vision and strategy for an innovative SaaS platform. Work with world-class engineers and designers.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-neutral-900 mb-1">KES 200K+</p>
                            <p class="text-xs text-neutral-500 mb-3">per month</p>
                            <a href="#" class="btn btn-primary btn-sm">Apply</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Recommendations -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <h3 class="text-xl font-bold text-neutral-900 mb-6">Recent Activity</h3>
                <div class="card space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-neutral-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-success-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">Application sent to TechFlow Inc.</p>
                                <p class="text-xs text-neutral-500">2 days ago</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pb-4 border-b border-neutral-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">Saved "Full Stack Engineer" at Digital Innovations</p>
                                <p class="text-xs text-neutral-500">1 week ago</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-neutral-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900">Profile updated with new skills</p>
                                <p class="text-xs text-neutral-500">2 weeks ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div>
                <h3 class="text-xl font-bold text-neutral-900 mb-6">Quick Links</h3>
                <div class="space-y-3">
                    <a href="#" class="block card-hover p-4">
                        <h4 class="font-medium text-neutral-900 mb-1">Complete Your Profile</h4>
                        <p class="text-sm text-neutral-600">Add more details to increase your visibility</p>
                    </a>

                    <a href="#" class="block card-hover p-4">
                        <h4 class="font-medium text-neutral-900 mb-1">Browse All Jobs</h4>
                        <p class="text-sm text-neutral-600">Explore opportunities matching your skills</p>
                    </a>

                    <a href="#" class="block card-hover p-4">
                        <h4 class="font-medium text-neutral-900 mb-1">Settings & Preferences</h4>
                        <p class="text-sm text-neutral-600">Manage your job alerts and preferences</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardModule() {
    return {
        activeStatTab: 'applications',
        showJobFilters: false,

        init() {
            console.log('Dashboard initialized with Alpine.js');
        },

        toggleFilters() {
            this.showJobFilters = !this.showJobFilters;
        },

        applyJob(jobId) {
            // This can be connected to an API call
            alert('Application submitted for job ' + jobId);
            console.log('Applied to job:', jobId);
        }
    };
}
</script>
@endsection

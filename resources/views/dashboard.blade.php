<x-app-layout>
    <!-- Breadcrumb and Header -->
    <div class="flex flex-col gap-2">
        <div class="flex items-center text-sm text-[#617589] dark:text-slate-400 font-medium">
            <span>Home</span>
            <span class="mx-2">/</span>
            <span class="text-[#111418] dark:text-white">Dashboard</span>
        </div>
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-[#111418] dark:text-white text-3xl font-bold leading-tight tracking-tight">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}</h1>
                <p class="text-[#617589] dark:text-slate-400 mt-1">Here is an overview of your student ID generation system.</p>
            </div>
            <button class="hidden md:flex cursor-pointer items-center gap-2 justify-center overflow-hidden rounded-lg h-10 px-4 bg-white border border-gray-200 hover:bg-gray-50 dark:bg-[#2a3b4d] dark:border-gray-700 dark:hover:bg-[#374a60] text-[#111418] dark:text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors shadow-sm">
                <span class="material-symbols-outlined text-lg">settings</span>
                <span>System Settings</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Template Status Card -->
        <div class="flex flex-col gap-3 rounded-xl p-5 bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-primary">
                    <span class="material-symbols-outlined icon-filled">description</span>
                </div>
                @if($isTemplateActive)
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">Active</span>
                @else
                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400 text-xs font-bold rounded-full">Inactive</span>
                @endif
            </div>
            <div>
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Template Status</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ $templateStatus }}</p>
            </div>
        </div>

        <!-- Total Students Card -->
        <div class="flex flex-col gap-3 rounded-xl p-5 bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-purple-600">
                    <span class="material-symbols-outlined icon-filled">groups</span>
                </div>
                @if($newStudentsCount > 0)
                <span class="text-[#078838] text-xs font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">trending_up</span> +{{ $newStudentsCount }}
                </span>
                @endif
            </div>
            <div>
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Total Students</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ number_format($totalStudents) }}</p>
            </div>
        </div>

        <!-- Generated KTMs Card -->
        <div class="flex flex-col gap-3 rounded-xl p-5 bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-600">
                    <span class="material-symbols-outlined icon-filled">badge</span>
                </div>
                <span class="text-[#617589] dark:text-slate-400 text-xs font-medium">{{ $generatedPercentage }}% Done</span>
            </div>
            <div>
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Generated KTMs</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ number_format($generatedKtms) }}</p>
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 mt-2">
                    <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $generatedPercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Failed/Errors Card -->
        <div class="flex flex-col gap-3 rounded-xl p-5 bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg text-red-600">
                    <span class="material-symbols-outlined icon-filled">error</span>
                </div>
            </div>
            <div>
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Failed/Errors</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ number_format($failedKtms) }}</p>
                @if($failedKtms > 0)
                <p class="text-red-500 text-xs mt-1">Requires attention</p>
                @else
                <p class="text-green-500 text-xs mt-1">All clear</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-col gap-4">
        <h3 class="text-[#111418] dark:text-white text-lg font-bold leading-tight">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button class="group flex items-center p-4 gap-4 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] hover:border-primary hover:ring-1 hover:ring-primary transition-all text-left">
                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">upload_file</span>
                </div>
                <div>
                    <h4 class="text-[#111418] dark:text-white font-bold text-sm">Upload Template</h4>
                    <p class="text-[#617589] dark:text-slate-400 text-xs mt-1">Configure new card design</p>
                </div>
            </button>

            <button class="group flex items-center p-4 gap-4 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] hover:border-primary hover:ring-1 hover:ring-primary transition-all text-left">
                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">manufacturing</span>
                </div>
                <div>
                    <h4 class="text-[#111418] dark:text-white font-bold text-sm">Start Generation</h4>
                    <p class="text-[#617589] dark:text-slate-400 text-xs mt-1">Process pending students</p>
                </div>
            </button>

            <button class="group flex items-center p-4 gap-4 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] hover:border-primary hover:ring-1 hover:ring-primary transition-all text-left">
                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">folder_zip</span>
                </div>
                <div>
                    <h4 class="text-[#111418] dark:text-white font-bold text-sm">Download All</h4>
                    <p class="text-[#617589] dark:text-slate-400 text-xs mt-1">Get ZIP archives of KTMs</p>
                </div>
            </button>
        </div>
    </div>

    <!-- Recent Batch Activity -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-[#111418] dark:text-white text-lg font-bold leading-tight">Recent Batch Activity</h3>
            <a class="text-primary text-sm font-bold hover:underline" href="#">View All</a>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-[#617589] dark:text-slate-400 uppercase bg-gray-50 dark:bg-[#23303e] border-b border-[#e5e7eb] dark:border-[#2a3b4d]">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Batch ID</th>
                            <th scope="col" class="px-6 py-3 font-medium">Action</th>
                            <th scope="col" class="px-6 py-3 font-medium">Status</th>
                            <th scope="col" class="px-6 py-3 font-medium">Processed</th>
                            <th scope="col" class="px-6 py-3 font-medium text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb] dark:divide-[#2a3b4d]">
                        @forelse($recentActivities as $activity)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#23303e] transition-colors">
                            <td class="px-6 py-4 font-medium text-[#111418] dark:text-white">{{ $activity->batch_id }}</td>
                            <td class="px-6 py-4 text-[#111418] dark:text-white">{{ $activity->action }}</td>
                            <td class="px-6 py-4">
                                @php
                                $statusColors = [
                                'completed' => 'green',
                                'failed' => 'red',
                                'processing' => 'yellow',
                                'uploaded' => 'blue',
                                'pending' => 'gray',
                                ];
                                $color = $statusColors[$activity->status] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 px-2 py-1 text-xs font-semibold text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-{{ $color }}-600 dark:bg-{{ $color }}-400"></span>
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[#617589] dark:text-slate-400">
                                @if($activity->processed_count > 0)
                                {{ $activity->processed_count }} Students
                                @elseif($activity->failed_count > 0)
                                {{ $activity->failed_count }} Failed
                                @else
                                -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-[#617589] dark:text-slate-400">{{ $activity->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[#617589] dark:text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 block">inbox</span>
                                No batch activities yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentActivities->count() > 0)
            <div class="px-6 py-4 border-t border-[#e5e7eb] dark:border-[#2a3b4d] bg-gray-50 dark:bg-[#23303e] flex justify-between items-center">
                <span class="text-xs text-[#617589] dark:text-slate-400">Showing last {{ $recentActivities->count() }} activities</span>
                <div class="flex gap-2">
                    <button class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-[#617589] dark:text-slate-400 disabled:opacity-50">
                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                    </button>
                    <button class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 text-[#617589] dark:text-slate-400">
                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-8 mb-6 text-center text-xs text-[#617589] dark:text-slate-500">
        <p>© {{ date('Y') }} KTM Generation System. All rights reserved.</p>
    </footer>
</x-app-layout>

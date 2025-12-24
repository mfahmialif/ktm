<x-admin-layout>
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
        <!-- Total Templates Card -->
        <div class="flex flex-col gap-3 rounded-xl p-5 bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-primary">
                    <span class="material-symbols-outlined icon-filled">description</span>
                </div>
            </div>
            <div>
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Jumlah Template</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ number_format($totalTemplates) }}</p>
            </div>
        </div>

        <!-- Generated KTMs Card -->
        <div class="flex flex-col gap-3 rounded-xl p-5 bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-600">
                    <span class="material-symbols-outlined icon-filled">badge</span>
                </div>
                <span class="text-[#617589] dark:text-slate-400 text-xs font-medium">{{ $generatedPercentage }}% Coverage</span>
            </div>
            <div>
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Jumlah KTM Generated</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ number_format($generatedKtms) }}</p>
            </div>
        </div>

        <!-- Not Generated KTMs Card -->
        <div class="flex flex-col gap-3 rounded-xl p-5 bg-white dark:bg-[#1a2632] border border-[#e5e7eb] dark:border-[#2a3b4d] shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-gray-600 dark:text-gray-400">
                    <span class="material-symbols-outlined icon-filled">pending_actions</span>
                </div>
            </div>
            <div>
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Jumlah KTM Belum Digenerate</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ number_format($notGeneratedKtms) }}</p>
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
                <p class="text-[#617589] dark:text-slate-400 text-sm font-medium">Jumlah Students</p>
                <p class="text-[#111418] dark:text-white text-2xl font-bold mt-1">{{ number_format($totalStudents) }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-col gap-4">
        <h3 class="text-[#111418] dark:text-white text-lg font-bold leading-tight">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('templates.index') }}" class="group flex items-center p-4 gap-4 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] hover:border-primary hover:ring-1 hover:ring-primary transition-all text-left">
                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">upload_file</span>
                </div>
                <div>
                    <h4 class="text-[#111418] dark:text-white font-bold text-sm">Manage Templates</h4>
                    <p class="text-[#617589] dark:text-slate-400 text-xs mt-1">Configure card designs</p>
                </div>
            </a>

            <a href="{{ route('ktm-generator.index') }}" class="group flex items-center p-4 gap-4 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] hover:border-primary hover:ring-1 hover:ring-primary transition-all text-left">
                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">manufacturing</span>
                </div>
                <div>
                    <h4 class="text-[#111418] dark:text-white font-bold text-sm">Start Generation</h4>
                    <p class="text-[#617589] dark:text-slate-400 text-xs mt-1">Process pending students</p>
                </div>
            </a>

            <a href="{{ route('download-jobs.index') }}" class="group flex items-center p-4 gap-4 bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] hover:border-primary hover:ring-1 hover:ring-primary transition-all text-left">
                <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined">history</span>
                </div>
                <div>
                    <h4 class="text-[#111418] dark:text-white font-bold text-sm">Download History</h4>
                    <p class="text-[#617589] dark:text-slate-400 text-xs mt-1">Get ZIP archives of KTMs</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Download History -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-[#111418] dark:text-white text-lg font-bold leading-tight">Download History</h3>
            <a class="text-primary text-sm font-bold hover:underline" href="{{ route('download-jobs.index') }}">View All</a>
        </div>

        <div class="bg-white dark:bg-[#1a2632] rounded-xl border border-[#e5e7eb] dark:border-[#2a3b4d] overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-[#617589] dark:text-slate-400 uppercase bg-gray-50 dark:bg-[#23303e] border-b border-[#e5e7eb] dark:border-[#2a3b4d]">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Download ID</th>
                            <th scope="col" class="px-6 py-3 font-medium">Template</th>
                            <th scope="col" class="px-6 py-3 font-medium">Status</th>
                            <th scope="col" class="px-6 py-3 font-medium text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb] dark:divide-[#2a3b4d]">
                        @forelse($downloadHistory as $job)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#23303e] transition-colors">
                            <td class="px-6 py-4 font-medium text-[#111418] dark:text-white">{{ $job->download_id }}</td>
                            <td class="px-6 py-4 text-[#111418] dark:text-white">{{ $job->template->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                $statusColors = [
                                'completed' => 'green',
                                'failed' => 'red',
                                'processing' => 'yellow',
                                'pending' => 'gray',
                                ];
                                $color = $statusColors[$job->status] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 px-2 py-1 text-xs font-semibold text-{{ $color }}-600 dark:text-{{ $color }}-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-{{ $color }}-600 dark:bg-{{ $color }}-400"></span>
                                    {{ ucfirst($job->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-[#617589] dark:text-slate-400">{{ $job->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-[#617589] dark:text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 block">history</span>
                                No download history yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-8 mb-6 text-center text-xs text-[#617589] dark:text-slate-500">
        <p>© {{ date('Y') }} KTM Generation System. All rights reserved.</p>
    </footer>
</x-admin-layout>

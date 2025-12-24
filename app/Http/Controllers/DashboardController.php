<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\KtmTemplate;
use App\Models\BatchActivity;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        // Get active template status
        $activeTemplate = KtmTemplate::getActive();
        $templateStatus = $activeTemplate && $activeTemplate->isConfigured() ? 'Configured' : 'Not Configured';
        $isTemplateActive = $activeTemplate !== null;

        // Get student statistics
        $totalStudents = Student::count();
        $generatedKtms = Student::generated()->count();
        $failedKtms = 0;

        // Calculate percentage
        $generatedPercentage = $totalStudents > 0
            ? round(($generatedKtms / $totalStudents) * 100)
            : 0;

        // Get recent batch activities
        $recentActivities = BatchActivity::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get growth (students added in last 30 days)
        $newStudentsCount = Student::where('created_at', '>=', now()->subDays(30))->count();

        return view('admin.dashboard', [
            'templateStatus' => $templateStatus,
            'isTemplateActive' => $isTemplateActive,
            'totalStudents' => $totalStudents,
            'generatedKtms' => $generatedKtms,
            'failedKtms' => $failedKtms,
            'generatedPercentage' => $generatedPercentage,
            'recentActivities' => $recentActivities,
            'newStudentsCount' => $newStudentsCount,
        ]);
    }
}

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
        $activeTemplate = KtmTemplate::where('status', KtmTemplate::STATUS_ACTIVE)->first();
        $templateStatus = $activeTemplate && $activeTemplate->isConfigured() ? 'Configured' : 'Not Configured';
        $isTemplateActive = $activeTemplate !== null;

        // Get statistics
        $totalStrategies = 0; // Unused
        $totalTemplates = KtmTemplate::count();
        $totalStudents = Student::count();

        // Count generated KTMs (total cards generated)
        $generatedKtms = \App\Models\StudentKtmStatus::generated()->count();

        // Count students without generated KTM (Not Generated)
        // We assume this means students who don't have ANY generated KTM yet
        // OR filtering by active template would be better, but user didn't specify.
        // Let's use: Total Students - Students who have generated at least one KTM.
        $studentsWithKtm = \App\Models\StudentKtmStatus::generated()->distinct('student_id')->count('student_id');
        $notGeneratedKtms = $totalStudents - $studentsWithKtm;

        $failedKtms = \App\Models\StudentKtmStatus::error()->count();

        // Calculate percentage (based on students coverage)
        $generatedPercentage = $totalStudents > 0
            ? round(($studentsWithKtm / $totalStudents) * 100)
            : 0;

        // Get recent download history
        $downloadHistory = \App\Models\KtmDownloadJob::with('template')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get growth (students added in last 30 days)
        $newStudentsCount = Student::where('created_at', '>=', now()->subDays(30))->count();

        return view('admin.dashboard', [
            'templateStatus' => $templateStatus,
            'isTemplateActive' => $isTemplateActive,
            'totalTemplates' => $totalTemplates,
            'totalStudents' => $totalStudents,
            'generatedKtms' => $generatedKtms,
            'notGeneratedKtms' => $notGeneratedKtms,
            'failedKtms' => $failedKtms,
            'generatedPercentage' => $generatedPercentage,
            'downloadHistory' => $downloadHistory,
            'newStudentsCount' => $newStudentsCount,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\KtmDownloadJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KtmDownloadJobController extends Controller
{
    /**
     * Display download jobs page
     */
    public function index()
    {
        return view('admin.download-jobs.index');
    }

    /**
     * Download ZIP file
     */
    public function download($id)
    {
        $downloadJob = KtmDownloadJob::findOrFail($id);

        if (!$downloadJob->zip_path || !Storage::disk('public')->exists($downloadJob->zip_path)) {
            abort(404, 'ZIP file not found');
        }

        $fileName = basename($downloadJob->zip_path);

        return Storage::disk('public')->download($downloadJob->zip_path, $fileName);
    }

    /**
     * Delete download job and ZIP file
     */
    public function destroy($id)
    {
        $downloadJob = KtmDownloadJob::findOrFail($id);

        // Delete ZIP file if exists
        if ($downloadJob->zip_path && Storage::disk('public')->exists($downloadJob->zip_path)) {
            Storage::disk('public')->delete($downloadJob->zip_path);
        }

        // Delete job record
        $downloadJob->delete();

        return redirect()->route('download-jobs.index')
            ->with('success', 'Download job berhasil dihapus');
    }
}

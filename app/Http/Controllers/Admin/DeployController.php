<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeploymentLog;
use App\Services\DeployService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeployController extends Controller
{
    public function __construct(private readonly DeployService $deployService)
    {
    }

    /**
     * Show the deployment dashboard with history.
     */
    public function index(): View
    {
        $logs      = DeploymentLog::with('user')->latest()->take(15)->get();
        $isRunning = $this->deployService->isDeploymentRunning();
        $lastLog   = $logs->first();

        return view('admin.deploy.index', compact('logs', 'isRunning', 'lastLog'));
    }

    /**
     * Trigger a new deployment.
     * Protected by CSRF (POST) and admin middleware on the route.
     */
    public function run(Request $request): JsonResponse
    {
        if ($this->deployService->isDeploymentRunning()) {
            return response()->json([
                'error' => 'A deployment is already in progress. Please wait for it to finish.',
            ], 409);
        }

        // Run synchronously — shared hosting rarely supports long-lived queue workers.
        // The SSH timeout config (DEPLOY_TIMEOUT) controls max execution time.
        $log = $this->deployService->deploy($request->user());

        return response()->json([
            'log_id'  => $log->id,
            'status'  => $log->status,
            'success' => $log->isSuccess(),
            'output'  => $log->output,
            'duration'=> $log->durationSeconds(),
        ]);
    }

    /**
     * Return the current status and output of a specific deployment log.
     * Used by the frontend to poll progress while a deployment is running.
     */
    public function status(DeploymentLog $log): JsonResponse
    {
        return response()->json([
            'log_id'     => $log->id,
            'status'     => $log->status,
            'output'     => $log->output,
            'finished_at'=> optional($log->finished_at)->toDateTimeString(),
            'duration'   => $log->durationSeconds(),
        ]);
    }
}

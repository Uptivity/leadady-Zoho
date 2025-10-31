<?php

namespace App\Http\Controllers;

use App\Jobs\PullLeadsJob;
use App\Models\PullJob;
use Illuminate\Http\Request;

class PullController extends Controller
{
    public function start(Request $request)
    {
        $filters = [
            'industry' => (string) $request->input('industry', ''),
            'company_industry' => (string) $request->input('company_industry', ''),
            'location' => (string) $request->input('location', ''),
        ];
        $required = [
            'phone' => (bool) $request->boolean('require_phone'),
            'email' => (bool) $request->boolean('require_email'),
            'company_email' => (bool) $request->boolean('require_company_email'),
        ];

        $job = PullJob::create([
            'status' => 'queued',
            'filters' => $filters,
            'required' => $required,
            'total' => 0,
            'processed' => 0,
            'failed' => 0,
        ]);

        PullLeadsJob::dispatch($job);

        return response()->json(['id' => $job->id, 'status' => $job->status]);
    }

    public function status(PullJob $pullJob)
    {
        return response()->json([
            'id' => $pullJob->id,
            'status' => $pullJob->status,
            'total' => $pullJob->total,
            'processed' => $pullJob->processed,
            'failed' => $pullJob->failed,
            'error' => $pullJob->error,
        ]);
    }
}


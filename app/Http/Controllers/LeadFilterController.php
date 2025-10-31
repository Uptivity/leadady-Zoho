<?php

namespace App\Http\Controllers;

use App\Services\Contracts\BigQueryServiceInterface;
use Illuminate\Http\Request;

class LeadFilterController extends Controller
{
    public function __construct(private readonly BigQueryServiceInterface $bq) {}

    public function counts(Request $request)
    {
        $filters = $this->extractFilters($request);
        $required = $this->extractRequired($request);
        $counts = $this->bq->getCounts($filters, $required);
        return response()->json($counts);
    }

    public function index(Request $request)
    {
        $filters = $this->extractFilters($request);
        $required = $this->extractRequired($request);
        $page = max(1, (int) $request->integer('page', 1));
        $perPage = 100;
        $result = $this->bq->getLeads($filters, $required, $page, $perPage);
        return response()->json($result);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'industry' => (string) $request->input('industry', ''),
            'company_industry' => (string) $request->input('company_industry', ''),
            'location' => (string) $request->input('location', ''),
        ];
    }

    private function extractRequired(Request $request): array
    {
        return [
            'phone' => (bool) $request->boolean('require_phone'),
            'email' => (bool) $request->boolean('require_email'),
            'company_email' => (bool) $request->boolean('require_company_email'),
        ];
    }
}


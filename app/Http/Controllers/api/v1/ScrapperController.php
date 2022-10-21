<?php

namespace App\Http\Controllers\api\v1;

use App\Services\Interfaces\IScrapperService;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScrapperController extends Controller
{
    private IScrapperService $scrapperService;

    public function __construct(
        IScrapperService $scrapperService,
    )
    {
        $this->scrapperService = $scrapperService;
    }

    public function getScrapperData(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->scrapperService->process();

        return response()->json('success');
    }
}
















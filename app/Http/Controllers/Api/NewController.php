<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\NewService;
use Exception;
use Illuminate\Http\Request;

class NewController extends Controller
{   
    protected $newRepository;
    public function __construct(NewService $newRepository)
    {
        $this->newRepository = $newRepository;
    }
    public function index()
    {
        try {
            return $this->newRepository->getNewData();
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Lỗi',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function show($id)
    {
        return $this->newRepository->getNewById($id);
    }
    public function New()
    {
        return $this->newRepository->getLatestNews();
    }
    public function limitNew()
    {
        return $this->newRepository->getLimitedNews();
    }
}

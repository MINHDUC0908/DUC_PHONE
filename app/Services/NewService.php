<?php

namespace App\Services;

use App\Repositories\NewRepository;

class NewService
{
    protected $newRepository;

    public function __construct(NewRepository $newRepository)
    {
        $this->newRepository = $newRepository;
    }

    public function getNewData()
    {
        $news = $this->newRepository->getNewData();
        return response()->json([
            'status' => true,
            'data' => $news
        ]);
    }

    public function getNewById($id)
    {
        try {
            $news = $this->newRepository->getNewById($id);
            return response()->json([
                'status' => true,
                'data' => $news
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài viết',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function getLatestNews($limit = 6)
    {
        try {
            $news = $this->newRepository->getLatestNews($limit);
            return response()->json([
                'status' => true,
                'data' => $news
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bài viết.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLimitedNews($limit = 3)
    {
        try {
            $news = $this->newRepository->getLimitedNews($limit);
            return response()->json([
                'status' => true,
                'data' => $news
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bài viết.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
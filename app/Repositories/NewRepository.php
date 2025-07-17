<?php

namespace App\Repositories;

use App\Models\News;

class NewRepository
{
    public function getNewData()
    {
        return News::select('id', 'title', 'outstanding', "images", 'created_at')
                    ->orderByDesc('created_at')
                    ->get();
    }

    public function getNewById($id)
    {
        return News::findOrFail($id);
    }

    public function getLatestNews($limit = 6)
    {
        return News::orderBy('created_at', 'DESC')
                    ->take($limit)
                    ->get();
    }
    
    public function getLimitedNews($limit = 3)
    {
        return News::orderBy('created_at', 'DESC')
                    ->take($limit)
                    ->get();
    }
}
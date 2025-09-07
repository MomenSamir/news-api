<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($q = $request->query('q')) {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('title', 'like', "%{$q}%")
                    ->orWhere('summary', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if ($source = $request->query('source')) {
            $query->where('source_name', $source);
        }

        if ($from = $request->query('from')) {
            $query->where('published_at', '>=', $from);
        }

        $perPage = (int) $request->query('per_page', 10);

        $articles = $query->orderBy('published_at', 'desc')->paginate(10);

        // ✅ Return the paginator object directly
        return response()->json($articles);
    }
}

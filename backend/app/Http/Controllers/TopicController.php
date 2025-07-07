<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Resources\TopicCollection;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'category' => 'sometimes|string',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = Topic::query();

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $perPage = $request->get('per_page', 15);
        $topics = $query->orderByDesc('published_at')->paginate($perPage);

        return new TopicCollection($topics);
    }
}

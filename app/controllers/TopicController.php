<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;
use App\Models\Topic;
use App\Validation\TopicQueryValidator;

class TopicController extends Controller
{
    public function index()
    {
        $params = $this->request->getQuery();
        $validator = new TopicQueryValidator();
        $messages = $validator->validate($params);
        if (count($messages)) {
            $errors = [];
            foreach ($messages as $message) {
                $errors[$message->getField()][] = $message->getMessage();
            }
            return $this->response->setStatusCode(422)->setJsonContent(['errors' => $errors]);
        }
        $page = max(1, (int)($params['page'] ?? 1));
        $perPage = min(max(1, (int)($params['per_page'] ?? 10)), 100);
        $category = $params['category'] ?? null;

        $builder = Topic::query();
        if ($category) {
            $builder->where('category = :cat:', ['cat' => $category]);
        }
        $countBuilder = clone $builder;
        $total = $countBuilder->columns('COUNT(*) as c')->execute()->getFirst()['c'];
        $offset = ($page - 1) * $perPage;
        $builder->orderBy('published_at DESC');
        $builder->limit($perPage, $offset);
        $topics = $builder->execute()->toArray();

        return $this->response->setJsonContent([
            'topics' => $topics,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => (int)$total
            ]
        ]);
    }
}

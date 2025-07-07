<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Paginator\Adapter\QueryBuilder as Paginator;
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

        $cacheKey = "topics:" . ($category ?: 'all') . ":{$page}:{$perPage}";
        $cached = $this->di->get('redis')->get($cacheKey);
        if ($cached) {
            return $this->response->setJsonContent(json_decode($cached, true));
        }

        $builder = $this->modelsManager->createBuilder()
            ->from(Topic::class);
        if ($category) {
            $builder->andWhere('category = :cat:', ['cat' => $category]);
        }
        $builder->orderBy('published_at DESC');

        $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder([
            'builder' => $builder,
            'limit'   => $perPage,
            'page'    => $page,
        ]);

        $pageObj = $paginator->paginate();
        $result = [
            'topics' => $pageObj->items->toArray(),
            'meta' => [
                'page' => $pageObj->current,
                'per_page' => $perPage,
                'total' => $pageObj->total_items,
            ]
        ];
        $this->di->get('redis')->setex($cacheKey, 30, json_encode($result));
        return $this->response->setJsonContent($result);
    }
}

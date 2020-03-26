<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * 記事を操作するControllerクラス
 */
class ArticlesController extends AppController
{
    /**
     * GET /articles/index
     */
    public function index(): void
    {
        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    /**
     * GET /articles/view
     *
     * @param string $slug 記事のスラッグ
     */
    public function view(string $slug = null): void
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }
}

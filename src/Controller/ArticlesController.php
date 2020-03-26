<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * 記事を操作するControllerクラス
 */
class ArticlesController extends AppController
{
    /**
     * ArticlesController を初期化する
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
    }

    /**
     * GET /articles/index
     */
    public function index(): void
    {
        $this->Authorization->skipAuthorization();
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
        $this->Authorization->skipAuthorization();
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
        $this->set(compact('article'));
    }

    /**
     * GET /articles/tagged
     *
     * @param string[] ...$tags
     * @return void
     */
    public function tags(string ...$tags): void
    {
        $this->Authorization->skipAuthorization();
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags
        ]);

        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }

    /**
     * GET,POST /articles/add
     */
    public function add(): ?\Cake\Http\Response
    {
        $article = $this->Articles->newEmptyEntity();
        $this->Authorization->authorize($article);

        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            $article->user_id = $this->request->getAttribute('identity')->getIdentifier();

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }

        $tags = $this->Articles->Tags->find('list');
        $this->set('tags', $tags);
        $this->set('article', $article);
        return null;
    }

    /**
     * GET,POST,PUT /articles/edit
     *
     * @param string $slug 記事のスラッグ
     */
    public function edit(string $slug): ?\Cake\Http\Response
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
        $this->Authorization->authorize($article);

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                'accessibleFields' => ['user_id' => false],
            ]);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        $tags = $this->Articles->Tags->find('list');
        $this->set('tags', $tags);
        $this->set('article', $article);
        return null;
    }

    /**
     * POST,DELETE /articles/delete
     *
     * @param string $slug 記事のスラッグ
     */
    public function delete($slug): ?\Cake\Http\Response
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->Authorization->authorize($article);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
        return null;
    }
}

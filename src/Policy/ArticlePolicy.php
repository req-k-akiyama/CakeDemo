<?php
declare(strict_types=1);

namespace App\Policy;

use App\Model\Entity\Article;
use Authorization\IdentityInterface;

/**
 * Article policy
 */
class ArticlePolicy
{
    /**
     * ユーザは記事を追加できるか？ 常にtrue
     *
     * @param Authorization\IdentityInterface $user The user.
     * @param App\Model\Entity\Article $article
     * @return bool
     */
    public function canAdd(IdentityInterface $user, Article $article)
    {
        return true;
    }

    /**
     * ユーザはこの記事を編集できるか？
     *
     * @param Authorization\IdentityInterface $user The user.
     * @param App\Model\Entity\Article $article
     * @return bool
     */
    public function canEdit(IdentityInterface $user, Article $article)
    {
        return $this->isAuthor($user, $article);
    }

    /**
     * ユーザはこの記事を削除できるか？
     *
     * @param Authorization\IdentityInterface $user The user.
     * @param App\Model\Entity\Article $article
     * @return bool
     */
    public function canDelete(IdentityInterface $user, Article $article)
    {
        return $this->isAuthor($user, $article);
    }

    /**
     * このユーザはこの記事の作者か？
     *
     * @param IdentityInterface $user
     * @param Article $article
     * @return boolean
     */
    protected function isAuthor(IdentityInterface $user, Article $article)
    {
        return $article->user_id === $user->getIdentifier();
    }
}

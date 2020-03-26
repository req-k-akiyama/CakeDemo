<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Validation\Validator;
use Cake\ORM\Query;
use Entity\Article;

/**
 * 記事テーブルを表現するORMクラス
 */
class ArticlesTable extends Table
{
    /**
     * ArticlesTableを初期化する
     *
     * @param array $config 初期化時のconfig
     */
    public function initialize(array $config): void
    {
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Tags', [
            'joinTable' => 'articles_tags',
            'dependent' => true
        ]);
    }

    /**
     * 永続化前の処理を実行する
     *
     * @param \Cake\Event\EventInterface $event イベント情報
     * @param \App\Model\Entity\Article $entity 作成するエンティティ
     * @param \ArrayObject $options オプション
     */
    public function beforeSave($event, $entity, $options): void
    {
        if ($entity->tag_string) {
            $entity->tags = $this->_buildTags($entity->tag_string);
        }

        if ($entity->isNew() && !$entity->slug) {
            // 記事タイトルからslugを生成する
            $sluggedTitle = Text::slug($entity->title);
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    /**
     * 既定のvalidationロジックを定義する
     *
     * @param Validator $validator Validatorインスタンス
     * @return Validator 設定後のvalidator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('title')
            ->minLength('title', 10)
            ->maxLength('title', 255)

            ->notEmptyString('body')
            ->minLength('body', 10);

        return $validator;
    }

    /**
     * タグによって記事を取得する
     *
     * @param Query $query Queryインスタンス
     * @param array $options オプション情報
     * @return Query find条件を指定したQueryインスタンス
     */
    public function findTagged(Query $query, array $options): Query
    {
        $columns = [
            'Articles.id', 'Articles.user_id', 'Articles.title',
            'Articles.body', 'Articles.published', 'Articles.created',
            'Articles.slug',
        ];

        $query = $query
            ->select($columns)
            ->distinct($columns);

        if (empty($options['tags'])) {
            $query->leftJoinWith('Tags')
                ->where(['Tags.title IS' => null]);
        } else {
            $query->innerJoinWith('Tags')
                ->where(['Tags.title IN' => $options['tags']]);
        }

        return $query->group(['Articles.id']);
    }

    /**
     * 文字列形式のタグを受け取り、新規のタグである場合作成し、配列化したTagエンティティを返す
     *
     * @param string $tagString カンマ区切り文字列形式のタグ
     * @return array Tagエンティティ
     */
    protected function _buildTags(string $tagString): array
    {
        $newTags = array_map('trim', explode(',', $tagString));
        $newTags = array_filter($newTags);
        $newTags = array_unique($newTags);

        $out = [];
        $query = $this->Tags->find()
            ->where(['Tags.title IN' => $newTags]);

        foreach ($query->extract('title') as $existing) {
            $index = array_search($existing, $newTags);
            if ($index !== false) {
                unset($newTags[$index]);
            }
        }

        foreach ($query as $tag) {
            $out[] = $tag;
        }
        foreach ($newTags as $tag) {
            $out[] = $this->Tags->newEntity(['title' => $tag]);
        }
        return $out;
    }
}

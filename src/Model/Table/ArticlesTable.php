<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;

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
        if ($entity->isNew() && !$entity->slug) {
            // 記事タイトルからslugを生成する
            $sluggedTitle = Text::slug($entity->title);
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }
}

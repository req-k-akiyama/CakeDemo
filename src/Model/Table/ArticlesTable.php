<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;

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
}

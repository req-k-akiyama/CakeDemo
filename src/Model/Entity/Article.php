<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * 記事レコードを表現するORMクラス
 */
class Article extends Entity
{
    /** @var array 各カラムの一括割当の可否 */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'slug' => false,
    ];
}

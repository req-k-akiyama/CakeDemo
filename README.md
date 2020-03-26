# I 社様向け CakePHP チュートリアル実装デモ

## 概要

このリポジトリは、転職活動のスキルテスト (I社様ご要請) のために、[CakePHP 4.x Quick Start Guide](https://book.cakephp.org/4/en/quickstart.html) に従ってごく簡単なCMSを実装したものです。

## 成果物

### Gitリポジトリ
https://github.com/req-k-akiyama/CakeDemo

### 動作デモ

以下に完成し動作するCMSがあります (2020/3/26 現在)  
http://18.180.19.40/articles

最初はログイン画面が表示されるため、 `Add User` から適当なユーザを作ってご確認ください。

予告なく動作停止する可能性があります。

## 動作環境

* サーバクラウド:  AWS
* OS:  Amazon Linux2
* インスタンスサイズ:  t2.micro
* リージョン:  ap-northeast-1(Tokyo)
* Elastic IP:  18.180.19.40
   * 無料枠の範囲で動作させるためドメインは取得していません
   * したがってSSLも適用していません
   * LBも噛ませていません

## ミドルウェア・言語・フレームワーク

* Nginx 1.12.2
* MySQL 5.7.29
  * RDSではなくlocal環境に同居しています
* PHP 7.4.4
* CakePHP 4.0.3
  * ほか細かいモジュールは `composer.lock` に譲ります

## やったこと

チュートリアルの内容を最後までほぼそのままなぞりました。  
ただし以下の点は個人的に付け加えて行いました。
* 可能な限りタイプヒンティング
* `bake` を使わずに指示通り手で作ったphpファイルにも`declare(strict_types=1)` をつけた
* DBの接続情報をconfigに直書きしない
  * commitしてpublicリポジトリにあがるのがどうしても気持ち悪かった
  * 環境変数から取るように修正
  * ただし本当に環境変数の設定するのは面倒だったので `dotenv` で設定した
* 各クラス・メソッド・プロパティにPHPDocを追加
  * チームが英語で書く方針であれば英語でも書けるが、ひとまず日本語で書いた
* バグや障害の回避（後述）

テキストエディタは `VS Code` を使い、`Romete-SSH` 拡張でリモートのサーバに入ってファイルを直接編集しました。

デプロイはシェルスクリプトでごく簡単に Git からの pull やパーミッション付与を行うにとどめています。

普段業務で作業するときと同じ感覚で commit の粒度を切ってあります。（動作が壊れない範囲でなるべく小さく）  
ただしすべて master ブランチ進行です。


## やってないこと

上記以外にチュートリアルの内容になかったことは基本的にやっていません。  
たとえば `bake` で作った `user` や `tag` の一覧ページなどは認証系の設定でアクセス不能になりましたが、放置しています。  
`bake` で生成された `PHPDoc` などにもあまり手を入れいません。

## ぶつかったバク・障害

### Validatorの設定時のエラー

チュートリアル中にあった [Validator の設定](https://book.cakephp.org/4/en/tutorials-and-examples/cms/articles-controller.html#update-validation-rules-for-articles)、  
`->allowEmptyString('title', false)`  
このメソッドにこんなシグネチャは存在しないのでエラーになりました。

詳しく追ってないですが、どこかで破壊的な変更があってドキュメントが追いついていないものと思われます。
https://github.com/cakephp/cakephp/blob/master/src/Validation/Validator.php#L858  
ここの実装を見て `->notEmptyString()` に変更しました。

### 記事内容の改行が表示されないバグ

個別の記事を参照する機能 ( /article/view ) で、記事の body に改行が含まれていても反映されないバグがありました。  
単純に `nl2br()` して直しました。

### Policy設定時のエラー

`Article` エンティティに対する Authorization について、`bake` で生成される Policy の `caxXXX` 系メソッドとコントローラのメソッド名が違うためエラーになりました。  
かなり CoC を重視するスタイルの開発なのに、なぜコントローラの API は素直に CRUD にしないんだろう（あるいはなぜ `bake` でコントローラの実装を見ないんだろう）と思いました。

### メモリ不足

AWSの無料分で動かそうとしたためマシンが貧弱すぎて、 `composer require` しようとしたらメモリ不足で失敗しました。  
AWSはデフォルトではswap領域が設定されていないのですが、仕方なく設定して乗り切りました。

<?php

namespace App\Services\Tree;

use App\Services\Keyword\KeywordService;

class Tree
{

  /**
   * Create a new command instance.
   *
   * @return void
   */
  private function __construct() {}

  /**
   * 引数に与えられたcallbackからデータを取得し、木を構築する。
   *
   * 構築は幅優先で構築される。
   * このとき、既出のキーワードがあった場合は、
   * そのノードは探索終了し、already = trueとする。
   *
   * 引数に与えられたノード数となるまで探索し、
   * 探索を打ち切ったノードは、end_of_search = trueとする。
   * 上記のノード数にはルートも含める。
   *
   * 結果例)
   * array(2) {
   *   ["name"]=> "キーワード1"
   *   ["children"]=>
   *   array(2) {
   *     [0]=>
   *     array(2) {
   *       ["name"]=> "キーワード2"
   *       ["children"]=>
   *       array(2) {
   *         [0]=>
   *         array(2) {
   *           ["name"]=> "キーワード4"
   *           ["children"]=> array(0) {}
   *         }
   *         [1]=>
   *         array(2) {
   *           ["name"]=> "キーワード5"
   *           ["children"]=> array(0) {}
   *         }
   *       }
   *     }
   *     [1]=>
   *     array(2) {
   *       ["name"]=> "キーワード3"
   *       ["children"]=>
   *       array(3) {
   *         [0]=>
   *         array(2) {
   *           ["name"]=> "キーワード6"
   *           ["children"]=> array(0) {}
   *         }
   *         [1]=>
   *         array(3) {
   *           ["name"]=> "キーワード2"
   *           ["already"]=> bool(true)
   *           ["children"]=> array(0) { }
   *         }
   *         [2]=>
   *         array(3) {
   *           ["name"]=> "キーワード7"
   *           ["children"]=> array(0) {}
   *           ["end_of_search"]=> bool(true)
   *         }
   *       }
   *     }
   *   }
   * }
   *
   * 本関数のアルゴリズムについては、同ディレクトリ上の以下ファイルを参照
   * build_tree_algorithm.svg
   *
   */
  public static function build($firstword, $keywordLimit, KeywordService $keywordService) {

    $keywordRoot = array('name' => $firstword, 'children' => array());

    $untreated = [];
    $untreated[] = &$keywordRoot;

    $addedList = [];
    $addedList[] = $firstword;
    $keywordCount = 0;

    while (true) {
      foreach($untreated as $index => $keyObj) {

        $nextKeywords = $keywordService->nextKeywords($keyObj['name']);

        if ($nextKeywords) {
          foreach($nextKeywords as $nextword) {

            if (!in_array($nextword, $addedList)) {
              $untreated[$index]['children'][] = array('name' => $nextword, 'children' => array());

              end($untreated[$index]['children']);
              $tempKey = key($untreated[$index]['children']);

              $untreated[] = &$untreated[$index]['children'][$tempKey];
              $addedList[] = $nextword;

            } else {
              $untreated[$index]['children'][] = array('name' => $nextword, 'already' => true, 'children' => array());
            }

            $keywordCount++;
            if ($keywordCount === $keywordLimit - 1) {
              end($untreated[$index]['children']);
              $tempKey = key($untreated[$index]['children']);
              $untreated[$index]['children'][$tempKey]['end_of_search'] = true;
              break 3;
            }
          }
        }
        unset($untreated[$index]);
      }
      $untreated = array_values($untreated);

      if (count($untreated) === 0) {
        break;
      }
    }

    return $keywordRoot;
  }

}

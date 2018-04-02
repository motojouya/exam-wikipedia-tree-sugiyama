<?php

namespace App\Services\Keyword;

use App\Services\WikipediaAccess;
use App\Services\DatabaseAccess;

class DBKeywordService implements KeywordService
{

  /** キーワードをメモリ上に保有しておく */
  private $keywordList = [];

  /**
   * 引数に与えられたキーワードに紐づくキーワード群を取得する
   *
   * 以下の優先度で取得していく。
   *   1. メモリ上のキーワード
   *   2. DB上の過去取得したキーワード
   *   3. Wikipediaに実際にアクセスして取得したキーワード
   *
   * Wikipediaから取得したものは、DBとメモリに保持しておく。
   */
  public function nextKeywords($keyword) {

    if (array_key_exists($keyword, $this->keywordList)) {
      return $this->keywordList[$keyword];
    }

    $keywordListDB = DatabaseAccess::searchKeywords($keyword);
    $this->keywordList = array_merge($this->keywordList, $keywordListDB);

    if (array_key_exists($keyword, $this->keywordList)) {
      return $this->keywordList[$keyword];
    }

    $keywordListWeb = WikipediaAccess::getNextKeyword($keyword);
    sleep(1);

    if ($keywordListWeb) {
      DatabaseAccess::saveKeywordRelations($keyword, $keywordListWeb);
      $this->keywordListDB[$keyword] = $keywordListWeb;
    }

    return $keywordListWeb;
  }
}

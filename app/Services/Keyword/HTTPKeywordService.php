<?php

namespace App\Services\Keyword;

use App\Services\WikipediaAccess;

class HTTPKeywordService implements KeywordService
{

  /**
   * 引数に与えられたキーワードに紐づくキーワード群をWikipediaから取得する
   */
  public function nextKeywords($keyword) {
    sleep(1);
    return WikipediaAccess::getNextKeyword($keyword);
  }
}

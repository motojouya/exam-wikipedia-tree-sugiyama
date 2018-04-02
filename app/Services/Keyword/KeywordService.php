<?php

namespace App\Services\Keyword;

interface KeywordService
{

  /**
   * 引数に与えられたキーワードに紐づくキーワード群を取得する
   */
  public function nextKeywords($keyword);
}

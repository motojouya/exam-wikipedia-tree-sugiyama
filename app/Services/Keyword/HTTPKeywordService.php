<?php

namespace App\Services\Keyword;

use App\Services\WikipediaAccess;

class HTTPKeywordService implements KeywordService
{

  /**
   * �����ɗ^����ꂽ�L�[���[�h�ɕR�Â��L�[���[�h�Q��Wikipedia����擾����
   */
  public function nextKeywords($keyword) {
    sleep(1);
    return WikipediaAccess::getNextKeyword($keyword);
  }
}

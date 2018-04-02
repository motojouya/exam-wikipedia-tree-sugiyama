<?php

namespace App\Services\Keyword;

use App\Services\WikipediaAccess;
use App\Services\DatabaseAccess;

class DBKeywordService implements KeywordService
{

  /** �L�[���[�h����������ɕۗL���Ă��� */
  private $keywordList = [];

  /**
   * �����ɗ^����ꂽ�L�[���[�h�ɕR�Â��L�[���[�h�Q���擾����
   *
   * �ȉ��̗D��x�Ŏ擾���Ă����B
   *   1. ��������̃L�[���[�h
   *   2. DB��̉ߋ��擾�����L�[���[�h
   *   3. Wikipedia�Ɏ��ۂɃA�N�Z�X���Ď擾�����L�[���[�h
   *
   * Wikipedia����擾�������̂́ADB�ƃ������ɕێ����Ă����B
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

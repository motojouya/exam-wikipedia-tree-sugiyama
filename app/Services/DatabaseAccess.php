<?php

namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Keyword;

class DatabaseAccess
{

  /**
   * Create a new command instance.
   *
   * @return void
   */
  private function __construct() {}

  private static $selectBreadthFirst= <<<EOF
    WITH RECURSIVE refrec (refer_from, refer_to, level) AS (
      SELECT refers.refer_from AS refer_from
           , refers.refer_to AS refer_to
           , 0 AS level
        FROM refers
       INNER JOIN keywords
          ON refers.refer_from = keywords.id
       WHERE keywords.keyword = ?
    UNION
      SELECT refers.refer_from AS refer_from
           , refers.refer_to AS refer_to
           , refrec.level + 1 AS level
        FROM refers
       INNER JOIN refrec
          ON refers.refer_from = refrec.refer_to
       ORDER BY level
    )
    SELECT keyfrom.id AS from_id
         , keyfrom.keyword AS from_word
         , keyfrom.refer_to_at AS refer_to_at
         , keyto.id AS to_id
         , keyto.keyword AS to_word
      FROM refrec
     INNER JOIN keywords AS keyfrom
        ON refrec.refer_from = keyfrom.id
     INNER JOIN keywords AS keyto
        ON refrec.refer_to = keyto.id
     ORDER BY keyfrom.id
     LIMIT 10;
EOF;

  private static $selectDepthFirst = <<<EOF
    WITH RECURSIVE refrec AS (
      SELECT refers.refer_from AS refer_from
           , refers.refer_to AS refer_to
        FROM refers
       INNER JOIN keywords
          ON refers.refer_from = keywords.id
       WHERE keywords.keyword = ?
    UNION
      SELECT refers.refer_from AS refer_from
           , refers.refer_to AS refer_to
        FROM refers
       INNER JOIN refrec
          ON refers.refer_from = refrec.refer_to
    )
    SELECT keyfrom.id AS from_id
         , keyfrom.keyword AS from_word
         , keyfrom.refer_to_at AS refer_to_at
         , keyto.id AS to_id
         , keyto.keyword AS to_word
      FROM refrec
     INNER JOIN keywords AS keyfrom
        ON refrec.refer_from = keyfrom.id
     INNER JOIN keywords AS keyto
        ON refrec.refer_to = keyto.id
     ORDER BY keyfrom.id
     LIMIT 100;
EOF;

  private static $insertRefers = "INSERT INTO refers (refer_from, refer_to) VALUES (?, ?)";

  private static $selectRefers = "SELECT refer_from, refer_to FROM refers WHERE refer_from = ? AND refer_to = ?";

  /**
   * �����̃L�[���[�h���L�[�ɁADB����֘A����L�[���[�h���擾����B
   * �T�������L�[���[�h�Ɋ֘A����L�[���[�h���ċA�I�Ɍ�������B
   *
   */
  public static function searchKeywords($keyword) {

    $keywordList = [];
    $keywords = DB::select(DatabaseAccess::$selectDepthFirst, array($keyword));

    $currentKeywordId = null;
    $currentKeywordWord = null;

    foreach ($keywords as $keyword) {

      if (!$currentKeywordId || $currentKeywordId !== $keyword->from_id) {
        $currentKeywordId = $keyword->from_id;
        $currentKeywordWord = $keyword->from_word;

        $keywordList[$currentKeywordWord] = [];
      }
      $keywordList[$currentKeywordWord][] = $keyword->to_word;
    }
    return $keywordList;
  }

  /**
   * DB�̃L�[���[�h�I�u�W�F�N�g��A�z�z��Ŏ擾����
   */
  private static function getKeywordMap($keywordList) {

    $keywordMap = [];
    $keywordListDB = Keyword::whereIn('keyword', $keywordList)->get();

    foreach ($keywordListDB as $keywordDB) {
      $keywordMapDB[$keywordDB->keyword] = $keywordDB;
    }
    return $keywordMap;
  }

  /**
   * �L�[���[�h�̊֘A��DB�ɕۑ�����
   */
  private static function saveRefers($referFromId, $referFromFrom) {
    $refer = DB::select(DatabaseAccess::$selectRefers, [$referFromId, $referFromFrom]);
    if (!$refer) {
      DB::insert(DatabaseAccess::$insertRefers, [$referFromId, $referFromFrom]);
    }
  }

  /**
   * ���ۗL�̃L�[���[�h��DB�ɕۑ�����
   */
  private static function saveKeyword($keyword, $referToAt) {
    $newKeyword = new Keyword;
    $newKeyword->keyword = $keyword;
    $newKeyword->refer_to_at = $referToAt;
    $newKeyword->save();
    return $newKeyword;
  }

  /**
   * �L�[���[�h�Ƃ���Ɋ֘A����L�[���[�h�Q��DB�ɕۑ�����B
   * ���̂Ƃ��A�����̊֘A���ۑ�����B
   */
  public static function saveKeywordRelations($keyword, $keywordList) {

    $keywordListSearchDB = $keywordList;
    $keywordListSearchDB[] = $keyword;
    $keywordMapDB = DatabaseAccess::getKeywordMap($keywordListSearchDB);

    if (!array_key_exists($keyword, $keywordMapDB)) {
      $keywordMapDB[$keyword] = DatabaseAccess::saveKeyword($keyword, Carbon::now());
    }

    foreach ($keywordList as $keywordInList) {
      if (!array_key_exists($keywordInList, $keywordMapDB)) {
        $keywordMapDB[$keywordInList] = DatabaseAccess::saveKeyword($keywordInList, null);
      }
      DatabaseAccess::saveRefers($keywordMapDB[$keyword]->id, $keywordMapDB[$keywordInList]->id);
    }
  }

}

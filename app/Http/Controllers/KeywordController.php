<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Tree\Tree;
use App\Services\Keyword\DBKeywordService;

class KeywordController extends Controller
{

  /**
   * いくつのキーワードを取得するか
   *
   */
  private $keywordLimit = 100;

  /**
   * 引数に与えられたキーワードから、再帰的にWikipediaにアクセスし、
   * キーワードツリーを構築し、そのJSONを返す。
   * このとき、一度取得したキーワードはDBにキャッシュしている。
   */
  public function search(Request $request, $argKeyword) {
    return Tree::build($argKeyword, $this->keywordLimit, new DBKeywordService());
  }
}

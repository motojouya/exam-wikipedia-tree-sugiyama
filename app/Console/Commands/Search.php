<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WikipediaAccess;
use App\Services\Tree\Tree;
use App\Services\Keyword\HTTPKeywordService;

class Search extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'command:search {keyword}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Search Wikipedia';

  /**
   * いくつのキーワードを取得するか
   *
   */
  private $keywordLimit = 100;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
      parent::__construct();
  }

  /**
   * Tree構造になっているキーワードを標準出力に出力する。
   */
  private function showKeywords($keywordTree, $level) {

    foreach(range(0, $level) as $i) {
      echo "  ";
    }
    echo "- ";
    echo $keywordTree['name'];
    if (array_key_exists('already', $keywordTree) && $keywordTree['already']) {
      echo "@";
    }
    if (array_key_exists('end_of_search', $keywordTree) && $keywordTree['end_of_search']) {
      echo "$";
    }
    echo "\n";

    $nextLevel = $level + 1;
    foreach ($keywordTree['children'] as $belowTree) {
      $this->showKeywords($belowTree, $nextLevel);
    }
  }

  /**
   * コマンドを実行する。
   * 引数に与えられたキーワードから、再帰的にWikipediaにアクセスし、
   * キーワードツリーを構築し、出力する。
   *
   * @return mixed
   */
  public function handle() {

    $argKeyword = $this->argument("keyword");
    $keywordTree = Tree::build($argKeyword, $this->keywordLimit, new HTTPKeywordService());

    if ($keywordTree['children']) {
      $this->showKeywords($keywordTree, 0);
    } else {
      echo "入力されたキーワードはWikipediaに存在しないようです。\n";
    }
  }

}

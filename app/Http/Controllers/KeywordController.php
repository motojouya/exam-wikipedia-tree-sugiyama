<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Tree\Tree;
use App\Services\Keyword\DBKeywordService;

class KeywordController extends Controller
{

  /**
   * �����̃L�[���[�h���擾���邩
   *
   */
  private $keywordLimit = 100;

  /**
   * �����ɗ^����ꂽ�L�[���[�h����A�ċA�I��Wikipedia�ɃA�N�Z�X���A
   * �L�[���[�h�c���[���\�z���A����JSON��Ԃ��B
   * ���̂Ƃ��A��x�擾�����L�[���[�h��DB�ɃL���b�V�����Ă���B
   */
  public function search(Request $request, $argKeyword) {
    return Tree::build($argKeyword, $this->keywordLimit, new DBKeywordService());
  }
}

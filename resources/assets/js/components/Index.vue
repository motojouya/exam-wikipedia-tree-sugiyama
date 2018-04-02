<template>
  <div>
    <vue-input-search @searchKeyword="searchKeyword" @getHistory="getHistory" @delHistory="delHistory" :histories="histories" :keyword="keyword" ></vue-input-search>
    <div class="tree_container" v-if="(status === 'result')">
      <vue-result :tree="tree" @searchKeyword="searchKeyword"></vue-result>
    </div>
    <div class="description" v-if="status === 'first'">
      <h2>Wikipediaから言葉を集めよう！</h2>
      <p class="description_main">Wikipediaのページ上の関連リンクをたどって、関連するキーワードを100個まで探索します。</p>
    </div>
    <div class="loading" v-if="status === 'searching'">
      <div class="loading_anime"></div>
      <span class="loading_text">検索中...</span>
    </div>
    <div class="description" v-if="status === 'none'">
      <p class="description_main">Wikipediaには存在しないキーワードでした。</p>
    </div>
  </div>
</template>

<script>
/**
 * statusは以下4つある
 *   first     -> 初期状態
 *   searching -> 検索中
 *   none      -> 検索結果が存在しなかった
 *   result    -> 検索結果を表示中
 */
import VueInputSearch from './InputSearch'
import VueResult from './Result'
import getDB from '../indexedDatabase'
import fetchSearch from '../fetch'

const DDL = {
  HISTORY: {
    name: 'history',
    option: {keyPath: 'keyword'},
  }
};
const db = getDB(DDL);

const search = (vm, keyword, times) => {
  fetchSearch(keyword, (json) => {
    if (json && json.children && json.children.length) {
      vm.tree = json;
      vm.status = 'result';
    } else {
      vm.tree = null;
      vm.status = 'none';
    }
  });
};

const searchHistory = (vm, keyword) => {
  db.getLikePrefix(DDL.HISTORY.name, keyword, (results) => {
    vm.histories = results.slice(0, 5);
  });
};

const putHistory = (vm, keyword) => {
  const keywordObj = {
    keyword: keyword,
  };
  db.put(DDL.HISTORY.name, keywordObj, () => {
    console.log('keyword is put in history. keyword: ' + keyword);
  });
};

const deleteHistory = (vm, keyword) => {
  db.del(DDL.HISTORY.name, keyword, () => {
    console.log('keyword is delete in history. keyword: ' + keyword);
  });
};

export default {
  components: {
    VueInputSearch,
    VueResult,
  },
  data() {
    return {
      histories: [],
      tree: null,
      status: 'first',
      keyword: '',
    };
  },
  methods: {
    searchKeyword(keyword) {
      this.keyword = keyword;
      this.status = 'searching';
      putHistory(this, keyword);
      search(this, keyword);
    },
    getHistory(keyword) {
      this.keyword = keyword;
      searchHistory(this, keyword);
    },
    delHistory(keyword) {
      deleteHistory(this, keyword);
      searchHistory(this, '');
    },
  },
}
</script>

<style scoped>
  .tree_container {
    margin: 10px 0 10px 10px;
  }
  .description {
    height: 100%;
    margin: 100px 0 0 20px;
  }
  .description_main {
    margin: 0 0 0 10px;
  }
  .loading {
    height: 100%;
    text-align: center;
    margin: 100px 0 0 0;
  }
  .loading_text {
    color: #191970;
    font-size: 2em;
  }
</style>

'use strict';

/**
 * サーバに関連するキーワードを問い合わせる
 *
 * TODO
 * 接続Timeout対策として3回まで失敗できるようにしてある
 * しかし本来はnginx周りの設定変更で対応すべき
 */
const search = (keyword, callback, times) => {

  if (!keyword) {
    return callback();
  }
  times = times || 0;

  fetch('/api/search/' + keyword)
    .then((result) => {
      return result.json();
    })
    .then(callback)
    .catch((err) => {
      if (times > 2) {
        console.log('Search fault. keyword: ' + keyword, err);
      } else {
        search(keyword, callback, times + 1);
      }
    });
};

export default search;

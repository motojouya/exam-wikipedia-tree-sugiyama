'use strict';

const indexedDB = self.indexedDB || self.mozIndexedDB || self.webkitIndexedDB || self.msIndexedDB;

const VERSION = 1;
const DB_NAME = 'wikisearch';

let db;
const objectStrores ={};

/**
 * IndexedDBの初期化
 */
const initDB = (OBJECT_STORES) => {

  const openReq = indexedDB.open(DB_NAME, VERSION);

  openReq.onupgradeneeded = (event) => {
    const db = event.target.result;
    event.target.transaction.onerror = (err) => {
      console.log('db upgrade error.', err);
    };
    for (let storeConfigKey in OBJECT_STORES) {
      if (OBJECT_STORES.hasOwnProperty(storeConfigKey)) {
        let storeConfig = OBJECT_STORES[storeConfigKey];
        const name = storeConfig.name;
        if (db.objectStoreNames.contains(name)) {
          db.deleteObjectStore(name);
        }
        objectStrores[name] = db.createObjectStore(name, storeConfig.option);
        if (storeConfig.func) {
          storeConfig.func(objectStrores[name]);
        }
      }
    }
  };

  openReq.onsuccess = (event) => {
    db = (event.target) ? event.target.result : event.result;
  };

  openReq.onerror = (event) => {
    console.log('db open error.');
  };
};

/**
 * DBにデータを追加する
 */
const put = (storeName, data, callback) => {

  const tranx = db.transaction([storeName], 'readwrite');
  tranx.oncomplete = () => {
    callback();
  };
  tranx.onerror = (err) => {
    console.log('db data put transaction error.', err);
  };

  const putReq = tranx.objectStore(storeName).put(data);
  putReq.onsuccess = (event) => {
    console.log('db data put success.');
  };
  putReq.onerror = (err) => {
    console.log('db data put error.', err);
  };
};

/**
 * DBからデータを削除する
 */
const del = (storeName, key) => {
  const deleteReq = db.transaction([storeName], 'readwrite').objectStore(storeName).delete(key);
  deleteReq.onsuccess = (event) => {
    console.log('db data delete success.');
  };
  deleteReq.onerror = (err) => {
    console.log('db data delete error.', err);
  };
};

/**
 * DBからデータを取得する
 * 検索キーワードがfalsyの場合は、全件を取得
 * 検索キーワードがtrusyの場合は、キーワードの前方一致を取得
 */
const getLikePrefix = (storeName, key, callback) => {

  const results = [];
  const cursorReq = db.transaction(storeName, 'readonly').objectStore(storeName).openCursor();
  cursorReq.onsuccess = (event) => {
    const result = event.target.result;
    if (result) {
      if (!key || result.value.keyword.indexOf(key) === 0){
        results.push(result.value);
      }
      result.continue();
    } else {
      return callback(results);
    }
  };
  cursorReq.onerror = (event) => {
    console.log('db data get error.', err);
  };
};

/**
 * DBを削除する
 */
const deleteDB = () => {
  const deleteReq = indexedDB.deleteDatabase(DB_NAME);
  deleteReq.onsuccess = (event) => {
    console.log('db delete success.');
    db = null;
  }
  deleteReq.onerror = () => {
    console.log('db delete error.');
  }
};

/**
 * DBを閉じる
 */
const closeDB = () => {
  db.close();
  db = null;
};

export default (OBJECT_STORES) => {
  if (!indexedDB) {
    console.log('cannot use IndexedDB.');
  }
  if (!db) {
    initDB(OBJECT_STORES);
  }
  return {
    put: put,
    getLikePrefix: getLikePrefix,
    del: del,
    closeDB: closeDB,
    deleteDB: deleteDB,
  };
};


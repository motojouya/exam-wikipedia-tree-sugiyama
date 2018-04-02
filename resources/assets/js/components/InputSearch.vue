<template>
  <div class="input_search_wrap">
    <div class="input_text_wrap flex">
      <div class="variableitem">
        <input type="text" v-model="keyword" @keyup="getHistory" class="input_text">
      </div>
      <button type="button" name="search" @click="search" class="search_button fixeditem">
        <span class="css_icon search_icon"></span>
      </button>
    </div>
    <ul v-if="editting" class="suggestions">
      <li v-for="history in histories" class="flex">
        <button type="button" name="search_history" @click="searchFromHistory" :value="history.keyword" class="variableitem search_from_history" >
          {{ history.keyword }}
        </button>
        <button type="button" name="delete_history" @click="deleteHistory" :value="history.keyword" class="fixeditem delete_history" >
          <span class="css_icon delete_icon"></span>
        </button>
      </li>
    </ul>
    <div v-if="editting" class="editting_filter" @click="closeHistory"></div>
  </div>
</template>
<script>
export default {
  props: {
    histories: Array,
    keyword: String,
  },
  data() {
    return {
      editting: false,
    }
  },
  methods: {
    search(e) {
      this.editting = false;
      this.$emit('searchKeyword', this.keyword);
    },
    searchFromHistory(e) {
      this.editting = false;
      this.keyword = e.currentTarget.value;
      this.$emit('searchKeyword', this.keyword);
    },
    getHistory(e) {
      this.editting = true;
      this.$emit('getHistory', this.keyword);
    },
    deleteHistory(e) {
      this.editting = true;
      var keywordWillDelete = e.currentTarget.value;
      this.$emit('delHistory', keywordWillDelete);
    },
    closeHistory(e) {
      this.editting = false;
    },
  },
}
</script>
<style>
  .input_search_wrap {
    position: relative;
    width: 100%;
  }
  .input_text_wrap {
    width: 100%;
    z-index: 10;
  }
  .input_text {
    outline: 0;
    width: 100%;
    margin: 0 5px 0 5px;
    padding: 0 0 0 0;
    font-size: 1em;
    border-top: none;
    border-right: none;
    border-bottom: 1px #000 solid;
    border-left: none;
  }
  ul.suggestions {
    list-style: none;
    position: absolute;
    width: 100%;
    top: 30px;
    left: 0;
    margin: 0;
    padding: 0;
    z-index: 10;
  }
  ul.suggestions > li {
    height: 30px;
    margin: 0 35px 0 5px;
    border-top: none;
    border-right: 1px #ddd solid;
    border-bottom: 1px #ddd solid;
    border-left: 1px #ddd solid;
  }
  .search_from_history {
    background-color: #fff;
    padding: 0 0 0 5px;
  }
  .delete_history {
    background-color: #fff;
    width: 30px;
    padding: 0;
  }
  .editting_filter {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    opacity: 0;
    z-index: 5;
  }
</style>

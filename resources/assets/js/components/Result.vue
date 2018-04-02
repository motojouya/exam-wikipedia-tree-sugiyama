<template>
  <dl v-if="exist" class="result_list">
    <dt>
      <span>- <a :href="href" target="_blank">{{ tree.name }}</a><span v-if="tree.already"> @</span><span v-if="tree.end_of_search"> $</span></span>
      <button type="button" name="search" @click="search" class="search_button_small fixeditem">
        <span class="css_icon search_icon_small"></span>
      </button>
    </dt>
    <dd v-if="hasChildren">
      <result v-for="child in tree.children" :tree="child" @searchKeyword="searchKeyword"></result>
    </dd>
  </dl>
</template>

<script>
export default {
  name: 'result',
  props: {
    tree: Object,
  },
  methods: {
    searchKeyword(keyword) {
      this.$emit('searchKeyword', keyword);
    },
    search(e) {
      this.$emit('searchKeyword', this.tree.name);
    },
  },
  computed: {
    hasChildren: function () {
      return this.tree
          && this.tree.children
          && this.tree.children.length
    },
    href: function () {
      return 'https://ja.wikipedia.org/wiki/' + this.tree.name;
    },
    exist: function () {
      return !!this.tree;
    },
  },
}
</script>
<style>
dl.result_list {
  margin: 0;
  padding: 0;
}
dl.result_list > dt{
  margin: 5px 0 0 0;
}
dl.result_list > dd{
  margin: 0 0 0 20px;
}
</style>

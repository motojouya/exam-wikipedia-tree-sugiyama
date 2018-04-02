import Vue from 'vue'
import Container from './components/Index.vue'

let vm = new Vue({
  el: '#app',
  components: {
    app: Container,
  },
  render: h => h('app'),
});


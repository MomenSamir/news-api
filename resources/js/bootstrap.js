import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import { createApp } from 'vue';
import ArticleList from './components/ArticleList.vue';

const app = createApp({});
app.component('article-list', ArticleList);

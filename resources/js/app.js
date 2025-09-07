import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import ArticleList from './components/ArticleList.vue';

const app = createApp({});
app.component('article-list', ArticleList);
app.mount('#app');

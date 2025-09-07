<template>
  <div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Latest Articles</h1>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-6 justify-between items-center">
      <input
        v-model="q"
        @keyup.enter="applyFilter"
        placeholder="Search articles..."
        class="w-full md:w-1/3 p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
      />
      <select
        v-model="source"
        @change="applyFilter"
        class="w-full md:w-1/4 p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
        <option value="">All sources</option>
        <option value="newsapi">NewsAPI</option>
        <option value="guardian">The Guardian</option>
        <option value="nyt">NYT</option>
      </select>
      <input
        type="date"
        v-model="from"
        @change="applyFilter"
        class="w-full md:w-1/4 p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
      />
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center text-blue-500 font-semibold mb-6">
      Loading articles...
    </div>

    <!-- No articles -->
    <div v-else-if="articles.length === 0" class="text-center text-red-500 font-semibold mb-6">
      No articles found.
    </div>

    <!-- Articles List -->
    <div v-else>
      <h2 class="text-xl font-semibold mb-4 text-gray-700">
        Articles ({{ articles.length }})
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="article in articles"
          :key="article.id"
          class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden"
        >
          <div class="p-4 flex flex-col h-full">
            <h3 class="font-bold text-lg mb-2 text-gray-800">{{ article.title }}</h3>
            <p class="text-sm text-gray-600 mb-2">
              {{ article.summary || "No summary available" }}
            </p>
            <p class="text-xs text-gray-400 mb-4">
              {{ formattedDate(article.published_at) }}
            </p>
            <a
              :href="article.source_id"
              target="_blank"
              class="mt-auto text-blue-500 font-medium hover:underline"
            >
              Read more
            </a>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center items-center mt-8 space-x-4">
        <button
          @click="prevPage"
          :disabled="!meta.prev_page_url"
          class="px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed transition"
        >
          Previous
        </button>
        <span class="text-gray-700 font-medium">
          Page <span class="font-bold">{{ meta.current_page }}</span> of <span class="font-bold">{{ meta.last_page }}</span>
        </span>
        <button
          @click="nextPage"
          :disabled="!meta.next_page_url"
          class="px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed transition"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      articles: [],
      meta: {},
      q: "",
      source: "",
      from: "",
      page: 1,
      per_page: 10,
      loading: false,
    };
  },
  methods: {
    async fetchArticles(page = 1) {
      this.loading = true;
      try {
        const res = await axios.get("/api/articles", {
          params: {
            page,
            per_page: this.per_page,
            q: this.q || undefined,
            source: this.source || undefined,
            from: this.from || undefined,
          },
        });

        this.articles = res.data.data || [];
        this.meta = {
          current_page: res.data.current_page,
          last_page: res.data.last_page,
          next_page_url: res.data.next_page_url,
          prev_page_url: res.data.prev_page_url,
        };
      } catch (err) {
        console.error(err);
        this.articles = [];
        this.meta = {};
      } finally {
        this.loading = false;
      }
    },
    nextPage() {
      if (this.meta.next_page_url) {
        this.page++;
        this.fetchArticles(this.page);
      }
    },
    prevPage() {
      if (this.meta.prev_page_url) {
        this.page--;
        this.fetchArticles(this.page);
      }
    },
    applyFilter() {
      this.page = 1;
      this.fetchArticles(this.page);
    },
    formattedDate(dt) {
      return dt ? new Date(dt).toLocaleString() : "";
    },
  },
  mounted() {
    this.fetchArticles(this.page);
  },
};
</script>

<style scoped>
body {
  background-color: #f9fafb;
}
</style>

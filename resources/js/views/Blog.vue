<template>
  <div class="blog-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Blog Finansowy</h1>
      
      <!-- Blog Posts -->
      <div v-if="posts.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article 
          v-for="post in posts" 
          :key="post.id"
          class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden"
        >
          <div class="p-6">
            <h2 class="text-xl font-semibold mb-3">{{ post.title }}</h2>
            <p class="text-gray-600 mb-4 line-clamp-3">{{ post.excerpt }}</p>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">{{ formatDate(post.created_at) }}</span>
              <button 
                @click="readMore(post.id)"
                class="text-blue-600 hover:text-blue-800 font-medium"
              >
                Czytaj więcej
              </button>
            </div>
          </div>
        </article>
      </div>
      
      <div v-else-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Ładowanie artykułów...</p>
      </div>
      
      <div v-else class="text-center py-8">
        <p class="text-gray-600">Brak artykułów</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Blog',
  data() {
    return {
      posts: [],
      loading: false
    }
  },
  methods: {
    async loadPosts() {
      this.loading = true
      try {
        const response = await fetch('/api/blog/posts')
        if (response.ok) {
          this.posts = await response.json()
        }
      } catch (error) {
        console.error('Error loading posts:', error)
      } finally {
        this.loading = false
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('pl-PL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    readMore(postId) {
      // Navigate to post detail or show modal
      console.log('Reading post:', postId)
    }
  },
  mounted() {
    this.loadPosts()
  }
}
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 
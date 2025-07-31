<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container mx-auto px-4 py-24">
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
          Znajdź najlepsze <span class="text-yellow-300">oferty finansowe</span>
        </h1>
        <p class="text-xl text-white mb-8 max-w-2xl">
          Porównuj kredyty, konta, lokaty i inne produkty finansowe. 
          Znajdź najlepsze oferty na rynku w kilka sekund.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
          <router-link 
            to="/kredyty-gotowkowe" 
            class="btn-primary text-center"
          >
            Porównaj kredyty
          </router-link>
          <router-link 
            to="/konta-osobiste" 
            class="btn-secondary text-center"
          >
            Sprawdź konta
          </router-link>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">
          Dlaczego Kaszflow?
        </h2>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="feature-card">
            <div class="icon-wrapper">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Najlepsze oferty</h3>
            <p class="text-gray-600">
              Porównujemy setki produktów z całego rynku, aby znaleźć dla Ciebie najlepsze warunki.
            </p>
          </div>
          
          <div class="feature-card">
            <div class="icon-wrapper">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Szybko i prosto</h3>
            <p class="text-gray-600">
              W kilka sekund znajdziesz odpowiedni produkt. Prosty proces bez skomplikowanych formularzy.
            </p>
          </div>
          
          <div class="feature-card">
            <div class="icon-wrapper">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Bezpiecznie</h3>
            <p class="text-gray-600">
              Twoje dane są bezpieczne. Nie przechowujemy wrażliwych informacji finansowych.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Best Offers Section -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">
          Najlepsze oferty
        </h2>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="offer-card">
            <h3 class="text-xl font-semibold mb-4">Kredyty gotówkowe</h3>
            <div class="space-y-3">
              <div v-for="offer in bestOffers.loans" :key="offer.product_id" class="offer-item">
                <div class="flex justify-between items-center">
                  <span class="font-medium">{{ offer.bank_name }}</span>
                  <span class="text-green-600 font-bold">{{ offer.aprc }}%</span>
                </div>
                <div class="text-sm text-gray-600">{{ offer.product_name }}</div>
              </div>
            </div>
            <router-link to="/kredyty-gotowkowe" class="btn-outline mt-4 block text-center">
              Zobacz wszystkie
            </router-link>
          </div>
          
          <div class="offer-card">
            <h3 class="text-xl font-semibold mb-4">Konta osobiste</h3>
            <div class="space-y-3">
              <div v-for="offer in bestOffers.accounts" :key="offer.product_id" class="offer-item">
                <div class="flex justify-between items-center">
                  <span class="font-medium">{{ offer.bank_name }}</span>
                  <span class="text-green-600 font-bold">{{ offer.management_fee_min }} zł</span>
                </div>
                <div class="text-sm text-gray-600">{{ offer.product_name }}</div>
              </div>
            </div>
            <router-link to="/konta-osobiste" class="btn-outline mt-4 block text-center">
              Zobacz wszystkie
            </router-link>
          </div>
          
          <div class="offer-card">
            <h3 class="text-xl font-semibold mb-4">Lokaty</h3>
            <div class="space-y-3">
              <div v-for="offer in bestOffers.deposits" :key="offer.product_id" class="offer-item">
                <div class="flex justify-between items-center">
                  <span class="font-medium">{{ offer.bank_name }}</span>
                  <span class="text-green-600 font-bold">{{ offer.interest_rate }}%</span>
                </div>
                <div class="text-sm text-gray-600">{{ offer.product_name }}</div>
              </div>
            </div>
            <router-link to="/lokaty" class="btn-outline mt-4 block text-center">
              Zobacz wszystkie
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'Home',
  data() {
    return {
      bestOffers: {
        loans: [],
        accounts: [],
        deposits: []
      }
    }
  },
  async mounted() {
    try {
      const response = await fetch('/api/products/best-offers')
      if (response.ok) {
        this.bestOffers = await response.json()
      }
    } catch (error) {
      console.error('Error loading best offers:', error)
    }
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #1d4ed8 100%);
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
  opacity: 0.3;
}

.feature-card {
  @apply bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow;
}

.icon-wrapper {
  @apply w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mb-4;
}

.offer-card {
  @apply bg-white p-6 rounded-lg shadow-md;
}

.offer-item {
  @apply p-3 border-b border-gray-100 last:border-b-0;
}

.btn-primary {
  @apply bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors;
}

.btn-secondary {
  @apply bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold border-2 border-blue-600 hover:bg-blue-50 transition-colors;
}

.btn-outline {
  @apply border-2 border-blue-600 text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-600 hover:text-white transition-colors;
}
</style> 
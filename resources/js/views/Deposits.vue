<template>
  <div class="deposits-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Porównywarka Lokat</h1>
      
      <!-- Results -->
      <div v-if="deposits.length > 0" class="space-y-4">
        <div 
          v-for="deposit in deposits" 
          :key="deposit.product_id"
          class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
              <img 
                :src="deposit.logo_url_format" 
                :alt="deposit.bank_name"
                class="w-16 h-8 object-contain"
              >
              <div>
                <h3 class="text-lg font-semibold">{{ deposit.bank_name }}</h3>
                <p class="text-gray-600">{{ deposit.product_name }}</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-green-600">{{ getMaxRate(deposit.interest_rate) }}%</div>
              <div class="text-sm text-gray-500">Oprocentowanie</div>
            </div>
          </div>
          
          <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
              <div class="text-sm text-gray-500">Okres</div>
              <div class="font-semibold">{{ deposit.period_description }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Kwota min/max</div>
              <div class="font-semibold">{{ formatCurrency(deposit.min_amount) }} - {{ formatCurrency(deposit.max_amount) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Kapitalizacja</div>
              <div class="font-semibold">{{ deposit.capital_interest ? 'Tak' : 'Nie' }}</div>
            </div>
          </div>
          
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
              {{ deposit.interest_rate_description }}
            </div>
            <a 
              :href="deposit.lead_url" 
              target="_blank"
              class="btn-primary"
              @click="trackClick(deposit.product_id)"
            >
              Otwórz lokatę
            </a>
          </div>
        </div>
      </div>
      
      <div v-else-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Ładowanie ofert...</p>
      </div>
      
      <div v-else class="text-center py-8">
        <p class="text-gray-600">Nie znaleziono ofert</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Deposits',
  data() {
    return {
      deposits: [],
      loading: false
    }
  },
  methods: {
    async loadDeposits() {
      this.loading = true
      try {
        const response = await fetch('/api/products/deposits')
        if (response.ok) {
          this.deposits = await response.json()
        }
      } catch (error) {
        console.error('Error loading deposits:', error)
      } finally {
        this.loading = false
      }
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
      }).format(amount)
    },
    getMaxRate(interestRates) {
      if (!interestRates || !Array.isArray(interestRates)) return 0
      return Math.max(...interestRates.map(rate => rate[1] || 0))
    },
    trackClick(productId) {
      if (window.Kaszflow) {
        window.Kaszflow.trackEvent('deposit_click', { product_id: productId })
      }
    }
  },
  mounted() {
    this.loadDeposits()
  }
}
</script> 
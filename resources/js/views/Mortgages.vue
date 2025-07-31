<template>
  <div class="mortgages-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Porównywarka Kredytów Hipotecznych</h1>
      
      <!-- Filters -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4">Filtry</h2>
        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Kwota kredytu</label>
            <input 
              v-model="filters.amount" 
              type="number" 
              class="input-field"
              placeholder="400000"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Okres (lata)</label>
            <input 
              v-model="filters.period" 
              type="number" 
              class="input-field"
              placeholder="30"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Wartość nieruchomości</label>
            <input 
              v-model="filters.property_value" 
              type="number" 
              class="input-field"
              placeholder="500000"
            >
          </div>
        </div>
        <button @click="loadMortgages" class="btn-primary mt-4">
          Wyszukaj kredyty
        </button>
      </div>
      
      <!-- Results -->
      <div v-if="mortgages.length > 0" class="space-y-4">
        <div 
          v-for="mortgage in mortgages" 
          :key="mortgage.product_id"
          class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
              <img 
                :src="mortgage.logo_url_format" 
                :alt="mortgage.bank_name"
                class="w-16 h-8 object-contain"
              >
              <div>
                <h3 class="text-lg font-semibold">{{ mortgage.bank_name }}</h3>
                <p class="text-gray-600">{{ mortgage.product_name }}</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-green-600">{{ mortgage.apr }}%</div>
              <div class="text-sm text-gray-500">RRSO</div>
            </div>
          </div>
          
          <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
              <div class="text-sm text-gray-500">Kwota kredytu</div>
              <div class="font-semibold">{{ formatCurrency(mortgage.amount) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Okres</div>
              <div class="font-semibold">{{ mortgage.period }} lat</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Rata</div>
              <div class="font-semibold">{{ formatCurrency(mortgage.first_installment) }}</div>
            </div>
          </div>
          
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
              Marża: {{ mortgage.margin }}%
            </div>
            <a 
              :href="mortgage.lead_url" 
              target="_blank"
              class="btn-primary"
              @click="trackClick(mortgage.product_id)"
            >
              Złóż wniosek
            </a>
          </div>
        </div>
      </div>
      
      <div v-else-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Ładowanie ofert...</p>
      </div>
      
      <div v-else class="text-center py-8">
        <p class="text-gray-600">Nie znaleziono ofert spełniających kryteria</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Mortgages',
  data() {
    return {
      mortgages: [],
      loading: false,
      filters: {
        amount: 400000,
        period: 30,
        property_value: 500000
      }
    }
  },
  methods: {
    async loadMortgages() {
      this.loading = true
      try {
        const params = new URLSearchParams(this.filters)
        const response = await fetch(`/api/products/mortgages?${params}`)
        if (response.ok) {
          this.mortgages = await response.json()
        }
      } catch (error) {
        console.error('Error loading mortgages:', error)
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
    trackClick(productId) {
      if (window.Kaszflow) {
        window.Kaszflow.trackEvent('mortgage_click', { product_id: productId })
      }
    }
  },
  mounted() {
    this.loadMortgages()
  }
}
</script> 
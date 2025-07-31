<template>
  <div class="loans-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Porównywarka Kredytów Gotówkowych</h1>
      
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
              placeholder="20000"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Okres (miesiące)</label>
            <input 
              v-model="filters.period" 
              type="number" 
              class="input-field"
              placeholder="48"
            >
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Rodzaj rat</label>
            <select v-model="filters.payment_type" class="input-field">
              <option value="s">Równe</option>
              <option value="m">Malejące</option>
              <option value="o">Równe i malejące</option>
            </select>
          </div>
        </div>
        <button @click="loadLoans" class="btn-primary mt-4">
          Wyszukaj kredyty
        </button>
      </div>
      
      <!-- Results -->
      <div v-if="loans.length > 0" class="space-y-4">
        <div 
          v-for="loan in loans" 
          :key="loan.product_id"
          class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
              <img 
                :src="loan.logo_url_format" 
                :alt="loan.bank_name"
                class="w-16 h-8 object-contain"
              >
              <div>
                <h3 class="text-lg font-semibold">{{ loan.bank_name }}</h3>
                <p class="text-gray-600">{{ loan.product_name }}</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-green-600">{{ loan.aprc }}%</div>
              <div class="text-sm text-gray-500">RRSO</div>
            </div>
          </div>
          
          <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
              <div class="text-sm text-gray-500">Kwota kredytu</div>
              <div class="font-semibold">{{ formatCurrency(loan.amount) }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Okres</div>
              <div class="font-semibold">{{ loan.period }} miesięcy</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Rata</div>
              <div class="font-semibold">{{ formatCurrency(loan.first_installment) }}</div>
            </div>
          </div>
          
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
              Całkowita kwota: {{ formatCurrency(loan.total_amount) }}
            </div>
            <a 
              :href="loan.lead_url" 
              target="_blank"
              class="btn-primary"
              @click="trackClick(loan.product_id)"
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
  name: 'Loans',
  data() {
    return {
      loans: [],
      loading: false,
      filters: {
        amount: 20000,
        period: 48,
        payment_type: 's'
      }
    }
  },
  methods: {
    async loadLoans() {
      this.loading = true
      try {
        const params = new URLSearchParams(this.filters)
        const response = await fetch(`/api/products/loans?${params}`)
        if (response.ok) {
          this.loans = await response.json()
        }
      } catch (error) {
        console.error('Error loading loans:', error)
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
        window.Kaszflow.trackEvent('loan_click', { product_id: productId })
      }
    }
  },
  mounted() {
    this.loadLoans()
  }
}
</script> 
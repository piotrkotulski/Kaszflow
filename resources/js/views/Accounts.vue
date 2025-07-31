<template>
  <div class="accounts-page">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-8">Porównywarka Kont Osobistych</h1>
      
      <!-- Results -->
      <div v-if="accounts.length > 0" class="space-y-4">
        <div 
          v-for="account in accounts" 
          :key="account.product_id"
          class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
              <img 
                :src="account.logo_url_format" 
                :alt="account.bank_name"
                class="w-16 h-8 object-contain"
              >
              <div>
                <h3 class="text-lg font-semibold">{{ account.bank_name }}</h3>
                <p class="text-gray-600">{{ account.product_name }}</p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-bold text-green-600">{{ account.management_fee_min }} zł</div>
              <div class="text-sm text-gray-500">Opłata miesięczna</div>
            </div>
          </div>
          
          <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
              <div class="text-sm text-gray-500">Przelewy zewnętrzne</div>
              <div class="font-semibold">{{ account.external_outgoing_transfer_fee_min }} zł</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Bonus za otwarcie</div>
              <div class="font-semibold text-green-600">{{ account.bonus || 'Brak' }}</div>
            </div>
          </div>
          
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
              {{ account.management_fee_exempting_conditions || 'Brak warunków zwolnienia' }}
            </div>
            <a 
              :href="account.lead_url" 
              target="_blank"
              class="btn-primary"
              @click="trackClick(account.product_id)"
            >
              Otwórz konto
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
  name: 'Accounts',
  data() {
    return {
      accounts: [],
      loading: false
    }
  },
  methods: {
    async loadAccounts() {
      this.loading = true
      try {
        const response = await fetch('/api/products/accounts')
        if (response.ok) {
          this.accounts = await response.json()
        }
      } catch (error) {
        console.error('Error loading accounts:', error)
      } finally {
        this.loading = false
      }
    },
    trackClick(productId) {
      if (window.Kaszflow) {
        window.Kaszflow.trackEvent('account_click', { product_id: productId })
      }
    }
  },
  mounted() {
    this.loadAccounts()
  }
}
</script> 
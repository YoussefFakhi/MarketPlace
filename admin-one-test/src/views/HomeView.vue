<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useMainStore } from '@/stores/main'
import { useCategoryStore } from '@/stores/category'
import { useServiceStore } from '@/stores/service'
import { useOrderStore } from '@/stores/order'
import { useStatsStore } from '@/stores/stats'
import {
  mdiAccountMultiple,
  mdiCartOutline,
  mdiChartTimelineVariant,
  mdiReload,
  mdiChartPie,
  mdiViewList,
} from '@mdi/js'
import * as chartConfig from '@/components/Charts/chart.config.js'
import LineChart from '@/components/Charts/LineChart.vue'
import SectionMain from '@/components/SectionMain.vue'
import CardBoxWidget from '@/components/CardBoxWidget.vue'
import CardBox from '@/components/CardBox.vue'
import NotificationBar from '@/components/NotificationBar.vue'
import BaseButton from '@/components/BaseButton.vue'
import CardBoxTransaction from '@/components/CardBoxTransaction.vue'
import CardBoxClient from '@/components/CardBoxClient.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import TableServices from '@/components/TableServices.vue'

const categoryStore = useCategoryStore()
const serviceStore = useServiceStore()
const orderStore = useOrderStore()
const statsStore = useStatsStore()
const mainStore = useMainStore()

const chartData = ref(null)

const fillChartData = () => {
  if (statsStore.charts.labels.length > 0) {
    chartData.value = {
      labels: statsStore.charts.labels,
      datasets: [
        {
          label: 'Daily Revenue ($)',
          fill: false,
          borderColor: '#10b981', // Emerald
          borderWidth: 2,
          borderDash: [],
          borderDashOffset: 0.0,
          pointBackgroundColor: '#10b981',
          pointBorderColor: 'rgba(255,255,255,0)',
          pointHoverBackgroundColor: '#10b981',
          pointBorderWidth: 20,
          pointHoverRadius: 4,
          pointHoverBorderWidth: 15,
          pointRadius: 4,
          data: statsStore.charts.revenue, // Real Revenue Data
          tension: 0.5
        },
        {
          label: 'Daily Orders',
          fill: false,
          borderColor: '#3b82f6', // Blue
          borderWidth: 2,
          data: statsStore.charts.orders, // Real Order Count Data
          tension: 0.5
        }
      ]
    }
  } else {
    chartData.value = chartConfig.sampleChartData()
  }
}

const fetchAllData = async () => {
  await statsStore.fetchStats()
  fillChartData()
  categoryStore.fetchCategories(categoryStore.pagination.current_page)
  serviceStore.fetchServices(serviceStore.pagination.current_page)
  orderStore.fetchOrders(orderStore.pagination.current_page)
}

let polling = null

onMounted(() => {
  fetchAllData() 
  polling = setInterval(fetchAllData, 30000)
})

onUnmounted(() => { //stop polling when component unmounts
  if (polling) clearInterval(polling)
})

const totalCategories = computed(() => categoryStore.categories.length)
const totalServices = computed(() => statsStore.totals.services)
const totalOrders = computed(() => statsStore.totals.orders)
const totalRevenue = computed(() => statsStore.totals.revenue)

// take info from store and reformulate it so we can use it in dashboard
const transactionBarItems = computed(() => {
  return orderStore.orders.slice(0, 4).map(order => ({
    amount: parseFloat(order.montant),
    date: new Date(order.created_at).toLocaleDateString(),
    business: order.service?.title || 'Unknown Service',
    type: order.status === 'completed' ? 'deposit' : 'invoice',
    name: order.client?.name || 'Client',
    account: order.freelancer?.name || 'Freelancer'
  }))
})

const activityItems = computed(() => {
  return serviceStore.services.slice(0, 4).map(service => ({
    name: service.user?.name || 'User',
    login: service.title,
    date: new Date(service.created_at).toLocaleDateString(),
    progress: Math.min(100, Math.round(parseFloat(service.price))), // use price as fake progress for UI
    text: `$${service.price}`,
    type: 'success'
  }))
})
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Overview" main>
      </SectionTitleLineWithButton>

      <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <CardBoxWidget
          trend="Live"
          trend-type="up"
          color="text-emerald-500"
          :icon="mdiCartOutline"
          :number="totalRevenue"
          prefix="$"
          label="Total Revenue"
        />
        <CardBoxWidget
          trend="Transactions"
          trend-type="info"
          color="text-blue-500"
          :icon="mdiChartTimelineVariant"
          :number="totalOrders"
          label="Total Orders"
        />
        <CardBoxWidget
          trend="Marketplace"
          trend-type="up"
          color="text-red-500"
          :icon="mdiViewList"
          :number="totalServices"
          label="Live Services"
        />
      </div>

      <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="flex flex-col justify-between">
          <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Recent Orders" />
          <CardBoxTransaction
            v-for="(transaction, index) in transactionBarItems"
            :key="index"
            :amount="transaction.amount"
            :date="transaction.date"
            :business="transaction.business"
            :type="transaction.type"
            :name="transaction.name"
            :account="transaction.account"
          />
          <div v-if="transactionBarItems.length === 0" class="text-center p-6 text-gray-500">
            No recent orders.
          </div>
        </div>
        <div class="flex flex-col justify-between">
          <SectionTitleLineWithButton :icon="mdiAccountMultiple" title="Latest Services" />
          <CardBoxClient
            v-for="(activity, index) in activityItems"
            :key="index"
            :name="activity.name"
            :login="activity.login"
            :date="activity.date"
            :progress="activity.progress"
            :text="activity.text"
            :type="activity.type"
          />
          <div v-if="activityItems.length === 0" class="text-center p-6 text-gray-500">
            No recent activity.
          </div>
        </div>
      </div>

      <SectionTitleLineWithButton :icon="mdiChartPie" title="Marketplace Trends">
        <BaseButton :icon="mdiReload" color="whiteDark" @click="fetchAllData" />
      </SectionTitleLineWithButton>

      <CardBox class="mb-6">
        <div v-if="chartData">
          <line-chart :data="chartData" class="h-96" />
        </div>
      </CardBox>

      <CardBox has-table>
        <TableServices />
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

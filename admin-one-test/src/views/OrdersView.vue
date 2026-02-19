<script setup>
import { mdiCartOutline, mdiTable } from '@mdi/js'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import TableOrders from '@/components/TableOrders.vue'
import NotificationBar from '@/components/NotificationBar.vue'
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiCartOutline" title="Order Monitoring" main>
      </SectionTitleLineWithButton>

      <NotificationBar color="info" :icon="mdiTable">
        <b>Transaction Logs:</b> Monitor all payments and service orders across the marketplace.
      </NotificationBar>

      <CardBox has-table>
        <TableOrders />
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

<script setup>
import { mdiViewList, mdiTable } from '@mdi/js'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import TableServices from '@/components/TableServices.vue'
import NotificationBar from '@/components/NotificationBar.vue'
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiViewList" title="Service Moderation" main>
      </SectionTitleLineWithButton>

      <NotificationBar color="info" :icon="mdiTable">
        <b>Moderation Panel:</b> Review and manage all services posted by freelancers.
      </NotificationBar>

      <CardBox has-table>
        <TableServices />
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

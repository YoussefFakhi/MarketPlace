<script setup>
import { mdiAccountGroup, mdiTable } from '@mdi/js'
import SectionMain from '@/components/SectionMain.vue'
import CardBox from '@/components/CardBox.vue'
import LayoutAuthenticated from '@/layouts/LayoutAuthenticated.vue'
import SectionTitleLineWithButton from '@/components/SectionTitleLineWithButton.vue'
import TableUsers from '@/components/TableUsers.vue'
import NotificationBar from '@/components/NotificationBar.vue'
</script>

<template>
  <LayoutAuthenticated>
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiAccountGroup" title="User Management" main>
      </SectionTitleLineWithButton>

      <NotificationBar color="info" :icon="mdiTable">
        <b>System Users:</b> Manage all registered freelancers, clients, and administrators.
      </NotificationBar>

      <CardBox has-table>
        <TableUsers />
      </CardBox>
    </SectionMain>
  </LayoutAuthenticated>
</template>

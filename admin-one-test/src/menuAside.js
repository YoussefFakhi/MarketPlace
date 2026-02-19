import {
  mdiAccountCircle,
  mdiMonitor,
  mdiGithub,
  mdiLock,
  mdiAlertCircle,
  mdiSquareEditOutline,
  mdiTable,
  mdiViewList,
  mdiTelevisionGuide,
  mdiResponsive,
  mdiPalette,
  mdiLogout,
  mdiFormatListBulletedType,
  mdiCartOutline,
  mdiAccountGroup,
} from '@mdi/js'

export const menuAsideMain = [
  {
    to: '/dashboard',
    icon: mdiMonitor,
    label: 'Dashboard',
  },
  {
    to: '/users-management',
    label: 'Users',
    icon: mdiAccountGroup,
  },
  {
    to: '/categories',
    label: 'Categories',
    icon: mdiFormatListBulletedType,
  },
  {
    to: '/services-moderation',
    label: 'Services',
    icon: mdiViewList,
  },
  {
    to: '/orders-monitoring',
    label: 'Orders',
    icon: mdiCartOutline,
  },
  {
    to: '/profile',
    label: 'Profile',
    icon: mdiAccountCircle,
  },
]

export const menuAsideBottom = [
  {
    label: 'Logout',
    icon: mdiLogout,
    color: 'info',
    isLogout: true,
  },
]

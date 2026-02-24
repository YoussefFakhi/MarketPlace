import {
  mdiAccountCircle,
  mdiMonitor,
  mdiLogout,
  mdiFormatListBulletedType,
  mdiCartOutline,
  mdiAccountGroup,
  mdiViewList,
  mdiMagnify,
  mdiClipboardListOutline,
  mdiChartBar,
  mdiPlus,
} from '@mdi/js'

//ADMIN SIDEBAR 
export const menuAdmin = [
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

// FREELANCER SIDEBAR 
export const menuFreelancer = [
  {
    to: '/freelancer/dashboard',
    icon: mdiChartBar,
    label: 'Dashboard',
  },
  {
    to: '/freelancer/services',
    icon: mdiViewList,
    label: 'My Services',
  },
  {
    to: '/freelancer/orders',
    icon: mdiClipboardListOutline,
    label: 'My Orders',
  },
  {
    to: '/profile',
    label: 'Profile',
    icon: mdiAccountCircle,
  },
]

// CLIENT SIDEBAR 
export const menuClient = [
  {
    to: '/client/dashboard',
    icon: mdiMonitor,
    label: 'Dashboard',
  },
  {
    to: '/client/browse',
    icon: mdiMagnify,
    label: 'Browse Services',
  },
  {
    to: '/client/orders',
    icon: mdiCartOutline,
    label: 'My Orders',
  },
  {
    to: '/profile',
    label: 'Profile',
    icon: mdiAccountCircle,
  },
]

// BOTTOM MENU (shared by all roles)
export const menuAsideBottom = [
  {
    label: 'Logout',
    icon: mdiLogout,
    color: 'info',
    isLogout: true,
  },
]

// Legacy export to not break old imports
export const menuAsideMain = menuAdmin

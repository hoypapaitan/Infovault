const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') }
    ]
  },
  {
    path: '/user/',
    component: () => import('layouts/LoggedInLayout.vue'),
    children: [
      { 
        path: 'dashboard',
        name: 'userDashboard',
        component: () => import('pages/User/Dashboard.vue') 
      },
      { 
        path: 'scholarships',
        name: 'applyScholarship',
        component: () => import('pages/User/Scholarship.vue') 
      },
      { 
        path: 'documents',
        name: 'attachedDocuments',
        component: () => import('pages/User/Documents.vue') 
      },
      { 
        path: 'profile',
        name: 'userProfile',
        component: () => import('pages/User/Profile.vue') 
      },
    ]
  },
  {
    path: '/admin/',
    component: () => import('layouts/LoggedInLayout.vue'),
    children: [
      { 
        path: 'dashboard',
        name: 'adminDashboard',
        component: () => import('pages/Admin/Dashboard.vue') 
      },
      { 
        path: 'userManagement',
        name: 'adminUserManagement',
        component: () => import('pages/Admin/UsersPage.vue') 
      },
      { 
        path: 'scholarshipManagement',
        name: 'adminScholarManagement',
        component: () => import('pages/Admin/ScholarshipPage.vue') 
      },
      { 
        path: 'announcements',
        name: 'adminAnnouncementManagement',
        component: () => import('pages/Admin/Announcements.vue') 
      },
      { 
        path: 'settings',
        name: 'adminUserSettings',
        component: () => import('pages/Admin/Settings.vue') 
      },
    ]
  },
  {
    path: '/scholar-unit/',
    component: () => import('layouts/LoggedInLayout.vue'),
    children: [
      { 
        path: 'dashboard',
        name: 'unitDashboard',
        component: () => import('pages/Unit/Dashboard.vue') 
      },
      { 
        path: 'scholarshipManagement',
        name: 'unitScholarManagement',
        component: () => import('pages/Admin/ScholarshipPage.vue') 
      },
      {
        path: 'announcementManagement',
        name: 'unitAnnouncementManagement',
        component: () => import('pages/Admin/ScholarshipPage.vue') 
      },
      {
        path: 'approvedScholars',
        name: 'unitApprovedScholars',
        component: () => import('pages/Unit/ApprovedList.vue') 
      },
      {
        path: 'declinedScholars',
        name: 'unitUnapprovedScholars',
        component: () => import('pages/Unit/DeclinedList.vue') 
      },
    ]
  },
  

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes

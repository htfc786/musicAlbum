const routes = [
  {
      path: '/',
      name: 'index',
      title: '首页',
      component: () => import('@/components/HelloWorld.vue'),
  },
  {
    path: '/helloworld2',
    name: 'helloworld2',
    title: 'HelloWorld2 page',
    component: () => import('@/components/HelloWorld2.vue'),
  },
]
export default routes
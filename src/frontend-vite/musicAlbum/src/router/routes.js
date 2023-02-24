const routes = [
  {
      path: '/',
      name: 'index',
      title: '首页',
      component: () => import('@/views/index.vue'),
  },
  {
    path: '/login',
    name: 'login',
    title: '登录页',
    component: () => import('@/views/login.vue'),
  },
  {
    path: '/register',
    name: 'register',
    title: '注册页',
    component: () => import('@/views/register.vue'),
  },
  {
    path: '/albumshow/:albumId/',
    name: 'albumshow',
    title: '查看相册',
    component: () => import('@/views/albumshow.vue'),
  },
  {
    path: '/albumphoto/:albumId/',
    name: 'albumphoto',
    title: '相册图片',
    component: () => import('@/views/albumphoto.vue'),
  },
  {
    path: '/albummake/:albumId/',
    name: 'albummake',
    title: '制作相册-首页',
    component: () => import('@/views/albummake.vue'),
  },
  {
    path: '/albummake/:albumId/template',
    name: 'albummake-template',
    title: '制作相册-模板',
    component: () => import('@/views/albummake-template.vue'),
  },
  {
    path: '/albummake/:albumId/music',
    name: 'albummake-music',
    title: '制作相册-音乐',
    component: () => import('@/views/albummake-music.vue'),
  },
  {
    path: '/albummake/:albumId/photo',
    name: 'albummake-photo',
    title: '制作相册-照片',
    component: () => import('@/views/albummake-photo.vue'),
  },
  {
    path: '/albummake/:albumId/write',
    name: 'albummake-write',
    title: '制作相册-文字',
    component: () => import('@/views/albummake-write.vue'),
  },
]
export default routes
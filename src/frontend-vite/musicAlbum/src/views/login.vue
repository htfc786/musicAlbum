<template>
  <div id="app">
    <h1 id="page-title">登录</h1>
    <hr>
    <div id="page-title">
      用户名：<input v-model="username" type="text" name="username" />  
      <br/>  
      密码：<input v-model="password" type="password" name="password" />  
      <br/>
      {{ info }}
      <input @click="login()" type="submit" value="登录" />
      <br/>  
      <span>没有账号？<router-link :to="{ name: 'register' }">注册</router-link></span>
    </div>
    <hr>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      username: "",
      password: "",
      info: ""
    }
  },
  mounted: function () {  // 打开页面时执行
    //判断是否登录
    if (localStorage.getItem("access_token") != null) {
      this.$router.push({ name:"index" });
      return;
    }
  },
  methods:{ // 方法
    login: function () {
      let that = this
      axios({ // 请求登录
          method: 'POST',
          url:'/albumapi-user-login',
          data: {
            username: this.username,
            password: this.password,
          }
        })
        .then(res => {
          console.log(res.data)
          if (res.data.code != 200){  //不是200 处理失败
            that.info = res.data.error;
            return;
          }
          //存localStorage
          localStorage.setItem('access_token', res.data.access_token);
          localStorage.setItem('userid', res.data.user_id);
          localStorage.setItem('username', this.username);

          this.$router.push({ name:"index" });
        });
    }
  },
}
</script>

<style scoped>
  body {
    background: #f3f3f3;
  }
  #app {
    background: #fff;
    margin: 0 auto;
    padding: 10px;
  }
  #page-title {
    text-align: center;
  }
</style>

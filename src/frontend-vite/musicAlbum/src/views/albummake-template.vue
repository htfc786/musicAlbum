<template>
  <div id="template">
    <div id="template-header">
      
      <router-link :to="{ name: 'albummake' }"><div class="template-close">返 回</div></router-link>
      <div id="template-menu">
        <div class="template-menu-btn">全部</div>
      </div>
    </div>
    <div id="template-box">
      <div class="template-item" v-for="template in templates" @click="changeTemplate(template._id)">
        <img :src="template.templateCover">
        <span>{{template.templateName}}</span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data() {  //数据
    return {
      username: "",
      access_token: "",
      albumId: "",
      // 模板
      templates: [],
    }
  },
  mounted: function () {  // 打开页面时执行
    if (localStorage.getItem("access_token") == null) {
      location.href = "./login.html";
      return;
    }
    this.username = localStorage.getItem("username");
    this.access_token = localStorage.getItem("access_token");
    this.albumId = this.$route.params.albumId;

    this.getTemplate();
  },
  methods: {
    getTemplate: function() {
      const that = this;
      axios({
          url: '/albumapi-make-template-getlist',
          method: 'post',
          data: {
            access_token: this.access_token,
          },
        })
        .then(function (e) {
          that.templates = e.data.data;
        })
    },
    changeTemplate: function(templateId) {
      const that = this;
      axios({
          url: '/albumapi-make-template-change',
          method: 'post',
          data: {
            access_token: this.access_token,
            albumId: this.albumId,
            templateId: templateId
          },
        })
        .then(function (e) {
          if (e.data.code != 200){
            alert("发生错误："+e.data.error)
            return;
          }
          alert(e.data.msg)
          that.$router.push({ name: 'main' });
        })
    },
  }
}
</script>

<style scoped>
#template-header {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0px;
    width: 100%;
    height: 60px;
    background-color: #fff;
    z-index: 999;
}
#template-menu {
    white-space: nowrap;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    height: 60px;
    line-height: 58px;
    background-color: #fff;
    border-bottom: 1px solid #D6D4D4;
    overflow: auto hidden;
    margin: 0 0 0 80px;
}
#template-menu div {
    font-size: 16pt;
    text-align: center;
    margin: 0 7.5px;
    display: inline-block;
}
.template-menu-btn{
    color: rgb(181, 181, 181); 
    width: auto; 
    margin: 0px 7.5px; 
    border-bottom: 0px solid rgb(255, 255, 255);
}
.template-menu-btnon{
    border-bottom: 2px solid #444;
    color: #444;
}
#template-box {
    position: absolute;
    margin-top: 60px;
    /* height: 620px; */
    /* width: 500px; */
    /* overflow-y: auto; */
    -webkit-overflow-scrolling: touch;
}
.template-item {
    position: relative;
    float: left;
    width: 150px;
    margin: 10px 0 0 12px;
    height: 190px;
    border-left: 1px solid #dddddd;
    border-top: 1px solid #dddddd;
    box-shadow: 2px 2px 3px #dddddd;
    padding: 5px;
    background-color: #fff;
    /* visibility: hidden; */
    visibility: visible;
}
.template-item img {
    position: relative;
    width: 129px;
    height: 129px;
    margin-left: 5px;
}
.template-item span {
    width: 100%;
    line-height: 40px;
    text-align: center;
    display: block;
    font-size: 15pt;
    /* font-family: '微软雅黑'; */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.template-item p {
    margin: 0;
    color: #999;
    font-size: 5pt;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.template-close {
    position: absolute;
    width: 72px;
    height: 40px;
    margin-top: 10px;
    /* left: 422px; */
    color: white;
    background-color: #444;
    border-radius: 20px;
    font-size: 19px;
    line-height: 39px;
    text-align: center;
    padding-left: 0px;
    margin-left: 8px;
}
#template-getInfo-loading{
    position: absolute;
    /* top: 40%; */
    width: 100px;
    /* left: 100px; */
    height: 135px;
    border-radius: 5px;
    overflow: hidden;
    margin: auto;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
}
#template-getInfo-loading-background{
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 0;
    background-color: black;
    opacity: 0.68;
}
#template-getInfo-loading-image{
    position: absolute;
    z-index: 1;
    top: 15px;
    left: 16px;
    animation: image-turn 1s linear infinite;
}
#template-getInfo-loading-text{
    position: absolute;
    line-height: 70px;
    top: 70px;  
    left: 0;
    right: 0;
    text-align: center;
    z-index: 1;
    color: white;
    font-size: 22px;
}
</style>

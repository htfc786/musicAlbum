const Template = {
  template: /*html*/`
  <div id="template">
    <div id="template-header">
      
      <router-link :to="{ name: 'main' }"><div class="template-close">返 回</div></router-link>
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
  `,
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
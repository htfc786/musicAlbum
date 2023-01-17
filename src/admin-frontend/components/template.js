const Template = {
  template: /*html*/ `
  <div id="music">
    <div class="container">
      <!-- css样式代码：https://v3.bootcss.com -->
      <!--面板的情景样式-->
      <div class="panel">
        <div class="panel-heading" style="position: relative;">
          <!--面板的标题-->
          <h2 class="panel-title">模板管理</h2>
          <div style="position: absolute;display: block;top: 0;right: 0;margin: 10px;">
            <a @click="getMusic()">重新加载</a>
            <router-link :to="{ name: 'template_add' }" style="padding: 8px;">添加模板</router-link>
          </div>
        </div>
        <!--面板的主体-->
        <!--在面板中嵌入一个表格-->
        
        <div class="panel-heading" v-show="!templates.length">
          <h3 class="panel-title">暂无数据</h3>
        </div>

        <table class="table">
          <thead>
            <tr class="bg-success">
              <td>模板id</td>
              <td>模板名称</td>
              <td>模板封面</td>
              <td>模板路径</td>
              <td>上传用户</td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="template in templates">
              <td>{{ template._id }}</td>
              <td>{{ template.templateName }}</td>
              <td><img class="templateIMG" :src="template.templateCover" /></td>
              <td>{{ template.templatePath }}</td>
              <td>{{ template.userId }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <!--
      <div class="row">
        <ul class="be-pager">
          <li title="首页:1" class="be-pager-prev"><a href="?page=1">首页</a></li>
          <li title="上一页:$FootLastPage" class="be-pager-prev"><a href="?page=$FootLastPage">上一页</a></li>
      
          <li title="下一页:$FootNextPage" class="be-pager-prev"><a href="?page=$FootNextPage">下一页</a></li>
          <li title="尾页:$allPage" class="be-pager-prev"><a href="?page=$allPage">尾页</a></li>
          
          <span class="be-pager-total">第 $page 页，共 $allPage 页，</span>
          <span class="be-pager-options-elevator">
            跳至 <input type="text" class="space_input" id="page_input"> 页
          </span>
        </ul>
      </div>
      -->
    </div>
  </div>
  `,
  data() {  //数据
    return {
      access_token: "",
      templates: []
    }
  },
  mounted: function () {  // 打开页面时执行
    if (localStorage.getItem("access_token") == null) {
      location.href = "./login.html";
      return;
    }
    this.username = localStorage.getItem("username");
    this.access_token = localStorage.getItem("access_token");

    this.getMusic();
  },
  methods: {
    getMusic: function(){
      const that = this;
      axios({
          url: '/albumadmin-template-get',
          method: 'post',
          data: {
            access_token: this.access_token,
          },
        })
        .then(function (e) {
          that.templates = e.data.data;
        })
    },
  }
}
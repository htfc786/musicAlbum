const Music = {
  template: /*html*/ `
  <div id="music">
    <div class="container">
      <!-- css样式代码：https://v3.bootcss.com -->
      <!--面板的情景样式-->
      <div class="panel">
        <div class="panel-heading" style="position: relative;">
          <!--面板的标题-->
          <h2 class="panel-title">音乐管理</h2>
          <div style="position: absolute;display: block;top: 0;right: 0;margin: 10px;">
            <a @click="getMusic()">重新加载</a>
            <router-link :to="{ name: 'music_add' }" style="padding: 8px;">添加音乐</router-link>
          </div>
        </div>
        <!--面板的主体-->
        <!--在面板中嵌入一个表格-->
        
        <div class="panel-heading" v-show="!musics.length">
          <h3 class="panel-title">暂无数据</h3>
        </div>

        <table class="table">
          <thead>
            <tr class="bg-success">
              <td>音乐id</td>
              <td>音乐名</td>
              <td>作曲家</td>
              <td>音乐文件</td>
              <td>上传用户</td>
              <td>操作</td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="music in musics">
              <td>{{ music._id }}</td>
              <td>{{ music.musicName }}</td>
              <td>{{ music.musicComposer }}</td>
              <td><a :href="music.musicUrl" :download="music.musicName">点击查看</a></td>
              <td>{{ music.userId }}</td>
              <td>
                <a @click="delMusic(music._id)">删除</a>
              </td>
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
      musics: []
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
          url: '/albumadmin-music-get',
          method: 'post',
          data: {
            access_token: this.access_token,
          },
        })
        .then(function (e) {
          that.musics = e.data.data;
        })
    },
    delMusic: function(musicId){
      if(!confirm("此操作将会删除此音乐，确定要继续吗？")){
        return;
      }
      const that = this;
      axios({
          url: '/albumadmin-music-del',
          method: 'post',
          data: {
            access_token: this.access_token,
            musicId: musicId,
          },
        })
        .then(function (e) {
          alert("删除成功")
          that.getMusic();
        })
    }
  }
}
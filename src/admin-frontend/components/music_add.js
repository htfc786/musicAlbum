const MusicAdd = {
  template: /*html*/ `
  <div class="container">
    <div class="panel-heading">
      <div style="position: absolute;display: block;margin: 10px;">
        <router-link :to="{ name: 'music' }" style="padding: 8px;">&lt;=返回</router-link>
      </div>
      <h2 class="panel-title">添加音乐</h2>
    </div>
    <div class="from-lable">
      <el-form 
        ref="addMusicForm"
        :rules="musicFormRules"
        :model="musicForm"
        label-width="auto"
        label-position="right"
        size="large"
      >
        <el-form-item label="音乐名：" prop="name">
          <el-input v-model="musicForm.name" />
        </el-form-item>
        
        <el-form-item label="作曲家："  prop="composer">
          <el-input v-model="musicForm.composer" />
        </el-form-item>

        <el-form-item label="音乐文件：" prop="musicFile">
          <el-upload
            ref="upload"
            :data="musicForm.uploadForm"
            :rules="musicFormRules"
            :limit="1"
            :on-exceed="handleExceed"
            :auto-upload="false"
            accept=".mp3,.ogg,.m4a,.wav"
          >
              <el-button type="primary">选择音乐文件</el-button>
              <template #tip>
                <div class="el-upload__tip text-red" style="font-size: 14px; margin: 0;">
                  请选择音乐文件，只能选择一个文件哦
                </div>
              </template>
          </el-upload>
        </el-form-item>

        <el-button type="primary" @click="submit()">提交</el-button>
      </el-form>
      
      <div style=" margin-top: 15px; " v-show="progress.show">
      上传进度：<el-progress :percentage="progress.percentage" />
      </div>
    </div>
  </div>
  `,
  data() {  //数据
    return {
      musicFormRules: {
        name: [
          {required: true, message: "请填写音乐名称", trigger: "blur"},
          {max: 50, message: "最长可输入50个字符", trigger: "blur"},
        ],
        musicFile: [
          {required: true, message: "请选择上传文件", trigger: "blur"},
        ]
      },
      musicForm: {
        name: '',
        composer: '',
      },
      progress: {
        show: false,
        percentage: 0
      }
    }
  },
  mounted: function () {  // 打开页面时执行
    if (localStorage.getItem("access_token") == null) {
      location.href = "./login.html";
      return;
    }
    this.username = localStorage.getItem("username");
    this.access_token = localStorage.getItem("access_token");
  },
  methods:{ // 方法
    handleExceed: function () {
      alert('只能选择一个文件！')
    },
    submit: function(){
      const uploadFiles = document.querySelector(".el-upload__input")
      if (!uploadFiles.files[0]) {
        alert("请选择文件！");
        return;
      }

      let fd = new FormData();

      fd.append("access_token", this.access_token);
      fd.append("musicName", this.musicForm.name);
      fd.append("musicComposer", this.musicForm.composer);

      for (var i=0;i<uploadFiles.files.length;i++) {
        fd.append("files["+i+"]", uploadFiles.files[i]);
      }

      const that = this;
      axios({
          url: '/albumadmin-music-add',
          method: 'post',
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          data: fd,
          onUploadProgress: function (e) {
            let filePercent = Math.round(e.loaded / e.total * 100);
            that.progress.show = true;
            that.progress.percentage = filePercent;
          },
        })
        .then(function(e){
          alert("上传成功！");
          that.$router.push({ name: 'music' });
        })
    },
  }
}
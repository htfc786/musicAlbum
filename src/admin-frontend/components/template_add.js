const TemplateAdd = {
  template: /*html*/ `
  <div class="container">
    <div class="panel-heading">
      <div style="position: absolute;display: block;margin: 10px;">
        <router-link :to="{ name: 'template' }" style="padding: 8px;">&lt;=返回</router-link>
      </div>
      <h2 class="panel-title">添加模板</h2>
    </div>
    <div class="from-lable">
      <el-steps :active="steps" align-center>
        <el-step title="步骤1 填写信息" />
        <el-step title="步骤2 上传文件" />
        <el-step title="步骤3 完成！" />
      </el-steps>

      <el-form 
        :rules="templateFormRules"
        :model="templateForm"
        label-width="auto"
        label-position="right"
        size="large"
        v-show="steps==0"
      >
        <el-form-item label="模板名：" prop="name">
          <el-input v-model="templateForm.name" />
        </el-form-item>
        
        <el-form-item label="模板封面：" prop="IMGFile">
          <el-upload
            ref="upload"
            :rules="templateFormRules"
            :limit="1"
            :on-exceed="handleExceed"
            :auto-upload="false"
            accept=".jpg,.jpeg,.png,.bmp,.gif,.webp"
          >
              <el-button type="primary">选择封面图片文件</el-button>
              <template #tip>
                <div class="el-upload__tip text-red" style="font-size: 14px; margin: 0;">
                  请选择封面图片文件，只能选择一个文件哦
                </div>

              </template>
          </el-upload>
        </el-form-item>

        <el-button type="primary" @click="submit()">提交</el-button>

        <div style=" margin-top: 15px; " v-show="progress.show">
        上传进度：<el-progress :percentage="progress.percentage" />
        </div>
      </el-form>
      <div v-show="steps==1" style=" font-size: 20px; ">
      <h3>恭喜！上传成功！</h3>
      由于云存储限制问题，无法上传模板文件夹，请直接去云存储管理页面上传模板文件和资源文件
      请将文件上传至云存储目录： <b style=" color: red; ">{{ serverPath }} </b><br/>
      注意：目录里一定要有<b>index.html</b>，云存储文件夹里的文件<b>不要删除</b>，也<b>不要手动将其修改名称或者将其覆盖</b>，上传的文件里，也<b>不要有与之名称相同的文件和文件夹</b>，谢谢配合。<br/>
      具体请查看网址: 
      <a href="">111</a><br/>
      <el-button type="primary" @click="upadteOk()">提交</el-button>
      </div>
      
      
    </div>
  </div>
  `,
  data() {  //数据
    return {
      templateFormRules: {
        name: [
          {required: true, message: "请填写模板名称", trigger: "blur"},
          {max: 50, message: "最长可输入50个字符", trigger: "blur"},
        ],
        IMGFile: [
          {required: true, message: "请选择上传文件", trigger: "blur"},
        ]
      },
      templateForm: {
        name: '',
      },
      progress: {
        show: false,
        percentage: 0
      },
      steps: 0,
      serverPath: "",
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
      fd.append("templateName", this.templateForm.name);

      for (var i=0;i<uploadFiles.files.length;i++) {
        fd.append("files["+i+"]", uploadFiles.files[i]);
      }

      const that = this;
      axios({
          url: '/albumadmin-template-add',
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
          if(e.data.code==200){
            alert("信息上传成功！");
            that.progress.show = false;
            that.steps = 1;
            that.serverPath = e.data.templatePath;
            return;
          }
          alert("发生问题："+e.data.error);
          
        })
    },
    upadteOk: function(){
      that.$router.push({ name: 'template' });
    },
  }
}
const Template = {
  template: /*html*/`
  <div id="template">
    <div id="template-header">
      
      <router-link :to="{ name: 'main' }"><div class="template-close">返 回</div></router-link>
      <div id="template-menu">
        <div class="template-menu-btn" onclick="getTemplateGroup('all',this)">全部</div>
        <div class="template-menu-btn" onclick="getTemplateGroup('$groupId',this)">$groupName</div>
      </div>
    </div>
    <div id="template-box">    
      
    </div>
    <div id="template-getInfo-loading" style="display:none;">
      <div id="template-getInfo-loading-background"></div>
      <img id="template-getInfo-loading-image" src="./src/image/make-image-loading.png">
      <span id="template-getInfo-loading-text">加载中</span>
    </div>
  </div>
  `
}
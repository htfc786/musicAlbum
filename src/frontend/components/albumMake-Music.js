const Music = {
  template: /*html*/`
  <div id="music">
    <div id="music-header">
      <div id="music-search-btn" onclick="searchMusic()">搜索</div>
        <router-link :to="{ name: 'main' }"><div class="music-close">返回</div></router-link>
      <div id="music-menu">
        <div class="music-menu-btn" id="music-menu-search" onclick="searchMusic()" style="display:none;">搜索结果</div>
        <div class="music-menu-btn music-menu-btnon" onclick="getMusicGroup('all',this)">全部</div>
        
        <div class="music-menu-btn" onclick="getMusicGroup('$groupId',this)">$groupName</div>
      </div>
    </div>
    <div class="music-list" id="music-list">

    </div>

    <div id="music-getInfo-loading" style="display:none;">
      <div id="music-getInfo-loading-background"></div>
      <img id="music-getInfo-loading-image" src="/src/image/make-image-loading.png">
      <span id="music-getInfo-loading-text">加载中</span>
    </div>
    <audio src="" id="musicPlayer" style="display:none;"></audio>
  </div>
  `
}
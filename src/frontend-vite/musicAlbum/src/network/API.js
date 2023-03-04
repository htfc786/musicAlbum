import { post,form } from '@/network/request'

export default {
  
  user:{
    // 登录
    login: function(username, password){
      return post("/albumapi-user-login", {
        username: username,
        password: password,
      })
    },
    // 注册
    register: function(username, password, confirm){
      return post("/albumapi-user-register", {
        username: username,
        password: password,
        confirm: confirm,
      })
    },
  },
  album: {
    getmy: function(){
      return post("/albumapi-album-getmy", {
        access_token: localStorage.getItem("access_token")
      })
    },
    add: function(){
      return post("/albumapi-album-add", {
        access_token: localStorage.getItem("access_token")
      })
    },
    del: function(albumId){
      return post("/albumapi-album-del", {
        access_token: localStorage.getItem("access_token"),
        album_id: albumId,
      })
    },
  },
  show: {
    albumdata: function(albumId){
      return post("/albumapi-show-albumdata", {
        albumId: albumId,
      })
    },
    getphoto: function(albumId){
      return post("/albumapi-show-photo-get", {
        albumId: albumId,
      })
    },
  },
  make: {
    template: {
      getlist: function(){
        return post("/albumapi-make-template-getlist", {
          access_token: localStorage.getItem("access_token"),
        })
      },
      change: function(albumId, templateId){
        return post("/albumapi-make-template-change", {
          access_token: localStorage.getItem("access_token"),
          albumId: albumId,
          templateId: templateId,
        })
      },
    },
    music: {
      getlist: function(){
        return post("/albumapi-make-music-getlist", {
          access_token: localStorage.getItem("access_token"),
        })
      },
      change: function(albumId, musicId){
        return post("/albumapi-make-music-change", {
          access_token: localStorage.getItem("access_token"),
          albumId: albumId,
          musicId: musicId,
        })
      },
    },
    photo: {
      add: function(fd, onUploadProgress){
        return form("/albumapi-make-photo-add", fd, onUploadProgress)
      },
      del: function(photoId){
        return post("/albumapi-make-photo-del", {
          access_token: this.access_token,
          photoId: photoId,
        })
      },
      move: function(photoId, photoAction){
        return post("/albumapi-make-photo-move", {
          access_token: localStorage.getItem("access_token"),
          photoId: photoId,
          photoAction: photoAction,
        })
      },
    },
    write: {
      changename: function(albumId, changeName){
        return post("/albumapi-make-album-namechange", {
          access_token: localStorage.getItem("access_token"),
          albumId: albumId,
          newName: changeName,
        })
      },
      change: function(photoId, changePhotoText){
        return post("/albumapi-make-write-change", {
          access_token: localStorage.getItem("access_token"),
          photoId: photoId,
          photoAction: changePhotoText,
        })
      },
    },
  },

  
}
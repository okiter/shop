$(function(){
  //>>1.复选框的状态
  //全选或者是全不选
  $('.check_all').click(function(){
    $('.id').prop('checked',$('.check_all').prop('checked'));
  });
  //通过下面的控制全选复选框
  $('.id').click(function(){
    $('.check_all').prop('checked',$('.id:not(:checked)').length==0);
  });


  //通用的ajax的get请求
  $('.ajax-get').click(function(){
    //>>2.从当前标签中获取url地址
    var url = $(this).attr('href');
    //>>1.发送ajax的get请求
    $.get(url,function(data){
      //>>3.显示提示框
      showMsg(data);
    });
    return false; //取消默认操作
  });


  //通用ajax的post的请求
  $('.ajax-post').click(function(){
    //url地址
    var url = $(this).attr('url')?$(this).attr('url'):$(this).closest('form').attr('action');
    //准params   $('.id').serialize() 只获取打勾的表单元素的值
    var params = $(this).attr('url')?$('.id').serialize():$(this).closest('form').serialize() ;
    $.post(url,params,function(data){
      showMsg(data);
    });
    return false;  //取消掉默认操作
  });




  //>>3.显示提示框
  function showMsg(data){
    layer.msg(data.info, {
      offset: 0,
      icon: data.status?1:2,   //显示不同的图标
      time:1000   //1秒钟之后执行下面的函数
    },function(){
      //>>4.隐藏之后刷新
      if(data.url){
        //如果有url地址,跳转到指定的url地址
        location.href = data.url;
      }else{
        //如果没有url地址,自身刷新
        location.reload();
      }
    });
  }

});
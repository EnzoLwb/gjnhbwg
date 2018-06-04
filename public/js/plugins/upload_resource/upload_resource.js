/**
 * Created by Administrator on 2017/10/16.
 */
function upload_resource(title,uploaded_type,id,type){
    var now_num=$('#'+id).children().length;
    var url = UPLOAD_RESOURCE_URL+"/" + uploaded_type+"/"+id+"/"+type+"/"+now_num;
    layer.open({
        title: title,
        type: 2,
        area: ['800px', '480px'],
        fix: true, //固定
        maxmin: false,
        move: false,
        resize: false,
        zIndex: 100000,
        content: url
    });
}

function del_img($this){
    console.log($(this));
    $this.parent().remove();
}
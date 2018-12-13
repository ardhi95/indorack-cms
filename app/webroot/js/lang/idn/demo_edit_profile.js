$(function(){

    function onSuccess(){
        $("#cp_photo").parent("a").find("span").html("Pilih photo lain");

        var img = $("#cp_target").find("#crop_image")

        if(img.length === 1){
            $("#cp_img_path").val(img.attr("src"));

            img.cropper({aspectRatio: 1,
                        done: function(data) {
                            $("#ic_x").val(data.x);
                            $("#ic_y").val(data.y);
                            $("#ic_h").val(data.height);
                            $("#ic_w").val(data.width);
                        }
            });

            $("#cp_accept").prop("disabled",false).removeClass("disabled");

            $("#cp_accept").on("click",function(){
                $("#user_image").html('<img src="'+cms_url+'img/loaders/default.gif"/>');
                $("#modal_change_photo").modal("hide");

                $("#cp_crop").ajaxForm({target: '#user_image'}).submit();
                $("#cp_target").html("Gunakan form di bawah untuk upload photo. Hanya file dengan ekstensi (*.gif,*.jpeg,*.jpg,*.png) yang di perbolehkan");
                $("#cp_photo").val("").parent("a").find("span").html("Upload File");
                $("#cp_accept").prop("disabled",true).addClass("disabled");
                $("#cp_img_path").val("");
            });
        }
    }

    $("#cp_photo").on("change",function(){
        if($("#cp_photo").val() == '') return false;
        $("#cp_target").html('<img src="'+cms_url+'img/loaders/default.gif"/>');
        $("#cp_upload").ajaxForm({target: '#cp_target',success: onSuccess}).submit();
    });


});

<div class="panel panel-default">
    <div class="panel-body">
		<?php echo do_shortcode( '[avatar_upload]' ); ?>
    </div>
</div>

<style>
    .wpua-edit-container button, input[type=submit] {
        margin-top: 10px !important;
        border: 0px !important;
        background: #e3e3e3 !important;
        font-family: tahoma !important;
        font-weight: normal !important; !important;
    }
    .wpua-edit-container button:hover {
        background: #e3e3e3 !important;
        color: #fff !important;
    }

    input[type=submit] {
        background: #3385ff !important;
        color: #fff !important;
    }

    h3, .h3-typography {
        font-family: "iransans", Helvetica, Arial, sans-serif;
        font-size: 20px;
        line-height: 40px;
        font-weight: 400;
        font-style: normal;
    }
    #wpua-max-upload-existing, #wpua-edit-attachment-existing {display: none;}
</style>
<script>
    jQuery(document).ready(function ($) {
        var contaier = $(".wpua-edit-container");

        contaier.find("h3").html("آواتار خود را انتخاب کنید");
        contaier.find("span#wpua-allowed-files-existing").html("تنها فایل با پسوند jpg و حداکثر حجم دو مگابایت");
        contaier.find("button#wpua-upload-existing").html("آپلود فایل");
        contaier.find("button#wpua-remove-existing").html("حذف عکس فعلی");
        contaier.find("button#wpua-undo-existing").html("برگشت به قبلی");
        $("input[type=submit]").attr("value", "ویرایش پروفایل");

    });
</script>
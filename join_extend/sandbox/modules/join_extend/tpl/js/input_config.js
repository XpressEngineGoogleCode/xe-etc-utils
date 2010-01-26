function doMark() {
    for(i = 0; i < required.length; i++)    markRequired(required[i]);
}

// 필수항목 표시
function markRequired(name) {
    jQuery("[name="+name+"]").parent().siblings("th").children("div").append('<span class="require">*</span>');
}
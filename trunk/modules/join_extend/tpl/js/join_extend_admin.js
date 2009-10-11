
/* 스킨 컬러셋 구해옴 */
function doGetSkinColorset(skin) {
    var params = new Array();
    params['skin'] = skin;

    var response_tags = new Array('error','message','tpl');
    exec_xml('join_extend', 'getJoin_extendAdminColorset', params, doDisplaySkinColorset, response_tags);
}

function doDisplaySkinColorset(ret_obj) {
    var tpl = ret_obj["tpl"];
    var old_height = xHeight("skin_colorset");
    xInnerHtml("skin_colorset", tpl);
    var new_height = xHeight("skin_colorset");
    if(typeof(fixAdminLayoutFooter)=="function") fixAdminLayoutFooter(new_height - old_height);
}

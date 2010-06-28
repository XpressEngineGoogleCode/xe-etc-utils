<?php
    /**
     * @file   jp.lang.php
     * @author flysky (sinsy200@gmail.com) 翻訳：HIRO
     * @brief  日本語言語パッケージ
     **/

    $lang->join_extend = '会員加入拡張';
    $lang->run_update = 'アップデートになりました.追加で会員加入拡張設定に行ってテーブルアップデートを行ってください.';
    $lang->request_update_table = '会員 DB アップデートが必要で加入が制限されました.管理者に要請してください';
    $lang->admin_request_update_table = '会員 DB アップデートが必要です.';
    
    $lang->update_table = 'テーブルアップデート';
    $lang->about_update_table = 'Ver.0.4.2から会員加入拡張のための別途のテーブルを利用します.<br/>テーブルアップデートをクリックすれば既存 member テーブルにある住民登録番号情報を新しいテーブルで移します.<br/>データは段階的に処理されます.完了になるまでお待ちください.<br/><span style="color: red"><strong>必ず会員 DB バックアップ後行ってください.</strong></span>';
    $lang->complete_update_table = '会員 DB アップデートが完了しました.';
    
    $lang->join_extend_title = '会員加入 1段階'; 

    $lang->basic_config = '基本設定';
    $lang->agree_config = '約款設定';
    $lang->extend_var_config = '拡張変数連動';
    $lang->restrictions_config = '加入制限設定';
    $lang->after_config = '加入と脱退後処理';
    $lang->jumin_config = '住民登録番号設定';

    $lang->input_config = '情報入力設定';
    $lang->about_input_config = '会員情報入力項目の必須可否,修正禁止,長さ制限等ができます. <br />拡張変数の必須可否は [会員管理]-[加入フォーム管理]で直接設定してください.<br/>拡張変数連動に設定した項目はこちらの設定と関係なく修正が禁止されます.';
    $lang->length = '長さ';
    $lang->great_than = '以上';
    $lang->less_than = '以下';
    $lang->no_modification = '修正禁止';
    $lang->birthday_no_mod_op1 = '修正禁止(加入の時可能)';
    $lang->birthday_no_mod_op2 = '修正禁止';
    $lang->msg_user_id_length = 'IDの長さ制限は 3~20 間で可能です.';
    $lang->msg_user_name_length = '名前の長さ制限は 2~40 間で可能です.';
    $lang->msg_nick_name_length = 'ニックネームの長さ制限は 2~40 間で可能です.';
    $lang->msg_email_length = '電子メールの長さ制限は 1~200 間で可能です.';
    $lang->msg_length = '文字長さ(%s)';
    
    $lang->my_about_user_id = '使用者 IDは %s字の間の英文+数字でなければならないし英文で始まらなければなりません.';
    $lang->my_about_password = 'パスワードは %s字にならなければなりません.';
    $lang->my_about_user_name = '名前は %s字以内ではなければなりません.';
    $lang->my_about_nick_name = 'ニックネームは %s字以内ではなければなりません.';
    
    $lang->use_join_extend = '会員加入拡張使用';
    $lang->about_use_join_extend = '会員加入拡張機能使用可否を選択します.';
    $lang->about_admin_id_2 = 'こちらに設定した管理者IDが歓迎メッセージなどの発送者で使われます.';
    
    $lang->use_agreement = '利用約款表示';
    $lang->about_use_agreement = '利用約款を表示及び同意を受けます.';
    $lang->agreement = '利用約款';

    $lang->use_private_agreement = '個人情報取り扱い方針表示';
    $lang->about_use_private_agreement = '個人情報取り扱い方針を表示及び同意を受けます.';
    $lang->private_agreement = '個人情報取り扱い方針';
    $lang->private_gathering_agreement = '個人情報収集及び利用';

    $lang->use_jumin = '住民登録番号受ける<br/>Korea only';
    $lang->about_use_jumin = '住民登録番号を受けます.';
    $lang->jumin = '住民登録番号';
    $lang->name = '名前';
    $lang->msg_empty = '%sの値を入力してください.';
    $lang->jumin_check = '実名確認';
    $lang->about_jumin = '住民登録番号は重複加入を阻むために使われます.';
    $lang->jumin1 = '住民登録番号前部分';
    $lang->jumin2 = '住民登録番号後部分';
    $lang->insert_fail_jumin = '住民登録番号保存に失敗しました.';
    $lang->invaild_jumin = '誤った住民登録番号です.';
    $lang->jumin_exist = '入力した住民登録番号はすでに加入されています.';
    
    $lang->save_jumin = '住民登録番号を保存する<br/>Korea only';
    $lang->about_save_jumin = '入力受けた住民登録番号を保存するか可否を選択します.<br/>保存する場合 md5 hashを利用暗号化保存され住民登録番号を利用して重複加入を阻むことができます. <br/>保存しない場合住民登録番号有效性検事だけ行い重複加入を阻むことはできません.';
    $lang->msg_save_jumin = '住民登録番号は重複加入を阻むために使われて,管理者も見られないように暗号化されて保存されます.';
    $lang->msg_not_save_jumin = '住民登録番号は実名確認のために使われて保存されません.';

    $lang->use_sex_restrictions = '性別制限使用<br/>Korea only';
    $lang->about_use_sex_restrictions = '設定された性別だけ加入を受けます.';
    $lang->man = '男';
    $lang->woman = '女';
    $lang->sex_var_name = '性別拡張変数名<br/>Korea only';
    $lang->about_sex_var_name = '住民登録番号を利用して性別情報を自動で入力します. <br/>会員官吏 - 加入フォーム管理に追加された性別情報の <strong>入力項目名前</strong>を入力してください. <br/>使わない場合空にしておいてください.';
    $lang->man_value = '男性値<br/>Korea only';
    $lang->about_man_value = '男性に対して設定した値を正確に入力してください.';
    $lang->woman_value = '女性値<br/>Korea only';
    $lang->about_woman_value = '女性に対して設定した値を正確に入力してください.';
    $lang->sex_restrictions = '%s性だけ加入することができます.';
    $lang->sex_restrictions_m = '男性だけ加入することができます.';
    $lang->sex_restrictions_w = '女性だけ加入することができます.';

    $lang->age_var_name = '年齢拡張変数名<br/>Korea only';
    $lang->about_age_var_name = '住民登録番号を利用して年齢情報を自動で入力します. <br/>会員管理 - 加入フォーム管理に追加された年齢情報の <strong>入力項目名前</strong>を入力してください. <br/>使わない場合空にしておいてください.';
        
    $lang->use_age_restrictions = '年齢制限使用<br/>Korea only';
    $lang->about_use_age_restrictions = '下の設定された年齢だけ加入を受けます. (万年齢)';
    $lang->age_restrictions = '年齢制限<br/>Korea only';
    $lang->msg_age_restrictions = '年齢制限で加入することができます. (万 %s~%s)';
    $lang->msg_junior_join = '年齢制限メッセージ<br/>Korea only';

    $lang->recoid_var_name = '推薦人 ID 拡張変数名';
    $lang->about_recoid_var_name = '推薦人 IDにポイントを支給します. <br/>会員管理 - 加入フォーム管理に追加された推薦人 IDの <strong>入力項目名前</strong>を入力してください. <br/>使わない場合空にしておいてください.';
    $lang->recoid_point = '推薦人ポイント';
    $lang->about_recoid_point = '推薦された会員に支給されるポイントです.';
    $lang->joinid_point = '推薦ポイント';
    $lang->about_joinid_point = '推薦人 IDを作成した会員に支給されるポイントです.';
    $lang->point_fail = '推薦である IDを利用したポイント支給に失敗しました.推薦人 IDが存在しないこともあります.';
    
    $lang->welcome = '加入歓迎メッセージ内容';
    $lang->welcome_title = '加入歓迎メッセージ題目';
    $lang->about_welcome_title = '加入歓迎メッセージ題目を入力してください. 内容は下で別に作成してください.';
    $lang->use_welcome = '加入歓迎メッセージ';
    $lang->about_use_welcome = '加入した会員に歓迎メッセージを送ります.';
    $lang->welcome_email = '加入歓迎メール内容';
    $lang->welcome_email_title = '加入歓迎メール題目';
    $lang->about_welcome_email_title = '加入歓迎メール題目を入力してください. 内容は下で別に作成してください.';
    $lang->use_welcome_email = '加入歓迎メール';
    $lang->about_use_welcome_email = '加入した会員に歓迎メールを送ります.';
    $lang->use_notify_admin = '管理者通報機能';
    $lang->about_use_notify_admin = '利用者が会員加入や会員脱退の時関連情報を管理者に知らせます.';
    $lang->only_signin = '会員加入だけ';
    $lang->only_signout = '会員脱退だけ';
    $lang->both = '両方';
    $lang->notify_admin_period = '管理者通報週期';
    $lang->about_notify_admin_period = '管理者通報機能の通報週期を選択してください.';
    $lang->notify_each_time = '毎回通報';
    $lang->notify_collect = '集めて通報';
    $lang->notify_admin_method = '管理者通報方法';
    $lang->about_notify_admin_method = '管理者にどんな方法で知らせるか選択してください.';
    $lang->message = 'メッセージ';
    $lang->email = '電子メール';
    $lang->notify_admin_collect_number = '集めて知らせる個数';
    $lang->about_notify_admin_collect_number = 'こちらに設定した個数位通報が積もれば集めて管理者に知らせます. (基本値 10個)';
    $lang->notify_type = 'タイプ';
    $lang->notify_title = '加入と脱退通報';
    
    $lang->agree_agreement= '利用約款に同意します.'; 
    $lang->agree_private_agreement= '個人情報取り扱い方針に同意します.'; 
    $lang->agree = '同意';
    $lang->junior = '%d歳以上';
    $lang->senior = '%d歳未満';

    $lang->msg_check_agree = '約款に同意が必要です.';
    $lang->session_problem = 'セッションエラー!また試みて見てください!';
    
    $lang->invitation_config = '招待状設定';
    $lang->use_invitation = '招待状機能使用';
    $lang->about_use_invitation = '招待状機能を使えば招待状番号を持った人だけ加入することができます.';
    $lang->unit_number = '個';
    $lang->generate_invitation = '招待状生成';
    $lang->about_generate_invitation = '一度に生成することができる招待状の最大個数は 100個です. 有效期間を設定して生成された招待状は該当の期間までに有效です. 有效期間が空白の場合有效期間のない招待状が生成されます.';
    $lang->msg_invitation_incorrect_count = '1~100 間を入力してください.';
    $lang->invitation_code = '招待状番号';
    $lang->invitation_join_id = '加入ID';
    $lang->invitation_joindate = '加入日';
    $lang->view = '表示';
    $lang->my_view_all = '全て';
    $lang->view_not_use = '使用できません';
    $lang->join_extend_invitation = '招待状確認';
    $lang->msg_invitation = '招待状番号を入力してください, Ctrl+V使用可能.';
    $lang->msg_empty_invitation_code = '招待状番号を入力してください';
    $lang->msg_incorrect_invitation = '有效ではない招待状番号です';
    $lang->msg_used_invitation = 'すでに使われた招待状番号です.';
    $lang->insert_fail_invitation = '招待状処理エラー';
    $lang->deleted_member = '脱退した会員';
    $lang->validdate = '有效期間';
    $lang->msg_validdate_past = '有效期間で過去の日付は使用不可です.';
    $lang->msg_expired_invitation = '有效期間が切れた招待状です. (%s)';
    
    $lang->coupon_config = '加入クーポン設定';
    $lang->use_coupon = '加入クーポン使用';
    $lang->coupon_var_name = 'クーポン拡張変数名';
    $lang->about_coupon_var_name = '会員管理 - 加入フォーム管理に追加されたクーポン入力ボックスの <strong>入力項目名前</strong>を入力してください.';
    $lang->about_use_coupon = '加入クーポン機能を使えば加入の時クーポン番号を入力した会員はクーポンのポイントを貰えます.';
    $lang->generate_coupon = '加入クーポン生成';
    $lang->receive_point = '貰えるポイント';
    $lang->about_generate_coupon = '一度に生成することができるクーポンの最大個数は 100個です. 有效期間を設定して生成されたクーポンは該当の期間までに有效です. 有效期間が空白の場合有效期間のないクーポンが生成されます.';
    $lang->msg_invalid_number = '数字で入力してください.';
    $lang->msg_incorrect_coupon = '有效ではないクーポン番号です.';
    $lang->msg_used_coupon = 'すでに使われたクーポン番号です.';
    $lang->msg_expired_coupon = '有效期間が切れたクーポンです. (%s)';
?>

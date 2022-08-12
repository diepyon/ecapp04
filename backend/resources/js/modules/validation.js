//共通バリデーションモジュール
function email(form) {
    const reg = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}.[A-Za-z0-9]{1,}$/ //メールアドレスの形式を定義

    let n = form.email.length //emailの文字数を取得
    let message = null
    let result = null
    if (n == 0) {
         message = "入力してください。"
    } else if (reg.test(form.email) == false) { //メールアドレスの形式になっているかチェック
         message = "メールアドレスの形式で入力してください。"
    } else if (n > 256) {
         message = "255文字以内で入力してください。"
    } else {
         message = ""
    }
    if (message == "") {
        result = true
    } else {
        result = false
    } //emailの入力に問題がなければtrueを返す

    return {
        'result': result,
        'message': message
    }
}

function password(value) {
    //主にパスワード変更時に使う
    if (value == null || value.length == 0) {
        return {
            'result': false,
            'message':  "入力してください。"
        } 
    } else if (value.length > 256) {
        return {
            'result': false,
            'message': "255文字以内で入力してください。"
        }     
    } else {
        return {
            'result': true,
            'message': null
        }
    }
}

function name(form) {
    let n = form.name.length //passwordの文字数
    let message = null
    let result = null

    if (n == 0) {
        message = "入力してください。"
    } else if (n > 256) { //文字数を変数にしたい
        message = "255文字以内で入力してください。"
    } else {
        message = ""
    }

    if (message == "") {
        result = true
    } else {
        result = false
    } //nameの入力に問題がなければtrueを返す
    return {
        'result': result,
        'message': message
    }
}

export {
    name,
    email,
    password,
}

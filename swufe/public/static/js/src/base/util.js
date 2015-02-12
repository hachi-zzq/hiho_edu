var JLib = JLib || {};

(function($, win){
    var util = JLib.Util = {};

    /*
     * 返回继承原型对象p的新对象
     */
    util.create = function(p){
        //要求p是对象，但不能是null
        if (p == null){
            throw TypeErrow();
        }

        //如果有Object.create，直接使用
        if (Object.create){
            console.log('调用Object.create');
            return Object.create(p);
        }

        //进一步检测p
        var t = typeof p;
        if (t != 'object' && t != 'function'){
            throw TypeErrow();
        }

        //通过检测，返回继承p的对象
        function f(){}
        f.prototype = p;
        console.log('调用自定义create');
        return new f();
    };

    /*
     * 返回无后缀的文件名
     */
    util.getFileName = function(s){
        return s.substring(0, s.lastIndexOf('.'));
    };

    /*
     * 将数组转为JSON
     */
    util.arrayToJSON = function(array){
        var len = array.length;
        var json = {};

        for(var i = 0; i < len; i++){
            json[array[i]['name']] = array[i]['value'];
        }

        return json;
    };

    /*
     * 将JSON字符串转为JSON
     */
    util.stringToJSON = function(data){
        if (typeof data != 'object'){
            return $.parseJSON(data);
        } else {
            return data;
        }
    };

    /*
	 * 检测字数，区分中英文
	 */
	util.checkWordLen = function(s) {
        return s.replace(/[^\x00-\xFF]/g, "**").length;
    };

    /*
	 * 计算剩余字数，区分中英文
	 */
	util.calText = function(text, max) {
        var len = parseInt(util.checkWordLen(text)/2);
        return max - len;
    };

    /*
	 * 截取字符串，区分中英文
	 */
	util.subStr = function(str, length) {
        var wlen = util.checkWordLen(str);

        if (wlen > length) {
          // 所有宽字用&&代替
          var c = str.replace(/&/g, " ").replace(/[^\x00-\xFF]/g, "&&");

          // c.slice(0, length)返回截短字符串位
          str = str.slice(0, c.slice(0, length)
                    // 由位宽转为JS char宽
                    .replace(/&&/g, " ")
                    // 除去截了半个的宽位
                    .replace(/&/g, "").length
                );
        }
        return str;
    };

    //range 范围
    (function(){
        util.range = function(){
            this.range = null;
            if (document.createRange){
                console.log('1 document.createRange');
                this.range = document.createRange();
            } else if (document.body.createTextRange) {
                console.log('2 document.body.createTextRange');
                this.range = document.body.createTextRange();
            } else {
                console.log('no range');
            }
        }
        var _proto = util.range.prototype;

        _proto.init = function(){
            console.log('range');
        };
    })();


})(jQuery, window);
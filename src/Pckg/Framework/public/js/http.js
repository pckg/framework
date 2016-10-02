var data = data || {};
var http = {

    formToData: function (vueElement, keys) {
        var data = {};

        $.each(keys, function (i, key) {
            data[key] = vueElement.form[key];
        });

        return data;
    },

    submitForm: function (vueElement, fields) {
        var data = http.formToData(vueElement, fields);
        var $form = $(vueElement.$el);
        var url = $form.attr('action');

        $.post(url, data, function (data) {

        }, 'JSON');
    },

    get: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            method: 'get'
        }).done(whenDone).error(whenError);
    },

    getJSON: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'get'
        }).done(whenDone).error(whenError);
    },

    post: function (url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        if (typeof whenDone == 'undefined') {
            whenDone = http.postDone;
        }

        data = http.fixUndefined(data);

        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'post',
            data: data
        }).done(whenDone).error(whenError);
    },

    fixUndefined: function (data) {
        $.each(data, function (key, val) {
            if (Array.isArray(val) || typeof val == 'object') {
                data[key] = http.fixUndefined(val);

            } else if (typeof val == 'undefined') {
                data[key] = '';

            }
        });

        return data;
    },

    postDone: function (json) {
        if (typeof json.redirect !== 'undefined') {
            if (typeof window.parent !== 'undefined' && window.parent.location.href.indexOf(json.redirect)) {
                parent.postMessage('refresh', window.location.origin);

            } else {
                window.location.href = json.redirect;

            }
        }
    },

    redirect: function (url) {
        window.location.href = url != null
            ? url
            : window.location.href;
    }

};

var locale = {

    price: function (price, decimals) {
        if (typeof price == 'undefined') {
            return '0 €';
        }

        if (price === null) {
            price = 0.0;
        }

        return parseFloat(price).toFixed((typeof decimals == 'undefined') ? 2 : decimals) + ' €';
    },

    date: function (date) {
        date = new Date(date);

        return date.getDate() + '. ' + (date.getMonth() + 1) + '. ' + date.getFullYear();
    },

    trans: function (trans, params) {
        $.each(params, function (key, val) {
            trans = trans.replace('{{ ' + key + ' }}', val);
        });

        return trans;
    }

};

var utils = {

    fix: function (value) {
        return value ? value : null;
    },

    url: function (url, params) {
        $.each(params, function (key, val) {
            url = url.replace('[' + key + ']', val);
        });

        return url;
    },

    pushToVue: function (obj) {
        $.extend(true, $vue, obj);
    },

    nl2br: function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

};
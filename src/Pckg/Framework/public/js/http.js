var data = data || {};
var http = {

    formToData: function (vueElement, keys) {
        var data = {};

        if (typeof keys === 'undefined') {
            $.each(vueElement.form, function (key, val) {
                data[key] = vueElement.form[key];
            });

        } else {
            $.each(keys, function (i, key) {
                data[key] = vueElement.form[key];
            });

        }

        return data;
    },

    submitForm: function (vueElement, fields) {
        return http.post($(vueElement.$el.attr('action')), http.formToData(vueElement, fields));
    },

    get: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            method: 'get'
        }).done(whenDone).error(whenError);
    },

    getJSON: function (url, whenDone, whenError) {
        var request = $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'get'
        });

        if (whenDone) {
            request.done(whenDone);
        }

        if (whenError) {
            request.error(whenError);
        }

        return request;
    },

    deleteJSON: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'delete'
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

    form: function ($form, successCallback) {
        return http.post($form.attr('action'), $form.serializeArray(), successCallback);
    },

    fixUndefined: function (data) {
        $.each(data, function (key, val) {
            if (Array.isArray(val) || typeof val == 'object') {
                data[key] = http.fixUndefined(val);

            } else if (typeof val == 'undefined') {
                data[key] = '';

            } else if (val === true) {
                data[key] = 1;

            } else if (val === false || val === '' || val === null) {
                data[key] = null;

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
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = 2;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString(props.locale.replace('_', '-').toLowerCase(), {
                currency: 'eur',
                currencyDisplay: 'symbol',
                maximumFractionDigits: decimals,
                minimumFractionDigits: decimals
            }) + ' ' + Pckg.config.locale.currencySign;
    },

    roundPrice: function (price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = 2;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString(props.locale.replace('_', '-').toLowerCase(), {
                currency: 'eur',
                currencyDisplay: 'symbol',
                maximumFractionDigits: decimals,
                minimumFractionDigits: 0
            }) + ' ' + Pckg.config.locale.currencySign;
    },

    date: function (date) {
        if (!date) {
            return null;
        }

        moment.locale(props.locale);

        return moment(date).format(Pckg.config.locale.format.dateMoment);
    },

    time: function (time) {
        if (!time) {
            return null;
        }

        moment.locale(props.locale);

        return moment(time).format(Pckg.config.locale.format.timeMoment);
    },

    datetime: function (datetime) {
        return this.date(datetime) + ' ' + this.time(datetime);
    },

    trans: function (trans, params) {
        $.each(params, function (key, val) {
            trans = trans.replace('{{ ' + key + ' }}', val);
        });

        return trans;
    }

};

var utils = {

    isSameDate: function (first, second) {
        return locale.date(first) == locale.date(second);
    },

    fix: function (value) {
        return value ? value : null;
    },

    url: function (url, params) {
        $.each(params, function (key, val) {
            url = url.replace('[' + key + ']', val);
        });

        return url;
    },

    nl2br: function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },

    closeIfIframe: function () {
        this.sendToParent('popup.close');
    },

    sendToParent: function (data) {
        parent.postMessage(data, window.location.origin);
    },

    isIframe: function () {
        return parent.window != window;
    },

    collect: function (item) {
        return Array.isArray(item)
            ? item
            : [item];
    },
    prepend: function (value, array) {
        var newArray = array.slice(0);

        newArray.unshift(value);

        return newArray;
    },
    sort: function (obj) {
        var sorted = {};
        Object.keys(obj).sort().forEach(function (value, key) {
            sorted[key] = value;
        });

        return sorted;
    },
    mergeObject: function (to, from) {
        $.each(from, function (key, value) {
            to[key] = value;
        });

        return to;
    },
    pushTo: function (to, from) {
        $.each(from, function (key, value) {
            to.push(value);
        });

        return to;
    },
    firstOf: function (items, callback) {
        var first = null;

        $.each(items, function (i, item) {
            if (callback(item, i)) {
                first = item;
                return false;
            }
        });

        return first;
    },
    lastOf: function (items, callback) {
        return this.firstOf(items.reverse(), callback);
    },
    last: function (items) {
        return items[items.length - 1];
    },
    sortInt: function (a, b) {
        return a < b ? -1 : (a > b ? 1 : 0);
    },
    splice: function (collection, item) {
        return collection.splice(collection.indexOf(item), 1);
    }

};

var settings = settings || {};
settings.vue = settings.vue || {};
settings.vue.gmaps = {
    themes: {
        getOptions: function (theme) {
            var themes = settings.vue.gmaps.themes;
            var options = themes.base;

            // merge with defaults
            if (typeof themes[theme] != 'undefined') {
                options = this.mergeOptions(options, themes[theme]);
            }

            return options;
        },
        mergeOptions: function (original, overwrite) {
            $.each(overwrite, function (i, values) {
                original[i] = values;
            });

            return original;
        },
        base: {
            zoom: 10,
            center: [46.055144, 14.512284],
            //mapTypeControl: false,
            //scrollwheel: false
        }
    }
};

var d = function (data) {
    console.log(data);
};

/**
 * jQuery serializeObject
 * @copyright 2014, macek <paulmacek@gmail.com>
 * @link https://github.com/macek/jquery-serialize-object
 * @license BSD
 * @version 2.5.0
 */
!function (e, i) {
    if ("function" == typeof define && define.amd) define(["exports", "jquery"], function (e, r) {
        return i(e, r)
    }); else if ("undefined" != typeof exports) {
        var r = require("jquery");
        i(exports, r)
    } else i(e, e.jQuery || e.Zepto || e.ender || e.$)
}(this, function (e, i) {
    function r(e, r) {
        function n(e, i, r) {
            return e[i] = r, e
        }

        function a(e, i) {
            for (var r, a = e.match(t.key); void 0 !== (r = a.pop());)if (t.push.test(r)) {
                var u = s(e.replace(/\[\]$/, ""));
                i = n([], u, i)
            } else t.fixed.test(r) ? i = n([], r, i) : t.named.test(r) && (i = n({}, r, i));
            return i
        }

        function s(e) {
            return void 0 === h[e] && (h[e] = 0), h[e]++
        }

        function u(e) {
            switch (i('[name="' + e.name + '"]', r).attr("type")) {
                case"checkbox":
                    return "on" === e.value ? !0 : e.value;
                default:
                    return e.value
            }
        }

        function f(i) {
            if (!t.validate.test(i.name))return this;
            var r = a(i.name, u(i));
            return l = e.extend(!0, l, r), this
        }

        function d(i) {
            if (!e.isArray(i))throw new Error("formSerializer.addPairs expects an Array");
            for (var r = 0, t = i.length; t > r; r++)this.addPair(i[r]);
            return this
        }

        function o() {
            return l
        }

        function c() {
            return JSON.stringify(o())
        }

        var l = {}, h = {};
        this.addPair = f, this.addPairs = d, this.serialize = o, this.serializeJSON = c
    }

    var t = {
        validate: /^[a-z_][a-z0-9_]*(?:\[(?:\d*|[a-z0-9_]+)\])*$/i,
        key: /[a-z0-9_]+|(?=\[\])/gi,
        push: /^$/,
        fixed: /^\d+$/,
        named: /^[a-z0-9_]+$/i
    };
    return r.patterns = t, r.serializeObject = function () {
        return new r(i, this).addPairs(this.serializeArray()).serialize()
    }, r.serializeJSON = function () {
        return new r(i, this).addPairs(this.serializeArray()).serializeJSON()
    }, "undefined" != typeof i.fn && (i.fn.serializeObject = r.serializeObject, i.fn.serializeJSON = r.serializeJSON), e.FormSerializer = r, r
});
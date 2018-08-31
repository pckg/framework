var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var data = data || {};
var http = {

    formToData: function formToData(vueElement, keys) {
        var data = {};

        if (typeof keys === 'undefined') {
            $.each(vueElement.form, function (key, val) {
                data[key] = val;
            });
        } else {
            $.each(keys, function (i, key) {
                data[key] = vueElement.form[key];
            });
        }

        return data;
    },

    submitForm: function submitForm(vueElement, fields) {
        return http.post($(vueElement.$el.attr('action')), http.formToData(vueElement, fields));
    },

    get: function get(url, whenDone, whenError) {
        return $.ajax({
            url: url,
            method: 'get'
        }).done(whenDone).error(whenError);
    },

    getJSON: function getJSON(url, whenDone, whenError) {
        var request = $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'get',
            type: 'get'
        });

        if (whenDone) {
            request.done(whenDone);
        }

        if (whenError) {
            request.error(whenError);
        }

        return request;
    },

    deleteJSON: function deleteJSON(url, whenDone, whenError) {
        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'delete',
            type: 'delete'
        }).done(whenDone).error(whenError);
    },

    post: function post(url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        if (typeof whenDone == 'undefined') {
            whenDone = http.postDone;
        }

        if (typeof whenError == 'undefined') {
            whenError = http.postError;
        }

        data = http.fixUndefined(data);

        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'post',
            type: 'post',
            data: data
        }).done(whenDone).error(whenError);
    },

    patch: function post(url, data, whenDone, whenError) {
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
            method: 'patch',
            type: 'patch',
            data: data
        }).done(whenDone).error(whenError);
    },

    form: function form($form, successCallback) {
        return http.post($form.attr('action'), $form.serializeArray(), successCallback);
    },

    fixUndefined: function fixUndefined(data) {
        $.each(data, function (key, val) {
            if (Array.isArray(val) || (typeof val === 'undefined' ? 'undefined' : _typeof(val)) == 'object') {
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

    postDone: function postDone(json) {
        if (typeof json.redirect !== 'undefined') {
            if (typeof window.parent !== 'undefined' && window.parent.location.href.indexOf(json.redirect)) {
                parent.postMessage('refresh', window.location.origin);
            } else {
                window.location.href = json.redirect;
            }
        }
    },

    postError: function postError(response) {
        console.log('postError', response.responseJSON);
    },

    redirect: function redirect(url) {
        if (!url || typeof url == 'undefined') {
            url = window.location.href;
        }

        window.location.href = url;
    }

};

var locale = {

    price: function price(_price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof _price == 'undefined' || _price === null) {
            _price = 0.0;
        }

        return parseFloat(_price).toLocaleString(Pckg.config.locale.current.replace('_', '-').toLowerCase(), {
            currency: 'eur',
            currencyDisplay: 'symbol',
            maximumFractionDigits: decimals,
            minimumFractionDigits: decimals
        }) + ' ' + Pckg.config.locale.currencySign;
    },

    roundPrice: function roundPrice(price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString(Pckg.config.locale.current.replace('_', '-').toLowerCase(), {
            currency: 'eur',
            currencyDisplay: 'symbol',
            maximumFractionDigits: decimals,
            minimumFractionDigits: 0
        }) + ' ' + Pckg.config.locale.currencySign;
    },

    date: function date(_date) {
        if (!_date) {
            return null;
        }

        moment.locale(Pckg.config.locale.current);

        return moment(_date).format(Pckg.config.locale.format.dateMoment);
    },

    time: function time(_time) {
        if (!_time) {
            return null;
        }

        moment.locale(Pckg.config.locale.current);

        return moment(_time).format(Pckg.config.locale.format.timeMoment);
    },

    datetime: function datetime(_datetime) {
        return this.date(_datetime) + ' ' + this.time(_datetime);
    },

    trans: function trans(_trans, params) {
        $.each(params, function (key, val) {
            _trans = _trans.replace('{{ ' + key + ' }}', val);
        });

        return _trans;
    }

};

var utils = {

    isSameDate: function isSameDate(first, second) {
        return locale.date(first) == locale.date(second);
    },

    fix: function fix(value) {
        return value ? value : null;
    },

    url: function url(_url, params) {
        if (!_url) {
            return;
        }
        if (_url.indexOf('@') === 0) {
            _url = Pckg.router.urls[_url.substring(1)] || null;
        }
        if (!_url) {
            return;
        }
        $.each(params, function (key, val) {
            _url = _url.replace('[' + key + ']', val);
        });

        return _url;
    },

    sluggify: function(str){
        return str.replace(/[^a-zA-Z0-9 -]/g, '')
            .replace(/[ -]+/g, '-')
            .replace(/^-|-$/g, '')
            .toLowerCase();
    },

    nl2br: function nl2br(str, is_xhtml) {
        var breakTag = is_xhtml || typeof is_xhtml === 'undefined' ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },

    closeIfIframe: function closeIfIframe() {
        this.sendToParent('popup.close');
    },

    closeAndRefresh: function closeAndRefresh() {
        $.magnificPopup.close();
        http.redirect();
    },

    sendToParent: function sendToParent(data) {
        parent.postMessage(data, window.location.origin);
    },

    isIframe: function isIframe() {
        return parent.window != window;
    },

    collect: function collect(item) {
        return Array.isArray(item) ? item : [item];
    },
    prepend: function prepend(value, array) {
        var newArray = array.slice(0);

        newArray.unshift(value);

        return newArray;
    },
    sort: function sort(obj) {
        var sorted = {};
        Object.keys(obj).sort().forEach(function (value, key) {
            sorted[key] = value;
        });

        return sorted;
    },
    mergeObject: function mergeObject(to, from) {
        $.each(from, function (key, value) {
            to[key] = value;
        });

        return to;
    },
    pushTo: function pushTo(to, from) {
        $.each(from, function (key, value) {
            to.push(value);
        });

        return to;
    },
    firstOf: function firstOf(items, callback) {
        var first = null;

        $.each(items, function (i, item) {
            if (callback(item, i)) {
                first = item;
                return false;
            }
        });

        return first;
    },
    lastOf: function lastOf(items, callback) {
        return this.firstOf(items.reverse(), callback);
    },
    last: function last(items) {
        return items[items.length - 1];
    },
    sortInt: function sortInt(a, b) {
        return a < b ? -1 : a > b ? 1 : 0;
    },
    splice: function splice(collection, item) {
        return collection.splice(collection.indexOf(item), 1);
    },
    groupBy: function (collection, groupBy) {
        grouped = {};
        $.each(collection, function (key, val) {
            grouped[groupBy(val)] ? grouped[groupBy(val)].push(val) : (grouped[groupBy(val)] = [val]);
        });

        return grouped;
    },

    lazyTemplate: function(resolve, obj, url) {
        http.getJSON(url, function(data) {
            obj.template = data.template;
            resolve(obj);
        });
    }

};

var settings = settings || {};
settings.vue = settings.vue || {};
settings.vue.gmaps = {
    themes: {
        getOptions: function getOptions(theme) {
            var themes = settings.vue.gmaps.themes;
            var options = themes.base;

            // merge with defaults
            if (typeof themes[theme] != 'undefined') {
                options = this.mergeOptions(options, themes[theme]);
            }

            return options;
        },
        mergeOptions: function mergeOptions(original, overwrite) {
            $.each(overwrite, function (i, values) {
                original[i] = values;
            });

            return original;
        },
        base: {
            zoom: 10,
            center: [46.055144, 14.512284]
        }
    }
};

var d = function d(data) {
    console.log(data);
};

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) {
    return typeof obj;
} : function (obj) {
    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
};

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

    ajax: function ajax(options, done, error) {
        let presetBeforeSend = options.beforeSend || null;
        options.beforeSend = function (request) {
            if (presetBeforeSend) {
                presetBeforeSend(request);
            }
            http.addLocale(request);
            http.addCsrf(request);
        };

        options = Object.assign({
            dataType: 'JSON'
        }, options || {});

        let a = $.ajax(options);

        let preError = error || null;
        error = function (response) {
            /**
             * Check for CSRF?
             */
            if ($dispatcher && response.responseJSON && ['Cross Site Request Forbidden', 'Cross Site Request Invalid'].indexOf(response.responseJSON.message || '') >= 0) {
                $dispatcher.$emit('notification:error', 'Your session has expired, please refresh your browser');
            }
            
            if (preError) {
                preError(response);
            }
        };

        if (a.done) {
            if (done) {
                a.done(done);
            }
            if (error) {
                a.fail(error);
            }
        } else {
            a.then(done || function(data){ console.log('no success handler', data); }, error || function(data){ console.log('no error handler', data); });
        }

        return a;
    },

    get: function (url, whenDone, whenError, options) {
        return this.ajax(Object.assign({
            url: url,
            type: 'GET'
        }, options || {}), whenDone, whenError);
    },

    post: function post(url, data, whenDone, whenError) {
        if (typeof data === 'function') {
            data = data();
        }

        data = http.fixUndefined(data);

        return this.ajax({
            url: url,
            type: 'POST',
            data: data
        }, whenDone, whenError);
    },

    patch: function post(url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        data = http.fixUndefined(data);

        return this.ajax({
            url: url,
            type: 'PATCH',
            data: data
        }, whenDone, whenError);
    },

    put: function post(url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        data = http.fixUndefined(data);

        return this.ajax({
            url: url,
            type: 'PATCH',
            data: data
        }, whenDone, whenError);
    },

    search: function get(url, whenDone, whenError, options) {
        this.ajax(Object.assign({
            url: url,
            type: 'SEARCH'
        }, options || {}), whenDone, whenError);
    },

    delete: function (url, whenDone, whenError) {
        return this.deleteJSON(url, whenDone, whenError);
    },

    deleteJSON: function deleteJSON(url, whenDone, whenError) {
        return this.ajax({
            url: url,
            type: 'DELETE'
        }, whenDone, whenError);
    },

    getJSON: function getJSON(url, whenDone, whenError, options) {
        return this.get(url, whenDone, whenError, options);
    },

    addCsrf: function(request) {
        var elements = document.getElementsByName('pckgvdth');
        if (elements.length === 0) {
            return;
        }
        request.setRequestHeader("X-Pckg-CSRF", elements[0].getAttribute('content'));
    },

    addLocale: function(request) {
        if (!Pckg || !Pckg.config || !Pckg.config.locale || !Pckg.config.locale.current) {
            return;
        }

        request.setRequestHeader("X-Pckg-Locale", Pckg.config.locale.current);
    },

    form: function form($form, successCallback) {
        return http.post($form.attr('action'), $form.serializeArray(), successCallback);
    },

    fixUndefined: function fixUndefined(data) {
        if (typeof data === 'string' || typeof data === 'number') {
            return data;
        } else if (typeof data === 'undefined') {
            return null;
        }

        $.each(data, function (key, val) {
            if (Array.isArray(val) || (typeof val === 'undefined' ? 'undefined' : _typeof(val)) == 'object') {
                data[key] = http.fixUndefined(val);
            } else if (typeof val == 'undefined') {
                data[key] = '';
            } else if (val === true) {
                data[key] = 1;
            } else if (val === false || val === '' || val === null) {
                data[key] = null;
            } else {
                data[key] = http.fixUndefined(val);
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
    },

    redirect: function redirect(url) {
        if (!url || typeof url == 'undefined') {
            url = window.location.href;
        }

        window.location.href = url;
    }

};

var locale = {

    price: function price(_price, decimals, currency) {
        return this.number(_price, decimals) + ' ' + (currency || Pckg.config.locale.currencySign);
    },

    number: function price(_price, decimals, locale) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof _price == 'undefined' || _price === null) {
            _price = 0.0;
        }

        return parseFloat(_price).toLocaleString((locale || Pckg.config.locale.current).replace('_', '-').toLowerCase(), {
            currency: 'eur',
            currencyDisplay: 'symbol',
            maximumFractionDigits: decimals,
            minimumFractionDigits: decimals
        });
    },

    roundPrice: function roundPrice(price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        let digits = parseInt(price) == parseFloat(price) ? 0 : (decimals || 2);

        return parseFloat(price).toLocaleString(Pckg.config.locale.current.replace('_', '-').toLowerCase(), {
            currency: 'eur',
            currencyDisplay: 'symbol',
            maximumFractionDigits: digits,
            minimumFractionDigits: digits
        }) + ' ' + Pckg.config.locale.currencySign;
    },

    roundNumber: function(number, decimals){
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof number == 'undefined' || number === null) {
            number = 0.0;
        }

        return parseInt(number) == parseFloat(number) ? parseInt(number) : parseFloat(number).toFixed(decimals || 2)
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

var collection = {

    collect: function(items, of) {
        return items.map((item) => { return new of(item) });
    },

    groupBy: function (collection, groupBy) {
        let groups = {};

        $.each(collection, function (i, item) {
            let group = groupBy(item, i);
            if (!groups[group]) {
                groups[group] = [];
            }
            groups[group].push(item);
        });

        return groups;
    },

    map: function (items, map) {
        let mapped = {};

        $.each(items, function (i, item) {
            mapped[i] = collection.getCallableKey(map, item, i);
        });

        return mapped;
    },

    keyBy: function (items, key) {
        let keyed = {};

        $.each(items, function (i, item) {
            keyed[collection.getCallableKey(key, item, i)] = item;
        });

        return keyed;
    },

    getCallableKey: function(key, item, i) {
        if (typeof key === 'string') {
            return item[key];
        }

        return key(item, i);
    },

    shuffle: function (unshuffled) {
        return unshuffled
            .map(function (a) {
                return {sort: Math.random(), value: a};
            })
            .sort(function (a, b) {
                return a.sort - b.sort;
            })
            .map(function (a) {
                return a.value;
            });
    }

};

var utils = {

    is: function (val) {
        if (typeof val === 'number') {
            return val.toString().length > 0;
        } else if (typeof val === 'boolean') {
            return val;
        } else if (typeof val === 'string') {
            return val.length > 0;
        }

        return !!val;
    },

    lcfirst: function (str) {
        if (!str) {
            return '';
        }

        return str.charAt(0).toLowerCase() + str.slice(1);
    },

    ucfirst: function (str) {
        if (!str) {
            return '';
        }

        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    toCamelCase: function(str) {
        return str.replace(/^([A-Z])|\s(\w)/g, function(match, p1, p2, offset) {
            if (p2) return p2.toUpperCase();
            return p1.toLowerCase();
        });
    },

    isSameDate: function isSameDate(first, second) {
        return locale.date(first) == locale.date(second);
    },

    fix: function fix(value) {
        return value ? value : null;
    },

    url: function url(_url, params, absolute) {
        if (!_url) {
            return;
        }
        if (_url.indexOf('@') === 0 && Pckg.router.urls[_url.substring(1)]) {
            _url = Pckg.router.urls[_url.substring(1)];
        }
        if (_url.indexOf('@') === 0 && Pckg.router.urls[_url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)]) {
            _url = Pckg.router.urls[_url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)];
        }
        if (!_url) {
            return;
        }
        $.each(params, function (key, val) {
            _url = _url.replace('[' + key + ']', val);
        });

        if (_url.indexOf('@/') === 0) {
            _url = _url.substring(1);
        }

        if (absolute) {
            _url = (Pckg.site.url || '') + _url;
        }

        return _url;
    },

    sluggify: function (str) {
        return str.replace(/[^a-zA-Z0-9 -]/g, '')
            .replace(/[ -]+/g, '-')
            .replace(/^-|-$/g, '')
            .toLowerCase();
    },

    nl2br: function nl2br(str, is_xhtml) {
        var breakTag = is_xhtml || typeof is_xhtml === 'undefined' ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },

    html2text: function (html) {
        let span = document.createElement('span');
        span.innerHTML = html;
        return span.textContent || span.innerText;
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
    next: function (all, current, noLoop) {
        let i = all.indexOf(current) + 1;
        if (i < 0) {
            i = 0;
        } else if (i >= all.length) {
            if (noLoop) {
                i = all.length - 1;
            } else {
                i = 0;
            }
        }
        return all[i];
    },
    prev: function (all, current, noLoop) {
        let i = all.indexOf(current) - 1;
        if (i < 0) {
            if (noLoop) {
                i = 0;
            } else {
                i = all.length - 1;
            }
        } else if (i >= all.length) {
            i = all.length - 1;
        }
        return all[i];
    },
    sortInt: function sortInt(a, b) {
        return a < b ? -1 : a > b ? 1 : 0;
    },
    splice: function splice(collection, item) {
        let index = collection.indexOf(item);
        if (index === -1) {
            return collection;
        }
        return collection.splice(index, 1);
    },
    toggle: function(items, key){
        if (items.indexOf(key) >= 0) {
            utils.splice(items, key);
        } else {
            items.push(key);
        }
    },
    groupBy: function (collection, groupBy) {
        grouped = {};
        $.each(collection, function (key, val) {
            grouped[groupBy(val)] ? grouped[groupBy(val)].push(val) : (grouped[groupBy(val)] = [val]);
        });

        return grouped;
    },

    lazyTemplate: function (resolve, obj, url) {
        if (typeof url !== 'string') {
            url = url(obj);
        }
        http.getJSON(url, function (data) {
            obj.template = data.template;
            if (data.form) {
                let originalData = obj.data;
                obj.data = function () {
                    let d = originalData ? originalData.call(this) : {};
                    d.form = data.form;
                    return d;
                };
            }
            resolve(obj);
        });
    },

    base64decode: function(str) {
        return atob(str);
    },

    base64encode: function(str) {
        return btoa(str);
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

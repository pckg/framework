var data = data || {};
var http = {

    formToData: function (vueElement, keys) {
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

    submitForm: function (vueElement, fields) {
        return http.post($(vueElement.$el.attr('action')), http.formToData(vueElement, fields));
    },

    search: function get(url, whenDone, whenError, options) {
        let finalOptions = Object.assign({
            url: url,
            type: 'SEARCH'
        }, options || {});

        return $.ajax(finalOptions).done(whenDone).error(whenError);
    },

    get: function (url, whenDone, whenError, options) {
        if (options) {
            return this.getJSON(url, whenDone, whenError, options);
        }

        let options = {
            url: url,
            type: 'GET',
            beforeSend: function(request) {
                request.setRequestHeader("X-Pckg-Locale", Pckg.config.locale.current);
            },
        };

        return $.ajax(options).done(whenDone).error(whenError);
    },

    getJSON: function (url, whenDone, whenError, options) {
        options = options || {};
        options.beforeSend = function (request) {
            request.setRequestHeader("X-Pckg-Locale", Pckg.config.locale.current);
        };

        var request = $.ajax(Object.assign({
            url: url,
            dataType: 'JSON',
            type: 'GET'
        }, options));

        if (whenDone) {
            request.done(whenDone);
        }

        if (whenError) {
            request.fail(whenError);
        }

        return request;
    },

    deleteJSON: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            dataType: 'JSON',
            type: 'DELETE'
        }).done(whenDone).error(whenError);
    },

    post: function (url, data, whenDone, whenError) {
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
        let options = {
            url: url,
            dataType: 'JSON',
            type: 'POST',
            data: data,
            beforeSend: function(request) {
                request.setRequestHeader("X-Pckg-Locale", Pckg.config.locale.current);
                var elements = document.getElementsByName('pckgvdth');
                if (elements.length === 0) {
                    return;
                }
                request.setRequestHeader("X-Pckg-CSRF", elements[0].getAttribute('content'));
            },
        };

        return $.ajax(options).done(whenDone).error(whenError);
    },

    patch: function (url, data, whenDone, whenError) {
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
            type: 'PATCH',
            data: data
        }).done(whenDone).error(whenError);
    },

    form: function ($form, successCallback) {
        return http.post($form.attr('action'), $form.serializeArray(), successCallback);
    },

    fixUndefined: function (data) {
        if (typeof data === 'string' || typeof data === 'number') {
            return data;
        }

        $.each(data, function (key, val) {
            if (Array.isArray(val) || typeof val == 'object') {
                data[key] = http.fixUndefined(val);

            } else if (typeof val == 'undefined') {
                data[key] = '';

            } else if (val === true) {
                data[key] = 1;

            } else if (val === false || val === '' || val === null) {
                data[key] = null;

            } else {
                data[key] = val;
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

    postError: function postError(response) {
        console.log('postError', response.responseJSON);
    },

    redirect: function (url) {
        if (!url || typeof url == 'undefined') {
            url = window.location.href;
        }

        window.location.href = url;
    }

};

var locale = {

    price: function (price, decimals, currency) {
        return this.number(price, decimals) + ' ' + (currency || Pckg.config.locale.currencySign);
    },

    number: function (price, decimals, locale) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString((locale || Pckg.config.locale.current).replace('_', '-').toLowerCase(), {
            currency: 'eur',
            currencyDisplay: 'symbol',
            maximumFractionDigits: decimals,
            minimumFractionDigits: decimals
        });
    },

    roundPrice: function (price, decimals) {
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

    date: function (date) {
        if (!date) {
            return null;
        }

        moment.locale(Pckg.config.locale.current);

        return moment(date).format(Pckg.config.locale.format.dateMoment);
    },

    time: function (time) {
        if (!time) {
            return null;
        }

        moment.locale(Pckg.config.locale.current);

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

var collection = {

    collect: function(items, of) {
        return items.map((item) => { return new of(item) });
    },

    groupBy: function(collection, groupBy) {
        let groups = {};

        $.each(collection, function(i, item){
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
        console.log('typeof', typeof val, val);
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

    ucfirst: function(str) {
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

    isSameDate: function (first, second) {
        return locale.date(first) == locale.date(second);
    },

    fix: function (value) {
        return value ? value : null;
    },

    url: function (url, params, absolute) {
        if (!url) {
            return;
        }
        if (url.indexOf('@') === 0 && Pckg.router.urls[url.substring(1)]) {
            url = Pckg.router.urls[_url.substring(1)];
        }
        if (url.indexOf('@') === 0 && Pckg.router.urls[url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)]) {
            url = Pckg.router.urls[_url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)];
        }
        if (!url) {
            return;
        }
        $.each(params, function (key, val) {
            url = url.replace('[' + key + ']', val);
        });

        if (absolute) {
            url = (Pckg.site.url || '') + url;
        }

        return url;
    },

    sluggify: function (str) {
        return str.replace(/[^a-zA-Z0-9 -]/g, '')
            .replace(/[ -]+/g, '-')
            .replace(/^-|-$/g, '')
            .toLowerCase();
    },

    nl2br: function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },

    html2text: function (html) {
        let span = document.createElement('span');
        span.innerHTML = html;
        return span.textContent || span.innerText;
    },

    closeIfIframe: function () {
        this.sendToParent('popup.close');
    },

    closeAndRefresh: function () {
        $.magnificPopup.close();
        http.redirect();
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
    sortInt: function (a, b) {
        return a < b ? -1 : (a > b ? 1 : 0);
    },
    splice: function (collection, item) {
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

    lazyTemplate: function(resolve, obj, url) {
        if (typeof url !== 'string') {
            url = url(obj);
        }
        http.getJSON(url, function(data) {
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
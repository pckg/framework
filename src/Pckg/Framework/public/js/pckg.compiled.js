var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var Pckg = Pckg || {};
Pckg = Object.assign(Pckg, {
    Collection: function (_Array) {
        _inherits(Collection, _Array);

        function Collection() {
            _classCallCheck(this, Collection);

            return _possibleConstructorReturn(this, (Collection.__proto__ || Object.getPrototypeOf(Collection)).apply(this, arguments));
        }

        _createClass(Collection, null, [{
            key: 'collect',
            value: function collect(items, type) {
                var collection = new Pckg.Collection();

                $.each(items, function (i, item) {
                    collection.push(new type(item));
                }.bind(this));

                return collection;
            }
        }]);

        return Collection;
    }(Array),
    Database: {
        Record: function () {
            function Record(data) {
                _classCallCheck(this, Record);

                /**
                 * Set default relations.
                 */
                var relations = this.getEntity().getRelations();
                $.each(relations, function (key, setting) {
                    if ((typeof setting === 'undefined' ? 'undefined' : _typeof(setting)) == 'object') {
                        /**
                         * Relations type is defined by of (class) and/or type (Array/Object)
                         */
                        if (setting.type == Array) {
                            /**
                             * Array relation
                             */
                            data[key] = Pckg.Collection.collect(data[key] || [], setting.of);
                        } else {
                            /**
                             * Object relation
                             */
                            data[key] = new setting.of(data[key] || {});
                        }
                    } else {
                        data[key] = data[key] || new value();
                    }
                });

                /**
                 * Set default fields.
                 */
                $.each(this.getEntity().getFields(), function (key, value) {
                    data[key] = data[key] || new value();
                });

                /**
                 * Set default fields.
                 */
                $.each(this.getEntity().getCollections(), function (key, value) {
                    data[key] = value;
                });

                /**
                 * Bind data to object, set getters and setters for vue.
                 */
                $.each(data, function (key, val) {
                    this[key] = val;
                }.bind(this));
            }

            _createClass(Record, [{
                key: '$set',
                value: function $set(key, val) {
                    $vue.$set(this, key, val);
                }
            }, {
                key: 'getData',
                value: function getData() {
                    var _this2 = this;

                    var fields = this.getEntity().getFields();
                    var data = {};

                    $.each(fields, function (field, val) {
                        return data[field] = _this2[field] || val;
                    });

                    return data;
                }
            }, {
                key: 'insert',
                value: function insert(callback) {
                    var data = this.getData();

                    if (typeof callback == 'undefined') {
                        callback = function callback(data) {
                        };
                    }

                    http.post(this.getUrl('insert'), data, callback);
                }
            }, {
                key: 'getEntity',
                value: function getEntity() {
                    return new Pckg.Database.Entity();
                }
            }]);

            return Record;
        }(),

        Entity: function () {
            function Entity() {
                _classCallCheck(this, Entity);
            }

            _createClass(Entity, [{
                key: 'getFields',
                value: function getFields() {
                    return {};
                }
            }, {
                key: 'getRelations',
                value: function getRelations() {
                    return {};
                }
            }, {
                key: 'getUrl',
                value: function getUrl(type, data) {
                    if (type == 'insert') {
                        return utils.url();
                    }
                }
            }, {
                key: 'getCollections',
                value: function getUrl() {
                    return {};
                }
            }]);

            return Entity;
        }()
    }
});

var Pckg = {
    Collection: class extends Array {

        static collect(items, type) {
            var collection = new Pckg.Collection();

            $.each(items, function (i, item) {
                collection.push(new type(item));
            }.bind(this));

            return collection;
        }
    },
    Database: {
        Record: class {
            constructor(data) {
                /**
                 * Set default relations.
                 */
                var relations = this.getEntity().getRelations();
                $.each(relations, function (key, setting) {
                    if (typeof setting == 'object') {
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
                        data[key] = data[key] || new value;
                    }
                });

                /**
                 * Set default fields.
                 */
                $.each(this.getEntity().getFields(), function (key, value) {
                    data[key] = data[key] || new value;
                });

                /**
                 * Bind data to object, set getters and setters for vue.
                 */
                $.each(data, function (key, val) {
                    this[key] = val;
                }.bind(this));
            }

            $set(key, val) {
                console.log('setting in vue');
                $vue.$set(this, key, val);
            }

            get(key) {
                console.log("__get " + key);
            }

            set(key) {
                console.log("__set" + key);
            }

            getData() {
                var fields = this.getEntity().getFields();
                var data = {};

                $.each(fields, (field, val) => {
                    return data[field] = this[field] || val;
                });

                return data;
            }

            insert(callback) {
                var data = this.getData();

                if (typeof callback == 'undefined') {
                    callback = function (data) {
                        console.log('auto callback', data);
                    }
                }

                http.post(this.getUrl('insert'), data, callback);
            }

            getEntity() {
                return new Pckg.Database.Entity();
            }
        },

        Entity: class {

            getFields() {
                return {};
            }

            getRelations() {
                return {};
            }

            getUrl(type, data) {
                if (type == 'insert') {
                    return utils.url()
                }

                console.log('unknown url');
            }

        }
    }
};
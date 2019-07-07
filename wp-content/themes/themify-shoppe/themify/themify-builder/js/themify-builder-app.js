window.themifybuilderapp = window.themifybuilderapp || {};
(function ($) {

    'use strict';

	// Check if drag event is not disabled
	if( typeof window.top.document.ondragstart === 'function' ) {
		window.top.document.ondragstart = null;
		window.document.ondragstart = null;
	}


    // extend jquery-ui sortable with beforeStart event
    var oldMouseStart = $.ui.sortable.prototype._mouseStart,
        is_fullSection = document.body.classList.contains('full-section-scrolling');
    $.ui.sortable.prototype._mouseStart = function (e, overrideHandle, noActivation) {
        if(e.type==='mousedown'){
            this._trigger('beforeStart', e,[this, this._uiHash()]);
        }
        oldMouseStart.apply(this, [e, overrideHandle, noActivation]);
    };

    // Serialize Object Function
    if (undefined === $.fn.themifySerializeObject) {
        $.fn.themifySerializeObject = function () {
            var o = {};
            for (var i = 0, len = this.length; i < len; ++i) {
                var type = this[i].type;
                if (this[i].classList.contains('wp-editor-area') && tinyMCE !== undefined) {
                    var tiny = tinyMCE.get(this[i].id);
                    if (tiny) {
                        this[i].value = tiny.getContent();
                    }
                }
                if (this[i].value !== '' && this[i].name && (type === 'text' || type === 'number' || type === 'radio' || type === 'checkbox' || type === 'textarea' || type === 'select-one' || type === 'hidden' || type === 'email' || type === 'select' || type === 'select-multiple' )) {
                    var name = this[i].name,
                            val = this[i].value;
                    //jQuery returns all selected values for select elements with multi option on
                    if( type === 'select-multiple' ) val = jQuery( this[i] ).val();

                    if (type === 'radio' || type === 'checkbox') {
                        val = this[i].checked && val;
                    }

                    if (o[name] !== undefined && type !== 'radio') {
                        !o[name].push && (o[name] = [o[name]]);
                        val && o[name].push(val);
                    } else {
                        val && (o[name] = val);
                    }
                }
            }
            return o;
        };
    }
    function getFormTemplates() {
        var key = 'tb_form_templates';
        function getData() {
            if (themifyBuilder.debug) {
                return false;
            }
            try {
                var record = localStorage.getItem(key),
                        m = '';
                if (!record) {
                    return false;
                }
                record = JSON.parse(record);
                for (var s in themifyBuilder.modules) {
                    m += s;
                }
                if (record.ver.toString() !== tbLocalScript.version.toString() || record.h !== Themify.hash(m)) {
                    return false;
                }
                return record.val;
            }
            catch (e) {
                return false;
            }
            return false;
        }
        function setData(value) {
            try {
                var m = '';
                for (var s in themifyBuilder.modules) {
                    m += s;
                }
                var record = {val: value, ver: tbLocalScript.version, h: Themify.hash(m)};
                localStorage.setItem(key, JSON.stringify(record));
                return true;
            }
            catch (e) {
                return false;
            }
        }

        function insert(data) {
            var insert = '';
            if(typeof data==='string'){
                insert = data;
            }
            else{//old version data, can be removed in the versions 13.02.2018
                for (var i in data) {
                    insert += data[i];
                }
            }
            document.body.insertAdjacentHTML('beforeend', insert);
            insert = data = null;
        }
        var data = getData();
        if (data) {//cache visual templates)
            insert(data);
            return;
        }
        $.ajax({
            type: 'POST',
            url: themifyBuilder.ajaxurl,
            data: {
                action: 'tb_load_form_templates',
                tb_load_nonce: themifyBuilder.tb_load_nonce
            },
            success: function (resp) {
                if (resp) {
                    insert(resp);	
                    setData(resp.replace(/\s\s+/g, ' '));
                }
            }
        });
    };

    var api = themifybuilderapp = {
        activeModel: null,
        Models: {},
        Collections: {},
        Mixins: {},
        Views: {Modules: {}, Rows: {}, SubRows: {}, Columns: {}, Controls: {}},
        Forms: {},
        Utils: {},
        Instances: {Builder: {}},
        cache: {repeaterElements: {}}
    },
    tempSettings = [];
    api.mode = 'default';
    api.autoSaveCid = null;
    api.hasChanged = null;
    api.editing = false;
    api.init = false;
    api.scrollTo = false;
    api.eventName = false;
    api.beforeEvent = false;
    api.saving = false;
    api.activeBreakPoint = 'desktop';
    api.zoomMeta = {isActive: false, size: 100};
    api.isPreview = false;
    api.Models.Module = Backbone.Model.extend({
        defaults: {
            element_id : null,
            elType: 'module',
            mod_name: '',
            mod_settings: {}
        },
        initialize: function () {
            api.Models.Registry.register(this.cid, this);

            if ( ! this.get('element_id') ) {
                this.set({element_id: api.Utils.generateUniqueID() }, {silent: true} );
            }
        },
        toRenderData: function () {
            return {
                slug: this.get('mod_name'),
                name: themifyBuilder.modules[this.get('mod_name')].name,
                excerpt: this.getExcerpt()
            };
        },
        getExcerpt: function (settings) {
            var setting = settings || this.get('mod_settings'),
                    excerpt = setting.content_text || setting.content_box || setting.plain_text || '';
            return this.limitString(excerpt, 100);
        },
        limitString: function (str, limit) {
            var new_str = '';
            if (str !== '') {
                str = this.stripHtml(str).toString(); // strip html tags
                new_str = str.length > limit ? str.substr(0, limit) : str;
            }
            return new_str;
        },
        stripHtml: function (html) {
            var tmp = document.createElement('div');
            tmp.innerHTML = html;
            return tmp.textContent || tmp.innerText || '';
        },
        setData: function (data) {
            var model = api.Views.init_module(data);
            model.model.trigger('custom:change', model);
        },
        backendLivePreview: function () {
            $('.tb_element_cid_' + this.cid).find('.module_excerpt').text(this.getExcerpt());
        },
        // for instant live preview
        getPreviewSettings: function () {
            return _.extend({cid: this.cid}, themifyBuilder.modules[ this.get('mod_name') ].defaults, tempSettings);
        },
        getIDattr: function() {
            return this.get('element_id') ? this.get('element_id') : api.Utils.generateUniqueID();
        }
    });

    api.Models.SubRow = Backbone.Model.extend({
        defaults: {
            element_id: null,
            elType: 'subrow',
            row_order: 0,
            gutter: 'gutter-default',
            column_alignment: 'col_align_top',
            background_video: '',
            mutevideo: '',
            unloopvideo: '',
            desktop_dir: 'ltr',
            tablet_dir: 'ltr',
            tablet_landscape_dir: 'ltr',
            mobile_dir: 'ltr',
            col_mobile: '-auto',
            col_tablet_landscape: '-auto',
            col_tablet: '-auto',
            cols: {},
            styling: {}
        },
        initialize: function () {
            api.Models.Registry.register(this.cid, this);

            if ( ! this.get('element_id') ) {
                this.set({element_id: api.Utils.generateUniqueID() }, {silent: true} );
            }
        },
        setData: function (data) {
            var model = api.Views.init_subrow(data);
            model.model.trigger('custom:change', model);
        }
    });

    api.Models.Column = Backbone.Model.extend({
        defaults: {
            element_id: null,
            elType: 'column',
            column_order: '',
            grid_class: '',
            component_name: 'column',
            background_video: '',
            mutevideo: '',
            unloopvideo: '',
            modules: {},
            styling: {}
        },
        initialize: function () {
            api.Models.Registry.register(this.cid, this);

            if ( ! this.get('element_id') ) {
                this.set({element_id: api.Utils.generateUniqueID() }, {silent: true} );
            }
        },
        setData: function (data) {
            var model = api.Views.init_column(data);
            model.model.trigger('custom:change', model);
        }
    });

    api.Models.Row = Backbone.Model.extend({
        defaults: {
            element_id: null,
            elType: 'row',
            row_order: 0,
            gutter: 'gutter-default',
            column_alignment: is_fullSection ? 'col_align_middle' : 'col_align_top',
            desktop_dir: 'ltr',
            tablet_dir: 'ltr',
            tablet_landscape_dir: 'ltr',
            mobile_dir: 'ltr',
            col_mobile: '-auto',
            col_tablet_landscape: '-auto',
            col_tablet: '-auto',
            background_video: '',
            mutevideo: '',
            unloopvideo: '',
            cols: {},
            styling: {}
        },
        initialize: function () {
            api.Models.Registry.register(this.cid, this);

            if ( ! this.get('element_id') ) {
                this.set({element_id: api.Utils.generateUniqueID() }, {silent: true} );
            }
        },
        setData: function (data) {
            var model = api.Views.init_row(data);
            model.model.trigger('custom:change', model);
        }
    });

    api.Collections.Rows = Backbone.Collection.extend({
        model: api.Models.Row
    });

    api.Models.Registry = {
        items: {},
        register: function (id, object) {
            this.items[id] = object;
        },
        lookup: function (id) {
            return this.items[id] || null;
        },
        remove: function (id) {
            this.items[id] = null;
            delete this.items[id];
        },
        destroy: function () {
            _.each(this.items, function (model, cid) {
                model.destroy();
            });
            this.items = {};
        }
    };

    api.Models.setValue = function (cid, data, silent) {
        silent = silent || false;
        var model = api.Models.Registry.lookup(cid);
        model.set(data, {silent: silent});
    };

    api.vent = _.extend({}, Backbone.Events);

    api.Views.register_module = function (args) {
        if ('default' !== api.mode) {
            this.Modules[ api.mode ] = this.Modules.default.extend(args);
        }
    };

    api.Views.init_module = function (args) {
        if (themifyBuilder.modules[args.mod_name] === undefined) {
            return false;
        }
        if (args.mod_settings === undefined && themifyBuilder.modules[ args.mod_name ].defaults !== undefined) {
            args.mod_settings = _.extend({}, themifyBuilder.modules[ args.mod_name ].defaults);
        }

        var model = args instanceof api.Models.Module ? args : new api.Models.Module(args),
                callback = this.get_module(),
                view = new callback({model: model, type: api.mode});

        return {
            model: model,
            view: view
        };
    };

    api.Views.get_module = function () {
        return this.Modules[ api.mode ];
    };

    api.Views.unregister_module = function () {
        if ('default' !== api.mode){
            this.Modules[ api.mode ] = null;
            delete this.Modules[ api.mode ];
        }
    };

    api.Views.module_exists = function () {
        return this.Modules.hasOwnProperty(api.mode);
    };

    // column
    api.Views.register_column = function (args) {
        if ('default' !== api.mode){
            this.Columns[ api.mode ] = this.Columns.default.extend(args);
        }
    };

    api.Views.init_column = function (args) {
        var model = args instanceof api.Models.Column ? args : new api.Models.Column(args),
                callback = this.get_column(),
                view = new callback({model: model, type: api.mode});

        return {
            model: model,
            view: view
        };
    };

    api.Views.get_column = function () {
         return this.Columns[api.mode];
    };

    api.Views.unregister_column = function () {
        if ('default' !== api.mode){
            this.Columns[ api.mode ] = null;
            delete this.Columns[ api.mode ];
        }
    };

    api.Views.column_exists = function () {
        return this.Columns.hasOwnProperty(api.mode);
    };

    // sub-row
    api.Views.register_subrow = function (args) {
        if ('default' !== api.mode){
            this.SubRows[ api.mode ] = this.SubRows.default.extend(args);
        }
    };

    api.Views.init_subrow = function (args) {
        var model = args instanceof api.Models.SubRow ? args : new api.Models.SubRow(args),
                callback = this.get_subrow(),
                view = new callback({model: model, type: api.mode});

        return {
            model: model,
            view: view
        };
    };

    api.Views.get_subrow = function () {
         return this.SubRows[ api.mode ];
    };

    api.Views.unregister_subrow = function () {
        if ('default' !== api.mode){
            this.SubRows[ api.mode ] = null;
            delete this.SubRows[ api.mode ];
        }
    };

    api.Views.subrow_exists = function () {
        return this.SubRows.hasOwnProperty(api.mode);
    };

    // Row
    api.Views.register_row = function (args) {
        if ('default' !== api.mode){
            this.Rows[ api.mode ] = this.Rows.default.extend(args);
        }
    };

    api.Views.init_row = function (args) {
        var attr = args.attributes;
        if (attr === undefined || ((attr.cols !== undefined && (Object.keys(attr.cols) > 0 || attr.cols.length > 0)) || (attr.styling !== undefined && Object.keys(attr.styling).length > 0))) {
            var model = args instanceof api.Models.Row ? args : new api.Models.Row(args),
                    callback = this.get_row(),
                    view = new callback({model: model, type: api.mode});

            return {
                model: model,
                view: view
            };
        }
        else {
            return false;
        }
    };

    api.Views.get_row = function () {
        return this.Rows[ api.mode ];
    };

    api.Views.unregister_row = function () {
        if ('default' !== api.mode){
            this.Rows[ api.mode ] = null;
            delete this.Rows[ api.mode ];
        }
    };

    api.Views.row_exists = function () {
        return this.Rows.hasOwnProperty(api.mode);
    };

    api.Views.BaseElement = Backbone.View.extend({
        type: 'default',
        events: {
            'click .tb_copy_component': 'copy',
            'click .tb_paste_component': 'paste',
            'click .tb_copy_style': 'copyStyling',
            'click .tb_paste_style': 'pasteStyling',
            'click .tb_import_component': 'import',
            'click .tb_export_component': 'export',
            'click .tb_save_component': 'save',
            'click .tb_duplicate': 'duplicate',
            'click .tb_delete': 'delete'
        },
        initialize: function (options) {
            _.extend(this, _.pick(options, 'type'));

            this.listenTo(this.model, 'custom:change', this.modelChange);
            this.listenTo(this.model, 'destroy', this.remove);
        },
        modelChange: function () {

            this.$el.attr(_.extend({}, _.result(this, 'attributes')));
            var el = this.render(),
                cid = api.beforeEvent.data('cid');
            $('.tb_element_cid_' + cid).replaceWith(el.el);
            if (api.mode === 'visual') {
                this.model.trigger('visual:change');
            }
            else {
                if (api.eventName === 'row') {
                    cid = this.$el.data('cid');
                }
                api.undoManager.push(cid, api.beforeEvent, this.$el, api.eventName);
                api.Mixins.Builder.update(this.$el);
                if (api.eventName === 'row') {
                    api.vent.trigger('dom:builder:change');
                }
            }
        },
        remove: function () {
            this.$el.remove();
        },
        copy: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $selected = $(e.currentTarget).closest('[data-cid]'),
                model = api.Models.Registry.lookup($selected.data('cid')); 
            if (!model) {
                return;
            }
            var component = model.get('elType');
            if (component === 'column') {
                component = model.get('component_name');
            }
            model = null;
            var data = this.getData($selected, component);
            if (component === 'sub-column') {
                data['component_name'] = component;
            }
            
            ThemifyBuilderCommon.Clipboard.set(component, data);
            var $dropdown = $selected.find('.tb_action_more > ul');
            $dropdown.css('top','10000%');
            setTimeout(function(){
                $dropdown.css('top','100%');
            },100);
        },
        paste: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $el = $(e.currentTarget).closest('[data-cid]'),
                model = api.Models.Registry.lookup($el.data('cid')); 
            if (!model) {
                return;
            }

            var component = model.get('elType');
            if (component === 'column') {
				component = model.get('component_name');
			}

			var data = ThemifyBuilderCommon.Clipboard.get(component);
            data = (false === data && 'column' === component) ? ThemifyBuilderCommon.Clipboard.get('sub-column') : data;
            data = (false === data && 'sub-column' === component) ? ThemifyBuilderCommon.Clipboard.get('column') : data;
            if (data === false) {
                ThemifyBuilderCommon.alertWrongPaste();
                return;
            }
            if (!ThemifyBuilderCommon.confirmDataPaste()) {
                return;
            }
            api.eventName = 'row';
            if (component === 'column' || component === 'sub-column') {
                data['grid_class'] = api.Utils.filterClass($el.prop('class'));
                if ($el.hasClass('first')) {
                    data['grid_class'] += ' first';
                }
                else if ($el.hasClass('last')) {
                    data['grid_class'] += ' last';
                }
                var width = $el[0].style['width'];
                if (width) {
                    data['grid_width'] = width.replace('%', '');
                }
                else {
                    data['grid_width'] = null;
                }
                data['component_name'] = component;
            }
            api.beforeEvent = ThemifyBuilderCommon.clone($el);
            api.hasChanged = true;
            model.setData(data);
        },
        // Copy component/modules styles
        copyStyling: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $selected = $(e.currentTarget).closest('[data-cid]'),
                model = api.Models.Registry.lookup($selected.data('cid'));
            if (!model) {
                return;
            }
            var data = {},
                component = model.get('elType');
            if ('module' !== component) {
                if (component === 'column') {
                    component = model.get('component_name');
                }
                data = model.attributes.styling;
            } else {
                var moduleName = model.get('mod_name'),
                    component = moduleName,
                    stylingFields = themifyBuilder.modules[moduleName].styling,
                    attributes = model.attributes.mod_settings;
                data = overrideStyle(stylingFields, attributes, data);
                function overrideStyle(stylingFields, attributes, data) {
                    Object.keys(attributes).forEach(function (key) {
                        if ('breakpoint_mobile' === key || 'breakpoint_tablet' === key || 'breakpoint_tablet_landscape' === key) {
                            data[key] = {};
                            data[key] = overrideStyle(stylingFields, attributes[key], data[key]);
                        } else {
                            var foundKey = key,
                                index = key.indexOf('-');
                            if (index !== -1) {
                                foundKey = key.substring(0, index);
                            }
                            var paddingField = (-1 !== foundKey.indexOf('padding')) ? true : false,
                                marginField = (-1 !== foundKey.indexOf('margin')) ? true : false,
                                borderField = (-1 !== foundKey.indexOf('border')) ? true : false;
                            if (stylingFields.includes(foundKey) || paddingField || marginField || borderField) {
                                data[key] = attributes[key];
                            }
                        }

                    });
                    return data;
                }

            }
            model = null;
            ThemifyBuilderCommon.Clipboard.set(component + 'Styling', data);
            var $dropdown = $selected.find('.tb_action_more > ul');
            $dropdown.css('top','10000%');
            setTimeout(function(){
                $dropdown.css('top','100%');
            },100);
        },
        // Paste component/modules styles
        pasteStyling: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $el = $(e.currentTarget).closest('[data-cid]'),
                model = api.Models.Registry.lookup($el.data('cid'));
            if (!model) {
                return;
            }
            var component = model.get('elType'),
                component = 'column' === component ? component = model.get('component_name') : component,
                moduleName = 'module' === component ? model.get('mod_name') : component,
                styling = ThemifyBuilderCommon.Clipboard.get(moduleName + 'Styling');
            styling = (false === styling && 'column' === component) ? ThemifyBuilderCommon.Clipboard.get('sub-columnStyling') : styling;
            styling = (false === styling && 'sub-column' === component) ? ThemifyBuilderCommon.Clipboard.get('columnStyling') : styling;
            if (styling === false) {
                ThemifyBuilderCommon.alertWrongPaste();
                return;
            }
            if (!ThemifyBuilderCommon.confirmDataPaste()) {
                return;
            }
            var data = this.getData($el, component);
            if ('module' === component) {
                var newSettings = data.mod_settings;
                Object.keys(styling).forEach(function (key) {
                    if ('breakpoint_mobile' === key || 'breakpoint_tablet' === key || 'breakpoint_tablet_landscape' === key) {
                        newSettings[key] = undefined !== newSettings[key] ? newSettings[key] : {};
                        Object.keys(styling[key]).forEach(function (breakpointKey) {
                            newSettings[key][breakpointKey] = styling[key][breakpointKey];
                        });
                    } else {
                        newSettings[key] = styling[key];
                    }
                });
                data.mod_settings = newSettings;
            } else {
                data.styling = styling;
            }
            api.eventName = 'row';
            if (component === 'column' || component === 'sub-column') {
                data['grid_class'] = api.Utils.filterClass($el.prop('class'));
                if ($el.hasClass('first')) {
                    data['grid_class'] += ' first';
                }
                else if ($el.hasClass('last')) {
                    data['grid_class'] += ' last';
                }
                var width = $el[0].style['width'];
                if (width) {
                    data['grid_width'] = width.replace('%', '');
                }
                else {
                    data['grid_width'] = null;
                }
                data['component_name'] = component;
            }
            api.beforeEvent = ThemifyBuilderCommon.clone($el);
            api.hasChanged = true;
            model.setData(data);
        },
        import: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $thisElem = $(e.currentTarget),
                    component = ThemifyBuilderCommon.detectBuilderComponent($thisElem),
                    el = $thisElem.closest('[data-cid]'),
                    model = api.Models.Registry.lookup(el.data('cid')),
                    options = {
                        data: {
                            action: 'tb_component_data',
                            component: component,
                            type: 'import'
                        }
                    };
            if (component === 'column' || component === 'sub-column') {
                var $selectedCol = $thisElem.closest('.module_column'),
                        $selectedRow = $selectedCol.closest('column' === component ? '.module_row' : '.module_subrow').index();
                options.data.indexData = {row: $selectedRow, col: $selectedCol.index()};
            }
            ThemifyBuilderCommon.Lightbox.open(options, null, function () {
                var $lightbox = this.$lightbox;
                $lightbox.find('#builder_submit_import_component_form').on('click', function (e) {
                    e.preventDefault();
                    var $dataField = $lightbox.find('#tb_data_field'),
                            dataPlainObject = JSON.parse($dataField.val());
                    if ((component === 'column' && dataPlainObject['component_name'] === 'sub-column') || (component === 'sub-column' && dataPlainObject['component_name'] === 'column')) {
                        dataPlainObject['component_name'] = component;
                    }
                    if (dataPlainObject['component_name'] === undefined || dataPlainObject['component_name'] !== component) {
                        ThemifyBuilderCommon.alertWrongPaste();
                        return;
                    }
                    dataPlainObject = api.Utils.clear(dataPlainObject, true);
                    if (component === 'column' || component === 'sub-column') {
                        dataPlainObject['column_order'] = $selectedCol.index();
                        dataPlainObject['grid_class'] = $selectedCol.prop('class');

                        if ('column' === component) {
                            dataPlainObject['row_order'] = $selectedRow;
                        } else {
                            dataPlainObject['sub_row_order'] = $selectedRow;
                            dataPlainObject['row_order'] = $selectedCol.closest('.module_row').index();
                            dataPlainObject['col_order'] = $selectedCol.parents('.module_column').index();
                        }
                    }
                    api.eventName = 'row';
                    api.beforeEvent = ThemifyBuilderCommon.clone(el);
                    api.hasChanged = true;
                    model.setData(dataPlainObject);
                    ThemifyBuilderCommon.Lightbox.close();
                });
            });
        },
        getData: function (el, component) {
            var data = {},
                    type = component || ThemifyBuilderCommon.detectBuilderComponent(el);
            switch (type) {
                case 'row':
                case 'subrow':
                    var $selectedRow = el.closest('.module_' + type),
                            rowOrder = $selectedRow.index();
                    data = api.Utils._getRowSettings($selectedRow[0], rowOrder, type);
                    break;
                case 'module':
                    data = api.Models.Registry.lookup(el.closest('.active_module').data('cid')).attributes;
                    data = api.Utils.clear(data, true);
                    break;
                case 'column':
                case 'sub-column':
                    var $selectedCol = el.closest('.module_column'),
                            $selectedRow = $selectedCol.closest('column' === type ? '.module_row' : '.module_subrow'),
                            rowOrder = $selectedRow.index(),
                            rowData = api.Utils._getRowSettings($selectedRow[0], rowOrder, 'column' === type ? 'row' : 'subrow'),
                            data = rowData.cols[ $selectedCol.index() ];
                    break;
            }
            return data;
        },
        export: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $thisElem = $(e.currentTarget),
                    component = ThemifyBuilderCommon.detectBuilderComponent($thisElem),
                    data = this.getData($thisElem, component),
                    options = {
                        data: {
                            action: 'tb_component_data',
                            component: component,
                            type: 'export'
                        }
                    };

            data['component_name'] = component;
            data = JSON.stringify(data);
            ThemifyBuilderCommon.Lightbox.open(options, null, function () {
                this.$lightbox.find('#tb_data_field').val(data).on('click', function () {
                    $(this).trigger('focus').trigger('select');
                });
            });
        },
        duplicate: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var current = $(e.currentTarget).closest('[data-cid]'),
                el = ThemifyBuilderCommon.clone(current),
                model = api.Models.Registry.lookup(el.data('cid'));
            current.removeClass('tb_element_cid_' + model.cid);
            el.hide().insertAfter(current);
            var k = model.get('elType') === 'module' ? 'mod_settings' : 'styling',
                data = this.getData(el, model.get('elType'));
            if (api.activeModel && api.activeModel.cid === model.cid && ThemifyBuilderCommon.Lightbox.$lightbox.is(':visible') ) {
                    var	options = api.Forms.serialize('tb_options_setting'),
                            animation = api.Forms.serialize('tb_options_animation'),
                            visible = api.Forms.serialize('tb_options_visibility'),
                            new_data = $.extend(true, {}, api.Mixins.Common.styleData),
                            stylefields = api.Forms.serialize('tb_options_styling', new_data);

                    stylefields = api.Utils.clear(stylefields, true);

                    var result = $.extend(true, stylefields, options, animation, visible);
                    result = api.Utils.clear(result);
                    data[k] = result;
            }
            api.eventName = 'duplicate';
            api.beforeEvent = el;
            api.hasChanged = true;
            model.setData(data);
            current.addClass('tb_element_cid_' + model.cid);
        },
        editComponent: function () {
            api.hasChanged = false;
            var component = api.activeModel.get('elType'),
                lightbox = ThemifyBuilderCommon.Lightbox.$lightbox;
            if (api.autoSaveCid === api.activeModel.cid) {
               
                if (api.activeModel.get('styleClicked')) {
                    lightbox.find('a[href="#tb_options_styling"]').trigger('click');
                    api.activeModel.unset('styleClicked', {silent: true});
                }
                else if (api.activeModel.get('visibileClicked')) {
                    lightbox.find('a[href="#tb_options_visibility"]').trigger('click');
                    api.activeModel.unset('visibileClicked', {silent: true});
                }
                else if(component==='module' || component==='row'){
                    lightbox.find('a[href="#tb_options_setting"]').trigger('click');
                }
                return;
            }
            var template = component === 'module' ? api.activeModel.get('mod_name') : component;
            ThemifyBuilderCommon.Lightbox.open({loadMethod: 'inline', templateID: 'builder_form_' + template}, function (response) {
                api.Mixins.Common.editComponentCallback(response, component, false, false);
            }, function (response) {
                if (api.activeModel.get('styleClicked')) {
					lightbox.find('a[href="#tb_options_styling"]').trigger('click');
                }
                else if (api.activeModel.get('visibileClicked')) {
                    lightbox.find('a[href="#tb_options_visibility"]').trigger('click');
                }
                else {
                    var scroll = component === 'column' || component === 'subrow' ? 'tb_options_styling' : 'tb_options_setting';
                    new SimpleBar(lightbox.find('#' + scroll)[0]);
                }
                api.autoSaveCid = api.activeModel.cid;
                setTimeout(function(){
                    api.activeModel.unset('styleClicked', {silent: true});
                    api.activeModel.unset('visibileClicked', {silent: true});
                },500);
            });
        },
        delete: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var current = $(e.currentTarget),
                    item = current.closest('[data-cid]'),
                    cid = item.data('cid'),
                    model = api.Models.Registry.lookup(cid);
                    if(!model){
                        return;
                    }
                    var component = model.get('elType');
                    if (!confirm(themifyBuilder.i18n[component + 'DeleteConfirm'])) {
                        return;
                    }
                var before = item.closest('.module_row'),
                    type = 'row',
                    after = '',
                    data = {};
                if (component === 'row') {
                    data['pos_cid'] = before.next('.module_row');
                    data['pos'] = 'before';
                    if (data['pos_cid'].length === 0) {
                        data['pos'] = 'after';
                        data['pos_cid'] = before.prev('.module_row');
                    }
                    type = 'delete_row';
                    data['pos_cid'] = data['pos_cid'].data('cid');
                }
                else {
                    cid = before.data('cid');
                }
                before = ThemifyBuilderCommon.clone(before);
                if(component!=='row'){
                    var r = item.closest('.module_subrow');
                }
                model.destroy();
                if(component!=='row' && r.length>0 && r.find('.active_module').length===0){
                   r.addClass('tb_row_empty');
                }
                if (component!== 'row') {
                    after = $('.tb_element_cid_' + cid);
                    var r  = after.closest('.module_row');
                    if(r.find('.active_module').length===0){
                        r.addClass('tb_row_empty');
                    }
                    r = null;
                }
                else {
                    api.vent.trigger('dom:builder:change');
                }
                api.hasChanged = true;
                api.undoManager.push(cid, before, after, type, data);
                api.toolbar.pageBreakModule.countModules();
        },
        save: function (e){
                e.preventDefault();
                e.stopPropagation();
                var options = {
                        data: {
                                action: 'tb_library_item_form',
                                postid: themifyBuilder.post_ID,
                                model: this.model.cid,
                                type: ThemifyBuilderCommon.detectBuilderComponent($(e.currentTarget))
                        }
                };
                ThemifyBuilderCommon.Lightbox.open(options);
            }

    });

    api.Views.BaseElement.extend = function (child) {
        var self = this,
                view = Backbone.View.extend.apply(this, arguments);
        view.prototype.events = _.extend({}, this.prototype.events, child.events);
        view.prototype.initialize = function () {
            if (_.isFunction(self.prototype.initialize))
                self.prototype.initialize.apply(this, arguments);
            if (_.isFunction(child.initialize))
                child.initialize.apply(this, arguments);
        };
        return view;
    };

    api.Views.Modules['default'] = api.Views.BaseElement.extend({
        tagName: 'div',
        attributes: function () {
            return {
                'class': 'tb_module module-' + this.model.get('mod_name') + ' active_module tb_element_cid_' + this.model.cid+' tb_'+this.model.get('element_id'),
                'data-cid': this.model.cid
            };
        },
        template: api.mode === 'visual'?null:wp.template('builder_module_item'),
        events: {
            'dblclick': 'edit',
            'click .themify_module_options': 'edit',
            'click .tb_module_styling': 'edit',
            'click .tb_visibility_component ': 'edit',
            'click .tb_swap': 'edit'
        },
        initialize: function () {
            this.listenTo(this, 'edit', this.edit);
            this.listenTo(this.model, 'dom:module:unsaved', this.removeUnsaved);
            this.listenTo(this.model, 'change:view', this.setView);

        },
        removeUnsaved: function () {
            this.model.destroy();
        },
        render: function () {
            if(api.mode !== 'visual'){
                this.el.innerHTML = this.template(this.model.toRenderData());;
            }
            return this;
        },
        setView: function (node) {
            this.setElement(node);
        },
        edit: function (e) {
            if (api.isPreview){
                return true;
            }
            if (e !== null) {
                e.preventDefault();
                e.stopPropagation();
                var cl = e.currentTarget.classList;
                if(api.mode==='visual' && !api.Forms.LayoutPart.id && this.model.get('mod_name') === 'layout-part' && !cl.contains('tb_swap')  && (e.type === 'dblclick' || cl.contains('themify_module_options'))){
                        api.Forms.LayoutPart.edit(e.currentTarget);
                        return;
                }
                if (cl.contains('tb_module_styling')) {
                    this.model.set({styleClicked: true}, {silent: true});
                }
                else if (cl.contains('tb_visibility_component')) {
                    this.model.set({visibileClicked: true}, {silent: true});
                }
            }

            if (this.model.cid !== api.autoSaveCid && api.autoSaveCid !== null) {
                api.Forms.saveComponent(null);
            }
            api.activeModel = this.model;
            this.editComponent();
        }
    });

    api.Views.Columns['default'] = api.Views.BaseElement.extend({
        tagName: 'div',
        attributes: function () {
            var classes = 'column' === this.model.get('component_name') ? '' : ' sub_column',
                    attr = {
                        'class': 'module_column tb-column tb_element_cid_' + this.model.cid + ' ' + this.model.get('grid_class') + classes,
                        'data-cid': this.model.cid,
                        'data-id': this.model.get('element_id')
                    };
            if (this.model.get('grid_width')) {
                attr['style'] = 'width:' + this.model.get('grid_width') + '%';
            }
            return attr;
        },
        template: wp.template('builder_column_item'),
        events: {
            'click .tb_option_column': 'edit',
            'dblclick': 'edit'
        },
        initialize: function () {
            this.listenTo(this.model, 'change:view', this.setView);
        },
        render: function (identify,identify2) {
            var component = this.model.get('component_name');
            this.el.innerHTML = this.template({component_name: component});
            var modules = this.model.get('modules');
            // check if it has module
            if (modules) {
                var container = document.createDocumentFragment();
                for (var i in modules) {
                    if (modules[i]!== undefined && modules[i]!== null) {
                            var m = modules[i],
                            moduleView = m.cols === undefined ? api.Views.init_module(m) : api.Views.init_subrow(m);
                            if(moduleView){
                                var cidentify = m.cols === undefined && identify2?identify2+'-'+i:(identify ? identify + '-' + i : false);
                                if (api.id && m.mod_name && cidentify!==false) {
                                    api.VisualCache[moduleView.model.cid] = m.mod_name + '-' + api.id + '-' + cidentify;
                                }
                                container.appendChild(moduleView.view.render(cidentify).el);
                            }
                    }
                }
                var holder = this.el.getElementsByClassName('tb_holder')[0];
                holder.appendChild(container);
                if(component==='sub-column'){
                    holder.classList.add('tb_subrow_holder');
                }
            }
            return this;
        },
        edit: function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (this.model.cid !== api.autoSaveCid && api.autoSaveCid !== null) {
                api.Forms.saveComponent(null);
            }
            api.activeModel = this.model;
            this.editComponent();

        },
        setView: function (node) {
            this.setElement(node);
        }
    });

    // SubRow view share same model as ModuleView
    api.Views.SubRows['default'] = api.Views.BaseElement.extend({
        tagName: 'div',
        attributes: function () {
            var attr = {
                'class': 'themify_builder_sub_row module_subrow active_module clearfix tb_element_cid_' + this.model.cid,
                'data-cid': this.model.cid,
                'data-id': this.model.get('element_id')
            };
            return attr;
        },
        events: {
            'dblclick': 'edit',
            'click .tb_style_subrow': 'edit',
            'click .tb_visibility_subrow': 'edit',
            'mousedown select':'openSelectBox'
        },
        initialize: function () {
            this.listenTo(this.model, 'change:view', this.setView);
        },
        openSelectBox:function(e){
            e.stopPropagation();  
        },
        render: function (identify) {
            var cols = this.model.get('cols'),
                    len = Object.keys(cols).length;
            this.el.innerHTML = ThemifyBuilderCommon.templateCache.get('tmpl-builder_subrow_item');
            if (api.id) {
                api.VisualCache[this.model.cid] = 'sub_row_' + identify;
            }
            if (len > 0) {
                var container = document.createDocumentFragment(),
                    not_empty = false;
                for (var i = 0; i <= len; ++i) {
                    if (cols[i] !== undefined) {
                        cols[i].component_name = 'sub-column';
                        var sidentify = identify + '-' + i,
                            columnView = api.Views.init_column(cols[i]);
                        if (api.id) {
                            api.VisualCache[columnView.model.cid] = 'sub_column_post_' + api.id + ' sub_column_' + sidentify;
                        }
                        container.appendChild(columnView.view.render('sub_row_' + sidentify).el);
                        if(not_empty===false && cols[i].modules!==undefined && cols[i].modules.length>0){
                            not_empty = true;
                        }
                    }
                }
                if(not_empty===false){
                   this.el.classList.add('tb_row_empty'); 
                }
                this.el.getElementsByClassName('subrow_inner')[0].appendChild(container);
            }
            if (api.init && !api.id) {
                api.Utils.selectedGridMenu(this.el);
            }
            return this;
        },
        setView: function (node) {
            this.setElement(node);
        },
        edit: function (e) {
            e.stopPropagation();
            e.preventDefault();

			if (e.currentTarget.classList.contains('tb_style_subrow')) {
				this.model.set({styleClicked: true}, {silent: true});
			}
			else if (e.currentTarget.classList.contains('tb_visibility_subrow')) {
				this.model.set({visibileClicked: true}, {silent: true});
			}

            if (this.model.cid !== api.autoSaveCid && api.autoSaveCid !== null) {
                api.Forms.saveComponent(null);
            }
            api.activeModel = this.model;
            this.editComponent();

        }
    });

	api.Views.Rows['default'] = api.Views.BaseElement.extend({
		tagName: 'div',
		attributes: function () {
			var pageBreakClass = ( this.model.attributes.styling
				&& ( 'custom_css_row' in this.model.attributes.styling )
				&& this.model.attributes.styling.custom_css_row.indexOf( 'tb-page-break' ) !== -1 )
					? 'tb-page-break' : '';

			var attr = {
				'class': 'themify_builder_row module_row clearfix tb_element_cid_' + this.model.cid + ' ' + pageBreakClass,
				'data-cid': this.model.cid
			};

			return attr;
		},
        events: {
            'click .tb_option_row': 'edit',
            'dblclick': 'edit',
            'click .tb_style_row': 'edit',
            'click .tb_visibility_component ': 'edit',
            'click .tb_grid_list li a': '_gridClicked',
            'click .grid_tabs li a': '_switchGridTabs',
            'click .tb_column_alignment li a': '_columnAlignmentClicked',
            'click .tb_column_direction li a': '_columnDirectionClicked',
            'change .gutter_select': '_gutterChange',
			// Options Preview
			'dblclick .tb_options_row_hover': '_stopLightbox',
			'hover .tb_option_row': '_updatePreviewOptions',
			'click .tb_row_hover__expand': '_expandOptions',
			'blur .tb_options_row_hover .tb_row_hover__input': '_setPreviewOption',
			'click .tb_row_hover__icon': '_setPreviewOption'
        },
        initialize: function () {
            this.listenTo(this.model, 'change:view', this.setView);
        },
        render: function (row,index) {
            var cols = this.model.get('cols'),
                    len = Object.keys(cols).length;
            this.el.innerHTML = ThemifyBuilderCommon.templateCache.get('tmpl-builder_row_item');
            if (len > 0) {
                var container = document.createDocumentFragment(),
                        col_order,
                        identify = false,
                        identify2 = false,
                        not_empty = false;
                for (var i = 0; i <= len; ++i) {
                    if (cols[i] !== undefined) {
                        cols[i].component_name = 'column';
                        var columnView = api.Views.init_column(cols[i]);
                        if (api.id) {
                            col_order = columnView.model.get('column_order');
                            if (col_order === undefined) {
                                col_order = i;
                            }
                            identify = row + '-' + col_order;
                            identify2 = index + '-' + col_order;
                        }
                        if (api.id) {
                            api.VisualCache[columnView.model.cid] = 'module_column_' + col_order + ' tb_' + api.id + '_column';
                        }
                        container.appendChild(columnView.view.render(identify,identify2).el);
                        if(not_empty===false && cols[i].modules!==undefined && cols[i].modules.length>0){
                            not_empty = true;
                        }
                    }
                }
                if(not_empty===false){
                   this.el.classList.add('tb_row_empty'); 
                }
                this.el.getElementsByClassName('row_inner')[0].appendChild(container);
            } else {
                this.el.classList.add('tb_row_empty'); 
                // Add column
                api.Utils._addNewColumn({
                    newclass: 'col-full',
                    component: 'column'
                }, this.el.getElementsByClassName('row_inner')[0]);
            }
            api.Utils.selectedGridMenu(this.el);

            return this;
        },
        edit: function (e) {
            e.stopPropagation();
            e.preventDefault();
            if(api.activeModel && api.activeModel.cid===this.model.get('cid')){
                return;
            }
            if (e.currentTarget.classList.contains('tb_style_row')) {
                this.model.set({styleClicked: true}, {silent: true});
            }
            else if (e.currentTarget.classList.contains('tb_visibility_component')) {
                this.model.set({visibileClicked: true}, {silent: true});
            }
            if (this.model.cid !== api.autoSaveCid && api.autoSaveCid !== null) {
                api.Forms.saveComponent(null);
            }
            api.activeModel = this.model;
            this.editComponent();
        },
        _switchGridTabs: function (e) {
            api.scrollTo = $(e.currentTarget).closest('[data-cid]');
            api.Forms.lightbox_switcher(e);
        },
        _gridClicked: function (e) {
            e.preventDefault();
            var $this = $(e.currentTarget),
                    set = $this.data('grid'),
                    handle = $this.data('handle'),
                    $base,
                    row,
                    is_sub_row = false,
                    type = $this.data('type'),
                    $p = $this.parent(),
                    is_desktop = type === 'desktop';
            if($p.hasClass('selected')){
                return;
            }
            var before = ThemifyBuilderCommon.clone($this.closest('.module_row'));
            $p.addClass('selected').siblings().removeClass('selected');
            if (handle === 'module') {
                if (set[0] !== '-full') {
                    is_sub_row = true;
                    var subRowDataPlainObject = {
                        cols: [{grid_class: 'col-full'}]
                    },
                    subRowView = api.Views.init_subrow(subRowDataPlainObject),
                            $mod_ori = $this.closest('.active_module'),
                            $mod_clone = $mod_ori.clone();
                    $mod_clone.insertAfter($mod_ori);
                    $base = subRowView.view.render().$el
                            .find('.tb_holder')
                            .prepend($mod_ori)
                            .end()
                            .insertAfter($mod_clone)
                            .find('.' + $this.prop('class').replace(' ', '.'))
                            .closest('li')
                            .addClass('selected')
                            .siblings().removeClass('selected')
                            .end().end().end()
                            .find('.subrow_inner');
                    $mod_clone.remove();
                    row = $base.closest('.module_subrow');
                }
            }
            else {
                is_sub_row = handle === 'subrow';
                row = $this.closest('.module_' + handle);
                $base = row.find('.' + handle + '_inner').first();
            }
            if ($base.length === 0) {
                return;
            }
            if (is_desktop || handle === 'module') {
                var $both = $base,
                        col = $this.data('col');
                $both = $both.add(row);
                if (col === undefined) {
                    col = 1;
                    $this.data('col', col);
                }
                $both.removeClass('col-count-1 col-count-' + $base.attr('data-basecol')).addClass('col-count-' + col);
                $base.attr('data-basecol', col);
                if (is_desktop) {
                    $this.closest('.tb_grid_menu').find('.tb_grid_reposnive .tb_grid_list').each(function () {
                        var selected = $(this).find('.selected'),
                                item = selected.find('a'),
                                mode = item.data('type'),
                                rcol = item.data('col');
                        if (rcol !== undefined && (rcol > col || (col === 4 && rcol === 3) || (col >= 4 && rcol >= 4 && col != rcol))) {
                            selected.removeClass('selected');
                            $base.removeClass('tb_grid_classes col-count-' + $base.attr('data-basecol') + ' ' + $base.attr('data-col_' + mode)).attr('data-col_' + mode, '');
                            $(this).closest('.tb_grid_list').find('.' + mode + '-auto').parent().addClass('selected');
                        }
                    });
                }
            }
            else {
                if (set[0] !== '-auto') {
                    var cl = 'column' + set.join('-'),
                            col = $this.data('col');
                    if (col === 3 && $base.attr('data-basecol') > col) {
                        cl += ' tb_3col';
                    }
                    $base.removeClass($base.attr('data-col_tablet') + ' ' + $base.attr('data-col_tablet_landscape') + ' ' + $base.attr('data-col_mobile'))
                            .addClass(cl + ' tb_grid_classes col-count-' + $base.attr('data-basecol')).attr('data-col_' + type, cl);
                }
                else {
                    $base.removeClass('tb_grid_classes tb_3col col-count-' + $base.attr('data-basecol') + ' ' + $base.attr('data-col_' + type)).attr('data-col_' + type, '');
                }
                if (api.mode === 'visual') {
                    $('body', top_iframe).height(document.body.scrollHeight);
                }
                api.Utils.setCompactMode($base.children('.module_column'));
                return false;
            }

            var cols = $base.children('.module_column'),
                    set_length = set.length,
                    col_cl = 'module_column' + (is_sub_row ? ' sub_column' : '') + ' col';
            for (var i = 0; i < set_length; ++i) {
                var c = cols.eq(i);
                if (c.length > 0) {
                    c.removeClass(api.Utils.gridClass.join(' ')).addClass(col_cl + set[i]);
                } else {
                    // Add column
                    api.Utils._addNewColumn({
                        newclass: col_cl + set[i],
                        component: is_sub_row ? 'sub-column' : 'column'
                    }, $base[0]);
                }
            }

            // remove unused column
            if (set_length < $base.children().length) {
                $base.children('.module_column').eq(set_length - 1).nextAll().each(function () {
                    // relocate active_module
                    var modules = $(this).find('.tb_holder').first();
                    modules.children().appendTo($(this).prev().find('.tb_holder').first());
                    $(this).remove(); // finally remove it
                });
            }
            var $children = $base.children();
            $children.removeClass('first last');
            if ($base.hasClass('direction-rtl')) {
                $children.last().addClass('first');
                $children.first().addClass('last');
            }
            else {
                $children.first().addClass('first');
                $children.last().addClass('last');
            }
            // remove sub_row when fullwidth column
            if (is_sub_row && set[0] === '-full') {
                var subrow = $base.closest('.module_subrow'),
                    $move_modules = $base.find('.active_module');
                $move_modules.insertAfter(subrow);
                subrow.remove();
                $move_modules.find('.tb_grid_list .grid-layout--full').parent().addClass('selected').siblings().removeClass('selected');
            }
            api.Utils.columnDrag($base, true);
            var row = $this.closest('.module_row');
            //api.Mixins.Builder.columnSort(row);
            api.hasChanged = true;
            api.Mixins.Builder.updateModuleSort(row);
            api.undoManager.push(row.data('cid'), before, row, 'row');
            Themify.body.trigger('tb_grid_changed',[row]);
        },
        _columnAlignmentClicked: function (e) {
            e.preventDefault();
            var $this = $(e.currentTarget),
                    handle = $this.data('handle'),
                    $row = null;
            if (handle === 'module' || $this.closest('li').hasClass('selected')) {
                return;
            }
            $this.closest('li').addClass('selected').siblings('li').removeClass('selected');
            $row = $this.closest('.module_' + handle);
            var alignment = $this.data('alignment'),
                    el = api.Models.Registry.lookup($row.data('cid')),
                before = ThemifyBuilderCommon.clone($row);
            $row.find('.' + handle + '_inner').first().removeClass(el.get('column_alignment')).addClass(alignment);
            el.set({column_alignment: alignment}, {silent: true});
            api.undoManager.push( before.data('cid'), before, $this.closest('.module_' + handle), 'row');
        },
        _columnDirectionClicked: function (e) {
            e.preventDefault();
            var $this = $(e.currentTarget),
                    handle = $this.data('handle'),
                    dir = $this.data('dir'),
                    $row = null;
            if (handle === 'module' || $this.closest('li').hasClass('selected')) {
                return;
            }
            $this.closest('li').addClass('selected').siblings('li').removeClass('selected');
            $row = $this.closest('.module_' + handle);
            var inner = $row.find('.' + handle + '_inner').first(),
                columns = inner.children('.module_column'),
                first = columns.first(),
                last = columns.last();
            if (dir === 'rtl') {
                first.removeClass('first').addClass('last');
                last.removeClass('last').addClass('first');
                inner.addClass('direction-rtl');
            }
            else {
                first.removeClass('last').addClass('first');
                last.removeClass('first').addClass('last');
                inner.removeClass('direction-rtl');
            }

            inner.attr('data-' + api.activeBreakPoint + '_dir', dir);
        },
        _gutterChange: function (e) {
            var $this = $(e.currentTarget),
                    handle = $this.data('handle');
            if (handle === 'module') {
                return;
            }
            var val = $this.val();
            $this.find('option').removeAttr('selected').filter('[value="' + val + '"]').attr('selected', 'selected');//need for undo/redo
            var row = $this.closest('.module_' + handle),
                    before = ThemifyBuilderCommon.clone(row),
                    inner = row.find('.' + handle + '_inner').first(),
                    el = api.Models.Registry.lookup(row.data('cid'));
            before.find('.tb_action_wrap .gutter_select').val(row.data('gutter'));//need for undo/redo
            api.Utils.columnDrag(inner, false, el.get('gutter'), val);
            inner.removeClass(el.get('gutter')).addClass(val);
            el.set({gutter: val}, {silent: true});
            api.undoManager.push( before.data('cid'), before, $this.closest('.module_' + handle), 'row');
        },
        setView: function (node) {
            this.setElement(node);
        },
		_stopLightbox: function( e ) {
			e.stopPropagation();
		},
		_updatePreviewOptions: function() {
			var options = $( '.tb_options_row_hover .tb_row_hover__input' ),
				currentStyle = api.Models.Registry.lookup( this.$el.data('cid') ).get( 'styling' );

			options.each( function() {
				var $this = $( this ),
					optionName = $this.data( 'option' ),
					currentValue = currentStyle[optionName] || '';

				if( $this.hasClass( 'tb_row_hover__icon' ) ) {
					$this.find( '.selected' ).removeClass( 'selected' );
					$this.find( '[data-value="' + currentValue + '"]' ).addClass( 'selected' )

				} else {
					$this.val( currentValue );
				}
			} );
		},
		_expandOptions: function( e ) {
			e.preventDefault();

			var triggerButton = $( e.currentTarget ).parent().parent().find( '.tb_option_row' );
			triggerButton.length && triggerButton.trigger( 'click' );
		},
		_setPreviewOption: function( e ) {
			var $this = $( e.currentTarget ),
				value = ! $this.hasClass( 'tb_row_hover__icon' ) ? $this.val() : '',
				currentOption = $this.data( 'option' ),
				currentModel = this.model,
				currentStyle = currentModel.get( 'styling' ),
				newValue = {};

			if( e.type === 'click' ) {
				var selectedEl = $( e.target );

				$this.find( '.selected' ).removeClass( 'selected' );
				selectedEl.addClass( 'selected' );
				value = selectedEl.data( 'value' ) || '';
			}

			if( currentOption && currentStyle ) {
				newValue[currentOption] = value
				currentModel.set( { styling: $.extend({}, currentStyle, newValue) }, { silent: true } )

				if( currentOption === 'row_anchor' ) {
					this.$el.find( '.tb_row_anchor' ).text( value.replace( '#', '' ) )
				} else if( $this.hasClass( 'tb_row_hover__icon' ) && api.mode === 'visual' ) {
					api.activeModel = currentModel;
					api.liveStylingInstance.init( currentModel.get( 'styling' ) );
					Themify.body.trigger( 'tb_row_' + currentOption.replace('row_',''), value );
				}
			}
		}
    });

    api.Views.Builder = Backbone.View.extend({
        type: 'default',
        events: {
            'click .tb_import_layout_button': 'importLayoutButton'
        },
        initialize: function (options) {
            _.extend(this, _.pick(options, 'type'));
            api.vent.on('dom:builder:change', this.newRowAvailable.bind(this));
            api.vent.on('dom:builder:init', this.init.bind(this));
        },
        init: function (init) {
            api.init = init;
            this.rowSort();
            if (api.mode === 'visual') {
                api.Mixins.Builder.updateModuleSort(this.$el);
                if (init) {
                    this.initModuleVisualDrag('.tb_module');
                    this.initRowGridVisualDrag();
                }
                setTimeout(function () {
                    api.Utils._onResize(true);
                }, 1500);
            }
            else {
                api.Mixins.Builder.updateModuleSort(this.$el);
                if (init) {
                    api.Mixins.Builder.initModuleDraggable(api.toolbar.$el,'.tb_module');
                    api.Mixins.Builder.initModuleDraggable(api.toolbar.$el,'.tb_row_grid');
                }
            }
            var self = this;
            setTimeout(function () {
                api.Utils.setCompactMode(self.el.getElementsByClassName('module_column'));
            }, 1000);
            api.vent.trigger('dom:builder:change');
            setTimeout(self.insertLayoutButton.bind(self),200);
            api.init = true;
        },
        render: function () {

            var container = document.createDocumentFragment(),
                    rows = this.collection,
                    row = false;
            for (var i = 0, len = rows.length; i < len; ++i) {
                var rowView = api.Views.init_row(rows.models[i]);
                if (rowView !== false) {
                    if (api.id) {
                        row = rowView.model.get('row_order');
                        if (row === undefined) {
                            row = i;
                        }
                    }
                    if (api.id) {
                        api.VisualCache[rowView.model.cid] = 'themify_builder_' + api.id + '_row module_row_' + row;
                    }
                    container.appendChild(rowView.view.render(row,i).el);
                }
            }

            this.el.appendChild(container);
            api.Utils.columnDrag(false, false);
            return this;
        },
        newRowAvailable: function () {
            var row = this.$el.children('.module_row:last');
            if (row.length === 0 || row.find('.active_module').length > 0) {
                var rowDataPlainObject = {
                    cols: [{grid_class: 'col-full'}]
                },
                rowView = api.Views.init_row(rowDataPlainObject),
                el = rowView.view.render().$el;
                this.el.appendChild(el[0]);
                api.Mixins.Builder.updateModuleSort(el);
                if (api.mode === 'visual' && api.activeBreakPoint !== 'desktop') {
                    $('body', top_iframe).height(document.body.scrollHeight);
                }
            }
        },
        insertLayoutButton: function () {
            this.$el.find('.tb_import_layout_button').remove();
            if (this.$('.module_row').length < 2) {
                var cl = themifyBuilder.is_premium ? '' : ' tb_lite';
                this.el.insertAdjacentHTML('beforeend', '<a href="#" class="tb_import_layout_button' + cl + '">' + themifyBuilder.i18n.text_import_layout_button + '</a>');
            }
        },
        importLayoutButton: function (e) {
            e.preventDefault();
            api.Views.Toolbar.prototype.loadLayout(e);
        }
    });

    api.Mixins.Common = {
        styleData: {},
        doTheBinding: function ($this, val, context) {
            var logic = false,
                responsive = false,
                binding = $this.data('binding'),
                is_responsive = 'desktop' !== api.activeBreakPoint;
            if (!val && binding['empty'] !== undefined) {
                logic = binding['empty'];
            }
            else if (val && binding[val] !== undefined) {
                if ($this.attr('type') === 'radio') {
                    logic = $this.is(':checked') ? binding[val] : false;
                } else {
                    logic = binding[val];
                }
            }
            else if (val && binding['not_empty'] !== undefined) {
                logic = binding['not_empty'];
            }
            else if (binding['select'] !== undefined && val !== binding['select']['value']) {
                logic = binding['select'];
            }
            else if (binding['checked'] !== undefined && $this.is(':checked')) {
                logic = binding['checked'];
            }
            else if (binding['not_checked'] !== undefined && !$this.is(':checked')) {
                logic = binding['not_checked'];
            }
            if (binding['responsive'] !== undefined && $this.is('select') ) {
                responsive = binding['responsive'];
                if ( is_responsive && responsive['disabled'] !== undefined && this.styleData['breakpoint_desktop'][ $this.prop('id') ] !== undefined && _.contains( responsive['disabled'], this.styleData['breakpoint_desktop'][ $this.prop('id') ] ) ) {
                    logic = binding[ this.styleData.breakpoint_desktop[ $this.prop('id') ] ];
                }
            }
            if (logic) {
                var items = [];
                if (logic['show'] !== undefined) {
                    items = logic['show'];
                }
                if (logic['hide'] !== undefined) {
                    items = items.concat(logic['hide']);
                }
                if (context === undefined || context.length === 0) {
                    context = $('#tb_lightbox_container', top_iframe);
                }
                for (var i = 0, len = items.length; i < len; ++i) {
                    if (logic['hide'] !== undefined && logic['hide'][i] !== undefined) {
                        $('.' + logic['hide'][i], context).addClass('_tb_hide').hide();
                    }
                    if ( logic['show'] !== undefined && logic['show'][i] !== undefined) {
                        $('.' + logic['show'][i], context).removeClass('_tb_hide').show();
                    }
                }
                if (logic['responsive'] !== undefined) {
                    var items_disabled = [];
                    if (logic['responsive']['disabled'] !== undefined) {
                        items_disabled = items_disabled.concat(logic['responsive']['disabled']);
                    }

                    for (var i = 0, len = items_disabled.length; i < len; ++i) {
                        if (logic['responsive']['disabled'] !== undefined && logic['responsive']['disabled'][i] !== undefined) {
                            if ( is_responsive ) {
                                $('.' + logic['responsive']['disabled'][i], context).addClass('reponive_disable');
                            } else {
                                $('.' + logic['responsive']['disabled'][i], context).removeClass('reponive_disable');   
                            }
                        }
                    }
                }
            }
        },
        moduleOptionsBinding: function () {
            var $this = this;
            $(top_iframe).on('change', '#tb_lightbox_container [data-binding]', function () {
                $this.doTheBinding($(this), $.trim($(this).val()), $(this).closest('.tb_repeatable_field_content'));
            }).on('click', '.themify-layout-icon[data-binding] a', function () {
                $this.doTheBinding($(this).parent(), $(this).prop('id'), $(this).closest('.tb_repeatable_field_content'));
            });
        },
        mode_change: function (component) {
            var isNewModule = component === 'module' && api.activeModel.get('is_new') !== undefined,
                    item = isNewModule ? $('.tb_element_cid_' + api.activeModel.cid).closest('.module_row') : $('.tb_element_cid_' + api.activeModel.cid);
            api.beforeEvent = ThemifyBuilderCommon.clone(item);
            var self = this,
                    key = component === 'module' ? 'mod_settings' : 'styling',
                    stylefields = null;
            self.styleData = {};
            self.styleData['breakpoint_desktop'] = $.extend(true, {}, api.activeModel.get(key));
            for (var k in themifyBuilder.breakpoints) {
                if (self.styleData['breakpoint_desktop']['breakpoint_' + k] !== undefined) {
                    self.styleData['breakpoint_' + k] = self.styleData['breakpoint_desktop']['breakpoint_' + k];
                    self.styleData['breakpoint_desktop']['breakpoint_' + k]=null;
                }
            }

            function setData(breakpoint) {
                if (stylefields === null) {
                    stylefields = ThemifyBuilderCommon.Lightbox.$lightbox.find('#tb_options_styling')[0];
                }
                self.styleData['breakpoint_' + breakpoint] = api.Forms.serialize('tb_options_styling', self.styleData, breakpoint, false);
            }
            function changeCallback(e, prevbreakpoint, breakpoint) {
                setData(prevbreakpoint);
                self.editComponentCallback(stylefields, component, self.styleData, true);
            }

            Themify.body.off('themify_builder_change_mode', changeCallback)
                    .on('themify_builder_change_mode', changeCallback)
                    .one('themify_builder_lightbox_before_close', function () {
                        tempSettings = [];
                        if (api.saving) {
                            setData(api.activeBreakPoint);
                        }
                        Themify.body.off('themify_builder_change_mode', changeCallback);
                    });
        },
        editComponentCallback: function (response, component, settings, is_mod_change) {
            var key = 'styling',
                    self = api.Mixins.Common,
                    type = component,
                    editors = [],
                    rbuttons = [],
                    rcheckbox = [],
                    gradients = [],
                    ranges = [],
                    repeater = [],
                    binding = [],
                    isNewModule = false,
                    breakpoints = api.activeBreakPoint !== 'desktop' ? Object.keys(themifyBuilder.breakpoints).reverse() : false;
            if (component === 'module') {
                type = api.activeModel.get('mod_name');
                key = 'mod_settings';
                isNewModule = api.activeModel.get('is_new') !== undefined;
            }
            if (breakpoints !== false) {
                var index = breakpoints.indexOf(api.activeBreakPoint);
                for (var i = 0; i <= index; ++i) {
                    breakpoints.shift();
                }
                breakpoints.push('desktop');
            }

            var parseSettings = function (options, data, all_settings, repeat) {
                var id = '';
                if (!repeat) {
                    id = options.getAttribute('name');
                    if (!id) {
                        id = options.getAttribute('id');
                    }
                }
                else {
                    id = options.getAttribute('data-input-id');
                }
                var val = data && data[id] !== undefined ? data[id] : false;
                if (!is_mod_change && ((val === 'px' && !options.classList.contains('tb_frame_unit')) || val === 'pixels' || val==='n' ||  val === '|' || val === 'default' || val === 'solid') && !options.classList.contains('themify-layout-icon')) {
                    return;
                }
                var $this_option = $(options),
                        cl = options.classList;
                if (!isNewModule && breakpoints !== false && !repeat && !val) {
                    if (!cl.contains('themify-checkbox')) {
                        for (var j = 0, blen = breakpoints.length; j < blen; ++j) {
                            if (all_settings['breakpoint_' + breakpoints[j]] !== undefined && all_settings['breakpoint_' + breakpoints[j]][id] !== undefined) {
                                val = all_settings['breakpoint_' + breakpoints[j]][id];
                                break;
                            }
                            else if (breakpoints[j] === 'desktop' && all_settings[id] !== undefined) {
                                val = all_settings[id];
                                break;
                            }
                        }
                    }
                    else if (all_settings[id] !== undefined && $this_option.closest('#tb_options_styling').length === 0) {
                        val = all_settings[id];
                    }
                }
                if (cl.contains('themify-gradient')) {
                    gradients.push({'k': $this_option, 'v': val});
                    if (val) {
                        $this_option.val(val);
                    }
                }
                else if (cl.contains('tb_range')) {
                        if(val) {
                            $this_option.val(val);
                        }
                        ranges.push( $this_option );
                }
                else if (cl.contains('tb_uploader_input')) {

                    if (val) {
                        var img_thumb = $('<img/>', {src: val, width: 50, height: 50});
                        $this_option.val(val).parent().find('.img-placeholder').html(img_thumb);
                    }
                    else if (is_mod_change) {
                        $this_option.val('').parent().find('.img-placeholder').empty();
                    }
                }
                else if (cl.contains('themify-option-query-cat')) {
                    if (val) {
                        var parent = $this_option.parent(),
                                cat_val = val.split('|')[0];
                        parent.find('#' + id + '_dropdown').children("option[value='" + cat_val + "']").prop('selected', true);
                        parent.find('.query_category_multiple').val(cat_val);
                    }
                }
                else if (cl.contains('tb_radio_input_container')) {
                    var radio = null,
                            v = val ? val : ($this_option.data('default') !== undefined ? $this_option.data('default') : false);
                    if (v !== false && v !== '') {
                        radio = $this_option.find("input[value='" + v + "']");
                        if (radio.is(':disabled')) {
                            radio = null;
                        }
                        else {
                            radio.prop('checked', true);
                        }
                    }
                    else if (is_mod_change && cl.contains('tb_icon_radio')) {
                        $this_option.find('input').prop('checked', false);
                    }
                    if (radio === null) {
                        radio = $this_option.find('input:checked');
                    }
                    // has group element enable
                    if (radio.length > 0 && cl.contains('tb_option_radio_enable')) {
                        rbuttons.push(radio);
                    }
                }
                else if (cl.contains('themify-checkbox')) {
                    var cel = $this_option.find('.tb-checkbox');

                    if (!val && _.isEmpty(data)) {
                        val = cel.map(function () {
                            return ($(this).is(':checked')) ? $(this).val() : null;
                        }).get().join('|');
                    }

                    if (val) {
                        var cselected = val.constructor === Array ? val : val.split('|');
                        cel.each(function () {
                            if (cselected.indexOf($(this).val()) !== -1) {
                                $(this).prop('checked', true);
                            }
                        });
                    }
                    else if ((!isNewModule || is_mod_change)) {
                        var groupInput = cel.closest('.tb_input').find('.tb_seperate_items input');

                        if (groupInput.length) {
                            var hasValue = false;

                            groupInput.each(function () {
                                if ($(this).val().length) {
                                    hasValue = true;
                                    return false;
                                }
                            });

                            cel.prop('checked', !hasValue);

                        } else {
                            cel.prop('checked', false);
                        }
                    }
                    if (cl.contains('tb_option_checkbox_enable')) {
                        rcheckbox.push(cel);
                    }

                } else if (cl.contains('themify-layout-icon')) {
                    if (val) {
                        var icons = options.getElementsByClassName('tfl-icon');
                        for(var i=0,len=icons.length;i<len;++i){
                            if(val===icons[i].getAttribute('id')){
                                icons[i].classList.add('selected');
                            }
                            else{
                                icons[i].classList.remove('selected');
                            }
                        }
                        icons = null;
                    }
                    else {
                        var m_defaults = themifyBuilder.modules[ type ];
                        if (m_defaults !== undefined && m_defaults.defaults !== undefined && m_defaults.defaults[id]) {
                            $this_option.find('#' + m_defaults.defaults[id]).addClass('selected');
                        }
                        else {
                            $this_option.find('a').first().addClass('selected');
                        }
                    }
                }
                else if (options.tagName === 'SELECT') {

                    if (!is_mod_change  && (cl.contains('unstick_el_row_select_input') || cl.contains('unstick_el_module_select_input') )) {
                        var populateType = cl.contains('unstick_el_module_select_input') ? 'module' : 'row',
                            unstickOption = [],
                            selectedUID = api.activeModel.get('element_id'),
                            uidList = api.Utils.getUIDList( populateType );
                            for(var k=0,len2=uidList.length;k<len2;++k){
                                if(uidList[k].element_id !== selectedUID){
                                    var uidText = 'row' === uidList[k].elType ? 'Row #' + uidList[k].element_id : uidList[k].mod_name + ' #' + uidList[k].element_id;
                                    if ( 'row' === uidList[k].elType && uidList[k].styling && uidList[k].styling.custom_css_id ){
                                        uidText = '#' + uidList[k].styling.custom_css_id;
                                    }
                                    else if ( 'module' === uidList[k].elType && uidList[k].mod_settings && uidList[k].mod_settings.custom_css_id ){
                                        uidText = '#' + uidList[k].mod_settings.custom_css_id;
                                    }
                                    var s = uidList[k].element_id===val?'selected="selected" ':'';
                                    unstickOption.push('<option '+s+'value="'+ uidList[k].element_id +'">'+ uidText +'</option>');
                                }
                            }
                            uidList = null;
                            setTimeout(function(){
                                $this_option[0].innerHTML = unstickOption.join('');
                                unstickOption = null; 
                            },1500);
                    }
                    else{
                        if (val) {
                            if(type==='maps-pro' && id==='w_map_unit'){//temp fix swaping range values,can be removed later 06.07.2018
                                val =  data['unit_w']? data['unit_w']:'px';
                            }
                            else if (cl.contains('font-family-select')) {
                                $this_option.data('selected', val);
                            }
                            else if ( cl.contains('font-weight-select') ){
                                $this_option.attr( 'data_value',val );
                            }
                            $this_option.val(val);
                        }
                        else if (is_mod_change) {
                            $this_option.find('option').prop('selected', false);
                        }

                        if (is_mod_change && cl.contains('font-family-select')) {

                            if ($this_option.prop('tabindex') === -1) {
                                $this_option.trigger('change.select');
                            }
                            else if (val) {
                                var $optgroup = $this_option.find('optgroup');
                                if (ThemifyBuilderCommon.safe_fonts[val] !== undefined) {
                                    $optgroup.first().html('<option selected="selected" data-type="webfont" value="' + val + '">' + ThemifyBuilderCommon.safe_fonts[val] + '</option>');
                                }
                                else {
                                    $optgroup.last().html('<option selected="selected" value="' + val + '">' + ThemifyBuilderCommon.google_fonts[val] + '</option>');
                                }
                            }
                        }
                    }
                }
                else if (options.tagName === 'TEXTAREA' || options.tagName === 'INPUT') {
                    var is_textarea = options.tagName === 'TEXTAREA';
                    if (val || is_mod_change) {
                        if (is_mod_change && !val) {
                            val = '';
                        }
                        $this_option.val(val);
                        if (cl.contains('minicolors-input')) {
                            var color = val,
                                    opacity = '';
                            if (val.indexOf('_') !== -1) {
                                color = api.Utils.toRGBA(val);
                                val = val.split('_');
                                opacity = val[1];
                                if (!opacity) {
                                    opacity = 1;
                                }else if(0 >= parseFloat(opacity)){
                                    opacity = 0;
                                }
                                $this_option.val(val[0]);
                            }
                            else if (val) {
                                if (val.indexOf('#') === -1) {
                                    color = '#' + val;
                                }
                                opacity = 1;
                            }
                            $this_option.attr('data-opacity', opacity).next('.minicolors-swatch').find('span').css({'background': color, 'opacity': opacity}).closest('.minicolors').next('.color_opacity').val(opacity);
                        }
                        else if (is_textarea && !isNewModule && (cl.contains('tb_shortcode_input') || cl.contains('tb_thumbs_preview'))) {
                            self.getShortcodePreview($this_option, val);
                        }
                    }
                    if (is_textarea && cl.contains('tb_lb_wp_editor')) {
                        editors.push($this_option);
                    }
                }
                else if (cl.contains('tb_row_js_wrapper')) {
                    var row_append = val ? val.length - 1 : 0,
                            items,
                            e = $.Event('click', {isTrigger: true, currentTarget: $this_option.next('.add_new').find('a').first()});
                    if (api.cache.repeaterElements[id] === undefined) {
                        items = options.getElementsByClassName('tb_repeatable_field');
                        api.cache.repeaterElements[id] = $(items[0]).clone();
                    }
                    for (var j = 0; j < row_append; ++j) {
                        api.Forms.moduleOptAddRow(e, null);
                    }
                    items = options.getElementsByClassName('tb_repeatable_field');
                    for (var j = 0, clen = items.length; j < clen; ++j) {
                        var items_child = items[j].getElementsByClassName('tb_lb_option_child'),
                            opt_val = val[j] !== undefined ? val[j] : false;
                        for (var k = 0, n = items_child.length; k < n; ++k) {
                            parseSettings(items_child[k], opt_val, all_settings, true);
                        }
                    }
                    repeater.push({el: $this_option, binding_type: $this_option.data('control-binding')});
                }
                // Hide conditional inputs
                if ($this_option.data('binding')) {
                    binding.push({el: $this_option, 'v': val});
                }else if(cl.contains('themify-checkbox') && $this_option.find('.tb-checkbox').data('binding')){
                    binding.push({el: $this_option.find('.tb-checkbox'), 'v': val});
                }
                if (!is_mod_change && $this_option.data('control-binding') && !cl.contains('tb_lb_wp_editor') && !cl.contains('minicolors-input') && !cl.contains('themify-gradient') && 'repeater' !== $this_option.data('control-type')) {
                    api.Views.init_control($this_option.data('control-type'), {el: $this_option, binding_type: $this_option.data('control-binding'), selector: $this_option.data('live-selector')});
                }
            };
            var all_settings = is_mod_change ? settings : api.activeModel.get(key),
                    el_settings = $.extend(true, {}, all_settings);
            if (api.activeBreakPoint !== 'desktop' || is_mod_change) {//closest styles
                if (el_settings['breakpoint_' + api.activeBreakPoint] !== undefined) {
                    el_settings = el_settings['breakpoint_' + api.activeBreakPoint];
                }
                else {
                    for (var j = 0, blen = breakpoints.length; j < blen; ++j) {
                        if (el_settings['breakpoint_' + breakpoints[j]] !== undefined) {
                            el_settings = el_settings['breakpoint_' + breakpoints[j]];
                            break;
                        }
                    }
                }
            }
            function once(e){
                api.hasChanged = true;
                for (var j = 0,len=options.length; j < len; ++j) {
                    options[j].removeEventListener('change', once);
                }
                options = null;
            }
			function imageUrlCheck(e) {
                var val = $( this ).val();
				if ( val != '' ) {
					if ( ThemifyBuilderCommon.isImageUrl(val) ) {
						$( this ).removeClass('tb_image_field_error');
					} else {
						$(this).addClass('tb_image_field_error');
						alert(themifyBuilder.i18n.incorrectImageURL);
					}
				} else {
					$( this ).removeClass('tb_image_field_error');
				}
            }
			
				$(response.querySelectorAll('.tb_uploader_input:not(#background_video):not([data-control-type="audio"])')).on('focus', function (event) {
					if (event.originalEvent !== undefined) {
						$(this).one('blur', imageUrlCheck);
					}
				}).on("change paste keyup", function() {
					$( this ).removeClass('tb_image_field_error');
				});
			
            var options = response.getElementsByClassName('tb_lb_option');
			
            for (var i = 0, len = options.length; i < len; ++i) {
                parseSettings(options[i], el_settings, all_settings, false);
                if(!is_mod_change){
                    options[i].addEventListener('change',once,{once:true});
                }
            }
            if(is_mod_change){
                options = null;
            }
            setTimeout(function () {
                for (var i = 0, len = rbuttons.length; i < len; ++i) {
                    ThemifyBuilderCommon.Lightbox.clickRadioOption(null, rbuttons[i]);
                }
                rbuttons = null;
                for (var i = 0, len = rcheckbox.length; i < len; ++i) {
                    ThemifyBuilderCommon.Lightbox.clickCheckboxOption(null, rcheckbox[i]);
                }
                rcheckbox = null;
            }, 1);
            if (!is_mod_change) {
                if (repeater.length > 0) {
                    setTimeout(function () {
                        for (var i = 0, len = repeater.length; i < len; ++i) {
                            api.Views.init_control('repeater', repeater[i]);
                        }
                        repeater = null;

                    }, 200);
                }
                setTimeout(function () {
                    binding = binding.reverse();
                    for (var i = 0, len = binding.length; i < len; ++i) {
                        self.doTheBinding(binding[i].el, binding[i].v);
                    }
                    binding = null;
                }, 1);
                setTimeout(function () {
                        for (var i = 0, len = ranges.length; i < len; ++i) {
                            api.Utils.createRange( ranges[i] );
                        }
                        ranges = null;
                }, 100);
            }
            setTimeout(function () {
                self.applyAll_init(is_mod_change);
                var b = response.getElementsByClassName('border_style');
                for (var i = 0, len = b.length; i < len; ++i) {
                    ThemifyBuilderCommon.Lightbox.hideShowBorder(null, $(b[i]));
                }
                b = null;
            }, 10);
            if ($.fn.ThemifyGradient !== undefined) {
               
                setTimeout(function () {
                    for (var i = 0, len = gradients.length; i < len; ++i) {
                        api.Utils.createGradientPicker(gradients[i].k, gradients[i].v, is_mod_change);
                    }
                    gradients = null;
                }, 400);
            }
            if (!is_mod_change) {
                setTimeout(function () {
                    ThemifyBuilderCommon.fontPreview(ThemifyBuilderCommon.Lightbox.$lightbox.find('#tb_lightbox_container'));
                }, 300);
                if (isNewModule && 'gallery' === type) {
                    setTimeout(function () {
                        $('.tb_gallery_btn', response).trigger('click');
                    }, 1);
                }

                setTimeout(function () {
                    // plupload init
                    api.Utils.builderPlupload('normal');
                }, 120);
                // colorpicker
                setTimeout(function () {
                    api.Utils.setColorPicker(response);
                }, 1);
                // Image Select
                if(component !== 'module'){
                    setTimeout(function () {
                        api.Utils.setImageSelect(response);
                    }, 800);
                }
                else{
                    // datepicker
                    setTimeout(function () {
                        api.Utils.datePicker(response);
                    }, 100);
                }
                if ('visual' === api.mode) {
                    setTimeout(function () {
                        tempSettings = component === 'module' ? api.Forms.serialize('tb_options_setting') : el_settings;//cache exclude styling
                        api.liveStylingInstance.init(tempSettings);
                    }, 1);
                    if (!isNewModule) {
                        ThemifyBuilderCommon.Lightbox.rememberRow();
                    }
                }

                setTimeout(function () {
                    if (editors.length > 0) {
                        var initEditor = function () {
                            for (var i = 0, len = editors.length; i < len; ++i) {
                                
                                api.Views.init_control('wp_editor', {el: editors[i], binding_type: editors[i].data('control-binding'), selector: editors[i].data('live-selector')});
                            }
                            editors = null;
                        };
                        if (!api.activeModel.get('styleClicked') && !api.activeModel.get('visibileClicked')) {
                            initEditor();
                        }
                        else {
                            Themify.body.on('themify_builder_tabsactive', function editTab(e, id, content) {
                                if (id === '#tb_options_setting') {
                                    initEditor();
                                    Themify.body.off('themify_builder_tabsactive', editTab);
                                }
                            });
                        }
                    }
                    self.mode_change(component);
                }, 1);
                // Trigger event
                Themify.body.trigger('editing_' + component + '_option', [type, el_settings, response]);
                if (api.mode === 'visual') {
                    // Trigger parent iframe
                    window.top.jQuery('body').trigger('editing_' + component + '_option', [type, el_settings, response]);
                 
                }
            }
        },
        // "Apply all" // apply all init
        applyAll_init: function (is_mod_change) {
            var items = $('.style_apply_all', top_iframe);
			items.off('change.tb_apply_all').on('change.tb_apply_all', function (e) {
                $(this).parent().css('display', 'inline-block');
				var parent = $(this).closest('.tb_input').find('.tb_seperate_items'),
					items = parent.find('li'),
					init = !e.isTrigger, 
                    has_radio_options = $(this).hasClass('tb_has_radio_options') ? true : false;
                if (has_radio_options) { var radio_items = $(this).siblings('input:radio'); }               
				if ($(this).is(':checked')) {
                    if (has_radio_options) { radio_items.addClass('_tb_disable'); }
					if (init) {
                        items.not(':first-child').hide();
                        items.first().find('span.tb_tooltip_up').hide();
					}
					else {//works faster
						items.not(':first-child').hide();
                        items.first().find('span.tb_tooltip_up').hide();
					}
                    items.first().find('span.tb_range_after').hide();
					parent.attr('data-checked', 1);
				}
				else {
                    if (has_radio_options) { radio_items.removeClass('_tb_disable'); }
					items.show();
                    items.first().find('span.tb_range_after').show();
                    items.first().find('span.tb_tooltip_up').show();
                    parent.removeAttr('data-checked');
                    if(has_radio_options) {
                        items.first().find('select').trigger('change');
                    }
				}
				if (init) {
					items.first().find('select').trigger('change');
				}
            });
            items.each(function() {
                $(this).parent().css('display', 'inline-block');
                var has_radio_options = $(this).hasClass('tb_has_radio_options') ? true : false;
                    if(has_radio_options) {
                        var radio_items = $(this).siblings('input:radio');
                        var item = this;
                        radio_items.on('click', function(e) {
                            $(item).trigger('change.tb_apply_all');
                        });
                    }
            });
            if (!is_mod_change) {
                items = items.filter(':checked');
            }
            items.trigger('change.tb_apply_all');
        },
        getShortcodePreview: function ($input, value) {
            var self = this;
            if (self.galerry_cache === undefined) {
                self.galerry_cache = {};
            }
            function callback(data) {
                $input.next('.tb_shortcode_preview').remove();
                if (data) {
                    $input.after(data);
                }
            }
            if (self.galerry_cache[value] !== undefined) {
                callback(self.galerry_cache[value]);
                return;
            }
            $.ajax({
                type: 'POST',
                url: themifyBuilder.ajaxurl,
                data:
                        {
                            action: 'tb_load_shortcode_preview',
                            tb_load_nonce: themifyBuilder.tb_load_nonce,
                            shortcode: value
                        },
                success: function (data) {
                    callback(data);
                    self.galerry_cache[value] = data;
                }
            });
        }
    };

    api.Mixins.Builder = {
        before:null,
        zindex:null,
        r:null,
        w:null,
        h:null,
        type:null,
        moduleHolderArgs:null,
        subrows:null,
        update: function (el) {
            var type = api.activeModel !== null ? api.activeModel.get('elType') : api.Models.Registry.lookup(el.data('cid')).get('elType');
            if (api.mode === 'visual') {
                api.Utils.loadContentJs(el, type);
            }
           // api.Mixins.Builder.columnSort(el);
            var row = el.closest('.module_row');
            api.Utils.columnDrag(row.find('.row_inner'), false);
            api.Utils.columnDrag(row.find('.subrow_inner'), false);
            api.Mixins.Builder.updateModuleSort(row);
        },
        dragScroll: function (type,off) {
            var body = $('body', top_iframe);
            if (api.mode === 'visual') {
                body = body.add(Themify.body);
            }
            if (this.top === undefined) {
                this.top = api.toolbar.$el;
                this.top = this.top.add($('#tb_fixed_bottom_scroll', top_iframe));
                if (api.mode !== 'visual') {
                    this.top = this.top.add('#wpadminbar');
                }
            }
            if (off === true) {
                this.top.off('mouseenter');
                if(type==='row' && api.mode === 'visual'){
                    api.toolbar.$el.find('.tb_zoom[data-zoom="100"]').trigger('click');;
                }
                body.removeClass('tb_drag_start tb_drag_'+type);
                return;
            }
            var scrollEl = api.activeBreakPoint === 'desktop' ? $('body,html') : $('body,html', top_iframe),
                bh,
                wh = 0;
            if (api.mode === 'visual') {
                bh = scrollEl.height();
                wh = $(window.top).height();
            }
            else {
                bh = $('#page-builder').height();
            }
            var part = (bh - wh);
            if(part>10000){
               part = 2; 
            }
            else if(part>5000){
                part = 5;
            }
            else{
                part = 10;
            }
            var step = ((bh - wh)*part)/100;
            function onDragScroll(e) {
                var id = $(this).prop('id'),
                    scroll = id === 'tb_toolbar' || id === 'wpadminbar' ? '-' : '+';
                scroll += '=' + step + 'px';
                scrollEl.stop().animate({
                    scrollTop: scroll
                },
                800);
               
            }
            body.addClass('tb_drag_start tb_drag_'+type);
            if(type==='row' && api.mode === 'visual'){
                api.toolbar.$el.find('.tb_zoom[data-zoom="50"]').trigger('click');
            }
            if (step > 0) {
                this.top.off('mouseenter').on('mouseenter', onDragScroll);
            }
        },
        rowSort: function () {
            var self = this,
                before_next,
            rowSortable = {
                        items:'>.module_row',
                        handle: '.tb_row_actions>.tb_grid_icon',
                        axis: 'y',
                        placeholder: 'tb_state_highlight',
                        containment: 'parent',
                        tolerance: 'pointer',
                        forceHelperSize: true,
                        forcePlaceholderSize: true,
                        scroll: false,
                        beforeStart: function (e,el, ui) {
                            if(!self.before){
                                before_next = true;
                                self.before = ui.item.next('.module_row');
                                if(self.before.length===0){
                                    self.before = ui.item.prev('.module_row');
                                    before_next = false;
                                }
                                self.before = self.before.data('cid');
                                self.dragScroll('row');
                            }
                        },
                        stop: function (e, ui) {
                            self.before  = before_next = null;
                            self.dragScroll('row',true);
                        },
                        update: function (e, ui) {
                            if(api.mode==='visual' && !ui.item.hasClass('tb_row_grid')){
                                var body = api.activeBreakPoint === 'desktop' ? $('html,body'): $('body', top_iframe);
                                body.scrollTop(ui.item.offset().top);
                                body = null;
                            }
                            if(e.type==='sortupdate' && self.before){
                                api.hasChanged = true;
                                var after = ui.item.next('.module_row'),
                                    after_next = true;
                                if(after.length===0){
                                    after = ui.item.prev('.module_row');
                                    before_next = after_next = false;
                                }
                                after = after.data('cid');
                                api.undoManager.push(ui.item.data('cid'), null,null,'row_sort',{bnext:before_next,'before':self.before,'anext':after_next,'after':after});
                            }
                            else if (ui.item.hasClass('predesigned_row') || ui.item.hasClass('tb_page_break_module') || ui.item.data('type') === 'row') {
                                if (ui.item.data('type') === 'row') {
                                    api.toolbar.libraryItems.get(ui.item.data('id'),'row',function($row){
                                        if (!Array.isArray($row)) { 
                                            $row = new Array($row);
                                        }
                                        self.rowDrop($row, ui.item);
                                    });
                                } else if (ui.item.hasClass('tb_page_break_module')) {
                                    api.toolbar.pageBreakModule.get(function (data) {
                                        self.rowDrop(data, ui.item);
                                    });
                                }
                                else{
                                    api.toolbar.preDesignedRows.get(ui.item.data('slug'),function(data){
                                         self.rowDrop(data, ui.item);
                                    });
                                }
                            }
                            else if(ui.item.hasClass('tb_row_grid')){
                                self.subRowDrop(ui.item.data('slug'), ui.item);
                            }
                            api.toolbar.pageBreakModule.countModules();
                        }
                    };
            if ('visual' === api.mode) {
                rowSortable.helper = function () {
                    return $('<div class="tb_sortable_helper"/>');
                };
            }
            this.$el.sortable(rowSortable);
            //this.columnSort(this.$el);
        },
        columnSort: function (el) {
            var before,
            colums;
            el.find('.row_inner, .subrow_inner').sortable({
                items: '> .module_column',
                handle: '> .tb_column_action .tb_column_dragger',
                axis: 'x',
                placeholder: 'tb_state_highlight',
                tolerance: 'pointer',
                cursorAt: {
                    top: 20,
                    left: 20
                },
                beforeStart: function (e,el, ui) {
                    Themify.body.addClass('tb_drag_start');
                    before = ThemifyBuilderCommon.clone(ui.item.closest('.module_row'));
                    colums = ui.item.siblings();
                    colums.css('marginLeft', 0);
                },
                start: function (e, ui) {
                    $('.tb_state_highlight').width(ui.item.width());
                },
                stop: function (e, ui) {
                    Themify.body.removeClass('tb_drag_start');
                    colums.css('marginLeft', '');
                },
                update: function (e, ui) {
                    var inner = ui.item.closest('.ui-sortable'),
                            children = inner.children('.module_column');
                    children.removeClass('first last');
                    if (inner.hasClass('direction-rtl')) {
                        children.last().addClass('first');
                        children.first().addClass('last');
                    }
                    else {
                        children.first().addClass('first');
                        children.last().addClass('last');
                    }
                    api.Utils.columnDrag(inner, false);
                    api.Utils.setCompactMode(children);
                    var row = inner.closest('.module_row');
                    api.undoManager.push(  row.data('cid'), before, row, 'row');
                }
            });
        },
        updateModuleSort: function (context, disable) { 
            var items = $('.tb_holder',context);
            if (disable) {
                items.sortable(disable);
                return false;
            }
            items.each(function () {
                $(this).data({uiSortable:null,sortable:null});
            });
            var self = this,
                children=null,
                helpHeight,
                holder;
            this.moduleHolderArgs = {
                placeholder: 'tb_state_highlight',
                items: '.active_module,>div>.active_module',
                connectWith: '.module_row:not(.tb-page-break) .tb_holder',
                revert: 100,
                scroll: false,
                cancel: '.tb_disable_sorting',
                cursorAt:{
                    top: 10,
                    left:90
                },
                sort:function( e, ui ) {//workaround to fix nested elements intersection, ui tolerance doesn't work as excerpted
                    if(children===null){
                        children = this.classList.contains('active_module')?this.children:$(this).find($(this).sortable( 'option' ).items).get();
                        holder = $( this ).children( '.tb_state_highlight:first' );
                        helpHeight  = ui.helper.outerHeight();
                    }
                    var helpTop     = ui.position.top,
                        helpBottom  = helpTop + helpHeight;
                    for(var i=0,len=children.length;i<len;++i){
                        if(!children[i].classList.contains('tb_sortable_helper') && !children[i].classList.contains('tb_state_highlight')){
                            var item = $(children[i]),
                                itemHeight = item.outerHeight(),
                                itemTop    = item.position().top,
                                itemBottom = itemTop + itemHeight;
                                if( helpTop > itemTop  &&  helpTop < itemBottom ) {
                                        var tolerance = Math.min( self.helpHeight, itemHeight ) / 2,
                                            distance  = helpTop - itemTop;
                                        if( distance < tolerance ) {
                                            holder.insertBefore( item );
                                            $( this ).sortable( 'refreshPositions' );
                                            break;
                                        }

                                } else if( helpBottom < itemBottom  &&  helpBottom > itemTop ) {
                                        var tolerance = Math.min( self.helpHeight, itemHeight ) / 2,
                                            distance  = itemBottom - helpBottom;

                                        if( distance < tolerance ) {
                                            holder.insertAfter( item );
                                            $( this ).sortable( 'refreshPositions' );
                                            break;
                                        }
                                }
                        }
                    }
                },
                beforeStart: function (e,el, ui) { 
                    if (!self.before) {
                        self.r = ui.item.closest('.module_row');
                        if(self.r.length>0){
                            self.before = ThemifyBuilderCommon.clone(self.r);
                            self.zindex = self.r.css('zIndex');
                            if(self.zindex==='auto'){
                                self.zindex = '';
                            }
                            self.r.css('zIndex',2);
                        }
                        else{
                            self.r = null;
                        }
                        self.w = ui.item[0].style['width'];
                        self.h = ui.item[0].style['height'];
                        ui.item.css({width:180,height:30});//need for get helper size on the dragging
                        self.type = 'module';
                        self.subrows = null;
                        if(ui.item[0].classList.contains('module_subrow')){
                             self.type+=' tb_drag_subrow';
                             self.subrows = true;
                        }
                        else if(ui.item[0].classList.contains('tb_row_grid')){
                            self.type ='column';
                            self.subrows = true;
                        }
                        self.dragScroll(self.type);
                        if(self.subrows===true){
                            self.subrows = [];
                            var tmp = [];
                            for(var i=0,len=el.items.length;i<len;++i){
                                if(!el.items[i].instance.element[0].classList.contains('tb_subrow_holder')){
                                    tmp.push(el.items[i]);
                                }
                                else{
                                    self.subrows.push(el.items[i].instance.element);
                                    el.items[i].instance.destroy();
                                }
                            }
                            el.items = tmp;
                            tmp = [];
                            for(var i=0,len=el.containers.length;i<len;++i){
                                if(!el.containers[i].element[0].classList.contains('tb_subrow_holder')){
                                    tmp.push(el.containers[i]);
                                }
                                else{
                                    self.subrows.push(el.containers[i].element);
                                    el.containers[i].destroy();
                                }
                            }
                            el.containers = tmp; 
                            tmp = null;
                        }
                    }
                },
                stop: function (e, ui) { 
                    if ('visual' === api.mode && ui.helper) {
                        $(ui.helper).remove();
                    }
                    ui.item.css({width:self.w,height:self.h});
                    self.dragScroll(self.type,true);
                    if(self.r){
                        self.r.css('zIndex',self.zindex);
                    }
                    self.before = self.w = self.h = children = holder = self.r = self.zindex  = helpHeight=self.type = null;
                    if(self.subrows){
                        for(var i=0,len=self.subrows.length;i<len;++i){
                            if(!self.subrows[i][0].classList.contains('ui-sortable')){
                                self.subrows[i].sortable(self.moduleHolderArgs);
                            }
                        }
                    }
                    self.subrows = null;
                },
                update: function (e, ui) {
                    ui.item.css({width:self.w,height:self.h});
                    if (ui.item.hasClass('tb_module_dragging_helper')) {
                        tempSettings = [];
                        var item =  $(ui.item.clone(false));
                        if(ui.item.data('id')){
                            var r = ui.item.closest('.module_row');
                            if(r.length>0){
                                self.before = ThemifyBuilderCommon.clone(r);
                                self.before.find('.tb_module_dragging_helper').remove();
                            }
                            r = null;
                        }
                        ui.item.after(item);
                        self.moduleDrop(item,null, self.before);
                    }
                    else {
                        if(ui.sender){
                            var row = ui.sender.closest('.module_row');
                            ui.sender.closest('.module_row').toggleClass('tb_row_empty',row.find('.active_module').length===0);
                            row = null;
                            var sub = ui.sender.closest('.module_subrow');
                            if(sub.length>0){
                               sub.toggleClass('tb_row_empty',sub.find('.active_module').length===0); 
                            }
                            sub = null;
                            // Make sub_row only can nested one level
                           if (ui.item.hasClass('module_subrow') && ui.item.parent().closest('.module_subrow').length > 0) {
                               items.sortable('cancel');
                               return;
                           }
                        }
                        if (self.before) {
                            api.hasChanged = true;
                            if(!ui.item.hasClass('module_subrow')){
                                ui.item.closest('.module_subrow').removeClass('tb_row_empty');
                            }
                            var moved_row = ui.item.closest('.module_row');
                            moved_row.removeClass('tb_row_empty');
                            api.undoManager.push(ui.item.data('cid'),self.before , moved_row, 'sort', {'before':self.before.data('cid'), 'after': moved_row.data('cid')});
                            self.before =null;
                            api.vent.trigger('dom:builder:change');
                            Themify.body.trigger('tb_' + self.type + '_sort', [ui.item]);
                        }
                     }
                     
                }
            };
            if ('visual' === api.mode) {
                this.moduleHolderArgs.helper = function () {
                    return $('<div class="tb_sortable_helper"/>');
                };
            }
            items.sortable(this.moduleHolderArgs);
        },
        initModuleDraggable: function (parent,cl) {
            var self = this,
                args = $.extend(true,{},this.moduleHolderArgs);
            args['update'] = false;
            args['appendTo'] = document.body;
            args['items'] = cl;
             if(cl==='.tb_row_grid'){
                args['connectWith'] = [args['connectWith'],(api.mode==='visual'?'#themify_builder_content-'+themifyBuilder.post_ID:'#tb_row_wrapper')];
            }
            args['stop'] = function (e, ui) { 
                $(this).sortable('cancel');
                ui.item.removeClass('tb_sortable_helper tb_module_dragging_helper');
                self.moduleHolderArgs.stop(e,ui);
            };
            args['start'] = function (e,ui) {
                ui.item.addClass('tb_sortable_helper tb_module_dragging_helper');
            };
            args['helper'] = function (e,ui) {
                return $('<div class="tb_sortable_helper tb_module_dragging_helper">'+ui.text()+'</div>');
            };
            parent.sortable(args);
        },
        initRowDraggable: function (parent,cl) {
            var self = this;
            parent.find(cl).draggable({
                    appendTo:Themify.body,
                    helper: 'clone',
                    revert: 'invalid',
                    connectToSortable:api.mode==='visual'?'#themify_builder_content-'+themifyBuilder.post_ID:'#tb_row_wrapper',
                    cursorAt: {
                        top: 10,
                        left: 40
                    },
                    start: function (e, ui) {
                        self.dragScroll('row');
                        ui.helper.addClass('tb_module_dragging_helper tb_sortable_helper').find('.tb_predesigned_rows_list_image').remove();
                    },
                    stop: function (e, ui) {
                        self.dragScroll('row',true);
                    }
            });
        },
        initModuleVisualDrag: function (cl) {
            var self = this;
            api.toolbar.$el.find(cl).ThemifyDraggable({
                iframe: '#tb_iframe',
                dropitems: '.tb_holder',
                elements: '.active_module',
                type:'module',
                onDrop: function (e, drag, drop) {
                    self.moduleDrop(drag, false,ThemifyBuilderCommon.clone(drop.closest('.module_row')));
                }
            });
        },
        initRowGridVisualDrag: function () {
                var self = this;
                api.toolbar.$el.find('.tb_row_grid').ThemifyDraggable({
                        iframe: '#tb_iframe',
                        dropitems: ".tb_holder:not('.tb_subrow_holder'),.themify_builder_content:not('.not_editable_builder')>.module_row",
                        elements: '.active_module',
                        cancel:'.tb_subrow_holder',
                        append:false,
                        type:'column',
                        onDrop: function (e, drag, drop) {
                            self.subRowDrop(drag.data('slug'), drag);
                        }
                });
        },
        initRowVisualDrag: function (cl) {
            var self = this;
            api.toolbar.$el.find(cl).ThemifyDraggable({
                iframe: '#tb_iframe',
                dropitems: ".themify_builder_content:not('.not_editable_builder')>.module_row",
                append: false,
                type:'row',
                onDrop: function (e, drag, drop) {
                    drag.addClass('tb_state_highlight').find('.tb_predesigned_rows_list_image').remove();
                    drag.show();
                    var body = api.activeBreakPoint === 'desktop' ? $('html,body'): $('body', top_iframe);
                    body.scrollTop(drag.offset().top-$('#headerwrap.fixed-header').outerHeight());
                    body = null;
                    if(drag.data('type') === 'row'){
                        api.toolbar.libraryItems.get(drag.data('id'),'row',function($row){
                            if (!Array.isArray($row)) { 
                                $row = new Array($row);
                            }
                            self.rowDrop($row, drag);
                        });
                    } else if(drag.hasClass('tb_page_break_module')) {
                        api.toolbar.pageBreakModule.get(function (data) {
                            self.rowDrop(data, drag);
                        });
                    } else {
                        api.toolbar.preDesignedRows.get(drag.data('slug'),function(data){
                            self.rowDrop(data, drag);
                        });
                    }
                }
            });
        },
        subRowDrop: function( data, drag ){
                var is_row = drag.parent('.themify_builder_content,#tb_row_wrapper').length>0;
                if(is_row ||  drag.closest('.sub_column').length===0 ){
                        data = api.Utils.grid(data);
                        var before,
                            type,
                            is_next;
                        if(!is_row){
                            before = ThemifyBuilderCommon.clone(drag.closest('.module_row'));
                            before.find('.tb_row_grid').remove();
                            type = 'row';
                        }
                        var row = is_row?api.Views.init_row({cols:data[0].cols}):api.Views.init_subrow({cols:data[0].cols}),
                            el = row.view.render().$el;
                        if(is_row ||drag.parent('.tb_holder').length>0){
                            drag[0].parentNode.replaceChild(el[0], drag[0]);
                        }
                        else{
                            var holder = drag.next('.tb_holder');
                            if(holder.length>0){
                                   holder.prepend(el); 
                            }
                            else{  
                                holder = drag.prev('.tb_holder');
                                holder.append(el); 
                            }
                        }
                        
                        if(is_row){
                            before = el.next('.module_row'); 
                            is_next = true;
                            if(before.length===0){
                                is_next = false;
                                before = el.prev('.module_row');
                            }
                            before = before.data('cid');
                            type = 'grid_sort';
                        }
                        el.find('.tb_grid_'+api.activeBreakPoint+' .tb_grid_list [data-col="'+ data[0].cols.length +'"]').first().parent().addClass('selected');
                        api.Utils.setCompactMode(el[0].getElementsByClassName('module_column'));
                        api.Mixins.Builder.update(el);
                        drag.remove();
                        api.hasChanged = true;
                        var after = el.closest('.module_row');
                        if(!is_row){
                            after.removeClass('tb_row_empty');
                        }
                        after.find('.tb_row_grid').remove();
                        api.undoManager.push(after.data('cid'), before, after, type,{next:is_next});
                }
                else{
                    drag.remove();
                }
        },
        rowDrop: function (data, drag) {
            function callback() {
                var prev_row_id = drag.prev('.module_row'),
                    bid;
                if(prev_row_id.length === 0){
                    bid = api.mode==='visual'?drag.closest('.themify_builder_content').data('postid'):null;
                    prev_row_id = false;
                }
                else{
                    prev_row_id = prev_row_id.data('cid');;
                }
                drag[0].innerHTML = '';
                drag[0].parentNode.replaceChild(fragment, drag[0]);
                api.hasChanged = true;
                api.undoManager.push('', '', '', 'predesign', {'prev': prev_row_id, 'rows': rows, 'bid': bid});
                for (var i = 0, len = rows.length; i < len; ++i) {
                    var col = rows[i].find('.module_column');
                    rows[i].find('ul.tb_grid_list li a[data-col="'+ col.length +'"]').first().parent().addClass('selected');
                    api.Utils.setCompactMode(col);
                    api.Mixins.Builder.update(rows[i]);
                }
                ThemifyBuilderCommon.showLoader('hide');
            }
            var checkEmpty = function (cols){
                for(var i in cols){
                    if((cols[i].styling && Object.keys(cols[i].styling).length > 0) || (cols[i].modules && Object.keys(cols[i].modules).length > 0)){
                        return true;
                    }
                }
                return false;
            },
            fragment = document.createDocumentFragment(),
                rows = [],
                styles = [];
                 for (var i = 0, len = data.length; i < len; ++i) {
                    if ((data[i].styling && Object.keys(data[i].styling).length > 0) || (data[i].cols && checkEmpty(data[i].cols))) {
                        var row = api.Views.init_row(data[i]);
                        if (row !== false) {
                            var r = row.view.render();
                            fragment.appendChild(r.el);
                            if(api.mode==='visual'){
                                var items = r.el.querySelectorAll('[data-cid]');
                                styles[r.el.dataset.cid] = 1;   
                                for(var i=0,len=items.length;i<len;++i){
                                    styles[items[i].dataset.cid] = 1;
                                }
                            }
                            rows.push(r.$el);
                        }
                    }
                }
                if (api.mode === 'visual') {
                    api.bootstrap(styles, callback);
                    styles = null;
                }
                else {
                    callback();
                }
        },
        moduleDrop: function (drag, drop,before) {
            var self = this;
            if( drag[0].classList.contains('tb_row_grid') ){
                self.subRowDrop(drag.data('slug'), drag);
                return;
            }
            var options = {mod_name: drag.data('module-slug')},
                type = drag.data('type'),
                is_library = type === 'part' || type === 'module';
                if(is_library){
                    api.toolbar.libraryItems.get(drag.data('id'),type,callback);
                  
                }
                else{
                    callback(options);
                }
                function callback(options){
                    var moduleView = api.Views.init_module(options),
                        module = moduleView.view.render();
                        function final(new_module){
                            if(!is_library){
                                moduleView.model.set({is_new: 1}, {silent: true});
                            }
                            var settings = new_module === true ? moduleView.model.getPreviewSettings() : moduleView.model.get('mod_settings');
                         
                            if (drop) {
                                drop.append(module.el);
                            }
                            else {
                                drag.replaceWith(module.el);   
                            }
                            if(is_library){
                                api.activeModel = moduleView.model;
                            }
                            else{
                                moduleView.view.trigger('edit', null);
                            }
                            api.hasChanged = true;
                            if (api.mode === 'visual' && Object.keys(settings).length > 1) {                               
                                if (type === 'part' || drag.data('type') === 'ajax') {
                                    var pComponent_added = true;
                                    moduleView.model.trigger('custom:preview:refresh', settings);
                                }
                                else if(type!=='module'){
                                    moduleView.model.trigger('custom:preview:live', settings);
                                }
                            }
                            if (is_library) {
                                    if (pComponent_added) {
                                            var pComponent = moduleView.view.$el.find('.tb_preview_component').detach();
                                            setTimeout(function(){
                                                    moduleView.view.$el.prepend(pComponent);
                                            },50);
                                    }
                                    if(before){
                                        var after = module.$el.closest('.module_row');
                                        after.removeClass('tb_row_empty').find('.tb_module_dragging_helper').remove();
                                        module.$el.closest('.module_subrow').removeClass('tb_row_empty');
                                        api.undoManager.push(after.data('cid'), before, after, 'row');
                                        api.vent.trigger('dom:builder:change');
                                        $('.tb_import_layout_button').remove();
                                    }
                            }
                        }
                        if (api.mode === 'visual' && is_library) {
                                var dataa = new Array();
                                dataa[moduleView.model.cid] = 1;
                                api.bootstrap(dataa, final);
                        } else {
                                final(true);
                        }
                }
			// Add WP editor placeholder
			if( api.mode !== 'visual' && $( '.themify-wp-editor-holder:not(.themify-active-holder)' ).length ) {
				$( '.themify-wp-editor-holder' ).addClass( 'themify-active-holder' );
			}

        },
        toJSON: function () {
            var option_data = {},
            rows = this.el.getElementsByClassName('module_row'),
            j = 0;
            for (var i = 0, len = rows.length; i < len; ++i) {
                var data = api.Utils._getRowSettings(rows[i], i);
                if(Object.keys(data).length > 0){
                    option_data[j] = data;
                    ++j;
            }
            }
            return option_data;
        }
    };

    api.Forms = {
        Data: {},
        Validators: {},
        bindEvents: function () {
            var actionEvent = 'true' === themifyBuilder.isTouch ? 'touchend' : 'click';
            $('body', top_iframe)
                    .on(actionEvent, '.builder_save_button', this.saveComponent)

                    .on(actionEvent, '#tb_lightbox_parent .add_new a', this.moduleOptAddRow)
                    .on(actionEvent, '#tb_submit_import_form', this.builderImportSubmit)
                    .on(actionEvent, '.tb_lightbox_switcher a', this.lightbox_switcher)
                    
                    /* Layout Action */
                    .on(actionEvent, '.layout_preview img', this.templateSelected)
                    .on(actionEvent, '#builder_submit_layout_form', this.saveAsLayout)
					/* library Items Action*/
                    .on(actionEvent, '#builder_submit_library_item_form', this.saveAsLibraryItem)
                    .on('keypress', '#tb_library_item_form input', this.saveAsLibraryItem)
                    .on('keyup', '#row_anchor',this.rowAnchor); 
          
            Themify.body.on('themify_builder_lightbox_close', this.clear)
                    .on(actionEvent, '.tb_module_front a', this.disable_links);
            api.Mixins.Common.moduleOptionsBinding();
        },
        parseSettings: function (item, is_style, breakpoint, repeat) {
            var value = false,
                    $this = $(item),
                    cl = item.classList,
                    option_id = '',
                    checked = true;
            if (repeat) {
                option_id = $this.data('input-id');
            }
            else {
                option_id = item.getAttribute('name');
                if (!option_id) {
                    option_id = item.getAttribute('id');
                }
            }
            if (cl.contains('tb_lb_wp_editor')) {
                if (tinyMCE !== undefined) {
                    var tid = item.getAttribute('id'),
                            tiny = tinyMCE.get(tid);
                    value = tiny !== null ? (tiny.hidden === false ? tiny.getContent() : switchEditors.wpautop(tinymce.DOM.get(tid).value)) : $this.val();
                } else {
                    value = $this.val();
                }
            }
            else if (cl.contains('themify-checkbox')) {
                var cselected = [];
                $this.find('.tb-checkbox:checked').each(function (i) {
                    cselected.push($(this).val());
                });
                value = cselected.length > 0 ? cselected.join('|') : false;
                cselected = null;
                checked = false;
            }
            else if (cl.contains('themify-layout-icon')) {
                value = $this.find('.selected').prop('id');
                checked = !cl.contains('tb_frame');
            }
            else if (cl.contains('themify-option-query-cat')) {
                var parent = $this.parent(),
                        single_cat = parent.find('.query_category_single'),
                        multiple_cat = parent.find('.query_category_multiple');
                value = multiple_cat.val() ? multiple_cat.val() + '|multiple' : single_cat.val() + '|single';
            }
            else if (cl.contains('tb_row_js_wrapper')) {
                value = [];
                var repeats = item.getElementsByClassName('tb_repeatable_field_content');
                for (var i = 0, len = repeats.length; i < len; ++i) {
                    var childs = repeats[i].getElementsByClassName('tb_lb_option_child');
                    value[i] = {};
                    for (var j = 0, clen = childs.length; j < clen; ++j) {
                        var v = this.parseSettings(childs[j], is_style, breakpoint, true);
                        if (v && v['v'] !== 'px' && v['v']!=='n' && v['v'] !== 'pixels' && v['v'] !== 'solid' && v['v'] !== 'default' && v['v'] !== '|') {
                            value[i][v['id']] = v['v'];
                        }
                    }
                }

            }
            else if (cl.contains('tb_radio_input_container')) {
                var input = $this.find('input:checked');
                if (breakpoint === 'desktop' || !input.hasClass('reponive_disable')) {
                    value = input.val();
                }
                checked = false;
            }
            else if (cl.contains('module-widget-form-container')) {
                value = $this.find(':input').themifySerializeObject();
            }
            else if (cl.contains('tb_widget_select')){
                value = $this.find('.selected').data('value');
            }
            else {
                value = $this.val();
                if(value!==''){
                    var opacity = $this.attr('data-opacity');
                    if (opacity !== undefined && opacity !== '' && opacity != 1 && opacity != '0.99') {
                        value += '_' + opacity;
                    }
                    checked = !cl.contains('tb_frame') && !cl.contains('border_color') && !cl.contains('border_width');
                }
                else{
                    value=false;
                } 
            }
            if (value || !is_style) {
                if (!is_style || (value !== 'px'  && value !== 'pixels' && value!=='n' && value !== 'solid' && value !== 'linear' && value !== 'default' && value !== '|' && (breakpoint === 'desktop' || $this.closest('.reponive_disable').length === 0)) || (value!=='%' && cl.contains('tb_frame_unit'))) {
                    if (!is_style && value === false) {
                        value = '';
                    }
                    else if(value==='%' && cl.contains('tb_frame_unit')){
                            return false;
                    }
                    return {'id': option_id, 'v': value, 'checked': checked};
                }
            }
            return false;
        },
        serialize: function (id, styles, breakpoint) {
            breakpoint = breakpoint || api.activeBreakPoint;
            var is_style = styles !== undefined;
            if (this.breakpoints === undefined && breakpoint !== 'desktop' && is_style) {
                this.breakpoints = Object.keys(themifyBuilder.breakpoints).reverse();
            }
            var result = {},
                    el = top_iframe.getElementById(id),
                    options = el.getElementsByClassName('tb_lb_option'),
                    breakpoints = breakpoint !== 'desktop' && is_style && this.breakpoints !== undefined ? this.breakpoints : false;
            if (breakpoints !== false) {
                var index = breakpoints.indexOf(breakpoint);
                for (var i = 0; i <= index; ++i) {
                    breakpoints.shift();
                }
                breakpoints.push('desktop');//sorted from small width to large
            }
            for (var i = 0, len = options.length; i < len; ++i) {
                var v = this.parseSettings(options[i], is_style, breakpoint, false);
                if (v !== false) {
                    if (breakpoints !== false && v['checked'] === true && options[i].tagName !== 'SELECT') {//don't save the same parent styles
                        var found = false;
                        for (var j = 0, blen = breakpoints.length; j < blen; ++j) {
                            if (styles['breakpoint_' + breakpoints[j]] !== undefined && styles['breakpoint_' + breakpoints[j]][v['id']] !== undefined && styles['breakpoint_' + breakpoints[j]][v['id']] === v['v']) {

                                found = true;
                                break;
                            }
                            else if (breakpoints[j] === 'desktop' && styles[v['id']] !== undefined && styles[v['id']] === v['v']) {
                                found = true;
                                break;
                            }
                        }

                        if (found) {
                            continue;
                        }
                    }
                    result[v['id']] = v['v'];
                }
            }
            return result;
        },
        saveComponent: function (e) {
            var auto_save = e === null;
            if (!auto_save) {
                e.preventDefault();
            }
            if (!api.hasChanged) {
                ThemifyBuilderCommon.Lightbox.close(auto_save);
                return;
            }
            var self = api.Forms,
                id = api.activeModel.get('elType'),
                is_module = id === 'module';
            if (is_module && !self.isValidate($('#tb_module_settings', top_iframe))) {
                return;
            }

            Themify.body.trigger('themify_builder_save_component');
            if (api.mode === 'visual') {
                // Trigger parent iframe
               window.top.jQuery('body').trigger('themify_builder_save_component');
            }

            api.saving = true;
            var result = {},
                    animation = null,
                    is_new = false,
                    column = false, //for the new modules of undo/redo
                    visible = null,
                    options = null,
                    k = 'styling',
                    elem = $('.tb_element_cid_' + api.activeModel.cid);
            if (is_module || id === 'row' || id === 'subrow') {
                if (is_module) {
                    k = 'mod_settings';
                    is_new = api.activeModel.get('is_new');
                    api.activeModel.unset('is_new', {silent: true});
                }

                if ( id !== 'subrow' ) {
                    options = self.serialize('tb_options_setting');
                }
                animation = self.serialize('tb_options_animation');
            }

            if ( is_module || id === 'row' || id === 'subrow' ){
				visible = self.serialize('tb_options_visibility');
				if (api.mode === 'visual') {
					if (visible['visibility_all'] === 'hide_all' || visible['visibility_desktop'] === 'hide' || visible['visibility_tablet'] === 'hide' || visible['visibility_mobile'] === 'hide') {
						elem.addClass('tb_visibility_hidden');
					}
					else {
						elem.removeClass('tb_visibility_hidden');
					}
				}
			}
            
            if (api.mode === 'visual') {
                api.liveStylingInstance.remember(api.activeModel.cid);
            }
            if (!auto_save) {
                ThemifyBuilderCommon.Lightbox.close(auto_save);
            }
            else{
                Themify.body.trigger('themify_builder_lightbox_before_close');
            }
            var data = $.extend(true, {}, api.Mixins.Common.styleData),
                styling = data['breakpoint_desktop'];
            data['breakpoint_desktop']=null;
            for (var i in data) {
                styling[i] = data[i];
            }
            result[k] = $.extend(true, styling, options, animation, visible);
            result[k] = api.Utils.clear(result[k]);
            var before_settings = api.activeModel.get(k);
            api.activeModel.set(result, {silent: true});
            if (is_module) {
                if (is_new) {
                    column = elem.closest('.module_column');
                    column.closest('.module_row').removeClass('tb_row_empty');
                    column.closest('.module_subrow').removeClass('tb_row_empty');
                    column = ThemifyBuilderCommon.clone(column);
                }
                api.vent.trigger('dom:builder:change');
                $('.tb_import_layout_button').remove();
            }
            var bstyles,astyles;
            if (api.mode === 'visual') {
                bstyles = api.liveStylingInstance.undoData;
                astyles = $.extend(true,{},api.liveStylingInstance.getRememberedStyles());
            }
            api.undoManager.push(api.activeModel.cid, api.beforeEvent, elem, 'save', {bsettings:before_settings,asettings:result[k],bstyles: bstyles,astyles:astyles, 'column': column});
            api.beforeEvent = false;
            api.saving = false;
            if (auto_save) {
                ThemifyBuilderCommon.Lightbox.close(auto_save);
            }
        },
        moduleOptAddRow: function (e, values) {
            e.preventDefault();
            var parent = $(e.currentTarget).parent().prev(),
                    template = api.cache.repeaterElements[ parent.prop('id') ].clone(),
                    row_count = Math.random().toString(36).substr(2, 7),
                    editors = [],
                    editor_cache = false,
                    uploader = false,
                    ranges = [],
                    is_not_trigger = !e.isTrigger || values;
            template.removeClass('collapsed').find('.row_inner').show();
            var items = template[0].getElementsByClassName('tb_lb_option_child');

            values = values ? $(values) : false;
            for (var i = 0, len = items.length; i < len; ++i) {
                var $child = $(items[i]),
                        input = values ? values.find('[data-input-id="' + $child.data('input-id') + '"]') : false,
                        cl = items[i].classList;
                if (cl.contains('tb_lb_wp_editor')) {

                    var orig_id = $child.data('input-id'),
                            repeated_id = $child.data('control-repeater'),
                            p = $child.closest('.wp-editor-wrap'),
                            new_id = orig_id + '_' + i + '_' + Math.random().toString(36).substr(2, 7);
                    if (editor_cache === false) {
                        editor_cache = p[0].innerHTML;
                    }
                    p[0].innerHTML  = editor_cache.replace(new RegExp(orig_id, 'g'), new_id);
                    $child = p.find('.tb_lb_wp_editor');
                    $child.attr({'name': orig_id, 'data-input-id': orig_id, 'data-control-repeater': repeated_id}).data({'input-id': orig_id, 'control-repeater': repeated_id});
                    if (input) {
                        var tid = input.prop('id'),
                            tiny = tinyMCE.get(tid),
                            value = tiny && tiny.hidden === false ? tiny.getContent() : switchEditors.wpautop(tinymce.DOM.get(tid).value);
                        $child.val(value);
                    }
                    else {
                        $child.val('');
                    }
                    editors.push($child);
                }
                else if (cl.contains('themify-layout-icon')) {
                    var layouts = $child.find('a');
                    layouts.removeClass('selected');
                    if (input) {
                        layouts.filter('#' + input.find('.selected').prop('id')).addClass('selected');
                    }
                    else {
                        var m_defaults = themifyBuilder.modules[ api.activeModel.get('mod_name') ];
                        if (m_defaults !== undefined && m_defaults.defaults !== undefined && m_defaults.defaults[orig_id]) {
                            layouts.filter('#' + m_defaults.defaults[orig_id]).addClass('selected');
                        }
                        else {
                            layouts.first().addClass('selected');
                        }
                    }
                }
                else if (cl.contains('tb_uploader_input')) {
                    if (is_not_trigger) {
                        uploader = true;
                        input = input !== false ? input.val() : '';
                        var p = $child.val(input).parent(),
                                placeholder = p.find('.thumb_preview').find('.img-placeholder');
                        p.find('.tb_upload_btn').prop('id', 'pluploader_' + row_count + '_' + i + 'tb_plupload_upload_ui').addClass('plupload-clone')
                                .find('.builder_button').prop('id', 'pluploader_' + row_count + '_' + i + 'tb_plupload_browse_button');
                        if (input !== '') {
                            var img_thumb = $('<img/>', {src: input, width: 50, height: 50});
                            placeholder.html(img_thumb);
                        }
                    }
                }
                else if (cl.contains('tb_radio_input_container')) {
                    var childs = items[i].getElementsByClassName('tb_radio_dnd'),
                            oriname = $child.data('input-id'),
                            val = input ? input.find(':checked').val() : false;
                    for (var j = 0, clen = childs.length; j < clen; ++j) {
                        var $self = $(childs[j]);
                        $self.prop({name: oriname + '_' + row_count, id: oriname + '_' + row_count + '_' + j, checked: false})
                                .next('label').prop('for', oriname + '_' + row_count + '_' + j);
                        if (val === $self.val() || (!val && $self.data('checked'))) {
                            $self.prop('checked', true);
                        }
                    }
                    if (cl.contains('tb_option_radio_enable')) {
                        ThemifyBuilderCommon.Lightbox.clickRadioOption(null, $child.find(':checked'));
                    }
                }
                else {
                    var val = input ? input.val() : '';
                    if (val === undefined) {
                        val = '';
                    }
                    $child.val(val);
                    if (is_not_trigger) {
                        if(cl.contains('tb_range')){
                            ranges.push( $child );
                        }
                        else if(input && cl.contains('minicolors-input')){
                            var wrapper = input.closest('.minicolors_wrapper'),
                                minicolor = wrapper.find('.minicolors-swatch-color'),
                                current_wrapper = $child.closest('.minicolors_wrapper'),
                                opacity = input.data('opacity');
                            current_wrapper.find('.minicolors-swatch-color').css({'background-color':minicolor.css('background-color'),'opacity':opacity});
                            current_wrapper.find('.color_opacity').val(wrapper.find('.color_opacity').val());
                            $child.data('opacity',opacity).attr('data-opacity',opacity);
                            minicolor = current_wrapper = opacity = wrapper = null;
                        }
                    }
                }
                if (is_not_trigger) {
                    // Hide conditional inputs
                    if ($child.data('binding')) {
                        api.Mixins.Common.doTheBinding($child, val, template);
                    }
                    if ($child.data('control-binding') && !cl.contains('tb_lb_wp_editor') && !cl.contains('minicolors-input') && !cl.contains('themify-gradient')) {
                        api.Views.init_control($child.data('control-type'), {el: $child, binding_type: $child.data('control-binding'), selector: $child.data('live-selector')});
                    }

                }
            }
			
			function imageUrlCheck (e) {
				var val = $( this ).val();
				if ( val != '' ) {
					if ( ThemifyBuilderCommon.isImageUrl(val) ) {
						$( this ).removeClass('tb_image_field_error');
					} else {
						$(this).addClass('tb_image_field_error');
						alert(themifyBuilder.i18n.incorrectImageURL);
					}
				} else {
					$( this ).removeClass('tb_image_field_error');
				}
			}

			$(template[0].querySelectorAll('.tb_uploader_input:not([data-control-type="audio"])')).on('focus', function (event) {
				$( this ).removeClass('tb_image_field_error');
					if (event.originalEvent !== undefined) {
						$(this).one('blur', imageUrlCheck);
					}
			} );

            if (is_not_trigger) {
                setTimeout(function () {
                    api.Utils.setColorPicker(template);
                }, 1);
            }
            parent[0].appendChild(template[0]);
            if (is_not_trigger) {
                if (editors.length > 0) {
                    setTimeout(function () {
                        for (var i = 0, len = editors.length; i < len; ++i) {
                            
                            api.Views.init_control('wp_editor', {el: editors[i], binding_type: editors[i].data('control-binding'), selector: editors[i].data('live-selector')});
                        }
                        editors = null;
                    }, 1);
                }
                if(ranges.length>0){
                    setTimeout(function () {
                        for (var i = 0, len = ranges.length; i < len; ++i) {
                            api.Utils.createRange( ranges[i] );
                        }
                        ranges = null;
                    },200);
                }
                if (uploader) {
                    api.Utils.builderPlupload('new_elemn');
                }
            }
        },
        builderImportSubmit: function (e) {
            e.preventDefault();

            var $this = $(this),
                    options = {
                        buttons: {
                            no: {
                                label: 'Replace Existing Builder'
                            },
                            yes: {
                                label: 'Append Existing Builder'
                            }
                        }
                    };

            ThemifyBuilderCommon.LiteLightbox.confirm(themifyBuilder.i18n.dialog_import_page_post, function (response) {
                $.ajax({
                    type: "POST",
                    url: themifyBuilder.ajaxurl,
                    dataType: 'json',
                    data:
                            {
                                action: 'builder_import_submit',
                                nonce: themifyBuilder.tb_load_nonce,
                                data: $this.closest('form').serialize(),
                                importType: 'no' === response ? 'replace' : 'append',
                                importTo: themifyBuilder.post_ID
                            },
                    beforeSend: function (xhr) {
                        ThemifyBuilderCommon.showLoader('show');
                    },
                    success: function (data) {
                        api.Forms.reLoad(data, themifyBuilder.post_ID);
                        ThemifyBuilderCommon.Lightbox.close();
                    }
                });

            }, options);
        },
        lightbox_switcher: function (e) {
            e.preventDefault();
            var id = $(e.currentTarget).attr('href').replace('#', '');
            if(id===api.activeBreakPoint){
                return;
            }
            if (api.activeModel && api.mode === 'visual') {
                api.scrollTo = api.liveStylingInstance.$liveStyledElmt;
            }
            $('.tb_breakpoint_switcher.breakpoint-' + id, top_iframe).trigger('click');
        },
        disable_links:function(e){
           e.preventDefault(); 
        },
        rowAnchor:function () {
                var rowAnchor,
                    el;
                if(api.mode==='visual'){
                    var live = api.liveStylingInstance,
                    rowAnchor = live.getStylingVal('row_anchor');
                    live.$liveStyledElmt.removeClass(live.getRowAnchorClass(rowAnchor));
                }
                rowAnchor = $.trim($(this).val());
                if(api.mode==='visual'){
                    live.setStylingVal('row_anchor', rowAnchor);
                    if (rowAnchor !== '') {
                        live.$liveStyledElmt.addClass(live.getRowAnchorClass(rowAnchor));
                    }
                    el = live.$liveStyledElmt;
					el.data('anchor',rowAnchor).attr('data-anchor',rowAnchor);
                }
                else{
                    el = $('.tb_element_cid_'+api.activeModel.cid);
                }
                el.children('.tb_row_actions').find('.tb_row_anchor').text(rowAnchor.replace('#',''));
        },
        LayoutPart:{
            cache:[],
            undo:null,
            old_id:null,
            isReload:null,
            id:null,
            init:false,
            html:null,
            el:null,
            options:null,
            scrollTo:function(prev, breakpoint){
                 api.scrollTo = api.Forms.LayoutPart.el;
            },
            insertSwap:function(module){
                module.getElementsByClassName('tb_dropdown')[0].insertAdjacentHTML('afterbegin','<li><div class="tb_swap ti-reload themify-tooltip-bottom"><div class="themify_tooltip">Swap</div></div></li>');
            },
            edit:function(item){
                ThemifyBuilderCommon.showLoader('show');
                document.body.classList.add('tb_layout_part_edit');
                if(api.activeModel){
                    $('.builder_save_button', top_iframe).trigger('click');
                }
                top_iframe.body.classList.add('tb_layout_part_edit');
                var self = this,
                    $item = $(item).closest('.active_module'),
                    builder = $item.find('.themify_builder_content'),
                    tpl = ThemifyBuilderCommon.templateCache.get('tmpl-small_toolbar');
                    this.id = builder.data('postid');
                    this.old_id = themifyBuilder.post_ID;
                    this.init = true;
                function callback(data){
                    document.getElementById('themify_builder_content-'+themifyBuilder.post_ID).insertAdjacentHTML('afterbegin','<div class="tb_overlay"></div>');
                    $item.addClass('tb_active_layout_part').closest('.row_inner').find('.active_module').each(function(){
                        if(!this.classList.contains('tb_active_layout_part')){
                            this.insertAdjacentHTML('afterbegin','<div class="tb_overlay"></div>');
                        }
                    });
                    var id = 'themify_builder_content-'+self.id;
                        self.html = $item[0].innerHTML;
                        themifyBuilder.post_ID = self.id;
                        $item[0].insertAdjacentHTML('afterbegin',tpl.replace('#postID#',self.id));
                        $item.removeClass('active_module module')
                            .closest('.tb_holder').removeClass('tb_holder').addClass('tb_layout_part_parent')
                            .closest('.module_row').addClass('tb_active_layout_part_row');
                        builder.attr('id',id).removeClass('not_editable_builder').empty();
                        
                        self.el = $item;
                        api.id = self.id;
                        var settings = [],
                            other_layouts = $('.'+id+'.not_editable_builder').closest('.active_module'),
                            items;
                        other_layouts.each(function(){
                           $(this).find('.themify-builder-generated-css').prop('disabled',true); 
                        });
                        other_layouts = null;
                        api.Instances.Builder[1] = new api.Views.Builder({el: '#'+id, collection: new api.Collections.Rows(data), type:api.mode});
                        items = api.Instances.Builder[1].render().el.querySelectorAll('[data-cid]');
                        for(var i=0,len=items.length;i<len;++i){
                            settings[items[i].dataset.cid] = 1;
                        }
                        items = null;
                        api.bootstrap(settings, finish);
                        function finish() {
                            settings = null;
                            api.Utils.loadContentJs(builder);
                            api.id = false;
                            Themify.body.on('themify_builder_change_mode', self.scrollTo);
                            api.hasChanged = true;
                            api.vent.trigger('dom:builder:init');
                            $item.find('.tb_toolbar_save').click(self.save.bind(self));
                            $item.find('.tb_toolbar_close_btn').click(self.close.bind(self));
                            $item.find('.tb_load_layout').click(api.Views.Toolbar.prototype.loadLayout);
                            $item.find('.tb_toolbar_import ul a').click(api.Views.Toolbar.prototype.import);
                            ThemifyBuilderCommon.showLoader('hide');
                            self.init = false;
                            self.undo = api.undoManager.stack;
                            api.undoManager.btnUndo = $item[0].getElementsByClassName('tb_undo_btn')[0];
                            api.undoManager.btnRedo = $item[0].getElementsByClassName('tb_redo_btn')[0];
                            api.undoManager.reset();
                            $item.find('.tb_undo_redo').click(function(e){
                                api.undoManager.do_change(e);
                            });
                            if(api.activeBreakPoint!=='desktop'){
                                api.Mixins.Builder.updateModuleSort(null,'disable');
                            }
                        }
                }
                
                if(this.cache[this.id]!==undefined){
                    callback(this.cache[this.id]);
                    return;
                }
                $.ajax({
                    type: 'POST',
                    dataType:'json',
                    url: themifyBuilder.ajaxurl,
                    data:{
                        action: 'tb_layout_part_swap',
                        nonce: themifyBuilder.tb_load_nonce,
                        id: self.id
                    },
                    success:function(res){
                        if(res){
                            self.cache[self.id] = res;
                            callback(res);
                        }
                    }

                });
            },
            close:function(e){
                e.preventDefault();
                e.stopPropagation();
                var self = this,
                    builder = this.el.find('.themify_builder_content');
                if(this.options!==null){
                    ThemifyBuilderCommon.showLoader('show');
                    var module = api.Models.Registry.lookup(this.el.data('cid'));
                    this.cache[this.id] = this.options;
                    $( document ).ajaxComplete(function afterRefresh(e, xhr, settings ) {
                        if(settings.data.indexOf('tb_load_module_partial',3)!==-1){
                            $(this).off('ajaxComplete',afterRefresh);
                            if(xhr.status===200){
                                self.el = api.liveStylingInstance.$liveStyledElmt;
                                builder = self.el.children('.themify_builder_content');
                                var html = builder[0].innerHTML,
                                    link = '';
                                    self.el.children('.themify-builder-generated-css').each(function(){
                                        link+=this.outerHTML;
                                    });
                                $('.themify_builder_content-'+self.id).each(function(){
                                    if($(this).hasClass('not_editable_builder')){
                                        var p = $(this).closest('.module');
                                            p.children('link.themify-builder-generated-css').remove();
                                            if(link!==''){
                                                p[0].insertAdjacentHTML('afterbegin', link);
                                            }
                                        this.innerHTML = html;
                                        api.Utils.loadContentJs($(this));
                                    }
                                });
                                link = html = null;
                                ThemifyBuilderCommon.showLoader('hide');
                                callback();
                            }
                            else{
                                ThemifyBuilderCommon.showLoader('error');
                            }
                        }
                    });
                    var options = $.extend(true, {},module.get('mod_settings'));
                    options['unsetKey'] = true;
                    module.trigger('custom:preview:refresh', options);
                    options = null;
                }
                else{
                    var other_layouts = $('.themify_builder_content-'+self.id+'.not_editable_builder').closest('.active_module');
                    other_layouts.each(function(){
                       $(this).find('.themify-builder-generated-css').removeAttr('disabled'); 
                    });
                    other_layouts = null;
                    this.el[0].innerHTML = self.html;
                    callback();
                    api.Utils.loadContentJs(builder);
                }
                function callback(){
                    self.el.removeClass('tb_active_layout_part').addClass('active_module module')
                    .closest('.tb_layout_part_parent').addClass('tb_holder').removeClass('tb_layout_part_parent')
                    .closest('.module_row').removeClass('tb_active_layout_part_row');
                    $('#tb_small_toolbar',self.el).remove();
                    var items = builder[0].querySelectorAll('[data-cid]');
                    for(var i=0,len=items.length;i<len;++i){
                        var cid = items[i].dataset.cid,
                        m = api.Models.Registry.lookup(cid);
                        if(m){
                            m.destroy();
                            api.Models.Registry.remove(cid);
                            api.liveStylingInstance.revertRules(cid);
                            delete api.VisualCache[cid];
                        } 
                    }
                    items = null;
                    builder.removeAttr('id').addClass('not_editable_builder');
                    document.body.classList.remove('tb_layout_part_edit');
                    top_iframe.body.classList.remove('tb_layout_part_edit');
                    $('.tb_overlay').remove();
                    api.undoManager.stack = self.undo;
                    api.undoManager.index = self.undo.length-1;
                    api.undoManager.btnUndo = api.toolbar.el.getElementsByClassName('tb_undo_btn')[0];
                    api.undoManager.btnRedo = api.toolbar.el.getElementsByClassName('tb_redo_btn')[0];
                    themifyBuilder.post_ID = self.old_id;
                    self.old_id = null;
                    self.html = null;
                    self.id = null;
                    self.el = null;
                    self.options =null;
                    self.isReload = null;
                    api.Instances.Builder[1]=null;
                    delete api.Instances.Builder[1];
                    self.undo = null;
                    Themify.body.off('themify_builder_change_mode', self.scrollTo);
                    api.Mixins.Builder.updateModuleSort();
                    api.undoManager.updateUndoBtns();
                    if(api.activeBreakPoint!=='desktop'){
                         api.Mixins.Builder.updateModuleSort(null,'disable');
                    }
                }
                
            },
            save:function(e){
                e.preventDefault();    
                e.stopPropagation();
                if(api.undoManager.hasUndo() || this.isReload!==null){
                    var self = this;
                    this.html = null;
                    this.old_settings=null;
                    ThemifyBuilderCommon.showLoader('show');
                    if (api.activeModel) {
                        $('.builder_save_button', top_iframe).trigger('click');
                    }
                    api.Utils.saveBuilder(function(res){
                        if(res.success){
                            self.options =res.data.builder_data;
                        }
                    }, 'main', 1);
                }
                else{
                    ThemifyBuilderCommon.showLoader('show');
                    setTimeout(function(){
                        ThemifyBuilderCommon.showLoader('hide');
                    },100);
                }
            }
        },
        reLoad: function (data, id,callback) {
            
            var is_layout_part = api.Forms.LayoutPart.id!==null,
                index=is_layout_part?1:0,
                settings = null,
                el = '';
        
            api.Mixins.Builder.updateModuleSort(null,'destroy');
            if(!is_layout_part){
                api.Models.Registry.destroy();
                api.Instances.Builder = {};
            }
            if (api.mode === 'visual') {
                var linkId = 'themify-builder-' + id + '-generated-css',
                    css = $('link#' + linkId);
                el = '#themify_builder_content-' + id;
                api.id = id;
                if(is_layout_part){
                    var parent = api.Instances.Builder[index].$el.closest('.module-layout-part');
                    css = css.add(parent.children('.themify-builder-generated-css')); 
                }
                css.remove();
                if (data.css !== undefined && data.css.css_file !== undefined) {
                    var link = '<link id="' + linkId + '" type="text/css" rel="stylesheet" href="' + data.css.css_file + '?tmp=' + Date.now() + '" />';
                    if(data.css.fonts){
                        link+='<link id="themify-builder-' + id + '-font" type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=' + data.css.fonts.join('|') + '" />';
                    }
                    if(is_layout_part){
                        parent[0].insertAdjacentHTML('afterbegin', link);
                        parent.children('link').removeAttr('id').addClass('themify-builder-generated-css');
                        parent = null;
                    }
                    else{
                       document.getElementById('themify-builder-admin-ui-css').insertAdjacentHTML('afterend', link);
                    }
                    link = null;
                }
                if(!is_layout_part){
                    api.VisualCache = [];
                    api.editing = false;
                    setTimeout(function () {
                        api.liveStylingInstance.reset();
                    }, 1);
                    Themify.body.addClass('sidebar-none full_width');
                    $('#sidebar,.page-title').remove();
                }
            }
            else {
                el = '#tb_row_wrapper';
            }
            if(is_layout_part){
                var items = api.Instances.Builder[index].el.querySelectorAll('[data-cid]');
                api.Forms.LayoutPart.isReload = true;
                for(var i=0,len=items.length;i<len;++i){
                    var cid = items[i].dataset.cid,
                    m = api.Models.Registry.lookup(cid);
                    if(m){
                        m.destroy();
                        api.Models.Registry.remove(cid);
                        api.liveStylingInstance.revertRules(cid);
                    } 
                }
                items = null;
                api.Instances.Builder[index].$el.empty();
            }
            api.Instances.Builder[index] = new api.Views.Builder({el: el, collection: new api.Collections.Rows(data.builder_data), type:api.mode});
            api.Instances.Builder[index].render();
            api.undoManager.reset();
            if(is_layout_part){
                settings = [];
                items =  api.Instances.Builder[index].el.querySelectorAll('[data-cid]');
                for(var i=0,len=items.length;i<len;++i){
                    settings[items[i].dataset.cid] = 1;
                }
                items = null;
            }
            if (api.mode === 'visual') {
                api.bootstrap(settings, finish);
            }
            else {
                finish();
            }
            
            function finish() {
                if (api.mode === 'visual') {
                    api.Utils.loadContentJs($(el));
                    api.id = false;
                }
                api.vent.trigger('dom:builder:init');
                ThemifyBuilderCommon.showLoader('hide');
                if (api.mode === 'visual' && api.activeBreakPoint !== 'desktop') {
                    $('body', top_iframe).height(document.body.scrollHeight);
                    setTimeout(function () {
                        $('body', top_iframe).height(document.body.scrollHeight);
                    }, 2000);
                }
                if(callback){
                    callback();
                }
            }
        },
        templateSelected: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $this = $(this).closest('.layout_preview'),
                    options = {
                        buttons: {
                            no: {
                                label: 'Replace Existing Layout'
                            },
                            yes: {
                                label: 'Append Existing Layout'
                            }
                        }
                    };

            ThemifyBuilderCommon.LiteLightbox.confirm(themifyBuilder.i18n.confirm_template_selected, function (response) {
                var id = themifyBuilder.post_ID,
                        args = {
                            type: 'POST',
                            url: themifyBuilder.ajaxurl,
                            dataType: 'json',
                            data: {
                                action: 'tb_set_layout',
                                mode: 'no' === response ? 1 : 0,
                                nonce: themifyBuilder.tb_load_nonce,
                                layout_slug: $this.data('slug'),
                                id: id,
                                layout_group: $this.data('group')
                            },
                            beforeSend: function () {
                                if ('visual' === api.mode) {
                                    ThemifyBuilderCommon.showLoader('show');
                                }
                            },
                            success: function (data) {
                                ThemifyBuilderCommon.Lightbox.close();
                                if (data.status === 'success') {
                                    api.Forms.reLoad(data, id);
                                } else {
                                    ThemifyBuilderCommon.showLoader('error');
                                    alert(data.msg);
                                }
                            }
                        };
                if ($this.data('group') === 'pre-designed') {
                    ThemifyBuilderCommon.showLoader('show');
                    var slug = $this.data('slug'),
                            file = 'https://themify.me/themify-layouts/' + slug + '.txt',
                            done = function () {
                                args.data.builder_data = api.layouts_selected[slug];
                                $.ajax(args);
                            };
                    if (!api.layouts_selected) {
                        api.layouts_selected = {};
                    }
                    else if (api.layouts_selected[slug]) {
                        done();
                        return;
                    }
                    $.get(file, null, null, 'text')
                            .done(function (data) {
                                api.layouts_selected[slug] = data;
                                done();
                            })
                            .fail(function (jqxhr, textStatus, error) {
                                ThemifyBuilderCommon.LiteLightbox.alert('There was an error in loading layout, please try again later, or you can download this file: (' + file + ') and then import manually (https://themify.me/docs/builder#import-export).');
                            })
                            .always(function () {
                                ThemifyBuilderCommon.showLoader();
                            });
                } else {
                    $.ajax(args);
                }
            }, options);
        },
        saveAsLayout: function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: themifyBuilder.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'tb_save_custom_layout',
                    nonce: themifyBuilder.tb_load_nonce,
                    form_data:$('#tb_save_layout_form', top_iframe).serialize()
                },
                success: function (data) {
                    if (data.status === 'success') {
                        ThemifyBuilderCommon.Lightbox.close();
                    } else {
                        alert(data.msg);
                    }
                }
            });
        },
        saveAsLibraryItem: function (e) {
                if('keypress' === e.type && e.keyCode !== 13){
                        return;
                }
                e.preventDefault();
                ThemifyBuilderCommon.showLoader('show');
                var $form =  ThemifyBuilderCommon.Lightbox.$lightbox.find('#tb_library_item_form'),
                    cid = $form.find('[name="model"]').val(),
                    model = api.Models.Registry.lookup(cid),
                    $settings,
                    component = model.get('elType');
                switch (component) {
                    case 'row':
                        $settings = api.Utils._getRowSettings($('.tb_element_cid_'+cid)[0],0) ;
                        break;

                    case 'module':
                        $settings = {'mod_name': model.get('mod_name'), 'mod_settings': model.get('mod_settings')};
                        break;
                } 
                
                $form.find('[name="item"]').val(JSON.stringify($settings));
                $form.find('[name="nonce"]').val(themifyBuilder.tb_load_nonce);
               
                $.ajax({
                        type: 'POST',
                        url: themifyBuilder.ajaxurl,
                        dataType: 'json',
                        data: $form.serialize(),
                        success: function (data) {
                                if (data.status === 'success') {
                                        $('#tb_module_panel',top_iframe).find('.tb_module_panel_search_text').val('');
                                        if(data.is_layout){
                                            api.hasChanged = true;
                                            var control = api.Views.ControlRegistry.lookup('selected_layout_part');
                                            if(control){
                                                control.data = [];
                                            }
                                            var elm = $('.tb_element_cid_'+cid),
                                                module,
                                                after,
                                                before =ThemifyBuilderCommon.clone(elm);
                                            if(component==='row'){
                                                var row = api.Views.init_row(data.replWith),
                                                    $Elem = row.view.render();
                                                    module = api.Models.Registry.lookup($Elem.$el.find('.active_module').data('cid'));
                                            } else {
                                                module = api.Views.init_module(data.replWith),
                                                $Elem = module.view.render();
                                                module = module.model;
                                            }
                                            elm.replaceWith($Elem.el);
                                            if(api.mode==='visual'){
                                                $( document ).ajaxComplete(function Refresh(e, xhr, settings ) {
                                                    if(settings.data.indexOf('tb_load_module_partial',3)!==-1){
                                                        $(this).off('ajaxComplete',Refresh);
                                                        if(component==='row'){
                                                            after = api.liveStylingInstance.$liveStyledElmt.closest('.module_row');
                                                        }
                                                        else{
                                                            after = api.liveStylingInstance.$liveStyledElmt;
                                                        }
                                                        api.undoManager.push( $Elem.$el.data('cid'), before, after, 'row');
                                                    }
                                                });
                                                module.trigger('custom:preview:refresh', module.get('mod_settings'));
                                            }
                                            else{
                                                after = $Elem.el;
                                                api.Mixins.Builder.updateModuleSort($Elem.$el);
                                                api.undoManager.push( $Elem.$el.data('cid'), before, after, 'row');
                                            }
                                           
                                        }
                                        var libraryItems = $('.tb_library_item_list'),
                                            html = api.toolbar.libraryItems.template([data]);
                                            if(api.mode==='visual'){
                                                libraryItems = libraryItems.add(api.toolbar.$el.find('.tb_library_item_list'));
                                            }
                                            libraryItems = libraryItems.get();
                                            for(var i=0,len=libraryItems.length;i<len;++i){
                                                var item = libraryItems[i].getElementsByClassName('simplebar-content');
                                                if(item.length>0){
                                                    item[0].insertAdjacentHTML('afterbegin',html);
                                                    $(libraryItems[i]).closest('.tb_module_panel_tab').find('.tb_module_types .active a').trigger('click');
                                                }
                                            }
                                            api.toolbar.libraryItems.bindEvents(true);
                                            ThemifyBuilderCommon.showLoader('hide');
                                            ThemifyBuilderCommon.Lightbox.close();
                                } else {
                                        alert(data.msg);
                                }
                        }
                });
        },
        isValidate: function ($form) {
            var validate = $form.find('[data-validation]');
            if (validate.length === 0) {
                return true;
            }
            var that = this,
                    errors = {};
            validate.each(function () {
                var $this = $(this),
                        rule = $this.data('validation'),
                        value = $this.val();
                if (!that.checkValidate(rule, value)) {
                    errors[ $this.prop('id') ] = $this.data('error-msg');
                }
            });

            $form.find('.tb_field_error').removeClass('tb_field_error').end().find('.tb_field_error_msg').remove();

            if (!_.isEmpty(errors)) {
                var errorCount = 0;
                _.each(errors, function (msg, div_id) {
                    var $field = $form.find('#' + div_id);
                    $field.addClass('tb_field_error');
                    var el = $('<span/>', {class: 'tb_field_error_msg', 'data-error-key': div_id}).text(msg);
                    if($field.is('select')){
                        el.insertAfter($field.closest('.selectwrapper'));
                    }
                     else{
                         el.insertAfter($field);
                     }       

                    if (!errorCount) {
                        var activeIndex = $form.children().index($field.closest('.tb_options_tab_wrapper')),
                                errorTab = activeIndex > -1 && $form.closest('#tb_lightbox_parent').find('.tb_options_tab > li').eq(activeIndex);

                        errorTab && !errorTab.hasClass('current') && errorTab.trigger('click');
                        ++errorCount;
                    }
                });
                return false;
            } else {
                return true;
            }
        },
        checkValidate: function (rule, value) {
            var validator = api.Forms.get_validator(rule);
            return validator(value);
        },
        clear: function () {
            if (api.activeModel) {
                if (api.activeModel.get('is_new') !== undefined) {
                    api.activeModel.trigger('dom:module:unsaved');
                }
                api.activeModel = null;
                if (tinyMCE !== undefined) {
                    for (var i = tinymce.editors.length - 1; i > -1; i--) {
                        if (tinymce.editors[i].id !== 'content') {
                            tinyMCE.execCommand('mceRemoveEditor', true, tinymce.editors[i].id);
                        }
                    }
                }
            }
        }
    };
    
    
    api.undoManager = {
        stack: [],
        is_working: false,
        index: -1,
        btnUndo: document.getElementsByClassName('tb_undo_btn')[0],
        btnRedo: document.getElementsByClassName('tb_redo_btn')[0],
        compactBtn:document.getElementsByClassName('tb_compact_undo')[0],
        init: function () {
            api.toolbar.$el.find('.tb_undo_redo').on('click', this.do_change.bind(this));
            if (!themifyBuilder.disableShortcuts) {
                $(top_iframe).on('keydown', this.keypres.bind(this));
                if (api.mode === 'visual') {
                    $(document).on('keydown', this.keypres.bind(this));
                }
            }
        },
        push:function(cid, before, after, type, data){
            if (api.hasChanged) {
                api.editing = false;
                if (after) {
                    after = ThemifyBuilderCommon.clone(after);
                }
                if (api.mode === 'visual' && (type === 'duplicate' || type === 'sort')) {
                    $(window).trigger('tfsmartresize.tfVideo');
                }
                this.stack.splice(this.index + 1, this.stack.length - this.index);
                this.stack.push({'cid': cid, 'type': type, 'data': data, 'before': before, 'after': after});
                this.index = this.stack.length - 1;
                this.updateUndoBtns();
                api.mode === 'visual' && Themify.body.trigger('builder_dom_changed', [type]);
            }
        },
        set: function (el) {
            var batch = el[0].querySelectorAll('[data-cid]');
            batch = Array.prototype.slice.call(batch);
            batch.unshift(el[0]);
            for (var i = 0, len = batch.length; i < len; ++i) {
                var model = api.Models.Registry.lookup(batch[i].getAttribute('data-cid'));
                if (model) {
                    model.trigger('change:view', batch[i]);
                }
            }
        },
        doScroll: function (el) {
            //todo
            return el;
            var offset = 0,
                body = api.mode !== 'visual' || api.activeBreakPoint === 'desktop' ? $('html,body'): $('body', top_iframe);
            if(api.mode === 'visual'){
                var fixed = $('#headerwrap');
                offset=40;
                if(fixed.length>0){
                    offset+=fixed.outerHeight();
                }
            }
            body.scrollTop(el.offset().top-offset);
            return el;
        },
        keypres: function (event) {
            // Redo
            if (90 === event.which && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                if ((true === event.ctrlKey && true === event.shiftKey) || (true === event.metaKey && true === event.shiftKey)) {
                    event.preventDefault();
                    if (this.hasRedo()) {
                        this.changes(false);
                    }
                } else if (true === event.ctrlKey || true === event.metaKey) { // UNDO
                    event.preventDefault();
                    if (this.hasUndo()) {
                        this.changes(true);
                    }
                }
            }
        },
        changes: function (is_undo) {
            var index = is_undo ? 0 : 1,
                stack = this.stack[this.index + index];
            if (stack !== undefined) {
                this.is_working = true;
              
                var el = '',
                    type = stack['type'],
                    item = $('.tb_element_cid_' + stack['cid']),
                    comon = ThemifyBuilderCommon,
                    cid = false;  
                    api.eventName = type;
                if (type === 'row') {
                    if (is_undo) {
                        el = comon.clone(stack.before);
                        cid = stack['cid'];
                    }
                    else {
                        el = comon.clone(stack.after);
                        cid = stack.before.data('cid');
                        item = $('.tb_element_cid_' + cid);
                    }
                    this.doScroll(item);
                    this.set(el);
                    el.toggleClass('tb_row_empty',el.find('.active_module').length===0);
                    item.replaceWith(el);
                }
                else if (type === 'duplicate') {
                    if (is_undo) {
                        this.doScroll($('.tb_element_cid_' + stack.after.data('cid'))).remove();
                    }
                    else {
                        this.doScroll(item);
                        el = comon.clone(stack.after);
                        cid = stack.before.data('cid');
                        this.set(el);
                        item.after(el);
                    }
                }
                else if (type === 'delete_row') {
                    if (!is_undo) {
                        this.doScroll(item).remove();
                    }
                    else {
                        el = comon.clone(stack.before);
                        cid = stack['cid'];
                        var position = $('.tb_element_cid_' + stack.data.pos_cid);
                        this.doScroll(position);
                        this.set(el);
                        if (stack.data.pos === 'after') {
                            position.after(el);
                        }
                        else {
                            position.before(el);
                        }
                    }

                }
                else if (type === 'sort') {
                    cid = stack['cid'];
                    var before;
                    if (is_undo) {
                        before = stack.data['before'];
                        el = comon.clone(stack.before);
                    }
                    else {
                        before = stack.data['after'];
                        el = comon.clone(stack.after);
                        if (api.mode === 'visual') {
                            el.find('.active_module').css({'display': 'block', 'height': 'auto'});
                        }
                    }
                    this.doScroll(el);
                    this.set(el);
                    var old_el = $('.tb_element_cid_' + cid).closest('.module_row');
                    $('.tb_element_cid_' + cid).remove();
                    old_el.toggleClass('tb_row_empty',old_el.find('.active_module').length===0);
                    old_el = null;
                    $('.tb_element_cid_' + before).replaceWith(el);
                    var r = el.closest('.module_row');
                        r.toggleClass('tb_row_empty',r.find('.active_module').length===0);
                        r = null;
                }
                else if(type==='row_sort'){
                    cid = stack['cid'];
                    var is_next = stack.data[is_undo?'bnext':'anext'],
                        el2 = $('.tb_element_cid_' + stack.data[is_undo?'before':'after']),
                       item = $('.tb_element_cid_' + cid);
                    el = comon.clone(item);
                    item.remove();
                    item = null;
                    this.set(el);
                    if(is_next){
                        el2.before(el);
                    }
                    else{
                        el2.after(el);
                    }
                    this.doScroll(el);
                }
                else if (type === 'save') {
                    var cid = stack['cid'],
                            model = api.Models.Registry.lookup(cid),
                            is_module = model.get('elType') === 'module',
                            k = is_module ? 'mod_settings' : 'styling';
                    if (is_module && stack.data.column) {
                        var r;
                        if (is_undo) {
                            r= $('.tb_element_cid_' + cid).closest('.module_row');
                            cid = false;
                            this.doScroll(item).remove();
                        }
                        else {
                            cid = stack.data.column.data('cid');
                            el = comon.clone(stack.data.column);
                            item = $('.tb_element_cid_' + cid);
                            this.doScroll(item);
                            this.set(el);
                            item.replaceWith(el);
                            r= el.closest('.module_row');
                        }
                        r.toggleClass('tb_row_empty',r.find('.active_module').length===0);
                        r = null;
                    }
                    else {
                        this.doScroll(item);
                        var settings ={},styles;
                        if(is_undo){
                            el = comon.clone(stack.before);
                            if (api.mode === 'visual') {
                                styles =  $.extend(true,{},stack.data.bstyles);
                            }
                            
                            settings[k] = stack.data.bsettings;
                        }
                        else{
                            el = comon.clone(stack.after);
                            if (api.mode === 'visual') {
                                styles = $.extend(true, {},stack.data.astyles);
                            }
                            settings[k] = stack.data.asettings;
                        }
                        if (api.mode === 'visual') {
                            api.liveStylingInstance.doUndo(styles);
                        }
                        model.set(settings, {silent: true});
                        settings = styles = null;
                        this.set(el);
                        item.replaceWith(el);
                    }
                }
                else if (type === 'predesign') {
                    
                    var rows = stack.data.rows;
                    if (is_undo) {
                        this.doScroll( $('.tb_element_cid_' + rows[0].data('cid')));
                        for (var i = 0, len = rows.length; i < len; ++i) {
                            $('.tb_element_cid_' + rows[i].data('cid')).remove();
                        }
                    }
                    else {
                        var fragment = document.createDocumentFragment(),
                                el = [];
                        for (var i = 0, len = rows.length; i < len; ++i) {
                            var row = comon.clone(rows[i]);
                            fragment.appendChild(row[0]);
                            el.push(row);
                        }
                        if (stack.data.prev !== false) {
                            this.doScroll($('.tb_element_cid_' + stack.data.prev)).after(fragment);
                        }
                        else {
                            this.doScroll((api.mode==='visual'?$('#themify_builder_content-' + stack.data.bid):$('#tb_row_wrapper'))).prepend(fragment);
                        }
                        for (var i = 0, len = el.length; i < len; ++i) {
                            this.set(el[i]);
                            api.Mixins.Builder.update(el[i]);
                        }
                    }
                }
                else if (type === 'import') {
                    var $builder = $('[data-postid="' + stack.data.bid + '"]'),
                        $elements = is_undo ? stack.data.before : stack.data.after,
                        self = this;
                    $elements = comon.clone($elements);
                    $builder.children().remove();
                    $builder.prepend($elements);
                    $elements.each(function () {
                        self.set($(this));
                    });
                }
                else if(type==='grid_sort'){
                    if(is_undo){
                        $('.tb_element_cid_' + stack['cid']).remove();
                    }
                    else{
                        var next = $('.tb_element_cid_' + stack.before),
                            el = comon.clone(stack.after),
                            cid = stack['cid'];
                        if(stack.data.next){
                            next.before(el);
                        }
                        else{
                            next.after(el);
                        }
                        this.set(el);
                    }
                }
                if (cid) {
                    api.Mixins.Builder.update($(el));
                }
                if (is_undo) {
                    --this.index;
                }
                else {
                    ++this.index;
                }
                this.is_working = false;
                this.updateUndoBtns();
                api.toolbar.pageBreakModule.countModules();
            }
        },
        hasRedo: function () {
            return this.index < (this.stack.length - 1);
        },
        hasUndo: function () {
            return this.index !== -1;
        },
        disable: function () {
            this.btnUndo.classList.add('tb_disabled');
            this.btnRedo.classList.add('tb_disabled');
            this.compactBtn.classList.add( 'tb_disabled');
        },
        updateUndoBtns: function () {
            var undo = this.hasUndo(),
                redo = this.hasRedo();
            if(undo){
                this.btnUndo.classList.remove( 'tb_disabled');
            }
            else{
                this.btnUndo.classList.add( 'tb_disabled');
            }
            if(redo){
                this.btnRedo.classList.remove( 'tb_disabled');
            }
            else{
                this.btnRedo.classList.add( 'tb_disabled');
            }
            if(undo || redo){
                this.compactBtn.classList.remove( 'tb_disabled');
            }
            else{
                this.compactBtn.classList.add( 'tb_disabled');
            }
        },
        reset: function () {
            this.stack = [];
            this.index = -1;
            this.updateUndoBtns();
        },
        do_change: function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (this.is_working === false && !e.currentTarget.classList.contains('tb_disabled')) {
                this.changes(e.currentTarget.classList.contains('tb_undo_btn'));
            }
        }
    };
    // Validators
    api.Forms.register_validator = function (type, fn) {
        this.Validators[ type ] = fn;
    };
    api.Forms.get_validator = function (type) {
        return this.Validators[type] !== undefined ? this.Validators[ type ] : this.Validators.not_empty; // default
    };

    api.Forms.register_validator('email', function (value) {
        var pattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                arr = value.split(','),
                errors = $.map(arr, function (v, i) {
                    return pattern.test(v) ? null : '1';
                });
        return !(errors.length > 0);
    });

    api.Forms.register_validator('not_empty', function (value) {
        return !(!value || '' === value.trim());
    });
    
    api.Views.Toolbar = Backbone.View.extend({
        events: {
            // Import
            'click .tb_import': 'import',
            // Layout
            'click .tb_load_layout': 'loadLayout',
            'click .tb_save_layout': 'saveLayout',
            // Duplicate
            'click .tb_dup_link': 'duplicate',
            'click .tb_toolbar_save': 'save',
            'click .tb_toolbar_backend_edit a': 'save',
            'click .tb_toolbar_close_btn': 'panelClose',
            'click .tb_breakpoint_switcher': 'breakpointSwitcher',
            // Zoom
            'click .tb_zoom': 'zoom',
            'click .tb_toolbar_zoom_menu_toggle': 'zoom',
            'click .tb_toolbar_builder_preview': 'previewBuilder'
        },
        render: function () {
            setTimeout(getFormTemplates,1);
            var moduleItems = [],
				that = this,
				panel = this.$el.find('.tb_module_panel_modules_wrap'),
				moduleItemTmpl = wp.template('builder_module_item_draggable');

            for (var slug in themifyBuilder.modules) {
                moduleItems.push(moduleItemTmpl({
                    slug: slug,
                    name: themifyBuilder.modules[slug].name,
                    type: (themifyBuilder.modules[slug].type ? themifyBuilder.modules[slug].type : ''),
                    favorite: +themifyBuilder.modules[slug].favorite
                }));
            }

            panel[0].innerHTML = moduleItems.join('') + '<span class="favorite-separator"></span>';
            $('#tmpl-builder_module_item_draggable').remove();
            if (api.mode === 'visual') {
                $('body', top_iframe)[0].appendChild(this.el);
            }
            moduleItems = moduleItemTmpl = null;
            setTimeout(function () {
                that.Panel.init();
                api.undoManager.init();
                function callback(){
                        new SimpleBar(panel[0]);
                        that.pageBreakModule.init();
                        that.preDesignedRows.init();
                        that.libraryItems.init();
                        that.common.init();
                        // Compact toolbar
                        setTimeout(function(){
                             that.help.init();
                        },800);
                        setTimeout(function(){
                            that.Revisions.init();
                        }, 1500);
                }
                if (api.mode === 'visual') {
                    window.top.jQuery('body').one('themify_builder_ready',callback);
                    that.unload();
                }
                else {
                    callback();
                }
            }, 1);
            // Fire Module Favorite Toggle
            $('body', top_iframe).on('click', '.tb_favorite', that.toggleFavoriteModule);
            if (api.mode === 'visual') {
                Themify.body.on('click', '.tb_favorite', that.toggleFavoriteModule);
            }
            this.autoFocus();
        },
        import: function (e) {
            e.preventDefault();
            var component = ThemifyBuilderCommon.detectBuilderComponent($(e.currentTarget)),
                    callback = null,
                    options = {
                        dataType: 'html',
                        data: {
                            action: 'builder_import',
                            type: component
                        }
                    };
            if (component !== 'file' || confirm(themifyBuilder.i18n.importFileConfirm)) {
                if (component === 'file') {
                    callback = function () {
                        api.Utils.builderPlupload('', true);
                    };
                }
                ThemifyBuilderCommon.Lightbox.open(options, null, callback);
            }
        },
        unload: function () {
            if (api.mode === 'visual') {
                document.head.insertAdjacentHTML('afterbegin', '<base target="_parent">');
            }
            window.top.onbeforeunload = function () {
                return  !api.editing && (api.hasChanged || api.undoManager.hasUndo()) ? 'Are you sure' : null;
            };
        },
        autoFocus:function(){
            this.$el.find('#tb_module_panel').on('transitionend',function(e){
              if(e.originalEvent.propertyName==='visibility' && api.activeBreakPoint==='desktop' && e.target.getAttribute('id')==='tb_module_panel'){
                $(this).find('.tb_module_panel_search_text').focus();
              }
            });
        },
        panelClose: function (e) {
            e.preventDefault();
             window.top.location.reload(true);
        },
        // Layout actions
        loadLayout: function (e) {
            e.preventDefault();
            var self = this;
            function layoutLayoutsList(preview_list) {
                preview_list.each(function (i) {
                    if (i % 4 === 0) {
                        $(this).addClass('layout-column-break');
                    }
                    else {
                        $(this).removeClass('layout-column-break');
                    }
                });
            }
            var options = self.layouts_list ? {loadMethod: 'html', data: self.layouts_list} : {data: {action: 'tb_load_layout'}};
            ThemifyBuilderCommon.Lightbox.open(options,
                    null,
                    function (lightbox) {
                        lightbox = $(lightbox);
                        var container = lightbox.find('#tb_tabs_pre-designed'),
                                filter = container.find('.tb_ui_dropdown_items');

                        /* the pre-designed layouts has been disabled */
                        if (container.length === 0) {
                            return;
                        }

                        function reInitJs() {
                            var preview_list = container.find('.layout_preview_list');
                            filter.show().find('a').on('click', function (e) {
                                e.preventDefault();
                                if (!$(this).hasClass('selected')) {
                                    var matched = preview_list;
                                    if ($(this).hasClass('all')) {
                                        matched.show();
                                    } else {
                                        preview_list.hide();
                                        matched = preview_list.filter('[data-category*="' + $(this).text() + '"]');
                                        matched.show();
                                    }
                                    layoutLayoutsList(matched);
                                    filter.find('a').removeClass('selected');
                                    $(this).addClass('selected');
                                    filter.parent().find('.tb_ui_dropdown_label').html($(this).text());
                                }
                            });
                            container.find('#tb_layout_search').on('keyup', function () {
                                var s = $.trim($(this).val()),
                                        matched = preview_list;
                                if (s === '') {
                                    matched.show();
                                } else {
                                    var selected = filter.find('a.all');
                                    if (!selected.hasClass('selected')) {
                                        selected.click();
                                    }
                                    preview_list.hide();
                                    matched = preview_list.find('.layout_title:contains(' + s + ')').closest('.layout_preview_list');
                                    matched.show();
                                }
                                layoutLayoutsList(matched);
                            });
                        }
                        if (self.layouts_list) {
                            reInitJs();
                            return;
                        }
                        ThemifyBuilderCommon.showLoader('show');
                        $.getJSON('https://themify.me/themify-layouts/index.json')
                                .done(function (data) {
                                    var template = window.top.wp.template('themify-builder-layout-item'),
                                            categories = {},
                                            html = '',
                                            parent = $(template(data));
                                    parent.find('li').each(function () {
                                        var cat = String( $(this).data('category') ).split( ',' );
                                        for (var i = 0, len = cat.length; i < len; ++i) {
                                            if ('' !== cat[i] && categories[cat[i]] === undefined) {
                                                html += '<li><a href="#">' + cat[i] + '</a></li>';
                                                categories[cat[i]] = 1;
                                            }
                                        }
                                    });
                                    categories = null;
                                    filter[0].insertAdjacentHTML('beforeend', html);
                                    container[0].insertAdjacentHTML('beforeend', parent[0].outerHTML);
                                    lightbox.find('.tb_tab').each(function () {
                                        layoutLayoutsList($(this).find('.layout_preview_list'));
                                    });
                                    self.layouts_list = lightbox[0];
                                    reInitJs();
                                    new SimpleBar(lightbox[0]);
                                })
                                .fail(function (jqxhr, textStatus, error) {
                                    ThemifyBuilderCommon.LiteLightbox.alert($('#tb_load_layout_error', container).show().text());
                                })
                                .always(function () {
                                    ThemifyBuilderCommon.showLoader('spinhide');
                                });
                    });
        },
        saveLayout: function (e) {
            e.preventDefault();
            var options = {
                data: {
                    action: 'tb_custom_layout_form',
                    postid: themifyBuilder.post_ID
                }
            };
            ThemifyBuilderCommon.Lightbox.open(options, function () {
                api.Utils.builderPlupload('normal');
            });
        },
        // Duplicate actions
        duplicate: function (e) {
            e.preventDefault();
            var self = this;
            function duplicatePageAjax() {
                self.Revisions.ajax({action: 'tb_duplicate_page', 'tb_is_admin': 'visual' !== api.mode}, function (url) {
                    url && (window.top.location.href = $('<div/>').html(url).text());
                });
            }
            if (confirm(themifyBuilder.i18n.confirm_on_duplicate_page)) {
                api.Utils.saveBuilder(duplicatePageAjax);
            }
        },
        Revisions: {
            init: function () {
                api.toolbar.$el.find('.tb_revision').on('click', this.revision.bind(this));
                $('body', top_iframe)
                        .on('click', '.js-builder-restore-revision-btn', this.restore.bind(this))
                        .on('click', '.js-builder-delete-revision-btn', this.delete.bind(this));
            },
            revision: function (e) {
                e.preventDefault();
                if (e.currentTarget.classList.contains('tb_save_revision')) {
                    this.save();
                }
                else {
                    this.load();
                }
            },
            load: function () {
                var options = {
                    data: {
                        action: 'tb_load_revision_lists',
                        postid: themifyBuilder.post_ID,
                        tb_load_nonce: themifyBuilder.tb_load_nonce,
                    }
                };
                ThemifyBuilderCommon.Lightbox.open(options);
            },
            ajax: function (data, callback) {
                var _default = {
                    tb_load_nonce: themifyBuilder.tb_load_nonce,
                    postid: themifyBuilder.post_ID,
                };
                data = $.extend({}, data, _default);
                return $.ajax({
                    type: 'POST',
                    url: themifyBuilder.ajaxurl,
                    data: data,
                    beforeSend: function () {
                        ThemifyBuilderCommon.showLoader('show');
                    },
                    complete: function () {
                        ThemifyBuilderCommon.showLoader('hide');
                    },
                    success: function (data) {
                        if ($.isFunction(callback)) {
                            callback.call(this, data);
                        }
                    }
                });
            },
            save: function (callback) {
                var self = this;
                ThemifyBuilderCommon.LiteLightbox.prompt(themifyBuilder.i18n.enterRevComment, function (result) {
                    if (result !== null) {
                        api.Utils.saveBuilder(function () {
                            self.ajax({action: 'tb_save_revision', rev_comment: result}, callback);
                        }, 'main', 0, true);
                    }
                });
            },
            restore: function (e) {
                e.preventDefault();
                var revID = $(e.currentTarget).data('rev-id'),
                        self = this,
                        restoreIt = function () {
                            self.ajax({action: 'tb_restore_revision_page', revid: revID}, function (data) {
                                if (data.status) {
                                    api.Forms.reLoad(data, themifyBuilder.post_ID);
                                    ThemifyBuilderCommon.Lightbox.close();
                                } else {
                                    ThemifyBuilderCommon.showLoader('error');
                                    alert(data.data);
                                }
                            });
                        };

                ThemifyBuilderCommon.LiteLightbox.confirm(themifyBuilder.i18n.confirmRestoreRev, function (response) {
                    if ('yes' === response) {
                        self.save(restoreIt);
                    } else {
                        restoreIt();
                    }
                }, {
                    buttons: {
                        no: {
                            label: 'Don\'t Save'
                        },
                        yes: {
                            label: 'Save'
                        }
                    }
                });

            },
            delete: function (e) {
                e.preventDefault();
                if (!confirm(themifyBuilder.i18n.confirmDeleteRev)) {
                    return;
                }
                var $this = $(e.currentTarget),
                        self = this,
                        revID = $this.data('rev-id');
                self.ajax({action: 'tb_delete_revision', revid: revID}, function (data) {
                    if (!data.success) {
                        ThemifyBuilderCommon.showLoader('error');
                        alert(data.data);
                    }
                    else {
                        $this.closest('li').remove();
                    }
                });
            }
        },
        save: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var link = $(e.currentTarget).closest('.tb_toolbar_backend_edit').length > 0 ? $(e.currentTarget).prop('href') : false;
            if ( themifyBuilder.is_gutenberg_editor && link !== false ) {
                api.undoManager.reset();
                api._backendSwitchFrontend(link);
                return;
            }

            api.Utils.saveBuilder(function (jqXHR, textStatus) {
                if (textStatus !== 'success') {
                    alert(themifyBuilder.i18n.errorSaveBuilder);
                }
                else if (link !== false) {
                    if (api.mode === 'visual') {
                        sessionStorage.setItem('focusBackendEditor', true);
                        window.top.location.href = link;
                    } else {
                        api.undoManager.reset();
                        api._backendSwitchFrontend(link);
                    }
                }
            });
        },
        libraryItems: {
                items: [],
                is_init:null,
                init: function () {
                    $(document).one('tb_panel_tab_tb_module_panel_library_wrap', this.load.bind(this));
                },
                load: function (e, parent) {
                    var self = this; 
                    parent = $(parent).find('.tb_module_panel_library_wrap');
                    parent.addClass('tb_busy');
                    $.ajax({
                        type: 'POST',
                        url: themifyBuilder.ajaxurl,
                        data: {
                            action: 'tb_get_library_items',
                            nonce: themifyBuilder.tb_load_nonce,
                            part:'all',
                            pid: themifyBuilder.post_ID
                        },
                        success: function (data) {
                            self.setData(data);
                            parent.removeClass('tb_busy');
                            self.is_init = true;
                        },
                        error: function () {
                            parent.removeClass('tb_busy');
                            ThemifyBuilderCommon.showLoader('error');
                            self.init();
                            api.toolbar.$el.find('.tb_library_item_list').html('<h3>Failed to load Library Items.</h3>');
                        }
                    });
                },
                get:function(id,type,callback){
                    if(this.items[id]!==undefined){
                        callback(this.items[id]);
                    }
                    else{
                        var self = this;
                        $.ajax({
                            type: 'POST',
                            url: themifyBuilder.ajaxurl,
                            dataType:'json',
                            data: {
                                action: 'tb_get_library_item',
                                nonce: themifyBuilder.tb_load_nonce,
                                type:type,
                                id: id
                            },
                            beforeSend: function (xhr) {
                                ThemifyBuilderCommon.showLoader('show');
                            },
                            success: function (data) {
                                ThemifyBuilderCommon.showLoader('hide');
                                if(data.status==='success'){
                                    self.items[id] = data.content;
                                    callback(data.content);
                                }
                                else{
                                    ThemifyBuilderCommon.showLoader('error');
                                }
                            },
                            error: function () {
                                ThemifyBuilderCommon.showLoader('error');
                            }
                        });
                    }
                },
                template:function(data){
                    var html='';
                    for (var i = 0, len = data.length; i < len; ++i) {
                        var type = 'part';
                        if(data[i].post_type.indexOf('_rows',5)!==-1){
                            type = 'row';
                        }
                        else if(data[i].post_type.indexOf('_module',5)!==-1){
                            type = 'module';
                        }
                        html += '<div class="tb_library_item tb_item_' + type + '" data-type="' + type + '" data-id="'+data[i].id+'">';
                        html += '<div class="tb_library_item_inner"><span>' + data[i].post_title + '</span>';
                        html += '<a href="#" class="remove_item_btn" title="Delete"></a></div></div>';
                    }
                    return html;
                },
                bindEvents:function(force){
                    if(api.mode==='visual'){
                        api.Mixins.Builder.initModuleVisualDrag('.tb_item_module,.tb_item_part');
                        api.Mixins.Builder.initRowVisualDrag('.tb_item_row');
                    }
                    else{
                        api.Mixins.Builder.initRowDraggable(api.toolbar.$el.find('.tb_module_panel_library_wrap').first(),'.tb_item_row');
                        api.Mixins.Builder.initModuleDraggable(api.toolbar.$el.find('.tb_library_item_list').first(),'.tb_item_module,.tb_item_part');
                    }  
                    if(api.toolbar.common.btn || (api.mode==='visual' && (api.toolbar.common.is_init || force))){
                        api.Mixins.Builder.initRowDraggable(api.toolbar.common.btn.find('.tb_module_panel_library_wrap').first(),'.tb_item_row');
                        api.Mixins.Builder.initModuleDraggable(api.toolbar.common.btn.find('.tb_library_item_list').first(),'.tb_item_module,.tb_item_part'); 
                     }
                },
                setData: function (data) {
                        var html = '<span id="no-content" style="display:none">No library content found.</span>'+this.template(data),
                            libraryItems = $('.tb_library_item_list');
                            if(api.mode==='visual'){
                                libraryItems = libraryItems.add(api.toolbar.$el.find('.tb_library_item_list'));
                            }
                        data = null;
                        libraryItems = libraryItems.get();
                        for(var i=0,len=libraryItems.length;i<len;++i){
                            libraryItems[i].insertAdjacentHTML('afterbegin',html);
                            new SimpleBar(libraryItems[i]);
                            $(libraryItems[i]).closest('.tb_module_panel_tab').find('.tb_module_types .active a').trigger('click');
                        }
                        Themify.body.on('click','.remove_item_btn',this.delete.bind(this));
                        if(api.mode==='visual'){
                            $('body',top_iframe).on('click','.remove_item_btn',this.delete.bind(this));
                        }
                       this.bindEvents();
                },
                delete: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var elem = $(e.currentTarget).closest('.tb_library_item'), 
                        type = elem.data('type');
                    if (confirm(themifyBuilder.i18n[type + 'LibraryDeleteConfirm'])) {
                        var id = elem.data('id');
                        $.ajax({
                            type: 'POST',
                            url: themifyBuilder.ajaxurl,
                            data: {
                                action: 'tb_remove_library_item',
                                nonce: themifyBuilder.tb_load_nonce,
                                id: id
                            },
                            beforeSend: function (xhr) {
                                ThemifyBuilderCommon.showLoader('show');
                            },
                            success: function (data) {
                                ThemifyBuilderCommon.showLoader('hide');
                                if(data){
                                    var el = elem.closest('#'+api.toolbar.common.btn.prop('id')).length>0?
                                        api.toolbar.$el.find('.tb_item_'+type+'[data-id="'+id+'"]')
                                        :api.toolbar.common.btn.find('.tb_item_'+type+'[data-id="'+id+'"]');
                                    elem = elem.add(el); 
                                    if(type==='part'){
                                        elem = elem.add($('.themify_builder_content-'+id).closest('.active_module'));
                                        var control = api.Views.ControlRegistry.lookup('selected_layout_part');
                                        if(control){
                                            control.data = [];
                                        }
                                    }
                                    elem.remove();
                                }
                                else{
                                    ThemifyBuilderCommon.showLoader('error');
                                }
                            },
                            error: function () {
                                ThemifyBuilderCommon.showLoader('error');
                            }
                        });
                    }
                }
            },
            preDesignedRows: {
                is_init: null,
                rows: {},
                init: function () {
                    setTimeout(function () {
                        //resolve dns and cache predessinged rows
                        var link = '<meta http-equiv="x-dns-prefetch-control" content="on"/><link href="//themify.me" rel="dns-prefetch preconnect"/>';
                        link += '<link href="//fonts.googleapis.com" rel="dns-prefetch"/>';
                        link += '<link href="//maps.google.com" rel="dns-prefetch"/>';
                        link += '<link href="https://themify.me/public-api/predesigned-rows/index.json" rel="prefetch"/>';
                        document.head.insertAdjacentHTML('afterbegin', link);
                    }, 7000);
                    $(document).one('tb_panel_tab_tb_module_panel_rows_wrap', this.load.bind(this));
                },
                load: function (e, parent) {
                    var self = this;
                        parent = $(parent).find('.tb_predesigned_rows_list');
                        parent.addClass('tb_busy');
                    $.getJSON('https://themify.me/public-api/predesigned-rows/index.json')
                    .done(function (data) {
                       self.setData(data, parent);
                    })
                    .fail(function (jqxhr, textStatus, error) {
                        self.setData({}, parent);
                        self.is_init = null;
                        ThemifyBuilderCommon.showLoader('error');
                        api.toolbar.$el.find('.tb_predesigned_rows_container').append('<h3>Failed to load Pre-Designed Rows from server.</h3>');
                        $(document).one('tb_panel_tab_tb_module_panel_rows_wrap', self.load.bind(self));
                    });
                },
                setData: function (data) {
                    var cats = [],
                        cat_html = '',
                        html = '';
                    for (var i = 0, len = data.length; i < len; ++i) {
                        var tmp = data[i].category.split(','),
                                item_cats = '';
                        for (var j = 0, clen = tmp.length; j < clen; ++j) {
                            if (cats.indexOf(tmp[j]) === -1) {
                                cats.push(tmp[j]);
                            }
                            item_cats += ' tb' + Themify.hash(tmp[j]);
                        }
                        if (data[i].thumbnail === undefined || data[i].thumbnail === '') {
                            data[i].thumbnail = 'https://placeholdit.imgix.net/~text?txtsize=24&txt=' + (encodeURI(data[i].title)) + '&w=181&h=77';
                        }
                       if(((i+1)%4)===0){
                            item_cats+=' tb_column_break';
                        }
                        html += '<div class="predesigned_row' + item_cats + '" data-slug="' + data[i].slug + '"><figure class="tb_predesigned_rows_list_image">';
                        if(data[i].thumbnail){
                            html += '<img alt="' + data[i].title + '" title="' + data[i].title + '" src="' + data[i].thumbnail + '" />';
                        }
                        html += '</figure><div class="tb_predesigned_rows_list_title">' + data[i].title + '</div></div>';
                    }
                    data = null;
                    cats.sort();
                    for (var i = 0, len = cats.length; i < len; ++i) {
                        cat_html += '<li><a href="#" data-slug="' + Themify.hash(cats[i]) + '">' + cats[i] + '</li>';
                    }
                    var filter = $('.tb_row_filter'),
                        predesigned = $('.tb_predesigned_rows_container'),
                        self = this;
                    if(api.mode==='visual'){
                        predesigned = predesigned.add( api.toolbar.$el.find('.tb_predesigned_rows_container'));
                        filter = filter.add( api.toolbar.$el.find('.tb_row_filter'));
                    }
                    filter = filter.get();
                    predesigned = predesigned.get();
                    for(var i=0,len=filter.length;i<len;++i){
                        filter[i].insertAdjacentHTML('beforeend', cat_html);
                        predesigned[i].innerHTML = html;
                        var img = predesigned[i].getElementsByTagName('img');
                         if(img.length>0) {
                            img = img[img.length-1];
                            $(img).one( 'load', function(){
                                callback($(this).closest('.tb_predesigned_rows_container')[0]);
                            });
                        }else{
                            callback(predesigned[i]);
                        }
                    }
                    function callback(el){
                        new SimpleBar( el );
                        if(self.is_init!==true){
                            if(api.mode==='visual'){
                                api.Mixins.Builder.initRowVisualDrag('.predesigned_row');
                            }
                            else{
                                api.Mixins.Builder.initRowDraggable(api.toolbar.$el.find('.tb_predesigned_rows_container').first(),'.predesigned_row');
                            }
                            if(api.toolbar.common.is_init){
                               api.Mixins.Builder.initRowDraggable(api.toolbar.common.btn.find('.tb_predesigned_rows_container').first(),'.predesigned_row');
                            }
                            self.is_init = true;
                        }
                        $(el).closest('.tb_predesigned_rows_list').removeClass('tb_busy').closest('.tb_module_panel_tab').find('.tb_ui_dropdown').css('visibility','visible');
                    }
                    Themify.body.on('click','.tb_row_filter a',this.filter.bind(this));
                    if(api.mode==='visual'){
                        $('body',top_iframe).on('click','.tb_row_filter a',this.filter.bind(this));
                    }
                },
                get: function (slug, callback) {
                    ThemifyBuilderCommon.showLoader('show');
                    if (this.rows[slug] !== undefined) {
                        if (typeof callback === 'function') {
                            callback(this.rows[slug]);
                        }
                        return;
                    }
                    var self = this;
                    $.getJSON('https://themify.me/public-api/predesigned-rows/' + slug + '.txt')
                        .done(function (data) {
                            self.rows[slug] = data;
                            if (typeof callback === 'function') {
                                callback(data);
                            }

                        }).fail(function (jqxhr, textStatus, error) {
                            ThemifyBuilderCommon.showLoader('error');
                            alert('Failed to fetch row template');
                    });
                },
                filter: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var el = $(e.currentTarget),
                            slug = el.data('slug'),
                            parent = el.closest('.tb_modules_panel_wrap'),
                            active = parent.find('.tb_ui_dropdown_label'),
                            rows = parent.find('.predesigned_row');
                    active.text(el.text());
                    parent.find('.tb_module_panel_search_text').val('');
                    var cl=slug?'tb' + slug:false;
                    active.data('active',cl);
                    rows.each(function () {
                        if (!cl || $(this).hasClass(cl)) {
                            $(this).show();
                        }
                        else {
                            $(this).hide();
                        }
                    }).filter(':visible').each(function(i){
                        if(((i+1)%4)===0){
                            $(this).addClass('tb_column_break');
                        }
                        else{
                            $(this).removeClass('tb_column_break');
                        }
                    });
            }
        },
        pageBreakModule: {
            init: function () {
                if (api.mode === 'visual') {
                    api.Mixins.Builder.initRowVisualDrag('.tb_page_break_module');
                }
                else {
                    api.Mixins.Builder.initRowDraggable(api.toolbar.$el.find('.tb_module_panel_rows_wrap').first(), '.tb_page_break_module');
                }
            },
            countModules: function () {
                var $pageBreakModules = (api.mode === 'visual') ? jQuery(self.frameElement).contents().find('.module-page-break') : $('.module_row.tb-page-break'),
                    counter = 1;
                $pageBreakModules.each(function(){
                    if (api.mode === 'visual') {
                        $(this).find('.page-break-order').text(counter);
                    }else{
                        $(this).find('.page-break-overlay').text('PAGE BREAK - ' + counter);
                    }
                    counter = counter + 1;
                })
                counter = 1;
            },
            get: function (callback) {
                ThemifyBuilderCommon.showLoader('show');
                var data = [
                    {
                      row_order: '0',
                      cols: [
                        {
                          column_order: '0',
                          grid_class: 'col-full first last',
                          modules: [
                            {
                              mod_name: 'page-break',
                              mod_settings: {}
                            }
                          ],
                          styling: {}
                        }
                      ],
                      column_alignment: 'col_align_middle',
                      styling: {
                        custom_css_row: 'tb-page-break'
                      }
                    },
                    {
                      row_order: '1',
                      cols: [
                        {
                          column_order: '0',
                          grid_class: 'col-full'
                        }
                      ]
                    }
                  ];
                if (typeof callback === 'function') {
                    callback(data);
                }
                return;
            },
        },
        common: {
            btn: null,
            is_init:null,
            clicked: null,
            init: function(){
                this.btn = $('<div class="tb_modules_panel_wrap" id="tb_plus_btn_popover"></div>');
                if ( $('.edit-post-layout__content').length>0) {
                    $('.edit-post-layout__content').append(this.btn);
                } else {
                        Themify.body.append(this.btn);
                }
                var self = this;
                if (api.mode === 'visual') {
                    api.toolbar.$el.find('.tb_module_types a').on('click', this.tabs.bind(this));
                }
                Themify.body.on('click', '.tb_module_types a', this.tabs.bind(this)).on('click', '.tb_row_btn_plus', this.show.bind(this));
                api.toolbar.$el.find('.tb_module_panel_search_text').on('keyup', this.search.bind(this));
                this.btn.on('click', '.add_module_btn', function (e) {
                    api.toolbar.Panel.add_module(e, self.clicked.closest('.module_row').find('.tb_holder').last());
                    self.clicked = null;
                    self.btn.hide();
                }).on('keyup', '.tb_module_panel_search_text', this.search.bind(this));
            },
            run:function(){
                    var markup = api.toolbar.$el.find('#tb_module_panel');
                    this.btn[0].insertAdjacentHTML('beforeend', markup[0].innerHTML);
                    this.btn.find('.tb_module_panel_lock').remove();
                    this.btn.find('.tb_module_outer').show();
                    this.btn.find('.tb_module_panel_search_text').val('');
                    new SimpleBar(this.btn.find('.tb_module_panel_modules_wrap')[0]);
                    api.Mixins.Builder.initModuleDraggable(this.btn,'.tb_module');
                    api.Mixins.Builder.initModuleDraggable(this.btn.find('.tb_rows_grid').first(),'.tb_row_grid');
                    if(api.toolbar.libraryItems.is_init || api.mode==='visual'){
                        api.Mixins.Builder.initModuleDraggable(this.btn.find('.tb_library_item_list').first(),'.tb_item_module,.tb_item_part');
                        api.Mixins.Builder.initRowDraggable(this.btn.find('.tb_library_item_list').first(),'.tb_item_row');
                    }
                    if(api.toolbar.preDesignedRows.is_init || api.mode==='visual'){
                        api.Mixins.Builder.initRowDraggable(this.btn.find('.tb_predesigned_rows_container').first(),'.predesigned_row');
                    }
                    this.is_init = true;
                    api.Mixins.Builder.initRowDraggable(api.toolbar.common.btn.find('.tb_module_panel_rows_wrap').first(),'.tb_page_break_module');
            }, 
            tabs: function (e) {
                e.preventDefault();
                e.stopPropagation();
                var elm = $(e.currentTarget),
                    target = elm.data('target'),
                    parent = elm.closest('.tb_modules_panel_wrap');
                parent.find('.'+elm.data('hide')).hide();
                var items  = parent.find('.' + target),
                    not_found = parent.find('#no-content');
                if(items.length>0){
                    not_found.hide();
                    items.show();
                }
                else{
                    not_found.show();
                }
                elm.closest('li').addClass('active').siblings().removeClass('active');
                parent.find('.tb_module_panel_search_text').val('').focus();
                $(document).trigger('tb_panel_tab_' + target, parent);
            },
            show: function (e) {
                e.preventDefault();
                e.stopPropagation();
                if(this.is_init===null){
                    this.run();
                }
                this.clicked = $(e.currentTarget);
                var self = this,
                    offset = this.clicked.offset(),
                    left=offset.left,top=offset.top;
                if ( $('.edit-post-layout__content').length>0 ) {
                    var $guten_container = $('.edit-post-layout__content');
                    top += $guten_container.scrollTop() - 70;
                    left = ( $guten_container.width() / 2 ) - 11;
                }
                else if(api.Forms.LayoutPart.el){
                    this.btn.width(api.Forms.LayoutPart.el.width());
                }
                left = (left - (this.btn.width() / 2) + 11);
                if(left<0){
                    left = 0;
                }
                this.btn.css({top: top, left: left }).show();
                if (api.mode === 'visual') {
                    if(api.activeBreakPoint !== 'desktop'){
                        $('body', top_iframe).height(document.body.scrollHeight + self.btn.outerHeight(true));
                        Themify.body.css('padding-bottom', 180);
                    }
                }
                if(api.activeBreakPoint === 'desktop'){
                    this.btn.find('.tb_module_panel_search_text').focus();
                }
                this.hide();
            },
            hide: function () {
                var self = this;
                function callback(e) {
                    if (!self.btn.is(':hover')) {
                        self.btn.hide().css('width','');
                        self.clicked = null;
                        $(document).off('click', callback);
                        $(top_iframe).off('click', callback);
                        if (api.mode === 'visual' && api.activeBreakPoint !== 'desktop') {
                            $('body', top_iframe).height(document.body.scrollHeight);
                            Themify.body.css('padding-bottom', '');
                        }
                    }
                }
                if (api.mode === 'visual') {
                    $(top_iframe).on('click', callback);
                }
                $(document).on('click', callback);
            },
            search: function (e) {
                var el = $(e.currentTarget),
                        parent = el.closest('.tb_modules_panel_wrap'),
                        target = parent.find('.tb_module_types .active').first().find('a').data('target'),
                        search=false,
                        filter=false,
                        is_module=false,
                        is_library=false,
                        s = $.trim(el.val());
                        if(target==='tb_module_panel_modules_wrap'){
                            search = parent.find('.tb_module_outer');
                            is_module = true;
                        }
                        else if(target==='tb_module_panel_rows_wrap' && api.toolbar.preDesignedRows.is_init){
                            filter = parent.find('.tb_ui_dropdown_label').data('active');
                            search = parent.find('.predesigned_row');
                        }
                        else if(target==='tb_module_panel_library_wrap'){
                            search = parent.find('.tb_library_item');
                            filter = parent.find('.tb_library_types .active a').data('target');
                            is_library = true;
                        }
                if (search !== false) {
                    var is_empty = s === '',
                        reg = !is_empty ? new RegExp(s, 'i') : false;
                    search.each(function () {
                        if (filter && !$(this).hasClass(filter)) {
                            return true;
                        } 
                        var elm = is_module ? $(this).find('.module_name') : (is_library ? $(this).find('.tb_library_item_inner span') : $(this).find('.tb_predesigned_rows_list_title'));
                        if (is_empty || reg.test(elm.text())) {
                            $(this).show();
                        }
                        else {
                            $(this).hide();
                        }
                    });
                }
            }
        },
        help:{
            init:function(){
                this.welcome();
                $('.tb_help_btn',api.toolbar.$el).on('click',this.show.bind(this));
            },
            is_clicked:null,
            show:function(e){
                e.preventDefault();
                e.stopPropagation();  
                var self = this;
                ThemifyBuilderCommon.showLoader('show');
               return $.ajax({
                    type: 'POST',
                    url: themifyBuilder.ajaxurl,
                    data:{tb_load_nonce: themifyBuilder.tb_load_nonce,action:'tb_help'},
                    complete:function(){
                        ThemifyBuilderCommon.showLoader('spinhide');
                    },
                    success:function(data){
                        top_iframe.body.insertAdjacentHTML('beforeend',data);
                        var $wrapper = $('#tb_help_videos',top_iframe.body);
                        $('.tb_player_btn',$wrapper).click(self.play.bind(self));
                        $('.tb_help_menu a',$wrapper).click(self.tabs.bind(self));
                        $('.tb_close_lightbox',$wrapper).click(self.close.bind(self));
                        if(!self.is_clicked){
                            $wrapper.slideDown();
                        }
                    }
                });
            },
            play:function(e){
                e.preventDefault();
                e.stopPropagation();
                var a = $(e.currentTarget).closest('a'),
                    href = a.prop('href'),
                    iframe = document.createElement( 'iframe' );
                    iframe.setAttribute( 'frameborder', '0' );
                    iframe.setAttribute( 'allow', 'autoplay; fullscreen' );
                    iframe.setAttribute('src', href+'?rel=0&showinfo=0&autoplay=1&enablejsapi=1&html5=1&version=3');
                    a.replaceWith(iframe);
                    
            },
            tabs:function(e){
                e.preventDefault();
                e.stopPropagation();  
                var $this = $(e.currentTarget),
                    wrapper = $('.tb_help_video_wrapper',top_iframe),
                    active =  wrapper.find($this.attr('href')),
                    activePlayer = active.find('.tb_player_btn');
                wrapper.find('.tb_player_wrapper').removeClass('active').hide();
                active.addClass('active').show();
                $this.closest('li').addClass('current').siblings().removeClass('current');
                this.stopPlay();
                if(activePlayer.length>0){
                    activePlayer.trigger('click');
                }
                else{
                    this.startPlay();
                }
            },
            execute:function(iframe,param){
                iframe.contentWindow.postMessage('{"event":"command","func":"' + param + '","args":""}', '*');
            },
            stopPlay:function(){
                var self = this;
                $('.tb_player_wrapper',top_iframe).each(function(){
                    if(!$(this).hasClass('active')){
                        var iframe = $(this).find('iframe');
                        if(iframe.length>0){
                            self.execute(iframe[0],'pauseVideo');
                        }
                    }
                });
            },
            startPlay:function(){
                var iframe =  $('.tb_player_wrapper.active',top_iframe).find('iframe');
                iframe.length>0 && this.execute(iframe[0],'playVideo');
            },
            close:function(e,callback){
                e.preventDefault();
                e.stopPropagation();
                $(e.currentTarget).closest('.tb_help_lightbox').slideUp('normal',function(){
                    $(this).next('.tb_overlay').remove();
                    $(this).empty().remove();
                    if(callback){
                        callback();
                    }
                });
            },
            welcome:function(){ 
                var $wrapper = $('#tb_first_help');
                if($wrapper.length>0){
                    var self = this;
                    top_iframe.body.insertAdjacentHTML('beforeend',$wrapper[0].outerHTML+'<div class="tb_overlay"></div>');
                    $wrapper.remove();
                    $wrapper =  $('#tb_first_help',top_iframe);
                    $wrapper.slideDown('normal',function(){
                        $('.tb_close_lightbox,.tb_start_building',$(this)).click(self.close.bind(self));
                        $('.tb_player_btn,.tb_watch',$(this)).click(self.watch.bind(self));
                        document.cookie = 'tb_first_on=1;expires=Tue, 19 Jan 2038 03:14:07 UTC;path=/';
                    });
                }
            },
            watch:function(e){
                e.preventDefault();
                e.stopPropagation();
                this.is_clicked = true;
                var self = this;
                this.show(e).done(function(){
                    self.close(e,function(){
                        $('#tb_help_videos',top_iframe.body).slideDown('normal',function(){
                            $(this).find('.tb_player_wrapper.active').find('.tb_player_btn').trigger('click');
                            self.is_clicked =null;
                        });
                    });
                });
                
            }
            
        },
        breakpointSwitcher: function (e) {
            e.preventDefault();


            if(!themifyBuilder.is_premium){
                return;
            }
            var w = '',
                    self = this,
                    breakpoint = 'desktop',
                    $this = $(e.currentTarget),
                    $body = $('body', top_iframe),
                    prevBreakPoint = api.activeBreakPoint;

	        var breaks = ['desktop','mobile','tablet','tablet_landscape'];
	        breaks.forEach(function (item) {
		        if($body.hasClass('builder-breakpoint-' + item))
			        prevBreakPoint = item;
	        });
            function callback() {
                self.responsive_grids(breakpoint, prevBreakPoint);
                Themify.body.trigger('themify_builder_change_mode', [prevBreakPoint, breakpoint]);
                if (api.mode === 'visual') {
                    api.iframe.css('will-change', 'auto');
                    api.Mixins.Builder.updateModuleSort(null, breakpoint === 'desktop' ? 'enable' : 'disable');
                    api.Utils._onResize(true, function () {
                        self.iframeScroll(breakpoint !== 'desktop');
                        $('body', top_iframe).height(breakpoint !== 'desktop' ? document.body.scrollHeight : 'auto');
                        api.scrollTo && setTimeout(function () {
                            api.scrollTo && $(window).add(top_iframe).scrollTop(api.scrollTo.offset().top);
                            api.scrollTo = false;
                        }, 500);

                        setTimeout(function () {
                            api.Utils.setCompactMode(document.getElementsByClassName('module_column'));
                            $body.removeClass('tb_start_animate');
                        }, 200);
                    });
                } else {
                    $body.removeClass('tb_start_animate');
                }
                api.toolbar.$el.find('.tb_compact_switcher i').prop('class',$this.find('i').prop('class'));
                $body
                        .toggleClass('tb_responsive_mode', breakpoint !== 'desktop')
                        .removeClass('builder-breakpoint-' + prevBreakPoint)
                        .addClass('builder-breakpoint-' + breakpoint);
            }

            if ($this.hasClass('breakpoint-tablet')) {
                breakpoint = 'tablet';
            } else if ($this.hasClass('breakpoint-tablet_landscape')) {
                breakpoint = 'tablet_landscape';
            } else if ($this.hasClass('breakpoint-mobile')) {
                breakpoint = 'mobile';
            }

            if (prevBreakPoint === breakpoint && e.originalEvent !== undefined)
                return false;
            api.activeBreakPoint = breakpoint;
            api.mode === 'visual' && ($body = $body.add(Themify.body));
            $body.addClass('tb_start_animate'); //disable all transitions
            breakpoint !== 'desktop' && (w = api.Utils.getBPWidth(breakpoint) - 1);

            if (api.mode === 'visual') {
                // disable zoom if active
                $('.tb_toolbar_zoom_menu', top_iframe).removeClass('tb_toolbar_zoom_active').find('.tb_toolbar_zoom_menu_toggle').data('zoom', 100);
                var wspace = $('.tb_workspace_container', top_iframe).width();
                w = ('tablet_landscape' === breakpoint && wspace < w && ThemifyBuilderCommon.Lightbox.dockMode.get() ) ? wspace : w; // make preview fit the screen when dock mode active

                if( api.isPreview ) {
                        var previewWidth = {
                                'tablet_landscape': themifyBuilder.breakpoints.tablet_landscape[1],
                                'tablet': themifyBuilder.breakpoints.tablet[1],
                                'mobile': themifyBuilder.breakpoints.mobile
                        };

                        if( breakpoint in previewWidth ) {
                                w = previewWidth[breakpoint];
                        }
                }
				api.iframe.css('width', w).parent().removeClass('tb_zoom_bg');

                // Avoid iframe resize when errors in the callback
                try {
                    if( w && api.iframe.width() != w ){
                        api.iframe.css( 'will-change', 'width' ).one( api.Utils.transitionPrefix(), callback );
                    } else {
                        callback();
                    }
                } catch( e ) {
                    api.iframe.css( 'width', wspace);
                }

            }
            else {
                callback();
            }
        },
        iframeScroll: function (init) {
            var top = $(top_iframe);
            top.off('scroll.themifybuilderresponsive');
            if (init) {
                top.on('scroll.themifybuilderresponsive', function () {
                    window.scrollTo(0, $(this).scrollTop());
                });
            }
        },
        responsive_grids: function (type, prev) {
            var rows = document.querySelectorAll('.row_inner,.subrow_inner'),
                    is_desktop = type === 'desktop',
                    set_custom_width = is_desktop || prev === 'desktop';
            for (var i = 0, len = rows.length; i < len; ++i) {
                var base = rows[i].getAttribute('data-basecol');
                if (base) {
                    var columns = rows[i].children,
                            grid = rows[i].dataset['col_' + type],
                            first = columns[0],
                            last = columns[columns.length - 1];
                    if (!is_desktop) {
                        if (prev !== 'desktop') {
                            rows[i].classList.remove('tb_3col');
                            var prev_class = rows[i].getAttribute('data-col_' + prev);
                            if (prev_class) {
                                rows[i].classList.remove($.trim(prev_class.replace('tb_3col', '').replace('mobile', 'column').replace('tablet', 'column')));
                            }
                        }
                        if (!grid || grid === '-auto') {
                            rows[i].classList.remove('tb_grid_classes');
                            rows[i].classList.remove('col-count-' + base);
                        }
                        else {
                            var cl = rows[i].getAttribute('data-col_' + type);
                            if (cl) {
                                rows[i].classList.add('tb_grid_classes');
                                rows[i].classList.add('col-count-' + base);
                                cl = cl.split(' ');

								cl.map( function( el ) {
									rows[i].classList.add( $.trim( el.replace( 'mobile', 'column' ).replace( 'tablet', 'column' ) ) );
								} );
                            }
                        }
                    }
                    if (set_custom_width) {
                        for (var j = 0, clen = columns.length; j < clen; ++j) {
                            var w = $(columns[j]).data('w');
                            if (w !== undefined) {
                                if (is_desktop) {
                                    columns[j].style['width'] = w + '%';
                                }
                                else {
                                    columns[j].style['width'] = '';
                                }
                            }
                        }
                    }
                    var dir = rows[i].getAttribute('data-' + type + '_dir');
                    if (dir === 'rtl') {
                        first.classList.remove('first')
                        first.classList.add('last');
                        last.classList.remove('last')
                        last.classList.add('first');
                        rows[i].classList.add('direction-rtl');
                    }
                    else {
                        first.classList.remove('last')
                        first.classList.add('first');
                        last.classList.remove('first')
                        last.classList.add('last');
                        rows[i].classList.remove('direction-rtl');
                    }
                }
            }
        },
        Panel: {
            el: null,
            is_locked: false,
            key: 'tb_module_panel_locked',
            init: function () {
                this.el = api.toolbar.$el.find('.tb_toolbar_add_modules_wrap');
                this.el.find('.tb_module_panel_lock').on('click', this.lock.bind(this));
                this._setupModulePanelState();
                this.el.find('.add_module_btn').on('click', this.add_module);
                this.compactToolbar();
            },
            add_module: function (e, holder) {
                e.preventDefault();
                e.stopPropagation();
                holder = holder || api.Instances.Builder[0].$el.find('.module_row').last().find('.tb_holder').first();
                var top = holder.offset().top - 37;

                if (api.mode === 'visual') {
                    if (api.activeBreakPoint !== 'desktop') {
                        $(top_iframe).scrollTop(top);
                    }
                }
                else {
                    top -= 50;
                }
                $(window).scrollTop(top);
                api.Mixins.Builder.moduleDrop($(e.currentTarget).closest('.tb_module'), holder);
            },
            compactToolbar:function(){
                var barLimit = api.mode === 'visual' ? 850 : 750;
                function callback(){
                     api.toolbar.$el.outerWidth() < barLimit?top_iframe.body.classList.add('tb_compact_toolbar'): top_iframe.body.classList.remove('tb_compact_toolbar'); 
                 }
                 $(window.top).on('tfsmartresize.compact',callback);  
                 if(api.mode==='visual'){
                     window.top.jQuery('body').one( 'themify_builder_ready', callback );
                 }
                 else{
                     callback();
                 }
            },
            resetPanel: function () {
                if (this.is_locked) {
                    this.toggleLock();
                }
            },
            toogle: function (e) {
                if (!this.is_locked) {
                    $(e.currentTarget).focus();
                    if(api.toolbar.$el.find('ul.tb_module_types li.active a').data('target') === 'tb_module_panel_library_wrap') {
                        $(document).trigger('change:tab:tb_module_panel_library_wrap', api.toolbar.$el.find( '#tb_module_panel.tb_modules_panel_wrap' ) );
                    }
                }
            },
            lock: function (e) {
                e.preventDefault();
                this.is_locked = !this.is_locked;
                localStorage.setItem(this.key, this.is_locked);
                this.toggleLock();
                if (!this.is_locked) {
                    this.el.addClass('tb_hide_panel');
                    var self = this;
                    setTimeout(function () {
                        api.toolbar.$el.find('#tb_switch_backend').focus();
                        self.el.removeClass('tb_hide_panel');
                    }, 1000);
                }
            },
            toggleLock: function () {
                var tollbars = api.toolbar.$el;
                if (api.mode === 'visual') {
                    tollbars = tollbars.add(Themify.body);
                    tollbars.addClass('tb_remove_transitions');
                }
                $('body', top_iframe).toggleClass('tb_module_panel_locked');
                $(window).trigger('tfsmartresize');
                tollbars.removeClass('tb_remove_transitions');
            },
            _setupModulePanelState: function () {
                this.is_locked = localStorage.getItem(this.key);
                if (this.is_locked === 'false') {
                    this.is_locked = false;
                }	
                if (this.is_locked) {
                    setTimeout(this.toggleLock, 1200);
                }
            },
            hide: function () {
                this.el.blur();
                if (this.is_locked) {
                    var tollbars = api.toolbar.$el;
                    if (api.mode === 'visual') {
                        tollbars = tollbars.add(Themify.body);
                        tollbars.addClass('tb_remove_transitions');
                    }
                    $('body', top_iframe).removeClass('tb_module_panel_locked');
                    $(window).trigger('tfsmartresize');
                    tollbars.removeClass('tb_remove_transitions');
                }
            },
            show: function () {
                if (!this.is_locked) {
                    this.el.focus();
                }
            }
        },
        toggleFavoriteModule: function () {
            var $this = $(this),
                    moduleBox = $this.closest('.tb_module_outer'),
                    slug = $this.parent().data('module-slug');

            $.ajax({
                type: 'POST',
                url: themifyBuilder.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'tb_module_favorite',
                    module_name: slug,
                    module_state: +!moduleBox.hasClass('favorited')
                },
                beforeSend: function (xhr) {
                    var prefix = api.Utils.transitionPrefix();
                    function callback(box, repeat) {

                        function finish() {
                            box.removeAttr('style');
                            if (repeat) {
                                var p = box.closest('#tb_plus_btn_popover').length > 0 ? api.toolbar.$el : $('#tb_plus_btn_popover');
                                callback(p.find('.tb-module-type-' + slug).closest('.tb_module_outer'), false);
                            }
                        }
                        if (!box.is(':visible')) {
                            box.toggleClass('favorited');
                            finish();
                            return;
                        }
                        box.css({
                            opacity: 0,
                            transform: 'scale(0.5)'
                        }).one(prefix, function () {
                            box.toggleClass('favorited').css({
                                opacity: 1,
                                transform: 'scale(1)'
                            }).one(prefix, finish);
                        });
                    }
                    callback(moduleBox, true);
                }
            });
        },
        zoom: function (e) {
            e.preventDefault();
            if ('desktop' !== api.activeBreakPoint)
                return true;
            function callback(){
                    api.Utils._onResize(true);
            }
            var $link,
                    $this = $(e.currentTarget),
                    zoom_size = $this.data('zoom'),
                    $canvas = $('.tb_iframe', top_iframe),
                    $parentMenu = $this.closest('.tb_toolbar_zoom_menu');

            if ($this.hasClass('tb_toolbar_zoom_menu_toggle')) {
                zoom_size = '100' == zoom_size ? 50 : 100;
                $this.data('zoom', zoom_size);
                $link = $this.next('ul').find('[data-zoom="' + zoom_size + '"]');
            } else {
                $link = $this;
                $parentMenu.find('.tb_toolbar_zoom_menu_toggle').data('zoom', zoom_size);
            }

            $canvas.removeClass('tb_zooming_50 tb_zooming_75');
            $link.parent().addClass('selected-zoom-size').siblings().removeClass('selected-zoom-size');
            if ('50' == zoom_size || '75' == zoom_size) {
                var scale = '50' == zoom_size ? 2 : 1.25;
                $canvas.addClass('tb_zooming_' + zoom_size).one(api.Utils.transitionPrefix(), callback).parent().addClass('tb_zoom_bg')
                        .css('height', Math.max(window.top.innerHeight * scale, 600));
                $parentMenu.addClass('tb_toolbar_zoom_active');
                api.zoomMeta.isActive = true;
                api.zoomMeta.size = zoom_size;
                Themify.body.addClass('tb_zoom_only');
            }
            else {
                $canvas.addClass('tb_zooming_' + zoom_size).one(api.Utils.transitionPrefix(), callback).parent().css('height', '');
                $parentMenu.removeClass('tb_toolbar_zoom_active');
                api.zoomMeta.isActive = false;
                Themify.body.removeClass('tb_zoom_only');
            }
        },
        previewBuilder: function (e) {
            e.preventDefault();
            function hide_empty_rows() {
                if (api.isPreview) {
                    var row_inner = $('.col-count-1.row_inner');
                    row_inner.each(function () {
                        if (this.getElementsByClassName('active_module').length === 0) {
                            var column = this.getElementsByClassName('module_column')[0],
                                    mcolumn = api.Models.Registry.lookup(column.dataset.cid);
                            if (mcolumn && Object.keys(mcolumn.get('styling')).length === 0) {
                                var row = $(this).closest('.module_row'),
                                        mrow = api.Models.Registry.lookup(row.data('cid'));
                                if (mrow && Object.keys(mrow.get('styling')).length === 0) {
                                    row.addClass('tb_hide');
                                }
                            }

                        }
                    });
                }
                else {
                    $('.tb_hide.module_row').removeClass('tb_hide');
                }
            }
            $(e.currentTarget).toggleClass('tb_toolbar_preview_active');
            api.isPreview = !api.isPreview;
            Themify.body.toggleClass('tb_preview_only themify_builder_active');
            $('body', top_iframe).toggleClass('tb_preview_parent');

            if (api.isPreview) {
                this.Panel.hide();
            } else {
                this.Panel.resetPanel();
            }
            hide_empty_rows();
            $('.builder-breakpoint-' + api.activeBreakPoint + ' a.breakpoint-' + api.activeBreakPoint, top_iframe).trigger('click');
            api.vent.trigger('dom:preview');
        }
    });

    api.Views.bindEvents = function () {
        ThemifyBuilderCommon.Lightbox.setup();
        ThemifyBuilderCommon.LiteLightbox.modal.on('attach', function () {
            this.$el.addClass('tb_lite_lightbox_modal');
        });
        api.Utils.mediaUploader();
        api.Utils.openGallery();
    };

    api.Utils = {
        onResizeEvents: [],
        gridClass: ['col-full', 'col2-1', 'col3-1', 'col4-1', 'col5-1', 'col6-1', 'col4-2', 'col4-3', 'col3-2'],
        _onResize: function (trigger, callback) {
            var events = $._data(window, 'events')['resize'];
            $(window.top).off('tfsmartresize.tb_visual').on('tfsmartresize.tb_visual', function (e) {
                if (tbLocalScript.fullwidth_support === '') {
                    $(window).trigger('tfsmartresize.tbfullwidth').trigger('tfsmartresize.tfVideo');
                }
                if(typeof themifyMobileMenuTrigger==='function'){
                    themifyMobileMenuTrigger(e);
                }
            })
            .off('tfsmartresize.zoom').on('tfsmartresize.zoom', function () {
                if (api.zoomMeta.isActive) {
                    var scale = '50' == api.zoomMeta.size ? 2 : 1.25;
                    $('.tb_workspace_container', top_iframe).css('height', Math.max(window.top.innerHeight * scale, 600));
                }
            });
            if (events !== undefined) {
                for (var i = 0, len = events.length; i < len; ++i) {
                    if (events[i].handler !== undefined) {
                        this.onResizeEvents.push(events[i].handler);
                    }
                }
            }
            $(window).off('resize');
            if (trigger) {
                var e = $.Event('resize', {type: 'resize', isTrigger: false});
                for (var i = 0, len = this.onResizeEvents.length; i < len; ++i) {
                    try {
                        this.onResizeEvents[i].apply(window, [e, $]);
                    }
                    catch (e) {
                    }
                }
                if (typeof callback === 'function') {
                    callback();
                }
            }

        },
        _addNewColumn: function (params, $context) {
            var columnView = api.Views.init_column({grid_class: params.newclass, component_name: params.component});
            $context.appendChild(columnView.view.render().el);
        },
        filterClass: function (str) {
            var n = str.split(' '),
                    new_arr = [];

            for (var i = 0, len = n.length; i < len; ++i) {
                if (this.gridClass.indexOf(n[i]) !== -1) {
                    new_arr.push(n[i]);
                }
            }
            return new_arr.join(' ');
        },
        _getRowSettings: function ($base, index, type) {
            var cols = {},
                    type = type || 'row',
                    option_data = {},
                    styling,
                    model_r = api.Models.Registry.lookup($base.dataset.cid);
            if (model_r) {
                // cols
                var inner = $base.getElementsByClassName(type + '_inner')[0],
                        columns = inner.children;
                for (var i = 0, len = columns.length; i < len; ++i) {
                    var modules = {},
                            model_c = api.Models.Registry.lookup(columns[i].dataset.cid);
                    if (model_c) {
                        // mods
                        var modules = columns[i].getElementsByClassName('tb_holder'),
                            items = {};
                        if (modules.length > 0) {
                            modules = modules[0].children;
                            if(modules[0]){
                                if(!modules[0].classList.contains('active_module')){
                                        modules = modules[0].children;
                                }
                                for (var j = 0, clen = modules.length; j < clen; ++j) {
                                        var module_m = api.Models.Registry.lookup(modules[j].dataset.cid);
                                        if (module_m) {
                                                styling = api.Utils.clear(module_m.get('mod_settings'), true);
                                                delete styling['cid'];
                                                items[j] = {mod_name: module_m.get('mod_name'), element_id: module_m.get('element_id')};
                                                if (Object.keys(styling).length > 0) {
                                                        items[j]['mod_settings'] = styling;
                                                }
                                                // Sub Rows
                                                if (modules[j].className.indexOf('module_subrow') !== -1) {
                                                        items[j] = this._getRowSettings(modules[j], j, 'subrow');
                                                }
                                        }
                                }
                            }
                        }
                        cols[i] = {
                            element_id: model_c.get('element_id'),
                            column_order: i,
                            grid_class: this.filterClass(columns[i].className),
                        };
                        if (Object.keys(items).length > 0) {
                            cols[i]['modules'] = items;
                        }
                        var custom_w = parseFloat(columns[i].style.width);
                        if (custom_w > 0 && !isNaN(custom_w)) {
                            cols[i]['grid_width'] = custom_w;
                        }
                        styling = api.Utils.clear(model_c.get('styling'), true);
                        if (Object.keys(styling).length > 0) {
                            cols[i]['styling'] = styling;
                        }
                    }
                }

                option_data = {
                    element_id: model_r.get('element_id'),
                    row_order: index,
                    cols: cols,
                    column_alignment: model_r.get('column_alignment'),
                    gutter: model_r.get('gutter')
                };
                var default_data = {
                    gutter: 'gutter-default',
                    column_alignment: is_fullSection ? 'col_align_middle' : 'col_align_top'
                },
                row_opt = {
                    desktop_dir: 'ltr',
                    tablet_dir: 'ltr',
					tablet_landscape_dir: 'ltr',
                    mobile_dir: 'ltr',
                    col_tablet_landscape: '-auto',
                    col_tablet: '-auto',
                    col_mobile: '-auto'
                };
                for (var i in option_data) {
                    if (option_data[i] === '' || option_data[i] === null || option_data[i] === default_data[i]) {
                        delete option_data[i];
                    }
                }
                styling = api.Utils.clear(model_r.get('styling'), true);
                for (var i in row_opt) {
                    var v = $.trim(inner.getAttribute('data-' + i));
                    if (v !== undefined && v !== '' && v !== row_opt[i]) {
                        option_data[i] = v;
                    }
                }
                if (Object.keys(styling).length > 0) {
                    option_data['styling'] = styling;
                }

            }
            return option_data;
        },
        selectedGridMenu: function (context) {
			var grids = context.getElementsByClassName('tb_grid_menu'),
				directions = ['mobile', 'tablet', 'tablet_landscape', 'desktop'];

			for ( var i = 0, len = grids.length; i < len; ++i ) {
				var $this = $(grids[i]),
					handle = $this.data('handle');

				if (handle !== 'module') {
					var row = $this.closest('.module_' + handle),
						model = api.Models.Registry.lookup(row.data('cid')),
						grid_base = [],
						$base = row.find('.' + handle + '_inner').first(),
						gutter = model.get('gutter'),
						column_aligment = model.get('column_alignment'),
						dir = model.get('desktop_dir'),
						styling = model.get('styling'),
						cl = '',
						attr = {},
						columns = $base[0].children;

					for (var j = 0, clen = columns.length; j < clen; ++j) {
						grid_base.push(api.Utils._getColClass(columns[j].className.split(' ')));
						columns[j].className = columns[j].className.replace(/first|last/ig, '');
						if (clen !== 1) {
							if (j === 0) {
								columns[j].className += dir === 'rtl' ? ' last' : ' first';
							}
							else if (j === (clen - 1)) {
								columns[j].className += dir === 'rtl' ? ' first' : ' last';
							}
						}
					}

					var $selected = $this.find('.tb_grid_desktop .grid-layout-' + grid_base.join('-')),
                                            $col = $selected.data('col');

					if ($selected.length > 0) {
						$selected.parent().addClass('selected').siblings().removeClass('selected');
						row.addClass('col-count-' + $col);
						cl = 'col-count-' + $col;
						attr['data-basecol'] = $col;
					}

					if (dir !== 'ltr') {
						cl += ' direction-rtl';
					}

					for (var j = 0; j < 4; ++j) {
						var dir = model.get(directions[j] + '_dir');

						if (dir !== 'ltr' && dir !== '') {
							attr['data-' + directions[j] + '_dir'] = dir;
							$selected = $this.find('.tb_grid_' + directions[j] + ' .column-dir-' + dir);
							$selected.parent().addClass('selected').siblings().removeClass('selected');
						}
						if (directions[j] !== 'desktop') {
							var _col = model.get('col_' + directions[j]);
							if (_col !== '-auto' && _col !== '' && _col !== undefined) {
								attr['data-col_' + directions[j]] = _col;
								$selected = $this.find('.tb_grid_' + directions[j] + ' .grid-layout-' + _col.replace(/column|tb_3col/ig, ''));
								$selected.parent().addClass('selected').siblings().removeClass('selected');
							}
						}
					}

					if (styling && styling['row_anchor'] !== undefined && styling['row_anchor'] !== '') {
						row.find('.tb_row_anchor').first().text(styling['row_anchor']);
					}

					styling = null;
					if (column_aligment !== 'col_align_top') {
						$this.find('.column-alignment-' + column_aligment).parent().addClass('selected').siblings().removeClass('selected');
						cl += ' ' + column_aligment;
					}

					if (gutter !== 'gutter-default') {
						$this.find('.gutter_select').val(gutter);
						cl += ' ' + gutter;
					}

					$base.addClass(cl).attr(attr);
                }
            }
        },
        clear: function (items, clear_all, is_array) {
            var res = is_array ? [] : {};
            for (var i in items) {
                if (Array.isArray(items[i])) {
                    var data = this.clear(items[i], clear_all, true);
                    if (data.length > 0) {
                        res[i] = data;
                    }
                }
                else if (typeof items[i] === 'object') {
                    var data = this.clear(items[i], clear_all, false);
                    if (!$.isEmptyObject(data)) {
                        res[i] = data;
                    }
                }
                else if (items[i]!==null &&  items[i]!==undefined && items[i]!==''  && items[i] !== 'px' && items[i] !== 'pixels' && items[i] !== 'n' && items[i] !== 'solid' && items[i] !== 'linear' && items[i] !== 'default' && items[i] !== '|') {
                    if (//remove old stored data
                            (i.indexOf('_gradient-css') !== -1)
							||
							(items[i]==='%' && i.indexOf('-frame_')!==-1)
                            || (i === 'cover_gradient_hover-css')
                            || (i === 'background_image-css')
                            || (i === 'background_image-type_gradient')
                            || (i.indexOf('gradient-angle') !== -1 && items[i] == '180')
                            || (i.indexOf('_padding_apply_all_padding') !== -1)
                            || (i.indexOf('_margin_apply_all_margin') !== -1)
                            || (i.indexOf('_border_apply_all_border') !== -1)
                            || (i === 'text_align_right')
                            || (i === 'text_align_center')
                            || (i === 'text_align_left')
                            || (i === 'text_align_justify')
                            || (items[i] === 'show' && i.indexOf('visibility_') !== -1)) {
                            continue;
                    }
                    else if (clear_all && (i === 'cover_gradient_hover-gradient' || i === 'background_image-gradient' || (i.indexOf('gradient-type') === -1 && i.indexOf('gradient-angle') === -1 && i.indexOf('_gradient-gradient') !== -1))) {
                        var mode = i.indexOf('background_gradient') !== -1 ? i.replace('_gradient', '_type').replace('-gradient', '') : i.replace('-gradient', '-type').replace('_gradient', '_color');
                        if (items[mode] !== 'gradient' && items[mode] !== 'cover_gradient' && items[mode] !== 'hover_gradient') {
                            var gfields = ['gradient-angle', 'type_image', 'circle-radial', 'gradient-type'],
                                tmp_id = i.replace('-gradient', ''); 
                            for (var j = 0, len = gfields.length; j < len; ++j) {
                                var tmp = tmp_id + '-' + gfields[j];
                                if (items[tmp] !== undefined) {
                                    items[tmp] = res[tmp] = null;
                                    delete items[tmp];
                                    delete res[tmp];
                                }
                            }
                            continue;
                        }
                    }
                    res[i] = items[i];
                }
				else if(items[i]==='px' && i.indexOf('-frame_')!==-1){
                    res[i] = items[i];
				}

            }
            return res;
        },
        builderPlupload: function (action_text, is_import) {
            var class_new = is_import ? '' : (action_text === 'new_elemn' ? '.plupload-clone' : ''),
                    $builderPlupoadUpload = $('.tb_plupload_upload_uic' + class_new, top_iframe);
            if ($builderPlupoadUpload.length > 0) {
                var self = this;
                if (self.pconfig === undefined) {
                    self.pconfig = JSON.parse(JSON.stringify(themify_builder_plupload_init));
                    self.pconfig['multipart_params']['_ajax_nonce'] = themifyBuilder.tb_load_nonce;
                    self.pconfig['multipart_params']['topost'] = themifyBuilder.post_ID;
                }
                $builderPlupoadUpload.each(function () {
                    var $this = $(this),
                            id1 = $this.prop('id'),
                            imgId = id1.replace('tb_plupload_upload_ui', ''),
                            config = $.extend(true, {}, self.pconfig),
                            parts = ['browse_button', 'container', 'drop_element', 'file_data_name'];
                    config['multipart_params']['imgid'] = imgId;
                    for (var i = 0, len = parts.length; i < len; ++i) {
                        config[parts[i]] = imgId + self.pconfig[parts[i]];
                    }

                    if ($this.data('extensions')) {
                        config['filters'][0]['extensions'] = $this.data('extensions');
                    }
                    else {
                        config['filters'][0]['extensions'] = api.activeModel !== null ?
                                config['filters'][0]['extensions'].replace(/\,zip|\,txt/, '')
                                : 'zip,txt';
                    }
                    var uploader = new window.top.plupload.Uploader(config);
                    uploader.init();

                    // a file was added in the queue
                    uploader.bind('FilesAdded', function (up, files) {
                        up.refresh();
                        up.start();
                        ThemifyBuilderCommon.showLoader('show');
                    });

                    uploader.bind('Error', function (up, error) {
                        var $promptError = $('.prompt-box .show-error');
                        $('.prompt-box .show-login').hide();
                        $promptError.show();

                        if ($promptError.length > 0) {
                            $promptError.html('<p class="prompt-error">' + error.message + '</p>');
                        }
                        $('.overlay, .prompt-box').fadeIn(500);
                    });

                    // a file was uploaded
                    uploader.bind('FileUploaded', function (up, file, response) {
                        var json = JSON.parse(response['response']),
                                alertData = $('#tb_alert', top_iframe),
                                status = 200 === response['status'] && !json.error ? 'done' : 'error';
                        if (json.error) {
                            ThemifyBuilderCommon.showLoader(status);
                            alert(json.error);
                            return;
                        }
                        if (is_import) {
                            var before = $('#tb_row_wrapper').children().clone(true);
                            alertData.promise().done(function () {
                                api.Forms.reLoad(json, themifyBuilder.post_ID);
                                var after = $('#tb_row_wrapper').children().clone(true);
                                ThemifyBuilderCommon.Lightbox.close();
                                api.undoManager.push( '', '', '', 'import', {before: before, after: after, bid: themifyBuilder.post_ID});
                            });
                        }
                        else {
                            ThemifyBuilderCommon.showLoader(status);
                            var response_url = json.large_url ? json.large_url : json.url;
                            $this.closest('.tb_input').find('.tb_uploader_input').val(response_url).trigger('change')
                                    .parent().find('.img-placeholder')
                                    .html($('<img/>', {src: json.thumb, width: 50, height: 50}));
                        }
                    });
                    $this.removeClass('plupload-clone');
                });
            }
        },
        columnDrag: function ($container, $remove, old_gutter, new_gutter) {
            var self = this;
            if ($remove) {
                var columns = $container ? $container.children('.module_column') : $('.module_column');
                columns.css('width', '');
                self.setCompactMode(columns);
            }
            var _margin = {
                default: 3.2,
                narrow: 1.6,
                none: 0
            };
            if (old_gutter && new_gutter) {
                var cols = $container.children('.module_column'),
                        new_margin = new_gutter === 'gutter-narrow' ? _margin.narrow : (new_gutter === 'gutter-none' ? _margin.none : _margin.default),
                        old_margin = old_gutter === 'gutter-narrow' ? _margin.narrow : (old_gutter === 'gutter-none' ? _margin.none : _margin.default),
                        margin = old_margin - new_margin;
                margin = parseFloat((margin * (cols.length - 1)) / cols.length);
                cols.each(function (i) {
                    if ($(this).prop('style').width) {
                        var w = parseFloat($(this).prop('style').width) + margin;
                        $(this).css('width', w + '%');
                    }
                });
                return;
            }
            var $cdrags = $container ? $container.children('.module_column').find('.tb_grid_drag') : $('.tb_grid_drag'),
                    _cols = {
                        default: {'col6-1': 14, 'col5-1': 17.44, 'col4-1': 22.6, 'col4-2': 48.4, 'col2-1': 48.4, 'col4-3': 74.2, 'col3-1': 31.2, 'col3-2': 65.6},
                        narrow: {'col6-1': 15.33, 'col5-1': 18.72, 'col4-1': 23.8, 'col4-2': 49.2, 'col2-1': 49.2, 'col4-3': 74.539, 'col3-1': 32.266, 'col3-2': 66.05},
                        none: {'col6-1': 16.666, 'col5-1': 20, 'col4-1': 25, 'col4-2': 50, 'col2-1': 50, 'col4-3': 75, 'col3-1': 33.333, 'col3-2': 66.666}
                    },
            $min = 5;
            $cdrags.each(function () {

                var $el,
                        $row,
                        $columns,
                        $current,
                        $el_width = 0,
                        dir,
                        cell = false,
                        cell_w = 0,
                        before = false,
                        $helperClass,
                        row_w,
                        dir_rtl,
                        start_w1;
                $(this).draggable({
                    axis: 'x',
                    cursor: 'col-resize',
                    distance: 0,
                    scroll: false,
                    snap: false,
                    containment: '.row_inner',
                    helper: function (e) {
                        $el = $(e.currentTarget);
                        $row = $el.closest('.subrow_inner');
                        if ($row.length === 0) {
                            $row = $el.closest('.row_inner');
                        }
                        $row.addClass('tb_drag_column_start');
                        dir = $el.hasClass('tb_drag_right') ? 'w' : 'e';
                        $helperClass = dir === 'w' ? 'tb_grid_drag_right_tooltip' : 'tb_grid_drag_left_tooltip',
                        before = ThemifyBuilderCommon.clone($row.closest('.module_row'));
                        return $('<div class="ui-widget-header tb_grid_drag_tooltip ' + $helperClass + '"></div><div class="ui-widget-header tb_grid_drag_tooltip"></div>');
                    },
                    start: function (e, ui) {
                        $columns = $row.children('.module_column');
                        $current = $el.closest('.module_column');
                        dir_rtl = $row.hasClass('direction-rtl');
                        if (dir === 'w') {
                            cell = dir_rtl ? $current.prev('.module_column') : $current.next('.module_column');
                            $el_width = $el.outerWidth();
                            start_w1 = $current.outerWidth();
                        }
                        else {
                            cell = dir_rtl ? $current.next('.module_column') : $current.prev('.module_column');
                            $el_width = $current.outerWidth();
                            start_w1 = $el_width;
                        }
                        $el_width =  parseInt($el_width);
                        cell_w = parseInt(cell.outerWidth()) - 2;
                        row_w = $row.outerWidth();
                    },
                    stop: function (e, ui) {
                        $('.tb_grid_drag_tooltip').remove();
                        $row.removeClass('tb_drag_column_start');
                        var percent = Math.ceil(100 * ($current.outerWidth() / row_w));
                        $current.css('width', percent + '%');
                        var cols = _cols.default,
                                margin = _margin.default;
                        if ($row.hasClass('gutter-narrow')) {
                            cols = _cols.narrow;
                            margin = _margin.narrow;
                        }
                        else if ($row.hasClass('gutter-none')) {
                            cols = _cols.none;
                            margin = _margin.none;
                        }
                        var cellW = margin * ($columns.length - 1);
                        $columns.each(function (i) {
                            if (i !== cell.index()) {
                                var w;
                                if ($(this).prop('style').width) {
                                    w = parseFloat($(this).prop('style').width);
                                }
                                else {
                                    var col = $.trim(self.filterClass($(this).attr('class')).replace('first', '').replace('last', ''));
                                    w = cols[col];
                                }
                                cellW += w;
                            }
                        });
                        cell.css('width', (100 - cellW) + '%');
                        cell = cell.add($current);
                        self.setCompactMode(cell);
                        var after = $row.closest('.module_row');
                        api.undoManager.push( after.data('cid'), before, after, 'row');
                        Themify.body.trigger('tb_grid_changed',[after]);
                    },
                    drag: function (e, ui) {
                        if (cell && cell.length > 0) {
                            var left = parseInt(ui.position.left),
                                px = $el_width + (dir === 'e' ? -(left) : left),
                                    $width = parseFloat((100 * px) / row_w);
                            if ($width >= $min && $width < 100) {
                                var max = cell_w;
                                max+=(dir === 'w' ? -(px-start_w1) :(start_w1-px));
                                var $max_percent = parseFloat((100 * max) / row_w);
                                if ($max_percent > $min && $max_percent < 100) {
                                    cell.css('width', max + 'px');
                                    $current.css('width', px + 'px').children('.' + $helperClass).html($width.toFixed(2) + '%');
                                    $current.children('.tb_grid_drag_tooltip').last().html($max_percent.toFixed(2) + '%');
                                    self.setCompactMode($current);
                                }
                            }
                        }

                    }

                });
            });
        },
        grid: function (slug) {
            var cols = [];
            slug = parseInt(slug); 
            if( slug === 1 ){
                    cols.push({"grid_class": "col-full"});
            }else {
                for (var i = 0; i < slug; ++i) {
                    cols.push({"column_order":i, "grid_class": "col" + slug + "-1"});
                }
            }
           
            return [{"row_order":"0","cols":cols}];
        },
        setCompactMode: function (col) {
            if (col instanceof jQuery) {
                col = col.get();
            }
            for (var i = 0, len = col.length; i < len; ++i) {
                if(col[i].clientWidth < 185 ){
                    col[i].classList.add('compact-mode');
                }
                else{
                    col[i].classList.remove('compact-mode');
                }
            }
        },
        initNewEditor: function (editor_id) {
            var $settings = tinyMCEPreInit.mceInit['tb_lb_hidden_editor'];
            $settings['elements'] = editor_id;
            $settings['selector'] = '#' + editor_id;
            // v4 compatibility
            return this.initMCEv4(editor_id, $settings);
        },
        initMCEv4: function (editor_id, $settings) {
            // v4 compatibility
            if (parseInt(tinyMCE.majorVersion) > 3) {
                // Creates a new editor instance
                var ed = new tinyMCE.Editor(editor_id, $settings, tinyMCE.EditorManager);
                ed.render();
                return ed;
            }
        },
        initQuickTags: function (editor_id) {
            // add quicktags
            if (typeof window.top.QTags === 'function') {
                window.top.quicktags({id: editor_id});
                window.top.QTags._buttonsInit();
            }
        },
        setImageSelect : function ( context ) {
									},
        setColorPicker: function (context) {
            $('.minicolors-swatch', context).each(function () {
                var $this = $(this),
                    parent = $this.closest('.minicolors_wrapper');
                $(this).one('click', function () {
                    var input = parent.find('.minicolors-input');
                    parent.prepend(input).find('.minicolors').remove();
                    api.hasChanged = true;
                    api.Views.init_control('color', {el: input, binding_type: input.data('control-binding')});
                }).prev('.minicolors-input').one('focusin', function () {
                    $(this).next('.minicolors-swatch').trigger('click');
                });
                parent.find('.color_opacity').one('change', function () {
                    $this.prev('.minicolors-input').attr('data-opacity', $(this).val());
                    $this.trigger('click');
                });
            });
        },
        datePickerLoaded:null,
        datePicker: function ( context ) {
            if(this.datePickerLoaded===null){
                var self = this;
                window.top.Themify.LoadCss(themifyBuilder.meta_url+'css/jquery-ui-timepicker.min.css');
                window.top.Themify.LoadAsync(themifyBuilder.includes_url+'js/jquery/ui/datepicker.min.js', function(){
                    window.top.Themify.LoadAsync(themifyBuilder.meta_url+'js/jquery-ui-timepicker.min.js',function(){
                        self.datePickerLoaded = true;
                        callback();
                    }, null,null,function(){
                        return window.top.$.fn.themifyDatetimepicker!==undefined;
                    });
                }, null,null,function(){
                    return window.top.$.fn.datepicker!==undefined;
                });
            }
            else{
                callback();
            }
            function callback(){
                var datePicker = window.top.$.fn.themifyDatetimepicker
                        ? window.top.$.fn.themifyDatetimepicker
                        : window.top.$.fn.datetimepicker;
                    
                if( ! datePicker ) return;

                $( '.themify-datepicker', context ).each( function() {
                        var input = $( this ),
                            clearButton = input.next( '.themify-datepicker-clear' );
                            clearButton.click(function( e ) {
                                e.preventDefault();
                                e.stopPropagation();
                                input.val( '' ).trigger( 'change' );
                                $(this).addClass( 'disable' );
                            } ).toggleClass( 'disable', ! input.val() );
                        datePicker.call( input, {
                                showButtonPanel: true,
                                changeYear: true,
                                dateFormat: input.data('dateformat' ) || 'yy-mm-dd',
                                timeFormat: input.data('timeformat' ) || 'HH:mm:ss',
                                stepMinute: 5,
                                stepSecond: 5,
                                controlType:input.data('timecontrol' ) || 'select',
                                oneLine:true,
                                separator: input.data( 'timeseparator' ) || ' ',
                                onSelect: function( date ) {
                                    clearButton.toggleClass( 'disable', '' == date );
                                    input.trigger( 'change' );
                                },
                                beforeShow: function() {
                                    $( '#ui-datepicker-div', top_iframe ).addClass( 'themify-datepicket-panel' );
                                }
                        });
                } );
            }
        },
        _getColClass: function (classes) {
            for (var i = 0, len = classes.length; i < len; ++i) {
                if (this.gridClass.indexOf(classes[i]) !== -1) {
                    return classes[i].replace('col', '');
                }
            }
        },
        saveBuilder: function (callback, saveto, i, onlyData) {
            saveto = saveto || 'main';
            i = i || 0;
            if (i === 0) {
                if (saveto === 'main' && api.activeModel) {
                    $('.builder_save_button', top_iframe).trigger('click');
                 }
                ThemifyBuilderCommon.showLoader('show');
            }
            var len = Object.keys(api.Instances.Builder).length,
                    view = api.Instances.Builder[i],
                    self = this,
                    id = view.$el.data('postid'),
                    data = view.toJSON();

            function sendData(id, data) {
                var data = {
                    action: 'tb_save_data',
                    tb_load_nonce: themifyBuilder.tb_load_nonce,
                    id: id,
                    data: JSON.stringify(data),
                    tb_saveto: saveto,
                    sourceEditor: 'visual' === api.mode ? 'frontend' : 'backend'
                };
                if (onlyData) {
                    data.only_data = onlyData;
                }
                return $.ajax({
                    type: 'POST',
                    url: themifyBuilder.ajaxurl,
                    cache: false,
                    data: data
                });
            };
            sendData(id, data).always(function (jqXHR, textStatus) {
                ++i;
                if (len === i) {
                    // load callback
                    if ($.isFunction(callback)) {
                        callback.call(self, jqXHR, textStatus);
                    }
                    if (textStatus !== 'success') {
                        ThemifyBuilderCommon.showLoader('error');
                    }
                    else {
                        ThemifyBuilderCommon.showLoader('hide');
                        api.editing = true;
                        Themify.body.trigger('themify_builder_save_data', [jqXHR, textStatus]);
                    }
                }
                else {
                    setTimeout(function () {
                        self.saveBuilder(callback, saveto, i,onlyData);
                    }, 50);
                }
            });
        },
        loadContentJs: function (el, type) {
            ThemifyBuilderModuleJs.loadOnAjax(el, type); // load module js ajax
            // hook
            if (api.saving === false) {
                var mediaelements = $('audio.wp-audio-shortcode, video.wp-video-shortcode', el);
                if (mediaelements.length > 0) {
                    if (themifyBuilder.media_css) {
                        for (var i in themifyBuilder.media_css) {
                            Themify.LoadCss(themifyBuilder.media_css[i]);
                        }
                        themifyBuilder.media_css = null;
                    }
                    mediaelements.each(function(){
                        var p = $(this).closest('.mejs-mediaelement');
                        if(p.length>0){
                            this.removeAttribute('style');
                            this.setAttribute('id',this.getAttribute('id').replace('_html5',''));
                            p.closest('.widget').html(this);   
                        }
                        
                    });
                    var settings = typeof window.top._wpmejsSettings !== 'undefined' ? window.top._wpmejsSettings : {};
                    mediaelements.mediaelementplayer(settings);
                }
            }
            Themify.body.trigger('builder_load_module_partial', [el, type]);
        },
        mediaUploader: function () {
            var _frames = {};
            $('body', top_iframe).on('click', '.tb_media_uploader', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var $el = $(this),
                        file_frame,
                        $builderInput = $el.closest('.tb_input'),
                        title = $el.data('uploader-title'),
                        text = $el.data('uploader-button-text'),
                        type = $el.data('library-type') ? $el.data('library-type') : 'image',
                        hkey = Themify.hash(type + title + text);
                if (_frames[hkey] !== undefined) {
                    file_frame = _frames[hkey];
                }
                else {
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: title,
                        library: {
                            type: type
                        },
                        button: {
                            text: text
                        },
                        multiple: false
                    });
                    _frames[hkey] = file_frame;
                }
                file_frame.off('select').on('select', function () {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    $builderInput.find('.tb_uploader_input').val(attachment.url).trigger('change')
                            .parent().find('.img-placeholder')
                            .html($('<img/>', {
                                src: attachment.url,
                                width: 50,
                                height: 50
                            }));
                    api.hasChanged = true;
                    $builderInput.find('.tb_uploader_input_attach_id').val(attachment.id);
                });
                // Finally, open the modal
                file_frame.open();

            }).on('click', '.tb_delete_thumb', function (e) {
                e.preventDefault();
                api.hasChanged = true;
                $(this).prev().empty().closest('.tb_input').find('.tb_uploader_input').val('').trigger('change');

            }).on('click', '.insert-media', function (e) {
                api.hasChanged = true;
                window.top.wpActiveEditor = $(this).data('editor');
            });
        },
        openGallery: function () {
            var clone = wp.media.gallery.shortcode,
                    $self = this,
                    file_frame = null;
            $('body', top_iframe).on('click', '.tb_gallery_btn', function (e) {
                e.preventDefault();
                var shortcode_val = $(this).closest('.tb_input').find('.tb_shortcode_input');
                if (file_frame === null) {
                    // Create the media frame.
                    file_frame = wp.media.frames.file_frame = wp.media({
                        frame: 'post',
                        state: 'gallery-edit',
                        title: wp.media.view.l10n.editGalleryTitle,
                        editing: true,
                        multiple: true,
                        selection: false
                    });
                    file_frame.$el.addClass('themify_gallery_settings');
                }
                wp.media.gallery.shortcode = function (attachments) {
                    var props = attachments.props.toJSON(),
                            attrs = _.pick(props, 'orderby', 'order');

                    if (attachments.gallery) {
                        _.extend(attrs, attachments.gallery.toJSON());
                    }
                    attrs.ids = attachments.pluck('id');
                    // Copy the `uploadedTo` post ID.
                    if (props.uploadedTo) {
                        attrs.id = props.uploadedTo;
                    }
                    // Check if the gallery is randomly ordered.
                    if (attrs._orderbyRandom) {
                        attrs.orderby = 'rand';
                        delete attrs._orderbyRandom;
                    }
                    // If the `ids` attribute is set and `orderby` attribute
                    // is the default value, clear it for cleaner output.
                    if (attrs.ids && 'post__in' === attrs.orderby) {
                        delete attrs.orderby;
                    }
                    // Remove default attributes from the shortcode.
                    _.each(wp.media.gallery.defaults, function (value, key) {
                        if (value === attrs[key]) {
                            delete attrs[key];
                        }
                    });
                    delete attrs['_orderByField'];
                    var shortcode = new window.top.wp.shortcode({
                        tag: 'gallery',
                        attrs: attrs,
                        type: 'single'
                    });
                    shortcode_val.val(shortcode.string()).trigger('change');

                    wp.media.gallery.shortcode = clone;
                    return shortcode;
                };

                file_frame.on('update', function (selection) {
                    var shortcode = wp.media.gallery.shortcode(selection).string().slice(1, -1);
                    shortcode_val.val('[' + shortcode + ']');
                    $self.setShortcodePreview(selection.models, shortcode_val);
                    api.hasChanged = true;
                });

                if ($.trim(shortcode_val.val()).length > 0) {
                    file_frame = wp.media.gallery.edit($.trim(shortcode_val.val()));
                    file_frame.state('gallery-edit').on('update', function (selection) {
                        var shortcode = wp.media.gallery.shortcode(selection).string().slice(1, -1);
                        shortcode_val.val('[' + shortcode + ']');
                        $self.setShortcodePreview(selection.models, shortcode_val);
                        api.hasChanged = true;
                    });
                } else {
                    file_frame.open();
                    file_frame.$el.find('.media-menu .media-menu-item').last().trigger('click');
                }

            });

        },
        setShortcodePreview: function (images, $input) {
            var $preview = $input.next('.tb_shortcode_preview'),
                    html = '';
            if ($preview.length === 0) {
                $preview = $('<div class="tb_shortcode_preview"></div>');
                $input.after($preview);
            }
            for (var i = 0, len = images.length; i < len; ++i) {
                var attachment = images[i].attributes,
                        url = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                html += '<img src="' + url + '" width="50" height="50" />';
            }
            $preview[0].innerHTML = html;
        },
        createRange: function ($range) {
                var tb_unit = $range.closest('.tb_range_input').next('.selectwrapper').find('.tb_unit'),
                    event = $range[0].dataset['controlBinding']!==undefined?'change':'keyup',
                    is_select = tb_unit.length>0 && tb_unit[0].tagName === 'SELECT';
                function changeValue( $el, condition ){
                    if( ! $el.data('increment') ){
                        if(is_select){
                            tb_unit.val(tb_unit.find('option').first().val()).trigger('change');
                        }
                        return;
                    }
                    var increment = $el.data('increment'),
                        is_increment = increment % 1 !== 0,
                        max = parseFloat($el.data('max')),
                        min = parseFloat($el.data('min')),
                        cval = $el.val(),
                        val = !is_increment ? parseInt( cval || 0 ) : parseFloat( cval || 0 );
                        increment = !is_increment ? parseInt( increment ) : parseFloat( increment);

                    if( 'increase' === condition && val < max){
                            if( val < min ){
                                $el.val(is_increment? parseFloat( min ).toFixed(1) : parseInt( min ) );
                                return;
                            }
                            $el.val( is_increment ? parseFloat(val + increment).toFixed(1) : val + increment );
                    } 
                    else if ( val > min ){
                        if(val > max ){
                            $el.val(is_increment ? parseFloat( max ).toFixed(1) : parseInt( max ) );
                            return;
                        }
                        $el.val( is_increment ? parseFloat(val - increment).toFixed(1) : val - increment );
                    }
                    api.hasChanged = true;
                    if($el.val().trim()) {
	                    $el.val( is_increment ? parseFloat( $el.val() ).toFixed( 1 ) : parseInt( $el.val() ) );
                    }
                }
                
                function setData(item){
                    $range.data({min: item.data('min'),max:item.data('max'),increment:item.data('increment')} );
                }
                $range.mousedown(function(e){
                    var lastY = e.pageY,
                        that = this;
                        $(window.top.document).on('mousemove.dragOnRangeInput', function(e){
                            if( e.pageY < lastY ){
                                changeValue($(that),'increase');
                            }else if(e.pageY > lastY){
                                changeValue($(that),'decrease');
                            }
                            lastY = e.pageY;
                            $(that).trigger(event);
                        }).on('mouseup.dragOnRangeInput', function(){
                            $(this).off('mousemove.dragOnRangeInput mouseup.dragOnRangeInput');
                            $(that).trigger(event);
                        });
                }).keydown(function(e){
                    if ( e.which === 38 ){
                        changeValue($(this),'increase');
                    }else if( e.which === 40 ){
                        changeValue($(this),'decrease');
                    }
                });
                if(is_select){
                    tb_unit.change(function(){
                        setData($(this).find(':selected'));
                        if(api.mode==='visual' && event==='change'){
                            tempSettings[$(this).prop('id')] = $(this).val();
                        }
                        $range.trigger(event);
                    });
                }
                if(tb_unit.length>0){
                    setData(is_select?tb_unit.find(':selected'):tb_unit);
                }
        },
        createClearBtn : function( $input ){
            $input.siblings('.tb_clear_btn').click(function(){
                $(this).hide();
                $input.val('').trigger('keyup');
            });
        },
        createGradientPicker: function ($input, value, update) {
            var $field = $input.closest('.themify-gradient-field'),
                    instance = null, // the ThemifyGradient object instance
                    is_removed = false,
                    $id = $input.data('id'),
                    $angleInput = $field.find('#' + $id + '-gradient-angle'),
                    gradient = $input.prev(),
                    args = {
                        angle: $angleInput.val(),
                        onChange: function (stringGradient, cssGradient) {
                            if (is_removed) {
                                stringGradient = cssGradient = '';
                            }
                            if ('visual' === api.mode) {
                                if ($id === 'cover_gradient' || $id === 'cover_gradient_hover') {

                                    api.liveStylingInstance.addOrRemoveComponentOverlay($id, cssGradient);
                                }
                                else {
                                    api.liveStylingInstance.bindBackgroundGradient($id, cssGradient);
                                }

                            }
                            $input.val(stringGradient);
                            api.hasChanged = true;
                        },
                        onInit: function () {
                            gradient.show();
                        }
                    };

            args.gradient = value ? value : ($input.data('default-gradient') ? $input.data('default-gradient') : undefined);
            if (!update) {
                gradient.ThemifyGradient(args);
            }
            instance = $input.prev().data('themifyGradient');
            // Linear or Radial select field
            var type = $field.find('#' + $id + '-gradient-type'),
                    circle = $field.find('#' + $id + '-circle-radial input'),
                    callback = function (val) {
                        var $angelparent = $angleInput.closest('.gradient-angle-knob'),
                                $radial_circle = $field.find('#' + $id + '-circle-radial');

                        if (val === 'radial') {
                            $angelparent.hide();
                            $angelparent.next('span').hide();
                            $radial_circle.show();
                        }
                        else {
                            $angelparent.show();
                            $angelparent.next('span').show();
                            $radial_circle.hide();
                        }
                    };
            if (update) {
                instance.settings = $.extend({}, instance.settings, args);
                instance.settings.type = type.val();
                instance.settings.circle = circle.is(':checked');
                instance.isInit = false;
                instance.update();
                instance.isInit = true;
            }
            else {
                $field.find('.themify-clear-gradient').click(function (e) {
                    e.preventDefault();
                    is_removed = true;
                    instance.settings.gradient = $.ThemifyGradient.default;
                    instance.update();
                    is_removed = false;
                });

                type.change(function (e) {
                    instance.setType($(this).val());
                    callback($(this).val());
                });

                circle.change(function () {
                    instance.setRadialCircle($(this).is(':checked'));
                });
                $angleInput.change(function () {
                    var $val = parseInt($(this).val());
                    if (!$val) {
                        $val = 0;
                    }
                    instance.setAngle($val);
                }).knob({
                    change: function (v) {
                        instance.setAngle(Math.round(v));
                    }
                });

                // angle input popup style
                $angleInput.removeAttr('style').parent().addClass('gradient-angle-knob').find('canvas').insertAfter($angleInput);
            }
            callback(type.val());
        },
        toRGBA: function (color) {
            var colorArr = color.split('_'),
                    patt = /^([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/;
            if (colorArr[0] !== undefined) {
                var matches = patt.exec(colorArr[0].replace('#', '')),
                        opacity = colorArr[1] !== undefined && colorArr[1] != '0.99' ? colorArr[1] : 1;
                return matches ? 'rgba(' + parseInt(matches[1], 16) + ', ' + parseInt(matches[2], 16) + ', ' + parseInt(matches[3], 16) + ', ' + opacity + ')' : color;
            }
            return color;
        },
        getIcon: function (icon) {
            if(icon.indexOf('fa-')===0){
                icon='fa '+icon;
            }
            return icon;
        },
        // get breakpoint width
        getBPWidth: function (device) {
            var breakpoints = _.isArray(themifyBuilder.breakpoints[ device ]) ? themifyBuilder.breakpoints[ device ] : themifyBuilder.breakpoints[ device ].toString().split('-');
			return breakpoints[ breakpoints.length - 1 ];
        },
        transitionPrefix: function () {
            if (this.transitionPrefix.pre === undefined) {
                var el = document.createElement('fakeelement'),
                        transitions = {
                            transition: 'transitionend',
                            OTransition: 'oTransitionEnd',
                            MozTransition: 'transitionend',
                            WebkitTransition: 'webkitTransitionEnd'
                        }

                for (var t in transitions) {
                    if (el.style[t] !== undefined) {
                        this.transitionPrefix.pre = transitions[t];
                        break;
                    }
                }
            }
            return this.transitionPrefix.pre;
        },
        generateUniqueID: function(){
            return Math.random().toString( 16 ).substr( 2, 7 );
        },
        getUIDList: function( type ) {
            type = type || 'row';
            var atts = _.pluck( api.Models.Registry.items, 'attributes' );
            return _.where( atts, {elType: type}) || [];
        }
    };

    _.extend(api.Views.BaseElement.prototype, api.Mixins.Common);
    _.extend(api.Views.Builder.prototype, api.Mixins.Builder);

    /**
     * Form control views.
     */

    api.Views.ControlRegistry = {
        items: {},
        register: function (id, object) {
            this.items[id] = object;
        },
        lookup: function (id) {
            return this.items[id] || null;
        },
        remove: function (id) {
            this.items[id] = null;
            delete this.items[id];
        },
        destroy: function () {
            _.each(this.items, function (view, cid) {
                view.remove();
            });
            this.items = {};
        }
    };

    api.Views.Controls[ 'default' ] = Backbone.View.extend({
        initialize: function (args) {
            api.Views.ControlRegistry.register(this.$el.prop('id'), this);
            if (args.binding_type) {
                this.binding_type = args.binding_type;
            }
            if (args.selector) {
                this.selector = args.selector;
            }
        },
        preview_element: function (value) {
            if (this.binding_type === undefined) {
                return;
            }
            api.hasChanged = true;
            if(api.activeModel.get('elType') === 'module'){
                var type = this.$el.data('control-type');

				// for "repeater" fields the data has to be parsed first
				if ( this.$el.closest( '.tb_row_js_wrapper' ).length ) {
					var $repeater = this.$el.closest( '.tb_row_js_wrapper' );
					tempSettings[ $repeater.attr( 'id' ) ] = api.Forms.parseSettings( $repeater[0] ).v;
				} else {
                    tempSettings[ this.$el.prop('id') ] = value;
                }

                if (api.mode === 'visual') {
                    if ('live' === this.binding_type) {
                        api.activeModel.trigger('custom:preview:live', tempSettings, type === 'wp_editor' || this.el.tagName === 'TEXTAREA', null, this.selector, value, this.$el);
                    } else if ('refresh' === this.binding_type) {
                        api.activeModel.trigger('custom:preview:refresh', tempSettings, this.selector, value, this.$el);
                    }
                }
                else{
                    api.activeModel.backendLivePreview();
                }
            }
        }
    });

    api.Views.Controls.default.extend = function (child) {
        var self = this,
                view = Backbone.View.extend.apply(this, arguments);
        view.prototype.events = _.extend({}, this.prototype.events, child.events);
        view.prototype.initialize = function () {
            if (_.isFunction(self.prototype.initialize))
                self.prototype.initialize.apply(this, arguments);
            if (_.isFunction(child.initialize))
                child.initialize.apply(this, arguments);
        };
        return view;
    };

    api.Views.register_control = function (type, args) {
        if ('default' !== type) {
            this.Controls[ type ] = this.Controls.default.extend(args);
        }
    };

    api.Views.get_control = function (type) {
        return this.control_exists(type) ? this.Controls[ type ] : this.Controls.default;
    };

    api.Views.control_exists = function (type) {

        return this.Controls.hasOwnProperty(type);
    };

    api.Views.init_control = function (type, args) {
        args = args || {};
        if (!args['binding_type'] && !args.el.hasClass('minicolors-input')) {
            args['binding_type'] = 'refresh';
        }
        if (!type) {
            type = 'change';
        }
        else if ('wp_editor' === type && args.el.hasClass('data_control_binding_live')) {
            args['binding_type'] = 'live';
        }
        var id = args.el.data('input-id');
        if (!id) {
            id = args.el.prop('id');
        }
        if('wp_editor' !== type){
            var exist = this.ControlRegistry.lookup(id);
            if (exist !== null) {
                exist.setElement(args.el).render();
                return exist;
            }
        }
        var control = api.Views.get_control(type);
        return new control(args);

    };

    // Register core controls
    api.Views.register_control('wp_editor', {
        initialize: function () {
            this.render();
        },
        render: function () {
            var that = this,
                    timer = 'refresh' === this.binding_type && this.selector === undefined ? 1000 : 50,
                    this_option_id = this.$el.prop('id'),
                    previous = false,
                    is_widget = false,
                    callback = _.throttle(function (e) {
                        var content = this.type === 'setupeditor' ? this.getContent() : $(this).val();
                        if (api.activeModel === null || previous === content) {
                            return;
                        }
                        previous = content;
                        if (is_widget !== false) {
                            that.$el.val(content).trigger('change');
                        }
                        else {
                            that.preview_element(content);
                        }
                    }, timer);
            api.Utils.initQuickTags(this_option_id);
            if (tinyMCE !== undefined && this.binding_type !== undefined) {

                if (typeof tinymce.editors[ this_option_id ]!=='undefined') { // clear the prev editor
                    tinyMCE.execCommand('mceRemoveEditor', true, this_option_id);
                }

                var ed = api.Utils.initNewEditor(this_option_id);
                    is_widget = this.$el.hasClass('wp-editor-area') ? this.$el.closest('#instance_widget').length > 0 : false;
				
				// Backforward compatibility
                ! ed.type && ( ed.type = 'setupeditor' );

                ed.on('change keyup', callback);
            }
            this.$el.on('change keyup', callback);
            return this;
        }
    });

    api.Views.register_control('change', {
        initialize: function () {
            this.render();
        },
        render: function () {
            var that = this,
                timer = 'refresh' === this.binding_type && this.selector === undefined ? 1000 : 50;
            var event = this.$el.data('control-event');
            if (event === undefined || event === '') {
                event = 'change';
                timer = 1;
            }
            this.$el.on(event, _.throttle(function (e) {
                that.preview_element(e.target.value);
            }, timer));
            return this;
        }
    });


    api.Views.register_control('query_category', {
        initialize: function () {
            this.render();
        },
        render: function () {
            var that = this,
                    parent = that.$el.parent(),
                    single_cat = parent.find('.query_category_single'),
                    multiple_cat = parent.find('.query_category_multiple');

            single_cat.add(multiple_cat).on('change', function (e) {
                var is_single = $(this).hasClass('query_category_single'),
                        option_value = !is_single ? (multiple_cat.val() + '|multiple') : (single_cat.val() + '|single');
                if (is_single) {
                    multiple_cat.val($(this).val());
                }
                that.preview_element(option_value);
            });
            return this;
        }
    });
    api.Views.register_control('layout', {
        initialize: function () {
            this.render();
        },
        render: function () {
			var that = this,
				this_option_id = this.$el.data('input-id'),
				defaultLayout = that.$('.tfl-icon.selected').prop('id');

				if(!this_option_id){
					this_option_id = this.$el.prop('id');
				}

			this.$( '.tfl-icon' ).click(function ( e ) {
				e.preventDefault();

				var $this = $( this ),
                                    selectedLayout = $this.prop('id');
				
				$this.addClass( 'selected' ).siblings().removeClass( 'selected' );
                                api.hasChanged = true;
                                if('visual' === api.mode){
                                    if ('live' === that.binding_type && that.$el.data('control-selector') !== undefined) {
                                            var $elmtToApplyTo = api.liveStylingInstance.$liveStyledElmt,
                                                    prevLayout = api.liveStylingInstance.getStylingVal(this_option_id);
                                            if (that.$el.data('control-selector') !== '') {
                                                    $elmtToApplyTo = api.liveStylingInstance.$liveStyledElmt.find(that.$el.data('control-selector'));
                                            }
                                            tempSettings[ this_option_id ] = selectedLayout;
                                            if (this_option_id === 'layout_feature') {
                                                    selectedLayout = 'layout-' + selectedLayout;
                                                    prevLayout = 'layout-' + prevLayout;
                                            }
                                            else if (this_option_id === 'columns') {
                                                    selectedLayout = this_option_id + '-' + selectedLayout;
                                                    prevLayout = this_option_id + '-' + prevLayout;
                                            }
                                            if (!prevLayout && defaultLayout) {
                                                    prevLayout = defaultLayout;
                                            }
                                            $elmtToApplyTo.removeClass(prevLayout).addClass(selectedLayout);

                                            if (this_option_id === 'layout_feature') {
                                                    selectedLayout = selectedLayout.substr(7);
                                            }
                                            else if (this_option_id === 'columns') {
                                                    selectedLayout = selectedLayout.substr(8);
                                            }
                                            api.liveStylingInstance.setStylingVal(this_option_id, selectedLayout);
                                            Themify.body.trigger('builder_load_module_partial', [api.liveStylingInstance.$liveStyledElmt, api.liveStylingInstance.type]);

                                    } else {
                                        if(this_option_id==='row_height' || this_option_id==='row_width'){
                                            Themify.body.trigger('tb_row_'+this_option_id.replace('row_',''),selectedLayout);
                                        }
                                        else{
                                            that.preview_element(selectedLayout);
                                        }
                                        
                                    }
                                }
            });
            return this;
        }
    });


    api.Views.register_control('checkbox', {
        initialize: function () {
            this.render();
        },
        render: function () {
            var that = this;
            this.$('input[type="checkbox"]').click(function () {
                if (that.binding_type !== undefined) {
                    var checked = that.$('input[type="checkbox"]:checked').map(function () {
                        return this.value;
                    }).get();
                    that.preview_element(checked.join('|'));
                }
            });
            return this;
        }
    });

    api.Views.register_control('color', {
        is_typing: false,
        initialize: function () {
            this.render();
        },
        render: function () {
            var that = this,
                    $colorOpacity = this.$el.next('.color_opacity'),
                    id = this.$el.prop('id');
            this.$el.minicolors({
                opacity: 1,
                changeDelay: 200,
                beforeShow: function () {
                    var lightbox = ThemifyBuilderCommon.Lightbox.$lightbox,
                            p = that.$el.closest('.minicolors'),
                            el = p.find('.minicolors-panel');
                    el.css('visibility', 'hidden').show();//get offset
                    if ((lightbox.offset().left + lightbox.width()) <= el.offset().left + el.width()) {
                        p.addClass('tb_minicolors_right');
                    }
                    else {
                        p.removeClass('tb_minicolors_right');
                    }
                    el.css('visibility', '').hide();
                },
                hide:function(){
                    var btn = that.$el.siblings('.tb_clear_btn');
                    that.$el.val()!==''?btn.show():btn.hide();
                },
                change: function (hex, opacity) {
                    if (!hex) {
                        opacity = '';
                    }
                    else if (opacity && '0.99' == opacity) {
                        opacity = 1;
                    } else if (opacity && 0 >= parseFloat( opacity ) ){
                        opacity = 0;
                    }
                    if (!that.is_typing && !$colorOpacity.is(':focus')) {
                        $colorOpacity.attr('data-opacity', opacity).data('opacity', opacity).val(opacity);
                    }
                    if(hex && 0 >= parseFloat($(this).minicolors('opacity'))){
                        $(this).minicolors('opacity',0);
                    }
                    var value = hex ? $(this).minicolors('rgbaString') : '';
                    if (that.binding_type !== undefined) {
                        that.preview_element(value);
                    }
                    else if (api.mode === 'visual') {
                        Themify.body.trigger('themify_builder_color_picker_change', [id, that.$el, hex ? value : '']);
                        if(id.indexOf('font_color') !== -1){
                            var textTag = '';
                            if('font_color' != id){
                                textTag = '_' + id.slice(-2);
                            }
                            var lightbox = ThemifyBuilderCommon.Lightbox.$lightbox,
                                $gradient = lightbox.find('#font_color_type'+textTag+'_font_gradient_color'+textTag+'_gradient'),
                                $solid = lightbox.find('#font_color_type'+textTag+'_font_color'+textTag+'_solid');
                            if($solid.length && $gradient.length){
                                $gradient.click();
                                $solid.click();
                            }
                        }
                    }
                }
            }).minicolors('show');

            that.el.parentNode.insertAdjacentHTML('beforeend','<div class="tb_clear_btn"></div>');
            api.Utils.createClearBtn( that.$el );
            $colorOpacity.on('blur keyup', function (e) {
                var opacity = parseFloat($.trim($(this).val()));
                if (opacity > 1 || isNaN(opacity) || opacity === '' || opacity < 0) {
                    opacity = !that.$el.val() ? '' : 1;
                    if (e.type === 'blur') {
                        $(this).val(opacity);
                    }
                }
                $(this).attr('data-opacity', opacity);
                that.is_typing = 'keyup' === e.type;
                that.$el.minicolors('opacity', opacity);
            });
        }
    });

    api.Views.register_control('repeater', {
        events: {
            'click .toggle_row': 'toggleField',
            'click .tb_duplicate_row': 'duplicateRowField',
            'click .tb_delete_row': 'deleteRowField'
        },
        initialize: function () {
            this.render();
        },
        render: function () {
            var el = this.$el,
                    that = this,
                    toggleCollapse = false;

            // sortable accordion builder
            el.sortable({
                items: '.tb_repeatable_field',
                handle: '.tb_repeatable_field_top',
                axis: 'y',
                placeholder: 'tb_state_highlight',
                tolerance: 'pointer',
                cursor: 'move',
                start: _.debounce(function (e, ui) {
                    if (tinyMCE !== undefined) {
                        el.find('.tb_lb_wp_editor').each(function () {
                            var id = $(this).prop('id'),
                                    content = tinymce.get(id).getContent();
                            $(this).data('content', content);
                            tinyMCE.execCommand('mceRemoveEditor', false, id);
                        });
                    }
                }, 300),
                stop: _.debounce(function (e, ui) {
                    if (tinyMCE !== undefined) {
                        el.find('.tb_lb_wp_editor').each(function () {
                            var id = $(this).prop('id');
                            tinyMCE.execCommand('mceAddEditor', false, id);
                            tinymce.get(id).setContent($(this).data('content'));
                        });
                    }

                    if (toggleCollapse) {
                        ui.item.removeClass('collapsed').find('.tb_repeatable_field_content').show();
                        toggleCollapse = false;
                    }
                    el.find('.tb_state_highlight').remove();
                    that.preview_element();
                }, 300),
                sort: function (e, ui) {
                    el.find('.tb_state_highlight').height(30);
                },
                beforeStart: function (event,el, ui) {
                    if (!ui.item.hasClass('collapsed')) {
                        ui.item.addClass('collapsed').find('.tb_repeatable_field_content').hide();
                        toggleCollapse = true;
                        el.sortable('refresh');
                    }
                }
            });

            return this;
        },
        toggleField: function (e) {
            e.preventDefault();
            $(e.currentTarget).closest('.tb_repeatable_field').toggleClass('collapsed').find('.tb_repeatable_field_content').slideToggle();
        },
        duplicateRowField: function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.$el.next('.add_new').find('a').trigger('click', $(e.currentTarget).closest('.tb_repeatable_field'));
            this.preview_element();
        },
        deleteRowField: function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (confirm(themifyBuilder.i18n.rowDeleteConfirm)) {
                $(e.currentTarget).closest('.tb_repeatable_field').remove();
                this.preview_element();
            }
        }
    });
    
    api.Views.register_control('widget_select', {
        data:null,
        cache:[],
        mediaInit:null,
        textInit:null,
        initialize: function () {
            
            this.render();
        },
        search:function(){
            var self = this;
            this.$el.closest('.tb_input').find('#widgets-search')
            .focus(this.show.bind(this))
            .blur(function(e){
                if(!e.relatedTarget || e.relatedTarget.id!=='available-widgets'){
                    self.hide();
                }
            })
            .keyup(function(){
                self.$el.show();
                var val = $(this).val().trim(),
                    r = new RegExp(val, 'i'),
                    items = self.el.getElementsByClassName('widget-tpl');
                    for(var i=0,len=items.length;i<len;++i){
                        if(val===''){
                            items[i].style['display'] = 'block';
                        }
                        else{
                            var title = items[i].getElementsByTagName('h3')[0];
                            title = title.textContent || title.innerText;
                            if(r.test(title)){
                                items[i].style['display'] = 'block';
                            }
                            else{
                                items[i].style['display'] = 'none';
                            }
                        }
                    }
                
            });
        },
        show:function(){
            this.$el.next('.tb_field_error_msg').remove();
            this.$el.parent().show();
        },
        hide:function(){
            this.$el.parent().hide();
        },
        select:function(item,settings_instance){
            var self = this,
                val = item.data('value'),
                base = item.data('idbase'),
                instance =$('#instance_widget', ThemifyBuilderCommon.Lightbox.$lightbox),
                callback = function (data) {
                        var initjJS = function () {
                                var form = $(data.form);
                                instance.addClass('open').html(form.html());
                                if (settings_instance) {
                                   for (var i in settings_instance) {
                                       form.find('[name="' + i + '"]').val(settings_instance[i]);
                                   }
                               }
                                form = null;
                                if (base === 'text') {
                                    if(wp.textWidgets){
                                        if (!self.textInit) {
                                            self.textInit = true;
                                            wp.textWidgets.init();
                                        }
                                        if(settings_instance){
                                            delete wp.textWidgets.widgetControls[settings_instance['widget-id']];
                                        }
                                    }
                                    
                                } else if (wp.mediaWidgets) {
                                    if(!self.mediaInit){
                                        wp.mediaWidgets.init();
                                        self.mediaInit = true;
                                    }
                                    if(settings_instance){
                                        delete wp.mediaWidgets.widgetControls[settings_instance['widget-id']];
                                    }
                                }
                             $(document).trigger('widget-added', [instance]); 
                                base === 'text' && api.Views.init_control('wp_editor', {el: instance.find('.wp-editor-area'), binding_type: 'refresh'});
                                
                                if(settings_instance){
                                    setTimeout(function(){
                                        new SimpleBar(top_iframe.getElementById('tb_options_setting'));
                                        new SimpleBar(self.el);
                                    },100);//widget animation delay is 50
                                }
                                else{
                                    api.mode === 'visual' && val && instance.find(':input').first().trigger('change');
                                }
                                instance.removeClass('tb_loading_widgets_form').find('select').wrap('<span class="selectwrapper"/>');
                            },
                            extra = function(data){
                                var str = '';
                                if(typeof data==='object'){
                                    for(var i in data){
                                        if(data[i]){
                                            str+=data[i];
                                        }
                                    }
                                }
                                if(str!==''){
                                    var s = document.createElement('script');
                                    s.type = 'text/javascript';
                                    s.text = str;
                                    var t = document.getElementsByTagName('script')[0];
                                    t.parentNode.insertBefore(s, t);
                                }
                            },
                            recurisveLoader = function (js, i) {
                                var len = js.length,
                                    loadJS = function (src, callback) {
                                        Themify.LoadAsync(src, callback, data.v);
                                    };
                                loadJS(js[i].src, function () {
                                    if(js[i].extra && js[i].extra.after){
                                        extra(js[i].extra.after);
                                    }
                                    ++i;
                                    i < len ? recurisveLoader(js, i) : initjJS();
                                });
                            };
                   
                   
                    if (self.cache[base] === undefined) {
                        data.template && document.body.insertAdjacentHTML('beforeend', data.template);
                        data.src.length > 0 ? recurisveLoader(data.src, 0):initjJS();
                    } else{
                        initjJS();
                    }
                };

                tempSettings['class_widget'] = val;
                instance.addClass('tb_loading_widgets_form').html('<i class="fa fa-refresh fa-spin"></i>');

                // backward compatibility with how Widget module used to save data
                $.each( settings_instance, function( i, v ) {
                        var old_pattern = i.match( /.*\[\d\]\[(.*)\]/ );
                        if ( $.isArray( old_pattern ) && old_pattern[1] !== 'undefined' ) {
                                delete settings_instance[ i ];
                                settings_instance[ old_pattern[1] ] = v;
                        }
                } );

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: themifyBuilder.ajaxurl,
                    data: {
                        action: 'module_widget_get_form',
                        tb_load_nonce: themifyBuilder.tb_load_nonce,
                        load_class: val,
                        tpl_loaded: self.cache[base]===1?1:0,
                        id_base: base,
                        widget_instance: settings_instance
                    },
                    success: function (data) {
                        if (data && data.form) {
                            callback(data);
                            self.cache[base] = 1;
                        }
                    }
                });
        },
        render: function () {
            var self = this,
                el = api.activeModel.get('mod_settings'),
                search_el = self.$el.closest('.tb_input').find('#widgets-search'),
                loader = search_el.prev('.tb_loading_widgets'),
                callback = function(){
                    var s = el[self.$el.prop('id')];
                    self.el.insertAdjacentHTML('afterbegin',self.data);
                    function select_widget(item,instance){
                        item.addClass('selected').siblings().removeClass('selected');
                        self.select(item,instance);
                        self.$el.removeAttr('data-validation');
                        search_el.val(item.find('.widget-title').text());
                        self.hide();
                        self.$el.find('.widget-tpl').show();
                    }
                    if(s){
                        var item = self.$el.find('[data-value="'+s+'"]');
                        select_widget(item,el['instance_widget']);
                    }
                    else{
                        new SimpleBar(self.el);
                    }
                    self.$el.find('.widget-tpl').click(function(){
                        select_widget($(this),null);
                    });
                    self.search();
                    loader.remove();
                    loader = null;
                    api.hasChanged = true;
                };
            loader.show();
            if(this.data===null){
                for(var j in themifyBuilder.widget_css){
                    window.top.Themify.LoadCss(themifyBuilder.widget_css[j]);
                }
                themifyBuilder.widget_css = null;
               
                $.ajax({
                    type: 'POST',
                    url: themifyBuilder.ajaxurl,
                    data: {
                            action: 'tb_get_widget_items',
                            nonce: themifyBuilder.tb_load_nonce
                    },
                    success: function ( data ) {
                        self.data = data;
                        callback();
                    }
                });
            }
            else{
                setTimeout(callback,1);
            }
            return this;
        }
    });
    
    api.Views.register_control('widget_form', {
        initialize: function () {
            this.render();
        },
        render: function () {
            var self =  this;
            this.$el.on('change', ':input', function(){
                self.preview_element(self.$el.find(':input').themifySerializeObject());
            });
            return this;
        }
    });
    
    api.Views.register_control('layout_part', {
        data:[],
        get:function(callback){
            var self = this;
            $.ajax({
                    type: 'POST',
                    url: themifyBuilder.ajaxurl,
                    dataType:'json',
                    data: {
                            action: 'tb_get_library_items',
                            nonce: themifyBuilder.tb_load_nonce,
                            pid: themifyBuilder.post_ID
                    },
                    beforeSend: function ( xhr ) {
                        ThemifyBuilderCommon.showLoader('show');
                    },
                    success: function ( data ) {
                            ThemifyBuilderCommon.showLoader('hide');
                            self.data = data;
                            callback();
                    },
                    error: function() {
                        ThemifyBuilderCommon.showLoader( 'error' );
                    }
            });
        },
        initialize: function () {
            this.render();
        },
        render: function () {
            var self = this;
            function callback(){
                   var opt='<option></<option>',
                        s = api.activeModel.get('mod_settings')[self.$el.prop('id')];
                    for(var i=0,len=self.data.length;i<len;++i){
                        var selected = s===self.data[i].post_name?' selected="selected"':'';
                        opt+= '<option'+selected+' value="'+self.data[i].post_name+ '">' +self.data[i].post_title+ '</option>';
                    }
                    self.el.insertAdjacentHTML('afterbegin',opt);
                    self.$el.change(function () {
                        self.preview_element(this.value);
                    });
            }
            if(this.data.length===0){
                this.get(callback);
            }
            else{
               callback();
            }
            return this;
        }
    });
})(jQuery);

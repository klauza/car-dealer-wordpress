/*!
 * ThemifyGradient
 * Enhanced version of ClassyGradient, with RGBA support
 *
 * Original script written by Marius Stanciu - Sergiu <marius@vox.space>
 * Licensed under the MIT license https://vox.SPACE/LICENSE-MIT
 * Version 1.1.1
 *
 */

(function ($) {

    $.ThemifyGradient = function (element, options) {
        var defaults = {
            gradient: $.ThemifyGradient.default,
            width: 200,
            height: 15,
            point: 8,
            angle: 180,
            circle: false,
            type: 'linear', // [linear / radial]
            onChange: function () {
            },
            onInit: function () {
            }
        },
        $element = $(element), $pointsContainer,
                $pointsInfosContent, $pointColor, $pointPosition, $btnPointDelete, _context, _selPoint,
                points = new Array();
        this.isInit = false;
        this.settings = {};
        this.__constructor = function () {
            this.settings = $.extend({}, defaults, options);
            this.update();
            this.settings.onInit();
            this.isInit = true;
            return this;
        };
        this.updateSettings = function (options) {
            this.settings = $.extend({}, defaults, options);
            this.update();
            return this;
        };
        this.update = function () {
            this._setupPoints();
            this._setup();
            this._render();
        };
        this.getCSSvalue = function () {
            var defDir = this.settings.angle + 'deg,';
            if (this.settings.type === 'radial') {
                defDir = this.settings.circle ? 'circle,' : ''; /* Radial gradients don't have angle */
            }
            var defCss = [];
            for (var i = 0, len = points.length; i < len; ++i) {
                defCss.push(points[i][1] + ' ' + points[i][0]);
            }
            return this.settings.type + '-gradient(' + defDir + defCss.join(', ') + ')';
        };
        this.getString = function () {
            var out = '';
            for (var i = 0, len = points.length; i < len; ++i) {
                out += points[i][0] + ' ' + points[i][1] + '|';
            }
            out = out.substr(0, out.length - 1);
            return out;
        };
        this.setType = function (type) {
            this.settings.type = type;
            this.settings.onChange(this.getString(), this.getCSSvalue());
        };
        this.setAngle = function (angle) {
            this.settings.angle = angle;
            this.settings.onChange(this.getString(), this.getCSSvalue());
        };
        this.setRadialCircle = function (circle) {
            this.settings.circle = circle;
            this.settings.onChange(this.getString(), this.getCSSvalue());
        };
        this._setupPoints = function () {
            points = new Array();
            if ($.isArray(this.settings.gradient)) {
                points = this.settings.gradient;
            }
            else {
                points = this._getGradientFromString(this.settings.gradient);
            }
        };
        this._setup = function () {
            var self = this,
            fragment = document.createDocumentFragment(),
            pointInfoFragment = document.createDocumentFragment(),
            _container = document.createElement('div'),
            pointsInfos = document.createElement('div'),
            delimiter = document.createElement('span'),
            _canvas = document.createElement('canvas'),
            percent = document.createElement('span');
            
            $btnPointDelete = document.createElement('a');
            $pointColor = document.createElement('div');
            $pointPosition = document.createElement('input');
            $pointsContainer =  document.createElement('div');
            $pointsInfosContent = document.createElement('div');
            
            _container.setAttribute('class','themifyGradient');
            _canvas.setAttribute('width',this.settings.width);
            _canvas.setAttribute('height',this.settings.height);
            $pointsContainer.style['width'] = this.settings.width + Math.round(this.settings.point / 2 + 1)+'px';
            $pointsContainer.setAttribute('class','points');
            $pointColor.setAttribute('class','point-color');
            delimiter.setAttribute('class','gradient_delimiter');
            percent.setAttribute('class','gradient_percent');
            percent.innerHTML = '%';
            $pointPosition.setAttribute('type', 'text');
            $pointPosition.setAttribute('class','point-position');
            $btnPointDelete.setAttribute('class', 'gradient-point-delete');
            $btnPointDelete.setAttribute('href', '#');
            pointsInfos.setAttribute('class','gradient-pointer-info');
            $pointsInfosContent.setAttribute('class','content');
            $pointColor.insertAdjacentHTML('afterbegin', '<div style="background-color: #00ff00"></div>');
            $btnPointDelete.insertAdjacentHTML('afterbegin', '<i class="ti-close"></i>');
            pointsInfos.insertAdjacentHTML('afterbegin', '<div class="gradient-pointer-arrow"></div>');
            pointInfoFragment.appendChild($pointColor);
            pointInfoFragment.appendChild(delimiter);
            pointInfoFragment.appendChild($pointPosition);
            pointInfoFragment.appendChild(percent);
            pointInfoFragment.appendChild($btnPointDelete);
            $pointsInfosContent.appendChild(pointInfoFragment);
            fragment.appendChild(_canvas);
            fragment.appendChild($pointsContainer);
            pointsInfos.appendChild($pointsInfosContent);
            
            fragment.appendChild(pointsInfos);
            _container.appendChild(fragment);
            
            pointInfoFragment = delimiter = percent = fragment = null;
            
            $element[0].innerHTML = '';
            $element[0].appendChild(_container);
            
            _container = pointsInfos = null;
            
            $pointsInfosContent = $($pointsInfosContent);
            $pointColor = $($pointColor);
            $pointPosition = $($pointPosition);
            $btnPointDelete = $($btnPointDelete);
            $pointsContainer = $($pointsContainer);
            _context = _canvas.getContext('2d');
            
            _canvas = $(_canvas);
            
            _canvas.off('click').on('click', function (e) {
                var offset = _canvas.offset(),
                        clickPosition = e.pageX - offset.left;
                clickPosition = Math.round((clickPosition * 100) / self.settings.width);
                var defaultColor = 'rgba(0,0,0, 1)', minDist = 999999999999;
                for (var i = 0, len = points.length; i < len; ++i) {
                    points[i][0] = parseInt(points[i][0]);
                    if ((points[i][0] < clickPosition) && (clickPosition - points[i][0] < minDist)) {
                        minDist = clickPosition - points[i][0];
                        defaultColor = points[i][1];
                    }
                    else if ((points[i][0] > clickPosition) && (points[i][0] - clickPosition < minDist)) {
                        minDist = points[i][0] - clickPosition;
                        defaultColor = points[i][1];
                    }
                }
                points.push([clickPosition + '%', defaultColor]);
                points.sort(self._sortByPosition);
                self._render();
                for (var i = 0, len = points.length; i < len; ++i) {
                    if (points[i][0] === clickPosition + '%') {
                        self._selectPoint($pointsContainer.find('.point:eq(' + i + ')'));
                    }
                }
                if (themifybuilderapp.mode === 'visual') {
                    setTimeout(self._colorPickerPosition, 315);
                }

            });
            this.pointEvents();
        };
        this.pointEvents = function () {
            var self = this;
            $element.find('.point-position').off('keyup focusout').on('keyup focusout', function (e) {
                var $val = parseInt($.trim($(this).val()));
                if (isNaN($val)) {
                    $val = 0;
                }
                else if ($val < 0) {
                    $val = Math.abs($val);
                }
                else if ($val >= 98) {
                    $val = 98;
                }
                if (e.type !== 'focusout') {
                    $val = Math.round(($val * self.settings.width) / 100);
                    $(this).closest('.themifyGradient').find('.themify_current_point').css('left', $val);
                    self._renderCanvas();
                }
                else {
                    $(this).val($val);
                }
            });
        };
        this._render = function () {
            this._initGradientPoints();
            this._renderCanvas();
        };
        this._colorPickerPosition = function () {
            var lightbox = ThemifyBuilderCommon.Lightbox.$lightbox,
                    p = $pointsInfosContent.find('.minicolors'),
                    el = p.find('.minicolors-panel');
            if ((lightbox.offset().left + lightbox.width()) <= el.offset().left + el.width()) {
                p.addClass('tb_minicolors_right');
            }
            else {
                p.removeClass('tb_minicolors_right');
            }
        };
        this._initGradientPoints = function () {
            var self = this,
                    html = '';
            $pointsContainer.empty();
            for (var i = 0, len = points.length; i < len; ++i) {
                html += '<div class="point" style="background-color: ' + points[i][1] + '; left:' + (parseInt(points[i][0]) * self.settings.width) / 100 + 'px;"></div>';
            }
            $pointsContainer[0].insertAdjacentHTML('afterbegin', html);
            html = null;
            $pointsContainer.find('.point').off('click').on('click', function () {
                self._selectPoint(this);
                if (themifybuilderapp.mode === 'visual') {//fix drag/drop window focus
                    self._colorPickerPosition();
                    $(document).trigger('mouseup');
                }
            }).draggable({
                axis: 'x',
                containment: 'parent',
                start: function (event, ui) {
                    $element.addClass('themify_gradient_drag').focus();
                },
                stop: function (event, ui) {
                    $element.removeClass('themify_gradient_drag').focus();
                    if (themifybuilderapp.mode === 'visual') {
                        $(document).trigger('mouseup');
                    }
                },
                drag: function (event, ui) {
                    self._selectPoint(this, true);
                    self._renderCanvas();
                }
            });
        };
        this.hexToRgb = function (hex) {
            // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
            var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function (m, r, g, b) {
                return r + r + g + g + b + b;
            });

            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        };
        this._selectPoint = function (el, is_drag) {
            var self = this;
            _selPoint = $(el);
            var left = parseInt(_selPoint.css('left'));
            $pointPosition.val(Math.round((left / this.settings.width) * 100));
            left -= 30;
            if (left < 0 && top_iframe.body.classList.contains('tb_module_panel_docked')) {
                left = 3;
            }
            $element.find('.gradient-pointer-info').css('marginLeft', left + 'px');
            if (is_drag) {
                return false;
            }
            $element.focus();
            _selPoint.addClass('themify_current_point').siblings().removeClass('themify_current_point');
            var bgColor = _selPoint.css('backgroundColor'),
                    color = bgColor.substr(4, bgColor.length);
            color = color.substr(0, color.length - 1);
            $element.find('.point-color div').remove();

            // create the color picker element
            var $input = $pointColor.find('.themify-color-picker');
            if ($input.length === 0) {
                $input = $('<input type="text" class="themify-color-picker" />');
                $input.appendTo($pointColor).minicolors({
                    opacity: true,
                    changeDelay: 200,
                    change: function (value, opacity) {
                        var rgb = self.hexToRgb(value);
                        if (!rgb) {
                            rgb = {r: 255, g: 255, b: 255};
                            opacity = 1;
                        }
                        _selPoint.css('backgroundColor', 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + opacity + ')');
                        self._renderCanvas();
                    }
                });
                $element.find('.minicolors').first().addClass('minicolors-focus');
                $btnPointDelete.off('click').on('click', function (e) {
                    e.preventDefault();
                    if (points.length > 1) {
                        points.splice(_selPoint.index(), 1);
                        self._render();
                    }
                });
            }
            var rgb = bgColor.replace(/^rgba?\(|\s+|\)$/g, '').split(','),
                    opacity = rgb.length === 4 ? rgb.pop() : 1; // opacity is the last item in the array
            rgb = this._rgbToHex(rgb);
            // set the color for colorpicker
            $input.val(rgb).attr('data-opacity', opacity).data('opacity', opacity).minicolors('settings', {value: rgb});
        };
        this._renderCanvas = function () {
            var self = this;
            points = new Array();
            $element.find('.point').each(function () {
                var position = Math.round((parseInt($(this).css('left')) / self.settings.width) * 100);
                points.push([position + '%', $(this).css('backgroundColor')]);
            });
            points.sort(self._sortByPosition);
            this._renderToCanvas();
            if (this.isInit) {
                this.settings.onChange(this.getString(), this.getCSSvalue());
            }
        };
        this._renderToCanvas = function () {
            var gradient = _context.createLinearGradient(0, 0, this.settings.width, 0);
            for (var i = 0, len = points.length; i < len; ++i) {
                gradient.addColorStop(parseInt(points[i][0]) / 100, points[i][1]);
            }
            _context.clearRect(0, 0, this.settings.width, this.settings.height);
            _context.fillStyle = gradient;
            _context.fillRect(0, 0, this.settings.width, this.settings.height);
        };
        this._getGradientFromString = function (gradient) {
            var arr = new Array(),
                    points = gradient.split('|');
            for (var i = 0, len = points.length; i < len; ++i) {
                var position,
                        el = points[i],
                        index = el.indexOf('%'),
                        sub = el.substr(index - 3, index);
                if (sub === '100' || sub === '100%') {
                    position = '100%';
                }
                else if (index > 1) {
                    position = parseInt(el.substr(index - 2, index));
                    position += '%';
                }
                else {
                    position = parseInt(el.substr(index - 1, index));
                    position += '%';
                }
                arr.push([position, el.replace(position, '')]);
            }
            return arr;
        };
        this._rgbToHex = function (rgb) {
            var R = rgb[0], G = rgb[1], B = rgb[2];
            function toHex(n) {
                n = parseInt(n, 10);
                if (isNaN(n)) {
                    return '00';
                }
                n = Math.max(0, Math.min(n, 255));
                return '0123456789ABCDEF'.charAt((n - n % 16) / 16) + '0123456789ABCDEF'.charAt(n % 16);
            }
            return '#' + toHex(R) + toHex(G) + toHex(B);
        };
        this._sortByPosition = function (data_A, data_B) {
            data_A = parseInt(data_A[0]);
            data_B = parseInt(data_B[0]);
            return data_A < data_B ? -1 : (data_A > data_B ? 1 : 0);
        };
        return this.__constructor();
    };
    $.ThemifyGradient.default = '0% rgba(0,0,0, 1)|100% rgba(255,255,255,1)';
    $.fn.ThemifyGradient = function (options) {
        return this.each(function () {
            if ($(this).data('themifyGradient') === undefined) {
                $(this).data('themifyGradient', new $.ThemifyGradient(this, options));
            }
        });
    };
})(jQuery);
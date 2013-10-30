/**
 * @class кнопки "копировать/вырезать/вставить"
 * в firefox показывает предложение нажать Ctl+c, в остальных - копирует
 *
 * @param  elRTE  rte   объект-редактор
 * @param  String name  название кнопки
 *
 * @author:    Dmitry Levashov (dio) dio@std42.ru
 * @copyright: Studio 42, http://www.std42.ru
 **/
(function ($) {
    elRTE.prototype.ui.prototype.buttons.copy = function (rte, name) {
        this.constructor.prototype.constructor.call(this, rte, name);

        this.command = function () {

        this.constructor.prototype.command.call(this);
        }
    }

    elRTE.prototype.ui.prototype.buttons.cut = elRTE.prototype.ui.prototype.buttons.copy;
    elRTE.prototype.ui.prototype.buttons.paste = elRTE.prototype.ui.prototype.buttons.copy;

})(jQuery);
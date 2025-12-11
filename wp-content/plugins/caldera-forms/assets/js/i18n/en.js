/*!
* Parsleyjs
* Guillaume Potier - <guillaume@wisembly.com>
* Version 2.2.0-rc2 - built Tue Oct 06 2015 10:20:13
* MIT Licensed
*
*/
!(function (factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module depending on jQuery.
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    // Node/CommonJS
    module.exports = factory(require('jquery'));
  } else {
    // Register plugin with global jQuery object.
    factory(jQuery);
  }
}(function ($) {
  // small hack for requirejs if jquery is loaded through map and not path
  // see http://requirejs.org/docs/jquery.html
  if ('undefined' === typeof $ && 'undefined' !== typeof window.jQuery)
    $ = window.jQuery;
// ParsleyConfig definition if not already set
window.ParsleyConfig = window.ParsleyConfig || {};
window.ParsleyConfig.i18n = window.ParsleyConfig.i18n || {};
// Define then the messages
window.ParsleyConfig.i18n.en = jQuery.extend(window.ParsleyConfig.i18n.en || {}, {
  defaultMessage: "This value seems to be invalid.",
  type: {
    email:        "Giá trị này phải là một email hợp lệ.",
    url:          "Giá trị này phải là một url hợp lệ.",
    number:       "Giá trị này phải là một số hợp lệ.",
    integer:      "Giá trị này phải là một số nguyên hợp lệ.",
    digits:       "Giá trị này phải là chữ số.",
    alphanum:     "Giá trị này phải là chữ và số."
  },
  notblank:       "Không được bỏ trống giá trị này.",
  required:       "Giá trị này là bắt buộc.",
  pattern:        "Giá trị này dường như không hợp lệ.",
  min:            "Giá trị này phải lớn hơn hoặc bằng %s.",
  max:            "Giá trị này phải thấp hơn hoặc bằng %s.",
  range:          "Giá trị này phải nằm trong khoảng từ %s đến %s.",
  minlength:      "Giá trị này quá ngắn. Nó nên có %s ký tự trở lên.",
  maxlength:      "Giá trị này quá dài. Nó nên có %s ký tự hoặc ít hơn.",
  length:         "Độ dài giá trị này không hợp lệ. Nó phải dài từ %s đến %s ký tự.",
  mincheck:       "Bạn phải chọn ít nhất %s lựa chọn.",
  maxcheck:       "Bạn phải chọn %s hoặc ít hơn.",
  check:          "Bạn phải chọn giữa các lựa chọn %s và %s.",
  equalto:        "Giá trị này phải giống nhau."
});
// If file is loaded after Parsley main file, auto-load locale
if ('undefined' !== typeof window.ParsleyValidator)
  window.ParsleyValidator.addCatalog('en', window.ParsleyConfig.i18n.en, true);
}));
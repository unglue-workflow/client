"use strict";

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Test = function Test(x) {
    _classCallCheck(this, Test);

    this._y = x;
};

var Test2 = function (_Test) {
    _inherits(Test2, _Test);

    function Test2(x) {
        _classCallCheck(this, Test2);

        var _this = _possibleConstructorReturn(this, (Test2.__proto__ || Object.getPrototypeOf(Test2)).call(this, x));

        _this._z = x;
        return _this;
    }

    return Test2;
}(Test);
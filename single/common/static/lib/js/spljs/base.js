var SplJs = {};


function merge(_old, _new) {
    for (var i in _new) {
        if (_new.hasOwnProperty(i)) {
            _old[i] = _new[i];
        }
    }
    return _old;
}

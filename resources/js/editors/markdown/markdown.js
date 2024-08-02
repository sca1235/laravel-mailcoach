import CodeMirror from 'codemirror-5/lib/codemirror';

import 'codemirror-5/addon/mode/overlay';
import 'codemirror-5/addon/edit/continuelist';
import 'codemirror-5/addon/display/placeholder';
import 'codemirror-5/addon/selection/mark-selection';
import 'codemirror-5/addon/search/searchcursor';

import 'codemirror-5/mode/clike/clike';
import 'codemirror-5/mode/cmake/cmake';
import 'codemirror-5/mode/css/css';
import 'codemirror-5/mode/diff/diff';
import 'codemirror-5/mode/django/django';
import 'codemirror-5/mode/dockerfile/dockerfile';
import 'codemirror-5/mode/gfm/gfm';
import 'codemirror-5/mode/go/go';
import 'codemirror-5/mode/htmlmixed/htmlmixed';
import 'codemirror-5/mode/http/http';
import 'codemirror-5/mode/javascript/javascript';
import 'codemirror-5/mode/jinja2/jinja2';
import 'codemirror-5/mode/jsx/jsx';
import 'codemirror-5/mode/markdown/markdown';
import 'codemirror-5/mode/nginx/nginx';
import 'codemirror-5/mode/pascal/pascal';
import 'codemirror-5/mode/perl/perl';
import 'codemirror-5/mode/php/php';
import 'codemirror-5/mode/protobuf/protobuf';
import 'codemirror-5/mode/python/python';
import 'codemirror-5/mode/ruby/ruby';
import 'codemirror-5/mode/rust/rust';
import 'codemirror-5/mode/sass/sass';
import 'codemirror-5/mode/shell/shell';
import 'codemirror-5/mode/sql/sql';
import 'codemirror-5/mode/stylus/stylus';
import 'codemirror-5/mode/swift/swift';
import 'codemirror-5/mode/vue/vue';
import 'codemirror-5/mode/xml/xml';
import 'codemirror-5/mode/yaml/yaml';

window.CodeMirror = CodeMirror;

import './EasyMDE.js';

window.CodeMirror.commands.tabAndIndentMarkdownList = function (cm) {
    var ranges = cm.listSelections();
    var pos = ranges[0].head;
    var eolState = cm.getStateAfter(pos.line);
    var inList = eolState.list !== false;

    if (inList) {
        cm.execCommand('indentMore');
        return;
    }

    if (cm.options.indentWithTabs) {
        cm.execCommand('insertTab');
    } else {
        var spaces = Array(cm.options.tabSize + 1).join(' ');
        cm.replaceSelection(spaces);
    }
};

window.CodeMirror.commands.shiftTabAndUnindentMarkdownList = function (cm) {
    var ranges = cm.listSelections();
    var pos = ranges[0].head;
    var eolState = cm.getStateAfter(pos.line);
    var inList = eolState.list !== false;

    if (inList) {
        cm.execCommand('indentLess');
        return;
    }

    if (cm.options.indentWithTabs) {
        cm.execCommand('insertTab');
    } else {
        var spaces = Array(cm.options.tabSize + 1).join(' ');
        cm.replaceSelection(spaces);
    }
};

(function($) {
  'use strict';
  var Path = '',
    editor = {};
  try {
    if (window.location.search.search(new RegExp('[?&]p=([^&$]*)', 'i')) !== -1) {
      Path = RegExp.$1;
    }
    Path = decodeURIComponent(Path);
  } catch (e) {
    alert(e);
    alert('Path=' + Path);
  }
  window.editor = editor;
  editor.detectFileMode = function(filePath) {
    if (/^\/etc\/apache2\//.test(filePath)) {
      return 'apache_conf';
    } else if (/Dockerfile$/.test(filePath)) {
      return 'dockerfile';
    }
    var ext = filePath.substring(filePath.lastIndexOf('.') + 1);
    // https://github.com/ajaxorg/ace/tree/master/lib/ace/mode
    switch (ext) {
      case 'css':
        return 'css';
      case 'js':
        return 'javascript';
      case 'json':
        return 'json';
      case 'asax':
      case 'ashx':
      case 'cs':
        return 'csharp';
      case 'xml':
        return 'xml';
      case 'phtml':
      case 'php':
        return 'php';
      case 'config':
        return 'xml';
      case 'as':
        return 'actionscript';
      case 'bat':
      case 'cmd':
        return 'batchfile';
      case 'c':
      case 'h':
      case 'hpp':
      case 'cpp':
        return 'c_cpp';
      case 'coffee':
        return 'coffee';
      case 'dart':
        return 'dart';
      case 'diff':
        return 'diff';
      case 'asp':
      case 'asa':
      case 'aspx':
      case 'ascx':
      case 'htm':
      case 'html':
        return 'html';
      case 'ini':
        return 'ini';
      case 'java':
        return 'java';
      case 'jsp':
        return 'jsp';
      case 'less':
        return 'less';
      case 'lua':
        return 'lua';
      case 'pl':
        return 'perl';
      case 'ps':
        return 'powershell';
      case 'py':
        return 'python';
      case 'cgi':
      case 'sh':
        return 'sh';
      case 'sql':
        return 'sql';
      case 'svg':
        return 'svg';
      case 'md':
        return 'markdown';
      default:
        return '';
    }
  };
  editor.detectFileModeByContent = function(content) {
    if (content.indexOf('#!/bin/sh') === 0) {
      return 'sh';
    }
    if (content.indexOf('#!/bin/bash') === 0) {
      return 'sh';
    }
    return '';
  };
  editor.save = function(close) {
    jQuery.ajax({
      url: '?',
      type: 'post',
      dataType: 'json',
      data: {
        content: editor.instance.getValue(),
        p: Path
      },
      success: function() {
        if (close) {
          history.back();
        } else {
          if (editor.isModified) {
            editor.isModified = false;
            document.title = document.title.substring(1); // remove leading '*'
          }
        }
      },
      error: function(r) {
        alert(r.responseText);
      }
    });
  };
  editor.clip = {
    getItems: function() {
      var j = localStorage.clipValue;
      return j ? JSON.parse(j) : [];
    },
    setItems: function(items) {
      localStorage.clipValue = JSON.stringify(items);
    },
    add: function(path, type) {
      var i = this.getItems();
      i.push({
        path: path,
        type: type
      });
      this.setItems(i);
    },
    remove: function(o) {
      var path = o,
        item,
        items = editor.clip.getItems();
      if (typeof (path) === 'object') {
        path = o.path;
      }
      item = items.filter(function(x) {
        return x.path === path;
      })[0];
      if (!item) {
        return false;
      }
      items.splice(items.indexOf(item), 1);
      this.setItems(items);
      return true;
    }
  };
  editor.del = function(aPath, trigger) {
    var success = false,
      div = $(trigger).parent('div');
    if (div.length === 0) {
      console.error('DEBUG: Cannot find row in DOM.');
    }
    $.ajax({
      url: '?',
      method: 'post',
      data: {
        p: aPath,
        rm: 1
      },
      async: false,
      success: function(r) {
        if (r.success === true) {
          div.remove();
          success = true;
        } else {
          alert(r.message);
        }
      },
      error: function(r) {
        alert(r.responseText);
      }
    });
    return success;
  };
  editor.cut = function(aPath) {
    editor.clip.add(aPath, 'cut');
    console.log('Clipboard: ' + aPath);
  };
  editor.copy = function(aPath) {
    editor.clip.add(aPath, 'copy');
    console.log('Clipboard: ' + aPath);
  };
  editor.paste = function(clipItemPath, dest) {
    var item,
      pasteAs,
      data;
    if (!clipItemPath) {
      clipItemPath = editor.clip.getItems()[0].path;
    }
    item = editor.clip.getItems().filter(function(x) {
      return x.path === clipItemPath;
    })[0];
    pasteAs = prompt(
      item.type === 'copy' ? 'Copy to:' : 'Move to:', item.path.substring(1 + item.path.lastIndexOf('/')));
    if (!pasteAs) {
      return;
    }
    dest += '/' + pasteAs;
    data = {
      source: item.path,
      dest: dest
    };
    if (item.type === 'copy') {
      data.cp = 1;
    } else if (item.type === 'cut') {
      data.mv = 1;
    }
    if (dest) {
      $.ajax({
        url: '?',
        method: 'post',
        data: data,
        success: function(r) {
          if (r.success === true) {
            editor.clip.remove(clipItemPath);
            window.location = window.location;
          } else {
            alert(r.message);
          }
        },
        error: function(r) {
          alert(r.responseText);
        }
      });
    }
  };
  editor.instance = null;
  (function() {
    var clipItems,
      list,
      mode;
    $('#editor').each(function() {
      $('header.list').hide();
      mode = editor.detectFileMode(Path);
      if (!mode) {
        mode = editor.detectFileModeByContent(document.getElementById('editor').textContent.trim());
      }
      editor.instance = ace.edit('editor');
      //editor.instance.setTheme('ace/theme/monokai');
      if ($(this).get(0).hasAttribute('data-readonly')) {
        document.title += ' [readonly]';
        editor.instance.setOptions({
          readOnly: true,
          highlightActiveLine: false,
          highlightGutterLine: false
        });
      }
      editor.instance.on('change', function() {
        if (!editor.isModified) {
          editor.isModified = true;
          document.title = '*' + document.title;
        }
      });
      if (mode) {
        if (typeof (console) !== 'undefined') {
          console.log('Setting editor mode: ' + 'ace/mode/' + mode);
        }
        editor.instance.getSession().setMode('ace/mode/' + mode);
      } else {
        if (typeof (console) !== 'undefined') {
          console.log('Unsupported editor mode. All available modes here: https://github.com/ajaxorg/ace-builds/tree/master/src-noconflict');
        }
      }
      editor.instance.commands.addCommand({
        name: 'Save',
        bindKey: {
          win: 'Ctrl-s',
          mac: 'Command-s'
        },
        exec: function() {
          editor.save(false);
        }
      });
    });

    // Remove any hashes from URL.
    if (window.location.hash) {
      window.location = window.location.search;
    }

    // Append clipboard files to the list.
    clipItems = editor.clip.getItems();
    list = $('#list');
    clipItems.forEach(function(clipItem) {
      var a = $('<a/>').text(clipItem.path),
        pasteButton = $('<a/>').addClass('paste').attr('href', '#'),
        removeButton = $('<a/>').addClass('remove-clip').attr('href', '#');
      list.append($('<div/>').addClass('clip').append(a).append(pasteButton).append(removeButton));
      pasteButton.click(function() {
        editor.paste(clipItem.path, Path);
        return false;
      });
      removeButton.click(function() {
        if (editor.clip.remove(clipItem.path)) {
          $(this).parent().remove();
        }
        return false;
      });
    });

    // Show the paste button for all directories if there is an item in the clipboard.
    if (clipItems.length > 0) {
      $('#list>div.dir>a.paste').css({
        display: 'table-cell'
      });
    }
  }());

  // Keyboard shortcuts.
  $(document).keydown(function(e) {
    if (e.keyCode === 115) { // F4
      $('.shellButton').click();
    }
  });
  $(function() {
    $('.searchForm select').change(function() {
      if ($(this).val() === 'Locate Database') {
        $('.searchForm input[type=checkbox]').prop('checked', false).prop('disabled', true);
      } else {
        $('.searchForm input[type=checkbox]').prop('disabled', false);
      }
    });
    $('.searchForm button').click(function() {
      var select = $('.searchForm select').val(),
        url = '?p=' + encodeURIComponent(Path).replace(/%2F/g, '/');
      if ($('.searchForm input[type=checkbox]').prop('checked')) {
        url += '&r=';
      }
      if (select === 'Filenames' || select === 'All') {
        url += '&find=' + encodeURIComponent($('.searchForm input[type=text]').val()).replace(/%2F/g, '/');
      }
      if (select === 'Content (All Files)' || select === 'All') {
        url += '&grep=' + encodeURIComponent($('.searchForm input[type=text]').val()).replace(/%2F/g, '/');
      }
      if (select === 'Content (Code Only)') {
        url += '&find=^[^.]*$|\\.(php|js|json|..?ss|p?html?|as..?|cs|vb|rb|py|txt|md|xml|xslt?|config)$&grep=' + encodeURIComponent($('.searchForm input[type=text]').val()).replace(/%2F/g, '/');
      }
      if (select === 'Locate Database') {
        url += '&locate=' + encodeURIComponent($('.searchForm input[type=text]').val().replace(/[ ]+/g, '.*')).replace(/%2F/g, '/');
      }
      window.location = url;
    });
    $('.newButton').click(function() {
      var v = prompt('New file (end with / for dir):', 'new.txt'),
        type = 'file';
      //if(v.endsWith('/')){
      if (v.substring(v.length - 1) === '/') {
        type = 'dir';
        v = v.substring(0, v.length - 1);
      }
      if (v) {
        $.ajax({
          url: '?',
          method: 'post',
          data: {
            new: 1,
            p: Path + '/' + v,
            type: type
          },
          dataType: 'json',
          success: function(r) {
            if (r.success === true) {
              window.location = '?p=' + encodeURIComponent(Path + '/' + v).replace(/%2F/g, '/');
            } else {
              alert(r.message);
            }
          },
          error: function(r) {
            alert(r.responseText);
          }
        });
      }
    });
    $('.searchButton').click(function() {
      $(this).addClass('active');
      $('.searchForm').show().find('input[type=text]:first').select();
    });
    $('.shellButton').click(function() {
      $(this).addClass('active');
      var input = $('.shellForm').show().find('input[type=text]:first').select(),
        first = true,
        continuingLastSession = false,
        blinkOn = true,
        callNext;
      callNext = function(cmd) {
        if (typeof (cmd) === 'undefined') {
          cmd = null;
        }
        $.ajax({
          url: '?',
          type: 'post',
          dataType: 'json',
          data: {
            'ajaxShell': cmd,
            p: Path
          },
          success: function(r) {
            if (r.lastCmd) {
              input.val(r.lastCmd).prop('readonly', true);
            }
            if (r.idle === true) {
              input.prop('readonly', false);
              return;
            }
            if (first === true && r.first !== true) {
              continuingLastSession = true;
              $('.shellForm input#shellFormBackground').prop('checked', true);
            }
            first = false;
            blinkOn = !blinkOn;
            $('#shellOutput').html((continuingLastSession ? 'Continuing last session...\n\n' : '') + r.output + (r.continue === true ? (blinkOn ? '_' : ' ') : ''));
            if (r.continue === true) {
              setTimeout(callNext, 500);
            } else { // Command finished
              if (r.result === 'failure') {
                $('.shellForm').css({
                  backgroundColor: '#f44'
                });
                alert('Last command failed.');
              } else if (r.result === 'success') {
                $('.shellForm').css({
                  backgroundColor: '#6f6'
                });
              } else {
                $('.shellForm').css({
                  backgroundColor: '#bbb'
                });
                alert('Last command had no result.');
              }
              window.onbeforeunload = null;
              input.prop('readonly', false).select();
            }
          }
        });
      };
      callNext();
      $('.shellForm').submit(function() {
        var shellHistory = JSON.parse(localStorage.shellHistory || '[]');
        shellHistory.push(input.val());
        localStorage.shellHistory = JSON.stringify(shellHistory);

        setTimeout(function() {
          window.onbeforeunload = function() {
            return 'You have a shell command running.';
          };
        }, 500);

        if (!$('.shellForm input#shellFormBackground').prop('checked')) {
          return true; // Normal form submit.
        }
        $('.shellForm').css({
          backgroundColor: '#fff'
        }); // prepare background for ajax command where result will set red or green background
        callNext(input.val());
        return false;
      });
    });
    $('#list .dir a[class!="seg"], #list .file a[class!="seg"]').click(function(e) {
      e.stopPropagation();
    });

    // Full row click.
    $('#list .dir, #list .file').click(function() {
      $(this).find('a.seg:last').get(0).click();
    });

    var buildShellHistoryDataList = function() {
      var datalist = $('#shellHistory'),
        shellHistory = JSON.parse(localStorage.shellHistory || '[]'),
        i,
        item;
      for (i = 0; i < shellHistory.length, item = shellHistory[i]; i += 1) {
        datalist.append($('<option/>').attr('value', item));
      }
    };
    buildShellHistoryDataList();

    var uploadDialog = null;
    var dropzoneProgress = function(percent) {
      if (uploadDialog === null) {
        uploadDialog = $('<dialog id="styledModal" />').text('Uploading ').append($('<progress max=100/>'));
        $(document.body).append(uploadDialog);
        uploadDialog.get(0).showModal();
      }
      var progress = uploadDialog.find('progress').get(0);
      progress.value = percent;
      if (percent === 100) {
        alert('Upload Complete!');
        window.location = window.location;
      }
    };
    $(document.body).dropzone({
      url: '?p=' + Path,
      clickable: false,
      previewsContainer: 'body',
      totaluploadprogress: dropzoneProgress
    });
    $('.uploadButton').dropzone({
      url: '?p=' + Path,
      clickable: true,
      previewsContainer: 'body',
      totaluploadprogress: dropzoneProgress
    });
  });
})(jQuery);

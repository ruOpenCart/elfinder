$(document).ready(function() {
  // From opencart.js
  // Override summernotes image manager
  $('[data-toggle=\'summernote\']').each(function() {
    var element = this;

    if ($(this).attr('data-lang')) {
      $('head').append('<script src="view/javascript/summernote/lang/summernote-' + $(this).attr('data-lang') + '.js"></script>');
    }

    $(element).summernote({
      lang: $(this).attr('data-lang'),
      disableDragAndDrop: true,
      height: 300,
      emptyPara: '',
      codemirror: { // codemirror options
        mode: 'text/html',
        htmlMode: true,
        lineNumbers: true,
        theme: 'monokai'
      },
      fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '30', '36', '48' , '64'],
      toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'image', 'elfinder', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']]
      ],
      popover: {
        image: [
          ['custom', ['imageAttributes']],
          ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
          ['float', ['floatLeft', 'floatRight', 'floatNone']],
          ['remove', ['removeMedia']]
        ],
      },
      buttons: {
        elfinder: function() {
          var ui = $.summernote.ui;
          var elfinder = ui.button({
            contents: '<i class="note-icon-picture"></i> elFinder',
            tooltip: $.summernote.lang[$.summernote.options.lang].image.image,
            click: function () {
              $('#modal-image').remove();
              $.ajax({
                url: 'index.php?route=extension/module/ocn_elfinder/manager&user_token=' + getURLVar('user_token') + '&textarea=' + $(element).attr('id'),
                dataType: 'html',
                success: function(html) {
                  $('body').append('<div id="modal-image" class="modal">' + html + '</div>');
                  $('#modal-image').modal('show');
                }
              });
            }
          });
          return elfinder.render();
        },
        image: function() {
          var ui = $.summernote.ui;

          // create button
          var button = ui.button({
            contents: '<i class="note-icon-picture" />',
            tooltip: $.summernote.lang[$.summernote.options.lang].image.image,
            click: function () {
              $('#modal-image').remove();

              $.ajax({
                url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token'),
                dataType: 'html',
                beforeSend: function() {
                  $('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                  $('#button-image').prop('disabled', true);
                },
                complete: function() {
                  $('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
                  $('#button-image').prop('disabled', false);
                },
                success: function(html) {
                  $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

                  $('#modal-image').modal('show');

                  $('#modal-image').delegate('a.thumbnail', 'click', function(e) {
                    e.preventDefault();

                    $(element).summernote('insertImage', $(this).attr('href'));

                    $('#modal-image').modal('hide');
                  });
                }
              });
            }
          });

          return button.render();
        }
      }
    });
  });

  // From common.js and add for elfinder
  // Image Manager elFinder
  $(document).on('click', 'a[data-toggle=\'image\']', function(e) {
    var $element = $(this);
    var $popover = $element.data('bs.popover'); // element has bs popover?

    e.preventDefault();

    // destroy all image popovers
    $('a[data-toggle="image"]').popover('destroy');

    // remove flickering (do not re-add popover when clicking for removal)
    if ($popover) {
      return;
    }

    $element.popover({
      html: true,
      placement: 'right',
      trigger: 'manual',
      content: function() {
        return '<button type="button" id="button-elfinder" class="btn btn-success"><i class="fa fa-file-image-o" aria-hidden="true"></i></button>';
      }
    });

    $element.popover('show');

    $('#button-elfinder').on('click', function() {
      var $button = $(this);
      var $icon   = $button.find('> i');

      $('#modal-image').remove();
console.log(getURLVar('user_token'))
      $.ajax({
        url: 'index.php?route=extension/module/ocn_elfinder/manager&user_token=' + getURLVar('user_token') + '&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
        dataType: 'html',
        beforeSend: function() {
          $button.prop('disabled', true);
          if ($icon.length) {
            $icon.attr('class', 'fa fa-circle-o-notch fa-spin');
          }
        },
        complete: function() {
          $button.prop('disabled', false);

          if ($icon.length) {
            $icon.attr('class', 'fa fa-pencil');
          }
        },
        success: function(html) {
          $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

          $('#modal-image').modal('show');
        }
      });

      $element.popover('destroy');
    });
  });
});

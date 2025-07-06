// file: admin_editor.js (Phiên bản Hoàn Thiện 2025)

document.addEventListener('DOMContentLoaded', () => {
    const editor = document.getElementById('content-editor');
    if (!editor) return;

    const hiddenTextarea = document.getElementById('content');
    const form = editor.closest('form');

    if (hiddenTextarea.value) {
        editor.innerHTML = hiddenTextarea.value;
    }

    function applyFontSize(newSize) {
        document.execCommand("styleWithCSS", false, true);
        document.execCommand('fontSize', false, '7');

        const elements = editor.querySelectorAll('span[style*="font-size"]');
        let selection = window.getSelection();

        elements.forEach(el => {
            if (selection.containsNode(el, true)) {
                const newPixelSize = `${parseInt(newSize, 10)}px`;
                el.style.fontSize = newPixelSize;

                const listItem = el.closest('li');
                if (listItem) {
                    listItem.style.fontSize = newPixelSize;
                }
            }
        });
        editor.focus();
    }

    window.formatDoc = function (command, value = null) {
        const listCommands = ['insertUnorderedList', 'insertOrderedList'];

        if (listCommands.includes(command)) {
            const selection = window.getSelection();
            if (selection.rangeCount > 0 && !selection.isCollapsed) {
                const element = selection.anchorNode.parentElement;

                const sizeSpan = element.closest('span[style*="font-size"]');
                const colorSpan = element.closest('span[style*="color"], font[color]');
                const existingSize = sizeSpan ? parseInt(sizeSpan.style.fontSize, 10) : null;
                const existingColor = colorSpan ? (colorSpan.style.color || colorSpan.getAttribute('color')) : null;

                document.execCommand(command, false, value);

                if (existingSize) applyFontSize(existingSize);
                if (existingColor) document.execCommand('foreColor', false, existingColor);

            } else {
                document.execCommand(command, false, value);
            }
        } else {
            document.execCommand(command, false, value);
        }

        if (command === 'foreColor') {
            const selection = window.getSelection();
            if (selection.rangeCount) {
                const parentElement = selection.anchorNode.parentElement;
                const listItem = parentElement.closest('li');
                if (listItem) {
                    listItem.style.color = value;
                }
            }
        }

        editor.focus();
        updateToolbarUI();
    }

    window.insertLink = function () {
        const url = prompt("Nhập URL của liên kết:", "https://");
        if (url) {
            formatDoc('createLink', url);
        }
    }

    const fontSizeSelector = document.getElementById('fontSizeSelector');
    fontSizeSelector.addEventListener('change', (e) => {
        applyFontSize(e.target.value);
    });

    const fontColorPicker = document.getElementById('fontColorPicker');
    fontColorPicker.addEventListener('input', (e) => {
        formatDoc('foreColor', e.target.value);
    });

    const updateToolbarUI = () => {
        ['bold', 'italic', 'underline', 'insertOrderedList', 'insertUnorderedList', 'justifyLeft', 'justifyCenter', 'justifyRight'].forEach(command => {
            const button = document.querySelector(`button[onclick*="${command}"]`);
            if (button) {
                button.classList.toggle('active', document.queryCommandState(command));
            }
        });
        updateFormatDisplay();
    };

    function updateFormatDisplay() {
        const selection = window.getSelection();
        if (!selection.rangeCount) return;

        let element = selection.anchorNode.parentElement;
        const sizeSpan = element.closest('span[style*="font-size"]');
        let currentSize = 16;
        if (sizeSpan) {
            currentSize = parseInt(sizeSpan.style.fontSize, 10);
        } else if (editor) {
            currentSize = parseInt(getComputedStyle(editor).fontSize, 10);
        }
        fontSizeSelector.value = currentSize || '16';

        let color = document.queryCommandValue('foreColor');
        if (color && color.startsWith('rgb')) {
            const parts = color.match(/\d+/g);
            color = '#' + parts.map(part => ('0' + parseInt(part).toString(16)).slice(-2)).join('');
        }
        fontColorPicker.value = color || '#e0e0e0';
    }

    document.querySelectorAll('.editor-toolbar button, .editor-toolbar input[type="color"]').forEach(el => {
        el.addEventListener('mousedown', e => e.preventDefault());
    });

    ['keyup', 'mouseup', 'focus', 'input'].forEach(event => {
        editor.addEventListener(event, updateToolbarUI);
    });

    form.addEventListener('submit', () => {
        hiddenTextarea.value = editor.innerHTML;
    });

    updateToolbarUI();
});
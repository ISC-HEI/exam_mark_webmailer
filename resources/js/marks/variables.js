import { VARIABLES } from './constants.js';
import { DOM } from './dom.js';

// ---------------
// Variables choice
// ---------------
const textarea = DOM.textareaMessage;
const menu = DOM.variableMenu;
const items = DOM.variableItems
let activeIndex = VARIABLES.DEFAULT_ACTIVE_INDEX;

const insertVariable = (variable) => {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    const before = text.substring(0, start - 1);
    const after = text.substring(end);
    
    textarea.value = before + `${VARIABLES.PREFIX}${variable}${VARIABLES.SUFFIX}` + after;
    
    const newCursorPos = start + variable.length + 1;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
    
    hideMenu();
    textarea.focus();
};

const hideMenu = () => {
    menu.classList.add('d-none');
    activeIndex = 0;
};

textarea.addEventListener('input', function(e) {
    const value = textarea.value;
    const cursorPos = textarea.selectionStart;
    const lastChar = value.substring(cursorPos - 1, cursorPos);

    if (lastChar === VARIABLES.PREFIX) {
        const coordinates = getCaretCoordinates(textarea, cursorPos);
        
        menu.style.top = (textarea.offsetTop + coordinates.top + 20) + 'px';
        menu.style.left = (textarea.offsetLeft + coordinates.left) + 'px';
        menu.classList.remove('d-none');
    } else {
        hideMenu();
    }
});

textarea.addEventListener('keydown', function(e) {
    if (!menu.classList.contains('d-none')) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            updateActiveItem();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            updateActiveItem();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            insertVariable(items[activeIndex].getAttribute('data-var'));
            resetActiveButton(items)
        } else if (e.key === 'Escape') {
            hideMenu();
        }
    }
});

function updateActiveItem() {
    items.forEach((item, index) => {
        item.classList.toggle('list-group-item-dark', index === activeIndex);
    });
}

items.forEach((item) => {
    item.addEventListener('click', () => {
        insertVariable(item.getAttribute('data-var'));
    });
});

document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && e.target !== textarea) hideMenu();
});

function getCaretCoordinates(element, position) {
    const div = document.createElement('div');
    div.id = 'textarea-mirror';
    const style = window.getComputedStyle(element);
    
    const props = ['fontFamily', 'fontSize', 'fontWeight', 'lineHeight', 'padding', 'border', 'width', 'boxSizing'];
    props.forEach(prop => div.style[prop] = style[prop]);
    
    div.textContent = element.value.substring(0, position);
    const span = document.createElement('span');
    span.textContent = element.value.substring(position) || '.';
    div.appendChild(span);
    
    document.body.appendChild(div);
    const { offsetTop: top, offsetLeft: left } = span;
    document.body.removeChild(div);
    
    return { top, left };
}

function resetActiveButton(items) {
    items.forEach(item => {
        if (item.classList.contains("list-group-item-dark")) {
            item.classList.remove("list-group-item-dark")
        }
    })
    items[0].classList.add("list-group-item-dark")
}

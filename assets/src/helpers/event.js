// https://stackoverflow.com/questions/14677019/emulate-jquery-on-with-selector-in-pure-javascript
// https://stackoverflow.com/questions/33830578/plain-javascript-equivalent-of-document-onevent-with-selector

const on = (element, selector, event, handler) => {
  element.addEventListener(event, (e) => { if (e.target.matches(selector)) handler(e); });
};

/* Add one or more listeners to an element
 ** @param {DOMElement} element - DOM element to add listeners to
 ** @param {string} eventNames - space separated list of event names, e.g. 'click change'
 ** @param {Function} listener - function to attach for each event as a listener
 */
function addListenerMulti(el, s, fn) {
  s.split(' ').forEach((e) => el.addEventListener(e, fn, false));
}

const addEventForChild = function (parent, eventName, childSelector, cb) {
  if (!parent) {
    return;
  }
  addListenerMulti(parent, eventName, (event) => {
    const clickedElement = event.target;
    const matchingChild = clickedElement.closest(childSelector);
    if (matchingChild) cb(event, matchingChild);
  });
};

const replaceElement = function (element) {
  const newElement = element.cloneNode(true);
  return element.parentNode.replaceChild(newElement, element);
};

export {
  on, addEventForChild, addListenerMulti, replaceElement,
};

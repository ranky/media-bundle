/**
 * @param html
 *
 * @return {HTMLElement}
 */
function htmlToElement(html) {
  const template = document.createElement('div');
  template.innerHTML = html.trim();
  return template;
}

export default htmlToElement;

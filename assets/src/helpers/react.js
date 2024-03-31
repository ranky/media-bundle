function getRootElement(target) {
  let element = target;
  while (element) {
    element = element.parentElement;
    if (Object.keys(element).some((key) => key.includes('_reactRootContainer'))) {
      return element;
    }
  }
  return null;
}

export { getRootElement };

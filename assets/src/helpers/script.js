async function addScript(src, callback = null) {
  const s = document.createElement('script');
  s.setAttribute('src', src);
  s.onload = callback;
  document.head.appendChild(s);
}

export default addScript;

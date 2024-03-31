const capitalize = (str) => {
  return str && str.charAt(0).toUpperCase() + str.slice(1);
};
const removeLastChar = (str, char) => {
  const lastChar = str.slice(-1);
  if (lastChar === char) {
    return str.slice(0, -1);
  }
  return str;
};
export { capitalize, removeLastChar };

const isArray = (value) => {
  return (!!value) && (value.constructor === Array);
};

const isLiteralObject = (value) => {
  return (!!value) && (value.constructor === Object);
};

function isEmptyObject(obj) {
  return JSON.stringify(obj) === '{}';
}

export { isArray, isLiteralObject, isEmptyObject };

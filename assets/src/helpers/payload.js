const payloadTransform = async (dataForm) => {
  if (!dataForm) {
    return null;
  }
  if (!(dataForm instanceof FormData)) {
    return JSON.stringify(dataForm);
  }
  const data = {};
  dataForm.forEach((value, name) => {
    data[name] = value;
  });

  return JSON.stringify(data);
};

export default payloadTransform;

import { isArray, isLiteralObject } from '@rankyMedia/helpers/types';
import { capitalize } from '@rankyMedia/helpers/string';

const errorTransform = (errors = null) => {
  if (!errors) {
    return null;
  }
  if (isArray(errors)) {
    return errors.join(', ');
  }
  if (isLiteralObject(errors)) {
    return Object.entries(errors)
      .map((element) => {
        return `<p><b>${capitalize(element[0])}:</b> ${element[1]}</p>`;
      });
  }
  return errors;
};

export default errorTransform;

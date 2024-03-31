import React from 'react';
// import parse from 'html-react-parser'; // 26.8 kB
import useTranslator from '@rankyMedia/api/hook/useTranslator';
import { TranslationsType } from '@rankyMedia/types/Translations';

type TransType = {
  message: keyof TranslationsType;
  data?: object;
};
const Trans = ({ message, data = null }: TransType): React.ReactElement => {
  const translator = useTranslator();
  const translatedMessage = translator.trans(message, data);
  const hasHTML = /<[a-z][\s\S]*>/i.test(translatedMessage);

  return (hasHTML
    // eslint-disable-next-line react/no-danger
    ? <span dangerouslySetInnerHTML={{ __html: translatedMessage }} />
    : translatedMessage
  ) as React.ReactElement;
};

export default Trans;
